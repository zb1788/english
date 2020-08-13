function getmain(){
  $(".middle").empty();
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
    
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
    var name=$("#hanzisearch").parent().find("input").val();
    if(flag==0){
      if(name=="") {alert("请输入您要搜索的成语");return false;}
    }else{
		if(name1==""&&name2==""&&name3==""&&name4=="") {alert("请输入您要搜索的成语");return false;}
	}
	$(".middle").empty();
    $.post("../Cydict/cysearch",{ran:Math.random(),name:name,name1:name1,name2:name2,name3:name3,name4:name4,flag:flag},function(data){
      $(".middle").html(data);
    });
}
//获取成语分类
function getDictClassfy(id){
	$(".suoyin").empty();
	$.post("../Cydict/cyclassfy",{id:id},function(data){
		$.each(data,function(key,value){

           var content="<li><img class='left' src='../../public/Klx/dict/images/"+value.fileurl+"' /><div class='suo_r'><h1>"+value.classfyname+"</h1>"
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
			   if(v.flag==1){
				   content=content+"<a href=' javascript:void(0);' class='zibianse' onclick='getindexinfo("+v.id+");'>"+v.classfyname+"</a>";
			   }else{
				   content=content+"<a href=' javascript:void(0);'  onclick='getindexinfo("+v.id+");'>"+v.classfyname+"</a>";
			   }
               
           });
		   
           content=content+"</span><div class='clearfix'></div>";
           $(".kuai01").append(content);
           content="<div class='kuai01'><span class='zii'>";
           $.each(data.child,function(k,v){
			  
              content=content+"<a href=' javascript:void(0);' name='ziti' onclick='getClassfyCy("+v.id+");$(this).addClass(\"zimubianse\")'>"+v.classfyname+"</a>"
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
		   //alert(data.jinyici.length);
           if(data.jinyici.length>0){
			   var i=1;
              $.each(data.jinyici,function(key,value){
				  if(i<3){
					  if(value){
						  $("#cyjyc").append("<a href=' javascript:void(0);' onclick='getcyinfo("+value.id+");'>"+value.cyname+"</a>");
					  }else{
						  $("#cyjyc").append("<a href=' javascript:void(0);'>没有近义词</a>");
					  }
				  }
				  i++;
               });
           }else{
               $("#cyjyc").append("没有近义词");
           }
           
           //展示链接
		   var obj=new Map();
           var arr_pinyin=data.cypinyin.split(" ");
           for(var i=0;i<data.cyname.length;i++){
			   if(data.cyname.substr(i,1).indexOf("，")<0){
				   if(obj.get(data.cyname.substr(i,1))!="1"){
					   $(".chai").append("<a href='../Dictpc/info?id="+data.cyname.substr(i,1)+"'>"+data.cyname.substr(i,1)+"</a>");
					   obj.put(data.cyname.substr(i,1),"1");
				   }   
			   }
           }
  })
}

function getClassfyCy(id){
  $("a[name='ziti']").removeClass("zimubianse");
  $("#cyclassfyid").val(id);
  pagelist(1,100);
}


 


  function hanzisearch(pageCurrent,page_size){
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
    var name=$("#hanzisearch").parent().find("input").val();
	if(name==""){alert("请输入您要搜索的成语");return false;}
    $(".suoyin02").empty();
    $('.page').empty();
    $.post("cysearchlist",{flag:"0",pageCurrent:pageCurrent,page_size:page_size,name1:name1,name2:name2,name3:name3,name4:name4,name:name,ran:Math.random()},function(data){
      var content="";
	  if(data.length==0){
		  $(".suoyin02").append("无此相关的成语，我们加紧进行进一步完善");  
	  }else{
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
	  }
	  });  
  }


function locationsearch(pageCurrent,page_size){
    var name1=$("#locationsearch").parent().find("input").eq(0).val();
    var name2=$("#locationsearch").parent().find("input").eq(1).val();
    var name3=$("#locationsearch").parent().find("input").eq(2).val();
    var name4=$("#locationsearch").parent().find("input").eq(3).val();
	if(name1==""&&name2==""&&name3==""&&name4==""){alert("请输入您要搜索的成语");return false;}
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
  
  
  
  /*
 * MAP对象，实现MAP功能
 *
 * 接口：
 * size()     获取MAP元素个数
 * isEmpty()    判断MAP是否为空
 * clear()     删除MAP所有元素
 * put(key, value)   向MAP中增加元素（key, value)
 * remove(key)    删除指定KEY的元素，成功返回True，失败返回False
 * get(key)    获取指定KEY的元素值VALUE，失败返回NULL
 * element(index)   获取指定索引的元素（使用element.key，element.value获取KEY和VALUE），失败返回NULL
 * containsKey(key)  判断MAP中是否含有指定KEY的元素
 * containsValue(value) 判断MAP中是否含有指定VALUE的元素
 * values()    获取MAP中所有VALUE的数组（ARRAY）
 * keys()     获取MAP中所有KEY的数组（ARRAY）
 *
 * 例子：
 * var map = new Map();
 *
 * map.put("key", "value");
 * var val = map.get("key")
 * ……
 *
 */
function Map() {
    this.elements = new Array();
    //获取MAP元素个数
    this.size = function() {
        return this.elements.length;
    };
    //判断MAP是否为空
    this.isEmpty = function() {
        return (this.elements.length < 1);
    };
    //删除MAP所有元素
    this.clear = function() {
        this.elements = new Array();
    };
    //向MAP中增加元素（key, value)
    this.put = function(_key, _value) {
        this.elements.push( {
            key : _key,
            value : _value
        });
    };
    //删除指定KEY的元素，成功返回True，失败返回False
    this.remove = function(_key) {
        var bln = false;
        try {
            for (i = 0; i < this.elements.length; i++) {
                if (this.elements[i].key == _key) {
                    this.elements.splice(i, 1);
                    return true;
                }
            }
        } catch (e) {
            bln = false;
        }
        return bln;
    };
    //获取指定KEY的元素值VALUE，失败返回NULL
    this.get = function(_key) {
        try {
            for (i = 0; i < this.elements.length; i++) {
                if (this.elements[i].key == _key) {
                    return this.elements[i].value;
                }
            }
        } catch (e) {
            return null;
        }
    };
    //获取指定索引的元素（使用element.key，element.value获取KEY和VALUE），失败返回NULL
    this.element = function(_index) {
        if (_index < 0 || _index >= this.elements.length) {
            return null;
        }
        return this.elements[_index];
    };
    //判断MAP中是否含有指定KEY的元素
    this.containsKey = function(_key) {
        var bln = false;
        try {
            for (i = 0; i < this.elements.length; i++) {
                if (this.elements[i].key == _key) {
                    bln = true;
                }
            }
        } catch (e) {
            bln = false;
        }
        return bln;
    };
    //判断MAP中是否含有指定VALUE的元素
    this.containsValue = function(_value) {
        var bln = false;
        try {
            for (i = 0; i < this.elements.length; i++) {
                if (this.elements[i].value == _value) {
                    bln = true;
                }
            }
        } catch (e) {
            bln = false;
        }
        return bln;
    };
    //获取MAP中所有VALUE的数组（ARRAY）
    this.values = function() {
        var arr = new Array();
        for (i = 0; i < this.elements.length; i++) {
            arr.push(this.elements[i].value);
        }
        return arr;
    };
    //获取MAP中所有KEY的数组（ARRAY）
    this.keys = function() {
        var arr = new Array();
        for (i = 0; i < this.elements.length; i++) {
            arr.push(this.elements[i].key);
        }
        return arr;
    };
}
  
  
  
  
  





    