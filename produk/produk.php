<?php  
	if(!class_exists("config")) die;
	if(!class_exists("produk")) die;
?>

<div class="tagKategoriSelect marginTop120px">
	<div class="container">
		<ul class="conTags">
			<!-- <li>Kategori <span class="fa fa-remove"></span></li> -->
		</ul>
	</div>
</div>
<div class="divProduk noAnimate marginBottom65px marginTop20px">
	<div class="container">
		<div id="tampilProduk" class="tampilProduk cf marginBottom200px">
			<?php
			    $produk = new produk;
			    $data = $produk->tampilProduk(null,null,null,$_SESSION['FlowerShop']['LIMIT_DEFAULT'],0);
				if($data) :
				foreach($data as $r) :
			?>
			<div class="col-4 col-m-6">
				<div class="detailProduk">
					<div class="img">
						<img class="lazy" data-src="<?= $r['url_img']??''; ?>" alt="">
					</div>
					<div class="badgePlus">
						<a href='<?= config::base_url("index.php?ref=detail_produk&produk_id=$r[produk_id]"); ?>'><span class="fa fa-external-link"></span></a>
					</div>
					<div class="description">
						<h2><?= $r['nama_produk']; ?></h2>
						<p class="harga">Rp <?= config::generate_hargaFormat($r['harga']); ?></p>
						<p class="stock"><?= $r['jml_stock']; ?> Persediaan</p>
						<p class="sale"><span class="fa fa-shopping-basket"></span> <?= $r['sale']; ?></p>			
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
		</div>
		<div class="conLoaderLine">
			<p></p>
			<div class="loaderLine">
				<div class="lineIn"></div>
			</div>
		</div>
	</div>
</div>

<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	/* action parameter untuk mengecek action yang dilakukan user untuk melakukan tindakan yang tepat */
	function getProduk(action, kategori_id, nama_kategori, offset=0) {
		$.ajax({
			type:"POST",
			url:"produk/tampilProdukAjax.php",
			data:{kategori_id:kategori_id,offset:offset},
			beforeSend:function(){
					
				if(action === 'scroll') {
					$('.loaderLine').fadeIn();
				} else {
					$(".conLoader").addClass("muncul");
					$(".loader").addClass("loader90");
				}
				$("statusAjax").attr("value","ajax");
			},
			success:function(respon) {
				$("statusAjax").attr("value","yes");

				// loading action
				if(action === 'scroll') {
					$('.loaderLine').fadeOut();
				} else {
					document.querySelector(".loader").classList.replace("loader90","loader100");
				}

				// set tagKategoriSelect
				if(action == 'set') {
					const tagKategoriSelect = '<li class="tags">'+nama_kategori+' <span class="fa fa-remove" id="kategori" kategori_id="'+kategori_id+'"></span></li>';
					$(".tagKategoriSelect ul").html(tagKategoriSelect);

				} else if(action == 'del') {
					const conTags = document.querySelector("ul.conTags");
					const kategori = document.querySelector("li.tags");
					conTags.removeChild(kategori);
				}

				// proccess data
				let data;
				try {
					data = JSON.parse(respon);
				}catch(e){}

				if(data != undefined) {
					let hasil = '';
					data.forEach(function(e){
							hasil += '<div class="col-4 col-m-6"><div class="detailProduk">';
							// img
							hasil += '<div class="img">';
							 hasil += '<img src="'+e.url_img+'" alt="">';
							hasil += '</div>';
							// badgePlus
							hasil += '<div class="badgePlus">';
							 hasil += '<a href="<?= config::base_url("index.php?ref=detail_produk&produk_id="); ?>'+e.produk_id+'"><span class="fa fa-external-link"></span></a>';
							hasil += '</div>';
							// description
							hasil += '<div class="description">';
							 hasil += '<h2>'+e.nama_produk+'</h2>';
							 hasil += '<p class="harga">Rp '+e.harga+'</p>';
							 hasil += '<p class="stock">'+e.jml_stock+' Persediaan</p>';
							 hasil += '<p class="sale"><span class="fa fa-shopping-basket"></span> '+e.sale+'</p>';
							hasil += '</div>';

							hasil += '</div></div>';
					});

					// end process
					if(action === 'scroll') {
						$('#tampilProduk').append(hasil);
					} else {
						$("#tampilProduk").html(hasil);
					}
				} else {
					if(action == "scroll") {
						$(".conLoaderLine p").text("Tidak ada data lagi!");
						setTimeout(function(){
							$(".conLoaderLine p").text("");
						},5000);
					} else {
						$("#tampilProduk").html('<center><h3 class="emptyData">Data kosong</h3></center>');
					}
				}

				if(action != "scroll") {
					$(".conLoader").removeClass("muncul");
					$(".loader").removeClass('loader100');
				}
			}//success
		})
	}

	const statusAjax = document.querySelector('statusAjax');
	// add tags
	const conBtnTampilProdukAjax = document.querySelector('.conBtnTampilProdukAjax');
	conBtnTampilProdukAjax.addEventListener('click',function(e){
		if(e.target.classList.contains('btnTampilProdukAjax') === true) {
			// jika ajax sedang tidak dilakukan
			if(statusAjax.getAttribute('value') != "ajax") {
				const kategori_id = e.target.getAttribute('kategori_id');
				const nama_kategori = e.target.getAttribute('nama_kategori');
				getProduk('set',kategori_id,nama_kategori);
			}
		}
	});

	// delete tags
	const conTags = document.querySelector("ul.conTags");
	if(conTags != null) {
		conTags.addEventListener('click',function(e){
			const id = e.target.getAttribute('id');
			// jika ajax sedang tidak dilakukan
			if(statusAjax.getAttribute('value') != "ajax" && id === 'kategori' && e.target.classList.contains('fa-remove') === true) {
				getProduk('del','all');
			}
		});
	}

	// infinite scoll
	$(window).scroll(function(){
		if($(window).height() + $(window).scrollTop() + 300 > $(document).height()) {
			// jika ajax sedang tidak dilakukan
			if(statusAjax.getAttribute('value') == "yes" && $(".conLoaderLine p").text().length == 0) {
				const kategori = document.querySelector("li.tags span#kategori");
				const offset = document.querySelectorAll("div.detailProduk").length;
				let kategori_id;
				if(kategori != null) {
					kategori_id = kategori.getAttribute('kategori_id');
				} else {
					kategori_id = 'all';
				}

				getProduk('scroll',kategori_id,null,offset);
			}
		}
	});

});
</script>