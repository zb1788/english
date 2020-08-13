<?php
namespace Klx\Controller;
use Think\Controller;
/** 
* 首页控制器类
*  
* @author         gm 
* @since          1.0 
*/  

class DictpcController extends CheckController {
	//首页索引
	public function getClassfy(){
		$type=I("id/d");
		//笔画数
		$bh=I("i/d");
		$rs="";
        if($type!=3){
        	$rs=M("hanzi_classfy")->where("type=%d and parentid=0",$type)->order("id")->select();
			//dump($rs);
			foreach($rs as $key=>$value){
				$rs[$key]["cindex"]=$this->getcindex($value["id"],$type);
			}
        }else{
            $bihua="笔画为".$bh."的全部汉字";
        	$rs=M("hanzi_classfy")->where("title='%s' and parentid=0",$bihua)->order("id")->find();
			$rs["cindex"]=$this->getcindex($rs["id"],$type);
        }
		
		$this->ajaxReturn($rs);
	}
    //获取子节点
	private function getcindex($id,$type){
		return M("hanzi_classfy")->where("parentid=%d and type=%d",$id,$type)->select();
	}

	//获取成语页面
	public function gethanzi(){
		$id=I("id/s");
		$sql="SELECT *,GROUP_CONCAT(s.audioname) AS pinyin,GROUP_CONCAT(s.audio) AS audio FROM hl_hanzi_info t LEFT JOIN hl_hanzi_audio s ON t.name=s.hzname where t.name='".$id."' GROUP BY t.id";
        $rs=M()->query($sql);
        foreach($rs as $key=>$value){
        	$rs[$key]["cy"]=M("cy_info")->where("cyname='%s'",$value["name"])->select();
        }
        
        $this->ajaxReturn($rs);
	}

	public function classfy(){
		$type=I("type/d");
		$this->assign("id",I("id"));
		$this->assign("type",$type);
		$title="";
		if($type==1){
           $title="拼音查字";
		}else{
           $title="部首查字";
		}
		$this->assign("title",$title);
		$this->display();
	}


	public function info(){
		$id=I("id/s");
		$this->assign("id",$id);
		$this->display();

	}


	public function getSendClassfy(){
		
		$id=I("id/s");
		$text=I("text/s");
		$type=I("type/d");
		$rs="";
		//如果是拼音的话
		set_time_limit(600);
		if($type==1){
			
			$rs=M("hanzi_classfy")->where("parentid=%d and type=%d",$id,$type)->group("title")->select();
			foreach($rs as $key=>$value){
				$rss=M("hanzi_classfy")->where("title='%s'",$value["title"])->select();
				$rs[$key]["cindex"]=$rss;
				unset($rss);
			}
            
		}else if($type==2){
			G("run");
			$model=M("hanzi_info");
			$rs=$model->where("bushou='%s'",$id)->group("bihua")->field("bihua as title")->order("bihua")->select();
			unset($model);
			foreach($rs as $key=>$value){
				$rss=M("hanzi_info")->where("bihua='%s' and bushou='%s'",$value["title"],$id)->field("id,name as text")->select();
				$rs[$key]["cindex"]=$rss;
				unset($rss);
			}
			G("end");
			//echo G("run","end");

		}
		$this->ajaxReturn($rs);
	}
}