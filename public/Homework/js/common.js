
//进行jQuery的封装
function getdata(url,getdata,type){
	//进行传参数的设置如果等于1的话表示post数据传输，如果是0的话表示使用get进行数据请求
	if(type==0){
		$.get(url,getdata,function(data){
          return data;
		});
	}else{
		$.post(url,getdata,function(data){
          return data;
		});
	}
}