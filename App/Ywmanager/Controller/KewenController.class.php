<?php
namespace Ywmanager\Controller;
use Think\Controller;
class KewenController extends CheckController {
    public function index(){
        $this->display();
    }

    /**
     * 添加章节
     */
    public function addchapter(){
        $grade = I('grade/s','');
        $term = I('term/s','');
        $version = I('version/s','');

        $this->assign('grade',$grade);
        $this->assign('term',$term);
        $this->assign('version',$version);
        $this->display();
    }

    public function getchapter(){
        $grade = I('grade/s','');
        $term = I('term/s','');
        $version = I('version/s','');

        $m = M('unit');
        $data_code = $m->field('ks_code')->where('r_grade="%s" and r_volume="%s" and r_version="%s"',$grade,$term,$version)->select();

        $sql_where=' WHERE R_SUBJECT="0001" AND R_GRADE="'.$grade.'" AND R_VOLUME="'.$term.'" AND R_VERSION="'.$version.'" AND KS_TYPE=0 AND C2=1 ';
        if(!empty($data_code)){
            foreach($data_code as $v){
                $sql_where.=" and KS_ID <> '".$v['ks_code']."' ";
            }
        }

        $ccm = M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        $sql = 'SELECT KS_ID,KS_CODE,P_ID,KS_NAME,KS_LEVEL,DISPLAY_ORDER,FLAG,DISPLAY_ABLE,IS_UNIT,KS_TYPE,R_GRADE,R_VOLUME,R_SUBJECT,R_VERSION,SECTION,R_XIU FROM SHARE_KNOWLEDGE_STRUCTURE'.$sql_where.'ORDER BY DISPLAY_ORDER';

        $data = $ccm->query($sql);
        $this->ajaxReturn($data);
    }


    /**
     * 获取中心库目录信息，并插入语文数据库
     */
    public function addchapterFromRms(){
        $chapterlist = I('chapterlist/s','');
        $chapterlist = str_replace('&quot;', '"', $chapterlist);//替换引号
        $chapterArr =  json_decode($chapterlist,true);

        $sql_where='';
        if (count($chapterArr)==1){
            //导出本章节
            $chapterid=$chapterArr[0]['ks_code'];
            $sql_where.=" KS_ID = '".$chapterid."' ";
        }else {
            //导出多个章节
            for ($i=0;$i<count($chapterArr);$i++){
                $sql_where.=" KS_ID = '".$chapterArr[$i]['ks_code']."' OR";
            }
            $sql_where = " ".trim($sql_where,'OR')."  ";
        }

        $ccm = M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        $sql = 'SELECT KS_ID,KS_CODE,P_ID,KS_NAME,KS_LEVEL,DISPLAY_ORDER,FLAG,DISPLAY_ABLE,IS_UNIT,KS_TYPE,R_GRADE,R_VOLUME,R_SUBJECT,R_VERSION,SECTION,R_XIU FROM SHARE_KNOWLEDGE_STRUCTURE WHERE '.$sql_where;

        $data = $ccm->query($sql);

        //格式数组为array('ks_code'=>'style');
        foreach($chapterArr as $v){
            $arr[$v['ks_code']] = $v['style'];
        }

        $m = M('unit');

        foreach($data as $v){
            $v['style'] = $arr[$v['ks_code']];
            $addid = $m->add($v);
            A('Ywmanager/User')->recordUserOption('unit','add','id='.$addid);
        }
        echo 'ok';
    }


    /**
     * 分页展示课程信息
     */
    public function fenye(){
        $pageCurrent=I('pageCurrent/d',0);
        $page_size=I('page_size/d',0);
        $grade=I('grade/s','');
        $term=I('term/s','');
        $version=I('version/s','');
        $Model=M();
        $sql_where='where ks_id<>0  ';
        if($grade==''){
            $sql_where.='';
        }else{
            $sql_where.=' and r_grade="'.$grade.'"';
        }
        if($term==''){
            $sql_where.='';
        }else{
            $sql_where.=' and r_volume="'.$term.'"';
        }
        if($version==''){
            $sql_where.='';
        }else{
            $sql_where.=' and r_version like "%'.$version.'%"';
        }

        $sql='select count(*) as num from yw_unit ';
        $sql1='select * from yw_unit ';

        $sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
        $sql_order=' order by display_order ';
        //echo $sql.$sql_where.$sql_limit;exit();
        $data_total=$Model->query($sql.$sql_where);
        //var_dump($data_total);exit();
        $total=$data_total[0]['num'];
        $sub_pages=4;
        /* 实例化一个分页对象 */
        Vendor('SubPages');
        $subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
        $page= $subPages->subPageCss3();
        $data=$Model->query($sql1.$sql_where.$sql_order.$sql_limit);
        //echo $sql.$sql_where.$sql_order.$sql_limit;
        $word[$page]=$data;
        $this->ajaxReturn($word);
    }


    /**
     * 删除单元
     */
    public function delunit(){
        $ks_code = I('id/s','');
        $text = M('text');
        $data_text = $text->field('id,url')->where('ks_code="%s"', $ks_code)->find();
        unlink('uploadsyw/kewenvoice/'.$data_text['url']);
        $text->where('ks_code="%s"', $ks_code)->delete();

        A('Ywmanager/User')->recordUserOption('text','del','ks_code='.$ks_code);

        $explains = M('text_explains');
        $explains->where('textid="%d"', $data_text['id'])->delete();

        A('Ywmanager/User')->recordUserOption('text_explains','del','textid='.$data_text['id']);

        $note = M('text_note');
        $note->where('textid="%d"', $data_text['id'])->delete();

        A('Ywmanager/User')->recordUserOption('text_note','del','textid='.$data_text['id']);

        $pinyin = M('text_pinyin');
        $pinyin->where('ks_code="%s"', $ks_code)->delete();

        A('Ywmanager/User')->recordUserOption('text_pinyin','del','ks_code='.$ks_code);

        $list = M('unit_word_list');
        $data_list = $list->where('ks_code="%s"', $ks_code)->select();

        $info = M('unit_word_info');

        foreach($data_list as $v){
            $info->where('listid="%d"', $v['id'])->delete();
            A('Ywmanager/User')->recordUserOption('unit_word_info','del','listid='.$v['id']);
        }

        $list->where('ks_code="%s"', $ks_code)->delete();

        A('Ywmanager/User')->recordUserOption('unit_word_list','del','ks_code='.$ks_code);

        $unit = M('unit');
        $unit->where('ks_code="%s"', $ks_code)->delete();
        A('Ywmanager/User')->recordUserOption('unit','del','ks_code='.$ks_code);
    }

    /**
     * 更新目录类型
     */
    public function updateStyle(){
        $ks_code = I('id/s','');
        $style = I('title_new/d',0);
        $m = M('unit');
        $m->where('ks_code="%s"', $ks_code)->setField('style',$style);
        A('Ywmanager/User')->recordUserOption('unit','update','ks_code='.$ks_code);
    }


    public function addkewen(){
        $ks_name = I('ks_name/s','');
        $ks_code = I('ks_code/s','');
        $style = I('style/d',0);

        $this->assign('style',$style);
        $this->assign('ks_name',$ks_name);
        $this->assign('ks_code',$ks_code);
        $this->display();
    }

    public function addkeweninfo(){
        $ks_code = I('ks_code/s','');
        $ks_name = I('ks_name/s','');
        $option = I('option/s','');
        $id = I('id/d',0);
        $style = I('style/d',0);

        if($option == 'edit'){
            $m = M('text');
            $data = $m->where('id="%d"',$id)->find();
            $this->assign('data',$data);
        }
        $this->assign('id',$id);
        $this->assign('ks_code',$ks_code);
        $this->assign('ks_name',$ks_name);
        $this->assign('option',$option);
        $this->assign('style',$style);
        $this->display();
    }

    /**
     * 添加课文内容到text表
     */
    public function addKewenToText(){
        $ks_code=I('ks_code/s','');
        $tncontent=I('tncontent/s','');
        $title=I('title/s',0);
        $filename=I('filename/s',0);
        $filepath=I('filepath/s',0);
        $type = I('type/d',0);
        $option = I('option/s','');
        $id = I('id/d',0);
        $r_code = I('r_code/s','');
        $r_title = I('r_title/s','');

        $m = M('text');

        $data['title']=$title;
        $data['text']=htmlspecialchars_decode($tncontent);
        $data['url']=$filepath;
        $data['r_code']=$r_code;
        $data['r_title']=$r_title;

        if($option == 'add'){
            $info = $m->where('ks_code="%s"',$ks_code)->select();
            $sortid = count($info);
            $data['ks_code']=$ks_code;
            $data['type'] = $type;
            $data['sortid'] = $sortid+1;
            $addid = $m->add($data);
            $this->unitSync($ks_code);
            A('Ywmanager/User')->recordUserOption('text','add','id='.$addid);
        }else if($option == 'edit'){
            $m->where('id="%d"',$id)->save($data);
            $this->unitSync($ks_code);
            A('Ywmanager/User')->recordUserOption('text','update','id='.$id);
        }





    }


    public function delAuthor(){
        $id = I('id/d',0);
        $ks_code = I('ks_code/s','');
        $m = M('text');
        $data['r_title']='';
        $data['r_code']='';
        $m->where('id="%d"',$id)->save($data);
        $this->unitSync($ks_code);
        A('Ywmanager/User')->recordUserOption('text','del','id='.$id);
    }

    /**
     * 分页展示课程信息
     */
    public function fenyeText(){
        $pageCurrent=I('pageCurrent/d',0);
        $page_size=I('page_size/d',0);
        $ks_code=I('ks_code/s','');
        $Model=M();
        $sql_where='where ks_code="'.$ks_code.'"';

        $sql='select count(*) as num from yw_text ';
        $sql1='select * from yw_text ';

        $sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
        $sql_order=' order by sortid ';
        $data_total=$Model->query($sql.$sql_where);
        $total=$data_total[0]['num'];
        $sub_pages=4;
        /* 实例化一个分页对象 */
        Vendor('SubPages');
        $subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
        $page= $subPages->subPageCss3();
        $data=$Model->query($sql1.$sql_where.$sql_order.$sql_limit);
        $word[$page]=$data;
        $this->ajaxReturn($word);
    }


    /**
     * 删除课文
     */
    public function delText(){
        $id = I('id/d',0);
        $m = M('text');
        $data = $m->where('id="%d"', $id)->find();
        unlink('uploadsyw/kewenvoice/'.$data['url']);
        $m->where('id="%d"', $id)->delete();
        $this->unitSync($data['ks_code']);
        A('Ywmanager/User')->recordUserOption('text','del','id='.$id);
    }

    public function upTextSort(){
        $data = I("data/s","");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        foreach($arr as $v){
            $id = $v["id"];
            $sortid = $v["sortid"];

            $id = !is_numeric($id)?0:$id;
            $sortid = !is_numeric($sortid)?0:$sortid;

            $Kchz=M("text");
            $Kchz->id=$id;
            $Kchz->sortid=$sortid;
            $Kchz->save();
            A('Ywmanager/User')->recordUserOption('text','update','id='.$id);

            $re = $Kchz->field('ks_code')->where('id="%d"',$id)->find();
        }
        $this->unitSync($re['ks_code']);
        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
    }

    /**
     * 语句翻译页面
     */
    public function fanyi(){
        $textid = I('bid/d',0);

        $this->assign('textid', $textid);
        $this->display();
    }

    /**
     * 添加/编辑翻译页面
     */
    public function addfanyi(){
        $textid = I('textid/d',0);
        $option = I('option/s','');

        $this->assign('textid',$textid);
        $this->assign('option',$option);
        $this->display();
    }

    /**
     * 添加释义到数据库
     */
    public function addGuwen(){
        $textid=I('textid/d',0);
        $sentence=I('sentence/s','');
        $description=I('description/s','');
        $Model=M('text_explains');

        $info = $Model->where('textid="%d"',$textid)->select();
        $sortid = count($info);

        $data['textid']=$textid;
        $data['content']=$sentence;
        $data['interpretation']=$description;
        $data['sortid'] = $sortid+1;
        $id=$Model->add($data);
        A('Ywmanager/User')->recordUserOption('text_explains','add','id='.$id);

        $text = M('text');
        $re = $text->field('ks_code')->where('id="%d"',$textid)->find();
        $this->unitSync($re['ks_code']);

        echo $id;
    }

    public function upExplainsSort(){
        $data = I("data/s","");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        foreach($arr as $v){
            $id = $v["id"];
            $sortid = $v["sortid"];

            $id = !is_numeric($id)?0:$id;
            $sortid = !is_numeric($sortid)?0:$sortid;

            $Kchz=M("text_explains");
            $Kchz->id=$id;
            $Kchz->sortid=$sortid;
            $Kchz->save();

            A('Ywmanager/User')->recordUserOption('text_explains','update','id='.$id);
        }

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_explains t where t.id='.$id.')';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);

        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
    }



    /**
     * 查询当前释义内容
     */
    public function queryExplainInfo(){
        $id=I('id/d',0);
        $Model=M('text_explains');
        $data=$Model->where('id="%d"',$id)->find();
        $this->ajaxReturn($data);
    }

    /**
     * 获取释义内容
     */
    public function getExplain(){
        $textid = I('textid/d',0);
        $Model=M('text_explains');
        $data = $Model->where('textid="%d"',$textid)->order('sortid')->select();
        $this->ajaxReturn($data);
    }


    /**
     * 编辑释义页面
     */
    public function editguwen(){
        $id = I('id/d',0);

        $Model=M('text_explains');
        $data = $Model->where('id="%d"',$id)->find();
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->display();
    }

    /**
     * 编辑释义
     */
    public function editExplain(){
        $id=I('id/d',0);
        $sentence=I('sentence/s','');
        $description=I('description/s','');
        $Model=M('text_explains');
        $data['content']=$sentence;
        $data['interpretation']=$description;
        $Model->where('id="%d"',$id)->save($data);

        A('Ywmanager/User')->recordUserOption('text_explains','update','id='.$id);

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_explains t where t.id='.$id.')';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);
    }

    /**
     * 删除释义
     */
    public function delExplain(){
        $id=I('id/d',0);
        $Model=M('text_explains');

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_explains t where t.id='.$id.')';
        $re = M()->query($sql);

        $Model->where('id="%d"', $id)->delete();

        A('Ywmanager/User')->recordUserOption('text_explains','del','id='.$id);

        $this->unitSync($re[0]['ks_code']);
    }




    /**
     * 词语解释页面
     */
    public function jieshi(){
        $textid = I('bid/d',0);

        $this->assign('textid', $textid);
        $this->display();
    }

    /**
     * 添加/编辑词语解释页面
     */
    public function addjieshi(){
        $textid = I('textid/d',0);
        $option = I('option/s','');

        $this->assign('textid',$textid);
        $this->assign('option',$option);
        $this->display();
    }
    /**
     * 添加词语解释到数据库
     */
    public function addNote(){
        $textid=I('textid/d',0);
        $sentence=I('sentence/s','');
        $description=I('description/s','');
        $Model=M('text_note');

        $info = $Model->where('textid="%d"',$textid)->select();
        $sortid = count($info);

        $data['textid']=$textid;
        $data['word']=$sentence;
        $data['content']=$description;
        $data['sortid'] = $sortid+1;
        $id=$Model->add($data);
        echo $id;

        A('Ywmanager/User')->recordUserOption('text_note','add','id='.$id);

        $sql = 'SELECT ks_code FROM yw_text WHERE id='.$textid.'';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);
    }


    public function upNoteSort(){
        $data = I("data/s","");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        foreach($arr as $v){
            $id = $v["id"];
            $sortid = $v["sortid"];

            $id = !is_numeric($id)?0:$id;
            $sortid = !is_numeric($sortid)?0:$sortid;

            $Kchz=M("text_note");
            $Kchz->id=$id;
            $Kchz->sortid=$sortid;
            $Kchz->save();

            A('Ywmanager/User')->recordUserOption('text_note','edit','id='.$id);
        }

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_note t where t.id='.$id.')';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);
        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
    }

    /**
     * 查询当前词语解释内容
     */
    public function queryNoteInfo(){
        $id=I('id/d',0);
        $Model=M('text_note');
        $data=$Model->where('id="%d"',$id)->find();
        $this->ajaxReturn($data);
    }

    /**
     * 获取词语解释内容
     */
    public function getNote(){
        $textid = I('textid/d',0);
        $Model=M('text_note');
        $data = $Model->where('textid="%d"',$textid)->order('sortid')->select();
        $this->ajaxReturn($data);
    }



    /**
     * 编辑释义页面
     */
    public function editNote(){
        $id = I('id/d',0);

        $Model=M('text_note');
        $data = $Model->where('id="%d"',$id)->find();
        $this->assign('data',$data);
        $this->assign('id',$id);
        $this->display();
    }



    /**
     * 编辑释义
     */
    public function editNoteInfo(){
        $id=I('id/d',0);
        $sentence=I('sentence/s','');
        $description=I('description/s','');
        $Model=M('text_note');
        $data['word']=$sentence;
        $data['content']=$description;
        $Model->where('id="%d"',$id)->save($data);

        A('Ywmanager/User')->recordUserOption('text_note','edit','id='.$id);

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_note t where t.id='.$id.')';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);
    }

    /**
     * 删除释义
     */
    public function delNote(){
        $id=I('id/d',0);
        $Model=M('text_note');

        $sql = 'SELECT ks_code FROM yw_text WHERE id=(SELECT t.textid FROM yw_text_note t where t.id='.$id.')';
        $re = M()->query($sql);

        $Model->where('id="%d"', $id)->delete();

        A('Ywmanager/User')->recordUserOption('text_note','del','id='.$id);

        $this->unitSync($re[0]['ks_code']);
    }


    /**
     * 导入课文录音页面
     */
    public function importkewen(){
        $ks_code = I('ks_code/s','');
        $this->assign('ks_code',$ks_code);
        $this->display();
    }

    /**
     * 获取课文录音列表
     */
    public function getKewenFromYibai(){
        $ks_code = I('ks_code/s','');
        $text = M('db_yibai.text',null);
        $unit = M('unit');
        $data = $unit->field('r_grade,r_volume,r_version')->where('ks_code="%s"',$ks_code)->find();

        $grade = (int)$data['r_grade'];
        $term = (int)$data['r_volume'];
        $version = (int)$data['r_version'];
        if($version == '0001'){
            $version = 1;
        }

        $data_text = $text->field('id,unit,name,mp3,write,read,cizu')->where('grade_id="%d" and term="%d" and press_id="%d"',$grade,$term,$version)->order('unit_number,lession')->select();
        $this->ajaxReturn($data_text);

    }

    /**
     * 播放mp3
     */
    public function playmp3(){
        $filepath = I('filepath/s','');
        $filepath = '../../ywyibai/kewen/'.$filepath;
        $this->assign('filepath',$filepath);
        $this->display();
    }

    /**
     * 复制语文100音频
     */
    public function copyKewenVoice(){
        $mp3 = I('mp3/s','');
        $dir = date('Ymd');
        $file = md5(uniqid()).'.mp3';
        $filepath = $dir.'/'.$file;
        $filedir = 'uploadsyw/kewenvoice/'.$dir.'/';
        if(!is_dir($filedir)){
            mkdir($filedir,0777);
        }
        if(is_file('ywyibai/kewen/'.$mp3)){
            copy('ywyibai/kewen/'.$mp3,'uploadsyw/kewenvoice/'.$filepath);
            echo $filepath;
        }else{
            echo '';
        }

    }


    /**
     * 导入拼音页面
     */
    public function tags(){
        $ks_code = I('ks_code/s','');
        $m = M();
        // $data = $m->field('id,pinyin')->select();

        $sql = 'SELECT m.id,m.pinyin,CASE WHEN n.pinyinid IS NOT NULL THEN 1 ELSE 0 END AS flag FROM yw_lib_pinyin m LEFT JOIN (SELECT pinyinid FROM yw_text_pinyin WHERE ks_code="'.$ks_code.'") n ON m.id=n.pinyinid order by m.id;';
        $data = $m->query($sql);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 添加拼音到数据库
     */
    public function addPinyinToData(){
        $ids = I('ids/s','');
        $ks_code = I('ks_code/s','');

        $m = M('text_pinyin');
        $m->where('ks_code="%s"',$ks_code)->delete();
        $arr = explode('|',$ids);
        foreach ($arr as $k => $v) {
            $data['ks_code'] = $ks_code;
            $data['pinyinid'] = $v;
            $addid = $m->add($data);

            A('Ywmanager/User')->recordUserOption('text_pinyin','add','id='.$addid);
        }

        $this->unitSync($ks_code);
    }

    public function queryPinyin(){
        $ks_code = I('ks_code/s','');
        $m = M();

        $sql = 'SELECT (SELECT n.pinyin FROM yw_lib_pinyin n WHERE n.id=m.pinyinid) as pinyin FROM yw_text_pinyin m WHERE m.ks_code="'.$ks_code.'" order by m.id;';
        $data = $m->query($sql);

        $pinyin = '';
        foreach($data as $v){
            $pinyin .= ' '.$v['pinyin'];
        }
        echo trim($pinyin);
    }


    /**
     * 生字管理页面
     */
    public function ciyumanage(){
        $ks_name = I('ks_name/s','');
        $ks_code = I('ks_code/s','');
        $style = I('style/d',0);

        $this->assign('style',$style);
        $this->assign('ks_name',$ks_name);
        $this->assign('ks_code',$ks_code);
        $this->display();
    }

    /**
     * 类型管理
     */
    public function typeinfo(){
        $m = M('unit_word_type');
        $data = $m->select();
        $this->assign('data',$data);
        $this->display();
    }


    public function gettypeinfos(){
        $m = M('unit_word_type');
        $data = $m->select();
        $this->ajaxReturn($data);
    }

    public function addtypeinfo(){
        $type = I('type/s','');
        $id = I('id/d',0);

        $m = M('unit_word_type');
        $data = $m->where('id="%d"',$id)->find();
        $this->assign('type',$type);
        $this->assign('id',$id);
        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 添加类型到数据库
     */
    public function addtype(){
        $option = I('option/s','');
        $title = I('title/s','');
        $type = I('type/d',0);
        $id = I('id/d',0);
        $color = I('color/d',0);


        $m = M('unit_word_type');
        $data = $m->where('name="%s" and type="%d"', trim($title),$type)->find();
        if(empty($data)){
            $info['name'] = trim($title);
            $info['type'] = $type;
            $info['color'] = $color;

            if($option == 'add'){
                $re = $m->add($info);
                echo $re;
                A('Ywmanager/User')->recordUserOption('unit_word_type','add','id='.$re);
            }else{
                $m->where('id="%d"',$id)->save($info);
                 A('Ywmanager/User')->recordUserOption('unit_word_type','update','id='.$id);
            }
        }else{
            echo '已存在';
        }
    }

    /**
     * 删除类型
     */
    public function deltype(){
        $id = I('id/d',0);

        $word_list = M('unit_word_list');
        $data = $word_list->where('typeid="%d"',$id)->select();

        if(empty($data)){
            $m = M('unit_word_type');
            $m->where('id="%d"', $id)->delete();
            echo 'ok';

            A('Ywmanager/User')->recordUserOption('unit_word_type','del','id='.$id);
        }else{
            echo 'error';
        }


    }

    /**
     * 编辑类型
     */
    public function edittype(){
        $id = I('id/d',0);
        $title = I('title_new/s','');
        $type = I('type/d',0);

        $m = M('unit_word_type');

        $data['name'] = trim($title);
        $data['type'] = $type;
        $m->where('id="%d"', $id)->save($data);

        A('Ywmanager/User')->recordUserOption('unit_word_type','update','id='.$id);
    }

    /**
     * 获取类型列表
     */
    public function typelist(){
        $m = M('unit_word_type');
        $data = $m->select();
        $this->ajaxReturn($data);
    }

    /**
     * 获取生字或者词的拼音
     */
    public function getPinyin(){
        $word_type_id = I('word_type_id/d',0);
        $leixing = I('leixing/d',0);
        $zi = I('zi/s','');
        $zi = trim($zi);
        $zi = trim($zi,'#');

        $ziArr = explode('#',$zi);

        $result = array();
        foreach($ziArr as $k=>$v){
            $length = mb_strlen($v,'utf8');
            if($length>1){
                //词
                $m = M('lib_zuci');
                $info = $m->field('id,ci,pys,tongyici,fanyici')->where('ci="%s"',$v)->find();
                if(empty($info)){
                    $data = $info;
                }else{
                    $arr = json_decode($info['pys'],true);
                    $data = array();

                    $pinyin = '';
                    $voice = '';
                    foreach($arr['zy'] as $vv){
                        $pinyin .= $vv['y'].' ';
                        $voice .= $vv['v'].'.mp3|';
                    }

                    $data['id'] = $info['id'];
                    $data['ci'] = $info['ci'];
                    // $data['tongyici'] = trim(str_replace('#',' ',$info['tongyici']));
                    // $data['fanyici'] = trim(str_replace('#',' ',$info['fanyici']));

                    $data['tongyici'] = $this->split_cizu(trim(trim($info['tongyici']),'#'));
                    $data['fanyici'] = $this->split_cizu(trim(trim($info['fanyici']),'#'));
                    $data['pinyin'] = rtrim($pinyin,' ');
                    $data['voice'] = rtrim($voice,'|');
                }
            }else{
                //字
                $m = M('lib_cixing');
                $data = $m->field('zi,zi_pinyin,py,cizu')->where('zi="%s"',$v)->select();
                foreach($data as $kk=>$vv){
                    $data[$kk]['cizu'] = $this->split_cizu($vv['cizu']);
                }
		//var_dump($data);
            }
            $result[$k] = $data;
        }




        $this->ajaxReturn($result);
    }



    /**
     * 添加会写，会读，生词到数据库
     * 触发unit
     */
    public function addContent(){
        // $word_type_id = I('word_type_id/d',0);
        // $leixing = I('leixing/d',0);
        // $content = I('content/s','');
        // $py = I('py/s','');
        // $fy = I('fy/s','');
        $ks_code = I('ks_code/s','');
        $infos = I("infos/s","");
        $arr = json_decode(str_replace("&quot;", '"', $infos),ture);

        $list = M('unit_word_list');
        $word_info = M('unit_word_info');

        $errorMsg = '';
        foreach($arr as $v){
            $word_type_id = $v['word_type_id'];
            $content = $v['content'];
            $py = $v['py'];
            $fy = $v['fy'];

            $sql = 'SELECT t.word FROM yw_unit_word_list l,yw_unit_word_info t WHERE l.id=t.listid AND l.typeid='.$word_type_id.' AND l.ks_code="'.$ks_code.'" AND t.word="'.$content.'"';
            $result = M()->query($sql);
            if(!empty($result)){
                $errorMsg .= '<p style="color:red;">【'.$content.'】已存在</p>';
               continue;
            }else{
                $errorMsg .= '<p style="color:green;">【'.$content.'】添加成功</p>';
            }

            $re = $list->where('ks_code="%s" and typeid="%d"', $ks_code,$word_type_id)->find();
            if(empty($re)){
                $sort = $list->where('ks_code="%s"', $ks_code)->select();
                $count = count($sort);
                $info['ks_code'] = $ks_code;
                $info['typeid'] = $word_type_id;
                $info['sortid'] = $count+1;
                $listid = $list->add($info);

                A('Ywmanager/User')->recordUserOption('unit_word_list','add','id='.$listid);
            }else{
                $listid = $re['id'];
            }


            $data['listid'] = $listid;
            $data['word'] = $content;


            $fy = str_replace('.mp3','',$fy);
            $fy = str_replace('|',' ',$fy);
            $data['py'] = $py;
            $data['fy'] = $fy;

            $res = $word_info->where('listid="%d"',$listid)->select();
            $sortid = count($res);
            $data['sortid'] = $sortid+1;
            $addid = $word_info->add($data);

            A('Ywmanager/User')->recordUserOption('unit_word_info','add','id='.$addid);
        }

        $this->unitSync($ks_code);
        echo $errorMsg;
    }

    /**
     * 获取课文标签页的内容(字词内容)
     */
    public function pagelist(){
        $ks_code = I('ks_code/s','');

        $m = M();
        $sql = 'SELECT l.id,(SELECT t.`name` FROM yw_unit_word_type t WHERE t.id=l.typeid) typename,(SELECT t.`type` FROM yw_unit_word_type t WHERE t.id=l.typeid) type,(SELECT t.`color` FROM yw_unit_word_type t WHERE t.id=l.typeid) color FROM yw_unit_word_list l WHERE l.ks_code="%s" ORDER BY l.sortid;';
        $data_list = $m->query($sql,$ks_code);

        $word_info = M('unit_word_info');

        foreach($data_list as $k=>$v){
            $data_info = $word_info->field('word')->where('listid="%d"',$v['id'])->order('sortid')->select();
            $data_list[$k]['info'] = $data_info;
        }
        $this->ajaxReturn($data_list);
    }



    //会读、会写管理页面
    public function ziciedit(){
        $ks_code = I('ks_code/s','');
        $listid = I('listid/d',0);
        $this->assign('ks_code',$ks_code);
        $this->assign('listid',$listid);
        $this->display();
    }


    //返回字词的结果
    public function fenyeZici(){
        $listid = I('listid/d',0);
        $ks_code = I('ks_code/s','');


        $word_info = M('unit_word_info');
        $re = $word_info->where('listid="%d"',$listid)->order('sortid')->select();
        foreach($re as $k=>$v){
            $length = mb_strlen($v['word'],'utf8');
            if($length == 1){
                //字
                $n = M('lib_cixing');
                $data = $n->field('zi,zi_pinyin,py,cizu')->where('zi="%s" and py="%s"',$v['word'],$v['fy'])->find();
                $re[$k]['zuci'] = $this->split_cizu($data['cizu']);
            }else{
                //词
                $n = M('lib_zuci');
                $info = $n->field('id,ci,pys,tongyici,fanyici')->where('ci="%s"',$v['word'])->find();
                $tongyici = $this->split_cizu($info['tongyici']);
                $fanyici = $this->split_cizu($info['fanyici']);
                $re[$k]['zuci'] = '近义词：'.$tongyici.'|反义词：'.$fanyici;
            }
        }
        $this->ajaxReturn($re);
    }

    private function split_cizu($cizu){
        $arr = explode('#',$cizu);
        return trim($arr[0].' '.$arr[1].' '.$arr[2]);
    }


    /**
     * 更新字词的顺序
     */
    public function upziciSort(){
        $data = I("data/s","");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        foreach($arr as $v){
            $id = $v["id"];
            $sortid = $v["sortid"];

            $id = !is_numeric($id)?0:$id;
            $sortid = !is_numeric($sortid)?0:$sortid;

            $word_info = M('unit_word_info');
            $word_info->id = $id;
            $word_info->sortid = $sortid;
            $word_info->save();

            A('Ywmanager/User')->recordUserOption('unit_word_info','update','id='.$id);
        }

        $sql = 'SELECT ks_code FROM yw_unit_word_list WHERE id=(select listid from yw_unit_word_info where id='.$id.')';
        $re = M()->query($sql);
        $this->unitSync($re[0]['ks_code']);

        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
    }

    /**
     * 更新字词的顺序
     */
    public function upTypeSort(){
        $data = I("data/s","");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);

        foreach($arr as $v){
            $id = $v["id"];
            $sortid = $v["sortid"];

            $id = !is_numeric($id)?0:$id;
            $sortid = !is_numeric($sortid)?0:$sortid;

            $unit_word_list = M('unit_word_list');
            $unit_word_list->id = $id;
            $unit_word_list->sortid = $sortid;
            $unit_word_list->save();

            A('Ywmanager/User')->recordUserOption('unit_word_list','update','id='.$id);
        }
        if(!is_null($id)){
            $sql = 'SELECT ks_code FROM yw_unit_word_list WHERE id='.$id;
            $re = M()->query($sql);
            $this->unitSync($re[0]['ks_code']);
        }

        $arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return);
    }

    /**
     * 删除字词
     */
    public function delzici(){
        $id = I('id/d',0);
        $m = M('unit_word_info');

        $sql = 'SELECT ks_code FROM yw_unit_word_list WHERE id=(select listid from yw_unit_word_info where id='.$id.')';
        $re = M()->query($sql);
        $m->where('id="%d"', $id)->delete();
        $this->unitSync($re[0]['ks_code']);
        A('Ywmanager/User')->recordUserOption('unit_word_info','del','id='.$id);
    }
    /**
     * 删除当前单元标签
     */
    public function delUnitType(){
        $id = I('id/d',0);
        $m = M('unit_word_list');
        $n = M('unit_word_info');
        $n->where('listid="%d"', $id)->delete();

        A('Ywmanager/User')->recordUserOption('unit_word_info','del','listid='.$id);

        $sql = 'SELECT ks_code FROM yw_unit_word_list WHERE id='.$id;
        $re = M()->query($sql);

        $m->where('id="%d"', $id)->delete();

        $this->unitSync($re[0]['ks_code']);
        A('Ywmanager/User')->recordUserOption('unit_word_list','del','id='.$id);

        echo 'ok';
    }





    /**
     * 导入课文会写，会读，生词
     */
    public function importzici(){
        $ks_code = I('ks_code/s','');
        $this->assign('ks_code',$ks_code);
        $this->display();
    }

    /**
     * 把语文100的字词写入数据库
     */
    public function addziciToDb(){
        $id = I('id/d',0);
        $ks_code = I('ks_code/s','');

        $text = M('db_yibai.text',null);
        $unit = M('unit');

        $data_text = $text->field('write,read,cizu')->where('id="%d"',$id)->find();
        $arr['read'] = $data_text['read'];
        $arr['write'] = $data_text['write'];
        $arr['note'] = $data_text['cizu'];


        $word_info = M('unit_word_info');
        $list = M('unit_word_list');

        //先删
        // $data_list = $list->field('id')->where('ks_code="%s',$ks_code)->select();
        // foreach($data_list as $v){
        //     $word_info->where('listid="%d"',$v['id'])->delete();
        // }
        // $list->where('ks_code="%s',$ks_code)->delete();
        //后加

        $data_list_write = $list->field('id')->where('ks_code="%s" and typeid=1',$ks_code)->find();
        $data_list_read = $list->field('id')->where('ks_code="%s" and typeid=2',$ks_code)->find();
        $data_list_word = $list->field('id')->where('ks_code="%s" and typeid=3',$ks_code)->find();


        $listid_write = $this->checkword_list($ks_code,$data_list_write,1,$list);
        $listid_read = $this->checkword_list($ks_code,$data_list_read,2,$list);
        $listid_word = $this->checkword_list($ks_code,$data_list_word,3,$list);



        $writeArr = json_decode($arr['write'],true);
        $readArr = json_decode($arr['read'],true);
        $wordArr = json_decode($arr['note'],true);

        $this->addziciFromYibai($writeArr['w'],$word_info,$listid_write);
        $this->addziciFromYibai($readArr['r'],$word_info,$listid_read);
        $this->addziciFromYibai($wordArr['r'],$word_info,$listid_word);

        $this->unitSync($re[0]['ks_code']);

        $re["errcode"] = 1;
        $re["msg"] = 'OK';
        $this->ajaxReturn($re);

    }

    //检查word_list是否有会读，会写，生词
    private function checkword_list($ks_code,$arr,$type,$list){
        if(empty($arr)){
            $sort = $list->where('ks_code="%s"', $ks_code)->select();
            $count = count($sort);
            $list->ks_code = $ks_code;
            $list->typeid = $type;
            $list->sortid = $count+1;
            $listid = $list->add();
            return $listid;

            A('Ywmanager/User')->recordUserOption('unit_word_list','add','id='.$listid);
        }else{
            return $arr['id'];
        }
    }

    private function addziciFromYibai($arr,$word_info,$listid){
        foreach($arr as $k=>$v){
            $content = $v['n'];
            $py = $v['p'];
            $fy = $this->pyformat($v['p']);
            $fy = str_replace('|',' ',$fy);
            $data['listid'] = $listid;
            $data['word'] = $content;
            $data['py'] = $py;
            $data['fy'] = $fy;
            $data['sortid'] = $k+1;
            $id = $word_info->add($data);

            A('Ywmanager/User')->recordUserOption('unit_word_info','add','id='.$id);
        }
    }


    /**
     * 格式化拼音
     */
    private function pyformat($pinyin){
        $pinyin = trim($pinyin);
        $arr = explode(' ',$pinyin);
        $fy = '';
        foreach($arr as $v){
            $info = $this->checkPinYin($v);
            if($info['flag']){
                //匹配成功
                $fy .= $info['fy'].'|';
            }else{
                $re["errcode"] = 2;
                $re["msg"] = '拼音匹配失败';
                $this->ajaxReturn($re);
            }
        }
        return rtrim($fy,'|');
    }


    /**
     * 检查发音是几声
     */
    private function checkPinYin($fayin){
        //基础数组aeiouv（其中v没有一声）
        $array = array('ḿ'=>'2','ń'=>'2','ň'=>'3',''=>'4','a'=>'0','ā'=>'1','á'=>'2','ǎ'=>'3','à'=>'4','e'=>'0','ē'=>'1','é'=>'2','ě'=>'3','è'=>'4','o'=>'0','ō'=>'1','ó'=>'2','ǒ'=>'3','ò'=>'4','ī'=>'1','í'=>'2','ǐ'=>'3','ì'=>'4','u'=>'0','ū'=>'1','ú'=>'2','ǔ'=>'3','ù'=>'4','ü'=>'0','ǘ'=>'2','ǚ'=>'3','ǜ'=>'4','i'=>'0');
        $arrayBase = array('ḿ'=>'m','ń'=>'n','ň'=>'n',''=>'n','a'=>'a','ā'=>'a','á'=>'a','ǎ'=>'a','à'=>'a','e'=>'e','ē'=>'e','é'=>'e','ě'=>'e','è'=>'e','i'=>'i','ī'=>'i','í'=>'i','ǐ'=>'i','ì'=>'i','o'=>'o','ō'=>'o','ó'=>'o','ǒ'=>'o','ò'=>'o','u'=>'u','ū'=>'u','ú'=>'u','ǔ'=>'u','ù'=>'u','ü'=>'ü','ǘ'=>'ü','ǚ'=>'ü','ǜ'=>'ü');

        $flag = 0;
        //遍历基础数组，判断当前发音是几声
        foreach($array as $k=>$v){
            $pos = strpos($fayin, $k);
            if($pos === false){
                //没有匹配
                $flag = 0;
            }else{
                $flag = 1;
                $yinNum = $v;
                $fayin = str_replace($k, $arrayBase[$k], $fayin);
                $fayin = str_replace('ü', 'v', $fayin);
                return array('flag'=>$flag,'fy'=>$fayin.$yinNum);
                exit;
            }
        }
        return array('flag'=>$flag);
        exit;
    }


    public function unitSyncAll(){
        $unit = M('unit');
        $data = $unit->field('ks_code')->select();
        foreach($data as $v){
            $ks_code = $v['ks_code'];
            $this->unitSync($ks_code);
        }
    }



    /**
     * 单元同步
     */
    public function unitSync($ks_code){
    $flag = 1;// 1全部，2生字，3课文
    if($ks_code!='00010903020319'||$ks_code!='00010903020322'||$ks_code!='00010a03020309'||$ks_code!='00010a0302030c'){

        $unit = M('unit');

        $text = M('text');
        $text_note = M('text_note');
        $text_explains = M('text_explains');
        $text_pinyin = M('text_pinyin');

        $m = M();

        $arr = array();

        $unitData = $unit->field('ks_code,style')->where('ks_code="%s"',$ks_code)->find();
        $textData = $text->field('id,title,text,url,r_code')->where('ks_code="%s"',$ks_code)->order('sortid')->select();

        //课文(默认)
        $arr['kewen'] = $textData;
        //课文
        if($unitData['style'] == 1){
            foreach($textData as $k=>$v){
                $textid = $v['id'];
                $noteData = $text_note->field('word,content')->where('textid="%d"',$textid)->order('sortid')->select();
                $textData[$k]['note'] = $noteData;
            }
            $arr['kewen'] = $textData;
        }
        //古文
        if($unitData['style'] == 2){
            foreach($textData as $k=>$v){
                $textid = $v['id'];
                $explainsData = $text_explains->field('content,interpretation')->where('textid="%d"',$textid)->order('sortid')->select();
                $noteData = $text_note->field('word,content')->where('textid="%d"',$textid)->order('sortid')->select();
                $textData[$k]['explains'] = $explainsData;
                $textData[$k]['note'] = $noteData;
            }
            $arr['kewen'] = $textData;
        }
        //拼音
        if($unitData['style'] == 3){
            $sql = 'SELECT t.pinyin,t.info FROM yw_text_pinyin l,yw_lib_pinyin t WHERE l.pinyinid=t.id and l.ks_code="'.$ks_code.'" ORDER BY t.id';
            $pingyinData = $m->query($sql);

            $arr['pinyin'] = $pingyinData;
            $arr['kewen'] = $textData;
        }


// var_dump($arr['kewen']);exit;


        //获取生字

        $sql = 'SELECT l.id,t.`name`,t.type,t.color FROM yw_unit_word_list l,yw_unit_word_type t WHERE l.typeid=t.id  AND l.ks_code="%s" ORDER BY l.sortid;';
        $data_list = $m->query($sql,$ks_code);

        $word_info = M('unit_word_info');

        foreach($data_list as $k=>$v){
            $data_info = $word_info->field('word,py,fy')->where('listid="%d"',$v['id'])->order('sortid')->select();
            if(empty($data_info)){
                unset($data_list[$k]);
            }else{
                foreach($data_info as $info_k => $info_v){
                    //此处修改,对应的TextController也需要修改
                    $length = mb_strlen($info_v['word'],'utf8');
                    if($length==1){
                        //字
                        $Model = M();
                        $sql_py = 'select cizu from yw_lib_cixing where zi="'.$info_v['word'].'" and py="'.$info_v["fy"].'"';
                        $data_py = $Model->query($sql_py);

                        $py_str = '';
                        $py_str .= $info_v['fy'];

                        if(empty($data_py)){
                            //没有组词
                            $data_info[$info_k]['fayin'] = $this->getpys($py_str,0);
                            $data_info[$info_k]['zuci'] = '';
                        }else{
                            //有组词
                            if(empty(trim($data_py[0]['cizu'],'#'))){
                                //字段为空
                                $data_info[$info_k]['fayin'] = $this->getpys($py_str,0);
                                $data_info[$info_k]['zuci'] = '';
                            }else{
                                //不为空
                                $arr_py = explode('#',trim($data_py[0]['cizu'],'#'));
                                $arr_py = array_slice($arr_py,0,3);
                                if(count($arr_py) == 3){
                                    array_pop($arr_py);
                                }
                                $sql_pys = 'select pys from yw_lib_zuci where ci="'.$arr_py[0].'"';
                                $data_pys = $Model->query($sql_pys);

                                $pyjson = $data_pys[0]['pys'];
                                $jsonArr = json_decode($pyjson,true);
                                foreach($jsonArr['zy'] as $json_v){
                                    $py_str .= '|'.$json_v['v'];
                                }
                                $py_str .= '|de0|'.$info_v['fy'];
                                $data_info[$info_k]['fayin'] = $this->getpys($py_str,2);
                                $data_info[$info_k]['zuci'] = $arr_py;
                            }
                        }
                    }else{
                        //词
                        $py_str = str_replace(' ','|',trim($info_v['fy']));
                        $data_info[$info_k]['fayin'] = $this->getpys($py_str,0);
                        $data_info[$info_k]['zuci'] = '';
                    }
                }
                $data_list[$k]['info'] = $data_info;
            }
        }

        $wordJson = json_encode($data_list,JSON_UNESCAPED_UNICODE);

        if($flag == 1||$flag == 2){
            if(!empty($data_list)){
                $data['word'] = $wordJson;
            }else{
                $data['word'] = '';
            }
        }



        foreach($arr['kewen'] as $kkk=>$vvv){
            $text = $vvv['text'];
            $noteArr = $vvv['note'];

            if(!empty($noteArr)){
                //古文词语解释
                foreach($noteArr as $note){
                    $ciyu = $note['word'];
                    $content = $note['content'];
                    $to = '<s explain="'.$content.'" onclick="showExplain(this);">'.$ciyu.'</s>';
                    $pattern = "/>[^>]+</";
                    preg_match_all($pattern, $text, $result);
                    $count = 0;
                    foreach($result[0] as $values){
                        if($count == 0){
                            $tostrtmp = $this->str_replace_limit($ciyu,$to,$values,1);
                            if($tostrtmp !== $values){
                                $count++;
                            }
                            $text = $this->str_replace_limit($values,$tostrtmp,$text,1);
                        }
                    }
                }
            }

            //循环字,判断color:1绿色,2蓝色内容在课文中
            foreach($data_list as $v){
                $color = $v['color'];
                if($color ==1 ){
                    //绿色
                    $class = 'greenFont';
                }else if($color == 2){
                    //蓝色
                    $class = 'yingFont';
                }else{
                    continue;
                }
                foreach($v['info'] as $vv){
                    $word = $vv['word'];
                    $py = $vv['py'];//hǎo
                    $fy = $vv['fy'];//hao3
                    $tostr = '<font class="'.$class.'" py="'.$py.'" fy="'.$fy.'" onclick="getZi(\''.$word.'\',\''.$py.'\',\''.$fy.'\',this);">'.$word.'</font>';
                    //替换的时候避免替换到html标签中的文字
                    $pattern = "/>[^>]+</";
                    preg_match_all($pattern, $text, $result);
                    $count = 0;
                    foreach($result[0] as $vvv){
                        if($count == 0){
                            $tostrtmp= $this->str_replace_limit($word,$tostr,$vvv,1);
                            if($tostrtmp !== $vvv){
                                $count++;
                            }
                            $text = $this->str_replace_limit($vvv,$tostrtmp,$text,1);
                        }
                    }

                }
            }
            $arr['kewen'][$kkk]['text'] = $text;
        }


    if(!empty($arr['pinyin'])||!empty($arr['kewen'])){
        if($flag == 1||$flag == 3){
            $data['content'] = json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }

        $unit->where('ks_code="%s"',$ks_code)->save($data);



    }//ifEND




    }

public function abc(){
    $ks_code = I('ks_code');
    $this->unitSync($ks_code);
}

    //获取发音
    //$pys=a|b|c
    //$num 停顿的秒数
    public function getpys($pys,$num){
        $py_Arr = explode('|',$pys);
        $zifyArr = array();
        for($i=0;$i<count($py_Arr);$i++){
            if($i==0){
                $zifyArr[$i] = array('fy'=>$py_Arr[$i],'sleep'=>$num);
            }else{
                $zifyArr[$i] = array('fy'=>$py_Arr[$i],'sleep'=>'0');
            }
        }
        return $zifyArr;
    }



    /**
    *替换字符串，只替换一次
    */

    function str_replace_limit($search, $replace, $subject, $limit=-1) {

        if (is_array($search)) {
            foreach ($search as $k=>$v) {
            $search[$k] = '`' . preg_quote($search[$k],'`') . '`';
            }
        }
        else {
            $search = '`' . preg_quote($search,'`') . '`';
        }

    return preg_replace($search, $replace, $subject, $limit);
    }





    private function wordFormat($str,$content,$text){
        preg_match_all("/./u", $str, $arr);
        $to = '<s explain="'.$content.'">'.$str.'</s>';
        $text = str_replace($str,$to,$text,$i);
        if($i == 0){
           $text = $this->wordSplit($str,$text);
        }
        return $text;
    }

    private function wordSplit($str,$text){
        preg_match_all("/./u", $str, $arr);

        $count = count($arr[0]);

        foreach($arr[0] as $v){
            $from1 = '<font class="yingFont">'.$v.'</font>';
            $from2 = '<font class="greenFont">'.$v.'</font>';
            $str1 = str_replace($v,$from1,$str);
            $str2 = str_replace($v,$from2,$str);

            $text = str_replace($str,$str1,$text,$m);
            if($m == 1){
                break;
            }
            $text = str_replace($str,$str2,$text,$n);
            if($n == 1){
                break;
            }
        }
        return $text;
    }



    /**
     * 处理数组成json
     * $type:1,write,2,read,3,word
     */
    private function createJson($arr,$type){
        $json = '{"info":[';
        foreach($arr as $k=>$v){
            if($type == 1|$type == 2){
                $content = $v['zi'];
            }else if($type ==3 ){
                $content = $v['word'];
            }
            $py = $v['py'];
            $fy = $v['fy'];
            $json .= '{"z":"'.$content.'","y":"'.$py.'","v":"'.$fy.'"},';
        }
        $json = rtrim($json,',');
        $json .= ']}';
        return $json;
    }

    /**
     * 创建内容的json（包含text，text_explains,text_not,pinyin）
     */
    public function createContentJson($textData,$pingyinData){
        $text_note = M('text_note');
        $text_explains = M('text_explains');

        $arr = array();
        foreach($textData as $k=>$v){
            $textid = $v['id'];
            $explainsData = $text_explains->field('content,interpretation')->where('textid="%d"',$textid)->select();
            $noteData = $text_note->field('word,content')->where('textid="%d"',$textid)->select();
            $textData[$k]['explians'] = $explainsData;
            $textData[$k]['note'] = $noteData;
        }

        $arr['text'] = $textData;
        $arr['pinyin'] = $pingyinData;

        $json = json_encode($arr);
        return $json;
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


    public function test(){
        $dir = 'pic/';
        $bishun = 'uploadsyw/pinyinpic/bishun/';
        $read = 'uploadsyw/pinyinpic/read/';
        $write = 'uploadsyw/pinyinpic/write/';

        vendor('fileDirUtil');
        $util = new \fileDirUtil();

        $m = M('lib_pinyin');
        $data = $m->select();

        $arr = array();

        foreach($data as $k=>$v){
            $pinyin = str_replace('ü','v',$v['pinyin']);
            //笔顺
            $bishunpic = $pinyin.'.png';
            $bishunDir = $dir.'A/'.$bishunpic;
            $targetDirbishun = $bishun.$bishunpic;

            $this->checkFile($bishunDir,$targetDirbishun);

            $arr[$pinyin]['bishun']['pic'] = $bishunpic;

            //写
            $writepic = $pinyin.'.png';
            $writeDir = $dir.'B/'.$writepic;
            $targetDirwrite = $write.$writepic;

            $this->checkFile($writeDir,$targetDirwrite);

            $arr[$pinyin]['write']['pic'] = $writepic;


            //读
            $dirlist = $dir.'C/'.$pinyin.'/';
            if(!is_dir($dirlist)){
                exit('no dir-----'.$dirlist);
            }

            $piclist = $util->dirList($dirlist);
            // var_dump($piclist);
            foreach($piclist as $kk=>$vv){
                if(!is_dir($read.$pinyin)){
                    mkdir($read.$pinyin);
                }
                $targetDirRead = $read.$pinyin.'/'.basename($vv);
                copy($vv,$targetDirRead);
                $arr[$pinyin]['read'][$kk]['pic'] = $targetDirRead;
                $arr[$pinyin]['read'][$kk]['v'] = 'a.mp3';
            }
            // exit;
            $json = json_encode($arr[$pinyin]);
            $m->where('id="%d"',$v['id'])->setField('info',$json);
        }
        // var_dump($arr);

    }


    public function checkFile($file,$dir){
        if(file_exists($file)){
            copy($file,$dir);
        }else{
            exit($file.'no');
        }
    }


    public function aa(){
        // vendor('fileDirUtil');
        // $util = new \fileDirUtil();
        // $dirlist = 'pic/C/b/';
        // $piclist = $util->dirList($dirlist);
        // var_dump($piclist);


        // $m = M('lib_pinyin');
        // $data = $m->select();

        // foreach($data as $v){
        //     $sql = 'update yw_lib_pinyin set info = "'.$v['info'].'" where id='.$v['id'].';';

        //     file_put_contents('111.txt', $sql.PHP_EOL,FILE_APPEND);
        // }


        $str = '<p>望天门山</p><p>李白</p><p><br/></p><p>天门中断<s explain="楚江：即长江。古代长江中游地带属楚国，所以叫楚江。" onclick="showExplain(this);">aa楚江</s>开，</p><p>碧水东楚流至此回。</p><p>两岸青山相对出，</p><p>孤帆一片日边来。</p><p><br/></p>';

    $pattern = ' A.*?(?=B)';
    $pattern2 = "/>[^>]+</";
    $pattern3 = "/>[^>]+(门)+[^>]+</";
    preg_match_all($pattern2, $str, $arr);
var_dump($arr);
    $replacement = 'A$1B';
    var_dump($str);
  var_dump( preg_replace($pattern2, $replacement, $str));

$text = "ooo    我是   空格";
    preg_match_all("/(\s+)/", $str, $arr);
var_dump($arr);
$text = preg_replace("/(\s+)/",' ',$text);
echo $text;
// print preg_replace($pattern, '\\1', $str);
        // for($i=0;$i<$count;$i++){
        //     echo $from.$i;
        // }

        // $m = M('unit');
        // $data = $m->where('ks_id="00010203020402"')->find();
        // $this->ajaxReturn($data);
    }




    /**
     * 验证lib_cixing中的都在词库中存在
     * @return [type] [description]
     */
    public function checkCiyu(){
        $cixing = M('lib_cixing');
        $zuci = M('lib_zuci');
        $data_cixing = $cixing->field('id,cizu')->select();
        foreach ($data_cixing as $k => $v) {
            $arr = explode('#',$v['cizu']);

            foreach($arr as $kk=>$vv){
                $data_zuci = $zuci->where('ci="%s"',$vv)->find();
                if(empty($data_zuci)){
                    unset($arr[$kk]);
                }
            }

            $str = implode('#',$arr);
            $cixing->where('id="%d"',$v['id'])->setField('cizu',$str);
        }
        echo 'ok';
    }


    /**
     * 验证lib_zuci中的近义词，反义词都在zuci表中存在
     * @return [type] [description]
     */
    public function checkZuci(){
        $zuci = M('lib_zuci');
        $data_zuci = $zuci->field('id,tongyici,fanyici')->select();
        foreach($data_zuci as $k=>$v){
            $arr_t = explode('#',trim($v['tongyici'],'#'));
            $arr_f = explode('#',trim($v['fanyici'],'#'));

            foreach($arr_t as $kt=>$vt){
                $data = $zuci->where('ci="%s"',$vt)->find();
                if(empty($data)){
                    unset($arr_t[$kt]);
                }
            }
            foreach($arr_f as $kf=>$vf){
                $data = $zuci->where('ci="%s"',$vf)->find();
                if(empty($data)){
                    unset($arr_f[$kf]);
                }
            }

            $str_t = implode('#',$arr_t);
            $str_f = implode('#',$arr_f);

            $str_t = '#'.$str_t.'#';
            $str_f = '#'.$str_f.'#';

           $re['tongyici'] = $str_t;
           $re['fanyici'] = $str_f;
           $zuci->where('id="%d"',$v['id'])->save($re);
        }
        echo 'ok';
    }


    /**
     * 修正生字，词语的发音
     * @return [type] [description]
     */
    public function repairVoice(){
        $m = M();
        $sql = 'select id,word from yw_unit_word_info';

        $data = $m->query($sql);

        $word_info = M('unit_word_info');

        foreach($data as $v){
            $id = $v['id'];
            $len = mb_strlen($v['word'],'utf-8');
            if($len == 1){
                // $sql_zi = 'select pys from yw_lib_zi where zi="'.$v['word'].'"';
                // $data_zi = $m->query($sql_zi);
                // foreach($data_zi as $kk=>$vv){
                //     $pys = $vv['pys'];
                //     $arr = $this->getFyPy($pys);
                //     $word_info->where('id="%d"',$id)->save($arr);
                // }
            }else{
                //词
                $sql_ci = 'select id,ci,pys from yw_lib_zuci where ci="'.$v['word'].'"';
                $data_ci = $m->query($sql_ci);
                foreach($data_ci as $kk=>$vv){
                    $pys = $vv['pys'];
                    $arr = $this->getFyPy($pys);
                    $word_info->where('id="%d"',$id)->save($arr);
                }
            }
        }


        //重新生成一遍json
        $this->unitSyncAll();
    }


    /**
     * 修正生字，词语的发音
     * @return [type] [description]
     */
    public function repairVoiceByWord($word){
        $m = M();
        $sql = 'select id,ci,pys from yw_lib_zuci where ci="'.$word.'"';

        $data = $m->query($sql);

        $word_info = M('unit_word_info');

        $kscodeArr = array();

        foreach($data as $k=>$v){
            $pys = $v['pys'];
            $sql_word = 'select id,listid,py,fy from yw_unit_word_info where word="'.$v['ci'].'"';
            $data_word = $m->query($sql_word);
            if(!empty($data_word)){
                foreach($data_word as $kk=>$vv){
                    $id = $vv['id'];
                    $arr = $this->getFyPy($pys);
                    $word_info->where('id="%d"',$id)->save($arr);
                    $sql_list = 'select ks_code from yw_unit_word_list where id='.$vv['listid'];
                    $data_list = $m->query($sql_list);
                    array_push($kscodeArr,$data_list[0]['ks_code']);
                }
            }
        }

        $kscodeArr = array_unique($kscodeArr);
        //重新生成一遍json
        foreach($kscodeArr as $k=>$v){
            $this->unitSync($v);
        }
    }


    private function getFyPy($pys){
        $jsonArr = json_decode($pys,true);
        $py = '';
        $fy = '';

        // var_dump($jsonArr);
        foreach($jsonArr['zy'] as $k=>$v){
            $py .= $v['y'].' ';
            $fy .= $v['v'].' ';
        }

        $arr['py'] = trim($py);
        $arr['fy'] = trim($fy);

        return $arr;
    }




    public function getDanyin(){
        set_time_limit(0);
        $m = M('lib_zi');
        $data = $m->field('id,pys,zi')->select();
        foreach($data as $v){
            $arr = json_decode($v['pys'],true);
            if(count($arr['zy'])==1){
                $fy = $arr['zy'][0]['v'];
                $fy1 = substr($fy,0,strlen($fy)-1);
                $num = substr($fy,strlen($fy)-1);
                $zi = $v['zi'];

                $field['yin'] = $fy;
                $field['yinbiao'] = $fy1;
                $field['num'] = $num;
                $field['zi'] = $zi;

                M('danyinzi')->add($field);
            }else{
                $zi = $v['zi'];
                $id = $v['id'];

                $field['zi'] = $zi;
                $field['zid'] = $id;
                M('duoyinzi')->add($field);
            }
        }
    }


    public function checkDuoyin(){
        set_time_limit(0);
        $sql = 'select t.pys,t.zi,t.id from yw_duoyinzi l,yw_lib_zi t where l.zid = t.id ';
        $data = M()->query($sql);

        foreach($data as $v){
            $zi = $v['zi'];
            $zid = $v['id'];
            $arr = json_decode($v['pys'],true);
            if(count($arr['zy'])>1){

                foreach($arr['zy'] as $vv){
                    $fy = $vv['v'];
                    $fy1 = substr($fy,0,strlen($fy)-1);
                    $num = substr($fy,strlen($fy)-1);
                    $sql_c = 'select yin  from yw_danyinzi where yin="'.$fy.'"';
                    $data_c = M()->query($sql_c);
                    if(empty($data_c)){
                        // echo $zid.'|'.$zi.'<br>';
                        $field['yin'] = $fy;
                        $field['yinbiao'] = $fy1;
                        $field['num'] = $num;
                        $field['zi'] = $zi;
                        M('danyinzi2')->add($field);
                    }
                }
            }else{
                //没有音
            }

        }

    }



    public function getErrorZi(){
        set_time_limit(0);
        $sql = 'select word,fy from yw_unit_word_info where LENGTH(word)=3';
        $data = M()->query($sql);
        foreach($data as $v){
            $fy = $v['fy'];
            $word = $v['word'];
            $arr = explode(' ',trim($fy));
            if(count($arr)>1){
                echo $word.'<br>';
            }
        }
    }





}
