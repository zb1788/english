var textreadarr={};
var batchid=0;
//音频MP3
var mp3=function(){
    this.curindex=0;
    this.mp3list=[];
    this.addclass="";
    this.removeclass="";
    this.time=1000;
    this.obj="";
    this.timer="";
    //播放参数的初始化
    this.playInit=function(playlist,addclass,removeclass,time,obj){
        this.mp3list = playlist;
        console.log(this.mp3list);
        this.addclass = addclass;
        this.removeclass = removeclass;
        this.time = time;
        this.obj = obj;
    };
    this.reset=function(){
      this.curindex=0;
    };
    this.setPlayTime=function(time){
      this.time = time;
    };
    this.addCurindex=function(){
      this.curindex=this.curindex+1;
    };
    this.reSetPlayList=function(){
        this.mp3list = [];
        try{
            clearTimeout(this.timer);
        }catch(e){
            //setTip("请升级到最新的优信");
        }
    };
    //去掉样式
    this.removeCurClass=function()
    {
        // if(this.removeclass!=this.addclass){
        //     $(this.obj).eq(this.curindex).addClass(this.removeclass).removeClass(this.addclass);
        // }

    };
    //去掉样式
    this.addCurClass=function()
    {
        // if(this.removeclass!=this.addclass){
        //     $(this.obj).removeClass(this.removeclass).addClass(this.addclass);
        // }

    };
    //播放列表
    this.playWordList=function(){
      //播放音频
        var self=this;
        if(self.curindex==0){
          try{
            self.addCurClass();
            self.removeCurClass();
            var mp3name=self.mp3list[self.curindex].mp3;
            UXinJSInterfaceSpeech.playAudio(mp3name);
          }catch(e){
              console.log(mp3name);
              //onAudioPlayStatus(0);
          }
        }else if(self.curindex==self.mp3list.length){
          clearTimeout(self.timer);
        }else{

          self.timer=setTimeout(function(){
            try{
              self.addCurClass();
              self.removeCurClass();
              var mp3name=self.mp3list[self.curindex].mp3;
              UXinJSInterfaceSpeech.playAudio(mp3name);
            }catch(e){
              console.log(mp3name);
              //onAudioPlayStatus(0);
            }
          },self.time);
        }
    };
    //停止播放
    this.stopPlayWordList=function(){
        //表示从第几个开始播放
        this.mp3list = [];
        try{
            clearTimeout(this.timer);
        }catch(e){
            //setTip("请升级到最新的优信");
        }
        try{
          UXinJSInterfaceSpeech.stopAudio();
        }catch(e){

        }
    };
    this.setPlaylist=function(data){
      this.mp3list=data;
    };
}
var mp3=new mp3();
var onAudioPlayStatus=function(status){
    if(status == 0){
      mp3.addCurindex();
      mp3.playWordList();
    } else {

    }
}
//判断是否支持webp图片

var checkWebp = function() {
    var d = document;
    var supportWebp;
    try {
        var ele = d.createElement('object');
        ele.type = 'image/webp';
        ele.innerHTML = '!';
        d.body.appendChild(ele);
        //奇妙所在,如果浏览器支持webp,那么这个object是不可见的(offsetWidth为0),
        //否则就会显示出来,有可视宽度.
        supportWebp = !ele.offsetWidth;
        d.body.removeChild(ele);
    }catch (err) {
        supportWebp = false;
    }
    return supportWebp;
}
var iswebp=checkWebp();
//关卡日志
function setUserLog(data,url){
  var logurl="../../Subject/Word/setUserLearnRecord";
  $.ajax({url:logurl,type:'post',data:{request:JSON.stringify(data),ran:Math.random()},
    success:function(data){
      batchid=data.batchid;
      window.location.href=url;
    },
    error:function(){

    }
  });
}

//当前关卡结束
function setUserOver(type,ks_code){
  var logurl="../../Subject/Word/setUserLearnOver";
  $.ajax({url:logurl,type:'post',data:{isover:"1",type:type,ks_code:ks_code,batchid:batchid,ran:Math.random()}});
}

//记录单词学习记录
function recordData(data){
  var logurl="../../Subject/Word/setUserWordReadRecord";
  $.ajax({url:logurl,type:'post',data:{request:JSON.stringify(data.data),batchid:batchid,ran:Math.random()}});
}


var mql = window.matchMedia('(orientation: portrait)');
function handleOrientationChange(mql) {
    if(mql.matches) {
      console.log('portrait');  // 竖屏

    }else {
      console.log('landscape'); // 横屏

    }
}
// 输出当前屏幕模式
handleOrientationChange(mql);
// 监听屏幕模式变化
mql.addListener(handleOrientationChange);

var gheight=0;

function getWidgetHeight(height){
  gheight=height;
  $(".nrBtn").css("bottom",height+"px");
  $("#scroller").css("height",(window.screen.height-50-44-40-15-height)+"px");
}



function toshare(){
  $(".am-share").addClass("am-modal-active"); 
  if($(".sharebg").length>0){
    $(".sharebg").addClass("sharebg-active");
  }else{
    $("body").append('<div class="sharebg"></div>');
    $(".sharebg").addClass("sharebg-active");
  }
  $(".sharebg-active,.share_btn").click(function(){
    $(".am-share").removeClass("am-modal-active");  
    setTimeout(function(){
      $(".sharebg-active").removeClass("sharebg-active"); 
      $(".sharebg").remove(); 
    },300);
  })
}

//替换地址中的参数
//替换地址中的参数
function changeUrlArg(url, arg, val){
    var pattern = arg+'=([^&]*)';
    var replaceText = arg+'='+val;
    return url.match(pattern) ? url.replace(eval('/('+ arg+'=)([^&]*)/gi'), replaceText) : (url.match('[\?]') ? url+'&'+replaceText : url+'?'+replaceText);
}

 //口语优信回调的接口函数
function getVoicePath(path,type){
    //alert(type + "sss="+path);
    //window.UXinJSInterface.hideProgress();
    //window.UXinJSInterface.showAlert("上传成功");
    var readid= textreadarr.readid;
    var content=textreadarr.content;
    var datatype=0;
    var chapterid=0;
    var ks_code=textreadarr.ks_code;
    var url="../Text/gettextsmartScore";
    //window.UXinJSInterface.hideProgress();
    UXinJSInterface.showAlert("正在评分,请稍后....");
    $.ajax({
        type:'POST',
        timeout: 30000,
        url:url,
        data:{
            content:content,
            ks_code:ks_code,
            filename:path,
            contentid:readid,
            chapterid:chapterid,
            type:datatype,
            ran:Math.random()
        },
        async:true,
        dataType:'json',
        success:function(data){
            var score=(data.score);
            UXinJSInterfaceSpeech.sendScoreResult(score*1);
            UXinJSInterface.hideProgress();
        },
        error:function(xhr,type,errorThrown){
            UXinJSInterface.hideProgress();
        }
    });
}

var setTip=function(content){
    $("#tips").remove();
    var tips=$('<div id="tips" style="z-index:1111111111111;"></div>');
        tips.appendTo($("body"));
      //var tip = document.getElementById('tips');
      tips.html(content);
      tips.css("display",'block');
      setTimeout(function(){
        tips.css("display",'none');
          tips.remove();
      }, 5000);
}

