<?php

namespace Ywmanager\Controller;

use Think\Controller;

class VersionController extends CheckController {

    /**
     * getVersionlist
     * 获取数据库中的版本列表
     *
     * @param  void
     * @return json 返回版本以及使用此版本的年级信息
     */
    public function getVersionlist() {
        // $Dao = M('db_english.rms_dictionary','engs'); // 实例化Word对象
        $Dao = M('rms_dictionary'); // 实例化Word对象
        $Data = $Dao->field('id,detail_name,detail_name_short,detail_order')->where('dictionary_code="edition"')->order('detail_order')->select();
        $this->ajaxReturn($Data);
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

        if (empty($id)) {
            $this->ajaxReturn("修改失败！ID为空");
        }
        else{
            $data = $Dao->where("id = '%s'", $id)->find();
            $re = $Dao->where("id = '%s'", $id)->setField('DETAIL_NAME_SHORT',$detail_name_short);

            A('Ywmanager/User')->recordUserOption('rms_dictionary','update','id='.$id);

            $this->ajaxReturn("修改成功！");
        }

    }

    public function imglist(){
        $r_subject = I('r_subject/s','');
        $this->assign('r_subject',$r_subject);
        $this->display();
    }


	public function getVersionimg() {
        $Dao = M(''); // 实例化Word对象
		$r_grade = I('r_grade/s',0);
        $r_volume = I('r_volume/s',0);
		$r_subject = I('r_subject/s',0);

        // $sql = "SELECT l.id,l.r_version,l.pic_path,(SELECT t.DETAIL_NAME_SHORT FROM db_english.engs_rms_dictionary t WHERE t.DICTIONARY_CODE='edition' AND t.DETAIL_CODE=l.r_version) as version_name,l.pic_path FROM db_english.engs_version_img l WHERE l.r_grade='".$r_grade."' AND l.r_volume='".$r_volume."' and l.r_subject='".$r_subject."';";
        $sql = "SELECT l.id,l.r_version,l.pic_path,(SELECT t.DETAIL_NAME_SHORT FROM yw_rms_dictionary t WHERE t.DICTIONARY_CODE='edition' AND t.DETAIL_CODE=l.r_version) as version_name,l.pic_path FROM yw_version_img l WHERE l.r_grade='".$r_grade."' AND l.r_volume='".$r_volume."' and l.r_subject='".$r_subject."';";
        $rs = $Dao->query($sql);
		$this->ajaxReturn($rs);
    }
	public function version_img_update(){
		$id = I('id/d',0);
        $pic = I('pic/s','');
        $type = I('type/s','');
        $version = I('version/s','');

        $Dao = M('rms_dictionary'); // 实例化Word对象
        // $Dao = M('db_english.rms_dictionary','engs_');
        $Data = $Dao->field('id,detail_code,detail_name_short,detail_order')->where('dictionary_code="edition"')->order('detail_order')->select();

        $this->assign('data',$Data);
		$this->assign('imgid',$id);
        $this->assign('pic',$pic);
        $this->assign('version',$version);
		$this->display();

	}

    public function delversion(){
        $id = I('id/d',0);

        // $dataimg = M('db_english.version_img','engs_');
        $dataimg = M('version_img');
        $dataimg->where('id="%d"',$id)->delete();
        A('Ywmanager/User')->recordUserOption('version_img','del','id='.$id);
    }


	public function Versionimgupload(){
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS_IMG");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		//$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = false;
		//$upload->subName  = array('date','Ym');
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
        $r_subject = I('r_subject/s','');
		$type = I('type/s',0);
		$pic = I('pic/s',0);

        // $dataimg = M('db_english.version_img','engs_');
		$dataimg = M('version_img');

		if($type == 'add'){ //新增
			$dataimg -> r_grade = $r_grade;
			$dataimg -> r_volume = $r_volume;
            $dataimg -> r_version = $r_version;
			$dataimg -> r_subject = $r_subject;
			$dataimg -> pic_path = 'uploadsyw/versionimg/'.$pic;
			$flag = $dataimg -> add();

            // A('Ywmanager/User')->recordUserOption('version_img','add','id='.$flag);

			if($flag){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		else{		//修改
			$dataimg -> pic_path = 'uploadsyw/versionimg/'.$pic;
			$flag = $dataimg -> where('id='.$imgid)->save();

            // A('Ywmanager/User')->recordUserOption('version_img','add','id='.$imgid);

			if($flag){
				echo "1";
			}
			else{
				echo "0";
			}
		}

	}

}

?>
