<?php

namespace Foundry;

final class PreparedStatement {

    private $placeholders, $stmt;

    public function __construct(\PDOStatement $stmt) {
        $this->placeholders = Array();
        $this->stmt         = $stmt;
    }

    public function bind(Array $placeholders) {
        $this->placeholders = $this->placeholders + $placeholders;
        return $this;
    }

    public function execute() {
        $numPlaceholders = substr_count($this->stmt->queryString, ':');

        if ($numPlaceholders > 0) {
            if ($numPlaceholders !== count($this->placeholders)) {
                $err = 'Placeholder and value counts don\'t match.';
                throw new \Exception($err);
            }

            foreach ($this->placeholders as $key => $args) {
                if ($key[0] !== ':') {
                    $key = ":{$key}";
                }
                array_unshift($args, $key);
                call_user_func_array(array($this->stmt, 'bindValue'), $args);
            }
        }

        $this->stmt->execute();

        $results = Array();
        if (strpos($this->stmt->queryString, 'UPDATE') === FALSE &&
            strpos($this->stmt->queryString, 'INSERT') === FALSE &&
            strpos($this->stmt->queryString, 'DELETE') === FALSE) {
            while($result = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($results, $result);
            }
        }
        $this->stmt->closeCursor();

        return $results;
    }

}
