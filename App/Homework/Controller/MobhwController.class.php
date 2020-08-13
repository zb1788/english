<?php
namespace Homework\Controller;
use Homework\Service\WorkService;
use Think\Controller;

/**
* 列举出现的公共的变量的意思
* 首页控制器类中的共有的变量$index表示是第几道试题$iserror表示是够显示错题$time表示时间$issubmit表示是否提交用来查看答案
*
* @author         qgy
* @since          2.0
*/

class MobhwController extends CheckController {
  //首页
  	public function examsquiz(){
		$tms=I("tms",0);
		$sso=I("sso");
		$yjArr=get_ip("",I("sso"));
		$tqms=$yjArr['tqms']['c1'];
		$hwid=I("hwid",0);
		$starttime=I("starttime");
		$batchid=I("batchid");
		$callbackURL=I("callbackURL");
		//是否过期的标志
		$isOverdue=I("isOverdue");
		$this->assign("isOverdue",$isOverdue);
		$this->assign("tms",$tms);
		$this->assign("hwid",$hwid);
		$this->assign("starttime",$starttime);
		$this->assign("batchid",$batchid);
		$this->assign("callbackURL",$callbackURL);
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
		//echo $issubmit;
		//作业的ID
		$homeworkid=I("homeworkid","0");
		$studentId=I("studentId");
		//取出用户的时间
		$submittime=Date("Y-m-d H:i:s");
		//跟新用户的打开作业的时间
		//M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentId,$homeworkid)->setField("lastupdatetime",$submittime);
		//查询学生做题的时间数据
		//$studentlist=M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentId,$homeworkid)->find();
        $workService = new \Homework\Service\RedisService();
        $data = $workService->getHash($studentId,$homeworkid);
        $time = time();
        if($data){
            $data = unserialize($data);
        }else{
            $data["usertime"] = 0;
            $data["starttime"] = $time;
            $data["updatetime"] = $time;
        }
        $studentTime["updatetime"] = $time;
        $workService -> addHash($studentId,$homeworkid,serialize($studentTime));
        $spendtime=$data["usertime"];

		//进行班级的数据的获取
		$classId=I("classId","0");
		//这里需要进行学生班级的查询
		cookie("studentId",$studentId);
		cookie("classId",$classId);
		$this->assign("classid",$classId);
		$this->assign("studentid",$studentId);
		//参数的初始化
		$quescount=0;
		$hwtype=0;
		$questions=array();
	  	//查询作业的情况
		$wacount=0;
		$wscount=0;
		$wccount=0;
		$wrcount=0;
		$tacount=0;
		$eqcount=0;
		if($iserror==0){
			$sql="select * from engs_homework t where t.id=".$homeworkid;
			$result=M()->query($sql);
			$wacount=$result[0]["wacount"];
			$wscount=$result[0]["wscount"];
			$wccount=$result[0]["wccount"];
			$wrcount=$result[0]["wrcount"];
			$tacount=$result[0]["tacount"];
			$eqcount=$result[0]["eqcount"];
			$quescount=$wacount+$wscount+$wccount+$wrcount+$tacount+$eqcount;
		}else{
			$sql="(select * from (SELECT t.id,(CASE WHEN s.word_evaluatid IS NOT NULL THEN s.answernum ELSE 0  END) AS isdo ,s.iscorrect AS iscorrect,'0' AS hwtype,'1' as quesscore,t.typeid  FROM engs_homework_word_evaluat t LEFT JOIN (SELECT word_evaluatid,COUNT(*) AS answernum,sum(iscorrect) AS iscorrect FROM engs_homework_student_word_evaluat  WHERE classid = '".$classId."' and studentid='".$studentId."' AND homeworkid = ".$homeworkid." and issubmit=1 AND iscorrect=1 GROUP BY word_evaluatid) s  ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." and (iscorrect='0' or iscorrect is null) ORDER BY t.sortid DESC) a) UNION ALL ";
			$sql=$sql."(select * from (SELECT t.questionsid AS id,(CASE WHEN s.answerid IS NOT NULL  THEN s.answernum  ELSE 0  END) AS isdo,(s.iscorrect) AS iscorrect,'1' AS hwtype,st.question_score as quesscore,'0' as typeid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT z.answerid,COUNT(*) AS answernum,SUM(iscorrect) AS iscorrect   FROM engs_homework_student_quiz z  WHERE  classid = '".$classId."' and studentid='".$studentId."'  AND homeworkid = ".$homeworkid." AND issubmit=1  GROUP BY answerid) s  ON t.id = s.answerid ,engs_homework_quiz eq ,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." AND (s.iscorrect=0 OR s.iscorrect IS NULL) ORDER BY e.paper_sortid) b) ";

      		$result=M()->query($sql);
			foreach($result as $key=>$value){
				$temp=array();
				$temp["parentid"]=$value["id"];
				array_push($questions,$temp);
				if($value["hwtype"]=='0'){
					if($value["typeid"]=='0'){
						$wccount=$wccount+1;
					}else if($value["typeid"]=='1'){
						$wrcount=$wrcount+1;
					}else if($value["typeid"]=='2'){
						$wscount=$wscount+1;
					}else if($value["typeid"]=='3'){
						$wrcount=$wrcount+1;
					}
					$quescount=$quescount+1;
				}else if($value["hwtype"]=='1'){
					$eqcount=$eqcount+1;
					$quescount=$quescount+1;
				}else if($value["hwtype"]=='2'){
					$wacount=$wacount+1;
					$quescount=$quescount+1;
				}else if($value["hwtype"]=='3'){
					$tacount=$tacount+1;
					$quescount=$quescount+1;
				}
			}
		}
		//查询判断判断是都提交作业了
		$issql="select * from engs_homework_student_list t where studentid='".$studentId."'  and paper_id=".$homeworkid." and submittime is not null";
		$isrs=M()->query($issql);
		if(empty($isrs)){
			$issubmit=0;
		}else{
			$issubmit=1;
		}

		if(empty($studentId)){
			$issubmit=1;
		}

		if($type==1&&empty($studentId)){
			$issubmit=0;
		}
		if($type==2&&empty($studentId)){
			  $issubmit=1;
		  $type=2;
		}
		if($type==2){
			 $issubmit=1;
		}
		
		//作业总体数量
		$this->assign("issubmit",$issubmit);
		$this->assign("iserror",$iserror);
		$this->assign("wacount",$wacount);
		$this->assign("wscount",$wscount);
		$this->assign("wccount",$wccount);
		$this->assign("wrcount",$wrcount);
		$this->assign("tacount",$tacount);
		$this->assign("eqcount",$eqcount);
		$this->assign("quescount",$quescount);
		$this->assign("index",$index);
		$this->assign("time",date('s' ,$spendtime));
		$this->assign("hwtype",$hwtype);
		$this->assign("type",$type);
		$this->assign("homeworkid",$homeworkid);
		$this->assign("questions",$questions);
		//echo $type."==".$issubmit;exit;
		if($isOverdue == "true"){
			$this->display("examsquiz_overdue");
		}
		else{
			if($issubmit == 1){
				$this->display();
			}else{
				$this->display("examsquizs");
			}
		}
		
  	}

  



    public function finish(){
		//作业总分数以及学生的分数
		$batchid=I("batchid");
		$homeworkid=I("homeworkid");
		$studentid=I("studentid");
		$classid=I("classid");
		$type=I("type/d",0);
		//用户的分数以及平均分
		//$homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
		$homeworkrs=M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->find();
		//$homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
		$homeworkscore=0;
		//查询用户的时间
		$publishstudents=json_decode($homeworkrs["publishstudents"],TRUE);
		$students=$publishstudents[$classid]["students"];
		//var_dump($students[$studentid]);exit;
        $studentscore=$students[$studentid]["score"];
        $homeworksubmittime=$students[$studentid]["time"];
		$submittimeseconds=$homeworksubmittime%60;

		$submittimeminute=($homeworksubmittime-$submittimeseconds)/60;

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
			$homeworksubmittime=$homeworksubmittime."秒";
		}

		if($homeworksubmittime=='null'||$homeworksubmittime==''||$homeworksubmittime==0){
			$homeworksubmittime="1秒";
		}
		$homeworkmaxscore=0.0;
		$homeworkaveragescore=0.0;
		$homeworksubmitnum=0;
		foreach($students as $key=>$value){
			//获取最高分以及平均分
			if($value["issubmit"]==1){
				$homeworkaveragescore=$homeworkaveragescore+$value["score"];
				$homeworksubmitnum=$homeworksubmitnum+1;
				if($value["score"]>$homeworkmaxscore){
					$homeworkmaxscore=$value["score"];
				}
			}

		}
		$homeworkaveragescore=round($homeworkaveragescore/$homeworksubmitnum,1);
		
		if($studentscore == "0"){
			$studentscore = $homeworkaveragescore;
		}
       // echo $homeworkaveragescore;exit;
		$this->assign("homeworkid",$homeworkid);
		$this->assign("studentscore",round($studentscore,1));
		$this->assign("batchid",$batchid);
		$this->assign("studentid",$studentid);
		$this->assign("classid",$classid);
		$this->assign("type",$type);
		$this->assign("homeworksubmittime",$homeworksubmittime);
		$this->assign("homeworkmaxscore",round($homeworkmaxscore,1));
		$this->assign("homeworkaveragescore",$homeworkaveragescore);
		$this->display();
    }

  //班级学生作答情况统计
  //班级学生作答情况统计
  public function getClassSituation(){
  	session_write_close();
  	$homeworkid=I("homeworkid");
  	$classid=I("classid");
  	$wars=array();
	$wsrs=array();
	$wcrs=array();
	$wrrs=array();
	$tars=array();
	$eqrs=array();
  	$sql="(SELECT  t.id, (CASE WHEN s.word_readid IS NOT NULL  THEN answernum ELSE 0  END) AS isdo,(CASE WHEN s.word_readid IS NOT NULL  THEN score ELSE 0  END) AS score,'2' AS hwtype,'100' AS quesscore,'1' as typeid,t.id as sortid FROM engs_homework_word_read t LEFT JOIN (SELECT  word_readid,COUNT(*) AS answernum,AVG(score) AS score  FROM engs_homework_student_word_read WHERE classid = '".$classid."' AND homeworkid = ".$homeworkid." AND issubmit=1 GROUP BY word_readid) s  ON t.id = s.word_readid WHERE t.homeworkid = ".$homeworkid.")  UNION ALL ";
	$sql=$sql."(SELECT t.id,(CASE WHEN s.word_evaluatid IS NOT NULL THEN s.answernum ELSE 0  END) AS isdo ,(CASE WHEN s.word_evaluatid IS NOT NULL THEN s.score ELSE 0 END) AS score,'0' AS hwtype,'1' as quesscore,t.typeid,t.sortid FROM engs_homework_word_evaluat t LEFT JOIN (SELECT word_evaluatid,COUNT(*) AS answernum,SUM(iscorrect) AS score FROM engs_homework_student_word_evaluat  WHERE classid = '".$classid."'  AND homeworkid = ".$homeworkid." and issubmit=1 GROUP BY word_evaluatid) s  ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." ORDER BY t.sortid,t.id) UNION ALL ";
	$sql=$sql." (SELECT t.`id`,(CASE WHEN s.textid IS NOT NULL THEN s.answernum  ELSE 0  END) AS isdo,(CASE WHEN s.textid IS NOT NULL  THEN s.score ELSE 0 END) AS score ,'3' AS hwtype,'100' as quesscore,'1' as typeid,t.textsortid FROM engs_homework_text_read eh,engs_text t  LEFT JOIN (SELECT textid,COUNT(*) AS answernum,AVG(score) AS score FROM engs_homework_student_text_read WHERE  classid = '".$classid."'  AND homeworkid = ".$homeworkid." and issubmit=1 group by textid) s ON t.`id` = s.textid WHERE eh.`homeworkid` = ".$homeworkid." AND eh.`chapterid` = t.`chapterid` ORDER BY  t.textsortid) UNION ALL ";
	$sql=$sql."(SELECT t.questionsid AS id,(CASE WHEN s.answerid IS NOT NULL  THEN s.answernum  ELSE 0  END) AS isdo,(CASE WHEN s.answerid IS NOT NULL  THEN s.score ELSE 0 END) AS score ,'1' AS hwtype,st.question_score as quesscore,'1' as typeid,e.paper_sortid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT z.answerid,COUNT(*) AS answernum,SUM(iscorrect) AS score  FROM engs_homework_student_quiz z  WHERE  classid = '".$classid."'  AND homeworkid = ".$homeworkid." AND issubmit=1 GROUP BY answerid) s  ON t.id = s.answerid ,engs_homework_quiz eq ,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." ORDER BY e.paper_sortid) order by hwtype,sortid,id";

		$result=M()->query($sql);
		foreach($result as $key=>$value){
			$temp=array();
			if($value["hwtype"]=='0'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				$temp["quesscore"]=$value["quesscore"];
				if($value["typeid"]==0){
					array_push($wcrs,$temp);
				}else if($value["typeid"]==2){
					array_push($wsrs,$temp);
				}else if($value["typeid"]==1||$value["typeid"]==3){
					array_push($wrrs,$temp);
				}
			}else if($value["hwtype"]=='1'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				$temp["quesscore"]=$value["quesscore"];
				array_push($eqrs,$temp);
			}else if($value["hwtype"]=='2'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				$temp["quesscore"]=$value["quesscore"];
				array_push($wars,$temp);
			}else if($value["hwtype"]=='3'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				$temp["quesscore"]=$value["quesscore"];
				array_push($tars,$temp);
			}
		}
		$arr["wa"]=$wars;
		$arr["ws"]=$wsrs;
		$arr["wc"]=$wcrs;
		$arr["wr"]=$wrrs;
		$arr["ta"]=$tars;
		$arr["eq"]=$eqrs;
		$this->ajaxReturn($arr);

  }

  	//学生作答情况统计
  	public function getStudentSituation(){
	  	$homeworkid=I("homeworkid");
	  	$studentid=I("studentid");
	  	$classid=I("classid");
	  	$wars=array();
		$wsrs=array();
		$wcrs=array();
		$wrrs=array();
		$tars=array();
		$eqrs=array();
		$user = array();
		$user["studentid"] = $studentid;
		$user["classid"] = $classid;
		$homework["homeworkid"] = $homeworkid;
		$workService = new \Homework\Service\WorkService();
		$homeworkrs = $workService -> getQuestionInfo($user,$homeworkid);
		//遍历不同的试题情况
		if(!empty($homeworkrs["wordread"])){
			foreach($homeworkrs["wordread"] as $key=>$value){
				//查询当前试题的分数
				$temp=array();
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["useranswer"]["useranswer"] == ""?0:1;
				$temp["score"]=$value["useranswer"]["userscore"];
				$temp["quesscore"]=100;
				array_push($wars,$temp);
			}
		}


		if(!empty($homeworkrs["textread"])){
			foreach($homeworkrs["textread"] as $key=>$value){
				//查询当前试题的分数
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["useranswer"]["useranswer"] == ""?0:1;
				$temp["score"]=$value["useranswer"]["userscore"];
				$temp["quesscore"]=100;
				array_push($tars,$temp);
			}
		}

		if(!empty($homeworkrs["wordtest"])){
			//var_dump($homeworkrs["wordtest"]);exit;
			foreach($homeworkrs["wordtest"] as $key=>$value){
				//查询当前试题的分数
				$temp=array();
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["useranswer"]["useranswer"] == ""?0:1;
				$temp["score"]=$value["useranswer"]["userscore"];
				$temp["quesscore"]=1;
				if($value["typeid"]==0){
					array_push($wcrs,$temp);
				}else if($value["typeid"]==2){
					array_push($wsrs,$temp);
				}else if($value["typeid"]==1||$value["typeid"]==3){
					array_push($wrrs,$temp);
				}
			}
		}

		if(!empty($homeworkrs["exampaper"])){
			foreach($homeworkrs["exampaper"] as $key=>$value){
				if($value["useranswer"]["userscore"] > 0){
					$quesscore = $value["useranswer"]["userscore"];
					break;
				}
			}
			if(empty($quesscore) || $quesscore == ''){
				$quesscore = 1;
			}
			foreach($homeworkrs["exampaper"] as $key=>$value){
				//查询当前试题的分数
				$temp=array();
				$temp["id"]=$value["ques_id"];
				$temp["isdo"]=$value["useranswer"]["useranswer"] == ""?0:1;
				$temp["score"]=$value["useranswer"]["userscore"];
				$temp["quesscore"]=$quesscore;
				array_push($eqrs,$temp);
			}
		}

  	    // $sql="(SELECT  t.id,( CASE WHEN s.studentid IS NOT NULL  THEN 1  ELSE 0  END) AS isdo,(CASE WHEN s.studentid IS NOT NULL THEN score ELSE 0  END) AS score,'2' AS hwtype,'100' as quesscore,'1' as typeid,t.id as sortid FROM engs_homework_word_read t  LEFT JOIN (SELECT * FROM engs_homework_student_word_read  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_readid WHERE t.homeworkid = ".$homeworkid." order by t.id) UNION ALL ";
		// $sql=$sql."(SELECT t.id,(CASE WHEN s.studentid IS NOT NULL THEN 1 ELSE 0  END) AS isdo ,s.iscorrect AS iscorrect,'0' AS hwtype,'1' as quesscore,t.typeid,t.sortid as sortid FROM engs_homework_word_evaluat t LEFT JOIN (SELECT *  FROM engs_homework_student_word_evaluat  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." ORDER BY t.sortid,t.id) UNION ALL ";
		// $sql=$sql."(SELECT t.`id`,(CASE WHEN s.studentid IS NOT NULL THEN 1  ELSE 0  END) AS isdo,(CASE WHEN s.studentid IS NOT NULL  THEN score ELSE 0 END) AS score ,'3' AS hwtype,'100' as quesscore,'1' as typeid,t.textsortid as sortid FROM engs_homework_text_read eh,engs_text t  LEFT JOIN (SELECT * FROM engs_homework_student_text_read WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.textid WHERE eh.`homeworkid` = ".$homeworkid." AND eh.`chapterid` = t.`chapterid` ORDER BY  t.textsortid) UNION ALL ";
		// $sql=$sql."(SELECT t.questionsid AS id,(CASE WHEN s.studentid IS NOT NULL  THEN 1  ELSE 0  END) AS isdo,s.iscorrect,'1' AS hwtype,st.question_score as quesscore,'1' as typeid,e.paper_sortid as sortid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_homework_student_quiz z  WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.answerid ,engs_homework_quiz eq ,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." ORDER BY e.paper_sortid) order by hwtype,sortid,id";
		// $result=M()->query($sql);
		// foreach($result as $key=>$value){
		// 	$temp=array();
		// 	if($value["hwtype"]=='0'){
		// 		$temp["id"]=$value["id"];
		// 		$temp["isdo"]=$value["isdo"];
		// 		$temp["score"]=$value["score"];
		// 		$temp["quesscore"]=$value["quesscore"];
		// 		if($value["typeid"]==0){
		// 			array_push($wcrs,$temp);
		// 		}else if($value["typeid"]==2){
		// 			array_push($wsrs,$temp);
		// 		}else if($value["typeid"]==1||$value["typeid"]==3){
		// 			array_push($wrrs,$temp);
		// 		}
				
		// 	}else if($value["hwtype"]=='1'){
		// 		$temp["id"]=$value["id"];
		// 		$temp["isdo"]=$value["isdo"];
		// 		$temp["score"]=$value["score"];
		// 		$temp["quesscore"]=$value["quesscore"];
		// 		array_push($eqrs,$temp);
		// 	}else if($value["hwtype"]=='2'){
		// 		$temp["id"]=$value["id"];
		// 		$temp["isdo"]=$value["isdo"];
		// 		$temp["score"]=$value["score"];
		// 		$temp["quesscore"]=$value["quesscore"];
		// 		array_push($wars,$temp);
		// 	}else if($value["hwtype"]=='3'){
		// 		$temp["id"]=$value["id"];
		// 		$temp["isdo"]=$value["isdo"];
		// 		$temp["score"]=$value["score"];
		// 		$temp["quesscore"]=$value["quesscore"];
		// 		array_push($tars,$temp);
		// 	}
		// }
		$arr["wa"]=$wars;
		$arr["ws"]=$wsrs;
		$arr["wc"]=$wcrs;
		$arr["wr"]=$wrrs;
		$arr["ta"]=$tars;
		$arr["eq"]=$eqrs;
		$this->ajaxReturn($arr);
    }


  	
    //作业的汇总
    public function getSummary(){
    	session_write_close();
  	  $homeworkid=I("homeworkid");
	  	$studentid=I("studentId");
	  	$classid=I("classId");
	  	$iserror=I("iserror/d",0);
	  	$wars=array();
		$wsrs=array();
		$wcrs=array();
		$wrrs=array();
		$tars=array();
		$eqrs=array();
		$sql="";
		if($iserror==0){
			$sql="(SELECT  t.id,s.studentid AS isdo,s.score AS score,'2' AS hwtype,'1' as itemtype,t.id as sortid FROM engs_homework_word_read t  LEFT JOIN (SELECT * FROM engs_homework_student_word_read  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_readid WHERE t.homeworkid = ".$homeworkid." and t.wordid is not null order by t.id ) UNION ALL ";
			$sql=$sql."(SELECT t.id,s.studentid AS isdo ,s.iscorrect as score,'0' AS hwtype,t.typeid as itemtype,t.sortid as sortid FROM engs_homework_word_evaluat t LEFT JOIN (SELECT *  FROM engs_homework_student_word_evaluat  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." ORDER BY t.sortid desc,t.id)  UNION ALL ";
			$sql=$sql."(SELECT t.`id`,s.studentid AS isdo,(CASE WHEN s.studentid IS NOT NULL  THEN score ELSE 0 END) AS score ,'3' AS hwtype,'1' as itemtype,t.textsortid as sortid FROM engs_homework_text_read eh,engs_text t  LEFT JOIN (SELECT * FROM engs_homework_student_text_read WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.textid WHERE eh.`homeworkid` = ".$homeworkid." AND eh.`chapterid` = t.`chapterid` ORDER BY  t.textsortid) UNION ALL ";
			$sql=$sql."(SELECT t.questionsid AS id,s.studentid AS isdo,s.iscorrect,'1' AS hwtype,'1' as itemtype,e.paper_sortid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_homework_student_quiz z  WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.answerid ,engs_homework_quiz eq,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." ORDER BY e.paper_sortid)  order by hwtype,sortid,id";
		}else{
		    $sql="(SELECT  t.id,s.studentid AS isdo,s.score AS score,'2' AS hwtype,'1' as itemtype,t.id as sortid FROM engs_homework_word_read t  LEFT JOIN (SELECT * FROM engs_homework_student_word_read  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_readid WHERE t.homeworkid = ".$homeworkid." and t.wordid is not null order by t.id)  UNION ALL ";
		    $sql=$sql."(SELECT t.id,s.studentid AS isdo ,s.iscorrect as score,'0' AS hwtype,t.typeid as itemtype,t.sortid FROM engs_homework_word_evaluat t LEFT JOIN (SELECT *  FROM engs_homework_student_word_evaluat  WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." ORDER BY t.sortid desc,t.id) UNION ALL ";
		    $sql=$sql."(SELECT t.`id`,s.studentid AS isdo,(CASE WHEN s.studentid IS NOT NULL  THEN score ELSE 0 END) AS score ,'3' AS hwtype,'1' as itemtype,t.textsortid as sortid FROM engs_homework_text_read eh,engs_text t  LEFT JOIN (SELECT * FROM engs_homework_student_text_read WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.textid WHERE eh.`homeworkid` = ".$homeworkid." AND eh.`chapterid` = t.`chapterid` ORDER BY  t.textsortid) UNION ALL ";
		    $sql=$sql."(SELECT t.questionsid AS id,s.studentid AS isdo,s.iscorrect,'1' AS hwtype,'1' as itemtype,e.paper_sortid as sortid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_homework_student_quiz z  WHERE  studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s  ON t.id = s.answerid ,engs_homework_quiz eq,engs_exams_stem st  WHERE st.id=e.stemid and eq.examsid=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`homeworkid`=".$homeworkid." ORDER BY e.paper_sortid) order by hwtype,sortid,id";
		}
		$result=M()->query($sql);
		foreach($result as $key=>$value){
			$temp=array();
			if($value["hwtype"]=='0'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				if($value["itemtype"]==0){
					array_push($wcrs,$temp);
				}else if($value["itemtype"]==1||$value["itemtype"]==3){
					array_push($wrrs,$temp);
				}else if($value["itemtype"]==2){
					array_push($wsrs,$temp);
				}
				
			}else if($value["hwtype"]=='1'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				array_push($eqrs,$temp);
			}else if($value["hwtype"]=='2'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				array_push($wars,$temp);
			}else if($value["hwtype"]=='3'){
				$temp["id"]=$value["id"];
				$temp["isdo"]=$value["isdo"];
				$temp["score"]=$value["score"];
				array_push($tars,$temp);
			}
		}
		$arr["wa"]=$wars;
		$arr["ws"]=$wsrs;
		$arr["wc"]=$wcrs;
		$arr["wr"]=$wrrs;
		$arr["ta"]=$tars;
		$arr["eq"]=$eqrs;
		$this->ajaxReturn($arr);
    }



    //作业的汇总
  	public function getPreviewSummary(){
  		session_write_close();
  		$wa=I("wa");
	    $wa = rtrim($wa, '"');
	    $wa = ltrim($wa, '"');
	    $wa = str_replace('&quot;', '"', $wa);
		$ta=I("ta");
	    $ta = rtrim($ta, '"');
	    $ta = ltrim($ta, '"');
	    $ta = str_replace('&quot;', '"', $ta);
		$ws=I("ws");
	    $ws = rtrim($ws, '"');
	    $ws = ltrim($ws, '"');
	    $ws = str_replace('&quot;', '"', $ws);
	    $wc=I("wc");
	    $wc = rtrim($wc, '"');
	    $wc = ltrim($wc, '"');
	    $wc = str_replace('&quot;', '"', $wc);
	   	$wr=I("wr");
	    $wr = rtrim($wr, '"');
	    $wr = ltrim($wr, '"');
	    $wr = str_replace('&quot;', '"', $wr);
	    $eq=I("eq");
	    $eq = rtrim($eq, '"');
	    $eq = ltrim($eq, '"');
	    $eq = str_replace('&quot;', '"', $eq);
		$wat=json_decode($wa);
		$tat=json_decode($ta);
	    $wst=json_decode($ws);
	    $wct=json_decode($wc);
	    $wrt=json_decode($wr);
	    $eqt=json_decode($eq);
		if(count($eqt)>0){
			$examsid=$eqt[0]->id;
			$questionanswersql="select '".$homeworkId."' as homeworkid,'".$studentId."' as studentid,'".$classId."' as classid,engs_exams_stem.examsid,t.id as quesansid,";
			$questionanswersql=$questionanswersql."t.questionsid as quesid,t.answer as quesanswer,t.answer_num,s.iscorrect from engs_exams_questions_answer t left join ";
			$questionanswersql=$questionanswersql."(select * from engs_homework_student_quiz  where homeworkid='".$homeworkId."' and studentid='".$studentId."' and ";
			$questionanswersql=$questionanswersql."classid='".$classId."' and examid='".$examsid."') as s on t.id=s.answerid,engs_exams_stem,engs_exams_questions";
			$questionanswersql=$questionanswersql."  where engs_exams_stem.id=engs_exams_questions.stemid and t.isdel=1 AND t.questionsid=engs_exams_questions.id and engs_exams_questions.examsid=".$examsid." and ";
			$questionanswersql=$questionanswersql."  engs_exams_stem.id in (select id from engs_exams_stem where engs_exams_stem.isdel=1 and engs_exams_stem.examsid=";
			$questionanswersql=$questionanswersql.$examsid.")";
			$questionanswersql=$questionanswersql." order by engs_exams_stem.sortid,engs_exams_stem.id,engs_exams_questions.sortid,engs_exams_questions.id";
			$questionanswersql=$questionanswersql.",t.sortid,t.id";
			$questionanswerrs=M()->query($questionanswersql);
			$eq=$questionanswerrs;
		}else{
			$eq=array();
		}
		$ws=array();
		foreach($wst as $key=>$value){
			array_push($ws,$value->id);
		}
		$wc=array();
		foreach($wct as $key=>$value){
			array_push($wc,$value->id);
		}
		$wr=array();
		foreach($wrt as $key=>$value){
			array_push($wr,$value->id);
		}
		$wa=array();
		foreach($wat as $key=>$value){
			array_push($wa,$value->id);
		}
		$ta=array();
		foreach($tat as $key=>$value){
			$watsql="select * from engs_text where isdel=1 and chapterid=".$value->id;
			$watextrs=M()->query($watsql);
			foreach($watextrs as $key=>$values){
				array_push($ta,$values["id"]);
			}
		}
		if(empty($eq)){
			$arr['examsquiz']=array();
		}else{
			$arr["examsquiz"]=$eq;
		}
		if(empty($ws)){
			$arr["wordspell"]=array();
		}else{
			$arr["wordspell"]=$ws;
		}
		if(empty($wc)){
			$arr["wordchoose"]=array();
		}else{
			$arr["wordchoose"]=$wc;
		}
		if(empty($wr)){
			$arr["wordrate"]=array();
		}else{
			$arr["wordrate"]=$wr;
		}
		if(empty($wa)){
			$arr["wordalound"]=array();
		}else{
			$arr["wordalound"]=$wa;
		}
		if(empty($ta)){
			$arr["textalound"]=array();
		}else{
			$arr["textalound"]=$ta;
		}
		$this->ajaxReturn($arr);
 	}

  	//教师反馈
  	public function feedback(){
	  	session_write_close();
	  	//作业总分数以及学生的分数
	  	$batchid=I("batchid");
	  	$homeworkid=I("homeworkid");
	  	$studentid=I("studentid");
		$classid=I("classid");
		$tqms=I("tqms");

		getHomeworkPublishStudents($tqms,$homeworkid,$batchid);
	  	//查询出没作业的数据
	  	//$homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
	  	$homeworkrs=M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->find();
		$yjArr=get_ip("",$tqms);
	    $sso=$yjArr['tms']['c1'];
	    cookie("sso",$sso);
	    $publishstudents=json_decode($homeworkrs["publishstudents"],TRUE);
	   	
	    //查看数组的长度
	    $classNum=count($publishstudents);
	    $this->assign('classNum',$classNum);
	    if(empty($classid)){
		    foreach($publishstudents as $key=>$value){
		    	$classid=$key;
		    	break;
		    }
		    //获取班级名称
	        $classname=getClassName($sso,$classid);
	        $publishstudents[$key]["className"]=$classname;
            //更新数据库
            M("homework")->where("id=%d",$homeworkid)->setField("publishstudents",json_encode($publishstudents));
            M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->setField("publishstudents",json_encode($publishstudents));
	    }else{
	    	$classname=$publishstudents[$classid]["className"];
	    }
	    //获取班级人数
	    $classstucount=$publishstudents[$classid]["studentCount"];
	    $this->assign("classid",$classid);
	    $this->assign("studentcount",$classstucount);
        $this->assign("classname",$classname);
		$publishstudents=json_decode($homeworkrs["publishstudents"],TRUE);
		$students=$publishstudents[$classid]["students"];
		$homeworkmaxscore=0.0;
		$homeworkaveragetime=0.0;
		$homeworksubmitnum=0;
		foreach($students as $key=>$value){
			//获取最高分以及平均分
			if($value["issubmit"]==1){
				$homeworkaveragetime=$homeworkaveragetime+$value["time"];
				$homeworksubmitnum=$homeworksubmitnum+1;
				if($value["score"]>$homeworkmaxscore){
					$homeworkmaxscore=$value["score"];
				}
			}

		}
		$studocount=M("homework_student_list")->where("paper_id=%d and classid='%s' and submittime is not null",$homeworkid,$classid)->count();
        //$students["submitCount"]=$studocount;
		$homeworkaveragetime=round($homeworkaveragetime/$homeworksubmitnum,1);
		$this->assign("batchid",$batchid);
		$this->assign("homeworkid",$homeworkid);
		$this->assign("homeworkname",$homeworkrs["paper_name"]);
		$this->assign("studentid",$studentid);
		$this->assign("tqms",$tqms);
		$this->assign("homeworksubmitnum",$studocount);
		$this->assign("homeworkmaxscore",round($homeworkmaxscore,1));
		$this->assign("homeworkaveragetime",$homeworkaveragetime);
	  	$this->display();
    }



    public function getHomeworkQuestion(){
    	session_write_close();
		//接收类型
		$type=I("type/d");//0表示单词测试 1表示听力训练
		//接收的是第几个数据
		$index=I("index/d",0);
		//是否是错题统计
		$iserror=I("iserror/d");
		//学生账号
		$studentid=I("studentId");
		//学生班级
		$classid=I("classId");
		//是否提交
		$issubmit=I("issubmit/d",0);
		//作业
		$homeworkid=I("homeworkid/d");
		//接收单词测试的个数
		$wscount=I("wscount/d",0);
		$wccount=I("wccount/d",0);
		$wrcount=I("wrcount/d",0);
		//接收单词朗读的个数
		$wacount=I("wacount/d",0);
		//接收课文朗读的个数
		$tacount=I("tacount/d",0);
		//单词测试的刷新数据
		if($type==0){
			//表示接受的是单词测试的数据
			$index=$index-$wacount;
			//这里进行分流
			if($wscount>0&&$index<$wscount){
				//$index=$index-$wacount;
				$wtsql="select '".$studentid."' as studentid,'1' as isvoice,'".$classid."' as classid,t.id,t.wordid,t.homeworkid,t.answer as quesans,s.answer as userans,";
				$wtsql=$wtsql."ew.`word` AS word,ep.`explains` AS explains,eb.ukmark,eb.`ukmp3` AS mp3,t.option_a,t.option_b,t.option_c,";
				$wtsql=$wtsql."t.typeid,s.iscorrect from engs_homework_word_evaluat t left join (select * from engs_homework_student_word_evaluat";
				$wtsql=$wtsql." where studentid='".$studentid."' and homeworkid='".$homeworkid."') s on t.id=s.word_evaluatid,";
			    $wtsql=$wtsql."engs_word ew left join engs_base_word_explains ep ON ew.`base_explainsid`=ep.`id` AND ew.`isdel`=1,engs_base_word eb";
				$wtsql=$wtsql." where t.typeid=2 and t.homeworkid=".$homeworkid." AND t.`wordid`=ew.`id` AND ew.`base_wordid`=eb.`id`";
				if($iserror=='1'){
					$wtsql=$wtsql." and (s.iscorrect = 0 OR s.iscorrect IS NULL)";
				}
				$wtsql=$wtsql." order by t.typeid, t.sortid,t.id  limit ".$index.",1";

			}else if($wccount>0&&$index<($wscount+$wccount)){
				$index=$index-$wscount;
				$wtsql="select '".$studentid."' as studentid,'".$classid."' as classid,'1' as isvoice,t.id,t.wordid,t.homeworkid,t.answer as quesans,s.answer as userans,";
				$wtsql=$wtsql."ew.`word` AS word,ep.`explains` AS explains,eb.ukmark,eb.`ukmp3` AS mp3,t.option_a,t.option_b,t.option_c,";
				$wtsql=$wtsql."t.typeid,s.iscorrect from engs_homework_word_evaluat t left join (select * from engs_homework_student_word_evaluat";
				$wtsql=$wtsql." where studentid='".$studentid."' and homeworkid='".$homeworkid."') s on t.id=s.word_evaluatid,";
			    $wtsql=$wtsql."engs_word ew left join engs_base_word_explains ep ON ew.`base_explainsid`=ep.`id` AND ew.`isdel`=1,engs_base_word eb";
				$wtsql=$wtsql." where t.typeid=0 and t.homeworkid=".$homeworkid." AND t.`wordid`=ew.`id` AND ew.`base_wordid`=eb.`id`";
				if($iserror=='1'){
					$wtsql=$wtsql." and (s.iscorrect = 0 OR s.iscorrect IS NULL)";
				}
				$wtsql=$wtsql." order by t.typeid, t.sortid,t.id  limit ".$index.",1";
			}else if($wrcount>0&&$index<($wscount+$wccount+$wrcount)){
				$index=$index-$wscount-$wccount;
				$wtsql="select '".$studentid."' as studentid,'".$classid."' as classid,'0' as isvoice,t.id,t.wordid,t.homeworkid,t.answer as quesans,s.answer as userans,";
				$wtsql=$wtsql."ew.`word` AS word,ep.`explains` AS explains,eb.ukmark,eb.`ukmp3` AS mp3,t.option_a,t.option_b,t.option_c,";
				$wtsql=$wtsql."t.typeid,s.iscorrect from engs_homework_word_evaluat t left join (select * from engs_homework_student_word_evaluat";
				$wtsql=$wtsql." where studentid='".$studentid."' and homeworkid='".$homeworkid."') s on t.id=s.word_evaluatid,";
			    $wtsql=$wtsql."engs_word ew left join engs_base_word_explains ep ON ew.`base_explainsid`=ep.`id` AND ew.`isdel`=1,engs_base_word eb";
				$wtsql=$wtsql." where (t.typeid=1 or t.typeid=3) and t.homeworkid=".$homeworkid." AND t.`wordid`=ew.`id` AND ew.`base_wordid`=eb.`id`";
				if($iserror=='1'){
					$wtsql=$wtsql." and (s.iscorrect = 0 OR s.iscorrect IS NULL)";
				}
				$wtsql=$wtsql." order by t.typeid, t.sortid,t.id  limit ".$index.",1";
				//echo $wtsql;exit;
			}
			$wtrs=M()->query($wtsql);
			if($wtrs[0]["typeid"]==0){
				$wtrs[0]["tcontent"]=$wtrs[0]["explains"];
			}else{
				$wtrs[0]["tcontent"]=$wtrs[0]["word"];
			}
			$wtitems=array();
			$wttemp["flag"]="A";
			$wttemp["content"]=$wtrs[0]["option_a"];
			array_push($wtitems,$wttemp);
			unset($wttemp);
			$wttemp["flag"]="B";
			$wttemp["content"]=$wtrs[0]["option_b"];
			array_push($wtitems,$wttemp);
			unset($wttemp);
			$wttemp["flag"]="C";
			$wttemp["content"]=$wtrs[0]["option_c"];
			array_push($wtitems,$wttemp);
			unset($wttemp);
			$rss["question"]=$wtrs[0];
			$rss["items"]=$wtitems;
			$wtanswer["quesanswer"]=$wtrs[0]["quesans"];
			$wtanswer["answer"]=$wtrs[0]["userans"];
			$wtanswer["quesansid"]=$wtrs[0]["id"];
			$wtanswer["quesid"]=$wtrs[0]["id"];
			$words=str_split($wtrs[0]["quesans"]);
			$wtanswer["words"]=$words;
			$wtanswer["userwords"]=explode(",", $wtrs[0]["userans"]);
			//班级汇总数据作答情况
			$sumsql="select count(*) as answernum,sum(case when iscorrect='1' then 1 else 0 end) as accnum,t.answer from engs_homework_student_word_evaluat t where t.issubmit=1 and t.homeworkid=".$homeworkid." and t.classid='".$classid."' and word_evaluatid=".$wtrs[0]['id']." group by t.homeworkid,t.classid,t.answer";
			$sumrs=M()->query($sumsql);
			$summarydata=0;
			$arrnum=0;
			$erroranswer="";
			$max=0;
			foreach($sumrs as $key=>$value){
				$userquestionsanswer=$value["answer"];
				$summarydata=$summarydata+$value["answernum"];
				$arrnum=$arrnum+$value["accnum"];
				$errrate=100-round($value["accnum"]*100/$value["answernum"]);
				$usersanswer=$wtrs[0]['quesans'];
				if($wtrs[0]['typeid']=='2'){
					$userquestionsanswer=explode(",", $userquestionsanswer);
					$userquestionsanswer=implode("",$userquestionsanswer);
				}
				if($max<=$errrate&&$userquestionsanswer!=$usersanswer){
					$max=$errrate;
					$erroranswer=$value["answer"];
				}
			}
			
			if(empty($summarydata)||$summarydata==0){
				$errorrate="0%";
			}else{
				$errorrate=round($arrnum*100/$summarydata)."%";
			}
			
			$answera["accrate"]=$errorrate;
			$answera["answernum"]=$summarydata;
			$answera["erroranswer"]=$erroranswer;
			if($wtrs[0]['typeid']=='2'){
				$wordrs=str_split($wtrs[0]['quesans']);
				$wtrs[0]["words"]=$wordrs;
				$itemsrs=explode(",", $wtrs[0]['option_a']);
				$itemarray=array();
				foreach($itemsrs as $key=>$value){
					if($value!=""){
						array_push($itemarray,$value);
					}
				}
				$wtrs[0]["items"]=$itemarray;
				$userwordsrs=explode(",", $wtrs[0]['userans']);
				$wtrs[0]["userwords"]=$userwordsrs;
				if(count($erroranswer)>0){
					$erroranswer=implode("",explode(",", $erroranswer));
				}
			}
			//学生作答的汇总数据
			$summarr=array("A"=>0,"B"=>0,"C"=>0);
			if($wtrs[0]['typeid']!='2'){
				$summarysql="select t.answer,count(*) as per from engs_homework_student_word_evaluat t where t.issubmit=1 and t.classid='".$classid."' and t.homeworkid=".$homeworkid." and t.word_evaluatid=".$wtrs[0]["id"]."  group by t.answer";
				$summaryrs=M()->query($summarysql);
				foreach($summaryrs as $key=>$value){
					$temp=array();
					if($value["answer"]=='A'){

						$summarr["A"]=($value["per"]/$summarydata)*100;
					}else if($value["answer"]=='B'){
						$summarr["B"]=($value["per"]/$summarydata)*100;
					}else if($value["answer"]=='C'){
						$summarr["C"]=($value["per"]/$summarydata)*100;
					}
				}
			}
			$rss["summary"]=$summarr;
			$wtanswer["summary"]=$answera;
			//班级汇总数据结束
			$temp=array();
			array_push($temp,$wtanswer);
			if($wtrs[0]["typeid"]!="2"){
				$rss["answer"]=$temp;
			}else{
				$wtanswer["answer"]=$wtrs[0]["userans"];
				$rss["answer"]=$wtanswer;
			}
			$rs["type"]=0;
			$rs["num"]=16;
			$rs["parent"]=$rss;
			$rs["children"]=array();
		}else if($type==1){
			//查询数据库中的作业中试卷的ID
			$index=$index-$wscount-$wccount-$wrcount-$wacount-$tacount;
			$questionrs=array();
			//查询试题
			$quessql="select '1' as isvoice,s.examsid as examid,eq.id as quizid,".$homeworkid." as homeworkid,t.*,s.parentid,s.content as stemcontent";
			$quessql=$quessql." from engs_exams_questions t left join engs_exams_stem s on t.stemid=s.id,engs_homework_quiz eq where (t.isdel=1";
			$quessql=$quessql." and s.isdel=1)  and t.examsid=eq.examsid and eq.homeworkid=".$homeworkid;
			if($iserror=='1'){
				$quessql=$quessql." and t.id not in (select distinct questionsid from engs_homework_student_quiz z where z.homeworkid=".$homeworkid." and z.studentid='".$studentid."' and z.iscorrect=1) ";
			}
			$quessql=$quessql." order by s.sortid,s.id,t.sortid,t.id limit ".$index.",1";
			$questionrs=M()->query($quessql);
			$examsid=$questionrs[0]["examid"];
			$quizid=$questionrs[0]["quizid"];
			$questionrs[0]["examsid"]=$examsid;
			$questionrs[0]["stemcontent"]=relace_tcontent($questionrs[0]["stemcontent"]);
			$questionrs[0]["tcontent"]=relace_tcontent($questionrs[0]["tcontent"]);
			$questionrs[0]["questions_answer"]=urldecode($questionrs[0]["questions_answer"]);
			$questionrs[0]["questions_tts"]=urldecode($questionrs[0]["questions_tts"]);
			$questions_items=(array)json_decode(urldecode($questionrs[0]["questions_items"]));
			//如果是图片的话 直接进行base64编码减少数据的请求
			if($questionrs[0]["itemtype"]=="1"){
				foreach($questions_items as $key=>$value){
					$filepath=C("CONST_UPLOADS").$value->content;
					$image_info = getimagesize($filepath);
					$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
					$questions_items[$key]->imgcontent=$base64_image_content;
				}
			}
			$questionrs[0]["questions_items"]=$questions_items;
			$questionid=$questionrs[0]["id"];
		    $childquestionrs=$this->getParentStem($questionrs[0]["stemid"]);


			//查询问题的答案以及用户的答案
			$questionanswersql="select t.id as quesansid,t.questionsid as quesid,t.answer as quesanswer,t.answer_num,s.* from engs_exams_questions_answer t left join ";
			$questionanswersql=$questionanswersql."(select * from engs_homework_student_quiz  where homeworkid=".$homeworkid." and studentid='".$studentid."' and ";
			$questionanswersql=$questionanswersql." examid=".$examsid." and questionsid=".$questionid.") as s";
			$questionanswersql=$questionanswersql." on t.id=s.answerid where t.isdel=1 AND t.questionsid=".$questionid;
			$questionanswerrs=M()->query($questionanswersql);
			$questionanswer=$questionanswerrs[0]["quesanswer"];

			//查询做这个作业的人数
			$answernumsql="select count(distinct t.studentid) as num from engs_homework_student_list t where t.submittime is not null and t.paper_id=".$homeworkid." and t.classid='".$classid."'";
			$answernumrs=M()->query($answernumsql);
			$answernum=$answerrs[0]['num'];

			//这里查询需要优化
			$questionratesql="SELECT answerid,answer,SUM(CASE WHEN iscorrect = 1 THEN 1 ELSE 0  END) AS accnum,COUNT(DISTINCT studentid) AS answernum  FROM engs_homework_student_quiz where issubmit=1 and homeworkid=".$homeworkid." and ";
			$questionratesql=$questionratesql."classid='".$classid."' and examid=".$examsid." and questionsid=".$questionid." GROUP BY answerid,answer ORDER BY accnum asc";
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
			if(empty($answernumtemp)||$answernumtemp==0){
				$data["accrate"]="0%";
			}else{
				$data["accrate"]=round($accnumtemp*100/$answernumtemp)."%";
			}
			if(round($accnumtemp*100/$answernumtemp)==100){
				$data["erroranswer"]="";
			}else{
				$data["erroranswer"]=$maxerroranswer;
			}
			$questionanswerrs[0]["summary"]=$data;
			//学生作答的汇总数据
			$summarysql="select t.answer,count(*) as per from engs_homework_student_quiz t where t.issubmit=1 and t.classid='".$classid."' and t.homeworkid=".$homeworkid." and t.questionsid=".$questionid." group by t.answer";
			$summaryrs=M()->query($summarysql);
			$summarr=array("A"=>0,"B"=>0,"C"=>0);
			foreach($summaryrs as $key=>$value){
				$temp=array();
				if($value["answer"]=='A'){
					$summarr["A"]=($value["per"]/$answernumtemp)*100;
				}else if($value["answer"]=='B'){
					$summarr["B"]=($value["per"]/$answernumtemp)*100;
				}else if($value["answer"]=='C'){
					$summarr["C"]=($value["per"]/$answernumtemp)*100;
				}
			}
			$ques["question"]=$questionrs[0];
			$ques["questts"]=json_decode($questionrs[0]["questions_tts"]);
			$ques["items"]=$questions_items;
			$ques["answer"]=$questionanswerrs;
			$ques["rate"]=$questionraters;
			$ques["summary"]=$summarr;
			$rs["type"]=1;
			$rs["num"]=16;
			$rs["parent"]=$ques;
			$rs["children"]=$childquestionrs[0];
		}else if($type==2){
			//单词朗读的刷新数据
			$wasql="SELECT s.id,ewe.pic,'1' as typeid,ew.word as tncontent,ewe.`morphology` ,ewe.`explains` as cncontent,t.id as readid,t.`wordid` as contentid,t.`homeworkid`,";
			$wasql=$wasql."ROUND(s.score,1) AS userscore,s.mp3 AS usermp3,ebw.ukmp3 as wordmp3,ebw.ukmark FROM engs_word AS ew LEFT JOIN engs_base_word_explains AS ewe ON ew.`base_explainsid`=ewe.`id` AND ew.`isdel`=1,engs_base_word ebw,";
			$wasql=$wasql."engs_homework_word_read t LEFT JOIN (SELECT * FROM engs_homework_student_word_read WHERE ";
			$wasql=$wasql."studentid='".$studentid."'  AND homeworkid='".$homeworkid."') s ON ";
			$wasql=$wasql."t.`id`=s.`word_readid` WHERE ebw.id=ew.base_wordid and t.`homeworkid`='".$homeworkid."' AND t.`wordid`=ew.`id` ";
			$wasql=$wasql." order by ew.sortid,ew.id, t.id limit ".$index.",1";
			
			//查询结果
			$rss=M()->query($wasql);
			$rs["question"]=$rss[0];
			//查询班级数据班级答题人数以及班级的作答人数 正确率 最高分
			$classsql="select count(*) as num,sum(case when t.score is not null and t.score>60 then 1 else 0 end) as accnum,round(max(t.score),1) as  maxscore from engs_homework_student_word_read t where t.issubmit=1 and t.homeworkid='".$homeworkid."' and classid='".$classid."' and word_readid='".$rss[0]['readid']."'";
			$classrs=M()->query($classsql);
			$rs["question"]["classresult"]=$classrs;
			$rs["type"]=2;
		}else if($type==3){
			//课文朗读的刷新数据
			$index=$index-$wscount-$wccount-$wrcount-$wacount;
			$tasql="SELECT s.id,'2' as typeid,eh.id as readid,eh.homeworkid,t.`id` as contentid,t.id AS textid,t.`chapterid`,t.`encontent` as tncontent,t.`cncontent`,ROUND(s.score,1) AS userscore,s.mp3 AS usermp3,t.mp3 as wordmp3 FROM engs_homework_text_read eh,engs_text t  LEFT JOIN ";
			$tasql=$tasql."(SELECT * FROM engs_homework_student_text_read WHERE studentid = '".$studentid."'  AND homeworkid = '".$homeworkid."') s ON t.`id` = s.textid WHERE eh.`homeworkid`=".$homeworkid;
			$tasql=$tasql." AND eh.`chapterid`=t.`chapterid` ORDER BY t.chapterid, t.sortid,eh.id limit ".$index.",1";
			$rss=M()->query($tasql);
			$rs["question"]=$rss[0];
			//查询结果
			//查询班级数据班级答题人数以及班级的作答人数 正确率 最高分
			$classsql="select count(*) as num,sum(case when t.score is not null and t.score>60 then 1 else 0 end) as accnum,round(max(t.score),1) as  maxscore from engs_homework_student_text_read t where t.issubmit=1 and t.homeworkid='".$homeworkid."' and classid='".$classid."' and textid='".$rss[0]['textid']."'";
			$classrs=M()->query($classsql);
			if(empty($classrs)){
				$classrs["num"]=0;
				$classrs["accnum"]=0;
				$classrs["maxscore"]=0;
			}
			$rs["question"]["classresult"]=$classrs;
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

    //预览数据显示
	public function preview(){
		session_write_close();
		$sso=I("sso");
		$tms=I("tms",0);
		//表示是第几道试题
		$index=I("index/d",0);
		//用户类型
		$type=I("type/d","0");
		$quescount=I("quescount");
		$yjArr=get_ip("",$sso);
		$tqms=$yjArr['tqms']['c1'];
		$this->assign("sso",$sso);
		$this->assign("tms",$tms);
		$this->assign("ks_code",$ks_code);
		$this->assign("username",$username);
		$this->assign("tqms",$tqms);
		$this->assign("index",$index);
		$this->assign("quescount",$quescount);
		$this->display();
	}

	//获取问题的TTS发声
	public  function getQuestiontts(){
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

	//获取学生姓名
	public function getAnswerStudentinfo(){
		$answer=I("answer/s");
		$classid=I("classid/d");
		$homeworkid=I("homeworkid/d");
		$questionsid=I("questionsid/d");
		//学生作答的汇总数据
		$summarysql="select t.studentid,t.studentname from engs_homework_student_quiz t where t.classid='".$classid."' and t.answer=".$answer." and t.questionsid=".$questionid." group by t.answer";
		$summaryrs=M()->query($summarysql);
		$this->ajaxReturn($summaryrs);
	}

	//获取答案的列表
	public function getAnswerUserList(){
		$answer=I("answer");
		$quesid=I("quesid");
		$classid=I("classid");
		$homeworkid=I("homeworkid");
		$typeid=I("typeid/d");
		$rs=array();
		if($typeid==1){
			$sql="select * from engs_homework_student_word_evaluat t where t.classid='".$classid."' and t.answer='".$answer."' and homeworkid=".$homeworkid." and word_evaluatid=".$quesid." and issubmit=1";
			$rs=M()->query($sql);
		}else{
			$sql="select t.studentid,t.studentname from engs_homework_student_quiz t where t.homeworkid=".$homeworkid." and t.classid='".$classid."' and t.answer='".$answer."' and t.questionsid=".$quesid." and t.issubmit=1";
			$rs=M()->query($sql);
		}
		$this->ajaxReturn($rs);
	}

	//获取作业的学生
	public function homeworkstudent(){
		$batchid=I("batchid");
		$homeworkid=I("homeworkid");
		$studentid=I("studentid");
		$classid=I("classid");
		$tqms=I("tqms");
	  	//查询出没作业的数据
	  	//$homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
	  	$homeworkrs=M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->find();
		//取出班级的数据
		$publishstudents=json_decode($homeworkrs["publishstudents"],TRUE);
		$classnum=1;
		if(count($publishstudents)==1){
			$classnum=0;
		}
		$classname=$publishstudents[$classid]["classMame"];
		$this->assign("classname",$classname);
		$this->assign("homeworkid",$homeworkid);
		$this->assign("homeworkname",$homeworkrs["homework_name"]);
		$this->assign("studentid",$studentid);
		$this->assign("classid",$classid);
		$this->assign("tqms",$tqms);
		$this->assign("classnum",$classnum);
		$this->assign("batchid",$batchid);
		$this->display();
	}


	//编辑作业的时间
	public function editHomeworkTime(){
		$batchid=I("batchid");
		$homeworkid=I("homeworkid");
		$studentId=I("studentid");
		$classId=I("classid");
		//$homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
		$homeworkrs=M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->find();
		$publishstudents=$homeworkrs["publishstudents"];
		$time=$publishstudents[$classId]["students"][$studentId]["time"];
		$time=$time+1;
		$publishstudents[$classId]["students"][$studentId]["time"]=$time;
		M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$homeworkid)->setField("publishstudents",$publishstudents);
	}

	//用户答题
	public function userAnswerQuestion(){
		//用户信息
		$user = I("user/s");
		$user = rtrim($user, '"');
	    $user = ltrim($user, '"');
	    $user = str_replace('&quot;', '"', $user);
		$question = I("question/s");
		$question = rtrim($question, '"');
	    $question = ltrim($question, '"');
	    $question = str_replace('&quot;', '"', $question);
		$useranswer = I("useranswer/s");
		$useranswer = rtrim($useranswer, '"');
	    $useranswer = ltrim($useranswer, '"');
	    $useranswer = str_replace('&quot;', '"', $useranswer);
		$user = json_decode($user,true);
		$question = json_decode($question,true);
		$useranswer = json_decode($useranswer,true);
		$user["studentname"] = cookie("username");
		$workService = new \Homework\Service\WorkService();
		//var_dump($useranswer);exit;
		try{
			$workService -> setUserAnswer($user,$question,$useranswer);
			$ret["flag"] = "1";
		}
		catch (\Exception $e) {  //如书写为（Exception $e）将无效
	     //echo $e->getMessage();
			$ret["flag"] = "0";
        }
		//数据库中对应的修改时间
		$spendtime = $useranswer["spendtime"];
		if(!empty($spendtime) && is_int(round($spendtime))&&!empty($question["homeworkid"])&&!empty($user["studentid"])){
			M()->execute("update engs_homework_student_list set papertime = papertime +".round($spendtime)." where paper_id = ".$question["homeworkid"]." and studentid='".$user["studentid"]."'");
		}
		$this -> ajaxReturn($ret);
	}

	//获取试题
	public function getHomeworkInfo(){
		$homeworkid = I("homeworkid");
		$username = I("studentid");
		$classid = I("classid");
		$workService = new \Homework\Service\WorkService();
		$user["studentid"]=$username;
		$user["classid"]=$classid;
		$homeworkrs = $workService -> getQuestionInfo($user,$homeworkid);
		$this -> ajaxReturn($homeworkrs);
	}


	//展示学生的页面
	public function examsquizs(){
		$this->display("examsquizs");
	}
}
