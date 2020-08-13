<?php
namespace Subject\Service;
use Think\Model;
use Subject\Service\UtilService;
use Homework\Service\RedisService;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 * 排名的service
 */
class RankService {

    //初始化防范连接redis集群
    public function __construct(){
		$this -> redis = new RedisService();
		file_put_contents("./log/redisInfo.log",Date("Y-m-d H:i:s").$this->redis.PHP_EOL,FILE_APPEND);
      }
      
    //获取课文朗读的排名
    public function getUserTextReadingRank($apptype,$classid="",$schoolid="",$time=""){
       
        // $redisKey = md5("text_rank_".$apptype."|".$classid."|".$schoolid."|".$time);
        // $result = $this -> redis -> get($redisKey);
        if($apptype == "1"){
            $moduleid = "10";
        }
        else{
            $moduleid = "74";
        }
        vendor("Sort");
        $config_result = M("db_english.rank_config","engs_")->where("moduleid = ".$moduleid)->select();
        foreach($config_result as $key=>$value){
            $configids[$value["type"]] = $value["id"];
        }
        //if(!$result){
            if(empty($time)){
                $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[0]; 
            }else{
                $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[0]." and date(updtime)>='".$time."'" ;
            }
            
            if(!empty($classid)&&empty($schoolid)){
                if(empty($time)){
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[1]." and typeid='".$classid."'";
                }else{
                  $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[1]." and typeid='".$classid."' and date(updtime)>='".$time."'";
                }
            }
            if(!empty($schoolid)&&empty($classid)){
                if(empty($time)){
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[2]." and typeid='".$schoolid."'";
                }else{
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[2]." and typeid='".$schoolid."' and date(updtime)>='".$time."'";
                }
                
            }
          //  echo $sql;
            $result = M()->query($sql);
            $rankresult = $result[0]["rank"];
            $rankresult = object_array(unserialize($rankresult)) ;
            //var_dump($rankresult);exit;
            $sort = new \Sort();
            $rankresult = $sort->quickSort($rankresult,"ub");
            //$this -> redis -> add($redisKey,serialize($rankresult),C("redistimeout"));
        // }else{
        //     $rankresult = unserialize($result,true);
        // }
        return $rankresult;
    }

    //获取单词的排名
    public function getUserWordRank($classid="",$schoolid="",$time=""){
        $redisKey = md5("word_rank_".$classid."|".$schoolid."|".$time);
        $result = $this -> redis -> get($redisKey);
        vendor("Sort");
        $config_result = M("db_english.rank_config","engs_")->where("moduleid = 1")->select();
        foreach($config_result as $key=>$value){
            $configids[$value["type"]] = $value["id"];
        }
        //if(!$result){
            if(empty($time)){
                $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[0]; 
            }else{
                $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[0]." and date(updtime)>='".$time."'" ;
            }
            
            if(!empty($classid)&&empty($schoolid)){
                if(empty($time)){
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[1]." and typeid='".$classid."'";
                }else{
                  $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[1]." and typeid='".$classid."' and date(updtime)>='".$time."'";
                }
            }
            if(!empty($schoolid)&&empty($classid)){
                if(empty($time)){
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[2]." and typeid='".$schoolid."'";
                }else{
                    $sql ="select rank from  db_english.engs_rank_info where configid=".$configids[2]." and typeid='".$schoolid."' and date(updtime)>='".$time."'";
                }
                
            }
          //  echo $sql;
            $result = M()->query($sql);
            $rankresult = $result[0]["rank"];
            $rankresult = object_array(unserialize($rankresult)) ;
            //var_dump($rankresult);exit;
            $sort = new \Sort();
            $rankresult = $sort->quickSort($rankresult,"ub");
            //$this -> redis -> add($redisKey,serialize($rankresult),C("redistimeout"));
        // }else{
        //     $rankresult = unserialize($result,true);
        // }
        return $rankresult;
    }


    //获取口算卡的排名
    public function getUserMathRank($classid="",$schoolid="",$time="",$configid="27"){
        vendor("Sort");
        //查询当前第几页的数据
        $redisKey = md5("math_rank_".$classid."|".$schoolid."|".$time);
        $result = $this -> redis -> get($redisKey);
        if(!$result){
            if(empty($time)){
                $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid; 
            }else{
                $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid." and date(updtime)>='".$time."'" ;
            }
            
            if(!empty($classid)&&empty($schoolid)){
                if(empty($time)){
                    $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid." and typeid='".$classid."'";
                }else{
                  $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid." and typeid='".$classid."' and date(updtime)>='".$time."'";
                }
            }
            if(!empty($schoolid)&&empty($classid)){
                if(empty($time)){
                    $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid." and typeid='".$schoolid."'";
                }else{
                    $sql ="select rank from  db_math.sx_stage_rank where configid=".$configid." and typeid='".$schoolid."' and date(updtime)>='".$time."'";
                }
                
            }
            $result = M()->query($sql);
            $rankresult = $result[0]["rank"];
            $rankresult = object_array(unserialize($rankresult)) ;
            //var_dump($rankresult);exit;
            $sort = new \Sort();
            $rankresult = $sort->quickSort($rankresult,"ub");
            $this -> redis -> add($redisKey,serialize($rankresult),C("redistimeout"));
        }else{
            $rankresult = unserialize($result,true);
        }
        return $rankresult;
    }
   
    
}