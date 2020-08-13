<?php
namespace Subject\Service;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class UtilService 
{	
	public static function getArrayByColumn($sarray,$carray){
		$ret = array();
		//查看是一位数组还是多为数组
		if (count($sarray) == count($sarray, 1)) {
		    //一维数组 
		    foreach($carray as $Key=>$value){
		    	$isexists = array_key_exists($value, $sarray);
		    	if($isexists){
		    		$ret[$value]=$sarray[$value];
		    		//array_push($ret,$sarray[$value]);
		    	}
		    }
		} else {
			array_map(function($value) use (&$ret){
				$temp = array();
				foreach($carray as $Key=>$val){
			    	$isexists = array_key_exists($val, $value);
			    	if($isexists){
			    		$temp[$val]=$value[$val];
			    		//array_push($temp,$value[$val]);
			    	}
			    }
			    $ret[] = $temp;
			}, $sarray);  
		}
		return $ret;
	}

	//验证参数个数以及参数是否为空
	public static function checkArgument($argus,$params){
		
	}
}