var data=[];
var useranswer="";

//使用全局变量issubmit
function setQuestionContent(data){
	var questionhtml="";
	if(data.question[0].typeid=="1"){
		if(data.parent.length==0){
			questionhtml=questionhtml+'<div id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy" id="audio-btn" quesid="'+data.question[0].id+'" type="0"><img id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div><div id="loading" style="display:none;"></div>';

			//questionhtml=questionhtml+'<div id="lanren" style="margin-top:10px;"><div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
		}
		questionhtml=questionhtml+'<div class="title" style="margin-top:10px;margin-left:10px;font-size: 1.0em;font-family: times;color:black;text-align:center;">';
		questionhtml=questionhtml+data.question[0].tcontent;
		questionhtml=questionhtml+'</div>';

		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: while;text-align:center;">';
		//选项的添加
		mui.each(data.items,function(index,item){
		    questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="1" itemflag="'+item.flag+'" quesid="'+data.question[0].id;
		    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
		    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
			questionhtml=questionhtml+'<a href="javascript:;" typeid="1" itemflag="'+item.flag+'" quesid="'+data.question[0].id;
		    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
		    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
		    if(data.question[0].itemtype=='1'){
		    	questionhtml=questionhtml+'<div class="mui-media-body" style="float:left;margin-top:30px;">';
		    }else{
		    	questionhtml=questionhtml+'<div class="mui-media-body" style="float:left;">';
		    }

			//正确答案和错误答案的展示
			if(issubmit=='1'){
				if(data.answer[0].quesanswer==item.content){
					useranswer=item.flag;
					if(item.flag==data.answer[0].answer){
						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
					}else{
						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
					}
				}else{
					if(item.flag==data.answer[0].answer){
						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;text-color:gray;" >';
					}else{
						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
					}
				}
//				if(item.flag==data.answer[0].answer){
//					if(quesanswer==item.content){
//						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:blue; border-radius:25px;display: inline-block;text-color:gray;" >';
//					}else{
//					    questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:blue; border-radius:25px;display: inline-block;text-color:gray;" >';
//					}
//				}else{
//					if(quesanswer==item.content){
//						questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:blue; border-radius:25px;display: inline-block;text-color:gray;" >';
//					}else{
//					    questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:blue; border-radius:25px;display: inline-block;text-color:gray;" >';
//					}
//
//					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
//				}
			}else{
				if(item.flag==data.answer[0].answer){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}

			questionhtml=questionhtml+'<span itemflag="'+item.flag+'" style="height:30px; line-height:30px; display:block; color:#666; text-align:center">';
			//选项的名称
			questionhtml=questionhtml+item.flag;
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'</div>';
			questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
			//选项的内容
			if(data.question[0].itemtype=='1'){
				questionhtml=questionhtml+'<img style="float:left;margin-left:20px;" src="http://en.czbanbantong.com/uploads/'+item.content+'" class="itemimg" alt="选项图片" width="120px" height="90px">';
			}else{
				questionhtml=questionhtml+'<div style="float:left;margin-top:5px;margin-left:20px;color:black;">'+item.content+'</div>';
			}

			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
		});
		questionhtml=questionhtml+'</ul>';
		//看是否提交数据
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;color:black;"><font>正确答案是'+useranswer;
				if(data.answer[0].answer==''||data.answer[0].answer==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.answer[0].answer;
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			if(data.answer[0].summary.erroranswer=='null'||data.answer[0].summary.erroranswer==undefined){
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;"></div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
			}
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.parent.length==0){
				//questionhtml=questionhtml+'<div id="lanren">	<div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						type:"1",
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}
	//如果问题是填空题的话进行填空题的样式的加载
	}else if(data.question[0].typeid=="2"){
		questionhtml=questionhtml+'<div id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy" id="audio-btn" quesid="'+data.question[0].id+'" type="0"><img id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div><div id="loading" style="display:none;"></div>';
		//questionhtml=questionhtml+'<div id="lanren" style="margin-top:10px;"><div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
		questionhtml=questionhtml+'<div class="tkcontent" style="margin-top:20px;margin-left:10px;font-size: 1.0em;font-family: times;color: black;text-align:left;">';
		//进行正则表达式的替换要是要蓝色
		var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
        var result;
        var sortid=0;
        var content=data.question[0].tcontent;
        while ((result = patt.exec(content)) != null)  {
        	var replace='<input id="eninput" type="text" class="mui-input-clear eninput"  typeid="2"  quesid="'+data.question[0].id;
		    replace=replace+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid+'"';
		    replace=replace+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'"';
		    replace=replace+' style="width:80px;background-color:blueviolet;border-radius:0.45em;color:white;text-align:center;height:25px;" placeholder="'+data.answer[sortid].answer_num+'"';
		    if(data.answer[sortid].answer!=null&&data.answer[sortid].answer!=undefined){
		    	replace=replace+' value="'+data.answer[sortid].answer+'"/>';
		    }else{
		    	replace=replace+' value=""/>';
		    }
            content=content.replace(result[0], replace);
           	sortid=sortid+1;
        }
		questionhtml=questionhtml+content;
		questionhtml=questionhtml+'</div>';
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;color:black;"><font>正确答案是'+data.answer[0].quesanswer;
				if(data.userans==''||data.userans==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.answer[0].answer;
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			//questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
			if(data.answer[0].summary.erroranswer=='null'||data.answer[0].summary.erroranswer==undefined){
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;"></div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
			}
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.parent.length==0){
				//questionhtml=questionhtml+'<div id="lanren">	<div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						type:"1",
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}
	//如果问题是判断题的话进行判断题的样式的加载
	}else if(data.question[0].typeid=="3"){
		var colorflag=0;
		questionhtml=questionhtml+'<div id="lanren" style="margin-top:10px;"><div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
		questionhtml=questionhtml+'<div class="title" style="margin-top:20px;margin-left:10px;font-size: 1.0em;font-family: times;color: blueviolet;">';
		questionhtml=questionhtml+data.question[0].tcontent;
		questionhtml=questionhtml+'</div>';
//		if(data.parent.length==0){
//			questionhtml=questionhtml+'<div id="lanren"><div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
//			questionhtml=questionhtml+'<ul class="mui-table-view" id="listen">';
//			questionhtml=questionhtml+'<li class="mui-table-view-cell mui-collapse" style="background-color:#efeff4;border: none;" quesid="'+data.question[0].id+'" cur="false">';
//			questionhtml=questionhtml+'<a class="mui-navigate-right" href="#" style="margin-left:3px;color:blue;">听力材料</a>';
//			questionhtml=questionhtml+'<div class="mui-collapse-content" id="ttscontent">';
//			questionhtml=questionhtml+'<h1>h1. Heading</h1>';
//			questionhtml=questionhtml+'<h3>h3. Heading</h3>';
//			questionhtml=questionhtml+'<h2>h2. Heading</h2>';
//			questionhtml=questionhtml+'<h4>h4. Heading</h4>';
//			questionhtml=questionhtml+'<h5>h5. Heading</h5>';
//			questionhtml=questionhtml+'<h6>h6. Heading</h6>';
//			questionhtml=questionhtml+'<p>';
//			questionhtml=questionhtml+'p. 目前最接近原生App效果的框架。';
//			questionhtml=questionhtml+'</p>';
//			questionhtml=questionhtml+'</div>';
//			questionhtml=questionhtml+'</li>';
//			questionhtml=questionhtml+'</ul>';
//		}
		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: white">';
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="3" itemflag="1" quesid="'+data.question[0].id;
	    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
		questionhtml=questionhtml+'<a href="javascript:;" typeid="3" itemflag="1" quesid="'+data.question[0].id;
	    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		if("1"==data.answer[0].answer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
		}
		if("1"==data.answer[0].quesanswer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;" >';
		}
		
		if("1"!=data.answer[0].answer&&"1"!=data.answer[0].quesanswer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
		}
		
		
		
		
		questionhtml=questionhtml+'<span itemflag="1" style="height:30px; line-height:30px; display:block; color:black; text-align:center">';
		//选项的名称
		questionhtml=questionhtml+"A";
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;<font style="color:black;">True</font>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="3" itemflag="0" quesid="'+data.question[0].id;
	    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
		questionhtml=questionhtml+'<a href="javascript:;" typeid="3" itemflag="0" quesid="'+data.question[0].id;
	    questionhtml=questionhtml+'" answerid="'+data.answer[0].quesansid+'" homeworkid="'+data.question[0].homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		if("0"==data.answer[0].answer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
		}
		if("0"==data.answer[0].quesanswer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;" >';
		}
		
		
		if("0"!=data.answer[0].answer&&"0"!=data.answer[0].quesanswer){
			questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
		}
		
		questionhtml=questionhtml+'<span itemflag="0" style="height:30px; line-height:30px; display:block; color:black; text-align:center">';
		//选项的名称
		questionhtml=questionhtml+"B";
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;<font style="color:black;">False</font>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;color:black;"><font>正确答案是';
				if(data.answer[0].quesanswer=='1'){
					questionhtml=questionhtml+'True';
				}else{
					questionhtml=questionhtml+'False';
				}
				if(data.answer[0].answer==''||data.answer[0].answer==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案";
					if(data.answer[0].answer=='1'){
						questionhtml=questionhtml+"True";
					}else{
						questionhtml=questionhtml+"False";
					}
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">';
			if(data.answer[0].summary.erroranswer=='1'){
				questionhtml=questionhtml+'True';
			}else if(data.answer[0].summary.erroranswer=='0'){
				questionhtml=questionhtml+'False';
			}else{
				questionhtml=questionhtml+'';
			}
			questionhtml=questionhtml+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.parent.length==0){
				//questionhtml=questionhtml+'<div id="lanren">	<div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						type:"1",
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}

		//如果问题是排序题的话进行排序题的样式的加载
	}else if(data.question[0].typeid=="4"){
		//排序题样式
		questionhtml=questionhtml+'<div id="lanren"><div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
		questionhtml=questionhtml+'<div class="title" style="margin-top:20px;margin-left:10px;font-size: 1.0em;font-family: times;color: blueviolet;">';
		questionhtml=questionhtml+data.question[0].tcontent;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div style="padding: 10px 10px;"><div id="segmentedControl" class="mui-segmented-control">';
		mui.each(data.answer,function(indexa,itema){
			questionhtml=questionhtml+'<a class="mui-control-item';
			if(indexa==0){
				questionhtml=questionhtml+' mui-active" typeid="4"  quesid="'+data.question[0].id;
			}else{
				questionhtml=questionhtml+'" typeid="4"  quesid="'+data.question[0].id;
			}
	    	questionhtml=questionhtml+'" answerid="'+itema.quesansid+'" homeworkid="'+data.question[0].homeworkid;
	    	questionhtml=questionhtml+'" examsid="'+data.question[0].examsid+'" quizid="'+data.question[0].quizid+'">';
	    	questionhtml=questionhtml+'<label style="color: black;">'+itema.answer_num+'&nbsp;&nbsp;</label>';
			questionhtml=questionhtml+'<font color="red;">';
			if(itema.answer!=''&&itema.answer!=null&&itema.answer!=undefined){
				questionhtml=questionhtml+itema.answer;
			}else{

			}
			questionhtml=questionhtml+'</font></a>';
		});
		questionhtml=questionhtml+'</div></div>';
		questionhtml=questionhtml+'<div><div class="mui-control-content mui-active" style="height:500px;">';
		questionhtml=questionhtml+'<div id="scroll" class="mui-scroll-wrapper"><div class="mui-scroll">';
		questionhtml=questionhtml+'<ul class="mui-table-view">';
		mui.each(data.items,function(index,item){
			questionhtml=questionhtml+'<li class="mui-table-view-cell pxt"  questext="'+item.flag+'" style="background-color: #efeff4;">';
			questionhtml=questionhtml+'<span>'+item.flag+'</span>.<a href="javascript:void(0);" style="display:inline;">';
			if(data.question[0].itemtype=='1'){
				questionhtml=questionhtml+'<img width="120px" height="90px" src="http://en.czbanbantong.com/uploads/'+item.content+'" class="itemimg" alt="选项图片">';
			}else{
				questionhtml=questionhtml+item.content;
			}
			questionhtml=questionhtml+'</a></li>';
		});
		questionhtml=questionhtml+'</ul></div></div></div></div>';
		if(issubmit=='1'){
			//由于排序题改成了选择题所以这部分需要的话在补充
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;color:black;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.parent.length==0){
				//questionhtml=questionhtml+'<div id="lanren">	<div class="play-pause " id="audio-btn" quesid="'+data.question[0].id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question[0].id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						type:"1",
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								console.log(v.tts_content);
								questionhtml=questionhtml+'<li><h5>'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}
	}
	return questionhtml;
}
//进行题型的类的封装其中tts表示的是音频，type表示的是题型其中题干的类型是0，stem表示的是题干的内容
function setContent(data){
	var questionhtml="";
	//如果问题是组合试题
	if(data.parent.length>0&&data.source=="0"){
		questionhtml=questionhtml+'<div  id="parent" quescount="'+data.question[0].quescount+'" stemid="'+data.question[0].stemid+'" >';
		questionhtml=questionhtml+'<div id="item1" class="mui-control-content mui-active" style="height:100px;">';
		questionhtml=questionhtml+'<div id="scroll" class="mui-scroll-wrapper" style="background-color: white; ">';
		questionhtml=questionhtml+'<div class="mui-scroll" style="background-color: white;" >';
//		questionhtml=questionhtml+'<div class="title" style="margin-top:10px;margin-left:10px;font-size: 1.0em;font-family: times;color:black;text-align:left;">';
//		questionhtml=questionhtml+data.parent[0].content;
//		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy" id="audio-btn" quesid="'+data.question[0].id+'" type="1"><img id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div><div id="loading" style="display:none;"></div>';
		questionhtml=questionhtml+'<div class="title" style="margin-top:10px;margin-left:10px;font-size: 1.0em;font-family: times;color:black;text-align:left;">';
		questionhtml=questionhtml+data.parent[0].content;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div  id="children">';
		questionhtml=questionhtml+'<div id="childrentitle">';
		questionhtml=questionhtml+'<ul class="mui-table-view mui-table-view-striped mui-table-view-condensed" style="background:#EEEEEE;">';
		questionhtml=questionhtml+'<li class="mui-table-view-cell">';
		questionhtml=questionhtml+'<h4>';
		questionhtml=questionhtml+'<span class="mui-pull-right" id="quesnum">';
		questionhtml=questionhtml+'<strong style="color:red;font-size:1.5em;" id="cquesindex">1</strong>/'+qunum;
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</h4>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div id="citem" class="mui-control-content mui-active" style="overflow: scroll;">';
		questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</div>';
	}else if(data.source=='1'&&data.parent.length>0){
		questionhtml=questionhtml+'<div id="childrentitle">';
		questionhtml=questionhtml+'<ul class="mui-table-view mui-table-view-striped mui-table-view-condensed" style="background:#EEEEEE;">';
		questionhtml=questionhtml+'<li class="mui-table-view-cell">';
		questionhtml=questionhtml+'<h4>';
		questionhtml=questionhtml+'<span class="mui-pull-right" id="quesnum">';
		questionhtml=questionhtml+'<strong style="color:red;font-size:1.5em;" id="cquesindex">1</strong>/'+qunum;
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</h4>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div id="citem" class="mui-control-content mui-active" style="overflow: scroll;">';
		questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
	}else{
		document.getElementById("quesnum").style.display="";
		questionhtml=questionhtml+'<div id="parent" quescount="'+data.question[0].quescount+'" stemid="'+data.question[0].stemid+'" style="height:100%;">';
		questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
	}
	return questionhtml;
}
//请求响应
function getRespose(url,errinfo,index,source,stemid){
	var respose="";
	listendata=[];
	mui.ajax(url,
		{
		data:{
			studentId:studentid,
			classId:classid,
			index:index,
			source:source,
			stemid:stemid,
			homeworkid:homeworkid,
			iserror:iserror,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			//服务器返回响应，根据响应结果，将试题进行赋值
			var queshtml=setContent(data);
			if(data.parent.length==0||(data.parent.length>0&&data.source=='0')){
				stopaudio();
				//全部填充
				document.getElementById("content").innerHTML=queshtml;
				if(data.parent.length==0){
					document.getElementById("quesindex").innerHTML=(index+1);
				}else{
					document.getElementById("quesnum").style.display="none";
				  document.getElementById("quesindex").innerHTML=(index+1);
				}
			}else{
				document.getElementById("children").innerHTML=queshtml;
			}

			if(data.parent.length>0){
				mui("#scroll").scroll({
					indicators: true //是否显示滚动条
				});
				//document.getElementById("quesindex").innerHTML=(index+1);
				document.getElementById("cquesindex").innerHTML=(index+1);
				//document.getElementById("quescounum").innerHTML=document.getElementById("quesnumcount").innerHTML;
				fheight=document.body.scrollHeight;
				pheight=document.getElementById("item1").offsetHeight;
				cheight=fheight-pheight-100;
				document.getElementById("citem").style.height=cheight+"px";
				document.getElementById("childrentitle").addEventListener("drag",function(e){
					var pheight=document.getElementById("item1").offsetHeight;
					var childh=document.getElementById("citem").offsetHeight;
					document.getElementById("content").style.height=fheight;
					console.log(fheight+"px");
					console.log(pheight+e.detail.deltaY);
					console.log(cheight);
					if((pheight+e.detail.deltaY)>fheight*0.1&&(pheight+e.detail.deltaY)<fheight*0.7){
						document.getElementById("item1").style.height=(pheight+e.detail.deltaY)+"px";
					    document.getElementById("citem").style.height=(fheight-44-54-(pheight+e.detail.deltaY))+"px";
					}
					e.stopPropagation();
				});
			}else{
				document.getElementById("quesindex").innerHTML=(index+1);
			}

			if(issubmit==0){

				//进行单击事件的注入选择试题和判断试题的js注入
				if(data.question[0].typeid=='1'||data.question[0].typeid=='3'){
					mui('ul.xuanze li').on('tap', 'a', function() {
						var items=this.parentNode.parentNode.getElementsByTagName("li");
						for(var i=0;i<items.length;i++){
							items[i].getElementsByTagName("div")[1].style.background="white";
						}
						this.getElementsByTagName("div")[1].style.background="#2bc8a0";
						var questionid=this.getAttribute("quesid");
						var homeworkid=this.getAttribute("homeworkid");
						var quizid=this.getAttribute("quizid");
						var examsid=this.getAttribute("examsid");
						var answerid=this.getAttribute("answerid");
						var useranswer=this.getAttribute("itemflag");
						var typeid=this.getAttribute("typeid");
						//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
						var url="setUseranswer";
						mui.ajax(url,
							{
							data:{
								questionid:questionid,
								useranswer:useranswer,
								homeworkid:homeworkid,
								quizid:quizid,
								answerid:answerid,
								examsid:examsid,
								typeid:typeid,
								ran:Math.random()
							},
							dataType:'json',//服务器返回json格式数据
							type:'post',//HTTP请求类型
							timeout:10000,//超时时间设置为10秒；
							async:true,
							success:function(data){
								//服务器返回然后进行h5的本地存储
								try{
									websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
								}catch(e){
									console.log("不支持websql");
								}
								
							},
							error:function(xhr,type,errorThrown){
								//异常处理；
								return errinfo;
							}
						});
		      	    });
				}
				//填空试题的js进行注入事件
				if(data.question[0].typeid=='2'){
					console.log("aaaa");
					console.log(mui("input.eninput"));
					//document.getElementById("eninput").addEventListener("input",function(e){
	//					console.log(this.value);
	//					console.log(e);
					//});
					mui("div.title").on('input',"input.eninput",function(e){
						console.log(e);
						var questionid=this.getAttribute("quesid");
						var homeworkid=this.getAttribute("homeworkid");
						var quizid=this.getAttribute("quizid");
						var examsid=this.getAttribute("examsid");
						var answerid=this.getAttribute("answerid");
						var useranswer=this.value;
						var typeid=this.getAttribute("typeid");
						//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
						var url="setUseranswer";
						mui.ajax(url,
							{
							data:{
								questionid:questionid,
								useranswer:useranswer,
								homeworkid:homeworkid,
								quizid:quizid,
								answerid:answerid,
								examsid:examsid,
								typeid:typeid,
								ran:Math.random()
							},
							dataType:'json',//服务器返回json格式数据
							type:'post',//HTTP请求类型
							timeout:10000,//超时时间设置为10秒；
							async:true,
							success:function(data){
								//服务器返回然后进行h5的本地存储
								try{
									websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
								}catch(e){
									console.log("不支持websql");
								}
							},
							error:function(xhr,type,errorThrown){
								//异常处理；
								return errinfo;
							}
						});
					});
				}

				//排序试题的js进行注入事件
				if(data.question[0].typeid=='4'){
					mui("div#scroll").on('tap','li',function(){
						var ques=this.getAttribute("questext");
						console.log(ques);
						//选择active的元素
						document.getElementsByClassName("mui-control-item mui-active")[0].getElementsByTagName("font")[0].innerHTML=ques;
						//将用户的答案进行写入数据库
						if(document.getElementsByClassName("mui-control-item mui-active").length>0){
							var url="setUseranswer";
							var questionid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("quesid");
							var homeworkid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("homeworkid");
							var quizid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("quizid");
							var examsid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("examsid");
							var answerid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("answerid");
							var useranswer=ques;
							var typeid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("typeid");
							mui.ajax(url,
								{
								data:{
									questionid:questionid,
									useranswer:useranswer,
									homeworkid:homeworkid,
									quizid:quizid,
									answerid:answerid,
									examsid:examsid,
									typeid:typeid,
									ran:Math.random()
								},
								dataType:'json',//服务器返回json格式数据
								type:'post',//HTTP请求类型
								timeout:10000,//超时时间设置为10秒；
								async:true,
								success:function(data){
									//服务器返回然后进行h5的本地存储
									try{
										websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
									}catch(e){
										console.log("不支持websql");
									}
								},
								error:function(xhr,type,errorThrown){
									//异常处理；
									return errinfo;
								}
							});
						}
						var items=document.getElementsByClassName("mui-control-item");
						for(var i=0;i<items.length;i++){
							if(hasClass(items[i],"mui-active")&&i<items.length-1){
								removeClass(items[i],"mui-active");
								addClass(items[i+1],"mui-active");
								i=items.length;
							}else{
								if(i==(items.length-1)){
									removeClass(items[i],"mui-active");
								}
							}
						}
					});
				}
			}
      	    //进行播放事件的注入
      	    //播放按钮的播放
			document.getElementById("audio-btn").addEventListener("tap",function(){
				var lanren=document.getElementById("lanren");
				var btn=document.getElementById("audio-btn");
				var loading=document.getElementById("loading");
				var classname=this.getAttribute("class");
				var quesid=this.getAttribute("quesid");
				var type=this.getAttribute("type");
				console.log(quesid);
				console.log(classname);
				if(classname.indexOf("pausing")>0){
					this.setAttribute("class","");
					this.setAttribute("class","btn  playing");
                    document.getElementById("sy-click").src="../../public/Homework/images/sy.gif";
				    question_play(listendata);
				  }
				else if(classname.indexOf("playing")>0){
					mp.clear();
					this.setAttribute("class","");
				  this.setAttribute("class","btn  pausing");
				  document.getElementById("sy-click").src="../../public/Homework/images/sy.png";
					//document.getElementById("sy-click").setAttribute("src","../../public/Homework/images/sy.png");
				}
				else {
					loading.style.display="";
					lanren.style.display="none";
				    var url="getQuestiontts";
					mui.ajax(url,
						{
						data:{
							quesid:quesid,
							type:type,
							ran:Math.random()
						},
						dataType:'json',//服务器返回json格式数据
						type:'post',//HTTP请求类型
						timeout:10000,//超时时间设置为10秒；
						async:false,
						success:function(data){
							loading.style.display="none";
							lanren.style.display="";
							//将数据载入数组中避免在同一个试题下音频的重复请求
							mui.each(data,function(k1,v1){
								var temp={};
								temp.tts_mp3=v1.tts_mp3;
								temp.tts_stoptime=v1.tts_stoptime;
								listendata.push(temp);
							});
							btn.setAttribute("class","");
							console.log("aaaaa");
							btn.setAttribute("class","btn playing");
							document.getElementById("sy-click").src="../../public/Homework/images/sy.gif";
						    //btn.style.backgroundImage="url(../../public/Homework/images/play-to-pause-faster.gif)";
								//addClass(btn,"play");
							mp.index = 0;
							mp.stemindex = 0;
							mp.queinitindex = 0;
							mp.questionindex = 0;
							mp.childstemindex = 0;
						    mp.childinitstemindex = 0;
						    mp.url = "";
						    mp.repeat = 1; //默认播放次数
						    mp.curpeat = 1;//当前播放到第几次
						    mp.url = "";
							question_play(listendata);
						},
						error:function(xhr,type,errorThrown){
							//异常处理；
							return errinfo;
						}
					});
				 }
			});
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errinfo;
		}
});
}


//监听右滑事件，若侧滑菜单未显示，右滑要显示菜单；
//window.addEventListener("swiperight", function(e) {
//	//默认滑动角度在-45度到45度之间，都会触发右滑菜单，为避免误操作，可自定义限制滑动角度；
//	if (Math.abs(e.detail.angle) < 4) {
//		openMenu();
//	}
//});

//试题父页面下一题切换
mui("div#content").on("swipeleft","div#parent",function(e){
	e.preventDefault();
    stopaudio();
    document.getElementById("content").innerHTML="";
    var addindex=this.getAttribute("quescount");
    var stemid=parseInt(this.getAttribute("stemid"));
    addindex=parseInt(addindex)-1;
    var index=parseInt(document.getElementById("quesindex").innerHTML)+addindex;
    if(index==quescount){
    	viewApi.go("#account");
    	return false;
    }else{
    	getRespose('getQuestion',"网络请求出错请等会儿再试试",index,0,stemid);
    }
});
//试题父页面上一题切换
mui("div#content").on("swiperight","div#parent",function(e){
	stopaudio();
	e.preventDefault();
	
	var addindex=this.getAttribute("quescount");
	var stemid=parseInt(this.getAttribute("stemid"));
    addindex=parseInt(addindex)+1;
	var index=parseInt(document.getElementById("quesindex").innerHTML)-addindex;
	if(index<0){
		mui.toast("已经到了第一页");
		return false;
	}
	document.getElementById("content").innerHTML="";
	getRespose('getQuestion',"网络请求出错请等会儿再试试",index,0,stemid);

  	
});



//组合部分试题子页面下一题滑动
mui("div#content").on("swipeleft","div#children",function(e){
    //表示进入下一个
    e.preventDefault();
    var stemid=parseInt(document.getElementById("parent").getAttribute("stemid"));
    var index=parseInt(document.getElementById("cquesindex").innerHTML);
    if(index==quescount){
    	viewApi.go("#account");
    	return false;
    }
    getRespose('getQuestion',"网络请求出错请等会儿再试试",index,1,stemid);
});

//组合部分试题子页面下一题滑动
mui("div#content").on("swiperight","div#children",function(e){
	//表示返回上一个
	e.preventDefault();
	var stemid=parseInt(document.getElementById("parent").getAttribute("stemid"));
	var index=parseInt(document.getElementById("cquesindex").innerHTML)-2;
	if(index<0){
		mui.toast("已经到了第一页");
		return false;
	}else{
		getRespose('getQuestion',"网络请求出错请等会儿再试试",index,1,stemid);
	}
    

});

//页面启动的时候进行websql的插入
function getSummaryData(id){
	var url="getSummaryData";
	mui.ajax(url,
		{
		data:{
			homeworkid:id,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			//服务器返回然后进行h5的本地存储
			mui.each(data,function(k,v){
				i=(k+1);
				if(issubmit=='0'){
					if(v.answer!=undefined&&v.answer!=''&&v.answer!='null'){
		        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
		        	}else{
		        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
		        	}	
				}else{
					if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
		        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
		        }else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
		        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
		        	}else{
		        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" style="background-color:gray;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
		        	}
				}
			});
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errinfo;
		}
	});
	return summaryhtml;
}

//单词测试的启动进行websql的插入
//页面启动的时候进行websql的插入
function getWordtestSummaryData(id){
	//var url="getWordtestSummaryData";
	var url="getSummary";
	var summaryhtml="";
	mui.ajax(url,
		{
		data:{
			studentId:studentid,
			classId:classid,
			homeworkid:id,
			iserror:iserror,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			if(data.wordtest.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p>单词测试<p>";
				mui.each(data.wordtest,function(k,v){
					i=(k+1);
					if(issubmit=='0'){
						if(v.answer!=undefined&&v.answer!=''&&v.answer!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="wordtest" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="wordtest" class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}	
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="wordtest" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        }else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="wordtest" class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="wordtest" class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});	
			}
			if(data.examsquiz.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p>听力训练<p>";
				mui.each(data.examsquiz,function(k,v){
					i=(k+1);
					if(issubmit=='0'){
						if(v.iscorrect=='1'||v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz"><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}	
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        }else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz" style=""><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}
					}
				});
			}
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errorThrown;
		}
	});
	return summaryhtml;
}



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




//小题播放
function question_play(quettsdata){
	var smallquetts = '';
	if(mp.questionindex < quettsdata.length){
		smallquetts = quettsdata[mp.questionindex];
		playurl = getmp3url(smallquetts.tts_mp3);
		mp.play(playurl);
		$("#jplayer").bind($.jPlayer.event.ended,function(event){
			mp.questionindex = mp.questionindex +1;
			mp3_progress = setTimeout(function(){
			question_play(quettsdata);
			},parseInt(smallquetts.tts_stoptime)*1000);
		});
	}else{
		mp.index = 0;
		mp.stemindex = 0;
		mp.queinitindex = 0;
		mp.questionindex = 0;
		mp.childstemindex = 0;
	    mp.childinitstemindex = 0;
	    mp.url = "";
	    mp.repeat = 1; //默认播放次数
	    mp.curpeat = 1;//当前播放到第几次
	    mp.url = "";
		document.getElementById("audio-btn").setAttribute("class","");
	  	document.getElementById("audio-btn").setAttribute("class","play-pause  pausing");
	    //document.getElementById("audio-btn").style.backgroundImage="url(../../public/Homework/images/pause-to-play-faster.gif)";
	}
}


//停止MP3播放
function stopaudio(){
	try{
		console.log(mp);
		mp.clear();
		mp.index = 0;
		mp.stemindex = 0;
		mp.queinitindex = 0;
		mp.questionindex = 0;
		mp.childstemindex = 0;
	    mp.childinitstemindex = 0;
	    mp.url = "";
	    mp.repeat = 1; //默认播放次数
	    mp.curpeat = 1;//当前播放到第几次
	    mp.url = "";
	}catch(e){
		console.log(e);
	}

}


function addClass(obj, cls){
    var obj_class = obj.className;//获取 class 内容.
    var blank = (obj_class != '') ? ' ' : '';//判断获取到的 class 是否为空, 如果不为空在前面加个'空格'.
    var added = obj_class + blank + cls;//组合原来的 class 和需要添加的 class.
    obj.className = added;//替换原来的 class.
}

function removeClass(obj, cls){
    var obj_class = ' '+obj.className+' ';//获取 class 内容, 并在首尾各加一个空格. ex) 'abc        bcd' -> ' abc        bcd '
    obj_class = obj_class.replace(/(\s+)/gi, ' ');//将多余的空字符替换成一个空格. ex) ' abc        bcd ' -> ' abc bcd '
    var removed = obj_class.replace(' '+cls+' ', ' ');//在原来的 class 替换掉首尾加了空格的 class. ex) ' abc bcd ' -> 'bcd '
    removed = removed.replace(/(^\s+)|(\s+$)/g, '');//去掉首尾空格. ex) 'bcd ' -> 'bcd'
    obj.className = removed;//替换原来的 class.
}

function hasClass(obj, cls){
    var obj_class = obj.className;//获取 class 内容.
    var obj_class_lst = obj_class.split(/\s+/);//通过split空字符将cls转换成数组.
    var x = 0;
    for(x in obj_class_lst) {
        if(obj_class_lst[x] == cls) {//循环数组, 判断是否包含cls
            return true;
        }
    }
    return false;
}


function getWordTestRespose(studentid,classid,homeworkid,index){
	var respose="";
	listendata=[];
	var url="getWordTest";
	mui.ajax(url,
		{
		data:{
			homeworkid:homeworkid,
			studentid:studentid,
			classid:classid,
			index:index,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			setWordtestContent(data[0]);
			document.getElementById("quesindex").innerHTML=(parseInt(index)+1);
			//进行单击事件的注入选择试题和判断试题的js注入
			if((data[0].typeid=='1'||data[0].typeid=='0')&issubmit=='0'){
				mui('ul.xuanze li').on('tap', 'a', function() {
					var ind=parseInt(document.getElementById("quesindex").innerHTML);
					var items=this.parentNode.parentNode.getElementsByTagName("li");
					for(var i=0;i<items.length;i++){
						items[i].getElementsByTagName("div")[1].style.background="white";
					}
					this.getElementsByTagName("div")[1].style.background="#2bc8a0";
					var questionid=this.getAttribute("quesid");
					var homeworkid=this.getAttribute("homeworkid");
					var wordid=this.getAttribute("wordid");
					var useranswer=this.getAttribute("itemflag");
					var typeid=this.getAttribute("type");
					//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
					var url="setUserWordtestanswer";
					mui.ajax(url,
						{
						data:{
							questionid:questionid,
							useranswer:useranswer,
							studentid:studentid,
							classid:classid,
							homeworkid:homeworkid,
							wordid:wordid,
							typeid:typeid,
							ran:Math.random()
						},
						dataType:'json',//服务器返回json格式数据
						type:'post',//HTTP请求类型
						timeout:10000,//超时时间设置为10秒；
						async:true,
						success:function(data){
							//服务器返回然后进行h5的本地存储
							try{
								websqlInsterDataToTable("wordtest",(parseInt(ind)-1),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
							}catch(e){
								console.log("不支持websql");
							}
						},
						error:function(xhr,type,errorThrown){
							//异常处理；
							console.log(errorThrown);
							return errorThrown;
						}
					});
	      	    });
			}
			//填空试题的js进行注入事件
			if(data[0].typeid=='2'&issubmit=='0'){
				mui("div.flex-container").on('tap',"a.worditems",function(e){
					var ind=parseInt(document.getElementById("quesindex").innerHTML);
					var text=this.getElementsByTagName("span")[0].innerHTML;
					//alert(document.getElementsByClassName("mui-active")[0].innerHTML=="");
					if(document.getElementsByClassName("mui-active").length>0&&!hasClass(this,"actived")&&document.getElementsByClassName("mui-active")[0].innerHTML==""){
						document.getElementsByClassName("mui-active")[0].innerHTML=text;
						this.style.backgroundColor="#efeff4";
						addClass(this,"actived");
						//向下移动
						var items=document.getElementsByClassName("mui-control-item");
						for(var i=0;i<items.length;i++){
							if(hasClass(items[i],"mui-active")&&i<items.length-1){
								removeClass(items[i],"mui-active");
								addClass(items[i+1],"mui-active");
								addClass(this,"items"+i);
								i=items.length;
							}else{
								if(i==(items.length-1)){
									removeClass(items[i],"mui-active");
									addClass(this,"items"+i);
								}
							}
						}
						var questionid=this.getAttribute("quesid");
						var wordid=this.getAttribute("wordid");
						var homeworkid=this.getAttribute("homeworkid");
						var typeid=this.getAttribute("typeid");
						//答案
						var userans=document.getElementById("segmentedControl").getElementsByTagName("a");
						var ans="";
						for(var i=0;i<userans.length;i++){
							var value=userans[i].innerText;
							if(i<(userans.length-1)){
								ans=ans+value+",";
							}else{
								ans=ans+value;
							}
						}
						//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
						var url="setUserWordtestanswer";
						mui.ajax(url,
							{
							data:{
								questionid:questionid,
								useranswer:ans,
								studentid:studentid,
								classid:classid,
								homeworkid:homeworkid,
								wordid:wordid,
								typeid:typeid,
								ran:Math.random()
							},
							dataType:'json',//服务器返回json格式数据
							type:'post',//HTTP请求类型
							timeout:10000,//超时时间设置为10秒；
							async:true,
							success:function(data){
								//服务器返回然后进行h5的本地存储
								try{
									websqlInsterDataToTable("wordtest",(ind),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
								}catch(e){
									console.log("不支持websql");
								}
								
							},
							error:function(xhr,type,errorThrown){
								//异常处理；
								return errorThrown;
							}
						});
					}
				});
				
				mui("#segmentedControl").on("tap", "a", function() {
					var ind=parseInt(document.getElementById("quesindex").innerHTML);
					var key=this.getAttribute("key");
					var classname="items"+key;
					if(key!='-1'){
						this.innerHTML="";
						document.getElementsByClassName(classname)[0].style.backgroundColor="";
						removeClass(document.getElementsByClassName(classname)[0],"actived");
						removeClass(document.getElementsByClassName(classname)[0],classname);
						//document.getElementById(key).setAttribute("id",'-1');
						var questionid=this.getAttribute("quesid");
						var wordid=this.getAttribute("wordid");
						var homeworkid=this.getAttribute("homeworkid");
						var typeid=this.getAttribute("typeid");
						//答案
						var userans=document.getElementById("segmentedControl").getElementsByTagName("a");
						var ans="";
						for(var i=0;i<userans.length;i++){
							var value=userans[i].innerText;
							if(i<(userans.length-1)){
								ans=ans+value+",";
							}else{
								ans=ans+value;
							}
						}
						//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
						var url="setUserWordtestanswer";
						mui.ajax(url,
							{
							data:{
								questionid:questionid,
								useranswer:ans,
								studentid:studentid,
								classid:classid,
								homeworkid:homeworkid,
								wordid:wordid,
								typeid:typeid,
								ran:Math.random()
							},
							dataType:'json',//服务器返回json格式数据
							type:'post',//HTTP请求类型
							timeout:10000,//超时时间设置为10秒；
							async:true,
							success:function(data){
								//服务器返回然后进行h5的本地存储
								try{
									websqlInsterDataToTable("wordtest",(ind),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
								}catch(e){
									console.log("不支持websql");
								}
							},
							error:function(xhr,type,errorThrown){
								//异常处理；
								return errorThrown;
							}
						});
					}
				});
				
				//发声
				document.getElementById("audio-btn").addEventListener("tap",function(){
					var mp3=this.getAttribute("mp3");
					//alert(mp3);
					document.getElementById("sy-click").src="../../public/Homework/images/sy.gif";
					console.log(word_mp3_url+mp3);
					mp.play(word_mp3_url+mp3);
					
				});
			}else if(data[0].typeid=='2'){
				//发声
				document.getElementById("audio-btn").addEventListener("tap",function(){
					var mp3=this.getAttribute("mp3");
					alert(mp3);
					//document.getElementById("sy-click").src="../../public/Homework/images/sy.gif";
					//mp.play(word_mp3_url+mp3);
					
				});
			}
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errorThrown;
		}
	});
}


function setWordtestContent(data){
	var questionhtml="";
	if(data.typeid=='0'){
		questionhtml=questionhtml+'<div class="title" style="margin-left:10px;font-size: 1.0em;font-family: times;color:black;text-align:center;vertival-align:middle;">';
		questionhtml=questionhtml+'<strong style="color:black;display:block;padding-top:20px;">'+data.explains+'</strong>';
		questionhtml=questionhtml+'</div>';

		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: white;">';
		//第一个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="A" quesid="'+data.id+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="A" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			if(data.quesans=="A"){
				if("A"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("A"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("A"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}

		questionhtml=questionhtml+'<span itemflag="A" style="height:30px; line-height:30px; display:block; color:black; text-align:center">A</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_a;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		//第二个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="B" quesid="'+data.id+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="B" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			if(data.quesans=="B"){
				if("B"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("B"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("B"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}
		questionhtml=questionhtml+'<span itemflag="B" style="height:30px; line-height:30px; display:block; color:black; text-align:center">B</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_b;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		//第三个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="C" quesid="'+data.id+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="C" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			
			if(data.quesans=="C"){
				if("C"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("C"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("C"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}

		questionhtml=questionhtml+'<span itemflag="C" style="height:30px; line-height:30px; display:block; color:black; text-align:center">C</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_c;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		//看是否提交数据
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;"><font>正确答案是'+data.quesans;
				if(data.userans==''||data.userans==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.userans;
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h6>班级数据</h6>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color:white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.erroranswer+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
		}
	}else if(data.typeid==1){
		questionhtml=questionhtml+'<div class="title" style="margin-left:10px;font-size: 1.0em;font-family: times;color:black;text-align:center;vertival-align:middle;">';
		questionhtml=questionhtml+'<strong style="color:black;display:block;padding-top:20px;">'+data.word+'</strong>';
		questionhtml=questionhtml+'</div>';

		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: white;">';
		//第一个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="A" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="A" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			if(data.quesans=="A"){
				if("A"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("A"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("A"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}

		questionhtml=questionhtml+'<span itemflag="A" style="height:30px; line-height:30px; display:block; color:black; text-align:center">A</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_a;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		//第二个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="B" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="B" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			if(data.quesans=="B"){
				if("B"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("B"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("B"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}
		questionhtml=questionhtml+'<span itemflag="B" style="height:30px; line-height:30px; display:block; color:black; text-align:center">B</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_b;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		//第三个选项的添加
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media"  itemflag="C" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<a href="javascript:;"  itemflag="C" quesid="'+data.id;
	    questionhtml=questionhtml+'" wordid="'+data.wordid+'" homeworkid="'+data.homeworkid;
	    questionhtml=questionhtml+'" type="'+data.typeid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		//正确答案和错误答案的展示
		if(issubmit=='1'){
			if(data.quesans=="C"){
				if("A"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}else{
				if("C"==data.userans){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:darkgoldenrod; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
				}
			}
		}else{
			if("C"==data.userans){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
		}

		questionhtml=questionhtml+'<span itemflag="C" style="height:30px; line-height:30px; display:block; color:black; text-align:center">C</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;';
		//选项的内容
		questionhtml=questionhtml+data.option_c;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		//看是否提交数据
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;"><font>正确答案是'+data.quesans;
				if(data.userans==''||data.userans==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.userans;
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.erroranswer+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
		}
	}else{
		
		questionhtml=questionhtml+'<div id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy" id="audio-btn" mp3="'+data.mp3+'"><img id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div>';
//		questionhtml=questionhtml+'<div class="title" style="margin-top:20px;margin-left:10px;font-size: 1.0em;font-family: times;">';
//		questionhtml=questionhtml+data.explains;
//		questionhtml=questionhtml+'</div>';
       var uw=data.userwords;
       questionhtml=questionhtml+'<center style="margin-top:10px;font-size:1.2em;">'+data.explains+'</center>';
       questionhtml=questionhtml+'<div class="" style="margin-top:20px;"><div id="segmentedControl" class="mui-segmented-control" style="border:none;">';
       //进行单词的展示
        if(issubmit=='1'){
	       	mui.each(data.words,function(k,v){
		       	questionhtml=questionhtml+'<a class="mui-control-item" style="width:30px;height:30px;border-bottom:1px solid;border-left:none;"></a>';
		    });
        }else{
	       	mui.each(data.words,function(k,v){
	       		var datav="";
	       		var ind=-1;
	       		if(data.userwords[k]==undefined){
	       			datav='';
	       			ind=-1;
	       			
	       		}else{
	       			datav=data.userwords[k];
	       			ind=k;
	       		}
	       		if(k==0){
	       			questionhtml=questionhtml+'<a class="mui-control-item mui-active" style="width:30px;height:40px;border-bottom:1px solid;border-left:none;" key="'+k+'" quesid="'+data.id+'" wordid="'+data.wordid+'" typeid="2" homeworkid="'+data.homeworkid+'">'+datav+'</a>';
	       		}else{
	       			questionhtml=questionhtml+'<a class="mui-control-item" style="width:30px;height:40px;border-bottom:1px solid;border-left:none;" key="'+k+'" quesid="'+data.id+'" wordid="'+data.wordid+'" typeid="2" homeworkid="'+data.homeworkid+'">'+datav+'</a>';	    	
	       		}
		    });
        }
	    questionhtml=questionhtml+'</div></div>';
		
		//进行单词选项的展示
		questionhtml=questionhtml+'<div class="mui-content-padded" style="background:white;margin-top:30px;">';
		questionhtml=questionhtml+'<div class="flex-container">';
		mui.each(data.items,function(key,value){
			var ind=data.userwords.indexOf(value);
			if(ind>-1){
				data.userwords[ind]="-1";
				questionhtml=questionhtml+'<a  class="worditems items'+ind+'" quesid="'+data.id+'" wordid="'+data.wordid+'" typeid="2" homeworkid="'+data.homeworkid+'" style="background-color: rgb(239, 239, 244);"><span class="mui-icon" style="font-family: inherit;"><font>'+value+'</font></span></a>';
			}else{
				questionhtml=questionhtml+'<a  class="worditems items" quesid="'+data.id+'" wordid="'+data.wordid+'" typeid="2" homeworkid="'+data.homeworkid+'"><span class="mui-icon" style="font-family: inherit;"><font>'+value+'</font></span></a>';
			}
			
		});
		questionhtml=questionhtml+'</div></div>';
		//看是否提交数据
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;"><font>正确答案是'+data.quesans;
				if(data.userans==''||data.userans==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案";
					var datatemp=data.userans.split(",");
					mui.each(datatemp,function(kk,vv){
						questionhtml=questionhtml+vv;
					});
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'作答人数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'易错单词';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">';
			var datatemp=data.answer.erroranswer.split(",");
			mui.each(datatemp,function(kk,vv){
				questionhtml=questionhtml+vv;
			});
			questionhtml=questionhtml+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
		}
	}
	document.getElementById("content").innerHTML=questionhtml;
}



