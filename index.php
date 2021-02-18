<?php 
	include 'init.php';
	
	$cart = new cart;
	$kategori = new kategori;
	$jmlProduk = $cart->setSessionCart_If_HasTransaksiOnDatabase_and_notThereSessionCart();
?>

<!DOCTYPE html>
<html leng="en">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<meta name="author" content="Reza Sariful Fikkri">
		<title>FlowerShop</title>
		<link rel="icon" href="icon">
		
		<link rel="stylesheet" type="text/css" href="assets/plugin/Font-awesome/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="assets/css/reset.css">
		<link rel="stylesheet" type="text/css" href="assets/css/flower.css">

		<script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="assets/js/jquery.easing.1.3.js"></script>
	</head>	
	<body>
		<ul class="menuHeader <?php if(!empty(@$_GET['ref'])) echo "white noAnimate"; ?>">
			<div class="container">
				<li><a href="<?=
						(!empty($_GET['ref']??''))? config::base_url() : "#tagLine";
				?>" class="logo to"><img src="assets/img/icon/web1.png"></a></li>

				<div class="pull-right conShowMobileHeader">
					<li><a id="showMobileHeader"><span class="fa fa-align-justify fa-lg"></span></a></li>
				</div>

				<div class="pull-right">
					<li><a href="<?= config::base_url('index.php?ref=cart'); ?>" class="shopping-cart"><span class="fa fa-shopping-cart"></span><span class="badgeJml <?= $jmlProduk['class']; ?>"><?= $jmlProduk['jmlProduk']; ?></span></a></li>
				</div>

				<div class="mobileHeader pull-right">
					<li class="liCloseMobileHeader"><a id="closeMobileHeader"><span class="fa fa-remove fa-lg"></span></a></li>
					<?php if(@$_GET['ref'] === 'produk') : ?>
					<li><a class="kategori btnDropdown" dropdown="kategori">Kategori <span class="fa fa-caret-down"></span></a>
						<ul class="menuDropdown conBtnTampilProdukAjax" id="kategori">
						<?php 
							$dataKategori = $kategori->tampilkategori();
							if($dataKategori) :
							foreach($dataKategori as $r) : 
						?>
							<li><a class="btnTampilProdukAjax" kategori_id="<?= $r['kategori_id']; ?>" nama_kategori="<?= $r['nama_kategori']; ?>"><?= $r['nama_kategori']; ?></a></li>
						<?php endforeach; endif; ?>
						</ul>
					</li>
					<?php endif; ?>

					<?php if(empty(trim(@$_GET['ref']))) : ?>
					<li><a href="#about" class="to">Tentang</a></li>
					<?php endif ?>
					
					<li><a href="<?= config::base_url('index.php?ref=produk'); ?>">Produk</a></li>

					<?php 
						if(@$_GET['ref'] != "login" && @$_GET['ref'] != "sigup") : 
						if(!isset($_SESSION['FlowerShop']['userLogin'])) :
					?>
					<li><a href="index.php?ref=login"><span class="fa fa-sign-in"></span></a></li>
					<li><a href="index.php?ref=sigup"><span class="fa fa-user-plus"></span></a></li>
					<?php else : ?>

					<li><a href="index.php?ref=editAccount"><span class="fa fa-user"></span> <?= explode(" ", $_SESSION['FlowerShop']['fullName'])[0]; ?></a></li>
					<li><a class="logout" href="sigup/logout.php"><span class="fa fa-sign-out"></span></a></li>
					<?php endif; endif; ?>
				</div>
			</div>
		</ul>

		<div class="conLoader">
			<div class="loader"></div>
		</div>

		<reza class="container-content">
		<?php 

		// page
		config::page("home/home.php",filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_STRING));

		?>
		</reza>

		<footer class="footer">
			<p class="copyright">&copy; 2019 - Reza</p>
		</footer>
		<!-- javascript flower -->
		<script type="text/javascript" src="assets/js/Flower.js"></script>
		<script type="text/javascript" src="assets/js/yall.min.js"></script>
		<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", yall());
		</script>
	</body>
</html>
