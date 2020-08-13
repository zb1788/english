<?php
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class WordBookModel extends Model
{
    protected $_validate = array(
        array('userid','require','用户名ID必须填写！'),
        array('wordid','require','单词ID必须填写'),
        array('ks_code','require','单元ksCode必须填写'),
    );


    //单词生词本
    public function addWordBook($array){
    	$this->userid=$array["userid"];
    	$this->ks_code=$array["ks_code"];
    	$this->wordid=$array["wordid"];
    	$this->areaid=$array["areacode"];
    	$this->source=$array["source"];
    	$this->isuse=1;
        $rs=$this->where("userid='%s' and ks_code='%s' and wordid=%d and areacode='%s' and isuse=1",$array["userid"],$array["ks_code"],$array["wordid"],$array["areacode"])->select();
        if(empty($rs)){
           $this->add($array); 
        } 
    }


    //单词生词本
    public function delWordBook($array){
    	$this->isuse=0;
    	$this->where("userid='%s' and ks_code='%s' and areacode='%s' and wordid='%s'",$array["userid"],$array["ks_code"],$array["areacode"],$array["wordid"])->save();
    }
}