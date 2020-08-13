<?php
namespace Klx\Controller;
use Think\Controller;
/**
* 首页控制器类
*
* @author         gm
* @since          1.0
*/

class CymdictController extends Controller {
	//成语分类标签
	public function index(){
		$rs=M("classfy")->where("parentid=0")->select();
		foreach($rs as $key=>$value){
			$rs[$key]["childclassfy"]=array();
		}
		$this->assign("list",$rs);
		$this->display();
	}



	public function getSencondClassfy(){
		$id=I("id");
       $rs=M("classfy")->where("parentid=%d",$id)->select();
       $this->ajaxReturn($rs);
	}

	//获取某一个分类下的成语
	public function classfycy(){
		$id=I("id");
		$suoyin=I("suoyin");
        $jiegou=I("jiegou");
		$cyrs=array();

		$rs=M("classfy")->where("parentid=%d",$id)->select();
		//dump($rs);
		if(count($rs)>0){

			foreach($rs as $key=>$value){

				$crs=M("cy_classfy")->where("classfyid=%d",$value["id"])->select();
				//dump($crs);
				if($key==0){
					$cyrs=$crs;
				}else{
					$cyrs=array_merge($cyrs,$crs);
				}

			}
		}else{
			$cyrs=M("cy_classfy")->where("classfyid=%d",$id)->select();
		}
		$this->assign("id",$id);
		$this->assign("suoyin",$suoyin);
		$this->assign("jiegou",$jiegou);
		$this->assign("cylist",$cyrs);
		$this->display();
	}





	//成语字典中具体的成语
	public function cyinfo(){
		$id=I("id");
		$classfyid=I("classfyid/d");
		$jiegou=I("jiegou");
		$suoyin=I("suoyin");
		$source=I("source/d");
		$rs=M("cy_info")->where("id=%d",$id)->find();
		//$arr = explode('、',$rs['cyjinyici']);
		//var_dump($arr);
		// $data=array();
		// if(count($arr)>0){
		// 	foreach($arr as $key=>$value){
		// 		$jinrs=M("cy_info")->where("cyname='%s'",$value)->field("id,cyname")->find();
		// 		$data[] = $jinrs;
		//     }
		// }
        //$rs["jinyici"]=$data;
        $this->assign("source",$source);
        $this->assign("classfyid",$classfyid);
        $this->assign("suoyin",$suoyin);
        $this->assign("jiegou",$jiegou);
        $this->assign("flag",I("flag/d"));
        $this->assign("name",I("name"));
        $this->assign("name1",I("name1"));
        $this->assign("name2",I("name2"));
        $this->assign("name3",I("name3"));
        $this->assign("name4",I("name4"));
        $this->assign("cyname",$rs["cyname"]);
        $this->assign("cypinyin",$rs["cypinyin"]);
        $this->assign("cyjieshi",$rs["cyjieshi"]);
        $this->assign("cydiangu",$rs["cydiangu"]);
        $this->assign("cycycd",$rs["cycycd"]);
        $this->assign("cygqsc",$rs["cygqsc"]);
        $this->assign("cyyfyf",$rs["cyyfyf"]);
        $this->assign("cyjiegou",$rs["cyjiegou"]);
        $this->assign("cyjinyici",$rs["cyjinyici"]);
        $this->assign("cyfanyici",$rs["cyfanyici"]);
        $this->assign("cygushi",$rs["cygushi"]);
        $this->assign("cycsnd",$rs["cycsnd"]);
        $this->display();
	}




	//汉字转拼音
	public function search(){
		$name=trim(I("name"));
		$flag=I("flag/d");
		$name1=trim(I("name1/s",0));
		$name2=trim(I("name2/s",0));
		$name3=trim(I("name3/s",0));
		$name4=trim(I("name4/s",0));
    	$sql="select * from hl_wxw_cy_info t";
    	$sql_order=' order by id';
		if($flag==0){
			$sql_where=" where t.cyname like '%".$name."%' or quanpin like '%".$name."%' or head like '%".$name."%'";

		}elseif($flag==1){
			$sql_where=" where ";
			if(!empty($name1)){
                $sql_where=$sql_where." SUBSTR(t.cyname,1,1)='".$name1."' ";
			}
			if(!empty($name2)){
				if(!empty($name1)){
					$sql_where=$sql_where." and SUBSTR(t.cyname,2,1)='".$name2."' ";
				}else{
					$sql_where=$sql_where."  SUBSTR(t.cyname,2,1)='".$name2."' ";
				}
			}
			if(!empty($name3)){
				if(empty($name1)&&empty($name2)){
                   $sql_where=$sql_where."  SUBSTR(t.cyname,3,1)='".$name3."' ";
				}else{
					$sql_where=$sql_where." and SUBSTR(t.cyname,3,1)='".$name3."' ";
				}
			}
			if(!empty($name4)){
				if(empty($name1)&&empty($name2)&&empty($name3)){
					$sql_where=$sql_where."  SUBSTR(t.cyname,4,1)='".$name4."' ";
				}else{
					$sql_where=$sql_where." and SUBSTR(t.cyname,4,1)='".$name4."' ";
				}

			}
		}
		$rs=M()->query($sql.$sql_where.$sql_order." limit 10000");
		foreach($rs as $key=>$value){
			if($name!=""&&$flag==0){
				$rs[$key]["cyname"]=str_replace($name, "<span class='font_y'>".$name."</span>", $value["cyname"]);
			}
			if($flag==1){
				if($name1!=""){
					$rs[$key]["cyname"]=str_replace($name1, "<span class='font_y'>".$name1."</span>", $value["cyname"]);
				}
				if($name1!=""){
					$rs[$key]["cyname"]=str_replace($name2, "<span class='font_y'>".$name2."</span>", $value["cyname"]);
				}
				if($name1!=""){
					$rs[$key]["cyname"]=str_replace($name3, "<span class='font_y'>".$name3."</span>", $value["cyname"]);
				}
				if($name1!=""){
					$rs[$key]["cyname"]=str_replace($name4, "<span class='font_y'>".$name4."</span>", $value["cyname"]);
				}
			}
		}
		//echo count($rs);exit;
		$this->assign("name",$name);
		$this->assign("name1",$name1);
		$this->assign("name2",$name2);
		$this->assign("name3",$name3);
		$this->assign("name4",$name4);
		$this->assign("flag",$flag);
		$this->assign("cylist",$rs);
		$this->assign("cycount",count($rs));
		$this->display();
	}

}
