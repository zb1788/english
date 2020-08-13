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
 * @param pageTitles 
 * @param tabArray
 * @param rightItem
 */
function openNewWebPageList(pageTitles,tabArray,rightItem){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openNewWebPageList(pageTitles,tabArray,rightItem);
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

String.prototype.format=function(format){
	var args=arguments;
    return this.replace(/\{(\d+)\}/g, function(m, i){
        return args[i];
    });
};