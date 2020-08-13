require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax",
    },
    shim: {
        "zepto": {
            exports:"$"
        },
        'enajax': {
            deps: ['zepto'],
            exports: 'enajax'
        }
    },
    waitSeconds: 0

});
//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    var read = {}; //推荐方式  
    var readUnitdata = function(url,loc){
        getUnitData(url,loc);
    };
    var getReadListData=function(url,loc){
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                var examlist="";
                $.each(data,function(k,v){
                    //基础模块的添加
                    examlist=examlist+'<li href="readword?chapterid='+v.id+'&ks_code='+v.ks_code+'&moduleid='+Request["moduleid"]+'&ks_short_name='+Request["ks_short_name"]+'&chaptername='+encodeURI(encodeURI(v.chapter))+'&urlCallBack='+encodeURI(Requests["urlCallBack"])+'">';
                    examlist=examlist+'<p class="btnYuan radius100 record" style="display: block;float: left;vertical-align: none;"><a class="btnYuan radius100 record" style="padding: 0 0;"><i class="icon-kyxl"></i></a></p>';
                    if(v.maxscore==undefined||v.maxscore==null||v.maxscore=='null'){
                        examlist=examlist+'<p style="display: block;"><span style="display:block;">'+v.chapter+'</span><span style="display:block;">快去测试吧</span></p>';
                    }else{
                        examlist=examlist+'<p style="display: block;"><span style="display:block;color: #269bd7;">'+v.chapter+'</span><span style="display:block;color: #269bd7;">最高得分：'+parseFloat(v.maxscore).toFixed(1)+'分 '+v.addtime.substr(0,10)+'</span></p>';
                    }
                    examlist=examlist+'<p><span style="float:right;"><i class="icon-right"></i></span></p></li>';
                })
                $(loc).html(examlist);
                $("li").click(function(){
                    var href=$(this).attr("href");
                    window.location.href=href;
                });
            },
            error:function(xhr,type){

            }
        })
    }

    //获取课文列表
    var initTextList=function(url,obj){
        Request = GetRequest();
        var chapterid=Request["chapterid"];
        var ks_code=Request["ks_code"];
        $.ajax({
            type:'GET',
            url:url,
            data:{chapterid:chapterid,ks_code:ks_code},
            dataType:'json',
            async:false,
            timeout: 30000,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                //初始化MP3对象
                mp3.playInit(data.downlist,data.downlist,"icon-volume-up","icon-uniE60C");
                //初始化下载列表
                mp3.mp3dowload();
                $.each(data.data,function(k,v){
                    var attrarr=[];
                    var temp={};
                    temp.id="class";
                    temp.val="con readli";
                    attrarr.push(temp);
                    var parentdiv =initDom("<div></div>",attrarr);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="pad10";
                    attrarr.push(temp);
                    var div =initDom("<div></div>",attrarr);
                    parentdiv.append(div);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="playWord pad10 clear alound";
                    attrarr.push(temp);
                    temp={};
                    temp.id="mp3";
                    temp.val=v.mp3;
                    attrarr.push(temp);
                     temp={};
                    temp.id="name";
                    temp.val=v.mp3;
                    attrarr.push(temp);
                    var playdiv =initDom("<div></div>",attrarr);
                    div.append(playdiv);
                    // attrarr=[];
                    // temp={};
                    // temp.id="id";
                    // temp.val="pale";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="class";
                    // temp.val="icon-favorite-o coll";
                    // attrarr.push(temp);
                    // var i =initDom("<i></i>",attrarr);
                    // playdiv.append(i);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bFontCenter font1";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="font-size:1.3em;";
                    attrarr.push(temp);
                    if(v.chapterid!=undefined){
                        temp={};
                        temp.id="style";
                        temp.val="text-align:left;";
                        attrarr.push(temp);
                    }
                    span =initDom("<span></span>",attrarr);
                    span.html(v.encontent);
                    playdiv.append(span);
                    attrarr=[];
                    if(v.chapterid!=undefined){
                        temp={};
                        temp.id="style";
                        temp.val="display: block;text-align:left;";
                        attrarr.push(temp);
                    }else{
                        temp={};
                        temp.id="style";
                        temp.val="display: block;text-align:center;";
                        attrarr.push(temp);
                    }
                    temp={};
                    temp.id="class";
                    temp.val="textP";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.html(v.cncontent);
                    playdiv.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="cPlay istop";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="margin-top:0px;";
                    attrarr.push(temp);
                    span =initDom("<div></div>",attrarr);
                    playdiv.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="id";
                    temp.val="colorfulPulse";
                    attrarr.push(temp);
                    var img =initDom("<div></div>",attrarr);
                    img.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span><span class="item-5"></span><span class="item-6"></span><span class="item-7"></span><span class="item-8"></span><span class="item-9"></span><span class="item-10"></span><span class="item-11"></span><span class="item-12"></span><span class="item-13"></span><span class="item-14"></span>')
                    span.append(img);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="btnYuan radius100 read";
                    attrarr.push(temp);
                    // temp={};
                    // temp.id="mp3";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    //  temp={};
                    // temp.id="name";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.html('<i class="icon-uniE60C actY"></i>');
                    span.append(a);
                    attrarr=[];
                    temp={};
                    temp.id="id";
                    temp.val="colorfulPulse";
                    attrarr.push(temp);
                    var img =initDom("<div></div>",attrarr);
                    img.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span><span class="item-5"></span><span class="item-6"></span><span class="item-7"></span><span class="item-8"></span><span class="item-9"></span><span class="item-10"></span><span class="item-11"></span><span class="item-12"></span><span class="item-13"></span><span class="item-14"></span>')
                    span.append(img);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="pad10";
                    attrarr.push(temp);
                    div =initDom("<div></div>",attrarr);
                    parentdiv.append(div);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="playWord pad10 clear";
                    attrarr.push(temp);
                    playdiv =initDom("<div></div>",attrarr);
                    div.append(playdiv);
                    // attrarr=[];
                    // temp={};
                    // temp.id="class";
                    // temp.val="btnYuan radius100 record";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="style";
                    // temp.val="margin: auto;";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="contentid";
                    // temp.val=v.id;
                    // attrarr.push(temp);
                    // temp={};
                    // //判断是单词还是课文
                    // temp.id="type";
                    // temp.val="0";
                    // if(chapterid!='0'){
                    //     temp.val="1";
                    // }
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="content";
                    // temp.val=v.encontent;
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="readtimes";
                    // temp.val=v.readtimes;
                    // attrarr.push(temp);
                    // a =initDom("<a></a>",attrarr);
                    // a.html('<i class="icon-ico-yuyin"></i>');
                    // playdiv.append(a);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bFontCenter";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    if(v.score==""||v.score=="null"||v.score==null||v.score==undefined){
                       v.score=0; 
                    }
                    span.html('<font class="fontZ">'+v.score+'</font><font class="fonG">分</font>');
                    playdiv.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="textH4";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="text-align:center;";
                    attrarr.push(temp);
                    h4 =initDom("<h4></h4>",attrarr);
                    if(v.maxscore==""||v.maxscore=="null"||v.maxscore==null||v.maxscore==undefined){
                       v.maxscore=0; 
                    }
                    h4.html('目前最高测评成绩：'+v.maxscore+'分');
                    playdiv.append(h4);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="textH4";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="text-align:center;";
                    attrarr.push(temp);
                    h4 =initDom("<h4></h4>",attrarr);
                    if(v.readtimes==""||v.readtimes=="null"||v.readtimes==null||v.readtimes==undefined){
                       v.readtimes=3; 
                    }
                    h4.html('你还有'+v.readtimes+'次测评机会');
                    playdiv.append(h4);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="ly-panX";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    //span.html('<i class="icon-error02 fa-2x"></i>');
                    playdiv.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="luyinD";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="line-height:40px; text-align:center; font-size:1.2em; color:#aaa; margin-top:10px;";
                    attrarr.push(temp);
                    div =initDom("<div></div>",attrarr);
                    if(v.readtimes==0){
                        div.html("&nbsp;");
                    }else{
                        div.text("点击录音");
                    }
                    playdiv.append(div);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="clearfix";
                    attrarr.push(temp);
                    div =initDom("<div></div>",attrarr);
                    playdiv.append(div);
                    //<div class="cPlay istop " style="margin-top: 20px;"><div id="colorfulPulse"><span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span></div><a style="margin-left: 20px;">00:17</a><div id="colorfulPulse"><span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span></div></div><a class="btnYuan radius100 record" style="margin: auto;" contentid="24810" type="0" content="mother" readtimes="3"><i class="icon-ico-yuyin"></i></a>
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="text-align: center;margin-top:10px;display:none;";
                    attrarr.push(temp);
                    temp={};
                    temp.id="class";
                    temp.val="istop recordtime";
                    attrarr.push(temp);
                    span =initDom("<div></div>",attrarr);
                    playdiv.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="id";
                    temp.val="colorfulPulse";
                    attrarr.push(temp);
                    var img =initDom("<div></div>",attrarr);
                    img.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span>')
                    span.append(img);
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="margin-left:10px;margin-right:10px;";
                    attrarr.push(temp);
                    // temp={};
                    // temp.id="mp3";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    //  temp={};
                    // temp.id="name";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    //a.html('00:00');
                    span.append(a);
                    attrarr=[];
                    temp={};
                    temp.id="id";
                    temp.val="colorfulPulse";
                    attrarr.push(temp);
                    var img =initDom("<div></div>",attrarr);
                    img.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span>')
                    span.append(img);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    if(v.readtimes==0){
                        temp.val="btnYuan radius100 record bGray";
                    }else{
                        temp.val="btnYuan radius100 record";
                    }
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="margin: auto;";
                    attrarr.push(temp);
                    temp={};
                    temp.id="contentid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    temp={};
                    //判断是单词还是课文
                    temp.id="type";
                    temp.val="0";
                    if(chapterid!='0'){
                        temp.val="1";
                    }
                    attrarr.push(temp);
                    temp={};
                    temp.id="content";
                    temp.val=v.encontent;
                    attrarr.push(temp);
                    temp={};
                    temp.id="readtimes";
                    temp.val=v.readtimes;
                    attrarr.push(temp);
                    a =initDom("<a></a>",attrarr);
                    // temp={};
                    // temp.id="mp3";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    //  temp={};
                    // temp.id="name";
                    // temp.val=v.mp3;
                    // attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.html('<i class="icon-ico-yuyin"></i>');
                    playdiv.append(a);

                    parentdiv.appendTo(obj);
                });
                //分页插件初始化
                mp3.setCurindex(0);
                playlist=mp3.getPlaylist();
                //设置总页数
                $("#count").text($(".readli").length);
                TouchSlide({ 
                    slideCell:"#iScroll",
                    startFun:function(i){
                        page=i;
                        //音频停止
                        record.setFlag(0);
                        // var playstatus=$(".readli").eq(i).find(".cPlay").hasClass("isplay");
                        // if(playstatus){
                        try{
                              UXinJSInterfaceSpeech.stopAudio();  
                        }catch(e){

                        }
                        $(".cPlay.iplay").addClass("istop").removeClass("iplay");
                        $(".recordtime").find("a").html("00:00");
                        try{window.clearInterval(interid);}catch(e){}
                        
                        //将正在录的音取消
                        if($(".readli").find(".recordtime.iplay").length>0){
                            $(".readli").find(".recordtime.iplay").parents(".playWord").find(".record").find("i").addClass("icon-playt").removeClass("icon-ico-yuyin");
                            $(".recordtime.iplay").find("a").html("00:00");
                            $(".recordtime.iplay").hide();
                            $(".recordtime.iplay").addClass("istop").removeClass("iplay");
                            
                            record.pauseVoice();
                        }
                        //$(".recordtime").hide();
                        $("#cur").text(i+1);
                        //$("#chapter").text($(".text").eq(i).parent().parent().attr("chapter"));
                        var ul= $("#iScroll-bd").find(".readli").eq(i).parent();
                        //获取屏幕的高度
                        var height=window.screen.height;
                        if(ul.height()<height){
                            ul.css("height",height+"px");
                        }
                        //获取总页数
                        var count=$(".con.readli").length;
                        if(i==(count-1)){
                            $(".fenT").css("background-color","#00bdc7");
                            $(".fenT").addClass("finish");
                        }else{
                            $(".fenT").css("background-color","#999;");
                            $(".fenT").removeClass("finish");
                        }
                        $(".record").removeClass("finish");
                        
                    },
                    endFun:function(i){
                        var arr=[];
                        arr.push(playlist[i]);
                        mp3.setPlaylist(arr);
                        console.log(arr);
                        mp3.setParentobj($("a.btnYuan.radius100 i").eq(i));
                        //高度自适应
                        var bd = document.getElementById("iScroll-bd");
                        
                        //设置当前的朗读样式
                        $(".readli").removeClass("recorecur");
                        $(".readli").eq(i).addClass("recorecur");  
                        $("#wrapper").resize();
                        //读音
                        var obj=$(".con.readli").eq(i).find(".playWord").eq(0);
                        mp3.setUse(0);
                        var mp3url=$(obj).attr("mp3");
                        //判断函数是否存在
                        var exitstatus=isExitsFunction("UXinJSInterfaceSpeech.playAudio");
                        if(exitstatus==-1){
                            setTip("请下载优教信使客户端");
                        }else if(exitstatus==1){
                            //判断是什么状态
                            var status=$(obj).find(".cPlay").hasClass("iplay");
                            if(status){
                                $(obj).find(".cPlay").addClass("istop").removeClass("iplay");
                                try{
                                    UXinJSInterfaceSpeech.stopAudio();  
                                }catch(e){
                                    setTip("请升级到最新的客户端");
                                }
                            }else{
                                try{
                                    $(obj).find(".cPlay").addClass("iplay").removeClass("istop");
                                    //$(this).parent().find("img").attr("src","__SUBJECT__/img/timg.gif");
                                    // $("img.curimg").removeClass("curimg");
                                    // $(this).parent().find("img").addClass("curimg");
                                    UXinJSInterfaceSpeech.playAudio(mp3url);
                                }catch(e){
                                    setTip("请升级到最新的客户端");
                                }
                            }
                        }else{
                            setTip("使用最新的客户端进行播放");
                        }
                    }
                });
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    var getUserReadData=function(url,obj){
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        var chapterid=Request["chapterid"];
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code,chapterid:chapterid},
            dataType:'json',
            async:false,
            timeout: 30000,
            context:$('body'),
            success: function(data){
                var score=0;
                var donum=0;
                var dotime=0;
                var word="";
                var wordnum=0;
                //遮罩消失
                hideloading();
                //初始化MP3对象
                mp3.playInit(data.downlist,data.downlist,"icon-volume-up","icon-uniE60C");
                mp3.mp3dowload();
                //下载用户的音频
                mp3.playInit(data.userlist,data.userlist,"icon-volume-up","icon-uniE60C");
                mp3.mp3dowload();
                $(obj).empty();
                $.each(data.data,function(k,v){
                    //wordnum=wordnum+1;
                    donum=v.count;
                    dotime=v.dotime==''?0:parseInt(v.dotime);
                    var attrarr=[];
                    var temp={};
                    temp.id="style";
                    temp.val="table-layout: fixed;";
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="btnYuan radius100 record";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="width:68px;";
                    attrarr.push(temp);
                    var p =initDom("<p></p>",attrarr);
                    li.append(p);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    if(parseInt(v.score)<0){
                        temp.val="ball_k";
                    }else if(parseInt(v.score)<60){
                        temp.val="ball_r";
                    }else if(parseInt(v.score)>=60){
                        temp.val="ball_l";
                    }
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.text(k+1);
                    p.append(a);
                    // attrarr=[];
                    // temp={};
                    // temp.id="class";
                    // temp.val="icon-ico-yuyin";
                    // attrarr.push(temp);
                    // var i =initDom("<i></i>",attrarr);
                    // a.append(i);
                    //<p style="display: block;line-height: 30px;"><span style="display:block;">hello</span></p>
                    attrarr=[];
                    // temp={};
                    // temp.id="style";
                    // temp.val="display: block;margin-left:-10px;";
                    // attrarr.push(temp);
                    p =initDom("<p></p>",attrarr);
                    li.append(p);
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="display:block;overflow: hidden;text-overflow: ellipsis;height: 40px;line-height: 40px;white-space: nowrap;";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text(v.content);
                    p.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="display:block;font-size:80%;";
                    attrarr.push(temp);
                    wordnum=wordnum+1;
                    var span =initDom("<span></span>",attrarr);
                    if(v.score<0){
                        span.text("您未作答");
                    }else{
                        
                        span.text("得分"+parseFloat(v.score).toFixed(1)+"分");
                        score=score+parseFloat(v.score);
                    }
                    p.append(span);
                    //<p><span style="float:right;color: #333;font-size: 120%;"><i class="icon-ico-yuyin"></i></span><span style="/* float:right; */color: #333;font-size: 120%;"><i class="icon-sound"></i></span></p>
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="width: 100px;"
                    attrarr.push(temp);
                    p =initDom("<p></p>",attrarr);
                    li.append(p);
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="float:right;color: #333;font-size: 120%;";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    p.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-ico-yuyin";
                    attrarr.push(temp);
                    temp={};
                    temp.id="mp3url";
                    temp.val=v.filename;
                    attrarr.push(temp);
                    i =initDom("<i></i>",attrarr);
                    span.append(i);
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="float:right;color: #333;font-size: 120%;";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    p.append(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-sound";
                    attrarr.push(temp);
                    temp={};
                    temp.id="mp3url";
                    temp.val=v.mp3;
                    attrarr.push(temp);
                    i =initDom("<i></i>",attrarr);
                    span.append(i);
                    // attrarr=[];
                    // temp={};
                    // temp.id="class";
                    // if(parseInt(v.score)<0){
                    //     temp.val="ball_k";
                    // }else if(parseInt(v.score)<60){
                    //     temp.val="ball_r";
                    // }else if(parseInt(v.score)>=60){
                    //     temp.val="ball_l";
                    // }
                    // attrarr.push(temp);
                    // var a =initDom("<a></a>",attrarr);
                    // var score=parseInt(v.score);
                    // if(score==-1){
                    //     score=0;
                    // }
                    // a.text(score+"分");
                    // a.bind("tap",function(){
                    //     //播放客户音频
                    //     alert("asfasdfasd");
                    // })
                    // li.append(a);
                    li.appendTo(obj);
                });
                //总分以及汇总数据
                if(wordnum==0){
                    $(".score").text("0");
                }else{
                    $(".score").text((score/wordnum).toFixed(1));
                }
                
                $(".donum").text(donum+"次");
                if(dotime==''){
                    dotime=0;
                }
                var timesvalue=0;
                var min = parseInt(dotime / 60);// 分钟数
                var secs = dotime % 60;
                if(min==0){
                    timesvalue="";
                }else{
                    timesvalue=min+"分";
                }
                if(secs==0){
                    timesvalue=timesvalue;
                }else{
                    timesvalue=timesvalue+secs+"秒";
                }
                $(".dotime").text(timesvalue);
            },
            error:function(xhr,type){

            }
        })
    }

    var getUserHistoryData=function(url,loc){
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code},
            dataType:'json',
            async:false,
            timeout: 30000,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                var examlist="";
                $.each(data,function(k,v){
                    //基础模块的添加
                    examlist=examlist+'<li href="readword?chapterid='+v.id+'&ks_code='+v.ks_code+'&moduleid='+Request["moduleid"]+'&ks_short_name='+Request["ks_short_name"]+'&chaptername='+encodeURI(encodeURI(v.chapter))+'">';
                    examlist=examlist+'<p class="btnYuan radius100 record" style="display: block;float: left;vertical-align: none;"><a class="btnYuan radius100 record" style="padding: 0 0;"><i class="icon-ico-yuyin"></i></a></p>';
                    if(v.maxscore==undefined||v.maxscore==null||v.maxscore=='null'){
                        examlist=examlist+'<p style="display: block;"><span style="display:block;">'+v.chapter+'</span><span style="display:block;">快去测试吧</span></p>';
                    }else{
                        examlist=examlist+'<p style="display: block;"><span style="display:block;">'+v.chapter+'</span><span style="display:block;">'+v.addtime.substr(0,10)+' 最高得分：'+v.maxscore+'分</span></p>';
                    }
                    examlist=examlist+'<p><span style="float:right;"><i class="icon-right"></i></span></p></li>';
                })
                $(loc).html(examlist);
                $("li").click(function(){
                    var href=$(this).attr("href");
                    window.location.href=href;
                });
            },
            error:function(xhr,type){

            }
        })
    }


    read.readUnitdata=readUnitdata;
    read.getReadListData=getReadListData;
    read.initTextList=initTextList;
    read.getUserReadData=getUserReadData;
    read.getUserHistoryData=getUserHistoryData;
    return read;
});