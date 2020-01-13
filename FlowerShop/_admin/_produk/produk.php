<?php
	if(!class_exists("config")) die;
	if(!class_exists("produk")) die;
	if(!class_exists("kategori")) die;

	$db = new produk;
	$kat = new kategori;
	if($db->cekLoginValid_halamanAdmin()) die;
?>
<div id="produk" class="produk noAnimate marginBottom200px">
	<div class="judul cf">
		<div class="container">
			<div class="col-1 marginTop15px">
				<a href="index.php?ref=tambahProduk" class="btn2 green" id="tambahDataProduk"><span class="fa fa-database"></span></a>
			</div>
			<div class="col-3">
				<select id="kategori">
					<option value="all" selected="">Kategori</option>
					<?php  
					$kategori = $kat->tampilkategori();
					if($kategori) :
					foreach($kategori as $k) :
					?>
					<option value="<?= $k['kategori_id']; ?>"><?= $k['nama_kategori']; ?></option>
					<?php endforeach; endif; ?>
				</select>
			</div>
			<div class="col-3">
				<select id="stock">
					<option value="all" selected>Persediaan</option>
					<option value="ada">Ada</option>
					<option value="habis">Habis</option>
				</select>
			</div>
			<div class="col-5">
				<div class="search">
					<input type="text" id="keyword" placeholder="Pencarian ...">
					<a id="search"><span class="fa fa-search"></span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="divProduk white noAnimate clearBoth">
		<div class="container">

			<div id="tampilProduk" class="tampilProduk cf">
			<?php  
				$data = $db->tampilProduk(null,null,null,$_SESSION['FlowerShop']['LIMIT_DEFAULT']);
				if($data) :
				foreach($data as $r) :
			?>
				<div class="col-4">
					<div class="detailProduk">
						<div class="img">
							<img src="<?= $r['url_img']; ?>" alt="">
						</div>
						<div class="description">
							<h2><?= $r['nama_produk']; ?></h2>
							<p class="harga">Rp <?= $r['harga']; ?></p>
							<p class="stock"><?= $r['jml_stock']; ?> Persediaan</p>
							<p class="sale"><span class="fa fa-shopping-basket"></span> <?= $r['sale']; ?></p><br>

							<a href="index.php?ref=editProduk&produk_id=<?= $r['produk_id']; ?>" class="khaki"><span class="fa fa-edit fa-lg"></span></a>
							<a class="deleteProduk" produk_id="<?= $r['produk_id']; ?>" class="orange"><span class="fa fa-trash-o fa-lg"></span></a>
							
						</div>
					</div>
				</div>
			<?php endforeach; endif; ?>
			</div>

		</div>
	</div>
	<div class="conLoaderLine">
		<p></p>
		<div class="loaderLine">
			<div class="lineIn"></div>
		</div>
	</div>
</div>
<input type="hidden" id="tokenCSRF" value="<?= config::generate_tokenCSRF(); ?>">
<statusAjax value="yes">
<script type="text/javascript">
$(function(){
	/* action != undefined khusus untuk infinite scroll */
	function tampil_produk(kategori, stock, searchInput, offset,action) {
		const valkategori_id = kategori.value;
		const valstock = stock.value;
		const valsearchInput = searchInput.value;

		const statusAjax = document.querySelector("statusAjax");

		$.ajax({
			type:"POST",
			url:"_produk/proses.php?action=tampilProduk",
			data:{kategori_id:valkategori_id, stock:valstock, searchInput:valsearchInput, offset:offset},
			beforeSend:function() {

				if(action == "scroll") {
					$('.loaderLine').fadeIn();
				} else {
					$("div.conLoader").addClass("muncul");
					$("div.conLoader .loader").addClass("loader90");
				}

				statusAjax.setAttribute("value","ajax");
				kategori.setAttribute("disabled","disabled");
				stock.setAttribute("disabled","disabled");
				searchInput.setAttribute("disabled","disabled");
			},
			success:function(respon) {
				kategori.removeAttribute("disabled");
				stock.removeAttribute("disabled");
				searchInput.removeAttribute("disabled");
				statusAjax.setAttribute("value","yes");

				if(action == "scroll") {
					$('.loaderLine').fadeOut();
				} else {
					document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");
				}

				let data;
				try {
					data = JSON.parse(respon);
				} catch(e){}

				if(data!=undefined) {
					let hasil = '';
					data.forEach(function(e){
						hasil += '<div class="col-4"><div class="detailProduk">';
							hasil += '<div class="img">';
								hasil += '<img src="'+e.url_img+'" alt="">';
							hasil += '</div>';
							hasil += '<div class="description">';
								hasil += '<h2>'+e.nama_produk+'</h2>';
								hasil += '<p class="harga">Rp '+e.harga+'</p>';
								hasil += '<p class="stock">'+e.jml_stock+' Persediaan</p>';
								hasil += '<p class="sale"><span class="fa fa-shopping-basket"> '+e.sale+'</p><br>'

								hasil += '<a href="index.php?ref=editProduk&produk_id='+e.produk_id+'" class="khaki"><span class="fa fa-edit fa-lg"></span></a> ';
								hasil += '<a class="deleteProduk" produk_id="'+e.produk_id+'" class="orange"><span class="fa fa-trash-o fa-lg"></span></a>';
							hasil += '</div>';
						hasil += '</div></div>';
					})
					if(action == "scroll") {
						$("div#tampilProduk").append(hasil);
					} else {
						$("div#tampilProduk").html(hasil);
					}

				} else {
					if(action == "scroll") {
						$(".conLoaderLine p").text("Tidak ada data lagi!");
						setTimeout(function(){
							$(".conLoaderLine p").text("");
						},8000);
					} else {
						$("div#tampilProduk").html('<center><h3 class="emptyData">Data kosong</h3></center>');	
					}
				}

				if(action == undefined) {
					$("div.conLoader").removeClass("muncul");
					$("div.conLoader .loader").removeClass("loader100");
				}
			}
		})
	}

	const kategori = document.querySelector("select#kategori");
	const stock = document.querySelector("select#stock");
	const btnSearch = document.querySelector("a#search");
	const searchInput = document.querySelector("input#keyword");
	const statusAjax = document.querySelector("statusAjax");
	if(kategori != undefined) {
		kategori.addEventListener("change",function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_produk(kategori, stock, searchInput);
			}
		});
	}
	if(stock != undefined) {
		stock.addEventListener("change",function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_produk(kategori, stock, searchInput);
			}
		});
	}
	if(btnSearch != undefined) {
		btnSearch.addEventListener("click", function(){
			if(statusAjax.getAttribute('value') == "yes") {
				tampil_produk(kategori, stock, searchInput);
			}
		});
	}

	// delete produk
	const tampilProduk = document.querySelector("div#tampilProduk");
	if(tampilProduk != undefined) {
		tampilProduk.addEventListener('click', function(e){
			let target = e.target;
			if(target.classList.contains("deleteProduk") == false) {
				target = e.target.parentElement;
			}

			if(target.classList.contains("deleteProduk") == true && statusAjax.getAttribute('value') == "yes") {
				const produk_id = target.getAttribute('produk_id');
				const tokenCSRF = document.querySelector("input#tokenCSRF").value;
				$.ajax({
					type:"POST",
					url:"_produk/proses.php?action=deleteProduk",
					data:{tokenCSRF:tokenCSRF,produk_id:produk_id},
					beforeSend:function() {
						$("div.conLoader").addClass("muncul");
						$("div.conLoader .loader").addClass("loader90");
						statusAjax.setAttribute("value","ajax");
						kategori.setAttribute("disabled","disabled");
						stock.setAttribute("disabled","disabled");
						searchInput.setAttribute("disabled","disabled");
					}, 
					success:function(respon) {
						statusAjax.setAttribute("value","yes");
						kategori.removeAttribute("disabled");
						stock.removeAttribute("disabled");
						searchInput.removeAttribute("disabled");
						document.querySelector("div.conLoader .loader").classList.replace("loader90","loader100");

						if(respon != undefined && respon == "success") {
							$(target.parentElement.parentElement.parentElement).remove();
						} else {
							FlowerAlert.show('Data gagal didelete!');
						}

						$("div.conLoader").removeClass("muncul");
						$("div.conLoader .loader").removeClass("loader100");
					}
				})
			}
		})
	}

	// infinite scroll
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height()+300 > $(document).height()) {
			if(statusAjax.getAttribute('value') == "yes" && $(".conLoaderLine p").text().length == 0) {
				const offset = document.querySelectorAll("div.detailProduk").length;
				tampil_produk(kategori, stock, searchInput, offset,"scroll");
			}
		}
	});
})
</script>