<?php

define("DBSUCCESSFUL", "-1");
define("DBNOTSUCCESSFUL", "-2");
define("DBNOCONNECTION", "-3");
define("USER_ACCOUNT_EXISTS", "-4");


class DBConf {

    public static $tables = array(
        // account related
        "USER_ACCOUNTS" => "user_accounts",
        "PARKING_OWNERS" => "parking_owners",
        "PARKING_CONSUMERS" => "parking_consumers",
        "PARKING" => "parking",
        "TRANSCATIONS" => "transcations",
        "CREDIT_CARDS" => "credit_cards",
        "BANK_ACCOUNTS" => "bank_accounts"
    );
    
    public static $databases = array(
        "PROD" => "prd",
        "STAGING" => "staging",
    );

}

