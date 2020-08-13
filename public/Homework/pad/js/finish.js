var mp = '';
var mp3_progress='';
var mp3_progress_reap="";
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
	oplay.stemindex = 0;
	oplay.queinitindex = 0;
	oplay.questionindex = 0;
	oplay.childstemindex = 0;
    oplay.playtimes=0;
    oplay.childinitstemindex = 0;
    oplay.url = "";
    oplay.repeat = 1; //默认播放次数
    oplay.curpeat = 1;//当前播放到第几次
    oplay.url = "";
  
    oplay.play = function(mp3) {
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    }
    oplay.clear = function() {

        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        //$("#jplayer").data("SpeakMP3Value", "0");
     $("#jplayer").unbind($.jPlayer.event.ended);
     $("#jplayer").unbind($.jPlayer.event.progress);
    };
    return oplay;
}

//答题卡数据的输出
$(function() {
    $.ajaxSetup({async: false});
    $("#jplayer").jPlayer({
        swfPath: '/public/Homework/js',
        wmode: "window",
        supplied: "mp3",
        preload: "none",
        volume: "1"
    });
    mp = new myplay();
    mp.clear();
    $.getJSON("getUserQuestionCard",{iserror:iserror,homeworkid:homeworkid,username:username,iserror:iserror,ran:Math.random()},function(data){
		$("#questioncard").tmpl(data).appendTo("#card");
	});
	$.getJSON("getHomeworkFinishQuestions",{iserror:iserror,homeworkid:homeworkid,username:username,classid:classid,ran:Math.random()},function(data){
		//将数据直接缓存在本地
		if(!questions){
			storage.removeItem("questions");
			storage.removeItem("fhomeworkid");
			storage.setItem("fhomeworkid",homeworkid);
			storage.removeItem("questions");
			storage.setItem("questions",encodeURI(JSON.stringify(data)));
		}
		questions=data;
		$("#tmplquestion").tmpl(questions).appendTo("#iScroll-bd");
		quescount=data.length;
		TouchSlide({
			slideCell: "#iScroll",
			prevCell : '#wpre',
	        nextCell : '#wnxt',
	        defaultIndex:index,
	        startFun:function(i){
	        	pageindex=i;
	        	curindex=i;
	        	var oldindex=$(".con.cur").index();
	        	try{
	        		$(".con.cur").find(".read").attr("src","../../public/Homework/images/sy.png");
	        	}catch(e){

	        	}
	        	//去掉之前的
	        	$(".con.cur").removeClass("cur");
	        	$(".con").eq(i).addClass("cur");
	        	$("span.load-container").find("div.loader").removeClass("loader");
	        	$("span.load-container").eq(i).find("div").addClass("loader");
	        	
	        	//下面的录音按钮的隐藏和显示
	        	//clearTimeout();
	        	try{
	        		//当前的questype
	        		var oldquestype=questions[oldindex].type;
	        		var curquestype=questions[curindex].type;
	        		if(oldquestype==5&&curquestype==5&&questions[oldindex].stemid==questions[curindex].stemid&&questions[oldindex].parentid!='0'&&questions[curquestype].parentid!='0'){
	        			//$(".con.cur").find(".read").attr("src","../../public/Homework/images/sy.png");
	        		}else{
	        			mp.pause();
		        		mp.clear();
		        		clearTimeout(mp3_progress);
	        			clearTimeout();
	        			mp.playtimes=0;
	        			mp.questionindex=0;
	        		}
	        	}catch(e){
	        		mp.pause();
	        		mp.clear();
	        		clearTimeout(mp3_progress);
	        		clearTimeout();
	        		mp.playtimes=0;
	        		mp.questionindex=0;
	        		console.log("清除音频");
	        	}
	        	
	        	if(questions[curindex].type!=0&&questions[curindex].type!=4){
	        		$(".bbtR .fl").hide();
	        	}else{
	        		$(".bbtR .fl").show();
	        	}
	        },
	        endFun:function(i){
	        	
	        	if(i==0){
	        		$("#wpre").removeClass("bGreen");
	        		$("#wpre").attr("disabled",true);
	        	}else{
	        		$("#wpre").addClass("bGreen");
	        		$("#wpre").attr("disabled",false);
	        	}
	        	if(i==(quescount-1)){
	        		$("#wnxt").removeClass("bGreen");
	        		$("#wnxt").attr("disabled",true);
	        	}else{
	        		$("#wnxt").addClass("bGreen");
	        		$("#wnxt").attr("disabled",false);
	        	}

	        	var off=document.getElementsByClassName("load-container")[i].offsetTop;
	        	$("#card").scrollTop(off-$(window).height()+200);
	        	$("#iScroll-bd").height($(".con.cur ul").height());
	        	$('#iScroll').animate({scrollTop:0},500);
	        	// console.log($("#iScroll").css('top'));
	        }
		});
	})

	//返回操作
	$(".head-left").click(function(){
		if(iserror=='0'){
			try{clearTimeout(mp3_progress);}catch(e){console.log(e);}
			try{mp.pause();mp.clear();}catch(e){console.log(e);}
			try{UXinJSInterface.setOnlinWorkHaveFinished(batchid);}catch(e){console.log(e);}
			UXinJSInterface.popTheController();
		}else{
			try{clearTimeout(mp3_progress);}catch(e){console.log(e);}
			try{mp.pause();mp.clear();}catch(e){console.log(e);}
			try{UXinJSInterface.setOnlinWorkHaveFinished(batchid);}catch(e){console.log(e);}
			UXinJSInterface.popTheController();
		}
	})
		

	$('.datiLeft,.datiRight,.posR,#iScroll').height($(window).height() - 50);
	$('.lfl').height($(window).height() - 190);
	var tanH = $('#tanS').height();
	$('#tanS').css('top',($(window).height() - tanH)/2);


	$('.datiLeft,.datiRight,.posR').height($(window).height() - 50);

	//$('.timu').height($(window).height() - 200);
	//$('.timu2,.ediT2').height($(window).height() - 400);
	//$('.lfl').height($(window).height() - 190);
	//$('.handle').css('left',($('.ediT2').width()-$('.handle').width()) / 2 );
	//var tanH = $('#tanS').height();
	$('#tanS').css('top', ($(window).height() - tanH) / 2);

	$('.zyMenu li.not').click(function(){
		$(this).append("<i class='icon-right2'></i>");
		$(this).addClass('cur');
		$(this).siblings('li').removeClass('cur');
		$(this).siblings('li').children('i').remove();
	})
	$('.qie a').click(function(){
		ss = $(this).index();
		$(this).addClass('cur');
		$(this).siblings('a').removeClass('cur');
		$(this).parent().next('.qieB').children('li').eq(ss).show();
		$(this).parent().next('.qieB').children('li').eq(ss).siblings().hide();
	})

	var tanH = $('#tanS').height();
	$('#tanS').css('top',($(window).height() - tanH)/2);
	$('#tan').click(function(){
	   $('#bg').show();
	   $('#tanS').show();
	})
	$('#close').click(function(){
	$('#tanS').hide();
		$('#bg').hide();
	})

	//错题解析
	$("#errorques").click(function(){
		if($(".load-container").find("i.icon-dui").length!=$(".load-container").length){
			window.location.href="?homeworkid="+homeworkid+"&classId="+classid+"&studentId="+username+"&tms="+sso+"&hwid="+hwid+"&starttime=0&batchid="+batchid+"&callbackURL=&type="+type+"&isOverdue="+isOverdue+"&index="+index+"&iserror=1";
		}else{
			alert("恭喜您全部答对了！");
		}
		
	})

	//错题解析
	$("#allerrorques").click(function(){
		window.location.href="?homeworkid="+homeworkid+"&classId="+classid+"&studentId="+username+"&tms="+sso+"&hwid="+hwid+"&starttime=0&batchid="+batchid+"&callbackURL=&type="+type+"&isOverdue="+isOverdue+"&index="+index+"&iserror=0";
	})

	//进行读音频的事件的委托注册
	$(document).on("click",".myrecord",function(){
        //播放时间的MP3的地址
        var id=$(".con").eq(pageindex).attr("userreadid");
        var type=$(".con").eq(pageindex).attr("type");
        if(id==undefined||id==''||id=='null'||id==null){
        	mui.toast("您没有作答该试题");
        	return false;
        }
        //var location=$(this).attr("loc");
        console.log("start");
        mui.ajax("../Public/playBack",
            {
            data:{
                id:id,
                type:type,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:50000,
            async:false,
            success:function(data){
                if(data.filename==""||data.filename==null||data.filename==undefined){
                    mui.toast("请先进行录音");
                }else{
                    console.log("http://"+document.domain+recordlocation+data.filename);
                    mp.play(window.location.protocol+"://"+document.domain+recordlocation+data.filename);   
                }
                
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                mui.toast("网络出错,请稍等一会在尝试");
            }
        });
	})

	$(document).on("click",".load-container a",function(){
		//将本试题中的所有的样式去掉
		var index=$(".load-container a").index($(this));
		var starttime=0;
		window.location.href="?homeworkid="+homeworkid+"&classId="+classid+"&studentId="+username+"&tms="+sso+"&hwid="+hwid+"&iserror="+iserror+"0&batchid="+batchid+"&callbackURL=&type="+type+"&isOverdue="+isOverdue+"&index="+index+"&iserror="+iserror;

	})

	//进行读音频的事件的委托注册
	$(document).on("click",".read",function(){
		//将本试题中的所有的样式去掉
		mp.clear();
		clearTimeout(mp3_progress);
		mp.playtimes=0;
		mp.questionindex=0;
		//console.log(questions[curindex].tts);
		var questype=questions[curindex].type;
		console.log(curindex);
		if(questype!=5){
			question_play(1,questions[curindex].tts,$(this));
		}else{
			question_play(parseInt(questions[curindex].question_playtimes),questions[curindex].tts,$(this));
		}
		
	})

	//进行读音频的事件的委托注册
	$(document).on("click",".record",function(){
		//将本试题中的所有的样式去掉
		//alert("record");
	})

	//进行读音频的事件的委托注册
	$(document).on("click",".myrecord",function(){
		//将本试题中的所有的样式去掉
		//alert("myrecord");
	})
});

function question_play(playtimes,quettsdata,obj){
	console.log(quettsdata);
	console.log('-----');
	clearTimeout(mp3_progress);
    var smallquetts = '';
    if(mp.playtimes<playtimes){
    	if(mp.questionindex<quettsdata.length){
    		$(obj).attr("src","../../public/Homework/images/sy.gif");
	        smallquetts = quettsdata[mp.questionindex];
	        console.log(quettsdata);
	        playurl = quettsdata[mp.questionindex].playmp3;
	        console.log(playurl);
	        mp.play(playurl);
	        $("#jplayer").bind($.jPlayer.event.ended,function(event){
	        	$(obj).attr("src","../../public/Homework/images/sy.png");
	        	console.log(quettsdata);
	        	console.log('1111111');
	            if(mp.questionindex<quettsdata.length){
	            	mp.questionindex = mp.questionindex +1;
	            	clearTimeout(mp3_progress);
	            	mp.clear();
	            	mp3_progress = setTimeout(function(){
	            		console.log(quettsdata);
	                    question_play(playtimes,quettsdata,obj);
	                },parseInt(quettsdata[mp.questionindex-1].stoptime)*1000);
	            }
	        });
    	}else{
    		mp.playtimes=mp.playtimes+1;
    		mp.questionindex=0;
            mp3_progress = setTimeout(function(){
            	mp.clear();
                question_play(playtimes,quettsdata,obj);
            },3000);
    	}
    }else{
    	console.log("afasdfasd");
    	mp.clear();
    	mp.playtimes=0;
		mp.questionindex=0;
		$(obj).attr("src","../../public/Homework/images/sy.png");
		return ;
		//clearTimeout(mp3_progress);
    }
}
