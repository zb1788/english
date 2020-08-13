//倒计时
function showTime(){
		time-=1;
		$('#time').html(time);
		// var type=$('a[name="suan"].cur1').attr('type');
		if (time==0){
			// var gene=$('.right span.cur').html();
			// if(gene=='口算'){
			// 	time=60;
			// }else{
			// 	if(type==2){
			// 		//分数
			// 		time=180;
			// 	}else{
			// 		time=120;
			// 	}

			// }
			$('#usetime').html(time);

			answerEnd();
			return false;
		}else{
			t=setTimeout("showTime();",1000);
			//t=setTimeout("showTime("+type+");",1000);
		}


}
//选中某个章节
function checkKecheng(obj){
	$('.j_bj').hide();
	clearTimeout(t);
	$('#status').val('end');
	var status=$('#status').val();
	if (status == 'start') {
		art.dialog.alert('请先结束练习！');
		return false;
	}
	else {
		var gene=$('.right span.cur').html();
		var type=$('a[name="suan"].cur1').attr('type');
		// if(gene=='口算'){
		// 	total_eq=15;
		// }else{
		// 	total_eq=10;
		// }

		if(gene=='口算'){
			time=60;
			totaltime=60;
		}else{
			if(type==2){
				//分数
				time=120;
				totaltime=120;
			}else{
				time=120;
				totaltime=120;
			}
		}
		//清空公共变量
			count=0;
			array=[];//存储试题信息
			arrAnswer=[];//存储答案信息
			usetime=0;//用时

			$('#time').html(time);
			$('#jindu').html('1');
			$('#total_eq').html(total_eq);

		$('#answer').html('');//清除答案
		$('#answer1').val('');
		$('#answer2').val('');
		$('#fenzi_3').val('');
		$('#fenmu_3').val('');

		var bid = $(obj).attr('bid');
		var kecheng=$(obj).children('b').html();
		var keypoint=$(obj).attr('keypoint');
		var example=$(obj).attr('example');
		var thinking=$(obj).attr('thinking');
		var type = $(obj).attr('type');
		var total = 15;

		$('#keypoint').html('');
		$('#example').html('');
		$('#thinking').html('');

		$('#kecheng').html(kecheng);
		$('#keypoint').html(keypoint);
		$('#example').html(example);
		$('#thinking').html(thinking);

		$('#kcjg').html(kecheng);

		$.get('../Klxsx/getinfo', {
				ran: Math.random(),
				bid: bid,
				total_eq:total_eq
			}, function(data){
				$.each(data, function(k, v){
					//alert(v.equation);
					//array.push(v.equation);
					var tmp={};
					tmp.id=v.id;
					tmp.name=v.equation;
					array.push(tmp);
				});

			});
	}
}
//用户输入答案
function submit(obj,type){
	var val=$(obj).attr('val');
	if(val=='+'||val=='-'||val=='*'||val=='/'||val=='('||val==')'){
		return false;
	}
	var str='';
	var str1='';
	if(type==1){
		str=$('#answer').html();
		if(val=='bac'){
				//str='';
				str = str.substr(0,(str.length-1));
		}else{
			if(checkAnswer(str)){
				str +=val;
			}
		}
		$('#answer').html(str);
	}else if(type==2){
		str=$('input[class="currentnow"]').val();
		if(val=='bac'){
				str='';
		}else{
			if(checkAnswer(str)){
				str +=val;
			}
		}
		$('input[class="currentnow"]').val(str);

	}else{
		str=$('input[class="currentnow"]').val();
		if(val=='bac'){
				str='';
		}else{
			if(checkAnswer(str)){
				str +=val;
			}
		}
		$('input[class="currentnow"]').val(str);

	}


}

//验证用户输入
function checkAnswer(str){
	//如果长度超过10位不能输入
	if(str.length>=6){
		return false;
	}else{
		return true;
	}
}

//式子拆分
//str为式子，kind为普通式子和分数式子
function chaifen(str,kind){
	var array1=[];
	var array2=[];
	var array3=[];
	var mp3=[];
	var fuhao='';
	if(str.indexOf("+")>0){
		array1=str.split('+');
		fuhao='jia.mp3';
	}else if(str.indexOf("-")>0){
		array1=str.split('-');
		fuhao='jian.mp3';
	}else if(str.indexOf("×")>0){
		array1=str.split('×');
		fuhao='cheng.mp3';
	}else if(str.indexOf("÷")>0){
		array1=str.split('÷');
		fuhao='chu.mp3';
	}
	if(kind==1){
		if(array1[0].indexOf(".")>0){
			array2=array1[0].split('.');
			mp3.push(array2[0]+'.mp3');
			mp3.push('dian.mp3');
			mp3.push(array2[1]+'.mp3');
		}else{
			mp3.push(array1[0]+'.mp3');
		}
		mp3.push(fuhao);
		if(array1[1].indexOf(".")>0){
			array3=array1[1].split('.');
			mp3.push(array3[0]+'.mp3');
			mp3.push('dian.mp3');
			mp3.push(array3[1]+'.mp3');
		}else{
			mp3.push(array1[1]+'.mp3');
		}
	}else{
		//分数
		array2=array1[0].split('/');
		array3=array1[1].split('/');
		mp3.push(array2[1]+'.mp3');
		mp3.push('fen.mp3');
		mp3.push(array2[0]+'.mp3');
		mp3.push(fuhao);
		mp3.push(array3[1]+'.mp3');
		mp3.push('fen.mp3');
		mp3.push(array3[0]+'.mp3');
	}

	return mp3;
}


//分数拆分
function strFormat(str){
	var array1=[];
	var array2=[];
	var array3=[];
	var fuhao='';
	if(str.indexOf("+")>0){
		array1=str.split('+');
		fuhao='+';
	}else{
		array1=str.split('-');
		fuhao='-';
	}

	array2=array1[0].split('/');
	array3=array1[1].split('/');
	return array2[0]+','+array2[1]+','+array3[0]+','+array3[1]+','+fuhao;
}


//答题结束
function answerEnd(){
	clearTimeout(t);
	var bid=$('a[name="suan"].cur1').attr('bid');
	var gene=$('.right span.cur').html();//获取当前是口算还是听算
	var type=$('a[name="suan"].cur1').attr('type');//获取当前算式类型：1普通算式2分数3余数
	var userid=$('#userid').val();
	var userarea=$('#userarea').val();

	// var uburl=mapObj[userarea]['ub'];
	var uburl = ubip;
	$.post('../Klxsx/getResult',{ran:Math.random(),userid:userid,userarea:userarea,bid:bid,type:type,gene:gene,uburl:uburl,json:JSON.stringify(arrAnswer)},function(data){

		$('#right').html(data.right);
		$('#wrong').html(data.wrong);
		(data.order==null)?data.order=0:data.order;
		$('#paiming').html(data.order);
		$('#starnum').val(data.star);


		var str='';
		for(i=0;i<data.star;i++){
			str+='<i class="ico2"></i>';
		}
		for(i=0;i<(5-data.star);i++){
			str+='<i class="ico1"></i>';
		}
		$('#stars').html(str);

		//更改右侧列表里的星星，暂时不用
		if(data.istop==1){
			var str2='';
			for(i=0;i<(5-data.star);i++){
				str2+='<i class="ico1"></i>';
			}
			for(i=0;i<data.star;i++){
				str2+='<i class="ico2"></i>';
			}
			$('a[name="suan"].cur1').children('label').html(str2);
		}


		if(data.status=='添加成功'){
			//奖励u币
			var ub = data.ubi;
			if(ub>0){
				var username=$.cookie('username');
				var usertype=$.cookie('usertype');
				var areaId=$.cookie('areaId');
				var areacode=$.cookie('areacode');
				var area=$.cookie('localAreaCode');
				//var url=mapObj[area]['ilearn'];

			}
			$('#ub').html(data.ubi);
		}else{
			$('#ub').html(0);
			//alert('u币添加失败!');
		}
	});

	$('#status').val('end');
	time=60;//公共时间重置为60
	usetime=0;
	count=0;//公共计数重置为0
	arrAnswer=[];//答案清空
	array=[];

	// if(gene=='口算'){
	// 	total_eq=15;
	// }else{
	// 	total_eq=10;
	// }
	// if(gene=='口算'){
	// 	time=60;
	// }else{
	// 	if(type==2){
	// 		//分数
	// 		time=180;
	// 	}else{
	// 		time=120;
	// 	}
	// }
	$('#time').html(totaltime);
	$("#answer").html('');
	$('#jindu').html('1');
	$('#total_eq').html(total_eq);

	$(".end").show();
	$(".end_t").hide();//确定结束练习页面
	$(".tijiao_t").show();//晒成绩
}



//切换口算听算
function show_tab(obj){
	$('a[name="suan"]').removeAttr('class');
	var bid=$(obj).attr('bid');
	$(obj).attr('class','cur');
	$(obj).siblings('span').removeAttr('class');
	if(bid=='2s_0'){
		$('#2s_0').children('ul').children('li').eq(0).children('a').attr('class','cur1');
		var obj1=$('a[name="suan"].cur1');
		checkKecheng(obj1);
		$('#2s_0').show();
		$('#2s_1').hide();
	}else{
		$('#2s_1').children('ul').children('li').eq(0).children('a').attr('class','cur1');
		var obj1=$('a[name="suan"].cur1');
		checkKecheng(obj1);
		$('#2s_1').show();
		$('#2s_0').hide();
	}
	//goBack();
	var obj=$('a[name="suan"].cur1');
	checkKecheng(obj);
	$(".end").hide();
	$(".j_bj").hide();
	$(".s_bj").show();
}

//获取排名信息
function pagelist(pageCurrent,page_size){
	var gene=$('#img').find('img:visible').attr('class');
	if(gene=='kou'){
		gene='口算';
	}else{
		gene='听算';
	}
	var type=$('#paihangbang').find('li[class="cur2"]').attr('type');
	//alert(gene+type);
	$.get('../Klxsx/getRank',{ran:Math.random(),gene:gene,type:type,pageCurrent:pageCurrent,page_size:page_size},function(data){
		var data = eval('('+data+')');
		var mypaiming=data.my.rank;
		var mytype=data.my.type;
		if(mytype=='class'){
			mytype='班级';
		}else if(mytype=='school'){
			mytype='学校';
		}else{
			mytype='全国';
		}

		var mystar=data.my.mystar;

		var myname=getTitle(mystar);

		(mystar==null)?mystar=0:mystar;
		(mypaiming==null)?mypaiming=0:mypaiming;
		$('#myname').html(myname);
		$('#mystar').html(mystar);
		$('#mypaiming').html(mypaiming);
		$('#mytype').html(mytype);

   		if (gene=='口算'){
			gene='stars_ks';
		}else{
			gene='stars_ts';
		}

		$('#myrank li:not(:first)').remove();
		$.each(data.now,function(k,v){
			var li=$('#demolist').children('li').clone();
			//alert(v.username);
			li.children('span').eq(0).children('b').html('第'+v.rank+'名');
			li.children('span').eq(0).children('font').html(v.username);
			li.children('span').eq(1).children('font').html(eval('v.'+gene));
			li.children('span').eq(2).children('b').html(getTitle(eval('v.'+gene)));
			li.appendTo('#myrank');
		});

		$('.manu').html('');
		$('.manu').html(data.page);

	});
}

function getTitle(mypaiming){
	var myname='';
	if(mypaiming>=0&&mypaiming<=50){
		myname='口算新手';
	}else if(mypaiming>=51&&mypaiming<=100){
		myname='口算能手';
	}else if(mypaiming>=101&&mypaiming<=150){
		myname='心算达人';
	}else if(mypaiming>=151&&mypaiming<=200){
		myname='神算子';
	}else{
		myname='口算王';
	}
	return myname;
}

function begLx(){
	var gene=$('.right span.cur').html();//获取当前是口算还是听算
	var type=$('a[name="suan"].cur1').attr('type');//获取当前算式类型：1普通算式2分数3余数
	if(gene=='口算'){
		$(".laba").hide();
		if(type==1){
			$("#shizi").html(array[0].name);
			$('#style1').css('display','block');
			$('#answer').css('margin-left','0px');
			$("#style11").show();
			$('#style2').hide();
			$('#style3').hide();
		}else if(type==2){
			var strs=strFormat(array[0].name);
			var strarray=[];
			strarray=strs.split(',');
			$('#fenzi_1').html(strarray[0]);
			$('#fenmu_1').html(strarray[1]);
			$('#fenzi_2').html(strarray[2]);
			$('#fenmu_2').html(strarray[3]);
			$('#fuhao').html(strarray[4]);

			$('#style1').hide();
			$('#style2').css('display','block');
			$('#style222').css('margin-left','0px');
			$("#style22").show();
			$('#style3').hide();
			$('#fenzi_3').focus();
			$('input[name="fenshu"]').removeAttr('class');
			$('#fenzi_3').attr('class','currentnow');
		}else{
			$("#shizi1").html(array[0].name);
			$('input[name="fenshu"]').removeAttr('class');
			$('#answer1').attr('class','currentnow');
			$("#style3").css('display','block');
			$('#style1').hide();
			$('#style2').hide();
			//$('#style3').show();
			$("#style33").show();
			$('#answer1').focus();
		}
	}else{
		$(".laba").show();
		//alert('aa');
		//听算
		if(type==1){
			$("#shizi").html(array[0].name);

			//var mp3arr=chaifen(array[0].name,1);//获取MP3数组
			var mp3arr=getmp3(array[0].name);//获取MP3数组

			$('#style1').css('display','block');
			$('#style2').hide();
			$('#style3').hide();
			$('#style11').hide();
			$('#answer').css('margin-left','40px');

		}else if(type==2){
			//var mp3arr=chaifen(array[0].name,2);
			var mp3arr=getmp3(array[0].name);

			var strs=strFormat(array[0].name);
			var strarray=[];
			strarray=strs.split(',');
			$('#fenzi_1').html(strarray[0]);
			$('#fenmu_1').html(strarray[1]);
			$('#fenzi_2').html(strarray[2]);
			$('#fenmu_2').html(strarray[3]);
			$('#style1').hide();
			$('#style2').css('display','block');
			$('#style3').hide();
			$('#style222').css('margin-left','40px');
			$('#style22').hide();
			$('#fenzi_3').focus();
			$('input[name="fenshu"]').removeAttr('class');
			$('#fenzi_3').attr('class','currentnow');
		}else{
			var mp3arr=getmp3(array[0].name);

			$("#shizi1").html(array[0].name);
			$('input[name="fenshu"]').removeAttr('class');
			$('#answer1').attr('class','currentnow');
			$("#style3").css({'margin-left':'60px','width':'150px'});
			$('#style1').hide();
			$('#style2').hide();
			$('#style3').show();
			$('#style33').hide();
			$('#answer1').focus();
		}
		play(mp3arr);
		//setTimeout(function(){play(mp3arr);},'1000');

	}
	showTime();
}

function play(mp3arr){
	//alert('ss');
	 var num=0;
	 var total=mp3arr.length;

	 jpstart(respath+'/uploadklx/klxsx/nan/'+mp3arr[num]);

	 $("#jplayer").bind($.jPlayer.event.ended, function(event) {
		num++;
		if(num<total){
			 jpstart(respath+'/uploadklx/klxsx/nan/'+mp3arr[num]);
		}
	  });
}

//获取式子的mp3
function getmp3(str){
	//var str='5×7';
	//alert(str);
	var mp3=[];
	var str1='';
	for(i=0;i<str.length;i++){
		if(!isNaN(Number(str.charAt(i)))){
			str1=str1+str.charAt(i);
		}else{
			//alert(str1);
			mp3.push(str1+'.mp3');
			if(str.charAt(i)=='+'){
				str1='jia.mp3';
			}else if(str.charAt(i)=="-"){
				str1='jian.mp3';
			}else if(str.charAt(i)=="×"){
				str1='cheng.mp3';
			}else if(str.charAt(i)=="÷"){
				str1='chu.mp3';
			}else if(str.charAt(i)=='.'){
				str1='dian.mp3';
			}else if(str.charAt(i)=='/'){
				str1='fen.mp3';
			}
			//alert(str1);
			mp3.push(str1);
			str1='';
		}
		if(i==(str.length-1)){
			//alert(str1);
			mp3.push(str1+'.mp3');
		}

	}
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;
    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}
	if(mp3.indexOf('fen.mp3')>0){
		var nownum=mp3.indexOf('fen.mp3');
		//alert(nownum);
		var up=mp3[nownum-1];
		mp3[nownum-1]=mp3[nownum+1];
		mp3[nownum+1]=up;

		mp3[nownum]='fen1.mp3';
	}else{
		//alert('cc');
	}

	if(mp3.indexOf('fen.mp3')>0){
		var nownum=mp3.indexOf('fen.mp3');
		//alert(nownum);
		var up=mp3[nownum-1];
		mp3[nownum-1]=mp3[nownum+1];
		mp3[nownum+1]=up;
	}
	var tmpstr=mp3.join('|');
	tmpstr=tmpstr.replace('fen1.mp3','fen.mp3');

	mp3=tmpstr.split('|');

	return mp3;

}




//停止播放
function jpclear() {
	$("#jplayer").jPlayer("clearMedia");
	$("#jplayer").jPlayer("stop");
	$("#jplayer").unbind($.jPlayer.event.ended);
	$("#jplayer").unbind($.jPlayer.event.progress);
}
//开始播放
function jpstart(mp3){
	$("#jplayer").jPlayer("setMedia", {
	mp3: mp3
	}).jPlayer("play");

}