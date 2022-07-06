<?php include  TEMPLATE_PATH . "/common/header.php"; ?>
<?php include  TEMPLATE_PATH . "/common/navigation.php"; ?>

<div class="container-fluid">
    <?php if ($TEMPLATE['list_active']): ?>
        
        <?php foreach ($TEMPLATE['list_active'] as $k => $data): ?>
            <?php if ($TEMPLATE['list_active_goods'][$data['id']]): ?>
                <?php foreach ($TEMPLATE['list_active_goods'][$data['id']] as $k2 => $goods): ?>
                    <div class="span4">
                        <img src="<?php echo $goods['img'] ?>" alt="" style="width: 500px" >
                        <br>
                        活动： <?php  echo $data['title']; ?> <br>
                        商品： <?php  echo $goods['title']?>  <br>
                        价格： <?php echo $goods['price_discount'] ?> <br>
                        原价： <span style="text-decoration:line-through;"><?php echo $goods['price_normal'] ?></span> <br>
                        库存： <?php echo $goods['num_left'] ?> <br>

                        <?php if ($goods['sys_status'] == 0): ?>
                            <?php echo "商品待上线 敬请期待"; ?>
                        <?php elseif($goods['sys_status'] == 1): ?>
                            <?php if ($goods['num_left'] < 1): ?>
                                <?php echo "商品已抢光 下次再来吧" ?>
                            <?php else: ?>
                               <!--  href="/buy.php?id=<?php echo $goods['id'] ?>" -->
                                <a  onclick="check_status(<?php echo $data['id'] ?>, <?php echo $goods['id'] ?>);return false">立即抢购</a>
                                &nbsp; | &nbsp;
                                 <a href="javascript:void();" onclick="addCard(<?php echo $data['id'] ?>, <?php echo $goods['id'] ?>);return false">加入购物车</a>
                            <?php endif ?>
                            
                        <?php elseif($goods['sys_status'] == 2): ?>
                           <?php  echo '商品已下线，下次再来买吧'; ?>
                        <?php endif ?>
                    </div>

                <?php endforeach ?>

            <?php endif ?>


        <?php endforeach ?>
    
      

    <?php else: ?>
        <center>
            暂时没有秒杀活动信息
        </center>
    <?php endif ?>
   
</div>

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

<script type="text/javascript">
 <?php if (!isset($TEMPLATE['login_user_info']['uid'])): ?>

    //  校验活动 商品状态
    function check_status(aid, goods_id)
    {
        alert('请先登录');
    }

    // 获取回答问题
    function getQuestion(aid, goods_id, user_sign)
    {
        alert('请先登录');
    }

    // 加入购物车
    function addCard(aid, goods_id)
    {
        alert('请先登录');
    }
<?php else: ?>

    // 校验活动 商品状态
    function check_status(aid, goods_id)
    {
        var status_url = '/astatus/' + aid + '_' + goods_id + '.js';
         $.ajax({
            // url: '/astatus.php',
             url: status_url,
             type: 'GET',
             dataType: 'json',
             async:true, 
            // data: {aid: goods_id,gid:goods_id},
             success : function(data) 
             {
                if (data['error_no']) 
                {
                    alert(data['error_msg']);
                } 
                else 
                {
                    getQuestion(aid, goods_id, data['user_sign']);
                }
             }
         });        
        
    }

    function getQuestion(aid, goods_id, user_sign)
    {
        var url = '/question.php?aid=' + aid;
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            async:true, 
            data: {param1: 'value1'},
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
    
    // 加入购物车
    function addCard(aid, goods_id)
    {
        $.cookie('mycarts_' + goods_id, aid);
        alert('成功加入购物车')
    }
<?php endif ?>

</script>


<iframe name="iframe_inner_post" src="about:blank" style="display:none;"></iframe>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>
<?php include  TEMPLATE_PATH . "/common/footer.php"; ?>








