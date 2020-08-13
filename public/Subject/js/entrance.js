var grades="";
$(function(){
    //进行首页的数据的请求
    $.ajax({
        type:'GET',
        url:'../../public/public/json/subjects.json?ran='+Math.random(),
        dataType:'json',
        async:true,
        success: function(data){
            //遮罩消失
            hideloading();
            grades=data;
            $.each(grades,function(k,v){
                //进行入口页面的年级的展示
                var attrarr=[];
                var temp={};
                temp.id="gradeid";
                temp.val=k;
                var gradesid=v.gradeid;
                attrarr.push(temp);
                var temp={};
                temp.id="grade";
                temp.val=v.gradeid;
                attrarr.push(temp);
                var li =initDom("<li></li>",attrarr);
                li.text(v.title);
                li.appendTo($("#grade"));
                li.bind("click",function(){
                    $(this).siblings().removeClass("on");
                    $(this).addClass("on");
                    $("#subjects").empty();
                    var gradeid=$(this).attr("gradeid");
                    var subjectid="";
                    $.each(grades[gradeid].subjects,function(key,subject){
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="entH2";
                        attrarr.push(temp);
                        var h2 =initDom("<h2></h2>",attrarr);
                        h2.appendTo($("#subjects"));
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val=subject.style;
                        attrarr.push(temp);
                        temp={};
                        temp.id="subjectid";
                        temp.val=subject.subjectid;
                        subjectid=subject.subjectid;
                        attrarr.push(temp);
                        var label =initDom("<label></label>",attrarr);
                        label.appendTo(h2);
                        label.text(subject.title);
                        if(subject.modules.length==0){
                            h2.remove();
                        }
                        $.each(subject.modules,function(keys,module){
                            attrarr=[];
                            var li =initDom("<li></li>",attrarr);
                            li.appendTo($("#subjects"));
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="btnTb";
                            attrarr.push(temp);
                            temp={};
                            temp.id="href";
                            if(module.type==6){
                                temp.val="../../"+module.url+"&moduleid="+module.id+"&subjectid="+subjectid+"&gradeid="+gradesid+"&type="+module.type+"&backUrl=/Subject/Index/entrance";
                            }else{
                                temp.val="../../"+module.url+"?moduleid="+module.id+"&subjectid="+subjectid+"&gradeid="+gradesid+"&type="+module.type+"&backUrl=/Subject/Index/entrance";
                            }
                            attrarr.push(temp);
                            a =initDom("<a></a>",attrarr);
                            a.appendTo(li);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="listIcon radius8";
                            attrarr.push(temp);
                            var span =initDom("<span></span>",attrarr);
                            span.appendTo(a);
                            attrarr=[];
                            temp={};
                            temp.id="src";
                            temp.val="../../public/public/images/"+module.style+".png";
                            attrarr.push(temp);
                            temp={};
                            temp.id="class";
                            temp.val="img100 radius8";
                            attrarr.push(temp);
                            //img100 radius8
                            var img =initDom("<img></img>",attrarr);
                            img.appendTo(span);
                            attrarr=[];
                            var b =initDom("<b></b>",attrarr);
                            b.text(module.title);
                            b.appendTo(a);
                            $(a).find("span").removeClass("fl");
                            if(keys==(subject.modules.length-1)){
                                $("#subjects").append("<div class='clear mB10'></div>");
                            }
                        });
                    });
                    $(window).resize();

                })
            })
            try{
                var gradeids=parseInt(grade,10);
                if(gradeids<=9){
                    $("li[gradeid='"+(parseInt(grade,10)-1)+"']").click();
                }else{
                    $("li[gradeid='9']").click();
                }
            }catch(e){

            }
        },
        error:function(xhr,type){
            hideloading();
            setTip("网络超时请稍后再试");
        }
    })

    //学习记录点击事件
    $(".head-right").click(function(){
        //获取年级
        var gradeid=$("#grade").find("li.on").attr("grade");
        window.location.href="../User/userrecord?gradeid="+gradeid;
    });
    //性能提交
    perface();
})
