define(['jquery','mobile','mui'],function($){  //注意模块的写法   
    var teacher_homework = {}; //推荐方式  
    var loadTip = function(tipMsg, refConId){  
        var tipMsg = tipMsg || "module is loaded finish.";  
        if(undefined === refConId || null === refConId || "" === refConId+""){  
            alert(tipMsg);  
        }else{  
            $('#' + (refConId+"")).html(tipMsg);  
        }  
    };
    var setHomwWorkName = function(name){  
        return name;
    };


    var setArrs = function(dom, select,arr){
        $(dom).find(select).each(function(){
            var temp={};
            temp.id=$(this).attr("bid");
            temp.quescount=$(this).attr("quescount");
            temp.typeid=$(this).attr("typeid");
            arr.push(temp);
        }) 
    };
    //设置作业
    var publish= function(dom,wordselect,textselect,examselect){
        //word text exams
        //word中有四种情况0表示单词跟读 1表示单词拼写 2表示听音选词 3表示英汉互译
        var words=[];
        //text中有四种情况0表示课文跟读
        var texts=[];
        //exams中有四种情况0表示试卷
        var exams=[];
        //单词的设置
        setArrs(dom,wordselect,words);
        //课文的设置
        setArrs(dom,textselect,texts);
        //试卷的设置
        setArrs(dom,examselect,exams);
        //汇总
        var hw={};
        hw.words=words;
        hw.texts=texts;
        hw.exams=exams;
        return hw;
    };
    //如有需要暴露(返回)本模块API(相关定义的变量和函数)给外部其它模块使用  
    teacher_homework.unitname = setHomwWorkName; 
    teacher_homework.publish=publish;
    return teacher_homework;  
});