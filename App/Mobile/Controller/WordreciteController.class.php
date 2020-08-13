<?php
namespace Mobile\Controller;
use Think\Controller;
class WordreciteController extends CheckController {
	/** 
	* index  
	* 背单词首页展示 
	* 
	*/  
     public function index(){
     	//传递年级教材学期名字
      $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
      $versionid = cookie('engm_versionid'); 
      $gradeid = cookie('engm_gradeid'); 
      $termid = cookie('engm_termid');
      $rms_unit=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("max(update_text_time) as exams_time")->group("r_grade,r_volume,r_version")->find();
      $fs=file_exists($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Wordrecite/indexg".$gradeid."v".$termid."v".$versionid.".html");
      if($rms_unit["exams_time"]<filemtime($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Wordrecite/indexg".$gradeid."v".$termid."v".$versionid.".html")&&$fs==1){
        $filename="Cache/Mobile/Wordrecite/indexg".$gradeid."v".$termid."v".$versionid;
        $this->display($filename);
      }else{
        //传递年级教材学期名字
         $versionid = cookie('engm_versionid'); 
          $gradeid = cookie('engm_gradeid'); 
          $termid = cookie('engm_termid');
          $arr["grade"]=$gradeid;
          $arr["volume"]=$termid;
          $arr["version"]=$versionid;
        $this->vgt_name = cookie('engm_gradename') .'.'. cookie('engm_versionname') .'.'. cookie('engm_termname');
        $unitlist=D("rms_unit")->getUnitListData($arr,0);
        //var_dump($unitlist)
        $this->assign("unitlist",$unitlist["result"]);
        $this->assign("isunit",$unitlist["unit"]);
        $this->assign("unitlen",$unitlist["len"]);
        $this->buildHtml('indexg'.$gradeid.'v'.$termid.'v'.$versionid, HTML_PATH . '/Mobile/Wordrecite/', 'index', 'utf8');     
  	    $this->display();
      }

    }



    /** 
  * index  
  * 背单词首页展示 
  * 
  */  
     public function getUnitList(){ 
      //传递年级教材学期名字
      $versionid = cookie('engm_versionid'); 
      $gradeid = cookie('engm_gradeid'); 
      $termid = cookie('engm_termid');
      $username = cookie('engm_username');
      $wordbcount = cookie('engm_wordbook_count');
      $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);

      //取单词单元列表，过滤无单词的单元
      $Model = new \Think\Model();
      $sql = 'SELECT  id,unitname,unitalias,wu.wordcount,ROUND((SELECT COUNT( distinct wordid) FROM engs_study_word esw WHERE esw.wordflag = 1 AND username = "%s" AND unitid=wu.unitid)/wu.wordcount,2)*100 AS learncount,(SELECT COUNT( distinct wordid) FROM engs_study_word esw WHERE esw.wordflag = 1 AND username = "%s" AND unitid=wu.unitid) as wordrecite ';
      $sql .= ' FROM engs_unit,(SELECT COUNT(id) AS wordcount,unitid FROM db_english.engs_word  WHERE isdel = 1 GROUP BY unitid) wu';
      $sql .= ' WHERE engs_unit.isdel = 1 AND engs_unit.id = wu.unitid  AND gradeid = %d AND versionid = %d AND termid = %d  AND engs_unit.id = wu.unitid ';  
      $sql .= ' ORDER BY engs_unit.sortid,engs_unit.id';
      $unitlist = $Model->query($sql,$username,$username,$gradeid,$versionid,$termid);
       
      $this->assign('unitlist',$unitlist);
      $this->display();

    }

    /** 
	* recite  
	* 背单词页面
	* 
	*/
    public function recite(){
      $unitid=I("unitid/s");
      //比较时间查询数据库
      $rms_unit=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("max(update_word_time) as word_time")->group("r_grade,r_volume,r_version")->find();
      $fs=file_exists(C("CACHE_FILE_ROOT")."/Wordrecite/reciteu".$unitid.".html");
      if($rms_unit["word_time"]<filemtime(C("CACHE_FILE_ROOT")."/Wordrecite/reciteu".$unitid.".html")&&$fs){
        $this->display("Cache/Mobile/Wordrecite/reciteu".$unitid);
      }else{
        $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
        $this -> assign("recordcount",$recordcount);
        $this -> assign("wordbcount",$wordbcount);
        $nextunit = explode(',',cookie('engm_nextunitid'));
        $this->assign("nextlen",count($nextunit));
        $this -> assign("nextunit",$nextunit);
        $index=I("indexn/d",0);
        $this -> assign("index",$index);
        $indexn=$index+1;
        $this -> assign("indexn",$indexn);
        $source=I("source/d",0);
        $unitid = I('unitid/s',0); 
        $Unit = M("rms_unit"); // 实例化User对象
        // 获取ID为3的用户的昵称 
        $unitname = $Unit->where('ks_code="%s"',$unitid)->getField('ks_name');
        $this->assign('unitname',$unitname);
        $this->assign('unitid',$unitid);
        $wordlist = $this->get_unit_word($unitid,$source);
        //var_dump($wordlist);exit();
        $this->assign('wordlist',$wordlist);
        $this->assign('source',$source);
        $this->buildHtml('reciteu'.$unitid, HTML_PATH . '/Mobile/Wordrecite/', 'recite', 'utf8');
        $this->display();

      }
    	//$this->display("././Cache/Mobile/Wordrecite/wordrecite00010402040221");
    }
    
    /** 
	* get_unit_word  
	* 获取本单元的所有单词
	* 
	*/
    protected function get_unit_word($unitid,$source){
    	$Model = new \Think\Model();
		$wordlist="";
      if("0"==$source)
      {
  	    $sql  = 'SELECT ew.id AS wordid,ew.word,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bwe.morphology,bwe.explains,bwe.pic,round(rand()*100) as flag,1 as errorflag ';
  	    $sql .= ' FROM engs_word ew, engs_base_word bw,engs_base_word_explains bwe ';
  	    $sql .= ' WHERE ew.base_wordid=bw.id AND ew.base_explainsid=bwe.id AND isdel=1 and ew.ks_code="%s" ';
  	    $sql .= ' ORDER BY ew.sortid,ew.id';	
        //echo $sql;
		$wordlist = $Model->query($sql,$unitid);
      }else{
        $sql  = 'SELECT ew.id AS wordid,ew.word,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bwe.morphology,bwe.explains,bwe.pic,round(rand()*100) as flag,1 as errorflag ';
        $sql .= ' FROM engs_word_book ewb,engs_word ew, engs_base_word bw,engs_base_word_explains bwe ';
        $sql .= ' WHERE  ew.id=ewb.wordid AND ew.base_wordid=bw.id AND ew.base_explainsid=bwe.id AND isdel=1 and userid="%s"';
        $sql .= ' ORDER BY ew.sortid,ew.id';
		$wordlist = $Model->query($sql,cookie("engm_username"));
      }
	    foreach ($wordlist as $key => $value) {
	    	$wordlist[$key]["keyid"] = $key;
	    	$choices=$this -> get_choice_list($value["wordid"]);
			$choicelist=array();
			array_push($choicelist,$choices[0]);
			array_push($choicelist,$choices[1]);
			array_push($choicelist,$choices[2]);
			array_push($choicelist,$value);
            shuffle($choicelist);
			//dump($choicelist);
	    	$wordlist[$key]["choicelist"]=$choicelist;
	    } 
   	    $data["arr"] = $wordlist;
	    $data["json"] = json_encode($wordlist); 
	    return $data;

    }
    
    /** 
	* get_choice_list  
	* 获取选择题的选项
	* 
	*/
    protected function get_choice_list($wordid){
    	$word=M("word");
		$rs=$word -> where("id=%d",$wordid) -> field("word,base_wordid,(select max(tags) from engs_base_word bw where bw.id=base_wordid) as tag") -> find();
		$id=$rs['wordid'];
		$tag=$rs["tag"];
		$words=$rs["word"];
		$subword=substr($words,0,1);
		//dump($subword);
     	$Model = new \Think\Model(); 
		$sql='SELECT t1.id AS keyid,t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic,1 as flag,0 as errorflag';
     	$sql.=' FROM engs_base_word_explains bwe join (SELECT  ROUND(RAND() * ((SELECT  MAX(id)  FROM engs_base_word_explains) - (SELECT  MIN(id)  FROM engs_base_word_explains)) + (SELECT  MIN(id)  FROM engs_base_word_explains)) AS id) AS t3,engs_base_word AS t1 JOIN ';
        $sql.=' (SELECT ROUND(RAND()*((SELECT MAX(id) FROM engs_base_word )-(SELECT  MIN(id)  FROM engs_base_word )) +(SELECT MIN(id) FROM engs_base_word)) AS id) AS t2  ';
		$sql.='  WHERE  t1.id=bwe.base_wordid and bwe.`id`>=t3.id and t1.isword=1 AND t1.id >= t2.id  And t1.word!="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';
        $sql.='  GROUP BY  t1.id ,t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3  LIMIT 100';
        $choicelist = $Model->query($sql,$words,$id);
		//dump($choicelist);
		if(count($choicelist)<3){
			unset($sql);
			$sql='SELECT t1.id AS keyid,t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic,1 as flag,0 as errorflag';
	     	$sql.=' FROM engs_base_word_explains bwe,engs_base_word AS t1 ';
			$sql.='  WHERE  t1.id=bwe.base_wordid and t1.isword=1 AND t1.word like "%'.$subword.'" AND t1.word !="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';
	        $sql.='  GROUP BY  t1.id ,t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by keyid LIMIT 100';
			$choicelist1 = $Model->query($sql,$words,$id);
			$scount=3-count($choicelist);
			for($i=0;$i<$scount;$i++){
				array_push($choicelist,$choicelist1[$i]);
			}
		}
        return $choicelist;
     }

     /** 
	* get_choice_list  
	* 获取选择题的选项
	* 
	*/
    public function user_recite_word($wordid){
     	$Model = new \Think\Model(); 
     	$sql='SELECT t1.id AS keyid,t1.word,t1.flag,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bwe.morphology,bwe.explains,bwe.pic';
     	$sql.=' FROM ((SELECT  *,1 AS flag FROM engs_word t WHERE  t.id = %d) UNION ALL (SELECT *,0 AS flag FROM engs_word s WHERE s.id != %d ORDER BY RAND() LIMIT 3)) t1,engs_base_word bw,engs_base_word_explains bwe';
        $sql.=' WHERE t1.wordid=bw.id AND t1.explainsid=bwe.id AND t1.isdel=1 ';
        $sql.='ORDER BY RAND()';
        $choicelist = $Model->query($sql,$wordid,$wordid); 
        return $choicelist;
     }

     /** 
	* recite_word_record  
	* 背诵单词记录
	* 
	*/
    public function recite_word_record(){
    	// $engm_reciteword_count=cookie("engm_reciteword_count")+1;
    	// cookie("engm_reciteword_count",$engm_reciteword_count);
    	// $versionid = cookie('engm_versionid'); 
	    // $gradeid = cookie('engm_gradeid'); 
	    // $termid = cookie('engm_termid'); 
    	// $username = cookie('engm_username');
    	// $areacode = cookie('engm_areacode');
    	// $wordid=I("wordid/d");
    	// $unitid=I("unitid/s");
    	// $study_word=M("study");
    	// $cur=date("Y-m-d");
     //  $arr_return["add"]=0;
    	// $rs=$study_word -> where("username='%s' and wordflag=1 and studytype=1 and engs_studyid=%d",$username,$wordid) -> field("addtime") -> order("id desc") -> find();
    	// if(!empty($rs)){
     //       if($rs["addtime"]!=$cur){
     //       	  $study_word -> addtime=$cur;
     //       	  $study_word -> gradeid=$gradeid;
     //          $study_word -> termid=$termid;
     //       	  $study_word -> versionid=$versionid;
     //       	  $study_word -> ks_code=$unitid;
     //       	  $study_word -> engs_studyid=$wordid;
     //       	  $study_word -> wordflag=2;
     //       	  $study_word -> username=$username;
     //       	  $study_word -> areacode=$areacode;
     //       	  $study_word -> addtimey=date("Y");
     //       	  $study_word -> addtimem=date("m");
     //       	  $study_word -> addtimed=date("d");
     //          $study_word -> studytype=1;
     //       	 // $study_word -> add();
     //          $arr_return["add"]=1;
     //       }
     //    }else{
     //    	    $study_word -> addtime=$cur;
     //       	  $study_word -> gradeid=$gradeid;
     //          $study_word -> termid=$termid;
     //       	  $study_word -> versionid=$versionid;
     //       	  $study_word -> ks_code=$unitid;
     //       	  $study_word -> engs_studyid=$wordid;
     //       	  $study_word -> wordflag=2;
     //       	  $study_word -> username=$username;
     //       	  $study_word -> areacode=$areacode;
     //       	  $study_word -> addtimey=date("Y");
     //       	  $study_word -> addtimem=date("m");
     //       	  $study_word -> addtimed=date("d");
     //          $study_word -> studytype=1;
     //       	  //$study_word -> add();
     //          $arr_return["add"]=1;
     //    }
		   //  $curword=cookie("engm_readword_count");
     //    cookie("engm_readword_count",$curword+$arr_return["add"]);
     //    $this  ->ajaxReturn($arr_return);
    }



}