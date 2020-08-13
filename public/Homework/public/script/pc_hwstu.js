// 录音相关开始
var jsReady = false;
function  f_getURL(){
        var url = "record_file_save";
        return url;
}
function startToAction(){
  console.log("ssssssss");
}
function sendToAction(filename){
   var readid = '';
   var readcontent= '';
   var contentid = '';
   if(readtype==0){
     readid = $('div.wordalound').attr('readid');
     readcontent= $('div.wordalound').attr('readcontent');
     $('.warecordmp3').parent().hide();
     $('.wordalound .btn_cs').html('<img src="/public/Homework//images/loading.gif"/>&nbsp;&nbsp;请稍等，正在测评').show();
   }
   else{
       readid = $('div.textalound').attr('readid');
       readcontent= $('div.textalound').attr('readcontent');
       contentid= $('div.textalound').attr('textid');
       $('.tarecordmp3').parent().hide();
       $('.textalound .btn_cs').html('<img src="/public/Homework/images/loading.gif"/>&nbsp;&nbsp;请稍等，正在测评').show();
   }
   //console.log(filename);
   $.getJSON('../public/getTestScore',{type:readtype,homeworkid:homeworkid,studentid:studentid,classid:classid,readid:readid,content:readcontent,contentid:contentid,filename:filename,ran:Math.random()},function(result){
      if(readtype == 0){
        $('.wordalound .btn_cs').html('得分：<font class=" redFon">'+result.result.data.score.toFixed(1)+'分</font>');
        $('.wordalound .btn_cs').show();
        if(parseInt(result.result.data.score) < 60){
          $('#cardwa'+readid).removeClass('complete').removeClass('error').addClass('error');
        }
        else{
          $('#cardwa'+readid).removeClass('complete').removeClass('error').addClass('complete');
        }

      }
      else{
        $('.textalound .btn_cs').html('得分：<font class=" redFon">'+result.result.data.score.toFixed(1)+'分</font>');
        $('.textalound .btn_cs').show();
        if(parseInt(result.result.data.score) < 60){
          $('#cardta'+contentid).removeClass('complete').removeClass('error').addClass('error');
        }
        else{
          $('#cardta'+contentid).removeClass('complete').removeClass('error').addClass('complete');
        }
      }
   });

}

function isReady() {
   return jsReady;
}
function pageInit() {
   jsReady = true;
   //document.forms["form1"].output.value += "\n" + "JavaScript is ready.\n";
}
function thisMovie(movieName) {
   if (navigator.appName.indexOf("Microsoft") != -1) {
       return window[movieName];
   } else {
       return document[movieName];
   }
}
function sendToActionScript(value) {
  //alert('ss');
  if(readtype == 0){
    thisMovie("vcomre").sendToActionScript(value);
  }else{
    thisMovie("ExternalInterfaceExample").sendToActionScript(value);
  }
     
}
// 录音相关结束
function tj_pop(issubmit){
  savePaper("{$homeworkid}",issubmit,interfaceServiceURL);
}

function back(){
  var url=interfaceServiceURL;
  location.href=url;
}

function clearcss(){
  //mp.stop();
  mp.clear();
  $('.sound_single').removeClass('active');
}
//单词拼写保存
function save_stu_wordpx(obj){
  
  var useranswer = $(obj).val();
  var useranswerarr = '';
  var tempusrans = '';
  if(useranswer != ''){
    useranswerarr = useranswer.split('');
    for (var i = 0 ; i < useranswerarr.length; i++) {
        if(i == (useranswerarr.length-1)){
          tempusrans+=useranswerarr[i];
        }
        else{
          tempusrans+=useranswerarr[i]+",";
        }
    }
    useranswer = tempusrans;
    var questionid = $('.wordwp').attr('questionid');
    var wordid = $('.wordwp').attr('wordid');
    var typeid = $('.wordwp').attr('typeid');
    $.getJSON('../public/setUserWordtestanswer',{homeworkid:homeworkid,studentid:studentid,classid:classid,typeid:typeid,questionid:questionid,wordid:wordid,useranswer:useranswer,ran:Math.random()},function(result){
      $('#cardwp'+wordid).addClass('complete');
   });
  }
}
//听力训练单题保存
function save_stu_exams(obj){
  var examsid = $(obj).attr('examsid');
  var quizid = $(obj).attr('quizid');
  var questionid = $(obj).attr('questionid');
  var answerid = $(obj).attr('answerid');
  var useranswer = $(obj).attr('useranswer');
  var typeid = $(obj).attr('typeid');
  $('a[option='+questionid+']').removeClass('btn_green').addClass('btn_gray');
  $(obj).removeClass('btn_gray').addClass('btn_green');

  $.getJSON('../public/setUseranswer',{homeworkid:homeworkid,studentid:studentid,classid:classid,typeid:typeid,questionid:questionid,examsid:examsid,quizid:quizid,answerid:answerid,useranswer:useranswer,ran:Math.random()},function(result){
    $('#eqcard'+questionid).addClass('complete');
   });
}
function onTopClick(questionid) {
    $(".te_tab a.aBtn[name=eq]").click();
    window.location.href = "#top"+questionid;
}
$(function() {               
  $("#scrollsidebar").fix({
		float : 'right',	//default.left or right
		//minStatue : true,
		skin : 'green',	//default.gray or blue
		durationTime : 600
	});

  if(wacount > 0){
    p.getStuWalist(homeworkid,0,'next',woradwaindex);
  }
  if(wscount > 0){
    p.getStuWplist(homeworkid,1,'next',woradwpindex);
  }
  if(wrcount > 0){
    p.getStuWylist(homeworkid,2,'next',woradwyindex);
  }
  if(wccount > 0){
    p.getStuWxlist(homeworkid,3,'next',woradwxindex);
  }
  if(tacount > 0){
    p.getStuTalist(homeworkid,4,'next',taindex);
  }
  if(eqcount > 0){
    //alert("sss");
    p.getStuExaminfo(homeworkid,$("#tmpleqquestion"),$(".examsquiz"));
  }
  
  

  $(".te_tab .aBtn").bind("click",function(){
     clearTimeout(mp3_progress);
     mp.clear();
     var name=$(this).attr("selecttype");
     if(name == 'wordalound'){
       readtype = 0;
     }
     else if(name == 'textalound'){
       readtype = 1;
     }
     else{
      readtype = 0;
     }
     $(".aBtn").removeClass("cur");
     $(this).addClass("cur");
     $(".homework").hide();
     $("."+name).show();
  });
  $(".aBtn:eq(0)").click();
  //跟读作业不能再电脑上做
   $(".numkeyboard").ioskeyboard({
      keyboardRadix:80,//键盘大小基数，实际大小比为9.4，即设置为100时实际大小为940X330
      keyboardRadixMin:40,//键盘大小的最小值，默认为60，实际大小为564X198
      keyboardRadixChange:true,//是否允许用户改变键盘大小,该功能仅能完美支持Chrome26；仅当keyboardRadixMin不小于60时才较好支持Safari内核浏览器
      clickeve:true,//是否绑定元素click事件
      colorchange:true,//是否开启按键记忆功能，如果开启，将随着按键次数的增加加深相应按键的背景颜色
      colorchangeStep:1,//按键背景颜色改变步伐，采用RBG值，默认为RGB(255,255,255),没按一次三个数字都减去步伐值
      colorchangeMin:154//按键背影颜色的最小值，默认为RGB(154,154,154)
  });
});

function word_prev_netx(obj){

  window.location.href = "#topone";
  var type = $(obj).attr('type');
  var action = $(obj).attr('action');
  if(type == 0){
    $(".te_tab a.aBtn[name=wa]").click();
  }
  if(type == 1){
    $(".te_tab a.aBtn[name=wp]").click();
  }
  if(type == 2){
    $(".te_tab a.aBtn[name=wy]").click();
  }
  if(type == 3){
    $(".te_tab a.aBtn[name=wx]").click();
  }
  if(type == 4){
    $(".te_tab a.aBtn[name=ta]").click();
  }
  if(type == 5){
    $(".te_tab a.aBtn[name=eq]").click();
  }
  if(type == 0 && action == 'cur'){ //单词跟读下一题
     woradwaindex = $(obj).attr('curindex');
     readtype = 0;
     p.getStuWalist(homeworkid,type,'next',woradwaindex);
  }
  if(type == 0 && action == 'next'){ //单词跟读下一题
    woradwaindex ++;
    readtype = 0;
    p.getStuWalist(homeworkid,type,action,woradwaindex);
  }
  if(type == 0 && action == 'prev'){ //单词跟读下一题
    woradwaindex--;
    readtype = 0;
    p.getStuWalist(homeworkid,type,action,woradwaindex);
  }
  if(type == 1 && action == 'cur'){ //单词拼写上一题
    woradwpindex = $(obj).attr('curindex');
    p.getStuWplist(homeworkid,type,action,woradwpindex);
  }
  if(type == 1 && action == 'next'){ //单词拼写上一题
    woradwpindex++;
    p.getStuWplist(homeworkid,type,action,woradwpindex);
  }
  if(type == 1 && action == 'prev'){ //单词拼写下一题
    woradwpindex--;
    p.getStuWplist(homeworkid,type,action,woradwpindex);
  }
  if(type == 2 && action == 'cur'){ //单词拼写下一题
    woradwyindex = $(obj).attr('curindex');
    p.getStuWylist(homeworkid,type,action,woradwyindex);
  }
  if(type == 2 && action == 'prev'){ //单词拼写下一题
    woradwyindex--;
    p.getStuWylist(homeworkid,type,action,woradwyindex);
  }
  if(type == 2 && action == 'next'){ //单词拼写下一题
    woradwyindex++;

    p.getStuWylist(homeworkid,type,action,woradwyindex);

  }
  if(type == 3 && action == 'cur'){ //单词拼写下一题
    woradwxindex = $(obj).attr('curindex');
    p.getStuWxlist(homeworkid,type,action,woradwxindex);
  }
  if(type == 3 && action == 'prev'){ //单词拼写下一题
    woradwxindex--;
    p.getStuWxlist(homeworkid,type,action,woradwxindex);
  }
  if(type == 3 && action == 'next'){ //单词拼写下一题
    woradwxindex++;
    p.getStuWxlist(homeworkid,type,action,woradwxindex);
  }
  if(type == 4 && action == 'cur'){ //单词拼写下一题
    taindex = $(obj).attr('curindex');
    readtype = 1;
    p.getStuTalist(homeworkid,type,'next',taindex);
  }
  if(type == 4 && action == 'prev'){ //单词拼写下一题
    taindex--;
    readtype = 1;
    p.getStuTalist(homeworkid,type,action,taindex);
  }
  if(type == 4 && action == 'next'){ //单词拼写下一题
    taindex++;
    readtype = 1;
    p.getStuTalist(homeworkid,type,action,taindex);
  }

} 
function changeinfo(wordinfo,type,action,index,length){
 // console.log('type:'+type);
   mp.clear();
   index = parseInt(index);
  if(type == 0){
    woradwaindex = index;
    change_title(woradwaindex,'wordalound',length);
    //else if 
  }
  if(type == 1){
    woradwpindex = index;
    change_title(woradwpindex,'wordwp',length);
    //else if 
  }
  if(type ==2){
    woradwyindex = index;
    change_title(woradwyindex,'wordwy',length);
    //else if 
  }
  if(type ==3){
    woradwxindex = index;
    change_title(woradwxindex,'wordwx',length);
    //else if 
  }
  if(type ==4){
    taindex = index;
    change_title(taindex,'textalound',length);
    //else if 
  }
  
  if(type == 0){
   // console.log(wordinfo.score);
    $('.wordalound').attr('readid',wordinfo.wordreadid);
    $('.wordalound').attr('readcontent',wordinfo.word);
    $('.wordalound .total').html(length);
    $('.wordalound .curindex').html(index+1);
    $('.wordalound .pic').removeAttr('src').attr('src',word_pic_url+wordinfo.pic);
    if(wordinfo.isstress == '1'){
      $('.wordalound .word').html(wordinfo.word+'<i class="fa  fa-star"></i>');
    }
    else{
      $('.wordalound .word').html(wordinfo.word);
    }
    
    if(wordinfo.isword == '1'){
      $('.wordalound .yinbiao').html('['+wordinfo.ukmark+']');
      
    }
    else{
      $('.wordalound .yinbiao').html('');
    }
    $('.wordalound .explains').html(wordinfo.explains);
    $('.wordalound .mp3').removeAttr('mp3').attr('mp3',wordinfo.ukmp3);
   
    if(wordinfo.userscore != null && wordinfo.userscore !="null"){
      $('.wordalound .warecordmp3').removeAttr('mp3').attr('mp3',wordinfo.usermp3);
      $('.wordalound .warecordmp3').removeAttr('recordid').attr('recordid',wordinfo.recordid);
      $('.wordalound .btn_cs').html('得分：<font class=" redFon">'+wordinfo.userscore+'分</font>');
      $('.wordalound .btn_cs').show();
      $('.warecordmp3').parent().show();
    }
    else{
      $('.wordalound .btn_cs').hide();
      $('.warecordmp3').parent().hide();
    }
    sendToActionScript('');
  }
  if(type == 1){
    // var questionid = wordinfo.id;
    // var useranswer2 = wordinfo.useranswer2;
    $('.wordwp .mp3').attr('mp3',wordinfo.mp3);
    $('.wordwp .explains').html((index+1)+'. '+wordinfo.explains);
    $('.wordwp .total').html(length);
    $('.wordwp .curindex').html(index+1);
    $('.wordwp').attr('questionid',wordinfo.quesid);
    $('.wordwp').attr('wordid',wordinfo.wordid);
    $('.wordwp').attr('typeid',wordinfo.typeid);
    $('.wordwp .numkeyboard').val(wordinfo.useranswer2);
  }
   if(type ==2){
    var wordid = wordinfo.wordid;
    //$('.wordwp .explains').html((index+1)+'. '+wordinfo.explains);
    $('.wordwy .total').html(length);
    $('.wordwy .curindex').html(index+1);
    if(wordinfo.typeid == 1){
      $('.wordwy .tingli').html((index+1)+'.<span></span>'+wordinfo.word);
    }
    else{
      $('.wordwy .tingli').html((index+1)+'.<span></span>'+wordinfo.explains);
    }
    $('.wordwy span[type=option]:eq(0)').html(wordinfo.option_a);
    $('.wordwy span[type=option]:eq(1)').html(wordinfo.option_b);
    $('.wordwy span[type=option]:eq(2)').html(wordinfo.option_c);
    $('.wordwy a[type=option]').each(function(){
      if($(this).attr('useranswer') == wordinfo.useranswer){
        $(this).removeClass('btn_gray').addClass('btn_green');
      }
      else{
        $(this).removeClass('btn_green').addClass('btn_gray');
      }
      $(this).unbind('click').bind('click',function(){
        $(this).parent().parent().find('p a.btn_green').addClass('btn_gray').removeClass('btn_green');
        $(this).addClass('btn_green').removeClass('btn_gray');
        $.getJSON('../public/setUserWordtestanswer',{homeworkid:homeworkid,studentid:studentid,classid:classid,typeid:wordinfo.typeid,questionid:wordinfo.quesid,wordid:wordinfo.wordid,useranswer:$(this).attr('useranswer'),ran:Math.random()},function(result){
            $('#cardwy'+wordid).addClass('complete');
          });
      });
   });
    //$('.numkeyboard').val('');
  }
  if(type ==3){
    var wordid = wordinfo.wordid;
    //$('.wordwp .explains').html((index+1)+'. '+wordinfo.explains);
    $('.wordwx .total').html(length);
    $('.wordwx .curindex').html(index+1);
    $('.wordwx .tingli').html((index+1)+'.<span></span>'+'<a href="javascript:void(0);" class="sound_single" mp3="'+wordinfo.mp3+'"></a>');
    $('.wordwx span[type=option]:eq(0)').html(wordinfo.option_a);
    $('.wordwx span[type=option]:eq(1)').html(wordinfo.option_b);
    $('.wordwx span[type=option]:eq(2)').html(wordinfo.option_c);
    $('.wordwx a[type=option]').each(function(){
      if($(this).attr('useranswer') == wordinfo.useranswer){
        $(this).removeClass('btn_gray').addClass('btn_green');
      }
      else{
        $(this).removeClass('btn_green').addClass('btn_gray');
      }
      $(this).unbind('click').bind('click',function(){
        $(this).parent().parent().find('p a.btn_green').addClass('btn_gray').removeClass('btn_green');
        $(this).addClass('btn_green').removeClass('btn_gray');
        $.getJSON('../public/setUserWordtestanswer',{homeworkid:homeworkid,studentid:studentid,classid:classid,typeid:wordinfo.typeid,questionid:wordinfo.quesid,wordid:wordinfo.wordid,useranswer:$(this).attr('useranswer'),ran:Math.random()},function(result){
          $('#cardwx'+wordid).addClass('complete');
          });
      });
   });
    //$('.numkeyboard').val('');
  }
  if(type ==4){
    $('.textalound .mp3').removeAttr('mp3').attr('mp3',wordinfo.mp3);
    $('.textalound').attr('readid',wordinfo.textreadid);
    $('.textalound').attr('readcontent',wordinfo.encontent);
    $('.textalound').attr('textid',wordinfo.textid);
    $('.textalound .total').html(length);
    $('.textalound .curindex').html(index+1);
    $('.textalound h1').html(wordinfo.chaptername+' ，共'+wordinfo.chapternum+'节课文，'+length+'小题（每题1分）。');
    $('.textalound .gendu_tit strong').html(wordinfo.encontent);
    $('.textalound .gendu_miaoshu').html(wordinfo.cncontent);
    $('.textalound .zcol-1h h2').html((index+1)+'.');
    if(wordinfo.userscore != null && wordinfo.userscore !="null"){
      $('.textalound .tarecordmp3').removeAttr('mp3').attr('mp3',wordinfo.usermp3);
      $('.textalound .tarecordmp3').removeAttr('recordid').attr('recordid',wordinfo.recordid);
      $('.textalound .btn_cs').html('得分：<font class=" redFon">'+wordinfo.userscore+'分</font>');
      $('.textalound .btn_cs').show();
      $('.tarecordmp3').parent().show();
    }
    else{
      $('.textalound .btn_cs').hide();
      $('.tarecordmp3').parent().hide();
    }
    sendToActionScript('');
  }
}
function change_title(curindex,name,length){
  //console.log('curindex:'+curindex+'==name:'+name+"==length:"+length);
  //如果只有一个单词
  if(length == 1){
      //如果前后都没有栏目
        if($('a.aBtn[selecttype='+name+']').prev().length == 0 && $('a.aBtn[selecttype='+name+']').next().length == 0 ){
          $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
          $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
        }
        //如果前边没有栏目，后边有栏目
         else if($('a.aBtn[selecttype='+name+']').prev().length == 0 && $('a.aBtn[selecttype='+name+']').next().length != 0 ){
            $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
            $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html($('a.aBtn[selecttype='+name+']').next().html()+' <i class="fa fa-hand-o-right" aria-hidden="true"></i>').unbind('click').bind('click',function(){
              $('a.aBtn[selecttype='+name+']').next().click();
            });
        }
        //如果边前有栏目，后边没有栏目
        else if($('a.aBtn[selecttype='+name+']').prev().length != 0 && $('a.aBtn[selecttype='+name+']').next().length == 0 ){
          $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
          $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html('<i class="fa fa-hand-o-left" aria-hidden="true"></i> '+$('a.aBtn[selecttype='+name+']').prev().html()).unbind('click').bind('click',function(){
            $('a.aBtn[selecttype='+name+']').prev().click();
          });
        }
        else{
          $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html('<i class="fa fa-hand-o-left" aria-hidden="true"></i> '+$('a.aBtn[selecttype='+name+']').prev().html()).unbind('click').bind('click',function(){
            $('a.aBtn[selecttype='+name+']').prev().click();
          });
          $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html($('a.aBtn[selecttype='+name+']').next().html()+' <i class="fa fa-hand-o-right" aria-hidden="true"></i>').unbind('click').bind('click',function(){
              $('a.aBtn[selecttype='+name+']').next().click();
            });
        }
  }
  //如果有多个单词
  else{
    //如果是第一个单词
    if(curindex == 0){
        $('.'+name).find('a[action=next]').html('下一题 <i class="fa fa-hand-o-right" aria-hidden="true"></i>').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').unbind('click').bind('click',function(){
         word_prev_netx($(this));
        });
              //如果前边没有栏目
        if($('a.aBtn[selecttype='+name+']').prev().length == 0 ){
            $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
        }
        else{
          $('.'+name).find('a[action=prev]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html('<i class="fa fa-hand-o-left" aria-hidden="true"></i> '+$('a.aBtn[selecttype='+name+']').prev().html()).unbind('click').bind('click',function(){
            $('a.aBtn[selecttype='+name+']').prev().click();
          });
        }
    }
    //最后一个单词
    else if(curindex == (length-1)){
       $('.'+name).find('a[action=prev]').html('<i class="fa fa-hand-o-left" aria-hidden="true"></i> 上一题').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').unbind('click').bind('click',function(){
         word_prev_netx($(this));
      });
      if($('a.aBtn[selecttype='+name+']').next().length == 0 ){
        $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_green').addClass('btn_gray').unbind('click');
      }
      else{
         $('.'+name).find('a[action=next]').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').html($('a.aBtn[selecttype='+name+']').next().html()+' <i class="fa fa-hand-o-right" aria-hidden="true"></i>').unbind('click').bind('click',function(){
              $('a.aBtn[selecttype='+name+']').next().click();
            });
      }
    }
    else{
      $('.'+name).find('a[action=prev]').html('<i class="fa fa-hand-o-left" aria-hidden="true"></i> 上一题').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').unbind('click').bind('click',function(){
         word_prev_netx($(this));
      });
      $('.'+name).find('a[action=next]').html('下一题 <i class="fa fa-hand-o-right" aria-hidden="true"></i>').removeAttr('onclick').removeClass('btn_gray').addClass('btn_green').unbind('click').bind('click',function(){
         word_prev_netx($(this));
        });

    }
  }

}
function reading(obj,flag){
  //console.log(wordmp3url);
  mp.clear();
   var playimg = '';
   if($(obj).find("img").length != 0){
       playimg = $(obj).find("img").attr('src');
       var _playimg =playimg.replace('png','gif');
      $(obj).find("img").attr('src',_playimg);
   }
   else{
    $(obj).find('a').addClass('active');
   }
   if(flag==0){
     var mp3=$(obj).find("a").attr("mp3");
     mp.play(wordmp3url +  mp3);
   }else if(flag==1){
     var mp3=$(obj).find("a").attr("mp3");
     mp.play(textmp3url +  mp3.substr(0,2) + "/" + mp3 + ".mp3");
   }
   else if(flag==2){
     var usermp3=$(obj).find("a").attr("mp3");
     var recordid=$(obj).find("a").attr("recordid");
     var recordtype = $(obj).find("a").attr("recordtype");
     $.getJSON('../public/playBack',{id:recordid,type:recordtype},function(result){
      //console.log(result.length);
        mp.play(recordmp3url + result.filename);
        $("#jplayer").bind($.jPlayer.event.ended,function(event){
        if(playimg != ''){
           $(obj).find("img").attr('src',playimg);
        }
        else{
           $(obj).find("a").removeClass('active');
        }     
        });
     });
   }
   $("#jplayer").bind($.jPlayer.event.ended,function(event){
    if(playimg != ''){
       $(obj).find("img").attr('src',playimg);
    }
    else{
       $(obj).find("a").removeClass('active');
    }     
    });
}

//拼写样式修改
function inputsty(obj){
//   $(obj).parent().find("input.cur").removeClass("cur");
// //  $(".wt").removeClass("cur");
//   $(obj).addClass("cur");
}

//拼写试题的提交
function px(obj){
  //若干部存在焦点了才进行单击问题
  if($(obj).parents(".testContent").find("input.cur").length>0){
    if($(obj).hasClass("btn_gray")){
      $(obj).parents(".testContent").find(".cur").val("");
      var word=$(obj).text();
      $(obj).parents(".testContent").find(".cur").val(word);
      //计算他是第几个
      var index=$(obj).parents(".testContent").find(".wt.cur").parents("ul").find(".wt").index($(obj).parents(".testContent").find(".cur")[0]);
      $(obj).parent().find("a[index='"+index+"']").removeClass("btn_green").addClass("btn_gray");
      $(obj).parents(".testContent").find(".cur").attr("index",index);
      $(obj).attr("index",index);
      $(obj).removeClass("btn_gray").addClass("btn_green");
      var len=$(obj).parents(".testContent").find(".cur").parents("ul").find(".wt").length-1;
      if(index<len){
        $(obj).parents(".testContent").find(".cur").removeClass("cur");
        $(obj).parents(".testContent").find("input.wt:eq("+(index+1)+")").addClass("cur");
        $(obj).parents(".testContent").find("input.wt:eq("+(index+1)+")").focus();
      }
    }
  }
}