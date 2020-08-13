<?php
namespace SSPHPSDK\SSTool;

use GuzzleHttp\Client;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class Upstream
{

    /**
     * get upstream post data
     * 
     * @param string $user_id
     *            app user id
     * @return []
     */
    public static function getUpstreamPostData($user_id,$user_client_ip)
    {
        $request = Request::createFromGlobals();
        $user_client_ip_from_request=$request->getClientIps()[0];
        if ($user_client_ip_from_request) {
            $user_client_ip=$user_client_ip_from_request;
        }
        $postData = [
            'appid' => getenv('SINGSOUND_APP_ID'),
            'user_id' => $user_id,
            'timestamp' => time(),
            'user_client_ip' => $user_client_ip
        ];
        self::checkPostDataForKeyParams($postData);
        $sign = self::createUpstreamPostDataSign($postData);
        $postData['request_sign'] = $sign;
        return $postData;
    }

    /**
     * get upstream post data signature
     * 
     * @param array $postData
     *            post data
     * @return string sha1 data sign
     */
    public static function createUpstreamPostDataSign($postData)
    {
        $postData['app_secret'] = getenv('SINGSOUND_APP_SECRET');
        ksort($postData);
        $kvOfPostData = [];
        foreach ($postData as $key => $value) {
            array_push($kvOfPostData, $key . '=' . $value);
        }
        $signString = implode('&', $kvOfPostData);
        $sign = md5($signString);
        return $sign;
    }

    public static function checkPostDataForKeyParams($postData)
    {
        if (0 == strlen($postData['appid'])) {
            throw new \Exception('no SINGSOUND_APP_ID provided.', 1);
        }
    }

    /**
     * get warrant from singsound upstream
     * 
     * @param string $user_id
     *            app user id
     * @return []
     */
    public static function getSSWarrantFromUpstream($user_id,$user_client_ip)
    {
        $postData = self::getUpstreamPostData($user_id,$user_client_ip);
        $client = new Client();
        $warrant_server_url=getenv('SINGSOUND_WARRANT_SERVER_URL');
        //echo $warrant_server_url;
        if (0 == strlen($warrant_server_url)) {
            throw new \Exception('no upstream url provided.', 3);
        }

        $response = $client->request('POST',$warrant_server_url, [
            'form_params' => $postData,
            'connect_timeout' => 5,
            'verify' => false  //如果是https连接，不验证ssl证书
        ]);
        $upstreamResult = $response->getBody()->getContents();
        $upstreamResult_obj = json_decode($upstreamResult, true);
        return $upstreamResult_obj;
    }
    
    
    
    /**
     * get warrant from singsound upstream
     * 
     * @param string $user_id
     *            app user id
     * @return []
     */
    public static function proceed_ginger_eval_result($url_query_string,$postData)
    {
        $client = new Client();
        $server_url=getenv('SINGSOUND_GINGER_SERVER_URL');
        if (!$server_url) {
            throw new \Exception('no SINGSOUND_GINGER_SERVER_URL provided.', 4);
        }
        $response = $client->request('POST', $server_url.$url_query_string, [
            'multipart' => $postData,
            'connect_timeout' => 5,
            'verify' => false,  //如果是https连接，不验证ssl证书
            'headers'  => [
                'Request-Index' => '0'
            ]
        ]);
        
        if ($response->getStatusCode()!=200) {
            throw new \Exception('eval request error.', 5);
        }
        
        $upstreamResult = $response->getBody();
        $upstreamResult = $response->getBody()->getContents();
        $upstreamResult_obj = json_decode($upstreamResult, true);
        $datatime = strtotime("now");
        $logdata["datatime"] = $datatime;
        $logdata["server_url"] = $server_url.$url_query_string;
        $logdata["user"] = $_COOKIE;
        $logdata["postData"] = $postData[2];
        $logdata["upstreamResult"] = $upstreamResult_obj;
        file_put_contents('./log/xianshenginterface.log'.date('Ymd'),$datatime.json_encode($logdata).PHP_EOL,FILE_APPEND); 
        return $upstreamResult_obj;
    }
    
    public static function buildJSONArray($user_id,$core_type,$warrant_id,array $core_params,array $audio_info)
    {
        $default_struct='{"start":{"cmd":"start","param":{"app":{"warrantId":"default","timestamp":"1482460350","userId":"default","sig":"default","applicationId":"default"},"audio":{},"request":{"coreType":"default","tokenId":"default"}}},"connect":{"cmd":"connect","param":{"app":{"warrantId":"default","timestamp":"default","userId":"default","sig":"default","applicationId":"default"},"sdk":{"source":7,"version":1,"protocol":2}}}}';
        $struct=json_decode($default_struct,true);
        $ginger_app_id=getenv('SINGSOUND_APP_ID');
        try {
            // Generate a version 1 (time-based) UUID object
            $uuid1 = Uuid::uuid1();
            $connection_id=$uuid1->getHex();
            $uuid1 = Uuid::uuid1();
            $request_id=$uuid1->getHex();
            
        } catch (UnsatisfiedDependencyException $e) {            
            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            echo 'Caught exception: ' . $e->getMessage() . "\n";
            die();
        }
        
        
        $struct['connect']['param']['app']['warrantId']=$warrant_id;
        $struct['connect']['param']['app']['timestamp']=(string)time();
        $struct['connect']['param']['app']['userId']=$user_id;
        $struct['connect']['param']['app']['applicationId']=$ginger_app_id;
        $struct['connect']['param']['app']['connect_id']=$connection_id;
        $struct['start']['param']['app']['warrantId']=$warrant_id;
        $struct['start']['param']['app']['timestamp']=(string)time();
        $struct['start']['param']['app']['userId']=$user_id;
        $struct['start']['param']['app']['applicationId']=$ginger_app_id;
        $struct['start']['param']['app']['connect_id']=$connection_id;
        $struct['start']['param']['audio']=$audio_info;
        $struct['start']['param']['request']=$core_params;
        $struct['start']['param']['request']['coreType']=$core_type;
        $struct['start']['param']['request']['tokenId']="default";
        $struct['start']['param']['request']['request_id']=$request_id;
        
        @$struct['connect']['param']['app']['signature']==self::build_signature($struct['connect']['param']);
        @$struct['start']['param']['app']['signature']==self::build_signature($struct['start']['param']);
        
        return $struct;
    }
    
    
    /**
     * 签名函数
     * @param array $param
     * @return string
     */
    public static function build_signature($param) {
        
        $encoding_data_kv =array();
        if (array_key_exists('app',$param)){
            foreach ($param['app'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('audio',$param)){
            foreach ($param['audio'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('request',$param)){
            foreach ($param['request'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        if (array_key_exists('sdk',$param)){
            foreach ($param['sdk'] as $k => $v) {
                $encoding_data_kv=array_merge($encoding_data_kv,array($k=>$v));
            }
        }
        $keys=array_keys($encoding_data_kv);
        sort($keys);
        $signature_string="";
        foreach ($keys as $key => $value) {
            $signature_string=$signature_string."&$value=".$encoding_data_kv[$value];
        }
        $signature_string=substr($signature_string, 1);
        $signature_md5=md5($signature_string);
        return $signature_md5;
    }
}