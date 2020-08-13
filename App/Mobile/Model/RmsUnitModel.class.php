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
    	
        //flag表示类型0表示word 1表示课文 2表示考试
        if($flag==0){
           //$sql=$sql." and t.ks_code in (select ks_code from engs_word )";
		    $sql = 'select a.ks_code,a.ks_name_short,is_unit,sum(case when b.ks_code is not null then 1 else 0 end) as wordcount from engs_rms_unit as a left join engs_word b on a.ks_code = b.ks_code where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s"  group by a.ks_code having is_unit+wordcount>0 order by a.display_order';
        }else if($flag==1){
		   $sql='SELECT a.ks_code,a.ks_name_short,is_unit,SUM(CASE WHEN b.ks_code IS NOT NULL THEN 1 ELSE 0 END) AS textcount FROM engs_rms_unit AS a LEFT JOIN engs_text_chapter b ON a.ks_code = b.ks_code AND b.isdel=1 WHERE a.flag=1 AND a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s"  GROUP BY a.ks_code HAVING is_unit+textcount>0 ORDER BY a.display_order';
           //$sql=$sql." and t.ks_code in (select ks_code from engs_text_chapter)";
        }else if($flag==2){
			//$sql="select * from engs_rms_unit t where r_grade='%s' and r_volume='%s' and r_version='%s' ";
           //$sql=$sql." and t.ks_code in (select ks_code from engs_exams where engs_exams.exams_classid=1 and engs_exams.isdel = 1) or t.is_unit=1";
           //$sql=$sql." order by display_order";
		   $sql = 'select a.ks_code,a.ks_name_short,is_unit,sum(case when b.ks_code is not null then 1 else 0 end) as examscount from engs_rms_unit as a left join (select * from engs_exams where exams_classid != 3) b on a.ks_code = b.ks_code and b.state=4 and b.isdel = 1 where  a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s"  group by a.ks_code having is_unit+examscount>0 order by a.display_order';
        }
        //echo $sql;
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