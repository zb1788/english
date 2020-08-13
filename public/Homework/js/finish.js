mui.init();
mui.back=function(){
	if(type=='1'){
		popTheController();
	}else if(type == '2'){
		window.location.href="homeworkstudent?homeworkid="+homeworkid+"&classid="+classid+"&batchid="+batchid;
	}else{
		UXinJSInterface.setOnlinWorkHaveFinished(batchid);
		popTheController();
	}

}

//进行作业的数据的显示
var homeworkurl="getStudentSituation";
mui.ajax(homeworkurl,
	{
	data:{
		homeworkid:homeworkid,
		studentid:studentid,
		classid:classid,
		ran:Math.random()
	},
	dataType:'json',//服务器返回json格式数据
	type:'get',//HTTP请求类型
	timeout:10000,//超时时间设置为10秒；
	async:true,
	success:function(data){
		var homeworkcount=0;
		var homeworksubmitcount=0;
		var wscount=0;
		var wccount=0;
		var wrcount=0;
		var wacount=0;
		var tacount=0;
		var eqcount=0;
		var hwscore=0;
		var userhwscore=0;
		//单词跟读
		if(data.wa.length>0){
			var accrnum=0;
			var averagescore=0;
			var content="";
			mui.each(data.wa,function(k,v){
				wacount=wacount+1;
				averagescore=averagescore+parseFloat(v.score);
				if(v.isdo=='1'&&parseInt(v.score)>50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&parseInt(v.score)<=50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
			});

			byId("wacount").innerHTML=data.wa.length;
			byId("wausercount").innerHTML=accrnum;
			byId("waaver").innerHTML=wacount==0?0:parseFloat(averagescore/wacount).toFixed(1);
			byId("wordalound").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordalound").style.display="";
		}else{
			byId("wordalound").style.display="none";
		}
		//单词测试
		if(data.ws.length>0){
			var accrnum=0;
			var content="";
			mui.each(data.ws,function(k,v){
				hwscore=hwscore+parseInt(v.quesscore);
				wscount=wscount+1;
				if(v.score=='1'){
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&v.score=='0'){
					emptynum=emptynum+1;
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					errornum=errornum+1;
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
          			emptynum=emptynum+1;
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
				if(v.isdo=='1'){
					homeworksubmitcount=homeworksubmitcount+1;
				}
			});
			var accrate=0;
			if(count==0){
				accrate=0;
			}else{
				accrate=Math.round(accrnum*100/wscount);
			}
			homeworkcount=homeworkcount+wscount;
			byId("wscount").innerHTML=wscount;
			byId("wsusercount").innerHTML=accrnum;
			byId("wsrate").innerHTML=accrate+"%";
			byId("wordspell").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordspell").style.display="";
		}else{
			byId("wordspell").style.display="none";
		}

		if(data.wc.length>0){
			var accrnum=0;
			var content="";
			mui.each(data.wc,function(k,v){
				hwscore=hwscore+parseInt(v.quesscore);
				wccount=wccount+1;
				if(v.score=='1'){
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&v.score=='0'){
					emptynum=emptynum+1;
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					errornum=errornum+1;
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
          			emptynum=emptynum+1;
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
				if(v.isdo=='1'){
					homeworksubmitcount=homeworksubmitcount+1;
				}
			});
			var accrate=0;
			if(count==0){
				accrate=0;
			}else{
				accrate=Math.round(accrnum*100/wccount);
			}
			homeworkcount=homeworkcount+wccount;
			byId("wccount").innerHTML=wccount;
			byId("wcusercount").innerHTML=accrnum;
			byId("wcrate").innerHTML=accrate+"%";
			byId("wordchoose").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordchoose").style.display="";
		}else{
			byId("wordchoose").style.display="none";
		}

		if(data.wr.length>0){
			var accrnum=0;
			var content="";
			mui.each(data.wr,function(k,v){
				hwscore=hwscore+parseInt(v.quesscore);
				wrcount=wrcount+1;
				if(v.score=='1'){
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&v.score=='0'){
					emptynum=emptynum+1;
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					errornum=errornum+1;
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
          			emptynum=emptynum+1;
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
				if(v.isdo=='1'){
					homeworksubmitcount=homeworksubmitcount+1;
				}
			});
			var accrate=0;
			if(count==0){
				accrate=0;
			}else{
				accrate=Math.round(accrnum*100/wrcount);
			}
			homeworkcount=homeworkcount+wrcount;
			byId("wrcount").innerHTML=wrcount;
			byId("wrusercount").innerHTML=accrnum;
			byId("wrrate").innerHTML=accrate+"%";
			byId("wordrate").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordrate").style.display="";
		}else{
			byId("wordrate").style.display="none";
		}
		//课文跟读
		if(data.ta.length>0){
			var accrnum=0;
			var averagescore=0;
			var content="";
			mui.each(data.ta,function(k,v){
				tacount=tacount+1;
				averagescore=averagescore+parseFloat(v.score);
				if(v.isdo=='1'&&parseInt(v.score)>50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&parseInt(v.score)<=50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
			});
			byId("tacount").innerHTML=data.ta.length;
			byId("tausercount").innerHTML=accrnum;
			byId("taaver").innerHTML=tacount==0?0:parseFloat(averagescore/tacount).toFixed(1);
			byId("textalound").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("textalound").style.display="";
		}else{
			byId("textalound").style.display="none";
		}

		//听力训练
		if(data.eq.length>0){
			var count=0;
			var accrnum=0;
			var content="";
			//临时变量存储
			var questemp="";
			var counttemp=0;
			mui.each(data.eq,function(k,v){
				count=count+1;
				hwscore=hwscore+parseFloat(v.quesscore);

				if(questemp!=v.id){
					questemp=v.id;
					counttemp=counttemp+1;
				}
				if(parseInt(v.score)>=1){
					userhwscore=userhwscore+parseInt(v.score)*parseFloat(v.quesscore);
					accrnum=accrnum+1;
					//console.log("ss="+accrnum);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&& parseInt(v.score) == 0){
					emptynum=emptynum+1;
					userhwscore=userhwscore+parseInt(v.score);
					errornum=errornum+1;
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
					emptynum=emptynum+1;
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wrcount)+parseInt(wscount)+parseInt(wccount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
				
			});
			var accrate=0;
			if(count==0){
				accrate=0;
			}else{
				accrate=Math.round(accrnum*100/count);
			}
			homeworkcount=homeworkcount+count;
			byId("eqcount").innerHTML=count;
			byId("equsercount").innerHTML=accrnum;
			byId("eqrate").innerHTML=accrate+"%";
			byId("examsquiz").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("examsquiz").style.display="";
		}else{
			byId("examsquiz").style.display="none";
		}
		//展示总分数以及用户的分数
		//byId("userhomeworkscore").innerHTML=userhwscore;
		//byId("homeworkscore").innerHTML=hwscore;
		byId("user").style.display="";
		mui("#content").on('click','a.quesnum',function(){
			var url=this.getAttribute("loc");
			url=url+"&homeworkid="+homeworkid+"&issubmit=1&studentId="+studentid+"&classId="+classid+"&batchid="+batchid+"&type="+type+"&hwid=&starttime=1472039061&batckid=&callbackURL=";
			mui.openWindow(url);
		})

		//解析判断一下这个人是否有错题
		document.getElementById("errquesid").addEventListener('tap',function(){
			if(emptynum==0){
				mui.toast("您太棒了，本次作业全部答对了！");
			}else{
				var url="";
				url="examsquiz?homeworkid="+homeworkid+"&issubmit=1&iserror=1&type=0&studentId="+studentid+"&classId="+classid+"&batchid="+batchid;
				mui.openWindow(url);
			}
		});
		document.getElementById("allquesid").addEventListener('tap',function(){
			var url=this.getAttribute("loc");
			//alert(url);
			url="examsquiz?homeworkid="+homeworkid+"&issubmit=1&iserror=0&type=0&studentId="+studentid+"&classId="+classid+"&batchid="+batchid;
			mui.openWindow(url);
			
		});
	},
	error:function(xhr,type,errorThrown){
		//异常处理；
		return errorThrown;
	}
});


var byId = function(id) {
	return document.getElementById(id);
};
