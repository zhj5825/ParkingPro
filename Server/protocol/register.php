<?php
// http://54.68.153.166/register.php
error_reporting(E_ALL);

include_once 'Constants.php';
include_once '../db/DBLogicOperations.php';
require_once __DIR__.'/../thrift/lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/proto').'/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/../thrift/lib');
$loader->registerDefinition('registration_thrift', $GEN_DIR);
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use Thrift\Serializer\TBinarySerializer;

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
	    "PHONE" => "Phone="
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
            $this->$response_code = Constants::BAD_REQUEST;
            return;
        }

        $this->processRegisterRequest($data);
    }

    public function processRegisterRequest($data) {
    	$register_request_obj = TBinarySerializer::deserialize(
    	    $data, 'registration_thrift\\RegistrationRequest');

        $email = $register_request_obj->email;
        $password = $register_request_obj->password;

        if (empty($email) || strlen($email) == 0) {
            $this->$response_code = BAD_REQUEST;
            return;
        }

        list($result, $status) = DBLogicOperations::addNewUserAccount(
            $email, $password);
        if (!$result) {
       	    $this->$response_code = Constants::BAD_REQUEST;
        }

        $this->$response = header(
            "HTTP/1.0 " . strval($response_code) . " " .
        	Constants::$http_status_codes[$response_code]. " " .
  	        "content-type:application/x-thrift" . " " .
  	        "content-length:");
        return $this->response;
    }
}

$data = file_get_contents("php://input");
$registration_response = new RegistrationRequest($data);

?>