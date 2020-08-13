<?php
namespace Homework\Model;
use Think\Model\ViewModel;
class WorkWordReadViewModel extends ViewModel {
   public $viewFields = array(
     'HomeworkWordRead'=>array('id','homeworkid'),
     'Word'=>array('id'=>'wordid','word','sortid','isdel','isword','_on'=>'HomeworkWordRead.wordid=Word.id'),
     'BaseWord'=>array('ukmark','ukmp3','letters','others','_on'=>'Word.base_wordid=BaseWord.id'),
     'BaseWordExplains'=>array('morphology','explains','enexplains','pic','_on'=>'Word.base_explainsid=BaseWordExplains.id'),
   );
}



