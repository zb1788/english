<?php
namespace Mobile\Controller;
use Think\Controller;

/** 
* 课文控制器
*  
* @author         gm 
* @since          1.0 
*/
class TextreadController extends CheckController {
    public function index(){
      $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
      $versionid = cookie('engm_versionid'); 
      $gradeid = cookie('engm_gradeid'); 
      $termid = cookie('engm_termid');
      $rms_unit=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("max(update_text_time) as exams_time")->group("r_grade,r_volume,r_version")->find();
      $fs=file_exists($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Textread/indexg".$gradeid."v".$termid."v".$versionid.".html");
      if($rms_unit["exams_time"]<filemtime($_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile/Textread/indexg".$gradeid."v".$termid."v".$versionid.".html")&&$fs==1){
        $filename="Cache/Mobile/Textread/indexg".$gradeid."v".$termid."v".$versionid;
        $this->display($filename);
      }else{
          $versionid = cookie('engm_versionid'); 
          $gradeid = cookie('engm_gradeid'); 
          $termid = cookie('engm_termid');
          $arr["grade"]=$gradeid;
          $arr["volume"]=$termid;
          $arr["version"]=$versionid;
        //传递年级教材学期名字
        $this->vgt_name = cookie('engm_gradename') .'.'. cookie('engm_versionname') .'.'. cookie('engm_termname');
        $unitlist=D("rms_unit")->getUnitListData($arr,1);
        $this->assign("unitlist",$unitlist["result"]);
        $this->assign("isunit",$unitlist["unit"]);
        $this->assign("unitlen",$unitlist["len"]);
        $this->buildHtml('indexg'.$gradeid."v".$termid."v".$versionid, HTML_PATH . '/Mobile/TextRead/', 'index', 'utf8');
  	    $this->display();
      }
	}


    /** 
	* 展示课文页面
	* 
	*/
	public function read(){
	  $model=I("model");
      $flag=I("flag");
      $unit = explode(',',cookie('engm_unitid'));
      $this -> assign("unit",$unit);
      $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
      $this -> assign("recordcount",$recordcount);
      $this -> assign("wordbcount",$wordbcount);
	  
	  //实现左右滑动进行单元的切换
	  $this -> assign("unitcount",count($unit));
	 //需要记住是从第几单元进入的详情学习压面，因此从首页经来的时候需要将次写到cookie中
	 if(empty($model))
     {
     	$model='pm';
		$curindex=cookie("engm_currunit");
		$indexn=I("indexn/d",$curindex+1);
        $indexp=I("indexp/d",$curindex-1);
	 }else{
	 	    $indexn=I("indexn/d");
        $indexp=I("indexp/d");
	 }     
	  //滑动下一单元
    if($flag=='1')
    {
      $indexn=$indexn-1;
      $indexp=$indexp-1;
    }
    elseif($flag=='0')
    {
      $indexn=$indexn+1;
      $indexp=$indexp+1;
      
    }
    $this -> assign("indexn",$indexn);
    $this -> assign("indexp",$indexp);
      $unitid = I('unitid/s',0); 
      $text_chapter=M("text_chapter");
      $textcount=$text_chapter -> where("isdel=1 and ks_code='%s'",$unitid) -> count();
      $this -> assign("textcount",$textcount);
    	$Unit = M("rms_unit"); // 实例化User对象
		  // 获取ID为3的用户的昵称 
      //$this -> assign("chaptercount",$chaptercount);
  		$unitname = $Unit->where("ks_code='%s'",$unitid)->getField('ks_name');
  		$this->assign('unitname',$unitname);
  		$this -> assign('unitid',$unitid);

      $textdata = $this-> get_unit_chapter_text($unitid);
      
      $this->assign('textdata',$textdata);
      $this->buildHtml('read'.$unitid, HTML_PATH . '/Mobile/TextRead/', 'read', 'utf8');
    	$this->display();
    }
    
    /** 
	* 取出单元下所有的章节
	* @param   单元的id
	* @retrun  课文的json数据 
	*/
    protected function get_unit_chapter($unitid)
    { 
		  $Chapter = M("text_chapter");
	    $chapterlist = $Chapter ->where('isdel = 1 and ks_code="%s"',$unitid)-> order("sortid,id") -> getField('id,chapter');
	    return $chapterlist;
    }

    /** 
	* 取出单元下所有的课文
	* @param   单元的id
	* @retrun  课文的json数据 
	*/
    protected function get_unit_chapter_text($unitid)
    { 
  		$Text=M("text");
  		$chapterlist=$this -> get_unit_chapter($unitid);
  		$textdata=Array();
  		if(!empty($chapterlist))
  		{
  			foreach($chapterlist as $key => $value){
  				$textlist= $Text -> where("isdel=1 and chapterid=%d",$key) -> order("sortid,id") -> select();
  				$textdata[$value]=$textlist;
  			}
  		}
  		$data["arr"] = $textdata;
	    $data["json"] = json_encode($textdata); 
	    return $data;
    }

    /** 
    * study_text_record
	* 课文朗读记录
	* @param   
	* @retrun   
	*/
    public function study_text_record(){
     //  $engm_readtextword=cookie("engm_readtextword")+1;
     //  cookie("engm_readtextword",$engm_readtextword);
    	// $versionid = cookie('engm_versionid'); 
	    // $gradeid = cookie('engm_gradeid'); 
	    // $termid = cookie('engm_termid'); 
    	// $username = cookie('engm_username');
     //  $usertype = cookie('engm_usertype');
     //  $areacode = cookie('engm_areacode');
    	// $chapterid=I("chapterid/d");
    	// $unitid=I("unitid/s");
    	// $study_text=M("study");
    	// $cur=date("Y-m-d");
     //  $arr_return["add"]=0;
    	// $rs=$study_text -> where("username='%s' and studytype=2  and engs_studyid=%d",$username,$chapterid) -> field("addtime") -> order("id desc") -> find();
    	// if(!empty($rs)){
     //       if($rs["addtime"]!=$cur){
     //       	  $study_text -> addtime=$cur;
     //       	  $study_text -> gradeid=$gradeid;
     //          $study_text -> termid=$termid;
     //       	  $study_text -> versionid=$versionid;
     //       	  $study_text -> ks_code=$unitid;
     //       	  $study_text -> engs_studyid=$chapterid;
     //       	  $study_text -> username=$username;
     //          $study_text -> usertype=$usertype;
     //       	  $study_text -> areacode=$areacode;
     //       	  $study_text -> addtimey=date("Y");
     //       	  $study_text -> addtimem=date("m");
     //       	  $study_text -> addtimed=date("d");
     //          $study_text -> studytype=2;
     //       	  $study_text -> add();
     //          $arr_return["add"]=1;
     //       }
     //    }else{
     //    	  $study_text -> addtime=$cur;
     //       	  $study_text -> gradeid=$gradeid;
     //          $study_text -> termid=$termid;
     //       	  $study_text -> versionid=$versionid;
     //       	  $study_text -> ks_code=$unitid;
     //       	  $study_text -> engs_studyid=$chapterid;
     //       	  $study_text -> username=$username;
     //       	  $study_text -> usertype=$usertype;
     //          $study_text -> areacode=$areacode;
     //       	  $study_text -> addtimey=date("Y");
     //       	  $study_text -> addtimem=date("m");
     //       	  $study_text -> addtimed=date("d");
     //          $study_text -> studytype=2;
     //       	  $study_text -> add();
     //          $arr_return["add"]=1;
     //    }
     //    $this  ->ajaxReturn($arr_return);
    }
	
	public function getTextDataByChapter(){
      $request=I("request");
      $request = rtrim($request, '"');
      $request = ltrim($request, '"');
      $request = str_replace('&quot;', '"', $request);
      $request = json_decode($request,true);
      $chapterid=$request["chapterid"];

      $chapterrs=M("text_chapter")->where("id=%d",$chapterid)->find();

      $textrs=M("text")->where("chapterid=%d and isdel=1",$chapterid)->field("enbefore,encontent,cncontent,mp3")->order("sortid,id")->select();
 
      $data["chaptername"]=$chapterrs["chapter"];
      $data["texts"]=$textrs;
      $data["baseurl"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/";
      $this->ajaxReturn($data);
    }
	
	public function setUserBrowserTextChapter(){
      $ut=I("ut",0);
      $areacode=I("serAreaCode",0);
      $chapterid=I("chapterid");
      $isparent=0;
      $userScanChapterText=M("user_scan_chapter_text");
      if($ut!="undefined"&&$areacode!="undefined"&&!empty($ut)&&!empty($areacode)){
        $rs=get_ip($areacode,"");
        $sso=$rs['sso']['c1'];
        $tms=$rs['tms']['c1'];
        $url="http://".$sso."/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=".cookie('ut');
        $json_ret = file_get_contents($url);  
        preg_match_all('/"name":"(.*?)"/', $json_ret, $tmp);
        $json_ret=str_replace($tmp[1][0], "", $json_ret);
        $truename=iconv("gbk", "UTF-8//IGNORE",$tmp[1][0]);
        $json_ret = iconv("gbk", "UTF-8//IGNORE",$json_ret);
        $result = json_decode($json_ret, true);
        if($result['authFlg']=='1'){
          cookie("jp_ut",cookie('ut'));
          $ouUser=$result['ouUser'];
          $user=$result['user'];
          $grade=$user['grade'];
          $area=$user['area'];
          $school=$user['school'];
          $schoolClasses=$user['schoolClasses'];
          $usertype=$user['usertype'];
          $areaid=$area['areaId'];
          $fullname=$area['fullname'];
          $areaname=$area['fullname'];
          $schoolid=$school['schoolId'];
          $schoolname=$school['schoolName'];
          $classid=$schoolClasses[0]['classId'];
          $classname=$schoolClasses[0]['className'];
          $gradeid=$schoolClasses[0]['gradeCode'];
          if($usertype != 0&&$usertype != 4){  
              $username=$user['username'];
          }else if($usertype==4){  
              $class=$user["schoolClasses"][0];
              $username=$user['username'];
          }else if($usertype==0){   
              $tmsurl = 'http://'.$tms.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
              $json_ret = file_get_contents($tmsurl);
              preg_match_all('/"realname":"(.*?)"/', $json_ret, $tmp);
              for($i=1;$i>=0;$i--){
                $value=$tmp[1][$i];
                $json_ret=str_replace($value, "", $json_ret);
                if($key==0){
                  $truename=iconv("gbk","utf-8//IGNORE",$value);
                  cookie("truename",$truename);
                }
              }
              $json_ret = iconv("gbk", "utf-8//IGNORE",$json_ret);
              $studentinfo = json_decode($json_ret, true);
              if($studentinfo["result"]>0){
                  $parentid=$user['username'];
                  $username=$studentinfo['rtnArray'][0]['student']['studentAccount'];
                  $usertype=4;
                  $isparent=1;
              }else{
                  
              }
          }
        }
        $useragent=$_SERVER['HTTP_USER_AGENT'].";uxin";
        $data["areaid"]=$areaid;
        $data["fullname"]=$fullname;
        $data["areaname"]=$areaname;
        $data["schoolid"]=$schoolid;
        $data["schoolname"]=$schoolname;
        $data["classid"]=$classid;
        $data["classname"]=$classname;
        $data["truename"]=$truename;
        $data["usertype"]=$usertype;
        $data["username"]=$username;
        $data["parentid"]=$parentid;
        $data["useragent"]=$useragent;
        $data["chapterid"]=$chapterid;
        $userScanChapterText->add($data);
      }else{
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        $data["areaid"]="";
        $data["fullname"]="";
        $data["areaname"]="";
        $data["schoolid"]="";
        $data["schoolname"]="";
        $data["classid"]="";
        $data["classname"]="";
        $data["truename"]="";
        $data["usertype"]="";
        $data["username"]="";
        $data["parentid"]="";
        $data["useragent"]=$useragent;
        $data["chapterid"]=$chapterid;
        $userScanChapterText->add($data);
      }

    }
}
