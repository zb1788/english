<?php
namespace Klx\Controller;
use Think\Controller;


class KlxzmmbController extends Controller {



   public function info(){
      cookie('subjectid','0003');
      if(empty(cookie('deviceType'))){
         getuserinfos();
      }
      $gradeid=I("gradeid");
      if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){
         cookie("gradeid",$gradeid);
      }
      $this->display();
   }



}


