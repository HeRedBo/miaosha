<?php 
/** 
 * 用户授权
 */

include 'init.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if('logout' == $action)
{
    clear_auth_cookie();
}
else
{

    // 默认就是登陆
    $id = rand(1, 10000000);
    $auth_cookie = [
        'uid' => $id, 
        'username' => '测试用户.' . $id
    ];
    save_auth_cookie($auth_cookie);

}

redirect('/');