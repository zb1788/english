/* 后台管理相关js
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*基础初始化类*/
$.EBC = {
    setSubject: function(obj,selectvalue,qtype) {       //查询学科
        $(obj).empty();
        if(selectvalue == 'all'){
            $(obj).append($("<option selected>").val('all').text('全部').attr('title','全部'));
        }
        // else{
        //     $(obj).append($("<option>").val('all').text('全部').attr('title','全部'));
        // }
        
        $.get('/Elearnmanager/getbasedata/getSubject', {qtype:qtype,random: Math.random()}, function(data) {
             if(data.length == 0){
                $(obj).append($("<option>").val(0).text('暂无学科'));
            }
            else{
                 $.each(data, function(i, value) {
                    if(selectvalue == value.detail_code){
                        $(obj).append($("<option selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                    }
                    else{
                        $(obj).append($("<option>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                    }

                });
            }
           
        });
    },
    setSpecial: function(obj,selectvalue) {       //查询特殊类型
        $(obj).empty();      
        $.get('/Elearnmanager/getbasedata/getSpecial', {random: Math.random()}, function(data) {
            $.each(data, function(i, value) {
                if(selectvalue == value.detail_code){
                    $(obj).append($("<option selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }
                else{
                    $(obj).append($("<option>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }

            });
        });
    },
    setSource: function(obj,selectvalue) {       //查询外接来源
        $(obj).empty();      
        $.get('/Elearnmanager/getbasedata/getSource', {random: Math.random()}, function(data) {
            $.each(data, function(i, value) {
                if(selectvalue == value.detail_code){
                    $(obj).append($("<option selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }
                else{
                    $(obj).append($("<option>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }

            });
        });
    },
    setGrade: function(obj,selectvalue,showtpe) {       //查询所有年级
        $(obj).empty();
        $.get('/Elearnmanager/getbasedata/getGrade', {grade_code:selectvalue,showtpe:showtpe,random: Math.random()}, function(data) {
            $.each(data, function(i, value) {
                if(showtpe == 'option'){
                    if(selectvalue == value.detail_code){
                        $(obj).append($("<option gradename='"+value.detail_name+"' selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                    }
                    else{
                        $(obj).append($("<option gradename='"+value.detail_name+"'>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                    }
                }
                else{
                    if(selectvalue.indexOf(value.detail_code) >= 0){
                        $(obj).append('<input gradename="'+value.detail_name+'" type="checkbox" name="appgrade" checked value="'+value.detail_code+'"/>&nbsp;'+value.detail_name+'&nbsp;&nbsp;&nbsp;&nbsp;');
                    }
                    else{
                        $(obj).append('<input gradename="'+value.detail_name+'" type="checkbox" name="appgrade"  value="'+value.detail_code+'"/>&nbsp;'+value.detail_name+'&nbsp;&nbsp;&nbsp;&nbsp;');
                    }
                }

            });
        });
    },
    setVersion: function(obj,subjectcode,app_id,selectvalue) {       //查询外接来源
        $(obj).empty();      
        $.get('/Elearnmanager/getbasedata/getVersion', {subjectcode:subjectcode,app_id:app_id,random: Math.random()}, function(data) {
            if(data.length == 0){
                $(obj).append($("<option>").val(0).text('暂无版本'));
            }
            else{
                $.each(data, function(i, value) {
                    if(selectvalue == value.id){
                        $(obj).append($("<option selected>").val(value.id).text(value.r_grade_name+'-'+value.r_term_name+'-'+value.r_version_name));
                    }
                    else{
                        $(obj).append($("<option>").val(value.id).text(value.r_grade_name+'-'+value.r_term_name+'-'+value.r_version_name));
                    }

                });
            }
            
        });
    },
    setAppname: function(obj,subjectcode,selectvalue,isspecial) {       //查询外接来源
        $(obj).empty();      
        $.get('/Elearnmanager/classapp/getAppList', {subject_code:subjectcode,isspecial:isspecial,random: Math.random()}, function(data) {
             if(data.length == 0){
                    $(obj).append($("<option>").val(0).text('暂无应用'));
            }
            else{
                 $.each(data, function(i, value) {
                    if(selectvalue == value.id){
                    $(obj).append($("<option selected>").val(value.id).text(value.app_name));
                    }
                    else{
                        $(obj).append($("<option>").val(value.id).text(value.app_name));
                    }
                });
            }
           
        });
    },
  setUnit: function(obj,queryid,selectvalue,querytype) {       //查询外接来源
        $(obj).empty();      
        $.get('/Elearnmanager/getbasedata/getUnit', {queryid:queryid,querytype:querytype,random: Math.random()}, function(data) {
            
            if(data.length == 0){
                $(obj).append($("<option>").val(0).text('暂无单元'));
            }
            else{
                $.each(data, function(i, value) {
                    if(selectvalue == value.id){
                        $(obj).append($("<option selected>").val(value.id).text(value.name));
                    }
                    else{
                        if(value.is_click == 1){
                            $(obj).append($("<option disabled>").val(value.id).text(value.name));
                        }
                        else{
                            $(obj).append($("<option>").val(value.id).text(value.name));
                        }
                    }

                });
            }
            
        });
    },
    setTerm: function(obj, selectvalue) {   //根据所选年级查询相应的学期
        $(obj).empty();
        $.get('/Elearnmanager/getbasedata/getTerm', {random: Math.random()}, function(data) {
            $.each(data, function(i, value) {
                if(selectvalue == value.detail_code){
                    $(obj).append($("<option selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }
                else{
                    $(obj).append($("<option>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }

            });
        });
    },
    setRmsVersion: function(obj, selectvalue) {    //根据所选年级查询相应的版本
        $(obj).empty();
        $.get('/Elearnmanager/getbasedata/getRmsVersion', {random: Math.random()}, function(data) {
            $.each(data, function(i, value) {
                if(selectvalue == value.detail_code){
                    $(obj).append($("<option selected>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }
                else{
                    $(obj).append($("<option>").val(value.detail_code).text(value.detail_name).attr('title',value.detail_name));
                }

            });
        });
    },
    setVideo: function(obj,app_id,isterm,isunit,selectvalue) {    //根据所选年级查询相应的版本
       // console.log($(obj));
        $(obj).empty();
        var subject_code = $("#subjectlist").val();
        var version_id = 0,unit_id=0;
        if(isterm == 1){
            version_id = $("#versionlist").val();
        }
        if(isunit == 1){
            unit_id = $("#unitlist").val();
        }
        $.get("/Elearnmanager/classapp/getCourseList", {subject_code: subject_code,app_id:app_id,unit_id:unit_id,version_id:version_id,isterm:isterm,isunit:isunit, random: Math.random()}, function(data) {

            if(data.length == 0){
                $(obj).append($("<option>").val(0).text('暂无视频'));
            }
            else{
                $.each(data, function(i, value) {
                    if(selectvalue == value.detail_code){
                        $(obj).append($("<option selected>").val(value.id).text(value.title).attr('title',value.title));
                    }
                    else{
                        $(obj).append($("<option>").val(value.id).text(value.title).attr('title',value.title));
                    }

                });
            }
            
        });
    },
    inIt: function(type) {
        

    }
}
function dialogTips(content) {
    art.dialog.tips('<font color="red">' + content + '……</font>', 0.5);
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

/** getWordList
 取出所选年级学期版本单元下的所有的单词
 **/
function getAppList()
{
    var subject_code = $("#subjectlist").val();
    if (subject_code != '') {
        var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
        $("#table_data td").parents("tr").remove();
        $.getJSON("/Elearnmanager/classapp/getAppList", {subject_code: subject_code, random: Math.random()}, function(data) {
            var i = 0;
            $.each(data, function(i, val) {
                i++;
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.find("input").attr("bid",val.id);
                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(val.sortid);
                td = tr.children('td').eq(2);
                td.html(val.id);
                td = tr.children('td').eq(3);
                td.html(val.detail_name);
                td = tr.children('td').eq(4);
                td.html(val.app_name);
                td = tr.children('td').eq(5);
                var gradehtml = '';
                $.each(val.grade_config,function(k,gradeval){
                    gradehtml += gradeval.grade_name+'&nbsp;&nbsp;';
                });
                td.html(gradehtml);
                tr.find("input").attr("bid", val.id);
                tr.appendTo("#table_data");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    }
            
}
function getCourseList(app_id,isterm,isunit)
{
    var subject_code = $("#subjectlist").val();
    var version_id = 0,unit_id=0;
    if(isterm == 1){
        version_id = $("#versionlist").val();
    }
    if(isunit == 1){
        unit_id = $("#unitlist").val();
    }
    if (subject_code != '') {
        //alert('ss');
        var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
        $("#table_data td").parents("tr").remove();
        $.getJSON("/Elearnmanager/classapp/getCourseList", {subject_code: subject_code,app_id:app_id,unit_id:unit_id,version_id:version_id,isterm:isterm,isunit:isunit, random: Math.random()}, function(data) {
            var i = 0;
            $.each(data, function(i, val) {
                i++;
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.find("input").attr("bid",val.id);
                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(val.sortid);
                td = tr.children('td').eq(2);
                td.html(val.title);
                if(isspecial == 0){
                    td = tr.children('td').eq(3);
                    td.html(val.q_num);
                }
                else{
                    td = tr.children('td').eq(3);
                    td.html(val.yaoqiu_num);
                    td = tr.children('td').eq(4);
                    td.html(val.fanwen_num);
                }
                tr.find("input").attr("bid", val.id);
                tr.find("input").attr("video_code", val.video_code);
                tr.appendTo("#table_data");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
        dloading.close();
    }
            $('.appedit').click(function(){
                    var id = $(this).attr('bid');
                    window.location.href="/Elearnmanager/classapp/appedit?id="+id;
                   
            });
            $('.appdetail').click(function(){
                    var id = $(this).attr('bid');
                    var addhref = '/Elearnmanager/classapp/app_course_list?app_id='+id;
                    //$(window.parent.frames['leftFrame'].document).find('.courselist').attr('href',addhref);
                    $(window.parent.frames['leftFrame'].document).find('.licourselist').click();
                    window.location.href=addhref;
                    
            });
}
function SaveApp(id){
    //alert('ss');
    var app_isterm = 1,app_isunit=1,app_isspecial=1,app_issource=1;
    var app_name = $.trim($('#appname').val());
    var app_subject_code = $('#subjectlist').val();
    if($('input[name=isterm]').is(':checked')){
        app_isterm = 1;
    }
    else{
        app_isterm = 0;
    }
    if($('input[name=isunit]').is(':checked')){
        app_isunit = 1;
    }
    else{
        app_isunit = 0;
    }
    var app_unit_type = $("input[name='unittype']:checked").val();
    if($('input[name=isspecial]').is(':checked')){
        app_isspecial = 1;
    }
    else{
        app_isspecial = 0;
    }
    var app_special_code = $('#special').val();
    var app_grade = '',app_grade_name='';
    $('input:checkbox[name=appgrade]:checked').each(function(i){
        app_grade += $(this).val()+',';
        app_grade_name += $(this).attr('gradename')+',';
    });
    if(app_grade.length > 0){
        app_grade = app_grade.substr(0,app_grade.length-1);
        app_grade_name = app_grade_name.substr(0,app_grade_name.length-1);
    }
    var app_pic = $('#filename').val();
    var app_drumbeating = $.trim($('#drumbeating').val());
    var app_object = $.trim($('#app_object').val());
    var app_target = $.trim($('#app_target').val());
    var filename_feat = $.trim($('#filename_feat').val());
    var filename_content = $.trim($('#filename_content').val());
    var filename_teac = $.trim($('#filename_teac').val());
    if($('input[name=issource]').is(':checked')){
        app_issource = 1;
    }
    else{
        app_issource = 0;
    }
    var app_source_code = $('#source').val();
    if(app_name == ''){
        dialogTips('应用名称不能为空');
        $('#appname').focus();
        return;
    }
    if(app_subject_code == 'all'){
        dialogTips('请选择所属学科');
        return;
    }
    if(app_grade ==''){
        dialogTips('请选择所属年级');
        return;
    }
    // if(app_pic == '' || app_pic == 'noimg'){
    //     dialogTips('请上传应用图片');
    //     return;
    // }
    // if(filename_feat == '' || filename_feat == 'noimg'){
    //     dialogTips('请上传课程特色图片');
    //     return;
    // }
    //  if(filename_content == '' || filename_content == 'noimg'){
    //     dialogTips('请上传课程内容图片');
    //     return;
    // }
    //  if(filename_teac == '' || filename_teac == 'noimg'){
    //     dialogTips('请上传课程名师图片');
    //     return;
    // }
    //console.log('go');
    $.post('/Elearnmanager/classapp/SaveApp',{id:id,app_isterm:app_isterm,app_isunit:app_isunit,app_isspecial:app_isspecial,app_issource:app_issource,app_name:app_name,app_subject_code:app_subject_code,app_unit_type:app_unit_type,app_special_code:app_special_code,app_grade:app_grade,app_grade_name:app_grade_name,app_pic:app_pic,app_drumbeating:app_drumbeating,app_object:app_object,app_target:app_target,filename_feat:filename_feat,filename_content:filename_content,filename_teac:filename_teac,app_source_code:app_source_code},function(result){
            dialogNotice('添加状态',result.message,3);
           var addhref = '/Elearnmanager/classapp/applist?subject_code='+app_subject_code;
            $(window.parent.frames['leftFrame'].document).find('.liapplist').click();
            window.location.href=addhref;
    });

}
function SaveCourse(id){
    //alert('ss');
    var app_isterm = 1,app_isunit=1,app_isspecial=1,app_issource=1;
    var course_name = $.trim($('#coursename').val());
    var app_subject_code = $('#subjectlist').val();
    var app_id = $('#applist').val();
    var version_id = 0;
    var unit_id = 0;
    if($('input[name=isterm]').is(':checked')){
        version_id = $('#versionlist').val();
    }
    else{
        version_id = 0;
    }
    if($('input[name=isunit]').is(':checked')){
        unit_id = $('#unitlist').val();
    }
    else{
        unit_id = 0;
    }
     if(app_subject_code == 'all'){
        dialogTips('请选择所属学科');
        return;
    }
    if($('input[name=isunit]').is(':checked') && unit_id==0){
        dialogTips('该版本下没有单元，请先添加单元');
        return;
    }
    var course_pic = $('#filename').val();
    var course_big_pic = $('#filename2').val();
    var course_code = $.trim($('#coursecode').val());
    if(course_name == ''){
        dialogTips('视频名称不能为空');
        $('#coursename').focus();
        return;
    }
    // if(course_pic == ''){
    //     dialogTips('请上传视频缩略图片');
    //     return;
    // }
     if(course_code == ''){
        dialogTips('视频编码不能为空');
        $('#coursecode').focus();
        return;
    }
    //console.log('go');
    $.getJSON('/Elearnmanager/classapp/SaveCourse',{id:id,app_id:app_id,course_name:course_name,app_subject_code:app_subject_code,version_id:version_id,unit_id:unit_id,course_pic:course_pic,course_big_pic:course_big_pic,course_code:course_code},function(result){
            dialogNotice('添加状态',result.message,3);
           var addhref = '/Elearnmanager/classapp/app_course_list?app_id='+$('#applist').val()+'&subject_code='+$('#subjectlist').val()+'&version_id='+$('#versionlist').val()+'&unit_id='+$('#unitlist').val();
            $(window.parent.frames['leftFrame'].document).find('.licourselist').click();
            window.location.href=addhref;
    });

}
function upAppsort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("BID");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_app_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getAppList();
        dloading.close();
    });
}
function upCoursesort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("bid");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_course_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getCourseList(app_id,isterm,isunit);
        dloading.close();
    });
}
function upQuesort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("bid");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_que_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getQueList();
        dloading.close();
    });
}

function getVersionList(){
  var subject_code = $('#subjectlist').val();
  var grade_code = $('#gradelist').val();
  var term_code = $('#termlist').val();
  var app_id = $('#applist').val();
  if(subject_code == 'all'){
    dialogTips('请选择所属学科');
    flag = false;
    return;
  }
  var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
    $("#table_data td").parents("tr").remove();
    $.getJSON("/Elearnmanager/getbasedata/getVersion", {app_id:app_id,subjectcode:subject_code,grade_code:grade_code,term_code:term_code,random: Math.random()}, function(data) {

        $.each(data, function(i, val) {

            var tr = $("#table_demo tr").eq(0).clone();
            var td = tr.children('td').eq(0);
            td.html(i+1);

            td = tr.children('td').eq(1);
            td.html(val.r_subject_name);
            td = tr.children('td').eq(2);
            td.html(val.r_grade_name);
            td = tr.children('td').eq(3);
            td.html(val.r_term_name);
            td = tr.children('td').eq(4);
            td.html(val.r_version_name);
         td = tr.children('td').eq(5);
            td.html('<img src="/'+val.r_pic+'" width="100" height="155"/>');
            td = tr.children('td').eq(6);
            td.html(val.r_remark);
            tr.find("input").attr("bid", val.id);
         tr.find("input").attr("bpic", val.r_pic);
            tr.appendTo("#table_data");
        });
    });
    $(".tr:odd").css("background", "#F5F8FA");
    dloading.close();

}
function editversionimg(versionid,pic){
  var myDialog =$.dialog.open('/Elearnmanager/classapp/version_img_update?pic='+pic,
  {
    id:'explains_update',
    title:'修改版本图片',
    window:'top',
    width:900,
    height:600,
    lock:true,
    opacity:0.3,
    button: [
      {
        name: '保存',
        callback: function () {  
          var iframe = this.iframe.contentWindow;
          var pic=$('#filename',iframe.document).attr('value'); 
          var closeflag = true;
         //alert(r_grade+"=="+r_volume+"=="+r_version+"=="+imgid+"=="+pic);
           $.get("/Elearnmanager/classapp/version_img_update_action?rt=" + Math.random(),{id:versionid,pic:pic},function(data) {
              if ($.trim(String(data)) != "1") {            
                dialogNotice("系统提示","更新失败，请与管理员联系",30);
                closeflag = false;   
              } 
              else{
                dialogNotice("系统提示","保存成功" ,30);
                location.reload();
             
              }            
          });
          return closeflag;
        },
        focus: true
      },
      {
        name: '关闭',
        callback: function () {
          location.reload();
      
          return true; 
        },
        focus: false
      }
    ]
  });
}
//添加版本
    function versionadd()
    {
      art.dialog.open('/Elearnmanager/classapp/version_add?subject_code='+$('#subjectlist').val()+'&app_id='+$('#applist').val()+'&grade_code='+$('#gradelist').val()+'&term_code='+$('#termlist').val(),{
            title:"添加版本",
            width:900,
            height:600,
            lock:true,
            opacity:0.3,
            button: [
              {
                name: '保存',
                callback: function () { 
                  var iframe = this.iframe.contentWindow;     
                  var re = iframe.versionadd();
                  if (re) { window.location.reload();return true;}
                  else{return false;}
                  },
                focus: true
              },
              {
                name: '关闭',
                callback: function () { 
                  //getUnitExamQuestionsList();
                },
                focus: false
              }
            ]
          });   
    }
//根据学科改变版本
    function change_version(){
        $.EBC.setVersion($('#versionlist'),$('#subjectlist').val(),$('#applist').val(),0);
        getUnitList();
    }

//添加单元
    function unitadd(subject_code,version_id,app_id,id,isterm)
    {
        var title = '添加单元';
        if(id > 0){
            title = "编辑单元";
        }
        var dourl = "";
        if(isterm == "1"){
            dourl = "resSelectFromKsCCM";
        }
        else{
            dourl = "unit_add";
        }

      art.dialog.open('/Elearnmanager/classapp/'+dourl+'?id='+id+'&subject_code='+subject_code+'&version_id='+version_id+'&app_id='+app_id+'&isterm='+isterm,{
            title:title,
            width:900,
            height:600,
            lock:true,
            opacity:0.3,
            button: [
              {
                name: '保存',
                callback: function () { 
                  var iframe = this.iframe.contentWindow;    
                  //var pic=$('#filename',iframe.document).attr('value'); 
                  var subject_code = $('#subjectlist',iframe.document).val();
                  var version_id = $('#versionlist',iframe.document).val(); 
                  var re = iframe.unitadd();
                  if (re) {
                  // window.location.href='/Elearnmanager/classapp/unitlist?subject_code='+subject_code+'&version_id='+version_id;
                  getUnitList();
                   return true;
                  }
                  else{return false;}
                  },
                focus: true
              },
              {
                name: '关闭',
                callback: function () { 
                  //getUnitExamQuestionsList();
                },
                focus: false
              }
            ]
          });   
    }
    //  * getUnitList 获取单元列表
//  */
function getUnitList() {
    var version_id = $("#versionlist").val();
    var app_id = $("#applist").val();
        //var dloading = art.dialog({time: 30, title: '加载中……', width: 130, height: 30, opacity: 0.3, lock: true});
        $("#table_data td").parents("tr").remove();
        $.getJSON("/Elearnmanager/classapp/getUnitList", {version_id: version_id,app_id:app_id,selec_unit_type:selec_unit_type,random: Math.random()}, function(data) {
     //alert(data.length);
            $.each(data, function(i, val) {
                i++;
              var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.find("input").attr("bid",val.id);
                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(val.sortid);
                td = tr.children('td').eq(2);
                td.html(val.name);
                td = tr.children('td').eq(3);
                if (val.is_click == '0') {
                    td.html('是');
                }
                else {
                    td.html('<font color="red">否</font>');
                }
                td = tr.children('td').eq(4);
                if (val.is_img == '1') {
                    td.html('是');
                }
                else {
                    td.html('<font color="red">否</font>');
                }
                td = tr.children('td').eq(5);
                if(val.is_img == '1'){
                    td.html('<img src="/'+val.pic+'" width="100" height="155"/>');
                }
                else{
                    td.html('');
                }
                td = tr.children('td').eq(6);
                td.html(val.ks_code);
                tr.find("input").attr("bid", val.id);
                tr.appendTo("#table_data");
            });
        });
        $(".tr:odd").css("background", "#F5F8FA");
       // dloading.close();
    
}
function upUnitsort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("bid");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_unit_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getUnitList();
        dloading.close();
    });
}

function getQueList(){
    var course_id = $('#videolist').val();
    $("#table_data td").parents("tr").remove();
    $.getJSON('/Elearnmanager/classapp/getQueList',{course_id:course_id,random:Math.random()},function(data){
          var i = 0;
          var htmlstr='';
            $.each(data, function(i, val) {
                i++;
                htmlstr = '';
                var tr = $("#table_demo tr").eq(0).clone();
                var td = tr.children('td').eq(0);
                td.find("input").attr("bid",val.id);
                td = tr.children('td').eq(1);
                var input = td.children('input');
                input.val(val.sortid);
                td = tr.children('td').eq(2);
                td.html(val.content);
                td = tr.children('td').eq(3);
                if(val.type == 1){
                    htmlstr = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form_table" >';
                    $(val.itemsdata).each(function(j,itemval){

                        if(val.itemtype == 1){
                            htmlstr += '<tr><td>'+itemval.flag+'：</td><td><img src="/'+itemval.content+'"  width="100" height="100"/ style="border:1px solid #CCC; padding:2px;" ></td></tr>'; 
                        }
                        else{
                             htmlstr += '<tr><td>'+itemval.flag+'：</td><td>'+itemval.content+'</td></tr>'; 
                        }
                    });
                    htmlstr += '</table>';
                }   
                td.html(htmlstr);
                td = tr.children('td').eq(4);
                if(val.type == 1){
                   td.html('选择题'); 
                }
                else{
                    td.html('判断题');
                }
                td = tr.children('td').eq(5);
                if(val.itemtype == 1){
                   td.html('图片'); 
                }
                else{
                    td.html('文字');
                }
                td = tr.children('td').eq(6);
                if(val.type==2){
                    if(val.answer == 0){
                        td.html('×');
                    }
                    else{
                        td.html('√');
                    }
                }
                else{
                    td.html(val.answer);
                }
                tr.find("input").attr("bid", val.id);
                tr.appendTo("#table_data");
            });  
    });
}

//添加试题
    function queadd(course_id,id,app_id)
    {
        var title = '添加试题';
        if(id > 0){
            title = "编辑试题";
        }
      art.dialog.open('/Elearnmanager/classapp/que_add?id='+id+'&course_id='+course_id+'&app_id='+app_id,{
            title:title,
            width:900,
            height:600,
            lock:true,
            opacity:0.3,
            button: [
              {
                name: '保存',
                callback: function () { 
                  var iframe = this.iframe.contentWindow;    
                  //var pic=$('#filename',iframe.document).attr('value'); 
                  var subject_code = $('#subjectlist',iframe.document).val();
                  var version_id = $('#versionlist',iframe.document).val(); 
                  var re = iframe.queaddshow();
                  if (re) {
                    getQueList();
                   //window.location.href='/Elearnmanager/classapp/unitlist?subject_code='+subject_code+'&version_id='+version_id;
                   return true;
                  }
                  else{return false;}
                  },
                focus: true
              },
              {
                name: '关闭',
                callback: function () { 
                  //getUnitExamQuestionsList();
                },
                focus: false
              }
            ]
          });   
    }
    function getClaimList(){
        var course_id = $('#videolist').val();
        $("#table_data td").parents("tr").remove();
        $.getJSON('/Elearnmanager/classapp/getClaimList',{course_id:course_id,random:Math.random()},function(data){
              var i = 0;
              var htmlstr='';
                $.each(data, function(i, val) {
                    i++;
                    htmlstr = '';
                    var tr = $("#table_demo tr").eq(0).clone();
                    var td = tr.children('td').eq(0);
                    td.find("input").attr("bid",val.id);
                    td = tr.children('td').eq(1);
                    var input = td.children('input');
                    input.val(val.sortid);
                    td = tr.children('td').eq(2);
                    td.html(val.content);
                    tr.find("input").attr("bid", val.id);
                    tr.appendTo("#table_data");
                });  
        });

}
function upCalimsort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("bid");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_que_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getClaimList();
        dloading.close();
    });
}
function getEssayList(){
        var course_id = $('#videolist').val();
        $("#table_data td").parents("tr").remove();
        $.getJSON('/Elearnmanager/classapp/getEssayList',{course_id:course_id,random:Math.random()},function(data){
              var i = 0;
              var htmlstr='';
                $.each(data, function(i, val) {
                    i++;
                    htmlstr = '';
                    var tr = $("#table_demo tr").eq(0).clone();
                    var td = tr.children('td').eq(0);
                    td.find("input").attr("bid",val.id);
                    td = tr.children('td').eq(1);
                    var input = td.children('input');
                    input.val(val.sortid);
                    td = tr.children('td').eq(2);
                    td.html(val.content);
                    tr.find("input").attr("bid", val.id);
                    tr.appendTo("#table_data");
                });  
        });

}
function upEssaysort() {
    var dloading = art.dialog({time: 30, title: '更新中……', width: 130, height: 30, opacity: 0.3, lock: true});
    if ($("#table_data tr.tr").length == 0)
        return;
    var arrjson = [];
    $("#table_data tr.tr").each(function() {
        var tr = $(this);
        var id = tr.find('input[name="sortid"]').attr("bid");
        var sortid = tr.find('input[name="sortid"]').val();
        var obj = {};
        obj.id = id;
        obj.sortid = sortid;
        arrjson.push(obj);
    });
    $.get("/Elearnmanager/classapp/edit_que_sort", {data: JSON.stringify(arrjson)}, function(result) {
        dialogTips(result.msg);
        getEssayList();
        dloading.close();
    });
}
//添加范文
    function essayadd(course_id,id,app_id)
    {
      art.dialog.open('/Elearnmanager/classapp/essay_add?id='+id+'&course_id='+course_id+'&app_id='+app_id,{
            title:"添加范文",
            width:1000,
            height:700,
            lock:true,
            opacity:0.3,
            button: [
              {
                name: '保存',
                callback: function () { 
                  var iframe = this.iframe.contentWindow;     
                  var re = iframe.essayadd();
               
                  if (re) {
                   getEssayList();
                   return true;
                }
                  else{return false;}
                  },
                focus: true
              },
              {
                name: '关闭',
                callback: function () { 
                  //getUnitExamQuestionsList();
                },
                focus: false
              }
            ]
          });   
    }
    //添加要求
    function claimadd(course_id,id,app_id)
    {
      art.dialog.open('/Elearnmanager/classapp/claim_add?id='+id+'&course_id='+course_id+'&app_id='+app_id,{
            title:"添加范文",
            width:1000,
            height:700,
            lock:true,
            opacity:0.3,
            button: [
              {
                name: '保存',
                callback: function () { 
                  var iframe = this.iframe.contentWindow;     
                  var re = iframe.claimadd();
               
                  if (re) {
                   getClaimList();
                   return true;
                }
                  else{return false;}
                  },
                focus: true
              },
              {
                name: '关闭',
                callback: function () { 
                  //getUnitExamQuestionsList();
                },
                focus: false
              }
            ]
          });   
    }
    function artopenunit_add(){
           art.dialog.open('../classapp/unit_add?id=0&subject_code='+$('#subjectlist').val()+'&version_id='+$('#versionlist').val()+'&app_id='+$('#applist').val()+'&isterm='+isterm+'&isunit='+isunit+'&unit_type='+unit_type+'&random='+Math.random(),{
                      title:'添加单元',
                      width:900,
                      height:600,
                      lock:true,
                      opacity:0.3,
                      button: [
                        {
                          name: '保存',
                          callback: function () { 
                            var iframe = this.iframe.contentWindow;    
                            //var pic=$('#filename',iframe.document).attr('value'); 
                            var subject_code = $('#subjectlist',iframe.document).val();
                            var version_id = $('#versionlist',iframe.document).val(); 

                            var re = iframe.unitadd();
                            var unit_id = $('#unitname',iframe.document).attr('unit_id'); 
                            if (re) {
                              unitlist(unit_id);
                             return true;
                            }
                            else{return false;}
                            },
                          focus: true
                        },
                        {
                          name: '关闭',
                          callback: function () { 
                            //getUnitExamQuestionsList();
                          },
                          focus: false
                        }
                      ]
                    }); 
    }
function imgupload(obj,filename){
    var flag = false;
  var imgsrc = '';
    //document.getElementById("uploadPhoto").src = "<%=basePath%>uc/teacher/uploadPhoto.jsp";
    var dialog = art.dialog({
        padding:0,
        //top:50,
        width:760,
        height:260,
        title:'图片上传',
        content: document.getElementById('edit_tx2'),
        lock:true,
        opacity: 0.2,
    ok: function(){
      imgsrc = $("#uploadPhoto").contents().find("#uploader").attr('imgsrc');
      if(typeof(imgsrc) != 'undefined' || imgsrc != ''){
        //console.log(imgsrc);
        
          $(obj).attr('src','/'+imgsrc);
          $(filename).val(imgsrc);
      }
      
      return true;
    },
    cancelVal: '取消',
    cancel: true
    });
    
}
// 获取URL中param参数的值
function getParameter(param) {
    var query = window.location.search;
    var iLen = param.length;
    var iStart = query.indexOf(param);
    if (iStart == -1)
        return null;
    iStart += iLen + 1;
    var iEnd = query.indexOf("&", iStart);
    if (iEnd == -1)
        return query.substring(iStart);
    return query.substring(iStart, iEnd);
}