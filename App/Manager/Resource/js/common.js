
/*基础初始化类*/
$.EBC = {
  setGrade:function(obj){          
    $(obj).empty(); 
    $.get('../getbasedata/getgrade',{random:Math.random()}, function(data){
      $.each(data, function(i,val){   
         $(obj).append($("<option>").val(val.id).text(val.name));
      });
    });
  },             
  setVersion:function(obj,gradeid){     
    $(obj).empty(); 
    $.get('../getbasedata/getversion',{gradeid:gradeid,random:Math.random()}, function(data){
      $.each(data, function(i,val){   
         $(obj).append($("<option>").val(val.id).text(val.name));
      });
    });
  },             
  setUnit:function(obj,gradeid,versionid,termid) {        
    $(obj).empty(); 
    $.get('../getbasedata/getunit',{gradeid:gradeid,versionid:versionid,termid:termid,random:Math.random()}, function(data){
      $.each(data, function(i,val){   
         $(obj).append($("<option>").val(val.id).text(val.name));
      });
    });
  }
};

function dialogTips(content){
  art.dialog.tips('<font color="red">' + content + '……</font>', 0.5); 
} 
 
function dialogNotice(title,content){
  dialogNotice(title,content,3);
}

function dialogNotice(title,content,ts){   
  art.dialog({
    title: title,
    width: 240,
      content: content,
      icon: 'info', 
      opacity:0.2,
      fixed: true,
        lock: true,
        time: ts
  });
}

function isNumber(obj) {
    var re = /^[0-9]+.?[0-9]*$/;    
    return re.test(obj);
} 