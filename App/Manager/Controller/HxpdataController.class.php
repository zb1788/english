<?php

namespace Manager\Controller;

use Think\Controller;

class HxpdataController extends CheckController {

    public function import() {
        $old_unit = I('oldunit/d', 0);
        $new_unit = I('newunit/d', 0);
        if ($old_unit != '0' && $new_unit != '0') {
            $beg_time = time();
            echo '开始执行' . date('Y-m-d H:i:s', $beg_time) . '<br \>';
            $engs_word = M('word');
            $engs_word->where('unitid =' . $new_unit)->delete();
            $rs = $engs_word->where('unitid =' . $old_unit)->order('sortid asc,id asc')->select();
            foreach ($rs as $key => $value) {
                $engs_word->unitid = $new_unit;
                $engs_word->word = $value['word'];
                $engs_word->wordid = $value['wordid'];
                $engs_word->explainsid = $value['explainsid'];
                $engs_word->isstress = $value['isstress'];
                $engs_word->isdel = $value['isdel'];
                $engs_word->add();
            }
            $engs_text_chapter = M('text_chapter');
            $engs_text = M('text');
            $engs_text_chapter->where('unitid =' . $new_unit)->delete();
            $rs1 = $engs_text_chapter->where('unitid =' . $old_unit)->order('sortid asc,id asc')->select();
            foreach ($rs1 as $key1 => $value1) {
                $engs_text_chapter->unitid = $new_unit;
                $engs_text_chapter->chapter = $value1['chapter'];
                $engs_text_chapter->sortid = $value1['sortid'];
                $engs_text_chapter->isdel = $value1['isdel'];
                $engs_text_chapter->issection = $value1['issection'];
                $newid = $engs_text_chapter->add();
                $engs_text->where('chapterid =' . $newid)->delete();
                $rstext = $engs_text->where('chapterid =' . $value1['id'])->order('sortid asc,id asc')->select();
                foreach ($rstext as $key2 => $value2) {
                    $engs_text->chapterid = $newid;
                    $engs_text->sectionid = $value2['sectionid'];
                    $engs_text->enbefore = $value2['enbefore'];
                    $engs_text->encontent = $value2['encontent'];
                    $engs_text->cncontent = $value2['cncontent'];
                    $engs_text->sortid = $value2['sortid'];
                    $engs_text->mp3 = $value2['mp3'];
                    $engs_text->addtime = $value2['addtime'];
                    $engs_text->voiceid = $value2['voiceid'];
                    $engs_text->stateid = $value2['stateid'];
                    $engs_text->isdel = $value2['isdel'];
                    $engs_text->isexample = $value2['isexample'];
                    $engs_text->add();
                }
            }
            $end_time = time();
            echo '执行完成' . date('Y-m-d H:i:s', $end_time) . '------共计' . ($end_time - $beg_time) . '秒';
        } else {
            echo '参数不正确';
        }
    }

    public function show() {
        //header("Content type:text/html;charset=gb2312");
        $engs_unit = M('rms_unit');
        $engs_unitonline = M('');
        //$engs_dictionary = M('rms_dictionary');
        $sql = "SELECT a.`id`,unitname,unitalias,b.`name` as gradename,c.`name` as tremname,d.`name` as versionname,b.`detail_code` AS r_grade,c.`detail_code` AS r_volume,d.`detail_code` AS r_version FROM engs_unit_online a ,engs_grade b,engs_term c,engs_version d WHERE a.`gradeid`=b.`id` AND a.`termid`=c.`id` AND a.`versionid` = d.`id` AND a.`isdel` = 1 AND a.`gradeid` != 10  ORDER BY a.`id` ASC;";
        //$sql = "SELECT a.`id`,unitname,b.`name` as gradename,c.`name` as tremname,d.`name` as versionname,b.`detail_code` AS r_grade,c.`detail_code` AS r_volume,d.`detail_code` AS r_version FROM engs_unit_online a ,engs_grade b,engs_term c,engs_version d WHERE a.`gradeid`=b.`id` AND a.`termid`=c.`id` AND a.`versionid` = d.`id` AND a.`isdel` = 1 AND a.`gradeid` != 10 and unitname=\"Topic2 My home is in an apartment building.\"   ORDER BY a.`id` ASC;";
        $rsonline = $engs_unitonline->query($sql);
        echo "<table width='100%' border=1>";
        foreach ($rsonline as $key2 => $value2) {
            $isdui = false;
            $r_grade = $value2['r_grade'];
            $r_volume = $value2['r_volume'];
            $r_version = $value2['r_version'];
            $unitid = $value2['id'];
            $ks_name_short = $value2['unitalias'];
            $onlinename = trimall($value2['unitname']);
            $onlinename = str_replace('’', '', $onlinename);
            $onlinename = str_replace('？', '', $onlinename);
            $onlinename = str_replace('，', '', $onlinename);
            $onlinename = str_replace(',', '', $onlinename);
            $onlinename = str_replace('\'', '', $onlinename);
            $onlinename = str_replace('！', '', $onlinename);
            $onlinename = str_replace('!', '', $onlinename);
            $onlinename = str_replace('.', '', $onlinename);
            $onlinename = str_replace('?', '', $onlinename);
            $onlinename = str_replace('（', '', $onlinename);
            $onlinename = str_replace('）', '', $onlinename);
            $onlinename = str_replace('(', '', $onlinename);
            $onlinename = str_replace(')', '', $onlinename);
            $onlinename = str_replace('：', '', $onlinename);
            $onlinename = str_replace(':', '', $onlinename);
            $onlinename = strtolower($onlinename);
            $onlinename = str_replace('moudle', 'module', $onlinename);
            $temparray1 = explode('unit', $onlinename);
            $rs1 = $engs_unit->where('r_grade = "' . $r_grade . '" and r_volume="' . $r_volume . '" and r_version="' . $r_version . '" and is_unit = 0')->order('display_order asc,ks_id asc')->select();
            echo "<tr><td width=20%><table>";
            for ($i = 0; $i < count($rs1); $i++) {
                $onlinename = trimall($value2['unitname']);
                $onlinename = str_replace('’', '', $onlinename);
                $onlinename = str_replace('？', '', $onlinename);
                $onlinename = str_replace('，', '', $onlinename);
                $onlinename = str_replace(',', '', $onlinename);
                $onlinename = str_replace('\'', '', $onlinename);
                $onlinename = str_replace('！', '', $onlinename);
                $onlinename = str_replace('!', '', $onlinename);
                $onlinename = str_replace('.', '', $onlinename);
                $onlinename = str_replace('?', '', $onlinename);
                $onlinename = str_replace('（', '', $onlinename);
                $onlinename = str_replace('）', '', $onlinename);
                $onlinename = str_replace('(', '', $onlinename);
                $onlinename = str_replace(')', '', $onlinename);
                $onlinename = str_replace('：', '', $onlinename);
                $onlinename = str_replace(':', '', $onlinename);
                $onlinename = strtolower($onlinename);
                $onlinename = str_replace('moudle', 'module', $onlinename);
                $temparray1 = explode('unit', $onlinename);
                $ks_name = trimall($rs1[$i]['ks_name']);
                $ks_code = $rs1[$i]['ks_code'];
                $ks_name = str_replace('’', '', $ks_name);
                $ks_name = str_replace('？', '', $ks_name);
                $ks_name = str_replace('，', '', $ks_name);
                $ks_name = str_replace(',', '', $ks_name);
                $ks_name = str_replace('\'', '', $ks_name);
                $ks_name = str_replace('！', '', $ks_name);
                $ks_name = str_replace('!', '', $ks_name);
                $ks_name = str_replace('.', '', $ks_name);
                $ks_name = str_replace('?', '', $ks_name);
                $ks_name = str_replace('（', '', $ks_name);
                $ks_name = str_replace('）', '', $ks_name);
                $ks_name = str_replace('(', '', $ks_name);
                $ks_name = str_replace(')', '', $ks_name);
                $ks_name = str_replace('：', '', $ks_name);
                $ks_name = str_replace(':', '', $ks_name);

                $ks_name = strtolower($ks_name);
                echo "<tr><td>" . $rs1[$i]['ks_name'] . "</td></tr>";
                $temparray2 = explode('unit', $ks_name);
                if (count($temparray2) > 1 && count($temparray1) <= 1) {
                    $ks_name = substr($ks_name, 5);
                }
                if (count($temparray2) <= 1 && count($temparray1) > 1) {
                    $onlinename = substr($onlinename, 5);
                }
                if (count($temparray2) > 1 && count($temparray1) > 1 && strlen($onlinename) == 5) {
                    $ks_name = substr($ks_name, 0, 5);
                }
                //echo "<tr><td>".$ks_name."</td></tr>";
                //echo "<tr><td>".count($temparray2)."---".count($temparray1)."</td></tr>";
                if ($onlinename == $ks_name) {

                    $isdui = true;
                    //$updatesql = "update engs_unit_online set ks_code='".$ks_code."' where id=".$unitid;
                    //$engs_unitonline->execute($updatesql);
                    //$engs_unit -> ks_name_short = $ks_name_short;
                    //$engs_unit ->where('ks_code="'.$ks_code.'"')->save();
                    break;
                }
            }
            echo "</table></td>";
            //echo "<td width=20%> ".$onlinename."</td>";
            echo "<td width=20%> " . $value2['unitname'] . "</td>";
            echo "<td width=5%> " . $value2['gradename'] . "</td>";
            echo "<td width=5%> " . $value2['tremname'] . "</td>";
            echo "<td width=5%> " . $value2['versionname'] . "</td>";
            if ($isdui) {
                echo "<td width=20%>2已经在中心库找到对应</td>";
            } else {
                echo "<td width=30%><font color='red'>1未在中心库找到对应</font></td>";
            }
            //echo "<td>".$ks_name."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function show2() {
        $engs_unit = M('');
        $sql = "SELECT a.`id`,unitname,b.`name`,c.`name`,d.`name`,b.`detail_code` AS r_grade,c.`detail_code` AS r_volume,d.`detail_code` AS r_version FROM engs_unit_online a ,engs_grade b,engs_term c,engs_version d WHERE a.`gradeid`=b.`id` AND a.`termid`=c.`id` AND a.`versionid` = d.`id` AND a.`isdel` = 1 AND a.`gradeid` != 10 ORDER BY a.`id` ASC;";
        $rs1 = $engs_unit->query($sql);
        foreach ($rs1 as $key1 => $value1) {
            $ks_name = trimall($value1['unitname']);
            $ks_name = str_replace('’', '', $ks_name);
            $ks_name = str_replace('？', '', $ks_name);
            $ks_name = str_replace('，', '', $ks_name);
            $ks_name = str_replace(',', '', $ks_name);
            $ks_name = str_replace('\'', '', $ks_name);
            $ks_name = str_replace('！', '', $ks_name);
            $ks_name = str_replace('!', '', $ks_name);
            $ks_name = str_replace('.', '', $ks_name);
            $ks_name = str_replace('?', '', $ks_name);
            $ks_name = str_replace('：', '', $ks_name);
            $ks_name = strtolower($ks_name);
            echo $ks_name . "<br/>";
        }

        //echo count($rs1);
    }

    public function updateword() {
        $oldword = M('');
        $newword = M('word_new');
        $sql = "SELECT a.*,b.`ks_code`  FROM engs_word a,engs_unit_online b WHERE a.`unitid` = b.`id` ";
        $olddata = $oldword->query($sql);
        $sql = "delete from engs_word_new";
        $newword->where('1=1')->delete();
        echo "start !\n";
        foreach ($olddata as $key => $value) {
            //echo $value['id'];
            $newword->id = $value['id'];
            $newword->ks_code = $value['ks_code'];
            $newword->word = $value['word'];
            $newword->base_wordid = $value['wordid'];
            $newword->base_explainsid = $value['explainsid'];
            $newword->sortid = $value['sortid'];
            $newword->isstress = $value['isstress'];
            $newword->isdel = $value['isdel'];
            $newword->add();
        }
        echo "end !\n";
    }

    public function updatewordbook() {
        $oldword = M('');
        $newword = M('word_book_new');
        $sql = "SELECT a.*,b.`ks_code`  FROM engs_word_book a,engs_unit_online b WHERE a.`unitid` = b.`id` ";
        $olddata = $oldword->query($sql);
        $sql = "delete from engs_word_book_new";
        $newword->where('1=1')->delete();
        echo "start !\n";
        foreach ($olddata as $key => $value) {
            //echo $value['id'];
            $newword->id = $value['id'];
            $newword->ks_code = $value['ks_code'];
            $newword->base_wordid = $value['wordid'];
            $newword->userid = $value['userid'];
            $newword->areacode = $value['areacode'];
            $newword->studytime = $value['studytime'];
            $newword->isuse = $value['isuse'];
            $newword->add();
        }
        echo "end !\n";
    }

    public function updatetext() {
        $oldword = M('');
        $newword = M('text_chapter_new');
        $sql = "SELECT a.*,b.`ks_code`  FROM engs_text_chapter a,engs_unit_online b WHERE a.`unitid` = b.`id` ";
        $olddata = $oldword->query($sql);
        $sql = "delete from engs_text_chapter_new";
        $newword->where('1=1')->delete();
        echo "start !\n";
        foreach ($olddata as $key => $value) {
            //echo $value['id'];
            $newword->id = $value['id'];
            $newword->ks_code = $value['ks_code'];
            $newword->chapter = $value['chapter'];
            $newword->sortid = $value['sortid'];
            $newword->issection = $value['issection'];
            $newword->isdel = $value['isdel'];
            $newword->add();
        }
        echo "end !\n";
    }

    public function updateunit() {
        $engs_unit = M('rms_unit');
        $engs_unitonline = M('unit_online');
        $data = $engs_unitonline->field('unitalias,ks_code')->select();
        foreach ($data as $key => $value) {
            $engs_unit->ks_name_short = $value['unitalias'];
            $engs_unit->where('ks_code="' . $value['ks_code'] . '"')->save();
        }
    }
//处理版本图片
    public function updatevimg() {
        echo "start!\n";
        $course_edu = M('course','edu_');
        $course_engs = M('course');
        $course_version_edu = M('course_version','edu_');
        $course_version_engs = M('course_version');
        $course_version_book_edu = M('course_version_book','edu_');
        $course_version_book_engs = M('course_version_book');
        $course_data = $course_edu->where('subject_code = "0003"')->select();
        foreach($course_data as $key => $value){
            //var_dump($value);
           // $course_engs ->add($value);
            $course_version_data = $course_version_edu ->where('course_id="'.$value["course_id"].'"')->select();
            foreach($course_version_data as $key1 => $value1){
               // echo $course_version_data['course_id'];
               // $course_version_engs ->add($value1);
                $course_version_book_data = $course_version_book_edu ->where('course_version_id="'.$value1["course_version_id"].'"')->select();
               foreach($course_version_book_data as $key2 => $value2){
                    $course_version_book_engs ->add($value2);
                }
            }
            
        }
        echo "end!\n";
    }
	public function updatevimg2(){
		 echo "start!\n";
		$mm = M('');
		$img = M('version_img');
		$sql = "SELECT grade_code ,term_code,b.`VERSION_CODE`, c.`PIC` FROM edu_course a,edu_course_version b,edu_course_version_book c WHERE a.subject_code = '0003' AND a.`COURSE_ID` = b.`COURSE_ID` AND b.`COURSE_VERSION_ID` = c.`COURSE_VERSION_ID`";
		$rs = $mm -> query($sql);
		foreach($rs as $key => $value){
			$img -> r_grade = $value["grade_code"];
			$img -> r_volume = $value["term_code"];
			$img -> r_version = $value["version_code"];
			$img -> pic_path = $value["pic"];
			$img -> add();
		}
		echo "end!\n";
	}
	public function downloadimg(){
		echo "start\n";
		$img = M('');
		$sql = "select pic_path from engs_version_img where pic_path !=''";
		$rs = $img->query($sql);
		foreach($rs as $key=>$value){
			$oldimgpath = $value["pic_path"];
			$temparray = explode('pic/',$oldimgpath);
			$imgname = $temparray[1];
			//echo "http://125.46.26.42/tms/".$oldimgpath;
			//echo $imgname."/n";
			$img = file_get_contents("http://125.46.26.42/tms/".$oldimgpath);
			file_put_contents("aaa/".$imgname, $img);
		}
		//$img = file_get_contents("http://125.46.26.42/tms/upload/book/pic/ffd91d23-c838-4384-994a-63c131e6918d.jpg");
		//file_put_contents("aaa/1.jpg", $img);
		echo "end\n";

	}
	public function userconfig(){
        echo "start!\n";

		$data = M('config');
        $grade = M('grade');
        $term = M('term');
        $version = M('version');
		$oldconfig = $data->field('id,gradeid,termid,versionid ')->select();
        foreach ($oldconfig as $key => $value) {
         
            $gradedata = $grade ->field('detail_code')->where('id='.$value['gradeid'])->select();
             $termdata = $term ->field('detail_code')->where('id='.$value['termid'])->select();
             $versiondata = $version ->field('detail_code')->where('id='.$value['versionid'])->select();
             $data -> gradeid = $gradedata[0]['detail_code'];
             $data -> termid = $termdata[0]['detail_code'];
            $data -> versionid = $versiondata[0]['detail_code'];
            $id = $value['id'];
            //echo $gradedata[0]['detail_code'];
             $data ->where('id='.$id)->save();
            
          
        }
        echo "end!\n";

	}

    /**
     * 获取试卷内的题型信息
     */
    public function updateexams() {      
           // echo "没有缓存";
        echo "开始执行";
            $sqlquery = M();

            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams';
            $examsdata = $sqlquery -> query($sql);
            ///var_dump($examsdata);
            foreach ($examsdata as $keyexams => $valueexams) {
               // echo $valueexams['id']."!!!";
                 $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$valueexams['id'].' and parentid = 0 and isdel = 1 order by sortid,id';
                $stemdata = $sqlquery -> query($sql);
                foreach ($stemdata as $key => $value) {
                   $stem_type = $value['stem_type'];
                   $typeid = "1"; 
                   if($stem_type == '1'){          //独立大题
                            $score = 0;
                            $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by sortid,id,question_num';    
                            $question = $sqlquery -> query($sql);
                            foreach ($question as $quekey => $quevalue) { 
                                $typeid = $quevalue['typeid'];
                                if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                    $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                    $que_items = $sqlquery->query($sql);
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

                                # code...
                            }
                            if($stemdata[$key]['question_score'] == ceil($stemdata[$key]['question_score'])){
                                $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                            }
                           // $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                            $stemdata[$key]['total_score'] = $score;
                            if($typeid == "2"){
                                $sqlscroe = '(每空'.$stemdata[$key]['question_score'].'分，共'.$score.'分)';
                            }
                            else{
                                $sqlscroe = '(每小题'.$stemdata[$key]['question_score'].'分，共'.$score.'分)';
                            }
                             $updatesql = 'update engs_exams_stem set content = CONCAT(content,"'.$sqlscroe.'") where id='.$value['id'];
                             $sqlquery->execute($updatesql);   
                    }
                    else{                           //组合大题
                         $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$valueexams['id'].' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                        $stem_children = $sqlquery -> query($sql);
                        foreach ($stem_children as $keychild => $childvalue) {
                            $score2 = 0;
                            $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                            $child_question = $sqlquery -> query($sql);
                            foreach ($child_question as $child_quekey => $child_quevalue) {
                            $typeid = $child_quevalue['typeid'];             
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
                            if($typeid == "2"){
                                 $sqlscroe = '(每空'.$stem_children[$keychild]['question_score'].'分，共'.$score2.'分)';
                            }
                            else{
                                 $sqlscroe = '(每小题'.$stem_children[$keychild]['question_score'].'分，共'.$score2.'分)';
                            }
                            
                             $updatesql = 'update engs_exams_stem set content = CONCAT(content,"'.$sqlscroe.'") where id='.$childvalue['id'];
                             $sqlquery->execute($updatesql);
                            //修改题干
                        }
                        
                        
                    }    
                }             
               
        }
        echo "完成";
    }


    public function search_text(){
        $sqlquery = M();
        $sql = "select ks_code from engs_word group by ks_code order by id ";
        $chapterdata = $sqlquery->query($sql);
        foreach ($chapterdata as $key => $value) {
            $sql = "select ks_code from share_knowledge_structure where ks_code='".$value['ks_code']."'";
            $result = $sqlquery->query($sql);
            if(count($result)<=0){
                echo $value['ks_code']."<br>";
            }
        }
    }
    public function level(){
        echo "开始分组<br>";
        $rms_unit = M('rms_unit');
        $word_table = M('word');
        $unit_relult = $rms_unit->field('ks_code')->select();
        $size = 5;
        foreach ($unit_relult as $key => $value) {
            $flag = false;
            $word_result = $word_table->order('sortid asc,id asc')->where('isdel = 1  and ks_code="'.$value['ks_code'].'"')->select();
            $wordnum = count($word_result);
            $a = ceil($wordnum / $size);
            $b = $wordnum % $size;
            echo $value['ks_code']."<br>";
            //echo "page==".$a."<br>";
            if($b == 1){
                $flag = true;
            }
           // echo "page==".$a."<br>";
            for ($i=1; $i <=$a ; $i++) {
                $end = $i*$size;
                $start = ($i-1)*$size;
                if($flag && $a > 1){       
                    if(($i+1) == $a || $i==$a){
                      $end = $i*$size -1;
                    }
                    if($i==$a){
                      $start = ($i-1)*$size -1; 
                    }
                }
                for ($j=$start; $j < $end && $j < $wordnum ; $j++) { 
                    // echo "i==".$i."<br>";
                    // echo "start==".$start."<br>";
                    // echo "size==".$size."<br>";
                    // echo "startj=".$start."<br>";
                    // echo "j<".$end."<br>";
                    // echo "curj=".$j."<br>";
                   $word_table->level_group = $i;
                    $word_table->where('id='.$word_result[$j]['id'])->save();
                }
            }
        }

        // $flag = false;
        //     $word_result = $word_table->order('sortid asc,id asc')->where('isdel = 1  and ks_code="00010203070b04"')->select();
        //     $wordnum = count($word_result);
        //     $a = ceil($wordnum / $size);
        //     $b = $wordnum % $size;
        //     echo $value['ks_code']."<br>";
        //     echo "page==".$a."<br>";
        //     if($b == 1){
        //         $flag = true;
        //     }
        //    // echo "page==".$a."<br>";
        //     for ($i=1; $i <=$a ; $i++) {
        //         $end = $i*$size;
        //         $start = ($i-1)*$size;
        //         if($flag && $a > 1){       
        //             if(($i+1) == $a || $i==$a){
        //               $end = $i*$size -1;
        //             }
        //             if($i==$a){
        //               $start = ($i-1)*$size -1; 
        //             }
        //         }
        //         echo "start==".$start."<br>";
        //         echo "end==".$end."<br>";
        //         for ($j=$start; $j < $end && $j < $wordnum ; $j++) { 
        //             // echo "i==".$i."<br>";
        //             // echo "start==".$start."<br>";
        //             // echo "size==".$size."<br>";
        //             // echo "startj=".$start."<br>";
        //             // echo "j<".$end."<br>";
        //             // echo "curj=".$j."<br>";
        //            $word_table->level_group = $i;
        //             $word_table->where('id='.$word_result[$j]['id'])->save();
        //         }
        //     }
            echo "分组结束";
    }
	 
     public function updateTextSortid(){
        set_time_limit(0);
        $sqlquery = M();
        $sql = "select ks_code from engs_text_chapter where isdel=1 and ks_code is not null group by ks_code order by ks_code ";
        echo $sql;
        $chapterdata = $sqlquery->query($sql);
        foreach($chapterdata as $key=>$value){
            $ks_code=$value["ks_code"];
            //查询chapter
            $chaptersql="select * from engs_text_chapter where ks_code=".$ks_code." and isdel=1 order by sortid";
            $chapterrs=M()->query($chaptersql);
            $index=1;
            foreach($chapterrs as $k=>$v){
                $chapterid=$v["id"];
                $textsql="select * from engs_text where chapterid=".$chapterid." and isdel=1 order by sortid";
                $textrs=M()->query($textsql);
                foreach($textrs as $tk=>$tv){
                    M("text")->where("id=%d",$tv["id"])->setField("textsortid",$index);
                    $index=$index+1;
                }
            }
        }
    }
           
}

?>
