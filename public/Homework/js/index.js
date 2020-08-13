//教师布置作业的页面
mui.init();    
$(function(){
    $(".orangFon.select").each(function(){
       $(this).html($(this).parents("li").next("ul").find("i.fa-check-c-o").length);
    });
});

 $(".zdie").click(function() {
    $(this).parent().next("ul").toggle();
    $(this).children("label").children("i").toggleClass("fa-minus-circle");
    $(this).children("label").children("i").toggleClass("fa-plus-circle");
});
$(".zdie:not(:last)").click();
$("ul").find("li").css("display","");

//单元素选中
$(".tab-zhu").click(function(){
    if($(this).hasClass("word_read")){
    	$(this).find("i").toggleClass("fa-check-square-o");
    	$(this).find("i").toggleClass("fa-square-o");
      if($(this).parent().find(".tab-wb20 i.fa-square-o").length>0){
        $(".wr").removeClass("fa-check-square-o");
        $(".wr").addClass("fa-square-o");
      }else{
        $(".wr").removeClass("fa-square-o");
        $(".wr").addClass("fa-check-square-o");
      }
      $(this).parent().prev("li").find(".select").html($(this).parent().find("i.fa-check-square-o").length);
      $("#zy").html($("i.child.fa-check-square-o").length);
    }else if($(this).hasClass("word_test")){
    	$(this).find("i").toggleClass("fa-check-square-o");
    	$(this).find("i").toggleClass("fa-square-o");
        if($(this).parent().find(".tab-wb20 i.fa-square-o").length>0){
            $(".wt").removeClass("fa-check-square-o");
            $(".wt").addClass("fa-square-o");
        }else{
            $(".wt").removeClass("fa-square-o");
            $(".wt").addClass("fa-check-square-o");
        }
        $(this).parent().prev("li").find(".select").html($(this).parent().find("i.fa-check-square-o").length);
    }else if($(this).hasClass("text_read")){
    	$(this).find("i").toggleClass("fa-check-square-o");
    	$(this).find("i").toggleClass("fa-square-o");
        if($(this).parent().find(".tab-wb20 i.fa-square-o").length>0){
            $(".tr").removeClass("fa-check-square-o");
            $(".tr").addClass("fa-square-o");
        }else{
            $(".tr").removeClass("fa-square-o");
            $(".tr").addClass("fa-check-square-o");
        }
        $(this).parent().prev("li").find(".select").html($(this).parent().find("i.fa-check-square-o").length);
        $("#zy").html($("i.child.fa-check-square-o").length);
    }else if($(this).hasClass("exams_quiz")){
      if($(this).find("i").hasClass("fa-dot-circle-o")){
         $(".eq").removeClass("fa-dot-circle-o").addClass("fa-circle-o");
       }else{
         $(this).children("i").toggleClass("fa-dot-circle-o");
         $(this).children("i").toggleClass("fa-circle-o");
         $(".eq").removeClass("fa-dot-circle-o").addClass("fa-circle-o");
         $(this).find("i").addClass("fa-dot-circle-o");
       }
       $(this).parent().prev("li").find(".select").html($(this).parent().find("i.fa-dot-circle-o").length);
       $("#zy").html($("i.child.fa-dot-circle-o").length);
    }
});
//多元素全部选中
$(".pmiddle3").click(function(){
	var obj=$(this).parent().find(".po-r-w100");
	var flag=$(obj).children("i").hasClass("fa-check-square-o");
    $(obj).children("i").toggleClass("fa-square-o");
    $(obj).children("i").toggleClass("fa-check-square-o");
    if($(obj).children("i").hasClass("wr")){
      if(flag){
        $("li.word_read").each(function(){
             if($(this).find("i").hasClass("fa-check-square-o")){
               $(this).click();
             }
        });
      }else{
        $("li.word_read").each(function(){
             if($(this).find("i").hasClass("fa-square-o")){
               $(this).click();
             }
        });
      }
    }else if($(obj).children("i").hasClass("wt")){
      if(flag){
        $("li.word_test").each(function(){
             if($(this).find("i").hasClass("fa-check-square-o")){
               $(this).click();
             }
        });
      }else{
        $("li.word_test").each(function(){
             if($(this).find("i").hasClass("fa-square-o")){
               $(this).click();
             }
        });
      }
    }else if($(obj).children("i").hasClass("tr")){
      if(flag){
        $("li.text_read").each(function(){
             if($(this).find("i").hasClass("fa-check-square-o")){
               $(this).click();
             }
        });

      }else{
        $("li.text_read").each(function(){
             if($(this).find("i").hasClass("fa-square-o")){
               $(this).click();
             }
        });

      }


    }else if($(obj).children("i").hasClass("eq")){
      if(flag){
        $("li.exams_quiz").each(function(){
             if($(this).find("i").hasClass("fa-check-square-o")){
               $(this).click();
             }
        });
      }else{
        $("li.exams_quiz").each(function(){
             if($(this).find("i").hasClass("fa-square-o")){
               $(this).click();
             }
        });
      }
    }
    $(obj).parent().find(".select").html($(obj).parent().next("ul").find("i.fa-check-square-o").length);
});
//单独的复选框的选中
$(".po-r-w100").click(function(){
	$(this).parent().find(".pmiddle3").click();
});
//发布作业单击事件
$(".pubhomework").click(function(){
    var wa=[];
    var wt=[];
    var ta=[];
    var eq=[];
    if($("ul.wa li i.fa-check-square-o").length>0){
      $("ul.wa li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        wa.push(obj);
      });
    }
    if($("ul.wht li i.fa-check-square-o").length>0){
      $("ul.wht li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        wt.push(obj);
      });
    }
    if($("ul.ta li i.fa-check-square-o").length>0){
      $("ul.ta li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        ta.push(obj);
      });
    }
    if($("ul.exq li i.fa-dot-circle-o").length>0){
      $("ul.exq li i.fa-dot-circle-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        eq.push(obj);
      });
    }
    if($("ul.wa li i.fa-check-square-o").length==0&&$("ul.wht li i.fa-check-square-o").length==0&&$("ul.ta li i.fa-check-square-o").length==0&&$("ul.exq li i.fa-dot-circle-o").length==0){
      mui.toast("请您选中之后再进行发布");
      return false;
    }
    if($("ul.exq li i.fa-dot-circle-o").length>1){
      mui.toast("听力试卷至多选择一套");
      return false;
    }
    $(this).attr("disbaled",true);
     //openProgressController("http://tqms.youjiaotong.com/tqms/mobile/homework/toEnPublishPage.action?username="+username+"&paper_id=123&ks_code="+ks_code+"&paper_name=123456");
    //var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
    $.post("teacher_english_publish",{ks_code:ks_code,username:username,wa:encodeURI(JSON.stringify(wa)),wt:encodeURI(JSON.stringify(wt)),ta:encodeURI(JSON.stringify(ta)),eq:encodeURI(JSON.stringify(eq)),ran:Math.random()},function(data){
        //dloading.close();
        //判断是不是平板的链接
        var device=0;
        try{
        	var device=getDeviceType();
        }
        catch(e){
        	//默认手机端的
        	device=1;
        }
        
        var url="";
       	if(device==3){
         //平板接口
          url=window.location.protocol+"://"+tqms+"/tqms/pad/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
       	}else{
         //手机接口
          url=window.location.protocol+"://"+tqms+"/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
       	}
       	 //console.log(url);
        //url="http://{$tqms}/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);

        openProgressController(url);
        //windows.location.href="http://tqms.youjiaotong.com/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+data.name;
    });
});

//预览作业单击事件
$(".dd-fen2.fl_right").click(function(){
	//预览数据库
	//websqlOpenDB();
    //直接进行预览页面的跳转在试卷预览中采用get方式进行数据的传输暂时解决方案四种类型的数据总共就是四个参数
    var wa=[];
    var wt=[];
    var ta=[];
    var eq=[];
    //单词跟读
    if($("ul.wa li i.fa-check-square-o").length>0){
      $("ul.wa li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        wa.push(obj);
      });
    }
    //单词测试
    if($("ul.wht li i.fa-check-square-o").length>0){
    	//alert("aaaaa");
      $("ul.wht li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        //alert(obj.id);
        wt.push(obj);
      });
    }
    //课文跟读
    if($("ul.ta li i.fa-check-square-o").length>0){
      $("ul.ta li i.fa-check-square-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        ta.push(obj);
      });
    }
    //听力训练
    if($("ul.exq li i.fa-dot-circle-o").length>0){
      $("ul.exq li i.fa-dot-circle-o").each(function(key,value){
        var obj={};
        obj.id=$(this).parent().parent().find("span:eq(0)").attr("wordid");
        obj.quescount=$(this).parent().parent().find("span:eq(0)").attr("quescount");
        eq.push(obj);
      });
    }
    if($("ul.wa li i.fa-check-square-o").length==0&&$("ul.wht li i.fa-check-square-o").length==0&&$("ul.ta li i.fa-check-square-o").length==0&&$("ul.exq li i.fa-dot-circle-o").length==0){
      mui.toast("请您选中之后再进行预览");
      return false;
    }
    if($("ul.exq li i.fa-dot-circle-o").length>1){
      mui.toast("听力试卷至多选择一套");
      return false;
    }
    var url="";
    url="preview?username="+username+"&ks_code="+ks_code+"&ispre=1&num=0&homeworkid="+homeworkid+"&source=0&wa="+encodeURI(JSON.stringify(wa))+"&wt="+encodeURI(JSON.stringify(wt))+"&ta="+encodeURI(JSON.stringify(ta))+"&eq="+encodeURI(JSON.stringify(eq))+"&sso="+sso;
    url=window.location.protocol+"://"+document.domain+"/Homework/Mobhw/"+url;
    //window.location.href=url;
    openProgressController(url);
});



$(function(){
	$("#nipic_search .tipSwitch").live("click",function(){
		showSearchTip();
		setSearchTip();
		SetCookie("tStatus",1);
	});
})

function nextStep(next){
	$(".tipbox").css({"visibility":"hidden","display":"none"});
	$(".tipbar").hide();
	$("#step" + next).css({"visibility":"visible","display":"block"});
	$("#tipbar" + (next -1)).show();
	if(next == 4){
		$("#searchTip").css("top","378px");
	}else {
		$("#searchTip").css("top","270px");
	}
	if(next == 4){
		$(".tipSwitchAnimate").css("top","539px");
	}else if(next == 6){
		$(".tipSwitchAnimate").css("top","357px");
	}else {
		$(".tipSwitchAnimate").css("top","352px");	
	}
	$(".tipSwitchAnimate").css("left","410px");
}
