<?php
// Issue a user registration request and test the user is successfully added
// to the table.
include_once './../../protocol/register.php';
include_once './../../protocol/Constants.php';
include_once './../../db/DBLogicOperations.php';
require_once __DIR__.'/../../thrift/lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/../../protocol/proto').'/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__.'/../../thrift/lib');
$loader->registerDefinition('registration_thrift', $GEN_DIR);
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use Thrift\Serializer\TBinarySerializer;

$register_request = new \registration_thrift\RegistrationRequest();
$register_request->email = "aaa@gmail.com";
$register_request->password = "123456";

$register_request_serialized = TBinarySerializer::serialize($register_request);
echo (new RegistrationRequest($register_request_serialized));
?>