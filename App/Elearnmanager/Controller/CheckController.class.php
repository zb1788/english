<?php

namespace Elearnmanager\Controller;

use Think\Controller;

/**
 * 验证是否登录控制器类
 *  
 * @author         gm 
 * @since          1.0 
 */
class CheckController extends Controller {

    public function _initialize() {
        /* 如果没有登录，则跳转到登录页面 */
        //$this->redirect('Login/index', array('uid'=>1,'bid'=>2));
        //echo session('adminuser');
        if (empty(session('adminuser'))) {
            $this->redirect('login/nolog');
            //header('location:'.U("login/index"));
        }
    }

}
