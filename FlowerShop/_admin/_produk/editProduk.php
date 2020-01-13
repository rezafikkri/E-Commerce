<?php
	if(!class_exists("config")) die;
	if(!class_exists("produk")) die;
	if(!class_exists("kategori")) die;
	
	$kat = new kategori;
	$db = new produk;
	if($db->cekLoginValid_halamanAdmin()) die;

	$data = $db->getOneProduk();
	$errors = config::get_form_errors();
?>

<div class="editProduk cf marginBottom65px marginTop120px">
	<div class="container">
		<div class="col-8 offset-left-2 offset-right-2">
			<form id="form" action="_produk/proses.php?action=editProduk" method="POST">
				<h3 class="judul marginBottom30px">Form tambah produk</h3>
				<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<input type="hidden" name="produk_id" value="<?= $_GET['produk_id']??''; ?>">

				<?= $errors['nama_produk']??''; ?>
				<input type="text" name="nama_produk" placeholder="Nama produk ..." value="<?= $data['nama_produk']??''; ?>">
				<?= $errors['kategori_id']??''; ?>
				<select name="kategori_id">
					<option disabled="" selected="">Kategori</option>
				<?php  
					if($kat->tampilkategori()) :
					foreach($kat->tampilkategori() as $r) :
				?>
					<option value="<?= $r['kategori_id']??''; ?>"
					<?php echo $r['kategori_id']===$data['kategori_id']?'selected':''; ?>
					><?= $r['nama_kategori']??''; ?></option>
				<?php endforeach; endif; ?>
				</select>
				<?= $errors['harga']??''; ?>
				<div class="col-6 nopadding-all">
					<span class="inputFake"><Rp>Rp</Rp> <angka id="hargaFake"></angka></span>
				</div>
				<div class="col-6 nopadding-r nopadding-t-b">
					<input type="text" id="hargaFormat" name="harga" placeholder="Harga ..." value="<?= $data['harga']??''; ?>">
				</div>
				<?= $errors['jml_stock']??''; ?>
				<input type="text" name="jml_stock" placeholder="Jumlah persediaan ..." value="<?= $data['jml_stock']??''; ?>">
				<?= $errors['urlImg']??''; ?>
				<input type="text" name="urlImg" placeholder="Url gambar ..." value="<?= $data['url_img']??''; ?>">
				<div class="conInputDropdown">
					<div class="conBtnDropdown">
						<a class="btnInputDropdown" target="gambarDropdown">Gambar lainnya <span class="fa fa-caret-right"></span></a>
					</div>
					<div id="gambarDropdown" class="InputDropdown">
						<?= $errors['urlImg1']??''; ?>
						<input type="text" name="urlImg1" placeholder="Url Gambar1 ..." value="<?= $data['url_img1']??''; ?>">
						<?= $errors['urlImg2']??''; ?>
						<input type="text" name="urlImg2" placeholder="Url Gambar2 ..." value="<?= $data['url_img2']??''; ?>">
						<?= $errors['urlImg3']??''; ?>
						<input type="text" name="urlImg3" placeholder="Url Gambar3 ..." value="<?= $data['url_img3']??''; ?>">
					</div>
				</div>
				<?= $errors['ulasanProduk']??''; ?>
				<textarea name="ulasanProduk" placeholder="Ulasan produk ..."><?= $data['ulasanProduk']??''; ?></textarea>
				<?= $errors['infoProduk']??''; ?>
				<textarea name="infoProduk" placeholder="Info produk ..."><?= $data['infoProduk']??''; ?></textarea>

				<button type="submit" class="btn2 btn2-khaki">Simpan</button>
				<a href="<?= config::base_url('_admin/index.php?ref=produkAdmin'); ?>" class="btn2"><span class="fa fa-arrow-left"></span></a>

			</form>

		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
	const hargaFake = document.querySelector("span.inputFake angka#hargaFake");
	const harga = document.querySelector('input[name="harga"]');
	if(hargaFake != undefined && harga != undefined) {
		hargaFake.innerText = format_harga(harga.value.split(",")[0], 2, ',', '.');

		harga.addEventListener('input', function(){
			harga.value = this.value;
			hargaFake.innerText = format_harga(this.value.split(",")[0], 2, ',', '.');
		});
	}
});
</script>