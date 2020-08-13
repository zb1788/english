var Baseurl = '';
var ListenConter = Baseurl + '/Device/';//听力训练控制器访问地址
var temphtmlstr = '';
var itemsdata = '';
var itemshtml = '';
var itemsclass = '';
var mp = '';
var timecode = '';
var questionInfo = [];//试题列表的数组
//替换填空题题干答案标识为input框
//去除HTML tag
function removeHTMLTagimg(str) {
    str=str.replace('/uploads',resource_path+'/uploads');
    return str;
}
function content_replace(content,type,stem_type,queid){
    //alert(content);
	var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
	if(type == '1'){
		content = content.replace(patt,'_____');	//选择题替换成下划线
	}
	else{
		content = content.replace(patt,'<input stem_type="'+stem_type+'" name="'+queid+'" type="text">');	//填空题替换成输入框
	}
	
	return content;
}
//选择题html
function xuanzeHtml(quevalue,stem_type){
	temphtmlstr = '';
    itemsdata = quevalue.items;
    if(quevalue.tcontent != ''){
        quevalue.tcontent = removeHTMLTagimg(content_replace(quevalue.tcontent,1));
    }
    
    itemshtml = '<div class="options">';
    $(itemsdata).each(function(itemskey,itemsvalue){
        if(quevalue.itemtype == '0'){   //选项是文字
            itemshtml += '<span class="option">'+itemsvalue.flag+'.</span>'+itemsvalue.content+'<br>';
        }
        else{   //选项是图片
            itemshtml += '<span>'+itemsvalue.flag+'</span>.<img src="'+resource_path+'/uploads/'+itemsvalue.content+'" width="120" height="90">&nbsp;';
        }
    });
        itemshtml += '</div>';
        temphtmlstr += '<div>';
        
        temphtmlstr += '</div>';
        temphtmlstr = '<a class="q">';
        temphtmlstr += '<div>';
       if(quevalue.tcontent !=''){
        temphtmlstr += quevalue.question_num+'. '+quevalue.tcontent;
       }
        
        temphtmlstr += itemshtml;
        temphtmlstr += '</div>';
        temphtmlstr += '</a>';
    return temphtmlstr;
}
//判断题html
function panduanHtml(quevalue,stem_type){
	temphtmlstr = '';
    quevalue.tcontent = removeHTMLTagimg(content_replace(quevalue.tcontent,1));
    itemshtml = '<div class="options">';
    itemshtml += '<span class="option">√</span>&nbsp;<span class="option">×</span>';
    itemshtml += '</div>';
    temphtmlstr += '<div>';      
    temphtmlstr += '</div>';
    temphtmlstr = '<a class="q">';
    temphtmlstr += '<div>';    
    temphtmlstr += quevalue.tcontent;
    temphtmlstr += itemshtml;
    temphtmlstr += '</div>';
    temphtmlstr += '</a>';
    return temphtmlstr;
}
//填空题html
function tiankongHtml(quevalue,stem_type){
    temphtmlstr = '';
    quevalue.tcontent = content_replace(quevalue.tcontent,1);
    temphtmlstr += '<div>';      
    temphtmlstr += '</div>';
    temphtmlstr = '<a class="q">';
    temphtmlstr += '<div>';    
    temphtmlstr += quevalue.tcontent;
    temphtmlstr += '</div>';
    temphtmlstr += '</a>';
    return temphtmlstr;
}
//排序题html
function paixuHtml(quevalue,stem_type){
    temphtmlstr = '';
    itemsdata = quevalue.items;
    var answer = quevalue.answer;
    if(quevalue.tcontent != ''){
        quevalue.tcontent = removeHTMLTagimg(content_replace(quevalue.tcontent,1));
    }
    
    itemshtml = '<div class="options">';
    $(itemsdata).each(function(itemskey,itemsvalue){
        if(quevalue.itemtype == '0'){   //选项是文字
            itemshtml += '<span class="option">'+itemsvalue.flag+'.</span>'+itemsvalue.content+'<br>';
        }
        else{   //选项是图片
            itemshtml += '<span>'+itemsvalue.flag+'</span>.<img src="'+resource_path+'/uploads/'+itemsvalue.content+'" width="120" height="90">&nbsp;';
        }
    });
    itemshtml += '<br>';
     $(answer).each(function(answerkey,answerval){
        itemshtml += '<span>'+answerval.answer_num+'._____</span>&nbsp;&nbsp;';
    });
        itemshtml += '</div>';
        temphtmlstr += '<div>';
        
        temphtmlstr += '</div>';
        temphtmlstr = '<a class="q">';
        temphtmlstr += '<div>';
       if(quevalue.tcontent !=''){
        temphtmlstr += quevalue.question_num+'. '+quevalue.tcontent;
       }
        
        temphtmlstr += itemshtml;

        temphtmlstr += '</div>';
        temphtmlstr += '</a>';
    return temphtmlstr;
}
function loadQuestions(callback){
	var htmlcode = '';
    var answerflag = '';
    var duwenque = '';
	$.getJSON(ListenConter+'ListeShowTv',{examsid:examsid,from_platform:from_platform},function(result){
        answerflag = '';
        questionInfo = result;
		$(result).each(function(i,val){
			htmlcode += '<article>';
			htmlcode += '<div class="arcontent">';
			htmlcode += '<div class="lyl_ti">';
			htmlcode += '<span class="fright"><a class="font_red s20">'+(i+1)+'</a>/'+result.length+'</span>';
            if(val.stem_type == '2'){
                htmlcode += '<h3>短文题</h3>';
            }
            else{
                if(val.typeid == '1'){
                htmlcode += '<h3>单项选择题</h3>';
                }
                else if(val.typeid == '3'){
                    htmlcode += '<h3>判断题</h3>';
                }
                else if(val.typeid == '2'){
                    htmlcode += '<h3>填空题</h3>';
                }
                else if(val.typeid == '4'){
                    htmlcode += '<h3>排序题</h3>';
                }
            }
            
			//htmlcode += '<h3>'+val.papertitle+'<a href="javascript:;" mp3="'+val.mp3+'" onclick="mp3Play(\''+val.mp3+'\')" class="sound_single"></a></h3>';
			htmlcode += '</div>';
            if(from_platform == "word"){
                if(val.mp3type ==1){
                 htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single" style="display:none"></a></h5>';
                }
                else{
                    htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single"></a></h5>';
                }
            }
            
            else{
                htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single"></a></h5>';
            }
            
            if(val.stem_type == '2'){
                 htmlcode += '<h5>'+removeHTMLTagimg(val.content)+'</h5>';
            }
			htmlcode += '<div class="lyl_da">';
            if(val.stem_type == '1'){
                if(val.typeid == '1'){
                htmlcode += xuanzeHtml(val,1);
                }
                else if(val.typeid == '3'){
                    htmlcode += panduanHtml(val,1);
                }
                else if(val.typeid == '2'){
                    htmlcode += tiankongHtml(val,1);
                }
                else{
                    htmlcode += paixuHtml(val,1);
                }
            }
			else{
                duwenque = val.question;
                
                $(duwenque).each(function(dukey,duval){
                    if(duval.typeid == '1'){
                    htmlcode += xuanzeHtml(duval,1);
                    }
                    else if(duval.typeid == '3'){
                        htmlcode += panduanHtml(duval,1);
                    }
                    else if(duval.typeid == '2'){
                        htmlcode += tiankongHtml(duval,1);
                    }
                    else {
                        htmlcode += paixuHtml(duval,1);
                    }
                });
            }
			htmlcode += '</div>';
			htmlcode += '<div class="fx_xuan">';
            if(val.stem_type == '1'){
               itemsdata = val.items;
                itemshtml = '';
                $(itemsdata).each(function(itemskey,itemsvalue){ 
                    if(itemsvalue.flag == '0'){
                        answerflag = '×';
                    }
                    else if(itemsvalue.flag == '1'){
                        answerflag = '√';
                    }
                    else{
                        answerflag=itemsvalue.flag;
                    }
                    itemshtml += '<span><a>'+answerflag+'</a></span>';          
                }); 
            }
			else{
                itemshtml = '';
            }
		    htmlcode += itemshtml;
			htmlcode += '</div>';
			htmlcode += '</div>';
			htmlcode += '</article>';
		});
		$('.bd').html(htmlcode);
	
		/*所有的p标签还有table标签都设置最大宽度、否则在手机屏幕上会错乱*/
		var mW=$(window).width()-30;
		$('#qContent .bd p').css('max-width', mW + 'px');
		resieImgAndTable($('article img'),$('article table'),mW);
		if ($.isFunction(callback)) {
			callback();
		}
	});			
}
var mp3_progress = '';
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
        //alert(mp3);
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
//获取mp3文件路径
function getmp3url(mp3name,mp3type){
    //mp3name = mp3name.substr(0,mp3name.length-1);
    var mp3url = '';
   if(mp3type == 4){
        mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
    }
    else if(mp3type == 2){
        mp3url = text_mp3_url+mp3name.substr(0,2)+'/'+mp3name;
    }
    else{
        mp3url = word_mp3_url+mp3name;
    }
    return mp3url;
}
//小题播放初始化化
function question_init(mp3index){
   // console.log(mp3index);
    $('.sound_single:eq('+mp3index+')').addClass('active');
    mp.questionindex = 0;
    clearTimeout(mp3_progress);
    var quettsdata = '';
    var tips = questionInfo[mp3index]['tips'];    //是否播放叮咚 0-不播 1播放
    quettsdata = questionInfo[mp3index]['tts'];
    var repeat = questionInfo[mp3index]['question_playtimes']; //播放次数
    var mp3type = questionInfo[mp3index]['mp3type']; //播放次数
    if(mp.curpeat >1){ 
        mp.play('/public/home/js/di.mp3');  
        $("#jplayer").bind($.jPlayer.event.ended,function(event){
            mp3_progress = setTimeout(function(){
                question_play(quettsdata,repeat,mp3index,mp3type);
            },2000);
        });
    }
    else{
        if(tips == 1){
            mp.play('/public/home/js/dingdong.mp3');
            $("#jplayer").bind($.jPlayer.event.ended,function(event){
                setTimeout(function(){
                    question_play(quettsdata,repeat,mp3index,mp3type);
                },1000);
            });
        }
        else{
            question_play(quettsdata,repeat,mp3index,mp3type);
        }
          
    }
}
//小题播放
function question_play(quettsdata,repeat,mp3index,mp3type){
    clearTimeout(mp3_progress);
    var smallquetts = '';
    if(mp.questionindex < quettsdata.length){
        smallquetts = quettsdata[mp.questionindex];
        var playurl = getmp3url(smallquetts.tts_mp3,mp3type);
        mp.play(playurl);
        $("#jplayer").bind($.jPlayer.event.ended,function(event){
            mp.questionindex = mp.questionindex +1;
            if(mp.questionindex < quettsdata.length){
                mp3_progress = setTimeout(function(){
                question_play(quettsdata,repeat,mp3index,mp3type);    
                },parseInt(smallquetts.tts_stoptime)*1000);
            }
            else{
                    if (mp.repeat >= repeat){
                        mp.repeat = 1;
                        mp.curpeat = 1;
                        mp.clear();
                        clearTimeout(mp3_progress);
                        $('.sound_single').removeClass('active');
                    }
                    else{
                        mp.repeat = mp.repeat +1;
                        mp.curpeat = mp.curpeat +1;
                        mp3_progress = setTimeout(function(){
                            question_init(mp3index);
                        },2*1000);
                    }
                }
        });
    }
}