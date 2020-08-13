require.config({
　baseUrl: "../../public/public/js",
　paths: {
　　　"zepto": "zepto.min",
      "enajax":"enajax",
　　　"mui": "mui.min",
      "view": "mui.view"
　},
  shim:{
        zepto:{  
              deps:[],  
              exports: '$'  
        },
        mui:{  
              deps:[],  
              exports: 'mui'  
        },  
        view:{  
              deps:['mui'],
              exports: 'view'  
        },
        enajax:{  
              deps:['zepto'],
              exports: 'enajax'  
        }  
    },
    waitSeconds: 0
});

require(['zepto','mui',"touchslide","view","setContent","enajax"], function($,mui,touchslide,view,hw,enajax){
    //进行时间的展示
    //if(issubmit=='0'&&type=='0'){
    mui.init({
        swipeBack:false //启用右滑关闭功能
    });


        
    mui.back=function(){
        if(issubmit==0){
            try{
                UXinJSInterfaceSpeech.stopAudio();
                clearTimeout(mp3time);
            }catch(e){
                //mui.toast("请升级到最新的优信");
            }
            var btnArray = ['放弃作答', '继续作答'];
            mui.confirm('您放弃本次作答吗？', '提示', btnArray, function(e) {
                if (e.index == 0) {
                    $.ajax({
                        type:'GET',
                        url:'../User/setUserExamsOver',
                        data:{quizid:quizid,ran:Math.random()},
                        context:$('body')
                    });
                    window.location.href=(decodeURIComponent(Requests["backsUrl"]));
                } else {
                    
                }
            });
        }else{
            window.location.href=((Requests["callbackURL"]));
        }
        
    }
    //时间设定
    var c=parseInt(time);
    //timedCount();
    if(issubmit==0){
        setInterval(
            function(){
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
                try{
                    document.getElementById('time').innerHTML =value;
                }catch(e){
                    console.log(e);
                }
                
                c=c+1 ;
            },1000)
    }

    //view单页视图
    var viewApi = mui('#app').view({
        defaultPage: '#setting'
    });
    //初始化单页的区域滚动
    mui('.mui-scroll-wrapper').scroll();
    var view = viewApi.view;
    var mask = mui.createMask();
    //下载音频
    mask.show();//显示遮罩
    mui.ajax("getExamsQuestionTTS",
        {
        data:{
            examsid:examsid,
            ran:Math.random()
        },
        dataType:'json',//服务器返回json格式数据
        type:'post',//HTTP请求类型
        async:true,
        success:function(data){

            mask.close();
            //服务器返回然后进行h5的本地存储
            mp3.playInit(data,data,"play","stop");
            //初始化下载列表
            mp3.mp3dowload(); 
        },
        error:function(xhr,type,errorThrown){
            //异常处理；
            mask.close();
            mui.toast("请升级到最新的优信");
        }
    });
    //切换页面


    var getQuestionSummary=function(id){
        var url="getStudentExamsSummary";
        var summaryhtml="";
        mui.ajax(url,
            {
            data:{
                quizid:quizid,
                examsid:examsid,
                iserror:iserror,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            async:false,
            success:function(data){
                var eqcount=0;
                var eqquesid="";
                //服务器返回然后进行h5的本地存储
                summaryhtml=summaryhtml+"<p>&nbsp;&nbsp;<p>";
                var kindex=0;
                mui.each(data,function(k,v){
                    if(v.id!=eqquesid){
                        eqquesid=v.id;
                        kindex=kindex+1;
                    }
                    i=(k+1);
                    if(parseInt(v.score)>='1'||v.score=='0'){
                        summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="listenexam?index='+(kindex-1)+'&backsUrl='+(encodeURIComponent(Requests["backsUrl"]))+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                    }else{
                        summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="listenexam?index='+(kindex-1)+'&backsUrl='+(encodeURIComponent(Requests["backsUrl"]))+'"><span class="mui-icon" style="colar:black">'+i+'</span></a>';
                    }
                });
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errorThrown;
            }
        });
        return summaryhtml;
    }

    //进行试题的展示
    if(issubmit=='1'){
        //做完作业查看作业的时候需要进行的操作
        document.getElementById("datika").setAttribute("href","javascript:void(0);");
        document.getElementById("datika").addEventListener("click",function(){
            window.location.href=((Requests["callbackURL"]));
         });
    }else{
        var oldBack = mui.back;
        mui.back = function() {
            if (viewApi.canBack()) { //如果view可以后退，则执行view的后退
                viewApi.back();
                if(pageindex==parseInt(quescount)){
                    var prev=document.getElementById("prev");
                    mui.trigger(prev,'click');
                }
            } else { //执行webview后退
                oldBack();
            }
        };
    }
    //答题卡的展示
    view.addEventListener('pageBeforeShow', function(e) {
        try{
            UXinJSInterfaceSpeech.stopAudio();
            clearTimeout(mp3time);
        }catch(e){
            //mui.toast("请升级到最新的优信");
        }
        try{
            $("#playing").attr("src","../../public/Homework/images/sy.png");
            $("#playing").parent().removeClass("playing");
        }catch(e){

        }
        try{
            $("#playing").attr("id","");
        }catch(e){

        }
        document.getElementById("accountcontent").innerHTML="";
        if(e.detail.page.id=="account"){

            document.getElementById("datika").style.display="none";
            summaryhtml="";
            if(issubmit=='0'){
                document.getElementById("timeshow").style.display="none";
                document.getElementsByTagName("nav")[0].style.display="";
            }else{
                document.getElementById("suwt").style.marginBottom="0px";
            }
            var summaryhtml=getQuestionSummary(examsid);
            document.getElementById("accountcontent").innerHTML=summaryhtml;
            document.getElementById("accountcontent").style.marginTop="0px";
        }else{
            document.getElementById("accountcontent").innerHTML="";
            if(issubmit=='0'){
                document.getElementsByTagName("nav")[0].style.display="none";
                document.getElementById("timeshow").style.display="";
            }
            document.getElementById("datika").style.display="";
        }
    });
    view.addEventListener('pageShow', function(e) {
        ////作业提交的监听
        document.getElementById("submithomework").addEventListener('tap',function(){
            var url="../public/stupublish";
            mask.show();//显示遮罩
            mui.ajax(url,
                {
                data:{
                    quizid:quizid,
                    paper_id:examsid,
                    ran:Math.random()
                },
                dataType:'json',//服务器返回json格式数据
                type:'post',//HTTP请求类型
                async:true,
                success:function(data){
                    //服务器返回然后进行h5的本地存储
                    if(data.state="1"){
                        mask.close();
                        window.location.href='finish?quizid='+quizid+'&examsid='+examsid+'&ks_code='+Requests["ks_code"]+"&moduleid="+Requests["moduleid"]+"&ks_short_name="+encodeURIComponent(Requests["ks_short_name"])+"&backsUrl="+encodeURIComponent(Requests["backsUrl"]);
                    }else{
                        mask.close();
                        mui.toast("网络问题，请稍等一会儿在提交");
                    }  
                },
                error:function(xhr,type,errorThrown){
                    //异常处理；
                    mask.close();
                    mui.toast("网络问题，请稍等一会儿在提交");
                }
            });
        });
    });


   //答题器试题跳转
    mui("div#accountcontent").on("tap","a.quessummary",function(){
        var index=this.getElementsByTagName("span")[0].innerHTML;
        var url=this.getAttribute("loc");
        if(issubmit==1&&type==1){
            //examsid=2951&ks_code=00010203070101&moduleid=3&ks_short_name=Unit1
            
            url=url+'&examsid='+examsid+'&ks_code='+Requests["ks_code"]+"&moduleid="+Requests["moduleid"]+"&ks_short_name="+encodeURIComponent(Requests["ks_short_name"])+"&issubmit="+issubmit+"&time="+c;
            mui.openWindow(url);
        }else{
            url=url+'&examsid='+examsid+'&ks_code='+Requests["ks_code"]+"&moduleid="+Requests["moduleid"]+"&ks_short_name="+encodeURIComponent(Requests["ks_short_name"])+"&issubmit="+issubmit+"&time="+c;
            mui.openWindow(url);
        }
    });
    

    function addClass(obj, cls){
        var obj_class = obj.className;//获取 class 内容.
        var blank = (obj_class != '') ? ' ' : '';//判断获取到的 class 是否为空, 如果不为空在前面加个'空格'.
        var added = obj_class + blank + cls;//组合原来的 class 和需要添加的 class.
        obj.className = added;//替换原来的 class.
    }

    function removeClass(obj, cls){
        var obj_class = ' '+obj.className+' ';//获取 class 内容, 并在首尾各加一个空格. ex) 'abc        bcd' -> ' abc        bcd '
        obj_class = obj_class.replace(/(\s+)/gi, ' ');//将多余的空字符替换成一个空格. ex) ' abc        bcd ' -> ' abc bcd '
        var removed = obj_class.replace(' '+cls+' ', ' ');//在原来的 class 替换掉首尾加了空格的 class. ex) ' abc bcd ' -> 'bcd '
        removed = removed.replace(/(^\s+)|(\s+$)/g, '');//去掉首尾空格. ex) 'bcd ' -> 'bcd'
        obj.className = removed;//替换原来的 class.
    }

    function hasClass(obj, cls){
        var obj_class = obj.className;//获取 class 内容.
        var obj_class_lst = obj_class.split(/\s+/);//通过split空字符将cls转换成数组.
        var x = 0;
        for(x in obj_class_lst) {
            if(obj_class_lst[x] == cls) {//循环数组, 判断是否包含cls
                return true;
            }
        }
        return false;
    }


    //分页插件
    TouchSlide({
        slideCell:"#leftTabBox",
        effect:"left",
        defaultIndex:parseInt(index),
        prevCell : '#prev',
        nextCell : '#next',
        pnLoop:false,
        startFun:function(i,c){
            mask.show();
            pageindex=i;
            //翻页的时候停止播放音频如果是单体的话 就停止 组合试题的话不停止
            
            if(i==(c-1)&&issubmit=='0'){
                try{
                    clearTimeout(mp3time);
                    UXinJSInterfaceSpeech.stopAudio();
                }catch(e){
                    //mui.toast("请升级到最新的优信");
                }
                viewApi.go("#account");
            }
            try{
                document.getElementsByClassName("ques")[i].style.marginLeft="0px";
                document.getElementsByClassName("ques")[i].style.paddingLeft="0px";
            }catch(e){

            }
        },
        endFun:function(i,c){

            var quesindex=c;
            var prepageitem="item"+(i-1);
            var pageitem="item"+i;
            var nxtpageitem="item"+(i+1);
            
            var postdata="";
            if(i==0){
                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={examsid:examsid,quizid:quizid,index:i,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getExamsQuestion',postdata,$("#"+pageitem),false,i,page); 
                }
                if((i+1)<quescount){
                    //进行下一页的刷新
                    try{
                        if(page[i+1]==undefined||page[i+1]==0){
                            postdata={examsid:examsid,quizid:quizid,index:(i+1),iserror:iserror,ran:Math.random()};
                            hw.getResponse('getExamsQuestion',postdata,$("#"+nxtpageitem),true,i+1,page);
                        }
                    }catch(e){
                        
                    }
                }
                
                
            }else if(i==(quescount-1)){
                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={examsid:examsid,quizid:quizid,index:i,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getExamsQuestion',postdata,$("#"+pageitem),false,i,page);
                }
                //进行上一页的刷新
                if((i-1)>=0){
                    if(page[i-1]==undefined||page[i-1]==0){
                        postdata={examsid:examsid,quizid:quizid,index:(i-1),iserror:iserror,ran:Math.random()};
                        hw.getResponse('getExamsQuestion',postdata,$("#"+prepageitem),true,i-1,page);
                    }
                }
                
            }else if(i<(quescount)){

                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={examsid:examsid,quizid:quizid,index:i,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getExamsQuestion',postdata,$("#"+pageitem),false,i,page);
                }
                //进行上一页的刷新
                if(page[i-1]==undefined||page[i-1]==0){
                postdata={examsid:examsid,quizid:quizid,index:(i-1),iserror:iserror,ran:Math.random()};
                    hw.getResponse('getExamsQuestion',postdata,$("#"+prepageitem),true,i-1,page);
                }
                //进行下一页的刷新
                if(page[i+1]==undefined||page[i+1]==0){
                    postdata={examsid:examsid,quizid:quizid,index:(i+1),iserror:iserror,ran:Math.random()};
                    hw.getResponse('getExamsQuestion',postdata,$("#"+nxtpageitem),true,i+1,page);
                }

            }
            $("#quesindex").text(pageviews[i].index+1);
            //题干中有图片的情况的处理
            var objs=document.getElementsByClassName("ques")[i].getElementsByClassName("tigan");
            try{
                obj.find("img").css("width",(screenwidth)+"px");
            }catch(e){
                
            }
            
            //滑动区域的处理
            var bd = document.getElementById("leftTabBox").getElementsByClassName("bd")[0].getElementsByClassName("ques")[i];
            bd.style.width = document.body.clientWidth+"px";
            try{
                bd.getElementsByClassName("parent")[0].style.minHeight = document.body.clientHeight+"px";
            }catch(e){
                bd.style.height = document.body.clientHeight+"px";
            }

            bd.style.marginLeft = "-100px";
            if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
            try{
                bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
            }catch(e){
//                  bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
            }
            hwstopaudio(i);
            var muiindex="item"+i;
            var obj = document.getElementById(muiindex);//获取当前带滚动条的div对象
            obj.scrollTop=20; //设置滚动距离
            var data=page_tts[i];
            listendata=[];
            mui.each(data,function(k1,v1){
                var temp={};
                temp.name=v1.tts_mp3+".mp3";
                temp.url=exams_mp3_url+v1.tts_mp3+".mp3";
                temp.stoptime=v1.tts_stoptime;
                listendata.push(temp);
            });
            mp3.setListstyle(0);
            mp3.setPlaytimes(pagedata[i].parent.question.questions_playtimes);
            mp3.setParentobj($(".ques .audio-btn")[i]);
            mp3.setPlaylist(listendata);
            mask.close();
             //console.log(listendata);
        }
    });

});