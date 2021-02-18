<?php
	if(!class_exists("config")) die;
	if(!class_exists("user")) die;

	$user = new user;
	if($user->cekLoginNo_HalamanUser()) die;
	$r = $user->getOneUser($_SESSION['FlowerShop']['userId']??'');
	$errors = config::get_form_errors();
?>
<div class="container">
	<div class="sigup col-6 offset-left-3 offset-right-3 marginBottom200px">
		<form action="sigup/proses.php?action=editAccount" method="post">
			<input type="hidden" id="tokenCSRF" name="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<p class="pesan good">Perincian Pengguna</p>
			
			<?= $user->pesanEditAccount(); ?>

			<?= $errors['full_name']??''; ?>
			<div class="inputGroup">
				<label for="fullName" id="fullNameLabel">Nama lengkap</label>
				<input type="text" id="fullName" name="full_name" value="<?= $r['full_name']; ?>">
			</div>
			<?= $errors['email']??''; ?>
			<div class="inputGroup">
				<label for="email" id="emailLabel">email</label>
				<input type="text" id="email" name="email" value="<?= $r['email']; ?>">
			</div>
			<?= $errors['username']??''; ?>
			<div class="inputGroup">
				<label for="username" id="usernameLabel">username</label>
				<input type="text" id="username" name="username" value="<?= $r['username']; ?>">
			</div>
			<?= $errors['password']??''; ?>
			<div class="inputGroup">
				<label for="password" id="passwordLabel">password</label>
				<input type="password" id="password" name="password">
			</div>

			<div class="conInputDropdown">

				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="kontakDropdown">Kontak <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="kontakDropdown" class="InputDropdown marginTop20px <?php if($errors['whatsapp']) echo "muncul"; ?>">

					<?= $errors['whatsapp']??''; ?>
					<div class="inputGroup">
						<label for="whatsapp" id="whatsappLabel">Whatsapp</label>
						<input type="text" name="whatsapp" id="whatsapp" value="<?= $r['whatsapp']; ?>">
					</div>
					<div class="inputGroup">
						<label for="country" id="countryLabel">Negara</label>
						<input type="text" name="country" id="country" value="<?= $r['country']; ?>">
					</div>
					<div class="inputGroup">
						<label for="city" id="cityLabel">Kota</label>
						<input type="text" name="city" id="city" value="<?= $r['city']; ?>">
					</div>
					<div class="inputGroup">
						<label for="subdistrict" id="subdistrictLabel">Kecamatan</label>
						<input type="text" id="subdistrict" name="subdistrict" value="<?= $r['subdistrict']; ?>">
					</div>
					<div class="inputGroup">
						<label for="village" id="villageLabel">Kelurahan/Desa</label>
						<input type="text" id="village" name="village" value="<?= $r['village']; ?>">
					</div>
					<div class="inputGroup">
						<label for="zip_code" id="zip_codeLabel">Kode Pos</label>
						<input type="text" id="zip_code" name="zip_code" value="<?= $r['zip_code']; ?>">
					</div>

				</div>
				<!-- simpan -->
				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="simpanDropdown">Simpan <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="simpanDropdown" class="InputDropdown">
					<button type="submit" class="btn2 btn2-khaki"><span class="fa fa-user-plus"></span> Done</button>
				</div>
				<!-- remove account -->
				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="removeAccount">Hapus Akun <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="removeAccount" class="InputDropdown">
					<a id="DeleteAccountpart1" class="btn2 btn2-red">Hapus <span class="fa fa-remove"></span></a>
				</div>

				<div class="FlowerModal_bg"></div>
				<div class="FlowerModal">
					<p class="pesan modal">Apakah kamu yakin! masukkan password!</p>
					<p class="warning pesanPasswordDelete"></p>
					<div class="inputGroup">
						<label for="passwordDelete" id="passwordDeleteLabel">Password</label>
						<input type="hidden" id="idUserDelete" value="<?= $r['user_id']; ?>">
						<input type="text" id="passwordDelete">
						<a id="DeleteAccountpart2" class="btn2 btn2-red">Hapus <span class="fa fa-remove"></span></a>
						<a id="closeModalDeleteAccount" class="btn2 btn2-khaki">Keluar <span class="fa fa-remove"></span></a>
					</div>
				</div>

			</div><!-- conInputDropdown -->
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
	$(function(){

		const eTarget = $(".sigup input");
		animateInput(eTarget);

		// delete account
		$("a#DeleteAccountpart1").click(function(){
			$("div.FlowerModal_bg").fadeIn();
			$("div.FlowerModal").fadeIn();
		})

		$("a#closeModalDeleteAccount").click(function(){
			$("div.FlowerModal_bg").fadeOut();
			$("div.FlowerModal").fadeOut();
			$("p.pesanPasswordDelete").html("");
			$("input#passwordDelete").val("");
			$("label#passwordDeleteLabel").removeClass("focus");
		})

		$("a#DeleteAccountpart2").click(function(){
			const statusAjax = document.querySelector("statusAjax");

			if(statusAjax.getAttribute("value") == "yes") {
				const passwordDelete = $("input#passwordDelete").val();
				const tokenCSRF = $("input#tokenCSRF").val();
				// netral
				$("p.warning").removeClass("pesan");
				$("p.warning").html("");

				$.ajax({
					type:"POST",
					url:"sigup/proses.php?action=deleteAccount",
					data:{tokenCSRF:tokenCSRF, password:passwordDelete},
					beforeSend:function() {
						$("div.conLoader").addClass("muncul");
						$("div.conLoader .loader").addClass("loader80");
						statusAjax.setAttribute("value","ajax");
					},
					success:function(respon) {
						statusAjax.setAttribute("value","yes");
						document.querySelector("div.conLoader .loader").classList.replace("loader80","loader100");

						let data;
						try {
							data = JSON.parse(respon);
						} catch(e){
							$("div.FlowerModal_bg").fadeOut();
							$("div.FlowerModal").fadeOut();
							FlowerAlert.show('Data gagal didelete');
						}

						if(data != undefined && data.success != undefined) {
							$("div.FlowerModal_bg").fadeOut();
							$("div.FlowerModal").fadeOut();
							FlowerAlert.show('Data berhasil didelete','',true);

						} else if(data != undefined && data.error != undefined && data.error == 'passwordSalah') {
							$("p.pesanPasswordDelete").addClass("pesan");
							$("p.pesanPasswordDelete").html('Password salah');
						}

						$("div.conLoader").removeClass("muncul");
						$("div.conLoader .loader").removeClass("loader100");
					}
				})
			}
		})
	})
</script>