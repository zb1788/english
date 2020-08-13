<?php
namespace Subject\Service;
use Think\Model;
use Think\Log;
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class UserService
{
    //获取用户的生词本
    public function getUserWordBook($user,$sortid){
        $userwordbook=M("user_word_book");
        if(empty($sortid)){
           $data=$userwordbook->where("username='%s' and localareacode='%s' and usertype='%s' and isdel=1",$user["username"],$user["localareacode"],$user["usertype"])->field(" `localareacode`,`usertype`,`username`,`word`,`explains`,max(`id`) as id,`base_wordid`,`base_explainsid`,max(`addtime`) as addtime,max(times) as times,'0' as ischecked")->group("`localareacode`,`usertype`,`username`,`word`,`explains`,`base_wordid`,`base_explainsid`")->order("times desc,addtime desc")->select();
        }else if($sortid==0){
           $data=$userwordbook->where("username='%s' and localareacode='%s' and usertype='%s' and isdel=1",$user["username"],$user["localareacode"],$user["usertype"])->field(" `localareacode`,`usertype`,`username`,`word`,`explains`,max(`id`) as id,`base_wordid`,`base_explainsid`,max(`addtime`) as addtime,max(times) as times,'0' as ischecked")->group("`localareacode`,`usertype`,`username`,`word`,`explains`,`base_wordid`,`base_explainsid`")->order("times desc,addtime desc")->select();
        }else if($sortid==1){
           $data=$userwordbook->where("username='%s' and localareacode='%s' and usertype='%s' and isdel=1",$user["username"],$user["localareacode"],$user["usertype"])->field(" `localareacode`,`usertype`,`username`,`word`,`explains`,max(`id`) as id,`base_wordid`,`base_explainsid`,max(`addtime`) as addtime,max(times) as times,'0' as ischecked")->group("`localareacode`,`usertype`,`username`,`word`,`explains`,`base_wordid`,`base_explainsid`")->order("word,addtime desc")->select();
        }
        $ret["data"]=$data;
        $ret["count"]=count($data);
        return $ret;
    }


    //获取用户的生词本分页数据
    public function getUserWordBookByNum($user,$start,$num){
        $userwordbook=M("user_word_book");
        $data=$userwordbook->where("username='%s' and usertype='%s' and localareacode='%s'",$user["username"],$user["usertype"],$user["localareacode"])->order("times desc,addtime desc")->limit($start,$num)->select();
        $ret["data"]=$data;
        $ret["count"]=count($data);
        return $ret;
    }

    //删除生词本
    public function delUserWordBook($id){
        $userwordbook=M("user_word_book");
        $data=$userwordbook->where("id=%d",$id)->setField("isdel",0);
        $ret["data"]=$data;
        return $ret;
    }

    //删除生词本
    public function delUserWordBookByWordInfo($user,$arr){
        $userwordbook=M("user_word_book");
        $rs=$userwordbook->where("username='%s' and usertype='%s' and localareacode='%s' and base_wordid=%d and base_explainsid=%d and word='%s' and explains='%s' and isdel=1",$user["username"],$user["usertype"],$user["localareacode"],$arr["base_wordid"],$arr["base_explainsid"],$arr["word"],$arr["explains"])->setField("isdel",0);
        return $rs;
    }

    //清空生词本
    public function clearUserWordBook($user){
        $userwordbook=M("user_word_book");
        $data=$userwordbook->where("username='%s' and usertype='%s' and localareacode='%s'",$user["username"],$user["usertype"],$user["localareacode"])->delete();
        $ret["data"]=$data;
        return $ret;
    }

    //收藏生词本目前收藏的生词本直接将id以及word存进去防止基础库数据删除
    public function collectUserWordBook($user,$arr){
        $userwordbook=M("user_word_book");
        //首先判断用户在这个模块下面是否收藏过单词如果收藏过那么更新时间将time字段进行加1
        //如果没有收藏那么添加这个单词
        $rs=$userwordbook->where("username='%s' and usertype='%s' and localareacode='%s' and source=%d and sourceid=%d and base_wordid=%d and base_explainsid=%d and word='%s' and explains='%s' and isdel=1",$user["username"],$user["usertype"],$user["localareacode"],$arr["source"],$arr["sourceid"],$arr["base_wordid"],$arr["base_explainsid"],$arr["word"],$arr["explains"])->find();
        if(!empty($arr["base_wordid"])){
            if(empty($rs)){
                $userwordbook->username=$user["username"];
                $userwordbook->localareacode=$user["localareacode"];
                $userwordbook->usertype=$user["usertype"];
                $userwordbook->source=$arr["source"];
                $userwordbook->sourceid=$arr["sourceid"];
                $userwordbook->base_wordid=$arr["base_wordid"];
                $userwordbook->base_explainsid=$arr["base_explainsid"];
                $userwordbook->word=$arr["word"];
                $userwordbook->explains=$arr["explains"];
                $userwordbook->addtime=$arr["addtime"];
                $userwordbook->add();
            }else{
                $userwordbook->addtime=Date("Y-m-d H:i:s");
                $userwordbook->times=$rs["times"]+1;
                $userwordbook->where("id=%d",$rs["id"])->save();
            }
        }
    }


    //用户的浏览记录
    public function setUserLearnRecord($user,$data,$num){
        //如果传过来的是index是-1
        $userWordBatch=M("user_word_batch");
        $ks_code=$data["ks_code"];
        $wcount=M("word")->where("ks_code='%s' and isdel=1",$ks_code)->count();
        //每组多少单词
        $index=$data["index"];
        if($index=='-1'){
            $isql="insert into engs_user_word_batch (`username`,`usertype`,`localareacode`,`ks_code`,`index`,`type`,`addtime`,`wordnum`) value ('%s','%s','%s','%s','%s','%s','%s','%s','%s')";
            $ret=$userWordBatch->execute($isql,$user["username"],$user["usertype"],$user["localareacode"],$data["ks_code"],$index,$data["type"],Date("Y-m-d H:i:s"),0);
        }else{
            $userWordBatch->username=$user["username"];
            $userWordBatch->usertype=$user["usertype"];
            $userWordBatch->localareacode=$user["localareacode"];
            $userWordBatch->ks_code=$data["ks_code"];
            $userWordBatch->index=$data["index"];
            $userWordBatch->type=$data["type"];
            $userWordBatch->addtime=Date("Y-m-d H:i:s");
            $ret=$userWordBatch->add();
        }
        return $ret;
    }

    //获取用户的学习记录
    public function getUserLearnRecord($user){
        $userWordBatch=M("user_word_batch");
        $rs=$userWordBatch->where("username='%s' and usertype='%s' and localareacode='%s'",$user["username"],$user["usertype"],$user["localareacode"])->select();
        return $rs;
    }

    //获取用户当前所在的学习位置
    public function getUserCurLearnPosition($user,$index,$type,$ks_code){
        $userWordBatch=M("user_word_batch");
        if($index==-1){
            $rs=$userWordBatch->where("username='%s' and type=%d and usertype='%s' and localareacode='%s' and ks_code='%s'",$user["username"],$type,$user["usertype"],$user["localareacode"],$ks_code)->order("id desc")->find();
        }else{
            $rs=$userWordBatch->where("username='%s' and `index`=%d and type=%d and usertype='%s' and localareacode='%s' and ks_code='%s'",$user["username"],$index,$type,$user["usertype"],$user["localareacode"],$ks_code)->order("id desc")->find();
        }
        return $rs;
    }

    //用户完成学习
    public function setUserLearnOver($id,$ub){
        $userWordBatch=M("user_word_batch");
        $time=Date("Y-m-d H:i:s");
        $rs=$userWordBatch->where("id=%d",$id)->find();
        $data["isfinish"]=1;
        $data["submittime"]=$time;
        $data["time"]=strtotime($time)-strtotime($rs["addtime"]);
        $data["ub"]=$ub;
        
        //ub接口调用成功的情况下才获取UB
        //if($ub>0){
        //    $ret=getUbiInterface("u_pass_check_zxzs",$ub,"z");
        //    if($ret[0]['result'] == 1){
        //        $data["suc"]=1;//成功
        //        //$userWordBatch->where("id=%d",$id)->save($data);
        //    }else{
        //        $data["suc"]=0;//失败
        //    }
        //}else{
        //    $data["suc"]=2;//表示不送UB
        //}
        $userWordBatch->where("id=%d",$id)->save($data);
    }

    //用户浏览单词的数量
    public function setUserWordLearnRecord($user,$data){
        $userWordAnswer=M("user_word_answer");
        //先把所有的1全部置为过时
        $userWordAnswer->where("username='%s' and usertype='%s' and localareacode='%s' and batchid='%s' and base_wordid='%s' and base_explainsid='%s' and sourceid='%s' and source='%s' and questype='%s'",$user["username"],$user["usertype"],$user["localareacode"],$data["batchid"],$data["base_wordid"],$data["base_explainsid"],$data["sourceid"],$data["source"],$data["questype"])->setField("isstate",0);
        $userWordAnswer->username=$user["username"];
        $userWordAnswer->usertype=$user["usertype"];
        $userWordAnswer->localareacode=$user["localareacode"];
        $userWordAnswer->batchid=$data["batchid"];
        $userWordAnswer->base_wordid=$data["base_wordid"];
        $userWordAnswer->base_explainsid=$data["base_explainsid"];
        $userWordAnswer->source=$data["source"];
        $userWordAnswer->sourceid=$data["sourceid"];
        $userWordAnswer->submittime=Date("Y-m-d H:i:s");
        $userWordAnswer->score=$data["score"];
        $userWordAnswer->answer=$data["answer"];
        $userWordAnswer->questype=$data["questype"];
        $userWordAnswer->add();
    }


    //设置用户版本
    public function setUserConfig($gradeid,$subjectid,$termid,$versionid,$user){
        $config=M("config");
        //避免同一数据进行二次插入
        $data["userid"]=$user["username"];
        $data["usertype"]=$user["usertype"];
        $data["areacode"]=$user["areacode"];
        $data["localareacode"]=$user["localareacode"];
        $data["subjectid"]=$subjectid;
        $data["gradeid"]=$gradeid;
        $data["termid"]=$termid;
        $data["versionid"]=$versionid;
        $data["configtime"]=Date("Y-m-d H:i:s");
        $config->add($data);
        echo M()->getLastSql();exit;
    }

    //用户是否回答这个试题$data是数组
    public function isSubmitBySourceAndBatch($user,$batchid,$data){
        $in=implode(",",$data);
        $sql="select count(distinct sourceid) as num from ".C('DB_PREFIX')."user_word_answer t where t.username='%s' and t.usertype='%s' and t.localareacode='%s' and t.batchid=%d and t.score=1 and t.isstate=1 and t.sourceid in (".$in.")";
        $rs=M()->query($sql,$user["username"],$user["usertype"],$user["localareacode"],$batchid);
        $count=$rs[0]["num"];
        $ret=false;
        if($count==0){
            $ret=false;
        }else{
            if($count==count($data)){
                $ret=true;
            }else{
                $ret=false;
            }
        }
        return $ret;
    }

    //设置当前是够正确
    public function setCurUserQuestionAnswerState($batchid,$state){
        $user_word_batch=M("user_word_batch");
        $user_word_batch->where("id=%d",$batchid)->setField("curanswerstate",$state);
    }



    //生词本批词
    public function setUserWordBookBatch($user,$datas){
        $user_word_batch=M("user_word_batch");
        $data["username"]=$user["username"];
        $data["usertype"]=$user["usertype"];
        $data["localareacode"]=$user["localareacode"];
        $data["ks_code"]="0";
        $data["index"]=-1;
        $data["type"]=-1;
        $data["addtime"]=Date("Y-m-d H:i:s");
        $data["source"]=1;
        $data["wordnum"]=count($datas);
        $data["wordlist"]=json_encode($datas);
        $id=$user_word_batch->add($data);
        $ret["id"]=$id;
        return $ret;
    }

    //获取总排行榜
    public function getTotalRank($user,$pageCurrent,$pageSize,$type,$time){
        $username = $user["username"];
        $usertype = $user["usertype"];
        $localareacode = $user["localareacode"];
        $classid= $user["classid"];
        $schoolid= $user["schoolid"];

        //时间范围
        $times = "";
        if($time == 'w'){
            $times = date("Y-m-d",strtotime("-7 day"));
        }else if($time == 'm'){
            $times = date("Y-m-d",strtotime("-30 day"));
        }

        
        //类型选择
        $classid = "";
        $schoolid = "";
        if($type == 'class'){
            $classid = $userclassid;
            $schoolid = "";
        }else if($type == 'school'){
            $classid = "";
            $schoolid = $userschoolid;
        }
        //从redis中获取数据
        $rankService = D("Subject/Rank","Service");
        $result = $rankService -> getUserWordRank($classid,$schoolid,$times);
        $my = array();
        $myflag = true;
        foreach($result as $key=>$value){
            $result[$key]["rank"]=($key+1);
            $result[$key]["total"]=-1;
            if($username == $value["username"] && $myflag){
                $my = $value;
                $my["rank"]=$key+1;
                $myflag = false;
            }
        }

        $data["info"] = $result;
        //数组的组合和兼容
        $hasRank = true;
        if(my["total"] == 0){
            $hasRank = false;
        }
        $my['mytotal'] = $my["total"];
        $my['myub'] = $my["ub"];
        $my['myRank'] = $my["rank"];
        $data["myinfo"] = $my;
        return $data; 

        // //默认从cookie取classid和schoolid
        // if(empty($classid)){
        //     if($usertype == 2){
        //         //教师
        //         $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "2" AND username="%s" ';
        //     }else{
        //         //学生或者家长
        //         $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "4" AND username="%s" ';
        //     }
        //     $data_user = M()->query($sql_user,$localareacode,$username);
        //     $schoolid = $data_user[0]['schoolid'];
        //     $classid = $data_user[0]['classid'];
        // }

        // //查询当前第几页的数据
        // $sql = 'SELECT "-1" as total,sum(m.ub) ub,n.truename,n.userpic,n.classname,n.schoolname,n.username FROM db_english.engs_user_word_batch m,db_english.engs_userinfo n';

        // $sql_where = ' where m.username=n.username AND m.localareacode=n.localareacode AND m.usertype=n.usertype AND n.isdel=0 and m.submittime is not null  ';


        // // $sql = 'SELECT "-1" AS total,SUM(m.ub)ub,n.truename,n.userpic,n.classname,n.schoolname,m.username,m.localareacode,m.usertype FROM db_english.engs_user_word_batch m LEFT JOIN (SELECT * FROM db_english.engs_userinfo n WHERE n.`isdel`=0) AS n ON m.`username`=n.username AND m.`usertype`=n.usertype AND m.`localareacode`=n.localareacode ';
        // // $sql_where = ' where n.truename is not null and m.submittime is not null  ';


        // if($type == 'class'){
        //     $sql_where .= ' AND n.localareacode = "'.$localareacode.'" and n.classid="'.$classid.'" ';
        // }else if($type == 'school'){
        //     $sql_where .= ' AND n.localareacode = "'.$localareacode.'" and n.schoolid = "'.$schoolid.'" ';
        // }else if($type == 'all'){
        //     $sql_where .= '';
        // }
        // if($time == 'w'){
        //     $sql_where .= ' AND DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(m.submittime) ';
        //     $sql_time =  ' AND DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(m.submittime) ';
        // }else if($time == 'm'){
        //     $sql_where .= ' AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(m.submittime) ';
        //     $sql_time = ' AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(m.submittime) ';
        // }else if($time == 'a'){
        //     $sql_where .= '';
        //     $sql_time = '';
        // }
        // $sql_rank_beg = 'SELECT tmp.*,@rank :=@rank+1 as rank FROM ((';
        // $sql_rank_beg .= $sql.$sql_where;
        // $sql_rank_beg .= ' GROUP BY m.username,m.localareacode,m.usertype ';
        // $sql_rank_beg .= ' ORDER BY sum(m.ub) DESC,max(m.submittime) ,n.truename  ';
        // $sql_rank_beg .= ' limit '.($pageCurrent-1)*$pageSize.','.$pageSize;
        // $sql_rank_beg .=  ') tmp,(SELECT @rank :='.($pageCurrent-1)*$pageSize.') b)';
        // $sqls=$sql_rank_beg.$sql.$sql_where.$sql_group.$sql_order.$sql_limit.$sql_rank_end;
        // $data_info = M()->query($sql_rank_beg);

        // $sql_group = ' GROUP BY m.username,m.localareacode,m.usertype ';
        // $sql_order = ' ORDER BY sum(m.ub) DESC,max(m.submittime) ,n.truename  ';

        // if($pageCurrent>1){
        //     $data['myinfo'] = '';
        // }else{
        //     $data['myinfo'] = $this->getMyRank($user,$classid,$sql_time,$sql,$sql_where,$sql_group,$sql_order);
        // }
        // $data['info'] = $data_info;
        // return $data;
    }

    //获取我的排名
    public function getMyRank($user,$classid,$sql_time,$sql,$sql_where,$sql_group,$sql_order){
        $my = array();

        if($user["usertype"] == 2){
            //教师
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "2" AND username="%s" and classid="%s"';
        }else{
            //学生或者家长
            $sql_user = 'SELECT userpic,truename,classid,classname,schoolid,schoolname FROM db_english.engs_userinfo WHERE isdel=0 AND localareacode="%s" AND usertype = "4" AND username="%s" and classid="%s"';
        }

        $data_user = M()->query($sql_user,$user["localareacode"],$user["username"],$classid);


        $my['classname'] = $data_user[0]['classname'];
        $my['truename'] = $data_user[0]['truename'];
        $my['schoolname'] = $data_user[0]['schoolname'];
        $my['userpic'] = $data_user[0]['userpic'];

        //查询我的当前u币数和篇数
        $sql_my = 'SELECT "-1" as total,sum(case when m.ub is null then 0 else m.ub end) as ub FROM db_english.engs_user_word_batch m,db_english.engs_userinfo n';
        $sql_my_where = ' and m.username="'.$user["username"].'"';
        $data_my = M()->query($sql_my.$sql_where.$sql_my_where.$sql_time);//加where条件

        if($data_my[0]['ub'] == 0||empty($data_my)||empty($data_my[0]['ub'])){
            //没有u币
            $hasRank = false;
            $mytotal = 0;
            $myub = 0;
        }else{
            $myub = $data_my[0]['ub'];
            $mytotal = $data_my[0]['total'];
            $hasRank = true;
        }

        $my['hasRank'] = $hasRank;
        $my['mytotal'] = $mytotal;
        $my['myub'] = $myub;


        //获取我的当前排名
        if($hasRank){
            //如果U币和篇数都相同，就按名字排序
            $sql_having_same = ' having sum(m.ub)='.$myub;
            $data_is_same = M()->query($sql.$sql_where.$sql_group.$sql_having_same.$sql_order);
            if(empty($data_is_same)){
                //没有相同的
                $is_same = false;
            }else{
                //都相同按名字
                $is_same = true;
                $arr_index = array_column($data_is_same,'username');
                $index = array_search($user["username"], $arr_index);
            }

            $sql_having = ' having sum(m.ub)>'.$myub;
            $sql_my_rank = $sql.$sql_where.$sql_group.$sql_having;
            $data_my_rank = M()->query($sql_my_rank);
            if(empty($data_my_rank)){
                if($is_same){
                    $myrank = 1+$index;
                }else{
                    $myrank = 1;
                }
            }else{
                if($is_same){
                    $myrank = count($data_my_rank)+1+$index;
                }else{
                    $myrank = count($data_my_rank)+1;
                }
            }
        }else{
            $myrank = 0;
        }

        $my['myRank'] = $myrank;

        return $my;
    }


    //获取用户信息
    public function getUserinfoByUser($user){
        $userinfo=M("userinfo");
        $rs=$userinfo->where("username='%s' and localareacode='%s' and usertype='%s' and isdel=0",$user["username"],$user["localareacode"],$user["usertype"])->find();
        return $rs;
    }

    //获取用户的书籍列表
    public function getUserBookList($user){
        $config=M();
        $sql="select t.*,(SELECT detail_name_short FROM engs_rms_dictionary WHERE dictionary_code='edition' AND detail_code=t.`versionid`) AS version_name,";
        $sql=$sql."(SELECT detail_name_short FROM engs_rms_dictionary WHERE dictionary_code='volume' AND detail_code=t.`termid`) AS term_name,";
        $sql=$sql."(SELECT detail_name_short FROM engs_rms_dictionary WHERE dictionary_code='grade' AND detail_code=t.`gradeid`) AS grade_name,";
        $sql=$sql."(SELECT detail_name_short FROM engs_rms_dictionary WHERE dictionary_code='subject' AND detail_code=t.`subjectid`) AS subject_name,";
        $sql=$sql."concat('http://192.168.151.208/',s.`pic_path`) as imgSrc from engs_config t left join engs_version_img s on t.`subjectid` = s.`r_subject` and t.`termid` = s.`r_volume` ";
        $sql=$sql."AND t.`versionid` = s.`r_version` AND t.`gradeid` = s.`r_grade` where t.`userid` = '41010110054500' and t.`usertype` = '4' and ";
        $sql=$sql."t.`localareacode` = '13.' and t.`subjectid`='0003' order by t.`configtime` ";
        $rs=M()->query($sql,$user["username"],$user["usertype"],$user["localareacode"]);
        return $rs;
    }

    //获取用户的版本
    public function getUserConfig($user,$subjectid,$gradeid){
        $rs=M("config")->where("userid='%s' and usertype=%d and localareacode='%s' and subjectid='%s'",$user["username"],$user["usertype"],$user["localareacode"],$subjectid)->order("id desc")->find();
        if(empty($rs)){
            $rs=getUserConfig($subjectid,$gradeid,"0","0",$user);
        }
        return $rs;
    }

    //用户是否已经送过UB
    public function isUserGetUB($batchid,$user){
        $userWordBatch=M("user_word_batch");
        $batchrs=$userWordBatch->where("id=%d",$batchid)->find();
        $rs=$userWordBatch->where("username='%s' and ks_code='%s' and usertype=%d and localareacode='%s' and `index`=%d and type=%d and submittime is not null and ub>0",$user["username"],$arr["ks_code"],$user["usertype"],$user["localareacode"],$batchrs["index"],$batchrs["type"])->find();
        if(empty($rs)){
            return 0;
        }else{
            return 1;
        }

    }

    //获取用户的记录计数
    public function getWordUnit($arr,$user,$moduleid){
        $sql="SELECT t.ks_code,COUNT(*) as userdata,sum(case when t.username='%s' then 1 else 0 end) as isdo FROM engs_user_modules_unit_log t WHERE t.ks_code IN (SELECT s.`ks_code`FROM engs_rms_unit s WHERE s.`r_grade` = '%s'AND s.`r_subject` = '%s'AND s.`r_volume` = '%s'AND s.`r_version` = '%s') AND t.moduleid = '%s' GROUP BY t.ks_code ORDER BY NULL";
        $rs=M()->query($sql,$user["username"],$arr["gradeid"],$arr["volumnid"],$arr["versionid"],$arr["subjectid"],$moduleid);
        return $rs;
    }

    //通过用户和单元以及模块信息获取用户的浏览信息 参数isunitclassfy表示是否按照单元进行分类
    public function getUserUnitLogList($user,$array,$module,$isunitclassfy=true){
        //ini_set('mongo.long_as_object', 1);
        //$mongo_config = C("DB_MONGO");
        //$db_prefix = $mongo_config["db_prefix"];
        $userModel = M('user_modules_unit_log');
        $map = array();
        //如果有单元信息的情况
        if(!empty($array)){
            foreach($array as $key=>$value){
                $map[$key] = $value;
            }
        }
        if(!empty($user)){
            $map["username"] = $user["username"];
            $map["usertype"] = $user["usertype"];
            $map["localareacode"] = $user["localareacode"];
        }

        if(!empty($module)){
            $map["moduleid"] = $module["moduleid"];
        }
        //用户和单元必须不能为空
            
        if($isunitclassfy){
            $result = $userModel -> where($map) -> group("ks_code") ->field("ks_code,count(distinct username) as usercount") -> select();
        }else{
            $result = $userModel -> where($map) -> group("ks_code,chapterid") ->field("ks_code,chapterid,count(distinct username) as usercount,sum(case when username='".$user["username"]."' then 1 else 0 end) as isdo ") -> select();
        }
        return $result;
    }


    //通过用户和单元以及模块信息获取用户的浏览信息 参数isunitclassfy表示是否按照单元进行分类
    public function getUserUbStageList($user,$array,$module,$isunitclassfy=true){
        //ini_set('mongo.long_as_object', 1);
        //$mongo_config = C("DB_MONGO");
        //$db_prefix = $mongo_config["db_prefix"];
        $userModel = M('db_math.user_stage',"sx_");
        $map = array();
        //如果有单元信息的情况
        if(!empty($array)){
            foreach($array as $key=>$value){
                $map[$key] = $value;
            }
        }
        //用户和单元必须不能为空
        if(!empty($user)){
            $map["username"] = $user["username"];
            $map["usertype"] = $user["usertype"];
            $map["localareacode"] = $user["localareacode"];
            $result = $userModel -> where($map) ->where("genreid != stageid") ->where("stageid != 1")-> group("genreid,stageid") ->field("genreid,stageid,sum(ub) as usercount") -> select();
        }else{
            $return = array();
            Log::record('user和module都不能为空参数信息不对');
        }
        return $result;
    }
}
