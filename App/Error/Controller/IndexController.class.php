<?php
namespace Error\Controller;
use Think\Controller;

/** 
* 框架
*  
* @author         gm 
* @since          1.0 
*/  
class IndexController extends Controller {
    public function fail(){
    	$this->display("fail");
    }

    public function error(){
    	$this->display("404");
    }
}

 
