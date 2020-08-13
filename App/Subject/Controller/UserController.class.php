<?php

namespace Subject\Controller;

use Think\Controller;
use Subject\Service\UserService;

/**
 * Index控制器
 */
class UserController extends CheckController {

    private $book = array();
    private $module = array();
    private $unit = array();

    public function _initialize(){
        parent::_initialize();
        $request=I("request");
        if(!empty($request)){
            $request = rtrim($request, '"');
            $request = ltrim($request, '"');
            $request = str_replace('&quot;', '"', $request);
            $request = json_decode($request,true);
            $this->book["subjectid"] = $request["subjectid"];
            $this->book["gradeid"] = $request["gradeid"];
            $this->book["termid"] = $request["termid"];
            $this->book["versionid"] = $request["versionid"];
            $this->module["moduleid"] = $request["moduleid"];
            $this->unit["ks_code"] = $request["ks_code"];
        }
    }

    public function index() {
        $this->display("index");
    }

    //设置用户信息
    public function setUserGradeVersion(){
    	$grade=I("grade");
    	$volume=I("volume");
    	$version=I("version");
    	cookie("gradeid",$grade);
        $subjectid=cookie("subjectid");
    	cookie("termid",$volume);
    	cookie("versionid",$version);
    	$username=cookie("username");
    	$areacode=cookie("areacode");
    	$config=M("config");
        $config->subjectid=$subjectid;
    	$config->gradeid=$grade;
    	$config->termid=$volume;
    	$config->versionid=$version;
    	$config->userid=$username;
    	$config->areacode=$areacode;
        $config->usertype=cookie("usertype");
    	$config->add();
    }

    //生词本
    public function vocabulary(){
        $this->display('vocabulary');
    }

    //查询生词本列表
    public function getVocabularyList(){
        $username=cookie("username");
        $sql="SELECT t.ks_code,t.id,t.wordid,ew.word,bw.usmark,bw.ukmark,bw.ukmp3,bw.usmark,bwe.morphology,bwe.explains,bw.isword ";
        $sql.="FROM  engs_word_book t, engs_word ew ,engs_base_word bw ,engs_base_word_explains bwe ";
        $sql.=" where ew.id = t.wordid  AND t.userid = '%s'  AND ew.base_wordid=bw.id AND ew.base_explainsid=bwe.id AND t.isuse=1 ";
        $sql.=" ORDER BY t.studytime desc";
        $result=M()->query($sql,$username);
        $this->ajaxReturn($result);
    }


    public function delUserGameVocabulary(){
        $bookid=I("bookid");
        M("user_word_game_book")->where("id=%d",$bookid)->setField("isdel",0);
    }


    //设置单词本
    public function delUsernameVocabulary(){
        $id=I("id");
        $wordbook=M("word_book");
        $wordbook->where("id=%d",$id)->setField("isuse","0");
    }

    public function getClassRank(){
        $ks_code=I("ks_code");
        $classid=cookie("classid");
        $mod=I("mod");
        $model=D($mod);
        $func=I("func");
        $result=$model->$func($ks_code,$classid);
        $arr["username"]=cookie("username");
        $arr["truename"]=cookie("truename");
        $arr["ranklist"]=$result;
        $this->ajaxReturn($arr);
    }


    // //班级排名
    // public function getReciteWordClassRank(){
    //     $ks_code=I("ks_code");
    //     $username-cookie("username");
    //     $classid=cookie("classid");
    //     $word=D("word");
    //     $result=$word->getReciteWordClassRank($ks_code,$classid);
    //     $arr["username"]=cookie("username");
    //     $arr["truename"]=cookie("truename");
    //     $arr["ranklist"]=$result;
    //     $this->ajaxReturn($arr);
    // }



    //正则替换
    public function test(){
        //被匹配的html代码
        $html='<div class="goods"><a href="http://url1111" target="_blank">asdfasd<img data-ks-lazyload="http://1111.jpg" alt="alt1111" width="" height=""/></a></div><div class="goods"><a href="http://url2222" target="_blank"><img data-ks-lazyload="http://2222.jpg" alt="alt2222" width="" height=""/></a></div><div class="goods"><a href="http://url3333" target="_blank"><img data-ks-lazyload="http://3333.jpg" alt="alt3333" width="" height=""/></a></div>';

        //去掉换行、制表等特殊字符，可以echo一下看看效果
        $html=preg_replace("/[\t\n\r]+/","",$html);


        //匹配表达式，注意两点，一是包含在'/ /'里面，再就是/要做转义处理成\/
        $partern='/>([^<>]+)</';

        //匹配结果
        preg_match_all($partern,$html,$result);
        var_dump($result);
        $html=preg_replace($partern,"\\1",$html);
        //打印结果
        var_dump($html);
    }

    //用户学习记录
    public function setUserLearnLog(){
        $contentid=I("contentid/d");
        $username=cookie("username");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $areaid=cookie("areacode");
        $subjectid=cookie("subjectid");
        $moduleid=I("moduleid");
        $truename=cookie("truename");
        $ks_code=I("ks_code/s");
        $chapterid=I("chapterid/d");
        $typeid=I("typeid/d");
        $source=I("source/d");
        $learn_log=M("user_learn_log");
        //source中的意义0表示读单词 1表示背单词  2表示读课文 3表示读单词 4表示读课文 5表示听力训练
        if(!empty($contentid)){
            $learn_log->username=$username;
            $learn_log->subjectid=$subjectid;
            $learn_log->truename=$truename;
            $learn_log->schoolid=$schoolid;
            $learn_log->classid=$classid;
            $learn_log->areaid=$areaid;
            $learn_log->contentid=$contentid;
            $learn_log->moduleid=$moduleid;
            $learn_log->ks_code=$ks_code;
            $learn_log->chapterid=$chapterid;
            $learn_log->typeid=$typeid;
            $learn_log->source=$source;
            $learn_log->addyear=Date("Y");
            $learn_log->addmonth=Date("m");
            $learn_log->addday=Date("d");
            $learn_log->usertype=cookie("usertype");
            $learn_log->add();
        }
    }

    //设置是否结束
    public function setUserReadOver(){
        //设置用户放弃这次作答
        $rs= M("user_read")->where("username='%s' and submittime is null",cookie("username"))->order("id desc")->find();
        M("user_read")->where("id=%d",$rs["id"])->setField("isover","1");
    }
    //设置背诵单词结束
    public function setUserReciteOver(){
        //设置用户放弃这次作答
        $rs= M("user_recite_word")->where("username='%s' and isover=0 and submittime is null",cookie("username"))->order("id desc")->find();
        M("user_recite_word")->where("id=%d",$rs["id"])->setField("isover","1");
    }

    //设置听力训练是够结束
    public function setUserExamsOver(){
        //设置用户放弃这次作答
        $quizid=I("quizid");
        $rs= M("user_quiz_list")->where("id='%d'",$quizid)->setField("isover","1");
    }


    //获取用户口语训练数据
    public function getUserReadData(){
        //设置用户放弃这次作答
        $chapterid=I("chapterid/d");
        $ks_code=I("ks_code/s","");
        $username=cookie("username");
        $word=D("word");
        $rs=$word->getUserReadData($username,$chapterid,$ks_code);

        $downlist=array();
        $userlist=array();
        foreach($rs as $key=>$value){
            if($chapterid==0){
                //$rs[$key]["mp3"]=$value["mp3"].".mp3";
                $down["name"]=$value["mp3"];
            }else{
                $rs[$key]["mp3"]=$value["mp3"].".mp3";
                $down["name"]=$value["mp3"].".mp3";
            }
            //$down["name"]=$value["mp3"];
            $down["format"]="mp3";
            $down["size"]=1;
            $user["name"]=$value["filename"];
            $user["format"]="mp3";
            $user["size"]=1;
            $user["url"]=C("user_record_path").$value["filename"];
            if($chapterid==0){
                $down["url"]=PROTOCOL."://".C("word_mp3_path").$value["mp3"];
                $down["url"] = getHttpUrl($down["url"]);
            }else{
                $down["url"]=PROTOCOL."://".C("text_mp3_path").substr($value["mp3"],0,2)."/".$value["mp3"].".mp3";
                $down["url"] = getHttpUrl($down["url"]);
            }
            array_push($downlist, $down);
            array_push($userlist, $user);
        }
        $arr["data"]=$rs;
        $arr["downlist"]=$downlist;
        $arr["userlist"]=$userlist;
        $this->ajaxReturn($arr);
    }

    //获取用户口语训练历史
    public function getUserReadHistoryData(){
        //设置用户放弃这次作答
        $username=cookie("username");
        $word=D("word");
        $rs=$word->getUserReadHistoryData($username);
        $this->ajaxReturn($rs);
    }

    //获取用户听力训练历史数据
    public function getUserListenHistoryData(){
        //设置用户放弃这次作答
        $username=cookie("username");
        $exam=D("exams");
        $rs=$exam->getUserListenHistoryData($username);
        $this->ajaxReturn($rs);
    }


    //学生作答情况统计
    public function getStudentSituation(){
        $quizid=I("quizid");
        $eqrs=array();
        $sql="(select * from (SELECT t.questionsid AS id,(CASE WHEN s.username IS NOT NULL  THEN 1  ELSE 0  END) AS isdo,s.iscorrect,'1' AS hwtype,st.question_score as quesscore,s.score,'1' as typeid FROM engs_exams_questions_answer t LEFT JOIN engs_exams_questions e  ON t.`questionsid` = e.`id` LEFT JOIN (SELECT  z.* FROM engs_user_quiz_history z  WHERE  z.quizid=".$quizid.") s  ON t.id = s.answerid ,engs_user_quiz_list eq ,engs_exams_stem st  WHERE st.id=e.stemid and eq.paper_id=st.examsid AND t.isdel = 1 AND e.`isdel`=1 AND eq.`id`=".$quizid." ORDER BY e.paper_sortid) d) ";
        $result=M()->query($sql);
        foreach($result as $key=>$value){
            $temp=array();
            $temp["id"]=$value["id"];
            $temp["isdo"]=$value["isdo"];
            $temp["score"]=$value["score"];
            $temp["quesscore"]=$value["quesscore"];
            array_push($eqrs,$temp);
        }
        $arr["eq"]=$eqrs;
        $this->ajaxReturn($arr);
    }


     /**
     * 学习记录列表获取
     */
    /**
     * 学习记录列表获取
     */
    public function record_list() {
        $model = M();
        $studyarr = array();
        $username = cookie('username');
        //$subjectid = I('subjectid');
        $start=I("start");
        $sql='SELECT ks_name as unit,(SELECT  detail_name FROM engs_rms_dictionary WHERE dictionary_code = "subject" AND detail_code = t.subjectid) AS SUBJECT,t.subjectid,SUBSTR(t.`datetime`,1,4) AS addyear,CONCAT(SUBSTR(t.`datetime`,1,4), SUBSTR(t.`datetime`,6,2)) AS MONTH,SUBSTR(t.`datetime`,6,2) AS addmonth,SUBSTR(t.`datetime`,9,2) AS addday,SUBSTR(t.`datetime`,12,5) AS seconds,(SELECT  title FROM engs_menu_modules WHERE engs_menu_modules.id = moduleid) AS module,COUNT(*) AS num FROM engs_user_modules_unit_log t WHERE  t.`username` = "'.$username.'" GROUP BY SUBSTR(t.`datetime`,1,4),SUBSTR(t.`datetime`,6,2),SUBSTR(t.`datetime`,9,2),SUBSTR(t.`datetime`,12,5),t.moduleid,t.ks_code HAVING module IS NOT NULL ORDER BY t.`datetime` DESC limit '.($start*16).',16';
        //$sql='SELECT (select c1 from engs_rms_unit where ks_code=t.ks_code) as unit,(select ks_name from engs_rms_unit where ks_code=t.ks_code) as unit,(select detail_name from engs_rms_dictionary where dictionary_code="subject" and detail_code=t.subjectid) as subject,t.subjectid,t.`addyear`,concat(t.`addyear`,t.`addmonth`) as month,t.`addmonth`,t.`addday`,(select title from engs_menu_modules where engs_menu_modules.id=moduleid) as module,count(distinct contentid) as num FROM engs_user_learn_log t WHERE t.`username` = "'.$username.'" and t.subjectid="'.$subjectid.'" GROUP BY t.`addyear`,t.`addmonth`,t.`addday`,t.moduleid,t.ks_code ORDER BY concat(t.`addyear`,t.`addmonth`,t.addday) DESC';
        //echo $sql;exit;
        $resultinfo = $model->query($sql);
        $this->ajaxReturn($resultinfo);
    }


    //二维数据的合并
    private function MergeArray($data,$begin,$end,$index){
        if($end!==0){
            if($begin<($end-1)){
                $middle=ceil(($begin+$end)/2);
                $leftArr=$this->MergeArray($data,$begin,$middle,$index);
                $rightArr=$this->MergeArray($data,$middle+1,$end,$index);
                $arr=array();
                foreach($leftArr as $key=>$value){
                    $rows=0;
                    if(is_array($value[0])){
                        $rows=1;
                    }
                    if($rows==0){
                        $arr[$key][]=$value;
                    }else{
                        foreach($value as $itemk=>$itemv){
                            $arr[$key][]=$itemv;
                        }
                    }
                }
                foreach($rightArr as $key=>$value){
                    if(isset($arr[$key])){
                        $rows=0;
                        if(is_array($value)){
                            $rows=1;
                        }
                        if($rows==0){
                            $arr[$key][]=$value;
                        }else{
                            foreach($value as $itemk=>$itemv){
                                $arr[$key][]=$itemv;
                            }
                        }
                    }else{
                        $rows=0;
                        //只需要判断一个就可以了
                        if(is_array($value)){
                            $rows=1;
                        }
                        if($rows==0){
                            $arr[$key][]=$value;
                        }else{
                            foreach($value as $itemk=>$itemv){
                                $arr[$key][]=$itemv;
                            }
                        }
                    }
                }
                return $arr;
            }else if($begin==($end-1)){
                if($data[$begin][$index]==$data[$end][$index]){
                    //进行合并
                    $arr=array();
                    array_push($arr,$data[$begin]);
                    array_push($arr,$data[$end]);
                    $ret[$data[$begin][$index]]=$arr;
                }else{
                    $ret[$data[$begin][$index]][]=$data[$begin];
                    $ret[$data[$end][$index]][]=$data[$end];
                }
                return $ret;
            }else if($begin==$end){
                $ret[$data[$begin][$index]][]=$data[$begin];
                return $ret;
            }
        }else{
            $ret[$data[$begin][$index]][]=$data[$begin];
            return $ret;
            //return $data;
        }

    }


    //获取用户设置的版本
    public function getUserVersionHistoryData(){
        $username=cookie("username");
        $subjectid=cookie("subjectid");
        if(empty($subjectid)){
            $sql="select * from (SELECT gradeid,versionid,termid,subjectid,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='grade' AND detail_code=gradeid) AS grade,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='edition' AND detail_code=versionid) AS VERSION,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='volume' AND detail_code=termid) AS term,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='subject' AND detail_code=subjectid) AS SUBJECT,max(configtime) as configtime FROM engs_config WHERE  userid = '".$username."' GROUP BY gradeid,versionid,termid,subjectid)  s  ORDER BY s.configtime DESC  LIMIT 10 ";
        }else{
            $sql="select * from (SELECT gradeid,versionid,termid,subjectid,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='grade' AND detail_code=gradeid) AS grade,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='edition' AND detail_code=versionid) AS VERSION,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='volume' AND detail_code=termid) AS term,(SELECT detail_name FROM engs_rms_dictionary WHERE dictionary_code='subject' AND detail_code=subjectid) AS SUBJECT,max(configtime) as configtime FROM engs_config WHERE subjectid = '".$subjectid."' AND userid = '".$username."' GROUP BY gradeid,versionid,termid,subjectid) s  ORDER BY s.configtime DESC  LIMIT 10 ";
        }
        $rs=M()->query($sql);
        $this->ajaxReturn($rs);
    }

    //获取用户版本信息
    public function getUserBookinfo(){
        $arr["username"]=cookie("username");
        $arr["gradeid"]=cookie("gradeid");
        $arr["subjectid"]=cookie("subjectid");
        getUserConfig(cookie("subjectid"),cookie("gradeid"),cookie("username"));
        $arr["versionid"]=cookie("versionid");
        $arr["termid"]=cookie("termid");
        $this->ajaxReturn($arr);
    }

    //学生进行生词本测试
    public function getUserReciteQuestionsData(){
        $word=D("word");
        $ks_code=I("ks_code");
        $username=cookie("username");
        $areaid=cookie("areacode");
        //查询单元的单词
        $data = stripslashes($_REQUEST["data"]);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $result = json_decode($data,true);
        $questions=array();
        $downlist=array();
        //查询用户背单词的记录
        foreach($result as $key=>$value){
            $graders=M("rms_unit")->where("ks_code='%s'",$value["ks_code"])->field("r_grade")->find();
            $gradeid=$graders["r_grade"];
            //获取单词的试题
            $question=$word->getReciteWordQuestion($gradeid,$value["id"],0);
            //获取问题
            $questions[$key]["question"]=$question;
            $down["name"]=$question["mp3"];
            $down["format"]="mp3";
            $down["size"]=1;
            $down["url"]=PROTOCOL."://".C("word_mp3_path").$question["mp3"];
            $down["ks_code"]=$value["ks_code"];
            $downlist[]=$down;
            //array_push($downlist, $down);
        }
        $arr["questions"]=$questions;
        $arr["downlist"]=$downlist;
        $this->ajaxReturn($arr);
    }


    public function userrecord(){
        $gradeid=I("gradeid");
        cookie("gradeid",$gradeid);
        $this->display("userrecord");
    }


    //用户设置版本
    public function setUserConfig(){
        $user=D("user","Service");
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $gradeid=$request["gradeid"];
        $termid=$request["termid"];
        $versionid=$request["versionid"];
        $subjectid=$request["subjectid"];
        //直接写一下cookie中的gradeid
        cookie("gradeid",$gradeid);
        cookie("versionid",$versionid);
        cookie("termid",$termid);
        $user->setUserConfig($gradeid,$subjectid,$termid,$versionid,$this->user);
    }


    //获取自己的排名
    // public function getMyWordRankByUser(){
    //     $user=D("User","Service");
    //     $ret=$user->getMyWordRankByUser($this->user);
    //     $this->ajaxReturn($ret);
    // }


    // //获取自己的排名
    // public function getWordRankList(){
    //     $start=I("start/d");
    //     $num=I("num/d");
    //     $user=D("User","Service");
    //     $ret=$user->getWordRankList($start,$num);
    //     $this->ajaxReturn($ret);
    // }

    //获取自己的排名
    public function getMyWordRankByUser($user){
        $username = $user["username"];
        $usertype = $user["usertype"];
        $localareacode = $user["localareacode"];
        $my = array();
        if($usertype == 2){
            //教师
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "2" AND username="%s" AND localareaid="%s" ';
        }else{
            //学生或者家长
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "4" AND username="%s" AND localareaid="%s"';
        }
        $data_user = M()->query($sql_user,$username,$localareacode);
        $schoolid = $data_user[0]['schoolid'];
        if(empty($classid)){
            $classid = $data_user[0]['classid'];
        }
        //判断是否有多个班级
        if(count($data_user)>1){
            $hasClass = true;
            $className = $data_now_class_name[0]['classname'];
        }else{
            $hasClass = false;
            $className = $data_user[0]['classname'];
        }

        $my['hasClass'] = $hasClass;
        $my['classname'] = $className;
        $my['truename'] = $data_user[0]['truename'];
        $my['schoolname'] = $data_user[0]['schoolname'];
        $my['userpic'] = $data_user[0]['userpic'];


    }

    //获取年级信息
    public function getUserClassInfoByUser($user){
        $sql_now_class_name = 'SELECT classname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "2" AND username="%s" AND localareaid="%s" and classid="%s"';
            $data_now_class_name = M()->query($sql_now_class_name,$username,$localareacode,$classid);
    }

    //获取排名列表信息
    public function getWordRankList(){

    }


    public function getTotalRank(){
        $apptype = I('apptype/d',1);//1;语文课文，2：英语课文；3：英语单词
        $pageCurrent = I('pageCurrent/d',0);
        $pageSize = I('pageSize/d',0);
        $type = I('type/s','class');//class:班级;school:学校;all:全国
        $time = I('time/s','a');//w:周;m:月；a：总榜
        $classid = I('classid/s','');

        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');

                $username = '01791';
                $usertype = '4';
                $localareacode = '2.';

        $my = array();

        if($usertype == 2){
            //教师
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "2" AND username="%s" AND localareaid="%s" ';
        }else{
            //学生或者家长
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "4" AND username="%s" AND localareaid="%s"';
        }

        $data_user = M()->query($sql_user,$username,$localareacode);
        \Think\Log::record("查询自己的排名".M()->getLastSql());
        $schoolid = $data_user[0]['schoolid'];
        if(empty($classid)){
            $classid = $data_user[0]['classid'];
        }

        //判断是否有多个班级
        if(count($data_user)>1){
            $hasClass = true;
            $sql_now_class_name = 'SELECT classname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "2" AND username="%s" AND localareaid="%s" and classid="%s"';
            $data_now_class_name = M()->query($sql_now_class_name,$username,$localareacode,$classid);
            $className = $data_now_class_name[0]['classname'];
        }else{
            $hasClass = false;
            $className = $data_user[0]['classname'];
        }

        $my['hasClass'] = $hasClass;
        $my['classname'] = $className;
        $my['truename'] = $data_user[0]['truename'];
        $my['schoolname'] = $data_user[0]['schoolname'];
        $my['userpic'] = $data_user[0]['userpic'];


        $sql_where = ' where m.username=n.username AND m.isdel=0 AND n.isdel=0 AND m.apptype="'.$apptype.'" AND m.localareacode=n.localareaid AND m.usertype=n.usertype ';
        if($time == 'w'){
            $sql_where .= ' AND DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(m.addtime) ';
            $sql_time =  ' AND DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(addtime) ';
        }else if($time == 'm'){
            $sql_where .= ' AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(m.addtime) ';
            $sql_time = ' AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(addtime) ';
        }else if($time == 'a'){
            $sql_where .= '';
            $sql_time = '';
        }

        if($type == 'class'){
            $sql_where .= ' AND n.localareaid = "'.$localareacode.'" and n.classid="'.$classid.'" ';
        }else if($type == 'school'){
            $sql_where .= ' AND n.localareaid = "'.$localareacode.'" and n.schoolid = "'.$schoolid.'" ';
        }else if($type == 'all'){
            //$sql_where .= '';
            // 20180912修改
            $sql = 'SELECT count(m.id) as total,sum(m.ub) ub,max(m.ubupdatetime) ubupdatetime,username,localareacode FROM db_english.engs_user_record m ';
			$sql_where = ' where m.isdel=0 AND m.apptype="'.$apptype.'" ';
        }


        $sql_group = ' GROUP BY m.username,m.localareacode,m.usertype ';
        $sql_order = ' ORDER BY sum(m.ub) DESC,count(m.id) DESC,n.truename  ';
        $sql_limit = ' limit '.($pageCurrent-1).','.$pageSize;


        $sql = 'SELECT count(m.id) as total,sum(m.ub) ub,n.truename,n.userpic,n.classname,n.schoolname,n.username FROM db_english.engs_user_record m,db_english.engs_userinfo n';

        //查询我的当前u币数和篇数
        $sql_my = 'SELECT count(id) as total,sum(ub) as ub FROM db_english.engs_user_record WHERE  isdel = 0 AND apptype = "'.$apptype.'" AND username="'.$username.'" AND localareacode = "'.$localareacode.'" AND usertype = "'.$usertype.'" ';
        
        $data_my = M()->query($sql_my.$sql_time);
        \Think\Log::record("查询我的当前u币数和篇数".M()->getLastSql());
        if(empty($data_my)){
            //没有u币
            $hasRank = false;
            $mytotal = 0;
            $myub = 0;
        }else{
            $myub = $data_my[0]['ub'];
            $mytotal = $data_my[0]['total'];
            $hasRank = true;
        }

        $my['hasRank'] = $hasRank;
        $my['mytotal'] = $mytotal;
        $my['myub'] = $myub;


        //获取我的当前排名
        if($hasRank){
            //如果U币和篇数都相同，就按名字排序
            $sql_having_same = ' having sum(m.ub)='.$myub.' and count(m.id)='.$mytotal;
            $data_is_same = M()->query($sql.$sql_where.$sql_group.$sql_having_same.$sql_order);
            if(empty($data_is_same)){
                //没有相同的
                $is_same = false;
            }else{
                //都相同按名字
                $is_same = true;
                $arr_index = array_column($data_is_same,'username');
                $index = array_search($username, $arr_index);
            }

            $sql_having = ' having sum(m.ub)>'.$myub.' or (sum(m.ub)='.$myub.' and count(m.id)>'.$mytotal.')';
            $sql_my_rank = $sql.$sql_where.$sql_group.$sql_having;
            $data_my_rank = M()->query($sql_my_rank);
            if(empty($data_my_rank)){
                if($is_same){
                    $myrank = 1+$index;
                }else{
                    $myrank = 1;
                }
            }else{
                if($is_same){
                    $myrank = count($data_my_rank)+1+$index;
                }else{
                    $myrank = count($data_my_rank)+1;
                }
            }
        }else{
            $myrank = 0;
        }

        $my['myRank'] = $myrank;


        //获取第一页的排名
        if($type == 'all'){
			foreach($data_info as $k=>$v){
				$sql_user = 'select  truename,userpic,username,classname,schoolname from db_english.engs_userinfo where isdel=0 and  username="'.$v['username'].'" and localareacode="'.$v['localareacode'].'" limit 1';
				$resuser = M()->query($sql_user);
				$data_info[$k]['truename'] = $resuser[0]['truename'];
				$data_info[$k]['userpic'] = $resuser[0]['userpic'];
				$data_info[$k]['classname'] = $resuser[0]['classname'];
				$data_info[$k]['schoolname'] = $resuser[0]['schoolname'];
			}
			
		}else{
            $data_info = M()->query($sql.$sql_where.$sql_group.$sql_order.$sql_limit);
            \Think\Log::record("查询排名".M()->getLastSql());
        }
        $data['myinfo'] = $my;
        $data['info'] = $data_info;
        $this->ajaxReturn($data);

    }


    public function share(){
        $request=I("requests");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $moduleid=$request["moduleid"];
        $learnways=getmodule($moduleid);
        $data["title"]=$learnways["sharetitle"];
        $data["content"]=$learnways["sharecontent"];
        $data["iconUrl"]=PROTOCOL."://".C('local_service').'/public/public/images/'.$learnways["style"].'.png';
        //页面
        $page=explode("/",$learnways["apptrourl"]);
        $page=array_pop($page);
        $data["pageUrl"]=PROTOCOL."://".C('local_service').'/share/html/'.$page.'?localareacode='.encode(cookie("localAreaCode"),C("appkey")).'&code='.encode(cookie("vfs_url"),C("appkey"));
        $ret=array();
        if(empty($page)||empty($learnways["style"])||empty($learnways["title"])){
            $ret["suc"]=0;
            $ret["msg"]="请检查模块json数据,查看模块json数据配置中title,style,apptrourl这三个字段必须非空";
            $ret["data"]=array();
        }else{
            $ret["suc"]=1;
            $ret["msg"]="请求成功";
            $ret["data"]=$data;
        }
        $this->ajaxReturn($ret);
    }


    public function WordShare(){
        $data["pageUrl"]=PROTOCOL."://".C('local_service').'/share/html/index2.html?localareacode='.encode(cookie("localAreaCode"),C("appkey")).'&code='.encode(cookie("vfs_url"),C("appkey"));
        $data["truename"]=cookie("truename");
        $data["suc"]=1;
        $this->ajaxReturn($data);
    }


    public function userUnitInfo(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $UserService = new UserService();
      $moduleid = $request["moduleid"];
      if($moduleid == 63){
        $book = arr_column_more($this->book, array('gradeid','subjectid','termid'));
        $ret = $UserService ->getUserUbStageList($this->user,$book,$this->module,true);
      }else{
        
        switch($moduleid){
            case 10:
                $book = arr_column_more($this->book, array('gradeid','subjectid','termid','versionid'));
                $ret = $UserService ->getUserUnitLogList($this->user,$book,$this->module,false);
                break;
            case 40:
                $ret = array(array("ks_code"=>"00010203070101","chapterid"=>"7214","usercount"=>1,"isdo"=>0),array("ks_code"=>"00010303070101","chapterid"=>"7215","usercount"=>1,"isdo"=>0));
                break;
            case 2:
                $configrs = getUserConfig($request["subjectid"],$request["gradeid"],"0","0",$this->user); 
                $request["termid"] = $configrs["termid"];
                $ret = $UserService ->getUserUnitLogList($this->user,$request,$this->module,false);
                break;
            default:
                $configrs = getUserConfig($request["subjectid"],$request["gradeid"],"0","0",$this->user); 
                $request["termid"] = $configrs["termid"];
                $ret = $UserService ->getUserUnitLogList($this->user,$request,$this->module);
                break;
        }
      }
      $this->ajaxReturn($ret);
    }

    public function testRank(){
        $ks_code = I("ks_code");
        $apptype = I("apptype");
        $rankService = D("Rank","Service");
        $result = $rankService -> getUserTextReadingRank($ks_code,$apptype);
        $this->ajaxReturn($result);
    }
}
