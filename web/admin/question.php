<?php
/**
 * 问答信息管理页
 *
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/25
 * Time: 上午10:28
 */

include 'init.php';
$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin';
$TEMPLATE['refer'] = $refer;
$TEMPLATE['type']  = 'question';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$question_model = new \model\Question();

if('list' == $action)
{
    $page = getReqInt('page','get', 1);
    $size = 20;
    $offset = ($page -1) * $size;
    $data_list = $question_model->getList($offset, $size);
    $TEMPLATE['data_list'] = $data_list;
    $TEMPLATE['pageTitle']  = '问答管理';
    include TEMPLATE_PATH . '/admin/qeustion_list.php';
}
else if ('edit' == $action) {	// 编辑页
    $id = getReqInt('id', 'get', 0);
    if ($id) {
        $data = $question_model->get($id);
    }
    else
    {
        $data = array('id' => 0, 'active_id' => 0, 'title' => '');
        for ($i = 1; $i <= 10; ++$i)
        {
            $data['ask' . $i] = '';
            $data['answer' . $i] = '';
        }

    }

    $TEMPLATE['data'] = $data;
    $TEMPLATE['pageTitle'] = '编辑问答信息-问答管理';
    include TEMPLATE_PATH . '/admin/question_edit.php';
}
else if ('save' == $action)
{	// 保存
    $info = $_POST['info'];
    $info['title'] = addslashes($info['title']);
    foreach ($info as $k => $v)
    {
        $question_model->$k = $v;
    }
    if ($info['id']) {
        $question_model->sys_lastmodify = time();
        $ok = $question_model->save();
    } else {
        $question_model->sys_dateline = $question_model->sys_lastmodify = time();
        $question_model->sys_ip = getClientIp();
        $ok = $question_model->create();
    }
    if ($ok) {
        redirect('question.php');
    }
    else
    {
        echo '<script>alert("数据保存失败");history.go(-1);</script>';
    }
}
else if ('delete' == $action)
{	// 删除
    $id = getReqInt('id', 'get', 0);
    if ($id) {
        $question_model->id = $id;
        $question_model->sys_status = 1;
        $ok = $question_model->save($data);
    }
    if ($ok) {
        redirect($refer);
    } else {
        show_result("下线的时候出现错误", $refer);
    }
}
else if ('reset' == $action)
{	// 恢复
    $id = getReqInt('id', 'get', 0);
    $ok =false;
    if ($id) {
        $question_model->id = $id;
        $question_model->sys_status = 0;
        $ok = $question_model->save($data);
    }
    if ($ok)
    {
        redirect($refer);
    } else {
        show_result("下线的时候出现错误", $refer);
    }
}
else
{
    echo 'error question action';
}