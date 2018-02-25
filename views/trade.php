<?php include  TEMPLATE_PATH . "/common/header.php"; ?>
<?php include  TEMPLATE_PATH . "/common/navigation.php"; ?>

<div class="container-fluid">

<?php if ($TEMPLATE['list_trade']): ?>
	<?php foreach ($TEMPLATE['list_trade'] as $k => $data): ?>
		<div class="span3">
			<?php 
				$goods_infos = json_decode($data['goods_info'],1);
			 ?>
			
			<?php foreach ($goods_infos as $key => $goods_info): ?>
				<?php 
					$goods = $goods_info['goods_info'];
					$num   = $goods_info['goods_num'];
				 ?>
				 <div>
			 		<img src="<?php echo $goods['img'] ?>" style="width: 500px;"> <br>
			 	商品： <span><?php echo $goods['title']; ?></span><br>
			 	价格： <span><?php echo $goods['price_discount']; ?></span><br>
				原价： <span style="text-decoration:line-through">
					<?php echo $goods['price_normal']; ?>
				</span><br>
				数量： <span><?php echo $num; ?></span>
			 </div>

			 <div class="clear:both">
			 	状态： <?php echo $arr_trade_status[$data['sys_status']] ?> &nbsp; &nbsp; <br>
			 	<?php if ($data['sys_status'] < 2): ?>
			 		<a href="/pay.php?id=<?php echo $data['id']; ?>">立即支付</a> &nbsp;| &nbsp;
			 	<?php elseif($data['sys_status'] < 5): ?>
			 		<a href="/pay.php?action=cancle&id=<?php echo $data['id']; ?>">取消订单</a>
			 	<?php endif ?>

			 
			 </div>
			<?php endforeach ?>
			
			 
			 
		</div>
	<?php endforeach ?>
<?php else: ?>
	<center>
		暂时还没有秒杀订单信息
	</center>
<?php endif ?>

</div>

<iframe name="iframe_inner_post" src="about:blank" style="display:none;"></iframe>
<?php include TEMPLATE_PATH . "/common/footer.php"; ?>