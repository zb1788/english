<?php

namespace Home\Controller;

use Think\Controller;

class RecordController extends Controller {

    public function index() {
        $this->display('index');
    }

    /**
     * 学习记录列表获取
     */
    /**
     * 学习记录列表获取
     */
    public function record_list() {
        $model = M();
        $studyarr = array();
        $username = cookie('username');
        $areacode = cookie('areacode');
        $sql='SELECT t.`addtimey`,concat(t.`addtimey`,t.`addtimem`) as month,t.`addtimem`,t.`addtimed`,SUM(IF(studytype = 1 AND wordflag <> 2, 1, 0)) AS xxnum,SUM(IF(studytype = 2, 1, 0)) AS textnum,SUM(IF(studytype = 1 AND wordflag = 2, 1, 0)) AS txnum,SUM(IF(studytype = 3 AND ks_code <> "0", 1, 0)) AS listennum,SUM(IF(studytype = 3 AND ks_code = "0", 1, 0)) AS listenztnum,SUM(IF(studytype = 2, 1, 0)) AS txnum FROM engs_study t WHERE t.`username` = "'.$username.'" AND areacode = "'.$areacode.'" GROUP BY t.`addtimey`,t.`addtimem`,t.`addtimed` ORDER BY t.addtime DESC';
        //$sql = 'select addtimey from engs_study where username="' . $userid . '" and areacode="'.$areacode.'" group by addtimey order by addtime desc,id desc';
		//echo $sql;
		$resultinfo = $model->query($sql);
        //echo count($resultinfo);
        $ret=$this->MergeArray($resultinfo,0,count($resultinfo)-1,"month");
       
        $return=array();
        foreach($ret as $key=>$value){
            $temp=array();
            $temp["addtimey"]=substr($key,0,4);
            $temp["addtimem"]=substr($key,4,2);
            $temp["dayinfo"]=$value;
            array_push($return,$temp);
        }
        //var_dump($return);
        $ret=$this->MergeArray($return,0,count($return)-1,"addtimey");

        $returnArr=array();
        foreach($ret as $key=>$value){
            $temp=array();
            $temp["addtimey"]=$key;
            $temp["monthinfo"]=$value;
            array_push($returnArr,$temp);
        }
        $this->ajaxReturn($returnArr);
    }


    //二维数据的合并
    private function MergeArray($data,$begin,$end,$index){
        if($end!==0){
            if($begin<($end-1)){
                $middle=ceil(($begin+$end)/2);
                $leftArr=$this->MergeArray($data,$begin,$middle,$index);
                $rightArr=$this->MergeArray($data,$middle+1,$end,$index);
                $arr=array();
                foreach($leftArr as $key=>$value){
                    $rows=0;
                    if(is_array($value[0])){
                        $rows=1;
                    }
                    if($rows==0){
                        $arr[$key][]=$value;
                    }else{
                        foreach($value as $itemk=>$itemv){
                            $arr[$key][]=$itemv;
                        }
                    }
                }
                foreach($rightArr as $key=>$value){
                    if(isset($arr[$key])){
                        $rows=0;
                        if(is_array($value)){
                            $rows=1;
                        }
                        if($rows==0){
                            $arr[$key][]=$value;
                        }else{
                            foreach($value as $itemk=>$itemv){
                                $arr[$key][]=$itemv;
                            }
                        }
                    }else{
                        $rows=0;
                        //只需要判断一个就可以了
                        if(is_array($value)){
                            $rows=1;
                        }
                        if($rows==0){
                            $arr[$key][]=$value;
                        }else{
                            foreach($value as $itemk=>$itemv){
                                $arr[$key][]=$itemv;
                            }
                        }
                    } 
                }
                return $arr;
            }else if($begin==($end-1)){
                if($data[$begin][$index]==$data[$end][$index]){
                    //进行合并
                    $arr=array();
                    array_push($arr,$data[$begin]);
                    array_push($arr,$data[$end]);
                    $ret[$data[$begin][$index]]=$arr;
                }else{
                    $ret[$data[$begin][$index]][]=$data[$begin];
                    $ret[$data[$end][$index]][]=$data[$end];
                }
                return $ret;
            }else if($begin==$end){          
                $ret[$data[$begin][$index]][]=$data[$begin];
                return $ret;
            }
        }else{
            $ret[$data[$begin][$index]][]=$data[$begin];
            return $ret;
            //return $data;
        }
        
    }


    

    /**
     * 学习记录详细信息获取
     */
    public function study_record_detail() {
        $curtime = I('curtime/s', '01');
        $userid = cookie('username');
        $areacode = cookie('areacode');
        $sql = 'select t.*, v.r_grade,v.r_volume,v.r_version,v.c1 from (select a.ks_code,wordflag as flag,a.engs_studyid as id,"word" as study_type,allcount,"0" as score,"0" as errornum,c.word as ucname from engs_study a,(select max(id) as newid,count(*) as allcount from engs_study where username = "' . $userid . '" and areacode="'.$areacode.'" and wordflag = 0 and addtime="' . $curtime . '" and studytype=1 group by ks_code) b,engs_word c where a.id = b.newid and a.engs_studyid = c.id  union select a.ks_code,wordflag as flag,a.engs_studyid as id,"word" as study_type,allcount,"0" as score,"0" as errornum,c.word as ucname from engs_study a,(select max(id) as newid,count(*) as allcount from engs_study where username = "' . $userid . '" and areacode="'.$areacode.'" and wordflag = 1 and addtime="' . $curtime . '" and studytype=1 group by ks_code) b,engs_word c where a.id = b.newid and a.engs_studyid = c.id  union select a.ks_code,wordflag as flag,a.engs_studyid as id,"word" as study_type,allcount,"0" as score,"0" as errornum,c.word as ucname from engs_study a,(select max(id) as newid,count(*) as allcount from engs_study where username = "' . $userid . '"  and areacode="'.$areacode.'" and wordflag = 2 and addtime="' . $curtime . '" and studytype=1 group by ks_code) b,engs_word c where a.id = b.newid and a.engs_studyid = c.id  union select a.ks_code, 3 as flag, a.engs_studyid as id,"text" as study_type, allcount,"0" as score,"0" as errornum, c.chapter as ucname from engs_study a, (select max(id) as newid, count(*) as allcount from engs_study where username = "' . $userid . '" and areacode="'.$areacode.'" and addtime="' . $curtime . '" and studytype=2 group by ks_code ) b, engs_text_chapter c where a.id = b.newid and a.engs_studyid = c.id  union select a.ks_code, 4 as flag, a.engs_studyid as id,"listen" as study_type, allcount,a.score,a.errornum, c.name as ucname from engs_study a, (select max(id) as newid, count(*) as allcount from engs_study where username = "' . $userid . '" and areacode="'.$areacode.'" and addtime="' . $curtime . '" and studytype=3 and ks_code !="0"   group by  ks_code ) b, engs_exams c where a.id = b.newid and a.engs_studyid = c.id union select  a.ks_code, 5 as flag, a.engs_studyid as id,"listenzt" as study_type, allcount,a.score,a.errornum, c.name as ucname from engs_study a, (select max(id) as newid, count(*) as allcount from engs_study where username = "' . $userid . '" and areacode="'.$areacode.'" and addtime="' . $curtime . '" and ks_code="0" and studytype=3 group by ks_code ) b, engs_exams c where a.id = b.newid and a.engs_studyid = c.id) t left join engs_rms_unit v on t.ks_code = v.ks_code order by t.flag';
        //echo $sql;
        $model = M();
        $record_detail = $model->query($sql);
        $this->ajaxReturn($record_detail);
    }
/**
 * 获取某条学习记录的单词或者课文在所在单元的位置
 */
    public function study_record_set_user() {
        $model = M('config');
        $gradeid = I('gradeid/d', 0);
        $termid = I('termid/d', 0);
        $versionid = I('versionid/d', 0);
        $ks_code = I('ks_code/d', 0);
        $studytype = I('studytype/s', 0);
        $word_text_id = I('word_text_id/d', 0);
        $data['gradeid'] = $gradeid;
        $data['termid'] = $termid;
        $data['versionid'] = $versionid;
        $data['userid'] = cookie('username');
        $curtime = date("Y-m-d H:i:s");
		
			$model->data($data)->add();
		
        if ($studytype == 'word') {
            $sql = 'select a.id from engs_word as a where a.isdel=1 and a.ks_code = ' . $ks_code . ' order by a.sortid,a.id';
            $rsword = $model->query($sql);
            for ($i = 0; $i < count($rsword); $i++) {
                if ($word_text_id == $rsword[$i]['id']) {
                    $wordindex = $i;
                    break;
                }
            }
            echo '{"wordindex":"' . $wordindex . '"}';
        } else {
            $sql = 'select id,chapter,issection,sortid from engs_text_chapter where ks_code = ' . $ks_code . ' and isdel = 1 order by sortid,id';
            $rs = $model->query($sql);
            for ($i = 0; $i < count($rs); $i++) {
                if ($word_text_id == $rs[$i]['id']) {
                    $textindex = $i;
                    $textsortid = $rs[$i]['sortid'];
                    break;
                }
            }
            echo '{"textindex":"' . $textindex . '","textsortid":"' . $textsortid . '"}';
        }
    }

}
