var maxZoom=3;
var minZoom=1.5;
var zoom=2;
var pagezoom = zoom;
var screenW = 0;
screenW = window.screen.width;
//alert(screenW);
if(screenW>1360){
	pagezoom=Number((screenW/1360).toFixed(1));
}

function zoomIn(selector) {
            zoom += 0.1;
            if(zoom>maxZoom) {
              zoom = maxZoom;
            } 
            
            $(selector).css("zoom",zoom);
          }
function zoomOut(selector) {
            // zoom -= 0.1;
            // if(zoom<minZoom) {
            //   zoom = minZoom
            //    $(selector).css("zoom",1);
            // }
            // else{
            //    $(selector).css("zoom",zoom);
            // }
           zoom -= 0.1;
            if(zoom<minZoom) {
              zoom = minZoom
            }
            
            $(selector).css("zoom",zoom);
          }
          
(function($) {
  $.fn.zoom = function(options) {  
    // build main options before element iteration  
    var opts = $.extend({}, $.fn.zoom.defaults, options);  
    // iterate and reformat each matched element  
    return this.each(function() {  
      $this = $(this);  
      // build element specific options  
      var o = $.meta ? $.extend({}, opts, $this.data()) : opts;  
      // update element styles  
      $this.css({  
        zoom: o.currentZoom
      }); 
      
      // 放大事件
      $("#"+o.opBtnId+"_zoomIn").click( function () { zoomIn($this,o); });

      // 缩小事件
      $("#"+o.opBtnId+"_zoomOut").click( function () { zoomOut($this,o); });
      
    });  
  };  
    // 私有函数：zoomIn  
  function zoomIn(selector,opts) {
            opts.currentZoom += 0.1;
            if(opts.currentZoom>opts.maxZoom) {
              opts.currentZoom = opts.maxZoom;
            } 
            
            selector.css("zoom",opts.currentZoom);
          }
  function zoomOut(selector,opts) {
            opts.currentZoom -= 0.1;
            if(opts.currentZoom<opts.minZoom) {
              opts.currentZoom = opts.minZoom
            }
            
            $(selector).css("zoom",opts.currentZoom);
          }  
  // 定义暴露format函数  
  $.fn.zoom.format = function(txt) {  
    return '<strong>' + txt + '</strong>';  
  };  
  // 插件的defaults  
  $.fn.zoom.defaults = {  
    maxZoom: 3,  
    minZoom: 1.5,
    currentZoom: 2,
    opBtnId:'zoom'
  };
})(jQuery);               