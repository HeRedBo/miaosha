<?php include  TEMPLATE_PATH . "/common/header.php"; ?>
<?php include  TEMPLATE_PATH . "/common/navigation.php"; ?>

<div class="container-fluid">
	<?php if ($TEMPLATE['carts_list']): ?>

    <form action="buy.php" method="POST">
        <?php foreach ($TEMPLATE['carts_list'] as $k => $data): ?>
            <?php if ($data['goods']): ?>
                <?php 
                    $goods_list = $data['goods'];
                    $active     = $data['active'];
                ?>
                <?php foreach ($goods_list as $k => $goods): ?>
                    <div class="span3">
                        <img src="<?php echo $goods['img']; ?> " style="width: 500px;"> <br>
                        活动： <span><?php echo $active['title']; ?></span> <br>
                        商品： <span><?php echo $goods['title']; ?></span> <br>
                        价格： <span><?php echo $goods['price_discount']; ?></span> <br>
                        原价： <span style="text-decoration: line-through;">
                            <?php echo $goods['price_normal'] ?>
                        </span> <br>
                        数量： <input type="text" name="num[]" value="1" style="width:30px"/>
                        <input type="hidden" name="goods[]" value="<?php echo $goods['id']; ?>" />
                    </div>
                <?php endforeach ?>
            <?php endif ?>
             <br style="clear:both;">
                <center>
                    <input type="hidden" name="action" value="buy_cart">
                    <input type="button" onclick="getQuestion(<?php echo $active['id'] .','. $goods['id']; ?>); return false;" value="确认提交" />
                    <input type="button" onclick="clearCarts(); return false;" value="清空购物车" />
                </center>
        <?php endforeach ?>
    </form>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="buy.php" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">秒杀问答</h4>
                    </div>
                    <div id="question_info">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php else: ?>
        <center>
            购物车没有商品，<a href="/">去首页看看吧</a>。
        </center> 
    <?php endif ?>
</div>
<script type="text/javascript">
	
	// 获取问答问题
	function getQuestion(aid, goods_id, user_sign)
    {
        var url = '/question.php?aid=' + aid;
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            async:true, 
        })
        .done(function(data) {
            console.log("success");
            var sign = data['sign'];
                var ask = data['ask'];
                var title = data['title'];
                var html = '';
                for (var i in data['data_list']) {
                    html += '<label><input type="radio" value="' + data['data_list'][i] + '" name="answer" /> ' + data['data_list'][i] + '</label><br/>';
                }
                html += '<label>商品数量： <input type="text" name="goods_num" value="1" /></label>';
                html += '<input type="hidden" name="question_sign" value="' + sign + '" />';
                html += '<input type="hidden" name="ask" value="' + ask + '" />';
                html += '<input type="hidden" name="active_id" value="' + aid + '" />';
                html += '<input type="hidden" name="goods_id" value="' + goods_id + '" />';
                html += '<input type="hidden" name="user_sign" value="' + user_sign + '" />';
                $("#question_info").html('<div class="modal-body">' + title + '[' + ask + ']</div>'
                    + '<div class="modal-body">' + html + '</div>'
                    + '<div class="modal-footer"><input type="submit" value="提交订单" /></div>');
                $('#myModal').modal();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });   
    }

    function clearCarts()
    {
        for (var c in $.cookie()) 
        {
            
            if(c.indexOf('mycarts_') === 0)
            {
                $.removeCookie(c);
            }
        }
        location.href="/cart.php";
    }
</script>

<iframe name="iframe_inner_post" src="about:blank" style="display:none;"></iframe>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>
<?php include  TEMPLATE_PATH . "/common/footer.php"; ?>

