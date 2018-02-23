<?php
/**
 * @name _local.inc.php
 * @desc 配置文件
 * @caution 路径和 URL 不要加反斜线
 */


/***************************************** 项目配置常量开始 *****************************************/

// 此项目和根目录URL
define('ROOT_DOMAIN','http://'. (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1'));

// 此项目绝对地址
define('ROOT_PATH', substr(dirname(__FILE__),0,-4));
define('APP_PATH',ROOT_PATH.'/web');

define('LOG_PATH',ROOT_PATH. 'log');
// 配置文件地址
define('AUTOLOAD_CONF_PATH',ROOT_PATH .'/conf');
define('CUSTOM_CLASS_PATH', ROOT_PATH . '/class');
define('TEMPLATE_PATH',ROOT_PATH .'/views');
/***************************************** 项目配置常量结束 *****************************************/

define('AUTH_COOKIE_NAME', 'miaosha_auth');
