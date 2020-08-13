<?php
return array(
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'=> __ROOT__. '/public/' . MODULE_NAME.'/',
		'__TERMID__'=> C('CONST_TERMID'),
		'__Pcss__' => __ROOT__ . '/public/public/css/',
		'__Pimg__' => __ROOT__ . '/public/public/images/',
		'__Pjs__' => __ROOT__ . '/public/public/js/',
		'__Hcss__' => __ROOT__ . '/public/home/css/',
		'__Himg__' => __ROOT__ . '/public/home/images/',
		'__Hjs__' => __ROOT__ . '/public/home/js/',
		'__UPLOAD__'=> __ROOT__. '/uploads/',
	),
	'CONST_VOICETYPE' => array(
			'1'	=> '中男Liang',
			'2'	=> '中女Hui',
			'3'	=> '美女Kate',
			'4'	=> '美女Salli',
			'5'	=> '美男Joey',
			'6'	=> '英女Bridget',
			'7'	=> '英男Brian',
	),
	'downloadmp3'=>"http://192.168.151.206/yylmp3/mp3_examtool/",
);
