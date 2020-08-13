<?php

namespace Subject\Controller;

use Think\Controller;

/**
 * Index控制器
 */
class DfController extends Controller {

    //登封诗词大赛参赛人数
    public function index() {
       $query = M();
       $sql = "select std_id from engs_df_paper_res  group by std_id";
       $res = $query->query($sql);
       $data["count"] = count($res);
       //$data["count"] = 50;
       $this->ajaxReturn($data);
    }

    public function getmyrank(){
      //echo "sssssssss";
      $username = I("username/s","0");
      $schoolId = I("schoolId/s","0");
     //echo $username;
      $query = M();
      $sql = "select sum(paper_scores) as totalscore, std_id from engs_df_paper_res where  paper_scores is not null and group_id = '".$schoolId."' group by  std_id order by totalscore";
      $res = $query->query($sql);
      // echo $sql;exit;
      $key = array_search($username,array_column($res, 'std_id'));
      // echo $key;exit;
      if($key >= 0 && $key != ''){
        // echo "ssss".$key;exit;
        $data["myrank"] = $key+1;
        $data["totalscore"] = $res[$key]["totalscore"];
      }
      else{
        $data["myrank"] = 0;
        $data["totalscore"] = 0;
      }
      $this->ajaxReturn($data);
    }

    public  function get_difficulty()
    {
      $username = I("username/s","0");
      $paper_res = M("df_paper_res");

    }
    public function getpaper(){
      $username = I("username/s","testtest");
      $gradeCode = I("gradeCode/s","0006");
      $paper = M("df_paper");
      $user_paper_res = M("df_paper_res");
      $paperList = $paper->field("paper_id,paper_name,difficulty")->order("difficulty")->where("grade_code<='%s'",$gradeCode)->select();
      $paperListChunk = array_chunk($paperList,120);
      $newPaperList = array();
      $newpapeStatus = array();
      $url = "https://tqmszz.zzedu.net.cn/online/interface/getSummerPaperStatus.action";
      foreach ($paperListChunk as $key => $value) {
        $paperId = '';
        foreach ($value as $skey => $svalue) {
          $paperId .= $svalue["paper_id"].",";
        }   
        $paperId = rtrim($paperId, ",");
        $papeStatus = $this->curl_post_contents($url,$username,$paperId);    
        $papeStatus = json_decode($papeStatus, true);
        foreach ($papeStatus as $pkey => $pvalue) {
          array_push($newpapeStatus,$pvalue);
        }    
      }
      foreach ($paperList as $key => $value) {
        $statusInfo = $this->get_paper_status($value["paper_id"], $newpapeStatus);
        if($statusInfo["flag"]){
          if($statusInfo["status"] != "1"){
            array_push($newPaperList,$value);
          }
        }
      }
      shuffle($newPaperList);
      shuffle($newPaperList);
      $paper_info = $newPaperList[0];
      $this->ajaxReturn($paper_info);
      // echo count($newpapeStatus);
    //  var_dump($newPaperList);
    

    }
    public function get_paper_status($paper_id, $newpapeStatus) {
      $res['flag']= false;
      foreach ($newpapeStatus as $key => $value) {
          # code...
          if($paper_id == $value["PAPER_ID"] ){
            $res['flag']= true;
            $res['status']= $value["STATUS"];
            $res['paper_id']= $value["PAPER_ID"];
           break;
          }
          else{
            continue;
          }
      }
      return $res;
  }
    //排行榜
    public function getRankInfo(){
      $username = I("username/s","0");
      // $gradeCode = I("gradeCode/s","0001");
      $schoolId = I("schoolId/s","0001");
      // echo $schoolId;
      //1-2年级排行
      $sql1 = "select std_id,std_name,sum(paper_scores) as totalscore from engs_df_paper_res where  paper_scores is not null and group_id = '".$schoolId."' and grade_code <= '0002'  group by std_id order by totalscore desc limit 20";
      // echo $sql;
      $res1 = M()->query($sql1);
      foreach ($res1 as $key => $value) {
     
        $res1[$key]["std_name"] = str_replace("的家长","",$value["std_name"]);
      }
      //3-4年级排行
      $sql2 = "select std_id,std_name,sum(paper_scores) as totalscore from engs_df_paper_res where  paper_scores is not null and group_id = '".$schoolId."' and grade_code <= '0004' and grade_code > '0002'  group by std_id order by totalscore desc limit 20";
      // echo $sql;
      $res2 = M()->query($sql2);
      foreach ($res2 as $key => $value) {
     
        $res2[$key]["std_name"] = str_replace("的家长","",$value["std_name"]);
      }
      //5-6年级排行
      $sql3 = "select std_id,std_name,sum(paper_scores) as totalscore from engs_df_paper_res where  paper_scores is not null and group_id = '".$schoolId."' and grade_code <= '0006' and grade_code > '0004'  group by std_id order by totalscore desc limit 20";
      // echo $sql;
      $res3 = M()->query($sql3);
      foreach ($res3 as $key => $value) {
     
        $res3[$key]["std_name"] = str_replace("的家长","",$value["std_name"]);
      }
    //1-2登封市排行
      $sql4 = "select std_id,std_name,sum(paper_scores) as totalscore from engs_df_paper_res where paper_scores is not null  group by std_id order by totalscore desc limit 20";
      $res4 = M()->query($sql4);
      foreach ($res4 as $key => $value) {
     
        $res4[$key]["std_name"] = str_replace("的家长","",$value["std_name"]);
      }

      //1-2登封市学校排行
      $sql5 = "select a.group_name,a.group_id,a.totalscore,b.num,c.student_num from (select group_name,group_id,sum(paper_scores) as totalscore from engs_df_paper_res where paper_scores is not null group by group_id) a left join (select group_id, count(distinct std_id) as num from engs_df_paper_res group by group_id) b on a.group_id = b.group_id LEFT JOIN engs_df_school c ON a.group_id = c.school_id order by a.totalscore desc limit 20";
      $res5 = M()->query($sql5);
      foreach ($res5 as $key => $value) {
        if($value['num']/$value['student_num'] > 1){
          $scale =  (1*100).'%';
        }
        else{
          $scale = ((sprintf("%.2f",($value['num']/$value['student_num'])))*100).'%';
        }
        $res5[$key]['scale'] = $scale;
      }
      $schoolkey = array_search($schoolId,array_column($res5, 'group_id'));
      // echo $schoolkey;
      if($schoolkey >= 0 && $schoolkey != ''){
        // echo $schoolkey;
        $data["schoolrank"] = $schoolkey+1;
        
      }
      else{
        $data["schoolrank"] = 0;
      }

      $data["gradeRank1"] = $res1;
      $data["gradeRank2"] = $res2;
      $data["gradeRank3"] = $res3;
      $data["gradeRank4"] = $res4;
      $data["gradeRank5"] = $res5;
      // var_dump(array_search('410185001083',array_column($res5, 'group_id')));
      // var_dump(array_search($schoolId,array_column($res5, 'group_id'))+1);
      $this->ajaxReturn($data);
    }

    public function getHistoryPaper(){
      $username = I("username/s","0");
      $user_paper_res = M("df_paper_res");
      // $paper_list = $paper->field("paper_id,paper_name,ifficulty")->order("ifficulty")->select();
      //查询该用户有作答但没有提交的卷子
      // $user_paper_list = $user_paper_res->field("paper_id,paper_name")->where("std_id='%s' and paper_scores is not null and paper_id in(select paper_id from engs_df_paper)",$username)->group("paper_id")->order("end_time desc")->select();
      
      $sql = "select a.paper_id,a.paper_name,a.end_time,a.paper_scores,b.difficulty from engs_df_paper_res a left join engs_df_paper b on a.paper_id = b.paper_id where a.std_id='".$username."' and a.paper_scores is not null group by a.paper_id order by a.end_time";
      // echo $sql;
      $user_paper_list = M()->query($sql);
      foreach ($user_paper_list as $key => $value) {
        if(!empty($value["end_time"])){
          $user_paper_list[$key]["save_time"] = substr($value["end_time"],4,2)."月".substr($value["end_time"],6,2)."日";
          switch ($value["difficulty"]) {
            case "1":
            $user_paper_list[$key]["difficulty_info"] = "容易";
              break;
            case "2":
              $user_paper_list[$key]["difficulty_info"] = "较易";
              break;
            case "3":
              $user_paper_list[$key]["difficulty_info"] = "一般";
              break;
            case "4":
              $user_paper_list[$key]["difficulty_info"] = "较难";
              break;
            case "5":
              $user_paper_list[$key]["difficulty_info"] = "困难";
              break;
            default:
              
         }
        }   
      }
      // var_dump($user_paper_list);
      $this->ajaxReturn($user_paper_list);
    }

    //post发送json数据
  function curl_post_contents($url,$username,$paperId) {
    $ch = curl_init();
    $post_fields['studentId'] = $username;
    $post_fields['paperId'] = $paperId;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));//POST的数据大于1024时的处理
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
  }
  public function getDynamicInfo(){
    $paper = M("df_paper_res");
    $paperList = $paper->field("std_name,paper_name,group_name,class_name,paper_scores")->where("paper_scores is not null")->limit(20)->order("end_time desc")->select();
    // echo $paper->getLastSql();
    $this->ajaxReturn($paperList);
  }
}
