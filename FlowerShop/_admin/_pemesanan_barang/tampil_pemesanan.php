<?php

include '../../init.php';
$db = new transaksi;

if(isset($_SESSION['FlowerShop']['userLogin']) && $_SESSION['FlowerShop']['level'] == "admin") {
	$tahun = filter_input(INPUT_POST, 'tahun', FILTER_SANITIZE_STRING);
	$bulan = (int)filter_input(INPUT_POST, 'bulan', FILTER_SANITIZE_STRING);
	$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
	$searchInput = filter_input(INPUT_POST, 'searchInput', FILTER_SANITIZE_STRING);
	$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_STRING);
	$totalPemOld = filter_input(INPUT_POST, 'totalPem', FILTER_SANITIZE_STRING);
	$totalQtyOld = filter_input(INPUT_POST, 'totalQty', FILTER_SANITIZE_STRING);

	if(!is_numeric($bulan) || $bulan > 12 || $bulan < 1) {
		$postAwal = mktime(0,0,0,01,01,$tahun);
		$postAkhir = mktime(0,0,0,12,31,$tahun);
	} else {
		$postAwal = mktime(0,0,0,$bulan,01,$tahun);
		$jmlDay = date("t", $postAwal);
		$postAkhir = mktime(0,0,0,$bulan,$jmlDay,$tahun);
	}

	if(strlen(trim($searchInput)) > 0) {
		$data = $db->tampil_pemesanan_barang($postAwal, $postAkhir, $status, $searchInput, $offset,$_SESSION['FlowerShop']['LIMIT_DEFAULT'])['hasil'];
	} else {
		$data = $db->tampil_pemesanan_barang($postAwal, $postAkhir, $status, null, $offset,$_SESSION['FlowerShop']['LIMIT_DEFAULT'])['hasil'];
	}

	if($data) {
		$totalQty = 0;
		$totalPem = 0;
		foreach($data as $key=>$val) {
			$data[$key]['jmlPemesanan'] = config::generate_hargaFormat((int)$val['qty']*(int)$val['harga']);
			$data[$key]['harga'] = config::generate_hargaFormat($val['harga']);
			$data[$key]['post'] = date("d M Y | H:i:s",$val['post']);
			if($data[$key]['status'] == 'sent') { $data[$key]['classStatus'] = 'blue'; }
			elseif($data[$key]['status'] == 'accepted') { $data[$key]['classStatus'] = 'green'; }
			else { $data[$key]['classStatus'] = 'orange'; }
			$data[$key]['status'] = config::generate_statusID($val['status']);

			$totalQty += $val['qty'];
			$totalPem += (int)$val['qty']*(int)$val['harga'];
		}

		/* pem */
		$arrTotalPemOld = explode(" ", $totalPemOld);
		if(count($arrTotalPemOld) == 3) {
			if(strtoupper($arrTotalPemOld[2]) == "K") {
				$totalPemOldReal = (int)$arrTotalPemOld[1]*1000;

			} else if(strtoupper($arrTotalPemOld[2]) == "M") {
				$totalPemOldReal = (int)$arrTotalPemOld[1]*1000000;

			} else if(strtoupper($arrTotalPemOld[2]) == "B") {
				$totalPemOldReal = (int)$arrTotalPemOld[1]*1000000000;

			} else if(strtoupper($arrTotalPemOld[2]) == "T") {
				$totalPemOldReal = (int)$arrTotalPemOld[1]*1000000000000;
			}

			$totalPem += $totalPemOldReal;
		}
		/* qty */
		if((int)$totalQtyOld != 0) {
			$totalQty += (int)$totalQtyOld;
		}

		echo json_encode([ 'data'=>$data, 'totalQty'=>$totalQty, 'totalPem'=>config::generate_hargaFormat($totalPem) ]);
	} else {
		echo null;
	}
}/* cek login */ else {
	echo null;
}