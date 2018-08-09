<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/2/28
 * Time: 23:37
 */

namespace app\admin\controller;

use app\model\Users as UserModel;


class Admin extends Init
{
    public function index(){
        // 查询
        $map           = $this->getMap();
        $map['is_admin'] = 1;

        // 排序
        $order = $this->getOrder('sort asc,id asc');

        $member = UserModel::alias('a')->field('a.*,b.name as role_name')->join('__ROLE__ b','a.role_id = b.id')
            ->where($map)->order($order)->paginate();
        
        $this->assign('admin', $member);
        return $this->fetch();
    }


    public function add(){
        if(isAjax()){
            return $this->save();
        }

        $role = \app\model\Role::where(['status'=>1])->select();
        $this->assign('role', $role);
        return $this->fetch('info');
    }

    public function edit(){
        if(isAjax()){
            return $this->save();
        }
        $info = UserModel::find(input('id'));
        $role = \app\model\Role::where(['status'=>1])->select();
        $this->assign('info', $info);
        $this->assign('role', $role);
        return $this->fetch('info');
    }

    private function save(){
        $data = input('post.');
        $data['code'] = getRandCode(5);
        $data['pwd_view'] = $data['password'];
        $data['status'] = $data['status'] == 'on' ? 1 :0;
        $data['password'] = md5($data['code'] . $data['password']);
        $data['update_time'] = time();
        $data['is_admin'] = 1;

        if($data['id']){
            $data['insert_time'] = time();
            $res = UserModel::update($data);
        }else{
            unset($data['id']);
            $res = UserModel::insertGetId($data);
        }

        if($res){
            $this->success('操作成功',url('/admin/index'));
        }else{
            $this->error('操作失败');
        }
    }
}