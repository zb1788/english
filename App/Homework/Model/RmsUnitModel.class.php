<?php
namespace Mobile\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class RmsUnitModel extends Model
{
	//获取单元信息
	public function getUnitListData($arr,$flag)
    {
    	$rms_unit=M();
    	$a=0;
    	$sql="select * from engs_rms_unit t where r_grade='%s' and r_volume='%s' and r_version='%s' ";
        //flag表示类型0表示word 1表示课文 2表示考试
        if($flag==0){
           $sql=$sql." and t.ks_code in (select ks_code from engs_word )";
        }else if($flag==1){
           $sql=$sql." and t.ks_code in (select ks_code from engs_text_chapter)";
        }else if($flag==2){
           $sql=$sql." and t.ks_code in (select ks_code from engs_exams where engs_exams.exams_classid=1 and engs_exams.isdel = 1)";
        }
        //echo $sql;
        $sql=$sql." order by display_order";
    	$rs=$rms_unit->query($sql,$arr["grade"],$arr["volume"],$arr["version"]);
    	foreach($rs as $key=>$value){
    		$a=$a+$value["is_unit"];
    	}
    	$arr_return["result"]=$rs;
        $arr_return["len"]=count($rs);
        $arr_return["unit"]=$a;
    	return $arr_return;
    }
}