 var client_id = 'f6dbb06d91764f1880c1e2d523b11ac2';
 var client_secret = '8b49fcacf86948efafa1073e68fbc72e';
 var response_type = "code";
  var grant_type = "authorization_code";
  var scope='snsapi_userinfo';
  var domain="https://auths.smartfengze.com:18891/oauth";
  var redirect_uri="https://en.czbanbantong.com/Elearn/fengze/index";
  var codeurl=domain+'/authorize?client_id='+client_id+'&response_type=code&grant_type=authorization_code&scope='+scope+'&redirect_uri='+redirect_uri;
   var id='',nickname='',phone='',gender='',photoUrl='',userToken='';

function getUserInfo(code){
  if(code!=null &&code!=''&& code!='undefined'){
     getAccess(code);
   }else{
     getCode();
   }
}
function getCode(){
   var codeurl=domain+'/authorize?client_id='+client_id+'&response_type=code&grant_type=authorization_code&scope='+scope+'&redirect_uri='+redirect_uri;
	  window.location.href=codeurl;
}
function getAccess(code){
	var tkurl=domain+'/token?code='+code+'&client_secret='+client_secret+'&grant_type=authorization_code&client_id='+client_id+'&redirect_uri='+redirect_uri;
	 $.ajax({
						url:tkurl,
						type:"post",
						success:function(data){
							token=data["access_token"];
              //alert(token);
							getInfo(token);
						},
						error:function(error){
              alert("获取access_token失败！"+code);
							return false;
						} 
	 });
}

function getInfo(token){
	var infourl='get_userinfo';
    $.post(infourl,{token:token,ran:Math.random()},function(result){
      if(result.flag == "0"){
        alert("获取用户信息失败");
      }
      else{
        getItem();
      }
    });
}
