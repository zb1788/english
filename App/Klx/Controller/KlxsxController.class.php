<?php
namespace Klx\Controller;
use Think\Controller;


class KlxsxController extends Controller {


	/**
	 * 首页
	 */
   public function index(){

   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	$userarea=cookie('localAreaCode');
   	$grade=cookie('gradeCode');

    $usertype=cookie('usertype');

    if($usertype==0){
      //家长
      $userid=cookie('student_number');
    }else{
      $userid=cookie('username');
    }


   	if($grade!='0001'&&$grade!='0002'&&$grade!='0003'){
   		$grade='0001';
   	}


    if(cookie('klx_ut')!=cookie('ut')){
      /*
      $mm = M('db_english.dictionary','engs_');
      $tmsRes = $mm->where('code="yjt" and remark="tms" and c3="%s"',$userarea)->find();
      $tmsip = $tmsRes["c1"];
      $ubRes = $mm->where('code="yjt" and remark="ub" and c3="%s"',$userarea)->find();
      $ubip = $ubRes["c1"];
      $ilearnRes = $mm->where('code="yjt" and remark="ilearn" and c3="%s"',$userarea)->find();
      $ilearnip = $ilearnRes["c1"];
      */

      $yjArr = get_ip($userarea,'');

      //$tmsip = $yjArr['tms']['c1'];
      //$ubip = $yjArr['ub']['c1'];
      //$ilearnip = $yjArr['ilearn']['c1'];
      $tmsip = $yjArr['tms']['title'];
      $ubip = $yjArr['ub']['title'];
      $ilearnip = $yjArr['ilearn']['title'];


      cookie('tmsip',$tmsip);
      cookie('ubip',$ubip);
      cookie('ilearnip',$ilearnip);
      cookie("klx_ut",cookie('ut'));
    }





   	$user=M('users');
    //$data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('username')->find();
   	$data_user=$user->where('userid="%s" and userarea="%s" ',$userid,$userarea)->field('username')->find();

    //var_dump($data_user);exit();
   	//查询用户信息，如果用户不存在就添加用户信息
   	$m=M('math');
   	if (!empty($data_user)){
   		$this->assign('truename',$data_user['username']);
   		// $sql_ks="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid) ) as gra FROM hl_math m where m.mathtype=1 and grade='%s' order by m.sortid";

   		// $sql_ts="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid) ) as gra FROM hl_math m where m.mathtype=2 and grade='%s' order by m.sortid";

      $sql_ks = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 1 AND m.grade = '%s' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";
      $sql_ts = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 2 AND m.grade = '%s' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";
      $data_ks=$m->query($sql_ks,$userid,$classid,$schoolid,$userarea,$grade);
   		$data_ts=$m->query($sql_ts,$userid,$classid,$schoolid,$userarea,$grade);
   	}else{
   		// $sql_ks="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid) ) as gra FROM hl_math m where m.mathtype=1 and grade='0001' order by m.sortid";
   		// $sql_ts="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id ORDER BY n.date DESC LIMIT 1) ) as gra FROM hl_math m where m.mathtype=2 and grade='0001' order by m.sortid";

      $sql_ks = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 1 AND m.grade = '0001' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";
      $sql_ts = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 2 AND m.grade = '0001' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";
      $data_ks=$m->query($sql_ks,$userid,$classid,$schoolid,$userarea,$userarea);
   		$data_ts=$m->query($sql_ts,$userid,$classid,$schoolid,$userarea,$userarea);
   	}
  // 	$userid='01788';

  	$this->assign('grade',$grade);
   	$this->assign('data_ks',$data_ks);
   	$this->assign('data_ts',$data_ts);
   	$this->display();
   }
   //获取用户信息
   public function getUserinfo(){

   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	$userarea=cookie('localAreaCode');
    $usertype=cookie('usertype');
   	$truename=I('truename/s');
    $abc = cookie('xxx');
    if($usertype==0){
      //家长
      $userid=cookie('student_number');
    }else{
      $userid=cookie('username');
    }


    if (is_null($schoolid)||empty($classid)||empty($userarea)||empty($usertype)||empty($truename)){

      exit('用户信息不全');
    }

   	$user=M('users');
    //$data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('id')->find();
   	$data_user=$user->where('userid="%s" and userarea="%s" ',$userid,$userarea)->field('id,classid,schoolid,username')->find();
   	//查询用户信息，如果用户不存在就添加用户信息
   	if (empty($data_user)){
   		$data['userid']=$userid;
   		$data['username']=$truename;
   		$data['userarea']=$userarea;
   		$data['classid']=$classid;
   		$data['schoolid']=$schoolid;
   		$user->add($data);

      // file_put_contents('public/history.txt', '当前PC用户登录成功[新增用户]['.$userid.'|'.$truename.'|'.$userarea.']记录用户ID：'.$userid."\r\n", FILE_APPEND);
   	}else{
      //如果班级或者学校或者姓名发生改变就更新数据库
      if($classid!==$data_user['classid']||$schoolid!==$data_user['schoolid']||$truename!==$data_user['username'])
      $data['username']=$truename;
      $data['classid']=$classid;
      $data['schoolid']=$schoolid;
      $user->where('id="%d"',$data_user['id'])->save($data);
      // file_put_contents('public/history.txt', '当前PC用户登录成功[已有用户]记录用户ID：'.$userid."\r\n", FILE_APPEND);
    }
    $arr['userid']=$userid;
    $arr['userarea']=$userarea;
    $this->ajaxReturn($arr);
   }
   /**
    * 获取当前年级下的章节信息
    */
   public function getGrades(){
   	$grade=I('grade/s');
   	if ($grade=='一年级'){
   		$grade='0001';
   	}elseif ($grade=='二年级'){
   		$grade='0002';
   	}else {
   		$grade='0003';
   	}
   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	$userarea=cookie('localAreaCode');
    $usertype=cookie('usertype');

    if($usertype==0){
      //家长
      $userid=cookie('student_number');
    }else{
      $userid=cookie('username');
    }


   	$m=M('math');
   	$sql_ks="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid) ) as gra FROM hl_math m where m.mathtype=1 and grade='%s' order by m.sortid";
   	$sql_ts="SELECT m.*,(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid)  as gold,(5-(SELECT max(n.stars) FROM hl_usersinfo n left JOIN hl_users t ON n.usersid=t.id  WHERE  t.userid='%s' AND t.classid='%s' AND t.schoolid='%s' AND t.userarea='%s' AND n.mathid=m.id GROUP BY n.mathid) ) as gra FROM hl_math m where m.mathtype=2 and grade='%s' order by m.sortid";
   	$data_ks=$m->query($sql_ks,$userid,$classid,$schoolid,$userarea,$userid,$classid,$schoolid,$userarea,$grade);
   	$data_ts=$m->query($sql_ts,$userid,$classid,$schoolid,$userarea,$userid,$classid,$schoolid,$userarea,$grade);
   	$data['ks']=$data_ks;
   	$data['ts']=$data_ts;
   //	var_dump($data);exit();
   	$this->ajaxReturn($data);
   }
   //获取15道试题信息
   public function getinfo(){
   	$bid=I('bid/d');
    $total_eq=I('total_eq/d');


   	$data=M('mathinfo')->where('mathid="%d"',$bid)->select();

   	$data=$this->unique_array($data, $total_eq);

   //	var_dump($data);exit();
   	$this->ajaxReturn($data);

   }
   /**
    * 获取成绩
    */
   public function getResult(){
   	$json=I('json/s',0);
   	$json=str_replace('&quot;', '"', $json);
   	$gene=I('gene/s');
   	$type=I('type/d');
    $bid=I('bid/d');
   	$uburl=I('uburl/s');
    $userid=I('userid/s');
    $userarea=I('userarea/s');

   	$right=0;
   	$wrong=0;
   	$star=0;
   	$tmpstr='';
    $lxarr = array();
    $top = 0;
    $ubi = 0;
   	//var_dump(json_decode($json,true));exit();
   	foreach (json_decode($json,true) as $v){
   		$Model=M('mathinfo');
   		$result=$Model->where('id="%d"',$v['id'])->field('result')->find();
   		if ($result['result']==$v['answer']){
   			$right++;
        $tmpstr.='r';
   		}else{
        if(!empty($v['answer'])){
          $wrong++;
        }
        $tmpstr.='|';
   		}

   	}
    //echo $tmpstr;exit();
    //计算连胜的次数，如果大于等于5次放入数组，U币计算用
    $tmparr=explode('|',$tmpstr);
   // var_dump($tmparr);
    foreach ($tmparr as $key => $value) {
      //echo strlen($value).'<br>';
      if(strlen($value)>=5){
        array_push($lxarr, strlen($value));
      }
    }
    //var_dump($lxarr);

   	if ($right==0){
   		$star=0;
   	}elseif ($right>0&&$right<=3){
   		$star=1;
   	}elseif ($right>3&&$right<=7){
   		$star=2;
   	}elseif ($right==8){
   		$star=3;
   	}elseif ($right>8&&$right<=10){
   		$star=4;
   	}else {
   		$star=5;
   	}

   	//$userid=cookie('username');
   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	//$userarea=cookie('localAreaCode');
    $usertype=cookie('usertype');


    // if($usertype==0){
    //   //家长
    //   $userid=cookie('student_number');
    // }else{
    //   $userid=cookie('username');
    // }
  //echo $userid.'/'.$userarea.'/'.$classid.'/'.$schoolid;exit;

	$user=M('users');
	//查询用户users的id
	$data_user=$user->where('userid="%s" and userarea="%s" ',$userid,$userarea)->field('id,stars_ks,stars_ts,ks_utime,ts_utime')->find();


  // file_put_contents('public/history.txt', '当前PC用户答题结束,用户优教通id:'.$userid.',区域:'.$userarea.',id：'.$data_user['id']."\r\n", FILE_APPEND);

    //var_dump($data_user);exit;

   	$userinfo=M('usersinfo');

    //增加用户记录
   	$reUserinfo=$userinfo->where('mathid="%d" and usersid="%s"',$bid,$data_user['id'])->field('stars')->find();

      $userstar['usersid']=$data_user['id'];
      $userstar['mathid']=$bid;
      $userstar['stars']=$star;
      $userstar['rightnum']=$right;
      $userstar['wrongnum']=$wrong;
      $userstar['countnum']=0;//测试用看是手机还是pc,0pc,1手机


   	if (empty($reUserinfo)){
      $ubi=$this->getUbi(0,1,$right,$lxarr);
      $data['istop']='1';
   	}else{
      //获取历史最高正确个数
      $max=$userinfo->where('mathid="%d" and usersid="%s"',$bid,$data_user['id'])->max('rightnum');
      //获取今天练习次数
      $countnum=$userinfo->where('mathid="%d" and usersid="%s" and to_days(date)=to_days(now()) ',$bid,$data_user['id'])->count();

      //var_dump($countnum);exit();
      // $userstar['countnum']= $reUserinfo['countnum']+1;
      // if($reUserinfo['top']>$right){
      //   $top=$reUserinfo['top'];
      // }else{
      //   $top=$right;
      // }
      // $userstar['top']=$top;
   		//$userinfo->where('mathid="%d" and usersid="%s"',$bid,$data_user['id'])->save($userstar);
      $ubi=$this->getUbi($max,($countnum+1),$right,$lxarr);


      if($right>$max){
        $data['istop']='1';
      }else{
        $data['istop']='0';
      }
   	}

    //var_dump($userstar);exit;
    if(!empty($data_user['id'])){
      $userinfo->add($userstar);
    }

    //echo $ubi;exit();

   	//查询当前用户的总星数,然后更新
   	if ($gene=='口算'){
      //$total=$userinfo->table('hl_usersinfo l,hl_math t')->where('l.mathid=t.id AND t.mathtype=1  and l.usersid="%s"',$data_user['id'])->max('l.stars')->group by('t.id');
      $sql='SELECT sum(n.totalstar) as totalstar FROM (SELECT max(l.stars) as totalstar FROM hl_usersinfo l,hl_math t WHERE l.mathid=t.id AND t.mathtype=1 AND l.usersid="%s" GROUP BY t.id) n';
      $re = M()->query($sql,$data_user['id']);

   		$totalstars=$re[0]['totalstar'];
   		$users['stars_ks']=$totalstars;
      $users['ks_utime']=date('Y-m-d H:i:s');

      $ziduan = 'stars_ks';
   	}else {
      //$total=$userinfo->table('hl_usersinfo l,hl_math t')->where('l.mathid=t.id AND t.mathtype=2 and l.mathid="%d" and l.usersid="%s"',$bid,$data_user['id'])->sum('l.stars');

      $sql='SELECT sum(n.totalstar) as totalstar FROM (SELECT max(l.stars) as totalstar FROM hl_usersinfo l,hl_math t WHERE l.mathid=t.id AND t.mathtype=2 AND l.usersid="%s" GROUP BY t.id) n';
      $re = M()->query($sql,$data_user['id']);

      $totalstars=$re[0]['totalstar'];
   		$users['stars_ts']=$totalstars;
      $users['ts_utime']=date('Y-m-d H:i:s');

      $ziduan = 'stars_ts';
   	}

    if($totalstars>$data_user[$ziduan]){
        $user->where('id="%d"',$data_user['id'])->save($users);
    }


   	//查询班级排名
   	if ($gene=='口算'){
   		$sql_class='SELECT rank FROM (SELECT id,stars_ks,rank FROM (SELECT tmp.id,tmp.stars_ks,@rank := @rank + 1 AS rank FROM ((SELECT id,stars_ks,ks_utime FROM hl_users where userarea="%s" and classid="%s" and schoolid="%s"  ) tmp,(SELECT @rank   := 0) a) ORDER BY tmp.stars_ks desc ,tmp.ks_utime asc) RESULT) bb WHERE bb.id="%d"';
   	}else{
   		$sql_class='SELECT rank FROM (SELECT id,stars_ts,rank FROM (SELECT tmp.id,tmp.stars_ts,@rank := @rank + 1 AS rank FROM ((SELECT id,stars_ts,ts_utime FROM hl_users where userarea="%s" and classid="%s" and schoolid="%s"  ) tmp,(SELECT @rank   := 0) a) ORDER BY tmp.stars_ts desc ,tmp.ts_utime asc) RESULT) bb WHERE bb.id="%d"';
   	}

   	//$order_class=$user->where('userarea="%s" and classid="%s" and schoolid="%s"',$userarea,$classid,$schoolid)->order('totalstars')->select();
   	$order_class=$user->query($sql_class,$userarea,$classid,$schoolid,$data_user['id']);

    //$ubi = 1;
    //调用U币接口

  if($ubi!=0){

    $ad = time() . mt_rand(1000,9999);
    $at = 'u';
    $a = $userid;
    $ut = '4';
    $c = 'u_kousuan'.$ubi;
    $ac = cookie('areacode');
    $n = 1;
    $t = date('Y-m-d H:i:s');
    $r = 'ss';
    $i = $c;
    $parm = '{"ad":"'.$ad.'","at":"'.$at.'","a":"'.$a.'","ut":"'.$ut.'","c":"'.$c.'","ac":"'.$ac.'","n":"'.$n.'","t":"'.$t.'","r":"'.$r.'","i":"'.$i.'"}';


    $uburl = cookie('ubip');

	if( stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true
		|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}


    $uburl= $protocol.'://'.$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);
    $uburl=str_replace('%7B', '{', $uburl);
    $uburl=str_replace('%7D', '}', $uburl);


    //echo $ubi.'|'.$uburl;exit;
    $curl = curl_init($uburl);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        //$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

        $result = json_decode($responseText, true);
       // var_dump($result);

        $issuc=$result[0]['result'];
        if($issuc==1){
          //echo '添加成功';
          $data['status']='添加成功';
          $data['ubi']=$result[0]['count'];
        }else{
          $data['status']='添加失败';
          $data['ubi']=0;
          //echo '添加失败';
        }

  }else{
    $data['ubi']=0;
  }


   	$data['right']=$right;
   	$data['wrong']=$wrong;
   	$data['star']=$star;
   	$data['order']=$order_class[0]['rank'];
   	$this->ajaxReturn($data);
   }

   /**
    *获取U币
    *$top 最高正确次数
    *$countnum 今天练习次数
    *$right 正确个数
    *$array 连续正确大于5的数组
    */
   public function getUbi($top,$countnum,$right,$array){

      $ubi=0;
      if($top<8){
        if($countnum<=3){
          foreach ($array as $key => $value) {
            if($value>=10){
              $ubi+=3;
              continue;
            }

             if($value>=8){
              $ubi+=2;
              continue;
            }

            if($value>=5){
              $ubi+=1;
            }
          }
        }else{
          $ubi=0;
        }
      }else{
        //如果当天最高记录大于8，不给u币
        $ubi=0;

      }
      return $ubi;
   }


   /**
    * 获取排名信息
    */
   public function getRank(){
   		$gene=I('gene/s');
   		$type=I('type/s');
   		$pageCurrent=I('pageCurrent/d',0);
   		$page_size=I('page_size/d',0);

   		$schoolid=cookie('schoolId');
   		$classid=cookie('classId');
   		$userarea=cookie('localAreaCode');
      $usertype=cookie('usertype');

      if($usertype==0){
      //家长
        $userid=cookie('student_number');
      }else{
        $userid=cookie('username');
      }


   		$user=M('users');
   		//查询用户users的id
   		$data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('id')->find();


   		if ($gene=='口算'){
   			$sql_gen='stars_ks';
        $sql_time='ks_utime';
   		}else{
   			$sql_gen='stars_ts';
        $sql_time='ts_utime';
   		}
   		if ($type=='class'){
   			$sql_type='where userarea="'.$userarea.'" and classid="'.$classid.'" and schoolid="'.$schoolid.'"';
   		}else if($type=='school'){
   			$sql_type='where userarea="'.$userarea.'" and schoolid="'.$schoolid.'"';
   		}else{
   			$sql_type=' ';
   		}

   		$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;

   		$sql_my='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM ((SELECT id,'.$sql_gen.' ,'.$sql_time.' FROM hl_users '.$sql_type.' ) tmp,(SELECT @rank   := 0) a ) ORDER BY tmp.'.$sql_gen.' desc ,tmp.'.$sql_time.' asc,id asc)   RESULT) bb WHERE bb.id="'.$data_user['id'].'"';
      // echo $sql_my;
      $order_my=$user->query($sql_my);
   		//var_dump($order_my);exit();

      $start = ($pageCurrent-1)*$page_size;
   		$sql='SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM ((SELECT id,username,'.$sql_gen.' ,'.$sql_time.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc ,'.$sql_time.' asc '.$sql_limit.') tmp,(SELECT @rank   := '.$start.') a) ORDER BY '.$sql_gen.' desc ,'.$sql_time.' asc,id asc) RESULT';
   		$orde=$user->query($sql);
      // echo $sql;

   		$sql_total='SELECT count(*) as num FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc ';

   		$data_total=$user->query($sql_total);
   		$total=$data_total[0]['num'];
      if ($type!=='class'){
     		if($total>20){
     			$total=20;
     		}
   		}

   		$sub_pages=6;
   		/* 实例化一个分页对象 */
   		Vendor('SubPagessx');

   		$subPages=new \SubPagessx($page_size,$total,$pageCurrent,$sub_pages);
   		$page= $subPages->subPageCss2();

   		$data['my']['rank']=$order_my[0]['rank'];
   		$data['my']['mystar']=$order_my[0][$sql_gen];
   		$data['my']['type']=$type;
   		$data['now']=$orde;
   		$data['page']=$page;
   		//var_dump($data);exit();
   		$this->ajaxReturn($data);
   }

   /**
    * 活动活动期间排名接口
    * @DateTime 2017-01-17T15:54:02+0800
    * @return   [type]                   [description]
    */
  public function getRankJson(){
      $gene          = I('gene/s');//口算|听说
      $type          = I('type/s');//class|school|all
      $schoolid      = I('schoolid/s');
      $classid       = I('classid/s');
      $userarea      = I('localAreaCode/s');
      $userid        = I('username/s');
      $start         = I('start/s');
      $end           = I('end/s');




      $user=M('users');
      //查询用户users的id
     // $data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('id')->find();


      if ($gene=='口算'){
        $sql_gen='stars_ks';
        $sql_time='ks_utime';
        $mathtype = 1;
      }else{
        $sql_gen='stars_ts';
        $sql_time='ts_utime';
        $mathtype = 2;
      }
      if ($type=='class'){
        $sql_type='where userarea="'.$userarea.'" and classid="'.$classid.'" and schoolid="'.$schoolid.'"';
      }else if($type=='school'){
        $sql_type='where userarea="'.$userarea.'" and schoolid="'.$schoolid.'"';
      }else{
        $sql_type=' ';
      }

      $sql_limit=' limit 0,10';

      $sql_my='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,'.$sql_gen.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc ,'.$sql_time.' asc ) tmp,(SELECT @rank   := 0) a) RESULT) bb WHERE bb.id="'.$data_user['id'].'"';
      // echo $sql_my;
      //$order_my=$user->query($sql_my);
      //var_dump($order_my);exit();


      $sql = 'SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.' ,@rank := @rank + 1 AS rank FROM (SELECT SUM(n.maxstars) AS '.$sql_gen.',m.id,m.`username` FROM ( SELECT MAX(l.stars) AS maxstars,l.usersid FROM (SELECT stars,usersid,mathid FROM hl_usersinfo WHERE DATE >"'.$start.'" AND DATE < "'.$end.'") l LEFT JOIN hl_math t ON l.mathid = t.id WHERE t.mathtype = '.$mathtype.' GROUP BY t.id,l.usersid) n , ( select id,username from hl_users t '.$sql_type.' ) m where n.usersid=m.id GROUP BY n.usersid ORDER BY '.$sql_gen.' DESC '.$sql_limit.') tmp,(SELECT @rank := 0) a) RESULT';

      // echo $sql;
      $data=$user->query($sql);

      $this->ajaxReturn($data);
   }

   public function test(){
   	$userid=cookie('username');
   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	$userarea=cookie('localAreaCode');
   	$user=M('users');

   	$str=I('sql');
   	echo $str;
   	M()->execute($str);
   }

   /**
    * 随机从数据取若干个数元素
    */

   function unique_array($array, $total){
   	$newArray = array();
   	shuffle($array);
   	$length = count($array);
   	for($i = 0; $i < $total; $i++){
   		if($i < $length){
   			$newArray[] = $array[$i];
   		}
   	}
   	return $newArray;
   }


   /**
    *请求接口
    */
  function getuserinfo23131(){

        //header("Content-type:text/html;charset=utf-8");
        $url = cookie('sso_url').'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
     // $url='http://ssozz.zzedu.net.cn/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=dbbba8448af7a8d3adc9b1bc460f4aecb6b9793d648b0d388de66964795ea973';
      $currentuserpic=cookie('engm_curruserpic');
        $json_ret = file_get_contents($url);
        //echo $json_ret;
        $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
        $result = json_decode($json_ret, true);
        if($result['authFlg']=='1')
        {
            $ouUser=$result['ouUser'];
            $user=$result['user'];
            cookie("engm_truename",$ouUser['name']);
            cookie("engm_username",$ouUser['code']);
            cookie("engm_usertype",$ouUser['type']);
            cookie("engm_areacode",$user['area']['areaId']);
        }
      else{
        redirect('http://www.czbanbantong.com');
      }
    }



   public function suanfa(){
    	$types=I('types/s');//算法：加法或者减法
    	$val=I('val/d');//结果类型:范围结果和指定结果1为范围，0为指定
    	$param1_min=I('param1_min/d');//数1的最小值
    	$param1_max=I('param1_max/d');//数1的最大值
    	$param2_min=I('param2_min/d');//数2的最小值
    	$param2_max=I('param2_max/d');//数2的最大值
    	$result_zhiding=I('result_zhiding/s');//指定结果的值
    	$result_min=I('result_min/d');//范围结果的最小值
    	$result_max=I('result_max/d');//范围结果的最大值
    	$total=I('total/d');//结果个数
    	$params_1=I('params_1/s','');//十百千
    	$params_2=I('params_2/s','');//十百千

    	if ($params_1==''&&$params_2==''){
    		$option=0;//非整十百千
    	}else {
    		$option=1;
    	}

    	$result_arr=array();//结果数组
    	if ($val==1){//结果类型为范围(0-9)
    		$result_arr=range($result_min, $result_max);
    	}else{//结果类型为指定(3,6,9)
    		$result_zhiding=str_replace('，', ',', $result_zhiding);
    		$result_arr=explode(',', $result_zhiding);
    		sort($result_arr);
    	}


    	$strArr=array();
    	if ($types=='加法'){
    		$data=$this->jiafayunsuan($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2);
    	}
    	if ($types=='减法'){
    		$data=$this->jianfayunsuan($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2);
    	}
    	if ($types=='加减混合'){
    		$data=$this->jiajianhunhe($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2);
    	}
    	if ($types == '乘法'){
    		$data = $this->chengfayunsuan($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2);
    	}
    	var_dump($data);
    	//$this->ajaxReturn($data);
    }

    /**
     * 加法运算
     */
    protected  function jiafayunsuan($param1_min,$param1_max,$param2_min,$param2_max,$result_arr,$total,$strArr,$option,$params_1,$params_2){
    	if ($option==0){
    		$params_1=1;
    		$params_2=1;
    	}
    	for ($i=$param1_min;$i<=$param1_max;$i++){
    		for ($j=$param2_min;$j<=$param2_max;$j++){
    			if ($i*$params_1+$j*$params_2>$result_arr[count($result_arr)-1]){
    				break;
    			}
    			if (in_array($i*$params_1+$j*$params_2, $result_arr)){
    				array_push($strArr, $i*$params_1.'+'.$j*$params_2.'='.($i*$params_1+$j*$params_2));
    			}
    		}
    	}
    	shuffle($strArr);
    	if ($total>count($strArr)){//如果结果数大于数组总数就取整个数组
    		$data=$strArr;
    	}else{
    		$data=array_slice($strArr, 0,$total);
    	}
    	return $data;
    }
    /**
     * 减法运算
     */
    protected  function jianfayunsuan($param1_min,$param1_max,$param2_min,$param2_max,$result_arr,$total,$strArr,$option,$params_1,$params_2){
    	if ($option==0){
    		$params_1=1;
    		$params_2=1;
    	}
    	for ($i=$param1_min;$i<=$param1_max;$i++){
    		for ($j=$param2_min;$j<=$param2_max;$j++){
    			if ($i*$params_1<$j*$params_2){
    				break;
    			}
    			if (in_array($i*$params_1-$j*$params_2, $result_arr)){
    				array_push($strArr, $i*$params_1.'-'.$j*$params_2.'='.($i*$params_1-$j*$params_2));
    			}
    		}
    	}
    	shuffle($strArr);
    	if ($total>count($strArr)){//如果结果数大于数组总数就取整个数组
    		$data=$strArr;
    	}else{
    		$data=array_slice($strArr, 0,$total);
    	}
    	return $data;
    }
    /**
     * 加减混合运算
     */
    protected function jiajianhunhe($param1_min,$param1_max,$param2_min,$param2_max,$result_arr,$total,$strArr,$option,$params_1,$params_2){
    	if ($option==0){
    		$params_1=1;
    		$params_2=1;
    	}
    	$arr=array('+','-');
    	for ($i=$param1_min;$i<=$param1_max;$i++){
    		for ($j=$param2_min;$j<=$param2_max;$j++){
    			if($arr[rand(0,1)]=='+'){
    				if ($i*$params_1+$j*$params_2<0){
    					break;
    				}
    				if ($i*$params_1+$j*$params_2>$result_arr[count($result_arr)-1]){
    					break;
    				}
    				if (in_array($i*$params_1+$j*$params_2, $result_arr)){
    					array_push($strArr, $i*$params_1.'+'.$j*$params_2.'='.($i*$params_1+$j*$params_1));
    				}
    			}else{
    				if ($i*$params_1<$j*$params_2){
    					break;
    				}
    				if (in_array($i*$params_1-$j*$params_2, $result_arr)){
    					array_push($strArr, $i*$params_1.'-'.$j*$params_2.'='.($i*$params_1-$j*$params_2));
    				}
    			}

    		}
    	}
    	shuffle($strArr);
    	if ($total>count($strArr)){//如果结果数大于数组总数就取整个数组
    		$data=$strArr;
    	}else{
    		$data=array_slice($strArr, 0,$total);
    	}
    	return $data;
    }
    //乘法运算
    public function chengfayunsuan($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2){
//     	$types=I('types/s');//算法：乘法
//     	$val=I('val/d');//结果类型:范围结果和指定结果1为范围，0为指定
//     	$param1_min=I('param1_min/d');//数1的最小值
//     	$param1_max=I('param1_max/d');//数1的最大值
//     	$param2_min=I('param2_min/d');//数2的最小值
//     	$param2_max=I('param2_max/d');//数2的最大值
//     	$result_zhiding=I('result_zhiding/s');//指定结果的值
//     	$result_min=I('result_min/d');//范围结果的最小值
//     	$result_max=I('result_max/d');//范围结果的最大值
//     	$total=I('total/d');//结果个数
//     	$params_1=I('params_1/s','');//十百千
//     	$params_2=I('params_2/s','');//十百千


    	if ($option==0){
    		$params_1=1;
    		$params_2=1;
    	}

    	for ($i=$param1_min;$i<=$param1_max;$i++){
    		for ($j=$param2_min;$j<=$param2_max;$j++){
    			if ($i*$params_1*$j*$params_2>$result_arr[count($result_arr)-1]){
    				break;
    			}
    			if (in_array($i*$params_1*$j*$params_2, $result_arr)){
    				array_push($strArr, $i*$params_1.'×'.$j*$params_2.'='.$i*$params_1*$j*$params_2);
    			}
    		}
    	}
    	shuffle($strArr);
    	if ($total>count($strArr)){//如果结果数大于数组总数就取整个数组
    		$data=$strArr;
    	}else{
    		$data=array_slice($strArr, 0,$total);
    	}
    	$this->ajaxReturn($data);
    }
    //除法运算
    public function chufayunsuan($param1_min, $param1_max, $param2_min, $param2_max, $result_arr, $total, $strArr ,$option ,$params_1 ,$params_2){
    	$types=I('types/s');//有余数，无余数
    	$val=I('val/d');//结果类型:范围结果和指定结果1为范围，0为指定
//     	$param1_min=I('param1_min/d');//数1的最小值
//     	$param1_max=I('param1_max/d');//数1的最大值
//     	$param2_min=I('param2_min/d');//数2的最小值
//     	$param2_max=I('param2_max/d');//数2的最大值
//     	$result_zhiding=I('result_zhiding/s');//指定结果的值
//     	$result_min=I('result_min/d');//范围结果的最小值
//     	$result_max=I('result_max/d');//范围结果的最大值
//     	$total=I('total/d');//结果个数
//     	$params_1=I('params_1/s','');//十百千
//     	$params_2=I('params_2/s','');//十百千

//     	if ($params_1==''&&$params_2==''){
//     		$option=0;//非整十百千
//     	}else {
//     		$option=1;
//     	}

    	if ($option==0){
    		$params_1=1;
    		$params_2=1;
    	}

    	$strArr=array();
    	for ($i=$param1_min;$i<=$param1_max;$i++){
    		for ($j=$param2_min;$j<=$param2_max;$j++){
    			if ($i*$params_1<$j*$params_2){
    				continue;//如果被除数小于除数，直接进入下次循环
    			}
    			if ($types=='无余数'){
    				if (in_array(($i*$params_1)/($j*$params_2), $result_arr)){
    					array_push($strArr, $i*$params_1.'÷'.$j*$params_2.'='.($i*$params_1)/($j*$params_2));
    				}
    			}else{
    				if (in_array(($i*$params_1)%($j*$params_2), $result_arr)){
    					array_push($strArr, $i*$params_1.'÷'.$j*$params_2.'='.intval(floor(($i*$params_1)/($j*$params_2))).'······'.($i*$params_1)%($j*$params_2));
    				}
    			}
    		}
    	}
    	shuffle($strArr);
    	if ($total>count($strArr)){//如果结果数大于数组总数就取整个数组
    		$data=$strArr;
    	}else{
    		$data=array_slice($strArr, 0,$total);
    	}
    	//var_dump($data);
    	$this->ajaxReturn($data);
    }












}


