<?php

namespace Home\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class IndexController extends Controller {

    public function index() {
        $this->display('index');
    }
    public function save_user_login_info() {
        if (cookie('ut')) {
        	$truename = I('truename/s',0);
            $curtime = date("Y-m-d H:i:s");
            $data['username'] = cookie('username');
            $data['truename'] = $truename;
            $data['schoolId'] = cookie('schoolId');
            $data['gradeCode'] = str_replace('\"', '', cookie('gradeCode'));
            $data['classId'] = str_replace('\"', '', cookie('classId'));
            $data['studentId'] = str_replace('\"', '', cookie('studentId'));
            $data['areaCode'] = cookie('areacode');
            $data['usertype'] = cookie('usertype');
            $data['localAreaCode'] = cookie('localAreaCode');
            $data['userkey'] = cookie('ut');
            $data['logtime'] = $curtime;
            $Model = M('user_log');
            $rs = $Model -> where('userkey="'.cookie('ut').'"') ->select();
            if (count($rs) == 0) {
            	$Model->data($data)->add();
            }
           
        }
    }
    public function LvsCheck(){
        $db_host = C("DB_HOST");
        $db_name = C("DB_NAME");
        $db_user = C("DB_USER");
        $db_pwd = C("DB_PWD");
        $model=M('menu_modules');
        $rs = $model ->select();
        if(count($rs) > 0){
            echo "OK";
        }
    }

    public function get_warrant_id(){
        $appid = "t458";
        $timestamp = time();
        $user_id = "vcom";
        $user_client_ip = "127.0.0.1";
        $app_secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        $warrant_available = "7200";
        $request_sign =md5("app_secret=".$app_secret."&appid=".$appid."&timestamp=".$timestamp."&user_client_ip=".$user_client_ip."&user_id=".$user_id);
        //$data['request_sign'] = $request_sign ;
        $parm = "app_secret=".$app_secret."&appid=".$appid."&timestamp=".$timestamp."&user_client_ip=".$user_client_ip."&user_id=".$user_id."&warrant_available=".$warrant_available."&request_sign=".$request_sign;
        $url = "http://trial.cloud.ssapi.cn:8080/auth/authorize";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$parm);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $return_data = curl_exec($ch);
        $errorCode = curl_errno($ch);
        curl_close($ch);
        $return_data = json_decode($return_data,true);
        if($return_data["code"] == "0"){
            $warrant_id = $return_data["data"]['warrant_id'];
        }
        else{
            $warrant_id = "0";
        }
        return $warrant_id;
//         en.word.score
// 英文单词评测


// en.sent.score
// 英文句子评测
       // var_dump($return_data);
    }
    public function uuid($prefix = '')
    {
      $chars = md5(uniqid(mt_rand(), true));
      $uuid  = substr($chars,0,8) . '-';
      $uuid .= substr($chars,8,4) . '-';
      $uuid .= substr($chars,12,4) . '-';
      $uuid .= substr($chars,16,4) . '-';
      $uuid .= substr($chars,20,8);
      return $prefix . $uuid;
    }
/**
     * 签名函数
     * @param array $param
     * @return string
     */
    public function build_signature($param) {
        
        $encoding_data_kv =array();
        if (array_key_exists('app',$param)){
            foreach ($param['app'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('audio',$param)){
            foreach ($param['audio'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('request',$param)){
            foreach ($param['request'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('sdk',$param)){
            foreach ($param['sdk'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        $keys=array_keys($encoding_data_kv);
        sort($keys);
        $signature_string="";
        foreach ($keys as $key => $value) {
            $signature_string=$signature_string."&$value=".$encoding_data_kv[$value];
        }
        $signature_string=substr($signature_string, 1);
        $signature_md5=md5($signature_string);
        return $signature_md5;
    }
    //先声评测接口示例
    public function TestEnScore(){
        $texttype = "1";
        $filename = "c17f4215a135929386bd3a513d61e4e0-mp3to44100.mp3";
        $text = "father";
        // smartstudy($filename,$text,1);
        // exti;
        $filedir = 'D:\wamp\www\en\recordwav\\';
        $filepath = $filedir.$filename;
        $video_info = getVideoInfo($filepath);
       //var_dump($video_info);exit;
        $acodec = $video_info['acodec'];
        $acodecarr = explode('mp4a',$acodec);
        
        if(count($acodecarr) > 1){
            $filename = EvaluateTrasfermp3($filename);
            //echo $filename;
        }
        $texttype = $texttype;
        Vendor('Audio');
        $audio = new \Audio();
        $username = cookie("username");
        //echo $username;
        $ip = getClientIp();
        //$ip = "127.0.0.1";
        $arr = explode('.',$filename);
        $extension = $arr[1];
        if($extension == "amr"){
            $sampleRate = 8000;
        }
        else{
            $sampleRate = 44100;
        }
        // if($video_info['asamplerate'] == 8000 && $extension == "mp3"){
        //     $filename = EvaluateTrasferamr($filename);
        //     echo $filename;
        //     exit;
        // }
        $evaluate_params = array("refText" => $text,"precision" => 0.8,"rank" => 100,"tokenId" => "default");
        $user_info = array();
        $audio_info = array("sampleRate"=>$sampleRate,"audioType" =>$extension);
        $filename = APP_ROOT."/recordwav/".$filename;
        //echo $filename;exit;
        $ret = $audio -> speakEvaluate($texttype,$evaluate_params,$user_info,$audio_info,$filename);
        echo json_encode($ret);
    }
    
}
 