<?php if(!class_exists("config")) die;

/**
* Keterangan :
	set transaksi_id di session : agar pada saat mendelete keranjang, mengambil qty barang yang dipesan tidak perlu select ke database *(generateJml_stockForDeleteKeranjang())*
*/
class cart extends config {

	private function cekOrderWhereStock($produk_id,$qty) {

	    if($qty > 0) {
	    	$get = $this->db->prepare("SELECT jml_stock from produk where produk_id=:produk_id");
		    $get->execute([ ':produk_id' => $produk_id ]);
		    $stock = $get->fetch(PDO::FETCH_ASSOC)['jml_stock'];
		    if($qty > $stock) {
		    	return false;
		    } else {
		    	return $stock;
		    }
	    } else {
	    	return false;
	    }
	}

	private function cekHasProdukInArraySessionCart($qtyInput, $produk_idInput) {

		if(isset($_SESSION['FlowerShop']['cart'])) {
			for($i=0; $i < count($_SESSION['FlowerShop']['cart']); $i++) {

				$data = $_SESSION['FlowerShop']['cart'][$i];
				if($data['produk_id'] == $produk_idInput) {
					$qtyNew = (int)$data['qty']+$qtyInput;
					$produk_id = $data['produk_id'];
					return ['index_arr'=>$i, 'qtyNew'=>$qtyNew];
				}
			}
		} else {
			return false;
		}
	}
	
	public function plusKeranjang($transaksi,$produk) {
		$cekLogin = config::cekLoginNo_methodUser();
		if($cekLogin) {
			return "nologin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		} 

		$qtyInput = (int)filter_input(INPUT_POST, 'qty', FILTER_SANITIZE_STRING);
		$produk_idInput = filter_input(INPUT_POST, 'produk_id', FILTER_SANITIZE_STRING);

		// jika session sudah ada dan produk sudah ada dalam array
		$dataArr = $this->cekHasProdukInArraySessionCart($qtyInput, $produk_idInput);
		if($dataArr) {
			// jika jumlah order tidak melebihi jumlah stock
			$stock = $this->cekOrderWhereStock($produk_idInput,$qtyInput);
			if($stock) {

				$sisaStock = $stock - $qtyInput;
				// jika transaksi dan jml stock berhasil di update
				if($transaksi->updateTransaksi($dataArr['qtyNew'],$produk_idInput) && $produk->updateJmlStock($sisaStock, $produk_idInput)) {

					$jmlProduk = $this->hitungJmlProduk($qtyInput);
					$_SESSION['FlowerShop']['cart'][$dataArr['index_arr']]['qty'] = $dataArr['qtyNew'];
					return json_encode(['success'=>'yes','sisaStock'=>$sisaStock ,'jmlProduk'=>$jmlProduk]);

				} else {
					return false;
				}

			} else {
				return json_encode(['jmlorderLebihDariStock' => "yes"]);
			}

		} else {
			// jika jumlah order tidak melebihi jumlah stock
			$stock = $this->cekOrderWhereStock($produk_idInput,$qtyInput);
			if($stock) {

				$sisaStock = $stock - $qtyInput;
				// jika insert data transaksi dan update jml stock produk berhasil
				$transaksi_id = config::generate_uuid();
				if($transaksi->insertTransaksi($transaksi_id,$qtyInput,$produk_idInput) && $produk->updateJmlStock($sisaStock, $produk_idInput)) {

					$jmlProduk = $this->hitungJmlProduk($qtyInput);
					$_SESSION['FlowerShop']['cart'][] = ['qty'=>$qtyInput, 'produk_id'=>$produk_idInput, 'transaksi_id'=>$transaksi_id];
					return json_encode(['success'=>'yes','sisaStock'=>$sisaStock ,'jmlProduk'=>$jmlProduk]);

				} else {
					return false;
				}

			} else {
				return json_encode(['jmlorderLebihDariStock' => "yes"]);
			}
		}
	}

	private function hitungJmlProduk($qtyNew = 0){
		$jml = $qtyNew;
		if(isset($_SESSION['FlowerShop']['cart'])) {
			foreach($_SESSION['FlowerShop']['cart'] as $r) {
				$jml += $r['qty'];
			}
		}
		return $jml;
	}

	public function setSessionCart_If_HasTransaksiOnDatabase_and_notThereSessionCart() {

	    if(isset($_SESSION['FlowerShop']['userLogin']) == 'yes' && !isset($_SESSION['FlowerShop']['cart'])) {

	    	$data = $this->db->prepare("SELECT transaksi_id ,produk_id, qty FROM transaksi where user_id=:user_id and status='ordered'");
	    	$data->execute([ ':user_id'=>$_SESSION['FlowerShop']['userId'] ]);
	    	$jmlData = 0;
	    	while ($r=$data->fetch(PDO::FETCH_ASSOC)) {
	    		$_SESSION['FlowerShop']['cart'][] = ['qty'=>$r['qty'], 'produk_id'=>$r['produk_id'], 'transaksi_id'=>$r['transaksi_id']];
	    		$jmlData += $r['qty'];
	    	}

	    	($jmlData == 0)? $class = "" : $class = "muncul";
	    	return [ 'jmlProduk' => $jmlData, 'class' => $class ];

	    } else if(isset($_SESSION['FlowerShop']['userLogin']) == 'yes') {

	    	(count($_SESSION['FlowerShop']['cart']) == 0)? $class = "" : $class = "muncul";
	    	return [ 'jmlProduk' => $this->hitungJmlProduk(), 'class' => $class ];
	    }
	}

	public function tampilKeranjang() {
	    
	    $get = $this->db->prepare("SELECT p.produk_id, p.nama_produk, p.harga, t.transaksi_id, t.qty
	    	from transaksi as t
	    	JOIN produk as p USING(produk_id)
	    	where t.user_id=:user_id and status='ordered' order by post desc");
	    $get->execute([ ':user_id' => $_SESSION['FlowerShop']['userId']??'' ]);
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    }
	    return @$hasil;
	}

	private function generateJml_stockForDeleteKeranjang($transaksi_id,$produk_id) {

		$get = $this->db->prepare("SELECT jml_stock from produk where produk_id=:produk_id");
		$get->execute([ ':produk_id' => $produk_id ]);
		$jml_stock = $get->fetch(PDO::FETCH_ASSOC)['jml_stock'];

	    foreach ($_SESSION['FlowerShop']['cart'] as $key=>$val) {
	    	if($val['transaksi_id'] == $transaksi_id) {

	    		$qty = $val['qty'];
	    		unset($_SESSION['FlowerShop']['cart'][$key]);
	    		break;
	    	}
	    }

	    return $qty+$jml_stock;
	}

	public function deleteKeranjang($produk) {
		$cekLogin = config::cekLoginNo_methodUser();
		if($cekLogin) {
			return "nologin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		} 

		$transaksi_id = filter_input(INPUT_GET, 'transaksi_id', FILTER_SANITIZE_STRING);
		$produk_id = filter_input(INPUT_GET, 'produk_id', FILTER_SANITIZE_STRING);

	    $del = $this->db->prepare("DELETE from transaksi where transaksi_id=:transaksi_id and user_id=:user_id");
	    $del->execute([ ':transaksi_id'=>$transaksi_id ,':user_id'=>$_SESSION['FlowerShop']['userId'] ]);
	    if($del->rowCount() > 0) {

	    	$jml_stock = $this->generateJml_stockForDeleteKeranjang($transaksi_id,$produk_id);
	    	$produk->updateJmlStock($jml_stock, $produk_id);
	    	return true;

	    } else {
	    	return false;
	    }
	}
}