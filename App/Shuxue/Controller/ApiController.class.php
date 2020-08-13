<?php
namespace Shuxue\Controller;
use Think\Controller;


class ApiController extends Controller {

    public $config = array(
        'df' => array(
            '0'=>array("begintime"=>"2019-10-18 09:00:00","endtime"=>"2019-10-18 09:30:00"),
            '1'=>array("begintime"=>"2019-10-18 10:10:00","endtime"=>"2019-10-18 10:40:00")
        )
    );

    //设置用户的年级信息
    public function setUserGrade(){
        $username = I("username");
        $grade = I("grade");
        $ret = array();
        if(empty($username) || empty($grade)){
            $ret["code"] = 0;
            $ret["msg"] = "缺少参数";
        }
        $users = M("df_users");
        $rs = $users -> where("username='%s'",$username) -> find();
        if(empty($rs)){
            $rs = $users -> add(["username"=>$username,"grade"=>$grade]);
            if($rs){
                $ret["code"] = 1;
                $ret["msg"] = "插入成功";
                cookie("gradeid",$grade);
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "插入失败";
            }
        }else{
            $rs = $users -> where("username='%s'",$username) -> setField("grade",$grade);
            if($rs){
                $ret["code"] = 1;
                $ret["msg"] = "更新成功";
                cookie("gradeid",$grade);
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "更新失败";
            }
        }
        $this->ajaxReturn($ret);
    }

    //获取用户信息
    public function getUser(){
        $username = I("username");
        if(empty($username)){
            $ret["code"] = 0;
            $ret["msg"] = "缺少参数";
        }
        $users = M("df_users");
        $rs = $users -> where("username='%s'",$username) -> find();
        $this->ajaxReturn($rs);
    }





    //用户开始答题
    public function startExams(){
        $username = I("username");
        //将用户名写入cookie中
        cookie("username",$username);
        $type = I("type");
        if($type !=0 && $type !=1){
            $ret["code"] = 1;
            $ret["msg"] = "演练";
        }else{
            $time = Date("Y-m-d H:i:s");
            $users = M("df_users");
            $rs = $users -> where("username='%s'",$username) -> setField(array("starttime".$type=>$time,"type"=>$type));
            if($rs){
                $ret["code"] = 1;
                $ret["msg"] = "更新成功";
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "更新失败";
            }
        }
        $this->ajaxReturn($ret);
    }

    //用户结束答题
    public function submitExams(){
        $username = cookie("username");
        $time = Date("Y-m-d H:i:s");
        $users = M("df_users");
        //计算用户用时
        $rs = $users -> where("username='%s'",$username) -> find();
        $type = $rs["type"];
        if(empty($rs["starttime0"]) && empty($rs["starttime1"])){
            $ret["code"] = 1;
            $ret["msg"] = "演练不提交";
        }else{
            $usertime = 0;
            if(!empty($rs["score".$type])){
                $usertime = strtotime($time) - strtotime($rs["starttime".$type]);
            }
            $rs = $users -> where("username='%s'",$username) -> setField(array("submittime".$type=>$time,"usertime".$type=>$usertime));
            echo M()->getLastSql();
            if($rs){
                $ret["code"] = 1;
                $ret["msg"] = "更新成功";
            }else{
                $ret["code"] = 0;
                $ret["msg"] = "更新失败";
            }
        }
        $this->ajaxReturn($ret);
    }

    //检测答题时间是否开始
    public function checkExam(){
        $username = I("username");
        $users = M("df_users");
        //$df = C("df");
        $df = $this->config["df"];

        $rs = $users -> where("username='%s'",$username) -> find();
        $ret = array();
        $ret["isup"] = $rs["isup"];
        $type = $rs["type"];
        if($type == 0){
            if(empty($rs["starttime".$type])){
                if($df[$type]["begintime"] > Date("Y-m-d H:i:s")){
                    $ret["data"] = array(0,0);
                    $ret["msg"] = "尚未开始";
                }else{
                    if($df[$type]["endtime"] < Date("Y-m-d H:i:s")){
                        $ret["data"] = array(2,0);
                        $ret["msg"] = "";
                    }else{
                        $ret["data"] = array(1,0);
                        $ret["msg"] = "";
                    }
                }
            }else{

                if(empty($rs["submittime".$type])){
                    $ret["data"] = array(1,0);
                    $ret["msg"] = "";
                    if(Date("Y-m-d H:i:s") > $df[$type]["endtime"]){
                        //同时更新用户数据
                        $rs = $users -> where("username='%s'",$username) -> setField(array("submittime".$type=>$df[$type]["endtime"],"usertime".$type=>(strtotime($df[$type]["endtime"])-strtotime($rs["starttime".$type]))));
                        $ret["data"] = array(2,0);
                        $ret["msg"] = "第一次开始时间已经结束";
                        if(Date("Y-m-d H:i:s")>$df[1]["endtime"]){
                            $ret["data"] = array(2,2);
                            $ret["msg"] = "第一次开始时间已经结束";
                        }
                    }
                }else{
                    $ret["data"] = array(2,0);
                    $ret["msg"] = "第一次开始已经结束,请内心等待";
                    //如果到了第二次开始的时间那么开始考试
                    if($rs["isup"] == 1 && Date("Y-m-d H:i:s")< $df[1]["begintime"]){
                        $ret["data"] = array(2,0);
                        $ret["msg"] = "第二次考试尚未开始,请内心等待";
                    }else if($rs["isup"] == 0 && Date("Y-m-d H:i:s")< $df[1]["begintime"]){
                        $ret["data"] = array(2,0);
                        $ret["msg"] = "对不起您没有进入第二次考试";
                    }else if($rs["isup"] == 1 && Date("Y-m-d H:i:s")> $df[1]["begintime"]){
                        $ret["data"] = array(2,1);
                        $ret["msg"] = "";
                        if(!empty($starttime) && ($rs["type"] == 0)){
                            //$rs = $users -> where("username='%s'",$username) -> setField(array("starttime".$type=>"","submittime"=> "","time"=>0,"type"=>1,'score'=>0));
                        }
                    }
                }
            }
        }else{
            if(empty($rs["submittime".$type])){
                $ret["data"] = array(2,1);
                $ret["msg"] = "";
                if(Date("Y-m-d H:i:s") > $df[$type]["endtime"]){
                    //同时更新用户数据
                    $rs = $users -> where("username='%s'",$username) -> setField("submittime".$type,Date("Y-m-d H:i:s"));
                    $ret["data"] = array(2,2);
                    $ret["msg"] = "第二次开始时间已经结束,请内心等待";
                }
            }else{
                $ret["data"] = array(2,2);
                $ret["msg"] = "第二次开始已经结束,请内心等待";
            }
        }
        $this->ajaxReturn($ret);
    }

    //获取开始列表
    public function getExamList(){
        $username = I("username");
        $sql = "select engs_df_users.grade as usergrade,engs_df_stage.* from engs_df_stage left join engs_df_users on engs_df_stage.grade = engs_df_users.grade  where username = '%s' order by engs_df_stage.type";
        $result = M()->query($sql,$username);
        $ret = array();
        if(empty($result["usergrade"])){
            $ret["code"] = 0;
            $ret["msg"] = "请选择年级";
        }else{
            $ret["code"] = 1;
            $ret["msg"] = "";
            $ret["data"] = $result;
        }
        $this->ajaxReturn($ret);
    }


    //获取试题
    public function getQuesView(){
        $type=I("type/d",0);
        //$grade = I("grade/s","0002");
        $username = cookie("username");
        $user = M("df_users") -> where("username='%s'",$username) ->find();
        $grade = $user["grade"];
        $df = $this->config["df"];
        //计算当前时间
        $totaltime = strtotime($df[$type]["endtime"]) - strtotime(Date("Y-m-d H:i:s"));
        if($type == 2){
            $grade = "0001";
            $totaltime = "50";
        }
        //数据直接推动到redis中进行操作
        $sql = "select b.*,s.`stagename`,'".$totaltime."' as totaltime,s.remark,s.id as stageid from sx_df_stage s left join sx_df_question q on s.id = q.stageid left join sx_df_base_question b on q.quesid=b.id where s.type=%d and gradeid='%s' order by b.id";
        $rs = M()->query($sql,$type,$grade);
        $this->ajaxReturn($rs);
    }

    //用户答题
    public function addUserAnswer(){
        $username = cookie("username");
        $stageid = I("stageid/d");
        $isright = I("isright/d");
        if($isright != 0 ){
            $isright = 1;
        }
        $quesid = I("quesid/d");
//        $username = "vcomyy";
//        $isright = 1;
//        $stageid = 1;
//        $quesid = 111;
        if($stageid == 1197){
            $this->ajaxReturn(array("suc"=>1,"msg"=>"chenggong"));
        }
        $userAnswer = M("df_user_answer");
        $rs = $userAnswer -> where("username='%s' and quesid = %d and stageid = %d",$username,$quesid,$stageid)->find();
        $users = M("df_users");
        $userRs = $users -> where("username = '%s'",$username) -> find();
        $type = $userRs["type"];
        $isadd = false;
        if(empty($rs)){
            $data["username"] = $username;
            $data["stageid"] = $stageid;
            $data["quesid"] = $quesid;
            $data["isright"] = $isright;
            $data["addtime"] = Date("Y-m-d H:i:s");
            $rs = $userAnswer -> add($data);
            if($isright == 1){
                $isadd = true;
            }
        }else{
            if($rs["isright"] != $isright){
                $isadd = true;
            }
            $data["isright"] = $isright;
            $data["updatetime"] = Date("Y-m-d H:i:s");
            $rs = $userAnswer -> where("username='%s' and stageid=%d and quesid = %d",$username,$stageid,$quesid)->save($data);
        }
        //同时更新用户信息

        if(!empty($rs)){
            if($isadd) {
                $users = M("df_users");
                $isright = ($isright == 0)?-1:1;
                $users->execute("update sx_df_users set score".$type." = score".$type." + ".$isright." where username = '" . $username . "' ");
                echo M()->getLastSql();exit;
            }
            $this->ajaxReturn(array("suc"=>1,"msg"=>"更新成功"));
        }else{
            $this->ajaxReturn(array("suc"=>0,"msg"=>"更新失败"));
        }
    }

    public function getRank(){
        $gradeid=I("gradeid/s","0004");
        $type = I("type/d",0);
        if($type == 0){
            $users = M("df_users");
            $rs = $users -> where("starttime".$type." is not null and grade='%s' and isdf = 1",$gradeid) -> order("score".$type." desc,usertime".$type." desc,id") -> select();
            $this->ajaxReturn($rs);
        }else if($type == 2){
            $users = M("df_users");
            $rs = $users -> where("isprize = 1  and isdf = 1 and grade='%s'",$gradeid) -> order("score1 desc,usertime1,id") -> select();
            $this->ajaxReturn($rs);
        }else if($type == 1){
            $users = M("df_users");
            $rs = $users -> where("starttime".$type." is not null and grade='%s' and isdf = 1 and isup =1 ",$gradeid) -> order("score".$type." desc,usertime".$type.",id") -> select();
            $this->ajaxReturn($rs);
        }
    }

    public function updateUserData(){
        $users = M("df_users");
        $data = I("data");
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data,true);
        foreach($res as $key=>$value){
            $id = $value["id"];
            M("users") -> where("id=%d",$id) -> setField("isup",1);
        }
    }

    //查看用户信息
    public function getUserList(){
        $users = M("df_users");
        $rs = $users  -> select();
        $this->ajaxReturn($rs);
    }

    //签到
    public function sign(){
        $username = I("username/s");
        $truename = I("truename/s");
        $schoolname = I("schoolname/s");
        $truename = urldecode(urldecode($truename));
        $schoolname = urldecode(urldecode($schoolname));
        if(empty($username) || empty($truename)){
            $this->ajaxReturn(["status"=>0,"msg"=>"参数错误"]);
        }
        $users = M("df_users");
        $rs = $users -> where("username='%s'",$username) -> find();
        if(empty($rs)){
            $rs = $users -> add(["username"=>$username,"truename"=>$truename,"schoolname"=>$schoolname,"issign"=>1]);
            if(empty($rs)){
                $this->ajaxReturn(["status"=>0,"msg"=>"插入失败"]);
            }else{
                $this->ajaxReturn(["status"=>1,"msg"=>"成功"]);
            }
        }else{
            $rs = $users -> where("username = '%s'") -> setField(["truename"=>$truename,"schoolname"=>$schoolname,"issign"=>1]);
            $this->ajaxReturn(["status"=>1,"msg"=>"更新成功"]);
        }
    }

//    public function doData(){
//        $dfbase = M("df_base_question");
//        $base = M("base_question");
//
//        $data = M()->query("SELECT * FROM sx_df_base_question t WHERE t.`genreid` <> 473");
//
//        foreach($data as $key=>$value){
//            $id = $value["id"];
//            $basers = $base -> where("id = %d",$id) -> find();
//            if(!empty($basers)){
//                $content = $basers["content"];
//                $dfbase -> where("id=%d",$id) -> setField("content",$content);
//            }
//        }
//    }
}

