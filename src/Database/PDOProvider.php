<?php

namespace App\Database;

use PDO;

class PDOProvider
{
    /** @var string */
    private $host;
    /** @var string */
    private $db;
    /** @var string */
    private $user;
    /** @var string */
    private $pass;

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
