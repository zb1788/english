<?php
namespace Homework\Controller;
use Think\Controller;

/**
* 用于获取数据的通用的接口
*
* @author         qgy
* @since          1.0
*/

class PcfunctionController extends Controller {

    //获取单词跟读单词列表
        //ids 单词所在ID组 或者作业ID
    // issubmit 是否提交
    // answer 是否作答
    //studentid 学生id
    // classid 班级ID
    //单词跟读列表
    private function getWordwa($ids,$issubmit,$answer,$studentid,$classid){
        if($issubmit==0&&$answer==1){  //预览
            $sql="select *,'".$issubmit."' as issubmit,'".$answer."' as answer from engs_word t ,engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.base_wordid=p.base_wordid and t.id in (".$ids.")";
        }
        else{
            $sql="select '".$issubmit."' as issubmit,t.homeworkid,t.wordid,t.id as wordreadid,s.word,s.isstress,e.ukmark,e.ukmp3,e.isword,p.explains,p.pic,";
            $sql=$sql."'".$answer."' as answer,";
            $sql=$sql."'".$studentid."' as studentid,";
            $sql=$sql."(select h.id from engs_homework_student_word_read h where h.homeworkid=t.homeworkid and h.word_readid=t.id and studentid='".$studentid."') as recordid,";
            $sql=$sql."(select h.score from engs_homework_student_word_read h where h.homeworkid=t.homeworkid and h.word_readid=t.id and studentid='".$studentid."') as userscore,";
            $sql=$sql."(select h.mp3 from engs_homework_student_word_read h where h.homeworkid=t.homeworkid and h.word_readid=t.id and studentid='".$studentid."') as usermp3,";
            $sql=$sql."(select sum(h.score)/count(*) from engs_homework_student_word_read h where h.homeworkid=t.homeworkid and h.word_readid=t.id and classid='".$classid."' group by h.homeworkid and h.word_readid) as average";
            $sql=$sql." from engs_homework_word_read t ,engs_word s ,engs_base_word e,engs_base_word_explains p ";
            $sql=$sql." where t.wordid=s.id and s.base_wordid=e.id and s.base_explainsid=p.id and p.base_wordid=e.id and t.homeworkid=".$ids;
        }
       
        $rs=m()->query($sql);
        $ret["title"]="单词跟读";
        $ret["words"]=$rs;
        $ret["score"]=count($rs);
        $ret["issubmit"]=$issubmit;
        $ret["answer"]=$answer;
        $ret["wordlistnum"]=count($rs);
        return $ret;
    }
//单词拼写-互译-选词列表
    private function getstuwordoption($homeworkid,$classid,$studentid,$answer,$issubmit,$typeid){
            $sql="select distinct t.wordid,s.word from engs_homework_word_evaluat t left join engs_word s on t.wordid=s.id where  t.homeworkid=".$homeworkid." and t.typeid in(".$typeid.") order by t.sortid desc";
            //echo $sql."===";exit;
            $rss=m()->query($sql);
            $result=array();
            $wtresult=array();
            $wordtestcounts=0;
            foreach($rss as $key=>$value){
                  //基本信息
                    $sql="select s.word,t.id,t.option_a,t.option_b,t.option_c,p.explains,e.ukmp3,t.answer as queanswer,max(case when st.studentid = '".$studentid."' then st.iscorrect else '' end) as iscorrect,t.typeid,";
                    //班级回答人数
                    $sql=$sql."sum(case when st.classid='".$classid."' then 1 else 0 end) as num,";
                    //显示分数
                    $sql=$sql."sum(case when st.classid='".$classid."'  then st.iscorrect else 0 end) as accnum,";
                    //用户分数
                    $sql=$sql."sum(case when st.studentid='".$studentid."' then st.score else 0 end) as userscore,";
                    //问题分数
                    $sql=$sql."'1' as quescore,";
                    //是否提交
                    $sql=$sql."'".$issubmit."' as issubmit,";
                    //是狗显示答案
                    $sql=$sql."'".$answer."' as answer,";
                    //用户答案
                    $sql=$sql."max(case when st.studentid='".$studentid."' then st.answer else '' end) as useranswer ";
                    //条件
                    $sql=$sql." from engs_homework_word_evaluat t left join engs_homework_student_word_evaluat st on st.word_evaluatid=t.id,";
                    $sql=$sql."engs_word s,engs_base_word e,engs_base_word_explains p ";
                    $sql=$sql."  where t.wordid=s.id and s.base_wordid=e.id and s.base_explainsid=p.id and p.base_wordid=e.id and t.typeid in(".$typeid.") ";
                    $sql=$sql." and t.homeworkid=".$homeworkid." and t.wordid=".$value["wordid"]."  group by t.id,t.sortid,t.option_a,t.option_b,t.option_c,p.explains,e.ukmp3 order by t.sortid desc";
                   //echo $sql."===";
                    //查询
                    $chrs=m()->query($sql);
                    //第一道试题的参数
                    $data["wordid"]=$value["wordid"];
                    $data["explains"]=$chrs[0]["explains"];
                    $data["mp3"]=$chrs[0]["ukmp3"];
                    $data["score"]=1;
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

                    
            }
            $ret["title"]="单词测试";
            $ret["words"]=($wtresult);
            $ret["score"]=$wordtestcounts;
            $ret["issubmit"]=$issubmit;
            $ret["answer"]=$answer;
            $ret["wordlistnum"]=count($wtresult);
            return $ret;
        }
        //获取学生课文的作业情况
        private function getText($homeworkid,$studentid,$classid,$answer,$issubmit){
            $sql="select t.id as textreadid,t.homeworkid,t.chapterid,s.chapter,s.sortid,s.ks_code from engs_homework_text_read t left join engs_text_chapter s on t.chapterid=s.id where t.homeworkid=".$homeworkid;

            $rs=M()->query($sql);
            $count=0;
            $newchapterarr = array();
            $newkey = 0;
            $textindex = 1;
            foreach($rs as $key=>$value){
                    $sql="select '".$issubmit."' as issubmit,'".$answer."' as answer,'".$studentid."' as studentid,t.id as textid,t.encontent,t.mp3,t.chapterid,t.cncontent, ";
                    $sql=$sql."(select s.id from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as recordid,";
                    $sql=$sql."(select s.score from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as userscore,";
                    $sql=$sql."(select s.mp3 from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and studentid='".$studentid."') as usermp3,";
                    $sql=$sql."(select count(distinct s.studentid) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as num,";
                    $sql=$sql."(select AVG(case when s.score is null then 0 else s.score end) from engs_homework_student_text_read s where s.homeworkid=".$homeworkid." and s.textid=t.id and s.classid='".$classid."') as average";
                    $sql=$sql." from engs_text t where t.chapterid=".$value["chapterid"]." and isdel=1 order by t.sortid";
                    $textrs=M()->query($sql);
                    //echo $sql;exit;
                    foreach ($textrs as $textskey => $textsvalue) {
                        $textrs[$textskey]['ks_code'] = $value['ks_code'];
                        $textrs[$textskey]['chaptername'] = '第'.($key+1).'节 '.$value['chapter'];
                        $textrs[$textskey]['textreadid'] = $value['textreadid'];
                        $textrs[$textskey]['chapternum'] = count($rs);
                        $textrs[$textskey]['textindex'] = $textindex;
                        $textindex ++ ;
                        array_push($newchapterarr, $textrs[$textskey]);
                    }
                    $rs[$key]["text"]=$textrs;
                    $rs[$key]["score"]=count($textrs);
                    $count=$count+count($textrs);
            }
            $ret["title"]="课文朗读";
            $ret["score"]=$count;
            $ret["chapters"]=$newchapterarr;
            $ret["chapters2"]=$rs;
            $ret["answer"]=$answer;
            $ret["issubmit"]=$issubmit;
            //var_dump($ret);
            return $ret;
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
                            $sql = "select answer as useranswer,iscorrect from engs_homework_student_quiz where homeworkid=".$homeworkid." and classid='".$classid."' and studentid='".$studentid."' and questionsid = ".$quevalue['id'];
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
                            $sql = "select answer as useranswer,iscorrect from engs_homework_student_quiz where homeworkid=".$homeworkid." and classid='".$classid."' and studentid='".$studentid."' and questionsid = ".$quevalue['id'];
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
//预览相关开始
    //单词跟读预览
    public function preview_wordwa(){
        $ids=I("ids");
        if(!empty($ids)){
            //获取单词的信息
                $ret=$this->getWordwa($ids,0,1,"","");
            $this->ajaxReturn($ret);
        }
    }
    
    //单词拼写预览
    public function preview_wordwp(){
        $ids=I("ids");
        if(!empty($ids)){
            //获取单词的信息
               $sql="select *,0 as issubmit,1 as answer from engs_word t ,engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.base_wordid=p.base_wordid and t.id in (".$ids.")";
            $rs=m()->query($sql);
            $ret["title"]="单词拼写";
            $ret["words"]=$rs;
            $ret["issubmit"]=0;
            $ret["answer"]=1;
            $ret["wordlistnum"]=count($rs);
            $this->ajaxReturn($ret);
        }
    }
    //英汉互译预览
    public function preview_wordwy(){
        $ids=I("ids");
        if(!empty($ids)){
            //获取单词的信息
               $sql="select *,0 as issubmit,1 as answer from engs_word t ,engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.base_wordid=p.base_wordid and t.id in (".$ids.")";
            $rss=m()->query($sql);
            $answer=['A','B','C'];
            foreach($rss as $key=>$value){
                $ran=mt_rand(0,100)%2;
                $rs=get_choice_list($value["id"]);
                if($ran==0){       //汉译英
                    $choice[0]=$rs[0]["word"];
                    $choice[1]=$rs[1]["word"];
                    $choice[2]=$value["word"];
                    shuffle($choice);
                    $founded = array_search($value["word"], $choice);
                    $rss[$key]["option_a"]=$choice[0];
                    $rss[$key]["option_b"]=$choice[1];
                    $rss[$key]["option_c"]=$choice[2];
                    $rss[$key]["answer"]=$answer[$founded];
                    $rss[$key]["typeid"]=$ran;
                }else{
                    $choice[0]=$rs[0]["explains"];
                    $choice[1]=$rs[1]["explains"];
                    $choice[2]=$value["explains"];
                    shuffle($choice);
                    $founded = array_search($value["explains"], $choice);
                    $rss[$key]["option_a"]=$choice[0];
                    $rss[$key]["option_b"]=$choice[1];
                    $rss[$key]["option_c"]=$choice[2];
                    $rss[$key]["answer"]=$answer[$founded];
                    $rss[$key]["typeid"]=$ran;
                    $rss[$key]["optiona"]=$data;
                }
                unset($choice);unset($data);unset($rs);
            }
            $ret["title"]="英汉互译";
            $ret["words"]=$rss;
            $ret["issubmit"]=0;
            $ret["answer"]=1;
            $ret["wordlistnum"]=count($rss);
            $this->ajaxReturn($ret);
        }
    }
    
    //听音选词预览
    public function preview_wordwx(){
        $ids=I("ids");
        if(!empty($ids)){
            //获取单词的信息
               $sql="select *,0 as issubmit,1 as answer from engs_word t ,engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.base_wordid=p.base_wordid and t.id in (".$ids.")";
            $rss=m()->query($sql);
            $answer=['A','B','C'];
            foreach($rss as $key=>$value){
                $rs=get_choice_list($value["id"]);
                $choice[0]=$rs[0]["word"];
                $choice[1]=$rs[1]["word"];
                $choice[2]=$value["word"];
                shuffle($choice);
                $founded = array_search($value["word"], $choice);
                $rss[$key]["option_a"]=$choice[0];
                $rss[$key]["option_b"]=$choice[1];
                $rss[$key]["option_c"]=$choice[2];
                $rss[$key]["answer"]=$answer[$founded];
            }
            $ret["title"]="听音选词";
            $ret["words"]=$rss;
            $ret["issubmit"]=0;
            $ret["answer"]=1;
            $ret["wordlistnum"]=count($rss);
            $this->ajaxReturn($ret);
        }
    }
    //课文跟读预览
    public function preview_getTexts(){
        $ids=I("ids");
        if(!empty($ids)){
            $count=0;
            $sql="select * from engs_text_chapter t where isdel=1 and id in (".$ids.")";
            $rs=M()->query($sql);
            $textindex = 1;
            foreach($rs as $key=>$value){
                $textrs=M("text")->where("chapterid=%d and isdel=1",$value["id"])->field("*,'0' as issubmit,'1' as answer")->order('sortid,id')->select();
                 foreach ($textrs as $textskey => $textsvalue) {
                    $textrs[$textskey]['textindex'] =$textindex;
                    $textindex ++;
                 }
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
    //听力训练预览
    public function preview_exams(){
        $examsid=I("ids");
        $studentid=session("username");
        $classid=session("classid");
        $rs=$this->get_exams(0,$studentid,$classid,$examsid,0,1,0);
        $this->ajaxReturn($rs);
    }
    //预览相关结束
    //做作业相样开始
    //单词跟读-做作业
    public function stu_wordwa(){
         $homeworkid=I("homeworkid");
         $classid=session("classid");
         $studentid=session("studentid");
         $viewtype = session('viewtype');
         if($classid == ''){
            $classId = I('classid/s',0);
         }
         if($studentid == ''){
            $studentid= I('studentid/s',0);
         }
         if($viewtype == 0){
            $ret=$this->getWordwa($homeworkid,0,0,$studentid,$classid);
         }
         else{
             $ret=$this->getWordwa($homeworkid,0,2,$studentid,$classid);
         }
        
         $this->ajaxReturn($ret);
    }
    //单词拼写-做作业stu_wordwp
    public function stu_wordwp(){
        $viewtype = session('viewtype');
        $homeworkid=I("homeworkid");
        $studentid=session("username");
        $classid=session("classid");
        if($viewtype == 0){
            $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,0,0,2);
        }
        else{
            $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,2,0,2);
        }
              //  var_dump($ret);
        $this->ajaxReturn($ret);
    }
    //英汉互译-做作业stu_wordwy
    public function stu_wordwy(){
        $viewtype = session('viewtype');
        $homeworkid=I("homeworkid");
        $studentid=session("username");
        $classid=session("classid");
        if($viewtype == 0){
            $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,0,0,'1,3');
        }
        else{
            $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,2,0,'1,3');
        }
              //  var_dump($ret);
        $this->ajaxReturn($ret);
    }
    //听音选词-做作业stu_wordwy
    public function stu_wordwx(){
        $viewtype = session('viewtype');

        $homeworkid=I("homeworkid");
        $studentid=session("studentid");
        $classid=session("classid");
        if($viewtype == 0){
           $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,0,0,0); 
        }
        else{
            $ret=$this->getStuWordOption($homeworkid,$classid,$studentid,2,0,0);
        }
              //  var_dump($ret);
        $this->ajaxReturn($ret);
    }
    //课文跟读做作业情况
    public function getStuTexts(){
        $homeworkid=I("homeworkid");
        $studentid=session("studentid");
        $classid=session("classid");
        $viewtype = session('viewtype');
        if($viewtype == 0){
            $ret=$this->getText($homeworkid,$studentid,$classid,0,0);
        }
       else{
         $ret=$this->getText($homeworkid,$studentid,$classid,2,0);
       }
        //var_dump($ret);
        $this->ajaxReturn($ret);
    }
    //听力试卷-做作业
    public function getStuExaminfo(){
        $homeworkid=I("ids");
        $studentid=session("studentid");
        $classid=session("classid");
        $sql="select group_concat(examsid) as ids,id as quizid from engs_homework_quiz t where t.homeworkid=".$homeworkid." group by homeworkid";
        $rs=m()->query($sql);
        if(!empty($rs)){
            $rs=$this->get_exams($homeworkid,$studentid,$classid,$rs[0]["ids"],0,0,$rs[0]["quizid"]);
            $this->ajaxReturn($rs);
        }

    }
    //学生反馈听力试卷-做作业
    public function getSturesultExaminfo(){
        $homeworkid=I("homeworkid");
        $studentid=session("studentid");
        $classid=session("classid");
        $viewtype = session('viewtype');
        $sql="select group_concat(examsid) as ids,id as quizid from engs_homework_quiz t where t.homeworkid=".$homeworkid." group by homeworkid";
        $rs=m()->query($sql);
        if(!empty($rs)){
            if($viewtype == 0){
                $rs=$this->get_exams($homeworkid,$studentid,$classid,$rs[0]["ids"],1,1,$rs[0]["quizid"]);
            }
            else{
                $rs=$this->get_exams($homeworkid,$studentid,$classid,$rs[0]["ids"],0,1,$rs[0]["quizid"]);
            }
            $this->ajaxReturn($rs);
        }

    }
    /////////////////////////////////////
    //学生查看作业反馈
    /////////////////////////////////////
    //学生单词测试反馈-拼写，互译，选词
    public function get_stu_feedback_wordtest(){
        
        //$this->ajaxReturn($rs);
    }


    private function getParentStem($parentid){
        //组合试题的问题
        $sql="select * from engs_exams_stem t where  t.id in (select parentid from engs_exams_stem s where s.id=".$parentid." and s.isdel=1)";
        $childrs=M()->query($sql);
        return $childrs;
    }

}
