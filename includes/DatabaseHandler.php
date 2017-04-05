<?php

namespace Foundry;

require_once('PreparedStatement.php');

final class DatabaseHandler {

    const SUPPORTED_DBS = Array('pgsql', 'mysql');
    const PDO_OPTIONS   = Array(
        \PDO::ATTR_ERRMODE          => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => FALSE
    );

    private $pdo;

    private function mysql(Array $options) {
        $host    = $options['dbhost'];
        $port    = $options['dbport'];
        $name    = $options['dbname'];
        $charset = $options['charset'];
        $user    = $options['dbuser'];
        $pass    = $options['dbpass'];
        $this->pdo = new \PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset={$charset}",
            $user, $pass, DatabaseHandler::PDO_OPTIONS
        );
    }

    private function pgsql(Array $options) {
        $host = $options['dbhost'];
        $port = $options['dbport'];
        $name = $options['dbname'];
        $user = $options['dbuser'];
        $pass = $options['dbpass'];
        $this->pdo = new \PDO(
            "pgsql:host={$host};port={$port};dbname={$name}",
            $user, $pass, DatabaseHandler::PDO_OPTIONS
        );
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function connect(Array $options = Array()) {
        $options = array_merge(Array(
            'prefix'  => 'mysql',
            'dbhost'  => 'localhost',
            'dbport'  => '3306',
            'dbname'  => '',
            'dbuser'  => 'root',
            'dbpass'  => '',
            'charset' => 'utf8mb4'
        ), $options);
        $prefix = $options['prefix'];
        if (!in_array($prefix, DatabaseHandler::SUPPORTED_DBS)) {
            throw new \Exception("PDO DSN prefix {$prefix} is not supported");
        }
        $this->{$prefix}($options);
        return $this;
    }

    public function lastInsertId($name = NULL) {
        return $this->pdo->lastInsertId($name);
    }

    public function prepare($query) {
        return new PreparedStatement($this->pdo->prepare($query));
    }

    public function rollback() {
        return $this->pdo->rollback();
    }
}
