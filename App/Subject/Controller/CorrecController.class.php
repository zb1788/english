<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class CorrecController extends CheckController {
    //宣传页
    public function entrance(){
        $gradeid = I('gradeid/s','0007');
        cookie('gradeid',$gradeid);
        $backUrl=I('backUrl/s','no');
        if(!empty($backUrl) || $backUrl != 'no'){
            session('backUrl',$backUrl);
        }
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',session('backUrl'));
       // echo $backUrl;
        //echo "sss";
        $this->display("entrance");
    }
    //首页
    public function index() {
        $gradeid = I('gradeid/s','0007');
        $backUrl=I('backUrl/s');
        $id = I('id/d',0);
        if($id > 0){
            $writing = M('writing_result');
            $data = $writing->field('content')->where('id='.$id)->find();
            $content = $data['content'];
        }
        else{
            $content = session('usercontent');
            $issubmit = session('issubmit');
        }
        
        if(empty($issubmit)){
            $issubmit = 0;
        }
        if(empty($content)){
            $content = '';
        }
        if(ceil($gradeid) <= 7){
            $type = "ZHONGKAO";
        }
        else{
            $type = "GAOKAO";
        }
        $truecontent= $content;
        $content = urlencode($content);
        $this->assign('type',$type);
        $this->assign('content',$content);
        $this->assign('truecontent',$truecontent);
        $this->assign('id',$id);
        $this->assign('issubmit',$issubmit);
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',$backUrl);
        $this->display("index");
    }
    public function tempcontent(){
        $content = $_POST['content'];
        session('usercontent',$content);
        session('issubmit',0);
    }
    function curl_post($imgbase64){
        try{
            //$name = time().rand(1000, 9999);
            $upload_path = C('recordpath');
		    $base64_image_content = $_POST['imgbase64'];
			//匹配出图片的格式
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
				$type = $result[2];
			
				$new_file = $upload_path."test.{$type}";
				$imgname = "test.{$type}";
				if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
				
				}else{
				
				}
			}         
        }catch (Exception $e) {
            exit;
        }
        //cookie('newfile',$new_file);
        $para = array (
                'token' => 'UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c' ,
                'img' => new \CURLFile(realpath('./recordwav/test.jpeg')),
                'isOneLine' => 0
        );
        //$para = http_build_query($para);
        $url = "http://open.smartstudy.com/api/handwriting";
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 过滤HTTP头
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,true); // post传输数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$para);// post传输数据

        $responseText = curl_exec($ch);
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
        curl_close($ch);
        $return_data = json_decode($responseText);
        $this->ajaxReturn($return_data);
        //echo '11111111111111111';
    }
    public function writing(){
       $content = $_POST['content'];
       $type = $_POST['type'];
       $id = $_POST['id'];
       $submittype = $_POST['submittype'];
       $param = array(
        'token'=>"UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c",
        'content'=>$content,
        'type'=>$type
        );
        $url = 'http://open.smartstudy.com/api/grading/writing';
        $ch = curl_init($url);
        $param = http_build_query($param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);// post传输数据
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
        curl_close($ch);
        session('writing_relult',$result);
        session('usercontent',$content);
         if($submittype == 0){
            session('issubmit',1);
            $return_data = json_decode($result);
            $title = substr($content,0,20);
            $writing = M('writing_result');
             if($id == 0){
                $username=cookie("username");
                $areacode=cookie("areacode");
                $localareacode=cookie("localAreaCode");
                $truename=cookie("truename");
                $schoolid=cookie("schoolid");
                $gradeid=cookie("gradeid");
                $usergradeid=cookie("usergradeid");
                $classid=cookie("classid");
                $usertype=cookie('usertype');
                if($username == '' || $username=='null' || $username == null){
                    $username=cookie("engm_username");
                    $areacode=cookie("engm_areacode");
                    $truename=cookie("engm_truename");
                    if($username == '' || $username=='null' || $username == null){
                        $username='hxp';
                        $truename='hxp';
                        $areacode='1.1.1';
                    }
                }
                $writing->username = $username;
                $writing->areacode = $areacode;
                $writing->truename = $truename;
                $writing->localareacode = $localareacode;
                $writing->schoolid = $schoolid;
                $writing->gradeid = $gradeid;
                $writing->usergradeid = $usergradeid;
                $writing->classid = $classid;
                $writing->usertype = $usertype;
                $writing->title = $title;
                $writing->content = $content;
                $writing->score = $return_data->data->score;
                $writing->submitnum = 1;
                $writing->submittime = date("Y-m-d H:i:s");
                $id = $writing->add();
            }
            else{
                $addscore_arr = $writing->field('score')->where('id = '.$id)->find();
                $addscore = ceil($return_data->data->score)-ceil($addscore_arr['score']);
                $writing->content =$content;
                $writing->score = $return_data->data->score;
                $writing->addscore =$addscore;
                $writing->submittime = date("Y-m-d H:i:s");
                $writing->where('id = '.$id)->save();
                $writing->where('id = '.$id)->setInc('submitnum',1);
            }
         }
        
        $this->ajaxReturn(json_decode('{"id":'.$id.'}'));
    }
    public function baogao(){
        $id = I('id/d',0);
        $gradeid = I('gradeid/s','0007');
        $backUrl=I('backUrl/s');
        $username=cookie("username");
        $areacode=cookie("areacode");
        $truename=cookie("truename");
        $result = session('writing_relult');
        $return_data = json_decode($result);
        //var_dump($return_data);
        $data = $return_data->data;
        $score = ceil($data->score);
        if($score >= 85){
            $dengji = 'A';
        }
        else if($score >= 75 && $score <= 84){
            $dengji = 'B';
        }
        else if($score >= 60 && $score <= 74){
            $dengji = 'C';
        }
        else if($score >= 31 && $score <= 59){
            $dengji = 'D';
        }
        else{
            $dengji = 'E';
        }
        $this->assign('score',$score);
        $this->assign('dengji',$dengji);
        $this->assign('truename',$truename);
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',$backUrl);
        $this->assign('id',$id);
        $this->display("baogao");

    }
    public function detail(){
        $gradeid = I('gradeid/s','0007');
        $backUrl=I('backUrl/s');
        $result = session('writing_relult');
        $content = session('usercontent');
        $return_data = json_decode($result);
        $radar = $return_data->data->radar;
        $nryd = $radar->内容要点;
        $wzzs = $radar->文章字数;
        $dlhf = $radar->段落划分;
        $xjlg = $radar->衔接连贯;
        $chsy = $radar->词汇使用得当;
        $chpx = $radar->词汇拼写;
        $chnd = $radar->词汇难度;
        $yufa = $radar->语法;
        $nryd = ceil($nryd*100);
        $wzzs = ceil($wzzs*100);
        $dlhf = ceil($dlhf*100);
        $xjlg = ceil($xjlg*100);
        $chsy = ceil($chsy*100);
        $chpx = ceil($chpx*100);
        $chnd = ceil($chnd*100);
        $yufa = ceil($yufa*100);
        $htmlBuff = '';
        $errReaBuff = '';
        $tempContent = '';
        $defColor ="icj bDefault";
        $yellowColor ="icj bYellow";
        $markList = $return_data->data->marks;
        $newmarkarrtemp = [];
        $newmarkarr = [];
        foreach($markList as $val) {
            $key = $val->start;
            if(!isset($newmarkarrtemp[$key])) {
                $newmarkarrtemp[$key] = $val;
            } 
        }
        foreach($newmarkarrtemp as $val) {
            $key = $val->end;
            if(!isset($newmarkarr[$key])) {
                $newmarkarr[$key] = $val;
            } 
        }
        $start = 0;
        $end = 0;
        $previousEnd = 0;
        $exp = "<a href=\"#\" onclick=\"commDialog('{2}','{3}','{4}','{5}');return false;\" class=\"tanLine {fontColor}\"><i class=\"{color}\">{0}</i>{1}</a>";
        $markindex = 0;
       foreach ($newmarkarr as $mark1) {
           $start = $mark1->start;
           $end = $mark1->end;
           if ($markindex == 0) {
             $htmlBuff.= mb_substr($content,0,$start,'utf-8');
            } else {
                $htmlBuff.= mb_substr($content,$previousEnd,($start-$previousEnd),'utf-8');
            }
            $tempContent = str_replace("{0}",($markindex+1),$exp);
            $tempContent = str_replace("{1}",mb_substr($content,$start,($end-$start),'utf-8'),$tempContent);
            $tempContent = str_replace("{2}",$mark1->isPositive,$tempContent);
            $tempContent = str_replace("{3}",$mark1->type,$tempContent);
            $tempContent = str_replace("{4}",$mark1->text,$tempContent);
            if($mark1->isPositive){
            //近义词的处理
                $explainList = $mark1->explain;
                $subExplainList = $mark1->subExplain;
                $explain = '';
                foreach ($explainList as $explainkey => $explainvalue) {
                    if($explainkey == 0){
                        $explain = $explainvalue;
                    }
                    else if($explainkey == (count($explainList)-1)){
                        $explain .= "," .$explainvalue."<br/>";
                    }
                    else{
                        $explain .= ",".$explainvalue;
                    }
                }
                foreach ($subExplainList as $subExplainkey => $subExplainvalue) {
                    $explain .= $subExplainvalue;
                }
                $tempContent = str_replace("{5}",$explain,$tempContent);
                $tempContent = str_replace("{color}",$defColor,$tempContent);
                $tempContent = str_replace("{fontColor}","",$tempContent);
            }
            else{
                //错误的处理
                $typeExplainList = $mark1->typeExplains;
                $explainList = $mark1->explain;
                $typeExplain = '';
                foreach ($typeExplainList as $typeExplainkey => $typeExplainvalue) {
                   if($typeExplainkey == 0){
                        $typeExplain .= "<br/><br/>".$typeExplainvalue;
                   }
                   else{
                       $typeExplain .= ",".$typeExplainvalue;
                   }
                }
                $tempContent = str_replace("{5}",$typeExplain,$tempContent);
                $tempContent = str_replace("{color}",$yellowColor,$tempContent);
                $tempContent = str_replace("{fontColor}","t2",$tempContent);
            }
            $htmlBuff .= $tempContent;
            $previousEnd = $end;
            // 如果最后一个，加上结尾
            if($markindex == (count($newmarkarr)-1)){
                $htmlBuff.= mb_substr($content,$previousEnd,strlen($content),'utf-8');
            }
            $markindex++;
       }
       //发生错误时的评语
       $errReaList = $return_data->data->errorReasons;
       $errorReason = "";
       foreach ($errReaList as $errReakey => $errReavalue) {
           $errorReason .= $errReavalue;
       }
        $subCommentsList = $return_data->data->subComments;
        $subItems1 = $subCommentsList->{1};
        $subItems2 = $subCommentsList->{2};
        $subItems3 = $subCommentsList->{3};
        $subItems4 = $subCommentsList->{4};
        $commentList = $return_data->data->comments;
        $this->assign('nryd',$nryd);
        $this->assign('truename',$truename);
        $this->assign('wzzs',$wzzs);
        $this->assign('dlhf',$dlhf);
        $this->assign('xjlg',$xjlg);
        $this->assign('chsy',$chsy);
        $this->assign('chpx',$chpx);
        $this->assign('chnd',$chnd);
        $this->assign('yufa',$yufa);
        $this->assign('htmlBuff',$htmlBuff);
        $this->assign('errorReason',$errorReason);
        $this->assign('subItems1',$subItems1);
        $this->assign('subItems2',$subItems2);
        $this->assign('subItems3',$subItems3);
        $this->assign('subItems4',$subItems4);
        $this->assign('subCommentsList',$subCommentsList);
        $this->assign('commentList',$commentList);
        $this->assign('isOffTopic',$return_data->data->isOffTopic);
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',$backUrl);
        //echo $subItems1['name'];
 //var_dump($subItems1);
        $this->display("detail");
    }
//批改记录
    public function writing_result(){
        $gradeid = I('gradeid/s','0007');
        $backUrl=I('backUrl/s');
        $writing = M('writing_result');
        $username=cookie("username");
        $truename=cookie("truename");
        $type = I('type/s','ZHONGKAO');
        if($username == '' || $username=='null' || $username == null){
            $username=cookie("engm_username");
            $areacode=cookie("engm_areacode");
            $truename=cookie("engm_truename");
            if($username == '' || $username=='null' || $username == null){
                $username='41010110136163';
                $truename='41010110136163';
                $areacode='1.1.1';
            }
        }
        $data = $writing->field('id,truename,title,score,addscore,content,submitnum,submittime')->order('id desc')->where('username="'.$username.'"')->select();
        $this->assign('result',$data);
        $this->assign('type',$type);
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',$backUrl);
        $this->display('writing_result');
    }

    //接口测试
    public function hxptest(){
    	$para = array (
                'token' => 'UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c' ,
                'img' => new \CURLFile(realpath('2.jpg')),
                'isOneLine' => 0
        );
        //$para = http_build_query($para);
        $url = "http://open.smartstudy.com/api/handwriting";
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,true); // post传输数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$para);// post传输数据

        $responseText = curl_exec($ch);
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
        curl_close($ch);
        var_dump($responseText);
    }
    

    public function hxpwriting(){
       $content = "The diagram indicates the structure and working process of a thermos Mask The thermos flask is used to keep the liquids and remain the temperature. Its most common use is to keep hot drinks, for example coffee, hat but you can also use it to keep things like. A thermos flask 15 all task is a flask made of glass. There is nearly no air ask is a cyinder shaped object between 30 and to om high. The inside of the thermos flask is inside this glass, so it is a very His a very poor conductor. In addition, the glass is silvered which reduces infrared radiation. This vacuum flask is surrounded by another cylinder made of metal or plastic, which protects the fragile glass inside. The top is a plug or cap, usually made of cork or plastic, which is covered by a plastic cup. This is taken off and used used to drink the liquid from some heat escapes from the cap but the special design of the thermos flask means that liquid maintains its temperature for several hours.";
       $param = array(
        'token'=>"UJbVHIXwKF3ca4JOQtLqLo1ajsebRv6c",
        'content'=>$content,
        'type'=>"ZHONGKAO"
        );
        $url = 'http://open.smartstudy.com/api/grading/writing';
        $ch = curl_init($url);
        $param = http_build_query($param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);// post传输数据
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
        curl_close($ch);
        session('writing_relult',$result);
        session('usercontent',$content);
        $return_data = json_decode($result);
        //var_dump($return_data);
        $radar = $return_data->data->radar;
        $nryd = $radar->内容要点;
        $wzzs = $radar->文章字数;
        $dlhf = $radar->段落划分;
        $xjlg = $radar->衔接连贯;
        $chsy = $radar->词汇使用得当;
        $chpx = $radar->词汇拼写;
        $chnd = $radar->词汇难度;
        $yufa = $radar->语法;
        $nryd = ceil($nryd*100);
        $wzzs = ceil($wzzs*100);
        $dlhf = ceil($dlhf*100);
        $xjlg = ceil($xjlg*100);
        $chsy = ceil($chsy*100);
        $chpx = ceil($chpx*100);
        $chnd = ceil($chnd*100);
        $yufa = ceil($yufa*100);
        $htmlBuff = '';
        $errReaBuff = '';
        $tempContent = '';
        $defColor ="icj bDefault";
        $yellowColor ="icj bYellow";
        $markList = $return_data->data->marks;
        $newmarkarrtemp = [];
        $newmarkarr = [];
        foreach($markList as $val) {
            $key = $val->start;
            if(!isset($newmarkarrtemp[$key])) {
                $newmarkarrtemp[$key] = $val;
            } 
        }
        foreach($newmarkarrtemp as $val) {
            $key = $val->end;
            if(!isset($newmarkarr[$key])) {
                $newmarkarr[$key] = $val;
            } 
        }
        //var_dump($newmarkarr);
        $start = 0;
        $end = 0;
        $previousEnd = 0;
        $exp = "<a href=\"#\" onclick=\"commDialog('{2}','{3}','{4}','{5}');return false;\" class=\"tanLine {fontColor}\"><i class=\"{color}\">{0}</i>{1}</a>";
        $markindex = 0;
       // echo   str_replace("aaa",substr($content,90,6),$content);
       foreach ($newmarkarr as $mark1) {
         if($markindex <= 100){
           $start = $mark1->start;
           $end = $mark1->end;
           if ($markindex == 0) {
             $htmlBuff.= mb_substr($content,0,$start,'utf-8');
            } else {
                $htmlBuff.= mb_substr($content,$previousEnd,($start-$previousEnd),'utf-8');
            }
            
            $tempContent = str_replace("{0}",($markindex+1),$exp);
            $tempContent = str_replace("{1}",mb_substr($content,$start,($end-$start),'utf-8'),$tempContent);
            $tempContent = str_replace("{2}",$mark1->isPositive,$tempContent);
            $tempContent = str_replace("{3}",$mark1->type,$tempContent);
            $tempContent = str_replace("{4}",$mark1->text,$tempContent);
            if($mark1->isPositive){
            //近义词的处理
                $explainList = $mark1->explain;
                $subExplainList = $mark1->subExplain;
                $explain = '';
                foreach ($explainList as $explainkey => $explainvalue) {
                    if($explainkey == 0){
                        $explain = $explainvalue;
                    }
                    else if($explainkey == (count($explainList)-1)){
                        $explain .= "," .$explainvalue."<br/>";
                    }
                    else{
                        $explain .= ",".$explainvalue;
                    }
                }
                foreach ($subExplainList as $subExplainkey => $subExplainvalue) {
                    $explain .= $subExplainvalue;
                }
                $tempContent = str_replace("{5}",$explain,$tempContent);
                $tempContent = str_replace("{color}",$defColor,$tempContent);
                $tempContent = str_replace("{fontColor}","",$tempContent);
            }
            else{
                //错误的处理
                $typeExplainList = $mark1->typeExplains;
                $explainList = $mark1->explain;
                $typeExplain = '';
                foreach ($typeExplainList as $typeExplainkey => $typeExplainvalue) {
                   if($typeExplainkey == 0){
                        $typeExplain .= "<br/><br/>".$typeExplainvalue;
                   }
                   else{
                       $typeExplain .= ",".$typeExplainvalue;
                   }
                }
                $tempContent = str_replace("{5}",$typeExplain,$tempContent);
                $tempContent = str_replace("{color}",$yellowColor,$tempContent);
                $tempContent = str_replace("{fontColor}","t2",$tempContent);
            }
            $htmlBuff .= $tempContent;
            $previousEnd = $end;
            // 如果最后一个，加上结尾
            if($markindex == (count($newmarkarr)-1)){
                $htmlBuff.= mb_substr($content,$previousEnd,strlen($content),'utf-8');
            }
            
         }
         $markindex++;
       }
    echo $htmlBuff;
       //var_dump($newmarkarr);exit;
       //发生错误时的评语
       $errReaList = $return_data->data->errorReasons;
       $errorReason = "";
       foreach ($errReaList as $errReakey => $errReavalue) {
           $errorReason .= $errReavalue;
       }
        $subCommentsList = $return_data->data->subComments;
        $subItems1 = $subCommentsList->{1};
        $subItems2 = $subCommentsList->{2};
        $subItems3 = $subCommentsList->{3};
        $subItems4 = $subCommentsList->{4};
        $commentList = $return_data->data->comments;
        $this->assign('nryd',$nryd);
        $this->assign('truename',$truename);
        $this->assign('wzzs',$wzzs);
        $this->assign('dlhf',$dlhf);
        $this->assign('xjlg',$xjlg);
        $this->assign('chsy',$chsy);
        $this->assign('chpx',$chpx);
        $this->assign('chnd',$chnd);
        $this->assign('yufa',$yufa);
        $this->assign('htmlBuff',$htmlBuff);
        $this->assign('errorReason',$errorReason);
        $this->assign('subItems1',$subItems1);
        $this->assign('subItems2',$subItems2);
        $this->assign('subItems3',$subItems3);
        $this->assign('subItems4',$subItems4);
        $this->assign('subCommentsList',$subCommentsList);
        $this->assign('commentList',$commentList);
        $this->assign('isOffTopic',$return_data->data->isOffTopic);
        $this->assign('gradeid',$gradeid);
        $this->assign('backUrl',$backUrl);

        
        //var_dump($return_data);
    }
}
