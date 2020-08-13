<?php
namespace Klx\Controller;
use Think\Controller;


class CheckController extends Controller {



    public function _initialize(){
      $userarea=cookie('localAreaCode');
      $yjArr = get_ip($userarea,'');
      $plsip = $yjArr['pls']['c1'];
      cookie('plsip',$plsip);
    }




}


