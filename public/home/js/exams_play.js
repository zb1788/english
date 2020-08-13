var stem_isplay = false;
var child_stem_isplay = false;
var que_isplay = false;
var stoptimes = 1; //停顿时间
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
	oplay.stemindex = 0;
	oplay.queinitindex = 0;
	oplay.questionindex = 0;
	oplay.childinitstemindex = 0;
	oplay.childstemindex = 0;
	oplay.single_tts_initindex = 0;
	oplay.single_ttsindex = 0;
    oplay.url = "";
    oplay.repeat = 1; //默认播放次数
    oplay.curpeat = 1;//当前播放到第几次
    oplay.play = function(mp3) {
        oplay.clear();
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    oplay.clear = function() {
		$("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    oplay.initindex = function(){
    	$('.playBtn').removeClass('active');
		oplay.index = 0;
		oplay.stemindex = 0;
		oplay.queinitindex = 0;
		oplay.questionindex = 0;
		oplay.childinitstemindex = 0;
		oplay.childstemindex = 0;
		oplay.single_tts_initindex = 0;
		oplay.single_ttsindex = 0;
    }
    return oplay;
}
var headermp3 = '';
var endmp3 = '';
//获取mp3文件路径
function getmp3url(mp3name){
	//mp3name = mp3name.substr(0,mp3name.length-1);
	var mp3url = '';
	var quespeed = $('input[name=speed]:checked').val();
	if(examstts_type == 1){			//系统生成
		if(quespeed == 0){
			mp3name = mp3name+'s';
		}
		else if(quespeed == 2){
			mp3name = mp3name+'q';
		}
	}
	mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
	return mp3url;
}
//试卷开始音播放
function begin_examsplay(examsdata){

	clearTimeout(mp3_progress);
	if(examsdata[0]['exams_tts'] == '0'){

		stem_play(examsdata,examsdata[0]['stem']);
		
	}
	else{		
		if(!stem_isplay){
			$(examsdata[0]['exams_tts']).each(function(i,value){
				if(value.tts_type == 'eb'){
				
					var headermp3url = getmp3url(value.tts_mp3);
					mp.play(headermp3url);
					$("#jplayer").bind($.jPlayer.event.ended,function(event){
						mp3_progress = setTimeout(function(){
							stem_play(examsdata,examsdata[0]['stem']);
						},parseInt(value.tts_stoptime)*1000);	
					});
				}
			});
		}
		else{
			stem_play(examsdata,examsdata[0]['stem']);
		}
	}
}
//试卷结束音播放
function end_examsplay(examsdata){
	clearTimeout(mp3_progress);
	if(examsdata[0]['exams_tts'] == '0'){
		clearTimeout(mp3_progress);
		mp.clear();
		mp.initindex();
		$('.submit').show();
	}
	else{
		$(examsdata[0]['exams_tts']).each(function(i,value){
			if(value.tts_type == 'ed'){
				var endmp3url = getmp3url(value.tts_mp3);
				mp.play(endmp3url);
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					clearTimeout(mp3_progress);
					mp.clear();
					mp.initindex();
					$('.submit').show();
				});
			}
			else{
				clearTimeout(mp3_progress);
				mp.clear();
				mp.initindex();
				$('.submit').show();
			}
		});

	}
}
//题干播放
function stem_play(examsdata,stemdata){
	clearTimeout(mp3_progress);
	stemttsdata = '';
	quetiondata = '';
	var repeat = 1;
	mp.curpeat = 1;
	mp.repeat =1 ;
	if(mp.stemindex < stemdata.length){
		stemttsdata = stemdata[mp.stemindex]['stem_tts'];
		quetiondata = stemdata[mp.stemindex]['question'];
		stoptimes = stemdata[mp.stemindex]['stoptimes'];
		if(examsdata[0]['tts_type'] == '2'){	//自助上传
			repeat = 1;
		}
		else{
			repeat = stemdata[mp.stemindex]['question_playtimes'];
		}
		$(stemttsdata).each(function(i,val){
				playurl = getmp3url(val.tts_mp3);
				if(!stem_isplay){
					mp.play(playurl);
					stem_isplay = true;
					$("#jplayer").bind($.jPlayer.event.ended,function(event){
						if(stemdata[mp.stemindex]['stem_type'] == '1'){	//独立大题
							mp.queinitindex = 0;
							mp3_progress = setTimeout(function(){
								question_init(examsdata,stemdata,quetiondata,repeat);
							},parseInt(stoptimes)*1000);				//根据大题干设置的停顿时间播放下边的小题
						}
						else{
							mp.childinitstemindex = 0;
							childstemdata = stemdata[mp.stemindex]['stem_children'];
							mp3_progress = setTimeout(function(){
								child_stem_init(examsdata,stemdata,childstemdata); //根据大题干设置的停顿时间播放下边的组合部分
							},parseInt(stoptimes)*1000);						
							//子题干及小题播放
						}	
					});
				}
				else{
					if(stemdata[mp.stemindex]['stem_type'] == '1'){	//独立大题
							
							question_init(examsdata,stemdata,quetiondata,repeat);				//tts_stoptime 每个听力材料设置的停顿时间
						}
						else{
							childstemdata = stemdata[mp.stemindex]['stem_children'];
							child_stem_init(examsdata,stemdata,childstemdata);					
							//子题干及小题播放
						}	
				}
				
			});
	}
	else{
		//播放试卷结束声音
		mp3_progress = setTimeout(function(){
			end_examsplay(examsdata);
		},2000);
	}
}
//组合大题的子题初始化
function child_stem_init(examsdata,stemdata,childstemdata){
	clearTimeout(mp3_progress);
	mp.childstemindex = 0;
		//alert(mp.curpeat);
		var repeat = 1;
		if(examsdata[0]['tts_type'] == '2'){	//自助上传
			repeat = 1;
		}
		else{
			repeat = childstemdata[mp.childinitstemindex]['question_playtimes'];
		}
		if(mp.curpeat > 1){
			stemttsdata =  childstemdata[mp.childinitstemindex]['stem_child_tts_nost'];
			// mp.play('/public/home/js/di.mp3');
			// $("#jplayer").bind($.jPlayer.event.ended,function(event){
			// 	mp3_progress = setTimeout(function(){		
			// 		child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);
			// 	},2000);
			// });
			mp3_progress = setTimeout(function(){		
					child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);
				},2000);
		}
		else{
			stemttsdata =  childstemdata[mp.childinitstemindex]['stem_child_tts'];
			if(examsdata[0]['tts_type'] == '2'){	//自助上传
					child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);
			}
			else{
				mp.play('/public/home/js/dingdong.mp3');
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					mp3_progress = setTimeout(function(){
						child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);
					},1000);
				});
			}
		}
}
//子题播放
function child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat){
	clearTimeout(mp3_progress);
	stoptimes = childstemdata[mp.childinitstemindex]['stoptimes'];	//播放下一个子题的停顿时间
	var smallstemtts = '';
	if(mp.childstemindex < stemttsdata.length){
		smallstemtts = stemttsdata[mp.childstemindex];
		playurl = getmp3url(smallstemtts.tts_mp3);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.childstemindex = mp.childstemindex +1;
			if(mp.childstemindex < stemttsdata.length){
				mp3_progress = setTimeout(function(){
				child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);	
				},2000);
			}
			else{
				if (mp.repeat >= repeat){
					mp.repeat = 1;
					mp.childinitstemindex = mp.childinitstemindex +1;
					mp.curpeat = 1;
				}
				else{
					mp.repeat = mp.repeat +1;
					mp.curpeat = mp.curpeat +1;
				}
				if(mp.curpeat > 1){
					stoptimes = 2;
				}
				if(mp.childinitstemindex < childstemdata.length){
					//alert(parseInt(stoptimes));
					mp3_progress = setTimeout(function(){
						child_stem_init(examsdata,stemdata,childstemdata);
					},parseInt(stoptimes)*1000);
				}
				else{
					stem_isplay = false;
					mp.stemindex = mp.stemindex +1;
					mp3_progress = setTimeout(function(){
						stem_play(examsdata,stemdata);
					},parseInt(stoptimes)*1000);
				}
			}
		});
	}
}
//小题播放初始化化
function question_init(examsdata,stemdata,quetiondata,repeat){
	clearTimeout(mp3_progress);
	quettsdata = '';
	mp.questionindex = 0;
	var tips = quetiondata[mp.queinitindex]['tips'];	//是否播放叮咚 0-不播 1播放
	stoptimes = quetiondata[mp.queinitindex]['stoptimes'];		//播放下一个小题时的停顿时间
	if(mp.curpeat >1){
		quettsdata = quetiondata[mp.queinitindex]['que_tts_noqn'];
		// mp.play('/public/home/js/di.mp3');	
		// $("#jplayer").bind($.jPlayer.event.ended,function(event){
		// 	mp3_progress = setTimeout(function(){
		// 		question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
		// 	},2000);
		// });
		mp3_progress = setTimeout(function(){
				question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
			},2000);
	}
	else{
		if(quetiondata.length == 1){
			quettsdata = quetiondata[mp.queinitindex]['que_tts_noqn'];
		}
		else{
			quettsdata = quetiondata[mp.queinitindex]['que_tts'];
		}
		if(examsdata[0]['tts_type'] == '2'){
			question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
		}
		else{
			if(tips == 1){
				mp.play('/public/home/js/dingdong.mp3');
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					setTimeout(function(){
						question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
					},1000);
				});
			}
			else{
				question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
			}
		}	
	}
}
//小题播放
function question_play(examsdata,stemdata,quetiondata,quettsdata,repeat){
	clearTimeout(mp3_progress);
	stoptimes = quetiondata[mp.queinitindex]['stoptimes'];	//小题播放结束后播放下一个小题的停顿时间
	var smallquetts = '';
	if(mp.questionindex < quettsdata.length){
		smallquetts = quettsdata[mp.questionindex];
		playurl = getmp3url(smallquetts.tts_mp3);
		//alert(playurl);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.questionindex = mp.questionindex +1;
			if(mp.questionindex < quettsdata.length){
				mp3_progress = setTimeout(function(){
				question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);	
				},2000);
			}
			else{
					if (mp.repeat >= repeat){
						mp.repeat = 1;
						mp.queinitindex = mp.queinitindex +1;
						mp.curpeat = 1;
					}
					else{
						mp.repeat = mp.repeat +1;
						mp.curpeat = mp.curpeat +1;
					}
					//alert(mp.questionindex);
					if(mp.curpeat > 1){
						stoptimes = 2;
					}
					if(mp.queinitindex < quetiondata.length){
						mp3_progress = setTimeout(function(){
							question_init(examsdata,stemdata,quetiondata,repeat);
						},parseInt(stoptimes)*1000);
					}
					else{
						stem_isplay = false;
						mp.stemindex = mp.stemindex +1;
						mp3_progress = setTimeout(function(){
							stem_play(examsdata,stemdata);
						},parseInt(stoptimes)*1000);
					}
				}
		});
	}
}

//单独听力材料初始化
function single_tts_init(examsdata,stem_type,stemkey,childstemkey,obj,quekey){
	//alert(quekey);
	clearTimeout(mp3_progress);
	mp.single_tts_initindex = 0;
	mp.single_ttsindex = 0;
	var single_tts_data = '';
	single_tts_data = examsdata[0]['stem'];
	if(stem_type == 1){		//独立大题	
		single_tts_data = single_tts_data[stemkey]['question'];
		single_tts_que_init(single_tts_data,obj,quekey);
	}
	else{
		single_tts_data = single_tts_data[stemkey]['stem_children'];
		single_tts_data = single_tts_data[childstemkey]['stem_child_tts_nost'];
		single_tts_stem_play(single_tts_data,obj);
	}
}

function single_tts_que_init(single_tts_data,obj,quekey){
	//alert($(obj).attr('stem_type'));
	mp.single_ttsindex = 0;
	if(quekey < single_tts_data.length){
		var ttsdata = single_tts_data[quekey]['que_tts'];
		single_tts_que_play(single_tts_data,ttsdata,obj,quekey);
	}
	else{
		mp.clear();
		clearTimeout(mp3_progress);
		$('.txtList p').removeClass('addcolor');
		$(obj).removeClass('active');
	}
}
function single_tts_que_play(single_tts_data,ttsdata,obj,quekey){
	clearTimeout(mp3_progress);
	var smallquetts = '';
	if(mp.single_ttsindex < ttsdata.length){
		smallquetts = ttsdata[mp.single_ttsindex];
		playurl = getmp3url(smallquetts.tts_mp3);
		$('.txtList p').removeClass('addcolor');
		$('#quetts'+smallquetts.id).addClass('addcolor');
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.single_ttsindex = mp.single_ttsindex +1;
			mp3_progress = setTimeout(function(){
				//alert($(obj).attr('stem_type'));
			single_tts_que_play(single_tts_data,ttsdata,obj,quekey);	
			},parseInt(smallquetts.tts_stoptime)*1000);
		});
	}
	else{
		mp.clear();
		clearTimeout(mp3_progress);
		$('.txtList p').removeClass('addcolor');
		$(obj).removeClass('active');
	}
}
function single_tts_stem_play(stemttsdata,obj){
	//alert($(obj).attr('stemkey'));
	var smallstemtts = '';
	if(mp.single_ttsindex < stemttsdata.length){
		smallstemtts = stemttsdata[mp.single_ttsindex];
		playurl = getmp3url(smallstemtts.tts_mp3);
		$('.txtList p').removeClass('addcolor');
		$('#childtt'+smallstemtts.id).addClass('addcolor');
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.single_ttsindex = mp.single_ttsindex +1;
			if(mp.single_ttsindex >= stemttsdata.length){
				mp.clear();
				clearTimeout(mp3_progress);
				$('.txtList p').removeClass('addcolor');
				$(obj).removeClass('active');
			}
			else{
				mp3_progress = setTimeout(function(){
				single_tts_stem_play(stemttsdata,obj);	
				},parseInt(smallstemtts.tts_stoptime)*1000);
			}		
		});
	}
}