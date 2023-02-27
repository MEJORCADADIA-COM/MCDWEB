<?php 
$path = realpath(dirname(__FILE__));
include_once $path . '/../helper.php';
include_once ($path . "/../config/config.php");

class Database {
    private readonly PDO $connection;

	public function __construct() {
        $config = [
            'host' => DB_HOST,
            'port' => 3306,
            'dbname' => DB_NAME,
        ];

        $dsn = "mysql:" . http_build_query($config, '', ';');
        $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
	}

    public function query($query, $params = []): bool|PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    public function insertId()
    {
        return $this->connection->lastInsertId();
    }
}