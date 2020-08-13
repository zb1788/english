//目前英语的所有的题型进行类的封装
//version 1.0 该模块需要依赖于jquery以及jquery-ui进行效果展示
//规定试题0表示单选题 1表示多选题 2表示填空题  题干的内容中0表示文字 1表示图片 2表示音频 3表示图文混合 4音频文字  选项0表示文字 1表示图片
//right表示正确 error表示错误
/*
<!--遮罩的代码-->
<div id="over" class="over"></div>
<div id="layout" class="layout">
	<img src="__SUBJECT__/img/2013112931.gif" alt="" />
</div>
<style>
	.over {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        opacity:0.5;
        z-index: 1000;
    }
    .layout {
        display: block;
        position: absolute;
        top: 40%;
        left: 40%;
        width: 20%;
        height: 20%;
        z-index: 1001;
        text-align:center;
    }
</style>
*/
var Question=function(a){
	a = a || {};
    var options = {
        //questype表示的是0单选题 1多选题 2表示拼写题 3表示填空题 4表示连线题
        isAuto:a.isauto,
        errorUrl:a.errorUrl,
        answerUrl:a.answerUrl,
        audioPlay:a.audioPlay,
        imgBlank:a.imgBlank,
        rightCallBack:a.rightCallBack,
        errorCallBack:a.errorCallBack
    };
    var GetRequest=function() {
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
    };
    this.error=function(data){
    	$.ajax({url:options.errorUrl,async:false,type:'post',data:{data:data,ran:Math.random()}});
    };
    //创建透明遮罩
    var shade=function(){
    	var over=$("<div id='shade' style='display: block;position: absolute;top: 0;left: 0;width: 100%;height: 100%;background-color: #f5f5f5;opacity:0;z-index: 1000;'></div>");
    	$("body").append(over);
    };
    //遮罩消失
    var disappear=function(obj){
    	$(obj).remove();
    }
    //browser:  navigator.userAgent 浏览器类型
    this.browser=function( ua ){
        var ret = {},
        webkit = ua.match( /WebKit\/([\d.]+)/ ),
        chrome = ua.match( /Chrome\/([\d.]+)/ ) ||ua.match( /CriOS\/([\d.]+)/ ),
        ie = ua.match( /MSIE\s([\d\.]+)/ ) ||ua.match( /(?:trident)(?:.*rv:([\w.]+))?/i ),
        firefox = ua.match( /Firefox\/([\d.]+)/ ),
        safari = ua.match( /Safari\/([\d.]+)/ ),
        opera = ua.match( /OPR\/([\d.]+)/ ),
        android = ua.match( /(?:Android);?[\s\/]+([\d.]+)?/ ),
        ios = ua.match( /(?:iPad|iPod|iPhone).*OS\s([\d_]+)/ );
        webkit && (ret.webkit = parseFloat( webkit[ 1 ] ));
        chrome && (ret.chrome = parseFloat( chrome[ 1 ] ));
        ie && (ret.ie = parseFloat( ie[ 1 ] ));
        firefox && (ret.firefox = parseFloat( firefox[ 1 ] ));
        safari && (ret.safari = parseFloat( safari[ 1 ] ));
        opera && (ret.opera = parseFloat( opera[ 1 ] ));
        android && (ret.android = parseFloat( android[ 1 ] ));
        ios && (ret.ios = parseFloat( ios[ 1 ].replace( /_/g, '.' ) ));
        return ret;
    };
    this.sound=function(className,audioFile,bottom){
    	var a=$("<a style='margin-bottom:"+bottom+"%;'></a>");
        a.attr("mp3",audioFile);
        a.html("<i class='icon-song2'></i>朗读");
    	a.addClass(className);
    	a.on("click",function(){
            var mp3=$(this).attr("mp3");
    		if(mp3==""){
    			var browser=this.browser(navigator.userAgent);
    			var location=window.location.href;
    			var errorinfo="audio can not founded";
    			var data={};
    			data.browser=browser;
    			data.location=location;
    			data.errorinfo=errorinfo;
    			error(JSON.stringify(data));
    		}else{
    			if(typeof options.audioPlay != 'function'){
    				var browser=this.browser(navigator.userAgent);
	    			var location=window.location.href;
	    			var errorinfo=options.audioPlay+" must be function";
	    			var data={};
	    			data.browser=browser;
	    			data.location=location;
	    			data.errorinfo=errorinfo;
	    			this.error(JSON.stringify(data));
    			}else{
    				try{
				        if(typeof(eval(options.audioPlay))=="function"){
				        	try{
				        		options.audioPlay(mp3);
				        	}catch(e){
				        		var browser=this.browser(navigator.userAgent);
				    			var location=window.location.href;
				    			var errorinfo=mp3+" can not play";
				    			var data={};
				    			data.browser=browser;
				    			data.location=location;
				    			data.errorinfo=errorinfo;
				    			this.error(JSON.stringify(data));
				        	}
				        }
				    }catch(e){
				    	var browser=this.browser(navigator.userAgent);
		    			var location=window.location.href;
		    			var version="";
		    			if(typeof UXinJSInterface != 'undefined'){
		    				try{
		    					version=UXinJSInterface.getDeviceType();
		    				}catch(e){
		    					version="1";
		    				}
						}
		    			var errorinfo="this version "+version+" function is not exist";
		    			var data={};
		    			data.browser=browser;
		    			data.location=location;
		    			data.errorinfo=errorinfo;
		    			this.error(JSON.stringify(data));
				    }
    			}
    		}
    	})
		return a;
    };
    /*
    *题目的展示<a class="btn100 radius100 bBlue"><i class="icon-song2"></i></a>
    */
    this.settncontent = function(tndata,tnType) {
    	var c=$("<div class='timu'></div>"),d;
        n=tnType;
    	switch(n)
		{
			case 0:
				d=$("<div class='mb20 pad10'></div>")
				d.html(tndata.tncontent);
				c.append(d);
				break;
			case 1:
			  	d=$("<img class='img100'></img>");
			  	d.attr("src",tndata.tncontent);
			  	c.append(d);
				break;
			case 2:
				d=this.sound('btn bBlue',tndata.mp3,"0%");
				c.append(d);
				break;
			case 3:
				d=document.createElement("label");
				d.class="mb10";
				d.innerHTML=tndata.tncontent;
				c.append(d);
				d=this.sound('btn bBlue',tndata.mp3,"0%");
				c.append(d);
				break;
            case 4:
                d=$("<div class='mb20'></div>");
                d.append('<h1 style="margin-bottom: 10px;" class="tmH1">'+tndata.cnexplain+'</h1>');
                var s=this.sound('btn bBlue',tndata.mp3,"10");
                d.append(s);
                var div=$('<div style="line-height: 40px;"></div>');
                d.append(div);
                $.each(tndata.tncontent,function(key,value){
                    var span=$('<span class="tkong01">&nbsp;</span>');
                    div.append(span);
                })
                var label=$('<label class="mb20 delback" style="display: inline;"><i class="icon-backspace"></i></label>');
                label.bind("click",function(){
                   var isdo=$(this).attr("isdo");
                   if(isdo=='1'){
                        return false;
                   }
                   var span=$(this).siblings();
                   for(var i=(span.length-1);i>=0;i--){
                        value=span[i];
                        var sp=$(value).html();
                        if(sp!=='&nbsp;'){
                            $(value).html('&nbsp;');
                            $(value).parent().find(".actived").removeClass("actived");
                            $(value).addClass("actived");
                            //并且删除选项
                            $(value).parents(".con").find(".items[curindex='"+i+"']").removeClass("actived").removeClass("right");
                            $(value).parents(".con").find(".items[curindex='"+i+"']").attr("isdo",null);
                            $(value).parents(".con").find(".items[curindex='"+i+"']").attr("curindex",null);
                            return false;
                        }
                   }
                })
                div.append(label);
                c.append(d);
                break;
		}
		return c;
    };


    var rightface = function(){
        $(".dtts").remove();
        var div=$('<div class="dtts" style="position: fixed;top: 30%;width: 40%;left: 30%;"></div>');
        var h2=$('<img src="../../public/Subject/images/dui.png" style="width:100%;"/>')
        div.append(h2);
        div.appendTo($("body"));
        $(div).show(200).delay(1000).fadeOut(500);
    };

    var errorface = function(){
        var div=$('<div class="dtts" style="position: fixed;top: 30%;width: 40%;left: 30%;"></div>');
        var h2=$('<img src="../../public/Subject/images/cuo.png" style="width:100%;"/>')
        div.append(h2);
        div.appendTo($("body"));
        $(div).show(200).delay(1000).fadeOut(500);
    };
    /*	 选项展示问题
	*    <div class="daan pad20">
	*		<a class="xxT backGfff">light</a>
	*		<a class="backGfff xxT">door</a>
	*		<a class="backGfff xxT">teacher</a>
	*		<a class="backGfff xxT">classroom</a>
	*	</div>
	*	<div class="pad5">
	*		<ul class="timuImg bLine">
	*	    	<li><span><i class="icon-dui04"></i><img src="img/timg (2).jpg" class="img100"></span></li>
	*	        <li><span><i class="icon-cuo04"></i><img src="img/timg (2).jpg" class="img100"></span></li>
	*	        <li><span><img src="img/timg (2).jpg" class="img100"></span></li>
	*	        <li><span><img src="img/timg (2).jpg" class="img100"></span></li>
	*	    </ul>
	*	</div>
	*/
    var setitem = function(itemdata,questionid,itemType,quesData){
        var questionid=questionid;
    	var item="";
        //单选的选择题的样式
        if(itemType=='single'){
            if(itemdata.typeid==0){
                item=$("<a class='backGfff xxT items' style='border-radius:5px;''></a>");
                item.bind("click",function(){
                    //选项的展示是什么样式

                    var typeid=$(this).attr("typeid");
                    //判断是够做过这道试题
                    var isdo=$(this).attr("isdo");
                    if(isdo=='1'){
                        return false;
                    }
                    $(this).attr("isdo","1");
                    $(this).siblings().attr("isdo","1");
                    $(this).addClass("actived");
                    //点击事件就是需要加一个actived
                    var data=[];
                    var iserror=$(this).attr("iserror");
                    var questionid=$(this).attr("questionid");
                    $(this).parent().find(".actived").each(function(key,value){
                        var temp={};
                        temp.iserror=$(value).attr("iserror");
                        temp.flag=$(value).attr("flag");
                        data.push(temp);
                    })

                    console.log(data);
                    //向题干中写入这个单词

                    var cart = $('.tkong01');
                    var imgtodrag = $(this);
                    try{
                        var choice=$(imgtodrag).text();
                        if(iserror=='1'){
                            $(imgtodrag).parents(".con").find(".tkong01").html("<font color='#ff7272'>"+choice+"</font>");
                        }else{
                            $(imgtodrag).parents(".con").find(".tkong01").text(choice);
                        }

                    }catch(e){
                        console.log("question is not choiceblank");
                    }
                    if(iserror=='1'){
                        //标记正确答案和错误答案
                        $(imgtodrag).parent().find(".error").removeClass("error");
                        // $(imgtodrag).parent().find("i.icon-cuo04").remove();
                        // $(imgtodrag).parent().find("i.icon-dui04").remove();
                        $(imgtodrag).addClass("shake");
                        if(typeid=='0'){
                            //$(imgtodrag).append($('<i class="icon-cuo04" style="float: right;text-align: center;line-height: 30px;"></i>'));
                            //$(imgtodrag).parent().find(".answer").append($('<i class="icon-dui04" style="float: right;text-align: center;line-height: 30px;"></i>'));
                            $(imgtodrag).addClass("error");
                            $(imgtodrag).parent().find(".answer").addClass("right");
                        }else{
                            //$(imgtodrag).find("span").append($('<i class="icon-cuo04" style="float: right;text-align: center;"></i>'));
                            //$(imgtodrag).parent().find(".answer").find("span").append($('<i class="icon-dui04" style="float: right;text-align: center;"></i>'));
                            $(imgtodrag).addClass("error");
                        }

                        //options.errorCallBack();
                        //上面封装一层透明的遮罩
                        shade();
                        errorface();
                        setTimeout(function(){
                          $(imgtodrag).removeClass("shake");
                          //消失遮罩
                          disappear($("#shade"));
                        },1000)
                        var questimes=$("#times");
                        var imgclone = $("<em style='font-size:20px;color: red;text-decoration: none;font-style: normal;font-weight: normal;'>-1</em>").clone().offset({
                            top: imgtodrag.offset().top,
                            left: imgtodrag.offset().left
                        }).css({
                            'opacity': '0.5',
                            'position': 'absolute',
                            'height': '40px',
                            'width': '40px',
                            'z-index': '100'
                        }).appendTo($('body')).animate({
                            'top': questimes.offset().top + 10,
                            'left': questimes.offset().left + 10,
                            'width': 75,
                            'height': 75
                        }, 1000, 'easeInOutExpo');
                        setTimeout(function () {
                            questimes.effect('shake', { times: 2 }, 200);
                            if(typeof options.errorCallBack == 'function'){

                                options.errorCallBack(imgtodrag,1);
                            }
                        }, 300);
                        imgclone.animate({
                            'width': 0,
                            'height': 0
                        }, function () {
                            $(this).detach();
                        });
                    }else{
                        //正确情况
                        $(imgtodrag).parent().find(".right").removeClass("right");
                        $(imgtodrag).parent().find("i.icon-dui04").remove();
                        $(imgtodrag).parent().find("i.icon-cuo04").remove();

                        if(typeid=='0'){
                            $(imgtodrag).addClass("right");
                        }else{

                        }

                        //$('tips').html("太棒了！").attr("style","display:block;");
                        shade();
                        rightface();
                        setTimeout(function(){
                            $('tips').attr("style","display:none;").html("");
                            disappear($("#shade"));
                            if(typeof options.rightCallBack == 'function'){
                                options.rightCallBack();
                            }
                        }, 1500);
                    }

                    var request=GetRequest();
                    console.log(options);
                    $.ajax({url:options.answerUrl,async:true,type:'post',data:{request:JSON.stringify(request),questionid:questionid,data:JSON.stringify(data),ran:Math.random()}});
                    //错误情况
                })
                item.addClass(itemType);
                item.attr("questionid",questionid);
                item.attr("typeid",itemdata.typeid);
                item.attr("wordid",quesData.wordid);
                item.attr("explainid",quesData.explainid);
                item.attr("flag",itemdata["flag"]);
                if(itemdata.flag==quesData.answer){
                    item.attr("iserror",0);
                    item.addClass("answer");
                }else{
                    item.attr("iserror",1);
                }
                item.html(itemdata.content);
            }else{
                item=$("<li class='items'></li>");
                item.bind("click",function(){
                    var typeid=$(this).attr("typeid");
                    //判断是够做过这道试题
                    var isdo=$(this).attr("isdo");

                    if(isdo=='1'){
                        return false;
                    }
                    $(this).attr("isdo","1");
                    $(this).siblings().attr("isdo","1");
                    $(this).addClass("actived");
                    //点击事件就是需要加一个actived
                    var data=[];
                    var iserror=$(this).attr("iserror");
                    var questionid=$(this).attr("questionid");
                    $(this).parent().find(".actived").each(function(key,value){
                        var temp={};
                        temp.iserror=$(value).attr("iserror");
                        temp.flag=$(value).attr("flag");
                        data.push(temp);
                    })
                    //向题干中写入这个单词

                    var cart = $('.tkong01');
                    var imgtodrag = $(this);
                    try{
                        var choice=$(imgtodrag).text();
                        if(iserror=='1'){
                            $(imgtodrag).parents(".con").find(".tkong01").html("<font color='#ff7272'>"+choice+"</font>");
                        }else{
                            $(imgtodrag).parents(".con").find(".tkong01").text(choice);
                        }

                    }catch(e){
                        console.log("question is not choiceblank");
                    }
                    if(iserror=='1'){
                        //标记正确答案和错误答案
                        $(imgtodrag).parent().find(".error").removeClass("error");
                        // $(imgtodrag).parent().find("i.icon-cuo04").remove();
                        // $(imgtodrag).parent().find("i.icon-dui04").remove();
                        $(imgtodrag).addClass("shake");
                        if(typeid=='0'){
                            //$(imgtodrag).append($('<i class="icon-cuo04" style="float: right;text-align: center;line-height: 30px;"></i>'));
                            //$(imgtodrag).parent().find(".answer").append($('<i class="icon-dui04" style="float: right;text-align: center;line-height: 30px;"></i>'));
                            $(imgtodrag).addClass("error");
                            $(imgtodrag).parent().find(".answer").addClass("right");
                        }else{
                            //$(imgtodrag).find("span").append($('<i class="icon-cuo04" style="float: right;text-align: center;"></i>'));
                            //$(imgtodrag).parent().find(".answer").find("span").append($('<i class="icon-dui04" style="float: right;text-align: center;"></i>'));
                            $(imgtodrag).addClass("error");
                            $(imgtodrag).parent().find(".answer").addClass("rightanswer");
                        }

                        //options.errorCallBack();
                        //上面封装一层透明的遮罩
                        shade();
                        errorface();
                        setTimeout(function(){
                          $(imgtodrag).removeClass("shake");
                          //消失遮罩
                          disappear($("#shade"));
                        },1000)
                        var questimes=$("#times");
                        var imgclone = $("<em style='font-size:20px;color: red;text-decoration: none;font-style: normal;font-weight: normal;'>-1</em>").clone().offset({
                            top: imgtodrag.offset().top,
                            left: imgtodrag.offset().left
                        }).css({
                            'opacity': '0.5',
                            'position': 'absolute',
                            'height': '40px',
                            'width': '40px',
                            'z-index': '100'
                        }).appendTo($('body')).animate({
                            'top': questimes.offset().top + 10,
                            'left': questimes.offset().left + 10,
                            'width': 75,
                            'height': 75
                        }, 1000, 'easeInOutExpo');
                        setTimeout(function () {
                            questimes.effect('shake', { times: 2 }, 200);
                            if(typeof options.errorCallBack == 'function'){
                                options.errorCallBack(imgtodrag,1);
                            }
                        }, 300);
                        imgclone.animate({
                            'width': 0,
                            'height': 0
                        }, function () {
                            $(this).detach();
                        });
                    }else{
                        //正确情况
                        $(imgtodrag).parent().find(".right").removeClass("right");
                        $(imgtodrag).parent().find("i.icon-dui04").remove();
                        $(imgtodrag).parent().find("i.icon-cuo04").remove();

                        if(typeid=='0'){
                            $(imgtodrag).addClass("right");
                            //$(imgtodrag).append($('<i class="icon-dui04" style="float: right;text-align: center;line-height: 30px;"></i>'));
                        }else{
                            $(imgtodrag).addClass("rightanswer");
                            //$(imgtodrag).find("span").append($('<i class="icon-dui04" style="float: right;text-align: center;"></i>'));
                        }

                        $('tips').html("太棒了！").attr("style","display:block;");
                        shade();
                        rightface();
                        setTimeout(function(){
                            $('tips').attr("style","display:none;").html("");
                            disappear($("#shade"));
                            if(typeof options.rightCallBack == 'function'){
                                options.rightCallBack();
                            }
                        }, 1000);
                    }
                    $(this).attr("isdo","1");
                    $(this).siblings().attr("isdo","1");
                    var request=GetRequest();
                    console.log(options);
                    $.ajax({url:options.answerUrl,async:true,type:'post',data:{request:JSON.stringify(request),questionid:questionid,data:JSON.stringify(data),ran:Math.random()}});
                    //错误情况
                })
                item.addClass(itemType);
                item.attr("style","margin: 0 auto;");
                item.attr("questionid",questionid);
                item.attr("typeid",itemdata.typeid);
                item.attr("wordid",quesData.wordid);
                item.attr("explainid",quesData.explainid);
                item.attr("flag",itemdata["flag"]);
                if(itemdata.flag==quesData.answer){
                    item.attr("iserror",0);
                    item.addClass("answer");
                }else{
                    item.attr("iserror",1);
                }
                var itemspan=$("<span></span>");
                item.append(itemspan);
                var itemimg=$("<img class='img100'></img>");
                itemimg.attr("src",itemdata.content);
                itemimg.attr("questionid",questionid);
                itemimg.attr("typeid",itemdata.typeid);
                itemimg.attr("wordid",quesData.wordid);
                itemimg.attr("explainid",quesData.explainid);
                itemimg.attr("flag",itemdata["flag"]);
                itemimg.onerror=function(){
                    this.src=options.imgBlank;
                    var browser=this.browser(navigator.userAgent);
                    var location=window.location.href;
                    var errorinfo="this "+questionid+" question's images is not exist";
                    var data={};
                    data.browser=browser;
                    data.location=location;
                    data.errorinfo=errorinfo;
                    this.error(JSON.stringify(data));
                }
                itemspan.append(itemimg);
            }
        }else if(itemType=='multiple'){
            var answer=quesData.answer;
            if(itemdata.typeid==0){
                item=$("<a class='backGfff xxT items'></a>");
                item.bind("click",function(){

                });
                item.addClass(itemType);
                item.attr("questionid",questionid);
                item.attr("wordid",quesData.wordid);
                item.attr("explainid",quesData.explainid);
                item.attr("typeid",itemdata.typeid);
                item.attr("flag",itemdata["content"]);
                item.attr("index",$.inArray(itemdata.content, answer));
                if($.inArray(itemdata.content, answer)>-1){
                    item.attr("iserror",0);
                    item.addClass("answer");
                }else{
                    item.attr("iserror",1);
                }
                item.html(itemdata.content);
            }else{
                item=$("<li class='items'></li>");
                item.addClass(itemType);
                item.attr("questionid",questionid);
                item.attr("wordid",quesData.wordid);
                item.attr("typeid",itemdata.typeid);
                item.attr("explainid",quesData.explainid);
                item.attr("flag",itemdata["flag"]);
                item.attr("index",$.inArray(itemdata.content, answer));
                if($.inArray(itemdata.content, answer)>-1){
                    item.attr("iserror",0);
                    item.addClass("answer");
                }else{
                    item.attr("iserror",1);
                }
                var itemspan=$("<span></span>");
                item.append(itemspan);
                var itemimg=$("<img class='img100'></img>");
                itemimg.attr(src,itemdata.content);
                itemimg.onerror=function(){
                    this.src=options.imgBlank;
                    var browser=this.browser(navigator.userAgent);
                    var location=window.location.href;
                    var errorinfo="this "+questionid+" question's images is not exist";
                    var data={};
                    data.browser=browser;
                    data.location=location;
                    data.errorinfo=errorinfo;
                    this.error(JSON.stringify(data));
                }
                itemspan.append(itemimg);
            }
        }else if(itemType=='spell'){
            var answer=quesData.tncontent;
            item=$('<span class="items multiple"></span>');
            item.bind("click",function(){
                var curindex=0;
                var isdo=$(this).attr("isdo");
                //如果全部填满了那就不能点击
                var tkongs=$(this).parents(".con").find(".timu").find(".tkong01");
                var zlength=0;
                $.each(tkongs,function(tk,tv){
                    if($(tv).html() === '&nbsp;'){
                        zlength=1;
                    }
                })
                if(isdo=='1'||zlength==0){
                    return false;
                }
                shade();
                $(this).attr("isdo",1);
                var val=$(this).text();
                var index=$(this).attr("index");
                var spans=$(this).parents(".con").find(".timu").find(".actived");
                var anslen=$(this).parents(".con").find(".tkong01").length;
                var itemlen=$(this).parent().find(".actived").length;
                if(itemlen==anslen){
                    setTimeout(function(){
                        $('tips').attr("style","display:none;").html("");
                            disappear($("#shade"));
                        }
                    ,100);
                    return false;
                }
                $(this).addClass("actived");
                var iserror=1;
                if(spans.length==0){
                    curindex=0;
                    if(index==0){
                        iserror=0;
                    }
                    spans=$(this).parents(".con").find(".timu").find(".tkong01").eq(0);
                    spans.attr("iserror",iserror);
                    $(this).attr("iserror",iserror);
                    $(this).attr("curindex",curindex);
                    $(this).parents(".con").find(".timu").find(".actived").removeClass("actived");
                    spans.text(val);
                    spans.next().addClass("actived");
                }else{
                    var ansindex=$(spans).index();
                    curindex=ansindex;
                    if(index==ansindex){
                        iserror=0;
                    }
                    spans.attr("iserror",iserror);
                    $(this).attr("iserror",iserror);
                    $(this).attr("curindex",curindex);
                    spans.text(val);
                    if(spans.next()!=undefined){
                        $(this).parents(".con").find(".timu").find(".actived").removeClass("actived");
                        spans.next().addClass("actived");
                    }
                }

                $(this).addClass("right");

                disappear($("#shade"));
            })
            //item.addClass(options.itemType);
            item.attr("questionid",questionid);
            item.attr("typeid",itemdata.typeid);
            item.attr("wordid",quesData.wordid);
            item.attr("explainid",quesData.explainid);
            item.attr("flag",itemdata["flag"]);
            item.attr("index",$.inArray(itemdata.content, answer));
            if($.inArray(itemdata.content, answer)>-1){
                item.attr("iserror",0);
                item.addClass("answer");
            }else{
                item.attr("iserror",1);
            }
            item.html(itemdata.content);
        }

    	return item;
    };
    //试题页面
    this.setQuestion=function(quesType,tnType,itemType,itemViewType,questionid,quesData){
        console.log(quesData.mp3s);
        //音频的试题进行音频的下载
        if(tnType==2||tnType==3||tnType==4){
            try{
                UXinJSInterfaceSpeech.cacheAudioFiles(iGetInnerText(JSON.stringify(quesData.mp3s)));
            }catch(e){
                setTip("升级到最新版本的优信");
            }
        }

        var ques=$("<div class='con'></div>");
        var isanalysis=0;
        if(quesData.analysis!=undefined&&quesData.analysis!=""&&quesData.analysis!=null&&quesData.analysis!='null'){
            isanalysis=1;
        }
        ques.attr("isanalysis",isanalysis);
        var ul=$("<ul></ul>");
        ques.append(ul);
        var overY=$("<div class='cgTi overY' style='height:"+(window.screen.height-51)+"px;'></div>");
        ul.append(overY);
        var questypes=$("<div style='color: white;margin-left: 10px;line-height:30px;'>"+quesData.questypename+"</div>");
        overY.append(questypes);
    	var tncontent=this.settncontent(quesData,tnType);
        overY.append(tncontent);
    	var items="";
    	if(itemType!='spell'&&itemViewType==0){
    		items=$('<div class="daan pad20 mb20"></div>');

    	}else if(itemType!='spell'&&itemViewType==1){
    		var itemdiv=$('<div class="pad5"></div>');
			items=$('<ul class="timuImg bLine  mb20"></ul>');
			itemdiv.append(items);
    	}else if(itemType=='spell'&&itemViewType==0){
            items=$('<div class="daxuan  mb20"></div>');
            var spellyes=$('<div questionid="'+questionid+'" wordid="'+quesData.wordid+'" explainid="'+quesData.explainid+'" style="position:fixed;z-index:0;left: 0;bottom: 0;width:100%;"><a class="spellbut" href="javascript:void(0);">确定</a></div>');
            spellyes.bind("click",function(){

                var data=[];
                var questionid=$(this).attr("questionid");
                var tcount=0;
                var icount=0;
                $(this).parents(".con").find(".timu").find(".tkong01").each(function(key,value){
                    tcount=tcount+1;
                    var iserror=$(value).attr("iserror");
                    if(iserror==undefined){
                    }else{
                        icount=icount+1;
                    }
                })

                if(icount==0){
                    //提示需要答题完毕
                    setTip("请完成本试题再点击确定提交");
                    return false;
                }

                $(this).parents(".con").find(".items").attr("isdo",1);
                $(".delback").attr("isdo",1);
                var isuserright=true;
                var answer=quesData.tncontent;
                var rightanswer="";
                var useranswer="";

                $(this).parents(".con").find(".timu").find(".tkong01").each(function(k,v){
                    var text=$(v).text();
                    useranswer=useranswer+text;
                    rightanswer=rightanswer+answer[k];
                })

                if(useranswer===rightanswer){
                    isuserright=true;
                }else{
                    isuserright=false;
                }

                if(!isuserright){
                    $(this).parents(".con").find(".timu").find(".tkong01").each(function(k,v){
                        var iserror=$(v).attr("iserror");
                        var temp={};
                        temp.iserror=iserror;
                        var text=$(v).text();
                        if(text==answer[k]){iserror='0'}else{iserror='1'}
                        temp.flag=text;
                        data.push(temp);
                        isuserright=isuserright&&(iserror=='0'?true:false);
                        if(iserror=='0'){
                            $(this).parents(".con").find(".daxuan").find("span[curindex='"+k+"']").addClass("right");
                        }else{
                            $(v).html("<font color='#ff7272'>"+text+"</font>");
                            $(this).parents(".con").find(".daxuan").find("span[curindex='"+k+"']").addClass("error");
                        }
                    })

                }


                if(!isuserright){
                    var imgtodrag = $(this);
                    var questimes=$("#times");
                    var imgclone = $("<em style='font-size:20px;color: red;text-decoration: none;font-style: normal;font-weight: normal;'>-1</em>").clone().offset({
                        top: imgtodrag.offset().top,
                        left: imgtodrag.offset().left
                    }).css({
                        'opacity': '0.5',
                        'position': 'absolute',
                        'height': '40px',
                        'width': '40px',
                        'z-index': '100'
                    }).appendTo($('body')).animate({
                        'top': questimes.offset().top + 10,
                        'left': questimes.offset().left + 10,
                        'width': 75,
                        'height': 75
                    }, 1000, 'easeInOutExpo');
                    errorface();
                    $("h1.textH1").remove();
                    $(this).parents(".con").find(".timu").append('<h1 class="textH1" style="text-align: center;">'+quesData.word+'</h1>');
                    setTimeout(function () {
                        questimes.effect('shake', { times: 2 }, 200);
                        if(typeof options.errorCallBack == 'function'){
                            options.errorCallBack(imgtodrag,1);
                        }
                    }, 1500);
                    imgclone.animate({
                        'width': 0,
                        'height': 0
                    }, function () {
                        $(this).detach();
                    });
                }else{
                    rightface();
                    if(typeof options.rightCallBack == 'function'){
                        options.rightCallBack();
                    }
                }
                var request=GetRequest();
                $.ajax({url:options.answerUrl,async:true,type:'post',data:{request:JSON.stringify(request),questionid:questionid,data:JSON.stringify(data),ran:Math.random()}});
                setTimeout(function(){
                    $('#tips').attr("style","display:none;").html("");
                    disappear($("#shade"));
                }, 10);
            })
            overY.append(spellyes);
        }
    	$.each(quesData.items,function(key,value){
    		var item=setitem(value,questionid,itemType,quesData);
    		items.append(item);
    	});

        overY.append(items);
				overY.append("<div class='mb50' style='.mb50{margin-bottom: 50px;    display: block;}'></div>");
        return ques;
    };
};
