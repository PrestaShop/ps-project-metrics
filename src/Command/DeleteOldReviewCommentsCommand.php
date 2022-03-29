<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Helper\DayComputer;
use App\Helper\ReviewCommentsDeleteService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class DeleteOldReviewCommentsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'ps:pr-review-comment:delete-old';

    /**
     * @var ReviewCommentsDeleteService
     */
    private $reviewCommentsDeleteService;

    /**
     * @param ReviewCommentsDeleteService $reviewCommentsDeleteService
     */
    public function __construct(ReviewCommentsDeleteService $reviewCommentsDeleteService)
    {
        $this->reviewCommentsDeleteService = $reviewCommentsDeleteService;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threeDaysAgo = DayComputer::getXDayBefore(3, new DateTime());

        $output->writeln(sprintf(
            'Delete PR review comments data older than %s',
            $threeDaysAgo->format('Y-m-d'),
        ));

        $result = $this->reviewCommentsDeleteService->deleteOlderThan($threeDaysAgo);

        return ($result) ? 1 : 0;
    }
}
