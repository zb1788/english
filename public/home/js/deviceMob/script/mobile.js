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

function showImage(imgSrc){
	if (typeof UXinJSInterface != 'undefined') {
		UXinJSInterface.showPicFromHtml(imgSrc);
	}
}

function openNewWebPage(title,url){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openNewWebPage(title,url);
	}
}

String.prototype.format=function(format){
	var args=arguments;
    return this.replace(/\{(\d+)\}/g, function(m, i){
        return args[i];
    });
};