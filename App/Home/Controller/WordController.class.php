<?php

namespace Home\Controller;

use Think\Controller;

class WordController extends Controller {
	public function index(){ //首页
        //获取用户生词本数量
        if (cookie('ut')) {
            $sqlquery = M();
            $userid = cookie('username');
            $areacode = cookie('areacode');
            $sql = 'select id from engs_word_book  where userid = "%s" and areacode="%s" and isuse=1';
            $word_book = $sqlquery->query($sql, $userid,$areacode);
            $array['wordbook_total'] = count($word_book); //获取用户生词本单词数量
        } else {
            $array['wordbook_total'] = 0;
        }
        $this->assign($array);
		$this -> display('index');
	}
    public function wordbook(){ //生词本页
        //获取用户生词本数量
        if (cookie('ut')) {
            $sqlquery = M();
            $userid = cookie('username');
            $areacode = cookie('areacode');
            $sql = 'select id from engs_word_book  where userid = "%s" and areacode="%s" and isuse=1';
            $word_book = $sqlquery->query($sql, $userid,$areacode);
            $array['wordbook_total'] = count($word_book); //获取用户生词本单词数量
        } else {
            $array['wordbook_total'] = 0;
        }
        $this->assign($array);
        $this -> display('wordbook');
    }

    //判断版本下是否有资源存在
    public function word_isexists(){
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        $sqlquery = M();
        $sql = 'select a.ks_code,a.ks_name_short,a.is_unit from engs_rms_unit as a,engs_word as b where a.flag = 1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and a.ks_code=b.ks_code and b.isdel = 1 group by b.ks_code order by a.display_order asc';
        $unitinfo = $sqlquery->query($sql,$gradeid,$termid,$versionid);
        $this->ajaxReturn($unitinfo);
    }
    /**
     * 获取版本下同步单词的单元信息
     */
    public function unitinfo_word() {   //获取同步单词单元信息
        //$postype = I('postype/d', 0);
        $gradeid = I('r_grade/s', 0);
        $termid = I('r_volume/s', 0);
        $versionid = I('r_version/s', 0);
        $sqlquery = M();
      //  $sql = 'select a.ks_code,a.ks_name_short,a.is_unit from engs_rms_unit as a,engs_word as b where a.flag = 1 and a.r_grade = "'.$gradeid.'" and a.r_volume = "'.$termid.'" and a.r_version = "'.$versionid.'" and a.ks_code=b.ks_code and b.isdel = 1 GROUP BY b.ks_code order by a.display_order asc';
        //$sql = 'select a.ks_code,a.ks_name_short,a.is_unit from engs_rms_unit as a where a.flag = 1 and a.r_grade = "'.$gradeid.'" and a.r_volume = "'.$termid.'" and a.r_version = "'.$versionid.'"  group by a.ks_code order by a.display_order asc';
        $sql = 'select a.ks_code,a.ks_name_short,is_unit,sum(case when b.ks_code is not null then 1 else 0 end) as wordcount from engs_rms_unit as a left join engs_word b on a.ks_code = b.ks_code where a.flag=1 and a.r_grade = "%s" and a.r_volume = "%s" and a.r_version = "%s" and a.r_subject="0003"  group by a.ks_code having is_unit+wordcount>0 order by a.display_order';
        $unitinfo = $sqlquery->query($sql,$gradeid,$termid,$versionid);
        $this->ajaxReturn($unitinfo);
    }

    /**
     * 获取单元下单词信息
     * @param type $unitid  //单元ID
     * @return json
     */
    public function get_word_list() {
        $unitid = I('unitid/s', 0);
        $curpostype = I('curpostype/d', 0);
        $wordpage = I('wordpage/d', 0);
        $gradeid = I('gradeid/s', 0);
        $curunitpos = I('curunitpos/d', 0);
        if(cookie('ut')){
            $userid = cookie('username');
            $areacode = cookie('areacode');
        }
        else {
            $userid = '0';
            $areacode = '0';
        }
       // C('tempunitid', $unitid);
        $sqlquery = M();
        $sql = 'select ew.id as wordid,ew.word,ew.isstress,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bw.tags,bwe.morphology,bwe.explains,bwe.pic, ';
        $sql.=' (select count(*) from engs_word_book ewb where ewb.wordid=ew.id and userid= "%s" and areacode="%s" and isuse=1) as cllocted_flag ';
        $sql .= ' from engs_word ew left join engs_base_word bw on ew.base_wordid=bw.id left join engs_base_word_explains bwe on ew.base_explainsid=bwe.id ';
        $sql .= ' where ew.isdel=1 and ew.ks_code="%s"';
        $sql .= ' ORDER BY ew.sortid,ew.id';
        //echo $sql;
        $wordlist = $sqlquery->query($sql,$userid,$areacode,$unitid);
        if(count($wordlist) == 0){
            $this->assign('isexists', 'no');
        }
        else{
             $this->assign('isexists', 'yes');
        }
       // echo $unitid;
        $this->assign('curunitid', $unitid);
        $this->assign('curpostype', $curpostype);
        $this->assign('wordpage', $wordpage);
        $this->assign('curunitpos', $curunitpos);
        $this->assign('wordlist', $wordlist);
        $this->assign('word_total', count($wordlist));
        if ($curpostype == '1') {  //如果是单词学习模块,获取当前单词的例句信息
            $curword = $wordlist[$wordpage]['word'];
            $expinfo = $this->get_word_exp($gradeid, $unitid, $curword);
            $this->assign('expinfonum', count($expinfo));
            $this->assign('expinfo', $expinfo);
        }
        //var_dump($wordlist);
        $this->display('get_word_list');
    }

    /**
     * 获取单词对应的例句,先在本单元的课文里查找,找不到后,从比当前年级小的年级里查找
     * @param type unitid 单元ID
     * @param type gradeid 年级ID
     * @param type word 当前需要索引的单词
     */
    public function get_word_exp($gradeid, $unitid, $word) {
        $sqlquery = M();
        $sql = 'select a.encontent,a.cncontent,a.mp3 from engs_text a,engs_text_chapter b,engs_rms_unit c where a.isdel = 1 and a.stateid=1 and a.chapterid = b.id and b.ks_code = c.ks_code and c.ks_code = "%s" and a.isexample=1 and match(a.encontent) against ("%s" in boolean mode) order by a.id';
        $expinfo = $sqlquery->query($sql, $unitid, $word);
        // if (count($expinfo) == 0) {
        //     $sql = 'select a.encontent,a.cncontent,a.mp3 from engs_text a,engs_text_chapter b,engs_rms_unit c where a.isdel = 1 and a.stateid=1 and a.chapterid = b.id and b.ks_code = c.ks_code and c.r_grade <= "%s" and match(a.encontent) against ("%s " in boolean mode) order by a.id';
        //     $expinfo = $sqlquery->query($sql, $gradeid, $word);
        // }
        return $expinfo;
    }

    /**
     * 单词学习找一找方法
     * @param type $wordtags 单词属性
     * @param type $findstr  查找字符
     * @param type $returnype 返回方式 0 return;1解析
     */
    public function word_search_list() {
        $wordtags = I('wordtags/s', 0);
        $curwordid = I('curwordid/d', 0);
        $findstr = I('findstr/s', 0);
        $trueword = I('trueword/s', 0);
        $Model = R("Public/mymodel");
        $sql = '(select t1.id,t1.word from engs_base_word as t1 join (select round(rand() * ((select max(id)from engs_base_word where word like "'.$findstr.'%" and word !="'.$trueword.'") - (select min(id) from engs_base_word where word like "'.$findstr.'%" and word !="'.$trueword.'")) + (select min(id) from engs_base_word where word like "'.$findstr.'%" and word !="'.$trueword.'")) as id) as t2 where t1.id >= t2.id and word like "'.$findstr.'%" and word !="'.$trueword.'" order by t1.id limit 2)union all (select id,word from engs_word where id = '.$curwordid.')';
        $word_search_list = $Model->query($sql);
        shuffle($word_search_list);
        $this->ajaxReturn($word_search_list);
        //$this->assign('word_search_list', $word_search_list);
        //$this->display();
    }
    /**
     * 获取用户生词本里的单元信息
     */
    public function unitinfo_wordbook() {
        if (cookie('ut')) {
            $postype = I('postype/d', 0);
            $userid = cookie('username');
            $areacode = cookie('areacode');
            $sqlquery =M();
            $sql = 'select a.ks_code,b.ks_name_short from engs_word_book as a,engs_rms_unit as b where a.ks_code = b.ks_code and a.isuse = 1 and userid ="%s" and areacode="%s" group by a.ks_code order by a.id desc';
            //echo $sql;
            $unitinfo = $sqlquery->query($sql,$userid,$areacode);
            //$this->assign('postype', $postype);
            //$this->assign('unitinfo', $unitinfo);
        }
        $this->ajaxReturn($unitinfo);
    }

    /**
     * 用户在该单元下的生词本信息
     * @param type $unitid
     */
    public function get_book_list() {
        $unitid = I('unitid/s',0);
        $postype = I('postype/d', 0);
        //echo $postype;
        $curunitpos =I('curunitpos/d', 0);
        $wordpage =I('wordpage/d', 0);
        if (cookie('ut')) {
            $userid = cookie('username');
            $areacode = cookie('areacode');
        } else {
            $userid = '0';
            $areacode = '0';
        }
        $sqlquery = M();
        $sql = 'select ew.id as wordid,ew.word,ew.ks_code,ew.isstress,bw.isword,bw.ukmark,bw.usmark,bw.ukmp3,bw.usmp3,bwe.morphology,bwe.explains,bwe.pic ';
        $sql .= ' from engs_word ew, engs_base_word bw,engs_base_word_explains bwe ';
       // $sql .= ' where ew.base_wordid=bw.id and ew.base_explainsid=bwe.id and isdel=1 and ew.ks_code="%s" ';
        $sql .= ' where ew.base_wordid=bw.id and ew.base_explainsid=bwe.id and isdel=1 ';
        $sql .= 'and ew.id in (select wordid from engs_word_book where userid = "%s" and areacode="%s" and isuse = 1) order by ew.sortid,ew.id ';
        //echo $sql;
        $wordlist = $sqlquery->query($sql, $userid,$areacode);
        //dump($wordlist);
        $this->assign('wordlist', $wordlist);
        $this->assign('postype', $postype);
        $this->assign('curunitid', $unitid);
        $this->assign('curunitpos', $curunitpos);
        $this->assign('wordpage', $wordpage);
        $this->assign('word_total', count($wordlist));
        $this->display('get_book_list');
    }

    /**
     * 将单词添加到生词本
     */
    public function book_collect() {
        $Model = M('word_book');
        $wordid = I("wordid/d", 0);
        $unitid = I("unitid/s", 0);
        $userid = cookie('username');
        $areacode = cookie('areacode');
        $time = time();
        $curltime = date("y-m-d H:i:s", $time);
        $rs = $Model->where('userid="%s" and areacode="%s" and wordid=%d',$userid,$areacode,$wordid)->find();
        if (count($rs) == 0) {
            $data['ks_code'] = $unitid;
            $data['wordid'] = $wordid;
            $data['userid'] = $userid;
            $data['areacode'] = $areacode;
            $data['isuse'] = 1;
            $data['studytime'] = $curltime;
            $Model->data($data)->add();
        }
        else{
            if($rs['isuse'] == 0){
                $data['isuse'] = 1;
                $data['studytime'] = $curltime;
            }
            else{
                $data['studytime'] = $curltime;
            }
             $Model->where('id=%d',$rs['id'])->data($data)->save();
        }
        $book_total = $Model->where('userid="%s" and areacode="%s" and isuse=1',$userid,$areacode)->count('wordid');
        $this->ajaxReturn($book_total);
    }

    /**
     * 从生词本中将生词移出
     */
    public function book_del() {
        $wordid = I('wordid/d', 0);
        if (cookie('ut')) {
            $userid = cookie('username');
            $areacode = cookie('areacode');
            $Model = M('word_book');
            $Model->where('userid="%s" and areacode="%s" and wordid=%d',$userid,$areacode,$wordid)->setField('isuse',0);
            $book_total = $Model->where('userid="%s" and areacode="%s" and isuse=1',$userid,$areacode)->count('wordid');
            $this->ajaxReturn($book_total);
        }
    }

    /**
     * 保存单词的学习记录
     */
    public function save_study_word_info() {
        $Model = M('study');
        if (cookie('ut')) {
            $wordflag = I('wordflag/d', 0);
            if ($wordflag == 0 || $wordflag == 1) { //单词跟读和单词学习统一为一类
                $wordflag = 0;
            } else if ($wordflag == 2) { //单词记忆为一类
                $wordflag = 1;
            } else {
                $wordflag = 2;    //单词听写为一类
            }
            $wordid = I('wordid/d', 0);
            $username = cookie('username');
            $areacode = cookie('areacode');
            $curtime = date("Y-m-d");
            $crutimearray = explode('-', $curtime);
            $data['wordflag'] = $wordflag;
            $data['ks_code'] = I('unitid/s', 0);
            $data['engs_studyid'] = $wordid;
            $data['username'] = $username;
            $data['usertype'] = cookie('usertype');
            $data['areacode'] = $areacode;
            $data['addtimey'] = $crutimearray[0];
            $data['addtimem'] = $crutimearray[1];
            $data['addtimed'] = $crutimearray[2];
            $data['addtime'] = $curtime;
            $data['studytype'] = 1;
            $word = $Model->where('engs_studyid=%d and username="%s" and areacode="%s" and wordflag = %d and studytype =1',$wordid,$username,$areacode,$wordflag)->field('addtime')->limit(1)->select();
            if (count($word) > 0) {
                if ($word[0]['addtime'] != $curtime) {
                    $Model->data($data)->add();
                }
            } else {
                $Model->data($data)->add();
            }
        }
    }

}
