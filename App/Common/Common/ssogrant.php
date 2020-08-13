<?php
if(!function_exists("getuserinfos")){
    //用户鉴权问题
    function getuserinfos(){
        $request = $_REQUEST;
       // var_dump($request);exit;
        $ut = $request["ut"];
        $areaCode = empty($request["areaCode"])?$request["serAreaCode"]:$request["areaCode"];
        $cookie = $_COOKIE;
        $cookie_areaCode = $cookie["localAreaCode"];
        $cookie_ut = $cookie["ut"];
        //判断是否参数中需要的数据
        $ut = empty($ut)?$cookie_ut:$ut;
        $areaCode = empty($areaCode)?$cookie_areaCode:$areaCode;
        //echo $ut;exit;
        //传递返回参数
        if($request["backUrl"]){cookie("backUrl",$request["backUrl"]);}
        if(empty($ut) || empty($areaCode)){
            //返回上次进来的页面
            $backUrl = $request["backUrl"];
            if(empty($backUrl)){
                redirect("http://www.czbanbantong.com");
            }else{
                redirect($backUrl);
            }
        }else {
            //判断两次UT是否相同
            $jp_ut = $cookie["jp_ut"];
            cookie("localAreaCode",$areaCode);
            cookie("ut",$ut);
            //如果是不相同的话
            //echo $jp_ut."==".$ut;exit;
            if ($jp_ut != $ut) {
                //根据区域获取不同平台的IP
                $areaData = S("areacode");
                if(!$areaData || empty($areaCode)){
                    $yjtAreaCode = M("db_english.yjt_url","engs_");
                    $result = $yjtAreaCode->select();
                    $ret = array();
                    foreach($result as $key=>$value){
                        $areacode = $value["areacode"];
                        $system = explode("_",$value["url_type"])[0];
                        $ret[$areacode][$system]["ip"] = $value["in_ip"];
                        $ret[$areacode][$system]["host"] = $value["url"];
                    }
                    S('areacode',$ret);
                    $areaData = $ret;
                }
                $areaData = $areaData[$areaCode];
                $sso = $areaData["sso"]["host"];
                $vfs = $areaData["vfs"]["host"];
                $tms = $areaData["tms"]["host"];
                $portal = $areaData["portal"]["host"];
                $tqms = $areaData["tqms"]["host"];
                $ub = $areaData["ub"]["host"];
                $pls = $areaData["pls"]["host"];
                $ubname = $areaData["ubname"];
                if(empty($ubname)){
                    $ubname = C("ubname");
                }else{
                    $ubname = $ubname["host"];
                }

                //将所欲的地址全部写入cookie
                cookie("sso_url",$sso);
                cookie("vfs_url",$vfs);
                cookie("tqms_url",$tqms);
                cookie("tms_url",$tms);
                cookie("pls_url",$pls);
                cookie("portal_url",$portal);
                cookie("ubname",$ubname);
                cookie("ub_url",$ub);

                //判断缓存文件
                $userInfo = S("student_" . $ut);
                if (!empty($userInfo)) {
                    $username = $userInfo["username"];
                    $usertype = $userInfo["usertype"];
                    $truename = $userInfo["truename"];
                    $areaname = $userInfo["areaname"];
                    $areaid = $userInfo["areaid"];
                    $localareacode = $userInfo["localareacode"];
                    $classid = $userInfo["classid"];
                    $classname = $userInfo["classname"];
                    $schoolid = $userInfo["schoolid"];
                    $schoolname = $userInfo["schoolname"];
                    $gradeid = $userInfo["gradeid"];
                    $userpic = $userInfo["userpic"];
                    $isparent = $userInfo["isparent"];
                    //写入cookie
                    cookie("userpic", $userpic);
                    cookie('usertype', $usertype);
                    cookie("areacode", $areaid);
                    cookie("fullname", $areaname);
                    cookie("schoolid", $schoolid);
                    cookie("schoolname", $schoolname);
                    cookie("classid", $classid);
                    cookie("classname", $classname);
                    cookie("usergradeid", $gradeid);
                    cookie("gradeid", $gradeid);
                } else {
                    $localareacode = $areaCode;
                    $url = PROTOCOL . "://" . $sso . "/sso/ssoGrant?isPortal=0&appFlg=SSO&encoding=UTF-8&data=json&ut=" . $ut;
                    //echo $url;
                    $user_result = get_content($url,"usergrantinterface.log");
                    //解析中文出错问题
                    // preg_match_all('/"name":"(.*?)"/', $user_result, $tmp);
                    // $json_ret = str_replace($tmp[1][0], "", $user_result);
                    // $truename = iconv("gbk", "UTF-8//IGNORE", $tmp[1][0]);
                    // cookie("truename", $truename);
                    //正则中文并且进行urlencode
                    $json_ret = iconv("UTF-8", "UTF-8//IGNORE", $user_result);
                    //$encoding = mb_detect_encoding($json_ret);
                    
                    $result = json_decode($json_ret, true);
                  //  var_dump($result);exit;
                    writeAppLog("usergrantinterfacejsonparse".Date("Y-m-d").".log",json_encode($result));
                    if ($result['authFlg'] == '1') {
                        cookie("jp_ut", $ut);
                        $ouUser = $result['ouUser'];
                        $user = $result['user'];
                        $grade = $user['grade'];
                        $area = $user['area'];
                        $userpic = $user['headPhoto'];
                        if (!empty($userpic) && $userpic != "") {
                            $userpic = PROTOCOL . "://" . $vfs . "/tms" . $userpic . "?r=1213";
                        } else {
                            $userpic = PROTOCOL . "://" . $portal . "/space/images/default1.png";
                        }
                        $parentid = "";
                        $isparent = 0;
                        $areaid = $area['areaId'];
                        $areaname = $area['fullname'];
                        $school = $user['school'];
                        $schoolClasses = $user['schoolClasses'];
                        $usertype = $user['usertype'];
                        $username = $user["username"];
                        $truename = $user["truename"];
                        $schoolid = $school['schoolId'];
                        $schoolname = $school['schoolName'];
                        $classid = $schoolClasses[0]['classId'];
                        $classname = $schoolClasses[0]['className'];
                        $gradeid = $schoolClasses[0]['gradeCode'];
                        if (empty($gradeid)) {
                            $gradeid = "0001";
                        }
                        //写入cookie
                        cookie("userpic", $userpic);
                        cookie('usertype', $usertype);
                        cookie("areacode", $areaid);
                        cookie("fullname", $areaname);
                        cookie("truename", $truename);
                        cookie("schoolid", $schoolid);
                        cookie("schoolname", $schoolname);
                        cookie("classid", $classid);
                        cookie("classname", $classname);
                        cookie("usergradeid", $gradeid);
                        cookie("gradeid", $gradeid);
                        if ($usertype != 0 && $usertype != 4) {  //老师
                            cookie("username", $username);
                            cookie("usertype", "2");
                            cookie("parentid", $parentid);
                        } else if ($usertype == 4) {  //学生
                            cookie("username", $user['username']);
                            cookie("usertype", $usertype);
                            cookie("parentid", $parentid);
                        } else if ($usertype == 0) {   //家长
                            $isparent = 1;
                            $tmsurl = PROTOCOL . '://' . $tms . '/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount=' . $username;
                            $json_ret = get_content($tmsurl,"tmsgetuserinfointerface".Date("Y-m-d").".log");
                            preg_match_all('/"realname":"(.*?)"/', $json_ret, $tmp);
                            for ($i = 1; $i >= 0; $i--) {
                                $value = $tmp[1][$i];
                                $json_ret = str_replace($value, "", $json_ret);
                                if ($i == 0) {
                                    $truename = iconv("gbk", "utf-8//IGNORE", $value);
                                    cookie("truename", $truename);
                                }
                            }
                            $json_ret = iconv("gbk", "utf-8//IGNORE", $json_ret);//转码，（这里只是个例子）
                            $studentinfo = json_decode($json_ret, true);
                            writeAppLog("tmsgetuserinfointerfacejsonparse".Date("Y-m-d").".log",json_encode($json_ret));
                            if ($studentinfo["result"] > 0) {
                                $student = $studentinfo['rtnArray'][0]['student']['studentNumber'];
                                $userpic = $studentinfo['rtnArray'][0]['student']['headPhoto'];
                                $parentid = $username;
                                if (!empty($userpic) && $userpic != "") {
                                    $userpic = PROTOCOL . "://" . $vfs . "/tms" . $userpic . "?r=1213";
                                } else {
                                    $userpic = PROTOCOL . "://" . $portal . "/space/images/default1.png";
                                }
                                cookie("username", $student);
                                cookie("parentid", $username);
                                cookie("userpic", $userpic);
                                cookie("usertype", 4);
                                $usertype = 4;
                                $username = $student;
                            } else {
                                redirect('../../../Error/Index/fail');
                            }
                        }
                        //比对查看数据是否相同如果不相同就进行数据库的插入
                        if($usertype == 4){
                            $ret["username"] = $username;
                            $ret["usertype"] = $usertype;
                            $ret["truename"] = $truename;
                            $ret["areaname"] = $areaname;
                            $ret["areaid"] = $areaid;
                            $ret["localareacode"] = $localareacode;
//                            $ret["classid"] = $classid;
//                            $ret["classname"] = $classname;
                            $ret["schoolClasses"] = $schoolClasses;
                            $ret["schoolid"] = $schoolid;
                            $ret["schoolname"] = $schoolname;
                            $ret["gradeid"] = $gradeid;
                            $ret["userpic"] = $userpic;
                            $ret["isparent"] = $isparent;
                            S("student_".$ut,$ret);
                            $md5key = md5(json_encode($ret));
                            $usermodel = M("db_english.userinfo","engs_");
                            $userrs = $usermodel -> where("username = '%s' and usertype = %d",$username,$usertype) -> find();
                            if(empty($userrs) || $userrs["md5key"] != $md5key){
                                //直接插入数据库
                                $usermodel -> where("username = '%s' and usertype = %d",$username,$usertype) -> setField("isdel",1);
                                $data["username"]=$username;
                                $data["truename"]=$truename;
                                $data["areaid"]=$areaid;
                                $data["areaname"]=$areaname;
                                $data["usertype"]=$usertype;
                                $data["localareacode"]=$localareacode;
                                $data["classid"]=$classid;
                                $data["classname"]=$classname;
                                $data["schoolid"]=$schoolid;
                                $data["schoolname"]=$schoolname;
                                $data["gradeid"]=$gradeid;
                                $data["isparent"]=$isparent;
                                $data["parentid"]=$parentid;
                                $data["userpic"]=$userpic;
                                $data["md5key"]=$md5key;
                                try{
                                    $usermodel->add($data);
                                }catch(Exception $e){
                                    redirect('../../../Error/Index/fail');
                                }
                            }
                        }else{
                            $ret["username"] = $username;
                            $ret["usertype"] = $usertype;
                            $ret["truename"] = $truename;
                            $ret["areaname"] = $areaname;
                            $ret["areaid"] = $areaid;
                            $ret["localareacode"] = $localareacode;
                            $ret["schoolid"] = $schoolid;
                            $ret["schoolname"] = $schoolname;
                            $ret["gradeid"] = $gradeid;
                            $ret["userpic"] = $userpic;
                            $ret["isparent"] = $isparent;
                            $ret["schoolClasses"] = $schoolClasses;
                            S("student_".$ut,$ret);
                            $md5key = md5(json_encode($ret));
                            $usermodel = M("db_english.userinfo","engs_");
                            $userrs = $usermodel -> where("username = '%s' and usertype = %d",$username,$usertype) -> find();
                            if(empty($userrs) || $userrs["md5key"] != $md5key){
                                foreach($schoolClasses as $key=>$value){
                                    $data["username"]=$username;
                                    $data["truename"]=$truename;
                                    $data["areaid"]=$areaid;
                                    $data["areaname"]=$areaname;
                                    $data["usertype"]=2;
                                    $data["localareacode"]=$localareacode;
                                    $data["classid"]=$value["classId"];
                                    $data["classname"]=$value["className"];
                                    $data["schoolid"]=$schoolid;
                                    $data["schoolname"]=$schoolname;
                                    $data["gradeid"]=$value["gradeCode"];
                                    $data["isparent"]=$isparent;
                                    $data["parentid"]=$parentid;
                                    $data["userpic"]=$userpic;
                                    $data["md5key"]=$md5key;
                                    try{
                                        $usermodel->add($data);
                                    }catch(Exception $e){
                                        redirect('../../../Error/Index/fail');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}