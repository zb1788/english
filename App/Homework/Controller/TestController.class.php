<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 首页控制器类
*
* @author         gm
* @since          1.0
*/

class TestController extends Controller {
    public function doData(){
    	set_time_limit(0);
    	ini_set("memory_limit", "1024");
		echo "正在处理中。。。。。。";
    	$quessql="SELECT t.*,s.`parentid` FROM engs_exams_questions t LEFT JOIN engs_exams_stem s ON t.`stemid`=s.`id` where s.id= order by t.sortid";
		$quesrs=M()->query($quessql);
		foreach($quesrs as $key=>$value){
			//查询选项
			$items="";
			if($value["typeid"]==1){
				$quesitmessql="SELECT flag,content,flag as value FROM engs_exams_questions_items t WHERE t.`questionsid`=".$value["id"]." AND t.`isdel`=1 ORDER BY t.`sortid`";
				$quesitmesrs=M()->query($quesitmessql);
				$items=urlencode(json_encode($quesitmesrs));
				
			}
			if($value["typeid"]==3){
				$items[0]["flag"]="A";
				$items[0]["content"]="True";
				$items[0]["value"]="1";
				$items[1]["flag"]="B";
				$items[1]["content"]="False";
				$items[1]["value"]="0";
				$items=urlencode(json_encode($items));
			}
			//查询答案问题
			$answer="";
			$answersql="SELECT * FROM engs_exams_questions_answer t WHERE t.`questionsid`=".$value["id"]." AND t.`isdel`=1";
			$answerrs=M()->query($answersql);
			
			if($value["typeid"]==1){
				foreach($quesitmesrs as $ik=>$iv){
					if($iv["content"]==$answerrs[0]["answer"]){
						$answer["answer"]=$iv["flag"];
						break;
					}
				}
				$answer["id"]=$answerrs[0]["id"];
				$answer=urlencode(json_encode($answer));
				
			}
			if($value["typeid"]==3){
				$answer["id"]=$answerrs[0]["id"];
				$answer["answer"]=$answerrs[0]["answer"];
				$answer=urlencode(json_encode($answer));
			}
			
			//查询音频
			$tts="";
			if($value["parentid"]=='0'||$value["parentid"]==null||empty($value["parentid"])){
				$questtssql="SELECT * FROM engs_exams_tts t WHERE t.`isdel`=1 AND t.`parentid`=".$value["id"]." AND t.`tts_type`='qe' order by t.sortid";
				$questtsrs=M()->query($questtssql);
				$tts=urlencode(json_encode($questtsrs));
			}else{
				$questtssql="SELECT * FROM engs_exams_tts t WHERE t.`isdel`=1 AND t.`parentid`=".$value["stemid"]." AND t.`tts_type`='st' and t.st_flag=0 order by t.sortid";
				$questtsrs=M()->query($questtssql);
				$tts=urlencode(json_encode($questtsrs));
			}
			$ques["questions_items"]=$items;
			$ques["questions_answer"]=$answer;
			$ques["questions_tts"]=$tts;
			
			M("exams_questions")->where("id=%d",$value["id"])->save($ques);
			
		}

        unset($quesrs);
        //处理paper_sortid
        $examssql="select * from engs_exams t where t.isdel=1";
		$examsrs=M()->query($examssql);
		foreach($examsrs as $key=>$value){
			$paper_sortid=0;
			//查询大题
			$stemsql="select * from engs_exams_stem t where t.examsid=".$value["id"]." and t.isdel=1 and parentid=0 order by sortid";
			$stemrs=M()->query($stemsql);
			foreach($stemrs as $sk=>$sv){
				//表示单题
				if($sv["stemtype"]=='1'){
					$questionsql="select * from engs_exams_questions t where t.stemid=".$sv["id"]." and t.isdel=1 order by t.sortid";
					$questionrs=M()->query($questionsql);
					foreach($questionrs as $qk=>$qv){
						$arr["paper_sortid"]=$paper_sortid;
						$arr["examsid"]=$value["id"];
						M("exams_questions")->where("id=%d",$qv["id"])->save($arr);
						$paper_sortid=$paper_sortid+1;
					}
				}else{
					$cstemsql="select * from engs_exams_stem t where t.examsid=".$value["id"]." and t.isdel=1 and parentid=".$sv["id"]." order by sortid";
					$cstemrs=M()->query($cstemsql);
					foreach($cstemrs as $ck=>$cv){
						$cquestionsql="select * from engs_exams_questions t where t.stemid=".$sv["id"]." and t.isdel=1 order by t.sortid";
						$cquestionrs=M()->query($cquestionsql);
						foreach($cquestionrs as $qk=>$qv){
							$arr["paper_sortid"]=$paper_sortid;
							$arr["examsid"]=$value["id"];
							M("exams_questions")->where("id=%d",$qv["id"])->save($arr);
							$paper_sortid=$paper_sortid+1;
						}
					}
				}
			}
		}
        echo "处理结束";
        
    }
    //首页显示
    public function index(){
    	$sql="SELECT DISTINCT t.`ks_code`,t.`c1` FROM engs_rms_unit t WHERE t.`c1` LIKE '%英语%'";
		$rs=M()->query($sql);
		$this->assign("list",$rs);
		$this->display();
    }


    //老数据进行处理
  public function getOldHomeworkData(){
    set_time_limit(0);
    $homework=M("homework");
    $homework_batch=M("homework_batch");
    $tqms=I("tqms");
    //将homework中的数据进行遍历
    ob_end_clean();
    ob_implicit_flush(1);
    echo "数据开始处理</br>";
    $flag=true;
    $num=0;
    while($flag){
      $result=M()->query("SELECT * FROM engs_homework WHERE teachid IS NOT NULL AND teachid!='' AND homework_name IS NOT NULL AND publish_time IS NOT NULL AND publishstudents IS  NULL order by id desc limit ".$num.",50");
      $num=$num+50;
      echo "<script language='javascript'>  document.body.innerHTML=''; </script>";
      echo "处理第".(($num/50))."批数据处理中....(当出现数据处理完毕的时候在关闭浏览器)</br>";
      if(!empty($result)&&count($result)!=0){
        foreach($result as $key=>$value){
          $paperurl="http://".$tqms."/tqms/mobile/homework/getPublishDataForEn.action?paper_id=".$value["id"];
          //获取content
          $content = file_get_contents($paperurl);
          //echo $content;
          //$content = mb_convert_encoding($content, "UTF-8", "gbk");//转码
          if(!empty($content)){
            //解析内容
            ob_end_clean();
            ob_implicit_flush(1);
            echo "正在处理".$value["id"]."处理中....(当出现数据处理完毕的时候在关闭浏览器)</br>";
            file_put_contents('data.log',$value["id"].PHP_EOL,FILE_APPEND);
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
            //编辑classlist列表
            $classStudents=array();
            foreach($classStudentMap as $key=>$obj){
              $classinfo=array();
              $students=array();
              $studentnum=0;
              foreach($obj as $skey=>$sobj){
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
              $classinfo["classId"]=$key;
              $classinfo["className"]="";
              $classinfo["studentCount"]=$studentnum;
              $classinfo["students"]=$students;
              $classStudents[$key]=$classinfo;
            }
          //班级批次列表
            foreach($batchClassMap as $key=>$value){
              //查询数据库中是否已经存在这个batch
              $rs=$homework_batch->where("batchid='%s'",$value)->find();
              //将数据插入数组中
              if(!empty($rs)){
                  $publishstudents=json_decode($rs["publishstudents"],TRUE);
                  foreach($class[$key] as $skey=>$svalue){
                    array_push($publishstudents[$key], $svalue);
                  }
                //保存这个数据到batchid表中
                  $homework_batch->publishstudents=json_encode($publishstudents);
                  $homework_batch->paper_name=$papername;
                  $homework_batch->publish_time=Date("Y-m-d H:i:s");
                  $homework_batch->where("batchid='%s'",$value)->save();
                  //跟新一下homework表中的数据 防止新老数据出问题
                  $homework->publishstudents=json_encode($publishstudents);
                  $homework->homeworkid=$homeworkid;
                  $homework->homework_name=$papername;
                  $homework->is_publish=1;
                  $homework->where("id=%d",$paperid)->save();
                }else{
                  $class=array();
                  $class[$key]=$classStudents[$key];
                  $homework_batch->batchid=$value;
                  $homework_batch->paper_id=$paperid;
                  $homework_batch->publish_time=Date("Y-m-d H:i:s");
                  $homework_batch->paper_name=$papername;
                  $homework_batch->username=$username;
                  $homework_batch->publishstudents=json_encode($class);
                  $homework_batch->homeworkid=$homeworkid;
                  $homework_batch->add();
                  $homework->publishstudents=json_encode($class);
                  $homework->homeworkid=$homeworkid;
                  $homework->is_publish=1;
                  $homework->where("id=%d",$paperid)->save();
                }
            }
          }
        }
        echo "处理第".(($num/50))."批数据完毕</br>";
      }else{
        $flag=false;
      }
    }
    echo "数据处理完毕</br>";
  }

//http://en.czbanbantong.com/Homework/Mobhw/homeworkstudent?homeworkid=121755&classid=158177149470077767&tqms=tqmsfj.czbanbantong.com&batchid=0975c1423d3b4eb39f93d8e4932d79bc&ran=0.7460618429979713
  //https://en.czbanbantong.com/Homework/Index/student_homework_feedback?homeworkId=e5c6002f96dd4fc99207c75a6225c327&isOverdue=false&studentId=350629100000099760&classId=158177149457782260&batchid=e2cefe2bdbdb42f6b722ca6f3557b351&paper_id=125768&sso=tmsfj.czbanbantong.com&ut=b722528c71e64ca3d341508774983aa336a30484e63554f89c61f4cd68f8228e129fd25475bca9607a525fe8c6277608&deviceType=mobile&deviceInfo=t%3A0%20xh%3AMuMu%20pp%3AAndroid%204.0.9-android-x86%2B%0A6.0.1%0AMuMu%0AV417IR%20release-keys%20vn%3AV4.1.5&serAreaCode=2.
    public function updatehomework(){
      $homeworkid="125768";
     $batchid="e2cefe2bdbdb42f6b722ca6f3557b351";
      $paper_id="125768";
      $studentid="350629100000099760";
      $schoolclass="158177149457782260";

      //查看作业问题
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
      $sql="select '1' as id,sum(score) as score,'4' as typeid from engs_homework_student_word_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
      $sql=$sql." union all select '1' as id,sum(score) as score,'5' as typeid from engs_homework_student_text_read where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."'";
      $sql=$sql." union all select '1' as id,sum(score) as score,type as typeid from engs_homework_student_word_evaluat where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."' group by type";
      $sql=$sql." union all select examid as id,sum(score) as score,'6' as typeid from engs_homework_student_quiz where homeworkid='".$paper_id."' and classid='".$schoolclass."' and studentid='".$studentid."' group by examid";
      $srs=M()->query($sql);
      //定义作业总共有几块内容
      foreach($srs as $key=>$value){
        if($value["typeid"]=='4'){
          //单词跟读问题
          if(!empty($value["score"])){
            $score=$value["score"]/$hrs["wacount"]+$score;
          }
        }else if($value["typeid"]=='5'){
          //课文跟读
          if(!empty($value["score"])){
            $score=$value["score"]/$hrs["tacount"]+$score;
          }
        }else if($value["typeid"]=='6'){
          //听力训练
          if(!empty($value["score"])){
            //计算试卷的总分
            $totalscore=getExamScore($value["id"]);
            $score=($value["score"]*100)/$totalscore+$score;
          }
        }else if($value["typeid"]=='0'){
          //听音选词
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wccount"]+$score;
          }
        }else if($value["typeid"]=='2'){
          //单词拼写
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wscount"]+$score;
          }
        }else{
          //英汉互译
          if(!empty($value["score"])){
            $score=($value["score"]*100)/$hrs["wrcount"]+$score;
          }
        }
      }


      $submittime=Date("Y-m-d H:i:s");


        $starttime = $data["starttime"];
        $spendtime = $data["usetime"];
        $updatetime = $data["updatetime"];
        $updatetime = date("Y-m-d H:i:s",$updatetime);
        $submittime = Date("Y-m-d H:i:s");
//echo $sql;exit;
  

     // var_dump($data);

     //echo $data->success;
       
        $batchrs=M("homework_batch")->where("batchid='%s'  and paper_id=%d",$batchid,$paper_id)->find();
        //更新学生数据的表
        $rs = M("homework_student_list")->where("studentid='%s' and paper_id='%d'",$studentid,$paper_id)->find();
        //将homework表中的数据表进行处理
        //echo $rs["submittime"];exit;
        $publishstudents=json_decode($batchrs["publishstudents"],TRUE);
        if($rs["submittime"] != "" && $rs["submittime"]!="null"){
         // echo $publishstudents[$schoolclass]["students"][$studentid]["issubmit"];exit;
            $publishstudents[$schoolclass]["students"][$studentid]["score"]=(double)($score/$homeworkblock);
            $publishstudents[$schoolclass]["students"][$studentid]["time"]=(int)($spendtime);
            $publishstudents[$schoolclass]["students"][$studentid]["issubmit"]=1;
             //echo $publishstudents[$schoolclass]["students"][$studentid]["score"];exit;
            $publishstudents=json_encode($publishstudents);
        //跟新批次表
        M("homework_batch")->where("batchid='%s' and paper_id=%d",$batchid,$paper_id)->setField("publishstudents",$publishstudents);
        }
        
        
        $ret["msg"]="发布成功";
        $ret["state"]="1";
   
    }
}
//https://en.czbanbantong.com/Homework/Index/student_homework_feedback?homeworkId=14e4ba49d762474cac061eb00bba9d6c&isOverdue=false&studentId=350629100000099773&classId=158177149457782260&batchid=4c50f7ddb95d49ccaae56b26e6653173&paper_id=121133&sso=tmsfj.czbanbantong.com&ut=9a5d6bb2389ff230595405f581c7381f74d775c641751486900afcf2b6063f1d6a5fc13308401456bddbdd0eba876479&deviceType=mobile&deviceInfo=t%3A0%20xh%3AMuMu%20pp%3AAndroid%204.0.9-android-x86%2B%0A6.0.1%0AMuMu%0AV417IR%20release-keys%20vn%3AV4.1.5-beta&serAreaCode=2.