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
class CpController extends Controller {
	public function index(){
		$examsid=I("examsid/d",0);
		$rs=M("exams")->join("left join engs_unit on engs_unit.id=engs_exams.unitid")->where("engs_exams.id=%d",$examsid)->find();
		$this->assign("examsname",$rs["name"]);
		$this->assign("examstime",$rs["examtime"]);
		$this->assign("gradeid",$rs["gradeid"]);
		$rs=M("exams_paper")->where("examsid=%d and classid=0 and sortid=0",$examsid)->order("sortid,id")->find();
		$this->assign("papername",$rs["tcontent"]);
		$this->assign("examsid",$examsid);
		$rs=M("exams_paper")->where("examsid=%d and classid!=0",$examsid)->select();
		$this->assign("paper",$rs);
		$this->display();
	}

	/**
	 * base_questions_add
	 * 添加独立小题页面
	 * @param 	void
	 * @return  null
	 */
	public function base_questions_add(){
		$paperid=I("paperid/d",0);
		$questype=I("questype/d",1);
		$classid=I("classid/d",0);
		$this->assign("classid",$classid);
		$this->assign("paperid",$paperid);
		$this->assign("questype",$questype);
		$gradeid=I("gradeid/d");
		$this->assign("gradeid",$gradeid);
		$grade=M("grade");
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		//关键词搜索
		$this->display();
	}



	/**
	 * base_questions_add
	 * 添加独立小题页面
	 * @param 	void
	 * @return  null
	 */
	public function base_questions_edit(){
		$id=I("id/d",0);
		$rs="";
	    $answer="";
	    $items="";
	    $listen="";
		$rs=M("exams_questions")->where("id=%d",$id)->find();
		$answer=M("exams_questions_answer")->where("questionsid=%d",$id)->field("id,answer")->find();
		$items=M("exams_questions_items")->where("questionsid=%d",$id)->field("id,sortid,content")->order("sortid")->select();
		$listen=M("exams_questions_listen")->where("questionsid=%d",$id)->order("sortid,id")->select();
		$this->assign("tcontent",$rs["tcontent"]);
		$this->assign("acontent",$rs["acontent"]);
		$this->assign("questype",$rs["examdaid"]);
		$this->assign("itemtype",$rs["itemtype"]);
		$this->assign("answer_content",$answer["answer"]);
		$this->assign("answer_id",$answer["id"]);
		$this->assign("itmes",$items);
		$this->assign("listen",$listen);
		$this->assign("id",$id);
		$grade=M("grade");
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		$this->display();
	}
	

	//组合题型页面
	public function combine_questions_add(){
		$paperid=I("paperid/d",0);
		$gradeid=I("gradeid/d",0);
		$id=I("id/d",0);
		$grade=M("grade");
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		$this->assign("id",$id);
		$this->assign("gradeid",$gradeid);
		$classid=I("classid/d",0);
		$questype=I("questype/d",0);
		$this->assign("classid",$classid);
		$this->assign("paperid",$paperid);
		$this->assign("questype",$questype);
		$this->display();
	}


	/**
	 * base_questions_add
	 * 组合题编辑页面
	 * @param 	void
	 * @return  null
	 */
	public function combine_questions_edit(){
		$id=I("id/d",0);
		$base_questions=M("exams_questions");
		$base_questions_listen=M("exams_questions_listen");
		$listen=$base_questions_listen->where("questionsid=%d",$id)->order("sortid,id")->select();
		$rs=$base_questions->where("id=%d",$id)->find();
		$this->assign("tcontent",$rs["tcontent"]);
		$this->assign("listen",$listen);
		$this->assign("acontent",$rs["acontent"]);
		$this->assign("id",$id);
		$grade=M("grade");
		$rs=$grade->field("id,name")->order("sortid")->select();
		$this->assign("grade",$rs);
		$this->display();
	}


	/**
	 * 组合试题的子题进行添加
	 * @param 	void
	 * @return  null
	 */
	public function combine_questions_child_add(){
		$id=I("id/d",0);
		$questype=I("questype/d");
		$gradeid=I("gradeid/d");
		$this->assign("gradeid",$gradeid);
		$base_questions=M("exams_questions");
		$rs=$base_questions=M("exams_questions")->where("id=%d",$id)->find();
		$this->assign("paperid",$rs["paperid"]);
		$this->assign("questype",$rs["examdaid"]);
		$this->assign("qid",$id);
		$this->display();
	}

    /**
	 * 组合试题的子题进行编辑
	 * @param 	void
	 * @return  null
	 */
	public function combine_questions_child_edit(){
		$id=I("id/d",0);
		$base_questions=M("exams_questions");
		$rs=$base_questions=M("exams_questions")->where("id=%d",$id)->find();
		$this->assign("itemtype",$rs["itemtype"]);
		$this->assign("tcontent",$rs["tcontent"]);
		$this->assign("questype",$rs["examdaid"]);
		//选项
		$base_questions_answer=M("exams_questions_answer");
		$base_questions_items=M("exams_questions_items");
		$rs=$base_questions_answer->where("questionsid=%d",$id)->select();
		$this->assign("answer",$rs);
		$rs=$base_questions_items->where("questionsid=%d",$id)->select();
		$this->assign("items",$rs);

		$this->assign("id",$id);
		$this->display();
	}


	  /**
     * 获取试卷内的题型信息
     */
    public function listenshow() {
        $examsid = I('examsid/d', 0);
		$unitid = I('unitid/d', 0);
        $model = M();
        $sql = 'select id,name,examtime from engs_exams where id= %d';
        $exams_info = $model->query($sql, $examsid);
        $this->assign('exams_info', $exams_info);
        $sql = 'select id, classid,examdaid,amount,score,tcontent,bstoptime,astoptime,tvoiceid,vvoiceid,mp3,readtimes,sortid from engs_exams_paper where examsid =%d and classid!=0 order by id,sortid';
        $paper_info = $model->query($sql, $examsid);
		$papersort[0] = '一、';
		$papersort[1] = '二、';
		$papersort[2] = '三、';
		$papersort[3] = '四、';
		$papersort[4] = '五、';
		$papersort[5] = '六、';
		$papersort[6] = '七、';
		$papersort[7] = '八、';
		$papersort[8] = '九、';
		$pakey = 0;

        foreach ($paper_info as $key => $value) {
            if ($value['classid'] != 0) {  //该题干不是注释
				$paper_info[$key]["papersort"] = $value["sortid"];
				//$sql = 'select id, classid,examdaid,amount,score,tcontent,bstoptime,astoptime,tvoiceid,vvoiceid,mp3,readtimes,sortid from engs_exams_paper where examsid ='.$examsid.' and classid=0 and sortid='.$value["sortid"].' order by id desc limit 1';
				$paper_info[$key]["zhushi"] = M("exams_paper")->where("examsid =%d and classid=0 and  sortid=%d",$examsid,$value["sortid"]-1)->order("id desc")->find();
                $sql = 'select id,paperid,classid,examdaid,parid,complexity,source,tcontent,acontent,itemtype from engs_exams_questions where paperid = ' . $value['id'] . ' and classid !=3 and isdel = 1 order by sortid,id';
			    $question_info = $model->query($sql);
				$paper_info[$key]["quenum"] = count($question_info);
				$paper_info[$key]["quescore"] = $value['score'];
                foreach ($question_info as $keyque => $que) {
                	
					$mp3str = '';
					$mp3sql = 'select nmp3,vvoiceid,mp3 from engs_exams_questions_listen where questionsid= ' . $que['id'] . ' order by sortid,id';
					$mp3info = $model->query($mp3sql);
					foreach ($mp3info as $keymp3 => $quemp3) {
							$mp3str .= $quemp3['nmp3'].'&'.$quemp3['mp3'].'|';
							
							
					}
					
					$question_info[$keyque]["mp3"] = $mp3str;
                    if ($que['classid'] == 1) {
                        $sqlanswer = 'select answer from engs_exams_questions_answer where questionsid = ' . $que['id']." order by sortid,id";
						
                        $que_answer = $model->query($sqlanswer);
                        $question_info[$keyque]["answer"] = $que_answer;
						
                        if ($value['examdaid'] == 1 || $value['examdaid'] == 4) { //该题是选择题，则获取该选择题的选项
                            $sqlitems = 'select flag,content from engs_exams_questions_items where questionsid = ' . $que['id'] . ' order by sortid,id';
                            $que_items = $model->query($sqlitems);
                            $question_info[$keyque]["items"] = $que_items;
                        }
						$question_info[$keyque]['score'] = $value['score'];
						$value['score'] = $value['score']*count($que_answer);
						$total_score += $value['score'];
						
						
                    } else {  //短文大题
                        $sql_dw = 'select id,paperid,classid,examdaid,parid,complexity,source,tcontent,acontent,itemtype,mp3 from engs_exams_questions where parid = ' . $que['id'] . ' and isdel = 1 order by sortid,id';
						//echo $sql_dw;
                        $duanwen_info = $model->query($sql_dw);
						$question_info[$keyque]["quenum"] = count($duanwen_info);
						$question_info[$keyque]["score"] = $value['score'];
						
                        foreach ($duanwen_info as $keydw => $dw) {
                            $sqldwanswer = 'select answer from engs_exams_questions_answer where questionsid = ' . $dw['id'];
                            $dw_answer = $model->query($sqldwanswer);
                            $duanwen_info[$keydw]["answer"] = $dw_answer;
                            if ($dw['examdaid'] == 1 || $dw['examdaid'] == 4) { //该题是选择题或者是排序题，则获取该选择题的选项
                                $sqldwitems = 'select flag,content from engs_exams_questions_items where questionsid = ' . $dw['id'] . ' order by sortid,id';
                                $dw_items = $model->query($sqldwitems);
                                $duanwen_info[$keydw]["items"] = $dw_items;
                            }
							$duanwen_info[$keydw]["score"] = $value['score'];
							$value['score'] = $value['score']*count($dw_answer);
							$total_score += $value['score'];
                        }
                        $question_info[$keyque]["duanwen_info"] = $duanwen_info;
						$question_info[$keyque]["duanwen_amout"] = count($duanwen_info);
                    }
					
                }
                $paper_info[$key]["que_info"] = $question_info;
				$pakey++;
            }
		
        }
        $this->assign('paper_infos', $paper_info);
		$this->assign('total_score', $total_score);
		$this->assign('examsid', $examsid);
		$this->assign('unitid', $unitid);
        $this->display();
    }

    //添加部分
    public function addTitle(){
    	$examsid=I("examsid/d",0);
    	$examspaper=M("exams_paper");
    	$examspaper->classid=1;
    	$examspaper->examsid=$examsid;
    	$arr_return["id"]=$examspaper->add();
    	$this->ajaxReturn($arr_return);
    }

    //题型选择页面
    public function choice(){
    	$paperid=I("paperid/d",0);
    	$gradeid=I("gradeid/d",0);
    	$this->assign("paperid",$paperid);
    	$this->assign("gradeid",$gradeid);
    	$this->display();
    }

    /**
	 *  examPaperDel
	 *  删除试卷中的某一部分
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
			M("exams_questions_listen")->where("questionsid=%d",$value["id"])->delete();
		}
		M("exams_questions")->where("paperid=%d",$paperid)->setField("isdel","0");
	}



	/**
	 * delete_questions_list
	 * 题库中的福利小题的删除
	 * @param 	void
	 * @return  null
	 */
	public function questionsDel(){
		$id=I("id/d");
		$exams_questions=M("exams_questions");
		$exams_questions_answer=M("exams_questions_answer");
		$exams_questions_items=M("exams_questions_items");
		$exams_questions_listen=M("exams_questions_items");
		$exams_questions->where("id=%d",$id)->setField("isdel",'0');
		//删除答案
		$exams_questions_answer->where("questionsid=%d",$id)->delete();
		//删除选项
		$exams_questions_items->where("questionsid=%d",$id)->delete();
		//删除听力
		$exams_questions_listen->where("questionsid=%d",$id)->delete();
		//如果是组合题删除组合题中的子题
		$rs=$exams_questions->where("parid=%d and isdel=1",$id)->field("id")->select();
		if(!empty($rs)){
			foreach($rs as $key=>$value){
				$exams_questions_answer->where("questionsid=%d",$value["id"])->delete();
				$exams_questions_items->where("questionsid=%d",$value["id"])->delete();
				$exams_questions_listen->where("questionsid=%d",$value["id"])->delete();
			}
		}
		$exams_questions->where("parid=%d",$id)->setField("isdel",'0');
	}


	/**
	 * add_exam_paper
	 * 添加组合小题
	 * @param 	void
	 * @return  json
	 */
	public function add_exam_paper(){
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
			$exams_paper -> vvoiceid=$tvoiceid;
			$exams_paper -> tvoiceid=$vvoiceid;
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
	 * add_base_questions
	 * 独立小题添加
	 * @param 	void
	 * @return  null
	 */
	public function add_exams_base_questions(){
		$id=I("id/d",0);
		$paperid=I("paperid/d",0);
		$source=I("source");
		$classid=I("classid/d",0);
		$questype=I("questype/d",0);
		$tcontent=html_entity_decode(I("tcontent/s"));
		$vcontent =  stripslashes(I("vcontent"));
		$vcontent = rtrim($vcontent ,' ');
		$vcontent = ltrim($vcontent ,' ');
		$vcontent = str_replace('&quot;', '"', $vcontent);
		$vcontent=json_decode($vcontent);
		$acontent=html_entity_decode(I("acontent/s"));
		$gradeids=I("gradeid/s");
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
			$base_questions->source=$source;
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
					if(!empty($itemcontent[$key])){
						$base_questions_items->questionsid=$lastid;
						$base_questions_items->flag=$value;
						$base_questions_items->content=$itemcontent[$key];
						$base_questions_items->typeid=$questype;
						$base_questions_items->sortid=($key+1);
						$base_questions_items->add();
					}
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
			if(!empty($paperid))
				{
					$this->baseToExam($lastid,$paperid);
					$exams_paper=M("exams_paper");
					$exams_paper->classid=$classid;
					$exams_paper->examdaid=$questype;
					$exams_paper->where("id=%d",$paperid)->save();
				}
		}else{
			//base_questions中添加小题
			$exams_questions=M("exams_questions");
			$exams_questions_items=M("exams_questions_items");
			$exams_questions_answer=M("exams_questions_answer");
			$exams_questions_answer=M("exams_questions_answer");
			$exams_questions_listen=M("exams_questions_listen");
			$exams_questions->tcontent=$tcontent;
			$exams_questions->acontent=$acontent;
			$exams_questions->itemtype=$itemtype;
			$exams_questions->where("id=%d",$id)->save();
			$delids=array();
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
					$exams_questions_listen->questionsid=$id;
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
					if(!empty($itemcontent[$key])){
						$exams_questions_items->flag=$value;
						$exams_questions_items->content=$itemcontent[$key];
						$exams_questions_items->typeid=$questype;
						$exams_questions_items->where("id=%d",$itemsids[$key])->save();
					}
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
	    * add_combine_questions
	    * 添加组合大题页面
	    * @param 	void
	    * @return  null
	    */
	   public function add_exams_combine_questions()
	   {
	   	    $paperid=I("paperid/d",0);
		   	$questype=I("questype/d");
		   	$classid=I("classid/d",0);
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
			$base_questions->classid=I("classid/d");
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
				$keyname=explode("*",$keyname);
				foreach($keyname as $key=>$value){
					$base_questions_keys->questionsid=$parid;
					$base_questions_keys->keyname=$value;
					$base_questions_keys->add();
				}
			}
			//短文试题添加年级
			$base_questions_grade=M("base_questions_grade");
			$grade=explode("*",$gradeids);
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
			$child_content=explode("*",$child_content);
			$child_item_type=explode("*",$child_item_type);
			$child_type=explode("*",$child_questype);
			$child_item_content=explode("*",$child_item_content);
			$child_item_flag=explode("*",$child_item_flag);
			$base_questions_items=M("base_questions_items");
			//$base_questions_items=M("base_questions_items");
			$base_questions_answer=M("base_questions_answer");
			$child_answer=explode("*",$child_answer);
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
						if(!empty($values)){
							$base_questions_items->questionsid=$id;
							$base_questions_items->flag=$child_item_flag_array[$keys];
							$base_questions_items->content=$values;
							$base_questions_items->sortid=($keys+1);
							$base_questions_items->add();
						}
					}	
				}	
				}
				if(!empty($paperid)){
					$this->baseToExam($parid,$paperid);
					$exams_paper=M("exams_paper");
					$exams_paper->classid=I("classid/d");
					$exams_paper->examdaid=$questype;
					$exams_paper->where("id=%d",$paperid)->save();
				}
		}



		/**
		 *  fromBaseToExam
		 *  将base表中的试题加入Exam表中
		 * @param  void
		 * @return  void
		 */
		private function fromBaseToExam(){
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


		//保存试卷
		public function saveexam(){
			$examsid=I("examsid/d",0);
			$examname=I("examname");
			$papername=I("papername");
			$ztime=I("ztimes");
			$isdisplay=I("display/d",0);
			$data =  stripslashes($_REQUEST["data"]);   
			$data = rtrim($data ,'"'); 
			$data = ltrim($data ,'"');
			$data = str_replace('&quot;', '"', $data);
	     	$res = json_decode($data);
	     	$key=2;
	     	M("exams")->examtime=$ztime;
	     	M("exams")->where("id=%d",$examsid)->save();
	     	$exams_paper=M("exams_paper");
	     	if($papername!=""){
	     		$exams_paper -> examsid=$examsid;
		     	$exams_paper -> classid=0;
		     	if($isdisplay==1){
		     		$exams_paper -> tcontent="<p>".$papername."</p>";
		     	}else{
		     		$exams_paper -> tcontent="";
		     	}
		        $exams_paper -> vcontent=$papername;
		        $exams_paper -> tvoiceid=1;
		        $exams_paper -> bstoptime=5;
	        	$exams_paper -> astoptime=5;
	        	$exams_paper -> readtimes=1;
	        	$exams_paper -> sortid=0;
	        	$exams_paper -> where("examsid=%d and classid=0 and sortid=0",$examsid)->delete();
	        	$exams_paper -> add();
	     	}else{
	     		$exams_paper -> where("examsid=%d and classid=0 and sortid=0",$examsid)->delete();

	     	}
	     	foreach($res as $obj){ 
	        	$id = $obj->id; 
	        	$vcontent = $obj->vncontent;
	        	$tcontent = $obj->tncontent;
	        	$repeate = $obj->repeate;
	        	$stoptime = $obj->stoptime;
	        	$qscore = $obj->qscore;
	        	$tvoiceid = $obj->tvoiceid;
	        	$sortid = $key;
	        	$key=$key+1;
	        	if($vcontent!=""){
	        		$exams_paper -> tcontent="<p>".$vcontent."</p>";
	        	}else{
	        		$exams_paper -> tcontent="";
	        	}
	        	$exams_paper -> vcontent=$vcontent;
	        	$exams_paper -> readtimes=$repeate;
	        	$exams_paper -> bstoptime=$stoptime;
	        	$exams_paper -> astoptime=$stoptime;
	        	$exams_paper -> sortid=$sortid;
	        	$exams_paper -> score=$qscore;
	        	$exams_paper -> tvoiceid=$tvoiceid;
	            $exams_paper -> where('id=%d',$id)->save();
	            $zhushi=$obj->partname;
	            $partid=$obj->partid;
	            if($partid!=''){
	            	if($zhishi==""){
                       $exams_paper->where("id=%d",$partid)->delete();
	            	}else{
	            		$exams_paper -> sortid=$sortid-1;
		            	if($zhushi!=""){
		            		$exams_paper -> tcontent="<p>".$zhushi."</p>";
		            	}else{
		            		$exams_paper -> tcontent="";
		            	}
		        		$exams_paper -> vcontent=$zhushi;
		        		$exams_paper -> where('id=%d',$partid)->save();  

	            	}
	            }else{
	            	if($zhushi!=""){
	            		$exams_paper -> sortid=$sortid-1;
		            	$exams_paper -> examsid=$examsid;
		            	if($zhushi!=""){
		            		$exams_paper -> tcontent="<p>".$zhushi."</p>";
		            	}else{
		            		$exams_paper -> tcontent="";
		            	}
		        		$exams_paper -> vcontent=$zhushi;
		        		$exams_paper -> readtimes=$repeate;
			        	$exams_paper -> bstoptime=$stoptime;
			        	$exams_paper -> astoptime=$stoptime;
			        	$exams_paper -> tvoiceid=$tvoiceid;
		        		$exams_paper -> add();
	            	}
	            	  
	            }
    	    }


		}



		/**
		 *  editCombineContentQuestions
		 *  获取组合体型列表
		 * @param  
		 * @return  void
		 */
		public function editCombineContentQuestions(){
			$id=I("id/d");
			$tcontent=html_entity_decode(I("tcontent/s"));
			$vcontent =  stripslashes(I("vcontent"));
			$vcontent = rtrim($vcontent ,'"');
			$vcontent = ltrim($vcontent ,'"');
			$vcontent = str_replace('&quot;', '"', $vcontent);
			$vcontent=json_decode($vcontent);
			$acontent=I("acontent/s");
			$base_questions=M("exams_questions");
			$base_questions_listen=M("exams_questions_listen");
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
			$base_questions=M("exams_questions");
			$base_questions_answer=M("exams_questions_answer");
			$base_questions_items=M("exams_questions_items");
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
						if(!empty($value)){
							$base_questions_items->content=$value;
							$base_questions_items->sortid=($key+1);
							$base_questions_items->where("id=%d",$itemsids[$key])->save();
					    }
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
						if(!empty($value)){
							$base_questions_items->flag=$data[$key];
							$base_questions_items->questionsid=$quesid;
							$base_questions_items->content=$value;
							$base_questions_items->sortid=($key+1);
							$base_questions_items->add();
						}
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
					if(!empty($value)){
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
		}


	public function Deltitle(){
		$id=I("id/d");
		$rs=M("exams_paper")->where("id=%d",$id)->find();
		M("exams_paper")->where("id=%d",$id)->delete();
		//删除此部分相关注释
		M("exams_paper")->where("examsid=%d and classid=0 and sortid=%d",$rs["examsid"],$rs["sortid"]-1)->delete();
		$sql="delete from engs_exams_questions_answer where engs_exams_questions_answer.questionsid in (select t.id from engs_exams_questions t where t.paperid=".$id.")";
		M()->execute($sql);
		$sql="delete from engs_exams_questions_items where engs_exams_questions_items.questionsid in (select t.id from engs_exams_questions t where t.paperid=".$id.")";
		M()->execute($sql);
		$sql="delete from engs_exams_questions_listen where engs_exams_questions_listen.questionsid in (select t.id from engs_exams_questions t where t.paperid=".$id.")";
		M()->execute($sql);

		M("exams_questions")->where("paperid=%d",$id)->delete();

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





	
}