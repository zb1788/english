	var username=null,truename = null,schoolname = null,usertype = 1;
	username=getUrlParam('username');
	usertype=getUrlParam('usertype');
	if(username){
	window.localStorage.setItem("dfusername",username);
	}else{
	username=window.localStorage.getItem("dfusername");
	}
	if(usertype){
	window.localStorage.setItem("dfusertype",usertype);
	}else{
	usertype=window.localStorage.getItem("dfusertype");
	}
    
    var param='username='+username+'&usertype='+usertype;
	var getUrl='https://tmszz.zzedu.net.cn';
	//var getUrl='https://tmsah.czbanbantong.com';
	var getSchool='https://tmszz.zzedu.net.cn/tms/interface/querySchool.jsp?queryType=bySchoolId&schoolId=';
	var eng='/Shuxue/Api/';
	var active='wxcs.html?'+param;
	var rank='xyfc.html?'+param;
	var index='index.html?'+param;
    var backUrl=location.href; 
    backUrl=encodeURIComponent(backUrl);
	
	$("#active").click(function(){
		 if(username == undefined || username == null){
			layerAlert('请先签到！');
			return false;
		 }
		 var signurl=eng+"getUser?username="+username;
			  $.getJSON(signurl,function(data){
				  if(data==null ){
					  layerAlert('请先签到！');
				  }
				  else if(data['issign']==1){
				    window.location.href=active;
				  }
			  }); 
	  });
	 function auth(){
		 var anadata = null;
		if(usertype==1)
		{    
			  getUrl+='/tms/interface/queryStudent.jsp?queryType=byNames&usernames='+username+'&jsoncallback=?';
			  anadata = function(data){
				var temp=data.rtnArray[0].realname;
				schoolId=data.rtnArray[0].schoolId;
                getSchoolName(schoolId);
				temp=encodeURIComponent(encodeURIComponent(temp));
				return temp;
			  }  
		}else if(usertype==2){
			  getUrl+='/tms/interface/queryStudent.jsp?queryType=byParent&parentAccount='+username+'&jsoncallback=?';
			  anadata = function(data){
				var temp=data.rtnArray[0].realname;
                schoolId=data.rtnArray[0].schoolId;
                getSchoolName(schoolId);
			    temp=encodeURIComponent(encodeURIComponent(temp));
				return temp;
			  }
		}
     //http://tmszz.zzedu.net.cn/tms/interface/querySchool.jsp?queryType=bySchoolId&schoolId=
	 //schoolName
		 $.getJSON(getUrl, function(data) {  
			truename = anadata(data);	
		 });
	 }
    function getSchoolName(schoolId){
		 getSchool+=schoolId+'&jsoncallback=?'; 
         $.getJSON(getSchool, function(data) {  
			schoolname=data.schoolName;
            schoolname=encodeURIComponent(encodeURIComponent(schoolname));
		 });
	}
	
     /*anadata = function(data){
				var temp=data.rtnArray[0].realname;
                schoolId=data.rtnArray[0].schoolId;
                getSchoolName(schoolId);
			    temp=encodeURIComponent(encodeURIComponent(temp));
				return temp;
			  }*/
	function userSign(username,truename,schoolname){
		//var eng='http://192.168.144.200/zx/Shuxue/Api/';
		var signurl=eng+"sign?username="+username+"&truename="+truename+"&schoolname="+schoolname;
	    $.getJSON(signurl,function(data){
		  if(data['status']==1){
			 layerAlert('签到成功！');
			 $("#sign img").attr("src","images/ssbtn.png");
			 $("#active").attr("href",active);
			 $("#sign").addClass("sign");
		  }
	   });
	}



    function getSign(callback){
		//var eng='http://192.168.144.200/zx/Shuxue/Api/';
		var signurl=eng+"getUser?username="+username;
			  $.getJSON(signurl,function(data){
				  if(data==null ){
					  if( (typeof callback) == 'function')callback();
				  }else if(data['issign']==1){
					  $("#sign").addClass("sign");
				      $("#sign img").attr("src","images/ssbtn.png");
				  }
			  }); 
	  }

     
	 
	function checkTimeResult(gradeid,type){
		 var url = eng+"checkExam?username="+username; 
		 $.getJSON(url,function(data){
			if(data["data"][0]==0 || data["data"][0]==1){
			 $("#getresult").empty();
			}
			else{
			getResult(gradeid,type);
			}
		 });
	  }
     function getResult(gradeid,type){
			var getresult=eng+'getRank?gradeid='+gradeid+"&type="+type;
			if(type==2){
			var str='<tr><th width="20%">排名</th><th width="40%">姓名</th></tr>';
			}else{
			var str='<tr><th width="20%">排名</th><th width="40%">姓名</th><th width="40%">分数</th></tr>';
			}
			$("#getresult").empty();
			$.getJSON(getresult, function(data) {
				if(data.length==0){return false;}
				 else{
				var num=data.length;
				for(var i=0;i<num;i++)
				{
					truename=data[i].truename;
				   if(type==0){
					score=parseInt(data[i].score0,10);
					score=score*2;
                    score='<td>'+score+'</td>';
					}
					else if(type==1){
					score=parseInt(data[i].score1,10);
					score=score*2;
					score='<td>'+score+'</td>';
					}
					else if(type==2){
					score='';
					}
						if(i<3){
						str+='<tr><td><img src="images/'+(i+1)+'.png" width="25" /></td><td>'+truename+'</td>'+score+'</tr>';}
						else{
						str+='<tr><td>'+(i+1)+'</td><td>'+truename+'</td>'+score+'</tr>';}
				}
					 $("#getresult").append(str);
				 }
                                 $("#getresult").append('<tr><td>&nbsp;</td><td></td></tr>');
			  });
       }

		function checkData(type){
			 var url = eng+"checkExam?username="+username; 
			 $.getJSON(url,function(data){
				if(data["data"][type]==0){
				  layerAlert('考试还未开始！');
				}
				else if(data["data"]["isup"]==1){
				  layerAlert('抱歉，你未进入决赛！');
				}
				else if(data["data"][type]==2){
					  layerAlert('考试已结束！');
					 }
				else{
					   beginExam(type);
					 }
			 });
         }

	   function checkTime2(){
		 var url = eng+"checkExam?username="+username; 
		 $.getJSON(url,function(data){
			  if(data["data"][0]==0){
			  layerOpen();
			  }
		 });
	  }

	   function checkTime(){
		 var url = eng+"checkExam?username="+username; 
		 $.getJSON(url,function(data){
			 if(data["data"][0]==1)
			 {
			   $("#paperA img").attr("src","images/gin.png");
			 }
			 else if(data["data"][0]==2){
			   $("#paperA img").attr("src","images/yjs.png");
			 }
			 if(data["data"][1]==1)
			 {
			   $("#paperB img").attr("src","images/gin.png");
			 }
			 else if(data["data"][1]==2){
			   $("#paperB img").attr("src","images/yjs.png");
			 }
		 });
	   }

	   function beginExam(type){
		   var beginurl=eng+"startExams?username="+username+"&type="+type;
			 $.getJSON(beginurl,function(data){
				  if(data['code']==1){
				  begin="/static_ksk/playksk.html?username="+username+"&type="+type+"&backUrl="+backUrl;
				  window.location.href=begin;
				  }
			  }); 
		}


	   function getGrade(username){
		var url = eng+"getUser?username="+username;
			  $.getJSON(url,function(data){
				  var grade=data["grade"];
                 if(grade==null || grade=="" || grade=="undefined"){   
					 layerOpen();
					 }  
					 else{
                  switch (grade) {
						case "0002":
							gradename = "二年级";
							break;
						case "0003":
							gradename = "三年级";
							 break;
						case "0004":
							gradename = "四年级";
							 break;
						case "0005":
							gradename = "五年级";
							 break;
						case "0006":
							gradename = "六年级";
		            } 
					  $("#grade").html(gradename);
				 }
			      });
	   }
	   function postGrade(username,grade){
		  var url = eng+"setUserGrade?username="+username+"&grade="+grade; 
			$.getJSON(url,function(data){
				if(data['code']=='1'){
				  flag=true;
				}
			}); 
	   }
	   function layerAlert(con){
		 layer.open({ 
			   content:con,
			   btn: '确定' 
			 })
		  }
	  function getUrlParam(name) {
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
			var r = window.location.search.substr(1).match(reg);
			if (r!=null) return unescape(r[2]);
			return null;
		  }
