<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 网上作业控制器类
*/

class ListenController extends Controller {
//单项预览
    public function index(){
        $examsid = I('examsid/d',0);
        $type = I('type/d',0);
        $rs=M("exams")->field('id,name,quescount')->where("id=%s and isdel=1",$examsid)->find();
        $this->assign("eqids",$examsid);
        $this->assign("exams_name",$rs['name']);
        $this->display('index');
    }
//听力训练预览
    public function preview_exams(){
        $examsid=I("ids");
        $studentid=session("username");
        $classid=session("classid");
        $rs=$this->get_exams(0,$studentid,$classid,$examsid,0,1,0);
        $this->ajaxReturn($rs);
    }
    //学生做作业
     public function student_exams(){
        //var_dump($_COOKIE);exit;
     	  getuserinfos(1);
        $examsid=I("examsid");
        $usertype = cookie('usertype');
        $username = cookie('username');
        $username = str_replace('jz','',$username);
        $tms = cookie("tms_url");
       // echo $username;exit;
        $portal = cookie("portal_url");

        $classid= getClassId($tms,$username);

        cookie("classid",$classid);
        //echo $usertype;exit;
        $submittime=Date("Y-m-d H:i:s");
        if($usertype != 0 && $usertype != 4){    ////老师
           $url="Location:index?examsid=".$examsid;
            header($url);
        }
    
        session("examsid",$examsid);
   
        
     
        session("starttime",time());
        session("username",$username);
        session("classid",$classid);
        
        $this->assign('classid',$classid);
        $this->assign('username',$username);
        $this->assign('examsid',$examsid);
        $this->assign('main',$portal);
        $this->assign('yjt_homeworkid',$yjt_homeworkid);
        $this->assign('tms',$tms);


        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');

        $rs=M("exams")->field('id,name,quescount')->where("id=%s and isdel=1",$examsid)->find();
        $this->assign("eqids",$examsid);
        $this->assign("exams_name",$rs['name']);

        $this ->assign('eqcardtitle',$arraynum[$cardnum].'听力训练');
        $this->assign("wacount",0);
        $this->assign("wscount",0);
        $this->assign("wrcount",0);
        $this->assign("wccount",0);
        $this->assign("tacount",0);
        $this->assign("eqcount",1);

        $sql="select * from engs_exams_student_list where engs_exams_student_list.paper_id='".$examsid."' and engs_exams_student_list.studentid='".$username."' and engs_exams_student_list.classid= '".$classid."' and  engs_exams_student_list.dotime is not null";
        $srs=M()->query($sql);
        if(empty($srs)){
    			if($usertype != 0){
    				//echo $usertype;exit;
	    			if($username!=''&&$username!='null'){
	    					M("exams_student_list")->studentid=$username;
	    					M("exams_student_list")->classid=$classid;
	    					M("exams_student_list")->dotime=Date("Y-m-d H:i:s");
	    					M("exams_student_list")->paper_id=$examsid;
	    					M("exams_student_list")->add();
	    					M("exams_student_list")->where("studentid='%s' and paper_id=%d",$username,$examsid)->setField("lastupdatetime",$submittime);
	    					$this->display('student_exams');
    			  	}
	    		}
    			else{    //家长,并且他的孩子没有做这个训练,转入预览页
    				$url="Location:index?examsid=".$examsid;
            		header($url);
    			}	
            
        }else{
          $sql="select * from engs_exams_student_list where engs_exams_student_list.paper_id='".$examsid."' and engs_exams_student_list.studentid='".$username."' and engs_exams_student_list.classid= '".$classid."' and  engs_exams_student_list.submittime is not null";
          $srs=M()->query($sql);
          if(empty($srs)){
          	if($usertype != 0){
          		M("homework_student_list")->where("studentid='%s' and paper_id=%d",$username,$examsid)->setField("lastupdatetime",$submittime);
            	$this->display('student_exams');
          	}
            else{
            	$url="Location:index?examsid=".$examsid;
                header($url);
            }
          }
          else{
            $url="Location:stufeedback?examsid=".$examsid."&classId=".$classid."&studentId=".$username;
            header($url);
          }
        }
 
        
    }

    //听力训练详细信息
        public function get_exams($homeworkid,$studentid,$classid,$examsid,$issubmit,$answer,$quizid){
        
        if(!empty($examsid)){
            $sqlquery = M();
            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id ='.$examsid;
            $examsdata = $sqlquery -> query($sql);
            $exams_tts_type = $examsdata[0]['tts_type'];
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes,"'.$examsid.'" as homeworkid from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$value['id'].' and tts_type="st" and isdel = 1 order by sortid,id';
                $stem_tts = $sqlquery->query($sql);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                    $sql='select '.$answer.' as answer,'.$issubmit.' as issubmit,"'.$examsid.'" as examsid,t.* from engs_exams_questions t where t.stemid ='.$value['id'].' and t.isdel=1 order by sortid,id';
                    $question = $sqlquery -> query($sql);
                    foreach ($question as $quekey => $quevalue) {
                            $sql = "select answer as useranswer,iscorrect from engs_exams_student_result where examsid=".$examsid." and classid='".$classid."' and studentid='".$studentid."' and questionsid = ".$quevalue['id'];
                            //echo $sql;exit;
                            $question[$quekey]["tcontent"]=relace_tcontent($question[$quekey]["tcontent"]);
                            $useranswer = $sqlquery -> query($sql);
                            if(count($useranswer) > 0){
                                $question[$quekey]["useranswer"]=$useranswer[0]['useranswer'];
                                $question[$quekey]["iscorrect"]=$useranswer[0]['iscorrect'];
                            }
                            else{
                                $question[$quekey]["useranswer"]='';
                                $question[$quekey]["iscorrect"]='';
                            }
                            
                            $question[$quekey]["quizid"] = $quizid;
                            $question[$quekey]["examsid"] = $examsid;
                            $question[$quekey]["question_score"]=$value['question_score'];
                            $que_items = $quevalue['questions_items'];
                            $que_items = json_decode(urldecode($que_items));
                            $que_items = object_to_array($que_items);
                            $question[$quekey]['items'] = $que_items;
                            $queanswer = $quevalue['questions_answer'];
                            $queanswer = json_decode(urldecode($queanswer));
                            $queanswer = object_to_array($queanswer);
                            //var_dump($queanswer);exit;
                            $question[$quekey]['que_answer'] = $queanswer;
                            $que_tts = $quevalue['questions_tts'];
                            $que_tts = json_decode(urldecode($que_tts));
                            $que_tts = object_to_array($que_tts);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $question[$quekey]['que_tts_noqn'] = $que_tts;
                            $stemdata[$key]['questypeid'] = $quevalue['typeid'];
                            $exams_score += $value['question_score'];
                        
                    }
                    $stemdata[$key]['question'] = $question;
                }
                else{           //组合大题
                    $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $stem_children[$keychild]["content"]=relace_tcontent($stem_children[$keychild]["content"]);
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                        
                         $sql='select '.$answer.' as answer,'.$issubmit.' as issubmit,"'.$examsid.'" as examsid,t.* from engs_exams_questions t where t.stemid ='.$childvalue['id'].' and t.isdel=1';
                       // echo $sql;
                        $question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) {
                            $sql = "select answer as useranswer,iscorrect from engs_exams_student_result where examsid=".$examsid." and classid='".$classid."' and studentid='".$studentid."' and questionsid = ".$quevalue['id'];
                            $useranswer = $sqlquery -> query($sql);
                            $question[$quekey]["tcontent"]=relace_tcontent($question[$quekey]["tcontent"]);
                            if(count($useranswer) > 0){
                                $question[$quekey]["useranswer"]=$useranswer[0]['useranswer'];
                                $question[$quekey]["iscorrect"]=$useranswer[0]['iscorrect'];
                            }
                            else{
                                $question[$quekey]["useranswer"]='';
                                $question[$quekey]["iscorrect"]='';
                            }

                            $question[$quekey]["quizid"] = $quizid;
                            $question[$quekey]["examsid"] = $examsid;

                            $question[$quekey]["question_score"]=$childvalue['question_score'];
                            $que_items = $quevalue['questions_items'];
                            $que_items = json_decode(urldecode($que_items));
                            $que_items = object_to_array($que_items);
                            $question[$quekey]['items'] = $que_items;
                            $queanswer = $quevalue['questions_answer'];
                            $queanswer = json_decode(urldecode($queanswer));
                            $queanswer = object_to_array($queanswer);
                            $question[$quekey]['que_answer'] = $queanswer;
                            $que_tts = $quevalue['questions_tts'];
                            $que_tts = json_decode(urldecode($que_tts));
                            $que_tts = object_to_array($que_tts);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $question[$quekey]['que_tts_noqn'] = $que_tts;
                            $stem_children[$keychild]['questypeid'] = $quevalue['typeid'];
                            $exams_score += $childvalue['question_score'];
                        }
                        $stem_children[$keychild]['question'] = $question;
                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                }
            }
            $examsdata[0]['stem'] = $stemdata;
            $examsdata[0]['exams_score'] = $exams_score;
            //var_dump($examsdata);
            //$this->ajaxReturn($examsdata);
            return $examsdata;
        }
    }

    public function getStuExaminfo(){
    	$examsid=I("examsid");
        $username=I("username");
        $classid=I("classid");

        //$examsinfo = new PcfunctionController();
    	$rs= $this -> get_exams(0,$username,$classid,$examsid,0,0,0);

    	//var_dump($rs);
    	$this->ajaxReturn($rs);
    }

   public function setUseranswer(){
      $examsid=I("examsid");
      $quizid=I("quizid");
      $quesid=I("questionid");
      $answerid=I("answerid");
      $useranswer=I("useranswer");
      $typeid=I("typeid/d",0);
      $studentid=cookie("username");
      $classid=cookie("classid");
      $areaid = cookie("areacode");
      //将用户的答案进行提交只记录最后一次的答案
      $student_quiz=M("exams_student_result");
      //计算时间直接当前时间和上次跟新时间相减
      $curtime=Date("Y-m-d H:i:s");
      //查询用户上次跟新时间
      $studentlist=M("exams_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$examsid)->find();
      $lastupdatetime= $studentlist["lastupdatetime"];
      $starttime=strtotime($curtime);
      $endtime=strtotime($lastupdatetime);
      $time=$starttime-$endtime;
      //跟新上次跟新时间
      M("exams_student_list")->lastupdatetime=$curtime;
      M("exams_student_list")->papertime=$studentlist["papertime"]+$time;
      M("exams_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$examsid)->save();
      //查询之前的答案
      $rs=$student_quiz->where("examsid=%d  and questionsid=%d and answerid=%d and studentid='%s' and classid='%s'",$examsid,$quesid,$answerid,$studentid,$classid)->find();
      if(empty($rs)){
      	  $student_quiz->areaid=$areaid;
          $student_quiz->studentid=$studentid;
          $student_quiz->classid=$classid; 
          $student_quiz->examsid=$examsid;
          $student_quiz->answerid=$answerid;
          $student_quiz->answer=$useranswer;
          $student_quiz->questionsid=$quesid;
          //查询答案进行正确错误判断
          $quesanswer=M("exams_questions_answer")->where("id=%d and isdel=1",$answerid)->find();
          $stemrs=M()->query("select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.isdel=1 and s.id=".$quesid.")");
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
          $student_quiz->do_time=$curtime;
          $student_quiz->spendtime=$time;
          if($studentid=='0'||$studentid==''){

          }else{
            $student_quiz->add();
          }
      }else{
      	$student_quiz->areaid=$areaid;
        $student_quiz->studentid=$studentid;
        $student_quiz->classid=$classid;
        $student_quiz->examsid=$examsid;
        $student_quiz->answerid=$answerid;
        $student_quiz->answer=$useranswer;
        $student_quiz->questionsid=$quesid;
        //查询答案进行正确错误判断
        $quesanswer=M("exams_questions_answer")->where("id=%d and isdel=1",$answerid)->find();
        $stemrs=M()->query("select * from engs_exams_stem t where t.id in (select stemid from engs_exams_questions s where s.isdel=1 and s.id=".$quesid.")");
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
        $student_quiz->do_time=$curtime;
        //$student_quiz->spendtime=$rs["spendtime"]+$time;
        if($studentid=='0'||$studentid==''){

        }else{
          $student_quiz->where("id=%d",$rs["id"])->save();
        }
      }
    }

    //学生提交作业接口
    public function stupublish(){
      $source=I("source/d",0);
      $paper_id=I("paper_id");
      $studentid=I("studentid");
      $schoolclass=I("classid");
      $submittime=Date("Y-m-d H:i:s");
   
      $studentname=cookie("engm_truename");

      //更新学生数据的表
       M("exams_student_list")->where("studentid='%s' and paper_id='%d'",$studentid,$paper_id)->setField("submittime",$submittime);
 	   M("exams_student_result")->where("studentid='%s' and classid='%s' and examsid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
 	   M("exams_student_result")->where("studentid='%s' and classid='%s' and examsid='%d'",$studentid,$schoolclass,$paper_id)->setField("studentname",$studentname);
       $ret["msg"]="发布成功";
       $ret["state"]="1";
       $this->ajaxReturn($ret);
    }
    //学生作业反馈
     public function stufeedback(){
        $examsid=I("examsid");
        $studentid= I('studentId/s',0);
        $classid = I('classId/s',0);
        $showtype = I('showtype/d',0);
        $rs=M("exams")->field('id,name,quescount')->where("id=%s and isdel=1",$examsid)->find();

        $this->assign("exams_name",$rs['name']);

        session('studentid',$studentid);
        session('username',$studentid);
        session('classid',$classid);
        $main = session("main");
        session('viewtype',0);
        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');

        $this ->assign('eqcardtitle',$arraynum[$cardnum].'听力训练');
        $sqlquery = M();
        $sql = "select sum(score) as score from engs_exams_student_result where examsid=".$examsid." and classid='".$classid."' and studentid='".$studentid."'";
    
		$eqdelv = $sqlquery->query($sql);
        $totalscore = getExamScore($examsid);
	    $eqdefenlv = $eqdelv[0]['score']/$totalscore;

        $average = sprintf("%.3f",$eqdefenlv)*100;

        $this->assign("average",$average);

        $this->assign('classid',$classid);
        $this->assign('studentid',$studentid);
        $this->assign('examsid',$examsid);
        $this->assign('showtype',$showtype);
        $this->assign('main',$main);
        //get_stu_feedback_wordtest($homeworktype,$iserror,$studentid,$classid,$issubmit,$homeworkid,$wordtype);
        // $wordwpfeedback = R('pcfunction/get_stu_feedback_wordtest',array(0,1,$studentid,$classid,1,$homeworkid,2));
        // var_dump($wordwpfeedback);
        $this->display('stufeedback');
     
    }
    //学生反馈听力试卷-做作业
    public function getSturesultExaminfo(){
        $examsid=I("examsid");
        $studentid=I("studentid");
        $classid=I("classid");
        $rs = $this->get_exams($examsid,$studentid,$classid,$examsid,1,1,0);
        $this->ajaxReturn($rs);

    }
}

