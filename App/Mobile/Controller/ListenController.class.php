<?php
namespace Mobile\Controller;
use Think\Controller;
class ListenController extends CheckController {

	public function index(){
        $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
        $versionid = cookie('engm_versionid'); 
        $gradeid = cookie('engm_gradeid'); 
        $termid = cookie('engm_termid'); 
        $username = cookie('engm_username');
        $rms_unit=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("max(update_exams_time) as exams_time")->group("r_grade,r_volume,r_version")->find();
        $fs=file_exists($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsunitidg".$gradeid."v".$volumeid."v".$versionid.".html");
        if($rms_unit["exams_time"]<filemtime($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsunitidg".$gradeid."v".$volumeid."v".$versionid.".html")&&$fs==1){
          $filename="Cache/Mobile/Listen/examsunitidg".$gradeid."v".$termid."v".$versionid;
          $this->display($filename);
        }else{
            $versionid = cookie('engm_versionid'); 
            $gradeid = cookie('engm_gradeid'); 
            $termid = cookie('engm_termid');
            $arr["grade"]=$gradeid;
            $arr["volume"]=$termid;
            $arr["version"]=$versionid;
            //传递年级教材学期名字
            $this->vgt_name = cookie('engm_gradename') .'.'. cookie('engm_versionname') .'.'. cookie('engm_termname');
            $unitlist=D("rms_unit")->getUnitListData($arr,2);
            $this->assign("unitlist",$unitlist["result"]);
            $this->assign("isunit",$unitlist["unit"]);
            $this->assign("unitlen",$unitlist["len"]);
            $this->buildHtml("examsunitidg".$gradeid."v".$termid."v".$versionid, HTML_PATH . '/Mobile/Listen/', 'index', 'utf8');
            $this->display();
        }
    }
	public function show_exams_list(){
        $unitid = I("unitid/s");
        $versionid = cookie('engm_versionid'); 
        $gradeid = cookie('engm_gradeid'); 
        $termid = cookie('engm_termid'); 
        $rms_unit=M("rms_unit")->where("ks_code='%s'",$unitid)->field("max(update_exams_time) as exams_time")->group("r_grade,r_volume,r_version")->find();
        $fs=file_exists($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsunitidshowlistg".$gradeid."v".$termid."v".$versionid."u".$unitid.".html");
        if($rms_unit["exams_time"]<filemtime($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsunitidshowlistg".$gradeid."v".$termid."v".$versionid."u".$unitid.".html")&&$fs==1){
          $filename="Cache/Mobile/Listen/examsunitidshowlistg".$gradeid."v".$termid."v".$versionid."u".$unitid;
          $this->display($filename);
        }else{
            $wordbcount = cookie('engm_wordbook_count');
            $recordcount= cookie('engm_record_count');
            $this -> assign("recordcount",$recordcount);
            $this -> assign("wordbcount",$wordbcount);
            $this->vgt_name = cookie('engm_gradename') .'.'. cookie('engm_versionname') .'.'. cookie('engm_termname');
            
            $sql = 'select id,name,ks_code from engs_exams where exams_classid !=3 and isdel = 1 and state=4 and ks_code="'.$unitid.'"';
            $model = new \Think\Model();
            //echo $sql;
            $exmaslist = $model->query($sql);
            //var_dump($exmaslist);exit();
            $this->assign('exmaslist', $exmaslist);
            $this->buildHtml("examsunitidshowlistg".$gradeid."v".$termid."v".$versionid."u".$unitid, HTML_PATH . '/Mobile/Listen/', 'show_exams_list', 'utf8');
            $this-> display();
        }
	}


    public function show_exams_info(){
         $examsid=I("examsid/d");
         $unitid=I("unitid/s");
         $this->assign("examsid",$examsid);
         $this->assign("unitid",$unitid);
         $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
         $this->display();
     }
    


	// public function show_exams_info1(){
 //        $examsid=I("examsid/d");
 //        $unitid=I("unitid/s");
 //        $this->assign("unitid",$unitid);
 //        $fs=file_exists($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsinfou".$unitid."e".$examsid.".html");
 //        //判断试卷修改时间
 //        $rms_unit=M("exams")->where("ks_code='%s' and id=%d and isdel=1",$unitid,$examsid)->field("max(UNIX_TIMESTAMP(pubtime)) as exams_time")->find();
 //        if($rms_unit["exams_time"]<filemtime($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Listen/examsinfou".$unitid."e".$examsid.".html")&&$fs==1){
 //          $filename="Cache/Mobile/Listen/examsinfou".$unitid."e".$examsid;
 //          $this->display($filename);
 //        }else{
 //            $en_exams=M("exams");
 //            $en_rms=M("rms_unit");
 //            $en_exams_tts=M("exams_tts");
 //            $en_exams_stem=M("exams_stem");
 //            $en_exams_questions=M("exams_questions");
 //            $en_exams_answer=M("exams_questions_answer");
 //            $en_exams_items=M("exams_questions_items");
 //            $en_exams_key=M("exams_questions_keys");
 //            //找出试卷
 //            $exams_rs=$en_exams->where("id=%d",$examsid)->find();
 //            $rms=$en_rms->where("ks_code='%s'",$exams_rs["ks_code"])->find();
 //            $exams_rs["unitname"]=getchar($rms['c1'],"GBK","UTF-8");
 //            $exams_rs["r_grade"]=getchar($rms['r_grade'],"GBK","UTF-8");
 //            $exams_rs["r_volume"]=getchar($rms['r_volume'],"GBK","UTF-8");
 //            $exams_rs["r_version"]=getchar($rms['r_version'],"GBK","UTF-8");
 //            $exams_tts=$en_exams_tts->where("(tts_type='eb' or tts_type='ee') and examsid=%d and parentid=%d",$examsid,$examsid)->select();
 //            $exams_rs["tts"]=$exams_tts;
 //            //试卷下面大题
 //            $exams_stem_rs=$en_exams_stem->where("examsid=%d and parentid=0",$examsid)->order("sortid,id")->select();
 //            //找出没到大题下面的小题
 //            foreach($exams_stem_rs as $stemk=>$stemv){
 //                $exams_stem_tts=$en_exams_tts->where(" examsid=%d and parentid=%d and tts_type!='qn'",$examsid,$stemv["id"])->select();
 //                $exams_stem_rs[$stemk]["tts"]=$exams_stem_tts;
 //                $exams_stem_son=$en_exams_stem->where("examsid=%d and parentid=%d",$examsid,$stemv["id"])->order("sortid,id")->select();
 //                if(empty($exams_stem_son)){
 //                    $exams_stem_rs[$stemk]["son"]=[];
 //                    $sql="SELECT DISTINCT t.*,(SELECT tts_content FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS tts_content,(SELECT tts_mp3 FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS tts_mp3,(SELECT voiceid FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS voiceid,(SELECT id FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS question_num_id FROM engs_exams_questions t  WHERE   stemid = %d";
 //                    //echo $sql."||".$stemv["id"];
 //                    $stem_questionlists_rs=M()->query($sql,$stemv["id"]);
 //                    foreach($stem_questionlists_rs as $questionsk=>$questionsv){
 //                         $questions_tts=$en_exams_tts->where("examsid=%d and parentid=%d and tts_type!='qn'",$examsid,$questionsv["id"])->select();
 //                         $stem_questionlists_rs[$questionsk]["tts"]=$questions_tts;
 //                         //查找选项
 //                         $questions_items=$en_exams_items->where("questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
 //                         $stem_questionlists_rs[$questionsk]["items"]=$questions_items;
 //                         //找出答案
 //                         $questions_answer=$en_exams_answer->where("questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
 //                         $stem_questionlists_rs[$questionsk]["answer"]=$questions_answer;
 //                         //找出关键词
 //                         $questions_key=$en_exams_key->where("questionsid=%d",$questionsv["id"])->select();
 //                         $stem_questionlists_rs[$questionsk]["keys"]=$questions_key;
 //                    }
 //                    $exams_stem_rs[$stemk]["questionlist"]=$stem_questionlists_rs; 
 //                }else{
 //                    $exams_stem_rs[$stemk]["questionlist"]=[];
 //                    foreach($exams_stem_son as $sonkey=>$sonvalue){
 //                        $exams_son_stem_tts=$en_exams_tts->where(" examsid=%d and parentid=%d and tts_type!='qn'",$examsid,$sonvalue["id"])->select();
 //                        $exams_stem_son[$sonkey]["tts"]=$exams_son_stem_tts;
 //                        $sql="SELECT DISTINCT t.*,(SELECT tts_content FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS tts_content,(SELECT tts_mp3 FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS tts_mp3,(SELECT voiceid FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS voiceid,(SELECT id FROM engs_exams_tts s WHERE t.id = s.parentid AND s.tts_type = 'qn') AS question_num_id FROM engs_exams_questions t  WHERE   stemid = %d";
 //                        $stem_questionlists_rs=M()->query($sql,$sonvalue["id"]);
 //                        foreach($stem_questionlists_rs as $questionsk=>$questionsv){
 //                             $questions_tts=$en_exams_tts->where("examsid=%d and parentid=%d and tts_type!='qn'",$examsid,$questionsv["id"])->select();
 //                             $stem_questionlists_rs[$questionsk]["tts"]=$questions_tts;
 //                             //查找选项
 //                             $questions_items=$en_exams_items->where("questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
 //                             $stem_questionlists_rs[$questionsk]["items"]=$questions_items;
 //                             //找出答案
 //                             $questions_answer=$en_exams_answer->where("questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
 //                             $stem_questionlists_rs[$questionsk]["answer"]=$questions_answer;
 //                             //找出关键词
 //                             $questions_key=$en_exams_key->where("questionsid=%d",$questionsv["id"])->select();
 //                             $stem_questionlists_rs[$questionsk]["keys"]=$questions_key;
 //                        }
 //                        $exams_stem_son[$sonkey]["questionlist"]=$stem_questionlists_rs;
 //                    }
                    
 //                    $exams_stem_rs[$stemk]["son"]=$exams_stem_son;
 //                }    
 //            }
 //            $exams_rs["paper"]=$exams_stem_rs;
 //            $this->assign("exams",$exams_rs);
 //            $this->buildHtml('examsinfou'.$unitid."e".$examsid, HTML_PATH . '/Mobile/Listen/', 'show_exams_info', 'utf8');
 //            $this->display();
 //        }
 //    }


    public function getStemTts(){
        $examsid = I('examsid/d', 0);
        $stemid = I('stemid/d', 0);
       //S($examsid.$unitid.'examsdata',null);
        // $examsdata = S($examsid.$unitid.'examsdata');    //获取缓存内容
        // if(empty($examsdata)){      
           // echo "没有缓存";
            $sqlquery = M();
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and id='.$stemid.' and parentid = 0 and isdel=1 order by id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$value['id'].' and tts_type="st" and isdel=1 order by sortid,id';     //题干的听力材料和mp3
                $stem_tts = $sqlquery -> query($sql);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by id,sortid,question_num';    
                        $question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) { 
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql);
                                $question[$quekey]['items'] = $que_items;
                                $sql = 'select a.flag as answer from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                $queanswer = $sqlquery -> query($sql);
                            }
                            else{
                                $question[$quekey]['items'] = 0;
                                 $sql = 'select answer from engs_exams_questions_answer where questionsid = ' . $quevalue['id'];     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                            }

                            $question[$quekey]['que_answer'] = $queanswer;

                            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            # code...
                        }
                        $stemdata[$key]['question'] = $question;
                        $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        $score = ceil($value['question_score']) * count($question);
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;


                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel=1 order by id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {

                        $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {
                            
                           
                            if($child_quevalue['typeid'] == '1' || $child_quevalue['typeid'] == '4'){       //如果是选择题或排序题就获取选项
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $child_quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $child_que_items = $sqlquery->query($sql);
                                $child_question[$child_quekey]['items'] = $child_que_items;
                                $sql = 'select a.flag as answer from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                $child_queanswer = $sqlquery -> query($sql);
                            }
                            else{
                                $sql = 'select answer from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'];     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                $child_question[$child_quekey]['items'] = 0;
                            }
                             $child_question[$child_quekey]['que_answer'] = $child_queanswer;
                        }
                        $stem_children[$keychild]['question'] = $child_question;
                        $stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        $score = ceil($childvalue['question_score']) * count($child_question);
                        $score2 += ceil($childvalue['question_score']) * count($child_question);
                        $stem_children[$keychild]['total_score']= $score;
                        $total_score += $score;

                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                    $stemdata[$key]['total_score'] = $score2;
                }

            }
            $sql = 'select id,ks_code,name,exams_times,tts_type,header_content,stopsecond from engs_exams where id='.$examsid;
            $examsdata = $sqlquery -> query($sql);
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
        //     S($examsid.$unitid.'examsdata',$examsdata);
            
        // }
        
        //var_dump($stemdata);
       $this -> ajaxReturn($examsdata);

        //$this -> assign('examsdata',$examsdata);
       // $this -> display();

    }


    // public function show_exam_info(){
    //     //$examsid=I("examsid/d");
    //     //echo $examsid;
    //     //$this->assign("examsid",$examsid);
    //     $this->display();
    // }



     /**
     * 获取试卷内的题型信息txt文件缓存
     */
    public function listenshow() {

        //校验文件是否存在
        $examsid = I('examsid/d', 0);
        //校验试卷是否更改
        $rms_unit=M("exams")->where("id=%d",$examsid)->field("max(UNIX_TIMESTAMP(pubtime)) as exams_time")->find();
        $fs=file_exists(C('CACHE_FILE_ROOT')."/Exams/".$examsid.'examsdata.txt');
        if($rms_unit["exams_time"]<filemtime(C('CACHE_FILE_ROOT')."/Exams/".$examsid.'examsdata.txt')&&$fs){
            //echo "fasdfasd";
            //$filename="Cache/Mobile/Textread/indexg".$gradeid."v".$termid."v".$versionid;
            //$this->display($filename);
        }else{
            if(!file_exists(C('CACHE_FILE_ROOT')."/Exams/".$examsid.'examsdata.txt')){
                $myfile = fopen(C('CACHE_FILE_ROOT')."/Exams/".$examsid.'examsdata.txt', "w");
                fclose($myfile);
            }
        
        //S($examsid.'examsdata',null);
        //$examsdata = S($examsid.'examsdata');    //获取缓存内容
        //if(empty($examsdata)){      
           // echo "没有缓存";
            $sqlquery = M();
            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id='.$examsid;
            $examsdata = $sqlquery -> query($sql);
            $exams_tts_type = $examsdata[0]['tts_type'];
            $sql = 'select id,stem_num,examsid,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel=1 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$value['id'].' and tts_type="st" and isdel=1  order by sortid,id';     //题干的听力材料和mp3
                $stem_tts = $sqlquery -> query($sql);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                        $score = 0;
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) { 
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql);
                                $question[$quekey]['items'] = $que_items;
                                if($quevalue['typeid'] == '1'){
                                   // $sql = 'select a.flag as answer,b.answer_num from engs_exams_questions_items a,engs_exams_questions_answer b where b.isdel=1 and a.isdel=1 and a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' group by b.answer order by b.sortid,b.id';
								   $sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                }
                                else{
                                    $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                }
                                $queanswer = $sqlquery -> query($sql);
                                $score += ceil($value['question_score']) * count($queanswer);
                            }
                            else{
                                $question[$quekey]['items'] = 0;
                                 $sql = 'select answer,answer_num from engs_exams_questions_answer where questionsid = ' . $quevalue['id'].' and isdel=1 order by sortid,id';     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                                $score += ceil($value['question_score']) * count($queanswer);
                            }

                            $question[$quekey]['que_answer'] = $queanswer;
                            //问题的听力材料
                            if($exams_tts_type == '1'){             //系统生成的听力材料
                                 $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';
                            }
                            else{
                                $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';
                            }
                            $que_tts = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and  isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            # code...
                        }
                        $stemdata[$key]['question'] = $question;
                        $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        //$score = ceil($value['question_score']) * count($question);
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;


                }   
                else{                           //组合大题
                    $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where isdel=1 and  examsid='.$examsid.' and parentid = '.$value['id'].'  and isdel=1 order by sortid,id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {
                        $score2 = 0;
                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by sortid,id,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {                         
                            if($child_quevalue['typeid'] == '1' || $child_quevalue['typeid'] == '4'){       //如果是选择题或排序题就获取选项
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $child_quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $child_que_items = $sqlquery->query($sql);
                                $child_question[$child_quekey]['items'] = $child_que_items;
                                if($child_quevalue['typeid'] == '1'){
                                   // $sql = 'select a.flag as answer,b.answer_num from engs_exams_questions_items a,engs_exams_questions_answer b where a.isdel=1 and b.isdel=1 and  a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' group by b.answer order by b.sortid,b.id';
								   $sql = 'select a.flag as answer,b.answer_num,b.questionsid from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' and a.questionsid= ' . $child_quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                }
                                else{
                                    $sql = 'select answer,answer_num from engs_exams_questions_answer where isdel=1 and questionsid = ' . $child_quevalue['id'].' order by sortid,id';     //问题的答案
                                }
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += ceil($childvalue['question_score']) * count($child_queanswer);
                            }
                            else{
                                $sql = 'select answer,answer_num from engs_exams_questions_answer where isdel=1 and questionsid = ' . $child_quevalue['id'].' order by sortid,id';     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                $score2 += ceil($childvalue['question_score']) * count($child_queanswer);
                                $child_question[$child_quekey]['items'] = 0;
                            }
                             $child_question[$child_quekey]['que_answer'] = $child_queanswer;
                        }
                        $stem_children[$keychild]['question'] = $child_question;
                        $stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        $score = ceil($childvalue['question_score']) * count($child_question);
                        //$score2 += ceil($childvalue['question_score']) * count($child_question);
                        $stem_children[$keychild]['total_score']= $score2;
                        $total_score += $score2;
                        $stemdata[$key]['question_score'] = ceil($stem_children[0]['question_score']);

                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                    $stemdata[$key]['total_score'] = $score2;
                }

            }
            
            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where isdel=1 and examsid='.$examsid.' and parentid='.$examsid.' and (tts_type ="eb" || tts_type="ed") order by sortid,id';   //试卷头尾的听力材料
            $examstts = $sqlquery -> query($sql);
            foreach ($examsdata as $examskey => $examsvalue) {
                $examsdata[$examskey]['stem'] = $stemdata;
                $examsdata[$examskey]['total_score'] = $total_score;
                if($examsdata[$examskey]['header_content'] == null){
                    $examsdata[$examskey]['header_content'] = '';
                }
                $examsdata[$examskey]['exams_tts'] = count($examstts) == 0 ? '0' : $examstts;
            }
            //S($examsid.$unitid.'examsdata',$examsdata);
            
        //}
        $data=json_encode($examsdata);
        file_put_contents(C('CACHE_FILE_ROOT')."/Exams/".$examsid.'examsdata.txt',$data);
    }


    //echo C('CACHE_FILE_ROOT')."/Exams/".$examsid."examsdata.txt";
    $redata=file_get_contents(C('CACHE_FILE_ROOT')."/Exams/".$examsid."examsdata.txt", "rb");
    //var_dump($redata);

    echo $redata;

    }



    /**
 * 保存听力训练的学习记录
 * @param type $unitid
 * @param type $chapterid
 */
    public function save_study_exams_info() {
        $Model = M('study');
        //if (cookie('ut')) {
            // $username = cookie('engm_username');
            // $areacode = cookie('engm_areacode');
            // $examsid = I('examsid/d', 0);
            // $score = I('score/s',0);
            // $complate_time = I('complate_time/d',0);
            // $curtime = date("Y-m-d");
            // $crutimearray = explode('-', $curtime);
            // $data['ks_code'] = I('unitid/s', 0);
            // $data['engs_studyid'] = $examsid;
            // $data['score'] = floatval($score);
            // $data['errornum'] = I('errornum/d', 0);
            // $data['username'] = $username;
            // $data['usertype'] = cookie('engm_usertype');
            // $data['areacode'] = $areacode;
            // $data['addtimey'] = $crutimearray[0];
            // $data['addtimem'] = $crutimearray[1];
            // $data['addtimed'] = $crutimearray[2];
            // $data['addtime'] = $curtime;
            // $data['studytype'] = 3;
            // $data['complate_time'] = $complate_time;
            // $exams = $Model->where('engs_studyid=' . $examsid . ' and username="' . $username . '" and areacode="'.$areacode.'" and studytype =3')->field('addtime')->limit(1)->select();
            // if (count($exams) > 0) {
            //     if ($exams[0]['addtime'] != $curtime) {
            //         $Model->data($data)->add();
            //     }
            // } else {
            //     $Model->add($data);
            // }
        //}
    }
}
