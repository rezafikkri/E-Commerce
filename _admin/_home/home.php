<?php 
	if(!class_exists("home")) die;
?>

<div class="home marginTop200px">
	<?php		
		$db = new home;
		if($db->cekLoginValid_halamanAdmin()) die;
	?>
	<div class="container">
		<div class="col-10 offset-left-1 offset-right-1 nopadding-all">
			<div class="col-4 nopadding-all">
				<div class="card bg"></div>
				<div class="card red">
					<div class="judul"><h3><span class="fa fa-users"></span> Total Pengguna</h3></div>
					<div class="ket"><p id="totalUsers"><?= $db->totalUsers(); ?></p></div>
				</div>
			</div>
			<div class="col-4 nopadding-all">
				<div class="card orange">
					<div class="judul"><h3><span class="fa fa-shopping-cart"></span> Total Pesanan</h3></div>
					<div class="ket"><p id="totalOrder"><?= $db->totalOrder(); ?></p></div>
				</div>
			</div>
			<div class="col-4 nopadding-all">
				<div class="card bg"></div>
				<div class="card green">
					<div class="judul"><h3><span class="fa fa-shopping-bag"></span> Total Penjualan</h3></div>
					<div class="ket"><p id="totalPayment"><?= $db->totalPayment(); ?></p></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	if(!!window.EventSource) {
		const totalUsers = document.querySelector("p#totalUsers");
		const totalOrder = document.querySelector("p#totalOrder");
		const totalPayment = document.querySelector("p#totalPayment");

		function connect(){
			let source = new EventSource('_home/getData.php');
			source.addEventListener('message', function(e) {
				if(e.origin === 'http://localhost') {
					let data;
					try {
						data = JSON.parse(e.data);
					}catch(e){}

					if(data != undefined) {
						totalUsers.innerText = data.totalUsers;
						totalOrder.innerText = data.totalOrder;
						totalPayment.innerText = data.totalPayment;
					}
				}
			});
			source.addEventListener('error', function(e) {
				if(source.readyState == 2) {
					setTimeout(function(){connect();}, 5000);
				}
			});
		}

		connect();
	}

</script>