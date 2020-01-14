<?php

/**
* 
*/
class config {

	protected $db;
	
	function __construct() {

		date_default_timezone_set("Asia/Jakarta");
		if(!isset($_SESSION)) {
			session_start();
		}

		$_SESSION['FlowerShop']['LIMIT_DEFAULT'] = 2;

		try {

			$this->db = new PDO("mysql:host=localhost;dbname=flowershop","root","");
			$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		} catch(Exception$e) {
			echo "gagal konek <br>".$e->getMessage();
		}
	}

	public function sortA_Z($arr=null) {
	    if($arr != null) {
	    	$n = count($arr);
			for($i = 0; $i<$n-1; $i++) {
				for($j=0; $j<$n-($i+1); $j++){
					if($arr[$j] > $arr[$j+1]) {
						$dummy=$arr[$j];
						$arr[$j]=$arr[$j+1];
						$arr[$j+1]=$dummy;
					}
				}
			}
			return $arr;
	    }
	    return null;
	}

	public static function generate_tokenCSRF() {
		$_SESSION['FlowerShop']['CSRF_token'] = bin2hex(random_bytes(32));
	    return $_SESSION['FlowerShop']['CSRF_token'];
	}

	public static function cek_CSRF_token() {
		$tokenInput = filter_input(INPUT_POST, 'tokenCSRF', FILTER_SANITIZE_STRING);
		if($tokenInput == null) { $tokenInput = filter_input(INPUT_GET, 'tokenCSRF', FILTER_SANITIZE_STRING); }

		if(isset($_SESSION['FlowerShop']['CSRF_token']) && $tokenInput == $_SESSION['FlowerShop']['CSRF_token']) {
	    	return true;
		} else {
	    	return false;
	    }
	}

	public static function generate_statusID($status) {
		if($status == "ready") {$status="siap";}
		elseif($status == "sent") {$status="dikirim";}
		elseif($status == "accepted") {$status="diterima";}
		return $status;	    
	}

	public static function generate_uuid() {

		$data = openssl_random_pseudo_bytes(16);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	public static function change_idForQuery_IN($id) {
		
		if(!empty(trim($id))) {
			$id = explode(',', $id);
			$questionmarks = str_repeat('?,', count($id)-1).'?';
			return [ 'id'=>$id, 'questionmarks'=>$questionmarks ];
		} else {
			return [ 'id'=>[null], 'questionmarks'=>'?' ];
		}
	}

	public static function base_url($uri='') {
		return "http://localhost/flowershop/".$uri;
	}

	public static function generate_hargaFormat($harga) {
		if(is_numeric((int)$harga)) {
			return number_format((int)$harga,2,',','.');

		} else {
			return '';
		}
	}

	public static function generate_angkaFormat($angka) {
	    if(is_numeric((int)$angka)) {
			if($angka < 1000) {
				return $angka;
			} elseif($angka < 1000000) {
				// k
				return number_format($angka/1000, 1)." K";

			} elseif($angka < 1000000000) {
				// m
				return number_format($angka/1000000, 1,',','.')." M";

			} elseif($angka < 1000000000000) {
				// b
				return number_format($angka/1000000000, 1,',','.')." B";

			} else {
				// t
				return number_format($angka/1000000000000, 1,',','.')." T";
			}

		} else {
			return '';
		}
	}

	private function maxLength_minLength($data,$type,$rule,$fieldForHuman) {
		    
		$rule = str_replace("]", "", $rule);
		$arrRule = explode("[", $rule);

		if($type=="maxLength" && strlen($data)>end($arrRule)) {
			return $fieldForHuman." tidak boleh lebih dari ".end($arrRule)." karakter!";

		} elseif($type=="minLength" && strlen($data)<end($arrRule)) {
			return $fieldForHuman." tidak boleh kurang dari ".end($arrRule)." karakter!";

		} else {
			return null;
		}
	}

	private function cek_rules($data, $fieldForHuman, $rule) {
		
	    $rule = explode('|', $rule);
	    for($i = 0; $i < count($rule); $i++) {

	    	// required
	    	if(strtolower($rule[$i]) == "required") {
	    		if(empty(trim($data))) {
	    			return $fieldForHuman." tidak boleh kosong!";
	    		} else {
	    			continue;
	    		}
	    	}
	    	// max length
	    	else if(preg_match("/maxLength\[\d+\]/", $rule[$i])) {
	    		$cek = $this->maxLength_minLength($data,'maxLength',$rule[$i],$fieldForHuman);
	    		if($cek) {
	    			return $cek;
	    		} else {
	    			continue;
	    		}
	    	}
	    	// min length
	    	else if(preg_match("/minLength\[\d+\]/", $rule[$i])) {
	    		$cek = $this->maxLength_minLength($data,'minLength',$rule[$i],$fieldForHuman);
	    		if($cek) {
	    			return $cek;
	    		} else {
	    			continue;
	    		}
	    	}
	    	// email
	    	else if(strtolower($rule[$i]) == "email") {
	    		if(!filter_var($data, FILTER_VALIDATE_EMAIL)) {
	    			return $fieldForHuman." tidak valid!";
	    		} else {
	    			continue;
	    		}
	    	}
	    	// integer
	    	else if(strtolower($rule[$i]) == "integer") {
	    		if(!filter_var($data, FILTER_VALIDATE_INT)) {
	    			return $fieldForHuman." tidak valid!";
	    		} else {
	    			continue;
	    		}
	    	}
	    	// unique table.field
	    	else if(preg_match("/unique\[\w+\.\w+\]/", $rule[$i])) {

	    		$ruleReplace = str_replace("]", "", $rule[$i]);
	    		$arrTbl1 = explode("[", $ruleReplace);
	    		$arrTbl2 = explode(".", end($arrTbl1));
	    		$table = $arrTbl2[0];
	    		$field = end($arrTbl2);
	    		// cek apakah data sudah ada didatabase
	    		$cek = $this->db->prepare("SELECT $field from $table where $field=:field");
	    		$cek->execute([ ':field' => $data ]);
	    		if($cek->rowCount() >= 1) {
	    			return $fieldForHuman." sudah ada, mohon cari ".$fieldForHuman." yang lain!";
	    		}
	    	}
	    }

	    return null;
	}

	public function form_validation($field=null, $fieldForHuman=null, $rule=null, $old_val=true) {

		// jika fieldForHuman kosong
		if(empty(trim($fieldForHuman))) $fieldForHuman = $field;
		if(!empty(trim($field)) && !empty(trim($rule))) {

			$data = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
			// jika data tidak valid
			$cek = $this->cek_rules($data, $fieldForHuman, $rule);
			// set session old value
			if($old_val==true) {
				$_SESSION['FlowerShop']['old_val'][$field] = $data;
			}
			if($cek) {
				// set session error
				$_SESSION['FlowerShop']['form_errors'][$field] = $cek;
				return true;
			} else {
				return false;
			}
		}
	}

	public static function set_delimiter($delimiterOpen=null, $delimiterClose=null) {
	    
	    if( isset($_SESSION['FlowerShop']['form_errors']) && !empty(trim($delimiterOpen)) && !empty(trim($delimiterClose)) ) {

	    	foreach($_SESSION['FlowerShop']['form_errors'] as $key=>$val) {
	    		$_SESSION['FlowerShop']['form_errors'][$key] = $delimiterOpen.$val.$delimiterClose;
	    	}

	    	return true;
	    }
	    return false;
	}

	public static function has_formErrors() {
	    if(isset($_SESSION['FlowerShop']['form_errors'])) {
	    	return true;
	    } else {
	    	return false;
	    }
	}

	public static function get_form_errors() {
	    if(isset($_SESSION['FlowerShop']['form_errors'])) {
	    	$errors = $_SESSION['FlowerShop']['form_errors'];
	    	unset($_SESSION['FlowerShop']['form_errors']);
	    	return $errors;
	    }

	    return null;
	}

	public static function get_old_value() {
	    if(isset($_SESSION['FlowerShop']['old_val'])) {
	    	$old_val = $_SESSION['FlowerShop']['old_val'];
	    	unset($_SESSION['FlowerShop']['old_val']);
	    	return $old_val;
	    }

	    return null;
	}

	/* cek login valid */
		protected function cekLoginValid_methodAdmin(){
			if(isset($_SESSION['FlowerShop']['userLogin']) && $_SESSION['FlowerShop']['level'] == 'admin') {
				return false;
			} else {
				return true;
			}
		}

		public function cekLoginValid_halamanAdmin() {
			$cek = $this->cekLoginValid_methodAdmin();
			// jika cek true; berarti tidak login atau login tapi bukan admin
			if($cek) {
				header("Location: ".config::base_url('index.php?ref=produk'));
				return true;
			}
			return false;
		}

		public function cekLoginYesHalamanLogin_and_sigup() {
			if(isset($_SESSION['FlowerShop']['userLogin'])) {
				if($_SESSION['FlowerShop']['level'] == 'admin') {
					header("Location: _admin/index.php");
				} else {
					header("Location: index.php");
				}
				return true;
			}
			return false;
		}

		protected function cekLoginNo_methodUser() {
			if(!isset($_SESSION['FlowerShop']['userLogin'])) {
				return true;
			}
			return false;
		}

		public function cekLoginNo_halamanDetailProduk() {

			$cek = $this->cekLoginNo_methodUser();
			if($cek) {
				header("Location: ".config::base_url()."index.php?ref=login&ket=accessDetailProdukNoLogin");
				return $cek;
			}
			return $cek;
		}

		public function cekLoginNo_HalamanUser() {

			$cek = $this->cekLoginNo_methodUser();
			if($cek) {
				header("Location: ".config::base_url()."index.php?ref=login");
				return $cek;
			}		
			return $cek;
		}
	/* cek login valid end */

	public static function page($pageDefault,$pageGet) {

		switch ($pageGet) {
			default:
				if(!file_exists("$pageDefault")) die ("file kosong");
				include "$pageDefault";
			break;
			case "login":
				if(!file_exists("sigup/login.php")) die ("file kosong");
				include "sigup/login.php";
			break;
			case "sigup":
				if(!file_exists("sigup/sigup.php")) die ("file kosong");
				include "sigup/sigup.php";
			break;
			case "produk":
				if(!file_exists("produk/produk.php")) die ("file kosong");
				include "produk/produk.php";
			break;
			case "detail_produk":
				if(!file_exists("produk/detail_produk.php")) die ("file kosong");
				include "produk/detail_produk.php";
			break;
			case "editAccount":
				if(!file_exists("sigup/editAccount.php")) die ("file kosong");
				include "sigup/editAccount.php";
			break;
			case "cart":
				if(!file_exists("cart/cart.php")) die ("file kosong");
				include "cart/cart.php";
			break;
			case "checkout":
				if(!file_exists("cart/checkout.php")) die ("file kosong");
				include "cart/checkout.php";
			break;
			case "callback_pay":
				if(!file_exists("cart/callback_pay.php")) die ("file kosong");
				include "cart/callback_pay.php";
			break;

			// admin
			case "cetak_alamat_pengiriman":
				if(!file_exists("_pemesanan_barang/cetak_alamat_pengiriman.php")) die ("file kosong");
				include "_pemesanan_barang/cetak_alamat_pengiriman.php";
			break;
			case "pemesanan_barang":
				if(!file_exists("_pemesanan_barang/pemesanan_barang.php")) die ("file kosong");
				include "_pemesanan_barang/pemesanan_barang.php";
			break;
			case "kategori":
				if(!file_exists("_kategori/kategori.php")) die ("file kosong");
				include "_kategori/kategori.php";
			break;
			case "addKategori":
				if(!file_exists("_kategori/addKategori.php")) die ("file kosong");
				include "_kategori/addKategori.php";
			break;
			case "editKategori":
				if(!file_exists("_kategori/editKategori.php")) die ("file kosong");
				include "_kategori/editKategori.php";
			break;
			case "produkAdmin":
				if(!file_exists("_produk/produk.php")) die ("file kosong");
				include "_produk/produk.php";
			break;
			case "tambahProduk":
				if(!file_exists("_produk/tambahProduk.php")) die ("file kosong");
				include "_produk/tambahProduk.php";
			break;
			case "editProduk":
				if(!file_exists("_produk/editProduk.php")) die ("file kosong");
				include "_produk/editProduk.php";
			break;
			case "user":
				if(!file_exists("_user/user.php")) die ("file kosong");
				include "_user/user.php";
			break;
			case "addUser":
				if(!file_exists("_user/addUser.php")) die ("file kosong");
				include "_user/addUser.php";
			break;
			case "editUser":
				if(!file_exists("_user/editUser.php")) die ("file kosong");
				include "_user/editUser.php";
			break;
		}
	}
}