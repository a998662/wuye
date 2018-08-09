<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/7/19
 * Time: 15:20
 */

namespace app\admin\controller;


class Readindex extends Init {
    public function index(){
        return $this->fetch();
    }

    public function read(){
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
        $content = curl_get(config('app_host').'/index.php');

        file_write($this->path . 'index.html', $content);

        return ['code'=>1,'msg'=>'更新成功'];
    }
}