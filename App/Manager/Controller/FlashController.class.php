<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 单词控制器
*  
* @author         gm 
* @since          1.0 
*/
class FlashController extends CheckController {
    /** 
	* flashedit 
	* 向此单元下添加新的单词
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function editflashword(){
        $id=I("id");
        $title=I("title");
        $unit=I("unitid");
        $ids=I("ids");
        $flash=M("flash");
        $flash_word=M("flash_word");
        $arr_return["isadd"] = 0;
		$arr_return["msg"] = "更新失败";
		$flash -> unitid = $unit;
		$flash -> title = trim($title);
		$flash -> flashtype=0;
        $flash -> addtime = date("Y-m-d H:i:s",time());
        if($id==0)
        { 
            //新增flash
            //echo "fasdfadsfadsf";

        	$id = $flash -> add();		    
        }
        else
        {  //修改flash
        	$rss = $flash -> where("id = %d",$id) ->save();
            $rsd = $flash_word -> where("flashid = %d",$id) ->delete();
        }
       
    	$wordlist = explode(",", $ids);
        foreach($wordlist as $key=>$value)
        { 
            $flash_word -> flashid =$id;   
	        $flash_word -> wordid = $value; 
	        $flash_word -> add();
	        $arr_return["isadd"] = 1;
		    $arr_return["msg"] = "更新成功";
        }
		$this->ajaxReturn($arr_return);
	}

    /** 
	* getWordFlashList  
	* 向此单元下添加新的单词
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function getFlashWordList(){
	    $unit=I("unit");
	    $model = new \Think\Model();
	    $sql="SELECT t.id,t.title,GROUP_CONCAT((SELECT  MAX(p.word)  FROM engs_word p  WHERE p.id = s.wordid) ) wordlist, t.unitid FROM engs_flash t  LEFT JOIN engs_flash_word s  ON s.flashid = t.id  WHERE t.unitid=%d and t.flashtype=0 GROUP BY t.id,t.title,t.unitid";
        $rs=$model -> query($sql,$unit,$id);
	    if(!$rs){$rs=0;} 
	    $this ->ajaxReturn($rs);
	}

	/** 
	* falshDel  
	* 向此单元下删除falshTextDel
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function falshDel() {
	    $flashid=I("flashid");
	    $flash=M("flash");
	    $flash_word=M("flash_word");
	    $rsd=$flash -> where("id= %d",$flashid) -> delete();
	    $frsd=$flash_word -> where("flashid=%d",$flashid) -> delete();
	    if(false==$rsd && false==$frsd){
	    	$arr_return["isadd"] = 0;
		    $arr_return["msg"] = "更新失败";
		    $this->ajaxReturn($arr_return);

	    }
	    else{
	    	$arr_return["isadd"] = 1;
		    $arr_return["msg"] = "更新成功";
		    $this->ajaxReturn($arr_return);
	    }
	}


	/** 
	* falshDel  
	* 向此单元下删除falshTextDel
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function falshTextDel() {
	    $flashid=I("flashid");
	    $flash=M("flash");
	    $flash_text=M("flash_text");
	    $rsd=$flash -> where("id= %d",$flashid) -> delete();
	    $frsd=$flash_text -> where("flashid=%d",$flashid) -> delete();
	    M("flash_picture")->where("flashid=%d",$flashid)->delete();
	    if(false==$rsd && false==$frsd){
	    	$arr_return["isadd"] = 0;
		    $arr_return["msg"] = "更新失败";
		    $this->ajaxReturn($arr_return);

	    }
	    else{
	    	$arr_return["isadd"] = 1;
		    $arr_return["msg"] = "更新成功";
		    $this->ajaxReturn($arr_return);
	    }
	}

    /** 
	* getWordFlashList  
	* 向此单元下添加新的单词
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function getWordFlashList() {
	   $unitid=I("unitid");
	   $flashid=I("flashid");
	   if(!empty($unitid))
	   {
	   	 if(empty($flashid))
	   	 {

            $model = new \Think\Model();
	        $sql="select t.word,t.id,max(CASE WHEN s.flashid IS NULL THEN 0 ELSE 1 END) ischecked from engs_word t LEFT JOIN engs_flash_word s ON t.id=s.wordid  where  t.unitid= %d and t.isdel=1 group by t.word,t.id";
            $rs=$model -> query($sql,$unitid);
	     }
	     else
	     {
	     	$model = new \Think\Model();
	        $sql="select t.word,t.id,max(CASE WHEN s.flashid = %d THEN 1 ELSE 0 END) ischecked from engs_word t LEFT JOIN engs_flash_word s ON t.id=s.wordid  where  t.unitid= %d and t.isdel=1 group by t.word,t.id";
            $rs=$model -> query($sql,$flashid,$unitid);
	     }
	   }
	   else
	   {
	   	 $rs=0;	   	 
	   }
	   $this -> ajaxReturn($rs);
	}
    

    /** 
	* flashwordedit  
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function flashwordedit() {
		$gradeid=I("gradeid/d");
		$this->assign("gradeid",$gradeid);
		$termid=I("termid/d");
		$this->assign("termid",$termid);
		$versionid=I("versionid/d");
		$this->assign("versionid",$versionid);
		$unitid=I("unitid/d");
		$this->assign("unitid",$unitid);
		$id=I("id/d",0);
		$this->assign("id",$id);
		$flash=M("flash");
		$word=M("word");
		$model=new \Think\Model();
		$wordcount=$word -> where("unitid=%d and isdel=1",$unitid)->count();
		$this -> assign("wordcount",$wordcount);
		$rs=$flash -> where("id=%d",$id)->field('title') ->find();
		if(!empty($rs)){
		   	$this->assign("title",$rs["title"]);
		    $sql="select count(*) as checkcount from engs_flash_word where flashid=%d";
		    $count=$model -> query($sql,$id);
		    $this -> assign('checkcount',$count[0]['checkcount']);
		}else{
		    $rs=M("unit")->where("id=%d and isdel=1",$unitid)->field("unitname")->find();
		    $this->assign("title",$rs["unitname"]);
		    $sql="select count(*) as checkcount from engs_flash_word where flashid in (select id from engs_flash where unitid=%d)";
		    $count=$model -> query($sql,$unitid);
		    $this -> assign('checkcount',$count[0]['checkcount']);
		}
		$this -> display();
	}

	public function setTitle() {
		$unitid=I("unitid/d");
		$rs=M("unit")->where("id=%d and isdel=1",$unitid)->find();
		$this->ajaxReturn($rs);
	}


	/** 
	* getTextFlashList  
	* 获取此flash下的text列表
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function getFlashTextList(){
	    $unit=I("unit");
	    $model = new \Think\Model();
	    $sql="SELECT t.id,t.title,t.flag,GROUP_CONCAT((SELECT MAX(et.chapter) FROM engs_text_chapter et WHERE et.id=s.chapterid order by et.id)) AS chapterlist FROM engs_flash t LEFT JOIN engs_flash_text s ON t.id=s.flashid WHERE t.flashtype=1 and t.unitid=%d GROUP BY t.id,t.title,t.flag";
        $rs=$model -> query($sql,$unit);
	    if(!$rs){$rs=0;} 
	    $this ->ajaxReturn($rs);
	}

	/** 
	* getTextFlashList  
	* 向此单元下添加新的单词
	* 
	*/
	public function getTextFlashList() {
	   $unitid=I("unitid");
	   $flashid=I("flashid");
	   if(!empty($unitid))
	   {
	   	 if(empty($flashid))
	   	 {

            $model = new \Think\Model();
	        $sql="SELECT  t.chapter,t.id,t.issection,MAX(CASE WHEN s.flashid IS NULL  THEN 0  ELSE 1  END) ischecked FROM engs_text_chapter t  LEFT JOIN engs_flash_text s  ON t.id = s.chapterid WHERE t.unitid =%d  AND t.isdel = 1 GROUP BY t.chapter,t.issection,t.id ORDER BY id ";
            $rs=$model -> query($sql,$unitid);
	     }
	     else
	     {
	     	$model = new \Think\Model();
	        $sql="SELECT t.chapter,t.id,t.issection,MAX(CASE WHEN s.flashid = %d  THEN 1  ELSE 0  END) ischecked FROM engs_text_chapter t  LEFT JOIN engs_flash_text s  ON t.id = s.chapterid WHERE t.unitid = %d  AND t.isdel = 1 GROUP BY t.chapter,t.issection,t.id ORDER BY id";
            $rs=$model -> query($sql,$flashid,$unitid);
	     }
	   }
	   else
	   {
	   	 $rs=0;	   	 
	   }
	   $this -> ajaxReturn($rs);
	}


	/** 
	* flashtextedit  
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function flashtextedit() {
		$gradeid=I("gradeid/d");
		$this->assign("gradeid",$gradeid);
		$termid=I("termid/d");
		$this->assign("termid",$termid);
		$versionid=I("versionid/d");
		$this->assign("versionid",$versionid);
		$unitid=I("unitid/d");
		$this->assign("unitid",$unitid);
		$id=I("id/d",0);
		$this->assign("id",$id);
		$flashpic=0;
		$flash=M("flash");
		$textchapter=M("text_chapter");
		$rs=$flash -> where("id=%d",$id)->field('title,templetid') ->find();
		if(!empty($rs)){
			$this->assign("templetid",$rs["templetid"]);
			if($rs["templetid"]==4){
				$picrs=M("flash_picture")->where("flashid=%d",$id)->find();
				if(!empty($picrs)){
					$flashpic=1;
				}
			}
		   	$this->assign("title",$rs["title"]);
		}else{
			$this->assign("templetid","1");
		    $rs=M("unit")->where("id=%d and isdel=1",$unitid)->field("unitname")->find();
		    $this->assign("title",$rs["unitname"]);
		}
		$this->assign("flashpic",$flashpic);
		$this -> display();
		}


	/** 
	* editflashtext  
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function editflashtext(){
        $id=I("id");
        $title=I("title");
        $unit=I("unitid");
        $templetid=I("templetid");
        $ids=I("ids");
        $flash=M("flash");
        $flash_text=M("flash_text");
        $arr_return["isadd"] = 0;
		$arr_return["msg"] = "更新失败";
		$flash -> unitid = $unit;
		$flash -> title = trim($title);
		$flash -> flashtype=1;
		$flash -> templetid=$templetid;
        $flash -> addtime = date("Y-m-d H:i:s",time());
        if($id==0)
        { 
        	$id = $flash -> add();
        	M("flash_picture")->where("flashid=0")->setfield("flashid",$id);		    
        }
        else
        {  //修改flash
        	$rss = $flash -> where("id = %d",$id) ->save();
            $rsd = $flash_text -> where("flashid = %d",$id) ->delete();
        }
    	$wordlist = explode(",", $ids);
        foreach($wordlist as $key=>$value)
        { 
            $flash_text -> flashid =$id;   
	        $flash_text -> chapterid = $value; 
	        $flash_text -> add();
	        $arr_return["isadd"] = 1;
		    $arr_return["msg"] = "更新成功";
        }
        
		$this->ajaxReturn($arr_return);
	}

	
	/** 
	* flashtextpic  
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function flashtextpic(){
        $flashid=I("flashid");
        $chapterid=I("chapterid");
        $this -> assign("chapterid",$chapterid);
        $this -> assign("flashid",$flashid);
		$this->display();
	}

	/** 
	* getflashtextpic  
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function getflashtextpic(){
        $chapterid=I("chapterid");
        $flashid=I("flashid");
        $sql="SELECT s.id as picid,t.id,t.encontent,s.picpath FROM engs_text t LEFT JOIN engs_flash_picture s ON t.id=s.textid AND s.flashid=%d WHERE t.isdel=1  AND t.chapterid=%d";
        $model=M();
        $rs=$model->query($sql,$flashid,$chapterid);
		$this->ajaxReturn($rs);
	}


	/** 
	* flashtextpicedit
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function flashtextpicedit(){
		$flashid=I("flashid/d");
        $user =  stripslashes(I("user"));  
		$user = rtrim($user ,'"'); 
		$user = ltrim($user ,'"');
		$user = str_replace('&quot;', '"', $user);
		$res=json_decode($user);
		$arr_return["msg"]=0;
        $arr_return["err"]="修改失败";
     	foreach($res as $obj){ 
     		$flash_picture=M("flash_picture");
        	$textid = $obj->textid;
        	$picid = $obj->picid;
        	$picpath = $obj->picpath;
        	if(!empty($picid)){
        		$flash_picture -> flashid=$flashid;
	        	$flash_picture -> textid=$textid;
	        	$flash_picture -> picpath=$picpath;
	        	$flash_picture -> addtime=Date("y-m-d H:i:s");
	        	$result=$flash_picture -> where("id=%d",$picid) ->save();
        	}else{
        		$flash_picture -> flashid=$flashid;
	        	$flash_picture -> textid=$textid;
	        	$flash_picture -> picpath=$picpath;
	        	$flash_picture -> addtime=Date("y-m-d H:i:s");
	        	$flash_picture ->add();
        	}
        }
        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
	}


	/** 
	* flashuserpicedit
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息editflashuserpic
	*/
	public function flashuserpicedit(){
		$userid=I("userid/d");
        $gradeid=I("gradeid/d");
        $termid=I("termid/d");
        $versionid=I("versionid/d");
        $this->assign("userid",$userid);
        $this->assign("gradeid",$gradeid);
        $this->assign("termid",$termid);
        $this->assign("versionid",$versionid);
        $this->display();
	}

	/** 
	* editflashuserpic
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function editflashuserpic(){
		$userid=I("userid/d");
        $gradeid=I("gradeid/d");
        $termid=I("termid/d");
        $versionid=I("versionid/d");
        $user=I("user");
        $user =  stripslashes(I("user"));  
		$user = rtrim($user ,'"'); 
		$user = ltrim($user ,'"');
		$user = str_replace('&quot;', '"', $user);
		$res=json_decode($user);
     	foreach($res as $obj){ 
     		$bookuser_picture=M("flash_bookuserpic"); 
        	$username = $obj->name; 
        	$username=substr($username, 0, stripos($username, "."));
        	$bookuser_picture->where("username='%s'",$username)->delete();
        	$picpath = $obj->path;
        	$bookuser_picture -> gradeid=$gradeid;
        	$bookuser_picture -> versionid=$versionid;
        	$bookuser_picture -> termid=$termid;
        	$bookuser_picture -> username=$username;
        	$bookuser_picture -> path=$picpath;
        	$bookuser_picture -> addtime=Date("y-m-d H:i:s");
        	$result=$bookuser_picture -> add();
     	}
	}
    

    /** 
	* getuserpiclist
	* 编辑flash页面
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function getuserpiclist(){
        $gradeid=I("gradeid/d");
        $termid=I("termid/d");
        $versionid=I("versionid/d");
        $model=M();
        $sql="SELECT t.*,(SELECT eg.name FROM engs_grade eg WHERE eg.id=t.gradeid) grade,";
        $sql.="(SELECT et.name FROM engs_term et WHERE et.id=t.termid) AS term,";
        $sql.="(SELECT ev.name FROM engs_version ev WHERE ev.id=t.versionid) AS VERSION FROM engs_flash_bookuserpic t";
        $sql.=" where t.gradeid=%d and t.termid=%d and t.versionid=%d";
        $rs=$model->query($sql,$gradeid,$termid,$versionid);
        $this->ajaxReturn($rs);
	}


	/** 
	* deluserpic
	* 删除课文角色图片
	* 
	* @param void
    * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
	*/
	public function deluserpic(){
        $id=I("id/d");
        $flash_bookuserpic=M("flash_bookuserpic"); 
        $flash_bookuserpic->where("id=%d",$id)->delete();
	}






}