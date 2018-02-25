<?php

include 'init.php';
$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin';
$TEMPLATE['refer'] = $refer;
$TEMPLATE['type']  = 'log';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

$log_model = new model\Log();

if('list' == $action)
{
    $page = getReqInt('page','get', 1);
    $size = 20;
    $offset = ($page -1) * $size;
    $data_list = $log_model->getList($offset, $size);
    $TEMPLATE['data_list'] = $data_list;
    $TEMPLATE['pageTitle']  = '日志管理';
    include TEMPLATE_PATH . '/admin/log_list.php';
}
else if ('reset' == $action) {	// 恢复
    $id = getReqInt('id', 'get', 0);
    $ok = false;
    if ($id) {
        $log_model->id = $id;
        $log_model->sys_status = 2;
        $ok = $log_model->save($data);
    }
    if ($ok)
    {
        redirect($refer);
    } else {
        show_result('确认处理的时候出现错误',$refer);
    }
}

else
{
    echo 'error goods action';
}
