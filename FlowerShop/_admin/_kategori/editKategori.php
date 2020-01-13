<?php  
	if(!class_exists("config")) die;
	if(!class_exists("kategori")) die;

	$db = new kategori;
	if($db->cekLoginValid_halamanAdmin()) die;
	$errors = config::get_form_errors();
?>
<div class="kategori marginTop120px">
	<div class="container">
		<div class="col-6 offset-left-3 offset-right-3">
			<h3 class="judul marginBottom30px">Edit kategori</h3>
			<form id="form" action="_kategori/proses.php?action=editKategori" method="POST">
				<?= $errors['nama_kategori']??''; ?>
				<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<input type="hidden" name="kategori_id" value="<?= @$_GET['kategori_id']; ?>">
				<input type="text" id="kategori" name="nama_kategori" value="<?= $db->getOneKategori()['nama_kategori']??''; ?>" placeholder="Kategori ...">

				<button type="submit" class="btn2 btn2-khaki" id="simpanKategori">Simpan</button>
				<a href="index.php?ref=kategori" class="btn2"><span class="fa fa-arrow-left"></span></a>
			</form>
		</div>
	</div>
</div>
