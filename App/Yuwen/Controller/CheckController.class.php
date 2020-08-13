<?php
namespace Yuwen\Controller;
use Think\Controller;

/**
* 验证是否登录控制器类
*
* @author         gm
* @since          1.0
*/

class CheckController extends Controller {
	/**
	* _initialize
	* 初始化验证
	*
	* @author         gm
	* @since          1.0
	*/
    public function _initialize(){
    	/*如果没有登录，则跳转到登录页面*/
        getuserinfos();
    }
}