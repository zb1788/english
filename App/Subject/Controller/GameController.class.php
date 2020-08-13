<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class GameController extends CheckController {

    public function index(){
        
        $username=cookie("username");
        $source=cookie("source");
        if($source==null||empty($source)){
            cookie("source",I("source",0));

        }
        
        $gradeid=I("gradeid","");
        if(!empty($gradeid)){
        	 	if($gradeid=='0011'||$gradeid=='0012'||$gradeid=='0013'){
                $gradeid='0010';
            }
            cookie("gradeid",$gradeid);
        }else{
            $gradeid=cookie("gradeid");
        }

        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("gradeid");
        $truename=cookie("truename");
        $subjectid=cookie("subjectid","0003");
        $moduleid="8";
        $ks_code="0";
        $ks_name="0";
        //setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);

        $gamegradeid=cookie("gamegradeid");

        if(empty($gamegradeid)||$gamegradeid!==intval($gradeid)){
            $gamegradeid=intval($gradeid);
            cookie("gamegradeid",$gamegradeid);
        }


        
        //查询当前用户的最新的一关
        $rs=M("user_word_game_level")->where("username='%s'",$username)->order("id desc")->find();
        if(empty($rs)){
            $gameid=0;
        }else{
            $gameid=$rs["gameid"];
        }
        $this->assign("gamegradeid",$gamegradeid);
        $this->assign("gameid",$gameid);
        $this->assign("gamegradename","");
        $this->display("index");
    }
    //读单词单元展示
    public function wordlist() {
        $gameid=I("gameid");
        $index=I("index");
        $sql="select * from engs_word_game_level t where t.gameid=%d order by t.sortid,t.id limit %d,1";
        $rs=M()->query($sql,$gameid,$index);
        $this->assign('levelid',$rs[0]["id"]);
        $this->display("wordlist");
    }

    //通过年级获取游戏列表
    public function getGameListByGrade(){
    	$grade=I("grade");
        $username=cookie("username");
    	//年级常量 
       $gameView=M();
        if($grade=='-1'){
           // $sql="SELECT t.*,a.sucnum,case when a.donum is null then 0 else a.donum end as donum,concat('".PROTOCOL."://".RESOURCE_DOMAIN.C('game_pic_path')."',t.pic) as gamepic,case when a.levelnum is null then 0 else a.levelnum end as curlevel,CASE WHEN t.levelnum=a.levelnum THEN 1 ELSE 0 END AS isover FROM engs_word_game t LEFT JOIN (SELECT gameid,COUNT(DISTINCT username) AS donum,SUM(sucnum) AS sucnum,SUM(CASE WHEN username='%s' THEN 1 ELSE 0 END) AS levelnum FROM (SELECT gameid,levelid,username,MAX(case when username = '%s' AND isover = 1 AND issuc = 1 THEN 1 ELSE 0 END) AS sucnum FROM engs_user_word_game_level GROUP BY gameid,levelid,username) AS b GROUP BY gameid) AS a ON t.`id`=a.gameid where t.name is not null and t.pic is not null and t.status=1 ORDER BY sortid,id";
            $sql="SELECT t.*,concat('".PROTOCOL."://".RESOURCE_DOMAIN.C('game_pic_path')."',t.pic) as gamepic FROM engs_word_game t  where t.name is not null and t.pic is not null and t.status=1 ORDER BY sortid,id";
            $rs=$gameView->query($sql);
        }else{
           // $sql="SELECT t.*,a.sucnum,case when a.donum is null then 0 else a.donum end as donum,concat('".PROTOCOL."://".RESOURCE_DOMAIN.C('game_pic_path')."',t.pic) as gamepic,case when a.levelnum is null then 0 else a.levelnum end as curlevel,CASE WHEN t.levelnum=a.levelnum THEN 1 ELSE 0 END AS isover FROM engs_word_game t LEFT JOIN (SELECT gameid,COUNT(DISTINCT username) AS donum,SUM(sucnum) AS sucnum,SUM(CASE WHEN username='%s' THEN 1 ELSE 0 END) AS levelnum FROM (SELECT gameid,levelid,username,MAX(case when username = '%s' AND isover = 1 AND issuc = 1 THEN 1 ELSE 0 END) AS sucnum FROM engs_user_word_game_level GROUP BY gameid,levelid,username) AS b GROUP BY gameid) AS a ON t.`id`=a.gameid where t.name is not null and t.pic is not null and substr(t.grades,'%s',1)=1 and  t.status=1 ORDER BY sortid,id";
             $sql="SELECT t.*,concat('".PROTOCOL."://".RESOURCE_DOMAIN.C('game_pic_path')."',t.pic) as gamepic FROM engs_word_game t  where t.name is not null and t.pic is not null and substr(t.grades,'%s',1)=1 and  t.status=1 ORDER BY sortid,id";
            $rs=$gameView->query($sql,$grade);
        }
        //echo $gameView->getLastSql();exit;
    	$this->ajaxReturn($rs);
    }

    //获取游戏下面的单词
    public function getWordListByLevelid(){
    	$levelid=I("id");
    	$sql="select * from engs_word_game_level t where t.id=%d";
    	$rs=M()->query($sql,$levelid);
    	$words=json_decode($rs[0]["words"]);
    	$this->ajaxReturn($words);
    }

    //获取游戏的关卡
    public function getLevelListByGameid(){
        $gameid=I("id");
        $username=cookie("username");
       // echo $username;exit;
        $usertype=cookie("usertype");
        $sql="SELECT t.*,case when l.rate is not null then ROUND(100*l.rate) else -1 end as score FROM engs_word_game_level t LEFT JOIN (SELECT s.*,max(s.rightnum/s.`donum`) as rate FROM `engs_user_word_game_level` as s WHERE s.isover = 1 AND s.issuc = 1 AND s.username = '%s' AND s.gameid =%d  GROUP BY `levelid`) l ON t.id = l.levelid WHERE t.gameid = %d ORDER BY sortid,id ";
        $rs=M()->query($sql,$username,$gameid,$gameid);
        //查询本游戏闯的最大的人
        // $sql="select * from engs_word_game_level t left join engs_user_word_game_level s on t.id=s.id order by id t.sortid desc";
        $this->ajaxReturn($rs);
    }

    //获取关卡的问题
    public function getQuestionListByLevelid(){
        $levelid=I("id");
        $sql="select * from engs_word_game_level t where t.id=%d";
        $rs=M()->query($sql,$levelid);
        $questions=json_decode($rs[0]["questions"],true);
        foreach($questions as $key=>$value){
            $questions[$key]["questypename"]=C("questypes")[$value["questype"]];
            $questions[$key]["cnexplain"]="";
            //查询这个单词的意思
            if($value["questype"]==6){
                $cnrs=M("base_word_explains")->where("id=%d",$value["explainid"])->find();
                $questions[$key]["cnexplain"]=$cnrs["morphology"].$cnrs["explains"];
            }
            // preg_match('/<span.*>(.*)<\/span>/isU',$str,$arr);
            // preg_replace("/<style>.+<\/style>/is", "", $msg);
            if($value["questype"]=='8'){
                $questions[$key]["tncontent"]=preg_replace('/<span>.*?<\/span>/', '<span class="tkong02">&nbsp;</span>', $questions[$key]["tncontent"]);
            }else if($value["questype"]=='7'){
                $questions[$key]["tncontent"]=preg_replace('/<span>/', '<span style="color: yellow;font-size: 20px;">', $questions[$key]["tncontent"]);
            }else if($value["questype"]=='0'||$value["questype"]=='1'||$value["questype"]=='6'||$value["questype"]=='3'){
                $mp3["name"]=$value["mp3"];
                $mp3["size"]=1;
                $mp3["format"]="mp3";
                $mp3["url"]=PROTOCOL."://".C('word_mp3_path').$value["mp3"];
                $mp3["url"] = getHttpUrl($mp3["url"]);
                $ret=array();
                array_push($ret,$mp3);
                $questions[$key]["mp3s"]=$ret;
            }
            
        }
        $this->ajaxReturn($questions);
    }

   function gameadduserinfo(){
        $index=I("index");
        $gameid=I("gameid");
        $username=cookie("username");
        $truename=cookie("truename");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("usergradeid");
        $usertype=cookie("usertype");
        $localareacode=cookie("localAreaCode");
        $areaid=cookie("areacode");
        $sql="select * from engs_word_game_level where gameid=%d order by sortid,id limit %d,2";
        $levelrs=M()->query($sql,$gameid,$index);
        $nextexitst=count($levelrs)==2?1:0;
        $levelrs=$levelrs[0];
        $levelid=$levelrs["id"];
        $questions=json_decode($levelrs["questions"],true);
        $quescount=count($questions);
        $errnum=round($quescount*0.25);
        $pregress=round((1/$quescount)*100);

        //查询当前有没有这个游戏的历史记录
        $userwordgamelevel=M("user_word_game_level");
        $userwordgamelevel->schoolid=$schoolid;
        $userwordgamelevel->gradeid=$gradeid;
        $userwordgamelevel->classid=$classid;
        $userwordgamelevel->username=$username;
        $userwordgamelevel->truename=$truename;
        $userwordgamelevel->schoolname=cookie("schoolname");
        $userwordgamelevel->classname=cookie("classname");
        $userwordgamelevel->fullname=cookie("fullname");
        $userwordgamelevel->areaid=$areaid;
        $userwordgamelevel->gameid=$gameid;
        $userwordgamelevel->levelid=$levelid;
        $userwordgamelevel->source=cookie("source");
        $userwordgamelevel->usertype=$usertype;
        $userwordgamelevel->localareacode=$localareacode;
        $userwordgamelevel->addtime=Date("Y-m-d H:i:s");
        $usergamelevelid=$userwordgamelevel->add();
   }
    //展示关卡的页面
    public function game(){
        $index=I("index");
        $gameid=I("gameid");
        $username=cookie("username");
        $truename=cookie("truename");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("usergradeid");
        $usertype=cookie("usertype");
        $localareacode=cookie("localAreaCode");
        $areaid=cookie("areacode");
        $sql="select * from engs_word_game_level where gameid=%d order by sortid,id limit %d,2";
        $levelrs=M()->query($sql,$gameid,$index);
        $nextexitst=count($levelrs)==2?1:0;
        $levelrs=$levelrs[0];
        $levelid=$levelrs["id"];
        $questions=json_decode($levelrs["questions"],true);
        $quescount=count($questions);
        $errnum=round($quescount*0.25);
        $pregress=round((1/$quescount)*100);

        //查询当前有没有这个游戏的历史记录
        // $userwordgamelevel=M("user_word_game_level");
        // $userwordgamelevel->schoolid=$schoolid;
        // $userwordgamelevel->gradeid=$gradeid;
        // $userwordgamelevel->classid=$classid;
        // $userwordgamelevel->username=$username;
        // $userwordgamelevel->truename=$truename;
        // $userwordgamelevel->schoolname=cookie("schoolname");
        // $userwordgamelevel->classname=cookie("classname");
        // $userwordgamelevel->fullname=cookie("fullname");
        // $userwordgamelevel->areaid=$areaid;
        // $userwordgamelevel->gameid=$gameid;
        // $userwordgamelevel->levelid=$levelid;
        // $userwordgamelevel->source=cookie("source");
        // $userwordgamelevel->usertype=$usertype;
        // $userwordgamelevel->localareacode=$localareacode;
        // $userwordgamelevel->addtime=Date("Y-m-d H:i:s");
        // $usergamelevelid=$userwordgamelevel->add();
        //判断是否在这个游戏中进行的
        $this->assign("nextexitst",$nextexitst);
        $this->assign("levelid",$levelid);
        $this->assign("quescount",$quescount);
        $this->assign("pregress",$pregress);
        $this->assign("errnum",$errnum);
        $this->assign("name",$levelrs["name"]);
        $this->assign("gameid",$gameid);
        $this->assign("index",$index);
        $this->display("game");
    }

    //接收用户的答案
    public function setUserGameAnswer(){
        $questionid=I("questionid");
        $request=I("request");
        $request = stripslashes($request);
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $data=I("data");
        $data = stripslashes($data);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data,true);
        $gameid=$request["gameid"];
        $index=$request["index"];
        //查询当前的levelid
        $sql="select * from engs_word_game_level where gameid=%d order by sortid,id limit %d,1";
        $levelrs=M()->query($sql,$gameid,$index);
        $levelrs=$levelrs[0];
        $levelid=$levelrs["id"];
        //是否正确
        $useriserror=true;
        foreach($data as $key=>$value){
            $flag=$value["flag"];
            $iserror=$value["iserror"];
            $iserror=(intval($iserror))==0?true:false;
            $useriserror=($useriserror&&$iserror);
            $data[$key]["dotime"]=Date("Y-m-d H:i:s");
        }
        $username=cookie("username");
        $usertype=cookie("usertype");
        $userwordgamelevel=M("user_word_game_level");
        $usergamers=$userwordgamelevel->where("username='%s' and levelid=%d and usertype=%d",$username,$levelid,$usertype)->order("id desc")->find();
        $quesanswer=json_decode($usergamers["quesanswer"],true);
        $quesanswer[$questionid]=$data;
        $quesanswer=json_encode($quesanswer);
        $userwordgamelevel->quesanswer=$quesanswer;
        $userwordgamelevel->donum=$usergamers["donum"]+1;
        $userwordgamelevel->rightnum=$usergamers["rightnum"]+($useriserror==true?1:0);
       // echo count($usergamers);exti;
        M("user_word_game_level")->where("id=%d",$usergamers["id"])->save();
    }

    public function setUserGameWordBook(){
        $username=cookie("username");
        $truename=cookie("truename");
        $schoolname=cookie("schoolname");
        $classname=cookie("classname");
        $fullname=cookie("fullname");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $usertype=cookie("usertype");
        $gradeid=cookie("usergradeid");
        $localareacode=cookie("localAreaCode");
        $areaid=cookie("areacode");
        //设置单词游戏的生词本
        $userwordgamebook=M("user_word_game_book");
        $questionid=I("questionid");
        $request=I("request");
        $request = stripslashes($request);
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $gameid=$request["gameid"];
        $index=$request["index"];
        $sql="select * from engs_word_game_level where gameid=%d order by sortid,id limit ".$index.",1";
        $levelrs=M()->query($sql,$gameid);
        $levelrs=$levelrs[0];
        $levelid=$levelrs["id"];
        $questions=json_decode($levelrs["questions"],true);
        $question=$questions[$questionid];
        $wordid=$question["wordid"];
        $explainid=$question["explainid"];
        if(!empty($wordid)&&!empty($explainid)){
            $userrs=$userwordgamebook->where("username='%s' and base_wordid=%d and base_explainid=%d ",$username,$wordid,$explainid)->find();
            if(empty($userrs)){
                $userwordgamebook->base_wordid=$wordid;
                $userwordgamebook->base_explainid=$explainid;
                $userwordgamebook->gameid=$gameid;
                $userwordgamebook->levelid=$levelid;
                $userwordgamebook->gradeid=$gradeid;
                $userwordgamebook->username=$username;
                $userwordgamebook->truename=$truename;
                $userwordgamebook->usertype=$usertype;
                $userwordgamebook->schoolid=$schoolid;
                $userwordgamebook->classid=$classid;
                $userwordgamebook->localareacode=$localareacode;
                $userwordgamebook->areaid=$areaid;
                $userwordgamebook->addtime=Date("Y-m-d H:i:s");
                $userwordgamebook->add();
            }else{
                $userwordgamebook->where("id=%d",$userrs["id"])->setField("errornum",$userrs["errornum"]+1);
            }
        }
    }

    public function getUserWordBookList(){
        $username=cookie("username");
        $areaid=cookie("areacode");
        $word_mp3_path = PROTOCOL."://".C("word_mp3_path");
        $word_mp3_path = getHttpUrl($word_mp3_path);
        $sql="select distinct t.id as bookid,'1' as size,'mp3' as format,ukmp3 as name,ukmp3 as mp3,z.morphology,concat('".$word_mp3_path."',ukmp3) as url,concat('".PROTOCOL."://".C("word_pic_path")."',z.pic) as pic,s.id,t.base_wordid,t.base_explainid,s.word,z.explains,z.morphology,s.ukmark from engs_user_word_game_book t left join engs_base_word s on t.base_wordid=s.id left join engs_base_word_explains z on t.base_explainid=z.id where t.isdel=1 and username='%s' ";
        $sql=$sql." and s.word is not null and z.explains is not null";
        $rs=M()->query($sql,$username);
        $ret["data"]=$rs;
        $downlist=array();
        foreach($rs as $key=>$value){
            $temp["size"]=1;
            $temp["format"]="mp3";
            $temp["name"]=$value["name"];
            $temp["url"]=$value["url"];
            $temp["url"] = getHttpUrl($temp["url"]);
            array_push($downlist,$temp);
        }
        $ret["downlist"]=$downlist;
        $this->ajaxReturn($ret);
    }

    public function setUserGameOver(){
        $usergameid=I("usergameid");
        M("user_word_game")->where("id=%d",$usergameid)->setField("isover",1);
        //设置这个游戏中的level中的人数
        
        //设置闯关的人数
        $gamers=M("user_word_game")->where("id=%d",$usergameid)->find();
        $sql="update engs_word_game set dotimenum=dotimenum+1 where id=".$gamers["gameid"];;
        M()->execute($sql);
        $userrs=M("user_word_game")->where("gameid=%d",$gamers["gameid"])->field("count(distinct username) as num")->group("gameid")->find();
        M("word_game")->where("id=%d",$gamers["gameid"])->setField("donum",$userrs["num"]);
    }

    public function setUserGameLevelOver(){
        $request=I("request");
        $issuc=I("issuc/d",0);
        $isover=I("isover/d",0);
        $request = stripslashes($request);
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $gameid=$request["gameid"];
        $index=$request["index"];
        $rightnum=I("rightnum/d");
        $donum=I("donum/d");
        $username=cookie("username");
        $truename=cookie("truename");
        $schoolname=cookie("schoolname");
        $classname=cookie("classname");
        $fullname=cookie("fullname");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $usertype=cookie("usertype");
        $gradeid=cookie("usergradeid");
        $source=cookie("source");
        $localareacode=cookie("localAreaCode");
        $areaid=cookie("areacode");
        $sql="select * from engs_word_game_level t where gameid=%d order by sortid,id limit %d,1";
        $levelrs=M()->query($sql,$gameid,($index));
        $levelid=$levelrs[0]["id"];
        $userwordgamelevel=M("user_word_game_level");
        $userrs=$userwordgamelevel->where("username='%s' and levelid=%d and usertype=%d",$username,$levelid,$usertype)->order("id desc")->find();
        $userwordgamelevel->submittime=Date("Y-m-d H:i:s");
        $userwordgamelevel->isover=$isover;
        $userwordgamelevel->issuc=$issuc;
        $userwordgamelevel->where("id=%d",$userrs["id"])->save();
        //这是游戏关卡的人数
        $sql="update engs_word_game_level set donum=donum+1,rightnum=rightnum+".$issuc." where id=%d";
        M()->execute($sql,$levelid);
        //当闯关成功的时候进行更新排名的数据
        $donum=$userrs["donum"];
        //$rightnum=$userrs["rightnum"];
        $source=cookie("source")==null?0:cookie("source");
        if($isover==1&&$issuc==1){
            $wordgamerank=M("user_word_game_rank");
            $sqls="select * from engs_user_word_game_rank where username='%s' and gameid=%d and levelid=%d and usertype=%d and isover=1 and issuc=1 and source=%d";
            $overrs=M()->query($sqls,$username,$gameid,$levelid,$usertype,$source);
            if(empty($overrs)){
                $wordgamerank->schoolid=$schoolid;
                $wordgamerank->gradeid=$gradeid;
                $wordgamerank->classid=$classid;
                $wordgamerank->username=$username;
                $wordgamerank->truename=$truename;
                $wordgamerank->areaid=$areaid;
                $wordgamerank->gameid=$gameid;
                $wordgamerank->levelid=$levelid;
                $wordgamerank->usertype=$usertype;
                $wordgamerank->localareacode=$localareacode;
                $wordgamerank->donum=$donum;
                $wordgamerank->rightnum=$rightnum;
                $wordgamerank->wordgameuserid=$userrs["id"];
                $wordgamerank->submittime=Date("Y-m-d H:i:s");
                $wordgamerank->source=$source;
                $wordgamerank->add();
            }else{
                if($rightnum>$overrs[0]["rightnum"]){
                    $data["donum"]=$donum;
                    $data["rightnum"]=$rightnum;
                    $data["submittime"]=Date("Y-m-d H:i:s");
                    $wordgamerank->where("username='%s' and gameid=%d and levelid=%d and isover=1 and issuc=1 and source=%d",$username,$gameid,$levelid,$source)->save($data);
                }
            }
        }
    }

    public function getWordinfo(){
        $wordid=I("wordid");
        $explainid=I("explainid");
        $rs=M("base_word")->where("id=%d",$wordid)->find();
        //查询释义
        $explainrs=M("base_word_explains")->where("id=%d",$explainid)->find();
        //查询释义的例句
        $explainexamples=M("base_word_explains_example")->where("explainid=%d",$explainid)->select();
        //查询扩展1 词形变化  2 记忆法 3 发散记忆 4 拓展用法
        $extendrs=M("base_word_extend")->where("wordid=%d",$wordid)->order("sortnum")->select();
        //记忆法
        $extendrs=M("base_word_memory")->where("wordid=%d",$wordid)->order("sortnum")->select();
    }

    //进入关卡
    public function gamelevel(){
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("gradeid");
        $truename=cookie("truename");
        $moduleid="8";
        $ks_code="0";
        $ks_name="0";
        



        $id=I("id");
        $username=cookie("username");
        $sql="SELECT sum(case when engs_user_word_game_book.base_wordid is not null then 1 else 0 end) AS num,SUM(CASE WHEN gameid = %d and engs_user_word_game_book.base_wordid is not null THEN 1  ELSE 0 END) AS levelnum FROM";
        $sql=$sql." engs_user_word_game_book,engs_base_word,engs_base_word_explains WHERE engs_user_word_game_book.base_wordid=engs_base_word.id and engs_user_word_game_book.base_explainid=engs_base_word_explains.id and username = '%s' AND isdel = 1 ";
        $rs=M()->query($sql,$id,$username);
        if(empty($rs)||empty($rs[0]["num"])){
            $this->assign("wordnum","0");
            $this->assign("num","0");
        }else{
            $num=$rs[0]["levelnum"]==""?0:$rs[0]["levelnum"];
            $this->assign("wordnum",$rs[0]["num"]);
            $this->assign("num",$num);
        }
        $rank=0;
        $namers=M("word_game")->where("id=%d",$id)->find();
        $ks_name=$namers["name"];
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,"0003",$ks_name);

        //计算用户的排名
        $mysql="select MAX(submittime) AS submittime,SUM(rightnum) AS levelnum from engs_user_word_game_rank t where gameid=%d  and username='%s' and isover=1 and issuc=1";
        $myrs=M()->query($mysql,$id,$username);
        if(!empty($myrs)&&$myrs[0]["submittime"]!=null){
            $levelnum=$myrs[0]["levelnum"];
            $submittime=$myrs[0]["submittime"];
        }

        if($levelnum!=0){
            $mysql="select username,MAX(submittime) AS submittime,SUM(rightnum) AS levelnum from engs_user_word_game_rank t where gameid=".$id." group by t.username having levelnum>".$levelnum." or (levelnum=".$levelnum." and submittime<'".$submittime."')";
            $levelrs=M()->query($mysql);
            $rank=count($levelrs);
            $rank=$rank+1;
        }else{
            $rank=0;
        }

        // $ret["levelnum"]=$levelnum;
        // $ret["rank"]=$rank+1;
        // $ret["truename"]=$truename;
        
        //计算排名以
        $sql="select username,count(distinct levelid) as num from engs_user_word_game_level where gameid=%d and isover=1 and issuc=1  group by username order by num desc";
        $rrs=M()->query($sql,$id);
        if(empty($rrs)){
            $count=0;
        }else{
            $count=count($rrs);
        }
        $this->assign("docount",$rank);
        //这个游戏的最高关卡
        $maxsql="select t.id as maxid,max(username) as maxuser,count(*) as maxnum from engs_user_word_game_level t";
        $maxsql=$maxsql." left join (select * from engs_word_game_level where donum>0 and gameid=%d order by sortid,id";
        $maxsql=$maxsql." limit 1) s on t.levelid=s.id where t.gameid=%d and t.username is not null and s.id is not null";
        $maxsql=$maxsql." group by t.id";
        $maxrs=M()->query($maxsql,$id,$id);
        $maxuser="";
        $maxnum=0;
        $maxid=0;
        if(!empty($maxrs)){
            $maxuser=$maxrs[0]["maxuser"];
            $maxnum=$maxrs[0]["maxnum"];
            $maxid=$maxrs[0]["maxid"];
        }
        $this->assign("maxuser",$maxuser);
        $this->assign("maxnum",$maxnum);
        $this->assign("maxid",$maxid);
        $this->assign("count",$count);
        $this->assign("rank",$rank);
        $this->display("gamelevel");
    }


    //排名
    public function getUserRankList(){
        $type=I("type/d");
        $gameid=I("gameid");
        $truename=cookie("truename");
        $username=cookie("username");
        $classid=cookie("classid");
        $schoolid=cookie("schoolid");
        //计算自己闯关数量
        $mysql="SELECT sum(wordnum) as  wordnum FROM (select t.levelid from engs_user_word_game_level t WHERE t.`isover`=1 AND t.`issuc`=1 AND t.`gameid`=%d AND username='%s' group by t.levelid) s left join engs_word_game_level p on s.levelid=p.id where p.id is not null";
        $myrs=M()->query($mysql,$gameid,$username);
        $mywordnum=$myrs[0]["wordnum"];
        $esql="SELECT count(distinct t.base_wordid,t.base_explainid) as errnum FROM engs_user_word_game_book t WHERE t.gameid = %d AND t.username = '%s' AND t.`isdel`=1 AND t.`levelid` IN (SELECT levelid FROM engs_user_word_game_level WHERE username = '%s' and gameid=%d  AND `isover` = 1 AND `issuc` = 1  )";
        $ers=M()->query($esql,$gameid,$username,$username,$gameid);
        if(empty($ers)){
            $wordsnum=$mywordnum-0;
        }else{
            if(empty($ers[0]["errnum"])){
                $wordsnum=$mywordnum-0;
            }else{
                $wordsnum=$mywordnum-$ers[0]["errnum"];
            }
        }
        if($type==0){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,count(distinct levelid) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND classid = '%s' AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime";
            $rs=M()->query($sql,$gameid,$classid);
        }else if($type==1){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,count(distinct levelid) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND schoolid = '%s' AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime";
            $rs=M()->query($sql,$gameid,$schoolid);
        }else if($type==2){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,count(distinct levelid) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime";
            $rs=M()->query($sql,$gameid);
        }
        $ret["username"]=$username;
        $ret["truename"]=$truename;
        $ret["mywordsnum"]=$wordsnum;
        $ret["ranklist"]=$rs;
        $this->ajaxReturn($ret);
    }

    //排名
    public function getUserStarRankList(){
        $type=I("type/d");
        $gameid=I("gameid");
        $truename=cookie("truename");
        $username=cookie("username");
        $classid=cookie("classid");
        $schoolid=cookie("schoolid");
        $page=I("page/d",0);

        $size=I("size/d",100);
        $page=$page*$size;

        //查询列表页面
        if($type==0){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,SUM(rightnum) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND classid = '%s' AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime limit ".$page.",".$size;
            $rs=M()->query($sql,$gameid,$classid);
        }else if($type==1){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,SUM(rightnum) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND schoolid='%s' AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime limit ".$page.",".$size;
            $rs=M()->query($sql,$gameid,$schoolid);
        }else if($type==2){
            $sql="SELECT username,MAX(truename) AS truename,MAX(submittime) AS submittime,SUM(rightnum) AS levelnum FROM engs_user_word_game_rank WHERE gameid = %d AND isover = 1 AND issuc = 1 GROUP BY username order by levelnum desc,submittime limit ".$page.",".$size;
            $rs=M()->query($sql,$gameid);
        }
        $ret["ranklist"]=$rs;
        $this->ajaxReturn($ret);
    }


    //排名
    public function getRankList(){
        $type=I("type/d");
        $gameid=I("gameid");
        //默认是没有省份的
        $localareacode=cookie("localareacode");
        $truename=cookie("truename");
        $username=cookie("username");
        $classid=cookie("classid");
        $schoolid=cookie("schoolid");
        if($type==0){
            $sql="select username,truename,count(distinct levelid) as levelnum,SUM(CASE WHEN (rightnum / donum)=1 THEN 3 WHEN round(rightnum*100 / donum)>=90 THEN 2 else 1 END) AS starnum  from engs_user_word_game_level where gameid=%d and classid='%s' and isover=1 and issuc=1 group by username order by levelnum,username desc";
            $rs=M()->query($sql,$gameid,$classid);
        }else if($type==1){
            $sql="select username,truename,count(distinct levelid) as levelnum,SUM(CASE WHEN (rightnum / donum)=1 THEN 3 WHEN round(rightnum*100 / donum)>=90 THEN 2 else 1 END) AS starnum  from engs_user_word_game_level where gameid=%d and schoolid='%s' and isover=1 and issuc=1 group by username order by levelnum,username desc";
            $rs=M()->query($sql,$gameid,$schoolid);
        }else if($type==2){
            $sql="select username,truename,count(distinct levelid) as levelnum,SUM(CASE WHEN (rightnum / donum)=1 THEN 3 WHEN round(rightnum*100 / donum)>=90 THEN 2 else 1 END) AS starnum  from engs_user_word_game_level where gameid=%d  and isover=1 and issuc=1 group by username order by levelnum,username desc";
            $rs=M()->query($sql,$gameid);
        }
        $ret["username"]=$username;
        $ret["truename"]=$truename;
        $ret["ranklist"]=$rs;
        $this->ajaxReturn($ret);
    }

    //解析
    public function getQuestionAnalysisByLevelWord(){
        $levelid=I("levelid");
        $wordid=I("wordid");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $wordrs=json_decode($rs["questions"],true);
        $analysis=$wordrs[$wordid]["analysis"];
        $explainid=$wordrs[$wordid]["explainid"];
        $examples=M("base_word_explains_example")->where("explainid=%d",$explainid)->select();
        $ret["analysis"]=$analysis;
        $ret["examples"]=$examples;
        $this->ajaxReturn($ret);
    }


    //单词的字典
    public function getDictByWordidExplainid(){
        $wordid=I("wordid");
        $explainid=I("explainid");
        $mp3list=array();
        $sql="select *,concat('".PROTOCOL."://".C("word_mp3_path")."',ukmp3) as mp3 from engs_base_word t left join (SELECT * FROM engs_base_word_explains WHERE id=%d) s on t.id=s.base_wordid where t.id=%d";
        $word=M()->query($sql,$explainid,$wordid);
        unset($temp);
        $temp["size"]="1";
        $temp["name"]=$word[0]["ukmp3"];
        $temp["url"]=$word[0]["mp3"];
        $temp["url"] = getHttpUrl($temp["url"]);
        $temp["format"]="mp3";
        array_push($mp3list,$temp);
        //处理$word
        $word[0]["extend_json"]=json_decode(urldecode($word[0]["extend_json"]),true);
        if($word[0]["extend_json"]==null){
            $word[0]["extend_json"]=array();
        }
        
        $examples=M("base_word_explains_example")->where("explainid=%d",$explainid)->field("*,concat('".PROTOCOL."://".C("example_pic_path")."',pic) as expic,concat(mp3,'.mp3') as exmp3")->select();
        $ret["words"]=$word[0];
        $ret["examples"]=$examples;
        
        foreach($examples as  $key=>$value){
            unset($temp);
            $temp["size"]="1";
            $temp["name"]=$value["mp3"].".mp3";
            $temp["url"]=PROTOCOL."://".C("example_mp3_path").$value["mp3"].".mp3";
            $temp["url"] = getHttpUrl($temp["url"]);
            $temp["format"]="mp3";
            array_push($mp3list,$temp);
        }
        $ret["mp3list"]=$mp3list;
        $this->ajaxReturn($ret);
    }

    //查看解析
    public function analysis(){
        $wordid=I("wordid");
        $example_mp3_path=PROTOCOL."://".C('example_mp3_path');
        $example_mp3_path = getHttpUrl($example_mp3_path);
        $explainid=I("explainid");
        $questionid=I("questionid");
        $gameid=I("gameid");
        $index=I("index");
        $rs=M("word_game_level")->where("gameid=%d",$gameid)->order("sortid")->limit($index.",1")->find();
        //$levelid=$levelrs["id"];
        //$rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($rs["questions"],true);
        $word=$questions[$questionid];
        $sql="select *,concat('".PROTOCOL."://".C("example_pic_path")."',pic) as expic,concat('".$example_mp3_path."',mp3) as exmp3 from engs_base_word_explains_example t where explainid=".$explainid;
        $examples=M()->query($sql);
        $this->assign("word",$word["word"]);
        $this->assign("analysis",$word["analysis"]);
        $this->assign("examples",$examples);
        $this->assign("example_mp3_path",$example_mp3_path);
        $this->display();
    }

    //单词学习页面
    public function wordstudy(){
        $index=I("index");
        $this->assign("index",$index);
        $this->display();
    }


    public function getMyStarRank(){
        $gameid=I("gameid");
        $type=I("type");
        $localareacode=cookie("localareacode");
        $truename=cookie("truename");
        $username=cookie("username");
        $classid=cookie("classid");
        $schoolid=cookie("schoolid");
        $submittime=Date("Y-m-d H:i:s");
        $levelnum=0;
        $rank=0;

        //查询用户是多少名
        $mysql="select MAX(submittime) AS submittime,SUM(rightnum) AS levelnum from engs_user_word_game_rank t where gameid=%d  and username='%s' and isover=1 and issuc=1";
        $myrs=M()->query($mysql,$gameid,$username);
        if(!empty($myrs)&&$myrs[0]["submittime"]!=null){
            $levelnum=$myrs[0]["levelnum"];
            $submittime=$myrs[0]["submittime"];
        }

        if($levelnum!=0){
            $mysql="select username,MAX(submittime) AS submittime,SUM(rightnum) AS levelnum from engs_user_word_game_rank t where gameid=".$gameid;
            if($type==0){
                $mysql=$mysql." and classid = '".$classid."' group by t.username having levelnum>".$levelnum." or (levelnum=".$levelnum." and submittime<'".$submittime."')";
            }else if($type==1){
                $mysql=$mysql." and schoolid = '".$schoolid."' group by t.username having levelnum>".$levelnum." or (levelnum=".$levelnum." and submittime<'".$submittime."')";
            }else if($type==2){
                $mysql=$mysql." group by t.username having levelnum>".$levelnum." or (levelnum=".$levelnum." and submittime<'".$submittime."')";
            }
            $levelrs=M()->query($mysql);
            $rank=count($levelrs)+1;
        }
        $ret["levelnum"]=$levelnum;
        $ret["rank"]=$rank;
        $ret["truename"]=$truename;
        $this->ajaxReturn($ret);

    }
}
