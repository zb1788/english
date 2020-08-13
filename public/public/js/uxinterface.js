/**
 * 手机端
 */
var needHideProgress = false,
    // 优信最新接口标识
    uxinFlag = (function (win) {
        if (typeof win.UxinJsCore != 'undefined') {
            return 2;
        } else if (typeof win.UXinJSInterface != 'undefined') {
            return 1;
        } else {
            return 0;
        }
    })(window),
    // 优信参数对象
    uxinMessageObj = function (methodName, params) {
        this.method = methodName || '';
        this.params = params || {};
    };

/**
 * 统一执行uxin函数
 * @param coreFn
 * @param jsFn
 * @param defFn
 * @returns {*}
 */
function uxinFnExecute (coreFn, jsFn, defFn) {
    if (uxinFlag === 2) {
        if (typeof defFn === 'function') {
            return window.UxinJsCore.postMessage(JSON.stringify(coreFn()));
        }
    } else if (uxinFlag === 1) {
        if (typeof jsFn === 'function') {
            return jsFn();
        }
    } else {
        if (typeof defFn === 'function') {
            return defFn();
        }
    }
}

// 显示错误信息，直接返回客户端首页
function showError () {
    uxinFnExecute(function () {
    }, function () {
        UXinJSInterface.alterViewShow()
    });
}

// 显示正在加载
function showLoading () {
    uxinFnExecute(function () {
        return new uxinMessageObj('showProgress', {info: ''});
    }, function () {
        UXinJSInterface.showProgress();
    });
}

// 隐藏正在加载
function hideLoading () {
    uxinFnExecute(function () {
        needHideProgress = false;
        return new uxinMessageObj('hideProgress');
    }, function () {
        UXinJSInterface.hideProgress();
        needHideProgress = false;
    }, function () {
        needHideProgress = true;
    });
}

/**
 * 查看大图
 * @param imgSrc 图片完整地址
 */
function showImage (imgSrc) {
    uxinFnExecute(function () {
        return new uxinMessageObj('showPicFromHtml', {imgPath: imgSrc});
    }, function () {
        UXinJSInterface.showPicFromHtml(imgSrc);
    });
}

/**
 * 创建一个新的webview
 * @param title 标题
 * @param url    地址
 */
function openNewWebPage (title, url) {
    uxinFnExecute(function () {
        return new uxinMessageObj('openNewWebPage', {
            title: title,
            url: url
        });
    }, function () {
        UXinJSInterface.openNewWebPage(title, url);
    });
}

/**
 * 打开多签的webview
 * @param pageTitles 内容
 * @param tabArray    页面tab签
 * @param rightItem 导航右侧
 * @param title 导航名称
 */
function openNewWebPageList (pageTitles, tabArray, rightItem, title) {
    // pageTitles":[],
    // "tabArray":"",
    // "rightItemJson":"",
    // "title":""
    uxinFnExecute(function () {
        return new uxinMessageObj('openNewWebPage', {
            pageTitles: pageTitles,
            tabArray: tabArray,
            rightItem: rightItem,
            title: title
        });
    }, function () {
        UXinJSInterface.openNewWebPageList(pageTitles, tabArray, rightItem, title);
    });
}


/**
 * 打开webview以json格式传输数据,可以设置是否刷新上一个webview
 * {
 *  "url":"",
 *  "isNeedReloadThisPage":1,//0不需要刷新1需要刷新
 *  "title":"",
 *  "isOnlyReadTitle":0,//0此处设置的title不生效读取页面中的title，1此处title生效
 *  "RightItem":{"ImageUrl":"","ItemTitle":"","ItemUrl":""}
 *  }
 * @param json
 */
function openNewProgressControllerFromJson (json) {
    uxinFnExecute(function () {
        return new uxinMessageObj('openNewProgressControllerFromJson', {
            paramsJson: json
        });
    }, function () {
        UXinJSInterface.openNewProgressControllerFromJson(json);
    });
}

/**
 * 选中第几个tab
 * @param index
 */
function popToIndexController (index) {
    uxinFnExecute(function () {
        return new uxinMessageObj('popToIndexController', {index: index});
    }, function () {
        UXinJSInterface.popToIndexController(index);
    });
}

/**
 * 相当于在手机端点击返回
 */
function popTheController () {
    uxinFnExecute(function () {
        return new uxinMessageObj('popTheController');
    }, function () {
        UXinJSInterface.popTheController();
    });
}

/**
 * 创建无header的webview
 * @param url
 */
function openProgressController (url) {
    uxinFnExecute(function () {
        return new uxinMessageObj('openProgressController', {url: url});
    }, function () {
        UXinJSInterface.openProgressController(url);
    });
}

/**
 * 显示Alert
 * @param msg
 */
function showAlert (msg) {
    uxinFnExecute(function () {
        return new uxinMessageObj('showProgress', {info: msg});
    }, function () {
        UXinJSInterface.showProgress(msg);
    });
}

/**
 * 隐藏Alert
 */
function hideAlert () {
    hideLoading();
}

/**
 * 返回到第一个webview,并刷新页面
 */
function popToRootController () {
    uxinFnExecute(function () {
        return new uxinMessageObj('popToRootController');
    }, function () {
        UXinJSInterface.popToRootController();
    });
}

/**
 * 返回到第一个webview,并打开指定地址
 * @param url
 */
function exchangeRootUrl (url) {
    uxinFnExecute(function () {
        return new uxinMessageObj('exchangeRootUrl', {url: url});
    }, function () {
        UXinJSInterface.exchangeRootUrl(url);
    });
}

/**
 * 变更学生作业状态
 * @param batchId 作业批次ID
 */
function setOnlinWorkHaveFinished (batchId) {
    uxinFnExecute(function () {
        return new uxinMessageObj('setOnlinWorkHaveFinished', {batchedId: batchId});
    }, function () {
        UXinJSInterface.setOnlinWorkHaveFinished(batchId);
    });
}


/**
 * 打开客户端的离线作业
 * @param chapterJson
 */
function setCurrentEduChapter (chapterJson) {
    uxinFnExecute(function () {
        return new uxinMessageObj('setCurrentEduChapter', {json: chapterJson})
    }, function () {
        UXinJSInterface.setCurrentEduChapter(chapterJson);
    });
}

/**
 * 打开拍照和相册，用户选择图片，并裁剪后返回给页面
 * @param area 区域信息 插件返回图片后，传递给页面上使用
 */
function selectPhoto (area) {
    uxinFnExecute(function () {
        return new uxinMessageObj('selectPhoto', {area: area})
    }, function () {
        UXinJSInterface.selectPhoto(area);
    });
}

String.prototype.format = function (format) {
    var args = arguments;
    return this.replace(/\{(\d+)\}/g, function (m, i) {
        return args[i];
    });
};