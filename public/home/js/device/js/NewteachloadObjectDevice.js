(function ($) {
    $.loadObject = function () { };
    $.extend($.loadObject,
    		 {
    		 	urlObject:{
    		 		dUrl:'',
    		 		qUrl:'',
    		 		qtpeUrl:''
    		 	}
    		 },
    		 {
    		 	ctxPath:''
    		 },
    		 {
    		 	selQData:[]
    		 },
    		 {
    		 	gridStoreSize:0,
    		 	setTotal:function(total){
    		 		this.gridStoreSize=total;
    		 	},
    		 	getTotal:function(){
    		 		return this.gridStoreSize;
    		 	}
    		 },
    		 {
    		 	pageParams:{
    		 		start:0,
    		 		limit:1
    		 	}
    		 },
    		 {
    		 	setPageParams:function(data){
    		 		this.pageParams=$.extend(this.pageParams, data); 
    		 	}
    		 },
    		 {
    		 	getPageParams:function(){
    		 		return this.pageParams;
    		 	}
    		 },
    		 {
    		 	getSelQData:function(){
    		 		return this.selQData;
    		 	}
    		 },
    		 {
    		 	setSelQData:function(data){
    		 		selQData=$.merge(this.selQData, data); 
    		 	}
    		 },
    		 {
    		 	addSelQData:function(data){
    		 		this.selQData.push(data);
    		 	}
    		 },
    		 {
    		 	isExist:function(item){
    		 		return jQuery.inArray(item,this.selQData)!=-1;
    		 	}
    		 },
    		 {
    		 	isNotExist:function(item){
    		 		return jQuery.inArray(item,this.selQData)==-1;
    		 	}
    		 },
    		 {
    		 	qtypeClick:function(){}
    		 },
    		 {
    		 	difficultyClick:function(){}
    		 },
    		 {
    		 	setLoadUrl:function(options){
    		 		urlObject=$.extend({},options);
    		 	}
    		 },
    		 {
    		 	getLoadUrl:function(){
    		 		return urlObject;
    		 	}
    		 },
    		 {
    		 	params:{}
    		 },
    		 {
    		 	setParams:function(options){
    		 		this.params=$.extend({},options);
    		 	}
    		 },
    		 {
    		 	addParams:function(options){
    		 		this.params=$.extend(getParams(),options);
    		 	}
    		 },
    		 {
    		 	getParams:function(){
    		 		return this.params;
    		 	}
    		 },
    		 {
    		 	loadOneTest:function(index,callbackFuction){
    		 		if(index<1 ){
    		 			return {};
    		 		}
    		 		var param=$.extend(getParams(),{start:index-1,limit:1});
    		 		var value={};
    		 		$.ajax({
						type:'POST',
						url:urlObject.qUrl,
						dataType:'json',
						data:param,
						success:function(data) {
							value={
								ques_id:data.items[0].id,
								currentNum:index,
								ques_num:data.total
							};
							if(jQuery.isFunction( callbackFuction )){
								callbackFuction(value);
							}
						}
					});
    		 	}
    		 },
             { loadDifficulty: function(){
             		var param=getParams();
					param['_dc']=(new Date()).getTime();
					$.template( "difficultyTemplate", '<a href="javascript:void(0);" did="${id}">${name}</a>' );
					$.ajax({
						type:'POST',
						url:urlObject.dUrl,
						dataType:'json',
						data:param,
						success:function(data) {
							$("#difficulty a[did!='']").remove();
							$.tmpl( "difficultyTemplate",data).appendTo("#difficulty");
							$("#difficulty a").click(function(){
								var olddi=$("#difficulty a[class=cur]");
								var newdi=this; 
								if(olddi.attr('did')==newdi.did){
								}else{
									olddi.removeClass('cur');
									$(newdi).addClass('cur');
									$.loadObject.difficultyClick();
								}
							});
						}
					});
             	} 
             },
             { loadQtypeId: function(){
					var param=getParams();
					param['_dc']=(new Date()).getTime();
					$.template( "qtypeTemplate", '<a href="javascript:void(0);" qid="${id}">${name}</a>' );
					$.ajax({
						type:'POST',
						url:urlObject.qtpeUrl,
						dataType:'json',
						data:param,
						success:function(data) {
							$("#qtype_id a[qid!='']").remove();
							$.tmpl( "qtypeTemplate",data).appendTo("#qtype_id");
							$("#qtype_id a").click(function(){
								var oldqt=$("#qtype_id a[class=cur]");
								var newqt=this; 
								if(oldqt.attr('qid')==newqt.qid){
								}else{
									oldqt.removeClass('cur');
									$(newqt).addClass('cur');
									$.loadObject.qtypeClick();
								}
							});
						}
					});
				}
			},
			{
				loadQuestion: function (){
					var param=$.extend(getParams(),$.loadObject.getPageParams());
					param['_dc']=(new Date()).getTime();
					$.ajax({
						type:'get',
						url:urlObject.qUrl,
						dataType:'json',
						data:param,
						success:function(data) {
							isReceiveSignal = false;//重置是否接收答题信号
							anMaps = {};//重置本次作答答题结果json对象
							anMapsHis = {};//重置历史作答答题结果json对象
							answered_stu_num = 0;//重置已答题人数
							//重置"答题人数"窗口中显示的学生答题结果
							$("#student a[id^='answer_']").attr("class","none");
							$("#student span[id^='span_']").text("");
							
							//处理分页
							$.pagebar.pageParams={
								start:data.start,
								limit:data.limit,
								total:data.total
							};
							$.pagebar.pageSelectID='pagebar1';
							$.pagebar.inint();
							$.pagebar.itemClick=function(d){
								isReceiveSignal = false;//点击换页后先不允许接收答题信号，直到新题读出来后再判断是否接收信号
								if($('#startorstop').text()=="停止答题" && isObjective){
									saveAnswer();//保存答题结果
								}
								$.loadObject.setPageParams(d);
								for(var i=0;i<3;i++){
									var modal;
									if(i==0){
										modal=$("#student");
									}
									if(i==1){
										modal=$("#answer");
									}
									if(i==2){
										modal=$("#result");
									}
									var topMeasure  = parseInt(modal.css('top'));
									modal.css({'visibility' : 'hidden', 'top' : topMeasure});
									$('.student-bg').css({'display' : 'none'});	
								}
								$.loadObject.loadQuestion();
							};//处理分页结束
							
							var questionsNum = data.total;//训练中的试题数
							if(questionsNum==0){//没有试题时的处理
								$('.aBtn a[data-reveal-id="student"]').removeAttr("data-reveal-id").attr("data-reveal-newId","student");
								$('.aBtn a[data-reveal-newId="student"]').attr("class","close-student nodisplay");
								$('.renshu').hide();
								$('.aBtn a[data-reveal-id="result"]').removeAttr("data-reveal-id").attr("data-reveal-newId","result");
								$('.aBtn a[data-reveal-newId="result"]').attr("class","close-student nodisplay");
								$('.aBtn a[data-reveal-id="answer"]').removeAttr("data-reveal-id").attr("data-reveal-newId","answer");
								$('.aBtn a[data-reveal-newId="answer"]').attr("class","close-student nodisplay");
								$('#startorstop').addClass("nodisplay").attr("href","javascript:void(0);");
								$('#startorstop').text("开始答题");
								return;
							}
							
							var questionObj = data.items[0];//当前试题对象
							var questionId = questionObj.id;
							var rightAnswer = questionObj.answer;//本题正确答案
							var answerType = questionObj.answer_type;
							answerControl = questionObj.answer_control;//答题控件
							//设置是否客观题全局变量
							var objective = questionObj.objective;
							if(objective=="1"){//客观题
								isObjective = true;
							}else{
								isObjective = false;
							}
							//设置本题是否是判断题
							if(answerType=="right_wrong"){
								isRightWrongQues = true;
							}else{
								isRightWrongQues = false;
							}
							if(answerControl=="radio"){
								if(isRightWrongQues){
									$("#inValidMemo").html("注：无效作答指含有选项以外的作答。");
								}else{
									$("#inValidMemo").html("注：无效作答指含有选项以外的作答及选择多个答案的作答。");
								}
							}else{
								$("#inValidMemo").html("注：无效作答指答案中包含了选项以外的作答；各选项的选择人数统计仅包含有效作答学生。");
							}
							anMapsHis = questionObj.studentsAnswerMap; //设置学生历史答题结果的json对象
							answerOptions = questionObj.answerOptionsList; //设置试题答案选项的json数组
							//将学生历史答题结果显示在"答题人数"窗口中
							for(var student_id in anMapsHis){
								var stuAnswerObj = anMapsHis[student_id][0];
								var stuAnswer = stuAnswerObj.anwser;
								if($("#answer_"+student_id).length>0 && stuAnswer){//有该学生才做处理，防止学生换班的情况；没作答不做处理
									answered_stu_num++;
									var anwser_result = stuAnswerObj.anwser_result;
									//var anwser_status = stuAnswerObj.anwser_status;
									//判断答案是否无效：选择候选项以外的选项、单选题选多个
									var inValidFlag = false;
									var stuAnswerArr = stuAnswer.split("");
									if(answerControl=="radio" && stuAnswerArr.length>1){
										inValidFlag = true;
									}else{
										for(var i=0;i<stuAnswerArr.length;i++){
											if($.inArray(stuAnswerArr[i], answerOptions)==-1){
												inValidFlag = true;
												break;
											}
										}
									}
									if(inValidFlag){
										document.getElementById("answer_"+student_id).className="bgcOrange";
										document.getElementById("span_"+student_id).innerHTML="("+stuAnswer+")";
									}else{
										if(anwser_result=="100"){//答题正确
											document.getElementById("answer_"+student_id).className="bgcGreen";
											document.getElementById("span_"+student_id).innerHTML="("+stuAnswer+")";
										}else{//答题错误
											document.getElementById("answer_"+student_id).className="bgcRed";
											document.getElementById("span_"+student_id).innerHTML="("+stuAnswer+")";
										}
									}
								}
							}
							document.getElementById("answered_stu_num").innerHTML = answered_stu_num;//设置当前题目已经答题的人数
							
							$("#list> li[class!=shijbt]").remove();
							var index=data.start;
							$.loadObject.setTotal(data.total);
							
							var file = "";
							var qfile = data.items[0].QFile;
							if(qfile!=null && qfile.length>0) {
								for(var i=0; i<qfile.length; i++){
									var f = qfile[i];
									if(f.file_extname=='mp3') {
										file += '<div style="padding:0px 14px;"><span class="mp3" mp3url="' + f.file_url + '"></span></div>'; 
									} else {
										file += '<div style="padding:0px 14px;"><font style="color:green">附件：</font><a href="' + f.file_url + '" target="_blank">' + f.file_source_name + '</a></div>';
									}
								}
							}
							//是客观题显示按钮
							if(isObjective){//客观题
								$('.aBtn a[data-reveal-newId="student"]').removeAttr("data-reveal-newId").attr("data-reveal-id","student");
								$('.aBtn a[data-reveal-id="student"]').attr("class","close-student");
								$('.renshu').show();
								$('.aBtn a[data-reveal-newId="result"]').removeAttr("data-reveal-newId").attr("data-reveal-id","result");
								$('.aBtn a[data-reveal-id="result"]').attr("class","close-student");
								$('#startorstop').removeClass("nodisplay").attr("href","javascript:startorstop();");
								if(answered_stu_num==0){
									//无历史答题结果就默认开始答题
									isReceiveSignal = true;
									$('#startorstop').text("停止答题");
								}else{
									//有历史答题结果就不接收答题信号
									isReceiveSignal = false;
									$('#startorstop').text("开始答题");
								}
							}else{//主观题
								$('.aBtn a[data-reveal-id="student"]').removeAttr("data-reveal-id").attr("data-reveal-newId","student");
								$('.aBtn a[data-reveal-newId="student"]').attr("class","close-student nodisplay");
								$('.renshu').hide();
								$('.aBtn a[data-reveal-id="result"]').removeAttr("data-reveal-id").attr("data-reveal-newId","result");
								$('.aBtn a[data-reveal-newId="result"]').attr("class","close-student nodisplay");
								$('#startorstop').addClass("nodisplay").attr("href","javascript:void(0);");
								$('#startorstop').text("开始答题");
							}
						
							var k_content = questionObj.knowledge_content;
							var difficulty_factor = questionObj.difficulty_factor;
							if(!difficulty_factor){
								difficulty_factor = '';
							}
							
							data.items[0].qcontent = 
								data.items[0].qcontent.replace(/position: relative;/g," "); // 解决放大时，图片不能发大导致图片错位
							var markups='<li style="list-style:none;"><div class="shux" style="{{html $item.getBorder()}}">' +
								'<h2>${qtype_name} - ${sbj_name} - 主知识点：'+k_content+' - 难度系数：'+difficulty_factor+'</h2></div>'+
					    		file + 
					    		'<input type="hidden" id="questionId" value="${id}"/>'+
					    		'<input type="hidden" id="question_${id}" value="${answer}"/>'+
					    		'<div class="neir fixck bigger_150">' + 
					    		'{{html qcontent}}' +
					    		'</div>' +
					    		'<div class="neir fixck bigger_150" style="display: none ;color:red" id="answer_value">{{html answer}}</div>'+
					    		'<div class="neir fixck bigger_150" style="display: none ;color:red" id="answer_analysis_value">{{html answer_analysis}}</div>'+
					    		'<div class="clearfix"></div></li>';  
				    		     
							$.template( "questionsTemplate", markups);
							
							$.tmpl( "questionsTemplate",data.items, {
								getIndex:function(){
									index=index+1;
									return index;
								},
								getBorder:function(){
									if(i>0){
										return "";
									}
									i=i+1;
									return "border:0;";
								},
								getOperateCls: function( isFavorite ) {
									if(!isFavorite){
							        	return 'btn_tk01';
							        }
							        return 'btn_tk02';
								},
							    getOperateValue: function( isFavorite ) {
							        if(!isFavorite){
							        	return '收藏';
							        }
							        return '已收藏';
							    } 
						    }).appendTo("#list");
							
						    $(".bigger_150").css("zoom",zoom);
						    
						    $(".mp3").each(function() {
								var file = jQuery(this).attr("mp3url");
								if(file!=null && file!='') {
									$(this).yump3();
								}
							});
						},
						error:function(data,textStatus, errorThrown){
							alert("读取试题请求异常:"+data.responseText);
						}
					});
				}
			}
           );
})(jQuery);