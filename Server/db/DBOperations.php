<?php

include_once 'def_db.php';
include_once 'DBConnection.php';

class DBOperations 
{
	public $m_connection;
	public $m_valid;
	
	// constructor
	public function __construct()
	{
		$this->m_valid = false;
		
		$this->m_connection = new DBConnection();
		$this->m_connection->connect();
		
		if ($this->m_connection->m_con)
			$this->m_valid = true;
	}
	
	public function __destruct()
	{
		$this->m_connection = null;
	}
	
	// Basic operations
	public function select($columns, $table, $conditions)
	{
		$sql = "SELECT ";
		
		$counter = 0;
		foreach ($columns as $column)
		{
			$counter = $counter + 1;
			
			if ($counter > 1)
				$sql = $sql . ",";
				
			$sql = $sql . $column;
		}
		
		$sql = $sql . " FROM " . $table . " WHERE ";
		
		$counter = 0;
		foreach ($conditions as $key => $value)
		{
			$counter = $counter + 1;
			
			if ($counter > 1)
				$sql = $sql . " AND ";
				
			$sql = $sql . $key . "='" . $value . "'";
		}
		//echo $sql . "\n";
		$result = mysql_query($sql);
		if (!$result)
		{
			return DBNOCONNECTION;
		}
		
		$row = mysql_fetch_array($result);
	
		if(empty($row[0]))
			return DBNOTSUCCESSFUL;

		return $row[0];
	}
	
	public function insert($table, $data)
	{
		$sql = "INSERT INTO " . $table . " (";
		
		$counter = 0;
		foreach ($data as $key => $value)
		{		
			$counter = $counter + 1;
			
			if ($counter > 1)
				$sql = $sql . ",";
			
			$sql = $sql . $key;
		}
		
		$sql = $sql . ") VALUES ('";

		$counter = 0;
		foreach ($data as $key => $value)
		{	
			$counter = $counter + 1;
			
			if ($counter > 1)
				$sql = $sql . "', '";
				
			$sql = $sql . $value;
		}
		
		$sql = $sql . "')";
		//echo $sql;
		$result = mysql_query($sql);
		
		if ($result)
			return DBSUCCESSFUL;
		else
			return DBNOTSUCCESSFUL;
	}
	
	public function delete($table, $conditions)
	{
		$sql = "DELETE FROM " . $table . " WHERE ";
		
		$counter = 0;
		foreach ($conditions as $key => $value)
		{	
			$counter = $counter + 1;
			
			if ($counter > 1)
				$sql = $sql . " AND ";
				
			$sql = $sql . $key . "='" . $value . "'";
		}
		
		//echo $sql . "\n";
		$result = mysql_query($sql);
		
		if ($result)
			return DBSUCCESSFUL;
		else
			return DBNOTSUCCESSFUL;
	}	

	// DB Operations
	public function isAccountExist($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
			
		$result = mysql_query("SELECT email FROM accounts WHERE email='" . mysql_real_escape_string($account). "'");
		
		if ($result && !empty(mysql_fetch_row($result)))
			return DBSUCCESSFUL;
		
		return DBNOTSUCCESSFUL;
	}
	
	public function addAccount($account, $password, $roletype, $first, $last, $add, $phone, $ssn, $rout, $accnum, $businame, $busides, $dev)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "INSERT INTO `accounts`(`email`, `password`, `roletype`, `firstname`, `lastname`, `address`, `phone`, `ssn`, `routingnumber`, `accountnumber`, `businessname`, `businessdescription`) VALUES (";
		$query = $query . "'" . $account . "', ";
		$query = $query . "'" . password_hash($password, PASSWORD_BCRYPT) . "', ";	
		$query = $query . "'" . $roletype . "', ";
		$query = $query . "'" . $first . "', ";
		$query = $query . "'" . $last . "', ";
		$query = $query . "'" . $add . "', ";
		$query = $query . "'" . $phone . "', ";
		$query = $query . "'" . $ssn . "', ";
		$query = $query . "'" . $rout . "', ";
		$query = $query . "'" . $accnum . "', ";
		$query = $query . "'" . $businame . "', ";
		$query = $query . "'" . $busides . "')";
		
		//echo $query;
		if ($result = mysql_query($query))
			return DBSUCCESSFUL;
			
		return DBNOTSUCCESSFUL;
	}
	
	public function verifyPasswd($account, $password)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT `password` FROM `accounts` WHERE `email`=";
		$query = $query . "'" . $account . "';";
		
		//echo $query
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
		
		$row = mysql_fetch_array($result);
		
		if (password_verify($password, $row[0]))
			return DBSUCCESSFUL;
			
		return DBNOTSUCCESSFUL;
	}	
	
	public function addSession($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		if ($this->delSessions($account) == DBNOCONNECTION)
		{
			return DBNOCONNECTION;
		}

		$sessionid = hash("sha256", uniqid() . uniqid(). date(DATE_ATOM));

		$query = "INSERT INTO `sessions`(`email`, `sessionid`, `createdtime`, `valid`) VALUES (";
		$query = $query . "'" . $account . "', ";
		$query = $query . "'" . $sessionid . "', ";
		$query = $query . "'" . date("Y-m-d H:i:s") . "', ";
		$query = $query . "'1')";

		//echo $query;
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		return $sessionid;
	}

	public function delSessions($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "UPDATE `sessions` SET `valid`=0 WHERE `email` = ";
		$query = $query . "'" . $account. "'";
		
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		return DBSUCCESSFUL;		
	}
	
	public function delSession($account, $sessionid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "UPDATE `sessions` SET `valid`=0 WHERE `email` = ";
		$query = $query . "'" . $account. "'";
		$query = $query . " AND `sessionid` = '";
		$query = $query . $sessionid . "'";
		
		//echo $query;
		
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		return DBSUCCESSFUL;
	}

	public function delSpecificSession($sessionid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "UPDATE `sessions` SET `valid`=0 WHERE `sessionid` = '";
		$query = $query . $sessionid . "'";
		
		//echo $query;
		
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		return DBSUCCESSFUL;
	}
	
	/*
	public function verifySession($account, $sessionid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT * FROM `sessions` WHERE `email`=";
		$query = $query . "'" . $account . "' AND `sessionid`='";
		$query = $query . $sessionid . "'";
		
		//echo $query;
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
		
		return DBSUCCESSFUL;
	}*/
	
	public function verifySession($sessionid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT * FROM `sessions` WHERE `sessionid`='";
		$query = $query . $sessionid . "' AND `valid`='1'";
		
		//echo $query;
		$result = mysql_query($query);
		
		if ($result && !empty(mysql_fetch_row($result)))
			return DBSUCCESSFUL;
		
		return DBNOTSUCCESSFUL;
	}
	
	public function getEmailBySession($sessionid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$column = array("email");
		$table = "sessions";
		$keys = array("sessionid");
		$values = array($sessionid);
		
		$conditions = array_combine($keys, $values);
		
		$result = $this->select($column, $table, $conditions);
		
		return $result;
	}
	
	public function getConsumerHistoryBySessionID($sessionid)
	{
		//Format 
		// MerchantName1#MerchantDes1#MerchantType1#Total1##
		$retvalue = "";
		$count = 0;
		$consumer = $this->getEmailBySession($sessionid);
		
		if ($consumer == DBNOCONNECTION)
			return $consumer;
			
		$query = "SELECT `merchant`, `total`, `createdtime` FROM `transactions` WHERE `customer`='" . $consumer . "' AND `status`='closed'";
		$result = mysql_query($query);

		while (($row = mysql_fetch_row($result)))
		{
			if ($count != 0)
				$retvalue = $retvalue . "##";

			$merchantemail = $row[0];
			$total = $row[1];
			$createdtime = $row[2];
			
			$query1 = "SELECT `businessdescription`, `businessname`, `address`, `phone` FROM `accounts` WHERE `email`='" . $merchantemail . "' AND `roletype`='merchant'";
			$result1 = mysql_query($query1);
			
			$row1 = mysql_fetch_row($result1);
			
			$busides = $row1[0];
			$businame = $row1[1];
			$busiadd = $row1[2];
			$busiphone = $row1[3];
			
			$retvalue = $retvalue . $businame . "#" . $busides . "#" . $busiadd . "#" . $busiphone . "#" . $total . "#" . $createdtime;
			
			$count = $count + 1;
		}
		
		return $retvalue;
	}	
	
	public function chargeTotal($sessionid, $datetime, $tranid, $total)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get email
		$query = "SELECT email FROM `sessions` WHERE `sessionid`='";
		$query = $query . $sessionid . "'";
		
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]))
			return DBNOTSUCCESSFUL;
			
		$account = $row[0];
		
		// Add transaction record
		$query = "INSERT INTO `transactions`(`merchant`, `tranid`, `total`, `createdtime`, `status`) VALUES ('";
		$query = $query . $account . "', ";
		$query = $query . "'" . $tranid . "', ";
		$query = $query . "'" . $total . "', ";
		$query = $query . "'" . $datetime . "', ";
		$query = $query . "'open')";
		//echo $query;
		$result = mysql_query($query);
		
		if ($result)
			return DBSUCCESSFUL;
		else
			return DBNOTSUCCESSFUL;
	}	
	
	public function verifyCharge($tranid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get email
		$query = "SELECT status FROM `transactions` WHERE `tranid`='";
		$query = $query . $tranid . "'";
		
		//echo $query;
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]))
			return DBNOCONNECTION;
			
		$status = $row[0];
		
		if (!strcmp("closed", $row[0]))
			return DBSUCCESSFUL;
		else
			return DBNOTSUCCESSFUL;
	}	
	
	public function verifyTransaction($tranid, $total)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get status
		$query = "SELECT * FROM `transactions` WHERE `tranid`='";
		$query = $query . $tranid . "' AND `total`='";
		$query = $query . $total . "'";
		
		//echo $query;
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]))
			return DBNOTSUCCESSFUL;
			
		return DBSUCCESSFUL;
	}
	
	public function updateTransaction($consumer, $tranid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get status
		$query = "UPDATE `transactions` SET `status`='closed', `customer`='" . $consumer . "' WHERE `tranid`='";
		$query = $query . $tranid . "'";
			
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		return DBSUCCESSFUL;
	}

	public function getMerchantAccount($tranid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT merchant FROM `transactions` WHERE `tranid`='";
		$query = $query . $tranid . "'";

		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]))
			return DBNOTSUCCESSFUL;
			
		return $row[0];
	}
	
	public function getMerchantBankAccount($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT routingnumber, accountnumber FROM `accounts` WHERE `email`='";
		$query = $query . $account . "'";

		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0])&&empty($row[1]))
			return DBNOTSUCCESSFUL;
			
		return $row[0] . "###" . $row[1];
	}
	
	public function getField($account, $field)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT " . $field . " FROM `accounts` WHERE `email`='";
		$query = $query . $account . "'";

		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]))
			return "";
			
		return $row[0];
	}	
	
	public function getCards($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT `cardnumber`, `cardtype` FROM `cards` WHERE `email`='";
		$query = $query . $account . "'";
		
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$ret = "";
			
		while ($row = mysql_fetch_row($result))
		{
			$ret = $ret . "##";
			$promotion = $this->getPromotion($row[0]);
			$ret = $ret . substr($row[0], strlen($row[0]) - 4, 4) . "#" . $row[1]. "#" . $promotion;
		}
			
		return $ret;
	}
	
	public function getPromotion($cardnumber)
	{
		if (preg_match('/' . "^4[0-9]{12}(?:[0-9]{3})?$" . '/', $cardnumber) == 1)
			return "5";
		else if (preg_match('/' . "^5[1-5][0-9]{14}$" . '/', $cardnumber) == 1)
			return "2";
		else if (preg_match('/' . "^3[47][0-9]{13}$" . '/', $cardnumber) == 1)
			return "2";
		else if (preg_match('/' . "^6(?:011|5[0-9]{2})[0-9]{12}$" . '/', $cardnumber) == 1)
			return "2";
		else if (preg_match('/' . "^(?:2131|1800|35\d{3})\d{11}$" . '/', $cardnumber) == 1)
			return "1";
		else	
			return "1";
	}
	
	public function getFullCardNumber($cardlast)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT `cardnumber` FROM `cards`";	
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$ret = "";
			
		while ($row = mysql_fetch_row($result))
		{
			if (strpos($row[0], $cardlast) != false)
				return $row[0];
		}
			
		return DBNOTSUCCESSFUL;
	}
	
	public function isCardExistForAccount($account, $cardnumber, $expmonth, $expyear, $nameoncard)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$column = array("cardnumber");
		$table = "cards";
		$keys = array("cardnumber", "email", "expmonth", "expyear", "nameoncard");
		$values = array($cardnumber, $account, $expmonth, $expyear, $nameoncard);
		
		$conditions = array_combine($keys, $values);
		
		$result = $this->select($column, $table, $conditions);
	
		if ($result == DBNOTSUCCESSFUL)
			return DBNOTSUCCESSFUL;
		else if ($result == DBNOCONNECTION)
			return DBNOCONNECTION;
		else 
			return DBSUCCESSFUL;
	}
	
	public function addCard($account, $cardnumber, $expmonth, $expyear, $nameoncard)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$table = "cards";
		$keys = array("cardnumber", "email", "expmonth", "expyear", "nameoncard", "cardtype");
		$values = array($cardnumber, $account, $expmonth, $expyear, $nameoncard, $this->getCardTypefromNumber($cardnumber));
		
		$data = array_combine($keys, $values);
		
		$result = $this->insert($table, $data);
		
		//for demo only
		$this->FakeBankaddCard($cardnumber);
		
		return $result;
	}
	
	public function deleteCard($account, $cardnumber, $expmonth, $expyear, $nameoncard)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$table = "cards";
		$keys = array("cardnumber", "email", "expmonth", "expyear", "nameoncard");
		$values = array($cardnumber, $account, $expmonth, $expyear, $nameoncard);
		
		$data = array_combine($keys, $values);
		
		$result = $this->delete($table, $data);
		
		return $result;
	}

	public function getCardTypefromNumber($cardnumber)
	{
		if (preg_match('/' . "^4[0-9]{12}(?:[0-9]{3})?$" . '/', $cardnumber) == 1)
			return "visa";
		else if (preg_match('/' . "^5[1-5][0-9]{14}$" . '/', $cardnumber) == 1)
			return "master";
		else if (preg_match('/' . "^3[47][0-9]{13}$" . '/', $cardnumber) == 1)
			return "americanexpress";
		else if (preg_match('/' . "^6(?:011|5[0-9]{2})[0-9]{12}$" . '/', $cardnumber) == 1)
			return "discover";
		else if (preg_match('/' . "^(?:2131|1800|35\d{3})\d{11}$" . '/', $cardnumber) == 1)
			return "jcb";
		else	
			return "unknown";
	}
	
	public function addItem($account, $itemname, $unitcost)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$table = "items";
		$keys = array("email", "itemname", "unitcost");
		$values = array($account, $itemname, $unitcost);
		
		$data = array_combine($keys, $values);
		
		$result = $this->insert($table, $data);
				
		return $result;
	}
	
	public function getItems($account)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$query = "SELECT `itemname`, `unitcost` FROM `items` WHERE `email`='";
		$query = $query . $account . "'";
		
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$ret = "";
			
		while ($row = mysql_fetch_row($result))
		{
			$ret = $ret . "##";
			$ret = $ret . $row[0] . "#" . $row[1];
		}
			
		return $ret;
	}
	
	// Fake Bank Operations
	public function depositBusiness($bankaccount, $total)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get status
		$query = "SELECT balance FROM `bankbusiness` WHERE `routingaccount`='";
		$query = $query . $bankaccount . "'";
		
		//echo $query;
		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if ($row[0] != 0 && empty($row[0]))
			return DBNOCONNECTION;
			
		// Update 
		$newbalance = $row[0] + $total;
		$query = "UPDATE `bankbusiness` SET `balance`=";
		$query = $query . "'" . $newbalance . "'";
		$query = $query . " WHERE `routingaccount` ='";
		$query = $query . $bankaccount . "'";
			
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
					
		return DBSUCCESSFUL;
	}
	
	public function chargeCredit($cardnumber, $total)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get status
		$query = "SELECT balance, creditlimit FROM `bankcards` WHERE `cardnumber`='";
		$query = $query . $cardnumber . "'";

		$result = mysql_query($query);
		
		if (!$result)
			return DBNOCONNECTION;
			
		$row = mysql_fetch_row($result);
		if (empty($row[0]) && empty($row[1]))
		{
			return DBNOTSUCCESSFUL;
		}

		$balance = $row[0];
		$creditlimit = $row[1];
		
		if ($balance + $total > $creditlimit)
			return DBNOTSUCCESSFUL;
			
		// Update 
		$newbalance = $balance + $total;
		$query = "UPDATE `bankcards` SET `balance`=";
		$query = $query . "'" . $newbalance . "'";
		$query = $query . " WHERE `cardnumber` ='";
		$query = $query . $cardnumber . "'";
			
		$result = mysql_query($query);
		if (!$result)
			return DBNOCONNECTION;
			
		return DBSUCCESSFUL;
	}	
	
	public function FakeBankaddCard($cardnumber)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$table = "bankcards";
		$keys = array("cardnumber", "balance", "creditlimit");
		$values = array($cardnumber, "1", "20000");
		
		$data = array_combine($keys, $values);
		
		$result = $this->insert($table, $data);
		
		return $result;
	}
	
	public function FakeBankaddBusinessAccount($routingaccount)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		$table = "bankbusiness";
		$keys = array("routingaccount", "balance");
		$values = array($routingaccount, "1");
		
		$data = array_combine($keys, $values);
		
		$result = $this->insert($table, $data);
		
		return $result;
	}
}

?>