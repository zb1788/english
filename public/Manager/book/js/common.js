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
var Request = new Object();
Request = GetRequest();

function dialogTips(content){
  art.dialog.tips('<font color="red">' + content + '……</font>', 0.5); 
} 
 
function dialogNotice(title,content){
  dialogNotice(title,content,3);
}

function dialogNotice(title,content,ts){   
  art.dialog({
    title: title,
    width: 240,
      content: content,
      icon: 'info', 
      opacity:0.2,
      fixed: true,
        lock: true,
        time: ts
  });
}

function isNumber(obj) {
    var re = /^[0-9]+.?[0-9]*$/;    
    return re.test(obj);
} 