<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');


define('PROTOCOL',"http");


//普通模式，解决官方分组不支持cli的问题
$depr = '/';
$path   = isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:'';

if(!empty($path)) {
    $params = explode($depr,trim($path,$depr));
}

!empty($params)?$_GET['g']=array_shift($params):"";
!empty($params)?$_GET['m']=array_shift($params):"";
!empty($params)?$_GET['a']=array_shift($params):"";

//if(count($params)>1) {
//// 解析剩余参数 并采用GET方式获取
//    preg_replace('@(\w+),([^\/]+)@e', '$_GET[\'\\1\']="\\2";', implode(',',$params));
//}
//if(!empty($params)){
//    $paramArr = explode("&",$params[0]);
//    if(count($paramArr)>0){
//        foreach($paramArr as $key=>$value){
//            $temp = explode("=",$value);
//            $_GET[$temp[0]] = $temp[1];
//        }
//    }
//}

//define('APP_MODE','cli');
define('MODE_NAME','cli');
define('APP_DEBUG',True);
define('APP_SUB','/');
define('APP_NAME','App');
define( 'APP_PATH', dirname(__FILE__).'/App/' );
require dirname(__FILE__).'/ThinkPHP/ThinkCliPHP.php';