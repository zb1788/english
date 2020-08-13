function getmain(){
  $.post("../Cydict/main",function(data){
    $(".middle").html(data);
  })
}
//获取索引页面
function getindexinfo(id){
  $(".middle").empty();
  $.post("../Cydict/indexinfo",{id:id},function(data){
    $(".middle").html(data);
  })
}
//获取成语信息页面
function getcyinfo(id){
  $(".middle").empty();
  $.post("../Cydict/cyinfo",{id:id},function(data){
    $(".middle").html(data);
  })
}
//搜索结果页面
function cysearch(flag){
    $(".middle").empty();
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
    var name=$("#hanzisearch").parent().find("input").val();
    if(flag==0){
      if(name=='') alert("")
    }
    $.post("../Cydict/cysearch",{ran:Math.random(),name:name,name1:name1,name2:name2,name3:name3,name4:name4,flag:flag},function(data){
      $(".middle").html(data);
    });
}
//获取成语分类
function getDictClassfy(id){
	$.post("../Cydict/cyclassfy",{id:id},function(data){
		$.each(data,function(key,value){

           var content="<li><img class='left' src='../../Public/Klx/Dict/images/sss.jpg' /><div class='suo_r'><h1>"+value.classfyname+"</h1>"
           if(key==0){
              content=content+"<span class='sy_zm'>";
           }else{
              content=content+"<span class='lab'>";
           }
           $.each(value.childclassfy,function(k,v){
           	content=content+"<a href=' javascript:void(0);' onclick='getindexinfo("+v.id+");'>"+v.classfyname+"</a>";
           });
           content=content+"</span><div class='clearfix'></div></li>";
           $(".suoyin").append(content);
		});
	})
}

//获取成语父分类
function getDictParentClassfy(id){
  $.post("../Cydict/cychildparent",{id:id,ran:Math.random()},function(data){
        if(data.child.length>0){
           var content="<h1>"+data.classfyname+"</h1><span class='sy_zm'>";
           $.each(data.parent,function(k,v){
               content=content+"<a href=' javascript:void(0);' onclick='getindexinfo("+v.id+");'>"+v.classfyname+"</a>";
           });
           content=content+"</span><div class='clearfix'></div>";
           $(".kuai01").append(content);
           content="<div class='kuai01'><span class='zii'>";
           $.each(data.child,function(k,v){
              content=content+"<a href=' javascript:void(0);' onclick='getClassfyCy("+v.id+");'>"+v.classfyname+"</a>"
           });
           content=content+"</span><div class='clearfix'></div></div>";
           $(".kuai01").after(content);
           $("span .zii a:eq(0)").click();
        }
        else
        {
           var content="<h1>"+data.classfyname+"</h1><span class='zpp'>";
           $.each(data.parent,function(k,v){
               content=content+"<a href=' javascript:void(0);' onclick='getindexinfo("+v.id+");'>"+v.classfyname+"</a>";
           });
           content=content+"</span><div class='clearfix'></div>";
           $(".kuai01").append(content);
           $("span .zpp a:eq(0)").click();
        }
          
  })
}



//成语信息
function getcy(id){
  $.post("../Cydict/getcy",{id:id,ran:Math.random()},function(data){
           //成语以及音频
           var cycontent=data.cyname;
           $("#chengyu").append(cycontent);
           //成语拼音
           $("#pingyin").append("拼 音："+data.cypinyin);
           //成语详细解释
           if(data.cyjieshi){
             $("#jieshi").append("<h5>解    释：</h5>"+data.cyjieshi);
           }else{
             $("#jieshi").hide();
           }
           if(data.cydiangu){
             $("#chuchu").append("<h5>出    处：</h5>"+data.cydiangu);
           }else{
             $("#chuchu").hide();
           }
           if(data.cyyfyf){
             $("#shili").append("<h5>语法用法：</h5>"+data.cyyfyf);
           }else{
             $("#shili").hide();
           }
           if(data.cycycd){
             $("#cycd").append("<h5>常用程度：</h5>"+data.cycycd);
           }else{
             $("#cycd").hide();
           }
           if(data.cygqsc){
             $("#gqsc").append("<h5>感情色彩：</h5>"+data.cygqsc);
           }else{
             $("#gqsc").hide();
           }
           if(data.cyjiegou){
             $("#jiegou").append("<h5>成语结构：</h5>"+data.cyjiegou);
           }else{
             $("#jiegou").hide();
           }
           if(data.cycsnd){
             $("#csnd").append("<h5>产生年代：</h5>"+data.cycsnd);
           }else{
             $("#csnd").hide();
           }
           if(data.cygushi){
             $("#cygushi").append("<h5>成语故事：</h5>"+data.cygushi);
           }else{
             $("#cygushi").hide();
           }
           //近义词反义词
           if(data.jinyici.length>0){
              
              $.each(data.jinyici,function(key,value){
                $("#cyjyc").append("<a href=' javascript:void(0);' onclick='getcyinfo("+value.id+");'>"+value.cyname+"</a>");
               });
           }else{
             $("#cyjcy").hide();
           }
           
           //展示链接
           var arr_pinyin=data.cypinyin.split(" ");
           for(var i=0;i<data.cyname.length;i++){
              $(".chai").append("<a href='../Dictpc/info?id="+data.cyname.substr(i,1)+"'>"+data.cyname.substr(i,1)+"</a>");
           }
  })
}

function getClassfyCy(id){
  // $.post("getClassfyCy",{id:id,ran:Math.random()},function(data){
  //   var content="";
  //   $(".lie").empty();
  //   $.each(data,function(key,value){
  //     if(value.cyname.length=4){
  //       content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname+"</a>";
  //     }else {
  //       if(value.cyname.length>4){
  //         content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname.substr(0,3)+"...</a>";
  //       }else{
  //         content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname+"&nbsp;</a>";
  //       }
  //     } 
  //   });
  //   $(".lie").append(content);
  // });
  $("#cyclassfyid").val(id);
  pagelist(1,75);
}


 function pagelist(pageCurrent,page_size){
  var id=$("#cyclassfyid").val();
  $(".page").empty();
  $('.lie').empty();
  var content="";
  $.get("fenye",
      {
        ran:Math.random(),
        pageCurrent:pageCurrent,
        page_size:page_size,
        id:id
      },
      function(data){
      $.each(data,function(k,v){
        $('.page').html(k);
        $.each(v,function(key,value){
          if(value.cyname.length==4){
              content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname+"</a>";
            }else if(value.cyname.length>4){
              content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname.substr(0,3)+"..</a>";
            }else if(value.cyname.length<4){
              content=content+"<a href=' javascript:void(0);' onclick='getcyinfo("+value.cyid+");'>"+value.cyname+"&nbsp;</a>";
            }
            
        });
        $(".lie").append(content);
      });
    })
 }


  function hanzisearch(pageCurrent,page_size){
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
    var name=$("#hanzisearch").parent().find("input").val();
    $(".suoyin02").empty();
    $('.page').empty();
    $.post("cysearchlist",{flag:"0",pageCurrent:pageCurrent,page_size:page_size,name1:name1,name2:name2,name3:name3,name4:name4,name:name,ran:Math.random()},function(data){
      var content="";
      $.each(data,function(key,value){
        $('.page').html(key);
        $.each(value,function(key,value){
          
          content=content+"<li><h3>"+value.cyname+value.cypinyin+"</h3> <p> 解释: "+value.cyjieshi+"</p><p> 标签 ：";
          $.each(value.keyword,function(k,v){
            content=content+"<a href=' javascript:void(0);' onclick='getindexinfo("+v.classfyid+");'> "+v.classfyname+"</a>";
          });
          content=content+"</p><a href=' javascript:void(0);' onclick='getcyinfo("+value.id+");' class='more02'>查看详情</a></li>";
        });
        $(".suoyin02").append(content);
     });
  });

  }


function locationsearch(pageCurrent,page_size){
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
    $(".suoyin02").empty();
    $('.page').empty();
    $.post("cysearchlist",{flag:"1",pageCurrent:pageCurrent,page_size:page_size,name1:name1,name2:name2,name3:name3,name4:name4,ran:Math.random()},function(data){
     var content="";
     $.each(data,function(key,value){
        $('.page').html(key);
        $.each(value,function(key,value){
          content=content+"<li><h3>"+value.cyname+value.cypinyin+"</h3> <p> 解释: "+value.cyjieshi+"</p><p> 标签 ：";
          $.each(value.keyword,function(k,v){
            content=content+"<a href=' javascript:void(0);' onclick='getindexinfo("+v.classfyid+");'> "+v.classfyname+"</a>";
          });
          content=content+"</p><a href='javascript:void(0);' onclick='getcyinfo("+value.id+");' class='more02'>查看详情</a></li>";
        });
        $(".suoyin02").html(content);
     });
     
    });
  }
  

  function getcylist(id){

  }





    