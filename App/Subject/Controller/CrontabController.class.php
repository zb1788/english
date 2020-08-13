<?php
namespace Subject\Controller;
use Think\Controller;

/**
 * 验证是否登录控制器类
 *
 * @author         gm
 * @since          1.0
 */

class CrontabController extends Controller {

    //初始化处理数据
    //口算王排行榜
    public function handleMathRankData(){
        ignore_user_abort(); // 后台运行
        vendor("Stastic");
        vendor("HeapTree");
        $appbegintime = time();
        echo "--开始处理时间".$appbegintime."--\r\n";
        $stastic = new \Stastic();
        //查询配置信息
        $rankConfig = M()->query("select * from db_math.sx_rank_config ");
        //$rankConfig = json_decode(json_encode($rankConfig),true);
        $configids  = array_column($rankConfig,"id");
        //查询用户前100信息
        $lastUpdateTime = S("lastUpdateTime");
        //没添只处理当前数据库中的数据 采用分页查询的方式
        $count = M()->query("select count(*) as count from db_math.sx_rank_user where isdel = 0");
        $resCount = $count[0]["count"];
        if($resCount == 0){
            return false;
        }
        $resCount = 500;
        $batch = array();
        $ids = array();
        $results = M()->query("SELECT * from db_math.sx_rank_user where isdel=0  order by addtime desc limit 0,1000");
        //每次处理2000条数据
        // for($page = 0;$page<floor($resCount/100);$page++){
        //     $results = M()->query("SELECT * from db_math.sx_rank_user where isdel=0  order by addtime limit ".($page*100).",100");
        //同一批次内用户只处理一次
        foreach($results as $key=>$value){
            $temp = array();
            $temp["id"] = $value["id"];
            if(isset($batch[$value["username"]])){
                if($batch[$value["username"]]["addtime"]<$value["addtime"]){
                    $batch[$value["username"]]["addtime"] = $value["addtime"];
                }
            } else {
                $temp["addtime"] = $value["addtime"];
                $batch[$value["username"]] = $temp;
            }
            array_push($ids,$value["id"]);
        }
        // }
        $batchKeys = array_keys($batch);
        echo "总处理人数".count($batchKeys);
        echo "\r\n";
        //var_dump($ids);
        //分批处理数据每次处理50个

        //这个批次的总用户数量
        $batchCount = count($batch);
        $pageNum = 20;
        for($page = 0;$page<ceil($batchCount/$pageNum);$page++){
            $updateArr = array();
            echo "第".$page."次数处理中";
            $sql = "select *,'1' as levelnum from db_math.sx_user_stage  where yjt_areacode is not null and (";
            $i = 0;
            $sqlwhere = "";
            //计算最后的索引
            $begin = $page*$pageNum;
            $end = ($page+1)*$pageNum>$batchCount?$batchCount:($page+1)*$pageNum;
            for($index=$begin;$index<$end;$index++){
                $key = $batchKeys[$index];
                $value = $batch[$key]["addtime"];
                if($i==0){
                    $sql = $sql." (username = '".$key."' and addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." (username = '".$key."' and addtime<='".$value."')";
                }else{
                    $sql = $sql." or (username = '".$key."' and addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." or (username = '".$key."' and addtime<='".$value."')";
                }
                $i++;
                array_push($updateArr,$batch[$key]["id"]);
            }

            $sql = $sql.")";

            //处理这个批次的代码
            $arr = array();
            $begin = self::getMillisecond();
            $heapMap = array();
            foreach($rankConfig as $key=>$value){
                //组装查询语句
                $day = $value["day"];
                $begintime = $value["begintime"];
                $endtime = $value["endtime"];
                if(empty($day)){
                    if(empty($begintime) && empty($endtime)){

                    }else{
                        if(!empty($begintime) && !empty($endtime)){
                            if(strtotime($begintime)>time() || strtotime($endtime)<time()){
                                continue;
                            }else{
                                $sql = $sql.' and (addtime>="'.$begintime.'" and addtime<"'.$endtime.'")';
                            }
                        }else{
                            if(!empty($begintime)){
                                $sql = $sql.' and addtime>="'.$begintime.'"';
                            }

                            if(!empty($endtime)){
                                $sql = $sql.' and addtime<"'.$endtime.'"';
                            }
                        }
                    }
                }else{
                    $sql = $sql.' and addtime>="'.(date("Y-m-d",strtotime($day." day")).' 00:00:00').'"';
                }

                $batchResult = M()->query($sql);

                //比较的条件
                $condition = $value["condition"];
                //待比较的字段
                $ranktype = $value["ranktype"];
                //堆的大小
                $top = $value["top"];
                //配置的索引
                $configid = $value["id"];
                //排名的范围
                $type = $value["type"];
                //排名的元数据类型
                $cata = $value["cata"];
                $colume = "";

                //分治算法进行数据的聚合
                $usergroup = $stastic->batchSum($batchResult,$cata,$ranktype);



                //查看这个类型的数据
                if($type == 0){

                }else if($type == 1){
                    $colume = "yjt_classid";
                }else if($type == 2){
                    $colume = "yjt_schoolid";
                }
                //遍历组合数据,减少数据库的查询次数
            
                $cataArr = array_column($usergroup,$colume);
                //var_dump($usergroup);exit;
               // var_dump($cataArr) ;exit;

                if(empty($cataArr)){
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from db_math.sx_stage_rank where configid=".$configid." and typeid = 0");
                }else{
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from db_math.sx_stage_rank where configid=".$configid." and typeid in (".$new_projectcode_array.")");
                }
                $ranked = array();
                foreach($result as $key=>$value){
                    $ranked[$value["typeid"]][$value["configid"]] = $value;
                }
                //排名
                foreach($usergroup as $key=>$user){
                    //条件

                    if(!empty($condition)){
                        //正则表达式
                        $pattern = '/#(.*?)#/';//需要转义/
                        preg_match($pattern,$condition,$match);
                        foreach($match as $key=>$value){
                            $replace = $user[$value];
                            $condition = str_replace("#".$value."#",$replace,$condition);
                        }
                        if(!$condition){
                            continue;
                        }
                    }
                    $typeid = $user[$cata];
                    $compareid = !empty($colume)?$user[$colume]:0;

                    unset($heapTree);
                    $heapTree = new \HeapTree($top);
                    //判断是否再数组中防止出现脏数据

                    if(isset($heapMap[$compareid]) && isset($heapMap[$compareid][$configid]) && !empty($heapMap[$compareid][$configid])){
                        $heapTree->init(unserialize($heapMap[$compareid][$configid]["rank"]),$typeid);
                    }else{

                        if(isset($ranked[$compareid]) && isset($ranked[$compareid][$configid]) && !empty($ranked[$compareid][$configid]["rank"])){
                            $heapTree->init(unserialize($ranked[$compareid][$configid]["rank"]),$typeid);
                            $heapMap[$compareid][$configid]["id"] = $ranked[$compareid][$configid]["id"];
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }else{
                            $heapMap[$compareid][$configid]["id"] = 0;
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }
                    }

                    $heapTree->add($user,$cata,$ranktype);
                    $rank = $heapTree->getarr();

                    $heapMap[$compareid][$configid]["rank"] = serialize($rank);


                }
            }

            //更新一次数据库
            $upd = array();
            $ins = array();
            foreach($heapMap as $key=>$value){
                $temp = array();
                $temp["rank"] = $value["rank"];
                $id   = $value["id"];
                if($id === 0){
                    $temp["configid"] = $value["configid"];
                    $temp["typeid"] = $value["typeid"];
                    array_push($ins,$temp);
                }else{
                    $temp["id"] = $id;
                    array_push($upd,$temp);
                }
            }
            //var_dump($heapMap);
            //echo self::getMillisecond()-$begin."\r\n";

            $StageRank = D("StageRank","Service");
            //分别操作数据库
            $StageRank->handleBatch($heapMap);
            echo "第".$page."次数处理完成\r\n";

            //更新这一批次所有的数据
            //M()->execute("update db_math.sx_rank_user set isdel = 1 where id in (".implode(",",$ids).")");
            //M()->execute("delete from db_math.sx_rank_user where id in (".implode(",",$ids).")");
            $result = M()->execute("insert into db_math.sx_rank_userbak select * from db_math.sx_rank_user  where ".$sqlwhere);

            if($result){
                M()->execute("delete from db_math.sx_rank_user  where ".$sqlwhere);
            }
        }
        //echo "delete from db_math.sx_rank_user  where ".$sqlwhere;
        $appendtime = time();
        $time = ($appendtime-$appbegintime);
        echo "--共用时".$time."秒--\r\n";
        echo "--结束处理时间".$appendtime."--\r\n";

    }

    public function index(){}

    //时间转化函数
    private function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    






    //更细存数区域数据
    public function cacheAreaData(){
        ignore_user_abort(); // 后台运行
        //进行缓存数据
        $yjtAreaCode = M("yjt_url");
        $result = $yjtAreaCode->select();
        $ret = array();
        foreach($result as $key=>$value){
            $areacode = $value["areacode"];
            $system = explode("_",$value["url_type"])[0];
            $ret[$areacode][$system]["ip"] = $value["in_ip"];
            $ret[$areacode][$system]["host"] = $value["url"];
        }
        S('areacode',$ret);
    }

    //每天删除中户信息
    public function deleteUserInfos(){
        $rs = M("userinfo") -> where("isdel=1") -> delete();
    }

    //缓存某个版本的数据
    public function cacheBookData(){

    }
  //课文朗读，口语评测 排行榜
  public function handleTextRankData(){
        ignore_user_abort(); // 后台运行
        vendor("Stastic");
        vendor("HeapTree");
        $appbegintime = time();
        $apptype  = "1";
        $moduleid = "10";
        echo "--开始处理时间".$appbegintime."--\r\n";
        $stastic = new \Stastic();
        //查询配置信息
        $rankConfig = M()->query("select * from engs_rank_config where moduleid=".$moduleid);
        //$rankConfig = json_decode(json_encode($rankConfig),true);
        $configids  = array_column($rankConfig,"id");
        //查询用户前100信息
        //var_dump($configids);
        $lastUpdateTime = S("lastUpdateTime");
        $count = M()->query("select count(*) as count from engs_rank_user where isdel = 0 and apptype=".$apptype);
        $resCount = $count[0]["count"];
        if($resCount == 0){
            return false;
        }
        $resCount = 500;
        $batch = array();
        $ids = array();
        $results = M()->query("select * from engs_rank_user where isdel=0 and apptype=".$apptype." order by id limit 0,300");
        //同一批次内用户只处理一次
        foreach($results as $key=>$value){
            $temp = array();
            $temp["id"] = $value["id"];
            if(isset($batch[$value["username"]])){
                if($batch[$value["username"]]["addtime"]<$value["addtime"]){
                    $batch[$value["username"]]["addtime"] = $value["addtime"];
                }
            } else {
                $temp["addtime"] = $value["addtime"];
                $batch[$value["username"]] = $temp;
            }
            array_push($ids,$value["id"]);
        }
        $batchKeys = array_keys($batch);
        echo "总处理人数".count($batchKeys);
        echo "\r\n";
        //这个批次的总用户数量
        $batchCount = count($batch);
        $pageNum = 20;
        for($page = 0;$page<ceil($batchCount/$pageNum);$page++){
            $updateArr = array();
            echo "第".$page."次数处理中";
            $sql = "select count(a.id) as total, a.ub,a.filepath,a.filename,b.classid,b.schoolid,b.classname,b.schoolname,b.truename,b.userpic from engs_user_record a left join  engs_userinfo b on a.username = b.username  where a.isdel = 0 and b.isdel = 0 and a.apptype=".$apptype." and (";
            $i = 0;
            $sqlwhere = "";
            //计算最后的索引
            $begin = $page*$pageNum;
            $end = ($page+1)*$pageNum>$batchCount?$batchCount:($page+1)*$pageNum;
            for($index=$begin;$index<$end;$index++){
                $key = $batchKeys[$index];
                $value = $batch[$key]["addtime"];
                if($i==0){
                    $sql = $sql." (a.username = '".$key."' and a.addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." (username = '".$key."' and addtime<='".$value."' and apptype=".$apptype.")";
                }else{
                    $sql = $sql." or (a.username = '".$key."' and a.addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." or (username = '".$key."' and addtime<='".$value."' and apptype=".$apptype.")";
                }
                $i++;
                array_push($updateArr,$batch[$key]["id"]);
            }

            $sql = $sql.")";

            //处理这个批次的代码
            $arr = array();
            $begin = self::getMillisecond();
            $heapMap = array();
            foreach($rankConfig as $key=>$value){
                //组装查询语句
                $day = $value["day"];
                $begintime = $value["begintime"];
                $endtime = $value["endtime"];
                if(empty($day)){
                    if(empty($begintime) && empty($endtime)){

                    }else{
                        if(!empty($begintime) && !empty($endtime)){
                            if(strtotime($begintime)>time() || strtotime($endtime)<time()){
                                continue;
                            }else{
                                $sql = $sql.' and (a.addtime>="'.$begintime.'" and a.addtime<"'.$endtime.'")';
                            }
                        }else{
                            if(!empty($begintime)){
                                $sql = $sql.' and a.addtime>="'.$begintime.'"';
                            }

                            if(!empty($endtime)){
                                $sql = $sql.' and a.addtime<"'.$endtime.'"';
                            }
                        }
                    }
                }else{
                    $sql = $sql.' and a.addtime>="'.(date("Y-m-d",strtotime($day." day")).' 00:00:00').'"';
                }
               // echo "ssss=".$sql;exit;
                $batchResult = M()->query($sql);

                //比较的条件
                $condition = $value["condition"];
                //待比较的字段
                $ranktype = $value["ranktype"];
                //堆的大小
                $top = $value["top"];
                //配置的索引
                $configid = $value["id"];
                //排名的范围
                $type = $value["type"];
                //排名的元数据类型
                $cata = $value["cata"];
                $colume = "";

                //分治算法进行数据的聚合
                $usergroup = $stastic->batchSum($batchResult,$cata,$ranktype);



                //查看这个类型的数据
                if($type == 0){

                }else if($type == 1){
                    $colume = "classid";
                }else if($type == 2){
                    $colume = "schoolid";
                }
                //遍历组合数据,减少数据库的查询次数
            
                $cataArr = array_column($usergroup,$colume);
                //var_dump($usergroup);exit;
               // var_dump($cataArr) ;exit;

                if(empty($cataArr)){
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid = 0");
                }else{
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid in (".$new_projectcode_array.")");
                }
                $ranked = array();
                foreach($result as $key=>$value){
                    $ranked[$value["typeid"]][$value["configid"]] = $value;
                }
                //排名
                foreach($usergroup as $key=>$user){
                    //条件

                    if(!empty($condition)){
                        //正则表达式
                        $pattern = '/#(.*?)#/';//需要转义/
                        preg_match($pattern,$condition,$match);
                        foreach($match as $key=>$value){
                            $replace = $user[$value];
                            $condition = str_replace("#".$value."#",$replace,$condition);
                        }
                        if(!$condition){
                            continue;
                        }
                    }
                    $typeid = $user[$cata];
                    $compareid = !empty($colume)?$user[$colume]:0;

                    unset($heapTree);
                    $heapTree = new \HeapTree($top);
                    //判断是否再数组中防止出现脏数据

                    if(isset($heapMap[$compareid]) && isset($heapMap[$compareid][$configid]) && !empty($heapMap[$compareid][$configid])){
                        $heapTree->init(unserialize($heapMap[$compareid][$configid]["rank"]),$typeid);
                    }else{

                        if(isset($ranked[$compareid]) && isset($ranked[$compareid][$configid]) && !empty($ranked[$compareid][$configid]["rank"])){
                            $heapTree->init(unserialize($ranked[$compareid][$configid]["rank"]),$typeid);
                            $heapMap[$compareid][$configid]["id"] = $ranked[$compareid][$configid]["id"];
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }else{
                            $heapMap[$compareid][$configid]["id"] = 0;
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }
                    }

                    $heapTree->add($user,$cata,$ranktype);
                    $rank = $heapTree->getarr();

                    $heapMap[$compareid][$configid]["rank"] = serialize($rank);


                }
            }

            //更新一次数据库
            $upd = array();
            $ins = array();
            foreach($heapMap as $key=>$value){
                $temp = array();
                $temp["rank"] = $value["rank"];
                $id   = $value["id"];
                if($id === 0){
                    $temp["configid"] = $value["configid"];
                    $temp["typeid"] = $value["typeid"];
                    array_push($ins,$temp);
                }else{
                    $temp["id"] = $id;
                    array_push($upd,$temp);
                }
            }
            //var_dump($heapMap);
            //echo self::getMillisecond()-$begin."\r\n";

            $StageRank = D("StageRank","Service");
            //分别操作数据库
            $StageRank->handleModuleBatch($heapMap);
            echo "第".$page."次数处理完成\r\n";

            //更新这一批次所有的数据
            //M()->execute("update db_math.sx_rank_user set isdel = 1 where id in (".implode(",",$ids).")");
            //M()->execute("delete from db_math.sx_rank_user where id in (".implode(",",$ids).")");
            $result = M()->execute("insert into engs_rank_userbak select * from engs_rank_user  where ".$sqlwhere);

            if($result){
                M()->execute("delete from engs_rank_user  where ".$sqlwhere);
            }
        }
        $appendtime = time();
        $time = ($appendtime-$appbegintime);
        echo "--共用时".$time."秒--\r\n";
        echo "--结束处理时间".$appendtime."--\r\n";
  }
  //口语评测 排行榜
  public function handleKypcRankData(){
        ignore_user_abort(); // 后台运行
        vendor("Stastic");
        vendor("HeapTree");
        $appbegintime = time();
        $apptype = "74";
        $moduleid = "74";
        echo "--开始处理时间".$appbegintime."--\r\n";
        $stastic = new \Stastic();
        //查询配置信息
        $rankConfig = M()->query("select * from engs_rank_config where moduleid=".$moduleid);
        //$rankConfig = json_decode(json_encode($rankConfig),true);
        $configids  = array_column($rankConfig,"id");
        //查询用户前100信息
        //var_dump($configids);
        $lastUpdateTime = S("lastUpdateTime");
        $count = M()->query("select count(*) as count from engs_rank_user where isdel = 0 and apptype=".$apptype);
        $resCount = $count[0]["count"];
        if($resCount == 0){
            return false;
        }
        $resCount = 500;
        $batch = array();
        $ids = array();
        $results = M()->query("select * from engs_rank_user where isdel=0 and apptype=".$apptype." order by id desc limit 0,1000");
        //同一批次内用户只处理一次
        foreach($results as $key=>$value){
            $temp = array();
            $temp["id"] = $value["id"];
            if(isset($batch[$value["username"]])){
                if($batch[$value["username"]]["addtime"]<$value["addtime"]){
                    $batch[$value["username"]]["addtime"] = $value["addtime"];
                }
            } else {
                $temp["addtime"] = $value["addtime"];
                $batch[$value["username"]] = $temp;
            }
            array_push($ids,$value["id"]);
        }
        $batchKeys = array_keys($batch);
        echo "总处理人数".count($batchKeys);
        echo "\r\n";
        //这个批次的总用户数量
        $batchCount = count($batch);
        $pageNum = 20;
        for($page = 0;$page<ceil($batchCount/$pageNum);$page++){
            $updateArr = array();
            echo "第".$page."次数处理中";
            $sql = "select count(a.id) as total,a.username, a.ub,a.filepath,REPLACE(a.filename,'\'','‘') as filename,b.classid,b.schoolid,b.classname,b.schoolname,b.truename,b.userpic from engs_user_record a left join  engs_userinfo b on a.username = b.username  where a.isdel = 0 and b.isdel = 0 and a.apptype=".$apptype." and (";
            $i = 0;
            $sqlwhere = "";
            //计算最后的索引
            $begin = $page*$pageNum;
            $end = ($page+1)*$pageNum>$batchCount?$batchCount:($page+1)*$pageNum;
            for($index=$begin;$index<$end;$index++){
                $key = $batchKeys[$index];
                $value = $batch[$key]["addtime"];
                if($i==0){
                    $sql = $sql." (a.username = '".$key."' and a.addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." (username = '".$key."' and addtime<='".$value."' and apptype=".$apptype.")";
                }else{
                    $sql = $sql." or (a.username = '".$key."' and a.addtime<='".$value."')";
                    $sqlwhere = $sqlwhere." or (username = '".$key."' and addtime<='".$value."' and apptype=".$apptype.")";
                }
                $i++;
                array_push($updateArr,$batch[$key]["id"]);
            }

            $sql = $sql.")";

            //处理这个批次的代码
            $arr = array();
            $begin = self::getMillisecond();
            $heapMap = array();
            //echo count($rankConfig);
            foreach($rankConfig as $key=>$value){
                //组装查询语句
             
                $day = $value["day"];
                $begintime = $value["begintime"];
                $endtime = $value["endtime"];
                if(empty($day)){
                    if(empty($begintime) && empty($endtime)){

                    }else{
                        if(!empty($begintime) && !empty($endtime)){
                            if(strtotime($begintime)>time() || strtotime($endtime)<time()){
                                continue;
                            }else{
                                $sql = $sql.' and (a.addtime>="'.$begintime.'" and a.addtime<"'.$endtime.'")';
                            }
                        }else{
                            if(!empty($begintime)){
                                $sql = $sql.' and a.addtime>="'.$begintime.'"';
                            }

                            if(!empty($endtime)){
                                $sql = $sql.' and a.addtime<"'.$endtime.'"';
                            }
                        }
                    }
                }else{
                    $sql = $sql.' and a.addtime>="'.(date("Y-m-d",strtotime($day." day")).' 00:00:00').'"';
                }
               
                $batchResult = M()->query($sql);
               
                foreach ($batchResult as $tempkey => $tempvalue) {
                      $tempusername = $tempvalue["username"];
                      $tempsql = 'select count(a.id) as total from engs_user_record a where username = "'.$tempusername.'"';
                      $rs = M()->query($tempsql);
                      $batchResult[$tempkey]["total"] = $rs[0]["total"];
                    
                     
                }
                 var_dump($batchResult);
                //比较的条件
                $condition = $value["condition"];
                //待比较的字段
                $ranktype = $value["ranktype"];
                //堆的大小
                $top = $value["top"];
                //配置的索引
                $configid = $value["id"];
                //排名的范围
                $type = $value["type"];
                //排名的元数据类型
                $cata = $value["cata"];
                $colume = "";

                //分治算法进行数据的聚合
                $usergroup = $stastic->batchSum($batchResult,$cata,$ranktype);



                //查看这个类型的数据
                if($type == 0){

                }else if($type == 1){
                    $colume = "classid";
                }else if($type == 2){
                    $colume = "schoolid";
                }
                //遍历组合数据,减少数据库的查询次数
            
                $cataArr = array_column($usergroup,$colume);
                //var_dump($usergroup);exit;
               // var_dump($cataArr) ;exit;

                if(empty($cataArr)){
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid = 0");
                }else{
                    $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                    $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid in (".$new_projectcode_array.")");
                }
                $ranked = array();
                foreach($result as $key=>$value){
                    $ranked[$value["typeid"]][$value["configid"]] = $value;
                }
                //排名
                foreach($usergroup as $key=>$user){
                    //条件

                    if(!empty($condition)){
                        //正则表达式
                        $pattern = '/#(.*?)#/';//需要转义/
                        preg_match($pattern,$condition,$match);
                        foreach($match as $key=>$value){
                            $replace = $user[$value];
                            $condition = str_replace("#".$value."#",$replace,$condition);
                        }
                        if(!$condition){
                            continue;
                        }
                    }
                    $typeid = $user[$cata];
                    $compareid = !empty($colume)?$user[$colume]:0;

                    unset($heapTree);
                    $heapTree = new \HeapTree($top);
                    //判断是否再数组中防止出现脏数据

                    if(isset($heapMap[$compareid]) && isset($heapMap[$compareid][$configid]) && !empty($heapMap[$compareid][$configid])){
                        $heapTree->init(unserialize($heapMap[$compareid][$configid]["rank"]),$typeid);
                    }else{

                        if(isset($ranked[$compareid]) && isset($ranked[$compareid][$configid]) && !empty($ranked[$compareid][$configid]["rank"])){
                            $heapTree->init(unserialize($ranked[$compareid][$configid]["rank"]),$typeid);
                            $heapMap[$compareid][$configid]["id"] = $ranked[$compareid][$configid]["id"];
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }else{
                            $heapMap[$compareid][$configid]["id"] = 0;
                            $heapMap[$compareid][$configid]["configid"] = $configid;
                            $heapMap[$compareid][$configid]["typeid"] = $compareid;
                        }
                    }

                    $heapTree->add($user,$cata,$ranktype);
                    $rank = $heapTree->getarr();

                    $heapMap[$compareid][$configid]["rank"] = serialize($rank);


                }
            }

            //更新一次数据库
            $upd = array();
            $ins = array();
            foreach($heapMap as $key=>$value){
                $temp = array();
                $temp["rank"] = $value["rank"];
                $id   = $value["id"];
                if($id === 0){
                    $temp["configid"] = $value["configid"];
                    $temp["typeid"] = $value["typeid"];
                    array_push($ins,$temp);
                }else{
                    $temp["id"] = $id;
                    array_push($upd,$temp);
                }
            }
            //var_dump($heapMap);
            //echo self::getMillisecond()-$begin."\r\n";

            $StageRank = D("StageRank","Service");
            //分别操作数据库
            $StageRank->handleModuleBatch($heapMap);
            echo "第".$page."次数处理完成\r\n";

            //更新这一批次所有的数据
            //M()->execute("update db_math.sx_rank_user set isdel = 1 where id in (".implode(",",$ids).")");
            //M()->execute("delete from db_math.sx_rank_user where id in (".implode(",",$ids).")");
            $result = M()->execute("insert into engs_rank_userbak select * from engs_rank_user  where ".$sqlwhere);

            if($result){
                M()->execute("delete from engs_rank_user  where ".$sqlwhere);
            }
        }
        $appendtime = time();
        $time = ($appendtime-$appbegintime);
        echo "--共用时".$time."秒--\r\n";
        echo "--结束处理时间".$appendtime."--\r\n";
  }
    //背单词排行榜
  public function handleWordRankData(){
    ignore_user_abort(); // 后台运行
    vendor("Stastic");
    vendor("HeapTree");
    $appbegintime = time();
    //$moduleid = I("moduleid/d",0);
    echo "--开始处理时间".$appbegintime."--\r\n";
    $stastic = new \Stastic();
    //查询配置信息
    $rankConfig = M()->query("select * from engs_rank_config where moduleid=1");
    //$rankConfig = json_decode(json_encode($rankConfig),true);
    $configids  = array_column($rankConfig,"id");
    //查询用户前100信息
    //var_dump($configids);
    $lastUpdateTime = S("lastUpdateTime");
    $count = M()->query("select count(*) as count from engs_rank_user_word where isdel = 0");
    $resCount = $count[0]["count"];
    if($resCount == 0){
        return false;
    }
    $resCount = 500;
    $batch = array();
    $ids = array();
    $results = M()->query("SELECT * from engs_rank_user_word where isdel=0  order by id limit 0,300");
    //同一批次内用户只处理一次
    foreach($results as $key=>$value){
        $temp = array();
        $temp["id"] = $value["id"];
        if(isset($batch[$value["username"]])){
            if($batch[$value["username"]]["addtime"]<$value["addtime"]){
                $batch[$value["username"]]["addtime"] = $value["addtime"];
            }
        } else {
            $temp["addtime"] = $value["addtime"];
            $batch[$value["username"]] = $temp;
        }
        array_push($ids,$value["id"]);
    }
    $batchKeys = array_keys($batch);
    echo "总处理人数".count($batchKeys);
    echo "\r\n";
    //这个批次的总用户数量
    $batchCount = count($batch);
    $pageNum = 20;
    for($page = 0;$page<ceil($batchCount/$pageNum);$page++){
        $updateArr = array();
        echo "第".$page."次数处理中";
        $sql = "select a.ub,b.classid,b.schoolid,b.classname,b.schoolname,b.truename,b.userpic from engs_user_word_batch a left join  engs_userinfo b on a.username = b.username  where  b.isdel = 0 and a.submittime is not null and a.ub>0  and (";
        $i = 0;
        $sqlwhere = "";
        //计算最后的索引
        $begin = $page*$pageNum;
        $end = ($page+1)*$pageNum>$batchCount?$batchCount:($page+1)*$pageNum;
        for($index=$begin;$index<$end;$index++){
            $key = $batchKeys[$index];
            $value = $batch[$key]["addtime"];
            if($i==0){
                $sql = $sql." (a.username = '".$key."' and a.addtime<='".$value."')";
                $sqlwhere = $sqlwhere." (username = '".$key."' and addtime<='".$value."')";
            }else{
                $sql = $sql." or (a.username = '".$key."' and a.addtime<='".$value."')";
                $sqlwhere = $sqlwhere." or (username = '".$key."' and addtime<='".$value."')";
            }
            $i++;
            array_push($updateArr,$batch[$key]["id"]);
        }

        $sql = $sql.")";

        //处理这个批次的代码
        $arr = array();
        $begin = self::getMillisecond();
        $heapMap = array();
        foreach($rankConfig as $key=>$value){
            //组装查询语句
            $day = $value["day"];
            $begintime = $value["begintime"];
            $endtime = $value["endtime"];
            if(empty($day)){
                if(empty($begintime) && empty($endtime)){

                }else{
                    if(!empty($begintime) && !empty($endtime)){
                        if(strtotime($begintime)>time() || strtotime($endtime)<time()){
                            continue;
                        }else{
                            $sql = $sql.' and (a.addtime>="'.$begintime.'" and a.addtime<"'.$endtime.'")';
                        }
                    }else{
                        if(!empty($begintime)){
                            $sql = $sql.' and a.addtime>="'.$begintime.'"';
                        }

                        if(!empty($endtime)){
                            $sql = $sql.' and a.addtime<"'.$endtime.'"';
                        }
                    }
                }
            }else{
                $sql = $sql.' and a.addtime>="'.(date("Y-m-d",strtotime($day." day")).' 00:00:00').'"';
            }
           // echo "ssss=".$sql;exit;
            $batchResult = M()->query($sql);

            //比较的条件
            $condition = $value["condition"];
            //待比较的字段
            $ranktype = $value["ranktype"];
            //堆的大小
            $top = $value["top"];
            //配置的索引
            $configid = $value["id"];
            //排名的范围
            $type = $value["type"];
            //排名的元数据类型
            $cata = $value["cata"];
            $colume = "";

            //分治算法进行数据的聚合
            $usergroup = $stastic->batchSum($batchResult,$cata,$ranktype);



            //查看这个类型的数据
            if($type == 0){

            }else if($type == 1){
                $colume = "classid";
            }else if($type == 2){
                $colume = "schoolid";
            }
            //遍历组合数据,减少数据库的查询次数
        
            $cataArr = array_column($usergroup,$colume);
            //var_dump($usergroup);exit;
           // var_dump($cataArr) ;exit;

            if(empty($cataArr)){
                $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid = 0");
            }else{
                $new_projectcode_array =  implode(',', array_map('change_to_quotes', $cataArr ));
                $result = M()->query("select * from engs_rank_info where configid=".$configid." and typeid in (".$new_projectcode_array.")");
            }
            $ranked = array();
            foreach($result as $key=>$value){
                $ranked[$value["typeid"]][$value["configid"]] = $value;
            }
            //排名
            foreach($usergroup as $key=>$user){
                //条件

                if(!empty($condition)){
                    //正则表达式
                    $pattern = '/#(.*?)#/';//需要转义/
                    preg_match($pattern,$condition,$match);
                    foreach($match as $key=>$value){
                        $replace = $user[$value];
                        $condition = str_replace("#".$value."#",$replace,$condition);
                    }
                    if(!$condition){
                        continue;
                    }
                }
                $typeid = $user[$cata];
                $compareid = !empty($colume)?$user[$colume]:0;

                unset($heapTree);
                $heapTree = new \HeapTree($top);
                //判断是否再数组中防止出现脏数据

                if(isset($heapMap[$compareid]) && isset($heapMap[$compareid][$configid]) && !empty($heapMap[$compareid][$configid])){
                    $heapTree->init(unserialize($heapMap[$compareid][$configid]["rank"]),$typeid);
                }else{

                    if(isset($ranked[$compareid]) && isset($ranked[$compareid][$configid]) && !empty($ranked[$compareid][$configid]["rank"])){
                        $heapTree->init(unserialize($ranked[$compareid][$configid]["rank"]),$typeid);
                        $heapMap[$compareid][$configid]["id"] = $ranked[$compareid][$configid]["id"];
                        $heapMap[$compareid][$configid]["configid"] = $configid;
                        $heapMap[$compareid][$configid]["typeid"] = $compareid;
                    }else{
                        $heapMap[$compareid][$configid]["id"] = 0;
                        $heapMap[$compareid][$configid]["configid"] = $configid;
                        $heapMap[$compareid][$configid]["typeid"] = $compareid;
                    }
                }

                $heapTree->add($user,$cata,$ranktype);
                $rank = $heapTree->getarr();

                $heapMap[$compareid][$configid]["rank"] = serialize($rank);


            }
        }

        //更新一次数据库
        $upd = array();
        $ins = array();
        foreach($heapMap as $key=>$value){
            $temp = array();
            $temp["rank"] = $value["rank"];
            $id   = $value["id"];
            if($id === 0){
                $temp["configid"] = $value["configid"];
                $temp["typeid"] = $value["typeid"];
                array_push($ins,$temp);
            }else{
                $temp["id"] = $id;
                array_push($upd,$temp);
            }
        }
        //var_dump($heapMap);
        //echo self::getMillisecond()-$begin."\r\n";

        $StageRank = D("StageRank","Service");
        //分别操作数据库
        $StageRank->handleModuleBatch($heapMap);
        echo "第".$page."次数处理完成\r\n";

        //更新这一批次所有的数据
        //M()->execute("update db_math.sx_rank_user set isdel = 1 where id in (".implode(",",$ids).")");
        //M()->execute("delete from db_math.sx_rank_user where id in (".implode(",",$ids).")");
        $result = M()->execute("insert into engs_rank_userbak_word select * from engs_rank_user_word  where ".$sqlwhere);

        if($result){
            M()->execute("delete from engs_rank_user_word  where ".$sqlwhere);
        }
    }
    $appendtime = time();
    $time = ($appendtime-$appbegintime);
    echo "--共用时".$time."秒--\r\n";
    echo "--结束处理时间".$appendtime."--\r\n";
    }

    
//处理旧数据
    public function checkHistory(){
        //查询历史数据
         $rs = M()->query("select username,max(addtime) as addtime from db_math.sx_user_stage where addtime > '2019-10-01 00:00:00' group by username ");
         //echo count($rs);
        // $sql = "";
        foreach($rs as $key=>$value){
            $sql=$sql."insert into db_math.sx_rank_user (username,addtime) value ('".$value["username"]."','".$value["addtime"]."');";
        }
        echo $sql;
        //M()->execute($sql);
    }

    //处理旧数据
    public function checkTextHistory(){
        //查询历史数据
        $rs = M()->query("select username,max(addtime) as addtime,apptype from db_english.engs_user_record where addtime > '2019-10-01 00:00:00' group by username");
        $sql = "";
        foreach($rs as $key=>$value){
            $sql=$sql."insert into db_english.engs_rank_user (username,addtime,apptype) value ('".$value["username"]."','".$value["addtime"]."','".$value["apptype"]."');";
        }
        echo $sql;
       // M()->execute($sql);
    }
    //处理旧数据
    public function checkWordHistory(){
        //查询历史数据
        $rs = M()->query("select username,max(addtime) as addtime from db_english.engs_user_word_batch where addtime > '2019-10-01 00:00:00' group by username");
        $sql = "";
        foreach($rs as $key=>$value){
            $sql=$sql."insert into db_english.engs_rank_user_word (username,addtime) value ('".$value["username"]."','".$value["addtime"]."');";
            
        }
        //echo $sql;
        M()->execute($sql);
    }

}
