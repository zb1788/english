/*基础初始化类*/
$.EBC = {
  setGrade:function(obj){   
      
    $(obj).empty(); 
    $.get('../getbasedata/getgrade',{random:Math.random()}, function(data){
      $.each(data, function(i,value){   
       $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));
     
      });
    });
  },
   setTerm:function(obj,gradeid){     
    $(obj).empty(); 
    $.get('../getbasedata/getTerm',{gradeid:gradeid,random:Math.random()}, function(data){
      $.each(data, function(i,value){   
         $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));
      });
    });
  },             
  setVersion:function(obj,gradeid){     
    $(obj).empty(); 
    $.get('../getbasedata/getversion',{gradeid:gradeid,random:Math.random()}, function(data){
      $.each(data, function(i,value){   
          $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));
      });
    });
  },             
  setUnit:function(obj,gradeid,versionid,termid) {        
    $(obj).empty(); 
    $.get('../getbasedata/getunit',{gradeid:gradeid,versionid:versionid,termid:termid,random:Math.random()}, function(data){
      $.each(data, function(i,val){   
         //$(obj).append($("<option >").val(val.ks_code).text(val.ks_name));
         $(obj).append("<option isunit='"+val.is_unit+"' value='"+val.ks_code+"'>"+val.ks_name+"</option>");
      });
    });
  },
  setTvoiceid:function(obj){          
    $(obj).empty();
    $.get('../getbasedata/getvoiceid',{random:Math.random(),flag:'0'}, function(data){
      $.each(data, function(i,value){   
         $(obj).append($("<option>").val(value.id).text(value.name));
      });
    });
  }, 
  setVvoiceid:function(obj){          
    $(obj).empty();
    $.get('../getbasedata/getvoiceid',{random:Math.random(),flag:'1'}, function(data){
      $.each(data, function(i,value){   
         $(obj).append($("<option>").val(value.id).text(value.name));
      });
    });
  },
  setProvince:function(obj){          
    $(obj).empty(); 
    $.get('../Getbasedata/getProvince',{random:Math.random()}, function(data){
      $.each(data, function(i,value){   
         $(obj).append($("<option>").val(value.id).text(value.name));
      });
    });
  }  
}

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