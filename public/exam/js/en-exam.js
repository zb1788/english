var content="显示内容: <br /><script id='editor6' type='text/plain' style='width:265px;height:250px;'></script>";
var issys=$('#form_header input[name="tts_type"]:checked').val();
//controllerToQuestion编辑窗口到试题页面
var option=['A','B','C','D','E','F','G'];
function controllerToQuestion(obj){
	var type=$(obj).attr("questiontype");
    if(type!='9'){
        var html = "";
        var arr=[];
        //进行json数据的组合
        var question=$(".contentedit");
        var questions={};
        //1、题号以及题号的音频
        questions.question_num=$(question).find("li:eq(0)").find("input").val();
        questions.question_num_voice=$(question).find("li:eq(0)").find("select").val();
        //2、小题分值
        //questions.question_points=$(question).find("li:eq(1)").find("input").val();
        //3、听力材料
        var ttsobject=$(question).find("li:eq(1)").find(".tt");
        //alert($(ttsobject).html());
        var ttss=[];
        $(ttsobject).each(function(key,val){
            var tts={};
            if(issys==1){
                tts.id=$(val).attr("id");
                tts.ttsnote=$(val).find("input").val();
                tts.tcontent=$(val).find("textarea").val();
                tts.ttsvoice=$(val).find("select:eq(0)").val();
                tts.ttstime=$(val).find("select:eq(1)").val();
                tts.mp3="";
            }else{
                tts.id=$(val).attr("id");
                tts.ttsnote=$(val).find("input").val();
                tts.tcontent=$(val).find("textarea").val();
                tts.ttsvoice=$(val).find("select:eq(0)").val();
                tts.ttstime=$(val).find("select:eq(1)").val();
                tts.mp3=$(val).find("img").attr("mp3");
            }
            ttss.push(tts);
        });
        questions.ttss=ttss;
        //4、问题提示音
        questions.question_note=$(question).find("li:eq(2)").find("select").val();
        //5、问题停顿时间
        questions.question_stoptime=$(question).find("li:eq(3)").find("select").val();
        
        //6、问题题干ue.getContent();
        questions.question_stem=ue.getContent();
        var tcontent=ue.getContent();
        
        var cur=$(".curedit").parent("div");
        $(cur).attr("question_num",questions.question_num);
        $(cur).attr("question_num_voice",questions.question_num_voice);
        $(cur).attr("question_note",questions.question_note);
        $(cur).attr("question_stoptime",questions.question_stoptime);
        var ttshtmls="";
        $.each(questions.ttss,function(key,val){
            if(val.ttsnote!=""){
                ttshtmls=ttshtmls+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"'><strong>"+val.ttsnote+"</strong>:";
            }else{
                ttshtmls=ttshtmls+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"'><strong></strong>"; 
            }
            ttshtmls=ttshtmls+"<font>"+val.tcontent+"</font></p>";
        });
        $(cur).find(".content_tts:eq(0)").html(ttshtmls);
        $(cur).find(".content_view:eq(0)").html(questions.question_stem);
        switch (type) {
        case "1":
            //7、问题选项
            $(cur).attr("typeid","1");
            var question_items={};
            var question_item=$(question).find("li:eq(5)");
            question_items.question_item_type=$(question_item).find("input[name='type']:checked").val();
            $(cur).attr("question_itemtype",question_items.question_item_type);
            var items=[];
            $(question_item).find("p").each(function(key,val){
              var item={};
              item.itemtype=$(val).find("input[type='radio']").is(":checked")?1:0;
              item.itemoption=$(val).find("input[type='radio']").val();
              item.itemcontent=$(val).find("input[type='text']").val();
              items.push(item);
            });
            question_items.contents=items;
            //8、选项排列方式
            var itemlayout=$(question).find("li:eq(6)").find("input:checked").val();
            question_items.itemlayout=itemlayout;
            questions.question_items=question_items;
            arr.push(questions);
            //$(cur).find(".content_options").empty();

            var items="";
            $.each(questions.question_items.contents,function(key,val){
                var itemhtml="<p>";            
                if(val.itemtype){
                     itemhtml=itemhtml+"<input type='radio' checked='true' value='"+val.itemoption+"' disabled='disabled' type='radio'/>";
                     $(cur).find(".answer").html("答案："+val.itemoption);
                }else{
                     itemhtml=itemhtml+"<input type='radio' value='"+val.itemoption+"' disabled='disabled'  type='radio'/>";
                }
                itemhtml=itemhtml+val.itemoption+"."+val.itemcontent+"</p>";
                items=items+itemhtml;
            });
            $(cur).find(".content_options").attr("question_item_type",question_items.question_item_type);
            $(cur).find(".content_options").html(items);

            if(question_items.itemlayout==1){
                $(cur).find(".content_options").addClass("content_options_v");
            }else{
                $(cur).find(".content_options").removeClass("content_options_v");
            }
            break;
        case "2":
            //7、判断题答案
            $(cur).attr("typeid","2");
            questions.answer=$(question).find("li:eq(5)").find("input:checked").val();
            arr.push(questions);
            $(cur).find(".content_options").empty();
            if(questions.answer==1){
                $(cur).find(".content_options").html("<p><input name='junge1' checked='checked' type='radio' value='1' disabled='disabled'/>True</p><p><input name='junge1' value='0' type='radio'   disabled='disabled' />False</p>");
                $(cur).find(".answer").html("答案：True");
            }else{
                $(cur).find(".content_options").html("<p><input name='junge1'  type='radio'  value='1' disabled='disabled'/>True</p><p><input name='junge1' type='radio' value='0'  disabled='disabled' checked='checked'/>False</p>");
                $(cur).find(".answer").html("答案：False");
            }      
            break;
        case "3":
            //7、填空题答案
            $(cur).attr("typeid","3");
            var answer="";
            var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
            var result;
            var sortid=1;
            while ((result = patt.exec(tcontent)) != null)  {
               answer+=("<span>"+sortid+"."+result[1]+"</span>&nbsp;&nbsp;"); 
               sortid=sortid+1;
            }
            $(cur).find(".answer").html("答案:"+answer);
            break;      
        case "4":
            //7、排序题选项以及答案
            $(cur).attr("typeid","4");
            var question_items={};
            var question_item=$(question).find("li:eq(5)");
            question_items.question_item_type=$(question_item).find("input[name='type']:checked").val();
            var items=[];
            $(question_item).find("p").each(function(key,val){
              var item={};
              item.itemoption=$(val).find("input:eq(0)").val();
              item.itemcontent=$(val).find("input:eq(1)").val();
              items.push(item);
            });
            question_items.contents=items;
            //8、选项排列方式
            questions.question_items=question_items;
            
            //$(cur).find(".content_options").empty();
            var question_answer=$(question).find("li:eq(6)");
            var answer=[];
            $(question_answer).find("p").each(function(key,val){
              var item={};
              item.answercontent=$(val).find("input[type='text']").val();
              answer.push(item);
            });
            question_items.options=answer;
            //8、选项排列方式
            questions.question_answer=question_items;
            arr.push(questions);
            var items="";
            $.each(questions.question_items.contents,function(key,val){
                var itemhtml="<p>";           
                itemhtml=itemhtml+"<strong>"+val.itemoption+"</strong>.<font>"+val.itemcontent+"</font></p>";
                items=items+itemhtml;
            });
            var answer="";
             $.each(questions.question_items.options,function(key,val){
                answer=answer+"&nbsp;<span>"+val.answercontent+"</span>&nbsp;";
                
            });
            $(cur).find(".answer").html("答案:"+answer);
            $(cur).find(".content_options").attr("question_item_type",question_items.question_item_type);
            $(cur).find(".content_options").html(items);
            break;
        default:
            return;
        }
    }else{
        var html = "";
        var arr=[];
        //进行json数据的组合
        var question=$(".contentedit");
        var questions={};
        //2、小题分值
        questions.question_points=$(question).find("li:eq(0)").find("input").val();
        //3、听力材料
        var ttsobject=$(question).find(".tt");
        //alert($(ttsobject).html());
        var ttss=[];
        $(ttsobject).each(function(key,val){
            var tts={};
            if(issys==1){
              tts.id=$(val).attr("id");
              tts.ttsnote=$(val).find("input").val();
              tts.tcontent=$(val).find("textarea").val();
              tts.ttsvoice=$(val).find("select:eq(0)").val();
              tts.ttstime=$(val).find("select:eq(1)").val();
              tts.mp3="";
            }else{
              tts.id=$(val).attr("id");
              tts.ttsnote=$(val).find("input").val();
              tts.tcontent=$(val).find("textarea").val();
              tts.ttsvoice=$(val).find("select:eq(0)").val();
              tts.ttstime=$(val).find("select:eq(1)").val();
              tts.mp3=$(val).find("img").attr("mp3");
            }
            ttss.push(tts);
        });
        questions.ttss=ttss;
        //4、问题提示音
        questions.question_tips=$(question).find("li:eq(2)").find("select").val();
        
        //5、问题停顿时间
        questions.question_stoptime=$(question).find("li:eq(3)").find("select").val();
        //6、问题播放次数
        questions.question_playtimes=$(question).find("li:eq(4)").find("select").val();
        //7、问题题干
        questions.question_stem=ue.getContent();
        var cur=$(".curedit");
        //alert(cur.html());
        var ttshtml="";
        $.each(questions.ttss,function(key,val){
            if(val.ttsnote){
                ttshtml=ttshtml+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"'><strong>"+val.ttsnote+"</strong>:";
            }else{
                ttshtml=ttshtml+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"'><strong>"+val.ttsnote+"</strong>";
            }
             ttshtml=ttshtml+"<font>"+val.tcontent+"</font></p>";
        });
        $(cur).parent().find(".content_tts:eq(0)").html(ttshtml);
        $(cur).parent().attr("stoptime",questions.question_stoptime);
        $(cur).parent().attr("points",questions.question_points);
        $(cur).parent().attr("tips",questions.question_tips);
        $(cur).parent().attr("playtimes",questions.question_playtimes);
        $(cur).parent().find(".content_view:eq(0)").html(questions.question_stem);
    }
	
}


////questionToController页面到编辑窗口
function questionToController(type){
    var questions={};
    var cur=$(".curedit").parent("div");
    //取出题干
    questions.question_stem= $(cur).find(".content_view").html();
    //取出听力材料
    var ttss=[];
    $(cur).find('.content_tts:eq(0)').find("p").each(function(key,val){
        var tts_temp={};
        tts_temp.ques_tts_content=$(val).find("font").html();
        //alert(tts_temp.ques_tts_content);
        tts_temp.ques_tts_voicetype=$(val).attr("voicetype");
        tts_temp.ques_tts_stoptime=$(val).attr("stoptime");
        tts_temp.ques_tts_voicenote=$(val).find("strong").text();
        ttss.push(tts_temp);
    })
    questions.ttss=ttss;
    var ttsobject=$("ul.contentedit li.tts");
    $(ttsobject).empty();
    ttsobject.append("听力材料:<br>");
    if(questions.ttss.length==0){
        if ($('#form_header input[name="tts_type"]:checked').val() == "1"){
          var tts=$("#item_tts_auto_edit_template .tt").clone();
          ttsobject.append(tts);
        }else{
          var tts=$("#item_tts_user_edit_template .upload").clone();
          ttsobject.append(tts);
        }
    }

    $.each(questions.ttss,function(key,value){
        if($('#form_header input[name="tts_type"]:checked').val()){
            var tts=$("#item_tts_auto_edit_template .tt").clone();
            $(tts).find("input:eq(0)").val(value.ques_tts_voicenote);
            $(tts).find("textarea").val(value.ques_tts_content);
            $(tts).find("select:eq(0)").val(value.ques_tts_voicetype);
            $(tts).find("select:eq(1)").val(value.ques_tts_stoptime);
            ttsobject.append(tts);
        }else{
            var tts=$("#item_tts_user_edit_template .tt").clone();
            $(tts).find("input:eq(0)").val(value.ques_tts_voicenote);
            $(tts).find("textarea").val(value.ques_tts_content);
            $(tts).find("select:eq(0)").val(value.ques_tts_voicetype);
            $(tts).find("select:eq(1)").val(value.ques_tts_stoptime);
            ttsobject.append(tts);
        }
        
    });
    ue.reset();
        setTimeout(function(){
        ue.setContent(questions.question_stem);
    },200)
    var li=$("ul.contentedit li");
    switch (type) {
        case "1":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            $(li).eq(2).find("select:eq(0)").val(questions.question_note);
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
            $("ul.contentedit li.itmes").empty();
            var items=[];
            $(cur).find(".content_options p").each(function(key,value){
                var item={};
                item.answer=$(value).find("input").is(":checked")?1:0;
                item.content=$(value).text();
                items.push(item);
            });
            questions.items=items;
            var question_item_type=$(cur).find(".content_options").attr("question_item_type");
            var itemshtml="";
            if(question_item_type==0){
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio'>文字<input name='questype' class='intext' value='1' type='radio'>图片<br>";
            }else if(question_item_type==1){
                itemshtml="选项内容:<input name='questype' class='intext' value='0'  type='radio'>文字<input name='questype' class='intext' value='1' checked='checked' type='radio'>图片<br>";
            }else{
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio'>文字<input name='questype' class='intext' value='1' type='radio'>图片<br>";
            }
            $.each(questions.items,function(key,value){
                if(value.answer==1){
                   itemshtml=itemshtml+"<p><input type='radio' checked='checked' value='"+option[key]+"' name='items' />";
                }else{
                   itemshtml=itemshtml+"<p><input type='radio'  value='"+option[key]+"' name='items'/>";
                }
                itemshtml=itemshtml+"<input name='textfield' size='30' type='text' value='"+value.content+"''>&nbsp;<img src='/english_exam/public/Manager/exam/images/icon_add.png' onclick='item_add(this);' width='20'><img src='/english_exam/public/Manager//exam/images/icon_delete.png' onclick='item_del(this);'></p>";
            });
            $("ul.contentedit li.itmes").html(itemshtml);

        break;
        case "2":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            $(li).eq(2).find("select:eq(0)").val(questions.question_note);
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
            $("ul.contentedit li.itmes").empty();
            var answer=$(cur).find(".content_options input:checked").val();
            questions.answer=answer;
            var itemshtml=" 选项内容:";
            if(questions.answer==1){
               itemshtml=itemshtml+"&nbsp;<input name='judge'  class='intext' type='radio' value='1'  checked='True'/>True&nbsp;<input name='judge'  class='intext' type='radio' value='0' />False";
            }else{
               itemshtml=itemshtml+"&nbsp;<input name='judge'  class='intext' type='radio' value='1' />True&nbsp;<input name='judge'  class='intext' type='radio' value='0' checked='True'/>False";
            }
            $("ul.contentedit li.itmes").html(itemshtml);
            break;
        case "3":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            $(li).eq(2).find("select:eq(0)").val(questions.question_note);
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);


            break;
        case "4":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            $(li).eq(2).find("select:eq(0)").val(questions.question_note);
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
            var items=[];
            $(cur).find(".content_options p").each(function(key,value){
                var item={};
                item.answer=$(value).find("strong").text();
                item.content=$(value).find("font").text();
                items.push(item);
            });
            questions.items=items;
            var answers=[];
            $(cur).find(".answer span").each(function(key,value){
                var item={};
                item.answer=$(value).text();
                answers.push(item);
            });
            questions.answers=answers;
            $("ul.contentedit li.itmes").empty();
            var itemshtml="";
            if(question_item_type==0){
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio'>文字<input name='questype' class='intext' value='1' type='radio'>图片<br>";
            }else if(question_item_type==1){
                itemshtml="选项内容:<input name='questype' class='intext' value='0'  type='radio'>文字<input name='questype' class='intext' value='1' checked='checked' type='radio'>图片<br>";
            }else{
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio'>文字<input name='questype' class='intext' value='1' type='radio'>图片<br>";
            }
            $.each(questions.items,function(key,value){
                   itemshtml=itemshtml+"<p><input type='text' size='1' style='width:20px;' value='"+value.answer+"' name='items' /><input name='textfield' size='20' type='text' value='"+value.content+"''><img src='/english_exam/public/Manager/exam/images/icon_add.png' onclick='line_add(this);' width='20'><img src='/english_exam/public/Manager//exam/images/icon_delete.png' onclick='line_del(this);'></p>";
            });
            $("ul.contentedit li.itmes").html(itemshtml);
            $("ul.contentedit li.answer").empty();
            var answerhtml="答案:<br/>"
            $.each(questions.answers,function(key,value){
                   answerhtml=answerhtml+"<p><input type='text' size='1' style='width:20px;'  value='"+value.answer+"' name='items' /><img src='/english_exam/public/Manager/exam/images/icon_add.png' onclick='answer_add(this);' width='20'><img src='/english_exam/public/Manager//exam/images/icon_delete.png' onclick='answer_del(this);'></p>";
            });
            $("ul.contentedit li.answer").html(answerhtml);
            break;
        case "5":
            questions.points=$(cur).attr("points");
            questions.tips=$(cur).attr("tips");
            questions.stoptime=$(cur).attr("stoptime");
            questions.playtimes=$(cur).attr("playtimes");
            $(li).eq(0).find("input:eq(0)").val(questions.points);
            $(li).eq(2).find("select").val(questions.tips);
            $(li).eq(3).find("select").val(questions.stoptime);
            $(li).eq(4).find("select").val(questions.playtimes);
            break;
        default:
            
            return;
    }
}

//音频的添加和删除
function voice_add(obj){
  $("#item_tts_auto_edit_template .tt").clone().insertAfter($(obj).parent());
}
function voice_del(obj){
    var id=$(this).parent().attr("id");
    $.getJSON("delTTS",{id:id,ran:Math.random()});
    $(obj).parent().remove();
}

//选项的添加和删除
function item_add(obj){
  $("#qunstion_select_edit_template .itmes p:eq(0)").clone().insertAfter($(obj).parent());
  var ind=$(obj).parents("p").index()-2;
  $(obj).parents("p").nextAll().each(function(key,value){
     $(value).find("input[type='radio']").val(option[ind+key]);
  });
}
function item_del(obj){
    var ind=$(obj).parents("p").index()-3;
    $(obj).parents("p").nextAll().each(function(key,value){
      $(value).find("input[type='radio']").val(option[ind-key]);
    });
    var id=$(obj).parent().attr("id");
    $.getJSON("delItem",{id:id,ran:Math.random()});
    $(obj).parent().remove();
}
//连线题的添加和删除
function line_add(obj){
    $("#qunstion_sort_edit_template .itmes p:eq(0)").clone().insertAfter($(obj).parent());
}
function line_del(obj){
   $(obj).parent().remove();
}
//答案添加和删除
function answer_add(obj){
    $("#qunstion_sort_edit_template .answer p:eq(0)").clone().insertAfter($(obj).parent());
}
function answer_del(obj){
    var id=$(obj).parent().attr("id");
    $.getJSON("delAnswer",{id:id,ran:Math.random()});
   $(obj).parent().remove();
}
//上传按钮事件
function upload(objs){
      //alert("fasdfasd");
      $("#file_uploads").remove();
      $("#upload_mp3").attr("id","");
      $(objs).parent().find("img").attr("id","upload_mp3");
      var obj=$("#file_upload").clone();
      $(obj).removeClass("template");
      $(obj).addClass("curfile");
      $(obj).attr("id","file_uploads");
      $(obj).find("form").attr("id","form");
      $(obj).insertAfter($(objs).parent());
}
//排序题选项的添加和删除
//试卷保存
function savepaper(){
    var ex=[];
    var exam={};
    //考试名称
    var examsid=$("#btn_exam_save").attr("examsid");
    if(examsid=="0"||examsid==""){
        examsid=0;
    }
    exam.examsid=examsid;
    var examname=$("#form_header").find("input[name='exam_name']").val();
    exam.examname=examname;
    //获取单元信息
    var unitid=$("#unit").attr("unitid");
    if(unitid=="0"||unitid==""||unitid==undefined){
        unitid="0";
    }
    //获取身份信息以及试卷类型信息
    var proid=$("#provice").attr("proid");
    if(proid=="0"||proid==""||proid==undefined){
        proid="0";
    }
    var yearid=$("#provice").attr("yearid");
    if(yearid=="0"||yearid==""||yearid==undefined){
        yearid="0";
    }
    var levelid=$("#provice").attr("levelid");
    if(levelid=="0"||levelid==""||levelid==undefined){
        levelid="0";
    }
    var typeid=$("#provice").attr("typeid");
    if(typeid=="0"||typeid==""||typeid==undefined){
        typeid="0";
    }
    exam.unitid=unitid;
    exam.proid=proid;
    exam.yearid=yearid;
    exam.levelid=levelid;
    exam.typeid=typeid;
    //音频处理
    var ttstype=$("#form_header").find("input[name='tts_type']:checked").val();
    exam.ttstype=ttstype;
    //应用类型
    var exams_classid=$("#form_header").find("input[name='exam_type']:checked").val();
    exam.exams_classid=exams_classid;
    //显示内容
    var header_content=$("#form_header").find("p:eq(5)").find("textarea[name='textarea']").val();
    exam.header_content=header_content;
    //停顿设置
    var stopsecond=$("#form_header").find("p:eq(8)").find("select").val();
    exam.stopsecond=stopsecond;
    //自动生成试卷开始音
    if(issys==1){
        var exam_tts=[];
        var tts={};
        var exambegin=$("#form_header").find("p:eq(6)").find("textarea[name='textarea']").val();
        var voiceid=$("#form_header").find("p:eq(6)").find("select:eq(0)").val();
        var id=$("#form_header").find("p:eq(6)").find("img").attr("id");
        if(id==undefined){
            id=0;
        }
        tts.exambegin=exambegin;
        tts.voiceid=voiceid;
        tts.mp3="";
        tts.flag=0;;
        tts.id=id;
        exam_tts.push(tts);
        //自动生成试卷结束音
        tts={};
        exambegin=$("#form_header").find("p:eq(9)").find("textarea[name='textarea']").val();
        voiceid=$("#form_header").find("p:eq(9)").find("select:eq(0)").val();
        id=$("#form_header").find("p:eq(9)").find("img").attr("id");
        if(id==undefined){
            id=0;
        }
        tts.exambegin=exambegin;
        tts.voiceid=voiceid;
        tts.mp3="";
        tts.flag=1;
        tts.id=id;
        exam_tts.push(tts);
        exam.exam_tts=exam_tts;
    }else{
        //自动生成试卷开始音
        var exam_upload=[];
        var upload={};
        exambegin=$("#form_header").find("p:eq(7)").find("textarea[name='textarea']").val();
        var mp3=$("#form_header").find("p:eq(7)").find("img").attr("mp3");
        var id=$("#form_header").find("p:eq(7)").find("img").attr("id");
        upload.exambegin=exambegin;
        upload.voiceid="";
        upload.mp3=mp3;
        upload.flag=0;
        upload.id=id;
        exam_tts.push(tts);
        //自动生成试卷结束音
        upload={};
        exambegin=$("#form_header").find("p:eq(10)").find("textarea[name='textarea']").val();
        mp3=$("#form_header").find("p:eq(10)").find("img").attr("mp3");
        id=$("#form_header").find("p:eq(10)").find("img").attr("id");
        upload.exambegin=exambegin;
        upload.voiceid="";
        upload.mp3=mp3;
        upload.id=id;
        upload.flag=1;
        exam_upload.push(upload);
        exam.exam_tts=exam_upload;

    }
    //独立大题
    var paper=[];
    $("#form_exam .stem_parent").each(function(k,v){
        alert("fsadfasdfasd");
        var id=$(v).attr("id");
        alert(id);
        if(id==undefined){
            id=0;
        }
        var points=$(v).attr("points");
        var stoptime=$(v).attr("stoptime");
        var tips=$(v).attr("tips");
        var playtimes=$(v).attr("playtimes");
        var single=$(v).find("div.content_tts:eq(0)");
        var paper_temp={};
        var tts_content=[];
        $(single).find("p").each(function(key,value){
           //取出前面的发音的人
           var tts_content_temp={};
           if(issys==1){
            var id=$(value).attr("id");
            if(id==undefined){
                id=0;
            }
            tts_content_temp.id=id;
            tts_content_temp.sortid=key+1;
            tts_content_temp.voiceid=$(value).attr("voicetype");
            tts_content_temp.voiceflag=$(value).find("strong").text();
            tts_content_temp.content=$(value).find("font").text();
            tts_content_temp.mp3="";
           }else{
            var id=$(value).attr("id");
            if(id==undefined){
                id=0;
            }
            tts_content_temp.id=id;
            tts_content_temp.sortid=key+1;
            tts_content_temp.voiceid="";
            tts_content_temp.voiceflag=$(value).find("strong").text();
            tts_content_temp.content=$(value).find("font").text();
            tts_content_temp.mp3=$(value).find("img").attr("mp3");
           }
           tts_content.push(tts_content_temp);
        });
        paper_temp.id=id;
        paper_temp.sortid=k+1;
        paper_temp.tts=tts_content;
        paper_temp.points=points;
        paper_temp.stoptime=stoptime;
        paper_temp.playtimes=playtimes;
        paper_temp.tips=tips;
        paper_temp.content=$(v).find("div.content_view:eq(0)").text();
        var stem_type=$(v).hasClass("stem_single");
        paper_temp.stem_type=stem_type?1:2;
        if(stem_type==1){
            var questions_lists=[];
            $(v).find(".question_item").each(function(k1,v1){
                var question=$(v1).find("div.content_tts");
                var questions={};
                var tts_question=[];
                $(question).find("p").each(function(key,value){
                   //取出前面的发音的人
                   var tts_content_temp={};
                   if(issys=="1"){
                     tts_content_temp.id=$(value).attr("id");
                     if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                     }
                     tts_content_temp.voiceid=$(value).attr("voicetype");
                     tts_content_temp.voiceflag=$(value).find("strong").text();
                     tts_content_temp.content=$(value).find("font").text();
                     tts_content_temp.sortid=key+1;
                     tts_content_temp.mp3="";
                   }else{
                     tts_content_temp.id=$(value).attr("id");
                     if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                     }
                     tts_content_temp.voiceid="";
                     tts_content_temp.sortid=key+1;
                     tts_content_temp.voiceflag=$(value).find("strong").text();
                     tts_content_temp.content=$(value).find("font").text();
                     tts_content_temp.mp3=$(value).find("img").attr("mp3");
                   }
                   
                   tts_question.push(tts_content_temp);
                });
                questions.tts=tts_question;
                questions.content=$(v1).find("div.content_view:eq(0)").text();
                //取出题号以及间隔时间以及提示音
                questions.question_num=$(v1).attr("question_num");
                if(questions.question_num==undefined){
                    questions.question_num=0;
                }
                questions.typeid=$(v1).attr("typeid");
                questions.question_num_voice=$(v1).attr("question_num_voice");
                if(questions.question_num_voice==undefined){
                    questions.question_num_voice=0;
                }
                questions.question_stoptime=$(v1).attr("question_stoptime");
                if(questions.question_stoptime==undefined){
                    questions.question_stoptime=0;
                }
                questions.question_note=$(v1).attr("question_note");
                if(questions.question_note==undefined){
                    questions.question_note=0;
                }
                questions.id=$(v1).attr("id");
                if($(v1).attr("id")==undefined){
                    questions.id=0;
                }
                questions.itemtype=$(v1).attr("itemtype");
                if($(v1).attr("itemtype")==undefined){
                    questions.itemtype=1;
                }
                questions.sortid=k1+1;
                //取出选项和答案
                var questype=$(v1).find("div.content_options:eq(0)").parent().attr("data-field-type");
                //判断题型是什么题型
                if(questype=='qunstion_select'){
                    question.typeid=1;
                    var item=[];
                    var answer=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      var item_temp={};
                      var item_answer={};
                      item_temp.flag=$(vi).find("input").val();
                      if($(vi).find("input").is(":checked")){
                        item_answer.content=$(vi).text().substr(2);
                        item_answer.sortid=ki+1;
                        item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                        if(item_answer.id==undefined){
                            item_answer.id=0;
                        }
                        answer.push(item_answer);
                      }
                      item_temp.content=$(vi).text().substr(2);
                      item_temp.id=$(vi).find("input").attr("itemid");
                      item_temp.sortid=ki+1;
                      if(item_temp.id==undefined){
                        item_temp.id=0;
                      }
                      item.push(item_temp);
                   });
                   questions.items=item;
                   questions.answer=answer;
                }else if(questype=='qunstion_judge'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      item_answer={};
                      if($(vi).find("input").is(":checked")){
                        item_answer.content=$(vi).find("input").val();
                        item_answer.sortid=1;
                        item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                        if(item_answer.id==undefined){
                            item_answer.id=0;
                        }
                        answer.push(item_answer);
                      }
                   });
                   questions.items=item;
                   questions.answer=answer;
                }else if(questype=='qunstion_input'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.answer:eq(0)").find("span").each(function(ki,vi){
                       var answer_temp={};
                       answer_temp.id=$(vi).attr("id");
                       if(answer_temp.id==undefined){
                        answer_temp.id=0;
                       }
                       answer_temp.content=$(vi).text();
                       answer_temp.sortid=ki+1;
                       answer.push(answer_temp);
                    });
                    questions.items=item;
                    questions.answer=answer;

                }else if(questype=='qunstion_sort'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      var item_temp={};
                      if(question.itemtype=="1"){
                        item_temp.flag=$(vi).find("input:eq(0)").val();
                        item_temp.content=$(vi).find("input:eq(1)").val();
                        item_temp.id=$(vi).find("input:eq(0)").attr("itemid");
                        if(item_temp.id==undefined){
                            item_temp.id=0;
                        }
                        item_temp.sortid=ki+1;
                        item.push(item_temp);
                      }else{
                        item_temp.flag=$(vi).find("input:eq(0)").val();
                        item_temp.content=$(vi).find("img").attr("href");
                        item_temp.id=$(vi).find("input:eq(0)").attr("itemid");
                        if(item_temp.id==undefined){
                            item_temp.id=0;
                        }
                        item_temp.sortid=ki+1;
                        item.push(item_temp);
                      }
                   });
                    $(v1).find("div.answer:eq(0)").find("span").each(function(ki,vi){
                       var answer_temp={};
                       answer_temp.id=$(vi).attr("id");
                       if(answer_temp.id==undefined){
                            answer_temp.id=0;
                        }
                       answer_temp.content=$(vi).text();
                       answer_temp.sortid=ki+1;
                       answer.push(answer_temp);
                    });
                   // item_answer.content=$(v1).find("div.answer:eq(0)").text().substr(4);;
                   // item_answer.sortid=1;
                   
                   questions.items=item;
                   questions.answer=answer;
                }
                
                questions_lists.push(questions);
            });
            paper_temp.questions_lists=questions_lists;
            paper_temp.son=[];
            
        }else{
            paper_temp.questions_lists=[];
            var son=[];
            $(v).find(".stem_son").each(function(ks,vs){
                if($(vs).hasClass("emputyfiled")){
                    continue;
                }
                var son_temp={};
                var pointsc=$(vs).attr("points");
                if($(vs).attr("points")==undefined){
                    pointsc==0;
                }
                var stoptimesc=$(vs).attr("stoptime");
                if($(vs).attr("stoptime")==undefined){
                    stoptimesc==0;
                }
                var playtimesc=$(vs).attr("playtimes");
                if($(vs).attr("playtimes")==undefined){
                    playtimesc==0;
                }
                var tipsc=$(vs).attr("tips");
                if($(vs).attr("tips")==undefined){
                    tipsc==0;
                }
                var singlec=$(vs).find("div.content_tts:eq(0)").text();
                var id=$(vs).attr("id");
                if(id==undefined){
                    id=0;
                }
                var sontts=$(vs).find("div.content_tts:eq(0)");
                var tts_content=[];
                $(sontts).find("p").each(function(key,value){
                   //取出前面的发音的人
                   var tts_content_temp={};
                   if(issys=="1"){
                      tts_content_temp.id=$(value).attr("id");
                      if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                      }
                      tts_content_temp.voiceid=$(value).attr("ttstype");
                      tts_content_temp.voiceflag=$(value).find("strong").text();
                      tts_content_temp.content=$(value).find("font").text();
                      tts_content_temp.sortid=key+1;
                      tts_content_temp.mp3="";
                   }else{
                      tts_content_temp.id=$(value).attr("id");
                      if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                      }
                      tts_content_temp.voiceid="";
                      tts_content_temp.sortid=key+1;
                      tts_content_temp.voiceflag=$(value).find("strong").text();
                      tts_content_temp.content=$(value).find("font").text();
                      tts_content_temp.sortid=key+1;
                      tts_content_temp.mp3=$(value).find("img").attr("mp3");;
                   }
                   tts_content.push(tts_content_temp);
                });
                son_temp.id=id;
                son_temp.content=$(vs).find("div.content_view:eq(0)").text();
                son_temp.tts=tts_content;
                son_temp.points=pointsc;
                son_temp.stoptime=stoptimesc;
                son_temp.playtimes=playtimesc;
                son_temp.tips=tipsc;
                son_temp.stem_type=2;
                var son_questionlists=[];
                $(vs).find(".question_item").each(function(k1,v1){
                var question=$(v1).find("div.content_tts");
                var questions={};
                var tts_question=[];
                $(question).find("p").each(function(key,value){
                   //取出前面的发音的人
                   var tts_content_temp={};
                   if(issys=="1"){
                     tts_content_temp.id=$(value).attr("id");
                     if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                     }
                     tts_content_temp.voiceid=$(value).attr("voicetype");
                     tts_content_temp.voiceflag=$(value).find("strong").text();
                     tts_content_temp.content=$(value).find("font").text();
                     tts_content_temp.sortid=key+1;
                     tts_content_temp.mp3="";
                   }else{
                     tts_content_temp.id=$(value).attr("id");
                     if(tts_content_temp.id==undefined){
                        tts_content_temp.id=0;
                     }
                     tts_content_temp.voiceid="";
                     tts_content_temp.sortid=key+1;
                     tts_content_temp.voiceflag=$(value).find("strong").text();
                     tts_content_temp.content=$(value).find("font").text();
                     tts_content_temp.mp3=$(value).find("img").attr("mp3");
                   }
                   
                   tts_question.push(tts_content_temp);
                });
                questions.tts=tts_question;
                questions.content=$(v1).find("div.content_view:eq(0)").text();
                //取出题号以及间隔时间以及提示音
                questions.question_num=$(v1).attr("question_num");
                if(questions.question_num==undefined){
                    questions.question_num=0;
                }
                questions.typeid=$(v1).attr("typeid");
                questions.question_num_voice=$(v1).attr("question_num_voice");
                if(questions.question_num_voice==undefined){
                    questions.question_num_voice=0;
                }
                questions.question_stoptime=$(v1).attr("question_stoptime");
                if(questions.question_stoptime==undefined){
                    questions.question_stoptime=0;
                }
                questions.question_note=$(v1).attr("question_note");
                if(questions.question_note==undefined){
                    questions.question_note=0;
                }
                questions.id=$(v1).attr("id");
                if($(v1).attr("id")==undefined){
                    questions.id=0;
                }
                questions.itemtype=$(v1).attr("itemtype");
                if($(v1).attr("itemtype")==undefined){
                    questions.itemtype=1;
                }
                questions.sortid=k1+1;
                //取出选项和答案
                var questype=$(v1).find("div.content_options:eq(0)").parent().attr("data-field-type");
                //判断题型是什么题型
                if(questype=='qunstion_select'){
                    question.typeid=1;
                    var item=[];
                    var answer=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      var item_temp={};
                      var item_answer={};
                      item_temp.flag=$(vi).find("input").val();
                      if($(vi).find("input").is(":checked")){
                        item_answer.content=$(vi).text().substr(2);
                        item_answer.sortid=ki+1;
                        item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                        if(item_answer.id==undefined){
                            item_answer.id=0;
                        }
                        answer.push(item_answer);
                      }
                      item_temp.content=$(vi).text().substr(2);
                      item_temp.id=$(vi).find("input").attr("itemid");
                      item_temp.sortid=ki+1;
                      if(item_temp.id==undefined){
                        item_temp.id=0;
                      }
                      item.push(item_temp);
                   });
                   questions.items=item;
                   questions.answer=answer;
                }else if(questype=='qunstion_judge'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      item_answer={};
                      if($(vi).find("input").is(":checked")){
                        item_answer.content=$(vi).find("input").val();
                        item_answer.sortid=1;
                        item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                        if(item_answer.id==undefined){
                            item_answer.id=0;
                        }
                        answer.push(item_answer);
                      }
                   });
                   questions.items=item;
                   questions.answer=answer;
                }else if(questype=='qunstion_input'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.answer:eq(0)").find("span").each(function(ki,vi){
                       var answer_temp={};
                       answer_temp.id=$(vi).attr("id");
                       if(answer_temp.id==undefined){
                        answer_temp.id=0;
                       }
                       answer_temp.content=$(vi).text();
                       answer_temp.sortid=ki+1;
                       answer.push(answer_temp);
                    });
                    questions.items=item;
                    questions.answer=answer;

                }else if(questype=='qunstion_sort'){
                    var answer=[];
                    var item=[];
                    $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                      var item_temp={};
                      if(question.itemtype=="1"){
                        item_temp.flag=$(vi).find("input:eq(0)").val();
                        item_temp.content=$(vi).find("input:eq(1)").val();
                        item_temp.id=$(vi).find("input:eq(0)").attr("itemid");
                        if(item_temp.id==undefined){
                            item_temp.id=0;
                        }
                        item_temp.sortid=ki+1;
                        item.push(item_temp);
                      }else{
                        item_temp.flag=$(vi).find("input:eq(0)").val();
                        item_temp.content=$(vi).find("img").attr("href");
                        item_temp.id=$(vi).find("input:eq(0)").attr("itemid");
                        if(item_temp.id==undefined){
                            item_temp.id=0;
                        }
                        item_temp.sortid=ki+1;
                        item.push(item_temp);
                      }
                   });
                    $(v1).find("div.answer:eq(0)").find("span").each(function(ki,vi){
                       var answer_temp={};
                       answer_temp.id=$(vi).attr("id");
                       if(answer_temp.id==undefined){
                            answer_temp.id=0;
                        }
                       answer_temp.content=$(vi).text();
                       answer_temp.sortid=ki+1;
                       answer.push(answer_temp);
                    });
                   // item_answer.content=$(v1).find("div.answer:eq(0)").text().substr(4);;
                   // item_answer.sortid=1;
                   
                   questions.items=item;
                   questions.answer=answer;
                }  
                    son_questionlists.push(questions);
                });
                son_temp.questions_lists=son_questionlists;
                son.push(son_temp);
            });
            paper_temp.son=son;
        }
        paper.push(paper_temp);    
    });
    exam.paper=paper;
    ex.push(exam);
    $.post("savePaper",{data:JSON.stringify(ex),ran:Math.random()}); 
}










