<?php
include 'def_mpp.php';

class MPPResponse
{
	public $m_status;
	public $m_responseheaders;
	
	public __construct()
	{
		$this->m_responseheaders = array();
	}
	
	public __destruct()
	{
	}
	
	public addHeader($header)
	{
		array_push($m_responseheaders, $header);
	}
	
	public generate()
	{
		$data = MPPVERSION10 . $m_status . "\r\n";
		
		foreach ($this->m_responseheaders as $line)
		{
			$data = $data . $line;
		}
		
		$data = $data . "\r\n";
		//echo base64_code($data);
	}
}

?>