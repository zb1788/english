function myplay() {
    var oplay = new Object();
    oplay.index = 0;
    oplay.queindex = 0;
    oplay.que2index = 0;
    oplay.que3index = 0;
    oplay.url = "";
    oplay.repeat = 1;
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
function artdialog(content){

	art.dialog({
				title:'提示',
				width:300,
				content:content,
				lock:true,
				opacity: 0.2,
				okValue:'确定',
				
				ok: function () {
				$("#"+classId).siblings().removeClass('cur_2');
				$("#"+classId).addClass('cur_2');
				return true;
			},
			cancelValue: '关闭',
			cancel: function () {
				$("#"+classId).siblings().removeClass('cur_2');
				$("#"+classId).addClass('cur_2');
				return true;
				}
			});
}
function getStudentInfo(classId)
{
	//alert(tmsurl);
	if(classId == ''){
		alert('该训练下没有班级答题，请查看其他训练');
	}
	else{
		$.getJSON(tmsurl+'/tms/interface/queryStudent.jsp?jsoncallback=?',{queryType:'answerToolByClass',schoolClassId:classId},function(result){
		//alert(result.rtnArray.length);
		StudentInfo = result.rtnArray;
		if(StudentInfo.length == 0){
			alert('该班级下没有学生，请查看其他班级');
		}
		else{
			$("#"+classId).attr("class","cur_2");
			studentNum = StudentInfo.length;
			getAnswerResult(examsid,classId,studentNum,from_platform);
		}
		//alert(StudentInfo.length);
	});
	}
	
}
function getAnswerResult(examsid,classId,studentNum,from_platform){
	
	$.getJSON(ListenConter+'getAnswerResult',{examsid:examsid,classId:classId,from_platform:from_platform},function(result){
		if(result.length == 0){
			alert('该班级没有学生训练反馈信息，请选择其他班级查看');
		}
		else{
			showDetailAnswer(examsid,classId,result[0].id,studentNum,from_platform);
		}
	});
}
function showDetailAnswer(examsid,classId,history_id,studentNum,from_platform){
	var sortid = '0';
	var papertitle = '';
	var qtitle = '';
	var mp3 = '';
	var questionId = '';
	var accuracy = '';
	var quetts = '';
	var flag_content = '';
	var ttshtml = '';
	$.getJSON(ListenConter+'ListeShowTv',{examsid:examsid,sum_student:studentNum,studentClassid:classId,from_platform:from_platform},function(result){

		questionInfo = result;
		//alert(questionInfo.length);
		$('.fank').html('试题数量：<span style="color:red;">'+questionInfo.length+'</span>  &nbsp;&nbsp; 班级平均得分：'+questionInfo[0].classaverage.toFixed(1)+'  ');
		showstr = '<tbody><tr class="tit2"><td width="16%">序号</td><td width="50%">题目</td><td width="16%"></td><td width="16%">正确率</td><tr>';
		for(var i = 0; i < questionInfo.length; i++){
			flag_content = '';
			ttshtml = '';
			if(i < 9){
				sortid = '0'+(i+1);
			}
			else{
				sortid = i+1;
			}
			quetts = questionInfo[i].tts;
			$(quetts).each(function(k,ttsval){
				if(k == 0){
				if(ttsval.flag_content){
					flag_content = ttsval.flag_content+':&nbsp;'
				}
				ttshtml += flag_content+ttsval.tts_content.substring(0,50)+'&nbsp';
				}
			});
			accuracy = parseInt(questionInfo[i].accuracy,10)+'%';
			if(from_platform == "word"){
				if(questionInfo[i].mp3type == 1){
					showstr += '<tr><td>'+sortid+'</td><td><a class="none" href="javascript:dantiShow('+i+');">'+ttshtml+'</a></td><td><a href="javascript:;"; class="sound_single" mp3="'+questionInfo[i].mp3+'" playindex="'+i+'" mp3type="'+questionInfo[i].mp3type+'" style="display:none;"></a></td><td>'+accuracy+'</td></tr>';
				}
				else{
					showstr += '<tr><td>'+sortid+'</td><td><a class="none" href="javascript:dantiShow('+i+');">'+ttshtml+'</a></td><td><a href="javascript:;"; class="sound_single" mp3="'+questionInfo[i].mp3+'" playindex="'+i+'" mp3type="'+questionInfo[i].mp3type+'"></a></td><td>'+accuracy+'</td></tr>';
				}
			}
			else{
				showstr += '<tr><td>'+sortid+'</td><td><a class="none" href="javascript:dantiShow('+i+');">'+ttshtml+'</a></td><td><a href="javascript:;"; class="sound_single" mp3="'+questionInfo[i].mp3+'" playindex="'+i+'" mp3type="'+questionInfo[i].mp3type+'"></a></td><td>'+accuracy+'</td></tr>';
			}
			
		}
		showstr += '</tbody>';
		$('.table_2').html(showstr);
		$('.sound_single').unbind('click').click(function(){
            clearTimeout(timecode);
            if($(this).hasClass('active')){
                mp.pause();
                $(this).removeClass('active');
            }
            else{
                $('.sound_single').removeClass('active');
                $(this).addClass('active');
                qplay($(this).attr('playindex'),$(this).attr('mp3'),$(this).attr('mp3type'));
            }
        });
	});
}
function qplay(num,mp3str,mp3type){
	//alert(questionInfo.length);
	mp.queindex = 0;
	mp3Play(num,mp3str,mp3type);
}
var timecode='';
function mp3Play(num,mp3str,mp3type) {
	//alert(mp3str);
    mp.clear();
    var mp3url = '';
    var mp3info = '';
    var mp3dir = '';
    var mp3name = '';
    var speed = 1;
    var carindex = mp.queindex;
	//alert(carindex);
    var mp3 = mp3str;
    mp3 = mp3.substr(0, mp3.length - 1);
    mp3info = mp3.split('|');
    mp3name = mp3info[carindex];
    mp3dir = mp3name.substr(0, 2);
    if(mp3type == 4){
    	mp3url = exams_mp3_url + mp3dir + '/' + mp3name + '.mp3';
    }
    else if(mp3type == 2){
    	mp3url = text_mp3_url + mp3dir + '/' + mp3name;
    }
    else{
    	 mp3url = word_mp3_url+mp3name;
    }
	$('.sound_single').removeClass('active');
    $('.sound_single:eq('+num+')').addClass('active');
	if(num == 100){
		$('.sound_single_danti').addClass('active');
	}
    mp.play(mp3url);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
        if ((carindex + 1) < mp3info.length)
        {
            mp.queindex = mp.queindex + 1;
            timecode = setTimeout(function() {
                mp3Play(num,mp3str,mp3type);
            }, 1000);
        }
        else {
            mp.clear();
            $('.sound_single').removeClass('active');
			if(num == 100){
				$('.sound_single_danti').removeClass('active');
			}
            mp.queindex = 0;
        }
    });
}
//去除HTML tag
function removeHTMLTagimg(str) {
	if(str != ''){
		str=str.replace('/uploads',resource_path+'/uploads');
	}
   
    return str;
}
//替换填空题题干答案标识为input框
function content_replace(content){
    var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
     content = content.replace(patt,'_____');   
    return content;
}
/*试题弹出层js*/
function dantiShow(num){
//试题展示
clearTimeout(timecode);
 $('.sound_single').removeClass('active');
	mp.clear();
    var content = '';
	var tcontent = '';
	rightAnswer = '';
	var quetts = '';
	var flag_content = '';
	var ttshtml = '';
	var duwenque = '';
	quetts = questionInfo[num].tts;
	$(quetts).each(function(k,ttsval){
		
		if(ttsval.flag_content){
					flag_content = ttsval.flag_content+':&nbsp;'
				}
				ttshtml += '<p>'+flag_content+ttsval.tts_content+'</p>';
	});
	if(from_platform == "word"){
		if(questionInfo[num].mp3type == 1){
			content += '<h3 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[num].id + '">' +questionInfo[num].papertitle+'<a href="javascript:;" class="sound_single_danti" mp3="'+questionInfo[num].mp3+'" mp3type="'+questionInfo[num].mp3type+'" style="display:none;"></a></h3>';
		}
		else{
			content += '<h3 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[num].id + '">' +questionInfo[num].papertitle+'<a href="javascript:;" class="sound_single_danti" mp3="'+questionInfo[num].mp3+'" mp3type="'+questionInfo[num].mp3type+'" ></a></h3>';
		}
		
	}
	else{
		content += '<h3 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[num].id + '">' +questionInfo[num].papertitle+'<a href="javascript:;" class="sound_single_danti" mp3="'+questionInfo[num].mp3+'" mp3type="'+questionInfo[num].mp3type+'" ></a></h3>';
	}
	if(questionInfo[num].stem_type == '1'){
		for(var i = 0; i< questionInfo[num].answer.length; i++){
			if(questionInfo[num].typeid == '3'){
				if(questionInfo[num].answer[i]['answer'] == 0) questionInfo[num].answer[i]['answer'] = '×';
		   		if(questionInfo[num].answer[i]['answer'] == 1) questionInfo[num].answer[i]['answer'] = '√';
			}
		  
			rightAnswer += questionInfo[num].answer[i]['answer'];
	   }
		
		
		if (questionInfo[num].typeid == 1) {  //选择题展示
			itemsOptions = questionInfo[num].items;
			
			if (questionInfo[num].itemtype == 0) {    //选项是文字
				content += '<dl class="ti_xuanze">';
				content += '<dt>' + removeHTMLTagimg(questionInfo[num].tcontent) + '</dt>';
				for (var i = 0; i < itemsOptions.length; i++) {
					content += '<dd>' + itemsOptions[i].flag + '.<span>' + itemsOptions[i].content + '</span></dd>';
				}

			}
			else {
				content += '<dl class="ti_xuanze ti_tupian">';
				content += '<dt>' + removeHTMLTagimg(questionInfo[num].tcontent) + '</dt>';
				for (var i = 0; i < itemsOptions.length; i++) {
					content += '<dd><img src="'+resource_path+'/uploads/' + itemsOptions[i].content + '"><span>' + itemsOptions[i].flag + '</span></dd>';
				}

			}
			// content += '<a href="javascript:;" class="sound_single" mp3="' + questionInfo[num].mp3 + '"></a>';
			content += '</dl>';
		}
		else if(questionInfo[num].typeid == 3){   //判断题展示
			
			tcontent = removeHTMLTagimg(questionInfo[num].tcontent);
			content += '<ul class="ti_panduan">';
			content += '<li>' + tcontent + '<span>√</span><span>×</span></li>';
			content += '</ul>';
		}
		else if(questionInfo[num].typeid == 2){		//填空题展示
			tcontent = removeHTMLTagimg(content_replace(questionInfo[num].tcontent));
			content += '<ul class="ti_tiankong">';
			content += '<li>' + tcontent + '</li>';
			content += '</ul>';
		}
		else{
			var question_num = '';
		    if(questionInfo[num].question_num != ''){
		        question_num = questionInfo[num].question_num+'. ';
		    }
		    else{
		        question_num = '';
		    }
			temphtmlstr = '';
			itemsdata = questionInfo[num].items;
			paixuhtml1 = '';
		    paixuhtml2 = '';
		    var answer = questionInfo[num].que_answer;
		    $(itemsdata).each(function(itemskey,itemsvalue){
			    if(questionInfo[num].itemtype == '0'){	//选项是文字
			    	paixuhtml1 += '<li>'+itemsvalue.flag+'.'+itemsvalue.content+'</li>';
			    }
			   	else{	//选项是图片
		            paixuhtml1 += '<li><img src="'+resource_path+'/uploads/'+itemsvalue.content+'"><span>'+itemsvalue.flag+'.</span></li>';
			    }
		        //alert(answer[itemskey].answer_num);
			   
		    });
		    $(answer).each(function(answerkey,answerval){
		         paixuhtml2 += '<li>'+answerval.answer_num+'.<input stem_type="'+stem_type+'" type="text" name="'+quevalue.id+'" size="5" maxlength="5"></li>';
		    });
		    if(questionInfo[num].itemtype == '0'){
		        temphtmlstr += '<ul class="ti_list">';
		        temphtmlstr += paixuhtml1;
		        temphtmlstr += '</ul>';
		    }
		    else{
		        temphtmlstr += '<ul class="ti_tupian">';
		        temphtmlstr += paixuhtml1;
		        temphtmlstr += '</ul>';
		    }
		    temphtmlstr += '<div class="clearfix"></div>';
		    temphtmlstr += '<ul class="ti_paixu">';
		    temphtmlstr += paixuhtml2;
		    temphtmlstr += '</ul>';
		    temphtmlstr += '<div class="clearfix"></div>';
		    content += temphtmlstr;
		}
	}
	 else{
	 	 
	 	 content +='<h4>'+questionInfo[num].content+'</h4>';
	 	 duwenque = questionInfo[num].question;
        $(duwenque).each(function(a,duval){
            $(duval.answer).each(function(b,avalue){
            	if(duval.typeid == '3'){
					if(avalue.answe == 0) avalue.answe = '×';
			   		if(avalue.answe == 1) avalue.answe = '√';
				}
                rightAnswer += avalue.answer_num+'.'+avalue.answer+'  ';
            });
            if (duval.typeid == 1) {  //选择题展示
				itemsOptions = duval.items;
				
				if (duval.itemtype == 0) {    //选项是文字
					content += '<dl class="ti_xuanze">';
					content += '<dt>' +duval.question_num+'. '+ removeHTMLTagimg(duval.tcontent) + '</dt>';
					for (var i = 0; i < itemsOptions.length; i++) {
						content += '<dd>' + itemsOptions[i].flag + '.<span>' + itemsOptions[i].content + '</span></dd>';
					}

				}
				else {
					content += '<dl class="ti_xuanze ti_tupian">';
					content += '<dt>' + removeHTMLTagimg(duval.tcontent) + '</dt>';
					for (var i = 0; i < itemsOptions.length; i++) {
						content += '<dd><img src="'+resource_path+'/uploads/' + itemsOptions[i].content + '"><span>' + itemsOptions[i].flag + '</span></dd>';
					}

				}
				// content += '<a href="javascript:;" class="sound_single" mp3="' + questionInfo[num].mp3 + '"></a>';
				content += '</dl>';
			}
			else if(duval.typeid == 2){		//填空题展示
				tcontent = removeHTMLTagimg(content_replace(duval.tcontent));
				content += '<ul class="ti_tiankong">';
				content += '<li>' + tcontent + '</li>';
				content += '</ul>';
			}
			else if(duval.typeid == 3){   //判断题展示
			
				tcontent = duval.tcontent;
				content += '<ul class="ti_panduan">';
				content += '<li>' + removeHTMLTagimg(tcontent) + '<span>√</span><span>×</span></li>';
				content += '</ul>';
			}
			else{
				var question_num = '';
			    if(duval.question_num != ''){
			        question_num = duval.question_num+'. ';
			    }
			    else{
			        question_num = '';
			    }
				temphtmlstr = '';
				itemsdata = duval.items;
				paixuhtml1 = '';
			    paixuhtml2 = '';
			    var answer = duval.answer;
			    $(itemsdata).each(function(itemskey,itemsvalue){
				    if(duval.itemtype == '0'){	//选项是文字
				    	paixuhtml1 += '<li>'+itemsvalue.flag+'.'+itemsvalue.content+'</li>';
				    }
				   	else{	//选项是图片
			            paixuhtml1 += '<li><img src="'+resource_path+'/uploads/'+itemsvalue.content+'"><span>'+itemsvalue.flag+'.</span></li>';
				    }
			        //alert(answer[itemskey].answer_num);
				   
			    });
			    $(answer).each(function(answerkey,answerval){
			         paixuhtml2 += '<li>'+answerval.answer_num+'.<input stem_type="'+stem_type+'" type="text" name="'+quevalue.id+'" size="5" maxlength="5"></li>';
			    });
			    if(duval.itemtype == '0'){
			        temphtmlstr += '<ul class="ti_list">';
			        temphtmlstr += paixuhtml1;
			        temphtmlstr += '</ul>';
			    }
			    else{
			        temphtmlstr += '<ul class="ti_tupian">';
			        temphtmlstr += paixuhtml1;
			        temphtmlstr += '</ul>';
			    }
			    temphtmlstr += '<div class="clearfix"></div>';
			    temphtmlstr += '<ul class="ti_paixu">';
			    temphtmlstr += paixuhtml2;
			    temphtmlstr += '</ul>';
			    temphtmlstr += '<div class="clearfix"></div>';
			    content += temphtmlstr;
			}

        });

	 }
		content += '<span class="answer2">正确答案：'+rightAnswer+'</span>';
		content += '<span class="answer2">听力材料：'+ttshtml+'</span>';
 
    $('.tiCon').html(content);
    $('.sound_single_danti').unbind('click').click(function(){
            clearTimeout(timecode);
            if($(this).hasClass('active')){
                mp.pause();
                $(this).removeClass('active');
            }
            else{
                $('.sound_single_danti').removeClass('active');
                $(this).addClass('active');
                qplay(100,$(this).attr('mp3'),$(this).attr('mp3type'));
            }
        });
    //alert(content);
	art.dialog({
		margin:15,
		title:'试题详细信息',
		width:600,
		content: document.getElementById('tixing'),//获取id为tixing层里面的内容，tixing层在页面下方
		lock:true,
		opacity: 0.2,
		okValue:'确定',
		ok: function () {
			mp.clear();
        return true;
    },
	cancelValue: '关闭',
    cancel: true
	});
}