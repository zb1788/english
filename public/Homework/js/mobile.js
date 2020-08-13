/**
 * 手机端
 */
var needHideProgress=false;
// 显示错误信息，直接返回客户端首页
function showError() {
	if (typeof UXinJSInterface != 'undefined') {
		UXinJSInterface.alterViewShow();
	}
}

// 显示正在加载
function showLoading() {
	if (typeof UXinJSInterface != 'undefined') {
		UXinJSInterface.showProgress();
	}
}

// 隐藏正在加载
function hideLoading() {
	if (typeof UXinJSInterface != 'undefined') {
		UXinJSInterface.hideProgress();
		needHideProgress=false;
	}else{
		needHideProgress=true;
	}
}

/**
 * 查看大图
 * @param imgSrc 图片完整地址
 */
function showImage(imgSrc){
	if (typeof UXinJSInterface != 'undefined') {
		UXinJSInterface.showPicFromHtml(imgSrc);
	}
}

/**
 * 创建一个新的webview
 * @param title 标题
 * @param url	地址
 */
function openNewWebPage(title,url){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openNewWebPage(title,url);
	}
}

/**
 * 打开多签的webview
 * @param pageTitles 内容
 * @param tabArray	页面tab签
 * @param rightItem 导航右侧
 * @param title 导航名称
 */
function openNewWebPageList(pageTitles,tabArray,rightItem,title){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openNewWebPageList(pageTitles,tabArray,rightItem,title);
	}
}

/**
 * 选中第几个tab
 * @param index
 */
function popToIndexController(index){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.popToIndexController(index);
	}
}

/**
 * 相当于在手机端点击返回
 */
function popTheController(){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.popTheController();
	}
}

/**
 * 创建无header的webview
 * @param url
 */
function openProgressController(url){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openProgressController(url);
	}
}

/**
 * 显示Alert
 * @param msg
 */
function showAlert(msg){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.showAlert(msg);
	}
}

/**
 * 隐藏Alert
 */
function hideAlert(){
	hideLoading();
}

/**
 * 返回到第一个webview,并刷新页面
 */
function popToRootController(){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.popToRootController();
	}
}

/**
 * 返回到第一个webview,并刷新页面
 */
function closewebview(){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.closeWebView();
	}
}


/**
 * 变更学生作业状态
 * @param batchId 作业批次ID
 */
function setOnlinWorkHaveFinished(batchId){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.setOnlinWorkHaveFinished(batchId);
	}
}

String.prototype.format=function(format){
	var args=arguments;
    return this.replace(/\{(\d+)\}/g, function(m, i){
        return args[i];
    });
};


/**
 * 获得移动端设备类型
 * 返回值(整形):
 * 1:ios phone
 * 2:android phone
 * 3:android pad
 */
function getDeviceType(){
	if(typeof UXinJSInterface != 'undefined'){
		return UXinJSInterface.getDeviceType();
	}
}




function getVoicePath(path){
    //在jplayer这个标签上加上一个属性表示单词的内容
    var filename=path;
    //alert(filename);
    //alert(pageindex);
    var contentid=document.getElementsByClassName("ques")[pageindex].getElementsByClassName("mp3voice")[0].getAttribute("contentid");
    tncontent=document.getElementsByClassName("ques")[pageindex].getElementsByClassName("mp3voice")[0].getAttribute("tncontent");
    var datatype=document.getElementsByClassName("ques")[pageindex].getElementsByClassName("mp3voice")[0].getAttribute("type");
    var readid=document.getElementsByClassName("ques")[pageindex].getElementsByClassName("mp3voice")[0].getAttribute("readid");
    var url="../Public/getTestScore";
    showAlert("正在评分,请稍后....");
    //mask.show();
    //alert("homeworkid="+homeworkid+"&studentid="+studentid+"&classid="+classid+"&wordreadid="+contentid+"&content="+tncontent+"&filename="+filename+"&textid="+readid+"&type="+type);
    //window.location.href=url+"?homeworkid="+homeworkid+"&studentid="+studentid+"&classid="+classid+"&wordreadid="+contentid+"&content="+tncontent+"&filename="+filename+"&textid="+readid+"&type="+type;
    mui.ajax(url,
        {
        data:{
            homeworkid:homeworkid,
            studentid:studentid,
            classid:classid,
            contentid:contentid,
            content:tncontent,
            filename:filename,
            readid:readid,
            type:datatype,
            ran:Math.random()
        },
        dataType:'json',//服务器返回json格式数据
        type:'post',//HTTP请求类型
        timeout:20000,
        async:true,
        success:function(data){
            //显示分数

            //显示星星的信息
            var content="";
            var score=-1;
            try{score=data.result.data.score;}catch(e){score=0;}
            var startint=parseInt(score);
            var userscore=parseFloat(score).toFixed(1);
            //alert(startint);
            if(startint>=0&&startint<=50){
                content=content+'<i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
            }else if(startint>50&&startint<=80){
                content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i>';
            }else if(startint>80){
                content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
            }else{
                content=content+'<i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
            }
            content=content+'<br><strong class="score">';
            if(score==-1){score=0;}
            content=content+userscore+'</strong>分';
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("rig-org")[0].innerHTML=content;
            //alert(document.getElementsByClassName("ques")[pageindex].getElementsByClassName["useraaaaa"][0].getAttribute(""));
            //用户的录音显示
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].setAttribute("mp3",data.uservoice);
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].childNodes[0].setAttribute("class","btn-bo02-on bo01 edi-yuan");
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].childNodes[0].setAttribute("type",datatype);
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].childNodes[0].setAttribute("bid",data.id);
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].childNodes[0].childNodes[0].setAttribute("bid",data.id);
            document.getElementsByClassName("ques")[pageindex].getElementsByClassName("uservoice")[0].childNodes[0].childNodes[0].setAttribute("type",datatype);
            try{UXinJSInterface.hideProgress();}catch(e){}
        },
        error:function(xhr,type,errorThrown){
            //异常处理；
            //mask.close();
            try{UXinJSInterface.hideProgress();}catch(e){}
            mui.toast("评分超时，请稍等一会儿再提交");
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
