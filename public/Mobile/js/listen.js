var content_type = 'listen';
var def_postype = 0; // 0-课文章节;1-课文内容
var def_obj;
var examsdatas;
/**
 * 听力训练页面加载完成后执行事件
 */
 var contenttype = 4;

function ListenLoad() {
   $('nav a:eq(3)').addClass('cur');
    $("#jplayer").jPlayer({
            swfPath: '/public/public/js',
            wmode: "window",
            supplied: "mp3",
            preload: "none",
            volume: "1"
    });
    mp = new myplay();
    $("#banben").click(function(){
        showOrHide('top','show',content_type);
    });   
    welcome(content_type);
    get_user_set_version(content_type,true);
    $('#versionok').click(function(){
        var r_grade,r_volume,r_version;
        r_grade =$('.nianji li[class=sel5] a').attr('r_grade');
        r_volume =$('.xueqi li[class=sel5] a').attr('r_volume');
        r_version = $('.banben li[class=sel] a').attr('r_version');
        //alert(r_grade+r_volume+r_version);
        set_version_sumbit(r_grade,r_volume,r_version,content_type);
    }); 
}
function ListentopicLoad(){
    $('nav a:eq(4)').addClass('cur');
    $("#jplayer").jPlayer({
            swfPath: '/public/public/js',
            wmode: "window",
            supplied: "mp3",
            preload: "none",
            volume: "1"
    });
    welcome(content_type);
    mp = new myplay();
    var gradeid,typeid,yearid,provinceid,gradeindex,typeindex,yearindex,provinceindex;
     $('#ztgrade a').unbind('click').click(function(){ 
        gradeid = $(this).attr('ztgradeid');
        yearid = $('#ztyear a[class=cur]').attr('yearid');
        typeid = $('#zttype a[class=cur]').attr('zttypeid');
        provinceid = $('#ztprovince a[class=cur]').attr('provinceid');
       gradeindex = $('#ztgrade a').index(this);
       typeindex = $('#zttype a').index($('#zttype a[class=cur]'));
       yearindex = $('#ztyear a').index($('#ztyear a[class=cur]'));
       provinceindex = $('#ztprovince a').index($('#ztprovince a[class=cur]'));
        show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex);
    });
    $('#zttype a').unbind('click').click(function(){  
        gradeid =$('#ztgrade a[class=cur]').attr('ztgradeid');
        yearid = $('#ztyear a[class=cur]').attr('yearid');
        typeid = $(this).attr('zttypeid');
        provinceid = $('#ztprovince a[class=cur]').attr('provinceid');
        gradeindex = $('#ztgrade a').index($('#ztgrade a[class=cur]'));
        typeindex =$('#zttype a').index(this);
        yearindex = $('#ztyear a').index($('#ztyear a[class=cur]'));
        provinceindex = $('#ztprovince a').index($('#ztprovince a[class=cur]'));
        show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex);
    });
    $('#ztyear a').unbind('click').click(function(){
     
         gradeid =$('#ztgrade a[class=cur]').attr('ztgradeid');
        yearid = $(this).attr('yearid');
        typeid =  $('#zttype a[class=cur]').attr('zttypeid');
        provinceid = $('#ztprovince a[class=cur]').attr('provinceid');
         gradeindex = $('#ztgrade a').index($('#ztgrade a[class=cur]'));
        typeindex = $('#zttype a').index($('#zttype a[class=cur]'));
        yearindex = $('#ztyear a').index($(this));
        provinceindex = $('#ztprovince a').index($('#ztprovince a[class=cur]'));
        show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex);
    });
    $('#ztprovince a').unbind('click').click(function(){
        gradeid =$('#ztgrade a[class=cur]').attr('ztgradeid');
        yearid = $('#ztyear a[class=cur]').attr('yearid');
        typeid =  $('#zttype a[class=cur]').attr('zttypeid');
        provinceid = $(this).attr('provinceid');
        gradeindex = $('#ztgrade a').index($('#ztgrade a[class=cur]'));
        typeindex = $('#zttype a').index($('#zttype a[class=cur]'));
        yearindex = $('#ztyear a').index($('#ztyear a[class=cur]'));
        provinceindex = $('#ztprovince a').index($(this));
       // $(this).addClass('cur');
        show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex);
    });
    show_ztexams_list(0,0,0,0,0,0,0,0);
    $('.catalog li:eq(0)').unbind('click').click(function(){
        $('#exams_info').hide();
        $('#exams_list').show();
    })
}
function show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex) {
    $.get(ListenConter + 'get_zt_exams_list',{yearid:yearid,typeid:typeid,provinceid:provinceid,gradeid:gradeid},function(result){
        if(result.length > 121){
            $('.sxBox a').removeClass('cur');
            $('#ztgrade a:eq('+gradeindex+')').addClass('cur');
            $('#zttype a:eq('+typeindex+')').addClass('cur');
            $('#ztyear a:eq('+yearindex+')').addClass('cur');
            $('#ztprovince a:eq('+provinceindex+')').addClass('cur');
            $('.boxCon').html(result);
        }
        else{
             //show_simple_mess('没有找到相关试卷!','no');
             open_msg_div('没有找到相关试卷');
        }
    });
}
/**
 * 获取试卷列表所在单元
 */
function show_unit_exams_list(postype) {
    $('#exams_info').hide();
    $('#exams_list').show();
    isfold = false;
    mp.clear();
    
    $.getJSON(ListenConter + 'unitinfo_exams', {r_grade: def_gradeid, r_volume: def_termid, r_version: def_versionid}, function(result) { 
        //alert(result.length);

        htmlstr = '';
        $('.catalog').hide();
        $(result).each(function(i,val){
            if(val.is_unit == '1'){
                isfold = true;
            }
            if(val.is_unit == '1'){
                 htmlstr+='<a ks_code="'+$(this).attr('ks_code')+'" ks_name_short="'+$(this).attr('ks_name_short')+'" is_unit="'+$(this).attr('is_unit')+'" href="javascript:void(0);" ></a>';
            }
            else{
                if(val.examscount > 0){
                    htmlstr+='<a ks_code="'+$(this).attr('ks_code')+'" ks_name_short="'+$(this).attr('ks_name_short')+'" is_unit="'+$(this).attr('is_unit')+'" href="javascript:void(0);" ></a>';
                }
            }
           
        });
        //alert(htmlstr)
        $('.catalog').html(htmlstr);
        htmlstr = '';
        if(!isfold){
            $('.catalog a').each(function(i){
                htmlstr+='<li><a ks_code="'+$(this).attr('ks_code')+'" href="javascript:;" flag="children" >'+$(this).attr('ks_name_short')+'<b>></b></a></li>';
            });
        }
        else{
             htmlstr = get_fold_menu();
        }
        $('.catalog').show();
        $('.catalog').html(htmlstr);
        fold_menu();
        if(isfold){
            $(".catalog .level2_menu a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog .level2_menu a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=parent]:eq(0)").click();
        }
        else{
            $(".catalog li a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog li a[flag=children]').index(this),0);
            });
            $(".catalog li a[flag=children]:eq(0)").click();
        }
    });
}
/*
 * 获取该单元下试卷列表
 */
function show_exams_list(unitid,postype,unitnum,chapterpage) {
    if ($('.playBtn').hasClass('active')) {  //正在播放时点击，则改为停止样式
        $('.playBtn').removeClass('active');
        $('.playBtn').attr('title', '播放');
        clearTimeout(mp3_progress);
        mp.pause();
        clearInterval(timer);
    }
    if (!islogin && unitnum)
    {
        open_tishi_div();
    }
    else{
        def_unitid = unitid;   
        $.get(ListenConter + 'get_exams_list', {unitid: unitid}, function(result) {
            //alert(result.length);
            if(result.length > 121){
                $('#exams_info').hide();
                $('#exams_list').html('');
                $('#exams_list').show();
                if(isfold){
                    $('.level2_menu a[flag=children]').removeClass('sel21');
                    $(def_obj).addClass('sel21');
                }
                else{
                    $('.catalog li a[flag=children]').removeClass('cur');
                    $(def_obj).addClass('cur');
                }
                //$('.catalog li a:eq(' + unitnum + ')').addClass('cur');
                $('#exams_list').html(result);
            }
            else{
                show_simple_mess('该章节下没有试卷资源，请浏览其他章节!','no');
            }
            
        });
    }
}
//去除HTML tag
function removeHTMLTagimg(str) {

   //str=str.replace(/ /ig,'');//去掉 
    return str;
}
//去除HTML tag
function removeHTMLTaginput(str) {

   //str=str.replace(/ /ig,'');//去掉 
    return str;
}

//替换填空题题干答案标识为input框
function content_replace(content,type,stem_type,queid){
	var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
	if(type == '1'){
		content = content.replace(patt,'_____');	//选择题替换成下划线
	}
	else{
		content = content.replace(patt,'<input stem_type="'+stem_type+'" name="'+queid+'" type="text">');	//填空题替换成输入框
	}
	
	return content;
}
//试卷头内容html
function headerHtml(examsdata){
	temphtmlstr = '';
	if(examsdata[0]['exams_times'] =='0'){
		examsdata[0]['exams_times'] = 20;
	}
	examstts_type = examsdata[0]['tts_type'];
	temphtmlstr = '<div class="titleCon"><div class="fl"><h2>'+examsdata[0]['name']+'</h2><a href="javascript:;" class="playBtn none"></a></div><div class="fr  none" id="changebutton"><a  href="javascript:;" class="aBtn cur">重新测验</a><a  href="javascript:;" class="aBtn">听力材料</a><a href="javascript:;" class="aBtn">返回</a></div><div class="clearfix"></div></div>';
    	temphtmlstr += '<div>'+examsdata[0]['header_content']+'</div>';
    	temphtmlstr +='<div class="speed"><span>速度：<input name="speed" type="radio" value="0"><label>慢速</label><input name="speed" type="radio" value="1" checked><label>正常</label><input name="speed" type="radio" value="2"><label>快速</label></span><span style="float:right;"><label id="totalscore">满分:'+examsdata[0]['total_score']+'分&nbsp;&nbsp;</label><span id="uptimer" display:none"><span id="minutes">00</span> : <span id="seconds">00</span></span></span></div>';
    	temphtmlstr += '<div class="boxCon">';
    	return temphtmlstr;
}

//题干html
function stemHtml(stemvalue){
	temphtmlstr = '';
    var str = '';
    if(stemvalue.questypeid ==1 || stemvalue.questypeid == 3){
        str = '小题';
    }
    else{
        str = '空';
    }
		stemvalue.content = removeHTMLTagimg(stemvalue.content);
		if(stemvalue.stem_type == '1'){
			temphtmlstr = '<h3 class="active questigan"><a href="javascript:void(0);">'+stemvalue.content+'<i class="icon-volume-up" onclick="javascript:listen(\'getStemTts\','+stemvalue.id+','+stemvalue.examsid+');"></i></a></h3>';
		}
    	else{
            if(stemvalue.stem_children.length ==1){
                temphtmlstr = '<h3 class="active questigan"><a href="javascript:void(0);">'+stemvalue.content+'<i class="icon-volume-up" onclick="javascript:listen(\'getStemTts\','+stemvalue.id+','+stemvalue.examsid+');"></i></a></h3>';
            }
            else{
                temphtmlstr = '<h3 class="active questigan"><a href="javascript:void(0);">'+stemvalue.content+'<i class="icon-volume-up" onclick="javascript:volice('+stemvalue.id+');"></i></a></h3>';
            }
    		
    	}
	return temphtmlstr;
}
//子题干内容
function childstemHtml(data){
	temphtmlstr = '';
    var str = '';
    if(data.questypeid ==1 || data.questypeid == 3){
        str = '小题';
    }
    else{
        str = '空';
    }
	data.content = removeHTMLTagimg(data.content);
	if(data.content != ''){
		temphtmlstr = '<h4 class="questigan">'+data.content+'</h4>';
	}
	

	return temphtmlstr;
}
//选择题html
function xuanzeHtml(quevalue,stem_type){
	temphtmlstr = '';
	itemsdata = quevalue.items;
   
    var heshuiclass = '';
    var bclass = '';
    if(quevalue.display == '0'){
        heshuiclass = 'shu';
        bclass = 'class="h90"';
    }
    else{
        heshuiclass = '';
         bclass = '';
    }
    var question_num = '';
    if(quevalue.question_num != ''){
        question_num = quevalue.question_num+'. ';
    }
    else{
        question_num = '';
    }
	quevalue.tcontent = content_replace(quevalue.tcontent,1);
    quevalue.tcontent = removeHTMLTagimg(quevalue.tcontent);
    itemshtml = '';
    $(itemsdata).each(function(itemskey,itemsvalue){
    	if(quevalue.itemtype == '0'){	//选项是文字
    		itemsclass = '';
    		itemshtml += '<dd><i><input stem_type="'+stem_type+'"  name="'+quevalue.id+'" type="radio" value="'+itemsvalue.flag+'">'+itemsvalue.flag+'.<span>'+itemsvalue.content+'</span></i></dd>';
    	}
    	else{	//选项是图片
    		itemsclass = 'ti_tupian';
    		itemshtml += '<dd><img width="200px" height="150px" src="/uploads/'+itemsvalue.content+'"><span><input stem_type="'+stem_type+'" name="'+quevalue.id+'" type="radio" value="'+itemsvalue.flag+'">'+itemsvalue.flag+'</dd>';
    	}
    });
    temphtmlstr += '<dl class="ti_xuanze '+heshuiclass+' '+itemsclass+'" id="'+quevalue.id+'">';
    if(quevalue.tcontent == ''){
    	temphtmlstr += '<b '+bclass+'>'+question_num+' </b>';
    }
    else{
    	temphtmlstr += '<dt class="questigan">'+question_num+quevalue.tcontent+'</dt>';
    }
    temphtmlstr += itemshtml;
    temphtmlstr += '</dl>';
    return temphtmlstr;
}




//填空题html
function tiankongHtml(quevalue,stem_type){
    var question_num = '';
    if(quevalue.question_num != ''){
        question_num = quevalue.question_num+'. ';
    }
    else{
        question_num = '';
    }
	quevalue.tcontent = content_replace(quevalue.tcontent,2,stem_type,quevalue.id);
    //quevalue.tcontent = removeHTMLTaginput(quevalue.tcontent,'input');
	temphtmlstr = '';
	temphtmlstr += '<p>'+question_num+quevalue.tcontent+'</p>';
	return temphtmlstr;
}
//判断题html
function panduanHtml(quevalue,stem_type){
	temphtmlstr = '';
    var question_num = '';
    if(quevalue.question_num != ''){
        question_num = quevalue.question_num+'. ';
    }
    else{
        question_num = '';
    }
    //alert(quevalue.tcontent.replace("<p>","").replace("</p>","").replace("<br>",""));
	//alert(quevalue.tcontent);
	temphtmlstr += '<ul class="ti_panduan">';
    temphtmlstr += '<li>'+question_num+quevalue.tcontent+'<span><input stem_type="'+stem_type+'" name="'+quevalue.id+'" type="radio" value="1">√</span><span><input name="'+quevalue.id+'" type="radio" value="0">×</span></li>';
    temphtmlstr +='</ul>';
    return temphtmlstr;
}
//排序题html
function paixuHtml(quevalue,stem_type){
    var question_num = '';
    if(quevalue.question_num != ''){
        question_num = quevalue.question_num+'. ';
    }
    else{
        question_num = '';
    }
	temphtmlstr = '';
	itemsdata = quevalue.items;
	paixuhtml1 = '';
    paixuhtml2 = '';
    var answer = quevalue.que_answer;
    $(itemsdata).each(function(itemskey,itemsvalue){
	    if(quevalue.itemtype == '0'){	//选项是文字
	    	paixuhtml1 += '<li>'+itemsvalue.flag+'.'+itemsvalue.content+'</li>';
	    }
	   	else{	//选项是图片
            paixuhtml1 += '<li><img width="200px" height="150px" src="/uploads/'+itemsvalue.content+'"><span>'+itemsvalue.flag+'.</span></li>';
	    }
        //alert(answer[itemskey].answer_num);
	   
    });
    $(answer).each(function(answerkey,answerval){
         paixuhtml2 += '<li>'+answerval.answer_num+'.<input stem_type="'+stem_type+'" type="text" name="'+quevalue.id+'" size="5" maxlength="5"></li>';
    });
    if(quevalue.itemtype == '0'){
        temphtmlstr += '<ul class="ti_list">';
        temphtmlstr += paixuhtml1;
        temphtmlstr += '</ul>';
    }
    else{
        temphtmlstr += '<ul class="ti_tupian">';
        temphtmlstr += paixuhtml1;
        temphtmlstr += '</ul>';
    }
    temphtmlstr += '<div class="clearfix"></div>';
    temphtmlstr += '<ul class="ti_paixu">';
    temphtmlstr += paixuhtml2;
    temphtmlstr += '</ul>';
    temphtmlstr += '<div class="clearfix"></div>';
    return temphtmlstr;
}
//题干听力材料
function stemttsHtml(stemvalue){
	temphtmlstr = '';
	stemttsdata = stemvalue.stem_tts;
	$(stemttsdata).each(function(i,value){
		temphtmlstr += '<h3 class="showtl" style="display:none;" stemid="'+stemvalue.id+'" class="examsmp3" tts_type="'+value.tts_type+'" stoptimes = "'+stemvalue.stoptimes+'" playtimes = "'+stemvalue.question_playtimes+'" st_flag="'+value.st_flag+'" mp3value="'+value.tts_mp3+'"></h3>';
	});
	return temphtmlstr;

}
//组合题听力材料
function childstemttsHtml(childstemtts,stem_type,stemkey,childstemkey){
    temphtmlstr = '';
    temphtmlstr ='<div class="txtList showtl" style="display:none;">';
    temphtmlstr +='<h3>听力材料<a href="javascript:;" stem_type="'+stem_type+'" stemkey="'+stemkey+'" childstemkey="'+childstemkey+'" class="sound_single"></a></h3>';
    $(childstemtts).each(function(childttskey,childttsval){
            childttsval.tts_content = removeHTMLTagimg(childttsval.tts_content);
            temphtmlstr+='<p id="childtt'+childttsval.id+'">'+childttsval.tts_content+'</p>'; 
            temphtmlstr +='<p>mp3地址:'+childttsval.tts_mp3+'</p>';
        });
    temphtmlstr +='</div>';
    return temphtmlstr;

}
//问题听力材料
function quettsHtml(quetiondata,stem_type,stemkey,childstemkey){
	temphtmlstr = '';
	temphtmlstr ='<div class="txtList showtl" style="display:none;">';
    temphtmlstr +='<h3>听力材料<a href="javascript:;" stem_type="'+stem_type+'" stemkey="'+stemkey+'" childstemkey="'+childstemkey+'" class="sound_single"></a></h3>';
    $(quetiondata).each(function(i,value){
    	quettsdata = value.que_tts;
    	$(quettsdata).each(function(quettskey,quettsval){
            quettsval.tts_content = removeHTMLTagimg(quettsval.tts_content);
    		temphtmlstr+='<p id="quetts'+quettsval.id+'">'+quettsval.tts_content+'</p>';  
            temphtmlstr +='<p>mp3地址:'+quettsval.tts_mp3+'</p>';
    	});
    });             
   	temphtmlstr +='</div>';
	return temphtmlstr;
}
//正确答案
function answerHtml(quetiondata,id){
    var answer = '';
    var answer_num = '';
	temphtmlstr = '';
	temphtmlstr ='<div class="answer" id="answer'+id+'" style="display:none">';
	temphtmlstr +='<strong>正确答案</strong>';
    $(quetiondata).each(function(i,value){
    	answerdata = value.que_answer;
    	$(answerdata).each(function(answerkey,answerval){
            answer = answerval.answer;
            answer_num = answerval.answer_num;
            if(answerval.answer == '1'){
                answer = '√';
            }
            else if(answerval.answer == '0'){
                answer = '×';
            }
    		temphtmlstr+='<span>'+answer_num+'. '+answer+'</span>&nbsp;&nbsp;';  
    	});
    });             
   	temphtmlstr +='</div>';
	return temphtmlstr;
}
/*
 * 获取试卷下详细信息
 */
function show_paper_info(examsid) {
        $('#exams_list').hide();
        $('#exams_info').html('');
        $('#exams_info').show();
       //openloading();
    $.getJSON('listenshow', {examsid: examsid,ran:Math.random()}, function(result) {
        //hideloading();
    	htmlstr = '';
    	examsdata = result;
        examsdatas=result;
    	headerHtml(examsdata);
    	stemdata = result[0]['stem'];
        var len=stemdata.length;
        //alert(len);
    	$(stemdata).each(function(stemkey,stemvalue){
    		htmlstr += '<div class="con"><ul><div class="tiCon" stemid="tiCon'+stemvalue.id+'" stem_type="'+stemvalue.stem_type+'" name="parent">';
			//alert(stemvalue.stem_type);
    		if(stemvalue.stem_type == '1'){	//独立题
    			htmlstr += stemHtml(stemvalue);
    			htmlstr += stemttsHtml(stemvalue);
    			quetiondata = stemvalue.question;
                var html="";
    			$(quetiondata).each(function(quekey,quevalue){	
    				if(quevalue.typeid == '1'){		//选择题
					if(quekey==0){
						htmlstr +='<div class="tixing xuanzeti">';
					}
					    
    					htmlstr += xuanzeHtml(quevalue,1);
                        //html="</div>";
    				}
    				else if(quevalue.typeid == '2'){	//如果这个小题是填空题
					if(quekey==0){
						htmlstr +='<div class="tixing tiankongti">';
					}
					
                        
                        htmlstr += tiankongHtml(quevalue,1);
                        //html="</div>";
    					
    				}
    				else if(quevalue.typeid == '3'){	//如果这个小题是判断题
                        
						if(quekey==0){
						htmlstr +='<div class="tixing panduanti">';
					}
						//alert(panduanHtml(quevalue,1));
                        htmlstr += panduanHtml(quevalue,1);
						//html="</div>";
    				}
    				else{		//如果这个小题是排序题
                        
						if(quekey==0){
						htmlstr +='<div class="tixing paixvti">';
					}
                        htmlstr += paixuHtml(quevalue,1);
                        //html="</div>";
    					
    				}

    			});
				html="</div>";
                htmlstr +=html;


    			
    			//htmlstr += quettsHtml(quetiondata,stemvalue.stem_type,stemkey,0);
    			htmlstr += answerHtml(quetiondata,stemvalue.id);
                //htmlstr += '<div class="boxCon"><p><a href="#" class="btn bRed">加入错题本</a></p></div>';
                
    		}
    		else{	//组合题
    			htmlstr += stemHtml(stemvalue);
    			htmlstr += stemttsHtml(stemvalue);
    			childstemdata = stemvalue.stem_children;
    			$(childstemdata).each(function(childstemkey,childstemvalue){
                    htmlstr += '<div class="tiCon" stemid="tiCon'+childstemvalue.id+'" name="child">';
    				htmlstr += childstemHtml(childstemvalue);
    				quetiondata = childstemvalue.question;
	    			$(quetiondata).each(function(quekey,quevalue){	
	    				if(quevalue.typeid == '1'){        //选择题
						if(quekey==0){
							htmlstr +='<div class="tixing xuanzeti">';
						}
                        htmlstr += xuanzeHtml(quevalue,1);
                        
                    }
                    else if(quevalue.typeid == '2'){    //如果这个小题是填空题

                            if(quekey==0){
							htmlstr +='<div class="tixing tiankongti">';
						}
                            htmlstr += tiankongHtml(quevalue,1);
                        
                        
                        
                    }
                    else if(quevalue.typeid == '3'){    //如果这个小题是判断题
                   
                            
							if(quekey==0){
							htmlstr +='<div class="tixing panduanti">';
						}
                            htmlstr += panduanHtml(quevalue,1);
                        
                       
                        
                        
                    }
                    else{       //如果这个小题是排序题
                        
                            if(quekey==0){
							htmlstr +='<div class="tixing paxvti">';
						}
                            htmlstr += paixuHtml(quevalue,1);
                        
                        
                        
                    }

                });
				html="</div>";
                htmlstr +=html;
	    			
	    			htmlstr += answerHtml(quetiondata,childstemvalue.id);
                    htmlstr += '</div>';
    			});
    		}
            if(stemkey==(len-1)){
                htmlstr += '<div class="boxCon"><p><a href="javascript:void(0);" class="btn bBlue submit" id="submit">提交答案</a></p></div>';
            }
    		htmlstr +="</div></ul></div>";
    	});
    	//
    	htmlstr += '</div>';
        $('#iScroll-bd').html(htmlstr);
        var obj=$(".questigan").find("img");
        //进行题干的图片的展示
        try{
        	var obj=$(".questigan").find("img");
			$(obj).each(function(key,value){
				if($(value).width()>(window.screen.availWidth-50)){
					$(this).attr("width",(window.screen.availWidth-50)+"px")
				}
			});	
        }catch(e){
        	
        }
        $("#jPlayer").append('<script type="text/javascript">TouchSlide( { slideCell:"#iScroll",titCell:".hd ul", endFun:function(i){ var bd = document.getElementById("iScroll-bd");bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";if(i>0)bd.parentNode.style.transition="200ms";}} );</script> ');
        $('#changebutton a').unbind('click').click(function(){
            $('#changebutton a').removeClass('cur');
            $(this).addClass('cur');
            if($(this).text() == "听力材料"){
               // $('.txtList').show();
               $('.showtl').show();
               
            }
            else{
                if($(this).text() == "返回"){
                    mp.clear();
                    clearTimeout(mp3_progress);
                    $('#exams_info').hide();
                    $('#exams_list').show();
                }
                else{
                   // $('.txtList').hide();
                    $('.showtl').hide();
                    $('.answer').hide();
                    $('.tiCon').removeClass('wrong');
                    $('input[name!=speed]').removeAttr('checked');
                    $('input[type=text]').val('');
                }
            }
        });
        $('.txtList a').unbind('click').click(function(){
            clearTimeout(mp3_progress);
            if($(this).hasClass('active')){
                mp.pause();
                $(this).removeClass('active');
            }
            else{
                $('.txtList a').removeClass('active');
                $(this).addClass('active');
                single_tts_init(examsdata,$(this).attr('stem_type'),$(this).attr('stemkey'),$(this).attr('childstemkey'),this);
            }
        });
        if(examstts_type != '1'){
        	$('.speed').hide();
        }
        // $('#submit').click(function(){
        //     alert("fsadfasd");
        //     //exams_submit(examsdata,$('.tiCon[name=parent]'));
        // });

    	loadlisten(examsdata);
    });
}
var timer = '';
var maxtime = '';
function loadlisten(examsdata){
	mp.index = 0;
    mp.clear();
	//$('.submit').hide();
   // $('.answer').show();
	$('.playBtn').unbind('click').click(function() {   //联播按纽点击时事件
        if ($(this).hasClass('active')) {  //正在播放时点击，则改为停止样式
            $(this).removeClass('active');
            $(this).attr('title', '播放');
            clearTimeout(mp3_progress);
            mp.pause();
            clearInterval(timer);
        }
        else{                              //停止时点击，则改为播放样式
            $(this).addClass('active');
            $(this).attr('title', '暂停');
		   	//$('.submit').hide();
		   	$('.answer').hide()
		   	$('.tiCon').removeClass('wrong');
		   	$('input[name!=speed]').removeAttr('checked');
		   	$('input[type=text]').val('');
		   	$('#timer').hide();
            $('.txtList').hide();
            $('.answer').hide();
		   	$('#updatetimer').show();
            begin_examsplay(examsdata);
            timer = setInterval(updateTime, 1000);
        }
    });
}
//考试计时
//考试计时
function format(a)
{
    return a.toString().replace(/^(\d)$/,'0$1')
}

var oRemain = 0;
function updateTime ()
{
 oRemain++;
 $('#minutes').html(format(parseInt(oRemain / 60)));
 $('#seconds').html(format(parseInt(oRemain %60)));
}
//带小数点运算
function accAdd(arg1,arg2){ 
    var r1,r2,m; 
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0} 
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0} 
    m=Math.pow(10,Math.max(r1,r2)) 
    return (arg1*m+arg2*m)/m 
} 

//试卷提交
var userscore =0;
var errornum = 0;
function exams_submit(examsdata,obj){
    mp.clear();
    //mp.initindex();
    //clearTimeout(mp3_progress);
    //clearInterval(timer);
    //$('.playBtn').removeClass('active');
    //$('i').removeClass('bg_wrong lie');
    //$('.answer span').removeClass('greenBg');
    //$('.ti_xuanze').removeClass('wrong');
    userscore = 0;
    errornum = 0;
    var stemarray = examsdata[0]['stem'];
    var stemdata = '';
    var childstemdata = '';
    var quevaluearr = '';
    var minutes = '';
    var seconds = '';
    var complate_time = '';
    $('.tiCon').removeClass('wrong');
   $(obj).each(function(i){
        iswrong = false;
        stemdata = stemarray[i];
      // if(i >=0){
        if($(this).attr('stem_type') == '1'){       //独立大题 
            quevaluearr = stemdata.question;
            submit_result(quevaluearr,stemdata.id,stemdata.question_score);
        }
        else{                    //组合大题
            childstemdata = stemdata.stem_children;
            $(childstemdata).each(function(j,clildstemval){
                quevaluearr = clildstemval.question;
                submit_result(quevaluearr,clildstemval.id,clildstemval.question_score);
            });
        }
       //}
   });
   //alert($('.titleCon').attr('userscore'));


   //$('.scoreCon').html('<p><span>总分：</span>'+examsdata[0].total_score+'分</p><p><span>得分：</span>'+$('.titleCon').attr('userscore')+'分</p><p><span>错误：</span>'+$('.titleCon').attr('errornum')+'题</p>');
   // $('#changebutton a:eq(1)').show();
   //  $('#score').show();
    //$('#below').show();
    $('#iScroll-bd').append('<div class="con" id="show_score"><ul><div class="tiCon"><h3 class="active"><a href="#">成绩</a>  <a href="javascript:autopage(0,false);"></a></h3><div class="chengji"><p>总分：'+examsdata[0].total_score+'分</p><p>得分：'+userscore+'分</p></div></div></ul></div>');
    $('#submit').hide();
    var pagenum=$(".con").length-2;
    autopage(0,false);
    minutes = $('#minutes').text();
    seconds = $('#seconds').text();
    complate_time = parseInt(minutes)*60+parseInt(seconds);
    //保存学习记录
    $.get('save_study_exams_info',{unitid:examsdata[0]['ks_code'],examsid:examsdata[0]['id'],score:userscore,errornum:$('.titleCon').attr('errornum'),complate_time:complate_time},function(result){     
    });
}
//判断对错，计算结果
function submit_result(quevaluearr,stemid,question_score){
    var userinput = '';
    var trueanswerarr = '';
    var inputvalue ='';
    var trueanswer = '';
    var answer_num = '';
    var iswrong = false;
    $(quevaluearr).each(function(j,queval){
   
        trueanswerarr = queval.que_answer;
        userinput = $('input[name='+queval.id+']');
        if(queval.typeid == '2' || queval.typeid == '4'){ //填空题
            $(userinput).each(function(k){
                trueanswer = trueanswerarr.length > 1 ? trueanswerarr[k].answer.toLowerCase() : trueanswerarr[0].answer.toLowerCase();
                answer_num = trueanswerarr.length > 1 ? trueanswerarr[k].answer_num : trueanswerarr[0].answer_num;
                inputvalue = $.trim($(this).val()).toLowerCase();           
                if(inputvalue == ''){
                        iswrong = true;
                        //$('#'+queval.id).addClass('wrong');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                        $('#answer'+queval.id+answer_num).addClass('greenBg3');
                        errornum++;
                }
                else{

                    if(inputvalue == trueanswer){
                        //userscore += parseInt(question_score);
                       // alert(userscore+'++'+question_score);
                        $('#answer'+queval.id+answer_num).removeClass('greenBg');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                        $('#answer'+queval.id+answer_num).addClass('greenBg');
                        userscore = accAdd(userscore,question_score);
                        
                        iswrong == false;
                            
                    }
                    else{
                        $(this).css('color','red'); 
                        $('#answer'+queval.id+answer_num).removeClass('greenBg');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                        $('#answer'+queval.id+answer_num).removeClass('greenBg3');                                
                        $('#answer'+queval.id+answer_num).addClass('greenBg2');
                        iswrong = true;
                        errornum++;
                    }  
                }               
            });  
        }
        else{           //选择题
            trueanswer = trueanswerarr[0].answer.toLowerCase();
            answer_num = trueanswerarr[0].answer_num;
            inputvalue = $('input[name='+queval.id+']:checked').val();
            inputvalue = $.trim(inputvalue).toLowerCase();
            if(inputvalue == ''){
                iswrong = true;
                //$('#'+queval.id).addClass('wrong');
                $('#answer'+queval.id+answer_num).removeClass('greenBg');
                $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                $('#answer'+queval.id+answer_num).addClass('greenBg3');
                errornum++;
            }
            else{
                if(inputvalue == trueanswer){
                  // alert(userscore+'++'+question_score);
                    userscore = accAdd(userscore,question_score);
                    $('#answer'+queval.id+answer_num).removeClass('greenBg');
                    $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                    $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                    $('#answer'+queval.id+answer_num).addClass('greenBg');
                    //alert(userscore);
                    iswrong == false;              
                }
                else{
                    iswrong = true;
                    if(queval.itemtype == '0'){
                        $('input[name='+queval.id+']:checked').parent().addClass('bg_wrong lie');
                    }
                    else{
                        $('input[name='+queval.id+']:checked').parent().parent().addClass('error');
                    }
                    $('#answer'+queval.id+answer_num).removeClass('greenBg');
                    $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                    $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                    $('#answer'+queval.id+answer_num).addClass('greenBg2');
                    errornum++;
                }
            }
        } 
        $('#answer'+stemid).show();
    });
    
}

function hide_score(){
    $('#score').hide();
    $('#below').hide();
}


function autopage(page,issumbit){
            if(!issumbit){
             $('#iScroll-bd').parent().css('overflow','visible');
            }
            TouchSlide( { slideCell:"#iScroll",
            titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
            autoPage:true, //自动分页
            endFun:function(i){ //高度自适应
                var bd = document.getElementById("iScroll-bd");
                bd.parentNode.style.height = bd.children[i].children[0].offsetHeight+"px";
               
                if(i>0)bd.parentNode.style.transition="200ms";//添加动画效果
                
                                mp.clear();
                                if(issumbit){
                                    $('#iScroll-bd').parent().css('overflow','hidden');
                                    mp.index = i;
                                    $('#submit').hide();
                                    //alert(mp.index);
                                    changepaper(i);
                                    //BodyScroll($('a[type=paper]:eq('+i+')'));
                                   // var paperid =$('a[type=paper]:eq('+i+')').attr('paperid');
                                    //settimer = setTimeout(function(){listen_play(paperid);},1000);
                                }
                                else{
                                    //BodyScroll($('#show_score'));
                                    
                                }
                                
            },
                        startFun:function(){
                            mp.clear();
                        },
                        defaultIndex:page           
        });
}


//听力材料播放、












function getParameter(param) {
    var query = window.location.search;
    var iLen = param.length;
    var iStart = query.indexOf(param);
    if (iStart == -1)
        return null;
    iStart += iLen + 1;
    var iEnd = query.indexOf("&", iStart);
    if (iEnd == -1)
        return query.substring(iStart);
    return query.substring(iStart, iEnd);
}
function myplay() {
    var oplay = new Object();
    oplay.index = 0;
    oplay.queindex = 0;
    oplay.que2index = 0;
    oplay.que3index = 0;
    oplay.url = "";
    oplay.repeat = 1;
    oplay.play = function(mp3) {
        oplay.clear();
        $("#jplayer").jPlayer("setMedia", {mp3: mp3}).jPlayer("play");
    };

    oplay.pause = function() {
        $("#jplayer").jPlayer("pause");
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    }
    oplay.clear = function() {
        $("#jplayer").jPlayer("stop");
        $("#jplayer").jPlayer("clearMedia");
        //$("#jplayer").data("SpeakMP3Value", "0");  
        $("#jplayer").unbind($.jPlayer.event.ended);
        $("#jplayer").unbind($.jPlayer.event.progress);
    };
    return oplay;
}
mp = new myplay();