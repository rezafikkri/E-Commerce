<?php  
	if(!class_exists("config")) die;
	if(!class_exists("produk")) die;
?>
<!-- tagLine -->
<div class="tagLine cf" id="tagLine">
	<div class="col-6 nopadding-all imgBackground" title="Foto oleh Shubham Shrivastava di Unsplash">
	</div>
	<div class="col-6 text">
		<h1>Mencari bunga?</h1>
		<h3>Kamu tidak salah</h3>
		<h2>Disini tempat-nya semua <span>Bunga</span></h2>
		<a href="#about" id="toAbout" class="btn displayInlineBlock btn-default">Selengkapnya &raquo;</a>
	</div>
</div>

<!-- about -->
<div class="about cf"  id="about">
	<div class="container">
		<div class="col-12"><h1>Tentang</h1></div>
		<div class="col-6 offset-left-2">
			<div id="right">
				<p class="web">Website ini adalah tempat membeli bunga, semua bunga ada disini. Melayani anda 24 jam. website ini dibuat dengan latar belakang, teknologi belakangan ini sudah semakin canggih dan sudah merabah ke dunia industri, demi kelangsungan usaha kami buat website ini dengan tujuan bisa mencapai pasar yang lebih luas, dan dengan pelayanan yang lebih baik, serta akses yang mudah, karena kamu tidak perlu pergi kemana-mana, tinggal duduk dan buka laptop atau handphone.</p>
			</div>
		</div>
		<div class="col-4">
			<div id="left">
				<ul class="contact">
					<li><span class="fa fa-facebook-square fa-lg tealFb"></span> fikkri.reza</li>
					<li><span class="fa fa-whatsapp fa-lg greenWa"></span> +6285387857788</li>
					<li><span class="fa fa-instagram fa-lg orangeIg"></span> fikkri.reza</li>
				</ul>
			</div>
		</div>
		<div class="col-2 offset-left-5 offset-right-5">
			<a href="#produk" id="toProduk">
				<div class="chrevDown">
					<span class="fa fa-angle-double-down"></span>
				</div>
			</a>
		</div>
	</div>
</div>

<!-- produk -->
<div id="produk" class="produk marginBottom65px">
	<h3 class="judul">Produk Populer</h3>
	<div class="divProduk marginTop20px">
		<div class="container">
			
			<div class="tampilProduk cf">
			<?php
			    $produk = new produk;
			    $data = $produk->tampilProduk(null,null,null,6,0,'orderby');
				if($data) :
				foreach($data as $r) :
			?>
			<div class="col-4">
				<div class="detailProduk">
					<div class="img">
						<img class="lazy" data-src="<?= $r['url_img']; ?>" alt="">
					</div>
					<div class="badgePlus">
						<a href='<?= config::base_url("index.php?ref=detail_produk&produk_id=$r[produk_id]"); ?>'><span class="fa fa-external-link"></span></a>
					</div>
					<div class="description">
						<a href="<?= config::base_url('index.php?ref=produk'); ?>" class="noBtn">
							<h2><?= $r['nama_produk']; ?></h2>
							<p class="harga">Rp <?= $r['harga']; ?></p>
							<p class="stock"><?= $r['jml_stock']; ?> Persediaan</p>
							<p class="sale"><span class="fa fa-shopping-basket"></span> <?= $r['sale']; ?></p>
						</a>				
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			</div>

		</div><!-- container -->
	</div><!-- divProduk -->
</div>
