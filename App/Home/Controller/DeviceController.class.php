<?php

namespace Home\Controller;

use Think\Controller;

class DeviceController extends Controller {
    //课堂训练获取听力试卷接口地址
    // http://en.youjiaotong.com/device/GetExamList?datatype=0&logintype=1&username=41010100010242&sso=tms.youjiaotong.com&ks_code=00010403040501&studentClassid=147098596789321647&truename=%25E6%25B5%258B%25E8%25AF%2595&areaId=1.1.1&time=0&jsoncallback=jQuery172046385268086212644_1493953782286&_=1493953800802
    // http://en.youjiaotong.com/device/GetExamList?datatype=0&logintype=2&username=4101010000020003&sso=tms.youjiaotong.com&ks_code=00010803040310&truename=%25E6%25B5%258B%25E8%25AF%2595&areaId=1.1.1&time=0&jsoncallback=jQuery172046385268086212644_1493953782286&_=1493953800802
     public function GetExamList() {
        $device_service= PROTOCOL."://".C('device_service');
        $web_domain = PROTOCOL."://". C('web_domain');
        $ks_code = I('ks_code/s', 0);//章节ID
        $logintype = I('logintype/d',0);//访问类型 0-pc，1-授课端，2-手机
        $ssourl = I('sso/s',0); //sso地址
        $jsoncallback = I('jsoncallback/s',0);
        // $ssourl = 'ssohenan.czbanbantong.com';
        // $tmsurl = str_replace('sso', 'tms', $ssourl);
        //echo $tmsurl;exit;
        $tmsarr = get_ip('',$ssourl);
        //var_dump($tmsarr);
        $tmsurl = $tmsarr['tms']['c1'];
        $tmsurl = PROTOCOL.'://'.$tmsurl;
        $datatype = I('datatype/d',0);//0-全部 5-听力训练 6-单词训练
        $username = I('username/s',0);
        $truename = I('truename/s',0);
        if($truename == '0'){
            
            $truename = I('userTrueName/s',0);
        }
        if($username == '0'){
            $username = cookie('username');
        }
        //echo 'truename = '.$truename;exit;
        $studentClassid = I('studentClassid/s',0);
       // echo '$studentClassid'.$studentClassid;
        if($studentClassid == 0){
            //通过教师账号查询他所有的班级
            $classcontent = get_content($tmsurl.'/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName='.$username);
            $classcontent = iconv('gb2312','utf-8//IGNORE',$classcontent);
            $classresult = json_decode($classcontent,true);
            $classArray = $classresult['rtnArray'];
            //var_dump($classArray);
            foreach ($classArray as $key => $value) {
                if ($key == count($classArray) - 1)
                    $studentClassid .= $value['classId'];
                else
                    $studentClassid .= $value['classId'] . ',';
            }
        }
        $model = M();
        // $sql = 'select id,name,date_format(addtime,"%Y-%m-%d") as addtime from engs_exams where ks_code="'.$ks_code.'" and exams_classid=2 and isdel = 1 and state = 4 order by id desc';
        $sql = '(select id,name,date_format(addtime,"%Y-%m-%d") as addtime,"exams" as from_platform  from engs_exams where ks_code="'.$ks_code.'"  and isdel = 1 and state = 4 order by id) union all (select id,name,date_format(addtime,"%Y-%m-%d") as addtime,"word" as from_platform  from engs_question_paper where ks_code="'.$ks_code.'"  and isdel = 1 and state = 2 order by id)';
        //echo $sql;
        $exams = $model->query($sql);
        foreach ($exams as $key => $value){
            // $sql = 'select id from engs_exams_stem where examsid = ' . $value["id"] . ' and stem_type = 1 and isdel = 1 order by sortid,id'; 
            if($value['from_platform'] =="exams"){
                $sql = 'select id from engs_exams_stem where examsid = ' . $value["id"] . '  and isdel = 1 order by sortid,id';
                $paper_info = $model->query($sql, $examsid);
                $paperid_str = '';
                foreach ($paper_info as $paperkey => $papervalue) {
                    if ($paperkey == count($paper_info) - 1)
                        $paperid_str .= $papervalue['id'];
                    else
                        $paperid_str .= $papervalue['id'] . ',';
                }
                if ($paperid_str != ''){
                    $sql = 'select a.id from engs_exams_questions a,engs_exams_stem b  where a.stemid in ( ' . $paperid_str . ') and a.isdel = 1  and a.stemid = b.id order by a.stemid,a.id';
                    $question = $model->query($sql);
                    $exams[$key]['question_sum'] = count($question); //这套卷子下一共有多少道小题
                }
                else{
                    $exams[$key]['question_sum'] = 0;
                }
                 
            }
            else{
                $sql = "select data from engs_question_paper where id = ".$value['id'];
                $question = $model->query($sql);
                $wordlist = $question[0]['data'];

                //$question = json_decode(urldecode($question[0]['data']));
                $wordlist=json_decode(htmlspecialchars_decode($wordlist));
                $wordlist = object_to_array($wordlist);
                $question = $wordlist;
                $exams[$key]['question_sum'] = count($question); //这套卷子下一共有多少道小题
            }
           
            $type = "5";
            $sql = 'select id from engs_device_history where exams_id = ' . $value["id"] . ' and studentclass in('.$studentClassid.') order by id';
            $result_history = $model->query($sql);
            if(count($result_history) > 0){
                $sql = 'select id from engs_device_history_answer where history_id = ' . $result_history[0]["id"];
                $result = $model->query($sql);
            }
            else{
                $result = array();
            }
            if($logintype == 0){    //pc端获取列表
                if(count($result) > 0){
                    $exams[$key]['isfankui'] = true;
                    $exams[$key]['fankuiurl'] = $device_service.'/Device/feedback?from_platform='.$value['from_platform'].'&examsid='.$value["id"].'&username='.$username.'&tms='.$ssourl;
                }
                else{
                    $exams[$key]['isfankui'] = false;
                }
                $exams[$key]['showurl'] = $device_service.'/Device/ShowExamInfoPc?from_platform='.$value['from_platform'].'&examsid='.$value["id"].'';
            }
            else if($logintype == 1){   //授课端获取列表
                if(count($result) > 0){
                    $exams[$key]['isfankui'] = true;
                    $exams[$key]['fankuiurl'] = $device_service.'/Device/feedback?from_platform='.$value['from_platform'].'&examsid='.$value["id"].'&username='.$username.'&studentClassid='.$studentClassid.'&tms='.$ssourl;
                }
                else{
                    $exams[$key]['isfankui'] = false;
                }
                $exams[$key]['showurl'] = $device_service.'/Device/ShowExamInfoTv?from_platform='.$value['from_platform'].'&username='.$username.'&truename='.$truename.'&examsid='.$value["id"].'&studentClassid='.$studentClassid.'&tms='.$ssourl.'&papername='.urlencode($value['name']).'&ks_code='.$ks_code;
            }
            else{
                if(count($result) > 0){     //手机端获取列表
                    $exams[$key]['isfankui'] = true;
                    $exams[$key]['fankuiurl'] = $device_service.'/Device/feedbackMob?from_platform='.$value['from_platform'].'&examsid='.$value["id"].'&username='.$username.'&tms='.$ssourl;
                }
                else{
                    $exams[$key]['isfankui'] = false;
                }
                $exams[$key]['showurl'] = $device_service.'/Device/ShowExamInfoMob?from_platform='.$value['from_platform'].'&examsid='.$value["id"].'';
            }
            $exams[$key]['type'] = $type;
        }
        if($jsoncallback != '0'){
            $this->ajaxReturn($exams,'JSONP');
        }
        else{
            $this->ajaxReturn($exams,'JSON');
        }
        //var_dump($exams);
        unset($exams);
    }
    //pc及授课端查看反馈页面
    public function feedback(){
        $Model =  M();
        $examsid = I("examsid/d",0);
        $username = I("username/s",0);
        $from_platform = I('from_platform/s','exams');
        $studentClassid = I("studentClassid/s",0);
        $ssourl = I("tms/s",0);
        $tmsarr = get_ip('',$ssourl);
        $tmsurl = $tmsarr['tms']['c1'];
        $result = explode('http', $tmsurl);
        if(count($result) <=1){
            $tmsurl = PROTOCOL.'://'.$tmsurl;
        }
        //echo $tmsurl.'/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName='.$username;
        $classcontent = get_content($tmsurl.'/tms/interface/querySchoolClass.jsp?queryType=byUserName&classType=a&userName='.$username);
        $classcontent = iconv('gb2312','utf-8//IGNORE',$classcontent);
        $classresult = json_decode($classcontent,true);
        $classArray = $classresult['rtnArray'];
       // var_dump($classArray);exit;
        $newclassArray = array();
        $i = 0;
        foreach ($classArray as $key => $value) {
            $sql = 'select id from engs_device_history where exams_id = ' . $examsid . ' and studentclass ="'.$value['classId'].'" and start_time is not null  order by id';
            $result = $Model->query($sql);
            if(count($result) > 0){
               $newclassArray[$i] =  $classArray[$key];
               $i++;
            }
        }
        if($studentClassid != 0){
            $def_classId = $studentClassid;
        }
        else{
            $def_classId = $newclassArray[0]['classId'];
        }
        if($from_platform == "exams"){
             $sql = 'select id ,name from engs_exams where id=%d';
        }
        else{
             $sql = 'select id ,name from engs_question_paper where id=%d';
        }
       
        $exams = $Model->query($sql, $examsid);
        $this -> assign('exams',$exams);
        $this -> assign('classInfo',$newclassArray);
        $this -> assign('def_classId',$def_classId);
        $this -> assign('examsid',$examsid);
        $this -> assign('tmsurl',$tmsurl);
        $this -> assign('from_platform',$from_platform);
        $this -> display('feedback');
    }
    
       //手机端查看反馈页面
    public function feedbackMob(){
        $Model =  M();
        $examsid = I("examsid/d",0);
        $callbackURL = I('callbackURL/s',0);
        $username = I("username/s",0);
        $from_platform = I('from_platform/s','exams');
        $ssourl = I("tms/s",0);
        $tmsarr = get_ip('',$ssourl);
        $tmsurl = $tmsarr['tms']['c1'];
        if(count($result) <=1){
            $tmsurl = PROTOCOL.'://'.$tmsurl;
        }
        $classcontent = get_content($tmsurl.'/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName='.$username);
        $classcontent = iconv('gb2312','utf-8//IGNORE',$classcontent);
        $classresult = json_decode($classcontent,true);
        $classArray = $classresult['rtnArray'];
        $newclassArray = array();
        $i = 0;
        foreach ($classArray as $key => $value) {
            $sql = 'select id from engs_device_history where exams_id = ' . $examsid . ' and studentclass ="'.$value['classId'].'"  order by id';
            $result = $Model->query($sql);
            if(count($result) > 0){
               $newclassArray[$i] =  $classArray[$key];
               $i++;
            }
        }
        if($from_platform == "exams"){
             $sql = 'select id ,name from engs_exams where id=%d';
        }
        else{
             $sql = 'select id ,name from engs_question_paper where id=%d';
        }
        $exams = $Model->query($sql, $examsid);
        $this -> assign('exams',$exams);
        $this -> assign('classInfo',$newclassArray);
        $this -> assign('def_classId',$newclassArray[0]['classId']);
        $this -> assign('def_classname',$newclassArray[0]['className']);
        $this -> assign('examsid',$examsid);
        $this -> assign('callbackURL',$callbackURL);
        $this -> assign('tmsurl',$tmsurl);
        $this -> assign('from_platform',$from_platform);
        $this -> display('feedbackmob');
    }
    /*
     * 同步训练PC版展示
     */

    public function ShowExamInfoPC() {
        $examsid = I('examsid/d', 0);
        $from_platform = I('from_platform/s','exams');
        if($examsid == 0){
            $examsid = "89";
        }
        $this->assign('examsid', $examsid);
        $this->assign('from_platform',$from_platform);
        $this->display('showexaminfopc');
    }
  /**
     *判断姓名是否全是中文
     */
   public function isAllChinese($str){
        //新疆等少数民族可能有·
        if(strpos($str,'·')){
            //将·去掉，看看剩下的是不是都是中文
            $str=str_replace("·",'',$str);
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }else{
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }
  }

    //授课端答题器页面
     public function ShowExamInfoTv() {
        $username = I('username');
        $truename = I('truename');
        $truename = urldecode($truename);
        $examsid = I('examsid/d',0);
        $papername = I('papername/s',0);
        $ks_code = I('ks_code/s',0);
        $fplatform_subtype = I('fplatform_subtype/s','0');
        $studentClassid = I('studentClassid/s',0);
        $teachComputerMac = I('teachComputerMac/s','0');
        $ssourl = I("tms/s",0);
        $tmsarr = get_ip('',$ssourl);
        $tmsurl = $tmsarr['tms']['c1'];
        $tqms = $tmsarr['tqms']['title'];
        if(count($result) <=1){
            $tmsurl = PROTOCOL.'://'.$tmsurl;
        }

        $temnum = count(explode('PAPERPEN',$fplatform_subtype));
        //echo strpos($fplatform_subtype,'PAPERPEN');
        if($temnum > 1){
            $smartPaperEnable = '1';

        }
        else{
            $smartPaperEnable = '0'; 
        }
        $loginStyle = I('loginStyle/s',1);
        $from_platform = I('from_platform/s','exams');
        //http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=answerToolByClass&schoolClassId=140705800417075269
       // echo $tmsurl.'/tms/interface/queryStudent.jsp?queryType=answerToolByClass&schoolClassId='.$studentClassid;
        if($from_platform == "exams"){
            $examsinfo = M('exams')->field('name')->where('id=%d',$examsid)->find();  
        }
        else{
            $examsinfo = M('question_paper')->field('name')->where('id=%d',$examsid)->find();
        }
        $papername = $examsinfo['name'];
        $json_ret = get_content($tmsurl.'/tms/interface/queryStudent.jsp?queryType=answerToolByClass&schoolClassId='.$studentClassid.'&teachComputerMac='.$teachComputerMac.'&smartPaperEnable='.$smartPaperEnable);
        //var_dump($json_ret);
        //echo $json_ret;
       // echo $tmsurl.'/tms/interface/queryStudent.jsp?queryType=answerToolByClass&schoolClassId='.$studentClassid.'&teachComputerMac='.$teachComputerMac.'&smartPaperEnable='.$smartPaperEnable;exit;
        $json_ret = iconv('gb2312','utf-8//IGNORE',$json_ret);

        $result = json_decode($json_ret,true);
        // echo $tmsurl.'/tms/interface/queryStudent.jsp?queryType=answerToolByClass&schoolClassId='.$studentClassid.'&teachComputerMac='.$teachComputerMac.'&smartPaperEnable='.$smartPaperEnable;
        // var_dump($json_ret);exit;
        $allowAnswerNopay = "1";
        $studentInfo = $result['rtnArray'];
        $deviceMap = '';
        $userMap = '';
        $noPayMap = array();
        //echo array_key_exists('allowAnswerNopay',$result);exit;
        //欠费是否允许答题属性：private String allowAnswerNopay="1";//学生欠费是否允许答题    1允许 0 不允许
        if(array_key_exists('allowAnswerNopay',$result)){    //班级信息存在该属性
            $allowAnswerNopay = $result['allowAnswerNopay'];
           // $allowAnswerNopay = "0";
            foreach($studentInfo as $key => $value){
                if($this->isAllChinese($value['realname'])){
                    $value['realname'] = mb_substr($value['realname'],0,4,'utf-8');
                }
                else{
                    $value['realname'] = mb_substr($value['realname'],0,8,'utf-8');
                }
                
                $userMap .= '"'.$value['studentNumber'].'":"'.$value['realname'].'",';
                if($allowAnswerNopay == "0"){  //学生欠费不允许答题
                    if($value['noPay'] == "0"){   //学生不欠费
                        $deviceMap .= '"'.$value['smartPaperMac'].'":"'.$value['studentNumber'].'",';
                        $deviceMap_tool .= '"'.$value['answerToolCode'].'":"'.$value['studentNumber'].'",';
                    }
                    else{
                        if($value['answerToolCode'] != ""){
                          array_push($noPayMap,$value);  
                        }
                        
                    }
                }
                else{
                    $deviceMap .= '"'.$value['smartPaperMac'].'":"'.$value['studentNumber'].'",';
                    $deviceMap_tool .= '"'.$value['answerToolCode'].'":"'.$value['studentNumber'].'",';
                }
                if($this->isAllChinese($value['realname'])){
                    $studentInfo[$key]['realname'] = mb_substr($value['realname'],0,4,'utf-8');
                }
                else{
                    $studentInfo[$key]['realname'] = mb_substr($value['realname'],0,8,'utf-8');
                }
                     
            }
        }
        else{
            foreach($studentInfo as $key => $value){ 
                $userMap .= '"'.$value['studentNumber'].'":"'.$value['realname'].'",';
                $deviceMap .= '"'.$value['smartPaperMac'].'":"'.$value['studentNumber'].'",';
                $deviceMap_tool .= '"'.$value['answerToolCode'].'":"'.$value['studentNumber'].'",';
                if($this->isAllChinese($value['realname'])){
                    $studentInfo[$key]['realname'] = mb_substr($value['realname'],0,4,'utf-8');
                }
                else{
                    $studentInfo[$key]['realname'] = mb_substr($value['realname'],0,8,'utf-8');
                }     
            }
        }
        //var_dump($noPayMap);exit;
        $deviceMap = substr($deviceMap,0,strlen($deviceMap)-1);
        $deviceMap_tool = substr($deviceMap_tool,0,strlen($deviceMap_tool)-1); 
        $userMap = substr($userMap,0,strlen($userMap)-1);
        $result = explode('http://', $tmsurl);
        if(count($result) > 1){
            $tmsurl = $result[1];
        }
       
        $this->assign('studentInfo', $studentInfo);
        $this->assign('deviceMap', $deviceMap);
        $this->assign('deviceMap_tool', $deviceMap_tool);
        $this->assign('userMap', $userMap);
        $this->assign('allowAnswerNopay',$allowAnswerNopay);
        $this->assign('noPayMap', $noPayMap);
        $this->assign('noPayMapCount',count($noPayMap));
        $this->assign('sum_student', count($studentInfo));
        $this->assign('examsid', $examsid);
        $this->assign('studentClassid', $studentClassid);
        $this->assign('username',$username);
        $this->assign('truename',$truename);
        $this->assign('papername',$papername);
        $this->assign('tqms',$tqms);
        $this->assign('ks_code',$ks_code);
        $this->assign('loginStyle',$loginStyle);
        $this->assign('from_platform',$from_platform);
        $device_conf = M();
        $sql = "select config_value,pageinterval from engs_device_config where config_type='type_change_page' and username='".$username."' and studentclassid='".$studentClassid."' order by id desc";
        $result = $device_conf -> query($sql);
        $cp_manual_uoflag = "false";
        $cp_submit_uoflag = "false";
        $cp_interval_uoflag = "false";
        $Intervalflag1 = "false";
        $Intervalflag2 = "false";
        $Intervalflag3 = "false";
        $Intervalflag4 = "false";
        $Intervalflag5 = "false";
        $Intervalflag6 = "false";
        $Intervalflag7 = "false";
        $Intervalflag8 = "false";
        
        if(count($result) > 0){
            $change_page_set = $result[0]['config_value'];
            $pageInterval = $result[0]['pageinterval'];
            if($change_page_set == 'option_cp_manual'){
                $cp_manual_uoflag = "true";
            }
            else if($change_page_set == 'option_cp_submit'){
                $cp_submit_uoflag = "true";
            }
            else{
                $cp_interval_uoflag = "true";
            }
            if($pageInterval == '0.5'){
                $Intervalflag1 = "true";
            }
            else if($pageInterval == '1'){
                $Intervalflag2 = "true";
            }
            else if($pageInterval == '1.5'){
                $Intervalflag3 = "true";
            }
            else if($pageInterval == '2'){
                $Intervalflag4 = "true";
            }
            else if($pageInterval == '2.5'){
                $Intervalflag5 = "true";
            }
            else if($pageInterval == '3'){
                $Intervalflag6 = "true";
            }
            else if($pageInterval == '4'){
                $Intervalflag7 = "true";
            }
            else if($pageInterval == '5'){
                $Intervalflag8 = "true";
            }
        }
        else{
            $change_page_set = "0";
            $pageInterval = "0";
        }
        $sql = "select config_value from engs_device_config where config_type='type_result_show' and username='".$username."' and studentclassid='".$studentClassid."' order by id desc";
        $result = $device_conf -> query($sql);
        $rs_detail_uoflag = "false";
        $rs_simple_uoflag = "false";
        if(count($result) > 0){
            $result_show_set = $result[0]['config_value'];
            if($result_show_set == 'option_rs_detail'){
                $rs_detail_uoflag = "true";
            }
            else if($result_show_set == 'option_rs_simple'){
                $rs_simple_uoflag = "true";
            }
        }
        else{
            $result_show_set = "0";
        }

        $this->assign('cp_manual_uoflag',$cp_manual_uoflag);
        $this->assign('cp_submit_uoflag',$cp_submit_uoflag);
        $this->assign('cp_interval_uoflag',$cp_interval_uoflag);
        $this->assign('rs_detail_uoflag',$rs_detail_uoflag);
        $this->assign('rs_simple_uoflag',$rs_simple_uoflag);
        $this->assign('Intervalflag1',$Intervalflag1);
        $this->assign('Intervalflag2',$Intervalflag2);
        $this->assign('Intervalflag3',$Intervalflag3);
        $this->assign('Intervalflag4',$Intervalflag4);
        $this->assign('Intervalflag5',$Intervalflag5);
        $this->assign('Intervalflag6',$Intervalflag6);
        $this->assign('Intervalflag7',$Intervalflag7);
        $this->assign('Intervalflag8',$Intervalflag8);
        $this->display('showexaminfotv');
    }
    //手机端预览页面
    public function ShowExamInfoMob(){
        $Model =  M();
        $examsid = I("examsid/d",0);
        $callbackURL = I('callbackURL/s',0);
        $from_platform = I('from_platform/s','exams');
        if($from_platform == "exams"){
            $sql = 'select id ,name from engs_exams where id=%d';
        }
        else{
            $sql = 'select id ,name from engs_question_paper where id=%d';
        }
        
        $exams = $Model->query($sql, $examsid);
        $this -> assign('exams',$exams);
        $this -> assign('examsid',$examsid);
        $this -> assign('from_platform',$from_platform);
        $this -> assign('callbackURL',$callbackURL);
        $this -> display('showexaminfomob');
    }
    /*
     * 授课端课堂训练试卷详细信息
     */
    public function ListeShowTv() {
        $examsid = I('examsid/d', 0);
        $studentClassid = I('studentClassid/s',0);
        $sum_student = I('sum_student/s',0);
        $from_platform = I('from_platform/s','exams');
        if($from_platform == '0');
        {
            $from_platform = "exams";
        }
        $classtotalscore = 0;
        $newquearray = array();
        $tempkey = 0;
        $model = M();
        //$examsid = 384;
        $sql = 'select id from engs_device_history where exams_id='.$examsid.'  and studentclass="'.$studentClassid.'"';
        $device_history = $model->query($sql); //查询这个班级对这套试卷的历史答题记录    
            $sqlquery = M();
        if($from_platform == "exams"){
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $stem_type = $value['stem_type'];
                $papertitle = $value['content'];
                $question_playtimes = $value['question_playtimes'];
                $question_score = $value['question_score'];
                if($stem_type == '1'){          //独立大题
                        $score = 0;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql);
              
                        foreach ($question as $quekey => $quevalue) { 
                    
                            $question[$quekey]['papertitle'] = $papertitle; 
                            $question[$quekey]['question_playtimes'] = $question_playtimes;
                            $question[$quekey]['question_score'] = $question_score;
                            $question[$quekey]['stem_type'] = '1';
                            $sql = 'select a.title from engs_dictionary a,engs_exams_questions_keys b where a.id=b.dictionary_id and b.dictionary_code="que_key4" and b.questionsid = ' . $quevalue['id'] . ' and b.isdel = 1 order by b.id desc';
                            $que_key4 = $model->query($sql);        //难易度
                            $question[$quekey]['que_key4'] = $que_key4[0]['title'];
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql);
                                $sql = 'select flag from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_options = $model->query($sql);
                                if($quevalue['typeid'] == '1'){     //选择题
                                    $sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                    $question[$quekey]['isObjective'] = true;  //是否接收答题器信号
                                }
                                else{           //排序题
                                    $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                     $question[$quekey]['isObjective'] = false; //是否接收答题器信号
                                }
                                $queanswer = $sqlquery -> query($sql);
                            }
                            else{           //填空题
                                if($quevalue['typeid'] == '2'){
                                    $question[$quekey]['isObjective'] = false;  //是否接收答题器信号
                                    $que_items = '0';
                                    $que_options = '0';
                                }
                                else{   //判断题
                                    $question[$quekey]['isObjective'] = true;   //是否接收答题器信号
                                    $que_options = array();
                                    $que_options[0]['flag'] = '0';
                                    $que_options[1]['flag'] = '1';
                                    $que_items = array();
                                    $que_items[0]['flag'] = '0';
                                    $que_items[1]['flag'] = '1';
                                }
                                 $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                                
                            }
                            foreach ($que_items as $que_items_key => $que_items_value) {    //查询每个选项有几个作答
                                if(count($device_history) > 0){
                                    $sql = 'select id from engs_device_history_answer where history_id = '.$device_history[0]['id'].' and answer = "'.$que_items_value['flag'].'" and question_id='.$quevalue['id'];
                                    $result =  $model -> query($sql);
                                    $que_items[$que_items_key]['reply_num'] = count($result);
                                }
                                else{
                                    $que_items[$que_items_key]['reply_num'] = 0;
                                }
                            }
                            $question[$quekey]['items'] = $que_items;
                            $question[$quekey]['itemsoption'] = $que_options;
                            $question[$quekey]['answer'] = $queanswer;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $mp3str = '';
                            foreach ($que_tts_noqn as $keymp3 => $quemp3) {
                                $mp3str .= $quemp3['tts_mp3'] . '|';
                            }
                            $question[$quekey]["mp3"] = $mp3str;
                            $question[$quekey]['tts'] = $que_tts_noqn;
                            if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_status !="0" order by id';
                                    $result = $model->query($sql);
                                    $anMpaHis = '';
                                    if(count($result) > 0){
                                        foreach($result as $hikey => $hivalue){ 
                                             $data1['qusetion_id'] = $hivalue['question_id'];
                                             $data1['answer'] = $hivalue['answer'];
                                             $data1['answer_result'] = $hivalue['answer_result'];
                                             $data1['anwser_status'] = $hivalue['answer_status'];
                                             $data2[$hivalue['std_id']] = $data1;     
                                        }
                                        $question[$quekey]["anMapsHis"] = $data2;
                                    }
                                    else{
                                        $question[$quekey]["anMapsHis"] = "{}";
                                    }
                                if($sum_student != 0){
                                    $totalscore = 0;
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" and answer_status !="0" order by id';
                                    $result_100 = $model->query($sql);
                                    if(count($result_100) > 0){
                                        $accuracy = count($result_100)/count($result)*100;
                                        foreach ($result_100 as $avekey => $aveval) {
                                            $totalscore = $totalscore+$aveval['scores'];
                                            $classtotalscore = $classtotalscore+$aveval['scores'];
                                        }
                                        $classaverage = $totalscore/count($result);

                                    }
                                    else{
                                        $classaverage = 0;
                                        $accuracy = 0;
                                    }
                                    $question[$quekey]['accuracy'] = $accuracy;  //这个班级对这个小题回答的正确率
                                    $question[$quekey]['average'] = $classaverage;  //这个班级对这个小题回答的平均分
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="99" and answer_status = "1" order by id';
                                    $result = $model->query($sql);
                                    $question[$quekey]['invalid_num']  = count($result);
                                } 
                            }
                            else{       //没有历史答题记录
                                $question[$quekey]["anMapsHis"] = "{}";
                                $question[$quekey]['accuracy'] = 0;  //这个班级对这个小题回答的正确率
                                $question[$quekey]['average'] = 0;  //这个班级对这个小题回答的平均分
                            }
                            
                             $newquearray[$tempkey] = $question[$quekey];
                             $newquearray[$tempkey]['mp3type'] =4;
                             $newquearray[$tempkey]['from_platform'] = 'exams';
                             $tempkey++;
                        }  
                        
                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $stem_children[$keychild]['stem_type'] = '2';
                        $stem_children[$keychild]['papertitle'] = $value['content'];
                        $stem_children[$keychild]['question_score'] = $childvalue['question_score'];
                        $stem_children[$keychild]['tips'] = 1;
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                        $mp3str = '';
                            foreach ($child_stem_tts as $ckeymp3 => $cquemp3) {
                                $mp3str .= $cquemp3['tts_mp3'] . '|';
                            }
                        $stem_children[$keychild]['mp3'] = $mp3str;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {   
                        $newquearray[$tempkey]["stem_type"] = 2; 
                        
                        $newquearray[$tempkey]["content"] = $childvalue['content'];
                        $newquearray[$tempkey]["papertitle"] = $value['content'];                   
                            if($child_quevalue['typeid'] == '1' || $child_quevalue['typeid'] == '4'){       //如果是选择题或排序题就获取选项
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $child_quevalue['id'] . ' and isdel = 1 order by sortid,id';
                                $child_que_items = $sqlquery->query($sql);
                                $child_question[$child_quekey]['items'] = $child_que_items;
                                if($child_quevalue['typeid'] == '1'){
                                    $sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' and a.questionsid= ' . $child_quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                }
                                else{
                                    $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'].' and isdel=1 order by sortid,id';     //问题的答案
                                }
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                            }
                            else{
                                $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'].' and isdel=1 order by sortid,id';     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                               $child_question[$child_quekey]['items'] = 0;
                            }
                             $child_question[$child_quekey]['answer'] = $child_queanswer;
                             $stem_children[$keychild]['questypeid'] = $child_quevalue['typeid'];
                             $stemdata[$key]['questypeid'] = $child_quevalue['typeid'];
                             if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_status !="0"  order by id';
                                    $result = $model->query($sql);
                                    $anMpaHis = '';
                                    if(count($result) > 0){
                                        foreach($result as $hikey => $hivalue){ 
                                             $data1['qusetion_id'] = $hivalue['question_id'];
                                             $data1['answer'] = $hivalue['answer'];
                                             $data1['answer_result'] = $hivalue['answer_result'];
                                             $data1['anwser_status'] = $hivalue['answer_status'];
                                             $data2[$hivalue['std_id']] = $data1;     
                                        }
                                        $newquearray[$tempkey]["anMapsHis"] = $data2;
                                    }
                                    else{
                                        $newquearray[$tempkey]["anMapsHis"] = "{}";
                                    }
                                if($sum_student != 0){
                                    //echo "ss";exit;
                                    $totalscore = 0;
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" and answer_status !="0" order by id';
                                    $result_100 = $model->query($sql);
                                    if(count($result_100) > 0){
                                        $accuracy = count($result_100)/count($result)*100;
                                        foreach ($result_100 as $avekey => $aveval) {
                                            $totalscore = $totalscore+$aveval['scores'];
                                            $classtotalscore = $classtotalscore+$aveval['scores'];
                                        }
                                        $classaverage = $totalscore/count($result);

                                    }
                                    else{
                                        $classaverage = 0;
                                        $accuracy = 0;
                                    }
                                    $newquearray[$tempkey]['accuracy'] = $accuracy;  //这个班级对这个小题回答的正确率
                                    $newquearray[$tempkey]['average'] = $classaverage;  //这个班级对这个小题回答的平均分
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="99" and answer_status = "1" order by id';
                                    $result = $model->query($sql);
                                    $newquearray[$tempkey]['invalid_num']  = count($result);
                                } 
                            }
                            else{       //没有历史答题记录
                                $newquearray[$tempkey]["anMapsHis"] = "{}";
                                $newquearray[$tempkey]['accuracy'] = 0;  //这个班级对这个小题回答的正确率
                                $newquearray[$tempkey]['average'] = 0;  //这个班级对这个小题回答的平均分
                            }
                           
                            
                            $newquearray[$tempkey]['mp3'] =$mp3str;
                            $newquearray[$tempkey]['tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                            $newquearray[$tempkey]['mp3type'] =4;
                            $newquearray[$tempkey]['from_platform'] = 'exams';
                            $newquearray[$tempkey]['isObjective']= false;
                            $newquearray[$tempkey]["question"] = $child_question[$child_quekey];
                            $tempkey++;
                        }

                        
                        //var_dump($stem_children[0]['question']);exit;
                       
                        
                        
                        

                       
                    }
                    
                }
            }             
      
        }
        else{
            $sql = "select data from engs_question_paper where id = ".$examsid;
            $question = $model->query($sql);
            $wordlist = $question[0]['data'];

            //$question = json_decode(urldecode($question[0]['data']));
            $wordlist=json_decode(htmlspecialchars_decode($wordlist));
            $wordlist = object_to_array($wordlist);
            //$question = $wordlist;
            //var_dump($wordlist);exit;
            foreach ($wordlist as $key => $value) {
                $newquearray[$key]['id'] = $value['id'];
                $newquearray[$key]['stemid'] = "0";
                $newquearray[$key]['question_num'] = $key+1;

                $newquearray[$key]['typeid'] = "1";
              
                $newquearray[$key]['itemtype'] =$value['itemtype'];
                $newquearray[$key]['display'] = "1";
                $newquearray[$key]['tips'] = "0";
                $newquearray[$key]['stoptimes'] = "0";
                $newquearray[$key]['mp3type'] = $value['stemcontenttype'];
                if($value['stemcontenttype'] == 1){
                    $newquearray[$key]['papertitle'] = "单词训练-请根据图片选择正确选项";
                    $newquearray[$key]['tcontent'] = '<img src="'.PROTOCOL."://".C('word_pic_path').$value['stemcontent'].'" />';
                }
                else{
                    $newquearray[$key]['papertitle'] = "单词训练-请根据听到的内容选择正确选项";
                    $newquearray[$key]['tcontent'] ='';
                }

                $newquearray[$key]['acontent'] = "";
                $newquearray[$key]['question_playtimes'] = "1";
                $newquearray[$key]['question_score'] = "1";
                $newquearray[$key]['stem_type'] = "1";
                $newquearray[$key]['que_key4'] = "容易";
                $newquearray[$key]['isObjective'] = false;
                $newquearray[$key]['items'] = $value['items'];
                $newquearray[$key]['itemsoption'] = $value['items'];
                $answer['answer'] = $value['answer'];
                $answer['answer_num'] = "0";
                $answer['questionsid'] = $value['id'];
                $newquearray[$key]['answer'][0] = $answer;
                $newquearray[$key]['mp3'] = $value['mp3'].'|';
               
                $tts['id']='0';
                $tts['tts_type']='0';
                $tts['parentid']='0';
                $tts['flag_content']='';
                $tts['tts_content']=$value['tts'];
                $tts['tts_stoptime']='2';
                $tts['tts_mp3']=$value['mp3'];
                $tts['st_flag']='1';
                $newquearray[$key]['tts'][0] = $tts;
                if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                    $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $value['id'] . ' and history_id = '.$device_history[0]['id'].'  order by id';
                        $result = $model->query($sql);
                        $anMpaHis = '';
                        if(count($result) > 0){
                            foreach($result as $hikey => $hivalue){ 
                                 $data1['qusetion_id'] = $hivalue['question_id'];
                                 $data1['answer'] = $hivalue['answer'];
                                 $data1['answer_result'] = $hivalue['answer_result'];
                                 $data1['anwser_status'] = $hivalue['answer_status'];
                                 $data2[$hivalue['std_id']] = $data1;     
                            }
                            $newquearray[$key]["anMapsHis"] = $data2;
                        }
                        else{
                            $newquearray[$key]["anMapsHis"] = "{}";
                        }
                    if($sum_student != 0){
                        $totalscore = 0;
                        $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $value['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" order by id';
                        $result_100 = $model->query($sql);
                        if(count($result_100) > 0){
                            $accuracy = count($result_100)/count($result)*100;
                            foreach ($result_100 as $avekey => $aveval) {
                                $totalscore = $totalscore+$aveval['scores'];
                                $classtotalscore = $classtotalscore+$aveval['scores'];
                            }
                            $classaverage = $totalscore/count($result);

                        }
                        else{
                            $classaverage = 0;
                            $accuracy = 0;
                        }
                        $newquearray[$key]['accuracy'] = $accuracy;  //这个班级对这个小题回答的正确率
                        $newquearray[$key]['average'] = $classaverage;  //这个班级对这个小题回答的平均分
                        $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $value['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="99" and answer_status = "1" order by id';
                        $result = $model->query($sql);
                        $newquearray[$key]['invalid_num']  = count($result);
                    } 
                }
                else{       //没有历史答题记录
                    $newquearray[$key]["anMapsHis"] = "{}";
                    $newquearray[$key]['accuracy'] = 0;  //这个班级对这个小题回答的正确率
                    $newquearray[$key]['average'] = 0;  //这个班级对这个小题回答的平均分
                }
                $newquearray[$key]['from_platform'] = 'word';

            }
            //var_dump($newquearray);exit;
        }
        if(count($device_history) > 0){
            $sql = 'select distinct std_id from engs_device_history_answer where history_id = '.$device_history[0]['id'].'  order by id';
            $result = $model->query($sql);
            $newquearray[0]['classaverage'] = $classtotalscore/count($result); 
        }
        else{
            $newquearray[0]['classaverage'] = 0; 
        }
        
       $this -> ajaxReturn($newquearray);
       unset($examsid);
       unset($exams_tts_type);
       unset($stemdata);
       unset($stem_tts);
       unset($question);
       unset($que_items);
       unset($que_tts);
       unset($que_tts_noqn);
       unset($stem_children);
       unset($child_question);
       unset($unitid);
       unset($child_que_items);
       unset($child_stem_tts);
       unset($unitid);
       unset($newquearray);
       unset($sql);
        //$this -> assign('examsdata',$examsdata);
       // $this -> display();

    }
       /*
     * 获取授课端历史作答信息
     */
       public function clearanMapsHis(){
            $examsid = I('examsid/d', 0);
            $queid = I('queid/d', 0);
            $studentClassid = I('studentClassid/s',0);
            $from_platform = I("from_platform/s",'exams');
            if($from_platform == '0'){
                $from_platform = 'exams';
            }
            $model = M();
            $sql = 'select id from engs_device_history where exams_id='.$examsid.' and studentclass="'.$studentClassid.'"';
           // echo $sql;exit;
            $device_history = $model->query($sql); //查询这个班级对这套试卷的历史答题记录
            $history_answer = M('device_history_answer');
            $data['answer_status'] = '3';
             if(count($device_history) > 0){
                $history_answer->where('question_id = ' . $queid . ' and history_id = '.$device_history[0]['id'])->save($data);
               // echo M()->getLastSql();
             }
       }
    /*
     * 获取授课端历史作答信息
     */
    public function getanMapsHis(){
        $examsid = I('examsid/d', 0);
        $queid = I('queid/d', 0);
        $studentClassid = I('studentClassid/s',0);
        $from_platform = I("from_platform/s",'exams');
        if($from_platform == '0'){
            $from_platform = 'exams';
        }
        $model = M();
        $sql = 'select id from engs_device_history where exams_id='.$examsid.' and studentclass="'.$studentClassid.'"';
       // echo $sql;exit;
        $device_history = $model->query($sql); //查询这个班级对这套试卷的历史答题记录
         if(count($device_history) > 0){
            $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $queid . ' and history_id = '.$device_history[0]['id'].' and answer_status !="3"  order by id';
                    $result = $model->query($sql);
                    $anMpaHis = '';
                    if(count($result) > 0){
                        foreach($result as $key => $value){ 
                             $data1['qusetion_id'] = $value['question_id'];
                             $data1['answer'] = $value['answer'];
                             $data1['answer_result'] = $value['answer_result'];
                             $data1['anwser_status'] = $value['answer_status'];
                             $data2[$value['std_id']] = $data1;     
                        }
                        
                    }
                    else{
                        $data2 = (object)null;
                    }
         }
         else{
                    $data2 = (object)null;
         }
         $this->ajaxReturn($data2);
    }

    /*
     * 授课端课堂训练套卷预览信息
     */
    public function ListeShowTvyulan() {
        $examsid = I('examsid/d', 0);
        $studentClassid = I('studentClassid/s',0);
        $sum_student = I('sum_student/s',0);
        $from_platform = I('from_platform/s','exams');
        $classtotalscore = 0;
        $newquearray = array();
        $tempkey = 0;
        if($from_platform == '0'){
            $from_platform = 'exams';
        }
        $model = M();
        $sql = 'select id from engs_device_history where exams_id='.$examsid.' and studentclass="'.$studentClassid.'"';
        $device_history = $model->query($sql); //查询这个班级对这套试卷的历史答题记录    
        $sqlquery = M();
        $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) { 
                           
                            if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,answer_result from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_status !="0"  order by id';
                                    $result1 = $model->query($sql);
                                    $sql = 'select std_id,answer_result from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" and answer_status !="0"  order by id';
                                    $result2 = $model->query($sql);
                                    $question[$quekey]["replaynum"] = count($result1);
                                    $question[$quekey]["replay_right_num"] = count($result2);


                            }
                            else{       //没有历史答题记录
                                $question[$quekey]["replaynum"] = 0;
                                $question[$quekey]["replay_right_num"] = 0;
                            }
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';

                            $que_tts = $model -> query($sql);

                            $question[$quekey]["tts"] = $que_tts;
                            $newquearray[$tempkey] = $question[$quekey];
                            $tempkey++;
                        }  
                        
                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and typeid in(1,3) and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {
                            if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,answer_result from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_status !="0"  order by id';
                                    $result1 = $model->query($sql);
                                    $sql = 'select std_id,answer_result from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" and answer_status !="0"  order by id';
                                    $result2 = $model->query($sql);
                                    $child_question[$child_quekey]["replaynum"] = count($result1);
                                    $child_question[$child_quekey]["replay_right_num"] = count($result2);


                            }
                            else{       //没有历史答题记录
                                $child_question[$child_quekey]["replaynum"] = 0;
                                $child_question[$child_quekey]["replay_right_num"] = 0;
                            }
                            $child_question[$child_quekey]["tts"] = $stem_children[$keychild]['tts'];
                            $newquearray[$tempkey] = $child_question[$child_quekey];
                            $tempkey++;
                        }
                        // $stem_children[$keychild]["replaynum"] = 0;
                        // $stem_children[$keychild]["replay_right_num"] = 0;
                        // $newquearray[$tempkey] = $stem_children[$keychild];
                        
                    }
                    
                }
            }
            //var_dump($newquearray);
        $this->ajaxReturn($newquearray);
    }
    public function saveAnswerUrl(){
        $device_history = M('device_history');  //历史记录主表
        $device_history_answer = M('device_history_answer');    //历史记录从表
        $Model =  M();
        $str = urldecode(I("json"));
        $str = htmlspecialchars_decode($str);
        //$str = '{"examsid":"121", "question_id":"1786", "studentClassid":"140687834519428108", "localAreaCode":"25.", "anMaps":{"41010110007335":{"question_id":"1786", "answer":"A", "answer_result":"99", "answer_status":"2"}, "41010110012983":{"question_id":"1786", "answer":"C", "answer_result":"99", "answer_status":"2"}}}';

       $json = json_decode($str,true);
       //echo json_last_error();
        $examsid = $json['examsid'];
       
        $question_id = $json['question_id'];
        $studentClassid = $json['studentClassid'];
        $questionScore = $json['questionScore'];
        $from_platform = $json['from_platform'];
        $tqms = $json['tqms'];
        $anMaps = $json['anMaps'];
        $keys = array_keys($anMaps);
       // var_dump($anMaps);
        $time = time();
        $curltime = date("y-m-d H:i:s", $time);
        $data_history['studentclass'] = $studentClassid;
        $data_history['exams_id'] = $examsid;
        $data_history['from_platform'] = $from_platform;
        $data_history['std_area_id'] = $tqms;
        $data_history['start_time'] = $curltime;
        $data_history_answer['question_id'] = $question_id;
        $data_history_answer['scores'] = $questionScore;
        //echo count($keys);
        for ($i = 0;$i <count($keys);$i++ ){
            $student_id = $keys[$i];
            $answer =  $anMaps[''.$student_id.'']['answer'];
            $answer_result =  $anMaps[''.$student_id.'']['answer_result'];
            $answer_status =  $anMaps[''.$student_id.'']['answer_status'];
            $data_history['std_id'] = $student_id;
            $data_history_answer['std_id'] = $student_id;
            $data_history_answer['answer'] = $answer;
            $data_history_answer['std_area_id'] = $tqms;
            $data_history_answer['answer_result'] = $answer_result;
            $data_history_answer['answer_status'] = $answer_status;
            $data_history_answer['answer_time'] = $curltime;
            //判断用户主表历史答题记录
            $result = $device_history->field('id')->where('exams_id = "' . $examsid . '" and studentclass = "' . $studentClassid . '"')->select();          
 if (count($result) == 0) {
                $history_id = $device_history->data($data_history)->add();
                
            }
            else{
                $history_id = $result[0]['id'];
            }
           
            $data_history_answer['history_id'] = $history_id;
            $result_answer = $device_history_answer->field('id')->where('std_id = "'.$student_id.'" and history_id = "' . $history_id . '" and question_id = "' . $question_id . '"')->select();
            if(count($result_answer) > 0){
                 $device_history_answer->data($data_history_answer)->where('id ='.$result_answer[0]['id'])->save();
            }
            else{
                $device_history_answer->data($data_history_answer)->add();
            }

        }
        
    } 
    public function getAnswerResult(){
        $examsid = I("examsid/d",0);
        $classId = I("classId/s",0);
        $from_platform = I("from_platform/s",'exams');
        if($from_platform == '0'){
            $from_platform = 'exams';
        }
        $sql = 'select id from engs_device_history where studentclass = "'.$classId.'" and exams_id = "'.$examsid.'" order by id desc';
        $Model =  M();
        $AnswerResult = $Model->query($sql);
        $this->ajaxReturn($AnswerResult);
    }
    //班级平均分
    // public function average(){
    //     $localAreaCode = I("localAreaCode/s",0);
    //     $examsid = I("examsid/d",0);
    //     $studentNum = I('studentNum/d',0);
    //     $studentclassid = I('studentclassid/s',0);
    //     $sql = 'select studentid,questionid,answer,answer_result,answer_status,score from engs_answerdevice_result where examsid = ' . $examsid . ' and localareacode = "'.$localAreaCode.'" and studentclassid = "'.$studentClassid.'" and answer_result = "100" order by id';
    //     $Model =  M();
    //     $average = $Model->query($sql);
    //     $totalscore = 0;
    //     if(count($average) > 0){
    //         for($i = 0; $i < count($average); $i++){
    //             $totalscore = $totalscore+$average[$i]['score'];
    //         }
    //         $data['average'] = $totalscore+$average[$i]['score']/$studentNum;
    //     }
    //     else{
    //         $data['average'] = 0;
    //     }

    //     $this->ajaxReturn($data);
    // }  
    public function save_device_conf(){
        $username = I('username/s',0);
        $config_type = I('config_type/s',0);
        $config_value = I('config_value/s',0);
        $pageInterval = I('pageInterval/s',0);
        $studentClassid = I('studentClassid/s',0);
        $Model =  M('device_config');
        $data['username'] = $username;
        $data['config_type'] = $config_type;
        $data['config_value'] = $config_value;
        $data['pageinterval'] = $pageInterval;
        $data['studentclassid'] = $studentClassid;
        $result = $Model->field('id')->where('username = "'.$username.'" and config_type = "' . $config_type . '" and studentclassid="'.$studentClassid.'"')->select();
        if(count($result) > 0){
            $Model->data($data)->where('id ='.$result[0]['id'])->save();
        }
        else{
            $Model->data($data)->add();
        }

    }
    public function getExamsData()
    {
        $examsid = I('examsid/d', 0);
        $studentClassid = I('studentClassid/s',0);
        $sum_student = I('sum_student/s',0);
        $from_platform = I('from_platform/s','exams');
        if($from_platform == '0');
        {
            $from_platform = "exams";
        }
        $examsinfo = M('exams')->field('name')->where('id=%d',$examsid)->find();
        $papername = $examsinfo['name'];
        $classtotalscore = 0;
        $newquearray = array();
        $tempkey = 0;
        $sqlquery = M();
        $model = M();
        $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $stem_type = $value['stem_type'];
                $papertitle = $value['content'];
                $question_playtimes = $value['question_playtimes'];
                $question_score = $value['question_score'];
                if($stem_type == '1'){          //独立大题
                        $score = 0;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and typeid in(1,3) and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql);
              
                        foreach ($question as $quekey => $quevalue) { 
                    
                            $question[$quekey]['papertitle'] = $papertitle; 
                            $question[$quekey]['question_playtimes'] = $question_playtimes;
                            $question[$quekey]['question_score'] = $question_score;
                            $question[$quekey]['stem_type'] = '1';
                            $question[$quekey]['stem_id'] = $value['id'];
                            $question[$quekey]['tcontent'] = str_replace("/uploads",PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads",$question[$quekey]['tcontent']);
                            $sql = 'select a.title from engs_dictionary a,engs_exams_questions_keys b where a.id=b.dictionary_id and b.dictionary_code="que_key4" and b.questionsid = ' . $quevalue['id'] . ' and b.isdel = 1 order by b.id desc';
                            $que_key4 = $model->query($sql);        //难易度
                            $question[$quekey]['que_key4'] = $que_key4[0]['title'];
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql);
                                $sql = 'select flag from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_options = $model->query($sql);
                                if($quevalue['typeid'] == '1'){     //选择题
                                    $sql = 'select a.flag as trueanswer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                    $question[$quekey]['isObjective'] = true;  //是否接收答题器信号
                                }
                                else{           //排序题
                                    $sql = 'select answer as trueanswer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                     $question[$quekey]['isObjective'] = false; //是否接收答题器信号
                                }
                                $queanswer = $sqlquery -> query($sql);
                            }
                            else{           //填空题
                                if($quevalue['typeid'] == '2'){
                                    $question[$quekey]['isObjective'] = false;  //是否接收答题器信号
                                    $que_items = '0';
                                    $que_options = '0';
                                }
                                else{   //判断题
                                    $question[$quekey]['isObjective'] = true;   //是否接收答题器信号
                                    $que_options = array();
                                    $que_options[0]['flag'] = '0';
                                    $que_options[1]['flag'] = '1';
                                    $que_items = array();
                                    $que_items[0]['flag'] = '0';
                                    $que_items[1]['flag'] = '1';
                                }
                                 $sql = 'select answer as trueanswer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                                
                            }
                            foreach ($que_items as $que_items_key => $que_items_value) {    //查询每个选项有几个作答
                                if(count($device_history) > 0){
                                    $sql = 'select id from engs_device_history_answer where history_id = '.$device_history[0]['id'].' and answer = "'.$que_items_value['flag'].'" and question_id='.$quevalue['id'];
                                    $result =  $model -> query($sql);
                                    $que_items[$que_items_key]['reply_num'] = count($result);
                                }
                                else{
                                    $que_items[$que_items_key]['reply_num'] = 0;
                                }
                                if($quevalue['itemtype'] == "1"){
                                    $que_items[$que_items_key]['content'] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/".$que_items[$que_items_key]['content'];
                                }
                            }
                            $question[$quekey]['items'] = $que_items;
                            $question[$quekey]['itemsoption'] = $que_options;
                            $question[$quekey]['trueanswer'] = $queanswer;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                             $mp3list = array();
                             $tts_content = '';
                            foreach ($que_tts_noqn as $keymp3 => $quemp3) {
                               // $mp3str .= $quemp3['tts_mp3'] . '|';
                                $mp3['mp3']= PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_exam/".substr($quemp3['tts_mp3'],0,2)."/".$quemp3['tts_mp3'].".mp3";
                                $mp3['stoptime']=$quemp3['tts_stoptime'];
                                array_push($mp3list,$mp3);
                                if($quemp3['flag_content'] != ''){
                                    $tts_content .= "<p>".$quemp3['flag_content'].":".$quemp3[tts_content]."</p>";
                                }
                                else{
                                    $tts_content .= "<p>".$quemp3[tts_content]."</p>";
                                }
                                
                            }
                             $question[$quekey]["mp3list"] = $mp3list;
                             $question[$quekey]["tts_content"] = $tts_content;
                            // $question[$quekey]['tts'] = $que_tts_noqn;
                            //$mp3['mp3']= RESOURCE_DOMAIN."yylmp3/mp3_exam";
                            if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].'  order by id';
                                    $result = $model->query($sql);
                                    $anMpaHis = '';
                                    if(count($result) > 0){
                                        foreach($result as $hikey => $hivalue){ 
                                             $data1['qusetion_id'] = $hivalue['question_id'];
                                             $data1['useranswer'] = $hivalue['answer'];
                                             $data1['answer_result'] = $hivalue['answer_result'];
                                             $data1['anwser_status'] = $hivalue['answer_status'];
                                             $data2[$hivalue['std_id']] = $data1;     
                                        }
                                        $question[$quekey]["anMapsHis"] = $data2;
                                    }
                                    else{
                                        $question[$quekey]["anMapsHis"] = "{}";
                                    }
                                if($sum_student != 0){
                                    $totalscore = 0;
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" order by id';
                                    $result_100 = $model->query($sql);
                                    if(count($result_100) > 0){
                                        $accuracy = count($result_100)/count($result)*100;
                                        foreach ($result_100 as $avekey => $aveval) {
                                            $totalscore = $totalscore+$aveval['scores'];
                                            $classtotalscore = $classtotalscore+$aveval['scores'];
                                        }
                                        $classaverage = $totalscore/count($result);

                                    }
                                    else{
                                        $classaverage = 0;
                                        $accuracy = 0;
                                    }
                                    $question[$quekey]['accuracy'] = $accuracy;  //这个班级对这个小题回答的正确率
                                    $question[$quekey]['average'] = $classaverage;  //这个班级对这个小题回答的平均分
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="99" and answer_status = "1" order by id';
                                    $result = $model->query($sql);
                                    $question[$quekey]['invalid_num']  = count($result);
                                } 
                            }
                            else{       //没有历史答题记录
                                $question[$quekey]["anMapsHis"] = "{}";
                                $question[$quekey]['accuracy'] = 0;  //这个班级对这个小题回答的正确率
                                $question[$quekey]['average'] = 0;  //这个班级对这个小题回答的平均分
                            }
                            
                             $newquearray[$tempkey] = $question[$quekey];
                             $newquearray[$tempkey]['mp3type'] =4;
                             $newquearray[$tempkey]['from_platform'] = 'exams';
                             $tempkey++;
                        }  
                        
                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $stem_children[$keychild]['stem_type'] = '2';
                        $stem_children[$keychild]['papertitle'] = $value['content'];
                        
                        $stem_children[$keychild]['question_playtimes'] = $childvalue['question_playtimes'];
                        $stem_children[$keychild]['tips'] = 1;
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                        $mp3list = array();
                        $tts_content = '';
                            foreach ($child_stem_tts as $ckeymp3 => $cquemp3) {
                                $mp3['mp3']= PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_exam/".substr($cquemp3['tts_mp3'],0,2)."/".$cquemp3['tts_mp3'].".mp3";
                                $mp3['stoptime']=$cquemp3['tts_stoptime'];
                                array_push($mp3list,$mp3);
                                if($cquemp3['flag_content'] != ''){
                                    $tts_content .= "<p>".$cquemp3['flag_content'].":".$cquemp3['tts_content']."</p>";
                                }
                                else{
                                    $tts_content .= "<p>".$cquemp3['tts_content']."</p>";
                                }
                            }
                        //$stem_children[$keychild]['mp3'] = $mp3str;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and typeid in(1,3) and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) { 
                            if($child_quevalue['tcontent'] == "" || $child_quevalue['tcontent'] == "null"){
                                $child_quevalue['tcontent'] = $childvalue['content'];
                            }
                            $newquearray[$tempkey]['id'] = $child_quevalue['id'];
                            $newquearray[$tempkey]['stemid'] = $child_quevalue['stemid'];
                            $newquearray[$tempkey]['question_num'] = $child_quevalue['question_num'];
                            $newquearray[$tempkey]['typeid'] = $child_quevalue['typeid'];
                            $newquearray[$tempkey]['tcontent'] = $child_quevalue['tcontent'];
                            $newquearray[$tempkey]['acontent'] = $child_quevalue['acontent'];
                            $newquearray[$tempkey]['itemtype'] = $child_quevalue['itemtype'];
                            $newquearray[$tempkey]['display'] = $child_quevalue['display'];
                            $newquearray[$tempkey]['tips'] = $child_quevalue['tips'];
                            $newquearray[$tempkey]['stoptimes'] = $child_quevalue['stoptimes'];
                            $newquearray[$tempkey]["stem_type"] = 2; 
                            $newquearray[$tempkey]['stem_id'] = $childvalue['id'];
                            $newquearray[$tempkey]["content"] = $childvalue['content'];
                            $newquearray[$tempkey]["papertitle"] = $value['content'];
                            $newquearray[$tempkey]["question_playtimes"] = $childvalue['question_playtimes'];
                            $newquearray[$tempkey]['question_score'] = $childvalue['question_score'];
                            $newquearray[$tempkey]['tcontent'] = str_replace("/uploads",PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads",$newquearray[$tempkey]['tcontent']);
                            $sql = 'select a.title from engs_dictionary a,engs_exams_questions_keys b where a.id=b.dictionary_id and b.dictionary_code="que_key4" and b.questionsid = ' . $child_quevalue['id'] . ' and b.isdel = 1 order by b.id desc';
                            $que_key4 = $model->query($sql);        //难易度
                            $newquearray[$tempkey]['que_key4'] = $que_key4[0]['title'];

                            if($child_quevalue['typeid'] == '1' || $child_quevalue['typeid'] == '4'){       //如果是选择题或排序题就获取选项
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $child_quevalue['id'] . ' and isdel = 1 order by sortid,id';
                                $child_que_items = $sqlquery->query($sql);
                                foreach ($child_que_items as $child_que_items_key => $child_que_items_value) {    //查询每个选项有几个作答
                                
                                    if($child_quevalue['itemtype'] == "1"){
                                        $child_que_items[$child_que_items_key]['content'] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/".$child_que_items[$child_que_items_key]['content'];
                                    }
                                }
                                $newquearray[$tempkey]['items'] = $child_que_items;
                               
                                if($child_quevalue['typeid'] == '1'){
                                    $sql = 'select a.flag as trueanswer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' and a.questionsid= ' . $child_quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                }
                                else{
                                    $sql = 'select answer as trueanswer,answer_num from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'].' and isdel=1 order by sortid,id';     //问题的答案
                                }
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                            }
                            else{
                            
                                $que_items = array();
                                $que_items[0]['flag'] = '0';
                                $que_items[1]['flag'] = '1';
                                $sql = 'select answer as trueanswer,answer_num from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'].' and isdel=1 order by sortid,id';     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                                $newquearray[$tempkey]['items'] = $que_items;
                            }
                            $newquearray[$tempkey]['trueanswer'] = $child_queanswer;
                             $stem_children[$keychild]['questypeid'] = $child_quevalue['typeid'];
                             $stemdata[$key]['questypeid'] = $child_quevalue['typeid'];
                             if(count($device_history) > 0){     //这个班级对这套试卷有历史答题记录
                                $sql = 'select std_id,question_id,answer,answer_result,answer_status from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].'  order by id';
                                    $result = $model->query($sql);
                                    $anMpaHis = '';
                                    if(count($result) > 0){
                                        foreach($result as $hikey => $hivalue){ 
                                             $data1['qusetion_id'] = $hivalue['question_id'];
                                             $data1['useranswer'] = $hivalue['answer'];
                                             $data1['answer_result'] = $hivalue['answer_result'];
                                             $data1['anwser_status'] = $hivalue['answer_status'];
                                             $data2[$hivalue['std_id']] = $data1;     
                                        }
                                        $newquearray[$tempkey]["anMapsHis"] = $data2;
                                    }
                                    else{
                                        $newquearray[$tempkey]["anMapsHis"] = "{}";
                                    }
                                if($sum_student != 0){
                                    //echo "ss";exit;
                                    $totalscore = 0;
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="100" order by id';
                                    $result_100 = $model->query($sql);
                                    if(count($result_100) > 0){
                                        $accuracy = count($result_100)/count($result)*100;
                                        foreach ($result_100 as $avekey => $aveval) {
                                            $totalscore = $totalscore+$aveval['scores'];
                                            $classtotalscore = $classtotalscore+$aveval['scores'];
                                        }
                                        $classaverage = $totalscore/count($result);

                                    }
                                    else{
                                        $classaverage = 0;
                                        $accuracy = 0;
                                    }
                                    $newquearray[$tempkey]['accuracy'] = $accuracy;  //这个班级对这个小题回答的正确率
                                    $newquearray[$tempkey]['average'] = $classaverage;  //这个班级对这个小题回答的平均分
                                    $sql = 'select std_id,question_id,answer,answer_result,answer_status,scores from engs_device_history_answer where question_id = ' . $child_quevalue['id'] . ' and history_id = '.$device_history[0]['id'].' and answer_result="99" and answer_status = "1" order by id';
                                    $result = $model->query($sql);
                                    $newquearray[$tempkey]['invalid_num']  = count($result);
                                } 
                            }
                            else{       //没有历史答题记录
                                $newquearray[$tempkey]["anMapsHis"] = "{}";
                                $newquearray[$tempkey]['accuracy'] = 0;  //这个班级对这个小题回答的正确率
                                $newquearray[$tempkey]['average'] = 0;  //这个班级对这个小题回答的平均分
                            }
                           
                            
                            $newquearray[$tempkey]['mp3list'] =$mp3list;
                            $newquearray[$tempkey]['tts_content'] =$tts_content;
                            // $newquearray[$tempkey]['tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;
                            $newquearray[$tempkey]['mp3type'] =4;
                            $newquearray[$tempkey]['from_platform'] = 'exams';
                            $newquearray[$tempkey]['isObjective']= false;
                            //$newquearray[$tempkey]["question"] = $child_question[$child_quekey];
                            $tempkey++;
                        }
                       
                    }
                    
                }
            } 
            $data["examsname"] = $papername;
            $data["examsdata"] = $newquearray;
            $this -> ajaxReturn($data);           
    }

   //套卷登分接口
    public function batchSaveAnswer(){
        $jsondata = urldecode(I("jsondata"));
       // $jsondata='{"paper_id":"89","studentClass":"147626947025699330","tqms":"tmszz.zzedu.net.cn","anMaps":{"vcomyy":[],"1333108429291552":[],"410101100000228128":[],"1325987845373984":[],"410101100000196929":[{"question_id":"71246","anwser":"A","anwser_result":"100","anwser_status":"2"},{"question_id":"73600","anwser":"B","anwser_result":"99","anwser_status":"2"},{"question_id":"71637","anwser":"C","anwser_result":"99","anwser_status":"2"},{"question_id":"72836","anwser":"D","anwser_result":"99","anwser_status":"2"}],"410101100000029352":[{"question_id":"71246","anwser":"C","anwser_result":"99","anwser_status":"2"},{"question_id":"73600","anwser":"A","anwser_result":"99","anwser_status":"2"},{"question_id":"71637","anwser":"D","anwser_result":"99","anwser_status":"2"},{"question_id":"72836","anwser":"B","anwser_result":"99","anwser_status":"2"}],"410101100000278456":[],"410101100000223925":[]}}';

       //{"paper_id":89,"username":"0","truename":"胡晓攀","tqms":"tqmstest.yjt361.com","anMaps":{"vcomhxp":[{"answer_result":99,"answer_status":2,"question_id":"1430","answer":"A"},{"answer_result":99,"answer_status":2,"question_id":"1444","answer":"B"}]}}
       //writeAppLog("tqmstest".Date("Y-m-d").".log",json_encode($jsondata));
        $jsoncallback = I("jsoncallback/s","0");
        $jsondata = htmlspecialchars_decode($jsondata);
        $studentResult = json_decode($jsondata,true);
   
        $device_history = M('device_history');  //历史记录主表
        $device_history_answer = M('device_history_answer');    //历史记录从表
        $Model =  M();
        $time = time();
        $curtime = date("y-m-d H:i:s", $time);
        $examsid = $studentResult['paper_id'];
        $studentClassid = $studentResult['studentClass'];
        $tqms = $studentResult['tqms'];
        $anMaps = $studentResult['anMaps'];
 
        $examsinfo = M('exams')->field('name,ks_code')->where('id=%d',$examsid)->find();
       // $papername = urlencode($examsinfo['name']);
        $papername = $examsinfo['name'];
        $ks_code = $examsinfo['ks_code'];
        $username = $studentResult['username'];
        $truename = $studentResult['truename'];

        $this->tqms_post($tqms,$examsid,$ks_code,$papername,$username,$truename);

        $data_history['studentclass'] = $studentClassid;
        $data_history['exams_id'] = $examsid;
        $data_history['from_platform'] = $from_platform;
        $data_history['std_area_id'] = $tqms;
        $data_history['start_time'] = $curtime;
        $result = $device_history->field('id')->where('exams_id = "' . $examsid . '" and studentclass = "' . $studentClassid . '"')->find();
                 
        if (count($result) == 0) {
            $history_id = $device_history->data($data_history)->add();
            //echo $device_history->getLastSql();
           // echo $history_id;exit; 
                
        }
        else{
            $history_id = $result['id'];
        }
        //echo $history_id;exit;  
        foreach ($anMaps as $key => $value) {
          
            if(count($value) > 0){
                $student_id = $key;
                foreach ($value as $quekey => $queval) {
                    $question_id = $queval['question_id'];
                    $questionScore = $queval['questionScore'];
                    $answer_result = $queval['anwser_result'];
                    $answer_status = $queval['anwser_status'];
                    $useranswer = $queval['anwser'];
                    $data_history_answer['std_id'] = $student_id;
                    $data_history_answer['answer'] = $useranswer;
                    $data_history_answer['std_area_id'] = $tqms;
                    $data_history_answer['answer_result'] = $answer_result;
                    $data_history_answer['answer_status'] = $answer_status;
                    $data_history_answer['answer_time'] = $curtime;
                    $data_history_answer['question_id'] = $question_id;
                    $data_history_answer['scores'] = "1.0";
                    $data_history_answer['history_id'] = $history_id;
                    $result_answer = $device_history_answer->field('id')->where('std_id = "'.$student_id.'" and history_id = "' . $history_id . '" and question_id = "' . $question_id . '"')->find();
                    if(count($result_answer) > 0){
                         $device_history_answer->data($data_history_answer)->where('id ='.$result_answer['id'])->save();
                    }
                    else{
                        $device_history_answer->data($data_history_answer)->add();
                    }
                    //echo $device_history_answer->getLastSql();
                }
            }
            
        }
    
        $data["code"] = "200";
        $data["message"] = "操作成功";
        if($jsoncallback == "0"){
            $this -> ajaxReturn($data);
        }
        else{
            $this -> ajaxReturn($data,"jsonp");
        }
    }

    public function tqms_post($tqms,$examsid,$lessionId,$homeWorkName,$username,$truename){
        $url=PROTOCOL."://".$tqms."/tqms/mobile/homework/saveEnClsrmPaper.action";
        $ch = curl_init();
        $post_fields['paper_id'] = $examsid;
        $post_fields['lessionId'] = $lessionId;
        $post_fields['homeWorkName'] = $homeWorkName;
        $post_fields['username'] = $username;
        $post_fields['truename'] = $truename;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));//POST的数据大于1024时的处理
        $res = curl_exec($ch);
        curl_close($ch);
        $urlttt=PROTOCOL."://".$tqms."/tqms/mobile/homework/saveEnClsrmPaper.action?paper_id=".$examsid."&lessionId=".$lessionId."&homeWorkName=".$homeWorkName."&username=".$username."&truename=".$truename;
        writeAppLog("tqmstest".Date("Y-m-d").".log",$urlttt."||".$res);
        //writeAppLog("tqmstest".Date("Y-m-d").".log",$url."||".$res);
    }
}
