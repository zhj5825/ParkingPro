<?php
include_once 'def_db.php';
include_once 'DBConnection.php';

class DBOperations 
{
	public $m_connection;
	public $m_valid;
	
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
	
	public function chargeTotal($sessionid, $datetime, $tranid, $total)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get email
		$query = "SELECT email FROM `sessions` WHERE `sessionid`='";
		$query = $query . $sessionid . "'";
		
		//echo $query;
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
	
	public function updateTransaction($tranid)
	{
		if (!$this->m_valid)
		{
			return DBNOCONNECTION;
		}
		
		// Get status
		$query = "UPDATE `transactions` SET `status`='closed' WHERE `tranid`='";
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
			return DBNOTSUCCESSFUL;
			
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
			$ret = $ret . substr($row[0], strlen($row[0]) - 4, 4) . "#" . $row[1];
		}
			
		return $ret;
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
	
	// Bank Operations
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
}

?>