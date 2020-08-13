//文件主要是进行不同体型的展示
define(['zepto.min','enElearn'],function($,enElearns){
    var selectBook = {}; //推荐方式     
    //初始化单词学习的列表
    var initBookImg=function(url,listobj){
        $(listobj).empty();
        $.ajax({
            type:'GET',
            url:url,
            dataType:'json',
            async:false,
            timeout: 300,
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
                    // li.bind("click",function(){
                    //     if($(this).find("i").hasClass("icon-right")){
                    //         $(this).find("i").addClass("icon-down").removeClass("icon-right");
                    //         $(this).parent().find("ol").hide();
                    //         $(this).next().show();   
                    //     }else{
                    //         $(this).find("i").addClass("icon-right").removeClass("icon-down");
                    //         $(this).next().hide();
                    //     }
                    //     $(window).resize();
                    // });
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="f1";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text(v.grade_name);
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
                    //console.log(v.termversion);
                    if(v.termversion.length==0){
                        ol.hide();
                    }else{
                        var iCount=0;
                        $.each(v.termversion,function(tk,tv){
                            iCount=iCount+1;
                            attrarr=[];
                            temp={};
                            temp.id="version_id";
                            temp.val=tv.id;
                            attrarr.push(temp);
                            temp={};
                            temp.id="class";
                            temp.val="version";
                            attrarr.push(temp);
                            temp={};
                            temp.id="app_id";
                            temp.val=tv.app_id;
                            attrarr.push(temp);
                            temp={};
                            temp.id="subject_code";
                            temp.val=tv.r_subject_code;
                            attrarr.push(temp);
                            temp={};
                            temp.id="grade_code";
                            temp.val=tv.r_grade_code;
                            attrarr.push(temp)
                            li =initDom("<li></li>",attrarr);
                            if(tv.checked==0){
                               // ol.hide();
                                //li.find("i.mark").hide();
                            }
                            li.appendTo(ol);
                            li.bind("click",function(){
                                $(".mark").hide();
                                $(this).find("i.mark").show();
                                var app_id=$(this).attr("app_id");
                                var subject_code=$(this).attr("subject_code");
                                var version_id=$(this).attr("version_id");
                                var grade_code=$(this).attr("grade_code");
                                showloading();
                                $.getJSON("setUserGradeVersion",{app_id:app_id,subject_code:subject_code,version_id:version_id,grade_code:grade_code},function(){
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
                            temp.id="class";
                            temp.val='wh150';
                            attrarr.push(temp);
                            var lable =initDom("<lable></lable>",attrarr);
                            lable.appendTo(span);
                            attrarr=[];
                            temp={};
                            temp.id="src";
                            temp.val=tv.r_pic;
                            attrarr.push(temp);
                            // temp={};
                            // temp.id="width";
                            // temp.val="100%";
                            // attrarr.push(temp);
                            // temp={};
                            // temp.id="height";
                            // temp.val="140px";
                            // attrarr.push(temp);
                            var img =initDom("<img></img>",attrarr);
                            img.appendTo(lable);
                            attrarr=[];
                            temp={};
                            temp.id="style";
                            temp.val="font-size:10px;";
                            attrarr.push(temp);
                            span =initDom("<span></span>",attrarr);
                            span.html(tv.r_grade_name+'.'+tv.r_term_name+'.'+tv.r_version_name);
                            span.appendTo(li);
                            if(iCount%3==0){
                                ol.append("<div class='clearfix'></div>");
                            }
                        });
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