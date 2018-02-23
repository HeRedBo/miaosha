<?php

/**
 * 抢购的处理逻辑
 */

include  'init.php';

$TEMPLATE['type'] = 'buy';
$TEMPLATE['pageTitle'] = '抢购';

// 参数处理
$active_id     = getReqInt('active_id');
$goods_id      = getReqInt('goods_id');
$goods_num     = getReqInt('goods_num');
$sign_data     = $_POST['user_sign'];
$question_sign = $_POST['question_sign'];
$ask           = $_POST['ask'];
$answer        = $_POST['answer'];
$action        = isset($_POST['action']) ? $_POST['action'] : false;

if('buy_num' == $action)
{
    $goods_num = $_POST['num'][0];
}
$client_ip = getClientIp();


/**
 * 1 校验用户是否登录
 */
if(!$login_user_info || !$login_user_info['uid'])
{
    $result  = [
        'error_no' => '101',
        'error_msg' => '登录之后才可以参与'
    ];
    return_result($result);
}
$uid       =  $login_user_info['uid'];
$user_name =  $login_user_info['username'];

/**
 * 2、校验参数是否正确,合法
 */
if(!$active_id || !$goods_id || !$goods_num || !$question_sign)
{
    $result = [
        'error_no' => '102',
        'error_msg' => '参数提交错误'
    ];
    return_result($result);
}

/**
 * 3.1、校验活动状态信息
 */
$status_check = false;
$str_sign_data  = unSignQuestion($sign_data);
$sign_data_info = json_decode(trim($str_sign_data),true);
// 时间不能超过五分钟 IP地址保存不变
if($sign_data_info && $sign_data_info['now'] < $now
    && $sign_data_info['now'] > $now - 3000
    && $sign_data_info['ip'] == $client_ip
    && $sign_data_info['uid'] == $uid
)
{
    $status_check = true;
}

if(!$status_check)
{
    $result = [
        'error_no' => '103',
        'error_msg' => '用户校验值校验没有通过'
    ];
    return_result($result);
}

// 3.2 校验问答信息是否正确
$question_check = false;
$str_question = unSignQuestion($question_sign);
$question_info= json_decode(trim($str_question),true);
if($str_question && $question_info)
{
    if($question_info['ask'] == $ask
        && $question_info['answer'] == $answer
        && $question_info['aid'] == $active_id
        && $question_info['uid'] == $uid
        && $question_info['ip'] == $client_ip
        && $question_info['now'] > $now - 300
    )
    {
        $question_check = true;
    }
}
if(!$question_check)
{
    $result = [
        'error_no' => '103',
        'error_msg' => '问答校验没有通过'
    ];
    return_result($result);
}

// 统一格式化单商品 组合商品的数据结构
$nums = $goods = [];
if('buy_cart' == $action)
{
    $nums = [$goods_num];
    $goods = $_POST['goods'];
}
else
{
    $num = $_POST['num'];
    $goods = $_POST['goods'];
}


/**
 * 4 校验用户是否已经购买
 */
$trade_model = new \model\Trade();
$trade_info = $trade_model->getUserTrade($uid, $active_id);
if($trade_info)
{
    $result = [
        'error_no' => '104',
        'error_msg' => '请不要重复提交订单'
    ];
    return_result($result);
}

























