<?php
namespace Subject\Controller;
use Think\Controller;

/**
* 验证是否登录控制器类
*
* @author         gm
* @since          1.0
*/

class ApiController extends Controller {
	//对外的接口
    public function getAppSituation(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $callback = I("callback");
        $username=I("username/s");
        //将数据写入文件
        file_put_contents('apiuser.txt',$username." ".Date("Y-m-d H:i:s").' getAppSituationInterface'.PHP_EOL,FILE_APPEND);
        if(empty($username)){
            $ret["suc"]=0;
            $ret["msg"]="用户不能为空";
            $ret["list"]=array();
        }else{
            $num=I("num/d",0);
            $sql="SELECT  engs_menu_modules.`title`,concat('".PROTOCOL."://".C("local_service")."/public/public/images/"."',engs_menu_modules.`style`,'.png') as imgcontent,";
            $sql=$sql." '1' as count FROM engs_menu_config ";
            $sql=$sql."LEFT JOIN engs_menu_modules ON engs_menu_config.`menuid` = engs_menu_modules.id ";
            //$sql = $sql." LEFT JOIN  ";
            //$sql=$sql."(SELECT  moduleid, COUNT(*) AS COUNT FROM engs_user_modules_unit_log GROUP BY moduleid) AS USER  ";
            //$sql=$sql."ON USER .`moduleid` = engs_menu_modules.id WHERE engs_menu_config.`ifuser` = 1  ";
            $sql = $sql." WHERE engs_menu_config.`ifuser` = 1  ";
            $sql=$sql."AND engs_menu_config.`id` IS NOT NULL  ";
            $sql=$sql."AND engs_menu_modules.id IS NOT NULL AND engs_menu_modules.url IS NOT NULL GROUP BY engs_menu_modules.id   ";
            //$sql=$sql." ORDER BY COUNT DESC ";
            if(!empty($num)){
                $sql=$sql."limit ".$num;
            }
            try{
                $rs=M()->query($sql);
                $ret["suc"]=1;
                $ret["msg"]="请求成功";
                $ret["list"]=$rs;
            }catch(Exception $e){
                $ret["suc"]=0;
                $ret["msg"]=$e->getMessage();
                $ret["list"]=array();
            }
        }
        echo $callback, '(', json_encode($ret), ')';
    }


    //前端异常监控代码
    public function setErrorLogApi(){
        $msg=I("msg");
        $line=I("line");
        $url=I("url");
        $agent=I("agent");
        $date=I("date");
        $referrer=I("referrer");
        if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
        {$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];}
        elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
        {$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];}
        elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
        {$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];}
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
        {$ip = getenv("HTTP_X_FORWARDED_FOR");}
        elseif (getenv("HTTP_CLIENT_IP"))
        {$ip = getenv("HTTP_CLIENT_IP");}
        elseif (getenv("REMOTE_ADDR"))
        {$ip = getenv("REMOTE_ADDR");}
        else{$ip = "Unknown";}
        $arr["msg"]=$msg;
        $arr["line"]=$line;
        $arr["url"]=$url;
        $arr["agent"]=$agent;
        $arr["clientip"]=$ip;
        $arr["useragent"]=$referrer;
        $arr["serverip"]=$_SERVER['SERVER_ADDR'];
        $arr["phpsession"]=cookie("PHPSESSID");
        $errorinfo=json_encode($arr);
        //进行写日志
        $curdate=Date("Ymd");
        file_put_contents('./log/error.log',$date.":".$errorinfo.PHP_EOL,FILE_APPEND);
    }

    //前端异常监控代码
    public function setPerformanceLogApi(){
        $performance=I("performance");
        $date=I("date");
        $performance = stripslashes($performance);
        $performance = rtrim($performance, '"');
        $performance = ltrim($performance, '"');
        $performance = str_replace('&quot;', '"', $performance);
        $performance = json_decode($performance,true);
        $url=I("url");
        $referer=I("referrer");
        if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
        {$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];}
        elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
        {$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];}
        elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
        {$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];}
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
        {$ip = getenv("HTTP_X_FORWARDED_FOR");}
        elseif (getenv("HTTP_CLIENT_IP"))
        {$ip = getenv("HTTP_CLIENT_IP");}
        elseif (getenv("REMOTE_ADDR"))
        {$ip = getenv("REMOTE_ADDR");}
        else{$ip = "Unknown";}
        $performance["referer"]=$referer;
        $performance["clientip"]=$ip;
        $performance["serverip"]=$_SERVER['SERVER_ADDR'];
        //浏览器类型
        $performance["useragent"]=$_SERVER['HTTP_USER_AGENT'];
        $performance["cookie"]=$_SERVER['HTTP_COOKIE'];
        $performance["cururl"]=$url;
        $performance["date"]=$date;
        $performance=json_encode($performance);
        $phpsession=cookie("PHPSESSID");
        //进行写日志
        $curdate=Date("Ymd");
        file_put_contents('./log/performance.log',strtotime("now").":".$phpsession.":".$performance.PHP_EOL,FILE_APPEND);
    }




    private function downfile($file_path,$file_name){
        header("Content-type:text/html;charset=utf-8");
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            echo "没有该文件文件";
            exit;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);

        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
        $file_con="";
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=$file_con.fread($fp,$buffer);
            $file_count+=$buffer;
        }
        echo $file_con;
        fclose($fp);
    }

    //通过接口回去每天的日志下载接口
    public function getErrorLogApi(){
        $curdate=I("date");
        if(empty($curdate)){
            $curdate=Date("Ymd");
        }
        $filename="./log/".$curdate."error.log";
        $this->downfile($filename,$curdate."error.log");
    }

    //获取每天的性能的直直
    public function getPerformanceLogApi(){
        $curdate=I("date");
        if(empty($curdate)){
            $curdate=Date("Ymd");
        }
        $filename="./log/".$curdate."performance.log";
        $this->downfile($filename,$curdate."performance.log");
    }

    public function getGradeSubjectPic(){
        $grade=I("grade");//学段
        $username=I("username");//扩展的个性化


    }


    //统计的接口
    public function analysisInterface(){
        $date=I("date","");
        if(empty($date)){
            exit("日期不能为空");exit;
        }
        $file_path='./log/'.$date."log";
        $filename=$date."log";
        $iplist=array('172.16.20.206','172.16.20.207','172.16.20.208');
        //判断本机的ip
        $ip='127.0.0.1';
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        //获取操作系统为win2000/xp、win7的本机IP真实地址
        exec("ifconfig", $out, $stats);
        if (!emptyempty($out)) {
            if (isset($out[1]) && strstr($out[1], 'addr:')) {
                $tmpArray = explode(":", $out[1]);
                $tmpIp = explode(" ", $tmpArray[1]);
                if (preg_match($preg, trim($tmpIp[0]))) {
                    $ip=trim($tmpIp[0]);
                }
            }
        }
        //判断是那一台服务器
        $index=array_search($ip,$iplist);
        $index=($index==0?1:0);
        $oip=$iplist[$index];
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 5 //超时时间，单位为秒
            )
        ));
        $content=file_get_contents("http://".$oip."/log/".$filename,0,$context);
        if($content["http_code"]!=200){
            //表示服务器异常直接对接微信进行消息的发送
            $content="";
        }
        file_put_contents($file_path, $content.PHP_EOL,FILE_APPEND);
        //下载文件
        header("Content-type:text/html;charset=utf-8");
        //首先要判断给定的文件存在与否

        if(!file_exists($file_path)){
            echo "没有该文件文件";
            exit;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);

        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
        $file_con="";
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=$file_con.fread($fp,$buffer);
            $file_count+=$buffer;
        }
        echo $file_con;
        fclose($fp);
    }

    //pad获取智学助手栏目接口
    public function getRecommendApp(){
        $gradeid=I("gradeid");
        $ut=I("ut");
        $backUrl=I("backUrl");
        $deviceType-I("deviceType");
        $areaCode=I("areaCode");
        $isrecommend=I("isrecommend/d",1);
        $levelid=I("levelid/d",0);
        $menuview=D("TjMenuView");
        $result=$menuview->where("TjMenuConfig.levelid=%d and isrecommend=%d",$levelid,$isrecommend)->order("TjMenuConfig.sortid")->select();
        $ret=array();
        foreach($result as $key=>$value){
            $temp["appTitle"]=$value["title"];
            $temp["appUrl"]="http://".$_SERVER["HTTP_HOST"].'/Subject/recomment?';
            $temp["appIconImg"]="http://".$_SERVER["HTTP_HOST"].'/'.__ROOT__.'/public/public/images/'.$value["style"].".png";
            $temp["appNewIcon"] = $value["isnew"];
            if($value["type"]=='6'){
                $temp["appUrl"]=$temp["appUrl"]."moduleid=".$value["id"]."&subjectid=".$value["r_subject"]."&gradeid=".$gradeid."&type=".$value["type"]."&ut=".$ut."&deviceType=".$deviceType."&areaCode=".$areaCode."&backUrl=".$backUrl;
            }
            else if($value["type"]=='10'){
                // 定义第三方应用url协议格式为：
                // localapp://包名&类名&参数
                // 注：参数防止有特殊字符，需要单独进行UrlEncode编码为utf-8格式。
                // 举例：打开本地应用flashplayer
                // localapp://com.hianzuo.oemswfply&com.hianzuo.jiajia.AppPlayActivity&%7b%22fromApp%22%3a%22com.zzvcom.eschoolbag%22%2c%22params%22%3a%22%5b%5d%22%2c%22uri%22%3a%22http%3a%2f%2fwww.test.com%2fswf%2ftest.swf%22%7d
                 $temp["appUrl"] = "localapp://".$value['url'];
            }
            else{
                $temp["appUrl"]=$temp["appUrl"]."moduleid=".$value["id"]."&subjectid=".$value["r_subject"]."&gradeid=".$gradeid."&type=".$value["type"]."&ut=".$ut."&deviceType=".$deviceType."&areaCode=".$areaCode."&backUrl=".$backUrl;
            }
            array_push($ret, $temp);
        }
        $this->ajaxReturn($ret);
    }






        //排行榜问题
    public function getRankList(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $callback = I("callback");
        $gradeid=I("gradeid");
        $provice=I("provice",0);//在参与对象是否做限制
        $start=I("start/d",0);//开始的记录
        $num=I("num/d",15);//获取的条数
        $starttime=I("starttime/s",Date("Y-m-d H:i:s"));
        $endtime=I("endtime/s",Date("Y-m-d H:i:s"));
        if(!empty($provice)){            
            $sql="SELECT COUNT(*) AS levelnum,MAX(s.`truename`) AS truename,MAX(s.`schoolname`) AS schoolname,MAX(s.`fullname`) AS fullname,MAX(s.`classname`) AS classnam,MAX(t.`submittime`) AS submittime,SUM(t.`submittime`-s.`addtime`) AS usertime,SUM(t.`rightnum`) AS score FROM engs_user_word_game_rank t LEFT JOIN engs_user_word_game_level s ON t.`wordgameuserid`=s.`id` WHERE t.gradeid='%s' AND (t.usertype=0 OR t.usertype=4) AND t.localareacode='".$provice."' and t.submittime>='".$starttime."' and t.submittime <= '".$endtime."' GROUP BY t.username ORDER BY score DESC,usertime DESC,submittime DESC ";
            if(!empty($num)&&$start!==""){
              $sql=$sql." limit ".$start.",".$num;
            }
        }else{            
            $sql="SELECT COUNT(*) AS levelnum,MAX(s.`truename`) AS truename,MAX(s.`schoolname`) AS schoolname,MAX(s.`fullname`) AS fullname,MAX(s.`classname`) AS classnam,MAX(t.`submittime`) AS submittime,SUM(t.`submittime`-s.`addtime`) AS usertime,SUM(t.`rightnum`) AS score FROM engs_user_word_game_rank t LEFT JOIN engs_user_word_game_level s ON t.`wordgameuserid`=s.`id` WHERE t.gradeid='%s' AND (t.usertype=0 OR t.usertype=4) and t.submittime>='".$starttime."' and t.submittime <= '".$endtime."'  GROUP BY t.username ORDER BY score DESC,usertime DESC,submittime DESC ";
            if(!empty($num)&&$start!==""){
                $sql=$sql." limit ".$start.",".$num;
            }
        }
        $rs=M()->query($sql,$gradeid);
        $ret["ranklist"]=$rs;
        echo $callback, '(', json_encode($ret), ')';
    }


    //获取用户的星星
    public function getMyStarRank(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $callback = I("callback");
        $username=I("username/s","");
        $provice=I("provice",0);
        $levelnum=0;
        $starnum=0;
        $rank=0;
        //查询用户是多少名
        $mysql="select s.username,s.fullname,s.truename,s.schoolname,s.classname,MAX(t.submittime) AS submittime,SUM(t.rightnum) AS starnum,count(*) as levelnum from engs_user_word_game_rank t left join engs_user_word_game_level s on t.wordgameuserid=s.id  where t.localareacode='%s'  and t.username='%s' and t.isover=1 and t.issuc=1 and s.username is not null group by s.username";
        $myrs=M()->query($mysql,$provice,$username);
        $userinfo=array();
        if(!empty($myrs)&&$myrs[0]["submittime"]!=null){
            $levelnum=$myrs[0]["levelnum"];
            $starnum=$myrs[0]["starnum"];
            $submittime=$myrs[0]["submittime"];
            $userinfo["username"]=$myrs[0]["username"];
            $userinfo["fullname"]=$myrs[0]["fullname"];
            $userinfo["truename"]=$myrs[0]["truename"];
            $userinfo["schoolname"]=$myrs[0]["schoolname"];
            $userinfo["classname"]=$myrs[0]["classname"];
        }

        if($levelnum!=0){
            $mysql="select t.username,MAX(t.submittime) AS submittime,SUM(t.rightnum) AS levelnum from engs_user_word_game_rank t engs_user_word_game_rank t LEFT JOIN engs_user_word_game_level s ON t.`wordgameuserid` = s.`id`  where t.localareacode='".$provice."' and t.isover=1 and t.issuc=1 AND (t.usertype = 0 OR t.usertype = 4) AND s.truename IS NOT NULL AND t.submittime > '2019-05-21 00:00:00' group by t.username having levelnum>".$starnum." or (levelnum=".$starnum." and submittime<'".$submittime."')";
            $levelrs=M()->query($mysql);
            $rank=count($levelrs)+1;
        }
        $ret["levelnum"]=$levelnum;
        $ret["starnum"]=$starnum;
        $ret["rank"]=$rank;
        $ret["userinfo"]=$userinfo;
        echo $callback, '(', json_encode($ret), ')';
    }


    public function getUserLatestPush(){
        $username=I("username");
        $usertype=I("usertype");
        $localareacode=I("localareacode");
        $msg="成功";
        $sql="SELECT CASE WHEN sum(pushnum) IS NULL THEN 0 ELSE sum(pushnum) END total  FROM engs_user_record WHERE username='%s' AND localareacode='%s' AND usertype = '%s' AND isdel=0";
        try{
           $rs=M()->query($sql,$username,$localareacode,$usertype);
        }catch(Exception $e){
           $msg="数据库出错";
        }
        $ret["msg"]=$msg;
        $ret["code"]=1;
        $ret["num"]=0;
        if(!empty($rs)){
            $ret["num"]=$rs[0]["total"];
        }
        $this->ajaxReturn($ret);
    }


    //获取用户的登录的区域信息
    public function getLocation(){
        $data["localareacode"]=encode(cookie("localAreaCode"),C("appkey"));
        $data["code"]=encode(cookie("vfs_url"),C("appkey"));
        $this->ajaxReturn($data);
    }


    //分享出去的获取二维码信息
    public function getQrcodeInfo(){
        $localareacode=I("localareacode");
        $code=I("code");
        //判断是什么手机android还是iphone
        $type=get_device_type();
        $localareacode=decode($localareacode,C("appkey"));
        $code=decode($code,C("appkey"));
        //数据库查询获取二维码信息
        $rs=M("yjt_url")->where("areacode='%s' and url_type='qr_url'",$localareacode)->find();
        if(empty($rs)){
            $data["title"]="优教信使";
            $data["url"]="http://cms.czbanbantong.com/pic/download/html/index.html";
        }else{
            $data["title"]=$rs["areaname"];
            $data["url"]=$rs["url"];
        }
        $data["qrurl"]="http://".$code."/A01/lib/defaultstyle/portalWJ/public/images/login/code2.png";
        $data["type"]=$type;
        $data["area"]=$localareacode;
        $this->ajaxReturn($data);
    }

    public function getEnList(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $ks_code = I("ks_code/s",null);
        $offset=I("offset/d",0);
        $num=I("num/d",10);
        $jsoncallback = I('jsoncallback/s','0');
        if(empty($ks_code)){
            $ret["suc"]=0;
            $ret["total"]=0;
            $ret["data"]=array();
            $ret["url"]="";
            $ret["msg"]="参数不能为空";
        }else{
            $exams=D("exams");
            $rs=$exams->getExamsListByKscode($ks_code,$offset,$num,C("respreview"));
            $total=$exams->getExamsListCountByKscode($ks_code);
            $ret["suc"]=1;
            $ret["total"]=$total;
            $ret["data"]=$rs;
            $ret["msg"]="成功";
        }
        if($jsoncallback != '0'){
            $this->ajaxReturn($ret,'JSONP');
        }
        else{
            $this->ajaxReturn($ret,'JSON');
        }
    }

    //获取单元下面的单词数据
    public function getWordListByKscode(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $ks_code=I("ks_code/s");
        $callback=I("callback",0);
        //获取本单元的单词
        $word=D("Word","Service");
        $rs=$word->getWordListByUnit($ks_code);
        $wordlist=array();
        foreach($rs["data"] as $key=>$value){
          $word=$value;
          $word["enmp3"]=PROTOCOL."://".C("word_mp3_path").$value["ukmp3"];
          $word["cnmp3"]=PROTOCOL."://".C("mp3_word_explain").substr($value["explains_mp3"],0,2)."/".$value["explains_mp3"].".mp3";
          $wordlist[$value["chaptername"]][]=$word;

        }
        if($callback != '0'){
            $this->ajaxReturn($wordlist,'JSONP');
        }else{
            $this->ajaxReturn($wordlist,'JSON');
        }
    }

    //获取版本下面的所有的单词
    public function getUniListByVersion(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $ks_code=I("ks_code/s");
        $callback=I("callback",0);
        //查询单元的书籍信息
        $unit=D("RmsUnit");
        $rs=$unit->getInfoByKsCode($ks_code);
        if(empty($rs)){
            $rs=array();
        }else{
            $volumeid=$rs["r_volume"];
            $gradeid=$rs["r_grade"];
            $versionid=$rs["r_version"];
            $rmsunit=D("RmsUnit");
            $rs=$rmsunit->getEnUnitData($gradeid,$volumeid,$versionid,"0003","","0");
            if(empty($rs)){
                $rs=array();
            }
        }
        if($callback != '0'){
            $this->ajaxReturn($rs,'JSONP');
        }
        else{
            $this->ajaxReturn($rs,'JSON');
        }
    }


	
	public function getUBInterface(){
        $ub = 1;
        if($ub>0){
            $result=getUbiInterface("u_pass_check_zxzs",$ub,"z","my","my");
            $issuc=$result[0]['result'];
	        if($issuc==1){
                $data["suc"]=1;//成功
                //$userWordBatch->where("id=%d",$id)->save($data);
            }else{
                $data["suc"]=0;//失败
            }
        }else{
            $data["suc"]=2;//表示不送UB
        }
        $this->ajaxReturn($data);
    }



    public function getUserModuleList(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        getuserinfos();
        $num = I("num/d",8);
        $callback=I("callback",0);
        $username = cookie("username");
        $localareacode = cookie("localAreaCode");
        $usertype = cookie("usertype");
        $gradeid = cookie("usergradeid");
        if((int)$gradeid>10){$gradeid="0010";}
        $ret = array();
        $rs = M("user_modules_unit_log")->where("username = '%s' and usertype = %d and localareacode = '%s' and usergradeid = '%s' and moduleid!='13'",$username,$usertype,$localareacode,$gradeid)->group("moduleid")->limit($num)->field("moduleid,count(*) as count")->order("count desc")->select();
        $rcount = count($rs);
        //如果用户没有需要的那么多就进行补充
        if($rcount<$num){
            //查看本年级下面的数据
            $result = M("user_grade_module_rank")->where("usergradeid='%s'  and moduleid!='13'",$gradeid)->field("moduleid,clicknum as count")->select();
            $modules = array_column($rs,'moduleid');
            foreach($result as $key=>$value){
                if(!in_array($value["moduleid"],$modules)){
                    array_push($rs, $value);
                }
            }
            $rcount = count($rs);
            if($rcount<$num){
                //模块进行补充
                $sql = 'SELECT s.id as moduleid,"0" as count FROM engs_menu_config t LEFT JOIN engs_menu_modules s ON t.`menuid` = s.`id` WHERE s.id IS NOT NULL AND t.`r_grade`="%s" and s.id!="13"  group by s.id  ORDER BY t.isrecommend,t.sortid';
                $recommendrs = M()->query($sql,$gradeid);
                $modules = array_column($rs,'moduleid');
                foreach($recommendrs as $key=>$value){
                    if(($key+1+$rcount)>$num){
                        break;
                    }
                    if(!in_array($value["moduleid"],$modules)){
                        array_push($rs, $value);
                    }
                    
                }
            }
        }   
        if(empty($rs)){
            $ret["count"] = 0;
        }else{
            $ret["count"] = count($rs);
        }
        $ret["data"] = $rs;
        if(empty($callback)){
            echo json_encode($rs);
        }else{
            echo $callback, '(', json_encode($rs), ')';
        }
    }


    public function setUserModuleUnitLog(){
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        getuserinfos();
        $username=cookie("username");
        $areacode=cookie("areacode");
        $schoolid=cookie("schoolid");
        $classid=cookie("classid");
        $gradeid=cookie("gradeid");
        $truename=cookie("truename");
        $subjectid=cookie("subjectid");
        $moduleid=I("moduleid","");
        $ks_code=I("ks_code","");
        $ks_name=I("ks_name","");
        setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name);
    }

     public function getUserBookRecord(){
        $token=I("token");
        $info = decode($token,C("appkey"));
        
        $arr = explode("|",$info);

        if(count($arr)<2){
            $result["code"] = 0;
            $result["code"] = "参数不正确";
        }else{
            $bookid = $arr[1];
            $username = $arr[0];
            $sql="select engs_book_pic.*,engs_book_page_content.id as contentid,engs_book_page_content.encontent,engs_book_page_content.cncontent,engs_user_text_read_share.filename as usermp3 from engs_book_pic left join engs_book_page_content on engs_book_pic.id = engs_book_page_content.`bookpicid` left join engs_user_text_read_share on  engs_book_pic.id = engs_user_text_read_share.chapterid and engs_book_pic.bookid = engs_user_text_read_share.ks_code and username='%s' and apptype = 6 where bookid=%d and isdel=1 order by pageindex,engs_book_pic.id,engs_book_page_content.id,engs_book_page_content.sortid ";
            $bookpicrs=M()->query($sql,$username,$bookid);
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
                //$value["usermp3"]="http://".$book_mp3_path.$value["mp3"];
                $value["mp3"]= PROTOCOL."://".WEB_DOMAIN."/recordwav/".$value["usermp3"];
                $result[$key] = $value;
                $result[$key]["contents"][] = $value;
            }
        }
        
        
        $this->ajaxReturn($result);
    }

    //长安获取应用的info信息
    public function getModuleList(){
        // header('Access-Control-Allow-Origin:*');
        // header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        // getuserinfos();
        // $num = I("num/d",8);
        // $callback=I("callback",0);
        // $username = cookie("username");
        // $localareacode = cookie("localAreaCode");
        // $usertype = cookie("usertype");
        // $gradeid = cookie("usergradeid");
        // if((int)$gradeid>10){$gradeid="0010";}
        // $ret = array();
        // $rs = M("user_modules_unit_log")->where("username = '%s' and usertype = %d and localareacode = '%s' and usergradeid = '%s' and moduleid!='13'",$username,$usertype,$localareacode,$gradeid)->group("moduleid")->limit($num)->field("moduleid,count(*) as count")->order("count desc")->select();
        // $rcount = count($rs);
        // //如果用户没有需要的那么多就进行补充
        // if($rcount<$num){
        //     //查看本年级下面的数据
        //     $result = M("user_grade_module_rank")->where("usergradeid='%s'  and moduleid!='13'",$gradeid)->field("moduleid,clicknum as count")->select();
        //     $modules = array_column($rs,'moduleid');
        //     foreach($result as $key=>$value){
        //         if(!in_array($value["moduleid"],$modules)){
        //             array_push($rs, $value);
        //         }
        //     }
        //     $rcount = count($rs);
        //     if($rcount<$num){
        //         //模块进行补充
        //         $sql = 'SELECT s.id as moduleid,"0" as count FROM engs_menu_config t LEFT JOIN engs_menu_modules s ON t.`menuid` = s.`id` WHERE s.id IS NOT NULL AND t.`r_grade`="%s" and s.id!="13"  group by s.id  ORDER BY t.isrecommend,t.sortid';
        //         $recommendrs = M()->query($sql,$gradeid);
        //         $modules = array_column($rs,'moduleid');
        //         foreach($recommendrs as $key=>$value){
        //             if(($key+1+$rcount)>$num){
        //                 break;
        //             }
        //             if(!in_array($value["moduleid"],$modules)){
        //                 array_push($rs, $value);
        //             }
                    
        //         }
        //     }
        // }   
        // if(empty($rs)){
        //     $ret["count"] = 0;
        // }else{
        //     $ret["count"] = count($rs);
        // }
        // $ret["data"] = $rs;
        // // if($callback != '0'){
        // //     $this->ajaxReturn($rs,'JSONP');
        // // }else{
        // //     $this->ajaxReturn($rs,'JSON');
        // // }
        // if(empty($callback)){
        //     echo json_encode($rs);
        // }else{
        //     echo $callback, '(', json_encode($rs), ')';
        // }
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $callback = I("callback");
        $modules = getmodule();
        $ret = array();
        foreach($modules as $key=>$value){
            $temp["id"] = $value["id"];
            $temp["title"] = $value["title"];
            $temp["style"] = $value["style"];
            $ret[] = $temp;
        }
        if(empty($callback)){
            echo json_encode($ret);
        }else{
            echo $callback, '(', json_encode($ret), ')';
        }
    }

    //登封市口算卡排名获取排名
    
    public function getRank(){
    	header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        //person class school
        $by = I("by/s","yjt_areacode");
        $group = I("group/s","yjt_schoolid");
        $callback = I("callback");
        $value = I("value/s");
        $gradeid = I("gradeid/s","");
        if(empty($value)){
            exit("param value is not exist");
        }
        $starttime = I("starttime",Date("Y-m-d H:i:s"));
        $endtime = I("endtime",Date("Y-m-d H:i:s"));
        $start = I("start",0);
        $num = I("num",10);
        // $sql = "select max(yjt_areacode) as areacode,max(yjt_areaname) as areaname,max(yjt_schoolid) as schoolid,max(yjt_schoolname) as schoolname,max(yjt_classid) as classid,max(yjt_classname) as classname,max(username) as username,max(yjt_truename) as truename,sum(IF(score<0,0,score)) as ub,sum(spendtime) as spendtime,count(distinct username) as usernamenum  from db_math.sx_df_user_stage where ".$by." = '".$value."' and addtime>='".$starttime."' and addtime<='".$endtime."'";
        $sql = "select yjt_areacode as areacode,yjt_areaname as areaname,yjt_schoolid as schoolid,yjt_schoolname as schoolname,yjt_classid as classid,yjt_classname as classname,username as username,yjt_truename as truename,sum(IF(score<0,0,score)) as ub,sum(spendtime) as spendtime,count(distinct username) as usernamenum  from db_math.sx_df_user_stage where ".$by." = '".$value."' and addtime>='".$starttime."' and addtime<='".$endtime."'";
        if(!empty($gradeid)){
            $sql = $sql." and yjt_gradeid = '".$gradeid."'";
        }
        $sql = $sql." group by ".$group." order by ub desc,spendtime asc limit ".$start.",".$num;
        // echo $sql;exit;
        // $rs = M()->query($sql);
        if(empty($callback)){
            echo json_encode($rs);
        }else{
            echo $callback, '(', json_encode($rs), ')';
        }
    }
    
     public function getPersonRank(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $callback = I("callback");
        $username = I("username/s","");
        $by = I("by","yjt_areacode");
        $value = I("value/s","1.1.19");
        $gradeid = I("gradeid/s","0");
        if($gradeid != "0"){
            $by2 = " and gradeid='".$gradeid."'";
        }
	    $starttime = I("starttime/s",Date("Y-m-d H:i:s"));
	    $endtime = I("endtime/s",Date("Y-m-d H:i:s"));
        $sql = "select sum(IF(score<0,0,score)) as score from db_math.sx_df_user_stage where username='".$username."' and addtime>='".$starttime."' and addtime<='".$endtime."'";
       
        // $myrs = M()->query($sql);
        $myub = 0;
        if(!empty($myrs)&&!empty($myrs[0]["score"])){
            $myub = $myrs[0]["score"];
        }
        // echo $sql;exit;
        $sql = "select username,sum(IF(score<0,0,score)) as score,SUM(spendtime) AS spendtime from db_math.sx_df_user_stage where ".$by."='".$value."' ".$by2." and addtime>='".$starttime."' and addtime<='".$endtime."'  group by username having score>".$myub ." ORDER BY spendtime asc";
        // echo $sql;exit;
	    // $rankrs = M()->query($sql);
        $rank = 0;
        if(!empty($rankrs)){
            $rank = count($rankrs);
        }
        
        $sql = "select count(distinct yjt_schoolid) as schoolnum,count(distinct username) as usernum,count(distinct yjt_classid) as classnum from db_math.sx_df_user_stage where ".$by."='".$value."' and addtime>='".$starttime."' and addtime<='".$endtime."'";
        // $totalrs = M()->query($sql);
        $ret["rank"] = $rank;
        $ret["schoolnum"] = 0;
        $ret["classnum"] = 0;
        $ret["usernum"] = 0;
        $ret["ubnum"] = $myub;
        if(!empty($totalrs)){
            $ret["schoolnum"] = $totalrs[0]["schoolnum"];
            $ret["classnum"] = $totalrs[0]["classnum"];
            $ret["usernum"] = $totalrs[0]["usernum"];
        }
        if(empty($callback)){
            echo json_encode($ret);
        }else{
            echo $callback, '(', json_encode($ret), ')';
        }

    }

    public function getStatData(){
        $username = I("username/s","");
        $localareacode = I("areaCode/s","");
        $modtype = I("modtype/d",2);
        $ks_code = I("ks_code/s","");
        $res_id = I("res_id/s","");
        $handletype = I("handletype/d",0);
        $statModul = M('yjt_student_stat');
        $data['username'] = $username;
        $data['localareacode'] = $localareacode;
        $data['modtype'] = $modtype;
        $data['ks_code'] = $ks_code;
        $data['res_id'] = $res_id;
        if($handletype == 0){
            $result = $statModul->field("id")->where("username='".$username."' and localareacode='".$localareacode."' and modtype=".$modtype." and ks_code='".$ks_code."' and res_id='".$res_id."'")->find();
            if(count($result) == 0){
                $statModul->add($data);
            }
        }
        else{
            if($modtype == 2){
                $result0 = $statModul->where("username='".$username."' and localareacode='".$localareacode."' and modtype= 0 and ks_code='".$ks_code."'")->select();
                $result1 = $statModul->where("username='".$username."' and localareacode='".$localareacode."' and modtype= 1 and ks_code='".$ks_code."'")->select();
                $result[0] = $result0;
                $result[1] = $result1;
            }
            else{
                $result2 = $statModul->where("username='".$username."' and localareacode='".$localareacode."' and modtype=".$modtype." and ks_code='".$ks_code."'")->select();
                $result[0] = $result2;
            }
            //$res["totalnum"] = count($result);
            $this->ajaxReturn($result);
        }
    }


    //测试缓存
    public function testCache(){
        getUserInfo();
    }




    
    //探测英语练列表接口
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
        $serverhost = $_SERVER["SERVER_ADDR"];
        global $appstarttime;
        $ret["LogLevel"] = "INFO";
        $ret["ITFState"] = "OK";
        $ret["ITFCode"] = "20000";
        $ret["msg1"] =  $serverhost."成功";
        $ret["ErrorModule"] = "YYL-WordUnitList";
        $ret["ITFTime"] = number_format(((getMillisecond() - $appstarttime)/1000), 3, ".", "");
        echo json_encode($ret);
    }

    //探测口算卡列表接口
    public function getMathUnitList(){
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
        $unit["subjectid"]=$subjectid;
        $unit["gradeid"]=$gradeid;
        $unit["termid"]=$termid;
        $unit["versionid"]=$versionid;
        $module["moduleid"]=$moduleid;
        $learnways=getmodule($moduleid);
        if(empty($versionid)){
            $configrs = getUserConfig($subjectid,$gradeid,"0","0",$this->user);
            $versionid = $configrs["versionid"];
            $termid = $configrs["termid"];
            $unit["versionid"]=$versionid;
            $unit["termid"]=$termid;
        }
        //静态数据文件缓存
        $cache_key = md5("subjectid".$subjectid."gradeid".$gradeid."termid".$termid."versionid".$versionid."moduleid".$moduleid."unitlist");
        $result = S($cache_key);
        if(!$result) {
            $unitService = new UnitService();
            $result = $unitService ->getBaseUnitList($unit,$module);
            S($cache_key,$result);
        }
        $serverhost = $_SERVER["SERVER_ADDR"];
        global $appstarttime;
        $ret["LogLevel"] = "INFO";
        $ret["ITFState"] = "OK";
        $ret["ITFCode"] = "20000";
        $ret["msg1"] =  $serverhost."成功";
        $ret["ErrorModule"] = "YYL-MathUnitList";
        $ret["ITFTime"] = number_format(((getMillisecond() - $appstarttime)/1000), 3, ".", "");
        echo json_encode($ret);
    }

        public function getUserGradeCode(){
        $username = I("username/s","410113100000205937");
        $userinfo = M("db_math.df_user_stage","sx_")->field("gradeid")->where("username='%d'",$username)->order("id")->find();
        //  echo M("db_math.df_user_stage","sx_")->getLastSql();
        //  var_dump($userinfo);
        if(empty($userinfo)){
            $data["gradeCode"] = "0";
        }
        else{
            $data["gradeCode"] = $userinfo["gradeid"];
        }
        $this->ajaxReturn($data,"jsonp");
    }
//////////////英语课文相关接口
    //通过单元获取课文信息内容,李金峰那边用
    public function getTextsDataByUnit(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        $text=D("text");
        $ks_code=I("ks_code");
        $result=$text->getTextListData($ks_code);
        //获取课文下面所有的音频
        // $mlist=$text->getUnitAudioList($ks_code);
        $ret["data"]=$result;
        // $ret["mp3list"]=$mlist;
        $ret = json_encode($ret,JSON_UNESCAPED_UNICODE);
        // $ret = '{"yingYu": [{"url": "https://h5.czbanbantong.com/xueyingyu/"}]}';
        echo $ret;
        // $this->ajaxReturn($ret,'jsonp');
    }
  
}
