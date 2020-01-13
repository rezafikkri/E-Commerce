<?php

include '../../init.php';
$user = new user;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == 'addUser') {
	echo $user->add_user();

} else if($action == 'editUser') {
	$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);
	$edit = $user->editUser();
	if($edit === "nologin") {
		header("Location: ".config::base_url('index.php?ref=produk'));
		die;
	} elseif($edit == true) {
		header("Location: ".config::base_url('_admin/index.php?ref=user'));
		die;
	} else {
		$_SESSION['FlowerShop']['pesanEditAccount'] = "gagalUpdate";
		header("Location: ".config::base_url('_admin/index.php?ref=editUser&user_id='.$user_id));
		die;
	}

} else if($action == 'deleteUser') {
	echo $user->deleteUser("admin");
}