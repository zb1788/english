<?php
//设置用户的模块单元的访问日志
function setUserUnitLog($moduleid,$schoolid,$classid,$username,$truename,$areacode,$ks_code,$subjectid,$ks_name){
	$userModulesUnitLog=M("user_modules_unit_log");
	$userModulesUnitLog->gradecode=cookie("gradeid");
	$userModulesUnitLog->username=cookie("username");
	$userModulesUnitLog->truename=cookie("truename");
	$userModulesUnitLog->areacode=cookie("areacode");
	$userModulesUnitLog->schoolid=cookie("schoolid");
	$userModulesUnitLog->classid=cookie("classid");
	$userModulesUnitLog->moduleid=$moduleid;
	$userModulesUnitLog->ks_code=$ks_code;
	$userModulesUnitLog->ks_name=$ks_name;
	$userModulesUnitLog->subjectid=$subjectid;
	$userModulesUnitLog->localareacode=cookie("localAreaCode");
	$userModulesUnitLog->usertype=cookie("usertype");
	$userModulesUnitLog->add();
}

function getUserConfig($subjectid,$gradeid,$termid,$versionid,$user){
	cookie("subjectid",$subjectid);
	cookie("gradeid",$gradeid);
	cookie("termid",$termid);
	cookie("versionid",$versionid);
	$zhigradeid=$gradeid;
	$zhisubjectid=$subjectid;
	$zhiversionid=$versionid;
	$zhitermid=$termid;
	$sql = "select * from engs_config t where t.userid = '".$user["username"]."' and t.usertype = '".$user["usertype"]."'";

	if(!empty($user["localareacode"])){
		$sql = $sql ." and t.localareacode='".$user["localareacode"]."'";;
	}

	if(!empty($gradeid)){
		$sql = $sql ." and t.gradeid='".$gradeid."'";
	}

	if(!empty($termid)){
		$sql = $sql ." and t.termid='".$termid."'";
	}

	if(!empty($subjectid)){
		$sql = $sql ." and t.subjectid='".$subjectid."'";
	}

	if(!empty($versionid)){
		$sql = $sql ." and t.versionid='".$versionid."'";
	}

	$sql = $sql ." order by t.id desc limit 1";
	$rs = M() -> query($sql);
	if(!empty($rs)){
		cookie("gradeid",$rs[0]["gradeid"]);
		cookie("subjectid",$rs[0]["subjectid"]);
		cookie("termid",$rs[0]["termid"]);
		cookie("versionid",$rs[0]["versionid"]);
		$zhiversionid = $rs[0]["versionid"];
		$zhitermid = $rs[0]["termid"];
		$zhigradeid = $rs[0]["gradeid"];
		$zhisubjectid = $rs[0]["subjectid"];
	}else{
		$verarr=getVersionByUserGrade(cookie("tms_url"),cookie("username"),cookie("gradeid"));
		if(!empty($verarr)){
			$subjectarr=$verarr[$subjectid];
			if(!$subjectarr){
				if($subjectid=='0003'){
					if($gradeid=="0007"||$gradeid=="0008"||$gradeid=="0009"){
						cookie("versionid",C('enversionid1'));
						$zhiversionid=C('enversionid1');
					}else if($gradeid=="0001"||$gradeid=="0002"){
						cookie("versionid",C('enversionid'));
						$zhiversionid=C('enversionid');
					}else{
						cookie("versionid",C('enversionid2'));
						$zhiversionid=C('enversionid2');
					}
					if($gradeid=="0009"){
						cookie("termid",C('termid1'));
						$zhitermid=C('termid1');
					}else{
						cookie("termid",C('termid'));
						$zhitermid=C('termid');
					}
				}else{
					cookie("versionid",C('cnversionid'));
					cookie("termid",C('termid'));
					$zhiversionid=C('cnversionid');
					$zhitermid=C('termid');
				}
			}else{
				if($subjectid=='0003'){
					if($gradeid=="0009"){
						cookie("termid",C('termid1'));
						$zhitermid=C('termid1');
					}else{
						cookie("termid",C('termid'));
						$zhitermid=C('termid');
					}
					$versionid=$subjectarr[cookie("termid")];
					if(empty($versionid)){
						if($gradeid=="0007"||$gradeid=="0008"||$gradeid=="0009"){
							cookie("versionid",C('enversionid1'));
							$zhiversionid=C('enversionid1');
						}else if($gradeid=="0001"||$gradeid=="0002"){
							cookie("versionid",C('enversionid'));
							$zhiversionid=C('enversionid');
						}else{
							cookie("versionid",C('enversionid2'));
							$zhiversionid=C('enversionid2');
						}
						if($gradeid=="0009"){
							cookie("termid",C('termid1'));
							$zhitermid=C('termid1');
						}else{
							cookie("termid",C('termid'));
							$zhitermid=C('termid');
						}
					}else{
						cookie("versionid",$versionid);
						$zhiversionid=$versionid;
					}
				}else{
					cookie("versionid",C('cnversionid'));
					cookie("termid",C('termid'));
					$zhiversionid=C('cnversionid');
					$zhitermid=C('termid');
				}

			}
		}else{
			if($subjectid=='0003'){
				if($gradeid=="0007"||$gradeid=="0008"||$gradeid=="0009"){
					cookie("versionid",C('enversionid1'));
					$zhiversionid=C('enversionid1');
				}else if($gradeid=="0001"||$gradeid=="0002"){
					cookie("versionid",C('enversionid'));
					$zhiversionid=C('enversionid');
				}else{
					cookie("versionid",C('enversionid2'));
					$zhiversionid=C('enversionid2');
				}
				if($gradeid=="0009"){
					cookie("termid",C('termid1'));
					$zhitermid=C('termid1');
				}else{
					cookie("termid",C('termid'));
					$zhitermid=C('termid');
				}
			}else{
				cookie("versionid",C('cnversionid'));
				cookie("termid",C('termid'));
				$zhiversionid=C('cnversionid');
				$zhitermid=C('termid');
			}
		}
		//向数据库中插入数据避免二次请求接口
		if(!empty($gradeid)){
			$data["versionid"]=$zhiversionid;
			$data["termid"]=$zhitermid;
			$data["gradeid"]=$zhigradeid;
			$data["subjectid"]=$zhisubjectid;
			$data["userid"]=$user["username"];
			$data["usertype"]=$user["usertype"];
			$data["localareacode"]=$user["localareacode"];
			$data["areacode"]=$user["areacode"];
			$data["configtime"]=Date("Y-m-d H:i:s");
			M("config")->add($data);
		}
	}
	$ret["termid"]=$zhitermid;
	$ret["versionid"]=$zhiversionid;
	$ret["gradeid"]=$zhigradeid;
	$ret["subjectid"]=$zhisubjectid;
	return $ret;
}



function get_sso_by_areacode($areacode){
	$sql="SELECT * FROM engs_yjt_url t WHERE t.`areacode`='".$areacode."'";
	$result=M()->query($sql);
	foreach($result as $key=>$value){
        $temp=$value['url_type'];
        //查找下划线
        $temps= explode("_", $temp);
        if(!empty($temp)){
             $arr[$temps[0]]=$value;
        }
    }
    return $arr;
}




//替换试卷中图片地址
function relace_tcontent($str){
    $resource_path = PROTOCOL."://".RESOURCE_DOMAIN.'/yylmp3/';
    $replacestr = str_replace('/uploads',$resource_path.'uploads',$str);
    return $replacestr;
}


function version_grade_term_set($versionid,$gradeid,$termid)
{
	//$DAO = M("");
	cookie('versionid',$versionid);
	cookie('gradeid',$gradeid);
	cookie('termid',$termid);
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
	$gradeid = cookie("gradeid");
	$versionid = cookie("versionid");
	$termid = cookie("termid");
	if ($gradeid || $versionid || $termid) {
		//读取数据库配置
		$versionid =  C("versionid");
		$gradeid =  C("gradeid");
		$termid =  C("termid");
		$username = cookie("username");
		if(!empty($username)){
			$rs=M("config")->where("userid='%s' and areacode='%s'",cookie('username'),cookie('areacode'))->field("gradeid,termid,versionid")->order("configtime desc")->find();
			if(!empty($rs)){
				$versionid=$rs["versionid"];
		        $gradeid = $rs["gradeid"];
		        $termid = $rs["termid"];
			}
		}
		cookie('versionid',$versionid);
		cookie('gradeid',$gradeid);
		cookie('termid',$termid);
		version_grade_term_getname();
	}
}

// function getmodule($moduleid){
// 	$json_string = file_get_contents('./public/public/json/module.json');
// 	$json_string = json_decode($json_string, true);
// 	$ret=array();
// 	foreach ($json_string as $key => $value) {
// 		if($value["id"]==$moduleid){
// 			$value["isapp"]=empty($value["apptrourl"])?"0":"1";
// 			$value["isrank"]=empty($value["rankinterface"])?"0":"1";
// 			$ret=$value;

// 			break;
// 		}
// 	}
//     $learnways=$ret;
//     return $learnways;
// }


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
	$sql="select * from rms_dictionary_detail where DICTIONARY_CODE='edition' and DETAIL_CODE='%s'";
	$engm_versionname = M()->query($sql, cookie('versionid'));
	cookie('versionname',$engm_versionname[0]["detail_name"]);
	$sql="select * from rms_dictionary_detail where DICTIONARY_CODE='grade' and DETAIL_CODE='%s'";
	$engm_gradename = M()->query($sql, cookie('gradeid'));
	cookie('gradename',$engm_gradename[0]["detail_name"]);
	$sql="select * from rms_dictionary_detail where DICTIONARY_CODE='volume' and DETAIL_CODE='%s'";
	$engm_termname = M()->query($sql, cookie('termid'));
	cookie('termname',$engm_termname[0]["detail_name"]);
}


function study_word_text_info()
{
	$engm_readword = M("study") -> where(" wordflag=0 and studytype=1 and username='%s'",cookie("username")) -> count();
	if(empty($engm_readword)){
		$engm_readword=0;
	}
	cookie('engm_readword_count',$engm_readword);
	$engm_reciteword = M("study") -> where(" wordflag=2 and studytype=1 and username='%s'",cookie("username")) -> count();
	if(empty($engm_reciteword)){
		$engm_reciteword=0;
	}
	cookie('engm_reciteword_count',$engm_reciteword);
    $engm_readtextword = M("study") -> where(" wordflag=0 and studytype=2 and username='%s'",cookie("username")) -> count();
    if(empty($engm_readtextword)){
		$engm_readtextword=0;
	}
	cookie('engm_readtextword',$engm_readtextword);
	$engm_wordbook=M("word_book") -> where("userid='%s'",cookie("username")) -> count();
	if(empty($engm_wordbook)){
		$engm_wordbook=0;
	}
	cookie('engm_wordbook_count',$engm_wordbook);
	cookie('engm_record_count',$engm_readword+$engm_reciteword+$engm_readtextword);
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
	$queryClassUrl = PROTOCOL."://".$tms."/tms/interface/querySchoolClass.jsp?queryType=byUserName&userName=".$username;
	$json_ret = get_content($queryClassUrl);
	$json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
	$classinfo = json_decode($json_ret, true);
	if($classinfo['result'] > 0){
		$classId= $classinfo['rtnArray'][0]['classId'];
	}
	return $classId;
}



//验证用户
function deep_in_array($value, $array) {
    foreach($array as $item) {
        if(!is_array($item)) {
            if ($item == $value) {
                return true;
            } else {
                continue;
            }
        }

        if(in_array($value, $item)) {
            return true;
        } else if(deep_in_array($value, $item)) {
            return true;
        }
    }
    return false;
}


//进行二维数组的排序
function sortt($data,$key) {
    if (count ( $data ) <= 1) {
      return $data;
    }
    $tem = $data [0][$key];
    $leftarray = array ();
    $rightarray = array ();
    for($i = 1; $i < count ( $data ); $i ++) {
        if ($data [$i][$key] <= $tem ) {
            $leftarray[] = $data[$i];
        } else {
            $rightarray[] = $data[$i];
        }
    }
    $leftarray=sortt($leftarray,$key,$type);
    $rightarray=sortt($rightarray,$key,$type);
    $sortarray = array_merge ( $leftarray, array ($data[0]), $rightarray );
    return $sortarray;
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string, $skey) {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value){$key < $strCount && $strArr[$key].=$value;}
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}
/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string, $skey) {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value){
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    }
    return base64_decode(join('', $strArr));
}

//判断手机类型返回类型0表示其他 1表示安卓 2表示苹果
function get_device_type()
{
	//全部变成小写字母
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$type =0;
	//分别进行判断
	if(strpos($agent,'iphone') || strpos($agent,'ipad'))
	{
		$type =2;
	}
	if(strpos($agent,'android'))
	{
		$type =1;
	}
	return $type;
}
//amr转mp3
function amrTrasfermp3($filename){
	//记录开始时间
	$starttime=Date("Y-m-d H:i:s");
	$path="/home/yylmp3/recordwav/";
	$yfilename=$filename;
	$patharr = pathinfo($yfilename);
	//获取扩展名称
	$extname=$patharr['extension'];
	$basename=basename($patharr['basename'],$extname);
	$outputname=$patharr['dirname'].'/'.$basename."mp3";
	$retrunname = $basename."mp3";
	exec("ffmpeg -i ".$path.$yfilename." ".$path.$outputname,$res, $rc);
	return $outputname;
  }
  function change_to_quotes($str) {
	return sprintf("'%s'", $str);
}
?>
