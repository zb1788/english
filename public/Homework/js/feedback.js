mui.init();
mui.back=function(){
	popTheController();
}

$("#feedbackback").click(function(){
	popTheController();
});

$("#studentclass").empty();
mui.ajax("../Public/getHomeworkClass",
	{
	data:{
		homeworkid:homeworkid,
		tqms:tqms,
		ran:Math.random()
	},
	dataType:'json',//服务器返回json格式数据
	type:'get',//HTTP请求类型
	timeout:10000,//超时时间设置为10秒；
	async:true,
	success:function(data){
		var content="";
		$.each(data,function(key,value){
			if(classid == value.id){
				content=content+'<li class="on" classid="'+value.id+'" classstunum="'+value.studentcount+'" style="cursor:pointer;">'+value.name+'</li>';
			}
			else{
				content=content+'<li classid="'+value.id+'" classstunum="'+value.studentcount+'" style="cursor:pointer;">'+value.name+'</li>';
			}
		});
		byId("studentclass").innerHTML=content;		
		getClass();
	},
	error:function(xhr,type,errorThrown){
		//异常处理；
		//return errorThrown;
		mui.toast("网络连接出错。。。。。。");
	}
});


//进行作业合计
getHomeworkStudentScore();




function getHomeworkStudentScore(){
	var homeworkurl="../Public/getHomeworkClassStudent";
	var submitflag = false;

	mui.ajax(homeworkurl,
		{
		data:{
			homeworkid:homeworkid,
			classid:classid,
			batchid:batchid,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:true,
		success:function(data){
			//$("#homeworkaveragetime").text(data.homeworkaveragetime);
			$("#homeworkmaxscore").text(data.homeworkmaxscore);
			//console.log(data.homeworksubmitnum);
			if(data.homeworksubmitnum == "0"){
				    $("#inforesult").hide();
				    $("#quescounts").html("(0道)");
					$("#nfk").show();
			}
			else{
				//进行作业的数据的显示
				$("#nfk").hide();
				$("#inforesult").show();
				
				getClassSituation();
			}
			
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			//return errorThrown;
			mui.toast("网络连接出错。。。。。。");
		}
	});
}



function getClass(){
	var homeworkurl="../Public/getClass";
	mui.ajax(homeworkurl,
		{
		data:{
			homeworkid:homeworkid,
			classid:classid,
			batchid:batchid,

			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:true,
		success:function(data){
			//alert(data.submitCount);
			$("#classname").text(data.className);
			$("#classstucount").text(data.classstucount);
			$("#submitstucount").text(data.submitCount);
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			//return errorThrown;
			mui.toast("网络连接出错。。。。。。");
		}
	});

}




var wacount=0,wscount=0,wccount=0,wrcount=0,tacount=0,eqcount=0;
function getClassSituation(){
	wacount=0,wscount=0,wccount=0,wrcount=0,tacount=0,eqcount=0;
	var homeworkurl="getClassSituation";
	mui.ajax(homeworkurl,
		{
		data:{
			homeworkid:homeworkid,
			classid:classid,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:true,
		success:function(data){
			var homeworkcount=0;
			var homeworksubmitcount=0;
			//单词跟读
			if(data.wa.length>0){
				var count=0;
				var accrnum=0;
				//var content='<ul class="mui-table-view" style=" width: 100%;">';
				var content='';
				//<li><a class="ball_blu">1<span>100%</span></a></li>
				mui.each(data.wa,function(k,v){
					wacount=wacount+1;
					content=content+'<li class="quesnum" loc="examsquiz?index='+(k)+'">';
					if(v.isdo=='0'){
						//<span class="mui-icon" style="width: 50;height: 40px;width: 40px;text-align: center;vertical-align: middle;border-radius: 50%;background-color: #FE5A59;line-height: 40px;">
						content=content+'<a class="ball_red">'+(k+1);
					}else{
						if(parseInt(v.score)<50){
							content=content+'<a class="ball_red">'+(k+1);
						}else{
							content=content+'<a class="ball_blu">'+(k+1);
						}

					}
					if(v.score=='0'){
						content=content+'<span>0分</span></a></li>';
					}else{
						content=content+'<span>'+parseInt(v.score)+'分</span></a></li>';
					}

				});
				//content=content+'</ul>';
				byId("wordalound").getElementsByTagName("ul")[0].innerHTML=content;
				byId("wordalound").style.display="";
			}else{
				byId("wordalound").style.display="none";
			}
			//单词测试<ul class="mui-table-view">
			if(data.ws.length>0){
				var count=0;
				var accrnum=0;
				//var content='<ul class="mui-table-view" style=" width: 100%;">';
				var content='';
				mui.each(data.ws,function(k,v){
					wscount=wscount+1;
					content=content+'<li class="quesnum" loc="examsquiz?index='+(wacount+k)+'">';
					if(v.isdo=='0'){
						content=content+'<a class="ball_red">'+(k+1)+'<span>0%</span></a></li>';
					}else{
						var accrate=(Math.round(parseInt(v.score)*100/parseInt(v.isdo)));
						if(accrate<=50){
							content=content+'<a class="ball_red" >'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}else{
							content=content+'<a class="ball_blu">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}

					}

				});
				//content=content+'</ul>';
				byId("wordspell").getElementsByTagName("ul")[0].innerHTML=content;
				byId("wordspell").style.display="";
			}else{
	//					document.getElementsByClassName("cd-timeline-block")[1].style.display="none";
				byId("wordspell").style.display="none";
			}

			if(data.wc.length>0){
				var count=0;
				var accrnum=0;
				//var content='<ul class="mui-table-view" style=" width: 100%;">';
				var content='';
				mui.each(data.wc,function(k,v){
					wccount=wccount+1;
					content=content+'<li class="quesnum" loc="examsquiz?index='+(wacount+wscount+k)+'">';
					if(v.isdo=='0'){
						content=content+'<a class="ball_red" >'+(k+1)+'<span>0%</span></a></li>';
					}else{
						var accrate=(Math.round(parseInt(v.score)*100/parseInt(v.isdo)));
						if(accrate<=50){
							content=content+'<a class="ball_red">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}else{
							content=content+'<a class="ball_blu">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}

					}

				});
				//content=content+'</ul>';
				byId("wordchoose").getElementsByTagName("ul")[0].innerHTML=content;
				byId("wordchoose").style.display="";
			}else{
	//					document.getElementsByClassName("cd-timeline-block")[1].style.display="none";
				byId("wordchoose").style.display="none";
			}

			if(data.wr.length>0){
				var count=0;
				var accrnum=0;
				//var content='<ul class="mui-table-view" style=" width: 100%;">';
				var content='';
				mui.each(data.wr,function(k,v){
					wrcount=wrcount+1;
					content=content+'<li class="quesnum" loc="examsquiz?index='+(wacount+wscount+wccount+k)+'">';
					if(v.isdo=='0'){
						content=content+'<a class="ball_red" >'+(k+1)+'<span>0%</span></a></li>';
					}else{
						var accrate=(Math.round(parseInt(v.score)*100/parseInt(v.isdo)));
						if(accrate<=50){
							content=content+'<a class="ball_red">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}else{
							content=content+'<a class="ball_blu">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}

					}

				});
				//content=content+'</ul>';
				byId("wordrate").getElementsByTagName("ul")[0].innerHTML=content;
				byId("wordrate").style.display="";
			}else{
	//					document.getElementsByClassName("cd-timeline-block")[1].style.display="none";
				byId("wordrate").style.display="none";
			}
			//课文跟读
			if(data.ta.length>0){
				var count=0;
				var accrnum=0;
				//var content='<ul class="mui-table-view" style=" width: 100%;">';
				var content='';
				mui.each(data.ta,function(k,v){
					tacount=tacount+1;
					content=content+'<li class="quesnum" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+k)+'">';
					if(v.isdo=='0'){
						//<span class="mui-icon" style="width: 50;height: 40px;width: 40px;text-align: center;vertical-align: middle;border-radius: 50%;background-color: #FE5A59;line-height: 40px;">
						content=content+'<a class="ball_red">'+(k+1);
					}else{
						if(parseInt(v.score)<50){
							content=content+'<a class="ball_red">'+(k+1);
						}else{
							content=content+'<a class="ball_blu">'+(k+1);
						}

					}
					if(v.score=='0'){
						content=content+'<span>0分</span></a></li>';
					}else{
						content=content+'<span>'+parseInt(v.score)+'分</span></a></li>';
					}
				});
				//content=content+'</ul>';
				byId("textalound").getElementsByTagName("ul")[0].innerHTML=content;
				byId("textalound").style.display="";
			}else{
	//					document.getElementsByClassName("cd-timeline-block")[2].style.display="none";
				byId("textalound").style.display="none";
			}

			//听力训练
			if(data.eq.length>0){
				//alert("fsdfasdfasd");
				var count=0;
				var accrnum=0;
				var questemp="";
				var counttemp=0;
				var content='';
				mui.each(data.eq,function(k,v){
					eqcount=eqcount+1;
					//console.log(v);
					if(questemp!=v.id){
						questemp=v.id;
						counttemp=counttemp+1;
					}
					content=content+'<li  class="quesnum" loc="examsquiz?index='+(wacount+wscount+wccount+wrcount+tacount+counttemp-1)+'">';
					if(v.isdo=='0'){
						content=content+'<a class="ball_red"  >'+(k+1)+'<span>0%</span><a></li>';
					}else{
						var accrate=(Math.round(parseInt(v.score)*100/parseInt(v.isdo)));
						if(accrate<=50){
							content=content+'<a class="ball_red">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}else{
							content=content+'<a class="ball_blu">'+(k+1)+'<span>'+(Math.round(parseInt(v.score)*100/parseInt(v.isdo)))+'%</span></a></li>';
						}

					}

				});
				//content=content+'</ul>';
				byId("examsquiz").getElementsByTagName("ul")[0].innerHTML=content;
				byId("examsquiz").style.display="";
				//document.getElementById("content").style.width=window.screen.availWidth;
			}else{
	//					document.getElementsByClassName("cd-timeline-block")[3].style.display="none";
				byId("examsquiz").style.display="none";
			}

			//试题总数
			byId("quescounts").innerHTML="("+(wacount+wscount+wccount+wrcount+tacount+eqcount)+"道)";
			


			mui("#content").on('click','li.quesnum',function(){
				var url=this.getAttribute("loc");
				url=url+"&homeworkid="+homeworkid+"&issubmit=1&type=3&studentId="+studentid+"&classId="+classid+"&batchid="+batchid;
				window.location.href=url;
			});



		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			//return errorThrown;
			mui.toast("网络连接出错。。。。。。");
		}
	});

}




var byId = function(id) {
	return document.getElementById(id);
};




