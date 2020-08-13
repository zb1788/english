function getHttpUrl(url) {
    // url = url.replace(/https/g, "http");
    // url = url.replace(/:8443/g, ":8080");
    return url;
    // body...
}
var userAgent = navigator.userAgent;
var indexA = userAgent.indexOf("Android")
if (indexA >= 0) {
    isAndroid = true;
    androidVersion = parseFloat(userAgent.slice(indexA + 8));
} else {
    isAndroid = false;
}
var showloading = function () {
    document.getElementById("over").style.display = "block";
    document.getElementById("layout").style.display = "block";
}
var showloading2 = function () {
    document.getElementById("over").style.display = "block";
    document.getElementById("layout2").style.display = "block";
}
var hideloading = function () {
    document.getElementById("over").style.display = "none";
    document.getElementById("layout").style.display = "none";
}
var hideloading2 = function () {
    document.getElementById("over").style.display = "none";
    document.getElementById("layout2").style.display = "none";
}

function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

function iGetInnerText(testStr) {
    var resultStr = testStr.replace(/\ +/g, ""); //去掉空格
    resultStr = testStr.replace(/[ ]/g, ""); //去掉空格
    resultStr = testStr.replace(/[\r\n]/g, ""); //去掉回车换行
    return resultStr;
}

function setTip(content) {
    $("#tips").html(content).show();
    setTimeout(function () {
        $("#tips").hide();
    }, 5000);
}
//初始化单词学习的列表
function initReadTextList(url, listobj) {
    showloading();
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            ks_code: ks_code
        },
        dataType: 'json',
        async: false,
        context: $('body'),
        success: function (data) {
            ReadTextList = data;
            hideloading();
            var htmlstr = '';
            var index = 0;
            var txpic = "";
            $.each(data.data, function (k, v) {
                
                if (v.isevaluate == 1) {
                    if(cindex == k){
                        cindex = index;
                    }
                    htmlstr += '<div class="swiper-slide chapter" chapter="' + v.chapter + '"  chapterid = "' + v.id + '">';
                    htmlstr += '<ul class="mui-table-view listbg">';
                    $.each(v.texts, function (tk, tv) {
                          if(tk%2 == 0){
                            txpic = '<img src="/public/Subject/kycp/images/tuxiang_03.png">';
                          }
                          else{
                            txpic = '<img src="/public/Subject/kycp/images/tuxiang_05.png">';
                          }

                        var enbeforecon = '';
                        var cnbeforecon = '';
                        if (tv.enbefore == "") {
                            enbeforecon = tv.encontent;
                            cnbeforecon = tv.cncontent;
                        } else {
                            enbeforecon = tv.enbefore + ":" + tv.encontent;
                            cnbeforecon = tv.enbefore + ":" + tv.cncontent;
                        }
                        htmlstr += '<li class="psort listbg">';
                        htmlstr += '<div class="relwf">'+txpic+'</div>';
                        htmlstr += '<div  id="score' + tv.id + '" ></div>';
                        htmlstr += '<li  class="mui-table-view-cell mui-collapse dianjijianju">';
                        htmlstr += '<a  class="mui-navigate-right corbg entext" href="javascript:;" id="entext' + tv.id + '">' + enbeforecon + '</a>';
                        htmlstr += '<a  class="mui-navigate-right corbg cntext" style="display:none" href="javascript:;">' + cnbeforecon + '</a>';
                        htmlstr += '<div style="" class="mui-collapse-content  mui-active">';
                        // htmlstr += '<ul class="owwje">';
                        //htmlstr += '<li class="mui-col3 play" mp3="' + tv.name + '" readid="'+tv.id+'" content="'+tv.encontent+'" datatype="1" chapterid="'+tv.chapterid+'"><img src="/public/Subject/kycp/images/duxietubiao_09.png"><br>原音</li>';
                        // htmlstr += '<li class="mui-col4 record"><img src="/public/Subject/kycp/images/duxietubiao_03.png"><br>跟读</li>';
                        // htmlstr += '<li class="mui-col5"><img src="/public/Subject/kycp/images/duxietubiao_06.png"><br>播放</li>';
                        // htmlstr += '</ul>';
                        htmlstr += '<div class="mb-hd">';
                        htmlstr += '<ul>';
                        htmlstr += '<li class="playmp3" mp3="' + tv.url + '" readid="' + tv.id + '" content="' + tv.encontent + '" datatype="1" chapterid="' + tv.chapterid + '" mp3time="'+tv.mp3time+'">';
                        // htmlstr += '<div class="next"><span class="iconfont icon-play on"></span><span class="iconfont icon-pause"></div>';
                        // htmlstr += '<div class="wz">原音</div>';
                        htmlstr += '<div class="next">';
                        htmlstr += '<div class="yyquan"><svg viewbox="0 0 440 440" style="display:;">';
                        htmlstr += '<circle cx="220" cy="220" r="150" stroke="#27bcad" stroke-width="10" fill="none"></circle>';
                        htmlstr += '<circle class="mycircle greenC" cx="220" cy="220" r="150" fill="none" style="display:none" />';
                        htmlstr += ' </svg></div>';
                        htmlstr += '<span class="iconfont iconfont icon-play-circle-fill on"></span><span class="iconfont icon-poweroff-circle-fill  ">';
                        htmlstr += ' </div>';
                        htmlstr += ' <div class="wz">原音</div>';
                        htmlstr += '</li>';
                        htmlstr += '<li>';
                        // htmlstr += '<div class="play record"><span class="iconfont icon-luyin on "></span><span class="iconfont icon-pause"></span></div>';
                        // htmlstr += '<div class="wz">跟读</div>';
                        htmlstr += '<div class="play">';
                        htmlstr += '<div class="yyquan zhong"><svg viewbox="0 0 440 440">';
                        htmlstr += '<circle cx="220" cy="220" r="170" stroke="#eb5a5c" stroke-width="10" fill="none"></circle>';
                        htmlstr += '<circle class="mycircle redC" cx="220" cy="220" r="170" fill="none" style="display:none" />';
                        htmlstr += '</svg></div>';
                        htmlstr += '<span class="iconfont icon-luyin on"></span> <span class="iconfont icon-luyin3 "></span>';
                        htmlstr += '</div>';
                        htmlstr += '<div class="wz">跟读</div>';
                        htmlstr += '</li>';
                        htmlstr += '<li class="recordplay" mp3time="'+tv.mp3time+'">';
                        //htmlstr += '<div class="prev"><span class="iconfont icon-play"></span></div>';
                        //htmlstr += '<div class="wz">播放</div>';
                        htmlstr += '<div class="prev">';
                        htmlstr += '<div class="yyquan"><svg viewbox="0 0 440 440">';
                        htmlstr += '<circle cx="220" cy="220" r="150" stroke="#fe934e" stroke-width="10" fill="none"></circle>';
                        htmlstr += '<circle class="mycircle" cx="220" cy="220" r="150" fill="none" style="display:none" />';

                        htmlstr += ' </svg></div>';
                        htmlstr += '<span class="iconfont icon-play-circle-fill on"></span><span class="iconfont icon-poweroff-circle-fill  ">';
                        htmlstr += '</div>';
                        htmlstr += '<div class="wz">播放</div>';
                        htmlstr += '</li>';
                        
                        htmlstr += '</ul>';
                        htmlstr += '</div>';
                        htmlstr += '</div>';
                        htmlstr += '</li>';
                        htmlstr += '</li>';
                    });
                    htmlstr += '</ul>';
                    htmlstr += '</div>';
                    index ++ ;
                }
            });
            listobj.html(htmlstr);
            var mp3cachelist = [];
            // $.each(data.mp3list, function (k, v) {
            //     var c = {};
            //     c.name = v.name;
            //     c.size = v.size;
            //     c.format = v.format;
            //     c.url = getHttpUrl(v.url);
            //     mp3cachelist.push(c);
            // });
            //console.log(JSON.stringify(mp3cachelist));
            // setTimeout(function () {
            //     try {
            //         play.mp3dowload(mp3cachelist);

            //     } catch (e) {
            //         setTip("音频下载失败,请升级至最新版本.");
            //     }

            // }, 500);
            if(data.data[0].issection == "1"){
                $("#tiaozhan").hide();
            }
            else{
                 $("#tiaozhan").show();
            }
        },
        error: function (xhr, type) {
            hideloading();
        }
    });
}
//模式设置
var setclass = "";
var stylestr = "";
var setclass2 = "";
var stylestr2 = "";

function startset() {
    showloading2();
    $('.seting').show();
    if ($('#fanyistyle').hasClass('mui-active')) {
        setclass = "mui-active";
        stylestr = "transition-duration: 0.2s; transform: translate(43px, 0px)";
    } else {
        setclass = "";
        stylestr = "transition-duration: 0.2s; transform: translate(0px, 0px)";
    }
    if ($('#beisongstyle').hasClass('mui-active')) {
        setclass2 = "mui-active";
        stylestr2 = "transition-duration: 0.2s; transform: translate(43px, 0px)";
    } else {
        setclass2 = "";
        stylestr2 = "transition-duration: 0.2s; transform: translate(0px, 0px)";
    }
}

//模式设置
function cancelset() {
    hideloading2();
    $('.seting').hide();
    $('#fanyistyle').removeClass('mui-active').addClass(setclass);
    $('#fanyistylehandle').removeAttr('style').attr('style', stylestr);
    $('#beisongstyle').removeClass('mui-active').addClass(setclass2);
    $('#beisongstylehandle').removeAttr('style').attr('style', stylestr2);

}

//监听设置结果
function setListener() {
    hideloading2();
    $('.seting').hide();
    if ($('#fanyistyle').hasClass('mui-active')) {
        $('.cntext').show();
    } else {
        $('.cntext').hide();
    }
    if ($('#beisongstyle').hasClass('mui-active')) {

        $('.entext').hide();
    } else {
        $('.entext').show();

    }
    //mui('#fanyestyle').switch().toggle();
}

//根据评测结果改变文本中单词相关颜色
function resetquecontent(result){
    var textstr = $(entextobj).html();
    $.each(result.details,function(k,v){
        if(parseInt(v.score) <= 60){
           textstr = textstr.replace(v.char,'<span class="biaohong">'+v.char+'</span>');
            //console.log(textstr+"60|||"+v.char);
        }
        else if(parseInt(v.score) >= 80){
            textstr = textstr.replace(v.char,'<span class="biaolv">'+v.char+'</span>');
            //console.log(textstr+"80|||"+v.char);
        }
    });
    $(entextobj).html(textstr);
   // console.log($(entextobj).html()); 
}


