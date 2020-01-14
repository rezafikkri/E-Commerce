<?php if(!class_exists("config")) die;

/** note :
*   get_password_asli for delete account, cek user valid with input password

	$action di delete user karena pada saat mendelete user di halaman user(level user) kita sudah mengecek loginValid dan CSRFtoken di getpasswordAsli, sedangkan di halaman admin kita tidak perlu getpasswordAsli untuk mendelete user.
*/
class user extends config {

	private function generateLevelUser() {
	    
	    // jika level kosong
	    $level = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_STRING);
		if(empty(trim($level)) || $level == 'null' || !isset($_SESSION['FlowerShop']['userLogin']) || $_SESSION['FlowerShop']['level'] != "admin") {
			$level = "user";
		}
		return $level;
	}
	
	public function add_user() {
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$this->form_validation('fullName','Full Name','required|maxLength[32]',false);
		$this->form_validation('username','Username','required|maxLength[32]|unique[user.username]',false);
		$this->form_validation('password','Password','required', false);
		$this->form_validation('email','Email','required|maxLength[50]|email',false);
		$this->form_validation('whatsapp','Whatsapp','required|maxLength[32]|integer',false);

		// proses error form validation
		$errors = $this->get_form_errors();
		if($errors) {
			return json_encode(['errors'=>$errors]);
		}

		$fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$passwordHash = PASSWORD_HASH($password, PASSWORD_ARGON2I);
		$user_id = config::generate_uuid();
		$level = $this->generateLevelUser();

		$whatsapp = filter_input(INPUT_POST, 'whatsapp', FILTER_SANITIZE_STRING);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
		$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
		$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
		$subdistrict = filter_input(INPUT_POST, 'subdistrict', FILTER_SANITIZE_STRING);
		$village = filter_input(INPUT_POST, 'village', FILTER_SANITIZE_STRING);
		$zip_code = filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_STRING);

		// insert data user
		$insert = $this->db->prepare("INSERT INTO user set user_id=:user_id, full_name=:fullName, username=:username, password=:password, level=:level, whatsapp=:whatsapp, email=:email, country=:country, city=:city, subdistrict=:subdistrict, village=:village, zip_code=:zip_code ");
		if($insert->execute([ ':user_id' => $user_id, ':fullName' => $fullName, ':username' => $username, ':password' => $passwordHash, ':level' => $level, ':whatsapp'=>$whatsapp, ':email'=>$email, ':country'=>$country, ':city'=>$city, ':subdistrict'=>$subdistrict, ':village'=>$village, ':zip_code'=>$zip_code ])) {

			if(isset($_SESSION['FlowerShop']['userLogin']) && $_SESSION['FlowerShop']['level'] == 'admin') {
				return json_encode(['success'=>'yes']);
			} else {
				return json_encode(['success'=>'index.php?ref=login']);
			}
		} else {
			return false;
		}
	}

	private function generate_user_id() {
		// generate user_id
		if($_SESSION['FlowerShop']['level']=="admin") {
			$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);
		} else {
			$user_id = $_SESSION['FlowerShop']['userId'];
		}
		return $user_id;
	}

	public function editUser() {
		$cekLogin = config::cekLoginNo_methodUser();
		if($cekLogin) {
			return "nologin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		} 

		$this->form_validation('full_name','Full Name','required|maxLength[32]',false);
		$this->form_validation('username','Username','required|maxLength[32]|unique[user.username]',false);
		$this->form_validation('email','Email','required|maxLength[50]|email',false);
		$this->form_validation('whatsapp','Whatsapp','required|maxLength[32]|integer',false);
		$this->set_delimiter('<p class="warning pesan">', '</p>');

		// proses error form validation
		if($this->has_formErrors()) {
			return false;
		}

		$fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$level = $this->generateLevelUser();

		$whatsapp = filter_input(INPUT_POST, 'whatsapp', FILTER_SANITIZE_STRING);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
		$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
		$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
		$subdistrict = filter_input(INPUT_POST, 'subdistrict', FILTER_SANITIZE_STRING);
		$village = filter_input(INPUT_POST, 'village', FILTER_SANITIZE_STRING);
		$zip_code = filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_STRING);
		$user_id = $this->generate_user_id();

		if(empty(trim($password))) {

			$insert = $this->db->prepare("UPDATE user set full_name=:fullName, username=:username, level=:level, whatsapp=:whatsapp, email=:email, country=:country, city=:city, subdistrict=:subdistrict, village=:village, zip_code=:zip_code where user_id=:user_id");
			if($insert->execute([ ':fullName' => $fullName, ':username' => $username, ':level' => $level, ':whatsapp'=>$whatsapp, ':email'=>$email, ':country'=>$country, ':city'=>$city, ':subdistrict'=>$subdistrict, ':village'=>$village, ':zip_code'=>$zip_code, ':user_id' => $user_id ])) {

				return true;
			} else {
				return false;
			}

		} else {

			$passwordHash = PASSWORD_HASH($password, PASSWORD_ARGON2I);
			$insert = $this->db->prepare("UPDATE user set full_name=:fullName, username=:username, password=:password, level=:level, whatsapp=:whatsapp, email=:email, country=:country, city=:city, subdistrict=:subdistrict, village=:village, zip_code=:zip_code where user_id=:user_id");
			if($insert->execute([ ':fullName' => $fullName, ':username' => $username, ':password' => $passwordHash, ':level' => $level, ':whatsapp'=>$whatsapp, ':email'=>$email, ':country'=>$country, ':city'=>$city, ':subdistrict'=>$subdistrict, ':village'=>$village, ':zip_code'=>$zip_code, ':user_id' => $user_id ])) {

				return true;
			} else {
				return false;
			}
		}
	}

	public function deleteUser($action) {
		if($action == "admin") {
			$cekLogin = config::cekLoginValid_methodAdmin();
			if($cekLogin) {
				return "nologin";
			}
			$token = config::cek_CSRF_token();
			if(!$token) {
				return $token;//false
			}
		}

		$user_id = $this->generate_user_id();
		$dataIn = config::change_idForQuery_IN($user_id);
		$user_id = $dataIn['id'];
		$questionmarks = $dataIn['questionmarks'];

		try {
			$del = $this->db->prepare("DELETE from user where user_id in($questionmarks)");
			$del->execute($user_id);

			if($del->rowCount()>0) {
				if($_SESSION['FlowerShop']['level'] != "admin") {
					unset($_SESSION['FlowerShop']);
				}
				return "success";
			} else {
				return "dataNull";
			}
		}catch(PDOException$e) {
			return false;
		}
	}

	public function getPasswordAsli() {

		$cekLogin = config::cekLoginNo_methodUser();
		if($cekLogin) {
			return "nologin";
		}
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$user_id = $_SESSION['FlowerShop']['userId'];
		$get = $this->db->prepare("SELECT password from user where user_id=:user_id and level!=:level");
		$get->execute([ ':user_id' => $user_id, ':level' => 'admin' ]);
		return $get->fetch(PDO::FETCH_ASSOC);
	}

	public function tampilUser() {

		$tampil = $this->db->prepare("SELECT user_id, full_name, username, level from user order by level desc");
		$tampil->execute();
		while ($r=$tampil->fetch(PDO::FETCH_ASSOC)) {
			$hasil[]=$r;
		}

		return @$hasil;
	}

	public function getOneUser($user_id) {

		$tampil = $this->db->prepare("SELECT user_id, full_name, username, level, email, whatsapp, country, city, subdistrict, village, zip_code
			from user where user_id=:user_id");
		$tampil->execute([ ':user_id' => $user_id ]);
		return $tampil->fetch(PDO::FETCH_ASSOC);
	}

	public function pesanEditAccount() {
	    
	    if(isset($_SESSION['FlowerShop']['pesanEditAccount']) && $_SESSION['FlowerShop']['pesanEditAccount']=='userUpdated') {
	    	unset($_SESSION['FlowerShop']['pesanEditAccount']);
			return '<p class="pesan good">Update berhasil</p>';

		} else if(isset($_SESSION['FlowerShop']['pesanEditAccount']) && $_SESSION['FlowerShop']['pesanEditAccount']=='gagalUpdate') {

			unset($_SESSION['FlowerShop']['pesanEditAccount']);
			return '<p class="pesan warning">Update gagal</p>';
		}
	}
}