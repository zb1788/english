var content_type = 'listen';
var def_postype = 0; // 0-课文章节;1-课文内容
var def_obj;
var issubmit = false; //是否已提交
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
    $('.catalog li:eq(0)').unbind('click').click(function(){
        $('#exams_info').hide();
        $('#exams_list').show();
        mp.clear();
        clearTimeout(mp3_progress);
    });
    show_ztexams_list(0,0,0,ztgradeid,0,0,0,0);
    get_list_choice();
}
function get_list_choice(){
    // $('#ztgrade a').unbind('click').click(function(){ 
    //     gradeid = $(this).attr('ztgradeid');
    //     yearid = $('#ztyear a[class=cur]').attr('yearid');
    //     typeid = $('#zttype a[class=cur]').attr('zttypeid');
    //     provinceid = $('#ztprovince a[class=cur]').attr('provinceid');
    //    gradeindex = $('#ztgrade a').index(this);
    //    typeindex = $('#zttype a').index($('#zttype a[class=cur]'));
    //    yearindex = $('#ztyear a').index($('#ztyear a[class=cur]'));
    //    provinceindex = $('#ztprovince a').index($('#ztprovince a[class=cur]'));
    //     show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex);
    // });
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
    
}
function show_ztexams_list(yearid,typeid,provinceid,gradeid,gradeindex,typeindex,yearindex,provinceindex) {
    $.get(ListenConter + 'get_zt_exams_list',{yearid:yearid,typeid:typeid,provinceid:provinceid,gradeid:gradeid},function(result){
        if(result == ''){
            result = '<h3></h3>没有找到相关试卷<h3>';
        }
        $('.sxBox a').removeClass('cur');
        $('#ztgrade a').removeClass('cur');
        $('#ztgrade a[ztgradeid='+ztgradeid+']').addClass('cur');
        $('#zttype a:eq('+typeindex+')').addClass('cur');
        $('#ztyear a:eq('+yearindex+')').addClass('cur');
        $('#ztprovince a:eq('+provinceindex+')').addClass('cur');
        $('.boxCon').html(result+'<div class="clearfix"></div>');
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
                if($('.catalog .level2_menu a[flag=children]').index(this) > 0){
                    if(isLogin()){
                        show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog .level2_menu a[flag=children]').index(this),0);
                    }
                }
                else{
                    show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog .level2_menu a[flag=children]').index(this),0);
                }
                
            });
            $(".catalog li a[flag=parent]:eq(0)").click();
        }
        else{
            $(".catalog li a[flag=children]").unbind('click').click(function(){
                def_obj = $(this);
                if($('.catalog li a[flag=children]').index(this) > 0){
                   if(isLogin()){
                        show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog li a[flag=children]').index(this),0);
                    } 
                }
                else{
                    show_exams_list($(this).attr('ks_code'),def_postype,$('.catalog li a[flag=children]').index(this),0);
                }
                
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
			temphtmlstr = '<h3>'+stemvalue.content+'</h3>';
		}
    	else{
            if(stemvalue.stem_children.length ==1){
                temphtmlstr = '<h3>'+stemvalue.content+'</h3>';
            }
            else{
                temphtmlstr = '<h3>'+stemvalue.content+'</h3>';
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
		temphtmlstr = '<h4>'+data.content+'</h4>';
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
    		itemshtml += '<dd><img src="'+versionimgurl+'/uploads/'+itemsvalue.content+'"><span><input stem_type="'+stem_type+'" name="'+quevalue.id+'" type="radio" value="'+itemsvalue.flag+'">'+itemsvalue.flag+'</dd>';
    	}
    });
    temphtmlstr += '<dl class="ti_xuanze '+heshuiclass+' '+itemsclass+'" id="'+quevalue.id+'">';
    if(quevalue.tcontent == ''){
    	temphtmlstr += '<b '+bclass+'>'+question_num+' </b>';
    }
    else{
    	temphtmlstr += '<dt>'+question_num+quevalue.tcontent+'</dt>';
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
    quevalue.tcontent = removeHTMLTaginput(quevalue.tcontent,'input');
	temphtmlstr = '';
	temphtmlstr += '<ul class="ti_tiankong">';
	temphtmlstr += '<li>'+question_num+quevalue.tcontent+'</li>';
	temphtmlstr +='</ul>';
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
	temphtmlstr += '<ul class="ti_panduan">';
    temphtmlstr += '<li>'+question_num+removeHTMLTagimg(quevalue.tcontent)+'<span><input stem_type="'+stem_type+'" name="'+quevalue.id+'" type="radio" value="1">√</span><span><input name="'+quevalue.id+'" type="radio" value="0">×</span></li>';
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
            paixuhtml1 += '<li><img src="'+versionimgurl+'/uploads/'+itemsvalue.content+'"><span>'+itemsvalue.flag+'.</span></li>';
	    }
        //alert(answer[itemskey].answer_num);
	   
    });
    paixuhtml1 += '<li>'+quevalue.tcontent+'</li>';
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
    temphtmlstr +='<h3>听力材料<a href="javascript:;" stem_type="'+stem_type+'" stemkey="'+stemkey+'" childstemkey="'+childstemkey+'" quekey="0" class="sound_single"></a></h3>';
    var flag_content = '';
    $(childstemtts).each(function(childttskey,childttsval){
            childttsval.tts_content = removeHTMLTagimg(childttsval.tts_content);
            if(childttsval.flag_content != ''){
                flag_content = childttsval.flag_content+':';
            }
            else{
                flag_content = '';
            }
            temphtmlstr+='<p id="childtt'+childttsval.id+'">'+flag_content+childttsval.tts_content+'</p>'; 
            //temphtmlstr +='<p>mp3地址:'+childttsval.tts_mp3+'</p>';
        });
    temphtmlstr +='</div>';
    return temphtmlstr;

}
//问题听力材料
function quettsHtml(quevalue,stem_type,stemkey,childstemkey,quekey){
	temphtmlstr = '';
	temphtmlstr ='<div class="txtList showtl" style="display:none;">';
    temphtmlstr +='<h3>听力材料<a href="javascript:;" stem_type="'+stem_type+'" stemkey="'+stemkey+'" childstemkey="'+childstemkey+'" quekey="'+quekey+'" class="sound_single"></a></h3>';
    var flag_content = '';
    quettsdata = quevalue.que_tts_noqn;
        $(quettsdata).each(function(quettskey,quettsval){
            quettsval.tts_content = removeHTMLTagimg(quettsval.tts_content);
            if(quettsval.flag_content != ''){
                flag_content = quettsval.flag_content+':';
            }
            else{
                flag_content = '';
            }
            temphtmlstr+='<p id="quetts'+quettsval.id+'">'+flag_content+quettsval.tts_content+'</p>';  
            //temphtmlstr +='<p>mp3地址:'+quettsval.tts_mp3+'</p>';
        });             
   	temphtmlstr +='</div>';
	return temphtmlstr;
}
//正确答案
function answerHtml(quevalue,id){
    var answer = '';
    var answer_num = '';
	temphtmlstr = '';
	temphtmlstr ='<div class="answer" id="divanswer'+quevalue.id+'" style="display:none">';
	temphtmlstr +='<strong id="strong'+quevalue.id+'">正确答案:</strong>';
    answerdata = quevalue.que_answer;
        $(answerdata).each(function(answerkey,answerval){
            answer = answerval.answer;
            if(answerdata.length >1){
                answer_num = answerval.answer_num+'. ';
            }
            else{
                answer_num = '';
            }
            if(quevalue.typeid == '3'){
                if(answerval.answer == '1'){
                answer = '√';
                }
                else if(answerval.answer == '0'){
                    answer = '×';
                }
            }
            
            // temphtmlstr+='<span id="answer'+quevalue.id+answer_num+'">'+answer_num+answer+'</span>';  
            temphtmlstr+='<span id="answer'+quevalue.id+answerkey+'">'+answer_num+answer+'</span>';
        });             
   	temphtmlstr +='</div>';
	return temphtmlstr;
}
/*
 * 获取试卷下详细信息
 */
function show_paper_info(examsid,showtype) {
        $('#exams_list').hide();
        $('#exams_info').html('');
        $('#exams_info').show();
       openloading();
    $.getJSON(ListenConter + 'listenshow', {examsid: examsid,unitid:def_unitid}, function(result) {
        hideloading();
    	htmlstr = '';
    	examsdata = result;
    	htmlstr += headerHtml(examsdata);
    	stemdata = result[0]['stem'];
    	$(stemdata).each(function(stemkey,stemvalue){
           // if(stemkey == 1){
    		htmlstr += '<div class="tiCon" stemid="tiCon'+stemvalue.id+'" stem_type="'+stemvalue.stem_type+'" name="parent">';
    		if(stemvalue.stem_type == '1'){	//独立题
    			htmlstr += stemHtml(stemvalue);
    			htmlstr += stemttsHtml(stemvalue);
    			quetiondata = stemvalue.question;
    			$(quetiondata).each(function(quekey,quevalue){
                    htmlstr += '<div class="tiCon ">';
                    htmlstr += '<div class="quetemp">';
    				if(quevalue.typeid == '1'){			//选择题
    					htmlstr += xuanzeHtml(quevalue,1);
    				}
    				else if(quevalue.typeid == '2'){	//如果这个小题是填空题
    					htmlstr += tiankongHtml(quevalue,1);
    				}
    				else if(quevalue.typeid == '3'){	//如果这个小题是判断题
    					htmlstr += panduanHtml(quevalue,1);
    					
    				}
    				else{		//如果这个小题是排序题
    					htmlstr += paixuHtml(quevalue,1);
    				}
                    htmlstr += answerHtml(quevalue,stemvalue.id);
                    htmlstr += quettsHtml(quevalue,stemvalue.stem_type,stemkey,0,quekey);
                    htmlstr +='</div>';
                    htmlstr +='</div>';
    			});	
    			
    			
    			
    		}
    		else{	//组合题
    			htmlstr += stemHtml(stemvalue);
    			htmlstr += stemttsHtml(stemvalue);
    			childstemdata = stemvalue.stem_children;
    			$(childstemdata).each(function(childstemkey,childstemvalue){
                    htmlstr += '<div class="tiCon" stemid="tiCon'+childstemvalue.id+'" name="child">';
                    htmlstr += '<div class="quetemp">';
    				htmlstr += childstemHtml(childstemvalue);
    				quetiondata = childstemvalue.question;
	    			$(quetiondata).each(function(quekey,quevalue){	
	    				if(quevalue.typeid == '1'){			//选择题
	    					htmlstr += xuanzeHtml(quevalue,2);
	    				}
	    				else if(quevalue.typeid == '2'){	//如果这个小题是填空题
	    					htmlstr += tiankongHtml(quevalue,2);
	    				}
	    				else if(quevalue.typeid == '3'){	//如果这个小题是判断题
	    					htmlstr += panduanHtml(quevalue,2);
	    					
	    				}
	    				else{		//如果这个小题是排序题
	    					htmlstr += paixuHtml(quevalue,2);
	    				}
                        htmlstr += answerHtml(quevalue,childstemvalue.id);
                        
	    			});	
                    htmlstr += childstemttsHtml(childstemvalue.stem_child_tts_nost,stemvalue.stem_type,stemkey,childstemkey);
                    htmlstr += '</div>';	
                    htmlstr += '</div>';
    			});
    		}

    		htmlstr += '<div class="clearfix"></div>';
    		htmlstr += '</div>';
            //}
    	});
        if(showtype != 'pcyulan'){
            htmlstr += '<div class="submit"><a href="javascript:void(0)" class="aBtn">提交答案</a></div>';
        }
    	
    	htmlstr += '</div>';
        $('#exams_info').html(htmlstr);
        $('#changebutton a:eq(0)').hide();
        $('#changebutton a:eq(1)').hide();
        if(showtype == 'pcyulan'){
            $('#changebutton a:eq(1)').show();
            $('#changebutton a:eq(2)').hide();          
        }
        $('#changebutton a').unbind('click').click(function(){   
            if(showtype != 'pcyulan'){
                $('#changebutton a').removeClass('cur');
                $(this).addClass('cur');
            }
            
            if($(this).text() == "听力材料"){
                mp.clear();
                mp.initindex();
                clearTimeout(mp3_progress);
                if(showtype == 'pcyulan'){
                   if($(this).hasClass('cur')){
                        $(this).removeClass('cur');
                        $('.answer').hide();
                        $('.txtList').hide(); 
                    }
                    else{
                        $(this).addClass('cur');
                        $('.answer').show();
                        $('.txtList').show();
                    } 
                }
                else{
                    $('.bgblack').css('color','#000');
                    $('.answer').show();
                    $('.txtList').show();
                }
                 
            }
            else{
                if($(this).text() == "返回"){
                    mp.clear();
                    mp.initindex();
                    clearTimeout(mp3_progress);
                    $('#exams_info').hide();
                    $('#exams_list').show();
                }
                else{
                    issubmit = false;
                    isuptime = true;
                    stem_isplay = false;
                    mp.clear();
                    mp.initindex();
                    clearTimeout(mp3_progress);
                    $('#minutes').html('00');
                    $('#seconds').html('00');
                    $('.answer strong').removeClass("bgblack");
                    $('.answer strong').removeAttr("style");
                    $('.answer span').removeAttr("style");
                    $('.answer span').removeClass("bgblack");
                    oRemain = 0;
                    $('.txtList').hide();
                    $('.showtl').hide();
                    $('.answer').hide();
                    $('.submit').hide();
                    $('#changebutton a:eq(0)').hide();
                    $('#changebutton a:eq(1)').hide();
                    $('.quetemp').removeClass('queclass');
                    $('.playBtn').removeClass('active');
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
                single_tts_init(examsdata,$(this).attr('stem_type'),$(this).attr('stemkey'),$(this).attr('childstemkey'),this,$(this).attr('quekey'));
            }
        });
        if(examstts_type != '1'){
        	$('.speed span:eq(0)').hide();     //如果是自助上传的mp3,隐藏速度选项
        }
        $('.submit').unbind('click').click(function(){
            exams_submit(examsdata,$('.tiCon[name=parent]'));
        })

    	loadlisten(examsdata);
    });
}
var timer = '';
var maxtime = '';
var isuptime = true;
function loadlisten(examsdata){
    issubmit = false;
    isuptime = true;
    stem_isplay = false;
	mp.index = 0;
    mp.clear();
    mp.initindex();
    clearTimeout(mp3_progress);
    clearInterval(timer);   
    $('#minutes').html('00');
    $('#seconds').html('00');
    oRemain = 0;
	$('.submit').hide();
	$('.playBtn').unbind('click').click(function() {   //联播按纽点击时事件
        $('.submit').hide();
        if ($(this).hasClass('active')) {  //正在播放时点击，则改为停止样式
            $(this).removeClass('active');
            $(this).attr('title', '播放');
            clearTimeout(mp3_progress);
            mp.pause();
        }
        else{                              //停止时点击，则改为播放样式
            $(this).addClass('active');
            $(this).attr('title', '暂停');
		    if(!issubmit){ 
                $('.txtList').hide();
                $('.answer').hide();
                $('#uptimer').show();
            }
		    else{
                $('#uptimer').hide();
            }
		   	//$('input[name!=speed]').removeAttr('checked');
		   	//$('input[type=text]').val('');
		   	//$('#timer').hide();   
            if(isuptime){
                timer = setInterval(updateTime, 1000);
                isuptime = false;
            }  
            begin_examsplay(examsdata);
           
        }
    });
}
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
    $('.submit').hide();
    issubmit = true;
    isuptime = false;
    mp.clear();
    mp.initindex();
    clearTimeout(mp3_progress);
    clearInterval(timer);
    $('#minutes').html('00');
    $('#seconds').html('00');
    oRemain = 0;
    $('.playBtn').removeClass('active');
    $('.quetemp').addClass('queclass');
    $('.answer').hide();
    $('#changebutton a:eq(0)').show();
    $('#changebutton a:eq(1)').show();
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
      // if(i == 3){
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
      // }
   });
   //alert($('.titleCon').attr('userscore'));
   $('.scoreCon').html('<p><span>总分：</span>'+examsdata[0].total_score+'分</p><p><span>得分：</span>'+$('.titleCon').attr('userscore')+'分</p><p><span>错误：</span>'+$('.titleCon').attr('errornum')+'题</p>');
   $('#changebutton a:eq(1)').show();
    $('#score').show();
    $('#below').show();
    minutes = $('#minutes').text();
    seconds = $('#seconds').text();
    complate_time = parseInt(minutes)*60+parseInt(seconds);
    //保存学习记录
    $.get(ListenConter + 'save_study_exams_info',{unitid:examsdata[0]['ks_code'],examsid:examsdata[0]['id'],score:$('.titleCon').attr('userscore'),errornum:$('.titleCon').attr('errornum'),complate_time:complate_time},function(result){     
    });
   
}
String.prototype.NoSpace = function() 
{ 
    return this.replace(/\s+/g, ""); 
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
                trueanswer = $.trim(trueanswer);
                inputvalue = $.trim($(this).val()).toLowerCase();
                if(inputvalue.NoSpace() == trueanswer.NoSpace()){
                    //alert(inputvalue+"=="+trueanswer); 
                        //userscore += parseInt(question_score);
                       // alert(userscore+'++'+question_score);
                        // $('#answer'+queval.id+answer_num).removeClass('greenBg');
                        // $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                        // $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                        // $('#answer'+queval.id+answer_num).addClass('greenBg');
                        userscore = accAdd(userscore,question_score);
                        
                        iswrong == false;
                        $('#strong'+queval.id).addClass('bgblack');
                        $('#answer'+queval.id+k).addClass('bgblack');
                            
                }
                else{
                    // $(this).css('color','red'); 
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg3');                                
                    // $('#answer'+queval.id+answer_num).addClass('greenBg2');
                    iswrong = true;
                    errornum++;
                    $('#divanswer'+queval.id).show();
                }            
            });  
        }
        else{           //选择题
            trueanswer = trueanswerarr[0].answer.toLowerCase();
            answer_num = trueanswerarr[0].answer_num;
            inputvalue = $('input[name='+queval.id+']:checked').val();
            inputvalue = $.trim(inputvalue).toLowerCase();
            if(inputvalue == trueanswer){
                  // alert(userscore+'++'+question_score);
                    userscore = accAdd(userscore,question_score);
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                    // $('#answer'+queval.id+answer_num).addClass('greenBg');
                    //alert(userscore);
                    iswrong == false;
                    $('#strong'+queval.id).addClass('bgblack');
                    $('#answer'+queval.id+'0').addClass('bgblack');      
                }
                else{
                    iswrong = true;
                    // if(queval.itemtype == '0'){
                    //     $('input[name='+queval.id+']:checked').parent().addClass('bg_wrong lie');
                    // }
                    // else{
                    //     $('input[name='+queval.id+']:checked').parent().parent().addClass('error');
                    // }
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg2');
                    // $('#answer'+queval.id+answer_num).removeClass('greenBg3');
                    // $('#answer'+queval.id+answer_num).addClass('greenBg2');
                    errornum++;
                    $('#divanswer'+queval.id).show();
                }
        } 
       
    });

    $('.titleCon').attr('userscore',userscore);
    $('.titleCon').attr('errornum',errornum);
}

function hide_score(){
    $('#score').hide();
    $('#below').hide();
}