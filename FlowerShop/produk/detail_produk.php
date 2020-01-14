<?php  
	if(!class_exists("config")) die;
	if(!class_exists("produk")) die;
?>

<div class="detail_produk cf marginBottom200px">
	<?php
		$produk = new produk;
		if($produk->cekLoginNo_halamanDetailProduk()) die;
		$r = $produk->tampilDetailProduk();
	?>
	<div class="keteranganProduk cf">
		<div class="container">
			<div class="col-5 conThumbnails">
				<div class="col-12 nopadding-all fotoProduk">
					<img data-src="<?= $r['url_img']; ?>" class="lazy thumbnail" alt="">
				</div>
				<?php if($r['url_img1']) : ?>
				<div class="col-4 nopadding-all fotoProdukOther">
					<img data-src="<?= $r['url_img1']??''; ?>" class="lazy thumbnail small" alt="">
				</div>
				<?php endif; if($r['url_img2']) : ?>
				<div class="col-4 nopadding-all fotoProdukOther">
					<img data-src="<?= $r['url_img2']??''; ?>" class="lazy thumbnail small" alt="">
				</div>
				<?php endif; if($r['url_img3']) : ?>
				<div class="col-4 nopadding-all fotoProdukOther">
					<img data-src="<?= $r['url_img3']??''; ?>" class="lazy thumbnail small" alt="">
				</div>
				<?php endif; ?>
			</div>
			<div class="col-7">
				<div class="text">
					<h1><?= $r['nama_produk']; ?></h1>
					<p class="harga">Rp <?= config::generate_hargaFormat($r['harga']); ?></p>
					<p class="stock"><?= $r['jml_stock']; ?> Persediaan</p>
					<p class="pesanProduk">Kontak : </p>
					<ul>
						<li><span class="fa fa-whatsapp whatsapp"></span> <?= $r['whatsapp']; ?></li>
						<li><span class="fa fa-envelope-o envelope"></span> <?= $r['email']; ?></li>
					</ul>
				</div>
				<div class="formAction marginTop20px">
					<form id="form">
						<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
						<input type="hidden" id="produk_id" value="<?= $r['produk_id']; ?>">
						<div class="quantity cf">
							<input type="number" id="inputQuantity" placeholder="Jumlah ..." max="<?= $r['jml_stock']; ?>">
						</div>
					</form>
					<a id="addToCart" class="btn2 btn2-default">Tambah ke Keranjang</a>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="col-12">
			<div class="col-6 nopadding-all InformasiProduk muncul">
				<h3>Info</h3>
				<p><?= $r['infoProduk']; ?></p>
			</div>
			<div class="col-6 nopadding-all ulasanProduk">
				<h3>Ulasan</h3>
				<p><?= $r['ulasanProduk']; ?></p>
			</div>
		</div>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
	$(function(){

		const addToCart = document.querySelector("a#addToCart");
		addToCart.addEventListener('click',function(e){
			e.preventDefault();
			const statusAjax = document.querySelector("statusAjax");
			if(statusAjax.getAttribute("value") == "yes") {

				const qty = document.querySelector("input#inputQuantity").value;
				const produk_id = document.querySelector("input#produk_id").value;
				const tokenCSRF = document.querySelector("input#tokenCSRF").value;

				if(qty > 0) {
					$.ajax({
						type:"POST",
						url:"cart/proses.php?action=addCart",
						data:{tokenCSRF:tokenCSRF, qty:qty, produk_id:produk_id},
						beforeSend:function(){
							$("div.conLoader").addClass("muncul");
							$("div.conLoader .loader").addClass("loader90");
							statusAjax.setAttribute("value","ajax");
						},
						success:function(response){
							statusAjax.setAttribute("value","yes");
							document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

							let data;
							try {
								data = JSON.parse(response);
							} catch(e) {
								FlowerAlert.show('Ada gangguan saat memproses permintaan, cek koneksi dan ulangi kembali!');
							}

							console.log(data);

							/* proses data */
							if(data != undefined && data.jmlorderLebihDariStock != undefined) {

								FlowerAlert.show('Jumlah barang yang dimasukkan melebihi stock yang ada. Reload halaman untuk melihat stock saat ini!');
							} else if(data != undefined && data.success != undefined) {
								let sisaStock = data.sisaStock;
								let jmlProduk = data.jmlProduk;

								document.querySelector("input#inputQuantity").setAttribute("max",sisaStock);
								document.querySelector("p.stock").innerText = sisaStock+" stock";
								document.querySelector(".shopping-cart span.badgeJml").innerText = jmlProduk;
								document.querySelector(".shopping-cart span.badgeJml").classList.add("muncul");
							}
							
							$("form#form")[0].reset();
							$(".conLoader").removeClass("muncul");
							$(".loader").removeClass('loader100');
						}
					});
				}
			}
		});
	});
</script>