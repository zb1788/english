<?php

namespace Klx\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class DictmController extends Controller {

    public function index() {

        $this->display();
    }


	public function chinesex() {
        $word=I("word/s");
        $sql="SELECT *,(SELECT url FROM hl_nami_hanzi nh WHERE nh.hanzi=t.name) AS gif,(SELECT nj.jibenjieshi FROM hl_nami_hanzi_jieshi nj WHERE nj.nameid=t.id) AS jibenjieshi,(SELECT nj.xiangxijieshi FROM hl_nami_hanzi_jieshi nj WHERE nj.nameid=t.id) AS xiangxijieshi,(SELECT nj.xiangguancizu FROM hl_nami_hanzi_jieshi nj WHERE nj.nameid=t.id) AS xiangguancizu FROM hl_hanzi_name t LEFT JOIN hl_nami_hanzi_info s ON t.`id`=s.`nameid` WHERE t.`name`='".$word."'";
        $rs=M()->query($sql);
        //var_dump($rs);
        $arr_return["data"]=$rs;
        if(!empty($rs)){
          $arr_return["result"]='succ';
        }else{
          $arr_return["result"]='false';
        }
        $this->ajaxReturn($arr_return);
    }

    public function share(){
      $content=I("content");
      $url=I("url");
      $domain=$_SERVER["SERVER_NAME"];
      $url="http://".$domain.$url;
      // if($source==0){
      //   //在线字典
      //   $url="http://".$domain."/Klx/Dictpc/index";
      // }else{
      //   $url="http://".$domain."/Klx/Cydict/index";
      // }
      shareIlearn($url,$content);
    }
}
