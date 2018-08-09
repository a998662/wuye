<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/2/28
 * Time: 23:37
 */

namespace app\admin\controller;

use app\model\Users as UserModel;

class User extends Init {
	public function index() {
		// 查询
		$map   = $this->getMap();
		$order = $this->getOrder('id desc');
		
		$member = UserModel::field('id,username,nickname,insert_time,update_time,status,email,mobile,is_admin')
			->where($map)->order($order)->paginate();
		
		$this->assign('admin', $member);
		
		return $this->fetch();
	}
	
	public function add() {
		if (isAjax()) {
			return $this->save();
		}
		
		$role = \app\model\Role::where(['status' => 1])->select();
		$this->assign('role', $role);
		
		return $this->fetch('info');
	}
	
	public function edit() {
		if (isAjax()) {
			return $this->save();
		}
		$info = UserModel::find(input('id'));
		$role = \app\model\Role::where(['status' => 1])->select();
		$this->assign('info', $info);
		$this->assign('role', $role);
		
		return $this->fetch('info');
	}
	
	private function save() {
		$data                = input('post.');
		$data['code']        = getRandCode(5);
		$data['pwd_view']    = $data['password'];
		$data['status']      = $data['status'] == 'on' ? 1 : 0;
		$data['password']    = md5($data['code'] . $data['password']);
		$data['update_time'] = time();
		
		if ($data['id']) {
			$data['insert_time'] = time();
			$res                 = UserModel::update($data);
		} else {
			unset($data['id']);
			$res = UserModel::insertGetId($data);
		}
		
		if ($res) {
			$this->success('操作成功', url('/user/index'));
		} else {
			$this->error('操作失败');
		}
	}
	
	public function setAdmin() {
		$data['id']       = input('id');
		$data['is_admin'] = input('is_admin');
		if ($data['id'] == 1) {
			$this->error('禁止操作默认账号或角色');
		}
		
		$res              = UserModel::update($data);
		if ($res) {
			if ($data['is_admin'] == 1) {
				$msg = '请到管理员页面选择您的角色';
				$url = url('/admin/edit', ['id' => $data['id']]);
			}
			$this->success('操作成功~ ' . $msg, $url);
		} else {
			$this->error('操作失败');
		}
	}
}