<?php
namespace Manager\Model;
use Think\Model\ViewModel;

class WordViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'Word'=>array('id','word','isstress','chaptername','isword','ks_code','base_wordid','base_explainsid','explains_content','sortid','isdel','explains_mp3','_type'=>'LEFT'),
        'BaseWord'=>array('ukmark','tags','usmp3','letters','others','extend_json', '_on'=>'Word.base_wordid=BaseWord.id','_type'=>'LEFT'),
        'BaseWordExplains'=>array('morphology','explains','enexplains','pic', '_on'=>'Word.base_explainsid=BaseWordExplains.id'),
    );
}