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
    $.getJSON("getQuestionCard",{homeworkid:homeworkid,username:username,ran:Math.random()},function(data){
		$("#questioncard").tmpl(data).appendTo("#card");
	});
	$.getJSON("getHomeworkQuestions",{homeworkid:homeworkid,username:username,ran:Math.random()},function(data){
		//将数据直接缓存在本地
		if(!questions){
			storage.removeItem("homeworkid");
			storage.setItem("homeworkid",homeworkid);
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
	        	curindex=i;
	        	pageindex=i;
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
	        			//alert("fasdfasd");
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

	        	//设置当前是第几个

	        	var off=document.getElementsByClassName("load-container")[i].offsetTop;
	        	$("#card").scrollTop(off-$(window).height()+200);
	        }
		});
	})


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
		window.location.href="?homeworkid="+homeworkid+"&classId="+classid+"&studentId="+username+"&tms="+tms+"&hwid="+hwid+"&starttime="+starttime+"&batchid="+batchid+"&callbackURL=&type="+type+"&isOverdue="+isOverdue+"&index="+index;

	})


	//采用的是时间委托进事件的注册
	$(document).on("click","p.zyp",function(){
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		//将本试题中的所有的样式去掉
		$(this).parent().find("span.ball").css("background","#fff");
		$(this).find("span.ball").css("background","green");
		var type=$(this).attr("type");
		var id=$(this).attr("bid");
		var iserror=$(this).attr("iserror");
		var answer=$(this).attr("flag");
		var score=$(this).attr("score");
		var contentid=$(this).attr("contentid");
		var typeid=$(this).attr("typeid");
		//把答案进行数据的存储
		questions[curindex].useranswer=answer;
		storage.setItem("questions",encodeURI(JSON.stringify(questions)));
		//同时修改questionscard中试题的样式

		//修改颜色
		if($("span.load-container").eq(curindex).find("a").hasClass("bGray")){
			$("span.load-container").eq(curindex).find("a").addClass("bGreen").removeClass("bGray");
		}
		$.getJSON("saveAnswer",{typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()});
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
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		//将本试题中的所有的样式去掉
		try{
            if(isExitsFunction(UXinJSInterfaceSpeech.startRecordVoice)){
                try{
                    var path=UXinJSInterfaceSpeech.startRecordVoice();
                }catch(e){
                    mui.toast("对不起，有问题请联系客户服务部");
                }
            }else{
                mui.toast("请升级到最新版本");
            }
        }catch(e){
            mui.toast("请升级到最新版本");
        }
	})

	//进行读音频的事件的委托注册
	$(document).on("click",".myrecord",function(){
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		//将本试题中的所有的样式去掉
        //播放时间的MP3的地址
        var id=$(".con").eq(pageindex).attr("userreadid");
        var type=$(".con").eq(pageindex).attr("type");
        if(id==undefined||id==''||id=='null'||id==null){
        	mui.toast("请先进行录音");
        	return false;
        }
        //var location=$(this).attr("loc");
        console.log("start");
        $.ajax({
		  url: "../Public/playBack",
		  data: {id:id,type:type,ran:Math.random()},
		  dataType: "json",
		  async:false,
		  success:function(data){
		  	if(data.filename==""||data.filename==null||data.filename==undefined){
                mui.toast("请先进行录音");
            }else{
                var recordmp3=window.location.protocol+"//"+document.domain+recordlocation+data.filename;
                mp.play(recordmp3);
            }
		  }
		});
	})

	//进行单词拼写的事件的委托注册
	$(document).on("click",".wspell",function(){
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		if($(this).find("a").attr("inputword")!=undefined&&$(this).find("a").attr("inputword")!=""){
			return false;
		}
		//将本试题中的所有的样式去掉
		var type=$(this).attr("type");
		var id=$(this).attr("bid");
		var iserror=$(this).attr("iserror");
		var word=$(this).attr("word");
		var score=$(this).attr("score");
		var contentid=$(this).attr("contentid");
		var typeid=$(this).attr("typeid");
		var ul=$(this).parents("div.con").find("ul.encontent");
		var inputwords=$(ul).find(".active");
		//计算他是第几个
		var index=$(ul).find("li").index(inputwords);
		var ques=questions[curindex];
		if(index>=0&&index<($(ul).find("li").length)){
		    //将光标移动到下一个
		    inputwords.next().addClass("active");
		    inputwords.removeClass("active");
		    inputwords.html(word);
		    //存储答案

			$(this).find("a").removeClass("wgray").addClass("wgreen");
			$(this).find("a").attr("inputword",index);
			//修改缓存中的数据
			var iindex=$(this).parent().find("li").index($(this));
			questions[curindex].items[iindex].ischoose=1;
			questions[curindex].items[iindex].inputword=index;

		}
		//判断一下答案是够正确

		var words=$(this).parents("div.con").find("ul.encontent").find("li");
		var answers=[];
		$.each(words,function(k,v){
			var temp=$(v).html();
			if(temp=='<a></a>'){
				temp="";
			}
			questions[curindex].encontent[k]=temp;
			answers.push(temp);
		})
		var answer=answers.join(",");
		questions[curindex].useranswer=answer;
		console.log(questions[curindex]);
		storage.setItem("questions",encodeURI(JSON.stringify(questions)));
		if($("span.load-container").eq(curindex).find("a").hasClass("bGray")){
			$("span.load-container").eq(curindex).find("a").addClass("bGreen").removeClass("bGray");
		}

		$.ajax({
		  url: "saveAnswer",
		  data: {typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()},
		  dataType: "json",
		  async:true
		});
		//$.getJSON("saveAnswer",{typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()});
	})

	//交作业响应事件
	//hwid=d3074193cf8f44c5946acac5b4dca1cb&homeworkid=4900&classId=147001643914524565&studentId=41010110001121&tms=tms.youjiaotong.com&batchid=cd947229d2a542898d6998f7235e6d9c
	$("#submithomework").click(function(){
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		var num=$(".load-container.load8").find("a.bGray").length;
		var content="";
		if(num==0){
		  	content="您已经全部做完,确认是否提交?";
		}else{
			content='您还有'+num+"道试题没有完成，确认是否提交?";
		}
		layer.confirm(content, {
		  btn: ['确认','取消'] //按钮
		}, function(){

		   var url="../public/stupublish";
        	$.getJSON(url,{studentid:username,classid:classid,batchid:batchid,homeworkid:hwid,paper_id:homeworkid,tms:tms,ran:Math.random()});
        	try{UXinJSInterface.setOnlinWorkHaveFinished(batchid);}catch(e){console.log(e);}
        	window.location.href='finish?homeworkid='+homeworkid+'&batchid='+batchid+'&studentId='+username+'&classId='+classid+'&type=0&tms='+tms+'&hwid='+hwid+'&iserror=0&isOverdue='+isOverdue;
		}, function(){
		  // layer.msg('也可以这样', {
		  //   time: 20000, //20s后自动关闭
		  //   btn: ['明白了', '知道了']
		  // });
		});

	})
	//拼写取消的响应事件
	$(document).on("click",".wcal",function(){
		if(isOverdue=='true'){
			mui.toast("作业已经过期");
			return false;
		}
		//将本试题中的所有的样式去掉
		var type=$(this).attr("type");
		var id=$(this).attr("bid");
		var iserror=$(this).attr("iserror");
		var score=$(this).attr("score");
		var contentid=$(this).attr("contentid");
		var typeid=$(this).attr("typeid");
		var ul=$(this).parents("div.con").find("ul.encontent");
		var ques=questions[curindex];
		var li=$(ul).find(".active");
		if(li.length==0){
			li=$(ul).find("li:last");
		}
		//如果这个li中有a标签的话
		var isa=$(li).find("a").length;
		//如果这个已经回答啊
		var index=$(ul).find("li").index(li);
		var iindex=0;
		if(isa>0){
			$(ul).find("li").eq(index-1).html('<a></a>');
			$(ul).find("li.active").removeClass("active");
			$(ul).find("li").eq(index-1).addClass("active");
			var cur=$(this).parents("div.con").find(".wspell .wsgray[inputword='"+(index-1)+"']");
			$(cur).addClass("wgray").removeClass("wgreen").attr("inputword","");
			iindex=$(this).parent().find("li").index($(cur).parent());
		}else{
			$(ul).find("li.active").removeClass("active");
			$(ul).find("li").eq(index).addClass("active");
			$(ul).find("li").eq(index).html('<a></a>');
			var cur=$(this).parents("div.con").find(".wspell .wsgray[inputword='"+(index)+"']");
			$(cur).addClass("wgray").removeClass("wgreen").attr("inputword","");
			iindex=$(this).parent().find("li").index($(cur).parent());
		}
		console.log(questions[curindex].items);
		console.log(iindex);
		console.log(questions[curindex].items.iindex-1)
		//将active前移动
		if(index>0){
			questions[curindex].items[iindex].ischoose=0;
			questions[curindex].items[iindex].inputword="";
			questions[curindex].activeindex=questions[curindex].activeindex-1;
		}
		var words=$(this).parents("div.con").find("ul.encontent").find("li");
		var answers=[];
		$.each(words,function(k,v){
			var temp=$(v).text();
			if(temp=='<a></a>'){
				temp="";
			}
			questions[curindex].encontent[k]=temp;
			answers.push(temp);
		})
		var answer=answers.join(",");
		questions[curindex].useranswer=answer;
		console.log(questions[curindex]);
		storage.setItem("questions",encodeURI(JSON.stringify(questions)));
		console.log(questions[curindex]);
		$.ajax({
		  url: "saveAnswer",
		  data: {typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()},
		  dataType: "json",
		  async:true
		});
		//$.getJSON("saveAnswer",{typeid:typeid,score:score,contentid:contentid,homeworkid:homeworkid,studentid:username,classid:classid,answer:answer,id:id,iserror:iserror,type:type,ran:Math.random()});
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

function getVoicePath(path){
    //在jplayer这个标签上加上一个属性表示单词的内容
    var filename=path;
    //alert(filename);
    //alert(pageindex);
    var contentid=$(".con").eq(pageindex).attr("contentid");
    var tncontent=$(".con").eq(pageindex).attr("content");
    var datatype=$(".con").eq(pageindex).attr("type");
    var readid=$(".con").eq(pageindex).attr("readid");
    var url="../Public/getTestScore";
    UXinJSInterface.showAlert("正在评分,请稍后....");
    //mask.show();
    //alert("homeworkid="+homeworkid+"&studentid="+studentid+"&classid="+classid+"&wordreadid="+contentid+"&content="+tncontent+"&filename="+filename+"&textid="+readid+"&type="+type);
    //window.location.href=url+"?homeworkid="+homeworkid+"&studentid="+studentid+"&classid="+classid+"&wordreadid="+contentid+"&content="+tncontent+"&filename="+filename+"&textid="+readid+"&type="+type;
    $.post(url,
        {
            homeworkid:homeworkid,
            studentid:username,
            classid:classid,
            contentid:contentid,
            content:tncontent,
            filename:filename,
            readid:readid,
            type:datatype,
            ran:Math.random()
        },function(data){
            //显示分数
            UXinJSInterface.hideProgress();
            //显示星星的信息
            var content="";
            var startint=(data.result.data.score).toFixed(1);
            $(".con").eq(pageindex).attr("userreadid",data.id);
            //将数据存入stroage
            questions[pageindex].userreadid=data.id;
			storage.setItem("questions",encodeURI(JSON.stringify(questions)));
            $(".con").eq(pageindex).find(".score").find("font.fontZ").text(startint);
            //将数据写入文件中
            questions[curindex].score=startint;
			storage.setItem("questions",encodeURI(JSON.stringify(questions)));
			//同时修改questionscard中试题的样式

			//修改颜色
			if($("span.load-container").eq(pageindex).find("a").hasClass("bGray")){
				$("span.load-container").eq(pageindex).find("a").addClass("bGreen").removeClass("bGray");
			}

            if(startint>=0&&startint<=20){
                content='<i class="icon-shoucang"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i>';
            }else if(startint>20&&startint<=40){
                content='<i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i>';
            }else if(startint>40&&startint<=60){
                content='<i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i>';
            }else if(startint>60&&startint<=80){
            	content='<i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang-o"></i>';
            }else if(startint>80&&startint<=100){
            	content='<i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i>';
            }else{
                content='<i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i><i class="icon-shoucang-o"></i>';
            }
            $(".con").eq(pageindex).find(".xingFont").html(content);
    });
}
