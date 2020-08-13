<?php
namespace Klx\Controller;
use Think\Controller;

class KlxszmbController extends Controller {
    public function index(){
    	//$userid='01788';
    	//$userarea='1.1';
      //   cookie('ut','59c0d8cb18c7318d86b03f7c59378ef188931c34a2b92fa939b1d3c6c17510c4a307df18354dd3ea');
      // cookie('sso_url','http://ssozz.zzedu.net.cn');
//echo "sss";exit;
       getuserinfos();
    	$userid=cookie('username');
    	$userarea=cookie('areacode');
    	$kid=$this->checkUser($userid, $userarea);

    	//$kid=56;
	    $m=M();
    	if ($kid==0){
	    	$sql='SELECT DISTINCT n.banben FROM hl_kecheng m RIGHT JOIN hl_ziversion n ON m.versionid=n.id WHERE m.nianji="一年级" AND m.xueqi="上学期" ORDER BY n.sortid';
	    	$data_version=$m->query($sql);
	    	$sql_first='select id from hl_ziversion where banben="%s"';
	    	$data_first=$m->query($sql_first,$data_version[0]['banben']);
			$data=M('kecheng')->where('nianji="一年级" and xueqi="上学期" and versionid="%d" and url is not null',$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
			$this->assign('banben_now',$data_version[0]['banben']);
    	}else {
    		$sql_user='SELECT m.nianji,m.xueqi,n.banben,m.versionid FROM hl_kecheng m,hl_ziversion n WHERE m.versionid=n.id AND m.id="%d"';
    		$data_user=$m->query($sql_user,$kid);
    		$data_user=$data_user[0];

    		$sql='SELECT DISTINCT n.banben FROM hl_kecheng m RIGHT JOIN hl_ziversion n ON m.versionid=n.id WHERE m.nianji="%s" AND m.xueqi="%s" ORDER BY n.sortid';
    		$data_version=M('kecheng')->query($sql,$data_user['nianji'],$data_user['xueqi']);

    		$data=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$data_user['nianji'],$data_user['xueqi'],$data_user['versionid'])->field('id,kecheng,url')->order('sortid')->select();
    		if ($data_user['nianji']=='一年级'){
    			$nianji='yinianji';
    		}elseif ($data_user['nianji']=='二年级'){
    			$nianji='ernianji';
    		}else {
    			$nianji='sannianji';
    		}
    		if ($data_user['xueqi']=='上学期'){
    			$xueqi='sxq';
    		}else {
    			$xueqi='xxq';
    		}
    		$this->assign('nianji',$nianji);
    		$this->assign('xueqi',$xueqi);
    		$this->assign('banben_now',$data_user['banben']);
    	}
    	$this->assign('kid',$kid);
		$this->assign('banben',$data_version);
    	$this->assign('kecheng',$data);
    	$this->display();
    }


    public function getuserinfos(){
	// echo cookie('ut').'|'.cookie('sso_url');exit();
		$ut=I("ut");
		//echo $ut;exit;
		$deviceType=I("deviceType");
		$areacode=I("areaCode");
		$arcareacode=I("serAreaCode");
		$areacode=empty($areacode)?$arcareacode:$areacode;
		if(cookie('klx_ut')!=cookie('ut')){
			echo "saaa";exit;
			cookie("ut",$ut);
		}
		else{
			echo "ssss";exit;
		}
	//	echo cookie('ut');exit;
        if(cookie('ut') != null){
		  //echo cookie('ut');exit;
		  $rs=get_ip($areacode,"");
		  $sso=$rs['sso']['c1'];
		  cookie('sso_url',$sso);
          if(cookie('klx_ut')!=cookie('ut')){
            //echo  cookie('sso').'||';exit;
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
			//echo $url;exit;
            if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
              $url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
            }


            $json_ret = file_get_contents($url);


            //var_dump( $json_ret);
            $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gbk");//转码，（这里只是个例子）
            //var_dump( $json_ret);
            $result = json_decode($json_ret, true);
            //var_dump($result);exit;
            if($result['authFlg']=='1')
            {
              cookie("klx_ut",cookie('ut'));
              $ouUser=$result['ouUser'];
              $user=$result['user'];
              //var_dump($user);exit;
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
                  cookie("schoolId",$user["school"]['schoolId']);
                  cookie("classId",$user["schoolClasses"][0]["classId"]);
                  cookie("gradeCode",$user['grade']['gradeCode']);
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
    /**
     *检查用户记录是否存在
     */
    private function checkUser($userid,$userarea){
    	$m=M('record');
    	$data=$m->where('userid="%s" and userarea = "%s"',$userid,$userarea)->field('kid')->find();
    	if (empty($data)){
    		return 0;
    	}else {
    		return $data['kid'];
    	}
    }
    public function updateRecord(){
    	$kid=I('kid/d');
    	$opt=I('opt/s');

    	$userid=cookie('username');
    	$userarea=cookie('areacode');
    	if ($userid!=''){
	    	$m=M('record');
	    	$data['userid']=$userid;
	    	$data['userarea']=$userarea;
	    	$data['kid']=$kid;
	    	$data_user=$m->where('userid="%s" and userarea="%s"',$userid,$userarea)->find();
	    	if (empty($data_user)){
	    		$m->add($data);
	    	}else {
	    		$m->where('userid="%s" and userarea="%s"',$userid,$userarea)->save($data);
	    	}
    	}
    }
    /**
     * 判断是否有人教版
     */
    private function checkVersion($data_version){
    	$data_version=array_column($data_version,'banben');
    	$default_v="人教版";
    	if (in_array($default_v, $data_version)){
    		//如果有人教版就查询人教版下的资源
    		$banben=$default_v;
    	}else{
    		$banben=$data_version[0];
    	}
    	return $banben;
    }
    /**
     * 改变年级或者学期
     */
    public function changeGrade(){
    	$nianji=I('nianji/s');
    	$xueqi=I('xueqi/s');
    	$m=M();
    	$sql='SELECT DISTINCT n.banben FROM hl_kecheng m RIGHT JOIN hl_ziversion n ON m.versionid=n.id WHERE m.nianji="%s" AND m.xueqi="%s" ORDER BY n.sortid';
    	$data_version=$m->query($sql,$nianji,$xueqi);
    	//$data_version=$m->distinct(true)->where('nianji="%s" and xueqi="%s"  and url is not null',$nianji,$xueqi)->field('banben')->select();
    	if (empty($data_version)){
    		echo 'error';
    	}else {
    		$sql_first='select id from hl_ziversion where banben="%s"';
    		$data_first=$m->query($sql_first,$data_version[0]['banben']);
	    	$data_kecheng=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
	    	$info['banben']=$data_version;
	    	$info['kecheng']=$data_kecheng;
	    	//var_dump($info);
	    	$this->ajaxReturn($info);
    	}
    }
    /**
     * 改变版本
     */
    public function changeVersion(){
    	$nianji=I('nianji/s');
    	$banben=I('banben/s');
    	$xueqi=I('xueqi/s');
    	$sql_first='select id from hl_ziversion where banben="%s"';
    	$data_first=M()->query($sql_first,$banben);
    	$m=M('kecheng');
    	$data=$m->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
    	$this->ajaxReturn($data);
    }

    /**
     * 课程汉字内容页
     */
    public function info(){
    	$kid=I('kid/d');
    	$m=M('kecheng');
    	$data=$m->where('id="%d"',$kid)->field('kecheng')->find();

    	$sql='SELECT m.zid,m.ziyinid,n.zi,n.pianpang,n.bihuashu,n.shizi,n.xiezi FROM hl_kecheng_hanzi m,hl_zi n WHERE m.zid=n.id AND m.kechengid="%d" ORDER BY sortid';
    	$Model=M();
    	$data_hanzi=$Model->query($sql,$kid);

    	//查询上一页和下一页的课程id

    	$sql_info='SELECT id FROM hl_kecheng WHERE nianji=(SELECT nianji from hl_kecheng WHERE id="%d") AND xueqi=(SELECT xueqi from hl_kecheng WHERE id="%d") and versionid=(SELECT versionid from hl_kecheng WHERE id="%d") ORDER BY sortid';
    	$data_info=$Model->query($sql_info,$kid,$kid,$kid);
    	$data_info=array_column($data_info,'id'); //二维数组转一维数组
    	//var_dump($data_info);
    	$keyarray=array_keys($data_info,$kid);
    	$key=$keyarray[0];

    	if ($data_info[$key-1]==''){
    		$kidup='first';
    	}else {
    		$kidup=$data_info[$key-1];
    	}

    	if ($data_info[$key+1]==''){
    		$kiddown='last';
    	}else {
    		$kiddown=$data_info[$key+1];
    	}

//     	$data_info=$m->where('id="%d"',$kid)->field('nianji,xueqi,versionid,sortid')->find();
//     	$sort=$data_info['sortid'];

//     	$sql1='SELECT id,case WHEN sortid="%d" then "下一页" else "上一页" end as flag FROM hl_kecheng WHERE nianji="%s" and xueqi="%s" AND versionid="%d" and sortid in ("%d","%d")';
//     	$data_sx=$Model->query($sql1,$sort+1,$data_info['nianji'],$data_info['xueqi'],$data_info['versionid'],$sort-1,$sort+1);
//     	if (count($data_sx)==2){
//     		$kidup=$data_sx[0]['id'];
//     		$kiddown=$data_sx[1]['id'];
//     	}else {
//     		if ($data_sx[0]['flag']=='上一页'){
//     			$kidup=$data_sx[0]['id'];
//     			$kiddown='last';
//     		}else {
//     			$kidup='first';
//     			$kiddown=$data_sx[0]['id'];
//     		}
//     	}

    	$this->assign('kidup',$kidup);
    	$this->assign('kiddown',$kiddown);
    	$this->assign('kid',$kid);
    	$this->assign('hanzi',$data_hanzi);
    	$this->assign('kecheng',$data['kecheng']);
    	$this->display();
    }

    public function queryHanziInfo(){
    	$zid=I('zid/d');
    	$ziyinid=I('ziyinid/d');
    	$m=M();
    	$sql='SELECT n.id,m.pianpang,m.bihuashu,m.shizi,m.xiezi,n.pinyin,n.wav,n.zaoju1,n.zaoju2,n.zaoju3,n.zuci1,n.zuci2,n.zuci3,n.ziyi1,n.ziyi2,n.ziyi3 FROM hl_zi m,hl_ziyin n WHERE m.id=n.zid AND n.id="%d"  AND m.id="%d"';
    	$data=$m->query($sql,$ziyinid,$zid);

    	$tmp=$this->checkDuoyin($zid, $ziyinid);

    	$duoyin['duoyin']=$tmp;
    	$duoyin['normal']=$data;
    	//var_dump($duoyin);exit();
    	$this->ajaxReturn($duoyin);
    }
    /**
     * 测试是否多音字
     */
    private function checkDuoyin($zid,$ziyinid){
    	$sql='SELECT id,pinyin,wav FROM hl_ziyin WHERE id<>"%d" AND zid="%d"';
    	$m=M();
    	$data=$m->query($sql,$ziyinid,$zid);
    	return  $data;
    }
    /**
     * 查询多音字信息
     */
    public function queryDuoyin(){
    	$ziyinid=I('ziyinid/d');
    	$m=M('ziyin');
    	$data=$m->where('id="%d"',$ziyinid)->field('pinyin,zaoju1,zaoju2,zaoju3,zuci1,zuci2,zuci3,ziyi1,ziyi2,ziyi3')->find();
    	$this->ajaxReturn($data);
    }





    /**
     * 测试
     */
    public function  test(){
    	$m=M('kecheng');
    	$data=$m->field('nianji,xueqi,banben,url,kecheng,sortid')->select();
    	$json= json_encode($data);
    	var_dump(json_decode($json));
    	var_dump(json_decode($json,true));
    }
}


