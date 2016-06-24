<?php

return [
    'DEBUG' => false, // 是否开启调试模式，默认不开启
    'ROUTE_TYPE' => 1, // 路径模式，1普通，2rewrite
    'ROUTE_CUT' => '/', // 路径切割符
    'INDEX' => 'index', // 默认控制器

    'CLASS_EXT' => '.class.php', // 类文件后缀名
    'VIEW_EXT' => '.view.php', // 模版文件后缀名

    'CORE_LANG' => 'en_US', // 内核信息语言
    'LANG' => 'en_US', // 应用语言
    'CHARSET' => 'UTF-8', // 字符集

    'DOMAIN' => $_SERVER['HTTP_HOST'], // 域名
    'HOST' => ((isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http').'://'.$_SERVER['HTTP_HOST']).'/', // host地址

    'BOOTSTRAP_PREFIX' => 'init', // Bootstrap启动方法前缀
    'SKIN_NAME' => 'default', // 模版名称
    'PATH' => '/', // 默认路径
    'IGNORE_CDN' => true, // 是否忽略CDN
    'CDN' => null, // CDN地址
    'DB' => null, // 默认数据库配置，默认为空
];