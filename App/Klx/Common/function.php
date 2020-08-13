<?php
function get_sso_ip_by_title($domain){
		$model = M();
		$sql = "select title,remark,c1 from engs_dictionary where code='yjt' and remark='sso' order by id";
		$ssoresult = $model -> query($sql);
		foreach ($ssoresult as $key => $value) {
			$ssoipArray[$value['title']] = $value['c1'];
		}
	$ssourl = 'http://'.$ssoipArray[$domain];
	return $ssourl;
}
function get_tms_ip_by_title($domain){
		$model = M();
		$sql = "select title,c1 from engs_dictionary where code='yjt' and remark='tms' order by id";
		$tmsresult = $model -> query($sql);
		foreach ($tmsresult as $key => $value) {
			$tmsipArray[$value['title']] = $value['c1'];
		}
		$tmsurl = 'http://'.$tmsipArray[$domain];
		return $tmsurl;
}

 function getuserinfosKlx(){
    if(cookie('ut') != null){
      if(cookie('klx_ut')!=cookie('ut')){
        $sso = str_replace("http://","",cookie('sso_url'));

        $yjArr = get_ip('',$sso);

        $ssoip = $yjArr['sso']['c1'];
        $tmsip = $yjArr['tms']['c1'];
        $ubip = $yjArr['ub']['c1'];
        $ilearnip = $yjArr['ilearn']['c1'];
        $localAreaCode = $yjArr['sso']['c3'];

        cookie('ssoip', $ssoip);
        cookie('tmsip', $tmsip);
        cookie('ubip', $ubip);
        cookie('ilearnip', $ilearnip);
        cookie('localAreaCode', $localAreaCode);


        $ssoip = 'http://'.$ssoip;
        $tmsip = 'http://'.$tmsip;
        $ubip = 'http://'.$ubip;
        $ilearnip = 'http://'.$ilearnip;


        $url = $ssoip.'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');

        if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
          $url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
        }

        $json_ret = file_get_contents($url);


        $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gbk");//转码，（这里只是个例子）

        $result = json_decode($json_ret, true);

        if($result['authFlg']=='1')
        {
          cookie("klx_ut",cookie('ut'));
          $ouUser=$result['ouUser'];
          $user=$result['user'];

          cookie('areacode',$user['area']['areaId']);
          if($user['usertype']==0){
              //家长,请求接口获取对应学生信息

              cookie("usertype",$user['usertype']);


              $tmsurl = $tmsip.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];

              if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
                $tmsurl = 'http://218.28.177.164/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
              }

              $json_ret = file_get_contents($tmsurl);
              $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gbk");//转码，（这里只是个例子）
              $studentinfo = json_decode($json_ret, true);

              if($studentinfo['result'] > 0){
                cookie("truename",$studentinfo['rtnArray'][0]['student']['realname']);
                $userid=$studentinfo['rtnArray'][0]['student']['studentNumber'];
                cookie("username",$studentinfo['rtnArray'][0]['student']['studentNumber']);
                cookie("schoolId",$studentinfo['rtnArray'][0]['student']['schoolId']);
                cookie("classId",$studentinfo['rtnArray'][0]['student']['schoolClassId']);
                cookie("gradeCode",$studentinfo['rtnArray'][0]['student']['gradeCode']);

              }
              //echo cookie('username').'|'.cookie('truename');exit;
          }else{
              //学生
              $userid=$user['username'];
              cookie("username",$user['username']);
              cookie("schoolId",$user['schoolId']);
              cookie("classId",$user['classId']);
              cookie("gradeCode",$user['gradeCode']);
              cookie("usertype",$user['usertype']);
              cookie("truename",$user['truename']);
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



	function shareIlearn($url,$content){
        $classidList = cookie('classId');
        $username = cookie('username');
        $truename = cookie('truename');
        $schoolId = cookie('schoolId');
        $content = $content;
        $infourl =$url;
        $windowtype = 1;

        $shareurl = 'http://'.cookie('ilearnip').'/exportInterface/share.action';


        $ch = curl_init();
        $post_fields['classidList'] = $classidList;
        $post_fields['username'] = $username;
        $post_fields['truename'] = $truename;
        $post_fields['schoolId'] = $schoolId;
        $post_fields['says.content'] = $content;
        $post_fields['says.infourl'] = $url;
        $post_fields['says.windowtype'] = $windowtype;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $shareurl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $res = curl_exec($ch);
        curl_close($ch);
        //var_dump($res);
        $json = json_decode($res,true);
        //var_dump($json);

        if($json['text'] == '分享成功'){
        	$data['error'] = 0;
        }else{
        	$data['error'] =1;
        }

        exit(json_encode($data));
	}





function getuserinfo(){
  $ut=I("ut");
  $deviceType=I("deviceType");
  $areacode=I("areaCode");
  if(!empty($ut)){
    $ut=$ut;
  }else{
    $ut=cookie("ut");
  }
  //1c5cb7129e30d79af97f722e22559172a6f9f68daa7a141cede5c60c9b7a5460
  if($ut != null){
    if(cookie('jp_ut')!=cookie('ut')){
      cookie('ut',$ut);
      cookie('areaCode',$areacode);
      cookie('deviceType',$deviceType);
      //根据areacode查询sso地址
      $rs=M("yjt_url")->where("areacode='%s' and url_type='sso_url'",$areacode)->find();
      $sso=$rs["url"];
          $url="http://".$sso."/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=".cookie('ut');
          $json_ret = file_get_contents($url);
          $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
          $result = json_decode($json_ret, true);
          if($result['authFlg']=='1')
          {
              cookie("jp_ut",cookie('ut'));
              $ouUser=$result['ouUser'];
              $user=$result['user'];
              $grade=$user['grade'];
              $area=$user['area'];
              $school=$user['school'];
              $usertype=$user['usertype'];
              //如果是家长的话
              if($usertype==4){
                  $class=$user["schoolClasses"][0];
                  cookie("username",$user['username']);
                  cookie("usertype",$usertype);
                  cookie("areacode",$area['areaId']);
                  cookie("gradeid",$grade['gradeCode']);
                  cookie("truename",$user['truename']);
                  cookie("schoolid",$school['schoolId']);
                  cookie("classid",$class['classId']);
                  cookie("parentid","");
              }else{
                  $urlarr=get_ip("",$sso);
                  $tms=$urlarr["tms"]["c1"];
                  $tmsurl = "http://".$tms.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
                  $json_ret = file_get_contents($tmsurl);
                  $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
                  $studentinfo = json_decode($json_ret, true);
                  if($studentinfo["result"]>0){
                    cookie("parentid",$user['username']);
                      cookie("truename",$studentinfo['rtnArray'][0]['student']['realname']);
                      cookie("username",$studentinfo['rtnArray'][0]['student']['studentAccount']);
                      cookie("gradeid",$studentinfo['rtnArray'][0]['student']['gradeCode']);
                      cookie("usertype","0");
                      cookie("areacode",$area['areaId']);
                      cookie("schoolid",$studentinfo['rtnArray'][0]['student']['schoolId']);
                      cookie("classid",$studentinfo['rtnArray'][0]['student']['schoolClassId']);
                  }else{
                    redirect('http://www.czbanbantong.com');
                  }
              }
          }else{
            redirect('http://www.czbanbantong.com');
          }
    }
  }else{
    redirect('http://www.czbanbantong.com');
  }
}




function getResourcePath(){
	return PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';
}