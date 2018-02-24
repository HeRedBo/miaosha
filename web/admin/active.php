<?php

/**
 * 活动信息管理页
 * 
 */
include 'init.php';
$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin';
$TEMPLATE['refer'] = $refer;
$TEMPLATE['type']  = 'active';
$TEMPLATE['pageTitle']  = 'active';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

$active_model = new \model\Active();
if('list' == $action) 
{
	$page = getReqInt('page','get', 1);
	$size = 20;
	$offset = ($page -1) * $size;
	$data_list = $active_model->getList($offset, $size);
	$TEMPLATE['data_list'] = $data_list;
	$TEMPLATE['pageTitle']  = '活动管理';
	include TEMPLATE_PATH . '/admin/active_list.php';
} 
else if('edit' == $action)
{
	// 新增与编辑
	$id = getReqInt('id','get',0);
	if($id)
	{
		$data = $active_model->get($id);
		$data['time_begin'] = date("Y-m-d H:i:s", $data['time_begin']);
		$data['time_end'] = date("Y-m-d H:i:s", $data['time_end']);
	}
	else
	{
		$data = [
			'id' => 0,
			'title' => '',
			'time_begin' => '',
			'time_end' => '',
		];
	}
	$TEMPLATE['data'] = $data;
	$TEMPLATE['pageTitle'] = '编辑活动信息-活动管理';
	include TEMPLATE_PATH . '/admin/active_edit.php';
} 
else if ('save' == $action)
{
	$info = $_POST['info'];
	$info['title']      = addslashes($info['title']);
	$info['time_begin'] = strtotime($info['time_begin']);
	$info['time_end']   = strtotime($info['time_end']);
	foreach ($info as $k => $v) 
	{
		$active_model->$k =$v;
	}
	if($info['id'])
	{
		$active_model->sys_lastmodify = time();
		$ok = $active_model->save();
	}
	else
	{
		$active_model->sys_lastmodify = $active_model->sys_dateline  = time();
		$active_model->sys_ip = getClientIp();
        $ok = $active_model->create();
	}
	if($ok)
	{
		redirect('active.php');
	}
	else
	{
		echo '<script>alert("数据保存失败");history.go(-1);</script>';
	}
}
else if ('delete' === $action) // 下线
{
	$id = getReqInt('id','get',0);
}



