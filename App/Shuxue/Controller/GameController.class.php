<?php
namespace Shuxue\Controller;
use Think\Controller;


class GameController extends CheckController {

	public function _initialize(){
		parent::_initialize();
        $ubname = cookie("ubname");
        $this->assign("ubname",$ubname);
    }

	public function index(){
		$gradeid = I("gradeid/s","");
		$subjectid = I("subjectid/s","");
		$moduleid = I("moduleid/s","");
		if($gradeid == ""){
			$gradeid = cookie("usergradeid");
		}
		cookie("moduleid",$moduleid);
		redirect("/Subject/Public/treeunit#/mathlist/?gradeid=".$gradeid."&subjectid=".$subjectid."&moduleid=".$moduleid);
		// $this->display();
	}

    public function lists(){
		$gradeid=I("gradeid");
        if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){
            cookie("gradeid",$gradeid);
        }
		$this->display();
	}
   
	public function getTerm(){
		$username = cookie('username');
    $usertype = cookie('usertype');
    $localareacode = cookie('localAreaCode');
		$data_term = M('user_term')->where('username="%s" and usertype="%s" and localareacode="%s"',$username,$usertype,$localareacode)->find();
		if(empty($data_term)){
			$data_term['termid'] = C('default_term');
			$data_term['username'] = $username;
			$data_term['localareacode'] = $localareacode;
			$data_term['usertype'] = $usertype;
			M('user_term')->add($data_term);
			$term = C('default_term');
			
		}else{
			$term = $data_term['termid'];
		}
		cookie('sx_termid',$term);
		$data['termCode'] = $term;
		$this->ajaxReturn($data);
	}

	public function setTerm(){
		$termCode = I('termCode/s','');

		$username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');
		$data_term = M('user_term')->where('username="%s" and usertype="%s" and localareacode="%s"',$username,$usertype,$localareacode)->find();
		if(empty($data_term)){
			$data_term['termid'] = $termCode;
			$data_term['username'] = $username;
			$data_term['localareacode'] = $localareacode;
			$data_term['usertype'] = $usertype;
			M('user_term')->add($data_term);
		}else{
			M('user_term')->where('username="%s" and usertype="%s" and localareacode="%s"',$username,$usertype,$localareacode)->setField('termid',$termCode);
		}
		cookie('sx_termid',$termCode);
		$info['msg'] = 'ok';
		$info['code'] = '1';
		$this->ajaxReturn($info);
	}

	//口算卡设置年级以及学科
	public function setGrade(){
		$gradeCode = I('gradeCode/s','');

		cookie('sx_gradeid',$gradeCode);

		cookie('sx_subjectid','0002');
		// $this->addLog('cookie:'.cookie('sx_gradeid'));
		$info['msg'] = 'ok';
		$info['code'] = '1';
		$this->ajaxReturn($info);
	}

	public function setGradeTermSubject(){
		$gradeid = I('gradeid/s','');
		$subjectid = I('subjectid/s','');
		// $termid = I('termid/s','');


		cookie('sx_gradeid',$gradeid);
		cookie('sx_subjectid',$subjectid);
		// cookie('sx_termid',$termid);

		// $this->addLog('cookie:'.cookie('sx_gradeid'));
		$info['msg'] = 'ok';
		$info['code'] = '1';
		$this->ajaxReturn($info);
	}

	public function genre(){
		$this->display();
	}
	public function genreting(){
		$gradeid=I("gradeid");
		if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){
            cookie("gradeid",$gradeid);
        }
		cookie('sx_subjectid','0002');
		$this->display();
	}
	public function addLog($sql){
		file_put_contents('666.txt',$sql);
	}
	public function setTestCookie(){
        $username = '41010110067990';
        $usertype = '4';
        $localareacode = '13.';
		// $gradeid = '0001';
		// 		$username = '41010100010241';
				// $username = '41010110043892';
				// $localareacode = '2.';
				// $username = '4101010000000010';
        // $usertype = '2';

		// cookie('sx_gradeid',$gradeid);
        cookie('username', $username);
        cookie('usertype', $usertype);
        cookie('localAreaCode', $localareacode);
	}
	
	//获取分类信息
	public function getGenres(){
		$grade = cookie('sx_gradeid');
		// $term = cookie('sx_termid');
		$subject = cookie('sx_subjectid');
		$username = cookie('username');
    $usertype = cookie('usertype');
    $localareacode = cookie('localAreaCode');

		// $sql = 'SELECT l.id,l.`name`,l.pic,l.total,l.studynum,CASE WHEN t.`status` is NULL THEN 0 ELSE t.`status` END status FROM sx_genre l LEFT JOIN (SELECT genreid,status FROM sx_user_genre WHERE genretype=1 and username="%s" AND usertype="%s" AND localareacode="%s" ) t ON l.id=t.genreid WHERE l.grade="%s" and l.term="%s" and l.subject="%s" and l.isdel=0 AND l.isshow=1 order by l.sortid';
		$sql = 'SELECT l.id,l.`name`,l.pic,l.total,IFNULL((SELECT studynum FROM sx_user_genre_study WHERE genreid = l.id),0) studynum,IFNULL(t.`status`,-1) status FROM sx_genre l LEFT JOIN (SELECT genreid,status FROM sx_user_genre WHERE genretype=1 and username="%s" AND usertype="%s" AND localareacode="%s" ) t ON l.id=t.genreid WHERE l.grade="%s" and l.subject="%s" and l.isdel=0 AND l.isshow=1 order by l.term,l.sortid';

		$data_genre = M()->query($sql,$username,$usertype,$localareacode,$grade,$subject);

		//  echo M()->getLastSql();exit;
		// $this->addLog(M()->getLastSql());

		$this->ajaxReturn($data_genre);
	}
	//获取分类信息
	public function getGenresByksk(){
		$grade = cookie('sx_gradeid');
		$term = cookie('sx_termid');
		$subject = cookie('sx_subjectid');
		$username = cookie('username');
    $usertype = cookie('usertype');
    $localareacode = cookie('localAreaCode');

		// $sql = 'SELECT l.id,l.`name`,l.pic,l.total,l.studynum,CASE WHEN t.`status` is NULL THEN 0 ELSE t.`status` END status FROM sx_genre l LEFT JOIN (SELECT genreid,status FROM sx_user_genre WHERE genretype=1 and username="%s" AND usertype="%s" AND localareacode="%s" ) t ON l.id=t.genreid WHERE l.grade="%s" and l.term="%s" and l.subject="%s" and l.isdel=0 AND l.isshow=1 order by l.sortid';
		$sql = 'SELECT l.id,l.`name`,l.pic,l.total,IFNULL((SELECT studynum FROM sx_user_genre_study WHERE genreid = l.id),0) studynum,IFNULL(t.`status`,-1) status FROM sx_genre l LEFT JOIN (SELECT genreid,status FROM sx_user_genre WHERE genretype=1 and username="%s" AND usertype="%s" AND localareacode="%s" ) t ON l.id=t.genreid WHERE l.grade="%s" and l.subject="%s" and l.term="%s" and l.isdel=0 AND l.isshow=1 order by l.sortid';

		$data_genre = M()->query($sql,$username,$usertype,$localareacode,$grade,$subject,$term);
		// echo M()->getLastSql();exit;
		// $this->addLog(M()->getLastSql());

		$this->ajaxReturn($data_genre);
	}

	//获取听算的分类
	public function getGenresByTing(){
		$grade = I("gradeid/s","0001");
	
		$username = cookie('username');
        $usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');
		
		$sql = 'SELECT l.id,l.name,IFNULL(t.`status`,-1) status FROM sx_math l LEFT JOIN (SELECT genreid,status FROM sx_user_genre WHERE genretype=2 AND username = "%s" AND usertype = "%s" AND localareacode = "%s") t ON l.id=t.genreid WHERE l.grade="%s" and l.mathtype=2';
		$data_genre = M()->query($sql,$username,$usertype,$localareacode,$grade);
		$this->ajaxReturn($data_genre);
	}

	//把用户进入当前关卡写入数据库
	//国勇那边调用这个接口，必须传入分类id和关卡ID
	public function addUserStage(){
		$stageid = I('stageid/d',0);
		$genreid = I('genreid/d',0);
		$subjectid = I('subjectid/s','');
		$gradeid = I('gradeid/s','');
		$isTing = I('isTing/d',0); //只有听算才会有这个,听算是1


		cookie('sx_subjectid',$subjectid);
		$grade = cookie('sx_gradeid',$gradeid);


		$username = cookie('username');
        $usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');


		$gradeid = $gradeid==""?cookie('sx_gradeid'):$gradeid;
		$subjectid = $subjectid ==""?cookie('sx_subjectid'):$subjectid;

		$data['gradeid'] = $gradeid;
		$data['subjectid'] = $subjectid;
		$data['genreid'] = $genreid;
		$data['stageid'] = $stageid;
		$data['status'] = 0;
		$data['username'] = $username;
		$data['usertype'] = $usertype;
		$data['localareacode'] = $localareacode;
		$data['addtime'] = date('Y-m-d H:i:s');

   if(cookie("areacode") == "1.1.13"){
    $res = M('df_user_stage')->field("id")->where("username='".$username."' and gradeid='".$gradeid."' and subjectid = '".$subjectid."' and genreid =  ".$genreid." and stageid=".$stageid)->find();
		if(!empty($res)){
			$userstageid = $res["id"];
		}
		else{
			$userstageid = M('df_user_stage')->add($data);
		}
		
	 }
	 else if(cookie("areacode") == "1.1.10"){
		$res = M('xy_user_stage')->field("id")->where("username='".$username."' and gradeid='".$gradeid."' and subjectid = '".$subjectid."' and genreid =  ".$genreid." and stageid=".$stageid)->find();
		// echo M('xy_user_stage')->getLastSql();exit;
		// var_dump($res);
		if(!empty($res)){
			$userstageid = $res["id"];
		}
		else{
			$userstageid = M('xy_user_stage')->add($data);
		}
		
	 }
	else{
		$res = M('user_stage')->field("id")->where("username='".$username."' and gradeid='".$gradeid."' and subjectid = '".$gradeid."' and genreid =  ".$genreid." and stageid=".$stageid)->find();
		if(!empty($res)){
			$userstageid = $res["id"];
		}
		else{
			$userstageid = M('user_stage')->add($data);
		}
		
	}

		$study = M('user_genre_study');
		if($isTing !=1){
			//不是听算就 增加记录人次
			$re = $study->where('genreid="%d"',$genreid)->find();
			if(empty($re)){
				$studyInfo['genreid'] = $genreid;
				$studyInfo['studynum'] = 1;
				$study->add($studyInfo);
			}else{
				$study->where('genreid="%d"',$genreid)->setInc('studynum');
			}
			$data_genre = M('genre')->where('id="%d"',$genreid)->field('grade,term,subject,name')->find();
		}else{
			//听算
			$sql = 'SELECT grade,"0002" as subject,name FROM sx_math WHERE id='.$genreid;
			$result = M()->query($sql);
			$data_genre['subject'] = $result[0]['subject'];
			$data_genre['grade'] = $result[0]['grade'];
			$data_genre['name'] = $result[0]['name'];
		}
		

		$info['userstageid'] = $userstageid;
		$info['subjectid'] = $data_genre['subject'];
		$info['gradeid'] = $data_genre['grade'];
		$info['name'] = $data_genre['name'];
		$this->ajaxReturn($info);
	}

	//获取当前关卡试题
	public function getQuesView(){
		$stageid = I('stageid/d',0);
		$type = I('type/d',0); //1各学科的2听算
		if($type == 1){
			$this->getQues($stageid);
		}else{
			$this->getQuesByKou($stageid);
		}		
	}

	//获取当前关卡试题信息
	public function getQues($stageid){
        // $stageid = I('stageid/d',0);
		
    $sql_ques = 'SELECT t.* FROM (SELECT quesid FROM sx_question  WHERE stageid='.$stageid.' and isdel=0 order by sortid) l  LEFT JOIN sx_base_question t ON  l.quesid=t.id';
		$sql_stage = 'SELECT remark,totaltime,rcode as r_code FROM sx_stage WHERE id ='.$stageid;
			
    $data_ques = M()->query($sql_ques);
		$data_stage = M()->query($sql_stage);
	
		if(cookie('sx_subjectid') == '0002'){
			if(count($data_ques)<15){
				//不够15道题
				$size = floor(15/count($data_ques));
				$data_ques_new = array();
				$data_ques_new = array_merge($data_ques_new,$data_ques);
				for($i=0;$i<$size;$i++){
					$data_ques_new = array_merge($data_ques_new,$data_ques);
				}
				if(count($data_ques_new)>15){
					shuffle($data_ques_new);
					array_splice($data_ques_new,15,count($data_ques_new)-15);	
				}
				$data_ques = $data_ques_new;
			}else if(count($data_ques)>15){
				shuffle($data_ques);
				array_splice($data_ques,15,count($data_ques)-15);
			}else{
				//刚好15道题
				shuffle($data_ques);
			}
		}else{
			if(count($data_ques)<5){
				//不够5道题
				$size = floor(5/count($data_ques));
				$data_ques_new = array();
				$data_ques_new = array_merge($data_ques_new,$data_ques);
				for($i=0;$i<$size;$i++){
					$data_ques_new = array_merge($data_ques_new,$data_ques);
				}
				if(count($data_ques_new)>5){
					shuffle($data_ques_new);
					array_splice($data_ques_new,5,count($data_ques_new)-5);	
				}
				$data_ques = $data_ques_new;
			}else if(count($data_ques)>5){
				shuffle($data_ques);
				array_splice($data_ques,5,count($data_ques)-5);
			}else{
				//刚好5道题
				shuffle($data_ques);
			}		
		}
		$info['ques'] = $data_ques;
		$info['stage'] = $data_stage;
		$info['pls_url'] = cookie("pls_url");
		$info['userName'] =  cookie('username');
        $this->ajaxReturn($info);		
	}

	//获取口算的试题信息
	public function getQuesByKou($stageid){
		// $stageid = I('stageid/d',0);
		$sql_ques = 'SELECT id,mathid as genreid,2 as questype,content,voice FROM sx_mathinfo WHERE mathid = '.$stageid;
		$sql_stage = 'SELECT keypoint,example,thinking,times as totaltime FROM sx_math WHERE id ='.$stageid;

		$data_ques = M()->query($sql_ques);
		$data_stage = M()->query($sql_stage);
		$data_stage[0]['remark'] = '';

		if(count($data_ques)<15){
			//不够15道题
			$size = floor(15/count($data_ques));
			$data_ques_new = array();
			$data_ques_new = array_merge($data_ques_new,$data_ques);
			for($i=0;$i<$size;$i++){
				$data_ques_new = array_merge($data_ques_new,$data_ques);
			}
			if(count($data_ques_new)>15){
				shuffle($data_ques_new);
				array_splice($data_ques_new,15,count($data_ques_new)-15);	
			}
			$data_ques = $data_ques_new;
		}else if(count($data_ques)>15){
			shuffle($data_ques);
			array_splice($data_ques,15,count($data_ques)-15);
		}else{
			//刚好15道题
			shuffle($data_ques);
		}


		$info['ques'] = $data_ques;
		$info['stage'] = $data_stage;
        $this->ajaxReturn($info);	

	}

	//进入当前分类
	public function goGenre(){
		$genreid = I('genreid/d',0);

		$username = cookie('username');
        $usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');
		
		$data = M('user_genre')->where('genreid="%d" and username="%s" and usertype="%s" and localareacode="%s"',$genreid,$username,$usertype,$localareacode)->find();
		if(empty($data)){

		}
	}

	//进入当前关卡(无用)
	public function goStage(){
		$stageid = I('stageid/d',0);

		$username = cookie('username');
        $usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');

		$data['stageid'] = $stageid;
		$data['status'] = 0;
		$data['username'] = $username;
		$data['usertype'] = $usertype;
		$data['localareacode'] = $localareacode;
		$data['addtime'] = date('Y-m-d H:i:s');
		if(cookie('areacode') == '1.1.13'){
			$res = M('df_user_stage')->field("id")->where("username='".$username."' and stageid=".$stageid)->find();
			if(!empty($res)){
				$id = $res["id"];
			}
			else{
				$id = M('df_user_stage')->add($data);
			}
		}
		else if(cookie('areacode') == '1.1.10'){
			$res = M('xy_user_stage')->field("id")->where("username='".$username."' and stageid=".$stageid)->find();
			if(!empty($res)){
				$id = $res["id"];
			}
			else{
				$id = M('xy_user_stage')->add($data);
			}
		}
		else{
			$res = M('user_stage')->field("id")->where("username='".$username."' and stageid=".$stageid)->find();
		if(!empty($res)){
			$id = $res["id"];
		}
		else{
			$id = M('user_stage')->add($data);
		}
		}
		$info['userstageid'] = $id;
		$this->ajaxReturn($info);
	}

	//记录每道题的对错
	public function addUserAnswer(){
		$userstageid = I('userstageid/d',0);
		$stageid = I('stageid/d',0);
		$quesid = I('quesid/d',0);
		$isright = I('isright/d',0);
		$type = I('type/d',0);
		$userselect = I('userselect/s','');

		$username = cookie('username');
		$usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');

		$data['userstageid'] = $userstageid;
		$data['questionid'] = $quesid;
		$data['isright'] = $isright;
		$data['type'] = $type;

		M('user_stage_info')->add($data);

		if($userselect == ''){
			$userselect = 100;
		}
		if($isright === 0 ){
			//自动加入错题本
			// $result = M('stage')->where('id="%d"',$stageid)->field('genreid')->find();
			if(cookie('areacode') == '1.1.13'){
				$result = M('df_user_stage')->where('id="%d"',$userstageid)->field('genreid')->find();
			}
			else if(cookie('areacode') == '1.1.10'){
				$result = M('xy_user_stage')->where('id="%d"',$userstageid)->field('genreid')->find();
			}
		else{
			$result = M('user_stage')->where('id="%d"',$userstageid)->field('genreid')->find();
		}
			$info = M('user_wrong')->where('genreid="%d" and quesid="%d" and isdel=0 and username="%s" and usertype="%s" and localareacode="%s"',$result['genreid'],$quesid,$username,$usertype,$localareacode)->find();
			$data_wrong['username'] = $username;
			$data_wrong['usertype'] = $usertype;
			$data_wrong['localareacode'] = $localareacode;
			$data_wrong['genreid'] = $result['genreid'];
			$data_wrong['quesid'] = $quesid;
			$data_wrong['userselect'] = $userselect;
			$data_wrong['isdel'] = 0;
			if(empty($info)){
				M('user_wrong')->add($data_wrong);
			}else{
				M('user_wrong')->where('genreid="%d" and quesid="%d" and isdel=0 and username="%s" and usertype="%s" and localareacode="%s"',$result['genreid'],$quesid,$username,$usertype,$localareacode)->setField('userselect',$userselect);
			}
		}
	}


	//获取错题本内容
	public function getWrongView(){
		$genreid = I('genreid/d',0);

		$username = cookie('username');
		$usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');

		$sql = 'SELECT l.userselect,t.* FROM sx_user_wrong l,sx_base_question t WHERE l.quesid=t.id and l.genreid="%d" and l.isdel=0 and l.username="%s" and l.usertype="%s" and l.localareacode="%s"';

		$data = M()->query($sql,$genreid,$username,$usertype,$localareacode);
		
		$answerSelect = array();
		foreach($data as $v){
			$str = $v['userselect'];
			if(strlen($str) == 1){
				array_push($answerSelect,$str);	
			}else{
				$arr = str_split($str);
				array_push($answerSelect,$arr);	
			}
		}

		$stage[0]['totaltime'] = 0;
		$stage[0]['remark'] = '';
		$info['ques'] = $data;
		$info['stage'] = $stage;
		$info['useranswer'] = $answerSelect;
		$this->ajaxReturn($info);
	}

	//清空错题本
	public function emptyWrongNotes(){
		$genreid = I('genreid/d',0);

		$username = cookie('username');
		$usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');
		M('user_wrong')->where('genreid="%d" and isdel=0 and username="%s" and usertype="%s" and localareacode="%s"',$genreid,$username,$usertype,$localareacode)->setField('isdel',1);
	}

	//获取当前分类下的关卡信息
	public function getStagesByGenre(){
		// $genreid = I('genreid/d',0);
		$request=I("request");
		$request = rtrim($request, '"');
		$request = ltrim($request, '"');
		$request = str_replace('&quot;', '"', $request);
		$request = json_decode($request,true);

		$genreid = $request['genreid'];

		$username = cookie('username');
    $usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');

		$sql = "SELECT l.id,l.stagename,CASE WHEN t.stageid is NULL THEN 0 ELSE 1 END ltype	 FROM ";
		$sql .= "( SELECT id, stagename,sortid FROM sx_stage WHERE genreid = '%d' AND isdel = 0 order by sortid) l ";
		$sql .=" LEFT JOIN ";
		$sql .=" ( SELECT stageid FROM sx_user_stage WHERE username = '%s' AND usertype = '%s' AND localareacode = '%s' AND STATUS = 1 GROUP BY stageid ) t ";
		$sql .= " ON l.id = t.stageid order by l.sortid";

		$data_stages = M()->query($sql,$genreid,$username,$usertype,$localareacode);
		// echo M()->getLastSql();exit;
		// var_dump($data_stages);
		
		$count = count($data_stages);
		$result = array();
		$result['count'] = $count;
		$flag = true;
		foreach($data_stages as $k=>$v){
			$info['id'] = $v['id'];
			$info['name']= $v['stagename'];
			$ltype = $v['ltype'];
			if($flag){
				if($ltype==0){
					$flag = false;
					$ltype = 1;
				}
			}
			
			$info['ltype'] = $ltype;
			$info['index'] = $k;
			$result['data'][$k] = $info;
		}

		$data_user = M('db_english.userinfo','engs_')->where('isdel=0 and username="%s" and usertype="%s" and localareacode="%s"',$username,$usertype,$localareacode)->find();
		$result['userinfo'] = $data_user;

		$this->ajaxReturn($result);
	}

	//检查是否有下一关
	public function checkHasNextStage(){
		$stageid =I('stageid/d',0);
		$type =I('type/d',0);//1口算 2 听算
		
		if($type == 1){
			$data_stage = M('stage')->where('id="%d" and isdel=0',$stageid)->field('genreid,sortid')->find();
			$sql = 'select id from sx_stage where isdel=0 and genreid="%d" and sortid>"%d" order by sortid limit 1';
			$data = M()->query($sql,$data_stage['genreid'],$data_stage['sortid']);
		}else{
			$data_stage = M('math')->where('id="%d"',$stageid)->field('grade,sortid')->find();
			$sql = 'select id from sx_math where grade="%s" and mathtype=2 and sortid>"%d" order by sortid limit 1';
			$data = M()->query($sql,$data_stage['grade'],$data_stage['sortid']);
		}


		if(empty($data)){
			$info['msg'] = '没有下一关';
			$info['code'] = '0';
		}else{
			$info['msg'] = $data[0]['id'];
			$info['code'] = '1';
		}
		$this->ajaxReturn($info);
	}

	//获取闯关结果
	public function getResult(){
		$moduleid = cookie("moduleid");
		$status = I('status/s',0);//闯关状态 0失败 1成功
		$type = I('type/d',0); //1口算 2听算
		$stageid = I('stageid/d',0);
		$userstageid = I('userstageid/d',0);//用户历史记录id
		$rightNum = I('rightNum/d',0);//用户历史记录id

		$subject = cookie('sx_subjectid');
		$grade = cookie('sx_gradeid');
		// $term = cookie('sx_termid');
		$username = cookie('username');
		$usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');
		$ub = 0;
		$user_stage_info = M('user_stage_info');
		$user_stage = M('user_stage');
		$df_user_stage = M('df_user_stage');
		$res = $df_user_stage->field("addtime")->where('id="%d"',$userstageid)->find();
		$data_stage['addtime'] = date('Y-m-d H:i:s');
		if($areaCode == "1.1.13" && $moduleid == "63"){
			$data_stage["endtime"] = date("Y-m-d H:i:s");
			// echo $data_stage["endtime"];exit;
			$data_stage["spendtime"] = (strtotime(date("Y-m-d H:i:s"))-strtotime($res['addtime']));
		}
		
		if($status == 1){
			//闯关成功才有U币
			// $data_user = M('db_english.userinfo','engs_')->where('isdel=0 and username="%s" and usertype="%s" and localareacode="%s"',$username,$usertype,$localareacode)->find();
			// $user_grade = $data_user['gradeid'];

			// if($user_grade*1>cookie('sx_gradeid')*1){
			// 	//高年级做低年级的题目不给U币
			// 	$ub = 0;
			// }else{
			// 	$ub_sum = $user_stage->where('stageid="%d" AND username="%s" AND localareacode="%s" AND usertype="%d"',$stageid,$username,$localareacode,$usertype)->sum('ub');
			// 	empty($ub_sum)?$ub_sum = 0:$ub_sum;
			// 	$ub = $rightNum - $ub_sum;
			// }

			if($usertype == 4){
				$ub_sum = $user_stage->where('stageid="%d" AND username="%s" AND localareacode="%s" AND usertype="%d"',$stageid,$username,$localareacode,$usertype)->sum('ub');
				empty($ub_sum)?$ub_sum = 1:$ub_sum = 0;
				//$ub = $rightNum - $ub_sum;
				$ub = $ub_sum;
			}else{
				//老师没有U币
				$ub = 0;
			}

			// $data = $user_stage_info->where('userstageid="%d" and isright=1 and type="%d"',$userstageid,$type)->field('questionid')->select();
			// foreach($data as $v){
			// 	$questionid = $v['questionid'];
			// 	$sql = 'SELECT * FROM sx_user_stage_info WHERE questionid="%d" AND isright=1 AND userstageid in ';
			// 	$sql .= '(SELECT id FROM sx_user_stage WHERE id<>"%d" AND username="%s" AND localareacode="%s" AND usertype="%d" AND stageid="%d")';
			// 	// $data_right =$user_stage_info->where('questionid="%d" and type="%d" and userstageid<>"%d" and isright=1',$questionid,$type,$userstageid)->find();
			// 	$data_right = M()->query($sql,$questionid,$userstageid,$username,$localareacode,$usertype,$stageid);
			// 	// echo M()->getLastSql();exit;
			// 	if(empty($data_right)){
			// 		//以前没做对过，才送U币
			// 		$ub++;
			// 	}
			// }
			if($areaCode == "1.1.13" && $moduleid == "63"){
				$data_stage['score'] = 1;
			}
		}
		

		$dataUb = M('user_stage')->where('id="%d"',$userstageid)->find();

		$data_stage['status'] = $status;
		$data_stage['ub'] = $ub + $dataUb['ub'];


		


		//验证是否送U币
    if($ub>0){
			//送U币
			$c = 'u_pass_check_zxzs';
			$n = 1;
			$action = 'z';
			$result = getUbiInterface($c,$n,$action,"my","my"); //测试注释

			// $result = false;
			if($result[0]['result'] == 1){
				//送U币成功
				$re['status'] = 1;
				$re['msg'] = '获得U币';
				$re['ub'] = $n;
				$re['isSuccess'] = $status;
				M('user_stage')->where('id="%d"',$userstageid)->save($data_stage);
			}else{
				//调用U币接口失败
				$re['status'] = 2;
				$re['msg'] = $result[0]['info'];
				$re['ub'] = 0;
				$re['isSuccess'] = $status;
				file_put_contents('./log/uberr.log',$ub.'|'.$re['msg'].PHP_EOL,FILE_APPEND);
				M('user_stage')->where('id="%d"',$userstageid)->save($data_stage);
				if($areaCode == "1.1.13" && $moduleid == "63"){
					M('df_user_stage')->where('id="%d"',$userstageid)->save($data_stage);
				}
			}
		}else{
			if($status == 1){
				//老师U币为0，但是闯关成功
				M('user_stage')->where('id="%d"',$userstageid)->save($data_stage);
				if($areaCode == "1.1.13" && $moduleid == "63"){
					M('df_user_stage')->where('id="%d"',$userstageid)->save($data_stage);
				}
			}
			$re['status'] = 1;
			$re['msg'] = '未获得U币';
			$re['ub'] = 0;
			$re['isSuccess'] = $status;
		}
		$re['subjectid'] = $subject;

		$this->ajaxReturn($re);
	}

	//检查U币是否达到最大
	public function checkUbIsMax(){
		return false;
	}

	//检查当前分类的用户做题状态
	public function checkUserGenreStatus(){
		$type = I('type/d',0); //1口算 2听算
		$stageid = I('stageid/d',0);

		$grade = cookie('sx_gradeid');
		// $term = cookie('sx_termid');
		$username = cookie('username');
		$usertype = cookie('usertype');
		$localareacode = cookie('localAreaCode');

		if($type == 1){
			//口算
			$data_stage = M('stage')->where('id="%d"',$stageid)->field('genreid')->find();
			$genreid = $data_stage['genreid'];
	
			$sql = 'SELECT id FROM (SELECT id,stagename FROM sx_stage  WHERE genreid="%d" AND isdel=0) l LEFT JOIN  ';
			$sql .= '(SELECT stageid,status FROM sx_user_stage WHERE status=1 AND username="%s" AND usertype="%s" AND localareacode="%s" GROUP BY stageid) t ';
			$sql .= 'ON l.id = t.stageid WHERE t.stageid is not null;';
			$data_genre = M()->query($sql,$genreid,$username,$usertype,$localareacode);
			
			//查询当前分类下的关卡数量
			$total = M('stage')->where('genreid="%d" and isdel=0',$genreid)->count(); 
			$complateNum = count($data_genre);//当前分类闯关成功的关卡个数
			if($total == $complateNum){
				//当前分类通关
				$status = 2;
			}else{
				//当前分类未通关
				if($complateNum>0){
					$status = 1;
				}else{
					$status = 0;
				}
			}
			$genretype = 1;
		}else{
			//听算
			$genreid = $stageid;
			
			$sql = 'SELECT stageid,status FROM sx_user_stage WHERE stageid="%d" AND status=1 AND username="%s" AND usertype="%s" AND localareacode="%s" GROUP BY stageid';
			$data_genre = M()->query($sql,$stageid,$username,$usertype,$localareacode,$grade);
			$complateNum = count($data_genre);//当前分类闯关成功的关卡个数
			if($complateNum>0){
				//当前分类通关
				$status = 2;
			}else{
				//当前分类未通关
				$status = 0;
			}
			$genretype = 2;
		}

		//查询用户当前分类下的做题状态 0 未闯关成功 1 闯关中 2所有关卡闯关成功,且只记录一条
		$data = M('user_genre')->where('genreid="%d" and username="%s" and usertype="%s" and localareacode="%s"',$genreid,$username,$usertype,$localareacode)->find();
		if(empty($data)){
			$data_user_genre['genreid'] = $genreid;
			$data_user_genre['status'] = $status;
			$data_user_genre['username'] = $username;
			$data_user_genre['usertype'] = $usertype;
			$data_user_genre['localareacode'] = $localareacode;
			$data_user_genre['genretype'] = $genretype;
			M('user_genre')->add($data_user_genre);
		}else{
			M('user_genre')->where('genreid="%d" and username="%s" and usertype="%s" and localareacode="%s"',$genreid,$username,$usertype,$localareacode)->setField('status',$status);
		}
	}


	public function getRemark(){
		$stageid = I('stageid/d',0);
		$data = M('stage')->where('id="%d"',$stageid)->field('remark')->find();
		$this->ajaxReturn($data);
	}


    //获取总排行榜
    public function getTotalRank(){
			$genreid= I('genreid/d',0);
			$pageCurrent = I('pageCurrent/d',0);
			$pageSize = I('pageSize/d',0);
			$type = I('type/s','class');//class:班级;school:学校;all:全国
			$time = I('time/s','a');//w:周;m:月；a：总榜

			$username = cookie('username');
			$usertype = cookie('usertype');
			$localareacode = cookie('localAreaCode');
			$userclassid = cookie('classid');
			$userschoolid = cookie('schoolid');
			
			//时间范围
			$times = "";
			if($time == 'w'){
					$times = date("Y-m-d",strtotime("-7 day"));
			}else if($time == 'm'){
					$times = date("Y-m-d",strtotime("-30 day"));
			}

			
			//类型选择
			$classid = "";
			$schoolid = "";
			if($type == 'class'){
					$classid = $userclassid;
					$schoolid = "";
					$configid = "36";
			}else if($type == 'school'){
					$classid = "";
					$schoolid = $userschoolid;
					$configid = "39";
			}
			else{
				$configid = "27";
			}
			//从redis中获取数据
			//echo $configid;
			$rankService = D("Subject/Rank","Service");
			$result = $rankService -> getUserMathRank($classid,$schoolid,$times,$configid);
			
			$my = array();
			$myflag = true;
			foreach($result as $key=>$value){
					$result[$key]["rank"]=($key+1);
					$result[$key]["truename"]=$value["yjt_truename"];
					$result[$key]["classname"]=$value["yjt_classname"];
					$result[$key]["schoolname"]=$value["yjt_schoolname"];
					$result[$key]["total"]=-1;
					if($username == $value["username"] && $myflag){
							$my = $value;
							$my["rank"]=$key+1;
							$myflag = false;
					}
			}
			//var_dump($result);exit;
			$data["info"] = $result;
			//数组的组合和兼容
			$hasRank = true;
			if($my["total"] == 0){
					$hasRank = false;
			}
			$my['mytotal'] = $my["total"];
			$my['myub'] = $my["ub"];
			$my['myRank'] = $my["rank"];
			$data["myinfo"] = $my;
			$this -> ajaxReturn($data); 

	}

	//获取我的排名
	public function getMyRank($usertype,$username,$localareacode,$classid,$apptype,$sql_time,$sql,$sql_where,$sql_group,$sql_order){
			$my = array();

			if($usertype == 4){
					//学生或者家长
					$sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "%d" AND username="%s" and classid="%s"';
			}else{
					//教师
					$sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "%d" AND username="%s" and classid="%s"';				
			}

			$data_user = M()->query($sql_user,$localareacode,$usertype,$username,$classid);


			$my['classname'] = $data_user[0]['classname'];
			$my['truename'] = $data_user[0]['truename'];
			$my['schoolname'] = $data_user[0]['schoolname'];
			$my['userpic'] = $data_user[0]['userpic'];

			//查询我的当前u币数和篇数
			$sql_my = 'SELECT -1 as total,sum(m.ub) as ub FROM db_math.sx_user_stage m,db_english.engs_userinfo n';
			$sql_my_where = ' and m.username="'.$username.'"';
			// echo $sql_my.$sql_where.$sql_my_where;exit;
			$data_my = M()->query($sql_my.$sql_where.$sql_my_where);//加where条件


			if($data_my[0]['ub'] == 0){
					//没有u币
					$hasRank = false;
					$mytotal = 0;
					$myub = 0;
			}else{
					$myub = $data_my[0]['ub'];
					$mytotal = $data_my[0]['total'];
					$hasRank = true;
			}

			$my['hasRank'] = $hasRank;
			$my['mytotal'] = $mytotal;
			$my['myub'] = $myub;


			//获取我的当前排名
			if($hasRank){
					//如果U币相同，就按时间排序
					$sql_having_same = ' having sum(m.ub)='.$myub;
					// echo $sql.$sql_where.$sql_group.$sql_having_same.$sql_order;exit;
					$data_is_same = M()->query($sql.$sql_where.$sql_group.$sql_having_same.$sql_order);
					if(count($data_is_same)==1){
							//没有相同的
							$is_same = false;
					}else{
							//都相同按时间
							$is_same = true;
							$arr_index = array_column($data_is_same,'username');
							$index = array_search($username, $arr_index);
					}

					$sql_having = ' having sum(m.ub)>'.$myub;
					$sql_my_rank = $sql.$sql_where.$sql_group.$sql_having;
					// echo $sql_my_rank;exit;
					$data_my_rank = M()->query($sql_my_rank);
					if(empty($data_my_rank)){
						//没有大于我的
							if($is_same){
									$myrank = 1+$index;
							}else{
									$myrank = 1;
							}
					}else{
							if($is_same){
									$myrank = count($data_my_rank)+1+$index;
							}else{
									$myrank = count($data_my_rank)+1;
							}
					}
			}else{
					$myrank = 0;
			}

			$my['myRank'] = $myrank;

			return $my;
	}



	public function getLocalareacode(){
		$data['localareacode'] = cookie('localAreaCode');
		$this->ajaxReturn($data);
	}

























}