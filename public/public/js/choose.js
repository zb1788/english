//听音选词题
var chooseQuestion=function(data,id){
    var attrarr=[];
    var temp={};
    temp.id="class";
    temp.val="con";
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
    temp.val="playWord pad10";
    attrarr.push(temp);
    var playdiv =initDom("<div></div>",attrarr);
    div.append(playdiv);
    if(data.question.typeid==0){
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="bFontCenter";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="padding-top:0px;";
        attrarr.push(temp);
        var span =initDom("<span></span>",attrarr);
        span.html(data.question.tncontent);
        playdiv.append(span);
    }else{
        // attrarr=[];
        // temp={};
        // temp.id="class";
        // temp.val="textH4";
        // attrarr.push(temp);
        // var h4 =initDom("<h4></h4>",attrarr);
        // playdiv.append(h4);
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
        //playdiv.append(i);
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
        // attrarr=[];
        // temp={};
        // temp.id="src";
        // temp.val=subjectsource+"/img/timg.png";
        // attrarr.push(temp);
        // var img =initDom("<img></img>",attrarr);
        // span.append(img);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="btnYuan radius100";
        attrarr.push(temp);
        var a =initDom("<a></a>",attrarr);
        playdiv.bind("click",function(){
            var status=$(this).find(".cPlay").hasClass("iplay");
            var exitstatus=isExitsFunction("UXinJSInterfaceSpeech.playAudio");
            if(exitstatus==-1){
                setTip("请下载优教信使客户端");
            }else if(exitstatus==1){
                if(status){
                    $(this).find(".cPlay").addClass("istop").removeClass("iplay");
                    try{
                        UXinJSInterfaceSpeech.stopAudio();  
                    }catch(e){
                        setTip("请升级到最新的客户端");
                    }
                }else{
                    $(this).find(".cPlay").addClass("iplay").removeClass("istop");
                    mp3.setUse(0);
                    mp3.setPlayliststyle(0);
                    mp3.playWordList(0);
                }
            }else{
                setTip("使用最新的客户端进行播放");
            }
        })
        // a.bind("click",function(){
        //     // $(this).parent().find("img").attr("src","../../public/Subject/img/timg.gif");
        //     // $("img.curimg").removeClass("curimg");
        //     // $(this).parent().find("img").addClass("curimg");
        //     mp3.setUse(0);
        //     mp3.setPlayliststyle(0);
        //     mp3.playWordList(0);
        // })
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
    }
    attrarr=[];
    temp={};
    temp.id="class";
    temp.val="bdc";
    attrarr.push(temp);
    var ul =initDom("<ul></ul>",attrarr);
    parentdiv.append(ul);
    $.each(data.question.items,function(k,v){
        attrarr=[];
        temp={};
        temp.id="flag";
        temp.val=v.flag;
        attrarr.push(temp);
        temp={};
        temp.id="word";
        temp.val=data.question.word;
        attrarr.push(temp);
        temp={};
        temp.id="quesid";
        temp.val=data.question.wordid;
        attrarr.push(temp);
        temp={};
        temp.id="answer";
        temp.val=data.question.answer;
        attrarr.push(temp);
        temp={};
        temp.id="logid";
        temp.val=id;
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="height: 100%;";
        attrarr.push(temp);
        var li =initDom("<li></li>",attrarr);
        if(v.typeid=='0'){
            li.html("<a style='display:inline-block;vertical-align:middle;'>"+v.flag+".</a><font style='display: inline-block;vertical-align: center;line-height: 100%;vertical-align: middle;width: 90%;size:1.2em;'>"+v.content+'</font>');
        }else{
            li.html("<a style='display:inline-block;vertical-align:middle;'>"+v.flag+".</a><img style='width:60px;height:45px;display:inline-block;' src='"+v.content+"'>");
        }
        li.bind("click",function(){
            var flag=$(this).attr("flag");
            var quesid=$(this).attr("quesid");
            var logid=$(this).attr("logid");
            var answer=$(this).attr("answer");
            var word=$(this).attr("word");

            if(!arrContrain(arrids,quesid)){
                arr.push(word);
                arrids.push(quesid);
            }else{
                return false;
            }
            // if(arrContrain(arr,word)){
      //           arr.push(word);
            //  //return false;
            // }
            //提交答案

            $(this).parent().find(".cur").removeClass("cur");
            $(this).parent().find(".error").removeClass("error");
            $(this).addClass("cur");
            if(flag!=answer){
                $(this).addClass("error").removeClass("cur");
                //将数据进行加入到local中存储
                if(!arrContrain(errorarr,word))errorarr.push(word);
                console.log(errorarr);
                //将单词异步更新到数据库中
                var ks_code=Requests["ks_code"];
                //添加生词本动画
                setTip("已经添加生词本");
                $.ajax({
                    type:'GET',
                    url:"../Word/setWordBook",
                    data:{wordid:quesid,action:"add",ks_code:ks_code,source:"reciteword"},
                    dataType:'json',
                    timeout: 300,
                    context:$(this),
                    complete:function(){
                        hideloading();
                    }
                });
                $(".recite").eq(page).find(".useransweritem").text(flag);
                $(".recite").eq(page).find(".answertips").show();
                //将错题加入生词本
                $(this).parent().find("li").each(function(k,v){
                    answer=$(v).attr("answer");
                    flag=$(v).attr("flag");
                    if(answer==flag){
                        $(v).addClass("cur");
                        return false;
                    }
                })
                $.ajax({
                    type:'GET',
                    url:"../Public/setUserWordtestanswer",
                    data:{quesid:quesid,useranswer:flag,id:logid},
                    async:true,
                    dataType:'json',
                    context:$('body')
                });
                //最后一页进行跳转
                if(page==($(".recite").length-1)){
                    storage.setItem("jp_recite_rate",Math.round(ratenum*100/($(".recite").length))+"%");
                    storage.setItem("jp_recite",encodeURI(JSON.stringify(errorarr)));
                    //计算时间
                    var timer=$("#timer").html();
                    var seconds=setSecondsTimer(timer);
                    storage.setItem("jp_timer",seconds);
                    //将数据进行ajax刷新
                    $.ajax({
                        type:'GET',
                        url:"setUserReciteResult",
                        data:{logid:logid},
                        dataType:'json',
                        timeout: 300,
                        context:$("body")
                    });
                    setTimeout(function(){ 
                        window.location.href="reciteresult?ks_code="+Requests["ks_code"]+"&moduleid="+Requests["moduleid"]+"&ks_short_name="+Requests["ks_short_name"];
                    }, 2000);
                    
                }
            }else{
                if(!arrContrain(rightarr,word)){
                    rightarr.push(word);
                    ratenum=ratenum+1;
                }
                $.ajax({
                    type:'GET',
                    url:"../Public/setUserWordtestanswer",
                    data:{quesid:quesid,useranswer:flag,id:logid},
                    async:true,
                    dataType:'json',
                    context:$('body')
                });
                if(page==($(".recite").length-1)){
                    storage.setItem("jp_recite_rate",Math.round(ratenum*100/($(".recite").length))+"%");
                    storage.setItem("jp_recite",encodeURI(JSON.stringify(errorarr)));
                    //计算时间
                    var timer=$("#timer").html();
                    var seconds=setSecondsTimer(timer);
                    storage.setItem("jp_timer",seconds);
                    //将数据进行ajax刷新
                    $.ajax({
                        type:'GET',
                        url:"setUserReciteResult",
                        data:{logid:logid},
                        dataType:'json',
                        timeout: 300,
                        context:$("body")
                    });
                    setTimeout(function(){ 
                        window.location.href="reciteresult?ks_code="+Requests["ks_code"]+"&moduleid="+Requests["moduleid"]+"&ks_short_name="+Requests["ks_short_name"];
                    }, 2000);
                    
                }else{
                    setTimeout(function(){ 
                        $("#next").click();
                    }, 2000);
                    
                }
                //document.getElementById("next").click();
            }
        });
        ul.append(li);
    });
    parentdiv.append("<div class='answertips' style='text-align: center;display:none;'>正确答案是<font color='blue'>"+data.question.answer+"</font>，您的答案是<font class='useransweritem' color='red'></font></div>");
    return parentdiv;
}

//进行内容的填充
var setPageContent=function(data,obj){
    //听力训练的展示
    //$.each(data.questions,function(key,value){
        var question=chooseQuestion(data,data.id);
        question.appendTo(obj);
    //});
}


var setUserPageContent=function(data,obj){
    //听力训练的展示
    $.each(data.questions,function(key,value){
        var question=chooseQuestion(value,data.id);
        question.appendTo(obj);
    });
}