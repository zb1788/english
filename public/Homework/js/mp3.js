var mp = '';
var mp3_progress='';
var mp3_progress_reap="";
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

