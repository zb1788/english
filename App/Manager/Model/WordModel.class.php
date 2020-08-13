<?php
namespace Manager\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class WordModel extends Model
{
    //根据id获取单词
    private function getWordById($wordid,$explainid){
        $sql='select engs_base_word.letters,engs_base_word.others,engs_base_word.id as base_wordid,engs_base_word.word,engs_base_word.usmp3 as ukmp3,engs_base_word.isword,engs_base_word_explains.explains,';
        $sql=$sql.'engs_base_word_explains.morphology,engs_base_word_explains.pic,engs_base_word_explains.id as explainid from  engs_base_word  ';
        $sql=$sql.'left join engs_base_word_explains on engs_base_word.id=engs_base_word_explains.base_wordid ';
        $sql=$sql.' where engs_base_word.id="%s" and engs_base_word_explains.id="%s"';
        $result=$this->query($sql,$wordid,$explainid);
        return $result;
    }

    //根据id获取单词
    private function getExampleByExplainid($explainid,$period){
    	$sql='select * from engs_base_word_explains_example where explainid=%d and substr(period,'.$period.',1)=1 order by rand() limit 1';
        $result=$this->query($sql,$explainid);
        if(empty($result)){
            $sql='select * from engs_base_word_explains_example where explainid=%d order by rand() limit 1';
            $result=$this->query($sql,$explainid);
        }
        return $result;
    }


    

    //获取中背单词的试题
    public function getReciteWordQuestion($index,$wordid,$explainid,$peroid){
        //是否重点单词
        $questypes=M("word_game_questype")->where("cindex=%d and isuse=1",$index)->find();
        //选择一种试题
        //调用函数以及试题规则
        $function=$questypes["func"];
        //调用对应的函数
	$peroid=$peroid+1;
        $data=$this->$function($wordid,$explainid,$peroid);
        return $data;
    }


    //英译汉
    private function wordTranslateExplain($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getWordChooseList($result[0]["base_wordid"],2,$result[0]["explainid"]);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["word"];
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=3;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            $item[$key]["content"]=$value["explains"];
            $item[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }
        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //汉译英
    private function explainTranslateWord($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getWordChooseList($result[0]["base_wordid"],2,$result[0]["explainid"]);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["explains"];
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=2;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            $item[$key]["content"]=$value["word"];
            $item[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }
        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

     //听音辨义
    private function listenChooseExplain($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getWordChooseList($result[0]["base_wordid"],2,$result[0]["explainid"]);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["ukmp3"];
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=0;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            $item[$key]["content"]=$value["explains"];
            $item[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }
        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //听音辨词
    private function listenChooseWord($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getWordChooseList($result[0]["base_wordid"],2,$result[0]["explainid"]);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["ukmp3"];
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["word"]=$result[0]["word"];
        $ret["questype"]=1;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            $item[$key]["content"]=$value["word"];
            $item[$key]["typeid"]=0;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }
        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //单词选图
    private function wordChoosePicture($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getWordChooseList($result[0]["base_wordid"],2,$result[0]["explainid"]);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$result[0]["word"];
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=4;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            //$items[$key]["content"]=$value["pic"];
            //获取远程图片
            $item[$key]["path"]=C("word_pic_path").$value["pic"];
            $filepath=C("word_pic_path").$value["pic"];
            $image_info = getimagesize($filepath);
            $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
            $item[$key]["content"]=$base64_image_content;
            //$questions_items[$key]->imgcontent=$base64_image_content;
            $item[$key]["typeid"]=1;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }

        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

    //短语选图
    private function exampleChoosePicture($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        $example=$this->getExampleByExplainid($explainid,$peroid);
        if(empty($example)){
            $ret=array();
            return $ret;
        }
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$example[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getExamplePictureChooseList($explainid,2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$example[0]["encontent"];
        $ret["mp3"]=$example[0]["mp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=5;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            //$items[$key]["content"]=$value["pic"];
            //获取远程图片
            $item[$key]["path"]=C("example_pic_path").$value["pic"];
            $filepath=C("example_pic_path").$value["pic"];
            $image_info = getimagesize($filepath);
            $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
            $item[$key]["content"]=$base64_image_content;
            //$questions_items[$key]->imgcontent=$base64_image_content;
            $item[$key]["typeid"]=1;
            if($result[0]["word"]===$value["word"]){
                $answer=$item[$key]["flag"];
            }

        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

    //单词音节拼写6
    private function wordSpell($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        if(empty($result[0]["letters"])){
            $ret=array();
            return $ret;
        }
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$result[0]["pic"];
        $letters=$result[0]["letters"];
        $others=$result[0]["others"];
        $lettersarr=explode(",",$letters);
        $othersarr=explode(",",$others);
        if(!empty($others)){
            $items=$othersarr;
        }else{
            $items=array();
        }
        foreach($lettersarr as $key=>$value){
            array_push($items, $value);
        }
        shuffle($items);
        $itemsarr=array();
        foreach($items as $key=>$value){
            $temp["flag"]=chr(65+$key);
            $temp["content"]=$value;
            $temp["typeid"]=0;
            array_push($itemsarr, $temp);
        }
        $ret["tncontent"]=$lettersarr;
        $ret["mp3"]=$result[0]["ukmp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=6;
        $ret["items"]=$itemsarr;
        $ret["answer"]=$lettersarr;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;

    }


    //语境选择释义7
    private function contextChooseExplain($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        $example=$this->getExampleByExplainid($explainid,$peroid);
        if(empty($example)){
            $ret=array();
            return $ret;
        }

        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$example[0]["pic"];
        array_push($items,$itemword);
        $itemothers=$this->getExplainChooseList($explainid,2);
        array_push($items,$itemothers[0]);
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$example[0]["encontent"];
        //匹配第一个
        //$ret["tncontent"]=str_replace($result[0]["word"],"####",$ret["tncontent"]);
        $ret["mp3"]=$example[0]["mp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=7;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            //$items[$key]["content"]=$value["pic"];
            //获取远程图片
            $item[$key]["content"]=$value["explains"];
            //$questions_items[$key]->imgcontent=$base64_image_content;
            $item[$key]["typeid"]=0;
            if($result[0]["explains"]===$value["explains"]){
                $answer=$item[$key]["flag"];
            }

        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }

    //语境选择单词8
    private function contextChooseWord($wordid,$explainid,$peroid){
        //查询这个单词的信息
        $result=$this->getWordById($wordid,$explainid);
        $example=$this->getExampleByExplainid($explainid,$peroid);
        if(empty($example)){
            $ret=array();
            return $ret;
        }
        //构造选项内容
        $items=array();
        $item=array();
        $itemword["word"]=$result[0]["word"];
        $itemword["isword"]=$result[0]["isword"];
        $itemword["ukmark"]=$result[0]["ukmark"];
        $itemword["ukmp3"]=$result[0]["ukmp3"];
        $itemword["morphology"]=$result[0]["morphology"];
        $itemword["explains"]=$result[0]["explains"];
        $itemword["pic"]=$example[0]["pic"];
        $itemword["isanswer"]=1;
        $itemw=$itemword;
        $str=$example[0]["encontent"];
        preg_match('/<span>(.*)<\/span>/isU',$str,$arr);
        $strword=$arr[1];
        $itemw["word"]=$strword;
        array_push($items,$itemw);
        $itemothers=$this->getExampleWordChooseList($explainid,2);
        $itemothers[0]["isanswer"]=0;
        array_push($items,$itemothers[0]);
        $itemothers[1]["isanswer"]=0;
        array_push($items,$itemothers[1]);
        shuffle($items);
        $ret["tncontent"]=$example[0]["encontent"];
        //匹配第一个
        //$ret["tncontent"]=str_replace($result[0]["word"],"####",$ret["tncontent"]);
        $ret["mp3"]=$example[0]["mp3"];
        $ret["word"]=$result[0]["word"];
        $ret["wordid"]=$wordid;
        $ret["explainid"]=$explainid;
        $ret["questype"]=8;
        $answer="";
        foreach($items as $key=>$value){
            $item[$key]["flag"]=chr(65+$key);
            //$items[$key]["content"]=$value["pic"];
            //获取远程图片
            $item[$key]["content"]=$value["word"];
            //$questions_items[$key]->imgcontent=$base64_image_content;
            $item[$key]["typeid"]=0;
            if($value["isanswer"]==1){
                $answer=$item[$key]["flag"];
            }

        }
        $ret["items"]=$item;
        $ret["answer"]=$answer;
        $ret["useranswer"]="";
        $ret["iserror"]="";
        //这里需要将数据写入数据库
        return $ret;
    }


    //真题单选9
    private function examChoose(){

    }


    //辨析题型10
    private function analysisChoose(){
        
        
    }
   


    //构造单词的选项
    private function getWordChooseList($wordid,$itemsnum,$explainid){
        $sql="select * from engs_base_word t left join engs_base_word_explains s on t.id=s.base_wordid where t.id=%d and s.id=%d";
        $rs=M()->query($sql,$wordid,$explainid);
        //$rs=M("base_word")->where("id=%d",$wordid)->field("word,id as base_wordid,max(tags) as tag")->find();
        //$rs=$this -> where("id=%d",$wordid) -> field("word,base_wordid,(select max(tags) from engs_base_word bw where bw.id=base_wordid) as tag") -> find();
        $id=$rs[0]['base_wordid'];
        $morphology=$rs[0]["morphology"];
        $tags=$rs[0]["tags"];
        $words=$rs[0]["word"];
        $subword=substr($words,0,1);
        $isgrade1=$rs[0]["isgrade1"];
        $isgrade2=$rs[0]["isgrade2"];
        $isgrade3=$rs[0]["isgrade3"];
        $in=array();
        if(!empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(!empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and  ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }

        if(!empty($choicelist)){
            foreach($choicelist as $key=>$value){
                array_push($in,$value["word"]);
            }
        }
        if(count($choicelist)<$itemsnum){
            $itemsnum1=$itemsnum-count($choicelist);
            if(!empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                   
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$tags,$words,$id);
            }else if(empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$words,$id);
            }else if(!empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$tags,$words,$id);
            }else if(empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$words,$id);
            }
            if(!empty($choicelist1)){
                foreach($choicelist1 as $key=>$value){
                    array_push($in,$value["word"]);
                    array_push($choicelist,$value);
                }
            }

        }
        if(count($choicelist)<$itemsnum){
            //更具词性在出一次
            if(!empty($tags)){
                $scount=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scount;
                $choicelistadd = $this->query($sql,$tags,$words,$id);
                $choiselistaddcount=count($choicelistadd);
                if(!empty($choicelistadd)){
                    for($i=0;$i<$choiselistaddcount;$i++){
                        array_push($in,$choicelistadd[$i]["word"]);
                        array_push($choicelist,$choicelistadd[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            if(!empty($morphology)){
                $scounts=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scounts;
                $choicelistadds = $this->query($sql,$morphology,$words,$id);
                $choiselistaddscount=count($choicelistadds);
                if(!empty($choicelistadds)){
                    for($i=0;$i<$choiselistaddscount;$i++){
                        array_push($in,$choicelistadds[$i]["word"]);
                        array_push($choicelist,$choicelistadds[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            $scountss=$itemsnum-count($choicelist);
            $sql='SELECT t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.isword=1 and ';
            var_dump($in);
            if(count($in)>0&&!empty($in)){
                $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
            }
            $sql.=' t1.word !="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';

            $sql.='  GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by rand() LIMIT '.$scountss;
            $choicelistaddss = $this->query($sql,$words,$id);
            $choiselistaddsscount=count($choicelistaddss);
            if(!empty($choicelistaddss)){
                for($i=0;$i<$choiselistaddsscount;$i++){
                    array_push($in,$choicelistaddss[$i]["word"]);
                    array_push($choicelist,$choicelistaddss[$i]);
                }
            }
        }
        return $choicelist;
    }

    //构造单词的选项
    private function getExamplePictureChooseList($explainid,$itemsnum){
        $sql="select * from engs_base_word_explains left join engs_base_word on engs_base_word_explains.base_wordid=engs_base_word.id where engs_base_word_explains.id=%d";
        $rs=M()->query($sql,$explainid);
        $wordid=$rs[0]["base_wordid"];
        $morphology=$rs[0]["morphology"];
        $tags=$rs[0]["tags"];
        $words=$rs[0]["word"];
        $sql="select * from engs_base_word_explains_example t where t.explainid!=%d and explainid in  (select id from engs_base_word_explains where morphology='%s') order by rand() limit ".($itemsnum);
        $choicelist = $this->query($sql,$explainid,$morphology);
        if(count($choicelist)<$itemsnum){
            $sql="select * from engs_base_word_explains_example t where t.explainid!=%d  order by rand() limit 100";
            $choicelistadd = $this->query($sql,$explainid);
            $scount=$itemsnum-count($choicelist);
            for($i=0;$i<$scount;$i++){
                array_push($choicelist,$choicelistadd[$i]);
            }
        }
        return $choicelist;
    }


    //构造单词的选项
    private function getExplainChooseList($explainid,$itemsnum){
        $sql="select * from engs_base_word_explains left join engs_base_word on engs_base_word_explains.base_wordid=engs_base_word.id where engs_base_word_explains.id=%d";
        $rs=M()->query($sql,$explainid);
        $wordid=$rs[0]["base_wordid"];
        $morphology=$rs[0]["morphology"];
        $tags=$rs[0]["tags"];
        $words=$rs[0]["word"];
        $isgrade1=$rs[0]["isgrade1"];
        $isgrade2=$rs[0]["isgrade2"];
        $isgrade3=$rs[0]["isgrade3"];
        $in=array();
        if(!empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(!empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and  ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }

        if(!empty($choicelist)){
            foreach($choicelist as $key=>$value){
                array_push($in,$value["word"]);
            }
        }
        if(count($choicelist)<$itemsnum){
            $itemsnum1=$itemsnum-count($choicelist);
            if(!empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                   
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$tags,$words,$id);
            }else if(empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$words,$id);
            }else if(!empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$tags,$words,$id);
            }else if(empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$words,$id);
            }
            if(!empty($choicelist1)){
                foreach($choicelist1 as $key=>$value){
                    array_push($in,$value["word"]);
                    array_push($choicelist,$value);
                }
            }

        }
        if(count($choicelist)<$itemsnum){
            //更具词性在出一次
            if(!empty($tags)){
                $scount=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scount;
                $choicelistadd = $this->query($sql,$tags,$words,$id);
                $choiselistaddcount=count($choicelistadd);
                if(!empty($choicelistadd)){
                    for($i=0;$i<$choiselistaddcount;$i++){
                        array_push($in,$choicelistadd[$i]["word"]);
                        array_push($choicelist,$choicelistadd[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            if(!empty($morphology)){
                $scounts=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scounts;
                $choicelistadds = $this->query($sql,$morphology,$words,$id);
                $choiselistaddscount=count($choicelistadds);
                if(!empty($choicelistadds)){
                    for($i=0;$i<$choiselistaddscount;$i++){
                        array_push($in,$choicelistadds[$i]["word"]);
                        array_push($choicelist,$choicelistadds[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            $scountss=$itemsnum-count($choicelist);
            $sql='SELECT t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.isword=1 and ';
            var_dump($in);
            if(count($in)>0&&!empty($in)){
                $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
            }
            $sql.=' t1.word !="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';

            $sql.='  GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by rand() LIMIT '.$scountss;
            $choicelistaddss = $this->query($sql,$words,$id);
            $choiselistaddsscount=count($choicelistaddss);
            if(!empty($choicelistaddss)){
                for($i=0;$i<$choiselistaddsscount;$i++){
                    array_push($in,$choicelistaddss[$i]["word"]);
                    array_push($choicelist,$choicelistaddss[$i]);
                }
            }
        }
        return $choicelist;
    }

    //构造单词的选项
    private function getExampleWordChooseList($explainid,$itemsnum){
        $sql="select * from engs_base_word_explains left join engs_base_word on engs_base_word_explains.base_wordid=engs_base_word.id where engs_base_word_explains.id=%d";
        $rs=M()->query($sql,$explainid);
        $wordid=$rs[0]["base_wordid"];
        $morphology=$rs[0]["morphology"];
        $tags=$rs[0]["tags"];
        $words=$rs[0]["word"];
        $isgrade1=$rs[0]["isgrade1"];
        $isgrade2=$rs[0]["isgrade2"];
        $isgrade3=$rs[0]["isgrade3"];
        if(!empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&!empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$morphology,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(!empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$tags,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }else if(empty($tags)&&empty($morphology)){
            $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and isgrade1=%d and isgrade2=%d and isgrade3=%d and  ';
            $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
            $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum;
            $choicelist = $this->query($sql,$words,$id,$isgrade1,$isgrade2,$isgrade3);
        }

        if(!empty($choicelist)){
            foreach($choicelist as $key=>$value){
                array_push($in,$value["word"]);
            }
        }
        if(count($choicelist)<$itemsnum){
            $itemsnum1=$itemsnum-count($choicelist);
            if(!empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s" and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                   
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$tags,$words,$id);
            }else if(empty($tags)&&!empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d  and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$morphology,$words,$id);
            }else if(!empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$tags,$words,$id);
            }else if(empty($tags)&&empty($morphology)){
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid  and t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$itemsnum1;
                $choicelist1 = $this->query($sql,$words,$id);
            }
            if(!empty($choicelist1)){
                foreach($choicelist1 as $key=>$value){
                    array_push($in,$value["word"]);
                    array_push($choicelist,$value);
                }
            }

        }
        if(count($choicelist)<$itemsnum){
            //更具词性在出一次
            if(!empty($tags)){
                $scount=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.tags="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scount;
                $choicelistadd = $this->query($sql,$tags,$words,$id);
                $choiselistaddcount=count($choicelistadd);
                if(!empty($choicelistadd)){
                    for($i=0;$i<$choiselistaddcount;$i++){
                        array_push($in,$choicelistadd[$i]["word"]);
                        array_push($choicelist,$choicelistadd[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            if(!empty($morphology)){
                $scounts=$itemsnum-count($choicelist);
                $sql='SELECT distinct t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
                $sql.=' FROM engs_base_word_explains bwe ,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and bwe.morphology="%s"  And t1.word!="%s"  AND t1.id!=%d and ';
                if(count($in)>0&&!empty($in)){
                    $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
                }
                $sql.='(t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.usmp3';
                $sql.=',t1.usmp3 order by rand() LIMIT '.$scounts;
                $choicelistadds = $this->query($sql,$morphology,$words,$id);
                $choiselistaddscount=count($choicelistadds);
                if(!empty($choicelistadds)){
                    for($i=0;$i<$choiselistaddscount;$i++){
                        array_push($in,$choicelistadds[$i]["word"]);
                        array_push($choicelist,$choicelistadds[$i]);
                    }
                }
            }
        }
        if(count($choicelist)<$itemsnum){
            $scountss=$itemsnum-count($choicelist);
            $sql='SELECT t1.word,t1.isword,t1.ukmark,t1.usmp3 as ukmp3,MAX(bwe.morphology) as morphology,MAX(bwe.explains) as explains,MAX(bwe.pic) as pic';
            $sql.=' FROM engs_base_word_explains bwe,engs_base_word AS t1 WHERE  t1.id=bwe.base_wordid and t1.isword=1 and ';
            if(count($in)>0&&!empty($in)){
                $sql.=' t1.word not in ('.trim(trim(json_encode($in),"["),"]").') and ';
            }
            $sql.=' t1.word !="%s"  AND t1.id!=%d and (t1.word is not null and t1.word!="" and bwe.explains is not null and bwe.explains !="") ';

            $sql.='  GROUP BY  t1.word,t1.isword,t1.ukmark,t1.usmark,t1.ukmp3,t1.usmp3 order by rand() LIMIT '.$scountss;
            $choicelistaddss = $this->query($sql,$words,$id);
            $choiselistaddsscount=count($choicelistaddss);
            if(!empty($choicelistaddss)){
                for($i=0;$i<$choiselistaddsscount;$i++){
                    array_push($in,$choicelistaddss[$i]["word"]);
                    array_push($choicelist,$choicelistaddss[$i]);
                }
            }
        }
        return $choicelist;
    }

}
