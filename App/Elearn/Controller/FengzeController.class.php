<?php

namespace Elearn\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class FengzeController extends Controller {

    public function index(){
    	$code = I("code/s","0");
    	$fzid = cookie("fzid");
    	$fztruename = cookie("fztruename");
    	$fznickname = cookie("fznickname");
    	$fzphone = cookie("fzphone");
        $client_id = 'f6dbb06d91764f1880c1e2d523b11ac2';
        $redirect_uri="https://en.czbanbantong.com/Elearn/fengze/index";
        $client_secret = '8b49fcacf86948efafa1073e68fbc72e';
        $tkurl='https://auths.smartfengze.com/oauth/token?code='.$code.'&client_secret='.$client_secret.'&grant_type=authorization_code&client_id='.$client_id.'&redirect_uri='.$redirect_uri;
        if(empty($fzid)){
            $res=$this->post_curls($tkurl);
            writeAppLog("fengzeouath".Date("Y-m-d").".log",$tkurl."||".$res);
            $res=json_decode($res, TRUE);
            $token=$res["access_token"];
            $this->get_userinfo($token);
        }
        $this->assign("code",$code);
        $this->assign("isgetuser",$isgetuser);
        $this->display("index");  
    }
    public function post_curls($url)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($ch); 
		curl_close($ch);
        return $result;
    }
    public function get_userinfo($token){
    	$info_url='https://authr.smartfengze.com/resource/user/userinfo?access_token='.$token;
        $res=$this->post_curls($info_url);
         writeAppLog("fengzeouath".Date("Y-m-d").".log","userinfo||=".$res);
		$res=json_decode($res, TRUE);
		if(!empty($res["status"])){
			$userinfo = $res["content"];
			cookie('fzid',$userinfo["id"]);  //用户标识
			cookie('fztruename',$userinfo["userAuth"]["realName"]); //真实姓名
			cookie('fznickname',$userinfo["nickname"]); //昵称
			cookie('fzphone',$userinfo["phone"]);  //电话
			cookie('fzgender',$userinfo["gender"]); //性别(1:男 2:女 3:未知)
			cookie('fzphotoUrl',$userinfo["photoUrl"]); //头像
			cookie('fzuserToken',$userinfo["userToken"]); //用户token
         	$data["flag"] = 1;
		}
		else{
			$data["flag"] = 0;
		}
		
	  // $this->ajaxReturn($data);
    	//$this->display("access_token");
    }

    public function getItem(){
    	$version = M();
        $sql = "select r_grade_code,r_grade_name from mc_version where app_id = 101  group by r_grade_code order by r_grade_code";
        $result = $version->query($sql);   
        foreach ($result as $key => $value) {
        	$sql = "select r_subject_code,r_subject_name from mc_version where app_id = 101 and r_grade_code='".$value["r_grade_code"]."' group by r_subject_code";
        	$subject_result = $version->query($sql);
        	foreach ($subject_result as $skey => $svalue) {
        		$sql = "select id as version_id, r_term_code,r_term_name from mc_version where app_id = 101 and r_grade_code='".$value["r_grade_code"]."' and r_subject_code='".$svalue["r_subject_code"]."' group by r_term_code order by r_term_code";
        		$term_result = $version->query($sql);
        		$subject_result[$skey]["term"] = $term_result;
        	}
        	$result[$key]["subject"] = $subject_result;
        }
         //var_dump($result);exit;
         $this->ajaxReturn($result);
    }

    public function getCourseList(){
    	$version_id = I("version_id/d","0");
    	$listOrderType = I("listOrderType/s","desc");
    	$course = M();
    	if($version_id == "0"){
            $version_id = cookie("fzversion_id");
            if(empty($version_id)){
                $sql = "select a.id,a.title,a.pic,a.video_code,(select count(*) from mc_fz_course_click b where a.id=b.course_id) as click from mc_course as a where a.app_id=101 order by click ".$listOrderType;
            }
    		else{
                $sql = "select a.id,a.title,a.pic,a.video_code,(select count(*) from mc_fz_course_click b where a.id=b.course_id) as click from mc_course as a where a.version_id=".$version_id." order by click ".$listOrderType;
            }
    		
    	}
    	else{
            cookie("fzversion_id",$version_id);
    		$sql = "select a.id,a.title,a.pic,a.video_code,(select count(*) from mc_fz_course_click b where a.id=b.course_id) as click from mc_course as a where a.version_id=".$version_id." order by click ".$listOrderType;
    	}
    	$result = M()->query($sql);
    	foreach ($result as $key => $value) {
    		$titlearr = explode("||", $value["title"]);
    		$result[$key]["title"] = $titlearr[0];
    		$picarr = explode("noimg", $value["pic"]);
    		if(count($picarr) > 1){
    			$result[$key]["pic"] = "/public/Elearn/fengze/images/video.jpg";
    		}
    		else{
    			$result[$key]["pic"] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$value["pic"];
    		}
    		
    	}
    	$this->ajaxReturn($result);
    }
    public function getCourseListSearch(){
        $keyword = I("keyword/s","0");
        $keyword = urldecode($keyword);
        $listOrderType = I("listOrderType/s","desc");
        $course = M();
        $sql = "select a.id,a.title,a.pic,a.video_code,(select count(*) from mc_fz_course_click b where a.id=b.course_id) as click from mc_course as a where a.title like '%".$keyword."%' and app_id = 101 order by click ".$listOrderType;
        $result = M()->query($sql);
        foreach ($result as $key => $value) {
            $titlearr = explode("||", $value["title"]);
            $result[$key]["title"] = $titlearr[0];
            $picarr = explode("noimg", $value["pic"]);
            if(count($picarr) > 1){
                $result[$key]["pic"] = "/public/Elearn/fengze/images/video.jpg";
            }
            else{
                $result[$key]["pic"] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$value["pic"];
            }
            
        }
        $this->ajaxReturn($result);
    }
    public function play(){
    	$course_id = I("course_id/d",0);
        $course = M();
        $result = $course->field("a.title,a.pic,a.video_code,b.r_grade_name,b.r_subject_name,b.r_version_name")->table("mc_course a,mc_version b")->where("a.version_id = b.id and a.id=%d",$course_id)->find();
        //var_dump($result);exit;
        $titlearr = explode("||", $result["title"]);
        if(count($titlearr)==3){
            $title = $titlearr[0];
            $user_name = $titlearr[1];
            $schoolname = $titlearr[2];
        }
        else{
            $title = $result["title"];
            $user_name = "无";
            $schoolname = "无";
        }
        $pic = $result["pic"];
        $video_code = $result["video_code"];
        $picarr = explode("noimg", $pic);
        if(count($picarr) > 1){
            $pic = "/public/Elearn/fengze/images/video.jpg";
        }
        else{
            $pic = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$pic;
        }
        $remark = $result["r_grade_name"]."  ".$result["r_subject_name"]." ".$result["r_version_name"];
        $this->assign("title",$title);
        $this->assign("user_name",$user_name);
        $this->assign("schoolname",$schoolname);
        $this->assign("pic",$pic);
        $this->assign("video_code",$video_code);
        $this->assign("remark",$remark);
        $this->assign("course_id",$course_id);
    	$this->display("play");
    }

    public function savePlayData(){
        $fzid = cookie('fzid');  //用户标识
        $fztruename = cookie('fztruename'); //真实姓名
        $fznickname = cookie('fznickname'); //昵称
        $fzphone = cookie('fzphone');  //电话
        $fzgender = cookie('fzgender'); //性别(1:男 2:女 3:未知)
        $fzphotoUrl = cookie('fzphotoUrl'); //头像
        $curtime = date("Y-m-d");
        $course_id = I("course_id/d","0");
        $fz_course_click = M("fz_course_click");
        $fz_course_click_temp = M("fz_course_click_temp");
        $data["user_id"] = $fzid;
        $data["user_nickname"] = $fznickname;
        $data["user_truename"] = $fztruename;
        $data["user_phone"] = $fzphone;
        $data["user_gender"] = $fzgender;
        $data["user_photourl"] = $fzphotoUrl;
        $data["course_id"] = $course_id;
        //$fz_course_click->add($data);
        $infores = $fz_course_click->field("id,date_format(play_time,'%Y-%m-%d') as play_time")->where("user_id = '%s' and course_id=%d",$fzid,$course_id)->order("id desc")->select();
        //echo $fz_course_click->getLastSql();
        if(count($infores) == 0){
            $fz_course_click->add($data);
        }
        else{
            $play_time = $infores[0]["play_time"];
            
            if($curtime != $play_time){
              $fz_course_click->add($data); 
            }
        }
        $fz_course_click_temp->add($data);
    	$result["flag"] = 0;
    	$this->ajaxReturn($result);
    }
}
