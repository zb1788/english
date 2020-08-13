<?php
namespace Homework\Model;
use Think\Model\ViewModel;
class WorkExamPaperViewModel extends ViewModel {
   public $viewFields = array(
     'HomeworkQuiz'=>array('id'=>'quizid','homeworkid','examsid'),
     'Exams'=>array('name','sortid','isdel','quescount','_on'=>'HomeworkQuiz.examsid=Exams.id'),
     'ExamsStem'=>array('id'=>'stemId','sortid'=>'ssortid','question_playtimes','stem_type','question_score','content','stem_playtimes','stoptimes','parentid','isdel'=>'sisdel','_on'=>'Exams.id=ExamsStem.examsid'),
     'ExamsQuestions'=>array('id'=>'quesId','typeid','tcontent','question_num','itemtype','sortid'=>'qisdel','isdel'=>'qisdel','stoptimes','questions_items','questions_answer','questions_tts','questions_playtimes','_on'=>'ExamsStem.id=ExamsQuestions.stemid'),
   );
}
