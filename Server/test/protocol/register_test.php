<?php
// Issue a user registration request and test the user is successfully added
// to the table.
include_once '../../../protocol/register.php';
include_once '../../../protocol/Constants.php';
include_once '../../../db/DBLogicOperations.php';
require_once __DIR__.'/../../thrift/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/../../protocol/proto').'/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__.'/../../thrift/lib');
$loader->registerDefinition('registration_thrift', $GEN_DIR);
$loader->register();

use Thrift\Transport\THttpClient;
use Thrift\Serializer\TBinarySerializer;

$register_request = new \registration_thrift\RegistrationRequest();
$register_request->email = "bbbee@gmail.com";
$register_request->password = "dd3456cc";

$register_request_serialized = TBinarySerializer::serialize($register_request);

$ch = curl_init('http://localhost/Server/protocol/register.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $register_request_serialized);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/thrift',
'Content-Length: ' . strlen($register_request_serialized))
);

$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($httpcode == 200) {
	echo 'New user is successfully added to DB.';
} else {
	echo 'Operation failed: ' . $httpcode;
}

?>
