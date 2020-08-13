<?php
namespace Yuwen\Controller;
use Think\Controller;
class TextController extends CheckController {
    /**
     * 验证用户信息,并更新
     */
    private function checkUserInfo(){
        $username = cookie("username");
        $truename = cookie("truename");
        $areacode = cookie("localAreaCode");
        $classid = cookie("classid");
        $schoolid = cookie("schoolid");

        if(empty($username)){
            $username = '111';
            $areacode = '111.';
            $truename = 'zb';
            $classid = '111';
            $schoolid = '111';
        }


        $sql = 'select id from yw_users where userid="%s" and userarea="%s"';
        $userdata = M()->query($sql,$username,$areacode);


        $data['username'] = $truename;
        $data['userid'] = $username;
        $data['userarea'] = $areacode;
        $data['classid'] = $classid;
        $data['schoolid'] = $schoolid;
        if(empty($userdata)){
            $uid = M('users')->add($data);
        }else{
            $uid = $userdata[0]['id'];
            M('users')->where('userid="%s" and userarea="%s"',$username,$areacode)->save($data);
        }

        cookie("cn_uid",$uid);
    }

    public function getuserinfos(){
        // cookie('sso_url','sso.youjiaotong.com');
        // cookie('username','41010110197476');
        // cookie('truename','测试3');
        // cookie('classid','143694889445646396');
        // cookie('schoolid','4101010001');


        $sso = str_replace("http://","",cookie('sso_url'));
        $yjArr = get_ip('',$sso);

        $ssoip = $yjArr['sso']['c1'];
        $tmsip = $yjArr['tms']['c1'];
        $ubip = $yjArr['ub']['c1'];
        $ilearnip = $yjArr['ilearn']['c1'];
        $plsip = $yjArr['pls']['c1'];
        $localAreaCode = $yjArr['sso']['c3'];

        cookie('ssoip', $ssoip);
        cookie('tmsip', $tmsip);
        cookie('ubip', $ubip);
        cookie('ilearnip', $ilearnip);
        cookie('plsip', $plsip);
        cookie('localAreaCode', $localAreaCode);
        $this->checkUserInfo();
    }



    public function text(){
        // $this->getuserinfos();
        $localAreaCode = cookie('localAreaCode');
        $this->assign('username',cookie('username'));
        $this->assign('plsip',cookie('pls_url'));
        $this->assign('localAreaCode',$localAreaCode);
        $this->display('text');
    }


    /**
     * 获取当前单元课文信息
     */
    public function getUnitInfo(){
        $ks_code = I('ks_code/s','');

        // $unit = D('unit');
        // $data = $unit->getUnitContentInfo($ks_code);
        // $m = M('db_english.rms_unit','engs_');
        $sql = 'select style,content,ks_name from yw_unit where ks_code="%s"';
        $data = M()->query($sql,$ks_code);

        $this->ajaxReturn($data);
    }

    /**
     * 获取课文中带颜色的字的基础信息
     */
    public function getZi(){
        $zi = I('zi/s','');
        $fy = I('fy/s','');

        // $sql = 'SELECT l.bushou,t.cizu FROM yw_lib_zi l,yw_lib_cixing t WHERE l.zi=t.zi AND l.zi="%s" AND t.py="%s"';
        $sql = 'SELECT l.bushou,m.cizu FROM yw_lib_zi l LEFT JOIN (SELECT t.cizu,t.zi FROM yw_lib_cixing t WHERE t.py="%s") m ON l.zi=m.zi WHERE l.zi="%s"';
        $data = M()->query($sql,$fy,$zi);
        $cizu = str_replace('#','，',$data[0]['cizu']);
        $cizuArr = explode('，',$cizu);
        if(count($cizuArr)>3){
            $cizu = $cizuArr[0].'，'.$cizuArr[1].'，'.$cizuArr[3];
        }
        $data[0]['cizu'] = $cizu;
        $this->ajaxReturn($data);
    }



    public function py(){
        $this->display('py');
    }

    public function word(){
        $this->display('word');
    }


    public function merge_spaces ( $string )
    {
        return preg_replace ( "/\s(?=\s)/","\\1", $string );
    }

    /**
     * 获取当前单元单词信息
     */
    public function getUnitWordInfo(){
        $ks_code = I('ks_code/s','');
         //echo "sss";
        // $unit = D('unit');
        // $data = $unit->getUnitWordInfo($ks_code);

        // $m = M('db_english.rms_unit','engs_');
        $sql = 'select p_id,display_order,style,word,ks_name from yw_unit where ks_code="%s"';
        $data = M()->query($sql,$ks_code);

        $sql_next = 'SELECT ks_code FROM yw_unit WHERE word<>"" AND word is not null AND  p_id="'.$data[0]['p_id'].'" AND display_order>"'.$data[0]['display_order'].'" ORDER BY display_order LIMIT 1;';
        $data_next = M()->query($sql_next);

        if(empty($data_next)){
            $ks_code_next = '';
        }else{
            $ks_code_next = $data_next[0]['ks_code'];
        }

        $str = $data[0]['word'];
        $jsonArr = json_decode($str,true);

        foreach($jsonArr as $k=>$v){
            foreach($v['info'] as $key=>$word){
                $wordArr = split_str($word['word']);
                $pyArr = explode(' ',$this->merge_spaces($word['py']));
                $fyArr = explode(' ',$this->merge_spaces($word['fy']));
                $jsonArr[$k]['info'][$key]['word'] = $wordArr;
                $jsonArr[$k]['info'][$key]['py'] = $pyArr;
                $jsonArr[$k]['info'][$key]['fy'] = $fyArr;
                $jsonArr[$k]['nextkscode'] = $ks_code_next;
            }
        }


        $wordArr = json_decode($data[0]['word'],true);

        // $domain = 'http://en.youjiaotong.com/';
        $domain = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $domain = PROTOCOL.'://'.C('resource_path');
        $domain = str_replace('https','http',$domain);
        $domain = str_replace(':8443',':8080',$domain);
        // $domain = 'http://192.168.133.17/yw/';
        $voiceList = array();
        $playArr = array();
        $k = -1;

        foreach($wordArr as $v){
            foreach($v['info'] as $key=>$fy){
                $arr = explode(' ',$this->merge_spaces($fy['fy']));

                foreach($arr as $vvv){
                    $k++;
                    $info['name'] = $vvv;
                    $info['url'] = $domain.'uploadsyw/zipinyin/'.$vvv.'.mp3';
                    $info['size'] = 10;
                    $info['format'] = 'mp3';
                    $voiceList[$k] = $info;
                }
                // if(count($arr)>1){
                //     $playArr[$v['id']][$key]['info'] = $arr;
                //     $playArr[$v['id']][$key]['type'] = 'ci';
                // }else{
                //     $playArr[$v['id']][$key]['info'] = $arr;
                //     $playArr[$v['id']][$key]['type'] = 'zi';
                // }

            }
        }
        $voiceListJson = json_encode($voiceList);
        $playArrJson = json_encode($playArr);
        $data['voiceList'] = $voiceListJson;
        // $data['playArr'] = $playArrJson;

        $data[0]['word'] = $jsonArr;
        $this->ajaxReturn($data);
    }


    /**
     * 添加用户记录
     * @param [int] $uid         [用户表id]
     * @param [String] $content     [记录内容:字|词|课文标题]
     * @param [int] $contenttype [1:课文;2:字;3:词]
     * @param [int] $type        [1：字词（学习）2：字词（听写）3：课文（学习）4：课文（跟读）]
     */
    public function addUserLog($uid,$content,$contenttype,$type){
        $dealtime = date('Y-m-d H:i:s');

        $m = M('user_log');
        $data['uid'] = session('userName');
        $data['content'] = $content;
        $data['contenttype'] = $contenttype;
        $data['type'] = $type;
        $data['addtime'] = $dealtime;

        $m->add($data);
    }



    public function wordinfo(){
        $this->display('wordinfo');
    }

    /**
     * 获取字的详细信息
     * @return [type] [description]
     */
    public function getWordInfo(){
        $wordinfo = I('wordinfo/s','');

        // $zi = D('LibZi');
        // $data = $zi->getWordInfo($wordinfo);

        // $zi = M('lib_zi');
        // $sql = 'SELECT zi,bushou,jiegou,zongbihua FROM yw_lib_zi l WHERE zi="%s"';
        // $data = $zi->query($sql,$wordinfo);

        // $cixing = M('lib_cixing');
        // $info = array();
        // $info['base'] = $data;
        // foreach($data as $k=>$v){
        //     $sql = 'SELECT zi_pinyin,py,cizu FROM yw_lib_cixing WHERE zi= "%s"';
        //     $re = $cixing->query($sql,$wordinfo);
        //     $info['info'][$k] = $re;
        // }

        // $sql = 'SELECT l.zi,l.bushou,l.jiegou,l.zongbihua,t.zi_pinyin,t.py,t.cizu FROM yw_lib_zi l,yw_lib_cixing t WHERE l.zi=t.zi AND l.zi="%s"';
        $sql = 'SELECT l.zi,l.bushou,l.jiegou,l.zongbihua,l.pys,t.zi_pinyin,t.py,t.cizu,CASE WHEN t.id IS NULL THEN 0 ELSE t.id END as id FROM yw_lib_zi l LEFT JOIN yw_lib_cixing t ON l.zi=t.zi WHERE l.zi="%s"';

        $info = M()->query($sql,$wordinfo);


        $this->ajaxReturn($info);
    }



    /**
     * 获取词的详细信息
     */
    public function getCiInfo(){
        $wordinfo = I('wordinfo/s','');

        $sql = 'SELECT tongyici,fanyici,pys FROM yw_lib_zuci WHERE ci="%s"';
        $data = M()->query($sql,$wordinfo);

        $tongyici = trim($data[0]['tongyici'],'#');
        $fanyici = trim($data[0]['fanyici'],'#');


        $tongArr = array_slice(explode('#',$tongyici),0,3);
        $fanArr = array_slice(explode('#',$fanyici),0,3);


        $data[0]['tongyici'] = $tongArr[0]==""?array():$tongArr;
        $data[0]['fanyici'] = $fanArr[0]==""?array():$fanArr;

        $ciArr = $this->split_str($wordinfo);
        $info['base'] = $data;
        $info['ci'] = $ciArr;
        $this->ajaxReturn($info);
    }


    private function split_str($string, $type='array', $charset='utf-8'){
        //通过ord()函数获取字符的ASCII码值，如果返回值大于 127则表示为中文字符的一半，再获取后一半组合成一个完整字符
        $flag = false;
        if(strtolower($charset) == 'utf-8'){
            //如果utf-8环境
            $flag = true;
            $string = iconv('utf-8', 'gbk', $string);//由于ord函数在gbk下单个中文长度为2，utf-8下长度为3
        }

        if(strtolower($charset) != 'gbk' && strtolower($charset) != 'utf-8')
        {
            exit('参数不合法!');
        }

        //把字符串转化为ascii码存入数组,如果是中文是由两个ASCII码组成，英文是一个
        $length = strlen($string);
        $result = array();
        for($i=0; $i<$length; $i++){
            if(ord($string[$i])>127){
                $result[] = ord($string[$i]).' '.ord($string[++$i]);
            }else{
                $result[] = ord($string[$i]);
            }
        }
        if(strtolower($type) == 'array'){
            //如果返回值要数组
            $str = '';
            foreach($result as $v){
                $isEmpty = strstr($v,' ');
                if(empty($isEmpty)){
                    $tmpstr = chr($v);
                    if($flag){
                        $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                    }
                    $data[] = $tmpstr;
                }else{
                    list($a,$b) = explode(' ',$v);
                    $tmpstr = chr($a).chr($b);
                    if($flag){
                        $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                    }
                    $data[] = $tmpstr;
                }
            }
            return $data;
        }elseif(strtolower($type) == 'string'){
            $data = array();
            foreach($result as $v){
                $isEmpty = strstr($v,' ');
                if(empty($isEmpty)){
                    $str .= chr($v);
                }else{
                    list($a,$b) = explode(' ',$v);
                    $str .= chr($a).chr($b);
                }
            }
            $str=iconv('gbk', 'utf-8', $str);
            return $str;
        }else{
            exit('参数不合法！');
        }
    }


    /**
     * 收藏/删除生字
     */
    public function addUserWord(){
        $ks_code = I('ks_code/s','');
        $type = I('type/s','');
        $word = I('word/s','');
        $py = I('py/s','');
        $fy = I('fy/s','');
        $fy = str_replace('#',' ',$fy);

        $word = trim($word);
        $py = trim($py);
        $fy = trim($fy);

        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');
        $username = cookie('username');

        $data['ks_code'] = $ks_code;
        $data['word'] = $word;
        $data['py'] = $py;
        $data['fy'] = $fy;
        $data['username'] = $username;
        $data['localareacode'] = $localareacode;
        $data['usertype'] = $usertype;

        $m = M('user_word');

        if($type == 'add'){
            $result = $m->where('username="%s" and localareacode="%s" and usertype="%d" and ks_code="%s" and word="%s" and isdel=0',$username,$localareacode,$usertype,$ks_code,$word)->find();
            if(empty($result)){
                $m->add($data);
            }
        }else{
            $data['isdel'] = 1;
            $m->where('username="%s" and localareacode="%s" and usertype="%d" and ks_code="%s" and word="%s"',$username,$localareacode,$usertype,$ks_code,$word)->save($data);
        }
    }


    public function getMyWord(){
        //获取当前听写的单词，并判断是否在复习本中
        $ks_code = I('ks_code/s','');
        $m = M('user_word');

        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');
        $username = cookie('username');

        $data = $m->field('word')->where('username="%s" and localareacode="%s" and usertype="%d" and ks_code="%s" and isdel=0',$username,$localareacode,$usertype,$ks_code)->select();
        $this->ajaxReturn($data);
    }



    //获取复习本信息（当前课本）
    public function getMyBookWord(){
        $ks_code = I('ks_code/s','');
        $sql = 'SELECT p_id,display_order FROM yw_unit WHERE ks_code = "'.$ks_code.'"';
        $data_kscode = M()->query($sql);
        $pid = $data_kscode[0]['p_id'];
        $display = $data_kscode[0]['display_order'];
        $username = cookie('username');


        $sql_next = 'SELECT ks_code FROM yw_unit WHERE word<>"" AND word is not null AND  p_id="'.$pid.'" AND display_order>"'.$display.'" ORDER BY display_order LIMIT 1;';
        $data_next = M()->query($sql_next);

        if(empty($data_next)){
            $ks_code_next = '';
        }else{
            $ks_code_next = $data_next[0]['ks_code'];
        }


        $sql_unit = 'SELECT l.ks_code,t.ks_name FROM (SELECT DISTINCT ks_code FROM yw_user_word WHERE isdel=0 and username="'.$username.'" AND ks_code LIKE "'.$pid.'%") l,yw_unit t WHERE l.ks_code=t.ks_code ORDER BY t.display_order';
        $data_unit = M()->query($sql_unit);

        foreach($data_unit as $k=>$v){
            $sql_word = 'SELECT id,word,py,fy FROM yw_user_word WHERE ks_code = "'.$v['ks_code'].'" and isdel=0 and username="'.$username.'" order by id';
            $info = M()->query($sql_word);

            $data_unit[$k]['nextkscode'] = $ks_code_next;
            foreach($info as $key=>$word){
                $length = mb_strlen($word['word'],'utf8');
                if($length==1){
                    //此处修改,对应的KewenController也需要修改
                    //字
                    $Model = M();
                    $sql_py = 'select cizu from yw_lib_cixing where zi="'.$word['word'].'" and py="'.$word["fy"].'"';
                    $data_py = $Model->query($sql_py);

                    $py_str = '';
                    $py_str .= $word['fy'];

                    if(empty($data_py)){
                        //没有组词
                        $data_unit[$k]['info'][$key]['fayin'] = $this->getpys($py_str,0);
                        $data_unit[$k]['info'][$key]['zuci'] = '';
                    }else{
                        //有组词
                        if(empty(trim($data_py[0]['cizu'],'#'))){
                            //字段为空
                            $data_unit[$k]['info'][$key]['fayin'] = $this->getpys($py_str,0);
                            $data_unit[$k]['info'][$key]['zuci'] = '';
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
                            $py_str .= '|de0|'.$word['fy'];

                            $data_unit[$k]['info'][$key]['fayin'] = $this->getpys($py_str,2);
                            $data_unit[$k]['info'][$key]['zuci'] = $arr_py;
                        }
                    }
                }else{
                    //词
                    $py_str = str_replace(' ','|',trim($word['fy']));
                    $data_unit[$k]['info'][$key]['fayin'] = $this->getpys($py_str,0);
                    $data_unit[$k]['info'][$key]['zuci'] = '';
                }


                $wordArr = split_str($word['word']);
                $pyArr = explode(' ',$word['py']);
                $fyArr = explode(' ',$word['fy']);
                $data_unit[$k]['info'][$key]['bid'] = $word['id'];
                $data_unit[$k]['info'][$key]['word'] = $wordArr;
                $data_unit[$k]['info'][$key]['py'] = $pyArr;
                $data_unit[$k]['info'][$key]['fy'] = $fyArr;
            }

        }

        $this->ajaxReturn($data_unit);
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
    public function delUserWord(){
        //获取当前听写的单词，并判断是否在复习本中
        $id = I('id/d',0);
        $m = M('user_word');

        $data['isdel'] = 1;
        $m->where('id="%d"',$id)->save($data);
        echo 'ok';
    }


    //获取用户配置
    public function getMyConfig(){
        $username = cookie("username");
        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');

        $sql_re = 'select `mode`,speed,`loop` from yw_user_config where username="%s" and localareacode="%s" and usertype="%s"';
        $result = M()->query($sql_re,$username,$localareacode,$usertype);

        if(empty($result)){
            $result['username'] = $username;
            $result['localareacode'] = $localareacode;
            $result['usertype'] = $usertype;
            $result['mode'] = 0;
            $result['speed'] = 2;
            $result['loop'] = 2;
            M('user_config')->add($result);
            $data[0] = $result;
        }else{
            $data = $result;
        }

        $this->ajaxReturn($data);
    }


    public function updateMyConfig(){
        $username = cookie("username");
        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');

        $mode = I('mode/d',0);
        $speed = I('speed/d',0);
        $loop = I('loop/d',0);

        $result['mode'] = $mode;
        $result['speed'] = $speed;
        $result['loop'] = $loop;
        M('user_config')->where('username="%s" and localareacode="%s" and usertype="%s"',$username,$localareacode,$usertype)->save($result);
    }




    //上传录音
    public function uploadVoiceToDb(){
        $username = cookie("username");
        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');
        $areaid = cookie('areacode');
        $ks_code = I('ks_code/s','');
        $path = I('path/s','');
        $filename = I('filename/s','');
        $recordType = I('recordType/d',0);
        $apptype = I('apptype/d',1);
        $isPush = I('isPush/d',0);

        if(empty($filename)){
            $re = M('unit')->field('ks_name')->where('ks_code="%s"',$ks_code)->find();
            $filename = $re['ks_name'];
        }

        // $data['uid'] = $uid;
        $data['username'] = $username;
        $data['localareacode'] = $localareacode;
        $data['usertype'] = $usertype;

        $data['ks_code'] = $ks_code;
        $data['filename'] = $filename;
        $data['filepath'] = $path;
        $data['type'] = $recordType;
        $data['addtime'] = date('Y-m-d H:i:s');
        $data['apptype'] = $apptype;//语文课文

        // M('user_record')->add($data);
        $id = M('db_english.user_record','engs_')->add($data);

        if($this->checkUbIsMax()){
            //送U币
            $c = 'u_textbook_tape_zxzs';
            $n = 1;
            $result = getUbiInterface($c,$n,$username,$localareacode,$areaid); //测试注释
            // $result = true;
            if($result[0]['result'] == 1){
                //送U币成功
                $this->addRecordUbi($id,$isPush);
                $re['status'] = 1;
                $re['msg'] = '上传成功，获得U币';
            }else{
                //调用U币接口失败
                $re['status'] = 2;
                $re['msg'] = '上传成功';
            }
        }else{
                $re['status'] = 1;
                $re['msg'] = '上传成功';
        }
        $this->ajaxReturn($re);
    }

    //检查上传录音当天U币是否达到上线20个
    public function checkUbIsMax(){
        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');

        $today = date('Y-m-d').' 00:00:01';
        $data = M('db_english.user_record','engs_')->where('username="%s" and localareacode="%s" and usertype="%d" and addtime>"%s"',$username,$localareacode,$usertype,$today)->select();

        if(count($data)>20){
            //不送U币
            return false;
        }else{
            //送U币
            return true;
        }
    }

    //删除录音
    public function delVoice(){
        $id = I('id/d',0);
        $filepath = I('filepath/s','');

        M('db_english.user_record','engs_')->where('id="%d"',$id)->setField('isdel',1);
        //M('user_record')->where('id="%d"',$id)->setField("isdel",1);
        unlink('recordwav/'.$filepath);
    }

    //查询录音
    public function selectVoice(){
        $uid = cookie("cn_uid");
        $areacode = cookie("localAreaCode");
        $classid = cookie("classid");
        $schoolid = cookie("schoolid");
        $ks_code = I('ks_code/s','');



        $uid = 82; $areacode = '13.'; $classid = '143571834625682475'; $schoolid = '410101000011';

        //个人录音
        // $sql_my = 'SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount,CASE WHEN (SELECT p.id FROM yw_user_like p WHERE p.uid='.$uid.' AND p.recordid=m.id AND p.isdel=0) IS NULL THEN 0 ELSE 1 END as ispraise FROM yw_user_record m,yw_users n WHERE m.uid=n.id AND m.uid='.$uid.' AND m.isdel=0 AND m.ks_code="'.$ks_code.'" ORDER BY m.addtime DESC';
        $sql_my = 'SELECT l.*,CASE WHEN t.recordid is not NULL THEN 1 ELSE 0 END as ispraise FROM (SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount FROM yw_user_record m,yw_users n WHERE m.uid=n.id AND m.uid='.$uid.' AND m.isdel=0 AND m.ks_code="'.$ks_code.'" ORDER BY m.addtime DESC) l LEFT JOIN (SELECT recordid FROM yw_user_like WHERE uid='.$uid.' AND isdel=0) t ON l.id=t.recordid ';
        $data_my = M()->query($sql_my);

        //同学录音
        // $sql_siblings = "SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount,CASE WHEN (SELECT p.id FROM yw_user_like p WHERE p.uid=".$uid." AND p.recordid=m.id AND p.isdel=0) IS NULL THEN 0 ELSE 1 END as ispraise  FROM yw_user_record m LEFT JOIN yw_users n ON m.uid=n.id WHERE m.isdel=0 AND m.ks_code='%s' AND n.id<>".$uid." AND n.userarea='%s' AND n.schoolid='%s' AND n.classid='%s' ORDER BY m.praise DESC,m.listencount DESC,m.addtime DESC";
        $sql_siblings = "SELECT l.*,CASE WHEN t.recordid is not NULL THEN 1 ELSE 0 END as ispraise FROM (SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount FROM yw_user_record m LEFT JOIN yw_users n ON m.uid=n.id WHERE m.isdel=0 AND m.ks_code='%s' AND n.id<>".$uid." AND n.userarea='%s' AND n.schoolid='%s' AND n.classid='%s' ORDER BY m.praise DESC,m.listencount DESC,m.addtime DESC) l LEFT JOIN (SELECT recordid FROM yw_user_like WHERE uid='%d' AND isdel=0) t ON l.id=t.recordid ";
        $data_siblings = M()->query($sql_siblings,$ks_code,$areacode,$schoolid,$classid);

        //其他人录音
        // $sql_other = "SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount,CASE WHEN (SELECT p.id FROM yw_user_like p WHERE p.uid=".$uid." AND p.recordid=m.id AND p.isdel=0) IS NULL THEN 0 ELSE 1 END as ispraise  FROM yw_user_record m LEFT JOIN yw_users n ON m.uid=n.id WHERE m.isdel=0 AND m.ks_code='%s' AND n.classid<>'%s' ORDER BY m.praise DESC,m.listencount DESC,m.addtime DESC";
        $sql_other = "SELECT l.*,CASE WHEN t.recordid is not NULL THEN 1 ELSE 0 END as ispraise FROM (SELECT m.id,n.username,m.ks_code,m.filename,m.filepath,m.type,m.praise,m.addtime,m.listencount FROM yw_user_record m LEFT JOIN yw_users n ON m.uid=n.id WHERE m.isdel=0 AND m.ks_code='%s' AND n.classid<>'%s' ORDER BY m.praise DESC,m.listencount DESC,m.addtime DESC) l LEFT JOIN (SELECT recordid FROM yw_user_like WHERE uid='%d' AND isdel=0) t ON l.id=t.recordid ";
        $data_other = M()->query($sql_other,$ks_code,$classid);

        $info = array();
        $info['my'] = $data_my;
        $info['siblings'] = $data_other;
        $info['other'] = $data_other;

        $this->ajaxReturn($info);
    }

    //点赞
    public function checkUserLike(){
        $uid = cookie("cn_uid");
        $recordid = I('recordid/d',0);
        $user_like = M('user_like');

        $data = $user_like->where('uid="%d" and recordid="%d"',$uid,$recordid)->find();

        if(empty($data)){
            $data['uid'] = $uid;
            $data['recordid'] = $recordid;
            $user_like->add($data);
            M('user_record')->where('id="%d"',$recordid)->setInc('praise');
        }else{
            if($data['isdel'] == 0){
                $user_like->where('uid="%d" and recordid="%d"',$uid,$recordid)->setField('isdel',1);
                M('user_record')->where('id="%d"',$recordid)->setDec('praise');
            }else{
                $user_like->where('uid="%d" and recordid="%d"',$uid,$recordid)->setField('isdel',0);
                M('user_record')->where('id="%d"',$recordid)->setInc('praise');
            }
        }

        $re = M('user_record')->field('praise')->where('id="%d"',$recordid)->find();
        $this->ajaxReturn($re);
    }

    //试听次数+1
    public function addListenCount(){
        $id = I('id/d',0);
        M('user_record')->where('id="%d"',$id)->setInc('listencount');
    }



    public function tba(){
        $zip=new \ZipArchive();
        $zifile = 'uploadsyw/1.zip';
        if($zip->open($zifile, \ZipArchive::OVERWRITE)=== TRUE){
            $zip->addFile('111.sql');
            $zip->addFile('uploadsyw/start.mp3');
            $zip->close(); //关闭处理的zip文件
        }
    }

    public function xpy(){
        $gradeid=I("gradeid");
        if(!empty($gradeid)&& cookie("gradeid")!=$gradeid){
            cookie("gradeid",$gradeid);
        }
		cookie('subjectid','0001');
        $this->display('xpy');
    }

    //添加学习拼音记录，只记录一次
    public function addPyRecord(){
        $index = I('index/s','');
        $m = M('user_py');
        $username = cookie('username');
        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');
        $data = $m->where('py="%s" and username="%s" and localareacode="%s" and usertype="%d"',$index,$username,$localareacode,$usertype)->find();

        if(empty($data)){
            $info['username'] = $username;
            $info['localareacode'] = $localareacode;
            $info['usertype'] = $usertype;
            $info['py'] = $index;
            $m->add($info);
        }
    }
    //获取当前用户的学习拼音记录
    public function getMyRecord(){
        $m = M('user_py');
        $localareacode = cookie("localAreaCode");
        $usertype = cookie('usertype');
        $username = cookie('username');
        $data = $m->where('username="%s" and localareacode="%s" and usertype="%d"',$username,$localareacode,$usertype)->select();
        $this->ajaxReturn($data);
    }


    public function setTestCookie(){
        $username = '15010100010010';
        $usertype = '3';
        $localareacode = '68.';

        // $username = '41010110067990';
        // $usertype = '4';


        cookie('username', $username);
        cookie('usertype', $usertype);
        cookie('localAreaCode', $localareacode);
    }


    public function setClass(){
        $classid = I('classid/s','');
        cookie('classid', $classid);
    }

    public function totalrank(){
        $localAreaCode = cookie('localAreaCode');
        $this->assign("localAreaCode",$localAreaCode);
        $this->display("totalrank");
    }

    //获取总排行榜
    public function getTotalRank(){
        $apptype = I('apptype/d',1);//1;语文课文，2：英语课文；3：英语单词
        $pageCurrent = I('pageCurrent/d',0);
        $pageSize = I('pageSize/d',0);
        $type = I('type/s','class');//class:班级;school:学校;all:全国
        $time = I('time/s','a');//w:周;m:月；a：总榜

        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');
        $userclassid = cookie('classid');
        $userschoolid = cookie('schoolid');

        //时间范围
        $time["starttime"] = "";
        if($time == 'w'){
            $time = date("Y-m-d",strtotime("-7 day"));
        }else if($time == 'm'){
            $time = date("Y-m-d",strtotime("-30 day"));
        }

        
        //类型选择
        $classid = "";
        $schoolid = "";
        if($type == 'class'){
            $classid = $userclassid;
            $schoolid = "";
        }else if($type == 'school'){
            $classid = "";
            $schoolid = $userschoolid;
        }
        //从redis中获取数据
        $rankService = D("Subject/Rank","Service");
        $result = $rankService -> getUserTextReadingRank($apptype,$classid,$schoolid,$time);
        $my = array();
        $myflag = true;
        foreach($result as $key=>$value){
            $result[$key]["rank"]=($key+1);
            if($username == $value["username"] && $myflag){
                $my = $value;
                $my["rank"]=$key+1;
                $myflag = false;
            }
        }

        $data["info"] = $result;
        //数组的组合和兼容
        $hasRank = true;
        if($my["total"] == 0){
            $hasRank = false;
        }
        $my['mytotal'] = $my["total"];
        $my['myub'] = $my["ub"];
        $my['myRank'] = $my["rank"];
        $data["myinfo"] = $my;
        $this -> ajaxReturn($data);    
    }

    //获取我的排名
    public function getMyRank($usertype,$username,$localareacode,$classid,$apptype,$sql_time,$sql,$sql_where,$sql_group,$sql_order,$type){
        $my = array();

        if($usertype == 4){
            //学生或者家长
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "%d" AND username="%s" and classid="%s"';            
        }else{
            //教师
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "%d" AND username="%s" and classid="%s"';
        }

        $data_user = M()->query($sql_user,$localareacode,$usertype,$username,$classid);
        \Think\Log::record("查询我的排名".M()->getLastSql());

        $my['classname'] = $data_user[0]['classname'];
        $my['truename'] = $data_user[0]['truename'];
        $my['schoolname'] = $data_user[0]['schoolname'];
        $my['userpic'] = $data_user[0]['userpic'];

        //查询我的当前u币数和篇数
        $sql_my = 'SELECT count(m.id) as total,sum(m.ub) as ub FROM db_english.engs_user_record m,db_english.engs_userinfo n';
        if($type == 'all'){
            $sql_my = 'SELECT count(m.id) as total,sum(m.ub) as ub FROM db_english.engs_user_record m';
        }
    
        $sql_my_where = ' and m.username="'.$username.'"';
        // echo $sql_my.$sql_where.$sql_my_where.$sql_time;exit;
        $data_my = M()->query($sql_my.$sql_where.$sql_my_where.$sql_time);//加where条件


        if($data_my[0]['total'] == 0){
            //没有u币
            $hasRank = false;
            $mytotal = 0;
            $myub = 0;
        }else{
            $myub = $data_my[0]['ub'];
            $mytotal = $data_my[0]['total'];
            $hasRank = true;
        }

        $my['hasRank'] = $hasRank;
        $my['mytotal'] = $mytotal;
        $my['myub'] = $myub;


        //获取我的当前排名
        if($hasRank){
            //如果U币和篇数都相同，就按名字排序
            $sql_having_same = ' having sum(m.ub)='.$myub.' and count(m.id)='.$mytotal;
            $data_is_same = M()->query($sql.$sql_where.$sql_group.$sql_having_same.$sql_order);
            if(empty($data_is_same)){
                //没有相同的
                $is_same = false;
            }else{
                //都相同按名字
                $is_same = true;
                $arr_index = array_column($data_is_same,'username');
                $index = array_search($username, $arr_index);
            }

            $sql_having = ' having sum(m.ub)>'.$myub.' or (sum(m.ub)='.$myub.' and count(m.id)>'.$mytotal.')';
            $sql_my_rank = $sql.$sql_where.$sql_group.$sql_having;
            $data_my_rank = M()->query($sql_my_rank);
            if(empty($data_my_rank)){
                if($is_same){
                    $myrank = 1+$index;
                }else{
                    $myrank = 1;
                }
            }else{
                if($is_same){
                    $myrank = count($data_my_rank)+1+$index;
                }else{
                    $myrank = count($data_my_rank)+1;
                }
            }
        }else{
            $myrank = 0;
        }

        $my['myRank'] = $myrank;

        return $my;
    }

    //查询是否有多个班级
    public function getClassList(){
        $username = cookie('username');
        $localareacode = cookie('localAreaCode');
        $usertype = cookie('usertype');
        // cookie('classid',null);
        //只有老师有可能有多个班级
        $sql_class = 'SELECT classid,classname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "%d" AND username="%s" ';
        $data_class = M()->query($sql_class,$localareacode, $usertype,$username);
        
        $classid = cookie('classid');
        if(count($data_class)>1){
            //有多个班级
            $data['isShow'] = true;
            $data['classList'] = $data_class;
        }else{
            $data['isShow'] = false;
        }
        $data['classid']= $classid;
        $this->ajaxReturn($data);
    }

    public function recordrank(){
        $ks_code = I('ks_code/s','');
        $moduleid = I('moduleid/d',0);
        $this->resetPushNum($ks_code);
        $arr = getmodule($moduleid);
        $modulename = $arr['title'];
        $modulepic = $arr['style'];
        $this->assign('localAreaCode',cookie('localAreaCode'));
        $this->assign('modulename',$modulename);
        $this->assign('modulepic',$modulepic);
        $this->display();
    }

   //获取我的录音的排行榜
    public function getRecordRank(){
        $apptype = I('apptype/d',1);//1;语文课文，2：英语课文；3：英语单词
        $pageCurrent = I('pageCurrent/d',0);
        $pageSize = I('pageSize/d',0);
        $type = I('type/s','class');//my:我的;class:班级;all:全国
        $ks_code = I('ks_code/s','');

        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');

        //默认从cookie取classid和schoolid
        if(empty(cookie('classid'))){
            if($usertype == 4){
                //学生或者家长
                $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "%d" AND username="%s" AND localareacode="%s"';                
            }else{
                //教师
                $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND usertype = "%d" AND username="%s" AND localareacode="%s" ';
            }
            $data_user = M()->query($sql_user,$usertype,$username,$localareacode);
            $schoolid = $data_user[0]['schoolid'];
            $classid = $data_user[0]['classid'];
        }else{
            $schoolid = cookie('schoolid');
            $classid = cookie('classid');
        }

        //合并成一条SQL
        if($type == 'my'){
            //我的录音
            $sql_list_left = '';
            $sql_list_left .= ' SELECT a.*, CASE WHEN b.recordid IS NULL THEN 0 ELSE 1 END ispay,1 playstatus FROM ';
            $sql_list_left .= ' ( ';
            $sql_list_left .= ' SELECT m.id,m.filename,m.filepath,m.ub,m.listencount,m.sharecount,m.ubupdatetime,m.type,n.truename,n.userpic,n.username,n.localareacode,n.areaid FROM ';
            $sql_list_left .= ' (SELECT id,username,localareacode,usertype,filename,filepath,ub,listencount,sharecount,ubupdatetime,type FROM db_english.engs_user_record WHERE isdel= 0 AND apptype="'.$apptype.'" AND ks_code="'.$ks_code.'" AND localareacode = "'.$localareacode.'" and username = "'.$username.'" ) m,';
            $sql_list_left .= ' (SELECT truename,userpic,username,localareacode,usertype,classid,areaid FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode = "'.$localareacode.'" and username = "'.$username.'" limit 1 ) n ';
            $sql_list_left .= ' WHERE m.username = n.username AND m.localareacode = n.localareacode  ';
            $sql_list_left .= ' ORDER BY m.ub DESC,m.ubupdatetime ';
            $sql_list_limit .= ' limit '.($pageCurrent-1)*$pageSize.','.$pageSize ;
            $sql_list_right .= ' ) a LEFT JOIN (SELECT recordid FROM db_english.engs_ub_record  WHERE fusername="'.$username.'" and type=1) b ON a.id=b.recordid ';
        }else if($type == 'class'){
            $sql_list_left = '';
            $sql_list_left .= ' SELECT a.*, CASE WHEN b.recordid IS NULL THEN 0 ELSE 1 END ispay,1 playstatus FROM ';
            $sql_list_left .= ' ( ';
            $sql_list_left .= ' SELECT m.id,m.filename,m.filepath,m.ub,m.listencount,m.sharecount,m.ubupdatetime,m.type,n.truename,n.userpic,n.username,n.localareacode,n.areaid FROM ';
            $sql_list_left .= ' (SELECT id,username,localareacode,usertype,filename,filepath,ub,listencount,sharecount,ubupdatetime,type FROM db_english.engs_user_record WHERE isdel= 0 AND apptype="'.$apptype.'" AND ks_code="'.$ks_code.'" AND localareacode = "'.$localareacode.'"  ) m,';
            $sql_list_left .= ' (SELECT truename,userpic,username,localareacode,usertype,classid,areaid FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode = "'.$localareacode.'" and classid="'.$classid.'" GROUP BY username ) n ';
            $sql_list_left .= ' WHERE m.username = n.username AND m.localareacode = n.localareacode  ';
            $sql_list_left .= ' ORDER BY m.ub DESC,m.ubupdatetime ';
            $sql_list_limit .= ' limit '.($pageCurrent-1)*$pageSize.','.$pageSize ;
            $sql_list_right .= ' ) a LEFT JOIN (SELECT recordid FROM db_english.engs_ub_record  WHERE fusername="'.$username.'" and type=1 ) b ON a.id=b.recordid ';
        }else if($type == 'all'){
            $sql_list_left = '';
            $sql_list_left .= ' SELECT a.*, CASE WHEN b.recordid IS NULL THEN 0 ELSE 1 END ispay,1 playstatus FROM ';
            $sql_list_left .= ' ( ';
            $sql_list_left .= ' SELECT m.id,m.filename,m.filepath,m.ub,m.listencount,m.sharecount,m.ubupdatetime,m.type,m.username,m.localareacode FROM ';
            $sql_list_left .= ' (SELECT id,username,localareacode,usertype,filename,filepath,ub,listencount,sharecount,ubupdatetime,type FROM db_english.engs_user_record WHERE isdel= 0 AND apptype="'.$apptype.'" AND ks_code="'.$ks_code.'" ) m ';
            $sql_list_left .= ' ORDER BY m.ub DESC,m.ubupdatetime ';
            $sql_list_limit .= ' limit '.($pageCurrent-1)*$pageSize.','.$pageSize ;
            $sql_list_right .= ' ) a LEFT JOIN (SELECT recordid FROM db_english.engs_ub_record  WHERE fusername="'.$username.'" and type=1 ) b ON a.id=b.recordid ';
        }


        //总数据
        //查询总篇数
        $sql_total = $sql_list_left.$sql_list_right;
        $data_total = M()->query($sql_total);
        $total = count($data_total);
        $data['total'] = $total;


        //当前分页数据
        $sql_list = $sql_list_left.$sql_list_limit.$sql_list_right;
        // echo $sql_list;exit;//打印SQL语句
        $sql_rank_beg = 'SELECT tmp.*,@rank :=@rank+1 as rank FROM ((';
        $sql_rank_end = ') tmp,(SELECT @rank :='.($pageCurrent-1)*$pageSize.') b)';

        $data_info = M()->query($sql_rank_beg.$sql_list.$sql_rank_end);
       // echo $sql_rank_beg.$sql_list.$sql_rank_end;exit;
        // $this->writeSqlLog();
        

        if($type=='all'){
            foreach($data_info as $k=>$v){
                $sql_user = 'select  truename,userpic,username,areaid from db_english.engs_userinfo where isdel=0 and  username="'.$v['username'].'" and localareacode="'.$v['localareacode'].'" limit 1';
                $resuser = M()->query($sql_user);
                $data_info[$k]['truename'] = $resuser[0]['truename'];
                $data_info[$k]['userpic'] = $resuser[0]['userpic'];
                $data_info[$k]['areaid'] = $resuser[0]['areaid'];
            }
        }

        //var_dump($data_info);exit;

        if($pageCurrent>1){
            $data['myinfo'] = '';
        }else{
            if($type == 'my'){
                $data['myinfo'] = '';
            }else{
                $data['myinfo'] = $this->getMyRankRecord($usertype,$username,$localareacode,$classid,$apptype,$type,$ks_code);

            }
        }
        $data['info'] = $data_info;
        $this->ajaxReturn($data);

    }

    //获取我的录音的排行
    public function getMyRankRecord($usertype,$username,$localareacode,$classid,$apptype,$type,$ks_code){
        $my = array();

        if($usertype == 4){
            //学生或者家长
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname,localareacode,areaid FROM db_english.engs_userinfo WHERE isdel=0  AND username="%s" AND localareacode="%s" and classid="%s"';   
        }else{
            //教师
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname,localareacode,areaid FROM db_english.engs_userinfo WHERE isdel=0  AND username="%s" AND localareacode="%s" and classid="%s"';
        }

        $data_user = M()->query($sql_user,$username,$localareacode,$classid);

       // echo M()->getLastSql();exit;

        $my['truename'] = $data_user[0]['truename'];
        $my['schoolname'] = $data_user[0]['schoolname'];
        $my['userpic'] = $data_user[0]['userpic'];
        $my['localareacode'] = $data_user[0]['localareacode'];
        $my['areaid'] = $data_user[0]['areaid'];
        $my['username'] = $username;

        //查询我的当前录音最高的u币数
        $sql_my = 'SELECT ub,listencount,sharecount,id,filepath,filename,type FROM db_english.engs_user_record WHERE  isdel = 0 AND apptype = "'.$apptype.'" AND username="'.$username.'" AND localareacode = "'.$localareacode.'" AND usertype = "'.$usertype.'" and ks_code="'.$ks_code.'" order by ub desc,ubupdatetime asc limit 1';
        $data_my = M()->query($sql_my);
        // echo $sql_my;exit;
        // var_dump($data_my);exit;

        if(empty($data_my[0]['id'])){
            //没有u币(没有录音)
            $hasRank = false;
            $mylistencount = 0;
            $mysharecount = 0;
            $myub = 0;
            $id = 0;
            $my['ispay'] = 0;
        }else{
            $my['filepath'] = $data_my[0]['filepath'];
            $my['filename'] = $data_my[0]['filename'];
            $my['type'] = $data_my[0]['type'];
            $myub = $data_my[0]['ub'];
            $mylistencount = $data_my[0]['listencount'];
            $mysharecount = $data_my[0]['sharecount'];
            $hasRank = true;
            $id = $data_my[0]['id'];

            //查询是否给自己送过U币
            $sql_ispay = 'select id from db_english.engs_ub_record where fusername="%s" and recordid="%d"';
            $data_pay = M()->query($sql_ispay,$username,$id);
            if(empty($data_pay)){
                //没送过
                $my['ispay'] = 0;
            }else{
                $my['ispay'] = 1;
            }
        }


        $my['hasRank'] = $hasRank;
        $my['mylistencount'] = $mylistencount;
        $my['mysharecount'] = $mysharecount;
        $my['myub'] = $myub;
        $my['id'] = $id;
        $my['playstatus'] = 1;

        if($type == 'class'){
            $sql_warn = 'and classid="'.$classid.'" ';
        }else{
            $sql_warn = '';
        }

        $sql  = ' SELECT m.id,m.filename,m.filepath,m.ub,m.listencount,m.sharecount,m.ubupdatetime,n.truename,n.userpic,n.username,n.localareacode,n.areaid FROM ( ';
        $sql .= ' SELECT id,username,localareacode,usertype,filename,filepath,ub,listencount,sharecount,ubupdatetime FROM db_english.engs_user_record WHERE isdel= 0 AND apptype="'.$apptype.'" AND ks_code="'.$ks_code.'") m,';
        $sql .= ' (SELECT truename,userpic,username,localareacode,usertype,classid,areaid FROM db_english.engs_userinfo WHERE isdel=0 '.$sql_warn.' GROUP BY username,localareacode,usertype)n ';

        $sql_where = '';
        $sql_where .= ' WHERE m.username = n.username AND m.localareacode = n.localareacode AND m.usertype = n.usertype';
        if($type == 'class'){
            $sql_where .= ' AND n.localareacode = "'.$localareacode.'" and n.classid="'.$classid.'" ';
        }else if($type == 'my'){
            $sql_where .= ' AND n.localareacode = "'.$localareacode.'" and n.username = "'.$username.'" ';
        }else if($type == 'all'){
            $sql_where .= '';
        }

        $sql_order = ' ORDER BY m.ub DESC,m.ubupdatetime ';

        //获取我的当前排名
        if($hasRank){
            //如果U币都相同，就按名字排序
            $sql_same = ' and m.ub='.$myub;
            // echo $sql.$sql_where.$sql_same.$sql_order;exit;
			if($type == 'all'){
				$sql_all_same = 'SELECT id, username, ub FROM db_english.engs_user_record WHERE isdel = 0 AND apptype = "'.$apptype.'" AND ks_code = "'.$ks_code.'"  and ub="'.$myub.'" ORDER BY ub DESC,ubupdatetime';
	            $data_is_same = M()->query($sql_all_same);
			}else{
				$data_is_same = M()->query($sql.$sql_where.$sql_same.$sql_order);				
			}

            if(empty($data_is_same)){
                //没有相同的
                $is_same = false;
            }else{
                //都相同按名字
                $is_same = true;
                $arr_index = array_column($data_is_same,'username');
                $index = array_search($username, $arr_index);
            }

            //查询比我大点有多少人
            $sql_max= ' and m.ub>'.$myub;
            $sql_my_rank = $sql.$sql_where.$sql_max;
            // echo $sql_my_rank;exit;

			if($type == 'all'){
				$sql_all_rank = 'SELECT id, username, ub FROM db_english.engs_user_record WHERE isdel = 0 AND apptype = "'.$apptype.'" AND ks_code = "'.$ks_code.'"  and ub>"'.$myub.'"';
	            $data_my_rank = M()->query($sql_all_rank);
			}else{
				$data_my_rank = M()->query($sql_my_rank);		
			}
            
            if(empty($data_my_rank)){
                if($is_same){
                    $myrank = 1+$index;
                }else{
                    $myrank = 1;
                }
            }else{
                if($is_same){
                    $myrank = count($data_my_rank)+1+$index;
                }else{
                    $myrank = count($data_my_rank)+1;
                }
            }
        }else{
            $myrank = 0;
        }

        $my['myRank'] = $myrank;

        return $my;
    }


    //试听次数+1
    public function addRecordListenCount(){
        $id = I('id/d',0);
        $tusername = I('tusername/s','');
        $fusername = cookie('username');
        if($tusername != $fusername){
            $this->addPushNum($id);
        }
        M('db_english.user_record','engs_')->where('id="%d"',$id)->setInc('listencount');
    }
 

    //删除录音
    public function delRecord(){
        $id = I('id/d',0);
        $filepath = I('filepath/s','');

        M('db_english.user_record','engs_')->where('id="%d"',$id)->setField('isdel',1);
        // unlink('recordwav/'.$filepath);
    }

    //分享加1
    public function addRecordShareCount(){
        $id = I('id/d',0);
        M('db_english.user_record','engs_')->where('id="%d"',$id)->setInc('sharecount');
        $this->addPushNum($id);
    }

    //U币加1
    public function addRecordUbi($id,$isPush){
        $date = date('Y-m-d H:i:s');
        M('db_english.user_record','engs_')->where('id="%d"',$id)->setInc('ub');
        M('db_english.user_record','engs_')->where('id="%d"',$id)->setField("ubupdatetime",$date);

        if($isPush == 1){
            $this->addPushNum($id);
        }
        $data['status'] = 1;
    }

    public function writeSqlLog(){
        $sql = M()->getLastSql();
        file_put_contents('zb_log.log',$sql,FILE_APPEND);
    }

    //推送加1
    public function addPushNum($id){
        M('db_english.user_record','engs_')->where('id="%d"',$id)->setInc('pushnum');
    }

    //查询推送数量
    public function getPushNum(){
        $ks_code = I('ks_code/s','');
        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');

        $num = M('db_english.user_record','engs_')->where('isdel=0 and localareacode="%s" and usertype="%d" and username="%s" and ks_code="%s"',$localareacode,$usertype,$username,$ks_code)->sum('pushnum');
        if(empty($num)){
            $num = 0;
        }
        $data['pushnum'] = $num;
        $this->ajaxReturn($data);
    }

    //重置pushnum
    public function resetPushNum($ks_code){

        $username = cookie('username');
        $usertype = cookie('usertype');
        $localareacode = cookie('localAreaCode');
        M('db_english.user_record','engs_')->where('isdel=0 and localareacode="%s" and usertype="%d" and username="%s" and ks_code="%s"',$localareacode,$usertype,$username,$ks_code)->setField('pushnum',0);
    }


    //先查看是否给某人送过U币
    public function checkIsPayUbForUser(){
        $id = I('id/d',0);
        $type = I('type/d',0);
        $tusername = I('tusername/s','');
        $tlocalAreaCode = I('tlocalAreaCode/s','my');
        $tareaid = I('tareaid/s','my');
        $fusername = cookie('username');
        if($tusername == $fusername){
            $isPush = 0;
        }else{
            $isPush = 1;
        }
        
        if($type == 1){
            //点赞送U币
            $c = 'u_voice_like_zxzs';
            $n = 1;
            $data = M('db_english.ub_record','engs_')->where('fusername="%s" and tusername="%s" and recordid="%d" and ub=1 and type="%d"',$fusername,$tusername,$id,$type)->select();
            if(empty($data)){
                //以前没送过U币
                $result = getUbiInterface($c,$n,$tusername,$tlocalAreaCode,$tareaid); //测试注释
                // $result = true;
                if($result[0]['result'] == 1){
                    //送U币成功
                    $this->addRecordUbi($id,$isPush);
                    $this->addUbiLog($fusername,$tusername,$id,$type);
                    $re['status'] = 1;
                    $re['msg'] = 'OK';
                }else{
                    //调用U币接口失败
                    $re['status'] = 2;
                    $re['msg'] = $result[0]['info'];
                }
            }else{
                //送过了不能再送了
                $re['status'] = 0;
                $re['msg'] = '每个录音只能送一次优币';
            }
        }else{
            //分享送U币
            $c = 'u_share_zxzs';
            $n = 1;
            //判断今天转发是否达到30次
            $today = date('Y-m-d').' 00:00:01';
            $data = M('db_english.ub_record','engs_')->where('fusername="%s" and tusername="%s" and recordid="%d" and ub=1 and type="%d" and addtime>="%s"',$fusername,$tusername,$id,$type,$today)->select();

            if(count($data)>20){
                //不送U币
                $re['status'] = 0;
                $re['msg'] = '今日转发送优币已达上线,不再送优币';
            }else{
                //送U币
                $result = getUbiInterface($c,$n,$tusername,$tlocalAreaCode,$tareaid); //测试注释
                if($result[0]['result']== 1){
                    $re['status'] = 1;
                    $re['msg'] = 'ok';
                    $this->addRecordUbi($id,$isPush);
                    $this->addUbiLog($fusername,$tusername,$id,$type);
                }else{
                    //调用U币接口失败
                    $re['status'] = 2;
                    $re['msg'] = $result[0]['info'];
                }
            }
        }


        $this->ajaxReturn($re);
    }


    //添加U币日志
    public function addUbiLog($fusername,$tusername,$id,$type){
        $data['fusername'] = cookie('username');
        $data['tusername'] = $tusername;
        $data['recordid'] = $id;
        $data['ub'] = 1;
        $data['addtime'] = date('Y-m-d H:i:s');
        $data['type'] = $type;
        M('db_english.ub_record','engs_')->add($data);
    }

    public function testub(){
        $c = I('c/s','');
        $n = I('n/d',0);
        $username = I('username/s','');
        $ut = I('ut/d',0);
        $ac = I('ac/s','');
        $ubip = I('ubip/s','');
        // $c = 'u_textbook_tape_zxzs';
        // $n = 1;
        $this->getUbiInterface($c,$n,$username,$ut,$ac,$ubip);
    }
    //调用U币接口
    public function getUbiInterface($c,$n,$username,$ut,$ac,$ubip){
      $ad = time() . mt_rand(1000,9999);  //唯一值
      $at = 'u';    //j（代表）、u（代表优币）、z（代表直接扣除或者增加多少优币）
    //   $a = cookie('username');  //帐号
    //   $ut = cookie('usertype'); //帐号类型
    //   $c = 'u_voice_like_zxzs';    //动作编码
    //   $c = 'u_kousuan1';
    //   $ac = cookie('areacode'); //区域(不是LocalAreaCode)

    //  $a = '15010110023719';
    //  $ut = '4';
    //  $ac = '23.';

     $a = $username;

    //   $n = 1;//数量
      $t = date('Y-m-d H:i:s');
      $r = 'ss';
      $i = $c;
      $parm = '{"ad":"'.$ad.'","at":"'.$at.'","a":"'.$a.'","ut":"'.$ut.'","c":"'.$c.'","ac":"'.$ac.'","n":"'.$n.'","t":"'.$t.'","r":"'.$r.'","i":"'.$i.'"}';

    //   $uburl = 'http://'.cookie('ub_url');
    //   $uburl = 'http://116.114.23.94';
    $uburl = 'http://'+$ubip;
      $uburl=$uburl.'/ub/interface/data_report.jsp?p='.urlencode($parm);

      $uburl=str_replace('%7B', '{', $uburl);
      $uburl=str_replace('%7D', '}', $uburl);

      echo $uburl;exit;
      

      $curl = curl_init($uburl);
          curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
          curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果

          $responseText = curl_exec($curl);
          //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
          curl_close($curl);

          //$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

          $result = json_decode($responseText, true);
          // var_dump($result);exit;

          $issuc=$result[0]['result'];
          if($issuc==1){
           // echo '添加成功';
            $data = true;
          }else{
            $data = false;
           // echo '添加失败';
          }
        return $data;
    }


    public function test1json(){
        $json = '{"myinfo":{"classname":"\u4e00\u5e74\u7ea7\uff081\uff09\u73ed","truename":"\u6d4b\u8bd54001\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8","schoolname":"\u6d4b\u8bd5\u5b66\u6821","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","hasRank":true,"mytotal":"-1","myub":"3","myRank":1},"info":[{"total":"-1","ub":"3","ubupdatetime":"2018-02-28 01:09:55","truename":"\u6d4b\u8bd54001\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","classname":"\u4e00\u5e74\u7ea7\uff081\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"41010110067990","rank":"1"},{"total":"-1","ub":"2","ubupdatetime":"2018-02-08 10:44:37","truename":"\u738b\u65b9\u5bb9","userpic":"http:\/\/vfs.youjiaotong.com\/tms\/upload\/user\/student\/20160503\/5\/808810009215.jpg?r=1213","classname":"\u4e00\u5e74\u7ea7\uff081\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"808810009215","rank":"2"},{"total":"-1","ub":"1","ubupdatetime":"2018-03-05 17:36:01","truename":"\u6d4b\u8bd541\u73ed2","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","classname":"\u4e00\u5e74\u7ea7\uff081\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"41010110054500","rank":"3"}]}';
        echo $json;
    }

    public function test2json(){
        $json = '{"myinfo":{"classname":"\u4e00\u5e74\u7ea7\uff081\uff09\u73ed","truename":"\u6d4b\u8bd54001\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u6d4b\u8bd5\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8\u54c8","schoolname":"\u6d4b\u8bd5\u5b66\u6821","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","hasRank":false,"mytotal":-1,"myub":0,"myRank":0},"info":[{"total":"-1","ub":"10","ubupdatetime":"2018-03-05 17:42:33","truename":"\u6d4b\u8bd5\u6559\u5e08241","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","classname":"\u9ad8\u4e00\uff081\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"41010100010241","rank":"1"},{"total":"-1","ub":"7","ubupdatetime":"2018-03-05 17:43:25","truename":"\u6d4b\u8bd5\u833c\u84bf","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","classname":"\u4e09\u5e74\u7ea7\uff0812\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"41010110307199","rank":"2"},{"total":"-1","ub":"5","ubupdatetime":"2018-03-08 00:53:22","truename":"\u9a6c\u6811\u5168","userpic":"http:\/\/www.youjiaotong.com\/space\/images\/default.png","classname":"\u9ad8\u4e8c\uff082\uff09\u73ed","schoolname":"\u6d4b\u8bd5\u5b66\u6821","username":"41010110302230","rank":"3"}]}';
        echo $json;
    }


}