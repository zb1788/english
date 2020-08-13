require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax",
    },
    shim: {
        'zepto':{
            exports:"$"
        },
        'enajax': {
            deps: ['zepto'],
            exports: 'enajax'
        },
    },
    waitSeconds: 0
});
//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    var classRank = {}; //推荐方式  
    //初始化单词学习的列表
    var initRankList=function(url,listobj){
        var Request = new Object();
        Request = GetRequest();
        var mod=Request["mod"];
        var func=Request["func"];
        var ks_code=Request["ks_code"];
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code,mod:mod,func:func},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //查询并且创建自己的排名情况
                var attrarr=[];
                var temp={};
                temp.id="class";
                temp.val="topM";
                attrarr.push(temp);
                var userdiv =initDom("<div></div>",attrarr);
                userdiv.appendTo(listobj);

                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="listImg fl mL10 bGray";
                attrarr.push(temp);
                var userimgspan =initDom("<span></span>",attrarr);
                userimgspan.appendTo(userdiv);
                userimgspan.html('<img src="../../public/Subject/images/trophy.png" /><b class="top"></b>');
                // attrarr=[];
                // temp={};
                // temp.id="src";
                // temp.val="../../public/Subject/images/trophy.png";
                // attrarr.push(temp);
                // var userimg =initDom("<img></img>",attrarr);
                // userimg.appendTo(userimgspan);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="listText";
                attrarr.push(temp);
                var userinfospan =initDom("<span></span>",attrarr);
                userinfospan.appendTo(userdiv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH3";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="overflow: hidden;width: 100px;";
                attrarr.push(temp);
                var userinfoh3 =initDom("<h3></h3>",attrarr);
                userinfoh3.text(data.truename);
                userinfoh3.appendTo(userinfospan);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH4";
                attrarr.push(temp);
                var userinfoh4 =initDom("<h4></h4>",attrarr);
                userinfoh4.text("我的作业完成情况");
                userinfoh4.appendTo(userinfospan);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="topH3 fr";
                attrarr.push(temp);
                var userscorespan =initDom("<span></span>",attrarr);
                userscorespan.appendTo(userdiv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="topList clear";
                attrarr.push(temp);
                var listul =initDom("<ul></ul>",attrarr);
                listul.appendTo(listobj);
                //遮罩消失
                var rank=1;
                $.each(data.ranklist,function(k,v){
                    if(data.username==v.username){
                        userscorespan.text(v.score+"分");
                        $(".top").text("第"+rank+"名");
                    }
                    //动态创建单词的元素
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="listIconText";
                    attrarr.push(temp);
                    var userli =initDom("<li></li>",attrarr);
                    userli.appendTo(listul);
                    //用户的排名信息
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="lico";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.appendTo(userli);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    if(rank==1){
                        temp.val="icon-topped first";
                    }else if(rank==2){
                        temp.val="icon-topped second";    
                    }else if(rank==3){
                        temp.val="icon-topped third";
                    }else{
                        temp.val="icon-other";
                    }
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    if(rank>3){
                        i.text(rank);
                    }
                    i.appendTo(span);
                    //用户头像信息
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="listImg radius100 fl bGray";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(userli);
                    attrarr=[];
                    temp={};
                    temp.id="src";
                    temp.val="../../public/Subject/img/vegetable.jpg";
                    attrarr.push(temp);
                    var userimg =initDom("<img></img>",attrarr);
                    userimg.appendTo(span);
                    //测试的类型
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="listText";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(userli);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="topH3";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="overflow: hidden;width: 100px;height: 50px;";
                    attrarr.push(temp);
                    var h3 =initDom("<h3></h3>",attrarr);
                    h3.appendTo(span);
                    h3.text(v.truename);
                    //用户的分数<span class="topH3 fr">16分</span>
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="topH3 fr";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.text(v.score+"分");
                    span.appendTo(userli);
                    rank=rank+1;
                });
                hideloading();
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    classRank.initRankList=initRankList;
    return classRank;
});