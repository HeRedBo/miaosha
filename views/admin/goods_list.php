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
                <th>商品名称</th>
                <th>图片</th>
                <th title="原价/优惠价">价格(原价/优惠价)</th>
                <th title="总数/每人限购数/剩余数">数量 (总数/每人限购数/剩余数)</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($TEMPLATE['data_list'] as $key => $data): ?>
                <tr>
                    <td><?php echo $data['id']; ?></td>
                    <td><?php echo $data['active_id']; ?></td>
                    <td><?php echo htmlspecialchars($data['title']); ?></td>
                    <td>
                        <img src="<?php echo $data['img']; ?>" alt="" style="width: 100px;">
                    </td>
                    <td><?php echo $data['price_normal'] . ' / '. $data['price_discount'];?></td>
                    <td><?php echo $data['num_total'] . ' / ' . $data['num_user'] . ' / ' . $data['num_left'];?></td>
                    <td><?php echo $arr_active_status[$data['sys_status']] ?></td>
                    <td>
                        <a href="?action=edit&id=<?php echo $data['id'];  ?>">编辑</a>
                        <?php if ($data['sys_status'] === '1'): ?>
                            | <a href="?action=delete&id=<?php echo $data['id']; ?>">下线</a>
                        <?php else: ?>
                            | <a href="?action=reset&id=<?php echo $data['id']; ?>">上线</a>
                        <?php endif ?>
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
