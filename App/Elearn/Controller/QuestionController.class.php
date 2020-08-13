<?php

namespace Elearn\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class QuestionController extends CheckController {

    public function index() {
        $app_id = I('app_id');
        $course_id = I('course_id/d',0);
        $backUrl = I('backUrl/s','0');
        if($backUrl == '0'){
            $backUrl = "http://www.czbanbantong.com" ;
        }
        $title = I('title/s','0');
        $videotitle = I('videotitle/s','0');
        $course_back = I('course_back/s','0');
        $version_id = I('version_id/d',0);
        $unit_id= I('unit_id/d',0);
        $isterm = I('isterm/d',0);
        $isunit = I('isunit/d',0);
       // $courseresult = M('course')->where('id=%d',$course_id)->find();
        //$yaoqiulist = M('question')->where('course_id=%d and type=3',$course_id)->order('id')->select();
        $this -> assign('videotitle',$videotitle);
        $this->assign('app_id',$app_id);
        $this->assign('version_id',$version_id);
        $this->assign('unit_id',$unit_id);
        $this->assign('isterm',$isterm);
        $this->assign('isunit',$isunit);
        $this -> assign('backUrl',$backUrl); 
        $this -> assign('title',$title);
        $this -> assign('course_id',$course_id);
        $this -> assign('course_back',$course_back);
        $this -> assign('pic_url',PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads");
        $this->display("index");
    }
    public function getQuestionsData(){
        $course_id = I('course_id/d',0);
        $que = M('question');
        $rs = $que->where('course_id=%d and isdel=1 and type in(1,2)',$course_id)->order('sortid','id')->select();
        foreach ($rs as $key => $value) {
            $itemsdata = $value['itemsdata'];
            $itemsdata = rtrim($itemsdata, '"');
            $itemsdata = ltrim($itemsdata, '"');
            $itemsdata = str_replace('&quot;', '"', $itemsdata);
            $itemsdata = json_decode($itemsdata);
            $rs[$key]['itemsdata'] =$itemsdata;
            $rs[$key]['content'] = html_entity_decode($value['content']);
            $rs[$key]['content'] = str_replace('/uploads', PROTOCOL."://".RESOURCE_DOMAIN.'/yylmp3/uploads', $rs[$key]['content']);
           // $rs[$key]['content'] = str_replace('src', 'style="margin-left:40px" src', $rs[$key]['content']);
        }
        //var_dump($rs[0]);exit;
        $this->ajaxReturn($rs);
    }
    public function saveUseranswer(){
        $app_id = I('app_id');
        $course_id = I('course_id/d',0);
        $que_id = I('que_id/d',0);
        $score = I('score/d',0);
        $useranswer = I('useranswer/s','0');
        $username=cookie("username");
        $areacode=cookie("areacode");
        $truename=cookie("truename");
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
        $recite = M('user_recite_question');
        $rs = $recite->where('app_id=%d and course_id=%d and question_id=%d and username="%s" and areacode="%s"',$app_id,$course_id,$que_id,$username,$areacode)->find();
        if(count($rs) > 0){
            $recite->useranswer = $useranswer;
            $recite->score = $score;
            $recite->where('id=%d',$rs['id'])->save();
        }
        else{
            $recite->app_id = $app_id;
            $recite->course_id = $course_id;
            $recite->username = $username;
            $recite->areacode = $areacode;
            $recite->truename = $truename;
            $recite->question_id = $que_id;
            $recite->useranswer = $useranswer;
            $recite->score = $score;
            $recite->add();
        }

    }
}
