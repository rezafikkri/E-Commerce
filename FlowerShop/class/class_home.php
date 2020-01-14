<?php if(!class_exists("config")) die;

/**
* 
*/
class home extends config {
	
	public function totalUsers(){
		$get = $this->db->prepare("SELECT count(user_id) as jmlUsers from user");
		$get->execute();
		$jmlUsers = $get->fetch(PDO::FETCH_ASSOC)['jmlUsers']??0;
		return $this->generate_angkaFormat($jmlUsers);
	}

	public function totalOrder() {
	    $get = $this->db->prepare("SELECT SUM(qty) as jmlOrder from transaksi where status=:status");
	    $get->execute([':status'=>'ordered']);
	    $jmlOrder = $get->fetch(PDO::FETCH_ASSOC)['jmlOrder']??0;
	    return $this->generate_angkaFormat($jmlOrder);
	}

	public function totalPayment() {
	    $get = $this->db->prepare("SELECT SUM(qty) as jmlPayment from transaksi where status=:status");
	    $get->execute([':status'=>'paid']);
	    $jmlPayment = $get->fetch(PDO::FETCH_ASSOC)['jmlPayment']??0;
	    return $this->generate_angkaFormat($jmlPayment);
	}

	public function sendMsg($id,$msg) {
	    echo "id: $id".PHP_EOL;
	    echo "data: $msg".PHP_EOL;
	    echo PHP_EOL;
	    ob_flush();
	    flush();
	}
}