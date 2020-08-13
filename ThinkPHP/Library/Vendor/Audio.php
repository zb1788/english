<?php
//---------------------引入依赖包---------------------------------------
require 'xiansheng/vendor/autoload.php';
use SSPHPSDK\RequestValidator;
use Symfony\Component\Dotenv\Dotenv;
use SSPHPSDK\SSTool\Upstream;

define('ROOT_PATH', dirname(__FILE__));

class Audio {

    private $user_id;
    private $client_ip;
    private $warrant_id;
    private $filename = "./test/en.word.score/C39223EBA07C4B56829A5F3E1002B240.mp3";
    //内核类型数组
    private $core_type_array = array(
        "en.word.score",
        "en.sent.score"
    );

    //音频属性
    private $audio_info = array(
        "sampleBytes" => 2,
        "sampleRate" => 8000,
        "channel" => 1,
        "audioType" => "mp3"
    );

    //内核评测参数
    private $core_params = array(
        "refText" => "Welcome back! Nice to see you again.",
        "precision" => 0.8,
        "rank" => 100,
        "tokenId" => "default"
    );

    // 用户信息；包含用户id、用户的客户端IP。
    private $user_data = array(
        "user_id" => "vcom",
        "user_client_ip"=>"127.0.0.1",//只有cli模式下才需要显式输入ip
        "dummy_info" => "content"//想要传入的其他内容
    );

    //---------------------基础变量配置，请根据.env.dist 文件配置.env文件-------------------------------------
    private function loadConfig(){
        if (file_exists(ROOT_PATH . '/xiansheng/.env')) {
            $dotenv = new Dotenv();
            $dotenv->load(ROOT_PATH . '/xiansheng/.env');
            if (getenv('SINGSOUND_WARRANT_RUNTIME_ENV')) {
                define('SINGSOUND_WARRANT_RUNTIME_ENV', getenv('SINGSOUND_WARRANT_RUNTIME_ENV'));
                if (SINGSOUND_WARRANT_RUNTIME_ENV == "prod") {
                    error_reporting(0);
                }
            }
        } else {
            die("env config file not found");
        }
    }

    //---------------------第一步：授权申请：变量配置-------------------------------------
    public function grant($user_info){
        // 用户信息；包含用户id、用户的客户端IP。
        $user_data = array_merge($this->user_data,$user_info);
        //获取token
        $inifilename = APP_ROOT.'/recordwav/xiansheng.ini';
        $ini_array = parse_ini_file($inifilename);
        //var_dump($ini_array);
        $warrant_id = $ini_array['SINGSOUND_WARRANT_ID'];
        $warrant_timestamp = $ini_array['SINGSOUND_WARRANT_TIMESTAMP'];
        $now = time();
        if(($now - 10*60)<$warrant_timestamp){
            $this->warrant_id=$warrant_id;
            //echo $warrant_id;
        }else{
            //---------------------第一步：授权申请：请求授权-------------------------------------
            $warrant_info = RequestValidator::getWarrantWithUserInfo($user_data);
            //var_dump($warrant_info);
            //得到授权ID，这里请加入更多的错误判断
            if(array_key_exists("warrant_id",$warrant_info["data"])){
                $this->warrant_id=$warrant_info["data"]["warrant_id"];
                $warrant_id = $this -> warrant_id;
                $ini_array['SINGSOUND_WARRANT_ID'] = $warrant_id;
                $ini_array['SINGSOUND_WARRANT_TIMESTAMP'] = $now + 20*60;
                //var_dump($ini_array);
                //echo $warrant_id;
                $newini = "";
                foreach ($ini_array as $k => $v){
                    $newini= $newini.$k."=".$v."\r\n";
                }
                //echo $newini;
                if ( ($fi = fopen($inifilename,"w")) )
                {
                    flock($fi, LOCK_EX);//排它锁
                    fwrite($fi, $newini);
                    flock($fi, LOCK_UN);                        
                    fclose($fi);                                
                }
            }else{
                // $ret["result"] = array();
                // $ret["code"] = 0;
                // $ret["msg"] = " got error for warrant_id";
                //die(" got error for warrant_id");
            }
        }
    }


    //---------------------第二步：语音评测：参数配置 文本部分-------------------------------------
    /**
     * $evaluate_type 评测类型 0单词 1课文
     * $evaluate_params 评测内容 "refText" => "Welcome","precision" => 0.8,"rank" => 100,"tokenId" => "default"
     * $user_info 用户ip "user_id" => "vcom","user_client_ip"=>"127.0.0.1","dummy_info" => "content"
     */
    public function speakEvaluate($evaluate_type,$evaluate_params,$user_info,$audio_info,$filename){
        //合并数组
        $core_params = array_merge($this->core_params,$evaluate_params);
        $audio_info = array_merge($this->audio_info,$audio_info);
        $this->filename= $filename;
        //加载配置文件
        $this -> loadConfig();
        //授权申请
        $this -> grant($user_info);

        $core_type = $this->core_type_array[$evaluate_type];
        //得到标准的语音评测的文本格式
        $text =Upstream::buildJSONArray($this->user_id,$core_type,$this->warrant_id, $core_params,$audio_info);
        //得到连接id
        $connection_id=$text['start']['param']['app']['connect_id'];
        //得到请求id
        $request_id=$text['start']['param']['request']['request_id'];
        //得到app_id,也可以直接从配置文件读取
        $app_id=$text['start']['param']['app']['applicationId'];
        //---------------------第二步：语音评测：参数配置 音频和表单部分-------------------------------------
        //直接打开音频文件来提交
        $form = [ ['name' => 'text','contents' => json_encode($text)   ],[ 'name' => 'audio',  'contents' => fopen($this->filename,'r')],['name' => 'filename','contents' => $filename]];
        //$form = [ ['name' => 'text','contents' => json_encode($text)   ],[ 'name' => 'audio',  'contents' => fopen('./test/en.word.score/C39223EBA07C4B56829A5F3E1002B240.mp3','r')]];
        //音频内容可以用fopen，也可以直接二进制放内容
        // $form = [ ['name' => 'text','contents' => json_encode($text)],[ 'name' => 'audio',  'contents' => $content]];  
        // $form = [ ['name' => 'text','contents' => json_encode($text)],[ 'name' => 'audio',  'contents' => file_get_contents('./test/en.word.score/request.wav')]];
        //var_dump($form);

        //---------------------第二步：语音评测：参数配置 URL部分-------------------------------------
        //拼接请求地址
        $url_query_string="/$core_type?appkey=$app_id&connect_id=$connection_id&request_id=$request_id&warrant_id=$this->warrant_id";

        $ret = array();


        //---------------------第二步：语音评测：发起评测请求并得到结果-------------------------------------
        // 结合评测请求发起评测得到结果
        try {
            $result=Upstream::proceed_ginger_eval_result($url_query_string,$form);
            $ret["result"] = $result;
            $ret['data']['score'] = $result['result']['overall'];
            $ret["code"] = 1;
            $ret["msg"] = "成功";
        }catch (Exception $e){
            // print("request error,please check!");
            // print $e->getMessage();
            // print $e->getTraceAsString();
            $ret["result"] = array();
            $ret["code"] = 0;
            $ret['data']['score'] = 0;
            $ret["msg"] = $e->getMessage();;

            //die();
        }
        return $ret;
    }
}

