//def_gradeid:默认年级ID;;
//def_termid:默认学期ID;;
//def_versionid:默认版本ID;;
//def_postype:版块标识:0跟读;1学习;2记忆;3听写
//userid;usertype; 用户名,用户类型;
//def_unitid::默认单元ID;;def_contentflag:页面内容类型;0首页;1同步单词;2同步课文
//wordmp3url:单词MP3地址;wordpicurl:单词图片地址:;textmp3url:课文mp3地址;jsurl:js目录地址
var def_gradeid = 0, def_termid = 0, def_versionid = 0, def_postype = 0, userid = 0, usertype = 0, def_unitid = 0, def_contentflag = 1, wordmp3url = '', wordpicurl = '', textmp3url = '', jsurl = '';
var islogin = true; //登录标识
var isfold = false;     //是否展示折叠菜单
var wp_progress = 0;//控制单词跟读的播放与暂停,0播放,1暂停
var Baseurl = '';
var IndexConter = Baseurl + '/Index/';//首页控制器访问地址
var WordConter = Baseurl + '/Word/';//同步单词控制器访问地址
var TextConter = Baseurl + '/Text/';//同步课文控制器访问地址
var PublicConter = Baseurl + '/Public/';//公用控制器访问地址
var ListenConter = Baseurl + '/Listen/';//听力训练控制器访问地址
var RecordConter = Baseurl + '/Record/';//公用控制器访问地址
var mp = '';
var htmlstr = '';
var temphtmlstr = '';
var examsdata = ''; //总试卷信息
var stemdata ='';   //总题干信息
var childstemdata ='';   //总题干信息
var quetiondata = ''; //问题信息
var stemttsdata = '';
var quettsdata = '';
var answerdata = '';
var itemsdata = ''; //选项信息
var itemsclass = '';
var itemshtml = '';
var paixuhtml1 ='';
var paixuhtml2='';
var examstts_type = 1; //mp3类型 1-系统生成 2-手工上传
var mp3_progress = '';
var playurl = '';
var mapObj = {};//外层集合对象，存放地区编码以及对应的地区系统编码域名集合对象
//去除HTML tag
function removeHTMLTagimg(str) {
    str=str.replace('/uploads',resource_path+'/uploads');//去掉 
    return str;
}
//去除HTML tag
function removeHTMLTaginput(str) {
    //str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
    // str = str.replace(/<(?!input).*?>/g, '')
    // str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
    // str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
   // str=str.replace(/ /ig,'');//去掉 
    return str;
}
//
function openloading(){
    $('#loading').show();
    $('#below').show();
}
//
function hideloading(){
    $('#loading').hide();
    $('#below').hide();
}
//保存cookie(name-名称，value-取值，expires-期限，path-cookie路径，domain-cookie的Domain,secure-是否需要保密传送)
function saveCookie(name, value, expires, path, domain, secure) {
    var strCookie = name + "=" + value;
    if (expires) {
        //计算Cookie的期限,   参数为天数  
        var curTime = new Date();
        curTime.setTime(curTime.getTime() + expires * 24 * 60 * 60 * 1000);
        strCookie += "; expires=" + curTime.toGMTString();
    }
    //Cookie的路径  
    strCookie += (path) ? "; path=" + path : "";
    //Cookie的Domain  
    strCookie += (domain) ? ";   domain=" + domain : "";
    //   是否需要保密传送,为一个布尔值  
    strCookie += (secure) ? ";   secure" : "";
    document.cookie = strCookie;
}
//获取cookle
function getcookle(name) {
    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arr != null && arr != "")
        return unescape(arr[2]);
    return "";
}
// 获取URL中param参数的值
function getParameter(param) {
    var query = window.location.search;
    var iLen = param.length;
    var iStart = query.indexOf(param);
    if (iStart == -1)
        return null;
    iStart += iLen + 1;
    var iEnd = query.indexOf("&", iStart);
    if (iEnd == -1)
        return query.substring(iStart);
    return query.substring(iStart, iEnd);
}
//菜单折叠
function fold_menu(){
    $(".catalog li a[flag=parent]").unbind('click').click(function(){
        if($(this).hasClass('cur')){
            $(this).removeClass("cur");
            $(this).parent().next(".level2_menu").slideUp();
        }
        else{
            $(".catalog li a[flag=parent]").removeClass('cur');
            $(".level2_menu").slideUp();
            $(this).parent().next(".level2_menu").slideDown();
            $(this).addClass("cur");
           $(this).parent().next(".level2_menu").find('a:first').click();

        }
    });
}
//获取用户设置的版本信息
//content_type :页面所在位置 word 同步单词 text 同步课文,listen 听力训练
//isshowlist 获取到用户设置的版本信息后是否加载资源列表 true 加载 ;flase 不加载
function get_user_set_version(content_type,isshowlist){
    htmlstr = '';
    $('.nianji li a[r_volume=0000]').parent().hide();
    $.getJSON(PublicConter+'get_user_set_version',{random:Math.random()},function(result){
        htmlstr = result.gradename+'.'+result.termname+'.'+result.versionname+'&nbsp; <i class="fa fa-cog"></i>';
        $('#user_set_version').html(htmlstr);
        def_gradeid = result.r_grade;
        def_termid = result.r_volume;
        def_versionid = result.r_version;
        if (def_gradeid == '0009') {
            $('.nianji li a[r_volume=0000]').parent().show();
        }
        $('.nianji li').removeClass('sel5');
        //alert(def_termid);
        $('.nianji li a[r_grade='+def_gradeid+']').parent().addClass('sel5');
        $('.nianji li a[r_volume='+def_termid+']').parent().addClass('sel5');
        get_vesion_list(def_gradeid,def_termid,def_versionid,content_type,isshowlist);
         //alert(htmlstr);
    });
    $('.nianji li a[r_grade]').unbind('click').click(function(){
        if ($(this).attr('r_grade') == '0009') {
            $('.xueqi li a[r_volume=0000]').parent().show();
        }
        else{
            $('.xueqi li a[r_volume=0000]').parent().hide();
            if($('.xueqi li a[r_volume=0000]').parent().hasClass('sel5')){
                $('.xueqi li a[r_volume=0000]').parent().removeClass('sel5');
                $('.xueqi li a[r_volume=0001]').parent().addClass('sel5');
            }
        }
        $('.nianji li a[r_grade]').parent().removeClass('sel5');
        $(this).parent().addClass('sel5');
        //根据所选择的年级学期获取版本列表，并判断是是否加载资源
        get_vesion_list($(this).attr('r_grade'),$('.xueqi li[class=sel5] a').attr('r_volume'),def_versionid,'no',isshowlist);
    });
    $('.xueqi li a[r_volume]').unbind('click').click(function(){
        $('.xueqi li a[r_volume]').parent().removeClass('sel5');
        $(this).parent().addClass('sel5');
        //根据所选择的年级学期获取版本列表，并判断是是否加载资源
        get_vesion_list($('.nianji li[class=sel5] a').attr('r_grade'),$(this).attr('r_volume'),def_versionid,'no',isshowlist);
    });
}
//获取该年级学期下的版本信息
//  r_grade:年级编码,r_volume:学期编码,r_version:版本编码,content_type:页面所在位置,isshowlist:是否加载资源列表
function get_vesion_list(r_grade,r_volume,r_version,content_type,isshowlist){
    //alert(content_type);
    if(!(r_grade !='0009' && r_volume == '0000'))
      $.getJSON(PublicConter+'get_version',{r_grade:r_grade,r_volume:r_volume},function(result){
            htmlstr = '';
            //alert(result.length)
            if(result.length > 0){
                $('#versionok').show();
                $(result).each(function(i){                
                    htmlstr+='<li><a href="javascript:void(0);" r_version="'+$(this).attr('detail_code')+'"><img src="'+versionimgurl+$(this).attr('pic_path')+'" width="800" height="1160"  alt=""/></a><p>'+$(this).attr('detail_name')+'</p></li>';           
                });
                $('.banben ul').html(htmlstr);
                if($('.banben li a[r_version='+r_version+']').length > 0){
                    $('.banben li a[r_version='+r_version+']').parent().addClass('sel');
                    $('.banben li a[r_version='+r_version+']').before('<span><img src="/public/home/images/check_lv.png" width="20" height="20"  alt=""/></span>');
                }
                else{
                    $('.banben li a[r_version]:eq(0)').parent().addClass('sel');
                    $('.banben li a[r_version]:eq(0)').before('<span><img src="/public/home/images/check_lv.png" width="20" height="20"  alt=""/></span>');
                }
                
                $('.banben li a[r_version]').unbind('click').click(function(){
                    $('.banben li a[r_version]').parent().removeClass('sel');
                    $('.banben li span').remove();
                    $('.banben li a[r_version='+$(this).attr('r_version')+']').parent().addClass('sel');   
                    $('.banben li a[r_version='+$(this).attr('r_version')+']').before('<span><img src="/public/home/images/check_lv.png" width="20" height="20"  alt=""/></span>');
                });
                
            }
            else{
                $('.banben ul').html(htmlstr);
                $('#versionok').hide();
            }
            if(isshowlist){
                if(content_type == 'word'){
                    $.getJSON(WordConter + 'word_isexists', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid},function(result){
                        if(result.length > 0){
                            show_unit_word_list(def_postype);
                        }
                        else{
                             show_message('该版本下没有单词资源,是否选择其他版本？', 'version_set', true);
                        }
                    });
                    
                }
                else if(content_type == 'wordbook'){
                    show_unit_wordbook_list(def_postype);
                }
                else if(content_type == 'text'){
                    $.getJSON(TextConter + 'text_isexists', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid},function(result){
                        if(result.length > 0){
                            show_unit_text_list(def_postype);
                        }
                        else{
                             show_message('该版本下没有课文资源,是否选择其他版本？', 'version_set', true);
                        }
                    });   
                }
                else if(content_type == 'listen'){
                    $.getJSON(ListenConter + 'exams_isexists', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid},function(result){
                        //alert(result.length);
                        if(result.length > 0){
                            show_unit_exams_list(def_postype);
                        }
                        else{
                             show_message('该版本下没有试卷资源,是否选择其他版本？', 'version_set', true);
                             
                        }
                    });  
                }
            }
            
        });
}

 function open_tishi_div(){
   // alert($('#denglu').length)
    $('#tishi').fadeIn(500);
    $('#below').show();
}

function open_msg_div(content){

    $('#showmsg').html(content);
    $('#message').show();
   $('#below').show();
    setTimeout(function(){
        $('#message').hide();
        $('#below').hide();
    },1000);
}
/**
 * 弹出层控制显示与隐藏
 * divid ：弹出层标识
 * showflag 弹出层类型 top=版本和login=登录 
 * content_type :页面所在位置
 */
function showOrHide(divid, showflag,content_type) {
    $('#tishi').hide();
    if (showflag == 'show') {
        if(divid == 'top'){
            $('#' + divid).slideDown();
            get_user_set_version(content_type);
        }
        else{
            $('#' + divid).fadeIn(500);
            //$('#' + divid).show();
           
        }
         $('#below').show();
    }
    else {
        if(divid == 'top'){
             $('#' + divid).slideUp();
        }
        else{
            $('#' + divid).hide();
            
        }
        $('#below').hide();
    }
    if (divid == 'login' && showflag == 'show') {
        setuserdefault();//加载用户登录弹出层事件
    }
}
/**
 * 信息提示框
 * @param {type} content 提示内容
 * @param {type} showtype提示之后的操作类型 login:弹出登录框;version_set:弹出版本选择框;;no:不执行任何操作
 * @param {type} chanceflag 是否显示取消按纽 true:显示;false:不显示
 * @returns {undefined} content_type 页面所在位置
 */
function show_message(content,showtype,chanceflag,content_type) {
    art.dialog({
        title: '英语同步练提示',
        content: content,
        ok: function() {
             if (showtype == 'login') {
                 showOrHide('login', 'show');
             }
             else if(showtype == 'version_set'){
                showOrHide('top', 'show');
             }
        },
        okValue: '确定',
        cancelValue: '取消',
        cancel: function() {
            if (showtype == 'version_set') {
				showOrHide('top', 'hide');
            }
            else{
                showOrHide('login', 'hide');
            }
        },
        fixed: true
                //lock: true
    });
}
//简单信息提示
function show_simple_mess(content, showtype,content_type) {
    art.dialog({
        title: '小优提示',
        content: content,

        beforeunload: function() {
            if (showtype == 'collect') {
                show_unit_word_list(0);
            }
            else if (showtype == 'memory_input') {
                $(".inputTxt").focus();
            }
            else if (showtype == 'inputname') {
                focuss($("#inputname"));
            }
            else if (showtype == 'pwd') {
                focuss($("#pwd"));
            }
        },
        time: 2000
    });
}


/**
 * 版本设置按纽提交事件
 * @returns {undefined}
 */
function set_version_sumbit(r_grade,r_volume,r_version,content_type) {
		if (content_type == 'word')
		{
			$.getJSON(WordConter + 'word_isexists', {r_grade: r_grade, r_volume: r_volume, r_version: r_version}, function(result) {
               // alert(result.length);
                //alert(result[0]['ks_name']);
                if (result.length == 0) {
                    show_message('该版本下没有单词资源,是否选择其他版本？', 'version_set', true);
                }
                else {
                    
                    save_user_set_vesion(r_grade,r_volume,r_version,content_type);    //保存用户设置的版本信息
                }
            });
		}
		else if (content_type == 'text')
		{
			$.get(TextConter + 'text_isexists', {r_grade: r_grade, r_volume: r_volume, r_version: r_version}, function(result) {
                if (result.length == 0) {
                    //showOrHide('top', 'hide');
                    show_message('该版本下没有课文资源,是否选择其他版本？', 'version_set', true);
                }
                else {
                    save_user_set_vesion(r_grade,r_volume,r_version,content_type);    //保存用户设置的版本信息

                }
            });
		}
        else if (content_type == 'listen')
        {
             $.get(ListenConter + 'exams_isexists', {r_grade: r_grade, r_volume: r_volume, r_version: r_version}, function(result) {
                if (result.length == 0) {
                    //showOrHide('top', 'hide');
                    show_message('该版本下没有试卷资源,是否选择其他版本？', 'version_set', true);
                }
                else {
                    save_user_set_vesion(r_grade,r_volume,r_version,content_type);    //保存用户设置的版本信息

                }
            });
        }
       
    }
function save_user_set_vesion(r_grade, r_volume, r_version,content_type) {
    def_gradeid = r_grade;
    def_termid = r_volume;
    def_versionid = r_version;
	$.cookie('temp_gradeid',r_grade);
	$.cookie('temp_termid',r_volume);
	$.cookie('temp_versionid',r_version);
    if (islogin) {  //如果用户登录,则记录用户设置的版本
        $.get(PublicConter + 'save_user_version', {gradeid: r_grade, termid: r_volume, versionid: r_version}, function(data) {
            showOrHide('top','hide',content_type);
            //alert(r_grade+r_volume+r_version)
            get_user_set_version(content_type,true);
        });
    }
    else{
        showOrHide('top','hide',content_type);
        get_user_set_version(content_type,true);
    }
}

function re_verifyimg(){
    $('#verifycode').attr("src", Baseurl + '/Public/verify_c?random=' + Math.random());    
}

function goto_record(){
    if(islogin){
        window.location.href=Baseurl+'/record';
    }
    else{
        open_tishi_div();
    }
}

function get_fold_menu(){
    var temphtmlstr = '';
            $('.catalog a').each(function(i){
                if($(this).attr('is_unit') == '1'){    //当前是单元目录 不可点
                    temphtmlstr += '<li><a href="javascript:;" flag="parent">'+$(this).attr('ks_name_short')+'<b>></b></a></li>';
                }
                else{       //当前是非单元目录 可点
                    if($(this).prev().length > 0 && $(this).next().length > 0){     //当前的上一个无素及下一个无素都存在
                        if($(this).prev().attr('is_unit') == '0' && $(this).next().attr('is_unit') == '0'){     //上一个及下一个都可点
                            temphtmlstr += '<a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a>';
                        }
                        else if($(this).prev().attr('is_unit') == '1' && $(this).next().attr('is_unit') == '1'){   //上一个及下一个都不可点
                            temphtmlstr += '<div class="level2_menu" style="display:none;"><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a></div>';
                        }
                        else if($(this).prev().attr('is_unit') == '0' && $(this).next().attr('is_unit') == '1'){    //上一个可点下一个不可点
                                temphtmlstr += '<a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a></div>';
                        }
                        else{
                             temphtmlstr += '<div class="level2_menu" style="display:none;"><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a>';           //上一个不可点下一个可点
                        }
                    }
                    else{
                        if($(this).prev().length == 0){
                            if($(this).next().attr('is_unit') == '1'){
                                temphtmlstr += '<div class="level2_menu" style="display:none;"><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a></div>';    //第一个
                            }
                            else{
                                temphtmlstr += '<div class="level2_menu" style="display:none;"><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a>';    //第一个
                            }
                            
                        }
                        else{
                            if($(this).prev().attr('is_unit') == '1'){
                                temphtmlstr += '<div class="level2_menu" style="display:none;"><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a></div>';                                        //最后一个
                            }
                            else{
                                temphtmlstr += '<a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" flag="children" >'+$(this).attr('ks_name_short')+'</a></div>';                                        //最后一个
                            }
                            
                        }
                    }
                }
            });
            return temphtmlstr;
}

function isLogin(){
    var ut = $.cookie('ut');
    if(ut == '' || ut == 'null' || ut == null){
        
        showOrHide('login','show');
        return false;
    }
    else{
        return true;
    }
}