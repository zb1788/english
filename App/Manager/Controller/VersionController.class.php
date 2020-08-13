<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class VersionController extends CheckController {
	
     public function getVersionlist() {
    	$type=I("type/d");
    	$Dao = M('rms_dictionary'); // 实例化Word对象
    	if($type==0){
    		$Data = $Dao->field('id,detail_name,detail_name_short,detail_order')->where('dictionary_code = "edition"')->order('detail_order')->select();
    	}else if($type==1){
    		$Data = $Dao->field('id,detail_name,detail_name_short,detail_order')->where('dictionary_code = "edition1"')->order('detail_order')->select();
    	}
        $this->ajaxReturn($Data);
    }


    public function version_edit() {
    	$id=I("id");
    	$Dao = M('rms_dictionary'); // 实例化Word对象
    	$rs=$Dao->where("id='%s'",$id)->find();
    	$this->assign("id",$rs["id"]);
    	$this->assign("sortid",$rs["sortid"]);
    	$this->assign("detail_name",$rs["detail_name"]);
    	$this->assign("detail_name_short",$rs["detail_name_short"]);
        $this->display("version_edit");
    }

    public function delversion(){
    	$id=I("id");
    	//判断如果这里有k的话才能删除
    	$first=substr($id,0,1);
    	if($first=='k'){
    		$Dao = M('rms_dictionary'); // 实例化Word对象
    		$Dao->where("id='%s'",$id)->delete();
    	}
    }

    /**
     * versionEdit  
     * 版本信息进行添加或者编辑
     * 
     * @param  void
     * @return json 返回版本以及使用此版本的年级信息
     */
    public function versionEdit() {
        $id = I("id/s");
        $detail_name_short = I("detail_name_short/s");
        $Dao = M('rms_dictionary'); // 实例化Word对象
        //$version -> name = trim($name); 
        $Dao->detail_name_short = $detail_name_short;
        if (empty($id)) {
            $this->ajaxReturn("修改失败！ID为空");
        }
        else{
            $Dao->where("id = '%s'", $id)->save();
            $this->ajaxReturn("修改成功！");
        }
        
    }

	public function getVersionimg() {
        $Dao = M(''); // 实例化Word对象
		$r_grade = I('r_grade/s',0);
		$r_volume = I('r_volume/s',0);

        $sql = "SELECT detail_code,detail_name FROM engs_rms_dictionary a WHERE a.detail_code IN (SELECT DISTINCT t.r_version FROM engs_rms_unit t WHERE t.r_grade ='".$r_grade."' AND t.r_volume = '".$r_volume."' AND t.is_unit = 0) AND a.dictionary_code = 'edition' ORDER BY a.detail_order ASC";
		$rs = $Dao->query($sql);
		foreach($rs as $key=>$value){
			$r_version = $value['detail_code'];
			$sql = "select id,pic_path,isdel from engs_version_img where r_grade='".$r_grade."' and r_volume='".$r_volume."' and r_version='".$r_version."' order by id desc";
			$rs1 = $Dao->query($sql);
			//echo count($rs1)."<br>";
			if(count($rs1) == 0){
				$rs[$key]['imgid'] = 0;
				$rs[$key]['pic_path'] = 0;
				$rs[$key]['isdel'] = 0;
			}
			else{
				$rs[$key]['imgid'] = $rs1[0]['id'];
				$rs[$key]['pic_path'] = $rs1[0]['pic_path'];
				$rs[$key]['isdel'] = $rs1[0]['isdel'];
			}
		}
		//var_dump($rs);
		$this->ajaxReturn($rs);
    }
	public function version_img_update(){
		$imgid = I('id/d',0);
		$pic = I('pic/s',0);
		$this->assign('imgid',$imgid);
		$this->assign('pic',$pic);
		$this->display();
		
	}
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

	public function version_img_update_action(){
		//imgid:imgid,r_grade:r_grade,r_volume:r_volume,r_version:r_version,pic:pic
		$imgid = I('imgid/d',0);
		$r_grade = I('r_grade/s',0);
		$r_volume = I('r_volume/s',0);
		$r_version = I('r_version/s',0);
		$r_subject = I('r_subject/s',0);
		$pic = I('pic/s',0);
		$dataimg = M('version_img');
		if($imgid == 0){ //新增
			$dataimg -> r_grade = $r_grade;
			$dataimg -> r_volume = $r_volume;
			$dataimg -> r_version = $r_version;
			$dataimg -> r_subject = $r_subject;
			$dataimg -> pic_path = $pic;
			$flag = $dataimg -> add();
			if($flag){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		else{		//修改
			$dataimg -> pic_path = $pic;
			$flag = $dataimg -> where('id='.$imgid)->save();
			if($flag){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		
	}
	
	 public function versionotheredit(){
		$id=I("id/s","");
		$unitalias=I("unitalias");
		$unitname=I("unitname");
		$sortid=I("sortid");
		$rms_dictionary=M("rms_dictionary");
		if(empty($id)){
			$rms_dictionary->dictionary_code='edition1';
			$rms_dictionary->detail_code="k".rand(1000,9999);
			$rms_dictionary->detail_name_short=$unitalias;
			$rms_dictionary->detail_name=$unitname;
			$rms_dictionary->detail_order=$sortid;
			$rms_dictionary->sortid=$sortid;
			$rms_dictionary->update_time=time();
			$rms_dictionary->id='k'.md5(time().rand(10000,99999));
			$rms_dictionary->add();
		}else{
			$rms_dictionary->detail_name_short=$unitalias;
			$rms_dictionary->detail_name=$unitname;
			$rms_dictionary->detail_order=$sortid;
			$rms_dictionary->sortid=$sortid;
			$rms_dictionary->update_time=time();
			$rms_dictionary->where("id='%s'",$id)->save();
		}
	}
	public function version_img_disable(){
		$id = I("imgid/d",0);
		$flag = I("flag/d",0);
		$rs=M("version_img");
		$rs->isdel=$flag;
		$rs->where("id='%s'",$id)->save();
	}
}

?>
