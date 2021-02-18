<?php
	if(!class_exists("config")) die;
	if(!class_exists("user")) die;
	  
	$user = new user;
	if($user->cekLoginValid_halamanAdmin()) die;
?>
<div class="user marginTop120px">
	<div class="container">
		<div class="col-12">
			<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
			<a id="deleteUser" class="btn2 btn2-red deleteKategori"><span class="fa fa-trash-o fa-lg"></span></a>
			<a href="index.php?ref=addUser" class="btn2 green"><span class="fa fa-database"></span></a>
		</div>
		<div class="col-12">
			<table class="table">
				<tr>
					<th width="10"></th>
					<th>Username</th>
					<th>Full name</th>
					<th width="300">Level</th>
					<th width="10"></th>
				</tr>
				<?php  
					$data = $user->tampilUser();
					if($data) :
					$no = 1;
					foreach($data as $r) :
				?>
				<tr>
					
					<td>
					<?php if($r['level'] != 'admin') : ?>
						<input type="checkbox" name="hapus[]" class="hapus" id="hapus<?= $no; ?>" value="<?= $r['user_id']; ?>">
						<label for="hapus<?= $no; ?>"></label>
					<?php endif; ?>
					</td>

					<td><?= $r['username']; ?></td>
					<td><?= $r['full_name']; ?></td>
					<td><?= $r['level']; ?></td>
					<td class="center">
						<a href="index.php?ref=editUser&user_id=<?= $r['user_id']; ?>"><span class="fa fa-edit fa-lg"></span></a>
					</td>
				</tr>
				<?php $no++; endforeach; endif; ?>
			</table>
		</div>
	</div>
</div>
<statusAjax	value="yes">
<script type="text/javascript">
	$(function(){
		// delete user
		$("a#deleteUser").click(function(){
			let i = 0;
			let user_id = [];
			$("input.hapus:checked").each(function(){
				user_id[i]=$(this).val();i++;
			})
			const tokenCSRF = $("input#tokenCSRF").val();
			const statusAjax = document.querySelector("statusAjax");

			if(statusAjax.getAttribute("value") == "yes") {
				$.ajax({
					type:"POST",
					url:"_user/proses.php?action=deleteUser",
					data:"tokenCSRF="+tokenCSRF+"&user_id="+user_id,
					beforeSend:function(){
						$("div.conLoader").addClass("muncul");
						$("div.conLoader .loader").addClass("loader90");
						statusAjax.setAttribute("value","ajax");
					},
					success:function(respon){
						statusAjax.setAttribute("value","yes");
						document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

						if(respon == "success") {
							$("input.hapus:checked").each(function(){
								$(this).parent().parent().remove(".hapus").animate({ opacity: "hide" }, "slow");
							})
						} else if(respon == "dataNull") {
							FlowerAlert.show('Mohon pilih data yang ingin didelete!');

						} else {
							FlowerAlert.show('Data gagal didelete!');
						}

						$("div.conLoader").removeClass("muncul");
							$("div.conLoader .loader").removeClass("loader100");
					}

				})
			}
		})
	})
</script>