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

list($success, $messege) = DBLogicOperations::setParkingLotAvailable(1,  "100", "1001", "1002");
if ($success == false) {
    echo "DBLogicOperations::setParkingLotAvailable failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::addParkingLotByName($email,  "180 Alicante Dr", "San Jose", "CA", "US", "12345");
if ($success == false) {
    echo "DBLogicOperations::addParkingLotByName failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::setParkingLotUnavailable(2,  "200", "2001", "2002");
if ($success == false) {
    echo "DBLogicOperations::SetParkingLotUnavailable failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::addParkingLotByName($email,  "180 Alicante Dr", "San Jose", "CA", "US", "12345");
if ($success == false) {
    echo "DBLogicOperations::addParkingLotByName failed: " . $messege;
} 

list($success, $messege) = DBLogicOperations::setParkingLotInactive(3);
if ($success == false) {
    echo "DBLogicOperations::setParkingLotInactive failed: " . $messege;
} 

echo "Test finished!..................";
