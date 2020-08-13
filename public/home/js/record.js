var recordinfo = new Array();
var record_detail_info = new Array();
function initData(){
     $('nav a:eq(5)').addClass('cur');
     welcome();
     get_record_list();
}
function get_record_list(){
    //console.log('ss');
    $.getJSON(RecordConter + 'record_list',{},function(result){
        var yearname = '';
        var monthname = '';
        var yearhtml = '';
        var curyearclass = '';
        var curyearnum = 0;
        if (result.length == 0) {
                $('#pdFollow').hide();
                $('#maincontent .tips').show();
        }
        else{
            $('#maincontent .tips').hide();
            recordinfo = result;
                yearhtml = '<li class="s">|</li>';
                $(result).each(function(num) {
                    yearname = $(this).attr("addtimey");
                    if (num == (result.length - 1)) {
                        curyearclass = 'on';
                    }
                    else {
                        curyearclass = '';
                    }
                    yearhtml += '<li class="m ' + curyearclass + '" curnum="' + num + '"><h3><a href="javascript:;">' + yearname + '</a></h3><ul class="sub">';
                    $($(this).attr("monthinfo")).each(function(mnum) {
                        monthname = $(this).attr("addtimem");
                        yearhtml += '<li><a href="#m' + monthname + '">' + monthname + '月</a></li>';
                    });

                    yearhtml += '</ul></li>';
                    if (num != (result.length - 1)) {
                        yearhtml += '<li class="s">|</li>';
                    }


                });
                yearhtml += '<li class="s">|</li><li class="block"></li>';//alert(yearhtml);
                $('#yearinfo').html(yearhtml);
                curyear();
              detail_record(0);
        }
    });
}
function detail_record(yearnum) {
    var yearname = '';
    var monthname = '';
    var dayname = '';
    var xxnum = 0, txnum = 0, textnum = 0,listennum = 0,listenztnum = 0;
    var htmlcode = '<div class="space"></div>';
    var dayclass = '';
    var pingyuclass = '';
    var monthinfoarray = new Array();
    monthinfoarray = recordinfo[yearnum];
    yearname = monthinfoarray.addtimey;
    $(monthinfoarray.monthinfo).each(function(num) {
        monthname = $(this).attr('addtimem');
        htmlcode += '<a id="m' + monthname + '" class="cn"></a>';
        htmlcode += '<dl><dt>' + monthname + '月</dt>'
        $($(this).attr('dayinfo')).each(function(daynum) {
            dayname = $(this).attr('addtimed');
            xxnum = $(this).attr('xxnum');
            xxnum = Number(xxnum);
            txnum = $(this).attr('txnum');
            txnum = Number(txnum);
            textnum = $(this).attr('textnum');
            textnum = Number(textnum);
            listennum = $(this).attr('listennum');
            listennum = Number(listennum);
            listenztnum = $(this).attr('listenztnum');
            listenztnum = Number(listenztnum);
            if ((daynum % 2) == 0) {
                dayclass = 'class="left"';
            }
            else {
                dayclass = 'class="right"';
            }

            if(xxnum <5 && txnum ==0 && textnum ==0 ){
                pingyuclass = 'pingyu6';
            }
            if (xxnum < 10 || textnum == 1 || (txnum > 0 && txnum <= 5)) {
                pingyuclass = 'pingyu5';
            }
            
           if ((xxnum >= 10 && xxnum < 15) || textnum == 2 || (txnum > 5 && txnum <= 8)) {
                pingyuclass = 'pingyu4';
            }
           if ((xxnum >= 15 && xxnum < 20) || textnum == 3 || (txnum > 8 && txnum <= 10)) {
                pingyuclass = 'pingyu3';
            }
            if (xxnum == 20 || textnum == 4 || (txnum > 10 && txnum <= 15)) {
                pingyuclass = 'pingyu2';
            }
            if (xxnum > 20 || textnum > 4 || txnum > 15) {
                pingyuclass = 'pingyu1';
            }
            htmlcode += '<dd ' + dayclass + '>';
            htmlcode += '<div class="date">' + monthname + '月' + dayname + '日</div>';
            htmlcode += '<div class="infoBox ' + pingyuclass + '"><a href="javascript:;" onclick="showdetailinfo(0,\'' + yearname + '\',\'' + monthname + '\',\'' + dayname + '\',\'' + pingyuclass + '\');"><span class="arrow"></span><span class="arrow2"></span><p>今天你一共学习了<strong>' + xxnum + '</strong>个单词，跟读了<strong>' + textnum + '</strong>段课文，听写了<strong>' + txnum + '</strong>个单词,测试了<strong>' + listennum + '</strong>套单元听力训练，模拟了<strong>' + listenztnum + '</strong>套中考听力试题。</p><span class="txt"><i></i></span></a></div>';
            htmlcode += '</dd>';
            htmlcode += '<div class="clearfix"></div>';

        });
        htmlcode += '</dl>';
        htmlcode += '<div class="clearfix"></div>';
    });
    $('#detaildata').html(htmlcode);


}
function curyear() {
    var ind = 0; //初始位置
    var nav = jQuery(".nav");
    //ind = jQuery(".nav .m").length - 1;
    var init = jQuery(".nav .m").eq(ind);
    var curnum = init.attr('curnum');
    // alert(curnum);
    var block = jQuery(".nav .block"); //滑块
    block.css({"left": init.position().left - 2}); //初始化滑块位置
    //nav.hover(function(){},function(){ block.animate({"left":init.position().left-2},100); detail_record(curnum);}); //移出导航滑块返回
    jQuery(".nav").slide({
        type: "menu", //效果类型
        titCell: ".m", // 鼠标触发对象
        targetCell: ".sub", // 效果对象，必须被titCell包含
        delayTime: 300, // 效果时间
        triggerTime: 0, //鼠标延迟触发时间
        returnDefault: true, //on返回初始位置
        defaultIndex: ind, //初始位置
        startFun: function(i, c, s, tit) { //控制当前滑块位置
            block.animate({"left": tit.eq(i).position().left - 2}, 100);
            //alert(tit.eq(i).attr('curnum'));
            detail_record(tit.eq(i).attr('curnum'));
        }
    });
    jQuery("#pdFollow").smartFloat();

}
//返回顶部
function backtotop() {

    // back-to-top
    var $back = $("<div class='back-to-top' id='back-to-top'><a href='javascript:void(0);'>Back to Top</a></div>");
    $back.appendTo(".wrap");

    $(window).scroll(function() {
        if ($(window).scrollTop() > 100) {
            $(".back-to-top").fadeIn(100);
        } else {
            $(".back-to-top").fadeOut(100);
        }
    });
    $(".back-to-top").click(function() {
        $('body,html').animate({scrollTop: 0}, 100);
        $("#sideFix li.nav-item").removeClass("current");
        return false;
    });

    // sideFix Button   
    $("#sideFix li.nav-item").click(function() {
        $("#sideFix li.nav-item").removeClass("current");
        $(this).addClass("current");
    });

    // ui-tab
    $(".ui-tab").each(function() {
        var $tabNav = $(this).find(".ui-tab-nav li");
        var $tabBox = $(this).find(".ui-tab-item");

        $(this).find(".ui-tab-nav li:first").addClass("current");
        ;
        $(this).find(".ui-tab-item:first").addClass("ui-tab-item-current");

        $tabNav.mouseover(function() {
            var index = $(this).index();
            $tabBox.eq(index).addClass("ui-tab-item-current")
                    .siblings().removeClass("ui-tab-item-current");
            $(this).addClass("current")
                    .siblings().removeClass("current");
        });
    });
}
function showdetailinfo(flag, year, month, day, pingyuclass) {
    var curtime = year + '-' + month + '-' + day;
    if (flag == 0) {
        //alert(month);
        $('#pdFollow').hide();
        $('#maincontent').hide();
        $('#maindetail').show();
        $.getJSON(RecordConter + 'study_record_detail', {curtime: curtime}, function(result) {
            if (result.length > 0) {
                record_detail_info = result;
                record_detail_page(1, pingyuclass);
            }
            else {
                $('#pdFollow').show();
                $('#maincontent').show();
                $('#maindetail').hide();
            }
        });
    }
    else {
        $('#pdFollow').show();
        $('#maincontent').show();
        $('#maindetail').hide();
    }
}
function record_detail_page(page, pingyuclass) {
    //alert(record_detail_info.length)
    var fuhao = new Array("①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩");
    var gradeid = 0, termid = 0;
    versionid = 0, unitid = 0, word_text_id = 0, studytype = "";
    var htmlcode = '';
    var tablestr = '';
    var pagestr = '', pagestr2 = '';
    var allpage = 0;
    var flagname = '';
    var fhnum = 0;
    var listnum = 6;
    var def_flag = 0;
    allpage = Math.ceil(record_detail_info.length / Number(listnum));
    //alert(allpage);
    htmlcode = '<div class="title"><a href="javascript:;" onclick="showdetailinfo(1);" class="aBtn cur fr"><i class="fa fa-chevron-left"></i>&nbsp;返回</a><div class="' + pingyuclass + '"><span class="txt"></span></div></div>';
    tablestr = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="rec"><tr class="title"><td>学习项目</td><td>学习记录</td></tr>';
    for (var i = (page - 1) * Number(listnum); i < record_detail_info.length && i < page * Number(listnum); i++) {
        gradeid = record_detail_info[i]['r_grade'];
        termid = record_detail_info[i]['r_volume'];
        versionid = record_detail_info[i]['r_version'];
        unitid = record_detail_info[i]['ks_code'];
        word_text_id = record_detail_info[i]['id'];
        studytype = record_detail_info[i]['study_type'];
        if (record_detail_info[i]['ks_code'] != '0')
        {
            if (record_detail_info[i]['flag'] == 0) {
            flagname = '单词跟读';
            def_flag = 0;
            }
            else if (record_detail_info[i]['flag'] == 1) {
                flagname = '单词记忆';
                def_flag = 2;
            }
            else if (record_detail_info[i]['flag'] == 2) {
                flagname = '单词听写';
                def_flag = 3;
            }
            else if (record_detail_info[i]['flag'] == 3){
                flagname = '课文跟读';
                def_flag = 4;
            }
            else{
                flagname = '听力训练';
            }
        }
        else{
            flagname = '中考专题';
        }
        
        if (i != 0 && record_detail_info[i]['flag'] != record_detail_info[i - 1]['flag']) {
            fhnum = 0;
        }
        if(record_detail_info[i]['flag'] == 4){
            record_detail_info[i]['ucname'] = record_detail_info[i]['ucname']+ '，本套试卷获得了<strong>'+record_detail_info[i]['score']+'</strong>分，做错了<strong>'+record_detail_info[i]['errornum']+'</strong>道小题';
            tablestr += '<tr><td>' + flagname + '<small>' + fuhao[fhnum] + '</small></td><td>' + record_detail_info[i]['c1'] + '：' + flagname + '<strong>' + record_detail_info[i]['allcount'] + '</strong>套，进行至 <a href="/listen">' + record_detail_info[i]['ucname'] + '</a></td></tr>';
        }
        else if (record_detail_info[i]['flag'] == 5)
        {
            record_detail_info[i]['ucname'] = record_detail_info[i]['ucname']+ '，本套试卷获得了<strong>'+record_detail_info[i]['score']+'</strong>分，做错了<strong>'+record_detail_info[i]['errornum']+'</strong>道小题';
            tablestr += '<tr><td>' + flagname + '<small>' + fuhao[fhnum] + '</small></td><td>' + flagname + '<strong>' + record_detail_info[i]['allcount'] + '</strong>套，进行至 <a href="/listen/listentopic">' + record_detail_info[i]['ucname'] + '</a></td></tr>';
        }
        else if (record_detail_info[i]['flag'] == 3){
             tablestr += '<tr><td>' + flagname + '<small>' + fuhao[fhnum] + '</small></td><td>' + record_detail_info[i]['c1'] +  '：' + flagname + '<strong>' + record_detail_info[i]['allcount'] + '</strong>个，进行至 <a href="/text">' + record_detail_info[i]['ucname'] + '</a></td></tr>';
        }
        else{
             tablestr += '<tr><td>' + flagname + '<small>' + fuhao[fhnum] + '</small></td><td>' + record_detail_info[i]['c1'] +  '：' + flagname + '<strong>' + record_detail_info[i]['allcount'] + '</strong>个，进行至 <a href="/word">' + record_detail_info[i]['ucname'] + '</a></td></tr>';
        }
        fhnum++;

    }
    tablestr += '</table>';
    pagestr = '<div class="pageBg"><div id="pages" class="page">共<strong>' + record_detail_info.length + '</strong>条 第<strong>' + page + '</strong>页/共<strong>' + allpage + '</strong>页  ';
    if (allpage == 1 || page == 1) {
        pagestr += '<a class="a1">上一页</a>  ';
    }
    else {
        pagestr += '<a href="javascript:;" onclick="record_detail_page(' + (Number(page) - 1) + ',\'' + pingyuclass + '\')" class="a1">上一页</a>  ';
    }
    //pagestr += pagestr2;
    for (var i = 1; i <= allpage; i++) {
        if (i == page) {
            pagestr += '<span>' + i + '</span>  ';
        }
        else {
            pagestr += '<a href="javascript:;" onclick="record_detail_page(' + i + ',\'' + pingyuclass + '\')">' + i + '</a>  ';
        }
    }
    if (allpage > 1 && page != allpage) {
        pagestr += '<a href="javascript:;" onclick="record_detail_page(' + (Number(page) + 1) + ',\'' + pingyuclass + '\')" class="a1">下一页</a>';
    }
    else {
        pagestr += '<a class="a1">下一页</a>';
    }
    pagestr += '</div></div>';
    $('.recordInfo').html(htmlcode + tablestr + pagestr);
}
//fixed
jQuery.fn.smartFloat = function() {
    var position = function(element) {
        var top = element.position().top, pos = element.css("position");
        jQuery(window).scroll(function() {
            var scrolls = jQuery(this).scrollTop();
            if (scrolls > top) {
                if (window.XMLHttpRequest) {
                    element.css({
                        position: "fixed",
                        top: 0
                    });
                } else {
                    element.css({
                        top: scrolls
                    });
                }
            } else {
                element.css({
                    position: pos,
                    top: top
                });
            }
        });
    };
    return jQuery(this).each(function() {
        position(jQuery(this));
    });
};