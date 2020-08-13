<?php
namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class WordController extends CheckController {
    //通过curindex获取单元
    public function getUnitByNum(){
        $curindex=I("curunitindex");
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $subjectid=$request["subjectid"];
        $gradeid=$request["gradeid"];
        $termid=$request["termid"];
        $versionid=$request["versionid"];
        if(empty($termid)){
            $termid=cookie("termid");
        }
        if(empty($versionid)){
            $versionid=cookie("versionid");
        }
        $word=D("Word","Service");
        $data=$word->getUnitByNum($gradeid,$termid,$versionid,$subjectid,$curindex);
        $this->ajaxReturn($data);
    }


    //获取用户选择的版本
    public function getUserBookList(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $user=D("User","Service");
        $rs=$user->getUserBookList($this->user);
        $this->ajaxReturn($rs);
    }


    //获取读单词的单元的信息
    public function getWordUnitData(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $unit=D("rms_unit");
        $log=D("log","Service");
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
        $userconfig=getUserConfig($subjectid,$gradeid,$termid,$versionid,$this->user);
        $learnways=getmodule($moduleid);
        $type=$learnways["type"];
        //获取秘籍
        if(!empty($termid)&&!empty($versionid)&&$termid!='0'&&$versionid!='0'){
            if($subjectid=='0001'){
                $result=$unit->getCnUnitListData($gradeid,$termid,$versionid,$subjectid,$this->user["username"],$moduleid,$type);
                $name=$unit->getCnVersionById($gradeid,$termid,$versionid,$subjectid);
            }else if($subjectid=="0003"){
                $result=$unit->getEnUnitListData($gradeid,$termid,$versionid,$subjectid,$this->user["username"],$moduleid,$type);
                $name=$unit->getEnVersionById($gradeid,$termid,$versionid,$subjectid);
            }   
        }else{
            if($subjectid=='0001'){
                $result=$unit->getCnUnitListData($gradeid,$userconfig["termid"],$userconfig["versionid"],$subjectid,$this->user["username"],$moduleid,$type);
                $name=$unit->getCnVersionById($gradeid,$userconfig["termid"],$userconfig["versionid"],$subjectid);
            }else if($subjectid=="0003"){
                $result=$unit->getEnUnitListData($gradeid,$userconfig["termid"],$userconfig["versionid"],$subjectid,$this->user["username"],$moduleid,$type);
                $name=$unit->getEnVersionById($gradeid,$userconfig["termid"],$userconfig["versionid"],$subjectid);
            }
        }
        $ret["data"]=$result;
        $ret["bookinfo"]=$name;
        $ret["ways"]=$learnways;
        $this->ajaxReturn($ret);
    }



    //学一学单词展示
    public function getWord(){
        $request = I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $curindex= I("index");
        $pindex=$curindex;
        $ks_code=$request["ks_code"];
        $index=I("params/d",0);
        
        $word=D("Word","Service");
        $user=D("User","Service");
        $index=$curindex*C("perword")+$index;
        //查询当前的记录是哪一组
        $rs=$user->getUserCurLearnPosition($this->user,$pindex,0,$ks_code);
        $batchid=$rs["id"];
        $wordnum=$rs["wordnum"];
        $count=$wordnum;
        $data=$word->getWordInfoByUnitAndNum($this->user,$ks_code,$index,1);
        //查询是够勾选例句
        $textdata=$word->getTextByUnitAndWord($ks_code,$data[0]["word"],1);
        $data[0]["textexamples"]=$textdata;
        $ret["count"]=$count;
        $ret["data"]=$data[0];
        $ret["index"]=$index;
        $this->ajaxReturn($ret);
    }

    //获取问题
    public function getSingleSpellQuestion(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=I("params/d",0);
        $curindex=I("index/d",0);
        $pindex=$curindex;
        $curindex=$curindex*C("perword");
        $word=D("Word","Service");
        $user=D("User","Service");
        $rs=$user->getUserCurLearnPosition($this->user,$pindex,1,$ks_code);
        $wordnum=$rs["wordnum"];
        $count=$wordnum;
        if($count==0||empty($rs)){
            $ret["count"]=0;
            $ret["data"]=array();
            $ret["index"]=$index;
            $ret["issubmit"]=1;
            $this->ajaxReturn($ret);
        }else{
            $batchid=$rs["id"];
            $icount=0;
            $data=array();
            //获取单选试题
            $wordrs=$word->getWordInfoByUnitAndNum($this->user,$ks_code,$curindex,C("perword"));
            //判断是够有单词拼写试题
            foreach($wordrs as $key=>$value){
                $exists=($value["letters"]==""||empty($value["letters"]))?false:true;
                if($exists){
                    if($icount==$index){
                        $data=$word->getWordSingleSpellByWordid($value["id"]);
                        $icount=$icount+1;
                        break;
                    }else{
                        $icount=$icount+1;
                    }
                }
            }
            //查询用户在这个批次中这个试题是够已经做过
            $id=$data["id"];
            $param[0]=$id;
            $isexist=$user->isSubmitBySourceAndBatch($this->user,$batchid,$param);
            //$data=$word->getWordSingleSpellByUnitAndNum($ks_code,$index,1);
            //$count=$word->getWordCountByUnitWithinLetters($ks_code);
            $ret["count"]=$count;
            $ret["data"]=$data;
            $ret["index"]=$index;
            $ret["issubmit"]=$isexist?1:0;
            $this->ajaxReturn($ret);
        }
    }


    //获取问题
    public function getMultiSpellQuestion(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=I("params/d",0);
        $pindex=I("index/s",0);
        $word=D("Word","Service");
        $user=D("User","Service");
        $rs=$user->getUserCurLearnPosition($this->user,$pindex,3,$ks_code);

        $batchid=$rs["id"];
        $wordnum=$rs["wordnum"];
        $data=$word->getWordMultiSpellByUnitAndNum($ks_code,$index,1);
        $count=$word->getWordCountByUnit($ks_code);
        $id=$data[0]["id"];
        $param[0]=$id;
        $isexist=$user->isSubmitBySourceAndBatch($this->user,$batchid,$param);
        $ret["issubmit"]=$isexist?1:0;
        $ret["count"]=$count;
        $ret["data"]=$data[0];
        $ret["index"]=$index;
        $this->ajaxReturn($ret);
    }

    //获取生词本测试试题
    public function getBookTestMultiSpellQuestion(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=I("params/d",0);
        $word=D("Word","Service");
        $user=D("User","Service");
        $rs=$user->getUserCurLearnPosition($this->user,-1,-1,0);

        $batchid=$rs["id"];
        $count=$rs["wordnum"];
        $booklist=json_decode($rs["wordlist"],true);
        $bookid=$booklist[$index];
        $data=$word->getWordBookMultiSpellQuestionByBookid($bookid);
        $id=$data["id"];
        $param[0]=$id;
        $isexist=$user->isSubmitBySourceAndBatch($this->user,$batchid,$param);
        $ret["issubmit"]=$isexist?1:0;
        $ret["count"]=$count;
        $ret["data"]=$data;
        $ret["index"]=$index;
        $this->ajaxReturn($ret);
    }

    //获取多选试题
    public function getMulQuestion(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=I("params/d",0);
        $curindex=I("index/d",0);
        $pindex=$curindex;
        $curindex=$curindex*C("perword");
        $word=D("Word","Service");
        $user=D("User","Service");
        $rs=$user->getUserCurLearnPosition($this->user,$pindex,2,$ks_code);
        $batchid=$rs["id"];
        $wordnum=$rs["wordnum"];
        $count=$wordnum;
        $index=$curindex+$index*5;
        $param=array();
        $word=D("Word","Service");
        $questions=$word->getWordByUnitAndNum($ks_code,$index,5);
        $tncontent=array();
        foreach($questions as $key=>$value){
            $temp["content"]=$value["morphology"]." ".$value["explains"];
            $temp["id"]=$value["id"];
            $temp["word"]=$value["word"];
            $temp["answer"]="";
            $temp["iserror"]=-1;
            $temp["isdo"]=0;
            array_push($tncontent,$temp);
            $param[$key]=$value["id"];
        }
        shuffle($tncontent);
        $items=$tncontent;
        shuffle($items);
        $question["tncontents"]=$tncontent;
        $question["items"]=$items;
        $question["ks_code"]=$ks_code;
        $isexist=$user->isSubmitBySourceAndBatch($this->user,$batchid,$param);
        //$count=$word->getWordCountByUnit($ks_code);
        $num=ceil($count/5);
        $ret["data"]=$question;
        $ret["count"]=$num;
        $ret["index"]=$index;
        $ret["issubmit"]=$isexist?1:0;
        $this->ajaxReturn($ret);
    }

    //保存用户答案
    public function setUserSingleSpellAnswer(){
        $request = stripslashes(I("params"));
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=$request["index"];
        $batchid=I("batchid");
        $iserror=true;
        $user=D("user","Service");
        //$rs=$user->getUserCurLearnPosition($this->user,-1,1,$ks_code);
        $data["batchid"]=$batchid;
        $data["base_wordid"]=$request["base_wordid"];
        $data["base_explainsid"]=$request["base_explainsid"];
        $data["source"]=1;
        $data["sourceid"]=$request["id"];
        $useranswer="";
        foreach($request["singlespell"]["tncontent"] as $key=>$value){
            $request["singlespell"]["tncontent"][$key]["iserror"]=(($value["answer"]===$value["word"])?1:0);
            $iserror=$iserror&&(($value["answer"]===$value["word"])?true:false);
            $useranswer=$useranswer.$value["answer"];
        }
        $data["score"]=$iserror?1:0;
        //如果错误了直接添加到生词本
        if(!$iserror){
            $word["word"]=$request["word"];
            $word["explains"]=$request["explains"];
            $word["base_wordid"]=$request["base_wordid"];
            $word["base_explainsid"]=$request["base_explainsid"];
            $word["addtime"]=Date("Y-m-d H:i:s");
            $word["source"]=1;
            $word["sourceid"]=$request["id"];
            $user->collectUserWordBook($this->user,$word);
        }
        $data["answer"]=$useranswer;
        $data["questype"]=1;
        $user->setUserWordLearnRecord($this->user,$data);
        $ret["data"]=$request;
        $ret["iserror"]=$iserror;
        $this->ajaxReturn($ret);
    }

    //这是用户拼写问题
    public function setUserMultiSpellAnswer(){
        $request = stripslashes(I("params"));
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=$request["index"];
        $batchid=I("batchid");
        $iserror=true;
        $user=D("user","Service");
        $word=D("word","Service");
        $data["batchid"]=$batchid;
        $data["base_wordid"]=$request["base_wordid"];
        $data["base_explainsid"]=$request["base_explainsid"];
        $data["source"]=3;
        $data["sourceid"]=$request["id"];
        $useranswer="";
        foreach($request["multispell"]["tncontent"] as $key=>$value){
            $request["multispell"]["tncontent"][$key]["iserror"]=(($value["answer"]===$value["word"])?1:0);
            $iserror=$iserror&&(($value["answer"]===$value["word"])?true:false);
            $useranswer=$useranswer.$value["answer"];
        }
        $data["score"]=$iserror?1:0;
        if(!$iserror){
            $words["word"]=$request["word"];
            $words["explains"]=$request["explains"];
            $words["base_wordid"]=$request["base_wordid"];
            $words["base_explainsid"]=$request["base_explainsid"];
            $words["addtime"]=Date("Y-m-d H:i:s");
            $words["source"]=3;
            $words["sourceid"]=$request["id"];
            $user->collectUserWordBook($this->user,$words);
        }
        $data["answer"]=$useranswer;
        $data["questype"]=3;
        $user->setUserWordLearnRecord($this->user,$data);
        $ret["data"]=$request;
        $ret["iserror"]=$iserror;
        $hg=0;
        //查询最后一关是否做完
        $hgtop=$word->isWordHg($ks_code,$this->user);
        if(!empty($hgtop)){
            $hg=1;
        }
        $ret["hg"]=$hg;
        $this->ajaxReturn($ret);
    }

    //这是用户拼写问题
    public function setUserBookTestMultiSpellAnswer(){
        $request = stripslashes(I("params"));
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=$request["index"];
        $batchid=I("batchid");
        $iserror=true;
        $user=D("user","Service");
        //$rs=$user->getUserCurLearnPosition($this->user,-1,-1,$ks_code);
        $data["batchid"]=$batchid;
        $data["base_wordid"]=$request["base_wordid"];
        $data["base_explainsid"]=$request["base_explainsid"];
        $data["source"]=3;
        $data["sourceid"]=$request["id"];
        $useranswer="";
        foreach($request["multispell"]["tncontent"] as $key=>$value){
            $request["multispell"]["tncontent"][$key]["iserror"]=(($value["answer"]===$value["word"])?1:0);
            $iserror=$iserror&&(($value["answer"]===$value["word"])?true:false);
            $useranswer=$useranswer.$value["answer"];
        }
        $data["score"]=$iserror?1:0;
        //用户得分的话那么就将这个单词从生词本中删除
        if($iserror){
            //$user->delUserWordBookByWordInfo($this->user,$request);
        }
        
        if(!$iserror){
            $words["word"]=$request["word"];
            $words["explains"]=$request["explains"];
            $words["base_wordid"]=$request["base_wordid"];
            $words["base_explainsid"]=$request["base_explainsid"];
            $words["addtime"]=Date("Y-m-d H:i:s");
            $words["source"]=3;
            $words["sourceid"]=$request["id"];
            $user->collectUserWordBook($this->user,$words);
        }
        $data["answer"]=$useranswer;
        $data["questype"]=3;
        $user->setUserWordLearnRecord($this->user,$data);
        $ret["data"]=$request;
        $ret["iserror"]=$iserror;
        $this->ajaxReturn($ret);
    }

    //用户的汉译英的作答数据
    public function setUserMultiChooseAnswer(){
        $request = stripslashes(I("params"));
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=$request["index"];
        $iserror=true;
        $user=D("user","Service");
        $word=D("word","Service");
        $batchid=I("batchid");
        //$rs=$user->getUserCurLearnPosition($this->user,-1,2,$ks_code);
        $isub=$user->isUserGetUB($batchid,$this->user);
        $datas["batchid"]=$batchid;
        $datas["source"]=0;
        $datas["sourceid"]=$request["id"];
        $useriserror=true;
        foreach($request["tncontents"] as $key=>$value){
            $iserror=true;
            $wordrs=$word->getUnitWordInfoByWordid($value["id"]);
            $datas["base_wordid"]=$wordrs["base_wordid"];
            $datas["base_explainsid"]=$wordrs["base_explainsid"];
            $request["tncontents"][$key]["iserror"]=(($value["answer"]===$value["word"])?1:0);
            $iserror=$iserror&&(($value["answer"]===$value["word"])?true:false);
            $datas["score"]=$iserror?1:0;
            $useriserror=$useriserror&&$iserror;
            $datas["answer"]=$value["answer"];
            $datas["questype"]=2;
            $datas["source"]=2;
            $datas["sourceid"]=$value["id"];
            $user->setUserWordLearnRecord($this->user,$datas);
            if(!$iserror){
                $words["word"]=$request["word"];
                $words["explains"]=$request["explains"];
                $words["base_wordid"]=$request["base_wordid"];
                $words["base_explainsid"]=$request["base_explainsid"];
                $words["addtime"]=Date("Y-m-d H:i:s");
                $words["source"]=2;
                $words["sourceid"]=$request["id"];
                $user->collectUserWordBook($this->user,$words);
            }
        }
        //查询一下ub的情况
        
        //设置当前是否正确
        $ret["data"]=$request;
        $ret["iserror"]=$useriserror;
        $ret["isub"]=$isub;
        $this->ajaxReturn($ret);
    }

    //获取数据库单词
    public function getWordChapterByUnit(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $word=D("Word","Service");
        $user=D("User","Service");
        $data=$word->getWordChapterByUnitAutomaticAndUsername($ks_code,C("perword"),$this->user);
        $wordlist=$word->getDataAndWordBookByUnitGroup($this->user,$ks_code);
        //$userinfo=$user->getUserinfoByUser($this->user);
        $ret["top"]=$data["top"];
        $ret["hg"]=$data["hg"];
        $ret["userinfo"]=$this->user;
        $ret["data"]=$data["data"];
        $ret["count"]=$data["count"];
        $ret["wordlist"]=$wordlist["data"];
        $ret["curlevel"]=$data["curlevel"];
        $this->ajaxReturn($ret);
    }


    //获取数据库单词
    public function getUnitWordListByKscode(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $word=D("Word","Service");
        $data=$word->getDataAndWordBookByUnit($this->user,$ks_code);
        $ret["data"]=$data["data"];
        $ret["count"]=$data["count"];
        $this->ajaxReturn($ret);
    }


    //获取数据库单词
    public function getUnitWordList(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $request=I("request");
        $isgroup=I("isgroup/d",0);
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $word=D("Word","Service");
        if($isgroup == 0){
            $data=$word->getDataAndWordBookByUnit($this->user,$ks_code);
        }else if($isgroup == 1){
            $data=$word->getDataAndWordBookByWordGroup($this->user,$ks_code);
        }
        $ret["data"]=$data["data"];
        $ret["count"]=$data["count"];
        $this->ajaxReturn($ret);
    }



    //收藏生词本
    public function userCollectLearnWord(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $word["word"]=$request["word"];
        $word["explains"]=$request["explains"];
        $word["base_wordid"]=$request["base_wordid"];
        $word["base_explainsid"]=$request["base_explainsid"];
        $word["word"]=$request["word"];
        $word["addtime"]=Date("Y-m-d H:i:s");
        $word["source"]=empty($request["source"])?0:$request["source"];
        $word["sourceid"]=$request["id"];
        $user=D("User","Service");
        $iscollect=$request["iscollect"];
        if($iscollect=='1'){
            $user->collectUserWordBook($this->user,$word);
        }else{
            $user->delUserWordBookByWordInfo($this->user,$word);
        }
    }

    //获取生词本列表
    public function getUserWordBookList(){
        $user=D("User","Service");
        $sortid=I("sortid/d","");
        $ret=$user->getUserWordBook($this->user,$sortid);
        $this->ajaxReturn($ret);
    }

    //删除生词本信息
    public function delUserWordBookById(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $id=$request["id"];
        $user=D("User","Service");
        $ret=$user->delUserWordBook($id);
        $this->ajaxReturn($ret);
    }


    //保存浏览记录
    public function setUserLearnRecord(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $data["ks_code"]=$request["ks_code"];
        $data["type"]=$request["type"];
        $data["index"]=$request["index"];
        $data["icount"]=$request["icount"];
        $curindex=$request["index"];
        $user=D("user","Service");
        $word=D("word","Service");
        //当时拼写试题的时候计算这一组中有几个试题
        $curindex=$curindex*C("perword");
        $ret=$user->setUserLearnRecord($this->user,$data,$data["icount"]);
        $rets["batchid"]=$ret;
        $this->ajaxReturn($rets);
    }

    //本次学习结束
    public function setUserLearnOver(){
        $type=I("type/d",0);
        $ks_code=I("ks_code");
        $batchid=I("batchid");
        $ub=0;
        if($type==2){
            $ub=5;
        }else if($type==3){
            $ub=10;
        }
        $user=D("user","Service");
        $isub=$user->isUserGetUB($batchid,$this->user);
        if($isub==1){
            $ub=0;
        }
        $user->setUserLearnOver($batchid,$ub); 
    }


    //记录本次单词浏览记录
    public function setUserWordReadRecord(){
        $request=I("request");
        $request = rtrim($request, '"');
        $request = ltrim($request, '"');
        $request = str_replace('&quot;', '"', $request);
        $request = json_decode($request,true);
        $ks_code=$request["ks_code"];
        $index=$request["index"];
        $batchid=I("batchid");
        $user=D("user","Service");
        $data["batchid"]=$batchid;
        $data["base_wordid"]=$request["base_wordid"];
        $data["base_explainsid"]=$request["base_explainsid"];
        $data["source"]=0;
        $data["sourceid"]=$request["id"];
        $data["score"]=0;
        $data["answer"]="";
        $data["questype"]=0;
        $user->setUserWordLearnRecord($this->user,$data);
    }

    //记录接口响应时间
    public function setResponseInterfaceTime(){
        $starttime=I("starttime");
        $endtime=I("endtime");
        $interface=I("interface");
        $log=D("Log","Service");
        $data["starttime"]=$starttime;
        $data["endtime"]=$endtime;
        $data["interface"]=$interface;
        $log->getEngsInterfaceResponseTime($data,$this->user);
    }

    //设置用户生词本测试批次
    public function setUserWordBookBatch(){
        $data=I("request");
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data,true);
        $user=D("User","Service");
        $word=D("Word","Service");
        $ret=$user->setUserWordBookBatch($this->user,$data);
        //刷新localstrage
        $rs=$word->getWordBookTest($data,$this->user);
        $arr["wordlist"][0]=$rs["data"];
        $arr["batchid"]=$ret["id"];
        $this->ajaxReturn($arr);
    }

    //排行数据布局
    public function getRankData(){
        $first=array(array("id"=>0,"name"=>"班级排名"),array("id"=>1,"name"=>"学校排名"),array("id"=>0,"name"=>"全国排名"));
        $second=array(array("id"=>0,"name"=>"周榜"),array("id"=>1,"name"=>"月榜"),array("id"=>0,"name"=>"总榜"));
        $user=D("User","Service");
        $rs=$user->getUserClassInfoByUser($this->user);
        $droplist=array();
        foreach($rs as $key=>$value){
            $temp["id"]=$value["classid"];
            $temp["name"]=$value["schoolname"].$value["classname"];
            array_push($droplist,$temp);
        }
        $ret["first"]=$first;
        $ret["second"]=$second;
        $ret["droplist"]=$droplist;
        $this->ajaxReturn($ret);
    }

    //获取全国排行
    public function getTotalRank(){
        $pageCurrent = I('pageCurrent/d',0);
        $pageSize = I('pageSize/d',0);
        $type = I('type/s','class');//class:班级;school:学校;all:全国
        $time = I('time/s','a');//w:周;m:月；a：总榜
        $user=D("User","Service");
        $ret=$user->getTotalRank($this->user,$pageCurrent,$pageSize,$type,$time);
        $this->ajaxReturn($ret);
    }



    //读单词单元展示
    public function wordunit() {
        // $subjectid=I("subjectid");
        // $gradeid=I("gradeid");
        // cookie("subjectid",$subjectid);
        // cookie("gradeid",$gradeid);
        // $backUrl=cookie("backUrl");
        // $this->assign("backUrl",$backUrl);
        // $this->display("wordunit");
        // $gradeid=I("gradeid");
        $moduleid=I("moduleid");
        $backUrl=I("backUrl");
        $subjectid=I("subjectid");
        $gradeid=I("gradeid");
        cookie("backUrl",$backUrl);
        $learnways=getmodule($moduleid);
        $url=$learnways["url"];
        
        //获取用户的班级以及年级等
        
        
        $query="";
        $count=count($_GET);
        foreach($_GET as $key=>$value){
            if($key=='m'){continue;} 
            if($key==($count-1)){
                $query=$query.$key."=".$value;
            }else{
                if($key=='gradeid'){
                    //这里关心用户选择的版本
                    $user=D("User","Service");
                    $rs=$user->getUserConfig($this->user,$subjectid,$gradeid);
                    if(!empty($rs)){
                        $query=$query.$key."=".$rs["gradeid"]."&";
                    }else{
                        $query=$query.$key."=".$value."&";
                    }
                }else{
                   $query=$query.$key."=".$value."&"; 
                }
                
            }     
        }
        //判断字符串中是否有问号
        if(strpos($url,"?") === false){     //使用绝对等于
            //不包含
            redirect("../../".$url."?".$query);
        }else{
            //包含
            redirect("../../".$url."&".$query);
        }
    }

    //口语作业单元展示
    public function readunit() {
        // $subjectid=I("subjectid");
        // $gradeid=I("gradeid");
        // cookie("subjectid",$subjectid);
        // cookie("gradeid",$gradeid);
        // $backUrl=cookie("backUrl");
        // $this->assign("backUrl",$backUrl);
        // $this->display("readunit");
        $moduleid=I("moduleid");
        $backUrl=I("backUrl");
        $subjectid=I("subjectid");
        $gradeid=I("gradeid");
        cookie("backUrl",$backUrl);
        $learnways=getmodule($moduleid);
        $url=$learnways["url"];
        $query="";
        $count=count($_GET);
        foreach($_GET as $key=>$value){
            if($key=='m'){continue;} 
            if($key==($count-1)){
                $query=$query.$key."=".$value;
            }else{
                if($key=='gradeid'){
                    //这里关心用户选择的版本
                    $user=D("User","Service");
                    $rs=$user->getUserConfig($this->user,$subjectid,$gradeid);
                    if(!empty($rs)){
                        $query=$query.$key."=".$rs["gradeid"]."&";
                    }else{
                        $query=$query.$key."=".$value."&";
                    }
                }else{
                   $query=$query.$key."=".$value."&"; 
                }
            }     
        }
        //判断字符串中是否有问号
        if(strpos($url,"?") === false){     //使用绝对等于
            //不包含
            redirect("../../".$url."?".$query);
        }else{
            //包含
            redirect("../../".$url."&".$query);
        }
    }

    //背单词单元展示
    public function reciteunit() {
        // $subjectid=I("subjectid");
        // $gradeid=I("gradeid");
        // cookie("subjectid",$subjectid);
        // cookie("gradeid",$gradeid);
        // $backUrl=cookie("backUrl");
        // $this->assign("backUrl",$backUrl);
        // $this->display("reciteunit");
        $moduleid=I("moduleid");
        $backUrl=I("backUrl");
        $subjectid=I("subjectid");
        $gradeid=I("gradeid");
        cookie("backUrl",$backUrl);
        $learnways=getmodule($moduleid);
        $url=$learnways["url"];
        $query="";
        $count=count($_GET);
        foreach($_GET as $key=>$value){
            if($key=='m'){continue;} 
            if($key==($count-1)){
                $query=$query.$key."=".$value;
            }else{
                if($key=='gradeid'){
                    //这里关心用户选择的版本
                    $user=D("User","Service");
                    $rs=$user->getUserConfig($this->user,$subjectid,$gradeid);
                    if(!empty($rs)){
                        $query=$query.$key."=".$rs["gradeid"]."&";
                    }else{
                        $query=$query.$key."=".$value."&";
                    }
                }else{
                   $query=$query.$key."=".$value."&"; 
                }
            }     
        }
        //判断字符串中是否有问号
        if(strpos($url,"?") === false){     //使用绝对等于
            //不包含
            redirect("../../".$url."?".$query);
        }else{
            //包含
            redirect("../../".$url."&".$query);
        }
    }
    
   

    //获取背单词的单元的信息
    public function getReciteUnitData(){
        $unit=D("rms_unit");
        $moduleid=I("moduleid");
        $username=cookie("username");
        $gradeid=cookie("gradeid");
        $subjectid=cookie("subjectid");
        getUserConfig($subjectid,$gradeid,$username);
        $versionid=cookie("versionid");
        $volumnid=cookie("termid");
        $result=$unit->getUnitListData($gradeid,$volumnid,$versionid,$subjectid,$username,$moduleid,"0");
        $name=$unit->getVersionById($gradeid,$volumnid,$versionid,$subjectid);
        $result["bookinfo"]=$name;
        $learnways=getmodule($moduleid);
        //$learnways=M("menu_modules")->where("id=%d",$moduleid)->field("remark,title")->find();
        $result["ways"]=$learnways;
        $this->ajaxReturn($result);
    }
    //通过单元获取单词信息
    public function getWordsDataByUnit(){
        $word=D("word");
        $ks_code=I("ks_code");
        $username=cookie("username");
        $result=$word->getWordListData($ks_code,$username);
        $this->ajaxReturn($result);
    }
    //通过生词本
    public function setWordBook(){
        $userid=cookie("username");
        $areaid=cookie("areacode");
        $wordid=I("wordid");
        $ks_code=I("ks_code");
        $source=I("source");
        $action=I("action");
        $arr["userid"]=$userid;
        $arr["areacode"]=$areaid;
        $arr["wordid"]=$wordid;
        $arr["ks_code"]=$ks_code;
        $arr["source"]=$source;
        $wordbook=D("word_book");
        if("add"==$action){
            $wordbook->addWordBook($arr);
        }else if("del"==$action){
            $wordbook->delWordBook($arr);
        }
    }

    //展示reciteword页面
    public function reciteword(){
        //查询用户背单词的
        $this->display("reciteword");
    }





    //获取背单词的问题的信息
    public function getReciteQuestionsData(){
        set_time_limit(0);
        $word=D("word");
        $ks_code=I("ks_code");
        $username=cookie("username");
        $areaid=cookie("areacode");
        $classid=cookie("classid");
        $truename=cookie("truename");
        $schoolid=cookie("schoolid");
        //$areaid="1.";
        $userrecite=M("user_recite_word");
        $maxid=$userrecite->field("max(id) as id")->find();
        $userlogid=1;
        $userrs=$userrecite->where("username='%s' and areaid='%s' and ks_code='%s' and submittime is null and isover=0",$username,$areaid,$ks_code)->order("id desc")->find();
        $userlogid=0;
        if(empty($userrs)){
            $userrecite->id=empty($maxid)?1:($maxid["id"]+1);
            $userrecite->areaid=$areaid;
            $userrecite->username=$username;
            $userrecite->classid=$classid;
            $userrecite->truename=$truename;
            $userrecite->schoolid=$schoolid;
            $userrecite->ks_code=$ks_code;
            $userrecite->usertype=cookie("usertype");
            $userlogid=$userrecite->add();
        }else{
            //比较看用户的这套试题是否过期
            $curtime=date("Y-m-d H:i:s");
            $dotime=$userrs["starttime"];
            $submittime=strtotime($dotime)+C("reciteword_overdue")*60;
            //$submittime=date('Y-m-d H:i:s',$submittime);
            if(strtotime($curtime)>($submittime)){
                $userrecite->id=empty($maxid)?1:($maxid["id"]+1);
                $userrecite->areaid=$areaid;
                $userrecite->username=$username;
                $userrecite->ks_code=$ks_code;
                $userrecite->classid=$classid;
                $userrecite->truename=$truename;
                $userrecite->schoolid=$schoolid;
                $userrecite->usertype=cookie("usertype");
                $userlogid=$userrecite->add();
            }else{
                $userlogid=$userrs["id"];
            }
        }
        //查询单元的单词
        $result=$word->getWordListData($ks_code,$username);
        $questions=array();
        $downlist=array();
        //查询用户背单词的记录
        $arr["downlist"]=$result;
        $arr["id"]=$userlogid;
        $this->ajaxReturn($arr);
    }

    //获取单个的背单词的试题
    public function getReciteQuestion(){
        $word=D("word");
        $index=I("index");
        $username=cookie("username");
        $areaid=cookie("areacode");
        $ks_code=I("ks_code");
        $userrecite=M("user_recite_word");
        $userrs=$userrecite->where("username='%s' and areaid='%s' and ks_code='%s' and submittime is null and isover=0",$username,$areaid,$ks_code)->order("id desc")->find();
        $userlogid=$userrs["id"];
        $result=$word->getWordListData($ks_code,$username);
        $wordid=$result[$index]["id"];
        $isword=$result[$index]["isword"];
        //将数据直接插入数据库中
        $questions=array();
        if(!empty($userrs["questions"])){
            $questions=json_decode($userrs["questions"],true);
            if(empty($questions[$wordid])){
                $graders=M("rms_unit")->where("ks_code='%s'",$ks_code)->field("r_grade")->find();
                $gradeid=$graders["r_grade"];
                $question=$word->getReciteWordQuestion($gradeid,$wordid,$isword);
                $questions[$question["wordid"]]=$question;
                $word->setUserReciteWord($userlogid,$questions);
            }else{
                $graders=M("rms_unit")->where("ks_code='%s'",$ks_code)->field("r_grade")->find();
                $gradeid=$graders["r_grade"];
                $question=$word->getReciteWordQuestion($gradeid,$wordid,$isword);
                $questions[$question["wordid"]]=$question;
                $question=$questions[$question["wordid"]];
            }
        }else{
            $graders=M("rms_unit")->where("ks_code='%s'",$ks_code)->field("r_grade")->find();
            $gradeid=$graders["r_grade"];
            $question=$word->getReciteWordQuestion($gradeid,$wordid,$isword);
            $questions[$question["wordid"]]=$question;
            $word->setUserReciteWord($userlogid,$questions);
        }
        $arr["question"]=$question;
        $arr["id"]=$userlogid;
        $this->ajaxReturn($arr);
    }

    //跟新数据库将这个记录更新成用户提交
    public function setUserReciteResult(){
        $logid=I("logid");
        //更新数据库
        $user_recite_word=M("user_recite_word");
        $user_recite_word->where("id=%d",$logid)->setField("submittime",Date("Y-m-d H:i:s"));
    }

    //获取口语的列表
    public function getReadList(){
        $ks_code=I("ks_code");
        $username=cookie("username");
        $areaid=cookie("areacode");
        //获取并判断是否有单词
        $text=D("text");
        $result=array();
        $wordresult=M("word")->where("ks_code='%s' and isdel=1 and base_wordid in (select id from engs_base_word where isword=1)",$ks_code)->count();
        if($wordresult>0){
            $arr["id"]=0;
            $arr["chapter"]="单词口语";
            $arr["ks_code"]=$ks_code;
            //查询最高分数据
            $userinfos=M("user_read")->where("ks_code='%s' and username='%s' and areaid='%s' and chapterid=0 and isover=0 and truename is not null",$ks_code,$username,$areaid)->field("MAX(score) AS maxscore,addtime")->order("id desc")->find();
            $arr["maxscore"]=$userinfos["maxscore"];
            $arr["addtime"]=$userinfos["addtime"];
            //$arr["count"]=$wordresult;
            array_push($result, $arr);
        }
        $chapterresult=$text->getReadChapterList($ks_code,$username,$areaid);
        foreach($chapterresult as $key=>$value){

             array_push($result, $value);
        }
        $this->ajaxReturn($result);
    }


    //展示readword页面
    public function readword(){
        $ks_code=I("ks_code/s");
        $chapterid=I("chapterid/d");
        $username=cookie("username");
        $areaid=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        unset($userread);
        $userread=M("user_read");
        $userrs=$userread->where("username='%s' and areaid='%s' and ks_code='%s' and chapterid=%d and submittime is null and isover=0",$username,$areaid,$ks_code,$chapterid)->order("id desc")->find();
        $userreadid=0;
        //跟读的是那个批次的
        if(empty($userrs)){
            unset($userread);
            $userread=M("user_read");
            $sql="insert into engs_user_read(`areaid`,`username`,`schoolid`,`classid`,`ks_code`,`chapterid`,`addtime`) value ('%s','%s','%s','%s','%s','%s','%s')";
            $userreadid=$userread->execute($sql,$areaid,$username,$schoolid,$classid,$ks_code,$chapterid,Date("Y-m-d H:i:s"));
        }else{
            unset($userread);
            //比较看用户的这套试题是否过期
            $curtime=date("Y-m-d H:i:s");
            $dotime=$userrs["addtime"];
            $submittime=strtotime($dotime)+C("readword_overdue")*60;
            //$submittime=date('Y-m-d H:i:s',$submittime);
            if(strtotime($curtime)>($submittime)){
                $userread=M("user_read");
                $sql="insert into engs_user_read(`areaid`,`username`,`schoolid`,`classid`,`ks_code`,`chapterid`,`addtime`) value ('%s','%s','%s','%s','%s','%s','%s')";
                $userreadid=$userread->execute($sql,$areaid,$username,$schoolid,$classid,$ks_code,$chapterid,Date("Y-m-d H:i:s"));
            }else{
                $userreadid=$userrs["id"];
            }
        }
        $this->display("readword");
    }

    //展示readchapter页面
    public function readchapter(){
        $this->display("readchapter");
    }

    //展示听写单词的页面
    public function learnword(){
        $ks_name = I("name/s","0");
        if($ks_name == "0"){
            $ks_name = I("ks_name/s","0");
        }
        $ks_name = urldecode($ks_name);
        $this->assign("ks_name",$ks_name);
        $this->display("word");
    }


    //展示readchapter页面
    public function readresult(){
        //显示口语结果页面
        $userread=M("user_read");
        $ks_code=I("ks_code");
        $chapterid=I("chapterid/d");
        $rs=$userread->where("username='%s' and ks_code='%s' and chapterid=%d and isover=0",cookie("username"),$ks_code,$chapterid)->order("id desc")->find();
        if(empty($rs["submittime"])){
            //跟新用户的分数
            $userreadinfo=M("user_read_info");
            $userrs=$userreadinfo->where("readid=%d",$rs["id"])->field("sum(`maxscore`) as score")->find();
            //查询这个章节下面有多少单词
            if($chapterid==0){
                $sql="select * from engs_word left join engs_base_word on engs_word.base_wordid=engs_base_word.id where engs_base_word.isword=1 and engs_word.isdel=1 and engs_word.ks_code='%s'";
                $rss=M()->query($sql,$ks_code);
                $count=count($rss);
            }else{
                $count=M("text")->where("chapterid='%s' and isdel=1",$chapterid)->count();
            }
            if($count!=0){
                $score=empty($userrs["score"])?0:round($userrs["score"]/$count,1);
            }else{
                $score=0;
            }
            $userread->score=$score;
            $userread->submittime=Date("Y-m-d H:i:s");
            $userread->truename=cookie("truename");
            $userread->where("id=%d",$rs["id"])->save();
        }
        $this->display("readresult");
    }
}
