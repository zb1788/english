//获取作业的内容数据
//定义paper类其中参数全部是json数据
var HomeWork = {
  createNew: function(data){
      var homework = {};
      HomeWork.work=data;
      HomeWork.getWordlist=function(data){
      	$(".wordlist").empty();
        var worddata=getdata('getWordlist',{rs_code:data.rs_code,ran:Math.random()},2);
        var content="";
        $.each(worddata,function(key,val){
           content=content+'<li class="mui-table-view-divider tab-zhu"><span class="tab-wb80">'+val.name+'</span><span class="tab-wb20"><i class="fa fa-square-o fa-lg"></i></span></li>';
        });
        $(".wordlist").html(content);
      };
      homework.getWordlisten = function(data){
        $(".wordlisten").empty();
        var worddata=getdata('getWordlisten',{rs_code:data.rs_code,ran:Math.random()},2);
        var content="";
        $.each(worddata,function(key,val){
           content=content+'<li class="mui-table-view-divider tab-zhu"><span class="tab-wb80">'+val.name+'</span><span class="tab-wb20"><i class="fa fa-square-o fa-lg"></i></span></li>';
        });
        $(".wordlisten").html(content);
      };
      Exams.getChapterlist=function(data){
        $(".textchapter").empty();
        var worddata=getdata('getTextchapter',{rs_code:data.rs_code,ran:Math.random()},2);
        var content="";
        $.each(worddata,function(key,val){
           content=content+'<li class="mui-table-view-divider tab-zhu"><span class="tab-wb80">'+val.name+'</span><span class="tab-wb20"><i class="fa fa-square-o fa-lg"></i></span></li>';
        });
        $(".textchapter").html(content);
      };
      Exams.getExamlist=function(data){
        $(".examlist").empty();
        var worddata=getdata('getExamlist',{rs_code:data.rs_code,ran:Math.random()},2);
        var content="";
        $.each(worddata,function(key,val){
           content=content+'<li class="mui-table-view-divider tab-zhu"><span class="tab-wb80">'+val.name+'</span><span class="tab-wb20"><i class="fa fa-square-o fa-lg"></i></span></li>';
        });
        $(".examlist").html(content);
      };
};









