mui.init();
mui.back=function(){
	 mui.openWindow("../Listen/show_exams_list?unitid="+unitid);
}

//进行作业的数据的显示
var homeworkurl="getStudentSituation";
mui.ajax(homeworkurl,
	{
	data:{
		homeworkid:homeworkid,
		studentid:studentid,
		classid:classid,
		examid:examid,
		ran:Math.random()
	},
	dataType:'json',//服务器返回json格式数据
	type:'post',//HTTP请求类型
	timeout:10000,//超时时间设置为10秒；
	async:true,
	success:function(data){
		var homeworkcount=0;
		var homeworksubmitcount=0;
		var wtcount=0;
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
				averagescore=averagescore+parseInt(v.score);
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
			byId("waaver").innerHTML=accrnum==0?0:Math.floor(averagescore/accrnum);
			byId("wordalound").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordalound").style.display="";
		}else{
			byId("wordalound").style.display="none";
		}
		//单词测试

		if(data.wt.length>0){
			var accrnum=0;
			var content="";
			mui.each(data.wt,function(k,v){
				hwscore=hwscore+parseInt(v.quesscore);
				wtcount=wtcount+1;
				if(v.score=='1'){
					accrnum=accrnum+parseInt(v.score);
					userhwscore=userhwscore+parseInt(v.score);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo='1'&&v.score=='0'){
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
				accrate=Math.round(accrnum*100/wtcount);
			}
			homeworkcount=homeworkcount+wtcount;
			byId("wtcount").innerHTML=wtcount;
			byId("wtusercount").innerHTML=accrnum;
			byId("wtrate").innerHTML=accrate+"%";
			byId("wordtest").getElementsByClassName("flex-container")[0].innerHTML=content;
			byId("wordtest").style.display="";
		}else{
			byId("wordtest").style.display="none";
		}
		//课文跟读
		if(data.ta.length>0){
			var accrnum=0;
			var averagescore=0;
			var content="";
			mui.each(data.ta,function(k,v){
				tacount=tacount+1;
				averagescore=averagescore+parseInt(v.score);
				if(v.isdo=='1'&&parseInt(v.score)>50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wtcount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&parseInt(v.score)<=50){
					accrnum=accrnum+parseInt(v.isdo);
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wtcount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(wtcount)+k)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}
			});
			byId("tacount").innerHTML=data.ta.length;
			byId("tausercount").innerHTML=accrnum;
			byId("taaver").innerHTML=accrnum==0?0:Math.floor(averagescore/accrnum);
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
				if(v.score=='1'){
					userhwscore=userhwscore+parseInt(v.score)*parseFloat(v.quesscore);
					accrnum=accrnum+parseInt(v.score);

					content=content+'<a style="background-color:#2bc8a0;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wtcount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else if(v.isdo=='1'&&v.score=='0'){
					emptynum=emptynum+1;
					userhwscore=userhwscore+parseInt(v.score);
					errornum=errornum+1;
					content=content+'<a style="background-color:#FE5A59;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wtcount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
				}else{
					emptynum=emptynum+1;
					content=content+'<a style="background-color:gainsboro;" class="quesnum" loc="examsquiz?index='+(parseInt(wacount)+parseInt(tacount)+parseInt(wtcount)+counttemp-1)+'"><span class="mui-icon" style="color: white;line-height: 20px;">'+(k+1)+'</span></a>';
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
		byId("userhomeworkscore").innerHTML=userhwscore;
		byId("homeworkscore").innerHTML=hwscore;
		byId("user").style.display="";
		mui("#content").on('click','a.quesnum',function(){
			var url=this.getAttribute("loc");
			url=url+"&homeworkid="+homeworkid+"&issubmit=1&studentId="+studentid+"&classId="+classid+"&batchid="+batchid+"&type=0&hwid=&starttime=1472039061&batckid=&callbackURL=&examsid="+examid+"&unitid="+unitid;
			mui.openWindow(url);
		})

		//解析判断一下这个人是否有错题
		document.getElementById("errquesid").addEventListener('tap',function(){
			if(emptynum==0){
				mui.toast("您太棒了，本次作业全部答对了！");
			}else{
				var url="";
				url="examsquiz?homeworkid="+homeworkid+"&examsid="+examid+"&issubmit=1&iserror=1&type=0&studentId="+studentid+"&classId="+classid+"&batchid="+batchid+"&unitid="+unitid;
				mui.openWindow(url);
			}
		});
		document.getElementById("allquesid").addEventListener('tap',function(){
			var url=this.getAttribute("loc");
			//alert(url);
			var wordtestcount=document.getElementById("wordtest").getElementsByTagName("a").length;
			if(wordtestcount>0){
				url="examsquiz?homeworkid="+homeworkid+"&examsid="+examid+"&issubmit=1&iserror=0&type=0&studentId="+studentid+"&classId="+classid+"&batchid="+batchid+"&unitid="+unitid;
				mui.openWindow(url);
			}else{
				url="examsquiz?homeworkid="+homeworkid+"&examsid="+examid+"&issubmit=1&iserror=0&type=0&studentId="+studentid+"&classId="+classid+"&batchid="+batchid+"&unitid="+unitid;
				mui.openWindow(url);
			}
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
