<?php
	//https://en.czbanbantong.com/ubtools.php?username=410101100000018196&usertype=4&areacode=1.1.1&c=u_voice_like_zxzs&n=1&ub_url=ubhenan.czbanbantong.com&protocol=https

	$ad = time() . mt_rand(1000,9999);  //唯一值
	//$at = $at;    //j（代表）、u（代表优币）、z（代表直接扣除或者增加多少优币）
	$a = $_GET['username'];  //帐号
	$ut = $_GET['usertype']; //帐号类型
	$ac = $_GET['areacode']; //区域(不是LocalAreaCode)
	$t = date('Y-m-d H:i:s');
	$r = 'ss';
	$i = $_GET['c'];//规则名称
	$n = $_GET['n'];//数量默认1
	$protocol = $_GET['protocol'];

	$parm = '{"ad":"'.$ad.'","at":"u","a":"'.$a.'","ut":"'.$ut.'","c":"'.$i .'","ac":"'.$ac.'","n":"'.$n.'","t":"'.$t.'","r":"'.$r.'","i":"'.$i.'"}';
	
	
	$uburl = $protocol.'://'.$_GET['ub_url'];
	$uburl=$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);
	$uburl=str_replace('%7B', '{', $uburl);
	$uburl=str_replace('%7D', '}', $uburl);

	echo $uburl.'</br>';

	$curl = curl_init($uburl);
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl, CURLOPT_TIMEOUT,5);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$responseText = curl_exec($curl);
	echo 'errorInfl:</br>';
	var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	echo '--------------------------------------------</br>';
	curl_close($curl);
	//$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组
	$result = json_decode($responseText, true);
	 var_dump($result);

	$issuc=$result[0]['result'];
	if($issuc==1){
	 echo 'ok';
	}else{
	  echo 'error';
	}
?>