<?php
namespace Klx\Controller;
use Think\Controller;


class KlxpymbController extends Controller {



   public function index(){
    if(empty(cookie('deviceType'))){
      getuserinfos();
    }
   	$this->display();
   }

   public function info(){
   	$id=I('id/d');
   	$this->assign('id',$id);
   	$this->display();
   }


}


