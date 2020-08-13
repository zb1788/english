<?php
namespace Yuwen\Controller;
use Think\Controller;
class ShareController extends Controller {
    public function share(){
        $date = date('Y-m-d H:i:s');
        $this->assign('now',$date);
        $this->display();
    }

    public function amrTomp3(){
      $filepath = I('filepath/s','');
      $path="/home/yylmp3/recordwav/";
      $yfilename=$path.$filepath;
      //获取扩展名称
      $extname=substr($yfilename, strrpos($yfilename, '.'));

      $outputname=str_replace($extname,'.mp3',$yfilename);

      $returnname = str_replace($extname,'.mp3',$filepath);

      if(file_exists($yfilename)){
        if(!file_exists($outputname)){
			if($extname=='wav'){
				exec("ffmpeg -i ".$yfilename." -f mp2 ".$outputname,$res, $rc);
			}else{
				exec("ffmpeg -i ".$yfilename." ".$outputname,$res, $rc);
			}
            
            if($rc == 0){
                $data['status'] = 'ok';
                $data['msg'] = '转码成功';
            }else{
                $data['status'] = 'error';
                $data['msg'] = '转码失败';
            }
        }else{
            $data['status'] = 'ok';
            $data['msg'] = '已转码过';
        }
      }else{
        $data['status'] = 'error';
        $data['msg'] = '文件不存在';
      }
      $data['filepath'] = $returnname;
      $this->ajaxReturn($data);
    }

}