/**
 * 查看试题大图
 */
var basepath = location.protocol + '//' + location.host;
$(document).delegate('article img', 'click', function() {
	showImage(basepath + $(this).attr("src"));
});

function resieImgAndTable(img,table,mW){
	img.each(function(k, v) {
		this.id=k+new Date().getTime()+"img";
		if(this.complete){
			thumbImg(mW,$('#'+this.id));
		}
	}).bind('load', function() { 
		thumbImg(mW,$('#'+this.id));
	});
	table.each(function(k, v) {
		thumbTable(mW, $(v));
	});
}

// 试题图片等比缩放
function thumbImg(maxWidth,el) {
	var e=el.get(0);
	var pw = maxWidth, w = el.width(), h = el
			.height();
	if (w > pw) {
		el.width(pw);
		el.height(el.height() * pw / w);
	}
}
function thumbTable(maxWidth, el) {
	var pw = Math.max(maxWidth - el.offset().left, 20), w = el.width(), h = el
			.height();
	if (w > pw) {
		var zoom = pw / w;
		el.css('zoom', zoom);
		el.find('img').css('zoom', zoom);
	}
}