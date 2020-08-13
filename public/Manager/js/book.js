function contentedit(obj,pageid){
  //首先是数据库进行插入然后在表格中进行插入
  var id=$(obj).parents(".contents").attr("bid");
  var contents=$(obj).parents(".contents").find("table");
  //内同
  var encontent=$(obj).parents(".contents").find("textarea").eq(0).val();
  var cncontent=$(obj).parents(".contents").find("textarea").eq(1).val();
  var filename=$(obj).parents(".contents").find(".filename").attr("path");
  var sort=$(contents).find("tr").length;
  $.post("setPageContent",{filename:filename,id:id,encontent:encontent,cncontent:cncontent,sort:sort,pageid:pageid,ran:Math.random()},function(data){
    getContentList(page);
    $(obj).parents(".contents").find("textarea").eq(0).val("");
    $(obj).parents(".contents").find("textarea").eq(1).val("");
    $(obj).parents(".contents").find(".filename").val("");
    $(obj).parents(".contents").find(".filename").attr("path","");
    $(obj).parents(".contents").attr("bid","0");
  });
}

function editcontent(obj,pageid){
  //首先是数据库进行插入然后在表格中进行插入
  $.post("findPageContent",{pageid:pageid,ran:Math.random()},function(data){
    $(obj).parents(".contents").find("textarea").eq(0).val("");
    $(obj).parents(".contents").find("textarea").eq(1).val("");
    $(obj).parents(".contents").find("textarea").eq(0).val(data.encontent);
    $(obj).parents(".contents").find("textarea").eq(1).val(data.cncontent);
    $(obj).parents(".contents").find(".filename").attr("path",data.filename);
    $(obj).parents(".contents").attr("bid",data.id);
  });
}


function contentdel(obj,id){
  var contents=$(obj).parents("contents").find("table");
  $.post("delPageContent",{id:id,ran:Math.random()},function(data){
    $(obj).parents("tr").remove();
  });
}

function wordchoose(pageid){
  art.dialog.open('wordslist?pageid='+pageid, {
      title: "单词列表",
      width: 800,
      height: 700,
      lock: true,
      opacity: 0.3,
      button: [
          {
              name: '保存',
              callback: function() {
                  getWordsList(page);
              },
              focus: true
          }
      ]
    });
}

function wordedit(obj,id,tindex){
  var contents=$(obj).parents("contents").find("table");
  $.post("delPageContent",{id:id,ran:Math.random()},function(data){
    $(contents).find("tr").eq(tindex-1).remove();
  });
}


function worddel(obj,id){
  $.post("delPageWord",{id:id,ran:Math.random()},function(data){
    $(obj).parents("li").remove();
  });
}


function questionedit(obj,pageid){
  var id=$(obj).parents(".questions").attr("bid");
  var tcontent=$(obj).parent().parent().find("textarea").val();
  var items=[];
  var answer="";
  $.each($(obj).parents(".form-group").find(".item"),function(key,value){
    var temp={};
    temp.id=$(value).attr("id");
    temp.content=$(value).find("input[type='text']").val();
    var checked=$(value).find("input[type='radio']").is(":checked");
    if(checked){
        answer=$(value).find("input[type='text']").val();
    }
    items.push(temp);
  })
  $.post("setPageQuestion",{pageid:pageid,id:id,tcontent:tcontent,answer:answer,items:JSON.stringify(items),ran:Math.random()},function(data){
     getQuestionsList(page);
     $(obj).parents(".questions").find("textarea").val("");
      $(obj).parents(".questions").find(".form-group").find(".items").empty().html('<div class="item"><a href="javascript:void(0);" onclick="itemadd(this);"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span></a><a href="javascript:void(0);" onclick="itemdel(this);" ><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a><input type="radio" name="name0" checked><input type="text"  id="name" placeholder="请输入名称" value=""></div>');
      $(obj).parents(".questions").attr("bid","0");
  });
}

//试题编辑editquestion
function editquestion(obj,picid,id){
  art.dialog.open('questionedit?picid='+picid+'&id='+id+'&ran='+Math.random(), {
      title: "编辑问题",
      width: 700,
      height: 600,
      lock: true,
      opacity: 0.3,
      button: [
              {
                  name: '保存',
                  callback: function() {
                      var iframe = this.iframe.contentWindow;
                      var re = iframe.save();
                      window.location.href="index?id="+Request["id"]+"&index="+page;
                      return true;                       
                  },
                  focus: true
              },
              {
                  name: '关闭',
                  callback: function() {
                      //$("#gradeid").change();
                      window.location.reload();
                  },
                  focus: false
              }
          ]
  });
}





function questiondel(obj,id){
  $.post("delPageQuestion",{id:id,ran:Math.random()},function(data){
    $(obj).parent().remove();
  });
}


function itemadd(obj){
  var tr='<div class="item"><a href="javascript:void(0);" onclick="itemadd(this);"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span></a>&nbsp;<a href="javascript:void(0);" onclick="itemdel(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>&nbsp;<input type="radio" name="name0">&nbsp;<input type="text"  id="name" placeholder="请输入名称"></div>';
  $(tr).insertAfter($(obj).parent('.item'));
}

function itemdel(obj){
  $(obj).parent('.item').remove();
}


function getContentList(index){
  
  var pageid=$(".contents").eq(index).attr("pageid");
  var contents=$(".contents").eq(index).find(".table").find("tbody");
  $(contents).empty();
  $.getJSON("getPicContentList", {pageid:pageid,random: Math.random() }, function (data) {
      $("#addpiccontent").tmpl(data).appendTo(contents);
  });
}

function getWordsList(index){
  var contents=$(".words").eq(index).find("ul.pager");
  console.log(contents);
  var pageid=$(".words:eq("+index+")").attr("pageid");
  $(contents).empty();
  $.getJSON("getPicWordList", {pageid:pageid,random: Math.random() }, function (data) {
      $("#addpicword").tmpl(data).appendTo(contents);
  });
}

function getQuestionsList(index){
  var contents=$(".questions").eq(index).find(".questionlist");
  var pageid=$(".questions:eq("+index+")").attr("pageid");
  $(contents).empty();
  $.getJSON("getPicQuestionList", {pageid:pageid,random: Math.random() }, function (data) {
      $("#addpicquestion").tmpl(data).appendTo(contents);
  });
}

function play(filename){
  jpclear();
  var mp3 = filename;
  if (!mp3) {
      alert("请上传音频");
  }
  else
  {
      mp3 = mp3url+filename;
      $("#jplayer").jPlayer("setMedia", {
          mp3: mp3
      }).jPlayer("play");
  }
}

function jpclear() {
    $("#jplayer").jPlayer("clearMedia");
    $("#jplayer").jPlayer("stop");
    $("#jplayer").unbind($.jPlayer.event.ended);
    $("#jplayer").unbind($.jPlayer.event.progress);
}



