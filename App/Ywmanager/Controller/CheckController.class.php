<?php
namespace Ywmanager\Controller;
use Think\Controller;
class CheckController extends Controller {

    public function _initialize(){
    	if (empty(session('userName'))){
    		$this->redirect('login/index');
    	}
    }
}