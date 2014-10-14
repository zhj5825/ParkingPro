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
    public static function addCreditCardByName($user_name,  $credit_card_number, 
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
    
    // If a new card is added, returns a tuple of (true, DBSUCCESSFUL);
    // otherwise, returns a tuple of (false, ERROR_CODE). 
    public static function addCreditCardByID($user_id,  $credit_card_number, 
            $credit_card_exp_month, $credit_card_exp_year, $credit_card_address,
            $credit_card_city, $credit_card_state, $credit_card_country, 
            $credit_card_zipcode, $name_on_card, $security_code) {
        list($success, $response) = DBUtilOperations::checkUserIDExistence($user_id);
        if ($success == false) {
            return array($success, $response);
        } 
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
        
    // If a new parking lot is added, returns a tuple of (true, DBSUCCESSFUL);
    // otherwise, returns a tuple of (false, ERROR_CODE). 
    public static function addParkingLotByName($owner_name,  $address, $city, 
            $state, $country, $zipcode) {
        list($success, $response) = DBUtilOperations::getUserID($owner_name);
        if ($success == false) {
            return array($success, $response);
        } 
        $owner_id = $response;
        // TODO(xifang): Check duplicate cards
        $result = DBUtilOperations::addParkingLot($owner_id, $address, $city, 
            $state, $country, $zipcode);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        } 
    }
    
    // If a new parking lot is added, returns a tuple of (true, DBSUCCESSFUL);
    // otherwise, returns a tuple of (false, ERROR_CODE). 
    public static function addParkingLotByID($owner_id,  $address, $city, 
            $state, $country, $zipcode) {
        list($success, $response) = DBUtilOperations::checkUserIDExistence($user_id);
        if ($success == false) {
            return array($success, $response);
        } 
        // TODO(xifang): Check duplicate cards
        $result = DBUtilOperations::addParkingLot($owner_id, $address, $city, 
            $state, $country, $zipcode);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        } 
    }
      
    // Updates the parking lot active currently, and set the available time afterwards and the correponsding price.
    public static function setParkingLotActive($id, $price, $available_start_time, $available_end_time) {
        $result = DBUtilOperations::updateParkingLotAvailability(
                $id, TableEnum::$parking_status["ACTIVE"], $price, $available_start_time, $available_end_time);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        } 
    }
    
    // Inactivates the parking lot.
    public static function setParkingLotInactive($id) {
        $result = DBUtilOperations::updateParkingLotAvailability(
                $id, TableEnum::$parking_status["INACTIVE"], NULL, NULL, NULL);
        if($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else {
            return array(true, DBSUCCESSFUL);
        } 
    } 
    
    public static function selectParkingLotsByZipcode($zipcode) {
        return DBUtilOperations::selectParkingLotsByZipcode($zipcode);
    }
    
    public static function selectParkingLotsByAddress($address, $city, $state, $country) {
        return DBUtilOperations::selectParkingLotsByAddress(
                $address, $city, $state, $country);
    }


}

