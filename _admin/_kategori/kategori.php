<?php
	if(!class_exists("config")) die;
	if(!class_exists("kategori")) die;

	$db = new kategori;
	if($db->cekLoginValid_halamanAdmin()) die;
?>
<div class="kategori marginTop120px marginBottom100px cf">
	<div class="container">
		<div class="col-8 offset-left-2 offset-right-2">
			<div class="col-12 nopadding-r-l marginBottom10px">
				<a id="deleteKategori" class="btn2 btn2-red deleteKategori"><span class="fa fa-trash-o fa-lg"></span></a>

				<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
				<a href="<?= config::base_url('_admin/index.php?ref=addKategori'); ?>" class="btn2 btn2-khaki deleteKategori"><span class="fa fa-database fa-lg"></span></a>
			</div>

			<table class="table">
				<tr>
					<th width="10"></th>
					<th>Kategori</th>
					<th></th>
				</tr>
				<tbody id="tampilkategori">
				<?php 
				
				if($db->tampilkategori()) :
				$no = 1;
				foreach($db->tampilkategori() as $r) :

				?>
				<tr class="jmlTr">
					<td>
						<input type="checkbox" class="hapus" name="hapus[]" id="hapus<?= $no; ?>" value="<?= $r['kategori_id']; ?>">
						<label for="hapus<?= $no; ?>"></label>
					</td>
					<td><?= $r['nama_kategori']; ?></td>
					<td class="center" width="10"><a href="index.php?ref=editKategori&kategori_id=<?= $r['kategori_id']; ?>"><span class="fa fa-edit fa-lg"></span></a></span></td>
				</tr>
				<?php $no++; endforeach; endif; ?>
				</tbody>
			</table>
		</div><!-- col-8 -->
	</div><!-- container -->
</div>

<script type="text/javascript">
	$(function(){

		// delete kategori
		$("a#deleteKategori").click(function(){

			let i = 0;
			let kategori_id = [];
			$("input.hapus:checked").each(function(){
				kategori_id[i] = $(this).val();i++;
			})
			const tokenCSRF = $("input#tokenCSRF").val();

			$.ajax({
				type:"POST",
				url:"_kategori/proses.php?action=deleteKategori",
				data:"tokenCSRF="+tokenCSRF+"&kategori_id="+kategori_id,
				beforeSend:function(){
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader80");
				},
				success:function(respon){
					document.querySelector("div.conLoader .loader").classList.replace("loader80","loader100");

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
		})
	})
</script>