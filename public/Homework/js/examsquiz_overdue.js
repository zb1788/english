//地址参数
// var homeworkid = GetQueryString("homeworkid");
// var studentid = GetQueryString("username");
// var classid = GetQueryString("classid");
//初始化音频参数
var mp3_progress='';
var mp3_progress_reap="";
//基础数据的初始化
var mySwiper = null;
var timer = null;
var errorQuestion = [];
var exist_flag = false;
var mode = false;
var errorIndex = 0;
//var homework_questions = window.localStorage.getItem("enhomework");
var question_start_time = new Date().getTime();
window.localStorage.clear();
var homework_questions = {};
homework_questions.id = homeworkid;
homework_questions.questions = [];
window.localStorage.setItem("enhomework",JSON.stringify(homework_questions));

var homework_questions_answer = [];
var homework_start_time = new Date().getTime();
var isback = true;
//前端mui框架初始化
mui.init({
    swipeBack:false //启用右滑关闭功能
});
var mask = mui.createMask(function(){
    $(".mui-backdrop").empty();
    $(".mui-backdrop").append($("<div></div>").addClass("ques_tips").html("作业提交中......"));
});//callback为用户点击蒙版时自动执行的回调；

mask.show();//关闭遮罩
mask.close();
//view单页视图
var viewApi = mui('#app').view({
    defaultPage: '#setting'
});
var view = viewApi.view;//创建view
var oldBack = mui.back;
mui.back = function() {
    if(isback){
        //window.UXinJSInterface.setOnlinWorkHaveFinished(batchid);
        window.UXinJSInterface.popTheController();
    }else{
        $(".homeworksubmit").hide();
        $(".mui-view").css("height","100%");
        isback = true;
        viewApi.back();
    }
};

//获取作业的试题并且进行试题的初始化
$.get("../Mobhw/getHomeworkInfo",{
    homeworkid:homeworkid,
    studentid:studentid,
    classid:classid
},function(data){
    //渲染顺序 1、单词跟读 2、单词测试 3、课文跟读 4、试卷
    var first = 0;
    if(data.wordread.length == 0){first = 1};
    if(data.textread.length == 0 && first == 1){first = 2};
    if(data.wordtest.length == 0 && first == 2){first = 3};
    $(".bd").empty();
    var fragment = document.createDocumentFragment();
    var htmlContent = [];
    var wordread = data.wordread;
    //只出第一个
    if(data.wordread.length>0){
        var question = {};
        question.word = wordread[0].word;
        question.explains = wordread[0].explains;
        question.ukmark = wordread[0].ukmark;
        question.pic = word_pic_url+wordread[0].pic;
        question.mp3 = word_mp3_url+wordread[0].ukmp3;
        question.readid = wordread[0].id;
        question.contentid = wordread[0].id;
        question.content = wordread[0].word;
        question.datatype = 0;
        question.homeworkid = homeworkid;
        question.useranswer = wordread[0].useranswer;
        //if(exist_flag){
            homework_questions.questions.push(question);
        //}
        fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderWordAlound(question))[0]);
    }else if(data.textread.length>0){
        var question = {};
        var value = data.textread[0];
        question.encontent = value.encontent;
        question.cncontent = value.cncontent;
        question.mp3 = text_mp3_url+value.mp3.substr(0,2)+"/"+value.mp3+".mp3";
        question.readid = value.id;
        question.contentid = value.textid;
        question.content = value.encontent;
        question.datatype = 1;
        question.homeworkid = homeworkid;
        question.useranswer = value.useranswer;
        //if(exist_flag){
            homework_questions.questions.push(question);
        //}
        fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderTextAlound(question))[0]);
    }else if(data.wordtest.length>0){
        var question = {};
        var value = data.wordtest[0];
        if(value.typeid == 0 ){
            question.tncontent = value.explains;
            var items = [];
            var item = {};
            item.flag = "A";
            item.content = value.option_a;
            item.value = "A";
            items.push(item);
            item = {};
            item.flag = "B";
            item.content = value.option_b;
            item.value = "B";
            items.push(item);
            item = {};
            item.flag = "C";
            item.content = value.option_c;
            item.value = "C";
            items.push(item);
            question.items = items;
            question.questionid = value.id;
            question.typeid = 0;
            question.score = 1;
            question.answer = value.answer;
            question.mp3 = word_mp3_url + value.ukmp3;
            question.wordid = value.wordid;
            question.datatype = 2;
            question.chapterid = value.chapterid?value.chapterid:0;
            question.homeworkid = homeworkid;
            question.useranswer = value.useranswer;
            //if(exist_flag){
                homework_questions.questions.push(question);
            //}
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(question))[0]);
        }else if(value.typeid == 1 || value.typeid == 3){
            question.tncontent = (value.typeid == 1?value.word:value.explains);
            var items = [];
            var item = {};
            item.flag = "A";
            item.content = value.option_a;
            items.push(item);
            item = {};
            item.flag = "B";
            item.content = value.option_b;
            items.push(item);
            item = {};
            item.flag = "C";
            item.content = value.option_c;
            items.push(item);
            question.items = items;
            question.questionid = value.id;
            question.typeid = value.typeid;
            question.answer = value.answer;
            question.mp3 = word_mp3_url + value.ukmp3;
            question.wordid = value.wordid;
            question.chapterid = 0;
            question.datatype = 2;
            question.score = 1;
            question.homeworkid = homeworkid;
            question.useranswer = value.useranswer;
            //if(exist_flag){
                homework_questions.questions.push(question);
            //}
           // console.log(question);
            //return false;
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(question))[0]);
        }else if(value.typeid == 2){
            question.tncontent = value.explains;
            var options = value.option_a.split(",");
            var items = [];
            $.each(options,function(k,v){
                var item = {};
                item.flag = "";
                item.content = v;
                items.push(item);
            })
            question.items = items;
            question.questionid = value.id;
            question.typeid = 2;
            question.score = 1;
            question.wordid = value.wordid;
            question.chapterid = 0;
            question.answer = value.word.split("");
            question.mp3 = word_mp3_url + value.ukmp3;
            question.datatype = 2;
            question.homeworkid = homeworkid;
            question.useranswer = value.useranswer;
            //if(exist_flag){
                homework_questions.questions.push(question);
//}
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderSpell(question))[0]);
        }
    }else if(data.exampaper.length>0){

        var question = {};
        var value = data.exampaper[0];
        question.content = value.content;
        question.questionid = value.quesid;
        question.tcontent = value.tcontent;
        question.items = JSON.parse(decodeURIComponent(value.questions_items));
        question.answer = JSON.parse(decodeURIComponent(value.questions_answer));
        question.questts = JSON.parse(decodeURIComponent(value.questions_tts));
        //console.log("sss"+question.items);
        question.score = value.question_score;
        question.question_playtimes = value.question_playtimes;
        question.datatype = 3;
        question.itemtype = value.itemtype;
        question.examsid = value.examsid;
        question.typeid = value.typeid;
        question.quizid = value.quizid;
        question.questionid =  value.quesid;
        question.homeworkid =  homeworkid;
        question.useranswer = value.useranswer;
        //if(exist_flag){
            homework_questions.questions.push(question);
        //}
        fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderExamPaper(question))[0]);
    }
    
    $(".bd")[0].appendChild(fragment);
    $(".swiper-slide").attr("style","height:"+window.innerHeight+"px;");
    $("#quesnumcount").html(data.wordread.length+data.textread.length+data.wordtest.length+data.exampaper.length);
    mySwiper = new Swiper('.swiper-container', {
        autoplay: false,//可选选项，自动滑动
        on: {
            touchStart: function(event){
               
                clearTimeout(timer);
                 var curindex = $(".swiper-slide-active").index();
                 //console.log(curindex);
                $(".tips").click(function(){
                    mask.show();//关闭遮罩
                })

            },
            touchEnd:function(event){
               //console.log("sss"); 
            },
            transitionStart:function(event){
                var curindex = $(".swiper-slide-active").index();
               // console.log(curindex);
                //直接将元素置空
                $("#playing").attr("src","../../public/Homework/images/sy.png");
                $("#playing").attr("id","");
                stopaudio();
            },
            transitionEnd:function(event){
                var curindex = $(".swiper-slide-active").index();
                homework_question_index = curindex;
                $("#quesindex").html(curindex+1);
                question_start_time = new Date().getTime();
                var type = homework_questions.questions[homework_question_index].datatype;
                var content = "单词跟读";
                if(type == 1){
                    content = "课文跟读"; 
                }else if(type == 2){
                    content = "单词测试";
                    var typeid = homework_questions.questions[homework_question_index].typeid;
                    if(typeid == 0){
                        content = "听音选词";
                    }else if(typeid == 1 || typeid == 3){
                        content = "英汉互译";
                    }else{
                        content = "单词拼写";
                    }
                }else if(type == 3){
                    content = "听力试卷";
                }
                $("#type").html(content);
            },
            init:function(event){
                $(".loading-img").remove();
                $("#quesindex").html(1);
                $(".bd .swiper-slide").show();
                homework_question_index = 0;
                homework_start_time = new Date().getTime();
                $(".title.alound .img").height((window.innerWidth-10)*0.6);
                $(".title.alound .img").width((window.innerWidth-10)*0.6*4/3);
                replaceExamPaperImg($(".tgimg"));
                $(".tigan .tgimg").width(window.innerWidth-10);
                var type = homework_questions.questions[homework_question_index].datatype;
                var content = "单词跟读";
                if(type == 1){
                    content = "课文跟读"; 
                }else if(type == 2){
                    content = "单词测试";
                    var typeid = homework_questions.questions[homework_question_index].typeid;
                    if(typeid == 0){
                        content = "听音选词";
                    }else if(typeid == 1 || typeid == 3){
                        content = "英汉互译";
                    }else{
                        content = "单词拼写";
                    }
                }else if(type == 3){
                    content = "听力试卷";
                }
                $("#type").html(content);
            }
        }
    })
    setTimeout(function(){
        var wordread = data.wordread;
        $.each(wordread,function(key,value){
            if(first == 0 && key == 0){
                return true;
            }
            var question = {};
            question.word = value.word;
            question.explains = value.explains;
            question.ukmark = value.ukmark;
            question.pic = word_pic_url+value.pic;
            question.mp3 = word_mp3_url+value.ukmp3;
            question.readid = value.id;
            question.contentid = value.id;
            question.content = value.word;
            question.datatype = 0;
            question.homeworkid = homeworkid;
            question.useranswer = value.useranswer;
            //if(exist_flag){
                homework_questions.questions.push(question);
            //}
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderWordAlound(question))[0]);
        })
        var textread = data.textread;
        $.each(textread,function(key,value){
            if(first == 1 && key == 0){
                return true
            }
            var question = {};
            question.encontent = value.encontent;
            question.cncontent = value.cncontent;
            question.mp3 = text_mp3_url+value.mp3.substr(0,2)+"/"+value.mp3+".mp3";
            question.readid = value.id;
            question.contentid = value.textid;
            question.content = value.encontent;
            question.datatype = 1;
            question.homeworkid = homeworkid;
            question.useranswer = value.useranswer;
            //if(exist_flag){
                homework_questions.questions.push(question);
            //}
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderTextAlound(question))[0]);
        })
        
        var wordtest = data.wordtest;
        var wordspell = [];
        var wordtranslate = [];
        var wordlisten = [];
        $.each(wordtest,function(key,value){
            if(first == 2 && key == 0){
                return true
            }
            var question = {};
            if(value.typeid == 0 ){
                question.tncontent = value.explains;
                var items = [];
                var item = {};
                item.flag = "A";
                item.content = value.option_a;
                item.value = "A";
                items.push(item);
                item = {};
                item.flag = "B";
                item.content = value.option_b;
                item.value = "B";
                items.push(item);
                item = {};
                item.flag = "C";
                item.content = value.option_c;
                item.value = "C";
                items.push(item);
                question.items = items;
                question.questionid = value.id;
                question.typeid = 0;
                question.answer = value.answer;
                question.mp3 = word_mp3_url + value.ukmp3;
                question.score = 1;
                question.wordid = value.wordid;
                question.datatype = 2;
                question.chapterid = value.chapterid?value.chapterid:0;
                question.homeworkid = homeworkid;
                question.useranswer = value.useranswer;
                wordtranslate.push(question);
                //if(exist_flag){
                    //homework_questions.questions.push(question);
                //}
                //fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(question))[0]);
            }else if(value.typeid == 1 || value.typeid == 3){
                question.tncontent = (value.typeid == 1?value.word:value.explains);
                var items = [];
                var item = {};
                item.flag = "A";
                item.content = value.option_a;
                item.value = "A";
                items.push(item);
                item = {};
                item.flag = "B";
                item.content = value.option_b;
                item.value = "B";
                items.push(item);
                item = {};
                item.flag = "C";
                item.content = value.option_c;
                item.value = "C";
                items.push(item);
                question.items = items;
                question.questionid = value.id;
                question.score = 1;
                question.typeid = value.typeid;
                question.answer = value.answer;
                question.mp3 = word_mp3_url + value.ukmp3;
                question.wordid = value.wordid;
                question.chapterid = 0;
                question.datatype = 2;
                question.homeworkid = homeworkid;
                question.useranswer = value.useranswer;
                wordlisten.push(question);
                //if(exist_flag){
                    //homework_questions.questions.push(question);
                //}
                //fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(question))[0]);
            }else if(value.typeid == 2){
                question.tncontent = value.explains;
                var options = value.option_a.split(",");
                var items = [];
                $.each(options,function(k,v){
                    var item = {};
                    item.flag = "";
                    item.content = v;
                    item.value = v;
                    items.push(item);
                })
                question.items = items;
                question.questionid = value.id;
                question.typeid = 2;
                question.wordid = value.wordid;
                question.chapterid = 0;
                question.answer = value.word.split("");
                question.mp3 = word_mp3_url + value.ukmp3;
                question.datatype = 2;
                question.score = 1;
                question.homeworkid = homeworkid;
                question.useranswer = value.useranswer;
                wordspell.push(question);
                //if(exist_flag){
                    //homework_questions.questions.push(question);
                //}
                //fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderSpell(question))[0]);
            }
        })
        //进行单词测试数据的汇总
        if(wordspell.length>0){
            $.each(wordspell,function(key,value){
                homework_questions.questions.push(value);
                fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderSpell(value))[0]);
            })
        }
        
        if(wordtranslate.length>0){
            $.each(wordtranslate,function(key,value){
               // console.log(value)
                homework_questions.questions.push(value);
                fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(value))[0]);
            })
        }
        if(wordlisten.length>0){
            $.each(wordlisten,function(key,value){
                homework_questions.questions.push(value);
                fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderChoose(value))[0]);
            })
        }
        
        var examquiz = data.exampaper;
        $.each(examquiz,function(key,value){
            if(first == 3 && key == 0){
                return true;
            }
            var question = {};
            question.content = value.content;
            question.tcontent = value.tcontent;
            question.items = JSON.parse(decodeURIComponent(value.questions_items));
            question.answer = JSON.parse(decodeURIComponent(value.questions_answer));
            question.questts = JSON.parse(decodeURIComponent(value.questions_tts));
            question.question_playtimes = value.question_playtimes;
            question.datatype = 3;
            question.examsid = value.examsid;
            question.quizid = value.quizid;
            question.questionid = value.quesid;
            question.score = value.question_score;
            question.homeworkid =  homeworkid;
            question.useranswer = value.useranswer;
            question.itemtype = value.itemtype;
            question.typeid = value.typeid;
            //if(exist_flag){
                homework_questions.questions.push(question);
            //}
            fragment.appendChild($("<div style='display:none'></div>").addClass("swiper-slide").html(renderExamPaper(question))[0]);
        })

        window.localStorage.setItem("enhomework",JSON.stringify(homework_questions));
        //一次性插入节点
        mySwiper.appendSlide(fragment);
        $(".swiper-slide").children().height($(document).height()-80);
        $(".swiper-slide").children().css("overflow","auto");
        $(".title.alound .img").height((window.innerWidth-10)*0.6);
        $(".title.alound.word .wordpic").height((window.innerWidth-10)*0.6);
        $(".title.alound .img").width((window.innerWidth-10)*0.6*4/3);
        replaceExamPaperImg($(".tgimg"));
        $(".tigan .tgimg").width(window.innerWidth-10);
        $(".text.alound").height(window.innerWidth*0.8);
        $(".swiper-slide").show();
        $(".swiper-slide").resize();
        mySwiper.update();
    },0) 
})

//获取试题的做题情况
var getQuestionSummary=function(){
    var fragment = document.createDocumentFragment();
    //直接从localstrage中获取数据
    var homework_questions = window.localStorage.getItem("enhomework");
    homework_questions = JSON.parse(homework_questions);
    var questions = homework_questions.questions;
    var type = -1;
    var typeid = 0;
    var content = "单词跟读";
    if(type == 1){
        content = "课文跟读"; 
    }else if(type == 2){
        content = "单词测试";
        var typeid = questions[0].typeid;
        if(typeid == 0){
            content = "听音选词";
        }else if(typeid == 1 || value.typeid == 3){
            content = "英汉互译";
        }else{
            content = "单词拼写";
        }
    }else if(type == 3){
        content = "听力试卷";
    }
    var parent = $("<div></div>");
    //var parent = $("<div></div>").append($("<span></span>").addClass("cata").html(content));
    var index = 0;
    $.each(questions,function(k,v){
        if(type == v.datatype && type != 2){
            index = index+1;
            if(v.useranswer!=undefined){
                if(v.useranswer.useranswer != "" && v.useranswer.useranswer != null){
                    parent.append($("<a></a>").addClass("quesnum active").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }else{
                    parent.append($("<a></a>").addClass("quesnum nonactive").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }
            }else{
                parent.append($("<a></a>").addClass("quesnum nonactive").append(
                    $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                ));
            }
        }else if(type == v.datatype && type == 2){
            index = index+1;
            if(typeid == v.typeid){
                if(v.useranswer!=undefined){
                    if(v.useranswer.useranswer != "" && v.useranswer.useranswer != null){
                        parent.append($("<a></a>").addClass("quesnum active").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }else{
                        parent.append($("<a></a>").addClass("quesnum nonactive").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }
                }else{
                    parent.append($("<a></a>").addClass("quesnum nonactive").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }
            }else if((typeid == 1 && v.typeid == 3)||(typeid == 3 && v.typeid == 1)){
                if(v.useranswer!=undefined){
                    if(v.useranswer.useranswer != "" && v.useranswer.useranswer != null){
                        parent.append($("<a></a>").addClass("quesnum active").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }else{
                        parent.append($("<a></a>").addClass("quesnum nonactive").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }
                }else{
                    parent.append($("<a></a>").addClass("quesnum nonactive").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }
            }else{
                index = 0;
                fragment.appendChild(parent[0]);
                type = v.datatype
                content = "单词跟读";
                if(type == 1){
                    content = "课文跟读";
                }else if(type == 2){
                    content = "单词测试";
                    typeid = v.typeid;
                    if(typeid == 0){
                        content = "听音选词";
                    }else if(typeid == 1||typeid == 3){
                        content = "英汉互译";
                    }else{
                        content = "单词拼写";
                    }
                }else if(type == 3){
                    content = "听力试卷";
                }
                parent = $("<div></div>").append($("<span></span>").addClass("cata").html(content));
                if(v.useranswer){
                    if(v.useranswer.useranswer != ""){
                        if(v.useranswer.useranswer != null && v.useranswer.useranswer != "" ){
                            parent.append($("<a></a>").addClass("quesnum active").append(
                                $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                            ));
                        }else{
                            parent.append($("<a></a>").addClass("quesnum nonactive").append(
                                $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                            ));
                        }
                        
                    }else{
                        parent.append($("<a></a>").addClass("quesnum nonactive").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }
                }else{
                    parent.append($("<a></a>").addClass("quesnum nonactive").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }
            }
        }else{
            index = 0;
            fragment.appendChild(parent[0]);
            type = v.datatype
            content = "单词跟读";
            if(type == 1){
                content = "课文跟读";
            }else if(type == 2){
                content = "单词测试";
                typeid = v.typeid;
                if(typeid == 0){
                    content = "听音选词";
                }else if(typeid == 1||typeid == 3){
                    content = "英汉互译";
                }else{
                    content = "单词拼写";
                }
            }else if(type == 3){
                content = "听力试卷";
            }
            parent = $("<div></div>").append($("<span></span>").addClass("cata").html(content));
            if(v.useranswer){
                if(v.useranswer.useranswer != ""){
                    if(v.useranswer.useranswer != null && v.useranswer.useranswer != "" ){
                        parent.append($("<a></a>").addClass("quesnum active").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }else{
                        parent.append($("<a></a>").addClass("quesnum nonactive").append(
                            $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                        ));
                    }
                    
                }else{
                    parent.append($("<a></a>").addClass("quesnum nonactive").append(
                        $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                    ));
                }
            }else{
                parent.append($("<a></a>").addClass("quesnum nonactive").append(
                    $("<span></span>").addClass("mui-icon").attr("index",k).html(index+1)
                ));
            }
        }
    })
    fragment.appendChild(parent[0]);
    return fragment;
}


//渲染试题开始
var renderSubmitAlound = function(data){
    var submitalount = $("<div></div>").addClass("mui-content-padded result hide").append(
        $("<h5></h5>").html("班级数据"),
        $("<ul></ul>").addClass("mui-table-view mui-grid-view content").append(
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("作答人数"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            ),
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("最高分"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            ),
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("平均分"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            )
        )
    );
    return submitalount;
}

var renderTTS = function(data){
    var tts = $("<div></div>").addClass("mui-content-padded").append(
        $("<h5></h5>").html("听力材料"),
        $("<ul></ul>").addClass("mui-table-view mui-grid-view").append(
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("使用时间"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            ),
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("最高分"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            ),
            $("<li></li>").addClass("mui-table-view-cell").append(
                $("<a></a>").append(
                    $("<span></span>").html("平均分"),
                    $("<div></div").addClass("mui-media-body").html("1秒")
                )
            )
        )
    );
    return tts;
}

var renderChoose = function(data){
   // console.log(data);
    //创建题干
    var parentdiv = $("<div></div>").addClass("xuanze");
    if(data.typeid == 0 || data.typeid == 2){
        parentdiv.append($("<div ></div>").addClass("tigan").append(
            $('<div class="lanren audio wordtest"><span class="sy-left"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+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" height="40px"></span><a class="edi-sy audio-btn" id="audio-btn" quesid="1692" type="0"><img src="../../public/Homework/images/sy.png" height="40px" class="sy-click" id="sy-click"></a><span class="sy-right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+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" height="40px"></span></div>')).append(
                $("<div></div>").addClass("content").html($("<font></font>"))
            )
        );
    }else{
        parentdiv.append($("<div style='line-height: 36px;'></div>").addClass("tigan").append(
                $("<div></div>").addClass("content").html($("<font style='font-size:25px;color:#8f8f94;'></font>").addClass("question-tips").html(data.tncontent))
            )
        );
    }
    
    var items = $("<ul></ul>").addClass("mui-table-view items");
    var answer = (data.useranswer != undefined?data.useranswer.useranswer:"");
   // console.log(answer);
    $.each(data.items,function(key,value){
        items.append(
            $("<li></li>").addClass("edi-cell mui-media item radio").attr("type",data.typeid).attr("wordid",data.wordid).attr("questionid",data.quesid).attr("chapterid",data.chapterid).attr("flag",value.flag).append(
                $("<div></div>").addClass("option").append(
                    $('<span></span>').addClass("option_circle").attr("quesid",data.quesid).attr("type",data.typeid).html(value.flag)
                )
            ).append(
                $("<span></span>").addClass("content").html(value.content)
            )
        );
    })
    parentdiv.append(items);
    parentdiv.append(renderSubmitAlound(data));
    return parentdiv;
}

var renderSpell = function(data){
    //创建题干
    var parentdiv = $("<div></div>").addClass("xuanze");
    var useranswer = $("<div></div>").addClass("useranswer");
    $.each(data.answer,function(k,v){
        // if(data.useranswer.useranswer != null && data.useranswer.useranswer != ""){
        //     var position = 0;
        //     $.each(data.items,function(key,value){
        //         if(value.content == data.useranswer.useranswer[k]){
        //             position = key;
        //         }
        //     })
        //     if(k == 0){
        //         useranswer.append($("<span class='answer focus' position='"+position+"'></span>").html(data.useranswer.useranswer[k]));
        //     }else{
        //         useranswer.append($("<span class='answer' position='"+position+"'></span>").html(data.useranswer.useranswer[k]));
        //     }
        // }else{
        //     if(k == 0){
        //         useranswer.append($("<span class='answer focus'></span>").html(""));
        //     }else{
        //         useranswer.append($("<span class='answer'></span>").html(""));
        //     }
        // }
        useranswer.append($("<span class='answer'></span>").html(""));
    })
    parentdiv.append($("<div></div>").addClass("tigan").append(
        $('<div class="lanren audio spell"><span class="sy-left"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+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" height="40px"></span><a class="edi-sy audio-btn" id="audio-btn" quesid="1692" type="0"><img src="../../public/Homework/images/sy.png" height="40px" class="sy-click" id="sy-click"></a><span class="sy-right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+VX2HOwPMMMwwoiju4q4ID/JglER90eASo0aNS0zURBMTY1TiBiYajfrig4maGF98wbjHREEUUFBUdBREVByWYWSblVnvMLf/sg59yq6ue05V/X379nbPSU667719/62+OvWdr05VG+ccqKnNill9BGoKWDU1BayamgJWbcZsYdh/vOSqy9NfGe+PZXA3v/VL+mTVpjbC4jG2eD/X+2n6SNWmHbAI1ld6/4D3Z1KkVVObLkoQ2TneL/P+Mu93ev+X9+44byKhJ4Y6YlfpiUZYzjZSlMXXsyZ8P4veL6DOs12bVwHLWRMlXM2E72eH93d7x7D6UlAVRAGbsWngrpu9P837470/Qfm0AjYXZaflfjZErqaAZSNriGSTrqRx0atW9ShgRZCYBLjzRnfU5rRRJw1YjbAK2GqgTANIrEZWBewsqANcx1GFYA6teqYrmk1CkHe8LzMRdSQgSWaubBy9CzNXJgKrUgKNsI99/hne30mvNlEKRg0S1FWf6/08qJOppjUBVJsQYBE0b/D+be9vpAgdgNGMGCR4bS/0/ik617ZKetIkCZjaeqQEEYjOoffbkuHajhgkeDysC7jY+ynv13p/qCLCWhjzRAZTfPP/56DFN5NPuhzXMGuUgG2CXo3t6cSbayKsm6BSgMU3WBe8RdWK6QFsCspGeD+qrB+G4KSToAN4fVgP/H7o1QdvUXhNnhKkYEinZdcqqrgWnxsAtx+uTXRda1kji+e40Ps7vN/k/e/eDynEJgtYxwy/a5mNu5agNTCow6Kf7f0l9Ped3g+uYQTeTDRmC2jxzVQA1hRAWwRCy8WL8XHdEGDHqPdi75+jnz/j/bewtisiVFabMkpgExANG2nxOGd6P8P7fu+PZhrfJsN8DtQm+V88x1nEr7eMAUhugjxaAVsRSVL+6DygBmbDBM6IQ/Vrifd9l4ZrxzR+rk4Bh99QqH1/8jmXdLBxRTyNrFOadBkh8UKgPsX7K7zf4P0uQT3A2as3Q28py93ebyWAc41vGSAYOs976efvQF76SiP0WuilJrlXQ513rRM+jnKNK9mcDsBGjTpw05E1yWsALh4TZ6a+7P3j3r+WGe7xf09mhtAQtbsCYM8nwONxrvH+oEBP0vqCRYrwx70fhdHJcSlF2kYjyDgSvvTZhHPj+7+M8dwTi7DcTS8xYEilLIxyYcUqvloCv02G7C4BuSsAOj0HN9QuEi1AwJ0Wnd8lHcskIwPqpW/yfpv360YoP5mEd+PU8pX0t097vxHGswQez/0CSjbx/j87hmRz4oDFm34RPXBHN30LE41cEl24YRjlnidTL98LvanWmJt2ifsagceCEAUdQ1FyETbc10Ug6KVSVVoYTpmEj6tfCOfD+95KP48j4Yuvayv0Jy82zwu3toWb3kxD59nRTRsGICAM6yHSXEjAxyqvxzHXcAw/60ER66dsAuUBs8BIYlaQ3aTrPJ0iM6oHG4TnwlWlxbaBePjz6NlARu6bZDI2VxJbzcxUOoNlmAaQQNLQ/z4fg5P3V3t/IhOdTiAAMMISaLnzbaTI+HoCimEALUlKVrg+qTG5qrTUthGt+CSNRJZRMybJGR3MoR5cC9iGiVQNE0kdoyQ4imabmM+4KFFbjM7Rgf5yl/AZLCr5ECVxl0aKgGFoQRpZpcaT5DKb8nDmMxidL6ZO9CwYXJ5jmAjnJgjcudGEF1reeC7CNsLw5yipamjoX2Ki2xl0LTjku0gei4fV0yiqbSSKklICkwDTFYZiUzlUG0EKC1Vki+Qu6cytgZpIUh06TjOkJNXMIyWoAayt5LsgJF1Blmoo2VpOer4jEJ5J17OXXjckyYxhIlcMEFchNQFz7tI9uYj3YofZTxSmET4PwOixLUBjiPKgXHev9ztIfpOSPmlauwNzOOtmK6KqSwDBUYI0wZA0Wis0UJjzR+nnbTTMppqmg/x6Lccka9KQaDJJGjeihOu7nF6twIO50su2e44h0F5O1AcpkLTlEo5K5xPVWg3lm6sIazJUwFZITDVcOLw+iRKdp3v/PPQmFFKpyjLHsAlYbabRnKCZliLsAoHo7d53e/9z0jm4ztwBWT8uPfNtBMTtAiDx2DiT+D7v3/J+jY+8XeF52/UEWJdk/Lkh0IA8b89FIAt8scqjpIs2zGe5SQBXkI5MRScqjUIG+iWDW5hOm15H2J922AL00v8gf34dOS4b+jWdM8hrx0sjSKaAaKqncBcqGosb8tNo1Qg8sqTVcuWD3Gdd0iFsRjoqDfHpiFCKQE0FZeLAlSaCTQYwlqFQUKA/JygfCLOPSB1eQ3+/Omm7nKUFRCdmPcJKD47TOl1heM1FQQMrBXcLK8sZcxGV20RDogsxxWijknDncgKVqim/xKh4AX3mTgJgk94LzQLG5z1GryGa4nTzW+j9fypUkvC3p3p/D73HAqJ/TnOSVsNhS5tT5KZmG4GvQmGYzkVuUzhnzerd1Wyt5ATpLn21FWA1BNaP0c9fhV7VWnq+TaQcOIqCHDfdFFGC0zPXmp4fc4dL6OdfQm/LfzfLEbYRuBoXBYHhcmlEK53PFgBdAnyXAVNO3nkMWGkZYOYecjULhlFXcskd/m4HRTm0c2CwYD28fw70vvQE+f03vO/KKCJWSpwTnTfWkxej9zOvw+Z4J2SGQ46PNoLqUDOdWaInEphc5h7C+3Td1xIzSnB0RZKPuDreJhraO9FxO9H/S/W8WK+A09pHiZ/uYu7ZMLyVU3bOpGh8EFbWH8+8DmuSoa0kfbkKsBtGqzUVKkUpsjVMogiM7MWpB2Hd1xXQrwvIUR/u+BIfTycQzqPs/iICjgN518VwXATWKehPqECGpjmQ6zwQrK/y/hHqBDO3/qwE2C6UdwN0BQkpbcA2m29w0Ted4YJKQOcsrPvaCivLAE2GEkgqQpMMz+GaMIJeCr1JgQ8SeDsJdQCGTmDmfjICbm7RZ64o6QyK1O8iHbcDZf185iKsKUSaUoSFjB7ZMA0kRXnIADLmjR3Iz2xBBuy5RKoUcbn7bBjAb6WkaDv0axAsw50NrJwxbDKduWEoV/q8j5K6ECYlzCyBtVYlkGSpTkUEk5IfgPzKBVfQQiX+azLRSuqIpQIVqciHoxq5kkcpYMSSm8tw8kZ4RjYT/bnnuMyA2cCMVHXVALYRIm0jNFjugXUEiaqTicI1CZ/J6LUuM2TX7BPGqSLAqCfpdTmQv7BEmjAxGV5smc5qKjpEfA6UvbZBf8KhgfqqtZmgBA5WLgORAF1DB6RhvwF5VsxURPu0A3FRzAjDpTQkOqYDcCDPjRyu8MxA0Gq5ANAIHVGqBbbMcc8i0CItOFxQT2ZW1ipV0RtBr0xlHem4RtB+ucYcZtgyAp+zmWSOAzxAvlZBin5cJZthwCslbxINKu26w/HeBejP7oV1dacoqTPQX8E805SgZshwFdpnTuJyNEw1DFeTIgdkZBlpQoFLWHIjCGSiMDb0cWrkJaZDGaGzhJUVixGAJD4ugRTtED2zQwKtMAwt4XjwHu9/oJ/3TDuPHWbFQS6yNAVOZzNgfhh6xdF7KJuVhl3HaK05vljDJ3PapVSx9oj3PxH/vpOJhl3m+gNQUUvdCPm1Zk6gZqFz/wJ6M1NXw2BlWK5CrKFO9ih1MvzsPd6/CYO758wsYLuQny7lEpLaSv4UUL/3/lHvDxB4gaEGUkWYq5C9JAkq5enhevbT3/cxHRHtgPcfQK+07z4mQlvm2eDzxP0dcI+Am6FXGnhupVSWSli4EmE3AZBLnroMcA/R+bGzhNkypAN3VygaMwHYDpSXv6RCvqkYWjnw4tKY6yLALMDKjSmMwNVMhovmKqYaWLkMx9Jw/0MC640wOIVpIkqAnevBSOlIlYpHCCSG3i9Tx7yffr9XoFMHKQLug5Wbl9jo/IeYxJJTCJqok32fOtm9MIPfB1FbwM31+JqpVShonU3h5zTScwU0lonEUtLI6bGWuU783F1Ro25oQZVi2Qt3lrmSfr6NfnfE++0CTw1U4nrvH6YIip3i2RWacdpGR0gJCO8DlfjvLAz9wwJWKlZJs+wG5Clal9E1a+exc6WNTYailP7fFoC+XJHM5TrhARo1DPDr4uLRI6YjCKifMB2tzU7k2EGuSDrLCnDP2gZxNZQAChm/LWiYpQx+xZfGJdX4TQWlsFBXpZ8T+msq9NmGjq43VxNcCgpptO9G93aMKAVQtOSeVSqVSZ1lpm2hMlq4imG8Zlue+PcnSQ1YAnlZRlobKmX5TSaidxja4goar7SXVm2UExNP5rgnAggjdSQ9Fu7/9UW61tuFY4cl9E7QYOd7u00mcklDu83IKGkDxj0dE4sbKHr8A8qbveXqWnPUA4Cf6nTAlwCWhv7SdOiAhFYASVgWcxX9vIvpLEGxuJaOuSyMdvtJojKUrM3lDuC1M12QUQtyc/aSshAklp95/w3JO7m9BmoSngbKtbXh82Gbz1PQF9Pb7EVlVvn3+JpQZfge/c+RDNCWC8fBpS1foeP8G+Sdz+casLEOazKJTWl6kANZl6LCgYqIVUo6YlnJCf9vkqTmAVICjkK7b3Jss1tMjS3TaLNaQ7DvhPysnVKCJHJZQWLJNWQNAGxBWbAF7mgTvvtH6O3i8hBpoVneydCIGjowiSF5rjZ+W03S1VQ2VFoxZWF1e6SWtNt0e6JTpD2eSBKYVE/GyH5jFFkPQ1/gPzwkPeGy/lWZfk9tO8A6avQwm3IY+OUUlks2Svy2ZWPUqAKOouVPKfnYmXS0tNPFnTEI/Pj3WzNDak0EM+ME43oD9UIhUtwK/ZkaqSG7kN8VBg01xOPEV9uUsEk7WUvyFnaqX3n/HZ2zBmAO6jTLUI6XS35CEndqnnnkNEfYtCE3Chw2lAZK65JuIuDfBeWvkJf4J2RkrbiTHYG+rrnASVuFCQqp8+6mDrE7k0w+QAnd3cBrqmpjkLWawtB3jICIX7qxh+GY6PeRdBOkpFpbIj8Off0xnXw4EUlVuUhdU/OQy+Svps72N+C/jQWPj3Wln4BetdnDCq/JALbE4bBhcE+mn0NvAkDaS2Cp5bkQACiq49aWt1DEsonEFYqPDcjFx0aSxVrwvyCFPQT5ac79NJrMfbY+dYBNGzPaMj2lBCcJWLuoMTeN6NoCh/4CDcU7YLBiHs+P1VQDxceZrycaBYC6ldetNiURFjINz1UDrbbxkIveAf3thFJ5qab4eC73+lfAtsvca+UbOwLphdsjts1XC3GrR0cqG6leOhsRdsVuzUNWNg1DFUzL69XIuo4B2yYKDwXYQidgO0sGrGEWC0CexVJbB4CtqaIa5Zz6MMcKidsVUJ7FUptDwLoKACEgworT/SMErIP22+qEyY/rYc4q7xWw7aOc1PA4MfAjAivWuo5q09ywihSVgYMtgadRdR1TAltIZhAcqMneA4PLqFcbXVFn/Tr0ir5xsqCrzaeAHSWHHfV25Dg9i5tA/LVmaFepSQGb47PjMqeRVQE7MtOopjaNgB3rFuPaCdS4BGrUHFZNbWoAu6pZLDW1cQO2UcCqzRJgrT42tVkC7Ex9r5Pa+gYs0oF99D7emVpNbSzWVtbCOoEfQ6+gZJR1AmpqawLYtagTUFOrNuOcJvtq8510qakpYNXUFLBqClg1NQWsmpoCVk0Bq6amgFVTU8CqKWDV1CZv/xNgAO0EPoIk8yYbAAAAAElFTkSuQmCC" height="40px"></span></div>')).append(
            $("<div></div>").addClass("content").html($("<font></font>").addClass("question-tips").html(data.tncontent)).append(useranswer)
        )
    );
    parentdiv.append($("<div></div>").addClass("clearfix"));
    //显示横线
    var items = $("<ul></ul>").addClass("mui-table-view items");
    $.each(data.items,function(key,value){
        if(value.content != ""){
                    items.append(
                        $("<li></li>").addClass("edi-cell mui-media item spell checkbox").append(
                            $("<div></div>").addClass("option").append(
                                $('<span></span>').addClass("option_circle").html(value.content)
                            )
                        )
                    );
        }
    })
    // //添加确定按钮
    // items.append(
    //     $("<li></li>").addClass("edi-cell mui-media item spell submit").attr("type",data.typeid).attr("wordid",data.wordid).attr("questionid",data.quesid).attr("chapterid",data.chapterid).append(
    //         $("<div></div>").addClass("option").append(
    //             $('<span></span>').addClass("option_circle").html('<i class="fa fa-check" aria-hidden="true"></i>')
    //         )
    //     )
    // );
    parentdiv.append(items);
    parentdiv.append(renderSubmitAlound(data));
    return parentdiv;
}

var renderWordAlound = function(data){
    //创建题干
    var parentdiv = $("<div></div>").addClass("alound");
    var userscore = (data.useranswer != undefined && data.useranswer.userscore != undefined)?data.useranswer.userscore:0;
    var ison = (data.useranswer == undefined?"":((data.useranswer.useranswer == undefined || data.useranswer.useranswer == "")?"":"on"));
    
    parentdiv.append(
        $("<div></div>").addClass("title alound").append(
            $("<div></div>").addClass("en center").append(
                data.word,
                //<a class="edi-sy audio-btn" id="audio-btn" quesid="1692" type="0"><img src="../../public/Homework/images/sy.png" height="40px" class="sy-click" id="sy-click"></a>
                $("<a></a>").addClass("edi-sy audio-btn").append(
                    $("<img></img>").addClass("sy-click").attr("src","../../public/Homework/images/sy.png")
                )
            ),
            $("<h5></h5>").addClass("yb center").html(data.ukmark),
            $("<h5></h5>").addClass("cn center").html(data.explains),
            $("<div></div>").addClass("wordpic").append(
                $("<img></img>").addClass("img").attr("src",data.pic)
            )
        ),
        // $("<div></div>").addClass("content alound").append(
        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan on voice").append(
        //             $("<i></i>").addClass("fa fa-volume-up fa-18")
        //         )
        //     ),
            
        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan uservoice").addClass(ison).append(
        //             $("<i></i>").addClass("fa fa-music fa-18")
        //         )
        //     ),

        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan on audio").append(
        //             $("<i></i>").addClass("fa fa-microphone")
        //         )
        //     ),

        //     $("<a></a>").addClass("fen4 rig-org").append(
        //         $("<div></div>").append(
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>0?"edi-gg":"")),
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>50?"edi-gg":"")),
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>80?"edi-gg":"")),
        //             $("<h1></h1").addClass("score").append(
        //                 $("<strong></strong>").html(userscore),
        //                 "分"
        //             )
        //         )
        //     )
        // ),
        renderSubmitAlound(data)
    )
    return parentdiv;
}

var renderTextAlound = function(data){
    //创建题干
    var parentdiv = $("<div></div>").addClass("alound");
    var userscore = (data.useranswer != undefined && data.useranswer.userscore != undefined)?data.useranswer.userscore:0;
    var ison = (data.useranswer == undefined?"":((data.useranswer.useranswer == undefined || data.useranswer.useranswer == "")?"":"on"));
    if(data.encontent.indexOf("\u00a0")){
        data.encontent =  data.encontent.replace(new RegExp("\u00a0","gm"),"\u0020");
    }
    
    parentdiv.append(
        $("<div></div>").addClass("title alound text").append(
            $("<div class='fen'></div>"),
            $("<div></div>").addClass("en center").append(
                data.encontent,
                $("<a></a>").addClass("edi-sy audio-btn").append(
                    $("<img></img>").addClass("sy-click").attr("src","../../public/Homework/images/sy.png")
                ),
                $("<h5></h5>").addClass("cn center").html(data.cncontent)
            ),
            $("<div class='fen'></div>")
        ),
        // $("<div></div>").addClass("content alound").append(
        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan on voice").append(
        //             $("<i></i>").addClass("fa fa-volume-up fa-18")
        //         )
        //     ),
            
        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan uservoice").addClass(ison).append(
        //             $("<i></i>").addClass("fa fa-music fa-18")
        //         )
        //     ),

        //     $("<a></a>").addClass("fen4").append(
        //         $("<div></div>").addClass("yuan on audio").append(
        //             $("<i></i>").addClass("fa fa-microphone")
        //         )
        //     ),
        //     $("<a></a>").addClass("fen4 rig-org").append(
        //         $("<div></div>").append(
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>0?"edi-gg":"")),
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>50?"edi-gg":"")),
        //             $("<i></i>").addClass("fa fa-star edi-gg").removeClass((userscore>80?"edi-gg":"")),
        //             $("<h1></h1").addClass("score").append(
        //                 $("<strong></strong>").html(userscore),
        //                 "分"
        //             )
        //         )
        //     )
        // ),
        renderSubmitAlound(data)
    )
    return parentdiv;
}

var renderExamPaper = function(data){
    //创建题干
   // console.log(data);
    var parentdiv = $("<div></div>").addClass("xuanze");
    parentdiv.append($("<div></div>").addClass("tigan exams").append(
        $('<div style="color:#8f8f94"></div>').html(data.content),
        $('<div class="lanren audio exams"><span class="sy-left"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+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" height="40px"></span><a class="edi-sy audio-btn" id="audio-btn" quesid="1692" type="0"><img src="../../public/Homework/images/sy.png" height="40px" class="sy-click" id="sy-click"></a><span class="sy-right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+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" height="40px"></span></div>'),
        $("<div></div>").addClass("tcontent").html(data.tcontent)
        ).append(
            $("<div></div>").addClass("content").html($("<font style='display:none'></font>").addClass("question-tips").html(data.tncontent))
        )
    );
    var items = $("<ul></ul>").addClass("mui-table-view items");
    var answer = data.useranswer != undefined?data.useranswer.useranswer:"";
    $.each(data.items,function(key,value){
        //判断文字和图片展示
        if(data.itemtype == 0 || data.typeid == 3){
            items.append(
                $("<li></li>").addClass("edi-cell mui-media item radio").attr("type",data.typeid).attr("wordid",data.wordid).attr("questionid",data.quesid).attr("chapterid",data.chapterid).attr("flag",value.value).append(
                    $("<div></div>").addClass("option").append(
                        $('<span></span>').addClass("option_circle").attr("quesid",data.quesid).attr("type",data.typeid).html(value.flag)
                    )
                ).append(
                    $("<span></span>").addClass("content").html(value.content)
                )
            );
        }else{
            items.append(
                $("<li></li>").addClass("edi-cell mui-media item radio").attr("type",data.typeid).attr("wordid",data.wordid).attr("questionid",data.quesid).attr("chapterid",data.chapterid).attr("flag",value.value).append(
                    $("<div></div>").addClass("option").append(
                        $('<span></span>').addClass("option_circle").attr("quesid",data.quesid).attr("type",data.typeid).html(value.flag)
                    )
                ).append(
                    $("<span></span>").addClass("content").html("<img width='120px' height='90px' src='"+resource+"/uploads/"+value.content+"'></img>")
                )
            );
        }
        
    })
    parentdiv.append(items);
    parentdiv.append(renderSubmitAlound(data));
    return parentdiv;
}

//事件响应开始
view.addEventListener('pageBeforeShow', function(e) {
    //console.log(e);
    isback = false;
    document.getElementById("accountcontent").innerHTML="";
    if(e.detail.page.id=="account"){
        document.getElementById("datika").style.display="";
        summaryhtml="";
        if(issubmit=='0'&&isOverdue=="false"){
            document.getElementById("timeshow").style.display="none";
            document.getElementsByTagName("nav")[0].style.display="";
        }else{
            document.getElementById("suwt").style.marginBottom="0px";
        }
        var summaryhtml=getQuestionSummary(homeworkid);
        $("#accountcontent")[0].appendChild(summaryhtml);
        $(".mui-view").css("height","94%");
        document.getElementById("accountcontent").style.marginTop="0px";
        if(issubmit == 1){
            $("nav.mui-bar.mui-bar-tab").hide();
            $(".homeworkanalysis").show();
        }else{
            $("nav.mui-bar.mui-bar-tab").hide();
            $(".homeworksubmit").show();
        }
        //直接定位到上次点击的位置
        var top = window.localStorage.getItem("scrollTop")?window.localStorage.getItem("scrollTop"):0;
        $("#accountcontent").offsetTop = parseInt(top)+44;
        // $("#accountcontent").offset({
        //     top:parseInt(top)+44,
        // });
    }else{
        document.getElementById("accountcontent").innerHTML="";
        if(issubmit=='0'&&type=='0'){
            document.getElementsByTagName("nav")[0].style.display="none";
            if(issubmit=='0'&&isOverdue=="false"){
                document.getElementById("timeshow").style.display="";
            }
        }
        document.getElementById("datika").style.display="";
    }
});
//提交作业事件

//错题解析
document.getElementById("errquesid").addEventListener('tap',function(e){
    $("nav.mui-bar.mui-bar-tab").hide();
    $(".mui-view").css("height","100%");
    mode = true;
    var index = errorQuestion[0];
    mySwiper.slideTo(index, 0, false);//切换到第一个slide，速度为1秒
    viewApi.back();
})
//试题解析
document.getElementById("allquesid").addEventListener('tap',function(e){
    $("nav.mui-bar.mui-bar-tab").hide();
    $(".mui-view").css("height","100%");
    mode = false;
    mySwiper.slideTo(0, 0, false);//切换到第一个slide，速度为1秒
    viewApi.back();
})
//点击播放
$("body").on("click",".title.alound .en,.title.alound .img,.alound .yuan.on.voice",function(){
    var mp3 = homework_questions.questions[homework_question_index].mp3;
    //判断方法是否存在
    if(window.UXinJSInterfaceSpeech){
        window.UXinJSInterfaceSpeech.playAudio(mp3);
    }else{
        mp.play(mp3);
    }
})

//单词测试播放
$("body").on("click",".tigan .lanren.audio.wordtest,.tigan .lanren.audio.spell",function(){
    var mp3 = homework_questions.questions[homework_question_index].mp3;
   // console.log(mp3);
    $(this).find(".audio-btn img").attr("id","playing");
    $("#playing").attr("src","../../public/Homework/images/sy.gif");
    var options = {};
    options.id = "wordtest";
    options.callback = function(){
        try{
            clearTimeout(mp3_progress);
        }catch(e){
           // console.log("第一次")
        }
        $("#playing").attr("src","../../public/Homework/images/sy.png");
        $("#playing").attr("id","");      
    }
    mp = mp.change(mp,options);
    mp.play(mp3);
})

$("body").on("click",".lanren.audio.exams",function(){
    var question = homework_questions.questions[homework_question_index];
    $(this).find(".audio-btn img").attr("id","playing");
    $("#playing").attr("src","../../public/Homework/images/sy.gif");
    question_play(question.question_playtimes,question.questts,$(this));
})


function getmp3url(mp3name){
    //mp3name = mp3name.substr(0,mp3name.length-1);
    var mp3url = '';
    var quespeed = 1;
    //if(examstts_type == 1){           //系统生成
        if(quespeed == 0){
            mp3name = mp3name+'s';
        }
        else if(quespeed == 2){
            mp3name = mp3name+'q';
        }
    //}
    debugger
    if(mp3name != null && mp3name != undefined && mp3name != ""){
        mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
    }
   // mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
    return mp3url;
}


function stopaudio(){
    try{
        clearTimeout(mp3_progress);
        clearTimeout(mp3_progress_reap);
    }catch(e){
       // console.log(e);
    }
    try{
        mp.pause();
    }catch(e){
      //  console.log(e);
    }
    mp3_progress='';
    mp3_progress_reap='';
    mp.index = 0;
    mp.stemindex = 0;
    mp.queinitindex = 0;
    mp.questionindex = 0;
    mp.childstemindex = 0;
    mp.playtimes=0;
    mp.childinitstemindex = 0;
    mp.url = "";
    mp.repeat = 1; //默认播放次数
    mp.curpeat = 1;//当前播放到第几次
    mp.url = "";
    mp.id = "";
    mp.callback = "";
}


function question_play(playtimes,quettsdata,obj){
    
    var smallquetts = '';
    clearTimeout();
    //播放次数
    if(mp.playtimes<playtimes){
        if(mp.questionindex < quettsdata.length){
            smallquetts = quettsdata[mp.questionindex];
            playurl = getmp3url(smallquetts.tts_mp3);
            //回调函数
            var options = {};
            options.id = "question";
            options.callback = function(){
                try{
                    clearTimeout(mp3_progress);
                }catch(e){
                   // console.log("第一次")
                }   
                mp3_progress = setTimeout(function(){
                    mp.questionindex = mp.questionindex +1;
                    question_play(playtimes,quettsdata,obj);
                },parseInt(quettsdata[mp.questionindex].tts_stoptime)*1000);
            }
            mp = mp.change(mp,options);
            mp.play(playurl);
        }else{
            mp.index = 0;
            mp.stemindex = 0;
            mp.queinitindex = 0;
            mp.questionindex = 0;
            mp.childstemindex = 0;
            mp.childinitstemindex = 0;
            mp.url = "";
            mp.repeat = 1; //默认播放次数
            mp.curpeat = 1;//当前播放到第几次
            mp.url = "";
            try{
                $("#playing").attr("src","../../public/Homework/images/sy.png");
                //stopaudio();
            }catch(e){
                //stopaudio();
                
            }
            mp.playtimes=mp.playtimes+1;
            try{
                clearTimeout(mp3_progress_reap);
            }catch(e){
                //console.log("第一次")
            }
            mp3_progress_reap=setTimeout(function(){
                $("#playing").attr("src","../../public/Homework/images/sy.gif");
                question_play(playtimes,quettsdata,obj);
            },5000);
            //document.getElementById("audio-btn").style.backgroundImage="url(../../public/Homework/images/pause-to-play-faster.gif)";
        }

    }else{
        mp.index = 0;
        mp.stemindex = 0;
        mp.queinitindex = 0;
        mp.questionindex = 0;
        mp.childstemindex = 0;
        mp.childinitstemindex = 0;
        mp.url = "";
        mp.repeat = 1; //默认播放次数
        mp.curpeat = 1;//当前播放到第几次
        mp.url = "";
        mp.playtimes=0;
        try{
            $("#playing").attr("src","../../public/Homework/images/sy.png");
            $("#playing").attr("id","");
            //stopaudio();
        }catch(e){
            //stopaudio();
            
        }
    }

}





//显示提示信息
$("body").on("click", ".tigan .questips", function() {
    var ele = $(this).parents(".tigan").find(".question-tips");
    if(ele.css('display') == 'none'){
        ele.show();
    }else{
        ele.hide();
    }
})
//单选题点击事件


//试题的跳转
$("body").on("click",".quesnum",function(e){
    var top = $(e.target).parents("#accountcontent").position().top;
   // console.log(top)
    window.localStorage.setItem("scrollTop",top);    
    $("nav.mui-bar.mui-bar-tab").hide();
    $(".mui-view").css("height","100%");
    var index = parseInt($(this).find(".mui-icon").attr("index"));
    mode = false;
    mySwiper.slideTo(index, 0, false);//切换到第一个slide，速度为1秒
    viewApi.back();
})
    
function todatika(){
    var quesindex = $("#quesindex").text();
    var quesnumcount = $("#quesnumcount").text();
    if(parseInt(quesindex) == parseInt(quesnumcount)){
        // console.log("sssss");
        // $("#datika").click();
        viewApi.go("#account");
    }
    else{
        //学生进行数据的提交
        timer = setTimeout(function(){
            mySwiper.slideNext();
        },1000);
    }
}       
        
        
