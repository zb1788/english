function Show_Tab_redLine(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			document.getElementById(where+"Tab_"+i).className="";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
			document.getElementById(where+"Tab_"+i).className="nav1";
			}
	}
}// JavaScript Document

var LastID = 1; 
function DoMenu(id) 
{
	var obj = document.getElementById("ChildMenu" + id); 
	var obj2 = document.getElementById("z" + id);  
	if (id != LastID)
	{
		if (obj) obj.className = (obj.className.toLowerCase() == "expanded" ? "collapsed" : "expanded"); 
		if (obj2) 
		{
			obj2.className = (obj2.className.toLowerCase() == "jiaarr" ? "jianarr" : "jiaarr"); 
		}
		
		if (document.getElementById("ChildMenu" + LastID)) document.getElementById("ChildMenu" + LastID).className = "collapsed"; 
		if (document.getElementById("z" + LastID)) document.getElementById("z" + LastID).className = "jiaarr"; 

	}else {
		if (obj) obj.className = (obj.className.toLowerCase() == "collapsed" ? "expanded" : "collapsed"); 
		if (obj2) 
		{
			obj2.className = (obj2.className.toLowerCase() == "jianarr" ? "jiaarr" : "jianarr"); 
		}
		}
		LastID = id;
} 
