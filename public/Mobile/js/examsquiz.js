mui.init({
	swipeBack:false //启用右滑关闭功能
});
mui.back=function(){
  //表示学生进去
  if(issubmit==0){
    mui.openWindow("../Listen/show_exams_list?unitid="+unitid);
  }else if(issubmit==1){
    //返回到教师的列表页面
    mui.openWindow('finish?unitid='+unitid+'&homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid+'&examsid='+examid);
  }
}

var mask = mui.createMask();
//只有在学生做作业才进行时间的读秒
if(issubmit=='0'&&type=='0'){
	var c=parseInt(time);
	timedCount();
	function timedCount(){
		var min=0,secs=0;
		var value="";
	    min = parseInt(c / 60);// 分钟数
	    if(min<10){
	    	value=value+"0"+min;
	    }else{
	    	value=value+min;
	    }
	    secs = c % 60;
	    if(secs<10){
	    	value=value+":0"+secs;
	    }else{
	    	value=value+":"+secs;
	    }
	    document.getElementById('time').innerHTML =value;
	    c=c+1  ;
	}
	setInterval("timedCount()",1000)
}


//初始化单页view
var viewApi = mui('#app').view({
	defaultPage: '#setting'
});
//初始化单页的区域滚动
mui('.mui-scroll-wrapper').scroll();
var view = viewApi.view;

(function($) {
	//学生做作业的时候
	if(issubmit=='1'&&type=="0"){
		//做完作业查看作业的时候需要进行的操作
		document.getElementById("datika").setAttribute("href","javascript:void(0);");
		document.getElementById("datika").addEventListener("click",function(){
			var url='finish?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid;
			mui.openWindow(url);
		})
	}else if(issubmit=='1'&&type=="1"){
		//做完作业查看作业的时候需要进行的操作
		document.getElementById("datika").setAttribute("href","javascript:void(0);");
		document.getElementById("datika").addEventListener("click",function(){
			popTheController();
			// var url='feedback?homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid;
			// mui.openWindow(url);
		})
	}else{
		var oldBack = $.back;
		$.back = function() {
			if (viewApi.canBack()) { //如果view可以后退，则执行view的后退
				viewApi.back();
				if(pageindex==(parseInt(wacount)+parseInt(wtcount)+parseInt(tacount)+parseInt(eqcount))){
					var prev=document.getElementById("prev");
					mui.trigger(prev,'click');
				}
			} else { //执行webview后退
				oldBack();
			}
		};
		//答题卡的展示
		view.addEventListener('pageBeforeShow', function(e) {
			stopaudio();
	        document.getElementById("accountcontent").innerHTML="";
			if(e.detail.page.id=="account"){
				document.getElementById("datika").style.display="none";
				summaryhtml="";
				if(issubmit=='0'&&type=='0'){
					document.getElementById("timeshow").style.display="none";
					document.getElementsByTagName("nav")[0].style.display="";
				}else{
					document.getElementById("suwt").style.marginBottom="0px";
				}
				var summaryhtml=getWordtestSummaryData(examid);
				document.getElementById("accountcontent").innerHTML=summaryhtml;
				document.getElementById("accountcontent").style.marginTop="0px";
			}else{
				document.getElementById("accountcontent").innerHTML="";
				if(issubmit=='0'&&type=='0'){
					document.getElementsByTagName("nav")[0].style.display="none";
					document.getElementById("timeshow").style.display="";
				}
				document.getElementById("datika").style.display="";
			}
		});
		view.addEventListener('pageShow', function(e) {
			////作业提交的监听
			document.getElementById("submithomework").addEventListener('tap',function(){
				var url="stupublish";
				mask.show();//显示遮罩
				mui.ajax(url,
					{
					data:{
						studentid:studentid,
						classid:classid,
						homeworkid:homeworkid,
						paper_id:examid,
						tms:tms,
						time:c,
						unitid:unitid,
						starttime:starttime,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:true,
					success:function(data){
						//服务器返回然后进行h5的本地存储
						mask.close();
						mui.openWindow('finish?unitid='+unitid+'&examsid='+examid+'&homeworkid='+homeworkid+'&batchid='+batchid+'&studentid='+studentid+'&classid='+classid+'&type=0');
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						mask.close();
						mui.toast("网络问题，请稍等一会儿在提交");
					}
				});
			});
		});
	}
	//分页滑动
	TouchSlide({
		slideCell:"#leftTabBox",
		effect:"left",
		defaultIndex:parseInt(index),
		prevCell : '#prev',
		nextCell : '#next',
		pnLoop:false,
		startFun:function(i,c){
			pageindex=i;
			//翻页的时候停止播放音频如果是单体的话 就停止 组合试题的话不停止
			if(i==(c-1)&&issubmit=='0'){
				viewApi.go("#account");
			}
			try{
				document.getElementsByClassName("ques")[i].style.marginLeft="0px";
				document.getElementsByClassName("ques")[i].style.paddingLeft="0px";
			}catch(e){

			}
		},
		endFun:function(i,c){
			var quesindex=c;
			if(issubmit!=0){
				quesindex=quesindex+1;
			}
			if(i<(quesindex-1)){
				var pageshow=page[i];
				//进行每页的宽度的点至
				var muiindex="item"+pageindex;
	            //判断是不是组合试题
	            try{
	            		var cquesindex=document.getElementsByClassName("mui-slider-item").get(pageindex).getElementsByClassName("cquesindex");
	                document.getElementsByClassName("mui-slider-item").get(pageindex).getElementsByClassName("cquesindex").get(0).innerHTML=(pageindex+1);
	            }catch(e){
	            	//表示不是组合试题
	            	//document.getElementById("quesindex").innerHTML=(pageindex+1);
	            }
	            var cocntent="";
	            //网页进行加载的时候进行预加载将当前页面以及前一夜以及后面的一页进行加载

				if(pageindex>=(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount))){
	             	document.getElementById("quesnumcount").innerHTML=(parseInt(eqcount));
	             	document.getElementById("quesindex").innerHTML=(pageindex+1-parseInt(wtcount)-parseInt(wacount)-parseInt(tacount));
	             	//记录学生做作业的频率以及教师的布置作业的类型
	             	//getLog(4);
	         		//首先加载当前页面的数据同步加载
	         		if(pageshow==undefined){
	         			getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),1,0,parseInt(pageindex),false);
	         		}
	         		//加载当前页面的前一页面的数据
	         		if(pageindex>(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount))&&pageindex<(parseInt(quescount)-1)){
	         			pageindex=pageindex-1;
	             		if(page[pageindex]==undefined){
	             			getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),1,0,parseInt(pageindex),true);
	             		}
					     //加载当前页面的下一页的数据异步加载
						pageindex=pageindex+2;
						if(page[pageindex]==undefined){
							getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),1,0,parseInt(pageindex),true);
						}
	         		}else if(pageindex>(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount))&&pageindex==(parseInt(quescount)-1)){
	         			//只加载在前一个页面
	         			pageindex=pageindex-1;
	             		if(page[pageindex]==undefined){
	             			getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),1,0,parseInt(pageindex),true);
	             		}
	         		}else if(pageindex==(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount))&&pageindex<(parseInt(quescount)-1)){
	         			//只加载在后一个页面
	         			pageindex=pageindex+1;
	             		if(page[pageindex]==undefined){
	             			getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),1,0,parseInt(pageindex),true);
	             		}
	         		}else if(pageindex==(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount))&&pageindex==(parseInt(quescount)-1)){
	         			//只加载当前页
	         		}


	             }else if(pageindex>=(parseInt(wtcount)+parseInt(wacount))){
	             	pageindex=i;
	             	document.getElementById("quesnum").style.display="";
	             	document.getElementById("quesindex").innerHTML=(pageindex+1-parseInt(wacount)-parseInt(wtcount));
	             	document.getElementById("quesnumcount").innerHTML=(parseInt(tacount));
	             	//getLog(3);
	             	if(page[pageindex]==undefined){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),3,0,pageindex,false);
	             	}
	             	//加载前一页的数据
	             	if(page[pageindex-1]==undefined&&pageindex>(parseInt(wtcount)+parseInt(wacount))){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex-1),3,0,(pageindex-1),true);
	             	}
	             	//加载下一页的数据
	             	if(page[pageindex+1]==undefined&&pageindex<(parseInt(wtcount)+parseInt(wacount)+parseInt(tacount)-1)){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex+1),3,0,(pageindex+1),true);
	             	}

	            }else if(pageindex>=(parseInt(wacount))){
	             	document.getElementById("quesindex").innerHTML=(pageindex+1-parseInt(wacount));
	             	document.getElementById("quesnumcount").innerHTML=(parseInt(wtcount));
	             	if(page[pageindex]==undefined){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),0,0,pageindex,false);
	             	}

	             	//加载前一页的数据
	             	if(page[pageindex-1]==undefined&&pageindex>(parseInt(wacount))){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex-1),0,0,(pageindex-1),true);
	             	}
	             	//加载下一页的数据
	             	if(page[pageindex+1]==undefined&&pageindex<(parseInt(wtcount)+parseInt(wacount)-1)){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex+1),0,0,(pageindex+1),true);
	             	}
	            }else{
	             	pageindex=i;
	             	document.getElementById("quesnum").style.display="";
	             	document.getElementById("quesindex").innerHTML=(pageindex+1);
	             	document.getElementById("quesnumcount").innerHTML=(parseInt(wacount));
	             	console.log(page);
	             	if(page[pageindex]==undefined){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex),2,0,pageindex,false);
	             	}

	             	//加载前一页的数据
	             	if(page[pageindex-1]==undefined&&pageindex>0){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex-1),2,0,(pageindex-1),true);
	             	}
	             	//加载下一页的数据
	             	if(page[pageindex+1]==undefined&&pageindex<(parseInt(wacount)-1)){
	             		getRespose('getHomeworkQuestion',"网络请求出错请等会儿再试试",parseInt(pageindex+1),2,0,(pageindex+1),true);
	             	}
	            }
				
				var stopflag=hwstopaudio(i);
				//进行音频的自动播放
				if(stopflag==1&&startflag){
					var muiindex="item"+i;
					try{
						var playbtn=document.getElementById(muiindex).getElementsByClassName("lanren")[0].getElementsByTagName("a")[0];
						startflag=true;
						//防止没有加载完成
						setTimeout(function(){
							playbtn.click();
						},500);
					}catch(e){

					}
				}
				
				//滑动区域的处理
	            var bd = document.getElementById("leftTabBox").getElementsByClassName("bd")[0].getElementsByClassName("ques")[i];
				bd.style.width = document.body.clientWidth+"px";
				try{
					bd.getElementsByClassName("parent")[0].style.minHeight = document.body.clientHeight+"px";
				}catch(e){
					bd.style.height = document.body.clientHeight+"px";
				}

				bd.style.marginLeft = "-100px";
				if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
	            try{
					bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
				}catch(e){
	//					bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
				}
				var muiindex="item"+i;
				var obj = document.getElementById(muiindex);//获取当前带滚动条的div对象
				console.log(obj.scrollTop);
				obj.scrollTop=20; //设置滚动距离
				console.log(obj.scrollTop);
				


			}
		}

	});
	//答题器试题跳转
	mui("div#accountcontent").on("tap","a.quessummary",function(){
		var index=this.getElementsByTagName("span")[0].innerHTML;
		var url=this.getAttribute("loc");
		if(issubmit==1&&type==1){
			url=url+"&homeworkid="+homeworkid+"&examsid="+examid+"&issubmit="+issubmit+"&time="+c+"&iserror="+iserror+"&starttime="+starttime+"&batchid="+batchid+"&hwid="+hwid+"&tms="+tms+"&studentId="+studentid+"&classId="+classid+"&type=2&callbackURL="+callbackURL+"&isOverdue="+isOverdue+"&unitid="+unitid;
			window.location.href=url;
		}else{
			url=url+"&homeworkid="+homeworkid+"&examsid="+examid+"&issubmit="+issubmit+"&time="+c+"&iserror="+iserror+"&starttime="+starttime+"&batchid="+batchid+"&hwid="+hwid+"&tms="+tms+"&studentId="+studentid+"&classId="+classid+"&type="+type+"&callbackURL="+callbackURL+"&isOverdue="+isOverdue+"&unitid="+unitid;
			mui.openWindow(url);
		}
	});
})(mui);

//app进行录音的函数调用
function getVoicePath(path){
	//在jplayer这个标签上加上一个属性表示单词的内容
	var filename=path;
	var url="../Public/getTestScore";
	mask.show();
	mui.ajax(url,
		{
		data:{
			homeworkid:homeworkid,
			studentid:studentid,
			classid:classid,
			wordreadid:wordreadid,
			content:tncontent,
			filename:filename,
			textid:textid,
			type:type,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:50000,
		async:true,
		success:function(data){
			//显示分数
			mask.close();
			//显示星星的信息
			var content="";
			var startint=parseInt(data.result.score);
			if(startint>0&&startint<40){
				content=content+'<i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
			}else if(startint>=40&&startint<75){
				content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i>';
			}else if(startint>=75&&startint<=100){
				content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
			}else{
				content=content+'<i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
			}
			content=content+'<br><strong class="score">';
			if(data.result.score==null){
				content=content+'0</strong>分';
			}else{
				content=content+Math.round(data.result.score)+'</strong>分';
			}
			document.getElementsByClassName("ques")[page].getElementsByClassName("rig-org")[0].innerHTML=content;
//					//用户的录音显示
			document.getElementsByClassName("ques")[page].getElementsByClassName("uservoice")[0].setAttribute("mp3",data.uservoice);
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			mask.close();
			mui.toast("您提交的太频繁了，请稍等一会儿在提交");
		}
	});
}

//判断函数是否存在
function isExitsFunction(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

//进行日志的记录的函数js动态添加
function getLog(type){
	var oHead = document.getElementsByTagName('HEAD').item(0);
    var oScript= document.createElement("script");
    oScript.type = "text/javascript";
    oScript.src="../Public/setLog?type="+type;
    oHead.appendChild(oScript);
}

