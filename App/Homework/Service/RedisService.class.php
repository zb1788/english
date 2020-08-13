<?php
namespace Homework\Service;
use Think\Model;
use \Predis\Autoloader;
use \Predis\Client;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class RedisService
{
    private $client = null;
    //集群配置文件
    public function __construct(){
        Autoloader::register();
        //设置redis配置
        $servers = array(
            '192.168.148.117:6379',
            '192.168.148.118:6380',
            '192.168.148.118:6381',
            '192.168.148.119:6379',
            '192.168.148.119:6380',
            '192.168.148.117:6381',
        );
        $options = ['cluster' =>'redis','parameters' => [
            'password' => 'redis'
        ]];
 
        $this -> client = new Client($servers, $options);
        // $server = array(
        //     'host'     => '192.168.117.113',
        //     'port'     => 6379,
        //     'database' => 0,
        //     'password' => 'redis'
        // );
        // $this -> client = new Client($server);
    }


    //普通操作
    public function add($name,$value){
        $this -> client -> set($name,$value);
		$expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
        $this -> client -> expireat($name, $expireTime);
    }  

    public function get($name){
       return $this -> client -> get($name);
    }

    public function exists($name){
        $this -> client -> exists($name);
    }

    public function incr($name){
        $this -> client -> incr($name);
    }

    public function incrby($name,$add){
        $this -> client -> incrby($name,$add);
    }

    public function delete($name){
        $this -> client -> del($name);
    }

    public function getKeys($name){
        return $this -> client -> keys($name);
    }


    /**  
        有序set表操作  
    */
    public function addSortSet($name,$index,$value){
        $this -> client -> zadd ($name,$index,$value) ;  
    }

    public function deleteSortSet($name,$value){
        $this -> client -> zadd ($name,$value) ;  
    }

    public function getSortSetByPosition($name,$begin,$end){
        $this -> client -> zrange($name,$begin,$end);
    }

    public function getAllSortSet($name){
        return $this -> client -> zrange($name,0,-1);
    }

    public function getCountSortSet($name){
        $this -> client -> zcard($name);
    }

    public function deleteSet(){

    }

    /**  
        hash表操作  
    */
    public function addHash($name,$key,$value){
        $this -> client -> hset($name,$key,$value);
        $timestamp = strtotime('+7 day');
        $this -> client -> expireat($name, $timestamp);
    } 

    public function getHash($name,$key){
        return $this -> client -> hget($name,$key);
    }

    public function existHash($name,$key){
        $this -> client -> hexists($name,$key);
    }

    public function deleteHash($name,$key){
        $this -> client -> hdel($name,$key);
    }

    public function countHash($name){
        $this -> client -> hlen($name);
    }

    public function hincrbyHash($name,$key,$add){
        $this -> client -> hincrby($name,$key,$add);
    }

    public function getAllHash($name){
        return $this -> client -> hgetall($name);
    }

}