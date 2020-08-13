<?php
namespace Klx\Controller;
use Think\Controller;


class KlxpyController extends Controller {



   public function index(){
   	$this->display();
   }

	public function getPlsUrl(){
		$userarea=cookie('localAreaCode');
		$yjArr = get_ip($userarea,'');
		$plsip = $yjArr['pls']['title'];
		$this->ajaxReturn($plsip);
	}

	public function playswf(){
		$this->assign('resourceurl',getResourcePath());
		$this->display();
    }

}


