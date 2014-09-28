<?php

require ("configuration.php");

/**
 * DB connection class.
 * TODO(xifang): Add logic for retry when connection is down.
 *
 */
class DBConnection {

    private $connection;

    public function __construct() {
        $this->connection = null;
    }

    public function __destruct() {
        close_db();
    }

    public function connect_localhost() {
        $this->connection = connect_db(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    }

    public function connect_db($host, $user, $password, $dbname) {
        $this->connection = mysql_connect($host, $$user, $password, $dbname);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            $this->connection = null;
        }
    }

    public function close_db() {
        if ($this->connection == null) {
            echo "No connection to MySQL: " . mysqli_connect_error();
            return;
        }
        mysqli_close($this->connection);
        $this->connection = null;
    }

    public function execute_sql_query($sqlquery) {
        $result = mysql_query($sqlquery, $this->connection);
        if ($result == false) {
            echo "No connection to MySQL: " . mysql_error();
        }
        return $result;
    }

}

?>