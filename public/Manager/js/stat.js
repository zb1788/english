/*基础初始化类*/
$.STAT = {
  SetExamsstat:function(obj,starttime,endtime){          
    $(".list_table td").parents("tr").remove();
    $.get('../getbasedata/getStatExams',{starttime:starttime,endtime:endtime,random:Math.random()}, function(data){
      $.each(data, function(i,value){
        i++;
        var tr = $(".list_demo tr").eq(0).clone(); 
        var td = tr.children('td').eq(0);
        td.html(value.gradename);
        td = tr.children('td').eq(1);
        td.html(value.versionname);
        td = tr.children('td').eq(2);
        td.html(value.termname);
        td = tr.children('td').eq(3);
        td.html(value.checked);
        td = tr.children('td').eq(4);
        td.html(value.tbchecked);
        td = tr.children('td').eq(5);
        td.html(value.ktchecked);
        td = tr.children('td').eq(6);
        td.html(value.checking);
        td = tr.children('td').eq(7);
        if(value.gradeid=="c"){
           td.html("&nbsp;&nbsp;"+value.num);
        }else{
           td.html("&nbsp;&nbsp;<a href='examsUnit_stat?gradeid="+value.gradeid+"&termid="+value.termid+"&versionid="+value.versionid+"&starttime="+starttime+"&endtime="+endtime+"'>"+value.num+'</a>');
        }
        tr.appendTo(".list_table");
      });

    });
  },
  SetExamsUnitstat:function(obj,starttime,endtime,gradeid,termid,versionid){          
    $(".list_table td").parents("tr").remove();
    $.get('../getbasedata/getStatExamsUnit',{gradeid:gradeid,termid:termid,versionid:versionid,starttime:starttime,endtime:endtime,random:Math.random()}, function(data){
      $.each(data, function(i,value){
        i++;
        var tr = $(".list_demo tr").eq(0).clone(); 
        var td = tr.children('td').eq(0);
        td.html(value.ks_name);
        td = tr.children('td').eq(1);
        td.html(value.checked);
        td = tr.children('td').eq(2);
        td.html(value.tbchecked);
        td = tr.children('td').eq(3);
        td.html(value.ktchecked);
        td = tr.children('td').eq(4);
        td.html(value.checking);
        td = tr.children('td').eq(5);
        td.html("&nbsp;&nbsp;"+value.num);
        tr.appendTo(".list_table");
      });

    });
  }
}