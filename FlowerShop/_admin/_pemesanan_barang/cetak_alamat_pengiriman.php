<?php

/**** Note :
$tujuan['cekStatusNotReady'] : bertujuan jika status = all dan semua data hasil select statusnya = ready, maka izinkan action update status

*/
if(!class_exists("config")) die;
if(!class_exists("transaksi")) die;

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

$tujuan = $db->tampil_alamat_pengiriman($postAwal, $postAkhir, $status, $searchInput, $limit);

?>
<div class="cetak_alamat_pengiriman cf marginTop120px marginBottom200px">
	<div class="container">
		<div class="col-4 offset-left-4 offset-right-4 hide_for_print">
			<table class="table">
				<tr>
					<th colspan="2" class="alignCenter">Info</th>
				</tr>
				<tr>
					<th>Tahun</th>
					<td><?= $_GET['tahun']??''; ?></td>
				</tr>
				<tr>
					<th>Bulan</th>
					<td><?= $db->bulan($_GET['bulan']??''); ?></td>
				</tr>
				<tr>
					<th>Status</th>
					<td><?= config::generate_statusID($_GET['status']??''); ?></td>
				</tr>
				<tr>
					<th>Keyword Search</th>
					<td><?= $_GET['searchInput']??''; ?></td>
				</tr>
			</table>
		</div>
		<div class="col-12" id="conAction">
			<a id="print" class="btn2 btn-default green hide_for_print"><span class="fa fa-print"></span></a>
			<a href="<?= config::base_url('_admin/index.php?ref=pemesanan_barang'); ?>" class="btn2 btn-default hide_for_print"><span class="fa fa-arrow-left"></span></a>

			<?php if($tujuan['cekStatusNotReady'] != "yes" && $tujuan['hasil'] == true): ?>
			<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<a id="show_modal_update_status" class="btn2 btn-default hide_for_print">Memperbaharui Status Pemesanan</a>
			<?php endif; ?>
		</div>
		<!-- break print for chrome -->
		<center><h1 class="page_break_for_print_first"><?= date('d/M/Y|H:i:s'); ?></h1></center>

		<?php 
			$break = 6;
			if($tujuan['hasil']) :
			$i = 1;
			foreach($tujuan['hasil'] as $r) :
		?>
		<input type="hidden" class="pemesanan_id" value="<?= $r['pemesanan_id']; ?>">
		<div class="col-6 col-print-6">
			<table class="table">
				<tr>
					<th>Nama</th>
					<td><?= $r['full_name']??'all'; ?></td>
				</tr>
				<tr>
					<th>Barang/Jumlah</th>
					<td><?= $r['nama_produk']."/".$r['qty']??'all'; ?></td>
				</tr>
				<tr>
					<th>Alamat kota</th>
					<td><?= $r['city']??'all'; ?></td>
				</tr>
				<tr>
					<th>Kecamatan</th>
					<td><?= $r['subdistrict']??'all'; ?></td>
				</tr>
				<tr>
					<th>Kelurahan</th>
					<td><?= $r['village']??'all'; ?></td>
				</tr>
				<tr>
					<th>Kode Pos</th>
					<td><?= $r['zip_code']??'all'; ?></td>
				</tr>
				<tr>
					<th>No telpon</th>
					<td><?= $r['whatsapp']??'all'; ?></td>
				</tr>
			</table>
		</div>

		<?php if($i == $break && $break != count($tujuan)) : $break += 6; ?>
		<!-- break print for chrome and mozilla -->
		<center><h1 class="page_break_for_print"><?= date('d/M/Y|H:i:s'); ?></h1></center>
		<?php endif; $i++; endforeach; endif; ?>

	</div>
</div>
<?php if($tujuan['cekStatusNotReady'] != "yes" && $tujuan['hasil'] == true): ?>
<div class="FlowerModal_bg"></div>
<div class="FlowerModal">
	<p class="marginBottom65px">Apakah kamu yakin, ingin memperbaharui status menjadi dikirim?</p>

	<a id="update_status_pemesanan" class="btn2 btn2-khaki">OK</a>
	<a id="close_modal_update_status" class="btn2 btn2-red"><span class="fa fa-remove"></span></a>
</div>
<?php endif; ?>

<statusAjax value="yes">
<script type="text/javascript">
$(function() {

	$(window).scroll(function() {
		if($(window).scrollTop() > 300) {
			$("a#print").addClass("fixed");
		} else {
			$("a#print").removeClass("fixed");
		}
	});

	$("a#print").click(function() {
		window.print();
	});

	$("a#show_modal_update_status").click(function() {
		$("div.FlowerModal_bg").fadeIn();
		$("div.FlowerModal").fadeIn();
	});

	$("a#close_modal_update_status").click(function() {
		$("div.FlowerModal_bg").fadeOut();
		$("div.FlowerModal").fadeOut();
	});

	$("a#update_status_pemesanan").click(function(){
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute("value") == "yes") {
			let id = [];
			let i = 0;
			$("input.pemesanan_id").each(function() {
				id[i] = this.value;i++;
			});
			const tokenCSRF = document.querySelector("input#tokenCSRF").value;

			$.ajax({
				type:"POST",
				url:"_pemesanan_barang/proses.php?action=update_status_pemesanan_to_sent",
				data:"tokenCSRF="+tokenCSRF+"&id="+id,
				beforeSend:function() {
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon) {
					statusAjax.setAttribute("value","yes");
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

					if(respon == "success") {
						$("div.FlowerModal_bg").remove();
						$("div.FlowerModal").remove();
						$("a#show_modal_update_status").remove();
						FlowerAlert.show('Status berhasil diperbaharui menjadi dikirim!');
					} else {
						FlowerAlert.show('Status gagal diperbaharui menjadi dikirim!');
					}

					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			});
		}
	});

});
</script>