<?php
namespace Homework\Model;
use Think\Model\ViewModel;
class WorkWordTestViewModel extends ViewModel {
   public $viewFields = array(
     'HomeworkWordEvaluat'=>array('id','homeworkid','wordid','option_a','option_b','option_c','answer','typeid','sortid'=>'esortid'),
     'Word'=>array('word','sortid','isdel','isword','_on'=>'HomeworkWordEvaluat.wordid=Word.id'),
     'BaseWord'=>array('ukmark','ukmp3','letters','others','_on'=>'Word.base_wordid=BaseWord.id'),
     'BaseWordExplains'=>array('morphology','explains','enexplains','pic','_on'=>'Word.base_explainsid=BaseWordExplains.id'),
   );
}

