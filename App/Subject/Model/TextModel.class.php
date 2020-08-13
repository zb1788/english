<?php
/*
 * @Author: your name
 * @Date: 2018-11-20 10:00:38
 * @LastEditTime: 2020-07-23 10:39:07
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: \www\en\App\Subject\Model\TextModel.class.php
 */ 
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class TextModel extends Model
{
    public function getTextListData($ks_code){
    	$chaptersql="select id,chapter,issection,isevaluate from engs_text_chapter where ks_code='%s' and isdel=1 order by sortid";
    	$chapterresult=M("text_chapter")->query($chaptersql,$ks_code);
    	foreach($chapterresult as $key=>$value){
            $path = PROTOCOL.'://'.RESOURCE_DOMAIN."/yylmp3/mp3_text/";
            // $path = getHttpUrl($path);
    		$textsql="select *,concat(mp3,'.mp3') as name,concat('".$path."',left(mp3,2),'/',mp3,'.mp3') as url, mp3time from engs_text where chapterid=%d and isdel=1 order by sortid";
            $textresult=$this->query($textsql,$value["id"]);
            foreach($textresult as $textkey=>$textval){
                $textresult[$textkey]['mp3time'] = ceil($textval['mp3time']);
            }
            $chapterresult[$key]["texts"]=$textresult;
            //$chapterresult["mp3"]=$path.$value["chapter_mp3"];
    	}
    	return $chapterresult;
    }

    //获取章节
    public function getChapterList($ks_code){
        $chapter=M("text_chapter");
        $chapterlist=$chapter->where("ks_code='%s' and isdel=1",$ks_code)->field("id,chapter,ks_code")->select();
        return $chapterlist;
    }

    //获取章节
    public function getReadChapterList($ks_code,$username,$areaid){
        $chapter=M("text_chapter");
        $sql="select engs_text_chapter.id,engs_text_chapter.chapter,engs_text_chapter.ks_code,z.maxscore,z.addtime";
        $sql=$sql." from engs_text_chapter left join (select chapterid,MAX(score) AS maxscore,ADDTIME from engs_user_read where username='%s' and ";
        $sql=$sql."areaid='%s' and ks_code='%s' and isover=0 and truename is not null group by chapterid order by id desc) as z on engs_text_chapter.id=z.chapterid ";
        $sql=$sql."where engs_text_chapter.ks_code='%s' and engs_text_chapter.isdel=1";
        $chapterlist=$this->query($sql,$username,$areaid,$ks_code,$ks_code);
        return $chapterlist;
    }


    //获取章节中的课文
    public function getTextList($chapterid){
        $textlist=$this->where("chapterid=%d and isdel=1",$chapterid)->select();
        return $textlist;
    }

    //获取这个单元下所有的音频
    public function getUnitAudioList($ks_code){
        //'1' as size,'mp3' as format,";
        //$sql=$sql."concat('".C('word_mp3_path')."',engs_base_word.ukmp3) as url,";
        $sql="SELECT concat(t.`mp3`,'.mp3') as name,'1' as size,'mp3' as format,concat('".PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_text/',left(mp3,2),'/',mp3,'.mp3') as url,ROUND(mp3time) as mp3time FROM engs_text t WHERE t.`chapterid` IN (SELECT id FROM engs_text_chapter s WHERE s.`ks_code`='%s' AND s.`isdel`=1) AND t.`isdel`=1 ";
        $rs=M()->query($sql,$ks_code);
        return $rs;
    }

}