<?php
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class RmsUnitModel extends Model
{
	//获取单元信息
	public function getUnitListData($grade,$volume,$version,$subject,$username,$moduleid,$type)
    {
    	$sql="select t.style,t.ks_code,t.ks_name_short as ks_name,t.is_unit,CASE WHEN s.ks_code IS NULL THEN 0 ELSE s.count END AS isdo ";
        if($type=='0'){
            //表示的是单词
            $sql=$sql.",(select count(*) from engs_word where ks_code=t.ks_code and isdel=1) as count ";

        }else if($type=='1'){
            //表示的是课文
            $sql=$sql.",(select count(*) from engs_text_chapter where ks_code=t.ks_code and isdel=1) as count ";

        }else if($type=='2'){
            //表示的是听力训练
            $sql=$sql.",(select count(*) from engs_exams where ks_code=t.ks_code and isdel=1 and state=4 and tts_type = 1) as count ";

        }else if($type=='3'){
            $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
        }else if($type=='4'){
            $sql=$sql.",(case when t.word is null or t.word='' then 0 else 1 end) as count ";
        }else if($type=='5'){
            $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
        }
        $sql=$sql."from engs_rms_unit t LEFT JOIN ";
        $sql=$sql."(SELECT count(*) as count,ks_code FROM engs_user_modules_unit_log WHERE  moduleid = '".$moduleid."' group by ks_code) s";
        $sql=$sql." ON t.`ks_code` = s.ks_code where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' ";
        //$type课文朗读3 生字听写4 古文诗词5 t.style 课文1 古文2 拼音3
        if($type=='3'){
            $sql=$sql." and t.style!=2 ";
        }else if($type=='4'){
            $sql=$sql." ";
        }else if($type=='5'){
            $sql=$sql." and t.style=2 ";
        }
        $sql=$sql." order by display_order";
    	$result=$this->query($sql,$grade,$volume,$version,$subject);
    	return $result;
    }

    //获取你年级版本数据
    public function getCourse($subjectid,$versionid,$gradeid,$termid){
        $rms_dictionary = M('rms_dictionary');
        $sql = 'SELECT v.r_grade,v.r_volume,v.r_version,(SELECT detail_name_short FROM engs_rms_dictionary WHERE dictionary_code="edition" AND detail_code=v.r_version) AS c4,s.pic_path FROM  engs_rms_unit v ';
        $sql .=' left join engs_version_img s on v.r_grade=s.r_grade and v.r_version=s.r_version and v.r_subject=s.r_subject and ';
        $sql .=' v.r_volume=s.r_volume WHERE v.r_grade = "%s" and v.r_subject="%s" ';
        $sql .=' group by v.r_grade,v.r_volume,v.r_version,v.c4 ORDER BY v.r_version';
        $verrs=$rms_dictionary->where("dictionary_code='grade' and DETAIL_CODE!='0010'")->field("detail_code,detail_name")->order("detail_order")->select();
        //$versionsql="Select r_version as detail_code,(SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_version AND dictionary_code='edition') as detail_name from engs_rms_unit where r_subject='0003' group by r_version";
        //$verrs=M()->query($versionsql);
        foreach ($verrs as $key => $value) {
            $gradeterm = $this->query($sql,$value["detail_code"],$subjectid);
            foreach ($gradeterm as $k => $v) { 
                //取默认配置的版本年级学期设置为选中
                if ($v["r_version"] == $versionid && $v["r_grade"] == $gradeid && $v["r_volume"] == $termid) {
                    $gradeterm[$k]["checked"] = 1;
                }else{
                    $gradeterm[$k]["checked"] = 0;
                }
            }
            $verrs[$key]["termversion"] = $gradeterm;
        }
        return $verrs;
    }

    //根据年级信息获取版本数据
    public function getVersionById($gradeid,$termid,$versionid,$subjectid){
        $imgrs=M("version_img")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("pic_path as pic")->find();
        $infosql="select (select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='edition' and DETAIL_CODE=r_version) as versionname,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='grade' and DETAIL_CODE=r_grade) as gradename,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='volume' and DETAIL_CODE=r_volume) as volumename,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='subject' and DETAIL_CODE=r_subject) as subjectname ";
        $infosql=$infosql." from engs_rms_unit where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'";
        $infors=$this->query($infosql,$gradeid,$termid,$versionid,$subjectid);
        $arr["img"]=$imgrs["pic"];
        $arr["versionname"]=$infors[0]["versionname"];
        $arr["gradename"]=$infors[0]["gradename"];
        $arr["volumename"]=$infors[0]["volumename"];
        $arr["subjectname"]=$infors[0]["subjectname"];
        return $arr;
    }

    
}