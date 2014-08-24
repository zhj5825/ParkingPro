<?php
/********************
DEMO: replace the port 8014 with your local host port
********************/
$myString = "Hello!";
echo $myString;
echo "<h5>I love using ParkingPro!</h5>";

$app_info = file_get_contents('http://localhost:8014/ParkingPro/Server/restapidemo/testdemoapi.php?action=get_app_list');
$app_info = json_decode($app_info, true);
var_dump($app_info);
;
?>
