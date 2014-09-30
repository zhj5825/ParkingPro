<?php
include_once 'DatabaseConfig.php';

class DBLogicOperations {

    // constructor
    public function __construct() {        
    }

    public function __destruct() {        
    }

    public static function addNewUserAccount($email, $password) {
        $account_exists = DBUtilOperations::checkUserAccountExistence($email);
        if($account_exists == DBNOTSUCCESSFUL) {
            return array(false, DBNOTSUCCESSFUL);
        } else if ($account_exists == true) {
            return array(false, USER_ACCOUNT_EXISTS);
        }
        
        $result = DBUtilOperations::addNewUserAccount($email, $password);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        }        
    }

}

