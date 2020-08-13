<?php 
 //获取节目列表
   function get_course_list($isterm,$isunit,$app_id,$version_id,$unit_id){
   		$coursedata = M();
   		$username=cookie("username");
        $areacode=cookie("areacode");
        if($username == '' || $username=='null' || $username == null){
            $username=cookie("engm_username");
            $areacode=cookie("engm_areacode");
            if($username == '' || $username=='null' || $username == null){
                $username='hxp';
                $areacode='1.1.1';
            }
            
        }
		if($isterm == 1){
			if($isunit == 1){
				$sqlwhere = ' where a.isdel = 1 and  app_id='.$app_id.' and unit_id='.$unit_id.' and version_id='.$version_id;
			}
			else{
				$sqlwhere = ' where a.isdel = 1 and app_id ='.$app_id.' and version_id='.$version_id;
			}
		}
		else{
			if($isunit == 1){
				$sqlwhere = ' where a.isdel = 1 and  app_id='.$app_id.' and unit_id='.$unit_id;
			}
			else{
				$sqlwhere = ' where a.isdel = 1 and app_id='.$app_id;
			}
			
		}
		$sql = "select a.*,(select count(*) from mc_question b where b.course_id = a.id and b.isdel = 1 ) as q_num,(select count(*) from mc_learn_record c where c.course_id = a.id and c.username = '".$username."' and c.areacode='".$areacode."') as learn_num from mc_course a ".$sqlwhere." order by a.sortid,a.id";
		//echo $sql;
		 $courselist = $coursedata->query($sql);
		 return $courselist;
   }
?>
