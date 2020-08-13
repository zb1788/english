/*获取不同试题的答案*/
$.EXAMSQUESTIONS={
	getChoice:function(items,answer,itemtype){
		var ans="";
		var ansid="";
		$.each(answer,function(key,value){
			ans=value.answer;
			ansid=value.id;
		});
		var content="";
		var itmesflag=['A','B','C'];
		$.each(items,function(key,value){
			var checked="";
			if(value.content==ans){
				if(itemtype==1){
					content+="&nbsp;&nbsp;<input disabled='disabled' name='choice"+value.questionsid+"' type='radio' bid='"+ansid+"' value='"+value.id+"' checked='"+checked+"'>"+value.flag+"."+value.content;
				}else{
					content+="&nbsp;&nbsp;<input disabled='disabled' name='choice"+value.questionsid+"' type='radio' bid='"+ansid+"' value='"+value.id+"' checked='"+checked+"'>"+value.flag+"."+"<img width='60px' height='60px' src='../../uploads/"+value.content+"'></img>";
				}
			}else{
				if(itemtype==1){
					content+="&nbsp;&nbsp;<input disabled='disabled' name='choice"+value.questionsid+"' type='radio' bid='"+ansid+"' value='"+value.id+"'>"+value.flag+"."+value.content;
				}else{
					content+="&nbsp;&nbsp;<input disabled='disabled' name='choice"+value.questionsid+"' type='radio' bid='"+ansid+"' value='"+value.id+"'>"+value.flag+"."+"<img width='60px' height='60px' src='../../uploads/"+value.content+"'></img>";
				}
				
			}
			
		});
		return content;
	},
	getDicide:function(items,answer){
		var ans="";
		var ansid="";
		var content="";
		$.each(answer,function(key,value){
			ans=value.answer;
			ansid=value.id;
		});
		if(ans==1){
			content="<input disabled='disabled' type='radio' name='"+ansid+"' value='"+ansid+"' checked='checked'>True&nbsp;&nbsp;<input type='radio' disabled='disabled' name='"+ansid+"' value='"+ansid+"'>False";
		}else{
			content="<input disabled='disabled' type='radio' name='"+ansid+"' value='"+ansid+"' checked='checked'>False&nbsp;&nbsp;<input type='radio' disabled='disabled' name='"+ansid+"' value='"+ansid+"'>True";
		}
		return content;
	},
	getBlank:function(items,answer){
		var content="";
		$.each(answer,function(key,value){
			content+=(key+1)+"."+value.answer+"&nbsp;&nbsp;"
		});
		return content;
	},
	getSort:function(items,answer,itemtype){
		var content="";
		$.each(answer,function(key,value){
			content+=(key+1)+"."+value.answer+"&nbsp;&nbsp;"
		});
		return content;
	}
}

/*试题的list表*/
function getQuestions(fun,paperid,classid,flag){
	var dloading = art.dialog({time:30,title:'加载中……',width:130,height:30,opacity:0.3,lock:true});
	$(".list_table td").parents("tr").remove();
	$.get("../Exam/"+fun,{ran:Math.random(),flag:flag,classid:classid,paperid:paperid},function(data){
		$.each(data,function(key,value){
			var tr=$("#table_demo").children("tbody").children("tr").clone();
			if(value.classid==1){tr.eq(0).children("td").eq(3).find("input").eq(0).hide();}
			tr.eq(0).children("td").eq(0).html(key+1);
			tr.eq(0).children("td").eq(1).find("input").val(key+1);
			var table=tr.eq(0).find("span");
			if(value.tcontent){
				table.eq(0).find(".content").html(value.tcontent);
			}else{
				table.eq(0).find(".content").html("无题干");
			}
			if(value.vcontent){
				table.eq(1).find(".content").html(value.vcontent);
			}else{
				table.eq(1).hide();
			}
			if(value.acontent){
				table.eq(2).find(".content").html(value.acontent);
			}else{
				table.eq(2).find(".content").html("无解析");
			}
			var content="";
			switch(value.examdaid)
			{
			   case '1':
			     content=$.EXAMSQUESTIONS.getChoice(value.items,value.answer,value.itemtype);
			   break;
			   case '2': 
			     content=$.EXAMSQUESTIONS.getBlank(value.items,value.answer);
			   break;
			   case '3':
			     content=$.EXAMSQUESTIONS.getDicide(value.items,value.answer);
			   break;
			   case '4':
			     content=$.EXAMSQUESTIONS.getSort(value.items,value.answer,value.itemtype);
			}
			if(value.answer.length!=0&&content){
			   table.eq(3).find(".content").html(content);
			}else{
			   table.eq(3).hide();
			}
			tr.find('input').attr("bid",value.id);
			tr.find('input').attr("complex",value.complexity);
			tr.appendTo(".list_table");
		});
       $(".tr:odd").css("background", "#F5F8FA");
       dloading.close();
	});
};


//上传图片插件
$(function(){
	$('#file_upload').uploadify({
    'height'    :30,
    'width'     :120,
    'buttonText': '上传图片',  //选择按钮显示的字符
    'multi'     : false,   //是否允许同时选择多个(false一次只允许选中一张图片)
    'method'    : 'post',
    'formData'  : {
      'folder'  : './uploads/exam/items',
      'fileext': 'png'
    },
    'swf'       : '../Public/Manager/js/uploadify/uploadify.swf?ver='+Math.random(),
    'uploader'  : '../Upload/index',
    'fileTypeExts': '*.png;*.jpg', //允许的后缀
    'fileTypeDesc': 'Image Files', //允许的格式，详见文档
     'onUploadSuccess' : function(file, data, response) 
    {
      //上传成功后的触发事件  
      var obj=eval("("+data+")");
      $("#myimg").attr("src",'../../uploads/'+obj.msg.savepath + obj.msg.savename);
	  $("#myimg").attr("bid",obj.msg.savepath + obj.msg.savename);
    }
  });	
});

 //上传按钮弹出框
  $(".ext_btn.pic").live("click",function(){
  	var ch=$(this);
	var img=$(this).parent().find(".img:eq(0)");
	var path="";
	if($(this).attr("bid")!=undefined){path='../../uploads/'+$(this).attr("bid");}
	$("#uploadfile:eq(0)").find("img:eq(0)").attr("src",path);
  	var dialog = art.dialog({
	    content: $("#uploadfile")[0],
	    fixed: true,
	    ok: function(){
			//var iframe=this.iframe.currentWindow;
			var path=$("#myimg").attr("bid");
			$(ch).attr("bid",path);
			$(img).attr("src",'../../uploads/'+path);
		}
    });
  });

  //获取问题的搜索结果分页
  var page_size=5;
  function pagelist(pageCurrent,page_size){
 	var complexity=$("#complexity").val();
	var questype=$("#questype").val();
	var grades=$("input[name='grade']:checked");
	var keyname=$("input[name='keyname']").val();
	var paperid=$("input[name='paperid']").val();
	var classid=$("input[name='classid']").val();
	//定义年级数组
	var gradeids=[];
	if (typeof(grades)!='undefined') {
		$("input[name='grade']:checked").each( function(){
			gradeids.push($(this).val());
		});
	}
	if (typeof(keyname)!="undefined") {keyname="";}	
	$(".list_table td").parents("tr").remove();
	$.get("../Exam/get_questions_list",{r:Math.random(),pageCurrent:pageCurrent,page_size:page_size,paperid:paperid,classid:classid,gradeids:gradeids.join(","),questype:questype,complexity:complexity,keyname:keyname},function(data){
		$.each(data,function(k,v){
			$('.page').html(k);
			$.each(v,function(key,value){
			var tr = $(".list_demo").children("tbody").children("tr").clone();
			if(value.classid==1){tr.eq(0).children("td").eq(4).find("input").eq(0).hide();}
			var td=tr.children("td");
			td.eq(0).html(key+1);
//			var content="";
//				if(value.examdaid=='0'){
//					content="注释";
//				}else if(value.examdaid=='1'){
//					content="选择题";
//				}else if(value.examdaid=='2'){
//					content="填空题";
//				}else if(value.examdaid=='3'){
//					content="判断题";
//				}else if(value.examdaid=='4'){
//					content="排序题";
//				}
//			td.eq(1).html(content);
			td.eq(1).html(value.grade);
			td.eq(2).html(value.keyname);
			
			var content = td.eq(3).find("span");
			if(value.tcontent){
				content.eq(0).find(".content").html(value.tcontent);
			}else{
				content.eq(0).hide();
			}
			if(value.vcontent){
				content.eq(1).find(".content").html(value.vcontent);
			}else{
				content.eq(1).hide();
			}
			if(value.acontent){
				content.eq(2).find(".content").html(value.acontent);
			}else{
				content.eq(2).hide();
			}
			
			//选项和答案显示在一起
			var quescontent="";
			switch(value.examdaid)
			{
			   case '1':
			     quescontent=$.EXAMSQUESTIONS.getChoice(value.items,value.answer,value.itemtype);
			   break;
			   case '2':
			     quescontent=$.EXAMSQUESTIONS.getBlank(value.items,value.answer);
			   break;
			   case '3':
			     quescontent=$.EXAMSQUESTIONS.getDicide(value.items,value.answer);
			   break;
			   case '4':
			     quescontent=$.EXAMSQUESTIONS.getSort(value.items,value.answer,value.itemtype);
			}
			if(value.classid==2){
				content.eq(3).hide();
			}
			if(value.answer.length!=0&&quescontent){
			   content.eq(3).find(".content").html(quescontent);
			}else{
			   content.eq(3).hide();
			}
		
//			if(value.complexity=='1'){
//					content="容易";
//				}else if(value.complexity=='2'){
//					content="适中";
//				}else if(value.complexity=='3'){
//					content="较难";
//				}
//			td.eq(5).html(content);
			td.eq(4).find("input").attr("bid",value.id);
			if(value.paperid!='0'){
				if(value.baseid){
				   td.eq(4).find(".choose").addClass("ext_btn_error");
				   td.eq(4).find(".choose").val("取消");	
				}
			}
			tr.appendTo(".list_table");	
		});
		$("#SelectPages").change(function(){
				 pagelist($("#SelectPages").val());
			});
		})
		
	});

 }

 /*删除试题以及试卷*/
function del(fun,id,pagecur,obj,paperid,classid){
	art.dialog.alert("确定要删除此题吗？",function(){
		$.getJSON("../Exam/"+fun,{ran:Math.random(),id:id},function(){
	        pagelist(pagecur,page_size,paperid,classid);
		});
	});	
}

//单体或者短文问题的修改
function editquestions(id,fun,flag,type,content,paperid,questype,examsid,classid){
	location.href='../Exam/'+fun+'?flag='+flag+'&type='+type+'&id='+id+'&paperid='+paperid+'&ran='+Math.random()+'&questype='+questype+'&examsid='+examsid+'&classid='+classid;
}

//获取小题内容
function getChildQuestionsList(id,flag){
	$(".list_table.childques td").parents("tr").remove();
	$.getJSON("../Exam/getChildQuestionsList",{flag:flag,id:id,ran:Math.random()},function(data){
		$.each(data,function(key,value){
			var tr=$(".table_demo").children("tbody").children("tr").clone();
			tr.children("td").eq(0).html(key+1);
			tr.children("td").eq(1).find("input").val(key+1);
			tr.children("td").eq(1).find("input").attr("bid",value.id);
			var content=tr.children("td").eq(2);
			if(value.examdaid!=4){
				if(value.tcontent){
					content.find(".content").eq(0).html(value.tcontent);
				}else{
					content.find("span").eq(0).hide();
				}
				
			}else{
				var con="";
				var answer="";
				$.each(value.items,function(k,v){
					if(value.itemtype=='1'){
						con+=(v.flag+"."+v.content+"&nbsp;&nbsp;&nbsp;");
					}else{
						con+=(v.flag+".<img width='50px' height='50px' src='../../uploads/"+v.content+"'>&nbsp;&nbsp;&nbsp;");
					}
					
				});
//				$.each(value.answer,function(k,v){
//					answer+=(v.sortid+"."+v.answer+"&nbsp;&nbsp;&nbsp;");
//				});
				content.find(".content").eq(0).html(con);
			}
			//答案
			var quescontent="";
			switch(value.examdaid)
			{
			   case '1':
			     quescontent=$.EXAMSQUESTIONS.getChoice(value.items,value.answer,value.itemtype);
			   break;
			   case '2': 
			     quescontent=$.EXAMSQUESTIONS.getBlank(value.items,value.answer);
			   break;
			   case '3':
			     quescontent=$.EXAMSQUESTIONS.getDicide(value.items,value.answer);
			   break;
			   case '4':
			     quescontent=$.EXAMSQUESTIONS.getSort(value.items,value.answer,value.itemtype);
			}
			if(value.answer.length!=0&&quescontent){
			   content.find(".content").eq(1).html(quescontent);
			}else{
			   content.find("span").eq(1).hide();
			}
			//储存试题的ID
			tr.children("td").eq(3).find("input").attr("bid",value.id);
			tr.appendTo(".childques");	
	});
	});
}


//听力材料的添加和删除
  $(".listen_add").click(function(){
 	var tr=$(".listen tr").eq(0).clone().show();
	tr.appendTo(".listen");
  });
  $(".listen_del").live("click",function(){
  	$(this).parent().parent().remove();
  });
