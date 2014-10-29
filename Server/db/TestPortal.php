<?php

include_once 'DBUtilOperations.php';
include_once 'DBLogicOperations.php';

echo "Starting test!..................";

function generateRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

$email = generateRandomString(10) . "@gmail.com";
$password = generateRandomString(10);

if (DBUtilOperations::checkUserAccountExistence($email) == true) {
    echo "DBUtilOperations::checkUserAccountExistence failed on test of checking nonexisting user";
}

if (DBUtilOperations::addNewUserAccount($email, $password) == false) {
    echo "DBUtilOperations::addNewUserAccount failed on test of adding nonexisting user";
}

if (DBUtilOperations::checkUserAccountExistence($email) == false) {
    echo "DBUtilOperations::checkUserAccountExistence failed on test of checking existing user";
}

list($success, $messege) = DBLogicOperations::addNewUserAccount($email, $password);
if ($success == true) {
    echo "DBLogicOperations::addNewUserAccount failed on test of adding existing user";
}

$email = generateRandomString(4) . "@gmail.com";
$password = generateRandomString(5);
list($success, $messege) = DBLogicOperations::addNewUserAccount($email, $password);
if ($success == false) {
    echo "DBLogicOperations::addNewUserAccount failed on test of adding nonexisting user";
}

list($success, $messege) = DBLogicOperations::addCreditCardByName($email,  "123", "11" , "14", "180 Alicante Dr", "San Jose", "CA", "US", "12345", "SB" , "123");
if ($success == false) {
    echo "DBLogicOperations::addCreditCard failed: " . $messege;
} 
    
list($success, $messege) = DBLogicOperations::addParkingLotByName($email,  "180 Alicante Dr", "San Jose", "CA", "US", "12345");
if ($success == false) {
    echo "DBLogicOperations::addParkingLotByName failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::setParkingLotActive(1,  "100", "1001", "1002");
if ($success == false) {
    echo "DBLogicOperations::setParkingLotActive failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::addParkingLotByName($email,  "181 Alicante Dr", "San Jose", "CA", "US", "12345");
if ($success == false) {
    echo "DBLogicOperations::addParkingLotByName failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::setParkingLotInactive(2);
if ($success == false) {
    echo "DBLogicOperations::setParkingLotInactive failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::selectParkingLotsByZipcode("12345");
if ($success == false ) {
    echo "DBLogicOperations::selectParkingLotsByZipcode failed: " . $messege;
} else if ($messege->num_rows == 0 ) {
  echo "DBLogicOperations::selectParkingLotsByZipcode failed: " . "zero parking lot is found";
}
list($success, $messege) = DBLogicOperations::selectParkingLotsByAddress("180 Alicante Dr", "San Jose", "CA", "US");
if ($success == false ) {
    echo "DBLogicOperations::selectParkingLotsByAddress failed: " . $messege;
} else if ($messege->num_rows == 0 ) {
    echo "DBLogicOperations::selectParkingLotsByAddress failed: " . "zero parking lot is found";
}

echo "Test finished!..................";
