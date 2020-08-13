require.config({
　baseUrl: "../../public/public/js",
　paths: {
　　"zepto": "zepto.min",
    "enajax": "enajax",
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

});
//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    var learnWord = {}; //推荐方式 
    var learnWordUnitdata = function(url,loc){
        getUnitData(url,loc);
    };
    
    //初始化单词学习的列表
    var initStudyList=function(url,listobj,imgobj){
        var Request = new Object();
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        var iswords=0;
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code,ran:Math.random()},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                //初始化MP3对象
                mp3.playInit(data,data,"","cur");
                //初始化下载列表
                mp3.setPlaystatus(1);
                mp3.mp3dowload();
                mp3wordlist=mp3.getPlaylist();
                $.each(data,function(k,v){
                    //动态创建单词的元素
                    if(v.isword==0){
                        iswords=1;
                    }
                    var attrarr=[];
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(listobj);
                    attrarr=[];
                    var temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="mp3";
                    temp.val=v.ukmp3;
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:inline-block;width:100%;";
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.bind("click",function(e){
                        //屏幕的总高度
                        var sheight=window.screen.availHeight;
                        var min=88;
                        var max=sheight-48;
                        //当前元素的高度
                        var top=$(this).offset().top;
                        var left=$(this).offset().left;
                        var height=$(this).offset().height;
                        //滑动的距离
                        var scroly=$('#scroller').css('transform').split("t")[2].replace(/[^0-9\-,]/g,'').split(',')[1];
                        //计算相同的距离上面的高度
                        var lis=$(this).parents('ul').find("li");
                        var lastli=lis[lis.length-1];
                        var firstli=lis[0];
                        var maxtop=$(lastli).offset().top;
                        var mintop=$(firstli).offset().top;
                        
                        //判断当前的元素和中间界限的高度
                        // if(top>(height/2)){
                        //     if((maxtop-top)<height/2){
                        //         $("#scroller").css('transform','translate(0px, -'+(top-height/2)+'px)');
                        //     }else{
                        //         $("#scroller").css('transform','translate(0px, -'+(height/2)+'px)');
                        //     }
                        // }
                        //点击的时候进行样式的改变
                        $(this).parents("li").addClass("cur");
                        $(this).parents("li").siblings().removeClass("cur");
                        $("#audioplay").addClass("stop").removeClass("play");
                        $("#audioplay").find("font").text("连读");
                        
                        $("#audioplay").find("i").addClass("icon-uniE60C").removeClass("icon-playt");
                        //进行播放控制
                        var index = $(this).parent().index();
                        var name=mp3wordlist[index].name;
                        try{UXinJSInterfaceSpeech.stopAudio();}catch(e){}
                        try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio(name);}catch(e){setTip('请升级到最新的优信');}
                    });
                    a.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon";
                    attrarr.push(temp);
                    var p =initDom("<p></p>",attrarr);
                    p.appendTo(a);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-uniE60C actY";
                    attrarr.push(temp);
                    temp={};
                    temp.id="contentid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="typeid";
                    temp.val="0";
                    attrarr.push(temp);
                    temp={};
                    temp.id="source";
                    temp.val="0";
                    attrarr.push(temp);
                    temp={};
                    temp.id="chapterid";
                    temp.val="0";
                    attrarr.push(temp);
                    temp={};
                    temp.id="ks_code";
                    temp.val=ks_code;
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    i.appendTo(p);
                    attrarr=[];
                    p =initDom("<p></p>",attrarr);
                    p.appendTo(a);
                    attrarr=[];
                    var span =initDom("<span></span>",attrarr);
                    span.appendTo(p);
                    span.text(v.word);
                    attrarr=[];
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(p);
                    if(v.ukmark!=null&&v.ukmark!=""&&v.ukmark!=undefined){
                        span.text("["+v.ukmark+"]");
                    }
                    attrarr=[];
                    temp={};
                    temp.id="style";
                    temp.val="display:block;"
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(p);
                    span.text(v.morphology+v.explains);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="iBtn";
                    attrarr.push(temp);
                    temp={};
                    temp.id="wordid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    p =initDom("<p></p>",attrarr);
                    p.bind("click",function(e){
                        //$(this).find("a").toggleClass("cur01");
                        var wordid=$(this).attr("wordid");
                        var ks_code=Request["ks_code"];
                        var action=$(this).find("a").hasClass("cur01")?"del":"add";
                        if(action=="add"){
                            $(this).find("a").addClass("cur01");
                            $(this).find("a").html('<i class="icon-favorite"></i>已收藏');
                            $("#imagemodel").find("p[wordid='"+wordid+"']").find("a").addClass("cur01");
                            $("#imagemodel").find("p[wordid='"+wordid+"']").find("a").html('<i class="icon-favorite"></i>已收藏');
                        }else if(action=="del"){
                            console.log($(this).find("a"));
                            $(this).find("a").removeClass("cur01");
                            $(this).find("a").html('<i class="icon-favorite-o"></i>收藏');
                            $("#imagemodel").find("p[wordid='"+wordid+"']").find("a").removeClass("cur01");
                            $("#imagemodel").find("p[wordid='"+wordid+"']").find("a").html('<i class="icon-favorite-o"></i>收藏');
                        }
                        var source="wordread";
                        //表示的是收藏数据这里进行的是ajax异步跟新
                        $.ajax({
                            type:'GET',
                            url:"../Word/setWordBook",
                            data:{wordid:wordid,action:action,ks_code:ks_code,source:source},
                            dataType:'json',
                            timeout: 300,
                            context:$(this),
                            complete:function(){
                                hideloading();
                            }
                        });
                        e.stopPropagation();
                    });
                    p.appendTo(li);
                    if(v.iscollect=='0'){
                        attrarr=[];
                        var temp={};
                        temp.id="href";
                        temp.val="javascript:void(0);";
                        attrarr.push(temp);
                        temp.id="class";
                        temp.val="aBtn fav";
                        attrarr.push(temp);
                        a =initDom("<a></a>",attrarr);
                        a.appendTo(p);
                        a.html('<i class="icon-favorite-o"></i>收藏');
                    }else{
                        attrarr=[];
                        var temp={};
                        temp.id="href";
                        temp.val="javascript:void(0);";
                        attrarr.push(temp);
                        temp.id="class";
                        temp.val="aBtn fav cur01";
                        attrarr.push(temp);
                        a =initDom("<a></a>",attrarr);
                        a.appendTo(p);
                        a.html('<i class="icon-favorite"></i>已收藏');
                    }
                    //初始化图文模式的单词
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="con";
                    attrarr.push(temp);
                    var imgdiv =initDom("<div></div>",attrarr);
                    imgdiv.appendTo(imgobj);
                    attrarr=[];
                    var imgul =initDom("<ul></ul>",attrarr);
                    imgul.appendTo(imgdiv);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="scrollCon";
                    attrarr.push(temp);
                    imgdiv =initDom("<div></div>",attrarr);
                    imgdiv.appendTo(imgul);
                    attrarr=[];
                    temp={};
                    temp.id="onclick";
                    temp.val="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio('"+v.name+"');}catch(e){setTip('请升级到最新的优信');};";
                    attrarr.push(temp);
                    var imgp =initDom("<p></p>",attrarr);
                    imgp.appendTo(imgdiv);
                    attrarr=[];
                    temp={};
                    temp.id="src";
                    temp.val=wordpicpath+v.pic;
                    attrarr.push(temp);
                    imgimg =initDom("<img></img>",attrarr);
                    imgimg.appendTo(imgp);
                    attrarr=[];
                    temp.id="onclick";
                    temp.val="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio('"+v.name+"');}catch(e){setTip('请升级到最新的优信');};";
                    attrarr.push(temp);
                    var imgp =initDom("<p></p>",attrarr);
                    imgp.appendTo(imgdiv);
                    // attrarr=[];
                    // temp={};
                    // temp.id="class";
                    // temp.val="aBtn bGray02 border-nav displayword";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="style";
                    // temp.val="display:none;margin:4px;";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="mp3name";
                    // temp.val=v.name;
                    // attrarr.push(temp);
                    // var imga =initDom("<a></a>",attrarr);
                    // imga.text("点击显示单词");
                    // imga.appendTo(imgp);
                    
                    if(v.ukmark!=null&&v.ukmark!=""&&v.ukmark!=undefined){
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="worddisplay";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="display:block;";
                        attrarr.push(temp);
                        var imgspan =initDom("<span></span>",attrarr);
                        imgspan.appendTo(imgp);
                        imgspan.text(v.word);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="wordremark alound ";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="display:block;";
                        attrarr.push(temp);
                        var imgspan =initDom("<span></span>",attrarr);
                        imgspan.html("["+v.ukmark+"]<a class=\"icc-song\" href=\"javascript:void(0);\"><i class=\"icon-uniE60C\"></i></a>");
                        imgspan.appendTo(imgp);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="wordexpl";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="display:block;";
                        attrarr.push(temp);
                        var imgspan =initDom("<span></span>",attrarr);
                        imgspan.html(v.morphology+v.explains);
                        imgspan.appendTo(imgp);
                        // attrarr=[];
                        // temp={};
                        // temp.id="class";
                        // temp.val="alound icc-song";
                        // attrarr.push(temp);
                        // temp={};
                        // temp.id="href";
                        // temp.val="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio('"+v.name+"');}catch(e){setTip('请升级到最新的优信');};";
                        // attrarr.push(temp);
                        // var wa=initDom("<a></a>",attrarr);
                        // wa.html('<i class="icon-uniE60C"></i>');
                        // wa.appendTo(imgspan);

                    }else{
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="worddisplay";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="display:block;";
                        attrarr.push(temp);
                        var imgspan =initDom("<span></span>",attrarr);
                        imgspan.appendTo(imgp);
                        imgspan.html(v.word);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="wordexpl";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="display:block;";
                        attrarr.push(temp);
                        var imgspan =initDom("<span></span>",attrarr);
                        imgspan.html(v.morphology+v.explains+"<a class=\"icc-song\" href=\"javascript:void(0);\"><i class=\"icon-uniE60C\"></i></a>");
                        imgspan.appendTo(imgp);
                        //imgspan.html(v.word+'<a style="margin-left:10px;" href="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio("'+v.name+'");}catch(e){setTip(/"请升级到最新的优信/");};');
                    }
                    
                    
                    attrarr=[];
                    temp={};
                    temp.id="wordid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="class";
                    temp.val="collect";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="cursor:pointer;";
                    attrarr.push(temp);
                    imgp =initDom("<p></p>",attrarr);
                    imgp.appendTo(imgdiv);
                    // imgp.bind("click",function(e){
                    //     //$(this).find("a").toggleClass("cur01");
                    //     var wordid=$(this).attr("wordid");
                    //     var ks_code=Request["ks_code"];
                    //     var action=$(this).find("a").hasClass("cur01")?"del":"add";
                    //     if(action=="add"){
                    //         $(this).find("a").addClass("cur01");
                    //         $(this).find("a").html('<i class="icon-favorite"></i>已收藏');
                    //     }else if(action=="del"){
                    //         console.log($(this).find("a"));
                    //         $(this).find("a").removeClass("cur01");
                    //         $(this).find("a").html('<i class="icon-favorite-o"></i>收藏');
                    //     }
                    //     var source="wordread";
                    //     //表示的是收藏数据这里进行的是ajax异步跟新
                    //     $.ajax({
                    //         type:'GET',
                    //         url:"../Word/setWordBook",
                    //         data:{wordid:wordid,action:action,ks_code:ks_code,source:source},
                    //         dataType:'json',
                    //         timeout: 300,
                    //         context:$(this),
                    //         complete:function(){
                    //             hideloading();
                    //         }
                    //     });
                    //     e.stopPropagation();
                    // });
                    if(v.iscollect=='0'){
                        attrarr=[];
                        var temp={};
                        temp.id="href";
                        temp.val="javascript:void(0);";
                        attrarr.push(temp);
                        temp.id="class";
                        temp.val="aBtn fav";
                        attrarr.push(temp);
                        a =initDom("<a></a>",attrarr);
                        a.appendTo(imgp);
                        a.html('<i class="icon-favorite-o"></i>收藏');
                    }else{
                        attrarr=[];
                        var temp={};
                        temp.id="href";
                        temp.val="javascript:void(0);";
                        attrarr.push(temp);
                        temp.id="class";
                        temp.val="aBtn fav cur01";
                        attrarr.push(temp);
                        a =initDom("<a></a>",attrarr);
                        a.appendTo(imgp);
                        a.html('<i class="icon-favorite"></i>已收藏');
                    }
                });
                if(iswords>0&&iswords==data.length){
                    mp3.setNourl(1);
                }
            },
            error:function(xhr,type){
                hideloading();

            }
        })
    }


    //单词朗读
    function wordAloundEvent(obj){
        alert("afasdfasdfasdf");
        
    }

    //收藏操作
    function wordCollectEvent(){

    }

    learnWord.learnWordUnitdata=learnWordUnitdata;
    learnWord.initStudyList=initStudyList;
    return learnWord;
});