<?php
namespace Homework\Controller;
use Think\Controller;
/**
* 网上作业控制器类
*/

class ComhwController extends Controller {
    //首页
    public function index(){
       // echo special_character("p");
        $tms=I("sso");
        cookie("tms",$tms);
        $yjArr=get_ip("",cookie("tms"));
        $sso=$yjArr['sso']['title'];
        cookie("sso",$sso);
        $tqms=$yjArr['tqms']['title'];
        $this->assign("tqms",$tqms);
        $ks_code=I("ks_code");
        $username=I("username"); 
        session("ks_code",$ks_code);
        session("username",$username);
        session("tqms",$tqms);
        cookie("tqms",$tqms);
        $username=I("username");
        //查询该单元下边是否有单词，课文及试卷资源
        $gradeinfo = M('rms_unit')->field('r_grade')->where('ks_code="%s"',$ks_code)->find();
        $this->assign('grade',$gradeinfo['r_grade']);
        //$this->assign('grade','0001');
        $sql="select count(*) as num,'0' as type from engs_word where ks_code='".$ks_code."' and isdel = 1 union select count(*) as num,'1' as type from engs_text_chapter  where ks_code='".$ks_code."' and isdel=1 union select count(*) as num,'2' as type from engs_exams where ks_code='".$ks_code."' and isdel=1";
        //echo $sql;
        $rs=M()->query($sql);
        $countnum = 0; 
        foreach ($rs as $key => $value) {
            $countnum = $countnum + $value['num'];
        }
        if($countnum == 0){
          $this->assign("result","0");
          $this->assign("wc","0");
          $this->assign("tc","0");
          $this->assign("ec","0");
        }
        else{
          $this->assign("result",$countnum);
          $sql="select a.id, a.word,a.base_wordid,a.base_explainsid,a.isstress,a.chaptername,b.isword from engs_word a left join engs_base_word b on a.base_wordid = b.id where a.ks_code='".$ks_code."' and a.isdel = 1 order by a.sortid,id";
          $wa=M()->query($sql);
          $totalstressnum = 0;
          if(count($wa) == 0){
            $this->assign("wc","0");
          }else{
            //查询单词是否有小节分组
            $sql = "select chaptername from engs_word where ks_code = '".$ks_code."' and isdel=1 and chaptername!='' group by chaptername";
            $chapternamelist = M()->query($sql);
            //var_dump($chapternamelist);
            if(count($chapternamelist) > 0){   //单词有分组
                foreach ($chapternamelist as $key => $value) {
                    $tempnum = 0;
                    $wordlist = array();
                    $wordpxlist = array();   
                        foreach ($wa as $wakey => $wavalue) {
                            if($wavalue['chaptername'] == $value['chaptername']){
                                array_push($wordlist, $wavalue);
                            }
                            if($wavalue['chaptername'] == $value['chaptername']  && $wavalue['isword'] == 1 && special_character($wavalue['word'])){
                                array_push($wordpxlist, $wavalue);
                                if($wavalue['isstress'] == 1){
                                    $tempnum ++;
                                }
                            }
                        }
                    $totalstressnum += count($wordpxlist);
                    $chapternamelist[$key]['wordlistnum'] = count($wordlist); 
                    $chapternamelist[$key]['wordlist'] = $wordlist;
                    $chapternamelist[$key]['wordpxlist'] = $wordpxlist;
                    $chapternamelist[$key]['wordpxlistnum'] = count($wordpxlist);
                    $chapternamelist[$key]['stresslistnum'] = $tempnum;
                }
            }
            else{ //单词没有分组
                $chapternamelist[0]['chaptername'] = '全选';
                $chapternamelist[0]['wordlistnum'] = count($wa);
                $chapternamelist[0]['wordlist'] = $wa;
                $wordpxlist = array(); 
                $tempnum = 0;
                foreach ($wa as $wakey => $wavalue) {
                    if($wavalue['isword'] == 1 && special_character($wavalue['word'])){
                        array_push($wordpxlist, $wavalue);
                        if($wavalue['isstress'] == 1){
                            $tempnum ++;
                        }
                    }
                }
                $totalstressnum = count($wordpxlist);
                $chapternamelist[0]['wordpxlist'] = $wordpxlist;
                $chapternamelist[0]['wordpxlistnum'] = count($wordpxlist);
                $chapternamelist[0]['stresslistnum'] = $tempnum;
            }
            

            $this->assign("wc",count($chapternamelist));
            $this->assign("chapternamelist",$chapternamelist);
            $this->assign("totalstressnum",$totalstressnum);
          }
          // if(!preg_match("/^[A-Za-z0-9]+$/",$str)){
          //       ts("不能包含中文和特殊字符！");
          //        exit();
          //   }

          $rs=M("text_chapter")->where("ks_code='%s' and isdel=1 and isevaluate = 1 ",$ks_code)->order('sortid,id')->select();
          
          $tc=count($rs);
          if($tc == 0){
            $this->assign("tc","0");
          }
          else{
            foreach ($rs as $key => $value) {
                $textnum = M('text')->field('id')->where('chapterid='.$value['id'].' and isdel = 1')->count();
                $rs[$key]['textnum']= $textnum;
            }
            $this->assign("tc",$tc);
            $this->assign("chapterlist",$rs);

          }
          $rs=M("exams")->field('id,name,quescount')->where("ks_code='%s' and isdel=1 and state=4",$ks_code)->select();
          $eq=count($rs);
          if($eq==0){
            $this->assign("ec","0");
          }else{
            $this->assign("ec",$eq);
            $this->assign("examslist",$rs);
          }
        }
        $this->assign("ks_code",$ks_code);
        $this->assign("username",$username);
        $screen_width_id = cookie('screen_width_id');
        $classscreen = 'w900';
        if($screen_width_id == 'w980'){
           $classscreen = 'w700';
        }
        $this->assign("screen_width_id",$screen_width_id);
        $this->assign("classscreen",$classscreen);
        $this->display('index');
    }


    public function preview(){
       $ks_code=session("ks_code");
       $username=session("username");
       $tqms = session('tqms');
        //取出单元名字
       $ksrs=M("rms_unit")->where("ks_code='%s'",$ks_code)->find();

       $this->assign("ks_name",$ksrs['ks_name']." 听读作业 ".Date('nd-Hi'));
    	 $post=$_POST;
    	//6种作业资格数组
    	$wordaloundarr=Array(); //单词跟读
        $wordpxarr=Array();     //单词拼写
        $wordyharr=Array();     //英汉互译
    	$worxcarr=Array();     //听音选词
    	$textaloundarr=Array();    //课文跟读
    	$examsaloundarr=Array();   //听力训练
        $wordaloundarrfb=Array(); //单词跟读
        $wordpxarrfb=Array();     //单词拼写
        $wordyharrfb=Array();     //英汉互译
        $worxcarrfb=Array();     //听音选词
        $wordfb=Array(); //单词发布数组
        $textfb=Array();    //课文发布数组
        $examsfb=Array();   //听力训练发布数组
        $wordfakey = 0;
    	foreach($post as $key=>$value){
    		if(stripos($key,"check-01")){
               array_push($wordaloundarr, $value);
               $wordfb[$wordfakey]['id']=$value;
               $wordfb[$wordfakey]['typeid']=0;
               $wordfb[$wordfakey]['quescount']=1;
               $wordfakey++;
    		}else if(stripos($key,"check-02")){
               array_push($wordpxarr, $value);
               $wordfb[$wordfakey]['id']=$value;
               $wordfb[$wordfakey]['typeid']=1;
               $wordfb[$wordfakey]['quescount']=1;
               $wordfakey++;
    		}else if(stripos($key,"check-03")){
               array_push($wordyharr, $value);
               $wordfb[$wordfakey]['id']=$value;
               $wordfb[$wordfakey]['typeid']=3;
               $wordfb[$wordfakey]['quescount']=1;
               $wordfakey++;
    		}else if(stripos($key,"check-04")){
               array_push($worxcarr, $value);
               $wordfb[$wordfakey]['id']=$value;
               $wordfb[$wordfakey]['typeid']=2;
               $wordfb[$wordfakey]['quescount']=1;
               $wordfakey++;
    		}
            else if(stripos($key,"check-05")){
               array_push($textaloundarr, $value);
            }
            else if(stripos($key,"check-06")){
               array_push($examsaloundarr, $value);
            }
    	}
        //var_dump($wordfb);
    	//分别获取具体的信息
    	$waids=implode(",",$wordaloundarr); //单词跟读
        $wpids=implode(",",$wordpxarr); //单词拼写
        $wyids=implode(",",$wordyharr); //英汉互译
    	$wxids=implode(",",$worxcarr); //听音选词
    	$taids=implode(",",$textaloundarr); //课文跟读
    	$eqids=implode(",",$examsaloundarr); //听力训练
    	$this->assign("waids",$waids);
    	$this->assign("wpids",$wpids);
        $this->assign("wyids",$wyids);
        $this->assign("wxids",$wxids);
    	$this->assign("taids",$taids);
    	$this->assign("eqids",$eqids);
        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');
        $cardarr = array();
        if($waids != ''){
           $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'单词跟读';
           $cardarr[$cardnum]['cardlist'] = $wordaloundarr;
           $cardnum++;
        }
        if($wpids != ''){
           $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'单词拼写';
           $cardarr[$cardnum]['cardlist'] = $wordpxarr;
           $cardnum++;
        }
        if($wxids != ''){
           $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'听音选词';
           $cardarr[$cardnum]['cardlist'] = $worxcarr;
           $cardnum++;
        }
        if($wyids != ''){
           $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'英汉互译';
           $cardarr[$cardnum]['cardlist'] = $wordyharr;
           $cardnum++;
        }
       
        if($taids != ''){
           $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'课文跟读';
           
           $textarr = Array();
            foreach ($textaloundarr as $key => $value) {
                $textnum = M('text')->field('id')->where('chapterid='.$value.' and isdel = 1')->select();
                $textaloundarrfb[$key]['id']=$value;
                $textaloundarrfb[$key]['typeid']=1;
                $textaloundarrfb[$key]['quescount']=count($textnum);
                foreach ($textnum as $textskey => $textsvalue) {
                   array_push($textarr, $textsvalue);
                 }
                
            }
              $cardarr[$cardnum]['cardlist'] = $textarr;
              $cardnum++;
        }

        if($eqids != ''){
            $rs=M("exams")->field('id,name,quescount')->where("id=%s and isdel=1",$eqids)->find();
            $cardarr[$cardnum]['title'] = $arraynum[$cardnum].'听力训练';
            $eqnumarr = array();
            for ($i=0; $i < $rs['quescount']; $i++) { 
                array_push($eqnumarr, $i);
            }
            $cardarr[$cardnum]['cardlist'] = $eqnumarr;
            $examsaloundarrfb[0]['id'] = $eqids;
            $examsaloundarrfb[0]['quescount'] = $rs['quescount'];
            $examsaloundarrfb[0]['typeid'] = 1;
        }
            
        //var_dump($cardarr);
        //拼装发布作业数组
        $homeworkarr = array();
        $homeworkarr['ks_code'] = $ks_code;
        $homeworkarr['username'] = $username;
        $homeworkarr['source'] = 0;
        $homeworkarr['homework']['words'] = $wordfb;
        $homeworkarr['homework']['texts'] = $textaloundarrfb;
        $homeworkarr['homework']['exams'] = $examsaloundarrfb;
        //var_dump(implode($homeworkarr));
        $this->assign("cardarr",$cardarr);
        $this->assign("homeworkarr",json_encode($homeworkarr));
        $this->assign("tqms",$tqms);
    	$this->display('preview');
        

    }
//单项预览
    public function preview_singal(){
        $id = I('id/d',0);
        $type = I('type/d',0);
        if($type == 0){
            $this->assign("taids",$id);
        }
        else{
            $this->assign("eqids",$id);
        }
        $this->display('preview_singal');
    }

    //学生做作业
     public function student_homework(){
        $homeworkid=I("paper_id");
        $channelid = I("channelid/s",0);
        $submittime=Date("Y-m-d H:i:s");
        if(!isset($_GET["studentId"])){
           $url="Location:teacher_view?homeworkid=".$homeworkid;
            header($url);
        }
        $role = I('role/s',0);
        $yjt_homeworkid = I("homeworkId");
        $interfaceServiceURL = I('interfaceServiceURL/s',0);
        session("paper_id",$homeworkid);
        session("homeworkid",I("homeworkId"));
        $isOverdue=I("isOverdue");
        $papreid=I("homeworkId");
        $username=I("username");
        $studentid = I("studentId");
        //if
        //$classid= getClassId(I("sso"),$username);
        $classid= I("classId/s","0");
        $workService = new \Homework\Service\WorkService();
        $workService -> setUserPaper($studentid,$homeworkid);
        if(isset($_GET["role"]) && $_GET["role"] == "jiazhang"){
         // echo "role=".$role;exit;
            $url="Location:stufeedback?homeworkid=".$homeworkid."&classId=".$classid."&studentId=".$username;
            header($url);
        }
        $batchid = '';
        $rs2=M("homework_batch")->where("paper_id=%d ",$homeworkid)->select();
        foreach ($rs2 as $key => $value) {
          $publishstudents=json_decode($value["publishstudents"],true);
           foreach($publishstudents as $bacthkey=>$bacthvalue){
            if($classid==$publishstudents[$bacthkey]["classId"]){     
              $batchid=$value["batchid"];
                break;
              }
            }
        }
       //echo $batchid;exit;
        
        $this->assign('batchid',$batchid);
        session("starttime",time());
        session("username",$username);
        session("classid",$classid);
        session("studentid",$studentid);
        cookie("tms",I("sso"));
        cookie("classid",$classid);
        cookie("studentid",$studentid);
        cookie("classId",$classid);
        cookie("studentId",$studentid);
        $this->assign('classid',$classid);
        $this->assign('studentid',$studentid);
        $this->assign('homeworkid',$homeworkid);
        $this->assign('yjt_homeworkid',$yjt_homeworkid);
        $this->assign('tms',I("sso"));
        $this->assign('interfaceServiceURL',$interfaceServiceURL);
        $yjArr=get_ip("",cookie("tms"));
        $tqms=$yjArr['tqms']['c1'];
        $sso=$yjArr['sso']['c1'];
        cookie("sso",$sso);
        //$tqms="tqms.youjiaotong.com";
        $this->assign("tpms",$tqms);
        $hdxx=$yjArr['ilearn']['title'];
        $this->assign("hdxx",$hdxx);
        $main=$yjArr['portal']['title'];
        session("main",$main);
        $this->assign("main",$main);
        
        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');

        $rs=M("homework")->where("id=%d",$homeworkid)->find();
        if($rs["wacount"] != 0){//单词跟读
            $this ->assign('wacardtitle',$arraynum[$cardnum].'单词跟读');
            $cardnum++;
        }
        if($rs["wscount"] != 0){//单词拼写
            $this ->assign('wpcardtitle',$arraynum[$cardnum].'单词拼写');
            $cardnum++;
        }
        if($rs["wccount"] != 0){//听音选词
            $this ->assign('wxcardtitle',$arraynum[$cardnum].'听音选词');
            $cardnum++;
        }
        if($rs["wrcount"] != 0){//英汉互译
            $this ->assign('wycardtitle',$arraynum[$cardnum].'英汉互译');
            $cardnum++;
        }
       
        if($rs["tacount"] != 0){//课文跟读
            $this ->assign('tacardtitle',$arraynum[$cardnum].'课文跟读');
            $cardnum++;
        }
        if($rs["eqcount"] != 0){//听力训练
            $this ->assign('eqcardtitle',$arraynum[$cardnum].'听力训练');
            $cardnum++;
        }
        $this->assign("wacount",$rs["wacount"]);
        $this->assign("wscount",$rs["wscount"]);
        $this->assign("wrcount",$rs["wrcount"]);
        $this->assign("wccount",$rs["wccount"]);
        $this->assign("tacount",$rs["tacount"]);
        $this->assign("eqcount",$rs["eqcount"]);
        $this->assign("homework_name",$rs["homework_name"]);
        $this->assign("isOverdue",$isOverdue);
        $this->assign("homeworkid",$homeworkid);
        $this->assign("channelid",$channelid);
        $sql="select * from engs_homework_student_list where engs_homework_student_list.homeworkid='".$papreid."' and engs_homework_student_list.studentid='".$username."' and engs_homework_student_list.classid= '".$classid."' and  engs_homework_student_list.dotime is not null";
        $srs=M()->query($sql);
        if(empty($srs)){
    			//查询engs_homework_student_list是否写入了
    			$sql="select * from engs_homework_student_list t where t.homeworkid='".$papreid."' and t.studentid='".$studentid."' and classid='".$classid."' and t.dotime is not null";
    			$listrs=M()->query($sql);
    			if(empty($listrs)){
    			  if($studentid!=''&&$studentid!='null'){
    				M("homework_student_list")->homeworkid=$papreid;
    				M("homework_student_list")->studentid=$studentid;
    				M("homework_student_list")->classid=$classid;
    				M("homework_student_list")->dotime=Date("Y-m-d H:i:s");
    				M("homework_student_list")->paper_id=$homeworkid;
    				M("homework_student_list")->add();
    			  }
    			}
            M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->setField("lastupdatetime",$submittime);
            $this->display('student_homework');
        }else{
          $sql="select * from engs_homework_student_list where engs_homework_student_list.homeworkid='".$papreid."' and engs_homework_student_list.studentid='".$username."' and engs_homework_student_list.classid= '".$classid."' and  engs_homework_student_list.submittime is not null";
          $srs=M()->query($sql);
          if(empty($srs)){
            M("homework_student_list")->where("studentid='%s' and paper_id=%d",$studentid,$homeworkid)->setField("lastupdatetime",$submittime);
            $this->display('student_homework');
          }else{
            $url="Location:stufeedback?homeworkid=".$homeworkid."&classId=".$classid."&studentId=".$username;
            header($url);
          }
        }
        //$this->display();
    }



    //学生作业反馈
     public function stufeedback(){
        $homeworkid=I("homeworkid");
        $studentid= I('studentId/s',0);
        $classid = I('classId/s',0);
        $showtype = I('showtype/d',0);

        session('studentid',$studentid);
        session('username',$studentid);
        session('classid',$classid);
        $main = session("main");
        session('viewtype',0);
        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');

        $rs=M("homework")->where("id=%d",$homeworkid)->find();
        if($rs["wacount"] != 0){//单词跟读
            $this ->assign('wacardtitle',$arraynum[$cardnum].'单词跟读');
            $cardnum++;
        }
        if($rs["wscount"] != 0){//单词拼写
            $this ->assign('wpcardtitle',$arraynum[$cardnum].'单词拼写');
            $cardnum++;
        }
        if($rs["wccount"] != 0){//听音选词
            $this ->assign('wxcardtitle',$arraynum[$cardnum].'听音选词');
            $cardnum++;
        }
        if($rs["wrcount"] != 0){//英汉互译
            $this ->assign('wycardtitle',$arraynum[$cardnum].'英汉互译');
            $cardnum++;
        }
       
        if($rs["tacount"] != 0){//课文跟读
            $this ->assign('tacardtitle',$arraynum[$cardnum].'课文跟读');
            $cardnum++;
        }
        if($rs["eqcount"] != 0){//听力训练
            $this ->assign('eqcardtitle',$arraynum[$cardnum].'听力训练');
            $cardnum++;
        }
        //$eq=M("homework_quiz")->where("homeworkid=%d",$homeworkid)->find();
       //  $exams = R('pcfunction/get_exams',array($homeworkid,$studentid,$classid,$eq['examsid'],0,1,0));
       //  //作业的总分
       //  $homework_score = $exams[0]['exams_score']+$rs["wacount"]+$rs["wscount"]+$rs["wrcount"]+$rs["wccount"]+$rs["tacount"];
       // // echo $homework_score;
       //  //var_dump($exams);
       //  //计算学生的得分
       //  //计算学生的分数
       //  $stussql="select sum(score) as score,'wt' as type from engs_homework_student_word_evaluat t where t.homeworkid=".$homeworkid." and t.studentid='".$studentid."' and t.classid='".$classid."' union all ";
       //  $stussql=$stussql."select sum(score) as score,'eq' as type from engs_homework_student_quiz t where t.homeworkid=".$homeworkid." and t.studentid='".$studentid."' and t.classid='".$classid."'";
       //  $stuscorere=M()->query($stussql);
       //  $stuscore=0.0;
       //  foreach($stuscorere as $k=>$v){
       //     $stuscore=$stuscore+$v["score"];
       //  }
       //  //学生得分率
       //  $average = round($stuscore/$homework_score*100,1).'%';
        //echo $average;exit;
        $average = getStuAverage($homeworkid,$classid,$studentid);
        $batchid = '';
        $rs2=M("homework_batch")->where("paper_id=%d ",$homeworkid)->select();
        foreach ($rs2 as $key => $value) {
          $publishstudents=json_decode($value["publishstudents"],true);
           foreach($publishstudents as $bacthkey=>$bacthvalue){
            if($classid==$publishstudents[$bacthkey]["classId"]){     
              $batchid=$value["batchid"];
                break;
              }
            }
        }
       $rs2=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
       $publishstudents=json_decode($rs2["publishstudents"],true);
       $students=$publishstudents[$classid];
       $studentarr = array();
       foreach ($students['students'] as $key => $value) {
          $temp = array();
          //var_dump($value);
          $temp['id'] = $value['id'];
          $temp['name'] = $value['name'];
          $temp['score'] = getStuAverage($homeworkid,$classid,$value['id']);
          $temp['time'] = $value['time'];
          $temp['classid'] = $classid;
          if($value['issubmit'] == 1){
            array_push($studentarr, $temp);
          }
          
       }
        $this->assign("average",$average);
        $this->assign("wacount",$rs["wacount"]);
        $this->assign("wscount",$rs["wscount"]);
        $this->assign("wrcount",$rs["wrcount"]);
        $this->assign("wccount",$rs["wccount"]);
        $this->assign("tacount",$rs["tacount"]);
        $this->assign("eqcount",$rs["eqcount"]);
        $this->assign("homework_name",$rs["homework_name"]);
        $this->assign('classid',$classid);
        $this->assign('studentid',$studentid);
        $this->assign('homeworkid',$homeworkid);
        $this->assign('showtype',$showtype);
        $this->assign('studentarr',$studentarr);
        $this->assign('main',$main);
        //get_stu_feedback_wordtest($homeworktype,$iserror,$studentid,$classid,$issubmit,$homeworkid,$wordtype);
        // $wordwpfeedback = R('pcfunction/get_stu_feedback_wordtest',array(0,1,$studentid,$classid,1,$homeworkid,2));
        // var_dump($wordwpfeedback);
        $this->display('stufeedback');
    }

     public function teacher_view(){
        $homeworkid=I("homeworkid");
        $main = session("main");
        session('viewtype',1);
        $cardnum = 0;
        $arraynum = array('0' => '一、', '1' => '二、','2' => '三、','3' => '四、','4' => '五、','5' => '六、','6' => '七、','7' => '八、','8' => '九、');

        $rs=M("homework")->where("id=%d",$homeworkid)->find();
        if($rs["wacount"] != 0){//单词跟读
            $this ->assign('wacardtitle',$arraynum[$cardnum].'单词跟读');
            $cardnum++;
        }
        if($rs["wscount"] != 0){//单词拼写
            $this ->assign('wpcardtitle',$arraynum[$cardnum].'单词拼写');
            $cardnum++;
        }
        if($rs["wccount"] != 0){//听音选词
            $this ->assign('wxcardtitle',$arraynum[$cardnum].'听音选词');
            $cardnum++;
        }
        if($rs["wrcount"] != 0){//英汉互译
            $this ->assign('wycardtitle',$arraynum[$cardnum].'英汉互译');
            $cardnum++;
        }
       
        if($rs["tacount"] != 0){//课文跟读
            $this ->assign('tacardtitle',$arraynum[$cardnum].'课文跟读');
            $cardnum++;
        }
        if($rs["eqcount"] != 0){//听力训练
            $this ->assign('eqcardtitle',$arraynum[$cardnum].'听力训练');
            $cardnum++;
        }
        //echo $average;exit;
       $rs=M("homework")->where("id=%d",$homeworkid)->find();
       
        $this->assign("wacount",$rs["wacount"]);
        $this->assign("wscount",$rs["wscount"]);
        $this->assign("wrcount",$rs["wrcount"]);
        $this->assign("wccount",$rs["wccount"]);
        $this->assign("tacount",$rs["tacount"]);
        $this->assign("eqcount",$rs["eqcount"]);
        $this->assign("homework_name",$rs["homework_name"]);
        $this->assign('homeworkid',$homeworkid);

        $this->assign('main',$main);
        //get_stu_feedback_wordtest($homeworktype,$iserror,$studentid,$classid,$issubmit,$homeworkid,$wordtype);
        // $wordwpfeedback = R('pcfunction/get_stu_feedback_wordtest',array(0,1,$studentid,$classid,1,$homeworkid,2));
        // var_dump($wordwpfeedback);
        $this->display('teacher_view');
    }
    //单个的详情
    public function devicefeedback(){
      $homeworkid=I("homeworkid");
      $id=I("id");
      $classid=I("classid");
      $source=I("source");
      $type=I("type");
      $this->assign("id",$id);
      $this->assign("homeworkid",$homeworkid);
      $this->assign("classid",$classid);
      $this->assign("source",$source);
      $this->assign("type",$type);
      $this->display('devicefeedback');

    }


    //单个的详情
    public function stuhomeworkfeedback(){
      $homeworkid=I("homeworkid");
      $id=I("id");
      $classid=I("classid");
      $source=I("source");
      $type=I("type");
      $this->assign("id",$id);
      $this->assign("homeworkid",$homeworkid);
      $this->assign("classid",$classid);
      $this->assign("source",$source);
      $this->assign("type",$type);
      $this->display('stuhomeworkfeedback');

    }


    /**
     * 获取试卷内的题型信息
     */
    public function listenshow($examsid) {
       S($examsid.$unitid.'examsdata',null);
       $total_score=0.0;
        $examsdata = S($examsid.$unitid.'examsdata');    //获取缓存内容
        if(empty($examsdata)){
           // echo "没有缓存";
            $sqlquery = M();
            $sql = 'select id,ks_code,name,exams_classid,exams_times,tts_type,header_content,stopsecond from engs_exams where id='.$examsid;
            $examsdata = $sqlquery -> query($sql);
            $exams_tts_type = $examsdata[0]['tts_type'];
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = 0 order by sortid,id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                    $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by id,sortid,question_num';
                    $question = $sqlquery -> query($sql);
                    foreach ($question as $quekey => $quevalue) {
                             $sql = 'select answer,answer_num from engs_exams_questions_answer where isdel=1 and questionsid = ' . $quevalue['id'].' order by sortid,id';     //独立大题问题的答案
                             $queanswer = $sqlquery -> query($sql);
                             $total_score += ceil($value['question_score']) * count($queanswer);
                        }
                }
                else{                           //组合大题
                   $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' order by id';
                   $stem_children = $sqlquery -> query($sql);
                  foreach ($stem_children as $keychild => $childvalue) {
                      $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                      $child_question = $sqlquery -> query($sql);
                      foreach ($child_question as $child_quekey => $child_quevalue) {
                              $sql = 'select answer,answer_num from engs_exams_questions_answer where isdel=1 and  questionsid = ' . $child_quevalue['id'].' order by sortid,id';     //问题的答案
                              $child_queanswer = $sqlquery -> query($sql);
                              $total_score += ceil($childvalue['question_score']) * count($child_queanswer);
                      }
                  }

            }
        }
      }
        return $total_score;
    }
        public function getStemTts(){
        $examsid = I('examsid/d', 0);
        $stemid = I('stemid/d', 0);
       //S($examsid.$unitid.'examsdata',null);
        // $examsdata = S($examsid.$unitid.'examsdata');    //获取缓存内容
        // if(empty($examsdata)){      
           // echo "没有缓存";
            $sqlquery = M();
            $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and id='.$stemid.' and parentid = 0 and isdel=1 order by id';
            $stemdata = $sqlquery -> query($sql);
            foreach ($stemdata as $key => $value) {
                $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$value['id'].' and tts_type="st" and isdel=1 order by sortid,id';     //题干的听力材料和mp3
                $stem_tts = $sqlquery -> query($sql);
                if(count($stem_tts) > 0){
                    $stemdata[$key]['stem_tts'] = $stem_tts;
                }
                else{
                    $stemdata[$key]['stem_tts'] = 0;
                }
                $stem_type = $value['stem_type'];
                if($stem_type == '1'){          //独立大题
                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$value['id'].' and isdel = 1 order by sortid,id,question_num';    
                        $question = $sqlquery -> query($sql);
                        foreach ($question as $quekey => $quevalue) { 
                            if($quevalue['typeid'] == '1' || $quevalue['typeid'] == '4'){    //如果是选择题或排序题就获取选项和答案                          
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $que_items = $sqlquery->query($sql);
                                $question[$quekey]['items'] = $que_items;
                                $sql = 'select a.flag as answer from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                $queanswer = $sqlquery -> query($sql);
                            }
                            else{
                                $question[$quekey]['items'] = 0;
                                 $sql = 'select answer from engs_exams_questions_answer where questionsid = ' . $quevalue['id'];     //独立大题问题的答案
                                $queanswer = $sqlquery -> query($sql);
                            }

                            $question[$quekey]['que_answer'] = $queanswer;

                            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and (tts_type="qn" || tts_type="qe") and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts'] = $que_tts;
                            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$quevalue['id'].' and tts_type="qe" and isdel=1 order by sortid,id';   //问题的听力材料
                            $que_tts_noqn = $sqlquery -> query($sql);
                            $question[$quekey]['que_tts_noqn'] = $que_tts_noqn;
                            # code...
                        }
                        $stemdata[$key]['question'] = $question;
                        $stemdata[$key]['question_score'] = ceil($stemdata[$key]['question_score']);
                        $score = ceil($value['question_score']) * count($question);
                        $stemdata[$key]['total_score'] = $score;
                        $total_score += $score;


                }   
                else{                           //组合大题
                     $sql = 'select id,stem_num,stem_type,question_playtimes,question_score,content,stem_playtimes,stoptimes from engs_exams_stem where examsid='.$examsid.' and parentid = '.$value['id'].' and isdel=1 order by id';
                    $stem_children = $sqlquery -> query($sql);
                    foreach ($stem_children as $keychild => $childvalue) {

                        $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type = "st" and isdel=1 order by sortid,id';   //组合题子题干听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$childvalue['id'].' and tts_type="st" and st_flag=0 and isdel=1 order by sortid,id';   //问题的听力材料
                        $child_stem_tts = $sqlquery -> query($sql);
                        $stem_children[$keychild]['stem_child_tts_nost'] = count($child_stem_tts) == 0 ? '0' : $child_stem_tts;

                        $sql='select id,stemid,question_num,typeid,tcontent,acontent,itemtype,display,tips,stoptimes from engs_exams_questions where stemid='.$childvalue['id'].' and isdel = 1 order by id,sortid,question_num';
                        $child_question = $sqlquery -> query($sql);
                        foreach ($child_question as $child_quekey => $child_quevalue) {
                            
                           
                            if($child_quevalue['typeid'] == '1' || $child_quevalue['typeid'] == '4'){       //如果是选择题或排序题就获取选项
                                $sql = 'select flag,content from engs_exams_questions_items where questionsid = ' . $child_quevalue['id'] . ' and isdel=1 order by sortid,id';
                                $child_que_items = $sqlquery->query($sql);
                                $child_question[$child_quekey]['items'] = $child_que_items;
                                $sql = 'select a.flag as answer from engs_exams_questions_items a,engs_exams_questions_answer b where a.content = b.answer and b.questionsid= ' . $child_quevalue['id'] . ' and a.isdel=1 and b.isdel=1 group by b.answer order by b.sortid,b.id';
                                $child_queanswer = $sqlquery -> query($sql);
                            }
                            else{
                                $sql = 'select answer from engs_exams_questions_answer where questionsid = ' . $child_quevalue['id'];     //问题的答案
                                $child_queanswer = $sqlquery -> query($sql);
                                $child_question[$child_quekey]['items'] = 0;
                            }
                             $child_question[$child_quekey]['que_answer'] = $child_queanswer;
                        }
                        $stem_children[$keychild]['question'] = $child_question;
                        $stem_children[$keychild]['question_score'] = ceil($stem_children[$keychild]['question_score']);
                        $score = ceil($childvalue['question_score']) * count($child_question);
                        $score2 += ceil($childvalue['question_score']) * count($child_question);
                        $stem_children[$keychild]['total_score']= $score;
                        $total_score += $score;

                    }
                    $stemdata[$key]['stem_children'] = $stem_children;
                    $stemdata[$key]['total_score'] = $score2;
                }

            }
            $sql = 'select id,ks_code,name,exams_times,tts_type,header_content,stopsecond from engs_exams where id='.$examsid;
            $examsdata = $sqlquery -> query($sql);
            $sql='select tts_type,parentid,flag_content,tts_content,tts_stoptime,tts_mp3,st_flag from engs_exams_tts where examsid='.$examsid.' and parentid='.$examsid.' and (tts_type ="eb" || tts_type="ed") and isdel=1 order by sortid,id';   //试卷头尾的听力材料
            $examstts = $sqlquery -> query($sql);
            foreach ($examsdata as $examskey => $examsvalue) {
                $examsdata[$examskey]['stem'] = $stemdata;
                $examsdata[$examskey]['total_score'] = $total_score;
                if($examsdata[$examskey]['header_content'] == null){
                    $examsdata[$examskey]['header_content'] = '';
                }
                $examsdata[$examskey]['exams_tts'] = count($examstts) == 0 ? '0' : $examstts;
            }
        //     S($examsid.$unitid.'examsdata',$examsdata);
            
        // }
        
        //var_dump($stemdata);
       $this -> ajaxReturn($examsdata);

        //$this -> assign('examsdata',$examsdata);
       // $this -> display();

    }
    public function record_file_save(){
        error_reporting(0);
        try{
           
            $upload_path = C('recordpath');
            
            $fileName=md5(date('YmdHis').mt_rand('10000','99999'));
            $Y = date('Y');
            $M = date('m');
            $D = date('d');
            $subDir  = $Y."/".$M."/".$D."/";
            if(!is_dir("/home/yylmp3/recordwav/".$subDir)){
              mkdir("/home/yylmp3/recordwav/".$subDir,0777,true);
            }
            $upload_filename =  $upload_path.$subDir.$fileName;
            $receiveFile = $upload_filename.'.wav';
            //echo $receiveFile;
            $ret = receiveStreamFile($receiveFile);
            $fileName = $subDir.$fileName.'.wav';    
            echo json_encode(array('success'=>(bool)$ret,'filename'=>$fileName));
        }catch (Exception $e) {
            exit;
        }
    }
    function hxpsmartstudy(){
      $fields['mp3Url'] = 'http://en2.czbanbantong.com/recordwav/5953354200e74.amr';
      $fields['question'] = 'when';
      $fields['token']='UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c';
           // var_dump($fields);
        //$url = "http://hxp.czbanbantong.com:8080/hxpup/upload.php";
     // $url = "http://open.smartstudy.com/api/grading/writing?mp3Url=http://en2.czbanbantong.com/recordwav/5953354200e74.amr&question=when&token=UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c";
      $url = "http://open.smartstudy.com/api/grading/writing";
        //$ch = curl_init();
        // @curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false); 
        // @curl_setopt($ch, CURLOPT_URL, $url);
        // @curl_setopt($ch, CURLOPT_ENCODING, "");
        // @curl_setopt($ch, CURLOPT_POST,1);
        // @curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        // @curl_setopt($ch, CURLOPT_HEADER, false);
        // @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $param = 'token=UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c&question=when&mp3Url=http://en2.czbanbantong.com:8080/yylmp3/mp3_word/CA04D2B852D1DF0918A35A13E8CC0810.mp3';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);// post传输数据
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $return_data = curl_exec($ch);
        // $code=mb_detect_encoding($return_data,array('ASCII','GB2312','GBK','UTF-8'));//检测字符串编码
        // echo $code;
        curl_close($ch);
        //echo "result=".$return_data;
       // $return_data = iconv('CP936','utf-8', $return_data);
        
        $return_data = json_decode($return_data);
        //$return_data = object_to_array($return_data);
        //echo $return_data;
        var_dump($return_data);
    
}
function update_usre_score (){
  $sqlquery = M();
  $recordpath = C('recordpath');
  $homework_student_word_read=M("homework_student_word_read");
  $homework_student_text_read=M("homework_student_text_read");
  //$sql = "SELECT a.word,b.`mp3`,b.id FROM engs_word a,engs_homework_student_word_read b,engs_homework_word_read c WHERE a.id=c.`wordid` AND c.`id` = b.`word_readid` AND b.`score`='0' ORDER BY b.id desc ";
  $sql = "select id,score,mp3 from engs_homework_student_word_read where score = '0' ORDER BY id desc";
  $list = $sqlquery->query($sql);
  // $list = $homework_student_word_read->field('id,mp3')->select();
  // $listtext = $homework_student_text_read->field('id,mp3')->select();
  echo '开始';
  foreach ($list as $key => $value) {
    if($value['mp3'] != null && $value['mp3'] != 'null' && $value['mp3'] != ''){
      $filepath =$recordpath.$value['mp3'];
      $result=smartstudy('aa','bb');
      $score = round($result->data->score,1);
      $homework_student_word_read->score=$score;
      $homework_student_word_read->where("id=%d",$value["id"])->save();
    } 
  }
  //$sql = "SELECT a.encontent,b.`mp3`,b.id FROM engs_text a,engs_homework_student_text_read b WHERE a.id=b.`textid` AND b.`score`='0' ORDER BY b.id desc limit 1";
  $sql = "select id,score,mp3 from engs_homework_student_text_read where score = '0' ORDER BY id desc";
  $listtext = $sqlquery->query($sql);
  foreach ($listtext as $key => $valuetext) {
    if($valuetext['mp3'] != null && $valuetext['mp3'] != 'null' && $valuetext['mp3'] != ''){
      $filepath =$recordpath.$valuetext['mp3'];
      $result=smartstudy('aa','bb');
      $score = round($result->data->score,1);
      $homework_student_text_read->score=$score;
      $homework_student_text_read->where("id=%d",$valuetext["id"])->save();
    } 
  }
  echo '结束';
}
    //教师查看听读作业反馈
    public function toHomeworkFeedbackPage(){
      $homeworkid = I('paper_id/d',3987);
      $batchid = I('batchId/s',3987);
      //$sso = I('sso/s','tms.youjiaotong.com');
      $localAreaCode = cookie('localAreaCode');
      $http_referer = $_SERVER['HTTP_REFERER'];
      //echo "http_refere=".$http_referer;
      //$http_referer="https://tqmszz.zzedu.net.cn/tqms/ssfd/sser?sdfdfdf=sfefef";
      if($http_referer == ""){
         $result = get_ip($localAreaCode,"");
      }
      else{
        $refererArr = parse_url($http_referer);
        $tqms = $refererArr["host"];
        $result = get_ip('',$tqms);
      }
      
      $tms = $result['tms']['title'];
      $tqms = $result['tqms']['title'];
      getHomeworkPublishStudents($tqms,$homeworkid,$batchid);
      //echo $localAreaCode;exit;
     // var_dump($result);exit;
      $rs = M("homework")->where("id=%d",$homeworkid)->find();
      $this->assign("wacount",$rs["wacount"]);
      $this->assign("wscount",$rs["wscount"]);
      $this->assign("wrcount",$rs["wrcount"]);
      $this->assign("wccount",$rs["wccount"]);
      $this->assign("tacount",$rs["tacount"]);
      $this->assign("eqcount",$rs["eqcount"]);
      $this->assign("homework_name",$rs["homework_name"]);
      $this->assign("homeworkid",$homeworkid);
      //班级信息
     //  $rs=M("homework")->where("id=%d",$homeworkid)->find();
     //  if($rs["publishstudents"] == '' || $rs["publishstudents"] == null || $rs["publishstudents"] =='null'){
     //     $rs2=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
     //     $publishstudents=json_decode($rs2["publishstudents"],true);
     //  }
     // else{
     //    $publishstudents=json_decode($rs["publishstudents"],true);
     // }
      $rs2=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
      $publishstudents=json_decode($rs2["publishstudents"],true);
      //var_dump($publishstudents);
     $batch=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
     $yjthomeworkid = $batch['homeworkid'];
      $classs=array();
      //var_dump($publishstudents);exit;
      foreach($publishstudents as $key=>$value){
        $classname=$publishstudents[$key]["className"];
        $temp=array();
        $temp["id"]=$publishstudents[$key]["classId"];
        if(empty($classname)||$classname=="null"){
         // echo "ss".cookie('localAreaCode');
          if($publishstudents[$key]["classId"] == "156687164789623308" ){
            $classname = "七年级（10）班";
          }
          else{
            $classname = getClassName($tms,$publishstudents[$key]["classId"]);
          }
         // echo $classname;
          $publishstudents[$key]["className"]=$classname;
         // M("homework")->where("id=%d",$homeworkid)->setField("publishstudents",json_encode($publishstudents));
         // M("homework_batch")->where("paper_id=%d",$homeworkid)->setField("publishstudents",json_encode($publishstudents));
        }
        $temp["name"]=$classname;
        $temp["batchid"]=$batchid;
        array_push($classs, $temp);
      }
      //var_dump($classs);exit;
      $this->assign("classsinfo",$classs);
      $this->assign("tms",$tms);
      $this->assign("tqms",$tqms);
      $this->assign("batchid",$batchid);
      $this->assign("yjthomeworkid",$yjthomeworkid);
      //
      $this -> display('toHomeworkFeedbackPage');
    }
    //教师查看听读作业反馈json
    public function toHomeworkFeedbackJson(){
      $classid = I('classid/s',0);
      $homeworkid = I('homeworkid/d',0);
      $batchid = I('batchid/s',0);
      $rs=M("homework")->where("id=%d",$homeworkid)->find();
      $sqlquery = M();
      $cardnum = 0;
      $feedback = array();
      $sql = "select studentid from engs_homework_student_list where classid='".$classid."' and paper_id=".$homeworkid." and submittime is not null group by studentid"; //提交的人数
      $submitnum = count($sqlquery->query($sql));
      $clas['average'] = 0;
      if($rs["wacount"] != 0){//单词跟读
            $cardtitle[$cardnum]['title'] = '单词跟读';
            $sql = "select sum(score) as score from engs_homework_student_word_read where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit =1";
           
            $delv = $sqlquery->query($sql);
            //得分率
            //$wdelv = (round($delv[0]['score']/($rs["wacount"]*100*$submitnum),1)*100);
            //echo sprintf("%.1f",0.4935);exit;
            $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($rs["wacount"]*100*$submitnum))*100;
            $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["wacount"];
            //易错词汇
              $wasql="select t.homeworkid,t.id,t.wordid,(select ew.word from engs_word ew where ew.id = t.wordid) as word,(case when avg(score) is null  then 0";
              $wasql=$wasql." else avg(score) end ) as average,case when max(score) is null then 0 else max(score) end as maxscore,case when min(score) is null then 0 else min(score) end as minscore,";
              $wasql=$wasql."sum(case when s.score is  null then 0 else 1 end) as num";
              $wasql=$wasql." from engs_homework_word_read t left join (select * from engs_homework_student_word_read where homeworkid=".$homeworkid." and classid='".$classid."' ) s on t.id=s.word_readid where";
              $wasql=$wasql." t.homeworkid = ".$homeworkid." and s.issubmit=1 group by t.wordid limit 0,5";
              //echo $wasql;exit;
              $wordAlound= $sqlquery->query($wasql);
              $cardtitle[$cardnum]['errword'] = $wordAlound;
              $cardtitle[$cardnum]['errtype'] = 1;
            $cardnum++;
        }
        if($rs["wscount"] != 0){//单词拼写
            $cardtitle[$cardnum]['title'] = '单词拼写';
            $sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type=2 and issubmit=1";
              //echo $sql;exit;
            $delv = $sqlquery->query($sql);
            //得分率
         // echo round($delv[0]['score']/($rs["wscount"]*$submitnum),1)*100;exit;
            //$wdelv = (round($delv[0]['score']/($rs["wscount"]*$submitnum),1)*100);
            //echo $rs["wscount"]*$submitnum;exit;
            $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($rs["wscount"]*$submitnum))*100;
           // echo $wdelv;exit;
            $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["wscount"];
            $sql="select s.*,(SELECT word FROM engs_word ew WHERE ew.id=t.wordid) as word,round(s.accnum/s.num,2) as accrate";
            $sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN (select wordid,word_evaluatid,count(*) as num,sum(case when iscorrect=1 then 1 else 0 end) as accnum from  engs_homework_student_word_evaluat  where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit=1 AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = ".$homeworkid."
      AND classid = '".$classid."') group by wordid,word_evaluatid) s  ";
            $sql=$sql."ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." AND t.typeid=2 and s.wordid IS NOT NULL HAVING (num - accnum) >0  ORDER BY accnum  limit 0,5";
         
            $wordpx= $sqlquery->query($sql);
            $cardtitle[$cardnum]['errtype'] = 0;
            $cardtitle[$cardnum]['errword'] = $wordpx;
            $cardnum++;
        }
        if($rs["wrcount"] != 0){//英汉互译
            $cardtitle[$cardnum]['title'] = '英汉互译';
             $sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type in(1,3) and issubmit=1";
            $delv = $sqlquery->query($sql);
            //得分率
            //$wdelv = (round($delv[0]['score']/($rs["wrcount"]*$submitnum),1)*100);
            $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($rs["wrcount"]*$submitnum))*100;
            $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["wrcount"];
             $sql="select s.*,(SELECT word FROM engs_word ew WHERE ew.id=t.wordid) as word,round(s.accnum/s.num,2) as accrate";
            $sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN (select wordid,word_evaluatid,count(*) as num,sum(case when iscorrect=1 then 1 else 0 end) as accnum from  engs_homework_student_word_evaluat  where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit=1 AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = ".$homeworkid."
      AND classid = '".$classid."') group by wordid,word_evaluatid) s  ";
            $sql=$sql."ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." AND t.typeid in(1,3) and s.wordid IS NOT NULL HAVING (num - accnum) >0  ORDER BY accnum  limit 0,5";
            $wordpy= $sqlquery->query($sql);
            $cardtitle[$cardnum]['errtype'] = 0;
            $cardtitle[$cardnum]['errword'] = $wordpy;
            $cardnum++;
        }
        if($rs["wccount"] != 0){//听音选词
            $cardtitle[$cardnum]['title'] = '听音选词';
             $sql = "select sum(score) as score from engs_homework_student_word_evaluat where homeworkid=".$homeworkid." and classid='".$classid."' and type=0 and issubmit=1";
            $delv = $sqlquery->query($sql);
            //echo $sql;exit;
            //得分率
           // echo $delv[0]['score'];exit;
            //$wdelv = (round($delv[0]['score']/($rs["wccount"]*$submitnum),1)*100);

            $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($rs["wccount"]*$submitnum))*100;
            $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["wccount"];
            $sql="select s.*,(SELECT word FROM engs_word ew WHERE ew.id=t.wordid) as word,round(s.accnum/s.num,2) as accrate";
            $sql=$sql." FROM engs_homework_word_evaluat t LEFT JOIN (select wordid,word_evaluatid,count(*) as num,sum(case when iscorrect=1 then 1 else 0 end) as accnum from  engs_homework_student_word_evaluat  where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit=1 AND studentid IN (SELECT studentid FROM engs_homework_student_list WHERE paper_id = ".$homeworkid."
      AND classid = '".$classid."') group by wordid,word_evaluatid) s  ";
            $sql=$sql."ON t.id = s.word_evaluatid WHERE t.homeworkid = ".$homeworkid." AND t.typeid =0 and s.wordid IS NOT NULL HAVING (num - accnum) >0  ORDER BY accnum  limit 0,5";

            $wordpx= $sqlquery->query($sql);
            $cardtitle[$cardnum]['errtype'] = 0;
            $cardtitle[$cardnum]['errword'] = $wordpx;
            $cardnum++;
        }
        if($rs["tacount"] != 0){//课文跟读
            $cardtitle[$cardnum]['title'] = '课文跟读';
           $sql = "select sum(score) as score from engs_homework_student_text_read where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit=1";
            $delv = $sqlquery->query($sql);
            //得分率
          //  $wdelv = (round($delv[0]['score']/($rs["tacount"]*100*$submitnum),1)*100);
           //echo sprintf("%.3f", $delv[0]['score']/($rs["tacount"]*100*$submitnum));exit;
           // echo sprintf("%.2f",$delv[0]['score']/($rs["tacount"]*100*$submitnum));exit;
            $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($rs["tacount"]*100*$submitnum))*100;
            $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["tacount"];
            $cardtitle[$cardnum]['errtype'] = 1;
            $cardnum++;
        }
        if($rs["eqcount"] != 0){//听力训练
           $sql = "select examsid from engs_homework_quiz where homeworkid=".$homeworkid;
           $examsinfo = $sqlquery->query($sql);
           $examsid = $examsinfo[0]['examsid'];
           $cardtitle[$cardnum]['title'] = '听力训练';
           $sql = "select sum(score) as score from engs_homework_student_quiz where homeworkid=".$homeworkid." and classid='".$classid."' and issubmit=1";
            $delv = $sqlquery->query($sql);
            //得分率
           //$wdelv = (round($delv[0]['score']/($rs["eqcount"]*$submitnum),1)*100);
           // echo $sql;exit;
            $totalscore = getExamScore($examsid);
            //echo $totalscore;exit;
           $wdelv = sprintf("%.3f",ceil($delv[0]['score'])/($totalscore*$submitnum))*100;
           $clas['average'] = $clas['average'] + $wdelv;
            $cardtitle[$cardnum]['defenlv'] = round($wdelv,1);
            $cardtitle[$cardnum]['qcount'] = $rs["eqcount"];
            $cardtitle[$cardnum]['errtype'] = 1;
            $cardnum++;
        }
       $feedback['question'] = $cardtitle;
       // $rs=M("homework")->where("id=%d",$homeworkid)->find();
       // if($rs["publishstudents"] == '' || $rs["publishstudents"] == null || $rs["publishstudents"] =='null'){
       //   $rs2=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
       //   $publishstudents=json_decode($rs2["publishstudents"],true);
       //  }
       // else{
       //    $publishstudents=json_decode($rs["publishstudents"],true);
       // }
       $rs2=M("homework_batch")->where("paper_id=%d and batchid='%s'",$homeworkid,$batchid)->find();
       $publishstudents=json_decode($rs2["publishstudents"],true);
       $students=$publishstudents[$classid];
       $total_student_num = count($students);
       //var_dump($rs["publishstudents"]);exit;
       $studentarr = array();
       $nosumbitstudentarr = array();
       $total_subtime = 0;
       foreach ($students['students'] as $key => $value) {
        $result = M("homework_student_list")->where("paper_id=%d and classid='%s' and studentid = '%s' and submittime is not null",$homeworkid,$classid,$value['id'])->find();
        if($result['id'] > 0){
            $temp = array();
            //var_dump($value);
            $temp['id'] = $value['id'];
            $temp['name'] = $value['name'];
            //$temp['score'] = $value['score'];
            $temp['score'] = round(getStuAverage($homeworkid,$classid,$value['id']),1);
            $temp['time'] = Sec2Time($value['time']);
            $temp['classid'] = $classid;
            $temp['issubmit'] = $value['issubmit'];
            array_push($studentarr, $temp);
            $total_subtime = $total_subtime + $value['time'];
        }
        else{
          $temp2=array();
           $temp2['id'] = $value['id'];
            $temp2['name'] = $value['name'];
            //$temp['score'] = $value['score'];
            $temp2['score'] = round(getStuAverage($homeworkid,$classid,$value['id']),1);
            $temp2['time'] = Sec2Time($value['time']);
            $temp2['classid'] = $classid;
            $temp2['issubmit'] = $value['issubmit'];
          array_push($nosumbitstudentarr, $temp2);
        }
       }

       $studentarr = $this->quickSort($studentarr,'score');
       $feedback['students'] = $studentarr;
       if($submitnum == 0){
         $average_time = 0;
       }
       else{
        $average_time = round($total_subtime/$submitnum);
       }
       
       $clas['submitnum'] = $submitnum; //已提交的人数
       $clas['total_submitnum'] = count($students['students']); //总人数
       $clas['no_submitnum'] = count($students['students']) - $submitnum; //未提交
       $clas['average_time'] = Sec2Time($average_time); //平均用时
       $clas['average'] = round($clas['average']/$cardnum,1); //平均得分
       $clas['students'] = $studentarr;
       $clas['nosumbitstudents'] = $nosumbitstudentarr;
       $feedback['classsinfo'] = $clas;
       //var_dump($studentarr);
      
       $this->ajaxReturn($feedback);
          //单词跟读得分率     

    }
    //此函数是快速排序的算法分治法
    private function quickSort($arr,$sortindex) {
      if(count($arr) > 1) {
        $_k=$arr[0];
        $k=$arr[0][$sortindex];
        $x=array();
        $y=array();
        $_size=count($arr);
        for($i=1;$i<$_size;$i++) {
          if($arr[$i][$sortindex] <=$k) {
            array_push($y,$arr[$i]);//大的放这边。这样子是从小到大排序，如果想从大到小返回，那么调换位置与$x[] =$arr[$i];的位置即可
          }else{
            array_push($x,$arr[$i]);//小的放这边
          }
        }
         //得到分割看来左右两边的数据
        $x= $this->quickSort($x,$sortindex);//左边的数据，对这些数据再次使用分割法排序，返回的结果就是排序后的数据
        $y= $this->quickSort($y,$sortindex);//右边的数据
        return $this->arrpreg($x,$_k,$y);
      }else{
        return $arr;
      }
    }
    //进行数组的合并
    private function arrpreg($a,$b,$c){
        $arr = array();
        if(count($a)>0){
          foreach ($a as $k => $r) {
              array_push($arr,$r);
          }
        }
        array_push($arr,$b);
        if(count($c)>0){
          foreach ($c as $k => $r) {
              array_push($arr,$r);
          }
        }
        return $arr;
    }
}
