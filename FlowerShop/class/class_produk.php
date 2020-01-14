<?php if(!class_exists("config")) die;

/**
* 
*/
class produk extends config {
	
	public function insertProduk() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('nama_produk', 'Nama produk', 'required|maxLength[32]', false);
		$this->form_validation('harga', 'Harga', 'required|maxLength[11]|integer', false);
		$this->form_validation('jml_stock', 'Jumlah Persediaan', 'required|maxLength[11]|integer', false);
		$this->form_validation('urlImg', 'Url Img', 'required|maxLength[100]', false);
		$this->form_validation('kategori_id', 'Kategori', 'required', false);

		// proses form error
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}
		
		$nama_produk = filter_input(INPUT_POST, 'nama_produk', FILTER_SANITIZE_STRING);
		$harga = filter_input(INPUT_POST, 'harga', FILTER_SANITIZE_STRING);
		$jml_stock = filter_input(INPUT_POST, 'jml_stock', FILTER_SANITIZE_STRING);
		$urlImg = filter_input(INPUT_POST, 'urlImg', FILTER_SANITIZE_STRING);

		$urlImg1 = filter_input(INPUT_POST, 'urlImg1', FILTER_SANITIZE_STRING);
		$urlImg2 = filter_input(INPUT_POST, 'urlImg2', FILTER_SANITIZE_STRING);
		$urlImg3 = filter_input(INPUT_POST, 'urlImg3', FILTER_SANITIZE_STRING);
		$info_produk = filter_input(INPUT_POST, 'info_produk', FILTER_SANITIZE_STRING);
		$ulasan_produk = filter_input(INPUT_POST, 'ulasan_produk', FILTER_SANITIZE_STRING);
		$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
		$produk_id = config::generate_uuid();
		$user_id = $_SESSION['FlowerShop']['userId'];

		$insert = $this->db->prepare("INSERT into produk set produk_id=:produk_id, kategori_id=:kategori_id, user_id=:user_id, nama_produk=:nama_produk, harga=:harga, infoProduk=:infoProduk, ulasanProduk=:ulasanProduk, jml_stock=:jml_stock, url_img=:url_img, url_img1=:url_img1, url_img2=:url_img2, url_img3=:url_img3");
		if($insert->execute([ ':produk_id'=>$produk_id, ':kategori_id'=>$kategori_id, ':user_id'=>$user_id, ':nama_produk' => $nama_produk, ':harga' => $harga, ':infoProduk'=>$info_produk, ':ulasanProduk' => $ulasan_produk, ':jml_stock' => $jml_stock, ':url_img'=>$urlImg,':url_img1'=>$urlImg1,':url_img2'=>$urlImg2,':url_img3'=>$urlImg3 ])) {

			return json_encode(["success"=>"yes"]);
		}
		return json_encode(["gagal"=>"yes"]);
	}

	public function editProduk() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('nama_produk', 'Nama produk', 'required|maxLength[32]', false);
		$this->form_validation('harga', 'Harga', 'required|maxLength[11]|integer', false);
		$this->form_validation('jml_stock', 'Jumlah Persediaan', 'required|maxLength[11]|integer', false);
		$this->form_validation('urlImg', 'Url Img', 'required|maxLength[100]', false);
		$this->form_validation('kategori_id', 'Kategori', 'required', false);
		$this->set_delimiter('<p class="pesan warning">','</p>');

		//proses form error
		if($this->has_formErrors()) {
			return false;
		}

		$produk_id = filter_input(INPUT_POST, 'produk_id', FILTER_SANITIZE_STRING);
		$nama_produk = filter_input(INPUT_POST, 'nama_produk', FILTER_SANITIZE_STRING);
		$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
		$harga = filter_input(INPUT_POST, 'harga', FILTER_SANITIZE_STRING);

		$infoProduk = filter_input(INPUT_POST, 'infoProduk', FILTER_SANITIZE_STRING);
		$ulasanProduk = filter_input(INPUT_POST, 'ulasanProduk', FILTER_SANITIZE_STRING);
		$jml_stock = filter_input(INPUT_POST, 'jml_stock', FILTER_SANITIZE_STRING);
		$urlImg = filter_input(INPUT_POST, 'urlImg', FILTER_SANITIZE_STRING);
		$urlImg1 = filter_input(INPUT_POST, 'urlImg1', FILTER_SANITIZE_STRING);
		$urlImg2 = filter_input(INPUT_POST, 'urlImg2', FILTER_SANITIZE_STRING);
		$urlImg3 = filter_input(INPUT_POST, 'urlImg3', FILTER_SANITIZE_STRING);

		$insert = $this->db->prepare("UPDATE produk set kategori_id=:kategori_id, nama_produk=:nama_produk, harga=:harga, infoProduk=:infoProduk, ulasanProduk=:ulasanProduk, jml_stock=:jml_stock, url_img=:url_img, url_img1=:url_img1, url_img2=:url_img2, url_img3=:url_img3 where produk_id=:produk_id");
		$insert->execute([ ':kategori_id' => $kategori_id, ':nama_produk' => $nama_produk, ':harga' => $harga, ':infoProduk'=>$infoProduk, ':ulasanProduk' => $ulasanProduk, ':jml_stock' => $jml_stock, ':url_img'=>$urlImg,':url_img1'=>$urlImg1,':url_img2'=>$urlImg2,':url_img3'=>$urlImg3, ':produk_id' => $produk_id ]);
		if($insert->rowCount() > 0){
			return true;
		}
		return false;
	}

	private function queryWhereKategori_idStock_and_searchInput($kategori_id,$stock,$searchInput) {
		/* menentukan where dan and query */
		$execute = [];
		$whereKategori_id = "";
		$whereStock = "";
		$whereSearchInput = "";
		$andStock = "";
		$andSearchInput = "";
		if($kategori_id != null) $whereKategori_id = "WHERE";
		elseif($stock != null) $whereStock = "WHERE";
		elseif($searchInput != null) $whereSearchInput = "WHERE";
		if($kategori_id != null && $stock != null) $andStock = "and";
		elseif(($kategori_id !=null || $stock != null) && $searchInput != null) $andSearchInput = "and";
		/* menentukan where dan and query */

		if($kategori_id != null) {
			$queryKategori_id = $whereKategori_id." kategori_id=:kategori_id";
			$execute = array_merge($execute, [':kategori_id'=>$kategori_id]);
		}
		if($stock != null) {
			if($stock == "ada") {
				$queryStock = $whereStock.$andStock." jml_stock>:stock";
				$execute = array_merge($execute, [':stock'=>0]);
			} elseif($stock == "habis") {
				$queryStock = $whereStock.$andStock." jml_stock=:stock";
				$execute = array_merge($execute, [':stock'=>0]);
			}
		}
		if($searchInput != null) {
			$querySearchInput = $whereSearchInput.$andSearchInput." nama_produk LIKE :nama_produk";
			$execute = array_merge($execute, [':nama_produk'=>$searchInput."%"]);
		}
		
		return ['execute'=>$execute,'queryKategori_id'=>$queryKategori_id??null, 'queryStock'=>$queryStock??null, 'querySearchInput'=>$querySearchInput??null];
	}

	public function tampilProduk($kategori_id=null,$stock=null,$searchInput=null,$limit,$offset=0,$orderBy=null) {

		$data = $this->queryWhereKategori_idStock_and_searchInput($kategori_id,$stock,$searchInput);
		$queryKategori_id = $data['queryKategori_id'];
		$queryStock = $data['queryStock'];
		$querySearchInput = $data['querySearchInput'];
		$execute = array_merge($data['execute']??[], [':offset'=>$offset, ':batas'=>$limit]);

		if(strtolower($orderBy) == "orderby") {
			$tampil = $this->db->prepare("SELECT nama_produk, harga, jml_stock, sale, produk_id, url_img from produk
			$queryKategori_id $queryStock $querySearchInput ORDER BY sale DESC LIMIT :offset,:batas");
		} else {
			$tampil = $this->db->prepare("SELECT nama_produk, harga, jml_stock, sale, produk_id, url_img from produk
			$queryKategori_id $queryStock $querySearchInput LIMIT :offset,:batas");
		}
		$tampil->execute($execute);

		$i = 0;
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[$i]=$r;
			$hasil[$i]['harga']=config::generate_hargaFormat($r['harga']);
			$i++;
		}
		return @$hasil;
	}

	public function deleteProduk() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$produk_id = filter_input(INPUT_POST, 'produk_id', FILTER_SANITIZE_STRING);
		try{
			$delProduk = $this->db->prepare("DELETE from produk where produk_id=:produk_id");
			$delProduk->execute([ ':produk_id' => $produk_id ]);
			if($delProduk->rowCount()>0){
				return "success";
			}
			return false;
		} catch(PDOException$e) {
			return false;
		}
	}

	public function getOneProduk() {

		$produk_id = filter_input(INPUT_GET, 'produk_id', FILTER_SANITIZE_STRING);
		$get = $this->db->prepare("SELECT nama_produk, produk_id, kategori_id, harga, infoProduk, ulasanProduk, jml_stock, sale, url_img, url_img1, url_img2, url_img3
			from produk where produk_id=:produk_id");
		$get->execute([ ':produk_id' => $produk_id ]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function tampilDetailProduk() {

		$produk_id = filter_input(INPUT_GET, 'produk_id', FILTER_SANITIZE_STRING);
		$get = $this->db->prepare("SELECT p.produk_id, p.jml_stock, p.infoProduk, p.ulasanProduk, p.nama_produk, p.harga, u.whatsapp, u.email, p.url_img, p.url_img1, p.url_img2, p.url_img3 
			FROM produk as p
			JOIN user as u USING(user_id)
			WHERE p.produk_id=:produk_id");
		$get->execute([':produk_id'=>$produk_id]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function updateJmlStock($jml_stock, $produk_id) {
	    
	    $up = $this->db->prepare("UPDATE produk set jml_stock=:jml_stock where produk_id=:produk_id");
	    return $up->execute([ ':jml_stock' => $jml_stock, ':produk_id' => $produk_id ]);
	}

	public function updateSale($sale, $produk_id) {
	    
	    $update = $this->db->prepare("UPDATE produk set sale=:sale where produk_id=:produk_id");
	    return $update->execute([ ':sale' => $sale, ':produk_id' => $produk_id ]);
	}

	public function getOldSale($produk_id) {
	    
	    $get = $this->db->prepare("SELECT sale from produk where produk_id=:produk_id");
	    $get->execute([ ':produk_id' => $produk_id ]);
	    return $get->fetch(PDO::FETCH_ASSOC)['sale'];
	}
}