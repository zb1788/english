<?php
return array(
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'=>  __ROOT__. '/public/' . MODULE_NAME.'/',
		'__IMG__'=>  '/uploads/',
		'__COMMON__'=>  __ROOT__. '/public/public/',
		'__MP3__' =>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN;?>",
		'__RECORD__' =>"<?php echo PROTOCOL.'://'.C('device_service');?>",
		'__RESOURCE__' =>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
		'__resource_path__' => "<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
		//'VAR_JSONP_HANDLER' => 'jsoncallback',
	),
	'smartstudy_api' =>'new', //用户评分用的接口，如果用老接口请修改值为“old”,如果不使用接口使用随机数请改值为“rand”;
);
