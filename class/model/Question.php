<?php
/**
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/2/23
 * Time: 下午9:23
 */

namespace  model;

class Question extends \Mysql\Crud
{
    protected  $table = 'ms_question';
    protected  $pk = 'id';

    public  function  getList($start = 0 , $end= 50)
    {

    }

    public  function  getActiveQuestion($aid)
    {
        $sql = "SELECT * FROM `"  . $this->table. "` WHERE active_id='" . $aid."' AND sys_status=0 LIMIT 1";
        return $this->getDb()->row($sql);
    }
}