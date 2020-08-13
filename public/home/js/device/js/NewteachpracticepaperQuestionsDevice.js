var saveAnswerUrl = ctxPath+"interface/answerdevice/saveanswer.action"; //保存答题结果url
$(document).ready(function () {
	var height = window.screen.height;
	$(".main").height(height-100);
	
	$.loadObject.setLoadUrl({
		dUrl:ctxPath+'interface/newteach/difficultyPractice_paper.action',//难易程度Url
  		qUrl:ctxPath+'interface/newteach/answerdevice/allPractice_paperQuestionsNew.action',//试题Url
    	qtpeUrl:ctxPath+'interface/newteach/questionsPractice_paperType.action'//题型Url
	});
	$.loadObject.ctxPath=ctxPath;
	$.loadObject.setParams(getParams());
	//search();
	//$.loadObject.loadDifficulty();
	//$.loadObject.loadQtypeId();
	$.loadObject.loadQuestion();
	$.loadObject.qtypeClick=function(){
		$("#difficulty a[class=on]").removeClass('cur');
		$("#difficulty a[did='']").addClass('cur');
		$.loadObject.setParams(getParams());
		$.loadObject.loadDifficulty();
		$.loadObject.loadQuestion();
	}
	$.loadObject.difficultyClick=function(){
		$.loadObject.setParams(getParams());
		$.loadObject.loadQuestion();
	}
});

function clearParamsCls(){
	$("#qtype_id a[class=cur]").removeClass('cur');
	$("#qtype_id a[qid='']").addClass('cur');
	$("#difficulty a[class=on]").removeClass('cur');
	$("#difficulty a[did='']").addClass('cur');
}

function getParams(){
	var param={};
//	param['questions.qtype_id']=$("#qtype_id a[class=cur]").attr('qid');
//	param['questions.difficulty']=$("#difficulty a[class=cur]").attr('did');
//	param['questions.remark']=$("#remark").val();
	param['questions.paper_id']=$("#paper_id").val();
	param['studentClass']=$("#studentClass").val();
	return param;
}
