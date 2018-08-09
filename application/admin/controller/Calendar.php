<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/5/15
 * Time: 13:26
 */

namespace app\admin\controller;


use think\Db;

class Calendar extends Init {
    public function index() {
        return $this->fetch();
    }

    public function add() {
        $this->save();
    }

    public function edit(){
        $this->save();
    }

    public function save() {
        $data          = input('post.');
        $color         = [1 => '#c5c5c5', 2 => '#5c90d2', 3 => '#44b4a6', 4 => '#ff6b6b',];
        $data['color'] = $color[$data['status']];

        if ($data['id']) {
            $res = Db::name('calendars')->update($data);
        } else {
            $data['create_id'] = session('admin.id');
            $res = Db::name('calendars')->insertGetId($data);
            $data['id'] = $res;
        }

        if ($res) {
            $this->success('操作成功', '', $data);
        } else {
            $this->error('操作失败');
        }
    }

    public function getData() {
        $start = !input('start') ?: strtotime(input('start'));
        $end   = !input('end') ?: strtotime(input('end'));
        $start == true && $where[] = ['start', '> time', $start];
        $end == true && $where[] = ['end', '< time', $end];
        $where[] = ['create_id', '=', session('admin.id')];

        $list = Db::name('calendars')->where($where)->select();
        return $list;
    }
}