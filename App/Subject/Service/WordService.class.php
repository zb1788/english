<?php
namespace Subject\Service;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class WordService extends Model
{
    //获取单元数据
    public function getDataByUnit($ks_code){
        $word=D("WordView");
        $data=$word->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->select();
        $ret["count"]=count($data);
        $ret["data"]=$data;
        return $data;
    }

    //获取单元以及单词本标志的数据
    public function getDataAndWordBookByUnit($user,$ks_code){
        $wordbook=M();
        $sql="SELECT Word.id AS id,Word.level_group AS level,Word.word AS word,Word.ks_code AS ks_code,Word.base_wordid AS base_wordid,Word.base_explainsid AS base_explainsid,Word.sortid AS sortid,Word.isdel AS wisdel,max(UserWordBook.id) AS bookid,(case when UserWordBook.id is null then 0 else 1 end) AS iscollect,UserWordBook.source AS source,UserWordBook.word AS bookword,(case when BaseWord.ukmark is null then '' else BaseWord.ukmark end) AS ukmark,BaseWord.tags AS tags,BaseWord.ukmp3 AS ukmp3,BaseWord.letters AS letters,BaseWord.others AS others,BaseWord.extend_json AS extend_json,BaseWordExplains.morphology AS morphology,BaseWordExplains.explains AS explains,BaseWordExplains.enexplains AS enexplains,BaseWordExplains.pic AS pic,BaseWordExplainsExample.encontent,BaseWordExplainsExample.cncontent,BaseWordExplainsExample.pic AS examplepic,BaseWordExplainsExample.mp3 AS examplemp3 FROM engs_word Word LEFT JOIN engs_user_word_book UserWordBook ON UserWordBook.base_wordid=Word.base_wordid and UserWordBook.username='%s' and UserWordBook.usertype='%s' and UserWordBook.localareacode='%s' and UserWordBook.isdel=1 LEFT JOIN engs_base_word BaseWord ON Word.base_wordid=BaseWord.id LEFT JOIN engs_base_word_explains BaseWordExplains ON Word.base_explainsid=BaseWordExplains.id LEFT JOIN engs_base_word_explains_example BaseWordExplainsExample ON Word.base_wordid = BaseWordExplainsExample.base_wordid AND Word.base_explainsid = BaseWordExplainsExample.explainid WHERE ( ks_code='%s' and Word.isdel=1 ) GROUP BY Word.id ORDER BY Word.sortid ASC,Word.id ASC ";
        $data=$wordbook->query($sql,$user["username"],$user["usertype"],$user["localareacode"],$ks_code);
        foreach($data as $key=>$value){
            $data[$key]["name"]=$value["ukmp3"];
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word/".$value["pic"];
            $data[$key]["examplepic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word_example/".$value["examplepic"];
            $data[$key]["examplemp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word_example/".$value["examplemp3"].".mp3";
            //题干以及试题的选项
            $data[$key]["multispell"]=$this->getWordMultiSpellQuestionByWordid($value);
            $data[$key]["singlespell"]=$this->getWordSingleSpellByWordid($value);
            $data[$key]["format"]="mp3";
            $data[$key]["size"]="1";
            $data[$key]["mp3time"]="2";
            $data[$key]["url"]="http://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $multichoose["isdo"]=0;
            $multichoose["answer"]="";
            $multichoose["iserror"]=-1;
            $data[$key]["multichoose"]=$multichoose;
        }
        $ret["count"]=count($data);
        $ret["data"]=$data;
        return $ret;
    }


    //获取单元以及单词本标志的数据
    public function getDataAndWordBookByUnitGroup($user,$ks_code){
        $wordbook=M();
        $sql="SELECT Word.id AS id,Word.level_group AS level,Word.word AS word,Word.ks_code AS ks_code,Word.base_wordid AS base_wordid,Word.base_explainsid AS base_explainsid,Word.sortid AS sortid,Word.isdel AS wisdel,max(UserWordBook.id) AS bookid,(case when UserWordBook.id is null then 0 else 1 end) AS iscollect,UserWordBook.source AS source,UserWordBook.word AS bookword,(case when BaseWord.ukmark is null then '' else BaseWord.ukmark end) AS ukmark,BaseWord.tags AS tags,BaseWord.ukmp3 AS ukmp3,BaseWord.letters AS letters,BaseWord.others AS others,BaseWord.extend_json AS extend_json,BaseWordExplains.morphology AS morphology,BaseWordExplains.explains AS explains,BaseWordExplains.enexplains AS enexplains,BaseWordExplains.pic AS pic,BaseWordExplainsExample.encontent,BaseWordExplainsExample.cncontent,BaseWordExplainsExample.pic AS examplepic,BaseWordExplainsExample.mp3 AS examplemp3 FROM engs_word Word LEFT JOIN engs_user_word_book UserWordBook ON UserWordBook.base_wordid=Word.base_wordid and UserWordBook.username='%s' and UserWordBook.usertype='%s' and UserWordBook.localareacode='%s' and UserWordBook.isdel=1 LEFT JOIN engs_base_word BaseWord ON Word.base_wordid=BaseWord.id LEFT JOIN engs_base_word_explains BaseWordExplains ON Word.base_explainsid=BaseWordExplains.id LEFT JOIN engs_base_word_explains_example BaseWordExplainsExample ON Word.base_wordid = BaseWordExplainsExample.base_wordid AND Word.base_explainsid = BaseWordExplainsExample.explainid WHERE ( ks_code='%s' and Word.isdel=1 ) GROUP BY Word.id ORDER BY Word.level_group ASC,Word.sortid ASC,Word.id ASC ";
        $data=$wordbook->query($sql,$user["username"],$user["usertype"],$user["localareacode"],$ks_code);
        $arr=array();
        $level=-1;
        $index=-1;
        foreach($data as $key=>$value){
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word/".$value["pic"];
            $data[$key]["examplepic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word_example/".$value["examplepic"];
            $data[$key]["examplemp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word_example/".$value["examplemp3"].".mp3";
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            $data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
            //题干以及试题的选项
            $data[$key]["multispell"]=$this->getWordMultiSpellQuestionByWordid($value);
            $data[$key]["singlespell"]=$this->getWordSingleSpellByWordid($value);
            $multichoose["isdo"]=0;
            $multichoose["answer"]="";
            $multichoose["iserror"]=-1;
            $data[$key]["multichoose"]=$multichoose;
            if($level == $value["level"]){
                $arr[$index][]=$data[$key];
            }else{
                $level=$value["level"];
                $index=$index+1;
                $arr[$index][]=$data[$key];
            }
        }
        $ret["count"]=count($count);
        $ret["data"]=$arr;
        return $ret;
    }

    //获取单元以及单词本标志的数据
    public function getDataAndWordBookByWordGroup($user,$ks_code){
        $wordbook=M();
        $sql="SELECT Word.chaptername,Word.id AS id,Word.level_group AS level,Word.word AS word,Word.ks_code AS ks_code,Word.base_wordid AS base_wordid,Word.base_explainsid AS base_explainsid,Word.sortid AS sortid,Word.isdel AS wisdel,max(UserWordBook.id) AS bookid,(case when UserWordBook.id is null then 0 else 1 end) AS iscollect,UserWordBook.source AS source,UserWordBook.word AS bookword,(case when BaseWord.ukmark is null then '' else BaseWord.ukmark end) AS ukmark,BaseWord.tags AS tags,BaseWord.ukmp3 AS ukmp3,BaseWord.letters AS letters,BaseWord.others AS others,BaseWord.extend_json AS extend_json,BaseWordExplains.morphology AS morphology,BaseWordExplains.explains AS explains,BaseWordExplains.enexplains AS enexplains,BaseWordExplains.pic AS pic,BaseWordExplainsExample.encontent,BaseWordExplainsExample.cncontent,BaseWordExplainsExample.pic AS examplepic,BaseWordExplainsExample.mp3 AS examplemp3 FROM engs_word Word LEFT JOIN engs_user_word_book UserWordBook ON UserWordBook.base_wordid=Word.base_wordid and UserWordBook.username='%s' and UserWordBook.usertype='%s' and UserWordBook.localareacode='%s' and UserWordBook.isdel=1 LEFT JOIN engs_base_word BaseWord ON Word.base_wordid=BaseWord.id LEFT JOIN engs_base_word_explains BaseWordExplains ON Word.base_explainsid=BaseWordExplains.id LEFT JOIN engs_base_word_explains_example BaseWordExplainsExample ON Word.base_wordid = BaseWordExplainsExample.base_wordid AND Word.base_explainsid = BaseWordExplainsExample.explainid WHERE ( ks_code='%s' and Word.isdel=1 ) GROUP BY Word.id ORDER BY Word.sortid ASC,Word.id ASC ";
        $data=$wordbook->query($sql,$user["username"],$user["usertype"],$user["localareacode"],$ks_code);
        $arr=array();
        $level=-1;
        $index=-1;
        foreach($data as $key=>$value){
            $data[$key]["name"]=$value["ukmp3"];
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word/".$value["pic"];
            $data[$key]["examplepic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word_example/".$value["examplepic"];
            $data[$key]["examplemp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word_example/".$value["examplemp3"].".mp3";
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            $data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
            $data[$key]["format"]="mp3";
            $data[$key]["size"]="1";
            $data[$key]["mp3time"]="2";
            $data[$key]["url"]=getHttpUrl($data[$key]["mp3"]);
            if($level == $value["chaptername"]){
                $arr[$index]["chaptername"]=$value["chaptername"];
                $arr[$index]["wordlist"][]=$data[$key];
            }else{
                $level=$value["chaptername"];
                $index=$index+1;
                $arr[$index]["chaptername"]=$value["chaptername"];
                $arr[$index]["wordlist"][]=$data[$key];
            }
        }
        $ret["count"]=count($data);
        $ret["data"]=$arr;
        return $ret;
    }

    //获取单元下面的单词信息
    public function getUnitWordInfoByWordid($wordid){
        $word=M("word");
        $rs=$word->where("id=%d",$wordid)->find();
        return $rs;
    }

    


    //获取意思的例句
    public function getExamplesByExplainid($explainid,$num){
        $baseWordExplainsExample=M("base_word_explains_example");
        if(empty($num)){
            $data=$baseWordExplainsExample->where("explainid=%d",$explainid)->select();
        }else{
            $data=$baseWordExplainsExample->where("explainid=%d",$explainid)->limit($num)->select();
        }
        foreach($data as $key=>$value){
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word_example/".$value["mp3"].".mp3";
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            //$data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
        }
        $ret["count"]=count($data);
        $ret["data"]=$data;
        return $ret;
    }

    //通过wordid获取单词信息
    public function getInfoByWordid($wordid){
        $word=D("WordView");
        $data=$word->where("id=%d",$wordid)->find();
        $ret["wordinfo"]=$data;
        $examplesrs=$this->getExamplesByExplainid[$data["base_explainsid"]];
        $ret["examples"]["count"]=count($examplesrs);
        $ret["examples"]["data"]=$examplesrs;
        return $ret;
    }

    //通过base获取单词信息
    public function getInfoByBaseid($baseid){
        $base=D("BaseView");
        $data=$base->where("id=%d",$wordid)->find();
        $ret["wordinfo"]=$data;
        $examplesrs=$this->getExamplesByExplainid[$data["base_explainsid"]];
        $ret["examples"]["count"]=count($examplesrs);
        $ret["examples"]["data"]=$examplesrs;
        return $ret;
    }

    //通过单词获取单词拼写试题
    public function getWordMultiSpellQuestionByWordid($wordinfo){
        //$rs=$this->getInfoByWordid($wordid);
        $answer=str_split($wordinfo["word"]);
        $quesanswer=array();
        $icount=0;
        $arr=array();
        foreach($answer as $key=>$value){
            $icount=$icount+1;
            $temp["index"]=$key;
            $temp["content"]=$value;
            array_push($arr,$value);
            $temp["flag"]=chr(97+$key);
            $temp["answer"]="";
            $temp["typeid"]=0;
            array_push($quesanswer, $temp);
        }
        $ret["tncontent"]=$quesanswer;
        $ret["answer"]=$quesanswer;
        $flag=true;
        while($icount<14){
            $ran=rand(0,25);
            $word=chr(97+$ran);
            $flag=array_search($word,$arr);
            if($flag===false){
                array_push($arr,$word);
                $temp["index"]=-1;
                $temp["content"]=$word;
                $temp["flag"]=chr(97+$icount);
                $temp["typeid"]=0;
                array_push($quesanswer, $temp);
                $icount=$icount+1;
            }else{
                continue;
            }
        }
        array_multisort(array_column($quesanswer,'content'),SORT_ASC,SORT_STRING,$quesanswer);
        $ret["items"]=$quesanswer;
        return $ret;
    }


    //通过单元获取每组试题
    public function getWordByUnitAndNum($ks_code,$start,$num){
        $word=D("WordView");
        $data=$word->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->limit($start,$num)->select();
        return $data;
    }

    //通过单元获取每组试题
    public function getWordInfoByUnitAndNum($user,$ks_code,$start,$num){
        $word=M();
        $sql="SELECT Word.id AS id,Word.word AS word,Word.ks_code AS ks_code,Word.base_wordid AS base_wordid,Word.base_explainsid AS base_explainsid,Word.sortid AS sortid,Word.isdel AS wisdel,max(UserWordBook.id) AS bookid,(case when UserWordBook.id is null then 0 else 1 end) AS iscollect,UserWordBook.source AS source,UserWordBook.word AS bookword,(case when BaseWord.ukmark is null then '' else BaseWord.ukmark end) AS ukmark,BaseWord.tags AS tags,BaseWord.ukmp3 AS ukmp3,BaseWord.letters AS letters,BaseWord.others AS others,BaseWord.extend_json AS extend_json,BaseWordExplains.morphology AS morphology,BaseWordExplains.explains AS explains,BaseWordExplains.enexplains AS enexplains,BaseWordExplains.pic AS pic FROM engs_word Word LEFT JOIN engs_user_word_book UserWordBook ON UserWordBook.base_wordid=Word.base_wordid and UserWordBook.username='%s' and UserWordBook.usertype='%s' and UserWordBook.localareacode='%s' and UserWordBook.isdel=1 LEFT JOIN engs_base_word BaseWord ON Word.base_wordid=BaseWord.id LEFT JOIN engs_base_word_explains BaseWordExplains ON Word.base_explainsid=BaseWordExplains.id WHERE ( ks_code='%s' and Word.isdel=1 ) GROUP BY Word.id ORDER BY Word.sortid ASC,Word.id ASC limit %d,%d";
        $data=$word->query($sql,$user["username"],$user["usertype"],$user["localareacode"],$ks_code,$start,$num);
        foreach($data as $key=>$value){
            $data[$key]["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word/".$value["pic"];
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["examples"]=$this->getExamplesByExplainid($value["base_explainsid"],1);
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            //$data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
        }
        return $data;
    }


    //通过单元获取单词数量
    public function getWordCountByUnit($ks_code){
        $count=$word=M("word")->where("isdel=1 and ks_code='%s'",$ks_code)->count();
        return $count;
    }


    //含有letter的单词数量
    public function getWordCountByUnitWithinLetters($ks_code){
        $word=D("WordView");
        $count=$word->where("ks_code='%s' and isdel=1 and letters!='' and letters is not null",$ks_code)->count();
        return $count;
    }

    //通过index获取单元
    public function getUnitByNum($gradeid,$termid,$versionid,$subjectid,$index){
        $infosql="select ks_code,ks_name,(select count(*) from engs_word where isdel=1 and engs_word.ks_code = engs_rms_unit.ks_code) as wordcount ";
        $infosql=$infosql." from engs_rms_unit where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' order by display_order";
        $rs=$this->query($infosql,$gradeid,$termid,$versionid,$subjectid);
        if(empty($rs)){
            $ret["ks_code"]="";
            $ret["ks_name"]="";
            $ret["index"]=$index;
        }else{
            if(count($rs) == ($index)){
                $ret["ks_code"]="";
                $ret["ks_name"]="";
                $ret["index"]=$index;
            }else{
                $count=$rs[$index]["wordcount"];
                while($count ==0 && $index<count($rs)){
                    $index=$index+1;
                    $count=$rs[$index]["wordcount"];
                }
                if($count > 0){
                    $ret["ks_code"]=$rs[$index]["ks_code"];
                    $ret["ks_name"]=$rs[$index]["ks_name"];
                    $ret["index"]=$index;
                }else if($index == count($rs)){
                    $ret["ks_code"]="";
                    $ret["ks_name"]="";
                    $ret["index"]=$index;
                }
            }
        }
        return $ret;
    }

    //获取本单元的句子
    public function getTextByUnitAndWord($ks_code,$word,$num){
        $sqlquery = M();
        $sql = 'select a.encontent,a.cncontent,a.mp3 from engs_text a,engs_text_chapter b,engs_rms_unit c where a.isdel = 1 and a.stateid=1 and a.chapterid = b.id and b.ks_code = c.ks_code and c.ks_code = "%s" and a.isexample=1 and a.isdel=1 and b.isdel=1 and match(a.encontent) against ("%s" in boolean mode) order by a.id limit %d';
        $rs = $sqlquery->query($sql, $ks_code, $word,$num);
        foreach($rs as $key=>$value){
            $rs[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/".substr($value["mp3"],0,2)."/".$value["mp3"].".mp3";
            $rs[$key]["mp3"] = getHttpUrl($rs[$key]["mp3"]);
           // $data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
        }
        if(empty($rs)){
            $ret["suc"]=0;
            $ret["data"]=array();
        }else{
            $ret["suc"]=1;
            $ret["data"]=$rs;
        }
        return $ret;
    }

    //获取单元中某一个单词的数据
    public function getWordSingleSpellByUnitAndNum($ks_code,$start,$num){
        $word=D("WordView");
        $data=$word->where("ks_code='%s' and isdel=1 and letters!='' and letters is not null",$ks_code)->order("sortid,id")->limit($start,$num)->select();
        foreach($data as $key=>$value){
            $letters=$value["letters"];
            $others=$value["others"];
            $letterarr=explode(",",$letters);
            $question["tncontent"]=$letterarr;
            $tncontents=array();
            foreach($letterarr as $skey=>$svalue){
                $tncontent["flag"]=chr(97+$skey);
                $tncontent["word"]=$svalue;
                $tncontent["answer"]="";
                $tncontent["iserror"]=-1;
                array_push($tncontents,$tncontent);
            }
            $question["tncontent"]=$tncontents;
            $question["answer"]=$tncontents;
            if(empty($others)){
                $otherarr=array();
            }else{
                $otherarr=explode(",",$others);
            }
            $itemarr=array_merge($letterarr,$otherarr);
            shuffle($itemarr);
            $items=array();
            foreach($itemarr as $ikey=>$ivalue){
                $item["flag"]=chr(97+$ikey);
                $item["content"]=$ivalue;
                $item["typeid"]=0;
                $item["isdo"]=0;
                array_push($items,$item);
            }
            $question["items"]=$items;
            $data[$key]["question"]=$question;
        }
        return $data;
    }

    //通过单词ID获取单选试题
    public function getWordSingleSpellByWordid($wordinfo){
        if($wordinfo["letters"]==""||empty($wordinfo["letters"])){
            return array();
        }
        $letters=$wordinfo["letters"];
        $others=$wordinfo["others"];
        $letterarr=explode(",",$letters);
        $question["tncontent"]=$letterarr;
        $tncontents=array();
        foreach($letterarr as $skey=>$svalue){
            $tncontent["flag"]=chr(97+$skey);
            $tncontent["word"]=$svalue;
            $tncontent["answer"]="";
            $tncontent["iserror"]=-1;
            array_push($tncontents,$tncontent);
        }
        $question["tncontent"]=$tncontents;
        $question["answer"]=$tncontents;
        if(empty($others)){
            $otherarr=array();
        }else{
            $otherarr=explode(",",$others);
        }
        $itemarr=array_merge($letterarr,$otherarr);
        shuffle($itemarr);
        $items=array();
        foreach($itemarr as $ikey=>$ivalue){
            $item["flag"]=chr(97+$ikey);
            $item["content"]=$ivalue;
            $item["iscorrect"]=array_search($ivalue,$letterarr)===false?0:1;
            $item["typeid"]=0;
            $item["isdo"]=0;
            array_push($items,$item);
        }
        $question["items"]=$items;
        return $question;
    }


    //获取单元中某一个单词的数据
    public function getWordMultiSpellByUnitAndNum($ks_code,$start,$num){
        $word=D("WordView");
        $data=$word->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->limit($start,$num)->select();
        foreach($data as $key=>$value){
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["ukmark"]=empty($value["ukmark"])?"":$value["ukmark"];
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            $answer=str_split($value["word"]);
            $quesanswer=array();
            $icount=0;
            $arr=array();
            $tncontents=array();
            foreach($answer as $akey=>$avalue){
                $tncontent["flag"]=chr(97+$skey);
                $tncontent["word"]=$avalue;
                $tncontent["answer"]="";
                $tncontent["iserror"]=-1;
                array_push($tncontents,$tncontent);
                $flag=array_search($avalue,$arr);
                if($flag===false){
                    $icount=$icount+1;
                    $temp["content"]=$avalue;
                    $temp["flag"]=chr(97+$akey);
                    $temp["typeid"]=0;
                    $temp["isdo"]=0;
                    array_push($quesanswer, $temp);
                    array_push($arr,$avalue);
                }
            }
            $question["tncontent"]=$tncontents;
            $question["answer"]=$tncontents;
            $flag=true;
            while($icount<14){
                $ran=rand(0,25);
                $word=chr(97+$ran);
                $flag=array_search($word,$arr);
                if($flag===false){
                    array_push($arr,$word);
                    $temp["content"]=$word;
                    $temp["flag"]=chr(97+$ran);
                    $temp["typeid"]=0;
                    $temp["isdo"]=0;
                    array_push($quesanswer, $temp);
                    $icount=$icount+1;
                }else{
                    continue;
                }
            }
            //shuffle($quesanswer);
            //按照字母顺序进行展示
            $quesanswer=sortt($quesanswer,"content");
            $question["items"]=$quesanswer;
            $data[$key]["question"]=$question;
        }
        return $data;
    }

    //获取单词的分组讲单词按照多少个进行分组
    public function getWordChapterByUnitAutomatic($ks_code,$num){
        $word=M("word");
        $rs=$word->where("isdel=1 and ks_code='%s'",$ks_code)->order("sortid,id")->select();
        //计算总共是多少个
        $page=ceil(count($rs)/$num);
        $data=array();
        for($i=0;$i<$page;$i++){
            $temp["name"]="第".$i."组";
            $temp["index"]=$i;
            array_push($data,$temp);
        }
        $ret["data"]=$data;
        $ret["count"]=count($data);
        return $ret;
    }

    //获取单词的分组讲单词按照多少个进行分组
    public function getUserWordBatch($ks_code,$user){
        $userwordbatch=M("user_word_batch");
        $rs=$userwordbatch->where("ks_code='%s' and username='%s' and localareacode='%s' and usertype='%s' and isfinish=1",$ks_code,$user["username"],$user["localareacode"],$user["usertype"])->group("engs_user_word_batch.index,engs_user_word_batch.type")->order("engs_user_word_batch.index,engs_user_word_batch.type")->field("index,type")->select();
        //计算总共是多少个
        $data=array();
        foreach($rs as $key=>$value){
            $data[$value["index"]][$value["type"]]=1;
        }
        return $data;
    }

    

    //获取单词的分组讲单词按照多少个进行分组
    public function getWordChapterByUnitAutomaticAndUsername($ks_code,$num,$user){
        $word=M("word");
        $rs=$word->where("isdel=1 and ks_code='%s'",$ks_code)->group("level_group")->order("level_group")->field("level_group")->select();
        $page=count($rs);
        //查询这个用户的浏览的记录
        $userdata=$this->getUserWordBatch($ks_code,$user);
        //判断下一个是否需要展示
        $nextflag=0;
        $flag=false;
        $icount=0;
        //用户的情况
        $data=array();
        foreach($rs as $key=>$value){
            $temp["name"]="第".($key+1)."组";
            $temp["index"]=$value["level_group"];
            $temp["ltype"]=isset($userdata[$value["level_group"]][0])?($userdata[$value["level_group"]][0]==null?0:1):0;
            $temp["dtype"]=isset($userdata[$value["level_group"]][2])?($userdata[$value["level_group"]][2]==null?0:1):0;
            if($temp["ltype"]==1&&$temp["dtype"]==1){
                $icount=$key+1;
            }else if($temp["ltype"]==1&&$temp["dtype"]==0){
                $icount=$key;
            }
            array_push($data,$temp);
        }
        if($icount<($page-1)){
            if($data[$icount]["ltype"] == 0 && $data[$icount]["dtype"] == 0){
                $top=0;
                $data[$icount]['ltype']=1;
                $curlevel=$icount*2;
            }else if($data[$icount]["ltype"] == 1 && $data[$icount]["dtype"] == 0){
                $top=0;
                $data[$icount]['dtype']=1;
                $curlevel=$icount*2+1;
            }
        }else if($icount==($page-1)){
            if($data[$icount]["ltype"] == 1 && $data[$icount]["dtype"] == 0){
                $top=0;
                $data[$icount]['dtype']=1;
                $curlevel=$icount*2+1;
            }else if($data[$icount]["ltype"] == 0 && $data[$icount]["dtype"] == 0) {
                $top=0;
                $data[$icount]['ltype']=1;
                $curlevel=$icount*2;
            }
        }else{
            //if($data[$icount]["ltype"] == 1 && $data[$icount]["dtype"] == 1){
               $top=1;
               $curlevel=$icount*2;
            //}else if($data[$icount]["ltype"] == 1 && $data[$icount]["dtype"] == 0) {
            //    $top=0;
            //    $curlevel=$icount*2;
            //}
        }
        //查询最后一关是否做完
        $hgtop=$this->isWordHg($ks_code,$user);
        if(!empty($hgtop)){
            $hg=1;
        }
        $ret["curlevel"]=$curlevel+1;
        $ret["count"]=count($rs)*2+1;
        $ret["top"]=$top;
        $ret["hg"]=$hg;
        $ret["data"]=$data;
        $ret["count"]=count($data);
        return $ret;
    }


    //获取单词的分组讲单词按照多少个进行分组
    public function getWordChapterByUnitChapter($ks_code,$num){
        $word=M("word");
        $rs=$word->where("isdel=1 and ks_code='%s'",$ks_code)->group("chaptername")->field("chaptername")->select();
        //计算总共是多少个
        $data=array();
        foreach($rs as $key=>$value){
            $temp["name"]=$value["chaptername"];
            $temp["index"]=$i;
            array_push($data,$temp);
        }
        $ret["data"]=$data;
        return $ret;
    }

    //获取单元下面的所有单词
    public function getWordListByUnit($ks_code){
        $word=D("WordView");
        $data=$word->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->select();
        $ret["data"]=$data;
        $ret["count"]=count($data);
        return $ret;
    }

    public function isWordHg($ks_code,$user){
        $hgtop=M("user_word_batch")->where("username='%s' and ks_code='%s' and usertype=%d and localareacode='%s' and type=3 and submittime is not null",$user["username"],$ks_code,$user["usertype"],$user["localareacode"])->find();
        return $hgtop;
    }


    //判断单词是否有拼写试题
    public function assertWordHasWordSpell($word_baseid){
        
    }

    //获取生词本中的单词
    public function getWordBookMultiSpellQuestionByBookid($bookid){
        $wordbook=D("WordBookView");
        $data=$wordbook->where("UserWordBook.id=%d",$bookid)->find();
        $answer=str_split($data["bookword"]);
        $quesanswer=array();
        $icount=0;
        $arr=array();
        $tncontents=array();
        foreach($answer as $akey=>$avalue){
            $tncontent["flag"]=chr(97+$skey);
            $tncontent["word"]=$avalue;
            $tncontent["answer"]="";
            $tncontent["iserror"]=-1;
            array_push($tncontents,$tncontent);
            $flag=array_search($avalue,$arr);
            if($flag===false){
                $icount=$icount+1;
                $temp["content"]=$avalue;
                $temp["flag"]=chr(97+$akey);
                $temp["typeid"]=0;
                $temp["isdo"]=0;
                array_push($quesanswer, $temp);
                array_push($arr,$avalue);
            }
        }
        $question["tncontent"]=$tncontents;
        $question["answer"]=$tncontents;
        $flag=true;
        while($icount<14){
            $ran=rand(0,25);
            $word=chr(97+$ran);
            $flag=array_search($word,$arr);
            if($flag===false){
                array_push($arr,$word);
                $temp["content"]=$word;
                $temp["flag"]=chr(97+$ran);
                $temp["typeid"]=0;
                $temp["isdo"]=0;
                array_push($quesanswer, $temp);
                $icount=$icount+1;
            }else{
                continue;
            }
        }
        //shuffle($quesanswer);
        //按照字母顺序进行展示
        $quesanswer=sortt($quesanswer,"content");
        $question["items"]=$quesanswer;
        $data["question"]=$question;
        $data["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$data["ukmp3"];
        $data["mp3"] = getHttpUrl($data["mp3"]);
        $data["ks_code"]="0";
        return $data;
    }


    //生成单词本测试的数据
    public function getWordBookTest($arr,$user){
        $wordbook=M();
        $sql="SELECT Word.id AS id,Word.level_group AS level,Word.word AS word,Word.ks_code AS ks_code,Word.base_wordid AS base_wordid,Word.base_explainsid AS base_explainsid,Word.sortid AS sortid,Word.isdel AS wisdel,max(UserWordBook.id) AS bookid,(case when UserWordBook.id is null then 0 else 1 end) AS iscollect,UserWordBook.source AS source,UserWordBook.word AS bookword,(case when BaseWord.ukmark is null then '' else BaseWord.ukmark end) AS ukmark,BaseWord.tags AS tags,BaseWord.ukmp3 AS ukmp3,BaseWord.letters AS letters,BaseWord.others AS others,BaseWord.extend_json AS extend_json,BaseWordExplains.morphology AS morphology,BaseWordExplains.explains AS explains,BaseWordExplains.enexplains AS enexplains,BaseWordExplains.pic AS pic,BaseWordExplainsExample.encontent,BaseWordExplainsExample.cncontent,BaseWordExplainsExample.pic AS examplepic,BaseWordExplainsExample.mp3 AS examplemp3 FROM engs_word Word LEFT JOIN engs_user_word_book UserWordBook ON UserWordBook.base_wordid=Word.base_wordid AND UserWordBook.base_explainsid = Word.base_explainsid 
        AND UserWordBook.`sourceid`= Word.id and UserWordBook.username='%s' and UserWordBook.usertype='%s' and UserWordBook.localareacode='%s' and UserWordBook.isdel=1 LEFT JOIN engs_base_word BaseWord ON Word.base_wordid=BaseWord.id LEFT JOIN engs_base_word_explains BaseWordExplains ON Word.base_explainsid=BaseWordExplains.id LEFT JOIN engs_base_word_explains_example BaseWordExplainsExample ON Word.base_wordid = BaseWordExplainsExample.base_wordid AND Word.base_explainsid = BaseWordExplainsExample.explainid WHERE ( UserWordBook.id in (%s) AND Word.id IS NOT NULL) GROUP BY Word.id ORDER BY Word.sortid ASC,Word.id ASC ";
        $data=$wordbook->query($sql,$user["username"],$user["usertype"],$user["localareacode"],implode(",",$arr));
        foreach($data as $key=>$value){
            $data[$key]["mp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word/".$value["ukmp3"];
            $data[$key]["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word/".$value["pic"];
            $data[$key]["examplepic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/pic_word_example/".$value["examplepic"];
            $data[$key]["examplemp3"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_word_example/".$value["examplemp3"].".mp3";
            $data[$key]["mp3"] = getHttpUrl($data[$key]["mp3"]);
            $data[$key]["examplemp3"] = getHttpUrl($data[$key]["examplemp3"]);
            //题干以及试题的选项
            $data[$key]["multispell"]=$this->getWordMultiSpellQuestionByWordid($value);
            $data[$key]["singlespell"]=$this->getWordSingleSpellByWordid($value);
            $multichoose["isdo"]=0;
            $multichoose["answer"]="";
            $multichoose["iserror"]=-1;
            $data[$key]["multichoose"]=$multichoose;
        }
        $ret["count"]=count($data);
        $ret["data"]=$data;
        return $ret;
    }

}