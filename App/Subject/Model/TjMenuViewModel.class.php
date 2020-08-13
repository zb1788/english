<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class TjMenuViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'TjMenuConfig'=>array('levelid','r_subject','menuid','isrecommend','sortid','isnew','_type'=>'LEFT'),
        'MenuModules'=>array('id','title','url','remark','style','type', '_on'=>'TjMenuConfig.menuid=MenuModules.id'),
    );
}