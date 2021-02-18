<?php
	if(!class_exists("config")) die;
	if(!class_exists("cart")) die;  

	$db = new cart;
	$dataCart = $db->tampilKeranjang();
	$tokenCSRF = config::generate_tokenCSRF();
?>
<div class="cart cf marginBottom200px">
	<div class="container">
		<div class="col-12">
			<table class="table marginBottom100px">
				<tr>
					<th>Nama</th>
					<th>Harga</th>
					<th>Jumlah</th>
					<th>Total</th>
				</tr>
				<?php 
					if($dataCart) : 
					$total = 0;
					foreach($dataCart as $r) : 
					$total += $r['harga']*$r['qty'];
				?>
				<tr>
					<td><?= $r['nama_produk']; ?></td>
					<td>Rp <?= config::generate_hargaFormat($r['harga']); ?></td>
					<td><?= $r['qty']; ?></td>
					<td>Rp <?= config::generate_hargaFormat($r['harga']*$r['qty']); ?></td>

					<td width="10"><a href="<?= config::base_url('cart/proses.php?action=deleteCart&produk_id='.$r['produk_id'].'&transaksi_id='.$r['transaksi_id']).'&tokenCSRF='.$tokenCSRF; ?>"><span class="fa fa-remove"></span></a></td>
				</tr>
				<?php endforeach; endif; ?>
				<tr>
					<th colspan="3" class="alignRight">Total</th>
					<td>Rp <?= config::generate_hargaFormat($total??''); ?></td>
				</tr>
			</table>
			<?php if($dataCart) : ?>
			<center><a href="<?= config::base_url('index.php?ref=checkout'); ?>" class="btn2 btn-default">Pembayaran</a></center>
			<?php endif; ?>
		</div>
	</div>
</div>