<?php

namespace App\Command;

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

        $day = (new DateTime())->format('Y-m-d');
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');

        $team = [
            'PierreRambaud',      # Pierre R.
            'matks',              # Matthieu F.
            'jolelievre',         # Jonathan L.
            'matthieu-rolland',   # Matthieu R.
            'Progi1984',          # Franck L.
            'atomiix',            # Thomas B.
            'NeOMakinG',          # Valentin S.
            'sowbiba',            # Ibrahima S.
        ];

        foreach ($team as $login) {
            $dataFromAPI = json_decode(
                $this->getReviewsByDay($this->githubToken, $login, $yesterday . "T00:00:00", $yesterday . "T23:59:59"),
                true
            );

            $edges = reset($dataFromAPI['data']['user']['contributionsCollection']);
            $edge = reset($edges);

            $PRurls = [];

            foreach ($edge as $dataBag) {
                $PR = $dataBag['node']['pullRequest'];
                $url = $PR['url'];
                $PRurls[] = $url;
            }

            if ($isDryRun) {
                $output->writeln(sprintf(
                    '%s reviewed %d reviews on %s',
                    $login,
                    count($PRurls),
                    $yesterday
                ));
            } else {
                $this->insertReview($pdo, $login, $PRurls, $yesterday);
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

        $response = curl_exec($chObj);

        return $response;
    }
}
