<!--导航 begin-->
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand" href="/admin/" target="_self">管理后台</a>
			<ul class="nav">
				<li <?php if ($TEMPLATE['type'] == 'active'){?>class="active"<?php }?>><a href="/admin/active.php" target="_self">活动</a></li>
				<li <?php if ($TEMPLATE['type'] == 'trade'){?>class="active"<?php }?>><a href="/admin/trade.php" target="_self">订单</a></li>
		    </ul>
		 
		</div>
	</div>
	
<!--导航 end-->