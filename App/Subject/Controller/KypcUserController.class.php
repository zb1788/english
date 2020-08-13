<?php
namespace Subject\Controller;
use Think\Controller;

/**
* 验证是否登录控制器类
*
* @author         gm
* @since          1.0
*/

class KypcUserController extends Controller {
    //疯转用户信息
    public $user=array();
	/**
	* _initialize
	* 初始化验证
	*
	* @author         gm
	* @since          1.0
	*/
    public function _initialize(){
    	/*如果没有登录，则跳转到登录页面*/
        // cookie("username","41010110000674");
        // cookie("usertype","4");
        // cookie("localareacode","13.");
        // cookie("areacode","1.1.1");
        $this->user["username"]=cookie("username");
        $this->user["versionid"]=cookie("versionid");
        $this->user["subjectid"]=cookie("subjectid");
        $this->user["termid"]=cookie("termid");
        $this->user["usertype"]=cookie("usertype");
        $this->user["classid"]=cookie("classid");
        $this->user["schoolid"]=cookie("schoolid");
        $this->user["localareacode"]=cookie("localAreaCode");
        $this->user["areacode"]=cookie("areacode");
        $this->user["gradeid"]=cookie("gradeid");
        $this->user["phpsession"]=cookie("PHPSESSID");
        $this->user["userpic"]=cookie("userpic");    
    }
}