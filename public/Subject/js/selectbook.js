require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax",
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
    },
    waitSeconds: 0
});

//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    var selectBook = {}; //推荐方式     
    //初始化单词学习的列表
    var initBookImg=function(url,listobj){
        $(listobj).empty();
        $.ajax({
            type:'GET',
            url:url,
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                $.each(data,function(k,v){
                    //动态创建单词的元素
                    var attrarr=[];
                    var temp={};
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(listobj);
                    li.bind("click",function(){
                        if($(this).find("i").hasClass("icon-right")){
                            $(this).find("i").addClass("icon-down2").removeClass("icon-right");
                            $(this).parent().find("ol").hide();
                            $(this).next().show();   
                        }else{
                            $(this).find("i").addClass("icon-right").removeClass("icon-down2");
                            $(this).next().hide();
                        }
                        $("#wrapper").resize();
                    });
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="fl";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text(v.detail_name);
                    span.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="fr";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(li);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="icon-right";
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    i.appendTo(span);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bookList";
                    attrarr.push(temp);
                    var ol =initDom("<ol></ol>",attrarr);
                    ol.appendTo(listobj);
                    console.log(v.termversion);
                    if(v.termversion.length==0){
                        ol.hide();
                    }else{
                        var display=0;
                        var iCount=0;
                        $.each(v.termversion,function(tk,tv){
                            iCount=iCount+1;
                            attrarr=[];
                            temp={};
                            temp.id="gradeid";
                            temp.val=v.detail_code;
                            attrarr.push(temp);
                            temp={};
                            temp.id="class";
                            temp.val="version";
                            attrarr.push(temp);
                            temp={};
                            temp.id="versionid";
                            temp.val=tv.r_version;
                            attrarr.push(temp);
                            temp={};
                            temp.id="volumeid";
                            temp.val=tv.r_volume;
                            attrarr.push(temp);
                            temp={};
                            temp.id="style";
                            temp.val="background: -webkit-gradient(linear,center top,center bottom,from(rgba(0, 0, 0, 0)),to(rgba(0,0,0, 0)));";
                            attrarr.push(temp);
                            li =initDom("<li></li>",attrarr);
                            if(tv.checked==1){
                                display=1;
                                //li.find("i.mark").hide();
                            }
                            li.appendTo(ol);
                            li.bind("click",function(){
                                $(".mark").hide();
                                $(this).find("i.mark").show();
                                var grade=$(this).attr("gradeid");
                                var volume=$(this).attr("volumeid");
                                var version=$(this).attr("versionid");
                                showloading();
                                $.getJSON("../User/setUserGradeVersion",{grade:grade,volume:volume,version:version},function(){
                                    hideloading();
                                    setTip("设置成功");
                                });
                            });
                            attrarr=[];
                            temp={};
                            temp.id="style";
                            temp.val="position: relative;";
                            attrarr.push(temp);
                            span =initDom("<span></span>",attrarr);
                            span.appendTo(li);
                            attrarr=[];
                            if(tv.checked==0){
                                temp={};
                                temp.id="style";
                                temp.val="color: #090;display:none;";
                                attrarr.push(temp);
                            }else{
                                temp={};
                                temp.id="style";
                                temp.val="color: #090;";
                                attrarr.push(temp);
                            }
                            temp={};
                            temp.id="class";
                            temp.val="icon-correct04 mark";
                            attrarr.push(temp);
                            var i =initDom("<i></i>",attrarr);
                            i.appendTo(span);
                            attrarr=[];
                            temp={};
                            temp.id="src";
                            temp.val=tv.pic_path;
                            attrarr.push(temp);
                            temp={};
                            temp.id="width";
                            temp.val="100%";
                            attrarr.push(temp);
                            temp={};
                            temp.id="height";
                            temp.val="140px";
                            attrarr.push(temp);
                            var img =initDom("<img></img>",attrarr);
                            img.appendTo(span);
                            attrarr=[];
                            temp={};
                            temp.id="style";
                            temp.val="font-size:10px;";
                            attrarr.push(temp);
                            span =initDom("<span></span>",attrarr);
                            if(tv.r_volume=="0000"){
                                span.html(tv.c4+"</br>全一册");
                            }else if(tv.r_volume=="0001"){
                                span.html(tv.c4+"</br>上学期");
                            }else if(tv.r_volume=="0002"){
                                span.html(tv.c4+"</br>下学期");
                            }
                            span.appendTo(li);
                            //if(iCount%3==0){
                            //    ol.append("<div class='clearfix'></div>");
                            //}
                        });
                        if(display==0){
                            ol.hide();
                        }
                    }
                });
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    selectBook.initBookImg=initBookImg;
    return selectBook;
});