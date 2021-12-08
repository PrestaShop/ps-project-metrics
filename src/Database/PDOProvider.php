<?php
namespace App\Database;

use PDO;

class PDOProvider
{
	private $host;
	private $db;
	private $user;
	private $pass;

	public function __construct($host, $db, $user, $pass)
	    {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
	}

    public function getPDO()
    {
        $host = $this->host;
        $db   = $this->db;
        $user = $this->user;
        $pass = $this->pass;
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
             $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
             throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $pdo;
    }
}