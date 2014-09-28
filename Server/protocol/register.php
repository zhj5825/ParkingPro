<?php
// http://54.68.153.166/register.php
include_once 'Constants.php';

class RegistrationRequest {
    public static $register_request_params = array(
	    "EMAIL" => "Email=",
	    "PASSWORD" => "Password=",
	    "ROLE" => "Role=",
	    "FIRST_NAME" => "FirstName=",
	    "LAST_NAME" => "LastName=",
	    "HOME_ADDRESS" => "HomeAddress=",
	    "HOME_CITY" => "HomeCity=",
	    "HOME_STATE" => "HomeState=",
	    "HOME_COUNTRY" => "HomeCountry=",
	    "HOME_ZIPCODE" => "HomeZipcode=",
	    "CREDIT_CARD_NUMBER" => "CreditCardNum=",
	    "CREDIT_CARD_EXP_MONTH" => "CreditCardExpMonth=",
	    "CREDIT_CARD_EXP_YEAR" => "CreditCardExpYear=",
	    "CREDIT_CARD_ADDRESS" => "CreditCardAddress=",
	    "CREDIT_CARD_CITY" => "CreditCardCity=",
	    "CREDIT_CARD_STATE" => "CreditCardState=",
	    "CREDIT_CARD_COUNTRY" => "CreditCardCountry=",
	    "CREDIT_CARD_ZIPCODE" => "CreditCardZipcode=",	
	    "NAME_ON_CARD" => "NameOnCard=",
	    "SECURITY_CODE" => "SecurityCode=",
	    "PHONE" => "Phone=",
	    "TIMESTAMP" => "Timestamp"
    );

    private $response_code;
    private $response;
    private $m_lines;

    public function __construct($data) {
  	    $this->$response = array();
	    $this->$response_code = http_response_code(200);
	    $this->verifyHttpPostRequest($data);
    }

	public function verifyHttpPostRequest($data) {
	    // Check format, protocol version, method type
	    if (strlen($data) == 0) {
            $this->$response_code = Constants::SUCCESS;
            return;
        }

        $this->m_lines = explode("\r\n", $data);
        if (count($this->m_lines) == 0) {
		    $this->$response_code = Constants::BAD_REQUEST;
		    return;
        }

        if (substr($this->m_lines[0], 0, strpos($this->m_lines[0], " "))
            !== "POST") {
		    $this->$response_code = Constants::BAD_REQUEST;
			return;
		}

        $this->processRegisterRequest();
    }

    public function processRegisterRequest()
    {
        $email = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["EMAIL"]));
		$password = $this->searchQueryStringValue(
		    self::$register_request_params["PASSWORD"]);
		$role = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["ROLE"]));
		$first = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["FIRSTNAME"]));
		$last = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["LASTNAME"]));
		$home_addr = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["HOME_ADDRESS"]));
		$home_city = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["HOME_CITY"]));
		$home_state = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["HOME_STATE"]));
		$home_country = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["HOME_COUNTRY"]));
		$home_zipcode = mysql_real_escape_string($this->searchQueryStringValue(
		    self::$register_request_params["HOME_ZIPCODE"]));
		$credit_card_number = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["CREDIT_CARD_NUMBER"]));
		$name_on_card = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["NAME_ON_CARD"]));
		$security_code = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["SECURITY_CODE"]));
		$credit_card_exp_month = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["CREDIT_CARD_EXP_MONTH"]));
		$credit_card_exp_year = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["CREDIT_CARD_EXP_YEAR"]));
		$credit_card_addr = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["CREDIT_CARD_ADDRESS"]));
		$credit_card_city = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["CREDIT_CARD_CITY"]));
		$credit_card_state = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["CREDIT_CARD_STATE"]));
		$credit_card_country = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["CREDIT_CARD_COUNTRY"]));
		$credit_card_zipcode = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["CREDIT_CARD_ZIPCODE"]));
		$phone = mysql_real_escape_string(
		    $this->searchQueryStringValue(
		        self::$register_request_params["PHONE"]));
		$timestamp = mysql_real_escape_string(
		    $this->searchQueryStringValue(
			    self::$register_request_params["TIMESTAMP"]));

        if (empty($email) || strlen($email) == 0)
        {
            $this->$response_code = BAD_REQUEST;
            return;
        }

	    // TODO(zhj5825): DB operation to add the new user.
	    
        // TODO(zhj5825): Generate register response.
        $this->$response = header(
            "HTTP/1.0 " . strval($response_code) . " " .
        	Constants::$http_status_codes[$response_code]);
        exit(json_encode($response));
    }

    // Given header key, search for value
    private function searchQueryStringValue($key)
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

$data = file_get_contents("php://input");
$registration_request = new RegistrationRequest($data);

?>