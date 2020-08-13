mui.init();
mui.back=function(){
	popTheController();
}

$("#content").empty();
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
			content=content+'<li classid="'+value.id+'" classstunum="'+value.studentcount+'">'+value.name+'</li>';
		});
		byId("studentclass").innerHTML=content;		
	},
	error:function(xhr,type,errorThrown){
		//异常处理；
		//return errorThrown;
		mui.toast("网络连接出错。。。。。。");
	}
});
getClassSituation();

function getClassSituation(){
	document.getElementById("homeyescount").innerHTML="";
	document.getElementById("homeyescount").style.display="none";
	document.getElementById("homeyes").style.display="none";
	document.getElementById("homeyes").innerHTML="";
	document.getElementById("homenocount").innerHTML="none";
	document.getElementById("homenocount").style.display="none";
	document.getElementById("homeno").style.display="none";
	document.getElementById("homeno").innerHTML="";
	var homeworkurl="../Public/getHomeworkClassStudent";
	mui.ajax(homeworkurl,
		{
		data:{
			homeworkid:homeworkid,
			classid:classid,
			batchid:batchid,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'get',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:true,
		success:function(data){
			var studentcount=0;
			var studentnocount=0;
			var studentcontent="";
			var studentnocontent="";
			classid=data.classId;
			mui.each(data.students,function(k,v){
				if(v.issubmit==0){
					studentnocount=studentnocount+1;
					studentnocontent=studentnocontent+'<span><a class="bor-name dp" href="finish?type=2&homeworkid='+homeworkid+'&studentid='+v.id+'&classid='+classid+'&batchid='+batchid+'">'+v.name+'</a></span>';
				}else{
					studentcount=studentcount+1;
					studentcontent=studentcontent+'<span><a class="bor-name zq" href="finish?type=2&homeworkid='+homeworkid+'&studentid='+v.id+'&classid='+classid+'&batchid='+batchid+'">'+v.name+'</a></span>';
				}
			});
			if(studentcount>0){
				byId("homeyescount").innerHTML="已提交："+studentcount+"人";
				byId("homeyescount").style.display="";
				byId("homeyes").style.display="";
				byId("homeyes").innerHTML=studentcontent;
			}
			if(studentnocount>0){
				byId("homenocount").innerHTML="未提交："+studentnocount+"人";
				byId("homenocount").style.display="";
				byId("homeno").style.display="";
				byId("homeno").innerHTML=studentnocontent;
			}
			try{
				$("#homeworkaveragetime")=data.homeworkaveragetime;
			}catch(e){
				
			}
			try{
				$("#homeworkmaxscore")=data.homeworkmaxscore;
			}catch(e){
				
			}
			
			mui("#content").on('click','span.quesnum',function(){
				var url=this.getAttribute("loc");
				
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