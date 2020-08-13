var showloading=function(){
	document.getElementById("over").style.display = "block";
    document.getElementById("layout").style.display = "block";
}
var hideloading=function(){
	document.getElementById("over").style.display = "none";
    document.getElementById("layout").style.display = "none";
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
var setTip=function(content){
    var tip = document.getElementById('tips');
    tip.innerHTML = content;
    tip.style.display = 'block';
    setTimeout(function(){ 
        tip.style.display = 'none';
    }, 3000);
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
        //timeout:4000,//超时时间设置为10秒；
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
    setInterval(
        function(){
            var value=setTimeValue(c);
            try{
                $(obj).html(value);
            }catch(e){
                console.log(e);
            }
            
            c=c+1 ;
        },1000)
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
//判断变量是否是函数
var isExitsFunction=function(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
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
function initBookImg(url,listobj){
    $(listobj).empty();
    $.ajax({
        type:'GET',
        url:url,
        dataType:'json',
        async:false,
        timeout: 300,
        context:$('body'),
        success: function(data){
            //遮罩消失
            hideloading();
            $.each(data,function(k,v){
                //动态创建单词的元素
                var attrarr=[];
                var temp={};
                var li =initDom("<li></li>",attrarr);
                li.appendTo(listobj);
                // li.bind("click",function(){
                //     if($(this).find("i").hasClass("icon-right")){
                //         $(this).find("i").addClass("icon-down").removeClass("icon-right");
                //         $(this).parent().find("ol").hide();
                //         $(this).next().show();   
                //     }else{
                //         $(this).find("i").addClass("icon-right").removeClass("icon-down");
                //         $(this).next().hide();
                //     }
                //     $(window).resize();
                // });
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="f1";
                attrarr.push(temp);
                var span =initDom("<span></span>",attrarr);
                span.text(v.grade_name);
                span.appendTo(li);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="fr";
                attrarr.push(temp);
                span =initDom("<span></span>",attrarr);
                span.appendTo(li);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="icon-right";
                attrarr.push(temp);
                var i =initDom("<i></i>",attrarr);
                i.appendTo(span);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="bookList";
                attrarr.push(temp);
                var ol =initDom("<ol></ol>",attrarr);
                ol.appendTo(listobj);
                //console.log(v.termversion);
                if(v.termversion.length==0){
                    ol.hide();
                }else{
                    var iCount=0;
                    $.each(v.termversion,function(tk,tv){
                        iCount=iCount+1;
                        attrarr=[];
                        temp={};
                        temp.id="version_id";
                        temp.val=tv.id;
                        attrarr.push(temp);
                        temp={};
                        temp.id="class";
                        temp.val="version";
                        attrarr.push(temp);
                        temp={};
                        temp.id="app_id";
                        temp.val=tv.app_id;
                        attrarr.push(temp);
                        temp={};
                        temp.id="subject_code";
                        temp.val=tv.r_subject_code;
                        attrarr.push(temp);
                        temp={};
                        temp.id="grade_code";
                        temp.val=tv.r_grade_code;
                        attrarr.push(temp)
                        li =initDom("<li></li>",attrarr);
                        if(tv.checked==0){
                           // ol.hide();
                            //li.find("i.mark").hide();
                        }
                        li.appendTo(ol);
                        li.bind("click",function(){
                            $(".mark").hide();
                           // $(this).find("i.mark").show();
                            var app_id=$(this).attr("app_id");
                            var subject_code=$(this).attr("subject_code");
                            var version_id=$(this).attr("version_id");
                            var grade_code=$(this).attr("grade_code");
                            //showloading();
                            $.getJSON("setUserGradeVersion",{app_id:app_id,subject_code:subject_code,version_id:version_id,grade_code:grade_code},function(){
                                // hideloading();
                                // setTip("设置成功");
                                // window.location.href = "/Elearn/Index/index?app_id=" + Request["app_id"] + "&backUrl=" +
                                // Request["backUrl"];
                                autoReturn(version_id);
                            });
                        });
                        attrarr=[];
                        temp={};
                        temp.id="style";
                        temp.val="position: relative;";
                        attrarr.push(temp);
                        span =initDom("<span></span>",attrarr);
                        span.appendTo(li);
                        attrarr=[];
                        if(tv.checked==0){
                            temp={};
                            temp.id="style";
                            temp.val="color: #090;display:none;";
                            attrarr.push(temp);
                        }else{
                            temp={};
                            temp.id="style";
                            temp.val="color: #090;";
                            attrarr.push(temp);
                        }
                        temp={};
                        temp.id="class";
                        temp.val="icon-correct04 mark";
                        attrarr.push(temp);
                        var i =initDom("<i></i>",attrarr);
                        i.appendTo(span);
                       
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val='wh150';
                        attrarr.push(temp);
                        var lable =initDom("<lable></lable>",attrarr);
                        lable.appendTo(span);
                        attrarr=[];
                        temp={};
                        temp.id="src";
                        temp.val=tv.r_pic;
                        attrarr.push(temp);
                        // temp={};
                        // temp.id="width";
                        // temp.val="100%";
                        // attrarr.push(temp);
                        // temp={};
                        // temp.id="height";
                        // temp.val="140px";
                        // attrarr.push(temp);
                        var img =initDom("<img></img>",attrarr);
                        img.appendTo(lable);
                        attrarr=[];
                        temp={};
                        temp.id="style";
                        temp.val="font-size:10px;";
                        attrarr.push(temp);
                        span =initDom("<span></span>",attrarr);
                        span.html(tv.r_grade_name+'.'+tv.r_term_name+'.'+tv.r_version_name);
                        span.appendTo(li);
                        if(iCount%3==0){
                            ol.append("<div class='clearfix'></div>");
                        }
                    });
                }
            });
            
        },
        error:function(xhr,type){
            hideloading();
        }
    })
}
