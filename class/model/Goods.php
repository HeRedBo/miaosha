<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/15
 * Time: 下午10:36
 */

namespace model;

 class Goods extends  \Mysql\Crud
 {
     protected  $table  = 'ms_goods';
     protected  $pk     = 'id';

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
      * 特定的活动的有效商品列表
      *
      * @param int $active_id 活动ID
      * @param int $status 活动状态ID
      */
     public function getListByActive($active_id = 0, $status = -1)
     {
         $params = ['active_id' => $active_id];
         if($status < 0)
         {
             $sql = "SELECT * FROM `" .$this->table. "` WHERE 
             active_id =:active_id ORDER BY `" .$this->pk."` DESC";
         }
         else
         {
             $sql = "SELECT * FROM `" .$this->table. "` WHERE 
             active_id =:active_id AND sys_status=:sys_status ORDER BY `" .$this->pk."` DESC";
             $params['sys_status'] = $status;
         }
         return $this->getDb()->query($sql, $params);
     }



 }