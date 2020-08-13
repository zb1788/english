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

var Requests = new Object();
Requests = GetRequest();

var perface=function(){
    try{
        var REPORT_URL = "/Subject/Api/setPerformanceLogApi?"; // 收集上报数据的信息
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

    }

}
//监听页面性能上报
//perface();

var error=function(msg,url,line){
    try{
        var REPORT_URL = "/Subject/Api/setErrorLogApi?"; // 收集上报数据的信息
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

var reponse=function(url){
  try{
      //return times;
      var latitude=0;
      var longitude=0;
      var img = new Image;
      img.onload = img.onerror = function(){
        img = null;
      };
      img.src = url;// 发送数据到后台cgi
  }catch(e){

  }
}

Date.prototype.format = function(format) {
       var date = {
              "M+": this.getMonth() + 1,
              "d+": this.getDate(),
              "h+": this.getHours(),
              "m+": this.getMinutes(),
              "s+": this.getSeconds(),
              "q+": Math.floor((this.getMonth() + 3) / 3),
              "S+": this.getMilliseconds()
       };
       if (/(y+)/i.test(format)) {
              format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
       }
       for (var k in date) {
              if (new RegExp("(" + k + ")").test(format)) {
                     format = format.replace(RegExp.$1, RegExp.$1.length == 1
                            ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
              }
       }
       return format;
}
