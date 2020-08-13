<?php 
/*定义分组公共函数的地方*/
function getUnitID($arr_cur_unit, $unitname = '')
{
	if (!isset($arr_unit)) { 
	    $unit=M('rms_unit');
	    $rs=$unit->where('flag=1 and is_unit=0')->field('ks_code,r_grade,r_volume,r_version,ks_name,ks_name_short') -> select(); 
		foreach($rs as $v) {
			$arr_unit[$v["ks_code"]]["r_grade"] = intval( $v["r_grade"]);
			$arr_unit[$v["ks_code"]]["r_version"] = intval( $v["r_version"]);
			$arr_unit[$v["ks_code"]]["r_volume"] =  intval( $v["r_volume"]);
			$arr_unit[$v["ks_code"]]["ks_name"] =  strtoupper($v["ks_name"]);		
		}
	}
	$arr_cur_unit["ks_name"] =  strtoupper($arr_cur_unit["ks_name"]);
	if (isset($arr_unit)) {
		$ks_code = array_search($arr_cur_unit, $arr_unit);
	}else
	{
		$ks_code = 0;
	}
	return $ks_code;
}
function clearSQL($str){ 
	$str = str_replace( '"', '\"' ,$str ) ; 
	$str = str_replace( '\\\\', '\\' ,$str ) ; 
	return trim($str);
}
function daddslashes($string, $force = 0, $strip = false) {
	if (!get_magic_quotes_gpc() || $force) {
		if (is_array($string)) {
			// 如果其为一个数组则循环执行此函数
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			// 下面是一个三元操作符，如果$strip为true则执行stripslashes去掉反斜线字符，再执行addslashes
			// 这里为什么要将$string先去掉反斜线再进行转义呢，因为有的时候$string有可能有两个反斜线，stripslashes是将多余的反斜线过滤掉
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
//判断字符串的的编码类型
function getchar($str,$formcode,$aftercode){
	// $encode = mb_detect_encoding($str, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
	// //return $encode;
 //    if ($encode != $aftercode){
 //      $str = iconv($formcode,$aftercode,$str);
 //    }
    return $str;
}

//获取试听音频
function gettempmp3($content,$voiceid,$rate){
        $url = "http://192.168.151.206/voice/voice.ashx";
        $post_data["content"]=$content;
        $post_data["voiceid"]=$voiceid;
        $post_data["rate"]=$rate;
		$ch = curl_init($url);
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_TIMEOUT,50000); 
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'Referer:http://www.131458.com/',  
	            'Host:www.131458.com',
	            'User-Agent:Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'),
	            'Cache-Control:no-cache',
	            'X-Requested-With:XMLHttpRequest',
	            'cookie:ASP.NET_SessionId=4biasa45s5dz0t55wxivuuai; Hm_lvt_ccc93bebd5e7bdc84975186073891702=1448072583; Hm_lpvt_ccc93bebd5e7bdc84975186073891702=1448075485; bdshare_firstime=1448072583684; wzwsconfirm=699a156309626dc0206ffda0c40c55f9; wzwstemplate=Mw==; wzwschallenge=V1pXU19DT05GSVJNX1BSRUZJWF9MQUJFTDMxMjQ0OTU='
	        );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $output = curl_exec($ch);
	    
	    return $output;
}
?>