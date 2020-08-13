<?php

return array(
    //数据库配置信息
    'DB_NAME' => 'db_happylearn', // 数据库名
    'DB_PREFIX' => 'hl_', // 数据库表前缀


    'DEFAULT_CONTROLLER'    =>  'Klxpy', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
            '__PUBLIC__'=> __ROOT__. '/public/' . MODULE_NAME.'',
	    '__RESOURCE__'=>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
    ),
);
