<?php
namespace Klx\Controller;
use Think\Controller;


class KlxsxmbController extends Controller {


	/**
	 * 首页
	 */
   public function index(){

      // cookie('ut','feb1154a12e509aa4fe5e553e796d8433ff2e9b2d4be72a2ad4e42b16ed1cdbea2cf3963a9f5b926');
      // cookie('sso_url','http://sso9xt.czbanbantong.com');

    $this->getuserinfos();

    //测试数据
    // cookie('schoolId','230707001001');
    // cookie('classId','143735784257948617');
    // cookie('localAreaCode','6.');
    // cookie('gradeCode','0001');
    // cookie('usertype','4');
    // cookie('username','23070710030134');
    //测试数据结束


   	$schoolid=cookie('schoolId');
   	$classid=cookie('classId');
   	$userarea=cookie('localAreaCode');
   	$grade=cookie('gradeCode');
    $usertype=cookie('usertype');
    $truename=cookie('truename');


 // echo $schoolid.'|'.$classid.'|'.$userarea.'|'.$grade.'|'.$usertype.'|'.$truename;exit;
       //cookie('localAreaCode','6.');$userarea='6.';//测试平台区域，上线后这个去掉！！！


    // if($usertype==0){
    //   //家长
    //   $userid=cookie('student_number');
    // }else{
    //   $userid=cookie('username');
    // }

    $userid=cookie('username');//优教通的用户ID,

   	if($grade!='0001'&&$grade!='0002'&&$grade!='0003'){
   		$grade='0001';
   	}



   	$user=M('users');
    //$data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('username,id')->find();
   	$data_user=$user->where('userid="%s" and userarea="%s" ',$userid,$userarea)->field('username,id')->find();

    //var_dump($data_user);exit();
   	//查询用户信息，如果用户不存在就添加用户信息
   	$m=M('math');
    $sql_total = "SELECT grade, count(*) as total FROM hl_math GROUP BY grade";
    $total = M()->query($sql_total);

    //var_dump($total);exit;
    //echo $userid;exit;
    //echo $data_user['id']
    //var_dump($data_user);exit;
   	if (!empty($data_user)){
      cookie('userid',$data_user['id']);

      //file_put_contents('public/history.txt', '当前手机用户登录成功[已有用户]记录cookie：'.$data_user['id']."\r\n", FILE_APPEND);

       if($classid!==$data_user['classid']||$schoolid!==$data_user['schoolid']||$truename!==$data_user['username']){
          $data['username']=$truename;
          $data['classid']=$classid;
          $data['schoolid']=$schoolid;
          $user->where('id="%d"',$data_user['id'])->save($data);
       }
      //查询每道题星星数量大于等于3的题目个数

      $sql = "SELECT a.grade,sum(case WHEN a.stars is NOT NULL THEN 1 ELSE 0 END) as num FROM (SELECT t.id,t.grade,max(b.stars) as stars FROM (select l.stars,l.mathid from hl_usersinfo l WHERE l.usersid='%s' AND l.stars>2 ) b RIGHT JOIN hl_math t ON b.mathid = t.id GROUP BY t.id) as a GROUP BY a.grade;";
      $re = M()->query($sql,$data_user['id']);
      //echo $data_user['id'];exit;
      //var_dump($re);exit;
      // (empty($re[0]['num']))?$grade01=0:$grade01=$re[0]['num'];
      // (empty($re[1]['num']))?$grade02=0:$grade02=$re[1]['num'];
      // (empty($re[2]['num']))?$grade03=0:$grade03=$re[2]['num'];
      $j0 = $re[0]['num']==null?0:$re[0]['num'];
      $j1 = $re[1]['num']==null?0:$re[1]['num'];
      $j2 = $re[2]['num']==null?0:$re[2]['num'];
      //echo $j0.'|'.$j1.'|'.$j2;exit;
      $jindu[0]['name']=$j0.'/'.$total[0]['total'];
      // echo $jindu[0]['name'];exit;
      $jindu[0]['style']=round($j0/$total[0]['total']*100);
      $jindu[0]['status']=$j0;
      $jindu[1]['name']=$j1.'/'.$total[1]['total'];
      $jindu[1]['style']=round($j1/$total[1]['total']*100);
      $jindu[1]['status']=$j1;
      $jindu[2]['name']=$j2.'/'.$total[2]['total'];
      $jindu[2]['style']=round($j2/$total[2]['total']*100);
      $jindu[2]['status']=$j2;

   	}else{
      //没有用户信息，新增用户信息

      $data['userid']=$userid;
      $data['username']=$truename;
      $data['userarea']=$userarea;
      $data['classid']=$classid;
      $data['schoolid']=$schoolid;

      if (empty($schoolid)||empty($classid)||empty($userarea)||empty($userid)||empty($truename)){

      }else{
        $id = $user->add($data);
      }

      cookie('userid',$id);

      //file_put_contents('public/history.txt', '当前手机用户登录成功[新增用户]['.$truename.'|'.$userid.'|'.$userarea.']记录cookie中：'.$id."\r\n", FILE_APPEND);

      $jindu[0]['name']='0/'.$total[0]['total'];
      $jindu[0]['style']=0;
      $jindu[0]['status']=0;
      $jindu[1]['name']='0/'.$total[1]['total'];
      $jindu[1]['style']=0;
      $jindu[1]['status']=0;
      $jindu[2]['name']='0/'.$total[2]['total'];
      $jindu[2]['style']=0;
      $jindu[2]['status']=0;

   	}


    if(!empty(cookie('nowGrade'))){
      $grade = cookie('nowGrade');
    }

    $this->assign('truename',cookie('truename'));
    $this->assign('jindu',$jindu);
  	$this->assign('grade',$grade);
   	$this->display();
   }



public function getuserinfos(){
        //header("Content-type:text/html;charset=utf-8");
    // echo cookie('ut').'|'.cookie('sso_url');exit();
    if(cookie('ut') != null){
      //echo cookie('ut');exit;
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

        /*
        cookie('tmsurl',str_replace('sso','tms',cookie('sso_url')));
        cookie('ilearnurl',str_replace('sso','ilearn',cookie('sso_url')));
        cookie('uburl',str_replace('sso','ub',cookie('sso_url')));

        $this->getLocalAreaCode(cookie('sso_url'));

        //echo cookie('tmsurl'),cookie('ilearnurl'),cookie('uburl');exit;

        $sso = str_replace("http://","",cookie('sso_url'));
        $mm = M('db_english.dictionary','engs_');
        $ssoRes = $mm->where('code="yjt" and remark="sso" and title="%s"',$sso)->find();
        $ssoip = $ssoRes["c1"];
        $tmsRes = $mm->where('code="yjt" and remark="tms" and title="%s"',str_replace("sso","tms",$sso))->find();
        $tmsip = $tmsRes["c1"];
        */

        $url = $ssoip.'/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
        //echo $url;exit;

        if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
          $url = 'http://218.28.177.227/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
        }
        //echo $url.'<br>';exit;
        //$url = 'http://sso.youjiaotong.com/sso/ssoGrant?isPortal=0&appFlg=SSO&ut='.cookie('ut');
        //echo $url.'<br>';exit;
        //echo $url;exit;
        //$currentuserpic=cookie('engm_curruserpic');
        $json_ret = file_get_contents($url);

        //echo $json_ret.'<br>';
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

              //echo $user['area']['areaId'].'type:'.$user['usertype'].$user['username'];exit;

              cookie("usertype",$user['usertype']);


              $tmsurl = $tmsip.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];

              if(count(explode('zzedu',cookie('sso_url'))) > 1){ //如果是郑州的用户
                $tmsurl = 'http://218.28.177.164/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
              }
              //echo $tmsurl;
              //$tmsurl = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount=jz41010110012983';
              $json_ret = file_get_contents($tmsurl);
              $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gbk");//转码，（这里只是个例子）
              $studentinfo = json_decode($json_ret, true);
              if($studentinfo['result'] > 0){
                 //var_dump($studentinfo['rtnArray'][0]);exit;
                // echo $studentinfo['rtnArray'][0]['student']['realname']."<br>";
                //echo $studentinfo['rtnArray'][0]['student']['studentNumber']."<br>";
                // echo $studentinfo['rtnArray'][0]['student']['schoolId']."<br>";
                // echo $studentinfo['rtnArray'][0]['student']['schoolClassId']."<br>";
                // echo $studentinfo['rtnArray'][0]['student']['gradeCode']."<br>";
                // exit;

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

  //获取LocalAreaCode
  public function getLocalAreaCode($str){
    if(count(explode('zzedu.',$str)) > 1){
      cookie('localAreaCode','1.');
    }else if(count(explode('henan.',$str)) > 1){
      cookie('localAreaCode','25.');
    }else if(count(explode('xc.',$str)) > 1){
      cookie('localAreaCode','27.');
    }else if(count(explode('fj.',$str)) > 1){
      cookie('localAreaCode','2.');
    }else if(count(explode('he.',$str)) > 1){
      cookie('localAreaCode','3.');
    }else if(count(explode('hl.',$str)) > 1){
      cookie('localAreaCode','6.');
    }else if(count(explode('sd.',$str)) > 1){
      cookie('localAreaCode','4.');
    }else if(count(explode('jl.',$str)) > 1){
      cookie('localAreaCode','7.');
    }else if(count(explode('zj.',$str)) > 1){
      cookie('localAreaCode','8.');
    }else if(count(explode('jx.',$str)) > 1){
      cookie('localAreaCode','13.');
    }else if(count(explode('ln.',$str)) > 1){
      cookie('localAreaCode','16.');
    }else if(count(explode('bx.',$str)) > 1){
      cookie('localAreaCode','30.');
    }else if(count(explode('tj.',$str)) > 1){
      cookie('localAreaCode','11.');
    }else if(count(explode('bj.',$str)) > 1){
      cookie('localAreaCode','24.');
    }else if(count(explode('gs.',$str)) > 1){
      cookie('localAreaCode','5.');
    }else if(count(explode('xj.',$str)) > 1){
      cookie('localAreaCode','22.');
      cookie('tmsurl','http://tmsgs.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('qh.',$str)) > 1){
      cookie('localAreaCode','26.');
      cookie('tmsurl','http://tmsgs.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('sn.',$str)) > 1){
      cookie('localAreaCode','18.');
    }else if(count(explode('nx.',$str)) > 1){
      cookie('localAreaCode','20.');
      cookie('tmsurl','http://tmssn.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('xz.',$str)) > 1){
      cookie('localAreaCode','21.');
      cookie('tmsurl','http://tmssn.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('sx.',$str)) > 1){
      cookie('localAreaCode','10.');
    }else if(count(explode('nm.',$str)) > 1){
      cookie('localAreaCode','23.');
    }else if(count(explode('ah.',$str)) > 1){
      cookie('localAreaCode','15.');
    }else if(count(explode('js.',$str)) > 1){
      cookie('localAreaCode','14.');
    }else if(count(explode('gx.',$str)) > 1){
      cookie('localAreaCode','12.');
    }else if(count(explode('guilin.',$str)) > 1){
      cookie('localAreaCode','32.');
      cookie('tmsurl','http://tmsgx.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('gd.',$str)) > 1){
      cookie('localAreaCode','17.');
    }else if(count(explode('hi.',$str)) > 1){
      cookie('localAreaCode','29.');
      cookie('tmsurl','http://tmsgd.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('yn.',$str)) > 1){
      cookie('localAreaCode','9.');
    }else if(count(explode('xy.',$str)) > 1){
      cookie('localAreaCode','19.');
    }else if(count(explode('jz.',$str)) > 1){
      cookie('localAreaCode','31.');
    }else if(count(explode('hn.',$str)) > 1){
      cookie('localAreaCode','28.');
    }else if(count(explode('dt.',$str)) > 1){
      cookie('localAreaCode','33.');
    }else if(count(explode('kw.',$str)) > 1){
      cookie('localAreaCode','34.');
    }else if(count(explode('hebi.',$str)) > 1){
      //鹤壁，与河南共建
      cookie('localAreaCode','35.');
      cookie('tmsurl','http://tmshenan.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('hb.',$str)) > 1){
      cookie('localAreaCode','37.');
      cookie('tmsurl','http://tmshn.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('hbhy.',$str)) > 1){
      cookie('localAreaCode','38.');
      cookie('tmsurl','http://tmshn.czbanbantong.com');
      cookie('ilearnurl',str_replace('tms','ilearn',cookie('tmsurl')));
      cookie('uburl',str_replace('tms','ub',cookie('tmsurl')));
    }else if(count(explode('yc.',$str)) > 1){
      cookie('localAreaCode','40.');
    }else if(count(explode('lj.',$str)) > 1){
      cookie('localAreaCode','39.');
    }else if(count(explode('dxal.',$str)) > 1){
      cookie('localAreaCode','41.');
    }else if(count(explode('dy.',$str)) > 1){
      cookie('localAreaCode','36.');
    }
  }

  public function lists(){
    $grade = I('grade/s');
    $type = I('type/d');
    if($grade =='0001'){
      $gradeName = '一年级';
    }else if($grade =='0002'){
      $gradeName = '二年级';
    }else{
      $gradeName = '三年级';
    }

    cookie('nowGrade',$grade);
    $schoolid=cookie('schoolId');
    $classid=cookie('classId');
    $userarea=cookie('localAreaCode');

    $usertype=cookie('usertype');

    // if($usertype==0){
    //   //家长
    //   $userid=cookie('student_number');
    // }else{
    //   $userid=cookie('username');
    // }

    $userid=cookie('username');

    $user=M('users');
    $data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('username')->find();
    $m=M('math');

    $sql_ks = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 1 AND m.grade = '%s' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";
    $sql_ts = "SELECT m.id,m.name,m.grade,m.keypoint,m.example,m.thinking,m.type,m.mathtype,m.sortid,MAX(z.stars) AS gold,(5 - MAX(z.stars)) AS gra FROM hl_math m LEFT JOIN (SELECT n.mathid,n.stars FROM hl_usersinfo n LEFT JOIN hl_users t ON n.usersid = t.id WHERE t.userid = '%s' AND t.classid = '%s' AND t.schoolid = '%s' AND t.userarea = '%s') z ON z.mathid = m.id WHERE m.mathtype = 2 AND m.grade = '%s' GROUP BY m.type,m.mathtype,m.id,m.name,m.keypoint,m.grade,m.example,m.thinking ORDER BY m.sortid;";

    $data_ks=$m->query($sql_ks,$userid,$classid,$schoolid,$userarea,$grade);
    $data_ts=$m->query($sql_ts,$userid,$classid,$schoolid,$userarea,$grade);
    //echo $sql_ks;
     //echo $userid.'|'.$classid.'|'.$schoolid.'|'.$userarea.'|'.$grade;
    //var_dump($data_ks);
     //exit();
    $this->assign('type',$type);
    $this->assign('data_ks',$data_ks);
    $this->assign('data_ts',$data_ts);
    $this->assign('grade',$gradeName);
    $this->display();
  }

/**
*排行榜页面
*/
  public function rank(){
    $type = I('type/d',1);
    ($type == 1)?$typename='口算排行':$typename='听算排行';

    $this->assign('type',$typename);
    $this->assign('truename',cookie('truename'));
    $this->display();
  }


public function getRankList(){
    $type = I('type/d',1);

    if ($type==1){
      $sql_gen='stars_ks';
    }else{
      $sql_gen='stars_ts';
    }


      $schoolid=cookie('schoolId');
      $classid=cookie('classId');
      $userarea=cookie('localAreaCode');
      $usertype=cookie('usertype');

      $sql_type_class='where userarea="'.$userarea.'" and classid="'.$classid.'" and schoolid="'.$schoolid.'"';
      $sql_type_school='where userarea="'.$userarea.'" and schoolid="'.$schoolid.'"';
      $sql_type=' ';

      $sql_my_class='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,'.$sql_gen.' FROM hl_users '.$sql_type_class.' ORDER BY '.$sql_gen.' desc) tmp,(SELECT @rank   := 0) a) RESULT) bb WHERE bb.id="'.cookie('userid').'"';

      $sql_my_school='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,'.$sql_gen.' FROM hl_users '.$sql_type_school.' ORDER BY '.$sql_gen.' desc) tmp,(SELECT @rank   := 0) a) RESULT) bb WHERE bb.id="'.cookie('userid').'"';
      $sql_my='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,'.$sql_gen.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc) tmp,(SELECT @rank   := 0) a) RESULT) bb WHERE bb.id="'.cookie('userid').'"';


      $sql_class='SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,username,'.$sql_gen.' FROM hl_users '.$sql_type_class.' ORDER BY '.$sql_gen.' desc limit 0,20) tmp,(SELECT @rank   := 0) a) RESULT';
      $sql_school='SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,username,'.$sql_gen.' FROM hl_users '.$sql_type_school.' ORDER BY '.$sql_gen.' desc limit 0,20) tmp,(SELECT @rank   := 0) a) RESULT';
      $sql='SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,username,'.$sql_gen.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc limit 0,20) tmp,(SELECT @rank   := 0) a) RESULT';

      $order_my_class=M()->query($sql_my_class);
      $order_my_school=M()->query($sql_my_school);
      $order_my=M()->query($sql_my);

      $order_class=M()->query($sql_class);
      $order_school=M()->query($sql_school);
      $order=M()->query($sql);

      $data['stars']=$order_my_class[0][$sql_gen];
      $data['order_class']=$order_class;
      $data['order_school']=$order_school;
      $data['order']=$order;
      $data['order_my_class']=$order_my_class[0]['rank'];
      $data['order_my_school']=$order_my_school[0]['rank'];
      $data['order_my']=$order_my[0]['rank'];

      $this->ajaxReturn($data);
}
//开始答题页面
public function start(){
  $bid=I('id/d');
  $type=I('type/d');
  $geshi=I('geshi/d');
  $grade=I('grade/s');
  $name = I('name/s');

  $this->assign('name',$name);
  $this->assign('grade',$grade);
  $this->assign('geshi',$geshi);
  $this->assign('type',$type);
  $this->assign('bid',$bid);
  $this->display();
}


   //获取15道试题信息
   public function getinfo(){
   	$bid=I('bid/d');
    $total_eq=15;


   	$data=M('mathinfo')->where('mathid="%d"',$bid)->select();

   	$data=$this->unique_array($data, $total_eq);

   //	var_dump($data);exit();
   	$this->ajaxReturn($data);

   }

   //结果展示页面
   public function result(){
    $right=I('right/d');
    $wrong=I('wrong/d');
    $usetime=I('usetime/d');
    $tmpstr=I('tmpstr/s');
    //$uburl=I('uburl/s');
    $name = I('name/s');

    $bid=I('bid/d');
    $type=I('type/d');
    $geshi=I('geshi/d');
    $grade=I('grade/s');


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

    if($star==5){
      $str='超级棒，你已经不需要再做此专题了';
    }else if($star==4){
      $str='棒棒哒！快去进行下一个练习吧';
    }else if($star==3){
      $str='合格了，撒花';
    }else if($star==2){
      $str='距离成功只有一步之遥';
    }else if($star==1){
      $str='革命尚未成功';
    }else{
      $str='继续努力';
    }

    $lxarr = array();
    $tmparr=explode('|',$tmpstr);
   // var_dump($tmparr);
    foreach ($tmparr as $key => $value) {
      //echo strlen($value).'<br>';
      if(strlen($value)>=5){
        array_push($lxarr, strlen($value));
      }
    }

      //file_put_contents('public/history.txt', '当前手机用户答题结束,用户id:'.cookie('userid')."\r\n", FILE_APPEND);

    // usersinfo增加用户信息记录
    $userinfo=M('usersinfo');
    $reUserinfo=$userinfo->where('mathid="%d" and usersid="%s"',$bid,cookie('userid'))->field('stars')->find();
    $userstar['usersid']=cookie('userid');
    $userstar['mathid']=$bid;
    $userstar['stars']=$star;
    $userstar['rightnum']=$right;
    $userstar['wrongnum']=$wrong;
    $userstar['countnum']=1;//测试用看是手机还是pc,0pc,1手机

    if (empty($reUserinfo)){
      $ubi=$this->getUbi(0,1,$right,$lxarr);
      $data['istop']='1';//是否历史最高
    }else{
      //获取历史最高正确个数
      $max=$userinfo->where('mathid="%d" and usersid="%s"',$bid,cookie('userid'))->max('rightnum');
      //获取今天练习次数
      $countnum=$userinfo->where('mathid="%d" and usersid="%s" and to_days(date)=to_days(now()) ',$bid,cookie('userid'))->count();

      $ubi=$this->getUbi($max,($countnum+1),$right,$lxarr);
      if($right>$max){
        $data['istop']='1';//是否历史最高
      }else{
        $data['istop']='0';//是否历史最高
      }
    }

    if(!empty(cookie('userid'))){
      $userinfo->add($userstar);
    }


    $user=M('users');
    //更新用户总星星
    if ($type==1){
      $sql='SELECT sum(n.totalstar) as totalstar FROM (SELECT max(l.stars) as totalstar FROM hl_usersinfo l,hl_math t WHERE l.mathid=t.id AND t.mathtype=1 AND l.usersid="%s" GROUP BY t.id) n';
      $re = M()->query($sql,cookie('userid'));

      $totalstars=$re[0]['totalstar'];

      $users['stars_ks']=$totalstars;
    }else {
      $sql='SELECT sum(n.totalstar) as totalstar FROM (SELECT max(l.stars) as totalstar FROM hl_usersinfo l,hl_math t WHERE l.mathid=t.id AND t.mathtype=2 AND l.usersid="%s" GROUP BY t.id) n';
      $re = M()->query($sql,cookie('userid'));

      $totalstars=$re[0]['totalstar'];
      $users['stars_ts']=$totalstars;
    }
    $user->where('id="%d"',cookie('userid'))->save($users);


    //调用U币接口

    $usertype=cookie('usertype');

    //$userid='01788';
    // if($usertype==0){
    //   //家长
    //   $userid=cookie('student_number');
    // }else{
    //   $userid=cookie('username');
    // }
    $userid=cookie('username');
    //echo 'Ubi:'.$ubi.'url:'.cookie('uburl').'<br>';exit;
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

		if( stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true
			|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		
		
      //$uburl = 'http://'.cookie('ubip');
      //$uburl=$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);

	  $uburl = cookie('ubip');
	  $uburl= $protocol.'://'.$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);


      $uburl=str_replace('%7B', '{', $uburl);
      $uburl=str_replace('%7D', '}', $uburl);

      //echo $uburl;exit;

      $curl = curl_init($uburl);
          curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
          curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果

          $responseText = curl_exec($curl);
          //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
          curl_close($curl);

          //$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

          $result = json_decode($responseText, true);
          //var_dump($result);

          $issuc=$result[0]['result'];
          if($issuc==1){
           // echo '添加成功';
            $data['status']='添加成功';
            //$data['ubi']=$result[0]['count'];
            $this->assign('ubi',$result[0]['count']);
          }else{
            $data['status']='添加失败';
            //$data['ubi']=0;
            $this->assign('ubi',0);
           // echo '添加失败';
          }

    }else{
      $this->assign('ubi',0);
    }





    $this->assign('name',$name);
    $this->assign('grade',$grade);
    $this->assign('geshi',$geshi);
    $this->assign('type',$type);
    $this->assign('bid',$bid);


    $this->assign('str',$str);
    $this->assign('right',$right);
    $this->assign('wrong',$wrong);
    $this->assign('usetime',$usetime);
    $this->assign('star',$star);
    $this->display();
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

      // if($usertype==0){
      // //家长
      //   $userid=cookie('student_number');
      // }else{
      //   $userid=cookie('username');
      // }
      $userid=cookie('username');

   		$user=M('users');
   		//查询用户users的id
   		$data_user=$user->where('userid="%s" and userarea="%s" and classid="%s" and schoolid="%s"',$userid,$userarea,$classid,$schoolid)->field('id')->find();


   		if ($gene=='口算'){
   			$sql_gen='stars_ks';
   		}else{
   			$sql_gen='stars_ts';
   		}
   		if ($type=='class'){
   			$sql_type='where '.$sql_gen.'<>0 and userarea="'.$userarea.'" and classid="'.$classid.'" and schoolid="'.$schoolid.'"';
   		}else if($type=='school'){
   			$sql_type='where '.$sql_gen.'<>0 and userarea="'.$userarea.'" and schoolid="'.$schoolid.'"';
   		}else{
   			$sql_type='where '.$sql_gen.'<>0  ';
   		}

   		$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;

   		$sql_my='SELECT rank,'.$sql_gen.' FROM (SELECT id,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,'.$sql_gen.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc) tmp,(SELECT @rank   := 0) a) RESULT) bb WHERE bb.id="'.$data_user['id'].'"';
   		$order_my=$user->query($sql_my);
   		//var_dump($order_my);exit();

   		$sql='SELECT id,username,'.$sql_gen.',rank FROM (SELECT tmp.id,tmp.username,tmp.'.$sql_gen.',@rank := @rank + 1 AS rank FROM (SELECT id,username,'.$sql_gen.' FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc '.$sql_limit.') tmp,(SELECT @rank   := 0) a) RESULT';

   		$orde=$user->query($sql);


   		$sql_total='SELECT count(*) as num FROM hl_users '.$sql_type.' ORDER BY '.$sql_gen.' desc ';

   		$data_total=$user->query($sql_total);
   		$total=$data_total[0]['num'];
   		if($total>20){
   			$total=20;
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


