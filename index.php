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
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

$appstarttime = getMillisecond();
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define("WEB_DOMAIN", "enhxp.czbanbantong.com");
define("MP3_DOMAIN", "resen.czbanbantong.com");
//访问协议
if( stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true
    || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ) {
    $protocol = 'https';
    //$RESOURCE_DOMAIN = MP3_DOMAIN.":8443";
} else {
    $protocol = 'http';
    //$RESOURCE_DOMAIN = MP3_DOMAIN.":8080";
}
$RESOURCE_DOMAIN = MP3_DOMAIN;
define('PROTOCOL',$protocol);
define("RESOURCE_DOMAIN", $RESOURCE_DOMAIN);
//INSTALL表示安装部署方式 1表示单机 0表示双击
define("INSTALL", 0);

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('BUILD_LITE_FILE',false);
define('APP_DEBUG',true);

define('APP_NAME','App');
// 定义应用目录
define('APP_PATH','./App/');
define('APP_ROOT','./');
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
//require './App/Runtime/lite.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
