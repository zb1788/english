
function videoPlay(video_code) {
	var protocol =  window.location.protocol;
	$.getJSON(protocol+"//plsedu.smartcityapp.cn/youjiao/doMutiplePlay.do?jsoncallback=?",{
			rcode:video_code,
	        userName:'4101018888880041',
	        filterType:2,
	        outType:1
		},function(result){
			if(result.jsonList.length > 0){
				var videourl = result.jsonList[0].list[0].path;
				videourl = videourl.split("?")[0];
				//console.log(videourl);
				$("#video_player").attr("src",videourl);
				$("#video_player").attr("autoplay","true");
			}
			else{
				alert("未返回负载地址");
			}
			//console.log(result.jsonList.length);
		});
}

function video_share(course_id){
	var protocol =  window.location.protocol;
    //var port =  window.location.port;
    var host = window.location.host;
    var share_url = protocol+"//"+host+"/Elearn/fengze/play?course_id="+course_id;
    var imgUrl=protocol+"//"+host+"/public/Elearn/fengze/images/share.jpg";
	var shareInfo = {
		title : "丰泽微课人气点播开始啦！",
		link : share_url,
		imgUrl : imgUrl,
		desc : "快来点播你喜爱的课程吧！期待你的观看！"
		}
	cst.shareHandle(shareInfo);
    //console.log(share_url);
}

function app_download(){
	window.location.href="https://app.smartfengze.com/share/share-zhfz.html";
}