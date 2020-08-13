//tab
function Show_Tab_List2(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"s_"+i).style.display="none";
			
			
			document.getElementById(where+"Tab_"+i).className="";
			}else{
			document.getElementById(where+"s_"+i).style.display="block";
		
		
			document.getElementById(where+"Tab_"+i).className="cur_hz";
			}
	}
}// JavaScript Document

function Show_Tab_wk(num,active,where){
	for(var i=0;i<num;i++){
		if(i!=active){
			document.getElementById(where+"y_"+i).style.display="none";
			
			
			document.getElementById(where+"Tab_wk_"+i).className="";
			}else{
			document.getElementById(where+"y_"+i).style.display="block";
		
		
			document.getElementById(where+"Tab_wk_"+i).className="cur_wk";
			}
	}
}// JavaScript Document