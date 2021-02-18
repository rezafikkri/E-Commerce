<?php

include '../../init.php';
include 'tcpdf_include.php';

$db = new transaksi;
if($db->cekLoginValid_halamanAdmin()) die;

$tahun = filter_input(INPUT_GET, 'tahun', FILTER_SANITIZE_STRING);
$bulan = (int)filter_input(INPUT_GET, 'bulan', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$searchInput = filter_input(INPUT_GET, 'searchInput', FILTER_SANITIZE_STRING);
$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_STRING);

if(!is_numeric($bulan) || $bulan > 12 || $bulan < 1) {
	$postAwal = mktime(0,0,0,01,01,$tahun);
	$postAkhir = mktime(0,0,0,12,31,$tahun);
} else {
	$postAwal = mktime(0,0,0,$bulan,01,$tahun);
	$jmlDay = date("t", $postAwal);
	$postAkhir = mktime(0,0,0,$bulan,$jmlDay,$tahun);
}

if($limit == null) {
	$limit = $_SESSION['FlowerShop']['LIMIT_DEFAULT'];
}

if(strlen($searchInput) > 0) {
	$data = $db->tampil_pemesanan_barang($postAwal, $postAkhir, $status, $searchInput, 0,$limit, "ASC");
} else {
	$data = $db->tampil_pemesanan_barang($postAwal, $postAkhir, $status, null, 0,$limit, "ASC");
}

// proses post generate bulan first to last
$postSort = config::sortA_Z($data['arrPost']);
if($postSort) {
	$firstMounth = $db->bulan(date('m',$postSort[0]));
	$lastMounth = $db->bulan(date('m',end($postSort)));
	if(date('m',$postSort[0]) < date('m',end($postSort)) ) {
		$bulanJudul = $firstMounth.'-'.$lastMounth.' '.$tahun;
	} else {
		$bulanJudul = $firstMounth.' '.$tahun;
	}
} else {
	$bulanJudul = $db->bulan($bulan).' '.$tahun;
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Reza Sariful Fikri');
$pdf->setTitle('Laporan Pemesanan');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('helvetica','',10);
$table = '<h1 style="font-size: 13px; text-align: center; margin-bottom: 10px;">Laporan Pemesanan Barang, '.$bulanJudul.'</h1>
<table border="1" cellspacing="0.5" cellpadding="5">
	<tr bgcolor="#f2f2f2">
		<th><b>Pengguna</b></th>
		<th><b>Status</b></th>
		<th><b>Tanggal</b></th>
		<th><b>Jumlah</b></th>
		<th><b>Total Transaksi</b></th>
	</tr>';

$totalQty = 0;
$totalPem = 0;
if($data['hasil']) {
	foreach($data['hasil'] as $r) {
		if($r['status'] == 'ready') $colorStatus = "#f2910a;";
		elseif($r['status'] == 'sent') $colorStatus = "#2693bf;";
		elseif($r['status'] == 'accepted') $colorStatus = "#55c125;";

		$table .= '<tr>
			<td>'.$r['username'].'</td>
			<td style="color: '.$colorStatus.'">'.config::generate_statusID($r['status']).'</td>
			<td>'.date('d m Y|H:i:s', $r['post']).'</td>
			<td>'.$r['qty'].'</td>
			<td>Rp '.config::generate_hargaFormat($r['qty']*$r['harga']).'</td>
		</tr>';

		$totalQty += $r['qty'];
		$totalPem += $r['qty']*$r['harga'];
	}
}

$table .= '<tr>
		<th colspan="3" align="right"><b>Total</b></th>
		<td>'.$totalQty.'</td>
		<td>Rp '.config::generate_hargaFormat($totalPem).'</td>
	</tr>
</table>';

$pdf->writeHTML($table, true, false, true, false, '');

$pdf->Output('Laporan_pemesanan_'.$bulanJudul.'.pdf', 'I');