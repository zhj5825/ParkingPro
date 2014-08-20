<?php

include_once 'def_protocol.php';
include_once '../db/DBOperations.php';

class Request
{
	public static $methods = array(
		"SIGNUP", 
		"LOGIN", 
		"LOGOUT", 
		"CHARGE", 
		"VERIFYCHARGE", 
		"PAY", 
		"ADDPAYMT", 
		"DELPAYMT", 
		"PROMO", 
		"VIEWHISTO",
		"ADDITEM"
	);
	
	public static $headers = array(
		"EMAIL" => "Email: ",
		"PASSWORD" => "Password: ",
		"ROLETYPE" => "Roletype: ",
		"FIRSTNAME" => "Firstname: ",
		"LASTNAME" => "Lastname: ",
		"ADDRESS" => "Address: ",
		"PHONE" => "Phone: ",
		"SSN" => "SSN: ",
		"ROUTINGNUMBER" => "Routing-Number: ",
		"ACCOUNTNUMBER" => "Account-Number: ",
		"BUSINESSNAME" => "Business-Name: ",
		"BUSINESSDESCPRIPTION" => "Business-Description: ",
		"DEVICE" => "Device: ",
		"SESSIONID" => "Session-Id: ",
		"DATETIME" => "Datetime: ",
		"TRANSACTIONID" => "Transaction-Id: ",
		"TOTAL" => "Total: ",
		"PAYMENTMT" => "Payment-Method: ", // card number
		"PAYMENTMTS" => "Payment-Methods: ",
		"PAYMENTTYPE" => "Payment-Type: ",
		"EXPMONTH" => "Expiration-Month: ",
		"EXPYEAR" => "Expiration-Year: ",
		"EXPDATE" => "Expiration-Date: ",
		"NAMEONCARD" => "Name-on-Card: ",
		"SECURITYCODE" => "Security-Code: ",
		"HISTORY" => "History: ",
		"ITEMNAME" => "Itemname: ",
		"UNITCOST" => "Unitcost: ",
		"ITEMS" => "Items: "
	);
	
	public $m_error;
	public $m_mppversion;
	public $m_mpprequestmethod;
	public $m_lines;
	
	public $m_responseheaders;

	public function __construct($data)
	{
		$this->m_responseheaders = array();
		$this->m_error = STATUS5000;
		$this->parse($data);
	}
	
	public function generateResponse()
	{
		$data = MPPVERSION10 . $this->m_error . "\r\n";
		
		foreach ($this->m_responseheaders as $line)
		{
			$data = $data . $line . "\r\n";
		}
		
		$data = $data . "\r\n";
		
		echo $data;
		//echo base64_encode($data);
	}
	
	public function parse($data)
	{
		// Check format, protocol version, method type
		$this->m_error = STATUS2000;
		
		if (strlen($data) == 0)
		{
			$this->m_error = STATUS5000;
			return;
		}
			
		$this->m_lines = explode("\r\n", $data);
		if (count($this->m_lines) == 0)
		{
			$this->m_error = STATUS5000;
			return;
		}
		
		$this->m_mppversion = strstr($this->m_lines[0], MPPVERSION);
		if (strncmp($this->m_mppversion, MPPVERSION10, strlen(MPPVERSION)) != 0)
		{
			$this->m_error = STATUS5001;
			return;
		}
		
		$this->m_mpprequestmethod = substr($this->m_lines[0], 0, strpos($this->m_lines[0], " "));
		if (!in_array($this->m_mpprequestmethod, self::$methods))
		{
			$this->m_error = STATUS5099;
			return;
		}
			
		$this->dispatch();
	}
	
	public function dispatch()
	{
		switch ($this->m_mpprequestmethod)
		{
			case 'SIGNUP':
				$this->parseSIGNUP();
				break;
			case 'LOGIN':
				$this->parseLOGIN();
				break;
			case 'LOGOUT':
				$this->parseLOGOUT();
				break;
			case 'CHARGE':
				$this->parseCHARGE();
				break;
			case 'VERIFYCHARGE':
				$this->parseVERIFYCHARGE();
				break;
			case 'PAY':
				$this->parsePAY();
				break;
			case 'ADDPAYMT':
				$this->parseADDPAYMT();
				break;
			case 'DELPAYMT':
				$this->parseDELPAYMT();
				break;
			case 'PROMO':
				$this->parsePROMO();
				break;
			case 'VIEWHISTO':
				$this->parseVIEWHISTO();
				break;
			case 'ADDITEM':
				$this->parseADDITEM();
				break;
		}
	}
	
	public function parseSIGNUP()
	{
		$account = mysql_real_escape_string($this->searchHeader(self::$headers["EMAIL"]));
		$password = $this->searchHeader(self::$headers["PASSWORD"]);
		$roletype = mysql_real_escape_string($this->searchHeader(self::$headers["ROLETYPE"]));
		$first = mysql_real_escape_string($this->searchHeader(self::$headers["FIRSTNAME"]));
		$last = mysql_real_escape_string($this->searchHeader(self::$headers["LASTNAME"]));
		$add = mysql_real_escape_string($this->searchHeader(self::$headers["ADDRESS"]));
		$phone = mysql_real_escape_string($this->searchHeader(self::$headers["PHONE"]));
		$ssn = mysql_real_escape_string($this->searchHeader(self::$headers["SSN"]));
		$rout = mysql_real_escape_string($this->searchHeader(self::$headers["ROUTINGNUMBER"]));
		$accnum = mysql_real_escape_string($this->searchHeader(self::$headers["ACCOUNTNUMBER"]));
		$businame = mysql_real_escape_string($this->searchHeader(self::$headers["BUSINESSNAME"]));
		$busides = mysql_real_escape_string($this->searchHeader(self::$headers["BUSINESSDESCPRIPTION"]));
		$dev = mysql_real_escape_string($this->searchHeader(self::$headers["DEVICE"]));
		
		if (empty($account) || strlen($account) == 0)
		{
			$this->m_error = STATUS4301;
			return;
		}
		
		$db = new DBOperations();
		$result = $db->isAccountExist($account);
		
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBSUCCESSFUL)
		{
			$this->m_error = STATUS4302;
			return;
		}
		
		//$result == DBNOTSUCCESSFUL. The account is available.
		$result = $db->addAccount($account, $password, $roletype, $first, $last, $add, $phone, $ssn, $rout, $accnum, $businame, $busides, $dev);
		
		if ($result == DBNOCONNECTION || $result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5098;
			return;
		}
		
		$result = $db->FakeBankaddBusinessAccount($rout . "#" . $accnum);
		
		if (strcmp($roletype, "merchant") == 0)
		{
			array_push($this->m_responseheaders, self::$headers["EMAIL"] . $account);
			array_push($this->m_responseheaders, self::$headers["FIRSTNAME"] . $first);
			array_push($this->m_responseheaders, self::$headers["LASTNAME"] . $last);
			array_push($this->m_responseheaders, self::$headers["ADDRESS"] . $add);
			array_push($this->m_responseheaders, self::$headers["PHONE"] . $phone);
			array_push($this->m_responseheaders, self::$headers["BUSINESSNAME"] . $businame);
			array_push($this->m_responseheaders, self::$headers["BUSINESSDESCPRIPTION"] . $busides);
		}
		else
		{
			array_push($this->m_responseheaders, self::$headers["EMAIL"] . $account);
			array_push($this->m_responseheaders, self::$headers["FIRSTNAME"] . $first);
			array_push($this->m_responseheaders, self::$headers["LASTNAME"] . $last);
		}
	}
	
	public function parseLOGIN()
	{
		$account = mysql_real_escape_string($this->searchHeader(self::$headers["EMAIL"]));
		$password = $this->searchHeader(self::$headers["PASSWORD"]);
		$role = $this->searchHeader(self::$headers["ROLETYPE"]);
		
		$db = new DBOperations();
		
		// if acccout exists
		$result = $db->isAccountExist($account);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4401;
			return;
		}
		
		// if passwd is correct
		$result = $db->verifyPasswd($account, $password);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4401;
			return;
		}	

		// if role is correct
		$result = $db->getField($account, "roletype");
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if (strcmp($result, $role) != 0)
		{
			$this->m_error = STATUS4402;
			return;
		}		
		
		// create session-id
		$result = $db->addSession($account);
		if ($result == DBNOCONNECTION || $result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5098;
			return;
		}
		
		array_push($this->m_responseheaders, self::$headers["SESSIONID"] . $result);
		array_push($this->m_responseheaders, self::$headers["EMAIL"] . $account);		
		
		if (strcmp($db->getField($account, "roletype"), "merchant") == 0)
		{
			array_push($this->m_responseheaders, self::$headers["FIRSTNAME"] . $db->getField($account, "firstname"));
			array_push($this->m_responseheaders, self::$headers["LASTNAME"] . $db->getField($account, "lastname"));
			array_push($this->m_responseheaders, self::$headers["ADDRESS"] . $db->getField($account, "address"));
			array_push($this->m_responseheaders, self::$headers["PHONE"] . $db->getField($account, "phone"));
			array_push($this->m_responseheaders, self::$headers["BUSINESSNAME"] . $db->getField($account, "businessname"));	
			array_push($this->m_responseheaders, self::$headers["BUSINESSDESCPRIPTION"] . $db->getField($account, "businessdescription"));
			array_push($this->m_responseheaders, self::$headers["ITEMS"] . $db->getItems($account));			
		}
		else
		{
			array_push($this->m_responseheaders, self::$headers["FIRSTNAME"] . $db->getField($account, "firstname"));
			array_push($this->m_responseheaders, self::$headers["LASTNAME"] . $db->getField($account, "lastname"));	
			array_push($this->m_responseheaders, self::$headers["PAYMENTMTS"] . $db->getCards($account));
			array_push($this->m_responseheaders, self::$headers["HISTORY"] . $db->getConsumerHistoryBySessionID($result));		
		}
	}

	public function parseLOGOUT()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));

		// Only if it is in the status of LOGIN, it can log out.
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			//return 2000 OK
			return;
		}
		
		$result = $db->delSpecificSession($sessionid);
		if ($result == DBNOCONNECTION || $result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5098;
			return;
		}
	}

	public function parseCHARGE()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		$datetime = mysql_real_escape_string($this->searchHeader(self::$headers["DATETIME"]));
		$tranid = mysql_real_escape_string($this->searchHeader(self::$headers["TRANSACTIONID"]));
		$total = mysql_real_escape_string($this->searchHeader(self::$headers["TOTAL"]));
		
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$result = $db->chargeTotal($sessionid, $datetime, $tranid, $total);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
	}		
	
	public function parseVERIFYCHARGE()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		$tranid = mysql_real_escape_string($this->searchHeader(self::$headers["TRANSACTIONID"]));
		
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$result = $db->verifyCharge($tranid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4501;
			return;
		}
	}	
	
	public function parsePAY()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		$tranid = mysql_real_escape_string($this->searchHeader(self::$headers["TRANSACTIONID"]));
		$total = mysql_real_escape_string($this->searchHeader(self::$headers["TOTAL"]));
		$paymt = mysql_real_escape_string($this->searchHeader(self::$headers["PAYMENTMT"]));
		
		// verify session
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		// verify charge
		$result = $db->verifyCharge($tranid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBSUCCESSFUL)
		{
			$this->m_error = STATUS4502;
			return;
		}
		
		// verify transaction
		$result = $db->verifyTransaction($tranid, $total);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4503;
			return;
		}
		
		// get credit number
		$cardlast = $paymt;
		$result = $db->getFullCardNumber($cardlast);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4503;
			return;
		}
			
		// charge credit card
		$bank = new BankConnection();
		$cardnumber = $result;
		$result = $bank->chargeCredit($cardnumber, $total);
		if ($result == BANK1)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == BANK2)
		{
			$this->m_error = STATUS5501;
			return;
		}
		
		// deposit to business
		$result = $db->getMerchantAccount($tranid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4503;
			return;
		}
		
		$email = $result;
		
		$result = $db->getMerchantBankAccount($email);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4503;
			return;
		}
		
		$bankaccount = $result;
				
		$result = $bank->depositBusiness($bankaccount, $total);
		if ($result == BANK1)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == BANK2)
		{
			$this->m_error = STATUS5501;
			return;
		}
		
		// update transaction consumer and 'closed'
		$result = $db->getEmailBySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		$consumer = $result;
		$result = $db->updateTransaction($consumer, $tranid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS4503;
			return;
		}
		
		array_push($this->m_responseheaders, self::$headers["HISTORY"] . $db->getConsumerHistoryBySessionID($sessionid));
	}
	
	public function parseADDPAYMT()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$result = $db->getEmailBySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$paymentmt = mysql_real_escape_string($this->searchHeader(self::$headers["PAYMENTMT"]));
		$expmonth = mysql_real_escape_string($this->searchHeader(self::$headers["EXPMONTH"]));
		$expyear = mysql_real_escape_string($this->searchHeader(self::$headers["EXPYEAR"]));
		$nameoncard = mysql_real_escape_string($this->searchHeader(self::$headers["NAMEONCARD"]));
		
		$account = $result;
		$result = $db->isCardExistForAccount($account, $paymentmt, $expmonth, $expyear, $nameoncard);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBSUCCESSFUL)
		{
			$this->m_error = STATUS3302;
			return;
		}
		
		$result = $db->addCard($account, $paymentmt, $expmonth, $expyear, $nameoncard);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		array_push($this->m_responseheaders, self::$headers["PAYMENTMTS"] . $db->getCards($account));
	}	
	
	public function parseDELPAYMT()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$result = $db->getEmailBySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		$account = $result;		
		$paymentmt = mysql_real_escape_string($this->searchHeader(self::$headers["PAYMENTMT"]));
		$expmonth = mysql_real_escape_string($this->searchHeader(self::$headers["EXPMONTH"]));
		$expyear = mysql_real_escape_string($this->searchHeader(self::$headers["EXPYEAR"]));
		$nameoncard = mysql_real_escape_string($this->searchHeader(self::$headers["NAMEONCARD"]));
		
		$result = $db->getFullCardNumber($paymentmt);
		if ($result == DBNOTSUCCESSFUL || $result == DBNOCONNECTION)
		{
			$this->m_error = STATUS3303;
			return;
		}
			
		$paymentmt = $result;

		$result = $db->isCardExistForAccount($account, $paymentmt, $expmonth, $expyear, $nameoncard);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS3303;
			return;
		}

		$result = $db->deleteCard($account, $paymentmt, $expmonth, $expyear, $nameoncard);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5098;
			return;
		}
	}	
	
	public function parseADDITEM()
	{
		$sessionid = mysql_real_escape_string($this->searchHeader(self::$headers["SESSIONID"]));
		
		$db = new DBOperations();	
		$result = $db->verifySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$result = $db->getEmailBySession($sessionid);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		$itemname = mysql_real_escape_string($this->searchHeader(self::$headers["ITEMNAME"]));
		$unitcost = mysql_real_escape_string($this->searchHeader(self::$headers["UNITCOST"]));
		
		$account = $result;
		
		$result = $db->addItem($account, $itemname, $unitcost);
		if ($result == DBNOCONNECTION)
		{
			$this->m_error = STATUS5098;
			return;
		}
		else if ($result == DBNOTSUCCESSFUL)
		{
			$this->m_error = STATUS5097;
			return;
		}
		
		array_push($this->m_responseheaders, self::$headers["ITEMS"] . $db->getItems($account));
	}		
	
	// Given header key, search for value
	private function searchHeader($key)
	{	
		foreach ($this->m_lines as $line)
		{
			if (strstr($line, $key))
			{
				return substr($line, strlen($key));
			}
		}
	}
}

?>