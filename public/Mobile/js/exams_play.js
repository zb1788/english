var mp = '';
var mp3_name = '',number_name='',mp3_dir='',mp3_url='',number_url='',mp3_barr='',mp3_sarr='',astoptime=0,repeat=1,settimer='';
function changepaper(num){
	$('#papernum').html((num+1)+'/'+papernum);
}
function  BodyScroll(curpos) {
    //alert($(curpos).offset().top);
    var container = $("html,body");
    var pos_y = $(curpos).offset().top - 300;
    $("html,body").animate({scrollTop: pos_y}, 100); //1000是ms,也可以用slow代替
}
function autopage(page,issumbit){
	
			if(!issumbit){
				$('#iScroll-bd').parent().css('overflow','visible');
			}
			
            TouchSlide( { slideCell:"#iScroll",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			autoPage:true, //自动分页
			endFun:function(i){ //高度自适应
				var bd = document.getElementById("iScroll-bd");
				bd.parentNode.style.minHeight = window.screen.availHeight+"px";
				BodyScroll($('div[class=con]:eq('+i+')'));
				//进行图片的控制
				var obj=$(".questigan").find("img");
                //进行题干的图片的展示
                try{
                	var obj=$(".questigan").find("img");
					$(obj).each(function(key,value){
						if($(value).width()>(window.screen.availWidth-50)){
							$(this).attr("width",(window.screen.availWidth-50)+"px")
						}
					});	
                }catch(e){
                	
                }
				//document.getElementsByClassName("con")[i].getElementsByClassName("tiCon")[0].style.minHeight=window.screen.availHeight+"px";
               
				if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
				
                                mp.clear();
                                if(issumbit){
									$('#iScroll-bd').parent().css('overflow','hidden');
									mp.index = i;
									$('#submit').hide();
									//alert(mp.index);
                                    changepaper(i);
                                    BodyScroll($('a[type=paper]:eq('+i+')'));
                                   // var paperid =$('a[type=paper]:eq('+i+')').attr('paperid');
                                    //settimer = setTimeout(function(){listen_play(paperid);},1000);
                                }
                                else{
                                    //BodyScroll($('#show_score'));
									  
                                }
                                
			},
                        startFun:function(){
                            mp.clear();
                        },
                        defaultIndex:page			
		});
			$('#submit').click(function(){
            							//alert("fsadfasd");
									    exams_submit(examsdatas,$('.tiCon[name=parent]'));
									  });
                }
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
	oplay.stemindex = 0;
	oplay.queinitindex = 0;
	oplay.questionindex = 0;
	oplay.childstemindex = 0;
	oplay.childinitstemindex = 0;
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
    }
    oplay.clear = function() {
		
        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    return oplay;
}
 mp = new myplay();
var headermp3 = '';
var endmp3 = '';



//获取mp3文件路径
function getmp3url(mp3name){
	//mp3name = mp3name.substr(0,mp3name.length-1);
	var mp3url = '';
	var quespeed = 1;
	//if(examstts_type == 1){			//系统生成
		if(quespeed == 0){
			mp3name = mp3name+'s';
		}
		else if(quespeed == 2){
			mp3name = mp3name+'q';
		}
	//}
	mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
	return mp3url;
}
//试卷头播放
function examsplay(examsdata){
	if(examsdata[0]['exams_tts'] == '0'){

		stem_play(examsdata,examsdata[0]['stem']);
		
	}
	else{
		var header_playend_stoptim = examsdata[0]['stopsecond'];	//试卷头播放结束后的停顿时间
		$(examsdata[0]['exams_tts']).each(function(i,value){
			if(value.flag_content == 'eb'){
				var headermp3url = getmp3url(value.tts_mp3);
				mp.play(headermp3url);
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					mp3_progress = setTimeout(function(){
						stem_play(examsdata,examsdata[0]['stem']);
					},parseInt(header_playend_stoptim)*1000);	
				});
			}
		});

	}
}
//题干播放
function stem_play(examsdata,stemdata){
	mp.stemindex = 0;
	stemttsdata = '';
	quetiondata = '';
	var repeat = '';
	mp.clear();
	mp.curpeat = 1;
	mp.repeat =1 ;
	//alert(mp.stemindex+"||"+stemdata.length);
	if(mp.stemindex < stemdata.length){
		stemttsdata = stemdata[mp.stemindex]['stem_tts'];
		quetiondata = stemdata[mp.stemindex]['question'];
		repeat = stemdata[mp.stemindex]['question_playtimes'];
		$(stemttsdata).each(function(i,val){
				playurl = getmp3url(val.tts_mp3);
				//alert(playurl);
				//prompt('',playurl);
				mp.play(playurl);
				$("#jplayer").bind($.jPlayer.event.ended,function(event){
					mp.stemindex = 0;
					//alert(stemdata[mp.stemindex]['stem_type']);
					if(stemdata[mp.stemindex]['stem_type'] == '1'){	//独立大题
						mp.queinitindex = 0;
						mp3_progress = setTimeout(function(){
							question_init(examsdata,stemdata,quetiondata,repeat);
						},parseInt(val.tts_stoptime)*1000);				//tts_stoptime 每个听力材料设置的停顿时间
					}
					else{
						childstemdata = stemdata[mp.stemindex]['stem_children'];
						mp3_progress = setTimeout(function(){
							child_stem_init(examsdata,stemdata,childstemdata);
						},2000);						
						//子题干及小题播放
					}	
				});
			});
	}
	else{
		//播放试卷结束声音
	}
}
//小题播放初始化化
function question_init(examsdata,stemdata,quetiondata,repeat){
	quettsdata = '';
	var stoptime = stemdata[mp.stemindex]['stoptimes'];		//播放下一个大题时的停顿时间
	mp.questionindex = 0;
	if(mp.queinitindex < quetiondata.length){
		if(mp.curpeat >1){
			quettsdata = quetiondata[mp.queinitindex]['que_tts_noqn'];
		}
		else{
			quettsdata = quetiondata[mp.queinitindex]['que_tts'];
		}
		question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);
	}
	else{
		mp.clear();
		clearTimeout(mp3_progress);
	}
	
}
//小题播放
function question_play(examsdata,stemdata,quetiondata,quettsdata,repeat){
	var stoptime = quetiondata[mp.queinitindex]['stoptimes'];	//小题播放结束后播放下一个小题的停顿时间
	var smallquetts = '';
	if(mp.questionindex < quettsdata.length){
		smallquetts = quettsdata[mp.questionindex];
		playurl = getmp3url(smallquetts.tts_mp3);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.questionindex = mp.questionindex +1;
			mp3_progress = setTimeout(function(){
			question_play(examsdata,stemdata,quetiondata,quettsdata,repeat);	
			},parseInt(smallquetts.tts_stoptime)*1000);
		});
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
		mp3_progress = setTimeout(function(){
			question_init(examsdata,stemdata,quetiondata,repeat);
		},parseInt(stoptime)*1000);
		
	}
}


//组合大题的子题初始化
function child_stem_init(examsdata,stemdata,childstemdata){
	mp.childstemindex = 0;
	//alert(mp.childinitstemindex);
	if(mp.childinitstemindex < childstemdata.length){
		var repeat = childstemdata[mp.childinitstemindex]['question_playtimes'];
		if(mp.curpeat > 1){
			stemttsdata =  childstemdata[mp.childinitstemindex]['stem_child_tts_nost'];
		}
		else{
			stemttsdata =  childstemdata[mp.childinitstemindex]['stem_child_tts'];
		}
		child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);

	}
	else{
		//mp.stemindex = mp.stemindex +1;
		//stem_play(examsdata,stemdata);
	}	
}
//子题播放
function child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat){
	var stoptime = childstemdata[mp.childinitstemindex]['stoptimes'];	//播放下一个子题的停顿时间
	var smallstemtts = '';
	if(mp.childstemindex < stemttsdata.length){
		smallstemtts = stemttsdata[mp.childstemindex];
		playurl = getmp3url(smallstemtts.tts_mp3);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.childstemindex = mp.childstemindex +1;
			mp3_progress = setTimeout(function(){
			child_stem_play(examsdata,stemdata,childstemdata,stemttsdata,repeat);	
			},parseInt(smallstemtts.tts_stoptime)*1000);
		});
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
		//alert(mp.questionindex);
		mp3_progress = setTimeout(function(){
			child_stem_init(examsdata,stemdata,childstemdata);
		},parseInt(stoptime)*1000);
	}
}


//进行听力播放点击时间url表示ajax访问的地址,stemid表示答题的题干id,examsid表示考试的id
function listen(url,stemid,examsid){
	//题干的id
	$.getJSON(url,{stemid:stemid,examsid:examsid,ran:Math.random()},function(data){
		//alert(data[0].stem);
		stem_play(data[0].stem,data[0].stem);
	});
}


