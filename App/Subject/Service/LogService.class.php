<?php
namespace Subject\Service;
use Think\Model;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class LogService 
{
	//接口相应时间
	public function getEngsInterfaceResponseTime($data,$user){
		$filename=Date("Y-m-d");
		//直接写日至
		file_put_contents('../log/response'.$filename.".txt", $data["interface"]."#".$data["starttime"]."#".$data["endtime"]."#".$user["username"]."#".$user["usertype"]."#".$user["localareacode"]."#".$user["phpsession"]."#".(strtotime($data["endtime"])-strtotime($data["starttime"])).PHP_EOL,FILE_APPEND);
	}
    
}