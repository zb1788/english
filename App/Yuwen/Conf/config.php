<?php

return array(
    // 数据库配置信息
    'DB_NAME' => 'db_yuwen', // 数据库名
    'DB_PREFIX' => 'yw_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集

    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
            '__PUBLIC__'=> __ROOT__. '/public/' . MODULE_NAME.'',
			'__ROOTPATH__'=> __ROOT__. '/public/'.'',
            '__RESOURCE__'=>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
            '__RESOURCIMG__'=>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
            '__RECORD__'=>"<?php echo PROTOCOL.'://'.WEB_DOMAIN.'/recordwav/';?>",
    ),
);
