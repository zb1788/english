<?php

return array(
    //数据库配置信息
    'DB_NAME' => 'db_math', // 数据库名
    'DB_PREFIX' => 'sx_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
    'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

    //'配置项'=>'配置值'
    'default_term' =>'0001',
    'TMPL_PARSE_STRING' => array(
            '__PUBLIC__'=> __ROOT__. '/public/' . MODULE_NAME.'',
            '__SUBJECT__'=> __ROOT__. '/public/Subject/',
            '__ROOTPATH__'=> __ROOT__. '/public/'.'',
            '__RESOURCE__'=>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
    ),
);
