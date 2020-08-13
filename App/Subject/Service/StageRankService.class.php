<?php
namespace Subject\Service;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class StageRankService
{
    public function handleBatch($multipleData = []){
        if(empty($multipleData)){
            return false;
        }
        foreach($multipleData as $rkey=>$rvalue){
            $typeid = $rkey;
            foreach($rvalue as $skey=>$svalue){
                $configid = $svalue["configid"];
                $rank = $svalue["rank"];
                $sql = "select * from db_math.sx_stage_rank t where t.typeid='%s' and t.configid=%d";
                $result = M()->query($sql,$typeid,$configid);
                if(!empty($result)){
                    M()->execute("update db_math.sx_stage_rank set rank='".$rank."',updtime='".Date('Y-m-d H:i:s')."' where typeid='".$typeid."' and configid=".$configid);
                }else{
                    M()->execute("insert into db_math.sx_stage_rank (typeid,configid,rank) value ('".$typeid."',".$configid.",'".$rank."')");
                }
            }
        }
    }
    public function handleModuleBatch($multipleData = []){
        
        if(empty($multipleData)){
            return false;
        }
        //var_dump($multipleData);
        foreach($multipleData as $rkey=>$rvalue){
            $typeid = $rkey;
            foreach($rvalue as $skey=>$svalue){
                $configid = $svalue["configid"];
                $rank = $svalue["rank"];
                // $rank = unserialize($rank);
                // var_dump($rank);exit;
                $sql = "select * from engs_rank_info t where t.typeid='%s' and t.configid=%d";
                $result = M()->query($sql,$typeid,$configid);
                if(!empty($result)){
                    M()->execute("update engs_rank_info set rank='".$rank."',updtime='".Date('Y-m-d H:i:s')."' where typeid='".$typeid."' and configid=".$configid);
                }else{
                    M()->execute("insert into engs_rank_info (typeid,configid,rank) value ('".$typeid."',".$configid.",'".$rank."')");
                }
            }
        }
    }
}
