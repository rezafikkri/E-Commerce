<?php if(!class_exists("config")) die; 

/**
* 
*/
class login extends config {

	public function prosesLogin() {
		$token = config::cek_CSRF_token();
		if(!$token) {
			return $token;//false
		}

		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		if(empty(trim($username))) {
			return json_encode(["usernameEmpty"=>"yes"]);
		}
		if(empty(trim($password))) {
			return json_encode(["passwordEmpty"=>"yes"]);
		}

		$page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING);
		// cek apakah user terdaftar
		$cek = $this->db->prepare("SELECT user_id, level, full_name, password from user where username=:username");
		$cek->execute([ ':username' => $username ]);
		$r = $cek->fetch(PDO::FETCH_ASSOC);
		if($cek->rowCount() == 1) {
			if(password_verify($password,$r['password'])) {
				
				$_SESSION['FlowerShop']['userLogin'] = 'yes';
				$_SESSION['FlowerShop']['level'] = $r['level'];
				$_SESSION['FlowerShop']['fullName'] = $r['full_name'];
				$_SESSION['FlowerShop']['userId'] = $r['user_id'];

				if($r['level'] == 'admin') {
					$return = json_encode(['success'=>"_admin/"]);
				} else if($page != null) {
					$return = json_encode(['success'=>"index.php?ref=".$page]);
				} else {
					$return = json_encode(['success'=>"index.php"]);
				}

				return $return;

			} else {
				return json_encode(["passwordWrong"=>'yes']);
			}

		} else {
			return json_encode(["usernameNotFound"=>"yes"]);
		}
	}
}