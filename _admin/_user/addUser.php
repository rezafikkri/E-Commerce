<?php
	if(!class_exists("config")) die;
	if(!class_exists("user")) die;
	  
	$db = new user;
	if($db->cekLoginValid_halamanAdmin()) die;
?>
<div class="container">
	<div class="col-6 offset-left-3 offset-right-3 marginTop120px marginBottom30px">
		<form id="form">
			<p class="pesan good">Silahkan masukkan data - data anda!</p>
			<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<p class="warning pesanFullName"></p>
			<input type="text" id="fullName" placeholder="Full name ...">

			<p class="warning pesanEmail"></p>
			<input type="text" id="email" placeholder="Email ...">

			<select id="level">
				<option selected="" disabled="">Level</option>
				<option value="user">User</option>
				<option value="admin">Admin</option>
			</select>
			<p class="warning pesanUsername"></p>
			<input type="text" id="username" placeholder="Username ...">

			<p class="warning pesanPassword"></p>
			<input type="password" id="password" placeholder="Password ...">
			<div class="conInputDropdown marginBottom30px">
				<div class="conBtnDropdown">
					<a class="btnInputDropdown" target="kontakDropdown">Kontak <span class="fa fa-caret-right"></span></a>
				</div>
				<div id="kontakDropdown" class="InputDropdown marginTop20px">

					<p class="warning pesanwhatsapp"></p>
					<input type="text" id="whatsapp" placeholder="Whatsapp ...">
					<select id="country">
						<option disabled="disabled" selected="">Negara</option>
					<?php 
						$country = ['singapore','indonesia'];
						foreach($country as $r) :
					?>
						<option value="<?= $r; ?>"><?= $r; ?></option>
					<?php endforeach; ?>
					</select>
					<input type="text" id="city" placeholder="Kota ...">
					<input type="text" id="subdistrict" placeholder="Kecamatan ...">
					<input type="text" id="village" placeholder="Desa/Kelurahan ...">
					<input type="text" id="zip_code" placeholder="Kode pos ...">

				</div>
			</div>

			<button type="submit" id="simpan" class="btn2 btn2-khaki"><span class="fa fa-user-plus"></span> Done</button>
			<a href="index.php?ref=user" class="btn2"><span class="fa fa-arrow-left"></span></a>
		</form>
	</div>
</div>

<script type="text/javascript">
$(function(){

	const eTarget = $(".sigup input");
	animateInput(eTarget);

	// add user
	$("button#simpan").click(function(e){
		e.preventDefault();

		const fullName = $("input#fullName").val();
		const email = $("input#email").val();
		const username = $("input#username").val();
		const password = $("input#password").val();
		const level = $("select#level").val();
		const whatsapp = $("input#whatsapp").val();
		const country = $("input#country").val();
		const city = $("input#city").val();
		const subdistrict = $("input#subdistrict").val();
		const village = $("input#village").val();
		const zip_code = $("input#zip_code").val();
		const tokenCSRF = $("input#tokenCSRF").val();

		// netral
		$(".warning").removeClass("pesan");
		$(".warning").html("");

		$.ajax({
			type:"POST",
			url:"_user/proses.php?action=addUser",
			data:{tokenCSRF:tokenCSRF, fullName:fullName ,email:email ,username:username ,password:password ,level:level ,whatsapp:whatsapp ,country:country ,city:city ,subdistrict:subdistrict ,village:village ,zip_code:zip_code},
			beforeSend:function(){
				$("div.conLoader").addClass("muncul");
				$("div.conLoader .loader").addClass("loader80");
			},
			success:function(respon) {
				document.querySelector("div.conLoader .loader").classList.replace("loader80","loader100");
				let data;
				try {
					data = JSON.parse(respon);
				}catch(e){
					FlowerAlert.show("Data gagal dimasukkan!");
				}

				if(data != undefined && data.errors != undefined) {
					$("input#password").val("");
					if(data.errors.fullName!=undefined) {
						$("p.pesanFullName").addClass("pesan");
						$("p.pesanFullName").text(data.errors.fullName);
					}
					if(data.errors.email!=undefined) {
						$("p.pesanEmail").addClass("pesan");
						$("p.pesanEmail").text(data.errors.email);
					}
					if(data.errors.username!=undefined) {
						$("p.pesanUsername").addClass("pesan");
						$("p.pesanUsername").text(data.errors.username);
					}
					if(data.errors.password!=undefined) {
						$("p.pesanPassword").addClass("pesan");
						$("p.pesanPassword").text(data.errors.password);
					}
					if(data.errors.whatsapp!=undefined) {
						$("div#kontakDropdown").addClass("muncul");
						$("p.pesanwhatsapp").addClass("pesan");
						$("p.pesanwhatsapp").text(data.errors.whatsapp);
					}
				} else if(data != undefined && data.success != undefined) {
					FlowerAlert.show("Data berhasil dimasukkan!");
					$("form#form")[0].reset();
				}

				$("div.conLoader").removeClass("muncul");
				$("div.conLoader .loader").removeClass("loader100");
			}
		})
	})

})
</script>