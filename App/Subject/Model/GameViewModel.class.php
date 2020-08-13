<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class GameViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'WordGame'=>array('id','name','pic','levelnum','wordnum','sortid','sortid','_type'=>'LEFT'),
        'UserWordGameLevel'=>array('gameid', '_on'=>'WordGame.id=UserWordGameLevel.gameid'),
    );
}