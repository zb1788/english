<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class WordBookViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
				'Word'=>array('id','word','ks_code','base_wordid','base_explainsid','sortid','isdel'=>'wisdel','_type'=>'LEFT'),
				'BaseWord'=>array('ukmark','tags','ukmp3','letters','others','extend_json', '_on'=>'Word.base_wordid=BaseWord.id','_type'=>'LEFT'),
				'BaseWordExplains'=>array('morphology','explains','enexplains','pic', '_on'=>'Word.base_explainsid=BaseWordExplains.id'),
        'UserWordBook'=>array('max(UserWordBook.id)'=>'bookid','(case when UserWordBook.id is null then 0 else 1 end)'=>'iscollect','source','word'=>'bookword','_on'=>'UserWordBook.base_wordid=Word.base_wordid and UserWordBook.base_explainsid=BaseWordExplains.id and UserWordBook.isdel=1'),
    );
}
