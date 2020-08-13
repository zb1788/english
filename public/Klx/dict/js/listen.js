var mp3_name = '',number_name='',mp3_dir='',mp3_url='',number_url='',mp3_barr='',mp3_sarr='',astoptime=0,repeat=1,settimer='';
function changepaper(num){
	$('#papernum').html((num+1)+'/'+papernum);
}
function  BodyScroll(curpos) {
    //alert($(curpos).offset().top);
    var container = $("html,body");
    var pos_y = $(curpos).offset().top - 300;
    $("html,body").animate({scrollTop: pos_y}, 100); //1000是ms,也可以用slow代替
}
function autopage(page,issumbit){
                    TouchSlide( { slideCell:"#iScroll",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			autoPage:true, //自动分页
			endFun:function(i){ //高度自适应
				var bd = document.getElementById("iScroll-bd");
                               // alert(bd.children[i].children[0].offsetHeight);
				bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";
                            
                               
                              // $('#iScroll-bd').parent().css('height',Number(bd.children[i].children[0].offsetHeight));
                              // alert($('#iScroll-bd').parent().height());
                              //$('#iScroll-bd').children(i).css('height',bd.children[i].children[0].offsetHeight);
				//if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
				
                                mp.clear();
                                if(issumbit){
                                    changepaper(i);
                                    mp.index = i;
                                    BodyScroll($('a[type=paper]:eq('+i+')'));
                                   // var paperid =$('a[type=paper]:eq('+i+')').attr('paperid');
                                    //settimer = setTimeout(function(){listen_play(paperid);},1000);
                                }
                                else{
                                    BodyScroll($('#show_score'));
                                }
                                
			},
                        startFun:function(){
                            mp.clear();
                        },
                        defaultIndex:page			
		});
                }
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
mp = new myplay();
function ListenLoad(){
     autopage(0,true);
	$("#jplayer").jPlayer({
        swfPath: '/public/public/js',
        wmode: "window",
        supplied: "mp3",
        preload: "none",
        volume: "1"
    });
}
function listen_play(paperid){
    clearTimeout(settimer);
    mp3_name = $('a[paperid='+paperid+']').attr('mp3url');
    astoptime = $('a[paperid='+paperid+']').attr('astoptime');
    repeat = $('a[paperid='+paperid+']').attr('repeat');
    mp3_dir = mp3_name.substr(0,2);
    mp3_url = exams_mp3_url+mp3_dir+'/'+mp3_name+'.mp3';
    mp.play(mp3_url);
    $("#jplayer").bind($.jPlayer.event.ended, function(event){
        settimer = setTimeout(function(){listen_dingdong_play(paperid,astoptime,repeat);},2000);  
    });
}
function listen_dingdong_play(paperid,astoptime,repeat){
    clearTimeout(settimer);
    var quenum = $('div[parentid='+paperid+']').length;
    var quecarindex = mp.queindex;
    if(quecarindex < quenum){
        if (mp.repeat >= repeat) {
            if (repeat == 1)
		{
                    mp.play('/public/home/js/dingdong.mp3');
                    $("#jplayer").bind($.jPlayer.event.ended, function(event){
                        settimer = setTimeout(function(){listen_number_play(paperid,astoptime,repeat);},2000);
                    });
		}
            else{
                    settimer = setTimeout(function(){listen_number_play(paperid,astoptime,repeat);},2000);
		}
        }
        else{
                mp.play('/public/home/js/dingdong.mp3');
                $("#jplayer").bind($.jPlayer.event.ended, function(event){
                    settimer = setTimeout(function(){listen_number_play(paperid,astoptime,repeat);},2000);
                });
            }
    }
    else{
        mp.queindex = 0;
        mp.clear();
        if((mp.index+1)< $('a[type=paper]').length){
            mp.index = mp.index +1;
            settimer = setTimeout(function(){autopage(mp.index,true);},2000);
        }
        else{
            mp.index = 0;
        }
    }
}
function listen_number_play(paperid,astoptime,repeat){
    clearTimeout(settimer);
    var isplaynum = true;
    var quenum = $('div[parentid='+paperid+']').length;
    var quecarindex = mp.queindex;
    var que2carindex = mp.que2index;
    BodyScroll($("div[parentid="+paperid+"]:eq(" + quecarindex + ")"));
    mp3_barr = $("div[parentid="+paperid+"]:eq(" + quecarindex + ")").attr('mp3url').substr(0,$("div[parentid="+paperid+"]:eq(" + quecarindex + ")").attr('mp3url').length-1).split('|');
 
    mp3_sarr = mp3_barr[que2carindex].split('&');
    number_name = mp3_sarr[0];
    mp3_name = mp3_sarr[1];
    if(number_name == ''){
        isplaynum = false;
    }
    mp3_dir = mp3_name.substr(0,2);
    mp3_url = exams_mp3_url+mp3_dir+'/'+mp3_name+'.mp3';
    number_url = exams_mp3_url+mp3_dir+'/'+number_name+'.mp3';
    if(isplaynum && quenum > 1 ){
        mp.play(number_url);
        $("#jplayer").bind($.jPlayer.event.ended, function(event){
            settimer = setTimeout(function(){listen_que_play(mp3_url,paperid,astoptime,repeat,que2carindex,mp3_barr);},1000);
        });
    }
    else{
        listen_que_play(mp3_url,paperid,astoptime,repeat,que2carindex,mp3_barr);
    } 
}
function listen_que_play(mp3_url,paperid,astoptime,repeat,que2carindex,mp3_barr){
    clearTimeout(settimer);
    mp.play(mp3_url);
    $("#jplayer").bind($.jPlayer.event.ended, function(event){
        if((que2carindex+1) < mp3_barr.length){
            mp.que2index = mp.que2index + 1;
            settimer = setTimeout(function(){listen_number_play(paperid,astoptime,repeat);},1000);
	}
        else{
            mp.que2index = 0;
            if (mp.repeat >= repeat) {
                mp.queindex = mp.queindex + 1;
                mp.repeat = 1;
                settimer = setTimeout(function(){listen_dingdong_play(paperid,astoptime,repeat);},Number(astoptime)*1000);
            }
            else {
                mp.repeat = mp.repeat + 1
                settimer = setTimeout(function(){listen_dingdong_play(paperid,astoptime,repeat);},2000);
            }
	}
    });
}
function listen_vcontent_play(mp3url)
{
    mp.clear();
    var tempmp3url = '';
    var mp3info = '';
    var mp3dir = '';
    var mp3name = '';
    var carindex = mp.que3index;
	var mp3 = mp3url;
	mp3 = mp3.substr(0,mp3.length-1);
	mp3info = mp3.split('|');
	mp3name = mp3info[carindex];
	mp3dir = mp3name.substr(0,2);
    tempmp3url = exams_mp3_url+mp3dir+'/'+mp3name+'.mp3';
    mp.play(tempmp3url);
    $("#jplayer").bind($.jPlayer.event.ended, function(event) {
		if ((carindex+1) < mp3info.length)
		{
			mp.que3index = mp.que3index +1;
			timecode = setTimeout(function(){listen_vcontent_play(mp3url);},1000);
		}
		else{
			 mp.clear();
			//$listen.removeClass('active');
			 mp.que3index = 0;
		}
       
       
    });
}
function accAdd(arg1,arg2){ 
	var r1,r2,m; 
	try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0} 
	try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0} 
	m=Math.pow(10,Math.max(r1,r2)) 
	return (arg1*m+arg2*m)/m 
} 
function show_score() {
    mp.clear();
		var listenlist = $('.showanswer[typeid]');
		var queid = '';
		var typeid = '';
		var inputvalue = '';
		var trueanswer = '';
		var trueanswerstr = '';
		var jiexstr = '';
		var total_score = $('#total_score').attr('total_score');
		var examsid = $('#total_score').attr('examsid');
		var unitid = $('#total_score').attr('unitid');
		var que_score = 0;
		var errnum = 0;
		listenlist.each(function(num){
			queid = $(this).attr('queid');
			typeid = $(this).attr('typeid');
                        trueanswer = $(this).attr('trueanswer');
                       
			if(typeid == 1 || typeid==3)//选择题和判断题
			{
				
				inputvalue = $('input[name='+queid+']:checked').val();

			}
			else if(typeid == 4 || typeid == 2){  //填空题
				inputvalue = '';
				$('input[name='+queid+']').each(function(pnum){
					inputvalue += $(this).val();
				});
			}
                       
                    if(trueanswer == inputvalue){
			
			que_score = accAdd(que_score,$('.showanswer[queid='+queid+']').attr('score'));
                        $('.showanswer[queid='+queid+'] .tips').removeClass('wrong');
                        $('.showanswer[queid='+queid+'] .tips').addClass('right');
                        $('.showanswer[queid='+queid+'] .tips').show();
                    }
                    else{
                 
			errnum = errnum +1;
                        $('.showanswer[queid='+queid+'] .tips').removeClass('right');
                        $('.showanswer[queid='+queid+'] .tips').addClass('wrong');
                         $('.showanswer[queid='+queid+'] .tips').show();
					
                    }
			
		});
		//alert('<p><span>总分：</span>'+total_score+'分</p><p><span>得分：</span>'+que_score+'分</p><p><span>错误：</span>'+errnum+'题</p>');
                $('#iScroll-bd').append('<div class="con" id="show_score"><ul><div class="tiCon"><h3 class="active"><a href="#">成绩</a></h3><div class="chengji"><p>总分：'+total_score+'分</p><p>得分：'+que_score+'分</p><p>错误：\n\
            '+errnum+'题</p></div></div></ul></div>');
                $('.cailiao').show();
                $('.jiexi').show();
                autopage(papernum,false);
//		$.get(ListenConter + 'save_study_exams_info',{unitid:unitid,examsid:examsid,score:que_score,errornum:errnum,gradeid:def_gradeid,termid:def_termid,versionid:def_versionid},function(result){		
//		});
}
function change_radio(queid,key){
   $('input[name='+queid+']:eq('+key+')').click(); 
}