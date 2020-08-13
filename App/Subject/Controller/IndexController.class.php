<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class IndexController extends CheckController {

    //英语学科的首页
    public function index() {
        //getuserinfos();
        $subjectid=I("subjectid","");
        $gradeid=I("gradeid","");
        if(!empty($subjectid)){
            cookie("subjectid",$subjectid);
        }
        if(!empty($gradeid)){
           cookie("gradeid",$gradeid); 
        }
        $this->display("index");
    }
    //英语平板学科的首页
    public function padindex() {
        //getuserinfos();
        $grade=cookie("gradeid");
        $this->assign("grade",$grade);
        $this->display("padindex");
    }
    public function xmlHttp(){
        $data= I("userinfo/s","0");
        echo $data;
    }
    //展示unit
    public function unit(){
        //getuserinfos();
        $gradeid=I("gradeid");
        if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){

            cookie("gradeid",$gradeid);
        }
        $backUrl=cookie("backUrl");
        $localAreaCode = cookie("localAreaCode");
        if(empty($backUrl)){
            $backUrl="";
        }else{
            $backUrl=$backUrl;
        }
        $areacode = cookie("areacode");
        if($areacode == "35.05.03" || $localAreaCode =="86." || $localAreaCode =="73."){
            $is_share = 1;
        }
        else{
            $is_share = 0;
        }
        $this->assign("is_share",$is_share);
        $this->assign("localAreaCode",$localAreaCode);
        $this->assign("backUrl",$backUrl);
        $this->display('React/unit');
    }

     //展示word
    public function word(){
        //getuserinfos();
        $this->display('React/word');
    }

    public function unitlist(){
        //getuserinfos();
        $this->display('React/index');
    }


    //英语学科的首页
    public function setbook() {
        //getuserinfos();
        $this->display("setbook");
    }

    //精品入口
    public function entrance() {
        $deviceType=cookie("deviceType");
        $grade=cookie("gradeid");
        $gradeid=I("gradeid",0);
        cookie("backUrl","");
        if(empty($gradeid)){
            $gradeid = $grade;
        }
        $this->assign("gradeid",$gradeid);
        if($deviceType=='pad'){
            $this->display("padindex");
        }else{
            $this->display("React/index");
        }
    }

    public function mobileindex(){
        //getuserinfos();
        $this->display("React/index");
    }

    public function padenindex(){
        //getuserinfos();
        $this->display("padindex");
    }

    
    //获取用户信息用于以后扩展
    public function getEnUserinfo(){
        //getuserinfos();
        $userconfig=D("user_config");
        //使用cookie
        $subjectid=cookie("subjectid");
        $username=cookie("username");
        $rs=$userconfig->getUserConfig($username,$subjectid);
        $this->ajaxReturn($rs);
    }

    //入口页面json数据生成方法
    public function dodata(){
        //getuserinfos();
        $grade=array("0001","0002","0003","0004","0005","0006","0007","0008","0009");
        $grades=array("一年级","二年级","三年级","四年级","五年级","六年级","七年级","八年级","九年级");
        $subject=array("0001","0002","0003","0004","0005","0011","0012","0013");
        $subjects=array("语文","数学","英语","物理","化学","地理","政治","历史");
        $styles=array("bGreen radius8","bZi radius8","bYellow radius8","bGreen radius8","bZi radius8","bYellow radius8","bGreen radius8","bZi radius8");
        $arr=array();
        for($i=0;$i<10;$i++){
            $temp=array();
            $temp["gradeid"]=$grade[$i];
            $temp["title"]=$grades[$i];
            $temp["subjects"]=array();
            foreach($subject as $key=>$value){
                $subjectarr=array();
                $subjectarr["subjectid"]=$value;
                $subjectarr["title"]=$subjects[$key];
                $subjectarr["style"]=$styles[$key];
                $sql="SELECT * FROM engs_menu_config LEFT JOIN engs_menu_modules ON engs_menu_config.`menuid`=engs_menu_modules.`id` WHERE engs_menu_config.`r_grade`= '%s' AND engs_menu_config.`r_subject`='%s' and engs_menu_modules.url is not null order by engs_menu_config.sortid";
                $rs=M()->query($sql,$grade[$i],$value);
                $subjectarr["modules"]=$rs;
                array_push($temp["subjects"], $subjectarr);
            }
            array_push($arr,$temp);
        }
        $this->ajaxReturn($arr);
    }

    public function getCourse(){
        //getuserinfos();
        $subjectid = I("subjectid");
        $cache_key = md5("subjectidbookversion".$subjectid);
        $rms_unit=D("rms_unit");
        if($subjectid=='0001'){
            $result=$rms_unit->getCnCourse($subjectid,$this->user["versionid"],$this->user["gradeid"],$this->user["termid"]);
        }else if($subjectid=='0003'){
            $result=$rms_unit->getEnCourse($subjectid,$this->user["versionid"],$this->user["gradeid"],$this->user["termid"]);
        }
        $this->ajaxReturn($result);
    }
    
    
    //推荐位置进行url跳转
    public function recomment(){
        //getuserinfos();
        //模块id根据模块id进行跳转
        $moduleid=I("moduleid");
        //$gradeid=I("gradeid");
        if($moduleid == "13"){
            $moduleid = "63";
        }
        $gradeid=cookie("gradeid");
        $backUrl=I("backUrl");
        $backUrl="http://www.czbanbantong.com";
        $subjectid=I("subjectid/s","0");	
        cookie("backUrl","/Subject/index/dojumpback");
        is_module_grant($moduleid);
        $learnways=getmodule($moduleid);
        $url=$learnways["url"];
        $query="";
        $count=count($_GET);
        $user=D("User","Service");
       // echo $subjectid;exit;
		if($subjectid == "0"){
			$subjectrs = M("menu_config")->where("menuid=%d",$moduleid)->find();
			$subjectid = $subjectrs["r_subject"];
			$query=$query."subjectid=".$subjectid;
        }
        // if($subjectid == "0"){
		// 	$subjectrs = M("menu_config")->where("menuid=%d",$moduleid)->find();
		// 	$subjectid = $subjectrs["r_subject"];
		// 	$query=$query."subjectid=".$subjectid;
        // }
        //echo cookie("gradeid");exti;
        if(in_array($moduleid,C("except"))){
            $gradeid= cookie("gradeid");
            if($gradeid == "0011" || $gradeid == "0012" || $gradeid == "0013"){
                $gradeid = "0010";
            }
            $query=$query."&gradeid=".$gradeid."&backUrl=".$backUrl;
        }else{
            $tempgradeid=I("gradeid/s");
            $rs = array();
            if(empty($tempgradeid)){
                $rs=$user->getUserConfig($this->user,$subjectid,$gradeid);
                $gradeid = $rs["gradeid"];
            }else{
                $gradeid = $tempgradeid;
            }
            if($gradeid == "0011" || $gradeid == "0012" || $gradeid == "0013"){
                $gradeid = "0010";
            }
            $query=$query."&gradeid=".$gradeid."&backUrl=".$backUrl;
        }
        //echo $rs["gradeid"];
        // $rs=$user->getUserConfig($this->user,$subjectid,$gradeid);
        // if(!empty($rs)){
        //     $query=$query."&gradeid=".$rs["gradeid"]."&backUrl=".$backUrl;
        // }else{
        //     $query=$query."&gradeid=".$gradeid."&backUrl=".$backUrl;
        // }
        foreach($_GET as $key=>$value){
            if($key=='m'||$key == 'gradeid'){continue;}
            if($key== "moduleid" && $value=="13"){
                $value = "63";
            }
            if($key == "backUrl"){
               $value = "/Subject/index/dojumpback";
            }
            $query=$query."&".$key."=".$value;
        }
        //echo $url."?".$query;exit;
        //判断字符串中是否有问号
        if(strpos($url,"?") === false){     //使用绝对等于
            //不包含
            redirect("../../".$url."?".$query);
        }else{
            //包含
            redirect("../../".$url."&".$query);
        }   
    }   
    
    public function dojumpurl(){
      //  $userinfo = //getuserinfos();
        //模块id根据模块id进行跳转
        $moduleid=I("moduleid/d",0);
        //$gradeid=I("gradeid");
        $gradeid=I("gradeid/s","0003");
        $subjectid=I("subjectid/s","0003");
        $termid = I("termid/s","0001");
        $versionid = I("versionid/s","0001");
        $ks_code = I("ks_code/s","0");
        $backUrl = I("backUrl/s","0");
        cookie("yjt_backUrl",$backUrl);
        cookie("backUrl","/Subject/index/dojumpback");
        is_module_grant($moduleid);
        $learnways=getmodule($moduleid);
        $url=$learnways["threeurl"];
        //echo $url;exit;
        $areacode=cookie("localAreaCode");
        $query="";
        $count=count($_GET);
        $user=D("User","Service");
        if($moduleid == 10){
            $sql = "select style from db_yuwen.yw_unit where ks_code='".$ks_code."'";
            $result = M()->query($sql);
            if($result){
                if($result[0]["style"] == "3"){
                    $url = "Yuwen/text/py"; 
                }
            }
        }
        foreach($_GET as $key=>$value){
           // if($key=='m'||$key == 'gradeid'){continue;}
           if($key == "backUrl"){
               $value = "/Subject/index/dojumpback";
           }
            $query=$query."&".$key."=".$value;
        }
        if($moduleid == 16){   //快乐背古诗
            $rs=M("yjt_url")->where("areacode='%s' and url_type='vfs_url'",$areacode)->find();
            $callbackURL = PROTOCOL.'://'.WEB_DOMAIN.'/Subject/index/dojumpback';
            redirect(PROTOCOL.'://'.$rs["url"].'/A01/lib/defaultstyle/2018yjt/03/shici/index.html?gradeid='.$gradeid.'&localAreaCode='.$areacode.'&parentId='.cookie("parentid")."&ksId=0001450901&studentId=".cookie("username")."&ut=".cookie("ut")."&callbackURL=".$callbackURL);
            exit;
        }
        if($moduleid == 1){
            $rs=M("rms_unit")->field("ks_code")->where("r_subject='%s' and r_version='%s' and r_grade = '%s' and r_volume = '%s'","0003",$versionid,$gradeid,$termid)->order("display_order asc")-> select();
           // var_dump($rs);
           $curunitindex = 0;
           foreach ($rs as $key => $value) {
               if($ks_code == $value["ks_code"]){
                   break;
               }
               else{
                $curunitindex++;  
               }
           }
           $query = $query."&curunitindex=".$curunitindex;
           //var_dump($rs);exit;
            //exit;
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

    function dojumpback(){
        // $url = cookie("yjt_backUrl");
        // $url = urldecode($url);
        //$url = "http://www.czbanbantong.com/";
        //echo $url;exit;
        // redirect($url);
        $this->display("dojumpback");
    }
}
