<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Helper\ReviewWeekStatsComputeService;
use App\Helper\TeamHelper;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

class ComputeReviewsWeekStatsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'ps:review-week-stats:compute';

    /**
     * @var ReviewWeekStatsComputeService
     */
    private $reviewWeekStatsComputeService;

    /**
     * @param ReviewWeekStatsComputeService $reviewWeekStatsComputeService
     */
    public function __construct(ReviewWeekStatsComputeService $reviewWeekStatsComputeService)
    {
        $this->reviewWeekStatsComputeService = $reviewWeekStatsComputeService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('maintainer', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maintainer = $input->getArgument('maintainer');

        if (!in_array($maintainer, TeamHelper::getTeam())) {
            throw new RuntimeException(sprintf('%s it not maintainer', $maintainer));
        }
        $today = new DateTime();

        $output->writeln(sprintf(
            'Compute PR review statistics of %s for %s',
            $maintainer,
            $today->format('Y-m-d')
        ));

        $recordLogs = $this->reviewWeekStatsComputeService->computePRReviewCommentStatistics($today, $maintainer);

        foreach ($recordLogs as $log) {
            $output->writeln($log);
        }

        return 0;

    }
}
