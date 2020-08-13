<?php

return array(
    'TMPL_PARSE_STRING' => array(
        '__ELEARN__'=> __ROOT__. '/public/' . MODULE_NAME.'/',
        '__SUBJECT__'=> __ROOT__. '/public/Subject/',
        '__PUBLIC__'=> __ROOT__. '/public/public/',
        '__RESOURCE__' =>"<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",    ),
 	'DB_NAME' => 'db_microclass', // 数据库名
    'DB_PREFIX' => 'mc_', // 数据库表前缀
    'reciteword_overdue'=>'30',//背单词保存时长30分钟
    'PIC_URL' => "<?php echo PROTOCOL.'://'.RESOURCE_DOMAIN.'/yylmp3/';?>",
     //是否对发送的评论内容进行敏感词过滤 0:不过滤；1:过滤
    'BLACK_WORD_FILTER' => '1' , 

    //敏感词过滤服务器IP,天津172.16.10.189 ;永城172.16.11.189; 江西移动172.16.33.195 ;内部测试:192.168.109.165
    'BLACK_WORD_FILTER_IP' => '192.168.109.165', 

    //评论过滤超时时间；
    'BLACK_WORD_FILTER_TIMEOUT' => '3', 

);
