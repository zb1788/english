<?php
return array(
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'=>  __ROOT__. '/public/' . MODULE_NAME.'/',
	),
	'CACHE_FILE_ROOT' => $_SERVER["CONTEXT_DOCUMENT_ROOT"].__ROOT__."/app/".MODULE_NAME."/View/Cache/Mobile", //缓存文件根目录
);