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

var player = function(list, type) {
  // this.list = function(list,type){
  //     // console.log(list);
  //     // console.log(JSON.stringify(list[0]));
  //     if(type == 'word'){
  //         //需要重新拼接json
  //         var obj = [];
  //         var start = '{"word":["noLoop"],"py":["start"],"fy":["start"],"fayin":[{"fy":"start_1","sleep":"0"}]}';
  //         var end = '{"word":["noLoop"],"py":["end"],"fy":["end"],"fayin":[{"fy":"end","sleep":"0"}]}';
  //         var ding = '{"word":["noLoop"],"py":["ding"],"fy":["ding"],"fayin":[{"fy":"ding","sleep":"0"}]}';
  //         obj.push(eval('('+start+')'));
  //         $.each(list,function(k,v){
  //             obj.push(eval('('+ding+')'));
  //             obj.push(v);
  //         });
  //         obj.push(eval('('+end+')'));
  //         return obj;
  //     }else{
  //         //不需要
  //         return list;
  //     }
  // }(list,type);
  this.list = list;
  this.leixing = 1; //1：定制的带开始结束提示语；2：不带提示语音，就是单独连续播放；3：下载完就播放
  this.playType = "";
  this.pageTotal;
  this.type = type;
  this.pageCurrent = 0;
  this.pageTotal;
  this.index = 0;
  this.pageSize;
  this.loop = 1;
  this.mode = 0; //0：自动；1：手动；
  this.count = 0;
  this.sleep = 2000; //每个音之间的间隔
  this.nextSleep = 0; //翻页的间隔
  this.t; //播放的
  this.tt; //翻页的
  this.flag = false; //是否停止播放
  this.num = 0; //当前第几个
  this.isloop = false; //是否可以循环

  this.init = function() {
    this.pageTotal = this.list.length;
  };

  this.checkLoop = function() {
    if (typeof this.list[this.pageCurrent].word == "undefined") {
      this.isloop = false;
    } else {
      if (this.list[this.pageCurrent].word[this.index] == "noLoop") {
        this.isloop = false;
      } else {
        this.isloop = true;
      }
    }
  };
  this.getIsloop = function() {
    return this.isloop;
  };
  this.getList = function() {
    return this.list;
  };
  this.getType = function() {
    return this.type;
  };
  this.getPageCurrent = function() {
    return this.pageCurrent;
  };
  this.getPageTotal = function() {
    return this.pageTotal;
  };
  this.getIndex = function() {
    return this.index;
  };
  this.getPageSize = function() {
    return this.pageSize;
  };
  this.getLoop = function() {
    return this.loop;
  };
  this.getCount = function() {
    return this.count;
  };
  this.getSleep = function() {
    return this.sleep;
  };
  this.getNextSleep = function() {
    return this.nextSleep;
  };
  this.getT = function() {
    return this.t;
  };
  this.getTt = function() {
    return this.tt;
  };
  this.getFlag = function() {
    return this.flag;
  };
  this.getNum = function() {
    return this.num;
  };
  this.getLeixing = function() {
    return this.leixing;
  };
  this.getPlayType = function() {
    return this.playType;
  };

  this.setPlayType = function(type) {
    this.playType = type;
  };
  this.setLeixing = function(num) {
    this.leixing = num;
  };

  this.setCount = function(num) {
    this.count = num;
  };
  this.setNum = function(num) {
    this.num = num;
  };
  this.setPageCurrent = function(num) {
    this.pageCurrent = num;
  };
  this.setPageTotal = function(num) {
    this.pageTotal = num;
  };
  this.setIndex = function(num) {
    this.index = num;
  };
  this.setPageSize = function(num) {
    this.pageSize = num;
  };
  this.setT = function(t) {
    this.t = t;
  };
  this.setTt = function(tt) {
    this.tt = tt;
  };
  this.setFlag = function(num) {
    if (num == 1) {
      this.flag = true;
    } else {
      this.flag = false;
    }
  };
  this.setSleep = function(num) {
    this.sleep = num;
  };
  this.setNextSleep = function(num) {
    this.nextSleep = num;
  };
  this.setLoop = function(num) {
    this.loop = num;
  };

  //当前页码加1
  this.addPageCurrent = function() {
    this.pageCurrent = ++this.pageCurrent;
  };
  //本页内容列表索引加1
  this.addIndex = function() {
    this.index = ++this.index;
  };
  //循环播放次数加1
  this.addCount = function() {
    this.count = ++this.count;
  };
  //当前字加1
  this.addNum = function() {
    this.num = ++this.num;
  };

  this.clearPlay = function() {
    clearTimeout(this.t);
  };

  this.download = function() {
    try {
      UXinJSInterfaceSpeech.DownLoadAudioFile("mp3", this.list);
    } catch (err) {
      console.log("error");
      setTip("下载失败");
    }
  };

  //下载mp3列表
  this.downloadList = function(aa) {
    // var jsonstr = '[{name:"20078311111257",size:"2916002",format:"mp3",url:"http://en.youjiaotong.com/yylmp3/mp3_word/B59E57FD261506BCDDF108EADBF2F456.mp3"}]';
    try {
      UXinJSInterfaceSpeech.cacheAudioFiles(
        iGetInnerText(JSON.stringify(downloadJson))
      );
    } catch (err) {
      setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
    }
  };
  //停止播放
  this.stopAudio = function() {
    try {
      UXinJSInterfaceSpeech.stopAudio();
      clearTimeout(mp3.getT());
    } catch (err) {
      setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
    }
  };
  //暂停播放
  this.pauseAudio = function() {
    UXinJSInterfaceSpeech.pauseAudio();
  };
  //暂停后恢复播放
  this.resumeAudio = function() {
    UXinJSInterfaceSpeech.resumeAudio();
  };
  //从指定开始时间播放该音频
  this.playAudioAtTime = function(url, Time) {
    UXinJSInterfaceSpeech.playAudioAtTime(url, Time);
  };
  //播放生字
  this.playWord = function() {
    try {
      this.checkLoop();
      if (this.getLeixing() == 2) {
        this.sleep = 0;
        this.flag = false;
        // setTimeout(() => {
        //   onAudioPlayStatus(0);
        // }, 4000);

        // return false;
        if (this.playType == "playci") {
          UXinJSInterfaceSpeech.playAudio(
            this.list[this.pageCurrent].fayin[this.index].fy + ".mp3"
          );
        } else {
          UXinJSInterfaceSpeech.playAudio(
            this.list[this.pageCurrent].fayin[this.index].fy
          );
        }
      } else {
        if (this.mode == 0) {
          if (this.flag) {
            return false;
          }
        }
        // alert(this.list[this.pageCurrent].fayin[this.index].fy);
        UXinJSInterfaceSpeech.playAudio(
          this.list[this.pageCurrent].fayin[this.index].fy + ".mp3"
        );
      }
    } catch (err) {
      setTip("播放失败，请退出重试。");
      console.log(
        this.pageCurrent +
          "/" +
          this.index +
          "/" +
          this.list[this.pageCurrent].fayin[this.index].fy
      );
    }
    mp3.setSleep(this.list[this.pageCurrent].fayin[this.index].sleep * 1000);
    // onAudioPlayStatus(0);
  };
  this.playSingle = function() {
    try {
      UXinJSInterfaceSpeech.playAudio(this.list.getBaseName());
    } catch (err) {
      setTip("播放失败，请退出重试。");
    }
  };
  this.playNet = function() {
    try {
      UXinJSInterfaceSpeech.playAudio(
        baseNetUrl + "/uploadsyw/zipinyin/" + this.list
      );
    } catch (err) {
      setTip("播放失败，请退出重试。");
    }
  };
  this.playTest = function() {
    try {
      UXinJSInterfaceSpeech.playAudio(baseNetUrl + "/uploadsyw/yes.mp3");
    } catch (err) {
      setTip("播放失败，请退出重试。");
    }
  };
};

//优信播放完毕调用函数
function onAudioPlayStatus(status) {
 // alert(status);
  $("#playimg").attr("src","/public/Yuwen/share/images/play.png");
  $("#playbutton").attr("isfirst", "1");
  $("#playbutton").prop("class", "icon-playk");
  if (status == 0) {
    playErrNum = 0;
    if (
      mp3.getType() == "kewen" ||
      mp3.getType() == "zi" ||
      mp3.getType() == "luyin"
    ) {
      $("i[name=playbutton]")
        .prop("class", "icon-bofang")
        .attr("isfirst", 1);
      try {
        time = 0;
        // HideJindu();//滑块隐藏
      } catch (e) {}
      return false;
    }
    if (
      mp3.getType().indexOf(".amr") > 0 ||
      mp3.getType().indexOf(".mp3") > 0
    ) {
      $("a[name=end]").hide();
      $("a[name=beg]").show();
      $("i[name=playbutton].icon-playk").attr("isfirst", 1);
      return false;
    }
    if (mp3.getFlag()) {
      return false;
    }
    mp3.addIndex();
    mp3.setPageSize(mp3.getList()[mp3.getPageCurrent()].fayin.length);

    if (mp3.getLeixing() == 2) {
      //录音连续播放
      if (mp3.getIndex() < mp3.getPageSize()) {
        //还有下一个
        if (mp3.getPlayType() == "playci") {
          setTimeout("mp3.playWord()", mp3.getSleep());
          return false;
        }
        $("i[name=playbutton]")
          .prop("class", "icon-bofang")
          .attr("isfirst", 1);

        $("ul.mui-table-view li")
          .eq(mp3.getIndex())
          .find("em")
          .eq(0)
          .find("i")
          .prop("class", "icon-zanting")
          .attr("isfirst", 0);
        var count = $(
          'label[bid_play="' +
            mp3.getList()[mp3.getPageCurrent()].fayin[mp3.getIndex()].id +
            '"]'
        ).html();
        $(
          'label[bid_play="' +
            mp3.getList()[mp3.getPageCurrent()].fayin[mp3.getIndex()].id +
            '"]'
        ).html(count * 1 + 1);
        var tusername = $(
          'label[bid_play="' +
            mp3.getList()[mp3.getPageCurrent()].fayin[mp3.getIndex()].id +
            '"]'
        )
          .next()
          .attr("tusername");
        addRecordListenCount(
          mp3.getList()[mp3.getPageCurrent()].fayin[mp3.getIndex()].id,
          tusername
        );

        var off_height = $(".mui-scroll").offset().top; //滑动区域具体屏幕顶部距离
        off_height = 120;
        //以下所说的高度，起点都是滑动区域的起点，不是屏幕顶端
        // var slid_height = $(window).height() - off_height; //获取滑动区域的高度
        var slid_height = $(document).height() - off_height; //获取滑动区域的高度

        var middle_height = slid_height / 2; //当前滑动区域的一半高度
        // alert($("ul.topList li").eq(5).offset().top);
        // alert('middle'+middle_height);
        var li_height = $("ul.mui-table-view li")
          .eq(mp3.getIndex())
          .height();
        var now_height =
          $("ul.mui-table-view li")
            .eq(mp3.getIndex())
            .offset().top - off_height; //获取当前li的高度
        // alert('liheight'+li_height);
        // alert('now'+now_height);
        var last_height =
          $("ul.mui-table-view li")
            .eq(mp3.getPageSize() - 1)
            .offset().top - off_height; //获取最后一个li的高度
        var move_height = now_height - middle_height + li_height; //每次滑动的距离
        // console.log("li height" + li_height);
        // console.log(
        //   "li height_top" +
        //     now_height +
        //     "|last_height" +
        //     last_height +
        //     "|move_height" +
        //     move_height +
        //     "|slid_height" +
        //     slid_height
        // );
        // console.log(now_height > middle_height);
        // console.log(last_height > slid_height);
        // totalheight++;
        // mui("#pullrefresh")
        //   .pullRefresh()
        //   .scrollTo(0, -li_height * totalheight);
        // totalheight += move_height;
        totalheight += li_height;
        //如果当前高度大于滑动区域的一半，并且最后一个元素的高度大于滑动区域的高度
        if (
          now_height > middle_height &&
          last_height + li_height > slid_height
        ) {
          // $(".inner").css('transform','translate(0px, -'+move_height+'px)');
          // window.scroll(0, move_height);

          mui("#pullrefresh")
            .pullRefresh()
            .scrollTo(0, -totalheight);
        }
      } else {
        //没有下一个了，播放完毕
        if (mp3.getPlayType() == "playci") {
          mp3.setIndex(0);
          mp3.addPageCurrent();

          if (mp3.getPageCurrent() < mp3.getPageTotal()) {
            setTimeout("mp3.playWord()", mp3.getSleep());
          }
          return false;
        }
        totalheight = 0;
        $("i[name=playbutton]")
          .prop("class", "icon-bofang")
          .attr("isfirst", 1);
        return false;
      }
    }
    if (mp3.getIndex() < mp3.getPageSize()) {
      setTimeout("mp3.playWord()", mp3.getSleep());
    } else {
      mp3.setIndex(0);
      if (mp3.getLoop() == 1 || !mp3.getIsloop()) {
        //不可以循环
        mp3.addPageCurrent();
        console.log(mp3.getPageCurrent());
        if (mp3.getPageCurrent() < mp3.getPageTotal()) {
          if (mp3.getPageCurrent() == 1) {
            setTimeout("mp3.playWord()", 0);
          } else {
            setTimeout("mp3.playWord()", mp3.getNextSleep());
          }

          if (mp3.getType() == "word") {
            //播放生字
            if (mp3.getLeixing() == 1) {
              //带语音提示的连续播放
              if (mode == 0) {
                //自动播放
                if (
                  mp3.getPageCurrent() > 2 &&
                  mp3.getPageCurrent() < mp3.getPageTotal() - 1 &&
                  mp3.getPageCurrent() % 2 != 0
                ) {
                  console.log("next");
                  wordObj.next();
                }
              }
            }
          }
        } else {
          // alert('播放完毕');
          //自动
          if (mp3.getLeixing() == 1) {
            if (mode == 0) {
              goMyWord();
              // showResultView();
            } else {
              var num = $(".redFont").html();
              var totalNum = $("#total").html();
              if (num == totalNum) {
                goMyWord();
                // showResultView();
              }
            }
          } else if (mp3.getLeixing() == 2) {
            //录音连续播放
            return false;
          }
        }
      } else if (mp3.getLoop() > 1 && mp3.getIsloop()) {
        mp3.addCount();
        mp3.setSleep(5 * 1000); //循环的时候要停顿
        if (mp3.getCount() < mp3.getLoop()) {
          setTimeout("mp3.playWord()", mp3.getSleep());
        } else {
          mp3.setCount(0);
          mp3.addPageCurrent();
          if (mp3.getPageCurrent() < mp3.getPageTotal()) {
            setTimeout("mp3.playWord()", mp3.getSleep());
            if (mp3.getType() == "word") {
              //播放生字
              if (
                mp3.getPageCurrent() > 2 &&
                mp3.getPageCurrent() < mp3.getPageTotal() - 1 &&
                mp3.getPageCurrent() % 2 != 0
              ) {
                wordObj.next();
              }
            }
          } else {
            //播放完毕
            if (mode == 0) {
              showResultView();
            } else {
              var num = $(".redFont").html();
              var totalNum = $("#total").html();
              if (num == totalNum) {
                goMyWord();
                // showResultView();
              }
            }
          }
        }
      }
    }
  } else {
    // alert("play error!");
    playErrNum++;
    if (playErrNum < 2) {
      if (mp3.getType() == "word") {
        //播放单词
        // UXinJSInterfaceSpeech.playAudio(
        //   baseNetUrl +
        //     "/uploadsyw/zipinyin/" +
        //     mp3.getList()[mp3.getPageCurrent()].fy[mp3.getIndex()] +
        //     ".mp3"
        // );
      } else if (mp3.getType() == "kewen") {
        //播放课文
        UXinJSInterfaceSpeech.playAudio(
          baseNetUrl + "/uploadsyw/kewenvoice/" + mp3.getList()
        );
      } else if (mp3.getType() == "zi") {
        //播放单个生字
        // UXinJSInterfaceSpeech.playAudio(
        //   baseNetUrl + "/uploadsyw/zipinyin/" + mp3.getList()
        // );
      } else if (mp3.getType() == "luyin") {
        //播放录音
        UXinJSInterfaceSpeech.playAudio(recordNetUrl + mp3.getList());
      }
      playErrNum = 0;
    } else {
      setTip("播放失败");
    }
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

function gopage(page) {
  var c = document.getElementById("bdslide");
  // a=-(page)*$(window).width();
  a =
    -page *
    $("#bdslide")
      .children("ul")
      .eq(0)
      .width();
  b = 200;
  (c = c.style),
    (c.webkitTransitionDuration = c.MozTransitionDuration = c.msTransitionDuration = c.OTransitionDuration = c.transitionDuration =
      b + "ms"),
    (c.webkitTransform = "translate(" + a + "px,0)" + "translateZ(0)"),
    (c.msTransform = c.MozTransform = c.OTransform = "translateX(" + a + "px)");
  c.style = c;
  $(".redFont").html(page + 1);
}

//提示信息
function setTip(content) {
  var tip = document.getElementById("tips");
  tip.innerHTML = content;
  tip.style.display = "block";
  setTimeout(function() {
    tip.style.display = "none";
  }, 3000);
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
 * 跳转字或者词的详细页面
 */
function goWordInfo(obj, type) {
  if (type == 1) {
    //word页面
    var goword = $(obj).attr("wordinfo");
    var backurl = $(obj).attr("backurl");
    var tagNamge = $("li.on").html();
  } else if (type == 2) {
    //revision页面
    var goword = $(obj)
      .parent()
      .next()
      .attr("wordinfo");
    var backurl = $(obj)
      .parent()
      .next()
      .attr("backurl");
    var tagNamge = "";
  } else if (type == 3) {
    //wordinfo页面
    var goword = $(obj).attr("wordinfo");
    var backurl = $(obj).attr("backurl");
    var tagNamge = $("#tag").html();
  }

  if (goword.length == 1) {
    var fy = $(obj).attr("fy");
  } else {
    var fy = "";
  }

  location.href =
    "wordinfo?wordinfo=" +
    encodeURI(encodeURI(goword)) +
    "&ks_code=" +
    ks_code +
    "&ks_name=" +
    encodeURI(encodeURI(ks_name)) +
    "&tag=" +
    encodeURI(encodeURI(tagNamge)) +
    "&fy=" +
    fy +
    "&index=" +
    index +
    "&backurl=" +
    backurl;
  // if(goword.length == 1){
  //     //字
  //     location.href="wordinfo?wordinfo="+encodeURI(encodeURI(goword))+"&ks_code="+ks_code+"&index="+index+'&backurl='+backurl;
  // }else{
  //     //词
  //     location.href="ci?wordinfo="+encodeURI(encodeURI(goword))+"&ks_code="+ks_code+"&index="+index+"&backurl="+backurl;
  // }
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
      this.sleep = 5;
      this.sleepStep = 1;
    } else if (step == 2) {
      this.sleep = 10;
      this.sleepStep = 2;
    } else if (step == 3) {
      this.sleep = 15;
      this.sleepStep = 3;
    } else {
      this.sleep = 10;
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

    mode = $("#mode i.icon-radio-checked").attr("bid");
    loop = $("#loop").val(); //重复次数
    if (mode == 0) {
      //自动0
      var speedStep = $("#sudu").val();
      speed = this.getSleepByStep(speedStep);
      if (this.type == 2) {
        mp3.setNextSleep(speed * 1000);
      }
    }
    if (this.type == 2) {
      mp3.setLoop(loop);
    }
    this.checkMode();
    var _this = this;
    $.get(
      "../Text/updateMyConfig",
      {
        ran: Math.random(),
        mode: mode,
        speed: speedStep,
        loop: loop
      },
      function(data) {
        _this.getMyConfig();
      }
    );
  };
  this.getMyConfig = function(callback) {
    var _this = this;
    $.get(
      "../Text/getMyConfig",
      {
        ran: Math.random()
      },
      function(data) {
        mode = data[0].mode;
        var speedStep = data[0].speed;
        loop = data[0].loop;
        speed = _this.getSleepByStep(speedStep);
        if (mode == 0) {
          $("#prev").hide();
          $("#next").hide();
          $("#mode")
            .find("i")
            .eq(0)
            .prop("class", "icon-radio-checked");
          $("#mode")
            .find("i")
            .eq(1)
            .prop("class", "icon-radio-unchecked");
          if (_this.type == 2) {
            mp3.setLoop(loop);
            mp3.setNextSleep(speed * 1000);
          }
        } else {
          $("#mode")
            .find("i")
            .eq(0)
            .prop("class", "icon-radio-unchecked");
          $("#mode")
            .find("i")
            .eq(1)
            .prop("class", "icon-radio-checked");
          $("#speedinfo").hide();
          if (_this.type == 2) {
            mp3.setLoop(loop);
            $("#next").hide();
            $("#prev").hide();
          }
        }
        $("#sudu").val(_this.sleepStep);
        $("#loop").val(loop);
        if (_this.type == 1) {
          //首页
        } else if (_this.type == 2) {
          //play页
          _this.checkMode();
          //获取播放列表
          wordObj = new wordPlay();
          wordObj.isShow();
        }
      }
    );
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
