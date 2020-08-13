var content_type = 'text';
var def_postype = 0; // 0-课文章节;1-课文内容
var def_obj;
//$.ajaxSetup({async: false});
//页面内容加载完毕后执行事件
function bodyonloadInit(){
    $('nav a:eq(2)').addClass('cur');
    $("#jplayer").jPlayer({
            swfPath: '/public/public/js',
            wmode: "window",
            supplied: "mp3",
            preload: "none",
            volume: "1"
    });
    mp = new myplay();
    $("#banben").click(function(){
        showOrHide('top','show',content_type);
    });   
    welcome(content_type);
    get_user_set_version(content_type,true);
    $('#versionok').click(function(){
        var r_grade,r_volume,r_version;
        r_grade =$('.nianji li[class=sel5] a').attr('r_grade');
        r_volume =$('.xueqi li[class=sel5] a').attr('r_volume');
        r_version = $('.banben li[class=sel] a').attr('r_version');
        //alert(r_grade+r_volume+r_version);
        set_version_sumbit(r_grade,r_volume,r_version,content_type);
    }); 
}

/**
 * 同步课文单元列表显示 
 * @param {type} url
 * @param {type} num
 * @returns {undefined}
 */
function show_unit_text_list(postype) {
    isfold = false;
    mp.clear();
    $.getJSON(TextConter + 'unitinfo_text', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid}, function(result) { 
        //alert(result.length);
        htmlstr = '';
        $('.catalog').hide();
        $(result).each(function(i,val){
            if(val.is_unit == '1'){
                isfold = true;
            }
            if(val.is_unit == '1'){
                 htmlstr+='<a ks_code="'+$(this).attr('ks_code')+'" ks_name_short="'+$(this).attr('ks_name_short')+'" is_unit="'+$(this).attr('is_unit')+'" href="javascript:void(0);" ></a>';
            }
            else{
                if(val.textcount > 0){
                    htmlstr+='<a ks_code="'+$(this).attr('ks_code')+'" ks_name_short="'+$(this).attr('ks_name_short')+'" is_unit="'+$(this).attr('is_unit')+'" href="javascript:void(0);" ></a>';
                }
            }
           
        });
        //alert(htmlstr)
        $('.catalog').html(htmlstr);
        htmlstr = '';
        if(!isfold){
            $('.catalog a').each(function(i){
                htmlstr+='<li><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:;" flag="children" >'+$(this).attr('ks_name_short')+'<b>></b></a></li>';
            });
        }
        else{
            htmlstr = get_fold_menu();
        }
        $('.catalog').show();
        $('.catalog').html(htmlstr);
        fold_menu();
        if(isfold){
            $(".catalog .level2_menu a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                show_chapter_list($(this).attr('ks_code'),def_postype,$('.catalog .level2_menu a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=parent]:eq(0)").click();
        }
        else{
            $(".catalog li a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                show_chapter_list($(this).attr('ks_code'),def_postype,$('.catalog li a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=children]:eq(0)").click();
        }
    });
}

/**
 * 获取单元下课文章节列表并展示 function show_word_list 对应控制器方法/index/get_unit_word
 * @param {type} def_unitid  单元ID
 * @param {type} postype 单词内容类型 0跟读,1学习,2记忆,3听写
 * @param {type} unitnum  当前单元位置
 * @param {type} wordpage  当前展示的单词位置,单词记忆和单词学习翻页时使用
 * @returns {undefined}
 */
function show_chapter_list(unitid,postype,unitnum,chapterpage) {
    var tempunitnum;
    //alert(unitid);
    if(isfold){
        tempunitnum = 1;
    }
    else{
        tempunitnum = 0;
    }
    if (!islogin && unitnum)
    {
        //open_tishi_div();
        isLogin();
    }
    else{
        mp.clear();
        def_unitid = unitid;
        $.get(TextConter + 'get_chapter_list', {unitid: unitid}, function(result) {
            //alert(result.length);
            if(result.length > 121){
                if(isfold){
                    $('.level2_menu a[flag=children]').removeClass('sel21');
                    $(def_obj).addClass('sel21');
                }
                else{
                    $('.catalog li a[flag=children]').removeClass('cur');
                    $(def_obj).addClass('cur');
                }
                //$('.catalog li a:eq(' + unitnum + ')').addClass('cur');
                $('.rightCon').html(result);
            }
            else{
                show_simple_mess('该章节下没有课文资源，请浏览其他章节!','no');
            }
            
        });
    }
}

//获取课文正文
function show_text_info(unitid, chapter_page) {
    mp.clear();
    mp.index = 0;
    $.get(TextConter + 'get_text_info', {unitid: unitid, chapter_page: chapter_page,gradeid:def_gradeid,termid:def_termid,versionid:def_versionid}, function(result) {
        $('.rightCon').html(result);
        load_text_read();

    });
}
/**
 * 课文阅读事件方法
 * @returns {undefined}
 */
function load_text_read() {
    wp_progress = 0;
   $(".readList li span").hide();//中文翻译隐藏;
    $('.readList strong a').hide();
    $('.playBtn').bind('click', function() {
        if ($(this).attr('class').indexOf('active') > 0) {  //正在播放时点击，则改为停止样式
            $(this).removeClass('active');
            $(this).attr('title', '播放');
            mp.pause();
        }
        else {                              //停止时点击，则改为播放样式
            $(this).addClass('active');
            $(this).attr('title', '暂停');
             twordplay('.readList strong', 'a', 'active', 1, 1, 'text', '.playBtn');
        }
    });
    $('.readList strong a').each(function(num) { //给列表加上单独播放事件
        $(this).bind('click', function() {
            $('.readList strong').removeClass('active');
            $('.readList strong a').removeClass('active');
            single_play($(this), 'active', 1, 1, 'text');
        });
    });
    $('input[name="text_select"]:eq(0)').bind('click', function() {  //显示原文释义选择框事件
        if ($(this).is(':checked')) {
            $(".readList li span").show(); //选中显示释义
        }
        else {
            $(".readList li span").hide();//取消隐藏释义
        }
    });
    $('input[name="text_select"]:eq(1)').bind('click', function() {  //单句根读选择框事件
        mp.clear();
        if ($(this).is(':checked')) {
            wp_progress = 0;
            $('.playBtn').removeClass('active');
            $('.playBtn').hide();
           // alert($('.readList strong a').length);
            $('.readList strong a').show();
        }
        else {
            mp.clear();
            $('.playBtn').show();
            $('.readList strong a').hide();
        }
    });
}
