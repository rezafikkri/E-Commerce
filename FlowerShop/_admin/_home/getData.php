<?php

include '../../init.php';
$db = new home;

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

$serverTime = time();
$msg = json_encode([
	'totalUsers'=>$db->totalUsers(),
	'totalOrder'=>$db->totalOrder(),
	'totalPayment'=>$db->totalPayment()
]);

$db->sendMsg($serverTime,$msg);