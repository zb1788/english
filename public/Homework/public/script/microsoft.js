// JavaScript Document



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