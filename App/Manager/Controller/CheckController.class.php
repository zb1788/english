<?php
namespace Manager\Controller;

use Think\Controller;

/**
 * 验证是否登录控制器类
 *  
 * @author         gm 
 * @since          1.0 
 */
class CheckController extends Controller {

    public function _initialize() {
	file_put_contents('operamanalog.log',time().'username:'.session('adminuser').'|Controller:'.CONTROLLER_NAME."|ACTION:".ACTION_NAME.PHP_EOL,FILE_APPEND);
        foreach($_REQUEST as $k=>$v){
	        file_put_contents('operamanalog.log','key:'.$k.'|value:'.$v.PHP_EOL,FILE_APPEND);
         }
	 /* 如果没有登录，则跳转到登录页面 */
        //$this->redirect('Login/index', array('uid'=>1,'bid'=>2));
        if (empty(session('adminuser'))) {
            $this->redirect('login/nolog');
            //header('location:'.U("login/index"));
        }
    }

}
