<!-- 导航 begin -->
<div class="navbar">
    <div class="navbar-inner">
        <a href="/list.php" class="brand" _target="_self">
            首页
        </a>
        <ul class="nav">
            <li <?php if ($TEMPLATE['type'] == 'trade'){?>class="active"<?php }?> >
                <a href="/trade.php" target="_self">我的订单</a>
            </li>
            <li <?php if ($TEMPLATE['type'] == 'cart'){?>class="active"<?php }?>>
                <a href="/cart.php" target="_self">购物车</a>
            </li>
        </ul>
        <ul class="nav pull-right">
            <li>
                <?php if (isset($TEMPLATE['login_user_info']['uid']) && $TEMPLATE['login_user_info']['uid']): ?>
                    <?php echo $TEMPLATE['login_user_info']['username']; ?>
                <a href="/login.php?action=logout">
                   退出
                </a>
                <?php else: ?>
                <a href="/login.php?action=login">登录</a>
                <?php endif ?>
            </li>
        </ul>
    </div>
</div>
<!-- 导航 end -->