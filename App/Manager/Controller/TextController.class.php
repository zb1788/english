<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 课文控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class TextController extends CheckController {
    /**
     * 获取批量导入课文的结果集信息
     */
    public function importtext() {
        Vendor("PHPExcel");
        $filename = $_REQUEST["filename"];
        $arr_rs = $this->importTextExcel($filename);//取批量导入课文的结果集信息
        $iserr = 0;
        $errmsg = "";
        $i = 0;
        foreach ($arr_rs as $row => $v) {
            if ($v["iserr"]) {
                $iserr = 1;
                $errmsg.= $v["errmsg"] . '</br>';
            } else {
                $i++;
            }
        }
        $data["iserr"] = $iserr;
        $data["errmsg"] = $errmsg;
        $data["sucnum"] = $i;
        $this->ajaxReturn($data);//返回json数据
    }
    /**
     * 分析上传的课文excel模板，对比数据库中的数据进行存储并返回结果
     */
     public function importTextExcel($filename) {
        $arr_excel = readExcel($filename);//读excel文件
	$rms_unit = M('rms_unit');
        $rms_dictionary = M("rms_dictionary");//定义rms字典表
        $rs = $rms_dictionary->where('dictionary_code like "edition%"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_version[$v["detail_code"]] = $v["detail_name"];//获取版本信息
        }
        $rs = $rms_dictionary->where('dictionary_code like "grade%"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_grade[$v["detail_code"]] = $v["detail_name"];//获取学期信息
        }
        $rs = $rms_dictionary->where('dictionary_code = "volume"')->field('detail_code,detail_name')->select();
        foreach ($rs as $v) {
            $arr_term[$v["detail_code"]] = $v["detail_name"];//获取学期信息
        }
        $arrcolumn = array('A', 'B', 'C', 'D', 'E', 'F', 'I');//定义必填列数组；A-序号 B-学期 C-版本 D-年级 E-单元全称 F-课文小节标题 I-发音正文
        $i = 0; //自定义序号
        $iserr = false;  //定义全局出错标识
        foreach ($arr_excel as $row => $v) { //循环excel表的每一行
            if (empty($v["Column"]['A'])) {  //如果当前行的A列值为空
                unset($arr_excel[$row]); //销毁此行变量数据
                continue; //终止此行循环，进行下一行的循环
            }
            /* 必填列判断 */
            foreach ($arrcolumn as $cv) { //循环当前行的必填列
                $v["iserr"] = false; //定义是否出错
                if (empty($v["Column"][$cv])) { //如果当前行的某一必填列为空
                    $v["iserr"] = true;     //当前行出错标识为真
                    $v["errmsg"] = '第' . $row . '行' . $cv . '列为空';  //设置当前行的出错信息
                    $arr_excel[$row] = $v; //将有出错信息的行数据覆盖原来的行数据
                    $iserr = true;    //设置全局出错标识为true
                    break;          //退出当行行必填列的循环
                }
            }
            if ($v["iserr"]) {
                continue;       //如果当前行的必填列有错误，则退出当前行的循环，进行下一行的循环
            }
            $i++; //自定义序号加1
            $arr_excel[$row]["A"] = is_numeric($v["Column"]["A"]) ? $v["Column"]["A"] : $i;  //如果当前行的第一列序号不是数字，则设置自定义序号为当前行第一列的值
            //开始比对数据
            $gradeid = array_search(trim($v["Column"]["D"]), $arr_grade);       //对比数据，获取当前行所填年级的年级code
            if (empty($gradeid)) {      //如果没有获取到年级code
                $v["iserr"] = true;     //当前行出错标识为真
                $v["errmsg"] = '第' . $row . '行D列，年级错误，没有所填写的年级';       //设置当前行的出错信息
                $arr_excel[$row] = $v;      //将有出错信息的行数据覆盖原来的行数据
                $iserr = true;          //设置全局出错标识为true
                continue;               //退出当前行的循环，进行下一行的循环
            }
            $v["Column"]['gradeid'] = $gradeid;     //将获取的当前所填年级的年级code加入到当前行信息的数组中
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
                $versionid = array_search(trim($v["Column"]["C"]), $arr_version);   //对比数据，获取当前行所填版本的版本code
                if (empty($versionid)) {        //如果没有获取到版本code
                    $v["iserr"] = true;         //当前行出错标识为真
                    $v["errmsg"] = '第' . $row . '行C列，版本错误，没有所填写的版本';      //设置当前行的出错信息
                    $arr_excel[$row] = $v;      //将有出错信息的行数据覆盖原来的行数据
                    $iserr = true;          //设置全局出错标识为true
                    continue;               //退出当前行的循环，进行下一行的循环
                }
                $v["Column"]['versionid'] = $versionid; //将获取的当前所填版本的版本code加入到当前行信息的数组中
            }
            $termid = array_search(trim($v["Column"]["B"]), $arr_term);     //对比数据，获取当前行所填学期的学期code
            if (empty($termid)) {       //如果没有获取到学期code
                $v["iserr"] = true;     //当前行出错标识为真
                $v["errmsg"] = '第' . $row . '行B列，学期错误,没有所填写的学期';     //设置当前行的出错信息
                $arr_excel[$row] = $v;      //将有出错信息的行数据覆盖原来的行数据
                $iserr = true;      //设置全局出错标识为true
                continue;               //退出当前行的循环，进行下一行的循环
            }
            $v["Column"]['termid'] = $termid;   //将获取的当前所填学期的学期code加入到当前行信息的数组中
            
            unset($arr_cur_unit);               //清空当前单元变量数据
            $arr_cur_unit["r_grade"] = $v["Column"]["gradeid"];     //年级
            $arr_cur_unit["r_version"] = $v["Column"]["versionid"]; //版本
            $arr_cur_unit["r_volume"] = $v["Column"]["termid"];         //学期
            $arr_cur_unit["ks_name"] = trim($v["Column"]["E"]); //章节全称
            //var_dump($arr_cur_unit);
            if(substr($gradeid,0,1)=='k'){
                $ks_code = $this->getOtherUnitId($arr_cur_unit); //章节全称
                $graders=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'grade1'",$gradeid)->find();
                $termrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'volume'",$termid)->find();
                $versionrs=M('rms_dictionary')->where("detail_code='%s' and dictionary_code = 'edition1'",$versionid)->find();
                if (empty($ks_code) || $ks_code===0) {
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
                    $rms_unit->display_order=$sortid;
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
                // $ks_code = getUnitID($arr_cur_unit); //章节全称
                // if (empty($ks_code) || $ks_code==0) {
                //     $v["iserr"] = true;
                //     $v["errmsg"] = '第' . $row . '行E列，章节错误，所填年级学期版本下没有该单元或该单元是非章节目录';
                //     $arr_excel[$row] = $v;
                //     $iserr = true;
                //     continue;
                // }
                $ks_code = getUnitID($arr_cur_unit);
                if (empty($ks_code) || $ks_code==0) {      //如果没有获取到章节ID
                    // echo "code".$ks_code;
                    $v["iserr"] = true;     //当前行出错标识为真
                    $v["errmsg"] = '第' . $row . '行F列，章节错误，所填年级学期版本下没有该单元或该单元是非章节目录';    //设置当前行的出错信息
                    $arr_excel[$row] = $v;  //将有出错信息的行数据覆盖原来的行数据
                    $iserr = true;      //设置全局出错标识为true
                    continue;           //退出当前行的循环，进行下一行的循环
                }
                //$v["Column"]["ks_code"] = $ks_code; //将获取到的章节ID加入到当前行信息的数组中
            }
            $v["Column"]["ks_code"] = $ks_code;
            unset($arr_cur_chapter);//清空当前小节变量数据
            $arr_cur_chapter["ks_code"] = $v["Column"]["ks_code"];   //将当前行的单元code值设置给当前小节信息数组
            $arr_cur_chapter["chapter"] = str_replace("  ", " ", trim($v["Column"]["F"]));  //将当前行的小节标题设置给当前小节信息数组
           // var_dump($arr_cur_chapter);
            $chapterid = $this->getChapterID($arr_cur_chapter);   //判断该章节是否存在，不存在进行添加并返回章节的ID
            $v["Column"]["chapterid"] = $chapterid;         //将获取到的课文小节ID加入到当前行信息的数组中
            $v["Column"]["G"] = is_numeric($v["Column"]["G"]) ? $v["Column"]["G"] : 0;  //判断当前行所写的段落标记是否为数字，不是数字的默认为0
            $sectionid = $v["Column"]["G"];             //段落标记
            $enbefore = str_replace("  ", " ", trim($v["Column"]["H"]));    //  不发音内容
            $encontent = str_replace("  ", " ", trim($v["Column"]["I"]));   //  发音正文
            $cncontent = str_replace("  ", " ", trim($v["Column"]["J"]));   //  中文翻译
            global $arr_voice;
            $voicename = trim($v["Column"]["K"]);   //发音类型
            $voiceid = array_search($voicename, $arr_voice);
            $voiceid = empty($voiceid) ? 0 : $voiceid;  //默认发音类型为0-自定义上传
            $text = M("text");      //定义课文正文表
            $rs = $text->where("encontent ='%s' and chapterid=%d  and enbefore='%s' and isdel=1", clearSQL($encontent), $chapterid,clearSQL($enbefore))->field('id')->select();
            if (!empty($rs)) {      //如果该行的正文已经添加过
                $v["iserr"] = true;     //当前行出错标识为真
                $v["errmsg"] = '第' . $row . '行J列正文内容已经在所填小节下存在';    //设置当前行的出错信息
                $arr_excel[$row] = $v;  //将有出错信息的行数据覆盖原来的行数据
                $iserr = true;      //设置全局出错标识为true
                continue;           //退出当前行的循环，进行下一行的循环
            }
            $sort = $text->where("chapterid=%d", $chapterid)->field("max(sortid) sortid")->find();  //获取最大顺序值
            $mp3 = $v["Column"]["L"];       //自定义上传的mp3目录和文件名
            $data["chapterid"] = $chapterid;    //小节ID
            $data["sectionid"] = $sectionid;    //段落标记
            $data["enbefore"] = $enbefore;      //不发音内容
            $data["encontent"] = $encontent;    //发音正文
            $data["cncontent"] = $cncontent;    //中文翻译
            $data["sortid"] = ($sort["sortid"] + 1); //最大序号+1
            $data["ftp_mp3"] = $mp3;        //mp3目录和文件名
            $data["addtime"] = date('Y-m-d H:i:s', time());//添加时间
            $data["voiceid"] = 0;       //发音类型
            $data["isdel"] = 1;         //删除标记
            $id = $text->add($data);    //数据添加
            $v["textid"] = $id;     //把添加过的正文ID给当前行的数组信息
            $arr_excel[$row] = $v;  //更新当前行数组信息
            $this->updateChapterSection($chapterid,$ks_code); //根据导入的正文内容修改这个小节的类型 0对话 1 段落
        }
        return $arr_excel;
    }

	
    /**
     *  updateChapterSection  
     *  更新数据库
     *根据导入的正文内容修改这个小节的类型 0对话 1 段落
     */
    protected function updateChapterSection($id,$ks_code) {
        $model = M();
        $sql = "update engs_text_chapter set issection = IFNULL((SELECT 1 FROM engs_text WHERE engs_text.sectionid <> 0 AND isdel = 1 AND engs_text.chapterid = engs_text_chapter.id LIMIT 1),0) where isdel = 1 and id=" . $id;
        $model->execute($sql);
        $rms_unit = M('rms_unit');
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$unitid.'"')->save();
    }
 /**
     *  getChapterID  
     *  判断课文小节是否存在，不存在时进行添加，并返回小节ID
     * @param  void
     * @return  
     */
    protected function getChapterID($arr_cur_chapter) {
        global $arr_chapter;
        $text_chapter = M("text_chapter");
        if (!isset($arr_chapter)) {
            $rs = $text_chapter->where("isdel=1")->field("id,ks_code,chapter")->select();  //查询现有的课文小节
            foreach ($rs as $v) {
                $arr_chapter[$v["id"]]["ks_code"] = $v["ks_code"];  //将现有的课文小节信息存入数组
                $arr_chapter[$v["id"]]["chapter"] = $v["chapter"];
            }
            
        }
        //var_dump($arr_chapter);
        if (isset($arr_chapter)) {              //
            $chapterid = array_search($arr_cur_chapter, $arr_chapter);// 判断excel表中当前行所写的小节是否存在
        }
        if (empty($chapterid)) {        //如果小节不存在，进行添加
            $sort = $text_chapter->where("ks_code='%s'", $arr_cur_chapter["ks_code"])->field("max(sortid) sortid")->find();
            $text_chapter->ks_code = $arr_cur_chapter["ks_code"];
            $text_chapter->chapter = $arr_cur_chapter["chapter"];
            $text_chapter->issection = 0;
            $text_chapter->isdel = 1;
            $text_chapter->sortid = ($sort["sortid"] + 1);
            $chapterid = $text_chapter->add();//新增课文小节
            $arr_chapter[$chapterid]["ks_code"] = $arr_cur_chapter["ks_code"];  //将现有的课文小节信息存入数组
            $arr_chapter[$chapterid]["chapter"] = $arr_cur_chapter["chapter"];
        }
        return $chapterid;   //返回课文小节ID
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
     *  getChapterList  
     *  获取该单元下的课文小节列表
     * @param  void
     * @return  json 
     */
    public function getChapterList() {
        $unitid = I("unitid/s", 0);
        $text_chapter = M("text_chapter");
        $rs = $text_chapter->where("isdel = 1  and ks_code ='%s'", $unitid)->field("id,chapter,sortid,issection,isevaluate,IFNULL((SELECT 0 FROM engs_text WHERE engs_text.stateid = 0 AND  engs_text.chapterid = engs_text_chapter.id LIMIT 1),1) AS stateid")->order('sortid,id')->select();
        $this->ajaxReturn($rs);
    }

    /**
     *  chapterEdit  
     *  课文小节添加或者修改
     */
    public function chapterEdit() {
        $unitid = I('unitid/s', 0); //单元code
        $id = I('id/d', 0);     //小节ID,如果为0则为新增
        $chapter = I('chapter');//小节标题
        $text_chapter = M("text_chapter");
        $rms_unit = M('rms_unit');
        $text_chapter->chapter = trim($chapter);
        $text_chapter->ks_code = $unitid;
        if (empty($id) || $id == 0) {
            $count = $text_chapter->where("ks_code='%s'", $unitid)->count();
            $text_chapter->isdel = 1;
            $text_chapter->issection = 0;
            $text_chapter->sortid = $count + 1;
            $id = $text_chapter->add();
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "添加成功";
            
        } else {
            //修改 
            $text_chapter->where("id=%d", $id)->save();
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "修改成功";
        }
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$unitid.'"')->save();
        $this->ajaxReturn($arr_return);
    }

    /**
     *  chapterDel  
     *  课文章节删除
     * @param  void
     */
    public function chapterDel() {
        $chapterid = I("chapterid/d", 0);
        $unitid = I("unitid/s");
        $text = M("text");//正文表
        $rms_unit = M('rms_unit');
        $text_chapter = M("text_chapter");//章节表
        $text_chapter->where("id=%d", $chapterid)->setfield('isdel', '0'); //将要删除的章节的删除标志置为0
        $res = $text_chapter->where("id=%d", $chapterid)->field('sortid')->find();
        $sql = "update engs_text_chapter set sortid=sortid-1 where sortid>%d and isdel=1 and ks_code='%s'";//修改该单元下未删除章节的顺序
        $model->execute($sql, $res['sortid'], $unitid);
        $text->where("chapterid=%d", $chapterid)->setfield('isdel', '0'); //将要删除的章节下的正文的删除标志置为0
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$unitid.'"')->save();
    }
/**
     *  chapterEvaluate  
     *  课文章节设置测评操作
     * @param  void
     */
    public function chapterEvaluate() {
        $chapterid = I("chapterid/d", 0);
        $type = I("type/d",1);
        $text_chapter = M("text_chapter");//章节表
        $text_chapter->where("id=%d", $chapterid)->setfield('isevaluate', $type); 
    }
    /**
     *  chapterlistup  
     *  同一单元下的小节修改顺序
     */
    public function chapterlistup() {
        $data = stripslashes($_REQUEST["data"]);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $ks_code = I('ks_code/s',0);
        $text_chapter = M("text_chapter");
        $rms_unit = M('rms_unit');
        foreach ($res as $obj) {
            $id = $obj->id;
            $sortid = $obj->sortid;
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;
            $text_chapter->sortid = $sortid;
            $result = $text_chapter->where('id=%d', $id)->save();
        }
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $this->ajaxReturn($arr_return);
    }
 /**
     *  textList  
     *  课文章节下正文列表页
     * @param  void
     * @return  json 表示单词添加是否成功的信息
     */
    public function textList() {
        $gradeid = I("gradeid/s", 0);
        $versionid = I("versionid/s", 0);
        $termid = I("termid/s", 0);
        $unitid = I("unitid/s", 0);
        $chapterid = I("chapterid/d", 0);
        $this->assign("gradeid", $gradeid);
        $this->assign("versionid", $versionid);
        $this->assign("termid", $termid);
        $this->assign("unitid", $unitid);
        $this->assign("chapterid", $chapterid);
        $this->display();
    }
    /**
     *  edit  
     *  小节正文编辑页面
     * @param  void
     * @return
     */
    public function edit() {
        $id = I("id/d");
        $chapterid = I("chapterid/d");
        $text = M("text");
        $this->assign("chapterid", $chapterid);
        $this->assign("id", $id);
        if (($id) != 0) {
            $rs = $text->where("id=%d", $id)->field("id,chapterid,sortid,sectionid,enbefore,encontent,cncontent,mp3,voiceid")->find();
            if (!empty($rs)) {
                $this->assign("id", $rs['id']);
                $this->assign("sectionid", $rs['sectionid']);
                $this->assign("sortid", $rs['sortid']);
                $this->assign("enbefore", $rs['enbefore']);
                $this->assign("encontent", $rs['encontent']);
                $this->assign("cncontent", $rs['cncontent']);
                $this->assign("voiceid", $rs['voiceid']);
            }
        }
        $this->display();
    }

    /**
     *  textMp3Edit  
     *  课文MP3编辑
     * @param  void
     * @return  json 
     */
    public function textMp3Edit() {
        $id = I("id/d", 0);
        $mp3 = trim(I("mp3/s", ""));
        $rms_unit = M('rms_unit');
        $text = M("text");
        $text->stateid = 0;
        $text->ftp_mp3 = $mp3;
        $text->where('id=%d', $id)->save();
        $arr_return["isadd"] = 1;
        $arr_return["msg"] = "保存成功";
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        $this->ajaxReturn($arr_return);
    }

    /**
     *  textEdit  
     *  课文编辑保存功能
     * @param  void
     * @return  
     */
    public function textEdit() {
        $id = I("id/d", 0);
        $chapterid = I("chapterid/d", 0);
        $sectionid = I("sectionid/d", 0);
        $sortid = I("sortid/d", 0);
        $enbefore = I("enbefore/s");
        $encontent = I("encontent/s");
        $cncontent = I("cncontent/s");
        $ks_code = I('ks_code/s',0);
        $mp3 = I("mp3/s");
        $voiceid = I("voiceid");
        $arr_return["isadd"] = 0;
        $arr_return["msg"] = "修改失败";
        $text = M("text");
        $rms_unit = M('rms_unit');
        if ($id == 0) {
            //echo "fasdfas";       
            $sort = $text->where("chapterid=%d", chapterid)->field("max(sortid) sortid")->find();
            $data["chapterid"] = $chapterid;
            $data["sectionid"] = $sectionid;
            $data["enbefore"] = clearSQL($enbefore);
            $data["encontent"] = clearSQL($encontent);
            $data["cncontent"] = clearSQL($cncontent);
            $data["sortid"] = ($sort["sortid"] + 1);
            $data["addtime"] = date('Y-m-d H:i:s', time());
            $data["voiceid"] = $voiceid;
            $data["stateid"] = 0;
            $data["isdel"] = 1;
            $data["ftp_mp3"] = clearSQL($mp3);
            $text->add($data);
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "添加成功";
        } else {
            //修改
            $text->chapterid = $chapterid;
            $text->sectionid = $sectionid;
            $text->enbefore = clearSQL($enbefore);
            $text->encontent = clearSQL($encontent);
            $text->cncontent = clearSQL($cncontent);
            $text->sortid = $sortid;
            $text->where("id=%s", $id)->save();
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "修改成功";
        }
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        $this->updateChapterSection($chapterid,$ks_code);
        $this->ajaxReturn($arr_return);
    }

    /**
     *  textlistup  
     *  修改课文次序
     * @param  void
     * @return  
     */
    public function textlistup() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $ks_code = I('ks_code/s',0);
        $rms_unit = M('rms_unit');
        $text = M("text");
        foreach ($res as $obj) {
            $chapterid = $obj->chapterid;
            $id = $obj->id;
            $sortid = $obj->sortid;
            $sectionid = $obj->sectionid;
            $voiceid = $obj->voiceid;

            if (empty($sectionid)) {
                $sectionid = 0;
            }
            if (empty($voiceid)) {
                $voiceid = 0;
            }
            $id = !is_numeric($id) ? 0 : $id;
            $sortid = !is_numeric($sortid) ? 0 : $sortid;         
            $text->chapterid = $chapterid;
            $text->sortid = $sortid;
            $text->sectionid = $sectionid;
            $text->voiceid = $voiceid;

            $result = $text->where("id=%d", $id)->save();
        }
        updateChapterSection($chapterid,$ks_code);
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
        $update_time = time();
        $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
        $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
        $this->ajaxReturn($arr_return);
    }

    /**
     *  textDel  
     *  课文删除
     * @param  void
     * @return  
     */
    public function textDel() {
        $textid = I("textid/d", 0);
        $chapterid = I("chapterid/d");
        $ks_code = I("ks_code/s",0);
        $text = M("text");
        $rms_unit = M('rms_unit');
        $result = $text->where("id=%d", $textid)->setfield("isdel", '0');
        $res = $text->where("id=%d", $chapterid)->field('sortid')->find();
        $sql = "update engs_text set sortid=sortid-1 where sortid>%d and isdel=1 and chapterid=%d";
        $model->execute($sql, $res['sortid'], $chapterid);
        if (false == $result) {
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "禁用失败";
            $this->ajaxReturn($arr_return);
        } else {
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "禁用成功";
            $update_time = time();
            $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
            $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
            $this->ajaxReturn($arr_return);
        }
    }

   

    /**
     *  getTextList  
     *  获取课文列表
     * @param  void
     * @return  
     */
    public function getTextList() {
        $chapterid = I("chapterid/d", 0);
        $text = M("text");
        $rs = $text->where("isdel = 1  and chapterid =%d ", $chapterid)->order("sortid,id")->select();
        $this->ajaxReturn($rs);
    }

    /**
     *  isExample  
     *  勾选例句
     * @param  void
     * @return  
     */
    public function isExample() {
        $textid = I("textid/d", 0);
        $isexample = I("isexample");
        $ks_code = I('ks_code/s',0);
        $text = M("text");
        $rms_unit = M('rms_unit');
        $result = $text->where("id=%d", $textid)->setfield("isexample", $isexample);
        if (false == $result) {
            $arr_return["isadd"] = 0;
            $arr_return["msg"] = "修改失败";
            $this->ajaxReturn($arr_return);
        } else {
            $arr_return["isadd"] = 1;
            $arr_return["msg"] = "修改成功";
            $update_time = time();
            $rms_unit -> update_text_time = $update_time;           //课文修改时间更新
            $rms_unit -> where('ks_code="'.$ks_code.'"')->save();
            $this->ajaxReturn($arr_return);
        }
    }
	
	public function transferchapter(){
        $unitid=I("unitid/s");
        $data=I("data");
        $arr=explode(",", $data);
        foreach($arr as $key=>$value){
            if(!empty($value)){
                M("text_chapter")->where("id=%d",$value)->setField("ks_code",$unitid);
            }
        }
    }

   
}
