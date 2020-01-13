<?php
	if(!class_exists("config")) die;
	if(!class_exists("kategori")) die;

	$kat = new kategori;
	if($kat->cekLoginValid_halamanAdmin()) die;
?>
<div class="kategori marginTop120px">
	<div class="container">
		<div class="col-6 offset-left-3 offset-right-3">
			<h3 class="judul marginBottom30px">Tambah kategori</h3>
			<form id="form">
				<p class="pesanKategori"></p>
				<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<input type="text" id="kategori" placeholder="Kategori ...">
				<button type="submit" class="btn2 btn2-khaki" id="simpanKategori">Simpan</button>
				<a class="btn2" href="<?= config::base_url('_admin/index.php?ref=kategori'); ?>"><span class="fa fa-arrow-left"></span></a>
			</form>
		</div>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function() {
	// simpan kategori
	$("button#simpanKategori").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute('value') == "yes") {

			$("p.pesanKategori").removeClass("pesan");
			$("p.pesanKategori").html("");

			const nama_kategori = $("input#kategori").val();
			const tokenCSRF = $("input#tokenCSRF").val();
			$.ajax({
				type:"POST",
				url:"_kategori/proses.php?action=insertKategori",
				data:{tokenCSRF:tokenCSRF, nama_kategori:nama_kategori},
				beforeSend:function() {
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
					statusAjax.setAttribute("value","ajax");
				},
				success:function(html){
					statusAjax.setAttribute("value","yes");
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

					let data;
					try {
						data = JSON.parse(html);
					}catch(e){}

					if(data != undefined && data.success != undefined) {
						FlowerAlert.show("Data berhasil diinsert!");
						$("#form")[0].reset();	
					} else if(data != undefined && data.errors != undefined) {
						$("p.pesanKategori").html(data.errors.nama_kategori);
					} else {
						FlowerAlert.show("Data gagal diinsert!");
					}

					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			})
		}
		
	})
});
</script>