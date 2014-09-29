<?php

include_once 'DatabaseConfig.php';
include_once 'DBConnectionManager.php';

class DBUtilOperations 
{
    // constructor
    public function __construct() {        
    }

    public function __destruct() {        
    }

    public function addAccount($user_name, $email, $password, $role_type, $first, 
                $last, $home_address, $home_city, $home_state, $home_country,
                $home_zipcode, $credit_card_number, $credit_card_exp_month,
                $credit_card_exp_year, $credit_card_address, $credit_card_city, 
                $credit_card_state, $credit_card_country, $credit_card_zipcode,
                $name_on_card, $security_code, $phone) { 
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

}
