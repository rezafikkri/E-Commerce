<?php if(!class_exists("config")) die;

/**
* 
*/
class kategori extends config {
	
	public function insertkategori() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('nama_kategori', 'Nama Kategori', 'required|maxLength[43]|unique[kategori_produk.nama_kategori]', false);
		$this->set_delimiter('<p class="warning pesan">','</p>');
		// proses error form validation
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$nama_kategori = filter_input(INPUT_POST, 'nama_kategori', FILTER_SANITIZE_STRING);
		$kategori_id = config::generate_uuid();
		$insert = $this->db->prepare("INSERT into kategori_produk set kategori_id=:kategori_id, nama_kategori=:nama_kategori");
		if($insert->execute([ ':kategori_id' => $kategori_id, ':nama_kategori' => $nama_kategori ])) {
			return json_encode(["success"=>"yes"]);
		} else {
			return false;
		}
	}

	public function tampilkategori() {

		$tampil = $this->db->prepare("SELECT * from kategori_produk order by nama_kategori asc");
		$tampil->execute();
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[] = $r;
		}
		return @$hasil;
	}

	public function deleteKategori() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
		$dataIN = config::change_idForQuery_IN($kategori_id);
		$kategori_id = $dataIN['id'];
		$questionmarks = $dataIN['questionmarks'];

		try {
			$del = $this->db->prepare("DELETE from kategori_produk where kategori_id in($questionmarks)");
			$del->execute($kategori_id);
			$cek = $del->rowCount();
			if($cek >= 1) {
				return "success";
			} else {
				return "dataNull";
			}
		}catch(PDOException$e) {
			return false;
		}
	}

	public function getOneKategori() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return false;
		}

		$kategori_id = filter_input(INPUT_GET, 'kategori_id', FILTER_SANITIZE_STRING);
		if(empty(trim($kategori_id))) {
			return "";
		} else {
			$get = $this->db->prepare("SELECT nama_kategori from kategori_produk where kategori_id=:kategori_id");
			$get->execute([ ':kategori_id' => $kategori_id ]);
			return $get->fetch(PDO::FETCH_ASSOC);
		}
	}

	public function editKategori() {
		$cekLogin = config::cekLoginValid_methodAdmin();
		if($cekLogin) {
			return "invalidlogin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('nama_kategori', 'Nama Kategori', 'required|maxLength[43]|unique[kategori_produk.nama_kategori]', false);
		$this->set_delimiter('<p class="warning pesan">','</p>');
		// proses error form validation
		if($this->has_formErrors()) {
			return false;
		}

		$kategori_id = filter_input(INPUT_POST, 'kategori_id', FILTER_SANITIZE_STRING);
		$nama_kategori = filter_input(INPUT_POST, 'nama_kategori', FILTER_SANITIZE_STRING);

		$edit = $this->db->prepare("UPDATE kategori_produk set nama_kategori=:nama_kategori where kategori_id=:kategori_id");
		$edit->execute([ ':nama_kategori' => $nama_kategori, ':kategori_id' => $kategori_id ]);
		if($edit->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}
}