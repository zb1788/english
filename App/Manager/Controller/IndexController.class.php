<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 框架
*  
* @author         gm 
* @since          1.0 
*/  
class IndexController extends CheckController {
    public function top(){ 
    	//$_SESSION['username'] = "高孟测试";
        $this->display();
    }
    public function index(){
    	$this -> display();
    }
}

 
