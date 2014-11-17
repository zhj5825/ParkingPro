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
    private $response_code;
    private $response;
    private $m_lines;
    private $db_client;

    public function __construct($data) {
  	    $this->$response = array();
	    $this->$response_code = http_response_code(200);
	    $this->verifyHttpPostRequest($data);
	    
	    try {
		    if (array_search('--http', $argv)) {
		    	$socket = new THttpClient('localhost', 8080, '../db/DBLogicOperations.php');
		    } else {
		        $socket = new TSocket('localhost', 9090);
		    }
		    $transport = new TBufferedTransport($socket, 1024, 1024);
		    $protocol = new TBinaryProtocol($transport);
		    $this->$db_client = new \registration_thrift\DBServiceClient($protocol);
		    $transport->open();
	    } catch (TException $tx) {
	    	print 'TException: '.$tx->getMessage()."\n";
	    }
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

        list($result, $status) = $this->$db_client->addNewUserAccount(
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
echo 'data = ' . $data;
$registration_response = $data; //new RegistrationRequest($data);

?>