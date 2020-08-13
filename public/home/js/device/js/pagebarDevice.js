(function ($) {
	$.pagebar=function(){};
	$.extend($.pagebar,{
		pageParams:{
			start:0,
			limit:10,
			total:0
		},
		pageSelectID:'',
		totalPage:0,
		currentPage:0,
		inint:function(){
			$.pagebar.initPage();
			$.pagebar.getToolsMenu();
		},
		itemClick:function(data){
			
		},
		initPage:function(){
			var params=$.pagebar.pageParams;
			this.totalPage=Math.ceil(params.total/params.limit);
			this.currentPage=Math.ceil((params.start+params.limit)/params.limit);
		},
		getToolsMenu:function(){
			totalPage = 0 == this.totalPage ? 1 : this.totalPage;
			pageParams=$.pagebar.pageParams;
			var next, prev,curPage=this.currentPage;
			prev = curPage - 1;
			next = curPage + 1;
			//当前的条数和总条数
			$('.tips').html(this.currentPage+"/"+pageParams.total);
			
			
			if (curPage == 1) {
				prev = curPage;
				$('#'+this.pageSelectID+' [p=prev]').addClass('nodisplay');
			} else {
				$('#'+this.pageSelectID+' [p=prev]').removeClass('nodisplay').attr('page',prev);
			}
			
			// 显示下一页
			if (curPage < totalPage) {
				$('#'+this.pageSelectID+' [p=n]').removeClass('nodisplay').attr('page',next);
	
			}else{
				$('#'+this.pageSelectID+' [p=n]').addClass('nodisplay');
			}
			
			//click事件
			$('#'+this.pageSelectID+' a').unbind('click');
			$('#'+this.pageSelectID+' a:not(.nodisplay)').one('click',function(){
				var nParams={
						start:($(this).attr('page')-1)*pageParams.limit
				};
				$.pagebar.itemClick(nParams);
			});
			$('#'+this.pageSelectID+' a.nodisplay').one('click',function(){
			});
		}
	});
})(jQuery);