function myplay() {
    var oplay = new Object();
    oplay.index = 0;
	oplay.queindex = 0;
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
    var wordid = $word.attr("wordid");
    var unitid = $word.attr("unitid");
    if (carindex < allnum) {
        if ($(playBtn).attr('class').indexOf('active') > 0) {   //如果联播按纽是播放状态，则播放
            BodyScroll($word);
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
            if (postype != 3)
                $(pdom + ":eq(" + carindex + ")").removeClass(cclass);
            if (postype == 'text')
                $word.removeClass('sound_single');
            else
                $word.removeClass(cclass);
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
        });
    }
    else
    {
        mp.index = 0;
        mp.clear();
        if (cclass == 'cur') //如果在播放的是单词听写,则播放按钮样式为active
            $(playBtn).removeClass('active');
        else
            $(playBtn).removeClass(cclass);
        $word.removeClass(cclass);
    }
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
    if (postype != 'text')
        save_study_word_info(def_postype, unitid, wordid); //保存单词学习记录
    mp.play(mp3);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
        mp.clear();
        $word.parent().removeClass(cclass);
        $word.removeClass(cclass);
    });
}
function  BodyScroll(curpos) {
    var container = $("html,body");
    var pos_y = $(curpos).offset().top - 300;
    $("html,body").animate({scrollTop: pos_y}, 100); //1000是ms,也可以用slow代替
}

function listenplay(pdom,repeat, timeoutnum)
{
	var allnum = $(pdom).length;
	var mp3url = ''
    var mp3dir = '';
    var speed = $('input[name=speed]:checked').val();
	var astoptime = '';
	var playflag = false;
	var carindex = mp.index;
	if (carindex < allnum) {
		if ($('.playBtn').attr('class').indexOf('active') > 0) {  //如果播放按纽在播放状态
			var listenmp3 = $(pdom + ":eq(" + carindex + ")").attr("mp3url");
			if(listenmp3.indexOf('|') == -1){  //是注释或者题干发音
				if(speed == 0){
					listenmp3 = listenmp3+'s';
				}
				else if(speed == 2){
					listenmp3 = listenmp3+'q';
				}
				astoptime = $(pdom + ":eq(" + carindex + ")").attr("astoptime");
				mp3dir = listenmp3.substr(0,2);
				mp3url = exams_mp3_url+mp3dir+'/'+listenmp3+'.mp3';
				mp.play(mp3url);
				$("#jplayer").bind($.jPlayer.event.ended, function(event){
					if (mp.repeat >= repeat) {
						mp.index = mp.index + 1;
						mp.repeat = 1;
					}
					else {
						mp.repeat = mp.repeat + 1
					}
					if ($('.playBtn').attr('class').indexOf('active') > 0) { //如果联播按纽是播放状态，则播放
						setTimeout(function() {
							listenplay(pdom, repeat, timeoutnum);
						}, Number(astoptime) * 1000);
					}
					else {
						mp.pause();
					}
				});
			}
			else{    //如果该音频是听力材料发音
				mp.play('/public/home/js/dingdong.mp3');
				$("#jplayer").bind($.jPlayer.event.ended, function(event){
					mp.index = mp.index + 1;
					listenmp3=listenmp3.substr(0,listenmp3.length-1); //去掉最后的一个"|"
					mp.queindex = 0;
					listen_que_play(listenmp3,pdom,repeat,timeoutnum);
				});
				
			}
		}
		else{
			mp.pause();
		}
	}
	else{
		mp.index = 0;
        mp.clear();
		$('.submit').show();
        $('.playBtn').removeClass('active');
        $('.playBtn').attr('title', '播放');
	}
}

function listen_que_play(listenmp3,pdom,repeat,timeoutnum){
	//alert(listenmp3);
	var quemp3 = listenmp3.split('|');
	var quemp3name = '';
	var quemp3dir = '';
	var quemp3url = '';
	var quecarindex = mp.queindex;
	//alert(quecarindex+"=="+quemp3.length);
	if(quecarindex < quemp3.length){
		quemp3name = quemp3[quecarindex];
		quemp3dir = quemp3name.substr(0,2);
		quemp3url = exams_mp3_url+quemp3dir+'/'+quemp3[quecarindex]+'.mp3';
		mp.play(quemp3url);
		$("#jplayer").bind($.jPlayer.event.ended, function(event){
			if((quecarindex+1) < quemp3.length){
				mp.queindex = mp.queindex + 1;
				setTimeout(function(){
					listen_que_play(listenmp3,pdom,repeat,timeoutnum);
				},2000);
			}
			else{
				setTimeout(function(){
					listenplay(pdom,repeat,timeoutnum);
				},5000);
			}
		});
	}
}
function listen_single_play(cdom)
{
    mp.index = 0;
    mp.clear();
    var $listen = cdom;
    var mp3url = '';
    var mp3dir = '';
    var speed = $('input[name=speed]:checked').val();
	var carindex = mp.index;
	var mp3 = $listen.attr("mp3url");
        if(speed == 0){
            mp3 = mp3+'s';
        }
        else if(speed == 2){
            mp3 = mp3+'q';
        }
	mp3dir = mp3.substr(0,2);
        mp3url = exams_mp3_url+mp3dir+'/'+mp3+'.mp3';
   
    $listen.addClass('active');
    mp.play(mp3url);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
        mp.clear();
        $listen.removeClass('active');
    });
}