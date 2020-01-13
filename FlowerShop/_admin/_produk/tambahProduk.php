<?php
	if(!class_exists("config")) die;
	if(!class_exists("kategori")) die;

	$kat = new kategori;
	if($kat->cekLoginValid_halamanAdmin()) die;
?>

<div class="tambahProduk cf marginTop120px marginBottom65px">
	<div class="container">
		<div class="col-8 offset-left-2 offset-right-2">
			<form id="form">
				<h3 class="judul marginBottom30px">Tambah Produk</h3>
				<p id="pesannama_produk"></p>
				<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<input type="text" id="nama_produk" placeholder="Nama produk ...">
				<p id="pesankategori_id"></p>
				<select id="kategori_id">
					<option disabled="" selected="">Kategori</option>
				<?php  
					if($kat->tampilkategori()) :
					foreach($kat->tampilkategori() as $r) :
				?>
					<option value="<?= $r['kategori_id']; ?>"><?= $r['nama_kategori']; ?></option>
				<?php endforeach; endif; ?>
				</select>
				<p id="pesanharga"></p>
				<div class="col-6 nopadding-all">
					<span class="inputFake"><Rp>Rp</Rp> <angka id="hargaFake"></angka></span>
				</div>
				<div class="col-6 nopadding-r nopadding-t-b">
					<input type="text" id="harga" placeholder="Harga ...">
				</div>

				<p id="pesanjml_stock"></p>
				<input type="text" id="jml_stock" placeholder="Jumlah Persediaan ...">
				<p id="pesanurlImg"></p>
				<input type="text" id="urlImg" placeholder="Url Gambar ...">
				<div class="conInputDropdown">
					<div class="conBtnDropdown">
						<a class="btnInputDropdown" target="gambarDropdown">Gambar lainnya <span class="fa fa-caret-right"></span></a>
					</div>
					<div id="gambarDropdown" class="InputDropdown">
						<input type="text" id="urlImg1" placeholder="Url Gambar1 ...">
						<input type="text" id="urlImg2" placeholder="Url Gambar2 ...">
						<input type="text" id="urlImg3" placeholder="Url Gambar3 ...">
					</div>
				</div>
				<textarea placeholder="Ulasan produk"></textarea>
				<textarea placeholder="Info produk"></textarea>

				<button type="submit" class="btn2 btn2-khaki" id="simpanProduk">Simpan</button>
				<a href="<?= config::base_url('_admin/index.php?ref=produkAdmin'); ?>" class="btn2"><span class="fa fa-arrow-left"></span></a>

			</form>

		</div>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){

	// simpan produk
	$("button#simpanProduk").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute("value") == "yes") {
			const nama_produk = $("input#nama_produk").val();
			const harga = $("input#harga").val();
			const jml_stock = $("input#jml_stock").val();
			const urlImg = $("input#urlImg").val();

			const kategori_id = $("select#kategori_id").val();
			const info_produk = $("textarea#infoProduk").val();
			const ulasan_produk = $("textarea#ulasanProduk").val();
			const urlImg1 = $("input#urlImg1").val();
			const urlImg2 = $("input#urlImg2").val();
			const urlImg3 = $("input#urlImg3").val();
			const tokenCSRF = $("input#tokenCSRF").val();

			$("p.warning").removeClass("pesan");
			$("p.warning").html("");

			$.ajax({
				type:"POST",
				url:"_produk/proses.php?action=insertProduk",
				data:{tokenCSRF:tokenCSRF, nama_produk:nama_produk, kategori_id:kategori_id, harga:harga, info_produk:info_produk, ulasan_produk:ulasan_produk, jml_stock:jml_stock, urlImg:urlImg, urlImg1:urlImg1, urlImg2:urlImg2, urlImg3:urlImg3},
				beforeSend:function() {
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon) {
					statusAjax.setAttribute("value","yes");
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");
					let data;
					try {
						data = JSON.parse(respon);
					}catch(e){
						FlowerAlert.show('Data gagal diinsert!');
					}

					if(data != undefined && data.success != undefined) {
						FlowerAlert.show('Data berhasil diinsert!');
						$("form#form")[0].reset();
					} else if(data != undefined && data.errors != undefined) {
						if(data.errors.nama_produk != undefined) {
							$("p#pesannama_produk").addClass("pesan");
							$("p#pesannama_produk").addClass("warning");
							$("p#pesannama_produk").html(data.errors.nama_produk);
						}
						if(data.errors.kategori_id != undefined) {
							$("p#pesankategori_id").addClass("pesan");
							$("p#pesankategori_id").addClass("warning");
							$("p#pesankategori_id").html(data.errors.kategori_id);
						}
						if(data.errors.harga != undefined) {
							$("p#pesanharga").addClass("pesan");
							$("p#pesanharga").addClass("warning");
							$("p#pesanharga").html(data.errors.harga);
						}
						if(data.errors.jml_stock != undefined) {
							$("p#pesanjml_stock").addClass("pesan");
							$("p#pesanjml_stock").addClass("warning");
							$("p#pesanjml_stock").html(data.errors.jml_stock);
						}
						if(data.errors.urlImg != undefined) {
							$("p#pesanurlImg").addClass("pesan");
							$("p#pesanurlImg").addClass("warning");
							$("p#pesanurlImg").html(data.errors.urlImg);
						}
					}

					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			})
		}
	})

	const hargaFake = document.querySelector("span.inputFake angka#hargaFake");
	const harga = document.querySelector("input#harga");
	if(hargaFake != undefined && harga != undefined) {
		harga.addEventListener('input', function(){
			harga.value = this.value;
			hargaFake.innerText = format_harga(this.value.split(",")[0], 2, ',', '.');
		});
	}

})
</script>