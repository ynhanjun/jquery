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
        'name' => '\w+',
        'id' => '\d+'
    ],
    '__rest__' => [
        ':version/categories' => 'api/:version.Category',
        ':version/items' => 'api/:version.Item',
        ':version/slides' => 'api/:version.Slide',
        ':version/news' => 'api/:version.News',
    ],
    'admin/auth' => 'Auth/token',
    ':version/goods' => 'api/:version.Item/best'
];
