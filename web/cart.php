<?php
/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2018/2/24 13:58
 * Desc: 我的购物车
 */
include 'init.php';
$TEMPLATE['type'] = 'cart';
$TEMPLATE['pageTitle'] = '我的购物车';

$cart_list = $goods_list = [];
foreach ($_COOKIE as $k => $v)
{
    if(strpos($k , 'mycarts_') === 0)
    {
        $aid = intval($v);
        $goods_id = intval(str_replace('mycarts_','', $k));
        $goods_list[$goods_id] = $aid;
    }
}

$active_model = new \model\Active();
$goods_model  = new \model\Goods();
foreach ($goods_list as $goods_id => $aid)
{
    $goods_info = $goods_model->get($goods_id);
    $active_info = $active_model->get($aid);
    if($goods_info && $active_info
        && $goods_info['active_id'] == $aid
        && $goods_info['sys_status'] === '1'
        && $goods_info['sys_status'] === '1'
        && $active_info['time_begin'] < $now
        && $active_info['time_end'] > $now
    )
    {
        // 校验数据的正确
        $cart_list[$aid]['active'] = $active_info;
        $cart_list[$aid]['goods'][] = $goods_info;
    }
}
$TEMPLATE['carts_list'] = $cart_list;
include TEMPLATE_PATH . '/cart.php';



