//Dom元素初始化
var initDom=function(dom,attrarr){
    var dom=$(dom);
    $.each(attrarr,function(key,value){
        dom.attr(value.id,value.val);
    });
    return dom;
}

//刷新试题列表
function getQuestionsList(levelid){
	$("#queslist").empty();
	$.getJSON("getQuestionsList",{levelid:levelid,ran:Math.random()},function(data){
            if(data=='null'||data==null){
		$("#loading").modal("hide");
                  return false;
            }
            console.log(data);
		$.each(data,function(key,value){
		var attrarr=[];
            var temp={};
            attrarr.push(temp);
            var tr =initDom("<tr></tr>",attrarr);
            tr.appendTo("#queslist")
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="line-height: 100%;text-align: center;vertical-align: middle;width: 100px;";
            attrarr.push(temp);
            var td =initDom("<td></td>",attrarr);
            td.text(value.word);
            td.appendTo(tr);

            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="line-height: 100%;text-align:center;vertical-align: middle;width: 300px;";
            attrarr.push(temp);
            td =initDom("<td></td>",attrarr);
            td.appendTo(tr);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="width:40%;display: inline;";
            attrarr.push(temp);
            temp={};
            temp.id="class";
            temp.val="form-control wordexplain";
            attrarr.push(temp);
            var select =initDom("<select></select>",attrarr);
            select.appendTo(td);
            $.getJSON("getExplainListByWordid",{wordid:value.wordid,ran:Math.random()},function(qdata){
                  $.each(qdata,function(qk,qv){
                        attrarr=[];
                        temp={};
                        temp.id="value";
                        temp.val=qv.id;
                        attrarr.push(temp);
                        var option =initDom("<option></option>",attrarr);
                        option.text(qv.explains);
                        option.appendTo(select);
                  })
            });
            select.val(value.explainid);

            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="text-align:center;width: 300px;";
            attrarr.push(temp);
            td =initDom("<td></td>",attrarr);
            td.appendTo(tr);
            if(value.questype==1||value.questype==0){
            	attrarr=[];
	            temp={};
	            temp.id="class";
	            temp.val="glyphicon glyphicon-headphones";
	            attrarr.push(temp);
                  temp={};
                  temp.id="mp3";
                  temp.val=value.mp3;
                  attrarr.push(temp);
	            var span =initDom("<span></span>",attrarr);
	            //span.html(value.tncontent);
	            span.appendTo(td);
            }else{
            	attrarr=[];
	            temp={};
	            temp.id="class";
	            temp.val="tncontent";
	            attrarr.push(temp);
	            var span =initDom("<span></span>",attrarr);
	            span.html(value.tncontent);
	            span.appendTo(td);
            }
            
            $.each(value.items,function(ik,iv){
            	attrarr=[];
	            temp={};
	            temp.id="class";
                  if(typeof value.answer=='string'){
                        if(iv.flag==value.answer){
                              temp.val="choice answer";
                        }else{
                              temp.val="choice";
                        }
                  }else{
                       if(value.answer.indexOf(iv.content)>=0){
                              temp.val="choice answer";
                        }else{
                              temp.val="choice";
                        } 
                  }
	            
	            attrarr.push(temp);
	            span =initDom("<span></span>",attrarr);
                  if(value.questype==4||value.questype==5){
                      span.html(iv.flag+".<img width='40px' height='40px' src='"+iv.content+"'/>");  
                  }else{
                      span.html(iv.flag+"."+iv.content);  
                  }
	            span.appendTo(td);
            });
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="tncontent";
            attrarr.push(temp);
            span =initDom("<span></span>",attrarr);
            span.html(value.analysis);

            

            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="line-height: 100%;text-align:center;vertical-align: middle;width: 350px;";
            attrarr.push(temp);
            td =initDom("<td></td>",attrarr);
            td.appendTo(tr);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="width:40%;display: inline;";
            attrarr.push(temp);
            temp={};
            temp.id="class";
            temp.val="form-control questype";
            attrarr.push(temp);
            var select =initDom("<select></select>",attrarr);
            select.appendTo(td);
            $.getJSON("getQuestypeList",{wordid:value.wordid,explainid:value.explainid,ran:Math.random()},function(qdata){
            	$.each(qdata,function(qk,qv){
            		attrarr=[];
		            temp={};
		            temp.id="value";
		            temp.val=qv.cindex;
		            attrarr.push(temp);
		            var option =initDom("<option></option>",attrarr);
		            option.text(qv.remark);
		            option.appendTo(select);
            	})
            });
            select.val(value.questype);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="line-height: 100%;text-align: center;vertical-align: middle;width: 300px;";
            attrarr.push(temp);
            td =initDom("<td></td>",attrarr);
            td.appendTo(tr);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="width:40%;display: inline;";
            attrarr.push(temp);
            temp={};
            temp.id="class";
            temp.val="form-control level";
            attrarr.push(temp);
            var select =initDom("<select></select>",attrarr);
            select.appendTo(td);
            //获取关卡
            $.getJSON("getLevelList",{gameid:gameid,ran:Math.random()},function(ldata){
            	$.each(ldata,function(lk,lv){
            		attrarr=[];
		            temp={};
		            temp.id="value";
		            temp.val=lv.id;
		            attrarr.push(temp);
		            var option =initDom("<option></option>",attrarr);
		            option.text(lv.name);
		            option.appendTo(select);
            	})
            });
            select.val(levelid);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="line-height: 100%;text-align: center;vertical-align: middle;";
            attrarr.push(temp);
            td =initDom("<td></td>",attrarr);
            if(value.questype==9||value.questype==10){
                  td.html('<span class="glyphicon glyphicon-edit" style="margin-left: 10px;" bid="'+value.wordid+'"  title="编辑"></span></span><span class="glyphicon glyphicon-remove" style="margin-left: 10px;" bid="'+value.wordid+'"  title="删除"></span>');
                  td.appendTo(tr);
            }else{
                 td.html('<span class="glyphicon glyphicon-refresh" style="margin-left: 10px;" bid="'+value.wordid+'" title="重新出题" ></span><span class="glyphicon glyphicon-edit" style="margin-left: 10px;" bid="'+value.wordid+'"  title="编辑"></span></span><span class="glyphicon glyphicon-remove" style="margin-left: 10px;" bid="'+value.wordid+'"  title="删除"></span>');
                  td.appendTo(tr);
            }
         });
            
	})
}

//获取用户刷新试题
function refreshQuestion(){
	$.getJSON("refreshQuestion",{word:word,questype:questype,ran:Math.random()},function(){

	})
}

//获取单题内容
function getQuestionById(){
	$.getJSON("getQuestionById",{id:id,ran:Math.random()},function(){

	})
}

//获取单题内容
function saveQuestions(){
	$.getJSON("getQuestionById",{id:id,ran:Math.random()},function(){

	})
}

//获取单题内容
function editQuestion(){
	$.getJSON("getQuestionById",{id:id,word:word,question:question,questype:questype,levelid,levelid,ran:Math.random()},function(){

	})
}


//删除试题
function delQuestion(){
	$.getJSON("delQuestion",{id:id,ran:Math.random()},function(){

	})
}


//添加关卡
function editLevel(){
	$.getJSON("editLevel",{id:id,content:content,ran:Math.random()},function(){

	})
}

//获取关卡列表
function getLevelsList(gameid){
	$("#levellists").empty();
	$.getJSON("getLevelList",{gameid:gameid,ran:Math.random()},function(data){
		$("#levellist").tmpl(data).appendTo('#levellists');
	})
}


//更改关卡
function changeLevel(){
	$.getJSON("changeLevel",{id:id,levelid,levelid,ran:Math.random()},function(){

	})
}




