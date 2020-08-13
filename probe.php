<?php

$ITFTime = getMillisecond();
$starttime = getMillisecond();

$ret = array();

//连接测试数据库

error_reporting(0);


$authentication = $_SERVER['YYLAuthentication'];

if(md5("yylserver") != $authentication){
    $ret["LogLevel"] = "Error";
    $ret["ITFState"] = "OK";
    $ret["ITFCode"] = "0";
    $ret["msg1"] =  "非法访问";
    $ret["ErrorModule"] = "YYL";
    $ret["ITFTime"] = number_format(((getMillisecond() - $starttime)/1000), 3, ".", "");
    echo json_encode($ret);
    return;
}

$config = require_once __DIR__."/App/Common/Conf/config.php";
$host = $config["DB_HOST"];
$username = $config["DB_USER"];
$password = $config["DB_PWD"];
$database = $config["DB_NAME"];
$dataport = $config["DB_PORT"];
$serverhost = $_SERVER["SERVER_ADDR"];

try {
    $pdo = new PDO("mysql:host=$host;port=$dataport;dbname=$database", $username, $password);//创建一个pdo对象
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    $ret["LogLevel"] = "Error";
    $ret["ITFState"] = "OK";
    $ret["ITFCode"] = "20002";
    $ret["msg1"] =  "server:$serverhost;mysql:host=$host;port=$dataport;dbname=$database;dbuser=$username;pwd=$password Catch".$e->getMessage();
    $ret["ErrorModule"] = "YYL";
    $ret["ITFTime"] = number_format(((getMillisecond() - $ITFTime)/1000), 3, ".", "");
    echo json_encode($ret);
    return;
}

//连接测试redis
$ITFTime = getMillisecond();
require_once "./ThinkPHP/Library/Predis/Autoloader.class.php";
Predis\Autoloader::register();
$redishost = $config["redis"]["host"];
$redisport = $config["redis"]["port"];
$redisauth = $config["redis"]["auth"];
$redis = new Predis\Client();
try {
    $redis->connect($redishost, $redisport);
    $redis->auth($redisauth);
}catch(Exception $e){
    $ret["LogLevel"] = "Error";
    $ret["ITFState"] = "OK";
    $ret["ITFCode"] = "20003";
    $ret["msg1"] =  "server:$serverhost;redis:host=$redishost;port=$redisport;pwd=$redisauth Catch:redis connection exception";
    $ret["ErrorModule"] = "YYL";
    $ret["ITFTime"] = number_format(((getMillisecond() - $ITFTime)/1000), 3, ".", "");
    echo json_encode($ret);
    return;
}


$ret["LogLevel"] = "INFO";
$ret["ITFState"] = "OK";
$ret["ITFCode"] = "20000";
$ret["msg1"] =  "server:$serverhost;数据库和redis正常";
$ret["ErrorModule"] = "YYL";
$ret["ITFTime"] = number_format(((getMillisecond() - $starttime)/1000), 3, ".", "");
echo json_encode($ret);


function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}


?>