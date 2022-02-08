<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use PDO;

class PRWaitingReviewStatusDeleteService
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        $sql = 'DELETE FROM pr_waiting_review_status';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute();
    }
}
