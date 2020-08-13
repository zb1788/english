$.ajaxSetup({async:false});
var content="显示内容: <br /><script id='editor6' type='text/plain' style='width:265px;height:250px;'></script>";
//controllerToQuestion编辑窗口到试题页面
var option=['A','B','C','D','E','F','G'];


//进行音频的处理
$("input[class='ttstype']").on("change",function(){
     //总共有几种类型
    var ttstype=$(this).val();
    if(ttstype=="3"||ttstype=="4"||ttstype=="6"){
        $(this).parent().find("input:eq(0)").val("W");
    }else if(ttstype=="5"||ttstype=="7"){
        $(this).parent().find("input:eq(0)").val("M");
    }
});

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
        var ttsobject="";
        //3、听力材料
        var issys=$('#form_header input[name="tts_type"]:checked').val();
        if(issys==1){
           ttsobject=$(question).find("li:eq(1)").find(".tt");
        }else{
           ttsobject=$(question).find("li:eq(1)").find(".upload");
        }

        //alert($(ttsobject).html());
        var ttss=[];
        
        $(ttsobject).each(function(key,val){
            var tts={};

            if(issys==1){
                tts.id=$(val).find("input").attr("id");
                tts.ttsnote=$(val).find("input").val();
                tts.tcontent=$(val).find("textarea").val();
                tts.ttsvoice=$(val).find("select:eq(0)").val();
                tts.ttstime=$(val).find("select:eq(1)").val();
                tts.ttstype=$(val).attr("ttstype");
                tts.stflag=$(val).find("input[type='checkbox']").is(":checked")?1:0;
                tts.mp3="";
            }else{
                tts.id=$(val).find("input").attr("id");
                tts.ttstype=$(val).attr("ttstype");
                tts.ttsnote=$(val).find("input").val();
                tts.tcontent=$(val).find("textarea").val();
                tts.ttsvoice=$(val).find("select:eq(0)").val();
                tts.ttstime=$(val).find("select:eq(1)").val();
                tts.stflag=$(val).find("input[type='checkbox']").is(":checked")?1:0;
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
        //问题的关键词
        questions.question_key1=$(question).find(".pro").val();
        questions.question_key2=$(question).find(".topic").val();
        questions.question_key3=$(question).find(".skill").val();
        questions.question_key4=$(question).find(".diff").val();
        
        var cur=$(".curedit").parent("div");
        $(cur).attr("question_num",questions.question_num);
        $(cur).attr("question_num_voice",questions.question_num_voice);
        $(cur).attr("tips",questions.question_note);
        $(cur).attr("question_stoptime",questions.question_stoptime);
        $(cur).attr("que_key1",questions.question_key1);
        $(cur).attr("que_key2",questions.question_key2);
        $(cur).attr("que_key3",questions.question_key3);
        $(cur).attr("que_key4",questions.question_key4);
		$(cur).find(".tihao").text(questions.question_num);
        var ttshtmls="";
        $.each(questions.ttss,function(key,val){
            if(val.ttsnote!=""){
                ttshtmls=ttshtmls+"<p id='"+val.id+"' stoptime='"+val.ttstime+"'  voicetype='"+val.ttsvoice+"'  ttstype='"+val.ttstype+"'  stflag='"+val.stflag+"'><strong>"+val.ttsnote+"</strong>:";
            }else{
                ttshtmls=ttshtmls+"<p id='"+val.id+"'  stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"' ttstype='"+val.ttstype+"' stflag='"+val.stflag+"'><strong></strong>"; 
            }
            ttshtmls=ttshtmls+"<font mp3="+val.mp3+">"+val.tcontent+"</font></p>";
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
            //选项的类型
            var questype=$(question_item).find("input[name='questype']:checked").val();
            $(cur).attr("itemtype",questype);
            $(question_item).find("p").each(function(key,val){
              var item={};
              if(questype=="0"){
                item.itemtype=$(val).find("input[type='radio']").is(":checked")?1:0;
                item.itemoption=$(val).find("input[type='radio']").val();
                item.id=$(val).find("input[type='radio']").attr("id");
                item.itemcontent=$(val).find("input[type='text']").val();
              }else{
                item.itemtype=$(val).find("input[type='radio']").is(":checked")?1:0;
                item.itemoption=$(val).find("input[type='radio']").val();
                item.id=$(val).find("input[type='radio']").attr("id");
                item.itemcontent=$(val).find("img:eq(0)").attr("mp3");

              }
              items.push(item);
            });
            question_items.contents=items;
            //8、选项排列方式
            var itemlayout=$(question).find("li:eq(6)").find("input:checked").val();
            $(cur).attr("stemtype",itemlayout);
            question_items.itemlayout=itemlayout;
            questions.question_items=question_items;
            arr.push(questions);
            //$(cur).find(".content_options").empty();

            var items="";
            $.each(questions.question_items.contents,function(key,val){
                var itemhtml="<p id='"+val.id+"'>";            
                if(val.itemtype){
                     itemhtml=itemhtml+"<input type='radio' id='"+val.id+"' checked='true' value='"+val.itemoption+"' disabled='disabled' type='radio'/>";
                     $(cur).find(".answer").html("答案："+val.itemoption);
                }else{
                     itemhtml=itemhtml+"<input type='radio' id='"+val.id+"' value='"+val.itemoption+"' disabled='disabled'  type='radio'/>";
                }
                if(questype=="0"){
                   itemhtml=itemhtml+val.itemoption+"."+val.itemcontent+"</p>"; 
                }else{
                    itemhtml=itemhtml+val.itemoption+".<img width='30px' height='30px' src='../../uploads/"+val.itemcontent+"'/></p>";
                }
                
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
                var flag=Math.random();
                $(cur).find(".content_options").html("<p><input name='junge"+flag+"' checked='checked' type='radio' value='1' disabled='disabled'/>True</p><p><input name='junge"+flag+"' value='0' type='radio'   disabled='disabled' />False</p>");
                $(cur).find(".answer").html("答案：True");
            }else{
                var flag1=Math.random();
                $(cur).find(".content_options").html("<p><input name='junge"+flag1+"'  type='radio'  value='1' disabled='disabled'/>True</p><p><input name='junge"+flag1+"' type='radio' value='0'  disabled='disabled' checked='checked'/>False</p>");
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
            question_items.question_item_type=$(question).find("input[name='questype']:checked").val();
            $(cur).attr("itemtype",question_items.question_item_type);
            var items=[];
            $(question_item).find("p").each(function(key,val){
              if(question_items.question_item_type==0){
                var item={};
                item.id=$(val).attr("id");
                item.itemoption=$(val).find("input:eq(0)").val();
                item.itemcontent=$(val).find("input:eq(1)").val();
                items.push(item);
              }else{
                var item={};
                item.id=$(val).attr("id");
                item.itemoption=$(val).find("input:eq(0)").val();
                item.itemcontent=$(val).find("img:eq(0)").attr("mp3");
                items.push(item);
              }
              
            });
            question_items.contents=items;
            //8、选项排列方式

            questions.question_items=question_items;
            
            //$(cur).find(".content_options").empty();
            var question_answer=$(question).find("li:eq(6)");
            var answer=[];
            $(question_answer).find("p").each(function(key,val){
              var item={};
              item.id=$(val).attr("id");
              item.answer_num=$(val).find("input[type='text']:eq(0)").val();
              item.answercontent=$(val).find("input[type='text']:eq(1)").val();
              answer.push(item);
            });
            question_items.options=answer;
            //8、选项排列方式
            var itemlayout=$(question).find("input[name='layout']:checked").val();
            $(cur).attr("stemtype",itemlayout);
            question_items.itemlayout=itemlayout;
            questions.question_answer=question_items;
            arr.push(questions);
            var items="";
            $.each(questions.question_items.contents,function(key,val){
                if(question_items.question_item_type==0){
                    var itemhtml="<p>";       
                    itemhtml=itemhtml+"<strong id='"+val.id+"'>"+val.itemoption+"</strong>.<font>"+val.itemcontent+"</font></p>";
                    items=items+itemhtml;
                }else{
                    var itemhtml="<p>";       
                    itemhtml=itemhtml+"<strong id='"+val.id+"'>"+val.itemoption+"</strong>.<img src='../../uploads/"+val.itemcontent+"' width='30px' height='30px'/></p>";
                    items=items+itemhtml;
                }
                
            });
            var answer="";
             $.each(questions.question_items.options,function(key,val){
                answer=answer+"&nbsp;<span id='"+val.id+"'><font>"+val.answer_num+"</font>.<font>"+val.answercontent+"</font></span>&nbsp;";
            });
            $(cur).find(".answer").html("答案:"+answer);
            $(cur).find(".content_options").attr("question_item_type",question_items.question_item_type);
            $(cur).find(".content_options").html(items);

            break;
        default:
            return;
        }
    }else{
        var issys=$('#form_header input[name="tts_type"]:checked').val();
        ///alert("aaaa");
        var html = "";
        var arr=[];
        //进行json数据的组合
        var question=$(".contentedit");
        var questions={};
        //2、小题分值
        questions.question_points=$(question).find("li:eq(0)").find("input").val();
        //3、听力材料
        var ttsobject="";
        //3、听力材料
        //alert(issys);
        if(issys==1){
           ttsobject=$(question).find(".tt");
        }else{
           ttsobject=$(question).find(".upload");
        }
        //alert($(ttsobject).html());
        //alert($(ttsobject).html());
        //alert("fasaaa");
        var ttss=[];
        $(ttsobject).each(function(key,val){
            var tts={};
            if(issys==1){
                //alert("fsadfasd");
              tts.id=$(val).find("input").attr("id");
              tts.ttsnote=$(val).find("input").val();
              tts.tcontent=$(val).find("textarea").val();
              tts.ttsvoice=$(val).find("select:eq(0)").val();
              tts.ttstime=$(val).find("select:eq(1)").val();
              tts.ttstype=$(val).attr("ttstype");
              tts.stflag=$(val).find("input[type='checkbox']").is(":checked")?1:0;
              tts.mp3="";
            }else{
              tts.id=$(val).find("input").attr("id");
              tts.ttsnote=$(val).find("input").val();
              tts.tcontent=$(val).find("textarea").val();
              tts.ttsvoice=$(val).find("select:eq(0)").val();
              tts.ttstime=$(val).find("select:eq(1)").val();
              tts.mp3=$(val).find("img").attr("mp3");
              tts.ttstype=$(val).attr("ttstype");
              tts.stflag=$(val).find("input[type='checkbox']").is(":checked")?1:0;
            }
            ttss.push(tts);
        });
        questions.ttss=ttss;
        //4、问题提示音
        questions.question_tips=$(question).find("li:eq(2)").find("select").val();
        
        //5、问题停顿时间
        questions.question_stoptime=$(question).find("li:eq(3)").find("select").val();
        if(questions.question_stoptime==undefined){
           questions.question_stoptime=$(question).find("li:eq(1)").find("select").val(); 
        }
        //6、问题播放次数
        questions.question_playtimes=$(question).find("li:eq(4)").find("select").val();
        //7、问题题干
        questions.question_stem=ue.getContent();
        //alert(questions.question_stem);
        var cur=$(".curedit");
        //alert(cur.html());
        var ttshtml="";
        $.each(questions.ttss,function(key,val){
            if(val.ttsnote){
                ttshtml=ttshtml+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"' ttstype='"+val.ttstype+"' stflag='"+val.stflag+"'><strong>"+val.ttsnote+"</strong>:";
            }else{
                ttshtml=ttshtml+"<p id='"+val.id+"' stoptime='"+val.ttstime+"' voicetype='"+val.ttsvoice+"' ttstype='"+val.ttstype+"' stflag='"+val.stflag+"'><strong>"+val.ttsnote+"</strong>";
            }
             ttshtml=ttshtml+"<font mp3="+val.mp3+">"+val.tcontent+"</font></p>";
        });
        $(cur).parent().find(".content_tts:eq(0)").html(ttshtml);
        $(cur).parent().attr("stoptime",questions.question_stoptime);
        $(cur).parent().attr("points",questions.question_points);
        $(cur).parent().attr("tips",questions.question_tips);
        $(cur).parent().attr("playtimes",questions.question_playtimes);
        $(cur).parent().find(".content_view:eq(0)").html(questions.question_stem);
    }
    $('.substr:eq(0)').text('同步成功');
    setTimeout("$('.substr:eq(0)').text('')",1000);
	
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
        tts_temp.id=$(val).attr("id");
        tts_temp.ques_tts_content=$(val).find("font").text();
        tts_temp.ques_tts_mp3=$(val).find("font").attr("mp3");
        //alert(tts_temp.ques_tts_content);
        tts_temp.ques_tts_voicetype=$(val).attr("voicetype");
        tts_temp.ques_tts_stoptime=$(val).attr("stoptime");
        tts_temp.ques_tts_voicenote=$(val).find("strong").text();
        tts_temp.ques_tts_ttstype=$(val).attr("ttstype");
        tts_temp.ques_tts_stflag=$(val).attr("stflag");
        ttss.push(tts_temp);
    })
    questions.ttss=ttss;
    var ttsobject=$("ul.contentedit li.tts");
    $(ttsobject).empty();
    ttsobject.append("听力材料:<br>");
    //alert(questions.ttss.length);
    if(questions.ttss.length==0){
        if ($('#form_header input[name="tts_type"]:checked').val() == "1"){
          var tts=$("#item_tts_auto_edit_template .tt").clone();
          if(type=='5'){
            $(tts).find("input[type='checkbox']").click();
          }
          ttsobject.append(tts);
        }else{
          var tts=$("#item_tts_user_edit_template .upload").clone();
          ttsobject.append(tts);
        }
    }

    $.each(questions.ttss,function(key,value){
        if($('#form_header input[name="tts_type"]:checked').val()=="1"){
            var tts=$("#item_tts_auto_edit_template .tt").clone();
            $(tts).find("input:eq(0)").val(value.ques_tts_voicenote);
            $(tts).find("input:eq(0)").attr("id",value.id);
            $(tts).find("textarea").val(value.ques_tts_content);
            $(tts).find("select:eq(0)").val(value.ques_tts_voicetype);
            $(tts).find("select:eq(1)").val(value.ques_tts_stoptime);
            $(tts).attr("ttstype",value.ques_tts_ttstype);
            $(tts).find(".ting").attr("mp3","");
            if(value.ques_tts_stflag==1){
                $(tts).find("input[type='checkbox']").click();
            }
            ttsobject.append(tts);
        }else{
            var tts=$("#item_tts_user_edit_template .upload").clone();
            $(tts).find("input:eq(0)").val(value.ques_tts_voicenote);
            $(tts).attr("ttstype",value.ques_tts_ttstype);
            $(tts).find("input:eq(0)").attr("id",value.id);
            $(tts).find("textarea").val(value.ques_tts_content);
            $(tts).find("select:eq(0)").val(value.ques_tts_voicetype);
            $(tts).find("select:eq(1)").val(value.ques_tts_stoptime);
            $(tts).find(".ting").attr("mp3",value.ques_tts_mp3);
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
            questions.question_note=$(cur).attr("tips");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            questions.question_display=$(cur).attr("stemtype");
            questions.itemtype=$(cur).attr("itemtype");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            if(questions.question_num_voice==''||questions.question_num_voice==undefined||questions.question_num_voice==null||questions.question_num_voice=="null"){
                $(li).eq(0).find("select:eq(0)").val(3);
            }
            $(li).eq(2).find("select:eq(0)").val(questions.question_note);
			if(questions.question_note==''||questions.question_note==undefined||questions.question_note==null||questions.question_note=="null"){
                $(li).eq(2).find("select:eq(0)").val(0);
            }
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
			if(questions.question_stoptime==''||questions.question_stoptime==undefined||questions.question_stoptime==null||questions.question_stoptime=="null"){
                $(li).eq(3).find("select:eq(0)").val(3);
            }
            $("ul.contentedit li.itmes").empty();
            var items=[];
            $(cur).find(".content_options p").each(function(key,value){
                var item={};
                if(questions.itemtype==0){
                   item.answer=$(value).find("input").is(":checked")?1:0;
                   item.id=$(value).find("input").attr("id");
                   item.content=$(value).text().substr(2);
                   items.push(item);
                }else if(questions.itemtype==1){
                   item.answer=$(value).find("input").is(":checked")?1:0;
                   item.id=$(value).find("input").attr("id");
                   item.content=$(value).find("img").attr("src").substr(14);
                   items.push(item);
                }else{
                    item.answer=$(value).find("input").is(":checked")?1:0;
                   item.id=$(value).find("input").attr("id");
                   item.content=$(value).text().substr(2);
                   items.push(item);
                }
            });
            questions.items=items;
            var question_item_type=$(cur).find(".content_options").attr("question_item_type");
            var itemshtml="";
            if(questions.itemtype==0){
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\" >文字<input name='questype' class='intext' value='1' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }else if(questions.itemtype==1){
                itemshtml="选项内容:<input name='questype' class='intext' value='0'  type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\">文字<input name='questype' class='intext' value='1' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }else{
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\">文字<input name='questype' class='intext' value='1' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }
            $.each(questions.items,function(key,value){
                if(value.answer==1){
                   itemshtml=itemshtml+"<p><input type='radio' id='"+value.id+"' checked='checked' value='"+option[key]+"' name='items' />";
                }else{
                   itemshtml=itemshtml+"<p><input type='radio' id='"+value.id+"' value='"+option[key]+"' name='items'/>";
                }
                if(questions.itemtype==0){
                    itemshtml=itemshtml+"<input name='textfield' size='30' type='text' value=\""+value.content+"\">&nbsp;<input name='upload'  class='file' type='button' value='上传' style='display:none;' onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='item_add(this);' width='20'><img src='/public/Manager//exam/images/icon_delete.png' onclick='item_del(this);'></p>";
                }else if(questions.itemtype==1){
                    itemshtml=itemshtml+"<input name='textfield' size='30' type='text' value=''  style='display:none;'>&nbsp;<input name='upload'  class='file' type='button' value='上传'  onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='item_add(this);' width='20' mp3='"+value.content+"'><img src='/public/Manager/exam/images/icon_delete.png' onclick='item_del(this);'></p>";
                }else{
                    itemshtml=itemshtml+"<input name='textfield' size='30' type='text' value=\""+value.content+"\">&nbsp;<input name='upload'  class='file' type='button' value='上传' style='display:none;' onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='item_add(this);' width='20'><img src='/public/Manager/exam/images/icon_delete.png' onclick='item_del(this);'></p>";
                }
                
            });
            questions.question_key1=$(cur).attr("que_key1");
            questions.question_key2=$(cur).attr("que_key2");
            questions.question_key3=$(cur).attr("que_key3");
            questions.question_key4=$(cur).attr("que_key4");
            $("ul.contentedit .pro").val(questions.question_key1);
            if(questions.question_key1==''||questions.question_key1==undefined||questions.question_key1==null||questions.question_key1=="null"){
                $("ul.contentedit .pro").val(93);
            }
            $("ul.contentedit .topic").val(questions.question_key2);
            $("ul.contentedit .skill").val(questions.question_key3);
            if(questions.question_key3==''||questions.question_key3==undefined||questions.question_key3==null||questions.question_key3=="null"){
                $("ul.contentedit .skill").val(81);
            }
            $("ul.contentedit .diff").val(questions.question_key4);
            if(questions.question_key4==''||questions.question_key4==undefined||questions.question_key4==null||questions.question_key4=="null"){
                $("ul.contentedit .diff").val(85);
            }
            $("ul.contentedit li.itmes").html(itemshtml);
            $("ul.contentedit").find("input[name='layout'][value='"+questions.question_display+"']").click();

        break;
        case "2":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            if(questions.question_num_voice==''||questions.question_num_voice==undefined||questions.question_num_voice==null||questions.question_num_voice=="null"){
                $(li).eq(0).find("select:eq(0)").val(3);
            }
			if(questions.question_note==''||questions.question_note==undefined||questions.question_note==null||questions.question_note=="null"){
                $(li).eq(2).find("select:eq(0)").val(0);
            }
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
			if(questions.question_stoptime==''||questions.question_stoptime==undefined||questions.question_stoptime==null||questions.question_stoptime=="null"){
                $(li).eq(3).find("select:eq(0)").val(3);
            }
            $("ul.contentedit li.itmes").empty();
            var answer=$(cur).find(".content_options input:checked").val();
            //alert(answer);
            questions.answer=answer;
            var itemshtml=" 答&nbsp;&nbsp;&nbsp;案:";
            if(questions.answer==1){
               itemshtml=itemshtml+"&nbsp;<input name='judge'  class='intext' type='radio' value='1'  checked='True'/>True&nbsp;<input name='judge'  class='intext' type='radio' value='0' />False";
            }else{
               itemshtml=itemshtml+"&nbsp;<input name='judge'  class='intext' type='radio' value='1' />True&nbsp;<input name='judge'  class='intext' type='radio' value='0' checked='True'/>False";
            }
            $("ul.contentedit li.itmes").html(itemshtml);
            questions.question_key1=$(cur).attr("que_key1");
            questions.question_key2=$(cur).attr("que_key2");
            questions.question_key3=$(cur).attr("que_key3");
            questions.question_key4=$(cur).attr("que_key4");
            $("ul.contentedit .pro").val(questions.question_key1);
            if(questions.question_key1==''||questions.question_key1==undefined||questions.question_key1==null||questions.question_key1=="null"){
                $("ul.contentedit .pro").val(21);
            }
            $("ul.contentedit .topic").val(questions.question_key2);
            $("ul.contentedit .skill").val(questions.question_key3);
            if(questions.question_key3==''||questions.question_key3==undefined||questions.question_key3==null||questions.question_key3=="null"){
                $("ul.contentedit .skill").val(81);
            }
            $("ul.contentedit .diff").val(questions.question_key4);
            if(questions.question_key4==''||questions.question_key4==undefined||questions.question_key4==null||questions.question_key4=="null"){
                $("ul.contentedit .diff").val(85);
            }
            break;
        case "3":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            if(questions.question_num_voice==''||questions.question_num_voice==undefined||questions.question_num_voice==null||questions.question_num_voice=="null"){
                $(li).eq(0).find("select:eq(0)").val(3);
            }
            if(questions.question_note==''||questions.question_note==undefined||questions.question_note==null||questions.question_note=="null"){
                $(li).eq(2).find("select:eq(0)").val(0);
            }
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
			if(questions.question_stoptime==''||questions.question_stoptime==undefined||questions.question_stoptime==null||questions.question_stoptime=="null"){
                $(li).eq(3).find("select:eq(0)").val(3);
            }
            questions.question_key1=$(cur).attr("que_key1");
            questions.question_key2=$(cur).attr("que_key2");
            questions.question_key3=$(cur).attr("que_key3");
            questions.question_key4=$(cur).attr("que_key4");
            $("ul.contentedit .pro").val(questions.question_key1);
            if(questions.question_key1==''||questions.question_key1==undefined||questions.question_key1==null||questions.question_key1=="null"){
                $("ul.contentedit .pro").val(21);
            }
            $("ul.contentedit .topic").val(questions.question_key2);
            $("ul.contentedit .skill").val(questions.question_key3);
            if(questions.question_key3==''||questions.question_key3==undefined||questions.question_key3==null||questions.question_key3=="null"){
                $("ul.contentedit .skill").val(81);
            }
            $("ul.contentedit .diff").val(questions.question_key4);
            if(questions.question_key4==''||questions.question_key4==undefined||questions.question_key4==null||questions.question_key4=="null"){
                $("ul.contentedit .diff").val(85);
            }
            break;
        case "4":
            questions.question_num=$(cur).attr("question_num");
            questions.question_num_voice=$(cur).attr("question_num_voice");
            questions.question_note=$(cur).attr("question_note");
            questions.question_stoptime=$(cur).attr("question_stoptime");
            questions.itemtype=$(cur).attr("itemtype");
            questions.stemtype=$(cur).attr("stemtype");
            $(li).eq(0).find("input:eq(0)").val(questions.question_num);
            $(li).eq(0).find("select:eq(0)").val(questions.question_num_voice);
            if(questions.question_num_voice==''||questions.question_num_voice==undefined||questions.question_num_voice==null||questions.question_num_voice=="null"){
                $(li).eq(0).find("select:eq(0)").val(3);
            }
           if(questions.question_note==''||questions.question_note==undefined||questions.question_note==null||questions.question_note=="null"){
                $(li).eq(2).find("select:eq(0)").val(0);
            }
            $(li).eq(3).find("select:eq(0)").val(questions.question_stoptime);
			if(questions.question_stoptime==''||questions.question_stoptime==undefined||questions.question_stoptime==null||questions.question_stoptime=="null"){
                $(li).eq(3).find("select:eq(0)").val(3);
            }
            var items=[];
            $(cur).find(".content_options p").each(function(key,value){
                if(questions.itemtype==0){
                    var item={};
                    item.answer=$(value).find("strong").text();
                    item.id=$(value).find("strong").attr("id");
                    item.content=$(value).find("font").text();
                    items.push(item);
                }else if(questions.itemtype==1){
                    var item={};
                    item.answer=$(value).find("strong").text();
                    item.id=$(value).find("strong").attr("id");
                    item.content=$(value).find("img").attr("src").substr(14);
                    items.push(item);
                }else{
                    var item={};
                    item.answer=$(value).find("strong").text();
                    item.id=$(value).find("strong").attr("id");
                    item.content=$(value).find("font").text();
                    items.push(item);
                }
                
            });
            questions.items=items;
            var answers=[];
            $(cur).find(".answer span").each(function(key,value){
                var item={};
                item.id=$(value).attr("id");
                item.answer_num=$(value).find("font:eq(0)").text();
                item.answer=$(value).find("font:eq(1)").text();
                answers.push(item);
            });
            questions.itemtype=$(cur).attr("itemtype");
            questions.answers=answers;
            $("ul.contentedit li.itmes").empty();
            var itemshtml="";
            if(questions.itemtype==0){
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\">文字<input name='questype' class='intext' value='1' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }else if(questions.itemtype==1){
                itemshtml="选项内容:<input name='questype' class='intext' value='0'  type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\">文字<input name='questype' class='intext' value='1' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }else{
                itemshtml="选项内容:<input name='questype' class='intext' value='0' checked='checked' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').show();$(this).parent().find('.file').hide();\">文字<input name='questype' class='intext' value='1' type='radio' onclick=\"javascript:$(this).parent().find('input[name=textfield]').hide();$(this).parent().find('.file').show();\">图片<br>";
            }
            $.each(questions.items,function(key,value){
                if(questions.itemtype==0){
                    itemshtml=itemshtml+"<p id='"+value.id+"'><input type='text' size='1' style='width:20px;' value='"+value.answer+"' name='items' />&nbsp;<input name='textfield' size='20' type='text' value='"+value.content+"'>&nbsp;<input name='upload'  class='file' type='button' value='上传' style='display:none;' onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='line_add(this);' width='20'>&nbsp;<img src='/public/Manager//exam/images/icon_delete.png' onclick='line_del(this);'></p>";
                }else if(questions.itemtype==1){
                    itemshtml=itemshtml+"<p id='"+value.id+"'><input type='text' size='1' style='width:20px;' value='"+value.answer+"' name='items' />&nbsp;<input name='textfield' size='20' type='text' value='' style='display:none;'>&nbsp;<input name='upload'  class='file' type='button' value='上传'  onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' mp3='"+value.content+"' onclick='line_add(this);' width='20'>&nbsp;<img src='/public/Manager//exam/images/icon_delete.png' onclick='line_del(this);'></p>";
                }else{
                    itemshtml=itemshtml+"<p id='"+value.id+"'><input type='text' size='1' style='width:20px;' value='"+value.answer+"' name='items' />&nbsp;<input name='textfield' size='20' type='text' value='"+value.content+"'>&nbsp;<input name='upload'  class='file' type='button' value='上传' style='display:none;' onclick='upload(this);'/>&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='line_add(this);' width='20'>&nbsp;<img src='/public/Manager//exam/images/icon_delete.png' onclick='line_del(this);'></p>";
                }
                   
            });
            $("ul.contentedit li.itmes").html(itemshtml);
            $("ul.contentedit li.answer").empty();
            var answerhtml="答案:<br/>"
            $.each(questions.answers,function(key,value){
                   answerhtml=answerhtml+"<p id='"+value.id+"'><input type='text' size='1' style='width:20px;'  value='"+value.answer_num+"' name='items' /><input type='text' size='1' style='width:20px;'  value='"+value.answer+"' name='items' />&nbsp;<img src='/public/Manager/exam/images/icon_add.png' onclick='answer_add(this);' width='20'>&nbsp;<img src='/public/Manager//exam/images/icon_delete.png' onclick='answer_del(this);'></p>";
            });
            $("ul.contentedit li.answer").html(answerhtml);
            questions.question_key1=$(cur).attr("que_key1");
            questions.question_key2=$(cur).attr("que_key2");
            questions.question_key3=$(cur).attr("que_key3");
            questions.question_key4=$(cur).attr("que_key4");
            $("ul.contentedit .pro").val(questions.question_key1);
            if(questions.question_key1==''||questions.question_key1==undefined||questions.question_key1==null||questions.question_key1=="null"){
                $("ul.contentedit .pro").val(21);
            }
            $("ul.contentedit .topic").val(questions.question_key2);
            $("ul.contentedit .skill").val(questions.question_key3);
            if(questions.question_key3==''||questions.question_key3==undefined||questions.question_key3==null||questions.question_key3=="null"){
                $("ul.contentedit .skill").val(81);
            }
            $("ul.contentedit .diff").val(questions.question_key4);
            if(questions.question_key4==''||questions.question_key4==undefined||questions.question_key4==null||questions.question_key4=="null"){
                $("ul.contentedit .diff").val(85);
            }
            $("ul.contentedit .pro").find("input[name='layout']").val(questions.stemtype);
            break;
        case "5":
            questions.points=$(cur).attr("points");
            questions.tips=$(cur).attr("tips");
            questions.stoptime=$(cur).attr("stoptime");
            questions.playtimes=$(cur).attr("playtimes");
            if(questions.tips==undefined){
               questions.tips=0;
            }
            if(questions.stoptime==undefined){
                questions.stoptime=5;
            }
            if(questions.playtimes==undefined){
                questions.playtimes=2;
            }

            $(li).eq(0).find("input:eq(0)").val(questions.points);
            $(li).eq(2).find("select").val(questions.tips);
            $(li).eq(3).find("select").val(questions.stoptime);
            $(li).eq(4).find("select").val(questions.playtimes);

            break;
        case "6":
            questions.stoptime=$(cur).attr("stoptime");
            if(questions.stoptime==undefined){
                questions.stoptime=5;
            }
            $(li).eq(1).find("select").val(questions.stoptime);     
            break;
        default:
            
            return;
    }
}

//小题的复制
function question_copy(obj){
    //去掉当前中div
    $(obj).attr("id","undefined");
    $(obj).attr("question_num_id","undefined");
    $(obj).find(".edit").attr("parentid","undefined");
    $(obj).find(".delete").attr("id","undefined");
    var question_num=$(obj).attr("question_num");

    //音频的id
    $(obj).find(".content_tts").find("p").each(function(key,value){
       $(value).attr("id","undefined");
    });
    //去掉选项的id
    $(obj).find(".content_options").find("p").each(function(key,value){
       $(value).attr("id","undefined");
    });
    $(obj).find(".content_options").find("p").each(function(key,value){
       $(value).find("input:eq(0)").attr("id","undefined");
    });
    $(obj).find(".content_options").find("p").each(function(key,value){
       $(value).find("strong:eq(0)").attr("id","undefined");
    });
    //去掉答案的id
    $(obj).find(".answer").attr("id","undefined");
    $(obj).find(".answer").find("span").each(function(key,value){
       $(value).attr("id","undefined");
    });
    $(obj).find("label").find(".tihao").html($(obj).find("label").find(".tihao").text()+"副本");
    
}

//音频的添加和删除
function voice_add(obj){
  $("#item_tts_auto_edit_template .tt").clone().insertAfter($(obj).parent());
}
function voice_del(obj){
    var id=$(obj).parent().find("input:eq(0)").attr("id");
    //alert(id);
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
    var id=$(obj).parents("p").find("input:eq(0)").attr("id");
    $.getJSON("delItem",{id:id,ran:Math.random()});
    $(obj).parent().remove();
}
//连线题的添加和删除
function line_add(obj){
    $("#qunstion_sort_edit_template .itmes p:eq(0)").clone().insertAfter($(obj).parent());
    $(obj).parent().parent().find("input[name='questype']:checked").click();
}
function line_del(obj){
   var id=$(obj).parent().attr("id");
   $.getJSON("delItem",{id:id,ran:Math.random()});
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
      $(objs).parent().find("img:eq(0)").attr("id","upload_mp3");
      var obj=$("#file_upload").clone();
      $(obj).removeClass("template");
      $(obj).addClass("curfile");
      $(obj).attr("id","file_uploads");
      $(obj).find("form").attr("id","form");
      $(obj).insertAfter($(objs).parent());
}
 //试听
function splay(obj){
    var mp3="";
      //将听力的音频内容取出来
      var issys=$('#form_header input[name="tts_type"]:checked').val();
      //alert(issys);
      if(issys==1){
          //系统生成
          var audiocontent=$(obj).parent().find("textarea").val();
          if(audiocontent==""){alert("请输入音频内容!");return;}
          var voiceid=$(obj).parent().find("select").val();
          mp3=play(audiocontent,voiceid);
          if(mp3==""){alert("音频生成忙碌请稍后再试!");return;}
          AudioPerform("http://192.168.151.206/voicetemp/"+mp3);
    }else{
          mp3=$(obj).attr("mp3");
          if(mp3==""||mp3==undefined){alert("请上传音频!");return;}
          AudioPerform("../../uploads/"+mp3);
    } 
}


//听力试听
function play(content,voiceid) {
    var result="";
    $.post("../Exams/ting",{ran:Math.random(),content:content,voiceid:voiceid},function(data){
      result=data.msg;
   });
    return result;

}
function AudioPerform(audiopath) {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/msie ([\d.]+)/)) {
        jQuery('#jplayer').html('<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"><param name="AutoStart" value="1" /><param name="Src" value="' + audiopath + '" /></object>');
    }
    else if (ua.match(/firefox\/([\d.]+)/)) {
        //            jQuery('#alert_sound').html('<embed src="' + audiopath + '" type="audio/wav" hidden="true" loop="false" mastersound></embed>');
        jQuery('#jplayer').html('<audio autoplay="autoplay"><source src="' + audiopath + '" type="audio/wav"/><source src="$!{TempletPath}images/ring.wav" type="audio/mpeg"/></audio>');
    }
    else if (ua.match(/chrome\/([\d.]+)/)) {
        jQuery('#jplayer').html('<audio src="' + audiopath + '" type="audio/wav" autoplay=”autoplay” hidden="true"></audio>');
    }
    else if (ua.match(/opera.([\d.]+)/)) {
        jQuery('#jplayer').html('<embed src="' + audiopath + '" hidden="true" loop="false"><noembed><bgsounds src=' + audiopath + '></noembed>');
    }
    else if (ua.match(/version\/([\d.]+).*safari/)) {
        jQuery('#jplayer').html('<audio src="' + audiopath + '" type="audio/wav" autoplay=”autoplay” hidden="true"></audio>');
    }
    else {
        jQuery('#jplayer').html('<embed src="' + audiopath + '" type="audio/wav" hidden="true" loop="false" mastersound></embed>');
    }
}


//排序题选项的添加和删除
//试卷保存
function savepaper(){
    var flag=0;
    var ex=[];
    var exam={};
    //考试名称
    var examsid=$("#btn_exam_save").attr("examsid");
    if(examsid=="0"||examsid==""||examsid==undefined){
        examsid=0;
    }
    exam.examsid=examsid;
    var examname=$("#form_header").find("input[name='exam_name']").val();
    if(examname==""||examname==undefined){
        alert("您的试卷名称不能为空");
        return;

    }
    exam.examname=examname;
    //应用类型
    var exams_classid=$("#form_header").find("input[name='exam_type']:checked").val();
    exam.exams_classid=exams_classid;
    //获取单元信息
    var unitid=$("#unit").attr("unitid");
    if(unitid=="0"||unitid==""||unitid==undefined){
        unitid="0";
    }
    if(exam.exams_classid=="1"||exam.exams_classid=="2"){
        if(unitid=="0"){
            alert("请选择单元");
            return;
        }
    }
    //获取真题省份信息
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
    if(exam.exams_classid=="3"){
        if(proid=="0"){
            alert("请选择省份");
            return;
        }
        if(yearid=="0"){
            alert("请选择年份");
            return;
        }
        if(levelid=="0"){
            alert("请选择学段");
            return;
        }
        if(typeid=="0"){
            alert("请选择类型");
            return;
        }
    }

    exam.unitid=unitid;
    exam.proid=proid;
    exam.yearid=yearid;
    exam.levelid=levelid;
    exam.typeid=typeid;
    //音频处理
    var ttstype=$("#form_header").find("input[name='tts_type']:checked").val();
    exam.ttstype=ttstype;
    
    //显示内容
    var header_content=$("#form_header").find("p:eq(5)").find("textarea[name='textarea']").val();
    exam.header_content=header_content;
    //停顿设置
    var stopsecond=$("#form_header").find("p:eq(8)").find("select").val();
    if(stopsecond==undefined){
        if(header_content!=""){
            alert("请选择停顿时间");
            return;
        } 
    }
    exam.stopsecond=stopsecond;
    //这里值得商榷  因为用户要是切换了 那么之前的就是全部没有了
    var issys=$('#form_header input[name="tts_type"]:checked').val();
    //试卷开始的音频
    if(issys==1){
        //自动生成试卷开始音
        var exam_tts=[];
        var tts={};
        var exambegin=$("#form_header").find("p:eq(6)").find("textarea[name='textarea']").val();
        var voiceid=$("#form_header").find("p:eq(6)").find("select:eq(0)").val();
        var id=$("#form_header").find("p:eq(6)").find("img").attr("id");
        if(id==undefined){
            id=0;
        }
        if(exambegin!=""){
            if(voiceid==undefined){
                alert("试卷的开始音没有选择发音类型");
                return;
            }
        }
        tts.exambegin=exambegin;
        tts.voiceid=voiceid;
        tts.mp3="";
        tts.flag="";;
        tts.id=id;
        exam_tts.push(tts);
        //自动生成试卷结束音
        tts={};
        var examend=$("#form_header").find("p:eq(9)").find("textarea[name='textarea']").val();
        var endvoiceid=$("#form_header").find("p:eq(9)").find("select:eq(0)").val();
        var endid=$("#form_header").find("p:eq(9)").find("img").attr("id");
        if(id==undefined){
            id=0;
        }
        if(examend!=""){
            if(endvoiceid==undefined){
                alert("试卷的结束音没有选择发音类型");
                return;
            }
        }
        tts.exambegin=examend;
        tts.voiceid=endvoiceid;
        tts.mp3="";
        tts.flag=1;
        tts.id=endid;
        exam_tts.push(tts);
        exam.exam_tts=exam_tts;
    }else{
        //手动上传试卷开始音
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
        exam_upload.push(upload);
        //手动上传试卷结束音
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

    }console.log(exam.exam_tts);
    
    var paper=[];
    $("#form_exam .stem_parent").each(function(k,v){
        //表示是几道大题
        var quekey=k+1;
        //独立大题
        //独立答题的答题内容
        var id=$(v).attr("id");
        if(id==undefined){
            id=0;
        }
        //分数
        var points=$(v).attr("points");
        //停顿时间
        var stoptime=$(v).attr("stoptime");
        //是否有叮咚
        var tips=$(v).attr("tips");
        //所属小题播放次数
        var playtimes=$(v).attr("playtimes");
        if(points==undefined){
            var stem_type=$(v).hasClass("stem_single")?1:2;
            if(stem_type==1){
                alert("试卷中的第"+quekey+"道大题没有设置分数");
                flag=1;
                return;
            }
        }
        if(stoptime==undefined){
            alert("试卷中的第"+quekey+"道大题没有设置停顿时间");
            flag=1;
            return;
        }
        if(tips==undefined){
            var stem_type=$(v).hasClass("stem_single")?1:2;
            if(stem_type==1){
               alert("试卷中的第"+quekey+"道大题没有设置叮咚");
               flag=1;
               return; 
            }
        }
        if(playtimes==undefined){
            var stem_type=$(v).hasClass("stem_single")?1:2;
            if(stem_type==1){
                alert("试卷中的第"+quekey+"道大题没有设置播放次数");
                flag=1;
                return;
           }
        }
        //取出大题的音频内容
        var single=$(v).find("div.content_tts:eq(0)");
        var paper_temp={};
        var tts_content=[];
        //单道大题的音频
        if($(single).find("p").length>0){
            $(single).find("p").each(function(key,value){
               //取出前面的发音的人
               var tts_content_temp={};
               //alert($(value).html());
               var id=$(value).attr("id");
               if(issys==1){
                if(id==undefined){
                    id=0;
                }
                tts_content_temp.id=id;
                tts_content_temp.sortid=key+1;
                tts_content_temp.voiceid=$(value).attr("voicetype");
                tts_content_temp.ttstype=$(value).attr("ttstype");
                tts_content_temp.stflag=$(value).attr("stflag");
                tts_content_temp.stoptime=$(value).attr("stoptime");
                tts_content_temp.voiceflag=$(value).find("strong").text();
                tts_content_temp.content=$(value).find("font").text();
                tts_content_temp.mp3="";
               }else{
                if(id==undefined){
                    id=0;
                }
                tts_content_temp.id=id;
                tts_content_temp.sortid=key+1;
                tts_content_temp.voiceid="";
                tts_content_temp.stflag=$(value).attr("stflag");
                tts_content_temp.voiceflag=$(value).find("strong").text();
                tts_content_temp.ttstype=$(value).attr("ttstype");
                tts_content_temp.stoptime=$(value).attr("stoptime");
                tts_content_temp.content=$(value).find("font").html();
                tts_content_temp.mp3=$(value).find("font").attr("mp3");
               }
               tts_content.push(tts_content_temp);
            });
        }
        paper_temp.id=id;
        paper_temp.sortid=k+1;
        paper_temp.tts=tts_content;
        paper_temp.points=points;
        paper_temp.stoptime=stoptime;
        paper_temp.playtimes=playtimes;
        paper_temp.tips=tips;
        paper_temp.content=$(v).find("div.content_view:eq(0)").html();
        //为了处理json数据 需要将双引号替换成单引号
        paper_temp.content=paper_temp.content.replace(/"/g,"'");
        var stem_type=$(v).hasClass("stem_single");
        paper_temp.stem_type=stem_type?1:2;
        if(stem_type==1){
            //非组合体情况
            var questions_lists=[];
            //当下面有问题的时候进行数据的组合
            if($(v).find(".question_item").length>0){
                $(v).find(".question_item").each(function(k1,v1){
                    //问题的音频
                    var question=$(v1).find("div.content_tts");
                    var questions={};
                    var tts_question=[];
                    //问题的音频
                    if($(question).find("p").length>0){
                        $(question).find("p").each(function(key,value){
                           //取出前面的发音的人
                           var tts_content_temp={};
                           if(issys=="1"){
                             tts_content_temp.id=$(value).attr("id");
                             if(tts_content_temp.id==undefined){
                                tts_content_temp.id=0;
                             }
                             tts_content_temp.stflag=$(value).attr("stflag");
                             tts_content_temp.ttstype=$(value).attr("ttstype");
                             tts_content_temp.voiceid=$(value).attr("voicetype");
                             tts_content_temp.stoptime=$(value).attr("stoptime");
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
                             tts_content_temp.stflag=$(value).attr("stflag");
                             tts_content_temp.stoptime=$(value).attr("stoptime");
                             tts_content_temp.ttstype=$(value).attr("ttstype");
                             tts_content_temp.voiceflag=$(value).find("strong").text();
                             tts_content_temp.content=$(value).find("font").html();
                             tts_content_temp.mp3=$(value).find("font").attr("mp3");
                           }
                           tts_question.push(tts_content_temp);
                        });
                    }
                    questions.tts=tts_question;
                    questions.content=$(v1).find("div.content_view:eq(0)").html();
                    questions.content=questions.content.replace(/"/g,"'");
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
                        alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题为设置停顿时间");
                        flag=1;
                        return;
                        questions.question_stoptime=0;
                    }
                    questions.tips=$(v1).attr("tips");
                    if(questions.tips==undefined){
                        alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题未设置是否有叮咚");
                        flag=1;
                        return false;
                        questions.tips=0;
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
                    questions.question_num_id=$(v1).attr("question_num_id");
                    if($(v1).attr("question_num_id")==undefined){
                        questions.question_num_id=0;
                    }
                    questions.que_key1=$(v1).attr("que_key1");
                    if($(v1).attr("que_key1")==undefined){
                        questions.que_key1=0;
                    }
                    questions.que_key2=$(v1).attr("que_key2");
                    if($(v1).attr("que_key2")==undefined){
                        questions.que_key2=0;
                    }
                    questions.que_key3=$(v1).attr("que_key3");
                    if($(v1).attr("que_key3")==undefined){
                        questions.que_key3=0;
                    }
                    questions.que_key4=$(v1).attr("que_key4");
                    if($(v1).attr("que_key4")==undefined){
                        questions.que_key4=0;
                    }
                    questions.stemdisplay=$(v1).attr("stemtype");
                    questions.sortid=k1+1;
                    //取出选项和答案
                    var questype=$(v1).attr("data-field-type");
                    //alert(questype);
                    //判断题型是什么题型
                    if(questype=='qunstion_select'){
                        questions.typeid=1;
                        var item=[];
                        var answer=[];
                        $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                          var item_temp={};
                          var item_answer={};
                          item_temp.flag=$(vi).find("input").val();
                          if($(vi).find("input").is(":checked")){
                            if(questions.itemtype=="0"){
                                item_answer.content=$(vi).text().substr(2);
                            }else if(questions.itemtype=="1"){
                                item_answer.content=$(vi).find("img").attr("src").substr(14);
                            }
                            
                            item_answer.sortid=ki+1;
                            item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                            if(item_answer.id==undefined){
                                item_answer.id=0;
                            }
                            if(item_answer.content==""){
                                alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题没有答案");
                                flag=1;
                                return;
                            }
                            answer.push(item_answer);
                          }
                          if(questions.itemtype=="0"){
                            item_temp.content=$(vi).text().substr(2);
                          }else if(questions.itemtype=="1"){
                            item_temp.content=$(vi).find("img").attr("src").substr(14);
                          }else{
                            alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题没有图片");
                            flag=1;return;
                          }
                          item_temp.id=$(vi).find("input").attr("id");
                          item_temp.sortid=ki+1;
                          if(item_temp.id==undefined){
                            item_temp.id=0;
                          }
                          item.push(item_temp);
                       });
                       questions.items=item;
                       questions.answer=answer;
                    }else if(questype=='qunstion_judge'){
                        questions.typeid=3;
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
                        questions.typeid=2;
                        var answer=[];
                        var item=[];
                        $(v1).find("div.answer:eq(0)").find("span").each(function(ki,vi){
                           var answer_temp={};
                           answer_temp.id=$(vi).attr("id");
                           if(answer_temp.id==undefined){
                            answer_temp.id=0;
                           }
                           answer_temp.content=$(vi).text().substr(2);
                           answer_temp.sortid=ki+1;
                           answer.push(answer_temp);
                        });
                        if(answer.length==0){
                            alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题没有答案");flag=1;return;
                        }
                        questions.items=item;
                        questions.answer=answer;

                    }else if(questype=='qunstion_sort'){
                        questions.typeid=4;
                        var answer=[];
                        var item=[];
                        $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                          var item_temp={};
                          if(questions.itemtype=="0"){
                            item_temp.flag=$(vi).find("strong").text();
                            item_temp.content=$(vi).find("font").text();
                            alert(item_temp.content);
                            item_temp.id=$(vi).find("strong").attr("id");
                            if(item_temp.id==undefined){
                                item_temp.id=0;
                            }
                            item_temp.sortid=ki+1;
                            item.push(item_temp);
                          }else{
                            item_temp.flag=$(vi).find("strong").text();
                            item_temp.content=$(vi).find("img").attr("src").substr(14);
                            item_temp.id=$(vi).find("strong").attr("id");
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
                           answer_temp.answernum=$(vi).find("font:eq(0)").text();
                           answer_temp.content=$(vi).find("font:eq(1)").text();
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
            }
            paper_temp.questions_lists=questions_lists;
            paper_temp.son=[];
            
        }else{
            //组合答题的情况
            paper_temp.questions_lists=[];
            var son=[];
            if($(v).find(".stem_son").length>0){
                $(v).find(".stem_son").each(function(ks,vs){
                    if($(vs).hasClass("emputyfiled")){
                        return ; 
                    }
                    var son_temp={};
                    var pointsc=$(vs).attr("points");
                    if($(vs).attr("points")==undefined){
                        pointsc==0;
                    }
                    var stoptimesc=$(vs).attr("stoptime");
                    if($(vs).attr("stoptime")==undefined){
                        alert("试卷中的第"+quekey+"道大题中的第"+(ks+1)+"道组合部分没有停顿时间");flag=1;return;
                        stoptimesc==0;
                    }
                    var playtimesc=$(vs).attr("playtimes");
                    if($(vs).attr("playtimes")==undefined){
                        alert("试卷中的第"+quekey+"道大题中的第"+(ks+1)+"道组合部分没有播放次数");flag=1;return;
                        playtimesc==0;
                    }
                    var tipsc=$(vs).attr("tips");
                    if($(vs).attr("tips")==undefined){
                        tipsc==0;
                    }
                    // var singlec=$(vs).find("div.content_tts:eq(0)").text();
                    // singlec=singlec.replace(/"/g,"'");
                    var id=$(vs).attr("id");
                    if(id==undefined){
                        id=0;
                    }
                    //组合部分的听力材料
                    var sontts=$(vs).find("div.content_tts:eq(0)");
                    var tts_content=[];
                    if($(sontts).find("p").length>0){
                        $(sontts).find("p").each(function(key,value){
                       //取出前面的发音的人
                       var tts_content_temp={};
                       if(issys=="1"){
                          tts_content_temp.id=$(value).attr("id");
                          if(tts_content_temp.id==undefined){
                            tts_content_temp.id=0;
                          }
                          tts_content_temp.stflag=$(value).attr("stflag");
                          tts_content_temp.voiceid=$(value).attr("voicetype");
                          tts_content_temp.ttstype=$(value).attr("ttstype");
                          tts_content_temp.stoptime=$(value).attr("stoptime");
                          tts_content_temp.voiceflag=$(value).find("strong").text();
                          tts_content_temp.content=$(value).find("font").text();
                          tts_content_temp.sortid=key+1;
                          tts_content_temp.mp3="";
                       }else{
                          tts_content_temp.stflag=$(value).attr("stflag");
                          tts_content_temp.ttstype=$(value).attr("ttstype");
                          tts_content_temp.id=$(value).attr("id");
                          if(tts_content_temp.id==undefined){
                            tts_content_temp.id=0;
                          }
                          tts_content_temp.voiceid="";
                          tts_content_temp.sortid=key+1;
                          tts_content_temp.voiceflag=$(value).find("strong").text();
                          tts_content_temp.stoptime=$(value).attr("stoptime");
                          tts_content_temp.content=$(value).find("font").html();
                          tts_content_temp.sortid=key+1;
                          tts_content_temp.mp3=$(value).find("font").attr("mp3");;
                       }
                       tts_content.push(tts_content_temp);
                    });

                    }
                    son_temp.id=id;
                    son_temp.content=$(vs).find("div.content_view:eq(0)").html();
                    son_temp.content=son_temp.content.replace(/"/g,"'");
                    son_temp.tts=tts_content;
                    son_temp.points=pointsc;
                    son_temp.sortid=ks+1;
                    son_temp.stoptime=stoptimesc;
                    son_temp.playtimes=playtimesc;
                    son_temp.tips=tipsc;
                    son_temp.stem_type=2;
                    //组合部分的试题列表
                    var son_questionlists=[];
                    if($(vs).find(".question_item").length>0){
                        $(vs).find(".question_item").each(function(k1,v1){
                        var question=$(v1).find("div.content_tts");
                        var questions={};
                        var tts_question=[];
                        if($(question).find("p").length>0){
                            $(question).find("p").each(function(key,value){
                               //取出前面的发音的人
                               var tts_content_temp={};
                               if(issys=="1"){
                                 tts_content_temp.id=$(value).attr("id");
                                 if(tts_content_temp.id==undefined){
                                    tts_content_temp.id=0;
                                 }
                                 tts_content_temp.stflag=$(value).attr("stflag");
                                 tts_content_temp.voiceid=$(value).attr("voicetype");
                                 tts_content_temp.stoptime=$(value).attr("stoptime");
                                 tts_content_temp.ttstype=$(value).attr("ttstype");
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
                                 tts_content_temp.stflag=$(value).attr("stflag");
                                 tts_content_temp.ttstype=$(value).attr("ttstype");
                                 tts_content_temp.stoptime=$(value).attr("stoptime");
                                 tts_content_temp.sortid=key+1;
                                 tts_content_temp.voiceflag=$(value).find("strong").text();
                                 tts_content_temp.content=$(value).find("font").html();
                                 tts_content_temp.mp3=$(value).find("font").attr("mp3");
                               }
                               
                               tts_question.push(tts_content_temp);
                            });
                        }
                        questions.tts=tts_question;
                        questions.content=$(v1).find("div.content_view:eq(0)").html();
                        //取出题号以及间隔时间以及提示音
                        questions.question_num=$(v1).attr("question_num");
                        if(questions.question_num==undefined){
                            questions.question_num=0;
                        }
                        questions.tips=$(v1).attr("tips");
                        if(questions.tips==undefined){
                            questions.tips=0;
                        }
                        questions.typeid=$(v1).attr("typeid");
                        questions.question_num_voice=$(v1).attr("question_num_voice");
                        if(questions.question_num_voice==undefined){
                            questions.question_num_voice=0;
                        }
                        questions.question_stoptime=$(v1).attr("question_stoptime");
                        if(questions.question_stoptime==undefined){
                            alert("试卷中的第"+quekey+"道大题中的第"+(ks+1)+"道组合部分中的第"+(k1+1)+"道小题没有停顿时间");flag=1;return;
                            questions.question_stoptime=0;
                        }
                        questions.question_note=$(v1).attr("question_note");
                        if(questions.question_note==undefined){
                            questions.question_note=0;
                        }
                        questions.question_num_id=$(v1).attr("question_num_id");
                        if($(v1).attr("question_num_id")==undefined){
                            questions.question_num_id=0;
                        }
                        questions.id=$(v1).attr("id");
                        if($(v1).attr("id")==undefined){
                            questions.id=0;
                        }
                        questions.itemtype=$(v1).attr("itemtype");
                        if($(v1).attr("itemtype")==undefined){
                            questions.itemtype=1;
                        }
                        questions.que_key1=$(v1).attr("que_key1");
                        if($(v1).attr("que_key1")==undefined){
                            questions.que_key1=0;
                        }
                        questions.que_key2=$(v1).attr("que_key2");
                        if($(v1).attr("que_key2")==undefined){
                            questions.que_key2=0;
                        }
                        questions.que_key3=$(v1).attr("que_key3");
                        if($(v1).attr("que_key3")==undefined){
                            questions.que_key3=0;
                        }
                        questions.que_key4=$(v1).attr("que_key4");
                        if($(v1).attr("que_key4")==undefined){
                            questions.que_key4=0;
                        }
                        questions.stemdisplay=$(v1).attr("stemtype");
                        questions.sortid=k1+1;
                        //取出选项和答案
                        var questype=$(v1).find("div.content_options:eq(0)").parent().attr("data-field-type");
                        //判断题型是什么题型
                        if(questype=='qunstion_select'){
                            questions.typeid=1;
                            var item=[];
                            var answer=[];
                            //进行判断题型的判断
                            $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                              var item_temp={};
                              var item_answer={};
                              item_temp.flag=$(vi).find("input").val();
                              if($(vi).find("input").is(":checked")){
                                if(questions.itemtype=="0"){
                                    item_answer.content=$(vi).text().substr(2);
                                }else if(questions.itemtype=="1"){
                                    item_answer.content=$(vi).find("img").attr("src").substr(14);
                                }else{
                                    alert("没有答案");
                                    flag=1;return;
                                }
                                //item_answer.content=$(vi).text().substr(2);
                                item_answer.sortid=ki+1;
                                item_answer.id=$(v1).find("div.answer:eq(0)").attr("id");
                                if(item_answer.id==undefined){
                                    item_answer.id=0;
                                }
                                answer.push(item_answer);
                              }
                              if(questions.itemtype=="0"){
                                item_temp.content=$(vi).text().substr(2);
                              }else if(questions.itemtype=="1"){
                                item_temp.content=$(vi).find("img").attr("src").substr(14);
                              }else{
                                alert("试卷中的第"+quekey+"道大题中的第"+(k1+1)+"道小题没有图片");flag=1;return;
                              }
                              //item_temp.content=$(vi).text().substr(2);
                              item_temp.id=$(vi).find("input").attr("id");
                              item_temp.sortid=ki+1;
                              if(item_temp.id==undefined){
                                item_temp.id=0;
                              }
                              item.push(item_temp);
                           });
                           questions.items=item;
                           questions.answer=answer;
                        }else if(questype=='qunstion_judge'){
                            questions.typeid=3;
                            var answer=[];
                            var item=[];
                            $(v1).find("div.content_options p").each(function(ki,vi){
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
                            questions.typeid=2;
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
                            questions.typeid=4;
                            var answer=[];
                            var item=[];
                            $(v1).find("div.content_options:eq(0)").find("p").each(function(ki,vi){
                              var item_temp={};
                              if(questions.itemtype=="0"){
                                item_temp.flag=$(vi).find("strong").text();
                                item_temp.content=$(vi).find("font").text();
                                item_temp.id=$(vi).find("strong").attr("id");
                                if(item_temp.id==undefined){
                                    item_temp.id=0;
                                }
                                item_temp.sortid=ki+1;
                                item.push(item_temp);
                              }else{
                                item_temp.flag=$(vi).find("strong").text();
                                item_temp.content=$(vi).find("img").attr("src").substr(14);
                                item_temp.id=$(vi).find("strong").attr("id");
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
                               answer_temp.answernum=$(vi).find("font:eq(0)").text();
                               answer_temp.content=$(vi).find("font:eq(1)").text();
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

                    }
                    son_temp.questions_lists=son_questionlists;
                    son.push(son_temp);
                });
                paper_temp.son=son;
            }
        }
        paper.push(paper_temp);    
    });
    exam.paper=paper;
    ex.push(exam);
    if(flag==1)return false;
    $.post("savePaper",{data:encodeURI(JSON.stringify(ex)),ran:Math.random()},function(d){
        window.location.reload();
    }); 
}











