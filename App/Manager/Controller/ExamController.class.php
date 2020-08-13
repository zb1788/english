<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 单词控制器
*  
* @author         gm 
* @since          1.0 
* @备注:此controller中使用flag标志,1标志来源于exam,2表示来源于基础题库
*/
class ExamController extends CheckController {
	
	/*考试页面开始处理begin*/
	/**
	 *  getExamList
	 *  获取考试列表
	 * @param  void
	 * @return  json 开始列表
	 */
	public function getExamList(){
		$unitid = I("unitid/s",0);
		$state=I("state/d",0);
		$model=M();
		$rs="";
		if($state==0){
			$sql="select * FROM engs_exams  where engs_exams.isdel=1 and engs_exams.ks_code='%s' and state=%d order by engs_exams.sortid,engs_exams.id";
			$rs= $model -> query($sql,$unitid);
		}else{
			$sql="select * FROM engs_exams  where engs_exams.isdel=1 and engs_exams.ks_code='%s' and state=%d order by engs_exams.sortid,engs_exams.id";
			$rs= $model -> query($sql,$unitid,$state);
		}
		$this -> ajaxReturn($rs);
	}
	
	/**
	 *  examDel
	 *  删除考试
	 * @param  void
	 * @return  void
	 */
	public function examDel(){
		$id = I("id/d",0);
		$exam=M("exams");
		$exam_paper=M("exams_paper");
		$model=M();
		$exam->where("id=%d",$id)->setField("isdel","0");
		$sql="update engs_exams_questions set isdel=0 where engs_exams_questions.paperid in (select id from engs_exams_paper where engs_exams_paper.examsid=%d)";
		$model->execute($sql,$id);
		unset($sql);
		$sql="delete from engs_exams_questions_answer where engs_exams_questions_answer.questionsid in (select id from engs_exams_questions where engs_exams_questions.isdel=0)";
		$model->execute($sql);
		unset($sql);
		$sql="delete from engs_exams_questions_items where engs_exams_questions_items.questionsid in (select id from engs_exams_questions where engs_exams_questions.isdel=0)";
		$model->execute($sql);
		$exam_paper->where("examsid=%d",$id)->delete();
	}
	
	/**
	 *  listUpExam
	 *  保存修改顺序
	 * @param  void
	 * @return  void
	 */
	public function listUpExam(){
		$data =  stripslashes($_REQUEST["data"]);
		$data = rtrim($data ,'"');
		$data = ltrim($data ,'"');
		$data = str_replace('&quot;', '"', $data);
		$res = json_decode($data);
		foreach($res as $obj){
			$id = $obj->id;
			$sortid = $obj->sortid;
			$id = !is_numeric($id)?0:$id;
			$sortid = !is_numeric($sortid)?0:$sortid;
			$exams=M("exams");
			$exams->sortid=$sortid;
			$result=$exams -> where('id=%d',$id)->save();
		}
		$arr_return["msg"]=1;
		$arr_return["err"]="修改成功";
		$this -> ajaxReturn($arr_return);
	}
	/*考试页面结束处理end*/
	
	
	/*考试页面编辑begin*/
	/**
	 * unitexam_add
	 * 考试名称编辑弹出窗口
	 * @param 	void
	 * @return  null
	 */
	public function unitexam_add(){
		$id = I('id/d',0);
		$examname="";
		if($id!=0){
			$examname=M("exams")->where("id=%d",$id)->find();
		}
		$this->assign("name",$examname["name"]);
		$this->assign("examtime",$examname["exams_times"]);
		$stateid=I('stateid/s',0);
		$gradeid=I("gradeid/s",0);
		$termid=I("termid/s",0);
		$versionid=I("versionid/s",0);
		$unitid=I("unitid/s",0);
		$this->assign("gradeid",$gradeid);
		$this->assign("termid",$termid);
		$this->assign("versionid",$versionid);
		$this->assign("unitid",$unitid);
		$this->assign("stateid",$stateid);
		$this->assign("id",$id);
		$this->display();
	}


   
	/**
	 * examEdit
	 * 考试名称编辑
	 * @param 	void
	 * @return  null
	 */
	public function examEdit(){
		$unitid = I('unitid/s',0);
		$stateid=I("stateid/d",0);
		$id = I('id/d',0);
		$examname =I('exam');
		$examtime=I("examtime/s",0);
		$exams=M("exams");
		if ($id==0) {
			$count=$exams -> where("ks_code='%s'",$unitid) -> count();
			$exams -> name =  trim($examname);
			$exams -> ks_code = $unitid ;
			$exams -> isdel  = 1;
			$exams -> exams_times = $examtime;
			$exams -> exams_classid = 1;
			$exams -> sortid= $count+1;
			$exams -> state=1;
			$exams -> addtime=Date("y-m-d H:i:s");
			$exams -> username=session("adminuser");
			$id=$exams -> add();
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "添加成功";
		}else
		{
			//修改
			$exams -> name =  trim($examname);
			$exams -> exams_times = $examtime;
			$exams -> ks_code = $unitid;
			$exams -> state=$stateid;
			if($stateid==4){
				$exams->pubtime=Date("y-m-d H:i:s");
				//进行数据的处理
				$this->dodata($id);
			}
			$exams -> where("id=%d",$id) -> save();
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "修改成功";
		}
		$this->ajaxReturn($arr_return);
	}
	
	/*考试页面编辑end*/
    

     /*真题考试页面编辑begin*/
     

     /**
	 * gettopicexam
	 * 考试名称编辑弹出窗口
	 * @param 	void
	 * @return  null
	 */
	public function getTopicExamList(){
		$stateid=I('stateid/d',0);
		$provinceid=I("provinceid/d",0);
		$typeid=I("typeid/d",0);
		$levelid=I("levelid/d",0);
		$model=M();
		$sql="select t.id,(SELECT ep.title FROM engs_dictionary ep WHERE ep.id=t.provinceid) AS province,(SELECT ep.title FROM engs_dictionary ep WHERE ep.id=t.levelid) AS levelid,(SELECT ep.title FROM engs_dictionary ep WHERE ep.id=t.typeid) as typeid,t.state,(SELECT ep.title FROM engs_dictionary ep WHERE ep.id=t.yearid) as year,t.name FROM";
		$sql.=" engs_exams t WHERE t.provinceid=%d and t.levelid=%d and t.typeid=%d and t.state=%d and isdel=1 order by t.sortid,t.id";
		$rs=$model->query($sql,$provinceid,$levelid,$typeid,$stateid);
		$this->ajaxReturn($rs);
	}





	/**
	 * topicexam_add
	 * 考试名称编辑弹出窗口
	 * @param 	void
	 * @return  null
	 */
	public function topicexam_add(){
		$id = I('id/d',0);
		$en_dictionary=M("dictionary");
		//取出关键词的
		$provincearr=$en_dictionary->where("code = 'province'")->order("code,sortid")->select();
		$yeararr=$en_dictionary->where("code = 'year'")->order("code,sortid")->select();
		$this->assign("provincearr",$provincearr);
		$this->assign("yeararr",$yeararr);
		$examname="";
		if($id!=0){
			$examname=M("exams")->where("id=%d",$id)->find();
		}
		$this->assign("name",$examname["name"]);
		$this->assign("yearid",$examname["yearid"]);
		$this->assign("examtime",$examname["exams_times"]);
		$stateid=I('stateid/d',0);
		if($id==0){
			$stateid=1;
		}
		$provinceid=I("provinceid/d",0);
		$typeid=I("typeid/d",1);
		$levelid=I("levelid/d",1);
		$this->assign("provinceid",$provinceid);
		$this->assign("typeid",$typeid);
		$this->assign("levelid",$levelid);
		$this->assign("stateid",$stateid);
		$this->assign("id",$id);
		$this->display();
	}


	/**
	 * topicexamEdit
	 * 考试名称编辑
	 * @param 	void
	 * @return  null
	 */
	public function examTopicEdit(){
		$stateid=I('stateid/d',0);
		$provinceid=I("provinceid/d",0);
		$typeid=I("typeid/d",0);
		$levelid=I("levelid/d",0);
		$year=I("yearid/d",0);
		$id = I('id/d',0);
		$examname =I('exam');
		$examtime=I("examtime/d",0);
		$exams=M("exams");
		if ($id==0) {
			$count=$exams -> where("provinceid=%d and isdel=1 and typeid=%d and levelid=%d",$provinceid,$typeid,$levelid) -> count();
			$exams -> name =  trim($examname);
			$exams -> exams_times=$examtime;
			$exams -> provinceid = $provinceid ;
			$exams -> levelid = $levelid ;
			$exams -> yearid = $year ;
			$exams -> typeid = $typeid ;
			$exams -> isdel = 1;
			$exams -> classid = 2;
			$exams -> sortid= $count+1;
			$exams -> state=1;
			$exams -> addtime=Date("y-m-d H:i:s");
			$exams -> username=session("adminuser");
			$id=$exams -> add();
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "添加成功";
		}else
		{
			//修改
			$exams -> exams_times=$examtime;
			$exams -> name =  trim($examname);
			$exams -> provinceid = $provinceid ;
			$exams -> levelid = $levelid ;
			$exams -> yearid = $year ;
			$exams -> typeid = $typeid ;
			$exams -> name =  trim($examname);
			$exams -> state=$stateid;
			if($stateid==4){
				$exams->pubtime=Date("y-m-d H:i:s");
				//进行数据的处理
				$this->dodata($id);
			}
			$exams -> where("id=%d",$id) -> save();
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "修改成功";
		}
		$this->ajaxReturn($arr_return);
	}

	
	/*真题考试页面编辑end*/



	
	/*试卷页面开始处理begin*/
	/**
	 *  unitexam_questions
	 *  试卷页面
	 * @param  void
	 * @return  void
	 */
	public function unitexam_questions(){
		$source=I("source/d",0);
		$this->assign("source",$source);
		$gradeid = I("gradeid/d",0);
		$versionid = I("versionid/d",0);
		$termid = I("termid/d",0);
		$unitid = I("unitid/d",0);
		$stateid = I("stateid/d",0);
		$examsid=I("examsid/d",0);
		$this->assign("listentype",C(CONST_VOICETYPE));
		$this->assign( "gradeid", $gradeid );
		$this->assign( "versionid", $versionid );
		$this->assign( "termid", $termid );
		$this->assign( "unitid", $unitid );
		$this->assign( "examsid", $examsid );
		$this->assign( "stateid", $stateid );
		$this->assign("flag","1");
		$this->display();
	}
	
	
	/**
	 *  getExamQuestionList
	 *  获取考试的题干内容
	 * @param  void
	 * @return  json
	 */
	public function getExamQuestionList(){
		$examsid=I("examsid/d",0);
		$exams_paper=M("exams_paper");
		$rs=$exams_paper->where("examsid=%d ",$examsid)->field("id,sortid,classid,examdaid,amount,score,tcontent,astoptime,bstoptime,(select count(*) from engs_exams_questions eq where eq.paperid=engs_exams_paper.id and isdel=1) as count,vvoiceid,tvoiceid")->order("sortid,id")->select();
		$this->ajaxReturn($rs);
	}
	
	
	/**
	 * exam_questions_add
	 * 考试添加组合小题答题页面
	 * @param 	void
	 * @return  null
	 */
	public function exam_questions_add(){
		$id=I("id/d");
		$examsid=I("examsid/d",0);
		$classid=I("classid/d",0);
		$quesflag=I("quesflag/d",0);
		$exams_paper=M("exams_paper");
		$this ->assign("examsid",$examsid);
		$this ->assign("id",$id);
		$this ->assign("classid",$classid);
		$this ->assign("quesflag",$quesflag);
		$repeate=1;
		$bstoptime=3;
		$astoptime=3;
		if($classid==0){
			$bstoptime=1;
		    $astoptime=1;
		}
		if(($id)!=0)
		{
			$rs=$exams_paper -> where("id=%d",$id) -> field("id,sortid,classid,examdaid,amount,score,readtimes,tcontent,vcontent,astoptime,bstoptime,vvoiceid,tvoiceid") -> find();
			if(!empty($rs))
			{
				$repeate=$rs['readtimes'];
				$this ->assign("id",$rs['id']);
				$this ->assign("examdaid",$rs['examdaid']);
				$this ->assign("sortid",$rs['sortid']);
				$this ->assign("amount",$rs['amount']);
				$this ->assign("score",$rs['score']);
				$this ->assign("tcontent",$rs['tcontent']);
				$this ->assign("vcontent",htmlspecialchars_decode($rs['vcontent']));
				$bstoptime=$rs['bstoptime'];
				$astoptime=$rs['astoptime'];
				$this ->assign("vvoiceid",$rs['vvoiceid']);
				$this ->assign("tvoiceid",$rs['tvoiceid']);
			}
		}
		$this ->assign("repeate",$repeate);
		$this ->assign("bstoptime",$bstoptime);
		$this ->assign("astoptime",$astoptime);
		$this ->display();
	}
	
	
	
	/**
	 * add_exam_questions
	 * 添加组合小题
	 * @param 	void
	 * @return  json
	 */
	public function add_exam_questions(){
		$id = I("id/d",0);
		$classid=I("classid/d",0);
		$examdaid=I("questype/d",0);
		$examsid = I("examsid/d",0);
		$repeat=I("repeat/d",1);
		$vvoiceid=I("vvoiceid/d",0);
		$tvoiceid=I("tvoiceid/d",0);
		$exams_paper=M("exams_paper");
		$vcontent = html_entity_decode(I("vcontent/s",0));
		$tcontent = html_entity_decode(I("tcontent/s",0));
		$score = I("score/s");
		$bstoptime = I("bstoptime/d");
		$astoptime = I("astoptime/d");
		$arr_return["isadd"] = 0;
		$arr_return["msg"] = "修改失败";
		if ($id==0) {
			$sort=$exams_paper->where("examsid=%d",chapterid)->field("max(sortid) sortid")->find();
			$data["classid"]=$classid;
			$data["examdaid"]=$examdaid;
			$data["examsid"] = $examsid;
			$data["vcontent"] = $vcontent;
			$data["tcontent"] = $tcontent;
			$data["score"] =  floatval($score);
			$data["readtimes"] = $repeat;
			$data["tvoiceid"]=$tvoiceid;
			$data["vvoiceid"]=$vvoiceid;
			$data["bstoptime"] = $bstoptime;
			$data["astoptime"] = $astoptime;
			$data["sortid"] = ($sort["sortid"]+1);
			$exams_paper -> add($data);
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "添加成功";
		}else
		{
			//修改
			$exams_paper -> examdaid=$examdaid;
			$exams_paper -> examsid = $examsid;
			$exams_paper -> stoptime = $stoptime;
			$exams_paper -> score =  floatval($score);
			$exams_paper -> vvoiceid=$vvoiceid;
			$exams_paper -> tvoiceid=$tvoiceid;
			$exams_paper -> readtimes=$repeat;
			$exams_paper -> vcontent = $vcontent;
			$exams_paper -> tcontent = $tcontent;
			$exams_paper -> bstoptime=$bstoptime;
			$exams_paper -> astoptime=$astoptime;
			$exams_paper -> classid=$classid;
			$exams_paper -> where("id=%d",$id) -> save();
			$arr_return["isadd"] = 1;
			$arr_return["msg"] = "修改成功";
	
		}
		$this -> ajaxReturn($arr_return);
	}
	
	/**
	 *  listUpExamPaper
	 *  保存Paper表中的顺序
	 * @param  void
	 * @return  json
	 */
	public function listUpExamPaper(){
		$data =  stripslashes($_REQUEST["data"]);
		$data = rtrim($data ,'"');
		$data = ltrim($data ,'"');
		$data = str_replace('&quot;', '"', $data);
		$res = json_decode($data);
		foreach($res as $obj){
			$id = $obj->id;
			$sortid = $obj->sortid;
			$id = !is_numeric($id)?0:$id;
			$sortid = !is_numeric($sortid)?0:$sortid;
			$exams_paper=M("exams_paper");
			$exams_paper->sortid=$sortid;
			$result=$exams_paper -> where('id=%d',$id)->save();
		}
		$arr_return["msg"]=1;
		$arr_return["err"]="修改成功";
		$this -> ajaxReturn($arr_return);
	}
	
	/**
	 *  examPaperDel
	 *  删除试卷
	 * @param  void
	 * @return  void
	 */
	public function examPaperDel(){
		$paperid=I("paperid/d",0);
		$exams_paper=M("exams_paper");
		$exams_paper->where("id=%d",$paperid)->delete();
		$rs=M("exams_questions")->where("paperid=%d",$paperid)->select();
		foreach($rs as $key=>$value){
			M("exams_questions_answer")->where("questionsid=%d",$value["id"])->delete();
			M("exams_questions_items")->where("questionsid=%d",$value["id"])->delete();
		}
		M("exams_questions")->where("paperid=%d",$paperid)->setField("isdel","0");
	}
	/*试卷页面结束处理end*/
	
	
	/*试题列表页面开始处理end*/
	/**
	 *  unitexam_questions_list
	 *  取出问题的答案
	 * @param  void
	 * @return  void
	 */
	public function unitexam_questions_list(){
		$paperid=I("paperid/d");
		$classid=I("classid/d");
		$questype=I("questype/d");
		$examsid=I("examsid/d",0);
		$flag=I("flag/d");
		$this->assign("flag",$flag);
		$this->assign("paperid",$paperid);
		$this->assign("classid",$classid);
		$this->assign("questype",$questype);
		$this->assign("examsid",$examsid);
		$this->display();
	}
	
	/**
	 *  listupExamsQuestions
	 *  将试卷中的小题进行顺序的调整
	 * @param  void
	 * @return  void
	 */
	public function listupExamsQuestions(){
		$data =  stripslashes($_REQUEST["data"]);
		$data = rtrim($data ,'"');
		$data = ltrim($data ,'"');
		$data = str_replace('&quot;', '"', $data);
		$res = json_decode($data);
		foreach($res as $obj){
			$id = $obj->id;
			$sortid = $obj->sortid;
			$id = !is_numeric($id)?0:$id;
			$exams_questions=M("exams_questions");
			$exams_questions->sortid=$sortid;
			$result=$exams_questions -> where('id=%d',$id)->save();
		}
		$arr_return["msg"]=1;
		$arr_return["err"]="修改成功";
		$this -> ajaxReturn($arr_return);
	}
	/*试题列表页面结束处理end*/
	
	
	
	/*单题处理公共方法begin*/
	/**
	 *  getExamQuestionItems
	 *  取出问题的选项
	 * @param  $questionsid表示问题的id,$flag表示来源
	 * @return  问题选项
	 */
	protected function getExamQuestionItems($questionsid,$flag){
		if($flag==1){
			$questions_itmes=M("exams_questions_items");
		}else{
			$questions_itmes=M("base_questions_items");
		}
		
		$rs=$questions_itmes->where("questionsid=%d",$questionsid)->order("sortid,id")->select();
		return $rs;
	}
	
	
	/**
	 *  getExamQuestionAnswer
	 *  取出问题的答案
	 * @param   $questionsid表示问题的id,$flag表示来源
	 * @return  问题的答案
	 */
	protected function getExamQuestionAnswer($questionsid,$flag){
		if($flag==1){
			$questions_answer=M("exams_questions_answer");
		}else{
			$questions_answer=M("base_questions_answer");
		}
		$rs=$questions_answer->where("questionsid=%d",$questionsid)->order("sortid,id")->select();
		return $rs;
	}
	/**
	 *  getExamQuestionList
	 *  获取问题以及问题的答案
	 * @param  void
	 * @return  void
	 */
	public function getUnitExamQuestionList(){
		$paperid=I("paperid/d");
		$classid=I("classid/d");
		$flag=I("flag/d");
		$rs="";
		if($flag==1){
			$questions=M("exams_questions");
			$rs=$questions -> where("paperid=%d and isdel=1 and classid=%d",$paperid,$classid)->field("id,paperid,baseid,examdaid,classid,parid,complexity,source,tcontent,acontent,itemtype")->order("sortid,id")->select();
		}else{
			$questions=M("base_questions");
			$rs=$questions -> where("isdel=1 and classid=%d",$classid)->field("id,paperid,baseid,examdaid,classid,parid,complexity,source,tcontent,acontent,itemtype")->order("sortid,id")->select();
		}
		foreach($rs as $key=>$value){
			$rs[$key]["items"]=$this->getExamQuestionItems($value["id"],$flag);
			$rs[$key]["answer"]=$this->getExamQuestionAnswer($value["id"],$flag);
		}
		$this->ajaxReturn($rs);
	}
	
	/**
	 * base_questions_add
	 * 添加独立小题页面
	 * @param 	void
	 * @return  null
	 */
	public function base_questions_add(){
		$flag=I("flag/d",0);
		$classid=I("classid/d",0);
		$questype=I("questype/d",0);
		$id=I("id/d",0);
		//type表示是不是弹窗
		$type=I("type/d",0);
		$paperid=I("paperid/d",0);
		$examsid=I("examsid/d",0);
		$this->assign("type",$type);
		$this->assign("id",$id);
		$this->assign("paperid",$paperid);
		$this->assign("examsid",$examsid);
		$this->assign("classid",$classid);
		$this->assign("flag",$flag);
		$this->assign("questype",$questype);
		if($id>0){
			//修改弹出页面
		    $rs="";
		    $answer="";
		    $items="";
		    $listen="";
			if($flag==1){
				$rs=M("exams_questions")->where("id=%d",$id)->find();
				$answer=M("exams_questions_answer")->where("questionsid=%d",$id)->field("id,answer")->find();
				$items=M("exams_questions_items")->where("questionsid=%d",$id)->field("id,sortid,content")->order("sortid")->select();
				$listen=M("exams_questions_listen")->where("questionsid=%d",$id)->order("sortid,id")->select();
			}else{
				$rs=M("base_questions")->where("id=%d",$id)->find();
				$answer=M("base_questions_answer")->where("questionsid=%d",$id)->field("id,answer")->find();
				$items=M("base_questions_items")->where("questionsid=%d",$id)->field("id,sortid,content")->order("sortid")->select();
				$listen=M("base_questions_listen")->where("questionsid=%d",$id)->order("sortid,id")->select();
			}
			$this->assign("tcontent",$rs["tcontent"]);
			$this->assign("acontent",$rs["acontent"]);
			$this->assign("questype",$rs["examdaid"]);
			$this->assign("itemtype",$rs["itemtype"]);
			$this->assign("answer_content",$answer["answer"]);
			$this->assign("answer_id",$answer["id"]);
			$this->assign("itmes",$items);
			$this->assign("listen",$listen);
		}
		else
		{
			//新增小题显示页面
			$complexity=I("complexity/d",0);
			$itemtype=I("itemtype/d",1);
			$this->assign("itemtype",$itemtype);
			$grade=M("grade");
			$rs=$grade->field("id,name")->order("sortid")->select();
			$this->assign("complexity",$complexity);
			$this->assign("grade",$rs);
		}
		$this->display();
	}
	
	
	/**
	 * delete_questions_list
	 * 独立小题删除
	 * @param 	void
	 * @return  null
	 */
	public function delete_questions_list(){
		$id=I("id/d");
		$flag=I("flag/d",0);
		if($flag==1){
			$base_questions=M("exams_questions");
			$base_questions_answer=M("exams_questions_answer");
			$base_questions_items=M("exams_questions_items");
		}else{
			$base_questions=M("base_questions");
			$base_questions_keys=M("base_questions_keys");
			$base_questions_grade=M("base_questions_grade");
			$base_questions_answer=M("base_questions_answer");
			$base_questions_items=M("base_questions_items");
			$base_questions_keys->where("questionsid=%d",$id)->delete();
			$base_questions_grade->where("questionsid=%d",$id)->delete();
		}
		//逻辑删除试题
		$base_questions->where("id=%d",$id)->setField("isdel",'0');
	
		//删除答案
		$base_questions_answer->where("questionsid=%d",$id)->delete();
		$base_questions_items->where("questionsid=%d",$id)->delete();
		$rs=$base_questions->where("parid=%d and isdel=1",$id)->field("id")->select();
		if(!empty($rs)){
			foreach($rs as $key=>$value){
				$base_questions_answer->where("questionsid=%d",$value["id"])->delete();
				$base_questions_items->where("questionsid=%d",$value["id"])->delete();
				if($flag!=1){
					$base_questions_keys->where("questionsid=%d",$value["id"])->delete();
					$base_questions_grade->where("questionsid=%d",$value["id"])->delete();
				}
			}
		}
		$base_questions->where("parid=%d",$id)->setField("isdel",'0');
	
	}
	
	/**
	 * combine_questions_add
	 * 短文试题添加页面
	 * @param 	void
	 * @return  null
	 */
	public function combine_questions_add(){
		$flag=I("flag/d");
		$paperid=I("paperid/d",0);
		$examsid=I("examsid/d",0);
		$this->assign("paperid",$paperid);
		$this->assign("examsid",$examsid);
		$this->assign("flag",$flag);
		$type=I("type/d",1);
		$id=I("id/d",0);
		$grade=M("grade");
		$currentpage=I("currentpage/d",1);
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		$questype=I("questype/d",0);
		$classid=I("classid/d",0);
		$complexity=I("complexity/d",0);
		$this->assign("classid",$classid);
		$this->assign("complexity",$complexity);
		$this->assign("questype",$questype);
		$this->assign("currentpage",$currentpage);
		$this->assign("type",$type);
		$this->assign("flag",$flag);
		$this->assign("source",$source);
		$this->assign("id",$id);
		if($id>0){
			if($flag==1){
				$base_questions=M("exams_questions");
				$base_questions_listen=M("exams_questions_listen");
			}else{
				$base_questions=M("base_questions");
				$base_questions_listen=M("base_questions_listen");
			}
			$listen=$base_questions_listen->where("questionsid=%d",$id)->order("sortid,id")->select();
			$rs=$base_questions->where("id=%d",$id)->find();
			$this->assign("tcontent",$rs["tcontent"]);
			$this->assign("listen",$listen);
			$this->assign("acontent",$rs["acontent"]);
			$this->display();
		}else{
			$classid=I("classid/d",0);
			$questype=I("questype/d",0);
			$complexity=I("complexity/d",0);
			$unitid=I("unitid/d",0);
			$termid=I("termid/d",0);
			$gradeid=I("gradeid/d",0);
			$versionid=I("versionid/d",0);
			$this->assign("classid",$classid);
			$this->assign("unitid",$unitid);
			$this->assign("gradeid",$gradeid);
			$this->assign("versionid",$versionid);
			$this->assign("termid",$termid);
			$this->assign("examsid",$examsid);
			$this->assign("paperid",$paperid);
			$this->assign("complexity",$complexity);
			$this->assign("questype",$questype);
			$this->display();
		}
	}
	
	

	
	
	/**
     * base_questions_list  
	 * 独立小题管理
     * @param 	void
     * @return  null
     */
	public function base_questions_list(){
		$this->assign("flag",'2');
		$questype=I("questype/d",1);
		$this->assign("questype",$questype);
		$complexity=I("complexity/d",1);
		$this->assign("complexity",$complexity);
		$currentpage=I("currentpage/d",1);
		$this->assign("currentpage",$currentpage);
		$this->assign("examflag",'0');
		$grade=M("grade");
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		$this->display();
	}
	
	
	
	/**
	 * get_questions_list
	 * 获取独立小题列表
	 * @param 	void
	 * @return  null
	 */
	public function get_questions_list(){
		import('ORG.Util.Page');
		$complexity=I("complexity/d");
		$questype=I("questype/d");
		$gradeids=I("gradeids/s");
		$keyname=I("keyname/s");
		$paperid=I("paperid/d",0);
		$classid=I("classid/d",0);
		$pageCurrent=I('pageCurrent/d',0);//当前页
		$page_size=I('page_size/d',0);//每页显示数量
		$model=M();
		//拼接sql
		$sql="SELECT id,examdaid,classid,itemtype,'".$paperid."' as paperid,";
		if($paperid!=0){
			$sql.="(SELECT engs_exams_questions.baseid  FROM engs_exams_questions WHERE engs_exams_questions.baseid=engs_base_questions.id AND engs_exams_questions.isdel=1 and engs_exams_questions.paperid=".$paperid." ) AS baseid,";
		}
		$sql.="(SELECT GROUP_CONCAT(engs_grade.`name`) FROM engs_base_questions_grade LEFT JOIN engs_grade ON engs_base_questions_grade.gradeid=engs_grade.id WHERE engs_base_questions_grade.questionsid=engs_base_questions.id) as grade,";
        $sql.="(SELECT GROUP_CONCAT(engs_base_questions_keys.keyname) FROM engs_base_questions_keys WHERE engs_base_questions_keys.questionsid=engs_base_questions.id) as keyname,";
        $sql.="tcontent,acontent,complexity FROM engs_base_questions WHERE engs_base_questions.isdel=1 and engs_base_questions.classid=".$classid." and engs_base_questions.complexity=".$complexity." AND engs_base_questions.examdaid=".$questype;
        if(!empty($gradeids)){
        	$sql.=" AND engs_base_questions.id IN (SELECT questionsid FROM engs_base_questions_grade WHERE engs_base_questions_grade.gradeid IN (".$gradeids.")";
        }
		if(!empty($keyname))
		{
			$sql.=" UNION ALL SELECT questionsid FROM engs_base_questions_keys WHERE ";
			$keyname=explode($keyname,",");
			$count=count(keyname);//定义关键字个数
			foreach($keyname as $key=>$value){
				$sql.=" engs_base_questions_keys.keyname like '%".$value."'";
				if($count>1&&key!=($count-1))$sql.=" and ";
			}
			$sql.=")";
		}else{
			if(!empty($gradeids)){$sql.=")";}
		}
		$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
		$sql_order=' order by id desc';
		//echo $sql.$sql_where.$sql_limit;exit();
		$data_total=$model->query($sql);
		$total=count($data_total);
		$sub_pages=4;
		/* 实例化一个分页对象 */
		Vendor('SubPages');
		$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
		$page= $subPages->subPageCss3();
		$data=$model->query($sql.$sql_order.$sql_limit);
		foreach($data as $key=>$value){
			$data[$key]["items"]=M("base_questions_items")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
			$data[$key]["answer"]=M("base_questions_answer")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
		}
		//echo $sql.$sql_order.$sql_limit;exit();
		//echo $sql.$sql_where.$sql_order.$sql_limit;
		$word[$page]=$data;
		$this->ajaxReturn($word);
	}

	/**
	 * add_base_questions
	 * 独立小题添加
	 * @param 	void
	 * @return  null
	 */
	public function add_base_questions(){
		$id=I("id/d",0);
		$flag=I("flag/d");
		$paperid=I("paperid/d");
		$questype=I("questype/d");
		$tcontent=html_entity_decode(I("tcontent/s"));
		$vcontent =  stripslashes(I("vcontent"));
		$vcontent = rtrim($vcontent ,'"');
		$vcontent = ltrim($vcontent ,'"');
		$vcontent = str_replace('&quot;', '"', $vcontent);
		$vcontent=json_decode($vcontent);
		$acontent=html_entity_decode(I("acontent/s"));
		$gradeids=I("gradeid/s");
		$source=I("source/s");
		$keyname=I("keyword/s");
		$complexity=I("complexity/d");
		$answer=I("answer/s");
		$answerids=I("answerids/s");
		$itemsflag=I("itemsflag/s");
		$itemcontent=I("itemcontent/s");
		$itemtype=I("itemtype/d");
		$itemsids=I("itemsids/s");
		//base_questions中添加小题
		if($id=='0')
		{
			$base_questions=M("base_questions");
			$base_questions->examdaid=$questype;
			$base_questions->classid=1;
			$base_questions->complexity=$complexity;
			$base_questions->source=$source;
			$base_questions->tcontent=$tcontent;
			$base_questions->acontent=$acontent;
			$base_questions->addtime=Date("y-m-d H:i:s");
			$base_questions->isdel=1;
			$base_questions->parid=0;
			$base_questions->itemtype=$itemtype;
			$lastid=$base_questions->add();
			//添加听力材料
			
			foreach($vcontent as $obj){
				$base_questions_listen=M("base_questions_listen");
				$listenid = $obj->id;
				$sortid = $obj->sortid;
				if($sortid==0){
					$sortid=1;
				}
				$content = $obj->content;
				$voiceid = $obj->voiceid;
				if($listenid!='0'){
					
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->where("id=%d",$listenid)->save();
				}else{
					$base_questions_listen->questionsid=$lastid;
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->add();
				}
			}
			//base_questions_grade中添加年级
			$base_questions_grade=M("base_questions_grade");
			$grade=explode("*",$gradeids);
			foreach($grade as $key=>$value){
				$base_questions_grade->questionsid=$lastid;
				$base_questions_grade->gradeid=$value;
				$base_questions_grade->add();
			}
			//base_questions_keys中添加关键字
			$base_questions_keys=M("base_questions_keys");
			if(!empty($keyname))
			{
				$keyname=explode(",",$keyname);
				foreach($keyname as $key=>$value){
					$base_questions_keys->questionsid=$lastid;
					$base_questions_keys->keyname=$value;
					$base_questions_keys->add();
				}
			}
			//对于非判断题需要进行选项的添加engs_base_questions_items
			$base_questions_items=M("base_questions_items");
			if($questype=='1'){
				$itemsflag=explode("*",$itemsflag);
				$itemcontent=explode("*",$itemcontent);
				foreach($itemsflag as $key=>$value){
					$base_questions_items->questionsid=$lastid;
					$base_questions_items->flag=$value;
					$base_questions_items->content=$itemcontent[$key];
					$base_questions_items->typeid=$questype;
					$base_questions_items->sortid=($key+1);
					$base_questions_items->add();
				}
			}
			//base_questions_answer中添加答案
			$base_questions_answer=M("base_questions_answer");
			$answer=explode("*",$answer);
			foreach($answer as $key=>$value){
				$base_questions_answer->questionsid=$lastid;
				$base_questions_answer->answer=$value;
				$base_questions_answer->sortid=($key+1);
				$base_questions_answer->add();
			}
			//同时将base中的数据添加到exam中
			if(!empty($paperid)){$this->baseToExam($lastid,$paperid);}
		}else{
			//base_questions中添加小题
			if($flag==1){
				$exams_questions=M("exams_questions");
				$exams_questions_items=M("exams_questions_items");
				$exams_questions_answer=M("exams_questions_answer");
				$exams_questions_answer=M("exams_questions_answer");
				$exams_questions_listen=M("exams_questions_listen");
			}else{
				$exams_questions=M("base_questions");
				$exams_questions_items=M("base_questions_items");
				$exams_questions_answer=M("base_questions_answer");
				$exams_questions_answer=M("base_questions_answer");
				$exams_questions_listen=M("base_questions_listen");
			}
			$exams_questions->tcontent=$tcontent;
			$exams_questions->acontent=$acontent;
			$exams_questions->itemtype=$itemtype;
			$exams_questions->where("id=%d",$id)->save();
			$delids=[];
			foreach($vcontent as $obj){
				$listenid = $obj->id;
				$sortid = $obj->sortid;
				$content = $obj->content;
				$voiceid = $obj->voiceid;
				if($listenid!='0'){
					array_push($delids,$listenid);
					$exams_questions_listen->sortid=$sortid;
					$exams_questions_listen->vcontent=$content;
					$exams_questions_listen->vvoiceid=$voiceid;
					$exams_questions_listen->where("id=%d",$listenid)->save();
				}else{
					$exams_questions_listen->questionsid=$lastid;
					$exams_questions_listen->sortid=$sortid;
					$exams_questions_listen->vcontent=$content;
					$exams_questions_listen->vvoiceid=$voiceid;
					$lischildid=$exams_questions_listen->add();
					array_push($delids,$lischildid);
					$exams_questions_listen->where("id=%d",$lischildid)->setField("questionsid",$id);
				}
			}
			if(count($delids)>0){
				$map['id']  = array('not in',$delids);
				$map['questionsid']  = $id;
				$exams_questions_listen->where($map)->delete();
			}
			//对于非判断题需要进行选项的添加engs_base_questions_items
			if($questype=='1'){
				$itemsids=explode("*",$itemsids);
				$itemsflag=explode("*",$itemsflag);
				$itemcontent=explode("*",$itemcontent);
				foreach($itemsflag as $key=>$value){
					$exams_questions_items->flag=$value;
					$exams_questions_items->content=$itemcontent[$key];
					$exams_questions_items->typeid=$questype;
					$exams_questions_items->where("id=%d",$itemsids[$key])->save();
				}
			}
			//base_questions_answer中添加答案
			if($questype!=2){
				$answer=explode("*",$answer);
				$answerids=explode("*",$answerids);
				foreach($answer as $key=>$value){
					$exams_questions_answer->answer=$value;
					$exams_questions_answer->sortid=($key+1);
					$exams_questions_answer->where("id=%d",$answerids[$key])->save();
				}
			}else{
				$exams_questions_answer->where("questionsid=%d",$id)->delete();
				$answer=explode("*",$answer);
				foreach($answer as $key=>$value){
					$exams_questions_answer->answer=$value;
					$exams_questions_answer->sortid=($key+1);
					$exams_questions_answer->questionsid=$id;
					$exams_questions_answer->add();
				}
			}
			
		}
		
	   }
	   
	   /**
	    * combine_questions_list
	    * 独立小题管理
	    * @param 	void
	    * @return  null
	    */
	   public function combine_questions_list(){
	   	$questype=I("questype/d",1);
	   	$this->assign("questype",$questype);
	   	$complexity=I("complexity/d",1);
	   	$this->assign("complexity",$complexity);
	   	$currentpage=I("currentpage/d",1);
	   	$this->assign("currentpage",$currentpage);
	   	$source=I("source/d",0);
	   	$this->assign("source",$source);
	   	$this->assign("examflag",'0');
	   	$grade=M("grade");
	   	$rs=$grade->field("id,name")->order("sortid")->select();
	   	$this->assign("grade",$rs);
	   	$this->display();
	   }
	   
	   
	   /**
	    * add_combine_questions
	    * 添加短文试题
	    * @param 	void
	    * @return  null
	    */
	   public function add_combine_questions()
	   {
		   	$questype=I("questype/d");
			$tcontent=html_entity_decode(I("tcontent/s"));
			$vcontent =  stripslashes(I("vcontent"));
			$vcontent = rtrim($vcontent ,'"');
			$vcontent = ltrim($vcontent ,'"');
			$vcontent = str_replace('&quot;', '"', $vcontent);
			$vcontent=json_decode($vcontent);
			$acontent=html_entity_decode(I("acontent/s"));
			$gradeids=I("gradeid/s");
			$source=I("source/s");
			$keyname=I("keyword/s");			
			$complexity=I("complexity/d");
			//添加短文答题内容
			$base_questions=M("base_questions");
			$base_questions->classid=2;
			$base_questions->examdaid=$questype;
			$base_questions->complexity=$complexity;
			$base_questions->source=$source;
			$base_questions->tcontent=$tcontent;
			$base_questions->acontent=$acontent;
			$base_questions->vcontent=$vcontent;
			$base_questions->addtime=Date("y-m-d H:m:s");
			$base_questions->isdel=1;
			$parid=$base_questions->add();
			//添加听力材料	
			foreach($vcontent as $obj){
				$base_questions_listen=M("base_questions_listen");
				$listenid = $obj->id;
				$sortid = $obj->sortid;
				$content = $obj->content;
				$voiceid = $obj->voiceid;
				if($listenid!='0'){
						
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->where("id=%d",$listenid)->save();
				}else{
					$base_questions_listen->questionsid=$parid;
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->add();
				}
			}
			//短文添加关键字
		   $base_questions_keys=M("base_questions_keys");
			if(!empty($keyname))
			{
				$keyname=explode(",",$keyname);
				foreach($keyname as $key=>$value){
					$base_questions_keys->questionsid=$parid;
					$base_questions_keys->keyname=$value;
					$base_questions_keys->add();
				}
			}
			//短文试题添加年级
			$base_questions_grade=M("base_questions_grade");
			$grade=explode(",",$gradeids);
			foreach($grade as $key=>$value){
				if(!empty($value)){
					$base_questions_grade->questionsid=$parid;
					$base_questions_grade->gradeid=$value;
					$base_questions_grade->add();
				}else{
					break;
				}
			}
			//以下是添加子小题
			$child_content=html_entity_decode(I("childcontent/s"));
			$child_item_flag=I("childitemflag/s");
			$child_item_content=html_entity_decode(I("childitemcontent/s"));
			$child_item_type=I("childitemtype/s");
			$child_answer=I("childanswer/s");
			$child_questype=I("childtype/s");
			//添加子题题干
			$child_content=explode(",",$child_content);
			$child_item_type=explode(",",$child_item_type);
			$child_type=explode(",",$child_questype);
			$child_item_content=explode(",",$child_item_content);
			$child_item_flag=explode(",",$child_item_flag);
			$base_questions_items=M("base_questions_items");
			//$base_questions_items=M("base_questions_items");
			$base_questions_answer=M("base_questions_answer");
			$child_answer=explode(",",$child_answer);
			foreach($child_content as $k=>$value)
			{
				$base_questions->classid=3;
				$base_questions->examdaid=$questype;
				$base_questions->complexity=$complexity;
				$base_questions->source=$source;
				$base_questions->tcontent=$value;
				$base_questions->parid=$parid;
				$base_questions->itemtype=$child_type[$k];
				$base_questions->addtime=Date("y-m-d H:m:s");
				$base_questions->isdel=1;
				$id=$base_questions->add();
				//添加子题答案
				$child_answer_array=explode("@",$child_answer[$k]);
				foreach($child_answer_array as $key=>$value)
				{
					$base_questions_answer->questionsid=$id;
					$base_questions_answer->answer=$value;
					$base_questions_answer->sortid=($key+1);
					$base_questions_answer->add();
				}
				//添加子题选项以及子项的标记
				if($questype!=3){
					$child_item_content_array=explode("@",$child_item_content[$k]);
					$child_item_flag_array=explode("@",$child_item_flag[$k]);
					$base_questions_items=M("base_questions_items");
					foreach($child_item_content_array as $keys=>$values){
						$base_questions_items->questionsid=$id;
						$base_questions_items->flag=$child_item_flag_array[$keys];
						$base_questions_items->content=$values;
						$base_questions_items->sortid=($keys+1);
						$base_questions_items->add();
					}	
				}	
				}
				if(!empty($paperid)){$this->baseToExam($lastid,$paperid);}
		}
		
		/**
		 *  exams_questions_select
		 *  试题选择页面
		 * @param  void
		 * @return  void
		 */
		public function exam_questions_select(){
			$flag=I("flag/d");
			$this->assign("flag",$flag);
			$classid=I("classid/d");
			$examsid=I("examsid/d",0);
			$amount=I("amount/d");
			$examflag='1';
			$complexity=I("complexity/d",0);
			$current_page=I("currpage/d",1);
			$this->assign("currentpage",$current_page);
			$this->assign("amount",$amount);
			$this->assign("complexity",$complexity);
			$this->assign("classid",$classid);
			$this->assign("examsid",$examsid);
			$this->assign("examflag",$examflag);
			$paperid=I("paperid/d",0);
			$questype=I("questype/d",0);
			$grade=M("grade");
			$rs=$grade->field("id,name")->order("sortid")->select();
			$this->assign("paperid",$paperid);
			$this->assign("questype",$questype);
		    $this->assign("grade",$rs);
			$this->display();
		}
		
		/**
		 *  fromBaseToExam
		 *  将base表中的试题加入Exam表中
		 * @param  void
		 * @return  void
		 */
		public function fromBaseToExam(){
			$questionsid=I("questionsid/d",0);
			$paperid=I("paperid/d",0);
			//将选择的问题直接复制到exams中
			$this->baseToExam($questionsid,$paperid);
			$this->ajaxReturn(1);
		}
		
		
		/**
		 *  BaseToExam
		 *  将base表中的试题加入Exam表中
		 * @param  void
		 * @return  void
		 */
		protected function baseToExam($questionsid,$paperid){
// 			$questionsid=I("questionsid/d",0);
// 			$paperid=I("paperid/d",0);
			//将选择的问题直接复制到exams中
			$exams_questions=M("exams_questions");
			$exams_questions_listen=M("exams_questions_listen");
			$base_questions_listen=M("base_questions_listen");
			$base_questions=M("base_questions");
			$base_questions_items=M("base_questions_items");
			$exam_questions_itmes=M("exams_questions_items");
			$base_questions_answer=M("base_questions_answer");
			$exams_questions_answer=M("exams_questions_answer");
			$exams_paper=M("exams_paper");
			$sqlw="update engs_exams_paper set amount=amount+1 where id=%d";
			$exams_paper->execute($sqlw,$paperid);
			$rs=$base_questions -> where("id=%d and isdel=1",$questionsid)->select();
			$count=$exams_questions->where("paperid=%d and isdel=1 and classid=%d",$paperid,$rs[0]["classid"])->count();
			$data["baseid"]=$rs[0]['id'];
			$data["examdaid"]=$rs[0]["examdaid"];
			$data["classid"]=$rs[0]["classid"];
			$data["parid"]=$rs[0]["parid"];
			$data["complexity"]=$rs[0]["complexity"];
			$data["source"]=$rs[0]["source"];
			$data["tcontent"]=$rs[0]["tcontent"];
			$data["acontent"]=$rs[0]["acontent"];
			$data["itemtype"]=$rs[0]["itemtype"];
			$data["addtime"]=$rs[0]["addtime"];
			$data["isdel"]=$rs[0]["isdel"];
			$data["sortid"]=$count+1;
			$data["parid"]=0;
			$id=$exams_questions->add($data);
			$exams_questions->where("id=%d",$id)->setField("paperid",$paperid);
			//将问题的听力复制到exams
			unset($rs);
			$rs=$base_questions_listen->where("questionsid=%d",$questionsid)->select();
			foreach($rs as $key=>$value){
				$listen["vvoiceid"]=$value["vvoiceid"];
				$listen["vcontent"]=$value["vcontent"];
				$listen["sortid"]=$value["sortid"];
				$listenid=$exams_questions_listen->add($listen);
				$exams_questions_listen->where("id=%d",$listenid)->setField("questionsid",$id);
			}
			unset($rs);
			if($data["classid"]=='2'){
				$rs=$base_questions -> where("parid=%d and isdel=1",$questionsid)->select();
				unset($count);
				unset($data);
				foreach($rs as $k=>$value){
					$data["baseid"]=$rs[$k]['id'];
					$data["examdaid"]=$rs[$k]["examdaid"];
					$data["classid"]=$rs[$k]["classid"];
					$data["parid"]=$id;
					$data["complexity"]=$rs[$k]["complexity"];
					$data["source"]=$rs[$k]["source"];
					$data["tcontent"]=$rs[$k]["tcontent"];
					$data["vcontent"]=$rs[$k]["vcontent"];
					$data["acontent"]=$rs[$k]["acontent"];
					$data["itemtype"]=$rs[$k]["itemtype"];
					$data["addtime"]=$rs[$k]["addtime"];
					$data["isdel"]=$rs[$k]["isdel"];
					$data["sortid"]=$k+1;
					$examid=$exams_questions->add($data);
					$exams_questions->where("id=%d",$examid)->setField("paperid",$paperid);
					//将base的选项和答案也复制到exams中
					$rs1=$base_questions_items->where("questionsid=%d",$rs[$k]['id'])->field("flag,content,typeid")->select();
					$data=[];
					foreach($rs1 as $key=>$value){
						$rs1[$key]["questionsid"]=$examid;
						$exam_questions_itmes->add($rs1[$key]);
					}
					$rs2=$base_questions_answer->where("questionsid=%d",$rs[$k]['id'])->field("answer,sortid")->select();
					foreach($rs2 as $key=>$value){
						$rs2[$key]["questionsid"]=$examid;
						$exams_questions_answer->add($rs2[$key]);
					}
				}
				
			}
			else{
				$exams_questions->where("id=%d",$id)->setField("paperid",$paperid);
				//将base的选项和答案也复制到exams中
				
				$rs=$base_questions_items->where("questionsid=%d",$questionsid)->field("flag,content,typeid")->select();
				$data=[];
				foreach($rs as $key1=>$value){
					$data["questionsid"]=$id;
					$data["flag"]=$rs[$key1]["flag"];
					$data["content"]=$rs[$key1]["content"];
					$data["sortid"]=$key1+1;
					$data["typeid"]=1;
					$exam_questions_itmes->add($data);
				}
				$base_questions_answer=M("base_questions_answer");
				$exams_questions_answer=M("exams_questions_answer");
				$rs=$base_questions_answer->where("questionsid=%d",$questionsid)->field("answer,sortid")->select();
				$dataans=[];
				foreach($rs as $key=>$value){
					$dataans["questionsid"]=$id;
					$dataans["answer"]=$rs[$key]["answer"];
					$dataans["sortid"]=$rs[$key]["sortid"];
					$exams_questions_answer->add($dataans);
				}
				
			}
			
		}
		
		
		
		/**
		 *  removeBaseFromExam
		 *  将Exam表中的试题删除删除从base中选择的试题
		 * @param  void
		 * @return  void
		 */
		public function removeBaseFromExam(){
			$questionsid=I("questionsid/d",0);
			$paperid=I("paperid/d",0);
			$classid=I("classid/d",0);
			$exams_paper=M("exams_paper");
			$sqlw="update engs_exams_paper set amount=amount-1 where id=%d";
			$exams_paper->execute($sqlw,$paperid);
			//将选择的问题直接复制到exams中
			$exams_questions=M("exams_questions");
			$rs=$exams_questions->where("baseid=%d and classid=%d and paperid=%d and isdel=1",$questionsid,$classid,$paperid)->field("id")->find();
			$id=$rs["id"];
			$exams_questions_answer=M("exams_questions_answer");
			$exams_questions_items=M("exams_questions_items");
			$exams_questions_answer->where("questionsid=%d",$id)->delete();

			$exams_questions_items->where("questionsid=%d",$id)->delete();
			$exams_questions->where("baseid=%d and classid=%d and paperid=%d and isdel=1",$questionsid,$classid,$paperid)->setField("isdel",'0');
			
			if(classid==2){
				unset($rs);
				$rs=$exams_questions->where("parid=%d and paperid=%d",$id,$paperid)->select();
				foreach($rs as $key=>$value){
					$exams_questions_items->where("questionsid=%d",$rs[$key]["id"])->delete();
					$exams_questions_answer->where("questionsid=%d",$rs[$key]["id"])->delete();
				}
				$exams_questions->where("parid=%d and paperid=%d",$id,$paperid)->setField("isdel",'0');
				
			}
			$this->ajaxReturn(1);
		}
		
		
		
		
		/**
		 *  getUnitExamCombineQuestionList
		 *  获取组合体型列表
		 * @param  void
		 * @return  void
		 */
		public function getUnitExamCombineList(){
			$paperid=I("paperid/d");
			$classid=I("classid/d");
			$exams_questions=M("exams_questions");
			$rs=$exams_questions->where("paperid=%d and classid=%d and isdel=1",$paperid,$classid)->select();
			foreach($rs as $key=>$value){
				$rs[$key]["question_items"]=$this->getUnitExamCombineQuestionList($value["id"]);
			}
			$this -> ajaxReturn($rs);
		}
		
		

		/**
		 *  getUnitExamCombineQuestionList
		 *  获取组合体型列表
		 * @param  id 表示exams_questions中的id
		 * @return  void
		 */
		protected function getUnitExamCombineQuestionList($id){
			$exams_questions=M("exams_questions");
			$rs=$exams_questions->where("parid=%d and classid=3 and isdel=1",$id)->select();
			foreach($rs as $key=>$value){
				$rs[$key]["items"]=$this->getExamQuestionItems($value["id"]);
				$rs[$key]["answer"]=$this->getExamQuestionAnswer($value["id"]);
			}
			return $rs;
		}
		
		/**
		 *  editCombineContentQuestions
		 *  获取组合体型列表
		 * @param  
		 * @return  void
		 */
		public function editCombineContentQuestions(){
			$id=I("id/d");
			$flag=I("flag/d",0);
			$tcontent=html_entity_decode(I("tcontent/s"));
			$vcontent =  stripslashes(I("vcontent"));
			$vcontent = rtrim($vcontent ,'"');
			$vcontent = ltrim($vcontent ,'"');
			$vcontent = str_replace('&quot;', '"', $vcontent);
			$vcontent=json_decode($vcontent);
			$acontent=I("acontent/s");
			if($flag==1){
				$base_questions=M("exams_questions");
				$base_questions_listen=M("exams_questions_listen");
			}else{
				$base_questions=M("base_questions");
				$base_questions_listen=M("base_questions_listen");
			}
			foreach($vcontent as $obj){
				$listenid = $obj->id;
				$sortid = $obj->sortid;
				$content = $obj->content;
				$voiceid = $obj->voiceid;
				if($listenid!='0'){
						
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->where("id=%d",$listenid)->save();
				}else{
					$base_questions_listen->questionsid=$id;
					$base_questions_listen->sortid=$sortid;
					$base_questions_listen->vcontent=$content;
					$base_questions_listen->vvoiceid=$voiceid;
					$base_questions_listen->add();
				}
			}
			$base_questions->where("id=%d",$id)->setField("tcontent",$tcontent);
			$base_questions->where("id=%d",$id)->setField("vcontent",$vcontent);
			$base_questions->where("id=%d",$id)->setField("acontent",$acontent);
		}
		
		/**
		 *  child_questions_list
		 *  查看短文子题页面
		 * @param
		 * @return  void
		 */
		public function child_questions_list(){
			$paperid=I("paperid/d",0);
			$this->assign("paperid",$paperid);
			$this->assign("source",$source);
			$qid=I("qid/d");
			$questype=I("questype/d");
			$flag=I("flag/d",0);
			$this->assign("flag",$flag);
			$this->assign("questype",$questype);
			$complexity=I("complexity/d");
			$this->assign("complexity",$complexity);
			$this->assign("qid",$qid);
			$current_page=I("currpage/d",1);
			$this->assign("currentpage",$current_page);
			if($flag==1){
                $paperid=I("paperid/d");
				$classid=I("classid/d");
				$unitid=I("unitid/d");
				$termid=I("termid/d");
				$gradeid=I("gradeid/d");
				$versionid=I("versionid/d");
				$examsid=I("id/d",0);
				$amount=I("amount/d");
				$this->assign("amount",$amount);
				$this->assign("paperid",$paperid);
				$this->assign("classid",$classid);
				$this->assign("unitid",$unitid);
				$this->assign("gradeid",$gradeid);
				$this->assign("versionid",$versionid);
				$this->assign("termid",$termid);
				$this->assign("examsid",$examsid);
			}
			$this->display();
		}
		
		/**
		 *  getChildQUestions
		 *  小题的问题和答案
		 * @param
		 * @return  void
		 */
		
		public function getChildQuestionsList(){
			$id=I("id/d");
			$flag=I("flag/d");
			$rs="";
			if($flag==2||$flag==3){
				$base_questions=M("base_questions");
				$rs=$base_questions->where("parid=%d and isdel=1",$id)->order("id")->select();
				foreach($rs as $key=>$value){
					$rs[$key]["items"]=M("base_questions_items")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
					$rs[$key]["answer"]=M("base_questions_answer")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
				}
			}else{
				$exams_questions=M("exams_questions");
				$rs=$exams_questions->where("parid=%d and isdel=1",$id)->order("id")->select();
				foreach($rs as $key=>$value){
					$rs[$key]["items"]=M("exams_questions_items")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
					$rs[$key]["answer"]=M("exams_questions_answer")->where("questionsid=%d",$value["id"])->order("sortid,id")->select();
				}
			}
			$this->ajaxReturn($rs);
		}
		
		/**
		 *  getChildQuestion获取小题内容
		 *  小题的问题和答案
		 * @param
		 * @return  void
		 */
		
		public function getChildQuestion(){
			$id=I("id/d");
			$flag=I("flag/d");
			if($flag==2){
				$base_questions=M("base_questions");
				$rs=$base_questions->where("id=%d and isdel=1",$id)->find();
				$rs["items"]=M("base_questions_items")->where("questionsid=%d",$id)->order("sortid,id")->select();
				$rs["answer"]=M("base_questions_answer")->where("questionsid=%d",$id)->order("sortid,id")->select();
				$this->ajaxReturn($rs);
			}else{
				$base_questions=M("exams_questions");
				$rs=$base_questions->where("id=%d and isdel=1",$id)->find();
				$rs["items"]=M("exams_questions_items")->where("questionsid=%d",$id)->order("sortid,id")->select();
				$rs["answer"]=M("exams_questions_answer")->where("questionsid=%d",$id)->order("sortid,id")->select();
				$this->ajaxReturn($rs);
			}
			
		}
		
		
		/**
		 *  editChildQuestions
		 *  修改小题的问题和答案
		 * @param
		 * @return  void
		 */
		
		public function editChildQuestions(){
			$id=I("id/d",0);
			$parid=I("parid/d",0);
			$paperid=I("paperid/d",0);
			$questype=I("questype/d",1);
			$flag=I("flag/d");
			if($flag==2){
				$base_questions=M("base_questions");
				$base_questions_answer=M("base_questions_answer");
				$base_questions_items=M("base_questions_items");
			}else{
				$base_questions=M("exams_questions");
				$base_questions_answer=M("exams_questions_answer");
				$base_questions_items=M("exams_questions_items");
			}
			$quescount=$base_questions->where("parid=%d",$parid)->count();
			if($questype==1){
				$tcontent=I("tcontent/s");
				$answer=I("answer/s");
				$answerid=I("answerid/d");
				$items=I("items/s");
				$itemsids=I("itemsids/s");
				$itemtype=I("itemtype/d");
				if($id>0){
					$base_questions->itemtype=$itemtype;
					$base_questions->tcontent=$tcontent;
					$base_questions->where("id=%d",$id)->save();
					$base_questions_answer->answer=$answer;
					$base_questions_answer->where("id=%d",$answerid)->save();
					$items=explode("@",$items);
					$itemsids=explode("@",$itemsids);
					foreach($items as $key=>$value){
						$base_questions_items->content=$value;
						$base_questions_items->sortid=($key+1);
						$base_questions_items->where("id=%d",$itemsids[$key])->save();
					}
				}else{
					$base_questions->itemtype=$itemtype;
					$base_questions->tcontent=$tcontent;
					$base_questions->parid=$parid;
					$base_questions->paperid=$paperid;
					$base_questions->classid=3;
					$base_questions->examdaid=$questype;
					$base_questions->sortid=$quescount;
					$quesid=$base_questions->add();
					$base_questions_answer->questionsid=$quesid;
					$base_questions_answer->answer=$answer;
					$base_questions_answer->add();
					$items=explode("@",$items);
					$itemsids=explode("@",$itemsids);
					$data=['A','B','C'];
					foreach($items as $key=>$value){
						$base_questions_items->flag=$data[$key];
						$base_questions_items->questionsid=$quesid;
						$base_questions_items->content=$value;
						$base_questions_items->sortid=($key+1);
						$base_questions_items->add();
					}
					
				}
				
			}else if($questype==3){
				$tcontent=I("tcontent/s");
				$answer=I("answer/s");
				$answerid=I("answerid/d");
				if($id>0){
					$base_questions->tcontent=$tcontent;
					$base_questions->where("id=%d",$id)->save();
					$base_questions_answer->answer=$answer;
					$base_questions_answer->where("id=%d",$answerid)->save();
				}else{
					$base_questions->parid=$parid;
					$base_questions->paperid=$paperid;
					$base_questions->classid=3;
					$base_questions->examdaid=$questype;
					$base_questions->tcontent=$tcontent;
					$base_questions->sortid=$quescount;
					$quesid=$base_questions->add();
					$base_questions_answer->questionsid=$quesid;
					$base_questions_answer->answer=$answer;
					$base_questions_answer->add();
				}
				
			}else if($questype==4){
				$items=I("items/s");
				$itemsflag=I("itemsflag/s");
				$answer=I("answer/s");
				$itemtype=I("itemtype/d");
				if($id>0){
					$base_questions->itemtype=$itemtype;
					$base_questions->where("id=%d",$id)->save();
				}else{
					$base_questions->itemtype=$itemtype;
					$base_questions->parid=$parid;
					$base_questions->paperid=$paperid;
					$base_questions->classid=3;
					$base_questions->examdaid=$questype;
					$base_questions->sortid=$quescount;
					$id=$base_questions->add();
				}
				$items=explode("@",$items);
				$itemsflag=explode("@",$itemsflag);
				$answer=explode("@",$answer);
				$base_questions_answer->where("questionsid=%d",$id)->delete();
				$base_questions_items->where("questionsid=%d",$id)->delete();
				foreach($items as $key=>$value){
					$base_questions_items->questionsid=$id;
					$base_questions_items->content=$value;
					$base_questions_items->sortid=($key+1);
					$base_questions_items->add();
					$base_questions_answer->questionsid=$id;
					$base_questions_answer->answer=$answer[$key];
					$base_questions_answer->sortid=($key+1);
					$base_questions_answer->add();
				}
			
			}	
		}
		/**
		 *  preview_exam_paper获取小题内容
		 *  小题的问题和答案
		 * @param
		 * @return  void
		 */
		public function preview_exam_paper(){
			$examsid=I("examsid/d",0);
			$this->assign("examsid",$examsid);
			$this->display();
		}
		
		
		/**
		 *  unitexam页面
		 *  小题的问题和答案
		 * @param
		 * @return  void
		 */
		public function unitexam(){
			$gradeid=I("gradeid",C('CONST_GRADEID'));
			$termid=I("termid",C('CONST_TERMID'));
			$versionid=I("versionid",C('CONST_VERSION'));
			$unitid=I("unitid",0);
			$stateid=I("stateid",1);
			$this->assign("gradeid",$gradeid);
			$this->assign("termid",$termid);
			$this->assign("versionid",$versionid);
			$this->assign("unitid",$unitid);
			$this->assign("state",$stateid);
			$this->display();
		}
		
		/**
		 *  changevoice页面
		 *  小题的问题和答案
		 * @param
		 * @return  void
		 */
		public function changevoice(){
			$flag=I("flag/d");
			$id=I("id/d");
			$voiceid=I("voiceid/d");
			$exam_paper=M("exams_paper");
			if($flag=='1'){
				$exam_paper->tvoiceid=$voiceid;
				$exam_paper->where("id=%d",$id)->save();
			}else{
				$exam_paper->vvoiceid=$voiceid;
				$exam_paper->where("id=%d",$id)->save();
			}
		}
		
		/**
		 *  examsCopy试卷复制
		 *  试卷的复制
		 * @param
		 * @return  void
		 */
		 public function examsCopy(){
		 	$id=I("id/d");
		 	$exams=M("exams");
		 	$exams_stem=M("exams_stem");
		 	$exams_questions=M("exams_questions");
		 	$exams_tts=M("exams_tts");
		 	$exams_questions_items=M("exams_questions_items");
		 	$exams_questions_answer=M("exams_questions_answer");
		 	$exams_questions_keys=M("exams_questions_keys");
            //复制考试
            $rs=$exams->where("id=%d and isdel=1",$id)->find();
            $data["ks_code"]=$rs["ks_code"];
            $data["name"]=$rs["name"]."副本";
            $data["tts_type"]=$rs["tts_type"];
            $data["exams_classid"]=$rs["exams_classid"];
            $data["exams_times"]=$rs["exams_times"];
            $data["header_content"]=$rs["header_content"];
            $data["stopsecond"]=$rs["stopsecond"];
            $data["state"]=$rs["state"];
            $data["addtime"]=Date("y-m-d H:i:s");
            $data["sortid"]=$rs["sortid"];
            $data["isdel"]=$rs["isdel"];
            $data["levelid"]=$rs["levelid"];
            $data["typeid"]=$rs["typeid"];
            $data["provinceid"]=$rs["provinceid"];
            $data["yearid"]=$rs["yearid"];
            $data["exams_word"]=$rs["exams_word"];
            $data["ftpmp3"]=$rs["ftpmp3"];
            $examsid=$exams->add($data);
            //复制试卷头和试卷尾部的音频
            $examstts=$exams_tts->where("examsid=%d and tts_type in ('eb','ee') and parentid=%d",$id,$id)->select();
            unset($data);
            foreach($examstts as $ekey=>$evalue){
                $data["examsid"]=$examsid;
	            $data["tts_type"]=$evalue["tts_type"];
	            $data["parentid"]=$examsid;
	            $data["flag_content"]=$evalue["flag_content"];
	            $data["tts_content"]=$evalue["tts_content"];
	            $data["tts_stoptime"]=$rs["tts_stoptime"];
	            $data["voiceid"]=$rs["voiceid"];
	            $data["tts_mp3"]=$rs["tts_mp3"];
	            $data["ftp_mp3"]=$rs["ftp_mp3"];
	            $data["sortid"]=$rs["sortid"];
	            $data["st_flag"]=$rs["st_flag"];
	            $data["isdel"]=$rs["isdel"];
	            $exams_tts->add($data);
            }
            //复制试卷的题干
            unset($data);
            unset($rs);
            $rs=$exams_stem->where("examsid=%d and parentid=0",$id)->select();
            if(!empty($rs)){
            	foreach($rs as $key1=>$value1){
	            	$exams_stem->examsid=$examsid;
	            	$exams_stem->stem_num=$value1["stem_num"];
	            	$exams_stem->stem_type=$value1["stem_type"];
	            	$exams_stem->question_playtimes=$value1["question_playtimes"];
	            	$exams_stem->question_score=$value1["question_score"];
	            	$exams_stem->content=$value1["content"];
	            	$exams_stem->stem_playtimes=$value1["stem_playtimes"];
	            	$exams_stem->stoptimes=$value1["stoptimes"];
	            	$exams_stem->sortid=$value1["sortid"];
	            	$exams_stem->parentid=$value1["parentid"];
	            	$exams_stem->tips=$value1["tips"];
	            	$exams_stem->isdel=$value1["isdel"];
	            	$examspaperid=$exams_stem->add();
	            	//复制题干的音频
	            	unset($data);
	            	$stemttsrs=$exams_tts->where("examsid=%d and tts_type='st' and parentid=%d",$id,$value1["id"])->select();
	            	foreach($stemttsrs as $ekey=>$evalue){
		                $datast["examsid"]=$examsid;
			            $datast["tts_type"]=$evalue["tts_type"];
			            $datast["parentid"]=$examspaperid;
			            $datast["flag_content"]=$evalue["flag_content"];
			            $datast["tts_content"]=$evalue["tts_content"];
			            $datast["tts_stoptime"]=$evalue["tts_stoptime"];
			            $datast["voiceid"]=$evalue["voiceid"];
			            $datast["tts_mp3"]=$evalue["tts_mp3"];
			            $datast["ftp_mp3"]=$evalue["ftp_mp3"];
			            $datast["sortid"]=$evalue["sortid"];
			            $datast["st_flag"]=$evalue["st_flag"];
			            $datast["isdel"]=$evalue["isdel"];
			            $exams_tts->add($datast);
		            }
		            $parentstem=$exams_stem->where("examsid=%d and parentid=%d",$id,$value1["id"])->select();
		            //独立答题情况
		            if(empty($parentstem)){
		            	$dataquestions=$exams_questions->where("stemid=%d",$value1["id"])->select();
		            	if(!empty($dataquestions)){
		            		foreach($dataquestions as $key2=>$value2){
		            			$exams_questions->stemid=$examspaperid;
			            		$exams_questions->question_num=$value2["question_num"];
			            		$exams_questions->typeid=$value2["typeid"];
			            		$exams_questions->scourse=$value2["scourse"];
			            		$exams_questions->tcontent=$value2["tcontent"];
			            		$exams_questions->acontent=$value2["acontent"];
			            		$exams_questions->itemtype=$value2["itemtype"];
			            		$exams_questions->isdel=$value2["isdel"];
			            		$exams_questions->sortid=$value2["sortid"];
			            		$exams_questions->display=$value2["display"];
			            		$exams_questions->addtime=Date("y-m-d H:i:s");
			            		$exams_questions->tips=$value2["tips"];
			            		$exams_questions->stoptimes=$value2["stoptimes"];
			            		$questionsid=$exams_questions->add();
			            		//复制问题的音频
			            		$questtsrs=$exams_tts->where("examsid=%d and (tts_type='qe' or tts_type='qn') and parentid=%d",$id,$value2["id"])->select();
				            	foreach($questtsrs as $ekey=>$evalue){
					                $dataqe["examsid"]=$examsid;
						            $dataqe["tts_type"]=$evalue["tts_type"];
						            $dataqe["parentid"]=$questionsid;
						            $dataqe["flag_content"]=$evalue["flag_content"];
						            $dataqe["tts_content"]=$evalue["tts_content"];
						            $dataqe["tts_stoptime"]=$evalue["tts_stoptime"];
						            $dataqe["voiceid"]=$evalue["voiceid"];
						            $dataqe["tts_mp3"]=$evalue["tts_mp3"];
						            $dataqe["ftp_mp3"]=$evalue["ftp_mp3"];
						            $dataqe["sortid"]=$evalue["sortid"];
						            $dataqe["st_flag"]=$evalue["st_flag"];
						            $dataqe["isdel"]=$evalue["isdel"];
						            $exams_tts->add($dataqe);
					            }
			            		//复制问题的选项
			            		$dataitems=$exams_questions_items->where("questionsid=%d",$value2["id"])->select();
			            		if(!empty($dataitems)){
			            			foreach($dataitems as $key4=>$value4){
				            			$exams_questions_items->questionsid=$questionsid;
				            			$exams_questions_items->flag=$value4["flag"];
				            			$exams_questions_items->content=$value4["content"];
				            			$exams_questions_items->sortid=$value4["sortid"];
				            			$exams_questions_items->typeid=$value4["typeid"];
				            			$exams_questions_items->isdel=$value4["isdel"];
				            			$exams_questions_items->add();
			            		    }
			            		}
			            		//复制问题的答案
			            		$dataanswer=$exams_questions_answer->where("questionsid=%d",$value2["id"])->select();
			            		if(!empty($dataanswer)){
			            			foreach($dataanswer as $key3=>$value3){
				            			$exams_questions_answer->questionsid=$questionsid;
				            			$exams_questions_answer->answer=$value3["answer"];
				            			$exams_questions_answer->sortid=$value3["sortid"];
				            			$exams_questions_answer->answer_num=$value3["answer_num"];
				            			$exams_questions_answer->isdel=$value3["isdel"];
				            			$exams_questions_answer->add();
			            		    }
			            		}
			            		//复制问题的关键词
	                            $datakeys=$exams_questions_keys->where("questionsid=%d",$value2["id"])->select();
			            		if(!empty($datakeys)){
			            			foreach($datakeys as $key3=>$value3){
				            			$exams_questions_keys->questionsid=$questionsid;
				            			$exams_questions_keys->dictionary_code=$value3["dictionary_code"];
				            			$exams_questions_keys->dictionary_id=$value3["dictionary_id"];
				            			$exams_questions_keys->isdel=$value3["isdel"];
				            			$exams_questions_keys->add();
			            		    }
			            		}		
		            		}
		            	}
		            }else{
		            	foreach($parentstem as $pk=>$pv){
		            		$exams_stem->examsid=$examsid;
			            	$exams_stem->stem_num=$pv["stem_num"];
			            	$exams_stem->stem_type=$pv["stem_type"];
			            	$exams_stem->question_playtimes=$pv["question_playtimes"];
			            	$exams_stem->question_score=$pv["question_score"];
			            	$exams_stem->content=$pv["content"];
			            	$exams_stem->stem_playtimes=$pv["stem_playtimes"];
			            	$exams_stem->stoptimes=$pv["stoptimes"];
			            	$exams_stem->sortid=$pv["sortid"];
			            	$exams_stem->parentid=$examspaperid;
			            	$exams_stem->tips=$pv["tips"];
			            	$exams_stem->isdel=$pv["isdel"];
			            	$examsparid=$exams_stem->add();
			            	//复制题干的音频
			            	$stemttpsrs=$exams_tts->where("examsid=%d and tts_type='st' and parentid=%d",$id,$pv["id"])->select();
			            	foreach($stemttpsrs as $ekey=>$evalue){
				                $datast["examsid"]=$examsid;
					            $datast["tts_type"]=$evalue["tts_type"];
					            $datast["parentid"]=$examsparid;
					            $datast["flag_content"]=$evalue["flag_content"];
					            $datast["tts_content"]=$evalue["tts_content"];
					            $datast["tts_stoptime"]=$evalue["tts_stoptime"];
					            $datast["voiceid"]=$evalue["voiceid"];
					            $datast["tts_mp3"]=$evalue["tts_mp3"];
					            $datast["ftp_mp3"]=$evalue["ftp_mp3"];
					            $datast["sortid"]=$evalue["sortid"];
					            $datast["st_flag"]=$evalue["st_flag"];
					            $datast["isdel"]=$evalue["isdel"];
					            $exams_tts->add($datast);
				            }
		            	    $dataquestions=$exams_questions->where("stemid=%d",$pv["id"])->select();
		            	    if(!empty($dataquestions)){
			            		foreach($dataquestions as $key2=>$value2){
			            			$exams_questions->stemid=$examsparid;
				            		$exams_questions->question_num=$value2["question_num"];
				            		$exams_questions->typeid=$value2["typeid"];
				            		$exams_questions->scourse=$value2["scourse"];
				            		$exams_questions->tcontent=$value2["tcontent"];
				            		$exams_questions->acontent=$value2["acontent"];
				            		$exams_questions->itemtype=$value2["itemtype"];
				            		$exams_questions->isdel=$value2["isdel"];
				            		$exams_questions->sortid=$value2["sortid"];
				            		$exams_questions->display=$value2["display"];
				            		$exams_questions->addtime=Date("y-m-d H:i:s");
				            		$exams_questions->tips=$value2["tips"];
				            		$exams_questions->stoptimes=$value2["stoptimes"];
				            		$questionsid=$exams_questions->add();
				            		//复制问题的音频
				            		$questtsrs=$exams_tts->where("examsid=%d and (tts_type='qe' or tts_type='qn') and parentid=%d",$id,$value2["id"])->select();
					            	foreach($questtsrs as $ekey=>$evalue){
						                $dataqe["examsid"]=$examsid;
							            $dataqe["tts_type"]=$evalue["tts_type"];
							            $dataqe["parentid"]=$questionsid;
							            $dataqe["flag_content"]=$evalue["flag_content"];
							            $dataqe["tts_content"]=$evalue["tts_content"];
							            $dataqe["tts_stoptime"]=$evalue["tts_stoptime"];
							            $dataqe["voiceid"]=$evalue["voiceid"];
							            $dataqe["tts_mp3"]=$evalue["tts_mp3"];
							            $dataqe["ftp_mp3"]=$evalue["ftp_mp3"];
							            $dataqe["sortid"]=$evalue["sortid"];
							            $dataqe["st_flag"]=$evalue["st_flag"];
							            $dataqe["isdel"]=$evalue["isdel"];
							            $exams_tts->add($dataqe);
						            }
				            		//复制问题的选项
				            		$dataitems=$exams_questions_items->where("questionsid=%d",$value2["id"])->select();
				            		if(!empty($dataitems)){
				            			foreach($dataitems as $key4=>$value4){
					            			$exams_questions_items->questionsid=$questionsid;
					            			$exams_questions_items->flag=$value4["flag"];
					            			$exams_questions_items->content=$value4["content"];
					            			$exams_questions_items->sortid=$value4["sortid"];
					            			$exams_questions_items->typeid=$value4["typeid"];
					            			$exams_questions_items->isdel=$value4["isdel"];
					            			$exams_questions_items->add();
				            		    }
				            		}
				            		//复制问题的答案
				            		$dataanswer=$exams_questions_answer->where("questionsid=%d",$value2["id"])->select();
				            		if(!empty($dataanswer)){
				            			foreach($dataanswer as $key3=>$value3){
					            			$exams_questions_answer->questionsid=$questionsid;
					            			$exams_questions_answer->answer=$value3["answer"];
					            			$exams_questions_answer->sortid=$value3["sortid"];
					            			$exams_questions_answer->answer_num=$value3["answer_num"];
					            			$exams_questions_answer->isdel=$value3["isdel"];
					            			$exams_questions_answer->add();
				            		    }
				            		}
				            		//复制问题的关键词
		                            $datakeys=$exams_questions_keys->where("questionsid=%d",$value2["id"])->select();
				            		if(!empty($datakeys)){
				            			foreach($datakeys as $key3=>$value3){
					            			$exams_questions_keys->questionsid=$questionsid;
					            			$exams_questions_keys->dictionary_code=$value3["dictionary_code"];
					            			$exams_questions_keys->dictionary_id=$value3["dictionary_id"];
					            			$exams_questions_keys->isdel=$value3["isdel"];
					            			$exams_questions_keys->add();
				            		    }
				            		}		
			            		}
			            	}
			            	
			            }
		            }
	              }	            	
	            }
            } 
		
		
		//topic页面
		
		public function topicexam(){
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


		//获取试卷单元统计信息
	    public function examsunit_stat(){
	        $gradeid=I("gradeid");
	        $termid=I("termid");
	        $versionid=I("versionid");
	        $starttime=I("starttime");
	        $endtime=I("endtime");
	        $this->assign("grade",$gradeid);
	        $this->assign("version",$versionid);
	        $this->assign("term",$termid);
	        $this->assign("starttime",$starttime);
	        $this->assign("endtime",$endtime);
	        $this->display();
	    }



	    //Excel下载
	    public function makeExcel(){
	        $starttime=I("starttime");
	        $endtime=I("endtime");
	        //$sql="SELECT s.r_grade as gradeid,s.r_volume as termid,s.r_version as versionid,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_volume` AND rms_dictionary_detail.`DICTIONARY_CODE`='volume') AS termname,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_grade` AND rms_dictionary_detail.`DICTIONARY_CODE`='grade') AS gradename,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_version` AND rms_dictionary_detail.`DICTIONARY_CODE`='edition') AS versionname,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state!=4 THEN 1 ELSE 0 END) AS checking,COUNT(*) as num FROM engs_exams t LEFT JOIN engs_rms_unit s ON t.`ks_code`=s.ks_code WHERE SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND isdel=1 AND t.ks_code IS NOT NULL AND t.`ks_code`!='0' GROUP BY s.r_grade,s.r_version,s.r_volume union all SELECT 'c' as gradeid,'1' as termid,'1' as versionid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`levelid`) AS gradeid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`typeid`) AS versionname, '中考' as termid,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state=3 THEN 1 ELSE 0 END) AS checking,COUNT(*) FROM engs_exams t  where SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND isdel=1 AND t.typeid!=0  GROUP BY t.`typeid`,t.`levelid`";	        
			$sql="SELECT s.r_grade as gradeid,s.r_volume as termid,s.r_version as versionid,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_volume` AND rms_dictionary_detail.`DICTIONARY_CODE`='volume') AS termname,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_grade` AND rms_dictionary_detail.`DICTIONARY_CODE`='grade') AS gradename,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_version` AND rms_dictionary_detail.`DICTIONARY_CODE`='edition') AS versionname,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state!=4 THEN 1 ELSE 0 END) AS checking,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=1 or t.exams_classid=3)  THEN 1 ELSE 0 END) AS tbchecked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=2) THEN 1 ELSE 0 END) AS ktchecked,COUNT(*) as num FROM engs_exams t LEFT JOIN engs_rms_unit s ON t.`ks_code`=s.ks_code WHERE SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND t.isdel=1 AND t.ks_code IS NOT NULL AND t.`ks_code`!='0' GROUP BY s.r_grade,s.r_version,s.r_volume union all SELECT 'c' as gradeid,'1' as termid,'1' as versionid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`levelid`) AS gradeid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`typeid`) AS versionname, '中考' as termid,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state!=4 THEN 1 ELSE 0 END) AS checking,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=1 or t.exams_classid=3)  THEN 1 ELSE 0 END) AS tbchecked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=2) THEN 1 ELSE 0 END) AS ktchecked,COUNT(*) FROM engs_exams t  where SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND t.isdel=1 AND t.typeid!=0  GROUP BY t.`typeid`,t.`levelid`";
			vendor('PHPExcel');
			$data=M()->query($sql);
	        $objExcel = new \PHPExcel();
	        $objWriter = new \PHPExcel_Writer_Excel2007($objExcel);
	        
	        $objExcel->setActiveSheetIndex(0);
	        $objActSheet = $objExcel->getActiveSheet();
	        $objActSheet->setTitle('试卷统计表');
	        
	        $objActSheet->setCellValue('A1','年级');
	        $objActSheet->setCellValue('B1','版本');
	        $objActSheet->setCellValue('C1','学期');
	        $objActSheet->setCellValue('D1','已发布');
	        $objActSheet->setCellValue('E1','同步发布');
	        $objActSheet->setCellValue('F1','课程发布');
	        $objActSheet->setCellValue('G1','待发布');
	        $objActSheet->setCellValue('H1','总数');

	        
	        //手动设置前4列宽度
	        $objActSheet->getColumnDimension('A')->setWidth(15);
	        $objActSheet->getColumnDimension('B')->setWidth(15);
			$objActSheet->getColumnDimension('C')->setWidth(20);
	        $objActSheet->getColumnDimension('D')->setWidth(10);
	        $objActSheet->getColumnDimension('E')->setWidth(10);
	        $objActSheet->getColumnDimension('F')->setWidth(10);
	        $objActSheet->getColumnDimension('G')->setWidth(10);
	        $objActSheet->getColumnDimension('H')->setWidth(10);
			
	        
	        foreach ($data as $key=>$v){
	            $objActSheet->setCellValueExplicit('A'.($key+2), $v['gradename'], \PHPExcel_Cell_DataType::TYPE_STRING);
	            //$objActSheet->setCellValue('A'.($key+2),$v['ks_code']);
	            $objActSheet->setCellValue('B'.($key+2),$v['versionname'],\PHPExcel_Cell_DataType::TYPE_STRING);
	            $objActSheet->setCellValue('C'.($key+2),$v['termname'],\PHPExcel_Cell_DataType::TYPE_STRING);
	            $objActSheet->setCellValue('D'.($key+2),$v['checked']);
	            $objActSheet->setCellValue('E'.($key+2),$v['tbchecked']);
	            $objActSheet->setCellValue('F'.($key+2),$v['ktchecked']);
	            $objActSheet->setCellValue('G'.($key+2),$v['checking']);
	            $objActSheet->setCellValue('H'.($key+2),$v['num']);
	        }
	        
	        $filename = "yyttlexam_ana.xlsx";
	        $objWriter->save($filename);  //保存到服务器
	        
	        ob_end_clean();//清除缓冲区,避免乱码
	        $filename=iconv('utf-8', 'gbk', $filename);
	        $filesize = filesize($filename);
	        header( "Content-Type: application/force-download;charset=utf-8");
	        header( "Content-Disposition: attachment; filename= ".$filename);
	        header( "Content-Length: ".$filesize);
	        ob_clean();
	        readfile($filename);
	        
	        unlink($filename); //下载完成后删除服务器文件
	        
	    }
		
		
		 //进行数据处理
	    private function doData($examsid){
	    	set_time_limit(0);
	 	    $quessql="SELECT t.*,s.`parentid` FROM engs_exams_questions t LEFT JOIN engs_exams_stem s ON t.`stemid`=s.`id` where t.isdel=1 and s.isdel=1 and s.examsid=".$examsid." order by t.sortid";
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
	        //处理paper_sortid
	        $examssql="select * from engs_exams t where t.isdel=1 and t.id=".$examsid;
			$examsrs=M()->query($examssql);
			foreach($examsrs as $key=>$value){
				$paper_sortid=0;
				//查询大题
				$stemsql="select * from engs_exams_stem t where t.examsid=".$value["id"]." and t.isdel=1 and parentid=0 order by sortid";
				$stemrs=M()->query($stemsql);
				foreach($stemrs as $sk=>$sv){
					//表示单题
					if($sv["stem_type"]=='1'){
						$questionsql="select * from engs_exams_questions t where t.stemid=".$sv["id"]." and t.isdel=1 order by t.sortid";
						$questionrs=M()->query($questionsql);
						foreach($questionrs as $qk=>$qv){
							M("exams_questions")->paper_sortid=$paper_sortid;
							M("exams_questions")->examsid=$sv["examsid"];
	            M("exams_questions")->questions_playtimes=$sv["question_playtimes"];
							M("exams_questions")->where("id=%d",$qv["id"])->save();
							$paper_sortid=$paper_sortid+1;
						}
					}else{
						$cstemsql="select * from engs_exams_stem t where t.examsid=".$value["id"]." and t.isdel=1 and parentid=".$sv["id"]." order by sortid";
						$cstemrs=M()->query($cstemsql);
						foreach($cstemrs as $ck=>$cv){
							$cquestionsql="select * from engs_exams_questions t where t.stemid=".$cv["id"]." and t.isdel=1 order by t.sortid";
							$cquestionrs=M()->query($cquestionsql);
							foreach($cquestionrs as $qk=>$qv){
								M("exams_questions")->paper_sortid=$paper_sortid;
								M("exams_questions")->examsid=$cv["examsid"];
	              M("exams_questions")->questions_playtimes=$cv["question_playtimes"];
								M("exams_questions")->where("id=%d",$qv["id"])->save();
								$paper_sortid=$paper_sortid+1;
							}
						}
					}
				}
				M("exams")->where("id=%d",$value["id"])->setField("quescount",$paper_sortid);
			}
	    }
		
}
