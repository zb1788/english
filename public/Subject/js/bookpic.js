(function(win,undefined){
	"use strict";
	var doc=win.document,
		nav=win.navigator,
		loc=win.location,
		html=doc.doccumentElement,
		klass=[],
		conf  = {
		    screens: [240, 320, 480, 640, 768, 800, 1024, 1280, 1440, 1680, 1920],
		    screensCss: {"gt": true, "gte": false, "lt": true, "lte": false, "eq": false},
		    browsers: [
		        {ie: {min: 6, max: 11}}
		        //,{ chrome : { min: 8, max: 33 } }
		        //,{ ff     : { min: 3, max: 26 } }
		        //,{ ios    : { min: 3, max:  7 } }
		        //,{ android: { min: 2, max:  4 } }
		        //,{ webkit : { min: 9, max: 12 } }
		        //,{ opera  : { min: 9, max: 12 } }
		    ],
		    browserCss: {"gt": true, "gte": false, "lt": true, "lte": false, "eq": true},
		    html5: true,
		    page: "-page",
		    section: "-section",
		    head: "head"
		};

	if(win.hrad_conf){
		for(var item in win.head_conf){
			if(win.head_conf[item]!==undefined){
				conf[item]=win.head_conf[item];
			}
		}
	}

	function pushClass(name) {
        klass[klass.length] = name;
    }

    function removeClass(name) {
        // need to test for both space and no space
        // https://github.com/headjs/headjs/issues/270
        // https://github.com/headjs/headjs/issues/226
        var re         = new RegExp(" ?\\b" + name + "\\b");
        html.className = html.className.replace(re, "");
    }

    function each(arr, fn) {
        for (var i = 0, l = arr.length; i < l; i++) {
            fn.call(arr, arr[i], i);
        }
    }

	var api=win[conf.head]=function(){
		api.ready.apply(null,arguments);
	}

	api.feature=function(key,enabled,queue){
		if(!key){
			html.className+=" "+klass.join(" ");
			klass=[];
			retur api;
		}

		if(Object.prototype.toString.call(enabled)){
			enabled=enabled.call();
		}

		pushClass((enabled?"":"no-")+key);

		api[key]=!!enabled;

		if(!queue){
			removeClass("no-"+key);
			removeClass(key);
			api.feature();
		}

		return api;
	};

	
})