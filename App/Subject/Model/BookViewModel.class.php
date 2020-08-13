<?php
namespace Subject\Model;
use Think\Model\ViewModel;

class BookViewModel extends ViewModel
{
	//字段
	public $viewFields = array(
        'Book'=>array('id','ks_code','name'=>'bookname','pic','_type'=>'LEFT'),
        'BookCataglory'=>array('id'=>'cataid','name'=>'cataglory', '_on'=>'Book.ks_code=BookCataglory.id'),
    	//'BookCataglory'=>array('cata.name'=>'parentname', '_as'=>"cata",'_on'=>'BookCataglory.parentid=cata.id'),
    );
}