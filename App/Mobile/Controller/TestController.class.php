<?php
namespace Mobile\Controller;
use Think\Controller;
/** 
* 首页控制器类
*  
* @author         gm 
* @since          1.0 
*/  

class testController extends Controller {
	/** 
	* index  
	* 首页展示 
	* 
	* @author         gm 
	* @since          1.0 
	*/  
     public function test(){ 
     	$url='http://ssokw.czbanbantong.com/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=7b3891bad2e76ff6c7dcbd4c73049aaad7eb258f6c4e89b75f3c46879416b8aee80b0bc1d86f7876';
        $json_ret = file_get_contents($url);
	    $url = "https://www.baidu.com";
	　　$ch = curl_init();
	　　curl_setopt($ch, CURLOPT_URL, $url);
	　　curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	　　// post数据
	　　curl_setopt($ch, CURLOPT_POST, 0);
	　　// post的变量
	　　curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	　　$output = curl_exec($ch);
	　　curl_close($ch);

	　　//打印获得的数据
	　　print_r($output);



	 	//echo "sss=".$json_ret;
     }

	public function index(){
		//study_word_text_info();
		$wordbcount = cookie('engm_wordbook_count');
	  $recordcount= cookie('engm_record_count');
	  $this -> assign("recordcount",$recordcount);
	  $this -> assign("wordbcount",$wordbcount);
	  $this -> assign("truename",cookie("engm_truename"));
        $this -> assign("currentuserpic",cookie("engm_curruserpic"));
        $this -> display();
    }


    public function getFile(){
    	$content=file_get_contents("C:\\Users\\qgy\\Desktop\\file.txt");
    	
    }
}   