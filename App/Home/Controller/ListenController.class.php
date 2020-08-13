<?php

namespace Home\Controller;

use Think\Controller;

class ListenController extends Controller {

    public function index() {
        $this->display();
    }
    
    public function listentopic(){
        $sqlquery = M();
        $gradeid = I('gradeid/d',88);
        S('ztgrade',null);
        S('zttype',null);
        S('ztyear',null);
        S('ztprovince',null);
        $zttype = S('zttype');
        if(empty($zttype)){
            $sql = "select a.id,a.title from engs_dictionary a,engs_exams b where code='examtype' and a.id = b.typeid and b.exams_classid = 3 and b.isdel = 1 and b.levelid = ".$gradeid." and b.state = 4 group by b.typeid order by a.sortid desc,a.id desc";
            $zttype = $sqlquery->query($sql);
            S('zttype',$zttype);
        }
        $ztyear = S('ztyear');
        if(empty($ztyear)){
            $sql = "select a.id,a.title from engs_dictionary a,engs_exams b where code='year' and a.id=b.yearid and b.exams_classid = 3 and b.isdel = 1 and b.levelid = ".$gradeid." and b.state = 4 group by b.yearid  order by a.sortid desc,a.id desc";
            $ztyear = $sqlquery->query($sql);
            S('ztyear',$ztyear);
        }
        $ztprovince = S('ztprovince');
        if(empty($ztprovince)){
            $sql = "select a.id,a.title from engs_dictionary a,engs_exams b where code='province' and a.id=b.provinceid and b.exams_classid = 3 and b.isdel = 1 and b.levelid = ".$gradeid." and b.state = 4 group by b.provinceid order by a.sortid desc,a.id desc";
            $ztprovince = $sqlquery->query($sql);
            S('ztprovince',$ztprovince);
        }
        $this->assign('ztgrade', $ztgrade);
        $this->assign('zttype', $zttype);
        $this->assign('ztyear', $ztyear);
        $this->assign('ztprovince', $ztprovince);
        $this ->assign('gradeid',$gradeid);
        unset($ztgrade);
        unset($ztgrade);
        unset($ztyear);
        unset($ztprovince);
        unset($sql);
        $this->display('listentopic');

    }
    /**
     * 获取版本下听力训练的单元信息
     */
    public function exams_isexists() {   //获取版本下听力训练的单元信息
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        S($gradeid.$termid.$versionid.'exams_isexists_list',null);
        $exams_isexists_list = S($gradeid.$termid.$versionid.'exams_isexists_list');    //获取缓存内容
        if(empty($exams_isexists_list)){
            $sqlquery = M();
          //  $sql = 'select a.ks_code,a.ks_name_short from engs_rms_unit as a,engs_exams as b where a.flag=1 and a.r_grade = ' . $gradeid . ' and a.r_volume = ' . $termid . ' and a.r_version = ' . $versionid . ' and b.exams_classid=1 and a.ks_code=b.ks_code and b.isdel =1 and b.state = 4 group by b.ks_code order by a.display_order';
            $sql = 'select a.ks_code,a.ks_name_short from engs_rms_unit as a,engs_exams as b where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and b.exams_classid !=3 and a.ks_code=b.ks_code and b.isdel =1 and b.state = 4 group by b.ks_code order by a.display_order';
            $exams_isexists_list = $sqlquery->query($sql,$gradeid,$termid,$versionid); 
            S($gradeid.$termid.$versionid.'exams_isexists_list',$exams_isexists_list);
        }
 
        $this->ajaxReturn($exams_isexists_list);
        unset($exams_isexists_list);
        unset($gradeid);
        unset($termid);
        unset($versionid);
        unset($sql);
        //$this->display();
    }

    /**
     * 获取版本下听力训练的单元信息
     */
    public function unitinfo_exams() {   //获取版本下听力训练的单元信息
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        S($gradeid.$termid.$versionid.'exams_unit_list',null);    //获取缓存内容
        $exams_unit_list = S($gradeid.$termid.$versionid.'exams_unit_list');    //获取缓存内容
        if(empty($exams_unit_list)){
                   $sqlquery = M();
            //$sql = 'select a.id,a.unitalias from engs_unit as a,engs_exams as b where a.isdel=1 and a.gradeid = ' . $gradeid . ' and a.termid = ' . $termid . ' and a.versionid = ' . $versionid . ' and b.classid=1 and a.id=b.unitid and b.isdel =1 and b.state = 4 group by b.unitid order by a.sortid,a.id';
            $sql = 'select a.ks_code,a.ks_name_short,is_unit,sum(case when b.ks_code is not null then 1 else 0 end) as examscount from engs_rms_unit as a left join engs_exams b on a.ks_code = b.ks_code and b.state=4 and b.isdel = 1 where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and a.r_subject="0003"  group by a.ks_code having is_unit+examscount>0 order by a.display_order';
            //echo $sql;
            $exams_unit_list = $sqlquery->query($sql,$gradeid,$termid,$versionid); 
            S($gradeid.$termid.$versionid.'exams_unit_list',$exams_unit_list);
        }
        $this->ajaxReturn($exams_unit_list);
        unset($exams_unit_list);
        unset($gradeid);
        unset($termid);
        unset($versionid);
        unset($sql);
    }
     
	public function get_zt_exams_list(){

		$sqlquery = M();
		$levelid = I('gradeid/d', 0);
		$yearid = I('yearid/d', 0);
		$typeid = I('typeid/d', 0);
		$provinceid = I('provinceid/d', 0);
       //S($leverid.$yearid.$typeid.$provinceid.'examslist',null);
       // $exams_list = S($leverid.$yearid.$typeid.$provinceid.'examslist');
        if($levelid != '0'){
            $sqlstr = 'and a.levelid = '.$levelid;
        }
		if($typeid != '0'){
			$sqlstr .= ' and typeid = '.$typeid;
		}
		if($provinceid != '0'){
			$sqlstr .= ' and provinceid = '.$provinceid;
		}
		if($yearid != '0'){
			$sqlstr .= ' and yearid = '.$yearid;
		}
		 //$sql = 'select a.id,a.name,(select count(*) from engs_exams_paper b where b.examsid = a.id and b.classid != 0) as papernum,(SELECT count(*) from engs_study_exams c where c.examsid = a.id)  as studynum  from engs_exams a where a.classid =2 and a.isdel = 1 and a.state = 4 '.$sqlstr;
       
                $sql = 'select a.id,a.name,a.quescount as papernum,(select count(*) from engs_study c where c.engs_studyid = a.id and c.studytype=3 and c.ks_code = "0")  as studynum from engs_exams a where a.exams_classid =3 and a.isdel = 1 and a.state = 4  '.$sqlstr.' order by a.yearid desc, a.sortid,a.id';
                //echo $sql;exit;
                $exams_list = $sqlquery->query($sql);
                //S($leverid.$yearid.$typeid.$provinceid.'examslist',$exams_list);
        

        $this->assign('exams_list', $exams_list);
        unset($levelid);
        unset($yearid);
        unset($provinceid);
        unset($typeid);
        unset($exams_list);
        unset($sql);
		$this->display('get_zt_exams_list');

	}
    /**
     * 获取单元下听力训练的试卷信息
     */
    public function get_exams_list() {
        $unitid = I('unitid/s', 0);
        $exams_list = S($unitid.'exams_list');    //获取缓存内容
        if(empty($exams_list)){
            $sqlquery = M();
            $sql = 'select a.id,a.name,a.quescount as papernum,(select count(*) from engs_study c where c.engs_studyid = a.id and c.studytype=3 and c.ks_code != "0")  as studynum from engs_exams a where a.ks_code = "%s" and a.isdel = 1 and a.state = 4 order by a.sortid,a.id' ;
            $exams_list = $sqlquery->query($sql, $unitid);
            S($unitid.'exams_list',$exams_list);
        }
        
        $this->assign('unitid', $unitid);
        $this->assign('exams_list', $exams_list);
        unset($exams_list);
        unset($unitid);
        unset($sql);
        $this->display('get_exams_list');
    }

    /**
     * 获取试卷内的题型信息
     */
    public function listenshow() {
        $examsid = I('examsid/d', 0);
        $unitid = I('unitid/s', 0);
        $from_platform = I('from_platform/s', 'exams'); 
        
        $sqlquery = M();
        if($from_platform == 'exams'){      
           // echo "没有缓存";
            
            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id=%d';
            $examsdata = $sqlquery -> query($sql,$examsid);
            $exams_tts_type = $examsdata[0]['tts_type'];
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid=%d and parentid = 0 and isdel = 1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql,$examsid);
            foreach ($stemdata as $key => $value) {
                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid=%d and parentid=%d and tts_type="st" and isdel = 1 order by sortid,id';     //题干的听力材料和mp3
                $stem_tts = $sqlquery -> query($sql,$examsid,$value['id']);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                        $score = 0;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid=%d and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql,$value['id']);
                        foreach ($question as $quekey => $quevalue) { 
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = %d and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql,$quevalue['id']);
                                $question[$quekey]['items'] = $que_items;
                                if($quevalue['typeid'] == '1'){
                                    $sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                }
                                else{
                                    $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                }
                                $queanswer = $sqlquery -> query($sql);
                                //$score += ceil($value['question_score']) * count($queanswer);
                                $score += $value['question_score'] * count($queanswer);
                            }
                            else{
                                $question[$quekey]['items'] = 0;
                                 $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                               // $score += ceil($value['question_score']) * count($queanswer);
                                $score += $value['question_score'] * count($queanswer);
                            }

                            $question[$quekey]['que_answer'] = $queanswer;
                            //问题的听力材料
                            if($exams_tts_type == '1'){             //系统生成的听力材料
                                 $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid=%d and parentid=%d and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';
                            }
                            else{
                                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid=%d and parentid=%d and tts_type="qe" and isdel=1 order by sortid,id';
                            }
                            $que_tts = $sqlquery -> query($sql,$examsid,$quevalue['id']);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid=%d and parentid=%d and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql,$examsid,$quevalue['id']);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            $stemdata[$key]['questypeid'] = $quevalue['typeid'];
                            # code...
                        }
                        if($stemdata[$key]['question_score'] == ceil($stemdata[$key]['question_score'])){
                            $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        }
                        $stemdata[$key]['question'] = $question;
                       // $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;


                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $score2 = 0;
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {                         
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
                               // $score2 += ceil($childvalue['question_score']) * count($child_queanswer);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                            }
                            else{
                                $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'].' and isdel=1 order by sortid,id';     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                //$score2 += ceil($childvalue['question_score']) * count($child_queanswer);
                                $score2 += $childvalue['question_score'] * count($child_queanswer);
                                $child_question[$child_quekey]['items'] = 0;
                            }
                             $child_question[$child_quekey]['que_answer'] = $child_queanswer;
                             $stem_children[$keychild]['questypeid'] = $child_quevalue['typeid'];
                             $stemdata[$key]['questypeid'] = $child_quevalue['typeid'];
                        }

                        $stem_children[$keychild]['question'] = $child_question;
                        //$stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        //$score = ceil($childvalue['question_score']) * count($child_question);
                        if($stem_children[$keychild]['question_score'] == ceil($stem_children[$keychild]['question_score'])){
                            $stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        }
                        //echo $score2."<br>";
                        $stem_children[$keychild]['total_score']= $score2;
                        $stemdata[$key]['total_score'] = $score2;
                        $total_score += $score2;
                        $stemdata[$key]['question_score'] = $stem_children[0]['question_score'];

                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                    
                }

            }
            
            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$examsid.' and (tts_type ="eb" || tts_type="ed") and isdel=1 order by sortid,id';   //试卷头尾的听力材料
            $examstts = $sqlquery -> query($sql);
            foreach ($examsdata as $examskey => $examsvalue) {
                $examsdata[$examskey]['stem'] = $stemdata;
                $examsdata[$examskey]['total_score'] = $total_score;
                if($examsdata[$examskey]['header_content'] == null){
                    $examsdata[$examskey]['header_content'] = '';
                }
                $examsdata[$examskey]['exams_tts'] = count($examstts) == 0 ? '0' : $examstts;
            }
            
            //var_dump($examsdata);exit;
        }
        else{
            $sql = "select data,ks_code,name from engs_question_paper where id = ".$examsid;
            $question = $sqlquery->query($sql);
            $wordlist = $question[0]['data'];

            //$question = json_decode(urldecode($question[0]['data']));
            $wordlist=json_decode(htmlspecialchars_decode($wordlist));
            $wordlist = object_to_array($wordlist);
            $examsdata[0]['id'] = $examsid;
            $examsdata[0]['ks_code'] = $question[0]['ks_code'];
            $examsdata[0]['exams_classid'] = 2;
            $examsdata[0]['exams_times'] = 15;
            $examsdata[0]['tts_type'] = 1;
            $examsdata[0]['header_content'] = '';
            $examsdata[0]['stopsecond'] = 3;
            $examsdata[0]['name'] = $question[0]['name'];
            foreach ($wordlist as $key => $value) {
                $newquearray[$key]['id'] = $value['id'];
                $newquearray[$key]['stem_num'] = $key+1;
                $newquearray[$key]['stem_type'] = 1;
                $newquearray[$key]['question_playtimes'] = "1";
                $newquearray[$key]['question_score'] =1;
        
                if($value['stemcontenttype'] == 1){
                    $newquearray[$key]['content'] = "第".($key+1)."大题-请根据图片选择正确选项";
                    //$newquearray[$key]['tcontent'] = '<img src="'.C('word_pic_path').$value['stemcontent'].'" />';
                }
                else{
                    $newquearray[$key]['content'] = "第".($key+1)."大题-请根据听到的内容选择正确的选项";
                    //$newquearray[$key]['tcontent'] ='';
                }

                $newquearray[$key]['stem_playtimes'] = "1"; 
                $newquearray[$key]['stoptimes'] = "5";
               
                $tts['id']='0';
                $tts['tts_type']='0';
                $tts['parentid']='0';
                $tts['flag_content']='';
                $tts['tts_content']=$value['tts'];
                $tts['tts_stoptime']='2';
                $tts['tts_mp3']=$value['mp3'];
                $tts['mp3type']=$value['stemcontenttype'];
                $tts['st_flag']='1';
                $newquearray[$key]['stem_tts'][0] = $tts;
                $newquearray[$key]['questypeid'] = "1";
                $questioninfo[0]['id']=$value['id'];
                $questioninfo[0]['stemid']=$value['id'];
                $questioninfo[0]['question_num']=$key+1;
                $questioninfo[0]['typeid']=1;
                if($value['stemcontenttype'] == 1){
                    $questioninfo[0]['tcontent'] = '<img src="'.PROTOCOL."://".C('word_pic_path').$value['stemcontent'].'" />';
                }
                else{
                    $newquearray[$key]['content'] = "第".($key+1)."大题-请根据听到的内容选择正确的选项";
                    $questioninfo[0]['tcontent'] = '';
                }

                $questioninfo[0]['acontent']='';
                $questioninfo[0]['itemtype']=$value['itemtype'];
                $questioninfo[0]['display']=0;
                $questioninfo[0]['tips']=1;
                $questioninfo[0]['stoptimes']=5;
                $questioninfo[0]['items']=$value['items'];
                $answer['answer'] = $value['answer'];
                $answer['answer_num'] = "0";
                $answer['questionsid'] = $value['id'];
                $questioninfo[0]['items']=$value['items'];
                $questioninfo[0]['que_answer'][0] = $answer;
                $questioninfo[0]['que_tts'][0] = $tts;
                $questioninfo[0]['que_tts_noqn'][0] = $tts;
                $newquearray[$key]['question'] = $questioninfo;
            }
            $examsdata[0]['stem'] = $newquearray;
            $examsdata[0]['total_score'] = count($wordlist);
            $examsdata[0]['header_content'] = '';
            $examsdata[0]['exams_tts'] = '0';
            //var_dump($examsdata);
        }
        
        //var_dump($stemdata);
       $this -> ajaxReturn($examsdata);
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
       unset($examsdata);
       unset($sql);
        //$this -> assign('examsdata',$examsdata);
       // $this -> display();

    }

    function test() {
        $str1 = "Tom likes ##答案[riding]## a bike";
        $str2 = '#{2}答案\\[(.*?)\\]#{2}';
        $str3 = '<input type="text" size="1" maxlength="1">';
        $str4 = preg_replace('[##.*?##]', $str3, $str1);
        echo "sss" . $str4;
    }

	/**
 * 保存听力训练的学习记录
 * @param type $unitid
 * @param type $chapterid
 */
    public function save_study_exams_info() {
        $Model = M('study');
        if (cookie('ut')) {
            $username = cookie('username');
            $areacode = cookie('areacode');
			$examsid = I('examsid/d', 0);
			$score = I('score/s',0);
            $complate_time = I('complate_time/d',0);
            $curtime = date("Y-m-d");
            $crutimearray = explode('-', $curtime);
            $data['ks_code'] = I('unitid/s', 0);
            $data['engs_studyid'] = $examsid;
			$data['score'] = floatval($score);
			$data['errornum'] = I('errornum/d', 0);
            $data['username'] = $username;
            $data['usertype'] = cookie('usertype');
            $data['areacode'] = $areacode;
            $data['addtimey'] = $crutimearray[0];
            $data['addtimem'] = $crutimearray[1];
            $data['addtimed'] = $crutimearray[2];
            $data['addtime'] = $curtime;
            $data['studytype'] = 3;
            $data['complate_time'] = $complate_time;
            $exams = $Model->where('engs_studyid=' . $examsid . ' and username="' . $username . '" and areacode="'.$areacode.'" and studytype =3')->field('addtime')->limit(1)->select();
            if (count($exams) > 0) {
                if ($exams[0]['addtime'] != $curtime) {
                    $Model->data($data)->add();
                }
            } else {
                $Model->data($data)->add();
            }
        }
    }
}
