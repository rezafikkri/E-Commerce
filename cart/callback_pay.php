<?php 
	if(!class_exists("config")) die;

	if(isset($_SESSION['FlowerShop']['callback_pay'])) :
	unset($_SESSION['FlowerShop']['callback_pay']);
?>
<div class="bg-callbackPay"></div>
<div class="callbackPay cf marginBottom300px">
	<div class="container">
		<div class="col-6 offset-left-3 offset-right-3">
			<img class="lazy" data-src="<?= config::base_url('assets/img/icon/delivery.png'); ?>" alt="">
			<h3><span>Terima kasih</span> untuk pembelian-mu</h3>
			<p>Pesananmu sedang dalam perjalanan &raquo; <span><?= $_SESSION['FlowerShop']['fullName']; ?></span></p>
		</div>
	</div>
</div>
<?php else : header("Location: ".config::base_url('index.php?ref=produk')); ?>
<?php endif; ?>