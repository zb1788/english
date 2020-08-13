var playtype = 1; //1原音播放，2滴一声播放,3录音播放,4人机对话
var obj1 = "";
var obj2 = "";
var obj3 = "";
var scoreobj = "";
var entextobj = "";
var userscore = 0;
var evaluate_result="";
var recordflag = false;
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
    //先声开始录音
    this.startEvaluateOralWith = function () {
        window.UXinJSInterfaceSpeech.startEvaluateOralWith(json);
    };
    //先声结束录音
    this.stopEvaluate = function () {
        window.UXinJSInterfaceSpeech.stopEvaluate();
    };
};

var record = new record();


var onAudioPlayStatus = function (status) {
    if (status == 0) {
        //alert(playtype);
        if (playtype == 1) {
            //alert("11");
            // $(obj1).find("img").attr('src', '/public/Subject/kycp/images/duxietubiao_09.png');
            $(obj1).find('circle.mycircle').hide();
            $(obj1).find('span.iconfont').eq(1).removeClass('on');
            $(obj1).find('span.iconfont').eq(0).addClass('on');
            play.playAudio("http://en.czbanbantong.com/public/home/js/di.mp3");
            playtype = 2;
        }
        if (playtype == 2) {
            //alert(playtpe);
            $(obj1).find('circle.mycircle').hide();
            $(obj1).find('span.iconfont').eq(1).removeClass('on');
            $(obj1).find('span.iconfont').eq(0).addClass('on');
            playtype = 3;
            var json = {
                type: "sentence",
                conten: content
            };
            // try {
            //     if (UXinJSInterfaceSpeech.startEvaluateOralWith !== undefined) {
            //         UXinJSInterfaceSpeech.startEvaluateOralWith(JSON.stringify(json));
            //         EvaluateType = 1;
            //         // alert("22");
            //     }

            // } catch (error) {
            //     // alert("11");
            //     EvaluateType = 0;
            //     record.recordReset();
            //     record.recordVoice();
            // }
            //$(obj2).find("img").attr('src', '/public/Subject/kycp/images/record2.gif');
            EvaluateType = 0;
            setTimeout(function(){
                recordflag = true;
                record.recordReset();
                record.recordVoice();
                $(obj2).find('circle.mycircle').css('animation', 'lineMove ' + circletime + 's ease-in-out 0s infinite');
                $(obj2).find('circle.mycircle').show();
                $(obj2).find('span.iconfont').eq(0).removeClass('on');
                $(obj2).find('span.iconfont').eq(1).addClass('on');
            },1500);
            setTimeout(function(){
                if(recordflag){
                    setTimeout(function () {
                    try {
                        // UXinJSInterfaceSpeech.stopEvaluate();
                        // EvaluateType = 1;
                        // alert(EvaluateType);
                        record.pauseVoice();
                        EvaluateType = 0;
                        $(obj2).find('circle.mycircle').hide();
                        $(obj2).find('span.iconfont').eq(1).removeClass('on');
                        $(obj2).find('span.iconfont').eq(0).addClass('on');
                        } catch (error) {
                            // record.pauseVoice();
                            // EvaluateType = 0;
                        }
                    }, (parseInt(mp3time)+1)*1000);
                }
            },1600)
            
            
        }
        if (playtype == 3) {
            // $(obj3).find("img").attr('src', '/public/Subject/kycp/images/duxietubiao_06.png');
            $(obj3).find('circle.mycircle').hide();
            $(obj3).find('span.iconfont').eq(1).removeClass('on');
            $(obj3).find('span.iconfont').eq(0).addClass('on');
        }
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
    //$(obj2).find("img").attr('src', '/public/Subject/kycp/images/duxietubiao_03.png');
    $(obj2).find('circle.mycircle').hide();
    $(obj2).find('span.iconfont').eq(1).removeClass('on');
    $(obj2).find('span.iconfont').eq(0).addClass('on');
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
    //alert(path)
    //$(obj3).find("img").attr('src', '/public/Subject/kycp/images/play.gif');
    $(obj3).find('circle.mycircle').css('animation', 'lineMove ' + circletime + 's ease-in-out 0s infinite');
    $(obj3).find('circle.mycircle').show();
    $(obj3).find('span.iconfont').eq(0).removeClass('on');
    $(obj3).find('span.iconfont').eq(1).addClass('on');
    play.stopAudio();
    play.playAudio(path);
    setTimeout(function () {
        window.UXinJSInterfaceSpeech.uploadAudioRecord(path);
    }, 2000);

}


//获取sdk测评后的分数
function getVoiceScore(score) {

    userscore = score;
    if (EvaluateType == 1) {
        $(scoreobj).addClass('fudongfenshu').html("<p>" + userscore + "</p>");
    }
    //alert("getVoiceScore="+userscore);
}
//获取上传后的地址

//口语优信回调的接口函数
function getVoicePath(path) {
    // alert(path);
    if (EvaluateType == 1) {
        $(obj3).find('circle.mycircle').css('animation', 'lineMove ' + circletime + 's ease-in-out 0s infinite');
        $(obj3).find('circle.mycircle').show();
        $(obj3).find('span.iconfont').eq(0).removeClass('on');
        $(obj3).find('span.iconfont').eq(1).addClass('on');
        play.stopAudio();
        play.playAudio("http://" + window.location.hostname + "/recordwav/" + path);
       // alert("http://" + window.location.hostname + "/yylmp3/recordwav/" + path);

    }
    $(obj3).attr("recordurl","http://" + window.location.hostname + "/recordwav/" + path);
    var url = "../Kypc/gettextsmartScore";
    //window.UXinJSInterface.hideProgress();
    //UXinJSInterface.showAlert("正在评分,请稍后....");
    // alert("getVoicePath="+userscore);
    $.ajax({
        type: 'POST',
        timeout: 30000,
        url: url,
        data: {
            content: content,
            ks_code: ks_code,
            filename: path,
            contentid: readid,
            chapterid: chapterid,
            type: 74,
            chapter: chaptertitle,
            EvaluateType: EvaluateType,
            score: userscore,
            ran: Math.random()
        },
        async: true,
        dataType: 'json',
        success: function (data) {
            userscore = (data.score);
            evaluate_result = data.evaluate_result;
            //  alert( $(scoreobj).attr('id'));
            resetquecontent(evaluate_result);
            if (EvaluateType == 0) {
                $(scoreobj).addClass('fudongfenshu').html("<p>" + userscore + "</p>");
            }

            //setTip(score * 1);
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
    // mp3time = TotalTime + 2;
}

function changePlayIcon(obj, imgname) {

}