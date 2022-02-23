<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Helper\DayComputer;
use App\Helper\PRWaitingReviewStatusDeleteService;
use App\Helper\PRWaitingReviewStatusRecordService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use DateTime;

class RecordPRsStatusesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'ps:prs-statuses:record';

    /**
     * @var PRWaitingReviewStatusRecordService
     */
    private $recordService;

    /**
     * @var PRWaitingReviewStatusDeleteService
     */
    private $deleteService;

    /**
     * @param PRWaitingReviewStatusRecordService $recordService
     * @param PRWaitingReviewStatusDeleteService $deleteService
     */
    public function __construct(PRWaitingReviewStatusRecordService $recordService, PRWaitingReviewStatusDeleteService $deleteService)
    {
        $this->recordService = $recordService;
        $this->deleteService = $deleteService;
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
                'no-weekend',
                null,
                InputOption::VALUE_OPTIONAL,
                'Dont run on weekend'
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

        $noWeekendRunOption = $input->getOption('no-weekend');
        $noWeekendRun = ('true' === $noWeekendRunOption);

        $day = new DateTime();

        if (DayComputer::isItWeekend($day) && $noWeekendRun) {
            $output->writeln('No run on the weekend');
            return 0;
        }

        if ($isDryRun === false) {
            $output->writeln('Delete existing pull requests review status records');
            $this->deleteService->deleteAll();
            $output->writeln('...done');
        }

        $output->writeln(sprintf(
            'Record pull requests review status for %s (dry-run: %s)',
            $day->format('Y-m-d'),
            ($isDryRun ? '<info>true</info>' : '<error>false</error>')
        ));

        $recordLogs = $this->recordService->recordAllPRWaitingReviewStatus($isDryRun);

        foreach ($recordLogs as $log) {
            $output->writeln($log);
        }

        return 0;

    }
}
