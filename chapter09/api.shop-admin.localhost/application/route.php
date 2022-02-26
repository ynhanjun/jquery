<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'version' => 'v\d+',
        'name' => '\w+',
        'id' => '\d+'
    ],
    '__rest__' => [
        ':version/categories' => 'api/:version.Category',
        ':version/items' => 'api/:version.Item',
        ':version/news' => 'api/:version.News',
        ':version/slides' => 'api/:version.Slide'
    ],
    ':version/auth' => 'api/:version.Auth/token',
    ':version/files' => 'api/:version.File/route',
    ':version/items' => 'api/:version.Item/route',
    ':version/news' => 'api/:version.News/route',
    ':version/slides' => 'api/:version.Slide/route',
    ':version' => 'api/:version.Index/index'
];
