/*次模块一栏jquery以及layer.js*/
var getWordGameList = function(url,grade,listobj,usercurgameid){
    $(listobj).empty();
    $.ajax({
        type:'GET',
        url:url,
        data:{grade:grade},
        dataType:'json',
        timeout: 13000,
        async:false,
        context:$('body'),
        success: function(data){
            //遮罩消失
            hideloading();
            //初始化列表
            $.each(data,function(k,v){
                var attrarr=[];
                var temp={};
                attrarr.push(temp);
                var li =initDom("<li></li>",attrarr);
                li.appendTo(listobj);
                if(v.id==usercurgameid){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="jbDang";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.text("当前");
                    span.appendTo(li);
                }else{
                    if(v.isstar==1){
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="jbJing";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        span.text("精");
                        span.appendTo(li);
                    }
                }
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="table";
                attrarr.push(temp);
                temp={};
                temp.id="href";
                temp.val="gamelevel?id="+v.id+"&backUrl="+Requests["backUrl"];
                attrarr.push(temp);
                var a =initDom("<a></a>",attrarr);
                a.appendTo(li);
                //需要将数据装到这个类中进行章节的展示
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="tableL";
                attrarr.push(temp);
                var div =initDom("<div></div>",attrarr);
                div.appendTo(a);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="img100";
                attrarr.push(temp);
                temp={};
                temp.id="src";
                temp.val=v.gamepic;
                attrarr.push(temp);
                var img =initDom("<img></img>",attrarr);
                img.appendTo(div);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="pad0-10";
                attrarr.push(temp);
                div =initDom("<div></div>",attrarr);
                div.appendTo(a);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH3";
                attrarr.push(temp);
                var h3 =initDom("<h3></h3>",attrarr);
                h3.text(v.name);
                h3.appendTo(div);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH4";
                attrarr.push(temp);
                var h4 =initDom("<h4></h4>",attrarr);
                h4.appendTo(div);
                var over="";
                // if(v.curlevel!='0'){
                //     if(v.isover=='1'){
                //         over='<span class="fr yinFont">已通关</span>';
                //     }else{
                //         over='<span class="fr">进行中</span>';
                //     }
                // }
                // h4.html('<font class="blueFont02">'+v.wordnum+'词</font>（'+v.curlevel+'/'+v.levelnum+'关）'+over);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH4";
                attrarr.push(temp);
                var h6 =initDom("<h6></h6>",attrarr);
                h6.appendTo(div);
                // h6.text(v.donum+"人正在闯关学习");
            });
        },
        error:function(xhr,type){
            hideloading();

        }
    })
};


var getLevelWordList = function(url,id,listobj){
    $(listobj).empty();
    $.ajax({
        type:'GET',
        url:url,
        data:{id:id},
        dataType:'json',
        async:false,
        context:$('body'),
        success: function(data){
            //遮罩消失
            hideloading();
            var attrarr=[];
            var temp={};
            temp.id="class";
            temp.val="textH5 pad0-10";
            attrarr.push(temp);
            var h5 =initDom("<h5></h5>",attrarr);
            h5.html('新单词学习<font class="blueFont pad0-10">0</font>个');
            h5.appendTo(listobj);
            //初始化列表
            var wordnum=0;
            if(data==null||data=='null'||data.length==0){
                return false;
            }
            $.each(data,function(k,v){
                wordnum=wordnum+1;
                attrarr=[];
                temp={};
                attrarr.push(temp);
                var li =initDom("<li></li>",attrarr);
                li.appendTo(listobj);
                attrarr=[];
                temp={};
                attrarr.push(temp);
                var span =initDom("<span></span>",attrarr);
                span.text(v.word);
                span.appendTo(li);
                //需要将数据装到这个类中进行章节的展示
                attrarr=[];
                temp={};
                attrarr.push(temp);
                span =initDom("<span></span>",attrarr);
                if(v.ukmark!=""&&v.ukmark!=undefined){
                    span.text("["+v.ukmark+"]");
                }
                span.appendTo(li);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH6";
                attrarr.push(temp);
                var h6 =initDom("<h6></h6>",attrarr);
                h6.text(v.morphology+v.explains);
                h6.appendTo(li);
            });
            h5.find("font").text(wordnum);
        },
        error:function(xhr,type){
            hideloading();

        }
    })
};

//<li><a class="kuai"><label>5</label><span class="gksuo"><i class="icon-suo"></i></span></a></li>
var getGameLevelsList = function(url,listobj){
    $(listobj).empty();
    var Request = new Object();
    Request = GetRequest();
    var id=Request["id"];
    $.ajax({
        type:'POST',
        url:url,
        data:{id:id},
        dataType:'json',
        async:true,
        context:$('body'),
        success: function(data){
            //计算总的个数
            var length=data.length;
            
            //每页显示的个数
            var per=8;
            //显示几个框架
            var page=Math.floor(length/per);
            //剩下的个数
            var overplus=data.length%per;
            //遮罩消失
            hideloading();
            $("#wordcount").text(data.length);
            var attrarr=[];
            var temp={};
            var pdiv="";
            var cdiv="";
            if(overplus>0){
                if(page%2==0){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bBlueM";
                    attrarr.push(temp);
                    var pdiv =initDom("<div></div>",attrarr);
                    pdiv.appendTo(listobj);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="xguan";
                    attrarr.push(temp);
                    var cdiv =initDom("<div></div>",attrarr);
                    cdiv.appendTo(pdiv);
                    //<li><a class="kuai"><label>5</label><span class="gksuo"><i class="icon-suo"></i></span></a></li>
                }else{
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bBlueM";
                    attrarr.push(temp);
                    var pdiv =initDom("<div></div>",attrarr);
                    pdiv.appendTo(listobj);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="xguan";
                    attrarr.push(temp);
                    var cdiv =initDom("<div></div>",attrarr);
                    cdiv.appendTo(pdiv);
                }
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="fenge";
                attrarr.push(temp);
                var ul =initDom("<ul></ul>",attrarr);
                ul.appendTo(cdiv);
                for(var j=(per-overplus-1);j>=0;j--){
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(ul);
                }
                //加载不正常的
                for(var j=(overplus-1);j>=0;j--){
                    attrarr=[];
                    temp={};
                    temp.id="level";
                    temp.val=page*per+j+1;
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(ul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="kuai";
                    attrarr.push(temp);
                    temp={};
                    temp.id="addr";
                    temp.val="wordlist?gameid="+Request["id"]+"&index="+(page*per+j);
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.appendTo(li);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var label =initDom("<label></label>",attrarr);
                    label.appendTo(a);
                    label.text(page*per+j+1);
                    if(data[page*per+j].score=='-1'){
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="gksuo";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        span.appendTo(a);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="icon-suo";
                        attrarr.push(temp);
                        var ic =initDom("<i></i>",attrarr);
                        ic.appendTo(span);
                    }else{
                        //<span class="pxing"><i class="icon-shoucang colorY"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i></span>
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="pxing";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        span.appendTo(a);
                        if(parseInt(data[page*per+j].score)<=75){
                            for(var x=0;x<3;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }else if(parseInt(data[page*per+j].score)>75&&parseInt(data[page*per+j].score)<=89){
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            for(var x=0;x<2;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }else if(parseInt(data[page*per+j].score)>89&&parseInt(data[page*per+j].score)<=99){
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            for(var x=0;x<1;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }

                        }else if(parseInt(data[page*per+j].score)==100){
                            for(var x=0;x<3;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang colorY";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }
                    }
                    
                }
                cdiv.height(ul.height());
            }
            //首先循环进行进行段的显示
            // <div  class="bYelloM">
            // <div class="xguan ">
            for(var i=(page-1);i>=0;i--){
                if(i%2==0){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bBlueM";
                    attrarr.push(temp);
                    var pdiv =initDom("<div></div>",attrarr);
                    pdiv.appendTo(listobj);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="xguan";
                    attrarr.push(temp);
                    var cdiv =initDom("<div></div>",attrarr);
                    cdiv.appendTo(pdiv);
                    //<li><a class="kuai"><label>5</label><span class="gksuo"><i class="icon-suo"></i></span></a></li>
                }else{
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="bBlueM";
                    attrarr.push(temp);
                    var pdiv =initDom("<div></div>",attrarr);
                    pdiv.appendTo(listobj);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="xguan";
                    attrarr.push(temp);
                    var cdiv =initDom("<div></div>",attrarr);
                    cdiv.appendTo(pdiv);
                }
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="fenge";
                attrarr.push(temp);
                var ul =initDom("<ul></ul>",attrarr);
                ul.appendTo(cdiv);
                for(var j=(per-1);j>=0;j--){
                    attrarr=[];
                    temp={};
                    temp.id="level";
                    temp.val=per*i+j+1;
                    attrarr.push(temp);
                    var li =initDom("<li></li>",attrarr);
                    li.appendTo(ul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="kuai";
                    attrarr.push(temp);
                    temp={};
                    temp.id="addr";
                    temp.val="wordlist?gameid="+Request["id"]+"&index="+(per*i+j);
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0)";
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.appendTo(li);
                    attrarr=[];
                    temp={};
                    attrarr.push(temp);
                    var label =initDom("<label></label>",attrarr);
                    label.appendTo(a);
                    label.text(per*i+j+1);
                    if(data[per*i+j].score=='-1'){
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="gksuo";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        span.appendTo(a);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="icon-suo";
                        attrarr.push(temp);
                        var ic =initDom("<i></i>",attrarr);
                        ic.appendTo(span);
                    }else{
                        //<span class="pxing"><i class="icon-shoucang colorY"></i><i class="icon-shoucang"></i><i class="icon-shoucang"></i></span>
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="pxing";
                        attrarr.push(temp);
                        var span =initDom("<span></span>",attrarr);
                        span.appendTo(a);
                        if(parseInt(data[per*i+j].score)<=75){
                            for(var x=0;x<3;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }else if(parseInt(data[per*i+j].score)>75&&parseInt(data[per*i+j].score)<=89){
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            for(var x=0;x<2;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }else if(parseInt(data[per*i+j].score)>89&&parseInt(data[per*i+j].score)<=99){
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            attrarr=[];
                            temp={};
                            temp.id="class";
                            temp.val="icon-shoucang colorY";
                            attrarr.push(temp);
                            var ic =initDom("<i></i>",attrarr);
                            ic.appendTo(span);
                            for(var x=0;x<1;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }else if(parseInt(data[per*i+j].score)==100){
                            for(var x=0;x<3;x++){
                                attrarr=[];
                                temp={};
                                temp.id="class";
                                temp.val="icon-shoucang colorY";
                                attrarr.push(temp);
                                var ic =initDom("<i></i>",attrarr);
                                ic.appendTo(span);
                            }
                        }
                    }
                }
                cdiv.height(ul.height());
            }
            //设置用户可以做的关卡
            var diff=$(".gksuo").last().attr("diff");
            $(".gksuo").last().empty();
            for(var x=0;x<3;x++){
                attrarr=[];
                temp={};
                if(x<parseInt(diff)){
                    temp.id="class";
                    temp.val="icon-shoucang colorY";
                }else{
                    temp.id="class";
                    temp.val="icon-shoucang";
                }
                attrarr.push(temp);
                var ic =initDom("<i></i>",attrarr);
                ic.appendTo($(".gksuo").last());
            }
            $(".gksuo").last().removeClass("gksuo").addClass("pxing");
            var myScroll=new IScroll("#wrapper",{
                momentum:true,
                click:true 
            });
            //将用户的头像添加到列表中
            $("span.pxing").parents("li").last().append($('<a class="myTx"><div class="posR jtou"><img src="../../public/Subject/img/book.jpg"></div></a>'));
            // if(maxid!='0'){
            //     $("li[level='"+maxid+"']").append($('<a class="friend friendH1"><img src="../../public/Subject/img/toux.png"></a>'));
            // }
            var curlevel=parseInt($("span.pxing").parents("li").last().attr("level"));
            //判断最大的lebvel的高度
            var screen=$(window).height()-45;
            var nodes=Math.round(screen/77);
            var overplus=screen%77;
            var overnodes=8-(nodes%8);
            var curheight=$('li[level="'+curlevel+'"]').offset().top;
            if((curheight+77)>screen){
                myScroll.scrollTo(0,-(curheight+77-screen),0, IScroll.utils.ease.elastic);
            }
            //判断当前的高度
            //if(curheight<)
            //myScroll.scrollTo(0,-(curheight+45+(77*overnodes)-screen),0, IScroll.utils.ease.elastic);
        },
        error:function(xhr,type){
            hideloading();
        }
    })
};


var getGameQuestionsList = function(url,id,listobj){
    $(listobj).empty();
    $.ajax({
        type:'GET',
        url:url,
        data:{id:id},
        dataType:'json',
        async:false,
        context:$('body'),
        success: function(data){
            //遮罩消失
            hideloading();
            //翻页问题
            // $('.cgTi').height($(window).height() - 55);
            var count=0;    
            setTimeout(function () {
                $('#dati').show();
                $(".wrap").remove();
                var len=0;
                questionslist=data;
                $.each(data,function(key,values){questionskeys.push(key);});
                var key=questionskeys[0];
                var value=questionslist[questionskeys[0]];
                var questype=value.questype;
                var tntype=0;
                var itemtype="single";
                if(questype=='6'){
                    tntype=4;
                    itemtype="spell";
                }else if(questype=='0'||questype=='1'){
                    tntype=2;
                }else if(questype=='3'){
                    tntype=3;
                }
                var itemviewtype=value.items[0].typeid;
                console.log(questionobj); 
                var question=questionobj.setQuestion(0,tntype,itemtype,itemviewtype,key,value);
                $(listobj).append(question);
            }, 1500);
        },
        error:function(xhr,type){
            hideloading();
        }
    })
};

//初始化单词学习的列表
var initRecordList=function(url,listobj){
    $.ajax({
        type:'GET',
        url:url,
        //data:{ks_code:ks_code},
        dataType:'json',
        async:false,
        context:$('body'),
        success: function(data){
            //遮罩消失
            hideloading();
            $("#wordcount").text(data.data.length);
            $.each(data.data,function(k,v){
                //<li class="table"><div><span>floor</span><span>[flɔ:(r)]</span><h6 class="textH6">n.楼层;地面，地板;底部;议员席</h6></div><span class="w50 tcen"><i class="icon-del"></i><h6 class="font08e">删除</h6></span></li>
                //动态创建单词的元素
                var attrarr=[];
                var temp={};
                temp.id="class";
                temp.val="table";
                attrarr.push(temp);
                var li =initDom("<li></li>",attrarr);
                li.appendTo(listobj);
                //<div class="check"><div class="check-box"><i><input type="checkbox" name="check-box"></i></div></div>
                attrarr=[];
                temp={};
                attrarr.push(temp);
                var pdiv =initDom("<div></div>",attrarr);
                pdiv.appendTo(li);
                attrarr=[];
                temp={};
                attrarr.push(temp);
                var span =initDom("<span></span>",attrarr);
                span.html(v.word);
                span.appendTo(pdiv);
                attrarr=[];
                temp={};
                attrarr.push(temp);
                span =initDom("<span></span>",attrarr);
                if(v.ukmark!=""&&v.ukmark!=null&&v.ukmark!="null"){
                    span.html("["+v.ukmark+"]");
                }
                span.appendTo(pdiv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH6";
                attrarr.push(temp);
                var h6 =initDom("<h6></h6>",attrarr);
                if(v.morphology==null&&v.explains==null){

                }else{
                    h6.html(v.morphology+v.explains);
                }
                
                h6.appendTo(pdiv);
                //<span class="w50 tcen"><i class="icon-del"></i><h6 class="font08e">删除</h6></span>
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="w50 tcen";
                attrarr.push(temp);
                temp={};
                temp.id="bookid";
                temp.val=v.bookid;
                attrarr.push(temp);
                var cdiv =initDom("<span></span>",attrarr);
                cdiv.html('<i class="icon-del"></i><h6 class="font08e">删除</h6>');
                cdiv.appendTo(li);
                cdiv.bind("click",function(){
                    var bookid=$(this).attr("bookid");
                    $.ajax({
                        type:'GET',
                        url:"../User/delUserGameVocabulary",
                        data:{bookid:bookid,ran:Math.random()},
                        dataType:'json',
                        timeout: 300,
                        context:$(this),
                        complete:function(){
                            $(this).parent().remove();
                            $("#wordcount").text(parseInt($("#wordcount").text())-1);
                            hideloading();
                            setTip("删除成功");
                        }
                    });
                });
            });
        },
        error:function(xhr,type){
            hideloading();
        }
    })
}

//编辑排名
var initRankList=function(url,listobj,type){
    var Request = new Object();
    Request = GetRequest();
    var gameid=Request["gameid"];
    $(listobj).empty();
    $.ajax({
        type:'GET',
        url:url,
        data:{gameid:gameid,type:type},
        dataType:'json',
        async:false,
        context:$('body'),
        success: function(data){
            if(data.ranklist.length==0){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="table";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="text-align:center;";
                attrarr.push(temp);
                var userli =initDom("<li></li>",attrarr);
                userli.text("暂无数据");
                userli.appendTo(listobj);
            }else{
                $("#truename").html(data.truename);
                var attrarr=[];
                var temp={};
                //遮罩消失
                var rank=1;
                var isexist=0;
                $.each(data.ranklist,function(k,v){
                    if(data.username==v.username){
                        isexist=1;
                        $("#levelnum").text(v.levelnum+"关");
                        $("#userrank").text(k+1);
                        $("#myword").text(data.mywordsnum);
                    }
                    //动态创建单词的元素
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="listIconText table";
                    attrarr.push(temp);
                    var userli =initDom("<li></li>",attrarr);
                    userli.appendTo(listobj);
                    //用户的排名信息
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="lico";
                    attrarr.push(temp);
                    var span =initDom("<span></span>",attrarr);
                    span.appendTo(userli);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    if(rank==1){
                        temp.val="icon-topped first";
                    }else if(rank==2){
                        temp.val="icon-topped second";    
                    }else if(rank==3){
                        temp.val="icon-topped third";
                    }else{
                        temp.val="icon-other";
                    }
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="color:grey;";
                    attrarr.push(temp);
                    var i =initDom("<i></i>",attrarr);
                    if(rank>3){
                        i.text(rank);
                    }
                    i.appendTo(span);
                    //测试的类型
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="listText";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.appendTo(userli);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="topH3";
                    attrarr.push(temp);
                    var label =initDom("<label></label>",attrarr);
                    label.appendTo(span);
                    label.text(v.truename);
                    //用户的分数<span class="topH3 fr">16分</span>
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="w100 textFR blueFont";
                    attrarr.push(temp);
                    span =initDom("<span></span>",attrarr);
                    span.html(v.levelnum+'关');
                    span.appendTo(userli);
                    rank=rank+1;
                });
                if(isexist==0){
                    $("#user").remove();
                    $("#wrapper").css("top","0px");
                }else{
                    $("#user").show();
                    $("#wrapper").css("top","82px");
                }
            }
            new IScroll("#wrapper",{
                momentum:true,
                click:true 
            });
            hideloading();
        },
        error:function(xhr,type){
            hideloading();
        }
    })
}

//星星排名
var initStarRankList=function(data,listobj){
    if(data.ranklist.length>0){
        $("#truename").html(data.truename);
        var attrarr=[];
        var temp={};
        //遮罩消失
        var rank=1;
        var isexist=0;
        $.each(data.ranklist,function(k,v){
            if(data.username==v.username){
                isexist=1;
                $("#levelnum").html(v.levelnum);
                $("#userrank").text(k+1);
            }
            //动态创建单词的元素
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="listIconText table";
            attrarr.push(temp);
            var userli =initDom("<li></li>",attrarr);
            userli.appendTo(listobj);
            //用户的排名信息
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="lico";
            attrarr.push(temp);
            var span =initDom("<span></span>",attrarr);
            span.appendTo(userli);
            attrarr=[];
            temp={};
            temp.id="class";
            if(rank==1){
                temp.val="icon-topped first";
            }else if(rank==2){
                temp.val="icon-topped second";    
            }else if(rank==3){
                temp.val="icon-topped third";
            }else{
                temp.val="icon-other";
            }
            attrarr.push(temp);
            var i =initDom("<i></i>",attrarr);
            if(rank>3){
                i.text(rank);
            }
            i.appendTo(span);
            //测试的类型
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="listText";
            attrarr.push(temp);
            span =initDom("<span></span>",attrarr);
            span.appendTo(userli);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="topH3";
            attrarr.push(temp);
            var label =initDom("<label></label>",attrarr);
            label.appendTo(span);
            label.text(v.truename);
            //用户的分数<span class="topH3 fr">16分</span>
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="w100 textFR blueFont";
            attrarr.push(temp);
            span =initDom("<span></span>",attrarr);
            span.html(v.levelnum+'<font class="orgFont pad0-10"><i class="icon-shoucang"></i></font>');
            span.appendTo(userli);
            rank=rank+1;
        });
    }
}

var getWordDict=function(url,listobj,wordid,explainid){
    $.ajax({
        type:'GET',
        url:url,
        data:{wordid:wordid,explainid:explainid},
        dataType:'json',
        async:true,
        context:$('body'),
        success: function(data){
            setTimeout(function(){
                try{
                    UXinJSInterfaceSpeech.cacheAudioFiles(iGetInnerText(JSON.stringify(data.mp3list)));
                }catch(e){
                    setTip("升级到最新版本的优信");
                }
            },500);
            
            //查询并且创建自己的排名情况
            var attrarr=[];
            var temp={};
            temp.id="class";
            temp.val="pad10";
            attrarr.push(temp);
            var userdiv =initDom("<div></div>",attrarr);
            userdiv.appendTo(listobj);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="textH2";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="height:auto;";
            attrarr.push(temp);
            var h2 =initDom("<h2></h2>",attrarr);
            if(data.words.ukmark!=""&&data.words.ukmark!=null&&data.words.ukmark!="null"){
                h2.html(data.words.word)
            }else{
                h2.html(data.words.word+'<a class="abo" onclick="javascript:var mp3=$(this).attr(\'mp3\');try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{UXinJSInterfaceSpeech.playAudio(mp3);}catch(e){setTip(\'请升级到最新的优信\');}" mp3="'+data.words.ukmp3+'"><i class="icon-song2"></i></a>')
            }
            
            h2.appendTo(userdiv);
            if(data.words.ukmark!=""&&data.words.ukmark!=null&&data.words.ukmark!="null"){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textH6 tcen";
                attrarr.push(temp);
                var h6 =initDom("<h6></h6>",attrarr);
                h6.html("["+data.words.ukmark+']<a class="abo" onclick="javascript:var mp3=$(this).attr(\'mp3\');try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{UXinJSInterfaceSpeech.playAudio(mp3);}catch(e){setTip(\'请升级到最新的优信\');}" mp3="'+data.words.ukmp3+'"><i class="icon-song2"></i></a>');
                h6.appendTo(userdiv);
            }
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="pad10";
            attrarr.push(temp);
            var expdiv =initDom("<div></div>",attrarr);
            expdiv.appendTo(listobj);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="textP mb10";
            attrarr.push(temp);
            var p =initDom("<p></p>",attrarr);
            p.html('<span class="border_kt">中</span><span>'+data.words.morphology+'</span><span>'+data.words.explains+'</span>');
            p.appendTo(expdiv);
            if(data.words.enexplains!=""){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textP mb10";
                attrarr.push(temp);
                var p =initDom("<p></p>",attrarr);
                p.html('<span class="border_kt">英</span><span>'+data.words.morphology+'</span><span>'+data.words.enexplains+'</span>');
                p.appendTo(expdiv);
            }
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="pad10";
            attrarr.push(temp);
            userdiv =initDom("<div></div>",attrarr);
            userdiv.appendTo(listobj);
            $.each(data.examples,function(key,val){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="table";
                attrarr.push(temp);
                var tablediv =initDom("<div></div>",attrarr);
                tablediv.appendTo(userdiv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="w100";
                attrarr.push(temp);
                span =initDom("<span></span>",attrarr);
                span.html('<img src="'+val.expic+'" class="img100" />')
                span.appendTo(tablediv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="pad0-10 font08e";
                attrarr.push(temp);
                var ediv =initDom("<div></div>",attrarr);
                ediv.appendTo(tablediv);
                attrarr=[];
                temp={};
                attrarr.push(temp);
                p =initDom("<p></p>",attrarr);
                p.html(val.encontent+'<a class="abo" onclick="javascript:var mp3=$(this).attr(\'mp3\');try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{UXinJSInterfaceSpeech.playAudio(mp3);}catch(e){setTip(\'请升级到最新的优信\');}" mp3="'+val.exmp3+'"><i class="icon-song2"></i></a>');
                p.appendTo(ediv);
                p.find("span").attr("style","color:red;");
                attrarr=[];
                temp={};
                attrarr.push(temp);
                p =initDom("<p></p>",attrarr);
                p.html(val.cncontent);
                p.appendTo(ediv);
            })

            //扩展用法
            $.each(data.words.extend_json,function(key,val){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="pad10 afterBorder4";
                attrarr.push(temp);
                var tablediv =initDom("<div></div>",attrarr);
                tablediv.appendTo(userdiv);
                //<p class="textP mb10"><span class="backGyin pad0-10">记忆法</span></p>
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="textP mb10";
                attrarr.push(temp);
                var p =initDom("<p></p>",attrarr);
                if(val.typeid=="1"){
                    p.html('<span class="backGyin pad0-10">词型</span>');
                }else if(val.typeid=="2"){
                    p.html('<span class="backGyin pad0-10">记忆法</span>');
                }else if(val.typeid=="3"){
                    p.html('<span class="backGyin pad0-10">发散记忆</span>');
                }else if(val.typeid=="4"){
                    p.html('<span class="backGyin pad0-10">拓展用法</span>');
                }
                p.appendTo(tablediv);
                $.each(val.value,function(k,v){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="textP mb10";
                    attrarr.push(temp);
                    var p =initDom("<p></p>",attrarr);
                    p.html('<b style="display:block;">'+v.name+'</b>'+v.explains);
                    p.appendTo(tablediv);
                })
            })
            
            hideloading();
        },
        error:function(xhr,type){
            hideloading();
        }
    })
}


var getWordStudyList=function(url,imgobj){
    var iswords=0;
    $.ajax({
        type:'GET',
        url:url,
        dataType:'json',
        success: function(data){
            //遮罩消失
            hideloading();
            //初始化MP3对象
            //初始化MP3对象
            mp3.playInit(data.data,data.downlist,"","cur");

            //初始化下载列表
            mp3.setPlaystatus(0);
            setTimeout(function(){mp3.mp3dowload();},2500);
            
            $.each(data.data,function(k,v){
                //动态创建单词的元素
                if(v.isword==0){
                    iswords=1;
                }
                var attrarr=[];
                var temp={};
                //初始化图文模式的单词
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="con";
                attrarr.push(temp);
                var imgdiv =initDom("<div></div>",attrarr);
                imgdiv.appendTo(imgobj);
                attrarr=[];
                var imgul =initDom("<ul></ul>",attrarr);
                imgul.appendTo(imgdiv);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="scrollCon";
                attrarr.push(temp);
                imgdiv =initDom("<div></div>",attrarr);
                imgdiv.appendTo(imgul);
                attrarr=[];
                temp={};
                temp.id="onclick";
                temp.val="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio('"+v.name+"');}catch(e){setTip('请升级到最新的优信');};";
                attrarr.push(temp);
                var imgp =initDom("<p></p>",attrarr);
                imgp.appendTo(imgdiv);
                attrarr=[];
                temp={};
                temp.id="src";
                temp.val=v.pic;
                attrarr.push(temp);
                temp={};
                temp.id="width";
                temp.val=(window.screen.width-10)+"px";
                attrarr.push(temp);
                temp={};
                temp.id="height";
                temp.val=(window.screen.width-10)*3/4+"px";
                attrarr.push(temp);
                imgimg =initDom("<img></img>",attrarr);
                imgimg.appendTo(imgp);
                attrarr=[];
                temp.id="onclick";
                temp.val="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio('"+v.name+"');}catch(e){setTip('请升级到最新的优信');};";
                attrarr.push(temp);
                var imgp =initDom("<p></p>",attrarr);
                imgp.appendTo(imgdiv);
                // attrarr=[];
                // temp={};
                // temp.id="class";
                // temp.val="aBtn bGray02 border-nav displayword";
                // attrarr.push(temp);
                // temp={};
                // temp.id="style";
                // temp.val="display:none;margin:4px;";
                // attrarr.push(temp);
                // temp={};
                // temp.id="mp3name";
                // temp.val=v.name;
                // attrarr.push(temp);
                // var imga =initDom("<a></a>",attrarr);
                // imga.text("点击显示单词");
                // imga.appendTo(imgp);
                
                if(v.ukmark!=null&&v.ukmark!=""&&v.ukmark!=undefined){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="worddisplay";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:block;";
                    attrarr.push(temp);
                    var imgspan =initDom("<span></span>",attrarr);
                    imgspan.appendTo(imgp);
                    imgspan.text(v.word);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="wordremark alound ";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:block;";
                    attrarr.push(temp);
                    var imgspan =initDom("<span></span>",attrarr);
                    imgspan.html("["+v.ukmark+"]<a class=\"icc-song\" href=\"javascript:void(0);\"><i class=\"icon-uniE60C\"></i></a>");
                    imgspan.appendTo(imgp);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="wordexpl";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:block;";
                    attrarr.push(temp);
                    var imgspan =initDom("<span></span>",attrarr);
                    if(v.explains!=null&&v.explains!='null'&&v.examples!=""&&v.examples!=0){
                        imgspan.html(v.morphology+v.explains);
                    }
                    imgspan.appendTo(imgp);
                }else{
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="worddisplay";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:block;";
                    attrarr.push(temp);
                    var imgspan =initDom("<span></span>",attrarr);
                    imgspan.appendTo(imgp);
                    imgspan.html(v.word);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="wordexpl";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    temp.val="display:block;";
                    attrarr.push(temp);
                    var imgspan =initDom("<span></span>",attrarr);
                    if(v.explains!=null&&v.explains!='null'&&v.examples!=""&&v.examples!=0){
                        imgspan.html(v.morphology+v.explains+"<a class=\"icc-song\" href=\"javascript:void(0);\"><i class=\"icon-uniE60C\"></i></a>");
                    }else{
                        imgspan.html("<a class=\"icc-song\" href=\"javascript:void(0);\"><i class=\"icon-uniE60C\"></i></a>");
                    }
                    
                    imgspan.appendTo(imgp);
                    //imgspan.html(v.word+'<a style="margin-left:10px;" href="javascript:try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio("'+v.name+'");}catch(e){setTip(/"请升级到最新的优信/");};');
                }
                
                
                attrarr=[];
                temp={};
                temp.id="wordid";
                temp.val=v.id;
                attrarr.push(temp);
                temp={};
                temp.id="class";
                temp.val="collect";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="cursor:pointer;";
                attrarr.push(temp);
                imgp =initDom("<p></p>",attrarr);
                imgp.appendTo(imgdiv);
            });
            TouchSlide({ 
                slideCell:"#iScroll",
                titCell:".hd li",    
                autoPage:true, //自动分页
                effect:"leftLoop",
                prevCell : '#prev',
                nextCell : '#next',
                defaultIndex:defaultIndex,
                startFun:function(i){
                    var bd = document.getElementById("iScroll-bd");
                    if($("#iScroll-bd").height()<$(window).height()-88-46){
                        bd.style.height=$(window).height()-40+"px";
                    }
                },
                endFun:function(i){ //高度自适应
                    //进行显示单词事件的注册
                    $(".displayword").click(function(){
                        $(this).parent().find("span").show();
                        $(this).hide();
                        var name=$(this).attr("mp3name");
                       try{UXinJSInterfaceSpeech.stopAudio();}catch(e){};
                       try{mp3.setUse(0);UXinJSInterfaceSpeech.playAudio(name);}catch(e){setTip('请升级到最新的优信');}
                        
                    });
                    defaultIndex=defaultIndex+1;
                    //clearTimeout();
                    // try{
                    //     UXinJSInterfaceSpeech.stopAudio();
                    // }catch(e){
                    //     setTip("请升级到最新的优信");
                    // }
                    //判断当前是播放状态还是什么状态
                    var status=$("#audioplay").hasClass("play");
                    mp3.setCurindex(i);
                    console.log($(".con").eq(i).find(".alound"));
                    try{
                        // if(mp3.getPlaystatus()==0){
                        //     $(".con").eq(i).find(".icc-song").click();
                        // }
                    }catch(e){

                    }
                    
                    // var bd = document.getElementById("iScroll-bd");
                    // bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";
                    // if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
                }
            });
            if(iswords>0&&iswords==data.length){
                mp3.setNourl(1);
            }
        },
        error:function(xhr,type){
            hideloading();

        }
    })
}