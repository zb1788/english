<?php
namespace Elearnmanager\Controller;
use Think\Controller;

/** 
* 基本数据获取
*  
* @author         gm 
* @since          1.0 
*/  
class GetbasedataController extends Controller {
    /*获取学科JSON数据*/
      public function getSubject(){
        $admin_subject_code = session('admin_subject_code');
        $qtype = I('qtype/d',0);
        $Dao = M('rms_dictionary'); // 实例化Word对象
        if($admin_subject_code == "all"){
          if($qtype == 0){
           
            $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="subject"')->order('detail_order')->select();
           }
           else{
            $sql = 'select detail_code,detail_name from mc_rms_dictionary where dictionary_code="subject" and detail_code in (select distinct app_subject_code from mc_application where isdel=1) order by detail_order';
            $Data = M()->query($sql);
           }
        }
        else{
            $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="subject" and detail_code="'.$admin_subject_code.'"')->order('detail_order')->select();
        }
        
        $this->ajaxReturn($Data);
    } 
    /*获取特殊类型JSON数据*/
      public function getSpecial(){
        //if (!IS_AJAX){$this->ajaxReturn();}
        // F('Grade',null); 
        if (empty($Data)) {
         $Dao = M('rms_dictionary'); // 实例化Word对象
         $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="special"')->order('detail_order')->select();
        } 
        $this->ajaxReturn($Data);
    }
    /*获取外接来源JSON数据*/
      public function getSource(){
        //if (!IS_AJAX){$this->ajaxReturn();}
        // F('Grade',null); 
        if (empty($Data)) {
         $Dao = M('rms_dictionary'); // 实例化Word对象
         $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="source"')->order('detail_order')->select();
        } 
        $this->ajaxReturn($Data);
    } 
	/*获取年级JSON数据*/
    public function getGrade(){
		 $grade_code = I('grade_code/s',0);
     $showtpe = I('showtpe/s',0);
		 if (empty($Data)) {
		 	$Dao = M('rms_dictionary'); // 实例化Word对象
      if($showtpe == 'option'){
          $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="grade" and detail_code in ('.$grade_code.')')->order('detail_order')->select();
      }
      else{
          $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="grade"')->order('detail_order')->select();
      }
			
		} 
		$this->ajaxReturn($Data);
    } 
/*获取年级JSON数据*/
    public function getTerm(){
     //if (!IS_AJAX){$this->ajaxReturn();}
     // F('Grade',null); 
     if (empty($Data)) {
      $Dao = M('rms_dictionary'); // 实例化Word对象
      $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="volume"')->order('detail_order')->select();
    } 
    $this->ajaxReturn($Data);
    } 
    /*获取年级JSON数据*/
    public function getRmsVersion(){
     //if (!IS_AJAX){$this->ajaxReturn();}
     // F('Grade',null); 
     if (empty($Data)) {
      $Dao = M('rms_dictionary'); // 实例化Word对象
      $Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="edition"')->order('detail_order')->select();
    } 
    $this->ajaxReturn($Data);
    } 
    /*获取版本JSON数据*/
    public function getVersion(){
		//if (!IS_AJAX){$this->ajaxReturn();}
		$subjectcode  = I("subjectcode/s", 0);
    $app_id = I("app_id/d",'100000000000');
    $term_code = I('term_code/s','aaa');
    $grade_code =I('grade_code/s',0);		 
		$Dao = M('version'); // 实例化Word对象
		if($term_code == 'aaa'){
      $Data = $Dao->where('r_subject_code="%s" and  app_id=%d and isdel=1',$subjectcode,$app_id)->order('r_grade_name')->select();
    }
		else{
      $Data = $Dao->where('r_subject_code="%s" and r_grade_code="%s" and r_term_code="%s"  and app_id=%d and isdel=1',$subjectcode,$grade_code,$term_code,$app_id)->order('r_grade_name')->select();
    }
		$this->ajaxReturn($Data);
    }
    
  //   /*获取单元JSON数据*/
    public function getUnit(){
		//if (!IS_AJAX){$this->ajaxReturn();}	
		$queryid = I("queryid/d", 0);
    $querytype = I("querytype/s", 0);
		$Dao = M('unit'); // 实例化Word对象
    if($querytype == 'version'){
      $Data = $Dao->field('id,name,version_id,is_click')->where("isdel = 1 and version_id=%d",$queryid)->order("sortid")->select();
    }
		else{
      $Data = $Dao->field('id,name,version_id,is_click')->where("isdel = 1 and app_id=%d",$queryid)->order("sortid")->select();
    }
		$this->ajaxReturn($Data);
    } 
  //   /*获取版本JSON数据*/
  //   public function getVoiceid(){
  //   	//if (!IS_AJAX){$this->ajaxReturn();}
  //   	$flag = I("flag", 0);
  //   	$Dao = C(CONST_VOICETYPE);
  //   	if(flag=='0'){
  //   		$this->ajaxReturn(array_slice($Dao,0,2));
  //   	}else{
  //   		$this->ajaxReturn(array_slice($Dao,2));
  //   	}
  //   }
  //   /*获取年级JSON数据*/
  //   public function getProvince(){
		// //if (!IS_AJAX){$this->ajaxReturn();}
		// // F('Grade',null); 
		// $Dao = M('dictionary');  // 实例化Word对象
		// $Data = $Dao->where('code="province"')->order("sortid,id")->select();
		// $this->ajaxReturn($Data);
  //   }

  //   /*获取年级JSON数据*/
  //   public function getTerm(){
  //       $gradeid=I("gradeid/s");
  //       $Dao = M('rms_dictionary'); // 实例化Word对象
  //       if($gradeid=='0010'){
  //       	//高中学科的话就是term2
  //       	$Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="electiveType"')->order('detail_order')->select();
  //       }else{
  //       	$Data = $Dao->field('detail_code,detail_name')->where('dictionary_code="volume"')->order('detail_order')->select();
  //       }
		// $this->ajaxReturn($Data);
  //   }

  //   /*获取年级JSON数据*/
  //   public function getChapter(){
  //       $unitid=I("unitid/s");
  //       $Dao = M('text_chapter'); // 实例化Word对象
  //       $Data = $Dao->where('ks_code="%s" and isdel=1',$unitid)->order('sortid,id')->select();
  //       $this->ajaxReturn($Data);
  //   } 


  //   //获取试卷统计信息
  //   public function getStatExams(){
  //       $starttime=I("starttime");
  //       $endtime=I("endtime");
  //       $sql="SELECT s.r_grade as gradeid,s.r_volume as termid,s.r_version as versionid,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_volume` AND rms_dictionary_detail.`DICTIONARY_CODE`='volume') AS termname,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_grade` AND rms_dictionary_detail.`DICTIONARY_CODE`='grade') AS gradename,(SELECT rms_dictionary_detail.`detail_name` FROM rms_dictionary_detail WHERE rms_dictionary_detail.`detail_code`=s.`r_version` AND rms_dictionary_detail.`DICTIONARY_CODE`='edition') AS versionname,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state!=4 THEN 1 ELSE 0 END) AS checking,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=1 or t.exams_classid=3)  THEN 1 ELSE 0 END) AS tbchecked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=2) THEN 1 ELSE 0 END) AS ktchecked,COUNT(*) as num FROM engs_exams t LEFT JOIN engs_rms_unit s ON t.`ks_code`=s.ks_code WHERE SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND t.isdel=1 AND t.ks_code IS NOT NULL AND t.`ks_code`!='0' GROUP BY s.r_grade,s.r_version,s.r_volume union all SELECT 'c' as gradeid,'1' as termid,'1' as versionid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`levelid`) AS gradeid,(SELECT engs_dictionary.`title` FROM engs_dictionary WHERE engs_dictionary.id=t.`typeid`) AS versionname, '中考' as termid,SUM(CASE WHEN t.state=4 THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.state!=4 THEN 1 ELSE 0 END) AS checking,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=1 or t.exams_classid=3)  THEN 1 ELSE 0 END) AS tbchecked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=2) THEN 1 ELSE 0 END) AS ktchecked,COUNT(*) FROM engs_exams t  where SUBSTR(t.addtime,1,10)>='".$starttime."' and SUBSTR(t.addtime,1,10)<='".$endtime."' AND isdel=1 AND t.typeid!=0  GROUP BY t.`typeid`,t.`levelid`";
  //       //echo $sql;
  //       $rs=M()->query($sql);
  //       $this->ajaxReturn($rs);
  //   }

  //   //获取试卷单元统计信息
  //   public function getStatExamsUnit(){
  //       $versionid=I("versionid");
  //       $gradeid=I("gradeid");
  //       $termid=I("termid");
  //       $starttime=I("starttime");
  //       $endtime=I("endtime");
  //       $sql="SELECT  s.`ks_code`,s.`ks_name`,SUM(CASE WHEN t.`state`!='4' THEN 1 ELSE 0 END) AS checking,SUM(CASE WHEN t.`state`='4' THEN 1 ELSE 0 END) AS checked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=1 or t.exams_classid=3)  THEN 1 ELSE 0 END) AS tbchecked,SUM(CASE WHEN t.`state`='4' and (t.exams_classid=2) THEN 1 ELSE 0 END) AS ktchecked,COUNT(*) AS num FROM engs_exams t LEFT JOIN engs_rms_unit s ON t.ks_code=s.ks_code WHERE SUBSTR(t.addtime, 1, 10) >= '".$starttime."'  AND SUBSTR(t.addtime, 1, 10) <= '".$endtime."'  AND t.isdel = 1  AND s.`r_grade` = '".$gradeid."'   AND s.`r_version` = '".$versionid."'  AND s.`r_volume` = '".$termid."' GROUP BY s.`ks_code` order by s.display_order";
  //       //echo $sql;
  //       $rs=M()->query($sql);
  //       $this->ajaxReturn($rs);
  //   }
    

  }
    