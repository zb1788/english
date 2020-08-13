<?php

namespace Home\Controller;

use Think\Controller;
class LvsTestController extends Controller {
    public function index(){
        $model=M('menu_modules');
     
            $rs = $model ->select();
            if($rs){
                echo "OK";
            }
            else{
                echo "error";
            }
        
    }
}