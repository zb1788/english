    var showloading=function(){
      document.getElementById("over").style.display = "block";
        document.getElementById("layout").style.display = "block";
    }
    var hideloading=function(){
      document.getElementById("over").style.display = "none";
        document.getElementById("layout").style.display = "none";
    }
    var learnText = {}; //推荐方式  
    var learnTextUnitdata = function(url,loc){
        getUnitData(url,loc);
    };
    function GetRequest() {
      var url = location.search; //获取url中"?"符后的字串
      var theRequest = new Object();
      if (url.indexOf("?") != -1) {
          var str = url.substr(1);
          strs = str.split("&");
          for(var i = 0; i < strs.length; i ++) {
              theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
          }
      }
      return theRequest;
    }
   function iGetInnerText(testStr) {
      var resultStr = testStr.replace(/\ +/g, ""); //去掉空格
      resultStr = testStr.replace(/[ ]/g, "");    //去掉空格
      resultStr = testStr.replace(/[\r\n]/g, ""); //去掉回车换行
      return resultStr;
   }
   function setTip(content){
    var tip = document.getElementById('tips');
    tip.innerHTML = content;
    tip.style.display = 'block';
    setTimeout(function(){ 
        tip.style.display = 'none';
    }, 5000);
   }
   function setTip2(content){
    var tip = document.getElementById('tips');
    tip.innerHTML = content;
    tip.style.display = 'block';
    setTimeout(function(){ 
        tip.style.display = 'none';
    }, 2000);
   }
    //初始化单词学习的列表
   function initReadTextList(url,listobj){
        var Request = new Object();
        Request = GetRequest();
        ks_code=Request["ks_code"];
        //alert(ks_code);
        $.ajax({
            type:'GET',
            url:url,
            data:{ks_code:ks_code},
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                ReadTextList = data;
                //console.log(JSON.stringify(ReadTextList.mp3list));
                //遮罩消失
                hideloading();
                //初始化列表
                var htmlstr = '';
                $.each(data.data,function(k,v){
                    //alert('sss');
                    htmlstr += '<ul>';
                    if(v.isevaluate == 1){

                       htmlstr += '<div class="model bLine"><span learntype="0"><a class="cur">读课文</a></span><span learntype="1"><a>练口语</a></span></div>'; 
                    }  
                       htmlstr += '<div class="child" chapter="'+v.chapter+'" chapterid = "'+v.id+'">';
                       htmlstr += ' <div class="text">';
                       $.each(v.texts,function(tk,tv){
                        var enbeforecon = '';
                        var cnbeforecon = '';
                        if(tv.enbefore==""){  
                            enbeforecon = tv.encontent;
                            cnbeforecon = tv.cncontent;
                        }else{
                            enbeforecon = tv.enbefore+":"+tv.encontent;
                            cnbeforecon = tv.enbefore+":"+tv.cncontent;
                        }
                        htmlstr += '<p mp3="'+tv.url+'"><span><i class="icon-uniE60C actY" contentid="'+tv.id+'" chapterid="'+tv.chapterid+'" typeid="1" ks_code="'+ks_code+'" source="1"></i></span><span class="en"><em>'+enbeforecon+'</em><em class="cn" style="display:none">'+cnbeforecon+'</em></span></p>';
                       });
                       htmlstr +='<span class="spanheight" style="height: 80px; clear: both; display: block;"></span>';
                       htmlstr += ' </div>';
                   htmlstr += '<div class="kouyu posR" style="display: none;">';
                    var singlecon = '';
                    var singleconen = '';
                   if(data.data[k].texts[0].enbefore == ''){
                        singlecon = data.data[k].texts[0].encontent;
                        singleconen = data.data[k].texts[0].cncontent;
                   }
                   else{
                        singlecon = data.data[k].texts[0].enbefore+":"+data.data[k].texts[0].encontent;
                        singleconen = data.data[k].texts[0].enbefore+":"+data.data[k].texts[0].cncontent;
                   }
                   //alert(data.data[k].texts[0].encontent);
                  
                   htmlstr += ' <p mp3="'+data.data[k].texts[0].url+'" contentid="'+data.data[k].texts[0].id+'" chapterid="'+data.data[k].texts[0].chapterid+'" typeid="1" ks_code="'+ks_code+'" source="1"><span class="en">'+singlecon+'</span><span class="ch" style="display:none">'+singleconen+'</span></p>';
                   htmlstr += '<a class="kypage"><strong>1</strong>/'+data.data[k].texts.length+'</a>';
                   if(data.data[k].texts.length == 1){
                        htmlstr += '<div class="nrBtn"><a class="off">上一页</a><a class="off">下一页</a></div>';
                   }
                   else{
                        htmlstr += '<div class="nrBtn"><a class="off">上一页</a><a class="" onclick="changelearn(2,\'next\','+data.data[k].texts.length+','+k+')">下一页</a></div>';
                   }
                   
                   htmlstr += '</div>';
                    
                   htmlstr += '</div>';
                       htmlstr += ' </ul>';

                });
                listobj.html(htmlstr);

                
            },
            error:function(xhr,type){
                hideloading();

            }
        })
    }


    //单词朗读
    function wordAloundEvent(obj){
        alert("afasdfasdfasdf");
        
    }

    //收藏操作
    function wordCollectEvent(){

    }


    //口语优信回调的接口函数
function getVoicePath(path,type){
    //alert(type + "sss="+path);
    //window.UXinJSInterface.hideProgress();
    //window.UXinJSInterface.showAlert("上传成功");
     var Request = new Object();
        Request = GetRequest();
        ks_code=Request["ks_code"];
    if(type == 1){ //只上传不评分
       // alert(ks_code);
        $.ajax({
            type:'POST',
            timeout: 5000,
            url:'../../Yuwen/Text/uploadVoiceToDb',
            data:{
                path:path,
                ks_code:ks_code,
                filename:chaptertitle,
                recordType:recordType,
                apptype:apptype,
                ran:Math.random()
            },
            async:true,
            success:function(data){
                try{
                    //alert(data.msg);
                    setTip(data.msg);
                }catch(e){
                    setTip("您的APP版本过低部分功能无法使用，请升级至最新版本。");
                }
            },
            error:function(e){

            }
        })
    }
    else{    
        var readid= textreadarr.readid;
        var content=textreadarr.content;
        var datatype=textreadarr.datatype;
        var chapterid=textreadarr.chapterid;
        var ks_code=textreadarr.ks_code;
        var url="../Text/gettextsmartScore";
        showloading();
       //window.UXinJSInterface.hideProgress();
        //UXinJSInterface.showAlert("正在评分,请稍后....");
        $.ajax({
            type:'POST',
            timeout: 30000,
            url:url,
            data:{
                content:content,
                ks_code:ks_code,
                filename:path,
                contentid:readid,
                chapterid:chapterid,
                type:datatype,
                ran:Math.random()
            },
            async:true,
            dataType:'json',
            success:function(data){
                var score=(data.score);
                UXinJSInterfaceSpeech.sendScoreResult(score*1);
                hideloading();
                UXinJSInterface.hideProgress();
            },
            error:function(xhr,type,errorThrown){
                hideloading();
                UXinJSInterface.hideProgress();
            //异常处理；
            //mask.close();
            setTip("评分超时，请稍等一会儿再提交");
            }
        });
    }
}