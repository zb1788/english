<?php
/*定义全局公共函数的地方*/

function curl_get_contents($url,$timeout=1) {
	$curlHandle = curl_init();
	curl_setopt( $curlHandle , CURLOPT_URL, $url );
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
	$result = curl_exec( $curlHandle );
	curl_close( $curlHandle );
	return $result;
}


//studentEnAnswer={studentId:'',studentname:'',schoolclass:'',homeworkId:'',spendTime:0,scores:8.5}
//post发送json数据
function curl_post_contents($url,$studentid,$studentname,$schoolclass,$homeworkid,$spendtime,$score) {
	$ch = curl_init();
	$post_fields['studentEnAnswer.studentId'] = $studentid;
	$post_fields['studentEnAnswer.studentname'] = $studentname;
	$post_fields['studentEnAnswer.schoolclass'] = $schoolclass;
	$post_fields['studentEnAnswer.homeworkId'] = $homeworkid;
	$post_fields['studentEnAnswer.spendTime'] = $spendtime;
	$post_fields['studentEnAnswer.scores'] = $score;

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 执行HTTP请求
	curl_setopt($ch , CURLOPT_URL , $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
	$res = curl_exec($ch);
	curl_close($ch);
  return $res;
}



function getuserinfo(){
		if(cookie('ut') != null){
			if(cookie('engm_ut')!=cookie('ut')){
				$sso=cookie("sso");
				$url = 'http://'.$sso.'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
				if(count(explode('zzedu',cookie('sso'))) > 1){ //如果是郑州的用户
					$url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
				}
				$currentuserpic=cookie('engm_curruserpic');
				$json_ret = file_get_contents($url);
				$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
				$result = json_decode($json_ret, true);
				if($result['authFlg']=='1')
				{
					cookie("engm_ut",cookie('ut'));
					$ouUser=$result['ouUser'];
					$user=$result['user'];
					cookie("engm_username",$user['username']);
					cookie("engm_usertype",$user['usertype']);
					cookie("engm_areacode",$user['area']['areaId']);
					cookie("engm_truename",$user['truename']);
					$tmsurl = str_replace('sso','tms',cookie('sso')).'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];

					if(count(explode('zzedu',cookie('sso'))) > 1){ //如果是郑州的用户
						$tmsurl = 'http://218.28.177.164/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
					}
					//echo $tmsurl;
					//$tmsurl = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount=jz41010110012983';
					$json_ret = file_get_contents($tmsurl);
					$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
					$studentinfo = json_decode($json_ret, true);
					if($studentinfo['result'] > 0){
						cookie("engm_truename",$studentinfo['rtnArray'][0]['student']['realname']);
					}



				}
			  else{
				redirect('http://www.czbanbantong.com');
			  }
			}
		}
		else{
			redirect('http://www.czbanbantong.com');
		}
    }
