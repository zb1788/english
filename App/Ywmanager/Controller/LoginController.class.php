<?php
namespace Ywmanager\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
    	$this->display();
    }

    /**
     * 登录
     */
    public function login(){
    	$userName=I('username/s','');
    	$passwd=I('pwd/s','');
    	$userName=trim($userName);
    	$passwd=md5(trim($passwd));

    	$Dao=M('user_admin');
    	$data=$Dao->where("username='%s'",$userName)->field('id,username,pwd,ifadmin')->find();
        if(empty($data)){
            echo '用户名不对';//用户名不对
            exit;
        }

    	$dbpasswd=$data['pwd'];
        $userId=$data['id'];
    	$ifadmin=$data['ifadmin'];

    	if ($passwd==$dbpasswd){
    		session('userId',$userId);
            session('userName',$userName);
    		session('ifadmin',$ifadmin);
		session('adminuser','xxx');
    		echo 'ok';
    	}else {
    		echo '密码不对';//密码不对
    	}
    }


    /**
     * 退出登录
     */
    public function logout(){
    		session('[destroy]');
    		$this->redirect('login/index');
    }
}
