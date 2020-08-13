<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 首页控制器类
*
* @author         gm
* @since          1.0
*/

class PublicController extends Controller {
    //学生提交作业接口
    public function stupublish(){
      $homeworkid=I("homeworkid");
      $batchid=I("batchid");
      $channelid=I("channelid","");
      $source=I("source/d",0);
      $paper_id=I("paper_id");
      $starttime=I("starttime");
      $studentid=I("studentid");
      $schoolclass=I("classid");
      $tms=I("tms");
      cookie("tms",$tms);
      $yjArr=get_ip("",$tms);
      $tqms=$yjArr['tqms']['c1'];
      cookie("tms",$yjArr['tms']['c1']);
      getHomeworkPublishStudents($tqms,$paper_id,$batchid);

      //查看作业问题
      $hrs=M("homework")->where("id=%d",$paper_id)->find();
      $batchrs=M("homework_batch")->where("batchid='%s' and homeworkid='%s' and paper_id=%d",$batchid,$homeworkid,$paper_id)->find();

      $homeworkblock=0;
      if($hrs["wacount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      if($hrs["wscount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      if($hrs["wccount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      if($hrs["wrcount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      if($hrs["tacount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      if($hrs["eqcount"]>0){
        $homeworkblock=$homeworkblock+1;
      }
      //计算分数
      $score=0.0;
      $sql="select '1' as id,sum(score) as score,'4' as typeid from engs_homework_student_word_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
      $sql=$sql." union all select '1' as id,sum(score) as score,'5' as typeid from engs_homework_student_text_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
      $sql=$sql." union all select '1' as id,sum(score) as score,type as typeid from engs_homework_student_word_evaluat where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."' group by type";
      $sql=$sql." union all select examid as id,sum(score) as score,'6' as typeid from engs_homework_student_quiz where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."' group by examid";
      $srs=M()->query($sql);
      //定义作业总共有几块内容
      foreach($srs as $key=>$value){
        if($value["typeid"]=='4'){
          //单词跟读问题
          if(!empty($value["score"])){
            $score=$value["score"]/$hrs["wacount"]+$score;
          }
        }else if($value["typeid"]=='5'){
          //课文跟读
          if(!empty($value["score"])){
            $score=$value["score"]/$hrs["tacount"]+$score;
          }
        }else if($value["typeid"]=='6'){
          //听力训练
          if(!empty($value["score"])){
            //计算试卷的总分
            $totalscore=getExamScore($value["id"]);
            $score=($value["score"]*100)/$totalscore+$score;
          }
        }else if($value["typeid"]=='0'){
          //听音选词
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wccount"]+$score;
          }
        }else if($value["typeid"]=='2'){
          //单词拼写
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wscount"]+$score;
          }
        }else{
          //英汉互译
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wrcount"]+$score;
          }
        }
      }

      // $sso=$yjArr['sso']['c1'];
      // cookie("sso",$sso);
      //获取用户信息

      getuserinfo($studentid);
      $submittime=Date("Y-m-d H:i:s");

        $workService = new \Homework\Service\RedisService();
        $data = $workService->getHash($studentid,$paper_id);
        $data = unserialize($data);
//        if($data){
//            $data = unserialize($data);
//        }else{
//            $data["usetime"] = 0;
//        }
//        $time = time();
//        $studentTime["updatetime"] = $time;
//        $studentTime["usetime"] =  $data["usetime"] + ($time - $data["updatetime"]) ;
//        $workService -> addHash($studentid,$homeworkid,serialize($studentTime));
      //  var_dump($data);exit;
        $starttime = $data["starttime"];
        $spendtime = $data["usetime"];
        $updatetime = $data["updatetime"];
        $updatetime = date("Y-m-d H:i:s",$updatetime);
        $submittime = Date("Y-m-d H:i:s");
//echo $sql;exit;
        M("homework_student_list")->where("studentid='".$studentid."' and classid='".$schoolclass."' and paper_id=".$paper_id)->setField(["papertime"=>$spendtime,"lastupdatetime"=>$updatetime,"submittime"=>Date("Y-m-d H:i:s")]);



      //查询学生做作业的时间
//      $result=M("homework_student_list")->where("studentid='".$studentid."' and classid='".$schoolclass."' and paper_id=".$paper_id)->select();
//      $dotime=$result[0]["dotime"];
//      $starttime=strtotime($dotime);
//      $endtime=strtotime($submittime);
//      $spendtime=$result[0]["papertime"];
      $starttime=date('YmdHis',$starttime);
      $studentname=cookie("engm_truename");
      //接口地址接口地址需要进行配置
      $url=PROTOCOL."://".$tqms."/online/interface/homework/saveEnAnswers.action";
      //通过curl进行post参数的提交
      //echo $url."?studentEnAnswer.studentId=".$studentid."&studentname=".$studentname."&schoolclass=".$schoolclass."&homeworkid=".$homeworkid."&spendtime=".(int)($spendtime),(double)($score/$homeworkblock),$starttime
      $rs=array();
      if($source==1){
        $rs=curl_post_contents($url,$studentid,$studentname,$schoolclass,$homeworkid,(int)($spendtime),(double)($score/$homeworkblock),$starttime,$channelid);
      }else{
        $rs=curl_post_contents($url,$studentid,$studentname,$schoolclass,$homeworkid,(int)($spendtime),(double)($score/$homeworkblock),$starttime);
      }
      $data=json_decode(urldecode($rs));
     // var_dump($data);
      if($data->success){
     //echo $data->success;
        //更新学生数据的表
        M("homework_student_list")->where("studentid='%s' and paper_id='%d'",$studentid,$paper_id)->setField("submittime",$submittime);
        //将homework表中的数据表进行处理
        $publishstudents=json_decode($batchrs["publishstudents"],TRUE);
        $publishstudents[$schoolclass]["students"][$studentid]["score"]=(double)($score/$homeworkblock);
        $publishstudents[$schoolclass]["students"][$studentid]["time"]=(int)($spendtime);
        $publishstudents[$schoolclass]["students"][$studentid]["issubmit"]=1;
        $publishstudents=json_encode($publishstudents);
        //跟新批次表
        M("homework_batch")->where("batchid='%s' and homeworkid='%s' and paper_id=%d",$batchid,$homeworkid,$paper_id)->setField("publishstudents",$publishstudents);
        //将用户的答案表进行更新
        M("homework_student_quiz")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
        M("homework_student_word_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
        M("homework_student_word_evaluat")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
        M("homework_student_text_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("issubmit","1");
        M("homework_student_quiz")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("studentname",cookie("engm_truename"));
        M("homework_student_word_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("studentname",cookie("engm_truename"));
        M("homework_student_word_evaluat")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("studentname",cookie("engm_truename"));
        M("homework_student_text_read")->where("studentid='%s' and classid='%s' and homeworkid='%d'",$studentid,$schoolclass,$paper_id)->setField("studentname",cookie("engm_truename"));
        $ret["msg"]="发布成功";
        $ret["state"]="1";
        $workService->deleteHash($studentid,$paper_id);
      }else{
        $ret["msg"]="发布失败,请稍后再试";
        $ret["state"]="0";
      }
      $this->ajaxReturn($ret);
    }

    public function publish(){
      foreach($_REQUEST as $k=>$v){
          file_put_contents('./log/enhomework.log','key:'.$k.'|value:'.$v.PHP_EOL,FILE_APPEND);
      }
      $paperid=I("paper_id/d");
      $papername=I("paper_name/s");
      $username=I("username/s");
      $homeworkid=I("homeworkId/s");
      $batchClassMap=I("batchClassMap/s","");
      $classStudentMap=I("classStudentMap/s","");
      $homework_batch=M("homework_batch");
      //将作业的状态置为1标示作业是否教师发布
      $homework=M("homework");
      $homework->is_publish=1;
      $homework->homework_name= urldecode($papername);
      $homework->publish_time=Date("Y-m-d H:i:s");
      $homework->where("id=%d",$paperid)->save();
      //处理批次和班级
      $batchClassMap = stripslashes($batchClassMap);
      $batchClassMap = rtrim($batchClassMap, '"');
      $batchClassMap = ltrim($batchClassMap, '"');
      $batchClassMap = str_replace('&quot;', '"', $batchClassMap);
      $batchClassMap=json_decode($batchClassMap);
      $classStudentMap = stripslashes($classStudentMap);
      $classStudentMap = rtrim($classStudentMap, '"');
      $classStudentMap = ltrim($classStudentMap, '"');
      $classStudentMap = str_replace('&quot;', '"', $classStudentMap);
      $classStudentMap=json_decode($classStudentMap);
      //编辑classlist列表
      $classStudents=array();
      foreach($classStudentMap as $key=>$obj){
        $classinfo=array();
        $students=array();
        $studentnum=0;
        foreach($obj as $sobj){
          $student=array();
          $student["id"]=$sobj->id;
          $student["name"]=$sobj->name;
          $student["parentIds"]=$sobj->parentIds;
          $student["score"]=0;
          $student["time"]=0;
          $student["issubmit"]=0;
          $students[$sobj->id]=$student;
          $studentnum=$studentnum+1;
        }
        $classinfo["classId"]=$key;
        $classinfo["className"]="";
        $classinfo["studentCount"]=$studentnum;
        $classinfo["students"]=$students;
        $classStudents[$key]=$classinfo;
      }
      //班级批次列表
      foreach($batchClassMap as $key=>$value){
        //查询数据库中是否已经存在这个batch
        $rs=$homework_batch->where("batchid='%s'",$value)->find();
        //将数据插入数组中
        if(!empty($rs)){
          $publishstudents=json_decode($rs["publishstudents"],TRUE);
          if(empty($publishstudents[$key])){
            $publishstudents[$key]=$classStudents[$key];
          }else{
            foreach($classStudents[$key]["students"] as $skey=>$svalue){
              $publishstudents[$key]["students"][$skey]=$svalue;
              $publishstudents[$key]["studentCount"]=$publishstudents[$key]["studentCount"]+1;
            }
          }
          //保存这个数据到batchid表中
          $homework_batch->publishstudents=json_encode($publishstudents);
          $homework_batch->paper_name=$papername;
          $homework_batch->publish_time=Date("Y-m-d H:i:s");
          $homework_batch->where("batchid='%s'",$value)->save();
          $homework->where("id=%d",$paperid)->setField("publishstudents",json_encode($publishstudents));
        }else{
          $class=array();
          $class[$key]=$classStudents[$key];
          $homework_batch->batchid=$value;
          $homework_batch->paper_id=$paperid;
          $homework_batch->publish_time=Date("Y-m-d H:i:s");
          $homework_batch->paper_name=$papername;
          $homework_batch->username=$username;
          $homework_batch->publishstudents=json_encode($class);
          $homework_batch->homeworkid=$homeworkid;
          $homework_batch->add();
          $homework->where("id=%d",$paperid)->setField("publishstudents",json_encode($class));
        }
      }
      $arr["succ"]=0;
      $arr["msg"]="发布成功";
      header('Content-Type:application/json; charset=utf-8');
      echo $jsoncallback.'('.json_encode($arr).');';
    }

    //英语语音评测问题
    public function getTestScore(){
      $filename=I("filename");
      $content=I("content");
      $studentid=I("studentid");
      $classid=I("classid");
      $homeworkid=I("homeworkid");
      $contentid=I("contentid");
      $readid=I("readid");
      $source=I("source/d",0);
      $recordpath = C('recordpath');
      //$filepath =$recordpath.$filename;
      $type=I("type/d",0);
      $result=smartstudy($filename,$content,$type);
      file_put_contents('./log/score.log',$result,FILE_APPEND);
      //$score = round($result->data->score,1);
      $score = round($result["data"]["score"],1);
      //计算时间直接当前时间和上次跟新时间相减
      $curtime=Date("Y-m-d H:i:s");
      //查询用户上次跟新时间
//      $studentlist=M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->find();
//      $lastupdatetime= $studentlist["lastupdatetime"];
//      $starttime=strtotime($curtime);
//      $endtime=strtotime($lastupdatetime);
//      $time=$starttime-$endtime;

        // $workService = new \Homework\Service\RedisService();
        // $data = $workService->getHash($studentid,$homeworkid);
        // if($data){
        //     $data = unserialize($data);
        // }else{
        //     $data["usetime"] = 0;
        // }
        // $time = time();
        // $studentTime["updatetime"] = $curtime;
        // $studentTime["usetime"] =  $data["usetime"] + ($time - strtotime($data["updatetime"])) ;
        // $workService -> addHash($studentid,$homeworkid,serialize($studentTime));

      //跟新上次跟新时间
      //M("homework_student_list")->lastupdatetime=$curtime;
      //M("homework_student_list")->papertime=$studentlist["papertime"]+$time;
      //M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->setField(["lastupdatetime"=>$curtime,"papertime"=>($studentlist["papertime"] + $time)]);
      //跟新学生的答案
      if($type==0){
        //将语音的信息进行保存
        $homework_student_word_read=M("homework_student_word_read");
        //用户重新录音那么直接将前面的录音进行冲掉
        $rs=$homework_student_word_read->where("studentid='%s' and classid='%s' and homeworkid='%d' and word_readid='%s'",$studentid,$classid,$homeworkid,$readid)->find();
        if(empty($rs)){
          $homework_student_word_read->studentid=$studentid;
          $homework_student_word_read->classid=$classid;
          $homework_student_word_read->homeworkid=$homeworkid;
          $homework_student_word_read->word_readid=$readid;
          $homework_student_word_read->tmp3='';
          $homework_student_word_read->mp3=$filename;
          $homework_student_word_read->score=$score;
          $homework_student_word_read->do_time=$curtime;
          $homework_student_word_read->spendtime=$time;
          $id=$homework_student_word_read->add();
        }else{
          $homework_student_word_read->studentid=$studentid;
          $homework_student_word_read->classid=$classid;
          $homework_student_word_read->homeworkid=$homeworkid;
          $homework_student_word_read->word_readid=$readid;
          $homework_student_word_read->tmp3='';
          $homework_student_word_read->mp3=$filename;
          $homework_student_word_read->score=$score;
          $homework_student_word_read->do_time=$curtime;
          $homework_student_word_read->spendtime=$rs["spendtime"]+$time;
          $homework_student_word_read->where("id=%d",$rs["id"])->save();
          $id=$rs["id"];
        }
      }else{
        //将语音的信息进行保存
        $homework_student_text_read=M("homework_student_text_read");
        //用户重新录音那么直接将前面的录音进行冲掉
        $rss=$homework_student_text_read->where("studentid='%s' and classid='%s' and homeworkid='%d' and text_readid='%s' and textid=%d",$studentid,$classid,$homeworkid,$readid,$contentid)->find();
        if(empty($rss)){
          $homework_student_text_read->studentid=$studentid;
          $homework_student_text_read->classid=$classid;
          $homework_student_text_read->homeworkid=$homeworkid;
          $homework_student_text_read->text_readid=$readid;
          $homework_student_text_read->tmp3='';
          $homework_student_text_read->mp3=$filename;
          $homework_student_text_read->score=$score;
          $rs=M("homework_text_read")->where("id='%d'",$readid)->find();
          $chapterid=$rs["chapterid"];
          $homework_student_text_read->chapterid=$chapterid;
          $homework_student_text_read->textid=$contentid;
          $homework_student_text_read->do_time=$curtime;
          $homework_student_text_read->spendtime=$time;
          $id=$homework_student_text_read->add();
        }else{
          $homework_student_text_read->studentid=$studentid;
          $homework_student_text_read->classid=$classid;
          $homework_student_text_read->homeworkid=$homeworkid;
          $homework_student_text_read->text_readid=$readid;
          $homework_student_text_read->tmp3='';
          $homework_student_text_read->mp3=$filename;
          $homework_student_text_read->score=$score;
          $rs=M("homework_text_read")->where("id='%d'",$readid)->find();
          $chapterid=$rs["chapterid"];
          $homework_student_text_read->chapterid=$chapterid;
          $homework_student_text_read->textid=$contentid;
          $homework_student_text_read->do_time=$curtime;
          $homework_student_text_read->spendtime=$rss["spendtime"]+$time;
          $homework_student_text_read->where("id=%d",$rss["id"])->save();
          $id=$rss["id"];
        }
      }
      $arr["result"]=$result;
      $arr["id"]=$id;
      $arr["success"]=1;
      $arr["uservoice"]=$filename;
      $this->ajaxReturn($arr);
    }

    //样式文件以及js文件的合并压缩
    public function getFile(){
      vendor('combo-master.minify');
    }


    //用户进行的页面的读写问题
    public function setLog(){
      $type=I("type");
      $ip=false;
      if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        $ip = $_SERVER["HTTP_CLIENT_IP"];
      }
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
        for ($i = 0; $i < count($ips); $i++) {
          if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
          $ip = $ips[$i];
          break;
          }
        }
      }
      $ip=$ip?$ip:$_SERVER['REMOTE_ADDR'];
      //文件名字进行日期
      $filename='./log/'.Date("Ymd").".log";
      //判断文件是否存在
      if(!file_exists($filename)){
        $myfile = fopen($filename, "w") or die("Unable to open file!");
        fclose($myfile);
      }
      //将数据写到log文件中
      file_put_contents($filename, $ip."|".$_SERVER["HTTP_USER_AGENT"]."|".Date("Y-m-d H:i:s"). "|".$type.PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    //获取作业的班级
    public function getHomeworkClass(){
      $paperid=I("homeworkid");
      $rs=M("homework")->where("id=%d",$paperid)->find();
      $publishstudents=json_decode($rs["publishstudents"],true);
      $classs=array();
      foreach($publishstudents as $key=>$value){
        $temp=array();
        $temp["id"]=$publishstudents[$key]["classId"];
        $classname=$publishstudents[$key]["className"];
        //调用接口
        if(empty($classname)||$classname=="null"){
          //表用接口
          $sso=cookie("sso");
          //echo $sso;exit;
          $classname=getClassName($sso,$temp["id"]);

          $publishstudents[$key]["className"]=$classname;
          //更新数据库
          M("homework")->where("id=%d",$paperid)->setField("publishstudents",json_encode($publishstudents));
        }
        $temp["name"]=$classname;
        $temp["studentcount"]=$publishstudents[$key]["studentCount"];
        array_push($classs, $temp);
      }
      $this->ajaxReturn($classs);
    }


    //获取作业的班级
    public function getHomeworkClassStudent(){
      $paperid=I("homeworkid");
      $classid=I("classid");
      $batchid = I("batchid/s","");
     // $rs=M("homework")->where("id=%d",$paperid)->find();
      $rs=M("homework_batch")->where("paper_id=%d and batchid='%s'",$paperid,$batchid)->find();
      $publishstudents=json_decode($rs["publishstudents"],true);
      $students=$publishstudents[$classid];
      //计算班级的平均分和最高分
      $homeworkmaxscore=0;
      $homeworktime=0;
      $homeworksubmitnum=0;
      foreach($students["students"] as $key=>$value){
        if($value["issubmit"]==1){
           $homeworksubmitnum=$homeworksubmitnum+1;
        }
       // echo $value["time"]. "||".$value['name']."<br/>";
        $homeworktime=ceil($homeworktime)+ceil($value["time"]);
        if($homeworkmaxscore<$value["score"]){
          $homeworkmaxscore=$value["score"];
        }
      }
      if($homeworksubmitnum==0){
        $homeworkaveragetime=0;
      }else{
        $homeworkaveragetime=round($homeworktime/$homeworksubmitnum);
      }
     // echo $homeworktime;exit;
      $students["homeworkmaxscore"]=round($homeworkmaxscore,1);
      $students["homeworkaveragetime"]=Sec2Time($homeworkaveragetime);
      $students["homeworksubmitnum"]=$homeworksubmitnum;
      $this->ajaxReturn($students);

    }

     //获取班级信息
    public function getClass(){
      $paperid=I("homeworkid");
      $classid=I("classid");
      $rs=M("homework")->where("id=%d",$paperid)->find();
      $publishstudents=json_decode($rs["publishstudents"],true);
      $students=$publishstudents[$classid];
      //获取提交的人数
      $studocount=M("homework_student_list")->where("paper_id=%d and classid='%s' and submittime is not null",$paperid,$classid)->count();
      $students["submitCount"]=$studocount;
      $this->ajaxReturn($students);

    }




    //回放音频
    public function playBack(){
      $id=I("id");
      $type=I("type/d",0);//0表示单词 1表示课文
      if($type==0){
        $rs=M("homework_student_word_read")->where("id=%d",$id)->find();
        $usermp3=$rs["mp3"];//用户的原始音频
        $filename=$rs["tmp3"];
        //获取名称
        $usermp3arr=explode(".", $usermp3);
        $filenamearr=explode(".", $filename);
        if((empty($filename)&&!empty($usermp3))||(!empty($filename)&&!empty($usermp3)&&$usermp3arr[0]!=$filenamearr[0])){
          if($usermp3arr[1]!='mp3'){
            $filename=voiceTrasfer($rs["studentid"],$usermp3,".wav");
          }else{
            $filename=$usermp3;
          }
          //写入数据库
          M("homework_student_word_read")->where("id=%d",$id)->setField("tmp3",$filename);
        }

      }else if($type==1){
        $rs=M("homework_student_text_read")->where("id=%d",$id)->find();
        $usermp3=$rs["mp3"];//用户的原始音频
        $filename=$rs["tmp3"];
        if((empty($filename)&&!empty($usermp3))||(!empty($filename)&&!empty($usermp3)&&$usermp3arr[0]!=$filenamearr[0])){
          if($usermp3arr[1]!='mp3'){
            $filename=voiceTrasfer($rs["studentid"],$usermp3,".wav");
          }else{
            $filename=$usermp3;
          }
          //$filename=voiceTrasfer($rs["studentid"],$usermp3,".wav");
          M("homework_student_text_read")->where("id=%d",$id)->setField("tmp3",$filename);
        }
      }
      $arr["filename"]=$filename;
      $this->ajaxReturn($arr);
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
      $student_quiz=M("homework_student_quiz");
      //计算时间直接当前时间和上次跟新时间相减
      $curtime=Date("Y-m-d H:i:s");
      //查询用户上次跟新时间
      $studentlist=M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->find();
      $lastupdatetime= $studentlist["lastupdatetime"];
      $starttime=strtotime($curtime);
      $endtime=strtotime($lastupdatetime);
      $time=$starttime-$endtime;

      //将数据写入redis
      // $workService = new \Homework\Service\WorkService();
      // $data = $workService->getHash($studentid,$homeworkid);
      // if($data){
      //     $data = unserialize($data);
      // }else{
      //     $data["usetime"] = 0;
      // }
      // $time = time();
      // $studentTime["updatetime"] = $curtime;
      // $studentTime["usetime"] =  $data["usetime"] + ($time - strtotime($data["updatetime"])) ;
      // $workService -> addHash($studentid,$homeworkid,serialize($studentTime));
//

      //跟新上次跟新时间
//      M("homework_student_list")->lastupdatetime=$curtime;
//      M("homework_student_list")->papertime=$studentlist["papertime"]+$time;
//      M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->save();
      //查询之前的答案
      $rs=$student_quiz->where("homeworkid=%d and examid=%d and quizid=%d and questionsid=%d and answerid=%d and studentid='%s' and classid='%s'",$homeworkid,$examsid,$quizid,$quesid,$answerid,$studentid,$classid)->find();
      if(empty($rs)){
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
        $student_quiz->spendtime=$rs["spendtime"]+$time;
        if($studentid=='0'||$studentid==''){

        }else{
          $student_quiz->where("id=%d",$rs["id"])->save();
        }
      }
    }

    //保存单词测试的答案
    public function setUserWordtestanswer(){
      $word_evaluatid=I("questionid");
      $wordid=I("wordid");
      $homeworkid=I("homeworkid");
      $studentid=I("studentid");
      $classid=I("classid");
      $typeid=I("typeid");
      $useranswer=I("useranswer");
      $homework_student_word_evaluat=M("homework_student_word_evaluat");
      //计算时间直接当前时间和上次跟新时间相减
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
      $rs=$homework_student_word_evaluat->where("studentid='%s' and classid='%s' and homeworkid=%d  and word_evaluatid=%s",$studentid,$classid,$homeworkid,$word_evaluatid)->find();
      if(empty($rs)){
          if($typeid=='2'){
          $homework_student_word_evaluat->studentid=$studentid;
          $homework_student_word_evaluat->classid=$classid;
          $homework_student_word_evaluat->homeworkid=$homeworkid;
          $homework_student_word_evaluat->wordid=$wordid;
          $homework_student_word_evaluat->word_evaluatid=$word_evaluatid;
          $homework_student_word_evaluat->type=$typeid;
          $homework_student_word_evaluat->answer=$useranswer;
          $homework_student_word_evaluat->do_time=$curtime;
          $homework_student_word_evaluat->spendtime=$time;
          //判断是否错误
          $wordevaluatrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          $userans=explode(",",$useranswer);
          $wordrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          $queans=str_split($wordrs["answer"]);
          $homework_student_word_evaluat->iscorrect=1;
          $homework_student_word_evaluat->score=1;
          foreach($queans as $qk=>$qv){
            if($qv!=$userans[$qk]){
              $homework_student_word_evaluat->iscorrect=0;
              $homework_student_word_evaluat->score=0;
              break;
            }
          }
          $homework_student_word_evaluat->add();
        }else{
          $homework_student_word_evaluat->studentid=$studentid;
          $homework_student_word_evaluat->classid=$classid;
          $homework_student_word_evaluat->homeworkid=$homeworkid;
          $homework_student_word_evaluat->wordid=$wordid;
          $homework_student_word_evaluat->word_evaluatid=$word_evaluatid;
          $homework_student_word_evaluat->type=$typeid;
          $homework_student_word_evaluat->answer=$useranswer;
          $homework_student_word_evaluat->do_time=Date("Y-m-d H:i:s");
          //判断是否错误
          $wordevaluatrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          if($useranswer==$wordevaluatrs["answer"]){
            $homework_student_word_evaluat->iscorrect=1;
            $homework_student_word_evaluat->score=1;
          }else{
            $homework_student_word_evaluat->iscorrect=0;
            $homework_student_word_evaluat->score=0;
          }
          if($studentid=='0'||$studentid==''){

          }else{
            $homework_student_word_evaluat->add();
          }
        }
      }else{
        if($typeid=='2'){
          $homework_student_word_evaluat->studentid=$studentid;
          $homework_student_word_evaluat->classid=$classid;
          $homework_student_word_evaluat->homeworkid=$homeworkid;
          $homework_student_word_evaluat->wordid=$wordid;
          $homework_student_word_evaluat->word_evaluatid=$word_evaluatid;
          $homework_student_word_evaluat->type=$typeid;
          $homework_student_word_evaluat->answer=$useranswer;
          $homework_student_word_evaluat->do_time=$curtime;
          $homework_student_word_evaluat->spendtime=$rs["spendtime"]+$time;
          //判断是否错误
          $wordevaluatrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          $userans=explode(",",$useranswer);
          $wordrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          $queans=str_split($wordrs["answer"]);
          $homework_student_word_evaluat->iscorrect=1;
          $homework_student_word_evaluat->score=1;
          foreach($queans as $qk=>$qv){
            if($qv!=$userans[$qk]){
              $homework_student_word_evaluat->iscorrect=0;
              $homework_student_word_evaluat->score=0;
              break;
            }
          }
          $homework_student_word_evaluat->where("id=%d",$rs["id"])->save();
        }else{
          $homework_student_word_evaluat->studentid=$studentid;
          $homework_student_word_evaluat->classid=$classid;
          $homework_student_word_evaluat->homeworkid=$homeworkid;
          $homework_student_word_evaluat->wordid=$wordid;
          $homework_student_word_evaluat->word_evaluatid=$word_evaluatid;
          $homework_student_word_evaluat->type=$typeid;
          $homework_student_word_evaluat->answer=$useranswer;
          $homework_student_word_evaluat->do_time=Date("Y-m-d H:i:s");
          //判断是否错误
          $wordevaluatrs=M("homework_word_evaluat")->where("id=%d",$word_evaluatid)->find();
          if($useranswer==$wordevaluatrs["answer"]){
            $homework_student_word_evaluat->iscorrect=1;
            $homework_student_word_evaluat->score=1;
          }else{
            $homework_student_word_evaluat->iscorrect=0;
            $homework_student_word_evaluat->score=0;
          }
          if($studentid=='0'||$studentid==''){

          }else{
            $homework_student_word_evaluat->where("id=%d",$rs["id"])->save();
          }
        }
      }
    }

    //催交接口
    public function superviseHomework(){
      $homeworkid=I("homeworkid");
      $classid=I("classid");
      $homeworkrs=M("homework")->where("id=%d",$homeworkid)->find();
      $publishstudents=json_decode($homeworkrs["publishstudents"],true);
      $students=$publishstudents[$classid];
      $notStudents=array();
      foreach($students as $key=>$value){
        if($value["issubmit"]==1){
          $temp["name"]=$value["name"];
          $temp["id"]=$value["id"];
          array_push($notStudents, $temp);
        }
      }
      $this->ajaxReturn($notStudents);
      //
    }

    //录音音频上传接口
    public function uploadrecords(){
      $upload = new \Think\Upload();// 实例化上传类
      //$upload->maxSize = 3145728;
      //$upload->rootPath =  C("CONST_UPLOADS");
       $upload->rootPath =  C("recordpath");
       //$upload->rootPath =  "D:\upolads\\";
       $upload->savePath = '';

      //上传文件重命名
      $fileName=md5(date('YmdHis').mt_rand('10000','99999'));
      $upload->saveName = $fileName;
      $upload->exts     = array('amr','mp3','wav');
     
     //多级目录存储
      $upload->autoSub  = true;
      $Y = date('Y');
      $M = date('m');
      $D = date('d');
      $upload->subName  = $Y."/".$M."/".$D;

      //文件上传
      $info = $upload->upload();
      
      if(!$info) {// 上传错误提示错误信息
        $arr_return["issuc"] = 0;
        $arr_return["msg"] = $upload->getError();
        $arr_return["filename"] = "";
        $this->ajaxReturn($arr_return);
      }else{// 上传成功
        $arr_return["issuc"] = 1;
        $arr_return["msg"] = "上传成功";
        $arr_return["filename"] = $info["filename"]["savepath"].$info["filename"]["savename"];
        $this->ajaxReturn($arr_return);
      }
  }

  //老数据进行处理
  public function clearRedisData(){
      set_time_limit(0);
      $homework = M("homework")->where("publish_time < '%s' or publish_time is null",Date("Y-m-d H:i:s",strtotime("-7 days")))->select();
      $workService = new \Homework\Service\WorkService();
      foreach($homework as $key=>$value){
        $homeworkid = $value["id"];
        $studentlist = M("homework_student_list")->where("paper_id = %d",$homeworkid)->select();
        $workService->clearHomeworkData($homeworkid,$studentlist);  
      }
  }
}
