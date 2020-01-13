<?php

include '../../init.php';
$db = new produk;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == 'insertProduk') {
	echo $db->insertProduk();

} else if($action == 'tampilProduk') {

	$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
	$stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_STRING);
	$searchInput = filter_input(INPUT_POST, 'searchInput', FILTER_SANITIZE_STRING);
	$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_STRING);

	if($kategori_id == "all") $kategori_id = null;
	if($stock == "all")  $stock = null;

	echo json_encode($db->tampilProduk($kategori_id,$stock,$searchInput,$_SESSION['FlowerShop']['LIMIT_DEFAULT'],$offset));

} else if($action == 'deleteProduk') {
	echo $db->deleteProduk();

} else if($action == 'editProduk') {
	$edit = $db->editProduk();
	if($edit === "invalidlogin") {
		header("Location: ".config::base_url('index.php?ref=produk'));
		die;
	} else if($edit == true) {
		header("Location: ".config::base_url('_admin/index.php?ref=produkAdmin'));
		die;
	} else {
		header("Location: ".config::base_url('_admin/index.php?ref=editProduk&produk_id='.filter_input(INPUT_POST, 'produk_id', FILTER_SANITIZE_STRING)));
		die;
	}
}