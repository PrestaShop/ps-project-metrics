<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Entity\ReviewStat;
use App\Helper\DayComputer;
use App\Helper\RecordService;
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
     * @var RecordService
     */
    private $recordService;

    /** @var string */
    protected static $defaultName = 'matks:record';

    /**
     * @param RecordService $recordService
     */
    public function __construct(RecordService $recordService)
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

        $day = new DateTime();
        $previousWorkedDay = DayComputer::getPreviousWorkedDayFromDateTime($day);
        $output->writeln(sprintf(
            'Record reviews for %s (dry-run: %s)',
            $previousWorkedDay->format('Y-m-d'),
            ($isDryRun ? '<info>true</info>' : '<error>false</error>')
        ));

        $recordLogs = $this->recordService->recordReviewsForDay($previousWorkedDay, $isDryRun);

        foreach ($recordLogs as $log) {
            $output->writeln($log);
        }

        return 0;
    }
}
