<?php

namespace Home\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class PublicController extends Controller {

    function mymodel() {
        $Model = new \Think\Model();
        return $Model;
    }

    /**
     * 获取用户的最后一次的版本设置
     */
    function get_user_set_version() {
        $sqlquery = M();
        //$rms_dictionary = M('rms_dictionary');
        if (cookie('ut') != '') {
            $userid = cookie('username');
            $areacode = cookie('areacode');
            $sql = 'select gradeid,versionid,termid from engs_config where userid="%s" and areacode = "%s" order by id desc limit 1';
            $user_config = $sqlquery->query($sql,$userid,$areacode);
            if (count($user_config) > 0) {
                foreach ($user_config as $key => $v) {
                    $gradeid = $v["gradeid"];
                    $versionid = $v['versionid'];
                    $termid = $v['termid'];
                }
                $grade = S($gradeid.'grade');
                if(empty($grade)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="grade" and detail_code="%s"';
                    $grade = $sqlquery -> query($sql,$gradeid); 
                    S($gradeid.'grade',$grade);
                }
                $term = S($termid.'term');
                if(empty($term)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="volume" and detail_code="%s"';
                    $term = $sqlquery -> query($sql,$termid);
                    S($termid.'term',$term);
                }
                $version = S($versionid.'version');
                if(empty($version)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="edition" and detail_code="%s"';
                    $version = $sqlquery -> query($sql,$versionid);
                    S($versionid.'version',$version);
                }
                
                $user_version['gradename'] = $grade[0]['detail_name'];
                $user_version['termname'] = $term[0]['detail_name'];
                $user_version['versionname'] = $version[0]['detail_name'];
                $user_version['r_grade'] = $grade[0]['detail_code'];
                $user_version['r_volume'] = $term[0]['detail_code'];
                $user_version['r_version'] = $version[0]['detail_code'];
                //$this -> ajaxReturn($user_version);
            }
            else{
                $user_version['gradename'] = C('CONST_GRADENAME');
                $user_version['termname'] = C('CONST_TERMNAME');
                $user_version['versionname'] = C('CONST_VERSIONNAME');
                $user_version['r_grade'] = C('CONST_GRADEID');
                $user_version['r_volume'] = C('CONST_TERMID');
                $user_version['r_version'] = C('CONST_VERSIONID');
            }
        }
        else{
            if(cookie('temp_termid') != null && cookie('temp_gradeid') !=null && cookie('temp_versionid') != null){
                $gradeid = cookie('temp_gradeid');
                $termid = cookie('temp_termid');
                $versionid = cookie('temp_versionid');
                $grade = S($gradeid.'grade');
                if(empty($grade)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="grade" and detail_code="'.$gradeid.'"';
                    $grade = $sqlquery -> query($sql); 
                    S($gradeid.'grade',$grade);
                }
                $term = S($termid.'term');
                if(empty($term)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="volume" and detail_code="'.$termid.'"';
                    $term = $sqlquery -> query($sql);
                    S($termid.'term',$term);
                }
                $version = S($versionid.'version');
                if(empty($version)){
                    $sql = 'select detail_code,detail_name from engs_rms_dictionary where dictionary_code="edition" and detail_code="'.$versionid.'"';
                    $version = $sqlquery -> query($sql);
                    S($versionid.'version',$version);
                }
                $user_version['gradename'] = $grade[0]['detail_name'];
                $user_version['termname'] = $term[0]['detail_name'];
                $user_version['versionname'] = $version[0]['detail_name'];
                $user_version['versionname'] = $version[0]['detail_name'];
                $user_version['r_grade'] = $grade[0]['detail_code'];
                $user_version['r_volume'] = $term[0]['detail_code'];
                $user_version['r_version'] = $version[0]['detail_code'];            
            }
            else{
                $user_version['gradename'] = C('CONST_GRADENAME');
                $user_version['termname'] = C('CONST_TERMNAME');
                $user_version['versionname'] = C('CONST_VERSIONNAME');
                $user_version['r_grade'] = C('CONST_GRADEID');
                $user_version['r_volume'] = C('CONST_TERMID');
                $user_version['r_version'] = C('CONST_VERSIONID');
            }
        }
        $this -> ajaxReturn($user_version);
    }


    /**
     * 根据年级学期展示可用的版本
     * @param type $gradeid
     */
    public function get_version() {
        $r_grade = I("r_grade/s", 0);
        $r_volume = I("r_volume/s", 0);
   
            $sqlquery = M();
           // $sql = 'select detail_code,detail_name from engs_rms_dictionary a where a.detail_code in(select distinct t.r_version from engs_rms_unit t where t.r_grade ="%s" and t.r_volume = "%s" and t.is_unit = 0) and a.dictionary_code = "edition" order by a.sortid asc';
            $sql = 'select a.id as imgid,a.pic_path,b.detail_code,b.detail_name from engs_version_img a left join engs_rms_dictionary b on a.r_version = b.detail_code where a.r_grade="%s" and a.r_volume="%s" and a.r_subject = "0003" and b.dictionary_code = "edition" order by b.sortid asc ';
            $version_list = $sqlquery->query($sql,$r_grade,$r_volume);      
        //var_dump($version);
        $this->ajaxReturn($version_list);
    }

    /**
     * 保存用户设置的版本信息
     * @param type $gradeid 用户选择的年级
     * @param type $termid  用户选择的学期
     * @param type $versionid 用户选择的版本
     */
    public function save_user_version() {
        $gradeid = I('gradeid/s', 0);
        $termid = I('termid/s', 0);
        $versionid = I('versionid/s', 0);
        $sqlquery = M('config');
 //echo cookie('ut');
            if (cookie('ut')) {    //如果用户已经登录
                $userid = cookie('username');
                $areacode = cookie('areacode');
                $time = time();
                $curltime = date("y-m-d H:i:s", $time);
                $data['gradeid'] = $gradeid;
                $data['termid'] = $termid;
                $data['versionid'] = $versionid;
                $data['userid'] = $userid;
                $data['areacode'] = $areacode;
                $data['configtime'] = $curltime;
                
                $user_set_version = $sqlquery->field('id')->where('userid="%s" and areacode = "%s"',$userid,$areacode)->order('id desc')->limit(1)->select();
                //$sql='select id from engs_config where userid="'.$userid.'" and areacode = "'.$areacode.'" order by id desc';
                
                // echo $user_set_version[0]['id'];
                if(count($user_set_version) > 0){       //该用户已经设置过版本，修改为最新设置信息
                    $sqlquery->where('id='.$user_set_version[0]['id'])->save($data);
                }
                else{

                   $id= $sqlquery->data($data)->add();      //新增
                   echo "id".$id;
                }
            }
    }

    public function verify_c() {
        ob_clean();
        $Verify = new \Think\Verify();
        $Verify->fontSize = 14;
        $Verify->length = 4;
        $Verify->useNoise = false;
        $Verify->useCurve = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 0;
        $Verify->imageH = 0;
        $Verify->reset = false;
        $Verify->entry();
    }

    public function check_verify() {
        $verify = new \Think\Verify();
        $code = I('code/s', 0);
        //echo $code;
        echo "scus".$verify->check($code);
    }

    public function getyjtdomain(){
        S('mapObj',null);
        $mapObj = S('mapObj');
        if(empty($mapObj)){
            $model = M();
            $sql = "select url,url_type,areacode from engs_yjt_url order by areaname";
            $yjtresult = $model -> query($sql);
            foreach ($yjtresult as $key => $value) {
               $mapObj[$value['areacode']][$value['url_type']] = PROTOCOL.'://'.$value['url'];
               $domain = $_SERVER['HTTP_HOST'];
               if($value['url_type'] == 'sso_url'){
                  $domain = $value['url'];
                  if(count(explode('jx', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'jxrrt.cn';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://enhdkt.jxrrt.cn/';
                   }
                   else if(count(explode('yc', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'czbanbantong.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://enyc.czbanbantong.com/';
                   }
                   else if(count(explode('youjiaotong', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'youjiaotong.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://en.youjiaotong.com/';
                   }
                   else if(count(explode('sjyjt123', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'sjyjt123.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://en.sjyjt123.com/';
                   }
                   else if(count(explode('yjt6', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'yjt6.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://en.yjt6.com/';
                   }
                   else if(count(explode('zhhdkt', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'zhhdkt.cn';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://enyd.zhhdkt.cn/';
                   }
                   else if(count(explode('kw', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'czbanbantong.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://enkw.czbanbantong.com/';
                   }
                   else if(count(explode('xm', $domain)) > 1){
                        $mapObj[$value['areacode']]['domain'] = 'czbanbantong.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://enxm.czbanbantong.com/';
                   }
                   else{
                        $mapObj[$value['areacode']]['domain'] = 'czbanbantong.com';
                        $mapObj[$value['areacode']]['eng_url'] = PROTOCOL.'://en.czbanbantong.com';
                   }
               }
               
            }
             
        }
        $this -> ajaxReturn($mapObj);
       // var_dump($mapObj);
    }
    public function get_provice(){
        $yjtprovice = S('yjtprovice');
        if(empty($yjtprovice)){
            $model = M();
            $sql = "select areaname,areacode from engs_yjt_url  group by areacode";
            $yjtprovice = $model -> query($sql);
             S('yjtprovice',$yjtprovice);
        }
        $this -> ajaxReturn($yjtprovice);
    }
    
    
    public function getRoles(){
        $ssourl=I("sso");
        $username=I("username");
        $url=$ssourl."/sso/interface/queryLoginUser.jsp?q=".$username."&timestamp=".time()."&loginUsertype=student&jsoncallback=?";
        $content=get_content($url);
        $content=substr($content,2,strlen($content)-4);
        $json_ret = mb_convert_encoding($content, "UTF-8", "gb2312");//转码
        $result = json_decode($json_ret, true);
        $this->ajaxReturn($result);

    }

    public function loginwww(){
        $ssourl=I("sso");
        cookie("ssourl",$ssourl);
        $username=I("username");
        $inputname=I("inputname");
        $pwd=I("password");
        $pwd=md5($pwd);
        $url=$ssourl.'/sso/verifyAuthInfo?data={"loginUsertype":"student","inputname":"'.$inputname.'","username":"'.$username.'","pwd":"'.$pwd.'","schoolId":"","appFlg":"portal","isPortal":"1","encodeU":"0"}&jsoncallback=?';
        $content=get_content($url);
        $content=substr($content,2,strlen($content)-5);
        $json_ret = mb_convert_encoding($content, "UTF-8", "gb2312");//转码
        $result = json_decode($json_ret, true);
        $ret["authFlg"]=-1;
        if($result!='null'){
            $ret["authFlg"]=$result["authFlg"];
            if(!isset($result["user"])||$result["user"]==''||$result["user"]=='null'||$result["user"]==null){
                $ret["islogin"]=0;
                $ret["msg"]=$result["authInfo"];
            }else{
                $ret["islogin"]=1;
                $ret["msg"]="登录成功";
                cookie("username", $result["user"]["username"]);
                cookie("usertype", $result["user"]["usertype"]);
                cookie("truename", $result["user"]["truename"]);
                cookie("ut", $result["ut"]);
                cookie("schoolId", $result["user"]["school"]["schoolId"]);
                cookie("areacode", $result["user"]["area"]["areaId"]);
                cookie("logintype", "web");
                cookie("engm_ut",cookie('ut'));
                cookie("engm_username",$result["user"]["username"]);
                $username=$result["user"]["username"];
                $now=date('Y-m-d');
                $date=M("userdate");
                $rs=$date->where("username='%s'",$username)->find();
                if(empty($rs)){
                    $data["username"]=$username;
                    $data["curdate"]=$now;
                    $date->add($data);
                    cookie("engm_tips",'0');
                }else{
                    if($rs["curdate"]==$now){
                        cookie("engm_tips",'1');
                    }else{
                        cookie("engm_tips",'0');
                        $date->where("username='%s'",$username)->setField("curdate",$now);
                    }

                }
                cookie("engm_usertype",$result["user"]["usertype"]);
                cookie("engm_areacode",$user['area']['areaId']);
                cookie("engm_truename",$result["user"]["truename"]);
                if (count($result["user"]["schoolClasses"]) > 0)
                {
                    cookie("gradeCode", $result["user"]["schoolClasses"][0]["gradeCode"]);
                    cookie("classId", $result["user"]["schoolClasses"][0]["classId"]);
                    cookie("engm_classid",$result["user"]["schoolClasses"][0]["classId"]);
                }
            }
        }else{
            $ret["msg"]="请联系客服";
        }
        $this->ajaxReturn($ret);
    }


    public function getUserFreeDays(){
        //http://192.168.141.204/sso/interface/queryUserProductPay.jsp?username=41010110043911&productId=YJT_STUDENT_EDUCARD 
        $ssourl=cookie("ssourl");
        $username=cookie("engm_username");
        //{"username":"41010110043911","productId":"YJT_STUDENT_EDUCARD","isOrder":"1","userOrderDate":"2016-06-23","useDisableDate":"2017-06-24","leftDays":"156"} 

        //判断是否过了免费期
        $queryurl=$ssourl."/sso/interface/queryUserProductPay.jsp?username=".$username."&productId=RRT_STU_PAY_EN_WINTER";
        $contents=get_content($queryurl);
        $json_rets = mb_convert_encoding($contents, "UTF-8", "gb2312");//转码
        $results = json_decode($json_rets, true);
        //进行学校的校验如果是那个学校的进行有效期的校验如果不是的话就直接将有限期进行后置
        $school="410101003871";
        if(cookie("schoolId")!=$school){
            $ret["isOrder"]="1";
            $ret["leftDays"]="1000";
            $ret["userOrderDate"]=$results["userOrderDate"];
            $ret["useDisableDate"]=$results["useDisableDate"];
            $ret["productName"]="RRT_STU_PAY_EN_WINTER";
        }else{
            $ret["isOrder"]=$results["isOrder"];
            $ret["leftDays"]=$results["leftDays"];
            $ret["userOrderDate"]=$results["userOrderDate"];
            $ret["useDisableDate"]=$results["useDisableDate"];
            $ret["productName"]="RRT_STU_PAY_EN_WINTER";
        }
        $this->ajaxReturn($ret);
    }
    
    
    public function DateAlert(){
        $username=cookie("engm_username");
        $now=date('Y-m-d');
        $date=M("userdate");
        $rs=$date->where("username='%s'",$username)->find();
        if(empty($rs)){
            $data["username"]=$username;
            $data["curdate"]=$now;
            $date->add($data);
            cookie("engm_tips",'0');
        }else{
            if($rs["curdate"]==$now){
                cookie("engm_tips",'1');
            }else{
                cookie("engm_tips",'0');
                $date->where("username='%s'",$username)->setField("curdate",$now);
            }

        }
    }

}
