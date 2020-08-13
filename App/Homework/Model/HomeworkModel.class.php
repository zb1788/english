<?php
namespace Homework\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class HomeworkModel extends Model
{
	//教师布置作业
	public function saveHomework($work,$wordalound,$wordtest,$texralound,$examsquiz){
		//不同的形式保存的数据不同进行扩展的时候直接后面参数
		//保存试卷
		$word_read=M("homework_word_read");
		$word_evaluat=M("homework_word_evaluat");
		$text_read=M("homework_text_read");
		$quiz=M("homework_quiz");
		$this->ks_code=$work["ks_code"];
		$this->teachid=$work["username"];
		$this->areaid=$work["areaid"];
		$this->publish_time=Date("Y-m-d H:i:s");
		$homeworkid=$this->add();
		$teacherhomework["wacount"]=0;
		$teacherhomework["wtcount"]=0;
		$teacherhomework["tacount"]=0;
		$teacherhomework["eqcount"]=0;
		//1插入单词听读作业
		if(!empty($wordalound)){
			foreach($wordalound as $key=>$value){
               $word_read->homeworkid=$homeworkid;
               $word_read->wordid=$value["wordid"];
               $word_read->add();
			   $teacherhomework["wacount"]=$teacherhomework["wacount"]+1;
			}
		}
		if(!empty($wordtest)){
			foreach($wordtest as $key=>$value){
               $word_evaluat->homeworkid=$homeworkid;
               $word_evaluat->wordid=$value["wordid"];
               $word_evaluat->option_a=$value["option_a"];
               $word_evaluat->option_b=$value["option_b"];
               $word_evaluat->option_c=$value["option_c"];
               $word_evaluat->answer=$value["answer"];
               $word_evaluat->typeid=$value["typeid"];
               $word_evaluat->sortid=$value["sortid"];
               $word_evaluat->add();
			   $teacherhomework["wtcount"]=$teacherhomework["wtcount"]+1;
			}
		}
		if(!empty($texralound)){
			foreach($texralound as $key=>$value){
               $text_read->homeworkid=$homeworkid;
               $text_read->chapterid=$value["chapterid"];
               $text_read->add();
			   $textcount=M("text")->where("chapterid=%d and isdel=1",$value["chapterid"])->count();
			   $teacherhomework["tacount"]=$teacherhomework["tacount"]+$textcount;
			}
		}
		if(!empty($examsquiz)){
			foreach($examsquiz as $key=>$value){
               $quiz->homeworkid=$homeworkid;
               $quiz->examsid=$value["examsid"];
               $quiz->add();
               $examscount=M("exams")->where("id=%d",$value["examsid"])->find();
			   $teacherhomework["eqcount"]=$teacherhomework["eqcount"]+$examscount["quescount"];
			}
		}
		$this->where("id=%d",$homeworkid)->save($teacherhomework);
		return $homeworkid;
	}
}
