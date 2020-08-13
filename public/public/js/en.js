//页面加载完成时，判断页面分辨率，自动加载不同样式。
$(document).ready(function(){
  var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
  if (clienW >= 1280) {
    $(".w980").each(function () {
                $(this).attr("class","w1200");
            }); 
  }else{
    $(".w1200").each(function () {
                $(this).attr("class","w980");
        
            });   
    }
  
  
});

//调整窗口宽度时，批量替换页面中的样式。
function resizeWidth(){
  var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
  if (clienW >= 1280) {
    $(".w980").each(function () {
                $(this).attr("class","w1200");
            }); 
  }else{
    $(".w1200").each(function () {
                $(this).attr("class","w980");
            });     
    } 
  }

$(window).resize(function(){
  setTimeout(resizeWidth,500);
})

//定义paper类其中参数全部是json数据
var en = {
  createNew: function(data){
  	  var en = {};
      en.ks_code=data;
      en.getWordlist=function(tpl,obj){
        $.getJSON("../../Pubinterface/index/getWordlist",{ks_code:en.ks_code,ran:Math.random},function(data){
            $(tpl).tmpl(data).appendTo(obj);
            //单词总数更新
            $("#wacount").html(data.length);
            $("#wtcount").html(data.length);
        });
      };
      en.getChapterlist=function(tpl,obj){
        $.getJSON("../../Pubinterface/index/getChapterList",{ks_code:en.ks_code,ran:Math.random},function(data){
        	$(tpl).tmpl(data).appendTo(obj);
          $("#tacount").html(data.length);
        })
      };
      en.getTextlist=function(tpl,obj){
        $.getJSON("../../Pubinterface/index/getWordlist",{ks_code:en.ks_code,ran:Math.random},function(data){
        	$(tpl).tmpl(data).appendTo(obj);
        })
      };
      en.getExamslist=function(tpl,obj){
        $.getJSON("../../Pubinterface/index/getExamsList",{ks_code:en.ks_code,ran:Math.random},function(data){
        	$(tpl).tmpl(data).appendTo(obj);
          $("#eqcount").html(data.length);
        });
      };
      en.getExaminfo=function(examstpl,obj){
        $.getJSON("../../Pubinterface/index/getWordlist",{ks_code:en.ks_code,ran:Math.random},function(){


        });
      };
      en.getPreWalist=function(ids,tpl,obj){  //单词跟读
        $.getJSON("../Pcfunction/preview_wordwa",{ids:ids,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPreWplist=function(ids,tpl,obj){//单词拼写
        $.getJSON("../Pcfunction/preview_wordwp",{ids:ids,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPreWylist=function(ids,tpl,obj){ //英汉互译
        $.getJSON("../Pcfunction/preview_wordwy",{ids:ids,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPreWxlist=function(ids,tpl,obj){ //听音选词
        $.getJSON("../Pcfunction/preview_wordwx",{ids:ids,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPreTalist=function(ids,tpl,obj){
        $.getJSON("../Pcfunction/preview_getTexts",{ids:ids,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPreExaminfo=function(ids,tpl,obj){
        $.getJSON("../Pcfunction/preview_exams",{ids:ids,ran:Math.random},function(data){
           examsdata = data;
           $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getPrecard=function(waids,wpids,wyids,wxids,taids,eqids,tpl,obj){
        $.getJSON("../Pcfunction/preview_card",{ids:ids,ran:Math.random},function(data){
           examsdata = data;
           $(tpl).tmpl(data).appendTo(obj);
        });
      };
       en.getStuWalist=function(homeworkid,type,action,index){  //单词跟读--做作业
        $.getJSON("../Pcfunction/stu_wordwa",{homeworkid:homeworkid,ran:Math.random},function(data){
          wordarr = data.words;
           $('#cardwa').empty();
           $('#tmplwacard').tmpl(data).appendTo($('#cardwa'));
           changeinfo(wordarr[index],type,action,index,wordarr.length);
          //$(tpl).tmpl({data:data,words:data.words[index],index:parseInt(index)+1}).appendTo(obj);
        });
      };
      en.getStuWplist=function(homeworkid,type,action,index){//单词拼写--做作业
        $.getJSON("../Pcfunction/stu_wordwp",{homeworkid:homeworkid,ran:Math.random},function(data){
          $('#cardwp').empty();
          $('#tmplwpcard').tmpl(data).appendTo($('#cardwp'));
          wordarr = data.words;
          changeinfo(wordarr[index],type,action,index,wordarr.length);
        });
      };
      en.getStuWylist=function(homeworkid,type,action,index){ //英汉互译--做作业
        $.getJSON("../Pcfunction/stu_wordwy",{homeworkid:homeworkid,ran:Math.random},function(data){
          wordarr = data.words;
          $('#cardwy').empty();
          $('#tmplwycard').tmpl(data).appendTo($('#cardwy'));
          changeinfo(wordarr[index],type,action,index,wordarr.length);
        });
      };
      en.getStuWxlist=function(homeworkid,type,action,index){ //听音选词--做作业
        $.getJSON("../Pcfunction/stu_wordwx",{homeworkid:homeworkid,ran:Math.random},function(data){
          wordarr = data.words;
          $('#cardwx').empty();
          $('#tmplwxcard').tmpl(data).appendTo($('#cardwx'));
          changeinfo(wordarr[index],type,action,index,wordarr.length);
        });
      };
      en.getStuTalist=function(homeworkid,type,action,index){  //课文跟读--做作业
        $.getJSON("../Pcfunction/getStuTexts",{homeworkid:homeworkid,ran:Math.random},function(data){
          chapterarr = data.chapters;
          $('#cardta').empty();
          $('#tmpltacard').tmpl(data).appendTo($('#cardta'));
          changeinfo(chapterarr[index],type,action,index,chapterarr.length);
         // $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getStuExaminfo=function(ids,tpl,obj){  //听力训练--做作业
        $.getJSON("../Pcfunction/getStuExaminfo",{ids:ids,ran:Math.random},function(data){
           examsdata = data;
           $(tpl).tmpl(data).appendTo(obj);
           $('#tmpleqcard').tmpl(data).appendTo($('#cardeq'));
        });
      };
      
      //
      //学生查看试题反馈-单词跟读
      en.getStuFeedbackWalist=function(homeworkid,username,classid,tpl,obj){
      
        $.getJSON("../Pcfunction/stu_wordwa",{homeworkid:homeworkid,ran:Math.random},function(data){
          wordarr = data.words;
           $('#cardwa').empty();
           $('#stutmplwacard').tmpl(data).appendTo($('#cardwa'));
           $(tpl).tmpl(data).appendTo(obj);
        });
      };
       //学生查看试题反馈-单词拼写
      en.getStuFeedbackWplist=function(homeworkid,username,classid,tpl,obj){
        $.getJSON("../Pcfunction/stu_wordwp",{homeworkid:homeworkid,ran:Math.random},function(data){
           $('#cardwp').empty();
           $('#stutmplwpcard').tmpl(data).appendTo($('#cardwp'));
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
       //学生查看试题反馈-英汉互译
      en.getStuFeedbackWylist=function(homeworkid,username,classid,tpl,obj){
        $.getJSON("../Pcfunction/stu_wordwy",{homeworkid:homeworkid,ran:Math.random},function(data){
           $('#cardwy').empty();
           $('#stutmplwycard').tmpl(data).appendTo($('#cardwy'));
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      //学生查看试题反馈-听音选词
      en.getStuFeedbackWxlist=function(homeworkid,username,classid,tpl,obj){
        $.getJSON("../Pcfunction/stu_wordwx",{homeworkid:homeworkid,ran:Math.random},function(data){
           $('#cardwx').empty();
           $('#stutmplwxcard').tmpl(data).appendTo($('#cardwx'));
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getStuFeedbackTalist=function(homeworkid,username,classid,tpl,obj){
       $.getJSON("../Pcfunction/getStuTexts",{homeworkid:homeworkid,ran:Math.random},function(data){
          $('#cardta').empty();
          $('#stutmpltacard').tmpl(data).appendTo($('#cardta'));
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getStuFeedbackExaminfo=function(homeworkid,username,classid,tpl,obj){
        $.getJSON("../Pcfunction//getSturesultExaminfo",{homeworkid:homeworkid,username:username,classid:classid,ran:Math.random},function(data){
          examsdata = data;
          $(tpl).tmpl(data).appendTo(obj);
          $('#stutmpleqcard').tmpl(data).appendTo($('#cardeq'));
        });
      };
      en.getDStuFeedbackWordread=function(id,homeworkid,classid,tpl,obj){
        $.getJSON("../../../Pubinterface/index/stuwafeedback",{homeworkid:homeworkid,id:id,classid:classid,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getDStuFeedbackWordtest=function(id,homeworkid,classid,tpl,obj){
        $.getJSON("../../../Pubinterface/index/stuwtfeedback",{homeworkid:homeworkid,id:id,classid:classid,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getDStuFeedbackTextread=function(id,homeworkid,classid,tpl,obj){
        $.getJSON("../../../Pubinterface/index/stutafeedback",{homeworkid:homeworkid,id:id,classid:classid,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      en.getDStuFeedbackExaminfo=function(id,homeworkid,classid,tpl,obj){
        $.getJSON("../../../Pubinterface/index/stueqfeedback",{homeworkid:homeworkid,id:id,classid:classid,ran:Math.random},function(data){
          $(tpl).tmpl(data).appendTo(obj);
        });
      };
      //进行选择题的判断quetpl表示问题的data表示json数据{quesid:id,questcontent:cotnent,tts:{},answer:{},item:{}}
      en.getQuesinfo=function(data,quetpl){
      	 var render = template.compile(examstpl);
		     var html = render(data);
		     return html;
      };

      //题干的数据展示
      en.getQuesinfo=function(data,quetpl){
      var render = template.compile(examstpl);
  		var html = render(data);
  		return html;
      };
      return en;
    }
};
function checkedAll(obj){
  var name=$(obj).attr("name");
  //console.log($(obj).next().next().html());
  var selectnum = $(obj).next().next();
   if($(obj).is(":checked")){
      $("input[name^=word"+name+"]").each(function(){
        var xuantitype = $(this).attr("xuantitype");
        if(!$(this).is(":checked")){
          $(this).attr("checked",true);
          $(selectnum).html(parseInt(selectnum.html())+1);
          if(xuantitype == "check-05"){
             $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())+parseInt($(this).attr('textnum')));
          }
          else{
             $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())+1);
          }
          
         
        }
      });
    }else{
      $("input[name^=word"+name+"]").each(function(){
        var xuantitype = $(this).attr("xuantitype");
        if($(this).is(":checked")){
          $(this).attr("checked",false);
          $(selectnum).html(parseInt(selectnum.html())-1);
          if(xuantitype == "check-05"){
             $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())-parseInt($(this).attr('textnum')));
          }
          else{
             $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())-1);
          }
         
        }
      });
    }
    totalselectnum();
}

function childcheck(obj){
  //console.log(obj);
   var name=$(obj).attr("selectype");
   var xuantitype = $(obj).attr("xuantitype");
   var selectobj = $('font.redFon[selectype='+name+']');
   if(!$(obj).is(":checked")){
      selectobj.html(parseInt(selectobj.html())-1);
      if(xuantitype == "check-05"){  //课文
        $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())-parseInt($(obj).attr('textnum')));
      }
      else if(xuantitype == "check-06"){   //试卷
          $('.xuanti'+xuantitype).html(0);
      }
      else{
            $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())-1);
      }
   }
   else{
     //console.log($(selectnum).html());
      selectobj.html(parseInt(selectobj.html())+1);
      if(xuantitype == "check-05"){  //课文
        $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())+parseInt($(obj).attr('textnum')));
      }
      else if(xuantitype == "check-06"){   //试卷
          $('.xuanti'+xuantitype).html($(obj).attr('quescount'));
      }
      else{
          $('.xuanti'+xuantitype).html(parseInt($('.xuanti'+xuantitype).html())+1);
      }   
   }
   totalselectnum();
}

function cancelAll(obj){
  $("input[name=wordcheck-06]").removeAttr('checked');
  $('.xuanticheck-06').html(0);
  totalselectnum();
}
function totalselectnum(){
  var tempnum = 0
  $('.xuanti font').each(function(){
    tempnum += parseInt($(this).html());
    $('.totalselectnum').html(tempnum);
  });
}
function queslen(){
  var num=parseInt($("#num").text());
  $("#num").html(num+1);
  return num+1;
}

function quesemp(){
  $("#num").html("0");
}


//提交试卷
function savePaper(homeworkid,issubmit,ilearurl){
  //var homeworkid="{$homeworkid}";
  //听读作业电脑上不能保存
  //1、保存单词测试的用户答案
  var ret=0;
  //当用户没有答题是哪一个中还有试题没有回答
  var title=0;
  var wtlen=$(".btn_green[name^=wt]").length;
  var wtarr=[];
  //听读作业测试
  if($(".wordtest").find(".testContent").length>0){
    $(".wordtest").find(".testContent").each(function(){
      var obj=$(this);
      //判断两种题型
      if($(this).find("div.shuju ul").length==0){
        if($(this).find(".btn_green[name^=wt]").length>0){
          $(this).find(".btn_green[name^=wt]").each(function(key,value){
             var obj={};
             obj.id=$(this).attr("quesid");
             obj.tag="a";
             obj.type="wt";
             obj.attr="name";
             obj.useranswer=$(this).text();
             wtarr.push(obj);
          });
          $(obj).attr("style","");
        }else{
          ret=1;
          title=1;
          $(obj).attr("style","border:1px solid #F00;");
        }
      }else{
        //拼写作业情况
        $(this).find("div.shuju ul").each(function(key,value){
            var answer="";
            var flag=0;
            var len=$(this).find("input").length;
            $(this).find("input").each(function(k,v){
              var text=$(this).val();
              if(text==""||text==undefined){
                text="*";
                flag=flag+1;
              }
              answer=answer+text;
            });
             var abc={};
             abc.id=$(this).attr("quesid");
             abc.tag="ul";
             abc.type="wt";
             abc.attr="name";
             abc.useranswer=answer;
             if(flag==len){
               ret=1;
               title=1;
               $(obj).attr("style","border:1px solid #F00;");
             }else{
                wtarr.push(abc);
                $(obj).attr("style","");
             }
        });
      }
    });
  }

  var eqxzarr=[];
  var eqpdarr=[];
  var eqtkarr=[];
  var eqpxarr=[];
  if($(".examsquiz").find(".testContent").length>0){

    //听力选择题的提交
    $(".examsquiz").find(".testContent").each(function(k,v){
      var obj=$(v);
      var eqxzlen=$(v).find(".btn_green[name^=eqxz]").length;
      if($(v).find(".btn_green[name^=eqxz]").length>0){
        if(eqxzlen>0){
          $(v).find(".btn_green[name^=eqxz]").each(function(key,value){
             var obj={};
             obj.id=$(value).attr("quesid");
             obj.examsid=$(value).attr("examsid");
             obj.tag="a";
             obj.type="eqxz";
             obj.attr="name";
             obj.useranswer=$(value).text();
             eqxzarr.push(obj);
          });
          $(obj).attr("style","");
        }else{
          ret=1;
          title=3;
          $(obj).attr("style","border:1px solid #F00;");
        }
      }

      //听力判断题的提交
      var eqpdlen=$(v).find(".btn_green[name^=eqpd]").length;
      if($(v).find(".btn_green[name^=eqpd]").length>0){
        if(eqpdlen>0){
          $(v).find(".btn_green[name^=eqpd]").each(function(key,value){
             var obj={};
             obj.id=$(value).attr("quesid");
             obj.examsid=$(value).attr("examsid");
             obj.tag="a";
             obj.type="eqpd";
             obj.attr="name";
             obj.useranswer=$(value).attr("value");
             eqpdarr.push(obj);
          });
          $(obj).attr("style","");
        }else{
          ret=1;
          title=3;
          $(obj).attr("style","border:1px solid #F00;");
        }
      }

      //听力填空题的提交
      var eqtklen=$(v).find("input[name^=eqtk]").length;
      if($(v).find("input[name^=eqtk]").length>0){
        if(eqtklen>0){
          var uansdata="";
          var obj={};
          $(v).find("input[name^=eqtk]").each(function(key,value){
             obj.id=$(value).attr("quesid");
             obj.examsid=$(value).attr("examsid");
             obj.tag="input";
             obj.type="eqtk";
             obj.attr="name";
             if(key ==0){
                uansdata=$(value).val();
             }
             else{
              uansdata=uansdata+","+$(value).val();
             }
             
          });
          obj.useranswer=uansdata;
          eqtkarr.push(obj);
          $(obj).attr("style","");
        }else{
          ret=1;
          title=3;
          $(obj).attr("style","border:1px solid #F00;");
        }

      }

      //听力排序题的提交
      var eqpxlen=$(v).find("input[name^=eqpx]").length;
      if($(v).find("input[name^=eqpx]").length>0){
        if(eqpxlen>0){
          var uansdata="";
          var obj={};
          $(this).find("input[name^=eqpx]").each(function(key,value){
             obj.id=$(value).attr("quesid");
             obj.examsid=$(value).attr("examsid");
             obj.tag="input";
             obj.type="eqpx";
             obj.attr="name";
             if(key ==0){
                uansdata=$(value).val();
             }
             else{
              uansdata=uansdata+","+$(value).val();
             }
          });
          obj.useranswer=uansdata;
          eqpxarr.push(obj);
          $(obj).attr("style","");
        }else{
          ret=1;
          title=3;
          $(obj).attr("style","border:1px solid #F00;");
        }
    }
    });
  }
  //提交作业修改
  artDialog(
      {
          top:'90%',
          content:'检查下是否有未做完的试题，点击【确定】按钮提交！',
          lock:true,
          style:'succeed noClose'
      },
      function(){
        $.post("../../Pubinterface/index/giveMark",{issubmit:issubmit,wtdata:encodeURI(JSON.stringify(wtarr)),eqpxarr:encodeURI(JSON.stringify(eqpxarr)),eqxzarr:encodeURI(JSON.stringify(eqxzarr)),eqpdarr:encodeURI(JSON.stringify(eqpdarr)),eqtkarr:encodeURI(JSON.stringify(eqtkarr)),homeworkid:homeworkid,ran:Math.random()},function(data){
          //进行错误的试题的标注
          // if(data.wa.length>0){
          //   $.each(data.wa,function(key,value){
          //     var obj=value.obj;
          //     $(obj).parents(".testContent").attr("style","border:1px solid #F00;");
          //   });
          // }
          // if(data.wt.length>0){
          //   $.each(data.wt,function(key,value){
          //     var obj=value.obj;
          //     if(value.iscorrect=="0"){
          //       $(obj).parents(".testContent").attr("style","border:1px solid #F00;");
          //     }
          //   });
          // }
          // if(data.ta.length>0){
          //   $.each(data.ta,function(key,value){
          //     var obj=value.obj;
          //     $(obj).parents(".testContent").attr("style","border:1px solid #F00;");
          //   });
          // }
          // if(data.eq.length>0){
          //   $.each(data.eq,function(key,value){
          //     var obj=value.obj;
          //     $(obj).parents(".testContent").attr("style","border:1px solid #F00;");
          //   });
          // }
          $(".wk_send").attr("disabled",true);
          var interfaceServiceURL=ilearurl;
          art.dialog({
              margin:5,
              width:450,
              height:200,
              title:'',
              content:$('#zy_ts').get(0),
              lock:true,
              time:2,
              opacity: 0.2,
              close:function(){
                  var url=interfaceServiceURL;
                  location.href=url;
              }
          });
        });
      },
      function(){
          return true;
      }
  );
}


//读单词声音
