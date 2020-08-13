function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}
var hideloading=function(){
    document.getElementById("over").style.display = "none";
    document.getElementById("layout").style.display = "none";
}
//Dom元素初始化
var initDom=function(dom,attrarr){
    var dom=$(dom);
    $.each(attrarr,function(key,value){
        dom.attr(value.id,value.val);
    });
    return dom;
}

var setTip=function(content){
    var tip = document.getElementById('tips');
    tip.innerHTML = content;
    tip.style.display = 'block';
    setTimeout(function(){ 
        tip.style.display = 'none';
    }, 2000);
}

var repeatend = 1;
function playWordList(pagecur,index)
{
    var allnum = $("div.text").eq(pagecur).find(".textread").length;
    var curindex = index;
    var $word = $("div.text").eq(pagecur).find(".textread").eq(curindex);
    var mp3 = $word.attr("mp3");
    console.log(mp3);
    $(".active").removeClass("active").removeClass("curplay");
    $word.addClass('active');
    //判断单词是够在中央
    try{
        var height=(window.screen.height-48-88)/3+(88);
        //当前元素的高度

        var top=$word.offset().top;
        var left=$word.offset().left;
        var lis=$("div.text").eq(pagecur).find(".textread");
        var lastli=lis[lis.length-1];
        var firstli=lis[0];
        var maxtop=$(lastli).offset().top;
        // var mintop=$(firstli).offset().top;
        //判断当前的元素和中间界限的高度
        $("#scroller").offset().top;
        if(top>(height)&&maxtop>(height*2-48)){
            $("#scroller").css('transform','translate(0px, '+($("#scroller").offset().top-(top-height/2))+'px)');
            //$("#scroller").css('-webkit-transform','translate(0px, -'+(height/2)+'px)');
        }
    }catch(e){

    }
    mp.clear();
    mp.play(mp3);
    var mp3time;
    $("#jplayer").bind($.jPlayer.event.ended,function(){
        if((curindex+1) < allnum){
            curindex ++;
          mp3time = setTimeout(function() {
                playWordList(pagecur,curindex);
                }, 1 * 2000);
        }
        else{
            $(".active").removeClass("active").addClass("curplay");
            $("#audioplay").addClass("stop").removeClass("play");
            $("#audioplay").find("font").text("连读");
            $("#audioplay").find("i").removeClass("icon-playt").addClass("icon-uniE60C");
        }
    });

    //     if ($(playBtn).attr('class').indexOf('active') > 0) {   //如果联播按纽是播放状态，则播放
    //         BodyScroll($word,postype);
    //         if (cdom != 'input' && postype != 'text') //单词听写和课文不在这里记录
    //             save_study_word_info(def_postype, unitid, wordid); //保存单词学习记录
    //         $('#curindex').html(carindex + 1);
    //         if (postype == 'text') {
    //             $(pdom + ":eq(" + carindex + ")").addClass(cclass);
    //             $word.addClass('sound_single');
    //         }
    //         else {
    //             if (cdom == 'input'){
    //                     $(pdom + ":eq(" + carindex + ")").addClass(cclass);
    //                 }
    //             else{
    //                 $(pdom + ":eq(" + carindex + ")").addClass(cclass);
    //                 $word.addClass(cclass);
    //             }
    //         }
    //         mp.play(mp3);
    //     }
    //     else {
    //         mp.pause();
    //     }
    //     $("#jplayer").bind($.jPlayer.event.ended, function(event) {
    //         if (mp.repeat >= repeat) {
    //             mp.index = mp.index + 1;
    //             mp.repeat = 1;
    //             repeatend = 2;
    //         }
    //         else {
    //             mp.repeat = mp.repeat + 1;
    //             repeatend = 1;
    //         }
    //         if (postype != 3){
    //             $(pdom + ":eq(" + carindex + ")").removeClass(cclass);
    //         }
    //         $word.removeClass(cclass);
    //         if(mp.index < allnum){
    //             if ($(playBtn).attr('class').indexOf('active') > 0) { //如果联播按纽是播放状态，则播放
    //                 if (repeatend ==2 && repeat == 2){
    //                     //alert("sss");
    //                     setTimeout(function() {
    //                         twordplay(pdom, cdom, cclass, repeat, timeoutnum, postype, playBtn);
    //                     }, 6000);
    //                 }
    //                 else{
    //                     setTimeout(function() {
    //                         twordplay(pdom, cdom, cclass, repeat, timeoutnum, postype, playBtn);
    //                     }, timeoutnum * 2000);
    //                 }
                
    //             }
    //             else {
    //                 mp.pause();
    //             }
    //         }
    //         else{
    //             mp.index = 0;
    //             mp.clear();
    //             if (cclass == 'cur') //如果在播放的是单词听写,则播放按钮样式为active
    //                 $(playBtn).removeClass('active');
    //             else
    //                 $(playBtn).removeClass(cclass);
    //             $word.removeClass(cclass);
    //         }
            
    //     });
    
    
}