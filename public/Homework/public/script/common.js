// JavaScript Document

/*弹出下拉框(层)*/
var timeOut=[];
function onMouseOutbox(x){
	timeOut[x]= window.setTimeout( function() {
	$('#'+x).hide();
	},0);
	$('#'+x).parent().siblings().css("position","relative");
	var abc=$('#'+x).attr("class");
	if(abc.indexOf("drop")>=0){
		$('#'+x).nextAll().removeClass();
		$('#'+x).siblings().addClass("arr2 arr_d");	 
	}
}
function onMouseOverbox(x){
	window.clearTimeout(timeOut[x]);
	$('#'+x).fadeIn(300);
	$('#'+x).parent().siblings().css("position","static");
	var abc=$('#'+x).attr("class");
	if(abc.indexOf("drop")>=0){
		$('#'+x).nextAll().removeClass();
		$('#'+x).siblings().addClass("arr2 arr_u");	 
	}
} 

function onMouseOutbox2(y){
	timeOut[y]= window.setTimeout( function() {
	$('#'+y).hide();
	},0);
	var abc=$('#'+y).attr("class");
	if(abc.indexOf("drop")>=0){
		$('#'+y).siblings().removeClass();
		$('#'+y).siblings().addClass("arr arr_d");	 
	}
}
function onMouseOverbox2(y){
	window.clearTimeout(timeOut[y]);
	$('#'+y).fadeIn(300);
	var abc=$('#'+y).attr("class");
	if(abc.indexOf("drop")>=0){
		$('#'+y).siblings().removeClass();
		$('#'+y).siblings().addClass("arr arr_u");	 
	}
} 

function onMouseOutbox_base(x){
timeOut[x]= window.setTimeout( function() {
$('#'+x).fadeOut(300);
},500);
}
function onMouseOverbox_base(x){
window.clearTimeout(timeOut[x]);
$('#'+x).fadeIn(300);
} 


/*页签切换*/
function Show_Tab(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			document.getElementById(where+"Tab_"+i).className="";
			document.getElementById(where+"Tab_"+i).childNodes[0].style.display="none";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
			document.getElementById(where+"Tab_"+i).className="cur_2";
			document.getElementById(where+"Tab_"+i).childNodes[0].style.display="block";
			}
	}
}

function Show_Tab_List2(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			document.getElementById(where+"Tab_"+i).className="";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
			document.getElementById(where+"Tab_"+i).className="cur_2";
			}
	}
}


function Show_Tab2(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			document.getElementById(where+"Tab_"+i).className="";
			document.getElementById(where+"Tab_"+i).childNodes[0].style.display="none";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
			document.getElementById(where+"Tab_"+i).className="sel2";
			document.getElementById(where+"Tab_"+i).childNodes[0].style.display="block";
			}
	}
}



/*下拉框的值选择后，上面的值发生对应的改变*/
$(".drop a").click(function(){
	var str = $(this).text();
	$(this).parent().prev().text(str);
	$(".drop").hide();
})

/*显示、隐藏左侧目录树*/
$(".hideTree a").click(function(){
		$(".dtree").hide();
		$(".kc_list").addClass('full');
		$(".kc_con").css('background-image','none');
		$(".rightfloat").fadeIn("200");
	}
)

$(".show_dtree a").click(function(){
		$(".dtree").show();
		$(".kc_list").removeClass("full");
		$(".kc_con").css('background-image','url(../public/images/bg_list.png)');
		$(".rightfloat").fadeOut("200");
	}
)


/*评论框获得焦点时变高*/
$(".mul_text").focus(function(){
	$(this).animate( { height: "80px"}, 400);
	})
	
$(".mul_text").blur(function(){
	$(this).animate( { height: "40px"}, 400);
	})

$(".piclist li").hover(function(){
	$(this).children("div.tjB2").show();
	},function(){
		$(this).children("div.tjB2").hide();
		})

/*切换角色弹出层*/
/*2015.3.4 start*/
	$("a.user").hover(function(){$(".login_con").fadeIn(500);})
	$("document").bind("click", function(){$(".login_con").hide(300);});
	$(".login_con").bind('mouseover', function(){$(document).unbind("click");});
	$(".login_con").bind('mouseout', function(){
		$(document).bind("click", function(){
			$(".login_con").hide(300);
			$("a.user").removeClass("hover");
		});
	})

$(".close_2 > a").click(function(){
	$(".login_con").hide();	
	$("a.user").removeClass("hover");
	})
$("a.user").hover(function(){
	$(this).addClass("hover");	
	})
	
//登录层晃动js
function shock(){
	for (i = 1; i < 7; i++)	{
		$('.login_con').animate({
			'right': '-=15'
		}, 3, function() {
		    $(this).animate({
				'right': '+=30'
			}, 3, function() {
				$(this).animate({
					'right': '-=15'
				}, 3, function() {
					$(this).animate({
						'right': 0
					}, 3, function() {
						
					});
				});
			});
		});
	}	
}
/*2015.3.4 end*/


//登录框检测js(不完善，请开发人员加以完善)
function login_check(){
	if ($(".usr > input").val() == '用户名' || $(".usr input").val == '' || $(".pwd > input").val() == ''){
			$(".usr").addClass("err");
			$(".pwd").addClass("err");
			shock();						
		}else{
			$(".usr").removeClass("err");
			$(".pwd").removeClass("err");
			//location.href="p_index.html";	
		}
	}


//页面加载完成时，判断页面分辨率，自动加载不同样式。
$(document).ready(function(){
	var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
	if (clienW >= 1280) {
		$(".w980").each(function () {
                $(this).attr("class","w1200");
            });	
	}else{
		$(".w1200").each(function () {
                $(this).attr("class","w980");
				
            });		
		}
	
	
});

//调整窗口宽度时，批量替换页面中的样式。
function resizeWidth(){
	var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
	if (clienW >= 1280) {
		$(".w980").each(function () {
                $(this).attr("class","w1200");
            });	
	}else{
		$(".w1200").each(function () {
                $(this).attr("class","w980");
            });			
		}	
	}

$(window).resize(function(){
	setTimeout(resizeWidth,500);
})


function showLogin(){
	$("#blo_2").hide();
	$("#blo_1").fadeIn(500);
	}
$(".tab_login a").click(function(){
	$(this).addClass("cur_5");
	$(this).siblings().removeClass();
	})