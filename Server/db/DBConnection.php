<?php
class DBConnection
{
	public $m_con;
	
	public function __construct()
	{
		$this->m_con = false;
	}
	
	public function __destruct()
	{
		if ($this->m_con)
		{
			//mysql_close($this->m_con);
			//$this->m_con = false;
			//debug_print_backtrace();
		}
	}
	
	public function connect()
	{
		$this->m_con = mysql_connect("localhost","root","");
		
		if (!$this->m_con)
		{
			//die('Could not connect: ' . mysql_error());
		}
		
		if (!mysql_select_db("mpaymentdb", $this->m_con))
		{
			//die('Could not find db: ' . mysql_error());
		}
	}
}
?>