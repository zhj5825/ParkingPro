<?php
$data = file_get_contents("php://input");

//$date = base64_decode($data);

$robj = new mpprequest($data);
$robj->generateResponse();

?>