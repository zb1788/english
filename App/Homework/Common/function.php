<?php
/*定义全局公共函数的地方*/

function curl_get_contents($url,$timeout=1) {
	$curlHandle = curl_init();
	curl_setopt( $curlHandle , CURLOPT_URL, $url );
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
	$result = curl_exec( $curlHandle );
	curl_close( $curlHandle );
	return $result;
}


//studentEnAnswer={studentId:'',studentname:'',schoolclass:'',homeworkId:'',spendTime:0,scores:8.5}
//post发送json数据
function curl_post_contents($url,$studentid,$studentname,$schoolclass,$homeworkid,$spendtime,$score,$starttime) {
	$ch = curl_init();
	$post_fields['studentEnAnswer.studentId'] = $studentid;
	$post_fields['studentEnAnswer.studentname'] = $studentname;
	$post_fields['studentEnAnswer.schoolclass'] = $schoolclass;
	$post_fields['studentEnAnswer.homeworkId'] = $homeworkid;
	$post_fields['studentEnAnswer.spendTime'] = $spendtime;
	$post_fields['studentEnAnswer.scores'] = $score;
	$post_fields['studentEnAnswer.startTime'] = $starttime;

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 执行HTTP请求
	curl_setopt($ch , CURLOPT_URL , $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));//POST的数据大于1024时的处理
	$res = curl_exec($ch);
	curl_close($ch);
  return $res;
}


function getuserinfo($username){
	//tmsjz.czbanbantong.com
	$url=PROTOCOL."://".cookie('tms')."/tms/interface/queryStudent.jsp?queryType=byNames&usernames=".$username;
//echo $url;exit;
	$json_ret = get_content($url);
	//echo $json_ret;
       //$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//תÂ£¬£¨ÕÀֻÊ¸öӣ©

       $json_ret = iconv("gb2312", "utf-8//IGNORE",$json_ret);
	$result = json_decode($json_ret, true);
	if(count($result['rtnArray'])>0){
			//cookie("engm_ut",cookie('ut'));
			//$ouUser=$result['ouUser'];
			$user=$result['rtnArray'][0];
			cookie("engm_username",$user['studentNumber']);
			cookie("engm_truename",$user['realname']);
	}else{
		redirect(PROTOCOL.'://www.czbanbantong.com');
	}
}

function getClassId($tms,$username){
	$classId = "0";
	$arr=get_ip('',$tms);
	$tms=$arr["tms"]["c1"];
	$queryClassUrl = PROTOCOL."://".$tms."/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName=".$username;
	$json_ret = get_content($queryClassUrl);
	$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
	$classinfo = json_decode($json_ret, true);
	if($classinfo['result'] > 0){
		$classId= $classinfo['rtnArray'][0]['classId'];
	}
	return $classId;
}



function receiveStreamFile($receiveFile){

    $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

    if(empty($streamData)){
        $streamData = file_get_contents('php://input');
    }

    if($streamData!=''){
        $ret = file_put_contents($receiveFile, $streamData, true);
    }else{
        $ret = false;
    }

    return $ret;

}

function special_character($str){
	return preg_match("/^[A-Za-z]+$/",$str);
	//return preg_match("/^[A-Za-z0-9]+$/",$str);
}

function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v)
    {
        if (gettype($v) == 'resource')
        {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array')
        {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}
//转码测试
function voiceTrasfer($username,$filename,$extname){
  //记录开始时间
  $starttime=Date("Y-m-d H:i:s");
  $path="/home/yylmp3/recordwav/";
  $yfilename=$path.$filename;
  //获取扩展名称
  $extname=substr($yfilename, strrpos($yfilename, '.')+1);
  $basename=basename($yfilename,$extname);

  $outputname=$path.$basename."mp3";

  $retrunname = $basename."mp3";
  exec("ffmpeg -i ".$yfilename." ".$outputname,$res, $rc);
  //shell_exec("ffmpeg -i audio.wav audio2mp31.mp3");
 // var_dump($res);
  //$endtime=Date("Y-m-d H:i:s");
  //$time=$endtime-$starttime;

  // $file='./log/transfer'.Date("Ymd").".log";
  // //判断文件是否存在
  // if(!file_exists($file)){
  //   $myfile = fopen($file, "w") or die("Unable to open file!");
  //   fclose($myfile);
  // }
  //记录转码的音频
  //file_put_contents($file,$username." transfer voice ".Date("Y-m-d H:i:s")." ".$filename." to ".$outputname." spendtime:".$time." ".var_dump($res),FILE_APPEND | LOCK_EX);
  return $retrunname;
}

function getClassName($sso,$classid){
	//http://tms.youjiaotong.com/tms/interface/querySchoolClass.jsp?queryType=bySchoolClassId&schooClasslId=147141255930298295
	$url=PROTOCOL."://".$sso."/tms/interface/querySchoolClass.jsp?queryType=bySchoolClassId&schooClasslId=".$classid;
	// $url=PROTOCOL."://".$sso."/tms/interface/querySchoolClass.jsp?queryType=bySchoolClassId&schoolClassId=".$classid;
	$result=get_content($url);
	if(count(explode("error", $result)) > 1){
		$url=PROTOCOL."://".$sso."/tms/interface/querySchoolClass.jsp?queryType=bySchoolClassId&schoolClassId=".$classid;
		$result=get_content($url);
	    $result = json_decode($result, true);
	}
	else{

	     $json_ret = mb_convert_encoding($result, "UTF-8", "gbk");//转码，（这里只是个例子）
		 $result = json_decode($json_ret, true);
	}
	
	return $result["className"];
}

function getStuAverage($homeworkid,$classid,$studentid){
	$wadefenlv = 0; $wpdefenlv = 0; $wydefenlv = 0; $wcdefenlv = 0; $tadefenlv = 0; $eqdefenlv = 0;
	$totalAverage = 0;
	$result = M("homework_student_list")->where("paper_id=%d and classid='%s' and studentid = '%s' and submittime is not null",$homeworkid,$classid,$studentid)->find();
	if($result['id'] > 0){
		$rs=M("homework")->where("id=%d",$homeworkid)->find();
		$sqlquery = M();
		$typenum = 0;
		//单词跟读得分率
		if($rs["wacount"] > 0){
			$sql = "select sum(score) as score from engs_homework_student_word_read where homeworkid=".$homeworkid." and classid='".$classid."' and studentid='".$studentid."'" ;
			$wadelv = $sqlquery->query($sql);
		    $wadefenlv = $wadelv[0]['score']/($rs["wacount"]*100);
		    $typenum++;
		}
		//单词拼写得分率
		if($rs["wscount"] > 0){
			$sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type=2 and studentid='".$studentid."'";
		    $wpdelv = $sqlquery->query($sql);
		    $wpdefenlv = $wpdelv[0]['score']/$rs["wscount"];
		    $typenum++;
		}

	    //英汉互译得分率
	    if($rs["wrcount"] > 0){
	    	$sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type in(1,3) and studentid='".$studentid."'";
		    $wydelv = $sqlquery->query($sql);
		    $wydefenlv = $wydelv[0]['score']/$rs["wrcount"];
		    $typenum++;
	    }

	    //听音选词得分率
	    if($rs["wccount"] > 0){
	    	$sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type=0 and studentid='".$studentid."'";
		    $wcdelv = $sqlquery->query($sql);
		    $wcdefenlv = $wcdelv[0]['score']/$rs["wccount"];
		    $typenum++;
	    }

	 	//课文跟读得分率
	 	if($rs["tacount"] > 0){
	 		$sql = "select sum(score) as score from engs_homework_student_text_read where homeworkid=".$homeworkid." and classid='".$classid."' and studentid='".$studentid."'";
		    $tadelv = $sqlquery->query($sql);
		    $tadefenlv = $tadelv[0]['score']/($rs["tacount"]*100);
		    $typenum++;
	 	}

	    //听力训练得分率
	    if($rs["eqcount"] > 0){
	    	$sql = "select sum(score) as score from engs_homework_student_quiz where homeworkid=".$homeworkid." and classid='".$classid."' and studentid='".$studentid."'";
		    $eqdelv = $sqlquery->query($sql);
		    $sql = "select examsid from engs_homework_quiz where homeworkid=".$homeworkid;
	        $examsinfo = $sqlquery->query($sql);
	        $examsid = $examsinfo[0]['examsid'];
	        $totalscore = getExamScore($examsid);
		    $eqdefenlv = $eqdelv[0]['score']/$totalscore;
		    $typenum++;
	    }
	    $totalAverage = sprintf("%.3f",($wadefenlv + $wpdefenlv+$wydefenlv+$wcdefenlv + $tadefenlv + $eqdefenlv)/$typenum)*100;
	}

   	return $totalAverage;
}
//替换试卷中图片地址
function relace_tcontent($str){

    $resource_path = PROTOCOL."://".RESOURCE_DOMAIN.'/yylmp3/';
    $replacestr = str_replace('/uploads',$resource_path.'uploads',$str);
    return $replacestr;
}

//获取听力训练总分
 function getExamScore($examsid){
 	   $sqlquery = M();
        $total_score=0;
        $sql = 'select id,stem_type,question_score from engs_exams_stem where examsid='.$examsid.' and parentid = 0 and isdel = 1 order by sortid,id';
        $stemdata = $sqlquery -> query($sql);
        foreach ($stemdata as $key => $value) {
            $stem_type = $value['stem_type'];
            if($stem_type == '1'){          //独立大题
                $score = 0;
                $sql='select t.* from engs_exams_questions t where t.stemid ='.$value['id'].' and t.isdel=1';
                $question = $sqlquery -> query($sql);
                foreach ($question as $quekey => $quevalue) {
                    $sql = 'select id from engs_exams_questions_answer where isdel=1 and questionsid='.$quevalue["id"];     //独立大题问题的答案
                    $queanswer = $sqlquery -> query($sql);
                    $score += $value['question_score'] * count($queanswer);
                }
                $stemdata[$key]['total_score'] = $score;
                $total_score += $score;
            }else if($stem_type ==2){                 //组合大题
                unset($question);
                unset($score);
                $sql = 'select id,stem_num,stem_type,question_score from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel = 1 order by sortid,id';
                $stem_children = $sqlquery -> query($sql);
                foreach ($stem_children as $keychild => $childvalue) {
                    //答题下面的问题开始
                    $sql='select t.* from engs_exams_questions t where t.stemid ='.$childvalue['id'].' and t.isdel=1';
                    $question = $sqlquery -> query($sql);
                    $score=0.0;
                    foreach ($question as $quekey => $quevalue) {
                        //问题答案
                        $sql = 'select id from engs_exams_questions_answer  where questionsid = ' . $quevalue['id'].' and isdel=1 ';     //独立大题问题的答案
                        $queanswer = $sqlquery -> query($sql);
                        $score += $childvalue['question_score'] * count($queanswer);
                    }
                    $total_score += $score;

                }
            }
        }
        return $total_score;
    }
    //将秒数转换为小时分钟
    function Sec2Time($time){
    if(is_numeric($time)){
	    $value = array(
	      "years" => 0, "days" => 0, "hours" => 0,
	      "minutes" => 0, "seconds" => 0,
	    );
	    if($time >= 31556926){
	      $value["years"] = floor($time/31556926);
	      $time = ($time%31556926);
	      $t=$value["years"] ."年";
	    }
	    if($time >= 86400){
	      $value["days"] = floor($time/86400);
	      $time = ($time%86400);

	      $t=$t.$value["days"] ."天";
	    }
	    if($time >= 3600){
	      $value["hours"] = floor($time/3600);
	      $time = ($time%3600);
	      $t=$t.$value["hours"] ."小时";
	    }
	    if($time >= 60){
	      $value["minutes"] = floor($time/60);
	      $time = ($time%60);
	      $t=$t.$value["minutes"] ."分";
	    }
	    $value["seconds"] = floor($time);
	    $t=$t.$value["seconds"]."秒";
	    Return $t;

     }
     else{
    	return (bool) FALSE;
     }
 }

 //如果题库接口当时没有传过来的情况
 	function getHomeworkPublishStudents($tqms,$paperid,$batchid){
 		//查询数据库中接口数据是否成功
 		$homework_batch=M("homework_batch");
 		$homework=M("homework");
 		$homeworbatchrs=M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$paperid)->find();
 		if(!empty($homeworbatchrs)){

 			return 1;
 		}else{
 			//判断这个年级下面是否有作业
 			$paperurl=PROTOCOL."://".$tqms."/tqms/mobile/homework/getPublishDataForEn.action?paper_id=".$paperid;
		    //获取content
		    $content = get_content($paperurl);
		    if(!empty($content)){
		        $content = json_decode($content,true);
		        $username = $content["username"];
		        $homeworkid = $content["homeworkId"];
		        $papername = $content["paper_name"];
		        $paperid = $content["paper_id"];
		        $batchClassMap = $content["batchClassMap"];
		        $batchClassMap = stripslashes($batchClassMap);
		        $batchClassMap = rtrim($batchClassMap, '"');
		        $batchClassMap = ltrim($batchClassMap, '"');
		        $batchClassMap = str_replace('&quot;', '"', $batchClassMap);
		        $batchClassMap = json_decode($batchClassMap,true);
		        $classStudentMap = $content["classStudentMap"];
		        $classStudentMap = stripslashes($classStudentMap);
		        $classStudentMap = rtrim($classStudentMap, '"');
		        $classStudentMap = ltrim($classStudentMap, '"');
		        $classStudentMap = str_replace('&quot;', '"', $classStudentMap);
		        $classStudentMap = json_decode($classStudentMap,true);
		        $classStudents=array();
		        foreach($classStudentMap as $keys=>$obj){
					$classinfo=array();
					$students=array();
					$studentnum=0;
					foreach($obj as $sobj){
						$student=array();
						$student["id"]=$sobj["id"];
						$student["name"]=$sobj["name"];
						$student["parentIds"]=$sobj["parentIds"];
						$student["score"]=0;
						$student["time"]=0;
						$student["issubmit"]=0;
						$students[$sobj["id"]]=$student;
						$studentnum=$studentnum+1;
					}
					$classinfo["classId"]=(string)$keys;
					$classinfo["className"]="";
					$classinfo["studentCount"]=$studentnum;
					$classinfo["students"]=$students;
					$classStudents[$keys]=$classinfo;
		        }
		        foreach($batchClassMap as $keyb=>$valueb){
		          	//查询数据库中是否已经存在这个batch
		          	$rs=$homework_batch->where("batchid='%s'",$valueb)->find();
		          	//var_dump($rs);exit;
		          	//将数据插入数组中
			        if(!empty($rs)){
			            $publishstudents=json_decode($rs["publishstudents"],TRUE);
			            if(empty($publishstudents[$keyb])){
							$publishstudents[$keyb]=$classStudents[$keyb];
							foreach($classStudents[$keyb]["students"] as $skey=>$svalue){
								$studentid=$skey;
								$temp=$svalue;
								//查询数据
								$ret=getStudentScore($paperid,$studentid);
								$temp["score"]=$ret["score"];
								$temp["time"]=empty($ret["papertime"])?1:$ret["papertime"];
								$temp["issubmit"]=$ret["issubmit"];
								$publishstudents[$keyb]["students"][$skey]=$temp;
							}
			            }else{
							foreach($classStudents[$keyb]["students"] as $skey=>$svalue){
								$studentid=$skey;
								$temp=$svalue;
								//查询数据
								$ret=getStudentScore($paperid,$studentid);
								$temp["score"]=$ret["score"];
								$temp["time"]=empty($ret["papertime"])?1:$ret["papertime"];
								$temp["issubmit"]=$ret["issubmit"];
								$publishstudents[$keyb]["students"][$skey]=$temp;
							}
			            }
			            //保存这个数据到batchid表中
			            $homework_batch->publishstudents=json_encode($publishstudents);
			            $homework_batch->paper_name=$papername;
			            $homework_batch->publish_time=Date("Y-m-d H:i:s");
			            $homework_batch->where("paper_id=%d and batchid='%s'",$paperid,$valueb)->save();
			            //$homework->where("id=%d and batchid='%s'",$paper_id,$valueb)->setField("publishstudents",json_encode($publishstudents));
			        }else{
			            $class=array();
			            foreach($classStudents[$keyb]["students"] as $skey=>$svalue){
			                $studentid=$skey;
			                $temp=$svalue;
			                //查询数据
			                $ret=getStudentScore($paperid,$studentid);
		                  	$temp["score"]=$ret["score"];
		                  	$temp["time"]=empty($ret["papertime"])?1:$ret["papertime"];
		                  	$temp["issubmit"]=$ret["issubmit"];
			                $classStudents[$keyb]["students"][$skey]=$temp;
			            }
			            $class[$keyb]=$classStudents[$keyb];
			            $homework_batch->batchid=$valueb;
			            $homework_batch->paper_id=$paperid;
			            $homework_batch->publish_time=Date("Y-m-d H:i:s");
			            $homework_batch->paper_name=$papername;
			            $homework_batch->username=$username;
			            $homework_batch->publishstudents=json_encode($class);
			            $homework_batch->homeworkid=$homeworkid;
			            $homework_batch->add();
			            //$homework->where("id=%d",$paperid)->setField("publishstudents",json_encode($class));
			        }
			    }
			}
	    }
 	}

 	//获取学生分数
 	function getStudentScore($paper_id,$studentid){
 		$rss=M("homework_student_list")->where("studentid='%s' and paper_id=%d and submittime is not null",$studentid,$paper_id)->find();
 		if(!empty($rss)){
 			$hrs=M("homework")->where("id=%d",$paper_id)->find();
			$homeworkblock=0;
			if($hrs["wacount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			if($hrs["wscount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			if($hrs["wccount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			if($hrs["wrcount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			if($hrs["tacount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			if($hrs["eqcount"]>0){
			$homeworkblock=$homeworkblock+1;
			}
			//计算分数
			$score=0.0;
			$sql="select '1' as id,sum(score) as score,'4' as typeid from engs_homework_student_word_read where homeworkid='".$paper_id."' and studentid='".$studentid."'";
			$sql=$sql." union all select '1' as id,sum(score) as score,'5' as typeid from engs_homework_student_text_read where homeworkid='".$paper_id."'  and studentid='".$studentid."'";
			$sql=$sql." union all select '1' as id,sum(score) as score,type as typeid from engs_homework_student_word_evaluat where homeworkid='".$paper_id."'  and studentid='".$studentid."' group by type";
			$sql=$sql." union all select examid as id,sum(score) as score,'6' as typeid from engs_homework_student_quiz where homeworkid='".$paper_id."' and studentid='".$studentid."' group by examid";
			$srs=M()->query($sql);
		    //定义作业总共有几块内容
		    foreach($srs as $bkey=>$bvalue){
		        if($bvalue["typeid"]=='4'){
		          //单词跟读问题
		          if(!empty($bvalue["score"])){
		            $score=$bvalue["score"]/$hrs["wacount"]+$score;
		          }
		        }else if($bvalue["typeid"]=='5'){
		          //课文跟读
		          if(!empty($bvalue["score"])){
		            $score=$bvalue["score"]/$hrs["tacount"]+$score;
		          }
		        }else if($bvalue["typeid"]=='6'){
		          //听力训练
		          if(!empty($bvalue["score"])){
		            //计算试卷的总分
		            $totalscore=getExamScore($bvalue["id"]);
		            $score=($bvalue["score"]*100)/$totalscore+$score;
		          }
		        }else if($bvalue["typeid"]=='0'){
		          //听音选词
		          if(!empty($bvalue["score"])){
		            $score=($bvalue["score"]*100)/$hrs["wccount"]+$score;
		          }
		        }else if($bvalue["typeid"]=='2'){
		          //单词拼写
		          if(!empty($bvalue["score"])){
		            $score=($bvalue["score"]*100)/$hrs["wscount"]+$score;
		          }
		        }else{
		          //英汉互译
		          if(!empty($bvalue["score"])){
		            $score=($bvalue["score"]*100)/$hrs["wrcount"]+$score;
		          }
		        }
	      	}
	      	$arr["issubmit"]=1;
	      	$arr["time"]=$rss["papertime"];
	      	$arr["score"]=(double)($score/$homeworkblock);
 		}else{
 			$arr["issubmit"]=0;
	      	$arr["score"]=0;
	      	$arr["time"]=1;
 		}
 		return $arr;
 	}
