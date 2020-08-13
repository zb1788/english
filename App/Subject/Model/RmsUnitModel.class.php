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

	//获取英文单元信息
	public function getEnUnitListData($grade,$volume,$version,$subject,$username,$moduleid,$type)
    {
    	$sql="select t.ks_code,t.r_subject,t.ks_name_short as ks_name,t.is_unit,0 AS isdo,0 as userdata ";
        if($type=='0'){
            //表示的是单词
            $sql=$sql.",(select count(*) from engs_word where ks_code=t.ks_code and isdel=1) as count ";

        }else if($type=='1'){
            //表示的是课文
            $sql=$sql.",(select count(*) from engs_text_chapter where ks_code=t.ks_code and isdel=1) as count ";

        }else if($type=='2'){
            //表示的是听力训练
            $sql=$sql.",(select count(*) from engs_exams where ks_code=t.ks_code and isdel=1 and state=4) as count ";

        }else if($type=='3'){
            $sql=$sql.",t.style,(case when t.content is null or t.content='' then 0 else 1 end) as count ";
        }else if($type=='4'){
            $sql=$sql.",t.style,(case when t.word is null or t.word='' then 0 else 1 end) as count ";
        }else if($type=='5'){
            $sql=$sql.",t.style,(case when t.content is null or t.content='' then 0 else 1 end) as count ";
        }
        $sql=$sql."from engs_rms_unit t ";
        //$sql = $sql."LEFT JOIN ";
        //$sql = $sql."(SELECT ks_code,IFNULL(count(id),0) as userdata FROM engs_user_modules_unit_log WHERE username='".$username."' and moduleid = '".$moduleid."' group by ks_code) s";
        //$sql=$sql." ON t.`ks_code` = s.ks_code ";
        $sql = $sql." where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' ";
        //$type课文朗读3 生字听写4 古文诗词5 t.style 课文1 古文2 拼音3
        if($type=='3'){
            //$sql=$sql." and t.style!=2 ";
            $sql=$sql." ";
        }else if($type=='4'){
            $sql=$sql." ";
        }else if($type=='5'){
            $sql=$sql." and t.style=2 ";
        }
        $sql=$sql." order by display_order";

      $result=$this->cache(true,'86400')->query($sql,$grade,$volume,$version,$subject);
      return $result;
    }


    //获取单元信息
    public function getEnUnitData($grade,$volume,$version,$subject,$username,$type)
    {
      $sql="select t.ks_code,t.ks_name_short as ks_name,t.is_unit ";
      if($type=='0'){
          //表示的是单词
          $sql=$sql.",(select count(*) from engs_word where ks_code=t.ks_code and isdel=1) as count ";

      }else if($type=='1'){
          //表示的是课文
          $sql=$sql.",(select count(*) from engs_text_chapter where ks_code=t.ks_code and isdel=1) as count ";

      }else if($type=='2'){
          //表示的是听力训练
          $sql=$sql.",(select count(*) from engs_exams where ks_code=t.ks_code and isdel=1 and state=4) as count ";

      }else if($type=='3'){
          $sql=$sql.",t.style,(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }else if($type=='4'){
          $sql=$sql.",t.style,(case when t.word is null or t.word='' then 0 else 1 end) as count ";
      }else if($type=='5'){
          $sql=$sql.",t.style,(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }
      $sql=$sql."from engs_rms_unit t where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' ";
      //$type课文朗读3 生字听写4 古文诗词5 t.style 课文1 古文2 拼音3
      if($type=='3'){
          //$sql=$sql." and t.style!=2 ";
          $sql=$sql." ";
      }else if($type=='4'){
          $sql=$sql." ";
      }else if($type=='5'){
          $sql=$sql." and t.style=2 ";
      }
      $sql=$sql." order by display_order";
      $result=$this->query($sql,$grade,$volume,$version,$subject);
      return $result;
    }

    //获取英语年级版本数据
    public function getEnCourse($subjectid,$versionid,$gradeid,$termid){
        $rms_dictionary = M('rms_dictionary');
        $gindex=0;
        $tvindex=-1;
        $sql = 'SELECT v.r_grade,v.r_volume,v.r_version,v.r_subject,z.detail_name_short AS c4,s.pic_path FROM  engs_rms_unit v ';
        $sql .=' left join (SELECT * FROM engs_rms_dictionary WHERE dictionary_code="edition") z on v.r_version=z.detail_code ';
        $sql .=' left join engs_version_img s on v.r_grade=s.r_grade and v.r_version=s.r_version and v.r_subject=s.r_subject and ';
        $sql .=' v.r_volume=s.r_volume WHERE v.r_grade = "%s" and v.r_subject="%s" and s.isdel=1 ';
        $sql .=' group by v.r_grade,v.r_volume,v.r_version ORDER BY v.r_version,v.r_grade,v.r_volume,z.sortid,s.id desc';
        $verrs=$rms_dictionary->where("dictionary_code='grade' and DETAIL_CODE!='0010'")->field("detail_code,detail_name")->order("detail_order")->select();
        foreach ($verrs as $key => $value) {
            $gradeterm = $this->query($sql,$value["detail_code"],$subjectid);
            foreach ($gradeterm as $k => $v) {
                $gradeterm[$k]["pic_path"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$v["pic_path"];
                //取默认配置的版本年级学期设置为选中
                if ($v["r_version"] == $versionid && $v["r_grade"] == $gradeid && $v["r_volume"] == $termid) {
                    $gindex=$key;
                    $tvindex=$k;
                    $gradeterm[$k]["checked"] = 1;
                }else{
                    $gradeterm[$k]["checked"] = 0;
                }
            }
            $verrs[$key]["termversion"] = $gradeterm;
        }
        $ret["versions"]=$verrs;
        $ret["gindex"]=$gindex;
        $ret["tvindex"]=$tvindex;
        return $ret;
    }

    //获取英语年级版本数据
    public function getCnCourse($subjectid,$versionid,$gradeid,$termid){
        $sql = 'SELECT v.r_subject,v.r_grade,v.r_volume,v.r_version,(SELECT detail_name_short FROM db_yuwen.yw_rms_dictionary WHERE dictionary_code="edition" AND detail_code=v.r_version) AS c4,s.pic_path FROM  db_yuwen.yw_unit v ';
        $sql .=' left join db_yuwen.yw_version_img s on v.r_grade=s.r_grade and v.r_version=s.r_version and v.r_subject=s.r_subject and ';
        $sql .=' v.r_volume=s.r_volume WHERE v.r_grade = "%s" and v.r_subject="%s" and s.pic_path is not null ';
        $sql .=' group by v.r_grade,v.r_volume,v.r_version ORDER BY v.r_version';
        //$versionsql="Select  r_version as detail_code,(SELECT detail_name FROM engs_rms_dictionary WHERE detail_code=r_version AND dictionary_code='edition') as detail_name from db_yuwen.yw_unit where r_subject='".$subjectid."' group by r_version";
        //$verrs=M()->query($versionsql);
        $verrs=M("db_yuwen.rms_dictionary","yw_")->where("dictionary_code='grade' and DETAIL_CODE!='0010'")->field("detail_code,detail_name")->order("detail_order")->select();
        $gindex=0;
        $tvindex=0;
        foreach ($verrs as $key => $value) {
            $gradeterm = M()->query($sql,$value["detail_code"],$subjectid);
            foreach ($gradeterm as $k => $v) {
                $gradeterm[$k]["pic_path"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$v["pic_path"];
                //取默认配置的版本年级学期设置为选中
                if ($v["r_version"] == $versionid && $v["r_grade"] == $gradeid && $v["r_volume"] == $termid) {
                    $gindex=$key;
                    $tvindex=$k;
                    $gradeterm[$k]["checked"] = 1;
                }else{
                    $gradeterm[$k]["checked"] = 0;
                }
            }
            $verrs[$key]["termversion"] = $gradeterm;
        }
        $ret["versions"]=$verrs;
        $ret["gindex"]=$gindex;
        $ret["tvindex"]=$tvindex;
        return $ret;
    }

    //获取中文单元信息
    public function getCnUnitListData($grade,$volume,$version,$subject,$username,$moduleid,$type){
      //$unit=D("rms_unit");
      $sql="select t.style,t.r_subject,t.ks_code,t.ks_name as ks_name,t.is_unit,0 AS isdo,0 as userdata ";
      if($type=='0'){
          //表示的是单词
          $sql=$sql.",(select count(*) from engs_word where ks_code=t.ks_code and isdel=1) as count ";
      }else if($type=='1'){
          //表示的是课文
          $sql=$sql.",(select count(*) from engs_text_chapter where ks_code=t.ks_code and isdel=1) as count ";
      }else if($type=='2'){
          //表示的是听力训练
          $sql=$sql.",(select count(*) from engs_exams where ks_code=t.ks_code and isdel=1 and state=4) as count ";
      }else if($type=='3'){
          $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }else if($type=='4'){
          $sql=$sql.",(case when t.word is null or t.word='' then 0 else 1 end) as count ";
      }else if($type=='5'){
          $sql=$sql.",(case when t.content is null or t.content='' then 0 else 1 end) as count ";
      }
      $sql=$sql."from db_yuwen.yw_unit t ";
      //$sql = $sql."LEFT JOIN ";
      //$sql=$sql."(SELECT ks_code,IFNULL(count(id),0) as userdata  FROM db_english.engs_user_modules_unit_log WHERE username='".$username."' and moduleid = '".$moduleid."' group by ks_code) s";
      //$sql=$sql." ON t.`ks_code` = s.ks_code ";
      $sql = $sql." where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s' ";
      //$type课文朗读3 生字听写4 古文诗词5 t.style 课文1 古文2 拼音3
      if($type=='3'){
          $sql=$sql." ";
      }else if($type=='4'){
          $sql=$sql." ";
      }else if($type=='5'){
          $sql=$sql." and t.style=2 ";
      }
      $sql=$sql." order by display_order";
      $result=M()->cache(true,'86400')->query($sql,$grade,$volume,$version,$subject);
      return $result;
    }

    //根据年级信息获取版本数据
    public function getEnVersionById($gradeid,$termid,$versionid,$subjectid){
        $imgrs=M("version_img")->where("r_grade='%s' and r_volume='%s' and r_version='%s'",$gradeid,$termid,$versionid)->field("pic_path as pic")->find();
        $infosql="select (select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='edition' and DETAIL_CODE=r_version) as versionname,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='grade' and DETAIL_CODE=r_grade) as gradename,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='volume' and DETAIL_CODE=r_volume) as volumename,";
        $infosql=$infosql."(select detail_name_short from engs_rms_dictionary where DICTIONARY_CODE='subject' and DETAIL_CODE=r_subject) as subjectname ";
        $infosql=$infosql." from engs_rms_unit where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'";
        $infors=$this->cache(true,'86400')->query($infosql,$gradeid,$termid,$versionid,$subjectid);
        $arr["img"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$imgrs["pic"];
        $arr["versionname"]=$infors[0]["versionname"];
        $arr["gradename"]=$infors[0]["gradename"];
        $arr["volumename"]=$infors[0]["volumename"];
        $arr["subjectname"]=$infors[0]["subjectname"];
        return $arr;
    }

    //获取中文版本
    public function getCnVersionById($gradeid,$termid,$versionid,$subjectid){
      $imgrs=M("db_yuwen.version_img","yw_")->where("r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'",$gradeid,$termid,$versionid,$subjectid)->field("pic_path as pic")->find();
      $infosql="select (select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='edition' and DETAIL_CODE=r_version) as versionname,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='grade' and DETAIL_CODE=r_grade) as gradename,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='volume' and DETAIL_CODE=r_volume) as volumename,";
      $infosql=$infosql."(select detail_name from db_yuwen.yw_rms_dictionary where DICTIONARY_CODE='subject' and DETAIL_CODE=r_subject) as subjectname ";
      $infosql=$infosql." from db_yuwen.yw_unit where r_grade='%s' and r_volume='%s' and r_version='%s' and r_subject='%s'";
      $infors=M()->cache(true,'86400')->query($infosql,$gradeid,$termid,$versionid,$subjectid);
      $arr["img"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$imgrs["pic"];
      $arr["versionname"]=$infors[0]["versionname"];
      $arr["gradename"]=$infors[0]["gradename"];
      $arr["volumename"]=$infors[0]["volumename"];
      $arr["subjectname"]=$infors[0]["subjectname"];
      // $result["bookinfo"]=$arr;
      return $arr;
    }


    //获取单元信息
    public function getInfoByKsCode($ks_code){
        $rs=$this->where("ks_code='%s'",$ks_code)->find();
        return $rs;
    }


}
