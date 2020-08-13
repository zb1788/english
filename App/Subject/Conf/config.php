<?php

return array(
    'TMPL_PARSE_STRING' => array(
        '__SUBJECT__'=> __ROOT__. '/public/' . MODULE_NAME.'/',
        '__PUBLIC__'=> __ROOT__. '/public/public/',
		'__YUWEN__'=> __ROOT__. '/public/Yuwen/',
        '__MP3__' => "<?php echo PROTOCOL.'://'.C('resource_path'); ?>",
        '__RESOURCE__' => "<?php echo PROTOCOL.'://'.C('resource_path'); ?>",
        '__ROOTPATH__'=> __ROOT__. '/public/'.'',
    ),
    'DATA_CACHE_TYPE'=>'file',//设置缓存方式为file
 	'DATA_CACHE_TIME'=>'600',//缓存周期600秒
 	'word_path'=>C("word_mp3_path"),
 	'VAR_JSONP_HANDLER' => 'jsoncallback',
 	'device_service' => C("web_domain"),
 	'reciteword_overdue'=>'30',//背单词保存时长30分钟
 	'readword_overdue'=>'30',//背单词保存时长30分钟
 	'readtimes'=>'3',
 	'examstime'=>'15',
 	'gradeid'=>'0001',
 	'subjectid'=>'0003',
 	'enversionid'=>'0011',
 	'enversionid2'=>'0049',
 	'enversionid1'=>'0001',//表示7、8、9年级默认版本
 	'cnversionid'=>'0001',
 	'termid'=>'0001',
 	'termid1'=>'0000',
 	'questypes'=>array('听音选意思','听音选词','汉译英','英译汉','单词选图片','句子选图片','单词拼写','语境选意思','语境选单词','真题单选','辨析单选'),
	 'perword'=>5,
	 'except' => array("60","61","62","75","76","82"),
	 'redistimeout'=>60,
);
