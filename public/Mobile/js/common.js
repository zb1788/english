//获取获取地址栏中的参数
function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

var mp = '';
var mp3_progress='';
var mp3_progress_reap="";
var start=0;
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
    oplay.stemindex = 0;
    oplay.queinitindex = 0;
    oplay.questionindex = 0;
    oplay.childstemindex = 0;
    oplay.playtimes=0;
      oplay.childinitstemindex = 0;
      oplay.url = "";
      oplay.repeat = 1; //默认播放次数
      oplay.curpeat = 1;//当前播放到第几次
      oplay.url = "";
  
    oplay.play = function(mp3) {
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    }
    oplay.clear = function() {

        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        //$("#jplayer").data("SpeakMP3Value", "0");
//      $("#jplayer").unbind($.jPlayer.event.ended);
//      $("#jplayer").unbind($.jPlayer.event.progress);
    };
    return oplay;
}

var Requests = new Object();
Requests = GetRequest();
$(function(){
    $("#jplayer").jPlayer({
        swfPath: '/public/Mobile/js',
        wmode: "window",
        supplied: "mp3",
        preload: "none",
        volume: "1"
    });
    mp = new myplay();
    mp.clear();
});
