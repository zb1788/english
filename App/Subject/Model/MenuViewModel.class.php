<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class MenuViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'MenuConfig'=>array('r_grade','r_subject','menuid','isrecommend','sortid','_type'=>'LEFT'),
        'MenuModules'=>array('id','title','url','remark','style','type', '_on'=>'MenuConfig.menuid=MenuModules.id'),
    );
}