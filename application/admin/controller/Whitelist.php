<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/27
 * Time: 16:03
 */

namespace app\admin\controller;


use app\model\Whitelist as WhitelistModel;

class Whitelist extends Init {

    public function index() {
        $name   = input('name');
        $name == true && $where[] = ['name', '=', "%$name%"];

        $list = WhitelistModel::where($where)->order('insert_time desc')->paginate();

        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加文章栏目
    function add() {
        $info['id'] = input('id');
        $this->assign('info', $info);
        if (isAjax()) {
            $this->save();
        }
        return $this->fetch('info');
    }

    //编辑文章栏目
    function edit() {
        $info = WhitelistModel::find(input('id'));

        $this->assign('info', $info);
        if (isAjax()) {
            $this->save();
        }
        return $this->fetch('info');
    }

    function save() {
        $data           = input('post.');
        $data['status'] = $data['status'] ? 1 : 0;

        if ($data['id']) {
            $res = WhitelistModel::update($data);
        } else {
            $data['insert_time'] = time();
            $res                 = WhitelistModel::insert($data);
        }

        $res ? $this->success('操作成功~') : $this->error('操作失败~');
    }
}