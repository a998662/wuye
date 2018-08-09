<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/30
 * Time: 17:51
 */

namespace app\admin\controller;

use app\model\Category;

class Sethtml extends Init {
    public function index(){
        $category = Category::field('alias')->where('parent_id',0)->select();

        foreach ($category as $v){
            $path = '/'.$v['alias'].'/list';
            $content = file_get_contents('http://wuye.com'.$path);
            print_r($content);
            $this->initStorage()->write(ROOT_PATH.'/html'.$path, $content);
        }
    }

    // 初始化模板编译存储器
    private function initStorage() {
        $type  = 'File';
        $class = false !== strpos($type, '\\') ? $type : '\\think\\template\\driver\\' . ucwords($type);
        return new $class();
    }
}