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
use think\facade\Route;

Route::get('shipin/list-[:name]-[:order]-[:page]','shipin/lists');
Route::get('shipin/:id','shipin/detail');
Route::get('news/list-[:name]-[:order]-[:page]','news/lists');
Route::get('news/:id','news/detail');
Route::get('play/:id','shipin/play');
Route::get('xiazai/list-[:name]-[:order]-[:page]','xiazai/lists');
Route::get('xiazai/:id','xiazai/detail');
Route::get('tupian/list-[:name]-[:order]-[:page]','tupian/lists');
Route::get('tupian/:id','tupian/detail');
Route::get('meinv/list-[:name]-[:order]-[:page]','meinv/lists');
Route::get('meinv/:id','meinv/detail');
Route::get('xiaoshuo/list-[:name]-[:order]-[:page]','xiaoshuo/lists');
Route::get('xiaoshuo/:id','xiaoshuo/detail');
Route::get('mp3/list-[:page]','mp3/lists');
Route::get('mp3list/:id-[:order]-[:page]','mp3/mp3list');
Route::get('mp3/:id','mp3/detail');
Route::get('search','index/search');
Route::post('search','index/search');
Route::get('gonggao/list-[:page]','gonggao/index');
Route::get('gonggao/:id','gonggao/info');

// 完整域名绑定到admin模块
Route::domain('admin', 'admin');

