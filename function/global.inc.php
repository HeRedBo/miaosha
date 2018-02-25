<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/15
 * Time: 下午11:08
 */

if(PHP_VERSION < '5.0.0')
{
    echo 'PHP VERSION MUST > 5';
    exit;
}

//默认将显示错误关闭
ini_set('display_errors', true);

//默认将读外部文件的自动转义关闭
ini_set("magic_quotes_runtime", 0);

//设置默认时区
date_default_timezone_set('PRC');

// 调试参数 __debug 的值
define('_DEBUG_PASS', 'miaosha_debug'); // TODO: 为了避免调试信息泄漏，请定义自己的密钥
// 是否开启调试状态
define('_IS_DEBUG', false);
// 异常信息等级
define('_ERROR_LEVEL', E_ALL);


class SYSCore
{
    public  static  function  registerAutoload($class = 'SYSCore')
    {
        spl_autoload_register([ $class, 'autoload']);
    }

    public  static  function  unRegisterAutoload($class)
    {
        spl_autoload_unregister([$class,'autoload']);
    }
    /**
     * 类文件的的自动载入
     *
     * @param $class_name
     * @return bool|mixed
     */
    public  static function  autoload($class_name)
    {
        if(
            strpos($class_name,'common')=== 0
            || strpos($class_name,'model') === 0
            || strpos($class_name,'Mysql') === 0
            || strpos($class_name,'Curl') === 0
            || strpos($class_name,'controller') === 0
        )
        {
            // 系统内部自定义的类域名空间
        }
        else
        {
            return true;
        }

        $class_name = str_replace('\\','/', $class_name);
        $class_path = CUSTOM_CLASS_PATH . DIRECTORY_SEPARATOR .$class_name.'.php';
        $class_path = str_replace('//','/',$class_path);
        if(file_exists($class_path))
        {
            return include_once  $class_path;
        }
        else
        {
            echo "file not exists class_path ={$class_path}";
        }
        return false;
    }
}

SYSCore::registerAutoload();

/**************** Debug Begin  ***************/

if(defined('_IS_DEBUG') && _IS_DEBUG || (isset($_REQUEST['__debug']) && strpos($_REQUEST['__debug'],_DEBUG_PASS) !== false))
{
    $debug_level = (isset($_REQUEST['__debug']) && strpos($_REQUEST['__debug'],_DEBUG_PASS) !== false) ? intval(substr($_REQUEST['__debug'], -1)) : 1;
    // $debug_level = intval(substr($_REQUEST['__debug'], -1));
    if($debug_level > 0 )
    {
        define('DEBUG_LEVEL', $debug_level);
    }
    else
    {
        define('DEBUG_LEVEL', 1);
    }
    // Debug 模式将错误打开
    ini_set('display_errors', true);
    // 设置错误级
    error_reporting(_ERROR_LEVEL);
    // Debug开关打开
    common\DebugLog::_init();

    // 注册shutdown 函数用来Debug显示;
    register_shutdown_function(['common\DebugLog','_show']);
}
else
{
    define('DEBUG_LEVEL', 0);
}
/**************** Debug End ***************/

if(defined('AUTOLOAD_CONF_PATH'))
{
    $handle = opendir(AUTOLOAD_CONF_PATH);
    if($handle)
    {
        while($file = readdir($handle))
        {
            if(substr($file, -8) == '.ini.php' && is_file(AUTOLOAD_CONF_PATH . DIRECTORY_SEPARATOR . $file))
            {
                include AUTOLOAD_CONF_PATH . DIRECTORY_SEPARATOR . $file;
            }
        }
        unset($handle, $file);
    }

}



