<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use PDO;

class ReviewCommentsDeleteService
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
    public function deleteOlderThan(DateTime $dateTime): bool
    {
        $date = $dateTime->format('Y-m-d');
        $sql = sprintf('DELETE FROM review_comment_webhook WHERE created_at < \'%s\'', $date);

        $statement = $this->pdo->prepare($sql);

        return $statement->execute();
    }
}
