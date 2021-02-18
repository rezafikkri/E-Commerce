<?php 
	if(!class_exists("config")) die;
	if(!class_exists("login")) die;

	$db = new login;
	$db->cekLoginYesHalamanLogin_and_sigup();
?>
<div class="container">
	<div class="col-4 offset-left-4 offset-right-4 marginBottom200px">
		<div class="login">
			<form>
				<input type="hidden" id="tokenCSRF" value="<?php if(class_exists("config")) echo config::generate_tokenCSRF(); ?>">
				<h1>Form <span>Login</span></h1>
				<?php if(@$_GET['ket'] == 'accessDetailProdukNoLogin') : ?>
				<p class="pesan good">Agar dapat melanjutkan, kamu harus login terlebih dahulu!</p>
				<input type="hidden" id="page" value="produk">
				<?php endif; ?>
				<p class="warning pesanUsername"></p>
				<div class="inputGroup">
					<label for="username" id="usernameLabel">username</label>
					<input type="text" id="username">
				</div>
				<p class="warning pesanPassword"></p>
				<div class="inputGroup">
					<label for="password" id="passwordLabel">password</label>
					<input type="password" id="password">
				</div>
				<button type="submit" id="login" class="btn btn-login"><span class="fa fa-sign-in"></span> Masuk</button>
				<a href="index.php?ref=sigup" class="btn btn-default"><span class="fa fa-user-plus"></span></a>
			</form>
		</div>
	</div>
</div>
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	const eTarget = $(".login input");
	animateInput(eTarget);

	$("button").click(function(e){
		e.preventDefault();
		const statusAjax = document.querySelector("statusAjax");

		if(statusAjax.getAttribute("value") == "yes") {

			const username = $("input#username").val();
			const password = $("input#password").val();
			const tokenCSRF = $("input#tokenCSRF").val();
			let page = $("input#page").val();
			if(page == undefined) page = null;

			$(".warning").removeClass('pesan');
			$(".warning").html("");

			$.ajax({
				type:"POST",
				url:"sigup/proses.php?action=login",
				data:{tokenCSRF:tokenCSRF, username:username, password:password, page:page},
				beforeSend:function(){
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
					statusAjax.setAttribute("value","ajax");
				},
				success:function(respon){
					statusAjax.setAttribute("value","yes");
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");
					$("input#password").val("");

					let data;
					try{
						data = JSON.parse(respon);
					}catch(e){
						FlowerAlert.show('Ada gangguan saat memproses permintaan, cek koneksi dan ulangi kembali!');
					}
							
					if(data != undefined && data.success != undefined){
						window.location = data.success;

					} else if(data != undefined && data.usernameEmpty != undefined){
						$(".pesanUsername").addClass('pesan');
						$(".pesanUsername").html("username harus diisi!");

					} else if(data != undefined && data.passwordEmpty != undefined) {
						$(".pesanPassword").addClass('pesan');
						$(".pesanPassword").html("password harus diisi!");
						
					} else if(data != undefined && data.usernameNotFound != undefined) {
						$(".pesanUsername").addClass('pesan');
						$(".pesanUsername").html("username tidak terdaftar!");

					} else if(data != undefined && data.passwordWrong != undefined) {
						$(".pesanPassword").addClass('pesan');
						$(".pesanPassword").html("password salah!");
					}

					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			})
		}
	})

})
</script>