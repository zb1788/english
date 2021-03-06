<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 考试控制器
*  
* @author         gm 
* @since          1.0 
* @备注:此controller中使用flag标志,1标志来源于exam,2表示来源于基础题库
*/
class ExamsController extends Controller {
	public function index(){
		$examsid=I("examsid/d",0);
		$en_dictionary=M("dictionary");
		//取出关键词的
		$proarr=$en_dictionary->where("code = 'que_key1'")->order("code,sortid")->select();
		$topicarr=$en_dictionary->where("code = 'que_key2'")->order("code,sortid")->select();
		$skillarr=$en_dictionary->where("code = 'que_key3'")->order("code,sortid")->select();
		$diffarr=$en_dictionary->where("code = 'que_key4'")->order("code,sortid")->select();
		$periodarr=$en_dictionary->where("code = 'period'")->order("code,sortid")->select();
		$examtypearr=$en_dictionary->where("code = 'examtype'")->order("code,sortid")->select();
		$provincearr=$en_dictionary->where("code = 'province'")->order("code,sortid")->select();
		$yeararr=$en_dictionary->where("code = 'year'")->order("code,sortid")->select();
		$this->assign("periodarr",$periodarr);
		$this->assign("examtypearr",$examtypearr);
		$this->assign("provincearr",$provincearr);
		$this->assign("yeararr",$yeararr);
		$this->assign("examsid",$examsid);
		$this->assign("proarr",$proarr);
		$this->assign("topicarr",$topicarr);
		$this->assign("skillarr",$skillarr);
		$this->assign("diffarr",$diffarr);
		$this->display();
	}


	//测试


// public function savePaper(){
// 	$data =stripslashes(I("data"));
//     $data=urldecode($data);
    
// 	//$data=base64_decode($data);
// 	//echo $data;
// 	//echo "aaaaaa";
// 		//$data = str_replace('&quot;', '"', $data);
// 		$data=json_decode($data);
// 		var_dump($data);
// }
	public function savePaper(){
		$data =  I("data");
		$data = str_replace("+", "%2B", $data);
		$data=urldecode($data);
		$res=json_decode($data);
		//var_dump($res);exit();
		//echo htmlspecialchars($res);
		//定义总分
		$exams_points=0.0;
		//保存试卷本身的属性
		$en_exams=M("exams");
		$en_exams_tts=M("exams_tts");
		$en_exams_stem=M("exams_stem");
		$en_exams_keys=M("exams_questions_keys");
		$en_exams_questions=M("exams_questions");
		$en_exams_questions_items=M("exams_questions_items");
		$en_exams_questions_answer=M("exams_questions_answer");
		$en_exams_questions_keys=M("exams_questions_keys");
		//echo "fasdfasd";exit();
		foreach($res as  $exam){

			//插入考试表
			$examsid=$exam->examsid;
			$arr["name"]=$exam->examname;
			$arr["exams_classid"]=$exam->exams_classid;
			$arr["tts_type"]=$exam->ttstype;
			$arr["header_content"]=$exam->header_content;
			if($exam->stopsecond=='undefined'||$exam->stopsecond==''){
				$exam->stopsecond=3;
			}
			$arr["stopsecond"]=$exam->stopsecond;
			$arr["ks_code"]=$exam->ks_code;
	        $arr["addtime"]=Date("Y-m-d H:i:s",time());
	        $arr["stopsecond"]=$exam->stopsecond;
        	$arr["ks_code"]=$exam->unitid;
        	$arr["provinceid"]=$exam->proid;
        	$arr["yearid"]=$exam->yearid;
        	$arr["typeid"]=$exam->typeid;
        	$arr["levelid"]=$exam->levelid;
	        if($exam->examsid=='0'||$exam->examsid=='undefined'){
	           $examsid=$en_exams->add($arr);
	        }else{
	           $en_exams->where("id=%d",$exam->examsid)->save($arr);
	        }
	        //echo "aaaa";
	        unset($arr);
	        //插入考试的tts
	        $sqlexamse="update engs_exams_tts set isdel=0  where examsid=%d and parentid=%d and tts_type in ('eb','ee')";
	        M()->execute($sqlexamse,$examsid,$examsid);
	        foreach($exam->exam_tts as $obj){
	        	if($obj->exambegin==""&&$obj->mp3=="")
	        		{}else{
	        		$arr["examsid"]=$examsid;
		             if($obj->flag=='0'||$obj->flag==''){
		             	$arr["tts_type"]="eb";
		             }else{
		             	$arr["tts_type"]="ee";
		             }
		             $arr["flag_content"]="";
		             $arr["parentid"]=$examsid;
		             $arr["tts_content"]=htmlspecialchars_decode($obj->exambegin);
		             $arr["voiceid"]=$obj->voiceid;
		             $arr["ftp_mp3"]=$obj->mp3;
		             if($obj->flag=="0"){
		             	$arr["sortid"]=1;
		             	$arr["tts_type"]="eb";
		             }else{
		             	$arr["sortid"]=1;
		             	$arr["tts_type"]="ee";
		             }
		             if($obj->exambegin==""&&$obj->mp3==""){
			              
		             }else{
		             	$en_exams_tts->add($arr);
		             }
		             
	        	}
	        }
	        unset($arr);
	        //插入stem表
	        foreach($exam->paper as $obj){
	        	$stemlastid=$obj->id;
	            $arr["examsid"]=$examsid;
	            $arr["stem_type"]=$obj->stem_type;
	            $arr["question_playtimes"]=$obj->playtimes;
	            $arr["question_score"]=$obj->points;
	            $exams_points=$exams_points+$obj->points;
	            $arr["content"]=htmlspecialchars_decode($obj->content);
	            $arr["stem_playtimes"]="1";
	            $arr["stoptimes"]=$obj->stoptime;
	            $arr["tips"]=$obj->tips;
	            $arr["sortid"]=$obj->sortid;
	            $arr["parentid"]=0;
	            if($stemlastid=='undefined'||$stemlastid=='0'){
	            	$stemlastid=$en_exams_stem->add($arr);
	            }else{
	            	$en_exams_stem->where("id=%d",$obj->id)->save($arr);
	            }
	            foreach($obj->tts as $voice){
	            	if(!empty($voice->content)){
	            		$arr["examsid"]=$examsid;
	            		if($voice->ttstype=='undefined'||$voice->ttstype==''){
	            			$voice->ttstype='st';
	            		}
	            		$arr["tts_type"]=$voice->ttstype;
	            		$arr["st_flag"]=$voice->stflag;
		                $arr["flag_content"]=$voice->voiceflag;
		                $arr["parentid"]=$stemlastid;
		                $arr["tts_content"]=htmlspecialchars_decode($voice->content);
		                $arr["voiceid"]=$voice->voiceid;
		                $arr["tts_stoptime"]=$voice->stoptime;
		                $arr["sortid"]=$voice->sortid;
		                $arr["ftp_mp3"]=$voice->mp3;
		                if($voice->content!=""){
		                	if($voice->id=='0'||$voice->id=='undefined'){
			                	$en_exams_tts->add($arr);
			                }else{
			                	$en_exams_tts->where("id=%d",$voice->id)->save($arr);
			                }

		                }
	            	}
	            }
	            unset($arr);
	            //echo var_dump($obj->questions_lists);exit();
	            foreach($obj->questions_lists as $question){
	            	$questionid=$question->id;
	        	    $arr["stemid"]=$stemlastid;
	                $arr["typeid"]=$question->typeid;
	                $arr["tcontent"]=htmlspecialchars_decode(trim($question->content));
	                $arr["acontent"]="";
	                $arr["itemtype"]=$question->itemtype;
	                $arr["addtime"]=Date("Y-m-d H:i:s",time());
	                $arr["stoptimes"]=$question->question_stoptime;
	                $arr["sortid"]=$question->sortid;
	                $arr["tips"]=$question->tips;
	                $arr["display"]=$question->stemdisplay;
	                //echo $question->question_num;
	                $arr["question_num"]=$question->question_num;
	                if($arr["question_num"]=='undefined'||$arr["question_num"]==''||$arr["question_num"]=="null"||$arr["question_num"]=='0'){	
	                	$arr["question_num"]="";
	                	//echo "fasdfasdfasdfas";
	                }else{
	                	$arr["question_num"]=$question->question_num;
	                }
	                //echo "aa".($arr["question_num"]=='null')."aa";
	                if($question->id=='undefined'||$question->id=='0'){
	                	//echo '2222222';
	                	$questionid=$en_exams_questions->add($arr);
	                }else{
	                	//echo "11111111&&&&&&&".$question->id;
	                	$en_exams_questions->where("id=%d",$question->id)->save($arr);
	                }
	                unset($arr);
	                $en_exams_keys->where("questionsid=%d",$questionid)->setfield("isdel","0");
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key1";
	                $arr["dictionary_id"]=$question->que_key1;
	                if($question->que_key1!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key2";
	                $arr["dictionary_id"]=$question->que_key2;
	                if($question->que_key2!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key3";
	                $arr["dictionary_id"]=$question->que_key3;
	                if($question->que_key3!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key4";
	                $arr["dictionary_id"]=$question->que_key4;
	                if($question->que_key4!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $en_exams_tts->where("parentid=%d and tts_type='qn'",$questionid)->setField("isdel","0");
	                $arr["examsid"]=$examsid;
	                $arr["tts_type"]="qn";
	                $arr["flag_content"]="";
	                $arr["parentid"]=$questionid;
	                $arr["tts_content"]=$question->question_num;
	                $arr["voiceid"]=$question->question_num_voice;
	                $arr["sortid"]=0;
	                $arr["ftp_mp3"]=$question->question_num_mp3;
	                if($question->question_num=='undefined'||$question->question_num==''||$question->question_num=='null'){
	                }else{
	                	$en_exams_tts->add($arr);
	                }
	                unset($arr);
	            	foreach($question->tts as $voice){
		                $arr["examsid"]=$examsid;
		                $arr["tts_stoptime"]=$voice->stoptime;
		                if($voice->ttstype=='undefined'||$voice->ttstype==''){
                           $voice->ttstype='qe';
		                }
		                $arr["tts_type"]=$voice->ttstype;
	            		$arr["st_flag"]=$voice->stflag;
		                $arr["flag_content"]=$voice->voiceflag;
		                $arr["parentid"]=$questionid;
		                $arr["tts_content"]=htmlspecialchars_decode($voice->content);
		                $arr["voiceid"]=$voice->voiceid;
		                $arr["sortid"]=$voice->sortid;
		                $arr["ftp_mp3"]=$voice->mp3;
		                if($voice->content!=""){
		                	if($voice->id=='0'||$voice->id=='undefined'){
		                		if($arr["tts_content"]=='undefined'||$arr["tts_content"]==''||$arr["tts_content"]=='null'){
	                            }else{
			                	  $en_exams_tts->add($arr);
			                	}
			                }else{
			                	if($arr["tts_content"]=='undefined'||$arr["tts_content"]==''||$arr["tts_content"]=='null'){
	                            }else{
			                	$en_exams_tts->where("id=%d",$voice->id)->save($arr);
			                    }
			                }

		                }
	                }
	                unset($arr);
	                foreach($question->items as $item){
                        //echo "aaa";
	                    $arr["questionsid"]=$questionid;
		                $arr["flag"]=$item->flag;
		                $arr["content"]=htmlspecialchars_decode(trim($item->content));
		                $arr["sortid"]=$item->sortid;
		                if($item->id=='0'||$item->id=='undefined'){
		                	$en_exams_questions_items->add($arr);
		                }else{
		                	$en_exams_questions_items->where("id=%d",$item->id)->save($arr);
		                }
	                }
	                unset($arr);

	                $en_exams_questions_answer->where("questionsid=%d",$questionid)->setField("isdel","0");
	                foreach($question->answer as $answers){
	                    $arr["questionsid"]=$questionid;
		                $answers->content=htmlspecialchars_decode(trim($answers->content));
		                //填空题的题号的存储
		                //echo strpos(trim($answers->content),"&")."1111";
		                if(strpos(trim($answers->content),"&")===false){

		                   $arr["answer"]=$answers->content;
		                   if($question->typeid=="4"){
		                   	  $arr["answer_num"]=$answers->answernum;
		                   }else if($question->typeid=="2"){
		                   	  $arr["answer_num"]="";
		                   }else{
		                   	  $arr["answer_num"]=$question->question_num;
		                   }
		                }else{
		                   $inarr=explode('&', trim($answers->content));
		                   $arr["answer"]=$inarr[1];
                           $arr["answer_num"]=$inarr[0];
		                }
		                $arr["sortid"]=$answers->sortid;
		                
		                $en_exams_questions_answer->add($arr); 
	                }

	            }
	            unset($arr);
	            foreach($obj->son as $son){
	            	//var_dump($son);exit();
	            	$stemsonlastid=$son->id;
	            	$arr["examsid"]=$examsid;
	                $arr["stem_type"]=$son->stem_type;
	                $arr["question_playtimes"]=$son->playtimes;
	                $arr["question_score"]=$son->points;
	                $exams_points=$exams_points+$obj->points;
	                $arr["content"]=htmlspecialchars_decode($son->content);
	                $arr["stem_playtimes"]=1;
	                $arr["stoptimes"]=$son->stoptime;
	                $arr["sortid"]=$son->sortid;
	                $arr["tips"]=$son->tips;
	                $arr["parentid"]=$stemlastid;
	                $arr["examsid"]=$examsid;
	                if($son->id=="0"||$son->id=='undefined'){
		                $stemsonlastid=$en_exams_stem->add($arr);
		            }else{
		                $en_exams_stem->where("id=%d",$son->id)->save($arr);
		            }
	                unset($arr);
	                foreach($son->tts as $voice){
		                $arr["examsid"]=$examsid;
		                if($voice->ttstype=='undefined'||$voice->ttstype==''||$voice->ttstype=='qe'||$voice->ttstype=='1'||$voice->ttstype=='0'){
                            $arr["tts_type"]='st';
		                }else{
		                	$arr["tts_type"]=$voice->ttstype;
		                }
	            		$arr["st_flag"]=$voice->stflag;
		                $arr["flag_content"]=$voice->voiceflag;
		                $arr["tts_stoptime"]=$voice->stoptime;
		                $arr["parentid"]=$stemsonlastid;
		                $arr["tts_content"]=htmlspecialchars_decode($voice->content);
		                $arr["voiceid"]=$voice->voiceid;
		                $arr["ftp_mp3"]=$voice->mp3;
		                $arr["sortid"]=$voice->sortid;
		                if($voice->id=='0'||$voice->id=='undefined'){
                            $en_exams_tts->add($arr);
		                }else{
                            $en_exams_tts->where("id=%d",$voice->id)->save($arr);
		                }
	                }
	                unset($arr);
	                //echo "111";
	            foreach($son->questions_lists as $question){
	            	$questionid=$question->id;
	        	    $arr["stemid"]=$stemsonlastid;
	                $arr["typeid"]=$question->typeid;
	                $arr["tcontent"]=htmlspecialchars_decode($question->content);
	                $arr["acontent"]="";
	                $arr["itemtype"]=$question->itemtype;
	                $arr["addtime"]=Date("Y-m-d H:i:s",time());
	                $arr["sortid"]=$question->sortid;
	                $arr["tips"]=$question->tips;
	                $arr["stoptimes"]=$question->question_stoptime;
	                $arr["display"]=$question->stemdisplay;
                    $arr["question_num"]=$question->question_num;
	                if($arr["question_num"]=='undefined'||$arr["question_num"]==''||$arr["question_num"]=='null'||$arr["question_num"]=='0'){
	                    $arr["question_num"]="";
	                }else{
                        $arr["question_num"]=$question->question_num;
	                }
	                if($question->id=='undefined'||$question->id=='0'){
	                	$questionid=$en_exams_questions->add($arr);
	                }else{
	                	$en_exams_questions->where("id=%d",$question->id)->save($arr);
	                }
	                unset($arr);
	                $en_exams_keys->where("questionsid=%d",$questionid)->setField("isdel","0");
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key1";
	                $arr["dictionary_id"]=$question->que_key1;
	                if($question->que_key1!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key2";
	                $arr["dictionary_id"]=$question->que_key2;
	                if($question->que_key2!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key3";
	                $arr["dictionary_id"]=$question->que_key3;
	                if($question->que_key3!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $arr["questionsid"]=$questionid;
	                $arr["dictionary_code"]="que_key4";
	                $arr["dictionary_id"]=$question->que_key4;
	                if($question->que_key4!="0"&&$questionid!="0"){
	                  $en_exams_keys->add($arr);
	                }
	                unset($arr);
	                $en_exams_tts->where("parentid=%d and tts_type='qn'",$questionid)->setField("isdel","0");
	                $arr["examsid"]=$examsid;
	                $arr["tts_type"]="qn";
	                $arr["flag_content"]="";
	                $arr["parentid"]=$questionid;
	                $arr["tts_content"]=$question->question_num;;
	                $arr["voiceid"]=$question->question_num_voice;
	                $arr["sortid"]=0;
	                $arr["ftp_mp3"]=$question->question_num_mp3;
	                if($question->question_num=='undefined'||$question->question_num==''||$question->question_num=='null'){
	                }else{
	                	$en_exams_tts->add($arr);
	                }
	                //$en_exams_tts->add($arr);
	                unset($arr);
	                
	            	foreach($question->tts as $voice){
		                $arr["examsid"]=$examsid;
		                if($voice->ttstype=='undefined'||$voice->ttstype==''){
                           $voice->ttstype='qe';
		                }
		                $arr["tts_type"]=$voice->ttstype;
		                
	            		$arr["st_flag"]=$voice->stflag;
		                $arr["flag_content"]=$voice->voiceflag;;
		                $arr["tts_stoptime"]=$voice->stoptime;
		                $arr["parentid"]=$questionid;
		                $arr["tts_content"]=htmlspecialchars_decode($voice->content);
		                $arr["voiceid"]=$voice->voiceid;
		                $arr["sortid"]=$voice->sortid;
		                $arr["ftp_mp3"]=$voice->mp3;
		                if($voice->content!=""){
		                	if($voice->id=='0'||$voice->id=='undefined'||$voice->id==""){
		                		if($arr["tts_content"]=='undefined'||$arr["tts_content"]==''||$arr["tts_content"]=='null'){
	                            }else{
			                	$en_exams_tts->add($arr);
			                    }
			                }else{
			                	if($arr["tts_content"]=='undefined'||$arr["tts_content"]==''||$arr["tts_content"]=='null'){
	                            }else{
			                	  $en_exams_tts->where("id=%d",$voice->id)->save($arr);
			                    }
			                }

		                }
	                }
	                unset($arr);
	                foreach($question->items as $item){
	                    $arr["questionsid"]=$questionid;
		                $arr["flag"]=$item->flag;
		                $arr["content"]=htmlspecialchars_decode(trim($item->content));
		                $arr["sortid"]=$item->sortid;
		                if($item->id=='0'||$item->id=='undefined'||$item->id==""){
		                	$en_exams_questions_items->add($arr);
		                }else{
		                	$en_exams_questions_items->where("id=%d",$item->id)->save($arr);
		                }
	                }
	                //echo "fasdfasdfasd";
	                unset($arr);
	                $en_exams_questions_answer->where("questionsid=%d",$questionid)->setField("isdel","0");
	                foreach($question->answer as $answers){
	                	$arr["questionsid"]=$questionid;
	                	 $answers->content=htmlspecialchars_decode(trim($answers->content));
	                	//echo $answers->content;
	                	//echo strpos(trim($answers->content),"A")."aaaa";
	                	 if(strpos(trim($answers->content),"&")===false){

		                   $arr["answer"]=$answers->content;
		                   if($question->typeid=="4"){
		                   	  $arr["answer_num"]=$answers->answernum;
		                   }else if($question->typeid=="2"){
		                   	  $arr["answer_num"]="";
		                   }else{
		                   	  $arr["answer_num"]=$question->question_num;
		                   }
		                }else{
		                   $inarr=explode('&', trim($answers->content));
		                   $arr["answer"]=$inarr[1];
                           $arr["answer_num"]=$inarr[0];
		                }
		                //$arr["answer"]=htmlspecialchars_decode(trim($answers->content));
		                $arr["sortid"]=$answers->sortid;
		                //$arr["answer_num"]=$answers->answernum;
		                $en_exams_questions_answer->add($arr);		                
	                }

	            }

	            }
		    }
        }
        $en_exams->where("id=%d",$examsid)->setField("state","1");
        $arr["state"]="success";
        $arr["examsid"]=$examsid;
        $this->ajaxReturn($arr);
	}

	public function getExam(){
		$examsid=I("examsid/d");
		$en_exams=M("exams");
		$en_rms=M("rms_unit");
		$en_exams_tts=M("exams_tts");
		$en_exams_stem=M("exams_stem");
		$en_exams_questions=M("exams_questions");
		$en_exams_answer=M("exams_questions_answer");
		$en_exams_items=M("exams_questions_items");
		$en_exams_key=M("exams_questions_keys");
		//找出试卷
		$exams_rs=$en_exams->where("id=%d",$examsid)->find();
		$rms=$en_rms->where("ks_code='%s'",$exams_rs["ks_code"])->find();
		$exams_rs["unitname"]=getchar($rms['c1'],"GBK","UTF-8");
		$exams_rs["r_grade"]=getchar($rms['r_grade'],"GBK","UTF-8");
		$exams_rs["r_volume"]=getchar($rms['r_volume'],"GBK","UTF-8");
		$exams_rs["r_version"]=getchar($rms['r_version'],"GBK","UTF-8");
		$exams_tts=$en_exams_tts->where("(tts_type='eb' or tts_type='ee') and isdel=1 and examsid=%d and parentid=%d",$examsid,$examsid)->select();
		$exams_rs["tts"]=$exams_tts;
		//试卷下面大题
		$exams_stem_rs=$en_exams_stem->where("examsid=%d and parentid=0 and isdel=1",$examsid)->order("sortid,id")->select();
		//找出没到大题下面的小题
		foreach($exams_stem_rs as $stemk=>$stemv){
			$exams_stem_tts=$en_exams_tts->where(" examsid=%d and parentid=%d and isdel=1 and tts_type!='qn'",$examsid,$stemv["id"])->select();
			$exams_stem_rs[$stemk]["tts"]=$exams_stem_tts;
			$exams_stem_son=$en_exams_stem->where("examsid=%d and isdel=1 and parentid=%d",$examsid,$stemv["id"])->order("sortid,id")->select();
			if(empty($exams_stem_son)){
				$exams_stem_rs[$stemk]["son"]=[];
				$sql="SELECT DISTINCT t.*,(SELECT tts_content FROM engs_exams_tts s WHERE t.id = s.parentid AND isdel=1 and s.tts_type = 'qn') AS tts_content,(SELECT tts_mp3 FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS tts_mp3,(SELECT voiceid FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS voiceid,(SELECT id FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS question_num_id FROM engs_exams_questions t  WHERE isdel=1 and   stemid = %d order by t.sortid,t.id";
				//echo $sql."||".$stemv["id"];
				$stem_questionlists_rs=M()->query($sql,$stemv["id"]);
				foreach($stem_questionlists_rs as $questionsk=>$questionsv){
	                 $questions_tts=$en_exams_tts->where("examsid=%d and parentid=%d and isdel=1 and  tts_type!='qn'",$examsid,$questionsv["id"])->order("sortid,id")->select();
	                 $stem_questionlists_rs[$questionsk]["tts"]=$questions_tts;
	                 //查找选项
	                 $questions_items=$en_exams_items->where("isdel=1 and questionsid=%d ",$questionsv["id"])->order("sortid,id")->select();
	                 $stem_questionlists_rs[$questionsk]["items"]=$questions_items;
	                 //找出答案
	                 $questions_answer=$en_exams_answer->where("isdel=1 and questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
	                 $stem_questionlists_rs[$questionsk]["answer"]=$questions_answer;
	                 //找出关键词
	                 $questions_key=$en_exams_key->where("isdel=1 and questionsid=%d",$questionsv["id"])->select();
	                 $stem_questionlists_rs[$questionsk]["keys"]=$questions_key;
			    }
			    $exams_stem_rs[$stemk]["questionlist"]=$stem_questionlists_rs; 
			}else{
                $exams_stem_rs[$stemk]["questionlist"]=[];
                foreach($exams_stem_son as $sonkey=>$sonvalue){
                	$exams_son_stem_tts=$en_exams_tts->where(" isdel=1 and examsid=%d and parentid=%d and tts_type!='qn'",$examsid,$sonvalue["id"])->order("sortid,id")->select();
					$exams_stem_son[$sonkey]["tts"]=$exams_son_stem_tts;
					$sql="SELECT DISTINCT t.*,(SELECT tts_content FROM engs_exams_tts s WHERE t.id = s.parentid AND isdel=1 and s.tts_type = 'qn') AS tts_content,(SELECT tts_mp3 FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS tts_mp3,(SELECT voiceid FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS voiceid,(SELECT id FROM engs_exams_tts s WHERE isdel=1 and t.id = s.parentid AND s.tts_type = 'qn') AS question_num_id FROM engs_exams_questions t  WHERE   isdel=1 and stemid = %d order by t.sortid,t.id";
                	$stem_questionlists_rs=M()->query($sql,$sonvalue["id"]);
					foreach($stem_questionlists_rs as $questionsk=>$questionsv){
		                 $questions_tts=$en_exams_tts->where("examsid=%d and parentid=%d and isdel=1 and tts_type!='qn' ",$examsid,$questionsv["id"])->select();
		                 $stem_questionlists_rs[$questionsk]["tts"]=$questions_tts;
		                 //查找选项
		                 $questions_items=$en_exams_items->where("isdel=1 and questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
		                 $stem_questionlists_rs[$questionsk]["items"]=$questions_items;
		                 //找出答案
		                 $questions_answer=$en_exams_answer->where("isdel=1 and questionsid=%d",$questionsv["id"])->order("sortid,id")->select();
		                 $stem_questionlists_rs[$questionsk]["answer"]=$questions_answer;
		                 //找出关键词
		                 $questions_key=$en_exams_key->where("isdel=1 and questionsid=%d",$questionsv["id"])->select();
		                 $stem_questionlists_rs[$questionsk]["keys"]=$questions_key;
				    }
				    $exams_stem_son[$sonkey]["questionlist"]=$stem_questionlists_rs;
                }
				
			    $exams_stem_rs[$stemk]["son"]=$exams_stem_son;
			}	 
		}
		$exams_rs["paper"]=$exams_stem_rs;
		$this->ajaxReturn($exams_rs);
	}

	public function test(){
        $people=array('name','sex','nation','brith');
		$this->assign("arr",$people);
		$this->display();
	}


   public function delExam(){
   	$id=I("id/d",0);
   	$en_exams=M("exams");
	$en_exams_tts=M("exams_tts");
	$en_exams_stem=M("exams_stem");
	$en_exams_questions=M("exams_questions");
	$en_exams_answer=M("exams_questions_answer");
	$en_exams_items=M("exams_questions_items");
	$en_exams_key=M("exams_questions_keys");
	$en_exams->where("id=%d",$id)->setField("isdel","0");
	$sql="update engs_exams_questions_items set isdel=0  where questionsid in (select id from engs_exams_questions where stemid in (select id from engs_exams_stem where examsid=".$id."))";
	M()->execute($sql);
	$sql="update engs_exams_questions_answer set isdel=0 where questionsid in (select id from engs_exams_questions where stemid in (select id from engs_exams_stem where examsid=".$id."))";
	M()->execute($sql);
	$sql="update engs_exams_questions set isdel=0 where stemid in (select id from engs_exams_stem where examsid=".$id.")";
	M()->execute($sql);
    $en_exams_stem->where("examsid=%d",$id)->setField("isdel","0");
    $en_exams_tts->where("examsid=%d",$id)->setField("isdel","0");
   }

   public function delStem(){
   	$id=I("id/d",0);
   	$en_exams=M("exams");
	$en_exams_tts=M("exams_tts");
	$en_exams_stem=M("exams_stem");
	$en_exams_questions=M("exams_questions");
	$en_exams_answer=M("exams_questions_answer");
	$en_exams_items=M("exams_questions_items");
	$en_exams_key=M("exams_questions_keys");
	if($id!=0){
		$sql="update engs_exams_questions_items set isdel=0 where questionsid in (select id from engs_exams_questions where stemid =".$id.")";
		M()->execute($sql);
		$sql="update engs_exams_questions_answer set isdel=0 where questionsid in (select id from engs_exams_questions where stemid=".$id.")";
		M()->execute($sql);
		$sql="update engs_exams_questions set isdel=0 where stemid =".$id;
		M()->execute($sql);
		$sql="update engs_exams_tts set isdel=0 where tts_type='st' and parentid=".$id;
		M()->execute($sql);
		//组合题的问题
		$rs=M()->query("select * from engs_exams_stem t where t.parentid=".$id);
		if(!empty($rs)){
			$sql="update engs_exams_stem set isdel=0 where parentid =".$id;
			M()->execute($sql);
			$sql="update engs_exams_questions set isdel=0 where stemid in (select id from engs_exams_stem where parentid=".$id.")";
			M()->execute($sql);
			$sql="update engs_exams_questions_answer set isdel=0 where questionsid in (select id from engs_exams_questions where stemid in (select id from engs_exams_stem where parentid=".$id."))";
			M()->execute($sql);
			$sql="update engs_exams_questions_items set isdel=0 where questionsid in (select id from engs_exams_questions where stemid in (select id from engs_exams_stem where parentid=".$id."))";
			M()->execute($sql);
			$sql="update engs_exams_tts set isdel=0 where tts_type='st' and parentid in (select * from engs_exams_stem where parentid=".$id.")";
		}
		$en_exams_stem->where("id=%d",$id)->setField("isdel","0");
	}
   }

   public function delQuestion(){
   	$id=I("id/d",0);
   	$en_exams=M("exams");
	$en_exams_tts=M("exams_tts");
	$en_exams_stem=M("exams_stem");
	$en_exams_questions=M("exams_questions");
	$en_exams_answer=M("exams_questions_answer");
	$en_exams_items=M("exams_questions_items");
	$en_exams_key=M("exams_questions_keys");
	$sql="update engs_exams_questions_items  set isdel=0 where questionsid =".$id;
	M()->execute($sql);
	$sql="update  engs_exams_tts set isdel=0 where tts_type='qe' and parentid  =".$id;
	M()->execute($sql);
	$sql="update  engs_exams_questions_answer set isdel=0 where questionsid =".$id;
	M()->execute($sql);
	$sql="update engs_exams_questions set isdel=0 where id =".$id;
	M()->execute($sql);

   }

   public function delTTS(){
   	$id=I("id/d",0);
	$sql="update  engs_exams_tts set isdel=0 where id  =".$id;
	M()->execute($sql);

   }

   public function delItem(){
   	$id=I("id/d",0);
	$sql="update engs_exams_questions_items set isdel=0 where id  =".$id;
	M()->execute($sql);

   }

   public function delAnswer(){
    $id=I("id/d",0);
    $sql="update engs_exams_questions_answer set isdel=0 where id  =".$id;
	M()->execute($sql);

   }


   //听力试听
	public function ting(){
		$content=I("content/s");
		$voiceid=I("voiceid/d",1);
		$mp3=gettempmp3($content,$voiceid,"n");
		$path=substr($mp3,0,6)."/".$mp3;
		$arr_return["msg"]=$path;
		$this->ajaxReturn($arr_return);
	}
	
	
	
	
	
	
	//查询生成试卷列表
    public function getPaperToolsList(){
    	$starttime=I("starttime");
    	$endtime=I("endtime");
    	$creator=I("creator");
    	$title=I("title");
    	$state=I("state");
    	$sql="select * from engs_examstools t where isdel=1 and SUBSTR(t.createtime, 1, 10)>='".$starttime."'";
    	if(!empty($endtime)){
    		$sql=$sql." and SUBSTR(t.createtime, 1, 10)<='".$endtime."'";
    	}
    	if(!empty($title)){
    		$sql=$sql." and title like '%".$title."%'";
    	}
    	if(!empty($creator)&&$creator!="0"){
    		$sql=$sql." and creator like '%".$creator."%'";
    	}
    	if($state!="-1"){
    		$sql=$sql." and state=".$state;
    	}
    	$rs=M()->query($sql);
    	$this->ajaxReturn($rs);
    }

    //编辑页面
    public function examstools(){
    	$id=I("id");
    	if(!empty($id)){
    		$rs=M("examstools")->where("id=%d",$id)->find();
    		$this->assign("title",$rs["title"]);
    	}
    	$this->assign("id",$id);
    	$this->display();
    }

    //获取试卷页面的音频
    public function getexamstoolslist(){
    	$id=I("id");
    	if(!empty($id)){
    		$rs=M("examstools_tts")->where("examsid=%d",$id)->select();
    	}else{
    		$rs[0]["tncontent"]="";
    		$rs[0]["voiceid"]=3;
    		$rs[0]["ttsstoptime"]=3;
    		$rs[0]["repeat"]=1;
    	}
    	$ret["list"]=$rs;
    	$this->ajaxReturn($ret);
    }

    //编辑页面
    public function examstoolsedit(){
    	$id=I("id");
    	$title=I("title");
    	$data=I("data");
    	$data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data);
    	$examstools=M("examstools");
    	//进行标题的修改
    	if(!empty($id)){
    		M("examstools")->where("id=%d",$id)->setField("title",$title);
    		M("examstools")->where("id=%d",$id)->setField("state",'0');
    	}else{
    		$examstools->title=$title;
    		$examstools->creator=session('adminuser');
    		$examstools->createtime=Date("Y-m-d H:i:s");
    		$id=$examstools->add();
    	}

    	$toolstts=M("examstools_tts");
    	$toolstts->where("examsid=%d",$id)->delete();
    	//进行音频的添加
    	foreach($data as $obj){
    		$tncontent=$obj->tncontent;
    		$voiceid=$obj->voiceid;
    		$stoptime=$obj->ttsstoptime;
    		$repeat=$obj->repeat;
			$toolstts->examsid=$id;
			$toolstts->tncontent=trim($tncontent);
    		$toolstts->voiceid=$voiceid;
    		$toolstts->ttsstoptime=$stoptime;
    		$toolstts->createtime=Date("Y-m-d H:i:s");
    		$toolstts->repeat=$repeat;
    		$toolstts->add();
    	}
    	$ret["examsid"]=$id;
    	$this->ajaxReturn($ret);
    }

    //重新生成
    public function changeStat(){
    	$id=I("id");
    	$examstools=M("examstools");
    	$examstools->where("id=%d",$id)->setField("state","0");
    }

    //发布生成
    public function publishstates(){
    	$id=I("id");
    	$examstools=M("examstools");
    	$examstools->where("id=%d",$id)->setField("state","1");
    }


    //MP3下载
    public function downloadmp3(){
    	$id=I("id");
    	$examstools=M("examstools");
    	$rs=$examstools->where("id=%d",$id)->find();
    	$url=C("downloadmp3").$rs["mp3"].".mp3";
    	$fileName=$rs["mp3"].".mp3";
		$file = file_get_contents($url); 
		$fp = fopen($fileName, 'w');  
		fwrite($fp, $file);  
		fclose($fp);
		ob_end_clean();//清除缓冲区,避免乱码
		$filesize = filesize($fileName);
		header( "Content-Type: application/force-download;charset=utf-8");
		header( "Content-Disposition: attachment; filename= ".$fileName);
		header( "Content-Length: ".$filesize);
		ob_clean();
		readfile($fileName);

		unlink($fileName); //下载完成后删除服务器文件
    }


    //试卷复制
    public function CopyExamTool(){
    	$id=I("id");
    	$examstools=M("examstools");
    	$rs=$examstools->where("id=%d",$id)->find();
    	$data["title"]=$rs['title'];
		$data["creator"]=session('adminuser');
		$data["createtime"]=Date("Y-m-d H:i:s");
		$examsid=$examstools->add($data);
		$examstooltts=M("examstools_tts")->where("examsid=%d",$id)->select();
		foreach($examstooltts as $key=>$value){
			M("examstools_tts")->examsid=$examsid;
			M("examstools_tts")->tncontent=$value["tncontent"];
			M("examstools_tts")->ttsstoptime=$value["ttsstoptime"];
			M("examstools_tts")->voiceid=$value["voiceid"];
			M("examstools_tts")->createtime=Date("Y-m-d H:i:s");
			M("examstools_tts")->add();
		} 
    }

    public function delExamTool(){
    	$id=I("id");
    	$examstools=M("examstools");
    	$examstools->where("id=%d",$id)->setField("isdel","0");
    }


}