<?php
namespace Mobile\Controller;
use Think\Controller;
/**
* 列举出现的公共的变量的意思
* 首页控制器类中的共有的变量$index表示是第几道试题$iserror表示是够显示错题$time表示时间$issubmit表示是否提交用来查看答案
*
* @author         qgy
* @since          2.0
*/

class ExamlistenController extends CheckController {
  //首页
    public function examsquiz(){
		$studentid=cookie("engm_username");
		$classid=cookie("engm_classid");
		$unitid=I("unitid");
		$this->assign("unitid",$unitid);
		//作业的ID
    	$homeworkid=md5($studentid.time().rand(10000,99999));
		$examid=I("examsid",0);
		//表示是第几道试题
		$index=I("index/d",0);
		//是否只显示错误
		$iserror=I("iserror","0");
		//显示时间
		$time=I("time/d",0);
		//用户类型
		$type=I("type/d","0");
		//是否提交
		$issubmit=I("issubmit/d",0);
		if($issubmit==1){
			$homeworkid=I("homeworkid");
		}else{
			$nowtime=Date("Y-m-d H:i:s");
			$sql="select * from engs_quiz_student_list t where t.studentid='".$studentid."' and t.dotime is not null and t.submittime is null and paper_id='".$examid."' and t.endtime>='".$nowtime."' order by t.endtime desc";
		    $listrs=M()->query($sql);
		    if(empty($listrs)){
		      if($studentid!=''&&$studentid!='null'){
		        M("quiz_student_list")->homeworkid=$homeworkid;
		        M("quiz_student_list")->studentid=$studentid;
		        M("quiz_student_list")->classid=$classid;
		        M("quiz_student_list")->dotime=Date("Y-m-d H:i:s");
		        $minute=C("userexamlisten");
		        M("quiz_student_list")->endtime=date('Y-m-d H:i:s',strtotime("+".$minute." minute"));;
		        M("quiz_student_list")->paper_id=$examid;
		        M("quiz_student_list")->add();
		      }
		    }else{
		    	$homeworkid=$listrs[0]["homeworkid"];
		    }
		}
		//进行班级的数据的获取
		
		//这里需要进行学生班级的查询
		cookie("studentId",$studentid);
		cookie("classId",$classid);
		$this->assign("classid",$classid);
		$this->assign("studentid",$studentid);
		//参数的初始化
		$quescount=0;
		$hwtype=0;
		$questions=array();
	    //查询作业的情况
	    if($iserror==1){
	    	$sql="SELECT t.questionsid AS id,(CASE WHEN s.answerid IS NOT NULL THEN s.answernum  ELSE 0 END) AS isdo,(s.iscorrect) AS iscorrect,'1' AS hwtype, st.question_score AS quesscore FROM engs_exams_questions_answer t  LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id`  LEFT JOIN (SELECT z.answerid,COUNT(*) AS answernum,SUM(iscorrect) AS iscorrect FROM engs_student_quiz_history z WHERE homeworkid='".$homeworkid."' and studentid = '".$studentid."'  AND issubmit = 1 GROUP BY answerid) s  ON t.id = s.answerid,engs_exams_stem st WHERE st.id = e.stemid AND t.isdel = 1 AND e.`isdel` = 1 AND e.`examsid` = ".$examid." AND (s.iscorrect = 0 OR s.iscorrect IS NULL) ORDER BY e.paper_sortid ";
	    }else{
	    	$sql="SELECT t.questionsid AS id,(CASE WHEN s.answerid IS NOT NULL THEN s.answernum  ELSE 0 END) AS isdo,(s.iscorrect) AS iscorrect,'1' AS hwtype, st.question_score AS quesscore FROM engs_exams_questions_answer t  LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id`  LEFT JOIN (SELECT z.answerid,COUNT(*) AS answernum,SUM(iscorrect) AS iscorrect FROM engs_student_quiz_history z WHERE homeworkid='".$homeworkid."' and studentid = '".$studentid."'  AND issubmit = 1 GROUP BY answerid) s  ON t.id = s.answerid,engs_exams_stem st WHERE st.id = e.stemid AND t.isdel = 1 AND e.`isdel` = 1 AND e.`examsid` = ".$examid." ORDER BY e.paper_sortid ";
	    }
		
		$result=M()->query($sql);
		foreach($result as $key=>$value){
			$temp=array();
			$temp["parentid"]=$value["id"];
			array_push($questions,$temp);
			$eqcount=$eqcount+1;
			$quescount=$quescount+1;
		}
		//作业总体数量
		$this->assign("homeworkid",$homeworkid);
		$this->assign("examid",$examid);
		$this->assign("issubmit",$issubmit);
		$this->assign("iserror",$iserror);
		$this->assign("isOverdue","false");
		$this->assign("wacount","0");
		$this->assign("wtcount","0");
		$this->assign("tacount","0");
		$this->assign("eqcount",$eqcount);
		$this->assign("quescount",$quescount);
		$this->assign("index",$index);
		$this->assign("time",$time);
		$this->assign("type",$type);
		$this->assign("questions",$questions);
		$this->display("/Listen/examsquiz");
  }

    public function setUseranswer(){
		$homeworkid=I("homeworkid","0");
		$examsid=I("examsid");
		$quizid=I("quizid");
		$quesid=I("questionid");
		$answerid=I("answerid");
		$useranswer=I("useranswer");
		$typeid=I("typeid/d",0);
		$studentid=cookie("studentId");
		$classid=cookie("classId");
		//将用户的答案进行提交只记录最后一次的答案
		$student_quiz=M("student_quiz_history");
		//阐述之前的答案
		$rsss=$student_quiz->where("homeworkid='%s' and examid=%d  and questionsid=%d and answerid=%d and studentid='%s'",$homeworkid,$examsid,$quesid,$answerid,$studentid)->find();
		if(empty($rsss)){
			$student_quiz->studentid=$studentid;
			$student_quiz->classid=$classid;
			$student_quiz->homeworkid=$homeworkid;
			$student_quiz->examid=$examsid;
			$student_quiz->quizid=$quizid;
			$student_quiz->answerid=$answerid;
			$student_quiz->answer=$useranswer;
			$student_quiz->questionsid=$quesid;
			//查询答案进行正确错误判断
			$quesanswer=M("exams_questions_answer")->where("id=%d and isdel=1",$answerid)->find();
			$stemrs=M()->query("select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.isdel=1 and s.id=".$quesid.")");
			//问题的选项

			//判断是否作答分三种情况
			if($typeid=='1'){
			$quesitemrs=M("exams_questions_items")->where("questionsid=%d and isdel=1 and flag='%s'",$quesid,$useranswer)->find();
			if(strtolower($quesitemrs["content"])==strtolower($quesanswer["answer"])){
				$student_quiz->iscorrect=1;
			  $student_quiz->score=$stemrs[0]["question_score"];
			}else{
				$student_quiz->iscorrect=0;
			  $student_quiz->score=0;
			}
			}else{
			if(strtolower($useranswer)==strtolower($quesanswer["answer"])){
				$student_quiz->iscorrect=1;
			  $student_quiz->score=$stemrs[0]["question_score"];
			}else{
				$student_quiz->iscorrect=0;
			  $student_quiz->score=0;
			}
			}
			$student_quiz->do_time=Date("Y-m-d H:i:s");
			if($studentid=='0'||$studentid==''){

			}else{
			  $student_quiz->add();
			}
		}else{
			$student_quiz->studentid=$studentid;
			$student_quiz->classid=$classid;
			$student_quiz->homeworkid=$homeworkid;
			$student_quiz->examid=$examsid;
			$student_quiz->quizid=$quizid;
			$student_quiz->answerid=$answerid;
			$student_quiz->answer=$useranswer;
			$student_quiz->questionsid=$quesid;
			//查询答案进行正确错误判断
			$quesanswer=M("exams_questions_answer")->where("id=%d and isdel=1",$answerid)->find();
			$stemrs=M()->query("select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.isdel=1 and s.id=".$quesid.")");
			//问题的选项

			//判断是否作答分三种情况
			if($typeid=='1'){
			$quesitemrs=M("exams_questions_items")->where("questionsid=%d and isdel=1 and flag='%s'",$quesid,$useranswer)->find();
			if(strtolower($quesitemrs["content"])==strtolower($quesanswer["answer"])){
				$student_quiz->iscorrect=1;
			  $student_quiz->score=$stemrs[0]["question_score"];
			}else{
				$student_quiz->iscorrect=0;
			  $student_quiz->score=0;
			}
			}else{
			if(strtolower($useranswer)==strtolower($quesanswer["answer"])){
				$student_quiz->iscorrect=1;
			  $student_quiz->score=$stemrs[0]["question_score"];
			}else{
				$student_quiz->iscorrect=0;
			  $student_quiz->score=0;
			}
			}
			$student_quiz->do_time=Date("Y-m-d H:i:s");
			if($studentid=='0'||$studentid==''){

			}else{
			  $student_quiz->where("id=%d",$rsss["id"])->save();
			}
		}	
    }



  public function finish(){
		//作业总分数以及学生的分数
  	    $homeworkid=I("homeworkid");
  	    $unitid=I("unitid");
		$examid=I("examsid");
		$studentid=I("studentid");
		$classid=I("classid");
		$type=I("type/d",0);
		$this->assign("type",$type);
		//已经提交的人数
		$homeworksubmitrs=M("quiz_student_list")->field("(UNIX_TIMESTAMP(submittime) - UNIX_TIMESTAMP(dotime)) AS usertime")->where("homeworkid='%s' and paper_id=%d and studentid='%s'",$homeworkid,$examid,$studentid)->find();
		$homeworksubmittime="";
		$submittimeseconds=$homeworksubmitrs["usertime"]%60;
		$submittimeminute=($homeworksubmitrs["usertime"]-$submittimeseconds)/60;
		if($submittimeseconds==0){
			$homeworksubmittime=$submittimeminute."分";
		}else{
			if($submittimeminute==0){
				$homeworksubmittime=$submittimeseconds."秒";
			}else if($submittimeminute!=0){
				$homeworksubmittime=$submittimeminute."分".$submittimeseconds."秒";
			}
		}
		if($homeworksubmittime==0){
			$homeworksubmittime=$homeworksubmitrs["usertime"]."秒";
		}
		if(empty($homeworksubmitrs)||$homeworksubmitrs["usertime"]=='null'||$homeworksubmitrs["usertime"]==''){
			$homeworksubmittime="1秒";
		}

		$homeworksubmitrs=M("quiz_student_list")->field("distinct studentid")->where("paper_id=%d",$examid)->select();
		$homeworksubmitnum=count($homeworksubmitrs);
		//获取最高分以及平均分
		$userscoresql="select homeworkid,studentid,sum(case when s.type='0' then score else 0 end) as wascore,sum(case when s.type='1' then score else 0 end) as eqscore from (select z.homeworkid,z.studentid,z.score,'1' as type,z.classid,z.examid from engs_student_quiz_history z where  z.examid=".$examid.") as s where  s.examid=".$examid." group by s.studentid,s.homeworkid";
		$userscorers=M()->query($userscoresql);
		$homeworkmaxscore=0.0;
		$classcsore=0.0;
		foreach($userscorers as $key=>$value){
			if($homeworkmaxscore<($value["wascore"]+$value["eqscore"])){
				$homeworkmaxscore=($value["wascore"]+$value["eqscore"]);
			}
			$classcsore=$classcsore+($value["wascore"]+$value["eqscore"]);
		}
		$homeworkaveragescore=round($classcsore/$homeworksubmitnum,1);
		$this->assign("examid",$examid);
		$this->assign("unitid",$unitid);
		$this->assign("homeworkid",$homeworkid);
		$this->assign("studentid",$studentid);
		$this->assign("classid",$classid);
		$this->assign("homeworksubmitnum",$homeworksubmitnum);
		$this->assign("homeworksubmittime",$homeworksubmittime);
		$this->assign("homeworkmaxscore",$homeworkmaxscore);
		$this->assign("homeworkaveragescore",$homeworkaveragescore);
		$this->display("Listen/finish");
    }

  	//学生作答情况统计
  	public function getStudentSituation(){
	  	$examid=I("examid");
	  	$homeworkid=I("homeworkid");
	  	$studentid=cookie("studentId");
		$classid=cookie("classId");
	  	$wars=array();
		$wtrs=array();
		$tars=array();
		$eqrs=array();
		$sql="SELECT t.questionsid AS id,(CASE WHEN s.studentid IS NOT NULL  THEN 1  ELSE 0  END) AS isdo,s.iscorrect,'1' AS hwtype,st.question_score as quesscore FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_student_quiz_history z  WHERE  homeworkid='".$homeworkid."' and studentid = '".$studentid."'  AND examid = ".$examid.") s  ON t.id = s.answerid ,engs_exams_stem st  WHERE st.id=e.stemid and st.examsid=".$examid." AND t.isdel = 1 AND e.`isdel`=1  ORDER BY e.paper_sortid";
		$result=M()->query($sql);
		foreach($result as $key=>$value){
			$temp=array();
			$temp["id"]=$value["id"];
			$temp["isdo"]=$value["isdo"];
			$temp["score"]=$value["iscorrect"];
			$temp["quesscore"]=$value["quesscore"];
			array_push($eqrs,$temp);
		}
		$arr["wa"]=array();
		$arr["wt"]=array();
		$arr["ta"]=array();
		$arr["eq"]=$eqrs;
		$this->ajaxReturn($arr);
    }


  	
    //作业的汇总
    public function getSummary(){
    	session_write_close();
  	    $examid=I("examid");
  	    $homeworkid=I("homeworkid");
	  	$studentid=cookie("studentId");
		$classid=cookie("classId");
	  	$iserror=I("iserror/d",0);
	  	$wars=array();
		$wtrs=array();
		$tars=array();
		$eqrs=array();
		$sql="";
		if($iserror==0){
			$sql="(SELECT t.questionsid AS id,s.studentid AS isdo,s.iscorrect,'1' AS hwtype FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e ON t.`questionsid` = e.`id` LEFT JOIN (SELECT z.* FROM engs_student_quiz_history z WHERE homeworkid='".$homeworkid."' and examid = ".$examid." AND studentid ='".$studentid."') s ON t.id = s.answerid,engs_exams_stem st WHERE st.id = e.stemid AND  st.examsid =".$examid." AND t.isdel = 1 AND e.`isdel` = 1 ORDER BY e.paper_sortid) ";
		}else{
	        $sql="(select * from (SELECT t.questionsid AS id,s.studentid AS isdo,s.iscorrect,'1' AS hwtype FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_homework_student_quiz z  WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.answerid ,engs_homework_quiz eq,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." ORDER BY e.paper_sortid)  d) ";
		}
		$result=M()->query($sql);
		foreach($result as $key=>$value){
			$temp=array();
			$temp["id"]=$value["id"];
			$temp["isdo"]=$value["isdo"];
			$temp["score"]=$value["iscorrect"];
			array_push($eqrs,$temp);
		}
		$arr["wa"]=array();
		$arr["wt"]=array();
		$arr["ta"]=array();
		$arr["eq"]=$eqrs;
		$this->ajaxReturn($arr);
    }



    public function getHomeworkQuestion(){
    	session_write_close();
		//接收类型
		$type=I("type/d");//0表示单词测试 1表示听力训练
		//接收的是第几个数据
		$index=I("index/d",1);
		//是否是错题统计
		$iserror=I("iserror/d");
		//学生账号
		$studentid=cookie("studentId");
		$classid=cookie("classId");
		//是否提交
		$issubmit=I("issubmit/d",0);
		//作业
		$examid=I("examid/d");
		//接收单词测试的个数
		$wtcount=I("wtcount/d",0);
		//接收单词朗读的个数
		$wacount=I("wacount/d",0);
		//接收课文朗读的个数
		$tacount=I("tacount/d",0);

		$homeworkid=I("homeworkid");
		//单词测试的刷新数据
		
		//查询数据库中的作业中试卷的ID
		$index=$index-$wtcount-$wacount-$tacount;
		$questionrs=array();
		//查询试题
		$quessql="select s.examsid as examid,0 as quizid,'".$homeworkid."' as homeworkid,t.*,s.parentid,s.content as stemcontent";
		$quessql=$quessql." from engs_exams_questions t left join engs_exams_stem s on t.stemid=s.id where (t.isdel=1";
		$quessql=$quessql." and s.isdel=1)  and s.examsid=".$examid;
		if($iserror=='1'){
			$quessql=$quessql." and t.id not in (select distinct questionsid from engs_student_quiz_history z where z.homeworkid='".$homeworkid."' and z.examid=".$examid." and z.studentid='".$studentid."' and z.iscorrect=1) ";
		}
		$quessql=$quessql." order by t.paper_sortid limit ".$index.",1";
		$questionrs=M()->query($quessql);
		$examsid=$questionrs[0]["examid"];
		$quizid=$questionrs[0]["quizid"];
		$questionrs[0]["examsid"]=$examsid;
		$questionrs[0]["tcontent"]=$this->relace_tcontent($questionrs[0]["tcontent"]);
		$questionrs[0]["stemcontent"]=$this->relace_tcontent($questionrs[0]["stemcontent"]);
		$questionrs[0]["questions_answer"]=urldecode($questionrs[0]["questions_answer"]);
		$questionrs[0]["questions_tts"]=urldecode($questionrs[0]["questions_tts"]);
		$questions_items=(array)json_decode(urldecode($questionrs[0]["questions_items"]));
		//如果是图片的话 直接进行base64编码减少数据的请求
		if($questionrs[0]["itemtype"]=="1"){
			foreach($questions_items as $key=>$value){
				$filepath=C("CONST_UPLOADS").$value->content;
				$image_info = $this->myGetImageSize($filepath,"curl", true);
				$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
				$questions_items[$key]->imgcontent=$base64_image_content;
			}
		}
		$questionrs[0]["questions_items"]=$questions_items;
		$questionid=$questionrs[0]["id"];
	    $childquestionrs=$this->getParentStem($questionrs[0]["stemid"]);

		//查询问题的答案以及用户的答案
		$questionanswersql="select t.id as quesansid,t.questionsid as quesid,t.answer as quesanswer,t.answer_num,s.* from engs_exams_questions_answer t left join ";
		$questionanswersql=$questionanswersql."(select * from engs_student_quiz_history  where  homeworkid='".$homeworkid."' and studentid='".$studentid."' and ";
		$questionanswersql=$questionanswersql." examid=".$examsid." and questionsid=".$questionid.") as s";
		$questionanswersql=$questionanswersql." on t.id=s.answerid where t.isdel=1 AND t.questionsid=".$questionid;
		$questionanswerrs=M()->query($questionanswersql);
		$questionanswer=$questionanswerrs[0]["quesanswer"];

		//查询做这个作业的人数
		$answernumsql="select count(distinct t.studentid) as num from engs_quiz_student_list t where t.paper_id=".$examsid." and t.submittime is not null";
		$answernumrs=M()->query($answernumsql);
		$answernum=$answerrs[0]['num'];

		//这里查询需要优化
		$questionratesql="SELECT answerid,answer,SUM(CASE WHEN iscorrect = 1 THEN 1 ELSE 0  END) AS accnum,COUNT(DISTINCT homeworkid) AS answernum  FROM engs_student_quiz_history where examid=".$examsid;
		$questionratesql=$questionratesql." and questionsid=".$questionid." and issubmit=1 GROUP BY answerid,answer ORDER BY accnum asc";
		$questionraters=M()->query($questionratesql);
		$accnumtemp=0;
		$answernumtemp=0;
		$maxerrorrate=0;
		$maxerroranswer="";
		foreach($questionraters as $k=>$v){
			$accnumtemp=$accnumtemp+$v["accnum"];
			$answernumtemp=$answernumtemp+$v["answernum"];
			if(($v["answernum"]-$v["accnum"])>=$maxerrorrate&&$questionanswer!=$v["answer"]){
				$maxerrorrate=($v["answernum"]-$v["accnum"]);
				$maxerroranswer=$v["answer"];
			}
		}
		$data["answernum"]=$answernumtemp;
		$data["accrate"]=round($accnumtemp*100/$answernumtemp)."%";
		if(round($accnumtemp*100/$answernumtemp)==100){
			$data["erroranswer"]="";
		}else{
			$data["erroranswer"]=$maxerroranswer;
		}
		$questionanswerrs[0]["summary"]=$data;
		$ques["question"]=$questionrs[0];
		$ques["questts"]=json_decode($questionrs[0]["questions_tts"]);
		$ques["items"]=$questions_items;
		$ques["answer"]=$questionanswerrs;
		$ques["rate"]=$questionraters;
		$rs["type"]=1;
		$rs["num"]=16;
		$rs["parent"]=$ques;
		$rs["children"]=$childquestionrs[0];
		$this->ajaxReturn($rs);
    }


    //替换试卷中图片地址
	private function relace_tcontent($str){
	    $resource_path = PROTOCOL."://".C('resource_path');
	    $replacestr = str_replace('/uploads',$resource_path.'uploads',$str);
	    return $replacestr;
	}


	private function getParentStem($parentid){
	  	//组合试题的问题
	    $sql="select * from engs_exams_stem t where  t.id in (select parentid from engs_exams_stem s where s.id=".$parentid." and s.isdel=1)";
		$childrs=M()->query($sql);
		return $childrs;
	}

		//获取问题的TTS发声
	function getQuestiontts(){
		$quesid=I("quesid");
		$stemtype=I("type/d",0);
		//判断是不是组合试题
		if($stemtype==0){
			$sql="SELECT * FROM engs_exams_tts t WHERE tts_type='qe' AND parentid=".$quesid." AND isdel=1 order by sortid";
		}else{
			//音频是他的父亲的音频
			$sql="SELECT * FROM engs_exams_tts t WHERE tts_type='st' and st_flag=0 AND parentid in (select stemid from engs_exams_questions s where s.id=".$quesid.") AND isdel=1 order by sortid";
		}
		$rs=M()->query($sql);
		$this->ajaxReturn($rs);
	}




    //学生提交训练
    public function stupublish(){
		$homeworkid=I("homeworkid");
		$paper_id=I("paper_id");
		$starttime=I("starttime");
		$studentid=I("studentid");
		$schoolclass=I("classid");
		$unitid=I("unitid");
		$time=I("time/d",0);
		$tms=I("tms");
		//计算分数
		$score=0.0;
		$sql="select sum(score) as score from engs_student_quiz_history where examid='".$paper_id."' and studentid='".$studentid."'";
		$srs=M()->query($sql);
		foreach($srs as $key=>$value){
			$score=$value["score"]+$score;
		}
		$submittime=Date("Y-m-d H:i:s");
		//查询学生做作业的时间
		$result=M("homework_student_list")->where("homeworkid='".$homeworkid."' and studentid='".$studentid."'  and paper_id=".$paper_id)->select();
		$dotime=$result[0]["dotime"];
		$starttime=strtotime($dotime);
		$endtime=strtotime($submittime);
		M("quiz_student_list")->where("homeworkid='".$homeworkid."' and studentid='".$studentid."' and paper_id=".$paper_id)->setField("submittime",$submittime);
		
		
		//将用户的答案表进行更新
		M("student_quiz_history")->where("homeworkid='%s' and studentid='%s' and examid='%d'",$homeworkid,$studentid,$paper_id)->setField("issubmit","1");
		$sql="select sum(case when iscorrect=0 then 1 else 0 end) as errornum,sum(score) as score from engs_student_quiz_history where homeworkid='%s' and studentid='%s' and examid='%d' group by homeworkid,studentid,classid,examid";
		$rs=M()->query($sql,$homeworkid,$studentid,$schoolclass,$paper_id);
		$score=$rs[0]["score"];
		$errornum=$rs[0]["errornum"];
		$this->save_study_exams_info($unitid,$score,$paper_id,($time),$errornum);
		$ret["msg"]="正在评分请稍候。。。。。";
		$ret["state"]="1";
		$this->ajaxReturn($ret);
    }



    private function save_study_exams_info($unitid,$score,$examsid,$complate_time,$errornum) {
        $Model = M('study');
        //if (cookie('ut')) {
            // $username = cookie('engm_username');
            // $areacode = cookie('engm_areacode');
            // $curtime = date("Y-m-d");
            // $crutimearray = explode('-', $curtime);
            // $data['ks_code'] = $unitid;
            // $data['engs_studyid'] = $examsid;
            // $data['score'] = floatval($score);
            // $data['errornum'] = $errornum;
            // $data['username'] = $username;
            // $data['usertype'] = cookie('engm_usertype');
            // $data['areacode'] = $areacode;
            // $data['addtimey'] = $crutimearray[0];
            // $data['addtimem'] = $crutimearray[1];
            // $data['addtimed'] = $crutimearray[2];
            // $data['addtime'] = $curtime;
            // $data['studytype'] = 3;
            // $data['complate_time'] = $complate_time;
            // $exams = $Model->where('engs_studyid=' . $examsid . ' and username="' . $username . '" and areacode="'.$areacode.'" and studytype =3')->field('addtime')->limit(1)->select();
            // if (count($exams) > 0) {
            //     if ($exams[0]['addtime'] != $curtime) {
            //         $Model->data($data)->add();
            //     }
            // } else {
            //     $Model->add($data);
            // }
        //}
    }



    private function myGetImageSize($url, $type = 'curl', $isGetFilesize = false)   
	{  
	    // 若需要获取图片体积大小则默认使用 fread 方式  
	    $type = $isGetFilesize ? 'fread' : $type;  
	   
	    if ($type == 'fread') {  
	        // 或者使用 socket 二进制方式读取, 需要获取图片体积大小最好使用此方法  
	        $handle = fopen($url, 'rb');  
	   
	        if (! $handle) return false;  
	   
	        // 只取头部固定长度168字节数据  
	        $dataBlock = fread($handle, 168);  
	    }  
	    else {  
	        // 据说 CURL 能缓存DNS 效率比 socket 高  
	        $ch = curl_init($url);  
	        // 超时设置  
	        curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	        // 取前面 168 个字符 通过四张测试图读取宽高结果都没有问题,若获取不到数据可适当加大数值  
	        curl_setopt($ch, CURLOPT_RANGE, '0-167');  
	        // 跟踪301跳转  
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	        // 返回结果  
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	   
	        $dataBlock = curl_exec($ch);  
	   
	        curl_close($ch);  
	   
	        if (! $dataBlock) return false;  
	    }  
	   
	    // 将读取的图片信息转化为图片路径并获取图片信息,经测试,这里的转化设置 jpeg 对获取png,gif的信息没有影响,无须分别设置  
	    // 有些图片虽然可以在浏览器查看但实际已被损坏可能无法解析信息   
	    $size = getimagesize('data://image/jpeg;base64,'. base64_encode($dataBlock));  
	    if (empty($size)) {  
	        return false;  
	    }  
	   
	    $result['width'] = $size[0];  
	    $result['height'] = $size[1];  
	   
	    // 是否获取图片体积大小  
	    if ($isGetFilesize) {  
	        // 获取文件数据流信息  
	        $meta = stream_get_meta_data($handle);  
	        // nginx 的信息保存在 headers 里，apache 则直接在 wrapper_data   
	        $dataInfo = isset($meta['wrapper_data']['headers']) ? $meta['wrapper_data']['headers'] : $meta['wrapper_data'];  
	   
	        foreach ($dataInfo as $va) {  
	            if ( preg_match('/length/iU', $va)) {  
	                $ts = explode(':', $va);  
	                $result['size'] = trim(array_pop($ts));  
	                break;  
	            }  
	        }  
	    }  
	   
	    if ($type == 'fread') fclose($handle);  
	   
	    return $result;  
	} 



}
