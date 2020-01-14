<?php

include '../init.php';
$db = new cart;
$produk = new produk;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "deleteCart") {

	$delete = $db->deleteKeranjang($produk);
	if($delete === "nologin") {
		header("Location: ".config::base_url('index.php?ref=login'));
		die;
	} else {
		header("Location: ".config::base_url('index.php?ref=cart'));
		die;
	}

} else if($action == "addCart") {
	$transaksi = new transaksi;
	echo $db->plusKeranjang($transaksi,$produk);

} else if($action == "checkout") {
	if(isset($_SESSION['FlowerShop']['cart'])) {
		$transaksi = new transaksi;
		$checkout = $transaksi->checkout($produk);
		if($checkout === "nologin") {
			header("Location: ".config::base_url('index.php'));
		} elseif($checkout === true) {
			$_SESSION['FlowerShop']['callback_pay'] = true;
			header("Location: ".config::base_url('index.php?ref=callback_pay'));
		} else {
			$_SESSION['FlowerShop']['pesanCheckout'] = "gagalCheckout";
			header("Location: ".config::base_url('index.php?ref=checkout'));
		}
	// jika tidak ada belanjaan di keranjang
	} else {
		header("Location: ".config::base_url('index.php'));
	}
}