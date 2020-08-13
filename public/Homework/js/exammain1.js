require.config({
　baseUrl: "../../public/Homework/js",
　paths: {
　　　"jquery": "jquery-1.7.2.min",
      "player": "jquery.jplayer.min",
　　　"mui": "mui.min",
      "view": "mui.view"
　},
  shim:{  
        mui:{  
              deps:[],  
              exports: 'mui'  
        },  
        view:{  
              deps:['mui'],
              exports: 'view'  
        }  
    }  
});

require(['jquery','mui',"mobile","touchslide","view","setContentuxin"], function($,mui,mobile,touchslide,view,hw){
    //进行时间的展示
    //if(issubmit=='0'&&type=='0'){
    mui.init({
            swipeBack:false //启用右滑关闭功能
        });
        
    mui.back=function(){
        try{
            $("#playing").attr("src","../../public/Homework/images/sy.png");
            $("#playing").parent().removeClass("playing");
        }catch(e){

        }
        stopaudio();
        try{
            $("#playing").attr("id","");
        }catch(e){

        }
      //表示学生进去
      if(type=="0"&&issubmit==0){
        popTheController();
        //var url=callbackURL;
        //mui.openWindow(url);
      }else if(type=="1"&&issubmit==0){
        //返回到教师的列表页面
        var url=callbackURL;
        mui.openWindow(url);
      }else if(issubmit==1&&type=="0"){
        mui.openWindow('finish?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid);
      }else{
        window.location.href='feedback?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid;
      }
    }
    var c=parseInt(time);

    //timedCount();
    if(issubmit==0&&studentid!="0"&&isOverdue=='false'){
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
                //$.getJSON("editHomeworkTime",{homeworkid:homeworkid,studentid:studentid,classid:classid});
            },1000)
    }
    //}
    //动态创建div
    var create=function(b,t) {
        var c = document.createElement("div");
        c.innerHTML = t,
        c.setAttribute("class",b);
        return c
    }
    //显示遮罩
    var show=function(a,ta,b,tb){
        var ttip=create(a,ta);
        var ttip_wz=create(b,tb);
        $(ttip).attr("style","background-color:#333; display:block; position:absolute; top: 0; left:0; color:#fff;  opacity:0.7; width:100%; display:none; box-shadow: 1px 1px 5px #555; text-align:center; z-index:1000;");
        $(ttip_wz).attr("style","width:100%;text-align:center;position:absolute; color:#fff; top: 40%; padding: 0 30px; z-index: 1001; font-size: 1.2em; line-height: 2em;");
        $(ttip).css('height',$(window).height());
        console.log(ttip);
        document.body.appendChild(ttip);
        document.body.appendChild(ttip_wz);
        $(ttip).fadeIn(300).delay(1500).fadeOut(300);
        $(ttip_wz).fadeIn(300).delay(1500).fadeOut(300); 
    }

    
    //view单页视图
    var viewApi = mui('#app').view({
        defaultPage: '#setting'
    });
    //初始化单页的区域滚动
    mui('.mui-scroll-wrapper').scroll();
    var view = viewApi.view;
    console.log(view);
    var mask = mui.createMask();


    var getQuestionSummary=function(id){
        var url="getSummary";
        var summaryhtml="";
        mui.ajax(url,
            {
            data:{
                studentId:studentid,
                classId:classid,
                homeworkid:id,
                iserror:iserror,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:false,
            success:function(data){
                var wscount=0;
                var wccount=0;
                var wrcount=0;
                var eqcount=0;
                var wacount=0;
                var tacount=0;
                if(data.wa.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词朗读<p>";
                    mui.each(data.wa,function(k,v){
                        wacount=wacount+1;
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }else{
                            if(v.score!=undefined&&v.score!=''&&v.score!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }
                    });
                }
                var eqquesid="";
                if(data.ws.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词拼写<p>";
                    mui.each(data.ws,function(k,v){
                        wscount=wscount+1;
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }else{
                            if(v.isdo!=''&&v.isdo!='null'&&v.score>0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else if(v.isdo!=''&&v.isdo!='null'&&v.score==0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style="background-color:#FE5A59;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
                            }
                        }
                    });
                }
                if(data.wc.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;听音选词<p>";
                    mui.each(data.wc,function(k,v){
                        wccount=wccount+1;
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }else{
                            if(v.isdo!=''&&v.isdo!='null'&&v.score>0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+k)+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else if(v.isdo!=''&&v.isdo!='null'&&v.score==0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+k)+'"  class="quessummary" style="background-color:#FE5A59;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+k)+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
                            }
                        }
                    });
                }
                if(data.wr.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;英汉互译<p>";
                    mui.each(data.wr,function(k,v){
                        wrcount=wrcount+1;
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }else{
                            if(v.isdo!=''&&v.isdo!='null'&&v.score>0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else if(v.isdo!=''&&v.isdo!='null'&&v.score==0){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'"  class="quessummary" style="background-color:#FE5A59;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
                            }
                        }
                    });
                }
                if(data.ta.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;课文朗读<p>";
                    mui.each(data.ta,function(k,v){
                        tacount=tacount+1;
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }else{
                            if(v.score!=undefined&&v.score!=''&&v.score!='null'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
                            }
                        }
                    });
                }
                var eqquesid="";
                if(data.eq.length>0){
                    //服务器返回然后进行h5的本地存储
                    summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;听力训练<p>";
                    var kindex=0;
                    mui.each(data.eq,function(k,v){
                        if(v.id!=eqquesid){
                            eqquesid=v.quesid;
                            kindex=kindex+1;
                        }
                        i=(k+1);
                        if(issubmit=='0'){
                            if(v.score=='1'||v.score=='0'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wscount+wccount+wrcount+wacount+tacount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wscount+wccount+wrcount+wacount+tacount+kindex-1)+'"><span class="mui-icon" style="colar:black">'+i+'</span></a>';
                            }
                        }else{
                            if(v.isdo!=''&&v.isdo!='null'&&v.score=='1'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wscount+wccount+wrcount+wacount+tacount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                        }else if(v.isdo!=''&&v.isdo!='null'&&v.score=='0'){
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wscount+wccount+wrcount+wacount+tacount+kindex-1)+'" style="background-color:#FE5A59;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
                            }else{
                                summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wscount+wccount+wrcount+wacount+tacount+kindex-1)+'" style=""><span class="mui-icon" style="colar:black">'+i+'</span></a>';
                            }
                        }
                    });
                }
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
        if(type=="0"){
            document.getElementById("datika").setAttribute("href","javascript:void(0);");
            document.getElementById("datika").addEventListener("click",function(){
                var url='finish?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid;
                mui.openWindow(url);
            })
        }else{
            document.getElementById("datika").setAttribute("href","javascript:void(0);");
            document.getElementById("datika").addEventListener("click",function(){
                var url='feedback?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid;
                mui.openWindow(url);
            })
        } 
    }else{
        var oldBack = mui.back;
        mui.back = function() {
            if (viewApi.canBack()) { //如果view可以后退，则执行view的后退
                viewApi.back();
                if(pageindex==(parseInt(wacount)+parseInt(wscount)+parseInt(wccount)+parseInt(wrcount)+parseInt(tacount)+parseInt(eqcount))){

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
            $("#playing").attr("src","../../public/Homework/images/sy.png");
            $("#playing").parent().removeClass("playing");
        }catch(e){

        }
        stopaudio();
        try{
            $("#playing").attr("id","");
        }catch(e){

        }
        document.getElementById("accountcontent").innerHTML="";
        if(e.detail.page.id=="account"){
            document.getElementById("datika").style.display="";
            summaryhtml="";
            if(issubmit=='0'&&isOverdue=="false"){
                document.getElementById("timeshow").style.display="none";
                document.getElementsByTagName("nav")[0].style.display="";
            }else{
                document.getElementById("suwt").style.marginBottom="0px";
            }
            var summaryhtml=getQuestionSummary(homeworkid);
            document.getElementById("accountcontent").innerHTML=summaryhtml;
            document.getElementById("accountcontent").style.marginTop="0px";
        }else{
            document.getElementById("accountcontent").innerHTML="";
            if(issubmit=='0'&&type=='0'){
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
                    studentid:studentid,
                    classid:classid,
                    batchid:batchid,
                    homeworkid:hwid,
                    paper_id:homeworkid,
                    tms:tms,
                    time:c,
                    starttime:starttime,
                    ran:Math.random()
                },
                dataType:'json',//服务器返回json格式数据
                type:'post',//HTTP请求类型
                timeout:10000,//超时时间设置为10秒；
                async:true,
                success:function(data){
                    //服务器返回然后进行h5的本地存储
                    if(data.state="1"){
                        mask.close();
                        mui.openWindow('finish?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid+'&type=0');
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
            url=url+"&homeworkid="+homeworkid+"&issubmit="+issubmit+"&time="+c+"&iserror="+iserror+"&starttime="+starttime+"&batchid="+batchid+"&hwid="+hwid+"&tms="+tms+"&studentId="+studentid+"&classId="+classid+"&type=2&callbackURL="+callbackURL+"&isOverdue="+isOverdue;
            window.location.href=url;
        }else{
            url=url+"&homeworkid="+homeworkid+"&issubmit="+issubmit+"&time="+c+"&iserror="+iserror+"&starttime="+starttime+"&batchid="+batchid+"&hwid="+hwid+"&tms="+tms+"&studentId="+studentid+"&classId="+classid+"&type="+type+"&callbackURL="+callbackURL+"&isOverdue="+isOverdue;
            mui.openWindow(url);
        }
    });


    //停止MP3播放
    function stopaudio(){
        try{
            clearTimeout(mp3_progress);
            clearTimeout(mp3_progress_reap);
        }catch(e){
            console.log(e);
        }
        try{
            mp.pause();
        }catch(e){
            console.log(e);
        }
        mp3_progress='';
        mp3_progress_reap='';
        mp.index = 0;
        mp.stemindex = 0;
        mp.queinitindex = 0;
        mp.questionindex = 0;
        mp.childstemindex = 0;
        mp.playtimes=0;
        mp.childinitstemindex = 0;
        mp.url = "";
        mp.repeat = 1; //默认播放次数
        mp.curpeat = 1;//当前播放到第几次
        mp.url = "";
    }

    //组合试题音频处理
    function hwstopaudio(i){
        var perpage=0;
        try{
            if(i==0||pageslider[i-1]==undefined){
                perpage=0;
            }else{
                perpage=pageslider[i-1];
            }
        }catch(e){
        }
        var nxtpage=0;
        try{
            if(pageslider[i+1]==undefined){
                nxtpage=0;
            }else{
                nxtpage=pageslider[i+1];
            }
        }catch(e){

        }
        pageslider[i]=perpage>nxtpage?perpage+1:nxtpage+1;
        if(page_obj[i].type!=1){
            try{
                $("#playing").attr("src","../../public/Homework/images/sy.png");
                $("#playing").parent().removeClass("playing");
            }catch(e){

            }
            stopaudio();
            try{
                $("#playing").attr("id","");
            }catch(e){

            }
            //return 1;
        }else{
            if(page_obj[i].type==0){
                stopaudio();
                //return 1;
            }else if(page_obj[i].type==1){
                console.log("ccccccccc");
                //组合试题从哪里过来的
                try{
                    $("#playing").attr("src","../../public/Homework/images/sy.png");

                }catch(e){
                    console.log("meishaosao");
                }
                if(pageslider[i]==(pageslider[i-1]+1)){
                    //表示从左边过来的数据判断左边的情况需要将昨天的那个直接停止了
                    if(page_obj[i].stemid!=page_obj[i-1].stemid){
                        try{
                            $("#playing").parent().removeClass("playing");
                        }catch(e){

                        }

                        console.log("left left left left");
                        try{
                            $("#playing").attr("id","");
                            //设置当前的是正在播放的情况
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.png");
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");
                            
                        }catch(e){
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.png");
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");
                        }
                        stopaudio();
                        //return 1;
                    }else{
                        console.log("left left left leftkaishi");
                        try{
                            //设置当前的是正在播放的情况
                            console.log(document.getElementById("playing"));
                            if($("#playing").parent().hasClass("playing")){
                                try{
                                    console.log("aaaaaa");
                                    $("#playing").parent().removeClass("playing");
                                }catch(e){
                                    
                                }
                                $("#playing").attr("id","");
                                //设置当前的是正在播放的情况
                                $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.gif");
                                $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");
                                $(".ques").eq(i).find(".sy_click").eq(0).parent().addClass("playing");
                            }
                        }catch(e){
                             console.log("aaaerror");
                        }
                        //return 0;

                    }

                }else if(pageslider[i]==(pageslider[i+1]+1)){
                    //表示从左边过来的数据判断右边的情况
                    if(page_obj[i].stemid!=page_obj[i+1].stemid){
                        try{
                            removeClass(document.getElementById("playing").parentNode,"playing");
                        }catch(e){

                        }
                        try{
                            $("#playing").attr("id","");
                            //设置当前的是正在播放的情况
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.png");
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");
                        }catch(e){
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.png");
                            $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");

                        }
                        stopaudio();
                        //return 1;
                    }else{
                        try{
                            //设置当前的是正在播放的情况判断目前的上一个是不是在播放状态
                            if($("#playing").parent().hasClass("playing")){
                                try{
                                    console.log("aaaaaa");
                                    $("#playing").parent().removeClass("playing");
                                }catch(e){
                                    
                                }
                                $("#playing").attr("id","");
                                //设置当前的是正在播放的情况
                                $(".ques").eq(i).find(".sy_click").eq(0).attr("src","../../public/Homework/images/sy.gif");
                                $(".ques").eq(i).find(".sy_click").eq(0).attr("id","playing");
                                $(".ques").eq(i).find(".sy_click").eq(0).parent().addClass("playing");
                            }
                        }catch(e){

                        }
                        //return 0;
                    }
                }
            }
        }
    }

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
            pageindex=i;
            //翻页的时候停止播放音频如果是单体的话 就停止 组合试题的话不停止

            if(i==(c-1)&&issubmit=='0'){
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
            console.log(pageviews);
            if(i==0){
                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={studentId:studentid,classId:classid,index:i,type:pageviews[i].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+pageitem),false,i,page); 
                }
                //进行下一页的刷新
                try{
                    if(page[i+1]==undefined||page[i+1]==0){
                        postdata={studentId:studentid,classId:classid,index:(i+1),type:pageviews[i+1].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                        hw.getResponse('getHomeworkQuestion',postdata,$("#"+nxtpageitem),true,i+1,page);
                    }
                }catch(e){
                    
                }
                
            }else if(i==(quescount-1)){
                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={studentId:studentid,classId:classid,index:i,type:pageviews[i].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+pageitem),false,i,page);
                }
                //进行上一页的刷新
                if(page[i-1]==undefined||page[i-1]==0){
                    postdata={studentId:studentid,classId:classid,index:(i-1),type:pageviews[i-1].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+prepageitem),true,i-1,page);
                }
            }else{

                //首页将进行下一页的刷新
                if(page[i]==undefined||page[i]==0){
                    postdata={studentId:studentid,classId:classid,index:i,type:pageviews[i].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+pageitem),false,i,page);
                }
                //进行上一页的刷新
                if(page[i-1]==undefined||page[i-1]==0){
                postdata={studentId:studentid,classId:classid,index:(i-1),type:pageviews[i-1].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+prepageitem),true,i-1,page);
                }
                //进行下一页的刷新
                if(page[i+1]==undefined||page[i+1]==0){
                    postdata={studentId:studentid,classId:classid,index:(i+1),type:pageviews[i+1].questype,homeworkid:homeworkid,wscount:wscount,wccount:wccount,wrcount:wrcount,wacount:wacount,tacount:tacount,iserror:iserror,ran:Math.random()};
                    hw.getResponse('getHomeworkQuestion',postdata,$("#"+nxtpageitem),true,i+1,page);
                }

            }
            $("#quesindex").text(pageviews[i].index+1);
            if(pageviews[i].type=='wa'){
                if(pageviews[i].index=='0'){
                    show("ttip","","ttip_wz","下面将开始单词跟读");
                    document.getElementById("type").innerHTML="单词跟读";
                    $("#quesnumcount").text(wacount);
                }else{
                    document.getElementById("type").innerHTML="单词跟读";
                    $("#quesnumcount").text(wacount);
                }   
            }else if(pageviews[i].type=='ws'){
                if(pageviews[i].index=='0'){
                    document.getElementById("type").innerHTML="单词拼写";
                    $("#quesnumcount").text(wscount);
                    show("ttip","","ttip_wz","下面将开始单词拼写");
                }else{
                    document.getElementById("type").innerHTML="单词拼写";
                    $("#quesnumcount").text(wscount);
                } 
                
            }else if(pageviews[i].type=='wc'){
                if(pageviews[i].index=='0'){
                    show("ttip","","ttip_wz","下面将开始听音选词");
                    document.getElementById("type").innerHTML="听音选词";
                    $("#quesnumcount").text(wccount);
                }else{
                    document.getElementById("type").innerHTML="听音选词";
                    $("#quesnumcount").text(wccount);
                } 
                
            }else if(pageviews[i].type=='wr'){
                if(pageviews[i].index=='0'){
                    show("ttip","","ttip_wz","下面将开始英汉互译");
                    document.getElementById("type").innerHTML="英汉互译";
                    $("#quesnumcount").text(wrcount);
                }else{
                    document.getElementById("type").innerHTML="英汉互译";
                    $("#quesnumcount").text(wrcount);
                } 
                
            }else if(pageviews[i].type=='ta'){
                if(pageviews[i].index=='0'){
                    show("ttip","","ttip_wz","下面将开始课文跟读");
                    document.getElementById("type").innerHTML="课文跟读";
                    $("#quesnumcount").text(tacount);
                }else{
                    document.getElementById("type").innerHTML="课文跟读";
                    $("#quesnumcount").text(tacount);
                } 
                
            }else if(pageviews[i].type=='eq'){
                if(pageviews[i].index=='0'){
                    show("ttip","","ttip_wz","下面将开始听力训练");
                    document.getElementById("type").innerHTML="听力训练";
                    console.log(page_tts[pageindex]);
                    $("#quesnumcount").text(eqcount);
                }else{
                    document.getElementById("type").innerHTML="听力训练";
                    console.log(page_tts[pageindex]);
                    $("#quesnumcount").text(eqcount);
                } 
                
            }
            // //题干中有图片的情况的处理
            // if(pageviews[i].type=='eq'){
            //     var objs=document.getElementById(pageitem).getElementsByClassName("tigan");
            //     console.log(objs);
            //     mui.each(objs,function(){
            //         try{
            //             if(!$(this).hasClass("over")){
            //                 var imgs=this.getElementsByTagName("img");
            //                 mui.each(imgs,function(key,value){
            //                         var currimg=this;
            //                         var image = new Image();
            //                         image.src = this.src;
            //                         var naturalWidth=0;
            //                         var naturalHeight=0;
            //                         image.onload = function(){
            //                             var _stemp = this;
            //                             naturalWidth=_stemp.width;
            //                             naturalHeight=_stemp.height;
            //                             console.log(screenwidth);
            //                             if(naturalWidth>(screenwidth-10)){
            //                                 currimg.style.width=(screenwidth)-10+"px";
            //                                 currimg.style.height=(screenwidth*naturalHeight/naturalWidth)+10+"px";
            //                             }
            //                         }

            //                 });
            //                 $(this).addClass("over");
            //             }  
            //         }catch(e){
            //         }
            //     });
            // }else{
            //     var objs=document.getElementById(pageitem).getElementsByClassName("tigan");
            //     console.log(objs);
            //     mui.each(objs,function(){
            //         console.log(this);
            //         try{
            //             if(!$(this).hasClass("over")){
            //                 var imgs=this.getElementsByTagName("img");
            //                 mui.each(imgs,function(key,value){
            //                         var currimg=this;
            //                         var image = new Image();
            //                         image.src = this.src;
            //                         var naturalWidth=0;
            //                         var naturalHeight=0;
            //                         image.onload = function(){
            //                             var _stemp = this;
            //                             naturalWidth=_stemp.width;
            //                             naturalHeight=_stemp.height;
            //                             console.log(screenwidth);
            //                             //if(naturalWidth>(screenwidth-10)){
            //                                 currimg.style.width=(screenwidth)-10+"px";
            //                                 currimg.style.height=(screenwidth*naturalHeight/naturalWidth)+10+"px";
            //                             //}
            //                         }

            //                 });
            //                 $(this).addClass("over");
            //             }  
            //         }catch(e){
            //         }
            //     });
            // }
            

            var stopflag=hwstopaudio(i);
            //进行音频的自动播放
            if(stopflag==1&&startflag){
                var muiindex="item"+i;
                try{
                    var playbtn=document.getElementById(muiindex).getElementsByClassName("lanren")[0].getElementsByTagName("a")[0];
                    startflag=true;
                    //防止没有加载完成
                    setTimeout(function(){
                        playbtn.click();
                    },500);
                }catch(e){

                }
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
                bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth-20+"px";
            }catch(e){
//                  bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
            }
            var muiindex="item"+i;
            var obj = document.getElementById(muiindex);//获取当前带滚动条的div对象
            console.log(obj.scrollTop);
            obj.scrollTop=20; //设置滚动距离
            console.log(obj.scrollTop);      
        }
    });

});
