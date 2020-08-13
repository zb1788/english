var grades="";
$(function(){
    //进行首页的数据的请求
    $.ajax({
        type:'GET',
        url:'../../public/public/json/subjects.json',
        dataType:'json',
        async:true,
        context:$('body'),
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
                        temp.val="table mB10 backGfff radius8 border-nav";
                        attrarr.push(temp);
                        var cli =initDom("<li></li>",attrarr);
                        cli.appendTo($("#subjects"));
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
                        label.appendTo(cli);
                        label.text(subject.title);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="icoF3";
                        attrarr.push(temp);
                        var cdiv=initDom("<div></div>",attrarr);
                        cdiv.appendTo(cli);
                        if(subject.modules.length==0){
                            cli.remove();
                        }
                        $.each(subject.modules,function(keys,module){
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
                            a.appendTo(cdiv);
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
                            
                        });
                        
                        
                    });
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

        }
    })
    //学习记录点击事件
    $(".head-right").click(function(){
        //获取年级
        var gradeid=$("#grade").find("li.on").attr("grade");
        window.location.href="../User/userrecord?gradeid="+gradeid;
    });

    perface();
})




