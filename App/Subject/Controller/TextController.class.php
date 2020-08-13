<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class TextController extends CheckController {

    public function textunit() {
        // $subjectid=I("subjectid");
        // $gradeid=I("gradeid");
        // cookie("subjectid",$subjectid);
        // cookie("gradeid",$gradeid);
        // $backUrl=cookie("backUrl");
        // $this->assign("backUrl",$backUrl);
        // $this->display("textunit");

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
    //获取课文单元信息
    public function getTextUnitData(){
    	$unit=D("rms_unit");
        $moduleid=I("moduleid");
        $username=cookie("username");
    	$gradeid=cookie("gradeid");
        $subjectid=cookie("subjectid");
        $volumnid=cookie("termid");
        $versionid=cookie("versionid");
        getUserConfig($subjectid,$gradeid,$termid,$versionid,$this->user);
    	$result=$unit->getUnitListData($gradeid,$volumnid,$versionid,$subjectid,$username,$moduleid,"1");
        $name=$unit->getVersionById($gradeid,$volumnid,$versionid,$subjectid);
        $result["bookinfo"]=$name;
        $learnways=getmodule($moduleid);
        $result["ways"]=$learnways;
    	$this->ajaxReturn($result);
    }
    //通过单元获取课文信息内容
    public function getTextsDataByUnit(){
        $text=D("text");
        $ks_code=I("ks_code");
        $result=$text->getTextListData($ks_code);
        //获取课文下面所有的音频
        $mlist=$text->getUnitAudioList($ks_code);
        $ret["data"]=$result;
        $ret["mp3list"]=$mlist;
        $this->ajaxReturn($ret);
    }

    //通过单元章节获取课文信息内容
    public function getTextsDataByChapter(){
        $chapterid=I("chapterid");
        $ks_code=I("ks_code");
        //进行数据的阐释
        $username=cookie("username");
        $areaid=cookie("areacode");
        $userread=M("user_read");
        $downlist=array();
        $userrs=$userread->where("username='%s' and areaid='%s' and ks_code='%s' and submittime is null",$username,$areaid,$ks_code)->order("id desc")->find();
        if($chapterid=='0'){
            $word=D("word");
            $result=$word->getReadWordList($ks_code,$username,$userrs["id"],$chapterid);
            foreach($result as $key=>$value){
                $temp["size"]="1";
                $temp["format"]="mp3";
                $temp["name"]=$value["mp3"];
                $temp["url"]=PROTOCOL."://".C("word_mp3_path").$value["mp3"];
                $temp["url"] = getHttpUrl($temp["url"]);
                array_push($downlist,$temp);
            }
        }else{
           $text=D("text");
           $result=$text->getTextList($chapterid);
           foreach($result as $key=>$value){
                $result[$key]["mp3"]=$value["mp3"].".mp3";
                $temp["size"]="1";
                $temp["format"]="mp3";
                $temp["name"]=$value["mp3"].".mp3";
                $path=substr($value["mp3"],0,2)."/".$value["mp3"].".mp3";
                $temp["url"]=PROTOCOL."://".C("text_mp3_path").$path;
                $temp["url"] = getHttpUrl($temp["url"]);
                array_push($downlist,$temp);
            }
        }
        $ret["data"]=$result;
        $ret["downlist"]=$downlist;
        $this->ajaxReturn($ret);
    }

    public function gettextsmartScore(){
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
      //var_dump($result);
      if(empty($result)){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
      }else{
        try{
          $score = $result['data']['score'];
          //出问题的时间直接写到文件中
        }catch(Exception $e){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
        }
      }
      $userread=M("user_text_read_score");
      $userread->username=$username;
      $userread->areaid=$areacode;
      $userread->schoolid=$schoolid;
      $userread->classid=$classid;
      $userread->truename=$truename;
      $userread->filename=$filename;
      $userread->chapterid=$chapterid;
      $userread->contentid=$contentid;
      $userread->content=$content;
      $userread->ks_code=$ks_code;
      $userread->type=$type;
      $userread->score=$score;
      $userread->usertype=cookie("usertype");
      $userread ->add();
      $arr["score"]=ceil($score);
      $this->ajaxReturn($arr);
    }
}
