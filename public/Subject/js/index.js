require.config({
　baseUrl: "../../public/public/js",
　paths: {
　　"zepto": "zepto.min",
　　"touchslide": "TouchSlide.1.1",
    "iscroll":"iscroll-lite",
    'layer':'layer',
　},
  shim: {
　　　　　　'enajax': {
　　　　　　　　deps: ['zepto'],
　　　　　　　　exports: 'enajax'
　　　　　　},
            'iscroll': {
　　　　　　　　exports: 'iscroll'
　　　　　　},
            'layer': {
　　　　　　　　exports: 'layer'
　　　　　　}
　　　　},
waitSeconds: 0
});
require(['zepto','enajax','touchslide','iscroll','layer'], function($,enajax,touchslide,IScroll,layer){
        console.log(window.history);
        //swindow.history.replaceState(null, "baidu", "http://192.168.133.14:8002/ensvn/Subject/Word/wordunit?moduleid=1");
        //图片左右滑动
        TouchSlide({ 
            slideCell:"#focus",
            titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
            mainCell:".bd ul", 
            effect:"left", 
            autoPlay:true,//自动播放
            autoPage:true, //自动分页
            switchLoad:"../images" //切换加载，真实图片路径为"_src" 
        });
        //进行首页的数据的请求
        $.ajax({
            type:'GET',
            url:'getEnUserinfo?ran='+Math.random(),
            dataType:'json',
            async:false,
            context:$('body'),
            success: function(data){
                //遮罩消失
                hideloading();
                var jmodule="";
                var zmodule="";
                $.each(data,function(k,v){
                    //基础模块的添加
                    if(v.typeid=="0"){
                        jmodule=jmodule+'<li class="listIconText" moduleid='+v.id+' url="../'+v.url+'?moduleid='+v.id+'">';
                        if(v.iffree=='0'){
                            //展示是否免费
                            //jmodule=jmodule+'    <span class="edi-jbiao">免费</span>';
                        }
                        jmodule=jmodule+v.style;
                        jmodule=jmodule+'    <span class="listText"><h3 class="textH3">'+v.title+'</h3>';
                        if(v.count==null||v.count==undefined){
                            jmodule=jmodule+'        <h4 class="textH4"><em class="ln">0人学习</em></h4>';
                        }else{
                            jmodule=jmodule+'        <h4 class="textH4"><em class="ln">'+v.count+'人学习</em></h4>';
                        }
                        jmodule=jmodule+'    </span>';
                        jmodule=jmodule+'</li>';
                    }else if(v.typeid=="1"){
                        //高级模块的添加
                        zmodule=zmodule+'<li class="listIconText" moduleid='+v.id+' url="\'../'+v.url+'?moduleid='+v.id+'\'">';
                        zmodule=zmodule+'    <span class="fr lineH55"><i class="icon-right"></i></span>';
                        zmodule=zmodule+v.style;
                        zmodule=zmodule+'    <span class="listText"><h3 class="textH3">'+v.title+'<em class="font08">1.88万人订购<font class="redFont">￥10.00</font></em></h3>';
                        zmodule=zmodule+'        <h4 class="textH4"><em class="ln">'+v.remark+'</em></h4>';
                        zmodule=zmodule+'    </span>';
                        zmodule=zmodule+'</li>';
                    }
                })
                $("#basemodel").html(jmodule);
                if(zmodule!=""){
                    $("#advance").show();
                    $("#advancemodel").html(zmodule);
                }
            },
            error:function(xhr,type){

            }
        })

    


    //滑动问题
    new IScroll("#wrapper",{
        momentum:true,
        click:true 
    });
    $("#wrapper").resize();

    //首页进行跳转
    $(".listIconText").click(function(){
        var url=$(this).attr("url");
        var moduleid=$(this).attr("moduleid");
        //用户的日志的记录
        createIframe($("body"),"../Public/setUserModuleUnitLog?ks_code=0&moduleid="+moduleid);
        window.location.href="../"+url;
    })

});


