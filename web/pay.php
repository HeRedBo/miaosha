<?php
/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2018/2/24 11:21
 * Desc: 支付接口
 */

include 'init.php';

/**
 * 1 校验用户是否登录
 */
if(!$login_user_info || !$login_user_info['uid'])
{
    $result  = [
        'error_no' => '101',
        'error_msg' => '请先登录'
    ];
    show_result($result);
}
$uid    =  $login_user_info['uid'];
$id     = getReqInt('id');
$action = isset($_GET['action']) ? $_GET['action'] : false;

if(!$id)
{
    $result  = [
        'error_no' => '102',
        'error_msg' => '参数异常'
    ];
    show_result($result);
}

$trade_model = new \model\Trade();
$info  = $trade_model->get($id);
if(!$info)
{
    $result  = ['error_no' => '103', 'error_msg' => '订单信息异常'];
    show_result($result);
}
if($info['uid'] != $uid)
{
    $result  = ['error_no' => '104', 'error_msg' => '没有权限更新订单信息'];
    show_result($result);
}
if($action)
{
    // 取消订单
    $trade_model->sys_status = 5;
    $trade_model->time_cancel = $now;
    $result = "取消订单";
}
else
{
    $trade_model->sys_status = 2;
    $trade_model->time_pay = $now;
    $result = "订单支付";
}

$ok = $trade_model->save($id);
if($ok)
{
    $result .= ': 成功';
}
else
{
    $result .= ':失败， 请稍后再试！';
}
show_result($result,'/trade.php');



