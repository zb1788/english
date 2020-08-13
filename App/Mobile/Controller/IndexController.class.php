<?php
namespace Mobile\Controller;
use Think\Controller;
/** 
* 首页控制器类
*  
* @author         gm 
* @since          1.0 
*/  

class IndexController extends CheckController {
	/** 
	* index  
	* 首页展示 
	* 
	* @author         gm 
	* @since          1.0 
	*/  
    public function index(){
		$logintype=$_GET["source"];
      if(empty($logintype)){
        $logintype=cookie("logintype");
      }else{
        cookie("logintype",$logintype);
      }
      $url=str_replace("http://","",cookie('sso_url'));
      $arr=get_ip("",$url);
      $ut=$_GET["ut"];
      cookie('ut',$ut);
      cookie("localAreaCode",$arr['tms']['areacode']);
      $this -> display();
    }

    

    //获取用户版本信息
    public function getuserinfo(){
		$username=I("username/s");
	  $engm_readword = M("study") -> where(" wordflag=0 and studytype=1 and username='%s'",$username) -> count();
		if(empty($engm_readword)){
			$engm_readword=0;
		}
		cookie('engm_readword_count',$engm_readword);
		$engm_reciteword = M("study") -> where(" wordflag=2 and studytype=1 and username='%s'",$username) -> count();
		if(empty($engm_reciteword)){
			$engm_reciteword=0;
		}
		cookie('engm_reciteword_count',$engm_reciteword);
		$engm_readtextword = M("study") -> where(" wordflag=0 and studytype=2 and username='%s'",$username) -> count();
		if(empty($engm_readtextword)){
			$engm_readtextword=0;
		}
		cookie('engm_readtextword',$engm_readtextword);
		$engm_wordbook=M("word_book") -> where("userid='%s'",$username) -> count();
		if(empty($engm_wordbook)){
			$engm_wordbook=0;
		}
	cookie('engm_wordbook_count',$engm_wordbook);
	cookie('engm_record_count',$engm_readword+$engm_reciteword+$engm_readtextword);
      $readwcount = cookie('engm_readword_count');
      $wordbcount = cookie('engm_wordbook_count');
      $recordcount = cookie('engm_record_count');
      $reciteword = cookie('engm_reciteword_count');
      
      if($reciteword==""){
        $reciteword="0";
      }
      if($readwcount==""){
        $readwcount="0";
      }
      if($recordcount==""){
        $recordcount="0";
      }
      if($wordbcount==""){
        $wordbcount="0";
      }

      $arr["reciteword"]=$engm_reciteword;
      $arr["readwcount"]=$engm_readword;
      $arr["recordcount"]=$engm_readword+$engm_reciteword+$engm_readtextword;
      $arr["wordbcount"]=$engm_wordbook;
      $this->ajaxReturn($arr);

    }

    /** 
	* course  
	* 课程设置 
	* 
	* @author         gm 
	* @since          1.0 
	* @mark           自定义配置未取到	
	*/  
    public function course(){
          $rms_dictionary = M('rms_dictionary');
          $rms_unit = M('rms_unit');
          $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
          $sql = 'SELECT distinct r_grade AS gradeid , r_volume AS termid, (SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_grade AND dictionary_code="grade") AS gradename,(SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_volume AND dictionary_code="volume") AS termname ';
          $sql .= 'FROM  engs_rms_unit v WHERE r_version = "%s" and  r_subject="0003" ';
          $sql .= ' ORDER BY gradeid, termid ';
          $versionsql="Select distinct r_version as detail_code,(SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_version AND dictionary_code='edition') as detail_name from engs_rms_unit where r_subject='0003'";
          $verrs=M()->query($versionsql);
          foreach ($verrs as $key => $value) {
              $gradeterm = $Model->query($sql,$value["detail_code"]);
              foreach ($gradeterm as $k => $v) { 
                  //取默认配置的版本年级学期设置为选中
                  if ($value["detail_code"] == cookie('engm_versionid') && $v["gradeid"] == cookie('engm_gradeid') && $v["termid"] == cookie('engm_termid')  ) {
                      $gradeterm[$k]["checked"] = 1;
                  }
              }
              $verrs[$key]["gradeterm"] = $gradeterm;
          }
          $this->assign('rsv',$verrs);
          $this->buildHtml("course", HTML_PATH . '/Mobile/Index/', 'course', 'utf8');
          $this->display();
    }

    /** 
	* vgtset  
	* 教材年级学期设置 
	* 
	* @author         gm 
	* @since          1.0 
	* @mark           自定义配置未取到	
	*/
    public function vgtset(){    	

        $versionid =I("versionid/s",0); 
        $gradeid =I("gradeid/s",0);
        $termid =I("termid/s",0);
        //echo "sss" .$gradeid;
		// $config=M("config");
		// $arr["gradeid"]=$gradeid;
		// $arr["termid"]=$termid;
		// $arr["versionid"]=$versionid;
		// $arr["userid"]=cookie('engm_username');
		// $arr["areacode"]=cookie('engm_areacode');
		// $arr["configtime"]=Date("y-m-d H:i:s");
	 //  $config->add($arr);
        version_grade_term_set($versionid,$gradeid,$termid);
        $this->ajaxReturn($arr);
    }

     /** 
	* record  
	* 学习记录 
	* 
	*/
    public function record(){
        $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
    	$this -> assign("recordcount",$recordcount);
    	$this -> assign("wordbcount",$wordbcount);    	
    	$username=cookie("engm_username");
      $areacode=cookie("engm_areacode");
    	//$username="1401011309260043";
      $Model=new \Think\Model(); 
    	$sql="SELECT h.username,h.addtimey,h.addtimem,h.addtimed,SUM(CASE WHEN studytype='1' AND wordflag=0 THEN 1 ELSE 0 END) AS learnword,";
    	$sql.="SUM(CASE WHEN studytype='1' AND wordflag=2 THEN 1 ELSE 0 END) AS reciteword,";
    	$sql.="SUM(CASE WHEN studytype='2' AND wordflag=0 THEN 1 ELSE 0 END) AS learntext,";
      $sql.="SUM(CASE WHEN studytype='3' AND wordflag=0 THEN 1 ELSE 0 END) AS learnexams";
    	$sql.=" FROM engs_study h where h.username='%s' and h.areacode='%s'";
      $sql.=" GROUP BY h.username,h.areacode,h.addtimey,h.addtimem,h.addtimed order by h.addtimey desc,h.addtimem desc,h.addtimed desc limit 100";
    	$recordlist = $Model->query($sql,$username,$areacode);
    	$this -> assign("recordlist",$recordlist);
    	$this -> display();
    }
    
    /** 
	* wordbook  
	* 生词本 
	* 
	*/
    public function wordbook(){
        $wordbcount = cookie('engm_wordbook_count');
        $recordcount= cookie('engm_record_count');
    	$this -> assign("recordcount",$recordcount);
    	$this -> assign("wordbcount",$wordbcount);    	
    	$username=cookie("engm_username");
    	//$username="1401011309260043";
        $Model=new \Think\Model(); 
    	$sql="SELECT t.wordid,ew.word,bw.usmark,bw.ukmark,bw.ukmp3,bw.usmark,bwe.morphology,bwe.explains,bw.isword ";
    	$sql.="FROM  engs_word_book t, engs_word ew ,engs_base_word bw ,engs_base_word_explains bwe ";
    	$sql.=" where ew.id = t.wordid  AND t.userid = '%s'  AND ew.base_wordid=bw.id AND ew.base_explainsid=bwe.id AND t.isuse=1 ";
        $sql.=" ORDER BY t.studytime desc";
        //echo $sql;
    	$wordbooklist = $Model->query($sql,$username);
    	$this -> assign("workbooklist",$wordbooklist);
    	$this -> display();
    }

    /** 
    * delwordbook  
    * 删除生词本 
    * 
    */
    public function delwordbook(){
        $wordid=I("wordid/d",0);
        $username=cookie("engm_username");
        $areacode=cookie("engm_areacode");
        $wb=M("word_book");
        if(0==$wordid){
            $wb -> where("userid='%s'",$username)->setField("isuse",0);
			      cookie('engm_wordbook_count','0');
        }else{
            $wb -> where("wordid=%d and userid='%s'",$wordid,$username) ->setField("isuse",0);
			      $wordbcount = cookie('engm_wordbook_count');
			      cookie('engm_wordbook_count',$wordbcount-1);
        }
    }

    public function learn(){
      $url=PROTOCOL."://".C("local_service")."/Subject/Index/entrance?ut=".cookie("ut")."&areaCode=".cookie("localAreaCode")."&deviceType=mobile";
      $ret["url"]=$url;
      $this->ajaxReturn($ret);
    }

}
 
// /**
//  * Project Form object, extends Zend_Form
//  *
//  * @author Nick <------@gmail.com> 2012-4-18
//  */
// class Project_Form extends Zend_Form {
        
//     /**
//      * Options name of default values
//      * @var string
//      */
//     const OPTIONS_DEFAULTE_VALUES = 'defaults';
        
//     /**
//      * Name of required attribute
//      * @var string
//      */
//     const NAME_OF_ATTRIBUTE_REQUIRED = 'required';
        
//     /**
//      * Extend is valid
//      * @param array $data
//      * @return void
//      */
//     protected function _beforeIsValid(array $data) {
//         return $data;
//     }
        
//     /**
//      * Validate submitted data
//      * @see Zend_Form::isValid()
//      */
//     public function isValid($data) {
//         $data = $this->_beforeIsValid($data);
//         return parent::isValid($data);
//     }
        
// }