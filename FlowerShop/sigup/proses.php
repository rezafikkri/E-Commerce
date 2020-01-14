<?php

include '../init.php';

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$db = new user;
$log = new login;

if($action == "sigup") {
	echo $db->add_user();
	die;

} else if($action == 'login') {
	echo $log->prosesLogin();
	die;

} else if($action == 'deleteAccount') {

	$passwordAsli = $db->getPasswordAsli();
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$cekPass = password_verify($password,$passwordAsli['password']);

	if($passwordAsli === "nologin") {
		echo null;
		die;

	} elseif($cekPass) {
		if($db->deleteUser("user") == "success") {
			echo json_encode([ 'success'=>config::base_url()."sigup/logout.php" ]);
			die;
		} else {
			echo null;
			die;
		}
	} elseif(!$cekPass) {
		echo json_encode([ 'error'=>"passwordSalah" ]);
		die;
	} else {
		echo null;
	}

} else if($action == 'editAccount') {

	$edit = $db->editUser();
	if($edit === "nologin") {
		header("Location: ../index.php?ref=login");
		die;
	} else if($edit == true) {
		$_SESSION['FlowerShop']['pesanEditAccount'] = 'userUpdated';
	} else {
		$_SESSION['FlowerShop']['pesanEditAccount'] = 'gagalUpdate';
	}
	header("Location: ../index.php?ref=editAccount");
	die;
}