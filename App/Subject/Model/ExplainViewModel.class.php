<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class ExplainViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'BaseWordExplains'=>array('morphology','explains','enexplains','_type'=>'LEFT'),
        'BaseWordExplainsExample'=>array('cncontent','encontent','pic','mp3','sortid', '_on'=>'Word.base_explainsid=BaseWordExplains.id'),
    );
}