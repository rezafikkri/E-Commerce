<?php
	if(!class_exists("config")) die;
	if(!class_exists("transaksi")) die;
	  
	$db = new transaksi;
	if($db->cekLoginValid_halamanAdmin()) die;

	$tahun = date("Y");
	$bulan = date("m");
	$jmlDay = date("t", mktime(0,0,0,$bulan,01,$tahun));
	$postAwal = mktime(0,0,0,$bulan,01,$tahun);
	$postAkhir = mktime(0,0,0,$bulan,$jmlDay,$tahun);

	$data = $db->tampil_pemesanan_barang($postAwal, $postAkhir, "all", null, 0,$_SESSION['FlowerShop']['LIMIT_DEFAULT']);
?>
<div class="pemesanan_barang marginTop120px marginBottom200px cf">
	<div class="container">
		<div class="col-12 nopadding-b">
			<div class="col-2 nopaddingInput-all marginTop15px">
				<a id="export_pdf_pemesanan" target="_blank" href="<?= config::base_url('_admin/_pemesanan_barang/export_pdf_pemesanan.php?tahun='.date('Y').'&bulan='.date('m').'&status=all'); ?>" class="btn2 btn-default red" title="Export laporan pemesanan barang"><span class="fa fa-file-pdf-o"></span></a>

				<a id="printAlamatPengiriman" title="Print alamat pengiriman" href="<?= config::base_url('_admin/index.php?ref=cetak_alamat_pengiriman&tahun='.date('Y').'&bulan='.date('m').'&status=all'); ?>" class="btn2 btn-default green"><span class="fa fa-print"></span></a>

				<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<a id="updateStatusBecomeAccepted" title="Update status menjadi diterima" class="btn2 btn-default green"><span class="fa fa-arrow-up"></span></a>
			</div>
			<div class="col-2 nopaddingInput-all">
				<select id="bulan">
					<option value="all" selected="">Bulan</option>
					<?php  
						for($bulan=1; $bulan<=12; $bulan++) :
					?>
					<option value="<?= $bulan; ?>"
					<?= date("m")==$bulan?'selected':''; ?>
					><?= $db->bulan($bulan); ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-2 nopaddingInput-r nopaddingInput-t-b">
				<select id="tahun">
					<option disabled="">Tahun</option>
					<?php 
						$tahunNow = date('Y');
						$firstYear = $db->getFirstYearTransaksi();
						for($tahun=$firstYear; $tahun<=$tahunNow; $tahun++) :
					?>
					<option value="<?= $tahun; ?>"
					<?= $tahun==$tahunNow?'selected':''; ?>
					><?= $tahun; ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-2 nopaddingInput-r nopaddingInput-t-b">
				<select id="status">
					<option value="all">Status</option>
					<?php  
						$status = ['ready','sent','accepted'];
						foreach($status as $r) :
					?>
					<option value="<?= $r; ?>"><?= config::generate_statusID($r); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-4 nopaddingInput-r nopaddingInput-t-b">
				<div class="search">
					<input type="text" id="keyword" placeholder="Pencarian ...">
					<a id="search"><span class="fa fa-search"></span></a>
				</div>
			</div>

		</div><!-- col-12 nopadding-b -->

		<div class="col-12 nopadding-t">
			<table class="table marginTop20px">
				<tr>
					<th width="10"></th>
					<th>Pengguna</th>
					<th>Status</th>
					<th>Tanggal</th>
					<th>Jumlah</th>
					<th colspan="2">Total Pesanan</th>
				</tr>
				<tbody id="tampilTransaksi">
				<?php 
				if($data['hasil']) :
				$no = 1;
				$totalQty = 0;
				$totalPem = 0;
				foreach($data['hasil'] as $r) :

					$totalQty += (int)$r['qty'];
					$totalPem += (int)$r['qty']*(int)$r['harga'];
					$classStatus = 'orange';
					if($r['status']=='sent') $classStatus = 'blue';
					elseif($r['status'] == 'accepted') $classStatus = 'green';
				?>
				<tr class="jmlPemesananTr">
					<td>
						<a class="caret" caret="caret<?= $no; ?>"><span class="fa fa-caret-right"></span></a>
					</td>
					<td><?= $r['username']; ?></td>

					<?php if($r['status'] == 'sent') : ?>
					<td>
						<a id="checkedStatusForAccepted" class="<?= $classStatus; ?>"><?= config::generate_statusID($r['status']); ?></a>
						<input type="checkbox" name="update[]" id="update<?= $no; ?>" class="update" value="<?= $r['pemesanan_id']; ?>">
						<label for="update<?= $no; ?>"></label>
					</td>
					<?php else: ?>
					<td>
						<a class="<?= $classStatus; ?> hoverNoPointer"><?= config::generate_statusID($r['status']); ?></a>
					</td>
					<?php endif; ?>

					<td><?= date("d M Y | H:i:s",$r['post']); ?></td>
					<td><?= $r['qty']; ?></td>
					<td>Rp <?= config::generate_hargaFormat((int)$r['qty']*(int)$r['harga']); ?></td>
				</tr>
				<tr id="caret<?= $no; ?>" class="caretDown">
					<td colspan="7">
						<p class="marginBottom5px"><b>Nama Lengkap</b> : <?= $r['full_name']; ?></p>
						<p><b><?= $r['nama_produk']; ?></b> : Rp <?= config::generate_hargaFormat($r['harga']); ?></p>
					</td>
				</tr>
				<?php $no++; endforeach; else : ?>
				<tr>
					<td colspan="7" class="emptyData">Data kosong</td>
				</tr>
				<?php endif; ?>
				</tbody><!-- tampilTransaksi -->
				<tr>
					<th colspan="4" class="alignRight">Total</th>
					<td id="totalQty"><?= $totalQty??''; ?></td>
					<td colspan="2" id="totalPem">Rp <?= config::generate_hargaFormat($totalPem??''); ?></td>
				</tr>
			</table>

			<center class="marginTop20px"><a id="readMore" class="btn2 btn-default"><span class="fa fa-arrow-down"></span></a></center>
		</div><!-- col-12 nopadding-t -->
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){

	/* 
		tampil pemesanan dan change url cetak alamat pengiriman 
		action != undefined khusus untuk function readMore
	*/
	function tampil_pemesanan(bulan, tahun, status, searchInput, action, offset=0, totalPem=0, totalQty=0) {
		const valtahun = tahun.value;
		const valbulan = bulan.value;
		const valstatus = status.value;
		const valsearchInput = searchInput.value;

		const statusAjax = document.querySelector("statusAjax");

		$.ajax({
			type:"POST",
			url:"_pemesanan_barang/tampil_pemesanan.php",
			data:{tahun:valtahun, bulan:valbulan, status:valstatus, searchInput:valsearchInput, offset:offset, totalPem:totalPem, totalQty:totalQty},
			beforeSend:function() {
				$("div.conLoader").addClass("muncul");
				$("div.conLoader .loader").addClass("loader90");
				bulan.setAttribute('disabled','disabled');
				tahun.setAttribute('disabled','disabled');
				status.setAttribute('disabled','disabled');
				searchInput.setAttribute('disabled','disabled');
				statusAjax.setAttribute('value','ajax');
			},
			success:function(respon) {
				statusAjax.setAttribute('value','yes');
				document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

				let datas;
				try {
					datas = JSON.parse(respon);
				} catch(e){}

				if(datas != undefined && datas.data != undefined) {
					let hasil = '';
					let jmlPemesananTr = 0;
					if(action != undefined) jmlPemesananTr = document.querySelectorAll('tr.jmlPemesananTr').length;

					datas.data.forEach(function(e, i){
						hasil+='<tr class="jmlPemesananTr">';
							hasil+='<td width="10"><a class="caret" caret="caret'+(jmlPemesananTr+i+1)+'"><span class="fa fa-caret-right"></span></td>';
							hasil+='<td>'+e.username+'</td>';

							hasil+='<td>';
							if(e.status == "dikirim") {
							hasil+='<a id="checkedStatusForAccepted" class="'+e.classStatus+'">'+e.status+'</a><input type="checkbox" name="update[]" id="update'+(i+jmlPemesananTr)+'" class="update" value="'+e.pemesanan_id+'"><label for="update'+(i+jmlPemesananTr)+'"></label>'
							} else {
							hasil+='<a class="'+e.classStatus+' hoverNoPointer">'+e.status+'</a>'
							}
							hasil+='</td>';

							hasil+='<td>'+e.post+'</td>';
							hasil+='<td>'+e.qty+'</td>';
							hasil+='<td>Rp '+e.jmlPemesanan+'</td>';
						hasil+='</tr>';
						hasil+='<tr id="caret'+(jmlPemesananTr+i+1)+'" class="caretDown">';
							hasil+='<td colspan="7"><b>'+e.nama_produk+'</b> : Rp '+e.harga+'</td>';
						hasil+='</tr>';
					});

					$("td#totalQty").html(datas.totalQty);
					$("td#totalPem").html("Rp "+datas.totalPem);
					if(action == undefined) {						
						$("tbody#tampilTransaksi").html(hasil);
					} else {
						$("tbody#tampilTransaksi").append(hasil);
					}
					
				} else {
					if(action == undefined) {
						let hasil = '<tr>';
						hasil+='<td class="emptyData" colspan="6">Data tidak ditemukan</td>';
						hasil +='</tr>';
						$("td#totalQty").html("");
						$("td#totalPem").html("");
						$("tbody#tampilTransaksi").html(hasil);
						$("a#jmlDataTersisa").text('0/0');
					} else {
						FlowerAlert.show("Tidak ada data lagi!");
					}
				}

				bulan.removeAttribute('disabled');
				tahun.removeAttribute('disabled');
				status.removeAttribute('disabled');
				searchInput.removeAttribute('disabled');

				// limit adalah jumlah seluruh data yang telah ditampilkan
				const limit = $("tr.jmlPemesananTr").length;
				change_url_cetak_and_export(bulan, tahun, status, searchInput, limit);

				$("div.conLoader").removeClass("muncul");
				$("div.conLoader .loader").removeClass("loader100");
			}
		});
	}

	function change_url_cetak_and_export(bulan, tahun, status, searchInput, limit=0){

		const hrefPrint = $("a#printAlamatPengiriman").attr('href');
		const hrefExport = $("a#export_pdf_pemesanan").attr('href');
		const valtahun = tahun.value;
		const valbulan = bulan.value;
		const valstatus = status.value;
		const valsearchInput = searchInput.value;
		// http://localhost/flowershop/_admin/index.php?ref=
		let urlPrint = hrefPrint.slice(0, hrefPrint.indexOf("cetak_alamat_pengiriman"));
		// http://localhost/flowershop/_admin/_pemesanan_barang/export_pdf_pemesanan.php
		let urlExport = hrefExport.slice(0, hrefExport.indexOf("?"));

		const whereCondition = valtahun+"&bulan="+valbulan+"&status="+valstatus;
		urlPrint += "cetak_alamat_pengiriman&tahun="+whereCondition;
		urlExport += "?tahun="+whereCondition;

		if(valsearchInput.length != 0) {
			urlPrint += "&searchInput="+valsearchInput;
			urlExport += "&searchInput="+valsearchInput;
		}
		if(limit != 0) {
			urlPrint += "&limit="+limit;
			urlExport += "&limit="+limit;
		}

		$("a#printAlamatPengiriman").attr("href",urlPrint);
		$("a#export_pdf_pemesanan").attr("href",urlExport)
	}

	const tahun = document.querySelector("select#tahun");
	const bulan = document.querySelector("select#bulan");
	const status = document.querySelector("select#status");
	const searchInput = document.querySelector("input#keyword");
	const btnsearch = document.querySelector("a#search");
	const statusAjax = document.querySelector("statusAjax");
	if(tahun != null) {
		tahun.addEventListener("change", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_pemesanan(bulan, tahun, status, searchInput);
			}
		});
	}
	if(bulan != null) {
		bulan.addEventListener("change", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_pemesanan(bulan, tahun, status, searchInput);
			}
		});
	}
	if(status != null) {
		status.addEventListener("change", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_pemesanan(bulan, tahun, status, searchInput);
			}
		});
	}
	if(searchInput != null && btnsearch != null) {
		btnsearch.addEventListener("click", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_pemesanan(bulan, tahun, status, searchInput);
			}
		});
	}

	/* update status */
	const table = document.querySelector("table.table");
	if(table != undefined) {
		table.addEventListener('click', function(e) {
			let target = e.target;
			if(target.getAttribute('id') == 'checkedStatusForAccepted') {
				e.target.nextElementSibling.click();

				if(e.target.nextElementSibling.checked == true) {
					document.querySelector("a#updateStatusBecomeAccepted").classList.add("muncul");
				} else {
					const inputCheckbox = document.querySelectorAll("input.update");
					let check = true;
					for(let i=0; i<inputCheckbox.length; i++) {
						if(inputCheckbox[i].checked == true) {
							check = true;
							break;
						} else {
							check = "remove";
						}
					};

					if(check == "remove") {
						document.querySelector("a#updateStatusBecomeAccepted").classList.remove("muncul");
					}
				}
			}
		});
	}

	$("a#updateStatusBecomeAccepted").click(function() {

		let id = [];
		let i = 0;
		$("input.update:checked").each(function() {
			id[i] = $(this).val();i++;
		});
		const tokenCSRF = document.querySelector("input#tokenCSRF").value;

		if(id.length != 0 && statusAjax.getAttribute('value') == "yes") {
			$.ajax({
				type:"POST",
				url:"_pemesanan_barang/proses.php?action=update_status_pemesanan_to_accepted",
				data:"tokenCSRF="+tokenCSRF+"&id="+id,
				beforeSend:function() {
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
					tahun.setAttribute('disabled','disabled');
					bulan.setAttribute('disabled','disabled');
					status.setAttribute('disabled','disabled');
					searchInput.setAttribute('disabled','disabled');
					statusAjax.setAttribute('value','ajax');
				},
				success:function(respon) {
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

					if(respon == 'success') {
						document.querySelector("a#updateStatusBecomeAccepted").classList.remove("muncul");
						$("input.update:checked").each(function() {
							this.previousElementSibling.text = 'diterima';
							this.previousElementSibling.removeAttribute('id');
							this.previousElementSibling.classList.replace('blue','green');
							this.previousElementSibling.classList.add('hoverNoPointer');
							this.parentElement.removeChild(this.nextElementSibling);
							this.parentElement.removeChild(this);
						});
						FlowerAlert.show('Status berhasil diperbaharui menjadi diterima!');
					} else {
						FlowerAlert.show('Status gagal diperbaharui menjadi diterima!');
					}

					bulan.removeAttribute('disabled');
					tahun.removeAttribute('disabled');
					status.removeAttribute('disabled');
					searchInput.removeAttribute('disabled');
					statusAjax.setAttribute('value','yes');

					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			});
		}
	});

	/* readMore */
	const readMore = document.querySelector("a#readMore");
	if(readMore != undefined) {
		readMore.addEventListener("click", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				const offset = document.querySelectorAll("tr.jmlPemesananTr").length;
				const totalPem = document.querySelector("td#totalPem").innerText;
				const totalQty = document.querySelector("td#totalQty").innerText;
				tampil_pemesanan(bulan, tahun, status, searchInput, 'readMore', offset, totalPem, totalQty);
			}
		});
	}

});
</script>