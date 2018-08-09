<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/7
 * Time: 14:14
 */

namespace app\admin\controller;


use think\Db;

class Syslog extends Init {
    public function index(){
        $list = Db::name('syslogs')->paginate();

        $this->assign('list',$list);
        return $this->fetch();
    }
}