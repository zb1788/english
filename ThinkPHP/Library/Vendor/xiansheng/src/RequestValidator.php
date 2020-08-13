<?php
namespace SSPHPSDK;

use \Closure;
use SSPHPSDK\SSInterface\SSValidator;
use SSPHPSDK\SSTool\Upstream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestValidator implements SSValidator
{

    public static $user_id = '';

    /**
     * get singsound warrant from upstream
     *
     * @return exception | ['code'=>integer, 'msg'=>'code description', 'data'=>[]]
     */
    public static function getSSWarrant($user_id = null, $user_client_ip = "127.0.0.1")
    {
        if ($user_id) {
            return Upstream::getSSWarrantFromUpstream($user_id, $user_client_ip);
        } else {
            return Upstream::getSSWarrantFromUpstream(self::$user_id, $user_client_ip);
        }
    }

    /**
     * get user data by post, get, and cookie
     *
     * @return ['key'=>'value']
     */
    public static function getUserData()
    {
        $request = Request::createFromGlobals();
        $postData = $request->request->all();
        $getData = $request->query->all();
        $cookieData = $request->cookies->all();
        return array_merge($cookieData, $getData, $postData);
    }

    public static function setUserId($user_id)
    {
        self::$user_id = strval($user_id);
    }

    /**
     * 请传入有效的用户信息来进行授权申请
     * 
     * @param array $userData            
     * @throws \Exception
     * @return array
     */
    public static function getWarrantWithUserInfo(array $userData)
    {
        if (array_key_exists("user_id", $userData)) {
            try {
                $SSWarrant = self::getSSWarrant($userData['user_id'], $userData['user_client_ip']);
            } catch (\Exception $e) {
                $SSWarrant = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                    'data' => []
                ];
            }
            $SSWarrant['data']['user_data'] = $userData;
            return $SSWarrant;
        } else {
            throw new \Exception("no user_id found in userData.");
        }
    }

    /**
     * using a Closure to validate request
     * if Closure returned true:
     * a upstream request will be sent, and singsound warrant will be returned to client
     * if Closure returned false:
     * 401 unauthorised will be return
     *
     * @param Closure $validator
     *            request validator
     * @return [type] [description]
     */
    public static function registRequestValidateFunction(Closure $validator)
    {
        $userData = self::getUserData();
        $isValidate = $validator($userData);
        if ($isValidate) {
            try {
                $SSWarrant = self::getSSWarrant($userData['user_id']);
            } catch (\Exception $e) {
                $SSWarrant = [
                    'code' => $e->getCode(),
                    'msg' => $e->getMessage(),
                    'data' => []
                ];
            }
            $SSWarrant['data']['user_data'] = $userData;
            (new Response(json_encode($SSWarrant), Response::HTTP_OK, array(
                'content-type' => 'application/json'
            )))->send();
        } else {
            (new Response('unauthorised', Response::HTTP_UNAUTHORIZED, array(
                'content-type' => 'text/plain'
            )))->send();
        }
    }
}