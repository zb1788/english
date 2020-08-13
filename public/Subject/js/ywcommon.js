var playErrNum = 0; //播放失败次数
var recordPathType = 1; //1：录音的试听，2：录音的上传
/**
 * 获取地址栏参数
 * 【注意】传递参数的时候使用encodeURI(encodeURI(content)),取的时候decodeURI(value)
 * @param {[String]} name [参数名称]
 */
function GetQueryString(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
  var r = window.location.search.substr(1).match(reg);
  if (r != null) return decodeURI(decodeURI(unescape(r[2])));
  return null;
}

function checkLocalStorage() {
  if (!window.localStorage) {
    mui.toast("您的手机不支持预览功能");
    return false;
  }
}

/**
 * 存储内容到localStorage
 * 【主要】如果是json格式需要encodeURI(JSON.stringify(value)),取的时候decodeURI(value)
 * @param {[String]} name  [名称]
 * @param {[String]} value [值]
 */
function setLocalStorage(name, value) {
  checkLocalStorage();
  window.localStorage.setItem(name, value);
}

/**
 * 从localStorage获取内容
 * @param {[String]} name  [description]
 * @param {[String]} value [description]
 */
function getLocalStorage(name) {
  checkLocalStorage();
  var value = window.localStorage.getItem(name);
  return decodeURI(value);
}

/**
 * 删除localStorage的指定内容
 * @param  {[String]} name [description]
 */
function removeLocalStorage(name) {
  checkLocalStorage();
  window.localStorage.removeItem(name);
}

function mobileBack() {
  if (document.referrer) {
    window.location.href = document.referrer;
  } else {
    history.back();
  }
}

String.prototype.trim = function() {
  return this.replace(/(^\s*)|(\s*$)/g, "");
};

//去除两边#
String.prototype.trim3 = function() {
  return this.replace(/(^#*)|(#*$)/g, "");
};
String.prototype.trimStr = function(str) {
  return this.replace(eval("/(^" + str + "*)|(" + str + "*$)/g"), "");
};
//替换字符串
String.prototype.replaceStr = function(str) {
  return this.replace(eval("/" + str + "/g"), "");
};
//获取文件名带后缀
String.prototype.getBaseName = function() {
  return this.substring(this.lastIndexOf("/") + 1);
};
//获取文件名不带后缀
String.prototype.getBaseNameNoExt = function() {
  return this.substring(this.lastIndexOf("/") + 1, this.lastIndexOf("."));
};
//获取文件后缀
String.prototype.getExt = function() {
  return this.substring(this.lastIndexOf(".") + 1);
};
//获取文件名带路径不带后缀
String.prototype.getFilePathNoExt = function() {
  return this.substring(0, this.lastIndexOf("."));
};
String.prototype.delHtmlTab = function() {
  return this.replace(/<[^>]+>/g, "");
};

//优信函数
function iGetInnerText(testStr) {
  var resultStr = testStr.replace(/\ +/g, ""); //去掉空格
  resultStr = testStr.replace(/[ ]/g, ""); //去掉空格
  resultStr = testStr.replace(/[\r\n]/g, ""); //去掉回车换行
  return resultStr;
}

//优信播放完毕调用函数
function onAudioPlayStatus(status) {
  console.log(speed)
  clearTimeout(timer);
  if (status == 0) {
    if(!isend){
      //if(loop >  1){
        if(curloop == (loop-1)){
          curloop = 0;
          timer = setTimeout(function(){
            swiper.slideNext();
          },speed*1000)
        }else{
          timer = setTimeout(function(){
            var words = listenarr;
            var witem = words[curpage];
            try{
              UXinJSInterfaceSpeech.playAudio(witem.mp3);
            }catch(e){
              onAudioPlayStatus(0);
            }
            curloop = curloop+1;
          },speed*1000)
        }
      //}else{
      //   timer = setTimeout(function(){
      //      swiper.slideNext();
      //    },speed*2000)
      //}
    }else{
      if(curloop == (loop-1)){
          curloop = 0;
          timer = setTimeout(function(){
            listenover();
          },speed*1000)
        }else{
          timer = setTimeout(function(){
            var words = listenarr;
            var witem = words[curpage];
            try{
              UXinJSInterfaceSpeech.playAudio(witem.mp3);
            }catch(e){
              onAudioPlayStatus(0);
            }

            curloop = curloop+1;
          },speed*1000)
        }
    }
  } else {

  }
    
}

//优信下载完成调用函数
function onCacheAudioStatus(status) {
  if (0 == status) {
    // alert("下載成功");
    if (mp3.getLeixing() == 3) {
      // UXinJSInterfaceSpeech.playAudio(mp3.getList());
      // UXinJSInterfaceSpeech.playAudio(mp3.getList()[mp3.getPageCurrent()].fayin[mp3.getIndex()].fy);
    }
  } else {
    // alert("下載出錯");
  }
}

//提示信息
function setTip(content) {
  var tip = document.getElementById("tips");
  tip.innerHTML = content;
  tip.style.display = "block";
  setTimeout(function() {
    tip.style.display = "none";
  }, 2000);
}

function setTipLocation(content, url) {
  var tip = document.getElementById("tips");
  tip.innerHTML = content;
  tip.style.display = "block";
  setTimeout(function() {
    window.location.href = url;
  }, 1000);
}

/**
 * 录音相关
 * -------------------------------------------
 */
//录音类
var record = function() {
  //开始录音
  this.recordVoice = function() {
    window.UXinJSInterfaceSpeech.audioRecordWithPause();
  };
  //暂停|停止录音
  this.pauseVoice = function() {
    window.UXinJSInterfaceSpeech.pauseAudioRecord();
  };
  //试听录音
  this.listenVoice = function() {
    window.UXinJSInterfaceSpeech.getRecordName();
  };
  //暂停试听
  this.pauseAudio = function() {
    // window.UXinJSInterfaceSpeech.pauseAudio();
    mp3.pauseAudio();
  };
  //暂停后恢复
  this.resumeAudio = function() {
    mp3.resumeAudio();
  };
  //上传录音
  this.uploadVoice = function() {
    window.UXinJSInterfaceSpeech.getRecordName();
  };
  //重置录音
  this.recordReset = function() {
    window.UXinJSInterfaceSpeech.recordReset();
  };
};

//优信调用此函数，传递录音路径
function getRecordPath(path) {
  if (recordPathType == 1) {
    mp3 = new player(path, "luyin");
    mp3.playSingle();
  } else if (recordPathType == 2) {
    window.UXinJSInterfaceSpeech.uploadAudioRecord(path);
  }
}

//获取上传后的地址
function getVoicePath1(path) {
  // alert("上传后文件：" + path);
  uploadVoiceToDb(path);
}
function getVoicePath(path, type) {
  // alert("上传后文件：" + path);
  uploadVoiceToDb(path);
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
  if (recordIsComplete) {
    $(".overY").css("margin-bottom", "280px");
    $(".tts").hide();
    checkRow(2);
    $("#mask").show();
    $(".bottomTan").show();
    $("#timestr").attr("bnum", "0");
  }
}

/**
 * 录音相关END
 * -------------------------------------------
 */

//网页调用优信的playAudioAtTime这个方法的时候，优信会回调网页的这个js（AudioHavePlayAtTime）
function AudioHavePlayAtTime(TotalTime) {
  try {
    // trackBar.value=TotalTime;
    //初始化trackBar
    document.getElementById("trackBar").max = TotalTime;
    $("#totalTime").html(secondToDate(TotalTime));
  } catch (e) {
    setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
  }
}

//格式化时间，秒
function secondToDate(result) {
  var h = Math.floor(result / 3600);
  var m = Math.floor((result / 60) % 60);
  var s = Math.floor(result % 60);

  if (m < 10) {
    m = "0" + m;
  }

  if (s < 10) {
    s = "0" + s;
  }
  if (h == 0) {
    result = m + "'" + s;
  } else {
    result = h + "'" + "'" + m + "'" + s;
  }
  return result;
}

//字词播放类
var wordPlay = function() {
  //获取总数
  this.total = (function() {
    return $("#bdslide").children("ul").length - 1;
  })();
  //当前索引
  this.index = 0;
  this.action = "next";
  this.getIndex = function() {
    return this.index;
  };
  //上一个
  this.prev = function() {
    this.action = "prev";
    this.index = this.index - 1;
    this.isShow();
  };
  //下一个
  this.next = function() {
    this.action = "next";
    this.index = this.index + 1;
    this.isShow();
  };
  this.isShow = function() {
    try {
      if (mode == 1) {
        //手动
        if (this.index == 0) {
          mp3.setFlag(1);
        }
        UXinJSInterfaceSpeech.stopAudio();
      } else {
        mp3.setFlag(0);
      }
    } catch (e) {
      setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
    }

    $(".redFont").html(this.index + 1);
    if (this.index > 0 && this.index < this.total) {
      $("#prev").show();
      $("#next").show();
    }
    if (this.index == 0) {
      $("#prev").hide();
    }
    if (this.index == this.total) {
      $("#next").hide();
    }
    $("#bdslide")
      .children("ul")
      .hide();
    $("#bdslide")
      .children("ul")
      .eq(this.index)
      .show();
    if (mode == 0) {
      $("#prev").hide();
      $("#next").hide();
      // mp3.setPageCurrent(0);
      // mp3.setIndex(0);
      // mp3.setPageTotal(mp3.getList().length);
    } else {
      if (this.index != 0) {
        mp3.setPageCurrent((this.index + 1) * 2);
      } else {
        if (this.action == "prev") {
          mp3.setPageCurrent(2);
        }
      }
      mp3.setPageTotal(1);
      mp3.setIndex(0);
      mp3.playWord();
    }
  };
};

//录音设置相关按钮
//type:1,首页2,play页
var wordSet = function(type) {
  this.type = type;
  this.sleepStep;
  //获取生字播放停顿的间隔(1,2,3)三个阶段
  this.getSleepByStep = function(step) {
    if (step == 1) {
      this.sleep = 3;
      this.sleepStep = 1;
    } else if (step == 2) {
      this.sleep = 5;
      this.sleepStep = 2;
    } else if (step == 3) {
      this.sleep = 8;
      this.sleepStep = 3;
    } else {
      this.sleep = 5;
      this.sleepStep = 2;
    }
    return this.sleep;
  };
  //弹出设置框
  this.show = function() {
    $("#mask").css("display", "block");
    $(".bottomTan").css("display", "block");
  };
  this.cancel = function() {
    $("#mask").css("display", "none");
    $(".bottomTan").css("display", "none");
  };
  //确定按钮
  this.ok = function() {
    $("#mask").css("display", "none");
    $(".bottomTan").css("display", "none");

    mode = $(".mode i.icon-radio-checked").parent().attr("bid");
    loop = $(".loop").val(); //重复次数
    var speedStep = $(".speed").val(); //重复次数
    speed = this.getSleepByStep(speedStep);
  };
  this.getMyConfig = function(callback) {
    var _this = this;
    // $.get(
    //   "../Text/getMyConfig",
    //   {
    //     ran: Math.random()
    //   },
    //   function(data) {
    //     mode = data[0].mode;
    //     var speedStep = data[0].speed;
    //     loop = data[0].loop;
    //     speed = _this.getSleepByStep(speedStep);
    //     if (mode == 0) {
    //       $("#prev").hide();
    //       $("#next").hide();
    //       $("#mode")
    //         .find("i")
    //         .eq(0)
    //         .prop("class", "icon-radio-checked");
    //       $("#mode")
    //         .find("i")
    //         .eq(1)
    //         .prop("class", "icon-radio-unchecked");
    //       if (_this.type == 2) {
    //         mp3.setLoop(loop);
    //         mp3.setNextSleep(speed * 1000);
    //       }
    //     } else {
    //       $("#mode")
    //         .find("i")
    //         .eq(0)
    //         .prop("class", "icon-radio-unchecked");
    //       $("#mode")
    //         .find("i")
    //         .eq(1)
    //         .prop("class", "icon-radio-checked");
    //       $("#speedinfo").hide();
    //       if (_this.type == 2) {
    //         mp3.setLoop(loop);
    //         $("#next").hide();
    //         $("#prev").hide();
    //       }
    //     }
    //     $("#sudu").val(_this.sleepStep);
    //     $("#loop").val(loop);
    //     if (_this.type == 1) {
    //       //首页
    //     } else if (_this.type == 2) {
    //       //play页
    //       _this.checkMode();
    //       //获取播放列表
    //       wordObj = new wordPlay();
    //       wordObj.isShow();
    //     }
    //   }
    // );
  };
  this.checkMode = function() {
    if (mode == 0) {
      var html = '<i class="icon-uniE60C"></i><font id="status">开始</font>';
      //   $("#prev").hide();
      //   $("#next").hide();
    } else {
      var html = '<i class="icon-uniE60C"></i><font id="status">开始</font>';
      //   $("#prev").hide();
      //   $("#next").hide();
      $(".pinTS").hide();
    }
    $(".boxCon")
      .find("a")
      .html(html);
  };
};

function submitInfo(ks_code, moduleid, ks_name) {
  $.ajax({
    url: "../../Subject/Public/setUserModuleUnitLog",
    data: {
      ran: Math.random(),
      ks_code: ks_code,
      moduleid: moduleid,
      ks_name: ks_name
    },
    // datatype: "json",
    type: "get",
    async: false,
    success: function(data) {
      //成功后回调
    },
    error: function(e) {
      //失败后回调
      // alert(e);
    },
    beforeSend: function() {
      //发送请求前调用，可以放一些"正在加载"之类额话
      // alert("正在加载");
    }
  });
}

function downloadAudio() {
  try {
    UXinJSInterfaceSpeech.cacheAudioFiles(
      iGetInnerText(JSON.stringify(eval("(" + downloadJson + ")")))
    );
  } catch (e) {
    setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
    if (1 == 2) {
      var json = eval("(" + downloadJson + ")");
      $.each(json, function(k, v) {
        console.log(v.url);
      });
    }
  }
}

function onWxSharedReq(status) {
  if (status == 0) {
    //分享成功
  } else {
    //分享失败
  }
}

function onUxinVideoEnd(status) {
  // alert(status);
  // hideVoiceView(0);
  if (status == 0) {
    //播放完成结束
  } else {
    //没有播放完成结束
  }
}
