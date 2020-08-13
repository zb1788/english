<?php
namespace Klx\Controller;
use Think\Controller;
/** 
* 首页控制器类
*  
* @author         gm 
* @since          1.0 
*/  

class CydictController extends CheckController {
	//成语分类标签
	public function cyclassfy(){
		$classtag=I("id/d",0);
		$rs=M("classfy")->where("parentid=%d",$classtag)->select();
		foreach($rs as $key=>$value){
			$rs[$key]["childclassfy"]=$this->cychildclassfy($value["id"],$value["id"]);
		}
		$this->ajaxReturn($rs);
	}

	//获取成语子分类
	private function cychildclassfy($parentid,$id){
		$sql="select *,(case when id=%d then 1 else 0 end) as flag from hl_classfy t where parentid=%d";
		$rs=M()->query($sql,$id,$parentid);
        //$rs=M("classfy")->where("parentid=%d",$parentid)->field("*,(case when id=)")->select();
		return $rs;
	}

	public function index(){
		$sql="select * from hl_cy_info where cygushi is not null and cyjinyici is not null and cyfanyici is not null";
		$rs=M()->query($sql);
		$startdate=strtotime(Date("y-m-d"));
		$enddate=strtotime("2015-01-04");
		$days=round(($startdate-$enddate))%1783;
		$this->assign("cyid",$rs[$days]["id"]);
		$this->assign("cyname",$rs[$days]["cyname"]);
		$this->assign("cypinyin",$rs[$days]["cypinyin"]);
		$this->assign("cyjieshi",$rs[$days]["cyjieshi"]);
		$this->assign("cydiangu",$rs[$days]["cydiangu"]);
		$this->assign("cyjinyici",$rs[$days]["cyjinyici"]);
		$this->assign("cyfanyici",$rs[$days]["cyfanyici"]);
		$this->assign("cygushi",$rs[$days]["cygushi"]);
		$this->display();
	}
    
    
     public function fenye(){
    	$pageCurrent=I('pageCurrent/d',0);
    	$page_size=I('page_size/d',0);
    	$id=I("id/d");
    	$Model=M("");
    	$sql_where=" where classfyid=".$id;
    	$sql='select count(*) as num from hl_wxw_cy_classfy';
    	$sql1="select * from hl_wxw_cy_classfy";
    
    	$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
    	$sql_order=' order by id';
    	//echo $sql.$sql_where.$sql_limit;exit();
    	$data_total=$Model->query($sql.$sql_where);
    	//var_dump($data_total);exit();
    	$total=$data_total[0]['num'];
    	$sub_pages=4;
    	/* 实例化一个分页对象 */
    	Vendor('SubPages');
    	$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
    	$page= $subPages->subPageCss4();
    	$data= $Model->query($sql1.$sql_where.$sql_order.$sql_limit);
    	//echo $sql.$sql_where.$sql_order.$sql_limit;
    	//var_dump($data);
    	$word[$page]=$data;
    	$this->ajaxReturn($word);
    }  

    public function fenye1(){
    	$pageCurrent=I('pageCurrent/d',0);
    	$page_size=I('page_size/d',0);
    	$id=I("id/d");
    	$Model=M("");
    	$sql_where=" where classfyid=".$id;
    	$sql='select count(*) as num from hl_wxw_cy_classfy';
    	$sql1="select * from hl_wxw_cy_classfy";
    
    	$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
    	$sql_order=' order by id';
    	//echo $sql.$sql_where.$sql_limit;exit();
    	$data_total=$Model->query($sql.$sql_where);

    	$total=$data_total[0]['num'];
    	$sub_pages=4;
    	/* 实例化一个分页对象 */
    	Vendor('SubPages');
    	$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
    	$page= $subPages->subPageCss4();
    	$data= $Model->query($sql1.$sql_where.$sql_order.$sql_limit);
    	var_dump($data);
    }

	//各分类下的成语
	public function getClassfyCy(){
		$id=I("id/d");
		$pageCurrent=I('pageCurrent/d',0);
    	$page_size=I('page_size/d',0);
    	$id=I("id/d");
    	$Model=M("");
    	$sql_where=" where classfyid=".$id;
    	$sql='select count(*) as num from hl_wxw_cy_classfy';
    	$sql1="select * from hl_wxw_cy_classfy";
    
    	$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
    	$sql_order=' order by id';
    	//echo $sql.$sql_where.$sql_limit;exit();
    	$data_total=$Model->query($sql.$sql_where);

    	$total=$data_total[0]['num'];
    	$sub_pages=4;
    	/* 实例化一个分页对象 */
    	Vendor('SubPages');
    	$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
    	$page= $subPages->subPageCss4();
    	$data= $Model->query($sql1.$sql_where.$sql_order.$sql_limit);
    	$word[$page]=$data;
    	$this->ajaxReturn($word);
	}

	//获取分类下的成语


	//由子分类找出父分类
	public function cychildparent(){
		$id=I("id");
		$parentid=M("classfy")->where("id=%d",$id)->find();
		$rs=M("classfy")->where("id=%d",$parentid["parentid"])->find();
		$rs["parent"]=$this->cychildclassfy($parentid["parentid"],$id);
		$rs["child"]=$this->cychildclassfy($id,$id);
		$this->ajaxReturn($rs);
	}

	//成语字典中具体的成语
	public function getcy(){
		$id=I("id");
		$rs=M("cy_info")->where("id=%d",$id)->find();
		$arr = explode('、',$rs['cyjinyici']);
		//var_dump($arr);
		$data=array();
		if(count($arr)>0){
			foreach($arr as $key=>$value){
				$jinrs=M("cy_info")->where("cyname='%s'",$value)->field("id,cyname")->find();
				$data[] = $jinrs;
		    }
		}
        $rs["jinyici"]=$data;
		$this->ajaxReturn($rs);
	}

	//成语字典详情页面
	public function cyinfo(){
		$id=I("id");
		$this->assign("id",$id);
		$this->display();
	}

	//成语搜索页面
	public function cysearchlist(){
		$pageCurrent=I('pageCurrent/d',0);
    	$page_size=I('page_size/d',0);
		$name=I("name");
		$flag=I("flag/d");
		$name1=I("name1/s",0);
		$name2=I("name2/s",0);
		$name3=I("name3/s",0);
		$name4=I("name4/s",0);
		$Model=M();
		$rs="";
		$sql_where="";
		$sql='select count(*) as num from hl_wxw_cy_info t';
    	$sql1="select * from hl_wxw_cy_info t";
    	$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
    	$sql_order=' order by id';
		if($flag==0){
			$sql_where=" where t.cyname like '%".$name."%'";
			$rs=M()->query($sql);
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
		$data_total=$Model->query($sql.$sql_where);
    	//var_dump($data_total);exit();
    	$total=$data_total[0]['num'];
    	$sub_pages=4;
    	Vendor('SubPages');
    	$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
    	$page= $subPages->subPageCss4();
    	$data= $Model->query($sql1.$sql_where.$sql_order.$sql_limit);
    	foreach($data as $key=>$value){
			$data[$key]["keyword"]=$this->getkeyword($value["id"]);
			if($name!=""&&$flag==0){
				$data[$key]["cyname"]=str_replace($name, "<span style='font-color:red;' class='font_y'>".$name."</span></font>", $value["cyname"]);
			}
			if($flag==1){
				if($name1!=""){
					$data[$key]["cyname"]=str_replace($name1, "<span style='font-color:red;' class='font_y'>".$name1."</span>", $value["cyname"]);
				}
				if($name2!=""){
					$data[$key]["cyname"]=str_replace($name2, "<span style='font-color:red;' class='font_y'>".$name2."</span>", $value["cyname"]);
				}
				if($name3!=""){
					$data[$key]["cyname"]=str_replace($name3, "<span style='font-color:red;' class='font_y'>".$name3."</span>", $value["cyname"]);
				}
				if($name4!=""){
					$data[$key]["cyname"]=str_replace($name4, "<span style='font-color:red;' class='font_y'>".$name4."</span>", $value["cyname"]);
				}
			}
		}
		$word[$page]=$data;
    	$this->ajaxReturn($word);
	}


	//成语分类
	private function getkeyword($id){
		$sql="select classfyname,classfyid from hl_wxw_cy_classfy t left join hl_wxw_classfy s on t.classfyid=s.id where cyid=%d";
		$rs=M()->query($sql,$id);
		//$rs=M("cy_classfy")->join("left join wxw_classfy on wxw_cy_classfy.classfyid=wxw_classfy.id")->where("cyid=%d",$id)->field("classfyname,classfyid")->select();
		return $rs;
	}


	//首页每日推荐
	public function main(){
		$startdate=strtotime(Date("y-m-d"));
		$enddate=strtotime("2009-12-05");
		$days=round(($enddate-$startdate)/3600/24)%4000;
		$rs=M("cy_info")->where("cygushi is not null and cyjinyici is not null and cyfanyici is not null")->order("id")->limit($days,1)->find();
		$this->assign("cyid",$rs["id"]);
		$this->assign("cyname",$rs["cyname"]);
		$this->assign("cypinyin",$rs["cypinyin"]);
		$this->assign("cyjieshi",$rs["cyjieshi"]);
		$this->assign("cydiangu",$rs["cydiangu"]);
		$this->assign("cyjinyici",$rs["cyjinyici"]);
		$this->assign("cyfanyici",$rs["cyfanyici"]);
		$this->assign("cygushi",$rs["cygushi"]);
		$this->display();
	}


	private function getPageCount($rs,$pagesize){
		$pageCount = count($rs)% $pageSize > 0 ? (count($rs)/ $pageSize+1):(count($rs)/ $pageSize);   
        $arr_return["pagecount"]=$pageCount;
        $arr_return["totalCount"]=count($rs);
        return $arr_return;
    }
    

    //成语搜索页面
    public function cysearch(){
		$name=I("name/s");
		$name1=I("name1/s");
		$name2=I("name2/s");
		$name3=I("name3/s");
		$name4=I("name4/s");
		$flag=I("flag/d");
		$this->assign("name",$name);
		$this->assign("name1",$name1);
		$this->assign("name2",$name2);
		$this->assign("name3",$name3);
		$this->assign("name4",$name4);
		$this->assign("flag",$flag);
		$this->display();

    }

    public function indexinfo(){
    	$id=I("id");
    	$this->assign("id",$id);
    	$rs=M("classfy")->where("parentid=%d",$id)->order(id)->find();
    	if(count($rs)){
    		$id=$rs["id"];
    	}
    	$this->assign("chiid",$id);
    	$this->display();
    }

}