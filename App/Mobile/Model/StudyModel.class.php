<?php
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class StudyModel extends Model
{
	//记录学习情况
	public function record($arr)
    {
    	$cur=date("Y-m-d");
    	$arr1["ks_code"]=$arr["ks_code"];
    	$arr1["wordflag"]=$arr["wordflag"];
    	$arr1["engs_studyid"]=$arr["id"];
    	$arr1["username"]=cookie("engm_username");
    	$arr1["usertype"]=cookie("engm_usertype");
    	$arr1["area_code"]=cookie("engm_areacode");
    	$arr1["studytype"]=$arr["studytype"];
		$arr1["addtimey"]=date("Y");
		$arr1["addtimem"]=date("m");
		$arr1["addtimed"]=date("d");
    	if($arr["id"]!=0){
			$sql="SELECT addtimey FROM `engs_study` WHERE ( username='%s' and wordflag=0 and engs_wordid=%d and ks_code='%s' ) ORDER BY id desc";
			$rs=M() -> query($sql,$username,$arr["id"],$arr["ks_code"]);
			if(!empty($rs)){
			   if($rs["addtime"]!=$cur){
				  $study_word -> add($arr1);
				  $arr_return["add"]=1;
			   }
			}else{
				  $study_word -> add($arr1);
				  $arr_return["add"]=1;
			}	
		}
		$this ->ajaxReturn($arr_return);  
    }
}