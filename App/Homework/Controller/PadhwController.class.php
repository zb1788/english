<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 列举出现的公共的变量的意思
* 首页控制器类中的共有的变量$index表示是第几道试题$iserror表示是够显示错题$time表示时间$issubmit表示是否提交用来查看答案
*
* @author         qgy
* @since          2.0
*/

class PadhwController extends CheckController {
  //首页
  	public function examsquiz(){
      $paper_id=I("homeworkid/d");
      $index=I("index/d",0);
      $classid=I("classId");
      $type=I("type");
      $studentId=I("studentId");
  		$tms=I("tms");
  		$yjArr=get_ip("",I("tms"));
  		$tqms=$yjArr['tqms']['c1'];
      $sso=$yjArr['sso']['c1'];
      $tms=$yjArr['tms']['c1'];
  		$hwid=I("hwid",0);
      $location=C("record_mp3_path");
      
  		$starttime=I("starttime");
  		$batchid=I("batchid");
  		$callbackURL=I("callbackURL");
      $isOverdue=I("isOverdue");
      //作业的名称
      $paper=M("homework_batch")->where("batchid='%s' and homeworkid='%s' and paper_id=%d",$batchid,$hwid,$paper_id)->find();
      $papername=$paper["paper_name"];
      $submittime=Date("Y-m-d H:i:s");
      //跟新用户的打开作业的时间
      M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentId,$paper_id)->setField("lastupdatetime",$submittime);
  		//是否过期的标志
  		$isOverdue=I("isOverdue");
      $issubmit=I("issubmit");
      $this->assign("sso",$sso);
      $this->assign("location",$location);
      $this->assign("batchid",$batchid);
      $this->assign("isOverdue",$isOverdue);
      $this->assign("type",$type);
      $this->assign("papername",$papername);
      $this->assign("studentid",$studentId);
      $this->assign("classid",$classid);
      $this->assign("index",$index);
      $this->assign("paperid",$paper_id);
      $this->assign("hwid",$hwid);
      $this->assign("tqms",$tqms);
      $this->assign("tms",I("tms"));
  		$this->display("examsquiz");
  	}

    public function finish(){
      $paper_id=I("homeworkid/d");
      $index=I("index/d",0);
      $classid=I("classId");
      $iserror=I("iserror/d",0);
      $type=I("type");
      $studentid=I("studentId");
      $tms=I("tms");
      $yjArr=get_ip("",I("tms"));
      $tqms=$yjArr['tqms']['c1'];
      $sso=$yjArr['sso']['c1'];
      $tms=$yjArr['tms']['c1'];
      $location=C("record_mp3_path");
      $hwid=I("hwid",0);
      $starttime=I("starttime");
      $batchid=I("batchid");
      $callbackURL=I("callbackURL");
      $isOverdue=I("isOverdue");

    

      //作业的名称
      $homeworkrs=M("homework_batch")->where("batchid='%s' and homeworkid='%s' and paper_id=%d",$batchid,$hwid,$paper_id)->find();
      $papername=$homeworkrs["paper_name"];


      $homeworkscore=0;
      //查询用户的时间
      $publishstudents=json_decode($homeworkrs["publishstudents"],TRUE);
      $students=$publishstudents[$classid]["students"];
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
      $this->assign("homeworkid",$homeworkid);
      $this->assign("studentscore",round($studentscore,1));
      $this->assign("batchid",$batchid);
      
      $this->assign("classid",$classid);
      $this->assign("type",$type);
      $this->assign("homeworksubmittime",$homeworksubmittime);
      $this->assign("homeworkmaxscore",round($homeworkmaxscore,1));
      $this->assign("homeworkaveragescore",$homeworkaveragescore);
      //是否过期的标志
      $isOverdue=I("isOverdue");

      $issubmit=I("issubmit");
      $this->assign("iserror",$iserror);
      $this->assign("location",$location);
      $this->assign("sso",$sso);
      $this->assign("batchid",$batchid);
      $this->assign("isOverdue",$isOverdue);
      $this->assign("type",$type);
      $this->assign("papername",$papername);
      $this->assign("classid",$classid);
      $this->assign("index",$index);
      $this->assign("studentid",$studentid);
      $this->assign("paperid",$paper_id);
      $this->assign("hwid",$hwid);
      $this->assign("tqms",$tqms);
      $this->assign("tms",$tms);
      $this->display("finish");
    }



  	public function getQuestionCard(){
  		$homeworkid=I("homeworkid/d");
  		$studentid=I("username/s");
  		$questions=array();
  		//查询单词跟读
  		$sql="select t.id,s.id as score from engs_homework_word_read t left join (select * from engs_homework_student_word_read where studentid = '".$studentid."'  and homeworkid = ".$homeworkid.") s on t.`id` = s.word_readid where t.`homeworkid` = ".$homeworkid." order by t.id";
      
      $wars=M()->query($sql);
  		if(!empty($wars)){
  			unset($temp);
  			$temp["name"]="单词跟读";
  			$temp["num"]=count($wars);
  			$temp["questions"]=$wars;
  			array_push($questions, $temp);
  		}
  		//查询单词测试
  		$sql="SELECT t.id,s.id as score,t.`typeid` FROM engs_homework_word_evaluat t LEFT JOIN (SELECT * FROM engs_homework_student_word_evaluat WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.word_evaluatid WHERE t.`homeworkid` = ".$homeworkid." ORDER BY sortid,t.id";
      $wtrs=M()->query($sql);
  		if(!empty($wtrs)){
  			$transarr=array();
  			$listenarr=array();
  			$spellarr=array();
  			foreach($wtrs as $key=>$value){
  				$temp["id"]=$value["id"];
  				$temp["score"]=$value["score"];
  				if($value["typeid"]==0){
  					array_push($listenarr, $temp);
  				}else if($value["typeid"]==1||$value["typeid"]==3){
  					array_push($transarr, $temp);
  				}else if($value["typeid"]==2){
  					array_push($spellarr, $temp);
  				}
  			}
  			if(count($spellarr)>0){
  				unset($temp);
  				$temp["name"]="单词拼写";
	  			$temp["num"]=count($spellarr);
	  			$temp["questions"]=$spellarr;
	  			array_push($questions, $temp);
  			}

        if(count($listenarr)>0){
          unset($temp);
          $temp["name"]="听音选词";
          $temp["num"]=count($listenarr);
          $temp["questions"]=$listenarr;
          array_push($questions, $temp);
        }
        if(count($transarr)>0){
          unset($temp);
          $temp["name"]="英汉互译";
          $temp["num"]=count($transarr);
          $temp["questions"]=$transarr;
          array_push($questions, $temp);
        }
        
  		}
  		//查询课文跟读
  		$sql="select t.id,e.`cncontent`,e.`encontent`,e.`mp3`,s.id as score from engs_homework_text_read t left join engs_text e on t.`chapterid`=e.`chapterid` left join (select  *  from engs_homework_student_text_read where studentid = '".$studentid."' and homeworkid = ".$homeworkid.") s on t.`id` = s.text_readid AND e.id=s.textid where e.isdel=1 and t.`homeworkid` = ".$homeworkid." order by e.textsortid,e.id";
  		$tars=M()->query($sql);
  		if(!empty($tars)){
  			unset($temp);
  			$temp["name"]="课文跟读";
  			$temp["num"]=count($tars);
  			$temp["questions"]=$tars;
  			array_push($questions, $temp);
  		}
  		//查询听力训练
  		$sql="SELECT t.id,s.id as score FROM engs_exams_questions t LEFT JOIN (SELECT * FROM engs_homework_student_quiz WHERE studentid='".$studentid."' AND homeworkid=".$homeworkid.") AS s ON t.id=s.questionsid WHERE t.isdel=1 and t.`examsid` IN (SELECT examsid FROM engs_homework_quiz WHERE `homeworkid`=".$homeworkid.") order by t.paper_sortid,t.id";
  		$eqrs=M()->query($sql);
  		if(!empty($eqrs)){
  			unset($temp);
  			$temp["name"]="听力训练";
  			$temp["num"]=count($eqrs);
  			$temp["questions"]=$eqrs;
  			array_push($questions, $temp);
  		}
  		$this->ajaxReturn($questions);
  	}



  	public function getHomeworkQuestions(){
  		$homeworkid=I("homeworkid/d");
  		$studentid=I("username/s");
  		$questions=array();
  		//查询单词跟读
  		$sql="select s.id as userreadid,t.id,t.`wordid`,w.`word`,p.`explains`,p.`pic`,p.`morphology`,s.score,s.mp3,(CASE WHEN b.`ukmark` IS NULL THEN '' ELSE b.ukmark END) AS ukmark,b.`ukmp3` from engs_homework_word_read t LEFT JOIN engs_word w ON t.`wordid`=w.`id` LEFT JOIN engs_base_word_explains p ON w.`base_explainsid`=p.`id` LEFT JOIN engs_base_word b ON w.base_wordid=b.id left join (select * from engs_homework_student_word_read where studentid = '".$studentid."'  and homeworkid = ".$homeworkid.") s on t.`id` = s.word_readid where t.`homeworkid` = ".$homeworkid." order by id";
  		$wars=M()->query($sql);
  		if(!empty($wars)){
  			foreach($wars as $key=>$value){
  				unset($temp);
  				$temp["name"]="单词跟读";
          $temp["userreadid"]=$value["userreadid"];
  				$temp["id"]=$value["id"];
          $temp["questype"]=0;
          $temp["score"]=$value["score"];
          $temp["contentid"]=$value["wordid"];
  				$temp["pic"]=$value["pic"];
  				$temp["picbaseroot"]=PROTOCOL."://".C("word_pic_path");
  				$temp["index"]=$key+1;
  				$temp["count"]=count($wars);
  				$temp["type"]=0;
  				$temp["typeid"]=0;
  				$temp["display"]=0;
	  			$temp["encontent"]=$value["word"];
	  			$temp["cncontent"]=$value["explains"];
	  			if(empty($value["ukmark"])){
	  				$temp["remark"]="";
	  			}else{
	  				$temp["remark"]=$value["ukmark"];
	  			}
	  			$ttss=array();
  				$tts["before"]="";
  				$tts["content"]=$value["word"];
  				$tts["mp3"]=$value["ukmp3"];
          $tts["playmp3"]=PROTOCOL."://".C("word_mp3_path").$value["ukmp3"];
  				$tts["stoptime"]="0";
  				array_push($ttss, $tts);
  				$temp["tts"]=$ttss;
	  			$temp["audiobaseroot"]=C("word_mp3_path");
	  			$temp["items"]=array();
	  			$temp["answer"]=array();
	  			array_push($questions, $temp);
  			}
  		}
  		//查询单词测试
  		$sql="SELECT t.id,p.pic,t.`homeworkid`,t.`answer`,t.`option_a`,t.`option_b`,t.`option_c`,t.`wordid`,t.`typeid`,w.`word`,p.`explains`,b.`ukmp3`,s.answer as useranswer from engs_homework_word_evaluat t  LEFT JOIN engs_word w ON t.`wordid`=w.`id` LEFT JOIN engs_base_word_explains p ON w.`base_explainsid`=p.`id` left join engs_base_word b on w.`base_wordid`=b.`id` LEFT JOIN (SELECT * FROM engs_homework_student_word_evaluat WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.word_evaluatid WHERE t.`homeworkid` = ".$homeworkid." ORDER BY t.sortid,t.id";
  		$wtrs=M()->query($sql);
  		if(!empty($wtrs)){
        $listenarr=array();
        $spellarr=array();
        $transarr=array();
  			foreach($wtrs as $key=>$value){
  				unset($temp);
  				$temp["id"]=$value["id"];
          $temp["questype"]=0;
          $temp["contentid"]=$value["wordid"];
          $temp["useranswer"]=$value["useranswer"];
  				$temp["remark"]="";
  				$temp["pic"]=$value["pic"];
  				$temp["picbaseroot"]=PROTOCOL."://".C("word_pic_path");
  				$temp["index"]=$key+1;
  				$temp["count"]=count($wtrs);
  				$temp["typeid"]=$value["typeid"];
  				$temp["display"]=0;
  				$ttss=array();
  				$tts["before"]="";
  				$tts["content"]=$value["word"];
  				$tts["mp3"]=$value["ukmp3"];
          $tts["playmp3"]=PROTOCOL."://".C("word_mp3_path").$value["ukmp3"];
  				$tts["stoptime"]="0";
  				array_push($ttss, $tts);
  				$temp["tts"]=$ttss;
	  			$temp["audiobaseroot"]=C("word_mp3_path");
  				$items=array();
  				if($value["typeid"]==0){
  					$temp["type"]=2;
  					$temp["encontent"]=$value["ukmp3"];
	  				$temp["cncontent"]=$value["explains"];
	  				$temp["answer"]=$value["answer"];
	  				$item["flag"]="A";
	  				$item["content"]=$value["option_a"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="A"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
	  				$item["flag"]="B";
	  				$item["content"]=$value["option_b"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="B"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
	  				$item["flag"]="C";
	  				$item["content"]=$value["option_c"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="C"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
  					$temp["name"]="听音选词";
  				}else if($value["typeid"]==1||$value["typeid"]==3){
  					$temp["type"]=1;
  					$item["flag"]="A";
	  				$item["content"]=$value["option_a"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="A"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
	  				$item["flag"]="B";
	  				$item["content"]=$value["option_b"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="B"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
	  				$item["flag"]="C";
	  				$item["content"]=$value["option_c"];
	  				$item["iserror"]=1;
	  				if($value["answer"]=="C"){
	  					$item["iserror"]=0;
	  				}
	  				array_push($items, $item);
  					if($value["typeid"]==1){
  						$temp["encontent"]=$value["word"];
	  					$temp["cncontent"]=$value["explains"];
  					}else{
  						$temp["encontent"]=$value["explains"];
	  					$temp["cncontent"]=$value["explains"];
  					}
  					$temp["answer"]=$value["answer"];
  					$temp["name"]="英汉互译";
  				}else if($value["typeid"]==2){
  					$temp["type"]=3;
            $words=str_split($value["word"]);
            $uswords=explode(",", $value["useranswer"]);
            $usertemparr=$uswords;
            $activeindex=0;
            foreach($words as $wk=>$wv){
              if(!empty($uswords)&&$uswords[$wk]!=null&&$uswords[$wk]!='null'){
                $activeindex=$wk+1;
                $words[$wk]=$uswords[$wk];
              }else{
                $words[$wk]="";
              }
            }
            $temp["activeindex"]=$activeindex;
  					$temp["encontent"]=$words;
	  				$temp["cncontent"]=$value["explains"];
	  				$temp["answer"]=$value["word"];
            $temparr=str_split($value["word"]);
	  				$ansarr=explode(",", substr($value["option_a"], 0,strlen($value["option_a"])-1));
	  				foreach($ansarr as $keys=>$values){
              $item["flag"]="";
              $item["content"]=$values;
              $item["iserror"]=1;
              $item["ischoose"]=0;
              $item["inputword"]="";
              $status=array_search($values,$temparr);
              if($status!==false){
                unset($temparr[$status]);
                $item["iserror"]=0;
              }
              //是否是用户选择的数据
              $status=array_search($values,$usertemparr);
              if($status!==false){
                unset($usertemparr[$status]);
                $item["ischoose"]=1;
                $item["inputword"]=$status;
              }
	  					array_push($items, $item);
	  				}
  					$temp["name"]="单词拼写";
  				}
  				$temp["items"]=$items;
          if($value["typeid"]==0){
            array_push($listenarr, $temp);
          }else if($value["typeid"]==1||$value["typeid"]==3){
            array_push($transarr, $temp);
          }else if($value["typeid"]==2){
            array_push($spellarr, $temp);
          }
  			}

        if(!empty($spellarr)){
          foreach($spellarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($spellarr);
            array_push($questions, $value);
          }
        }

        if(!empty($listenarr)){
          foreach($listenarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($listenarr);
            array_push($questions, $value);
          }
        }
        
        if(!empty($transarr)){
          foreach($transarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($transarr);
            array_push($questions, $value);
          }
        }
        

  		}
  		//查询课文跟读
  		$sql="select s.id as userreadid,t.id,e.`cncontent`,e.`encontent`,e.`mp3`,e.id as textid,s.score,s.mp3 as usermp3 from engs_homework_text_read t left join engs_text e on t.`chapterid`=e.`chapterid` left join (select  *  from engs_homework_student_text_read where studentid = '".$studentid."' and homeworkid = ".$homeworkid.") s on t.`id` = s.text_readid AND e.id=s.textid where e.isdel=1 and t.`homeworkid` = ".$homeworkid." order by e.textsortid,e.id";
  		
      $tars=M()->query($sql);
  		if(!empty($tars)){
  			foreach($tars as $key=>$value){
  				unset($temp);
  				$temp["name"]="课文跟读";
  				$temp["id"]=$value["id"];
          $temp["userreadid"]=$value["userreadid"];
          $temp["score"]=$value["score"];
          $temp["contentid"]=$value["textid"];
  				$temp["index"]=$key+1;
          $temp["questype"]=0;
  				$temp["count"]=count($tars);
  				$temp["type"]=4;
  				$temp["pic"]="";
  				$temp["picbaseroot"]="";
  				$temp["typeid"]=0;
  				$temp["display"]=0;
	  			$temp["encontent"]=$value["encontent"];
	  			$temp["cncontent"]=$value["cncontent"];
	  			$temp["remark"]="";
	  			$ttss=array();
  				$tts["before"]="";
  				$tts["content"]=$value["word"];
  				$tts["mp3"]=$value["mp3"].".mp3";
          $tts["playmp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/".substr($value["mp3"],0,2)."/".$value["mp3"].".mp3";
  				$tts["stoptime"]="0";
  				array_push($ttss, $tts);
  				$temp["tts"]=$ttss;
	  			$temp["audiobaseroot"]=RESOURCE_DOMAIN."/yylmp3/mp3_text/";
	  			$temp["items"]=array();
	  			$temp["answer"]=array();
	  			array_push($questions, $temp);
  			}
  		}
  		//查询听力训练
  		$sql="SELECT st.id as stemid,st.parentid,t.itemtype,st.question_playtimes,st.content as stemcontent,st.parentid,t.id,t.`itemtype`,t.`typeid`,t.`tcontent`,t.`questions_playtimes`,t.`questions_answer`,t.`questions_items`,t.`questions_tts`,s.answer as useranswer FROM engs_exams_questions t left join engs_exams_stem st on t.stemid=st.id LEFT JOIN (SELECT * FROM engs_homework_student_quiz WHERE studentid='".$studentid."' AND homeworkid=".$homeworkid.") AS s ON t.id=s.questionsid WHERE st.isdel=1 and t.isdel=1 and t.`examsid` IN (SELECT examsid FROM engs_homework_quiz WHERE `homeworkid`=".$homeworkid.") order by t.paper_sortid,t.id";
  		$eqrs=M()->query($sql);
  		if(!empty($eqrs)){
  			foreach($eqrs as $key=>$value){
  				unset($temp);
  				$temp["name"]="听力训练";
  				$temp["id"]=$value["id"];
          $temp["contentid"]=$value["id"];
          $temp["itemtype"]=$value["itemtype"];
          $temp["question_playtimes"]=$value["question_playtimes"];
  				$temp["index"]=$key+1;
  				$temp["count"]=count($eqrs);
  				$temp["type"]=5;
          $temp["typeid"]=$value["typeid"];
          $temp["stemid"]=$value["stemid"];
          $temp["parentid"]=$value["parentid"];
          $temp["useranswer"]=$value["useranswer"];
  				$temp["pic"]="";
  				$temp["picbaseroot"]=PROTOCOL."://".C("pic_exams");
  				$temp["typeid"]=$value["typeid"];
  				$temp["display"]=$value["itemtype"];
	  			//$temp["encontent"]=$value["tcontent"];
          $temp["questype"]=0;
          //在这里替换题干内容
          if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"])){
            preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"],$match);
            $value["tcontent"]=str_replace($match[1],PROTOCOL."://".C('resource_domain')."/yylmp3/".$match[1],$value["tcontent"]);
            
          }
          $temp["encontent"]=$value["tcontent"];
          //进行替换
          $html=str_replace($match[1],"aaaaaa",$html);
	  			$temp["cncontent"]="";
          if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"])){
            preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"],$match);
            $value["stemcontent"]=str_replace($match[1],PROTOCOL."://".C('resource_domain')."/yylmp3/".$match[1],$value["stemcontent"]);
            
          }
          $temp["stemcontent"]=$value["stemcontent"];

          if($value["parentid"]!='null'&&$value["parentid"]!=0&&$value["parentid"]!=''){
            $childrs=$this->getParentStem($value["parentid"]);
            $temp["cncontent"]=$childrs[0]["content"];
            $temp["questype"]=1;
          }
	  			$temp["remark"]="";
	  			$temp["answer"]="";
	  			$questions_answer=(array)json_decode(urldecode($value["questions_answer"]));
          //$temp["answerss"]=$questions_answer;
				  $questions_tts=(array)json_decode(urldecode($value["questions_tts"]));
				  $questions_items=(array)json_decode(urldecode($value["questions_items"]));
				  $items=array();
  				foreach($questions_items as $keys=>$values){
  					$item["flag"]=$values->flag;
            if($temp["itemtype"]=='1'&&$value["typeid"]==1){
              $item["content"]=PROTOCOL."://".C("resource_path")."/uploads/".$values->content;
            }else{
              $item["content"]=$values->content;
            }
  					$item["iserror"]=0;
            if($value["typeid"]==1){
              if($item["flag"]==$questions_answer["answer"]){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }
            }else if($value["typeid"]==3){
              if($values->content=='True'){
                $item["content"]="√";
              }else{
                $item["content"]="×";
              }
              if($values->content=='True'&&$questions_answer["answer"]==1){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }else if($values->content=='False'&&$questions_answer["answer"]==0){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }
            }
  					array_push($items,$item);
  				}
  				$ttss=array();
  				foreach($questions_tts as $keys=>$values){
  					$tts["before"]=$values->flag_content;
    					$tts["content"]=$values->tts_content;
    					$tts["mp3"]=$values->tts_mp3.".mp3";
              $tts["playmp3"]=PROTOCOL."://".C("exams_mp3_path").substr($values->tts_mp3,0,2)."/".$values->tts_mp3.".mp3";
    					$tts["stoptime"]=$values->tts_stoptime;
  					 array_push($ttss,$tts);
  				}
  				$temp["tts"]=$ttss;
	  			$temp["audiobaseroot"]=C("exams_mp3_path");
	  			$temp["items"]=$items;
	  			array_push($questions, $temp);
  			}
  		}
  		$this->ajaxReturn($questions);
  	}

    private function getParentStem($parentid){
      //组合试题的问题
      $sql="select * from engs_exams_stem t where  t.id =".$parentid;
      $childrs=M()->query($sql);
      return $childrs;
    }


  //保存答案
  public function saveAnswer(){
    $type=I("type/d");
    $iserror=I("iserror");
    // if($iserror==0){
    //   $iserror=1;
    // }else{
    //   $iserror=0;
    // }
    $id=I("id");
    $answer=I("answer");
    $homeworkid=I("homeworkid");
    $studentid=I("studentid");
    $classid=I("classid");
    $contentid=I("contentid");
    $score=I("score");
    $typeid=I("typeid");
    $curtime=Date("Y-m-d H:i:s");
    //查询用户上次跟新时间
    $studentlist=M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->find();
    $lastupdatetime= $studentlist["lastupdatetime"];
    $starttime=strtotime($curtime);
    $endtime=strtotime($lastupdatetime);
    $time=$starttime-$endtime;
    //跟新上次跟新时间
    M("homework_student_list")->lastupdatetime=$curtime;
    M("homework_student_list")->papertime=$studentlist["papertime"]+$time;
    M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->save();
    //通过type判断是那种作业
    if($type==0){

    }else if($type==1||$type==2||$type==3){
      if($iserror==0){
        $iserror=1;
      }else{
        $iserror=0;
      }
      //英汉互译的问题提交
      $student_word_evaluat=M("homework_student_word_evaluat");
      $rs=$student_word_evaluat->where("studentid='%s' and classid='%s' and homeworkid=%d and word_evaluatid=%d",$studentid,$classid,$homeworkid,$id)->find();
      if(empty($rs)){
        $student_word_evaluat->studentid=$studentid;
        $student_word_evaluat->classid=$classid;
        $student_word_evaluat->areaid=$areaid;
        $student_word_evaluat->homeworkid=$homeworkid;
        $student_word_evaluat->word_evaluatid=$id;
        $student_word_evaluat->wordid=$contentid;
        $student_word_evaluat->do_time=Date("Y-m-d H:i:s");
        $student_word_evaluat->type=$typeid;
        $student_word_evaluat->answer=$answer;
        if($typeid==2){
          $useranswer=explode(",", $answer);
          $useranswer=implode("", $useranswer);
          //查询这个试题的答案
          $wordrs=M("word")->where("id=%d",$contentid)->find();
          if($useranswer==$wordrs["word"]){
            $iserror=1;
          }else{
            $iserror=0;
          }
          $student_word_evaluat->iscorrect=$iserror;
        }else{
          $student_word_evaluat->iscorrect=$iserror;
        }
        if($iserror==0){
          $student_word_evaluat->score=0;
        }else{
          $student_word_evaluat->score=$score;
        }
        $student_word_evaluat->iscorrect=$iserror;
        $student_word_evaluat->spendtime=$time;
        $student_word_evaluat->add();
      }else{
        $student_word_evaluat->do_time=Date("Y-m-d H:i:s");
        $student_word_evaluat->type=$typeid;
        
        $student_word_evaluat->answer=$answer;
        if($typeid==2){
          $useranswer=explode(",", $answer);
          $useranswer=implode("", $useranswer);
          //查询这个试题的答案
          $wordrs=M("word")->where("id=%d",$contentid)->find();
          if($useranswer==$wordrs["word"]){
            $iserror=1;
          }else{
            $iserror=0;
          }
          $student_word_evaluat->iscorrect=$iserror;
        }else{
          $student_word_evaluat->iscorrect=$iserror;
        }

        if($iserror==0){
          $student_word_evaluat->score=0;
        }else{
          $student_word_evaluat->score=$score;
        }
        
        $student_word_evaluat->spendtime=$rs["spendtime"]+$time;
        $student_word_evaluat->where("id=%d",$rs["id"])->save();
      }
    }else if($type==5||$type==6){
      $student_quiz=M("homework_student_quiz");
      $rs=$student_quiz->where("studentid='%s' and classid='%s' and homeworkid=%d and questionsid=%d",$studentid,$classid,$homeworkid,$id)->find();
      if(empty($rs)){
        //查询作业的数据
        $quizrs=M("homework_quiz")->where("homeworkid=%d",$homeworkid)->find();
        $student_quiz->studentid=$studentid;
        $student_quiz->classid=$classid;
        $student_quiz->areaid=$areaid;
        $student_quiz->homeworkid=$homeworkid;
        $student_quiz->quizid=$quizrs["id"];
        $student_quiz->examid=$quizrs["examsid"];
        $student_quiz->questionsid=$contentid;
        $student_quiz->do_time=Date("Y-m-d H:i:s");
        $stemrs=M("exams_questions")->where("id=%d",$contentid)->find();
        if($iserror==1){
          //查询试题的分数
          //$sql="SELECT engs_exams_stem.`question_score` FROM engs_exams_questions LEFT JOIN engs_exams_stem ON engs_exams_questions.`stemid`=engs_exams_stem.`id` WHERE engs_exams_questions.id=%d";
          $stem=M("exams_stem")->where("id=%d",$stemrs["stemid"])->find();
          $student_quiz->score=$stem["question_score"];
        }else{
          $student_quiz->score=0;
        }
        
        $student_quiz->answer=$answer;
        //查询这个试题的answerid
        
        //$questions_answer=(array)json_decode(urldecode($answerrs["questions_answer"]));
        //查询答案的数据
        $answerrs=M("exams_questions_answer")->where("isdel=1 and questionsid=%d",$contentid)->find();
        $student_quiz->answerid=$answerrs["id"];
        $student_quiz->iscorrect=$iserror;
        $student_quiz->spendtime=$time;
        $student_quiz->add();
      }else{
        $stemrs=M("exams_questions")->where("id=%d",$contentid)->find();
        $student_quiz->do_time=Date("Y-m-d H:i:s");
        if($iserror==1){
          $stem=M("exams_stem")->where("id=%d",$stemrs["stemid"])->find();
          $student_quiz->score=$stem["question_score"];
          $student_quiz->score=$score;
        }else{
          $student_quiz->score=0;
        }
        $student_quiz->answer=$answer;
        $student_quiz->iscorrect=$iserror;
        $student_quiz->spendtime=$rs["spendtime"]+$time;
        $student_quiz->where("id=%d",$rs["id"])->save();
      }
    }
  }

  //获取结果的列表
  public function getUserQuestionCard(){
      $homeworkid=I("homeworkid/d");
      $studentid=I("username/s");
      $iserror=I("iserror");
      $questions=array();
      //查询单词跟读
      $sql="select t.id,s.score as score from engs_homework_word_read t left join (select * from engs_homework_student_word_read where studentid = '".$studentid."'  and homeworkid = ".$homeworkid.") s on t.`id` = s.word_readid where t.`homeworkid` = ".$homeworkid." order by id";
      if($iserror==1){
        $sql=$sql." and (s.id is null or s.score<50)";
      }
      
      $wars=M()->query($sql);
      if(!empty($wars)){
        unset($temp);
        $temp["name"]="单词跟读";
        $temp["type"]="0";
        $temp["num"]=count($wars);
        $temp["questions"]=$wars;
        array_push($questions, $temp);
      }
      
      //查询单词测试
      $sql="SELECT t.id,s.score as score,t.`typeid`,s.iscorrect FROM engs_homework_word_evaluat t LEFT JOIN (SELECT * FROM engs_homework_student_word_evaluat WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.word_evaluatid WHERE t.`homeworkid` = ".$homeworkid;
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score=0)";
      }
      $sql=$sql." ORDER BY t.sortid,t.id";
      $wtrs=M()->query($sql);
      if(!empty($wtrs)){
        $transarr=array();
        $listenarr=array();
        $spellarr=array();
        unset($temp);
        foreach($wtrs as $key=>$value){
          $temp["id"]=$value["id"];
          $temp["score"]=$value["score"];
          if($value["typeid"]==0){
            array_push($listenarr, $temp);
          }else if($value["typeid"]==1||$value["typeid"]==3){
            array_push($transarr, $temp);
          }else if($value["typeid"]==2){
            array_push($spellarr, $temp);
          }
        }

        if(count($spellarr)>0){
          unset($temp);
          $temp["name"]="单词拼写";
          $temp["type"]="1";
          $temp["num"]=count($spellarr);
          $temp["questions"]=$spellarr;
          array_push($questions, $temp);
        }
        
        
        
        if(count($listenarr)>0){
          unset($temp);
          $temp["name"]="听音选词";
          $temp["type"]="1";
          $temp["num"]=count($listenarr);
          $temp["questions"]=$listenarr;
          array_push($questions, $temp);
        }

        if(count($transarr)>0){
          unset($temp);
          $temp["name"]="英汉互译";
          $temp["type"]="1";
          $temp["num"]=count($transarr);
          $temp["questions"]=$transarr;
          array_push($questions, $temp);
        }
      }
      //查询课文跟读
      //$sql="select t.id,s.id as score from engs_homework_text_read t left join engs_text e on t.`chapterid`=e.`chapterid` left join (select  *  from engs_homework_student_text_read where studentid = '".$studentid."' and homeworkid = ".$homeworkid.") s on t.`id` = s.text_readid AND e.id=s.textid where e.isdel=1 and t.`homeworkid` = ".$homeworkid." order by e.textsortid";
      $sql="select t.id,e.encontent,e.cncontent,e.mp3,s.score as score from engs_homework_text_read t left join engs_text e on t.`chapterid`=e.`chapterid` left join (select  *  from engs_homework_student_text_read where studentid = '".$studentid."' and homeworkid = ".$homeworkid.") s on t.`id` = s.text_readid AND e.id=s.textid where e.isdel=1 and t.`homeworkid` = ".$homeworkid;
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score<50)";
      }
      $sql=$sql." order by e.textsortid,e.id";
      $tars=M()->query($sql);
      if(!empty($tars)){
        unset($temp);
        $temp["name"]="课文跟读";
        $temp["type"]="0";
        $temp["num"]=count($tars);
        $temp["questions"]=$tars;
        array_push($questions, $temp);
      }
      //查询听力训练
      $sql="SELECT t.id,s.score as score,s.iscorrect FROM engs_exams_questions t LEFT JOIN (SELECT * FROM engs_homework_student_quiz WHERE studentid='".$studentid."' AND homeworkid=".$homeworkid.") AS s ON t.id=s.questionsid WHERE t.isdel=1 and t.`examsid` IN (SELECT examsid FROM engs_homework_quiz WHERE `homeworkid`=".$homeworkid.") ";
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score=0)";
      }
      $sql=$sql." order by t.paper_sortid,t.id";
      $eqrs=M()->query($sql);
      if(!empty($eqrs)){
        unset($temp);
        $temp["name"]="听力训练";
        $temp["num"]=count($eqrs);
        $temp["type"]="1";
        $temp["questions"]=$eqrs;
        array_push($questions, $temp);
      }
      $this->ajaxReturn($questions);
    }

    //试卷完成的页面
    public function getHomeworkFinishQuestions(){
      $homeworkid=I("homeworkid/d");
      $studentid=I("username/s");
      $classid=I("classid");
      $iserror=I("iserror");
      $questions=array();
      //查询单词跟读
      $sql="select s.id as userreadid,t.id,t.`wordid`,w.`word`,p.`explains`,p.`pic`,p.`morphology`,s.score,s.mp3,(CASE WHEN b.`ukmark` IS NULL THEN '' ELSE b.ukmark END) AS ukmark,b.`ukmp3` from engs_homework_word_read t LEFT JOIN engs_word w ON t.`wordid`=w.`id` LEFT JOIN engs_base_word_explains p ON w.`base_explainsid`=p.`id` LEFT JOIN engs_base_word b ON w.base_wordid=b.id left join (select * from engs_homework_student_word_read where studentid = '".$studentid."'  and homeworkid = ".$homeworkid.") s on t.`id` = s.word_readid where t.`homeworkid` = ".$homeworkid;
      if($iserror==1){
        $sql=$sql." and (s.id is null or s.score<50)";
      }
      $sql=$sql." order by t.id";
      $wars=M()->query($sql);
      if(!empty($wars)){
        foreach($wars as $key=>$value){
          unset($temp);
          $temp["name"]="单词跟读";
          $temp["userreadid"]=$value["userreadid"];
          $temp["questype"]=0;
          $temp["id"]=$value["id"];
          $temp["mp3"]=$value["mp3"];
          $temp["contentid"]=$value["wordid"];
          $temp["pic"]=$value["pic"];
          $temp["picbaseroot"]=PROTOCOL."://".C("word_pic_path");
          $temp["index"]=$key+1;
          $temp["count"]=count($wars);
          $temp["type"]=0;
          $temp["typeid"]=0;
          $temp["display"]=0;
          $temp["encontent"]=$value["word"];
          $temp["cncontent"]=$value["explains"];
          if(empty($value["ukmark"])){
            $temp["remark"]="";
          }else{
            $temp["remark"]=$value["ukmark"];
          }
          $ttss=array();
          $tts["before"]="";
          $tts["content"]=$value["word"];
          $tts["mp3"]=$value["ukmp3"];
          $tts["playmp3"]=C("word_mp3_path").$value["ukmp3"];
          $tts["stoptime"]="0";
          array_push($ttss, $tts);
          $temp["tts"]=$ttss;
          $temp["audiobaseroot"]=PROTOCOL."://".C("word_mp3_path");
          $temp["items"]=array();
          $temp["answer"]=array();
          $temp["score"]=empty($value["score"])?0:$value["score"];
          //查询这个试题的所有的做试题的人
          $ssql="select count(*) as num,sum(case when score>60 then 1 else 0 end) as passnum,max(score) as maxscore from engs_homework_student_word_read where classid = '".$classid."'  and homeworkid = ".$homeworkid." and word_readid=".$value["id"];
          $rss=M()->query($ssql);
          $temp["num"]=0;
          $temp["passrate"]="0%";
          $temp["maxscore"]="0";
          if(!empty($rss[0]["num"])&&$rss[0]["num"]!=0){
            $temp["num"]=$rss[0]["num"];
            $temp["maxscore"]=$rss[0]["maxscore"];
            $temp["passrate"]=round($rss[0]["passnum"]*100/$rss[0]["num"])."%";
          }
          array_push($questions, $temp);
        }
      }
      //查询单词测试
      $sql="SELECT s.iscorrect,t.id,p.pic,t.`homeworkid`,t.`answer`,t.`option_a`,t.`option_b`,t.`option_c`,t.`wordid`,t.`typeid`,w.`word`,p.`explains`,b.`ukmp3`,s.answer as useranswer from engs_homework_word_evaluat t  LEFT JOIN engs_word w ON t.`wordid`=w.`id` LEFT JOIN engs_base_word_explains p ON w.`base_explainsid`=p.`id` left join engs_base_word b on w.`base_wordid`=b.`id` LEFT JOIN (SELECT * FROM engs_homework_student_word_evaluat WHERE studentid = '".$studentid."'  AND homeworkid = ".$homeworkid.") s ON t.`id` = s.word_evaluatid WHERE t.`homeworkid` = ".$homeworkid;
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score=0)";
      }
      $sql=$sql." ORDER BY t.sortid,t.id";
      $wtrs=M()->query($sql);
      if(!empty($wtrs)){
        $listenarr=array();
        $spellarr=array();
        $transarr=array();
        foreach($wtrs as $key=>$value){
          unset($temp);
          $temp["id"]=$value["id"];
          $temp["questype"]=0;
          $temp["iscorrect"]=$value["iscorrect"];
          $temp["contentid"]=$value["wordid"];
          $temp["useranswer"]=$value["useranswer"];
          $temp["useranswer"]=implode("",explode(",", $value["useranswer"]));
          
          $temp["remark"]="";
          $temp["pic"]=$value["pic"];
          $temp["picbaseroot"]=PROTOCOL."://".C("word_pic_path");
          $temp["index"]=$key+1;
          $temp["count"]=count($wtrs);
          $temp["typeid"]=$value["typeid"];
          $temp["display"]=0;
          $ttss=array();
          $tts["before"]="";
          $tts["content"]=$value["word"];
          $tts["mp3"]=$value["ukmp3"];
          $tts["playmp3"]=C("word_mp3_path").$value["ukmp3"];
          $tts["stoptime"]="0";
          array_push($ttss, $tts);
          $temp["tts"]=$ttss;
          $temp["audiobaseroot"]=PROTOCOL."://".C("word_mp3_path");
          $items=array();
          if($value["typeid"]==0){
            $temp["type"]=2;
            $temp["encontent"]=$value["ukmp3"];
            $temp["cncontent"]=$value["explains"];
            $temp["answer"]=$value["answer"];
            $item["flag"]="A";
            $item["content"]=$value["option_a"];
            $item["iserror"]=1;
            if($value["answer"]=="A"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            $item["flag"]="B";
            $item["content"]=$value["option_b"];
            $item["iserror"]=1;
            if($value["answer"]=="B"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            $item["flag"]="C";
            $item["content"]=$value["option_c"];
            $item["iserror"]=1;
            if($value["answer"]=="C"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            $temp["name"]="听音选词";
          }else if($value["typeid"]==1||$value["typeid"]==3){
            $temp["type"]=1;
            $item["flag"]="A";
            $item["content"]=$value["option_a"];
            $item["iserror"]=1;
            if($value["answer"]=="A"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            $item["flag"]="B";
            $item["content"]=$value["option_b"];
            $item["iserror"]=1;
            if($value["answer"]=="B"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            $item["flag"]="C";
            $item["content"]=$value["option_c"];
            $item["iserror"]=1;
            if($value["answer"]=="C"){
              $item["iserror"]=0;
            }
            array_push($items, $item);
            if($value["typeid"]==1){
              $temp["encontent"]=$value["word"];
              $temp["cncontent"]=$value["explains"];
            }else{
              $temp["encontent"]=$value["explains"];
              $temp["cncontent"]=$value["explains"];
            }
            $temp["answer"]=$value["answer"];
            $temp["name"]="英汉互译";
          }else if($value["typeid"]==2){
            $temp["type"]=3;
            $words=str_split($value["word"]);
            $uswords=explode(",", $value["useranswer"]);
            $usertemparr=$uswords;
            $activeindex=0;
            foreach($words as $wk=>$wv){
              if(!empty($uswords)&&$uswords[$wk]!=null&&$uswords[$wk]!='null'){
                $activeindex=$wk+1;
                $words[$wk]=$uswords[$wk];
              }else{
                $words[$wk]="";
              }
            }
            $temp["activeindex"]=$activeindex;
            $temp["encontent"]=$words;
            $temp["cncontent"]=$value["explains"];
            $temp["answer"]=$value["word"];
            $temparr=str_split($value["word"]);
            $ansarr=explode(",", substr($value["option_a"], 0,strlen($value["option_a"])-1));
            foreach($ansarr as $keys=>$values){
              $item["flag"]="";
              $item["content"]=$values;
              $item["iserror"]=1;
              $item["ischoose"]=0;
              $item["inputword"]="";
              $status=array_search($values,$temparr);
              if($status!==false){
                unset($temparr[$status]);
                $item["iserror"]=0;
              }
              //是否是用户选择的数据
              $status=array_search($values,$usertemparr);
              if($status!==false){
                unset($usertemparr[$status]);
                $item["ischoose"]=1;
                $item["inputword"]=$status;
              }
              array_push($items, $item);
            }
            $temp["name"]="单词拼写";
          }
          $temp["items"]=$items;
          $answera=array();
          if($value["typeid"]!=2){
            //班级汇总数据作答情况
            $sumsql="select count(*) as answernum,sum(case when iscorrect='1' then 1 else 0 end) as accnum,t.answer from engs_homework_student_word_evaluat t where t.issubmit=1 and t.homeworkid=".$homeworkid." and t.classid='".$classid."' and word_evaluatid=".$value['id']." group by t.homeworkid,t.classid,t.answer";
            $sumrs=M()->query($sumsql);
            $summarydata=0;
            $arrnum=0;
            $erroranswer="";
            $max=0;
            $usersanswer=$value['answer'];
            foreach($sumrs as $keyrs=>$valuers){
              $userquestionsanswer=$valuers["answer"];
              $summarydata=$summarydata+$valuers["answernum"];
              $arrnum=$arrnum+$valuers["accnum"];
              $errrate=100-round($valuers["accnum"]*100/$valuers["answernum"]);
              if($max<=$errrate&&$userquestionsanswer!=$usersanswer){
                $max=$errrate;
                $erroranswer=$valuers["answer"];
              }
            }
            if(empty($summarydata)||$summarydata==0){
              $errorrate="0%";
            }else{
              $errorrate=round($arrnum*100/$summarydata)."%";
            }
            //$errorrate=round($arrnum*100/$summarydata)."%";
            $answera["accrate"]=$errorrate;
            $answera["answernum"]=$summarydata;
            $answera["erroranswer"]=$erroranswer;
            //学生作答的汇总数据
            $summarr=array("A"=>0,"B"=>0,"C"=>0);
            $summarysql="select t.answer,count(*) as per from engs_homework_student_word_evaluat t where t.issubmit=1 and t.classid='".$classid."' and t.homeworkid=".$homeworkid." and t.word_evaluatid=".$value["id"]."  group by t.answer";
            $summaryrs=M()->query($summarysql);
            foreach($summaryrs as $key=>$valuesumm){
              if($valuesumm["answer"]=='A'){
                $summarr["A"]=($valuesumm["per"]/$summarydata)*100;
              }else if($valuesumm["answer"]=='B'){
                $summarr["B"]=($valuesumm["per"]/$summarydata)*100;
              }else if($valuesumm["answer"]=='C'){
                $summarr["C"]=($valuesumm["per"]/$summarydata)*100;
              }
            }
          }else{
            $sumsql="select count(*) as answernum,sum(case when iscorrect='1' then 1 else 0 end) as accnum,t.answer from engs_homework_student_word_evaluat t where t.issubmit=1 and t.homeworkid=".$homeworkid." and t.classid='".$classid."' and word_evaluatid=".$value['id']." group by t.homeworkid,t.classid,t.answer";
            //echo $sumsql;exit;
            $sumrs=M()->query($sumsql);
            $summarydata=0;
            $arrnum=0;
            $erroranswer="";
            $max=0;
            foreach($sumrs as $keyrsz=>$valuersz){
              $userquestionsanswer=$valuersz["answer"];
              $summarydata=$summarydata+$valuersz["answernum"];
              $arrnum=$arrnum+$valuersz["accnum"];
              $errrate=100-round($valuersz["accnum"]*100/$valuersz["answernum"]);
              $usersanswer=$value['answer'];
              $userquestionsanswer=explode(",", $userquestionsanswer);
              $userquestionsanswer=implode("",$userquestionsanswer);
              if($max<=$errrate&&$userquestionsanswer!=$usersanswer){
                $max=$errrate;
                $erroranswer=$valuersz["answer"];
              }
            }
            $errorrate=round($arrnum*100/$summarydata)."%";
            if(empty($summarydata)||$summarydata==0){
              $errorrate="0%";
            }
            $answera["accrate"]=$errorrate;
            $answera["answernum"]=$summarydata;
            $erroranswer=explode(",", $erroranswer);
            $erroranswer=implode("", $erroranswer);
            $answera["erroranswer"]=$erroranswer;
          }
          $temp["itemssummary"]=$summarr;
          $temp["answersummary"]=$answera;
          if($value["typeid"]==0){
            array_push($listenarr, $temp);
          }else if($value["typeid"]==1||$value["typeid"]==3){
            array_push($transarr, $temp);
          }else if($value["typeid"]==2){
            array_push($spellarr, $temp);
          }
        }

        if(!empty($spellarr)){
          foreach($spellarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($spellarr);
            array_push($questions, $value);
          }
        }


        if(!empty($listenarr)){
          foreach($listenarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($listenarr);
            array_push($questions, $value);
          }
        }
      
        
        if(!empty($transarr)){
          foreach($transarr as $key=>$value){
            $value["index"]=$key+1;
            $value["count"]=count($transarr);
            array_push($questions, $value);
          }
        }

      }
      //查询课文跟读
      $sql="select s.id as userreadid,t.id,e.`cncontent`,e.`encontent`,e.`mp3`,e.id as textid,s.score,s.mp3 as usermp3 from engs_homework_text_read t left join engs_text e on t.`chapterid`=e.`chapterid` left join (select  *  from engs_homework_student_text_read where studentid = '".$studentid."' and homeworkid = ".$homeworkid.") s on t.`id` = s.text_readid AND e.id=s.textid where e.isdel=1 and t.`homeworkid` = ".$homeworkid;
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score<50)";
      }
      $sql=$sql." order by e.textsortid,t.id";
      $tars=M()->query($sql);
      if(!empty($tars)){
        foreach($tars as $key=>$value){
          unset($temp);
          $temp["name"]="课文跟读";
          $temp["userreadid"]=$value["userreadid"];
          $temp["id"]=$value["id"];
          $temp["questype"]=0;
          $temp["contentid"]=$value["textid"];
          $temp["index"]=$key+1;
          $temp["mp3"]=$value["usermp3"];
          $temp["score"]=$value["score"];
          $temp["count"]=count($tars);
          $temp["type"]=4;
          $temp["pic"]="";
          $temp["picbaseroot"]="";
          $temp["typeid"]=0;
          $temp["display"]=0;
          $temp["encontent"]=$value["encontent"];
          $temp["cncontent"]=$value["cncontent"];
          $temp["remark"]="";
          $ttss=array();
          $tts["before"]="";
          $tts["content"]=$value["word"];
          $tts["mp3"]=$value["mp3"].".mp3";
          $tts["playmp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/".substr($value["mp3"],0,2)."/".$value["mp3"].".mp3";
          $tts["stoptime"]="0";
          array_push($ttss, $tts);
          $temp["tts"]=$ttss;
          $temp["audiobaseroot"]=RESOURCE_DOMAIN."/yylmp3/mp3_text/";
          $temp["items"]=array();
          $temp["answer"]=array();
          //查询这个试题的所有的做试题的人
          $ssql="select count(*) as num,sum(case when score>60 then 1 else 0 end) as passnum,max(score) as maxscore from engs_homework_student_text_read where classid = '".$classid."'  and homeworkid = ".$homeworkid." and textid=".$value["textid"];
          $rss=M()->query($ssql);
          $temp["num"]=0;
          $temp["passrate"]="0%";
          $temp["maxscore"]="0";
          if(!empty($rss[0]["num"])){
            $temp["num"]=$rss[0]["num"];
            $temp["maxscore"]=$rss[0]["maxscore"];
            $temp["passrate"]=round($rss[0]["passnum"]*100/$rss[0]["num"])."%";
          }
          array_push($questions, $temp);
        }
      }
      //查询听力训练
      $sql="SELECT st.id as stemid,st.parentid,t.itemtype,st.question_playtimes,st.content as stemcontent,st.parentid,t.id,t.`itemtype`,t.`typeid`,t.`tcontent`,t.`questions_playtimes`,t.`questions_answer`,t.`questions_items`,t.`questions_tts`,s.answer as useranswer,t.examsid FROM engs_exams_questions t left join engs_exams_stem st on t.stemid=st.id LEFT JOIN (SELECT * FROM engs_homework_student_quiz WHERE studentid='".$studentid."' AND homeworkid=".$homeworkid.") AS s ON t.id=s.questionsid WHERE st.isdel=1 and t.isdel=1 and t.`examsid` IN (SELECT examsid FROM engs_homework_quiz WHERE `homeworkid`=".$homeworkid.")";
      if($iserror==1){
        $sql=$sql." and (s.score is null or s.score=0)";
      }
      $sql=$sql." order by t.paper_sortid,t.id";
      $eqrs=M()->query($sql);
      if(!empty($eqrs)){
        foreach($eqrs as $key=>$value){
          unset($temp);
          $temp["name"]="听力训练";
          $temp["id"]=$value["id"];
          $temp["contentid"]=$value["id"];
          $temp["itemtype"]=$value["itemtype"];
          $temp["question_playtimes"]=$value["question_playtimes"];
          $temp["index"]=$key+1;
          $temp["count"]=count($eqrs);
          $temp["type"]=5;
          $temp["typeid"]=$value["typeid"];
          $temp["stemid"]=$value["stemid"];
          $temp["parentid"]=$value["parentid"];
          $temp["useranswer"]=$value["useranswer"];
          $temp["pic"]="";
          $temp["picbaseroot"]=C("pic_exams");
          $temp["typeid"]=$value["typeid"];
          $temp["display"]=$value["itemtype"];
          //$temp["encontent"]=$value["tcontent"];
          //在这里替换题干内容
          if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"])){
            preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"],$match);
            $value["tcontent"]=str_replace($match[1],PROTOCOL."://".C('resource_domain')."/yylmp3/".$match[1],$value["tcontent"]);
          }
          $temp["encontent"]=$value["tcontent"];
          //进行替换
          $html=str_replace($match[1],"aaaaaa",$html);
          $temp["cncontent"]="";
          if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"])){
            preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"],$match);
            $value["stemcontent"]=str_replace($match[1],PROTOCOL."://".C('resource_domain')."/yylmp3/".$match[1],$value["stemcontent"]);
            
          }
          $temp["stemcontent"]=$value["stemcontent"];
          $temp["questype"]=0;
          if($value["parentid"]!='null'&&$value["parentid"]!=0&&$value["parentid"]!=''){
            $childrs=$this->getParentStem($value["parentid"]);
            $temp["cncontent"]=$childrs[0]["content"];
            $temp["questype"]=1;
          }
          $temp["remark"]="";
          $temp["answer"]="";
          $questions_answer=(array)json_decode(urldecode($value["questions_answer"]));
          //$temp["answerss"]=$questions_answer;
          $questions_tts=(array)json_decode(urldecode($value["questions_tts"]));
          $questions_items=(array)json_decode(urldecode($value["questions_items"]));
          $items=array();
          foreach($questions_items as $keys=>$values){
            $item["flag"]=$values->flag;
            if($temp["itemtype"]=='1'&&$value["typeid"]==1){
              $item["content"]=PROTOCOL."://".C("resource_path")."/uploads/".$values->content;
            }else{
              $item["content"]=$values->content;
            }
            $item["iserror"]=0;
            if($value["typeid"]==1){
              if($item["flag"]==$questions_answer["answer"]){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }
            }else if($value["typeid"]==3){
              if($values->content=='True'){
                $item["content"]="√";
              }else{
                $item["content"]="×";
              }
              
              if($values->content=='True'&&$questions_answer["answer"]==1){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }else if($values->content=='False'&&$questions_answer["answer"]==0){
                $item["iserror"]=1;
                $temp["answer"]=$values->flag;
              }
            }
            array_push($items,$item);
          }
          $ttss=array();
          foreach($questions_tts as $keys=>$values){
            $tts["before"]=$values->flag_content;
              $tts["content"]=$values->tts_content;
              $tts["mp3"]=$values->tts_mp3.".mp3";
              $tts["playmp3"]=PROTOCOL."://".C("exams_mp3_path").substr($values->tts_mp3,0,2)."/".$values->tts_mp3.".mp3";
              $tts["stoptime"]=$values->tts_stoptime;
             array_push($ttss,$tts);
          }
          $temp["tts"]=$ttss;
          $temp["audiobaseroot"]=C("exams_mp3_path");
          $temp["items"]=$items;
          //这里查询需要优化
          $questionratesql="SELECT answerid,answer,SUM(CASE WHEN iscorrect = 1 THEN 1 ELSE 0  END) AS accnum,COUNT(DISTINCT studentid) AS answernum  FROM engs_homework_student_quiz where issubmit=1 and homeworkid=".$homeworkid." and ";
          $questionratesql=$questionratesql."classid='".$classid."' and examid=".$value["examsid"]." and questionsid=".$value["id"]." GROUP BY answerid,answer ORDER BY accnum asc";
          $questionraters=M()->query($questionratesql);
          $accnumtemp=0;
          $answernumtemp=0;
          $maxerrorrate=0;
          $maxerroranswer="";
          foreach($questionraters as $k=>$v){
            $accnumtemp=$accnumtemp+$v["accnum"];
            $answernumtemp=$answernumtemp+$v["answernum"];
            if(($v["answernum"]-$v["accnum"])>=$maxerrorrate&&$temp["answer"]!=$v["answer"]){
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
          $temp["answersummary"]=$data;
          //学生作答的汇总数据
          $summarysql="select t.answer,count(*) as per from engs_homework_student_quiz t where t.issubmit=1 and t.classid='".$classid."' and t.homeworkid=".$homeworkid." and t.questionsid=".$value["id"]." group by t.answer";
          $summaryrs=M()->query($summarysql);
          $summarr=array("A"=>0,"B"=>0,"C"=>0);
          foreach($summaryrs as $key=>$value){
            if($value["answer"]=='A'){
              $summarr["A"]=($value["per"]/$answernumtemp)*100;
            }else if($value["answer"]=='B'){
              $summarr["B"]=($value["per"]/$answernumtemp)*100;
            }else if($value["answer"]=='C'){
              $summarr["C"]=($value["per"]/$answernumtemp)*100;
            }
          }
          $temp["itemsummary"]=$summarr;
          array_push($questions, $temp);
        }
      }
      $this->ajaxReturn($questions);
    }


    //班级排名
    public function rank(){
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
      //进行排序
      $classnum=1;
      if(count($publishstudents)==1){
        $classnum=0;
      }
      $classname=$publishstudents[$classid]["classMame"];
      $students=$publishstudents[$classid]["students"];
      $ranstudents=array();
      foreach($students as $key=>$value){
        $homeworksubmittime=$value["time"];
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
        $value["time"]=$homeworksubmittime;
        if($value["issubmit"]==1){
          $ranstudents[$key]=$value;
        }
      }
      //给二维数组进行排序
      $students=$this->multi_array_sort($students,"score",SORT_DESC);
      //var_dump($students);exit;
      $this->assign("students",$students);
      $this->assign("homeworkid",$homeworkid);
      $this->assign("homeworkname",$homeworkrs["paper_name"]);
      $this->assign("studentid",$studentid);
      $this->display("rank");
    }

    private function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){ 
      if(is_array($multi_array)){ 
        foreach ($multi_array as $row_array){ 
          if(is_array($row_array)){ 
            $key_array[] = $row_array[$sort_key]; 
          }else{ 
            return false; 
          } 
        } 
      }else{ 
        return false; 
      } 
      array_multisort($key_array,$sort,$multi_array); 
      return $multi_array; 
    }

}
