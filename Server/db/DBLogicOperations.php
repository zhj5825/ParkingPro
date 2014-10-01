<?php
include_once 'DatabaseConfig.php';

class DBLogicOperations {

    // constructor
    public function __construct() {        
    }

    public function __destruct() {        
    }
    
    // If a new user account is added, returns a tuple of (true, DBSUCCESSFUL);
    // otherwise, returns a tuple of (false, ERROR_CODE). 
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
    
    // If a new card is added, returns a tuple of (true, DBSUCCESSFUL);
    // otherwise, returns a tuple of (false, ERROR_CODE). 
    public static function addCreditCard($user_name,  $credit_card_number, 
            $credit_card_exp_month, $credit_card_exp_year, $credit_card_address,
            $credit_card_city, $credit_card_state, $credit_card_country, 
            $credit_card_zipcode, $name_on_card, $security_code) {
        list($success, $response) = DBUtilOperations::getUserID($user_name);
        if ($success == false) {
            return array($success, $response);
        } 
        $user_id = $response;
        // TODO(xifang): Check duplicate cards
        $result = DBUtilOperations::addCreditCard($user_id, $credit_card_number, 
                $credit_card_exp_month, $credit_card_exp_year, 
                $credit_card_address, $credit_card_city, $credit_card_state, 
                $credit_card_country, $credit_card_zipcode, $name_on_card, 
                $security_code);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        } 
    }

}

