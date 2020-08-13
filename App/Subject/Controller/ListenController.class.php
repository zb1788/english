<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class ListenController extends CheckController {

    public function listenunit() {
        $subjectid=I("subjectid");
        $gradeid=I("gradeid");
        cookie("subjectid",$subjectid);
        cookie("gradeid",$gradeid);
        $areacode=cookie("localAreaCode");
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $truename=cookie("truename");
        $moduleid=I("moduleid","");
        $ks_code=I("ks_code","0");
        $ks_name=I("ks_name","");
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,"0003",$ks_name);
        $backUrl=cookie("backUrl");
        // $this->assign("backUrl",$backUrl);
        // $this->display("listenunit");

        $moduleid=I("moduleid");
        $backUrl=I("backUrl");
        cookie("backUrl",$backUrl);
        $learnways=getmodule($moduleid);
        $url=$learnways["url"];
        $query="";
        $count=count($_GET);
        foreach($_GET as $key=>$value){
            if($key=='m'){continue;} 
            if($key==($count-1)){
                $query=$query.$key."=".$value;
            }else{
                $query=$query.$key."=".$value."&";
            }     
        }
        //判断字符串中是否有问号
        if(strpos($url,"?") === false){     //使用绝对等于
            //不包含
            redirect("../../".$url."?".$query);
        }else{
            //包含
            redirect("../../".$url."&".$query);
        }
    }
    //获取读单词的单元的信息
    public function getListenUnitData(){
    	$unit=D("rms_unit");
        $moduleid=I("moduleid");
        $username=cookie("username");
    	$gradeid=cookie("gradeid");
    	
    	$subjectid=cookie("subjectid");

        getUserConfig($subjectid,$gradeid,$username);
        $volumnid=cookie("termid");
        $versionid=cookie("versionid");
    	$result=$unit->getUnitListData($gradeid,$volumnid,$versionid,$subjectid,$username,$moduleid,"2");
        $name=$unit->getVersionById($gradeid,$volumnid,$versionid,$subjectid);
        $result["bookinfo"]=$name;
        $learnways=getmodule($moduleid);
        //$learnways=M("menu_modules")->where("id=%d",$moduleid)->field("remark,title")->find();
        $result["ways"]=$learnways;
    	$this->ajaxReturn($result);
    }

    public function examlist(){
        $ks_name = I('ks_short_name/s','0');
        if($ks_name == "0"){
            $ks_name = I('ks_name/s',"0");
        }
        $this->assign("ks_name",$ks_name);
        $this->display("examlist");
    }

    //获取试卷列表
    public function getExamList(){
        $ks_code=I("ks_code");
        $username=cookie("username");
        $exams=D("exams");
        $result=$exams->getExamsListData($ks_code,$username);
        $this->ajaxReturn($result);
    }
    
    //听力训练展示
    public function listenexam(){
        $areacode=cookie("localAreaCode");
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $truename=cookie("truename");
        $gradeid=cookie("gradeid");
        $subjectid=cookie("subjectid");
        $moduleid=I("moduleid","");
        $ks_code=I("ks_code","0");
        if($ks_code=='unfedined'){
            $ks_code="";
        }
        $ks_name=I("ks_name","");
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,"0003",$ks_name);
        $exams=D("exams");
        $time=I("time",0);
        $quizid=I("quizid","0");
        $ks_code=I("ks_code",0);
        $ks_short_name=I("ks_short_name",0);
        $moduleid=I("moduleid",0);
        //表示是第几道试题
        $index=I("index/d",0);
        $issubmit=I("issubmit/d",0);
        $iserror=I("iserror/d",0);
        //作业的ID
        $examsid=I("examsid","0");
        //这里需要进行学生班级的查询
        $username=cookie("username");
        $classid=cookie("classid");
        $schoolid=cookie("schoolid");
        $truename=cookie("truename");
        $areaid=cookie("areacode");
        //参数的初始化
        $quescount=0;        
        //查询作业的情况
        $exam=$exams->getExamsById($examsid);
        //作业的时间
        $dotime=Date("Y-m-d H:i:s");
        //相数据表中写入数据
        $user_quiz_list=M("user_quiz_list");
        $rs=$user_quiz_list->where("username='%s' and isover=0 and submittime is null",$username)->order("dotime desc")->find();
        if($issubmit==0){
            $maxrs=$user_quiz_list->field("max(id) as maxid")->find();
            if(empty($rs)){
                $maxid=1;
                if(!empty($max)){
                    $maxid=$maxrs["maxid"]+1;;
                }
                $user_quiz_list->maxid=$maxid;
                $user_quiz_list->areaid=$areaid;
                $user_quiz_list->ks_code=$ks_code;
                $user_quiz_list->username=$username;
                $user_quiz_list->schoolid=$schoolid;
                $user_quiz_list->classid=$classid;
                $user_quiz_list->paper_id=$examsid;
                $user_quiz_list->truename=$truename;
                $user_quiz_list->dotime=Date("Y-m-d H:i:s");
                $user_quiz_list->usertype=cookie("usertype");
                $id=$user_quiz_list->add();
            }else{
                if((strtotime($dotime )-strtotime($rs["dotime"]))>C("examstime")*60){
                    $maxid=1;
                    if(!empty($max)){
                        $maxid=$maxrs["maxid"]+1;;
                    }
                    $user_quiz_list->maxid=$maxid;
                    $user_quiz_list->areaid=$areaid;
                    $user_quiz_list->ks_code=$ks_code;
                    $user_quiz_list->username=$username;
                    $user_quiz_list->schoolid=$schoolid;
                    $user_quiz_list->classid=$classid;
                    $user_quiz_list->paper_id=$examsid;
                    $user_quiz_list->truename=$truename;
                    $user_quiz_list->dotime=Date("Y-m-d H:i:s");
                    $user_quiz_list->usertype=cookie("usertype");
                    $id=$user_quiz_list->add();
                }else{
                    $id=$rs["id"];
                }
            }
        }else{
            $rs=$user_quiz_list->where("username='%s' and isover=0",$username)->order("dotime desc")->find();
            $id=$rs["id"];
            $quizid=$rs["id"];
        }
        $examquescount=$exam["quescount"];
        $examname=$exam["name"];
        //作业总体数量
        $this->assign("classid",$classid);
        $this->assign("username",$username);
        $this->assign("starttime",$starttime);
        $this->assign("examquescount",$examquescount);
        $this->assign("examname",$examname);
        $this->assign("index",$index);
        $this->assign("iserror",$iserror);
        $this->assign("examsid",$examsid);
        $this->assign("quizid",$quizid);
        $this->assign("ks_code",$ks_code);
        $this->assign("ks_short_name",$ks_short_name);
        $this->assign("moduleid",$moduleid);
        $this->assign("issubmit",$issubmit);
        $this->assign("time",$time);
        $this->assign("id",$id);
        $this->display("listenexam");
    }

    //单个问题进行展示
    public function getExamsQuestionTTS(){
        $examsid=I("examsid");
        $exams=D("exams");
        $result=$exams->getQuestionTTSByExamsid($examsid);
        $this->ajaxReturn($result);
    }

    //单个问题进行展示
    public function getExamsQuestion(){
        $examsid=I("examsid");
        $index=I("index",0);
        $quizid=I("quizid/d",0);
        $username=cookie("username");
        $classid=cookie("classid");
        $exams=D("exams");
        $result=$exams->getQuestionByExamsid($username,$classid,$quizid,$examsid,$index);
        //var_dump($result);exit;
        $this->ajaxReturn($result);
    }

    //每个学生的作业总结
    public function getStudentExamsSummary(){
        $examsid=I("examsid");
        $quizid=I("quizid");
        $username=cookie("username");
        $classid=cookie("classid");
        $sql="select engs_exams_questions.*,s.id as useranswerid,s.answer as useranswer,s.score from engs_exams_questions";
        $sql=$sql." left join (select * from engs_user_quiz_history where examid=%d ";
        $sql=$sql."and username='%s' and classid='%s' and quizid=%d ) as s on engs_exams_questions.id=s.questionsid";
        $sql=$sql." where examsid=%d  and isdel=1 order by engs_exams_questions.paper_sortid,id";
        $result=M()->query($sql,$examsid,$username,$classid,$quizid,$examsid);
        $this->ajaxReturn($result);
    }


  //听力训练完成
  public function finish(){
        //作业总分数以及学生的分数
        $quizid=I("quizid");
        $examsid=I("examsid");
        $ks_code=I("ks_code");
        $moduleid=I("moduleid");
        $ks_short_name=I("ks_short_name");
        $username=cookie("username");
        $classid=cookie("classid");
        $type=I("type/d",0);
        //用户的分数以及平均分
        $homeworkrs=M("user_quiz_list")->where("id=%d",$quizid)->find();

        $homeworkscore=0;
        //查询用户的时间
        $studentscore=$homeworkrs["score"];
        $starttime=strtotime($homeworkrs["dotime"]);
        $endtime=strtotime($homeworkrs["submittime"]);
        $homeworksubmittime=$endtime-$starttime;
        //将这个转换成分和秒
        $usertime="";
        $homeworkmunite=floor($homeworksubmittime/60);
        if($homeworkmunite==0){
            $usertime="";
        }else{
            $usertime=$homeworkmunite."分";
        }
        $homeworksecond=$homeworksubmittime%60;
        if($homeworksecond==0){
            $usertime=$usertime;
        }else{
            $usertime=$usertime.$homeworksecond."秒";
        }
        $homeworkmaxscore=0.0;
        $homeworkaveragescore=0.0;
        $homeworksubmitnum=0;
        $homeworkaveragetime=0;
        //获取班级中的最高分以及平均分
        $studentrs=M("user_quiz_list")->where("classid='%s' and paper_id=%d and isover=0 and submittime is not null",$classid,$examsid)->select();
        foreach($studentrs as $key=>$value){
            //获取最高分以及平均分
            $homeworkaveragescore=$homeworkaveragescore+$value["score"];
            $starttime=strtotime($value["dotime"]);
            $endtime=strtotime($value["submittime"]);
            $homeworkaveragetime=$homeworkaveragetime+$endtime-$starttime;
            $homeworksubmitnum=$homeworksubmitnum+1;
            if($value["score"]>$homeworkmaxscore){
                $homeworkmaxscore=$value["score"];
            }
        }
        if($homeworksubmitnum == 0){
            $homeworkaveragetime = 0;
        }else{
            $homeworkaveragetime=round($homeworkaveragetime/$homeworksubmitnum);
        }
        $usertimea="";
        $homeworkmunitea=floor($homeworkaveragetime/60);
        if($homeworkmunitea==0){
            $usertimea="";
        }else{
            $usertimea=$homeworkmunitea."分";
        }
        $homeworkseconda=$homeworkaveragetime%60;
        if($homeworkseconda==0){
            $usertimea=$usertimea;
        }else{
            $usertimea=$usertimea.$homeworkseconda."秒";
        }
        $homeworkaveragescore=round($homeworkaveragescore/$homeworksubmitnum,1);
        $this->assign("quizid",$quizid);
        $this->assign("studentscore",round($studentscore,1));
        $this->assign("batchid",$batchid);
        $this->assign("studentid",$studentid);
        $this->assign("classid",$classid);
        $this->assign("ks_code",$ks_code);
        $this->assign("moduleid",$moduleid);
        $this->assign("examsid",$examsid);
        $this->assign("ks_short_name",$ks_short_name);
        $this->assign("type",$type);
        $this->assign("homeworkaveragetime",$usertimea);
        $this->assign("homeworksubmittime",$usertime);
        $this->assign("homeworksubmitnum",$homeworksubmitnum);
        $this->assign("homeworkmaxscore",round($homeworkmaxscore,1));
        $this->assign("homeworkaveragescore",$homeworkaveragescore);
        $this->display();
    }

    //中高考的专题
    public function topiclist(){
        $subjectid=I("subjectid");
        $gradeid=I("gradeid");
        cookie("subjectid",$subjectid);
        cookie("gradeid",$gradeid);
        $areacode=cookie("localAreaCode");
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $truename=cookie("truename");
        $moduleid=I("moduleid","");
        $ks_code=I("ks_code","0");
        $ks_name=I("ks_name","");
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,"0003",$ks_name);
        $levelid=I("levelid");
        $rs=M("dictionary")->where("id=%d",$levelid)->find();
        $this->assign("title",$rs["title"]."听力");
        $this->display("topiclist");
    }

    //获取三级联动
    public function getList(){
        $code=I("code");
        $dictionary=M("dictionary");
        $rs=$dictionary->where("code='%s'",$code)->order("sortid desc")->select();
        $this->ajaxReturn($rs);
    }


    //获取专题的试卷
    public function getTopicList(){
        $levelid = I('gradeid/d', 0);
        $yearid = I('yearid/d', 0);
        $typeid = I('typeid/d', 0);
        $provinceid = I('provinceid/d', 0);
        if($levelid != '0'){
            $sqlstr = 'and a.levelid = '.$levelid;
        }
        if($typeid != '0'){
            $sqlstr .= ' and typeid = '.$typeid;
        }
        if($provinceid != '0'){
            $sqlstr .= ' and provinceid = '.$provinceid;
        }
        if($yearid != '0'){
            $sqlstr .= ' and yearid = '.$yearid;
        }
        $sql = 'select a.id,a.name,a.quescount as papernum,(select count(*) from engs_study c where c.engs_studyid = a.id and c.studytype=3 and c.ks_code = "0")  as studynum from engs_exams a where a.exams_classid =3 and a.isdel = 1 and a.state = 4  '.$sqlstr.' order by a.yearid desc, a.sortid,a.id';
        $exams_list = M()->query($sql);
        $this->ajaxReturn($exams_list);
    }
 
}
