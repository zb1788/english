(function() {

	window.sets_version = '151030';
	//	window.sets_version = new Date().getTime();

	seajs.config({
		charset : 'utf-8',
		base : SETS_ROOT,
		alias : { // libs:
			//			'jquery':'lib/jquery/1.9.1/jquery.js',
			'urianchor' : 'lib/urianchor/jquery.uriAnchor',
			'gevent' : 'lib/gevent/jquery.event.gevent-0.1.9',
			'bootsets' : 'lib/bootsets/js/sets',
			'jsviews' : 'lib/jsviews/1.0.0-alpha/back/jsviews',
			'moment' : 'lib/moment/moment',
			'move' : 'lib/layout/jquery.event.move',
			'scrollbar' : 'lib/scrollbar/jquery.mCustomScrollbar',
			'wheel' : 'lib/scrollbar/jquery.mousewheel',
			'datetimepicker' : 'lib/datetimepicker/js/bootstrap-datetimepicker'
		//'shimplaceholder' : 'lib/shimplaceholder/jquery.html5-placeholder-shim'
		},
		//		preload : [ 'gevent', 'bootstrap', 'jsviews', 'urianchor', 'jquery', 'seajs-text' ],
		preload : [ 'gevent', 'bootsets', 'jsviews', 'urianchor' ],
		map : [ [ /^(.*\.(?:css|js))(.*)$/i, '$1?__v=' + window.sets_version ] ]
	});
	seajs._route = {
		production : false,
		appPath : function(appName) {
			if (this.production) {
				return 'dist/' + appName.split('/').pop();
			}
			return appName;
		}
	};

	seajs.use(seajs._route.appPath('core/utils'), function(app) {
		$(function() {
			if (app.isRetina()) {
				$('body').addClass('retina');
			}
		});
	});

})();
