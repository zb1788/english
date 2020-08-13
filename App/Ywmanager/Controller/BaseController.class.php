<?php
namespace Ywmanager\Controller;
use Think\Controller;
class BaseController extends CheckController {

    //查找汉字及展示
    public function ziinfo(){
        $szi = I("szi");
        $Z = M("lib_zi"); // 实例化User对象
        // 查找status值为1name值为think的用户数据
        $data = $Z->where('zi="%s"', $szi)->find();
        $re = array();
        if (empty($data)) {
            $re["iserr"] = 1;
        }
        else
        {
            $re["iserr"] = 0;
            $re["ziinfo"] = $data;
        }
        $this->ajaxReturn($re);
    }

    /**
     * 获取当前字的组词
     */
    public function ziyininfo(){
        $zi = I("zi/s",'');
        $Z = M("lib_cixing"); // 实例化User对象
        $data = $Z->where('zi="%s"', $zi)->select();
        $this->ajaxReturn($data);
    }

    /**
     * 字新增/编辑页面
     */
    public function ziyinedit(){
        $zi = I('zi/s','');
        $id = I('id/d',0);

        $m = M("lib_cixing");
        $data = $m->where('id="%d"',$id)->find();

        $cizu = $data['cizu'];
        $arr = explode('#',$cizu);
        $re[0] = $arr[0];
        $re[1] = $arr[1];
        $re[2] = $arr[2];

        $data['zuci'] = $re;
        $this->assign('zi',$zi);
        $this->assign('id',$id);
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 保存生字发音笔画偏旁等基本信息
     */
    public function zisave(){
        $data = I("data");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);

        $zi = $arr["zi"] ;
        $id =$arr["id"] ;
        $pianpang =$arr["pianpang"] ;
        $bihuashu =$arr["bihuashu"] ;
        $bushouwai =$arr["bushouwai"] ;
        $jiegou =$arr["jiegou"] ;

        $re = array();
        $m = M('lib_zi');

        $info['bushou'] = $pianpang;
        $info['jiegou'] = $jiegou;
        $info['zongbihua'] = $bihuashu;
        $info['buwaibihua'] = $bushouwai;

        $m->where('id="%d"',$id)->save($info);

        A('Ywmanager/User')->recordUserOption('lib_zi','update','id='.$id);
    }


    /**
     * 保存字音和组词
     */
    public function ziyinsave(){
        $data = I("data");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        $zi = $arr["zi"] ;
        $id =$arr["id"] ;
        $pinyin =$arr["pinyin"] ;


        $re = array();

        $ciArrays[0] = $arr['zuci1'];
        $ciArrays[1] = $arr['zuci2'];
        $ciArrays[2] = $arr['zuci3'];

        $model = M();
        for($i=0;$i<count($ciArrays);$i++){
            if($ciArrays[$i]==''){
                continue;
            }
            $sql = 'select id from yw_lib_zuci where ci ="'.$ciArrays[$i].'"';
            $result = $model->query($sql);
            if(empty($result)){
                $re["errcode"] = 3;
                $re["msg"] = '词库没有['.$ciArrays[$i].'],请先去词库添加';
                $this->ajaxReturn($re);
            }
        }


        if($id == 0){
            //新增
            $m = M('lib_cixing');
            $info = $this->checkPinYin($pinyin);
            if($info['flag']){
                //匹配成功
                $py = $info['fy'];
            }else{
                $re["errcode"] = 2;
                $re["msg"] = '拼音匹配失败';
                $this->ajaxReturn($re);
            }
            $data = $m->where('zi="%s" and py="%s"',$zi,$py)->find();
            if(!empty($data)){
                $re["errcode"] = 1;
                $re["msg"] = '已存在';
                $this->ajaxReturn($re);
            }else{
                $cizu = $arr['zuci1'].'#'.$arr['zuci2'].'#'.$arr['zuci3'];
                $cizu = rtrim($cizu,'##');
                $cizu = rtrim($cizu,'#');

                $res['zi'] = $zi;
                $res['zi_pinyin'] = $pinyin;
                $res['zi_py'] = $arr['wupinyin'];
                $res['cizu'] = $cizu;
                $info = $this->checkPinYin($pinyin);
                if($info['flag']){
                    //匹配成功
                    $res['py'] = $info['fy'];
                    $addid = $m->add($res);

                    A('Ywmanager/User')->recordUserOption('lib_cixing','add','id='.$addid);

                    $re["errcode"] = 0;
                    $re["msg"] = 'ok';
                    $this->ajaxReturn($re);
                }else{
                    $re["errcode"] = 2;
                    $re["msg"] = '拼音匹配失败';
                    $this->ajaxReturn($re);
                }
            }
        }else{
            //更新
            $m = M('lib_cixing');
            $cizu = $arr['zuci1'].'#'.$arr['zuci2'].'#'.$arr['zuci3'];
            $cizu = rtrim($cizu,'##');
            $cizu = rtrim($cizu,'#');

            $res['zi'] = $zi;
            $res['zi_pinyin'] = $pinyin;
            $res['zi_py'] = $arr['wupinyin'];
            $res['cizu'] = $cizu;
            $info = $this->checkPinYin($pinyin);
            if($info['flag']){
                //匹配成功
                $res['py'] = $info['fy'];
                $m->where('id="%d"',$id)->save($res);

                A('Ywmanager/User')->recordUserOption('lib_cixing','update','id='.$id);

                $re["errcode"] = 0;
                $re["msg"] = 'ok';
                $this->ajaxReturn($re);
            }else{
                $re["errcode"] = 2;
                $re["msg"] = '拼音匹配失败';
                $this->ajaxReturn($re);
            }
        }
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





    /**
     * 获取组词
     */
    public function ciinfo(){
        $ci = I("ci/s",'');
        $Z = M("lib_zuci"); // 实例化User对象
        $data = $Z->where('ci="%s"', trim($ci))->find();
        $re = array();
        if (empty($data)) {
            $re["iserr"] = 1;
        }
        else
        {
            $arr = json_decode($data['pys'],true);

            $py = '';
            $fy = '';
            foreach($arr['zy'] as $v){
                    $py .= $v['y'].' ';
                    $fy .= $v['v'].'.mp3|';
            }

            $data['pinyin'] = rtrim($py,' ');
            $data['voice'] = rtrim($fy,'|');
            $data['tongyici'] = str_replace('#',' ',trim($data['tongyici'],'#'));
            $data['fanyici'] = str_replace('#',' ',trim($data['fanyici'],'#'));
            $re["iserr"] = 0;
            $re["ciinfo"] = $data;
        }
        $this->ajaxReturn($re);
    }


    /**
     * 词新增/编辑页面
     */
    public function ciyinedit(){
        $id = I('id/d',0);
        $ci = I('ci/s','');

        $m = M("lib_zuci");
        $data = $m->where('id="%d"',$id)->find();

        $tongyici = $data['tongyici'];
        $fanyici = $data['fanyici'];

        $data['tong'] = $this->chaifen($tongyici);
        $data['fan'] = $this->chaifen($fanyici);

        $arr = json_decode($data['pys'],true);
        $py = '';
        foreach($arr['zy'] as $v){
                $py .= $v['y'].'#';
        }

        $data['py'] = rtrim($py,'#');


        $this->assign('id',$id);
        $this->assign('ci',$ci);
        $this->assign('data',$data);
        $this->display();
    }


    private function chaifen($str){
        $str = trim($str, '#');
        $arr = explode('#',$str);
        $re[0] = $arr[0];
        $re[1] = $arr[1];
        $re[2] = $arr[2];

        return $re;
    }


    /**
     * 保存词
     */
    public function cisave(){
        $data = I("data");
        $arr = json_decode(str_replace("&quot;", '"', $data),ture);
        $ci = $arr["ci"] ;
        $id =$arr["id"] ;
        $pinyin =$arr["pinyin"] ;
        $jinyi1 =$arr["jinyi1"] ;
        $jinyi2 =$arr["jinyi2"] ;
        $jinyi3 =$arr["jinyi3"] ;
        $fanyi1 =$arr["fanyi1"] ;
        $fanyi2 =$arr["fanyi2"] ;
        $fanyi3 =$arr["fanyi3"] ;

        $re = array();

        $ciArrays[0] = $jinyi1;
        $ciArrays[1] = $jinyi2;
        $ciArrays[2] = $jinyi3;
        $ciArrays[3] = $fanyi1;
        $ciArrays[4] = $fanyi2;
        $ciArrays[5] = $fanyi3;

        $model = M();
        for($i=0;$i<count($ciArrays);$i++){
            if($ciArrays[$i]==''){
                continue;
            }
            $sql = 'select id from yw_lib_zuci where ci ="'.$ciArrays[$i].'"';
            $result = $model->query($sql);
            if(empty($result)){
                $re["errcode"] = 3;
                $re["msg"] = '词库没有['.$ciArrays[$i].'],请先去词库添加';
                $this->ajaxReturn($re);
            }
        }


        $pinyin = trim($pinyin);
        $json = $this->chaifenStr($pinyin);

        if($id == 0){
            //新增
            $m = M('lib_zuci');

            $data = $m->where('ci="%s" ',$ci)->find();
            if(!empty($data)){
                $re["errcode"] = 1;
                $re["msg"] = '已存在';
            }else{
                $tongyici = $arr['jinyi1'].'#'.$arr['jinyi2'].'#'.$arr['jinyi3'];
                $tongyici = rtrim($tongyici,'##');
                $tongyici = rtrim($tongyici,'#');
                $fanyici = $arr['fanyi1'].'#'.$arr['fanyi2'].'#'.$arr['fanyi3'];
                $fanyici = rtrim($fanyici,'##');
                $fanyici = rtrim($fanyici,'#');

                $res['ci'] = $ci;
                $res['tongyici'] = $tongyici;
                $res['fanyici'] = $fanyici;
                $res['pys'] = $json;

                $addid = $m->add($res);

                A('Ywmanager/User')->recordUserOption('lib_zuci','add','id='.$addid);

                $re["errcode"] = 0;
                $re["msg"] = 'ok';
            }
        }else{
            //更新
            $m = M('lib_zuci');
            $tongyici = $arr['jinyi1'].'#'.$arr['jinyi2'].'#'.$arr['jinyi3'];
            $tongyici = rtrim($tongyici,'##');
            $tongyici = rtrim($tongyici,'#');

            $fanyici = $arr['fanyi1'].'#'.$arr['fanyi2'].'#'.$arr['fanyi3'];
            $fanyici = rtrim($fanyici,'##');
            $fanyici = rtrim($fanyici,'#');
            $res['ci'] = $ci;
            $res['tongyici'] = $tongyici;
            $res['fanyici'] = $fanyici;
            $res['pys'] = $json;

            $m->where('id="%d"',$id)->save($res);

            A('Ywmanager/User')->recordUserOption('lib_zuci','update','id='.$id);

            $re["errcode"] = 0;
            $re["msg"] = 'ok';
        }
        R('Kewen/repairVoiceByWord',[$ci]);
        $this->ajaxReturn($re);
    }


    /**
     * 拆分词语拼音
     */
    private function chaifenStr($pinyin){
        $arr = explode('#', $pinyin);

        $json = '{"zy":[';
        foreach($arr as $v){
            $info = $this->checkPinYin(trim($v));
            if($info['flag']){
                //匹配成功
                $json .= '{"y":"'.trim($v).'","v":"'.$info['fy'].'"},';
            }else{
                $re["errcode"] = 2;
                $re["msg"] = '拼音匹配失败';
                $this->ajaxReturn($re);
            }
        }
        $json = rtrim($json,',');
        $json .= ']}';
        return $json;
    }






















}