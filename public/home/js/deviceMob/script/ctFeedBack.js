/**
 * 互动训练反馈
 */
function addTouch(){
			var maxHeight=$(window).height()-35;
			var mW=$(window).width()-30;
			$('#paper .bd p').css('max-width', mW + 'px');
			$('article img,table').each(function(k,v){
				thumbImg(mW,$(v));
			});
			 TouchSlide({ slideCell:"#paper",
				prevCell:'#prev',
				nextCell:'#next',
				pnLoop:false,
				startFun:function(){
					mp.clear();
					clearTimeout(timecode);
					mp.queindex = 0;

				},
				endFun:function(i,c){
					$(this.prevCell+','+this.nextCell).removeClass('no');
					if(i==0){
						$(this.prevCell).addClass('no');
					}
					if((c-i) == 1){
						$(this.nextCell).addClass('no');
					}
					mp.repeat = 1; //默认播放次数
    				mp.curpeat = 1;//当前播放到第几次
    				mp.clear();
    				clearTimeout(mp3_progress);
    				$('.sound_single').removeClass('active');
					$('.sound_single:eq('+i+')').unbind('click').click(function(){     //音频播放事件
				            clearTimeout(mp3_progress);
				            if($(this).hasClass('active')){
				                mp.pause();
				                $(this).removeClass('active');
				            }
				            else{
				                $(this).addClass('active');
				                question_init(i);
				            }
				    });
				}
			});
			$('article').height(maxHeight);
			hideLoading();
		}

function htmlNotEmpty(html) {
	if (typeof (html) == 'undefined') {
		return false;
	}
	html = String(html);
	var imgRegExp = /<img[^>]+>/im, // 图片正则
	htmlRegExp = /\\r|\\n|\\t|&nbsp;|<[^>]+>|\/\/|,/img;// 判断HTML标签、换行符和空格正则
	if (html.search(imgRegExp) > -1) {
		return true;
	}
	html = html.replace(htmlRegExp, '');
	if ($.trim(html).length > 0) {
		return true;
	}

	return false;
}
function hideClass(){
	$('.bjj').hide();
	$('.kcsz').hide();
}
var Baseurl = '';
var ListenConter = Baseurl + '/Device/';//听力训练控制器访问地址
var temphtmlstr = '';
var itemsdata = '';
var itemshtml = '';
var itemsclass = '';
var mp = '';
var timecode = '';
var questionInfo = [];//试题列表的数组
//去除HTML tag
function removeHTMLTagimg(str) {
   str=str.replace('/uploads',resource_path+'/uploads');
    return str;
}
//替换填空题题干答案标识为input框
function content_replace(content,type,stem_type,queid){
	content = content.replace('<p>','');
	content = content.replace('</p>','');
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
	quevalue.tcontent = content_replace(quevalue.tcontent,1);
    itemshtml = '<div class="options">';
    $(itemsdata).each(function(itemskey,itemsvalue){
    	if(quevalue.itemtype == '0'){	//选项是文字
    		itemshtml += '<span class="option">'+itemsvalue.flag+'.</span>'+itemsvalue.content+'<br>';
    	}
    	else{	//选项是图片
    		itemshtml += '<span>'+itemsvalue.flag+'</span>.<img src="'+resource_path+'/uploads/'+itemsvalue.content+'" width="120" height="90">&nbsp;';
    	}
    });
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
//判断题html
function panduanHtml(quevalue,stem_type){
	temphtmlstr = '';
    quevalue.tcontent = content_replace(quevalue.tcontent,1);
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
function getStudentInfo(classId)
{
	//alert(classname);
	$.getJSON(tmsurl+'/tms/interface/queryStudent.jsp?jsoncallback=?',{queryType:'answerToolByClass',schoolClassId:classId},function(result){
		//alert(result.rtnArray.length);
		StudentInfo = result.rtnArray;
		if(StudentInfo.length == 0){
			//artdialog('该班级下没有学生，请查看其他班级');
			
		}
		else{

			studentNum = StudentInfo.length;
			$('#classlist a').each(function(){
				if($(this).attr('aid') == classId){
					$('#classlist a').removeClass('on');
					$(this).addClass('on');
				}
			});
			
			getAnswerResult(examsid,classId,studentNum,from_platform);
		}
		//alert(StudentInfo.length);
	});
	
}
function getAnswerResult(examsid,classId,studentNum){
	
	$.getJSON(ListenConter+'getAnswerResult',{examsid:examsid,classId:classId,from_platform:from_platform},function(result){
		if(result.length == 0){
			//alert('该班级没有学生训练反馈信息，请选择其他班级查看');
		}
		else{
			loadQuestions(examsid,classId,result[0].id,studentNum,from_platform);
		}
	});
}

function loadQuestions(examsid,classId,history_id,studentNum,from_platform){
	var htmlcode = '';
	var answer = '';
	var duwenque = '';
	$.getJSON(ListenConter+'ListeShowTv',{examsid:examsid,sum_student:studentNum,studentClassid:classId,from_platform:from_platform},function(result){
		$(result).each(function(i,val){
			questionInfo = result;
			answer = '';
			$('#classinfo').html(classname+'，班级平均分：'+result[0].classaverage.toFixed(1)+'分')
			htmlcode += '<article>';
			htmlcode += '<div class="arcontent">';
			htmlcode += '<div class="lyl_ti" style="margin-top:125px;">';
			htmlcode +='<div class="sc">';
			htmlcode += '<span class="fright"><a class="font_red s20">'+(i+1)+'</a>/'+result.length+'</span>';
			if(val.stem_type == '2'){
                htmlcode += '<span class="fleft">短文题</span>';
            }
            else{
                if(val.typeid == '1'){
                htmlcode += '<span class="fleft">选择题</span>';
                }
                else if(val.typeid == '3'){
                     htmlcode += '<span class="fleft">判断题</span>';
                }
                else if(val.typeid == '2'){
                   htmlcode += '<span class="fleft">填空题</span>';
                }
                else if(val.typeid == '4'){
                    htmlcode += '<span class="fleft">排序题</span>';
                }
            }
			htmlcode +='</div>';
			
			htmlcode += '<h3>题目信息</h3>';
            if(from_platform == "word"){
                if(val.mp3type ==1){
                    htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single" style="display:none;"></a></h5>';
                }
                else{
                    htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single"></a></h5>';
                }
            }
            else{
                htmlcode += '<h5>'+val.papertitle+'<a href="javascript:;"  class="sound_single"></a></h5>';
            }
			
			if(val.stem_type == '2'){
                 htmlcode += '<h5>'+val.content+'</h5>';
            }
			if(val.stem_type == '1'){
				var accuracy = parseInt(val.accuracy,10)+'%';
				if(val.typeid == '1'){
					htmlcode += xuanzeHtml(val,1);
				}
				else if(val.typeid == '3'){
					htmlcode += panduanHtml(val,1);
					if(val.answer[0].answer == '0'){
					val.answer[0].answer = '×';
					}
					if(val.answer[0].answer == '1'){
						val.answer[0].answer = '√';
					}
				}
				else if(val.typeid == '2'){
					htmlcode += tiankongHtml(val,1);
				}
				else{
					htmlcode += paixuHtml(val,1);
				}
				 $(val.answer).each(function(answerkey,answerval){
				        answer += answerval.answer_num+'.'+answerval.answer+' ';
				    });
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
                     $(duval.answer).each(function(answerkey,answerval){
				        answer += answerval.answer_num+'.'+answerval.answer+' ';
				    });
                });
			}
			htmlcode += '<div class="lyl_da">';
			htmlcode += '<h3>答案解析</h3>';
			htmlcode += '<a class="red"><div class="c_i" >答案：'+answer+'</div></a>';
			htmlcode += '</div>';
			if(val.stem_type == '1'){
				if(val.typeid == '1' || val.typeid == '3'){
					htmlcode += '<div class="lyl_da mb50">';
					htmlcode += '<h3>习题反馈</h3>';
					htmlcode += '<a class="red fk">';
					htmlcode += '<div class="c_i" >正确率：'+accuracy+'</div>';
					itemsdata = val.items;
					itemshtml = '';
					$(itemsdata).each(function(itemskey,itemsvalue){ 
						if(itemsvalue.flag == '0'){
						itemsvalue.flag = '×';
						}
						if(itemsvalue.flag == '1'){
							itemsvalue.flag = '√';
						}
						itemshtml += itemsvalue.flag+'('+itemsvalue.reply_num+'人) ';			
				    });
				    itemshtml += '无效('+val.invalid_num+'人)';
					htmlcode += '<div class="c_i" >选项分析：'+itemshtml+'</div>';
					htmlcode += '</a>';
					htmlcode += '</div>';
				}
				
			}
			
			htmlcode += '</div>';
			htmlcode += '</article>';
		});
		$('.bd').html(htmlcode);
		addTouch();
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
	$('.sound_single:eq('+mp3index+')').addClass('active');
    mp.questionindex = 0;
    clearTimeout(mp3_progress);
    var quettsdata = '';
    var tips = questionInfo[mp3index]['tips'];    //是否播放叮咚 0-不播 1播放
    quettsdata = questionInfo[mp3index]['tts'];
    var repeat = questionInfo[mp3index]['question_playtimes']; //播放次数
    var mp3type = questionInfo[mp3index]['mp3type']; //mp3类型
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