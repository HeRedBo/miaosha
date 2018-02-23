<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/15
 * Time: 下午11:08
 */

error_reporting(E_ALL ^ E_DEPRECATED);

function create_sign($params = null)
 {
    if($params && is_array($params))
    {
        ksort($params);
        $str = '2018_miaosha';
        foreach ($params as $key => $value)
        {
            if($key != 'sign')
            {
                $str .= $key . $value;
            }
        }
        return strtoupper(md5($str));
    }
    return '';
 }

function save_auth_cookie($auth_cookie)
{
    $now = time();
    header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
    $data_list = [];
    foreach ($auth_cookie as $key => $value)
    {
        $data_list[] = $key . '=' . $value;
    }
    $sign = create_sign($auth_cookie);
    $data_list[] = 'sign='. $sign;
    $cookie = implode('&', $data_list);
    $expire = isset($auth_cookie['third_expires']) ? $auth_cookie['third_expires'] : time();
    if($expire <= $now)
    {
        $expire = $now + 86400;
    }
    setcookie(AUTH_COOKIE_NAME, $cookie, $expire,'/', '');
}

function clear_auth_cookie()
{
    setcookie(AUTH_COOKIE_NAME, '', 1, '/', '');
}

function get_login_user_info()
{
    $data_list = [];
    $cookie = null;
    if(isset($_COOKIE[AUTH_COOKIE_NAME]))
    {
        $cookie = $_COOKIE[AUTH_COOKIE_NAME];
    }
    if($cookie)
    {
        $arr_cookie = explode('&', $cookie);
        foreach ($arr_cookie as $str_cookie)
        {
            $index = strpos($str_cookie, '=');
            if ($index !== false)
            {
                $data_list[substr($str_cookie, 0, $index)] = urldecode(substr($str_cookie, $index + 1));
            }
            else
            {
                $data_list[$str_cookie] = '';
            }
        }
        $sign = $data_list['sign'];
        $new_sign = create_sign($data_list);
        if($sign != $new_sign)
        {
            // cookie 校验失败
            return null;
        }
        else
        {
            return $data_list;
        }

    }
    return null;
}


/**
 * 根据明文生成密文
 *
 * @param $pwd
 * @return string
 */
function create_password($pwd)
{
    return md5('84_miaosha'. $pwd);
}


function redirect($url)
{
    if(!empty($url))
    {
        header("location:". $url);
    }
    exit;
}

/**
 * 接收用户输入值- 整型
 *
 * @param string $name 变量的名称
 * @param string $method 接受方式 GET &  POST & REQUEST
 * @param int $default  默认值
 * @param bool $min 最小值
 * @param bool $max 最大值
 *  @return int|mixed
 */
function getReqInt($name, $method="REQUEST", $default =0, $min = false, $max = false)
{
    $method = strtoupper($method);
    switch ($method)
    {
        case 'POST':
            $variable = $_POST;
            break;
        case 'GET':
            $variable = $_GET;
            break;
        default:
            $variable = $_REQUEST;
            break;
    }
    if(!isset($variable[$name]) || $variable[$name] == '')
        return $default;

    $value = intval($variable[$name]);
    if($min !== false)
        $value = max($value, $min);
    if($max !== false)
        $value = min($value, $max);
    return $value;
}

/**
 * 返回数据json 格式的内容
 * @param $result
 */
function return_result($result)
{
    echo json_encode($result);
    exit;
}

function show_result($result , $url = '/')
{
    if(isset($result['error_no']) && isset($result['error_msg']))
    {
        echo '<script>
        alert("异常代码: ' . $result['error_no']. ' \n异常信息 ' . $result['error_msg'] . '");
        </script>';
    }
    else
    {
       echo '<script>
       alert(" ' .$result.'");
        </script>';
    }
    echo '<script>location.href="' .$url. '"</script>';
    exit();
}


/**
 * @name getClientIp
 * @desc 获得客户端ip
 * @return  string client ip
 */
function getClientIp()
{
    $online_ip = '';
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $online_ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $online_ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $online_ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $online_ip = $_SERVER['REMOTE_ADDR'];
    }
    return $online_ip;
}


/**
 * 加解密的密钥
 *
 * @return string
 */

function signKey()
{
    return pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
}


function signQuestion($info)
{
    $key = signKey();
    $plaintext = json_encode($info);
    # 为 CBC 模式创建随机的初始向量
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv      = mcrypt_create_iv($iv_size , MCRYPT_RAND);

    /**
     * 创建一个AES 兼容的密文 Rijndael 分组大小 = 128）
     * 仅适用于编码后的输入不是以 00h 结尾的
     * （因为默认是使用 0 来补齐数据）
     */
    $cipher_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
    # 将初始向量附加在密文之后，以供解密时使用
    $cipher_text = $iv. $cipher_text;

    # 对密文进行 base64 编码
    $cipher_text_base64 = base64_encode($cipher_text);
    return $cipher_text_base64;
}


/**
 * 将字符串解开,得到问答信息
 * @param $cipher_text_base64
 */
function unSignQuestion($cipher_text_base64)
{
    $key = signKey();
    # === 警告 ===
    # 密文并未进行完整性和可信度保护，
    # 所以可能遭受 Padding Oracle 攻击。
    # --- 解密 ---
    $cipher_text_dec = base64_decode($cipher_text_base64);
    # 初始向量大小，可以通过 mcrypt_get_iv_size() 来获得
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    # 获取除初始向量外的密文
    $iv_dec = substr($cipher_text_dec, 0, $iv_size);
    $cipher_text_dec = substr($cipher_text_dec, $iv_size);
    # 可能需要从明文末尾移除 0
    $plain_text_dec  = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $cipher_text_dec, MCRYPT_MODE_CBC, $iv_dec);
    return $plain_text_dec;
}










