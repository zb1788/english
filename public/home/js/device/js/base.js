// 获取URL中param参数的值
function getParameter(param) {
    var query = window.location.search;
    var iLen = param.length;
    var iStart = query.indexOf(param);
    if (iStart == -1)
        return null;
    iStart += iLen + 1;
    var iEnd = query.indexOf("&", iStart);
    if (iEnd == -1)
        return query.substring(iStart);
    return query.substring(iStart, iEnd);
}
var stem_isplay = false;
var child_stem_isplay = false;
var que_isplay = false;
var stoptimes = 1; //停顿时间
var mp3_progress = '';
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
    oplay.stemindex = 0;
    oplay.queinitindex = 0;
    oplay.questionindex = 0;
    oplay.childinitstemindex = 0;
    oplay.childstemindex = 0;
    oplay.single_tts_initindex = 0;
    oplay.single_ttsindex = 0;
    oplay.url = "";
    oplay.repeat = 1; //默认播放次数
    oplay.curpeat = 1;//当前播放到第几次
    oplay.play = function(mp3) {
        oplay.clear();
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    oplay.clear = function() {
        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    oplay.initindex = function(){
        $('.playBtn').removeClass('active');
        oplay.index = 0;
        oplay.stemindex = 0;
        oplay.queinitindex = 0;
        oplay.questionindex = 0;
        oplay.childinitstemindex = 0;
        oplay.childstemindex = 0;
        oplay.single_tts_initindex = 0;
        oplay.single_ttsindex = 0;
    }
    return oplay;
}
var isReceiveSignal = false;//是否接受答题器信号
var isObjective = false; //当前题目是否是客观题
var isRightWrongQues = false; //当前题目是否是判断题
var examsid = '';//试卷ID
var Baseurl = '';
var ListenConter = Baseurl + '/Device/';//听力训练控制器访问地址
var questionInfo = [];//试题列表的数组
var anMaps = {}; //所有学生本次作答答题结果的json对象
var anMapsHis = {}; //所有学生历史答题结果的json对象
var answerOptions = []; //试题答案选项的json数组
var tempanswerOptions = []; //试题答案选项的json临时数组
var itemsOptions = []; //试题选项的json数组
var answered_stu_num = 0;//当前题目已经答题的学生人数
var totalPage = 0;
var currentPage = 0;
var pageSelectID = 'pagebar1';
var saveAnswerUrl = ListenConter + 'saveAnswerUrl';
var saveDeviceConfUrl = ListenConter + 'save_device_conf';
var mp = '';
var rightAnswer = '';
var questionScore = '';
var questionType = '';
var timecode = '';
//去除HTML tag
function removeHTMLTagimg(str) {
    if(str != ''){
        str=str.replace('/uploads',resource_path+'/uploads');
    }
    //alert(str);
    return str;
}
//去除HTML tag
function removeHTMLTaginput(str) {
    if(str != ''){
        str=str.replace('/uploads',resource_path+'/uploads');
    }
   
    return str;
}
//替换填空题题干答案标识为input框
function content_replace(content){
    var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
    if(content != ''){
        
     content = content.replace(patt,'_____');   
    }
    return content;
}
//获取该套试卷下的详细信息
function loadQuestion(page) {
    //alert(page);
	mp = new myplay();
	$("#toolbar p.switch").toggle(
            function() {
                $(this).parent().removeClass("right").addClass("left");
            },
            function() {
                $(this).parent().removeClass("left").addClass('right');
            })

    $("#student ul > li").click(function() {
        $(this).siblings().removeClass("show");
        if (!$(this).children("a").hasClass("none")) {
            if ($(this).hasClass("show")) {
                $(this).removeClass("show");
            } else {
                $(this).addClass("show");
            }
        }
    });
    $("#toolbar p").click(function() {
        var studentsLi = $("#student ul > li");
        if (studentsLi.hasClass("show")) {
            studentsLi.removeClass("show");
        }
    });
    //设置答题人数窗口里学生列表的高度，防止低分辨率时学生显示不全
    var height = $("ul.stuList").height();
    var maxHeight = Math.round($(window).height() * 0.55);
    if (height > maxHeight) {
        $("ul.stuList li").css("width", "12.0%");
        $("ul.stuList").height(maxHeight);
        $("ul.stuList").width($("ul.stuList").width() + 14);
        $("ul.stuList").css("overflow-y", "scroll");
	}

    $.getJSON(ListenConter + 'ListeShowTv', {examsid: examsid,studentClassid:studentClassid,sum_student:sum_student,from_platform:from_platform}, function(result) {
        questionInfo = result;
        totalPage = questionInfo.length; //试卷总页数
        //alert(result.length);
        showQuestion(page);
        //alert(questionInfo[0].mp3);
    });   //alert(examsid);
}
//
function showQuestion(page) {
    //alert(page);
    currentPage = parseInt(page);
    totalPage = 0 == totalPage ? 1 : totalPage;
    var next, prev = currentPage;
    //alert(prev);
    prev = currentPage - 1;
    next = currentPage + 1;
    //当前的条数和总条数
    $('.tips').html(currentPage + "/" + totalPage);
    if (currentPage == 1) {
        prev = currentPage;
        $('#'+pageSelectID+' [p=prev]').addClass('nodisplay');
    } else {
        $('#'+ pageSelectID+' [p=prev]').removeClass('nodisplay').attr('page', prev);
    }

    // 显示下一页
    if (currentPage < totalPage) {
        $('#'+pageSelectID+' [p=next]').removeClass('nodisplay').attr('page', next);

    } else {
        $('#'+ pageSelectID+' [p=next]').addClass('nodisplay');
    }
    if (questionInfo.length == 0) {//没有试题时的处理
        $('.aBtn a[data-reveal-id="student"]').removeAttr("data-reveal-id").attr("data-reveal-newId", "student");
        $('.aBtn a[data-reveal-newId="student"]').attr("class", "close-student nodisplay");
        $('.renshu').hide();
        $('.aBtn a[data-reveal-id="result"]').removeAttr("data-reveal-id").attr("data-reveal-newId", "result");
        $('.aBtn a[data-reveal-newId="result"]').attr("class", "close-student nodisplay");
        $('.aBtn a[data-reveal-id="answer"]').removeAttr("data-reveal-id").attr("data-reveal-newId", "answer");
        $('.aBtn a[data-reveal-newId="answer"]').attr("class", "close-student nodisplay");
        $('.aBtn a[data-reveal-id="paperResult"]').removeAttr("data-reveal-id").attr("data-reveal-newId","paperResult");
        $('.aBtn a[data-reveal-newId="paperResult"]').attr("class","close-student nodisplay");
        $('.aBtn a[data-reveal-id="config"]').removeAttr("data-reveal-id").attr("data-reveal-newId","config");
        $('.aBtn a[data-reveal-newId="config"]').attr("class","close-student nodisplay");
        $('#startorstop').addClass("nodisplay").attr("href", "javascript:void(0);");
        setStartBtnStatus("start");
        return;
    }
    //上一页下一页click事件
    $('#'+pageSelectID+' a').unbind('click');
    $('#'+pageSelectID+' a:not(.nodisplay)').one('click', function() {
        isReceiveSignal = false;//点击换页后先不允许接收答题信号，直到新题读出来后再判断是否接收信号
        clearTimeout(timer);
        //alert($('#startorstop').data("status"));
        // if ($('#startorstop').data("status")=="stop") {
        //     //alert("sss");
        //     saveAnswer();//保存答题结果
        // }
        saveAnswer();//保存答题结果
        //隐藏弹出窗
        $("a[data-reveal-id]").each(function(){
            var modal = $("#"+$(this).attr("data-reveal-id"));
            modal.css({'visibility' : 'hidden'});
            $('.student-bg').css({'display' : 'none'}); 
            //处理设置窗口
            if(modal.attr("id")=="config"){
                restoreConf();
            }
        });
        //隐藏答题分析柱图学生名单弹窗
        $("#studentStat").css("visibility","hidden");
        $(".bg-on-student").hide();
        mp.repeat = 1; //默认播放次数
        mp.curpeat = 1;//当前播放到第几次
        mp.clear();
        clearTimeout(mp3_progress);
        $('#mp3play').removeClass('active');
        showQuestion($(this).attr('page'));
    });
    //试题展示
    var content = '';
	var tcontent = '';
    var zhishidian = '';
    var nandu = '';
	rightAnswer = '';
    var duwenque = '';
    if(questionInfo[currentPage - 1].mp3type == '1'){
        $('#mp3play').hide();
    }
    else{
        $('#mp3play').show();
    }
	isObjective = questionInfo[currentPage - 1].isObjective;
    questionScore = questionInfo[currentPage - 1].question_score;
    questionType = questionInfo[currentPage - 1].typeid;
    if(questionInfo[currentPage - 1].stem_type == '1'){
       
       for(var i = 0; i< questionInfo[currentPage - 1].answer.length; i++){
        if(isObjective){
            rightAnswer += questionInfo[currentPage - 1].answer[i]['answer'];
        }
        else{
            rightAnswer += questionInfo[currentPage - 1].answer[i].answer_num+'.'+questionInfo[currentPage - 1].answer[i].answer+'  ';
        }
       }
       if(questionInfo[currentPage - 1].que_key1 == null){
        zhishidian = '无';
       }
       else{
        zhishidian = questionInfo[currentPage - 1].que_key1;
       }
       if(questionInfo[currentPage - 1].que_key4 == null){
        nandu = '一般';
       }
       else{
        nandu = questionInfo[currentPage - 1].que_key4;
       }
      if (questionInfo[currentPage - 1].typeid == 1) {  //选择题展示
            itemsOptions = questionInfo[currentPage - 1].items;

            content += '<div class="shux"><h2 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[currentPage - 1].id + '">单项选择题 </h2></div>';
            content += '<div class="neir fixck bigger_150" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(215, 215, 215); zoom: 1.5;">';
            // if (questionInfo[currentPage - 1].itemtype == 0) {    //选项是文字
            
                // if(questionInfo[currentPage - 1].tcontent == ''){
                //     content += removeHTMLTagimg(questionInfo[currentPage - 1].papertitle);
                // }
                // else{
                //     content += content_replace(removeHTMLTagimg(questionInfo[currentPage - 1].tcontent));
                // }
                content += '<div>'+removeHTMLTagimg(questionInfo[currentPage - 1].papertitle)+'</div>';
                 content += '<div class="tg">'+content_replace(removeHTMLTagimg(questionInfo[currentPage - 1].tcontent))+'</div>';
                content += '<div class="options">';
                for (var i = 0; i < itemsOptions.length; i++) {
                    if (questionInfo[currentPage - 1].itemtype == 0) {    //选项是文字
                       content += '<p><span class="option">' + itemsOptions[i].flag + '.</span>' + itemsOptions[i].content+'</p>';
                    }
                    else{
                       content += '<span class="option">' + itemsOptions[i].flag + '.</span><img class="lazyload" src="/public/home/js/device/images/loading.gif" data-original="'+resource_path+'/uploads/' + itemsOptions[i].content + '" width="120" height="90">&nbsp;&nbsp;';
                    }
                }
                content += '</div>';
            content += '</div>';
            $("#inValidMemo").html("注：无效作答指含有选项以外的作答及选择多个答案的作答。");
        }
        else if(questionInfo[currentPage - 1].typeid == 3){   //判断题展示       
            tcontent = content_replace(removeHTMLTagimg(questionInfo[currentPage - 1].tcontent));
            content += '<div class="shux"><h2 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[currentPage - 1].id + '">判断题 </h2></div>';
            content += '<div class="neir fixck bigger_150" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(215, 215, 215); zoom: 1.5;">';
            content += questionInfo[currentPage - 1].papertitle;
            //alert(tcontent);
            content += '<div class="tg">'+tcontent+'</div>';
            content += '<div class="options">';
            content += '<span class="option">√</span>&nbsp;&nbsp;<span class="option">×</span>';
            // content += '<a href="javascript:;" class="sound_single" mp3="' + questionInfo[currentPage - 1].mp3 + '"></a>';
            content += '</div>';
            content += '</div>';
            $("#inValidMemo").html("注：无效作答指含有选项以外的作答。");
        }
        else if(questionInfo[currentPage - 1].typeid == 2){   //填空题展示       
            tcontent = removeHTMLTagimg(content_replace(questionInfo[currentPage - 1].tcontent));
            content += '<div class="shux"><h2 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[currentPage - 1].id + '">填空题 - 英语  - 难度：'+nandu+'</h2></div>';
            content += '<div class="neir fixck bigger_150" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(215, 215, 215); zoom: 1.5;">';
            content += questionInfo[currentPage - 1].papertitle;
            content += '<div class="tg">'+tcontent+'</div>';
            content += '</div>';
            $("#inValidMemo").html("注：无效作答指含有选项以外的作答。");
        }
        else{               //排序题
             itemsOptions = questionInfo[currentPage - 1].items;

            content += '<div class="shux"><h2 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[currentPage - 1].id + '">排序题 </h2></div>';
            content += '<div class="neir fixck bigger_150" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(215, 215, 215); zoom: 1.5;">';
            // if (questionInfo[currentPage - 1].itemtype == 0) {    //选项是文字
            
                if(questionInfo[currentPage - 1].tcontent == ''){
                    content += removeHTMLTagimg(questionInfo[currentPage - 1].papertitle);
                }
                else{
                    content += content_replace(removeHTMLTagimg(questionInfo[currentPage - 1].tcontent));
                }
                content += '<div class="options">';
                for (var i = 0; i < itemsOptions.length; i++) {
                    if (questionInfo[currentPage - 1].itemtype == 0) {    //选项是文字
                       content += '<span class="option">' + itemsOptions[i].flag + '.</span>' + itemsOptions[i].content+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                    else{
                       content += '<span class="option">' + itemsOptions[i].flag + '.</span><img class="lazyload" src="/public/home/js/device/images/loading.gif" data-original="'+resource_path+'/uploads/' + itemsOptions[i].content + '" width="120" height="90">&nbsp;&nbsp;';
                    }
                }
                content += '<br>';
                for(var k = 0; k< questionInfo[currentPage - 1].answer.length; k++){
                    content += '<span class="option">' + questionInfo[currentPage - 1].answer[k].answer_num + '.</span>_____&nbsp;&nbsp;';
                }
                content += '</div>';
            content += '</div>';
        }
    }
    else{
        duwenque = questionInfo[currentPage - 1].question;
        $(duwenque).each(function(a,duval){
            $(duval.answer).each(function(b,avalue){
                if(avalue.answer == 0) avalue.answer = "×";
                if(avalue.answer == 1) avalue.answer = "√";
                rightAnswer += avalue.answer_num+'.'+avalue.answer+'  ';
            });
        });

        //alert(rightAnswer);
       if(questionInfo[currentPage - 1].que_key1 == null){
        zhishidian = '无';
       }
       else{
        zhishidian = questionInfo[currentPage - 1].que_key1;
       }
       if(questionInfo[currentPage - 1].que_key4 == null){
        nandu = '一般';
       }
       else{
        nandu = questionInfo[currentPage - 1].que_key4;
       }
       content += '<div class="shux"><h2 id="qutesion" answer="' + rightAnswer + '" queid="' + questionInfo[currentPage - 1].id + '">'+removeHTMLTagimg(questionInfo[currentPage - 1].papertitle)+'</h2></div>';
        var dutcontent = removeHTMLTagimg(questionInfo[currentPage - 1].content);
         content += '<div class="neir fixck bigger_150" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(215, 215, 215); zoom: 1.5;">';
        content += '<div class="tg">'+dutcontent+'</div>';
        $(duwenque).each(function(c,cvalue){
            if(cvalue.tcontent != ''){
                content += '<p>'+cvalue.question_num+'. '+content_replace(removeHTMLTagimg(cvalue.tcontent))+'</p>';
            }
            else{
                //content += cvalue.question_num+'&nbsp;&nbsp;&nbsp;';
            }
            if(cvalue.typeid == '4'){
                itemsOptions = cvalue.items;
                content += '<div class="options">';
                for (var j = 0; j < itemsOptions.length; j++) {
                    if (cvalue.itemtype == 0) {    //选项是文字
                       content += '<p><span class="option">' + itemsOptions[j].flag + '.</span>' + itemsOptions[j].content+'</p>';
                    }
                    else{
                       content += '<span class="option">' + itemsOptions[j].flag + '.</span><img class="lazyload" src="/public/home/js/device/images/loading.gif" data-original="'+resource_path+'/uploads/' + itemsOptions[j].content + '" width="120" height="90">&nbsp;&nbsp;';
                    }
                }
                //content += '<br>';
                $(duwenque).each(function(a,duval){
                    $(duval.answer).each(function(b,avalue){
                        content += '<span class="option">' + avalue.answer_num + '.</span>_____&nbsp;&nbsp;';
                    });
                });
                content += '</div>';
            }
            else if(cvalue.typeid == '1'){  //选择题
                itemsOptions = cvalue.items;
                content += '<div class="options">';
                if(cvalue.tcontent == ''){
                    content += cvalue.question_num+'.&nbsp;&nbsp;&nbsp;';
                }
                for (var j = 0; j < itemsOptions.length; j++) {
                    if (cvalue.itemtype == 0) {    //选项是文字
                       content += '<span class="option">' + itemsOptions[j].flag + '.</span>&nbsp;' + itemsOptions[j].content+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                    else{
                       content += '<span class="option">' + itemsOptions[j].flag + '.</span>&nbsp;<img class="lazyload" src="/public/home/js/device/images/loading.gif" data-original="'+resource_path+'/uploads/' + itemsOptions[j].content + '" width="120" height="90">&nbsp;&nbsp';
                    }
                }
                content += '</div>';
               
            }
            else if(cvalue.typeid == '3'){  //判断题
               
                content += '<div class="options">';
                content += '<span class="option">√</span>&nbsp;&nbsp;<span class="option">×</span>';
                content += '</div>';
               
            }
        });
        content += '</div>';
    }
    $('#realContent').html(content);
    $('.tg img').removeAttr('width').removeAttr('height').removeAttr('style');
    var _imgsrc='';
    var _width='';
    $('.tg img').each(function(){
        _width = $(this).width();
        _height = $(this).height();
        _imgsrc = $(this).attr('src');
        if(_width > 300){
            _width=_width/2;
            
        }
        else{
            _width=_width*2;
            }
        $(this).width(_width);
        // if(_height > 100){
        //     _height=_height/2;
        //     $(this).height(100);
        // }
       
        $(this).attr('src','/public/home/js/device/images/loading.gif').attr('data-original',_imgsrc).attr('class','lazyload');
        
    });

    $("img.lazyload").lazyload();//

    $('#mp3play').unbind('click').click(function(){     //音频播放事件
            clearTimeout(mp3_progress);
            if($(this).hasClass('active')){
                mp.pause();
                $(this).removeClass('active');
            }
            else{
                $(this).addClass('active');
                question_init((currentPage-1));
            }
    });
    isReceiveSignal = false;//重置是否接收答题信号
    anMaps = {};//重置本次作答答题结果json对象
    anMapsHis = {};//重置历史作答答题结果json对象
    answered_stu_num = 0;//重置已答题人数
	answerOptions = [];
    //重置"答题人数"窗口中显示的学生答题结果
    $("#student ul.stuList a").attr("class","none").attr("answerStatus","0").attr("answerResult","0");
    $("#student ul.stuList a span").text("");
    //显示学生历史答题的结果开始
    //alert('11');
    if(questionInfo[currentPage - 1].stem_type == '1' && isObjective){
       // alert('22');
        $.getJSON(ListenConter + 'getanMapsHis', {examsid: examsid,from_platform:from_platform,studentClassid:studentClassid,queid:questionInfo[currentPage - 1].id,random:Math.random()},function(result){
           // console.log(ListenConter + 'getanMapsHis?examsid='+examsid+'&from_platform='+from_platform+'&studentClassid='+studentClassid+'&queid='+questionInfo[currentPage - 1].id);
           //alert("aaa");
          //  console.log(result.length);
            anMapsHis = result;     //设置学生历史答题结果的json对象
            tempanswerOptions = questionInfo[currentPage - 1].itemsoption;//设置试题答案选项的json数组
        for(var i=0;i<tempanswerOptions.length;i++){
            answerOptions[i]=tempanswerOptions[i]['flag'];
            //alert(tempanswerOptions[i]['flag']);
        }
        //alert(answerOptions[0]);
        //将学生历史答题结果显示在"答题人数"窗口中
        var inValidClass="bgYellow";
        var rightClass="bgcGreen";
        var wrongClass="bgcRed";
        if(RESULT_SHOW_TYPE==MODULEID_MAP['option_rs_simple']){
            inValidClass="bgcGreen";
            rightClass="bgcGreen";
            wrongClass="bgcGreen";
        }
        for(var student_id in anMapsHis){
            var stuAnswerObj = anMapsHis[student_id];
            var stuAnswer = stuAnswerObj.answer;
            //alert(student_id);
            if($("#answer_"+student_id).length>0 ){//有该学生才做处理，防止学生换班的情况；没作答不做处理
               // alert('ss');
                answered_stu_num++;
                var answer_result = stuAnswerObj.answer_result;
                //var answer_status = stuAnswerObj.answer_status;
                //判断答案是否无效：选择候选项以外的选项、单选题选多个
                var inValidFlag = false;
                var stuAnswerArr = stuAnswer.split("");
                if(stuAnswerArr.length>1 || stuAnswerArr == ''){
                    inValidFlag = true;
                }else{
                    for(var i=0;i<stuAnswerArr.length;i++){
                        if($.inArray(stuAnswerArr[i], answerOptions)==-1){
                        inValidFlag = true;
                        break;
                        }
                    }
                }
                //alert(inValidFlag);
                if(inValidFlag){
                    $("#answer_"+student_id).attr("class",inValidClass).attr("answerStatus","1").attr("answerResult","99");
                }
                else{
                    if(answer_result=="100"){//答题正确
                        $("#answer_"+student_id).attr("class",rightClass).attr("answerStatus","2").attr("answerResult","100");
                    }
                    else{//答题错误
                        $("#answer_"+student_id).attr("class",wrongClass).attr("answerStatus","2").attr("answerResult","99");
                    }
                }
                if(stuAnswer == 0) stuAnswer = "×";
                if(stuAnswer == 1) stuAnswer = "√";
                 document.getElementById("span_"+student_id).innerHTML="("+stuAnswer+")";
            }
        }
            document.getElementById("answered_stu_num").innerHTML = answered_stu_num;//设置当前题目已经答题的人数
            //设置答题结果页面的答题人数
            $("#rightCount").text($("#student ul.stuList a[answerResult='100']").length);
            $("#wrongCount").text($("#student ul.stuList a[answerStatus='2'][answerResult='99']").length);
            $("#inValidCount").text($("#student ul.stuList a[answerStatus='1']").length);
            $("#noAnswerCount").text($("#student ul.stuList a[answerStatus='0']").length);
            if(timer){
                    clearTimeout(timer);
            }
            //显示学生历史答题的结果结束
            $('.aBtn a[data-reveal-newId="student"]').removeAttr("data-reveal-newId").attr("data-reveal-id", "student");
            $('.aBtn a[data-reveal-id="student"]').attr("class", "close-student");
            $('.renshu').show();
            $('.aBtn a[data-reveal-newId="result"]').removeAttr("data-reveal-newId").attr("data-reveal-id", "result");
            $('.aBtn a[data-reveal-id="result"]').attr("class", "close-student");
            $('#startorstop').addClass("nodisplay").unbind("click");
            setStartBtnStatus("start");
            //是客观题显示按钮
                                    if(isObjective){//客观题
                                        $('.renshu').show();
                                        $('.aBtn a[data-reveal-newId="student"]').removeAttr("data-reveal-newId").attr("data-reveal-id","student");
                                        $('.aBtn a[data-reveal-id="student"]').attr("class","close-student");
                                        $('.aBtn a[data-reveal-newId="result"]').removeAttr("data-reveal-newId").attr("data-reveal-id","result");
                                        $('.aBtn a[data-reveal-id="result"]').attr("class","close-student");
                                        if(answered_stu_num==0){
                                            //无历史答题结果就默认开始答题
                                            if($('#startorstop').data("status")=="start"){

                                                // if(VcomAQTool && ("Start" in VcomAQTool)){
                                                //    // alert('sss');
                                                //      VcomAQTool.Start();
                                                // }
                                                start();
                                            }
                                            isReceiveSignal = true;
                                            $('#startorstop').addClass("nodisplay").unbind("click");
                                            setStartBtnStatus("stop");
                                            timer = setTimeout(function(){
                                                $('#startorstop').removeClass("nodisplay").unbind("click").bind("click",startorstop);
                                            },disableTime);
                                        }else{
                                            //有历史答题结果就不接收答题信号

                                            stop();
                                            isReceiveSignal = false;
                                            $('#startorstop').removeClass("nodisplay").unbind("click").bind("click",startorstop);
                                            setStartBtnStatus("start");
                                        }
                                    }else{//主观题
                                        isReceiveSignal = false;
                                        $("#pageTime").hide();
                                         if(PAGE_INTERVAL_ID){
                                                clearInterval(PAGE_INTERVAL_ID);
                                         }
                                        $('.renshu').hide();
                                        $('.aBtn a[data-reveal-id="student"]').removeAttr("data-reveal-id").attr("data-reveal-newId","student");
                                        $('.aBtn a[data-reveal-newId="student"]').attr("class","close-student nodisplay");
                                        $('.aBtn a[data-reveal-id="result"]').removeAttr("data-reveal-id").attr("data-reveal-newId","result");
                                        $('.aBtn a[data-reveal-newId="result"]').attr("class","close-student nodisplay");
                                        $('#startorstop').addClass("nodisplay").unbind("click");
                                        setStartBtnStatus("start");
                                    }

                //处理自动换页
                if(PAGE_INTERVAL_ID){
                    clearInterval(PAGE_INTERVAL_ID);
                }
                if(CHANGE_PAGE_TYPE==MODULEID_MAP['option_cp_interval']){
                        PAGE_COUNTDOWN_TIME = moment(PAGE_START_TIME);
                        $("#pageTime").text(PAGE_COUNTDOWN_TIME.format("mm:ss"));
                        $("#pageTime").show();
                        PAGE_INTERVAL_ID = setInterval(function(){
                            PAGE_COUNTDOWN_TIME.subtract(1, "seconds");
                            $("#pageTime").text(PAGE_COUNTDOWN_TIME.format("mm:ss"));
                            if(PAGE_COUNTDOWN_TIME.isSame(PAGE_END_TIME)){
                                if(PAGE_INTERVAL_ID){
                                    clearInterval(PAGE_INTERVAL_ID);
                                }
                                $("#pagebar1 a.next").click();
                            }
                        },1000);
                    
                }
        });
    
    }
    else{
        isReceiveSignal = false;
        $("#pageTime").hide();
         if(PAGE_INTERVAL_ID){
                clearInterval(PAGE_INTERVAL_ID);
         }
        setStartBtnStatus("start");
        $('.renshu').hide();
        $('.aBtn a[data-reveal-id="student"]').removeAttr("data-reveal-id").attr("data-reveal-newId","student");
        $('.aBtn a[data-reveal-newId="student"]').attr("class","close-student nodisplay");
        $('.aBtn a[data-reveal-id="result"]').removeAttr("data-reveal-id").attr("data-reveal-newId","result");
        $('.aBtn a[data-reveal-newId="result"]').attr("class","close-student nodisplay");
        $('#startorstop').addClass("nodisplay").unbind("click");
         
    }
		
	
	//setTimeout(function(){mp3Play($('.sound_single').attr('mp3'))},1000);
       // $(".bigger_150").css("zoom",zoom);
       // $(".tg").css("zoom",1);
       question_id=$("#qutesion").attr("queid");
}
//获取mp3文件路径
function getmp3url(mp3name,mp3type){
    //mp3name = mp3name.substr(0,mp3name.length-1);
    var mp3url = '';
    //console.log(mp3type);
    if(mp3type == 4){
        mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
    }
    else if(mp3type == 2){
        mp3url = text_mp3_url+mp3name.substr(0,2)+'/'+mp3name;
    }
    else{
        mp3url = word_mp3_url+mp3name;
    }
    return mp3url;
}
//小题播放初始化化
function question_init(mp3index){
    mp.questionindex = 0;
    clearTimeout(mp3_progress);
    var quettsdata = '';
    var tips = questionInfo[mp3index]['tips'];    //是否播放叮咚 0-不播 1播放
    quettsdata = questionInfo[mp3index]['tts'];
    var repeat = questionInfo[mp3index]['question_playtimes']; //播放次数
    var mp3type = questionInfo[mp3index]['mp3type']; //mp3类型
    if(mp.curpeat >1){ 
        mp.play('/public/home/js/di.mp3');  
        $("#jplayer").bind($.jPlayer.event.ended,function(event){
            mp3_progress = setTimeout(function(){
                question_play(quettsdata,repeat,mp3index,mp3type);
            },2000);
        });
    }
    else{
        if(tips == 1){
            mp.play('/public/home/js/dingdong.mp3');
            $("#jplayer").bind($.jPlayer.event.ended,function(event){
                setTimeout(function(){
                    question_play(quettsdata,repeat,mp3index,mp3type);
                },1000);
            });
        }
        else{
            question_play(quettsdata,repeat,mp3index,mp3type);
        }
          
    }
}
//小题播放
function question_play(quettsdata,repeat,mp3index,mp3type){
    clearTimeout(mp3_progress);
    var smallquetts = '';
    if(mp.questionindex < quettsdata.length){
        smallquetts = quettsdata[mp.questionindex];
        var playurl = getmp3url(smallquetts.tts_mp3,mp3type);
        mp.play(playurl);
        $("#jplayer").bind($.jPlayer.event.ended,function(event){
            mp.questionindex = mp.questionindex +1;
            if(mp.questionindex < quettsdata.length){
                mp3_progress = setTimeout(function(){
                question_play(quettsdata,repeat,mp3index,mp3type);    
                },parseInt(smallquetts.tts_stoptime)*1000);
            }
            else{
                    if (mp.repeat >= repeat){
                        mp.repeat = 1;
                        mp.curpeat = 1;
                        mp.clear();
                        clearTimeout(mp3_progress);
                        $('#mp3play').removeClass('active');
                    }
                    else{
                        mp.repeat = mp.repeat +1;
                        mp.curpeat = mp.curpeat +1;
                        mp3_progress = setTimeout(function(){
                            question_init(mp3index);
                        },2*1000);
                    }
                }
        });
    }
}
function bodyOnLoad() {
    $("#toolbar p.switch").toggle(
            function() {
                $(this).parent().removeClass("right").addClass("left");
            },
            function() {
                $(this).parent().removeClass("left").addClass('right');
            })

    $("#student ul > li").click(function() {
        $(this).siblings().removeClass("show");
        if (!$(this).children("a").hasClass("none")) {
            if ($(this).hasClass("show")) {
                $(this).removeClass("show");
            } else {
                $(this).addClass("show");
            }
        }
    });
    $("#toolbar p").click(function() {
        var studentsLi = $("#student ul > li");
        if (studentsLi.hasClass("show")) {
            studentsLi.removeClass("show");
        }
    });
    //设置答题人数窗口里学生列表的高度，防止低分辨率时学生显示不全
    var height = $("ul.stuList").height();
    var maxHeight = Math.round($(window).height() * 0.55);
    if (height > maxHeight) {
        $("ul.stuList li").css("width", "12.0%");
        $("ul.stuList").height(maxHeight);
        $("ul.stuList").width($("ul.stuList").width() + 14);
        $("ul.stuList").css("overflow-y", "scroll");
    }

    loadQuestion(1);
}
