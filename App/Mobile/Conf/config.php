<?php
return array(
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'=>  __ROOT__. '/public/' . MODULE_NAME.'/',
		'__COMMON__'=>  __ROOT__. '/public/',
		'__RESOURCE__'=>"<?php echo PROTOCOL.'://'.C('resource_path'); ?>",
	),
	'CACHE_FILE_ROOT' => $_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/App/".MODULE_NAME."/View/Cache/Mobile", //缓存文件根目录
	'userexamlisten'=>15,//配置学生练习的时间
);