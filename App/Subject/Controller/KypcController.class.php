<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class KypcController extends KypcUserController {
    //通过单元获取课文信息内容
    public function getTextsDataByUnit(){
        $text=D("text");
        $ks_code=I("ks_code");
        $result=$text->getTextListData($ks_code);
        //获取课文下面所有的音频
        $mlist=$text->getUnitAudioList($ks_code);
        //var_dump($result);
        $index = 0;
        foreach ($result as $key => $val) {
          if($val["isevaluate"] != 0){
              $valuatearr[$index] = $val;
              $index++;
          }
        }
        $ret["data"]=$result;
        $ret["mp3list"]=$mlist;
        session("textinfo",$valuatearr);
        $this->ajaxReturn($ret);
    }
    public function record_detail(){
      $username=cookie("username");
        if($username == ''){
          $username = 'test';
        }
      $localareacode=cookie("areacode");
        if($localareacode == ''){
          $localareacode = '1.';
        }
      $this->assign('username',$username);
      $this->assign('localareacode',$localareacode);
      $this->display('record_detail');
    }
    //人机对话详情
    public function get_record_detail(){
      $ks_code=I("ks_code");
      $chapterid = I("chapterid");
      $username=cookie("username");
      if($username == ''){
        $username = '41010110307079';
      }
      $localareacode=cookie("areacode");
      if($localareacode == ''){
        $localareacode = '1.';
      }

      $textsql="select a.id, b.cncontent,a.score,a.content as encontent,a.evaluate_result,concat('".WEB_DOMAIN."/recordwav/',a.filename) as recordurl,replace(a.filename,left(filename,11),'') as name from engs_user_text_read_share a left join engs_text b on a.contentid = b.id where a.chapterid=".$chapterid." and a.username='".$username."' order by a.contentid";
      //echo  $textsql;exit;
      $textresult = M()->query($textsql);
      foreach ($textresult as $key => $value) {
        $encontent = $value["encontent"];
        $evaluate_result = json_decode($value["evaluate_result"],true); 
        $char_details = $evaluate_result["result"]["details"];//句子中各单词详细信息
        $integrity = $evaluate_result["result"]["integrity"]; //完整度得分
        $accuracy = $evaluate_result["result"]["accuracy"]; //准确度得分
        $fluency = $evaluate_result["result"]["fluency"]["overall"]; //流利度得分
        if($evaluate_result["result"]["fluency"]["speed"] == "0"){
          $fluency_speed = "慢";
        }
        else if($evaluate_result["result"]["fluency"]["speed"] == "1"){
          $fluency_speed = "正常";
        }
        else{
          $fluency_speed = "快";
        }
        $fluency_pause = $evaluate_result["result"]["fluency"]["pause"];
        $textresult[$key]["integrity"] = $integrity;
        $textresult[$key]["accuracy"] = $accuracy;
        $textresult[$key]["fluency"] = $fluency;
        $textresult[$key]["fluency_speed"] = $fluency_speed;
        $textresult[$key]["fluency_pause"] = $fluency_pause;
       // $textresult[$key]["fluency_speed"] = $fluency_speed;
        $missword = "无";
        $repatword = "无";
        foreach ($char_details as $ckey => $cvalue) {
          if($cvalue["score"] <= "60"){
            //echo $encontent.$cvalue["char"];exit;
            $encontent = str_replace($cvalue["char"], "<span class=\"biaohong\">".$cvalue["char"]."</span>", $encontent);
          }
          else if($cvalue["score"] >= "80"){
            $encontent = str_replace($cvalue["char"], "<span class=\"biaolv\">".$cvalue["char"]."</span>", $encontent);
          }
          if(!empty($cvalue["db_type"])){
              if($cvalue["db_type"] == "1"){
                $missword.= $cvalue["char"]."，";
              }
              else if($cvalue["db_type"] == "2"){
                $repatword.= $cvalue["char"]."，";
              }
          }

        }
        $textresult[$key]["encontent"] = $encontent;
        $textresult[$key]["missword"] = $missword;
        $textresult[$key]["repatword"] = $repatword;
        $totalscore = ceil($totalscore)+ceil($value["score"]);
      }
      $average_scorce = ceil($totalscore/count($textresult));
      if($average_scorce <= 60){
        $star = array("1");
      }
      else if($average_scorce > 60 && $average_scorce <=70){
        $star = array("1","1");
      }
      else if($average_scorce > 70 && $average_scorce <=80){
        $star = array("1","1","1");
      }
      else if($average_scorce > 80 && $average_scorce <=90){
        $star = array("1","1","1","1");
      }
      else{
        $star = array("1","1","1","1","1");
      }
      $recordmp3sql = "select replace(filename,left(filename,11),'') as name, '1' as size,'mp3' as format,concat('http://".WEB_DOMAIN."/recordwav/',filename) as url from engs_user_text_read_share where chapterid=".$chapterid." and username='".$username."'  order by contentid";
      $recordresult = M()->query($recordmp3sql);

      $result['data'] = $textresult;
      $result['mp3list'] = $recordresult;
      $result['average_res']["average_scorce"] = $average_scorce;
      $result['average_res']["star_res"] = $star;
      $this->ajaxReturn($result);
    // var_dump($textresult);

    }
    //上传评分 
    public function gettextsmartScore(){
        foreach($_REQUEST as $k=>$v){
          file_put_contents('hahaha.txt','key:'.$k.'|value:'.$v.PHP_EOL,FILE_APPEND);
      }
      $username=cookie("username");
      if($username == ''){
        $username = 'test';
      }
      $localareacode=cookie("localareacode");
      if($localareacode == ''){
        $localareacode = '1.';
      }
      $classid=cookie("classid");
      $schoolid=cookie("schoolid");
      $truename=cookie("truename");
      $usertype = cookie('usertype');
      $filename=I("filename");
      $content=I("content");
      $chapterid=I("chapterid/d",0);
      $contentid=I("contentid/d",0);
      $ks_code=I("ks_code/s",0);
      $chapter=I("chapter/s",'0');
      $type=I("type/d",0);
      $recordpath = C('recordpath');
      $filename = I('filename/s','');
      $apptype = I('type/d',1);
      $EvaluateType = I('EvaluateType/d',0);
      $score = I('score/d',0);
      if($EvaluateType == 0){
        $result=smartstudy($filename,$content,1);
      }
      else{
        $result['data']['score'] = $score;
      }
      //var_dump($result);exit;
      if(empty($result)){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
      }else{
        try{
          $score = $result['data']['score'];
          $evaluate_result = $result['result']['result'];
          //出问题的时间直接写到文件中
        }catch(Exception $e){
          file_put_contents('./log/zhikeinterface.log',strtotime("now").":".$username.":".$filename.":".$content.PHP_EOL,FILE_APPEND);
          $score=mt_rand(10,90);
        }
      }
      $evaluate_result = json_encode($evaluate_result);
      $userread=M("user_text_read_share");
      $data['username'] = $username;
      $data['localareacode'] = $localareacode;
      $data['schoolid'] = $schoolid;
      $data['classid'] = $classid;
      $data['truename'] = $truename;
      $data['filename'] = $filename;
      $data['chapterid'] = $chapterid;
      $data['contentid'] = $contentid;
      $data['content'] = $content;
      $data['ks_code'] = $ks_code;
      $data['score'] = $score;
      $data['apptype'] = $type;
      $data['title'] = $chapter;
      $data['usertype'] = $usertype;
      $data['evaluate_result'] = $evaluate_result;
      $result = $userread ->field('id')->where("username='".$username."'  and localareacode='".$localareacode."' and ks_code='".$ks_code."' and chapterid=".$chapterid." and contentid=".$contentid)->find();
      //echo 'username='.$username;
      if(count($result) > 0){
        $userread ->where('id='.$result['id'])->save($data);
      }
      else{
        $userread ->add($data);
      }
      
      $arr["score"]=ceil($score);
      $arr["evaluate_result"] = $evaluate_result;

        $rank['username'] = $username;
        $rank['localareacode'] = $localareacode;
        $rank['usertype'] = $usertype;

        $rank['ks_code'] = $ks_code;
        $rank['filename'] = $content;
        $rank['filepath'] = $filename;
        $rank['type'] = 2;
        $rank['addtime'] = date('Y-m-d H:i:s');
        $rank['apptype'] = $apptype;//语文课文
        //file_put_contents('./log/hxp.log',strtotime("now").":username:".$username."::localareacode:".$localareacode."::filename:".$chapter."::filepath:".$filename."::usertype:".$usertype."::addtime::".date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
        // M('user_record')->add($data);
        $id = M('user_record')->add($rank);
        
      $this->ajaxReturn($arr);
    }
    public function gettextinfo(){
      $result = session("textinfo");
      //var_dump($result);exit;
      $this->ajaxReturn($result);
    }

    //通过单元获取课文信息内容
    public function getUserTextsDataByUnit(){
      //subjectid":"0003","gradeid":"0001","termid":"0001","versionid":"0011","moduleid":"10"}
      $username=cookie("username");
     // echo $username;
      if($username == ''){
        $username = 'test';
      }
      $localareacode=cookie("areacode");
      if($localareacode == ''){
        $localareacode = '1.';
      }
      $request=I("request");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);
      //var_dump($request);
      $subjectid=$request['subjectid'];
      $gradeid=$request['gradeid'];
      $termid=$request['termid'];
      $versionid=$request['versionid'];
      //if(empty($versionid)){
        $configrs = getUserConfig($subjectid,$gradeid,"0","0",$this->user); 
        $versionid = $configrs["versionid"];
        $termid = $configrs["termid"];
      //}
     
    //  echo "select ks_code from engs_rms_unit where r_grade='".$gradeid."' and r_subject = '".$subjectid."' and r_volume = '".$termid."' and r_version='".$versionid."'";
      $ks_code_data = M()->query("select ks_code from engs_rms_unit where r_grade='".$gradeid."' and r_subject = '".$subjectid."' and r_volume = '".$termid."' and r_version='".$versionid."'");
      $ret = array();
      //echo M()->getLastSql();
      foreach ($ks_code_data as $key => $val) {
          $chapterdata = M()->query("select id from engs_text_chapter where ks_code='".$val['ks_code']."'");
        //  var_dump($chapterdata);
          foreach ($chapterdata as $b => $v) {
            $usercontdata = M()->query("SELECT id AS usercount FROM engs_user_text_read_share where chapterid=".$v['id']." GROUP BY username");
            if(count($usercontdata) == 0){
              continue;
            }
            $result['ks_code'] = $val['ks_code'];
            $result['chapterid'] = $v['id'];
            $result['usercount'] = count($usercontdata);
            $avgdata = M()->query("SELECT AVG(score) AS avger FROM engs_user_text_read_share where username='".$username."' and localareacode='".$localareacode."' and chapterid=".$v['id']);
            $result['isdo'] = round($avgdata[0]['avger'],1);
            // $result['isdo'] = 20;
            $ret[] = $result;
          }
      }

      //var_dump($result);

     $this->ajaxReturn($ret);
  }
  

  function amr_trasfer_mp3(){
    $username=cookie("username");
      if($username == ''){
        $username = 'test';
      }
      $localareacode=cookie("areacode");
      if($localareacode == ''){
        $localareacode = '1.';
      }
      $type = I('type');
      $chapterid = I('chapterid/d',0);
      $userread=M("user_text_read_share");
      if($chapterid != 0){
        $result = $userread->field('id,filename')->where('username="'.$username.'" and apptype='.$type.' and chapterid in ('.$chapterid.') " and mp3path is not null')->select();
      }
      else{
        $result = $userread->field('id,filename')->where('username="'.$username.'" and apptype='.$type.' and mp3path is not null')->select();
      }
      foreach ($result as $key => $value) {
        $mp3path = amrTrasfermp3($value['filename']);
        $data['mp3path'] = $mp3path;
        $userread ->where('id='.$value['id'])->save($data);
      }
  }

  function getWebShareList(){
      $ks_code=I("ks_code");
      $chapterid = I("chapterid/d",0);
      $username=I("username");
      if($username == ''){
        $username = 'test';
      }
      $localareacode=I("areacode");
      if($localareacode == ''){
        $localareacode = '1.';
      }
      $apptype = I("apptype");
      $textsql="select b.encontent,b.cncontent,a.score,concat('".WEB_DOMAIN."/recordwav/',a.filename) as recordurl,replace(a.filename,left(filename,11),'') as name from engs_user_text_read_share a left join engs_text b on a.contentid = b.id where a.chapterid=".$chapterid." and a.username='".$username."' and a.apptype='".$apptype."'   order by a.contentid";
      $textresult = M()->query($textsql);
//echo $textsql;
      $recordmp3sql = "select replace(mp3path,left(mp3path,11),'') as name, '1' as size,'mp3' as format,concat('http://".WEB_DOMAIN."/recordwav/',mp3path) as url from engs_user_text_read_share where chapterid=".$chapterid." and username='".$username."' and apptype='".$apptype."' order by contentid";
      $recordresult = M()->query($recordmp3sql);

      $result['data'] = $textresult;
      $result['mp3list'] = $recordresult;
      $this->ajaxReturn($result);
  }
}
