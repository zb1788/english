<?php
namespace Elearnmanager\Controller;

use Think\Controller;

class ClassappController extends CheckController {
	public function applist(){
		$subject_code = I('subject_code/s','all');
		$admin_subject_code = session('admin_subject_code');
		$this->assign('subject_code',$subject_code);
		$this->assign('admin_subject_code',$admin_subject_code);
		$this->display();
	}
	//获取应用列表
	public function getAppList(){
		$appdata = M();
		$subject_code=I('subject_code/s','all');
		$isspecial = I('isspecial/d',2);
		$admin_subject_code = session('admin_subject_code');
		if($isspecial == 2){
			$whereispecial = '';
		}
		else{
			$whereispecial = ' and a.app_isspecial='.$isspecial;
		}

		if($admin_subject_code == 'all'){
			if($subject_code == 'all'){
				$sql="select a.id,a.app_name,a.sortid,b.detail_name from mc_application a left join mc_rms_dictionary b on a.app_subject_code = b.detail_code where a.isdel=1 ".$whereispecial." and b.dictionary_code = 'subject' order by a.sortid,a.id";
				$applist = $appdata->query($sql);
			}
			else{
				$sql="select a.id,a.app_name,a.sortid,b.detail_name from mc_application a left join mc_rms_dictionary b on a.app_subject_code = b.detail_code where a.app_subject_code = '%s' ".$whereispecial." and a.isdel=1 and b.dictionary_code = 'subject' order by a.sortid,a.id";
				$applist = $appdata->query($sql,$subject_code);
			}
		}
		else{
			//$applist = $appdata->where('app_subject_code="%s"',$subject_code)->order('sortid')->select();
			$sql="select a.id,a.app_name,a.sortid,b.detail_name from mc_application a left join mc_rms_dictionary b on a.app_subject_code = b.detail_code where a.app_subject_code = '%s' ".$whereispecial." and a.isdel=1 and b.dictionary_code = 'subject' order by a.sortid,a.id";
			$applist = $appdata->query($sql,$admin_subject_code);
		}
		foreach ($applist as $key => $value) {
			$grade_config = M('grade_config')->where('app_id=%d and isdel=1',$value['id'])->order('id')->select();
			$applist[$key]['grade_config'] = $grade_config;
		}
		$this->ajaxReturn($applist);
	}
	public function app_del(){
		$app=M('application');
		$id = I('app_id/d',0);
		$app->isdel = 0;
		$app->where('id=%d',$id)->save();
		M('grade_config')-> where('app_id=%d',$id)->setField('isdel','0');
		M('course')-> where('app_id=%d',$id)->setField('isdel','0');
		M('unit')-> where('app_id=%d',$id)->setField('isdel','0');
		M('version')-> where('app_id=%d',$id)->setField('isdel','0');
		M('question')-> where('app_id=%d',$id)->setField('isdel','0');
	}
	//获取视频列表
	public function getCourseList(){
		$coursedata = M();
		$subject_code=I('subject_code/s','all');
		$isterm=I('isterm/d',0);
		$isunit=I('isunit/d',0);
		$app_id = I('app_id/d',0);
		$version_id = I('version_id/d',0);
		$unit_id = I('unit_id/d',0);
		if($isterm == 1){
			if($isunit == 1){
				$sqlwhere = ' where a.isdel = 1 and  app_id='.$app_id.' and unit_id='.$unit_id.' and version_id='.$version_id;
			}
			else{
				$sqlwhere = ' where a.isdel = 1 and app_id ='.$app_id.' and version_id='.$version_id;
			}
		}
		else{
			if($isunit == 1){
				$sqlwhere = ' where a.isdel = 1 and  app_id='.$app_id.' and unit_id='.$unit_id;
			}
			else{
				$sqlwhere = ' where a.isdel = 1 and app_id='.$app_id;
			}
			
		}
		$sql = "select a.*,(select count(*) from mc_question b where b.course_id = a.id and b.isdel = 1 and b.type in(1,2)) as q_num,(select count(*) from mc_question b where b.course_id = a.id and b.isdel = 1 and b.type =3) as yaoqiu_num,(select count(*) from mc_question b where b.course_id = a.id and b.isdel = 1 and b.type =4) as fanwen_num from mc_course a ".$sqlwhere." order by sortid";
		//echo $sql;
		 $courselist = $coursedata->query($sql);
		 $this->ajaxReturn($courselist);
	}
	//微课应用添加
	public function appadd(){
		$subject_code = I('subject_code/s','0003');
		$this->assign('subject_code',$subject_code);
		$this -> display();
	}
	//微课应用编辑
	public function appedit(){
		$id=I('id/d',0);
		$appdata = M('application');
			$appgrade = M('grade_config');
			$appresult = $appdata->where('id=%d',$id)->find();
			$graderesult = $appgrade->where('app_id=%d and isdel=1',$id)->order('sortid')->select();
			foreach ($graderesult as $key => $value) {
				if($key != (count($graderesult)-1)){
					$gradestr .= $value['grade_code'].',';
				}
				else{
					$gradestr .= $value['grade_code'];
				}
			}
			$this -> assign('id',$id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_object',$appresult['app_object']);
			$this -> assign('app_target',$appresult['app_target']);
			$this -> assign('pic_feat',$appresult['app_course_feat']);
			$this -> assign('pic_content',$appresult['app_course_content']);
			$this -> assign('pic_teac',$appresult['app_course_teac']);
			//$this -> assign('app_object',html_entity_decode($appresult['app_content']));
			$this -> assign('app_pic',$appresult['app_pic']);
		$this -> display();
	}
	//微课应用图片上传
	public function Appimgupload(){
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'mcappimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//微课应用图片上传
	public function Contentimgupload(){
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'contentimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//微课内容图片上传
	public function Courseimgupload(){ 
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'mccourseimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//版本图片上传
	public function Versionimgupload(){ 
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'versionimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//版本图片上传
	public function Unitimgupload(){ 
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'unitimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//选项图片上传
	public function Itemimgupload(){ 
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true;
		//$upload->subName  = array('date','Ym');	
		$upload->subName  = 'unitimg';
	    $info = $upload->upload();  
	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
	       	$this->ajaxReturn($arr_return);
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
			$arr_return["msg"] = $info["Filedata"];
			//$arr_return["msg"] = $info["file"];
	       	$this->ajaxReturn($arr_return);
			//var_dump($info);
	    }
	}
	//微课应用提交
	public function SaveApp(){
		//app_isterm:app_isterm,app_isunit:app_isunit,app_isspecial:app_isspecial,app_issource:app_issource,app_name:app_name,app_subject_code:app_subject_code,app_unit_type:app_unit_type,app_special_code:app_special_code,app_grade:app_grade,app_pic:app_pic,app_drumbeating:app_drumbeating,app_content:app_content,app_source_code:app_source_code
		$application = M('application');
		$grade_config = M('grade_config');
		$id = I('id/d',0);
		$app_isterm = I('app_isterm/d',0);
		$app_isunit = I('app_isunit/d',0);
		$app_isspecial = I('app_isspecial/d',0);
		$app_issource = I('app_issource/d',0);
		$app_name = I('app_name/s',0);
		$app_subject_code = I('app_subject_code/s',0);
		$app_unit_type = I('app_unit_type/d',0);
		$app_special_code = I('app_special_code/s',0);
		$app_grade = I('app_grade/s',0);
		$app_grade_name = I('app_grade_name/s',0);
		$app_grade_arr = explode(',', $app_grade);
		$app_grade_namearr = explode(',', $app_grade_name);
		$app_pic = I('app_pic/s',0);
		$app_drumbeating = I('app_drumbeating/s',0);
		$app_object = I('app_object/s',0);
		$app_target = I('app_target/s',0);
		$filename_feat = I('filename_feat/s',0);
		$filename_content = I('filename_content/s',0);
		$filename_teac = I('filename_teac/s',0);
		$app_source_code = I('app_source_code/s',0);
		$data['app_isterm']= $app_isterm;
		$data['app_isunit']= $app_isunit;
		$data['app_isspecial']= $app_isspecial;
		$data['app_issource']= $app_issource;
		$data['app_name']= $app_name;
		$data['app_subject_code']= $app_subject_code;
		$data['app_unit_type']= $app_unit_type;
		$data['app_special_code']= $app_special_code;
		$data['app_pic']= $app_pic;
		$data['app_drumbeating']= $app_drumbeating;
		$data['app_object']= $app_object;
		$data['app_target']= $app_target;
		$data['app_course_teac']= $filename_teac;
		$data['app_course_feat']= $filename_feat;
		$data['app_course_content']= $filename_content;
		$data['app_source_code']= $app_source_code;
		$sort = $application->field('max(id) as sort')->find();
		$data['sortid']= ceil($sort['sort'])+1;
		//var_dump($app_grade_namearr);
		if($id == 0){
			$app_id = $application->add($data);
			if($app_id){
				foreach ($app_grade_arr as $key => $value) {
					$sort = $grade_config->field('max(id) as sort')->find();
					$gradedata['app_id'] = $app_id;
					$gradedata['grade_code'] = $value;
					$gradedata['grade_name'] = $app_grade_namearr[$key];
					$gradedata['sortid'] = ceil($sort['sort'])+1;
					$grade_config->add($gradedata);
				}
				$returndata['message'] = '添加成功';
			}
			else{
				$returndata['message'] = '添加失败';
			}
		}
		else{
			$upflag=$application->where('id=%d',$id)->save($data);
			//$grade_config->where('app_id=%d',$id)->delete();
			$cur_config = $grade_config->where('app_id=%d',$id)->select();
			foreach ($cur_config as $key => $value) {
				if(count(explode($value['grade_code'], $app_grade)) > 1){
					if($value['isdel'] == 0){
						$grade_config->isdel = 1;
						$grade_config->where('id=%d',$value['id'])->save();
					}
				}
				else{
					if($value['isdel'] == 1){
						$grade_config->isdel = 0;
						$grade_config->where('id=%d',$value['id'])->save();
					}
					
				}
			}
			foreach ($app_grade_arr as $key => $value) {
				    $result =  $grade_config->where('app_id=%d and grade_code="%s"',$id,$value)->find();
				    if(count($result) == 0){
				    	$sort = $grade_config->field('max(id) as sort')->find();
						$gradedata['app_id'] = $id;
						$gradedata['grade_code'] = $value;
						$gradedata['grade_name'] = $app_grade_namearr[$key];
						$gradedata['sortid'] = ceil($sort['sort'])+1;
						$grade_config->add($gradedata);
				    }
					
			}
			if($upflag){
				$returndata['message'] = '修改成功';
			}
			else{
				$returndata['message'] = '修改失败';
			}
		}
		$this->ajaxReturn($returndata);
	}
	//微课内容提交
	public function SaveCourse(){
		//app_isterm:app_isterm,app_isunit:app_isunit,app_isspecial:app_isspecial,app_issource:app_issource,app_name:app_name,app_subject_code:app_subject_code,app_unit_type:app_unit_type,app_special_code:app_special_code,app_grade:app_grade,app_pic:app_pic,app_drumbeating:app_drumbeating,app_content:app_content,app_source_code:app_source_code
		$course = M('course');
		$id = I('id/d',0);
		$app_id = I('app_id/d',0);
		$version_id = I('version_id/d',0);
		$unit_id = I('unit_id/d',0);
		$course_name = I('course_name/s',0);
		$app_subject_code = I('app_subject_code/s',0);
		$course_pic = I('course_pic/s',0);
		$course_big_pic = I('course_big_pic/s',0);
		$course_code = I('course_code/s',0);
		$data['app_id']= $app_id;
		$data['subject_code']= $app_subject_code;
		$data['version_id']= $version_id;
		$data['unit_id']= $unit_id;
		$data['title']= $course_name;
		$data['pic']= $course_pic;
		$data['big_pic']= $course_big_pic;
		$data['video_code']= $course_code;
		$sort = $course->field('max(id) as sort')->find();
		$data['sortid']= ceil($sort['sort'])+1;
		//var_dump($app_grade_namearr);
		if($id == 0){
			$course_id = $course->add($data);
			if($course_id){
				$returndata['message'] = '添加成功';
			}
			else{
				$returndata['message'] = '添加失败';
			}
		}
		else{
			$upflag=$course->where('id=%d',$id)->save($data);
			if($upflag){
				$returndata['message'] = '修改成功';
			}
			else{
				$returndata['message'] = '修改失败';
			}
		}
		$this->ajaxReturn($returndata);
	}
	/**
 * 批量修改应用顺序
 */
    public function edit_app_sort() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $app = M("application");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $app->sortid = $sortid;
            $result = $app->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = "修改成功";
        $this -> ajaxReturn($arr_return);
    }
    public function edit_course_sort() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $course = M("course");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $course->sortid = $sortid;
            $result = $course->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = "修改成功";
        $this -> ajaxReturn($arr_return);
    }
    public function course_del(){
    	$course_id = I('course_id/d',0);
    	$course = M('course');
    	$question = M('question');
    	$course->isdel = 0;
    	$course->where('id=%d',$course_id)->save();
    	$question->isdel = 0;
    	$question->where('course_id=%d',$course_id)->save();
    }
    public function hxptest(){
    	// $grade_config = M('grade_config');
    	// $result = $grade_config->where('id=15')->find();
    	// echo count($result);
    	$str = "#EXTM3U
#EXT-X-VERSION:3

#EXT-X-STREAM-INF:BANDWIDTH=1273000,RESOLUTION=720x576
mda-gh4cg2b9zygde9bf_720x576_1273000.m3u8

#EXT-X-STREAM-INF:BANDWIDTH=761000,RESOLUTION=480x384
mda-gh4cg2b9zygde9bf_480x384_761000.m3u8

#EXT-X-ENDLIST";
	   //echo $str;
	   $arr1 = explode('RESOLUTION=720x576', $str);
	   $arr2 = explode('#EXT-X-STREAM-INF', $arr1[1]);
	   echo $arr2[0];
    }

    public function app_course_list(){
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$subject_code = I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->where('isdel = 1')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('isdel = 1 and app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			if(count($appidarr) >0){
				$app_id = $appidarr['id'];
			}	
		}
		if($app_id > 0){
			$appresult = $appdata->where('id=%d',$app_id)->find();
			$this -> assign('app_id',$app_id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_content',$appresult['app_content']);
			$this -> assign('app_pic',$appresult['app_pic']);
			$this -> assign('version_id',$version_id);
			$this -> assign('unit_id',$unit_id);
		}
		else{
			$this -> assign('app_id',$app_id);
			$this -> assign('subject_code',$subject_code);
			$this -> assign('version_id',$version_id);
			$this -> assign('unit_id',$unit_id);
		}
		
		$this -> display();
    }
    public function app_course_add(){
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$subject_code = I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->where('isdel = 1')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('isdel = 1 and app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			if(count($appidarr) >0){
				$app_id = $appidarr['id'];
			}	
		}
		if($app_id > 0){
			$appresult = $appdata->where('id=%d',$app_id)->find();
			$this -> assign('app_id',$app_id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_content',$appresult['app_content']);
			$this -> assign('app_pic',$appresult['app_pic']);
		}
		else{
			$this -> assign('app_id',$app_id);
			$this -> assign('subject_code',$subject_code);
		}
			$this -> assign('version_id',$version_id);
			$this -> assign('unit_id',$unit_id);
		$this -> display();
    }
    public function app_course_edit(){
    	$course_id = I('course_id/d',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$coursedata = M('course');
    	$subject_code = I('subject_code/s',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			$app_id = $appidarr['id'];
		}
		$appresult = $appdata->where('id=%d',$app_id)->find();
		$courseresult = $coursedata->where('id=%d',$course_id)->find();
		$this -> assign('app_id',$app_id);
		$this -> assign('course_id',$course_id);
		$this -> assign('version_id',$version_id);
		$this -> assign('unit_id',$unit_id);
		$this -> assign('app_name',$appresult['app_name']);
		$this -> assign('subject_code',$appresult['app_subject_code']);
		$this -> assign('isterm',$appresult['app_isterm']);
		$this -> assign('isunit',$appresult['app_isunit']);
		$this -> assign('unit_type',$appresult['app_unit_type']);
		$this -> assign('isspecial',$appresult['app_isspecial']);
		$this -> assign('special_code',$appresult['app_special_code']);
		$this -> assign('issource',$appresult['app_issource']);
		$this -> assign('source_code',$appresult['app_source_code']);
		$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
		$this -> assign('app_content',$appresult['app_content']);
		$this -> assign('app_pic',$appresult['app_pic']);
		$this -> assign('course_title',$courseresult['title']);
		$this -> assign('course_pic',$courseresult['pic']);
		$this -> assign('course_big_pic',$courseresult['big_pic']);
		$this -> assign('course_code',$courseresult['video_code']);
		$this -> display();
    }
    public function versionlist(){
    	$subject_code = I('subject_code/s',0);
    	$grade_code = I('grade_code/s',0);
    	$term_code = I('term_code/s',0);
    	$app_id = I('app_id/d',0);
    	$this -> assign('subject_code',$subject_code);
    	$this -> assign('app_id',$app_id);
    	$this -> assign('grade_code',$grade_code);
    	$this -> assign('term_code',$term_code);
    	$appgrade = M('grade_config');

		$graderesult = $appgrade->where('app_id=%d and isdel=1',$app_id)->order('sortid')->select();
		foreach ($graderesult as $key => $value) {
			if($key != (count($graderesult)-1)){
				$gradestr .= $value['grade_code'].',';
			}
			else{
				$gradestr .= $value['grade_code'];
			}
		}
		$this -> assign('grade_code',$gradestr);
    	
    	$this -> display();
    
    }
    public function get_version_img(){
    	$subject_code = I('subject_code/s',0);
    	
    	$grade_code = I('grade_code/s',0);
    
    	$term_code = I('term_code/s',0);
   
    	$version_code = I('version_code/s',0);
  
    	$version = M('version_img');
    	$rs = $version->where('r_grade_code="%s" and r_subject_code="%s" and r_term_code="%s" and r_version_code="%s" and isdel=1',$grade_code,$subject_code,$term_code,$version_code)->find();
    	$rs['length'] = count($rs);
    	$this->ajaxReturn($rs);
    }
    public function version_add(){
    	$subject_code = I('subject_code/s',0);
    	$grade_code = I('grade_code/s',0);
    	$term_code = I('term_code/s',0);
    	$app_id = I('app_id/d',0);
    	$this -> assign('subject_code',$subject_code);
    	$this -> assign('app_id',$app_id);
    	$this -> assign('grade_code',$grade_code);
    	$this -> assign('term_code',$term_code);
    	$appgrade = M('grade_config');
		$graderesult = $appgrade->where('app_id=%d and isdel=1',$app_id)->order('sortid')->select();
		foreach ($graderesult as $key => $value) {
			if($key != (count($graderesult)-1)){
				$gradestr .= $value['grade_code'].',';
			}
			else{
				$gradestr .= $value['grade_code'];
			}
		}
		$this -> assign('grade_code',$gradestr);
    	$this -> display();

    }
    public function get_app_grade_conf(){
    	$app_id = I('app_id/d',0);
    	$appgrade = M('grade_config');
		$graderesult = $appgrade->where('app_id=%d and isdel=1',$app_id)->order('sortid')->select();
		foreach ($graderesult as $key => $value) {
			if($key != (count($graderesult)-1)){
				$gradestr .= $value['grade_code'].',';
			}
			else{
				$gradestr .= $value['grade_code'];
			}
		}
		$data['grade_code']=$gradestr;
		$this->ajaxReturn($data);
    }
    public function Version_save(){
    	$subject_code = I('subject_code/s',0);
    	$app_id = I('app_id/d',0);
    	$subject_name = I('subject_name/s',0);
    	$grade_code = I('grade_code/s',0);
    	$grade_name = I('grade_name/s',0);
    	$term_code = I('term_code/s',0);
    	$term_name = I('term_name/s',0);
    	$version_code = I('version_code/s',0);
    	$version_name = I('version_name/s',0);
    	$pic = I('pic/s',0);
    	$remark = I('remark/s',0);
    	$version = M('version');
    	$sort = $version->field('max(id) as sort')->find();
    	$add['app_id'] = $app_id;
    	$add['r_grade_code'] = $grade_code;
    	$add['r_version_code'] = $version_code;
    	$add['r_term_code'] = $term_code;
    	$add['r_subject_code'] = $subject_code;
    	$add['r_version_name'] = $version_name;
    	$add['r_grade_name'] = $grade_name;
    	$add['r_term_name'] = $term_name;
    	$add['r_subject_name'] = $subject_name;
    	$add['r_pic'] = $pic;
    	$add['r_remark'] = $remark;
    	$add['sortid'] = ceil($sort['sort'])+1;
    	$rs = $version->where('r_grade_code="%s" and r_subject_code="%s" and r_term_code="%s" and r_version_code="%s" and isdel=1 and app_id=%d',$grade_code,$subject_code,$term_code,$version_code,$app_id)->find();
    	if(count($rs) > 0){
    		$data['msg']='该版本已经在该应用下存在，请重新选择';
    		$data['flag'] = 0;
    	}
    	else{
    		$version->add($add);
    		$data['msg']='添加成功';
    		$data['flag'] = 1;
    	}
    	$this->ajaxReturn($data);
    	
    }
    public function version_img_update(){
  
    	$pic = I('pic/s',0);
    	$this->assign('pic',$pic);
    	$this ->display();
    	
    }
    public function version_img_update_action(){
    	$id=I('id/d',0);
    	$pic = I('pic/s',0);
    	$version = M('version');
    	$version->r_pic = $pic;
    	$version->where('id=%d',$id)->save();
    	echo "1";
    }
    public function version_del(){
    	$id=I('version_id/d',0);
    	$version = M('version');
    	$version->isdel = 0;
    	$version->where('id=%d',$id)->save();
    }
    public function unitlist(){
    	$subject_code=I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
    	$app_id = I('app_id/d',0);
    	$this -> assign('subject_code',$subject_code);
    	$this -> assign('version_id',$version_id);
    	$this -> assign('unit_id',$unit_id);
    	$this -> assign('app_id',$app_id);
    	$this -> display();
    }
     public function nov_unitlist(){
    	$subject_code=I('subject_code/s',0);
    	$app_id = I('app_id/d',0);
    	$unit_id = I('unit_id/d',0);
    	$this -> assign('subject_code',$subject_code);
    	$this -> assign('app_id',$app_id);
    	$this -> assign('unit_id',$unit_id);
    	$this -> display('nov_unitlist');
    }
    public function unit_add(){
    	$id=I('id/d',0);
    	$subject_code=I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$app_id = I('app_id/d',0);
    	$isterm = I('isterm/d',0);
    	$isunit = I('isunit/d',0);
    	$unit_type = I('unit_type/d',0);
    	$unitdata = M('application')->field('app_unit_type')->where('id=%d',$app_id)->find();
    	if($id > 0){
    		$unit=M('unit');
    		$rs = $unit->where('id=%d',$id)->find();
    		$this->assign('unitname',$rs['name']);
    		$this->assign('isclick',$rs['is_click']);
    		$unit_type = $rs['is_img'];
    		$this->assign('pic',$rs['pic']);
    		$this->assign('picvalue',$rs['pic']);
    		$this->assign('sortid',$rs['sortid']);
    	}
    	else{
    		$this->assign('pic','uploads/learnimg/noimg.png');
    		$this->assign('picvalue','');
    	}
    	$this -> assign('id',$id);
    	$this -> assign('subject_code',$subject_code);
    	$this -> assign('version_id',$version_id);
    	$this -> assign('app_id',$app_id);
		$this -> assign('isterm',$isterm);
		$this -> assign('isunit',$isunit);
		$this->assign('unit_type',$unitdata['app_unit_type']);
    	$this -> display('unit_add');
    }
    public function Unit_save(){
    	$id=I('id/d',0);
    	$version_id = I('version_id/s',0);
    	$app_id = I('app_id/s',0);
    	$unit_name = I('unit_name/s',0);
    	$ks_code = I('ks_code/s',0);
    	$pic = I('pic/s',0);
    	$isclick = I('isclick/d',0);
    	$isimg = I('isimg/d',0);
    	$unit = M('unit');
    	$sort = $unit->field('max(id) as sort')->find();
    	$unit->version_id = $version_id;
    	$unit->app_id = $app_id;
    	$unit->ks_code = $ks_code;
    	$unit->name = $unit_name;
    	$unit->is_click = $isclick;
    	$unit->is_img = $isimg;
    	$unit->pic = $pic;
    	
    	if($id == 0){
    	  $unit->sortid = ceil($sort['sort'])+1;
    	  $unit_id = $unit->add();
    		$data['msg']='添加成功';
    		$data['flag'] = 1;
    		$data['unit_id'] = $unit_id;
    	}
    	else{
    		$unit->where('id=%d',$id)->save();
    		$data['msg']='修改成功';
    		$data['flag'] = 1;
    		$data['unit_id'] = $id;
    	}
    	$this->ajaxReturn($data);
    }
    public function getUnitList(){
    	$version_id = I('version_id/d',0);
    	$app_id = I('app_id/d',0);
    	$selec_unit_type = I('selec_unit_type/s',0);
    	$unit = M('unit');
    	if($selec_unit_type == 'version'){
    		$rs = $unit->where('version_id=%d and app_id=%d and isdel=1 and version_id !=0',$version_id,$app_id)->order('sortid,id')->select();
    	}
    	else{
    		$rs = $unit->where('app_id=%d and version_id =0 and isdel=1 and app_id !=0',$app_id)->order('sortid,id')->select();
    	}
    	$this->ajaxReturn($rs);
    }
    public function edit_unit_sort() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $unit = M("unit");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $unit->sortid = $sortid;
            $result = $unit->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = "修改成功";
        $this -> ajaxReturn($arr_return);
    }
    public function unit_del(){
    	$id=I('unit_id/d',0);
    	$unit = M('unit');
    	$unit->isdel = 0;
    	$unit->where('id=%d',$id)->save();
    }
    public function app_que_list(){
    	$video_id = I('video_id/d',0);
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$subject_code = I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	//echo $version_id;exit;
    	$unit_id = I('unit_id/d',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->where('isdel = 1')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('isdel = 1 and app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			if(count($appidarr) >0){
				$app_id = $appidarr['id'];
			}	
		}
		if($app_id > 0){
			$appresult = $appdata->where('id=%d',$app_id)->find();
			$this -> assign('app_id',$app_id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_content',$appresult['app_content']);
			$this -> assign('app_pic',$appresult['app_pic']);
		}
		else{
			$this -> assign('app_id',$app_id);
			$this -> assign('subject_code',$subject_code);

		}
		$this -> assign('video_id',$appresult['video_id']);
		$this -> assign('version_id',$version_id);
		$this -> assign('unit_id',$unit_id);
		$this -> display();
    }

    public function getQueList(){
    	$course_id = I('course_id/d',0);
    	$que = M('question');
    	$rs = $que->where('course_id=%d and isdel=1 and type in(1,2)',$course_id)->order('sortid','id')->select();
    	foreach ($rs as $key => $value) {
    		$itemsdata = $value['itemsdata'];
    		$itemsdata = rtrim($itemsdata, '"');
	        $itemsdata = ltrim($itemsdata, '"');
	        $itemsdata = str_replace('&quot;', '"', $itemsdata);
	        $itemsdata = json_decode($itemsdata);
	        $rs[$key]['itemsdata'] =$itemsdata;
	        $rs[$key]['content'] = html_entity_decode($value['content']);
    	}
    	//var_dump($rs[0]);exit;
    	$this->ajaxReturn($rs);
    }
    public function que_add(){
    	$id=I('id/d',0);
    	$course_id=I('course_id/d',0);
    	$app_id=I('app_id/d',0);
    	$flagarr[0]['flag'] = 'A';
    	$flagarr[1]['flag'] = 'B';
    	$flagarr[2]['flag'] = 'C';
    	$flagarr[3]['flag'] = 'D';
    	$flagarr[4]['flag'] = 'E';
    	$flagarr[5]['flag'] = 'F';
    	$flagarr[6]['flag'] = 'G';
    	if($id > 0){
    		$que=M('question');
    		$rs = $que->where('id=%d',$id)->find();
    		$content = $rs['content'];
    		$answer = $rs['answer'];
    		$sortid = $rs['sortid'];
    		$itemtype = $rs['itemtype'];
    		$type = $rs['type'];
    		$itemsdata = $rs['itemsdata'];
    		$itemsdata = rtrim($itemsdata, '"');
	        $itemsdata = ltrim($itemsdata, '"');
	        $itemsdata = str_replace('&quot;', '"', $itemsdata);
	        $itemsdata = json_decode($itemsdata);
	        if($rs['type']==1){
	        	if($rs['itemtype']==0){
	        		//var_dump($itemsdata);
	        		foreach ($itemsdata as $key => $value) {
	        			//echo $value->content;
	        			$this ->assign('content'.$value->flag,$value->content);
	        			
	        		}
	        	}
	        	else{
	        		
			        $this ->assign('pic'.$value->flag,$value->content);
	        	}
	        }
	       
    		$this ->assign('quetitle',html_entity_decode($content));
    		$this ->assign('itemsdata',$itemsdata);
    		$this ->assign('trueanswer',$answer);
    		$this ->assign('sortid',$sortid);
    		$this ->assign('itemtype',$itemtype);
    		$this ->assign('type',$type);
    	}
    	$this ->assign('id',$id);
    	$this ->assign('app_id',$app_id);
    	$this ->assign('course_id',$course_id);
    	$this ->assign('flagarr',$flagarr);
    	$this ->display();
    }
    public function Que_save(){
    	 //  $.getJSON('../classapp/Que_save',{id:id,type:type,itemtype:itemtype,sortid:sortid,quetitle:quetitle,itemsdata:itemsdata,trueanswer:trueanswer
    	$course_id = I('course_id/d',0);
    	$id = I('id/d',0);
    	$type = I('type/d',0);
    	$app_id = I('app_id/d',0);
    	$itemtype = I('itemtype/d',0);
    	$quetitle = I('quetitle/s',0);
    	$itemsdata = I('itemsdata/s',0);
    	$trueanswer = I('trueanswer/s',0);
    	$que=M('question');
    	$sort = $que->field('max(id) as sort')->find();
    	$que -> app_id = $app_id;
    	$que -> course_id = $course_id;
    	$que -> type = $type;
    	$que -> content = $quetitle;
    	$que -> itemtype = $itemtype;
    	$que -> answer = $trueanswer;
    	$que -> itemsdata = $itemsdata;
    	
    	if($id == 0){
    		$que -> sortid = ceil($sort['sort'])+1;
    		$id = $que->add();
    	}
    	else{
    		$que->where('id=%d',$id)->save();
    	}
    	if($id > 0){
    		$arr_return["msg"] = "修改成功";
    		$arr_return["flag"] = "1";
    	}
    	else{
    		$arr_return["msg"] = "修改失败";
    		$arr_return["flag"] = "0";
    	}
        $this -> ajaxReturn($arr_return);
    }
    public function edit_que_sort() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $que = M("question");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $que->sortid = $sortid;
            $result = $que->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = "修改成功";
        $this -> ajaxReturn($arr_return);
    }
    public function que_del(){
    	$id=I('que_id/d',0);
    	$que = M('question');
    	$que ->isdel = 0;
    	$que ->where('id=%d',$id)->save();
    }
    public function app_claim_list(){
    	$video_id = I('video_id/d',0);
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$subject_code = I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->where('isdel = 1')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('isdel = 1 and app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			if(count($appidarr) >0){
				$app_id = $appidarr['id'];
			}	
		}
		if($app_id > 0){
			$appresult = $appdata->where('id=%d',$app_id)->find();
			$this -> assign('app_id',$app_id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_content',$appresult['app_content']);
			$this -> assign('app_pic',$appresult['app_pic']);
		}
		else{
			$this -> assign('app_id',$app_id);
			$this -> assign('subject_code',$subject_code);
		}
		$this -> assign('video_id',$video_id);
		$this -> assign('unit_id',$unit_id);
		$this -> assign('version_id',$version_id);
		$this -> display();
    }
    public function getClaimList(){
    	$course_id = I('course_id/d',0);
    	$que = M('question');
    	$rs = $que->where('course_id=%d and isdel=1 and type =3',$course_id)->order('sortid','id')->select();
    	//var_dump($rs[0]);exit;
    	$this->ajaxReturn($rs);
    }
    public function claim_add(){
    	$course_id = I('course_id/d',0);
    	$id = I('id/d',0);
    	$app_id = I('app_id/d',0);
    	if($id >0){
    		$que=M('question');
    		$rs = $que->where('id=%d',$id)->find();
    		$this->assign('title',$rs['content']);
    		$this ->assign('content',html_entity_decode($rs['remark']));
    	}
    	$this->assign('course_id',$course_id);
    	$this->assign('id',$id);
    	$this->assign('app_id',$app_id);
    	$this->display();
    }
    public function Claim_save(){
    	 //  $.getJSON('../classapp/Que_save',{id:id,type:type,itemtype:itemtype,sortid:sortid,quetitle:quetitle,itemsdata:itemsdata,trueanswer:trueanswer
    	$course_id = I('course_id/d',0);
    	$id = I('id/d',0);
    	$app_id = I('app_id/d',0);
    	$quetitle = I('quetitle/s',0);
    	$claim_content = I('claim_content/s',0);
    	$que=M('question');
    	$sort = $que->field('max(id) as sortid')->find();
    	$sortid = ceil($sort['sortid'])+1;
    	$que -> app_id = $app_id;
    	$que -> course_id = $course_id;
    	$que -> type = 3;
    	$que -> content = $quetitle;
    	$que -> remark = $claim_content;
    	$que -> sortid = $sortid;
    	if($id == 0){
    		$id = $que->add();
    	}
    	else{
    		$que->where('id=%d',$id)->save();
    	}
    	if($id > 0){
    		$arr_return["msg"] = "修改成功";
    		$arr_return["flag"] = "1";
    	}
    	else{
    		$arr_return["msg"] = "修改失败";
    		$arr_return["flag"] = "0";
    	}
        $this -> ajaxReturn($arr_return);
    }
    //-----------------------
    public function app_essay_list(){
    	$video_id = I('video_id/d',0);
    	$app_id = I('app_id/d',0);
    	$appdata = M('application');
    	$subject_code = I('subject_code/s',0);
    	$version_id = I('version_id/d',0);
    	$unit_id = I('unit_id/d',0);
		if($app_id == 0){
			if($subject_code == 0 || $subject_code == 'all' ){
				$appidarr = $appdata->field('id')->where('isdel = 1')->order('sortid')->find();
			}
			else{
				$appidarr = $appdata->field('id')->where('isdel = 1 and app_subject_code="%s"',$subject_code)->order('sortid')->find();
			}
			if(count($appidarr) >0){
				$app_id = $appidarr['id'];
			}	
		}
		if($app_id > 0){
			$appresult = $appdata->where('id=%d',$app_id)->find();
			$this -> assign('app_id',$app_id);
			$this -> assign('gradestr',$gradestr);
			$this -> assign('app_name',$appresult['app_name']);
			$this -> assign('subject_code',$appresult['app_subject_code']);
			$this -> assign('isterm',$appresult['app_isterm']);
			$this -> assign('isunit',$appresult['app_isunit']);
			$this -> assign('unit_type',$appresult['app_unit_type']);
			$this -> assign('isspecial',$appresult['app_isspecial']);
			$this -> assign('special_code',$appresult['app_special_code']);
			$this -> assign('issource',$appresult['app_issource']);
			$this -> assign('source_code',$appresult['app_source_code']);
			$this -> assign('app_drumbeating',$appresult['app_drumbeating']);
			$this -> assign('app_content',$appresult['app_content']);
			$this -> assign('app_pic',$appresult['app_pic']);
		}
		else{
			$this -> assign('app_id',$app_id);
			$this -> assign('subject_code',$subject_code);
		}
		$this -> assign('video_id',$video_id);
		$this -> assign('unit_id',$unit_id);
		$this -> assign('version_id',$version_id);
		$this -> display();
    }
    public function getEssayList(){
    	$course_id = I('course_id/d',0);
    	$que = M('question');
    	$rs = $que->where('course_id=%d and isdel=1 and type =4',$course_id)->order('sortid','id')->select();
    	//var_dump($rs[0]);exit;
    	$this->ajaxReturn($rs);
    }
    public function Essay_save(){
    	 //  $.getJSON('../classapp/Que_save',{id:id,type:type,itemtype:itemtype,sortid:sortid,quetitle:quetitle,itemsdata:itemsdata,trueanswer:trueanswer
    	$course_id = I('course_id/d',0);
    	$id = I('id/d',0);
    	$app_id = I('app_id/d',0);
    	$quetitle = I('quetitle/s',0);
    	$essay_content = I('essay_content/s',0);
    	$author = I('author/s',0);
    	$que=M('question');
    	$sort = $que->field('max(id) as sortid')->find();
    	$sortid = ceil($sort['sortid'])+1;
    	$que -> app_id = $app_id;
    	$que -> course_id = $course_id;
    	$que -> type = 4;
    	$que -> content = $quetitle;
    	$que -> remark = $essay_content;
    	$que -> author = $author;
    	$que -> sortid = $sortid;
    	if($id == 0){
    		$id = $que->add();
    	}
    	else{
    		$que->where('id=%d',$id)->save();
    	}
    	if($id > 0){
    		$arr_return["msg"] = "修改成功";
    		$arr_return["flag"] = "1";
    	}
    	else{
    		$arr_return["msg"] = "修改失败";
    		$arr_return["flag"] = "0";
    	}
        $this -> ajaxReturn($arr_return);
    }
    public function essay_add(){
    	$course_id = I('course_id/d',0);
    	$id = I('id/d',0);
    	$app_id = I('app_id/d',0);
    	if($id >0){
    		$que=M('question');
    		$rs = $que->where('id=%d',$id)->find();
    		$this->assign('title',$rs['content']);
    		$this ->assign('content',html_entity_decode($rs['remark']));
    		$this->assign('author',$rs['author']);
    	}
    	$this->assign('course_id',$course_id);
    	$this->assign('id',$id);
    	$this->assign('app_id',$app_id);
    	$this->assign('author','优教通');
    	$this->display();
    }

      public function getResTreeNodes(){
        $id=I('id/s','');
        $m=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        if ($id==''){
            $max = 6;//默认6级
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE IS_UNIT='0' and flag<>'0' AND KS_LEVEL=2 ORDER BY DISPLAY_ORDER";
            //$sql="SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>6,'true','false') as isParent,C1,DISPLAY_ORDER  FROM t_share_knowledge_structure WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL=2 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b')";
        }else {
            $sql_l = "SELECT max(KS_LEVEL) as total FROM SHARE_KNOWLEDGE_STRUCTURE WHERE FLAG=1 AND KS_CODE like '".$id."%';";
            $re = $m->query($sql_l);
            //var_dump($re);exit;
            $max = $re[0]['total'];//最大level
            //$sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(c2<>1,'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";

        }
        $data_mulu = $m->query($sql);

        $json="[";
        foreach ($data_mulu as $v){
            $json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'",isParent:"'.$v['isparent'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_level'].'", maxLevel:"'.$max.'"},';
        }
        $json = rtrim($json,',');
        $json = $json.']';
        echo $json;
    }
    public function queryRes(){
    	$ks_code=I('ks_code/s',0);
    	$Model=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
    	$sql="SELECT ro.R_CODE,ro.R_TITLE,se.c1 FROM RMS_RESOURCEINFO ro,SHARE_KNOWLEDGE_STRUCTURE se WHERE ro.R_KS_ID=se.KS_CODE AND ro.R_STATE='1' and R_FORMAT_MARK='mp4' AND (ro.R_HASHCODE='' OR ro.R_HASHCODE IS NULL) AND se.KS_CODE='%s' ORDER BY ro.R_EXT5";
    	//echo $sql;
    	$data=$Model->query($sql,$ks_code);
    	$this->ajaxReturn($data);
    }
    public function queryRes2(){
    	$search_value=I('search_value/s',0);
    	$Model=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
    	$sql="SELECT ro.R_CODE,ro.R_TITLE,se.c1 FROM RMS_RESOURCEINFO ro,SHARE_KNOWLEDGE_STRUCTURE se WHERE ro.R_KS_ID=se.KS_CODE AND ro.R_STATE='1' and R_FORMAT_MARK='mp4' AND (ro.R_HASHCODE='' OR ro.R_HASHCODE IS NULL) AND ro.R_TITLE like '%".$search_value."%' ORDER BY ro.R_EXT5";
    	//echo $sql;
    	$data=$Model->query($sql);
    	$this->ajaxReturn($data);
    }
    
    public function videoplay(){
    	$code = I('code/s',0);
    	$this ->assign('code',$code);
    	$this->display();
    }
	public function Evaluatelist_qg(){
		$area = I('area/d',0);
		$this ->assign('area',$area);
    	$this->display();
    }
    public function managerEvaluate_qg(){
    	$area=I('area/d',0);
    	if($area == 0){
    		$Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@60.217.236.216/db_microclass');
    	}
    	else if($area == 1){
    		$Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@117.169.19.236/db_microclass');
    	}
    	else{
    		$Model=M('',null,'mysql://vcom:yjt_yyl20160309@10.181.155.238/db_microclass');
    	}
    	$subject_code = I('subject_code/s','0');
    	$app_id = I('app_id/d',0);
    	if($subject_code == 'all'){
    		$sql="select a.*,b.app_name from mc_evaluate a left join mc_application b on a.app_id = b.id order by a.id desc";
    	}
    	else{
    		$sql="select a.*,b.app_name from mc_evaluate a , mc_application b where a.app_id = b.id and a.app_id=".$app_id." order by a.id desc";
    	}
    	$data=$Model->query($sql);
    	$this->ajaxReturn($data);
    	//var_dump($data);
    }
    public function evaluate_del_qg(){
    	$area=I('area/d',0);
    	if($area == 0){
    		$Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@60.217.236.216/db_microclass');
    	}
    	else if($area == 1){
    		$Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@117.169.19.236/db_microclass');
    	}
    	else{
    		$Model=M('',null,'mysql://vcom:yjt_yyl20160309@10.181.155.238/db_microclass');
    	}
    	$flag=I('flag/d',0);
    	$id=I('id/d',0);
    	$ids=I('ids/s','0');
    	if($flag == 0){
    		$data=$Model->table('mc_evaluate')->where("id=".$id)->delete();
    	}
    	else{
    		$ids=urldecode($ids);
        	$ids = rtrim($ids, '"');
        	$ids = ltrim($ids, '"');
        	$ids = str_replace('&quot;', '"', $ids);
        	$ids = json_decode($ids);
        	//var_dump($ids);
        	foreach ($ids as $key => $value) {
        		$id = $value->id;
        		$data=$Model->table('mc_evaluate')->where("id=".$id)->delete();
        	}
    	}

    }

    public function getResKsTreeNodes(){
        $id=I('id/s','');
        $m=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        if ($id==''){
            $max = 6;//默认6级
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE IS_UNIT='0' and flag<>'0' AND KS_LEVEL=2 ORDER BY DISPLAY_ORDER";
            //$sql="SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>6,'true','false') as isParent,C1,DISPLAY_ORDER  FROM t_share_knowledge_structure WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL=2 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b')";
        }else {
            $sql_l = "SELECT max(KS_LEVEL) as total FROM SHARE_KNOWLEDGE_STRUCTURE WHERE FLAG=1 AND KS_CODE like '".$id."%';";
            $re = $m->query($sql_l);
            //var_dump($re);exit;
            $max = $re[0]['total'];//最大level
            //$sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(c2<>1,'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";

        }
        $data_mulu = $m->query($sql);

        $json="[";
        foreach ($data_mulu as $v){
            $json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'",isParent:"'.$v['isparent'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_level'].'", maxLevel:"'.$max.'"},';
        }
        $json = rtrim($json,',');
        $json = $json.']';
        echo $json;
    }
}