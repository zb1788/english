<?php
namespace Mobile\Controller;
use Think\Controller;
/** 
* 学单词控制器类
*  
* @author         gm 
* @since          1.0 
*/
class WordstudyController extends CheckController {
	/** 
	* index  
	* 学单词首页展示 
	* 
	* @author         gm 
	* @since          1.0 
	*/  
    public function index(){
	  // //将所有的单元信息进行缓存
      $versionid = cookie('engm_versionid'); 
	  $gradeid = cookie('engm_gradeid'); 
	  $termid = cookie('engm_termid');
	  $arr["grade"]=$gradeid;
	  $arr["volume"]=$termid;
	  $arr["version"]=$versionid;
	  version_grade_term_getname();
	  $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
	  //比较时间查询数据库
	  $rms_unit=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("max(update_word_time) as word_time")->group("r_grade,r_volume,r_version")->find();
	  $fs=file_exists(C("CACHE_FILE_ROOT")."/Wordstudy/indexg".$gradeid."v".$volumeid."v".$versionid.".html");
	  if($rms_unit["word_time"]<filemtime(C("CACHE_FILE_ROOT")."/Wordstudy/index.html")&&$fs){
	  	$this->display("Cache/Mobile/Wordstudy/indexg".$gradeid."v".$termid."v".$versionid);
	  }else{
	  //传递年级教材学期名字
		$this->vgt_name = cookie('engm_gradename') .'.'. cookie('engm_versionname') .'.'. cookie('engm_termname');
		$unitlist=D("rms_unit")->getUnitListData($arr,0);
		$this->assign("unitlist",$unitlist["result"]);
		$this->assign("isunit",$unitlist["unit"]);
		$this->assign("unitlen",$unitlist["len"]);
		$this->buildHtml('indexg'.$gradeid.'v'.$termid.'v'.$versionid, HTML_PATH . '/Wordstudy/', 'index', 'utf8');
		$this->display();
	  }
	  
	}


	//获取单元列表
	public function getUnitList(){
		$versionid = cookie('engm_versionid'); 
	    $gradeid = cookie('engm_gradeid'); 
	    $termid = cookie('engm_termid'); 
	    $username = cookie('engm_username');

	    //取单词单元列表，过滤无单词的单元
	    $Model = new \Think\Model(); 
	    $sql="SELECT engs_rms_unit.ks_code,ks_name,ks_name_short,wu.wordcount,is_unit,ROUND((SELECT  COUNT(DISTINCT engs_wordid)  FROM engs_study  WHERE wordflag = 0  AND studytype=0 AND username = '%s'  AND ks_code = wu.ks_code) / wu.wordcount, 2 ) * 100 AS learncount,(SELECT  COUNT(DISTINCT engs_wordid)  FROM  engs_study  WHERE wordflag = 0  AND username = '%s'  AND studytype=0 AND ks_code = wu.ks_code) AS wordlearned FROM engs_rms_unit,(SELECT  COUNT(id) AS wordcount, ks_code  FROM engs_word  WHERE isdel = 1  GROUP BY ks_code) wu WHERE engs_rms_unit.ks_code = wu.ks_code   AND r_grade = '%s'  AND r_version = '%s' AND r_volume = '%s' AND engs_rms_unit.ks_code = wu.ks_code ORDER BY display_order, engs_rms_unit.ks_code";
	    $unitlist = $Model->query($sql,$username,$username,$gradeid,$versionid,$termid); 
	    $this->ajaxReturn($unitlist);
	}
    
    /** 
	* study  
	* 单词学习 
	* 
	*/ 
    public function study(){
     $model=I("model");
     $flag=I("flag");
	 $xflag=I("xflag");
	  $wordbcount = cookie('engm_wordbook_count');
	  $recordcount= cookie('engm_record_count');
	  $this -> assign("recordcount",$recordcount);
	  $this -> assign("wordbcount",$wordbcount);
	 
     $unit = explode(',',cookie('engm_unitid'));
     $this -> assign("unit",$unit);
	 $this -> assign("unitcount",count($unit));
	 //需要记住是从第几单元进入的详情学习压面，因此从首页经来的时候需要将次写到cookie中
	 if(empty($model))
     {
     	$model='pm';
		$curindex=cookie("engm_currunit");
		$indexn=I("indexn/d",$curindex+1);
        $indexp=I("indexp/d",$curindex-1);
	 }else{
	 	$indexn=I("indexn/d");
        $indexp=I("indexp/d");
	 }     
	  //滑动下一单元
    if($flag=='1')
    {
      if($xflag!='1')
	  {
      $indexn=$indexn-1;
      $indexp=$indexp-1;
	  }else{
	  	$indexn=$indexn-1;
        $indexp=$indexp;
	  }
    }
    elseif($flag=='0')
    {
    	if($xflag!='1')
		{
		      $indexn=$indexn+1;
		      $indexp=$indexp+1;
		}else{
			$indexn=$indexn;
            $indexp=$indexp+1;
		}
      
    }
    $this -> assign("indexn",$indexn);
    $this -> assign("indexp",$indexp);
	$this -> assign("model",$model);
     	  
	 
    	$unitid = I('unitid/s',0);
		$gradeid=cookie("engm_gradeid"); 
    	$Unit = M("rms_unit"); // 实例化User对象
      $Word=M("word");
      $wordcount=$Word -> where("isdel=1 and ks_code='%s'",$unitid) -> count();
      $this -> assign('wordcount',$wordcount);
	   // 获取ID为3的用户的昵称 
	   $unitname = $Unit->where("ks_code='%s'",$unitid)->getField('ks_name');
  		$this -> assign('unitid',$unitid);
  		$this -> assign('unitname',$unitname);
	    $wordlist = $this->get_unit_word($gradeid,$unitid);
	    $this->assign('wordlist',$wordlist);
	    $this->buildHtml('study'.$unitid, HTML_PATH . '/Mobile/Wordstudy/', 'study', 'utf8');
	    //$CachePath=HTML_PATH . '/Mobile/Wordstudy/'.$unitid;
    	$this->display();
    }
    

    /** 
	* get_unit_word
	* 学单词首页展示 
	* 
	* @author         gm 
	* @since          1.0 
	*/ 
 	protected function get_unit_word($gradeid,$unitid)
    { 
	    $username = cookie('engm_username');
		$Model = new \Think\Model(); 
	    $sql  = 'SELECT ew.id AS wordid,ew.word,ew.isstress,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bwe.morphology,bwe.explains,bwe.pic, ';
      $sql.=' (select count(distinct ewb.wordid) from engs_word_book ewb where ewb.wordid=ew.id and ewb.userid="%s" and isuse=1) as cllocted_flag ';
	    $sql .= ' FROM engs_word ew, engs_base_word bw,engs_base_word_explains bwe ';
	    $sql .= ' WHERE ew.base_wordid=bw.id AND ew.base_explainsid=bwe.id AND isdel=1 and ew.ks_code="%s" ';
	    $sql .= ' ORDER BY ew.sortid,ew.id';
      //echo $sql;	
	    $wordlist = $Model->query($sql,$username,$unitid);

	    foreach ($wordlist as $key => $value) {
	    	$wordlist[$key]["keyid"] = $key;
	    	$example=$this -> get_word_example($gradeid,$unitid,$value["word"]);
	    	$wordlist[$key]["example"]=$example;
	    } 
	    $data["arr"] = $wordlist;
	    $data["json"] = json_encode($wordlist); 
	    return $data;
    }
    
    
    /** 
	* get_word_example
	* 学单词例句 
	* 
	* @author         gm 
	* @since          1.0 
	*/
    protected function get_word_example($gradeid,$unitid,$word){
      	$Model = new \Think\Model(); 
  		  $sql="select a.encontent,a.cncontent,a.mp3 from engs_text a,engs_text_chapter b WHERE a.isexample = 0 And a.stateid=1 AND a.chapterid = b.id  AND b.ks_code = '%s' AND MATCH(a.encontent) AGAINST ('%s' IN BOOLEAN MODE) order by a.id limit 1";
        //$sql='select a.encontent,a.cncontent from engs_text a limit ';
  	    $example = $Model->query($sql,$unitid,$word);
  		  if(count($example)==0){
  			$sql = 'select a.encontent,a.cncontent,a.mp3 from engs_text a,engs_text_chapter b,engs_rms_unit c WHERE a.isexample = 0 And a.stateid=1 AND a.chapterid = b.id AND b.ks_code = c.ks_code AND c.r_grade <= "%s" AND MATCH(a.encontent) AGAINST ("%s " IN BOOLEAN MODE) order by a.id limit 1';
              //echo $sql;exit();
              $example = $Model->query($sql, $gradeid, $word);
  		   }
        //dump($example);
  	    return $example;
    }
    


    /** 
	* study_word_record
	* 单词学习记录
	* 
	* @author         gm 
	* @since          1.0 
	*/
    public function study_word_record(){
    	$engm_readword_count=cookie("engm_readword_count")+1;
    	cookie("engm_readword_count",$engm_readword_count);
    	$versionid = cookie('engm_versionid'); 
	    $gradeid = cookie('engm_gradeid'); 
	    $termid = cookie('engm_termid'); 
    	$username = cookie('engm_username');
    	$areacode = cookie('engm_areacode');
    	$usertype = cookie('engm_usertype');
    	$wordid=I("wordid/d");
    	$unitid=I("unitid/s");
        $arr_return["add"]=0;
    	$study_word=M("study");
    	$cur=date("Y-m-d");
		if($wordid!=0){
			$sql="SELECT * FROM `engs_study` WHERE ( username='%s' and studytype=1 and wordflag=0 and engs_studyid=%d and ks_code='%s' ) ORDER BY id desc";
			$rs=M() -> query($sql,$username,$wordid,$unitid);
			if(!empty($rs)){
			   if($rs[0]["addtime"]!=$cur){
				  $study_word -> addtime=$cur;
				  $study_word -> gradeid=$gradeid;
				  $study_word -> termid=$termid;
				  $study_word -> versionid=$versionid;
				  $study_word -> ks_code=$unitid;
				  $study_word -> engs_studyid=$wordid;
				  $study_word -> wordflag=0;
				  $study_word -> username=$username;
				  $study_word -> usertype=$usertype;
				  $study_word -> areacode=$areacode;
				  $study_word -> addtimey=date("Y");
				  $study_word -> addtimem=date("m");
				  $study_word -> addtimed=date("d");
				  $study_word -> studytype=1;
				 // $study_word -> add();
				  $arr_return["add"]=1;
			   }
			}else{
				  $study_word -> addtime=$cur;
				  $study_word -> gradeid=$gradeid;
				  $study_word -> termid=$termid;
				  $study_word -> versionid=$versionid;
				  $study_word -> ks_code=$unitid;
				  $study_word -> engs_studyid=$wordid;
				  $study_word -> wordflag=0;
				  $study_word -> username=$username;
				  $study_word -> usertype=$usertype;
				  $study_word -> areacode=$areacode;
				  $study_word -> addtimey=date("Y");
				  $study_word -> addtimem=date("m");
				  $study_word -> addtimed=date("d");
				  $study_word -> studytype=1;
				//  $study_word -> add();
				  $arr_return["add"]=1;
			}	
		}
        $this ->ajaxReturn($arr_return);
    }


    /** 
	* collect_word
	* 收藏单词到生词本
	* 
	* @author         gm 
	* @since          1.0 
	*/
    public function collect_word(){
    	$engm_wordbook_count=cookie("engm_wordbook_count")+1;
    	cookie("engm_wordbook_count",$engm_wordbook_count);
    	$wordid=I("wordid/d");
    	$unitid=I("unitid/s");
    	$username=cookie("engm_username");
    	$areacode=cookie("engm_areacode");
    	$word_book=M("word_book");
    	$sql="select * from engs_word_book t where t.isuse=1 and t.userid='".$username."' and wordid=".$wordid;
    	$rs=M()->query($sql);
    	if(empty($rs)){
    		$Model = new \Think\Model(); 
    		$sql="insert into engs_word_book set userid='".$username."',wordid=".$wordid.",ks_code='".$unitid."',studytime=now(),areacode='".$areacode."',isuse=1";
    		$Model -> execute($sql);
    		$arr_return["add"]=1;
    		$arr_return["msg"]="收藏成功";
    	}else{
    		$arr_return["add"]=0;
    		$arr_return["msg"]="生字本中已经存在！";
    	}
    	$this -> ajaxReturn($arr_return);
    }


}




