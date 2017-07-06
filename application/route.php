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
Route::rule('jytz','admin/Login/login');//后台登录路由
Route::rule('index','shop/Index/index');//前台首页路由
Route::rule('user','shop/Users/users');//用户中心路由
Route::rule('coupon/:bus_id','shop/Coupon/coupon_list');//商家产品页路由
Route::rule('coupon_list_type/:bus_type_id','shop/Coupon/coupon_list_type');//商品类型列表路由
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],


];
