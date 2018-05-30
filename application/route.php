<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::rule('yqy','admin/Login/login');//后台登录路由
Route::rule('index','home/Index/index');//前台首页路由
Route::rule('news','home/Articles/news');//最新资讯路由
Route::rule('jishu','home/Articles/jishu');//技术分享路由
Route::rule('share','home/Articles/share');//源码分享路由
Route::rule('chat','home/Chat/chat');//随心笔记路由
Route::rule('release','home/Release/index');//需求发布路由
Route::rule('news_detail/:aid','home/Articles/detail','get');//文章详情路由
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
