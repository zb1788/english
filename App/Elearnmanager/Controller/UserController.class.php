<?php

namespace Elearnmanager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class UserController extends CheckController {

    /**
     * pwdmgr  
     * 修改密码页面展示
     */
    public function pwdmgr() {

        $userid = $_SESSION["adminuserid"];
        $this->assign("userid", $userid);
        $this->display();
    }

    /**
     * pwd  修改密码输入框展示
     */
    public function pwd() {
        $userid = I("id/d");
        $this->assign("userid", $userid);
        $this->display();
    }

    /**
     * pwdedit密码保存  
     */
    public function pwdedit() {
        $userid = I("userid/d");
        $pwd = md5(I("pwd"));
        $admin = M("admin");
        $rs = $admin->where(" id = %d", $userid)->setfield("pwd", $pwd);
        if ($rs > 0) {
            $arr_return['resultflag'] = "修改成功";
        } else {
            $arr_return['resultflag'] = "修改失败";
        }
        $this->ajaxReturn($arr_return);
    }

    /**
     * useradd  
     * 用户添加
     */
    public function useradd() {
        $username = I("username");
        $pwd = I("pwd");
        $truename = I("truename");
        $ifuse = I("ifuse");
        $ifadmin = I("ifadmin");
        $subject_code = I("subject_code/s",0);
        if($ifadmin == 1){
            $subject_code = "all";
        }
        $admin = M("admin");
        $rs = $admin->where("username = '%s'", $username)->order("id")->field("username")->select();
        if (count($rs) > 0) {
            $arr_return['resultflag'] = 0;
            $arr_return['errorinfo'] = "账号名重复，请重新设置";
        } else {
            $pwd = md5($pwd);
            $admin->username = trim($username);
            $admin->pwd = trim($pwd);
            $admin->truename = trim($truename);
            $admin->subject_code = $subject_code;
            $admin->ifuse = $ifuse;
            $admin->ifadmin = $ifadmin;
            $admin->add();
            $arr_return['resultflag'] = 1;
            $arr_return['errorinfo'] = "添加成功";
        }
        $this->ajaxReturn($arr_return);
    }

   /**
     * useredit  
     * 用户编辑保存
     */
    public function useredit() {
        $id = I("id");
        $pwd = I("pwd");
        $pwd = md5($pwd);
        $username = I("username");
        $truename = I("truename");
        $ifuse = I("ifuse");
        $ifadmin = I("ifadmin");
        $subject_code = I("subject_code/s",0);
        if($ifadmin == 1){
            $subject_code = "all";
        }
        $admin = M("admin");
        $admin->username = trim($username);
        $admin->pwd = trim($pwd);
        $admin->truename = trim($truename);
        $admin->subject_code = $subject_code;
        $admin->ifuse = $ifuse;
        $admin->ifadmin = $ifadmin;
        $admin->where("id = %d", $id)->save();
    }

    /**
     * del  
     * 删除后台用户
     * 
     * @param  void
     * @return void 
     */
    public function upisuse() {
        $id = I("id");
        $admin = M("admin");
        $admin->ifuse = 0;
        $admin->where("id = %d", $id)->save();
    }

    /**
     * edit  
     * 用户编辑页面初始化
     * 
     * @param  void
     * @return void 
     */
    public function edit() {
        $admin = M("admin");
        $id = I("id");
        if (empty($id)) {
            $this->display();
        } else {
            $rs = $admin->where("id=%d", $id)->field("id,username,truename,subject_code,ifuse,ifadmin")->find();
            $username = $rs['username'];
            $truename = $rs['truename'];
            $subject_code = $rs['subject_code'];
            $ifuse = $rs['ifuse'];
            $ifadmin = $rs['ifadmin'];
            if ($ifuse == 1) {
                $ifuseyes = 'checked';
                $ifuseno = '';
            } else {
                $ifuseno = 'checked';
                $ifuseyes = '';
            }
            if ($ifadmin == 1) {
                $ifadminyes = 'checked';
                $ifadminno = '';
            } else {
                $ifadminyes = '';
                $ifadminno = 'checked';
            }
            $this->assign("username", $username);
            $this->assign("truename", $truename);
            $this->assign("subject_code", $subject_code);
            $this->assign("ifuseyes", $ifuseyes);
            $this->assign("ifuseno", $ifuseno);
            $this->assign("ifadminyes", $ifadminyes);
            $this->assign("ifadminno", $ifadminno);
            $this->assign("id", $id);
            $this->display();
        }
    }

    /**
     * edit  
     * 在此版本下添加单元信息
     * 
     * @param  void
     * @return void 将接收的参数执行到edit页面下
     */
    public function getuserlist() {
        // $admin = M("admin");
        // $rs = $admin->where('ifuse = 1')->order("id")->field("id,username,truename,ifuse,ifadmin")->select();
        $sql = "select a.id,a.username,a.truename,a.ifuse,a.ifadmin,b.detail_name from mc_admin a left join mc_rms_dictionary b on a.subject_code = b.detail_code where b.dictionary_code='subject'";
        $rs = M()->query($sql);
        $this->ajaxReturn($rs);
    }

    /**
     * getprovincelist  
     * 
     * 获取省份列表
     * @param  void
     * @return void 将接收的参数执行到edit页面下
     */
    public function getdictionarylist() {
        $dict_type = I('dict_type/s');
        $dictionary = M("dictionary");
        $rs = $dictionary->where("code='".$dict_type."' and isdel=1")->order("sortid,id")->field("id,title,remark,sortid")->select();
        $this->ajaxReturn($rs);
    }

    /**
     * provincelist  
     * delprovince
     * 
     * @param  void
     * @return void 将接收的参数执行到edit页面下
     */
    public function deldictionary() {
        $id = I("id/d");
        $dictionary = M("dictionary");
        $dictionary -> isdel = 0;
        $dictionary->where("id=%d", $id)->save();
    }

    /**
     * editdictionary
     * 英语同步练字典编辑
     */
    public function editdictionary() {
        $id = I("id/d");
        $name = I("name/s");
        $dict_type = I("dict_type/s");
        if ($dict_type == 'year') {
            $remark = '年份';
        }
        if ($dict_type == 'province') {
            $remark = '省份';
        }
        if ($dict_type == 'que_key1') {
            $remark = '疑问词';
        }
        if ($dict_type == 'que_key2') {
            $remark = '话题';
        }
        if ($dict_type == 'que_key3') {
            $remark = '技能';
        }
        $dictionary = M("dictionary");
        if ($id != 0) {
            $dictionary->title = $name;
            $dictionary->remark = $remark;
            $dictionary->where("id=%d", $id)->save();
        } else {
            $count = $dictionary->where("code = '%s'", $dict_type)->count();
            $dictionary->title = $name;
            $dictionary->code = $dict_type;
            $dictionary->remark = $remark;
            $dictionary->sortid = $count+1;
            $dictionary->add();
        }
    }

    //省份排序

    public function dictionarylistup() {
        $data = stripslashes($_REQUEST["data"]);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        foreach ($res as $obj) {
            $id = $obj->id;
            $sortid = $obj->sortid;

            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;

            $dictionary = M("dictionary");
            $dictionary->sortid = $sortid;
            $result = $dictionary->where('id=%d', $id)->save();
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $this->ajaxReturn($arr_return);
    }
//优教通域名管理
    public function getyjtlist() {
        $dictionary = M("dictionary");
        $type = I('type/d',0);
        $c2 = I('c2/s',0);
        if($type != '0'){
            $rs = $dictionary->where("code='yjt' and isdel=1 and c2='".$c2."'")->order("sortid,id")->field("id,title,remark,sortid,c1,c2,c3")->order('c3,remark')->select();
        }
        else{
            $rs = $dictionary->where("code='yjt' and isdel=1")->order("sortid,id")->field("id,title,remark,sortid,c1,c2,c3")->order('c3,remark')->select();
        }
        
        $this->ajaxReturn($rs);
    }
     public function getyjtprovice() {
        $dictionary = M("dictionary");
        $rs = $dictionary->where("code='yjt' and isdel=1")->order("sortid,id")->field("c2")->order('c3,remark')->group('c2')->select();
        $this->ajaxReturn($rs);
    }
//优教通域名添加及修改
public function edityjtdomain() {
        $id = I("id/d");
        $domainname = I("domainname/s");
        $domainip = I("domainip/s");
        $domainpro = I("domainpro/s");
        $domaincode = I("domaincode/s");
        $domaintype = I("domaintype/s");
        $dictionary = M("dictionary");
        if ($id != 0) {
            $dictionary->title = $domainname;
            $dictionary->remark = $domaintype;
            $dictionary->c1 = $domainip;
            $dictionary->c2 = $domainpro;
            $dictionary->c3 = $domaincode;
            $dictionary->where("id=%d", $id)->save();
            $arr_return['resultflag'] = 1;
            $arr_return['errorinfo'] = "修改成功";
        } else {
            $count = $dictionary->where("code = 'yjt'")->count();
            $dictionary->code = 'yjt';
            $dictionary->title = $domainname;
            $dictionary->remark = $domaintype;
            $dictionary->c1 = $domainip;
            $dictionary->c2 = $domainpro;
            $dictionary->c3 = $domaincode;
            $dictionary->sortid = $count+1;
            $dictionary->add();
            $arr_return['resultflag'] = 1;
            $arr_return['errorinfo'] = "添加成功";
        }
        $this->ajaxReturn($arr_return);
    }
    public function yjtedit() {
        $dictionary = M("dictionary");
        $id = I("id");
        if (empty($id)) {
            $this->display();
        } else {
            $rs = $dictionary->where("id=%d", $id)->field("id,title,remark,sortid,c1,c2,c3")->find();
            $domainname = $rs['title'];
            $domaintype = $rs['remark'];
            $domainip = $rs['c1'];
            $domainpro = $rs['c2'];
            $domaincode = $rs['c3'];
            $this->assign("domainname", $domainname);
            $this->assign("domaintype", $domaintype);
            $this->assign("domainip", $domainip);
            $this->assign("domainpro", $domainpro);
            $this->assign("domaincode", $domaincode);
            $this->assign("id", $id);
            $this->display();
        }
    }

    public function upyjt_domain(){
        $dictionary = M("dictionary");
        $id = I("id");
        if (empty($id)) {
           return;
        } 
        else{
           $dictionary->isdel = 0;
           $dictionary->where("id=%d", $id)->save();
        }
    }

    public function getBookCatagloryList(){
        $rs=M("book_cataglory")->where("parentid=0")->order("sortid,id")->select();
        foreach($rs as $key=>$value){
            $crs=M("book_cataglory")->where("parentid=%d",$value["id"])->order("sortid,id")->select();
            $rs[$key]["themes"]=$crs;
        }
        $this->ajaxReturn($rs);
    }


    public function editBookCataglory(){
        $id=I("id");
        $name=I("name");
        $book_catalory=M("book_cataglory");
        if(empty($id)){
            $book_catalory->name=$name;
            $book_catalory->add();
        }else{
            $book_catalory->where("id=%d",$id)->setField("name",$name);
        }  
    }

    public function delBookCataglory(){
        $id=I("id");
        $rs=M("book_cataglory")->where("id=%d",$id)->delete();
    }

    public function themes(){
        $parentid=I("parentid");
        //查询所有的父亲级别的元素
        $list=M("book_cataglory")->where("parentid=0")->order("sortid,id")->select();
        $this->assign("list",$list);
        $this->assign('parentid',$parentid);
        $this->display();
    }

    public function getBookThemeList(){
        $id=I("parentid");
        $rs=M("book_cataglory")->where("parentid=%d",$id)->order("sortid,id")->select();
        $this->ajaxReturn($rs);
    }


    public function editBookTheme(){
        $id=I("id");
        $name=I("name");
        $parentid=I("parentid");
        $book_catalory=M("book_cataglory");
        if(empty($id)){
            $book_catalory->name=$name;
            $book_catalory->parentid=$parentid;
            $book_catalory->add();
        }else{
            $book_catalory->name=$name;
            $book_catalory->parentid=$parentid;
            $book_catalory->where("id=%d",$id)->save();
        }
    }

    public function delBookTheme(){
        $id=I("id");
        $book_catalory=M("book_cataglory");
        $book_catalory->where("id=%d",$id)->delete();
    }

    public function copyBookTheme(){
        $id=I("id/d");
        $sql="insert into book_cataglory(name,parentid,sortid) select name,parentid,sortid from book_cataglory where id=".$id;
        M()->execute($sql);
    }

    public function changeBookThemeCatalogy(){
        $id=I("id/d");
        $parentid=I("parentid/d");
        $book_catalory=M("book_cataglory");
        $book_catalory->where("id=%d",$id)->setField("parentid",$parentid);
    }

    public function bookThemeCatalogyListup() {
        $data = stripslashes($_REQUEST["data"]);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        foreach ($res as $obj) {
            $id = $obj->id;
            $sortid = $obj->sortid;

            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;

            $book_catalory = M("book_cataglory");
            $book_catalory->sortid = $sortid;
            $result = $book_catalory->where('id=%d', $id)->save();
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $this->ajaxReturn($arr_return);
    }
}
?>