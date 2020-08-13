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
        },
    },
    waitSeconds: 0
});
//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    var learnText = {}; //推荐方式  
    var learnTextUnitdata = function(url,loc){
        getUnitData(url,loc);
    };
    
    //初始化单词学习的列表
    var initReadTextList=function(url,listobj){
        var Request = new Object();
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
                //初始化列表
                mp3.playInit(data.data,data.mp3list,"icon-uniE60C actY","icon-uniE60C actY");
                mp3.setPlaystatus(1);
                mp3.setPlaytype(1);
                //初始化下载列表
                mp3.mp3dowload();
                $.each(data.data,function(k,v){
                    var attrarr=[];
                    var temp={};
                    temp.id="class";
                    temp.val="con text";
                    attrarr.push(temp);
                    temp.id="chapter";
                    temp.val=v.chapter;
                    attrarr.push(temp);
                    var div =initDom("<div></div>",attrarr);
                    div.appendTo(listobj);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var ul =initDom("<ul></ul>",attrarr);
                    ul.appendTo(div);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="text";
                    attrarr.push(temp);
                    div =initDom("<div></div>",attrarr);
                    div.appendTo(ul);
                    //需要将数据装到这个类中进行章节的展示
                    $.each(v.texts,function(tk,tv){
                        //动态创建单词的元素
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="textread";
                        attrarr.push(temp);
                        temp={};
                        temp.id="mp3";
                        temp.val=tv.mp3;
                        attrarr.push(temp);
                        var p =initDom("<p></p>",attrarr);
                        p.appendTo(div);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="icon-uniE60C actY";
                        attrarr.push(temp);
                        temp={};
                        temp.id="contentid";
                        temp.val=tv.id;
                        attrarr.push(temp);
                        temp={};
                        temp.id="chapterid";
                        temp.val=tv.chapterid;
                        attrarr.push(temp);
                        temp={};
                        temp.id="typeid";
                        temp.val="1";
                        attrarr.push(temp);
                        temp={};
                        temp.id="ks_code";
                        temp.val=ks_code;
                        attrarr.push(temp);
                        temp={};
                        temp.id="source";
                        temp.val="1";
                        attrarr.push(temp);
                        temp={};
                        temp.id="style";
                        temp.val="font-size: 120%;";
                        attrarr.push(temp);
                        var i =initDom("<i></i>",attrarr);
                        i.appendTo(p);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="en";
                        attrarr.push(temp);
                        temp={};
                        temp.id="class";
                        temp.val="en";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        if(tv.enbefore==""){
                            span.html("<font>"+tv.encontent+"</font>");
                        }else{
                            span.html("<font>"+tv.enbefore+":"+tv.encontent+"</font>");
                        }
                        span.appendTo(p);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="cn";
                        attrarr.push(temp);
                        span =initDom("<span></span>",attrarr);
                        if(tv.enbefore==""){
                            span.html("<font>"+tv.cncontent+"</font>");
                        }else{
                            span.html("<font>"+tv.enbefore+":"+tv.cncontent+"</font>");
                        }
                        span.appendTo(p);
                    });
                });
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

    learnText.learnTextUnitdata=learnTextUnitdata;
    learnText.initReadTextList=initReadTextList;
    return learnText;
});