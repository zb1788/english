<?php
/**
 * 用户登录首页
 * @author Zhangbo1
 *
 */
namespace Klx\Controller;
use Think\Controller;
class FileController extends Controller {
    public function index(){
    	$name=I('name/s','');//随机数
    	$x=I('iconx/s','');//X坐标
    	$y=I('icony/s','');//Y坐标
    	$notes=I('notes/s','');//资源名称
    	$link=I('link/s','');//jsdata内容
    	$type=I('type/s','');//资源类型
    	$page=I('pagenum/d',0);//页码
    	$action=I('action/s','');//操作
    	$userid=I('userid/s','');//用户id
    	$areaid=I('areaid/s','');//用户区域
    	$bookid=I('bookid/s','');//书本id

        $areaid='13.';
		echo $areaid;

        //$m=M('','','DB_CONFIG')->table('t_book_page_file');
		$m=M('book_page_file','t_');
        $data['link']=$link;
        $data['ran']=$name;
        $data['x']=$x;
        $data['y']=$y;
        $data['name']=$notes;
        $data['type']=$type;
        $data['page']=$page;
        $data['userid']=trim($userid);
        $data['areaid']=$areaid;
        $data['bookid']=$bookid;
        if ($action=='add'){
        	$m->add($data);
        }
        if ($action=='update'){
        	$m->where('ran="%s" and bookid="%s" and userid="%s" and areaid="%s"',$name,$bookid,$userid,$areaid)->save($data);
        }
        if ($action=='del'){
        	$m->where('ran="%s" and bookid="%s" and page="%d" and userid="%s" and areaid="%s"',$name,$bookid,$page,$userid,$areaid)->delete();
        }
    }

    public function makexml(){
    	$userid=I('userid/s','');//用户id
    	$areaid=I('areaid/s','');//用户区域
    	$bookid=I('bookid/s','');//书本id

        $areaid='13.';

        //echo $userid.'|'.$bookid.'|'.$areaid;

    	$xml="<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
    	$xml.="<data name=\"电子课本CION图标\">\r\n";
    	//$m=M('','','DB_CONFIG')->table('t_book_page_file');
		$m=M('book_page_file','t_');
    	$data=$m->where('bookid="%s" and userid="%s" and areaid="%s"',$bookid,$userid,$areaid)->field('link,ran,x,y,name,type,page')->select();

    	foreach ($data as $v){
    		$xml.="		<subobj>\r\n			<name>".$v['ran']."</name>\r\n			<iconx>".$v['x']."</iconx>\r\n			<icony>".$v['y']."</icony>\r\n			<notes>".$v['name']."</notes>\r\n			<link>".$v['link']."</link>\r\n			<type>".$v['type']."</type>\r\n			<pagenum>".$v['page']."</pagenum>\r\n		</subobj>\r\n";
    	}
    	$xml.="</data>";
    	echo $xml;
    	//var_dump($xml);
//      	$handle=fopen("icon.xml","w");
//      	fwrite($handle,$xml);
    }
}