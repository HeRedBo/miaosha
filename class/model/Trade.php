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

