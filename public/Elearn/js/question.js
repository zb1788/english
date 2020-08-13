function InitLlstData(url,listobj){
	var Request = new Object();
	Request = GetRequest();
	var course_id = Request['course_id'];
	$.getJSON(url,{course_id:course_id},function(data){
		$('.topH4').html('<span class="fl mL10">单项选择题（共'+data.length+'题，共'+data.length+'分）</span><span class="fr mR10"><font class="redFont">1</font>/'+data.length+'</span>');
		var htmlstr = '';
		$(data).each(function(i,val){
			//if(i == 1){
				htmlstr += '<div class="con" trueanswer="'+val.answer+'" que_id="'+val.id+'">';
			 htmlstr += '<ul>';
			 htmlstr += '<div class="pad10"><div class="wk-ti">'+val.content+'</div></div>';
			 htmlstr += '<ul class="bdc">';

			 if(val.type == 1){
			 	$(val.itemsdata).each(function(k,itemsval){
			 		if (val.itemtype == 0) {
			 			htmlstr +='<li useranswer="'+itemsval.flag+'"><a>'+itemsval.flag+'.</a>'+itemsval.content+'&nbsp;&nbsp;<a><i useranswer="'+itemsval.flag+'"></i></a></li>';
			 		}
			 		else{
			 			htmlstr +='<li useranswer="'+itemsval.flag+'"><a>'+itemsval.flag+'.</a><img src="'+pic_url+itemsval.content+'" class="w100" />&nbsp;&nbsp;<a><i useranswer="'+itemsval.flag+'"></i></a></li>';
			 		}
			 	});
			 }
			 else{
			 	htmlstr +='<li useranswer="1"><a><i useranswer="1"></i></a>√</li><li useranswer="0"><a><i useranswer="0"></i></a>×</li>';
			 }
			 htmlstr += '</ul>';
			 htmlstr += '</div>';
			//}
			 
			
		});
		//console.log(htmlstr);
		$(listobj).html(htmlstr);
		TouchSlide({ slideCell:"#iScroll",
            switchLoad:"_src",
                endFun:function(i){ //高度自适应
                    //var bd = document.getElementById("iScroll-bd");
                    //bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";
                    var winH = $(window).height() - 90;
              		var divH = $('#iScroll-bd').children().eq(i).children().height();
		              if (winH > divH){
		              	$('#iScroll-bd').parent().height(winH);
		              	$('#iScroll-bd').children().eq(i).children().height(winH);
		              }else {
		              	$('#iScroll-bd').parent().height(divH);
		              }        
                   // if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
                    $('.redFont').html((i+1));
                    //console.log($('div.con').eq(i).find('ul.bdc li'));
                    var trueanswer = $('div.con').eq(i).attr('trueanswer');
                    var que_id = $('div.con').eq(i).attr('que_id');
                    var score = 0;
                    $('ul.bdc li').removeClass('cur').removeClass('error');
                    $('ul.bdc li i').removeClass('icon-correct02').removeClass('icon-error02');
                    $('div.con').eq(i).find('ul.bdc li').unbind('click').bind('click',function(){
        				$('ul.bdc li').removeClass('cur').removeClass('error');
        				if($(this).attr('useranswer')== trueanswer){
        					$(this).addClass('cur');
        					$(this).find('i').addClass('icon-correct02');
        					score = 1;
        				}
        				else{
        					$(this).addClass('error');
        					$(this).find('i').addClass('icon-error02');
        					$('div.con').eq(i).find('ul.bdc li[useranswer="'+trueanswer+'"]').addClass('cur');
        					$('div.con').eq(i).find('i[useranswer="'+trueanswer+'"]').addClass('icon-correct02');
        					score = 0;
        				}
        				//console.log(que_id);
        				$.getJSON('saveUseranswer',{app_id:Request['app_id'],course_id:Request['course_id'],que_id:que_id,score:score,useranswer:$(this).attr('useranswer')})
        			});
                    
                }
        });
		hideloading();
	});
}