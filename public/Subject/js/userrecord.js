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
    var userlog = {}; //推荐方式  
    //初始化单词学习的列表
    var initLogList=function(url,listobj,start){
        var Request = new Object();
        Request = GetRequest();
        var subjectid=Request["subjectid"];
        var listlen=$(".recordList").find("li").length;
        //$(listobj).empty();
        hideloading();
        try{
            UXinJSInterface.showAlert("加载学习记录,请稍后...");
        }catch(e){

        }
        $.ajax({
            type:'GET',
            url:url,
            data:{subjectid:subjectid,start:start},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                try{
                    UXinJSInterface.hideProgress();
                }catch(e){

                }
                
                if(data.length>0){
                    //<h3><i class="icon-radio-checked"></i><span class="btn-date">昨天</span></h3>
                    $.each(data,function(k,v){
                        if(month!=(v.month+v.addday)){
                            month=v.month+v.addday;
                            var attrarr=[];
                            var h3 =initDom("<h3></h3>",attrarr);
                            h3.appendTo(listobj);
                            attrarr=[];
                            var temp={};
                            temp.id="class";
                            temp.val="icon-radio-checked";
                            attrarr.push(temp);
                            var i =initDom("<i></i>",attrarr);
                            i.appendTo(h3);
                            temp={};
                            temp.id="class";
                            temp.val="btn-date";
                            attrarr.push(temp);
                            var span =initDom("<span></span>",attrarr);
                            span.text(v.month.substr(0,4)+"/"+v.month.substr(4,2)+"/"+v.addday);
                            span.appendTo(h3);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="true";
                            attrarr.push(temp);
                            var li =initDom("<li></li>",attrarr);
                            li.appendTo(listobj);
                            attrarr=[];
                            var temp={};
                            temp.id="class";
                            temp.val="btn-time";
                            attrarr.push(temp);
                            var span =initDom("<span></span>",attrarr);
                            span.text(v.seconds);
                            span.appendTo(li);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-radio-checked";
                            attrarr.push(temp);
                            var i =initDom("<i></i>",attrarr);
                            i.appendTo(li);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="record";
                            attrarr.push(temp);
                            span =initDom("<span></span>",attrarr);
                            if(v.unit == ""){
                                span.html("在"+v.subject+"<font class='yingFont'>《"+v.module+"》</font>学习");
                            }else{
                                span.html("在"+v.subject+"<font class='yingFont'>《"+v.module+"》</font>学习“"+v.unit+"”");
                            }
                            span.appendTo(li);
                            li.appendTo(listobj);
                        }else{
                            var attrarr=[];
                            var temp={};
                            temp.id="class";
                            temp.val="true";
                            attrarr.push(temp);
                            var li =initDom("<li></li>",attrarr);
                            li.appendTo(listobj);
                            attrarr=[];
                            var temp={};
                            temp.id="class";
                            temp.val="btn-time";
                            attrarr.push(temp);
                            var span =initDom("<span></span>",attrarr);
                            span.text(v.seconds);
                            span.appendTo(li);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-radio-checked";
                            attrarr.push(temp);
                            var i =initDom("<i></i>",attrarr);
                            i.appendTo(li);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="record";
                            attrarr.push(temp);
                            span =initDom("<span></span>",attrarr);
                            if(v.unit == ""){
                                span.html("在"+v.subject+"<font class='yingFont'>《"+v.module+"》</font>学习");
                            }else{
                                span.html("在"+v.subject+"<font class='yingFont'>《"+v.module+"》</font>学习“"+v.unit+"”");
                            }
                            span.appendTo(li);
                            li.appendTo(listobj);
                        }
                        // <li class="ture">
                        //     <span class="btn-time">21:00</span><i class="icon-radio-checked"></i>
                        //     <span class="record">应用<font class="yingFont">《单词测试》</font>进行“Unit1”学习，用时<font class="yelloFont">2分10秒</font></span>
                        // </li>
                        //动态创建单词的元素
                        
                    });
                }else if(listlen==0){
                    $("#audioplay").hide();
                    var attrarr=[];
                    var temp={};
                    temp.id="class";
                    temp.val="true";
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(listobj);
                    attrarr=[];
                    var temp={};
                    temp.id="class";
                    temp.val="btn-time";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-radio-checked";
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    i.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="record";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.html("主人太懒了，什么都没有留下");
                    span.appendTo(li);
                    li.appendTo(listobj);
                }else{
                    setTip("已经到了最后了");
                }
                //修改page的数值
                var page=$("#audioplay").attr("page");
                $("#audioplay").attr("page",parseInt(page)+1);
                
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    userlog.initLogList=initLogList;
    return userlog;
});