//ajax请求之前的效果
var wordreadpath="http://en2.czbanbantong.com:8080/yylmp3/mp3_word/";
var wordpicpath="http://en2.czbanbantong.com:8080/yylmp3/pic_word/";
var textreadpath="";
var examreadpath="";
var examreadpath="";
var bookimg=""
var subjectid="";
var versionid="";
var gradeid="";
var termid="";
var showloading=function(){
	document.getElementById("over").style.display = "block";
    document.getElementById("layout").style.display = "block";
}
var hideloading=function(){
	document.getElementById("over").style.display = "none";
    document.getElementById("layout").style.display = "none";
}



var setTimes=function(){
    
}

var perface=function(){
    try{
        var REPORT_URL = "../Api/setPerformanceLogApi?"; // 收集上报数据的信息
        var times = {};
        var performance=window.performance;
        var url=encodeURIComponent(location.href);
        var referrer=encodeURIComponent(document.referrer);
        if (!performance) {
            // 当前浏览器不支持
            console.log('你的浏览器不支持 performance 接口');

        }else{
            var t = performance.timing;
            //【重要】页面加载完成的时间
            //【原因】这几乎代表了用户等待页面可用的时间
            times.loadPage = t.loadEventEnd - t.navigationStart;
            //【重要】解析 DOM 树结构的时间
            //【原因】反省下你的 DOM 树嵌套是不是太多了！
            times.domReady = t.domComplete - t.responseEnd;
         
            //【重要】重定向的时间
            //【原因】拒绝重定向！比如，http://example.com/ 就不该写成 http://example.com
            times.redirect = t.redirectEnd - t.redirectStart;
         
            //【重要】DNS 查询时间
            //【原因】DNS 预加载做了么？页面内是不是使用了太多不同的域名导致域名查询的时间太长？
            // 可使用 HTML5 Prefetch 预查询 DNS ，见：[HTML5 prefetch](http://segmentfault.com/a/1190000000633364)            
            times.lookupDomain = t.domainLookupEnd - t.domainLookupStart;
         
            //【重要】读取页面第一个字节的时间
            //【原因】这可以理解为用户拿到你的资源占用的时间，加异地机房了么，加CDN 处理了么？加带宽了么？加 CPU 运算速度了么？
            // TTFB 即 Time To First Byte 的意思
            // 维基百科：https://en.wikipedia.org/wiki/Time_To_First_Byte
            times.ttfb = t.responseStart - t.navigationStart;
         
            //【重要】内容加载完成的时间
            //【原因】页面内容经过 gzip 压缩了么，静态资源 css/js 等压缩了么？
            times.request = t.responseEnd - t.requestStart;
         
            //【重要】执行 onload 回调函数的时间
            //【原因】是否太多不必要的操作都放到 onload 回调函数里执行了，考虑过延迟加载、按需加载的策略么？
            times.loadEvent = t.loadEventEnd - t.loadEventStart;
         
            // DNS 缓存时间
            times.appcache = t.domainLookupStart - t.fetchStart;
         
            // 卸载页面的时间
            times.unloadEvent = t.unloadEventEnd - t.unloadEventStart;
         
            // TCP 建立连接完成握手的时间
            times.connect = t.connectEnd - t.connectStart;
        }
        //return times;
        var url= REPORT_URL +"performance="+JSON.stringify(times)+"&url="+url+"&referrer="+referrer+"&date="+new Date;// 组装错误上报信息内容URL
        var latitude=0;
        var longitude=0;
        var img = new Image;
        img.onload = img.onerror = function(){
          img = null;
        };
        img.src = url;// 发送数据到后台cgi  
    }catch(e){
        console.log("网络出错");
    }
    
}


var error=function(msg,url,line){
    try{
        var REPORT_URL = "../Api/setErrorLogApi?"; // 收集上报数据的信息
        var m =[msg, url, line, navigator.userAgent, new Date];// 收集错误信息，发生错误的脚本文件网络地址，用户代理信息，
        var referrer=encodeURIComponent(document.referrer);
        //return times;
        var url= REPORT_URL + 'msg='+msg+'&url='+url+"&line="+line+"&agent="+navigator.userAgent+"&date="+new Date+"&referrer="+referrer// 组装错误上报信息内容URL
        var latitude=0;
        var longitude=0;
        var img = new Image;
        img.onload = img.onerror = function(){
          img = null;
        };
        img.src = url;// 发送数据到后台cgi  
    }catch(e){
        console.log("网络出错");
    }
    
}
// 监听错误上报
window.onerror = function(msg,url,line){
   error(msg,url,line);
}



//优信回调方法
function phoneCallback(){
    
}

//获取用户设置的版本信息


var arrContrain=function(array,obj){
    var i=array.length;
    while(i--){
       if (array[i] === obj) {  
            return true;  
        }  
    }
    return false;
}

var numToString=function(num){
    if(num==1){
        return "一";
    }else if(num==2){
        return "二";
    }else if(num==3){
        return "三";
    }else if(num==4){
        return "四";
    }else if(num==5){
        return "五";
    }else if(num==6){
        return "六";
    }else if(num==7){
        return "七";
    }else if(num==8){
        return "八";
    }else if(num==9){
        return "九";
    }else if(num==10){
        return "高中";
    }
}

//Dom元素初始化
var initDom=function(dom,attrarr){
    var dom=$(dom);
    $.each(attrarr,function(key,value){
        dom.attr(value.id,value.val);
    });
    return dom;
}


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

var Requests = new Object();
Requests = GetRequest();
console.log(document.cookie)
var arrCookie=document.cookie.split(";");
for(var i=0;i<arrCookie.length;i++){
     var arr=arrCookie[i].split("=");
     //找到名称为userId的cookie，并返回它的值
     if(arr[0]=='subjectid'){
        subjectid=arr[1];
     }else if(arr[0]=='versionid'){
        versionid=arr[1];
     }else if(arr[0]=='gradeid'){
        gradeid=arr[1];
     }else if(arr[0]=='termid'){
        termid=arr[1];
     }
}

//获取用户设置版本的cookie信息
// try{
//     $.ajax({
//         type:'GET',
//         url:"../User/getUserBookinfo?ran="+Math.random(),
//         async:true,
//         success: function(data){
//             //遮罩消失
//             subjectid=data.subjectid;
//             versionid=data.versionid;
//             gradeid=data.gradeid;
//             termid=data.termid;
//         },
//         error:function(xhr,type){

//         }
//     })
// }catch(e){
//     console.log("request error");
// }


/** 
 * 动态创建iframe 
 * @param dom 创建iframe的容器，即在dom中创建iframe。dom可以是div、span或者其他标签。 
 * @param src iframe中打开的网页路径 
 * @param onload iframe加载完后触发该事件，可以为空 
 * @return 返回创建的iframe对象 
*/  
function createIframe(dom, src, onload){  
    //在document中创建iframe  
    var iframe = document.createElement("iframe");  
      
    //设置iframe的样式  
    iframe.style.width = '100%';  
    iframe.style.height = '100%';  
    iframe.style.margin = '0';  
    iframe.style.padding = '0';  
    iframe.style.overflow = 'hidden';  
    iframe.style.border = 'none';  
      
    //绑定iframe的onload事件  
    if(onload && Object.prototype.toString.call(onload) === '[object Function]'){  
        if(iframe.attachEvent){  
            iframe.attachEvent('onload', onload);  
        }else if(iframe.addEventListener){  
            iframe.addEventListener('load', onload);  
        }else{  
            iframe.onload = onload;  
        }  
    }  
      
    iframe.src = src;  
    //把iframe加载到dom下面
    console.log(dom);
    dom.append(iframe);  
    return iframe;  
}

//跳转共有函数

var gopage=function(page){
    var c=document.getElementById("iScroll-bd");
    a=-(page)*$(window).width();
    b=200;
    c =  c.style,
    c.webkitTransitionDuration = c.MozTransitionDuration = c.msTransitionDuration = c.OTransitionDuration = c.transitionDuration = b + "ms",
    c.webkitTransform = "translate(" + a + "px,0)" + "translateZ(0)",
    c.msTransform = c.MozTransform = c.OTransform = "translateX(" + a + "px)"
    c.style=c;
    
    try{
        $("#cur").text(page+1);
        $("#chapter").text($(".text").eq(page).parent().parent().attr("chapter"));
        mp3.setCurpage(page);
    }catch(e){

    }
    
}
  
/** 
 * 销毁iframe，释放iframe所占用的内存。 
 * @param iframe 需要销毁的iframe对象 
*/  
// function destroyIframe(iframe){  
//     //把iframe指向空白页面，这样可以释放大部分内存。  
//     iframe.src = 'about:blank';  
//     try{  
//         iframe.contentWindow.document.write('');  
//         iframe.contentWindow.document.clear();  
//     }catch(e){}  
//     //把iframe从页面移除
//     iframe.parent().find("iframe").remove();
//     //iframe.parentNode.remove(iframe);  
// }

var SectionToChinese=function(section){
    var chnNumChar = ["0","1","2","3","4","5","6","7","8","9"];
    var chnUnitChar = ["","十","百","千","万"];
    var strIns = '', chnStr = '';
    var unitPos = 0;
    var zero = true;
    while(section > 0){
        var v = section % 10;
        if(v === 0){
            if(!zero){
                zero = true;
                chnStr = chnNumChar[v] + chnStr;
            }
        }else{
            zero = false;
            strIns = chnNumChar[v];
            strIns += chnUnitChar[unitPos];
            chnStr = strIns + chnStr;
        }
        unitPos++;
        section = Math.floor(section / 10);
    }
    return chnStr;
}

var setTip=function(content){
    var tip = document.getElementById('tips');
    tip.innerHTML = content;
    tip.style.display = 'block';
    setTimeout(function(){ 
        tip.style.display = 'none';
    }, 2000);
}


var getUnitData=function(url,loc){
    $("#unit").empty();
    var request=GetRequest();
    var moduleid=request["moduleid"];
	$.ajax({
        type:'GET',
        url:url,
        data:{moduleid:moduleid,ran:Math.random()},
        async:true,
        dataType:'json',
        success: function(data){
            //遮罩消失
            hideloading();
            //初始化版本信息
            var bookinfo='<span class="fl pad10"><img src="'+data.bookinfo.img+'" id="bookimg" />';
            bookinfo=bookinfo+'</span>';
            bookinfo=bookinfo+'<span class="fr pad10">';
            bookinfo=bookinfo+'    <h2 class="textH2">'+data.bookinfo.gradename+'('+data.bookinfo.volumename+')</h2>';
            bookinfo=bookinfo+'    <h3 class="textH3">'+data.bookinfo.versionname+'</h3>'
            //bookinfo=bookinfo+'    <h5 class="textH4">2012年9月版</h5>';
            bookinfo=bookinfo+'    <p class="textP">';
            //bookinfo=bookinfo+'        <strong>背单词秘籍</strong>';
            bookinfo=bookinfo+'        <i class="icon-uniF005"></i>';
            bookinfo=bookinfo+'        <i class="icon-uniF005"></i>';
            bookinfo=bookinfo+'        <i class="icon-uniF005"></i>';
            bookinfo=bookinfo+'        <i class="icon-uniF005"></i>';
            bookinfo=bookinfo+'        <i class="icon-uniF005"></i>';
            bookinfo=bookinfo+'        <br />'+data.ways.remark;
            bookinfo=bookinfo+'    </p>';
            bookinfo=bookinfo+'</span>';

            $("#bookinfo").html(bookinfo);
            $(".title").html(data.ways.title);
            var len=0;
            var unit="";
            $.each(data,function(k,v){
                //基础模块的添加
                var attrarr=[];
                var temp={};
                if(v.is_unit=="1"){
                    len=len+1;
                    temp.id="title";
                    temp.val=v.ks_name;
                    attrarr.push(temp);
                    var h2 =initDom("<h2></h2>",attrarr);
                    h2.text(v.ks_name);
                    $("#unit").append(h2);
                	//unit=unit+'<h2 title="'+v.ks_name+'">'+v.ks_name+'</h2>';
                }else if(v.is_unit=="0"){
                    len=len+1;
                    temp.id="class";
                    temp.val="true";
                    attrarr.push(temp);
                    temp={};
                    temp.id="bid";
                    temp.val=v.ks_code;
                    attrarr.push(temp);
                    temp={};
                    temp.id="count";
                    temp.val=v.count;
                    attrarr.push(temp);
                    temp={};
                    temp.id="title";
                    temp.val=v.ks_name;
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    var count=v.count;
                    li.bind("click",function(){
                        var ks_code=$(this).attr("bid");
                        var count=$(this).attr("count");
                        var title=$(this).attr("title");
                        var request=GetRequest();
                        if(count!=null&&count!=undefined&&parseInt(count)>0){
                            createIframe($("body"),"../Public/setUserModuleUnitLog?ks_code="+ks_code+"&moduleid="+request["moduleid"]+"&ks_name="+v.ks_name);
                            window.location.href=loc+"?ks_code="+ks_code+"&moduleid="+request["moduleid"]+"&ks_short_name="+encodeURI(encodeURI(title));
                        }else{
                            setTip("这个单元下面没有资源");
                        }  
                    });
                    $("#unit").append(li);
                    attrarr=[];
                    var s =initDom("<s></s>",attrarr);
                    li.append(s);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="fl";
                    attrarr.push(temp);
                    temp={};
                    temp.id="title";
                    temp.val=v.ks_name;
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(count!=null&&count!=undefined&&parseInt(count)>0){
                        if(parseInt(v.userdata)==0){
                            temp.val="position: absolute;left: 0px;right: 80px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;";
                        }else{
                            temp.val="position: absolute;left: 0px;right: 80px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;color:#269bd7;";
                        }
                    }else{
                        temp.val="position:absolute;left: 0px;right: 80px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;color:#aaa;";
                    }
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text(v.ks_name);
                    li.append(span);
                    //if(count!=null&&count!=undefined&&parseInt(count)>0){
                    attrarr=[];
                    temp={};
                    if(count!=null&&count!=undefined&&parseInt(count)>0){
                        temp.id="class";
                        temp.val="fr fonG";
                    }
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    if(count!=null&&count!=undefined&&parseInt(count)>0){
                        if(v.isdo!='0'){
                            span.html(parseInt(v.isdo)+'人次学习<i class="icon-right"></i>');
                        }else{
                            span.html('<i class="icon-right"></i>');
                        }
                    }else{
                        span.html('&nbsp;');
                    }
                    li.append(span);
                }
            });
            if(len==0){
                var attrarr=[];
                var temp={};
                var li =initDom("<li></li>",attrarr);
                li.text("请使用“选教材”选择您需要的年级、版本");
                $("#unit").append(li);
                $("#bookinfo").hide();
            }
            //滑动问题
            new IScroll("#wrapper",{
                momentum:true,
                click:true 
            });
            $("#wrapper").resize();
        },
        error:function(xhr,type){

        }
    })
}


//设置标题
var setTitle=function(obj){
    var Request=GetRequest();
    obj.text(Request["ks_short_name"]);
}



//进行相应数据
var getResponse=function(url,data,func){
    var respose="";
    listendata=[];
    mui.ajax(url,
        {
        data:data,
        dataType:'json',//服务器返回json格式数据
        type:'post',//HTTP请求类型
        async:isasync,
        success:function(data){
            func(data);
        },
        error:function(xhr,type,errorThrown){
            //异常处理；
            mui.toast("网络请求出错请等会儿再试试");
            return errorThrown;
        }
    });
}

//进行计时间的函数
var setTimer=function(c,obj){
    var id=setInterval(
        function(){
            var value=setTimeValue(c);
            try{
                $(obj).html(value);
            }catch(e){
                console.log(e);
            }
            
            c=c+1 ;
        },1000)
    return id;
}

//将秒转成时间格式
var setTimeValue=function(c){
    var min=0,secs=0;
    var value="";
    min = parseInt(c / 60);// 分钟数
    if(min<10){
        value=value+"0"+min;
    }else{
        value=value+min;
    }
    secs = c % 60;
    if(secs<10){
        value=value+":0"+secs;
    }else{
        value=value+":"+secs;
    }
    return value;
}

//将时间格式转换成吗秒
var setSecondsTimer=function(c){
    console.log(c);
    var timerarr=c.split(":");
    var seconds=parseInt(timerarr[0])*60+parseInt(timerarr[1]);
    return seconds;
}

//口语优信回调的接口函数
function getVoicePath(path,type){
    //alert(type + "sss="+path);
    //window.UXinJSInterface.hideProgress();
    //window.UXinJSInterface.showAlert("上传成功");
     var readid=$(".recorecur").find("i.icon-ico-yuyin").parent().attr("contentid");
    var content=$(".recorecur").find("i.icon-ico-yuyin").parent().attr("content");
    var datatype=$(".recorecur").find("i.icon-ico-yuyin").parent().attr("type");
    var request=GetRequest();
    var chapterid=request["chapterid"];
    var ks_code=request["ks_code"];
    var url="../Public/getTestScore";
    showloading();
    //window.UXinJSInterface.hideProgress();
    UXinJSInterface.showAlert("正在评分,请稍后....");
    $.ajax({
        type:'POST',
        timeout: 5000,
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
        context:$('body'),
        success:function(data){
            //显示分数
            //UXinJSInterface.hideProgress();
            var content="";
            var score=(data.score);
            //分数
            $(".recorecur").find("font.fontZ").text(score);
            //最高分
            $(".recorecur").find(".textH4").eq(0).text("目前最高测评成绩："+data.maxscore+"分");
            //剩余次数
            if(data.readtimes>0){
                $(".recorecur").find(".textH4").eq(1).text("你还有"+data.readtimes+"次测评机会");
            }else{
                $(".recorecur").find(".textH4").eq(1).text("本试题的测试机会已经用完");
            }

            $(".recorecur").attr("readtimes",data.readtimes);

            //设置录音完成的样式
            if(parseInt(data.readtimes)==0){
                $(".recorecur").find(".luyinD").html("&nbsp;");
                $(".recorecur").find(".record").addClass("bGray");
            }
            
            //单词播放音频
            try{
                mp3.setUse(0);
                UXinJSInterfaceSpeech.playAudio(path);
            }catch(e){
                setTip("请升级到最新的优信听录音声音");
            }
            hideloading();
           UXinJSInterface.hideProgress();
            //直接播放音频
            //$(".recorecur").find("font.fontZ").text(score);     
        },
        error:function(xhr,type,errorThrown){
            hideloading();
            UXinJSInterface.hideProgress();
            //异常处理；
            //mask.close();
            setTip("评分超时，请稍等一会儿再提交");
        }
    });
}

//判断变量是否是函数
var isExitsFunction=function(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

var mp3time="";

//播放进行的封装
var mp3=function(){
    //初始化
    this.curindex=0;
    //类型是MP3
    this.playstatus=0;
    //播放的类型是课文还是MP3
    this.playtype=0;
    //表示播放的列表
    this.mp3list='';
    //表示要添加的样式
    this.addclass="";
    //表示要删除的样式
    this.removeclass="";
    //表示父亲节点
    this.parentobj="";
    //表示两个单词间隔的时间
    this.stoptime=1000;
    //表示遍数之间的间隔时间
    this.playstoptime=500;
    //表示播放完成是够有动作
    this.isvisiable=0;
    //表示的是播放的方式0表示顺序向下1表示播完遍数之后再向下播放
    this.playstyle=0;
    //列表播放总遍数
    this.playtimes=1;
    //表示当前播放的遍数
    this.curplaytimes=1;
    //表示表示停止播放模式
    this.stopflag=0;
    //表示是否开启重播模式
    this.replayflag=0;
    //表示播放列表的样式0表示的列表是{} 1表示的列表是{{}}
    this.playliststype=0;
    //表示的是什么模式0表示单一模式 1表示翻页图文模式
    this.liststyle=0;
    //download下载的列表
    this.downlist=0;
    //当前的页码
    this.curpage=0;
    //是否使用封装的类进行播放
    this.isuser=1;
    //表示是够报错
    this.type=1;
    this.isnourl=0;
    //播放参数的初始化
    this.playInit=function(playlist,downlist,addclass,removeclass){
        this.mp3list = playlist;
        this.downlist= downlist;
        this.addclass = addclass;
        this.removeclass = removeclass;
    };
    this.setNourl=function(isuse){
        this.isnourl = isuse;
    };
    this.setPlaystatus=function(playstatus){
        this.playstatus = playstatus;
    };
    this.getPlaystatus=function(){
        return this.playstatus;
    };
    this.setPlaytype=function(playtype){
        this.playtype = playtype;
    };
    this.getPlaytype=function(){
        return this.playtype;
    };
    this.setUse=function(isuse){
        this.isuser = isuse;
    };
    this.getUse=function(){
        return this.isuser;
    };
    this.setPlaylist=function(playlist){
        this.mp3list = playlist;
    };
    this.getPlaylist=function(){
        return this.mp3list;
    };
    this.setListstyle=function(liststyle){
        this.liststyle=liststyle;
    };
    this.getListstyle=function(liststyle){
        return this.liststyle;
    };
    //设置当前播放的次数
    this.setCurPlaytimes=function(){
        this.curplaytimes=1;
    };
    //获取当前播放的次数
    this.getPlaytimes=function(){
        return this.playtimes;
    };
    //设置当前播放的次数
    this.setPlaytimes=function(playtimes){
        this.playtimes=playtimes;
    };
    //获取是否可见
    this.getVisiable=function(){
        return this.isvisiable;
    };
    //获取列表的长度
    this.getLength=function(){
        return this.mp3list.length;
    };
    //设置列表中两个之前的停顿时间
    this.setStoptime=function(stoptime)
    {
        this.stoptime=stoptime;
    };
    //获取列表两个之间的停顿时间
    this.getStoptime=function()
    {
        return this.stoptime;
    };
    //列表的游标向下走一个
    this.addCurindex=function()
    {
        this.curindex=this.curindex+1;
    };
    //设置播放的游标
    this.setCurindex=function(index)
    {
        this.curindex=index;
    };
    //设置播放的游标
    this.setType=function(type)
    {
        this.type=type;
    };
    //获取当前播放的游标
    this.getCurindex=function()
    {
        return this.curindex;
    };
    //设置父亲节点对象
    this.setParentobj=function(parentobj)
    {
        this.parentobj=parentobj;
    };
    this.setCurpage=function(curpage){
        this.curpage=curpage;
    };
    this.getCurpage=function(curpage){
        return this.curpage;
    };
    this.addCurpage=function(curpage){
        this.curpage=this.curpage+1;
    };
    //去掉样式
    this.removeCurClass=function()
    {
        if(this.removeclass!=this.addclass){
            $(this.parentobj).eq(this.curindex).addClass(this.removeclass).removeClass(this.addclass);
        }
        
    };
    //去掉样式
    this.addCurClass=function()
    {
        if(this.removeclass!=this.addclass){
            $(this.parentobj).removeClass(this.removeclass).addClass(this.addclass);
        }
        
    };
    //下载MP3列表
    this.mp3dowload=function(){
       // console.log(iGetInnerText(JSON.stringify(this.downlist)));
        try{
            UXinJSInterfaceSpeech.cacheAudioFiles(iGetInnerText(JSON.stringify(this.downlist)));
        }catch(e){
            setTip("升级到最新版本的优信");
        }
    };
    //获取单词列表
    this.getdownlist=function(){
        return this.downlist;
    };
    //设置playliststyle
    this.setPlayliststyle=function(playliststype){
        this.playliststype=playliststype;
    };
    //获取playliststyle
    this.getPlayliststyle=function(){
        return this.playliststype;
    };
    //播放单个单词
    this.playWord=function(){
        //当前的节点
        var cur=$(this.parentobj).eq(this.curindex);
        //进行异步的请求
        try{
            learnlog(cur);
        }catch(e){

        }
        //设置停顿时间差
        try{
            var stoptime=this.mp3list[this.curindex].stoptime;
            if(stoptime!=undefined&&stoptime!=''){
                this.stoptime=stoptime*1000;
            }
        }catch(e){

        }

        //判断单词是够在中央
        try{
            var height=(window.screen.height-48-88)/3+(88);
            //当前元素的高度

            var top=$(this.parentobj).eq(this.curindex).offset().top;
            var left=$(this.parentobj).eq(this.curindex).offset().left;
            var lis=$(this.parentobj);
            var lastli=lis[lis.length-1];
            var firstli=lis[0];
            var maxtop=$(lastli).offset().top;
            // var mintop=$(firstli).offset().top;
            //判断当前的元素和中间界限的高度
            $("#scroller").offset().top;
            if(top>(height)&&maxtop>(height*2-48)){
                $("#scroller").css('transform','translate(0px, '+($("#scroller").offset().top-(top-height/2))+'px)');
                //$("#scroller").css('-webkit-transform','translate(0px, -'+(height/2)+'px)');
            }
        }catch(e){

        }
        //样式的变化
        if(this.isuser==1){
            this.addCurClass();
            this.removeCurClass();
        }
        try{
            if($(this.parentobj).parents("p").eq(this.getCurindex()).hasClass("textread")){
                $(".active").removeClass("active");   
                $(this.parentobj).parents("p.textread").eq(this.getCurindex()).addClass("active");
                $(this.parentobj).parents("p.textread").eq(this.getCurindex()).addClass("curplay");
            }
        }catch(e){

        }

        //$(this.parentobj).eq(this.curindex).addClass(this.removeclass).removeClass(this.addclass);
        //如果是顺序列表的话
        var mp3name="";
        var cuepagelen=0;
        if(this.playliststype==0){
            cuepagelen=this.getLength();
        }else{
            cuepagelen=this.getCurpageListlen();
        }
        if(this.curindex!=cuepagelen){
            if(this.playliststype==0){
                if(this.type == 1){
                    mp3name=this.mp3list[this.curindex].name;
                }else{
                    mp3name=this.mp3list[this.curindex].url;
                }
                
            }else{
                if(this.type == 1){
                    mp3name=this.mp3list[this.curpage].texts[this.curindex].name;
                }else{
                    mp3name=this.mp3list[this.curpage].texts[this.curindex].url;
                }
                
            }
            //播放音频
            try{
                if(this.type == 1&&this.playstatus==0){
                    UXinJSInterfaceSpeech.playAudio(mp3name);
                    //console.log(mp3name);
                }
                else{
                    //console.log(mp3name);
                    UXinJSInterfaceSpeech.playAudio(mp3name);
                }
                //onAudioPlayStatus(0);
            }catch(e){
                //报错提示
                //setTip("请升级到最新的优信");
            }
        }else{

            clearTimeout(mp3time);
            if(this.playliststype==0){
                if(this.getVisiable()==1){
                    //读一读样式变化
                    try{
                        $("#audioplay").addClass("stop").removeClass("play");
                        $("#audioplay").find("font").text("连读");
                        $("#audioplay").find("i").addClass("icon-uniE60C").removeClass("icon-playt");

                        $("p.active").removeClass("active").removeClass("curplay");
                    }catch(e){
                        //不处理
                    }
                    if(this.isnourl==1){
                        mask('您已经学习完本单元单词了','单词测试',"reciteword?ks_code="+Requests["ks_code"]+"&ks_short_name="+Requests["ks_short_name"]+"&moduleid=9");
                    }else{
                        mask('您已经学习完本单元单词了','单词测试',"reciteword?ks_code="+Requests["ks_code"]+"&ks_short_name="+Requests["ks_short_name"]+"&moduleid=9",'口语训练',"readword?chapterid=0&ks_code="+Requests["ks_code"]+"&moduleid=4&ks_short_name="+Requests["ks_short_name"]+"&chaptername=%25E5%258D%2595%25E8%25AF%258D%25E5%258F%25A3%25E8%25AF%25AD");
                    }
                    
                }
            }else{

                //读一读样式变化
                try{
                    //$("#audioplay").addClass("stop").removeClass("play");
                    //$("#audioplay").find("font").text("开始");
                    $("p.active").removeClass("active").removeClass("curplay");
                    $("#audioplay").find("font").text("连读");
                    $("#audioplay").find("i").removeClass("icon-playt").addClass("icon-uniE60C");
                    $("#audioplay").addClass("stop").removeClass("play");
                    $(".active").removeClass("active").removeClass("curplay");  
                }catch(e){
                    //不处理
                }
                if(this.getVisiable()==1){
                    mask('您已经学习完本单元单词了','单词测试',"reciteword?ks_code="+Requests["ks_code"]+"&ks_short_name="+Requests["ks_short_name"]+"&moduleid=9",'口语训练',"readword?chapterid=0&ks_code="+Requests["ks_code"]+"&moduleid=4&ks_short_name="+Requests["ks_short_name"]+"&chaptername=%25E5%258D%2595%25E8%25AF%258D%25E5%258F%25A3%25E8%25AF%25AD");
                }
            }
            

        }
    };
    //获取本页面中列表的长度
    this.getCurpageListlen=function(){
        return this.mp3list[this.curpage].texts.length;
    };
    //播放列表

    this.playWordList=function(cur,playtimes,isvisiable,playliststype,playstyle,playstoptime,stoptime){

        if(playliststype!=undefined){
            this.playliststype=playliststype;
        }
        if(playtimes!=undefined){
            this.playtimes=playtimes;
        }
        if(playstoptime!=undefined){
            this.playstoptime=playstoptime;
        }
        if(stoptime!=undefined){
            this.stoptime=stoptime;
        }
        if(isvisiable!=undefined){
            this.isvisiable=isvisiable;
        }
        if(playstyle!=undefined){
            this.playstyle=playstyle;
        }
        //播放音频
        if(this.playliststype==0){
            //表示的是{}
            if(this.playstyle==0){
                //表示的是顺序播放列表
                this.setCurindex(cur);

                this.playWord();
                this.addCurindex();
                //判断是否需要进行下一遍
                if(this.curplaytimes<this.playtimes&&this.curindex==this.getLength()){
                    this.curplaytimes=this.curplaytimes+1;
                    this.curindex=0;
                }
            }else if(this.playstyle==1){
                //表示的是播放完一个在播放下一个
                this.setCurindex(cur);
                this.playWord();
                this.curplaytimes=this.curplaytimes+1;
                if(this.curplaytimes==this.playtimes){
                    this.addCurindex();
                    this.curplaytimes=1;
                }
            }
        }else if(this.playliststype==1){
            if(this.playstyle==0){
                //表示的是顺序播放列表
                this.setCurindex(cur);
                this.playWord();
                this.addCurindex();
            }else if(this.playstyle==1){
                //表示的是播放完一个在播放下一个
                this.setCurindex(cur);
                this.playWordList(0);
            }
        }
    };
    //停止播放
    this.stopWord=function(cur){
        //表示从第几个开始播放
        this.stopflag=1;
    };
    //重放
    this.rePlay=function(cur){
        //表示从第几个开始播放

        
    }
}
//定义MP3对象
var mp3=new mp3();

//数据是否缓存成功
var onCacheAudioStatus=function(status){
    if(0 == status){
        mp3.setType(0);
        if(mp3.getPlaytype()==0&&mp3.getPlaystatus()!=0){
            //播放音频并且设置成0
            mp3.setPlaystatus(0);
            $(".con").eq(0).find(".alound").click(); 
        }
        
    }else{
        mp3.setType(1);
        //alert("下載出錯");
    }
}


var iGetInnerText=function(testStr) {
    var resultStr = testStr.replace(/\ +/g, ""); //去掉空格
    resultStr = testStr.replace(/[ ]/g, "");    //去掉空格
    resultStr = testStr.replace(/[\r\n]/g, ""); //去掉回车换行
    return resultStr;
}

var mask=function(content,yes,yesurl,no,nourl){
    if(no==undefined){
        layer.open({
            anim: 'up'
            ,style: 'border:none; color:#666;'
            ,content:content
            ,btn: yes
            ,yes: function(index){
                window.location.href=yesurl;
            }         
        });
    }else{
        layer.open({
            anim: 'up'
            ,style: 'border:none; color:#666;'
            ,content:content
            ,btn: [yes, no]
            ,yes: function(index){
               window.location.href=yesurl;
            }
            ,no: function(index){
               window.location.href=nourl;
            }          
        });        
    }      
}


var isExitsFunction=function(funcName) {
    if(typeof UXinJSInterfaceSpeech != 'undefined'){
        try {
            if (typeof(eval(funcName)) == "function") {
                return 1;
            }
        } catch(e) {}
        return 0;
    }else{
        return -1;
    }  
}






//进行学习记录的跟新
var learnlog=function(obj){
    //当前的obj
    var contentid=$(obj).attr("contentid");
    var ks_code=$(obj).attr("ks_code");
    var chapterid=$(obj).attr("chapterid");
    var typeid=$(obj).attr("typeid");
    var source=$(obj).attr("source");
    var moduleid=Requests["moduleid"];
    var url="../User/setUserLearnLog"
    $.ajax({
        type:'POST',
        url:url,
        data:{
            contentid:contentid,
            ks_code:ks_code,
            moduleid:moduleid,
            chapterid:chapterid,
            typeid:typeid,
            source:source,
            ran:Math.random()
        },
        async:true,
        dataType:'json',
        timeout: 30000,
        context:$('body')
    });
    
}

//停止播放的公共方法
function stopautio(){
    try{
        clearTimeout(mp3time);
        UXinJSInterfaceSpeech.stopAudio();
    }catch(e){
        //setTip("请升级到最新的优信");
    }
}


//听力训练的音频停止事件其中perpage表示来的页面 nxtpage表示去的页面
function hwstopaudio(i){
    var perpage=0;
    //设置当前音频的来源的音频
    try{
        if(i==0||pageslider[i-1]==undefined){
            perpage=0;
        }else{
            perpage=pageslider[i-1];
        }
    }catch(e){}
    //设置当前音频的去向的音频
    var nxtpage=0;
    try{
        if(pageslider[i+1]==undefined){
            nxtpage=0;
        }else{
            nxtpage=pageslider[i+1];
        }
    }catch(e){}
    pageslider[i]=perpage>nxtpage?perpage+1:nxtpage+1;
    //判断当前是从哪里过来的
    if(page_obj[i].type!=1){
        try{
            $("#playing").addClass("play").removeClass("stop");
        }catch(e){}
        try{
            document.getElementById("playing").removeAttribute("id");
        }catch(e){}
        stopautio();
        return 1;
    }else{
            if(page_obj[i].type==0){
                    stopaudio();
                    return 1;
            }else if(page_obj[i].type==1){
            //组合试题从哪里过来的
            if(pageslider[i]==(pageslider[i-1]+1)){
                //表示从左边过来的数据判断左边的情况需要将昨天的那个直接停止了
                if(page_obj[i].stemid!=page_obj[i-1].stemid){
                    try{$("#playing").removeClass("stop").addClass("play");}catch(e){}
                    try{$("#playing").attr("id","");}catch(e){}
                    //设置当前的是正在播放的情况
                    $(".ques").eq(i).find("a.audio-btn").attr("id","playing");
                    stopautio();
                    //return 1;
                }else{
                    try{
                        //设置当前的是正在播放的情况
                        var status=$("#playing").hasClass("stop");
                        if(status){
                            $("#playing").removeClass("stop").addClass("play");
                            $("#playing").attr("id","");
                            $(".ques").eq(i).find("a.audio-btn").attr("id","playing");
                            $("#playing").removeClass("play").addClass("stop");
                        }
                    }catch(e){}
                    //return 0;
                }

            }else if(pageslider[i]==(pageslider[i+1]+1)){
                //表示从左边过来的数据判断右边的情况
                if(page_obj[i].stemid!=page_obj[i+1].stemid){
                    try{$("#playing").removeClass("stop").addClass("play");}catch(e){}
                    try{$("#playing").attr("id","");}catch(e){}
                    //设置当前的是正在播放的情况
                    $(".ques").eq(i).find("a.audio-btn").attr("id","playing");
                    stopautio();
                    //return 1;
                }else{
                    try{
                        //设置当前的是正在播放的情况
                        var status=$("#playing").hasClass("stop");
                        if(status){
                            $("#playing").removeClass("stop").addClass("play");
                            $("#playing").attr("id","");
                            $(".ques").eq(i).find("a.audio-btn").attr("id","playing");
                            $("#playing").removeClass("play").addClass("stop");
                        }
                    }catch(e){}
                    //return 0;
                }
            }
        }
    }
}

//record中需要zepto的支持获取jquery的支持
var record = function(){
    this.path="";
    this.addclass="";
    this.removeclass="";
    this.obj="";
    this.uploadflag=0;
    //适应多个插件的时候
    this.init=function(obj,removeclass,addclass){
        this.obj=obj;

        this.removeclass=removeclass;
        this.addclass=addclass;
    };
    this.getObj=function(){
        return this.obj;
    };
    //获取上传标志位置
    this.getFlag=function(obj,removeclass,addclass){
        return this.uploadflag;
    };
    //设置上传标志位置
    this.setFlag=function(flag){
        this.uploadflag=flag;
    };
    //开始录音
    this.recordVoice = function(){
        $(this.obj).removeClass(this.removeclass).addClass(this.addclass);
        window.UXinJSInterfaceSpeech.audioRecordWithPause();
    };
    //暂停|停止录音
    this.pauseVoice = function(){
        window.clearInterval(interid);
        window.UXinJSInterfaceSpeech.pauseAudioRecord();
        $(this.obj).removeClass(this.addclass).addClass(this.removeclass);
    };
    //试听录音
    this.listenVoice = function(){
        window.UXinJSInterfaceSpeech.getRecordName();
    };
    //暂停试听
    this.pauseAudio = function(){
        // window.UXinJSInterfaceSpeech.pauseAudio();
        mp3.pauseAudio();
    };
    //暂停后恢复
    this.resumeAudio = function(){
        mp3.resumeAudio();
    };
    //上传录音
    this.uploadVoice = function(){
        window.UXinJSInterfaceSpeech.getRecordName();
    };
    //重置录音
    this.recordReset = function(){
        window.UXinJSInterfaceSpeech.recordReset();
    };
    //开始录音
    this.start=function(){

        this.recordReset();
        this.recordVoice();
        this.uploadflag=1;
    };
    //进行录音样式
    this.create=function(){

    };
    //停止上传代码
    this.stop=function(){
        
        this.pauseVoice();
        
    }
}

var record=new record();



//优信调用此函数，传递录音路径
function getRecordPath(path){
    //上传的回调
    var uploadflag=record.getFlag();
    if(uploadflag==1){
        window.UXinJSInterfaceSpeech.uploadAudioRecord(path);
        record.setFlag(0);
    }
}

function onRecordStatus(status){
    switch(status){
        case "-1":
            record.setFlag(0);
            setTip('SD卡不存在或磁盘空间不足');
            break;
        case "-2":
            record.setFlag(0);
            setTip('请求录音权限失败');
            break;
        case "-3":
            record.setFlag(0);
            setTip('录音时间太短');
            break;
        case "-4":
            setTip('超过最大录音时长音频自动上传');
            //时间停止
            var obj=record.getObj();
            $(obj).parents(".playWord").find(".recordtime").addClass("istop").removeClass("iplay");
            window.clearInterval(interid);
            $('.recorecur .recordtime').hide();
            $('.recorecur .luyinD').show();
            //$('.recorecur .luyinD').html("请点击录音");
            $(obj).parents(".playWord").find(".recordtime").hide();
            record.stop();
            break;
        case "-99":
             setTip('未知错误');
            break;
        default:
            window.UXinJSInterface.showAlert("录音结束,上传并评分,请稍候");
            record.uploadVoice();
    }
}





