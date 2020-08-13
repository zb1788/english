<?php
namespace Yuwen\Model;
use Think\Model;
class UnitModel extends Model{

    /**
     * 获取当前单元课文信息
     */
    public function getUnitContentInfo($ks_code){
        $sql = 'select style,content,ks_name from yw_unit where ks_code="%s"';
        return $this->query($sql,$ks_code);
    }


    public function getUnitWordInfo($ks_code){
        $sql = 'select style,word,ks_name from yw_unit where ks_code="%s"';
        return $this->query($sql,$ks_code);
    }







}
