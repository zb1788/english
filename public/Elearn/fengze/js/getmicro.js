function ItemInit(){
  var ul1 = document.getElementById("ul1");
  var ul2 = document.getElementsByClassName("ul2");
  var li1 = document.getElementsByClassName("li1");
  var ul3 = document.getElementsByClassName("ul3");
  var li2 = document.getElementsByClassName("li2");
  var ul4 = document.getElementsByClassName("ul4");
  var li3 = document.getElementsByClassName("li3");
  //获取二级菜单
  loadItem(li1, ul2);
  //获取三级菜单
  loadItem(li2, ul3);
  
  loadItem(li3, ul4);
}
//显示下拉菜单
function loadItem(obj, ul) {
  for (var index = 0; index < obj.length; index++) {
    obj[index].index = index;
    obj[index].onmouseover = function() {
      ul[this.index].style.display = "block";
    }
    obj[index].onmouseout = function() {
      ul[this.index].style.display = "none";
    }
  }
}

function getItem(){
	$.ajax({
		url:"getItem",
		type:"post",
		dataType:"json",
		data:{},
		success:function(data){
			$("#getitem").tmpl(data).appendTo(".ul2");
			setTimeout(function(){
				ItemInit();
				getCourseList(version_id);
			},500);
		},
		error:function(error){
			return false;
		} 
	});
}

function getCourseList(id){
	version_id = id;
	//console.log(version_id);
	$(".ul2").hide();
	$(".ul3").hide();
	$(".ul4").hide();
	$(".kechenglist").empty(); 
	$.ajax({
		url:"getCourseList",
		type:"post",
		dataType:"json",
		data:{version_id:version_id,listOrderType:listOrderType},
		success:function(data){
			$("#script_list").tmpl(data).appendTo(".kechenglist");
		},
		error:function(error){
			return false;
		} 
	});
}

function changeOrder(){
	var src = $("#imgorder").attr("src");
	//console.log(src.indexOf("downlist"));
	if(src.indexOf("downlist") != -1){
		listOrderType = "asc";
		$("#imgorder").attr("src","/public/Elearn/fengze/images/uplist.png");
	}
	else{

		listOrderType = "desc";
		$("#imgorder").attr("src","/public/Elearn/fengze/images/downlist.png");
	}
    getCourseList(version_id);
}


function goPlay(id){
	$.ajax({
		url:"savePlayData",
		type:"get",
		dataType:"json",
		data:{course_id:id,ran:Math.random()},
		success:function(data){
			//window.location.href="play?course_id="+id;
		},
		error:function(error){
			return false;
		} 
	});
   
}