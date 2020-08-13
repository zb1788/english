var tms=mapObj[userarea]['tms'];
var truename="";		
if(usertype==4){
	$.getJSON(tms+'/tms/interface/queryStudent.jsp?jsoncallback=?',{queryType:'byNames',usernames:username},function(data){
	if (data.rtnArray[0].schoolId == '') {
		return false;
	}
	else 
	{
		truename = data.rtnArray[0].realname;
	}	
	});		
}else{
	//¼Ò³¤µÇÂ¼
	$.getJSON(tms+'/tms/interface/queryStudent.jsp?jsoncallback=?',{queryType:'byParent',parentAccount:username},function(data){
		if (data.rtnArray[0].schoolId == '') {
			return false;
		}
		else 
		{
			truename = data.rtnArray[0].realname;
		}	
	});		
}