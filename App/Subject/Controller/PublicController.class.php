<?php

namespace Subject\Controller;

use Think\Controller;
use Subject\Service\UnitService;

/**
 * Index控制器
 */
class PublicController extends CheckController {

    //用户单元学习记录
    public function setUserModuleUnitLog(){
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("gradeid");
        $truename=cookie("truename");
        $subjectid=cookie("subjectid");
        $moduleid=I("moduleid","");
        $ks_code=I("ks_code","");
        $ks_name=I("ks_name","");
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
    }


    public function enunit(){
      $gradeid=I("gradeid");
      if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){
          cookie("gradeid",$gradeid);
      }
      $this->display();
    }

    //用户模块学习记录
    public function setUserModuleLog(){
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $truename=cookie("truename");
        $subjectid=cookie("subjectid");
        $moduleid=I("moduleid");
        $ks_code="0";
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid);
    }


    //保存试卷的答案
    public function setUseranswer(){
      $homeworkid=I("homeworkid","0");
      $examsid=I("examsid");
      $quizid=I("quizid");
      $quesid=I("questionid");
      $answerid=I("answerid");
      $useranswer=I("useranswer");
      $typeid=I("typeid/d",0);
      $username=cookie("username");
      $classid=cookie("classid");
      $schoolid=cookie("schoolid");
      $areaid=cookie("areacode");
      //将用户的答案进行提交只记录最后一次的答案
      $student_quiz=M("user_quiz_history");
      //阐述之前的答案
      $rs=$student_quiz->where("examid=%d and quizid=%d and questionsid=%d and answerid=%d ",$examsid,$quizid,$quesid,$answerid)->find();
      if(empty($rs)){
          $student_quiz->username=$username;
          $student_quiz->areaid=$areaid;
          $student_quiz->schoolid=$schoolid;
          $student_quiz->classid=$classid;
          $student_quiz->examid=$examsid;
          $student_quiz->quizid=$quizid;
          $student_quiz->answerid=$answerid;
          $student_quiz->answer=$useranswer;
          $student_quiz->questionsid=$quesid;
          $student_quiz->usertype=cookie("usertype");
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
          $student_quiz->do_time=Date("Y-m-d H:i:s");
          if($username=='0'||$username==''){

          }else{
            $student_quiz->add();
          }
      }else{
        $student_quiz->username=$username;
        $student_quiz->areaid=$areaid;
        $student_quiz->schoolid=$schoolid;
        $student_quiz->classid=$classid;
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
        $student_quiz->do_time=Date("Y-m-d H:i:s");
        if($username=='0'||$username==''){

        }else{
          $student_quiz->where("id=%d",$rs["id"])->save();
        }
      }
    }

    //保存单词测试的答案
    public function setUserWordtestanswer(){
      $id=I("id");
      $quesid=I("quesid");
      $useranswer=I("useranswer");
      $user_recite_word=M("user_recite_word");
      //查询用户的试题
      $result=$user_recite_word->where("id=%d",$id)->find();
      //修改并且判断答案
      $questions=json_decode($result["questions"],true);
      $questions[$quesid]["useranswer"]=$useranswer;
      if($useranswer==$questions[$quesid]["answer"]){
        $questions[$quesid]["iserror"]=1;
      }else{
        $questions[$quesid]["iserror"]=0;
      }

      //判断分数
      $score=$result["score"]+$questions[$quesid]["iserror"];
      $user_recite_word->questions=json_encode($questions);
      $user_recite_word->score=($score);
      $result=$user_recite_word->where("id=%d",$id)->save();
      $arr["iserror"]=$questions[$quesid]["iserror"];
      $this->ajaxReturn($arr);
    }



    //英语语音评测问题
    public function getTestScore(){
      foreach($_REQUEST as $k=>$v){
          file_put_contents('hahaha.txt','key:'.$k.'|value:'.$v.PHP_EOL,FILE_APPEND);
      }
      $username=cookie("username");
      $areacode=cookie("areacode");
      $classid=cookie("classid");
      $schoolid=cookie("schoolid");
      $truename=cookie("truename");
      $filename=I("filename");
      $content=I("content");
      $chapterid=I("chapterid/s");
      $contentid=I("contentid/d",0);
      $ks_code=I("ks_code/s",0);
      $type=I("type/d",0);
      $recordpath = C('recordpath');
      //$filepath =$recordpath.$filename;
      $result=smartstudy($filename,$content,$type);
      if(empty($result)){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
      }else{
        try{
          $score = round($result["data"]["score"],1);
          //出问题的时间直接写到文件中
        }catch(Exception $e){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
        }
      }

      //$score = 50;
      $readtimes=0;
      $maxscore=0;
      //查询用户做的是哪一个批次的
      $userread=M("user_read");
      $userrs=$userread->where("username='%s' and areaid='%s' and ks_code='%s' and submittime is null",$username,$areacode,$ks_code)->order("id desc")->find();
      //查询这个批次中的单词是否回答
      $userinfo=M("user_read_info")->where("username='%s' and areaid='%s' and ks_code='%s' and readid=%d and type=%d and contentid=%d and chapterid=%d",$username,$areacode,$ks_code,$userrs["id"],$type,$contentid,$chapterid)->order("id desc")->find();
      //进行数据库的插入操作
      $student_tts=M("user_read_info");
      $student_tts->readid=$userrs["id"];
      $student_tts->username=$username;
      $student_tts->areaid=$areacode;
      $student_tts->schoolid=$schoolid;
      $student_tts->classid=$classid;
      $student_tts->truename=$truename;
      $student_tts->filename=$filename;
      $student_tts->chapterid=$chapterid;
      $student_tts->contentid=$contentid;
      $student_tts->content=$content;
      $student_tts->ks_code=$ks_code;
      $student_tts->type=$type;
      $student_tts->score=$score;
      $student_tts->usertype=cookie("usertype");
      if(empty($userinfo)){
        $student_tts->maxscore=$score;
        $maxscore=$score;
        $student_tts->readtimes=1;
        $readtimes=$readtimes+1;
        $id=$student_tts->add();
      }else{
        if($score>$userinfo["maxscore"]){
          $student_tts->maxscore=$score;
          $maxscore=$score;
        }else{
          $maxscore=$userinfo["maxscore"];
        }
        $student_tts->readtimes=$userinfo["readtimes"]+1;
        $readtimes=$userinfo["readtimes"]+1;
        if($userinfo["readtimes"]<C('readtimes')){
           $student_tts->where("id=%d",$userinfo["id"])->save();
        }
      }
      $arr["score"]=$score;
      $arr["id"]=$id;
      $arr["filename"]=$filename;
      $arr["maxscore"]=$maxscore;
      //跟新一下数据库中的最高分
      $userread->where("id=%d",$userrs["id"])->setField("maxscore",$maxscore);
      $userread->where("id=%d",$userrs["id"])->setField("filename",$filename);
      $msg="";
      if($readtimes==C('readtimes')){
        $msg="";
      }else{
        $msg="您还有".(C('readtimes')-$readtimes)."次测评机会";
      }
      $arr["msg"]=$filename;
      $arr["readtimes"]=C('readtimes')-$readtimes;
      $this->ajaxReturn($arr);
    }


     //学生提交作业接口
    public function stupublish(){
      $paper_id=I("paper_id");
      $quizid=I("quizid");
      $studentid=cookie("username");
      $schoolclass=cookie("classid");
      $tms=I("tms");
      //查看作业问题
      $hrs=M("exams")->where("id=%d",$paper_id)->find();
      //计算分数
      $score=0.0;
      $sql="select sum(score) as score from engs_user_quiz_history where quizid='".$quizid."' group by examid";
      $srs=M()->query($sql);
      $submittime=Date("Y-m-d H:i:s");
      //查询学生做作业的时间
      $result=M("user_quiz_list")->where("id=%d",$quizid)->select();
      $dotime=$result[0]["dotime"];
      M("user_quiz_list")->where("id=%d",$quizid)->setField("submittime",$submittime);
      M("user_quiz_list")->where("id=%d",$quizid)->setField("score",$srs[0]["score"]);
      //更新是否满分
      $studentrs=M("user_quiz_history")->where("quizid='%d' and iscorrect=1",$quizid)->select();
      if(!empty($studentrs)&&count($studentrs)==$hrs["quescount"]){
        //表示满分
        M("user_quiz_list")->where("id='%d'",$quizid)->setField("ismark","1");
      }
      $studentname=cookie("trurname");
      //将用户的答案表进行更新
      M("user_quiz_history")->where("quizid='%d'",$quizid)->setField("issubmit","1");
      M("user_quiz_history")->where("quizid='%d'",$quizid)->setField("truename",$studentname);
      $ret["msg"]="发布成功";
      $ret["state"]="1";
      $this->ajaxReturn($ret);
    }

    public function unit(){
      //设置学科的cookie信息
      $subjectid=I("subjectid");
      $gradeid=I("gradeid");
      cookie("subjectid",$subjectid);
      cookie("gradeid",$gradeid);
      $backUrl=cookie("backUrl");
      $this->assign("backUrl",$backUrl);
      $this->display("unit");

    }

    //wordunit的单元
    public function getUnitData(){
      //$unit=D("rms_unit");
      $moduleid=I("moduleid");
      $learnways=getmodule($moduleid);
      //获取秘籍
      $username=cookie("username");
      $gradeid=cookie("gradeid");
      $subjectid=cookie("subjectid");
      $configrs = getUserConfig($subjectid,$gradeid,"0","0",$this->user);
      $volumnid = $configrs["termid"];
      $versionid = $configrs["versionid"];
      // $volumnid=cookie("termid");
      // $versionid=cookie("versionid");
      $type=$learnways["type"];
      $sql="select t.style,t.ks_code,t.ks_name as ks_name,t.is_unit,CASE WHEN s.ks_code IS NULL THEN 0 ELSE s.count END AS isdo,case when userdata is null then 0 else userdata end as userdata ";
      if($type=='0'){
          //表示的是单词
          $sql=$sql.",(select count(*) from engs_word where ks_code=t.ks_code and isdel=1) as count ";

      }else if($type=='1'){
          //表示的是课文
          $sql=$sql.",(select count(*) from engs_text_chapter where ks_code=t.ks_code and isdel=1) as count ";

      }else if($type=='2'){
          //表示的是听力训练
          $sql=$sql.",(select count(*) from engs_exams where ks_code=t.ks_code and isdel=1 and state=4) as count ";

      }else if($type=='3'){
          $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }else if($type=='4'){
          $sql=$sql.",(case when t.word is null or t.word='' then 0 else 1 end) as count ";
      }else if($type=='5'){
          $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }
      $sql=$sql."from db_yuwen.yw_unit t LEFT JOIN ";
      //$sql=$sql."(SELECT DISTINCT ks_code FROM engs_user_modules_unit_log WHERE username = '".$username."'  AND moduleid = '".$moduleid."') s";
      $sql=$sql."(SELECT count(*) as count,ks_code,sum(case when username='".$username."' then 1 else 0 end) as userdata  FROM db_english.engs_user_modules_unit_log WHERE  moduleid = '".$moduleid."' group by ks_code) s";
      $sql=$sql." ON t.`ks_code` = s.ks_code where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' ";
      //$type课文朗读3 生字听写4 古文诗词5 t.style 课文1 古文2 拼音3
      if($type=='3'){
          $sql=$sql." ";
      }else if($type=='4'){
          $sql=$sql." ";
      }else if($type=='5'){
          $sql=$sql." and t.style=2 ";
      }
      $sql=$sql." order by display_order";
      $result=M()->query($sql,$gradeid,$volumnid,$versionid,$subjectid);
      //$result=$unit->getUnitListData($gradeid,$volumnid,$versionid,$subjectid,$username,$moduleid,$learnways["type"]);
      $imgrs=M("db_yuwen.version_img","yw_")->where("r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'",$gradeid,$volumnid,$versionid,$subjectid)->field("pic_path as pic")->find();
      $infosql="select (select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='edition' and DETAIL_CODE=r_version) as versionname,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='grade' and DETAIL_CODE=r_grade) as gradename,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='volume' and DETAIL_CODE=r_volume) as volumename,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='subject' and DETAIL_CODE=r_subject) as subjectname ";
      $infosql=$infosql." from db_yuwen.yw_unit where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'";
      $infors=M()->query($infosql,$gradeid,$volumnid,$versionid,$subjectid);
      $arr["img"]=PROTOCOL."://".C("resource_domain")."/yylmp3/".$imgrs["pic"];
      $arr["versionname"]=$infors[0]["versionname"];
      $arr["gradename"]=$infors[0]["gradename"];
      $arr["volumename"]=$infors[0]["volumename"];
      $arr["subjectname"]=$infors[0]["subjectname"];
      //$name=$unit->getVersionById($gradeid,$volumnid,$versionid,$subjectid);
      $result["bookinfo"]=$arr;
      $result["ways"]=$learnways;
      $this->ajaxReturn($result);
    }

    //课文朗读
    public function alound(){
      $subjectid=I("subjectid");
      $gradeid=I("gradeid");
      if(!empty($subjectid)){
        cookie("subjectid",$subjectid);
      }else{
        cookie("subjectid","0001");
      }
      if(!empty($gradeid)){
        cookie("gradeid",$gradeid);
      }
      $backUrl=cookie("backUrl");
      $this->assign("backUrl",$backUrl);
      $this->assign("function","alound");
      $this->display("unit");
    }

    //生字听写
    public function word(){
      $subjectid=I("subjectid");
      $gradeid=I("gradeid");
      if(!empty($subjectid)){
        cookie("subjectid",$subjectid);
      }else{
        cookie("subjectid","0001");
      }
      if(!empty($gradeid)){
        cookie("gradeid",$gradeid);
      }
      $backUrl=cookie("backUrl");
      $this->assign("backUrl",$backUrl);
      $this->assign("function","word");
      $this->assign("page","word");
      $this->display("unit");
    }

    //古文诗词
    public function poem(){
      $subjectid=I("subjectid");
      $gradeid=I("gradeid");
      if(!empty($subjectid)){
        cookie("subjectid",$subjectid);
      }else{
        cookie("subjectid","0001");
      }
      if(!empty($gradeid)){
        cookie("gradeid",$gradeid);
      }
      $backUrl=cookie("backUrl");
      $this->assign("backUrl",$backUrl);
      $this->assign("function","poem");
      $this->assign("page","text");
      $this->display("unit");
    }

    //设置版本
    public function getCourse(){
        $subjectid=cookie("subjectid");
        $versionid=cookie("versionid");
        $termid=cookie("termid");
        $gradeid=cookie("gradeid");



        //$result=$rms_unit->getCourse($subjectid,$versionid,$gradeid,$termid);
        $sql = 'SELECT v.r_grade,v.r_volume,v.r_version,(SELECT detail_name_short FROM db_yuwen.yw_rms_dictionary WHERE dictionary_code="edition" AND detail_code=v.r_version) AS c4,s.pic_path FROM  db_yuwen.yw_unit v ';
        $sql .=' left join db_yuwen.yw_version_img s on v.r_grade=s.r_grade and v.r_version=s.r_version and v.r_subject=s.r_subject and ';
        $sql .=' v.r_volume=s.r_volume WHERE v.r_grade = "%s" and v.r_subject="%s" and s.pic_path is not null';
        $sql .=' group by v.r_grade,v.r_volume,v.r_version ORDER BY v.r_version';
        //$versionsql="Select  r_version as detail_code,(SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_version AND dictionary_code='edition') as detail_name from db_yuwen.yw_unit where r_subject='".$subjectid."' group by r_version";
        //$verrs=M()->query($versionsql);
        $verrs=M("db_yuwen.rms_dictionary","yw_")->where("dictionary_code='grade' and DETAIL_CODE!='0010'")->field("detail_code,detail_name")->order("detail_order")->select();
        foreach ($verrs as $key => $value) {
            $gradeterm = M()->query($sql,$value["detail_code"],$subjectid);
            foreach ($gradeterm as $k => $v) {
                $gradeterm[$k]["pic_path"]=PROTOCOL."://".C("resource_domain")."/yylmp3/".$v["pic_path"];
                //取默认配置的版本年级学期设置为选中
                if ($v["r_version"] == $versionid && $v["r_grade"] == $gradeid && $v["r_volume"] == $termid) {
                    $gradeterm[$k]["checked"] = 1;
                }else{
                    $gradeterm[$k]["checked"] = 0;
                }
            }
            $verrs[$key]["termversion"] = $gradeterm;
        }
        $this->ajaxReturn($verrs);
    }

    //英语100句子的单元
    public function getEnUnitData(){
      $moduleid=I("moduleid/d",0);
      $learnways=getmodule($moduleid);
      //获取秘籍
      $username=cookie("username");
      $gradeid=cookie("gradeid");
      $subjectid=cookie("subjectid");
      //getUserConfig($subjectid,$gradeid,$username);
      $volumnid=cookie("termid");
      $versionid=cookie("versionid");
      $type=$learnways["type"];
      $sql="SELECT t.id AS ks_code,t.enchapter AS ks_name,'0' AS is_unit,CASE WHEN s.ks_code IS NULL THEN 0 ELSE s.count END AS isdo,";
      $sql=$sql."CASE WHEN userdata IS NULL THEN 0 ELSE userdata END AS userdata,'1' AS COUNT FROM engs_contact_chapter t LEFT JOIN ";
      $sql=$sql."(SELECT COUNT(*) AS COUNT,ks_code,SUM(CASE WHEN username = '".$username."' THEN 1 ELSE 0 END) AS userdata FROM ";
      $sql=$sql."db_english.engs_user_modules_unit_log WHERE moduleid = ".$moduleid." GROUP BY ks_code) s ON t.id = s.ks_code WHERE t.isdel=1 group by t.id ORDER BY sortid ";
      $result=M()->query($sql);
      $arr["img"]="";
      $arr["versionname"]="";
      $arr["gradename"]="";
      $arr["volumename"]="";
      $arr["subjectname"]="";
      $result["bookinfo"]=$arr;
      $result["ways"]=$learnways;
      $this->ajaxReturn($result);
    }

    //英语100句子的课文
    public function getTextsDataByUnit(){
      $ks_code=I("ks_code/d",0);
      $chaptersql="select id,enchapter as chapter from engs_contact_chapter where id='%s' and isdel=1 order by sortid";
      $chapterresult=M()->query($chaptersql,$ks_code);
      $audio=array();
      $contact_mp3_path = PROTOCOL."://".C('contact_mp3_path');
      $contact_mp3_path = getHttpUrl($contact_mp3_path);
      foreach($chapterresult as $key=>$value){
        $textsql="select *,'mp3' AS format,'1' AS size,content as encontent,'' as enbefore,concat(tts_mp3,'.mp3') as name,concat('".$contact_mp3_path."',left(tts_mp3,2),'/',tts_mp3,'.mp3') as url from engs_contact_text where chapterid=%d and isdel=1 order by sortid";
        $textresult=M()->query($textsql,$value["id"]);
        $chapterresult[$key]["texts"]=$textresult;
        array_push($audio,$textresult);
      }
      $result=$chapterresult;
      //获取课文下面所有的音频
      $ret["data"]=$result;
      $ret["mp3list"]=$audio[0];
      $this->ajaxReturn($ret);
    }

    //地址的分发跳转
    public function webjump(){
      $areacode=cookie("localAreaCode");
      $username=cookie("username");
      $usertype=cookie("usertype");
      $areacode=cookie("areacode");
      $schoolid=cookie("schoolid");
      $classid=cookie("classid");
      $truename=cookie("truename");
      $gradeid=I("gradeid");
      cookie("gradeid",$gradeid);
      $subjectid=I("subjectid");
      cookie("subjectid",$subjectid);
      $moduleid=I("moduleid","");
      $ks_code=I("ks_code","0");
      $ks_name=I("ks_name","0");
      $callbackURL = I("backUrl/s","0");
      if($callbackURL == "0"){
          $callbackURL = PROTOCOL."://".$_SERVER['HTTP_HOST']."/Subject/Index/entrance";
      }
      else{
        $callbackURL = urlencode($callbackURL);
      }
      setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
      $areacode=cookie("localAreaCode");
      $rs=M("yjt_url")->where("areacode='%s' and url_type='vfs_url'",$areacode)->find();
      if($usertype=='2'){
        //http://vfshenan.czbanbantong.com/A01/lib/defaultstyle/2018yjt/03/shici/index.html?localAreaCode=25.
        redirect(PROTOCOL.'://'.$rs["url"].'/A01/lib/defaultstyle/2018yjt/03/shici/index.html?localAreaCode='.$areacode.'&parentId='.cookie("parentid")."&ksId=0001450901&ut=".cookie("ut")."&callbackURL=".PROTOCOL."://".$_SERVER['HTTP_HOST']."/Subject/Index/entrance");
      }else{
        redirect(PROTOCOL.'://'.$rs["url"].'/A01/lib/defaultstyle/2018yjt/03/shici/index.html?localAreaCode='.$areacode.'&parentId='.cookie("parentid")."&ksId=0001450901&studentId=".cookie("username")."&ut=".cookie("ut")."&callbackURL=".$callbackURL);
      }

    }


    public function vfsjump(){
      $areacode=cookie("localAreaCode");
      $username=cookie("username");
      $areacode=cookie("areacode");
      $schoolid=cookie("schoolid");
      $classid=cookie("classid");
      $truename=cookie("truename");
      $gradeid=I("gradeid");
      cookie("gradeid",$gradeid);
      $subjectid=I("subjectid",18);
      cookie("subjectid",$subjectid);
      $moduleid=I("moduleid","");
      $ks_code=I("ks_code","0");
      $ks_name=I("ks_name","0");
      setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
      $type=I("typeids/d");
      $areacode=cookie("localAreaCode");
      $backUrl=I("backUrl/s","");
      if(empty($backUrl)){
        $backUrl=PROTOCOL."://".$_SERVER['HTTP_HOST']."/Subject/Index/entrance";
      }
      $rs=M("yjt_url")->where("areacode='%s' and url_type='vfs_url'",$areacode)->find();
      //http://vfshenan.czbanbantong.com/A01/lib/defaultstyle/2018yjt/03/shici/index.html?localAreaCode=25.
      if($type==1){
        $url=PROTOCOL."://".$rs["url"]."/A01/lib/defaultstyle/2017yjt/08/gkzt/html/gkzt/index.html?";
      }else{
        $url=PROTOCOL."://".$rs["url"]."/A01/lib/defaultstyle/2017yjt/08/gkzt/html/gkzt/list.html?";
      }
      redirect($url."localAreaCode=".$areacode."&parentId=".cookie("parentid")."&studentId=".cookie("username")."&ut=".cookie("ut")."&callbackURL=".$backUrl);
    }

    //积分接口
    public function jfInterface(){
      $score=I("score");

    }

    //用户日志记录
    public function setUserLog(){
      $username=cookie("username");
      $areacode=cookie("areacode");
      $schoolid=cookie("schoolid");
      $classid=cookie("classid");
      $truename=cookie("truename");
      //接受用户数据
      $request=I("request");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);
      $gradeid=$request["gradeid"];
      $subjectid=$request["subjectid"];
      $moduleid=$request["moduleid"];
      $ks_code=($request["ks_code"]=='undefined'||$request["ks_code"]==""||$request["ks_code"]==null||$request["ks_code"]=='null')?"0":$request["ks_code"];
      $ks_name=$request["ks_name"];
      if(!empty($moduleid)){
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
        if(!empty($ks_code)||$ks_code=="0"){
          $unit_modules_log=M("unit_module_log");
          $rs=$unit_modules_log->where("ks_code='%s' and moduleid=%d",$ks_code,$moduleid)->find();
          if(empty($rs)){
            $unit_modules_log->ks_code=$ks_code;
            $unit_modules_log->moduleid=$moduleid;
            $unit_modules_log->num=1;
            $unit_modules_log->lastupdatetime=Date("Y-m-d H:i:s");
            $unit_modules_log->add();
          }else{
            $unit_modules_log->num=$rs["num"]+1;
            $unit_modules_log->lastupdatetime=Date("Y-m-d H:i:s");
            $unit_modules_log->where('id=%d',$rs["id"])->save();
          }
        }
      }
    }

    public function setUserBrowserConfig(){
      $gradeid=I("gradeid/s","0001");
      $subjectid=I("subjectid/s","0003");
      $moduleid=I("moduleid/d","0");
      cookie("gradeid",$gradeid);
      cookie("subjectid",$subjectid);
      cookie("moduleid",$moduleid);
    }


    public function getUnitList(){
      $request=I("request");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);
      $subjectid=$request["subjectid"];
      $gradeid=$request["gradeid"];
      $termid=$request["termid"];
      $versionid=$request["versionid"];
      $moduleid=$request["moduleid"];

      if($moduleid == "63" && ceil($gradeid) > 6){
        $gradeid = "0006";
      }
      
      $unit["subjectid"]=$subjectid;
      $unit["gradeid"]=$gradeid;
      $unit["termid"]=$termid;
      $unit["versionid"]=$versionid;
      $module["moduleid"]=$moduleid;
      $learnways=getmodule($moduleid);
      

      if(empty($versionid)){
        $configrs = getUserConfig($subjectid,$gradeid,"0","0",$this->user); 
        $versionid = $configrs["versionid"];
        $termid = $configrs["termid"];
        $unit["versionid"]=$versionid;
        $unit["termid"]=$termid;
      }
      //静态数据文件缓存
      $unitService = new UnitService();
      $result = $unitService ->getBaseUnitList($unit,$module);
      foreach ($result as $key => $value) {
        $ks_name = "";
        $ks_name_arr = split_str($value["ks_name"]);
        if(count($ks_name) > 23){
          foreach ($ks_name_arr as $key => $value) {
            $ks_name.=$value;
          }
          $result[$key]["ks_name"] = $ks_name."...";
        }
       
      }
      
      
      $this->ajaxReturn($result);
    }

    public function getBookInfo(){
      $request=I("request");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);
      $subjectid=$request["subjectid"];
      $gradeid=$request["gradeid"];
      $termid=$request["termid"];
      $versionid=$request["versionid"];
      $moduleid=$request["moduleid"];
      if(empty($versionid)){
        $configrs = getUserConfig($subjectid,$gradeid,"0","0",$this->user);
        $versionid = $configrs["versionid"];
        $termid = $configrs["termid"];
      }
      $learnways=getmodule($moduleid);
      $cache_key = md5("subjectid".$subjectid."gradeid".$gradeid."termid".$termid."versionid".$versionid."moduleid".$moduleid."book");
      $result = S($cache_key);
      if(!$result){
        $unit=D("rms_unit");
        if($subjectid=='0001'){
            $name=$unit->getCnVersionById($gradeid,$termid,$versionid,$subjectid);
        }else if($subjectid=="0003"){
            $name=$unit->getEnVersionById($gradeid,$termid,$versionid,$subjectid);
        }
        $result["ways"] = $learnways;
        $result["bookinfo"]=$name;
        S($cache_key,$result);
      }
      $this->ajaxReturn($result);
    }

    public function updateUserTextStatus(){
      $username=cookie("username");
      $areacode=cookie("areacode");
      $schoolid=cookie("schoolid");
      $classid=cookie("classid");
      $gradeid=cookie("gradeid");
      $truename=cookie("truename");
      $subjectid=cookie("subjectid");
      
      $request = I("data");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);

      $ks_code = $request["ks_code"];
      $ks_name = $request["ks_name"];
      $chapterid = $request["chapterid"];
      $contentid = $request["textid"];
      $gradeid = $request["gradeid"];
      $termid = $request["termid"];
      $versionid = $request["versionid"];

      $configrs = getUserConfig($request["subjectid"],$request["gradeid"],"0","0",$this->user); 
      $termid = $configrs["termid"];

      $usename = $this->user["username"];
      $usertype = $this->user["usertype"];
      $localareacode = $this->user["localareacode"];

      //如果插入之后就不再进行插入
      $userModulesUnitLog=M("user_modules_unit_log");
      $rs = $userModulesUnitLog -> where("username = '%s' and usertype = '%s' and localareacode = '%s' and ks_code = '%s' and chapterid = %d and contentid = %d",$username,$usertype,$localareacode,$ks_code,$chapterid,$contentid) -> find();

      if(!$rs){
        $userModulesUnitLog->gradecode=$this->user["gradeid"];
        $userModulesUnitLog->username=$this->user["username"];
        $userModulesUnitLog->truename=$this->user["truename"];
        $userModulesUnitLog->areacode=$this->user["areacode"];
        $userModulesUnitLog->schoolid=$this->user["schoolid"];
        $userModulesUnitLog->classid=$this->user["classid"];
        $userModulesUnitLog->usergradeid=$this->user["gradeid"];
        $userModulesUnitLog->moduleid=2;
        $userModulesUnitLog->ks_code=$ks_code;
        $userModulesUnitLog->ks_name=$ks_name;
        $userModulesUnitLog->subjectid="0003";
        $userModulesUnitLog->chapterid=$chapterid;
        $userModulesUnitLog->contentid=$contentid;
        $userModulesUnitLog->localareacode=$this->user["localareacode"];
        $userModulesUnitLog->usertype=$this->user["usertype"];
        $userModulesUnitLog->gradeid = $gradeid;
        $userModulesUnitLog->termid = $termid;
        $userModulesUnitLog->versionid = $versionid;
        $userModulesUnitLog->add();
      }
      //setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);             
    }


    public function treeunit(){
      $moduleid = cookie("moduleid");
      $areaCode = cookie("areacode");
      // echo $areaCode;exit;
      $backUrl = cookie("backUrl");
      $localAreaCode = cookie("localAreaCode");
      $this->assign("backUrl",$backUrl);
      $this->assign("localAreaCode",$localAreaCode);
      // echo $moduleid;exit;
      if($areaCode == "1.1.13" && $moduleid == "63"){
        $this->display('React/dflist');
      }
      else{
        $this->display('React/list');
      }
      
    }


    public function index(){
      $matrix=array(array(0,0.333,0.666,1),array(0,0.333,0.666,1),array(0,0.333,0.666,1));

    }

}
