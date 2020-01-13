<?php
	if(!class_exists("config")) die;
	if(!class_exists("user")) die;
	  
	$user = new user;
	if($user->cekLoginValid_halamanAdmin()) die;
	$r = $user->getOneUser(filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_STRING));
	$errors = config::get_form_errors();
?>
<div class="container">
	<div class="col-6 offset-left-3 offset-right-3 marginTop120px marginBottom30px">
		<form id="form" action="_user/proses.php?action=editUser" method="post">
			<p class="pesan good">Silahkan masukkan data - data anda!</p>
			<?= $user->pesanEditAccount(); ?>
			<input type="hidden" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<input type="hidden" name="user_id" value="<?= $r['user_id']; ?>">

			<?= $errors['full_name']??''; ?>
			<input type="text" name="full_name" placeholder="Full name ..." value="<?= $r['full_name']; ?>">

			<?= $errors['email']??''; ?>
			<input type="text" name="email" placeholder="Email ..." value="<?= $r['email']; ?>">
			<select name="level">
				<option selected="" disabled="">Level</option>

				<?php foreach(['user','admin'] as $l) : ?>
				<option value="<?= $l; ?>"
				<?php echo $l==$r['level']?'selected':''; ?>
				><?= $l; ?></option>
				<?php endforeach; ?>

			</select>
			<?= $errors['username']??''; ?>
			<input type="text" name="username" placeholder="Username ..." value="<?= $r['username']; ?>">
			<input type="password" name="password" placeholder="Password ...">

			<div class="conInputDropdown marginBottom30px">
				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="kontakDropdown">Kontak <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="kontakDropdown" class="InputDropdown <?php if($errors['whatsapp']??false) echo "muncul"; ?> marginTop20px">

					<?= $errors['whatsapp']??''; ?>
					<input type="text" name="whatsapp" placeholder="Whatsapp ..." value="<?= $r['whatsapp']; ?>">
					<select name="country">
						<option disabled="disabled" selected="">Negara</option>
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
					<input type="text" name="city" placeholder="Kota ...">
					<input type="text" name="subdistrict" placeholder="Kecamatan ...">
					<input type="text" name="village" placeholder="Desa/Kelurahan ...">
					<input type="text" name="zip_code" placeholder="Kode pos ...">

				</div>
			</div>

			<button type="submit" class="btn2 btn2-khaki"><span class="fa fa-user-plus"></span> Done</button>
			<a href="index.php?ref=user" class="btn2"><span class="fa fa-arrow-left"></span></a>
		</form>
	</div>
</div>