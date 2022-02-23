<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Helper\DayComputer;
use App\Helper\PRReviewCommentStatsComputeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use DateTime;

class ComputePRReviewCommentStatsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'matks:pr-review-comment-stats:compute';

    /**
     * @var PRReviewCommentStatsComputeService
     */
    private $prReviewCommentStatsRecordService;

    /**
     * @param PRReviewCommentStatsComputeService $prReviewCommentStatsRecordService
     */
    public function __construct(PRReviewCommentStatsComputeService $prReviewCommentStatsRecordService)
    {
        $this->prReviewCommentStatsRecordService = $prReviewCommentStatsRecordService;
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

        $yesterday = new DateTime('yesterday');

        $output->writeln(sprintf(
            'Compute PR review comments statistics for %s (dry-run: %s)',
            $yesterday->format('Y-m-d'),
            ($isDryRun ? '<info>true</info>' : '<error>false</error>')
        ));

        $recordLogs = $this->prReviewCommentStatsRecordService->computePRReviewCommentStatistics($yesterday, $isDryRun);

        foreach ($recordLogs as $log) {
            $output->writeln($log);
        }

        return 0;

    }
}
