<?php
require 'vendor/autoload.php';
use SSPHPSDK\RequestValidator;
use Symfony\Component\Dotenv\Dotenv;

define('ROOT_PATH', dirname(__FILE__));

if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(ROOT_PATH . '/.env');
    
    if (getenv('SINGSOUND_WARRANT_RUNTIME_ENV')) {
        define('SINGSOUND_WARRANT_RUNTIME_ENV', getenv('SINGSOUND_WARRANT_RUNTIME_ENV'));
        if (SINGSOUND_WARRANT_RUNTIME_ENV == "prod") {
            error_reporting(0);
        }
    }
} else {
    die("env config file not found");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type");

RequestValidator::registRequestValidateFunction(function ($userData) {
    // 此回调函数用来检查用户数据中用户id的有效性，也可以进行更多的内容和有效性检查。
    // 检查通过了就返回true
    /**
     * set user_id of your application of each ss_warrant
     * 1.
     * if user_id is not given, default '',
     * it means that ss_warrant is same between deffirent users
     * 2. if user_id is given,
     * SS_SDK need to give same user_id parameter to Ginger, otherwise, ss_warrant check will refuse your connection
     */
    if (array_key_exists('user_id', $userData)) {
        RequestValidator::setUserId(strval($userData['user_id']));
    } else {
        new Exception("please provide user_id in http request ");
        return false;
    }
    
    /**
     * return ture, means that:
     * your own check is done, and that's a validate request, and you want it to get ss_warrant.
     * return false, means that:
     * whatever, this request is invalidate, and you do not want it to get ss_warrant.
     */
    return true;
});
