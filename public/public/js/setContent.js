require.config({
　baseUrl: "../../public/public/js",
　paths: {
　　　"jquery": "jquery.min",
　},
  waitSeconds: 0
});
//文件主要是进行不同体型的展示
define(['jquery','mui',],function($,mui){ 
    var studentContent = {}; //推荐方式  
    //Dom元素初始化
    var initDom=function(dom,attrarr){
    	var dom=$(dom);
    	$.each(attrarr,function(key,value){
    		dom.attr(value.id,value.val);
    	});
    	return dom;
    }
    //第一题出现遮罩
    var create=function(b,t) {
        var c = document.createElement("div");
        c.innerHTML = t,
        c.setAttribute("class",b);
        return c
    }
    //$.pop.show("ttip","","ttip_wz","下面将开始听力训练");
    var show=function(a,ta,b,tb){
        var ttip=this.create(a,ta);
        var ttip_wz=this.create(b,tb);
        $(ttip).attr("style","background-color:#333; display:block; position:absolute; top: 0; left:0; color:#fff;  opacity:0.7; width:100%; display:none; box-shadow: 1px 1px 5px #555; text-align:center; z-index:1000;");
        $(ttip_wz).attr("style","width:100%;text-align:center;position:absolute; color:#fff; top: 40%; padding: 0 30px; z-index: 1001; font-size: 1.2em; line-height: 2em;");
        $(ttip).css('height',$(window).height());
        console.log(ttip);
        document.body.appendChild(ttip);
        document.body.appendChild(ttip_wz);
        $(ttip).fadeIn(300).delay(500).fadeOut(300);
        $(ttip_wz).fadeIn(300).delay(500).fadeOut(300);
    }
    //动态创建语音
    var setVoice=function(data,clickevent){
    	var attrarr=[];
    	var temp={};
    	temp.id="class";
    	temp.val="lanren cPlay istop";
    	attrarr.push(temp);
        temp={};
    	temp.id="id";
    	temp.val="lanren";
    	attrarr.push(temp);
        temp={};
    	temp.id="style";
    	temp.val="text-align:center;";
    	attrarr.push(temp);
        temp={};
        temp.id="quesid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        temp={};
        temp.id="playtimes";
        temp.val=data.questions_playtimes;
        attrarr.push(temp);
        temp={};
        temp.id="stoptimes";
        temp.val=data.stoptimes;
        attrarr.push(temp);
    	var div =initDom("<div></div>",attrarr);
        div.bind("tap",clickevent);
        //左边的波浪
    	attrarr=[];
        temp={};
        temp.id="id";
        temp.val="colorfulPulse";
        attrarr.push(temp);
        temp={};
        temp.id="class";
        temp.val="colorfulPulse";
        attrarr.push(temp);
    	var vdiv=initDom("<div></div>",attrarr);
        vdiv.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span><span class="item-5"></span><span class="item-6"></span><span class="item-7"></span><span class="item-8"></span><span class="item-9"></span><span class="item-10"></span><span class="item-11"></span><span class="item-12"></span><span class="item-13"></span><span class="item-14"></span>');
    	vdiv.appendTo(div);
        //中间的点击区域
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="edi-sy audio-btn play btnYuan radius100";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="audio-btn";
        attrarr.push(temp);
        temp={};
        temp.id="quesid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        temp={};
        temp.id="playtimes";
        temp.val=data.questions_playtimes;
        attrarr.push(temp);
        temp={};
        temp.id="stoptimes";
        temp.val=data.stoptimes;
        attrarr.push(temp);
        var ma=initDom("<a></a>",attrarr);
        ma.appendTo(div);
        ma.html('<i class="icon-uniE60C actY"></i>');
        
        //右边的波浪
        attrarr=[];
        temp={};
        temp.id="id";
        temp.val="colorfulPulse";
        attrarr.push(temp);
        temp={};
        temp.id="class";
        temp.val="colorfulPulse";
        attrarr.push(temp);
        var vdiv=initDom("<div></div>",attrarr);
        vdiv.html('<span class="item-1"></span><span class="item-2"></span><span class="item-3"></span><span class="item-4"></span><span class="item-5"></span><span class="item-6"></span><span class="item-7"></span><span class="item-8"></span><span class="item-9"></span><span class="item-10"></span><span class="item-11"></span><span class="item-12"></span><span class="item-13"></span><span class="item-14"></span>');
        vdiv.appendTo(div);
    	return div;
    }
    //听力训练选择题
    var choiceExamsQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        // temp.id="quescount";
        // temp.val=data.quescount;
        // attrarr.push(temp);
        temp.id="stemid";
        temp.val=data.question.stemid;
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        var stemdiv=$("<div></div>");
        stemdiv.addClass("tigan");
        stemdiv.attr("style","margin-top:10px;margin-left:10px;font-family: times;text-align:left;font-size:0.8em;color: #8f8f94;");
        //进行替换
        //进行图片的展示的情况
        // try{
        //     var objs=stemdiv.getElementsByClassName("tigan");
        //     $(objs).find("img").attr("style","width:80%;");
        //     //console.log(objs);
        //     // mui.each(objs,function(){
        //     //     try{
        //     //         var imgs=this.getElementsByTagName("img");
        //     //         mui.each(imgs,function(key,value){
        //     //                 // var currimg=this;
        //     //                 // var image = new Image();
        //     //                 // image.src = this.src;
        //     //                 // var naturalWidth=0;
        //     //                 // var naturalHeight=0;
        //     //                 // image.onload = function(){
        //     //                 //     var _stemp = this;
        //     //                 //     naturalWidth=_stemp.width;
        //     //                 //     naturalHeight=_stemp.height;
        //     //                 //     console.log(screenwidth);
        //     //                 //     if(naturalWidth>screenwidth){
        //     //                 //         currimg.style.width=(screenwidth)+"px";
        //     //                 //         currimg.style.height=(screenwidth*naturalHeight/naturalWidth)-10+"px";
        //     //                 //     }
        //     //                 // }

        //     //         });
        //     //     }catch(e){
        //     //     }
        //     // });
        // }catch(e){
            
        // }
        stemdiv.html(data.question.stemcontent);
        parentdiv.append(stemdiv);
    	//判断是否有音频1表示需要音频 0表示不需要音频
    	var voicediv="";
    	if(data.question.isvoice=='1'){
    		voicediv=setVoice(data.question,examsPlayEvent);
            parentdiv.append(voicediv);
    	}
    	//选择题展示
        
    	var div=$("<div></div>");
    	div.addClass("title tigan");
    	div.attr("style","margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;");
        div.html(data.question.tcontent);
        parentdiv.append(div);
    	var ul=$("<ul></ul>");
    	ul.addClass("mui-table-view xuanze");
    	ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        var answerflag="";
    	mui.each(data.question.questions_items,function(index,item){
    		var li=$("<li></li>");
    		li.addClass("mui-table-view-cell mui-media");
    		li.attr("quesid",data.question.id);
    		li.attr("itemflag",item.flag);
    		li.attr("answerid",data.answer[0].quesansid);
    		li.attr("homeworkid",data.question.homeworkid);
    		li.attr("examsid",data.question.examsid);
    		li.attr("quizid",data.question.quizid);
    		li.attr("typeid",data.question.typeid);
            li.bind("tap",choiceExamsEvent);
    		var a=$("<a></a>");
    		a.attr("quesid",data.question.id);
    		a.attr("itemflag",item.flag);
    		a.attr("answerid",data.answer[0].quesansid);
    		a.attr("homeworkid",data.question.homeworkid);
    		a.attr("examsid",data.question.examsid);
    		a.attr("quizid",data.question.quizid);
    		a.attr("typeid",data.question.typeid);
    		li.html(a);
    		//a.appendTo(li);
    		var div=$("<div></div>");
    		div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            if(data.question.itemtype=='1'){
    		    div.attr("style","float:left;position:absolute;line-height: 100px;");
            }

    		a.append(div);
    		var tdiv=$("<div></div>");
    		tdiv.addClass("items");
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            //tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.flag==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.flag==data.answer[0].answer&&item.content==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag!=data.answer[0].answer&&item.content==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag==data.answer[0].answer&&item.content!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }

            }
    		//tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            div.append(tdiv);
    		var span=$("<span></span>");
    		span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
    		span.attr("itemflag",item.flag);
            span.text(item.flag);
    		tdiv.append(span);
    		if(data.question.itemtype=='1'){
    			var imgdiv=$("<img></img>");
    			imgdiv.addClass("itemimg");
    			imgdiv.attr("style","float:left;margin-left:40px;");
    			imgdiv.attr("height","90px");
    			imgdiv.attr("width","120px");
    			imgdiv.attr("src",resource+"/uploads/"+item.content);
			}else{
				var imgdiv=$("<div></div>");
    			imgdiv.addClass("itemimg");
    			imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
    			imgdiv.html(item.content);
			}
			a.append(imgdiv);
			li.appendTo(ul);
    	});
        parentdiv.append(ul);
        if(issubmit=='1'){
            //个人作答情况
            if(type!=1&&studentid!=""){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是"+answerflag;
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案"+data.answer[0].answer;
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }
            //答案情况
            if(type==1){
                attrarr=[];
                temp={};0
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
                attrarr.push(temp);
                var userclassdiv=initDom("<div></div>",attrarr);
                userclassdiv.appendTo(parentdiv);
                attrarr=[];
                var userclassh5=initDom("<h5></h5>",attrarr);
                userclassh5.text("作答情况");
                userclassdiv.append(userclassh5);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view mui-table-view-chevron";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="margin-top: 10px;";
                attrarr.push(temp);
                var userclassul=initDom("<ul></ul>",attrarr);
                 userclassul.appendTo(userclassdiv);
                 var itemsarray=["A","B","C"];
                 for(var i=0;i<3;i++){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell mui-collapse";
                    attrarr.push(temp);
                    temp={};
                    temp.id="itemflag";
                    temp.val=itemsarray[i];
                    attrarr.push(temp);
                    temp={};
                    temp.id="quesid";
                    temp.val=data.question.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="homeworkid";
                    temp.val=data.question.homeworkid;
                    attrarr.push(temp);
                    var userclassli=initDom("<li></li>",attrarr);
                    userclassli.bind("tap",function(){
                        if(isOverdue!='false'){
                            return false;
                        }
                        var obj=$(this);
                        $(this).find("ul").empty();
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view mui-table-view-chevron";
                        attrarr.push(temp);
                        var itemuserul=initDom("<ul></ul>",attrarr);
                        $(this).append(itemuserul);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view-cell";
                        attrarr.push(temp);
                        var itemuserli=initDom("<li></li>",attrarr);
                        itemuserul.append(itemuserli);
                        var answer=$(this).attr("itemflag");
                        var quesid=$(this).attr("quesid");
                        var homeworkid=$(this).attr("homeworkid");
                        //主要进行ajax的数据获取
                        $.getJSON("getAnswerUserList",{classid:classid,answer:answer,quesid:quesid,homeworkid:homeworkid,typeid:"0",ran:Math.random()},function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    itemuserli.append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                            
                        });
                    });
                    userclassli.appendTo(userclassul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-navigate-right";
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(itemsarray[i]==answerflag){
                        temp.val="color: #2bc8a0;";
                    }else{
                        temp.val="color: #FE5A59;";
                    }
                    attrarr.push(temp);
                    var userclassa=initDom("<a></a>",attrarr);
                    userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                    userclassli.append(userclassa);
                }
            }
            


            //展示个人的数据
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
            attrarr.push(temp);
            var userdiv=initDom("<div></div>",attrarr);
            userdiv.appendTo(parentdiv);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="margin-bottom:10px;";
            attrarr.push(temp);
            var userh5=initDom("<h5></h5>",attrarr);
            userh5.text("班级数据");
            userdiv.append(userh5);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-grid-view";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="background-color: white;";
            attrarr.push(temp);
            var userul=initDom("<ul></ul>",attrarr);
            userdiv.append(userul);
            
            //展示作答人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("作答人次");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.answernum!=undefined){
                datanum=data.answer[0].summary.answernum;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示及格人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("正确率");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.accrate!=undefined){
                datanum=data.answer[0].summary.accrate;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示最高分的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("易错项");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum="";
            if(data.answer[0].summary.erroranswer!=undefined){
                datanum=data.answer[0].summary.erroranswer;
            }
            if(datanum==""){
               usernumdiv.html("<font size='0.8em'>无</font>");
            }else{
               usernumdiv.text(datanum); 
            }
            usera.append(usernumdiv);
            attrarr=[];
            temp={};0
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            temp={};
            temp.id="id";
            temp.val="listen";
            attrarr.push(temp);
            var ttsdiv=initDom("<div></div>",attrarr);
            ttsdiv.appendTo(parentdiv);
            attrarr=[];
            var ttsclassh5=initDom("<h5></h5>",attrarr);
            ttsclassh5.text("听力材料");
            ttsdiv.append(ttsclassh5);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="listen";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var ttssuserul=initDom("<ul></ul>",attrarr);
            ttsdiv.append(ttssuserul);
            mui.each(data.questts,function(k,v){
                attrarr=[];
                var ttsclassli=initDom("<li></li>",attrarr);
                ttssuserul.append(ttsclassli);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;";
                attrarr.push(temp);
                var ttsh5=initDom("<h5></h5>",attrarr);
                if(v.flag_content==''){
                    ttsh5.html(v.tts_content);
                }else{
                    ttsh5.html('<strong>'+v.flag_content+'</strong>:'+v.tts_content);
                }
                ttsclassli.append(ttsh5);
            });
        }
		return parentdiv;
    }

    //填空题
    var blankExamsQuestion=function(data){
        //待更新
    }

    //判断题
    var trueOrFalseExamsQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        // temp.id="quescount";
        // temp.val=data.quescount;
        // attrarr.push(temp);
        temp.id="stemid";
        temp.val=data.stemid;
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        var stemdiv=$("<div></div>");
        stemdiv.addClass("tigan");
        stemdiv.attr("style","margin-top:10px;margin-left:10px;font-family: times;text-align:left;font-size:0.8em;color: #8f8f94;");
        stemdiv.html(data.question.stemcontent);
        parentdiv.append(stemdiv);
        //判断是否有音频1表示需要音频 0表示不需要音频
        var voicediv="";
        if(data.question.isvoice=='1'){
            voicediv=setVoice(data.question,examsPlayEvent);
            parentdiv.append(voicediv);
        }
        //选择题展示
        
        var div=$("<div></div>");
        div.addClass("title tigan");
        div.attr("style","margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;");
        div.html(data.question.tcontent);
        parentdiv.append(div);
        var ul=$("<ul></ul>");
        ul.addClass("mui-table-view xuanze");
        ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        mui.each(data.question.questions_items,function(index,item){
            var li=$("<li></li>");
            li.addClass("mui-table-view-cell mui-media");
            li.attr("quesid",data.question.id);
            li.attr("itemflag",item.value);
            li.attr("answerid",data.answer[0].quesansid);
            li.attr("homeworkid",data.question.homeworkid);
            li.attr("examsid",data.question.examsid);
            li.attr("quizid",data.question.quizid);
            li.attr("typeid",data.question.typeid);
            li.bind("tap",choiceExamsEvent);
            var a=$("<a></a>");
            a.attr("quesid",data.question.id);
            a.attr("itemflag",item.value);
            a.attr("answerid",data.answer[0].quesansid);
            a.attr("homeworkid",data.question.homeworkid);
            a.attr("examsid",data.question.examsid);
            a.attr("quizid",data.question.quizid);
            a.attr("typeid",data.question.typeid);
            li.html(a);
            //a.appendTo(li);
            var div=$("<div></div>");
            div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            a.append(div);
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            div.append(tdiv);
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.value==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.value==data.answer[0].answer&&item.value==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.value!=data.answer[0].answer&&item.value==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.value==data.answer[0].answer&&item.value!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }

            }
            var span=$("<span></span>");
            span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
            span.attr("itemflag",item.flag);
            span.text(item.flag);
            tdiv.append(span);
            if(data.itemtype=='1'){
                var imgdiv=$("<img></img>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;");
                imgdiv.attr("height","90px");
                imgdiv.attr("width","120px");
                imgdiv.attr("src",item.content);
            }else{
                var imgdiv=$("<div></div>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
                imgdiv.html(item.content);
            }
            a.append(imgdiv);
            li.appendTo(ul);
        });
        parentdiv.append(ul);

        if(issubmit=='1'){
             //个人作答情况
            if(type!=1&&studentid!=""){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是";
                if(data.answer[0].quesanswer=='0'){
                    userquestionanswer=userquestionanswer+"False";
                }else if(data.answer[0].quesanswer=='1'){
                    userquestionanswer=userquestionanswer+"True";
                }
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案";
                    if(data.answer[0].answer=="0"){
                        userquestionanswer=userquestionanswer+"False";
                    }else{
                        userquestionanswer=userquestionanswer+"True";
                    }
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }

            //答案情况
            if(type==1){
                attrarr=[];
                temp={};0
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
                attrarr.push(temp);
                var userclassdiv=initDom("<div></div>",attrarr);
                userclassdiv.appendTo(parentdiv);
                attrarr=[];
                var userclassh5=initDom("<h5></h5>",attrarr);
                userclassh5.text("作答情况");
                userclassdiv.append(userclassh5);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view mui-table-view-chevron";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="margin-top: 10px;";
                attrarr.push(temp);
                var userclassul=initDom("<ul></ul>",attrarr);
                 userclassul.appendTo(userclassdiv);
                 var itemsarray=["A","B"];
                 for(var i=0;i<2;i++){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell mui-collapse";
                    attrarr.push(temp);
                    temp={};
                    temp.id="itemflag";
                    temp.val=i;
                    attrarr.push(temp);
                    temp={};
                    temp.id="quesid";
                    temp.val=data.question.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="homeworkid";
                    temp.val=data.question.homeworkid;
                    attrarr.push(temp);
                    var userclassli=initDom("<li></li>",attrarr);
                    userclassli.bind("tap",function(){
                        var obj=$(this);
                        $(this).find("ul").empty();
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view mui-table-view-chevron";
                        attrarr.push(temp);
                        var itemuserul=initDom("<ul></ul>",attrarr);
                        $(this).append(itemuserul);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view-cell";
                        attrarr.push(temp);
                        var itemuserli=initDom("<li></li>",attrarr);
                        itemuserul.append(itemuserli);
                        var answer=$(this).attr("itemflag");
                        var quesid=$(this).attr("quesid");
                        var homeworkid=$(this).attr("homeworkid");
                        //主要进行ajax的数据获取
                        $.getJSON("getAnswerUserList",{classid:classid,answer:answer,quesid:quesid,homeworkid:homeworkid,typeid:"0",ran:Math.random()},function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    itemuserli.append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                            
                        });
                    });
                    userclassli.appendTo(userclassul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-navigate-right";
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(itemsarray[i]==answerflag){
                        temp.val="color: #2bc8a0;";
                    }else{
                        temp.val="color: #FE5A59;";
                    }
                    attrarr.push(temp);
                    var userclassa=initDom("<a></a>",attrarr);
                    userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                    userclassli.append(userclassa);
                }
            }
        //展示班级的数据
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var userdiv=initDom("<div></div>",attrarr);
        userdiv.appendTo(parentdiv);
        attrarr=[];
        temp={};
        temp.id="style";
        temp.val="margin-bottom:10px;";
        attrarr.push(temp);
        var userh5=initDom("<h5></h5>",attrarr);
        userh5.text("班级数据");
        userdiv.append(userh5);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view mui-grid-view";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="background-color: white;";
        attrarr.push(temp);
        var userul=initDom("<ul></ul>",attrarr);
        userdiv.append(userul);
        
        //展示作答人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("作答人次");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.answernum!=undefined){
            datanum=data.answer[0].summary.answernum;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示及格人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("及格率");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.accrate!=undefined){
            datanum=data.answer[0].summary.accrate;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示最高分的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("易错项");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.erroranswer!=undefined){
            datanum=data.answer[0].summary.erroranswer;
        }
        if(datanum==0&&datanum!=""){
            datanum="False";
        }else if(datanum!=""){
            datanum="True";
        }else{
            datanum="<font size='0.8em'>无</font>";
        }
        usernumdiv.html(datanum);
        usera.append(usernumdiv);
        attrarr=[];
        temp={};0
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="listen";
        attrarr.push(temp);
        var ttsdiv=initDom("<div></div>",attrarr);
        ttsdiv.appendTo(parentdiv);
        attrarr=[];
        var ttsclassh5=initDom("<h5></h5>",attrarr);
        ttsclassh5.text("听力材料");
        ttsdiv.append(ttsclassh5);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="listen";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var ttssuserul=initDom("<ul></ul>",attrarr);
        ttsdiv.append(ttssuserul);
        mui.each(data.questts,function(k,v){
            attrarr=[];
            var ttsclassli=initDom("<li></li>",attrarr);
            ttssuserul.append(ttsclassli);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var ttsh5=initDom("<h5></h5>",attrarr);
            if(v.flag_content==''){
                ttsh5.html(v.tts_content);
            }else{
                ttsh5.html('<strong>'+v.flag_content+'</strong>:'+v.tts_content);
            }
            ttsclassli.append(ttsh5);
        });
    }
        return parentdiv;
    }


    //排序题
    var sequenceExamsQuestion=function(data){
        //待更新
    }

   

    //选择题的监听事件
    function choiceExamsEvent(){
        //alert("听力训练选择题");
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var quizid=$(this).attr("quizid");
        var examsid=$(this).attr("examsid");
        var answerid=$(this).attr("answerid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUseranswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                homeworkid:homeworkid,
                quizid:quizid,
                answerid:answerid,
                examsid:examsid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }

    //填空题的监听事件
    function blankExamsEvent(){
        alert("听力训练填空题");
    }

    //判断题的监听事件
    function trueOrFalseExamsEvent(){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var quizid=$(this).attr("quizid");
        var examsid=$(this).attr("examsid");
        var answerid=$(this).attr("answerid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUseranswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                homeworkid:homeworkid,
                quizid:quizid,
                answerid:answerid,
                examsid:examsid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }

    //排序题的监听事件
    function sequenceExamsEvent(){
    	alert("听力训练排序题");
    }


    //英汉互译的监听事件
    function wordTranslateEvent(obj){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var wordid=$(this).attr("wordid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUserWordtestanswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                studentid:studentid,
                classid:classid,
                homeworkid:homeworkid,
                wordid:wordid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });

    }

    //单词跟读的监听事件
    function wordChooseEvent(){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var wordid=$(this).attr("wordid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUserWordtestanswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                studentid:studentid,
                classid:classid,
                homeworkid:homeworkid,
                wordid:wordid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }

    //点击开始播放
    function examsPlayEvent(){
        var status=$(this).find("a").hasClass("play");
        var playtimes=$(this).attr("playtimes");
        var stoptimes=$(this).attr("stoptimes");
        if(!status){
            $(this).addClass("istop").removeClass("iplay");
            $(this).find("a").addClass("play").removeClass("stop");
            try{
                clearTimeout(mp3time);
                UXinJSInterfaceSpeech.stopAudio();
                $(this).find("a").attr("id","");
            }catch(e){
                setTip("请升级到最新的优信");

            }
            
        }else{
            $(this).addClass("iplay").removeClass("istop");
            //$(this).addClass("istop").removeClass("iplay");
            $(this).find("a").addClass("stop").removeClass("play");
            $(this).find("a").attr("id","playing");
            //这是播放遍数
            mp3.setPlaytimes(playtimes);
            mp3.setStoptime(stoptimes);
            mp3.setCurPlaytimes();
            mp3.playWordList(0);
        }
        
    }

    //进行内容的填充
    var setpagecontent=function(data,pageitems){
        //听力训练的展示
        if(data.type=='1'){
            if(data.parent.question.typeid=='1'){
                //调用听力训练的选择试题
                var pagecontent=choiceExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='2'){
                //调用听力训练的填空试题
                var pagecontent=blankExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='3'){
                //表用听力训练的判断试题
                var pagecontent=trueOrFalseExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='4'){
                //表用听力训练的排序试题
                var pagecontent=sequenceExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }
        }

        //单词测试的展示
        if(data.type=='0'){
            if(data.parent.question.typeid=='2'){
                //调用单词测试的单词拼写
                var pagecontent=wordSpellQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='0'){
                //调用单词测试的听音选词
                var pagecontent=wordChooseQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='3'||data.parent.question.typeid=='1'){
                //表用听力训练英汉互译
                var pagecontent=wordTranslateQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }
        }

        //单词跟读的展示
        if(data.type=='2'){
            //调用单词跟读的
            var pagecontent=wordAloundQuestion(data.question);
            $(pageitems).html(pagecontent);
        }

        //单词跟读的展示
        if(data.type=='3'){
            //调用课文跟读
            var pagecontent=textAloundQuestion(data.question);
            $(pageitems).html(pagecontent);
        }
        //var objs=stemdiv.getElementsByClassName("tigan");
        $(pageitems).find(".tigan").find("img").attr("style","width:80%;");
    }

    //进行相应数据
    var getResponse=function(url,data,pageitems,isasync,index,page){
    	var respose="";
		listendata=[];
		mui.ajax(url,
			{
			data:data,
			dataType:'json',//服务器返回json格式数据
			type:'post',//HTTP请求类型
			//timeout:4000,//超时时间设置为10秒；
			async:isasync,
			success:function(data){
                setpagecontent(data,pageitems);
                page[index]=1;
                pagedata[index]=data;
                try{
                    page_tts[index]=data.parent.questts;
                }catch(e){
                    page_tts[index]=[];
                }
                var obj={};
                if(data.children==null||data.children=='null'||data.children==''||data.children==undefined){
                    obj.type=0;
                }else{
                    obj.type=1;
                }
                if(data.type==1){
                    obj.stemid=data.parent.question.stemid;
                }else{
                    obj.stemid='0';
                }
                page_obj[index]=obj;
			},
			error:function(xhr,type,errorThrown){
				//异常处理；
				mui.toast("网络请求出错请等会儿再试试");
				return errorThrown;
			}
	    });
    }

    studentContent.getResponse=getResponse;
    return studentContent;
});