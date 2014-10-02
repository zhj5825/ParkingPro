<?php

require ("ConnectionConfig.php");

/**
 * DB connection class.
 * TODO(xifang): Add logic for retry when connection is down.
 *
 */
class DBConnection {

    public $connection;
    public function __construct() {
        $this->connection = new mysqli(
                ConnectionConf::$ConnLogin["DB_HOST"],
                ConnectionConf::$ConnLogin["DB_USER"], 
                ConnectionConf::$ConnLogin["DB_PASSWORD"], 
                ConnectionConf::$ConnLogin["DB_DATABASE"]);
        if ($this->connection->connect_errno) {
            printf("Connect failed: %s with error code:%d\n", 
                    $this->connection->connect_error, 
                    $this->connection->connect_errno);
            exit();
        }
    }

    public function __destruct() {
        $this->close_db();
    }


    public function close_db() {
        $this->connection->close();
    }

    public function execute_sql_query($sqlquery) {
        return $this->connection->query($sqlquery);
    }

}
