/* 后台管理相关js
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*基础初始化类*/
$.EBC = {
    setGrade: function (obj) { //查询所有年级
        $(obj).empty();
        $.get('../getbasedata/getgrade', {
            random: Math.random()
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));

            });
        });
    },
    setTerm: function (obj, gradeid) { //根据所选年级查询相应的学期
        $(obj).empty();
        $.get('../getbasedata/getTerm', {
            gradeid: gradeid,
            random: Math.random()
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));
            });
        });
    },
    setVersion: function (obj, gradeid) { //根据所选年级查询相应的版本
        $(obj).empty();
        $.get('../getbasedata/getversion', {
            gradeid: gradeid,
            random: Math.random()
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.detail_code).text(value.detail_name));
            });
        });
    },
    setUnit: function (obj, gradeid, versionid, termid) { //根据所选的年级学期版本查询相应的单元信息
        $(obj).empty();
        //alert( gradeid+versionid+termid);
        $.get('../getbasedata/getunit', {
            gradeid: gradeid,
            versionid: versionid,
            termid: termid,
            random: Math.random()
        }, function (data) {

            $.each(data, function (i, val) {
                $(obj).append("<option isunit='" + val.is_unit + "' value='" + val.ks_code + "'>" + val.ks_name + "</option>");
            });
        });
    },
    setTvoiceid: function (obj) {
        $(obj).empty();
        $.get('../getbasedata/getvoiceid', {
            random: Math.random(),
            flag: '0'
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.id).text(value.name));
            });
        });
    },
    setVvoiceid: function (obj) {
        $(obj).empty();
        $.get('../getbasedata/getvoiceid', {
            random: Math.random(),
            flag: '1'
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.id).text(value.name));
            });
        });
    },
    setProvince: function (obj) {
        $(obj).empty();
        $.get('../Getbasedata/getProvince', {
            random: Math.random()
        }, function (data) {
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.id).text(value.name));
            });
        });
    },
    setChapter: function (obj, unitid) {
        $(obj).empty();
        $.get('../Getbasedata/getChapter', {
            unitid: unitid,
            random: Math.random()
        }, function (data) {
            //alert("aaaaa");
            $.each(data, function (i, value) {
                $(obj).append($("<option>").val(value.id).text(value.chapter));
            });
        });

    },
    inIt: function (type) {
        this.setGrade($('#gradeid'));
        this.setTerm($('#termid'), $('#gradeid').val());
        if (type != 'vimglist') {
            this.setVersion($('#versionid'), $('#gradeid').val());

        }
        if (type != 'unitlist') {
            this.setUnit($('#unitid'), $('#gradeid').val(), $('#versionid').val(), $('#termid').val());
        }
        selectChanage('gradeid', type);
        if (type != 'vimglist') {
            selectChanage('versionid', type);
        }

        selectChanage('termid', type);
        if (type != 'unitlist') {
            this.setUnit($('#unitid'), $('#gradeid').val(), $('#versionid').val(), $('#termid').val());
        }
        selectChanage('unitid', type);
        if (type == 'wordlist') {
            getWordList();
        }
        if (type == 'chapterlist') {
            getChapterList(0);
        }
        if (type == 'examslist') {
            getExamList();
        }
        if (type == 'textlist') {
            $("#gradeid").val($("#gradeid").attr("bid"));
            $("#termid").val($("#termid").attr("bid"));

            this.setVersion($('#versionid'), $('#gradeid').val());
            $("#versionid").val($("#versionid").attr("bid"));
            this.setUnit($('#unitid'), $('#gradeid').val(), $('#versionid').val(), $('#termid').val());
            $("#unitid").val($("#unitid").attr("bid"));
            this.setChapter($('#chapterid'), $('#unitid').val());
            $("#chapterid").val($("#chapterid").attr("bid"));
            //getChapterList(1);
        }
        if (type == 'unitlist') {
            getUnitList();
        }
        if (type == 'vimglist') {
            getversionimg();
        }
    }
}

function selectChanage(objstr, type) {
    $('#' + objstr).change(function () {
        if (objstr == 'gradeid' || objstr == 'termid') {
            // $.EBC.setTerm($('#termid'), $('#gradeid').val());
            $.EBC.setVersion($('#versionid'), $('#gradeid').val());
            if (type != 'unitlist') {
                $.EBC.setUnit($('#unitid'), $('#gradeid').val(), $('#versionid').val(), $('#termid').val());
            }
        }

        if (objstr == 'versionid') {
            $.EBC.setUnit($('#unitid'), $('#gradeid').val(), $('#versionid').val(), $('#termid').val());
        }
        if (type == 'wordlist') {
            //alert("sss");
            getWordList();
        }
        if (type == 'chapterlist') {
            getChapterList(0);
        }
        if (type == 'textlist') {
            $.EBC.setChapter($('#chapterid'), $('#unitid').val());
            //getChapterList(1);
        }
        if (type == 'unitlist') {
            getUnitList();
        }
        if (type == 'vimglist') {
            getversionimg();
        }
    });
}

function dialogTips(content) {
    art.dialog.tips('<font color="red">' + content + '……</font>', 0.5);
}

function dialogNotice(title, content) {
    dialogNotice(title, content, 3);
}

function dialogNotice(title, content, ts) {
    art.dialog({
        title: title,
        width: 240,
        content: content,
        icon: 'info',
        opacity: 0.2,
        fixed: true,
        lock: true,
        time: ts
    });
}

function isNumber(obj) {
    var re = /^[0-9]+.?[0-9]*$/;
    return re.test(obj);
}
/*
 * 单词验证方法
 */
function word_check() { //单词验证，输出验证结果到页面table
    $("#table_data").show();
    var wordlist = $.trim($("#wordlist").val());
    if (wordlist != "") {
        $("#table_data td").parents("tr").remove();
        $.get('../word/checkdata', {
            wordlist: wordlist,
            random: Math.random()
        }, function (data) { //返回验证结果json
            var i = 0;
            $.each(data, function (i, val) {
                i++;
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.html(i);
                td = tr.children('td').eq(1);
                td.html(val.word);
                td = tr.children('td').eq(2);
                td.html("是");
                if (val.isyes != 1) {
                    td.html("否");
                    td.attr("class", "red");
                }
                tr.appendTo("#table_data");
            });
        }, "json");
    }
}
/** getWordList
 取出所选年级学期版本单元下的所有的单词
 **/
function getWordList() {
    var unitid = $("#unitid").val();
    if (unitid != '') {
        var dloading = art.dialog({
            time: 30,
            title: '加载中……',
            width: 130,
            height: 30,
            opacity: 0.3,
            lock: true
        });
        $("#table_data td").parents("tr").remove();
        $.getJSON("../word/getlist", {
            unitid: unitid,
            searchtype: searchtype,
            random: Math.random()
        }, function (data) {
            var i = 0;
            $.each(data, function (i, val) {
                i++;
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.find("input").attr("bid", val.id);
                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(i);
                td = tr.children('td').eq(2);
                td.html(val.word);
                if (val.wordid == 0) {
                    td.attr("class", "red");
                }
                td = tr.children('td').eq(3);
                var select = td.children('select');
                select.empty();

                $.each(val.explains, function (rows, v) {
                    select.append($("<option>").val(v.id).text(v.morphology + v.explains));
                });
                select.attr("value", val.base_explainsid);
                td = tr.children('td').eq(4);
                var input = td.children('input');
                input.val(val.explains_content);
                td = tr.children('td').eq(5);
                var select = td.children('select');
                select.attr("BID", val.id);
                select.attr("value", val.explains_voiceid);
                if (val.isstress == 1) {
                    td = tr.children('td').eq(6);
                    input = td.children('input');
                    input.attr("checked", true);
                }
                td = tr.children('td').eq(7);
                td.html("否");

                if (val.isword != 1) {
                    td.html("<b>是</b>");
                    td.attr("class", "blue");
                }
                td = tr.children('td').eq(8);
                td.html(val.chaptername);
                td = tr.children('td').eq(9);
                var input = td.children('input');
                input.val(val.level_group);
                tr.find("input").attr("BID", val.id);
                tr.find("input").attr("mp3", val.usmp3);
                tr.find("input").attr("content", val.word);
                tr.find("input.ext_btn_listen").attr("dvideo", val.explains_mp3);
                tr.appendTo("#table_data");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    }
}

/*
 * getExamList()获取试卷列表
 */
function getExamList() {
    var unitid = $("#unitid").val();
    var state = $("#stateid").val();
    if (unitid != '') {
        var dloading = art.dialog({
            time: 30,
            title: '加载中……',
            width: 130,
            height: 30,
            opacity: 0.3,
            lock: true
        });
        $(".list_table td").parents("tr").remove();
        $.getJSON("../exam/getExamList", {
            unitid: unitid,
            state: state,
            random: Math.random()
        }, function (data) {
            var i = 0;
            $.each(data, function (i, val) {
                i++;
                var tr = $(".list_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.html(i);

                td = tr.children('td').eq(1);
                td.find('input').val(i);
                td = tr.children('td').eq(2);
                td.html(val.name);
                td = tr.children('td').eq(3);
                td.attr("bid", val.state);
                var content;
                if (val.state == '1') {
                    content = "编辑中";
                } else if (val.state == '2') {
                    content = "已完成待发";
                } else if (val.state == '3') {
                    content = "已生成待审";
                } else if (val.state == '4') {
                    content = "发布完成";
                }
                td.html(content);

                if (val.state == '4') {
                    tr.find(".info").hide();
                }
                tr.find("input").attr("BID", val.id);
                if (val.mp3time) {
                    tr.find("input").attr("mp3", "1");
                } else {
                    tr.find("input").attr("mp3", "0");
                }
                tr.find("input[name='state']").attr("state", val.state);
                tr.appendTo(".list_table");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    };
}


/*
 * addword()添加单词或者短语事件
 */
function addword() {
    if ($("#unitid").find("option:selected").attr("isunit") == 1) {
        art.dialog.alert("所选单元为非章节目录，请重新选择");
        return false;
    }
    art.dialog.prompt('请输入要添加的单词', function (val) {
        val = $.trim(val);
        var unitid = $("#unitid").val();
        if (val != "") {
            $.get('../word/add', {
                flag: 1,
                unitid: unitid,
                word: val,
                random: Math.random()
            }, function (data) {
                art.dialog.tips(data.msg);
                if (data.isadd == "1") {
                    getWordList();
                    art.dialog.confirm('是否继续添加单词', function () {
                        addword();
                    });
                };
            }, "json");
        }
    });
}

function addphrase() {
    if ($("#unitid").find("option:selected").attr("isunit") == 1) {
        art.dialog.alert("所选单元为非章节目录，请重新选择");
        return false;
    }
    art.dialog.prompt('请输入要添加的短语', function (val) {
        val = $.trim(val);
        var unitid = $("#unitid").val();
        if (val != "") {
            $.get('../word/add', {
                flag: 0,
                unitid: unitid,
                word: val,
                random: Math.random()
            }, function (data) {
                art.dialog.tips(data.msg);
                if (data.isadd == "1") {
                    getWordList();
                    art.dialog.confirm('是否继续添加短语', function () {
                        addphrase();
                    });
                };
            }, "json");
        }
    });
}
/*upWordsort
 * 同步单词管理--修改单词顺序及释义
 */
function upWordsort() {
    var dloading = art.dialog({
        time: 30,
        title: '更新中……',
        width: 130,
        height: 30,
        opacity: 0.3,
        lock: true
    });
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    var ks_code = $('#unitid').val();
    $("#table_data tr.tr").each(function () {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("BID");
        var sortid = tr.find('input[name="sortid"]').val();
        var isstress = tr.find('input[name="isstress"]:checked').val();
        var explainsid = tr.find('select[name="explainsid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        obj.isstress = isstress;
        obj.explainsid = explainsid;
        arrjson.push(obj);
    });
    $.post("../word/listup", {
        data: JSON.stringify(arrjson),
        ks_code: ks_code
    }, function (result) {
        getWordList();
        dloading.close();
        alert("保存成功");
    });
}
/*upWordsort
 * 同步单词管理--修改闯关分组
 */
function upWordgroup() {
    var dloading = art.dialog({
        time: 30,
        title: '更新中……',
        width: 130,
        height: 30,
        opacity: 0.3,
        lock: true
    });
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    var ks_code = $('#unitid').val();
    $("#table_data tr.tr").each(function () {
        var tr = $(this);
        var id = tr.find('input[name="level_group"]').attr("BID");
        var groupid = tr.find('input[name="level_group"]').val();
        var obj = {};
        obj.id = id;
        obj.groupid = groupid;
        arrjson.push(obj);
    });
    $.post("../word/levelgroup_up", {
        data: JSON.stringify(arrjson),
        ks_code: ks_code
    }, function (result) {
        getWordList();
        dloading.close();
        alert("保存成功");
    });
}
/**
 * getChapterList 获取该单元下课文章节
 type 0-在课文小节页面展示列表 1-在课文正文列表页展示下拉框小节
 **/
function getChapterList(type) {
    var unitid = $("#unitid").val();
    //alert(unitid);
    if (unitid != '') {
        var dloading = art.dialog({
            time: 30,
            title: '加载中……',
            width: 130,
            height: 30,
            opacity: 0.3,
            lock: true
        });
        $("#table_data td").parents("tr").remove();
        $.getJSON("../text/getchapterlist", {
            unitid: unitid,
            random: Math.random()
        }, function (data) { //获取章节目录json
            var i = 0;
            // alert(data.length);
            if (data.length > 0) {
                $.each(data, function (i, val) {
                    if (type != 1) {
                        i++;
                        var tr = $("#table_demo tr").eq(0).clone();
                        var td = tr.children('td').eq(0);
                        td.find("input").attr("bid", val.id);
                        td = tr.children('td').eq(1);
                        var input = td.children('input');
                        input.val(i);
                        td = tr.children('td').eq(2);
                        td.html(val.chapter);
                        td = tr.children('td').eq(3);
                        td.html("对话")
                        if (val.issection == 1) {
                            td.html("<b>段落</b>");
                            td.attr("class", "blue");
                        }
                        td = tr.children('td').eq(4);
                        td.html("已发布")
                        if (val.stateid != 1) {
                            td.html("<b>待发布</b>");
                            td.attr("class", "blue");
                        }
                        td = tr.children('td').eq(5);
                        td.html("是")
                        if (val.isevaluate != 1) {
                            td.html("<b>否</b>");
                            td.attr("class", "red");
                            tr.find('.isevaluate').val('设为测评').removeClass('ext_btn_isevaluatecan').addClass('ext_btn_isevaluate');
                        }
                        td = tr.children('td').eq(6);
                        td.html('<img id="usmp3" src="/public/Manager/images/sound.gif" width="16" height="13" class="listen" style="cursor: pointer;"  dvideo="'+val.chapter_mp3+'"/>');

                        tr.find("input").attr("BID", val.id);
                        tr.find('.isevaluate').attr("isevaluate", val.isevaluate);
                        tr.appendTo("#table_data");
                    } else {
                        $('#chapterid').append($("<option>").val(val.id).text(val.chapter));
                    }
                });
                if (type == 1) {
                    getTextList(); //加载课文小节正文
                }
            } else {
                //alert('没有章节信息！');
            }

        });

        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    }
}
/**
 * editChapter 添加或者编辑课文小节，如果id为0则为新添加
 */
function editChapter(id, chapter) {
    art.dialog.prompt('小节名称', function (val) {
        val = $.trim(val);
        var unitid = $("#unitid").val();
        if (val != "") {
            $.get('../text/chapterEdit', {
                unitid: unitid,
                id: id,
                chapter: val,
                random: Math.random()
            }, function (data) {
                art.dialog.tips(data.msg);
                if (data.isadd == 1) {
                    getChapterList(); //添加成功后获取新的章节列表
                }
            }, "json");
        }
    }, chapter);
}
/**
 * 获取章节下正文内容
 * @returns {undefined}
 */
function getTextList() {
    var chapterid = $("#chapterid").val();
    if (chapterid > 0) {
        var dloading = art.dialog({
            time: 30,
            title: '加载中……',
            width: 130,
            height: 30,
            opacity: 0.3,
            lock: true
        });
        $("#table_data td").parents("tr").remove();
        $.getJSON("../text/gettextlist", {
            chapterid: chapterid,
            random: Math.random()
        }, function (data) {
            var i = 0;
            $.each(data, function (i, val) {
                i++;
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.html(i);

                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(i);

                td = tr.children('td').eq(2);
                var input = td.children('input');
                input.val(val.sectionid);

                td = tr.children('td').eq(3);
                td.html(val.enbefore);

                td = tr.children('td').eq(4);
                td.html(val.encontent);

                td = tr.children('td').eq(5);
                td.html(val.cncontent);
                var flag = true;
                td = tr.children('td').eq(6);
                input = td.children('input');
                if (val.isexample == 1) {
                    input.attr("checked", true);
                } else {
                    flag = false;
                };

                td = tr.children('td').eq(7);
                var select = td.children('select');
                select.val(val.voiceid);

                td = tr.children('td').eq(8);
                td.html("已发布")
                if (val.stateid != 1) {
                    td.html("<b>待发布</b>");
                    td.attr("class", "red");
                }
                tr.find("input").attr("BID", val.id);
                tr.find(".ext_btn_submit").attr("ftp_mp3", val.ftp_mp3);
                tr.find(".ext_btn_listen").attr("dvideo", val.mp3);
                tr.find(".ext_btn_download").attr("dvideo", val.mp3);
                tr.appendTo("#table_data");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    }
}
/*
 * 编辑正文内容
 */
function editText(id) {
    var chapterid = $("#chapterid").val();
    var ks_code = $('#unitid').val();
    art.dialog.open('../text/edit?chapterid=' + chapterid + '&id=' + id + "&ks_code=" + ks_code, {
        title: "课文内容",
        width: 550,
        height: 320,
        lock: true,
        opacity: 0.3,
        button: [{
                name: '保存',
                callback: function () {
                    var iframe = this.iframe.contentWindow;
                    var re = iframe.editText();
                    if (re) {
                        getTextList();
                        return true;
                    } else {
                        return false;
                    }
                },
                focus: true
            },
            {
                name: '关闭',
                callback: function () {
                    //$("#gradeid").change();
                },
                focus: false
            }
        ]
    });
}
/*
 * getUnitList 获取单元列表
 */
function getUnitList() {

    var gradeid = $("#gradeid").val();
    var versionid = $("#versionid").val();
    var termid = $("#termid").val();
    //var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
    $("#table_data td").parents("tr").remove();
    $.getJSON("../unit/getunitlist", {
        gradeid: gradeid,
        versionid: versionid,
        termid: termid,
        random: Math.random()
    }, function (data) {
        //alert(data.length);
        $.each(data, function (i, val) {

            var tr = $("#table_demo tr").eq(0).clone();
            var td = tr.children('td').eq(0);
            td.html(val.display_order);
            td = tr.children('td').eq(1);
            if (val.is_unit == '0') {
                td.html('是');
            } else {
                td.html('<font color="red">否</font>');
            }
            td = tr.children('td').eq(2);
            td.html(val.ks_name_short);

            td = tr.children('td').eq(3);
            td.html(val.ks_name);
            td = tr.children('td').eq(4);
            td.html(val.ks_code);
            if (val.ks_code.substr(0, 1) == 'k') {
                tr.find("input.ext_btn_error").show();
            } else {
                tr.find("input.ext_btn_error").hide();
            }
            tr.find("input").attr("BID", val.ks_code);
            tr.find("input").attr("BName", val.ks_name_short);
            tr.appendTo("#table_data");
        });
    });
    $(".tr:odd").css("background", "#F5F8FA");
    // dloading.close();

}
/**
 * editUnit 编辑单元简称
 * @param {type} ks_code
 * @param {type} ks_name_sort
 * @returns {undefined}
 */
function editUnit(ks_code, ks_name_sort, type) {
    if (type == '0') {
        art.dialog.prompt('小节简称', function (val) {
            val = $.trim(val);
            if (val != "") {
                $.get('../unit/unitedit', {
                    ks_code: ks_code,
                    ks_name_short: val,
                    random: Math.random()
                }, function (data) {
                    art.dialog.tips(data.msg);
                    getUnitList();
                }, "json");
            }
        }, ks_name_sort);
    } else {
        editOtherUnit(ks_code);
    }
}
/**
 * editUnit 编辑版本简称
 * @param {type} ks_code
 * @param {type} ks_name_sort
 * @returns {undefined}
 */
function editVersion(id, detail_name_short, type) {
    if (type == '0') {
        art.dialog.prompt('版本简称', function (val) {
            val = $.trim(val);
            if (val != "") {
                $.get('../version/versionEdit', {
                    id: id,
                    detail_name_short: val,
                    random: Math.random()
                }, function (data) {
                    art.dialog.tips(data);
                    getVersionlist();
                }, "json");
            }
        }, detail_name_short);
    } else {
        editOtherVersion(id);
    }

}

function getVersionlist(type) {
    var dloading = art.dialog({
        time: 30,
        title: '加载中……',
        width: 130,
        height: 30,
        opacity: 0.3,
        lock: true
    });
    $("#table_data td").parents("tr").remove();
    $.getJSON("../version/getVersionlist", {
        type: type,
        random: Math.random()
    }, function (data) {
        $.each(data, function (i, val) {
            var tr = $("#table_demo tr").eq(0).clone();
            var td = tr.children('td').eq(0);
            td.html(val.detail_order);
            td = tr.children('td').eq(1);
            td.html(val.detail_name_short);

            td = tr.children('td').eq(2);
            td.html(val.detail_name);
            if (type == 1) {
                tr.find("input.ext_btn_error").show();
            } else {
                tr.find("input.ext_btn_error").hide();
            }
            tr.find("input").attr("BID", val.id);
            tr.find("input").attr("BName", val.detail_name_short);
            tr.appendTo("#table_data");
        });
    });
    $(".tr:odd").css("background", "#F5F8FA");
    dloading.close();
}

function getversionimg() {
    var gradeid = $("#gradeid").val();
    var termid = $("#termid").val();
    var dloading = art.dialog({
        time: 30,
        title: '加载中……',
        width: 130,
        height: 30,
        opacity: 0.3,
        lock: true
    });
    $("#table_data td").parents("tr").remove();
    $.getJSON("../version/getVersionimg", {
        r_grade: gradeid,
        r_volume: termid,
        random: Math.random()
    }, function (data) {

        $.each(data, function (i, val) {

            var tr = $("#table_demo tr").eq(0).clone();
            var td = tr.children('td').eq(0);
            td.html(i + 1);

            td = tr.children('td').eq(1);
            td.html(val.detail_name);
            td = tr.children('td').eq(2);

            td.html('<img src="/' + val.pic_path + '" width="180"/>');
            td = tr.children('td').eq(3);
            if (val.isdel == 0) {
                td.html('<font color = "red">停用</font>');
                td = tr.children('td').eq(4).append('&nbsp;&nbsp;<input type="button" class="ext_btn ext_btn_success " value="启用" onclick="version_disable(' + val.imgid + ',1);"  />');
            } else {
                td.html('启用');
                td = tr.children('td').eq(4).append('&nbsp;&nbsp;<input type="button" class="ext_btn ext_btn_error " value="停用" onclick="version_disable(' + val.imgid + ',0);"  />');

            }
            tr.find("input").attr("BID", val.imgid);
            tr.find("input").attr("Bpic", val.pic_path);
            tr.find("input").attr("Bcode", val.detail_code);
            //td = tr.children('td').eq(4).append('ssss');
            tr.appendTo("#table_data");
        });
    });
    $(".tr:odd").css("background", "#F5F8FA");
    dloading.close();

}

function version_disable(id, flag) {
    var content = '';
    var notice = ''
    if (flag == 0) {
        content = '你确定要停用这个版本吗';
        notice = '停用成功';
    } else {
        content = '你确定要启用这个版本吗';
        notice = '启用成功';
    }
    art.dialog({
        content: content,
        ok: function () {
            $.get('../version/version_img_disable?rt=' + Math.random(), {
                imgid: id,
                flag: flag
            }, function () {

                dialogNotice("系统提示", notice, 1);
                getversionimg();
            });
            return true;
        },
        cancelVal: '关闭',
        cancel: true //为true等价于function(){}
    });
}

function editversionimg(imgid, pic, r_version) {
    //alert(imgid);
    var r_grade = $('#gradeid').val();
    var r_volume = $('#termid').val();
    var myDialog = $.dialog.open('../version/version_img_update?pic=' + pic, {
        id: 'explains_update',
        title: '修改图片',
        window: 'top',
        width: 500,
        height: 330,
        lock: true,
        opacity: 0.3,
        button: [{
                name: '保存',
                callback: function () {
                    var iframe = this.iframe.contentWindow;
                    var pic = $('#filename', iframe.document).attr('value');
                    var closeflag = true;
                    //alert(r_grade+"=="+r_volume+"=="+r_version+"=="+imgid+"=="+pic);
                    $.get("../version/version_img_update_action?rt=" + Math.random(), {
                        imgid: imgid,
                        r_grade: r_grade,
                        r_volume: r_volume,
                        r_version: r_version,
                        r_subject: "0003",
                        pic: pic
                    }, function (data) {
                        if ($.trim(String(data)) != "1") {
                            dialogNotice("系统提示", "更新失败，请与管理员联系", 30);
                            closeflag = false;
                        } else {
                            dialogNotice("系统提示", "保存成功", 30);
                            //location.reload();
                            getversionimg();
                        }
                    });
                    return closeflag;
                },
                focus: true
            },
            {
                name: '关闭',
                callback: function () {
                    //location.reload();
                    getversionimg();
                    return true;
                },
                focus: false
            }
        ]
    });
}