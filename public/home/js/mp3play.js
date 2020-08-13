function myplay() {
    var oplay = new Object();
    oplay.index = 0;
	oplay.queindex = 0;
	oplay.que2index = 0;
	oplay.que3index = 0;
    oplay.url = "";
    oplay.repeat = 1;
    oplay.play = function(mp3) {
        oplay.clear();
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    }
    oplay.clear = function() {
		
        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        //$("#jplayer").data("SpeakMP3Value", "0");  
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    return oplay;
}
//获取mp3文件路径
function getmp3url(mp3name){
	//mp3name = mp3name.substr(0,mp3name.length-1);
	var mp3url = '';
	mp3url = text_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
	return mp3url;
}

/**
 * mp3播放连播
 * @param {type} pdom 列表的父元素
 * @param {type} cdom 列表的子元素
 * @param {type} cclass 播放时添加样式
 * @param {type} repeat 播放次数
 * @param {type} timeoutnum 延迟时间
 * @param {type} postype 当前播放的内容:0单词跟读,1单词学习,2单词记忆,3单词听写,
 *@param {type} playBtn 播放按钮对象
 * @returns {undefined}
 */
var repeatend = 1;
function twordplay(pdom, cdom, cclass, repeat, timeoutnum, postype, playBtn)
{
    var allnum = $(pdom).length;
	
    if (cdom == 'input')
        allnum = allnum - 1;
    var carindex = mp.index;
    var $word = $(pdom + ":eq(" + carindex + ")").find(cdom);
    var mp3 = $word.attr("mp3url");
    if(postype == 'text'){
    	mp3 = getmp3url(mp3);
    }
    var wordid = $word.attr("wordid");
    var unitid = $word.attr("unitid");
        if ($(playBtn).attr('class').indexOf('active') > 0) {   //如果联播按纽是播放状态，则播放
            BodyScroll($word,postype);
            if (cdom != 'input' && postype != 'text') //单词听写和课文不在这里记录
                save_study_word_info(def_postype, unitid, wordid); //保存单词学习记录
            $('#curindex').html(carindex + 1);
            if (postype == 'text') {
                $(pdom + ":eq(" + carindex + ")").addClass(cclass);
                $word.addClass('sound_single');
            }
            else {
                if (cdom == 'input'){
						$(pdom + ":eq(" + carindex + ")").addClass(cclass);
					}
                else{
                    $(pdom + ":eq(" + carindex + ")").addClass(cclass);
					$word.addClass(cclass);
				}
            }
            mp.play(mp3);
        }
        else {
            mp.pause();
        }
        $("#jplayer").bind($.jPlayer.event.ended, function(event) {
            if (mp.repeat >= repeat) {
                mp.index = mp.index + 1;
                mp.repeat = 1;
				repeatend = 2;
            }
            else {
                mp.repeat = mp.repeat + 1;
				repeatend = 1;
            }
            if (postype != 3){
                $(pdom + ":eq(" + carindex + ")").removeClass(cclass);
            }
           	$word.removeClass(cclass);
            if(mp.index < allnum){
                if ($(playBtn).attr('class').indexOf('active') > 0) { //如果联播按纽是播放状态，则播放
                    if (repeatend ==2 && repeat == 2){
                        //alert("sss");
                        setTimeout(function() {
                            twordplay(pdom, cdom, cclass, repeat, timeoutnum, postype, playBtn);
                        }, 6000);
                    }
                    else{
                        setTimeout(function() {
                            twordplay(pdom, cdom, cclass, repeat, timeoutnum, postype, playBtn);
                        }, timeoutnum * 2000);
                    }
                
                }
                else {
                    mp.pause();
                }
            }
            else{
                mp.index = 0;
                mp.clear();
                if (cclass == 'cur') //如果在播放的是单词听写,则播放按钮样式为active
                    $(playBtn).removeClass('active');
                else
                    $(playBtn).removeClass(cclass);
                $word.removeClass(cclass);
            }
            
        });
    
    
}
/**
 * 单个播放
 * @param {type} cdom
 * @param {type} cclass
 * @param {type} repeat
 * @param {type} timeoutnum
 * @param {type} postype
 * @returns {undefined}
 */
function single_play(cdom, cclass, repeat, timeoutnum, postype)
{
    $('.playBtn').removeClass('active');
    mp.index = 0;
    mp.clear();
    var $word = cdom;
    var mp3 = $word.attr("mp3url");
    var wordid = $word.attr("wordid");
    var unitid = $word.attr("unitid");
    $word.parent().addClass(cclass);
    $word.addClass(cclass);
    if (postype != 'text'){
        save_study_word_info(def_postype, unitid, wordid); //保存单词学习记录
    }
    else{
        mp3 = getmp3url(mp3);
    }
    mp.play(mp3);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
        mp.clear();
        $word.parent().removeClass(cclass);
        $word.removeClass(cclass);
    });
}
function example_play(cdom, cclass, repeat, timeoutnum, postype)  //例句播放
{ 
    $('.playBtn').removeClass('active');
    mp.index = 0;
    mp.clear();
    var $word = cdom;
    var mp3 = $word.attr("mp3url");
    var wordid = $word.attr("wordid");
    var unitid = $word.attr("unitid");
    $word.parent().addClass(cclass);
    $word.addClass(cclass);
    mp3 = getmp3url(mp3);
    mp.play(mp3);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
        mp.clear();
        $word.parent().removeClass(cclass);
        $word.removeClass(cclass);
    });
}
function  BodyScroll(curpos,postype) {
	if(postype == 'text'){
		curpos = curpos.parent();
	}
	//alert($(curpos).offset().top);
    var container = $("html,body");
    var pos_y = $(curpos).offset().top - 300;
    $("html,body").animate({scrollTop: pos_y}, 100); //1000是ms,也可以用slow代替
}
