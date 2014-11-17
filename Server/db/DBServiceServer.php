<?php
require_once __DIR__.'/../thrift/lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'../protocol/proto').'/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/../thrift/lib');
$loader->registerDefinition('registration_thrift', $GEN_DIR);
$loader->register();

if (php_sapi_name() == 'cli') {
   ini_set("display_errors", "stderr");
}

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

class DBServiceServerHandler implements \registration_thrift\DBServiceIf {
	public function addNewUserAccount(\registration_thrift\RegistrationRequest $request) {
		DBLogicOperations::addNewUserAccount($request->email, $request->password);
	}
}

header('Content-Type', 'application/x-thrift');
if (php_sapi_name() == 'cli') {
    echo "\r\n";
}

$handler = new DBServiceServerHandler();
$processor = new \registration_thrift\DBServiceProcessor($handler);

$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
$protocol = new TBinaryProtocol($transport, true, true);

$transport->open();
$processor->process($protocol, $protocol);
$transport->close();
