require.config({
    baseUrl: "../../public/public/js",
    paths: {
        "zepto": "zepto.min",
        "enajax": "enajax",
    },
    shim: {
        "zepto": {
            exports: "$"
        },
        'enajax': {
            deps: ['zepto'],
            exports: 'enajax'
        }
    },
    waitSeconds: 0
});
//文件主要是进行不同体型的展示
define(['zepto', 'enajax'], function ($, enajax) {
    var listen = {}; //推荐方式  
    var listenUnitdata = function (url, loc) {
        getUnitData(url, loc);
    };

    var getExamsListData = function (url, loc) {
        Request = GetRequest();
        var ks_code = Request["ks_code"];
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                ks_code: ks_code,
                ran: Math.random()
            },
            dataType: 'json',
            async: false,
            context: $('body'),
            success: function (data) {
                //遮罩消失
                hideloading();
                var examlist = "";
                if (data.length == 0) {
                    examlist = "<li><p>该章节下没有听力训练资源</p></li>";
                } else {
                    $.each(data, function (k, v) {
                        //基础模块的添加
                        examlist = examlist + '<li onclick="javascript:window.location.href=\'listenexam?examsid=' + v.id + '&ks_code=' + Request["ks_code"] + '&moduleid=' + Request["moduleid"] + '&backsUrl=' + encodeURIComponent(encodeURIComponent(location.href)) + '&ks_short_name=' + encodeURI(encodeURI(Request["ks_short_name"])) + '\';">';
                        examlist = examlist + '<p class="btnYuan radius100 record" style="display: block;float:left;line-height: 20px;vertical-align: none;">';
                        examlist = examlist + '<a class="btnYuan radius100 record" style="padding: 0 0;"><i class="icon-tlxl" style="';
                        examlist = examlist + 'height: 40px;"></i></a></p><p style="display: block;">';
                        if (v.userscore == '1') {
                            examlist = examlist + '<span style="display:block;color: #269bd7;">' + v.name + '</span>';
                            examlist = examlist + '<span style="display:block;color: #269bd7;">试卷已有' + v.num + '人次参与,' + v.accnum + '人次满分</span>';
                        } else {
                            examlist = examlist + '<span style="display:block;">' + v.name + '</span>';
                            examlist = examlist + '<span style="display:block;">试卷已有' + v.num + '人次参与,' + v.accnum + '人次满分</span>';
                        }

                        examlist = examlist + '</p><p><span style="float:right;">';
                        examlist = examlist + '<i class="icon-right"></i></span></p></li>';
                    })
                }

                $(loc).html(examlist);
                new IScroll("#wrapper", {
                    momentum: true,
                    click: true
                });
                $("#wrapper").resize();
            },
            error: function (xhr, type) {

            }
        })
    }

    listen.listenUnitdata = listenUnitdata;
    listen.getExamsListData = getExamsListData;
    return listen;
});