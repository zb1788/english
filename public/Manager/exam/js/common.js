function checkNum(){
	if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
	if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
	if(event.returnValue==false) alert("只能输入数字");  
}


// function voice_add(obj){
//   var parent=$(obj).parent().html();
//   $("<p class='tt'><input name='textfield' class='tcontenttype' size='1' style='width:20px;' type='text'><textarea class='tcontent' name='textarea' cols='35' rows='1'></textarea><br>
// 	  <select name='voiceid' class='ttstype'>
// 		<option value='0'>美音男</option>
// 		<option value='1'>美音男1</option>
// 	  </select>
// 	  <select class='ttsstoptime'>
// 		<option value='0'>0秒</option>
// 		<option selected='selected' value='1'>1秒</option>
// 	  </select>
// 	  <img src='images/icon_add.png'><img src='images/icon_delete.png'><img src='images/icon_play.png'>
// 	</p>").insertAfter(parent);
// }


function voice_del(obj){
	var parent=$(obj).parent();
	parent.remove();
}

function items_add(obj){
	var parent=$(obj).parent();
	$("<p><input name='items' class='intext' value='A' type='radio'><input name='textfield' size='30' type='text'><img src='images/icon_add.png' width='20'><img src='images/icon_delete.png'></p>").insertAfter(parent);
}

function items_del(obj){
	var parent=$(obj).parent();
	parent.remove();
}


