<?php

namespace common;

class Datasource
{
    public static $redises = [];
    public static  $caches = [];

    public  function  __construct()
    {
    }
    public  static  function getRedis($config_name =NULL, $server_region = 'default')
    {
        if($config_name == NULL)
            return ;
        if(isset(self::$redises[$config_name]) && self::$redises[$config_name])
        {
            return self::$redises[$config_name];
        }
        global  $config;
        $redis_config = $config['redis'][$config_name];
        try
        {
            self::$redises[$config_name] = RedisHelper::instance($config_name, $redis_config, $server_region);
        }
        catch (\Exception $e)
        {
            self::$redises[$config_name] = null;
        }
        return self::$redises[$config_name];
    }
}

class RedisHelper
{
    private $_config_name = '';
    private $_redis_config = null;
    private $_server_region = null;
    public  $timeout =1;
    private $_redis = null;
    private static  $instances = [];
    private static  $connect_error = 0;
    private $call_error = 0;

    private  function  __construct($config_name, $redis_config, $sever_region)
    {
        if($config_name && $redis_config && $sever_region)
        {
            $this->_config_name   = $config_name;
            $this->_redis_config  = $redis_config;
            $this->_server_region = $sever_region;
            $this->timeout        = isset($this->_redis_config[$sever_region]['timeout']) ? $this->_redis_config[$sever_region]['timeout'] :$this->timeout;

            try
            {
                $this->_redis = new \Redis();
                $this->_redis->connect($this->_redis_config[$sever_region]['host'],$this->_redis_config[$sever_region]['port'], $this->timeout);
                if($this->_redis_config[$sever_region]['password'] && !$this->_redis->auth($this->_redis_config[$sever_region]['password']))
                {
                    $this->_redis = null;
                }
            }
            catch (\Exception $e)
            {
                $this->_redis = null;
            }
        }
        else
        {
            $this->_redis = null;
        }
    }

    public  static function instance($config_name, $redis_config, $server_region)
    {
        if(!$config_name || !$redis_config)
            return false;

        $start_time = microtime();
        $only_key = $config_name . ':' . $server_region;

        if(!isset(self::$instances[$only_key]))
        {
            try
            {
                self::$instances[$only_key] = new RedisHelper($config_name, $redis_config, $server_region);
                self::$connect_error = 0;
            }
            catch (\Exception $e)
            {
                if(self::$connect_error < 2)
                {
                    self::$connect_error += 1;
                    return RedisHelper::instance($config_name, $redis_config, $server_region);
                }
                else
                {
                    self::$connect_error = 0;
                    self::$instances[$only_key] = new RedisHelper(false, false, false);
                }
            }
        }

        $redis_config_info = [];
        if($redis_config && isset($redis_config[$server_region]) && isset($redis_config[$server_region]['password']))
        {
            $redis_config_info = $redis_config[$server_region];
            unset($redis_config_info['password']);
        }
        \common\DebugLog::_redis('redis_instance', $config_name, $redis_config_info, $start_time, microtime() , null);
        self::$connect_error =0;
        return self::$instances[$only_key];
    }

    public  function  __call($name, $arguments)
    {

        if(!$this->_redis)
        {
            return false;
        }
        $start_time = microtime();

        try
        {
            if('scan' == $name)
            {
                $data = $this->_redis->scan($arguments[0]);
            }
            else
            {
                $data = call_user_func_array([$this->_redis, $name], $arguments);
            }
        }
        catch (\Exception $e)
        {
            if($this->call_error < 2)
            {
                $this->call_error ++;
                return call_user_func_array([$this->_redis, $name], $arguments);
            }
            else
            {
                $this->call_error = 0;
            }
            $data = false;
        }

        $this->call_error = 0;
        $redis_config = $this->_redis_config[$this->_server_region];
        if($redis_config && isset($redis_config['password']))
            unset($redis_config['password']);
        \common\DebugLog::_redis($name, $arguments, $redis_config, $start_time, microtime() , (is_array($data) || is_string($data)) ? $data : null);
        return $data;
    }

    public function __destruct()
    {
        if($this->_redis != null)
        {
            $this->_redis->close();
        }
    }
}