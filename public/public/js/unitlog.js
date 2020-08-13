function GetEnRequest() {
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

var Enrequests = new Object();
Enrequests = GetEnRequest();


var endata={};
endata.ks_code=Enrequests["ks_code"]==undefined?"0":Enrequests["ks_code"];
endata.ks_name=decodeURI(decodeURI(Enrequests["ks_short_name"]));
endata.gradeid=Enrequests["gradeid"];
endata.subjectid=Enrequests["subjectid"];
endata.moduleid=Enrequests["moduleid"];
//进行ajax请求数据的实时记录
$.ajax({
  url:"../../Subject/Public/setUserLog?ran=0.6841660777815695",
  type:'post',
  data:{request:JSON.stringify(endata)},
  success:function(data){

  },
  error:function(){

  }
})
