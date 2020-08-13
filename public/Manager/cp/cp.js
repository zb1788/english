/*此js主要针对的是英语测评累的添加试题进行的*/
//添加大题部分
var changeb=0;
var changea=0;
$(function(){
	$(".part.nav").sortable({ 
	 	start: function(event, ui)
	 	{
	 		var changeb=ui.item.prevAll("li").length;
	 	},
	 	stop:  function(event, ui)
	 	{
		 	//交换左边栏目部分
		 	ui.item.addClass("cur");
		 	//计算位置
		 	var position=$(".part.nav .cur").prevAll("li").length;
		 	ui.item.removeClass("cur");
		 	changea=position;
		 	ui.item.find("a").attr("href","#paper-part-"+(position-1));
		 	ui.item.find(".part").text((position-1));
		 	var i=0;
		 	$.each(ui.item.nextAll("li"),function(){
		 		$(this).find("a").attr("href","#paper-part-"+(position+i));
		 		$(this).find(".part").text((position+i));
		 		i=i+1;
		 	});
		 	
		 	//交换右边div部分
		 	changeDiv($("#paper-part-"+changeb),$("#paper-part-"+changea));
		 } 
	});
    $( ".part.nav").disableSelection();

    //设置分值
$(".qscore").live("keyup",function(){
     var score=0;
     var data=$(".qscore");
     $.each(data,function(key,value){
        var temp=$(this).val();
        if(temp=="")temp=0;
        score=score+parseInt(temp);
     });
      $("#paperscore").text(score);
    });


})
function addTitle(examsid){
	//计算目前是第几部分
	var len=$(".part.nav li").length;
	var $content=$("<li><a href='#paper-part-"+(len-2)+"' class='btn btn-part' >第<font class='part'>"+(len-1)+"</font>部分</a></li>");
	var insert=$(".part.nav li:last");
	$content.insertBefore(insert);
    $.get("addTitle",{examsid:examsid,ran:Math.random()},function(data){
        var question="<div id='paper-part-"+(len-2)+"' class='section-part'><div class='part-header'><form class='form-inline part-name-wrap'><div class='form-group'><label class='partm'>第"+(len-1)+"部分：</label><input  class='form-control form-input part-name' placeholder='部分名称' type='text'></div><a class='btn btn-default btn-remove-part pull-right' bid='"+data.id+"' examsid='"+examsid+"'>删除该部分</a></form><form class='form mt10 w'><div><textarea  class='form-control form-input paper-desc mb10' placeholder='部分答题说明，该信息考生可见（选填）'></textarea></div></form></div><div class='part-questions-container mt30'><div style='height: 51px;' class='pin-wrapper'><div style='width: 849px;' class='part-questions-toolbar unselectable'><ul class='list-inline inline-block'></ul><ul class='list-inline pull-right'><li>声音：<span class='text-golden-yellow'><select class='select'><option value='1'>中男Liang</option><option value='2'>中女Hui</option></select></span></li><li>次数：<span class='text-golden-yellow'><input class='form-control form-input input-point'  type='text'></span><span class='text-medium-gray ml5'>遍</span></li><li>停顿时间：<span class='text-golden-yellow'><input class='form-control form-input input-point'  type='text'></span><span class='text-medium-gray ml5'>秒</span></li><li>总分值：<span class='text-golden-yellow'><input class='form-control form-input input-point qscore'  type='text'></span><span class='text-medium-gray ml5'>分</span></li><li><a class='btn btn-success btn-sm btn-add-questions' bid='"+data.id+"' examsid='"+examsid+"'><i class='fa fa-plus'></i> 添加题目</a></li></ul><div class='cb'></div></div></div><ul  class='part-question-list q-list'><p>该部分下还没有试题，请从这里添加 <i class='fa fa-hand-o-up'></i></p></ul></div></div>";
        $("#paper-form-container").append(question);
    })
	
}


//数字转化函数
function convertToChinese(num){
    var N = ["一", "二", "三", "四", "五", "六", "七", "八", "九"];   
    var str = num.toString();  
    var len = num.toString().length;  
    var C_Num = []; 
    for(var i = 0; i < len; i++){  
        C_Num.push(N[str.charAt(i)]);  
    }  
    return C_Num.join('');  
}



//答题相互变化顺序
function changeDiv(obj1,obj2){
	var $div1 = obj1;
    var $div3 = obj2;
    var $temobj1 = $("<div></div>");
    var $temobj2 = $("<div></div>");
    $temobj1.insertBefore($div1);
    $temobj2.insertBefore($div3);
    $div1.insertAfter($temobj2);
    $div3.insertAfter($temobj1);
    $temobj1.remove();
    $temobj2.remove();
    var i=-1;
    $.each($(".section-part"),function(){
    	$(this).attr("id","paper-part-"+(i+1));
    	$(this).find(".partm").html("第"+(i+2)+"部分：");
    	i=i+1;
    });

}



$(".btn-add-questions").live("click",function(){
  	var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    var gradeid=$("#gradeid").val();
  	//?flag=2&type=1&ran='+Math.random()+'&id=0&questype='+$('#questype').val()+'&complexity='+$('#complexity').val()+'&currentpage='+$("#current_page")
    art.dialog.open("../cp/choice?ran="+Math.random()+"&paperid="+id+"&gradeid="+gradeid,{
        title:"添加单项小题",
        width:800,
        height:810,
        lock:true,
        opacity:0.3,
        cancel:function(){
          location.href="../Cp/index?examsid="+examsid;
        }
    });
});


//单题添加
function show_paper_info(examsid) {
    $.get('listenshow', {examsid: examsid}, function(result) {
        $('#paper-form-container').append(result);
    });
}


/*大题删除删除的是paper中的数据以及每一份部分中小题的数据*/
$(".btn-remove-part").live("click",function(){
  var id=$(this).attr("bid");
  var examsid=$(this).attr("examsid");
  art.dialog.confirm('你确定要删除此部分大题吗？', function () {
      $.get("../Cp/Deltitle",{id:id,random:Math.random()},function(){
        location.href="../Cp/index?examsid="+examsid;
      });
    });
  });




//单题编辑
$(".baseedit").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    //?flag=2&type=1&ran='+Math.random()+'&id=0&questype='+$('#questype').val()+'&complexity='+$('#complexity').val()+'&currentpage='+$("#current_page")
    art.dialog.open("../cp/base_questions_edit?ran="+Math.random()+"&id="+id,{
        title:"添加单项小题",
        width:800,
        height:760,
        lock:true,
        opacity:0.3,
        cancel:function(){
          location.href="../Cp/index?examsid="+examsid;
        }
    });
});

//单题删除
$(".basedel").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    art.dialog.confirm('你确定要删除小题吗？', function () {
      $.get("../Cp/questionsDel",{id:id,random:Math.random()},function(){
        location.href="../Cp/index?examsid="+examsid;
      });
    });
});


//单题删除
$(".combinechilddel").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    art.dialog.confirm('你确定要删除此组合题下的小题吗？', function () {
      $.get("../Cp/questionsDel",{id:id,random:Math.random()},function(){
        location.href="../Cp/index?examsid="+examsid;
      });
    });
});


//组合题编辑
$(".combineedit").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    //?flag=2&type=1&ran='+Math.random()+'&id=0&questype='+$('#questype').val()+'&complexity='+$('#complexity').val()+'&currentpage='+$("#current_page")
    art.dialog.open("../Cp/combine_questions_edit?ran="+Math.random()+"&id="+id,{
        title:"组合题",
        width:800,
        height:500,
        lock:true,
        opacity:0.3,
        cancel:function(){
          location.href="../Cp/index?examsid="+examsid;
        }
    });
});

//组合题删除
$(".combinedel").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    art.dialog.confirm('你确定要删除此大题吗？', function () {
      $.get("../Cp/questionsDel",{id:id,random:Math.random()},function(){
        location.href="../Cp/index?examsid="+examsid;
      });
    });
});



//添加小题
$(".combinechildadd").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    art.dialog.open("../Cp/combine_questions_child_add?ran="+Math.random()+"&id="+id,{
        title:"组合题小题添加",
        width:650,
        height:300,
        lock:true,
        opacity:0.3,
        cancel:function(){
          location.href="../Cp/index?examsid="+examsid;
        }
    });


});



//编辑组合题小题
$(".combinechildedit").live("click",function(){
    var id=$(this).attr("bid");
    var examsid=$(this).attr("examsid");
    art.dialog.open("../Cp/combine_questions_child_edit?ran="+Math.random()+"&id="+id,{
        title:"组合题小题编辑",
        width:650,
        height:300,
        lock:true,
        opacity:0.3,
        cancel:function(){
          location.href="../Cp/index?examsid="+examsid;
        }
    });


});







//保存试卷
  function savepaper(examsid){
      //每一部分的数据
      var ztimes=$("#ztimes").val();
      var examname=$("#exam").val();
      var papername=$("#exampaper").val();
      var arrjson =[];
      var isdisplay=$("input[type='checkbox']").is(":checked")?1:0;
      $(".section-part").each(function(){
        var tr =$(this); 
        var id = tr.find('.btn-add-questions').attr("BID");
        var cncontent = tr.find('textarea').val();
        var vncontent = tr.find('textarea').val();
        var tvoiceid = tr.find('.select').val();
        var repeate = tr.find('.repeate').val();
        var stoptime = tr.find('.stoptime').val();
        var qscore = tr.find('.qscore').val();
        var partname = tr.find('.part-name').val();
        var partid = tr.find('.part-name').attr("partid");
        var obj = {};
        obj.id = id;
        obj.vncontent=vncontent;
        obj.cncontent=cncontent;
        obj.tvoiceid = tvoiceid;  
        obj.repeate = repeate;  
        obj.stoptime = stoptime;
        obj.qscore = qscore;
        obj.partname=partname;
        obj.partid=partid;   
        arrjson.push(obj);       
      });
      $.post("saveexam",{ran:Math.random(),display:isdisplay,ztimes:ztimes,examsid:examsid,examname:examname,papername:papername,data:JSON.stringify(arrjson)},function(){
        location.href="../Cp/index?examsid="+examsid;
      });
    }





