<?php 
	include '../init.php';
	// run session_start
	$log = new login;
?>

<!DOCTYPE html>
<html leng="en">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title>FlowerShop</title>
		<link rel="icon" href="icon">
		
		<link rel="stylesheet" type="text/css" href="../assets/plugin/Font-awesome/css/Font-awesome.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/reset.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/flower.css">

		<script type="text/javascript" src="../assets/js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="../assets/js/jquery.easing.1.3.js"></script>
	</head>	
	<body>
		<ul class="menuHeader white noAnimate">
			<div class="container">
				<li><a href="index.php" class="logo"><img src="../assets/img/icon/web1.png"></a></li>
				<div class="pull-right conShowMobileHeader">
					<li><a id="showMobileHeader"><span class="fa fa-align-justify fa-lg"></span></a></li>
				</div>
				<div class="mobileHeader pull-right">
					<li class="liCloseMobileHeader"><a id="closeMobileHeader"><span class="fa fa-remove fa-lg"></span></a></li>

					<li><a href="<?= config::base_url('_admin/index.php?ref=kategori'); ?>">Kategori</a></li>
					<li><a href="<?= config::base_url('_admin/index.php?ref=produkAdmin'); ?>">Produk</a></li>
					<li><a href="<?= config::base_url('_admin/index.php?ref=pemesanan_barang'); ?>">Pemesanan</a></li>

					<li><a href="index.php?ref=user"><span class="fa fa-user"></span> <?= explode(" ", $_SESSION['FlowerShop']['fullName'])[0]; ?></a></li>
					<li><a class="logout" href="../sigup/logout.php"><span class="fa fa-sign-out"></span></a></li>
				</div>
			</div>
		</ul>

		<div class="conLoader">
			<div class="loader"></div>
		</div>

		<Reza class="container-content">
		<?php 

		// page
		config::page("_home/home.php",filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_STRING));

		?>
		</Reza>

		<!-- javascript flower -->
		<script type="text/javascript" src="<?= config::base_url('assets/js/Flower.js'); ?>"></script>
	</body>
</html>