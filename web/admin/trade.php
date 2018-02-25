<?php
/**
 * 订单信息管理页
 *
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/24
 * Time: 下午10:51
 */

include 'init.php';
$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin';
$TEMPLATE['refer'] = $refer;
$TEMPLATE['type']  = 'trade';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$trade_model = new \model\Trade();
if('list' == $action)
{
    $page = getReqInt('page','get', 1);
    $size = 20;
    $offset = ($page -1) * $size;
    $data_list = $trade_model->getList($offset, $size);
    $TEMPLATE['data_list'] = $data_list;
    $TEMPLATE['pageTitle']  = '订单管理';
    include TEMPLATE_PATH . '/admin/trade_list.php';
}
else if('edit' == $action)
{
    $id = getReqInt('id', 'get', 0);
    if ($id) {
        $data = $trade_model->get($id);
    } else {
        $data = [
            'id' => 0, 
            'active_id' => 0, 
            'title' => '', 
            'description' => '', 
            'img' => '',
            'price_normal' => '0', 
            'price_discount' => '0',
            'num_total' => 0, 
            'num_user' => 0,
            'time_begin' => '', 
            'time_end' => ''
        ];
    }

    $TEMPLATE['data'] = $data;
    $TEMPLATE['pageTitle'] = '编辑订单信息-订单管理';
    include TEMPLATE_PATH . '/admin/trade_edit.php';
} 
else if ('confirm' == $action)
{
    $id = getReqInt('id','get',0);
    $ok = false;
    if($id)
    {
        $trade_model->id = $id;
        $trade_model->sys_status = 4;
        $ok = $trade_model->save();
    }

    if($ok)
    {
        redirect($refer);
    }
    else
    {
        show_result("确认订单的时候出现错误", $refer);
    }
}
else
{
    echo 'error goods action';
}




