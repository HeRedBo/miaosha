<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/22
 * Time: 下午9:08
 *
 *  校验活动和商品的状态
 *
 *
 *
 *
 */

include 'init.php';

$aid = getReqInt('aid');
$gid = getReqInt('gid');

// 1 请求参数校验
//if(!$login_user_info['uid'] || !$aid  || $gid)
//{
//    $result  = [
//        'error_no' => '201',
//        'error_msg' => '请求参数异常',
//    ];
//    echo json_encode($result);
//    exit;
//}
// 2
$redis_object = \common\Datasource::getRedis('instance1');

$data = $redis_object->mget(
    [
        'st_a_' . $aid,
        'st_g_' . $gid,
    ]
);

$info = [
    'now' => time(),
    'ip'  => getClientIp(),
    'uid' => $login_user_info['uid']
];
$str = signQuestion($info);
echo json_encode(['user_sign' => $str]);







