
mui.init({
		swipeBack:false //启用右滑关闭功能
	});

mui.back=function(){
    //返回到教师的列表页面
    //mp.clear();
    stopaudio();
    //调用方法隐藏加载的状态
    try{
       UXinJSInterface.hideProgress(); 
    }catch(e){

    }
    popTheController();
}

$(function(){
    $("#jplayer").jPlayer({
        swfPath: '/public/Homework/js',
        wmode: "window",
        supplied: "mp3",
        preload: "none",
        volume: "1"
    });
    mp = new myplay();
    mp.clear();
});


var mask = mui.createMask();//callback为用户点击蒙版时自动执行的回调；
//只有在学生做作业才进行时间的读秒
if(issubmit=='0'&&type=='0'){
	var c=parseInt("{$time}");
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
//这里需要取出课文的数据


//初始化单页view
var viewApi = mui('#app').view({
	defaultPage: '#setting'
});
//初始化单页的区域滚动
mui('.mui-scroll-wrapper').scroll();
var view = viewApi.view;


	//遮罩展示
	
	//处理view的后退与webview后退
	var oldBack = $.back;
	mui.back = function() {
		if (viewApi.canBack()) { //如果view可以后退，则执行view的后退
			viewApi.back();
		} else { //执行webview后退
			//oldBack();
            stopaudio();
            popTheController();
		}
	};


	//监听页面切换事件方案1,通过view元素监听所有页面切换事件，目前提供pageBeforeShow|pageShow|pageBeforeBack|pageBack四种事件(before事件为动画开始前触发)
	//第一个参数为事件名称，第二个参数为事件回调，其中e.detail.page为当前页面的html对象
	view.addEventListener('pageBeforeShow', function(e) {
		console.log(e.detail.page.id + ' beforeShow');
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
			//查询本地数据库中的数据
			//websqlGetAllData("questions",homeworkid,studentid,classid,setSummary);
			var summaryhtml=getPreviewSummaryData();
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
		console.log(e.detail.page.id + ' show');
		////作业提交的监听
		document.getElementById("submithomework").addEventListener('tap',function(){
			var url="../public/stupublish";
			mask.show();//显示遮罩
			mui.ajax(url,
				{
				data:{
					studentid:studentid,
					classid:classid,
					homeworkid:hwid,
					paper_id:homeworkid,
					tms:tms,
					time:c,
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
					mui.openWindow("finish?homeworkid={$homeworkid}&batchid={$batchid}&studentid={$studentid}&classid={$classid}&type=0");
				},
				error:function(xhr,type,errorThrown){
					//异常处理；
					mask.close();
					mui.toast("网络问题，请稍等一会儿在提交");
					return errorThrown;
				}
			});
		});
	});
	view.addEventListener('pageBeforeBack', function(e) {
		console.log(e.detail.page.id + ' beforeBack');
	});
	view.addEventListener('pageBack', function(e) {
		console.log(e.detail.page.id + ' back');
	});
	TouchSlide({
		slideCell:"#leftTabBox",
		effect:"left",
		defaultIndex:parseInt(index),
		startFun:function(i,c){

		},
		endFun:function(i,c){
			var pageindex=i;

			var muiindex="item"+pageindex;
            //判断是不是组合试题
            try{
            	var cquesindex=document.getElementsByClassName("mui-slider-item").get(pageindex).getElementsByClassName("cquesindex");
                document.getElementsByClassName("mui-slider-item").get(pageindex).getElementsByClassName("cquesindex").get(0).innerHTML=(pageindex+1);
            }catch(e){
            	//表示不是组合试题
            	document.getElementById("quesindex").innerHTML=(pageindex+1);
            }
            var cocntent="";
            if((pageindex>=(wscount)+(wccount)+(wrcount)+(wacount)+(tacount))&&(eqcount)>0){
            	if(pageindex==(wscount)+(wccount)+(wrcount)+(wacount)+(tacount)){
					
            		$.pop.show("ttip","","ttip_wz","下面将开始听力训练");
				}
				$("#type").text("听力训练");
            	//进行听力训练的刷新
             	var id=eqt[0].id;
             	document.getElementById("quesnumcount").innerHTML=((eqcount));
             	document.getElementById("quesindex").innerHTML=(pageindex+1-((wscount)+(wccount)+(wrcount)+(wacount)+(tacount)));
             	if(page[i]==undefined){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex),1,id,pageindex,1);
             	}
             	if(page[i-1]==undefined&&i>((wscount)+(wccount)+(wrcount)+(wacount)+(tacount))&&i<=((quescount)-1)){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)-1,1,id,pageindex-1,1);
             	}
             	if(page[i+1]==undefined&&i<((quescount)-1)&&i>=((wscount)+(wccount)+(wrcount)+(wacount)+(tacount))){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)+1,1,id,pageindex+1,1);
             	}
             	console.log(page_tts);
            }else if((pageindex>=(wscount)+(wccount)+(wrcount)+(wacount))&&(tacount)>0){
            	if(pageindex==(wscount)+(wccount)+(wrcount)+(wacount)){
					
            		$.pop.show("ttip","","ttip_wz","下面将开始课文跟读作业");
				}
				$("#type").text("课文跟读");
            	//课文的刷新
            	var taindex=pageindex-((wscount)+(wccount)+(wrcount)+(wacount));

                var curtaindex=0;
                var nextaindex=0;
                var pretaindex=0;
                var taindexcur=0;
                var taindexpre=0;
                var taindexnxt=0;
                for(var j=0;j<tat.length;j++){
                    console.log(taindex);
                    if((taindex+1)<=parseInt(tat[j].quescount)){
                        if((taindex+1)==parseInt(tat[j].quescount)){
                            if(tat[j+1]!=undefined){
                                taindexnxt=0;
                                nextaindex=j+1;
                                if(j>0){
                                    if(parseInt(tat[j].quescount)!=1){
                                        taindexpre=taindex-1;
                                        pretaindex=j;
                                    }else{
                                         taindexpre=tat[j-1].quescount;
                                        pretaindex=j-1;
                                    }      
                                }else{
                                    pretaindex=j-1;
                                }
                            }else{
                                nextaindex=j;
                                taindexnxt="";
                                if(j>0){
                                    if(parseInt(tat[j].quescount)!=1){
                                        taindexpre=taindex-1;
                                        pretaindex=j;
                                    }else{
                                        taindexpre=tat[j-1].quescount;
                                        pretaindex=j-1;
                                    }      
                                }else{
                                    pretaindex=j-1;
                                }
                            }
                            
                        }else if((taindex+1)<parseInt(tat[j].quescount)){
                            nextaindex=j;
                            pretaindex=j;
                            taindexpre=taindex-1;
                            taindexnxt=taindex+1;
                            // if(){


                            // }else{

                            // }
                        }
                        curtaindex=j;
                        break;
                    }else{
                        
                        taindex=taindex-parseInt(tat[j].quescount);
                    }
                }
            	var id=tat[curtaindex].id;
            	document.getElementById("quesnumcount").innerHTML=((tacount));
             	//document.getElementById("quesindex").innerHTML=(pageindex+1-((wscount)+(wccount)+(wrcount)+(wacount)));
                document.getElementById("quesindex").innerHTML=pageindex+1;
             	if(page[pageindex]==undefined){

             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(taindexcur),1,tat[curtaindex].id,pageindex,3,0);
             	}
             	if(page[pageindex-1]==undefined&&pageindex>((wscount)+(wccount)+(wrcount)+(wacount))&&i<=((wscount)+(wccount)+(wrcount)+(wacount)+(tacount)-1)){
             		id=tat[pretaindex].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(taindexpre),1,tat[pretaindex].id,pageindex-1,3,0);
             	}
             	if(page[pageindex+1]==undefined&&pageindex<((wscount)+(wccount)+(wrcount)+(wacount)+(tacount)-1)&&i>=((wscount)+(wccount)+(wrcount)+(wacount))){
             		id=tat[nextaindex].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(taindexnxt),1,tat[nextaindex].id,pageindex+1,3,0);
             	}
                document.getElementById("quesindex").innerHTML=(pageindex-((wscount)+(wccount)+(wrcount)+(wacount))+1);
             	console.log(page_tts);
            }else if((pageindex>=(wacount)+(wscount)+(wccount))&&(wrcount)>0){
            	if(pageindex==(wscount)+(wccount)+(wacount)){
					
            		$.pop.show("ttip","","ttip_wz","下面将开始英汉互译作业");
				}
				$("#type").text("英汉互译");
            	//单词测试的刷新
            	var wtindex=pageindex-((wacount)+(wscount)+(wccount));
            	var id=wrt[wtindex].id;
            	var typeid=wrt[wtindex].typeid;
            	document.getElementById("quesnumcount").innerHTML=((wrcount));
             	document.getElementById("quesindex").innerHTML=(pageindex+1-(wacount)-(wscount)-(wccount));
             	if(page[i]==undefined){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex),1,id,pageindex,0,typeid);
             	}
             	if(page[i-1]==undefined&&i>(wccount)+(wscount)+(wacount)&&i<=((wscount)+(wccount)+(wrcount)+(wacount)-1)){
             		id=wrt[wtindex-1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)-1,1,id,pageindex-1,0,typeid);
             	}
             	if(page[i+1]==undefined&&i<((wscount)+(wccount)+(wrcount)+(wacount)-1)&&i>=(wccount)+(wrcount)+(wacount)){
             		id=wrt[wtindex+1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)+1,1,id,pageindex+1,0,typeid);
             	}
             	console.log(page_tts);

            }else if((pageindex>=(wacount)+(wscount))&&(wccount)>0){
            	if(pageindex==(wacount)){
					
            		$.pop.show("ttip","","ttip_wz","下面将开始听音选词作业");
				}
				$("#type").text("听音选词");
            	//单词测试的刷新
            	var wtindex=pageindex-((wacount)+(wscount));
            	var id=wct[wtindex].id;
            	var typeid=wct[wtindex].typeid;
            	document.getElementById("quesnumcount").innerHTML=((wccount));
             	document.getElementById("quesindex").innerHTML=(pageindex+1-(wacount)-(wscount));
             	if(page[i]==undefined){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex),1,id,pageindex,0,typeid);
             	}
             	if(page[i-1]==undefined&&i>(wacount)+(wscount)&&i<=((wscount)+(wccount)+(wacount)-1)){
             		id=wct[wtindex-1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)-1,1,id,pageindex-1,0,typeid);
             	}
             	if(page[i+1]==undefined&&i<((wscount)+(wccount)+(wacount)-1)&&i>=(wacount)+(wscount)){
             		id=wct[wtindex+1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)+1,1,id,pageindex+1,0,typeid);
             	}
             	console.log(page_tts);

            }else if((pageindex>=wacount)&&wscount>0){
            	if(pageindex==wacount){
					
            		$.pop.show("ttip","","ttip_wz","下面将开始单词拼写作业");
				}
				$("#type").text("单词拼写");
            	//单词测试的刷新
            	var wtindex=pageindex-((wacount));
            	var id=wst[wtindex].id;
            	var typeid=wst[wtindex].typeid;
            	document.getElementById("quesnumcount").innerHTML=((wscount));
             	document.getElementById("quesindex").innerHTML=(pageindex+1-(wacount));
             	if(page[i]==undefined){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex),1,id,pageindex,0,typeid);
             	}
             	if(page[i-1]==undefined&&i>wacount&&i<=(wscount+wacount-1)){
             		id=wst[wtindex-1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)-1,1,id,pageindex-1,0,typeid);
             	}
             	if(page[i+1]==undefined&&i<((wscount)+(wacount)-1)&&i>=(wacount)){
             		id=wst[wtindex+1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)+1,1,id,pageindex+1,0,typeid);
             	}
             	console.log(page_tts);

            }else if(wacount>0){
            	//单词朗读的刷新
            	var waindex=pageindex;
            	if(pageindex==0){	
            		$.pop.show("ttip","","ttip_wz","下面将开始单词跟读作业");
				}
				$("#type").text("单词跟读");
            	var id=wat[waindex].id;
            	document.getElementById("quesnumcount").innerHTML=((wacount));
             	document.getElementById("quesindex").innerHTML=(pageindex+1);
             	if(page[i]==undefined){
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex),1,id,pageindex,2,0);
             	}
             	if(page[i-1]==undefined&&i>0&&i<=((wacount)-1)){
             		id=wat[waindex-1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)-1,1,id,pageindex-1,2,0);
             	}
             	if(page[i+1]==undefined&&i<((wacount)-1)&&i>=0){
             		id=wat[waindex+1].id;
             		getPreviewRespose('../Index/getHomeworkPreviewQuestion',"网络请求出错请等会儿再试试",(pageindex)+1,1,id,pageindex+1,2,0);
             	}
             	console.log(page_tts);

            }
            try{
                   hwstopaudio(i); 
               }catch(e){

               }
            
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
				bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth-20+"px";
                // bd.getElementsByClassName("parent")[0].style.marginLeft = "10px";
                // bd.getElementsByClassName("parent")[0].style.marginRight = "5px";
			}catch(e){
				//bd.getElementsByClassName("parent")[0].style.width = document.body.clientWidth+"px";
			}

		}	//1.0	null	每次切换效果结束时执行函数，用于处理特殊情况或创建更多效果。用法 endFun:function(i,c){ }； 其中i为当前分页，c为总页数	详解>>

	});



	//试题汇总
	mui("div#accountcontent").on("tap","a.quessummary",function(){
		var index=this.getElementsByTagName("span")[0].innerHTML;

		var url=this.getAttribute("loc");
		showLoading();
        //url="preview?username="+username+"&ks_code="+ks_code+"&ispre=1&num=0&homeworkid="+homeworkid+"&source=0&homework="+encodeURI(JSON.stringify(homework))+"&sso="+sso;
        url=url+"&username="+username+"&ks_code="+ks_code+"&ispre=1&num=0&homeworkid=&source=0&sso="+sso+"&quescount="+quescount;
        url=window.location.protocol+"://"+document.domain+"/Homework/Mobhw/"+url;
        window.location.href=url;
        // //创建form表单
        // var temp_form = document.createElement("form");
        // temp_form.action = url;
        // //如需打开新窗口，form的target属性要设置为'_blank'
        // temp_form.target = "_self";
        // temp_form.method = "post";
        // temp_form.style.display = "none";
        // //添加参数
        // var opt = document.createElement("textarea");
        // opt.name = "homework";
        // opt.value = encodeURI((homework));
        // temp_form.appendChild(opt);
        // document.body.appendChild(temp_form);
        // //提交数据
        // temp_form.submit();
		// url=url+'&username='+username+'&ks_code='+ks_code+'&sso='+sso+'&wt='+encodeURI(wt)+'&eq='+encodeURI(eq)+'&ispre=1&num=0&homeworkid=&source=0&wa='+encodeURI(wa)+'&ta='+encodeURI(ta);
		// mui.openWindow(url);
	});


	//发布作业
	document.getElementById("publish").addEventListener("click",function(){
		stopaudio();
		teacher_publish(this,homework);
	});


//作业发布问题
function teacher_publish(obj,homework){
    $(obj).attr("disbaled",true);
  	mask.show();
    $.post("../../Pubinterface/Index/publish_homework",{homework:((homework)),source:"1",ran:Math.random()},function(data){
         mask.close();
        //判断是不是平板的链接
        var device=0;
        try{
            var device=getDeviceType();
        }catch(e){
            //默认手机端的
            device=1;
        }
        var url="";
        if(device==3){
         //平板接口
          url=window.location.protocol+"//"+tqms+"/tqms/pad/homework/toEnPublishForStuPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
        }else{
         //手机接口
          url=window.location.protocol+"//"+tqms+"/tqms/mobile/homework/toEnPublishForStuPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);
        }
		//数据处理
		$.get("../Index/publishData",{homeworkid:data.homeworkid,ran:Math.random()})
        //url="http://{$tqms}/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+encodeURIComponent(data.name);

        openProgressController(url);
        //windows.location.href="http://tqms.youjiaotong.com/tqms/mobile/homework/toEnPublishPage.action?username="+data.username+"&paper_id="+data.homeworkid+"&ks_code="+data.ks_code+"&paper_name="+data.name;
    });
}
