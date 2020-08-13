var mp = '';
var mp3_progress = '';
var mp3_progress_reap = "";
var curindex = 0;
var paper_id, std_id = getUserInfo("registername"),
	std_name = getUserInfo("realname"),
	studentclass = getUserInfo("classid"),
	surl, curl;
var _status = getPaperData('state_allow_answer'); //1允许答题0、不允许答题 2互评 3自评
var mand = false,
	createNum = 0; //强制提交
var useranswer = "";
var trueanswer = "";
var answerObj;
var question_id = "";

function Answer(question_id, answer, answer_result, answer_status) {
	this.question_id = question_id;
	this.answer = answer;
	this.answer_result = answer_result;
	this.answer_status = answer_status;
}

function myplay() {
	var oplay = new Object();
	oplay.index = 0;
	oplay.stemindex = 0;
	oplay.queinitindex = 0;
	oplay.questionindex = 0;
	oplay.childstemindex = 0;
	oplay.playtimes = 0;
	oplay.childinitstemindex = 0;
	oplay.url = "";
	oplay.repeat = 1; //默认播放次数
	oplay.curpeat = 1; //当前播放到第几次
	oplay.url = "";

	oplay.play = function (mp3) {
		$("#jplayer").jPlayer("setMedia", {
			mp3: mp3
		}).jPlayer("play");
	};

	oplay.pause = function () {
		$("#jplayer").jPlayer("pause");
		$("#jplayer").unbind($.jPlayer.event.ended);
		$("#jplayer").unbind($.jPlayer.event.progress);
	}
	oplay.clear = function () {

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
		if (typeof (eval(funcName)) == "function") {
			return true;
		}
	} catch (e) {}
	return false;
}

//答题卡数据的输出
$(function () {
	if (mobile.isEmpty(std_id) || mobile.isEmpty(std_name) || mobile.isEmpty(studentclass)) {
		showAlert("获取用户信息失败");
		setTimeout("popTheController()", 1000);
		//return;
	}
	$.ajaxSetup({
		async: false
	});
	$("#jplayer").jPlayer({
		swfPath: 'js',
		wmode: "window",
		supplied: "mp3",
		preload: "none",
		volume: "1"
	});
	mp = new myplay();
	mp.clear();
	// $.getJSON("json/question.json",{ran:Math.random()},function(data){
	// 	//将数据直接缓存在本地

	// 	questions=data;
	// 	$("#tmplquestion").tmpl(questions).appendTo("#iScroll-bd");
	// 	quescount=data.length;
	// 	trueanswer = questions[0].trueanswer[0].trueanswer;
	// 	//console.log(questions);

	// })
//	alert(getPaperData('content_single_paper'));
	$.ajax({
		url: getPaperData('content_single_paper'),
		//  url: 'http://192.168.2.135:8899/20200612/down/qone/90838171627725.json',
		dataType: 'jsonp',
		scriptCharset: 'gbk',
		jsonpCallback: 'getQue',
		success: function (data) {
			//	console.log("ssss");
			questions = data;
			$("#tmplquestion").tmpl(questions).appendTo("#iScroll-bd");
		//	$("#tmplquestion").tmpl(questions).appendTo("#iScroll-bd");
			quescount = data.length;
			trueanswer = questions[0].trueanswer[0].trueanswer;
			//alert(trueanswer);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			/*jqXHR对象的信息*/
			//console.log('jqXHR.responseText --> ', jqXHR.responseText);
			// console.log('jqXHR.status --> ', jqXHR.status);
			// console.log('jqXHR.readyState --> ', jqXHR.readyState);
			// console.log('jqXHR.statusText --> ', jqXHR.statusText);
			// /*其他两个参数的信息*/
			// console.log('textStatus --> ', textStatus);
			// console.log('errorThrown --> ', errorThrown);
		}
	})
	//$('.datiLeft,.datiRight,.posR').height($(window).height() - 100);
	//修改box的高度
	$(".box").height($(window).height() - 50);
	$('.timu').height($(window).height() - 200);
	$('.timu2,.ediT2').height($(window).height() - 400);
	$('.lfl').height($(window).height() - 190);
	$('.handle').css('left', ($('.ediT2').width() - $('.handle').width()) / 2);
	var tanH = $('#tanS').height();
	$('#tanS').css('top', ($(window).height() - tanH) / 2);
	$('.zyMenu li.not').click(function () {
		$(this).append("<i class='icon-right2'></i>");
		$(this).addClass('cur');
		$(this).siblings('li').removeClass('cur');
		$(this).siblings('li').children('i').remove();
	})
	$('.qie a').click(function () {
		ss = $(this).index();
		$(this).addClass('cur');
		$(this).siblings('a').removeClass('cur');
		$(this).parent().next('.qieB').children('li').eq(ss).show();
		$(this).parent().next('.qieB').children('li').eq(ss).siblings().hide();
	})

	var tanH = $('#tanS').height();
	$('#tanS').css('top', ($(window).height() - tanH) / 2);
	$('#close').click(function () {
		$('#tanS').hide();
		$('#bg').hide();
	})

	//采用的是时间委托进事件的注册
	//点击选项后保存
	$(document).on("click", "p.zyp", function () {
		//将本试题中的所有的样式去掉
		$(this).parent().find("span.ball").css("background", "#fff");
		$(this).find("span.ball").css("background", "green");
		var type = $(this).attr("type");
		var id = $(this).attr("bid");
		question_id = $(this).attr("bid");
		var iserror = $(this).attr("iserror");
		useranswer = $(this).attr("flag");
		var score = $(this).attr("score");
		var contentid = $(this).attr("contentid");
		var typeid = $(this).attr("typeid");
		//把答案进行数据的存储
		//同时修改questionscard中试题的样式

		//修改颜色
		if ($("span.load-container").eq(curindex).find("a").hasClass("bGray")) {
			$("span.load-container").eq(curindex).find("a").addClass("bGreen").removeClass("bGray");
		}
		//$.getJSON("saveAnswer",{typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()});
	})

	//进行读音频的事件的委托注册
	$(document).on("click", ".read", function () {
		//将本试题中的所有的样式去掉
		mp.clear();
		clearTimeout(mp3_progress);
		mp.playtimes = 0;
		mp.questionindex = 0;
		//console.log(questions[curindex].tts);
		var questype = 5;
		//console.log(curindex);
		question_play(parseInt(questions[0].question_playtimes), questions[0].mp3list, $(this));

	});

	//提交
	$(document).on("click", "#submit", function () {
		//将本试题中的所有的样式去掉
		var anMaps = {};
		mp.clear();
		clearTimeout(mp3_progress);
		//console.log(useranswer);
	//	console.log(trueanswer);
		if (mobile.isEmpty(useranswer)) {
			return _eran();
		}
		// if (useranswer == trueanswer) {
		// 	//answerObj = new Answer(question_id, keys, "100", "2");
		// } else {
		// 	//answerObj = new Answer(question_id, keys, "99", "2");
		// }
		// //anMaps[std_id] = answerObj;
		// var params = $.toJSON({
		// 	'question_id': question_id,
		// 	'studentClassid': studentclass,
		// 	'questionScore': 1,
		// 	'from_platform': "exams",
		// 	'tqms': "",
		// 	'useranswer':useranswer
		// 	//'anMaps': anMaps
		// });
		// console.log(params);
		console.log(useranswer);
		$("#submit").hide();
		sendCommand("questionb", useranswer); //发送指令
		hideAlert();
		closeAnswerSheet();
		openScreenLockMode(); //关闭当前的webview

	});
});

function question_play(playtimes, quettsdata, obj) {
	clearTimeout(mp3_progress);
	var smallquetts = '';
	if (mp.playtimes < playtimes) {
		if (mp.questionindex < quettsdata.length) {
			$(obj).attr("src", "images/sy.gif");
			smallquetts = quettsdata[mp.questionindex];
			playurl = quettsdata[mp.questionindex].mp3;
			mp.play(playurl);
			$("#jplayer").bind($.jPlayer.event.ended, function (event) {
				$(obj).attr("src", "images/sy.png");
				if (mp.questionindex < quettsdata.length) {
					mp.questionindex = mp.questionindex + 1;
					clearTimeout(mp3_progress);
					mp.clear();
					mp3_progress = setTimeout(function () {

						question_play(playtimes, quettsdata, obj);
					}, parseInt(quettsdata[mp.questionindex - 1].stoptime) * 1000);
				}
			});
		} else {
			mp.playtimes = mp.playtimes + 1;
			mp.questionindex = 0;
			mp3_progress = setTimeout(function () {
				mp.clear();
				question_play(playtimes, quettsdata, obj);
			}, 3000);
		}
	} else {

		mp.clear();
		mp.playtimes = 0;
		mp.questionindex = 0;
		$(obj).attr("src", "images/sy.png");
		return;
		//clearTimeout(mp3_progress);
	}
}

//强制提交
function recvCommand(type){
	if(type == "endquestionb" || type == "endotherscore"){
		closeAnswerSheet();
		openScreenLockMode();
	}
}
function _eran(){
	uiShowToast("请作答完之后提交");
	return false;
}