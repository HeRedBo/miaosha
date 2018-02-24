<?php
/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2018/2/24 10:41
 * Desc:
 */
/**
 * 我的订单
 */
include 'init.php';
$TEMPLATE['type'] = 'trade';
$TEMPLATE['pageTitle'] = '我的订单';


if(!$login_user_info || !$login_user_info['uid'])
{
    $result  = [
        'error_no' => '101',
        'error_msg' => '请先登录'
    ];
    show_result($result);
}
$uid  = $login_user_info['uid'];
$trade_model = new \model\Trade();
$list_trade = $trade_model->getUserTrade($uid);
$TEMPLATE['list_trade'] = $list_trade;
include TEMPLATE_PATH . '/trade.php';



