<?php 
/*定义全局公共函数的地方*/
/** 
* version_grade_term_set  
* 设置版本、年级、学期的COOKIE及更新数据库
* @param 	$versionid 	int 版本
* @param 	$gradeid 	int 年级
* @param 	$termid 	int 学期
* @return 	void
* @author   gm 
* @since    1.0 
*/  
function version_grade_term_set($versionid,$gradeid,$termid)
{
	//$DAO = M("");
	cookie('engm_versionid',$versionid);
	cookie('engm_gradeid',$gradeid);
	cookie('engm_termid',$termid);
	//更新数据库配置
	//$DAO = M("");

	version_grade_term_getname();

}

/** 
* version_grade_term_get  
* 读取数据配置的版本、年级、学期，无值，则取数据库值，无配置则根据配置常量设置 
* @param 	void
* @return 	void
* @author   gm 
* @since    1.0 
*/  
function version_grade_term_get()
{
	if (empty(cookie('engm_versionid')) || empty(cookie('engm_versionid')) || empty(cookie('engm_termid'))) {	
		//读取数据库配置
		//$DAO = M("");
		if (empty($DAO)) {
			//
			$versionid =  C("CONST_VERSIONID"); 
			$gradeid =  C("CONST_GRADEID"); 
			$termid =  C("CONST_TERMID");
			if(!empty(cookie('engm_username'))){
				$rs=M("config")->where("userid='%s' and areacode='%s'",cookie('engm_username'),cookie('engm_areacode'))->field("gradeid,termid,versionid")->order("configtime desc")->find();
				if(!empty($rs)){
					$versionid=$rs["versionid"]; 
			        $gradeid = $rs["gradeid"]; 
			        $termid = $rs["termid"];
				}
			} 
			cookie('engm_versionid',$versionid);
			cookie('engm_gradeid',$gradeid);
			cookie('engm_termid',$termid);
			version_grade_term_getname();
		}
		else
		{			
			cookie('engm_versionid',$versionid);
			cookie('engm_gradeid',$gradeid);
			cookie('engm_termid',$termid);
			version_grade_term_getname();
		}
	}
}


/** 
* version_grade_term_getname  
* 读取COOKIE配置的版本、年级、学期的名字
* @param 	void
* @return 	void
* @author   gm 
* @since    1.0 
*/  
function version_grade_term_getname()
{
	$sql="select * from engs_rms_dictionary where DICTIONARY_CODE='edition' and DETAIL_CODE='%s'";
	$engm_versionname = M()->query($sql, cookie('engm_versionid'));
	cookie('engm_versionname',$engm_versionname[0]["detail_name"]);
	$sql="select * from engs_rms_dictionary where DICTIONARY_CODE='grade' and DETAIL_CODE='%s'";
	$engm_gradename = M()->query($sql, cookie('engm_gradeid'));
	cookie('engm_gradename',$engm_gradename[0]["detail_name"]);
	$sql="select * from engs_rms_dictionary where DICTIONARY_CODE='volume' and DETAIL_CODE='%s'";
	$engm_termname = M()->query($sql, cookie('engm_termid'));
	cookie('engm_termname',$engm_termname[0]["detail_name"]);
}


function study_word_text_info()
{
	// $engm_readword = M("study") -> where(" wordflag=0 and studytype=1 and username='%s'",cookie("engm_username")) -> count();
	// if(empty($engm_readword)){
	// 	$engm_readword=0;
	// }
	// cookie('engm_readword_count',$engm_readword);
	// $engm_reciteword = M("study") -> where(" wordflag=2 and studytype=1 and username='%s'",cookie("engm_username")) -> count();
	// if(empty($engm_reciteword)){
	// 	$engm_reciteword=0;
	// }
	// cookie('engm_reciteword_count',$engm_reciteword);
 //    $engm_readtextword = M("study") -> where(" wordflag=0 and studytype=2 and username='%s'",cookie("engm_username")) -> count();
 //    if(empty($engm_readtextword)){
	// 	$engm_readtextword=0;
	// }
	// cookie('engm_readtextword',$engm_readtextword);
	// $engm_wordbook=M("word_book") -> where("userid='%s' and isuse=1",cookie("engm_username")) -> count();
	// if(empty($engm_wordbook)){
	// 	$engm_wordbook=0;
	// }
	cookie('engm_wordbook_count',$engm_wordbook);
	cookie('engm_record_count',$engm_readword+$engm_reciteword+$engm_readtextword);
}



function getuserinfo(){
	    //cookie('ut','dbbba8448af7a8d3adc9b1bc460f4aecb6b9793d648b0d388de66964795ea973');
	    //cookie('sso_url','http://ssozz.zzedu.net.cn');
        //header("Content-type:text/html;charset=utf-8");
	    
	    $logintype=$_GET["source"];
	    if(empty($logintype)){
	    	$logintype=cookie("logintype");
	    }else{
	    	cookie("logintype",$logintype);
	    }
	    
	    if($logintype=='web'){
	    	$ut=$_GET["ut"];
	    	if(empty($ut)){
	    		$ut=cookie("ut");
	    		if($ut != null){
					if(cookie('engm_ut')!=cookie('ut')){
						redirect('../../Home/Index/login');
					}
				}else{
					redirect('../../Home/Index/login');
				}
	    	}else{
	    		//$url="ssozz.zzedu.net.cn";
	    		$url="ssokw.czbanbantong.com";
	    		cookie('sso_url',$url);
	    		cookie('ut',$ut);
				$url=get_sso_ip($url);
				$url =$url.'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.$ut;
				if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
					$url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.$ut;
				}
				//$currentuserpic=cookie('engm_curruserpic');
				$json_ret = file_get_contents($url);
				//echo $json_ret;
			      $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
			     //	$json_ret = iconv("gb2312", "utf-8//IGNORE",$json_ret);
				$result = json_decode($json_ret, true);
				if($result['authFlg']=='1')
				{
					cookie("engm_ut",$ut);
					$ouUser=$result['ouUser'];
					$user=$result['user'];
					cookie("engm_username",$user['username']);
					cookie("engm_usertype",$user['usertype']);
					cookie("engm_areacode",$user['area']['areaId']);
					cookie("engm_truename",$user['truename']);
					cookie("schoolId",$user['school']["schoolId"]);
					$tms=str_replace('sso','tms',cookie('sso_url'));
					$tms=str_replace("http://","",$tms);
					$tmsurl=get_tms_ip($tms);
					$tmsurl = $tmsurl.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
					if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
						$tmsurl = 'http://218.28.177.164/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
					}
					$json_ret = file_get_contents($tmsurl);
					$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
					$studentinfo = json_decode($json_ret, true);
					if($studentinfo["result"]>0){
						cookie("engm_truename",$studentinfo['rtnArray'][0]['student']['realname']);
						cookie("engm_username",$studentinfo['rtnArray'][0]['student']['studentAccount']);
					}
					$classid= getClassId($tmsurl,cookie("engm_username"));
				}else{
					redirect('../../Home/Index/login');
			  	}
	    	}
	    	
	    }else{
	    	if(cookie('ut') != null){
			if(cookie('engm_ut')!=cookie('ut')){
				//areaCode=58.&studentId=360101100001266590&parentId=jz360101100001266590&ut=2e4f13e50a2154756d2a3321434c2d3b0457125db2080ac1bd7e790e265b27fc8a72a13821c95bce4b3975e97c8e95bc&s=0.837174294909407
				cookie('areaCode',null);
				cookie('studentId',null);
				cookie('parentId',null);
				cookie('engm_ut',null);
				cookie('localAreaCode',null);
				$areaCode=I('areaCode',0);
				if(empty($areaCode)){
					cookie('devcetype',"0");
				}else{
					cookie('devcetype',"1");
				}
				$url=str_replace("http://","",cookie('sso_url'));
				$arr=get_ip("",$url);
				//$url=cookie('sso_url');
				
				cookie("localAreaCode",$arr['tms']['areacode']);
				$url ="http://".$url.'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
				if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
					$url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
				}
				$currentuserpic=cookie('engm_curruserpic');
				$json_ret = file_get_contents($url);
				preg_match_all('/"name":"(.*?)"/', $json_ret, $tmp);
				$json_ret=str_replace($tmp[1][0], "", $json_ret);
				$truename=iconv("gbk", "UTF-8//IGNORE",$tmp[1][0]);
				cookie("engm_truename",$truename);
				//正则中文并且进行urlencode
				$json_ret = iconv("gbk", "UTF-8//IGNORE",$json_ret);
				//echo $json_ret;
				//$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
				$result = json_decode($json_ret, true);
				if($result['authFlg']=='1')
				{
					cookie("engm_ut",cookie('ut'));
					$ouUser=$result['ouUser'];
					$user=$result['user'];
					cookie("engm_username",$user['username']);
					cookie("engm_usertype",$user['usertype']);
					cookie("engm_areacode",$user['area']['areaId']);
					//cookie("engm_truename",$user['truename']);
					$tmsurl=str_replace('sso','tms',cookie('sso_url'));
					//$tms=str_replace("http://","",$tms);
					//$tmsurl=get_tms_ip($tms);
					
					$tmsurl = $tmsurl.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];

					if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
						$tmsurl = 'http://218.28.177.164/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
					}
					$json_ret = file_get_contents($tmsurl);
					preg_match_all('/"name":"(.*?)"/', $json_ret, $tmp);
					preg_match_all('/"realname":"(.*?)"/', $json_ret, $tmp);
					for($i=1;$i>=0;$i--){
						$value=$tmp[1][$i];
						$json_ret=str_replace($value, "", $json_ret);
						if($key==0){
						  $truename=iconv("gbk","utf-8//IGNORE",$value);
						  cookie("engm_truename",$truename);
						}
					}
	                $json_ret = iconv("gbk", "utf-8//IGNORE",$json_ret);//转码，（这里只是个例子）
					//$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
					$studentinfo = json_decode($json_ret, true);
					if($studentinfo["result"]>0){
						//cookie("engm_truename",$studentinfo['rtnArray'][0]['student']['realname']);
						cookie("engm_username",$studentinfo['rtnArray'][0]['student']['studentAccount']);
					}
					$classid= getClassId($tmsurl,cookie("engm_username"));
					cookie("engm_classid",$classid);
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
		
    }

function curl_get_contents($url,$timeout=1) { 
	$curlHandle = curl_init(); 
	curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
}



function getClassId($tms,$username){
	$classId = "0";
	$queryClassUrl = $tms."/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName=".$username;
	$json_ret = file_get_contents($queryClassUrl);
	$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
	$classinfo = json_decode($json_ret, true);
	if($classinfo['result'] > 0){
		$classId= $classinfo['rtnArray'][0]['classId'];
	}
	return $classId;
}
