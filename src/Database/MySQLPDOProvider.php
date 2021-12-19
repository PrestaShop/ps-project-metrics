<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Database;

use PDO;

class MySQLPDOProvider
{
    /** @var string */
    private string $host;
    /** @var string */
    private string $db;
    /** @var string */
    private string $user;
    /** @var string */
    private string $pass;

    /**
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $host, string $db, string $user, string $pass)
    {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        $host = $this->host;
        $db = $this->db;
        $user = $this->user;
        $pass = $this->pass;
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $pdo;
    }
}
