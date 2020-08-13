<?php
/**
 *@author:qgy
 *@version:1.0
 *@date:2016-06-01
 *统计算法
 */

class Stastic{

    //多维数组根据
    public function batchSum($arr,$column,$sum){

        $count = count($arr);
        if($count == 0){
            return;
        }
        if($count==1){
            return $arr;
        }
        
        $left_arr = array_slice($arr,0,floor($count/2));
        $right_arr = array_slice($arr,floor($count/2),$count);

        $left = self::batchSum($left_arr,$column,$sum);
        $right = self::batchSum($right_arr,$column,$sum);

        $list = array_column($right,$column);

        $ret = $right;
        foreach($left as $key=>$value){
            $username = $value[$column];
            $rightindex = array_search($username,$list);
            if($rightindex !== false){
                $ret[$rightindex][$sum] = $ret[$rightindex][$sum] + $value[$sum];
            }else{
                $ret[] = $value;
            }
        }
        return $ret;
    }
}
