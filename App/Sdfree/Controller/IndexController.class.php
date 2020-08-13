<?php
namespace Sdfree\Controller;
use Think\Controller;

/** 
* 框架
*  
* @author         gm 
* @since          1.0 
*/  
class IndexController extends Controller {
    public function index(){
    	$sdbackUrl = I("backUrl/s","0");
    	if($sdbackUrl != null && $sdbackUrl != "0" && $sdbackUrl != "null"){
    		cookie("sdbackUrl",sdbackUrl);
    	}
    	$backUrl = cookie("sdbackUrl");
    	$this->assign('backUrl',$backUrl);
    	$this->display("index");
    }
    public function xpy(){
		cookie('subjectid','0001');
        $this->display('xpy');
    }

    //英语100句子的单元
    public function getEnUnitData(){
      $moduleid=54;
      //$learnways=getmodule($moduleid);
      //获取秘籍
      $username="000000";
      $gradeid=cookie("gradeid");
      $subjectid=cookie("subjectid");
      //getUserConfig($subjectid,$gradeid,$username);
      $volumnid=cookie("termid");
      $versionid=cookie("versionid");
      $type=$learnways["type"];
      $sql="SELECT t.id AS ks_code,t.enchapter AS ks_name,'0' AS is_unit,CASE WHEN s.ks_code IS NULL THEN 0 ELSE s.count END AS isdo,";
      $sql=$sql."CASE WHEN userdata IS NULL THEN 0 ELSE userdata END AS userdata,'1' AS COUNT FROM engs_contact_chapter t LEFT JOIN ";
      $sql=$sql."(SELECT COUNT(*) AS COUNT,ks_code,SUM(CASE WHEN username = '".$username."' THEN 1 ELSE 0 END) AS userdata FROM ";
      $sql=$sql."db_english.engs_user_modules_unit_log WHERE moduleid = ".$moduleid." GROUP BY ks_code) s ON t.id = s.ks_code WHERE t.isdel=1 group by t.id ORDER BY sortid ";
      $result=M()->query($sql);
      $arr["img"]="";
      $arr["versionname"]="";
      $arr["gradename"]="";
      $arr["volumename"]="";
      $arr["subjectname"]="";
      $result["bookinfo"]=$arr;
     // $result["ways"]=$learnways;
      $this->ajaxReturn($result);
    }
    //英语100句子的课文
    public function getTextsDataByUnit(){
      $ks_code=I("ks_code/d",0);
      $chaptersql="select id,enchapter as chapter from engs_contact_chapter where id='%s' and isdel=1 order by sortid";
      $chapterresult=M()->query($chaptersql,$ks_code);
      $audio=array();
      $contact_mp3_path = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_contact/";
      $contact_mp3_path = getHttpUrl($contact_mp3_path);
      foreach($chapterresult as $key=>$value){
        $textsql="select *,'mp3' AS format,'1' AS size,content as encontent,'' as enbefore,concat(tts_mp3,'.mp3') as name,concat('".$contact_mp3_path."',left(tts_mp3,2),'/',tts_mp3,'.mp3') as url from engs_contact_text where chapterid=%d and isdel=1 order by sortid";
        $textresult=M()->query($textsql,$value["id"]);
        $chapterresult[$key]["texts"]=$textresult;
        array_push($audio,$textresult);
      }
      $result=$chapterresult;
      //获取课文下面所有的音频
      $ret["data"]=$result;
      $ret["mp3list"]=$audio[0];
      $this->ajaxReturn($ret);
    }
    //德育绘本屋
    public function dyhb(){
        $model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.166.200/db_microclass');
        
        // $pingjia = M('evaluate')->where('app_id=%d and isdel=1',$app_id)->order('id desc')->select();
        // $this ->assign('pingjia',$pingjia);
        // //var_dump($unitlist);exit;
        // $this ->assign('r_grade_code',$version['r_grade_code']);
        // $this ->assign('r_term_code',$version['r_term_code']);
        // $this ->assign('r_version_code',$version['r_version_code']);
        // $this ->assign('r_grade_name',$version['r_grade_name']);
        // $this ->assign('r_term_name',$version['r_term_name']);
        // $this ->assign('r_version_name',$version['r_version_name']);
        // $this ->assign('r_subject_name',$version['r_subject_name']);
        // $this ->assign('r_remark',$version['r_remark']);
        // $this ->assign('r_pic',$r_pic);
        // $this ->assign('h2name',$h2name);
        // $this -> assign('unitlist',$unitlist);
        // $this -> assign('app_id',$app_id);
        // $this -> assign('version_id',$version_id);
        // $this -> assign('courselist',$courselist);
        // $this -> assign('app_name',$appresult['app_name']);
        // $this -> assign('subject_code',$appresult['app_subject_code']);
        // $this -> assign('isterm',$appresult['app_isterm']);
        // $this -> assign('isunit',$appresult['app_isunit']);
        // $this -> assign('unit_type',$appresult['app_unit_type']);
        // $this -> assign('isspecial',$appresult['app_isspecial']);
        // $this -> assign('special_code',$appresult['app_special_code']);
        // $this -> assign('issource',$appresult['app_issource']);
        // $this -> assign('source_code',$appresult['app_source_code']);
        // $this -> assign('app_drumbeating',$appresult['app_drumbeating']);
        // $appresult['app_object'] = str_replace('||', '|', $appresult['app_object']);
        // $appresult['app_target'] = str_replace('||', '|', $appresult['app_target']);
        // $this -> assign('app_object',explode('|', $appresult['app_object']));
        // $this -> assign('app_target',explode('|', $appresult['app_target']));
        // $this -> assign('pic_feat',$appresult['app_course_feat']);
        // $this -> assign('pic_content',$appresult['app_course_content']);
        // $this -> assign('pic_teac',$appresult['app_course_teac']);
        // //$this -> assign('app_object',html_entity_decode($appresult['app_content']));
        // $this -> assign('app_pic',$appresult['app_pic']);
        // $this -> assign('backUrl',$backUrl);
        // $this -> assign('username',$username); 
        // $this -> assign('areacode',$areacode);
        // $this -> assign('pls_url',$pls_url);
        // $app_moduleid = cookie('app_moduleid');
        // $this -> assign('app_moduleid',$app_moduleid);
        $this->display("dyhb");
    }
}

 
