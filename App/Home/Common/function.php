
<?php
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v)
    {
        if (gettype($v) == 'resource')
        {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array')
        {
            $obj[$k] = (array)object_to_array($v);
        }
    }
 
    return $obj;
}
//替换试卷中图片地址
function get_examsimg_url($str){
    $resource_path = C('resource_path');
    $replacestr = str_replace('/uploads',$resource_path.'uploads',$str);
    return $replacestr;
}

 function createZip($openFile,$zipObj,$sourceAbso,$newRelat = '')  
   {  
        while(($file = readdir($openFile)) != false)  
        {  
            if($file=="." || $file=="..")  
                continue;  
              
            /*源目录路径(绝对路径)*/  
            $sourceTemp = $sourceAbso.'/'.$file;  
            /*目标目录路径(相对路径)*/  
            $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;  
            if(is_dir($sourceTemp))  
            {  
                //echo '创建'.$newTemp.'文件夹<br/>';  
                $zipObj->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/  
                createZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);  
            }  
            if(is_file($sourceTemp))  
            {  
                //echo '创建'.$newTemp.'文件<br/>';  
                $zipObj->addFile($sourceTemp,$newTemp);  
            }  
        }  
  }  

  function downloadfile($filepath,$savepath){
    if(!file_exists($savepath)){
        $filestr = file_get_contents($filepath);
        if($filestr){
            if(file_put_contents($savepath,$filestr)){
                $data["saveflag"] = true;
                $data["savemsg"] = "保存成功 || ".$savepath;
            }
            else{
                $data["saveflag"] = false;
                $data["savemsg"] = "保存失败 || ".$savepath;
            }
            $data["downflag"] = true;
            $data["downmsg"] = "下载成功 || ".$filepath;
        }
        else{
            $data["downflag"] = false;
            $data["downmsg"] = "下载失败 || ".$filepath;
        }
    }
    else{
        $data["downflag"] = true;
        $data["downmsg"] = "下载成功 || ".$filepath;
        $data["saveflag"] = true;
        $data["savemsg"] = "保存成功 || ".$savepath;
    }
    return $data;
  }

function downloadzip ($filename, $showname='',$expire=180) {
      if(file_exists($filename)){
        $length = filesize($filename);
      }elseif(is_file(UPLOAD_PATH.$filename)){
        $filename = UPLOAD_PATH.$filename;
        $length = filesize($filename);
      }else {
        throw_exception($filename.L('下载文件不存在！'));
      }
      if(empty($showname)){
        $showname = $filename;
      }
      $showname = basename($showname);
      if(empty($filename)){
        $type = mime_content_type($filename);
      }else{
        $type = "application/octet-stream";
      }
      ob_end_clean();


    $ua = $_SERVER["HTTP_USER_AGENT"];
    $encoded_filename = urlencode($showname);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    
    if (preg_match("/MSIE/", $ua)) {
      header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
      header('Content-Disposition: attachment; filename*="utf8\'\'' . $showname . '"');
    } else {
      header('Content-Disposition: attachment; filename="' . $showname . '"');
    }


      //发送Http Header信息 开始下载
     // header("content-type:text/html; charset=utf-8");
     header('Content-Type: application/octet-stream');
      header("Pragma: public");
      header("Cache-control: max-age=".$expire);
      //header('Cache-Control: no-store, no-cache, must-revalidate');
      header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
      //下面一行就是改动的地方，即用iconv("UTF-8","GB2312//TRANSLIT",$showname)系统函数转换编码为gb2312 
    //不管用了
      //header("Content-Disposition: attachment; filename=". iconv("UTF-8","gb2312",$showname));
      header("Content-Length: ".$length);
      //header("Content-type: ".$type);
      header('Content-Encoding: none');
      header("Content-Transfer-Encoding: binary" );
      ob_clean();

      readfile($filename);
      //exit();
    }
?>