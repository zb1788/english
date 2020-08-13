/*
 * jQuery Reveal Plugin 1.0
 * www.ZURB.com
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/


(function($) {

/*---------------------------
 Defaults for Reveal
----------------------------*/
	 
/*---------------------------
 Listener for data-reveal-id attributes
----------------------------*/

	$('a[data-reveal-id]').live('click', function(e) {
		e.preventDefault();
		//var obj = $('.student-bg');
		var modalLocation = $(this).attr('data-reveal-id');
		//alert(modalLocation);
		if(modalLocation=='student'){
			$("#student ul > li").removeClass("show");
		}
		if(modalLocation=='answer'){
			// alert(rightAnswer);
			var trueanswer = rightAnswer;
			var ttclstr = '';
			var tts_flag = '';
			if(trueanswer == 0) trueanswer = "×";
			if(trueanswer == 1) trueanswer = "√";
			$(questionInfo[currentPage - 1]['tts']).each(function(i,ttsvalue){
				if(ttsvalue.flag_content != ''){
					tts_flag = ttsvalue.flag_content+"：";
				}
				else{
					tts_flag = '';
				}
				ttclstr += '<p>'+tts_flag+ttsvalue.tts_content+'</p>';
			});
			$('#answer_display').html(trueanswer);//参考答案
			$('#answer_analysis_display').html(ttclstr);//听力材料
			//答案解析为空就隐藏不显示
			// var analysis_text = $.trim($('#answer_analysis_display').text());
			// if(analysis_text==null || analysis_text==""){
			// 	$('#answer_analysis_p').hide();
			// }else{
			// 	$('#answer_analysis_p').show();
			// }
			//设置答案解析窗口高度，防止低分辨率时显示不全
			var maxHeight = Math.round($(window).height()*0.55);
		    var answerWinH = $("#answer div.boxCon")[0].scrollHeight;
		    $("#answer div.boxCon").css("maxHeight",maxHeight);
		    if(answerWinH>maxHeight){
		    	$("#answer div.boxCon").css("overflow-y","scroll");
		    }else{
		    	$("#answer div.boxCon").css("overflow-y","hidden");
		    }
		}
		if(modalLocation=='result'){
			$("#result > div.boxCon > dl.chart").remove();
			//设置正确率
			var answerNum = $("#student ul.stuList a[answerStatus!='0']").length;//已作答人数
			var rightNum = $("#student ul.stuList a[answerResult='100']").length;
			var rightRatio = 0;
			if(answerNum!=0){
				rightRatio = Math.round(rightNum/answerNum*100);
			}
			$("#rightNum").html(rightNum+"人");
			$("#rightRatio").html(rightRatio+"%");
			//设置用于答案统计的学生答案json对象,如果是重新作答就统计本次作答结果,否则就统计历史作答结果
			var anMapsToDeal = {};
			if(answered_stu_num>0){
				if(!$.isEmptyObject(anMaps)){
					//alert("11");
					anMapsToDeal = anMaps;
				}else{
					//alert(22);
					anMapsToDeal = anMapsHis;
				}
			}
			//alert(anMapsToDeal);
			var staticMap = {};//各个答案选项的答题人数map--key:选项;value:人数
			for(var i=0;i<answerOptions.length;i++){
				var answerOption = answerOptions[i];
				//alert(answerOption);
				var stuIds = [];
				//alert(anMapsToDeal['41010110012983'].answer);
				for(var student_id in anMapsToDeal){
					//alert(student_id)
					//alert(anMapsToDeal[student_id]['answer']);
					var stuAnswerObj = anMapsToDeal[student_id];
					var stuAnswer = stuAnswerObj.answer;
					//alert(stuAnswer);
					if($("#answer_"+student_id).length>0 ){//有该学生才做处理，防止学生换班的情况；没作答不做处理
						// if($("#answer_"+student_id).attr("class")!="bgcOrange"){
						// 	// var stuAnswerArr = stuAnswer.split("");//如果是多选题，那么学生作答的每个选项都分别统计
						// 	// for(var j=0;j<stuAnswerArr.length;j++){
						// 	// 	if(answerOption==stuAnswerArr[j]){
						// 	// 		stuIds.push(student_id);
						// 	// 		break;
						// 	// 	}
						// 	// }
						// 	//alert('ss');
						// 	//stuIds.push(student_id);
						// }
						if(answerOption == stuAnswer){
							stuIds.push(student_id);
						}
					}
				}
				
				staticMap[answerOption] = stuIds;
			}
			
			//设置统计图
			var barTotalLength = 450;//柱图总长度
			var _k="";
			$.each(staticMap, function(k, v){
				var stuCount = v.length;
				var ratio = 0;
				var width = 0;
				if(sum_student!=0){
					ratio = Math.round(stuCount/sum_student*100);
					width = Math.round(barTotalLength*(ratio/100));
				}
				_k = k;
				if(k == 0) _k = "×";
				if(k == 1) _k = "√";
				$("#result > div.boxCon > div.clearfix").before('<dl class="chart" type="option"><dt data="'+k+'">'+_k+'</dt><dd class="jdb"><i style="width:'+width+'px;"></i><b>'+ratio+'%</b></dd><dd class="num1"><span>'+stuCount+'</span>人</dd></dl>');
			});
			//设置无效作答和未作答
			var inValidNum = $("#student ul.stuList a[answerStatus='1']").length;
			var inValidRatio = 0;
			var inValidWidth = 0;
			if(sum_student!=0){
				inValidRatio = Math.round(inValidNum/sum_student*100);
				inValidWidth = Math.round(barTotalLength*(inValidRatio/100));
			}
			var noAnswerNum = $("#student ul.stuList a[answerStatus='0']").length;
			var noAnswerRatio = 0;
			var noAnswerWidth = 0;
			if(sum_student!=0){
				noAnswerRatio = Math.round(noAnswerNum/sum_student*100);
				noAnswerWidth = Math.round(barTotalLength*(noAnswerRatio/100));
			}
			$("#result > div.boxCon > div.clearfix").before('<dl class="chart" type="inValid"><dt>'+'无效作答'+'</dt><dd class="jdb"><i style="width:'+inValidWidth+'px;"></i><b>'+inValidRatio+'%</b></dd><dd class="num1"><span>'+inValidNum+'</span>人</dd></dl>');
			$("#result > div.boxCon > div.clearfix").before('<dl class="chart" type="noAnswer"><dt>'+'未作答'+'</dt><dd class="jdb"><i style="width:'+noAnswerWidth+'px;"></i><b>'+noAnswerRatio+'%</b></dd><dd class="num1"><span>'+noAnswerNum+'</span>人</dd></dl>');
			//处理统计图弹窗提示作答学生名单
			$("#result > div.boxCon > dl.chart dd.jdb").each(function(){
				var stuCount = Number($(this).next(".num1").children("span").text());
				if(stuCount>0){
					$(this).css("cursor","pointer");
					$(this).click(function(){
						$("#studentStat").height("auto");
						$("#studentStat ul.stuList").remove();
						$("#studentStat > div.boxCon").append('<ul class="stuList"></ul>');
						//$("#studentStat > div.boxCon > p").hide();
						var title = "";//标题
						var _option = '';
						if($(this).parent("dl").attr("type")=="option"){
							var option = $(this).prev("dt").attr('data');
							_option = option;
							if(option == 0) _option = "×";
							if(option == 1) _option = "√";
							title = "选择"+_option+"的学生";
							var stuIds = staticMap[option];
							for(var i=0;i<stuIds.length;i++){
								var student_id = stuIds[i];
								var stuName = $("#answer_"+student_id).children("font").text();
								$("#studentStat ul.stuList").append('<li><a href="javascript:void(0);" class="none"><font>'+stuName+'</font><span></span></a></li>');
							}
						}else if($(this).parent("dl").attr("type")=="inValid"){
							title = $(this).prev("dt").text()+"的学生";
							$("#student ul.stuList a[answerStatus='1']").each(function(){
								var stuName = $(this).children("font").text();
								$("#studentStat ul.stuList").append('<li><a href="javascript:void(0);" class="none"><font>'+stuName+'</font><span></span></a></li>');
							});
						}else if($(this).parent("dl").attr("type")=="noAnswer"){
							title = $(this).prev("dt").text()+"的学生";
							$("#student ul.stuList a[answerStatus='0']").each(function(){
								var stuName = $(this).children("font").text();
								$("#studentStat ul.stuList").append('<li><a href="javascript:void(0);" class="none"><font>'+stuName+'</font><span></span></a></li>');
							});
						}
						$("#studentStat .title h2").text(title);
						var resultH = $("#result").height();
						if($("#studentStat ul.stuList li").length==0){
							//$("#studentStat > div.boxCon > p").show();
						}else{
							//弹窗大小要跟答案分析窗口一样，学生过多时出滚动条
							var statH = $("#studentStat").height();
							if(statH>resultH){
								var stuListH = $("#studentStat ul.stuList").height();
								$("#studentStat ul.stuList").height(stuListH-(statH-resultH));
								$("#studentStat ul.stuList li").css("width","12.0%");
								$("#studentStat ul.stuList").width($("#studentStat ul.stuList").width()+14);
								$("#studentStat ul.stuList").css("overflow-y","scroll");
							}
						}
						$("#studentStat").height(resultH);
						$(".bg-on-student").show();
						$("#studentStat").css("visibility","visible");
					});
				}
			});
		}
		if(modalLocation=='paperResult'){
			//如果还未停止答题要先停止答题
			if($('#startorstop').data("status")=="stop"){
				if(VcomAQTool && ("Stop" in VcomAQTool)){
							VcomAQTool.Stop();
				}
					isReceiveSignal = false;
					setStartBtnStatus("start");
					//如果有倒计时就停止计时
					if(PAGE_INTERVAL_ID){
						clearInterval(PAGE_INTERVAL_ID);
					}
					$("#pageTime").hide();
					saveAnswer(2); //保存答题结果
			}
			paperresult(); 
		}
		$('#'+modalLocation).reveal($(this).data());
	});
/*---------------------------
 Extend and Execute
----------------------------*/

    $.fn.reveal = function(options) {
        
        
        var defaults = {  
	    	animation: 'none', //fade, fadeAndPop, none
		    animationspeed: 300, //how fast animtions are
		    closeonbackgroundclick: true, //if you click background will modal close?
		    dismissmodalclass: 'close-student' //the class of a button or element that will close an open modal
    	}; 
    	
        //Extend dem' options
        var options = $.extend({}, defaults, options); 
	
        return this.each(function() {
        
/*---------------------------
 Global Variables
----------------------------*/
        	var modal = $(this),
        		topMeasure  = parseInt(modal.css('top')),
				topOffset = modal.height() + topMeasure,
          		locked = false,
				modalBG = $('.student-bg');

/*---------------------------
 Create Modal BG
----------------------------*/
			if(modalBG.length == 0) {
				modalBG = $('<div class="student-bg" />').insertAfter(modal);
				modalBG.css({position:"absolute",height:$(document).height(),opacity:"0.35"});
			}		    
        	
/*---------------------------
 Open and add Closing Listeners
----------------------------*/
        	//Open Modal Immediately
    		
			//修改逻辑，实现点击同一个按钮时，能切换弹出层的显示/关闭
			if(modal.css('visibility')!='hidden'){
				closeModal();
			}else{
				openModal();
				//Close Modal Listeners
				var closeButton = $("." + options.dismissmodalclass +"[data-reveal-id!='"+modal.attr("id")+"']").bind('click.modalEvent',closeModal)
				if(options.closeonbackgroundclick) {
					modalBG.css({"cursor":"pointer"})
					modalBG.bind('click.modalEvent',closeModal)
				}
			}
    		
/*---------------------------
 Open & Close Animations
----------------------------*/
			//Entrance Animations
			function openModal() {
				modalBG.unbind('click.modalEvent');
				$("." + options.dismissmodalclass).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({'top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"top": $(document).scrollTop()+topMeasure,
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					}
					if(options.animation == "fade") {
						modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+topMeasure});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					} 
					if(options.animation == "none") {
						modal.css({'visibility' : 'visible', 'bottom':$(document).scrollTop()+topMeasure});
						modalBG.css({"display":"block"});	
						unlockModal()				
					}   
				}
			}    	
			
			//Closing Animation
			function closeModal() {
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"top":  $(document).scrollTop()-topOffset,
							"opacity" : 0
						}, options.animationspeed/2, function() {
							modal.css({'top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
							unlockModal();
						});					
					}  	
					if(options.animation == "fade") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"opacity" : 0
						}, options.animationspeed, function() {
							modal.css({'opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
							unlockModal();
						});					
					}  	
					if(options.animation == "none") {
						modal.css({'visibility' : 'hidden', 'top' : topMeasure});
						modalBG.css({'display' : 'none'});	
					}   			
				}
			}
			
/*---------------------------
 Animations Locks
----------------------------*/
			function unlockModal() { 
				locked = false;
			}
			function lockModal() {
				locked = true;
			}	
			
        });//each call
    }//orbit plugin call
})(jQuery);

function paperresult(){
		$.getJSON(ListenConter + 'ListeShowTvyulan', {examsid: examsid,studentClassid:studentClassid,from_platform:from_platform,random:Math.random()}, function(result) {
			        //alert(result.length);
			        var quetts = '';
					var flag_content = '';
					var ttshtml = '';
					
			        var paperResultAreaHtml = '<tr class="tit2"><td width="10%">序号</td><td >题目</td><td width="16%">答对人数/作答人数</td></tr>';
			        $('#qNum').html(result.length);
			        $(result).each(function(i,val){
			        	ttshtml = '';
			        	quetts = val.tts;
						$(quetts).each(function(k,ttsval){
							if(k == 0){
								if(ttsval.flag_content){
								flag_content = ttsval.flag_content+':&nbsp;'
								}
								ttshtml += flag_content+ttsval.tts_content.substring(0,50)+'&nbsp';
							}
							
						});
			        	paperResultAreaHtml += '<tr class="tit2"><td width="10%">'+(parseInt(i)+1)+'</td><td class="qName"><a href="javascript:;" qindex="'+(parseInt(i)+1)+'">'+ttshtml+'</a></td><td width="16%">'+val.replay_right_num+'/'+val.replaynum+'</td></tr>';
			        })
			   		$('#qlist').html(paperResultAreaHtml);
			   		if($("#paperResultArea").innerHeight()>$("#paperResult .boxCon").height()){
							$("#paperResultArea").css({"padding-right":25});
					}
					$("#qlist td.qName").click(function(){
						var qindex = $(this).children("a").attr("qindex");
						viewQues(qindex);
					});
	    		}); 
}