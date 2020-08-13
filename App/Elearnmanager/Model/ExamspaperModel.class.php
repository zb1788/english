<?php
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class ExamspaperModel extends BaseModel
{
	public function getExamspaperListData($condition)
    {
        return $this->tableExams()->where($condition)->select();
    }
	
	
	public function getExamsFindData($condition)
	{
		return $this->tableExams()->where($conditon)->find();
	} 
	
	public function getDeleteExamsData()
    {
    	if(!empty($_REQUEST["id"]))
    	{
    		return $this->tableExams()->where("id=%d",$_REQUEST["id"])->delete();
    	}
    }
	
	public function getAddExamsData($adddata)
	{
		return $this->tableExams()->add($adddata);
	}
	
	public function getSaveExamsData($condition,$savedata)
	{
		return $this->tableExams()->where($condition)->add($adddata);
	}
}