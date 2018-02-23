<?php

/**
 * @name init.php
 * @desc 文件初始化设置， 包含此目录需要的文件以及变量声明
 * @author red bo
 */

header('Content-Type: text/html;charset=utf-8');

include '../conf/_local.inc.php';

include ROOT_PATH . '/function/global.inc.php';
include ROOT_PATH . '/function/function.inc.php';

// 调试探针，初始化完成，页面开始执行
\common\DebugLog::_time('_init.php, start page');
$TEMPLATE = [];
$login_user_info = get_login_user_info();
$TEMPLATE['login_user_info'] = $login_user_info;
$now = time();




