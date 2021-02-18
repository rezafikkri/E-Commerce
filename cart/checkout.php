<?php  
if(!class_exists("config")) die;

if(isset($_SESSION['FlowerShop']['userLogin']) && isset($_SESSION['FlowerShop']['cart']) && count($_SESSION['FlowerShop']['cart']) > 0) :

	$user = new user;
	$transaksi = new transaksi;
	$user_id = $_SESSION['FlowerShop']['userId'];
	$r = $user->getOneUser($user_id);

	$error = config::get_form_errors();
	$old = config::get_old_value();
?>
<div class="checkout cf marginBottom200px">
	<div class="container">
		<h3 class="judul">Pembayaran</h3>
		<div class="col-10 offset-left-1 offset-right-1 cf">
			<form id="form" method="post" action="<?= config::base_url('cart/proses.php?action=checkout'); ?>">
				<?= $transaksi->pesan_checkout(); ?>
				<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<div class="col-6 nopadding-all">
					<input disabled="" type="text" name="whatsapp" value="<?= $r['whatsapp']; ?>">
					<input disabled="" type="text" name="fullName" value="<?= $_SESSION['FlowerShop']['fullName']; ?>">
					<?= $error['country']??''; ?>
					<select name="country">
						<option disabled="" selected="">Negara</option>
						<?php 
							$country = ['singapore','indonesia']; 
							foreach($country as $c) :
						?>
						<option value="<?= $c; ?>"
						<?php 
							if($c == strtolower($old['country']??'')) echo "selected";
							elseif($c == strtolower($r['country'])) echo "selected";
						?>
						><?= $c; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-6 nopadding-t-b nopadding-r">
					<?= $error['city']??''; ?>
					<input type="text" name="city" placeholder="Kota ..." value="<?= $old['city']??$r['city']; ?>">

					<?= $error['subdistrict']??''; ?>
					<input type="text" name="subdistrict" placeholder="Kecamatan ..." value="<?= $old['subdistrict']??$r['subdistrict']; ?>">

					<?= $error['village']??''; ?>
					<input type="text" name="village" placeholder="Kelurahan ..." value="<?= $old['village']??$r['village']; ?>">
				</div>
				<div class="col-6 offset-left-3 offset-right-3 nopadding-all">
					<?= $error['zip_code']??''; ?>
					<input type="text" name="zip_code" placeholder="zip_code ..." value="<?= $old['zip_code']??$r['zip_code']; ?>">
				</div>
				<div class="col-12">
					<center>
						<button type="submit" class="btn2 btn-default marginTop20px">Beli</button>
						<a href="<?= config::base_url('index.php?ref=cart'); ?>" class="btn2 btn-default"><span class="fa fa-arrow-left"></span></a>
					</center>
				</div>
			</form>
		</div>
	</div>
</div>
<?php elseif(class_exists("config")) : header("Location: ".config::base_url('index.php?ref=produk')); ?>
<?php endif; ?>
