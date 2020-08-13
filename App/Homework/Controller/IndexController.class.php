<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 首页控制器类
*
* @author         gm
* @since          1.0
*/

class IndexController extends CheckController {
  //教师发布作业页面
  public function teacher_publish(){
    $sso=I("sso");
    $this->assign("sso",$sso);
    $ks_code=I("rescode");
    $this->assign("ks_code",$ks_code);
    $username=I("username");
    $this->assign("username",$username);
    $yjArr=get_ip("",$sso);
    cookie("sso",$yjArr['sso']['c1']);
    $tqms=$yjArr['tqms']['title'];
    cookie("tqms",$tqms);
    $this->assign("tqms",$tqms);
    //获取本单元的单词
    //$sql="select * from (SELECT id,word,'0' AS hwtype,isstress,isword,'1' as quescount,t.chaptername FROM engs_word t  WHERE t.`ks_code`='".$ks_code."' and t.isdel=1 order by sortid) a UNION ALL select * from (SELECT t.id,chapter,'1' AS hwtype,'0' as isstress,'0' as isword,COUNT(*) AS quescount,'' as chaptername FROM engs_text_chapter t left join engs_text s on t.id=s.chapterid WHERE t.`ks_code`='".$ks_code."' and t.isdel=1 and s.isdel=1 group by t.ks_code,t.chapter,t.id order by t.sortid) b UNION ALL select * from (SELECT id,NAME,'2' AS hwtype,'0' as isstress,'0' as isword,quescount,'' as chaptername FROM engs_exams t WHERE t.`ks_code`='".$ks_code."' and t.isdel=1 and t.state=4 order by sortid) c";
    $sql = "select id,word,'0' as hwtype,isstress,isword,'1' as quescount,t.chaptername from engs_word t  where t.`ks_code`='".$ks_code."' and t.isdel=1 order by sortid";
    //echo $sql;exit;
    $hwrs=M()->query($sql);

    $sql = "select t.id,chapter as word,'1' as hwtype,'0' as isstress,'0' as isword,count(*) as quescount,'' as chaptername from engs_text_chapter t left join engs_text s on t.id=s.chapterid where t.`ks_code`='".$ks_code."' and t.isdel=1 and t.isevaluate = 1 and s.isdel=1 group by t.ks_code,t.chapter,t.id order by t.sortid";
    $textres = M()->query($sql);
    foreach ($textres as $key => $value) {
        array_push($hwrs, $value);
    }
    $sql = "select id,name as word,'2' as hwtype,'0' as isstress,'0' as isword,quescount,'' as chaptername from engs_exams t where t.`ks_code`='".$ks_code."' and t.isdel=1 and t.state=4 order by sortid";
    $exres =  M()->query($sql);
    foreach ($exres as $key => $value) {
        array_push($hwrs, $value);
    }
    //var_dump($hwrs);exit;
    $wordlistr=array();
    $wordlistt=array();
    $chapterlist=array();
    $examslist=array();
    $wordimport=0;
    $tempchapter="-1";
    $wordlencount=0;
    $wordlaloundcount=0;
    //var_dump($hwrs);exit;
    foreach($hwrs as $key=>$value){
      $temp=array();
      if($value["hwtype"]=="0"){
        $wordlencount=$wordlencount+1;
       
        $temp["id"]=$value["id"];
        $temp["word"]=$value["word"];
        $temp["imp"]=$this->special_character($temp["word"]);
        if($this->special_character($temp["word"])==1){
          $wordimport=$wordimport+1;
           if($value["isword"]=='0'){
            $wordlaloundcount=$wordlaloundcount+1;
            }
        }
        $temp["isstress"]=$value["isstress"];
        $temp["isword"]=$value["isword"];
        $temp["quescount"]=$value["quescount"];
        //展示
        if($tempchapter==$value["chaptername"]){
          array_push($wordlistr[$value["chaptername"]],$temp);
        }else{
          $tempchapter=$value["chaptername"];
          $wordlistr[$value["chaptername"]]=array();
          array_push($wordlistr[$value["chaptername"]],$temp);
        }			  
      }else if($value["hwtype"]=="1"){
        $temp["id"]=$value["id"];
        $temp["chapter"]=$value["word"];
        $temp["isstress"]=$value["isstress"];
        $temp["isword"]=$value["isword"];
        $temp["quescount"]=$value["quescount"];
        $temp["imp"]=1;
        array_push($chapterlist,$temp);
      }else if($value["hwtype"]=="2"){
        $temp["id"]=$value["id"];
        $temp["name"]=$value["word"];
        $temp["isstress"]=$value["isstress"];
        $temp["isword"]=$value["isword"];
        $temp["quescount"]=$value["quescount"];
        $temp["imp"]=1;
        array_push($examslist,$temp);
      }
    }
   //var_dump($wordlistr);exit;
    //判断版本下是否有资源
    if(count($wordlistr)==0&&count($examslist)==0){
      $result=0;
    }else{
      $result=1;
    }
    //判断是否有二级
    if($tempchapter==''||$tempchapter=='-1'){
      $this->assign("section",0);
    }else{
      $this->assign("section",1);
    }
    //年级判断
    $sql="select r_grade as grade from engs_rms_unit t where t.ks_code='".$ks_code."'";
    $unitrs=M()->query($sql);
    if($unitrs[0]["grade"]=='0001'||$unitrs[0]["grade"]=='0002'){
      $wordimport=0;
    }
    //章节总数
    $examscount=count($examslist);
    $this->assign("result",$result);
    $this->assign("wordlaloundcount",$wordlaloundcount);
    $this->assign("wordlencount",$wordlencount);
    $this->assign("wordimport",$wordimport);
    $this->assign("wordcount",count($wordlistr));
    $this->assign("chaptercount",count($chapterlist));
    $this->assign("examscount",count($examslist));
    $this->assign("wordlistr",$wordlistr);
    $this->assign("wordlistt",$wordlistt);
    $this->assign("chapterlist",$chapterlist);
    $this->assign("examslist",$examslist);
    $this->assign("homeworkid",$homeworkid);
    $this->display();
  }

  //返回1没有 0表示有
  private function special_character($str){
    return preg_match("/^[A-Za-z]+$/",$str);
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
  * teacher_english_publish
  * 教师布置作业函数
  *
  */
  public function teacher_english_publish(){
    $wa=I("word");
    $wa=urldecode($wa);
    $wa = rtrim($wa, '"');
    $wa = ltrim($wa, '"');
    $wa = str_replace('&quot;', '"', $wa);
    $wa = json_decode($wa);
    $ta=I("text");
    $ta=urldecode($ta);
    $ta = rtrim($ta, '"');
    $ta = ltrim($ta, '"');
    $ta = str_replace('&quot;', '"', $ta);
    $ta = json_decode($ta);
    $eq=I("exams");
    $eq=urldecode($eq);
    $eq = rtrim($eq, '"');
    $eq = ltrim($eq, '"');
    $eq = str_replace('&quot;', '"', $eq);
    $eq = json_decode($eq);
    // 教师提交作业分四部分
    //创建作业表
    $ks_code=I("ks_code");
    $username=I("username");
    $homework=M("homework");
    $homework->ks_code=$ks_code;
    $homework->teachid=$username;
    $homework->areaid=$areacode;
	  $homework->publish=Date("Y-m-d H:i:s");
    //查询作业名称
    $rs=M("rms_unit")->where("ks_id='%s'",$ks_code)->find();
    $ks_name=$rs["ks_name"];
    $homework->homework_name=$rs["ks_name"];
    $homeworkteacherid=$homeworkid=$homework->add();
	  $homeworkteacher=array();
	  $homeworkteacher["wacount"]=0;
	  $homeworkteacher["wtcount"]=0;
	  $homeworkteacher["tacount"]=0;
	  $homeworkteacher["eqcount"]=0;
    //单词朗读0表示单词跟读 1表示单词拼写 2表示听音选词 3表示英汉互译
    $homework_word_read=M("homework_word_read");
    //单词测试
    $homework_word_evaluat=M("homework_word_evaluat");
    foreach($wt as $key=>$value){
      if($value->typeid==0){
        $homework_word_read->wordid=$value->id;
        $homework_word_read->homeworkid=$homeworkid;
        $homework_word_read->add();
        $homeworkteacher["wacount"]=$homeworkteacher["wacount"]+1;
      }else if($value->typeid==1){
        $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS ukmp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON t.base_explainsid=s.id WHERE t.id=".$value->id;
        $wordrs=M()->query($sql);
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $word=str_replace($qian,$hou,$wordrs[0]["word"]);
        //过滤掉非字符
        $words=array();
        $optionarr=str_split($word);
        foreach($optionarr as $kw=>$vw){
            if(preg_match("/^[a-zA-Z\s]+$/",$vw)){
               array_push($words, $vw);
            }
        }
        $optionarr=$words;
        $len=count($optionarr);
        //$data["answer"]=implode(",",$arr);
        if($len<10){
            for ($i = 1; $i <= 10-$len; $i++) {
                $ran=rand(65, 122);
                $rword=chr($ran);
                while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                  $ran=rand(65, 122);
                }
                array_push($optionarr,$rword);
            }
        }else if($len>=10&&i<15){
            for ($i = 1; $i <= 15-$len; $i++) {
                $ran=rand(65, 122);
                $rword=chr($ran);
                while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                  $ran=rand(65, 122);
                }
                
                array_push($optionarr,$rword);
             }

        }else{
            for ($i = 1; $i <= 20-$len; $i++) {
                $ran=rand(65, 122);
                $rword=chr($ran);
                while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                  $ran=rand(65, 122);
                }
                array_push($optionarr,$rword);
            }

        }
        //顺序随机
        shuffle($optionarr);
        $data["homeworkid"]=$homeworkid;
        $data["wordid"]=$value->id;
        $options="";
        foreach($optionarr as $k=>$v){
          $options=$options.$v.',';
        }
        $data["option_a"]=$options;
        $data["option_b"]="";
        $data["option_c"]="";
        $answerwtt="";
        foreach($words as $wttk=>$wttv){
          $answerwtt=$answerwtt.$wttv;
        }
        $data["answer"]=$answerwtt;
        $data["typeid"]=2;
        $data["sortid"]=mt_rand(1000, 9999);
        $homework_word_evaluat->add($data);
        unset($choice);unset($data);
        $homeworkteacher["wtcount"]=$homeworkteacher["wtcount"]+1;
      }else if($value->typeid==2){
        $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS ukmp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON t.base_explainsid=s.id WHERE t.id=".$value->id;
        $wordrs=M()->query($sql);
        $choice=array();
        $answer=['A','B','C'];
        $rs=$this->get_choice_list($value->id);
        $choice[0]=$rs[0]["word"];
        $choice[1]=$rs[1]["word"];
        $choice[2]=$wordrs[0]["word"];
        shuffle($choice);
        $founded = array_search($wordrs[0]["word"], $choice);
        $data["homeworkid"]=$homeworkid;
        $data["wordid"]=$value->id;
        $data["option_a"]=$choice[0];
        $data["option_b"]=$choice[1];
        $data["option_c"]=$choice[2];
        $data["answer"]=$answer[$founded];
        $data["typeid"]=0;
        $data["sortid"]=mt_rand(1000, 9999);
        $homework_word_evaluat->add($data);
        unset($choice);unset($data);
        $homeworkteacher["wtcount"]=$homeworkteacher["wtcount"]+1;
      }else if($value->typeid==3){
        $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS ukmp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON t.base_explainsid=s.id WHERE t.id=".$value->id;
        $wordrs=M()->query($sql);
        $choice=array();
        $answer=['A','B','C'];
        $rs=$this->get_choice_list($value->id);
        $data["homeworkid"]=$homeworkid;
        $data["wordid"]=$value->id;
        $choice[0]=$rs[0]["explains"];
        $choice[1]=$rs[1]["explains"];
        $choice[2]=$wordrs[0]["explains"];
        shuffle($choice);
        $founded = array_search($wordrs[0]["explains"], $choice);
        $data["option_a"]=$choice[0];
        $data["option_b"]=$choice[1];
        $data["option_c"]=$choice[2];
        $data["answer"]=$answer[$founded];
        $data["typeid"]=1;
        $data["sortid"]=mt_rand(1000, 9999);
        $homework_word_evaluat->add($data);
        unset($choice);unset($data);
        $homeworkteacher["wtcount"]=$homeworkteacher["wtcount"]+1;
      }
    }

    //课文朗读
    foreach($ta as $key=>$value){
        $homework_text_read=M("homework_text_read");
        $homework_text_read->chapterid=$value->id;
        $homework_text_read->homeworkid=$homeworkid;
        $homework_text_read->add();
		    $quescount=M("text")->where("chapterid=%d and isdel=1",$value->id)->count();
		    $homeworkteacher["tacount"]=$homeworkteacher["tacount"]+$quescount;

    }

    //听力训练
    foreach($eq as $key=>$value){
        $homework_quiz=M("homework_quiz");
        $homework_quiz->examsid=$value->id;
        $homework_quiz->homeworkid=$homeworkid;
        $homework_quiz->add();
        $examscount=M("exams")->where("id=%d",$value->id)->find();
		    $homeworkteacher["eqcount"]=$homeworkteacher["eqcount"]+$value->quescount;
    }

    //更新作业中的试题的数量的数据
	  M("homework")->where("id=%d",$homeworkteacherid)->save($homeworkteacher);
    //给题库系统进行作业发布的展示
    $arr["homeworkid"]=$homeworkid;
    $arr["ks_code"]=$ks_code;
    $arr["username"]=$username;
    $arr["name"]=$ks_name." 听读作业 ".Date('nd-Hi');
    $this->ajaxReturn($arr);
  }

  /**
  * teacher_english_publish
  * 学生作业情况
  *
  */
  public function student_homework(){
    //为了区分学生答题的情况表示是否教师预览
    $callbackURL=I("callbackURL");
    $source=I("source","0");
    $ilearid=I("homeworkId/s");
    $studentid=I("studentId","0");
    $isOverdue=I("isOverdue");
    $homeworkid=I("paper_id");
    $deviceType=I("deviceType");
    $starttime=I("starttime",0);
    $batchid=I("batchid");
    $sso=I("sso");
    $yjArr=get_ip("",$sso);
    //$classid= getClassId(I("sso"),$studentid);
    $classid= I("classId/s","0");
    //信息写入cookie
    cookie("tms",$sso);
    cookie("username",$studentid);
    cookie("ilearid",$ilearid);
    cookie("classid",$classid);
    cookie("isOverdue",$isOverdue);
    cookie("batchid",$batchid);
    cookie("sso",$yjArr['sso']['c1']);
    cookie("tqms",$yjArr['tqms']['c1']);
    //查询engs_homework_student_list是否写入了
    
    $sql="select * from engs_homework_student_list t where t.homeworkid='".$ilearid."' and t.studentid='".$studentid."'  and t.dotime is not null";
    $listrs=M()->query($sql);
    if(empty($listrs)){
      if($studentid!=''&&$studentid!='null'){
        M("homework_student_list")->homeworkid=$ilearid;
        M("homework_student_list")->studentid=$studentid;
        M("homework_student_list")->classid=$classid;
        M("homework_student_list")->dotime=Date("Y-m-d H:i:s");
        M("homework_student_list")->paper_id=$homeworkid;
        M("homework_student_list")->add();
      }
    }
    $this->assign("username",cookie("username"));
    $this->assign("ilearid",cookie("ilearid"));
    $this->assign("batchid",cookie("batchid"));
    $this->assign("isOverdue",cookie("isOverdue"));
    $this->assign("homeworkid",$homeworkid);
    $this->assign("classid",$classid);
    $this->assign("sso",$sso);
    $this->assign("callbackURL",$callbackURL);
    $this->assign("source",$source);
    $this->assign("username",$studentid);
    $this->assign("isOverdue",$isOverdue);
    $this->assign("starttime",$starttime);
    //$this->display("../Mobhw/examsquiz");
    if($deviceType=='pad'){
        $url="../Padhw/examsquiz?homeworkid=".$homeworkid."&classId=".$classid."&studentId=".$studentid."&tms=".$sso."&sso=".$sso."&hwid=".$ilearid.'&starttime='.$starttime."&batchid=".$batchid."&callbackURL=".$callbackURL."&type=".$source."&isOverdue=".$isOverdue;
    }else{
        $url="../Mobhw/examsquiz?homeworkid=".$homeworkid."&classId=".$classid."&studentId=".$studentid."&tms=".$sso."&sso=".$sso."&hwid=".$ilearid.'&starttime='.$starttime."&batchid=".$batchid."&callbackURL=".$callbackURL."&type=".$source."&isOverdue=".$isOverdue;
    } 
    echo "<script>location.href='".$url."';</script>";   // 跳转到 t.ph
  }

  /**
  * teacher_english_publish
  * 班级反馈
  *
  */
  public function class_homework_feedback(){
      $homeworkid=I("paper_id");
      $studentid=I("studentId");
      $batchid=I("batch_id");
      $classid=I("classId");
      $sso=I("sso");
      $yjArr=get_ip("",$sso);
      cookie("sso",$yjArr['sso']['c1']);
      $tqms=$yjArr['tqms']['title'];
      //$tqms="192.168.107.60";
      session("classid",$classid);
      session("username",$studentid);
	    $url="../Mobhw/feedback?homeworkid=".$homeworkid."&classid=".$classid."&studentid=".$studentid."&type=1&tqms=".$tqms."&batchid=".$batchid."&sso=".$sso;
      echo "<script>location.href='".$url."';</script>";   // 跳转到 t.php
  }


  /**
   * 教师布置作业进行异步的将数据写入到redis中
   */
  public function publishData(){
    $homeworkid = I("homeworkid/d");
    $workService = new \Homework\Service\WorkService();
		$workService -> getQuestionInfo(array(),$homeworkid);
  }

  /**
  * student_homework_feedback
  * 学生作业反馈
  *
  */
  public function student_homework_feedback(){
      //为了区分过来的是从哪里过来的 学生的来源的全部不需要进行跳转
      $hwid=I("homeworkId");
      $homeworkid=I("paper_id");
      $deviceType=I("deviceType");
      $studentid=I("studentId");
      $batchid=I("batchid");
      $sso=I("sso");
      $classid=I("classId");
      if(empty($classid)||$classid=='null'||$classid==null){
        $classid= getClassId(I("sso"),$studentid);        
      }
      session("username",$studentid);
      session("classid",$classid);
      $this->assign("homeworkid",$homeworkid);
      $this->assign("studentid",$studentid);
      $this->assign("classid",$classid);
      if($deviceType=='pad'){
        $url="../Padhw/finish?hwid=".$hwid."&homeworkid=".$homeworkid."&classId=".$classid."&studentId=".$studentid."&tms=".$sso."&batchid=".$batchid;
    }else{
        $url="../Mobhw/finish?hwid=".$hwid."&homeworkid=".$homeworkid."&classid=".$classid."&studentid=".$studentid."&type=0&batchid=".$batchid;
    }
      
      echo "<script>location.href='".$url."';</script>";   // 跳转到 t.php

  }

  /**
  * getHomeworkPreviewQuestion
  * 预览获取作业
  *
  */
	public function getHomeworkPreviewQuestion(){
		  session_write_close();
  		//接收类型
	  	$type=I("type/d");//0表示单词测试 1表示听力训练
	  	//接收的是第几个数据
	  	$index=I("index/d",1);
      $typeid=I("typeid/d",0);
		  //是否是错题统计
	    $id=I("id");
  		//接收单词测试的个数
  		$wscount=I("wscount/d",0);
      $wccount=I("wccount/d",0);
      $wrcount=I("wrcount/d",0);
  		$wacount=I("wacount/d",0);
  		$tacount=I("tacount/d",0);
	  	//为了保持数据的一致性 减少代码的冗余 在此将单词测试的数据进行拼接
	  	if($type==0){
	  	//表示接受的是单词测试的数据
          $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS mp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON  t.base_explainsid=s.id WHERE t.id=".$id;
          $wtrs=M()->query($sql);
          if($typeid==1){
            //单词拼写
            $choice=array();
            $qian=array(" ","　","\t","\n","\r");
            $hou=array("","","","","");
            $word=str_replace($qian,$hou,$wtrs[0]["word"]);
            //过滤掉非字符
            $wordnum=array();
            for($num=65;$num<=122;$num++){
                if($num==91||$num==92||$num==93||$num==94||$num==95||$num==96){
                    continue;
                }else{
                    array_push($wordnum,$num);
                }
            }
            //过滤掉非字符
            $words=array();
            $optionarr=str_split($word);
            foreach($optionarr as $kw=>$vw){
                if(preg_match("/^[a-zA-Z\s]+$/",$vw)){
                   array_push($words, $vw);
                   $number=ord($vw);
                   $index=array_search($number,$wordnum);
                   unset($wordnum[$index]);
                }
            }
            $optionarr=$words;
            $len=count($optionarr);
            
            $data["answer"]=implode(",",$arr);
            if($len<10){
                for ($i = 1; $i < 10-$len; $i++) {
                    //$ran=rand(65, 122);
                    $rand = array_rand($wordnum);
                    $ran = $wordnum[$rand];
                    $rword=chr($ran);
                    unset($wordnum[$rand]);
                    array_push($optionarr,$rword);
                }
            }else if($len>=10&&i<15){
                for ($i = 1; $i < 15-$len; $i++) {
                    $rand = array_rand($wordnum);
                    $ran = $wordnum[$rand];
                    $rword=chr($ran);
                    unset($wordnum[$rand]);
                    array_push($optionarr,$rword);
                 }

            }else if($len>=15&&i<20){
                for ($i = 1; $i < 20-$len; $i++) {
                    $rand = array_rand($wordnum);
                    $ran = $wordnum[$rand];
                    $rword=chr($ran);
                    unset($wordnum[$rand]);
                    array_push($optionarr,$rword);
                }
            }else{
                for ($i = 1; $i < 25-$len; $i++) {
                    $rand = array_rand($wordnum);
                    $ran = $wordnum[$rand];
                    $rword=chr($ran);
                    unset($wordnum[$rand]);
                    array_push($optionarr,$rword);
                }
            }
            shuffle($optionarr);
            array_push($optionarr,"");
            //进行数据的展示
            $wtrs[0]["tcontent"]=$wtrs[0]["explains"];
            $wordrs=str_split($wtrs[0]['word']);
            $wtrs[0]["words"]=$wordrs;
            $wtrs[0]["items"]=$optionarr;
            $wtrs[0]["typeid"]=2;
            $wtrs[0]["option_a"]=implode(",",$optionarr);
            $rss["question"]=$wtrs[0];
            $rss["items"]=$optionarr;
            $wtanswer["answer"]=$wtrs[0]['word'];
            $wtanswer["quesid"]=$wtrs[0]["id"];
            $words=str_split($wtrs[0]["word"]);
            $wtanswer["words"]=$words;
            $wtanswer["userwords"]=$wtrs[0]["words"];
            $rss["answer"]=$wtanswer;
          }else if($typeid==2){
            $ran=0;
            $wtrs[0]["typeid"]=$ran;
            $choice=$this->get_choice_list(id);
            $answerran=mt_rand(0, 100)%2;
            $wtitems=array();
            $answertemp=array();
            if($answerran==0){
              $wttemp["flag"]="A";
              $wtrs[0]["quesans"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
            }else if($answerran==1){
              $wtrs[0]["quesans"]="B";
              $wttemp["flag"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
            }else{
              $wtrs[0]["quesans"]="C";
              $wttemp["flag"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
            }
            unset($wttemp);
            $rss["question"]=$wtrs[0];
            $wtrs[0]["typeid"]=3;
            $rss["items"]=$wtitems;
            $wtanswer["quesanswer"]=$wtrs[0]["quesans"];
            $wtanswer["answer"]=$wtrs[0]["quesans"];
            $wtanswer["quesid"]=$wtrs[0]["id"];
            $words=str_split($wtrs[0]["word"]);
            $answer=array();
            array_push($answer,$wtanswer);
            $rss["answer"]=$answer;
          }else if($typeid==3){
            $ran=mt_rand(0, 100)%2;
            $wtrs[0]["typeid"]=$ran;
            $choice=$this->get_choice_list(id);
            $answerran=mt_rand(0, 100)%2;
            $wtitems=array();
            $answertemp=array();
            if($answerran==0){
              $wttemp["flag"]="A";
              $wtrs[0]["quesans"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
            }else if($answerran==1){
              $wtrs[0]["quesans"]="B";
              $wttemp["flag"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
            }else{
              $wtrs[0]["quesans"]="C";
              $wttemp["flag"]="A";
              if($ran==0){
                $wtrs[0]["tcontent"]=$wtrs[0]['explains'];
                $wttemp["content"]=$choice[0]["word"];
              }else{
                $wtrs[0]["tcontent"]=$wtrs[0]['word'];
                $wttemp["content"]=$choice[0]["explains"];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
              $wttemp["flag"]="B";
              if($ran==0){
                $wttemp["content"]=$choice[1]["word"];
              }else{
                $wttemp["content"]=$choice[1]["explains"];
              }
              array_push($wtitems,$wttemp);
              $wttemp["flag"]="C";
              if($ran==0){
                $wttemp["content"]=$wtrs[0]['word'];
              }else{
                $wttemp["content"]=$wtrs[0]['explains'];
              }
              array_push($wtitems,$wttemp);
              unset($wttemp);
            }
            unset($wttemp);
            $wtrs[0]["typeid"]=1;
            $rss["question"]=$wtrs[0];
            // $wtrs[0]["typeid"]=$ran;
            $rss["items"]=$wtitems;
            $wtanswer["quesanswer"]=$wtrs[0]["quesans"];
            $wtanswer["answer"]=$wtrs[0]["quesans"];
            $wtanswer["quesid"]=$wtrs[0]["id"];
            $words=str_split($wtrs[0]["word"]);
            $answer=array();
            array_push($answer,$wtanswer);
            $rss["answer"]=$answer;
          }
    			$rs["type"]=0;
    			$rs["num"]=16;
    			$rs["parent"]=$rss;
    			$rs["children"]=array();
		}else if($type==1){
			//查询数据库中的作业中试卷的ID
			$index=$index-$wscount-$wacount-$tacount-$wrcount-$wccount;
			$examsid=$id;
			$quizid="";
			//查询试题
			$quessql="select '".$examsid."' as examsid,'".$quizid."' as quizid,'".$homeworkid."' as homeworkid,t.*,s.parentid";
			$quessql=$quessql." from engs_exams_questions t left join engs_exams_stem s on t.stemid=s.id where (t.isdel=1";
			$quessql=$quessql." and s.isdel=1)  and t.examsid=".$examsid;
			$quessql=$quessql." order by t.paper_sortid limit ".$index.",1";
			$questionrs=M()->query($quessql);
			$questions_items=(array)json_decode(urldecode($questionrs[0]["questions_items"]));
			//如果是图片的话 直接进行base64编码减少数据的请求
			if($questionrs[0]["itemtype"]=="1"){
				foreach($questions_items as $key=>$value){
					$filepath=C("CONST_UPLOADS").$value->content;
					$image_info = getimagesize($filepath);
					$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
					//$value->content=$base64_image_content;
					$questions_items[$key]->imgcontent=$base64_image_content;
				}
			}
			$questions_answer=(array)json_decode(urldecode($questionrs[0]["questions_answer"]));
			$questions_tts=json_decode(urldecode($questionrs[0]["questions_tts"]));
			$questionid=$questionrs[0]["id"];
			$questions_answer[0]["quesansid"]=$questions_answer["id"];
			$questions_answer[0]["answer"]=$questions_answer["answer"];
			$stemsql="select * from engs_exams_stem s where s.id='".$questionrs[0]["stemid"]."'";
			$stemrs=M()->query($stemsql);
			$questionrs[0]["stemcontent"]=$stemrs[0]["content"];
      if(empty($questionrs[0]["stemid"]))
			$childquestionrs=$this->getParentStem($questionrs[0]["stemid"]);
			$ques["question"]=$questionrs[0];
			$ques["items"]=$questions_items;
			$ques["answer"]=$questions_answer;
			$ques["tts"]=$questions_tts;
			$ques["rate"]=0;
			$rs["type"]=1;
			$rs["num"]=16;
			$rs["parent"]=$ques;
			$rs["children"]=$childquestionrs[0];
		}else if($type==2){
		  	//单词朗读的刷新数据
			$wasql="SELECT ew.word as tncontent,ewe.`morphology` ,ewe.`explains` as cncontent";
			$wasql=$wasql.",ebw.ukmp3 as usermp3,ewe.pic,ebw.ukmark FROM engs_word AS ew,engs_base_word ebw,engs_base_word_explains AS ewe ";
			$wasql=$wasql."  WHERE ebw.id=ew.base_wordid and ew.`base_explainsid`=ewe.`id` and ew.id='".$id."'order by ew.sortid,ew.id ";
			//查询结果
			$rs=M()->query($wasql);
			$rs[0]["classresult"]=array();
			$rs["type"]=2;
		}else if($type==3){
		  	//课文朗读的刷新数据
			//$index=$index-$wtcount-$wacount;
			$tasql="SELECT t.`id`,t.`chapterid`,t.`encontent` as tncontent,t.`cncontent`,t.mp3 as usermp3 FROM engs_text t  WHERE ";
			$tasql=$tasql." t.`chapterid`='".$id."' ORDER BY t.sortid,t.id limit ".$index.",1";
			$rs=M()->query($tasql);
			$rs[0]["classresult"]=array();
			$rs["type"]=3;
		}
		$this->ajaxReturn($rs);

	}


	private function getParentStem($parentid){
	  	//组合试题的问题
		$sql="select * from engs_exams_stem t where  t.id in (select parentid from engs_exams_stem s where s.id=".$parentid." and s.isdel=1)";
		$childrs=M()->query($sql);
		return $childrs;
	}
	
}
