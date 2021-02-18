<?php

/*** Note :
$statusnew untuk value status update
$status untuk query where
*/

include '../../init.php';
$db = new transaksi;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "update_status_pemesanan_to_sent") {

	$pemesanan_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$statusWhere = 'ready';
	$statusSet = 'sent';
	if($db->update_status_pemesanan($pemesanan_id, $statusSet, $statusWhere) > 0) {
		echo "success";
	} else {
		echo "gagal";
	}

} elseif($action == "update_status_pemesanan_to_accepted") {
	$pemesanan_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$statusWhere = 'sent';
	$statusSet = 'accepted';
	if($db->update_status_pemesanan($pemesanan_id, $statusSet, $statusWhere) > 0) {
		echo "success";
	} else {
		echo "gagal";
	}
}

