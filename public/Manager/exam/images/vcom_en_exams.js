//定义paper类其中参数全部是json数据
var Exams = {
  createNew: function(data){
      var Exams = {};
      Exams.papers = data.paper;  
      Exams.tts = data.tts;
      Exams.types=data.types;
      Exams.stem=data.stem;
      Exams.questionlist=data.questionlist;
      Exams.son=data.son;
      Exams.edit=$("a[class='edit']");
      Exams.del=$("a[class='delete']");
      Exams.copy=$("a[class='copy']");
      Exams.initQuestion=function(question){
        var questionhtml="<div class='question_items_list ui-sortable'>";
        if(question.typeid==1){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_select' itemtype='"+question.itemtype+"' ><label class='question_types_label'>选择题";
        }else if(question.typeid==2){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_input'><label class='question_types_label'>填空题";
        }else if(question.typeid==3){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_judge'><label class='question_types_label'>判断题";
        }else if(question.typeid==4){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_sort'><label class='question_types_label'>排序题";
        }
        questionhtml=questionhtml+"<div class='controlbar clearfix'><a ctrltype='edit' paperid='"+question.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' paperid='"+question.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
        questionhtml=questionhtml+"<div class='content_tts'>";
        //独立小题题干的html代码
        $.each(question.tts,function(qtk,qtv){
           if(qtv.flag_content){
             questionhtml=questionhtml+"<p id="+qtv.id+" ttstype='"+qtv.tts_type+"'><strong>"+qtv.flag_content+"</strong>:<font>"+qtv.tts_content+"</font></p>";
           }else{
             questionhtml=questionhtml+"<p id="+qtv.id+" ttstype='"+qtv.tts_type+"'><font>"+qtv.tts_content+"</font></p>";
           }
        });
        questionhtml=questionhtml+"</div>";
        questionhtml=questionhtml+"<div class='content_view'>"+question.tcontent+"</div>";
        var answerflag="";
        if(question.typeid==1){
          questionhtml=questionhtml+"<div class='content_options'>";
          $.each(question.items,function(k,v){
              if(question.answer[0].answer==v.content){
                 questionhtml=questionhtml+"<p><input name='' type='radio' value='"+v.flag+"' checked='true' disabled='disabled'>"+v.content+"</p>";
                 answerflag=v.flag;
              }else{
                 questionhtml=questionhtml+"<p><input name='' type='radio' value='"+v.flag+"' disabled='disabled'>"+v.content+"</p>";
              }
          });
          questionhtml=questionhtml+"</div>";
          questionhtml=questionhtml+"<div class='answer'>答案："+answerflag+"</div>";
          questionhtml=questionhtml+"</div>";
        }else if(question.typeid==2){
          questionhtml=questionhtml+"<div class='content_options'>";
          if(question.answer==1){
              questionhtml=questionhtml+"<p><input name='junge1' type='radio' checked='true' disabled='disabled'>True</p><p><input name='junge1' type='radio' checked='checked' disabled='disabled'>False</p> ";
          }else{
              questionhtml=questionhtml+"<p><input name='junge1' type='radio' disabled='disabled'>True</p><p><input name='junge1' type='radio' checked='true' checked='checked' disabled='disabled'>False</p> ";
          }
          questionhtml=questionhtml+"</div>";
          questionhtml=questionhtml+"<div class='answer'>答案："+question.answer+"</div>";
          questionhtml=questionhtml+"</div>";
        }else if(question.typeid==3){
          questionhtml=questionhtml+"<div class='answer'>答案："+question.answer+"</div>";
        }else if(question.typeid==4){

        }
        return questionhtml;
      };
      Exams.initPaper = function(){
        var examshtml="";
        $.each(this.papers,function(pk,pv){
          var paperhtml="";
          if(pv.stem_type==1){
          //独立小题的html代码
          paperhtml="<div class='stem_parent stem_single' data-field-type='stem_single' points='"+pv.qusetion_score+"' playtimes='"+pv.qusetion_playtimes+"' stoptime='"+pv.stoptimes+"' tips='"+pv.tips+"'><label class='question_types_label'>大题";
          }else{
          paperhtml="<div class='stem_parent stem_combination' data-field-type='stem_combination' ><label class='question_types_label'>组合大题";
          }
          paperhtml=paperhtml+"<div class='controlbar clearfix'><a ctrltype='edit' id='"+pv.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' id='"+pv.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
          paperhtml=paperhtml+"<div class='content_tts'>";
          //题干的音频
          $.each(pv.tts,function(key,value){

            if($.trim(value.flag_content)!=""){
              paperhtml=paperhtml+"<p id="+value.id+" ttstype='"+value.tts_type+"'><strong>"+value.flag_content+"</strong>:<font>"+value.tts_content+"</font></p>";
            
            }else{
              paperhtml=paperhtml+"<p id="+value.id+" ttstype='"+value.tts_type+"'><font>"+value.tts_content+"</font></p>";
            }
              
          });
          paperhtml=paperhtml+"</div>";
          paperhtml=paperhtml+"<div class='content_view'>"+pv.content+"</div>";
          if(pv.son.length>0){
            $.each(pv.son,function(sk,sv){
              paperhtml=paperhtml+"<div class='stem_son_list clearfix ui-sortable'>";
              paperhtml=paperhtml+"<div class='stem_son emputyfiled'></div>";
              paperhtml=paperhtml+"<div class='stem_son' data-field-type='stem_son' points='"+pv.qusetion_score+"' playtimes='"+pv.qusetion_playtimes+"' stoptime='"+pv.stoptimes+"' tips='"+pv.tips+"'>";
              paperhtml=paperhtml+"<label class='question_types_label'>组合大题-部分<div class='controlbar clearfix'><a ctrltype='edit' id='"+sv.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' id='"+sv.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
              paperhtml=paperhtml+"<div class='content_tts'>";
              $.each(sv.tts,function(stk,stv){
                if($.trim(stv.flag_content!="")){
                   paperhtml=paperhtml+"<p id="+stv.id+" ttstype='"+stv.tts_type+"'><strong>"+stv.flag_content+"</strong>:<font>"+stv.tts_content+"</font></p>";
                 }else{
                   paperhtml=paperhtml+"<p id="+stv.id+" ttstype='"+stv.tts_type+"'><font>"+stv.tts_content+"</font></p>";
                 }
              });
              paperhtml=paperhtml+"</div>";
              paperhtml=paperhtml+"<div class='content_view'>"+sv.content+"</div>";
              $.each(sv.questionlist,function(qk,qv){
                 paperhtml=paperhtml+Exams.initQuestion(qv);
              });
            });
          }else{
            $.each(pv.questionlist,function(qk,qv){
                 paperhtml=paperhtml+Exams.initQuestion(qv);
              });
          }
          if(pv.son.length>0){
            paperhtml=paperhtml+"</div></div>";
          }
            paperhtml=paperhtml+"</div>";
            examshtml=examshtml+paperhtml;  
          });
        return examshtml;  
      };
      Exams.delPaper=function(){
        $(Exams.del).on("click",function(){
            var id=$(this).attr("id");
            art.dialog.promt("确定要删除此大题吗?",function(){
              $.getJSON("delExams",{id:id,type:"1",ran:Math.random()},function(){});
            });
        });
      };
      Exams.delQuestion=function(){
        $(Exams.del).on("click",function(){
            var id=$(this).attr("id");
            art.dialog.promt("确定要删除此大题吗?",function(){
              $.getJSON("delExams",{id:id,type:"3",ran:Math.random()},function(){});
            });
        });
      };
      Exams.delCombine=function(){
        $(Exams.del).on("click",function(){
            var id=$(this).attr("id");
            art.dialog.promt("确定要删除此大题吗?",function(){
              $.getJSON("delExams",{id:id,type:"2",ran:Math.random()},function(){});
            });
        });
      };
      return Exams;
    }
};









