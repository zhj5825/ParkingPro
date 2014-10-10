<?php

include_once 'DatabaseConfig.php';
include_once 'DBConnectionManager.php';

class DBUtilOperations {

    // constructor
    public function __construct() {
        
    }

    public function __destruct() {
        
    }

    public static function addNewUserAccount($email, $password) {       
        $columns = array(
            'UserName' => $email,
            'Email' => $email,
            'Password' => $password            
        );

        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->insert_info(
                DBConf::$tables["USER_ACCOUNTS"], $columns);  
    }

    public static function checkUserAccountExistence($user_name) {
        $columns = array(
            'count(*)'
        );
        $conditions = array(
            'UserName' => $user_name
        );
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->select_info(
                DBConf::$tables["USER_ACCOUNTS"], $columns, $conditions);        
        
        if ($result == false) {
            return DBNOTSUCCESSFUL;
        }
        
        return $result->fetch_row()[0] > 0;
    }
    
    public static function checkUserIDExistence($user_id) {
        $columns = array(
            'count(*)'
        );
        $conditions = array(
            'ID' => $user_id
        );
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->select_info(
                DBConf::$tables["USER_ACCOUNTS"], $columns, $conditions);
        
        if ($result == false) {
            return DBNOTSUCCESSFUL;
        }
        
        return $result->fetch_row()[0] > 0;
    }
    
    public static function getUserID($user_name) {
        $columns = array(
            'ID'
        );
        $conditions = array(
            'UserName' => $user_name
        );
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->select_info(
                DBConf::$tables["USER_ACCOUNTS"], $columns, $conditions);

        if ($result == false) {
            return array(false, DBNOTSUCCESSFUL);
        } else if ($result->num_rows == 0) {
            return array(false, USER_ACCOUNT_NOT_EXISTS);
        }
        return array(true, $result->fetch_row()[0]);
    }

    public static function addCreditCard($user_id,  $credit_card_number, 
            $credit_card_exp_month, $credit_card_exp_year, $credit_card_address,
            $credit_card_city, $credit_card_state, $credit_card_country, 
            $credit_card_zipcode, $name_on_card, $security_code) {               
        $columns = array(
            'UserId' => $user_id,
            'CreditCardNum' => $credit_card_number,
            'CreditCardExpMonth' => $credit_card_exp_month,
            'CreditCardExpYear' => $credit_card_exp_year,
            'CreditCardAddress' => $credit_card_address,
            'CreditCardCity' => $credit_card_city,
            'CreditCardState' => $credit_card_state,
            'CreditCardCountry' => $credit_card_country,
            'CreditCardZipcode' => $credit_card_zipcode,
            'CreditCardName' => $name_on_card,
            'CreditCardSecurityCode' => $security_code
        );

        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->insert_info(
                DBConf::$tables["CREDIT_CARDS"], $columns);
    }

    public static function addParkingLot($owner_id,  $address, $city, 
            $state, $country, $zipcode) {        
        $columns = array(
            'OwnerId' => $owner_id,
            'Address' => $address,
            'City' => $city,
            'State' => $state,
            'Country' => $country,
            'Zipcode' => $zipcode
        );

        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->insert_info(
                DBConf::$tables["PARKING"], $columns);
    } 
    
    public static function updateParkingLotAvailability($id, 
            $status, $price, $available_start_time, $available_end_time) {
        $columns = array(
            'Status' => $status,
            'ListedPrice' => $price,
            'AvailableStartTime' => $available_start_time,
            'AvailableEndTime' => $available_end_time
        );
        $conditions = array(
            'ID' => $id
        );
        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->update_info(
                DBConf::$tables["PARKING"], $columns, $conditions);
    }

}
