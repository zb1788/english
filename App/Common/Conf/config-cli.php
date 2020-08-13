<?php
return array(
    'MODULE_ALLOW_LIST' => array('Home', 'Mobile','Klx','Homework','Pubinterface','Manager','Subject','Yuwen','Shuxue','Elearn','Elearnmanager','Sdfree','Error'),
    'DEFAULT_MODULE' => 'Home',
    //数据库配置信息
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '192.168.148.106', // 服务器地址
    'DB_NAME' => 'db_english', // 数据库名
    'DB_USER' => 'enjsbvcom', // 用户名
    'DB_PWD' => 'gT_Q_qMbiVR9eQGp', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'engs_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
    'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    'SHOW_PAGE_TRACE' => false, //调试模式
    'SESSION_AUTO_START' => true, //是否开启session
    'URL_HTML_SUFFIX' => '', //路由功能
    'ERROR_PAGE'=>'/Error/Index/404.html',
    'URL_MODEL' => '2',
    'URL_CASE_INSENSITIVE' => true, //不区分路由的大小写
    /* 自定义常量，学期常量 */
    'CONST_TERMID' => '0001',
    'CONST_VERSIONID' => '0049',
    'CONST_GRADEID' => '0003',
    'CONST_TERMNAME' => '上学期',
    'CONST_VERSIONNAME' => '人教PEP',
    'CONST_GRADENAME' => '三年级',
    'CONST_UPLOADS' => './uploads/',
    'CONST_UPLOADS_BOOK' => './uploads/book/pic/',
    'CONST_UPLOADS_BOOK_MP3' => './uploads/book/mp3/',
    'CONST_UPLOADS_EXAM' => '/home/yylmp3/uploads/',
    'CONST_FLASH_PATH'=>'/yylmp3/flash/',
    'game_pic_path' => '/yylmp3/uploads/game/pic/',
    'book_pic_path' => RESOURCE_DOMAIN.'/yylmp3/uploads/book/pic/',
    'book_mp3_path' => RESOURCE_DOMAIN.'/yylmp3/uploads/book/mp3/',
    'mp3_word_explain'=> RESOURCE_DOMAIN.'/yylmp3/mp3_word_explain/',
    'resource_domain'=>RESOURCE_DOMAIN,
    'contact_mp3_path'=>RESOURCE_DOMAIN.'/yylmp3/mp3_contact/',
    'example_pic_path'=>RESOURCE_DOMAIN.'/yylmp3/pic_word_example/',
    'example_mp3_path'=>RESOURCE_DOMAIN.'/yylmp3/mp3_word_example/',
    'word_mp3_path' => RESOURCE_DOMAIN.'/yylmp3/mp3_word/',
    'word_pic_path' => RESOURCE_DOMAIN.'/yylmp3/pic_word/',
    'text_mp3_path' => RESOURCE_DOMAIN.'/yylmp3/mp3_text/',
    'exams_mp3_path' => RESOURCE_DOMAIN.'/yylmp3/mp3_exam/', //听力训练音频地址
    'record_mp3_path' => '/recordwav/',                         //用户录音文件目录
    'resource_path' => RESOURCE_DOMAIN.'/yylmp3/',  //资源总目录
    'user_record_path'=>WEB_DOMAIN.'/recordwav/',
    'device_service' => WEB_DOMAIN,  //随堂训练域名地址 需要手动配置（都是web域名）
    'local_service' => WEB_DOMAIN,  //本机域名地址  需要手动配置（都是web域名）
    'web_domain'=>WEB_DOMAIN,
    'recordpath'=>'/home/yylmp3/recordwav/', //用户上传的音频的地址
    'URL_ROUTER_ON'   => true,//使用tp的路由
    'URL_ROUTE_RULES'=>array(
        '/^Subject\/recomment(.*)$/' => 'Subject/Index/recomment',
    ),
    'share_url_path'=>WEB_DOMAIN.'/appinfo/share/',
    'protocol'=>"<?php echo PROTOCOL;?>",
    'respreview'=>'/Homework/Listen/Index',
    'ubname'=>'优币',
);
