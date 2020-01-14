<?php
	if(!class_exists("config")) die;
	if(!class_exists("login")) die;

	$db = new login;
	$db->cekLoginYesHalamanLogin_and_sigup();
?>
<div class="container">
	<div class="sigup col-6 offset-left-3 offset-right-3 marginBottom200px">
		<form id="reset">
			<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<p class="pesan good">Silahkan masukkan data - data anda!</p>
			<p class="warning pesanfullName"></p>
			<div class="inputGroup">
				<label for="fullName" id="fullNameLabel">Nama lengkap</label>
				<input type="text" id="fullName">
			</div>
			<p class="warning pesanemail"></p>
			<div class="inputGroup">
				<label for="email" id="emailLabel">email</label>
				<input type="text" id="email">
			</div>
			<p class="warning pesanusername"></p>
			<div class="inputGroup">
				<label for="username" id="usernameLabel">username</label>
				<input type="text" id="username">
			</div>
			<p class="warning pesanpassword"></p>
			<div class="inputGroup">
				<label for="password" id="passwordLabel">password</label>
				<input type="password" id="password">
			</div>

			<div class="conInputDropdown marginBottom65px">
				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="kontakDropdown">Kontak <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="kontakDropdown" class="InputDropdown marginTop20px">

					<p class="warning pesanwhatsapp"></p>
					<div class="inputGroup">
						<label for="whatsapp" id="whatsappLabel">Whatsapp</label>
						<input type="text" id="whatsapp">
					</div>
					<div class="inputGroup">
						<label for="country" id="countryLabel">Negara</label>
						<input type="text" id="country">
					</div>
					<div class="inputGroup">
						<label for="city" id="cityLabel">Kota</label>
						<input type="text" id="city">
					</div>
					<div class="inputGroup">
						<label for="subdistrict" id="subdistrictLabel">Kecamatan</label>
						<input type="text" id="subdistrict">
					</div>
					<div class="inputGroup">
						<label for="village" id="villageLabel">Kelurahan/Desa</label>
						<input type="text" id="village">
					</div>
					<div class="inputGroup">
						<label for="zip_code" id="zip_codeLabel">Kode Pos</label>
						<input type="text" id="zip_code">
					</div>

				</div>
			</div>

			<button type="submit" id="simpan" class="btn btn-login"><span class="fa fa-user-plus"></span> Daftar</button>
			<a href="index.php?ref=login" class="btn btn-default"><span class="fa fa-sign-in"></span> Masuk</a>
		</form>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
	$(function(){

		const eTarget = $(".sigup input");
		animateInput(eTarget);

		// add user
		$("button#simpan").click(function(e){
			e.preventDefault();
			const statusAjax = document.querySelector("statusAjax");

			if(statusAjax.getAttribute("value") == "yes") {

				const fullName = $("input#fullName").val();
				const email = $("input#email").val();
				const username = $("input#username").val();
				const password = $("input#password").val();
				const whatsapp = $("input#whatsapp").val();
				const country = $("input#country").val();
				const city = $("input#city").val();
				const subdistrict = $("input#subdistrict").val();
				const village = $("input#village").val();
				const zip_code = $("input#zip_code").val();
				const tokenCSRF = $('input#tokenCSRF').val();

				// netral
				$(".warning").removeClass("pesan");
				$(".warning").html("");

				$.ajax({
					type:"POST",
					url:"sigup/proses.php?action=sigup",
					data:{level:'admin', tokenCSRF:tokenCSRF, fullName:fullName, password:password, email:email, username:username, password:password ,whatsapp:whatsapp, country:country, city:city,subdistrict:subdistrict,village:village,zip_code:zip_code},
					beforeSend:function(){
						$("div.conLoader").addClass("muncul");
						$("div.conLoader .loader").addClass("loader90");
						statusAjax.setAttribute("value","ajax");
					},
					success:function(respon) {
						statusAjax.setAttribute("value","yes");
						document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

						let data;
						try {
							data = JSON.parse(respon);
						} catch(e){
							FlowerAlert.show('Ada gangguan saat memproses permintaan, cek koneksi dan ulangi kembali!');
						}

						if(data != undefined && data.errors != undefined) {
							let error = data.errors;
							// reset password
							$("input#password").val("");

							if(error.fullName != undefined) {
								$("p.pesanfullName").addClass("pesan");
								$("p.pesanfullName").text(error.fullName);
							}
							if(error.username != undefined) {
								$("p.pesanusername").addClass("pesan");
								$("p.pesanusername").text(error.username);
							}
							if(error.password != undefined) {
								$("p.pesanpassword").addClass("pesan");
								$("p.pesanpassword").text(error.password);
							}
							if(error.email != undefined) {
								$("p.pesanemail").addClass("pesan");
								$("p.pesanemail").text(error.email);
							}
							if(error.whatsapp != undefined) {
								$("div#kontakDropdown").addClass("muncul");
								$("p.pesanwhatsapp").addClass("pesan");
								$("p.pesanwhatsapp").text(error.whatsapp);
							}

						} else if(data != undefined && data.success != undefined) {
							$("div#kontakDropdown").removeClass("muncul");
							FlowerAlert.show('Selamat kamu telah berhasil mendaftar!',data.success);
							$("form#reset")[0].reset();
						}

						$("div.conLoader").removeClass("muncul");
						$("div.conLoader .loader").removeClass("loader100");
					}
				});
			}
		})

	})
</script>