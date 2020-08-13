function loadData(){

}

function commitAction(obj,type){
	//var res=setIseParams(0,2,1,5000,1800,-1);
	//if(!res){
  //     return;
	//}
	switch(type){
		case 1:
			window.UXinJSInterface.showProgress();
			var the_timeout = setTimeout("window.UXinJSInterface.hideProgress();", 5000);
			break;
		case 2:
			break;
		case 3:
			window.UXinJSInterface.alterViewShow("test");
			break;
		case 4:

			break;
		case 5:
			//var text = document.getElementById("content").value;
			var text=$(obj).text();
			//window.UXinJSInterfaceSpeechEvaluator.startListening(text);
			break;
		case 6:
			window.UXinJSInterfaceSpeechEvaluator.stopListening();
			break;
		case 7:
			window.UXinJSInterfaceSpeechEvaluator.cancel();
			break;
		case 8:
			//var result = window.UXinJSInterfaceSpeechEvaluator.parserResult();
			var result = window.UXinJSInterfaceSpeechEvaluator.getTotalByResult();
			//document.getElementById("result").value = result;
			return result;
			//document.getElementById("result").value = "总分："+result;
			break;
		case 9:
			window.UXinJSInterfaceSpeechEvaluator.play();
			break;
		case 10:
			window.UXinJSInterfaceSpeechEvaluator.StopPlay();
			break;
		case 11:
			var ret = window.UXinJSInterfaceSpeechEvaluator.deleteRecord();
			if(ret){
				window.UXinJSInterface.showToast("删除成功");
			}else{
				window.UXinJSInterface.showToast("删除失败");
			}
		case 12:
		    window.UXinJSInterfaceSpeechEvaluator.setFtpParams("192.168.151.126","21","boz","boz");
			window.UXinJSInterfaceSpeechEvaluator.startUpload();
			break;


	}
}

function SEResults(result){
	document.getElementById("result").value = result;
}

function SETimeout(error){
	window.UXinJSInterface.showToast(error);
}

function SEError(error){
	window.UXinJSInterface.showToast(error);
}

function SEPlayOver(){
	window.UXinJSInterface.showToast("播放完成");
}

function fptUploadResult(result){

	window.UXinJSInterface.showToast("上传结果："+result);
	$("#jplayer").attr("upmp3",result);
	//alert($("#jplayer").attr("upmp3"));
}

//听力测评设置接口
/**
	 * 设置评测参数
	 *
	 * @param language
	 * 		语种
	 * 			0 // 英语
	 * 			1	// 汉语
	 * @param category
	 * 		题型
	 * 			0	// 单字
	 * 			1 // 词语
	 * 			2	// 句子
	 * @param resultLevel
	 * 		结果等级
	 * 			0	// 简单
	 *			1	// 完整
	 * @param btimeout
	 * 		前端点超时	毫秒
	 * @param atimeout
	 * 		后端点超时	毫秒
	 * @param ptimeout
	 * 		评测超时	毫秒(默认-1)
	 * @return
	 * 		true 成功
	 * 		false 失败
	 */
