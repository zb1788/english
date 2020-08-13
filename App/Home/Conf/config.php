<?php

return array(
    'TMPL_PARSE_STRING' => array(
        '__Pcss__' => __ROOT__ . '/public/public/css/',
        '__Pimg__' => __ROOT__ . '/public/public/images/',
        '__Pjs__' => __ROOT__ . '/public/public/js/',
        '__Hcss__' => __ROOT__ . '/public/home/css/',
        '__Himg__' => __ROOT__ . '/public/home/images/',
        '__Hjs__' => __ROOT__ . '/public/home/js/',
        '__resource_path__' => "<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
        '__device_service__' => "<?php echo PROTOCOL.'://'.WEB_DOMAIN.'/';?>",

    ),
    'DATA_CACHE_TYPE'=>'file',//设置缓存方式为file     
 	//'DATA_CACHE_TIME'=>'36000',//缓存周期600秒 
 	'DATA_CACHE_TIME'=>'600',//缓存周期600秒 
 	'VAR_JSONP_HANDLER' => 'jsoncallback',
);
