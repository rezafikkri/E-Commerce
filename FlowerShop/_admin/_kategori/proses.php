<?php

include '../../init.php';
$db = new kategori;
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($action == "insertKategori") {
	echo $insert = $db->insertkategori();

} else if($action == "deleteKategori") {
	echo $db->deleteKategori();

} else if($action == "editKategori") {

	$edit = $db->editKategori();
	if($edit === "invalidlogin") {
		header("Location: ".config::base_url('index.php?ref=produk'));
		die;
	} elseif($edit == true) {
		header("Location: ".config::base_url('_admin/index.php?ref=kategori'));
		die;
	} else {
		$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
		$_SESSION['FlowerShop']['editKategori'] = "gagalEdit";
		header("Location: ".config::base_url('_admin/index.php?ref=editKategori&kategori_id='.$kategori_id));
		die;
	}
}