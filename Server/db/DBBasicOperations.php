<?php

include_once 'def_db.php';
include_once 'DBConnection.php';

class DBBasicOperations 
{
	public function __construct()
	{
	}
	
	public function __destruct()
	{
	}

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
		echo $sql;
		$result = mysql_query($sql);
		if (!$result)
			return DBNOCONNECTION;
			
		echo "hhh";
			
		if ($result && !empty(mysql_fetch_array($result)))
		{
			$row = mysql_fetch_array($result);
			echo $row[0];
			//return mysql_fetch_row($result);
		}
		
		return DBNOTSUCCESSFUL;
	}
	
	public static function insert($table, $data)
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
		echo $sql;
		$result = mysql_query($sql);
		
		return $result;
	}
	
	public static function update()
	{
	}
}

?>