require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax":"enajax"
    },
    shim: {
        'zepto':{
            deps: [],
            exports: '$'
        },
        'enajax': {
            deps: ['zepto'],
            exports: 'enajax'
        }
    },
    waitSeconds: 0
});

//文件主要是进行不同体型的展示
define(['zepto','enajax'],function($,enajax){
    
    var booklist = {}; //推荐方式     
    //初始化考试列表
    var initList=function(url,listurl,parentid,firstobj,secondobj,thirdobj,listobj,viewtype){
        $(firstobj).empty();
        $(secondobj).empty();
        $(thirdobj).empty();
        $(listobj).empty();
        if(Requests["first"]!=undefined){
            first=Requests["first"];
        }
        $.ajax({
            type:'GET',
            url:url,
            data:{parentid:parentid},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                $.each(data,function(key,value){
                    var attrarr=[];
                    var temp={};
                    temp.id="bid";
                    temp.val=value.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:;";
                    attrarr.push(temp);
                    temp={};
                    temp.id="class";
                    if(first==0||viewtype!=0){
                        if(key==0){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }else{
                        if(value.id==first){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }
                    
                    
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.text(value.title);
                    firstobj.append(a);
                    //dd.appendTo(listobj);
                    a.bind("click",function(){
                        $(firstobj).find(".on").removeClass("on");
                        $(this).addClass("on");
                        var parentid=$(firstobj).find(".on").attr("bid");
                        getSecondListData(url,listurl,parentid,secondobj,thirdobj,listobj,1);
                    });
                })
                var parentsid=$(firstobj).find(".on").attr("bid");
                getSecondListData(url,listurl,parentsid,secondobj,thirdobj,listobj,viewtype);
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    var getSecondListData=function(url,listurl,parentid,secondobj,thirdobj,listobj,viewtype){
        $(secondobj).empty();
        $(thirdobj).empty();
        $(listobj).empty();
        var second=0;
        if(Requests["second"]!=undefined){
            second=Requests["second"];
        }
        $.ajax({
            type:'GET',
            url:url,
            data:{parentid:parentid},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                $.each(data,function(key,value){
                    var attrarr=[];
                    var temp={};
                    temp.id="bid";
                    temp.val=value.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:;";
                    attrarr.push(temp);
                    temp={};
                    temp.id="class";
                    if(second==0||viewtype!=0){
                        if(key==0){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }else{
                        if(value.id==second){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.text(value.title);
                    secondobj.append(a);
                    //dd.appendTo(listobj);
                    a.bind("click",function(){
                        $(secondobj).find(".on").removeClass("on");
                        $(this).addClass("on");
                        var parentid=$(secondobj).find(".on").attr("bid");
                        getThridListData(url,listurl,parentid,thirdobj,listobj,1);
                    });
                })
                var parentsid=$(secondobj).find(".on").attr("bid");
                getThridListData(url,listurl,parentsid,thirdobj,listobj,viewtype);
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    var getThridListData=function(url,listurl,parentid,thirdobj,listobj,viewtype){
        $(thirdobj).empty();
        $(listobj).empty();
        var third=0;
        if(Requests["third"]!=undefined){
            third=Requests["third"];
        }
        $.ajax({
            type:'GET',
            url:url,
            data:{parentid:parentid},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                $.each(data,function(key,value){
                    var attrarr=[];
                    var temp={};
                    temp.id="bid";
                    temp.val=value.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:;";
                    attrarr.push(temp);
                    temp={};
                    temp.id="class";
                    if(third==0||viewtype!=0){
                        if(key==0){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }else{
                        if(value.id==third){
                            temp.val="paperattr on";
                        }else{
                            temp.val="paperattr";
                        }
                    }
                    attrarr.push(temp);
                    var a =initDom("<a></a>",attrarr);
                    a.text(value.title);
                    thirdobj.append(a);
                    //dd.appendTo(listobj);
                    a.bind("click",function(){
                        $(thirdobj).find(".on").removeClass("on");
                        $(this).addClass("on");
                        var parentid=$(thirdobj).find(".on").attr("bid");
                        getExamsListData(listurl,parentid,listobj);
                    });
                })
                var parentsid=$(thirdobj).find(".on").attr("bid");
                getExamsListData(listurl,parentsid,listobj);
            },
            error:function(xhr,type){
                hideloading();
            }
        })
    }

    var getExamsListData=function(url,parentid,listobj){
        Request = GetRequest();
        var peroid=Request["levelid"]
        $(listobj).empty();
        $.ajax({
            type:'GET',
            url:url,
            data:{parentid:parentid,ran:Math.random()},
            dataType:'json',
            async:false,
            timeout: 30000,
            context:$('body'),
            success: function(data){
                var first=$("#ztgrade").find(".on").attr("bid");
                var second=$("#ztyear").find(".on").attr("bid");
                var third=$("#zttheme").find(".on").attr("bid");
                //遮罩消失
                hideloading();
                var examlist="";
                if(data.data=='null'||data.data==null||data.data.length==0){
                    $(listobj).html("目前此分类下面没有资源");
                }else{
	                $.each(data.data,function(k,v){
	                    //基础模块的添加
	                    examlist=examlist+'<li>';
	                    examlist=examlist+'    <a href="index?id='+v.id+'&first='+first+'&second='+second+'&third='+third+'&backUrl='+Requests["backUrl"]+'">';
	                    examlist=examlist+'        <div class="boxS">';
	                    examlist=examlist+'            <div class="hbimg posR">';
	                    examlist=examlist+'                <img class="img100" src="'+v.pic+'" />';
	                    if(v.num!=null){
	                        examlist=examlist+'                <label class="hb-jb">已阅读</label>';
	                    }
	                    examlist=examlist+'                <span>'+v.bookname+'</span>';
	                    examlist=examlist+'            </div>';
	                    examlist=examlist+'            <div class="table">';
	                    examlist=examlist+'                <div class="hbwz">';
	                    examlist=examlist+'                    <h6>'+data.parents[1].name+'</h6>';
	                    examlist=examlist+'                    <h6>'+data.parents[2].name+'</h6>';
	                    examlist=examlist+'                </div>';
	                    examlist=examlist+'                <div class="w60"><a href="index?id='+v.id+'&first='+first+'&second='+second+'&third='+third+'&backUrl='+encodeURIComponent(Requests["backUrl"])+'" class="hbBtn">阅读</a></div>';
	                    examlist=examlist+'            </div>';
	                    examlist=examlist+'        </div>';
	                    examlist=examlist+'    </a>';
	                    examlist=examlist+'</li>';
	                });
	                $(listobj).html(examlist);
	              }
	              
	              $("#scroller").height($(document).height()-44+"px");
                // new IScroll("#wrapper",{
                //     momentum:true,
                //     click:true 
                // });
                // $("#wrapper").resize();
            },
            error:function(xhr,type){

            }
        })
    }

    booklist.initTopicList=initList;
    return booklist;
});