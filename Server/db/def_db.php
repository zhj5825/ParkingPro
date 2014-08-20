<?php
define ("DBSUCCESSFUL", "1");
define ("DBNOTSUCCESSFUL", "2");
define ("DBNOCONNECTION", "3");

class DBConf
{
	public static $tables = array(
		"ACCOUNT" => "account", 
		"CARDS" => "cards", 
		"DEVICES" => "devices", 
		"SESSIONS" => "sessions", 
		"TRANSACTIONS" => "transactions", 
		// fake bank
		"MERCHANTBANKACCOUNT" => "bankbusiness", 
		"CONSUMERCARD" => "bankcards"
	);
}

?>