<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Entity\ReviewStat;
use App\Helper\DayComputer;
use App\Helper\TeamHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use DateTime;

class RecordReviewsCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $githubToken;

    /** @var string */
    protected static $defaultName = 'matks:record';

    /**
     * @param EntityManager $entityManager
     * @param string $githubToken
     */
    public function __construct(EntityManager $entityManager, string $githubToken)
    {
        $this->entityManager = $entityManager;
        $this->githubToken = $githubToken;
        parent::__construct();
    }

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
                $this->insertReview($login, $PRurls, $previousWorkedDay);
            }
        }

        $this->entityManager->flush();

        return 0;
    }

    /**
     * @param PDO $pdo
     * @param string $login
     * @param array $PRs
     * @param string $day
     */
    private function insertReview(string $login, array $PRs, string $day)
    {
        $reviewStatRecord = new ReviewStat(
            $login,
            '"' . implode('";"', $PRs) . '"',
            new DateTime($day),
            count($PRs)
        );

        $this->entityManager->persist($reviewStatRecord);
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
