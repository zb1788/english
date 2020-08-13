var mp3time = 0,circletime=0;;
var playtype = 4; //4人机对话 系统先，5用户先
var playflag = 0; //0系统先开始完成，1用户先开始完成，2全部完成
var obj1 = "";
var obj2 = "";
var obj3 = "";
var scoreobj = "";
var pingjunscorce = '0';
var uxinmp3 = function () {
    //download下载的列表
    this.downlist = 0;
    //下载MP3列表
    this.mp3dowload = function (playlist) {
        this.downlist = playlist;
        //console.log(iGetInnerText(JSON.stringify(this.downlist)));
        try {
            UXinJSInterfaceSpeech.cacheAudioFiles(iGetInnerText(JSON.stringify(this.downlist)));
        } catch (e) {
            setTip("升级到最新版本的优信");
        }
    };
    //音频播放
    this.playAudio = function (mp3name) {
        try {
            console.log(mp3name);
            UXinJSInterfaceSpeech.playAudio(mp3name);
        } catch (e) {
            setTip("播放失败");
        }
    };
    //音频暂停
    this.pauseAudio = function () {
        UXinJSInterfaceSpeech.pauseAudio();
    }
    //音频暂停后恢复播放
    this.resumeAudio = function () {
        UXinJSInterfaceSpeech.resumeAudio();
    }
    //音频播放停止

    this.stopAudio = function () {

        UXinJSInterfaceSpeech.stopAudio();
    };
    //断点播放

    this.playAudioAtTime = function (mp3name, starttime) {

        UXinJSInterfaceSpeech.playAudioAtTime(mp3name, starttime);
    };
}

//定义MP3对象
var play = new uxinmp3();

//录音类
var record = function () {
    //开始录音
    this.recordVoice = function () {
        window.UXinJSInterfaceSpeech.audioRecordWithPause();
    };
    //暂停|停止录音
    this.pauseVoice = function () {
        window.UXinJSInterfaceSpeech.pauseAudioRecord();
    };
    //试听录音
    this.listenVoice = function () {
        window.UXinJSInterfaceSpeech.getRecordName();
    };
    //暂停试听
    this.pauseAudio = function () {
        play.pauseAudio();
    };
    //暂停后恢复
    this.resumeAudio = function () {
        play.resumeAudio();
    };
    //上传录音
    this.uploadVoice = function () {
        window.UXinJSInterfaceSpeech.getRecordName();
    };
    //重置录音
    this.recordReset = function () {
        window.UXinJSInterfaceSpeech.recordReset();
    };
};

var record = new record();


var onAudioPlayStatus = function (status) {
    if (status == 0) {
        // $("#text" + curplayindex).removeClass("talker2").addClass('talker');
        curplayindex++;
        // $("#text" + curplayindex).show();
        scrolllist(textarr, curplayindex, playlength);

    }

}
/**
 * 录音错误信息回调函数
 * @param  {[type]}
 * status [0：录音成功；-1：SD卡不存在或磁盘空间不足；-2：请求录音权限失败；
 * -3：录音时间太短；-4：超过最大录音时长；-99：未知错误
 * ]
 * @return {[type]}        [description]
 */

function onRecordStatus(status) {
    var recordIsComplete = true;
    switch (status) {
        case "-1":
            // setTip('SD卡不存在或磁盘空间不足');
            recordIsComplete = false;
            break;
        case "-2":
            // setTip('请求录音权限失败');
            recordIsComplete = false;
            break;
        case "-3":
            // setTip('录音时间太短');
            recordIsComplete = false;
            break;
        case "-4":
            // setTip('超过最大录音时长');
            recordIsComplete = false;
            break;
        case "-99":
            // setTip('未知错误');
            recordIsComplete = false;
            break;
    }
    completeRecord(recordIsComplete);
}

function completeRecord(recordIsComplete) {
    $(obj2).find("img").attr('src', '/public/Subject/kycp/images/duxietubiao_03.png');
    if (recordIsComplete) {
        try {
            record.uploadVoice();
        } catch (error) {
            setTip('上传失败');
        }

    }
}

//优信调用此函数，传递录音路径
function getRecordPath(path) {
    $(obj3).find("img").attr('src', '/public/Subject/kycp/images/play.gif');
    play.stopAudio();
    //play.playAudio(path);
    setTimeout(function () {
        window.UXinJSInterfaceSpeech.uploadAudioRecord(path);
    }, 2000);

}

//获取上传后的地址

//口语优信回调的接口函数
function getVoicePath(path) {
    // alert(path);
   // alert(textarr[curplayindex].name);
    var url = "../Kypc/gettextsmartScore";
    //window.UXinJSInterface.hideProgress();
    //UXinJSInterface.showAlert("正在评分,请稍后....");
    $.ajax({
        type: 'POST',
        timeout: 30000,
        url: url,
        data: {
            content: textarr[curplayindex].encontent,
            ks_code: ks_code,
            filename: path,
            contentid: textarr[curplayindex].id,
            chapterid: textarr[curplayindex].chapterid,
            type: 74,
            chapter:chaptertitle,
            ran: Math.random()
        },
        async: true,
        dataType: 'json',
        success: function (data) {
            var score = (data.score);
            //$(scoreobj).addClass('fudongfenshu').html("<p>"+score+"</p>");
           // setTip(score * 1);
            totalscore = parseInt(score)+parseInt(totalscore);
           // $("#text" + curplayindex).removeClass("talker2").addClass('talker');
            curplayindex++;
            $("#text" + curplayindex).show();
            setTimeout(function () {
                scrolllist(textarr, curplayindex, playlength);
            }, 500);

        },
        error: function (xhr, type, errorThrown) {
            // UXinJSInterface.hideProgress();
            //异常处理；
            //mask.close();
            setTip("评分超时，请稍等一会儿再提交");
        }
    });
    
}

/**
 * 录音相关END
 * -------------------------------------------
 */

//网页调用优信的playAudioAtTime这个方法的时候，优信会回调网页的这个js（AudioHavePlayAtTime）
function AudioHavePlayAtTime(TotalTime) {
    //mp3time = TotalTime;
}

function changePlayIcon(obj, imgname) {

}

function starttalk(textarr, curplayindex, playlength) {
    if (curplayindex < playlength) {
        mp3time = parseInt(textarr[curplayindex].mp3time)+1;
        circletime = parseInt(textarr[curplayindex].mp3time)+1;
        if ((curplayindex % 2 == 0)) {
           // $("#touxiang").attr("src", "/public/Subject/kycp/images/tuxiang_03.png");
            //$("#playicon").attr("src", "/public/Subject/kycp/images/play.gif");
            $(".playicon").find('.mycircle').css('animation','lineMove '+circletime+'s ease-in-out 0s infinite');
            $(".playicon").show();
            $(".luyinicon").hide();
            play.playAudio(textarr[curplayindex].url);

        } else {
            record.recordReset();
            //$("#playicon").attr("src", "/public/Subject/kycp/images/record2.gif");
            $(".luyinicon").find('.mycircle').css('animation','lineMove '+circletime+'s ease-in-out 0s infinite');
            $(".playicon").hide();
            $(".luyinicon").show();
            record.recordVoice();
            setTimeout(function () {
                record.pauseVoice();
            }, mp3time*1000);
            //curplayindex++;
        }

    }
    else{
        //alert("over");
        window.location.href="change?ks_code="+ks_code+"&curindex="+curindex+"&totalscore="+totalscore+"&chapterid="+chapterid+"&"+parm;
    }
}
function starttalk2(textarr, curplayindex, playlength) {
    if (curplayindex < playlength) {
        mp3time = parseInt(textarr[curplayindex].mp3time)+1;
        circletime = parseInt(textarr[curplayindex].mp3time)+1;
        if ((curplayindex % 2 == 0)) {
            record.recordReset();
            $(".luyinicon").find('.mycircle').css('animation','lineMove '+circletime+'s ease-in-out 0s infinite');
            $(".playicon").hide();
            $(".luyinicon").show();
            record.recordVoice();
            setTimeout(function () {
                record.pauseVoice();
            }, mp3time*1000);
           // curplayindex++;

        } else {
            //$("#touxiang").attr("src", "/public/Subject/kycp/images/tuxiang_03.png");
            $(".playicon").find('.mycircle').css('animation','lineMove '+circletime+'s ease-in-out 0s infinite');
            $(".playicon").show();
            $(".luyinicon").hide();
            play.playAudio(textarr[curplayindex].url);
        }

    }
    else{
        //alert("over");
        //window.location.href="change?ks_code="+ks_code+"&curindex="+curindex;
         pingjunscorce = parseInt(totalscore/playlength);
         window.location.href="record_detail?ks_code="+ks_code+"&curindex="+curindex+"&pingjunscorce="+pingjunscorce+"&chapterid="+chapterid+"&"+parm;
        
    }
}


function scrolllist(textarr, curplayindex, playlength){
    console.log("curplayindex="+curplayindex);
    var $ul = $("#con ul");
    var liFirstHeight = $ul.find("li:first").height(); //第一个li的高度
    if(curplayindex == 0){liFirstHeight = 0;}
    $ul.animate({
        bottom: liFirstHeight + 0 + "px"
    }, 1500, function() {
        //动画完成时
        //将ul的最后一个剪切li插入为ul的第一个li
        if(curplayindex != 0){
            $ul.find("li:first").appendTo($ul);
            liFirstHeight = $ul.find("li:first").height(); //刚插入的li的高度
            $ul.css({
                bottom: 0 + "px"
            }); 
        }
        if (playtype == 4) {
                starttalk(textarr, curplayindex, playlength);
        } else{
                starttalk2(textarr, curplayindex, playlength);
        }
       //利用css的top属性将刚插入的li隐藏在列表上方  因li的上下padding:10px所以要-20  

    });
                
}