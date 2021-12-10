<?php

declare(strict_types=1);

namespace App\Command;

use App\Helper\DayComputer;
use App\Helper\TeamHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PDO;
use App\Database\PDOProvider;
use DateTime;

class RecordReviewsCommand extends Command
{
    /**
     * @var PDOProvider
     */
    private $pdoProvider;

    /**
     * @var string
     */
    private $githubToken;

    /**
     * @param PDOProvider $provider
     * @param string $githubToken
     */
    public function __construct(PDOProvider $provider, string $githubToken)
    {
        $this->pdoProvider = $provider;
        $this->githubToken = $githubToken;
        parent::__construct();
    }

    /** @var string */
    protected static $defaultName = 'matks:record';

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_OPTIONAL,
                'Dry run'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDryRun = $input->getOption('dry-run');
        $pdo = $this->pdoProvider->getPDO();

        $day = new DateTime();
        $previousWorkedDay = (DayComputer::getPreviousWorkedDayFromDateTime($day))->format('Y-m-d');
        $output->writeln(sprintf('Record reviews for %s', $previousWorkedDay));

        $team = TeamHelper::getTeam();

        foreach ($team as $login) {
            $dataFromAPI = json_decode(
                $this->getReviewsByDay(
                    $this->githubToken,
                    $login,
                    $previousWorkedDay . "T00:00:00",
                    $previousWorkedDay . "T23:59:59"
                ),
                true
            );

            $PRurls = $this->extractPRUrls($dataFromAPI);

            if ($isDryRun) {
                $output->writeln(sprintf(
                    '%s reviewed %d reviews on %s',
                    $login,
                    count($PRurls),
                    $previousWorkedDay
                ));
            } else {
                $this->insertReview($pdo, $login, $PRurls, $previousWorkedDay);
            }
        }

        return 0;
    }

    /**
     * @param PDO $pdo
     * @param string $login
     * @param array $PRs
     * @param string $day
     */
    private function insertReview(PDO $pdo, string $login, array $PRs, string $day)
    {
        $sql = "INSERT INTO reviews (login, PR, day, total) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$login, '"' . implode('";"', $PRs) . '"', $day, count($PRs)]);
    }

    /**
     * @param string $token
     * @param string $login
     * @param string $from
     * @param string $to
     *
     * @return bool|string
     *
     * @see https://docs.github.com/en/graphql/overview/explorer
     */
    private function getReviewsByDay(string $token, string $login, string $from, string $to)
    {
        $query = sprintf('
		{
		  user(login: "%s") {
		    contributionsCollection(from: "%s", to: "%s") {
		      pullRequestReviewContributions(first: 100) {
		        edges {
		          node {
		            occurredAt
		            pullRequest {
		              url
		            }
		          }
		        }
		      }
		    }
		  }
		}
		', $login, $from, $to);
        $json = json_encode(['query' => $query, 'variables' => []]);

        $chObj = curl_init();
        curl_setopt($chObj, CURLOPT_URL, 'https://api.github.com/graphql');
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($chObj, CURLOPT_VERBOSE, false);
        curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
        curl_setopt($chObj, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization: bearer ' . $token
            )
        );

        return curl_exec($chObj);
    }

    /**
     * @param array $dataFromAPI
     *
     * @return array
     */
    protected function extractPRUrls(array $dataFromAPI): array
    {
        $edges = reset($dataFromAPI['data']['user']['contributionsCollection']);
        $edge = reset($edges);

        $urls = [];
        foreach ($edge as $dataBag) {
            $PR = $dataBag['node']['pullRequest'];
            $url = $PR['url'];
            $urls[] = $url;
        }

        return $urls;
    }
}
