/**
 * 获取地址栏参数
 * 【注意】传递参数的时候使用encodeURI(encodeURI(content)),取的时候decodeURI(value)
 * @param {[String]} name [参数名称]
 */
function GetQueryString(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
  var r = window.location.search.substr(1).match(reg);
  if (r != null) return decodeURI(decodeURI(unescape(r[2])));
  return null;
}

function getTermName(code) {
  var name;
  code == "0001" ? (name = "上学期") : (name = "下学期");
  return name;
}

function getTermCode(name) {
  var code;
  name == "上学期" ? (code = "0001") : (code = "0002");
  return code;
}

function getGradeName(code) {
  var gradeName = "";
  if (code == "0001") {
    gradeName = "一年级";
  } else if (code == "0002") {
    gradeName = "二年级";
  } else if (code == "0003") {
    gradeName = "三年级";
  } else if (code == "0004") {
    gradeName = "四年级";
  } else if (code == "0005") {
    gradeName = "五年级";
  } else if (code == "0006") {
    gradeName = "六年级";
  }
  return gradeName;
}

function getGradeCode(code) {
  var gradeCode = "";
  if (code == "一年级") {
    gradeCode = "0001";
  } else if (code == "二年级") {
    gradeCode = "0002";
  } else if (code == "三年级") {
    gradeCode = "0003";
  } else if (code == "四年级") {
    gradeCode = "0004";
  } else if (code == "五年级") {
    gradeCode = "0005";
  } else if (code == "六年级") {
    gradeCode = "0006";
  } else {
    gradeCode = "0";
  }
  return gradeCode;
}

function checkLocalStorage() {
  if (!window.localStorage) {
    mui.toast("您的手机不支持预览功能");
    return false;
  }
}

/**
 * 存储内容到localStorage
 * 【主要】如果是json格式需要encodeURI(JSON.stringify(value)),取的时候decodeURI(value)
 * @param {[String]} name  [名称]
 * @param {[String]} value [值]
 */
function setLocalStorage(name, value) {
  checkLocalStorage();
  window.localStorage.setItem(name, value);
}

/**
 * 从localStorage获取内容
 * @param {[String]} name  [description]
 * @param {[String]} value [description]
 */
function getLocalStorage(name) {
  checkLocalStorage();
  var value = window.localStorage.getItem(name);
  return decodeURI(value);
}

/**
 * 删除localStorage的指定内容
 * @param  {[String]} name [description]
 */
function removeLocalStorage(name) {
  checkLocalStorage();
  window.localStorage.removeItem(name);
}
