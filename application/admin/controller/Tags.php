<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11
 * Time: 22:08
 */

namespace app\admin\controller;

use think\Db;

class Tags extends Init {
    function index() {
        $list = Db::name('tags')->paginate();
        $this->assign('tags', $list);
        return $this->fetch();
    }

    function add() {
        if (isPost()) {
            $data = input('post.');
            $data['status'] = $data['status'] ? 1 : 0;
            $data['insert_time'] = time();
            $res            = Db::name('tags')->insertGetId($data);

            if ($res) {
                return ['code' => 1, 'msg' => '添加成功'];
            } else {
                return ['code' => 0, 'msg' => '添加失败'];
            }
        }
        return $this->fetch('info');
    }

    function edit() {
        if (isPost()) {
            $data           = input('post.');
            $data['status'] = $data['status'] ? 1 : 0;
            $data['insert_time'] = time();
            $res            = Db::name('tags')->update($data);
            if ($res) {
                return ['code' => 1, 'msg' => '编辑成功'];
            } else {
                return ['code' => 0, 'msg' => '编辑失败'];
            }
        }
        $id   = input('id');
        $info = Db::name('tags')->find($id);
        $this->assign('info', $info);
        return $this->fetch('info');
    }
}