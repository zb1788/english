<?php

namespace Elearn\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class IndexController extends CheckController {
    public function index(){
        $app_id=I('app_id/d',0);
        $ks_code = I("ks_code/s","0");
        $username=cookie("username");
        $areacode=cookie("areacode");
        $pls_url = cookie("pls_url");
        if(empty($pls_url)){
            $pls_url = 'plshenan.czbanbantong.com';
        }
        $subject_code=cookie("subjectid");
        $backUrl = I('backUrl/s','0');
        if($backUrl == '0'){
           $backUrl = "http://www.czbanbantong.com" ;
        }
        $moduleid = I('moduleid/d',0);
        $gradeid = I('gradeid/s','0');
        $subjectid=I('subjectid/s','0');
        $termid=I('termid/s','0002');
        $version_id = I("vid/s","0");
        $pyversion_code = I("versionid/s","0001");
        if(ceil($gradeid) >= 10){
            $gradeid = "0010";
        }
        if($moduleid != 0){
            cookie('app_moduleid',$moduleid);
        }
        if($gradeid != '0'){
            
            cookie('app_gradeid',$gradeid);
            cookie('gradeid',$gradeid);
        }
        else{
            $gradeid = cookie('gradeid');
            cookie('app_gradeid',$gradeid);

            
            cookie('gradeid',$gradeid);
        }
        if($subjectid != '0'){
            cookie('subjectid',$subjectid);
        }
        if($username == '' || $username=='null' || $username == null){
            $username=cookie("engm_username");
            $areacode=cookie("engm_areacode");
            if($username == '' || $username=='null' || $username == null){
                $username='hxp198359';
                $areacode='1.1.1';
            }
            
        }
        if(empty($username)){
            $username='hxp198359';
            $areacode='1.1.1';
        }
        //$subject_code='aaa';
      //  echo "sss".$username;exit;
        $appdata = M('application');
        $appresult = $appdata->where('id=%d',$app_id)->find();
        //var_dump($appresult);exit;
        $isterm = $appresult['app_isterm'];  //该应用是否有版本
        $isunit = $appresult['app_isunit'];   //该应用是否有单元
        $unit_type = $appresult['unit_type'];   //单元展示方式
        $subject_code = $appresult['app_subject_code'];
        cookie('app_name',$appresult['app_name']);
        cookie('subjectid',$subject_code);
        //echo $isterm;exit;
        //如果有版本获取版本信息
        if($isterm == 1){
            $app_gradeid = $gradeid;
            if($version_id == "0"){
                $version = M('version')->field("id,r_grade_name,r_version_name,r_term_name,r_pic")->where('app_id = %d and r_subject_code = "%s" and r_grade_code = "%s" and r_term_code = "%s" and r_version_code = "%s" and isdel=1',$app_id,$subject_code,$app_gradeid,$termid,$pyversion_code)->order('sortid','id')->find();
                if(count($version) == 0){
                    $version = M('version')->field("id,r_grade_name,r_version_name,r_term_name,r_pic")->where('app_id = %d and r_subject_code = "%s" and r_grade_code = "%s" and isdel=1',$app_id,$subject_code,$app_gradeid)->order('sortid','id')->find();
                }
            }
            else{
                $version = M('version')->field("id,r_grade_name,r_version_name,r_term_name,r_pic")->where('id = %d and isdel=1',$version_id)->order('sortid','id')->find();
            }
            $version_id = $version['id'];
            if($isunit == 1){
                $sql = "select a.*,(select count(*) from mc_learn_record b where b.unit_id = a.id and b.username = '".$username."' and b.areacode='".$areacode."' ) as learn_num from mc_unit a where a.app_id=".$app_id." and a.version_id=".$version_id." and a.isdel = 1 order by a.sortid,a.id";
                $unitlist = M()->query($sql);
                
            }
            else{
                if(count($version) > 0){
                    $courselist = get_course_list($isterm,$isunit,$app_id,$version_id,0);
                }
                
            }
            $h2name = $version['r_grade_name'].$version['r_version_name']."（".$version['r_term_name']."）";
            $r_pic = $version['r_pic'];
        }
        else{
            if($isunit == 1){
                 $sql = "select a.*,(select count(*) from mc_learn_record b where b.unit_id = a.id and b.username = '".$username."' and b.areacode='".$areacode."' ) as learn_num from mc_unit a where a.app_id=".$app_id." and a.version_id=0 and a.isdel=1 order by a.sortid,a.id";
                 $unitlist = M()->query($sql);

            }
            else{
                $courselist = get_course_list($isterm,$isunit,$app_id,0,0);
            }
            $h2name = $appresult['app_name'];
            $r_pic = $appresult['app_pic'];
        }

        $pingjia = M('evaluate')->where('app_id=%d and isdel=1',$app_id)->order('id desc')->select();
        $this ->assign('pingjia',$pingjia);
        //var_dump($unitlist);exit;
        $this ->assign('r_grade_code',$version['r_grade_code']);
        $this ->assign('r_term_code',$version['r_term_code']);
        $this ->assign('r_version_code',$version['r_version_code']);
        $this ->assign('r_grade_name',$version['r_grade_name']);
        $this ->assign('r_term_name',$version['r_term_name']);
        $this ->assign('r_version_name',$version['r_version_name']);
        $this ->assign('r_subject_name',$version['r_subject_name']);
        $this ->assign('r_remark',$version['r_remark']);
        $this ->assign('r_pic',$r_pic);
        $this ->assign('h2name',$h2name);
        $this -> assign('unitlist',$unitlist);
        $this -> assign('app_id',$app_id);
        $this -> assign('version_id',$version_id);
        $this -> assign('courselist',$courselist);
        $this -> assign('app_name',$appresult['app_name']);
        $this -> assign('subject_code',$appresult['app_subject_code']);
        $this -> assign('isterm',$appresult['app_isterm']);
        $this -> assign('isunit',$appresult['app_isunit']);
        $this -> assign('unit_type',$appresult['app_unit_type']);
        $this -> assign('isspecial',$appresult['app_isspecial']);
        $this -> assign('special_code',$appresult['app_special_code']);
        $this -> assign('issource',$appresult['app_issource']);
        $this -> assign('source_code',$appresult['app_source_code']);
        $this -> assign('app_drumbeating',$appresult['app_drumbeating']);
        $appresult['app_object'] = str_replace('||', '|', $appresult['app_object']);
        $appresult['app_target'] = str_replace('||', '|', $appresult['app_target']);
        $this -> assign('app_object',explode('|', $appresult['app_object']));
        $this -> assign('app_target',explode('|', $appresult['app_target']));
        $this -> assign('pic_feat',$appresult['app_course_feat']);
        $this -> assign('pic_content',$appresult['app_course_content']);
        $this -> assign('pic_teac',$appresult['app_course_teac']);
        //$this -> assign('app_object',html_entity_decode($appresult['app_content']));
        $this -> assign('app_pic',$appresult['app_pic']);
        $this -> assign('backUrl',$backUrl);
        $this -> assign('username',$username); 
        $this -> assign('areacode',$areacode);
        $this -> assign('pls_url',$pls_url);
        $app_moduleid = cookie('app_moduleid');
        $this -> assign('app_moduleid',$app_moduleid);
        $this->display("index");
        // if($ks_code == "0"){
        //     $this->display("index");  
        // }
        // else{
        //     $result = array();
        //     $result = M('unit')->field("id,name")->where("ks_code='".$ks_code."' and isdel = 1 and app_id = ".$app_id)->find();
        //     if(empty($result["id"])){
        //         $this->display("index");
        //     }
        //     else{
        //         $url = "course?app_id=".$app_id."&version_id=".$version_id."&subject_code=".$appresult['app_subject_code']."&unit_id=".$result['id']."&isterm=".$appresult['app_isterm']."&isunit=".$appresult['app_isunit']."&title=".$result['name']."&backUrl=".$backUrl;
        //         redirect($url);
        //     }

        //    // http://enhxp.yjt361.com:8080/Elearn/index/course?app_id=80&version_id=6737&subject_code=0002&unit_id=361&isterm=1&isunit=1&title=%E6%97%B6%E3%80%81%E5%88%86%E3%80%81%E7%A7%92&backUrl=/Subject/index/dojumpback
        // }
    }
         public function ping(){
            $app_id = I('app_id/d',0);
            $backUrl = I('backUrl/s','0');
             if($backUrl == '0'){
                $backUrl = "http://www.czbanbantong.com" ;
             }
            $app_name = I('app_name/s','0');
            $this -> assign('app_id',$app_id); 
            $this -> assign('backUrl',$backUrl); 
            $this -> assign('app_name',$app_name);
            $this->display("ping");
         }
         public function pinglunaction(){
            $app_id = I('app_id/d',0);
            $score = I('score/d',0);
            $content = I('content/s','0');
            $content = htmlspecialchars($content);
            $content = mb_substr($content,0,20,'utf-8');
            if(!empty(cookie('username'))){
                $username=cookie("username");
                $areacode=cookie("areacode");
                $truename=cookie("truename");
                //是否对发送的评论内容进行敏感词过滤 0:不过滤；1:过滤
                $BLACK_WORD_FILTER = C('BLACK_WORD_FILTER');
                $BLACK_WORD_FILTER_IP = C('BLACK_WORD_FILTER_IP');
                $BLACK_WORD_FILTER_TIMEOUT = C('BLACK_WORD_FILTER_TIMEOUT');
                $pl_service = 'http://'.$BLACK_WORD_FILTER_IP.'/check';
                if($BLACK_WORD_FILTER == '1'){
                    $ch = curl_init();
                    $post_fields['s'] = urlencode($content);
                    $post_fields['f'] = 0;
                    $post_fields['t'] = 1;
                    $post_fields['i'] = $username."_".$areacode;
                    $post_fields['c'] = "en";
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT,$BLACK_WORD_FILTER_TIMEOUT);
                    // 执行HTTP请求
                    curl_setopt($ch , CURLOPT_URL , $pl_service);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                    $res = curl_exec($ch);
                    curl_close($ch);
                    if($res != ''){
                        $content = urldecode($res);
                        $temparr = explode('*', $content);
                        if (count($temparr) > 1){
                            $data['flag'] = 0;
                            $data['message'] = '评论内容违规！';
                        }
                        else{
                            $data['flag'] = 1;
                            $data['message'] = '评论成功';
                        }
                    }
                    else{
                        $data['flag'] = 1;
                        $data['message'] = '评论成功';
                    }
                    
                }
                else{
                    $data['flag'] = 1;
                    $data['message'] = '评论成功';
                }
                if($data['flag'] == 1){
                    $evaluate = M('evaluate');
                    $evaluate->app_id = $app_id;
                    $evaluate->content = $content;
                    $evaluate->score = $score;
                    $evaluate->username = $username;
                    $evaluate->truename = $truename;
                    $evaluate->areacode = $areacode;
                    $evaluate->add();
                }
                $this ->ajaxReturn($data);
            }
            
        }
        public function course(){
            $username=cookie("username");
            $areacode=cookie("areacode");
            $pls_url = cookie("pls_url");
            $app_id = I('app_id/d',0);
            $backUrl = I('backUrl/s','0');
            
            if($backUrl == '0'){
                $backUrl = "http://www.czbanbantong.com" ;
            }
            //echo "s=".$backUrl;
            if(empty($pls_url)){
                $pls_url = 'plshenan.czbanbantong.com';
            }
            if(empty($username)){
                $username='hxp';
                $areacode='1.1.1';
            }
            $title = I('title/s','0');
            $appdata = M('application');
            $appresult = $appdata->where('id=%d',$app_id)->find();
            $subject_code=I('subject_code/s','0');
            $version_id = I('version_id/d',0);
            $unit_id= I('unit_id/d',0);
            $isterm = I('isterm/d',0);
            $isunit = I('isunit/d',0);
            $courselist = get_course_list($isterm,$isunit,$app_id,$version_id,$unit_id);
            $this -> assign('courselist',$courselist);
            $this->assign('app_id',$app_id);
            //var_dump($courselist);
            //echo $appresult['app_isspecial'];exit;
            $this -> assign('isspecial',$appresult['app_isspecial']);
            $this -> assign('special_code',$appresult['app_special_code']);
            $this -> assign('backUrl',$backUrl); 
            $this -> assign('title',$title);
            $this -> assign('version_id',$version_id);
            $this -> assign('unit_id',$unit_id);
            $this -> assign('isterm',$isterm);
            $this -> assign('isunit',$isunit);
            $this -> assign('username',$username); 
            $this -> assign('areacode',$areacode);
            $this -> assign('pls_url',$pls_url);
            $app_moduleid = cookie('app_moduleid');
            $this -> assign('app_moduleid',$app_moduleid);
            $this->display("course");
        }
        public function course_show(){
            $username=cookie("username");
            $areacode=cookie("areacode");
            $pls_url = cookie("pls_url");
            $app_id = I('app_id');
            $subject_code=I('subject_code/s',0);
            $version_id = I('version_id/d',0);
            $unit_id= I('unit_id/d',0);
            $isterm = I('isterm/d',0);
            $isunit = I('isunit/d',0);
            $course_id = I('course_id/d',0);
            $backUrl = I('backUrl/s','0');
            $course_back = I('course_back/s','0');
            if($backUrl == '0'){
                $backUrl = "http://www.czbanbantong.com" ;
            }
            if(empty($pls_url)){
                $pls_url = 'plshenan.czbanbantong.com';
            }
            if(empty($username)){
                $username='hxp';
                $areacode='1.1.1';
            }
            $title = I('title/s','0');
            $courseresult = M('course')->where('id=%d',$course_id)->find();
            $yaoqiulist = M('question')->where('course_id=%d and type=3 and isdel=1',$course_id)->order('sortid','id')->select();
            $fanwenlist = M('question')->where('course_id=%d and type=4 and isdel=1',$course_id)->order('sortid','id')->select();
            $this -> assign('videotitle',$courseresult['title']);
            $this -> assign('pic',$courseresult['big_pic']);
            $this -> assign('code',$courseresult['video_code']);
            $this->assign('app_id',$app_id);
            $this -> assign('yaoqiulist',$yaoqiulist);
            $this -> assign('fanwenlist',$fanwenlist);
            $this -> assign('backUrl',$backUrl); 
            $this -> assign('title',$title);
            $this -> assign('version_id',$version_id);
            $this -> assign('course_id',$course_id);
            $this -> assign('unit_id',$unit_id);
            $this -> assign('isterm',$isterm);
            $this -> assign('isunit',$isunit);
            $this -> assign('subject_code',$subject_code);
            $this -> assign('username',$username); 
            $this -> assign('areacode',$areacode);
            $this -> assign('course_back',$course_back);
            $this -> assign('pls_url',$pls_url);
            $app_moduleid = cookie('app_moduleid');
            $this -> assign('app_moduleid',$app_moduleid); 
            $this->display("course_show");
        }
        public function claim_show(){
            $app_id = I('app_id');
            $id = I('id/d',0);
            $subject_code=I('subject_code/s','0');
            $version_id = I('version_id/d',0);
            $unit_id= I('unit_id/d',0);
            $isterm = I('isterm/d',0);
            $isunit = I('isunit/d',0);
            $course_id = I('course_id/d',0);
            $backUrl = I('backUrl/s','0');
            $course_back = I('course_back/s','0');
            if($backUrl == '0'){
                $backUrl = "http://www.czbanbantong.com" ;
            }
            $title = I('title/s','0');
            $que=M('question');
            $rs = $que->where('id=%d',$id)->find();
            $this->assign('claimtitle',$rs['content']);
            $rs['remark'] = str_replace('/uploads', PROTOCOL."://".RESOURCE_DOMAIN.'/yylmp3/uploads', $rs['remark']);
            $this ->assign('content',html_entity_decode($rs['remark']));
            $this->assign('app_id',$app_id);
            $this->assign('course_id',$course_id);
            $this -> assign('backUrl',$backUrl); 
            $this -> assign('title',$title);
            $this -> assign('version_id',$version_id);
            $this -> assign('unit_id',$unit_id);
            $this -> assign('isterm',$isterm);
            $this -> assign('isunit',$isunit);
            $this -> assign('subject_code',$subject_code);
            $this -> assign('course_back',$course_back);
            $this->display("claim_show");
        }
        public function essay_show(){
            $app_id = I('app_id');
            $id = I('id/d',0);
            $subject_code=I('subject_code/s','0');
            $version_id = I('version_id/d',0);
            $unit_id= I('unit_id/d',0);
            $isterm = I('isterm/d',0);
            $isunit = I('isunit/d',0);
            $course_id = I('course_id/d',0);
            $backUrl = I('backUrl/s','0');
            $course_back = I('course_back/s','0');
            if($backUrl == '0'){
              $backUrl = "http://www.czbanbantong.com" ;
            } 
            $title = I('title/s','0');
            $que=M('question');
            $rs = $que->where('id=%d',$id)->find();
            $this->assign('essaytitle',$rs['content']);
            $remark = $rs['remark'];
            $remark = str_replace('/uploads', PROTOCOL."://".RESOURCE_DOMAIN.'/yylmp3/uploads', $remark);
            $remark = str_replace('&lt;strong&gt', '&lt;span&gt', $remark);
            $remark = str_replace('&lt;/strong&gt', '&lt;/span&gt', $remark);
            $remark = str_replace('&lt;p&gt', '&lt;p class="zhengwen plr10"&gt', $remark);
            $remark = str_replace('{', '<em>', $remark);
            $remark = str_replace('｛', '<em>', $remark);
            $remark = str_replace('}', '</em>', $remark);
            $remark = str_replace('｝', '</em>', $remark);
            //echo $remark;exit;
            $this ->assign('content',html_entity_decode($remark));
            $this->assign('app_id',$app_id);
            $this->assign('course_id',$course_id);
            $this -> assign('backUrl',$backUrl); 
            $this -> assign('title',$title);
            $this -> assign('version_id',$version_id);
            $this -> assign('unit_id',$unit_id);
            $this -> assign('isterm',$isterm);
            $this -> assign('isunit',$isunit);
            $this -> assign('subject_code',$subject_code);
            $this -> assign('course_back',$course_back);
            $this->display("essay_show");
        }
        public function app(){
            $appdata = M('application');
            $appresult = $appdata->where('isdel=1')->order('sortid','id')->select();
            $this -> assign('appresult',$appresult);
            //var_dump($appresult);exit;
            $this->display("app");
        }
        public function getGrade_version(){
        $rms_version = M('version');
        $app_id = I('app_id/d',0);
        $subject_code = I('subject_code/s','0');
        $version_id = I('version_id/d',0);
        $appgrade = M('grade_config');
        //该应用适配的年级
        $graderesult = $appgrade->where('app_id=%d and  isdel=1',$app_id)->order('sortid','id')->select();
        var_dump($graderesult);exit;
        foreach ($graderesult as $key => $value) {
            $gradeterm = $rms_version->where('app_id=%d and r_grade_code="%s" and isdel=1',$app_id,$value['grade_code'])->order('id')->select();
            foreach ($gradeterm as $k => $v) { 
                //取默认配置的版本年级学期设置为选中
                if ($v["id"] == $version_id) {
                    $gradeterm[$k]["checked"] = 1;
                }else{
                    $gradeterm[$k]["checked"] = 0;
                }
                $gradeterm[$k]['r_pic'] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$v['r_pic'];
                $gradeterm[$k]['app_id'] = $app_id;
                //$gradeterm[$k]['subject_code']=$subject_code;
            }
            $graderesult[$key]["termversion"] = $gradeterm;
        }
        //var_dump($graderesult);exit;
        $this->ajaxReturn($graderesult);
    }
     public function setUserGradeVersion(){
        $app_id=I("app_id");
        $version_id=I("version_id/d",0);
        $subject_code=I("subject_code/s",'0');
        $grade_code=I("grade_code/s",'0');
        $username=cookie("username");
        $areacode=cookie("areacode");
        if($grade_code != 0){
            cookie('gradeid',$grade_code);
            cookie('app_gradeid',$grade_code);
        }
        if($username == '' || $username=='null' || $username == null){
            $username=cookie("engm_username");
            $areacode=cookie("engm_areacode");
            $truename=cookie("engm_truename");
            if($username == '' || $username=='null' || $username == null){
               $username='hxp198359';
                $truename='hxp';
                $areacode='1.1.1';
            }
        }
        if(empty($username)){
            $username='hxp198359';
            $areacode='1.1.1';
        }
        $config=M("user_version_config");
        $config->app_id=$app_id;
        $config->version_id=$version_id;
        $config->subject_code=$subject_code;
        $config->grade_code=$grade_code;
        $config->username=$username;
        $config->areacode=$areacode;
        $config->add();
     }
     public function saveLearnRecord(){
        $username=cookie("username");
        $areacode=cookie("areacode");
        $localareacode=cookie("localAreaCode");
        $truename=cookie("truename");
        $schoolid=cookie("schoolid");
        $gradeid=cookie("gradeid");
        $usergradeid=cookie("usergradeid");
        $classid=cookie("classid");
        $usertype=cookie('usertype');
        if($username == '' || $username=='null' || $username == null){
            $username=cookie("engm_username");
            $areacode=cookie("engm_areacode");
            $truename=cookie("engm_truename");
            if($username == '' || $username=='null' || $username == null){
                $username='hxp';
                $truename='hxp';
                $areacode='1.1.1';
            }
        }
        $app_id=I("app_id/d",0);
        $version_id=I("version_id/d",0);
        $unit_id=I("unit_id/d",0);
        $course_id=I("course_id/d",0);
        $record = M('learn_record');
        $record->username = $username;
        $record->areacode = $areacode;
        $record->truename = $truename;
        $record->localareacode = $localareacode;
        $record->schoolid = $schoolid;
        $record->gradeid = $gradeid;
        $record->usergradeid = $usergradeid;
        $record->classid = $classid;
        $record->usertype = $usertype;
        $record->app_id = $app_id;
        $record->version_id = $version_id;
        $record->unit_id = $unit_id;
        $record->course_id = $course_id;
        $record->learntime = time();
        $record->add();
        $course_click = M('course_click');
        $rs = $course_click->where('course_id = '.$course_id)->find();
        if(count($rs) > 0){
            $course_click->where('course_id = '.$course_id)->setInc('click',1);
        }
        else{
            $course_click->course_id = $course_id;
            $course_click->click = 1;
            $course_click->add();

        }

     }
     public function entrance(){
         $this->display("entrance");
     }

     public function entrancedata(){
        $Dao = M('rms_dictionary'); // 实例化Word对象
        $data = $Dao->field('detail_code,detail_name')->where('dictionary_code="grade"')->order('detail_order')->select();
        foreach ($data as $key => $value) {
            $appdata = M('grade_config')->field('app_id')->where('grade_code="%s" and isdel=1',$value['detail_code'])->order('id')->select();
            //console.log()
            if(count($appdata) > 0){
               $app_idarr = '';
                foreach ($appdata as $key2 => $value2) {
                    $app_idarr .= $value2['app_id'].',';
                }
                $app_idarr = substr($app_idarr, 0, -1);
                $sql = "select distinct a.app_subject_code,b.detail_name from mc_application a left join mc_rms_dictionary b on a.app_subject_code = b.detail_code where a.id in(".$app_idarr.") and b.dictionary_code = 'subject' order by a.id";
                $subject = M()->query($sql);
                foreach ($subject as $key3 => $value3) {
                    $sql = "select b.id,b.app_name from mc_grade_config a left join mc_application b on a.app_id = b.id where a.grade_code = '".$value['detail_code']."' and b.app_subject_code = '".$value3['app_subject_code']."' and b.isdel=1 order by b.id";
                    $applist = m()->query($sql);
                    $subject[$key3]['applist'] = $applist;
                }
                $data[$key]['subject'] = $subject; 
            }
            
        }
        
        //var_dump($data);
        $this->ajaxReturn($data);
     }

}
