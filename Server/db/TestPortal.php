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

list($success, $messege) = DBLogicOperations::addCreditCard($email,  "123", "11" , "14", "180 Alicante Dr", "San Jose", "CA", "US", "12345", "SB" , "123");
if ($success == false) {
    echo "DBLogicOperations::addCreditCard failed: " . $messege;
}  


echo "Test finished!..................";
