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
    show_result($result);
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
    show_result($result);
}

/**
 * 3.1、校验活动状态信息
 */
$status_check = false;
$str_sign_data  = unSignQuestion($sign_data);
$sign_data_info = json_decode(trim($str_sign_data),true);

// 时间不能超过五分钟 IP地址和用户保持不变
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
    show_result($result);
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
    show_result($result);
}

// 统一格式化单商品 组合商品的数据结构 后期优化 相关数据读缓存
$nums = $goods = [];
if('buy_cart' != $action)
{
   $nums = [$goods_num];
   $goods = [$goods_id];
}
else
{
   $nums = $_POST['num'];
   $goods = $_POST['goods'];
}

$redis_obj =  \common\Datasource::getRedis('instance1');

$d_list = [
    'miaosha:string:u_trade_'. $uid. '_'. $active_id,
    'miaosha:string:st_a_'. $active_id
];

/**
 * 从缓冲中读取商品的信息 商品信息使用哈希存储
 *
 * id, sys_status number num_left
 * price_normal price_discount
 */
foreach ($goods as $i => $goods_id)
{
    $d_list[] = 'miaosha:string:info_g_'. $goods_id;
}
$data_list = $redis_obj->mget($d_list);

/**
 * 4 校验用户是否已经购买
 */
if($data_list[0])
{
    $result = [
        'error_no' => '104',
        'error_msg' => '请不要重复提交订单'
    ];
    show_result($result);
}




/**
 * 4 校验用户是否已经购买
 */
//$trade_model = new \model\Trade();
//$trade_info = $trade_model->getUserTrade($uid, $active_id);
//if($trade_info)
//{
//    $result = [
//        'error_no' => '104',
//        'error_msg' => '请不要重复提交订单'
//    ];
//    show_result($result);
//}

/**
 *  5、校验活活动信息 商品信息是否正常
 */
//$active_model = new \model\Active();
//$active_info = $active_model->get($active_id);

// 商品信息直接从 redis 缓存中读取
$active_info = $data_list[1];
if($active_info)
{
    $active_info = json_decode($active_info,1);
}
if(
    !$active_info || $active_info['sys_status'] != 1
    || $active_info['time_begin'] > $now
    || $active_info['time_end'] < $now
)
{
    $result = [
        'error_no' => '105',
        'error_msg' => '活动信息异常'
    ];
    show_result($result);
}
unset($data_list[0]);
unset($data_list[1]);

$num_total = $price_total = $price_discount = 0;
$trade_goods = [];

$goods_model = new \model\Goods();
//$goods_info  = $goods_model->get($goods_id);
foreach ($data_list as $i =>  $goods_info)
{
    $goods_num = $nums[$i -2];
    $goods_info = json_decode($goods_info,1);
 
   // 6、商品信息校验 状态校验
    if(!$goods_info || $goods_info['sys_status'] !=1)
    {
        $result = [
            'error_no' => '106',
            'error_msg' => '商品信息异常'
        ];
        show_result($result);
    }
   // 7、商品购买数量限制
    if($goods_num > $goods_info['num_user'])
    {
        $result = [
            'error_no' => '107',
            'error_msg' => '超过商品数量的限制'
        ];
        show_result($result);
    }

    // 8、商品剩余判断
    if($goods_info['num_left'] < $goods_num)
    {
        $result = [
            'error_no' => '108',
            'error_msg' => '商品库存不足'
        ];
        show_result($result);
    }

    // 9、减库存 ① 减少redis的缓存收 ②减少数据库库存
    $left  = $goods_model->changeLeftNumCached($goods_id, 0- $goods_num);

    $ok = false;
    if($left >= 0)
    {
        // 减少数据库数据库存
        $ok = $goods_model->changeLeftNum($goods_id, 0 - $goods_num);
    }
    else
    {
        $result = array('error_no' => '109', 'error_msg' => '商品剩余数量不足');
        show_result($result);
    }

    // 10.1 创建订单信息
    $trade_goods[] = [
        'goods_info' => $goods_info,
        'goods_num'  => $goods_num
    ];

    $num_total      = $goods_num;
    $price_total    = $goods_info['price_normal'] * $goods_num;
    $price_discount = $goods_info['price_discount'] * $goods_num;
}
// 10.2 保存订单信息
$trade_info = [
    'active_id' => $active_id,
    'goods_id' => $goods_id,
    'num_total' => $num_total,
    'num_goods' => count($goods),
    'price_total' => $price_total,
    'price_discount' => $price_discount,
    'goods_info' =>  json_encode($trade_goods),
    'uid' => $uid,
    'username' => $user_name,
    'sys_dateline' => $now,
    'time_confirm' => $now,
    'sys_status' => 1,
    'sys_ip' => $client_ip,
];
$trade_model = new \model\Trade();
foreach ($trade_info as $k => $v)
{
    $trade_model-> $k  = $v;
}
$trade_id = $trade_model->create();
// 秒杀成功 标识一下该用户已经参加过该活动:
if ($trade_id) {
    $key = 'miaosha:string:u_trade_'. $uid. '_'. $active_id;
    $redis_obj->set($key, 1,86400);
}

// 11 返回提示信息
$result = '秒杀成功，请尽快去支付';
show_result($result);


































