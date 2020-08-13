/**
 * 获取单元信息
 */
function getUnitInfo() {
  $.ajax({
    url: "../Text/getUnitInfo",
    data: {
      ran: Math.random(),
      ks_code: ks_code
    },
    // datatype: "json",
    type: "get",
    async: false,
    success: function(data) {
      //成功后回调
      $(".title").html(data[0].ks_name);
      ks_name = data[0].ks_name;
      textTitle = data[0].ks_name;
      textJson = eval("(" + data[0].content + ")");
      var len = textJson.kewen.length;
      if (data[0].style == 3) {
        //拼音
        barType = 3;
        showContent(data[0].style, ks_code);
      } else {
        if (len == 1) {
          $(".topH4").hide();
          $(".overY").css("margin-top", "45px");
        } else {
          $(".topH4").show();
          $(".overY").css("margin-top", "85px");
        }
        $(".ywBook").addClass("kw");
        if (data[0].style == 1) {
          //普通课文
          // $('.ywBook').removeClass('ss');
          // $('.ywBook').addClass('kw');
          barType = 1;
          showContent(data[0].style, ks_code);
        } else if (data[0].style == 2) {
          barType = 2;
          //古文
          // $('#fanyi').show();
          // $('.ywBook').removeClass('kw');
          // $('.ywBook').addClass('ss');
          showContent(data[0].style, ks_code);
        }
      }
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

/**
 * 展示课文内容
 * @param  {[json]} textJson [课文内容json]
 * @param  {[Sting]} style    [1:课文；2：古文；3：拼音]
 * @param  {[Sting]} ks_code  [章节编码]
 */
function showContent(style, ks_code) {
  // [{name:"20078311111257",size:"2916002",format:"mp3",url:"http://en.youjiaotong.com/yylmp3/mp3_word/B59E57FD261506BCDDF108EADBF2F456.mp3"}]
  downloadJson = "[";
  if (style == 1) {
    $.each(textJson.kewen, function(k, v) {
      var contentDemo = $("#demo")
        .children("div")
        .clone();
      contentDemo
        .find(".ywBook")
        .append(
          v.text.replace(/img src="/g, 'img style="width:100%" src="' + respath)
        );
      //contentDemo.find('.ywBook').append('<div style="height:25px;"></div>');
      contentDemo.appendTo("#iScroll-bd");
      textMp3Arr[k] = v.url;
      textTitleArr[k] = v.title;
      textAuthorArr[k] = v.r_code;
      var arr = new Array();
      arr["style"] = style;
      arr["isShowExPlains"] = 0;
      arr["title"] = v.title;
      textExplainsArr[k] = arr;
      if (v.url != "") {
        downloadJson +=
          '{"name":"' +
          v.url.getBaseName() +
          '","size":"10","format":"mp3","url":"' +
          kewenNetUrl +
          v.url +
          '"},';
      }
    });
  } else if (style == 2) {
    $.each(textJson.kewen, function(k, v) {
      var contentDemo = $("#demo")
        .children("div")
        .clone();
      // contentDemo.find('.textTitle').html(v.title);
      contentDemo
        .find(".ywBook")
        .append(
          v.text.replace(/img src="/g, 'img style="width:100%" src="' + respath)
        );
      contentDemo.appendTo("#iScroll-bd");
      textMp3Arr[k] = v.url;
      textTitleArr[k] = v.title;
      textAuthorArr[k] = v.r_code;
      var arr = new Array();
      arr["style"] = style;

      v.explains.length > 0
        ? (arr["isShowExPlains"] = 1)
        : (arr["isShowExPlains"] = 0);
      arr["explains"] = v.explains;
      arr["title"] = v.title;
      textExplainsArr[k] = arr;
      if (v.url != "") {
        downloadJson +=
          '{"name":"' +
          v.url.getBaseName() +
          '","size":"10","format":"mp3","url":"' +
          kewenNetUrl +
          v.url +
          '"},';
      }
    });
  } else if (style == 3) {
    var len = textJson.kewen.length;
    var pinyinHtml = "";
    $.each(textJson.pinyin, function(k, v) {
      if (k == 0) {
        pinyinHtml +=
          '<a class="btnY03 radius100 on" onclick="showPinyin(this,' +
          k +
          ');">' +
          v.pinyin +
          "</a>";
      } else {
        pinyinHtml +=
          '<a class="btnY03 radius100" onclick="showPinyin(this,' +
          k +
          ');">' +
          v.pinyin +
          "</a>";
      }

      var infoJson = eval("(" + v.info + ")");
      $.each(infoJson.read, function(kk, vv) {
        var strs = new Array(); //定义一数组
        strs = vv.v.split("|"); //字符分割
        for (i = 0; i < strs.length; i++) {
          downloadJson +=
            '{"name":"' +
            strs[i].getBaseName() +
            '","size":"10","format":"mp3","url":"' +
            pinyinNetUrl +
            strs[i] +
            '"},';
        }
      });
    });
    $(".tabQ").append(pinyinHtml);
    showPinyin(
      $(".tabQ")
        .find("a")
        .eq(0),
      0
    );
    if (len > 0) {
      $("#istab").show();
      $("#contentInfo").html(
        textJson.kewen[0].text.replace(
          /img src="/g,
          'img style="width:100%" src="' + respath
        )
      );
      // if(textJson.kewen[0].url==''||typeof(textJson.kewen[0].url)=='undefined'){
      //     $('.bottom').find('span').eq(1).children('a').eq(0).addClass('btnGray');
      //     $('.bottom').find('i').eq(1).attr('url',textJson.kewen[0].url);
      // }else{
      //     $('.bottom').find('span').eq(1).children('a').eq(0).removeClass('btnGray');
      //     $('.bottom').find('i').eq(1).attr('url',textJson.kewen[0].url);
      // }
      // $('.icon-playk').attr('texttitle',textJson.kewen[0].title);
      $("#content").show();
      textMp3Arr[1] = textJson.kewen[0].url;
      textTitle = textJson.kewen[0].title;
      TouchSlide({
        slideCell: "#leftTabBox",
        endFun: function(i, c) {
          if (i > 0) {
            //显示控件
            showBar(i);
          } else {
            //隐藏控件
            try {
              UXinJSInterfaceSpeech.hideVoiceView(1);
            } catch (e) {
              setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
            }
          }
        }
      });
      if (textJson.kewen[0].url != "") {
        downloadJson +=
          '{"name":"' +
          textJson.kewen[0].url.getBaseName() +
          '","size":"10","format":"mp3","url":"' +
          kewenNetUrl +
          textJson.kewen[0].url +
          '"},';
      }
    } else {
      $("#tab").hide();
      $("#content").hide();
    }
  }

  $(".icon-ico-yuyin").attr("ks_code", ks_code);
  $(".icon-uniE609").attr("ks_code", ks_code);
  downloadJson = downloadJson.trimStr(",");
  downloadJson += "]";

  if (typeof UXinJSInterfaceSpeech.hideVoiceView === "function") {
    setTimeout("downloadAudio()", 500);
  } else {
    //alert('no');
  }
}

function showPinyin(obj, k) {
  $(obj)
    .siblings()
    .removeClass("on");
  $(obj).addClass("on");
  var infoJson = eval("(" + textJson.pinyin[k].info + ")");
  $("#bishun").attr(
    "src",
    resource + "/uploadsyw/pinyinpic/bishun/" + infoJson.bishun.pic
  );
  $("#write").attr(
    "src",
    resource + "/uploadsyw/pinyinpic/write/" + infoJson.write.pic
  );
  var readHtml = "";
  $.each(infoJson.read, function(kr, vr) {
    if (vr.v.indexOf("|") >= 0) {
      var objclass = "img100";
    } else {
      var objclass = "img30";
    }
    readHtml +=
      '<img src="' +
      resource +
      "/uploadsyw/" +
      vr.pic +
      '" class="' +
      objclass +
      '" onclick="play(this);" url="' +
      vr.v +
      '" />';
  });
  $("#read").html(readHtml);
}

/**
 * 检查是否展示翻译按钮（有则存入LocalStorage）
 */
function checkExplains(i) {
  removeLocalStorage("cn_title");
  removeLocalStorage("cn_explains");
  if (textExplainsArr[i]["style"] == 1 || textExplainsArr[i]["style"] == 3) {
    if (textAuthorArr[i] == "" || typeof textAuthorArr[i] == "undefined") {
      $("#fanyi").hide();
      $("#tta").hide();
      $(".tTan").hide();
    } else {
      $("#fanyi")
        .children("span")
        .eq(0)
        .html("作者简介");
      $("#fanyi").attr("r_code", textAuthorArr[i]);
      $("#fanyi").show();
      $("#tta").hide();
      $(".tTan").hide();
    }
  } else {
    if (textExplainsArr[i]["isShowExPlains"]) {
      //有翻译
      setLocalStorage(
        "cn_explains",
        encodeURI(JSON.stringify(textExplainsArr[i]["explains"]))
      );
      setLocalStorage("cn_title", textExplainsArr[i]["title"]);

      if (textAuthorArr[i] == "" || typeof textAuthorArr[i] == "undefined") {
        $("#fanyi")
          .children("span")
          .eq(0)
          .html("翻译");
        // $('#fanyi').prop('href','explains?ks_code='+ks_code+'&index='+index);
        $("#fanyi").attr("ks_code", ks_code);
        $("#fanyi").attr("index", index);
        $("#fanyi").show();
        $("#tta").hide();
        $(".tTan").hide();
      } else {
        $("#fanyi").hide();
        $("#tta").show();
        $(".tTan")
          .find("li")
          .eq(0)
          .attr("ks_code", ks_code);
        $(".tTan")
          .find("li")
          .eq(0)
          .attr("index", index);
        $(".tTan")
          .find("li")
          .eq(1)
          .attr("r_code", textAuthorArr[i]);
      }
    } else {
      //没有翻译
      if (textAuthorArr[i] == "" || typeof textAuthorArr[i] == "undefined") {
        //没有作者简介
        $("#fanyi").hide();
        $("#tta").hide();
        $(".tTan").hide();
      } else {
        //有简介
        $("#fanyi")
          .children("span")
          .eq(0)
          .html("作者简介");
        $("#fanyi").attr("r_code", textAuthorArr[i]);
        $("#fanyi").show();
        $("#tta").hide();
        $(".tTan").hide();
      }
    }
  }
}

/**
 *跳转到翻译还是作者简介
 */
function goExplainsOrAuthor(obj) {
  var ks_code_in = $(obj).attr("ks_code");
  var index_in = $(obj).attr("index");
  var r_code = $(obj).attr("r_code");
  var userName = $("username").html();
  var plsip = $("#plsip").html();
  $(".tTan").hide();
  try {
    UXinJSInterfaceSpeech.stopAudio();
    // UXinJSInterfaceSpeech.hideVoiceView(1);
  } catch (e) {
    setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
  }

  if (typeof $(obj).attr("ks_code") == "undefined") {
    //作者简介
    $.getJSON(
      window.location.protocol+"//" + plsip + "/youjiao/doMutiplePlay.do?jsoncallback=?",
      {
        rcode: r_code,
        userName: userName,
        filterType: 2,
        outType: 1
      },
      function(data) {
        if (typeof data.jsonList[0] == "undefined" || data.jsonList[0] == "") {
          setTip("暂无简介");
        } else {
          location.href = data.jsonList[0].list[0].path;
        }
      }
    );
  } else {
    //翻译
    try {
      UXinJSInterfaceSpeech.hideVoiceView(1);
    } catch (e) {
      setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
    }
    location.href = "explains?ks_code=" + ks_code_in + "&index=" + index_in;
  }
}
/**
 * 获取字的基础信息
 * @param  {[Sting]} zi  [字]
 * @param  {[Sting]} py  [拼音]
 * @param  {[Sting]} fy  [发音]
 * @param  {[Obj]} obj [对象]
 */
function getZi(zi, py, fy, obj) {
  $("#textplay").attr("isfirst", "1");
  if ($(obj).parent()[0].tagName == "S") {
    //有下划线
    return false;
  } else {
    // 获取字的信息_没有下划线
    showZi(zi, py, fy, obj);
  }
}

/**
 * 弹出框（字的详细信息）
 * @param  {[Sting]} zi  [字]
 * @param  {[Sting]} py  [拼音]
 * @param  {[Sting]} fy  [发音]
 * @param  {[Obj]} obj [对象]
 */
function showZi(zi, py, fy, obj) {
  $("#zi").html(zi);
  $("#py").html(py);
  $("#fy").attr("url", fy);
  $.get(
    "../Text/getZi",
    {
      zi: zi,
      fy: fy
    },
    function(data) {
      $("#bushou").html(data[0].bushou);
      $("#wordlist").html(data[0].cizu);
    }
  );
  // $('obj').find('a').eq(0).unbind('click').click(function(){
  // 	var url = $(this).attr('url');
  //     alert(url);return false;
  //     mp3 = new player(url+'.mp3','zi');
  //     mp3.playNet();
  // })
  mui("#popover_zi").popover("show", obj);
}

/**
 * 获取词的内容，并选择弹出框类型（1：只有下划线；2：下划线+变色）
 * @param  {[Obj]} obj [对象]
 */
function showExplain(obj) {
  $("#textplay").attr("isfirst", "1");
  var explain = $(obj).attr("explain");
  var word = $(obj).html();
  if ($(obj).find("font").length > 0) {
    //下划线+变色
    var ziObj = $(obj)
      .find("font")
      .eq(0);
    var zi = ziObj.html();
    var py = ziObj.attr("py");
    var fy = ziObj.attr("fy");
    zi = zi.replace(/<\/?[^>]*>/g, "");
    word = word.replace(/<\/?[^>]*>/g, "");
    $("#popover_select")
      .find("font")
      .eq(0)
      .html(zi);
    $("#popover_select")
      .find("font")
      .eq(1)
      .html(word);
    $("#popover_select")
      .find("dd")
      .eq(0)
      .unbind("click")
      .click(function() {
        mui("#popover_select").popover("hide");
        $.each(obj.childNodes, function(k, v) {
          if (v.tagName == "FONT") {
            showZi(zi, py, fy, obj.childNodes[k]);
          }
        });
      });
    $("#popover_select")
      .find("dd")
      .eq(1)
      .unbind("click")
      .click(function() {
        mui("#popover_select").popover("hide");
        showWord(obj);
      });
    mui("#popover_select").popover("show", obj);
  } else {
    //只有下划线
    showWord(obj);
  }
}

/**
 * 弹出框（词的弹出框）
 * @param  {[Obj]} obj [对象]
 */
function showWord(obj) {
  var explain = $(obj).attr("explain");
  var word = $(obj).html();
  word = word.replace(/<\/?[^>]*>/g, "");
  $("#popover_word")
    .find("h2")
    .html(word);
  $("#popover_word")
    .find("p")
    .html(explain);
  mui("#popover_word").popover("show", obj);
}

/**
 * 获取字的详细信息
 */
function getWordInfo(wordinfo, fy) {
  wordinfo_p = wordinfo;
  $.ajax({
    url: "../Text/getWordInfo",
    data: {
      ran: Math.random(),
      wordinfo: wordinfo
    },
    // datatype: "json",
    type: "get",
    async: false,
    success: function(data) {
      //成功后回调
      $("#scroller .pad10").remove();
      $("#scroller .h50").remove();
      var wordDemo = $("#wordDemo")
        .children()
        .clone();
      var zi = data[0].zi;
      var bushou = data[0].bushou;
      var jiegou = data[0].jiegou;
      var zongbihua = data[0].zongbihua;
      if (jiegou == 1) {
        jiegou = "独体字";
      } else if (jiegou == 2) {
        jiegou = "上下";
      } else if (jiegou == 3) {
        jiegou = "上中下";
      } else if (jiegou == 4) {
        jiegou = "左右";
      } else if (jiegou == 5) {
        jiegou = "左中右";
      } else if (jiegou == 6) {
        jiegou = "半包围";
      } else if (jiegou == 7) {
        jiegou = "全包围";
      } else if (jiegou == 8) {
        jiegou = "嵌套";
      } else {
        jiegou = "品字体";
      }
      // $('#zi').html(zi);
      // $('#bs').html(bushou);
      // $('#jg').html(jiegou);
      // $('#bh').html(zongbihua);

      wordDemo.find("#zi").html(zi);
      wordDemo.find("#bs").html(bushou);
      wordDemo.find("#jg").html(jiegou);
      wordDemo.find("#bh").html(zongbihua);

      var html = "";
      var html_other = "";
      var pyHtml = "";
      downloadJson = "[";
      if (data[0].id != 0) {
        //在lib_zi和lib_cixing表都存在
        $.each(data, function(k, v) {
          var cizu = v.cizu;
          var py = v.zi_pinyin;
          var cizuArr = new Array();
          cizuArr = cizu.split("#");
          if (v.py == fy) {
            html += '<dl class="ywCi clear">';
            html +=
              '<dd class="ywCidd"  py="' +
              v.py +
              '" onclick="play(this);"><span class="greenFont">' +
              v.zi_pinyin +
              '</span><a class="greenFont"><i class="icon-uniE60C"></i></a></dd>';
          } else {
            html_other += '<dl class="ywCi clear">';
            html_other +=
              '<dd class="ywCidd"  py="' +
              v.py +
              '" onclick="play(this);"><span class="greenFont">' +
              v.zi_pinyin +
              '</span><a class="greenFont"><i class="icon-uniE60C"></i></a></dd>';
          }
          downloadJson +=
            '{"name":"' +
            v.py.trim() +
            '","size":"10","format":"mp3","url":"' +
            baseNetUrl +
            "uploadsyw/zipinyin/" +
            v.py.trim() +
            '.mp3"},';
          if (cizu !== "") {
            $.each(cizuArr, function(kk, vv) {
              if (kk > 2) {
                return false;
              }
              if (v.py == fy) {
                html +=
                  '<dd class="ywCidd"><a onclick="getCiInfo(\'' +
                  vv +
                  '\');" class="btnYw" wordtype="2" wordinfo="' +
                  vv +
                  '" backurl="' +
                  backurl +
                  '">' +
                  vv +
                  "</a></dd>";
              } else {
                html_other +=
                  '<dd class="ywCidd"><a onclick="getCiInfo(\'' +
                  vv +
                  '\');" class="btnYw" wordtype="2" wordinfo="' +
                  vv +
                  '" backurl="' +
                  backurl +
                  '">' +
                  vv +
                  "</a></dd>";
              }
            });
          } else {
            //没有组词
            if (v.py == fy) {
              html += '<dd class="ywCidd"><a class="btnYw" >暂无组词</a></dd>';
            } else {
              html_other +=
                '<dd class="ywCidd"><a class="btnYw" >暂无组词</a></dd>';
            }
          }
          if (v.py == fy) {
            html += "</dl>";
          } else {
            html_other += "</dl>";
          }
          // pyHtml += '<b class="bgfont" py="'+v.py+'" onclick="play(this);">【<font>'+py+'</font>】<a><i class="icon-uniE60C"></i></a></b>';
          if (v.py == fy) {
            pyHtml +=
              '<b class="bgfont" py="' +
              v.py +
              '" onclick="play(this);">【<font>' +
              py +
              '</font>】<a><i class="icon-uniE60C"></i></a></b>';
          }
        });
      } else {
        //lib_cixing表中为空，py用lib_zi表中的(这种情况后续排除)
        var pys = eval("(" + data[0].pys + ")");
        $.each(pys.zy, function(k, v) {
          // if(v.v == fy){
          pyHtml +=
            '<b class="bgfont" py="' +
            v.v +
            '" onclick="play(this);">【<font>' +
            v.y +
            '</font>】<a><i class="icon-uniE60C"></i></a></b>';
          // return false;
          // }
          downloadJson +=
            '{"name":"' +
            v.v.trim() +
            '","size":"10","format":"mp3","url":"' +
            baseNetUrl +
            "uploadsyw/zipinyin/" +
            v.v.trim() +
            '.mp3"},';
        });
      }

      downloadJson = downloadJson.trimStr(",");
      downloadJson += "]";
      setTimeout("downloadAudio()", 500);
      var zigif =
        '<img id="img" style="margin:0 auto;" src="' +
        resource +
        "uploadklx/klxsz/uploads/zigif/" +
        zi +
        ".gif" +
        '" class="img60"/>';
      // var zigif = '<img id="img" style="margin:0 auto;" src="http://192.168.133.11/yw/uploadsyw/zigif/%E5%95%8A.gif " class="img60"/>';
      if (data.length > 1) {
        //多音字
        wordDemo.find("#duoyin").html(html_other);
      } else {
        wordDemo.find("#other").remove();
      }

      wordDemo.find("#info").html(html);
      wordDemo.find("#zigif").append(zigif);
      wordDemo.find("#py").html(pyHtml);
      wordDemo.find("#kname").html(ks_name);
      $("#scroller").append(wordDemo);
      checkNowNum(wordinfo);
      // $("img").onload = function() {
      //   alert("x");
      //   $("#wrapper").resize();
      // };
      imgLoad(img, function() {
        $("#wrapper").resize();
      });
    },
    error: function(e) {
      //失败后回调
      // alert(e);
    },
    beforeSend: function() {
      //发送请求前调用，可以放一些"正在加载"之类额话
      // alert("正在加载");
    },
    complete: function() {}
  });
}
function imgLoad(img, callback) {
  var timer = setInterval(function() {
    if (img.complete) {
      callback(img);
      clearInterval(timer);
    }
  }, 50);
}
/**
 * 获取词的详细信息
 */
function getCiInfo(wordinfo) {
  wordinfo_p = wordinfo;
  $.ajax({
    url: "../Text/getCiInfo",
    data: {
      ran: Math.random(),
      wordinfo: wordinfo
    },
    // datatype: "json",
    type: "get",
    async: false,
    success: function(data) {
      //成功后回调
      $("#scroller .pad10").remove();
      $("#scroller .h50").remove();
      var wordDemo = $("#ciDemo")
        .children()
        .clone();
      var ci = data.ci;
      var ciHtml = "";
      $.each(ci, function(k, v) {
        ciHtml += '<span class="zi">' + v + "</span>";
      });
      // $('.marBottom10').html(ciHtml);
      wordDemo.find(".marBottom10").html(ciHtml);
      var tongyici = data.base[0].tongyici;
      var fanyici = data.base[0].fanyici;
      ciJson = eval("(" + data.base[0].pys + ")");

      if (tongyici.length == 0 && fanyici.length == 0) {
        // $('#ciyu').hide();
        wordDemo.find("#ciyu").hide();
      } else {
        if (tongyici.length == 0) {
          // $('#tongyici').hide();
          wordDemo.find("#tongyici").hide();
        } else {
          var tongyiciHtml = "";
          $.each(tongyici, function(k, v) {
            tongyiciHtml +=
              '<dd class="ywCidd"><a href="javascript:showci(\'' +
              v +
              '\');" class="btnYw">' +
              v +
              "</a></dd>";
          });
          wordDemo
            .find("#tongyici")
            .find("dl")
            .html(tongyiciHtml);
          wordDemo.find("#tongyici").show();
          // $('#tongyici').find('dl').html(tongyiciHtml);
          // $('#tongyici').show();
        }

        if (fanyici.length == 0) {
          wordDemo.find("#fanyici").hide();
          // $('#fanyici').hide();
        } else {
          var fanyiciHtml = "";
          $.each(fanyici, function(k, v) {
            fanyiciHtml +=
              '<dd class="ywCidd"><a href="javascript:showci(\'' +
              v +
              '\');" class="btnYw">' +
              v +
              "</a></dd>";
          });
          wordDemo
            .find("#fanyici")
            .find("dl")
            .html(fanyiciHtml);
          wordDemo.find("#fanyici").show();
          // $('#fanyici').find('dl').html(fanyiciHtml);
          // $('#fanyici').show();
        }
      }
      downloadJson = "[";
      var pyHtml = " ";
      var fyHtml = "#";
      $.each(ciJson.zy, function(k, v) {
        pyHtml += v.y.trim() + " ";
        fyHtml += v.v.trim() + "#";
        downloadJson +=
          '{"name":"' +
          v.v.trim() +
          '","size":"10","format":"mp3","url":"' +
          baseNetUrl +
          "uploadsyw/zipinyin/" +
          v.v.trim() +
          '.mp3"},';
      });
      downloadJson = downloadJson.trimStr(",");
      downloadJson += "]";
      // console.log(downloadJson);
      setTimeout("downloadAudio()", 500);
      pyHtml = pyHtml.trim();
      fyHtml = fyHtml.trim3();
      wordDemo.find("#py").html(pyHtml);
      wordDemo
        .find("#py")
        .siblings("i")
        .attr("fy", fyHtml);
      wordDemo.find("#kname").html(ks_name);
      $("#scroller").append(wordDemo);
      checkNowNum(wordinfo);
      $("#wrapper").resize();
    },
    error: function(e) {
      //失败后回调
      // alert(e);
    },
    beforeSend: function() {
      //发送请求前调用，可以放一些"正在加载"之类额话
      // alert("正在加载");
    },
    complete: function() {}
  });
}

function showci(wordinfo) {
  getCiInfo(wordinfo);
}

//播放课文
function playKewen(obj) {
  var url = $(obj).attr("url");
  if (url == "") {
    setTip("暂无音频");
    return false;
  }
  var isfirst = $(obj).attr("isfirst");
  if ($(obj).hasClass("icon-playk")) {
    //开始播放
    $(obj).prop("class", "icon-playt");
    if (isfirst == 1) {
      //第一次播放
      mp3 = new player(url, "kewen");
      mp3.playSingle();
      // $('.huakuai').show();
      // $('.rang_width').css('width','0px');
      // mp3.playAudioAtTime(url.getBaseName(),0);
      // go();
      $(obj).attr("isfirst", "0");
    } else {
      //暂停后继续播放
      mp3.resumeAudio();
      // goOn();
    }
  } else if ($(obj).hasClass("icon-playt")) {
    //暂停播放
    $(obj).prop("class", "icon-playk");
    mp3.pauseAudio();
    // clearTimeout(t_jindu);
  }
}

//试听录音
function listenVoice(obj) {
  var isfirst = $(obj).attr("isfirst");
  if ($(obj).hasClass("icon-playk")) {
    //开始播放
    $(obj).prop("class", "icon-playt");
    if (isfirst == 1) {
      recordPathType = 1;
      //第一次播放
      record.listenVoice();
      $(obj).attr("isfirst", "0");
    } else {
      //暂停后继续播放
      record.resumeAudio();
    }
  } else if ($(obj).hasClass("icon-playt")) {
    //暂停播放
    $(obj).prop("class", "icon-playk");
    record.pauseAudio();
  }
}
//上传录音
function uploadVoice() {
  recordPathType = 2;
  record.uploadVoice();
  mp3.stopAudio();
}

function uploadVoiceToDb(path) {
  // var filename = $('.icon-playk').attr('texttitle');
  var filename = textTitle;
  $.get(
    "../Text/uploadVoiceToDb",
    {
      ran: Math.random(),
      path: path,
      ks_code: ks_code,
      filename: filename,
      recordType: recordType,
      apptype: 1,
      isPush: 0
    },
    function(data) {
      setTip(data.msg);
      closeView();
      $("i[name=playbutton]")
        .prop("class", "icon-playk actY")
        .attr("isfirst", 1);
    }
  );
}

function showTab() {
  maskFlag = true;
  $("i[name=playbutton]")
    .prop("class", "icon-playk actY")
    .attr("isfirst", 1);
  mp3.stopAudio();
  $("#mask").show();
  $(".tanDb").show();
}

function maskClick(obj) {
  if (maskFlag) {
    $(obj).hide();
    $(".tanDb").hide();
  }
}

function closeClick() {
  UXinJSInterfaceSpeech.stopAudio();
  closeView();
}
//关闭弹窗
function closeView() {
  $(".bottomTan").hide();
  $("#mask").hide();
  $("#bt03").hide();
  $(".overY").css("margin-bottom", "60px");
  $(".bottom").css("height", "60px");
  // $('.ywBook').removeClass('row');
  checkRow(2);
  $("#masG02").hide();
  $(".tts").hide();
  $("#bt01").show();
  $("#shiting").attr("isfirst", "1");
  $("#timestr").attr("bnum", "0");
}

//开始背诵
function bei(type) {
  recordIsComplete = false;
  $("#masG02").css("bottom", "160px");
  maskFlag = false;
  recordType = type;
  $("#mask").hide();
  $(".tanDb").hide();
  $("#bt01").hide();
  $(".overY").css("margin-bottom", "100px");
  $(".bottom").css("height", "100px");
  $("#bt02").show();

  setTimeout(function() {
    luyy();
  }, 1000);
}

//type:1,add;2,remove
function checkRow(type) {
  if (isAndroid) {
    if (androidVersion < 4.3) {
      // 版本小于4.3的事情
      if (type == 1) {
        $(".ywBook").addClass("row1"); //加模糊的，需要就加，不需要不加
		$("#masG02").show();
		$(".tts").show();
      } else {
        $(".ywBook").removeClass("row1");
		$("#masG02").hide();
		$(".tts").hide();
      }
    } else {
      if (type == 1) {
        $(".ywBook").addClass("row"); //加模糊的，需要就加，不需要不加
		$("#masG02").show();
		$(".tts").show();
      } else {
        $(".ywBook").removeClass("row");
		$("#masG02").hide();
		$(".tts").hide();
      }
    }
  } else {
    if (type == 1) {
      $(".ywBook").addClass("row"); //加模糊的，需要就加，不需要不加
	  $("#masG02").show();
	  $(".tts").show();
    } else {
      $(".ywBook").removeClass("row");
	  $("#masG02").hide();
	  $(".tts").hide();
    }
  }
}

//进入录音
function luyy() {
  $("#bt02").hide();
  $(".overY").css({
    "margin-bottom": "150px"
  });
  $(".bottom").css("height", "150px");
  //变成暂停按钮和样式
  $(".ywBook").addClass("pauseClass");
  $("#zanting").prop("class", "icon-playt");
  if (recordType == 1) {
    // $('.ywBook').addClass('row');//加模糊的，需要就加，不需要不加
    checkRow(1);
    $("#masG02").show();
    $(".tts").show();
  }
  $(".CCyin")
    .find("div")
    .attr("style", "animation-play-state: running;");
  $("#bt03").show();

  record.recordReset(); //重置录音状态
  record.recordVoice();
  showTime();
}
//暂停录音
function zanting() {
  if ($(".ywBook").hasClass("pauseClass")) {
    //暂停
    record.pauseVoice();
    clearTimeout(t_record);
    // $('#ttt').html('暂停');
    $(".ywBook").removeClass("pauseClass");
    // $('.ywBook').removeClass('row');
    checkRow(2);
    $("#masG02").hide();
    $(".CCyin")
      .find("div")
      .removeAttr("style");
    $(".tts").hide();
    $("#zanting").prop("class", "icon-ico-yuyin");
  } else {
    //开始或继续
    record.recordVoice();
    showTime();
    $("#zanting").prop("class", "icon-playt");
    $(".ywBook").addClass("pauseClass");
    if (recordType == 1) {
      // $('.ywBook').addClass('row');
      checkRow(1);
      $("#masG02").show();
      $(".tts").show();
    }
    $(".CCyin")
      .find("div")
      .attr("style", "animation-play-state: running;");
    // $('#ttt').html('继续');
  }
}
//放弃录音弹窗
function fangqi() {
  if ($(".ywBook").hasClass("pauseClass")) {
    record.pauseVoice();
    $(".ywBook").removeClass("pauseClass");
  }
  clearTimeout(t_record);
  // $('.ywBook').removeClass('row');
  checkRow(2);
  $("#masG02").hide();
  $(".CCyin")
    .find("div")
    .removeAttr("style");
  $(".tts").hide();
  $("#zanting").prop("class", "icon-ico-yuyin");
  $("#mask").show();
  $(".dialog2").show();
}
//放弃
function fangqiVoice() {
  $("#mask").hide();
  $(".dialog2").hide();
  $("#bt03").hide();
  $(".overY").css("margin-bottom", "60px");
  $(".bottom").css("height", "60px");
  // $('.ywBook').removeClass('row');
  checkRow(2);
  $("#masG02").hide();
  $(".CCyin")
    .find("div")
    .removeAttr("style");
  $(".tts").hide();
  $("#bt01").show();
  $("#timestr").attr("bnum", "0");
}
//取消不放弃
function continuVoice() {
  $("#mask").hide();
  $(".dialog2").hide();
}

//完成录音
function chengg() {
  // $('#ttt').html('暂停');
  //如果没有停止，就先停止
  recordIsComplete = true;
  clearTimeout(t_record);
  if ($(".ywBook").hasClass("pauseClass")) {
    record.pauseVoice();
    $(".ywBook").removeClass("pauseClass");
    $(".CCyin")
      .find("div")
      .removeAttr("style");
  } else {
    completeRecord(recordIsComplete);
  }

  // if(recordIsComplete){
  //     $('.overY').css('margin-bottom','280px');
  //     $('.tts').hide();
  //     checkRow(2);
  //     $('#mask').show();
  //     $('.bottomTan').show();
  //     $('#timestr').attr('bnum','0');
  // }
}

function showTime() {
  timeNum = $("#timestr").attr("bnum");
  timeNum++;
  $("#timestr").attr("bnum", timeNum);
  $("#timestr").html(formatTime(timeNum));
  t_record = setTimeout("showTime();", 1000);
}

function formatTime(str) {
  var minute = parseInt(str / 60);
  var second = str % 60;
  if (minute < 10) {
    minute = "0" + minute;
  }
  if (second < 10) {
    second = "0" + second;
  }

  return minute + ":" + second;
}

//进入我的录音页面
function goMyrecord(obj) {
  UXinJSInterfaceSpeech.stopAudio();
  var ks_code = $(obj).attr("ks_code");
  var backurl = $(obj).attr("backurl");
  location.href =
    "myrecord?ks_code=" + ks_code + "&index=" + index + "&backurl=" + backurl;
}

//展示控件1
//openAudioView(type,voices,progresType,url)
//type:1，控件1；2，控件2
//voices:json字符串[{"url":"http://...."}]
//progresType: 1,显示进度条,0不显示进度条
//url：要跳转的地址
function showBar(i) {
  var domain = window.location.host;
  if (barType == 1) {
    //课文
    var moduleid = 10;
  } else if (barType == 2) {
    //古文
    var moduleid = 12;
  } else if (barType == 3) {
    //拼音
    var moduleid = 10;
  }
  var url =
    window.location.protocol+"//" +
    domain +
    "/Yuwen/Text/recordrank?ks_code=" +
    ks_code +
    "&ks_name=" +
    encodeURI(encodeURI(ks_name)) +
    "&index=" +
    index +
    "&apptype=1&moduleid=" +
    moduleid;
  if (textMp3Arr[i] == "" || typeof textMp3Arr[i] == "undefined") {
    var voices = "[]";
  } else {
    var voices = '[{"url":"' + kewenNetUrl + textMp3Arr[i] + '"}]';
    // var voices = '[{"url":"'+kewenNetUrl+textMp3Arr[i]+'"},{"url":"'+kewenNetUrl+textMp3Arr[i]+'"},{"url":"'+kewenNetUrl+textMp3Arr[i]+'"},{"url":"'+kewenNetUrl+textMp3Arr[i]+'"}]';
  }
  $.ajax({
    url: "../Text/getPushNum",
    type: "get",
    data: {
      ran: Math.random(),
      ks_code: ks_code
    },
    dataType: "json",
    success: function(data) {
      try {
        UXinJSInterfaceSpeech.hideVoiceView(1);
        UXinJSInterfaceSpeech.openAudioView(
          1,
          voices,
          1,
          url,
          data.pushnum * 1
        );
      } catch (e) {
        setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
      }
    },
    error: function(e) {}
  });
}

//优信调用此函数
function blurToggle(type, stateType) {
  // alert(type + "/" + stateType);
  recordType = type;
  if (type == 1) {
    //背诵
    if (stateType == 1) {
      //增加模糊
      checkRow(1);
    } else {
      //取消模糊
      checkRow(2);
    }
  } else {
    //朗读
    checkRow(2);
  }
}

//当调用openAudioView函数的时候，优信回调这个函数传递高度
function getWidgetHeight(height) {
  // alert('控件高度:'+height);
  barHeight = height;
  changeTextHeight(height);
}

function changeTextHeight(height) {
  //课文或者古文
  var bd = document.getElementById("iScroll-bd");
  if (
    $(window).height() - 84 - height <
    bd.children[kewenNum].children[0].offsetHeight
  ) {
    bd.parentNode.style.height =
      bd.children[kewenNum].children[0].offsetHeight + 80 + "px"; //越大下面空白越大
    bd.style.height =
      bd.children[kewenNum].children[0].offsetHeight + 80 + "px";
    bd.children[kewenNum].height =
      bd.children[kewenNum].children[0].offsetHeight + 80 + "px";
    if (i > 0) bd.parentNode.style.transition = "200ms"; //添加动画效果
  } else {
    bd.parentNode.style.height = $(window).height() - 84 - height + "px";
    // bd.parentNode.style.border = "solid";
    bd.style.height = $(window).height() - 84 - height + "px";
    bd.children[kewenNum].height = $(window).height() - 84 - height + "px";
    // bd.children[kewenNum].style.border = "solid";
    // alert(bd.children[kewenNum].height);
  }
  $(".overY").css("margin-bottom", height + "px");
  window.scroll(0, 0);
}
