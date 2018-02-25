<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/24
 * Time: 上午1:06
 */

namespace model;

class Trade extends \Mysql\Crud
{
    protected  $table = 'ms_trade';
    protected  $pk    = 'id';


    /**
     * 分页查询， 不需要条件
     *
     * @param int $start 分页开始偏移位置
     * @param int $end 分页结束偏移位置
     * @return mixed 列表
     */
    public  function  getList($start = 0, $end = 50)
    {
        $start = max(0, $start);
        $end   = min(50, $end);
        $sql = "SELECT * FROM `"  .$this->table . "` ORDER BY `$this->pk` DESC LIMIT $start, $end";
        return $this->getDb()->query($sql);
    }


    /**
     * 获取特定用户的特定活动中的订单信息
     *
     * @param int  $uid 用户ID
     * @param int $active_id 活动id
     * @return mixed array
     */
    public  function  getUserTrade($uid, $active_id = 0)
    {
        $params = ['uid' => $uid];
        if($active_id)
        {
            $params['active_id'] = $active_id;
            $sql = "SELECT * FROM `" . $this->table. "` WHERE uid =:uid AND active_id =:active_id ORDER BY "
            .$this->pk . " DESC LIMIT 100";
        }
        else
        {
            $sql = "SELECT * FROM `" . $this->table. "` WHERE uid =:uid  ORDER BY "
                .$this->pk . " DESC LIMIT 100";
        }
        return $this->getDb()->query($sql, $params);
    }
}

