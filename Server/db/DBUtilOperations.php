<?php

include_once 'DatabaseConfig.php';
include_once 'DBConnectionManager.php';

class DBUtilOperations {

    // constructor
    public function __construct() {
        
    }

    public function __destruct() {
        
    }
/*
    public static function addAccount($user_name, $email, $password, $role_type, $first, $last, $home_address, $home_city, $home_state, $home_country, $home_zipcode, $credit_card_number, $credit_card_exp_month, $credit_card_exp_year, $credit_card_address, $credit_card_city, $credit_card_state, $credit_card_country, $credit_card_zipcode, $name_on_card, $security_code, $phone) {
        $query = "INSERT INTO `user_accounts`(`UserName`, `Email`, `Password`, "
                . "`RoleType`, `FirstName`, `LastName`, `HomeAddress`, `HomeCity`, "
                . "`HomeState`, `HomeCountry`, `HomeZipcode`, `Phone`"
                //. "`CreditCardNumber`, "
                //. "`CreditCardExpMonth`, `CreditCardExpYear`, `CreditCardAddress`, "
                //. "`CreditCardCity`, `CreditCardState`, `CreditCardCountry`, "
                //. "`CreditCardZipcode`, `NameOnCard`, `SecurityCode`"
                . ") VALUES (";
        $query = $query . "'" . $user_name . "', ";
        $query = $query . "'" . $email . "', ";
        $query = $query . "'" . password_hash($password, PASSWORD_BCRYPT) . "', ";
        $query = $query . "'" . $role_type . "', ";
        $query = $query . "'" . $first . "', ";
        $query = $query . "'" . $last . "', ";
        $query = $query . "'" . $home_address . "', ";
        $query = $query . "'" . $home_city . "', ";
        $query = $query . "'" . $home_state . "', ";
        $query = $query . "'" . $home_country . "', ";
        $query = $query . "'" . $home_zipcode . "', ";
        $query = $query . "'" . $phone . "')";

        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->execute_sql_query($query);
        if ($result == false) {
            return DBSUCCESSFUL;
        }
        return DBNOTSUCCESSFUL;
    }
*/
    public static function addNewUserAccount($email, $password) {
        $query = "INSERT INTO "
                . DBConf::$tables["USER_ACCOUNTS"] 
                . "(`UserName`, `Email`, `Password`"
                . ") VALUES (";
        $query = $query . "'" . $email . "', ";
        $query = $query . "'" . $email . "', ";
        $query = $query . "'" . $password . "')";

        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->execute_sql_query($query);
    }

    public static function checkUserAccountExistence($user_name) {
        $query = "SELECT count(*) from "
                . DBConf::$tables["USER_ACCOUNTS"] 
                . " WHERE UserName='"
                . $user_name . "'";
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->execute_sql_query($query);
        if ($result == false) {
            return DBNOTSUCCESSFUL;
        }
        
        return $result->fetch_row()[0] > 0;
    }
    
    public static function checkUserIDExistence($user_id) {
        $query = "SELECT count(*) from "
                . DBConf::$tables["USER_ACCOUNTS"] 
                . " WHERE ID='"
                . $user_id . "'";
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->execute_sql_query($query);
        if ($result == false) {
            return DBNOTSUCCESSFUL;
        }
        
        return $result->fetch_row()[0] > 0;
    }
    
    public static function getUserID($user_name) {
                $query = "SELECT ID from "
                . DBConf::$tables["USER_ACCOUNTS"]
                . " WHERE UserName='"
                . $user_name . "'";
        $connection = DBConnectionManager::getInstance()->getConnection();
        $result = $connection->execute_sql_query($query);
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
        $query = "INSERT INTO "
                . DBConf::$tables["CREDIT_CARDS"] . "(`UserId`, `CreditCardNum`, "
                . "`CreditCardExpMonth`, `CreditCardExpYear`,"
                . "`CreditCardAddress`, `CreditCardCity`,"
                . "`CreditCardState`, `CreditCardCountry`,"
                . "`CreditCardZipcode`, `CreditCardName`, "
                . "`CreditCardSecurityCode`"                
                . ") VALUES (";
        $query = $query . "'" . $user_id . "', ";
        $query = $query . "'" . $credit_card_number . "', ";
        $query = $query . "'" . $credit_card_exp_month . "', ";
        $query = $query . "'" . $credit_card_exp_year . "', ";
        $query = $query . "'" . $credit_card_address . "', ";
        $query = $query . "'" . $credit_card_city . "', ";
        $query = $query . "'" . $credit_card_state . "', ";
        $query = $query . "'" . $credit_card_country . "', ";
        $query = $query . "'" . $credit_card_zipcode . "', ";
        $query = $query . "'" . $name_on_card . "', ";
        $query = $query . "'" . $security_code . "')";

        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->execute_sql_query($query);
    }
    
    public static function addParkingLot($owner_id,  $address, $city, 
            $state, $country, $zipcode) {
        $query = "INSERT INTO "
                . DBConf::$tables["PARKING"] . "(`OwnerId`, "
                . "`Address`, `City`, `State`, `Country`, `Zipcode`"
                . ") VALUES (";
        $query = $query . "'" . $owner_id . "', ";
        $query = $query . "'" . $address . "', ";
        $query = $query . "'" . $city . "', ";
        $query = $query . "'" . $state . "', ";
        $query = $query . "'" . $country . "', ";
        $query = $query . "'" . $zipcode . "')";
       
        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->execute_sql_query($query);
    } 
    
    public static function updateParkingLotAvailability($id,  $status, $price, $available_start_time, $available_end_time) {
        $query = "UPDATE "
                . DBConf::$tables["PARKING"] . " SET ";
        $query = $query . "Status='" . $status . "', ";
        $query = $query . "ListedPrice='" . $price . "', ";
        $query = $query . "AvailableStartTime='" . $available_start_time . "', ";
        $query = $query . "AvailableEndTime='" . $available_end_time . "' ";

        $query = $query . "Where ID='" . $id . "'";
        $connection = DBConnectionManager::getInstance()->getConnection();
        return $connection->execute_sql_query($query);
    }
    
}
 