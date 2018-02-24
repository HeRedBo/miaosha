<?php
namespace model;

class Active extends  \Mysql\Crud
{
    protected $table = 'ms_active';
    protected $pk    = 'id';

    public function getList($start = 0 , $end = 20)
    {
        $start = max(0, $start);
        $end = min(50, $end);
        $sql = "SELECT * FROM `" . $this->table . "` ORDER BY `$this->pk` DESC LIMIT $start, $end";
        return $this->getDb()->query($sql);
    }

    public function getListInUse()
    {
        global $now;
        $sql = "SELECT * from `". $this->table ."` WHERE sys_status=:sys_dateline AND time_end > :now 
        ORDER BY `" . $this->pk. "` DESC";
        $params = [
            'sys_dateline' =>  1,
            'now' => $now
        ];
        return $this->getDb()->query($sql, $params);
    }


}