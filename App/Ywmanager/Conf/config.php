<?php

return array(
    // //数据库配置信息
     'DB_TYPE' => 'mysql', // 数据库类型
     'DB_HOST' => 'localhost', // 服务器地址
     'DB_NAME' => 'db_yuwen', // 数据库名
      'DB_USER' => 'vcom', // 用户名
      'DB_PWD' => 'yjt_yyl20160309', // 密码
     'DB_PORT' => 3306, // 端口
     'DB_PREFIX' => 'yw_', // 数据库表前缀
     'DB_CHARSET' => 'utf8', // 字符集
     'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

   //  'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
   //  'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    'CONST_UPLOADS' => './uploadsyw/kewenvoice/',
    'CONST_UPLOADS_IMG' => './uploadsyw/versionimg/',

    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
            '__PUBLIC__'=> __ROOT__. '/public/' . MODULE_NAME.'',
    ),
);
