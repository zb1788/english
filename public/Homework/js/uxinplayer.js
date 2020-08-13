var uxinplayer = "";
//播放的时候需要定义全局的player
//uxinplayer表示UXIN的web播放器
//参数option 需要两个参数id以及callback
var mp = new Object();


mp.index = 0;
mp.stemindex = 0;
mp.queinitindex = 0;
mp.questionindex = 0;
mp.childstemindex = 0;
mp.playtimes=0;
mp.childinitstemindex = 0;
mp.url = "";
mp.repeat = 1; 
mp.curpeat = 1;
mp.url = "";
mp.id = "";
mp.callback = "";

mp.play = function(mp3) {
    try{
        window.UXinJSInterfaceSpeech.playAudio(mp3);
    }catch(e){

    }
};

mp.pause = function() {
    try{
        window.UXinJSInterfaceSpeech.stopAudio();
    }catch(e){

    }
};

mp.change = function(obj,options){
    obj.callback = options.callback;
    obj.id = options.id;
    return obj;
};


//uxin全局的播放完成的状态
function onAudioPlayStatus(status){
    if(status == 0){
        (mp.callback !=undefined && (typeof mp.callback === "function"))?mp.callback():"";
    }else{

    }
}