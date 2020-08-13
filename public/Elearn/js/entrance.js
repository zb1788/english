require(['zepto.min','enElearn'], function($,enElearn){
        var myScroll;
        function loaded () {
           myScroll = new IScroll('#wrapper');
           myScroll = new IScroll('#wrapper01');
        }

        var grades="";
        //进行首页的数据的请求
        $.ajax({
            type:'GET',
            url:'entrancedata',
            dataType:'json',
            async:false,
            timeout: 3000,
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
                    attrarr.push(temp);
                    var temp={};
                    temp.id="grade_code";
                    var gradesid=v.detail_code;
                    temp.val=v.detail_code;
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    li.text(v.detail_name);
                    li.appendTo($("#grade"));
                    li.bind("click",function(){
                        $(this).siblings().removeClass("on");
                        $(this).addClass("on");
                        $("#subjects").empty();
                        // var gradeid=$(this).attr("gradeid");
                        // var subjectid="";
                        // console.log(gradeid);
                        $.each(v.subject,function(key,subject){
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
                            temp.val=subject.app_subject_code;
                            subjectid=subject.app_subject_code;
                            attrarr.push(temp);
                            var label =initDom("<label></label>",attrarr);
                            label.appendTo(h2);
                            label.text(subject.detail_name);
                            // if(subject.modules.length==0){
                            //     h2.remove();
                            // }
                            $.each(subject.applist,function(keys,module){
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
                                temp.val="/Elearn/index/index?app_id="+module.id+"&gradeid="+gradesid+"&backUrl=/Elearn/index/entrance";
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
                                temp.val="../../public/public/images/kwld.jpg";
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
                                b.text(module.app_name);
                                b.appendTo(a);
                                $(a).find("span").removeClass("fl");
                                if(keys==(subject.applist.length-1)){
                                    $("#subjects").append("<div class='clear mB20'></div>");
                                }
                            });
                            
                            
                        });
                        $(window).resize();
                    })
                }) 
            },
            error:function(xhr,type){

            }
        })        
        //点击显示当前的年级信息
        $("li[gradeid='0']").click();
        //学习记录点击事件
        // $(".head-right").click(function(){
        //     //获取年级
        //     var gradeid=$("#grade").find("li.on").attr("grade");
        //     window.location.href="../User/userrecord?gradeid="+gradeid;
        // });
});


