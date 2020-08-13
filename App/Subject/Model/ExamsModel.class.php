<?php
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class ExamsModel extends Model
{
    public function getExamsListData($ks_code,$username){
    	$examsql="select *,case when accnum is null then 0 else accnum end as accnum,case when num is null then 0 else num end as num,userscore from engs_exams left join (select paper_id,sum(case when ismark=1 then 1 else 0 end) as accnum,count(*) as num,sum(case when username='".$username."' then 1 else 0 end) as userscore from engs_user_quiz_list where ks_code='%s' and isover=0 group by paper_id) s on engs_exams.id=s.paper_id where ks_code='%s' and isdel=1  and state=4 order by sortid,id";
        $examslist=$this->query($examsql,$ks_code,$ks_code);
    	return $examslist;
    }


     public function getExamsListByKscode($ks_code,$offset,$num,$url=""){
        if(empty($url)){
            $examsql="select engs_exams.id,name,quescount,(case when click is null then 0 else click end) as hot from engs_exams left join engs_exams_click on engs_exams.id=engs_exams_click.exams_id where ks_code='%s' and isdel=1  and state=4 order by sortid,id limit ".$offset.",".$num;
        }else{
            $examsql="select engs_exams.id,name,quescount,concat('".$url."') as url,(case when click is null then 0 else click end) as hot from engs_exams left join engs_exams_click on engs_exams.id=engs_exams_click.exams_id  where ks_code='%s' and isdel=1  and state=4 order by sortid,id limit ".$offset.",".$num;
        }
        $examslist=$this->query($examsql,$ks_code);
        return $examslist;
    }


    //获取听力训练
    public function getExamsById($examsid){
    	$result=$this->where("id=%d",$examsid)->field("id,name,quescount,ks_code")->find();
		return $result;
    }

    //获取音频
    public function getQuestionTTSByExamsid($examsid){
        $sql="select * from engs_exams_questions t where examsid=%d and isdel=1 ORDER BY t.paper_sortid,t.sortid,t.id";
        $rs=M()->query($sql,$examsid);
        $arr=array();
        foreach($rs as $key=>$value){
            $tts=json_decode(urldecode($value["questions_tts"]),true);
            foreach($tts as $tk=>$tv){
                $temp["name"]=$tv["tts_mp3"];
                $temp["format"]="mp3";
                $temp["size"]="1";
                //http://125.46.30.33/yylmp3/mp3_exam/A4/A424DDC6A78EF5DC41607935EC7A4FB8.mp3
                $temp["url"]=PROTOCOL."://".C("exams_mp3_path").substr($tv["tts_mp3"], 0,2)."/".$tv["tts_mp3"].".mp3";
                $temp["url"] = getHttpUrl($temp["url"]);
                array_push($arr, $temp);
            }     
        }
        return $arr;
    }

    public function getExamsListCountByKscode($ks_code){
        $count=$this->where("ks_code='%s' and isdel=1 and state=4",$ks_code)->count();
        return $count;
    }


    //获取听力试卷的问题
    public function getQuestionByExamsid($username,$classid,$quizid,$examsid,$index){
        $quessql="select '1' as isvoice,s.examsid as examid,'".$quizid."' as quizid,t.*,s.parentid,s.content as stemcontent";
        $quessql=$quessql." from engs_exams_questions t left join engs_exams_stem s on t.stemid=s.id where (t.isdel=1";
        $quessql=$quessql." and s.isdel=1)  and s.examsid=".$examsid;
        if($iserror=='1'){
                $quessql=$quessql." and t.id not in (select distinct questionsid from engs_user_quiz_history z where z.quizid=".$quizid." and z.username='".$username."' and z.iscorrect=1) ";
        }
        $quessql=$quessql." order by t.paper_sortid limit ".$index.",1";
        $questionrs=M()->query($quessql);
        $examsid=$questionrs[0]["examid"];
        $quizid=$questionrs[0]["quizid"];
        $questionrs[0]["examsid"]=$examsid;
        $questionrs[0]["stemcontent"]=relace_tcontent($questionrs[0]["stemcontent"]);
        $questionrs[0]["tcontent"]=relace_tcontent($questionrs[0]["tcontent"]);
        $questionrs[0]["tcontent"] = str_replace("&nbsp;", " ", $questionrs[0]["tcontent"]);
        $questionrs[0]["questions_answer"]=urldecode($questionrs[0]["questions_answer"]);
        $questionrs[0]["questions_tts"]=urldecode($questionrs[0]["questions_tts"]);
        $questions_items=(array)json_decode(urldecode($questionrs[0]["questions_items"]));
        //如果是图片的话 直接进行base64编码减少数据的请求
        if($questionrs[0]["itemtype"]=="1"){
                foreach($questions_items as $key=>$value){
                        $filepath=C("CONST_UPLOADS").$value->content;
                        //$image_info = getimagesize($filepath);
                        //$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $questions_items[$key]->imgcontent=$filepath;
                }
        }
        $questionrs[0]["questions_items"]=$questions_items;
        $questionid=$questionrs[0]["id"];
        $childquestionrs=$this->getParentStem($questionrs[0]["stemid"]);

        //查询问题的答案以及用户的答案
        $questionanswersql="select t.id as quesansid,t.questionsid as quesid,t.answer as quesanswer,t.answer_num,s.* from engs_exams_questions_answer t left join ";
        $questionanswersql=$questionanswersql."(select * from engs_user_quiz_history  where quizid=".$quizid." and username='".$username."' and ";
        $questionanswersql=$questionanswersql." examid=".$examsid." and questionsid=".$questionid.") as s";
        $questionanswersql=$questionanswersql." on t.id=s.answerid where t.isdel=1 AND t.questionsid=".$questionid;
        $questionanswerrs=M()->query($questionanswersql);
        $questionanswer=$questionanswerrs[0]["quesanswer"];

        //查询做这个作业的人数
        $answernumsql="select count(distinct t.username) as num from engs_user_quiz_list t where t.submittime is not null and t.paper_id=".$examsid." and t.classid='".$classid."'";
        $answernumrs=M()->query($answernumsql);
        $answernum=$answerrs[0]['num'];

        //这里查询需要优化
        $questionratesql="SELECT answerid,answer,SUM(CASE WHEN iscorrect = 1 THEN 1 ELSE 0  END) AS accnum,COUNT(username) AS answernum  FROM engs_user_quiz_history where issubmit=1 and ";
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
        $data["accrate"]=round($accnumtemp*100/$answernumtemp)."%";
        if(round($accnumtemp*100/$answernumtemp)==100){
                $data["erroranswer"]="";
        }else{
                $data["erroranswer"]=$maxerroranswer;
        }
        $questionanswerrs[0]["summary"]=$data;
        //学生作答的汇总数据
        $summarysql="select t.answer,count(*) as per from engs_user_quiz_history t where t.issubmit=1 and t.classid='".$classid."' and t.questionsid=".$questionid." group by t.answer";
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
    	return $rs;
    }
    
    //获取父亲的题干
    private function getParentStem($parentid){
        //组合试题的问题
        $sql="select * from engs_exams_stem t where  t.id in (select parentid from engs_exams_stem s where s.id=".$parentid." and s.isdel=1)";
        $childrs=M()->query($sql);
        return $childrs;
    } 


    //获取用户听力训练数据
    public function getUserListenHistoryData($username){
        $sql="SELECT max(engs_user_quiz_list.id),SUBSTR(engs_rms_unit.c1, 5) AS NAME,engs_user_quiz_list.submittime,max(engs_user_quiz_list.score) as score,";
        $sql=$sql."(SELECT engs_exams.name FROM engs_exams WHERE engs_exams.id=engs_user_quiz_list.`paper_id`) AS chapter ";
        $sql=$sql."FROM engs_user_quiz_list LEFT JOIN engs_rms_unit ON engs_user_quiz_list.ks_code = engs_rms_unit.ks_code ";
        $sql=$sql." WHERE engs_user_quiz_list.username = '".$username."' AND engs_user_quiz_list.isover = 0 and engs_user_quiz_list.score is not null and engs_user_quiz_list.submittime is not null group by engs_user_quiz_list.paper_id ORDER BY ";
        $sql=$sql." engs_user_quiz_list.submittime DESC ";
        //历史列表
        $result=$this->query($sql,$username);
        return $result;
    }

    //获取听力训练的排名
    public function getListenExamClassRank($examsid,$classid){
        //获取用户的背单词的内容
        //echo $examsid,$classid;
        $sql="SELECT id,username,truename,score FROM engs_user_quiz_list WHERE id IN (SELECT  MAX(id) FROM engs_user_quiz_list WHERE isover=0 AND truename IS NOT NULL AND score IS NOT NULL AND username IS NOT NULL AND paper_id='%s' and classid='%s' GROUP BY username) order by score desc";
        //echo $sql;exit;
        $result=$this->query($sql,$examsid,$classid);
        return $result;
    }



}