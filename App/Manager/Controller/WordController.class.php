<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class WordController extends CheckController {

    /**
     *  checkdata  
     * 单词存在性验证
     * @param  void
     * @return  json 其中 word表示验证的单词 isyes表示是否存在(1 表示存在,0表示不存在)
     */
    public function checkData() {
        $arrrows = explode("\n", I("wordlist")); //将接收到的单词字符串以换行符分隔
        $data = array();        //定义结果集数组
        foreach ($arrrows as $row => $v) {   //循环单词列表
            $word = trim($v);
            if (trim($word) == "")  //如果该行单词为空
                continue;           //结束本次循环，进行下一次循环
            $arr = array();
            $arr["word"] = $word;
            $arr["isyes"] = 0;
            $baseword = M('base_word');
            $rs = $baseword->where("word='%s'", $word)->getField("id"); //查询该单词在数据库中是否存在
            if (!empty($rs)) {
                $arr["isyes"] = 1;
            }
            $data[] = $arr;
        }
        $this->ajaxReturn($data);
    }

    /**
     *  addmulti  
     *  单词批量添加
     * @param  void
     * @return  json 表示单词添加是否成功的信息
     */
    public function addMulti() {
        $arrrows = explode("\n", I("wordlist"));
        $unitid = I("unitid/s", 0);
        $word = M("word"); // 实例化Word对象
        $baseword = M('base_word'); // 实例化base_Word对象
        $rms_unit = M('rms_unit');
        
        $issuc = 1;
        $errmsg = "";
        $i = 0;
        $ic = 0;
        foreach ($arrrows as $row => $v) {
            $i++;
            $v = str_replace("，", ',', $v); //将中文的逗号替换成英文的逗号
            $arrcol = explode(",", $v); //将单词信息拆分
            if (empty($arrcol[0])) {
                $issuc = 0;
                $errmsg.= '第' . $i . '行单词为空<br>';
                continue;
            }
            $arrcol[1] = ($arrcol[1] == "是") ? 1 : 0;
            $wordname = trim(str_replace("  ", " ", $arrcol[0])); //如果是词组将中文的空格替换成英文的空格 
            $isstress = $arrcol[1];
            $sortid = $i;
            $rs = $word->where("isdel = 1 and word='%s' and ks_code='%s'", $wordname, $unitid)->field('id')->find();
            if (!empty($rs)) {
                $iserr = 1;
                $errmsg.= '第' . $i . '行单词已经添加<br>';
                continue;
            }
   
            $rs = $baseword->where("word='%s'", $wordname)->field("id,(SELECT IFNULL(MIN(id),0 ) from engs_base_word_explains where engs_base_word_explains.base_wordid = engs_base_word.id) as explainsid")->find();

            $wordid = empty($rs["id"]) ? 0 : $rs["id"];
            $explainsid = empty($rs["explainsid"]) ? 0 : $rs["explainsid"];

            unset($data);
            $data['ks_code'] = $unitid;
            $data['word'] = $wordname;
            $data['base_wordid'] = $wordid;
            $data['base_explainsid'] = $explainsid;
            $data['sortid'] = $sortid;
            $data['isstress'] = $isstress;
            $data['isdel'] = 1;
            $id = $word->add($data);

            unset($data);
            $data['id'] = $id;
            $data['sortid'] = $id;
            $word->save($data);
            $ic++;
            $update_time = time();
            $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
            $rms_unit -> where('ks_code="'.$unitid.'"')->save();
        }
       
        unset($data);
        $data["issuc"] = 1;
        $data["msg"] = $errmsg;
        $data["sucnum"] = $ic;
        $this->ajaxReturn($data);
    }

    //获取版本
    public function download(){
        $sql="select detail_name,detail_code from engs_rms_dictionary where  engs_rms_dictionary.dictionary_code='edition' and detail_code in (select r_version from engs_rms_unit where engs_rms_unit.r_subject='0003')";
        $rs=M()->query($sql);
        $this->assign("versions",$rs);
        $this->display();
    }

    /**
     *  importword  
     *  用excel将单词进行批量导入
     *  @param  void
     *  @return  json 返回单词是否导入的基本信息
     */
    public function importWord() {
        Vendor("PHPExcel");
        $filename = I("filename");
        $arr_rs = $this->importWordExcel($filename); //获取导入的结果集
        $iserr = 0;
        $errmsg = "";
        $i = 0;
        foreach ($arr_rs as $row => $v) {
            if ($v["iserr"]) {
                $iserr = 1;
                $errmsg.= $v["errmsg"] . '<br>';
            } else {
                $i++;
            }
        }
        $data["iserr"] = $iserr;
        $data["errmsg"] = $errmsg;
        $data["sucnum"] = $i;
        $this->ajaxReturn($data);
    }

    /**
     *  importWordExcel  
     *  读取Excel文件并将文件中的单词插入数据库
     * @param   filename表示文件存放路径
     * @return  array 
     */
     protected function importWordExcel($filename) {
        $arr_excel = readExcel($filename);
        $rms_unit = M('rms_unit');
        $rms_dictionary = M("rms_dictionary");
        $rs = $rms_dictionary->where('dictionary_code like "edition%"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_version[$v["detail_code"]] = $v["detail_name"];
        }
        $rs = $rms_dictionary->where('dictionary_code like "grade%"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_grade[$v["detail_code"]] = $v["detail_name"];
        }
        $rs = $rms_dictionary->where('dictionary_code = "volume"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_term[$v["detail_code"]] = $v["detail_name"];
        }
        //A B C D E G 
        $arrcolumn = array('A', 'B', 'C', 'D', 'E', 'F');
        $i = 0;
        $iserr = false;
        foreach ($arr_excel as $row => $v) {
            if (empty($v["Column"]['A'])) {
                unset($arr_excel[$row]);
                continue;
            }
            /* 必填列判断 */
            foreach ($arrcolumn as $cv) {
                $v["iserr"] = false;
                if (empty($v["Column"][$cv])) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行' . $cv . '列为空';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    break;
                }
            }
            if ($v["iserr"]) {
                continue;
            }
            $i++;
            $arr_excel[$row]["A"] = is_numeric($v["Column"]["A"]) ? $v["Column"]["A"] : $i;
            //开始比对数据
            $gradeid = array_search(trim($v["Column"]["D"]), $arr_grade);
            if (empty($gradeid)) {
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行D列，年级错误，没有该年级';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $v["Column"]['gradeid'] = $gradeid;
            if(substr($gradeid,0,1)=='k'){
                //进行版本以及单元的添加
                $versionid = array_search(trim($v["Column"]["C"]), $arr_version);
                if (empty($versionid)) {
                    //添加版本
                    $versionid="k".rand(1000,9999);
                    $rms_dictionary=M("rms_dictionary");
                    $rms_dictionary->dictionary_code='edition1';
                    $rms_dictionary->detail_code=$versionid;
                    $rms_dictionary->detail_name_short=trim($v["Column"]["C"]);
                    $rms_dictionary->detail_name=trim($v["Column"]["C"]);
                    $rms_dictionary->detail_order=1;
                    $rms_dictionary->sortid=1;
                    $rms_dictionary->update_time=time();
                    
                    $rms_dictionary->id='k'.md5(time().rand(10000,99999));
                    $rms_dictionary->add();
                    //将这个添加到数组中
                    $arr_version[$versionid] = trim($v["Column"]["C"]);  
                }
                $v["Column"]['versionid'] = $versionid;
            }else{
                $versionid = array_search(trim($v["Column"]["C"]), $arr_version);
                if (empty($versionid)) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行C列，版本错误，没有该版本';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    continue;
                }
                $v["Column"]['versionid'] = $versionid;
            }
            $termid = array_search(trim($v["Column"]["B"]), $arr_term);
            if (empty($termid)) {
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行B列，学期错误,没有该学期';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $v["Column"]['termid'] = $termid;
            
            
            
            unset($arr_cur_unit);
            $arr_cur_unit["r_grade"] = $v["Column"]["gradeid"];
            $arr_cur_unit["r_version"] = $v["Column"]["versionid"];
            $arr_cur_unit["r_volume"] = $v["Column"]["termid"];
            $arr_cur_unit["ks_name"] = str_replace("  ", " ",trim($v["Column"]["E"]));//章节全称
            //$arr_cur_unit["ks_name"] = trim($v["Column"]["F"]); //章节全称
            //var_dump($arr_cur_unit);
            
            if(substr($gradeid,0,1)=='k'){
                $ks_code = $this->getOtherUnitId($arr_cur_unit); //章节全称
                $graders=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'grade1'",$gradeid)->find();
                $termrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'volume'",$termid)->find();
                $versionrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'edition1'",$versionid)->find();
                if (empty($ks_code) || $ks_code=="0") {
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
                    $rms_unit->ks_name=$arr_cur_unit["ks_name"];
                    $rms_unit->ks_name_short=$arr_cur_unit["ks_name"];
                    $rms_unit->display_order=1;
                    $rms_unit->c1="班班通.".$graders["detail_name"].".".$termrs["detail_name"].".英语.".$versionrs["detail_name"].".".$arr_cur_unit["ks_name"];
                    $rms_unit->is_unit=0;
                    $rms_unit->r_grade=$gradeid;
                    $rms_unit->r_volume=$termid;
                    $rms_unit->r_subject="0003";
                    $rms_unit->r_version=$versionid;
                    $rms_unit->add();
                    $ks_code=$id;
                }
            }else{
                $ks_code = getUnitID($arr_cur_unit); //章节全称
                if (empty($ks_code) || $ks_code==0) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行E列，章节错误，所填年级学期版本下没有该单元或该单元是非章节目录';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    continue;
                }
            }
            
            $v["Column"]["ks_code"] = $ks_code;
            $v["Column"]["G"] = trim($v["Column"]["G"]) == "是" ? 1 : 0;
            $wordname = trim(str_replace("  ", " ", trim($v["Column"]["F"])));
            $isstress = $v["Column"]["G"];
            $word = M("word");
            $sort = $word->where("ks_code='%s' and isdel=1", $ks_code)->field("max(sortid) sortid")->find();
            $sortid = $sort["sortid"] + 1;
            //$sortid = $arr_excel[$row]["A"];
            $rs = $word->where("word ='%s' and ks_code='%s' and isdel=1", clearSQL($wordname), $ks_code)->field('id')->select();
            if (!empty($rs)) {
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行F列，单词已经添加';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $baseword = M('base_word');
            $rs = $baseword->where("word ='%s'", clearSQL($wordname))->field("id ,(SELECT IFNULL(MIN(id),0 ) from engs_base_word_explains where engs_base_word_explains.base_wordid = engs_base_word.id) as explainsid")->find();
            if($wordid=='0'){
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行，词库中不能匹配这个单词';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $wordid = empty($rs["id"]) ? 0 : $rs["id"];
            $explainsid = empty($rs["explainsid"]) ? 0 : $rs["explainsid"];
            if($explainsid=='0'){
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行，词库中不能匹配这个单词的释义';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $word->ks_code = $ks_code;
            $word->word = $wordname;
            $word->base_wordid = $wordid;
            $word->base_explainsid = $explainsid;
            $word->sortid = $sortid;
            $word->isstress = $isstress;
            $word->isdel = 1;
            $id = $word->add();
            $v["unitwordid"] = $id;
            $arr_excel[$row] = $v;
            $update_time = time();
            $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
            $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        }
        return $arr_excel;
    }

    /**
     * getlist  
     * 取出在此单元下的所有的单词
     * 
     * @access   public  
     * @param    void
     * @return 	json 返回每次的属性 
     */
    public function getList() {
        $unitid = I("get.unitid/s", 0);
        $searchtype = I("get.searchtype/d", 0);
        $word = M('word'); // 实例化Word对象
        if($searchtype == 0){
            $sql = "select a.chaptername,a.level_group,a.id,a.word,a.base_wordid,a.base_explainsid,a.isstress,a.explains_content,a.explains_mp3,a.explains_voiceid,b.isword,b.usmp3 from engs_word a,engs_base_word b where a.base_wordid = b.id and a.isdel=1 and a.ks_code='".$unitid."' and b.isword=0 order by a.sortid,a.id";
            //$sql = "select '---' as 'chaptername',id,word,isword,usmp3 from engs_base_word  where isword=0 order by id";
            $rs = M()->query($sql);
           
        }
        else{
            $rs = $word->where("isdel=1 and ks_code='%s'", $unitid)->field("chaptername,level_group,id,word,base_wordid,base_explainsid,isstress,explains_content,explains_mp3,explains_voiceid,(SELECT ISWORD FROM engs_base_word WHERE engs_base_word.id =  engs_word.base_wordid) AS isword,sortid")->order("sortid,id")->select();
        }
        
        foreach ($rs as $key => $value) {
            $word_explains = M('base_word_explains'); // 实例化Word对象
            $rs2 = $word_explains->where("base_wordid='%s'", $value["base_wordid"])->order('id')->field("id,morphology,explains")->select();
            $rs[$key]["explains"] = $rs2;
        }
        $this->ajaxReturn($rs);
    }

    private function getOtherUnitId($arr){
        $rs=M("rms_unit")->where("r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='0003' and ks_name='%s'",$arr["r_grade"],$arr["r_volume"],$arr["r_version"],$arr["ks_name"])->find();
        $ks_code=0;
        if(empty($rs)){
            $ks_code=0;
        }else{
            $ks_code=$rs["ks_code"];
        }
        return $ks_code;
    }

    /**
     * del  
     * 删除word表中的单词(单词删除不是物理删除)
     * 
     * @param void
     * @return json 表示单词是否更新成功
     */
    public function updel() {
        $wordid = I("wordid/d", 0);
        $ks_code = I("ks_code/d", 0);
        $word = M("word");
        $rms_unit = M('rms_unit');
        $model = M();
        $word->isdel = '0';
        $result = $word->where('id=%d', $wordid)->save();
        //$res = $word->where("id=%d", $wordid)->field('sortid')->find();
        //$sql = "update engs_word set sortid=sortid-1 where sortid>%d and isdel=1";
        //$model->execute($sql, $res['sortid']);
        if (false !== $result) {
            $update_time = time();
            $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
            $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "单词禁用成功！";
            $this->ajaxReturn($arr_return);
        } else {
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "单词禁用失败！";
            $this->ajaxReturn($arr_return);
        }
    }

    /**
     * add  
     * 向此单元下添加新的单词
     * 
     * @param void
     * @return json 返回单词是否添加成功 isadd表示是否添加 msg表示错误信息
     */
    public function add() {
        $unitid = I("unitid/s");
        $worder = I("word/s");
        $flag = I("flag/d");
        $word = M('word');
        $rms_unit = M('rms_unit');
        $str = ($flag == 1) ? "单词" : "短语";
        if (trim($worder) == '') {
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "添加失败，" . $str . "为空！";
            $this->ajaxReturn($arr_return);
        }
        $worder = trim(str_replace("  ", " ", $worder));
        $baseword = M('base_word');
        $basers = $baseword->where("word ='%s' and isword=%d", $worder, $flag)->field('id ,(SELECT IFNULL(MIN(id),0 ) from engs_base_word_explains where engs_base_word_explains.base_wordid = engs_base_word.id) as explainsid')->find();
        $wordid = empty($basers["id"]) ? 0 : $basers["id"];
        $explainsid = empty($basers["explainsid"]) ? 0 : $basers["explainsid"];
        $rs = $word->where("isdel = 1 and word ='%s' and ks_code='%s'", $wordid, $unitid)->field('id')->find();
        if (!empty($rs)) {
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "添加失败，" . $str . "已经添加！";
            $this->ajaxReturn($arr_return);
        }
        $sort = $word->where("ks_code='%s' and isdel=1", $unitid)->field("max(sortid) sortid")->find();
        $sortid = $sort["sortid"] + 1;
        $data['ks_code'] = $unitid;
        $data['word'] = $worder;
        $data['base_wordid'] = $wordid;
        $data['base_explainsid'] = $explainsid;
        $data['sortid'] = $sortid;
        $data['isstress'] = 0;
        $data['isdel'] = 1;
        $id = $word->data($data)->add();
        $arr_return["isadd"] = 1;
        $arr_return["msg"] = "添加成功";
        $update_time = time();
        $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
        $rms_unit -> where('ks_code="'.$unitid.'"')->save();
        $this->ajaxReturn($arr_return);
    }
/**
 * 批量修改单词顺序和释义
 */
    public function listup() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $ks_code = I('ks_code/s',0);
        $word = M("word");
        $rms_unit = M('rms_unit');
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $isstress = $obj->isstress;
            $explainsid = $obj->explainsid;
            if (empty($isstress)) {
                $isstress = 0;
            }
            if (empty($explainsid)) {
                $explainsid = 0;
            }
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $isstress = !is_numeric($isstress) ? 0 : $isstress;
            $explainsid = !is_numeric($explainsid) ? 0 : $explainsid;
            $word->sortid = $sortid;
            $word->isstress = $isstress;
            $word->base_explainsid = $explainsid;
            $result = $word->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $update_time = time();
        $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
	$this->updateWordSortid($ks_code);
        //$this -> ajaxReturn($arr_return);
    }
	/**
 * 批量修改单词闯关分组
 */
    public function levelgroup_up() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $ks_code = I('ks_code/s',0);
        $word = M("word");
        $rms_unit = M('rms_unit');
        foreach ($res as $obj) {          
            $id = $obj->id;
            $groupid = $obj->groupid;
            $id = !is_numeric($id) ? 0 : $id;
            $groupid = !is_numeric($groupid) ? 0 : $groupid;
            $word->level_group = $groupid;
            $result = $word->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $update_time = time();
        $rms_unit -> update_word_time = $update_time;           //单词修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        //$this -> ajaxReturn($arr_return);
    }
	public function transferword(){
        $unitid=I("unitid/s");
        $data=I("data");
        $arr=explode(",", $data);
        foreach($arr as $key=>$value){
            if(!empty($value)){
                M("word")->where("id=%d",$value)->setField("ks_code",$unitid);
            }
        }
    }


    public function addChapter(){
        $data = (I("data"));
        $words=explode(",",$data);
        $chapter=I("chapter");
        $chapter=rtrim($chapter);
        $chapter=ltrim($chapter);

        //if(!empty($chapter)){
            foreach($words as $key=>$value){
                M("word")->where("id=%d",$value)->setField("chaptername",$chapter);
            }
        //}
    }
    function hxpsmartstudy(){
        $filename = I('mp3/s',0);
        $content = I('content/s',0);
    //     $filepath = "/home/yylmp3/mp3_word/".$filename;
    //   $fields['fileData'] = '@'.$filepath;
    //   $fields['question'] = $content;
    //   // $fields['fileData'] = '@'.$filepath;
    //   // $fields['question'] = $text;
    // $url = "http://sls.smartstudy.com/jp/grade/speaking";
    // $ch = curl_init();
    // @curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false); 
    // @curl_setopt($ch, CURLOPT_URL, $url);
    // @curl_setopt($ch, CURLOPT_ENCODING, "");
    // @curl_setopt($ch, CURLOPT_POST,1);
    // @curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    // @curl_setopt($ch, CURLOPT_HEADER, false);
    // @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $return_data = curl_exec($ch);
    // curl_close($ch);
    // $return_data = json_decode($return_data);
    //return $return_data;http://en.youjiaotong.com/Homework/public/getTestScore?type=0&homeworkid=4799&studentid=41010110054500&classid=145309364247144332&readid=1968&content=shout+at%E2%80%A6&contentid=&filename=14987991439684.wav&ran=0.46434252918697894
    //-------------------------------------------------------------------
 //    $domain = C('record_net_url');
    $param = 'token=UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c&question='.$content.'&mp3Url=http://en2.czbanbantong.com:8080/yylmp3/mp3_word/'.$filename;
  $url = "http://open.smartstudy.com/api/grading/speaking";
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch,CURLOPT_POSTFIELDS,$param);// post传输数据
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $return_data = curl_exec($ch);
  curl_close($ch);
  $return_data = json_decode($return_data);
  // return $return_data;
    //var_dump($return_data);
    $this->ajaxReturn($return_data);
    }

	
    private function updateWordSortid($ks_code){
        $rs=M("word")->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->select();
        foreach($rs as $key=>$val){
            M("word")->sortid=$key+1;
            M("word")->where("id=%d",$val["id"])->save();
        }
    }
    
    public function save_explanis(){
        $type = I('type/d',0);
        $val = I("val");
        $id = I("id/d",0);
        $word = M('word');
        if($id != 0){
            if($type == 0){
                $word -> explains_voiceid = $val;
            }
            else{
                $word -> explains_content = $val;
            }
            $word -> state = 0;
            $word->where("id = ".$id)->save();
        }
    }


    public function export(){
        set_time_limit(0);
        ini_set('memory_limit',-1);
        $basepath="/home/engtool/";
        $data=I("arrFile");
        $data=urldecode($data);
        $data=json_decode($data,true);
        $where=array();
        foreach($data as $key=>$value){
            if(strpos($value,"_")){
                continue;
            }else{
                array_push($where,"'".$value."'");
            }
        }
        
        if(empty($where)){
            $this->ajaxReturn("错误");
        }else{
            $rms=M("rms_unit")->where("ks_code in (".implode(",",$where).")")->select();
            //创建一个文件加
            $dir=md5(\uniqid().time());
            if (!file_exists($dir)){
                mkdir ($dir,0777,true);
                foreach($rms as $key=>$value){
                    $ks_code=$value["ks_code"];
                    $c1=$value["c1"];
                    $list=explode(".",$c1,6);
                    $len=count($list);
                    $list[$len-1]="'".$list[$len-1]."'";
                    //创建文件夹
                    $path = $basepath."/".$dir."/".implode("/", $list)."/";
                    //createDir($path);
                    $command = "mkdir -p ".$path; //ls是linux下的查目录，文件的命令
                    exec($command,$array);
                    $word=D("WordView")->where("isdel=1 and ks_code='%s'",$ks_code)->select();
                    if(!empty($word)){
                        $enpath=$path."/en";
                        $command = "mkdir -p ".$enpath; //ls是linux下的查目录，文件的命令
                        exec($command,$array);
                        //createDir($enpath);
                        $cnpath=$path."/cn";
                        $command = "mkdir -p ".$cnpath; //ls是linux下的查目录，文件的命令
                        exec($command,$array);
                        foreach($word as $key=>$value){
                            $explains_mp3="/usr/local/httpd/apache2/htdocs/yylmp3/mp3_word_explain/".substr($value["explains_mp3"],0,2)."/".$value["explains_mp3"].".mp3";
                            $word_mp3="/usr/local/httpd/apache2/htdocs/yylmp3/mp3_word/".$value["usmp3"];
                            //copy($explains_mp3,$cnpath);
                            exec("cp ".$word_mp3." ".$enpath);
                            exec("cp ".$explains_mp3." ".$cnpath);
                            //downmp3("http://192.168.151.208/yylmp3/mp3_word/".$value["usmp3"],$value["usmp3"],iconv('utf-8', 'gbk', $enpath));
                            //$word_mp3="/usr/local/httpd/apache2/htdocs/yylmp3/mp3_word/".$value["usmp3"];
                            //copy($word_mp3,$enpath);
                            $word[$key]["enmp3"]=$value["usmp3"];
                            $word[$key]["explains"]=$value["explains_content"];
                            $word[$key]["cnmp3"]=$value["explains_mp3"].".mp3";
                        }
                        $ret=array();
                        foreach($word as $key=>$value){
                            $ret[$value["chaptername"]][]=$value;
                        }
                        file_put_contents("/home/engtool/".$dir."/data.json",json_encode($ret));
                        exec("mv "."/home/engtool/".$dir."/data.json ".$path);
                        //打包数据
                        //packImg($basepath,$dir);
                    }
                }
		$command = "zip -r -q -o /home/engtool/".$dir.".zip  /home/engtool/".$dir;
                        exec($command,$array);
                        header("Content-Type: application/zip");
                        header("Content-Transfer-Encoding: Binary");

                        header("Content-Length: " . filesize("/home/engtool/".$dir.".zip"));
                        header("Content-Disposition: attachment; filename=\"" . basename("/home/engtool/".$dir.".zip") . "\"");

                        readfile("/home/engtool/".$dir.".zip");
                        exit;
  
            } else {
                
            }  
        }

    }


     public function getNodesForBigData(){
        $id=$_GET["id"];
        $code=explode("_",$id);
        $opt=$code[0];
        if($opt == 'v'){
            $versionid=$code[1];
            $sql="select distinct r_grade as id,(select detail_name from engs_rms_dictionary where engs_rms_dictionary.detail_code = r_grade and engs_rms_dictionary.dictionary_code='grade') as name from engs_rms_unit where r_version='".$versionid."' group by r_grade order by r_grade";
            $rs=M()->query($sql);
            $content="[";
            $max=count($rs)-1;
            foreach($rs as $key=>$value){
                $nId = $versionid;
                $nName = "tree".$nId;
                $content = $content . "{ id:'g_".$value["id"]."_v_".$versionid."',  name:'".$value["name"]."',isParent:true}";
                if ($key<$max) {
                    $content = $content .  ",";
                }
            }
            $content=$content."]";
            echo $content;
        }else if($opt == 't'){
            $termid=$code[1];
            $gradeid=$code[3];
            $versionid=$code[5];
            $sql="select distinct ks_code as id,c1 as name from engs_rms_unit where r_version='".$versionid."' and r_grade ='".$gradeid."' and r_volume='".$termid."' order by display_order";
            $rs=M()->query($sql);
            $content="[";
            $max=count($rs)-1;
            foreach($rs as $key=>$value){
                $nId = $versionid;
                $nName = "tree".$nId;
                $content = $content . "{ id:'".$value["id"]."',  name:'".$value["name"]."'}";
                if ($key<$max) {
                    $content = $content .  ",";
                }
            }
            $content=$content."]";
            echo $content;
        }else if($opt == 'g'){
            $gradeid=$code[1];
            $versionid=$code[3];
            $content="[";
            $content = $content . "{ id:'t_0001_g_".$gradeid."_v_".$versionid."',  name:'上学期',isParent:true},";
            $content = $content . "{ id:'t_0002_g_".$gradeid."_v_".$versionid."',  name:'下学期',isParent:true}";
            $content=$content."]";
            echo $content;
        }
        //$this->ajaxReturn($rs);
    }


    public function changeNewStatus(){
        $id=I("id/d");
        $ischeck=I("ischeck/d");
        if($ischeck == 0){
            $ischeck = 0;
        }else{
            $ischeck = 1;
        }
        M("word")->where("id=%d",$id)->setField("isnew",$ischeck);
    }
}
