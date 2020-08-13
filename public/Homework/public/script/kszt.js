// JavaScript Document

$(".g_cheng ul li.sel8").children("span").animate( { height: "60px"}, 400);
$(".g_cheng ul li").hover(function(){
		$(this).children("span").animate( { height: "60px"}, 400);	
	},function(){
		$(this).children("span").animate( { height: "10px"}, 400);	
	
	})
$(".g_cheng ul li").click(function(){
		if($(this).attr("class").indexOf("green")>=0){
			$(this).css("background","#17ccb5");
			$(".more_zt").attr("class","m10 more_zt x_lv");
			$(".exam_subnav").attr("class","m10 exam_subnav x_lv");
			$(".exam_ziyuan").attr("class","m10 exam_ziyuan x_lv");
			$(".more_zt dd").css('background','#17ccb5');
		}
		if($(this).attr("class").indexOf("huang")>=0){
			$(this).css("background","#E1C231");
			$(".more_zt").attr("class","m10 more_zt x_huang");
			$(".exam_subnav").attr("class","m10 exam_subnav x_huang");
			$(".exam_ziyuan").attr("class","m10 exam_ziyuan x_huang");
			$(".more_zt dd").css('background','#e1c231');
		}
		if($(this).attr("class").indexOf("cheng")>=0){
			$(this).css("background","#f76c6c");
			$(".more_zt").attr("class","m10 more_zt");
			$(".exam_subnav").attr("class","m10 exam_subnav");
			$(".exam_ziyuan").attr("class","m10 exam_ziyuan");
			$(".more_zt dd").css('background','#f76c6c');
		}
		$(this).addClass("sel8");
		$(this).siblings().removeClass("sel8");
		$(this).siblings().css("background","#efefef");
		$(this).siblings().children("span").animate( { height: "10px"}, 400);
	}	
	)