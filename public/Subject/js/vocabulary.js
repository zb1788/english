require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax"
    },
    shim: {
        'zepto':{
            deps:[],
            exports:'$'
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
    var userrecord = {}; //推荐方式  
    //初始化单词学习的列表
    var initRecordList=function(url,listobj){
        var Request = new Object();
        Request = GetRequest();
        var ks_code=Request["ks_code"];
        $.ajax({
            type:'GET',
            url:url,
            //data:{ks_code:ks_code},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                $("#wordcount").text(data.length);
                $.each(data,function(k,v){
                    //动态创建单词的元素
                    var attrarr=[];
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(listobj);
                    attrarr=[];
                    var temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.appendTo(li);
                    //<div class="check"><div class="check-box"><i><input type="checkbox" name="check-box"></i></div></div>
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="check";
                    attrarr.push(temp);
                    var pdiv =initDom("<div></div>",attrarr);
                    pdiv.bind("click",function(){
                        if($(this).find(".check-box").hasClass("checkedBox")){
                            $(this).find(".check-box").removeClass("checkedBox");
                            //全选按钮样式变化
                            $("#all").find(".check-box").removeClass("checkedBox");
                        }else{
                            $(this).find(".check-box").addClass("checkedBox");
                            //全选按钮样式变化
                            if($("#iStudy").find(".checkedBox").length==$("#iStudy").find("li").length){
                                $("#all").find(".check-box").addClass("checkedBox");
                            }  
                        }
                    });
                    pdiv.appendTo(a);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="check-box";
                    attrarr.push(temp);
                    temp={};
                    temp.id="wordid";
                    temp.val=v.wordid;
                    attrarr.push(temp);
                    temp={};
                    temp.id="isword";
                    temp.val=v.isword;
                    attrarr.push(temp);
                    temp={};
                    temp.id="ks_code";
                    temp.val=v.ks_code;
                    attrarr.push(temp);
                    var cdiv =initDom("<div></div>",attrarr);
                    cdiv.appendTo(pdiv);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    i.appendTo(cdiv);
                    // attrarr=[];
                    // temp={};
                    // temp.id="type";
                    // temp.val="checkbox";
                    // attrarr.push(temp);
                    // temp={};
                    // temp.id="name";
                    // temp.val="check-box";
                    // attrarr.push(temp);
                    // var input =initDom("<input></input>",attrarr);
                    // input.appendTo(i);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var p =initDom("<p></p>",attrarr);
                    p.appendTo(a);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text(v.word);
                    span.appendTo(p);
                    span =initDom("<span></span>",attrarr);
                    span.text(v.morphology+v.explains);
                    span.appendTo(p);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="w80";
                    attrarr.push(temp);
                    temp={};
                    temp.id="bid";
                    temp.val=v.id;
                    attrarr.push(temp);
                    p =initDom("<p></p>",attrarr);
                    p.bind("click",function(){
                        var id=$(this).attr("bid");
                        $.ajax({
                            type:'GET',
                            url:"../User/delUsernameVocabulary",
                            data:{id:id},
                            dataType:'json',
                            timeout: 300,
                            context:$(this),
                            complete:function(){
                                $(this).parent().remove();
                                $("#wordcount").text(parseInt($("#wordcount").text())-1);
                                hideloading();
                                setTip("删除成功");
                            }
                        });
                    });
                    p.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-del";
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    i.appendTo(p);
                    attrarr=[];
                    temp={};
                    var b =initDom("<b></b>",attrarr);
                    b.text("移除");
                    b.appendTo(p);
                    li.appendTo(listobj);
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
    userrecord.initRecordList=initRecordList;
    return userrecord;
});