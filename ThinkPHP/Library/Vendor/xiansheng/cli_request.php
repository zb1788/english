<?php
//---------------------引入依赖包---------------------------------------
require 'vendor/autoload.php';
use SSPHPSDK\RequestValidator;
use Symfony\Component\Dotenv\Dotenv;
use SSPHPSDK\SSTool\Upstream;

//---------------------基础变量配置，请根据.env.dist 文件配置.env文件-------------------------------------
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


//---------------------第一步：授权申请：变量配置-------------------------------------
//用户id；请确保用户id真实有效，不要使用固定字符串；用来跟踪用户行为，提高学习效果，防火墙基础参数
$user_id="vcom";

// 用户信息；包含用户id、用户的客户端IP。
$user_data = array(
    "user_id" => $user_id,
    "user_client_ip"=>"127.0.0.1",//只有cli模式下才需要显式输入ip
    "dummy_info" => "content"//想要传入的其他内容
);

//---------------------第一步：授权申请：请求授权-------------------------------------
$warrant_info = RequestValidator::getWarrantWithUserInfo($user_data);
//得到授权ID，这里请加入更多的错误判断
if(array_key_exists("warrant_id",$warrant_info["data"])){
    $warrant_id=$warrant_info["data"]["warrant_id"];
}else{
    var_dump($warrant_info);
    die(" got error for warrant_id");
}


//---------------------第二步：语音评测：参数配置 文本部分-------------------------------------
//内核类型
$core_type = "en.word.score";
//内核评测参数
$core_params = array(
    "refText" => "Welcome back! Nice to see you again.",
    "precision" => 0.8,
    "rank" => 100,
    "tokenId" => "default"
);
//音频属性
$audio_info = array(
    "sampleBytes" => 2,
    "sampleRate" => 8000,
    "channel" => 1,
    "audioType" => "mp3"
);
//得到标准的语音评测的文本格式
$text =Upstream::buildJSONArray($user_id,$core_type,$warrant_id, $core_params,$audio_info);
//得到连接id
$connection_id=$text['start']['param']['app']['connect_id'];
//得到请求id
$request_id=$text['start']['param']['request']['request_id'];
//得到app_id,也可以直接从配置文件读取
$app_id=$text['start']['param']['app']['applicationId'];

//---------------------第二步：语音评测：参数配置 音频和表单部分-------------------------------------
//直接打开音频文件来提交
$form = [ ['name' => 'text','contents' => json_encode($text)   ],[ 'name' => 'audio',  'contents' => fopen('./test/en.word.score/C39223EBA07C4B56829A5F3E1002B240.mp3','r')]];
//音频内容可以用fopen，也可以直接二进制放内容
// $form = [ ['name' => 'text','contents' => json_encode($text)],[ 'name' => 'audio',  'contents' => $content]];  
// $form = [ ['name' => 'text','contents' => json_encode($text)],[ 'name' => 'audio',  'contents' => file_get_contents('./test/en.word.score/request.wav')]];


//---------------------第二步：语音评测：参数配置 URL部分-------------------------------------
//拼接请求地址
$url_query_string="/$core_type?appkey=$app_id&connect_id=$connection_id&request_id=$request_id&warrant_id=$warrant_id";


//---------------------第二步：语音评测：发起评测请求并得到结果-------------------------------------
// 结合评测请求发起评测得到结果
try {
    $result=Upstream::proceed_ginger_eval_result($url_query_string,$form);
}catch (Exception $e){
    print("request error,please check!");
    print $e->getMessage();
    print $e->getTraceAsString();
    die();
}

//---------------------第三步：客户业务逻辑实现-------------------------------------

//业务逻辑
printf("got recordId:\n");
print $result['recordId'];
print("\n");
print("got result:\n");
var_dump($result["result"]);