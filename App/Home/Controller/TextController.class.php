<?php

namespace Home\Controller;

use Think\Controller;

class TextController extends Controller {
    public function index(){ //首页
        //获取用户生词本数量
        $this -> display('index');
    }

    //判断版本下是否有资源存在
    public function text_isexists(){
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        $sqlquery = M();
        $sql = 'select a.ks_code,a.ks_name_short from engs_rms_unit as a,engs_text_chapter as b where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and a.ks_code=b.ks_code and a.r_subject="0003" and b.isdel =1  group by b.ks_code order by a.display_order';
        $unitinfo = $sqlquery->query($sql,$gradeid,$termid,$versionid);
        $this->ajaxReturn($unitinfo);
    }
    /**
     * 获取版本下同步课文的单元信息
     */
    public function unitinfo_text() {   //获取同步课文的单元信息
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        $sqlquery = M();
        //$sql = 'select a.ks_code,a.ks_name_short from engs_rms_unit as a,engs_text_chapter as b,engs_text as c where a.flag=1 and a.r_grade = "'.$gradeid.'" and a.r_volume = "'.$termid.'" and a.r_version = "'.$versionid.'" and a.ks_code=b.ks_code and b.isdel =1 and c.stateid=1 and c.isdel=1 and c.chapterid = b.id group by b.ks_code order by a.display_order';
        //echo $sql;
         $sql = 'select a.ks_code,a.ks_name_short,is_unit,sum(case when b.ks_code is not null then 1 else 0 end) as textcount from engs_rms_unit as a left join engs_text_chapter b on a.ks_code = b.ks_code and b.isdel=1 where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and a.r_subject="0003" group by a.ks_code having is_unit+textcount>0 order by a.display_order';
        $unitinfo = $sqlquery->query($sql,$gradeid,$termid,$versionid);
        //$this->assign('unitinfo', $unitinfo);
        $this->ajaxReturn($unitinfo);
    }

    /**
     * 获取单元下的课文章节列表
     */
    public function get_chapter_list() {
        $unitid = I('unitid/s', 0);
        $sqlquery = M();
       // $sql = 'select id,chapter,issection,sortid,(select count(*) from engs_study_text b where b.chapterid = a.id)  as studynum  from engs_text_chapter a where a.ks_code = "%s" and isdel = 1 order by a.sortid,a.id';
        $sql = 'select id,chapter,issection,sortid,(select count(*) from engs_study c where c.engs_studyid = a.id and c.studytype=2 and c.ks_code != "0")  as studynum from engs_text_chapter a where a.ks_code = "%s" and isdel = 1 order by a.sortid,a.id';
        $chapter_list = $sqlquery->query($sql, $unitid);
        $this->assign('unitid', $unitid);
        $this->assign('chapter_list', $chapter_list);
        $this->display('get_chapter_list');
    }

    /**
     * 获取课文章节详细内容
     */
    public function get_text_info() {
        $unitid = I('unitid/s', 0); //当前单元
        $chapter_page = I('chapter_page/d', 0); //当前单节索引值
		$gradeid = I('gradeid/s', 0); //当前年级
		$termid = I('termid/s', 0); //当前学期
		$versionid = I('versionid/s', 0); //当前版本
        $sqlquery = M();
        $sql = 'select id,chapter,issection,sortid from engs_text_chapter a where a.ks_code = "%s" and isdel = 1 order by sortid,id';
        $chapter_list = $sqlquery->query($sql, $unitid); //获取章节列表
        $chapter_id = $chapter_list[$chapter_page]['id']; //章节ID
        $chapter_title = $chapter_list[$chapter_page]['chapter']; //章节ID
        $issection = $chapter_list[$chapter_page]['issection']; //0对话;1自然段
        $sql = 'select sectionid,enbefore,encontent,cncontent,mp3 from engs_text where chapterid = %d  and isdel = 1 and stateid = 1 order by sortid,id';
        $text_list = $sqlquery->query($sql,$chapter_id);
        if ($issection == '1') {  //段落形式
            $text_duan_info = array();
            $sql = 'select sectionid from engs_text where chapterid = %d  and isdel = 1 and stateid = 1 group by sectionid order by sortid,id';
            $text_duan_list = $sqlquery->query($sql,$chapter_id);
            for ($i = 0; $i < count($text_duan_list); $i++) {
                $sql = 'select sectionid,enbefore,encontent,cncontent,mp3 from engs_text where chapterid = %d  and isdel = 1 and stateid = 1 and sectionid = %d order by sortid,id';
                $text_temp_duan_info = $sqlquery->query($sql,$chapter_id,$text_duan_list[$i]["sectionid"]);
                array_push($text_duan_info, $text_temp_duan_info);
            }
            $this->assign('text_duan_info', $text_duan_info);
            //dump($text_duan_info);
        }
        $this->assign('chapter_total', count($chapter_list));
        $this->assign('chapter_page', $chapter_page);
        $this->assign('curunitid', $unitid);
        $this->assign('chapter_title', $chapter_title);
        $this->assign('issection', $issection);
        $this->assign('text_list', $text_list);
        $this->assign('text_total', count($text_list));
        $this->save_study_text_info($unitid, $chapter_id);
        $this->display('get_text_info');
    }
/**
 * 保存同步课文的学习记录
 * @param type $unitid
 * @param type $chapterid
 */
    public function save_study_text_info($unitid, $chapterid) {
        $Model = M('study');
        if (cookie('ut')) {
            $username = cookie('username');
            $areacode = cookie('areacode');
            $curtime = date("Y-m-d");
            $crutimearray = explode('-', $curtime);
            $data['ks_code'] = $unitid;
            $data['engs_studyid'] = $chapterid;
            $data['username'] = $username;
            $data['usertype'] = cookie('usertype');
            $data['areacode'] = $areacode;
            $data['addtimey'] = $crutimearray[0];
            $data['addtimem'] = $crutimearray[1];
            $data['addtimed'] = $crutimearray[2];
            $data['addtime'] = $curtime;
            $data['studytype'] = 2;
            $text = $Model->where('engs_studyid=%d and username="%s" and areacode="%s" and studytype =2',$chapterid,$username,$areacode)->field('addtime')->limit(1)->select();
            if (count($text) > 0) {
                if ($text[0]['addtime'] != $curtime) {
                    $Model->data($data)->add();
                }
            } else {
                $Model->data($data)->add();
            }
        }
    }

}
