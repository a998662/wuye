<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/23
 * Time: 11:13
 */

namespace app\admin\controller;


use think\Db;

class Banner extends Init {
    public function index(){
        $list = Db::name('banner')->paginate();

        $this->assign('list',$list);
        return $this->fetch();
    }

    //添加文章栏目
    function add(){
        $info['parent_id'] = input('parent_id');
        $this->assign('info', $info);
        if(isAjax()){
            $this->save();
        }
        return $this->fetch('info');
    }

    //编辑文章栏目
    function edit(){
        $info = Db::name('banner')->find(input('parent_id'));

        $this->assign('info', $info);
        if(isAjax()){
            if(input('banner') != $info['banner']){
                unlink('.'.$info['banner']);
            }
            $this->save();
        }
        return $this->fetch('info');
    }

    function save(){
        $data = input('post.');
        $data['status'] = $data['status'] ? 1 : 0;

        if($data['id']){
            $res = Db::name('banner')->update($data);
        } else{
            $data['insert_time'] = time();
            $res = Db::name('banner')->insert($data);
        }

        $res ? $this->success('操作成功~') : $this->error('操作失败~');
    }
}