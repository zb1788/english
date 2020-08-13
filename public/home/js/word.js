var content_type = 'word';
var def_postype = 1; // 0-跟读 1-学习 2-记忆 3-听写
var def_obj ='';
//$.ajaxSetup({async: false});
//页面内容加载完毕后执行事件
function bodyonloadInit(){
    $('nav a:eq(1)').addClass('cur');
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
        set_version_sumbit(r_grade,r_volume,r_version,'word');
    });
   
}
function word_book_onloadInit(){
    $('nav a:eq(1)').addClass('cur');
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
    show_wordbook_list(0,0,0,0);
    // get_user_set_version('wordbook',true);
    // $('#versionok').click(function(){
    //     var r_grade,r_volume,r_version;
    //     r_grade =$('.nianji li[class=sel5] a').attr('r_grade');
    //     r_volume =$('.xueqi li[class=sel5] a').attr('r_volume');
    //     r_version = $('.banben li[class=sel] a').attr('r_version');
    //     //alert(r_grade+r_volume+r_version);
    //     set_version_sumbit(r_grade,r_volume,r_version,'word');
    // });
   
}
/**
 * 同步单词单元列表显示 function show_unit_word_list 
 * @param {type} url
 * @param {type} num
 * @returns {undefined}
 */
function show_unit_word_list(postype) {
    isfold = false;
    mp.clear();
    $.getJSON(WordConter + 'unitinfo_word', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid}, function(result) { 
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
                if(val.wordcount > 0){
                    htmlstr+='<a ks_code="'+$(this).attr('ks_code')+'" ks_name_short="'+$(this).attr('ks_name_short')+'" is_unit="'+$(this).attr('is_unit')+'" href="javascript:void(0);" ></a>';
                }
            }
           
        });
        $('.catalog').html(htmlstr);
        htmlstr = '';
        if(!isfold){
            $('.catalog a').each(function(i){
                htmlstr+='<li><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:;" flag="children" >'+$(this).attr('ks_name_short')+'</a></li>';
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
                show_word_list($(this).attr('ks_code'),def_postype,$('.catalog .level2_menu a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=parent]:eq(0)").click();
        }
        else{
            $(".catalog li a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                show_word_list($(this).attr('ks_code'),def_postype,$('.catalog li a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=children]:eq(0)").click();
        }
    });
}
/**
 * 生词本下单元列表显示 function show_unit_word_list 
 * @param {type} url
 * @param {type} num
 * @returns {undefined}
 */
function show_unit_wordbook_list(postype) {
   // alert(postype);
    mp.clear();
    $.getJSON(WordConter + 'unitinfo_wordbook', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid}, function(result) { 
        //alert(result.length);
        htmlstr = '';
        $(result).each(function(i){
            //alert($(this).attr('ks_name_short'));
            if($(this).attr('is_unit') == '1'){
                htmlstr += '<li><span>'+$(this).attr('ks_name_short')+'</span></li>';
            }
            else{
                htmlstr+='<li><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:void(0);" >'+$(this).attr('ks_name_short')+'</a></li>';
            }
        });
        $('.catalog').html(htmlstr);
        //$('.catalog li a:eq(0)').addClass('cur');
        $('.catalog li a').unbind('click').click(function(){
           
             $('.catalog li a').removeClass('cur');
             $(this).addClass('cur');
           show_wordbook_list($(this).attr('ks_code'), 0, $('.catalog li a').index(this), 0);
        });
        $('.catalog li a:eq(0)').click();
    });
}
/**
 * 获取单元下单词列表并展示 function show_word_list 对应控制器方法/index/get_unit_word
 * @param {type} def_unitid  单元ID
 * @param {type} postype 单词内容类型 0跟读,1学习,2记忆,3听写
 * @param {type} unitnum  当前单元位置
 * @param {type} wordpage  当前展示的单词位置,单词记忆和单词学习翻页时使用
 * @returns {undefined}
 */
function show_word_list(unitid, postype,unitnum,wordpage) {

    mp.clear();
    var tempunitnum;
    //alert(unitid);
    if(isfold){
        tempunitnum = 1;
    }
    else{
        tempunitnum = 0;
    }
    if (!islogin && unitnum > 0)
    {
        //show_message('你还没有登录,请登录后再浏览其他单元,是否现在登录', 'login', true);
       // art.dialog.get("tishi");
       //open_tishi_div();
       isLogin();
       //showOrHide('tishi', 'show',content_type);
    }
    else{
    mp.index = 0;
    mp.clear();
    def_unitid = unitid;
    def_postype = postype;
    var mp3list = ''; //顺序播放列表
    //var mp3list = ''; //顺序播放列表
    if ($.cookie('study_word_index')) {
        wordpage = $.cookie('study_word_index');
    }
    $.get(WordConter + 'get_word_list', {unitid: unitid, curpostype: postype, wordpage: wordpage, gradeid: def_gradeid, curunitpos: unitnum}, function(result) {
        if(result.indexOf('isexists="yes"') > 0){
            if(isfold){
                    $('.level2_menu a[flag=children]').removeClass('sel21');
                    $(def_obj).addClass('sel21');
            }
            else{
                $('.catalog li a[flag=children]').removeClass('cur');
                $(def_obj).addClass('cur');
            }
            $('.rightCon').html(result);
            if (def_postype == 0) {  //单词跟读,顺序播放和单独播放同时存在
                load_word_read(wordpage);//加载单词跟读方法
            }
            else if (def_postype == 1) {   //单词学习mp3播放
                single_play($('a.readBtn'), 'active', 1, 1, def_postype);//自动播放该单词
                $('a.readBtn').bind('click', function() {  //朗读按纽点击播放
                    single_play($('a.readBtn'), 'active', 1, 1, def_postype);
                });
                $('a.sound_single').bind('click', function() {  //例句点击播放
                    example_play($('a.sound_single'), 'active', 1, 1, def_postype);
                });
            }
            else if (def_postype == 2) {//单词记忆mp3播放
                single_play($('a.readBtn'), 'active', 1, 1, def_postype);//自动播放该单词
                $('a.readBtn').bind('click', function() {  //朗读按纽点击播放
                    single_play($('a.readBtn'), 'active', 1, 1, def_postype);
                });
                $(".icon_dui").hide(); //隐藏对号
                $(".icon_cuo").hide(); //隐藏错号
                $("big").hide();        //隐藏正确单词
                $('#explains').hide();  //隐藏解释信息
                $('.find a').bind('click', function() {   //找一找按纽事件
                    word_search($(this));  //点击时执行找一找事件函数
                });
                $('#surebtn').bind('click', function() {  //单词记忆确定按纽事
                    var input_word = $.trim($(".inputTxt").val());//用户输入的单词
                    if (input_word == "" || input_word == "请输入你听到的单词") {
                        show_simple_mess('请输入你听到的单词', 'memory_input');
                    }
                    else {
                        word_true_false(); //有输入时判断输入对错
                    }
                });
            }
            else if (def_postype == 3) {   //单词听写事件
                load_dictation();
            }
            $.cookie('study_word_index', null);
        }
        else{
             show_simple_mess('该章节下没有单词资源，请浏览其他章节!','no');
        }
    });
    }
}
/**
 * 获取生词本单元下单词列表并展示 function show_word_list 对应控制器方法/index/get_book_list
 * @param {type} def_unitid  单元ID
 * @param {type} book_pos 单词内容类型 0跟读,1学习,2记忆,3听写
 * @param {type} unitnum  当前单元位置
 * @param {type} wordpage  当前展示的单词位置,单词记忆和单词学习翻页时使用
 * @returns {undefined}
 */
function show_wordbook_list(unitid, postype, unitnum, wordpage) {
    //alert(wordpage);
   // $.cookie('cur_sc_unit_id', unitid);
   //alert(postype);
    $.get(WordConter + 'get_book_list', {unitid: unitid, postype: postype, curunitpos: unitnum,wordpage:wordpage}, function(result) {
        $('.rightCon').html(result);
        if (postype == 0) {  //生词本单词跟读,顺序播放和单独播放同时存在
            load_word_read();  //调用单词听写事件方法
        }
        else {
            load_dictation();//调用听写事件方法
        }
    });
}
/**
 * 单词跟读事件方法,同步单词的单词跟读和生词本里的单词跟读都调用此方法
 * @returns {undefined}
 */
function load_word_read(wordpage) {
    $('.playBtn').bind('click', function() {   //联播按纽点击时事件
        if ($(this).attr('class').indexOf('active') > 0) {  //正在播放时点击，则改为停止样式
            $(this).removeClass('active');
            $(this).attr('title', '播放');
            mp.pause();
        }
        else {                              //停止时点击，则改为播放样式
            $(this).addClass('active');
            $(this).attr('title', '暂停');
            $('.listWord tr a.sound_single').removeClass('active');
            twordplay('.listWord tr', 'a.sound_single', 'active', 1, 1, def_postype, '.playBtn');
        }
    });
    $('.listWord tr a.sound_single').each(function(num) { //给列表加上单独播放事件
        $(this).bind('click', function() {
            $('.listWord tr a.sound_single').removeClass('active');
            single_play($(this), 'active', 1, 1, def_postype);
        });

        if ($.cookie('study_word_index')) {
            single_play($('.listWord tr a.sound_single:eq(' + $.cookie('study_word_index') + ')'), 'active', 1, 1, def_postype);
        }
    });
}
//进入到生词本
function go_wordbook(){
    if(islogin){
        if(parseInt($('#booktotal').text()) > 0){
            window.location.href = WordConter+'wordbook';    
        }
        else{
            show_simple_mess('生词本里没有已收藏的单词', 'no');
        }
        
    }
    else {
       // show_message('你还没有登录,请登录后再查看生词本,是否现在登录', 'login', true);
       //open_tishi_div();
       isLogin();
    }
}
/**
 * 将单词加入到生词本
 * @param {type} unitid 单元ID
 * @param {type} wordid 单词ID engs_word.id
 * @returns {undefined}
 */
function book_collect(unitid, wordid) {
    if (islogin) {
        $.getJSON(WordConter + 'book_collect', {unitid: unitid, wordid: wordid}, function(result) {
                
                $('#collect' + wordid).addClass('focus');
                $('#collect' + wordid).attr('href', 'javascript:Cancel_collect('+unitid+','+wordid+');');
                $('#collect' + wordid).html('<i class="fa fa-heart"></i>&nbsp;已收藏');
                $('#booktotal').html(result);
           
        });
    }
    else {
        //show_message('你还没有登录,请登录后再使用收藏功能,是否现在登录', 'login', true);
        open_tishi_div();
         
    }
}
function Cancel_collect(unitid,wordid) {
    art.dialog({
        title: '小优提示',
        content: '你确定取消该单词的收藏吗',
        ok: function() {
            this.title('取消中…');
            $.get(WordConter + 'book_del', {wordid: wordid}, function(result) {
                $('#collect' + wordid).removeClass('focus');
                $('#collect' + wordid).attr('href', 'javascript:book_collect('+unitid+','+wordid+');');
                $('#collect' + wordid).html('<i class="fa fa-heart-o"></i>&nbsp;收&nbsp;&nbsp;藏');
                $('#booktotal').html(result);//更新页面生词本里的数量
               
            });
        },
        okValue: '确定',
        cancelValue: '取消',
        cancel: function() {
        },
        fixed: true,
        lock: true
    });
}
/**
 * 生词本移出方法
 * @returns {undefined}
 */
function delBook(wordid,unitid, postype, unitnum, wordpage) {
    art.dialog({
        title: '小优提示',
        content: '你确定将该单词从生词本里移出吗',
        ok: function() {
            this.title('移除中…');
            $.get(WordConter + 'book_del', {wordid: wordid}, function(result) {
                $('#booktotal').html(result);//更新页面生词本里的数量
                 if(parseInt($('#booktotal').text()) > 0){
                    //window.location.href = WordConter+'wordbook'; 
                    show_wordbook_list(unitid, postype, unitnum, wordpage)
                    //show_unit_wordbook_list(postype);
                 }
                 else{
                    window.location.href='/word';
                 }
                //show_unit_book_list(0);
            });
        },
        okValue: '确定',
        cancelValue: '取消',
        cancel: function() {
        },
        fixed: true,
        lock: true
    });
}
/**
 * 单词记忆找一找方法
 * @param {type} obj  找一找按纽对象
 * @returns {undefined}
 */
function word_search(obj) {
    var findstr = obj.attr('findstr'); //要查找的单词首字母
    var wordtags = obj.attr('wordtags');//要查找的单词属性
    var wordid = obj.attr('wordid');//当前的单词ID
    var trueword = $('#surebtn').attr('trueword');
    $.getJSON(WordConter + 'word_search_list', {findstr: findstr, wordtags: wordtags, curwordid: wordid, trueword: trueword}, function(result) {
        htmlstr='';
        $(result).each(function(i){
            htmlstr += '<strong><input name="wordsearch" type="radio" value="'+$(this).attr('word')+'"><span>'+$(this).attr('word')+'</span></strong>';
        });
        $('.sele').html(htmlstr);
        $('input[name="wordsearch"]').bind('click', function() { //找一找的单选按钮点击事件
            $(".inputTxt").val($(this).val());
            word_true_false();  //单选按纽点击时自动判断对错
        });
    });
}
/**
 * 单词记忆模板判断输入对错
 * @returns {undefined}
 */
function word_true_false() {
    var trueword = $('#surebtn').attr('trueword');
    var input_word = $.trim($(".inputTxt").val());
    var wordtotal = $('#surebtn').attr('wordtotal');
    var wordunitid = $('#surebtn').attr('wordunitid');
    var wordpostype = $('#surebtn').attr('wordpostype');
    var wordcurunitpos = $('#surebtn').attr('wordcurunitpos');
    var wordcurpage = $('#surebtn').attr('wordcurpage');
    if (input_word == trueword) {
        $(".icon_dui").show(); //输入的答案正确，显示对号,错号隐藏
        $(".icon_cuo").hide();
        $("big").show();  //显示正确单词
        $('#explains').show();//显示解释
        $('.preNext a:eq(0)').removeAttr('onclick');
        $('.preNext a:eq(1)').removeAttr('onclick');
        setTimeout(function() {
            //如果单词正确,2秒后跳转到下一个
            if(Number(wordcurpage) != (Number(wordtotal)-1)){
                show_word_list(wordunitid,wordpostype,wordcurunitpos,(Number(wordcurpage)+1));
            }
        }, 1000);
    }
    else {
        $(".icon_cuo").show();
        $(".icon_dui").hide(); //输入的答案不正确，显示错号，对号隐藏
    }
}
/**
 * 单词听写事件,同步单词的听写和生词本的听写都加载这个事件
 * @returns {undefined}
 */
function load_dictation() {
    wp_progress = 0;
    $('.listInput li big').hide();
    $('.playBtn').bind('click', function() {
        $('#dictation').removeClass('gray');
        if ($(this).attr('class').indexOf('active') > 0) {  //正在播放时点击，则改为停止样式
            $(this).removeClass('active');
            $(this).attr('title', '播放');
            mp.pause();
        }
        else {                              //停止时点击，则改为播放样式
            $(this).addClass('active');
            $(this).attr('title', '暂停');
            twordplay('.listInput li', 'input', 'cur', 2, 1, def_postype, '.playBtn');
        }
    });
    $('#dictation').bind('click', function() {  //听写完成按钮点击事件
        mp.clear();
        $(this).addClass('gray');
        if ($('.playBtn').attr('class').indexOf('active') > 0)
        {
            $('.playBtn').removeClass('active');
            $('.playBtn').attr('title', '播放');
            mp.pause();
        }
        dictation_check();//检查听写结果
    });
}
/*
 * 单词听写模块检查听写结果
 * @returns {undefined}
 */
function dictation_check() {
    $('#dictation').removeClass('gray');
    var Runitid = '', Rwordid = '', Wunitid = '', Wwordid = '', InputNum = 0;
    var saveflag = false;  //是否向数据库生词本添加生词,默认false
    var checkflag = true; //检查结果标志
    var InputLength = $('input[name="dictation_input"]').length;
    $('input[name="dictation_input"]').each(function(num) {
        if ($.trim($(this).val()) != '') {    //如果输入框里有值,则与正确单词进行比较
            if ($.trim($(this).val()) == $(this).attr('trueword')) { //如果输入正确
                $(this).parent().removeClass("wrong");
                $(this).next().show();  //显示对号及正确的单词
                $(this).next().find('i').show();
                //添加学习记录
                Rwordid = $(this).attr("wordid");  //获取听写正确的单词ID
                Runitid = $(this).attr("unitid");  //获取听写正确的单词ID
                save_study_word_info(def_postype, Runitid, Rwordid);//将听写的单词加入学习记录
            }
            else {//如果输入错误,将出错的单词添加到生词本
                $(this).parent().addClass("wrong");
                $(this).next().show();//显示对号及正确的单词
                $(this).next().find('i').hide(); //将对号隐藏
                Wwordid = $(this).attr("wordid");  //获取听写错误的单词ID
                Wunitid = $(this).attr("unitid");  //获取听写错误的单词ID
                checkflag = false;
                save_study_word_info(def_postype, Wunitid, Wwordid); //将听写的单词加入学习记录
                save_W_word_collect(Wunitid, Wwordid);//将出错的单词加入生词本
            }

            InputNum++;
            saveflag = true;
        }
    });
    if (!saveflag) {
        show_simple_mess('请至少输入一个你听到的单词', 'no');
    }
    else {
        if (def_postype == 4 && !checkflag) {
            show_simple_mess('本次听写的是生词本的单词,还是有错误,还需要多多练习哦', 'no');
        }
        else if (!checkflag) {
            if(islogin){
                show_simple_mess('出错的单词已经添加到生词本,需要多多练习哦', 'no');
            }
            else{

                show_message('你还没有登录，听写出错的单词无法加入生词本,是否现在登录', 'login', true);
            }
        }
        else if (InputNum < InputLength - 1) {
            show_simple_mess('本次听写输入的单词全部答对,但是还有空的单词没写,需要再来一次吗', 'no');
        }
        else {
            show_simple_mess('恭喜你,本次听写全部正确', 'no');
        }
    }
}
/**
 * 将单词听写出错的单词加入生词本,如果生词本里存在则不添加
 * @param {type} unitid
 * @param {type} wordid
 * @returns {undefined}
 */
function save_W_word_collect(unitid, wordid) {
    if (islogin) {
        $.getJSON(WordConter + 'book_collect', {unitid: unitid, wordid: wordid}, function(result) {
            $('#booktotal').html('（' + result + '）');
        });
    }
}
function save_study_word_info(wordflag, unitid, wordid) {
    //alert(wordflag);
    if (islogin) {
        $.get(WordConter + 'save_study_word_info', {wordflag: wordflag, unitid: unitid, wordid: wordid}, function() {
        });
    }
}