#!/usr/bin/env php
<?php
namespace think;

ini_set('display_errors', 'on');

if(strpos(strtolower(PHP_OS), 'win') === 0)
{
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}



// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('ROOT_PATH', __DIR__ . '/');

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';
require_once __DIR__ . '/workerman/autoload.php';
// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app',[APP_PATH])->bind('push/Run')->run()->send();

