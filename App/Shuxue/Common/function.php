<?php
function getuserinfo(){
    $ut=I("ut");
    $deviceType=I("deviceType");
    $areacode=I("areaCode");
    if(!empty($ut)){
        $ut=$ut;
    }else{
        $ut=cookie("ut");
    }
    //1c5cb7129e30d79af97f722e22559172a6f9f68daa7a141cede5c60c9b7a5460
    if($ut != null){
        if(cookie('jp_ut')!=cookie('ut')){
            cookie('ut',$ut);
            cookie('areaCode',$areacode);
            cookie('deviceType',$deviceType);
            //根据areacode查询sso地址
            $rs=M("yjt_url")->where("areacode='%s' and url_type='sso_url'",$areacode)->find();
            $sso=$rs["url"];
            $url="http://".$sso."/sso/ssoGrant?isPortal=0&appFlg=SSO&ut=".cookie('ut');
            $json_ret = file_get_contents($url);
            $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
            $result = json_decode($json_ret, true);
            if($result['authFlg']=='1')
            {
                cookie("jp_ut",cookie('ut'));
                $ouUser=$result['ouUser'];
                $user=$result['user'];
                $grade=$user['grade'];
                $area=$user['area'];
                $school=$user['school'];
                $usertype=$user['usertype'];
                //如果是家长的话
                if($usertype==4){
                    $class=$user["schoolClasses"][0];
                    cookie("username",$user['username']);
                    cookie("usertype",$usertype);
                    cookie("areacode",$area['areaId']);
                    cookie("gradeid",$grade['gradeCode']);
                    cookie("truename",$user['truename']);
                    cookie("schoolid",$school['schoolId']);
                    cookie("classid",$class['classId']);
                    cookie("parentid","");
                }else{
                    $urlarr=get_ip("",$sso);
                    $tms=$urlarr["tms"]["c1"];
                    $tmsurl = "http://".$tms.'/tms/interface/queryStudent.jsp?queryType=detailByParent&parentAccount='.$user['username'];
                    $json_ret = file_get_contents($tmsurl);
                    $json_ret = mb_convert_encoding($json_ret, "UTF-8", "gb2312");//转码，（这里只是个例子）
                    $studentinfo = json_decode($json_ret, true);
                    if($studentinfo["result"]>0){
                        cookie("parentid",$user['username']);
                        cookie("truename",$studentinfo['rtnArray'][0]['student']['realname']);
                        cookie("username",$studentinfo['rtnArray'][0]['student']['studentAccount']);
                        cookie("gradeid",$studentinfo['rtnArray'][0]['student']['gradeCode']);
                        cookie("usertype","0");
                        cookie("areacode",$area['areaId']);
                        cookie("schoolid",$studentinfo['rtnArray'][0]['student']['schoolId']);
                        cookie("classid",$studentinfo['rtnArray'][0]['student']['schoolClassId']);
                    }else{
                        redirect('http://www.czbanbantong.com');
                    }
                }
            }else{
                redirect('http://www.czbanbantong.com');
            }
        }
    }else{
        redirect('http://www.czbanbantong.com');
    }
}