<?php

return [
    'DEBUG' => true, // 是否开启调试模式，默认不开启
    'ROUTE' => ROUTE_DEFAULT, // ROUTE_DEFULT 普通PATHINFO模式，ROUTE_REGEX 正则转发模式，多种则用“|”分隔
    'ROUTE_CUT' => '/', // 路径切割符
    'FORCE_STATIC' => false, // 是否强制伪静态，是的话对于/index.php/test/这样的路径输出404
    'DEFAULT_CONTROLLER' => 'index', // 默认控制器
    'DEFAULT_INDEX_FILE' => 'index.php', // Default index file, default: index.php
    'CLASS_EXT' => '.class.php', // 类文件后缀名
    'VIEW_EXT' => '.view.php', // 模版文件后缀名
    'FUNCTION_EXT' => '.func.php',
    'CORE_LANGUAGE' => 'en_US', // 内核信息语言
    'LANGUAGE' => 'en_US', // 应用语言
    'CHARSET' => 'UTF-8', // 字符集

    'SKIN_NAME' => 'default', // 模版名称
    'PATH' => '/', // 默认路径
    'STATIC_PATH' => 'demo', // 静态文件相对根路径的地址
    'IGNORE_CDN' => true, // 是否忽略CDN
    'CDN' => null, // CDN地址，下面为CDN配置demo
    // 'CDN' => 'http://cdn', // 本例为单个cdn地址，如果多个地址则用下面的方式
    // 'CDN' => [
    //     'http://cdn1',
    //     'http://cdn2',
    //     'http://cdn3',
    //     'http://cdn4',
    //     ],
    'DB' => null, // 默认数据库配置，默认为空，下面为DB配置demo(SLAVE配置可以忽略)
    // 'DB' => [
    //     'TYPE' => 'pgsql',
    //     'PREFIX' => 'blog.',
    //     'MASTER' => [
    //         'HOST' => '127.0.0.1',
    //         'PORT' => '5432',
    //         'DBNAME' => 'demo',
    //         'USER' => 'demo',
    //         'PASSWORD' => 'demo'
    //     ],
    //     'SLAVE' => [
    //          [
    //              'HOST' => '127.0.0.2',
    //              'PORT' => '5432',
    //              'DBNAME' => 'demo',
    //              'USER' => 'demo',
    //              'PASSWORD' => 'demo'
    //         ],
    //         [
    //              'HOST' => '127.0.0.3',
    //              'PORT' => '5432',
    //              'DBNAME' => 'demo',
    //              'USER' => 'demo',
    //              'PASSWORD' => 'demo'
    //         ],
    //     ]
    // ],

    'SESSION' => [
        'PREFIX' => '' // SESSION前缀
    ],
    'COOKIE' => [
        'PREFIX' => '' // COOKIE前缀
    ]
];
