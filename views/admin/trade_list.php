<?php include  TEMPLATE_PATH . "/admin/common/header.php"; ?>
<?php include  TEMPLATE_PATH . "/admin/common/navigation.php"; ?>

<div class="container-fluid">
    <a href="?action=edit" class="btn btn-primary">添加商品</a>

    <?php if ($TEMPLATE['data_list']): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>活动ID</th>
                <th>商品ID</th>
                <th>用户ID</th>
                <th>用户名</th>
                <th style="width: 60px;">数量</th>
                <th style="width: 60px;">订单价格</th>
                <th>订单时间</th>
                <th>商品详情</th>
                <th>创建时间</th>
                <th>IP</th>
                <th>状态</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
            <?php foreach ($TEMPLATE['data_list'] as $key => $data): ?>
                <tr>
                    <td><?php echo $data['id'];?></td>
                    <td><?php echo $data['active_id'];?></td>
                    <td><?php echo $data['goods_id'];?></td>
                    <td><?php echo $data['uid'];?></td>
                    <td><?php echo $data['username'];?></td>
                    <td>
                        单品数：
                        <br/>
                        <?php echo $data['num_total'];?>
                        <br/><br/>
                        种类数：
                        <br/>
                        <?php echo $data['num_goods'];?>
                    </td>
                    <td>
                        原价：
                        <br/>
                        <?php echo $data['price_total'];?>
                        <br/><br/>
                        优惠：
                        <br/>
                        <?php echo $data['price_discount'];?>
                    </td>


                    <td>
                        确认：
                        <br/>
                        <?php echo date('Y-m-d H:i:s', $data['time_confirm']);?><br/>
                        支付：
                        <br/>
                        <?php echo date('Y-m-d H:i:s', $data['time_pay']);?><br/>
                        过期：
                        <br/>
                        <?php echo date('Y-m-d H:i:s', $data['time_over']);?><br/>
                        取消：
                        <br/>
                        <?php echo date('Y-m-d H:i:s', $data['time_cancel']);?>
                    </td>
                    <td>
                       <?php
                            $goods_info = json_decode($data['goods_info'],1);
                       ?>
                        
                        <?php foreach ($goods_info as $key => $goods): ?>
                            <?php 
                                $num = $goods['goods_num'];
                                 $info = $goods['goods_info'];

                             ?>
                            <div>
                                商品名称： <span><?php echo $info['title'] ?></span> <br>
                                数量： <span><?php echo $goods['goods_num']; ?></span><br>
                                <img src="<?php echo $info['img']; ?>" alt="" width="80"> <br>
                            </div>
                        <?php endforeach ?>
                    </td>

                    <td><?php echo date('Y-m-d H:i:s', $data['sys_dateline']);?></td>
                    <td><?php echo $data['sys_ip'];?></td>
                    <td><?php echo $arr_trade_status[$data['sys_status']];?></td>
                    <td>
                    <?php if ($data['sys_status']==='2') {?>
                     <a href="?action=confirm&id=<?php echo $data['id'];?>">确认处理</a>
                    <?php } ?>
                </td>

                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <center>
            暂时还没有商品信息，现在就来<a href="?action=edit">添加商品</a>
        </center>
    <?php endif ?>

</div>

<iframe name="iframe_inner_post" src="about:blank" style="display:none;"></iframe>
<?php include TEMPLATE_PATH . "/admin/common/footer.php"; ?>
