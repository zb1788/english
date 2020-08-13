<?php
namespace Subject\Service;
use Think\Model;
use Subject\Service\UtilService;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class UnitService 
{
	//模块列表
	private $modules_model = array(
		"2"=>array("db_english.rms_unit",""),
		"74"=>array("db_english.rms_unit",""),
		"73"=>array("db_english.rms_unit",""),
		"63"=>array("db_math.genre","sx_")
	);

	//关键列
	private $table_key = array(
		"2"=>array("ks_code","chapter",0),
		"74"=>array("ks_code","chapter",0),
		"73" =>array("ks_code","",0),
		"63"=>array("genreid","stagename",0)
	);

	//是否关联表
	private $table_base_relation = array(
		"2"=>array('LEFT JOIN __TEXT_CHAPTER__ s  ON __RMS_UNIT__.ks_code = s.ks_code LEFT JOIN __TEXT__  p on s.id=p.chapterid'),
		"74"=>array('LEFT JOIN __TEXT_CHAPTER__ s  ON __RMS_UNIT__.ks_code = s.ks_code LEFT JOIN __TEXT__  p on s.id=p.chapterid'),
		"73"=>array('LEFT JOIN (SELECT ks_code,count(*) as count FROM __WORD__ where isdel=1 group by ks_code) as s ON __RMS_UNIT__.ks_code = s.ks_code'),
		//"10"=>array(),
		"63"=>array('LEFT JOIN db_math.sx_stage ON db_math.sx_genre.`id` = db_math.sx_stage.`genreid`')
	);

	//条件
	private $table_base_where = array(
		"2"=>array(
				array("r_subject","and","subjectid",0),
				array("r_grade","and","gradeid",0),
				array("r_version","and","versionid",0),
				array("r_volume"," and ","termid",0),
				array("(s.isdel = 1 and p.isdel=1 or s.id is null)","","",1)
			),
		"74"=>array(
				array("r_subject","and","subjectid",0),
				array("r_grade","and","gradeid",0),
				array("r_version","and","versionid",0),
				array("r_volume"," and ","termid",0),
				array("(s.isdel = 1 and p.isdel=1 or s.id is null)","","",1)
			),
		"73"=>array(
				array("r_subject","and","subjectid",0),
				array("r_grade","and","gradeid",0),
				array("r_version","and","versionid",0),
				array("r_volume","","termid",0)
			),
		"63"=>array(
				array("subject","and","subjectid",0),
				array("grade","and","gradeid",0),
				array("db_math.sx_genre.isdel = 0","and","",1),
				array("db_math.sx_stage.isdel = 0","and","",1),
				array("db_math.sx_genre.isshow =1","","",1),
			)
	);

	private $table_base_order = array(
		"2"=>"engs_rms_unit.display_order,s.sortid,p.sortid",
		"74"=>"engs_rms_unit.display_order,s.sortid,p.sortid",
		"73" =>"display_order",
		"63"=>"sx_genre.`term`,sx_genre.`sortid`,sx_stage.`sortid`"
	);

	//执行中回调
	private $table_base_callback = array(
		"2"=>array("get_text_mp3"),
		"74"=>array("get_text_mp3"),
		"73"=>array(),
		"63"=>array()
	);

	//执行后回调
	private $table_base_over_callback = array(
		"2"=>array("array_to_tree_more"),
		"74"=>array("array_to_tree_more"),
		"73"=>array(),
		"63"=>array("array_to_tree")
	);

	private $table_column_name = array(
		"2"=>"ks_name",
		"74"=>"ks_name",
		"73" =>"ks_name",
		"63"=>"name",
	);
	//mongo的表
	private $mongo_table = "engs_user_module_log";
	//采集用户数据使用mogondb

	//获取列表中个人的总数据
	//参数说明 $user表示用户信息
	//		  $unit表示用户单元信息
	


	//获取单元基础数据
	public function getBaseUnitList($where,$module){
		$ret = array();
		$moduleid = $module["moduleid"];
		if(empty($moduleid)){
			$ret["suc"] = "404";
			$ret["msg"] = "没有配置此模块";
			$ret["data"] = array();
			return $ret;
		}
		$query_table = $this->modules_model[$moduleid][0];
		$table_perfix = $this->modules_model[$moduleid][1];
		if(empty($query_table)){
			$ret["suc"] = "404";
			$ret["msg"] = "没有配置相关的模块对应的表";
			$ret["data"] = array();
			return $ret;
		}
		$query_relation = $this->table_base_relation[$moduleid];
		$query_where = $this->table_base_where[$moduleid];
		$query_order = $this->table_base_order[$moduleid];
		$table_key = $this->table_key[$moduleid];
		$column_names = $this->table_column_name[$moduleid];
		$table_base_callback = $this->table_base_callback[$moduleid];


		//拼接sql语句
		if(empty($table_perfix)){
			$Model = M($query_table);
		}else{
			$Model = M($query_table,$table_perfix);
		}
		if($query_relation&&!empty($query_relation)){
			$Model = $Model -> join($query_relation);
		}

		if(!empty($query_where)){
			$wheres = "";
			$order = "";
			//拼接sql
			
			foreach($query_where as $key=>$value){
				if($value[3] == 0){
					if($where[$value[2]]!=""&&!empty($where[$value[2]])){
						$wheres = $wheres ."".$value[0]."='".$where[$value[2]]."' ".$value[1]." ";
					}
				}else{
					if(empty($value[2])){
						$wheres = $wheres ."".$value[0]." ".$value[1]." ";
					}else{
						$wheres = $wheres ."".$value[0]."='".$value[2]."' ".$value[1]." ";
					}
					
				}
			}
			if($wheres!=""&&!empty($wheres)){
				$Model = $Model -> where($wheres);
			}
		}

		if(!empty($query_order)){
			$Model = $Model -> order($query_order);
		}
		$basers = $Model ->order($query_order)->select();
		
		$result = array();
		array_map(function($value) use (&$basers,&$result,&$table_key,&$column_names,$table_base_callback){
			$temp = $value;
			$temp["isdo"]=0;
			$temp["usercount"]=0;
			$temp["ks_name"]=$temp[$column_names];
			//加工音频文件
			if(!empty($table_base_callback)){

				foreach($table_base_callback as $key=>$val){
					$val($temp);
				}
			}
			if(empty($value["count"])){
				$temp["count"]=0;
			}
			if($table_key[2] == 0){
				$result[] = $temp;
			}else{
				$result[$table_key[0]] = $temp;
			}
		}, $basers);


		$table_base_over_callback = $this->table_base_over_callback[$module["moduleid"]];
		if(!empty($table_base_over_callback)){
			foreach($table_base_over_callback as $key=>$val){
				$result = $val($result,$table_key[0],$table_key[1],$column_names);
			}
		}
		return $result;
	}
}