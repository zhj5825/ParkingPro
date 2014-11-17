<?php
header('Content-Type', 'application/x-thrift');
if (php_sapi_name() == 'cli') {
    echo "\r\n";
}

$handler = new DBServiceServerHandler();
$processor = new \tutorial\CalculatorProcessor($handler);
$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
$protocol = new TBinaryProtocol($transport, true, true);
$transport->open();
$processor->process($protocol, $protocol);
$transport->close();