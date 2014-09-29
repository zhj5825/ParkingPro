<?php
include_once 'DBConnection.php';

/**
 * Singleton class.
 * It maintains a pool of db connections to one database. We currently only support one db connection.
 *
 */
class DBConnectionManager {

    /**
     * Call this method to get singleton
     *
     * @return DBConnectionManager
     */
    private $connection;

    public static function getInstance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new DBConnectionManager();
        }
        return $inst;
    }

    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct() {
        $this->connection = new DBConnection();
    }
    

}
