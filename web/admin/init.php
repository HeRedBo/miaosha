<?php
/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2018/2/24 15:38
 * Desc:
*/
/**
 * @name init.php
 * @desc 后台文件初始化设置， 包含此目录需要的文件以及变量声明
 * @author red bo
 */
header('Content-Type: text/html;charset=utf-8');
include '../../conf/_local.inc.php';
include ROOT_PATH . './function/global.inc.php';
include ROOT_PATH . './function/function.inc.php';

// 调试探针，初始化完成，页面开始执行
\common\DebugLog::_time('_init.php, start page');
$TEMPLATE = [];
$login_user_info = get_login_user_info();
$TEMPLATE['login_user_info'] = $login_user_info;

/**
 * 验证登录授权
 * 
 * *************************** Password project ****************************
 */
if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
	$_SERVER['PHP_AUTH_USER'] !='miaosha' || $_SERVER['PHP_AUTH_PW'] !='miaosha'
)
{
	Header("WWW-Authenticate: Basic realm=\"Login\"");
	Header("HTTP/1.0 401 Unauthorized");
	echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
	exit;
}