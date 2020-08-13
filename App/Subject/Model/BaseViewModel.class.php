<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class BaseViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'BaseWord'=>array('ukmark','tag','ukmp3','letters','others','extend_json','_type'=>'LEFT'),
        'BaseWordExplains'=>array('morphology','explains','enexplains','pic', '_on'=>'Word.base_explainsid=BaseWordExplains.id'),
    );
}