<?php
/*
 * @Author: your name
 * @Date: 2018-11-20 10:00:33
 * @LastEditTime: 2020-07-08 15:54:45
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \www\en\App\Shuxue\Controller\CheckController.class.php
 */ 
namespace Shuxue\Controller;
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