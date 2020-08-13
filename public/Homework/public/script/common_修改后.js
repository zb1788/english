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
		$('#'+x).siblings().removeClass();
		$('#'+x).siblings().addClass("arr2 arr_d");	 
	}
}
function onMouseOverbox(x){
	window.clearTimeout(timeOut[x]);
	$('#'+x).fadeIn(300);
	$('#'+x).parent().siblings().css("position","static");
	var abc=$('#'+x).attr("class");
	if(abc.indexOf("drop")>=0){
		$('#'+x).siblings().removeClass();
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

function hid_tree(){
	var clienW =($(window).width()); //浏览器时下窗口可视区域宽度 
	if (clienW < 1280) {
		$(".dtree").hide();
		$(".kc_list").addClass('full');
		$(".kc_con").css('background-image','none');
		$(".rightfloat").fadeIn("200");	
		}
	}

$(".piclist li").hover(function(){
	$(this).children("div.tjB2").show();
	},function(){
		$(this).children("div.tjB2").hide();
		})

/*切换角色弹出层*/
	$("a.user").hover(function(){$(".login_con").fadeIn(500);})
	$("document").bind("click", function(){$(".login_con").hide(300);});
	$(".login_con").bind('mouseover', function(){$(document).unbind("click");});
	$(".login_con").bind('mouseout', function(){
		$(document).bind("click", function(){
			$(".login_con").hide(300);
		});
	})

$(".clos > a").click(function(){
	$(".login_con").hide();	
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
						'right': 120
					}, 3, function() {
						
					});
				});
			});
		});
	}	
}
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

function showLogin(){
	$("#blo_2").hide();
	$("#blo_1").fadeIn(500);
	}
$(".tab_login a").click(function(){
	$(this).addClass("cur_5");
	$(this).siblings().removeClass();
	})