<?php

namespace Home\Controller;

use Think\Controller;
class ZipdownloadController extends Controller {
    //返回单题模板下载地址
	public function que_download(){
      $data["versionid"] = "v1.0";
      $data["downurl"] = PROTOCOL."://".WEB_DOMAIN."/Home/Zipdownload/singlezip";
      $this->ajaxReturn($data);
	}
    //单题模板下载地址
	public function singlezip(){
        $exportPath  = "./public/que_mould/";
		$zipName = './public/downloadzip/singleque.zip';
        $zip=new \ZipArchive();
        if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)!==TRUE) {
            exit('无法打开文件，或者文件创建失败');
        }
        $openFile = opendir($exportPath);
        createZip($openFile,$zip,$exportPath);
        @closedir($exportPath);
        $zip->close();//关闭
        if(!file_exists($zipName)){
            exit("无法找到文件"); //即使创建，仍有可能失败
        }
        //如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
        // header("Cache-Control: public");
        // header("Content-Description: File Transfer");
        // header('Content-disposition: attachment; filename='.basename($zipName)); //文件名
        // header("Content-Type: application/zip"); //zip格式的
        // header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        // header('Content-Length: '. filesize($zipName)); //告诉浏览器，文件大小
        // @readfile($zipName);
        downloadzip($zipName);
	} 

    //套卷模板下载地址
    public function examszip(){
        $examsid = I("examsid/d",0);
        $username = I("username/s","0");
        $truename = I("truename/s","0");
        $tqms = I("tqms/s","0");
        $questions=array();
        //查询套卷内容开始
        //查询听力训练
        $examsinfo = M("exams")->field("name,ks_code")->where("id=".$examsid)->find();
        $examsname = $examsinfo["name"];
        $ks_code = $examsinfo["ks_code"];
        
        $sql="select st.id as stemid,st.parentid,t.itemtype,st.question_playtimes,st.content as stemcontent,st.parentid,t.id,t.`itemtype`,t.`typeid`,t.`tcontent`,t.`questions_playtimes`,t.`questions_answer`,t.`questions_items`,t.`questions_tts` from engs_exams_questions t left join engs_exams_stem st on t.stemid=st.id where st.examsid='".$examsid."' and st.isdel = 1 and t.isdel = 1 order by st.parentid,st.id,t.id";
        //echo $sql;exit;
        $eqrs=M()->query($sql);
        if(!empty($eqrs)){
            foreach($eqrs as $key=>$value){
                unset($temp);
                $temp["name"]="听力训练";
                $temp["id"]=$value["id"];
                $temp["contentid"]=$value["id"];
                $temp["itemtype"]=$value["itemtype"];
                $temp["question_playtimes"]=$value["question_playtimes"];
                $temp["index"]=$key+1;
                $temp["count"]=count($eqrs);
                $temp["type"]=5;
                $temp["typeid"]=$value["typeid"];
                $temp["stemid"]=$value["stemid"];
                $temp["parentid"]=$value["parentid"];
                $temp["typeid"]=$value["typeid"];
                $temp["display"]=$value["itemtype"];
                //$temp["encontent"]=$value["tcontent"];
                $temp["questype"]=0;
            //在这里替换题干内容
                if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"]))
                {
                    preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["tcontent"],$match);
                    $picurl = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$match[1];
                    $picsavename = basename($match[1]);
                    $picsaveurl = "./public/batch_mould/mp3_pic/".$picsavename;
                    $downresult =  downloadfile($picurl,$picsaveurl);
                    $value["tcontent"]="<p><img class='tgimg' src = 'mp3_pic/".$picsavename."'></p>";
                }
                $temp["encontent"]=$value["tcontent"];
                //进行替换
                $temp["cncontent"]="";
                if(preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"]))
                {
                    preg_match('/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/',$value["stemcontent"],$match);
                    $picurl = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/".$match[1];
                    $picsavename = basename($match[1]);
                    $picsaveurl = "./public/batch_mould/mp3_pic/".$picsavename;
                    $downresult =  downloadfile($picurl,$picsaveurl);
                    $value["stemcontent"]="<p><img class='tgimg' src = 'mp3_pic/".$picsavename."'></p>";       
                }
                $temp["stemcontent"]=$value["stemcontent"];

                if($value["parentid"]!='null'&&$value["parentid"]!=0&&$value["parentid"]!=''){
                    $childrs=$this->getParentStem($value["parentid"]);
                    $temp["cncontent"]=$childrs[0]["content"];
                    $temp["questype"]=1;
                }
                $temp["remark"]="";
                $temp["answer"]="";
                $questions_answer=(array)json_decode(urldecode($value["questions_answer"]));
                //$temp["answerss"]=$questions_answer;
                $questions_tts=(array)json_decode(urldecode($value["questions_tts"]));
                $questions_items=(array)json_decode(urldecode($value["questions_items"]));
                $items=array();
                foreach($questions_items as $keys=>$values){
                    $item["flag"]=$values->flag;
                    if($temp["itemtype"]=='1'&&$value["typeid"]==1){
                       $picurl = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/".$values->content;
                       $picsavename = basename(PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/uploads/".$values->content);
                       $picsaveurl = "./public/batch_mould/mp3_pic/".$picsavename;
                       $downresult =  downloadfile($picurl,$picsaveurl);
                       $item["content"]="mp3_pic/".$picsavename;
                    }else{
                      $item["content"]=$values->content;
                    }
                    $item["iserror"]=0;
                    $item["pd_answer"]=0;
                    if($value["typeid"]==1){
                      if($item["flag"]==$questions_answer["answer"]){
                        $item["iserror"]=1;
                        $temp["answer"]=$values->flag;
                      }
                    }else if($value["typeid"]==3){

                      if($values->content=='True'){
                        $item["content"]="√";
                      }else{
                        $item["content"]="×";
                      }
                      if($values->content=='True'&&$questions_answer["answer"]==1){
                        $item["iserror"]=1;
                        $temp["answer"]=$values->flag;
                        $item["pd_answer"]="√";
                        
                      }else if($values->content=='False'&&$questions_answer["answer"]==0){
                        $item["iserror"]=1;
                        $temp["answer"]=$values->flag;
                        $item["pd_answer"]="×";
                      }
                    }
                    array_push($items,$item);
                }
                $ttss=array();
                foreach($questions_tts as $keys=>$values){
                    $tts["before"]=$values->flag_content;
                    $tts["content"]=$values->tts_content;
                    $tts["playmp3"]="mp3_pic/".$values->tts_mp3.".mp3";
                   // $tts["mp3url"]=PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_exam/".substr($values->tts_mp3,0,2)."/".$values->tts_mp3.".mp3";
                    $tts["stoptime"]=$values->tts_stoptime;
                    array_push($ttss,$tts);
                    $fileurl = PROTOCOL."://".RESOURCE_DOMAIN."/yylmp3/mp3_exam/".substr($values->tts_mp3,0,2)."/".$values->tts_mp3.".mp3";
                    $saveurl = "./public/batch_mould/mp3_pic/".$values->tts_mp3.".mp3";
                    $downresult =  downloadfile($fileurl,$saveurl);
                }
                $temp["tts"]=$ttss;
                $temp["audiobaseroot"]=C("exams_mp3_path");
                $temp["items"]=$items;
                array_push($questions, $temp);
               // var_dump($questions);exit;
            }
            $data["name"] = "听力训练";
            $data["num"] = count($questions);
            $data["examsid"] = $examsid;
            $data["examsname"] = $examsname;
            $data["tqms"] = $tqms;
            $data["ks_code"] = $ks_code;
            $data["username"] = $username;
            $data["truename"] = $truename;
            $data["saveurl"] = PROTOCOL."://".WEB_DOMAIN."/Home/Device/batchSaveAnswer";
            $data["questions"] = $questions;
            
        }
        $htmlstr = file_get_contents("./public/batch_mould/mould.html");
      //  echo("old=".$htmlstr);
        $htmlstr = str_replace('$$result$$', json_encode($data), $htmlstr);
       // echo($htmlstr);
        //$filestr = '<script type="text/javascript">var result_json = \''.json_encode($data).'\'</script>';
        file_put_contents("./public/batch_mould/index.html", $htmlstr);
        //file_put_contents("./public/batch_mould/json/batch.json",json_encode($data));
        //查询套卷内容结束 
        $exportPath  = "./public/batch_mould/";
        $zipName = './public/downloadzip/'.$examsid.'.zip';
        $zip=new \ZipArchive();
        if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)!==TRUE) {
            exit('无法打开文件，或者文件创建失败');
        }
        $openFile = opendir($exportPath);
        createZip($openFile,$zip,$exportPath);
        @closedir($exportPath);
        $zip->close();//关闭
        if(!file_exists($zipName)){
            exit("无法找到文件"); //即使创建，仍有可能失败
        }
        //如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
        // header("Cache-Control: public");
        // header("Content-Description: File Transfer");
        // header('Content-disposition: attachment; filename='.basename($zipName)); //文件名
        // header("Content-Type: application/zip"); //zip格式的
        // header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        // header('Content-Length: '. filesize($zipName)); //告诉浏览器，文件大小
        // @readfile($zipName);
         downloadzip($zipName);
    } 

    private function getParentStem($parentid){
      //组合试题的问题
      $sql="select * from engs_exams_stem t where  t.id =".$parentid;
      $childrs=M()->query($sql);
      return $childrs;
    }
}


