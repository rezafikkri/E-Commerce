<?php if(!class_exists("config")) die;

/**
* 
*/
class transaksi extends config {
	
	public function insertTransaksi($transaksi_id,$qty,$produk_id) {
	    
	    $insert = $this->db->prepare("INSERT into transaksi set transaksi_id=:transaksi_id, produk_id=:produk_id, user_id=:user_id, qty=:qty, status='ordered', post=:post");
	    return $insert->execute([ ':transaksi_id' => $transaksi_id, ':produk_id' => $produk_id, ':user_id' => $_SESSION['FlowerShop']['userId'], ':qty' => $qty, ':post' => time() ]);
	}

	public function updateTransaksi($qty,$produk_id) {
	    
	    $update = $this->db->prepare("UPDATE transaksi set qty=:qty, post=:post where produk_id=:produk_id and user_id=:user_id and status='ordered'");
	    return $update->execute([ ':qty' => $qty, ':post' => time(), ':produk_id' => $produk_id, ':user_id' => $_SESSION['FlowerShop']['userId'] ]);
	}

	private function updateAllStatusTransaksi() {
	    
	    $update = $this->db->prepare("UPDATE transaksi set status=:status where user_id=:user_id and status='ordered'");
	    return $update->execute([ ':status'=>'paid', ':user_id'=>$_SESSION['FlowerShop']['userId'] ]);
	}

	private function insertPemesananBarang($produk_id,$qty,$user_id,$country,$city,$subdistrict,$village,$zip_code,$status,$post) {

		$transaksi_detail_id = config::generate_uuid();

	    $update = $this->db->prepare("INSERT pemesanan_barang set pemesanan_id=:pemesanan_id, produk_id=:produk_id, qty=:qty, user_id=:user_id, country=:country, city=:city, subdistrict=:subdistrict, village=:village, zip_code=:zip_code, status=:status, post=:post");

	    return $update->execute([ ':pemesanan_id'=>$transaksi_detail_id, ':produk_id'=>$produk_id, ':qty'=>$qty, ':user_id'=>$user_id, ':country'=>$country, ':city'=>$city, ':subdistrict'=>$subdistrict, ':village'=>$village, ':zip_code'=>$zip_code, ':status'=>$status, ':post'=>$post ]);
	}

	public function checkout($produk) {
		$cekLogin = config::cekLoginNo_methodUser();
		if($cekLogin) {
			return "nologin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('country', 'Negara', 'required|maxLength[50]', true);
		$this->form_validation('city', 'Kota', 'required|maxLength[50]', true);
		$this->form_validation('subdistrict', 'Kecamatan', 'required|maxLength[50]', true);
		$this->form_validation('village', 'Kelurahan/Desa', 'required|maxLength[50]', true);
		$this->form_validation('zip_code', 'Kode Pos', 'required|maxLength[10]|integer', true);
		$this->set_delimiter('<p class="pesan warning">', "</p>");

		// proses error form validation
		if($this->has_formErrors()) {
			return false;
		}

		$user_id = $_SESSION['FlowerShop']['userId'];
		$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
		$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
		$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
		$subdistrict = filter_input(INPUT_POST, 'subdistrict', FILTER_SANITIZE_STRING);
		$village = filter_input(INPUT_POST, 'village', FILTER_SANITIZE_STRING);
		$zip_code = filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_STRING);

		$status = 'ready';
		$post = time();

		$this->updateAllStatusTransaksi();
		foreach($_SESSION['FlowerShop']['cart'] as $r){

			$this->insertPemesananBarang($r['produk_id'],$r['qty'],$user_id,$country,$city,$subdistrict,$village,$zip_code,$status,$post);
			$sale = (int)$produk->getOldSale($r['produk_id'])+$r['qty'];
			$produk->updateSale($sale, $r['produk_id']);
		}
		// hapus session cart
		unset($_SESSION['FlowerShop']['cart']);
		return true;
	}

	public function pesan_checkout() {
		if(isset($_SESSION['FlowerShop']['pesanCheckout']) && $_SESSION['FlowerShop']['pesanCheckout'] == "gagalCheckout") {
			unset($_SESSION['FlowerShop']['pesanCheckout']);
			return '<p class="pesan warning alignCenter">Gagal checkout!</p>';
		}
	}

	private function QueryStatusQuerySearchANDExecute($postAwal, $postAkhir, $status, $searchInput){

		$execute = [':postAwal'=>$postAwal, ':postAkhir'=>$postAkhir ];

	    if($status != "all") { 
	    	$QueryStatus = "and pb.status=:status";
	    	$execute = array_merge($execute, [':status'=>$status]);
	    }
	    if($searchInput != null) {
	    	$QuerySearch = "and (u.username LIKE :username OR u.full_name LIKE :full_name)";
	    	$execute = array_merge($execute, [':username'=>$searchInput."%", ':full_name'=>$searchInput]);
	    }

	    return ['execute'=>$execute, 'QueryStatus'=>$QueryStatus??null, 'QuerySearch'=>$QuerySearch??null];
	}

	public function tampil_pemesanan_barang($postAwal, $postAkhir, $status, $searchInput=null, $offset=0, $limit, $orderByParam=null) {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return false;
		}

		$whiteList = ['DESC','ASC'];
		if(in_array(strtoupper($orderByParam), $whiteList)) {
			$orderByParam = $orderByParam;
		} else {
			$orderByParam = "DESC";
		}

		$data = $this->QueryStatusQuerySearchANDExecute($postAwal, $postAkhir, $status, $searchInput);
		$QueryStatus = $data['QueryStatus']??'';
		$QuerySearch = $data['QuerySearch']??'';

		$execute = array_merge($data['execute'], [':offset'=>$offset, ':batas'=>$limit]);
	    $get = $this->db->prepare("SELECT u.full_name, u.username, pb.pemesanan_id, pb.status, pb.post, pb.qty, p.harga, p.nama_produk
			FROM pemesanan_barang as pb
			JOIN user as u USING(user_id)
			JOIN produk as p USING(produk_id)
			WHERE pb.post BETWEEN :postAwal AND :postAkhir $QueryStatus $QuerySearch ORDER BY pb.post $orderByParam LIMIT :offset, :batas");
	    $get->execute($execute);
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    	$arrPost[] = $r['post'];
	    }

	    return ['hasil'=>$hasil??null, 'arrPost'=>$arrPost??null];
	}

	public function tampil_alamat_pengiriman($postAwal, $postAkhir, $status, $searchInput=null, $limit) {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return false;
		}

		$data = $this->QueryStatusQuerySearchANDExecute($postAwal, $postAkhir, $status, $searchInput);
		$QueryStatus = $data['QueryStatus'];
		$QuerySearch = $data['QuerySearch'];
		$execute = array_merge($data['execute'], [':batas'=>$limit]);

	    $get = $this->db->prepare("SELECT u.full_name, u.whatsapp, pb.city, pb.subdistrict, pb.village, pb.zip_code, pb.status, pb.qty, pb.pemesanan_id, p.nama_produk
	    	from pemesanan_barang as pb
	    	JOIN user as u ON u.user_id=pb.user_id
	    	JOIN produk as p ON p.produk_id=pb.produk_id
	    	where pb.post BETWEEN :postAwal AND :postAkhir $QueryStatus $QuerySearch 
	    	ORDER BY pb.post DESC LIMIT :batas");
	   	$get->execute($execute);
	   	$cekStatusNotReady = '';
	    while ($r=$get->fetch(PDO::FETCH_ASSOC)) {
	    	$hasil[]=$r;
	    	if($r['status'] != 'ready') $cekStatusNotReady = 'yes';
	    }
	    return ['hasil'=>@$hasil, 'cekStatusNotReady'=>$cekStatusNotReady];
	}

	public function update_status_pemesanan($pemesanan_id, $statusSet, $statusWhere) {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return !$cekLogin;//false
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$dataIn = config::change_idForQuery_IN($pemesanan_id);
		$execute = array_merge([$statusSet, $statusWhere], $dataIn['id']);
		$questionmarks = $dataIn['questionmarks'];

	    $up = $this->db->prepare("UPDATE pemesanan_barang set status=?
	    	where status=? and pemesanan_id in($questionmarks)");
	    $up->execute($execute);
	    return $up->rowCount();
	}

	public function getFirstYearTransaksi() {
	    
	    if(!isset($_SESSION['FlowerShop']['firstYearTransaksi'])) {

	    	$get = $this->db->prepare("SELECT post from pemesanan_barang order by post ASC");
	    	$get->execute();
	    	$post = $get->fetch(PDO::FETCH_ASSOC)['post'];

	    	$_SESSION['FlowerShop']['firstYearTransaksi'] = date("Y", $post);
	    	return $_SESSION['FlowerShop']['firstYearTransaksi'];
	    } else {
	    	return $_SESSION['FlowerShop']['firstYearTransaksi'];
	    }
	}

	public function bulan($bulan) {
	    
	    switch ($bulan) {
	    	case 1:
	    		$bulan = "Januari";
	    		break;
	    	case 2:
	    		$bulan = "Februari";
	    		break;
	    	case 3:
	    		$bulan = "Maret";
	    		break;
	    	case 4:
	    		$bulan = "April";
	    		break;
	    	case 5:
	    		$bulan = "Mei";
	    		break;
	    	case 6:
	    		$bulan = "Juni";
	    		break;
	    	case 7:
	    		$bulan = "Juli";
	    		break;
	    	case 8:
	    		$bulan = "Agustus";
	    		break;
	    	case 9:
	    		$bulan = "September";
	    		break;
	    	case 10:
	    		$bulan = "Oktober";
	    		break;
	    	case 11:
	    		$bulan = "November";
	    		break;
	    	case 12:
	    		$bulan = "Desember";
	    		break;
	    }

	    return $bulan;
	}
}