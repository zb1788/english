<?php
namespace Ywmanager\Controller;
use Think\Controller;
class UserController extends CheckController {
    public function index(){
        $this->display();
    }
    public function getuser(){
        $pageCurrent=I('pageCurrent/d',0);
        $page_size=I('page_size/d',0);

        $Model=M();



        $sql='select count(*) as num from yw_user_admin ';
        $sql1='select * from yw_user_admin ';

        $ifadmin = session('ifadmin');

        if($ifadmin ==1){
            $where = ' where id<>0';
        }else{
            $where = ' where id='.session('userId');
        }

        $sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
        $sql_order=' order by id ';
        $data_total=$Model->query($sql.$where);
        $total=$data_total[0]['num'];
        $sub_pages=4;
        /* 实例化一个分页对象 */
        Vendor('SubPages');
        $subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
        $page= $subPages->subPageCss3();
        $data=$Model->query($sql1.$where.$sql_order.$sql_limit);
        //echo $sql.$sql_where.$sql_order.$sql_limit;
        $word[$page]=$data;
        $this->ajaxReturn($word);
    }



    public function addUserToDb(){
        $usernameinfo = I('usernameinfo/s','');
        $passwd = I('passwd/s','');
        $level_flag = I('level_flag/d',0);


        $m = M('user_admin');


        $re = $m->where('username="%s"',$usernameinfo)->find();
        if(!empty($re)){
            exit('error');
        }

        $data['username'] = trim($usernameinfo);
        $data['truename'] = trim($usernameinfo);
        $data['pwd'] = md5(trim($passwd));
        $data['ifadmin'] = $level_flag;

        $m->add($data);
        echo 'ok';
    }


    public function resetPasswd(){
        $id = I('id/d',0);

        $m = M('user_admin');
        $m->where('id="%d"',$id)->setField('pwd',md5('123456'));
        echo 'ok';
    }



    public function delUser(){
        $id = I('id/d',0);

        $m = M('user_admin');
        $m->where('id="%d"',$id)->delete();
        echo 'ok';
    }




    public function edituser(){
        $id = I('id/d',0);

        $this->assign('id',$id);
        $this->display();
    }



    public function editUserInfo(){
        $id = I('id/d',0);
        $oldPasswd = I('oldPasswd/s','');
        $newPasswd = I('newPasswd/s','');
        $newPasswdNext = I('newPasswdNext/s','');

        $m = M('user_admin');
        $data = $m->where('id="%d"',$id)->find();

        if(md5($oldPasswd) == $data['pwd']){
            $m->where('id="%d"',$id)->setField('pwd',md5(trim($newPasswd)));
            echo 'ok';
        }else{
            echo 'err';
        }
    }


    public function recordUserOption($tablename,$option,$tableid){
        $dealtime = date('Y-m-d H:i:s');

        $m = M('user_history');
        $data['username'] = session('userName');
        $data['tablename'] = $tablename;
        $data['option'] = $option;
        $data['tableid'] = $tableid;
        $data['dealtime'] = $dealtime;

        $m->add($data);
    }





















}
