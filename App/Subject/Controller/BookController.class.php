<?php
namespace Subject\Controller;
use Think\Controller;

class BookController extends CheckController {
    //获取子分类
    public function getCata(){
        $parentid=I("parentid");
        if($parentid!='-1'){
            $rs=M("book_cataglory")->where("parentid=%d",$parentid)->order("sortid,id")->field("id,name as title")->select();
        }else{
            $rs=M("book_cataglory")->order("sortid,id")->field("id,name as title,parentid,remark")->select();
            $rs=treeData($rs,0);

        }
        $this->ajaxReturn($rs);
    }

    //获取绘本
    public function getBookList(){
        $parentid=I("parentid/d");
        $cataid=I("catid");
        $username = cookie("username");
        $sql="SELECT '".$username."' as username,Book.id AS id,Book.ks_code AS ks_code,Book.name AS bookname,concat('".PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/book/pic/',Book.pic) AS pic,BookCataglory.id AS cataid,BookCataglory.name AS cataglory,score FROM engs_book Book LEFT JOIN (SELECT bookid,max(score) AS score FROM engs_book_user WHERE username='%s' AND submittime IS NOT NULL GROUP BY bookid) s ON Book.id=s.bookid LEFT JOIN engs_book_cataglory BookCataglory ON Book.ks_code=BookCataglory.id WHERE ( Book.ks_code=%d and Book.`isdel`=1 and Book.`status`=1)  order by Book.sortid,Book.id";
        $rs=M()->query($sql,cookie("username"),$parentid);
        //查询他的父亲
        // $sql="SELECT T2.id,T2.name FROM (SELECT @r AS _id,(SELECT @r := parentid FROM engs_book_cataglory WHERE id = _id) AS parent_id,@l := @l + 1 AS lvl FROM (SELECT @r := ".$parentid.",@l := 0) vars,engs_book_cataglory h WHERE @r <> 0) T1 JOIN engs_book_cataglory T2 ON T1._id = T2.id ORDER BY T1.lvl DESC ";
        // $book_cataglory=M()->query($sql);
        // $ret["parents"]=$book_cataglory;
        $ret["data"]=$rs;
        $this->ajaxReturn($ret);
    }

    //获取绘本的图片
    public function getBookPic(){
        $bookid=I("id");
        $index=I("index/d",1);
        $batchid=I("batchid",0);
        $index=$index-1;
        //$sql="select * from engs_book_pic where bookid=%d and isdel=1 order by pageindex,id limit ".$index.",1";
        $sql="select engs_book_pic.*,engs_book_page_content.id as contentid,engs_book_page_content.encontent,engs_book_page_content.cncontent,engs_book_page_content.filename as mp3 from engs_book_pic left join engs_book_page_content on engs_book_pic.id = engs_book_page_content.`bookpicid` where bookid=%d and isdel=1 order by pageindex,engs_book_pic.id,engs_book_page_content.id,engs_book_page_content.sortid ";
        $bookpicrs=M()->query($sql,$bookid);
        
        $result = array();
        foreach($bookpicrs as $key=>$value){
            if(empty($bookpicrs["mp3"])){
                $value["isaudio"]=0;
            }else{
                $value["isaudio"]=1;
            }
            $value["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/book/pic/".$value["filename"];
            $book_mp3_path = C("book_mp3_path");
            $book_mp3_path = str_replace("8443","8080",$book_mp3_path);
            $value["name"]=$value["mp3"];
            $value["url"]="http://".$book_mp3_path.$value["mp3"];
            $value["mp3"]="http://".$book_mp3_path.$value["mp3"];
            $value["mp3time"]=2;
            $value["size"]=1;
            $value["format"]="mp3";
            $result[$key] = $value;
            $result[$key]["contents"][] = $value;
        }
        $ret["data"] = $result;
        //获取单词
        $word_mp3_path = C('word_mp3_path');
        $word_mp3_path = str_replace("8443","8080",$word_mp3_path);
        $sql="select engs_base_word.word,engs_base_word_explains.explains,engs_base_word_explains.morphology,concat('http://".$word_mp3_path."',engs_base_word.usmp3) as mp3 from engs_book_page_words";
        $sql=$sql." left join engs_book_pic on bookpicid=engs_book_pic.id,engs_base_word,engs_base_word_explains where engs_base_word.id=";
        $sql=$sql."engs_book_page_words.wordid and engs_base_word_explains.id=engs_book_page_words.explainsid and ";
        $sql=$sql."engs_book_pic.bookid=%d and engs_book_pic.isdel=1 order by engs_book_pic.pageindex,engs_book_page_words.sortid";
        $rs=M()->query($sql,$bookid);
        $ret["word"] = $rs;

        //获取问题
        $sql="SELECT *,engs_book_page_questions.id as quesid FROM engs_book_page_questions LEFT JOIN (SELECT z.answer as useranswer,z.`questionid` FROM engs_book_user_answer z WHERE  username='%s') l ON engs_book_page_questions.id=l.questionid LEFT JOIN engs_book_pic ON ";
        $sql=$sql."engs_book_page_questions.`bookpicid`=engs_book_pic.`id` where ";
        $sql=$sql."engs_book_pic.bookid=%d order by engs_book_page_questions.`sortid`";
        $rs=M()->query($sql,cookie("username"),$bookid);
        foreach($rs as $key=>$value){
            $rs[$key]["quesitems"]=json_decode($value["items"]);
            $resource_path = PROTOCOL."://".C('resource_path');
            $rs[$key]["tncontent"]=str_replace('/uploads',$RESOURCE_DOMAIN.'/yylmp3/uploads',$rs[$key]["tncontent"]);
        }
        $ret["question"] = $rs;
        $ret["tonken"] = encode(cookie("username")."|".$bookid,C("appkey"));
        // $bookpicrs=$bookpicrs[0];
       
        // $bookpicrs["pic"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/book/pic/".$bookpicrs["filename"];
        // $picid=$bookpicrs["id"];
        // $contentrs=M("book_page_content")->where("bookpicid=%d",$picid)->order("sortid,id")->select();
        // $book_mp3_path = C("book_mp3_path");
        // $book_mp3_path = str_replace("8443","8080",$book_mp3_path);
        // foreach($contentrs as $key=>$value){
        //     $contentrs[$key]["mp3"]="http://".$book_mp3_path.$value["filename"];
        // }
        //$bookpicrs["data"]=$result;
        $this->ajaxReturn($ret);
    }

    //获取绘本的单词
    public function getBookQuestionList(){
        $bookid=I("bookid");
        $batchid=I("batchid");
        $sql="SELECT *,engs_book_page_questions.id as quesid FROM engs_book_page_questions LEFT JOIN (SELECT z.answer as useranswer,z.`questionid` FROM engs_book_user_answer z WHERE z.`batchid`=%d AND username='%s') l ON engs_book_page_questions.id=l.questionid LEFT JOIN engs_book_pic ON ";
        $sql=$sql."engs_book_page_questions.`bookpicid`=engs_book_pic.`id` where ";
        $sql=$sql."engs_book_pic.bookid=%d order by engs_book_page_questions.`sortid`";
        $rs=M()->query($sql,$batchid,cookie("username"),$bookid);
        foreach($rs as $key=>$value){
            $rs[$key]["quesitems"]=json_decode($value["items"]);
            $resource_path = PROTOCOL."://".C('resource_path');
            $rs[$key]["tncontent"]=str_replace('/uploads',$resource_path.'uploads',$rs[$key]["tncontent"]);
        }
        $this->ajaxReturn($rs);
    }

    //获取绘本的问题
    public function getBookWordList(){
        $bookid=I("id");
        $word_mp3_path = C('word_mp3_path');
        $word_mp3_path = str_replace("8443","8080",$word_mp3_path);
        $sql="select engs_base_word.word,engs_base_word_explains.explains,engs_base_word_explains.morphology,concat('http://".$word_mp3_path."',engs_base_word.usmp3) as mp3 from engs_book_page_words";
        $sql=$sql." left join engs_book_pic on bookpicid=engs_book_pic.id,engs_base_word,engs_base_word_explains where engs_base_word.id=";
        $sql=$sql."engs_book_page_words.wordid and engs_base_word_explains.id=engs_book_page_words.explainsid and ";
        $sql=$sql."engs_book_pic.bookid=%d and engs_book_pic.isdel=1 order by engs_book_pic.pageindex,engs_book_page_words.sortid";
        $rs=M()->query($sql,$bookid);
        $this->ajaxReturn($rs);
    }

    //获取用户答案
    public function saveAnswer(){
        $id=I("id");
        $batchid=I("batchid");
        $iserror=I("iserror");
        $bookid=I("bookid");
        $answer=I("answer");
        $book_user_answer=M("book_user_answer");
        //首先查询然后修改
        $rs=$book_user_answer->where("username='%s' and batchid=%d and questionid=%d",cookie("username"),$batchid,$id)->find();
        if(empty($rs)){
            $book_user_answer->username=cookie("username");
            $book_user_answer->truename=cookie("truename");
            $book_user_answer->localareacode=cookie("localAreaCode");
            $book_user_answer->questionid=$id;
            $book_user_answer->usertype=cookie("usertype");
            $book_user_answer->iserror=$iserror;
            $book_user_answer->answer=$answer;
            $book_user_answer->batchid=$batchid;
            $book_user_answer->bookid=$bookid;
            $book_user_answer->classid=cookie("classid");
            $book_user_answer->addtime=Date("Y-m-d H:i:s");
            $book_user_answer->add();
        }else{
            $book_user_answer->iserror=$iserror;
            $book_user_answer->answer=$answer;
            $book_user_answer->addtime=Date("Y-m-d H:i:s");
            $book_user_answer->where("id=%d",$rs["id"])->save();
        }
        
    }

    //首页
    public function index(){
        $id=I("id/d");
        $first=I("first/d");
        $second=I("second/d");
        $third=I("third/d");
        $rs=M("book")->where("id=%d",$id)->find();
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("gradeid");
        $truename=cookie("truename");
        cookie("subjectid","0003");
        $subjectid="0003";
        $moduleid="5";
        $ks_code="0";
        $ks_name=$rs["name"];
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
        $pic=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/book/pic/".$rs["pic"];
        $this->assign("pic",$pic);
        $this->assign("first",$first);
        $this->assign("second",$second);
        $this->assign("third",$third);
        $this->assign("id",$id);
        $this->display("index");
    }

    //绘本
    public function book(){
        $bookid=I("id");
        $piccount=M("book_pic")->where("bookid=%d and isdel=1",$bookid)->count();
        $this->assign("piccount",$piccount);
        $this->display("book");
    }



   //用户开始阅读
    public function setUserBook(){
        $id=I("id");
        $score=I("score/d",0);
        $user_book=M("book_user");
        $user_book->username=cookie("username");
        $user_book->usertype=cookie("usertype");
        $user_book->localareacode=cookie("localAreaCode");
        $user_book->bookid=$id;
        $user_book->addtime=Date("Y-m-d H:i:s");
        $user_book->submittime = Date("Y-m-d H:i:s");
        $user_book->score = $score;
        $id=$user_book->add();
        $ret["id"]=$id;
        $this->ajaxReturn($ret);
    }

    //疑问
    public function translation(){
        $bookid=I("id");
        $sql="select * from engs_book_page_content left join engs_book_pic on engs_book_page_content.bookpicid=engs_book_pic.id where engs_book_pic.bookid=%d order by engs_book_page_content.sortid";
        $rs=M()->query($sql,$bookid);
        $this->assign("contents",$rs);
        $this->assign("id",$bookid);
        $this->display("translation");
    }

    //疑问
    public function wordlist(){
        $bookid=I("id");
        //查询是否有试题
        $sql="SELECT * FROM engs_book_page_questions t LEFT JOIN engs_book_pic s ON t.`bookpicid` = s.`id` WHERE s.`bookid` = %d";
        $rs=M()->query($sql,$bookid);
        $isqueustion=0;
        if(!empty($rs)){
            $isqueustion=1;
        }
        $this->assign("isqueustion",$isqueustion);
        $this->assign("id",$bookid);
        $this->display("wordlist");
    }

    //疑问
    public function question(){
        $bookid=I("id");
        $this->assign("id",$bookid);
        $this->display("question");
    }



    //分数排名
    public function getRank(){
        $username-cookie("username");
        $classid=cookie("classid");
        $bookid=I("bookid");
        $sql="SELECT * FROM (SELECT  MAX(id) AS id,batchid,MAX(ADDTIME) AS answertime,SUM(iserror) AS score,username,truename ";
        $sql=$sql." FROM engs_book_user_answer t WHERE t.`bookid` = %d AND t.classid = '%s' GROUP BY t.batchid ORDER BY answertime DESC) ";
        $sql=$sql." a WHERE EXISTS (SELECT 1 FROM(SELECT MAX(ADDTIME) AS answertime,username ";
        $sql=$sql."  FROM engs_book_user_answer t WHERE t.`bookid` = %d AND t.classid = '%s' GROUP BY t.username ORDER BY answertime DESC) b ";
        $sql=$sql."  WHERE a.username = b.username AND a.answertime = b.answertime) order by score desc";
        $result=M()->query($sql,$bookid,$classid,$bookid,$classid);
        $arr["username"]=cookie("username");
        $arr["truename"]=cookie("truename");
        $arr["ranklist"]=$result;
        $this->ajaxReturn($arr);
    }

    //用户本书完成
    public function setUserBookOver(){
        $batchid=I("batchid");
        M("book_user")->where("id=%d",$batchid)->setField("submittime",Date("Y-m-d H:i:s"));
    }
        
        
    public function cataglory(){
        $gradeid=I("gradeid","");
        if(!empty($gradeid)){
            cookie("gradeid",$gradeid);
        }
        $this->display("cataglory");
    }

	
}