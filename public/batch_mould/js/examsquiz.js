var mp = '';
var mp3_progress='';
var mp3_progress_reap="";
var paper_id,std_id=getUserInfo("registername"),std_name=getUserInfo("realname"),studentclass=getUserInfo("classid"),surl,curl;
//alert("std_id="+std_id+"==studentclass="+studentclass);
var  _status = getPaperData('state_allow_answer');//1允许答题0、不允许答题 2互评 3自评
var mand=false,createNum=0;//强制提交
var useranswer = "";
var trueanswer = "";
var answerObj = [];
var question_id = "";
var saveAnswerData={};
function Answer(question_id, useranswer, answer_result, answer_status) {
	this.question_id = question_id;
	this.answer = useranswer;
	this.answer_result = answer_result;
	this.answer_status = answer_status;
}
function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串

    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    ////console.log(theRequest);
    return theRequest;
}


////console.log(Requests);
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


//判断函数是否存在
function isExitsFunction(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

//答题卡数据的输出
$(function() {
	if(mobile.isEmpty(std_id)||mobile.isEmpty(std_name)||mobile.isEmpty(studentclass)){
		showAlert("获取用户信息失败");
		setTimeout("popTheController()", 1000 );
		//return;
	}
    $.ajaxSetup({async: false});
    $("#jplayer").jPlayer({
        swfPath: 'js',
        wmode: "window",
        supplied: "mp3",
        preload: "none",
        volume: "1"
    });
    mp = new myplay();
    mp.clear();
 //    $.getJSON("getQuestionCard",{homeworkid:homeworkid,username:username,ran:Math.random()},function(data){
	// 	$("#questioncard").tmpl(data).appendTo("#card");
	// });
	// $.getJSON("json/batch.json",{ran:Math.random()},function(data){

	// })
     //将数据直接缓存在本地
		// if(!questions){
		// 	storage.removeItem("homeworkid");
		// 	storage.setItem("homeworkid",homeworkid);
		// 	storage.removeItem("questions");
		// 	storage.setItem("questions",encodeURI(JSON.stringify(data)));
		// }
	// $.ajax({
 //        url: 'json/batch.json',
 //        async: false,
 //        success: function (data) {
 //            ipAddress = data.ip;
 //        }
 //    });
		questions=resultJosn.questions;
		$("#tmplquestion").tmpl(questions).appendTo("#iScroll-bd");
		$("#questioncard").tmpl(resultJosn).appendTo("#card");
		quescount=questions.length;
		$(".examsname").html(resultJosn["examsname"]);
		//console.log(index);
		trueanswer = questions[0].answer;
		surl = resultJosn.saveurl;
	    slide(index);
	    
	    function slide(index){
	    	TouchSlide({
			slideCell: "#iScroll",
			prevCell : '#wpre',
	        nextCell : '#wnxt',
	        defaultIndex:index,
	        startFun:function(i){
	        	curindex=i;
	        	pageindex=i;
	        	var oldindex=$(".con.cur").index();
	        	try{
	        		$(".con.cur").find(".read").attr("src","images/sy.png");
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
	        		trueanswer = questions[curindex].answer;
	        		mp.pause();
	        		mp.clear();
	        		clearTimeout(mp3_progress);
        			clearTimeout();
        			mp.playtimes=0;
        			mp.questionindex=0;
	        	}catch(e){
	        		mp.pause();
	        		mp.clear();
	        		clearTimeout(mp3_progress);
	        		clearTimeout();
	        		mp.playtimes=0;
	        		mp.questionindex=0;
	        		//console.log("清除音频");
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

	        	//设置当前是第几个

	        	var off=document.getElementsByClassName("load-container")[i].offsetTop;
	        	$("#card").scrollTop(off-$(window).height()+200);
	        }
		});
	    }

	//$('.datiLeft,.datiRight,.posR').height($(window).height() - 100);
	//修改box的高度
	$(".box").height($(window).height() - 50);
	$('.timu').height($(window).height() - 200);
	$('.timu2,.ediT2').height($(window).height() - 400);
	$('.lfl').height($(window).height() - 190);
	$('.handle').css('left',($('.ediT2').width()-$('.handle').width()) / 2 );
	var tanH = $('#tanS').height();
	$('#tanS').css('top', ($(window).height() - tanH) / 2);
	$('.zyMenu li.not').click(function() {
		$(this).append("<i class='icon-right2'></i>");
		$(this).addClass('cur');
		$(this).siblings('li').removeClass('cur');
		$(this).siblings('li').children('i').remove();
	})
	$('.qie a').click(function() {
		ss = $(this).index();
		$(this).addClass('cur');
		$(this).siblings('a').removeClass('cur');
		$(this).parent().next('.qieB').children('li').eq(ss).show();
		$(this).parent().next('.qieB').children('li').eq(ss).siblings().hide();
	})

	var tanH = $('#tanS').height();
	$('#tanS').css('top', ($(window).height() - tanH) / 2);
	$('#close').click(function() {
		$('#tanS').hide();
		$('#bg').hide();
	})



	$(document).on("click",".load-container a",function(){
		//将本试题中的所有的样式去掉
		var index=$(".load-container a").index($(this));
		var starttime=0;
		//window.location.href="?index="+index;
		slide(index);

	})


	//采用的是时间委托进事件的注册
	$(document).on("click","p.zyp",function(){
		// if(isOverdue=='true'){
		// 	mui.toast("作业已经过期");
		// 	return false;
		// }
		//将本试题中的所有的样式去掉
		$(this).parent().find("span.ball").css("background","#fff");
		$(this).find("span.ball").css("background","green");
		var type=$(this).attr("type");
		var id=$(this).attr("bid");
		var iserror=$(this).attr("iserror");
		useranswer=$(this).attr("flag");
		var score=$(this).attr("score");
		var contentid=$(this).attr("contentid");
		var typeid=$(this).attr("typeid");
		var pd_answer=$(this).attr("pd_answer");
		//把答案进行数据的存储
		//storage.setItem("questions",encodeURI(JSON.stringify(questions)));
		//同时修改questionscard中试题的样式

		//修改颜色
		if($("span.load-container").eq(curindex).find("a").hasClass("bGray")){
			$("span.load-container").eq(curindex).find("a").addClass("bGreen").removeClass("bGray");
		}
		$("span.load-container").eq(curindex).find("a").attr("question_id",id).attr("useranswer",useranswer).attr("trueanswer",trueanswer).attr("typeid",typeid).attr("pd_answer",pd_answer);
       // console.log(trueanswer+"!!"+useranswer);

		//$.getJSON("saveAnswer",{typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()});
	})

	//进行读音频的事件的委托注册
	$(document).on("click",".read",function(){
		//将本试题中的所有的样式去掉
		mp.clear();
		clearTimeout(mp3_progress);
		mp.playtimes=0;
		mp.questionindex=0;
		////console.log(questions[curindex].tts);
		var questype=questions[curindex].type;
		//console.log(curindex);
		if(questype!=5){
			question_play(1,questions[curindex].tts,$(this));
		}else{
			question_play(parseInt(questions[curindex].question_playtimes),questions[curindex].tts,$(this));
		}

	})

	//进行读音频的事件的委托注册

	//交作业响应事件
	//hwid=d3074193cf8f44c5946acac5b4dca1cb&homeworkid=4900&classId=147001643914524565&studentId=41010110001121&tms=tms.youjiaotong.com&batchid=cd947229d2a542898d6998f7235e6d9c
	$("#submithomework").click(function(){
		if(typeof(std_id) ==  "undefined"){
		
			std_id = "vcomhxp";
		}
		var anMaps = {};
		
	   $("span.load-container").find("a").each(function(i){
	   	var userresult = {};
	   	if($(this).hasClass("bGreen"))
	   		{
	   		  if($(this).attr("useranswer") == $(this).attr("trueanswer")){
              
             
              userresult.anwser_result=100;
              userresult.anwser_status=2;
	         	
	         }
	         else{
	           userresult.anwser_result=99;
              userresult.anwser_status=2;
	         	
	         }
						userresult.question_id=$(this).attr("question_id");
						if($(this).attr("typeid") == "3"){
							userresult.anwser=$(this).attr("pd_answer");
						}
						else{
							userresult.anwser=$(this).attr("useranswer");
						}
              
              answerObj.push(userresult);
	   		}
		   	
		 });
		
	     anMaps[std_id] = answerObj;
			 saveAnswerData.paper_id = resultJosn.examsid;
			 saveAnswerData.username = resultJosn.username;
			 saveAnswerData.truename = resultJosn.truename;
			 saveAnswerData.tqms = resultJosn.tqms;
       saveAnswerData.studentClass = studentclass;
			 saveAnswerData.anMaps = anMaps;
			// alert(JSON.stringify(saveAnswerData));
       //console.log(JSON.stringify(saveAnswerData));
       showAlert("正在保存");
      //  $.getJSON(surl+"?jsoncallback=?",{jsondata:JSON.stringify(saveAnswerData),ran:Math.random()},function(){
			// 	//  sendCommand("endquestiona",location.pathname);//发送指令
			// 	 sendCommand("questiona",location.pathname);//发送指令
      //  	popTheController();//关闭当前的webview
			//  })
			//alert(surl);
			$.ajax({
				url:surl,
				type:"POST",
				dataType: 'jsonp',
				jsonp: "jsoncallback",
				data:{jsondata:JSON.stringify(saveAnswerData),ran:Math.random()},
				success:function(result){
				//	alert("提交成功");
					sendCommand("questiona",location.pathname);//发送指令
					popTheController();//关闭当前的webview
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){  
				//	alert(XMLHttpRequest.readyState + XMLHttpRequest.status + XMLHttpRequest.responseText);  
				}  
				
			})
	})
	
});

function question_play(playtimes,quettsdata,obj){
	//console.log(quettsdata);
	//console.log('-----');
	clearTimeout(mp3_progress);
    var smallquetts = '';
    if(mp.playtimes<playtimes){
    	if(mp.questionindex<quettsdata.length){
    		$(obj).attr("src","images/sy.gif");
	        smallquetts = quettsdata[mp.questionindex];
	        //console.log(quettsdata);
	        playurl = quettsdata[mp.questionindex].playmp3;
	        //console.log(playurl);
	        mp.play(playurl);
	        $("#jplayer").bind($.jPlayer.event.ended,function(event){
	        	$(obj).attr("src","images/sy.png");
	        	//console.log(quettsdata);
	        	//console.log('1111111');
	            if(mp.questionindex<quettsdata.length){
	            	mp.questionindex = mp.questionindex +1;
	            	clearTimeout(mp3_progress);
	            	mp.clear();
	            	mp3_progress = setTimeout(function(){
	            		//console.log(quettsdata);
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
    	//console.log("afasdfasd");
    	mp.clear();
    	mp.playtimes=0;
		mp.questionindex=0;
		$(obj).attr("src","images/sy.png");
		return ;
		//clearTimeout(mp3_progress);
    }
}

//强制提交
function recvCommand(type){
	if(type == "endquestiona" || type == "endpreview"){
		goBackHome(1);
	}else if(type == "cancelquestiona"){
		  sendCommand("cancelquestiona",location.pathname);//发送指令
			goBackHome(1);
	}
}