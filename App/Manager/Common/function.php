<?php 
/*定义分组公共函数的地方*/
function getUnitID($arr_cur_unit, $unitname = '')
{
	if (!isset($arr_unit)) { 
	    $unit=M('rms_unit');
	    $rs=$unit->where('flag=1 and is_unit=0')->field('ks_code,r_grade,r_volume,r_version,ks_name,ks_name_short') -> select(); 
		foreach($rs as $v) {
			$arr_unit[$v["ks_code"]]["r_grade"] = intval( $v["r_grade"]);
			$arr_unit[$v["ks_code"]]["r_version"] = intval( $v["r_version"]);
			$arr_unit[$v["ks_code"]]["r_volume"] =  intval( $v["r_volume"]);
			$arr_unit[$v["ks_code"]]["ks_name"] =  strtoupper($v["ks_name"]);		
		}
	}
	$arr_cur_unit["ks_name"] =  strtoupper($arr_cur_unit["ks_name"]);
	if (isset($arr_unit)) {
		$ks_code = array_search($arr_cur_unit, $arr_unit);
	}else
	{
		$ks_code = 0;
	}
	return $ks_code;
}
function clearSQL($str){ 
	$str = str_replace( '"', '\"' ,$str ) ; 
	$str = str_replace( '\\\\', '\\' ,$str ) ; 
	return trim($str);
}
function daddslashes($string, $force = 0, $strip = false) {
	if (!get_magic_quotes_gpc() || $force) {
		if (is_array($string)) {
			// 如果其为一个数组则循环执行此函数
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			// 下面是一个三元操作符，如果$strip为true则执行stripslashes去掉反斜线字符，再执行addslashes
			// 这里为什么要将$string先去掉反斜线再进行转义呢，因为有的时候$string有可能有两个反斜线，stripslashes是将多余的反斜线过滤掉
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
//判断字符串的的编码类型
function getchar($str,$formcode,$aftercode){
	// $encode = mb_detect_encoding($str, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
	// //return $encode;
 //    if ($encode != $aftercode){
 //      $str = iconv($formcode,$aftercode,$str);
 //    }
    return $str;
}

//获取试听音频
function gettempmp3($content,$voiceid,$rate){
        $url = "http://192.168.151.206/voice/voice.ashx";
        $post_data["content"]=$content;
        $post_data["voiceid"]=$voiceid;
        $post_data["rate"]=$rate;
		$ch = curl_init($url);
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_TIMEOUT,50000); 
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'Referer:http://www.131458.com/',  
	            'Host:www.131458.com',
	            'User-Agent:Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'),
	            'Cache-Control:no-cache',
	            'X-Requested-With:XMLHttpRequest',
	            'cookie:ASP.NET_SessionId=4biasa45s5dz0t55wxivuuai; Hm_lvt_ccc93bebd5e7bdc84975186073891702=1448072583; Hm_lpvt_ccc93bebd5e7bdc84975186073891702=1448075485; bdshare_firstime=1448072583684; wzwsconfirm=699a156309626dc0206ffda0c40c55f9; wzwstemplate=Mw==; wzwschallenge=V1pXU19DT05GSVJNX1BSRUZJWF9MQUJFTDMxMjQ0OTU='
	        );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $output = curl_exec($ch);
	    
	    return $output;
}

//文件下载
function downfile($file_path,$file_name)
{
	header("Content-type:text/html;charset=utf-8");
	//首先要判断给定的文件存在与否 
	if(!file_exists($file_path)){ 
		echo "没有该文件文件"; 
		exit; 
	}
	$fp=fopen($file_path,"r");
	$file_size=filesize($file_path);
	
	//下载文件需要用到的头 
	Header("Content-type: application/octet-stream"); 
	Header("Accept-Ranges: bytes"); 
	Header("Accept-Length:".$file_size); 
	Header("Content-Disposition: attachment; filename=".$file_name);
	$buffer=1024; 
	$file_count=0;
	$file_con="";
	//向浏览器返回数据 
	while(!feof($fp) && $file_count<$file_size){ 
		$file_con=$file_con.fread($fp,$buffer); 
		$file_count+=$buffer; 
	}
	echo $file_con;
	fclose($fp);
}

  function Chinese_Money_Max($i,$s=1){  
    $c_digIT_min = array("零","十","百","千","万","亿","兆");  
    $c_num_min = array("零","一","二","三","四","五","六","七","八","九","十");  
     
    $c_digIT_max = array("零","十","百","千","万","亿","兆");  
    $c_num_max = array("零","一","二","三","四","五","六","七","八","九","十");  
     
    if($s==1){  
        $c_digIT = $c_digIT_max;  
        $c_num = $c_num_max;  
    }else{  
        $c_digIT = $c_digIT_min;  
        $c_num = $c_num_min;  
    }  
  
    if($i<0)  
        return "负".Chinese_Money_Max(-$i);  
        //return "-".Chinese_Money_Max(-$i);  
    if ($i < 11)  
        return $c_num[$i];  
    if ($i < 20)  
        return $c_num[1].$c_digIT[1] . $c_num[$i - 10];  
    if ($i < 100) {  
        if ($i % 10)  
            return $c_num[$i / 10] . $c_digIT[1] . $c_num[$i % 10];  
        else  
            return $c_num[$i / 10] . $c_digIT[1];  
    }  
    if ($i < 1000) {  
        if ($i % 100 == 0)  
            return $c_num[$i / 100] . $c_digIT[2];  
        else if ($i % 100 < 10)  
            return $c_num[$i / 100] . $c_digIT[2] . $c_num[0] . Chinese_Money_Max($i % 100);  
        else if ($i % 100 < 10)  
            return $c_num[$i / 100] . $c_digIT[2] . $c_num[1] . Chinese_Money_Max($i % 100);  
        else  
            return $c_num[$i / 100] . $c_digIT[2] . Chinese_Money_Max($i % 100);  
    }  
    if ($i < 10000) {  
        if ($i % 1000 == 0)  
            return $c_num[$i / 1000] . $c_digIT[3];  
        else if ($i % 1000 < 100)  
            return $c_num[$i / 1000] . $c_digIT[3] . $c_num[0] . Chinese_Money_Max($i % 1000);  
        else  
            return $c_num[$i / 1000] . $c_digIT[3] . Chinese_Money_Max($i % 1000);  
    }  
    if ($i < 100000000) {  
        if ($i % 10000 == 0)  
            return Chinese_Money_Max($i / 10000) . $c_digIT[4];  
        else if ($i % 10000 < 1000)  
            return Chinese_Money_Max($i / 10000) . $c_digIT[4] . $c_num[0] . Chinese_Money_Max($i % 10000);  
        else  
            return Chinese_Money_Max($i / 10000) . $c_digIT[4] . Chinese_Money_Max($i % 10000);  
    }  
    if ($i < 1000000000000) {  
        if ($i % 100000000 == 0)  
            return Chinese_Money_Max($i / 100000000) . $c_digIT[5];  
        else if ($i % 100000000 < 1000000)  
            return Chinese_Money_Max($i / 100000000) . $c_digIT[5] . $c_num[0] . Chinese_Money_Max($i % 100000000);  
        else  
            return Chinese_Money_Max($i / 100000000) . $c_digIT[5] . Chinese_Money_Max($i % 100000000);  
    }  
    if ($i % 1000000000000 == 0)  
        return Chinese_Money_Max($i / 1000000000000) . $c_digIT[6];  
    else if ($i % 1000000000000 < 100000000)  
        return Chinese_Money_Max($i / 1000000000000) . $c_digIT[6] . $c_num[0] . Chinese_Money_Max($i % 1000000000000);  
    else  
        return Chinese_Money_Max($i / 1000000000000) . $c_digIT[6] . Chinese_Money_Max($i % 1000000000000);  
}   
  
    function Chinese_Money($i){  
 	$j=Floor($i);  
    $x=($i-$j)*100;  
    //return $x;  
    //return Chinese_Money_Max($j)."元".Chinese_Money_Min($x)."整";  
    return Chinese_Money_Max($j,'0');  
}

    function split_str($string, $type='array', $charset='utf-8'){
    //通过ord()函数获取字符的ASCII码值，如果返回值大于 127则表示为中文字符的一半，再获取后一半组合成一个完整字符
    $flag = false;
    if(strtolower($charset) == 'utf-8'){
        //如果utf-8环境
        $flag = true;
        $string = iconv('utf-8', 'gbk//IGNORE', $string);//由于ord函数在gbk下单个中文长度为2，utf-8下长度为3
    }
    if(strtolower($charset) != 'gbk' && strtolower($charset) != 'utf-8')
    {
        exit('参数不合法!');
    }

    //把字符串转化为ascii码存入数组,如果是中文是由两个ASCII码组成，英文是一个
    $length = strlen($string);
    $result = array();
    for($i=0; $i<$length; $i++){
        if(ord($string[$i])>127){
            $result[] = ord($string[$i]).' '.ord($string[++$i]);
        }else{
            $result[] = ord($string[$i]);
        }
    }
    if(strtolower($type) == 'array'){
        //如果返回值要数组
        $str = '';
        foreach($result as $v){
            $isEmpty = strstr($v,' ');
            if(empty($isEmpty)){
                $tmpstr = chr($v);
                if($flag){
                    $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                }
                $data[] = $tmpstr;
            }else{
                list($a,$b) = explode(' ',$v);
                $tmpstr = chr($a).chr($b);
                if($flag){
                    $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                }
                $data[] = $tmpstr;
            }
        }
        return $data;
    }elseif(strtolower($type) == 'string'){
        $data = array();
        foreach($result as $v){
            $isEmpty = strstr($v,' ');
            if(empty($isEmpty)){
                $str .= chr($v);
            }else{
                list($a,$b) = explode(' ',$v);
                $str .= chr($a).chr($b);
            }
        }
        $str=iconv('gbk', 'utf-8', $str);
        return $str;
    }else{
        exit('参数不合法！');
    }
}

  //进行二维数组的排序
function sortt($data,$key) {  
    if (count ( $data ) <= 1) {  
      return $data;  
    }
    $tem = $data [0][$key];  
    $leftarray = array ();  
    $rightarray = array ();  
    for($i = 1; $i < count ( $data ); $i ++) {
        if ($data [$i][$key] <= $tem ) {  
            $leftarray[] = $data[$i];  
        } else {  
            $rightarray[] = $data[$i];  
        }      
    }  
    $leftarray=sortt($leftarray,$key,$type);  
    $rightarray=sortt($rightarray,$key,$type);  
    $sortarray = array_merge ( $leftarray, array ($data[0]), $rightarray );  
    return $sortarray;  
}

function delFileUnderDir($dir){
    if ( $handle = opendir($dir) ) { 
        while ( false !== ( $item = readdir( $handle ) ) ) { 
            if ( $item != "." && $item != ".." ) { 
                if (is_dir($dirName."/".$item)) { 
                    delFileUnderDir($dirName."/".$item); 
                } else {//开源代码phpfensi.com 
                    if( unlink($dirName."/".$item)){
                        echo "成功删除文件：".$dirName."/".$item."<br />n";
                    } 
                } 
            } 
        } 
        closedir( $handle ); 
    } 
}


function addFileToZip($path,$zip){
    $path=iconv("utf-8","gb2312",$path);
    $handler=@opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        //$filename=iconv("gb2312","utf-8",$filename); 
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                addFileToZip($path."/".$filename, $zip);
            }else{ //将文件加入zip对象
                $zip->addFile($path."/".$filename);
            }
        }
    }
    
}

function Directory($path){ 
	echo "$path";
    if (is_dir($path)){  
		//echo "对不起！目录 " . $path . " 已经存在！";
	}else{
		//第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
		$res=mkdir(iconv('gbk', 'utf-8', $path),0777,true); 
		if ($res){
			//echo "目录 $path 创建成功";
		}else{
			//echo "目录 $path 创建失败";
		}
	}
}

function createDir($aimUrl, $mode = 0777) {
    $aimUrl = str_replace('', '/', $aimUrl);
    $aimDir = '';
    $arr = explode('/', $aimUrl);
    foreach ($arr as $str) {
        $aimDir .= $str . '/';
        if (!file_exists($aimDir)) {
            mkdir(iconv( 'gbk','utf-8',$aimDir), $mode);
        }
    }
}


function packImg($basepath,$dir){
    $zip = new \ZipArchive;
    $zipname = $dir.'.zip';
    echo $basepath."/".$zipname;
    if($zip->open($basepath."/".$zipname, ZIPARCHIVE ::CREATE)=== TRUE){
        addFileToZip($basepath.$dir."/", $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
        //删除文件夹
        @closedir($basepath."/".$dir);
        delFileUnderDir($basepath."/".$dir);
       
        //下载zip文件
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
    
        header("Content-Length: " . filesize($zipname));
        header("Content-Disposition: attachment; filename=\"" . basename($zipname) . "\"");
    
        readfile($basepath."/".$dir."/".$zipname);
        exit;
    }else{
        exit('无法打开文件，或者文件创建失败');
    }
} 
?>
