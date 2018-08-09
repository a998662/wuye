<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 21:44
 */

namespace app\admin\controller;

use app\model\Messages as MessageModel;
use app\model\Users;

class Messages extends Init
{
    function initialize()
    {
        parent::initialize();
        $message = MessageModel::where('type', 1)->select();
        $this->assign('message', $message);
    }

    //文章列表页
    public function index()
    {
        $search_field = input('search_field');
        $value = input('keyword');
        $where[] = ['time', '>', 0];

        $search_field = explode('|', $search_field);
        if ($value) {
            if (count($search_field) > 1) {
                $map1 = ['a.title', 'LIKE', "%" . $value . "%"];
                $map2 = ['b.username', 'LIKE', "%" . $value . "%"];
                $message = MessageModel::alias('a')->field('a.*,b.username')->leftJoin('users b', 'a.user_id=b.id')->whereOr([$map1, $map2])->order('a.id desc')->paginate(10);
            } else {
                switch ($search_field[0]) {
                    case 'title':
                        $where[] = ['a.title', 'like', "%" . $value . "%"];
                        break;
                    case 'username':
                        $where[] = ['b.username', 'LIKE', "%" . $value . "%"];
                        break;
                }
                $message = MessageModel::alias('a')->field('a.*,b.username')->leftJoin('users b', 'a.user_id=b.id')->where($where)->order('a.id desc')->paginate(10);
            }
        } else {
            $message = MessageModel::alias('a')->field('a.*,b.username')->leftJoin('users b', 'a.user_id=b.id')->where($where)->order('a.id desc')->paginate(10);
        }

        if ($message) {
            foreach ($message as $key => $value) {
                $condition = '';
                if ($value['send_condition']) {
                    $send_condition = json_decode($value['send_condition'], true);
                    foreach ($send_condition as $k => $v) {
                        switch ($k) {
                            case 'username':
                                $condition .= "用户名：$v</br>";
                                break;
                            case 's_date':
                                $condition .= "注册开始日期：" . date('Y-m-d H:i:s', $v) . "</br>";
                                break;
                            case 'e_date':
                                $condition .= "注册结束日期：" . date('Y-m-d H:i:s', $v) . "</br>";
                                break;
                            case 'vip_level':
                                $condition .= "VIP等级：" . ($v == 1 ? '非VIP' : 'VIP') . "</br>";
                                break;
                        }
                    }
                }

                $message[$key]['send_condition'] = $condition;
            }
        }

        $this->assign('message', $message);
        return $this->fetch();
    }

    //添加文章
    public function add()
    {
        if (isAjax()) {
            $form_data = input('post.');
            $form_data=$form_data['data'];
            if($form_data['username'] && ($form_data['vip_level'] || $form_data['s_date'] || $form_data['e_date'])) $this->error('发送用户条件只能二选一');
            $data = $this::getData($form_data);

//            $data['user_id'] = 0;
//            $data['title'] = nl2br($form_data['title']);
//            $data['content'] = nl2br($form_data['content']);
//            $data['from'] = 0;
//            $data['type'] = 1;
//            $data['status'] = 0;
//            $data['time'] = time();
//            $data['content'] = clearCss($form_data['editorValue']);
//
//            $where = [];
//            //用户名
//            if ($form_data['username']) {
//                $where['username'] = $form_data['username'];
//            }
//            //注册时间
//            if ($form_data['s_date']) {
//                $where['s_date'] = strtotime($form_data['s_date']);
//            }
//
//            if ($form_data['e_date']) {
//                $where['e_date'] = strtotime($form_data['e_date']);
//            }
//            //vip等级
//            if ($form_data['vip_level']) {
//                $where['vip_level'] = $form_data['vip_level'];
//            }
//
//            $data['send_condition'] = json_encode($where);
            unset($data['id']);
            unset($data['editorValue']);

            $code = MessageModel::insert($data);

            $msg = $code ? '添加成功' : '添加失败';

            if ($code) $this->success($msg, prevUrl()); else
                $this->error($msg);
        }
        $this->assign('prevUrl', prevUrl());
        return $this->fetch('info');
    }

    //编辑文章
    public function edit()
    {
        if (isAjax()) {
            $form_data = input('post.');
            $form_data=$form_data['data'];
            if($form_data['username'] && ($form_data['vip_level'] || $form_data['s_date'] || $form_data['e_date'])) $this->error('发送用户条件只能二选一');
            $data = $this::getData($form_data);

//            $data['user_id'] = 0;
//            $data['title'] = nl2br($form_data['title']);
//            $data['content'] = nl2br($form_data['content']);
//            $data['from'] = 0;
//            $data['type'] = 1;
//            $data['status'] = 0;
//            $data['time'] = time();
//            $data['content'] = clearCss($form_data['editorValue']);
//
//            $where = [];
//            //用户名
//            if ($form_data['username']) {
//                $where['username'] = $form_data['username'];
//            }
//            //注册时间
//            if ($form_data['s_date']) {
//                $where['s_date'] = strtotime($form_data['s_date']);
//            }
//
//            if ($form_data['e_date']) {
//                $where['e_date'] = strtotime($form_data['e_date']);
//            }
//            //vip等级
//            if ($form_data['vip_level']) {
//                $where['vip_level'] = $form_data['vip_level'];
//            }
//
//            $data['send_condition'] = json_encode($where);

            $data['id'] = $form_data['id'];

            unset($data['editorValue']);

            $code = db('messages')->update($data);
            $msg = $code ? '编辑成功' : '编辑失败';

            if ($code) $this->success($msg, input('prevUrl')); else
                $this->error($msg);
        }

        $info = db('messages')->find(input('id'));
        if ($info['send_condition']) {
            $send_condition = json_decode($info['send_condition'], true);
            foreach ($send_condition as $k => $v) {
                switch ($k) {
                    case 'username':
                        $info['username'] = $v;
                        break;
                    case 's_date':
                        $info['s_date'] = date('Y-m-d H:i:s', $v);
                        break;
                    case 'e_date':
                        $info['e_date'] = date('Y-m-d H:i:s', $v);
                        break;
                    case 'vip_level':
                        $info['vip_level'] = $v;
                        break;
                }
            }
        }

        $this->assign($info);
        $this->assign('prevUrl', prevUrl());

        return $this->fetch('info');
    }

    public function sendDirect()
    {
        if (isAjax()) {
            $form_data = input('post.');
            $form_data=$form_data['data'];
            if($form_data['username'] && ($form_data['vip_level'] || $form_data['s_date'] || $form_data['e_date'])) $this->error('发送用户条件只能二选一');

            $id =  $form_data['id'];
            $data = $this::getData($form_data);
            MessageModel::startTrans();

            if($id)
            {
                $data['id'] = $form_data['id'];
                unset($data['editorValue']);
                $code1 = MessageModel::update($data);

            }
            else
            {
                unset($data['id']);
                unset($data['editorValue']);
                $code1 = MessageModel::insert($data);
            }

            if($code1)
            {
                $result = $this::sendMessage($id);
                if ($result['code'])
                {
                    MessageModel::commit();
                    $this->success($result['msg'], input('prevUrl'));
                }
                else
                {
                    MessageModel::rollback();
                    $this->error($result['msg']);
                }

            }
            else
            {
                MessageModel::rollback();
                $this->error('发送失败');

            }



        }

        $this->assign('info', $form_data);
        return $this->fetch('info');

    }

    public function send()
    {

        $table = input('table/s');
        $id = input('id/i');
        MessageModel::startTrans();
        $result = $this::sendMessage($id);
        if ($result['code'])
        {
            MessageModel::commit();
            $this->success('操作成功~');
        }
        else
        {
            MessageModel::rollback();
            $this->error($result['msg']);
        }

//        $info = MessageModel::find($id)->toArray();
//        if ($info['status'] != 0)
//            $this->error('消息已发送，不能重复发送');
//        $now = time();
//        $info['send_time'] = $now;
//        $info['status'] = 1;
//        $send_condition = json_decode($info['send_condition'], true);
//        $where = [];
//        if ($send_condition) {
//            foreach ($send_condition as $k => $v) {
//                switch ($k) {
//                    case 'username':
//                        $where[] = ['username', 'in', $v];
//                        break;
//                    case 's_date':
//                        $where[] = ['insert_time', '>=', $v];
//                        break;
//                    case 'e_date':
//                        $where[] = ['insert_time', '<', $v];
//                        break;
//                    case 'vip_level':
//                        //$where[] = ['vip_level', 'in', $v];
//                        break;
//                }
//            }
//        }
//
//        $where['is_admin'] = 0;
//
//        $data['title'] = $info['title'];
//        $data['content'] = $info['content'];
//        $data['from'] = 0;
//        $data['type'] = 1;
//        $data['status'] = 1;
//        $data['time'] = $info['time'];
//        $data['content'] = $info['content'];
//        $user = Users::where($where)->select()->toArray();
//        if ($user) {
//            foreach ($user as $value) {
//                $data['user_id'] = $value['id'];
//                $data['send_time'] = $now;
//                $insert_data[] = $data;
//            }
//        } else {
//            $this->error('没有用户被选中');
//        }
//
//        MessageModel::startTrans();
//        $code = MessageModel::insertAll($insert_data, true);
//        if ($code) {
//            $code = MessageModel::update($info);
//            if ($code) {
//                MessageModel::commit();
//                $this->success('操作成功~');
//            } else {
//                MessageModel::rollback();
//                $this->error('操作失败');
//            }
//        } else {
//            MessageModel::rollback();
//            $this->error('操作失败');
//        }


    }

    public function del()
    {
        $table = input('table/s');
        $id = input('id/i');
        $info = db($table)->find($id);
        if ($info['status'] != 0)
            $this->error('消息已发送，不能删除');

        $code = db($table)->delete($id);
        if ($code)
            $this->success('操作成功~');
        else
            $this->error('操作失败');

    }

    private function getData($form_data)
    {
        $data['user_id'] = 0;
        $data['title'] = nl2br($form_data['title']);
        $data['content'] = nl2br($form_data['content']);
        $data['from'] = 0;
        $data['type'] = 1;
        $data['status'] = 0;
        $data['time'] = time();
        $data['content'] = clearCss($form_data['editorValue']);

        $where = [];
        //用户名
        if ($form_data['username']) {
            $where['username'] = $form_data['username'];
        }
        //注册时间
        if ($form_data['s_date']) {
            $where['s_date'] = strtotime($form_data['s_date']);
        }

        if ($form_data['e_date']) {
            $where['e_date'] = strtotime($form_data['e_date']);
        }
        //vip等级
        if ($form_data['vip_level']) {
            $where['vip_level'] = $form_data['vip_level'];
        }

        $data['send_condition'] = json_encode($where);
        return $data;
    }

    private function sendMessage($id)
    {

        if($id)
        {
            $info = MessageModel::find($id)->toArray();
            if ($info['status'] != 0) {
                $result = array('code' => 0, 'msg' => '消息已发送，不能重复发送');
                return $result;
            }
        }

        $now = time();
        $info['send_time'] = $now;
        $info['status'] = 1;
        $send_condition = json_decode($info['send_condition'], true);
        $where = [];
        if ($send_condition) {
            foreach ($send_condition as $k => $v) {
                switch ($k) {
                    case 'username':
                        $where[] = ['username', 'in', $v];
                        break;
                    case 's_date':
                        $where[] = ['insert_time', '>=', $v];
                        break;
                    case 'e_date':
                        $where[] = ['insert_time', '<', $v];
                        break;
                    case 'vip_level':
                        //$where[] = ['vip_level', 'in', $v];
                        break;
                }
            }
        }

        $where[] = ['is_admin','=', 0];

        $data['title'] = $info['title'];
        $data['content'] = $info['content'];
        $data['from'] = 0;
        $data['type'] = 1;
        $data['status'] = 1;
        $data['time'] = $info['time'];
        $data['content'] = $info['content'];
        $user = Users::where($where)->select()->toArray();
        if ($user) {
            foreach ($user as $value) {
                $data['user_id'] = $value['id'];
                $data['send_time'] = $now;
                $insert_data[] = $data;
            }
        } else {
            $result = array('code' => 0, 'msg' => '没有用户被选中');
            return $result;
        }

        //MessageModel::startTrans();
        $code = MessageModel::insertAll($insert_data, true);
        if ($code) {
            $code = MessageModel::update($info);
            if ($code) {
                //MessageModel::commit();
                $result = array('code' => 1, 'msg' => '操作成功~');
                return $result;
            } else {
                //MessageModel::rollback();
                $result = array('code' => 0, 'msg' => '操作失败~');
                return $result;
            }
        } else {
            //MessageModel::rollback();
            $result = array('code' => 0, 'msg' => '操作失败~');
            return $result;
        }
    }
}