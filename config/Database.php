<?php

class Database
{
    private string $host;
    private string $database;
    private string $username;
    private string $password;

    private ?PDO $connection = null;

    public function __construct()
    {
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function connect(): PDO
    {
        if ($this->connection === null) {

            try {

                $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                    $this->username,
                    $this->password
                );

                $this->connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                $this->connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $e) {

                die("Erro na conexão com o banco de dados.");
            }
        }

        return $this->connection;
    }
}