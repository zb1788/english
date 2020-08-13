var userpath = "";
var playStatus = false;
var recordtime = 2000;
var pichost = "http://192.168.144.82";
//audiotype 0表示是单词 1表示是课文列表 2表示是课文  3表示是绘本
function onAudioPlayStatus(status) {
    if (audiotype == 3) {
        audiostopbook(status);
    } else if (audiotype == 1) {
        audiostopchapterlist(status);
    } else if (audiotype == 2) {
        audiostoptext(status);
    } else if (audiotype == 4) {
        audiostoprecord(status);
    } else if (audiotype == 5) {
        audiostopword(status);
    } else if (audiotype == 6) {
        audiostopbookcontent(status);
    } else if (audiotype == 7) {
        overaudioindex = overaudioindex + 1;
        if (overaudioindex < audiojson.length) {
            window.UXinJSInterfaceSpeech.playAudio(audiojson[overaudioindex]);
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
    // var recorddom = document.getElementsByClassName("swiper-slide-active")[0].getElementsByClassName("record-icon");
    // recorddom[1].src = '/public/Subject/kycp/images/duxietubiao_03.png';
    if (recordIsComplete) {
        try {
            records.uploadVoice();
        } catch (error) {
            setTip('上传失败');
        }

    }
}

//优信调用此函数，传递录音路径
function getRecordPath(path) {
    recordpath = path;
    // var recorddom = document.getElementsByClassName("swiper-slide-active")[0].getElementsByClassName("record-icon");
    // recorddom[2].src = '/public/Subject/kycp/images/play.gif';
    UXinJSInterfaceSpeech.stopAudio();
    var enuser_recore_mp3 = window.localStorage.getItem("enuser_recore_mp3");
    if (enuser_recore_mp3) {
        var userrecords = JSON.parse(enuser_recore_mp3);
        userrecords.push(path);
        window.localStorage.setItem("enuser_recore_mp3", JSON.stringify(userrecords));
    } else {
        var userrecords = [];
        userrecords.push(path);
        window.localStorage.setItem("enuser_recore_mp3", JSON.stringify(userrecords));
    }

    userrecords.push(path);

    timer = setTimeout(function () {
        window.UXinJSInterfaceSpeech.uploadAudioRecord(path);
    }, 2000);
}

//获取上传后的地址

//口语优信回调的接口函数
function getVoicePath(path) {
    var url = "../Kypc/gettextsmartScore";
    playtype = 3;
    UXinJSInterfaceSpeech.stopAudio();
    //window.UXinJSInterface.hideProgress();

    //UXinJSInterface.showAlert("正在评分,请稍后....");
    $.ajax({
        type: 'POST',
        timeout: 1000,
        url: url,
        data: {
            content: content,
            ks_code: bookid,
            filename: path,
            contentid: contentid,
            chapterid: pageid,
            chapter: chapter,
            type: "6",
            ran: Math.random()
        },
        async: true,
        dataType: 'json',
        success: function (data) {
            playtype = 3;
            audiojson.push(recordpath);
            //播放修改样式
            try {
                removeClass(recorddom.getElementsByClassName("prev")[0].getElementsByClassName("iconfont")[1], "on");
                addClass(recorddom.getElementsByClassName("prev")[0].getElementsByClassName("iconfont")[0], "on");
                recorddom.getElementsByClassName("circle")[0].setAttribute("class", "circle mycircle");
            } catch (e) {

            }
            window.UXinJSInterfaceSpeech.playAudio(recordpath);
            var score = (data.score == undefined || data.score == false) ? 0 : data.score;
            averagescore = averagescore + score;
            audiocount = audiocount + 1;
            document.getElementsByClassName("swiper-slide-active")[0].getElementsByClassName("score")[0].innerHTML = score;
        },
        error: function (xhr, type, errorThrown) {
            //console.log(xhr);
            //console.log(type);
            //console.log(errorThrown);
            //异常处理；
            playtype = 3;
            window.UXinJSInterfaceSpeech.playAudio(recordpath);
        }
    });

}

/**
 * 录音相关END
 * -------------------------------------------
 */

//网页调用优信的playAudioAtTime这个方法的时候，优信会回调网页的这个js（AudioHavePlayAtTime）
function AudioHavePlayAtTime(TotalTime) {
    mp3time = TotalTime;
}

function changePlayIcon(obj, imgname) {

}

function audiostopbookcontent(status) {
    if (status == 0) {
        //修改暂停按钮样式
        var mp3list = list[position[0]]["contents"];
        if (list.length == (curpage + 1)) {
            timer = setTimeout(function () {
                position[1] = position[1] + 1;
                mySwiper.destroy(false);
            }, 2000)
        } else {
            if (mp3list.length <= (position[1] + 1)) {
                try {
                    document.getElementsByClassName("playicons")[curpage].style.display = "block";
                    document.getElementsByClassName("stopicons")[curpage].style.display = "none";
                } catch (e) {

                }
                //显示翻译信息等待两秒之后在进行翻页
                document.getElementsByClassName("swiper-slide")[curpage].getElementsByClassName("bookcontent")[0].getElementsByClassName("am-list")[0].getElementsByClassName("am-list-body")[0].getElementsByClassName("am-list-item")[position[1]].getElementsByClassName("am-list-content")[0].getElementsByClassName("am-list-brief")[0].style.display = "";
                //之后两秒进行页面的跳转
                timer = setTimeout(function () {
                    position[1] = position[1] + 1;
                    mySwiper.slideNext();
                }, 1000)
            } else {
                timer = setTimeout(function () {
                    try {
                        //修改样式
                        //document.getElementsByClassName("swiper-slide")[curpage].getElementsByClassName("am-list-item")[position[1]].getElementsByClassName("am-list-content")[0].style.color="red";
                        var mp3name = mp3list[position[1]].mp3;
                        window.UXinJSInterfaceSpeech.playAudio(mp3name);
                    } catch (e) {

                    }
                }, 2000)
            }
        }
    } else {

    }
}

function audiostopbook(status) {
    try {
        clearTimeout(timer);
    } catch (e) {}
    if (status == 0) {
        playtime = playtime + (new Date().getTime() - starttime);
        var parent = document.getElementsByClassName("banner")[0].getElementsByClassName("swiper-slide")[position[0]];
        if (parent.getElementsByClassName("active") != undefined && parent.getElementsByClassName("active").length > 0) {
            parent.getElementsByClassName("active")[0].style.color = "";
            parent.getElementsByClassName("active")[0].getElementsByClassName("am-list-brief")[0].style.color = "";
            parent.getElementsByClassName("active")[0].classList.remove('active')
        }
        var mp3list = list[position[0]]["texts"];
        //先判断行
        if (list.length <= position[0]) {
            position[0] = 0;
            position[1] = 0;
            mySwiper.slideTo(0);
            var positions = position;
            startplayer(list, loop, time, positions);
        } else {
            if (position[1] < (mp3list.length)) {
                if (playtime < time || time == 0) {
                    timer = setTimeout(function () {
                        playtime = playtime + intervaltime;
                        var positions = position;
                        startplayer(list, loop, time, positions);
                    }, intervaltime)
                }
            } else {
                if (loop == 1) {
                    position[0] = position[0] + 1;
                    position[1] = 0;
                    mySwiper.slideTo(position[0]);
                    var positions = position;
                    startplayer(list, loop, time, positions);
                } else {
                    position[1] = 0;
                    var positions = position;
                    startplayer(list, loop, time, positions);
                }
            }
        }
    } else {

    }
}





function audiostoprecord(status) {
    //  alert("type=="+type+";;;playtype=="+playtype);
    if (status == 0) {
        if (type == 1) {
            var recorddom = document.getElementsByClassName("swiper-slide-active")[0];
            if (playtype == 1) {
                removeClass(recorddom.getElementsByClassName("next")[0].getElementsByClassName("iconfont")[1], "on");
                addClass(recorddom.getElementsByClassName("next")[0].getElementsByClassName("iconfont")[0], "on");
                recorddom.getElementsByClassName("next")[0].getElementsByClassName("circle")[0].setAttribute("class", "circle greenC");
                recorddom.getElementsByClassName("next")[0].getElementsByClassName("circle")[0].style.display = "none";


                records.recordVoice();
                removeClass(recorddom.getElementsByClassName("play")[0].getElementsByClassName("iconfont")[0], "on");
                addClass(recorddom.getElementsByClassName("play")[0].getElementsByClassName("iconfont")[1], "on");
                recorddom.getElementsByClassName("play")[0].getElementsByClassName("circle")[0].setAttribute("class", "mycircle circle redC");
                timer = setTimeout(function () {
                    removeClass(recorddom.getElementsByClassName("play")[0].getElementsByClassName("iconfont")[1], "on");
                    addClass(recorddom.getElementsByClassName("play")[0].getElementsByClassName("iconfont")[0], "on");
                    recorddom.getElementsByClassName("play")[0].getElementsByClassName("circle")[0].setAttribute("class", "circle redC");
                    recorddom.getElementsByClassName("play")[0].getElementsByClassName("circle")[0].style.display = "none";
                    records.pauseVoice();
                }, mp3time * 1000 + recordtime);
            } else if (playtype == 3) {
                removeClass(recorddom.getElementsByClassName("prev")[0].getElementsByClassName("iconfont")[0], "on");
                addClass(recorddom.getElementsByClassName("prev")[0].getElementsByClassName("iconfont")[1], "on");
                recorddom.getElementsByClassName("prev")[0].getElementsByClassName("circle")[0].setAttribute("class", "circle");
                recorddom.getElementsByClassName("prev")[0].getElementsByClassName("circle")[0].style.display = "none";
                playtype = 1;
                mySwiper.slideNext();
            }
        } else if (type == 0) {
            document.getElementsByClassName("swiper-slide-active")[0].getElementsByClassName("am-list-item")[curindex].getElementsByClassName("am-list-content")[0].style.color = "black";
            curindex = curindex + 1;
            if (curindex < playlist.length) {
                timer = setTimeout(function () {
                    var mp3name = mp3list[curindex].mp3;
                    document.getElementsByClassName("swiper-slide-active")[0].getElementsByClassName("am-list-item")[curindex].getElementsByClassName("am-list-content")[0].style.color = "red";
                    UXinJSInterfaceSpeech.playAudio(mp3name);
                }, 2000);
                if (isloop) {
                    mySwiper.slideNext();
                }
            } else {
                if (isloop) {
                    mySwiper.slideNext();
                }
            }

        }
    }
}


function audiostopchapterlist(status) {
    try {
        clearTimeout(timer);
    } catch (e) {}

    if (status == 0) {
        playtime = playtime + (new Date().getTime() - starttime) / 1000;
        var listkeys = Object.keys(list);
        var childrenkeys = Object.keys(list[listkeys[position[0]]]["children"]);
        var mp3list = list[listkeys[position[0]]]["children"][childrenkeys[position[1]]]["children"];
        var mp3listkeys = Object.keys(mp3list);
        var request = getJson();
        var data = {};
        data.ks_code = listkeys[position[0]];
        data.ks_name = list[listkeys[position[0]]].name;
        data.chapterid = childrenkeys[position[1]];
        data.textid = mp3listkeys[mp3index - 1];
        data.gradeid = request["gradeid"];
        data.termid = request["termid"];
        data.versionid = request["versionid"];
        try {
            updateUserStatus(JSON.stringify(data));
        } catch (e) {

        }
        if (mp3index < mp3listkeys.length) {
            if (playtime < time || time == 0) {
                timer = setTimeout(function () {
                    playtime = playtime + intervaltime / 1000;
                    vcomEnAudioPlay();
                }, intervaltime)
            } else {
                //removeAudioClass(position);
                try {
                    clearTimeout(timer);
                } catch (e) {}
                try {
                    //console.log("next")
                    document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                } catch (e) {
                    //console.log(e)
                    //console.log("样式问题")
                }
                //UXinJSInterfaceSpeech.stopAudio();
            }
        } else {
            //判断循环情况
            if (loop == 0) { //本节循环
                mp3index = 0;
                //removeAudioClass(position);
                //addAudioIcon(position,"half-audio-active","audio-active");
                if (playtime < time || time == 0) {
                    timer = setTimeout(function () {
                        playtime = playtime + intervaltime / 1000;
                        startplayer(list, loop, time, position);
                    }, intervaltime);
                } else {
                    //removeAudioClass(position);
                    try {
                        clearTimeout(timer);
                    } catch (e) {}
                    try {
                        //console.log("next")
                        document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                    } catch (e) {
                        //console.log(e)
                        //console.log("样式问题")
                    }
                    //UXinJSInterfaceSpeech.stopAudio();
                }
            } else if (loop == 1) {
                mp3index = 0;
                //removeAudioClass(position);
                //addAudioIcon(position,"half-audio-active","audio-active");
                position[1] = position[1] + 1;
                if (childrenkeys.length > position[1]) {
                    if (playtime < time || time == 0) {
                        timer = setTimeout(function () {
                            playtime = playtime + intervaltime / 1000;
                            startplayer(list, loop, time, position);
                        }, intervaltime)
                    } else {
                        //removeAudioClass(position);
                        try {
                            clearTimeout(timer);
                        } catch (e) {}
                        try {
                            //console.log("next")
                            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                        } catch (e) {
                            //console.log(e)
                            //console.log("样式问题")
                        }
                        //UXinJSInterfaceSpeech.stopAudio();
                    }
                } else {
                    position[1] = 0;
                    if (playtime < time || time == 0) {
                        timer = setTimeout(function () {
                            playtime = playtime + intervaltime / 1000;
                            startplayer(list, loop, time, position);
                        }, intervaltime)
                    } else {
                        //removeAudioClass(position);
                        try {
                            clearTimeout(timer);
                        } catch (e) {}
                        try {
                            //console.log("next")
                            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                        } catch (e) {
                            //console.log(e)
                            //console.log("样式问题")
                        }
                        //UXinJSInterfaceSpeech.stopAudio();
                    }
                }
            } else if (loop == 2) {
                mp3index = 0;
                //removeAudioClass(position);
                //addAudioIcon(position,"half-audio-active","audio-active");
                position[1] = position[1] + 1;
                if (childrenkeys.length > position[1]) {
                    if (playtime < time || time == 0) {
                        timer = setTimeout(function () {
                            playtime = playtime + intervaltime / 1000;
                            startplayer(list, loop, time, position);
                        }, intervaltime)
                    } else {
                        //removeAudioClass(position);
                        try {
                            clearTimeout(timer);
                        } catch (e) {}
                        try {
                            //console.log("next")
                            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                        } catch (e) {
                            //console.log(e)
                            //console.log("样式问题")
                        }
                        //UXinJSInterfaceSpeech.stopAudio();
                    }
                } else {
                    position[0] = position[0] + 1;
                    position[1] = 0;
                    if (listkeys.length > position[0]) {
                        if (playtime < time || time == 0) {
                            timer = setTimeout(function () {
                                playtime = playtime + intervaltime / 1000;
                                startplayer(list, loop, time, position);
                            }, intervaltime)
                        } else {
                            //removeAudioClass(position);
                            try {
                                clearTimeout(timer);
                            } catch (e) {}
                            try {
                                //console.log("next")
                                document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                            } catch (e) {
                                //console.log(e)
                                //console.log("样式问题")
                            }
                            //UXinJSInterfaceSpeech.stopAudio();
                        }
                    }
                }
            }

        }
    } else {

    }
}



function audiostoptext(status) {
    //console.log("playtime=" + playtime);
    //console.log("time=" + time);
    try {
        clearTimeout(timer);
    } catch (e) {}
    if (status == 0) {
        playtime = playtime + (new Date().getTime() - starttime) / 1000;
        var parent = document.getElementsByClassName("banner")[0].getElementsByClassName("swiper-slide")[position[0]];
        if (parent.getElementsByClassName("active") != undefined && parent.getElementsByClassName("active").length > 0) {
            parent.getElementsByClassName("active")[0].style.color = "";
            try {
                parent.getElementsByClassName("active")[0].getElementsByClassName("am-list-brief")[0].style.color = "";
            } catch (e) {

            }
            //parent.getElementsByClassName("active")[0].getElementsByClassName("am-list-brief")[0].style.color = "";
            parent.getElementsByClassName("active")[0].classList.remove('active')
        }
        var mp3list = list[position[0]]["texts"];
        var request = getJson();
        var data = {};
        data.ks_code = request["ks_code"];
        data.ks_name = request["ks_name"];
        data.chapterid = mp3list[position[1]].chapterid;
        data.textid = mp3list[position[1]].id;
        data.gradeid = request["gradeid"];
        data.termid = request["termid"];
        data.versionid = request["versionid"];
        try {
            updateUserStatus(JSON.stringify(data));
        } catch (e) {

        }
        //console.log("textstop")
        //console.log(list)
        //console.log(position)
        //先判断行
        if (list.length <= position[0]) {
            position[0] = 0;
            position[1] = 0;
            var positions = position;
            if (playtime < time || time == 0) {
                timer = setTimeout(function () {
                    playtime = playtime + intervaltime / 1000;
                    //startplayer(list,loop,time,positions);
                    mySwiper.slideTo(0);
                }, intervaltime);

            } else {
                try {
                    clearTimeout(timer);
                } catch (e) {}
                try {
                    //console.log("next")
                    document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[2].click();
                } catch (e) {
                    //console.log(e)
                    //console.log("样式问题")
                }
                //UXinJSInterfaceSpeech.stopAudio();
            }
        } else {
            position[1] = position[1] + 1;
            if (position[1] >= mp3list.length) {
                //看是单元循环还是本节循环
                if (loop == 0) {
                    //如果是本节循环
                    position[1] = 0;
                    var positions = position;
                    if (playtime < time || time == 0) {
                        timer = setTimeout(function () {
                            playtime = playtime + intervaltime / 1000;

                            startplayer(list, loop, time, positions);
                        }, intervaltime);
                    } else {
                        try {
                            clearTimeout(timer);
                        } catch (e) {}
                        try {
                            //console.log("next")
                            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                        } catch (e) {
                            //console.log(e)
                            //console.log("样式问题")
                        }
                        //UXinJSInterfaceSpeech.stopAudio();
                    }
                } else if (loop == 1) {
                    //如果是单元循环的话
                    if ((position[0] + 1) >= list.length) {
                        position[0] = 0;
                        position[1] = 0;

                        var positions = position;
                        if (playtime < time || time == 0) {

                            timer = setTimeout(function () {
                                playtime = playtime + intervaltime / 1000;
                                // startplayer(list,loop,time,positions);
                                mySwiper.slideTo(0);
                            }, intervaltime);
                        } else {
                            try {
                                clearTimeout(timer);
                            } catch (e) {}
                            try {
                                //console.log("next")
                                document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                            } catch (e) {
                                //console.log(e)
                                //console.log("样式问题")
                            }
                            //UXinJSInterfaceSpeech.stopAudio();
                        }
                    } else {
                        position[0] = position[0] + 1;
                        position[1] = 0;
                        //mySwiper.slideTo(position[0]);
                        var positions = position;
                        if (playtime < time || time == 0) {

                            timer = setTimeout(function () {
                                playtime = playtime + intervaltime / 1000;
                                mySwiper.slideTo(position[0]);
                                //startplayer(list,loop,time,positions);
                            }, intervaltime);
                        } else {
                            try {
                                clearTimeout(timer);
                            } catch (e) {}
                            try {
                                //console.log("next")
                                document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                            } catch (e) {
                                //console.log(e)
                                //console.log("样式问题")
                            }
                            //UXinJSInterfaceSpeech.stopAudio();
                        }
                    }
                }
            } else {
                if (playtime < time || time == 0) {
                    timer = setTimeout(function () {
                        playtime = playtime + intervaltime / 1000;
                        var positions = position;
                        startplayer(list, loop, time, positions);
                    }, intervaltime)
                } else {
                    try {
                        clearTimeout(timer);
                    } catch (e) {}
                    try {
                        //console.log("next")
                        document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
                    } catch (e) {
                        //console.log(e)
                        //console.log("样式问题")
                    }
                    //UXinJSInterfaceSpeech.stopAudio();
                }
            }
        }
    } else {

    }
}

function audiostopword(status) {

}


function getJson() {
    var url = window.location.href;
    var theRequest = new Object();
    if (url.indexOf("?") == -1) {
        return theRequest;
    }
    var arr = url.split('?')[1].split('&');
    for (var i = 0; i < arr.length; i++) {
        var kye = arr[i].split("=")[0]
        var value = arr[i].split("=")[1]
        // 给对象赋值
        theRequest[kye] = value;
    }
    return theRequest;
}

function startplayer(lists, loops, times, positions) {
    try {
        clearTimeout(timer);
    } catch (e) {}
    isplay = true;
    if (audiotype == 3) {
        bookstartplayer(lists, loops, times, positions);
    } else if (audiotype == 1) {
        chapterstartplayer(lists, loops, times, positions);
    } else if (audiotype == 2) {
        textstartplayer(lists, loops, times, positions);
    } else {
        audiostopword(status);
    }
}

function textstartplayer(lists, loops, times, positions) {
    //将数据给UXIN熄屏播放组件进行播放
    isplay = true
    if (loops == 0) {
        var arr = [];
        var mp3list = lists[position[0] % lists.length];
        arr.push(mp3list)
        try {
            UXinJSInterfaceSpeech.updateBackgroundMusicArr(JSON.stringify(arr), loops, 3, times);
        } catch (e) {
            //console.log(e)
        }
    } else {
        var mp3list = JSON.stringify(lists);
        try {
            UXinJSInterfaceSpeech.updateBackgroundMusicArr(mp3list, loops, 3, times);
        } catch (e) {

        }
    }

    list = lists;
    loop = loops;
    time = times;
    position = positions;
    var listkeys = Object.keys(list);
    var childrenkeys = Object.keys(list[listkeys[position[0]]]["texts"]);
    //如果超过了
    if (position[0] >= list.length) {
        position[0] = 0;
        position[1] = 0;
        timer = setTimeout(function () {
            mySwiper.slideTo(0);
        }, intervaltime);
    } else {
        if (position[0] < 0) {
            position[0] = 0;
        }
        if (position[1] < 0) {
            position[0] = position[0] - 1;
            if (position[0] < 0) {
                position[0] = 0;
            }
            position[1] = 0;
            mySwiper.slideTo(position[0]);
        } else {
            //console.log("textstart");
            //console.log(positions);
            //console.log("textend");
            if (positions[1] >= childrenkeys.length) {
                position[0] = position[0] + 1;
                if (position[0] >= list.length) {
                    position[0] = 0;
                }
                position[1] = 0;
                mySwiper.slideTo(position[0]);
                var positions = position;
                //timer = setTimeout(function(){
                startplayer(list, loop, time, positions);
                //},intervaltime);
            } else {
                //首先将当前的样式进行修改
                var parent = document.getElementsByClassName("swiper-wrapper")[0].getElementsByClassName("swiper-slide")[position[0]];
                //删除样式
                if (parent.getElementsByClassName("active").length > 0) {
                    parent.getElementsByClassName("active")[0].style.color = "";
                    try {
                        parent.getElementsByClassName("active")[0].getElementsByClassName("am-list-brief")[0].style.color = "";
                    } catch (e) {

                    }
                    parent.getElementsByClassName("active")[0].classList.remove('active');
                }
                //添加样式
                var children = parent.getElementsByClassName("am-list-item")[position[1]];
                children.getElementsByClassName("am-list-content")[0].style.color = "orange";
                try {
                    children.getElementsByClassName("am-list-content")[0].getElementsByClassName("am-list-brief")[0].style.color = "orange";
                } catch (e) {

                }
                children.getElementsByClassName("am-list-content")[0].classList.add('active');
                //播放
                if (playtime < time || time == 0) {
                    starttime = new Date().getTime();
                    vcomEnAudioPlay();
                }
            }
        }
    }
}

function chapterstartplayer(lists, loops, times, positions) {
    // alert(JSON.stringify(lists));
    //把所有的颜色都变成黑色 然后把需要变色的变成黑色
    list = JSON.parse(JSON.stringify(lists));
    //alert(SON.stringify(lists));
    //console.log(list)
    if (loops == 0) {
        var unit_index_list = Object.keys(list);
        var cur_unit_index = unit_index_list[position[0]];
        var cur_chapter_list = list[cur_unit_index].children;
        var chapter_index_list = Object.keys(cur_chapter_list);
        var cur_chapter_index = chapter_index_list[position[1]];
        var cur_chapter = cur_chapter_list[cur_chapter_index];
        cur_chapter.texts = cur_chapter.children;
        var arr = [];
        arr.push(cur_chapter);
        try {
            UXinJSInterfaceSpeech.updateBackgroundMusicArr(JSON.stringify(arr), loops, 3, times);
        } catch (e) {

        }
    } else if (loops == 1) {
        var unit_index_list = Object.keys(list);
        var cur_unit_index = unit_index_list[position[0]];
        var cur_chapter_list = list[cur_unit_index].children;
        var mp3_list = [];
        for (var i = 0; i < cur_chapter_list.length; i++) {
            chapter_index_list[i].texts = chapter_index_list[i].children;
            mp3_list.push(chapter_index_list[i]);
        }
        //console.log(cur_chapter_list)
        try {
            UXinJSInterfaceSpeech.updateBackgroundMusicArr(JSON.stringify(mp3_list), loops, 3, times);
        } catch (e) {

        }
    } else if (loops == 2) {
        var mp3_list = [];
        for (var j = 0; j < list.length; j++) {
            var cur_chapter_list = list[j].children;
            for (var i = 0; i < cur_chapter_list.length; i++) {
                cur_chapter_list[i].texts = cur_chapter_list[i].children;
                mp3_list.push(cur_chapter_list[i]);
            }
        }
        try {
            UXinJSInterfaceSpeech.updateBackgroundMusicArr(JSON.stringify(mp3_list), loops, 3, times);
        } catch (e) {

        }
    }
    var ksk = document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk");
    // for(var i = 0;i<ksk.length;i++){
    //     var children = ksk[i].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item");
    //     for(var j = 0;j<children.length;j++){
    //         if(i == positions[0] && j == positions[j]){

    //         }else{
    //             children[j].getElementsByClassName("am-list-content")[0].style.color = "";
    //             children[j].getElementsByClassName("am-list-content")[0].classList.add('active');
    //         }
    //     }
    // }
    loop = loops;
    time = times;
    position = positions;
    //首先将当前的样式进行修改
    removeAudioClass(position);

    //改变图标样式
    addAudioClass(position);
    //addAudioIcon(position,"non-audio-active","audio-active");
    //播放
    //console.log(time);
    if (playtime < time || time == 0) {
        starttime = new Date().getTime();
        vcomEnAudioPlay();
    }
}



function bookstartplayer() {
    list = lists;
    loop = loops;
    time = times;
    position = positions;
    if (position[0] >= list.length) {
        position[0] = 0;
        position[1] = 0;
        mySwiper.slideTo(0);
        var positions = position;
        startplayer(list, loop, time, positions);
    } else {
        //首先将当前的样式进行修改
        var parent = document.getElementsByClassName("banner")[0].getElementsByClassName("swiper-slide")[position[0]];
        var children = parent.getElementsByClassName("am-list-item")[position[1]];
        //改变图标样式
        children.getElementsByClassName("am-list-content")[0].style.color = "orange";
        children.getElementsByClassName("am-list-content")[0].getElementsByClassName("am-list-brief")[0].style.color = "orange";
        children.getElementsByClassName("am-list-content")[0].classList.add('active');
        //播放
        if (playtime < time || time == 0) {
            starttime = new Date().getTime();
            vcomEnAudioPlay();
        }
    }
}

//音频播放
function vcomEnAudioPlay() {
   // alert(audiotype);
    //console.log(audiotype)
    if (audiotype == 3) {
        var listkeys = Object.keys(list);
        var childrenkeys = Object.keys(list[listkeys[position[0]]]["texts"]);
        var mp3list = list[listkeys[position[0]]]["texts"];
        var mp3 = mp3list[position[1]].mp3;
        position[1] = position[1] + 1;
        UXinJSInterfaceSpeech.playAudio(mp3);
    } else if (audiotype == 1) {
        //console.log("play")
        //console.log(list)
        var listkeys = Object.keys(list);
        //console.log(listkeys)
        var childrenkeys = Object.keys(list[listkeys[position[0] % listkeys.length]]["children"]);
        //console.log(childrenkeys)
        //console.log(position)
        //直接将状态修改了
        if (listkeys.length > position[0] && childrenkeys.length > position[1]) {
            var mp3list = list[listkeys[position[0] % listkeys.length]]["children"][childrenkeys[position[1] % childrenkeys.length]]["children"];
            var mp3listkeys = Object.keys(mp3list);
            var mp3 = mp3list[mp3listkeys[mp3index % mp3listkeys.length]].mp3;

            var object = mp3list[mp3listkeys[mp3index % mp3listkeys.length]];
             //alert(JSON.stringify(object));
            try {
                mp3index++;
                //window.UXinJSInterfaceSpeech.playAudioObject(JSON.stringify(object));
                window.UXinJSInterfaceSpeech.playAudio(mp3);
            } catch (e) {
                //console.log(e)
                onAudioPlayStatus(0);
            }
        }
    } else if (audiotype == 2) {
        //console.log(position);
        var listkeys = Object.keys(list);
        var childrenkeys = Object.keys(list[listkeys[position[0] % listkeys.length]]["texts"]);
        //if(listkeys.length>position[0]&&childrenkeys.length>position[1]){
        var mp3list = list[listkeys[position[0] % listkeys.length]]["texts"];
        //console.log(position[1]);
        //console.log(mp3list);
        var mp3 = mp3list[position[1] % mp3list.length].url;
        var object = mp3list[position[1] % mp3list.length];
        object.mp3 = mp3;
        //console.log(object);
        try {
           // window.UXinJSInterfaceSpeech.playAudioObject(JSON.stringify(object));
             window.UXinJSInterfaceSpeech.playAudio(mp3);
        } catch (e) {
            // try{
            //     window.UXinJSInterfaceSpeech.playAudio(mp3);
            // }catch(e){

            // }
        }
    } else {
        audiostopword(status);
    }


}

//录音类
var record = function () {
    //开始录音
    this.recordVoice = function () {
        window.UXinJSInterfaceSpeech.recordReset();
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
var records = new record();

//熄屏播放app调用播放
function playAudioLast(object) {
    playStatus = true;
    try {
        clearTimeout(timer);
    } catch (e) {}
    isplay = true;
    try {
        //console.log("stop")
        document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[1].click();
    } catch (e) {
        //console.log(e)
        //console.log("样式问题")
    }
}

function playAudioNext(object) {
    debugger
    playStatus = true;
    try {
        clearTimeout(timer);
    } catch (e) {}
    isplay = true;
    try {
        //console.log("next")
        document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[3].click();
    } catch (e) {
        //console.log(e)
        //console.log("样式问题")
    }
}

function playAudioStop(object) {
    playStatus = false;
    isplay = true;
    try {
        clearTimeout(timer);
    } catch (e) {}
    try {
        window.UXinJSInterfaceSpeech.stopAudio();
    } catch (e) {

    }
    if (audiotype == 2) {
        var playObject = JSON.parse(decodeURIComponent(object));
        //当前播放的ID
        var alllists = list;
        //查找当前播放的是哪一页
        var chapterid = playObject.chapterid;
        var textid = playObject.id;
        var page = 0;
        for (var i = 0; i < alllists.length; i++) {
            var value = alllists[i];
            if (value.id === chapterid) {
                page = i;
            }
        }
        //获取当前的页面
        var curpage = mySwiper.activeIndex;
        if (page !== curpage) {
            mySwiper.slideTo(page, 0);
        }
        //获取当前播放的是哪一个
        var textlists = alllists[page].texts;
        var column = 0;
        for (var j = 0; j < textlists.length; j++) {
            var value = textlists[j];
            if (value.id === playObject.id) {
                column = j;
            }
        }
        position = [page % (alllists.length), column % (textlists.length)];
        //点击停止
        //网页播放面板的停止样式
        try {
            //console.log("stop")
            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[2].click();
        } catch (e) {
            //console.log(e)
            //console.log("样式问题")
        }
    } else if (audiotype == 1) {
        //章节列表进行播放问题
        var playObject = JSON.parse(decodeURIComponent(object));
        //console.log("chapterstop")
        //console.log(playObject)
        var textid = playObject.id;
        var page = 0,
            column = 0,
            i = 0,
            j = 0;
        for (var unit in list) {
            i = i + 1
            for (var chapter in list.children) {
                j = j + 1
                for (var text in chapter.children) {
                    if (text.id == textid) {
                        page = i;
                        column = j;
                    }
                }
            }
        }
        position = [page, column];
        //console.log(position)
        try {
            //console.log("stop")
            document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[2].click();
        } catch (e) {
            //console.log(e)
            //console.log("样式问题")
        }

    }
    isplay = false;
}

function playAudioPlay(object) {
    playStatus = true;
    isplay = false;
    //console.log("play")
    try {
        document.getElementsByClassName("playpanel")[0].getElementsByClassName("am-flexbox-item")[2].click();
    } catch (e) {
        //console.log(e)
        //console.log("样式问题")
    }
    //vcomEnAudioPlay();
}


//后台熄屏播放屏幕点亮回调函数
function restoreForegroundStateWithMusicUrl(object) {
    if (audiotype == 2) {
        var playObject = JSON.parse(decodeURIComponent(object));
        //当前播放的ID
        var alllists = list;
        //查找当前播放的是哪一页
        var chapterid = playObject.chapterid;
        var textid = playObject.id;
        var page = 0;
        for (var i = 0; i < alllists.length; i++) {
            var value = alllists[i];
            if (value.id === chapterid) {
                page = i;
            }
        }
        //获取当前的页面
        var curpage = mySwiper.activeIndex;
        if (page !== curpage) {
            mySwiper.slideTo(page, 0);
        }
        //获取当前播放的是哪一个
        var textlists = alllists[page].texts;
        var column = 0;
        for (var j = 0; j < textlists.length; j++) {
            var value = textlists[j];
            if (value.id === playObject.id) {
                column = j;
            }
        }
        position = [page, column];
        if (playStatus) {
            vcomEnAudioPlay();
        }
    } else if (audiotype == 1) {
        var playObject = JSON.parse(decodeURIComponent(object));
        //当前播放的ID
        var alllists = list;
        //查找当前播放的是哪一页
        var chapterid = playObject.chapterid;
        var textid = playObject.id;
        var page = 0;
        for (var i = 0; i < alllists.length; i++) {
            var chapter_list = alllists[i].children;
            for (var j = 0; j < chapter_list.length; j++) {
                if (chapter_list[j].id == chapterid) {
                    page = i;
                    column = j;
                }
                var text_list = chapter_list[j].children;
                for (var p = 0; p < text_list.length; p++) {
                    if (text_list[p].id == textid) {
                        mp3index = p;
                    }
                }
            }
        }
        position = [page, column];
        if (playStatus) {
            vcomEnAudioPlay();
        }
    }
}

//工具类
function removeAudioClass(pos) {
    var parent = document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]];
    var children = parent.getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]];
    if (parent.getElementsByClassName("active") != undefined && parent.getElementsByClassName("active").length > 0) {
        for (var i = 0; i < parent.getElementsByClassName("active").length; i++) {
            parent.getElementsByClassName("active")[i].style.color = "";
            parent.getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[i].getElementsByClassName("am-list-content")[0].classList.remove('active');
        }
    }
}

function addAudioClass(pos) {
    var parent = document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]];
    var children = parent.getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]];
    var color = children.getElementsByClassName("am-list-content")[0].style.color;
    if (color == "orange") {
        // children.getElementsByClassName("am-list-content")[0].style.color = "#ceb3b3";
        // children.getElementsByClassName("am-list-content")[0].classList.add('active')
    } else {
        children.getElementsByClassName("am-list-content")[0].style.color = "orange";
        children.getElementsByClassName("am-list-content")[0].classList.add('active')
    }
}



function addAudioIcon(pos, removeclassname, addclassname) {
    var parent = document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]];
    var children = parent.getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]];
    var curclassname = children.classList;
    if (!curclassname.contains('audio-active')) {
        if (!curclassname.contains("half-audio-active")) {

            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].getElementsByClassName("am-list-thumb")[0].style.backgroundColor = "#4CD964";
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].getElementsByClassName("am-list-thumb")[0].style.borderColor = "#4CD964";

            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].classList.remove("non-audio-active");
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].classList.add("half-audio-active");
        } else {
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].getElementsByClassName("am-list-thumb")[0].style.backgroundColor = "#50B4F9";
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].getElementsByClassName("am-list-thumb")[0].style.borderColor = "#50B4F9";
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].classList.remove("non-audio-active");
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].classList.remove("half-audio-active");
            document.getElementsByClassName("my-list")[0].getElementsByClassName("ksk")[pos[0]].getElementsByClassName("am-accordion-content-box")[0].getElementsByClassName("am-list-item")[pos[1]].classList.add("audio-active");
        }
    }
}

function stopplayer() {
    try {
        clearTimeout(timer);
    } catch (e) {

    }

    isplay = false;
    //停止音频
    try {
        UXinJSInterfaceSpeech.stopAudio();
    } catch (e) {

    }

}


function updateUserStatus(data) {
    $.post("../public/updateUserTextStatus", {
        data: data
    });
}


function addClass(obj, cls) {
    var obj_class = obj.className, //获取 class 内容.
        blank = (obj_class != '') ? ' ' : ''; //判断获取到的 class 是否为空, 如果不为空在前面加个'空格'.
    added = obj_class + blank + cls; //组合原来的 class 和需要添加的 class.
    obj.className = added; //替换原来的 class.
}

function removeClass(obj, cls) {
    var obj_class = ' ' + obj.className + ' '; //获取 class 内容, 并在首尾各加一个空格. ex) 'abc    bcd' -> ' abc    bcd '
    obj_class = obj_class.replace(/(\s+)/gi, ' '), //将多余的空字符替换成一个空格. ex) ' abc    bcd ' -> ' abc bcd '
        removed = obj_class.replace(' ' + cls + ' ', ' '); //在原来的 class 替换掉首尾加了空格的 class. ex) ' abc bcd ' -> 'bcd '
    removed = removed.replace(/(^\s+)|(\s+$)/g, ''); //去掉首尾空格. ex) 'bcd ' -> 'bcd'
    obj.className = removed; //替换原来的 class.
}