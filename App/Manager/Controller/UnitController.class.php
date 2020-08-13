<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class UnitController extends CheckController {

    /**
     * unitedit  
     * 修改单元简称
     * 
     * @param void
     * @return json 返回
     */
    public function unitedit() {
        $ks_code = I("ks_code/s");
        $ks_name_short = I("ks_name_short/s");
        // echo $ks_code.$ks_name_short;
        $ks_name_short = trim(str_replace("  ", " ", $ks_name_short));
        $model = M('rms_unit');
        //修改 
        $model->ks_name_short = $ks_name_short;
        $result = $model->where("ks_code='%s'", $ks_code)->save();
        $arr_data["msg"] = "修改成功";
        $this->ajaxReturn($arr_data);
    }

    /**
     * 获取单元列表
     * 
     */
    public function getunitlist() {
        $gradeid = I("gradeid/s", 0);
        $versionid = I("versionid/s", 0);
        $termid = I("termid/s", 0);
        $unit = M("rms_unit");
        $rs = $unit->where("flag = 1 and r_grade ='%s' and r_version ='%s' and r_volume='%s' ", $gradeid, $versionid, $termid)->field("ks_code,r_grade,r_version,r_volume,ks_name,ks_name_short,is_unit,display_order")->order("display_order")->select();
        $this->ajaxReturn($rs);
    }
	
	
	/**
     * 获取单元列表
     * 
     */
    public function transedit() {
        $gradeid = I("gradeid/s", 0);
        $versionid = I("versionid/s", 0);
        $termid = I("termid/s", 0);
        $unitid=I("unitid/s",0);
        $data=I("data/s",0);
        $type=I("type/s",0);
        $this->assign("gradeid",$gradeid);
        $this->assign("versionid",$versionid);
        $this->assign("termid",$termid);
        $this->assign("unitid",$unitid);
        $this->assign("data",$data);
        $this->assign("type",$type);
        $this->display();
    }
   
     public function unitOtherEdit(){
        $id=I("id");
        $gradeid=I("gradeid");
        $termid=I("termid");
        $versionid=I("versionid");
        $subjectid="0003";
        $unitalias=I("unitalias");
        $unitname=I("unitname");
        $isunit=I("isunit");
        $sortid=I("sortid");
        $rms_unit=M("rms_unit");
        //年级 班班通.一年级.下学期.英语.上海牛津版（全国）.Unit1 New Year
        $graders=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'grade1'",$gradeid)->find();
        $termrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'volume'",$termid)->find();
        $versionrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'edition1'",$versionid)->find();
        if(empty($id)){
            $id="k".uniqid();
            $rms_unit->ks_id=$id;
            $rms_unit->business_type="26";
            $rms_unit->ks_level=6;
            $rms_unit->c2=1;
            $rms_unit->c3=0;
            $rms_unit->c4=$versionrs["detail_name"];
            $rms_unit->c5=0;
            $rms_unit->c6="";
            $rms_unit->flag=1;
            $rms_unit->display_able=1;
            $rms_unit->ks_type=0;
            $rms_unit->update_time=time();
            $rms_unit->ks_code=$id;
            $rms_unit->ks_name=$unitname;
            $rms_unit->ks_name_short=$unitalias;
            $rms_unit->display_order=$sortid;
            $rms_unit->c1="班班通.".$graders["detail_name"].".".$termrs["detail_name"].".英语.".$versionrs["detail_name"].".".$unitname;
            $rms_unit->is_unit=$isunit;
            $rms_unit->r_grade=$gradeid;
            $rms_unit->r_volume=$termid;
            $rms_unit->r_subject="0003";
            $rms_unit->r_version=$versionid;
            $rms_unit->add();
        }else{
            $rms_unit->ks_name=$unitname;
            $rms_unit->ks_name_short=$unitalias;
            $rms_unit->display_order=$sortid;
            $rms_unit->c1="班班通.".$graders["detail_name"].".".$termrs["detail_name"].".英语.".$versionrs["detail_name"].".".$unitname;
            $rms_unit->is_unit=$isunit;
            $rms_unit->r_grade=$gradeid;
            $rms_unit->r_volume=$termid;
            $rms_unit->r_subject="0003";
            $rms_unit->r_version=$versionid;
            $rms_unit->where("ks_code='%s'",$id)->save();
        }
    }


    public function unit_edit(){
        $ks_code=I("id");
        $gradeid=I("gradeid");
        $termid=I("termid");
        $versionid=I("versionid");
        $rs=M("rms_unit")->where("ks_code='%s'",$ks_code)->find();
        $this->assign("id",$ks_code);
        if(empty($id)){
            $this->assign("gradeid",$gradeid);
            $this->assign("termid",$termid);
            $this->assign("versionid",$versionid);
        }else{
            $this->assign("gradeid",$gradeid);
            $this->assign("termid",$rs["r_volume"]);
            $this->assign("versionid",$rs["r_version"]);
        }
        $this->assign("isunit",$rs["is_unit"]);
        $this->assign("sortid",$rs["display_order"]);
        $this->assign("unitname",$rs["ks_name"]);
        $this->assign("unitalias",$rs["ks_name_short"]);
        $this->display("unit_edit");
    }

    public function delUnit(){
        $id=I("id");
        M("rms_unit")->where("ks_code='%s'",$id)->delete();
        //删除单词
        M("word")->where("ks_code='%s'",$id)->setField("isdel",0);
        //删除课文章节
        M("text_chapter")->where("ks_code='%s'",$id)->setField("isdel",0);
    }

}
