<?php
namespace Subject\Model;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class UserConfigModel extends Model
{
	//数据的校验
    protected $_validate = array(
        // 手机验证:必填、格式需正确、唯一性
        array('username','require','用户名必须填写！'),
        array('configid','require','手机号码格式不正确'),
    );


    //获取用户的模块信息
    public function getUserConfig($username,$subjectid){
          $sql="SELECT ";
          // $sql=$sql."(SELECT detail_name FROM engs_menu_dictionary WHERE dictionary_code='grade' AND detail_code=engs_menu_config.`r_grade`) AS grade,";
          // $sql=$sql."(SELECT detail_name FROM engs_menu_dictionary WHERE dictionary_code='subject' AND detail_code=engs_menu_config.`r_subject`) AS SUBJECT,";
          $sql=$sql."engs_menu_modules.`title`,";
          $sql=$sql."engs_menu_modules.`id`,";
          $sql=$sql."engs_menu_modules.url,";
          $sql=$sql."engs_menu_modules.style,";
          $sql=$sql."engs_menu_modules.`typeid`,";
          $sql=$sql."engs_menu_modules.`remark`,";
          $sql=$sql."engs_menu_config.`iffree`,";
          $sql=$sql."engs_menu_config.`ifuser`";
//          $sql=$sql."user.`count`  ";
          $sql=$sql."FROM ";
          $sql=$sql." engs_menu_config  ";
          $sql=$sql."LEFT JOIN engs_menu_modules ";
          $sql=$sql."  ON engs_menu_config.`menuid` = engs_menu_modules.id ";
//          $sql=$sql."LEFT JOIN (select moduleid,count(*) as count from engs_user_modules_unit_log group by moduleid) as user";
//          $sql=$sql."  ON user.`moduleid` = engs_menu_modules.id ";
          $sql=$sql." WHERE engs_menu_config.`ifuser` = 1 and engs_menu_config.r_subject='".$subjectid."' ";
          $sql=$sql." AND engs_menu_config.`id` IS NOT NULL ";
          $sql=$sql." AND engs_menu_modules.id IS NOT NULL and engs_menu_modules.url is not null order by  engs_menu_modules.sortid";
          $result=$this->query($sql);
          return $result;
    }
}