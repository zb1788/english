<?php

namespace Elearn\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class JiangxiController extends Controller {

    public function index(){
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
    
    public function getCourseList(){
        $subject_code = I("subject_code/s","0");
        $studytype = I("studytype/d","0");
        $username = I("username/s","0");
        $ret_study = array();
        $ret_nostudy = array();
        $sql = "select a.id,a.title,a.pic,a.video_code,a.total_time,b.username as studyid from mc_course a left join (SELECT username,course_id FROM mc_learn_jx_record WHERE username = '".$username."')  b on a.id = b.course_id where a.app_id = 102 and a.subject_code ='".$subject_code."'  order by a.id desc";
        //echo $sql;
        $result = M()->query($sql);
        //var_dump($result);
        foreach ($result as $key => $value) {
            
            if(empty($value["studyid"])){
                $value["studytype"] = "0";
                $result[$key]["studytype"] = "0";
                array_push($ret_nostudy, $value);
            }
            else{
                $value["studytype"] = "1";
                $result[$key]["studytype"] = "1";
                array_push($ret_study, $value);
            }
            
        }
        
        if($studytype == "0"){
            $this->ajaxReturn($result);
        }
        else if($studytype == "1"){
            $this->ajaxReturn($ret_study);
        }
        else{
            $this->ajaxReturn($ret_nostudy);
        }
    }
    public function play(){
    	$course_id = I("course_id/d",0);
        $course = M();
        $result = $course->field("a.title,a.pic,subject_code,a.video_code,total_time")->table("mc_course a")->where(" a.id=%d",$course_id)->find();
        //var_dump($result);exit;
        $pic = $result["pic"];
        $video_code = $result["video_code"];
        $total_time = $result["total_time"];
        $subject_code = $result["subject_code"];
        $pic = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$pic;
       
        $this->assign("title",$title);
        $this->assign("pic",$pic);
        $this->assign("video_code",$video_code);
        $this->assign("total_time",$total_time);
        $this->assign("subject_code",$subject_code);
        $this->assign("course_id",$course_id);
    	$this->display("play");
    }
//
   public  function get_video_time($id,$path)
   {
    //$path = "/tmp/1.mp3";
    $time = exec("ffmpeg -i " . escapeshellarg($path) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
    echo "sssf=".$time;exit;
    list($hms, $milli) = explode('.', $time);
    list($hours, $minutes, $seconds) = explode(':', $hms);
    $total_seconds = (($hours * 3600) + ($minutes * 60) + $seconds)*1000;//毫秒
    M('course')->where("id=%d",$id)->setField('total_time',$total_seconds);
    return $total_seconds;
    //echo $total_seconds;
   }
    public function savePlayData(){
        $course_id = I("course_id/d",0);
        $videourl = I("videourl/s","0");
        $total_time = I("total_time/s","0");
        $username = I("username/s","0");
        //echo "sss=".$total_time;
        if($total_time == "0"){
            $time = exec("ffmpeg -i " . escapeshellarg($videourl) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
            list($hms, $milli) = explode('.', $time);
            list($hours, $minutes, $seconds) = explode(':', $hms);
            $total_time = (($hours * 3600) + ($minutes * 60) + $seconds)*1000;//毫秒
            M('course')->where("id=%d",$course_id)->setField('total_time',$total_time);
        }
        $data["username"] = $username;
        $data["course_id"] = $course_id;
        $res = M("learn_jx_record")->where("username = '".$username."' and course_id =".$course_id)->find();
        //echo M("learn_jx_record")->getLastSql();
        if(count($res) == 0){
            M("learn_jx_record")->add($data);
        }
        $result["total_time"] = $total_time;
        $this->ajaxReturn($result);
        //
    }
    public function post_study_data(){
        $data = I("jsondata/s","0");
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        //echo $data;
        $url="http://gjy.czbanbantong.com/dxp/api/onlineCourse/onlineCourseTime";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Content-Length:' . strlen($data)));//
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        writeAppLog("jxstudy".Date("Y-m-d").".log","||".$res);
    }
}