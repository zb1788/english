//定义paper类其中参数全部是json数据
var Exams = {
  createNew: function(data){
      var Exams = {};
      Exams.exam=data;
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
        var questionhtml="";
        if(question.typeid==1){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_select' question_num_id='"+question.question_num_id+"' itemtype='"+question.itemtype+"' id='"+question.id+"' question_note='"+question.tips+"' question_stoptime='"+question.stoptimes+"' question_note='"+question.tips+"' question_num='"+question.tts_content+"' question_num_voice='"+question.voiceid+"' question_stoptime='"+question.stoptimes+"' stemtype='"+question.display+"' tips='"+question.tips+"'";
          $.each(question.keys,function(ktk,ktv){
            questionhtml=questionhtml+" "+ktv.dictionary_code+"='"+ktv.dictionary_id+"' ";
          });
          questionhtml=questionhtml+"><label class='question_types_label'>选择题";
        }else if(question.typeid==2){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_input' question_num_id='"+question.question_num_id+"' itemtype='"+question.itemtype+"' id='"+question.id+"' question_note='"+question.tips+"' question_stoptime='"+question.stoptimes+"' question_note='"+question.tips+"'  question_num='"+question.tts_content+"' question_num_voice='"+question.voiceid+"' question_stoptime='"+question.stoptimes+"' stemtype='0' tips='"+question.tips+"'";
          $.each(question.keys,function(ktk,ktv){
            questionhtml=questionhtml+" "+ktv.dictionary_code+"='"+ktv.dictionary_id+"' ";
          });
          questionhtml=questionhtml+"><label class='question_types_label'>填空题";
        }else if(question.typeid==3){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_judge' question_num_id='"+question.question_num_id+"' itemtype='"+question.itemtype+"' id='"+question.id+"' question_note='"+question.tips+"' question_stoptime='"+question.stoptimes+"' question_note='"+question.tips+"'  question_num='"+question.tts_content+"' question_num_voice='"+question.voiceid+"' question_stoptime='"+question.stoptimes+"' stemtype='0' tips='"+question.tips+"'";
          $.each(question.keys,function(ktk,ktv){
            questionhtml=questionhtml+" "+ktv.dictionary_code+"='"+ktv.dictionary_id+"' ";
          });
          questionhtml=questionhtml+"><label class='question_types_label'>判断题";
        }else if(question.typeid==4){
          questionhtml=questionhtml+"<div class='question_item' data-field-type='qunstion_sort'  question_num_id='"+question.question_num_id+"' itemtype='"+question.itemtype+"' id='"+question.id+"' question_note='"+question.tips+"' question_stoptime='"+question.stoptimes+"' question_note='"+question.tips+"'  question_num='"+question.tts_content+"' question_num_voice='"+question.voiceid+"' question_stoptime='"+question.stoptimes+"' stemtype='"+question.display+"' tips='"+question.tips+"'";
          $.each(question.keys,function(ktk,ktv){
            questionhtml=questionhtml+" "+ktv.dictionary_code+"='"+ktv.dictionary_id+"' ";
          });
          questionhtml=questionhtml+"><label class='question_types_label'>排序题";
        }

        questionhtml=questionhtml+"<div class='controlbar clearfix'><a ctrltype='edit' paperid='"+question.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' paperid='"+question.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
        questionhtml=questionhtml+"<div class='content_tts'>";
        //独立小题题干的html代码
        $.each(question.tts,function(qtk,qtv){
           if(qtv.flag_content){
             questionhtml=questionhtml+"<p id="+qtv.id+" ttstype='"+qtv.tts_type+"' stoptime='"+qtv.tts_stoptime+"' voicetype='"+qtv.voiceid+"' stflag='"+qtv.st_flag+"'><strong>"+qtv.flag_content+"</strong>:<font mp3='"+qtv.ftp_mp3+"'>"+qtv.tts_content+"</font></p>";
           }else{
             questionhtml=questionhtml+"<p id="+qtv.id+" ttstype='"+qtv.tts_type+"' stoptime='"+qtv.tts_stoptime+"' voicetype='"+qtv.voiceid+"' stflag='"+qtv.st_flag+"'><font mp3='"+qtv.ftp_mp3+"'>"+qtv.tts_content+"</font></p>";
           }
        });
        questionhtml=questionhtml+"</div>";
        questionhtml=questionhtml+"<div class='content_view'>"+question.tcontent+"</div>";
        var answerflag="";
        var answerid="";
        if(question.typeid==1){
          if(question.display==0){
             questionhtml=questionhtml+"<div class='content_options'>";
          }else{
             questionhtml=questionhtml+"<div class='content_options content_options_v'>";
          }
          $.each(question.items,function(k,v){
            if(question.itemtype==0){
              if(question.answer.length>0){
                if(question.answer[0].answer==v.content){
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' checked='true' disabled='disabled' id='"+v.id+"'>"+v.flag+"."+v.content+"</p>";
                   answerflag=v.flag;
                   answerid=question.answer[0].id;
                }else{
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' disabled='disabled' id='"+v.id+"'>"+v.flag+"."+v.content+"</p>";
                }
              }else{
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' disabled='disabled' id='"+v.id+"'>"+v.flag+"."+v.content+"</p>";
              }
            }else{
              if(question.answer.length>0){
                if(question.answer[0].answer==v.content){
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' checked='true' disabled='disabled' id='"+v.id+"'>"+v.flag+".<img src='../../uploads/"+v.content+"'/></p>";
                   answerflag=v.flag;
                   answerid=question.answer[0].id;
                }else{
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' disabled='disabled' id='"+v.id+"'>"+v.flag+".<img src='../../uploads/"+v.content+"'/></p>";
                }
              }else{
                   questionhtml=questionhtml+"<p id='"+v.id+"'><input name='' type='radio' value='"+v.flag+"' disabled='disabled' id='"+v.id+"'>"+v.flag+".<img src='../../uploads/"+v.content+"'/></p>";
              }
            }
              
          });
          questionhtml=questionhtml+"</div>";
          questionhtml=questionhtml+"<div class='answer' id='"+answerid+"'>答案："+answerflag+"</div>";
        }else if(question.typeid==3){

          questionhtml=questionhtml+"<div class='content_options'>";
          if(question.answer[0].answer==1){
              var flag=Math.random();
              questionhtml=questionhtml+"<p><input name='junge"+flag+"' type='radio' value='1' checked='true' disabled='disabled'>True</p><p><input value='0' name='junge"+flag+"' type='radio' disabled='disabled'>False</p> ";
          }else{
              var flag1=Math.random();
              questionhtml=questionhtml+"<p><input name='junge"+flag1+"' type='radio' value='1' disabled='disabled'>True</p><p><input value='0' name='junge"+flag1+"' type='radio' checked='true'  disabled='disabled'>False</p> ";
          }
          questionhtml=questionhtml+"</div>";
          if(question.answer[0].answer=='1'){
            question.answer[0].answer="True";
          }else{
            question.answer[0].answer="False";
          }
          questionhtml=questionhtml+"<div class='answer' id='"+question.answer[0].id+"'>答案："+question.answer[0].answer+"</div>";

        }else if(question.typeid==2){
          questionhtml=questionhtml+"<div class='answer'>答案：";
          $.each(question.answer,function(ki,vi){
              questionhtml=questionhtml+"&nbsp;&nbsp;<span id='"+vi.id+"'>"+(ki+1)+"."+vi.answer+"</span>";
          });
          questionhtml=questionhtml+"</div>";
        }else if(question.typeid==4){
          questionhtml=questionhtml+"<div class='content_options'>"; 
          $.each(question.items,function(k,v){
            if(question.itemtype==0){
              questionhtml=questionhtml+"<p><strong id='"+v.id+"'>"+v.flag+"</strong>.<font>"+v.content+"</font></p>";
            }else{
              questionhtml=questionhtml+"<p><strong id='"+v.id+"'>"+v.flag+"</strong>.<img src='../../uploads/"+v.content+"' width='30px' height='30px'/></p>";
            }
            
          });
          questionhtml=questionhtml+"</div>";
          questionhtml=questionhtml+"<div class='answer'>答案：";
          $.each(question.answer,function(ki,vi){
              questionhtml=questionhtml+"&nbsp;&nbsp;<span id='"+vi.id+"'><font>"+vi.answer_num+"</font>.<font>"+vi.answer+"</font></span>";
          });
          questionhtml=questionhtml+"</div>";
        }
        return questionhtml+"</div>";
      };
      Exams.initPaper = function(){
        //进行试卷头的展示

        var exams=Exams.exam;
        //试卷名称
        $("#exam_name").val(exams.name);
        $("#btn_exam_save").attr("examsid",exams.id);
        //音频处理
        $("input[name='tts_type'][value='"+exams.tts_type+"']").click();
        //应用类型
        $("input[name='exam_type'][value='"+exams.exams_classid+"']").click();
        //进行单元的或者专题的匹配
        //进行学期的匹配
        if(exams.exams_classid=="1"||exams.exams_classid=="2"){
          $("#unit").val(exams.unitname);
          $("#unit").attr("unitid",exams.ks_code);
          $("#provice").attr("provinceid","0");
          $("#provice").attr("yearid","0");
          $("#provice").attr("levelid","0");
          $("#provice").attr("typeid","0");
          $("#gradeid").val(exams.r_grade);
          $("#gradeid").change();
          $("#versionid").val(exams.r_version);
          $("#versionid").change();
          $("#termid").val(exams.r_volume);
          $("#termid").change();
          $("#unitid").val(exams.ks_code);
        }else{
          $("#provice").attr("provinceid",exams.provinceid);
          $("#provice").attr("yearid",exams.yearid);
          $("#provice").attr("levelid",exams.levelid);
          $("#provice").attr("typeid",exams.typeid);
          $("#proid").val(exams.provinceid);
          $("#yearid").val(exams.yearid);
          $("#levelid").val(exams.levelid);
          $("#typeid").val(exams.typeid);
          $("#unitid").attr("unitid","0");
          $("#typeid").change();
        }
        //显示内容的展示
        $("#header_content").val(exams.header_content);
        //测试提示音展示停顿时间
        $("#examsstoptimes").val(exams.stopsecond);
        if(exams.tts!=''){ 
          $("#ttsexamsbegin").val(exams.tts[0].tts_content);
          $("#ttsexamsbegin").parent().find("img").attr("id",exams.tts[0].id);
          $("#ttsexamsbeginvoice").val(exams.tts[0].voiceid);
          $("#ttsexamsbeginvoicemp3").val(exams.tts[0].mp3);
          
          //播放完成音频展示
          $("#ttsexamsend").val(exams.tts[1].tts_content);
          $("#ttsexamsend").parent().find("img").attr("id",exams.tts[1].id);
          $("#ttsexamsendvoice").val(exams.tts[1].voiceid);
          $("#ttsexamsendvoicemp3").val(exams.tts[1].mp3);
        }
        var examshtml="";
        $.each(this.papers,function(pk,pv){
          var paperhtml="";
          if(pv.stem_type==1){
          //独立小题的html代码
          paperhtml="<div class='stem_parent stem_single' data-field-type='stem_single' id='"+pv.id+"' points='"+pv.question_score+"' playtimes='"+pv.question_playtimes+"' stoptime='"+pv.stoptimes+"' tips='"+pv.tips+"'><label class='question_types_label'>大题";
          }else{
          paperhtml="<div class='stem_parent stem_combination' data-field-type='stem_combination' id='"+pv.id+"' stoptime='"+pv.stoptimes+"'><label class='question_types_label'>组合大题";
          }
          paperhtml=paperhtml+"<div class='controlbar clearfix'><a ctrltype='edit' id='"+pv.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' id='"+pv.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
          paperhtml=paperhtml+"<div class='content_tts'>";
          //题干的音频
          $.each(pv.tts,function(key,value){
            if($.trim(value.flag_content)!=""){
              paperhtml=paperhtml+"<p id="+value.id+" ttstype='"+value.tts_type+"' stoptime='"+value.tts_stoptime+"' voicetype='"+value.voiceid+"' stflag='"+value.st_flag+"'><strong>"+value.flag_content+"</strong>:<font mp3='"+value.ftp_mp3+"'>"+value.tts_content+"</font></p>";
            
            }else{
              paperhtml=paperhtml+"<p id="+value.id+" ttstype='"+value.tts_type+"'stoptime='"+value.tts_stoptime+"' voicetype='"+value.voiceid+"' stflag='"+value.st_flag+"'><font mp3='"+value.ftp_mp3+"'>"+value.tts_content+"</font></p>";
            }
              
          });
          paperhtml=paperhtml+"</div>";
          paperhtml=paperhtml+"<div class='content_view'>"+pv.content+"</div>";
          if(pv.son.length>0){
            $.each(pv.son,function(sk,sv){
              paperhtml=paperhtml+"<div class='stem_son_list clearfix ui-sortable'>";
              paperhtml=paperhtml+"<div class='stem_son emputyfiled'></div>";
              paperhtml=paperhtml+"<div class='stem_son' data-field-type='stem_son' points='"+sv.question_score+"' playtimes='"+sv.question_playtimes+"' stoptime='"+sv.stoptimes+"' tips='"+sv.tips+"' id='"+sv.id+"'>";
              paperhtml=paperhtml+"<label class='question_types_label'>组合大题-部分<div class='controlbar clearfix'><a ctrltype='edit' id='"+sv.id+"' class='edit' title='编辑'></a><a ctrltype='copy' class='copy' title='复制'></a><a ctrltype='delete' id='"+sv.id+"' class='delete' title='删除'></a><a ctrltype='collapse' class='collapse_n' title='折叠'></a></div></label>";
              paperhtml=paperhtml+"<div class='content_tts'>";
              $.each(sv.tts,function(stk,stv){
                if($.trim(stv.flag_content)!=""){
                   paperhtml=paperhtml+"<p id="+stv.id+" ttstype='"+stv.tts_type+"' stoptime='"+stv.tts_stoptime+"' voicetype='"+stv.voiceid+"' stflag='"+stv.st_flag+"'><strong>"+stv.flag_content+"</strong>:<font mp3='"+stv.ftp_mp3+"'>"+stv.tts_content+"</font></p>";
                 }else{
                   paperhtml=paperhtml+"<p id="+stv.id+" ttstype='"+stv.tts_type+"' stoptime='"+stv.tts_stoptime+"' voicetype='"+stv.voiceid+"' stflag='"+stv.st_flag+"'><font mp3='"+stv.ftp_mp3+"'>"+stv.tts_content+"</font></p>";
                 }
              });
              paperhtml=paperhtml+"</div>";
              paperhtml=paperhtml+"<div class='content_view'>"+sv.content+"</div><div class='question_items_list ui-sortable'>";

              $.each(sv.questionlist,function(qk,qv){
                 paperhtml=paperhtml+Exams.initQuestion(qv);
              });
              paperhtml=paperhtml+"</div>";
              paperhtml=paperhtml+"</div></div>";
            });
          }else{
            paperhtml=paperhtml+"<div class='question_items_list ui-sortable'>";
            $.each(pv.questionlist,function(qk,qv){
                 paperhtml=paperhtml+Exams.initQuestion(qv);
              });
            paperhtml=paperhtml+"</div>";
            paperhtml=paperhtml+"</div>";
          }
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









