<?php
namespace Elearnmanager\Controller;
use Think\Controller;
/**/
class LoginController extends Controller {
    public function index(){
    	// $Word  = M('Word','base_');
	    // $Word -> select();
	    // var_dump(  $Word -> select());
	  
        //$this->show(dump($User->select()),'utf-8');
        $this->display();
    }
    
     /**
     * 登录
     */
    public function login(){
//     	echo 'aaa';
       session(array('expire'=>3600));
    	$username = I('username/s');
        $pwd = I('pwd');
        $pwd = md5($pwd);
        //echo $pwd;
        $admin=M("admin");
        $rs=$admin -> where("username='%s'",$username) -> field("id,username,pwd,ifadmin,ifuse,subject_code") -> find();
        //$this ->ajaxReturn($rs);
        //dump($rs);
        if(count($rs)==1)
        {
            $arr_return['flag']=0;
            $arr_return['errorinfo']="账号不存在，请重新输入";
        }
        else{
            if ($pwd == $rs['pwd']) {
            	session('adminuser',$username);
    		    session('adminuserid',$rs['id']);
    		    session('ifadmin',$rs['ifadmin']);
                session('admin_subject_code',$rs['subject_code']);
                session('userName','xxxx');
                 // $_SESSION["adminuser"] = $username;
                 // $_SESSION["adminuserid"] = $rs['id'];
                 // $_SESSION["ifadmin"] = $rs['ifadmin'];
                 if ($rs['ifuse'] == 0) {
                 	$arr_return['flag']=0;
                 	$arr_return['errorinfo']="该账号未启用，请联系管理员";
                 }
                 else
                {
                    $arr_return['flag']=1;
                 	$arr_return['errorinfo']="";
                }

            }
            else{
                $arr_return['flag']=0;
                $arr_return['errorinfo']="密码错误，请重新输入";
            }
        }
    	$this -> ajaxReturn($arr_return);
    }
 public function nolog(){
	$this -> display();
 }
    /**
     * 退出登录
     */
    public function logout(){
    		session('[destroy]');
    		//$this->redirect('../login/index');
    	 
    }
}