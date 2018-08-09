<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 21:44
 */

namespace app\admin\controller;


use think\Db;


class Placard extends Init {


    //文章列表页
    public function index() {
        $list = Db::name('placards')->paginate();

        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加文章
    public function add() {
        if (isAjax()) {
            $data                = input('post.');
            $data['user_id']     = session('admin.id');
            $data['status']      = $data['status'] ? 1 : 0;
            $data['insert_time'] = time();

            $tags = explode(',', $data['keywords']);
            $this->addTag($tags);
            unset($data['id']);


            $code = Db::name('placards')->insertGetId($data);
            $msg  = $code ? '添加成功' : '添加失败';

            if ($code) $this->success($msg, prevUrl()); else
                $this->error($msg);
        }

        return $this->fetch('info');
    }

    //编辑文章
    public function edit() {
        if (isAjax()) {
            $data = input('post.');
            $data['status']      = $data['status'] ? 1 : 0;
            $code = Db::name('placards')->update($data);
            $msg  = $code ? '编辑成功' : '编辑失败';

            if ($code) $this->success($msg, prevUrl()); else
                $this->error($msg);
        }
        $info = Db::name('placards')->find(input('id'));
        $this->assign('info', $info);

        return $this->fetch('info');
    }
}