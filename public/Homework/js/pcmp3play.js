var  wordmp3url = '', wordpicurl = '', textmp3url = '', examsmp3url='';
var mp = '',mp3_progress='',examsdata='',stemdata='',stem_isplay=false;
var stemttsdata='',quetiondata='',stoptimes=0;
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
    	//alert(mp3);
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
//获取mp3文件路径
function getmp3url(mp3name){
	//mp3name = mp3name.substr(0,mp3name.length-1);
	var mp3url = '';
	mp3url = examsmp3url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
	return mp3url;
}
function mp3play(stemindex,curobj){
	mp.initindex();
	clearTimeout(mp3_progress);
	mp.clear();
	$('.sound_single').removeClass('active');
	$(curobj).addClass('active');
	stemdata = examsdata[0]['stem'];
	//console.log(stemdata);
	stem_play(stemdata,stemindex,curobj);
}
//题干播放
function stem_play(stemdata,stemindex,curobj){
	mp.stemindex = stemindex;
	clearTimeout(mp3_progress);
	stemttsdata = '';
	quetiondata = '';
	var repeat = 1;
	mp.curpeat = 1;
	mp.repeat =1 ;
		stemttsdata = stemdata[mp.stemindex]['stem_tts'];
		quetiondata = stemdata[mp.stemindex]['question'];
		stoptimes = stemdata[mp.stemindex]['stoptimes'];
		stoptimes = 2;
		if(examsdata[0]['tts_type'] == '2'){	//自助上传
			repeat = 1;
		}
		else{
			repeat = stemdata[mp.stemindex]['question_playtimes'];
		}
		$(stemttsdata).each(function(i,val){
				playurl = getmp3url(val.tts_mp3);
				//alert(stem_isplay);
				mp.play(playurl);
					stem_isplay = true;
					$("#jplayer").bind($.jPlayer.event.ended,function(event){
						if(stemdata[mp.stemindex]['stem_type'] == '1'){	//独立大题
							mp.queinitindex = 0;
							mp3_progress = setTimeout(function(){
								question_init(stemdata,quetiondata,repeat);
							},parseInt(stoptimes)*1000);				//根据大题干设置的停顿时间播放下边的小题
						}
						else{
							mp.childinitstemindex = 0;
							childstemdata = stemdata[mp.stemindex]['stem_children'];
							mp3_progress = setTimeout(function(){
								child_stem_init(stemdata,childstemdata); //根据大题干设置的停顿时间播放下边的组合部分
							},parseInt(stoptimes)*1000);						
							//子题干及小题播放
						}	
					});
				
			});
}
//组合大题的子题初始化
function child_stem_init(stemdata,childstemdata){
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
			// 		child_stem_play(stemdata,childstemdata,stemttsdata,repeat);
			// 	},2000);
			// });
			mp3_progress = setTimeout(function(){		
					child_stem_play(stemdata,childstemdata,stemttsdata,repeat);
				},2000);
		}
		else{
			stemttsdata =  childstemdata[mp.childinitstemindex]['stem_child_tts'];
			if(examsdata[0]['tts_type'] == '2'){	//自助上传
					child_stem_play(stemdata,childstemdata,stemttsdata,repeat);
			}
			else{
				mp.play('/public/home/js/dingdong.mp3');
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					mp3_progress = setTimeout(function(){
						child_stem_play(stemdata,childstemdata,stemttsdata,repeat);
					},1000);
				});
			}
		}
}
//子题播放
function child_stem_play(stemdata,childstemdata,stemttsdata,repeat){
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
				child_stem_play(stemdata,childstemdata,stemttsdata,repeat);	
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
						child_stem_init(stemdata,childstemdata);
					},parseInt(stoptimes)*1000);
				}
				else{
					$('.sound_single').removeClass('active');
					mp.initindex();
				}
			}
		});
	}
}
//小题播放初始化化
function question_init(stemdata,quetiondata,repeat){
	//console.log(repeat);
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
		// 		question_play(stemdata,quetiondata,quettsdata,repeat);
		// 	},2000);
		// });
		mp3_progress = setTimeout(function(){
				question_play(stemdata,quetiondata,quettsdata,repeat);
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
			question_play(stemdata,quetiondata,quettsdata,repeat);
		}
		else{
			if(tips == 1){
				mp.play('/public/home/js/dingdong.mp3');
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					setTimeout(function(){
						question_play(stemdata,quetiondata,quettsdata,repeat);
					},1000);
				});
			}
			else{
				question_play(stemdata,quetiondata,quettsdata,repeat);
			}
		}	
	}
}
//小题播放
function question_play(stemdata,quetiondata,quettsdata,repeat){
	clearTimeout(mp3_progress);
	stoptimes = quetiondata[mp.queinitindex]['stoptimes'];	//小题播放结束后播放下一个小题的停顿时间
	var smallquetts = '';
	if(mp.questionindex < quettsdata.length){
		smallquetts = quettsdata[mp.questionindex];
		//alert(mp.questionindex+"=="+smallquetts.tts_mp3);
		playurl = getmp3url(smallquetts.tts_mp3);
		//alert(playurl);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.questionindex = mp.questionindex +1;
			if(mp.questionindex < quettsdata.length){
				mp3_progress = setTimeout(function(){
				question_play(stemdata,quetiondata,quettsdata,repeat);	
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
							question_init(stemdata,quetiondata,repeat);
						},parseInt(stoptimes)*1000);
					}
					else{
						$('.sound_single').removeClass('active');
						mp.initindex();
					}
				}
		});
	}
}

$(function(){
	 $("#jplayer").jPlayer({
            swfPath: '/public/Homework/js',
            wmode: "window",
            supplied: "mp3",
            preload: "none",
            volume: "1"
 	});
 	mp = new myplay();
});

