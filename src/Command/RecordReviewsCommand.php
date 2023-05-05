<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Helper\DayComputer;
use App\Helper\ReviewRecordService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordReviewsCommand extends Command
{
    /**
     * @var ReviewRecordService
     */
    private ReviewRecordService $recordService;

    /** @var string */
    protected static $defaultName = 'ps:review-stats:record';

    /**
     * @param ReviewRecordService $recordService
     */
    public function __construct(ReviewRecordService $recordService)
    {
        $this->recordService = $recordService;
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
            )
            ->addOption(
                'start-date',
                null,
                InputOption::VALUE_OPTIONAL,
                'Start date'
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
        $dryRunOption = $input->getOption('dry-run');
        $isDryRun = ('false' !== $dryRunOption);

        $startDate = $input->getOption('start-date');

        if (!empty($startDate)) {
            $startDate = new DateTime($startDate);
            $endDate = new DateTime();

            $interval = new DateInterval('P1D');
            $dateRange = new DatePeriod($startDate, $interval, $endDate);

            foreach ($dateRange as $day) {
                $output->writeln(sprintf(
                    'Record reviews for %s (dry-run: %s)',
                    $day->format('Y-m-d'),
                    ($isDryRun ? '<info>true</info>' : '<error>false</error>')
                ));

                $recordLogs = $this->recordService->recordReviewsForDay($day, $isDryRun);
            }
        } else {
            $day = new DateTime();
            $day->modify('-1 day');
            $output->writeln(sprintf(
                'Record reviews for %s (dry-run: %s)',
                $day->format('Y-m-d'),
                ($isDryRun ? '<info>true</info>' : '<error>false</error>')
            ));
            $recordLogs = $this->recordService->recordReviewsForDay($day, $isDryRun);
        }

        foreach ($recordLogs as $log) {
            $output->writeln($log);
        }

        return 0;
    }
}
