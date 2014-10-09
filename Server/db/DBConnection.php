<?php

require ("ConnectionConfig.php");

/**
 * DB connection class.
 * TODO(xifang): Add logic for retry when connection is down.
 *
 */
class DBConnection {

    private $connection;
    public function __construct() {
        $this->connection = new mysqli(
                ConnectionConf::$ConnLogin["DB_HOST"],
                ConnectionConf::$ConnLogin["DB_USER"], 
                ConnectionConf::$ConnLogin["DB_PASSWORD"], 
                ConnectionConf::$ConnLogin["DB_DATABASE"]);
        if ($this->connection->connect_errno) {
            printf("Connect failed: %s\n", $this->connection->connect_error);
            exit();
        }
    }

    public function __destruct() {
        $this->close_db();
    }
    
    public function execute_sql_query($sqlquery) {
        if ($this->is_connected() == false) {
           printf ("Error: %s\n", $this->connection->error);
           return false;
        }
        return $this->connection->query($sqlquery);
    }
    
    private function is_connected()
    {
        return mysqli_ping ($this->connection);
    }

    private function close_db() {
        $this->connection->close();
    }

    public function insert_info($table_name, $columns) {
        $keys = "";
        $values = "";
        foreach($columns as $key => $value) {
            $keys = $keys . "`" . $key . "`,";
            $values = $values . "'" . $value . "',";
            
        }
        if (strlen($keys) > 0) {
            $keys = substr($keys, 0, -1);
        }
        if (strlen($values) > 0) {
            $values = substr($values, 0, -1);
        }
        $query = "INSERT INTO "
                . $table_name . "("
                . $keys . ") VALUES ("
                . $values . ")";
        return $this->execute_sql_query($query);
    } 
}
