<?php  

include '../init.php';
$produk = new produk;

$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_STRING);

if($kategori_id == "all") {
	$produk = $produk->tampilProduk(null,null,null,$_SESSION['FlowerShop']['LIMIT_DEFAULT'],$offset);
} else {
	$produk = $produk->tampilProduk($kategori_id,null,null,$_SESSION['FlowerShop']['LIMIT_DEFAULT'],$offset);
}

if($produk) {
	$i = 0;
	foreach($produk as $r) {
		$hasil[$i] = $r;
		$hasil[$i]['harga'] = config::generate_hargaFormat($r['harga']);
		$i++;
	}

	echo json_encode($hasil);
}

?>