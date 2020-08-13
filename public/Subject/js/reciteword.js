require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax",
        "choose":"choose",
    },
    shim: {
        'zepto':{
            deps: [],
            exports: '$'
        },
        'enajax': {
            deps: ['zepto'],
            exports: 'enajax'
        },
        'choose': {
            deps: ['zepto','enajax'],
            exports: 'choose'
        }
    },
    waitSeconds: 0
});
//文件主要是进行不同体型的展示
define(['zepto','enajax','choose'],function($,enajax,choose){
    var reciteWord = {}; //推荐方式  
    var reciteWordUnitdata = function(url,loc){
        getUnitData(url,loc);
    };
    //初始化单词学习的列表
    var initReciteList=function(url,listobj){
        var Request = new Object();
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        hideloading();
        try{
            UXinJSInterface.showAlert("系统正在为您出题,请稍后...");
        }catch(e){
            setTip("请安装优教信使");
        }
        
        //showloading();
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                //hideloading();
                try{
                    UXinJSInterface.hideProgress();
                }catch(e){
                    setTip("请安装优教信使");
                }
                
                //初始化
                mp3.playInit(data.downlist,data.downlist,"icon-uniE60C actY","icon-uniE60C actY");
                //资源下载列表
                mp3.mp3dowload();
                //setPageContent(data,listobj);
                //创建
                var html="";
                for(var i=0;i<data.downlist.length;i++){
                    html=html+'<div class="con recite" isvisiable="0"><img src="../../public/Subject/img/2013112931.gif" alt="" /></div>';
                }
                $(listobj).html(html);
            },
            error:function(xhr,type){
                hideloading();
            }
        });
    }


    //单词朗读
    function wordAloundEvent(obj){
        alert("afasdfasdfasdf");
        
    }

    //收藏操作
    function wordCollectEvent(){

    }

    reciteWord.reciteWordUnitdata=reciteWordUnitdata;
    reciteWord.initReciteList=initReciteList;
    return reciteWord;
});