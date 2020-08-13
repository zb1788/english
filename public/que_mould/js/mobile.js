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
 * 创建一个携带sso域名和头像cookie的webview
 * @param url	地址
 */
function openSSoController(url){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openSSoController(url);
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
 * 打开webview以json格式传输数据,可以设置是否刷新上一个webview
 * {
 *  "url":"",
 *  "isNeedReloadThisPage":1,//0不需要刷新1需要刷新
 *  "title":"",
 *  "isOnlyReadTitle":0,//0此处设置的title不生效读取页面中的title，1此处title生效
 *  "RightItem":{"ImageUrl":"","ItemTitle":"","ItemUrl":""}
 *  }
 * @param json
 */
function openNewProgressControllerFromJson(json){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openNewProgressControllerFromJson(json);
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
		UXinJSInterface.showProgress(msg);
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
 * 返回到第一个webview,并打开指定地址
 * @param url
 */
function exchangeRootUrl(url){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.exchangeRootUrl(url);
	}
}

/**
 * 根据key获取客户端的cookie值
 * */
function getCookieValue(key){
	var v='';
	if(typeof UXinJSInterface != 'undefined' && typeof UXinJSInterface.getCookieValue == 'function'){
		v=UXinJSInterface.getCookieValue(key);
	}
   return v;
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

/**
 * 打开客户端的离线作业
 * @param chapterJson
 */
function setCurrentEduChapter(chapterJson){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.setCurrentEduChapter(chapterJson);
	}
}


/**
 * 
 * 根据指定目录读取本地文件内容
 * @param path 例如：/localfiles/test.txt
 * @param charset 字符集：utf-8、gbk等
 * @returns
 */
function read(path,charset){
	if(typeof UXinJSInterface != 'undefined'){
		return UXinJSInterface.read(path,charset);
	}
}

/**
 * 获取用户信息
 * @param type
 * registername 用户账号	realname	用户名称
 * mobile	用户手机号		classid		班级id
 * schoolid	学校id
 * versionName app设备版本名称  如：V3.0.9
 * versionCode app设备版本号   如：20180503
 */
function getUserInfo(type){
	if(typeof UXinJSInterface != 'undefined'){
		return UXinJSInterface.getUserInfo(type);
	}
}

/**
 * 电子书包返回应用主界面
 * @param type
 * 标识是否锁屏，0 不锁屏，1 锁屏
 * */
function goBackHome(type){
	if(typeof UXinJSInterface != 'undefined' && typeof UXinJSInterface.goBackHome == 'function' ){
		UXinJSInterface.goBackHome(type);
	}
}

/**
 * @param type 指令类型
 * @param data
 * state：发送中间状态， （data为1正在下载，2下载完成，3下载失败，4正在答题，5正在登分，6正在预习，7正在分享）
 * questiona：开始套卷作答
 * endquestiona：结束套卷作答
 * preview：开始课堂探究
 * endpreview”：结束课堂探究
 */
function sendCommand(type,data){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.sendCommand(type,data);
	}
}

/**
 * 跨域POST大量数据
 * @param url 提交地址
 * @param params json格式的参数
 * @returns response: 成功返回数据，失败返回失败原因，类型字符串
 *
 */
function post(url,params){
	if(typeof UXinJSInterface != 'undefined'){
		return UXinJSInterface.post(url,params);
	}
}

/**
 * 调用客户端拍照
 * @param area 属性值
 */
function selectPhoto(area){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.selectPhoto(area);
	}
}

/**
 * 电子书包App按照指定方式打开相机功能
 * @param type int 打开类型，0 拍照，1 相册，2 画板
 * @param area String 区域信息  插件返回图片后，传递给页面上使用
 * @param picPath String 画板原图路径
 * */
function selectPhotoByType(type,area,picPath){
	if(typeof UXinJSInterface != 'undefined'){
		if(typeof UXinJSInterface.selectPhotoByType == 'undefined'){
			UXinJSInterface.selectPhoto(area);
		}else{
			UXinJSInterface.selectPhotoByType(type,area,picPath);
		}
	}
}

/**
 * 电子书包单题互动获取模板下载地址和用户作答权限
 * @param type (content_single_paper 单题作答内容地址,state_allow_answer 允许作答状态,返回值为：1允许答题0、不允许答题 2互评 3自评)
 * @returns response: 成功返回数据，失败返回失败原因，类型字符串
 *
 */
function getPaperData(type){
	if(typeof UXinJSInterface != 'undefined'){
		return UXinJSInterface.getPaperData(type);
	}else{
		if(type == 'state_allow_answer'){
			return 0;
		}
	}
	return '';
}

/**
 * 电子书包透明锁屏
 *
 */
function openScreenLockMode(){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.openScreenLockMode();
	}
}

/**
 * 电子书包优教信使工具栏
 * 使用方法：答题工具条可用功能列表，用数字字串标识可用功能，例如图片和文字：12
 * @param types (1	答题卡,2	文字,3	图片,4	录屏,5	录音,6	画板)
 * 		  data是json格式的字符串{"picPath": 加载图片路径, "uploadPath": 上传服务路径}
 *	      picPath 如果没有就不加载
 *        uploadPath 如果没有图片就返回给你页面
 * @returns 无
 *
 */
function openAnswerSheet(types){
	if(typeof UXinJSInterface != 'undefined'){
		var as=arguments, asLen=arguments.length,_data=null;
		if(asLen > 1){
			_data=as[1];
		}
		UXinJSInterface.openAnswerSheet(types,_data);
	}
}

/**
 * 电子书包优教信使关闭工具条
 * */
function closeAnswerSheet(){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.closeAnswerSheet();
	}
}

/**
 * 电子书包优教信使日志输出
 * @param message (输出的消息内容)
 * @returns 无
 *
 */
function log(message){
	if(typeof UXinJSInterface != 'undefined'){
		UXinJSInterface.log(message);
	}
}

/**
 * 电子书包优教信使提示信息
 * @param message (提示的消息内容)
 * @returns 无
 *
 */
function uiShowToast(message){
	if(typeof UXinJSInterface != 'undefined' && typeof UXinJSInterface.uiShowToast == 'function' ){
		UXinJSInterface.uiShowToast(message);
	}
}

String.prototype.format=function(format){
	var args=arguments;
    return this.replace(/\{(\d+)\}/g, function(m, i){
        return args[i];
    });
};


/**
 * 增加cookie
 */
(function(){var $={isArray:function(v){return toString.apply(v)==="[object Array]"},isEmpty:function(v,allowBlank){return v===null||v===undefined||((this.isArray(v)&&!v.length))||(!allowBlank?v==="":false)},type:function(obj){if(obj==null){return obj+""}return typeof obj},isFunction:function(obj){return this.type(obj)==="function"},extend:function(){var src,copyIsArray,copy,name,options,clone,target=arguments[0]||{},i=1,length=arguments.length,deep=false;if(typeof target==="boolean"){deep=target;target=arguments[i]||{};i++}if(typeof target!=="object"&&!jQuery.isFunction(target)){target={}}if(i===length){target=this;i--}for(;i<length;i++){if((options=arguments[i])!=null){for(name in options){src=target[name];copy=options[name];if(target===copy){continue}if(deep&&copy&&(jQuery.isPlainObject(copy)||(copyIsArray=jQuery.isArray(copy)))){if(copyIsArray){copyIsArray=false;clone=src&&jQuery.isArray(src)?src:[]}else{clone=src&&jQuery.isPlainObject(src)?src:{}}target[name]=jQuery.extend(deep,clone,copy)}else{if(copy!==undefined){target[name]=copy}}}}}return target}};var pluses=/\+/g;function encode(s){return config.raw?s:encodeURIComponent(s)}function decode(s){return config.raw?s:decodeURIComponent(s)}function stringifyCookieValue(value){return encode(config.json?JSON.stringify(value):String(value))}function parseCookieValue(s){if(s.indexOf('"')===0){s=s.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\")}try{s=decodeURIComponent(s.replace(pluses," "));return config.json?JSON.parse(s):s}catch(e){}}function read(s,converter){var value=config.raw?s:parseCookieValue(s);return $.isFunction(converter)?converter(value):value}var config=$.cookie=function(key,value,options){if(value!==undefined&&!$.isFunction(value)){options=$.extend({},config.defaults,options);if(typeof options.expires==="number"){var days=options.expires,t=options.expires=new Date();t.setTime(+t+days*86400000)}return(document.cookie=[encode(key),"=",stringifyCookieValue(value),options.expires?"; expires="+options.expires.toUTCString():"",options.path?"; path="+options.path:"",options.domain?"; domain="+options.domain:"",options.secure?"; secure":""].join(""))}var result=key?undefined:{};var cookies=document.cookie?document.cookie.split("; "):[];for(var i=0,l=cookies.length;i<l;i++){var parts=cookies[i].split("=");var name=decode(parts.shift());var cookie=parts.join("=");if(key&&key===name){result=read(cookie,value);break}if(!key&&(cookie=read(cookie))!==undefined){result[name]=cookie}}return result};config.defaults={};$.removeCookie=function(key,options){if($.cookie(key)===undefined){return false}$.cookie(key,"",$.extend({},options,{expires:-1}));return !$.cookie(key)};this.mobile=$})(window);
/**
 * 设置cookie
 * */
function writeMobileCookie(){
	var key=['deviceType','ut','deviceInfo'],_v;
	if(mobile.isEmpty(mobile.cookie('ut')) && mobile.isEmpty(mobile.cookie('deviceType'))){
		return;
	}
	for(var i=0;i<key.length;i++){
		if(mobile.isEmpty(mobile.cookie('deviceType'))){
			_v=getCookieValue(key[i]);
			if(key[i] == 'deviceType' && mobile.isEmpty(_v)){
				_v='mobile';
			}
			_v&&mobile.cookie(key[i],String(_v),{'path':'/'});
		}
	}
}
writeMobileCookie();
