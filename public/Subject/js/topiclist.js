require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax"
    },
    shim: {
        'zepto':{
            deps: [],
            exports: '$'
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
    var topiclist = {}; //推荐方式     
    //初始化考试列表
    var initList=function(url,code,listobj){
        $(listobj).empty();
        $.ajax({
            type:'GET',
            url:url,
            data:{code:code},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                var attrarr=[];
                var temp={};
                temp.id="bid";
                temp.val=0;
                attrarr.push(temp);
                temp={};
                temp.id="href";
                temp.val="javascript:;";
                attrarr.push(temp);
                temp={};
                temp.id="class";
                temp.val="paperattr on";
                attrarr.push(temp);
                var a =initDom("<a></a>",attrarr);
                a.text("全部");
                listobj.append(a);
                //dd.appendTo(listobj);
                a.bind("click",function(){
                    $(this).parent().find(".on").removeClass("on");
                    $(this).find("a").addClass("on");
                    var typeid=$("#zttype").find("a.on").attr("bid");
                    var yearid=$("#ztyear").find("a.on").attr("bid");
                    var provinceid=$("#ztprovince").find("a.on").attr("bid");
                    getExamsListData("getTopicList",$("#iStudy"),typeid,yearid,provinceid);
                });
                $.each(data,function(k,v){
                    //动态创建单词的元素<dd><a href="javascript:;" zttypeid="0" class="cur">全部</a></dd>
                    attrarr=[];
                    temp={};
                    temp.id="bid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:;";
                    attrarr.push(temp);
                    temp.id="class";
                    temp.val="paperattr";
                    attrarr.push(temp);
                    a =initDom("<a></a>",attrarr);
                    a.text(v.title);
                    listobj.append(a);
                    //dd.appendTo(listobj);
                    a.bind("click",function(){
                        $(this).parent().find(".on").removeClass("on");
                        $(this).find("a").addClass("on");
                        var typeid=$("#zttype").find("a.on").attr("bid");
                        var yearid=$("#ztyear").find("a.on").attr("bid");
                        var provinceid=$("#ztprovince").find("a.on").attr("bid");
                        getExamsListData("getTopicList",$("#iStudy"),typeid,yearid,provinceid);
                    });
                });
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    var getExamsListData=function(url,loc,examstype,yearid,proviceid){
        Request = GetRequest();
        var peroid=Request["levelid"]
        $(loc).empty();
        $.ajax({
            type:'GET',
            url:url,
            data:{gradeid:peroid,typeid:examstype,yearid:yearid,provinceid:proviceid,ran:Math.random()},
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
                    examlist=examlist+'<li onclick="javascript:window.location.href=\'listenexam?examsid='+v.id+'&ks_code='+Request["ks_code"]+'&moduleid='+Request["moduleid"]+'&backsUrl='+encodeURIComponent(encodeURIComponent(location.href))+'&ks_short_name='+encodeURI(encodeURI(Request["ks_short_name"]))+'\';">';
                    examlist=examlist+'<a href="javascript:void(0);"><p ><b class="btnYuan03 radius100 bDefault"><i class="icon-tlxl"></i></b></p>';
                    examlist=examlist+'<p><label class="textH2 clear">'+v.name+'</label><em class="font08">本测试包含'+v.papernum+'道题</em></p>';
                    examlist=examlist+'</a><p><i class="icon-right"></i></p></li>';
                });
                $(loc).html(examlist);
                new IScroll("#wrapper",{
                    momentum:true,
                    click:true 
                });
                $("#wrapper").resize();
            },
            error:function(xhr,type){

            }
        })
    }

    var initTopicList=function(url,typeobj,yearobj,proviceobj){
        initList(url,"examtype",typeobj);
        initList(url,"year",yearobj);
        initList(url,"province",proviceobj);
    }

    topiclist.initTopicList=initTopicList;
    topiclist.getExamsListData=getExamsListData;
    return topiclist;
});