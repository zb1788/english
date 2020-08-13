require.config({
　baseUrl: "../../public/Homework/js",
　paths: {
　　　"jquery": "jquery-1.7.2.min",
　　　"mui": "mui.min"
　}
});

require(['jquery','mui','publish',"mobile"], function($,mui,hw,mobile){
	$(function(){
				try{
           UXinJSInterface.hideProgress(); 
        }catch(e){

        }
	    $('li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
	    $('li.parent_li > span.zdie').on('tap', function (e) {
	        var children = $(this).parent('li.parent_li').find(' > ul > li');
	        if (children.is(":visible")) {
	            children.hide('fast');
	            $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-circle').removeClass('fa-minus-circle');
	        } else {
	            children.show('fast');
	            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-circle').removeClass('fa-plus-circle');
	        }
	        e.stopPropagation();
	    });
	    //进行复选框的选中
	    $('li.tab-zhu').on('tap', function (e) {
            //如果section的章节的问题
            if($(this).hasClass("section")){
                $(this).find("i").toggleClass("fa-check-square-o");
                $(this).find("i").toggleClass("fa-square-o");
                if($(this).find("i").hasClass("fa-square-o")){
                    //去掉的数据
                    $(this).parent().parent().parent().siblings("span.pmiddle4").find("font.count").html($(this).parent().parent().parent().find("li.tab-zhu").find("i.fa-check-square-o").length);
                    $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                }else{
                    //去掉的数据
                    $(this).parent().parent().parent().siblings("span.pmiddle4").find("font.count").html($(this).parent().parent().parent().find("li.tab-zhu").find("i.fa-check-square-o").length);
                    $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                }

            }else{
                if($(this).find("i").hasClass("fa-square-o")||$(this).find("i").hasClass("fa-check-square-o")){
                    $(this).find("i").toggleClass("fa-check-square-o");
                    $(this).find("i").toggleClass("fa-square-o");
                    //如果删除了需要判断父亲一级是不是需要吧勾去掉
                    var checklist=$(this).parent().find("i.fa-check-square-o").length;
                    var classname=$(this).attr("class").substr(36);
                    $("font."+classname).html(checklist);
                    if($(this).hasClass("word")){
                        //添加解决ios的兼容行问题
                        var wordalound = ($("font.word_alound").length == 0)?0:$("font.word_alound").html();
                        var wordspell = ($("font.word_spell").length == 0)?0:$("font.word_spell").html();
                        var wordchoose = ($("font.word_choose").length == 0)?0:$("font.word_choose").html();
                        var wordtranslate = ($("font.word_translate").length == 0)?0:$("font.word_translate").html();
                        $(this).parents("li.min70").find(".select").html(parseInt(wordalound)+parseInt(wordspell)+parseInt(wordchoose)+parseInt(wordtranslate));
                    }
                }else{
                    $(this).find("i").toggleClass("fa-dot-circle-o");
                    $(this).find("i").toggleClass("fa-circle-o");
                    //这是单选框需要将其他的置换成不选中
                    $(this).addClass("active");
                    $(this).parent("ul").find("li").not(".active").find("i").addClass('fa-circle-o').removeClass('fa-dot-circle-o');
                    $(this).removeClass("active");
                    if($(this).find("i").hasClass("fa-dot-circle-o")){
                        $(this).parents("li.min70").find(".select").html("1");
                    }else{
                        $(this).parents("li.min70").find(".select").html("0");
                    }
                }
            }
	        e.stopPropagation();
	    });

	    //进行全选的响应
	    $('span.po-r-w100').on('tap', function (e) {
	    	$(this).find("i").toggleClass("fa-check-square-o");
    		$(this).find("i").toggleClass("fa-square-o");
            //检查下面的有没有孩子
            if($(this).siblings("ul").find("span.po-r-w100")){
                if($(this).find("i").hasClass("fa-square-o")){
                    var i=$(this).siblings("ul").find("span.po-r-w100").find("i.fa-check-square-o");
                    $(i).toggleClass("fa-square-o");
                    $(i).toggleClass("fa-check-square-o");
                    
                }else{
                    var i=$(this).siblings("ul").find("span.po-r-w100").find("i.fa-square-o");
                    $(i).toggleClass("fa-check-square-o");
                    $(i).toggleClass("fa-square-o");
                }  
            }
            //判断分单词和课文
            if($(this).hasClass("text")){
                //表示的是进行的是课文的全选判断是否选中的情况
                var ul=$(this).siblings("ul").find("li");
                if($(this).find("i").hasClass("fa-check-square-o")){
                    //表示选中的情况
                    $(ul).find("i.fa-square-o").addClass("fa-check-square-o").removeClass("fa-square-o");
                    $(this).siblings("span.pmiddle3").find("font.count").html($(ul).find("i.fa-check-square-o").length);
                }else{
                    $(ul).find("i.fa-check-square-o").addClass("fa-square-o").removeClass("fa-check-square-o");
                    //表示没有选中
                    $(this).siblings("span.pmiddle3").find("font.count").html("0");
                }
            }else if($(this).hasClass("word")){
                //区分是否有章节
                if($(this).hasClass("section")){

                    //有章节的情况
                    //表示的是进行的是课文的全选判断是否选中的情况判断点击的是那个全选
                    if($(this).hasClass("child")){
                        //点击的是section的全选
                        var ul=$(this).siblings("ul").find("li");
                        if($(this).find("i").hasClass("fa-check-square-o")){
                            $(ul).find("i.fa-square-o").addClass("fa-check-square-o").removeClass("fa-square-o");
                            //表示选中的情况
                            $(this).parent().parent().siblings("span.pmiddle4").find("font.count").html($(this).parent().parent().find("li.tab-zhu").find("i.fa-check-square-o").length);
                            $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                        }else{
                            $(ul).find("i.fa-check-square-o").addClass("fa-square-o").removeClass("fa-check-square-o");
                            //表示没有选中
                            $(this).parent().parent().siblings("span.pmiddle4").find("font.count").html($(this).parent().parent().find("li.tab-zhu").find("i.fa-check-square-o").length);
                            $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                        }
                    }else{
                        //点击的是section的全选
                        var ul=$(this).siblings("ul");
                        if($(this).find("i").hasClass("fa-check-square-o")){
                            $(ul).find("li.tab-zhu").find("i.fa-square-o").addClass("fa-check-square-o").removeClass("fa-square-o");
                            //表示选中的情况
                            $(this).siblings("span.pmiddle4").find("font.count").html($(ul).find("li.tab-zhu").find("i.fa-check-square-o").length);
                            $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                        }else{
                            $(ul).find("li.tab-zhu").find("i.fa-check-square-o").addClass("fa-square-o").removeClass("fa-check-square-o");
                            //表示没有选中
                            $(this).siblings("span.pmiddle4").find("font.count").html("0");
                            $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                        }
                    }
                }else{
                    var ul=$(this).siblings("ul");
                    if($(this).find("i").hasClass("fa-check-square-o")){
                        $(ul).find("li.tab-zhu").find("i.fa-square-o").addClass("fa-check-square-o").removeClass("fa-square-o");
                        //表示选中的情况
                        $(this).siblings("span.pmiddle4").find("font.count").html($(ul).find("li.tab-zhu").find("i.fa-check-square-o").length);
                        $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                    }else{
                        $(ul).find("li.tab-zhu").find("i.fa-check-square-o").addClass("fa-square-o").removeClass("fa-check-square-o");
                        //表示没有选中
                        $(this).siblings("span.pmiddle4").find("font.count").html("0");
                        $("strong.word").html($(".homeworkword").find("li.tab-zhu").find("i.fa-check-square-o").length);
                    }
                }

            }
	        e.stopPropagation();
	    });

        //教师作业发布
        $("#pubhomework").click(function(){
            var homework={};
            homework.ks_code=ks_code;
            homework.username=username;
            homework.homework=hw.publish($("body"),".word.fa-check-square-o",".text.fa-check-square-o",".exam.fa-dot-circle-o");
            
            if(homework.homework.words.length==0&&homework.homework.texts.length==0&&homework.homework.exams.length==0){
                mui.toast("请您至少选择一种在进行操作");return;
            }
						showLoading();
            $.post("../../Pubinterface/Index/publish_homework",{homework:encodeURI(JSON.stringify(homework)),source:"1",ran:Math.random()},function(data){
                hideLoading();
                //判断是不是平板的链接
                var device=0;
                try{
                    var device=getDeviceType();
                }catch(e){
                    //默认手机端的
                    device=1;
                }
                var url="";
                if(device==3){
                 //平板接口
                  url=window.location.protocol+"//"+tqms+"/tqms/pad/homework/toEnPublishForStuPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
                }else{
                 //手机接口
                  url=window.location.protocol+"//"+tqms+"/tqms/mobile/homework/toEnPublishForStuPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
                }
                //console.log(url);
                //url="http://{$tqms}/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
                //这里进行 异步的ajax请求防止程序进行阻塞
                $.get("../Index/publishData",{homeworkid:data.homeworkid,ran:Math.random()})
                openProgressController(url);
                //window.location.href=url;
                //windows.location.href="http://tqms.youjiaotong.com/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+data.name;
            });
        });

        //教师预览
        $("#previewhomework").click(function(){
            var homework={};
            homework.ks_code=ks_code;
            homework.username=username;
            homework.homework=hw.publish($("body"),".word.fa-check-square-o",".text.fa-check-square-o",".exam.fa-dot-circle-o");
            if(homework.homework.words.length==0&&homework.homework.texts.length==0&&homework.homework.exams.length==0){
                mui.toast("请您至少选择一种在进行操作");return;
            }
            showLoading();
            var quescount=0;
            if(homework.homework.exams.length>0){
                quescount=quescount+parseInt(homework.homework.exams[0].quescount);
            }
            for(var i=0;i<homework.homework.texts.length;i++){
                quescount=quescount+parseInt(homework.homework.texts[i].quescount);
            }
            quescount=quescount+homework.homework.words.length;

            var url="";
            //url="preview?username="+username+"&ks_code="+ks_code+"&ispre=1&num=0&homeworkid="+homeworkid+"&source=0&homework="+encodeURI(JSON.stringify(homework))+"&sso="+sso;
            url="preview?username="+username+"&ks_code="+ks_code+"&ispre=1&num=0&homeworkid="+homeworkid+"&source=0&sso="+sso+"&quescount="+quescount;
            url=window.location.protocol+"//"+document.domain+"/Homework/Mobhw/"+url;
            //window.location.href=url;
            //创建form表单
            // var temp_form = document.createElement("form");
            // temp_form.action = url;
            // //如需打开新窗口，form的target属性要设置为'_blank'
            // temp_form.target = "_blank";
            // temp_form.method = "post";
            // temp_form.style.display = "none";
            // //添加参数
            // var opt = document.createElement("textarea");
            // opt.name = "homework";
            // opt.value = encodeURI(JSON.stringify(homework));
            if(!window.localStorage){
                mui.toast("您的手机不支持预览功能");return false;
            }
            var storage=window.localStorage;
            storage.removeItem("homework");
            storage.setItem("homework",encodeURI(JSON.stringify(homework)));
            //window.location.href=url;
            hideLoading();
            openProgressController(url);
            //temp_form.appendChild(opt);
            //document.body.appendChild(temp_form);
            //提交数据
            //temp_form.submit();
        });
	});
});


