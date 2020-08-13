<?php
namespace Yuwen\Model;
use Think\Model;
class LibZiModel extends Model{

    /**
     * 获取当前单元课文信息
     */
    public function getWordInfo($wordinfo){
        $sql = 'SELECT bushou,jiegou,zongbihua FROM yw_lib_zi l WHERE zi="%s"';
        $data = $this->query($sql,$wordinfo);

        $info = array();
        foreach($data as $v){
            $sql = 'SELECT zi_pinyin,py,cizu FROM yw_lib_cixing WHERE zi= "%s"';
            $re =
        }
    }








}
