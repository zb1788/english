<?php
namespace Mobile\Controller;
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
      	//$this->redirect('Login/index', array('uid'=>1,'bid'=>2)); 
       	 //getuserinfo();
        //cookie("engm_username","ceshimobile");
        //cookie("engm_areacode","1.1.1.");
        //cookie("engm_usertype","2");

		version_grade_term_get();
        //cookie("username","");
        //cookie("ut",'86c028d3af6766a4ae62d82cf5b443e497b859da308f228888646de5acad65e1c1db7b77a10ad2ed');
        //study_word_text_info();



    }
    //public checkUser(){
    //    $this-> display("../index/checkuser.html");
    //}

    // public function index(){
    // 	$this->display();
    // }
	
    // /**
    //  * 登录
    //  */
    // public function login(){
    // 	$userName=I('userName/s',0);
    // 	$passwd=I('passwd/s',0);
    // 	$userName=trim($userName);
    // 	$passwd=md5(trim($passwd));
    // 	$Dao=M('user_bg');
    // 	$data=$Dao->where("username='%s'",$userName)->field('id,username,pwd')->select();
    // 	$dbpasswd=$data[0]['pwd'];
    // 	$userId=$data[0]['id'];
    // 	if ($passwd==$dbpasswd){
    // 		session('userId',$userId);
    // 		session('userName',$userName);
    // 		echo 'ok';
    // 	}else {
    // 		echo 'error';
    // 	}
    // }
    // /**
    //  * 退出登录
    //  */
    // public function logout(){
    // 		session('[destroy]');
    // 		$this->redirect('login/index');
    	 
    // }
}
