<?php
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class WordModel extends Model
{
    //根据id获取单词
    private function getWord($wordid){
        $sql='select engs_word.id,engs_word.base_wordid,engs_base_word.word,engs_base_word.ukmp3,engs_base_word.isword,engs_base_word_explains.explains,';
        $sql=$sql.'engs_base_word_explains.morphology,engs_base_word_explains.pic from engs_word left join engs_base_word on ';
        $sql=$sql.'engs_word.base_wordid=engs_base_word.id left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id ';
        $sql=$sql.' where engs_word.id=%d';
        $result=$this->query($sql,$wordid);
        return $result;
    }

    //根据用户名以及单元获取单词列表
    public function getWordListData($ks_code,$username){
    	$sql="select engs_word.isword,engs_word.word,engs_word.id,engs_base_word.ukmark,engs_base_word.ukmp3,engs_base_word.ukmp3 as name,";
        $sql=$sql."engs_base_word_explains.morphology,engs_base_word_explains.explains,'1' as size,'mp3' as format,";
        $sql=$sql."concat('".C('word_mp3_path')."',engs_base_word.ukmp3) as url,";
    	$sql=$sql."engs_base_word_explains.pic,(case when book.wordid is not null then 1 else 0 end) as iscollect from engs_word left join ";
    	$sql=$sql." engs_base_word on engs_word.base_wordid=engs_base_word.id ";
    	$sql=$sql." left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id";
    	$sql=$sql." left join (select distinct wordid from engs_word_book where userid='%s' and isuse=1) book on engs_word.id=book.wordid";
    	$sql=$sql." where engs_word.ks_code='%s' and engs_word.isdel=1";
    	$data=$this->query($sql,$username,$ks_code);
    	return $data;
    }

    //获取中背单词的试题
    public function getReciteWordQuestion($gradeid,$wordid,$isimport){
        //是否重点单词
        if($isimport==1){
            $data=$this->wordSpell($wordid);
        }else{
            $questypes=M("questype_config")->where("gradeid='%s'",$gradeid)->select();
            //选择一种试题
            $index=mt_rand(0,count($questypes)-1);
            //调用函数以及试题规则
            $function=$questypes[$index]["func"];
            //调用对应的函数
            $data=$this->$function($wordid);
        }
        return $data;
    }

    //听音选词$id表示用户背单词记录表的ID $wordid表示单词 $username表示用户名称 $areaid表示区域ID
    private function listenChooseWord($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=C("word_path").$result[0]["ukmp3"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["wordid"]=$wordid;
        $ret["word"]=$result[0]["word"];
        $ret["typeid"]=1;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            $items[$key]["content"]=$value["word"];
            $items[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }
        }
        $ret["items"]=$items;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

    //听音选汉语
    private function listenChooseExplain($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=C("word_path").$result[0]["ukmp3"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["typeid"]=1;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            $items[$key]["content"]=$value["explains"];
            $items[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }
        }
        $ret["items"]=$items;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

    //看词选图形
    private function wordChoosePicture($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["word"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["typeid"]=0;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            //$items[$key]["content"]=$value["pic"];
            //获取远程图片
            $items[$key]["path"]=C("word_pic_path").$value["pic"];
            $filepath=C("word_pic_path").$value["pic"];
            $image_info = getimagesize($filepath);
            $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
            $items[$key]["content"]=$base64_image_content;
            //$questions_items[$key]->imgcontent=$base64_image_content;
            $items[$key]["typeid"]=1;
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }

        }
        $ret["items"]=$items;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //英译汉
    private function wordTranslateExplain($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["word"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["typeid"]=0;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            $items[$key]["content"]=$value["explains"];
            $items[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }
        }
        $ret["items"]=$items;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //汉译英
    private function explainTranslateWord($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["explains"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["typeid"]=0;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            $items[$key]["content"]=$value["word"];
            $items[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }
        }
        $ret["items"]=$items;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //单词拼写
    private function wordSpell($wordid,$username,$areaid){
        //查询这个单词的信息
        $result=$this->getWord($wordid);
        //构造选项内容
        $items=array();
        array_push($items,$result);
        $itemothers=$this->getChooseList($result[0]["base_wordid"],2);
        array_push($items,$itemothers);
        shuffle($items);
        $ret["tncontent"]=C("word_path").$result[0]["ukmp3"];
        $ret["mp3"]=C("word_path").$result[0]["ukmp3"];
        $ret["wordid"]=$wordid;
        $ret["word"]=$result[0]["word"];
        $ret["items"]=$items;
        $answer="";
        foreach($items as $key=>$value){
            $items[$key]["flag"]=chr(65+$key);
            if($result[0]["word"]===$value["word"]){
                $answer=$items[$key]["flag"];
            }
        }
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;

    }

    //构造选项
    private function getChooseList($wordid,$itemsnum){
        $rs=$this -> where("id=%d",$wordid) -> field("word,base_wordid,(select max(tags) from engs_base_word bw where bw.id=base_wordid) as tag") -> find();
        $id=$rs['wordid'];
        $tag=$rs["tag"];
        $words=$rs["word"];
        $subword=substr($words,0,1);
        $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
        $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.isword=1  And t1.word!="%s"  AND t1.id!=%d and ';
        $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3';
        $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
        $choicelist = $this->query($sql,$words,$id);
        if(count($choicelist)<$itemsnum){
            unset($sql);
            $sql='SELECT t1.word,t1.isword,t1.ukmark,t1.ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.isword=1 AND t1.word like "%'.$subword.'"';
            $sql.=' AND t1.word !="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';
            $sql.='  GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by rand() LIMIT 100';
            $choicelistadd = $this->query($sql,$words,$id);
            $scount=$itemsnum-count($choicelist);
            for($i=0;$i<$scount;$i++){
                array_push($choicelist,$choicelistadd[$i]);
            }
        }
        return $choicelist;
    }

    //记录用户的测试数据
    public function setUserReciteWord($id,$questions){
        M("user_recite_word")->where("id=%d",$id)->setField("questions",json_encode($questions));
    }

    //获取单词列表
    public function getWordList($ks_code){
        $sql="select engs_word.isword,engs_word.word as encontent,engs_word.id,engs_base_word.ukmark,engs_base_word.ukmp3 as mp3,";
        $sql=$sql."concat(engs_base_word_explains.morphology,engs_base_word_explains.explains) as cncontent,engs_base_word_explains.pic ";
        $sql=$sql."from engs_word left join  engs_base_word on engs_word.base_wordid=engs_base_word.id ";
        $sql=$sql." left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id";
        $sql=$sql." where engs_word.ks_code='%s' and engs_word.isdel=1";
        $data=$this->query($sql,$ks_code);
        return $data;
    }

    //获取单词列表
    public function getReadWordList($ks_code,$username,$readid,$chapterid){
        $sql="select engs_word.isword,engs_word.word as encontent,engs_word.id,engs_base_word.ukmark,engs_base_word.ukmp3 as mp3,engs_base_word.ukmp3 as ftp_mp3,case when z.readtimes is null then ".C('readtimes')." else (".C('readtimes')."-z.readtimes) end as readtimes,";
        $sql=$sql."concat(engs_base_word_explains.morphology,engs_base_word_explains.explains) as cncontent,engs_base_word_explains.pic,z.score,z.maxscore ";
        $sql=$sql."from engs_word left join  engs_base_word on engs_word.base_wordid=engs_base_word.id ";
        $sql=$sql." left join engs_base_word_explains on engs_word.base_explainsid=engs_base_word_explains.id";
        $sql=$sql." left join (SELECT * FROM engs_user_read_info t WHERE t.`ks_code`='%s' AND username='%s' AND readid=%d AND chapterid=%d) as z on engs_word.id=z.contentid ";
        $sql=$sql." where engs_word.ks_code='%s' and engs_word.isdel=1";
        $data=$this->query($sql,$ks_code,$username,$readid,$chapterid,$ks_code);
        return $data;
    }

    //获取背单词班级排名情况
    public function getReciteWordClassRank($ks_code,$classid){
        $user_recite_word=M("user_recite_word");
        //获取用户的背单词的内容
        $sql="Select username,truename,max(score) as score from engs_user_recite_word t where t.ks_code='%s' and t.truename is not null and  t.classid='%s' and isover=0 group by username order by score desc";
        $result=$this->query($sql,$ks_code,$classid);
        return $result;
    }


    //获取口语训练班级排名情况
    public function getReadWordClassRank($ks_code,$classid){
        //$sql="SELECT username,truename,score AS score FROM engs_user_read t WHERE t.id IN (SELECT MAX(id) FROM engs_user_read WHERE t.ks_code='%s' AND t.classid='%s'  AND isover=0  GROUP BY username) and t.truename is not null ORDER BY score DESC";
        $sql="Select username,truename,max(score) as score from engs_user_read t where t.ks_code='%s' and t.classid='%s' and isover=0 and chapterid in (SELECT * FROM (SELECT chapterid FROM engs_user_read WHERE username = '".cookie('username')."' ORDER BY id DESC LIMIT 1) AS a) and t.truename is not null group by username order by score desc";
        $result=$this->query($sql,$ks_code,$classid);
        return $result;
    }

    //获取用户口语训练数据
    public function getUserReadData($username,$chapterid,$ks_code){
        $user_read_word=M("user_read");
        //获取用户的背单词的内容
        $sql="select max(id) as id,(count(*)-sum(isover)) as count from (select (case when username='%s' then id else 0 end) as id,isover from engs_user_read where chapterid=%d and isover=0) as s";
        $rs=$this->query($sql,$username,$chapterid);
        //查询时间
        $timers=$user_read_word->where("id=%d",$rs[0]["id"])->field("(UNIX_TIMESTAMP(submittime)-UNIX_TIMESTAMP(addtime)) as dotime")->find();
        $result="";
        //判断是不是单词
        if($chapterid=="0"){
            $sql="select '".$rs[0]["count"]."' as count,'".$timers["dotime"]."' as dotime,case when s.maxscore is null then -1 else s.maxscore end score,s.filename,engs_word.word as content,engs_base_word.ukmp3 as mp3 from engs_word left join engs_base_word on engs_word.base_wordid=engs_base_word.id left join (select * from engs_user_read_info where username='%s' and readid=%d) s on engs_word.id=s.contentid where engs_word.ks_code='%s' and engs_word.isdel=1 ";
            $result=$this->query($sql,$username,$rs[0]["id"],$ks_code);
        }else{
            $sql="select '".$rs[0]["count"]."' as count,'".$timers["dotime"]."' as dotime,case when s.maxscore is null then -1 else s.maxscore end score,s.filename,encontent as content,mp3 from engs_text left join (select * from engs_user_read_info where username='%s' and readid=%d ) s on engs_text.id=s.contentid where engs_text.chapterid=%d and engs_text.isdel=1 ";
            $result=$this->query($sql,$username,$rs[0]["id"],$chapterid);
        }
        return $result;
    }

    //获取用户口语训练数据
    public function getUserReadHistoryData($username){
        $sql="select substr(engs_rms_unit.c1,5) as name,engs_user_read.submittime,engs_user_read.score,case when engs_user_read.chapterid=0 then '单词口语' else (select chapter from engs_text_chapter where engs_text_chapter.id=engs_user_read.chapterid) end as chapter from engs_user_read left join engs_rms_unit on engs_user_read.ks_code=engs_rms_unit.ks_code where engs_user_read.username='%s' and isover=0 order by engs_user_read.submittime desc";
        //历史列表
        $result=$this->query($sql,$username);
        return $result;
    }
}