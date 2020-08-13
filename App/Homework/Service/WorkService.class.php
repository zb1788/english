<?php
namespace Homework\Service;
use Homework\Service\RedisService;
use Homework\Model\WorkWordReadViewModel as WorkWordRead;
use Homework\Model\WorkTextReadViewModel as WorkTextRead;
use Homework\Model\WorkWordTestViewModel as WorkWordTest;
use Homework\Model\WorkExamPaperViewModel as WorkExamPaper;


class WorkService 
{
	private $redis = null;

	public function __construct(){
		$this -> redis = new RedisService();
		file_put_contents("./log/redisInfo.log",Date("Y-m-d H:i:s").$this->redis.PHP_EOL,FILE_APPEND);
  	}


	/*单词听读*/
	public function getWordRead($homeworkid){
		//首先从redis中获取
		//$this -> redis -> delete($homeworkid."_0");
		$data = $this -> redis -> getAllSortSet($homeworkid."_0");
		if(empty($data)){
			//查询数据库
			$workWordRead = new WorkWordRead();
			$ret = $workWordRead -> where("isdel=1 and homeworkid = %d",$homeworkid)->order("Word.sortid,wordid,id")->select();
			//日志中打印sql
			\Think\Log::record("作业单词跟读".$workWordRead->getLastSql());
			foreach($ret as $key=>$value){
				$this -> redis -> addSortSet($homeworkid."_0",$key,serialize($value));
			}	
		}else{
			$ret = array();
			foreach($data as $key=>$value){
				$perdata = unserialize($value);
				$ret[] = $perdata;
			}
		}
		return $ret;
	}

	/*课文听读*/
	public function getTextRead($homeworkid){
		//$this -> redis -> delete($homeworkid."_1");
		//首先从redis中获取
		$data = $this -> redis -> getAllSortSet($homeworkid."_1");
		if(empty($data)){
			//查询数据库
			$workTextRead = new WorkTextRead();
			$ret = $workTextRead -> where("TextChapter.isdel=1 and Text.isdel=1 and homeworkid=%d",$homeworkid) -> order("TextChapter.sortid,Text.sortid,id")->select();
			\Think\Log::record("作业课文朗读".$workTextRead->getLastSql());
			foreach($ret as $key=>$value){
				$this -> redis -> addSortSet($homeworkid."_1",$key,serialize($value));
			}	
		}else{
			$ret = array();
			foreach($data as $key=>$value){
				$perdata = unserialize($value);
				$ret[] = $perdata;
			}
		}
		return $ret;
	}

	/*单词测试*/
	public function getWordTest($homeworkid){
		//首先从redis中获取
		//首先从redis中获取
	
		$this -> redis -> delete($homeworkid."_2");
		$data = $this -> redis -> getAllSortSet($homeworkid."_2");
		if(empty($data)){
			//查询数据库
			$workWordTest = new WorkWordTest();
			$ret = $workWordTest -> where("isdel =1 and homeworkid=%d",$homeworkid) -> order("HomeworkWordEvaluat.typeid,esortid,HomeworkWordEvaluat.id")->select();

			\Think\Log::record("作业单词测试".$workWordTest->getLastSql());
			foreach($ret as $key=>$value){
				$this -> redis -> addSortSet($homeworkid."_2",$key,serialize($value));
			}	
		}else{
			$ret = array();
			foreach($data as $key=>$value){
				$perdata = unserialize($value);
				$ret[] = $perdata;
			}
		}
		
		return $ret;
	}

	/*听力试卷*/
	public function getExamPaper($homeworkid){
		//首先从redis中获取
		//$this -> redis -> delete($homeworkid."_3");
		$data = $this -> redis -> getAllSortSet($homeworkid."_3");
		if(empty($data)){
			//查询数据库
			$workExamPaper = new WorkExamPaper();
			$ret = $workExamPaper -> where("ExamsStem.isdel =1 and ExamsQuestions.isdel =1 and homeworkid=%d",$homeworkid) -> order("ExamsStem.sortid,ExamsStem.id,ExamsQuestions.sortid,ExamsQuestions.id") -> select();
			\Think\Log::record("作业单词跟读".$workExamPaper->getLastSql());
			foreach($ret as $key=>$value){
				$this -> redis -> addSortSet($homeworkid."_3",$key,serialize($value));
			}	
		}else{
			$ret = array();
			foreach($data as $key=>$value){
				$perdata = unserialize($value);
				$ret[] = $perdata;
			}
		}
		return $ret;
	}


	//保存用户答案
	public function setWordRead($user,$question,$useranswer){
		$homewordStudentWordRead = M("homework_student_word_read");
		$data["homeworkid"]=$question["homeworkid"];
		$data["classid"]=$user["classid"];
		$data["studentid"]=$user["studentid"];
		$data["word_readid"]=$question["questionid"];
		$data["score"]=$useranswer["score"];
		$data["mp3"]=$useranswer["mp3"];
		$data["spendtime"]=$useranswer["spendtime"];
		$rs = $homewordStudentWordRead -> where("homeworkid=%d and classid='%s' and studentid='%s' and word_readid=%d")->find();
		if(empty($rs)){
			$homewordStudentWordRead->save($data);
		}else{
			$homewordStudentWordRead->where("homeworkid=%d and classid='%s' and studentid='%s' and word_readid=%d")->update($data);
		}
	}

	public function setTextRead($user,$question,$useranswer){
		$homewordStudentTextRead = M("homework_student_text_read");
		$data["homeworkid"]=$question["homeworkid"];
		$data["classid"]=$user["classid"];
		$data["studentid"]=$user["studentid"];
		$data["text_readid"]=$question["questionid"];
		$data["chapterid"]=$questionid;
		$data["textid"]=$questionid;
		$data["score"]=$useranswer["score"];
		$data["mp3"]=$useranswer["mp3"];
		$data["spendtime"]=$useranswer["spendtime"];
		$rs = $homewordStudentTextRead -> where("homeworkid=%d and classid='%s' and studentid='%s' and text_readid=%d and chapterid=%d and textid=%d")->find();
		if(empty($rs)){
			$homewordStudentTextRead->save($data);
		}else{
			$homewordStudentTextRead->where("homeworkid=%d and classid='%s' and studentid='%s' and word_readid=%d and chapterid=%d and textid=%d")->update($data);
		}
	}

	public function setWordTest($user,$question,$useranswer){
		$homewordStudentWordEvaluat = M("homework_student_word_evaluat");
		$data["homeworkid"]=$question["homeworkid"];
		$data["classid"]=$user["classid"];
		$data["studentid"]=$user["studentid"];
		$data["word_evaluatid"]=$question["questionid"];
		$data["wordid"]=$question["wordid"];
		$data["score"]=$useranswer["userscore"];
		//var_dump($useranswer);
		$useranswers = array();
		if(is_array($useranswer["useranswer"])){
			$useranswers = implode(",",$useranswer["useranswer"]);
		}else{
			$useranswers = $useranswer["useranswer"];
		}
		$data["answer"]=$useranswers;
		$data["iscorrect"]=$useranswer["iscorrect"]>0?1:0;
		$data["type"]=$question["typeid"];
		$data["do_time"]=Date("Y-m-d H:i:s");
		$data["spendtime"]=$useranswer["spendtime"];
		$rs = $homewordStudentWordEvaluat -> where("homeworkid=%d and classid='%s' and studentid='%s' and word_evaluatid=%d and wordid=%d",$question["homeworkid"],$user["classid"],$user["studentid"],$question["questionid"],$question["wordid"])->find();
		if(empty($rs)){
			$homewordStudentWordEvaluat->add($data);
		}else{
			$homewordStudentWordEvaluat->where("homeworkid=%d and classid='%s' and studentid='%s' and word_evaluatid=%d and wordid=%d",$question["homeworkid"],$user["classid"],$user["studentid"],$question["questionid"],$question["wordid"])->save($data);
		}
		if($user["studentid"]=="442001100001287176"){
			writeAppLog("gd_442001100001287176".Date("Y-m-d").".log",$homewordStudentWordEvaluat->getLastSql());
		}
	}

	public function setExamPaper($user,$question,$useranswer){
		$homewordStudentQuiz = M("homework_student_quiz");
		$data["homeworkid"]=$question["homeworkid"];
		$data["classid"]=$user["classid"];
		$data["studentid"]=$user["studentid"];
		$data["studentname"] = $user["studentname"];
		$data["questionsid"]=$question["questionid"];
		$data["examid"]=$question["examsid"];
		$data["quizid"]=$question["quizid"];
		$data["score"]=$useranswer["userscore"];
		$data["answer"]=$useranswer["useranswer"];
		$data["iscorrect"]=$useranswer["iscorrect"];
		$data["spendtime"]=$useranswer["spendtime"];
		$data["do_time"]=Date("Y-m-d H:i:s");
		//查询试题的答案
		$data["answerid"] = $question["answer"]["id"];
		$rs = $homewordStudentQuiz -> where("homeworkid=%d and classid='%s' and studentid='%s' and questionsid=%d ",$question["homeworkid"],$user["classid"],$user["studentid"],$question["questionid"])->find();
		if(empty($rs)){
			$homewordStudentQuiz->add($data);
		}else{
			$homewordStudentQuiz->where("homeworkid=%d and classid='%s' and studentid='%s' and questionsid=%d",$question["homeworkid"],$user["classid"],$user["studentid"],$question["questionid"])->save($data);
		}
	}

	/**
	 * 获取用户的答案
	 */
	public function getUserWordRead($homework,$user,$question){
		$rs  = $this -> redis -> getHash($user["studentid"]."_".$homework["homeworkid"]."_0",$question["questionid"]);
		$ret  = array();
		if(!$rs){
			$sql = "SELECT * FROM engs_homework_student_word_read WHERE homeworkid=".$homework["homeworkid"]." AND studentid='".$user["studentid"]."' AND word_readid=".$question["questionid"];
			$rs = M()->query($sql);
			if(empty($rs)){
				$ret = array();
				$ret["userscore"] = 0;
				$ret["useranswer"] = "";
			}else{
				$ret["userscore"] = $rs[0]["score"];
				$ret["useranswer"] = $rs[0]["mp3"];
			}
			$this -> redis -> addHash($user["studentid"]."_".$homework["homeworkid"]."_0",$question["questionid"],serialize($ret));	
		}else{
			$ret = unserialize($rs);
		}
		return $ret;
	}

	/**
	 * 获取用户的答案
	 */
	public function getUserTextRead($homework,$user,$question){
		$rs  = $this -> redis -> getHash($user["studentid"]."_".$homework["homeworkid"]."_1",$question["questionid"]."_".$question["textid"]);
		$ret  = array();
		if(!$rs){
			$sql = "SELECT * FROM engs_homework_student_text_read  WHERE homeworkid=".$homework["homeworkid"]." AND studentid='".$user["studentid"]."' AND text_readid=".$question["questionid"]." AND textid=".$question["textid"];
			$rs = M()->query($sql);
			if(empty($rs)){
				$ret = array();
				$ret["userscore"] = 0;
				$ret["useranswer"] = "";
			}else{
				$ret["userscore"] = $rs[0]["score"];
				$ret["useranswer"] = $rs[0]["mp3"];
			}
			$this -> redis -> addHash($user["studentid"]."_".$homework["homeworkid"]."_1",$question["textid"],serialize($ret));	
		}else{
			$ret = unserialize($rs);
		}
		return $ret;
	}

	/**
	 * 获取用户的答案
	 */
	public function getUserWordTest($homework,$user,$question){
		$rs  = $this -> redis -> getHash($user["studentid"]."_".$homework["homeworkid"]."_2",$question["questionid"]);
		if(!$rs){
			$sql = "SELECT * from engs_homework_student_word_evaluat WHERE homeworkid=".$homework["homeworkid"]." AND studentid='".$user["studentid"]."' AND word_evaluatid=".$question["questionid"];
			$rs = M()->query($sql);
			\Think\Log::record("作业单词测试用户信息".M()->getLastSql());
			if(empty($rs)){
				$ret = array();
				$ret["useranswer"] = "";
				$ret["userscore"] = 0;
				$ret["iscorrect"] = 0;
			}else{
				$answer = $rs[0]["answer"];
				if($rs[0]["type"] == 2){
					$answer = explode(",",$answer);
				}
				$ret["useranswer"] = $answer;
				$ret["userscore"] = $rs[0]["score"];
				$ret["iscorrect"] = $rs[0]["iscorrect"];
			}
			$this -> redis -> addHash($user["studentid"]."_".$homework["homeworkid"]."_2",$question["questionid"],serialize($ret));	
		}else{
			$ret = unserialize($rs);
		}
		//var_dump($ret);exit;
		return $ret;
	}

	/**
	 * 获取用户的答案
	 */
	public function getUserExamspaper($homework,$user,$question){
		
		$rs  = $this -> redis -> getHash($user["studentid"]."_".$homework["homeworkid"]."_3",$question["questionid"]);
		if(!$rs){
			$sql = "SELECT * FROM engs_homework_student_quiz  WHERE homeworkid=".$homework["homeworkid"]." AND studentid='".$user["studentid"]."' AND questionsid=".$question["questionid"];
			$rs = M()->query($sql);
			if(empty($rs)){
				$ret = array();
				$ret["useranswer"] = "";
				$ret["userscore"] = 0;
				$ret["iscorrect"] = 0;
			}else{
				$ret["useranswer"] = $rs[0]["answer"];
				$ret["userscore"] = $rs[0]["score"];
				$ret["iscorrect"] = $rs[0]["iscorrect"];
			}
			$this -> redis -> addHash($user["studentid"]."_".$homework["homeworkid"]."_3",$question["questionid"],serialize($ret));	
		}else{
			$ret = unserialize($rs);
		}
		//var_dump($ret);exit;
		return $ret;
	}

	/**
	 * 通过作业ID获取所有的试题结合
	 */
	public function getAllQuestionInfoByHomeworkid($homeworkid){
		$wordread	= $this -> getWordRead($homeworkid);
		$textread 	= $this -> getTextRead($homeworkid);
		$wordtest 	= $this -> getWordTest($homeworkid);
		$exampaper 	= $this -> getExamPaper($homeworkid);
		return array('wordread'=>$wordread,'textread'=>$textread,'wordtest'=>$wordtest,'exampaper'=>$exampaper);
	}


	public function getQuestionInfo($user,$homeworkid){
		$homework["homeworkid"] = $homeworkid;
		$wordread = $this -> getWordRead($homeworkid);
		if(!empty($user)){
			foreach($wordread as $key=>$value){
				//获取对应试题的答案
				$question["questionid"] = $value["id"];
				$wordread[$key]["useranswer"] = $this->getUserWordRead($homework,$user,$question);
			}
		}
		$textread = $this -> getTextRead($homeworkid);
		if(!empty($user)){
			foreach($textread as $key=>$value){
				//获取对应试题的答案
				$question["questionid"] = $value["id"];
				$question["textid"] = $value["textid"];
				$textread[$key]["useranswer"] = $this->getUserTextRead($homework,$user,$question);
			}
		}
		$wordtest = $this -> getWordTest($homeworkid);
		if(!empty($user)){
			foreach($wordtest as $key=>$value){
				//获取对应试题的答案
				$question["questionid"] = $value["id"];
				$wordtest[$key]["useranswer"] = $this->getUserWordTest($homework,$user,$question);
			}
		}
		$exampaper = $this -> getExamPaper($homeworkid);
		if(!empty($user)){
			foreach($exampaper as $key=>$value){
				//获取对应试题的答案
				$question["questionid"] = $value["quesid"];
				$exampaper[$key]["useranswer"] = $this->getUserExamspaper($homework,$user,$question);
				$exampaper[$key]["ques_id"] =$value["quesid"];
			}
		}
		foreach ($exampaper as $examkey => $examvalue) {
			$exampaper[$examkey]["tcontent"] = relace_tcontent($exampaper[$examkey]["tcontent"]);
			
		}
		return array(
			"wordread"=>$wordread,
			"textread"=>$textread,
			"wordtest"=>$wordtest,
			"exampaper"=>$exampaper
		);
	}


	/**
	 * 根据类型和作业ID该类型下所有的试题
	 */
	public  function getQuestionInfoByType($homeworkid,$type){
		$rs = array();
		if($type == 0){
			$rs = $this -> getWordRead($homeworkid);
		}else if($type == 1){
			$rs = $this -> getTextRead($homeworkid);
		}else if($type == 2){
			$rs = $this -> getWordTest($homeworkid);
		}else if($type ==3){
			$rs = $this -> getExamPaper($homeworkid);
		}
		return $rs;
	}

	//获取某一个试题的用户答案
	public function getUserQuestionAnswer($username,$homeworkid,$questionid,$type){
		$ret = $this -> redis -> hGet($username."_".$homeworkid."_".$type,$questionid);
		return $ret;
	}




	//获取用户的作答情况
	public function getUserAnswer($username,$homeworkid,$question){
		$rs = array();
		if($question["type"] == 0){
			$rs = $this -> getUserWordRead($homeworkid,$username,$question);
		}else if($question["type"] == 1){
			$rs = $this -> getUserTextRead($homeworkid,$username,$question);
		}else if($question["type"] == 2){
			$rs = $this -> getUserWordTest($homeworkid,$username,$question);
		}else if($question["type"] ==3){
			$rs = $this -> getUserExamPaper($homeworkid,$username,$question);
		}
		return $rs;
	}

	/**
	 * 获取试题
	 * 参数 homeworkid 作业ID type 表示类型 start表示从第几个开始获取 num表示获取几个 user表示当前用户
	 */
	public function getQuestions($homeworkid,$type,$start,$num,$user){
		$questions = $this -> getQuestionInfoByType($homeworkid,$type);
		$ret = array();
		if($start>count($questions)){
			return $ret;
		}
		$num = count($questions)>($start+$num)?$num:(count($questions)-$start);
		for($i=0;$i<$num;$i++){
			$questions[$start+$i]["userinfo"] = $this -> getUserAnswer($user["username"],$homeworkid,$questions[$start+$i]);
			array_push($ret,$questions[$start+i]);
		}
		return $ret;
	}


	//用户作答的情况
	public function setUserAnswer($user,$question,$useranswer){
		//设置用户的答案
		$score = $useranswer["userscore"];
		$answer = $useranswer["useranswer"];
		$flag = $useranswer["iscorrect"];
		$type = $question["datatype"];
		$this -> redis -> addHash($user["studentid"]."_".$question["homeworkid"]."_".$question["datatype"],$question["questionid"],serialize($useranswer));
		//将数据写入redis
        $data = $this->redis->getHash($user["studentid"],$question["homeworkid"]);
        if($data){
            $data = unserialize($data);
        }else{
            $data["usetime"] = 0;
        }
        $time = time();
        $curtime=Date("Y-m-d H:i:s");
        $studentTime["updatetime"] = $time;
        $studentTime["usetime"] =  $data["usetime"] + ($time - $data["updatetime"]) ;
        $this -> redis -> addHash($user["studentid"],$question["homeworkid"],serialize($studentTime));
		//插入数据库
		$data = $this->redis->getHash($user["studentid"],$question["homeworkid"]);
		$data = unserialize($data);
		//var_dump($data);
		if($user["studentid"]=="442001100001287176"){
			writeAppLog("gd_442001100001287176".Date("Y-m-d").".log",json_encode($user)."|||".json_encode($user).'|||'.json_encode($question)."|||".json_encode($useranswer));
		}
		if($type == 0){
			//$this->setWordRead($user,$question,$useranswer);
		}else if($type == 1){
			//$this->setTextRead($user,$question,$useranswer);
		}else if($type == 2){
			$this->setWordTest($user,$question,$useranswer);
		}else if($type ==3){
			$this->setExamPaper($user,$question,$useranswer);
		}
	}

	//获取用户做题时间
    public function getUserPaper($username,$paperid){
        $data = $this->redis->getHash($username,$paperid);
        return unserialize($data);
    }

    //设置用户做题时间
    public function setUserPaper($username,$paperid){
        $data = $this->redis->getHash($username,$paperid);
        if($data){
            $data = unserialize($data);
            $data["updatetime"] = time();
        }else{
            $data["usetime"] = 0;
            $data["updatetime"] = time();
            $data["dotime"] = time();
        }
        $this -> redis -> addHash($username,$paperid,serialize($data));
    }


	public function clearHomeworkData($homeworkid,$studentlist){
		$this -> redis -> delete($homeworkid."_0");
		$this -> redis -> delete($homeworkid."_1");
		$this -> redis -> delete($homeworkid."_2");
		$this -> redis -> delete($homeworkid."_3");
		file_put_contents("./log/clearnRedisData.log",Date("Y-m-d H:i:s").$homeworkid.PHP_EOL,FILE_APPEND);
		if(!empty($studentlist)){
			foreach($studentlist as $key=>$value){
				$student = $value["studentid"];
				$this -> redis -> delete($student."_".$homeworkid."_0");
				$this -> redis -> delete($student."_".$homeworkid."_1");
				$this -> redis -> delete($student."_".$homeworkid."_2");
				$this -> redis -> delete($student."_".$homeworkid."_3");
				file_put_contents("./log/clearnRedisData.log",Date("Y-m-d H:i:s").$student."_".$homeworkid.PHP_EOL,FILE_APPEND);
			}
		}
	}
}