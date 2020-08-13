<?php
 //用户鉴权问题
   if(!function_exists("getuserinfos")){
    //用户鉴权问题
    function getuserinfos(){
        $request = $_GET;
    //    var_dump($_REQUEST);exit;
        $ut = $request["ut"];
        // echo $ut;
        $areaCode = empty($request["areaCode"])?$request["serAreaCode"]:$request["areaCode"];
        $cookie = $_COOKIE;
        $cookie_areaCode = $cookie["localAreaCode"];
        $cookie_ut = $cookie["ut"];
        //判断是否参数中需要的数据
        $ut = empty($ut)?$cookie_ut:$ut;
        $areaCode = empty($areaCode)?$cookie_areaCode:$areaCode;
       
        //传递返回参数
        if($request["backUrl"]){cookie("backUrl",$request["backUrl"]);}
            //判断两次UT是否相同
            $jp_ut = $cookie["jp_ut"];
            cookie("localAreaCode",$areaCode);
            cookie("ut",$ut);
            //如果是不相同的话
           
            if ($jp_ut != $ut) {
                //根据区域获取不同平台的IP
            //    echo $ut;exit;
                $areaData = S("areacode");
                if(!$areaData || empty($areaCode)){
                    $yjtAreaCode = M("db_english.yjt_url","engs_");
                    $result = $yjtAreaCode->select();
                    $ret = array();
                    foreach($result as $key=>$value){
                        $areacode = $value["areacode"];
                        $system = explode("_",$value["url_type"])[0];
                        $ret[$areacode][$system]["ip"] = $value["in_ip"];
                        $ret[$areacode][$system]["host"] = $value["url"];
                    }
                    S('areacode',$ret);
                    $areaData = $ret;
                }
                $areaData = $areaData[$areaCode];
                $sso = $areaData["sso"]["host"];
                $vfs = $areaData["vfs"]["host"];
                $tms = $areaData["tms"]["host"];
                $portal = $areaData["portal"]["host"];
                $tqms = $areaData["tqms"]["host"];
                $ub = $areaData["ub"]["host"];
                $pls = $areaData["pls"]["host"];
                $ubname = $areaData["ubname"];
                if(empty($ubname)){
                    $ubname = C("ubname");
                }else{
                    $ubname = $ubname["host"];
                }
                // var_dump($ubname);exit;
                //将所欲的地址全部写入cookie
                cookie("sso_url",$sso);
                cookie("vfs_url",$vfs);
                cookie("tqms_url",$tqms);
                cookie("tms_url",$tms);
                cookie("pls_url",$pls);
                cookie("portal_url",$portal);
                cookie("ubname",$ubname);
                cookie("ub_url",$ub);

                //判断缓存文件
                $userInfo = S("student_" . $ut);
              
                if (!empty($userInfo)) {
                    // var_dump("存在userInfo");exit;
                    $username = $userInfo["username"];
                    $usertype = $userInfo["usertype"];
                    $truename = $userInfo["truename"];
                    $areaname = $userInfo["areaname"];
                    $areaid = $userInfo["areaid"];
                    $localareacode = $userInfo["localareacode"];
                    $classid = $userInfo["classid"];
                    $classname = $userInfo["classname"];
                    $schoolid = $userInfo["schoolid"];
                    $schoolname = $userInfo["schoolname"];
                    $gradeid = $userInfo["gradeid"];
                    $userpic = $userInfo["userpic"];
                    $isparent = $userInfo["isparent"];
                    //写入cookie
                    cookie("userpic", $userpic);
                    cookie('usertype', $usertype);
                    cookie("areacode", $areaid);
                    cookie("fullname", $areaname);
                    cookie("schoolid", $schoolid);
                    cookie("schoolname", $schoolname);
                    cookie("classid", $classid);
                    cookie("classname", $classname);
                    cookie("usergradeid", $gradeid);
                    cookie("gradeid", $gradeid);
                    cookie("truename", $truename);
                    cookie("usertype", $usertype);
                    cookie("username", $username);
                    cookie("areaname", $areaname);
                    cookie("areaid", $areaid);
                    cookie("isparent", $isparent);
                    cookie("areacode", $areaid);
                    cookie("fullname", $areaname);
                    prduct_grant($sso,$username);
                } else {
                    
                    $localareacode = $areaCode;
                    $url = PROTOCOL . "://" . $sso . "/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=" . $ut;
                    // echo $url;exit;
                    $user_result = get_content($url,"usergrantinterface.log");
                    //解析中文出错问题
                    preg_match_all('/"name":"(.*?)"/', $user_result, $tmp);
                    $json_ret = str_replace($tmp[1][0], "", $user_result);
                    $truename = iconv("gbk", "UTF-8//IGNORE", $tmp[1][0]);
                    cookie("truename", $truename);
                    //正则中文并且进行urlencode
                    $json_ret = iconv("gbk", "UTF-8//IGNORE", $user_result);
                    $result = json_decode($json_ret, true);
                    // var_dump($result);exit;
                    writeAppLog("usergrantinterfacejsonparse".Date("Y-m-d").".log",json_encode($result));
                    if ($result['authFlg'] == '1') {
                        cookie("jp_ut", $ut);
                        $ouUser = $result['ouUser'];
                        $user = $result['user'];
                        $grade = $user['grade'];
                        $area = $user['area'];
                        $userpic = $user['headPhoto'];
                        if (!empty($userpic) && $userpic != "") {
                            $userpic = PROTOCOL . "://" . $vfs . "/tms" . $userpic . "?r=1213";
                        } else {
                            $userpic = PROTOCOL . "://" . $portal . "/space/images/default1.png";
                        }
                        $parentid = "";
                        $isparent = 0;
                        $areaid = $area['areaId'];
                        $areaname = $area['fullname'];
                        $school = $user['school'];
                        $schoolClasses = $user['schoolClasses'];
                        $usertype = $user['usertype'];
                        $username = $user["username"];
                        $schoolid = $school['schoolId'];
                        $schoolname = $school['schoolName'];
                        $classid = $schoolClasses[0]['classId'];
                        $classname = $schoolClasses[0]['className'];
                        $gradeid = $schoolClasses[0]['gradeCode'];
                        if (empty($gradeid)) {
                            $gradeid = "0001";
                        }
                        //写入cookie
                        cookie("userpic", $userpic);
                        cookie('usertype', $usertype);
                        cookie("areaid", $areaid);
                        cookie("areacode", $areaid);
                        cookie("fullname", $areaname);
                        cookie("areaname", $areaname);
                        cookie("schoolid", $schoolid);
                        cookie("schoolname", $schoolname);
                        cookie("classid", $classid);
                        cookie("classname", $classname);
                        cookie("usergradeid", $gradeid);
                        cookie("gradeid", $gradeid);
                       // echo $usertype;exit;
                        if ($usertype != 0 && $usertype != 4) {  //老师
                            cookie("username", $username);
                            cookie("usertype", "2");
                            cookie("parentid", $parentid);
                        } else if ($usertype == 4) {  //学生
                            cookie("username", $user['username']);
                            cookie("usertype", $usertype);
                            cookie("parentid", $parentid);
                        } else if ($usertype == 0) {   //家长
                            cookie("parentid", $username);
                            $truename = str_replace("的家长", "", $truename);
                            if(strpos($username,'jza') == true ||strpos($username,'jzb') == true ){
                                $username = substr($username, 3);
                            }
                            else{
                                $username = substr($username, 2);
                            }   
                            cookie("username", $username);
                            cookie("truename", $truename);
                            cookie("usertype", 4);
                            $usertype == 4;
                            $isparent = 1;
                        }
                           prduct_grant($sso,$username);
                        //比对查看数据是否相同如果不相同就进行数据库的插入
                            $ret["username"] = $username;
                            $ret["usertype"] = $usertype;
                            $ret["truename"] = $truename;
                            $ret["areaname"] = $areaname;
                            $ret["areaid"] = $areaid;
                            $ret["areacode"] = $areaid;
                            $ret["localareacode"] = $localareacode;
                            $ret["classid"] = $classid;
                            $ret["classname"] = $classname;
                            $ret["schoolClasses"] = $schoolClasses;
                            $ret["schoolid"] = $schoolid;
                            $ret["schoolname"] = $schoolname;
                            $ret["gradeid"] = $gradeid;
                            $ret["userpic"] = $userpic;
                            $ret["isparent"] = $isparent;
                            S("student_".$ut,$ret);
                            $md5key = md5(json_encode($ret));
                            $usermodel = M("db_english.userinfo","engs_");
                            $userrs = $usermodel -> where("username = '%s' and usertype = %d",$username,$usertype) -> find();
                            if(empty($userrs) || $userrs["md5key"] != $md5key){
                                //直接插入数据库
                                $usermodel -> where("username = '%s' and usertype = %d",$username,$usertype) -> setField("isdel",1);
                                $data["username"]=$username;
                                $data["truename"]=$truename;
                                $data["areaid"]=$areaid;
                                $data["areaname"]=$areaname;
                                $data["usertype"]=$usertype;
                                $data["localareacode"]=$localareacode;
                                $data["classid"]=$classid;
                                $data["classname"]=$classname;
                                $data["schoolid"]=$schoolid;
                                $data["schoolname"]=$schoolname;
                                $data["gradeid"]=$gradeid;
                                $data["isparent"]=$isparent;
                                $data["parentid"]=$parentid;
                                $data["userpic"]=$userpic;
                                $data["md5key"]=$md5key;
                                try{
                                    $usermodel->add($data);
                                }catch(Exception $e){
                                    redirect('../../../Error/Index/fail');
                                }
                            }  
                    }
                }
            }
            else{
                $prductusername = cookie("prductusername");
                $sso =  cookie("sso_url");
                // echo $sso;
                // echo $prductusername;exit;
                prduct_grant($sso,$prductusername);
            }
        
    }
}

//是否包含数字
function array_is_exists($value,$arr){
	return in_array($value, $arr);
}
//组合树形结构
function array_to_tree($arr,$column,$pcolumn,$name){
	//print_r($arr);exit;
	$result = array();
	foreach($arr as $key=>$value){
		if(empty($result[$value[$column]])){
			$result[$value[$column]]=$value;
			$result[$value[$column]]["children"] = array();
		}
		//if(!empty($value[$pcolumn])){
			$value[$name] = $value[$pcolumn];
			array_push($result[$value[$column]]["children"], $value);
		//}
	}
	$ret = array();
	foreach($result as $key=>$value){
		array_push($ret,$value);
	}
	return $ret;
}

function array_to_tree_more($arr,$first,$sender,$name){
	$result = [];
	foreach($arr as $key=>$value){
		if(empty($result[$value["ks_id"]])){
			$value["name"]=$value["ks_name"];
			$result[$value["ks_id"]]=$value;
			$result[$value["ks_id"]]["children"] = array();
			if(!empty($value["chapterid"])){
				$value["name"] = $value["chapter"];
				$result[$value["ks_id"]]["children"][$value["chapterid"]]=$value;
				$result[$value["ks_id"]]["count"]=1;
				$result[$value["ks_id"]]["children"][$value["chapterid"]]["children"]=array();
				//同时将他的孩子也加进去
				if(!empty($value["id"])){
					$result[$value["ks_id"]]["children"][$value["chapterid"]]["children"][$value["id"]]=$value;
					$result[$value["ks_id"]]["children"][$value["chapterid"]]["count"]=1;
				}
			}
		}else{
			if(!empty($value["chapterid"])){
				if(empty($result[$value["ks_id"]]["children"][$value["chapterid"]])){
					$value["name"] = $value["chapter"];
					$result[$value["ks_id"]]["children"][$value["chapterid"]]=$value;
					$result[$value["ks_id"]]["count"]=$result[$value["ks_id"]]["count"]+1;
					$result[$value["ks_id"]]["children"][$value["chapterid"]]["children"]=array();
					//同时将他的孩子也加进去
					if(!empty($value["id"])){
						$result[$value["ks_id"]]["children"][$value["chapterid"]]["children"][$value["id"]]=$value;
						$result[$value["ks_id"]]["children"][$value["chapterid"]]["count"]=$result[$value["ks_id"]]["children"][$value["chapterid"]]["count"]+1;
					}
				}else{
					//同时将他的孩子也加进去
					if(!empty($value["id"])){
						$result[$value["ks_id"]]["children"][$value["chapterid"]]["children"][$value["id"]]=$value;
						$result[$value["ks_id"]]["children"][$value["chapterid"]]["count"]=$result[$value["ks_id"]]["children"][$value["chapterid"]]["count"]+1;
					}
				}
			}
		}
	}
	return $result;
}

//获取数据中固定的列
function arr_column_more($arr,$columns){
	$ret = array();
	foreach($arr as $key=>$value){
		if(array_is_exists($key,$columns)){
			$ret[$key]=$value;
		}
	}
	return $ret;
}

/*定义全局公共函数的地方*/
function get_sso_ip($domain,$remark){
		$model = M();
		//$sql="SELECT title,c1 FROM engs_dictionary t WHERE CODE='yjt' AND c3 IN (SELECT c3 FROM engs_dictionary s WHERE s.`title` ='".$domain."') AND remark='".."'"
		$sql = "select title,c1 from engs_dictionary where code='yjt' and remark='sso' order by id";
		$ssoresult = $model -> query($sql);
		foreach ($ssoresult as $key => $value) {
			$ssoipArray[$value['title']] = $value['c1'];
		}
	$ssourl = 'http://'.$ssoipArray[$domain];
	return $ssourl;
}
function get_tms_ip($domain){
		$model = M();
		$sql = "select title,c1 from engs_dictionary where code='yjt' and remark='tms' order by id";
		$tmsresult = $model -> query($sql);
		foreach ($tmsresult as $key => $value) {
			$tmsipArray[$value['title']] = $value['c1'];
		}
		$tmsurl = 'http://'.$tmsipArray[$domain];
		return $tmsurl;
}
// function get_content($url){
// 	$ch = curl_init();
//     $timeout = 5;
//     curl_setopt($ch,CURLOPT_URL,$url);
//     curl_setopt($curl, CURLOPT_HEADER, 0);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
//    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
// 	$content = curl_exec($ch);
// 	curl_close($ch);
// 	return $content;
// }
function get_content($url,$filename = "interface.log"){
	$ch = curl_init();
  $timeout = 5;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	$content = curl_exec($ch);
	$ret["requestinfo"] = curl_getinfo($ch);
	$errorCode = curl_errno($ch);
	$ret["errorinfo"] = ($content === false)?curl_error($ch):"";
	curl_close($ch);
	//写日志每一个生成的日志
	$ret["url"] = $url;
	if($content === false || $content == "false"){
		$ret["result"] = "SSO认证异常";
	}
	else{
		if($errorCode != '0'){
			$ret["result"] = "SSO认证异常";
		}
		else{
			$ret["result"] = iconv("gbk", "UTF-8//IGNORE",$content);
			$post = stripslashes($ret["result"]);
		    $post = rtrim($post, '"');
		    $post = ltrim($post, '"');
		    $ret["result"] = json_decode($post, true);
		    if($ret["result"] == null || $ret["result"] =="null"){
		    	$ret["result"] = "json_decode失败";
		    }
		}
	}
	$ret["status"] = ($content === false)?"error":"ok";
	$time = time();
	$json[$time] = $ret;
	//writeAppLog($filename,json_encode($json,JSON_UNESCAPED_SLASHES));
	writeAppLog($filename,stripslashes(json_encode($json,JSON_UNESCAPED_SLASHES)));

	
	//writeAppLog($filename,$content);
	return $content;
}

/**
 * 获取当前省份的所有系统的ip和区域信息，两个参数至少传一个
 * @param  [string] $area   [区域id]
 * @param  [string] $domain [域名]
 * @return [arr]         [数组]
 */
function get_ip($area,$domain){
    $model = M();
    if(INSTALL==0){
    	$sql="SELECT areacode,url_type,url as title,url as c1,areacode as c3,url as domain FROM db_english.engs_yjt_url s  WHERE s.`areacode` IN (SELECT areacode FROM db_english.engs_yjt_url t WHERE  url='".$domain."' OR areacode='".$area."' ) order by areacode desc";
    }else{
    	$sql="SELECT areacode,url_type,url as title,url as c1,areacode as c3,url as domain FROM db_english.engs_yjt_url s  WHERE s.`areacode` IN (SELECT areacode FROM db_english.engs_yjt_url t WHERE  url='".$domain."' OR areacode='".$area."' ) order by areacode desc";
    }
   
		$result = $model -> query($sql);

    foreach($result as $key=>$value){
        $temp=$value['url_type'];
        //查找下划线
        $temps= explode("_", $temp);
        if(!empty($temp)){
             $arr[$temps[0]]=$value;
        }
    }
    //var_dump($result);
    return $arr;
}

/**
    * get_choice_list
    * 获取选择题的选项
    *
    */
function get_choice_list($wordid){
    $word=M("word");
    $rs=$word -> where("id=%d",$wordid) -> field("ks_code,word,base_wordid") -> find();
    $id=$rs['wordid'];
    $tag=$rs["tag"];
    $words=$rs["word"];
    $subword=substr($words,0,1);
    //dump($subword);
    $Model = new \Think\Model();
	$sql="select t.word,p.explains,p.morphology,s.ukmark,s.usmark,s.ukmp3,s.usmp3 from engs_word t,";
	$sql=$sql."engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.isdel=1 and t.id!=".$wordid." and t.ks_code='".$rs["ks_code"]."' order by rand()";
	$choicelist=$Model->query($sql);
	//查询本单元下的所有的单词作为选项
    if(count($choicelist)<3){
        unset($sql);
				$sql="select t.word,p.explains,p.morphology,s.ukmark,s.usmark,s.ukmp3,s.usmp3 from engs_word t,";
				$sql=$sql."engs_base_word s,engs_base_word_explains p where t.base_wordid=s.id and t.base_explainsid=p.id and t.isdel=1 and t.word not like '%".$rs["word"]."%' order by rand() limit 3";
        $choicelist1 = $Model->query($sql);
        $scount=3-count($choicelist);
        for($i=0;$i<$scount;$i++){
            array_push($choicelist,$choicelist1[$i]);
        } 
    }
    return $choicelist;
}


function getVersion($username,$tmsurl){
	$url=PROTOCOL."://".$tmsurl."/tms/interface/queryVersion.jsp?currentTermOnly=1&queryType=byUser&username=".$username;
	$json=get_content($url);
	$json_ret = mb_convert_encoding($json, "UTF-8", "gbk");//转码，（这里只是个例子）
	$data = json_decode($json_ret, true);
	$ret["success"]=0;
	$arr=array();
	if(empty($data["error"])){
		$data=$data["rtnArray"];
		$ret["success"]=1;
		foreach($data as $key=>$obj){
			$temp["termid"]=$obj["termCode"];
			$temp["versionid"]=$obj["versionCode"];
			$temp["subjectid"]=$obj["subjectCode"];
			$temp["version"]=$obj["grade"].".".$obj["term"].".".$obj["subject"].".".$obj["version"];
			$arr[$obj["subjectCode"]]=$temp;
		}
	}
	$ret["data"]=$arr;
	return $ret;
}


function deterVersion($version){
	$arr=explode(".",$version);
	if($arr[0]>3){
		return true;
	}else if($arr[0]==3){
		if($ar[1]>=9){
			return true;
		}else{
			return false;
		}
	}else if($arr[0]<3){
		return false;
	}
}

//超时限制
function getVersionByUserGrade($tms,$username,$gradeid,$encode='UTF8'){
	$context = stream_context_create(array(
		'http' => array(
			'timeout' => 5 //超时时间，单位为秒
		)
	));
	$start=Date("Y-m-d H:i:s");
	$url=PROTOCOL."://".$tms."/tms/interface/queryVersion.jsp?reqEncoding=".$encode."&queryType=byUser&username=".$username."&jsoncallback=jQuery1111002278933371705283_1506061688260&gradeCodes=".$gradeid."&_=1506061688262";
	$json=get_content($url,0,$context);
	$end=Date("Y-m-d H:i:s");
	file_put_contents("./log/userverinterface.log",$url.":".($end-$start).PHP_EOL,FILE_APPEND);
	if($json){
		$json_ret = mb_convert_encoding($json, "UTF-8", "gbk");//转码，（这里只是个例子）
		$json_ret=str_replace("jQuery1111002278933371705283_1506061688260(", "", $json_ret);
		$json_ret=str_replace(")", "", $json_ret);
		$data = json_decode($json_ret, true);
		//进行json的封装
		$verarr=array();
		foreach($data["rtnArray"] as $key=>$value){
			if(!array_key_exists($value["subjectCode"],$verarr)){
				$verarr[$value["subjectCode"]]=array();
			}
			$verarr[$value["subjectCode"]][$value["termCode"]]=$value["versionCode"];
		}
	}else{
		$verarr=array();
	}
	return $verarr;
}

function getUbiInterface($c,$n,$tusername,$tlocalAreaCode,$tareaid,$at='u'){
	$n = 1;
	$ad = time() . mt_rand(1000,9999);  //唯一值
	$at = $at;    //j（代表）、u（代表优币）、z（代表直接扣除或者增加多少优币）
	$a = $tusername;
	$ac = $tareaid;

	if(empty($a)){
		$a = cookie('username');  //帐号
	}
	if(empty($tlocalAreaCode) || $tlocalAreaCode == "my"){

		$tlocalAreaCode = cookie("localAreaCode");
	}
	if(empty($tareaid) || $tareaid == "my"){
		$ac = cookie('areacode'); //区域(不是LocalAreaCode)
	}
	$ut = cookie('usertype'); //帐号类型
	$urldata = get_ip($tlocalAreaCode,"");
	$ub_title = $urldata["ub"]["title"];
	$t = date('Y-m-d H:i:s');
	$r = 'ss';
	$i = $c;
	if($ut == "0"){  //家长
		if(strpos($a,"jz") === false){
			$a = "jz".$a;
		}
	}
	$parm = '{"ad":"'.$ad.'","at":"u","a":"'.$a.'","ut":"'.$ut.'","c":"'.$c.'","ac":"'.$ac.'","n":"'.$n.'","t":"'.$t.'","r":"'.$r.'","i":"'.$i.'"}';
	
	if( stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true
		|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	
	$uburl = $protocol.'://'.$ub_title;
	$uburl=$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);
	$uburl=str_replace('%7B', '{', $uburl);
	$uburl=str_replace('%7D', '}', $uburl);

	$curl = curl_init($uburl);
	//echo $uburl;exit;
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl, CURLOPT_TIMEOUT,5);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);
	//$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组
	$result = json_decode($responseText, true);
	// var_dump($result);exit;
	$issuc=$result[0]['result'];
	if($issuc==1){
	// echo '添加成功';
		$data = true;
	}else{
		$data = false;
		file_put_contents("./log/ub.log",$uburl."|".$responseText.PHP_EOL,FILE_APPEND);
	// echo '添加失败';
	}
	return $result;
}


function getmodule($moduleid = ""){
	$json_string = file_get_contents('./public/public/json/module.json');
	$json_string = json_decode($json_string, true);
	$ret=array();
	if(empty($moduleid)){
		return $json_string;
	}
	foreach ($json_string as $key => $value) {
		if($value["id"]==$moduleid){
			$value["isapp"]=empty($value["apptrourl"])?"0":"1";
			$value["isrank"]=empty($value["rankinterface"])?"0":"1";
			$ret=$value;

			break;
		}
	}
    $learnways=$ret;
    return $learnways;
}

function getClientIp($type = 0) {  
    $type       =  $type ? 1 : 0;  
    static $ip  =   NULL;  
    if ($ip !== NULL) return $ip[$type];  
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);  
        $pos    =   array_search('unknown',$arr);  
        if(false !== $pos) unset($arr[$pos]);  
        $ip     =   trim($arr[0]);  
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {  
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];  
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {  
        $ip     =   $_SERVER['REMOTE_ADDR'];  
    }  
    // IP地址合法验证  
    $long = sprintf("%u",ip2long($ip));  
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);  
    return $ip[$type];  
}  

function smartstudy($filename,$text,$texttype=0,$file=""){
	// $filename = "2019/07/03/c17f4215a135929386bd3a513d61e4e0-mp3to44100.mp3";
	// $text = "father";
    $user_record_path = C('user_record_path');
	$filepath = "/home/yylmp3/recordwav/".$filename;
	$video_info = getVideoInfo($filepath);
	$acodec = $video_info['acodec'];
	$acodecarr = explode('mp4a',$acodec);
	
	if(count($acodecarr) > 1){
		$filename = EvaluateTrasfermp3($filename);
		//echo $filename;
	}
	$texttype = $texttype;
	Vendor('Audio');
	$audio = new \Audio();
	$username = cookie("username");
	//echo $username;
	$ip = getClientIp();
	//$ip = "127.0.0.1";
	$arr = explode('.',$filename);
	$extension = $arr[1];
	if($extension == "amr"){
		$sampleRate = 8000;
	}
	else{
		$sampleRate = 44100;
	}
	if($video_info['asamplerate'] == 8000 && $extension == "mp3"){
       $filename = change_sampleRate($filename,"44100");
    }
	$evaluate_params = array("refText" => $text,"precision" => 0.8,"rank" => 100,"tokenId" => "default");
	$user_info = array();
	$audio_info = array("sampleRate"=>$sampleRate,"audioType" =>$extension);
	$filename = APP_ROOT."/recordwav/".$filename;
	//echo $filename;exit;
	$ret = $audio -> speakEvaluate($texttype,$evaluate_params,$user_info,$audio_info,$filename);
	writeAppLog("smartstudy".Date("Y-m-d").".log","filename=".$filename."||text=".$text."||".json_encode($ret));
  	return $ret;
}
//返回http
 function getHttpUrl($url){
 	$url = str_replace("https","http",$url);
   //$url = str_replace(":8081",":8080",$url);
   $url = str_replace(":8443",":8080",$url);
    return $url;
 }


function treeData($data,$pid = 0){
    $result = array();
    foreach($data as $key=>$v){
        if($v['parentid'] == $pid){
            $v['children'] = treeData($data,$v['id']);
            $result[] = $v;
        }
    }
    return $result;
}


function get_text_mp3(&$item){
	$item["mp3"] = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/".substr($item["mp3"], 0,2)."/".$item["mp3"].".mp3";
	$item["mp3"] = getHttpUrl($item["mp3"]);
}


function writeAppLog($filename,$content){
	file_put_contents("./log/".$filename,$content.PHP_EOL,FILE_APPEND);
}


function speak(){
	Vendor('Audio');
	$audio = new \Audio();
	$evaluate_params = array("refText" => "Welcome","precision" => 0.8,"rank" => 100,"tokenId" => "default");
    $user_info = array("user_id" => "vcom","user_client_ip"=>"127.0.0.1","dummy_info" => "content");
	$ret = $audio -> speakEvaluate(0,$evaluate_params,$user_info);
	return $ret;
}
/**获取音视频属性信息 */
function getVideoInfo($file){
	//define('FFMPEG_CMD','D:\ffmpeg\bin\ffmpeg.exe -i "%s" 2>&1');
	define('FFMPEG_CMD','ffmpeg -i "%s" 2>&1');
	ob_start();
	
	passthru(sprintf(FFMPEG_CMD, $file));
	//echo sprintf(FFMPEG_CMD, $file);
	$video_info = ob_get_contents();
	ob_end_clean();
	//echo $video_info;
	// 使用输出缓冲，获取ffmpeg所有输出内容
	$ret = array();
	
	// Duration: 00:33:42.64, start: 0.000000, bitrate: 152 kb/s
	if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $video_info, $matches)){
	 $ret['duration'] = $matches[1]; // 视频长度
	 $duration = explode(':', $matches[1]);
	 $ret['seconds'] = $duration[0]*3600 + $duration[1]*60 + $duration[2]; // 转为秒数
	 $ret['start'] = $matches[2]; // 开始时间
	 $ret['bitrate'] = $matches[3]; // bitrate 码率 单位kb
	}
	
	// Stream #0:1: Video: rv20 (RV20 / 0x30325652), yuv420p, 352x288, 117 kb/s, 15 fps, 15 tbr, 1k tbn, 1k tbc
	if(preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $video_info, $matches)){
	 $ret['vcodec'] = $matches[1];  // 编码格式
	 $ret['vformat'] = $matches[2]; // 视频格式
	 $ret['resolution'] = $matches[3]; // 分辨率
	 list($width, $height) = explode('x', $matches[3]);
	 $ret['width'] = $width;
	 $ret['height'] = $height;
	}
	
	// Stream #0:0: Audio: cook (cook / 0x6B6F6F63), 22050 Hz, stereo, fltp, 32 kb/s
	if(preg_match("/Audio: (.*), (\d*) Hz/", $video_info, $matches)){
	 $ret['acodec'] = $matches[1];  // 音频编码
	 $ret['asamplerate'] = $matches[2]; // 音频采样频率
	}
	
	if(isset($ret['seconds']) && isset($ret['start'])){
	 $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间
	}
	
	$ret['size'] = filesize($file); // 视频文件大小
	
	return $ret;
	
   }
   /**先声测评音频文件不支持时进行转码 */
   function EvaluateTrasfermp3($filename){
	//记录开始时间
	$starttime=Date("Y-m-d H:i:s");
	$path="/home/yylmp3/recordwav/";
	$yfilename=$filename;
	$patharr = pathinfo($yfilename);
	//获取扩展名称
	$extname=$patharr['extension'];
	$basename=basename($patharr['basename'],".".$extname);
	$newname =$basename."-mp4atomp3";
	if($patharr['dirname'] == "."){
			$outputname= $newname.".mp3";

	}
	else{
			$outputname=$patharr['dirname'].'/'.$newname.".mp3";
	}
	//IOS录出来的mp4a格式双声道强制转化为单声道
	exec("ffmpeg -i ".$path.$yfilename." -ac 1 ".$path.$outputname,$res, $rc);
	return $outputname;
  }

  /**先声测评音频文件不支持时进行转码 */
   function change_sampleRate($filename,$sampleRate){
	//记录开始时间
	if(empty($sampleRate)){
		$sampleRate = "44100";
	}
	$starttime=Date("Y-m-d H:i:s");
	$path="/home/yylmp3/recordwav/";
	$yfilename=$filename;
	$patharr = pathinfo($yfilename);
	//获取扩展名称
	$extname=$patharr['extension'];
	$basename=basename($patharr['basename'],".".$extname);
	$newname =$basename."-mp3to".$sampleRate;
	if($patharr['dirname'] == "."){
			$outputname= $newname.".mp3";

	}
	else{
			$outputname=$patharr['dirname'].'/'.$newname.".mp3";
	}
	//IOS录出来的mp4a格式双声道强制转化为单声道
	exec("ffmpeg -i ".$path.$yfilename." -ar ".$sampleRate."  -ac 1 ".$path.$outputname,$res, $rc);
	return $outputname;
  }
  function object_array($array) {  
	if(is_object($array)) {  
		$array = (array)$array;  
	 } if(is_array($array)) {  
		 foreach($array as $key=>$value) {  
			 $array[$key] = object_array($value);  
			 }  
	 }  
	 return $array;  
}

/**
 * 分割字符串(支持中英文混合)
 * @param  string $string  [源字符串]
 * @param  string $type    [返回值的类型:array|string]
 * @param  string $charset [源字符串编码,默认utf-8]
 * @return [type]          [description]
 */
function split_str($string, $type='array', $charset='utf-8'){
  //通过ord()函数获取字符的ASCII码值，如果返回值大于 127则表示为中文字符的一半，再获取后一半组合成一个完整字符
    $flag = false;
    if(strtolower($charset) == 'utf-8'){
      //如果utf-8环境
      $flag = true;
      $string = iconv('utf-8', 'gbk', $string);//由于ord函数是在gbk下才能使用,所以如果是中文先转化为gbk再处理
    }

    if(strtolower($charset) != 'gbk' && strtolower($charset) != 'utf-8')
    {
      exit('参数不合法!');
    }

    //把字符串转化为ascii码存入数组,如果是中文是由两个ASCII码组成，英文是一个
    $length = strlen($string);
    $result = array();
    for($i=0; $i<$length; $i++){
        if(ord($string[$i])>127){
          $result[] = ord($string[$i]).' '.ord($string[++$i]);
        }else{
            $result[] = ord($string[$i]);
        }
    }

    if(strtolower($type) == 'array'){
      //如果返回值要数组
      $str = '';
      foreach($result as $v){
          if(empty(strstr($v,' '))){
              $tmpstr = chr($v);
              if($flag){
                $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
              }
              $data[] = $tmpstr;
          }else{
              list($a,$b) = explode(' ',$v);
              $tmpstr = chr($a).chr($b);
              if($flag){
                $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
              }
              $data[] = $tmpstr;
          }
      }
      return $data;
    }elseif(strtolower($type) == 'string'){
      $data = array();
      foreach($result as $v){
          if(empty(strstr($v,' '))){
              $str .= chr($v);
          }else{
              list($a,$b) = explode(' ',$v);
              $str .= chr($a).chr($b);
          }
      }
      $str=iconv('gbk', 'utf-8', $str);
      return $str;
    }else{
      exit('参数不合法！');
    }
}
//智学助手子产品鉴权
function prduct_grant($sso,$username){
     cookie("prductusername",$username);
     $redisClient = new \Homework\Service\RedisService();
     $prductresult = $redisClient->get($username."_prduct");
     
     if(empty($prductresult)){
        $url = PROTOCOL . "://" . $sso . "/sso/pdGrant?username=".$username."&appFlg=eng-zxzs";
        
        $prductresult = get_content($url,"userprductresult.log");
        $prductresult = iconv("gbk", "UTF-8//IGNORE", $prductresult);
        $prductresult = json_decode($prductresult, true);
        $redisClient->add($username."_prduct",serialize($prductresult));
     }
}

//判断栏目是否有权限
function is_module_grant($moduleid){
        $prductusername = cookie("prductusername");
        $redisClient = new \Homework\Service\RedisService();
        $prductresult = $redisClient->get($prductusername."_prduct");
       // $prductresult = '{"appFlg":"uxin","appIdNameMap":{"42.01":"作业","42.02":"成绩","42.03":"同步学习"},"appIdNumberMap":{"42.01":"uxin-p-jxyy-xxrw","42.02":"uxin-jxyy-cj","42.03":"uxin-p-jxyy-tbfx"},"appNumbersNoGrant":["eng-zxzs-t-dcxx-1","eng-zxzs-t-kwld-10","eng-zxzs-t-fjyd-2"],"reasonUrl":"","tip":"尊敬的用户您好！您所享受的试用期服务未包含该项业务；如需使用，请进行相关业务订购。感谢您的关注，期待为您提供更多优质教育服务！","userType":"4","username":"111910352894"}';
        if(!empty($prductresult)){
           $prductresult = unserialize($prductresult);
           //var_dump($prductresult);exit;
           cookie("granttip",$prductresult["tip"]);
           cookie("grantusertype",$prductresult["userType"]);
          // cookie("reasonUrl",$prductresult["reasonUrl"]);
           $appNumbersNoGrant = $prductresult["appNumbersNoGrant"];
           foreach ($appNumbersNoGrant as $key => $value) {
            //echo $value;
                $NoGrantIdArr = explode("-",$value);
               // echo $NoGrantIdArr[4];
                 if(count($NoGrantIdArr) == 5){
                     if($moduleid == $NoGrantIdArr[4]){
                        redirect('../../../Error/Index/grant');
                        exit;
                     }
                 }
           }
           
           //var_dump($appNumbersNoGrant);exit;
        }

}