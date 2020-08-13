//页面初始化方法
function setuserdefault() {
    //初始化账号
    $(".mt20").removeClass("qk2");
    $(".greenBtn").hide();
    if ($.cookie("inputname")) {
        $("#inputname").attr("value", $.cookie("inputname"));
    }
    //初始化密码
    if ($.cookie("pwd")) {
        $("#pwd").attr("value", $.cookie("pwd"));
        $("#savepass").attr("checked", true);
    }
    //初始化用户角色选中
	$(".tab a").removeClass('sel');
	if($.cookie("loginUsertype")){
		$("#loginUsertype").attr("value", $.cookie("loginUsertype"));
		$("#" + $.cookie("loginUsertype")).addClass("sel");
	}
	else{
		$("#loginUsertype").attr("value", 'teacher');
		$("#teacher").addClass("sel");
	}
	 //调用选择账号接口
    $("#roleDiv").attr("phonenum", "");
    //选择角色点击事件
    $(".tab a").click(function() {
        $(this).siblings().removeClass();
        $(this).addClass("sel");
        if ($(this).attr('id') != 'student') {
            $(".mt20").removeClass("qk2");
            $(".greenBtn").hide();
        }
        else {
            if ($('#area').val() == '1.' || $('#area').val() == '25.') {  //郑州和河南地区显示注册按钮
                $(".mt20").addClass("qk2");
                $(".greenBtn").show();
            }
        }
        //登录角色
        $("#loginUsertype").attr("value", $(this).attr("id"));
        //调用选择账号接口

        $("#roleDiv").attr("phonenum", "");
        $("#pwd").val('');
        clearusername();//多账号时选择账号
    });
    var selectstr = '';
    var areahtmlstr = '';
    areahtmlstr = '<option>选择地区</option>';
    $.getJSON(PublicConter+'get_provice',{},function(result){
        $(result).each(function(i,val){
            if($.cookie('localAreaCode') == val.areacode){
                selectstr = 'selected';
            }
            else{
                if (val.c2 == '河南') {
                selectstr = 'selected';
                }
                else {
                    selectstr = '';
                }
            }
            areahtmlstr += '<OPTION ' + selectstr + '  value="' +val.areacode+ '">'+val.areaname+'</OPTION>';
        });
        $('#area').html(areahtmlstr);
        $("select").not(".native select").selectui({
            // 是否自动计算宽度
            autoWidth: true,
            // 是否启用定时器刷新文本和宽度
            interval: true
        });
        $("#validateCode").val('');
        $("#loginbuttion").attr("disabled", false);
        re_verifyimg();
    });
}


//当输入为手机号的时候，调用选择用户多账号接口，显示该手机号角色绑定的多个账号
function clearusername() {
    if ($("#inputname").attr("value").length != 11) {
        $("#roleDiv").css("display", "none"); //隐藏角色框
    } else {
		
        getRoles(); //调用选择账号接口
    }
    $("#username").attr("value", "");
}
//多账号时选择账号
function getRoles() {
    var localAreaCode = $("#area").val();
    var ssourl = '';
    ssourl = mapObj[localAreaCode]["sso_url"];
    var phoneNum = $("#inputname").attr("value");
    var loginUsertype = "";
    if ($("#loginUsertype").val().replace(/\s+/g, "") == "") { //默认老师
        $("#loginUsertype").attr("value", "teacher");
        loginUsertype = "teacher";
    } else {
        loginUsertype = $("#loginUsertype").val();
    }
		var queryurl = ssourl + "/sso/interface/queryLoginUser.jsp?q=" + phoneNum + "&timestamp=" + Math.floor(Math.random() * 10000) + "&loginUsertype=" + loginUsertype + "&jsoncallback=?";
        $.getJSON(queryurl, function(result) {
                    $("#roleList").html("");
                    if (result.length > 0) {
                        for (var num = 0; num < result.length; num++) {
                            $("#roleList").append('<li><a href="javascript:;" roleid="' + result[num].username + '" onclick="selRole(this);" onmouseover="curRole('+num+')" >' + result[num].role + '</a></li>');
                        }
						$("#roleDiv").css("display", "block");
						$("#username").attr("value", result[0].username);
						$("#roleDiv").attr("phonenum", phoneNum);
                    }
					else{
						$("#roleDiv").css("display", "none");
						
					}
              
        });
}
function selRole(sobj) {
    try {
        $("#username").attr("value", $(sobj).attr("roleid"));
        $("#roleDiv").css("display", "none");
        $(document).unbind("click");
    } catch (e) {
        alert(e);
    }
}
//提示无角色可选择
function alertNoRole(flag) {
    if (flag) {
        $("#getrolemsg").show();
        window.setTimeout("$('#getrolemsg').hide(200);", 1000);
    }
}
//登录
var localAreaCode = '';
function tologin() {
    $.ajaxSetup({async: false});
    var sumbitflag = true;
    var ssourl = '';
    var domain = '';
    var eng_url = '';
    var inputname = $("#inputname").attr("value").replace(/\s+/g, "");
    if ($("#username").val() == "")
        $("#username").attr("value", inputname);

    var username = $("#username").attr("value").replace(/\s+/g, "");
    var pwd = $("#pwd").attr("value").replace(/\s+/g, "");
    var loginUsertype = $("#loginUsertype").attr("value").replace(/\s+/g, "");
    if (inputname == "" || inputname == '用户名') {
        show_simple_mess("用户名不能为空", 'inputname');
        sumbitflag = false;
        return false;
    } else if (pwd == "") {
        show_simple_mess("密码不能为空", 'pwd');

        sumbitflag = false;
        return false;
    }
    if ($("#area").val() != '选择地区') {
        localAreaCode = $("#area").val();
        ssourl = mapObj[localAreaCode]["sso_url"];
        domain = mapObj[localAreaCode]["domain"];
        eng_url = mapObj[localAreaCode]["eng_url"];
    }
    else {
        show_simple_mess("请选择所在地区", '');
        sumbitflag = false;
        return false;
    }
    // if ($("#validateCode").val() == "") {
    //     show_simple_mess("验证码不能为空", 'validateCode');
    //     sumbitflag = false;
    //     return false;
    // }
    // else {
    //     var validateCode = $("#validateCode").val();
    //     $.get(Baseurl + '/Public/check_verify?random=' + Math.random(), {code: validateCode}, function(result) {
    //         if (result.indexOf("1") == -1) {
    //             show_simple_mess("验证码错误，请重新输入", 'validateCode');
    //             re_verifyimg();//重新生成验证码
				// $("#validateCode").val('');
    //             sumbitflag = false;
    //         }
    //         else{
    //            sumbitflag = true; 
    //         }

    //     });
    // }
    if (sumbitflag) {
        if ($("#savepass").attr("checked")) {
            $.cookie("pwd", $("#pwd").val(), {path: '/'});
            $.cookie("savepass", 1, {path: '/'});
        } else {
            $.cookie("pwd", null, {path: '/'});
            $.cookie("savepass", 0, {path: '/'});
        }
        //登录置为不可用
        $("#loginbuttion").attr("disabled", true);
        $.cookie("inputname", inputname, {path: '/'});

        if ($("#loginUsertype").val().replace(/\s+/g, "") == "") { //默认老师
            $("#loginUsertype").attr("value", "teacher", {path: '/', domain: domain});
            $.cookie("loginUsertype", $("#loginUsertype").val().replace(/\s+/g, ""), {path: '/', domain: domain});
        } else {
            $.cookie("loginUsertype", $("#loginUsertype").val().replace(/\s+/g, ""), {path: '/', domain: domain});
        }
        pwd = hex_md5(pwd);
        var loginurl = ssourl + '/sso/verifyAuthInfo?data={"loginUsertype":"' + loginUsertype + '","inputname":"' + inputname + '","username":"' + username + '","pwd":"' + pwd + '","schoolId":"","appFlg":"portal","isPortal":"1","encodeU":"0"}&jsoncallback=?';
        //prompt('',loginurl);
        $.getJSON(loginurl, function(result) {
            if (result.authFlg == '0')
            {
                islogin = true;
                $.cookie("username", result.user.username, {path: '/', domain: domain});
                $.cookie("usertype", result.user.usertype, {path: '/', domain: domain});
                $.cookie("truename", result.user.truename, {path: '/', domain: domain});
                $.cookie("ut", result.ut, {path: '/', domain: domain});
                $.cookie("schoolId", result.user.school.schoolId, {path: '/', domain: domain});
                if (result.user.schoolClasses.length > 0)
				{
					$.cookie("gradeCode", result.user.schoolClasses[0].gradeCode, {path: '/', domain: domain});
					$.cookie("classId", result.user.schoolClasses[0].classId, {path: '/', domain: domain});
				}
                $.cookie("areacode", result.user.area.areaId, {path: '/', domain: domain});
                $.cookie('localAreaCode', localAreaCode, {path: '/', domain: domain});
                 $.get(IndexConter+'save_user_login_info',{truename:result.user.truename},function(data){});     //记录用户登录信息
                //welcome(content_type);
                window.location.href=eng_url;

            }
            else
            {
                $("#loginbuttion").attr("disabled", false);
                show_simple_mess(result.authInfo, '',content_type);
                //re_verifyimg();//重新生成验证码
            }
        });
    }

}
function welcome(content_type){
   // $.getJSON(PublicConter+'getyjtdomain',{},function(result){
    $.getJSON('/public/public/json/yjtdomain.json',{},function(result){
        mapObj = result;
        var welstr = '';
        islogin = isLogin();
        //console.log($.cookie('localAreaCode'));
        if($.cookie('ut') !=null){
           
            var portal = mapObj[$.cookie('localAreaCode')]["portal_url"];
            
            userid = $.cookie('username')
            var tmsurl = "";
            var truename = "";  
            usertype = $.cookie('usertype');
            if(usertype == 2 || usertype == 3){ //使用教师信息查询接口
            
                tmsurl = mapObj[$.cookie('localAreaCode')]["tms_url"]+'/tms/interface/queryTeacher.jsp?queryType=byName&jsoncallback=?&username='+userid;
                $.getJSON(tmsurl,function(result){
                    //alert(result.truename);
                    truename = result.truename; 
                    welstr = '欢迎你 <span>'+truename+ '</span>，<a href="javascript:void(0)" onclick="outlogin();">退出</a>';
                    $('#welcome').html(welstr+'<div id="outligin"></div>');
                   
                });
                //http://IP
            }
            if(usertype == 4){//学生
                tmsurl = mapObj[$.cookie('localAreaCode')]["tms_url"]+'/tms/interface/queryStudent.jsp?queryType=byNames&jsoncallback=?&usernames='+userid;
                $.getJSON(tmsurl,function(result){
                    truename = result.rtnArray[0].realname;
                    welstr = '欢迎你 <span>'+truename+ '</span>，<a href="javascript:void(0)" onclick="outlogin();">退出</a>';
                    $('#welcome').html(welstr+'<div id="outligin"></div>');
                    
                });
            }
            if(usertype == 0){//家长
                tmsurl = mapObj[$.cookie('localAreaCode')]["tms_url"]+'/tms/interface/queryStudent.jsp?queryType=byParent&jsoncallback=?&parentAccount='+userid;
                $.getJSON(tmsurl,function(result){
                    //truename = result.rtnArray;
                    //truename = truename.realname;
                truename = result.rtnArray[0].studentParents[0].realname;
                    welstr = '欢迎你 <span>'+truename+ '</span>，<a href="javascript:void(0)" onclick="outlogin();">退出</a>';
                    $('#welcome').html(welstr+'<div id="outligin"></div>');
                        
                });
            }
             
        }
        else{
            loginflag = 0;
            welstr = '欢迎您，您目前还是游客 ，<a href="javascript:void(0)" onclick="showOrHide(\'login\',\'show\')">登录</a>';
            $('#welcome').html(welstr+'<div id="outligin"></div>');
            //showOrHide('login','show');
        }
    });
}
function outlogin() {
    // alert('ss');
    // console.log($.cookie('localAreaCode'));
    if ($.cookie('localAreaCode')) {
        var ssourl = mapObj[$.cookie('localAreaCode')]["sso_url"];
        var domain = mapObj[$.cookie('localAreaCode')]["domain"];
    }
    $('#outlogin').html('<iframe width=0 height=0 src="' + ssourl + '/sso/logout_i.jsp"></iframe>');
    $.cookie('ut', null, {path: '/', domain: domain});
    window.location.reload();
   // window.location.href = '/';
   //welcome();
}
function focuss(t) {
    var a = $(t).val();
    if (a == '用户名' || a == '验证码' || a == '密码') {
        $(t).val("");
    }

    $(t).css('color', '#333');
    $(t).parent().removeClass();
    $(t).parent().addClass("bor_hover");
}

function blurr(t) {
    $(t).css('color', '#aaa');
    $(t).parent().removeClass();
    $(t).parent().addClass("bor");
}
function findpwd() {
    var areacode = '';
	var tmsurl = '';
	var portalurl = '';
	   if ($("#area").val() != '选择地区') {
        areacode = $("#area").val();
        tmsurl = mapObj[areacode]["tms_url"];
		portalurl = mapObj[areacode]["portal_url"];
		window.open(tmsurl+'/tms/ucUser/forgetPwdAccount.do?pram=1&portalUrl='+portalurl);
    }
}

function register() {

	var areacode = '';
	var tmsurl = '';
	var portalurl = '';
	   if ($("#area").val() != '选择地区') {
        areacode = $("#area").val();
        tmsurl = mapObj[areacode]["tms_url"];
		portalurl = mapObj[areacode]["portal_url"];
		window.open(tmsurl+'/tms/register/registerS.do?pram=1&portalUrl='+portalurl);
    }

}
function curRole(num){

	$('#roleList li a').removeClass('active');
	$('#roleList li a:eq('+num+')').addClass('active');
}
function selectchange() {
	clearusername();
    if (($('#area').val() == '1.' || $('#area').val() == '25.') && $('#student').attr('class') == 'sel') {  //如果是学生身份并且是郑州和河南地区显示注册按钮

        $(".mt20").addClass("qk2");
        $(".greenBtn").show();
    }
    else {
        $(".mt20").removeClass("qk2");
        $(".greenBtn").hide();
    }
}