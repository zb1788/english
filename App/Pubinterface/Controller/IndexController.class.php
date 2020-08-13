<?php
namespace Pubinterface\Controller;
use Think\Controller;

/**
* 用于获取数据的通用的接口
*
* @author         qgy
* @since          1.0
*/

class IndexController extends Controller {

    //获取单元下的信息
    public function getUnitContentList(){
        $ks_code=I("ks_code");
        $sql="select id,word,'1' as quescount,'w' as type from engs_word t where t.ks_code='".$ks_code." and t.isdel=1' union all select id,chapter,quescount,'t' as type from text_chapter s where s.ks_code='".$ks_code."' and s.isdel=1 union all select id,chapter,quescount,'e' as type from engs_exam z where s.ks_code='".$ks_code."' and z.isdel=1 and z.state=4";
        $rs=M()->query($sql);
        $this->ajaxReturn($rs);
    }

	//获取单词信息
	public function getWordlist(){
		$ks_code=I("ks_code");
        $wlength = 20;
        $wsub = 17;
        $screen_width_id = cookie('screen_width_id');
        if($screen_width_id == 'w980'){
            $strsub = 15;
            $wsub = 12;
        }
		//$rs=M("word")->where("ks_code='%s' and isdel=1",$ks_code)->field("id,(CASE WHEN LENGTH(word)<=".$strsub." THEN word ELSE CONCAT(MID(word,1,".$wsub."),'...') END) AS word,word as title,isstress")->select();
        $rs=M("word")->where("ks_code='%s' and isdel=1",$ks_code)->field("id,word,word as title,isstress")->select();
		$this->ajaxReturn($rs);
	}

	//获取章节课文信息
	public function getChapterList(){
        $ks_code=I("ks_code");
		$rs=M("text_chapter")->where("ks_code='%s' and isdel=1 ",$ks_code)->order('sortid,id')->select();
		$this->ajaxReturn($rs);
	}

	//获取试卷列表
	public function getExamsList(){
        $ks_code=I("ks_code");
		$rs=M("exams")->where("ks_code='%s' and isdel=1 and state=4",$ks_code)->select();
        $this->ajaxReturn($rs);
	}

	//获取试卷信息examsid表示试卷的id,$type表示时候已经提交,$answer表示时候显示答案
	public function getExam($homeworkid,$studentid,$classid,$examsid,$issubmit,$answer){
				S($examsid.$unitid.'examsdata',null);
        $examsdata = S($examsid.$unitid.'examsdata');   //获取缓存内容
        if(empty($examsdata)){
           // echo "没有缓存";
            $sqlquery = M();
            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id in ('.$examsid.')';
            $examsdata = $sqlquery -> query($sql);
            $exams_tts_type = $examsdata[0]['tts_type'];
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes,"'.$examsid.'" as homeworkid from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            //var_dump($stemdata);exit;
            foreach ($stemdata as $key => $value) {
                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$value['id'].' and tts_type="st" and isdel = 1 order by sortid,id';     //题干的听力材料和mp3
                $stem_tts = $sqlquery -> query($sql);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];

                if($stem_type == '1'){          //独立大题
                        $score = 0;
                        $sql='select "'.$answer.'" as answer,"'.$issubmit.'" as issubmit,"'.$examsid.'" as examsid,t.* from engs_exams_questions t where t.stemid ='.$value['id'].' and t.isdel=1';
												$question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) {

                            $question[$quekey]["question_score"]=$value['question_score'];
							//主要是处理填空试题的正则
							$question[$quekey]["tcontent"]=preg_replace("/\#\#答案(.*?)\#\#/",'<span>________</span>',$question[$quekey]["tcontent"]);
                           //问题选项
                            $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                            $que_items = $sqlquery->query($sql);
                            $question[$quekey]['items'] = $que_items;
                            //问题答案
                             $sql = 'select engs_exams_questions_answer.answer as quesans,answer_num,engs_homework_student_quiz.answer as userans,(select flag from engs_exams_questions_items where engs_exams_questions_items.content=engs_exams_questions_answer.answer and questionsid='.$quevalue['id'].' and isdel=1) as quesflag,engs_homework_student_quiz.iscorrect from engs_exams_questions_answer left join engs_homework_student_quiz on engs_exams_questions_answer.id=engs_homework_student_quiz.answerid and engs_homework_student_quiz.homeworkid='.$homeworkid.' and engs_homework_student_quiz.studentid= "'.$studentid.'" where engs_exams_questions_answer.questionsid = ' . $quevalue['id'].' and engs_exams_questions_answer.isdel=1 order by sortid,engs_exams_questions_answer.id';     //独立大题问题的答案
                            //echo $sql;exit;
                            $queanswer = $sqlquery -> query($sql);
                            $score += $value['question_score'] * count($queanswer);
                            if($type==1){
                                $queanswer=0;
                            }

                            $question[$quekey]['que_answer'] = $queanswer;
                            //问题的听力材料
                            if($exams_tts_type == '1'){             //系统生成的听力材料
                                 $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';
                            }
                            else{
                                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';
                            }
                            $que_tts = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            $stemdata[$key]['questypeid'] = $quevalue['typeid'];

                        }
                        if($stemdata[$key]['question_score'] == ceil($stemdata[$key]['question_score'])){
                            $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        }
                        $stemdata[$key]['question'] = $question;
                       // $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;

                        $quekey =0;

                }
                else if($stem_type ==2){
                    $quekey =0;                   //组合大题
                    unset($question);
                    unset($score);
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $score2 = 0;
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        //答题下面的问题开始
                        $sql='select "'.$answer.'" as answer,"'.$issubmit.'" as issubmit,"'.$examsid.'" as examsid,t.* from engs_exams_questions t where t.stemid ='.$childvalue['id'].' and t.isdel=1';
                       // echo $sql;
                        $question = $sqlquery -> query($sql);

                        $score=0.0;
                        foreach ($question as $quekey => $quevalue) {

                            $question[$quekey]["question_score"]=$value['question_score'];
                            //主要是处理填空试题的正则
                            $question[$quekey]["tcontent"]=preg_replace("/\#\#答案(.*?)\#\#/",'<span>________</span>',$question[$quekey]["tcontent"]);
                           //问题选项

                            $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';

                            $que_items = $sqlquery->query($sql);
                            $question[$quekey]['items'] = $que_items;
                            //问题答案
                             $sql = 'select engs_exams_questions_answer.answer as quesans,answer_num,engs_homework_student_quiz.answer as userans,(select flag from engs_exams_questions_items where engs_exams_questions_items.content=engs_exams_questions_answer.answer and questionsid='.$quevalue['id'].' and isdel=1) as quesflag,engs_homework_student_quiz.iscorrect from engs_exams_questions_answer left join engs_homework_student_quiz on engs_exams_questions_answer.id=engs_homework_student_quiz.answerid and engs_homework_student_quiz.homeworkid='.$homeworkid.' and engs_homework_student_quiz.studentid= "'.$studentid.'" where engs_exams_questions_answer.questionsid = ' . $quevalue['id'].' and engs_exams_questions_answer.isdel=1 order by sortid,engs_exams_questions_answer.id';     //独立大题问题的答案
                            //echo $sql;exit;
                            $queanswer = $sqlquery -> query($sql);
                            $score += $childvalue['question_score'] * count($queanswer);
                            if($type==1){
                                $queanswer=0;
                            }

                            $question[$quekey]['que_answer'] = $queanswer;
                            //问题的听力材料
                            if($exams_tts_type == '1'){             //系统生成的听力材料
                                 $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';
                            }
                            else{
                                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';
                            }
                            $que_tts = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            $stem_children[$keychild]['questypeid'] = $quevalue['typeid'];
                        }
                        //答题下面的问题结束
                        $stem_children[$keychild]['question'] = $question;
                        if($stem_children[$keychild]['question_score'] == ceil($stem_children[$keychild]['question_score'])){
                            $stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        }
                        $stem_children[$keychild]['total_score']= $score;
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;
                        $stemdata[$key]['question_score'] = $stem_children[0]['question_score'];

                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                }

             }

            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$examsid.' and (tts_type ="eb" && tts_type="ed") and isdel=1 order by sortid,id';   //试卷头尾的听力材料
            $examstts = $sqlquery -> query($sql);
            foreach ($examsdata as $examskey => $examsvalue) {
                $examsdata[$examskey]['stem'] = $stemdata;
                $examsdata[$examskey]['total_score'] = $total_score;
                if($examsdata[$examskey]['header_content'] == null){
                    $examsdata[$examskey]['header_content'] = '';
                }
                $examsdata[$examskey]['exams_tts'] = count($examstts) == 0 ? '0' : $examstts;
            }
            S($examsid.$unitid.'examsdata',$examsdata);

        }
       //$this->ajaxReturn($examsdata);
        return $examsdata;
	}

	//获取作业接口
	public function getHomeworkList($ks_code){
		$homework=M("homework");
		$rs=$homework->where("ks_code='%s'",$ks_code)->select();
		return $rs;
	}


	public function getStuhwInfo(){
         $homeworkid=I("paper_id");
		 $classid=I("classId");
		 $jsoncallback=I("jsoncallback");
		 //查询试卷中有的类型
		 $typeList=array();
		 $wtsql="select * from engs_homework_word_evaluat t where t.homeworkid=".$homeworkid;
		 $wtrs=M()->query($wtsql);
		 $eqsql="select * from engs_homework_quiz t where t.homeworkid=".$homeworkid;
		 $eqrs=M()->query($eqsql);

		 //查询所有的学生信息
		 $sql=" select studentid from engs_homework_student_word_evaluat where engs_homework_student_word_evaluat.homeworkid=".$homeworkid." and engs_homework_student_word_evaluat.classid='".$classid."' ";
		 $sql=$sql." union select studentid from engs_homework_student_quiz where engs_homework_student_quiz.homeworkid=".$homeworkid." and engs_homework_student_quiz.classid='".$classid."'";
		 $rs=M()->query($sql);
		 if(!empty($wtrs)&&!empty($rs)){
			 array_push($typeList,"Wordevaluat");
		 }
		 if(!empty($eqrs)&&!empty($rs)){
			 array_push($typeList,"examsquiz");
		 }

		 $sturesult=array();
		 foreach($rs as $key=>$value){
       //查询单词跟读得分
			 $stu=array();
			 $stu["studentId"]=$value["studentid"];
			 $scoreList=array();
			 if(!empty($wtrs)){
				 $temp=array();
				 $temp["type"]="Wordevaluat";
				 $sql="select sum(score) as score from engs_homework_student_word_evaluat t where t.studentid='".$value["studentid"]."' and homeworkid=".$homeworkid." and classid='".$classid."'";
				 $wttrs=M()->query($sql);
				 if(empty($wttrs)){
					 $scoreDccs=0;
					 $temp["score"]=$scoreDccs;
				 }else{
					 $scoreDccs=$wttrs[0]["score"];
					 if(empty($scoreDccs)){
						 $scoreDccs=0;
					 }
					 $temp["score"]=$scoreDccs;
				 }
				 array_push($scoreList,$temp);
			 }
			 if(!empty($eqrs)){
				 unset($temp);
				 $temp=array();
				 $temp["type"]="examsquiz";
				 $sql="select sum(score) as score from engs_homework_student_quiz t where t.studentid='".$value["studentid"]."' and homeworkid=".$homeworkid." and classid='".$classid."'";
				 $eqsrs=M()->query($sql);
				 if(empty($eqsrs[0]["score"])){
					 $scoreTlcs=0;
					 $temp["score"]=$scoreTlcs;
				 }else{
					 $scoreTlcs=$eqsrs[0]["score"];
					 if(empty($scoreTlcs)){
						 $scoreTlcs=0;
					 }
					 $temp["score"]=$scoreTlcs;
				 }
				 array_push($scoreList,$temp);
		  }
			$stu["scoreList"]=$scoreList;
			array_push($sturesult,$stu);
		 }
		 $ret["flag"]=0;
		 $ret["errormsg"]="";
		 $ret["typeList"]=$typeList;
		 $ret["studentList"]=$sturesult;
		 if(empty($jsoncallback)){
 			echo json_encode($ret);
 		 }else{
 			header('Content-Type:application/json; charset=utf-8');
 			echo $jsoncallback.'('.json_encode($ret).');';
 		 }
	}


	//获取错题统计反馈
	public function getStuhwErrorFeedback(){
		$homeworkid=I("paper_id");
		$classid=I("classId");
		$num=I("num");
		$jsoncallback=I("jsoncallback");
		//分四部分然后将四部分合起来按照错误率进行排序
		//1单词朗读
		$wordAlound=array();
		// $wasql="SELECT concat('http://en.youjiaotong.com/Homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=0&classid=".$classid."&homeworkid=".$homeworkid."') as url,t.`homeworkid`,t.`id`,";
		// $wasql=$wasql."t.`wordid`,(SELECT ew.word FROM engs_word ew WHERE ew.id = t.wordid) AS name,(CASE   WHEN AVG(score) IS NULL  THEN 0";
		// $wasql=$wasql." ELSE AVG(score) END ) AS average,CASE WHEN MAX(score) IS NULL THEN 0 ELSE MAX(score) END AS maxscore,CASE WHEN MIN(score) IS NULL THEN 0 ELSE MIN(score) END AS minscore,";
		// $wasql=$wasql."sum(CASE WHEN s.score IS  NULL THEN 0 ELSE 1 END) AS num";
		// $wasql=$wasql." FROM engs_homework_word_read t LEFT JOIN (SELECT * FROM engs_homework_student_word_read WHERE homeworkid=".$homeworkid." AND classid='".$classid."' ) s ON t.`id`=s.`word_readid` WHERE";
		// $wasql=$wasql." t.`homeworkid` = ".$homeworkid." GROUP BY t.`wordid`";
		// $wordAlound=M()->query($wasql);

		//按照平均分由高到低进行排序
		if(empty($wordAlound)){
			$wordAloundrs=[];
		}else{
			$wordAloundrs=$this->quickSort($wordAlound,"average");
			$wordAloundrs=array_slice($wordAloundrs,0,$num);
		}


        $sql="select concat('http://".C("domain")."/Homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=1&classid=".$classid."&homeworkid=".$homeworkid."') as url,s.*,t.typeid,(SELECT word FROM engs_word ew WHERE ew.id=t.wordid) as name,round(s.accnum/s.num) as accrate";
            $sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN (select wordid,word_evaluatid,count(*) as num,sum(case when iscorrect=1 then 1 else 0 end) as accnum from  engs_homework_student_word_evaluat  where homeworkid=".$homeworkid." and classid='".$classid."' AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = ".$homeworkid."
      AND classid = '".$classid."') group by wordid,word_evaluatid) s  ";
            $sql=$sql."ON t.`id` = s.`word_evaluatid` WHERE t.homeworkid = ".$homeworkid." AND s.wordid IS NOT NULL HAVING (num - accnum) >0 ORDER BY accrate";

        //echo $sql;exit;
		$wordlist=M()->query($sql);
        $wordTestrslist=[];
        $wordYHrslist=[];
        $wordXCrslist=[];
        $wordTestrs=[];
        $wordYHrs=[];
        $wordXCrs=[];
		foreach($wordlist as $key=>$value){
			$wordlist[$key]["errorrate"]=(100-round($value["accnum"]*100/$value["num"]))."%";
            $wordlist[$key]["errornum"]=$value["num"]-$value["accnum"];
            if($wordlist[$key]["typeid"] == 2){
               array_push($wordTestrslist, $wordlist[$key]);//拼写
            }
            else if($wordlist[$key]["typeid"] == 0){
                array_push($wordXCrslist, $wordlist[$key]);//选词
            }
            else{
                array_push($wordYHrslist, $wordlist[$key]);//选词
            }

		}
		if(empty($wordTestrslist)){
            $wordTestrs=[];
			
		}else{
			$wordTestrslist=$this->quickSort($wordTestrslist,"errorrate");
			$wordTestrs=array_slice($wordTestrslist,0,$num);
		}
        if(empty($wordXCrslist)){
            $wordXCrs=[];
            
        }else{
            $wordXCrslist=$this->quickSort($wordXCrslist,"errorrate");
            $wordXCrs=array_slice($wordXCrslist,0,$num);
        }
        if(empty($wordYHrslist)){
            $wordYHrs=[];
            
        }else{
            $wordYHrslist=$this->quickSort($wordYHrslist,"errorrate");
            $wordYHrs=array_slice($wordYHrslist,0,$num);
        }
		//3课文朗读
		$textAlound=array();
		if(empty($textAlound)){
			$textAloundrs=[];
		}else{
			$textAloundrs=$this->quickSort($textAlound,"average");
			$textAloundrs=array_slice($textAloundrs,0,$num);
		}

		//4听力训练采用先判断错误率的方式
		//查新作业数据
		$examsid=M("homework_quiz")->where("homeworkid=%d",$homeworkid)->field("group_concat(examsid) as exams")->group("homeworkid")->find();
		if(empty($examsid)){
            $examsQuizrs=[];
		}else{
			$examsQuiz=$this->getFeedBackExam($examsid["exams"],$homeworkid,$classid);
			$examsQuizrs=$this->quickSort($examsQuiz,"errorrate");
			$errornum=0;
			$examsarr=array();
			foreach($examsQuizrs as $key=>$value){
				if($value["errorrate"]==0){
				}else{
					array_push($examsarr,$value);
					if(count($examsarr)==$num){
						break;
					}
				}
			}
			foreach($examsarr as $ek=>$ev){
				$examsarr[$ek]["errorrate"]=$examsarr[$ek]["errorrate"]."%";
			}
			$examsQuizrs=$examsarr;
		}
		$data["Wordread"]=$wordAloundrs;
		$data["Wordevaluat"]=$wordTestrs;
        $data["Wordhy"]=$wordYHrs;
        $data["Wordxc"]=$wordXCrs;
		$data["Textread"]=$textAloundrs;
		$data["examsquiz"]=$examsQuizrs;
		if(empty($jsoncallback)){
			echo json_encode($data);
		}else{
			header('Content-Type:application/json; charset=utf-8');
			echo $jsoncallback.'('.json_encode($data).');';
		}
	}

    //获取课文的情况
    public function getTexts(){
        $ids=I("ids");
        if(!empty($ids)){
            $count=0;
            $sql="select * from engs_text_chapter t where isdel=1 and id in (".$ids.")";
            $rs=M()->query($sql);
            foreach($rs as $key=>$value){
                $textrs=M("text")->where("chapterid=%d and isdel=1",$value["id"])->field("*,'0' as issubmit,'1' as answer")->select();
                $rs[$key]["text"]=$textrs;
                $rs[$key]["score"]=count($textrs);
                $count=$count+count($textrs);
            }
            $ret["title"]="课文朗读";
            $ret["score"]=$count;
            $ret["chapters"]=$rs;
            $ret["issubmit"]=0;
            $ret["answer"]=1;
            $this->ajaxReturn($ret);
        }
    }


		//获取学生课文的作业情况
		private function getText($homeworkid,$studentid,$classid,$answer,$issubmit){
			$sql="select * from engs_homework_text_read t left join engs_text_chapter s on t.chapterid=s.id where t.homeworkid= ".$homeworkid;
			$rs=M()->query($sql);
			$count=0;
			foreach($rs as $key=>$value){
					$sql="select '".$issubmit."' as issubmit,'".$answer."' as answer,t.id,t.encontent,t.mp3,";
					$sql=$sql."(select max(s.score) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as userscore,";
					$sql=$sql."(select max(s.mp3) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as usermp3,";
					$sql=$sql."(select count(distinct s.studentid) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as num,";
					$sql=$sql."(select AVG(case when s.score is null then 0 else s.score end) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as average";
					$sql=$sql." from engs_text t where t.chapterid=".$value["id"]." and isdel=1";
					$textrs=M()->query($sql);
					$rs[$key]["text"]=$textrs;
					$rs[$key]["score"]=count($textrs);
					$count=$count+count($textrs);
			}
			$ret["title"]="课文朗读";
			$ret["score"]=$count;
			$ret["chapters"]=$rs;
            $ret["answer"]=$answer;
            $ret["issubmit"]=$issubmit;
			return $ret;
		}

    //获取学生课文的作业情况
    public function getStuTexts(){
        $homeworkid=I("homeworkid");
				$studentid=session("studentid");
				$classid=session("classid");
        $ret=$this->getText($homeworkid,$studentid,$classid,0,0);
        $this->ajaxReturn($ret);
    }


    //获取学生课文朗读作业结果
    public function getStuResultTexts(){
        $homeworkid=I("homeworkid");
        $studentid=I("username");
        $classid=I("classid");
				$ret=$this->getText($homeworkid,$studentid,$classid,1,1);
        $this->ajaxReturn($ret);
    }


		//将函数进行合并
		private function getWord($ids,$issubmit,$answer,$studentid,$classid){
				if($issubmit==0&&$answer==1){
					$sql="SELECT *,'".$issubmit."' as issubmit,'".$answer."' as answer FROM engs_word t ,engs_base_word s,engs_base_word_explains p WHERE t.`base_wordid`=s.`id` AND t.`base_explainsid`=p.`id` AND t.`base_wordid`=p.`base_wordid` AND t.id in (".$ids.")";
				}else{
					$sql="SELECT '".$issubmit."' as issubmit,t.`homeworkid`,t.`wordid`,s.`word`,e.`ukmark`,e.`ukmp3`,p.`explains`,p.`pic`,";
					$sql=$sql."'".$answer."' AS answer,";
					if($answer==1){
						$sql=$sql."(SELECT h.mp3 FROM engs_homework_student_word_read h WHERE h.homeworkid=t.`homeworkid` AND h.word_readid=t.id and studentid='".$studentid."') AS usermp3,";
					}
					$sql=$sql."(SELECT h.score FROM engs_homework_student_word_read h WHERE h.homeworkid=t.`homeworkid` AND h.word_readid=t.id and studentid='".$studentid."') AS score,";
					$sql=$sql."(SELECT SUM(h.score)/COUNT(*) FROM engs_homework_student_word_read h WHERE h.homeworkid=t.`homeworkid` AND h.word_readid=t.id and classid='".$classid."' GROUP BY h.homeworkid AND h.word_readid) AS average";
					$sql=$sql." FROM engs_homework_word_read t ,engs_word s ,engs_base_word e,engs_base_word_explains p ";
					$sql=$sql." WHERE t.`wordid`=s.`id` AND s.`base_wordid`=e.`id` AND s.`base_explainsid`=p.`id` AND p.`base_wordid`=e.`id` AND t.`homeworkid`=".$ids;
				}
				$rs=M()->query($sql);
				$ret["title"]="单词朗读";
				$ret["words"]=$rs;
				$ret["score"]=count($rs);
                $ret["issubmit"]=$issubmit;
                $ret["answer"]=$answer;
				return $ret;
		}

    //获取单词的情况
    public function getWords(){
        $ids=I("ids");
        if(!empty($ids)){
            //获取单词的信息
						$ret=$this->getWord($ids,0,1,"","");
            $this->ajaxReturn($ret);
        }
    }

    //获取学生作业情况
    public function getStuWords(){
        $homeworkid=I("homeworkid");
				$classid=session("classId");
				$studentid=session("studentid");
				$ret=$this->getWord($homeworkid,0,0,$studentid,$classid);
        $this->ajaxReturn($ret);
    }


    //获取学生单词听读作业情况
    public function getStuResultWords(){
        $homeworkid=I("homeworkid");
        $studentid=I("username");
        $classid=I("classid");
        $ret=$this->getWord($homeworkid,1,1,$studentid,$classid);
        $this->ajaxReturn($ret);
    }

		//此方法主要是进行单词测试的展示函数
		private function getStuWordOption($homeworkid,$classid,$studentid,$answer,$issubmit){
			$sql="SELECT distinct t.wordid,s.word FROM engs_homework_word_evaluat t left join engs_word s on t.wordid=s.id WHERE  t.`homeworkid`=".$homeworkid." order by t.sortid desc";
			$rss=M()->query($sql);
			$result=array();
			$wtresult=array();
			$wordtestcounts=0;
			foreach($rss as $key=>$value){
				  //基本信息
					$sql="SELECT s.word,t.id,t.option_a,t.option_b,t.option_c,p.explains,e.ukmp3,t.answer as queanswer,max(CASE WHEN st.studentid = '".$studentid."' THEN st.iscorrect ELSE '' END) as iscorrect,t.typeid,";
					//班级回答人数
					$sql=$sql."sum(case when st.classid='".$classid."' then 1 else 0 end) AS num,";
					//显示分数
					$sql=$sql."SUM(case when st.classid='".$classid."'  then st.`iscorrect` else 0 end) AS accnum,";
					//用户分数
					$sql=$sql."SUM(CASE WHEN st.studentid='".$studentid."' THEN st.`score` ELSE 0 END) AS userscore,";
					//问题分数
					$sql=$sql."'1' as quescore,";
					//是否提交
					$sql=$sql."'".$issubmit."' as issubmit,";
					//是狗显示答案
					$sql=$sql."'".$answer."' as answer,";
					//用户答案
					$sql=$sql."max(CASE WHEN st.studentid='".$studentid."' THEN st.`answer` ELSE '' END) AS useranswer ";
					//条件
					$sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN engs_homework_student_word_evaluat st ON st.`word_evaluatid`=t.`id`,";
					$sql=$sql."engs_word s,engs_base_word e,engs_base_word_explains p ";
					$sql=$sql."  WHERE t.`wordid`=s.`id` AND s.`base_wordid`=e.`id` AND s.`base_explainsid`=p.`id` AND p.`base_wordid`=e.`id` ";
					$sql=$sql." AND t.`homeworkid`=".$homeworkid." and t.wordid=".$value["wordid"]."  GROUP BY t.id,t.sortid,t.option_a,t.option_b,t.option_c,p.explains,e.ukmp3 order by t.sortid desc";
                   //echo $sql."===";
					//查询
					$chrs=M()->query($sql);
					//第一道试题的参数
					$data["wordid"]=$value["wordid"];
					$data["explains"]=$chrs[0]["explains"];
					$data["mp3"]=$chrs[0]["ukmp3"];
					$data["score"]=1;
                    if($chrs[0]["typeid"]==2){
                        $optionarr = explode(',', $chrs[0]["option_a"]);
                        $optionstr = '';
                        foreach ($optionarr as $keyoptionarr => $valueoptionarr) {

                            $optionstr=$optionstr.'<a class="btn_gray wt_px" href="javascript:void(0);" onclick="px(this);" >'.$valueoptionarr.'</a>';
                            # code...
                        }
                        $chrs[0]["option_a"]= $optionstr;
                    }
					$data["option_a"]=$chrs[0]["option_a"];
					$data["typeid"]=$chrs[0]["typeid"];
					$data["option_b"]=$chrs[0]["option_b"];
					$data["option_c"]=$chrs[0]["option_c"];
					$data["answer"]=$chrs[0]["answer"];
                    $data["word"]=$chrs[0]["word"];
                    $data["explains"]=$chrs[0]["explains"];
                    if($chrs[0]["typeid"]=='2'){
                        $data["word"]=str_split($chrs[0]["queanswer"]);
                    }
					$data["quesid"]=$chrs[0]["id"];
					$data["issubmit"]=$chrs[0]["issubmit"];
					$data["iscorrect"]=$chrs[0]["iscorrect"];
					$data["num"]=$chrs[0]["num"];
					$data["queanswer"]=$chrs[0]["queanswer"];
					$data["useranswer"]=$chrs[0]["useranswer"];
                    $uanswerarr = explode(",", $chrs[0]["useranswer"]);
                    $useranswer2 ="";
                    foreach ($uanswerarr as $ukey => $uvalue) {
                        $useranswer2.= $uvalue;
                    }
                    $data["useranswer2"] = $useranswer2;
					//辨识率
					if($chrs[0]["num"]==0||empty($chrs[0]["num"])){
						$data["bsrate"]="0%";
					}else{
						$data["bsrate"]=round($chrs[0]["bsscore"]*100/$chrs[0]["num"])."%";
					}
					//拼写率
					if($chrs[0]["num"]==0||empty($chrs[0]["num"])){
						$data["pxrate"]="0%";
					}else{
						$data["pxrate"]=round($chrs[0]["pxscore"]*100/$chrs[0]["num"])."%";
					}
                    //错误率
                    if($chrs[0]["num"]==0||empty($chrs[0]["num"])){
                        $data["errrate"]="0%";
                    }else{
                        $data["errrate"]=(100-round($chrs[0]["accnum"]*100/$chrs[0]["num"]))."%";
                    }
                    //正确率
                    if($chrs[0]["num"]==0||empty($chrs[0]["num"])){
                        $data["accrate"]="0%";
                    }else{
                        $data["accrate"]=round($chrs[0]["accnum"]*100/$chrs[0]["num"])."%";
                    }
					$wordtestcounts=$wordtestcounts+1;
					array_push($wtresult,$data);
					//$rss[$key]["optiona"]=$data;
					unset($choice);unset($data);

					// $choice[0]=$chrs[1]["option_a"];
					// $choice[1]=$chrs[1]["option_b"];
					// $choice[2]=$chrs[1]["option_c"];
					// $data["typeid"]=$chrs[1]["typeid"];
					// $data["wordid"]=$value["wordid"];
					// $data["word"]=$value["word"];
					// $data["mp3"]=$chrs[1]["ukmp3"];
					// $data["score"]=1;
					// $data["option_a"]=$chrs[1]["option_a"];
					// $data["iscorrect"]=$chrs[1]["iscorrect"];
					// $data["option_b"]=$chrs[1]["option_b"];
					// $data["option_c"]=$chrs[1]["option_c"];
					// $data["answer"]=$chrs[1]["answer"];
					// $data["quesid"]=$chrs[1]["id"];
					// $data["issubmit"]=$chrs[1]["issubmit"];
					// $data["num"]=$chrs[1]["num"];
					// $data["queanswer"]=$chrs[1]["queanswer"];
					// $data["useranswer"]=$chrs[1]["useranswer"];
					// //辨识率
					// if($chrs[1]["num"]==0||empty($chrs[1]["num"])){
					// 	$data["bsrate"]="0%";
					// }else{
					// 	$data["bsrate"]=round($chrs[1]["bsscore"]*100/$chrs[1]["num"])."%";
					// }
					// //拼写率
					// if($chrs[1]["num"]==0||empty($chrs[1]["num"])){
					// 	$data["pxrate"]="0%";
					// }else{
					// 	$data["pxrate"]=round($chrs[1]["pxscore"]*100/$chrs[1]["num"])."%";
					// }
					// array_push($wtresult,$data);
					// $rss[$key]["optionb"]=$data;
					// $wordtestcounts=$wordtestcounts+1;
					// if(count($chrs)>2){
					// 		unset($choice);unset($data);
					// 		$choice[0]=$chrs[2]["option_a"];
					// 		$choice[1]=$chrs[2]["option_b"];
					// 		$choice[2]=$chrs[2]["option_c"];
					// 		$data["typeid"]=$chrs[2]["typeid"];
					// 		$data["wordid"]=$value["wordid"];
					// 		//$data["word"]=str_split($value["word"]);
					// 		$qian=array(" ","　","\t","\n","\r");
					// 		$hou=array("","","","","");
					// 		$word=str_replace($qian,$hou,$value["word"]);
					// 		//过滤掉非字符
					// 		$words=array();
					// 		$wordarr=str_split($word);
					// 		foreach($wordarr as $kw=>$vw){
					// 				if(preg_match("/^[a-zA-Z\s]+$/",$vw)){
					// 					 array_push($words, $vw);
					// 				}
					// 		}
					// 		$data["word"]=$words;
					// 		$data["mp3"]=$chrs[2]["ukmp3"];
					// 		$data["score"]=1;
					// 		$data["option_a"]=$chrs[2]["option_a"];
					// 		$data["option_b"]=$chrs[2]["option_b"];
					// 		$data["option_c"]=$chrs[2]["option_c"];
					// 		$data["answer"]=$chrs[2]["answer"];
					// 		$data["quesid"]=$chrs[2]["id"];
					// 		$data["iscorrect"]=$chrs[2]["iscorrect"];
					// 		$data["issubmit"]=$chrs[2]["issubmit"];
					// 		$data["num"]=$chrs[2]["num"];
					// 		$data["queanswer"]=$chrs[2]["queanswer"];
					// 		$ua=array();
					// 		$uansdata=explode(",",$chrs[2]["useranswer"]);
					// 		foreach($uansdata as $uk=>$uv){
					// 			if($uk>0){
					// 				array_push($ua,$uv);
					// 			}
					// 		}
					// 		$data["useranswer"]=$ua;
					// 		//辨识率
					// 		if($chrs[2]["num"]==0||empty($chrs[2]["num"])){
					// 			$data["bsrate"]="0%";
					// 		}else{
					// 			$data["bsrate"]=round($chrs[2]["bsscore"]*100/$chrs[2]["num"])."%";
					// 		}
					// 		//拼写率
					// 		if($chrs[2]["num"]==0||empty($chrs[2]["num"])){
					// 			$data["pxrate"]="0%";
					// 		}else{
					// 			$data["pxrate"]=round($chrs[2]["pxscore"]*100/$chrs[2]["num"])."%";
					// 		}
					// 		array_push($wtresult,$data);
					// 		$rss[$key]["optionc"]=$data;
					// 		$wordtestcounts=$wordtestcounts+1;
					// }
			}
			$ret["title"]="单词测试";
			$ret["words"]=($wtresult);
			$ret["score"]=$wordtestcounts;
			return $ret;
		}

    //获取学生的单词测试的内容
    public function getStuWordOptions(){
        $homeworkid=I("homeworkid");
				$studentid=session("username");
				$classid=session("classid");
				$ret=$this->getStuWordOption($homeworkid,$classid,$studentid,0,0);
              //  var_dump($ret);
    $this->ajaxReturn($ret);
    }


    //获取学生单词测试的结果内容
    public function getStuWordResultOptions(){
        $homeworkid=I("homeworkid");
        $studentid=I("username");
        $classid=I("classid");
        $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,1,1);
        $this->ajaxReturn($ret);
    }


    //单词预览
    public function getWordOptions(){
        $ids=I("ids");
        //获取单词的信息
        //set_time_limit(0);
        if(!empty($ids)){
            $sql="SELECT s.ukmp3,t.id,t.word,p.explains,p.pic,s.ukmark,t.isstress,s.isword FROM engs_word t ,engs_base_word s,engs_base_word_explains p WHERE t.`base_wordid`=s.`id` AND t.`base_explainsid`=p.`id` AND t.`base_wordid`=p.`base_wordid` AND t.id in (".$ids.")";
            //直接生成的数据
            $rss=M()->query($sql);
            $result=array();
            $answer=['A','B','C'];
            $count=0;
			$wtresult=array();
            foreach($rss as $key=>$value){
                if($value["isstress"]=='1'&& $value["isword"]=='1'){
                    unset($data);
                    //拆分字符串
                    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
                    $word=str_replace($qian,$hou,$value["word"]);
                    //过滤掉非字符
                    $words=array();
                    $wordarr=str_split($word);
                    foreach($wordarr as $kw=>$vw){
                        if(preg_match("/^[a-zA-Z\s]+$/",$vw)){
                           array_push($words, $vw);
                        }
                    }
                    $wordarr=$words;
                    $options=array();
                    foreach($wordarr as $kh=>$vh){
                      array_push($options, "<a href='#' class='btn_green'>".$vh."</a>");
                    }
                    $len=count($wordarr);
                    if(count($wordarr)<10){
                        //生成10个单词
                        for($i=0;$i<10-$len;$i++){
                            $ran=mt_rand(0,25);
                            if(empty(chr(65+$ran))){
                                $i=$i-1;
                                continue;
                            }
                            //echo 65+$ran."||".chr(65+$ran),"</br>";
                            $temp=chr(65+$ran);
                            array_push($wordarr, $temp);
                            array_push($options, "<a href='#' class='btn_gray'>".$temp."</a>");
                        }
                    }else if(count($wordarr)>=10 and count($wordarr)<15){
                        //生成15个单词
                        for($i=0;$i<15-$len;$i++){
                            $ran=mt_rand(0,25);
                            if(empty(chr(65+$ran))){
                                $i=$i-1;
                                continue;
                            }
                            $temp=chr(65+$ran);
                            array_push($wordarr, $temp);
                            array_push($options, "<a href='#' class='btn_gray'>".$temp."</a>");
                        }

                    }else{
                        //生成20个单词
                        for($i=0;$i<20-$len;$i++){
                            $ran=mt_rand(0,25);
                            if(empty(chr(65+$ran))){
                                $i=$i-1;
                                continue;
                            }
                            $temp=chr(65+$ran);
                            array_push($wordarr, $temp);
                            array_push($options, "<a href='#' class='btn_gray'>".$temp."</a>");
                        }
                    }
                    shuffle($wordarr);
                    shuffle($options);
                    //var_dump($wordarr);exit;
                    //将选项直接存入数据库
                    $roptions="";
                    foreach($options as $k=>$v){
                        $roptions=$roptions.$v;
                    }
                   // echo $options;exit;
                    $data["wordid"]=$value["id"];
                    $data["word"]=$words;
                    $data["mp3"]=$value["ukmp3"];
                    $data["score"]=1;
                    $data["option_a"]=$roptions;
                    $data["option_b"]="";
                    $data["option_c"]="";
                    $data["answer"]=$words;
                    $data["typeid"]=2;
                    $data["issubmit"]=0;
                    $data["explains"]=$value["explains"];
                    $data["trueanswer"]=$value["word"];
                                        array_push($wtresult,$data);
                    $rss[$key]["optionc"]=$data;
                    $count=$count+1;
                }else{
                    $ran=mt_rand(0,100)%2;
                    if($ran==0){
                        unset($choice);unset($data);unset($rs);
                        $rs=get_choice_list($value["id"]);
                        //var_dump($rs);exit;
                        $choice[0]=$rs[0]["word"];
                        $choice[1]=$rs[1]["word"];
                        $choice[2]=$value["word"];
                        shuffle($choice);
                        $founded = array_search($value["word"], $choice);
                        $data["wordid"]=$value["id"];
                        $data["word"]=$value["word"];
                        $data["explains"]=$value["explains"];
                        $data["mp3"]=$value["ukmp3"];
                        $data["score"]=1;
                        $data["option_a"]=$choice[0];
                        $data["option_b"]=$choice[1];
                        $data["option_c"]=$choice[2];
                        $data["answer"]=$answer[$founded];
                        $data["typeid"]=0;
                        $data["issubmit"]=0;
                                        array_push($wtresult,$data);
                        $rss[$key]["optiona"]=$data;
                        $count=$count+1;

                    }else{
                        unset($choice);unset($data);unset($rs);
                        $rs=get_choice_list($value["id"]);
                        $data["wordid"]=$value["id"];
                        $data["word"]=$value["word"];
                        $data["mp3"]=$value["ukmp3"];
                        $data["score"]=1;
                        $choice[0]=$rs[0]["explains"];
                        $choice[1]=$rs[1]["explains"];
                        $choice[2]=$value["explains"];
                        shuffle($choice);
                        $founded = array_search($value["explains"], $choice);
                        $data["option_a"]=$choice[0];
                        $data["option_b"]=$choice[1];
                        $data["option_c"]=$choice[2];
                        $data["answer"]=$answer[$founded];
                        $data["typeid"]=1;
                        $data["issubmit"]=0;
                        array_push($wtresult,$data);
                        $rss[$key]["optionb"]=$data;
                        $count=$count+1;
                    }
                }
            }
            $ret["title"]="单词测试";
            $ret["words"]=$wtresult;
            $ret["score"]=$count;
            $this->ajaxReturn($ret);
        }
    }

    //每道试题把id以及用户的答案以及input的name提交给服务器进行评分
    /**
    *post提交的时候每个对象需要三个属性id,name,useranswer,type,score其中type 0,1,2,3
    *return json:id,answer,name,useranswer,decide
    **/
    public function giveMark(){
	    $issubmit=I("issubmit",1);
        $homeworkid=I("homeworkid");
        $wadata=I("wadata");
        $wtdata=I("wtdata");
        $tadata=I("tadata");
        $eqxzdata=I("eqxzarr");
        $eqpddata=I("eqpdarr");
        $eqtkdata=I("eqtkarr");
        $eqpxdata=I("eqpxarr");
        $wadata=urldecode($wadata);
        $wadata=json_decode($wadata);
        $wtdata=urldecode($wtdata);
        $wtdata=json_decode($wtdata);
        $tadata=urldecode($tadata);
        $tadata=json_decode($tadata);
        $eqxzdata=urldecode($eqxzdata);
        $eqxzdata=json_decode($eqxzdata);
        $eqpddata=urldecode($eqpddata);
        $eqpddata=json_decode($eqpddata);
        $eqtkdata=urldecode($eqtkdata);
        $eqtkdata=json_decode($eqtkdata);
        $eqpxdata=urldecode($eqpxdata);
        $eqpxdata=json_decode($eqpxdata);
        $retwt=array();
        $retwa=array();
        $retta=array();
        $reteq=array();
        //var_dump($eqpxdata);exit;
        //单词测试的分数判断
		$score=0.0;
        $student_word_evaluat=M("homework_student_word_evaluat");
        $word_evaluat=M("homework_word_evaluat");
        foreach($wtdata as $obj){
					  //将强之前的答案进行答案删除
		  $student_word_evaluat->where("studentid='%s' and  classid='%s' and homeworkid='%s' and word_evaluatid='%s'",session("username"),session("classid"),$homeworkid,$obj->id)->delete();
            $student_word_evaluat->studentid=session("username");
            $student_word_evaluat->areaid=session("areaid");
            $student_word_evaluat->classid=session("classid");
            $student_word_evaluat->homeworkid=$homeworkid;
            $student_word_evaluat->word_evaluatid=$obj->id;
            $test["useranswer"]=$obj->useranswer;
            //进行正确与否的判断
            $rs=$word_evaluat->where("id=%d",$obj->id)->find();
			$student_word_evaluat->type=$rs["typeid"];
			$student_word_evaluat->wordid=$rs["wordid"];
			$test["answer"]=$rs["answer"];
			$answer="";
			//非填空试题的判断
			if($rs["typeid"]!='2'){
				$answer=$obj->useranswer;
				$student_word_evaluat->answer=$obj->useranswer;
				if(empty(trim($obj->useranswer))){
					$score=$score+0.0;
					$student_word_evaluat->score=0;
					$student_word_evaluat->iscorrect='';
				}else{
					if($rs["answer"]==$obj->useranswer){
						$score=$score+1.0;
						$student_word_evaluat->score=1;
						$student_word_evaluat->iscorrect=1;
						$test["iscorrect"]=1;
					}else{
						$score=$score+0.0;
						$student_word_evaluat->score=0;
						$student_word_evaluat->iscorrect=0;
						$test["iscorrect"]=0;
					}
				}
			}else{
				//进行单词拼写答案的比对
				$words=str_split($rs["answer"]);
				$userwords=str_split(trim($obj->useranswer));
				$student_word_evaluat->score=0;

				$student_word_evaluat->iscorrect=0;
				$iscorrect=0;
				$test["iscorrect"]=0;
				$wordrsarr=0;
				$wordrserr=0;
				foreach($words as $k=>$v){
					if(strtolower(trim($v))!=strtolower(trim($userwords[$k]))){
						$student_word_evaluat->score=0;
					  $test["iscorrect"]=0;
						if($userwords[$k]=='*'){
							 $wordrserr=$wordrserr+1;
                            // $answer=$answer." "."";
						}else{
							 //$answer=$answer." ".$userwords[$k];
						}
					}else{
						$wordrsarr=$wordrsarr+1;
						//$answer=$answer." ".$userwords[$k];
					}
				}
                foreach ($userwords as $keyu => $uv) {
                    if($keyu == 0){
                        $answer=$answer.$userwords[$keyu];
                    }
                    else{
                        $answer=$answer.",".$userwords[$keyu];
                    }
                }
				if($wordrsarr==count($words)){
					$score=$score+1.0;
					$student_word_evaluat->iscorrect=1;
					$student_word_evaluat->score=1;
				}else{
					$score=$score+0.0;
					if(count($userwords)==1){
						if($userwords[$k]=='*'){
							$student_word_evaluat->iscorrect='';
							$student_word_evaluat->score=0;
						}else{
							if($wordrserr==1){
								$student_word_evaluat->iscorrect=0;
								$student_word_evaluat->score=0;
							}
						}
					}else{
						if($wordrserr==count($userwords)){
							$student_word_evaluat->iscorrect='';
							$student_word_evaluat->score=0;
						}else{
							$student_word_evaluat->iscorrect=0;
							$student_word_evaluat->score=0;
						}
					}
				}
				$student_word_evaluat->answer=$answer;
			}
            $student_word_evaluat->do_time=Date("Y-m-d H:i:s");
            $student_word_evaluat->add();
            $test["obj"]=$obj->tag."[".$obj->attr."='".$obj->type."_".$obj->id."']";
            array_push($retwt, $test);
        }
        //听力试卷的填空试题分数判断
        $student_quiz=M("homework_student_quiz");
        $quiz=M("homework_quiz");
        foreach($eqtkdata as $obj){
		  $student_quiz->where("studentid='%s' and  classid='%s' and homeworkid='%s' and questionsid='%s' and examid='%s'",session("username"),session("classid"),$homeworkid,$obj->id,$obj->examsid)->delete();
            $rs=$quiz->where("homeworkid=%d and username='%s' and classid='%s' and examsid=%d and isdel=1",$homeworkid,$username)->find();

            //试题的分数的查询
            $sql="select * from engs_exams_stem t where t.isdel=1 and t.id in (select stemid from engs_exams_questions s where s.id=".$obj->id.")";
            $srs=M()->query($sql);
            $quescore=$srs[0]["question_score"];
            //用户的答案的判断
            $sql="select * from engs_exams_questions_answer t where isdel=1 and t.questionsid=".$obj->id." order by sortid";
            //echo $sql;exit;
            $queanrs=M()->query($sql);
            $useranswerarr=explode(",",$obj->useranswer);
            $useranswer=array();
            $answer=array();
            $error=array();
            $objstatus=array();
            $uanswer="";
						$userscore=0.0;
						$wordrsarr=0;
						$wordrserr=0;
            foreach($queanrs as $tkkey=>$tkvalue){
                $student_quiz->studentid=session("username");
                $student_quiz->areaid=session("areaid");
                $student_quiz->classid=session("classid");
                $student_quiz->homeworkid=$homeworkid;
                $student_quiz->questionsid=$obj->id;
                $student_quiz->examid=$obj->examsid;
                $student_quiz->quizid=$rs["id"];
                array_push($useranswer, $useranswerarr[$tkkey]);
                array_push($answer, $tkvalue["answer"]);
                $trueanswer_id = $tkvalue["id"];
                $status=$obj->tag."[".$obj->attr."='".$obj->type."_".$obj->id."']:eq(".$tkkey.")";
                array($objstatus,$status);
			    if($useranswerarr[$tkkey]==''){
                  //$wordrserr=$wordrserr+1;
					$score=$score+0.0;
					$userscore=$userscore+0.0;
                    $student_quiz->score=0;
                    $student_quiz->iscorrect='';
				}
                else{
					if(strtolower(trim($tkvalue["answer"]))==strtolower(trim($useranswerarr[$tkkey]))){
						$wordrsarr=$wordrsarr+1;
                        $score=$score+$quescore;
						$userscore=$userscore+$quescore;
						$uanswer=$uanswer.",".$useranswerarr[$tkkey];
                        $student_quiz->score=$quescore;
                        $student_quiz->iscorrect=1;
	                    array($error,"1");
	                }
                    else{
						$wordrserr=$wordrserr+1;
						$userscore=$userscore+0.0;
	                    $score=$score+0.0;
					    $userscore=$userscore+0.0;
					    $uanswer=$uanswer.",".$useranswerarr[$tkkey];
    	                array($error,"0");
                        $student_quiz->score= 0;
                        $student_quiz->iscorrect=0;
	                }
				}
                 $student_quiz->answer=$useranswerarr[$tkkey];
                 $student_quiz->answerid = $trueanswer_id;
                 $student_quiz->do_time=Date("Y-m-d H:i:s");
                 $student_quiz->add();

            }


        }
        foreach($eqxzdata as $obj){
			$student_quiz->where("studentid='%s' and  classid='%s' and homeworkid='%s' and questionsid='%s' and examid='%s'",session("username"),session("classid"),$homeworkid,$obj->id,$obj->examsid)->delete();
            $student_quiz->studentid=session("username");
            $student_quiz->areaid=session("areaid");
            $student_quiz->classid=session("classid");
            $student_quiz->homeworkid=$homeworkid;
            $student_quiz->questionsid=$obj->id;
            $student_quiz->examid=$obj->examsid;
            $rs=$quiz->where("homeworkid=%d  and examsid=%d",$homeworkid,$obj->examsid)->find();
            $student_quiz->quizid=$rs["id"];
            //试题的分数的查询
            $sql="select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.id=".$obj->id.")";
            $srs=M()->query($sql);
            $quescore=$srs[0]["question_score"];
            //用户的答案的判断


            $sql="select t.flag,s.id from engs_exams_questions_items t left join engs_exams_questions_answer s on t.questionsid=s.questionsid and t.content=s.answer  where s.isdel=1 and t.isdel=1 and t.questionsid=".$obj->id." and s.answer is not null";
            $queanrs=M()->query($sql);
            $answer=$queanrs[0]["flag"];
            if($obj->useranswer==""){
                $score=$score+0.0;
                $student_quiz->score=0.0;
                $student_quiz->iscorrect='';
            }else{
                if($answer==$obj->useranswer){
                	$score=$score+$quescore;
                $student_quiz->score=$quescore;
                	$student_quiz->iscorrect=1;
                $test["iscorrect"]=1;
                }else{
                	$score=$score+0.0;
                $student_quiz->score=0;
                	$student_quiz->iscorrect=0;
                $test["iscorrect"]=0;
                }
            }
            $student_quiz->answerid=$queanrs[0]["id"];
            $student_quiz->answer=$obj->useranswer;
            $test["useranswer"]=$obj->useranswer;
            $student_quiz->do_time=Date("Y-m-d H:i:s");
            $student_quiz->add();

            $test["obj"]=$obj->tag."[".$obj->attr."='".$obj->type."_".$obj->id."']";
            array_push($reteq, $test);
        }
        //听力试卷的判断试题分数判断
        //var_dump($wtdata);exit;
        foreach($eqpddata as $obj){
					  $student_quiz->where("studentid='%s' and  classid='%s' and homeworkid='%s' and questionsid='%s' and examid='%s'",session("username"),session("classid"),$homeworkid,$obj->id,$obj->examsid)->delete();
            $student_quiz->studentid=session("username");
            $student_quiz->areaid=session("areaid");
            $student_quiz->classid=session("classid");
            $student_quiz->homeworkid=$homeworkid;
            $student_quiz->questionsid=$obj->id;
            $student_quiz->examid=$obj->examsid;
						$student_quiz->answer=$obj->useranswer;
            $rs=$quiz->where("homeworkid=%d  and examsid=%d",$homeworkid,$obj->examsid)->find();
            $student_quiz->quizid=$rs["id"];
            //试题的分数的查询
            $sql="select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.id=".$obj->id.")";
            $srs=M()->query($sql);
            $quescore=$srs[0]["question_score"];
            //用户的答案的判断

            $sql="select * from engs_exams_questions_answer t where isdel=1 and t.questionsid=".$obj->id;
            $queanrs=M()->query($sql);
            $answer=$queanrs[0]["answer"];
						if(''==$obj->useranswer){
							$score=$score+0.0;
							$student_quiz->score=0;
							$student_quiz->iscorrect='';
						}else{
							if($answer==$obj->useranswer){
								$score=$score+$quescore;
	              $student_quiz->score=$quescore;
								$student_quiz->iscorrect=1;
	              $test["iscorrect"]=1;
	            }else{
								$score=$score+0.0;
	              $student_quiz->score=0;
								$student_quiz->iscorrect=0;
	              $test["iscorrect"]=0;
	            }

						}
            $student_quiz->answerid=$queanrs[0]["id"];
            $test["useranswer"]=$obj->useranswer;
            $student_quiz->do_time=Date("Y-m-d H:i:s");
            $student_quiz->add();
            $test["obj"]=$obj->tag."[".$obj->attr."='".$obj->type."_".$obj->id."']";
            array_push($reteq, $test);
        }
        //听力试卷的排序试题分数判断
        foreach($eqpxdata as $obj){
					  $student_quiz->where("studentid='%s' and  classid='%s' and homeworkid='%s' and questionsid='%s' and examid='%s'",session("username"),session("classid"),$homeworkid,$obj->id,$obj->examsid)->delete();


            $rs=$quiz->where("homeworkid=%d and username='%s' and classid='%s' and examsid=%d",$homeworkid,$username)->find();

            //试题的分数的查询
            $sql="select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.id=".$obj->id.")";
            $srs=M()->query($sql);
            $quescore=$srs[0]["question_score"];
            //用户的答案的判断
            $sql="select * from engs_exams_questions_answer t where t.isdel=1 and t.questionsid=".$obj->id." order by sortid";
            $queanrs=M()->query($sql);
            $useranswerarr=explode(",",$obj->useranswer);
            $useranswer=array();
            $answer=array();
            $error=array();
            $objstatus=array();
            $score=0.0;
						$uanswer="";
						$uanswer="";
						$userscore=0.0;
						$wordrsarr=0;
						$wordrserr=0;


			 foreach($queanrs as $key=>$value){
                $student_quiz->studentid=session("username");
                $student_quiz->areaid=session("areaid");
                $student_quiz->classid=session("classid");
                $student_quiz->homeworkid=$homeworkid;
                $student_quiz->questionsid=$obj->id;
                $student_quiz->examid=$obj->examsid;
                $student_quiz->quizid=$rs["id"];
               array_push($useranswer, $useranswerarr[$key+1]);
               array_push($answer, $value["answer"]);
               $trueanswer_id = $value["id"];
               $status=$obj->tag."[".$obj->attr."='".$obj->type."_".$obj->id."']:eq(".$key.")";
               array($objstatus,$status);
                             if($useranswerarr[$key]==''){
                  //$wordrserr=$wordrserr+1;
                                    $score=$score+0.0;
                                    $userscore=$userscore+0.0;
                                    $student_quiz->score=0;
                                    $student_quiz->iscorrect='';
                             }else{
                                 if(strtolower(trim($value["answer"]))==strtolower(trim($useranswerarr[$key]))){
                                     $wordrsarr=$wordrsarr+1;
                     $score=$score+$quescore;
                                     $userscore=$userscore+$quescore;
                                     $uanswer=$uanswer.",".$useranswerarr[$key];
                                     $student_quiz->score=$quescore;
                                     $student_quiz->iscorrect=1;
                     array($error,"1");
                   }else{
                                     $wordrserr=$wordrserr+1;
                                     $userscore=$userscore+0.0;
                     $score=$score+0.0;
                                     $userscore=$userscore+0.0;
                                     $uanswer=$uanswer.",".$useranswerarr[$key];
                     array($error,"0");
                     $student_quiz->score= 0;
                     $student_quiz->iscorrect=0;
                   }
                             }
                 $student_quiz->answer=$useranswerarr[$key];
                 $student_quiz->answerid = $trueanswer_id;
                 $student_quiz->do_time=Date("Y-m-d H:i:s");
                 $student_quiz->add();

            }
        }


        $ret["wa"]=$retwa;
        $ret["wt"]=$retwt;
        $ret["ta"]=$retta;
        $ret["eq"]=$reteq;
				$ret["score"]=$score;
				$ret=array();
				if($issubmit==1){
					//向题库中发送数据
					$starttime=date('YmdHis',session("starttime"));
	        $spendtime=time()-session("starttime");
					$homeworkid=session("homeworkid");
		      $paper_id=session("paper_id");
		    	$studentid=session("username");
		    	$schoolclass=session("classid");
					//这里向我的数据库中写入数据

					$yjArr=get_ip("",cookie("tms"));

	        $tqms=$yjArr['tqms']['c1'];



					getuserinfo();
					$studentname=cookie("engm_truename");

					//分数的查询
					unset($sql);
					$score=0.0;
		      $sql="select sum(score) as score from engs_homework_student_word_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
		      $sql=$sql." union all select sum(score) as score from engs_homework_student_text_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
		      $sql=$sql." union all select sum(score) as score from engs_homework_student_word_evaluat where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
		      $sql=$sql." union all select sum(score) as score from engs_homework_student_quiz where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
		      $srs=M()->query($sql);
		      foreach($srs as $key=>$value){
		        $score=$value["score"]+$score;
		      }
              $submittime=Date("Y-m-d H:i:s");
                //查询学生做作业的时间
              $result=M("homework_student_list")->where("studentid='".$studentid."' and classid='".$schoolclass."' and paper_id=".$paper_id)->select();
              $dotime=$result[0]["dotime"];

              $starttime=strtotime($dotime);
              $endtime=strtotime($submittime);
              $spendtime=$endtime-$starttime;
					//$tqms="tqms.youjiaotong.com";
		    	//接口地址接口地址需要进行配置
					$url="http://".$tqms."/online/interface/homework/saveEnAnswers.action";
		    	//通过curl进行post参数的提交

		      $rs=curl_post_contents($url,$studentid,$studentname,$schoolclass,$homeworkid,(int)($spendtime),(double)($score),$starttime);
		    	$data=json_decode($rs);
		    	if($data->success){
                   // M("homework_student_list")->where("studentid='".$studentid."' and classid='".$classid."' and paper_id=".$paper_id)->delete();
                    M("homework_student_list")->where("studentid='".$studentid."' and classid='".$schoolclass."' and paper_id=".$paper_id)->setField("submittime",$submittime);
                    //将用户的答案表进行更新
                    M("homework_student_quiz")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
                    M("homework_student_word_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
                    M("homework_student_word_evaluat")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
                    M("homework_student_text_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");            
     //                $arr["studentid"]=$studentid;
     //                $arr["classid"]=$schoolclass;
     //                $arr["homeworkid"]=$homeworkid;
					// $arr["paper_id"]=$paper_id;
     //                $arr["dotime"]=Date("Y-m-d H:i:s");
     //                M("homework_student_list")->add($arr);
		    		$arr["msg"]="发布成功";
		    		$arr["state"]="1";
		    	}else{
		    		$arr["msg"]="发布失败,请稍后再试";
		    		$arr["state"]="0";
		    	}
			  }
	      //$this->ajaxReturn($arr);
    }


    //获取试卷信息
    public function getExaminfo(){
       $examsid=I("ids");
			 $studentid=I("username");
			 $classid=I("classid");
       $rs=$this->getExam(0,$studentid,$classid,$examsid,0,1);
       $this->ajaxReturn($rs);

    }


    //获取学生的作业的听力试卷
    public function getStuExaminfo(){
        $homeworkid=I("homeworkid");
				$studentid=I("username");
 			  $classid=I("classid");
        $sql="SELECT GROUP_CONCAT(examsid) as ids FROM engs_homework_quiz t WHERE t.`homeworkid`=".$homeworkid." GROUP BY homeworkid";
        $rs=M()->query($sql);
        if(!empty($rs)){
            $rs=$this->getExam($homeworkid,$studentid,$classid,$rs[0]["ids"],0,0);
            $this->ajaxReturn($rs);
        }

    }

		//获取学生的作业的听力试卷
    public function getStuResultExaminfo(){
        $homeworkid=I("homeworkid");
				$studentid=I("username");
 			  $classid=I("classid");
        $sql="SELECT GROUP_CONCAT(examsid) as ids FROM engs_homework_quiz t WHERE t.`homeworkid`=".$homeworkid." GROUP BY homeworkid";
        $rs=M()->query($sql);
        if(!empty($rs)){
            $rs=$this->getExam($homeworkid,$studentid,$classid,$rs[0]["ids"],1,1);
            $this->ajaxReturn($rs);
        }

    }


    //主要是进行作业的全部反馈
    public function getHomeworkFeedback(){
        $homeworkid=I("paper_id");
        $classid=I("classid");
				$jsoncallback=I("jsoncallback");
        //分四部分然后将四部分合起来按照错误率进行排序
        //1单词朗读
				$wordAlound=array();
				// $wasql="SELECT 'http://en.youjiaotong.com/homework/comhw/stuhomeworkfeedback?quesid=123&type=1&classid=123343' as url,t.`homeworkid`,t.`id`,t.`wordid`,(SELECT ew.word FROM engs_word ew WHERE ew.id = t.wordid) AS word,(CASE   WHEN AVG(score) IS NULL  THEN 0";
				// $wasql=$wasql." ELSE AVG(score) END ) AS average,CASE WHEN MAX(score) IS NULL THEN 0 ELSE MAX(score) END AS maxscore,CASE WHEN MIN(score) IS NULL THEN 0 ELSE MIN(score) END AS minscore,";
				// $wasql=$wasql."sum(CASE WHEN s.score IS  NULL THEN 0 ELSE 1 END) AS num";
				// $wasql=$wasql." FROM engs_homework_word_read t LEFT JOIN (SELECT * FROM engs_homework_student_word_read WHERE homeworkid=".$homeworkid." AND classid='".$classid."' ) s ON t.`id`=s.`word_readid` WHERE";
				// $wasql=$wasql." t.`homeworkid` = ".$homeworkid." GROUP BY t.`wordid`";
				// $wordAlound=M()->query($wasql);

				//按照平均分由高到低进行排序
				if(empty($wordAlound)){
					$wordAloundrs=[];
				}else{
					$wordAloundrs=$this->quickSort($wordAlound,"average");
				}
        //2单词测试
				$sql="select *,(SELECT word FROM engs_word ew WHERE ew.id=t.wordid) as name from engs_homework_word_evaluat t where t.homeworkid=".$homeworkid." group by t.wordid";
        $wordlist=M()->query($sql);
        foreach($wordlist as $key=>$value){
          $sql="SELECT ";
					$sql=$sql."SUM(CASE WHEN (s.`iscorrect`!='' and s.iscorrect is not null) THEN 1 ELSE 0 END) AS num,";
          $sql=$sql."SUM(CASE WHEN (t.typeid=0) and (s.`iscorrect`!='' and s.iscorrect is not null) THEN 1 ELSE 0 END) AS yhbs,";
					$sql=$sql."SUM(CASE WHEN (t.`typeid`=1) and (s.`iscorrect`!='' and s.iscorrect is not null) THEN 1 ELSE 0 END) AS hybs,";
          $sql=$sql."SUM(CASE WHEN t.`typeid`=2 and (s.`iscorrect`!='' and s.iscorrect is not null) THEN 1 ELSE 0 END) px,";
          $sql=$sql."SUM(CASE WHEN s.`iscorrect`=1 AND (t.typeid=0) THEN 1 ELSE 0 END) AS yhbsa,";
					$sql=$sql."SUM(CASE WHEN s.`iscorrect`=1 AND (t.typeid=1) THEN 1 ELSE 0 END) AS hybsa,";
          $sql=$sql."SUM(CASE WHEN s.`iscorrect`=1 AND (t.typeid=2) THEN 1 ELSE 0 END) AS pxa ";
          $sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN engs_homework_student_word_evaluat s  ";
          $sql=$sql."ON t.`id` = s.`word_evaluatid` WHERE t.homeworkid = ".$homeworkid." AND t.wordid = ".$value['wordid']." AND s.classid = '".$classid."' group by t.wordid";
          $rs=M()->query($sql);

          $rs[0]["bsrate"]=round((($rs[0]['yhbsa']*100/$rs[0]['yhbs'])+($rs[0]['hybsa']*100/$rs[0]['hybs']))/2);
          $rs[0]["pxrate"]=round($rs[0]['pxa']*100/$rs[0]['px']);
					$wordlist[$key]["identifyrate"]=$rs[0]["bsrate"];
					$wordlist[$key]["identifyrate"]=$rs[0]["speelrate"];
					$wordlist[$key]["identifyrate"]=$rs[0]["num"];
          //$wordlist[$key]["que"]=$rs;
        }
        //$this->assign("wordtestlen",count($wordlist));

				// $wtsql="SELECT t.`homeworkid`,t.`id`,t.`wordid`,(SELECT ew.word FROM  engs_word ew  WHERE ew.id = t.wordid) AS word,";
				// $wtsql=$wtsql."sum(CASE WHEN s.score IS  NULL THEN 0 ELSE 1 END) AS num,";
				// $wtsql=$wtsql."SUM(CASE WHEN (t.`typeid`=0 OR t.typeid=1) AND s.iscorrect=1 THEN 1 ELSE 0 END) AS identifyrate,";
				// $wtsql=$wtsql."SUM(CASE WHEN s.iscorrect=0 THEN 1 ELSE 0 END) AS errorrate,";
        // $wtsql=$wtsql."SUM(CASE WHEN t.`typeid`=2 AND s.iscorrect=1 THEN 1 ELSE 0 END) AS speelrate FROM engs_homework_word_evaluat t LEFT JOIN";
				// $wtsql=$wtsql." (SELECT * FROM engs_homework_student_word_evaluat WHERE homeworkid=".$homeworkid." AND classid='".$classid."' ) s ON t.`id`=s.`word_evaluatid`";
        // $wtsql=$wtsql."  WHERE t.`homeworkid` = ".$homeworkid." GROUP BY t.`wordid`";
				// $wordTest=M()->query($wtsql);
				if(empty($wordlist)){
					$wordTestrs=[];
				}else{
					$wordTestrs=$this->quickSort($wordlist,"speelrate");
				}
        //3课文朗读
				$textAlound=array();
				// $tasql="SELECT t.`homeworkid`,t.`id`,t.`chapterid`,(SELECT chapter FROM engs_text_chapter s WHERE s.id=t.chapterid) AS chapter,et.`id`,et.encontent,";
				// $tasql=$tasql."sum(CASE WHEN s.score IS  NULL THEN 0 ELSE 1 END) AS num,";
				// $tasql=$tasql."CASE WHEN AVG(score) IS NULL THEN 0 ELSE AVG(score) END AS average,";
				// $tasql=$tasql."CASE WHEN MAX(score) IS NULL THEN 0 ELSE AVG(score) END AS maxscore,";
				// $tasql=$tasql."CASE WHEN MIN(score) IS NULL THEN 0 ELSE AVG(score) END AS minscore ";
        // $tasql=$tasql."FROM engs_homework_text_read t LEFT JOIN engs_text et ON t.chapterid=et.chapterid  LEFT JOIN ";
				// $tasql=$tasql."(SELECT * FROM engs_homework_student_text_read WHERE homeworkid=".$homeworkid." AND classid='".$classid."') s ";
				// $tasql=$tasql."ON t.`id`=s.text_readid WHERE t.`homeworkid` = ".$homeworkid." GROUP BY et.id";
				// $textAlound=M()->query($tasql);
				if(empty($textAlound)){
					$textAloundrs=[];
				}else{
					$textAloundrs=$this->quickSort($textAlound,"average");
				}

        //4听力训练采用先判断错误率的方式
				//查新作业数据
				$examsid=M("homework_quiz")->where("homeworkid")->field("group_concat(examsid) as exams")->group("homeworkid")->find();
				$examsQuiz=$this->getFeedBackExam($examsid["exams"],$homeworkid,$classid);

				$examsQuizrs=$this->quickSort($examsQuiz,"errorrate");

				$data["Wordread"]=$wordAloundrs;
				$data["Wordevaluat"]=$wordTestrs;
				$data["Textread"]=$textAloundrs;
				$data["examsquiz"]=$examsQuizrs;
				if(empty($jsoncallback)){
					echo json_encode($data);
				}else{
					header('Content-Type:application/json; charset=utf-8');
	        echo $jsoncallback.'('.json_encode($data).');';
				}
    }


		//此函数是快速排序的算法分治法
		private function quickSort($arr,$sortindex) {
		  if(count($arr) > 1) {
				$_k=$arr[0];
		    $k=$arr[0][$sortindex];
		    $x=array();
		    $y=array();
		    $_size=count($arr);
		    for($i=1;$i<$_size;$i++) {
		      if($arr[$i][$sortindex] <=$k) {
						array_push($y,$arr[$i]);//大的放这边。这样子是从小到大排序，如果想从大到小返回，那么调换位置与$x[] =$arr[$i];的位置即可
		      }else{
		        array_push($x,$arr[$i]);//小的放这边
		      }
		    }
		     //得到分割看来左右两边的数据
		    $x= $this->quickSort($x,$sortindex);//左边的数据，对这些数据再次使用分割法排序，返回的结果就是排序后的数据
		    $y= $this->quickSort($y,$sortindex);//右边的数据
		    return $this->arrpreg($x,$_k,$y);
		  }else{
		    return $arr;
		  }
    }

		//进行数组的合并
		private function arrpreg($a,$b,$c){
        $arr = array();
				if(count($a)>0){
					foreach ($a as $k => $r) {
	            array_push($arr,$r);
	        }
				}
				array_push($arr,$b);
        if(count($c)>0){
					foreach ($c as $k => $r) {
	            array_push($arr,$r);
	        }
				}
        return $arr;
    }



		//获取试卷信息examsid表示试卷的id,$type表示时候已经提交,$answer表示时候显示答案
		public function getFeedBackExam($examsid,$homeworkid,$classid){
	           // echo "没有缓存";
	            $sqlquery = M();
	            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id in ('.$examsid.')';
	            $examsdata = $sqlquery -> query($sql);
	            $exams_tts_type = $examsdata[0]['tts_type'];
	            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes,"'.$examsid.'" as homeworkid from engs_exams_stem where examsid in ('.$examsid.') and parentid = 0 and isdel = 1 order by sortid,id';
	            $stemdata = $sqlquery -> query($sql);
							$arr=array("一","二","三","四","五","六","七","八","九");
							$ret=array();
	            //var_dump($stemdata);exit;
	            foreach ($stemdata as $key => $value) {
	                $stem_type = $value['stem_type'];
	                if($stem_type == '1'){          //独立大题
	                        $score = 0;
	                        $sql='SELECT "'.$examsid.'" as examsid, t.id,t.stemid,t.question_num,t.typeid,t.tcontent,t.acontent,t.itemtype,t.display,t.tips,t.stoptimes,CASE WHEN s.num IS NULL THEN 0 ELSE s.num END AS num,CASE WHEN s.accrnum IS NULL THEN 0 ELSE s.accrnum END AS accrnum,';
													$sql=$sql."case when t.typeid=1 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=4&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=2 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=5&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=3 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=6&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=4 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=7&classid=".$classid."&homeworkid=".$homeworkid."') end as url ";
	                        $sql=$sql.'FROM engs_exams_questions t LEFT JOIN (SELECT  eq.`homeworkid`,eq.examid,eq.classid,eq.questionsid,eq.quizid,COUNT(*) AS num,SUM(iscorrect) AS accrnum  FROM ';
													$sql=$sql.'engs_homework_student_quiz eq  WHERE eq.iscorrect!="" and eq.`homeworkid`='.$homeworkid.' AND eq.classid="'.$classid.'" AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = '.$homeworkid.' AND classid = "'.$classid.'") GROUP BY eq.`homeworkid`,eq.examid,eq.classid,';
													$sql=$sql.'eq.questionsid,eq.quizid) s ON t.`id`=s.questionsid WHERE  t.stemid ='.$value['id'].'  AND t.isdel = 1 ORDER BY t.sortid,t.id,t.question_num';
													$question = $sqlquery -> query($sql);
													unset($data);
													foreach($question as $qk=>$qv){
														$data=array();
														$data["id"]=$qv["id"];
														$data["name"]="第".$arr[$key]."大题、第".($qk+1)."小题";
														$data["num"]=$qv["num"];
														$data["url"]=$qv["url"];
                                                        $data["errnum"]=$qv["num"]-$qv["accrnum"];
                                                        $data["errornum"]=$qv["num"]-$qv["accrnum"];
														if($qv["num"]=="0"){
															 $data["errorrate"]=0;
														}else{
															 $data["errorrate"]=round($data["errnum"]*100/$data["num"]);
														}
														array_push($ret,$data);
													}
	                }
	                else{                           //组合大题
	                    $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid in ('.$examsid.') and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';

	                    $stem_children = $sqlquery -> query($sql);
	                    foreach ($stem_children as $keychild => $childvalue) {
	                        $sql='SELECT "'.$examsid.'" as examsid,t.id,t.stemid,t.question_num,t.typeid,t.tcontent,t.acontent,t.itemtype,t.display,t.tips,t.stoptimes,CASE WHEN s.num IS NULL THEN 0 ELSE s.num END AS num,CASE WHEN s.accrnum IS NULL THEN 0 ELSE s.accrnum END AS accrnum,';
													$sql=$sql."case when t.typeid=1 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=4&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=2 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=5&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=3 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=6&classid=".$classid."&homeworkid=".$homeworkid."')";
													$sql=$sql." when t.typeid=4 then concat('http://".C("domain")."/homework/Comhw/stuhomeworkfeedback?id=',t.id,'&type=7&classid=".$classid."&homeworkid=".$homeworkid."') end as url ";
	                        $sql=$sql.'FROM engs_exams_questions t LEFT JOIN (SELECT  eq.`homeworkid`,eq.examid,eq.classid,eq.questionsid,eq.quizid,COUNT(*) AS num,SUM(iscorrect) AS accrnum  ';
													$sql=$sql.'FROM engs_homework_student_quiz eq  WHERE eq.iscorrect!="" and eq.`homeworkid`='.$homeworkid.' AND eq.classid="'.$classid.'" AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = '.$homeworkid.' AND classid = "'.$classid.'") GROUP BY eq.`homeworkid`,eq.examid,eq.classid,eq.questionsid,eq.quizid) s ';
													$sql=$sql.'ON t.`id`=s.questionsid WHERE  t.stemid ='.$childvalue['id'].'  AND t.isdel = 1 ORDER BY t.sortid,t.id,t.question_num';
	                        $child_question = $sqlquery -> query($sql);
	                        $stem_children[$keychild]['question'] = $child_question;
													foreach($child_question as $qk=>$qv){
														$data=array();
														$data["id"]=$qv["id"];
														$data["name"]="第".$arr[$key]."大题、第".($keychild+1)."部分中的第".($qk+1)."小题";
														$data["num"]=$qv["num"];
														$data["url"]=$qv["url"];
                                                        $data["errnum"]=$qv["num"]-$qv["accrnum"];
                                                        $data["errornum"]=$qv["num"]-$qv["accrnum"];
														if($qv["percount"]=="0"){
															 $data["errorrate"]=0;
														}else{
															 $data["errorrate"]=round($data["errnum"]*100/$qv["num"]);
														}
														array_push($ret,$data);
													}
	                    }
	                }
	             }

	        return $ret;
		}


		//针对反馈的页面进行的函数
		public function stuwafeedback(){
			$id=I("id");
			$classid=I("classid");
			$studentid=I("studentid");
			$homeworkid=I("homeworkid");
			$sql="select avg(case when b.score is null then 0 else b.score end) as avarage,";
			$sql=$sql."(select ukmp3 from engs_word left join engs_base_word on engs_word.base_wordid=engs_base_word.id where engs_word.id=t.wordid) as mp3,";
			$sql=$sql."(select pic from engs_word left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id where engs_word.id=t.wordid) as pic,";
			$sql=$sql."sum(case when b.score is null then 0 else 1 end) as  num from engs_homework_word_read t left join ";
			$sql=$sql."(select * from engs_homework_student_word_read s where s.homeworkid=".$homeworkid." and s.classid='".$classid."') b on t.id=b.word_readid";
			$sql=$sql." where  t.id=".$id." group by t.homeworkid";
      $ret=M()->query($sql);
			$this->ajaxReturn($ret);
		}

		//针对反馈的页面进行的函数
		public function stuwtfeedback(){
			//单词的id
			$id=I("id");
			$classid=I("classid");
			$homeworkid=I("homeworkid");
			$sql="SELECT t.*,";
			$sql=$sql."(select engs_word.word from engs_word left join engs_base_word on engs_word.base_wordid=engs_base_word.id WHERE engs_word.id=t.`wordid`) as word,";
			$sql=$sql."(select engs_base_word_explains.explains from engs_word left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id WHERE engs_word.id=t.`wordid`) as explains,";
			$sql=$sql."(select engs_base_word.ukmp3 from engs_word left join engs_base_word on engs_word.base_wordid=engs_base_word.id WHERE engs_word.id=t.`wordid`) as mp3,";
			$sql=$sql."(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id)-";
			$sql=$sql."(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id AND  z.iscorrect=1) as eperstu,";
			$sql=$sql."(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id AND  z.iscorrect=1) as perstu,";
			$sql=$sql."ROUND(100*(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id AND  z.iscorrect=1)/";
			$sql=$sql."(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id)) AS per,";
			$sql=$sql."(100-ROUND(100*(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id AND  z.iscorrect=1)/";
			$sql=$sql."(SELECT COUNT(*) FROM engs_homework_student_word_evaluat z WHERE z.classid='".$classid."' AND z.homeworkid=t.homeworkid AND z.word_evaluatid=t.id)))";
			$sql=$sql." AS eper FROM engs_homework_word_evaluat t  WHERE t.`homeworkid`=".$homeworkid." and t.id=".$id." ORDER BY t.id,typeid";
			//echo  $sql;exit;
            $rs=M()->query($sql);
			$ans="";
			foreach($rs as $k=>$v){
				if($v["typeid"]==2){
					$optionarr=str_split($v["answer"]);
					$udata=explode(",",$v["stuanswer"]);
					foreach($optionarr as $k1=>$v1){
							$ans=$ans."<a onclick='afocus(this);' style='border-style:solid;width:25px;height:25px;border-width:1px;margin-left: 10px;display:inline-block;'>".$udata[$k1+1]."</a>";
					}
					$rs[$k]["answer"]=$ans;
				}
			}
			$this->ajaxReturn($rs);
		}

		//针对反馈的页面进行的函数
		public function stutafeedback(){
			$textid=I("id");
			$classid=I("classid");
			$homeworkid=I("homeworkid");
			$homework_text_read=M("homework_text_read");
			$sql="select * from engs_homework_text_read t left join engs_text_chapter s on t.chapterid=s.id where t.homeworkid= ".$homeworkid." and chapterid=".$textid;
			$textrs=M()->query($sql);
			$count=0;
			$textreadcount=0;
			foreach($textrs as $key=>$value){
					$sql="select t.encontent,t.mp3,";
					$sql=$sql."(select max(s.score) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as userscore,";
					$sql=$sql."(select max(s.mp3) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as usermp3,";
					$sql=$sql."(select count(distinct s.studentid) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as num,";
					$sql=$sql."(select AVG(case when s.score is null then 0 else s.score end) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as average";
					$sql=$sql." from engs_text t where t.chapterid=".$value["id"]." and isdel=1";
					$texts=M()->query($sql);
					$textreadcount=$textreadcount+count($texts);
					$textrs[$key]["text"]=$texts;
			}
			$this->ajaxReturn($textrs);
		}

		//针对反馈的页面进行的函数
		public function stueqfeedback(){
			$questionsid=I("id");
			$classid=I("classid");
			$homeworkid=I("homeworkid");
			$sql='select t.id,t.stemid,t.question_num,t.typeid,t.tcontent,t.acontent,t.itemtype,t.display,t.tips,t.stoptimes,';
			$sql=$sql.'CASE WHEN s.num IS NULL THEN 0 ELSE s.num END AS num,CASE WHEN s.accrnum IS NULL THEN 0 ELSE s.accrnum END AS accrnum ';
			$sql=$sql.'FROM engs_exams_questions t LEFT JOIN (SELECT  eq.`homeworkid`,eq.examid,eq.classid,eq.questionsid,eq.quizid,COUNT(*) AS num,SUM(iscorrect) AS accrnum  FROM ';
			$sql=$sql.'engs_homework_student_quiz eq  WHERE eq.`homeworkid`='.$homeworkid.' AND eq.classid="'.$classid.'" GROUP BY eq.`homeworkid`,eq.examid,eq.classid,eq.questionsid,eq.quizid) s';
			$sql=$sql.' ON t.`id`=s.questionsid WHERE t.id ='.$questionsid.'  AND t.isdel = 1 ORDER BY t.sortid,t.id,t.question_num';
			$question = M() -> query($sql);
			foreach ($question as $quekey => $quevalue) {
					$question[$quekey]["errnum"]=$quevalue["num"]-$quevalue["accrnum"];
					if($quevalue["num"]=="0"){
						 $question[$quekey]["errorrate"]="0%";
					}else{
						 $question[$quekey]["errorrate"]=round($question[$quekey]["errnum"]*100/$question[$quekey]["num"])."%";
					}
					//主要是处理填空试题的正则
					$question[$quekey]["tcontent"]=preg_replace("/\#\#答案(.*?)\#\#/",'<span>________</span>',$question[$quekey]["tcontent"]);
					if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案
							$sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
							$que_items = M()->query($sql);
							$question[$quekey]['items'] = $que_items;
							if($quevalue['typeid'] == '1'){
									$sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b ';
									$sql=$sql.'where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.questionsid= ' . $quevalue['id'] . ' and';
									$sql=$sql.' a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
							}
							else{
									$sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
							}
							$queanswer = M() -> query($sql);
					}
					else{
							$question[$quekey]['items'] = 0;
							 $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
							//echo $sql;exit;
							$queanswer = M() -> query($sql);
					}
					$question[$quekey]['que_answer'] = $queanswer;
                    $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where  parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                    $que_tts = M() -> query($sql);
                    if(count($que_tts) == 0){
                        //组合题问题听力材料
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where  parentid='.$quevalue['stemid'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $que_tts = M() -> query($sql);
                    }
                    $question[$quekey]['que_tts'] = $que_tts;
			}
			$this->ajaxReturn($question);
		}


        //根据用户名查询学科以及对应的版本
        public function getUserVersion(){
            $username=I("username","");
            if(empty($username)){
                $ret["succ"]=0;
                $ret["msg"]="用户名不能为空";
                $ret["data"]=array();
            }else{
                $tms=I("tms","");
                if(empty($tms)){
                   $ret["succ"]=0;
                   $ret["msg"]="TMS地址参数不能为空";
                   $ret["data"]=array(); 
                }else{
                    $subjectcode=I("subjectcode","");
                    if(empty($subjectcode)){
                        $content=getVersion($username,$tms);
                        if($content["success"]==0){
                            $ret["succ"]=0;
                            $ret["msg"]="接口参数错误或者接口请求失败";
                            $ret["data"]=array();
                        }else{
                            $ret["succ"]=1;
                            $ret["data"]=$content["data"];
                            $ret["msg"]="";
                        }
                    }else{
                        $content=getVersion($username,$tms);
                        if($content["success"]==0){
                            $ret["succ"]=0;
                            $ret["msg"]="接口参数错误或者接口请求失败";
                            $ret["data"]=array();
                        }else{
                            $ret["succ"]=1;
                            $ret["data"]=$content["data"][$subjectcode];
                            $ret["msg"]="";
                        }
                    }
                }
            }
            
            $this->ajaxReturn($ret);
        }

    public function publish_homework(){
        $protocol = I("ishttps/s",'http:');
        $data=I("homework");
        $source=I("source/d",0);
        $data=urldecode($data);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data);
        $ks_code=$data->ks_code;
        $username=$data->username;
        $homework=$data->homework;
        $words=$homework->words;
        $texts=$homework->texts;
        $exams=$homework->exams;
        $tqms = cookie('tqms');
        if(empty($tqms)){
            $tqms = cookie('tms');
        }
        //想homework表中插入数据
        $homework=M("homework");
        $homework->ks_code=$ks_code;
        $homework->teachid=$username;
        $homework->areaid=$tqms;
        $homework->publish_time=Date("Y-m-d H:i:s");
        //查询作业名称
        $rs=M("rms_unit")->where("ks_id='%s'",$ks_code)->find();
        $ks_name=$rs["ks_name"];
        $homework->homework_name=$ks_name;
        $homeworkid=$homeworkid=$homework->add();
        $this->english_publish($homeworkid,$words,$texts,$exams);
        //返回数据
        $arr["homeworkid"]=$homeworkid;
        $arr["ks_code"]=$ks_code;
        $arr["username"]=$username;
        //$arr["name"]=urlencode(urlencode($ks_name." 听读作业 ".Date('nd-Hi'))) ;
        //$arr["name"]=urlencode($ks_name." 听读作业 ".Date('nd-Hi')) ;
        $arr["name"]=$ks_name." 听读作业 ".Date('nd-Hi') ;
        if($source=='0'){
            $yjArr=get_ip("",cookie("tms"));
            $tqms=$yjArr['tqms']['title'];
            $arr["url"]=$protocol."//".$tqms."/tqms/hw/toEnPublishForStuPage.action?paper_id=".$homeworkid."&username=".$username."&ks_code=".$ks_code."&paper_name=".urlencode($arr["name"]);
        }else{
            $arr["url"]=$ks_name." 听读作业 ".Date('nd-Hi');
        }
        
        $this->ajaxReturn($arr);
    }


    //布置作业函数
    protected function english_publish($homeworkid,$words,$texts,$exams){
        $homeworkteacher=array();
        $homeworkteacher["wacount"]=0;
        $homeworkteacher["wscount"]=0;
        $homeworkteacher["wccount"]=0;
        $homeworkteacher["wrcount"]=0;
        $homeworkteacher["tacount"]=0;
        $homeworkteacher["eqcount"]=0;
        //单词测试的处理
        //单词朗读0表示单词跟读 1表示单词拼写 2表示听音选词 3表示英汉互译
        $homework_word_read=M("homework_word_read");
        //单词测试
        $homework_word_evaluat=M("homework_word_evaluat");
        foreach($words as $key=>$value){
            $wewordid=$value->id;
            if(empty($wewordid)){
                continue;
            }
            if($value->typeid==0){
                $homework_word_read->wordid=$value->id;
                $homework_word_read->homeworkid=$homeworkid;
                if(empty($value->id)){
                    continue;
                }
                $homework_word_read->add();
                $homeworkteacher["wacount"]=$homeworkteacher["wacount"]+$value->quescount;
            }else if($value->typeid==1){
                $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS ukmp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON t.base_explainsid=s.id WHERE t.id=".$value->id;
                $wordrs=M()->query($sql);
                $qian=array(" ","　","\t","\n","\r");
                $hou=array("","","","","");
                $word=str_replace($qian,$hou,$wordrs[0]["word"]);
                //数字数组
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
                
                //$data["answer"]=implode(",",$arr);
                if($len<10){
                    for ($i = 1; $i < 10-$len; $i++) {
                        //$ran=rand(65, 122);
                        $rand = array_rand($wordnum);
                        $ran = $wordnum[$rand];
                        $rword=chr($ran);
                        unset($wordnum[$rand]);

                        // while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                        //   $ran=rand(65, 122);
                        // }
                        array_push($optionarr,$rword);
                    }
                }else if($len>=10&&i<15){
                    for ($i = 1; $i < 15-$len; $i++) {
                        $rand = array_rand($wordnum);
                        $ran = $wordnum[$rand];
                        $rword=chr($ran);
                        unset($wordnum[$rand]);

                        // while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                        //   $ran=rand(65, 122);
                        // }
                        array_push($optionarr,$rword);
                     }

                }else if($len>=15&&i<20){
                    for ($i = 1; $i < 20-$len; $i++) {
                        $rand = array_rand($wordnum);
                        $ran = $wordnum[$rand];
                        $rword=chr($ran);
                        unset($wordnum[$rand]);

                        // while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                        //   $ran=rand(65, 122);
                        // }
                        array_push($optionarr,$rword);
                    }
                }else{
                    for ($i = 1; $i < 25-$len; $i++) {
                        $rand = array_rand($wordnum);
                        $ran = $wordnum[$rand];
                        $rword=chr($ran);
                        unset($wordnum[$rand]);

                        // while($ran==91||$ran==92||$ran==93||$ran==94||$ran==95||$ran==96||in_array($rword, $optionarr,TRUE)){
                        //   $ran=rand(65, 122);
                        // }
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
                $homeworkteacher["wscount"]=$homeworkteacher["wscount"]+$value->quescount;
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
                $homeworkteacher["wccount"]=$homeworkteacher["wccount"]+$value->quescount;
            }else if($value->typeid==3){
                $sql="SELECT word,explains,isstress,(SELECT ukmp3 FROM engs_base_word WHERE id=t.base_wordid) AS ukmp3,(SELECT isword FROM engs_base_word WHERE id=t.base_wordid) AS isword FROM engs_word t LEFT JOIN engs_base_word_explains s ON t.base_explainsid=s.id WHERE t.id=".$value->id;
                $wordrs=M()->query($sql);
                $choice=array();
                $answer=['A','B','C'];
                $rs=$this->get_choice_list($value->id);
                $ran=mt_rand(0,1);
                if($ran==0){
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
                    $data["typeid"]=3;
                    $data["sortid"]=mt_rand(1000, 9999);
                    $homework_word_evaluat->add($data);
                }else{
                    $choice[0]=$rs[0]["explains"];
                    $choice[1]=$rs[1]["explains"];
                    $choice[2]=$wordrs[0]["explains"];
                    shuffle($choice);
                    $founded = array_search($wordrs[0]["explains"], $choice);
                    $data["homeworkid"]=$homeworkid;
                    $data["wordid"]=$value->id;
                    $data["option_a"]=$choice[0];
                    $data["option_b"]=$choice[1];
                    $data["option_c"]=$choice[2];
                    $data["answer"]=$answer[$founded];
                    $data["typeid"]=1;
                    $data["sortid"]=mt_rand(1000, 9999);
                    $homework_word_evaluat->add($data);
                }
                unset($choice);unset($data);
                $homeworkteacher["wrcount"]=$homeworkteacher["wrcount"]+$value->quescount;
            }
        }
        //课文测试的处理
        foreach($texts as $key=>$value){
            $chapterid=$value->id;
            if(empty($chapterid)){
                continue;
            }
            $homework_text_read=M("homework_text_read");
            $homework_text_read->chapterid=$value->id;
            $homework_text_read->homeworkid=$homeworkid;
            $homework_text_read->add();
            $homeworkteacher["tacount"]=$homeworkteacher["tacount"]+$value->quescount;
        }
        //试卷测试的处理
        foreach($exams as $key=>$value){
            $examsid=$value->id;
            if(empty($examsid)){
                continue;
            }
            $homework_quiz=M("homework_quiz");
            $homework_quiz->examsid=$value->id;
            $homework_quiz->homeworkid=$homeworkid;
            $homework_quiz->add();
            $examscount=M("exams")->where("id=%d",$value->id)->find();
            $homeworkteacher["eqcount"]=$homeworkteacher["eqcount"]+$value->quescount;
        }

        //布置作业修改数量
        M("homework")->where("id=%d",$homeworkid)->save($homeworkteacher);

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
        $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 ';
        $sql.='  WHERE  t1.id=bwe.base_wordid  and t1.isword=1  And t1.word!="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';
        $sql.='  GROUP BY  t1.id ,t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by rand()  LIMIT 100';
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


    //这里进行的需要的是异步的请求
    public function getStudentinfo(){
        //进行数据的examsid
        $sso=I("sso");
        $homeworkid=I("homeworkid");
        $studentid=I("studentid");
        //查询学生姓名
        $studentname="秦国勇";
        //设置学生跟读的姓名
        M("homework_student_word_read")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_text_read")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_word_evaluat")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_quiz")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
    }


    //进行音频格式的转码需要
    public function audioTransform(){
        //音频的基础路劲
        $basepath="";
        $andio=I("audio");
        //查询学生姓名
        $studentname="秦国勇";
        //设置学生跟读的姓名
        M("homework_student_word_read")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_text_read")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_word_evaluat")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
        M("homework_student_quiz")->where("homeworkid=%d and studentid='%s'",$homeworkid,$studentid)->setField("studentname",$studentname);
    }




    public function getExamScore($examsid){
        $total_score=0;
        $sql = 'select id,stem_type,question_score from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
        $stemdata = $sqlquery -> query($sql);
        foreach ($stemdata as $key => $value) {
            $stem_type = $value['stem_type'];
            if($stem_type == '1'){          //独立大题
                $score = 0;
                $sql='select t.* from engs_exams_questions t where t.stemid ='.$value['id'].' and t.isdel=1';
                $question = $sqlquery -> query($sql);
                foreach ($question as $quekey => $quevalue) {
                    $sql = 'select id from engs_exams_questions_answer where isdel=1 and questionsid='.$quevalue["id"];     //独立大题问题的答案
                    $queanswer = $sqlquery -> query($sql);
                    $score += $value['question_score'] * count($queanswer);    
                }
                $stemdata[$key]['total_score'] = $score;
                $total_score += $score;
            }else if($stem_type ==2){                 //组合大题
                unset($question);
                unset($score);
                $sql = 'select id,stem_num,stem_type,question_scores from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                $stem_children = $sqlquery -> query($sql);
                foreach ($stem_children as $keychild => $childvalue) {
                    //答题下面的问题开始
                    $sql='select t.* from engs_exams_questions t where t.stemid ='.$childvalue['id'].' and t.isdel=1';
                    $question = $sqlquery -> query($sql);
                    $score=0.0;
                    foreach ($question as $quekey => $quevalue) {
                        //问题答案
                        $sql = 'select id from engs_exams_questions_answer  where questionsid = ' . $quevalue['id'].' and isdel=1 ';     //独立大题问题的答案
                        $queanswer = $sqlquery -> query($sql);
                        $score += $childvalue['question_score'] * count($queanswer);
                    }
                    $total_score += $score;

                }
            }
        }
        return $total_score;
    }



}
