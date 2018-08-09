<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/3/7
 * Time: 22:44
 */

namespace app\admin\controller;

use app\model\Tags;
use app\model\Users;
use think\Controller;
use app\model\Menu as MenuModel;
use app\model\Role as MenuRole;
use think\Db;
use app\model\Video;

class Init extends Controller {
    protected $path = '/home/html/';
    /**
     * 初始化
     * @author 胡卫兵 <659998662@qq.com>
     */
    public function initialize() {
        $admin = session('admin');

        if (!$admin) {
            $this->error('您还未登录，请登录后再来', url('/publics/login'), '', 1);
        }
        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $url        = '/' . $controller . '/' . $action;

        if ($module . $controller == 'adminindex') {
            $_location = [];
        } else {
            $_location = MenuModel::getLocation('', true) ?: [];
        }

        if (empty($_location) && !in_array($controller, [
                'upload',
                'collect',
                'index'
            ])) $this->error('获取不到当前节点地址，可能未添加节点');

        //判断用户操作是否超时，超时则踢出
        /*if (!($module . $controller . $action == 'adminpublicslogin') && !($module . $controller . $action == 'adminpublicslogout') && $admin) {
            //当用户有操作时，判断是否超时
            $user = Users::field('action_time, action_ip, is_login,id')->find(session('admin.id'));
            if ($user['is_login']) {
                if ($user['action_time'] + 6000 >= time()) {
                    Users::where(['id' => $user['id']])->update([
                        'action_time' => time(),
                        'action_ip'   => get_client_ip()
                    ]);
                } else {
                    Users::where(['id' => $user['id']])->update([
                        'action_time' => 0,
                        'action_ip'   => '',
                        'is_login'    => 0
                    ]);
                    session('admin', null);
                }
            } else {
                session('admin', null);
            }
        }*/

        //面包屑
        $breadcrumb = '<li><i class="fa fa-map-marker"></i></li>';
        foreach ($_location as $k => $v) {
            $_url       = $v['url'] ? url($v['url']) : 'javascript:void(0);';
            $breadcrumb .= '<li><a class="link-effect" href="' . $_url . '">' . $v['name'] . '</a></li>';
        }

        $node_ids = MenuRole::where(['id' => $admin['role_id']])->value('nodes');

        $where[] = ['status', '=', 1];
        if ($admin['role_id'] != 1) {
            $where[] = ['id', 'in', $node_ids];
        }
        /*Db::name::listen(function($sql, $time, $explain){
            // 记录SQL
            echo $sql. ' ['.$time.'s]';
            // 查看性能分析结果
            dump($explain);
        });*/

        $urls  = [];
        $menus = MenuModel::where($where)->order('sort asc')->select();
        foreach ($menus as $k => $v) {
            $urls[$k] = strtolower($v['url']);
        }

        $this->assign('Menu', $menus);
        $this->assign('module', $module);
        $this->assign('controller', $controller);
        $this->assign('action', $action);
        $this->assign('_location', $_location);
        $this->assign('url', $url);

        $this->assign('admin_base_icon', '/application/admin/view/icons.html');
        $this->assign('breadcrumb', $breadcrumb);

        if (!in_array($url, $urls) && $admin['role_id'] != 1) {
            $this->error('您无权操作此内容');
        }
    }

    /**
     * 快速编辑
     *
     * @param array $record 行为日志内容
     *
     * @author 胡卫兵 <659998662@qq.com>
     */
    public function quickEdit() {
        $field           = input('post.name', '');
        $value           = input('post.value', '');
        $type            = input('post.type', '');
        $id              = input('post.id') ?: input('post.pk');
        $table           = input('post.table');
        $validate        = input('post.validate', '');
        $validate_fields = input('post.validate_fields', '');
        $table == '' && $this->error('缺少数据表');
        $field == '' && $this->error('缺少字段名');
        $id == '' && $this->error('缺少主键值');
        $Model         = Db::name($table);
        $pk            = $Model->getPk();
        $protect_table = [
            '__USERS__',
            '__ROLE__',
            config('database.prefix') . 'users',
            config('database.prefix') . 'role',
        ];
        // 验证是否操作管理员
        if (in_array($Model->getTable(), $protect_table) && $id == 1) {
            $this->error('禁止操作超级管理员');
        }
        // 验证器
        if ($validate != '') {
            $validate_fields = array_flip(explode(',', $validate_fields));
            if (isset($validate_fields[$field])) {
                $result = $this->validate([$field => $value], $validate . '.' . $field);
                if (true !== $result) $this->error($result);
            }
        }
        switch ($type) {
            // 日期时间需要转为时间戳
            case 'combodate':
                $value = strtotime($value);
            break;
            // 开关
            case 'switch':
                $value = $value == 'true' ? 1 : 0;
            break;
            // 密码
            case 'password':
                $code = $Model->where($pk, $id)->value('code');
                $Model->where($pk, $id)->setField('pwd_view', $value);
                $value = md5($code . $value);
            break;
        }
        $protect_table1 = [
            '__VIDEO__',
            '__COMMENT__',
            config('database.prefix') . 'video',
            config('database.prefix') . 'comment',
        ];
        // 验证是否审核操作
        if (in_array($Model->getTable(), $protect_table1)) {
            $value = $value ? $value : -1;
        }
        $result = $Model->where($pk, $id)->setField($field, $value);

        if (false !== $result) {
            // 记录行为日志
            if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 获取筛选条件
     * @author 胡卫兵 <659998662@qq.com>
     *
     * @return array
     */
    final protected function getMap() {
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');
        $select_field     = input('param._select_field/s', '');
        $select_value     = input('param._select_value/s', '');
        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[] = [$search_field, 'like', "%$keyword%"];
        }
        // 下拉筛选
        if ($select_field != '') {
            $select_field = array_filter(explode('|', $select_field), 'strlen');
            $select_value = array_filter(explode('|', $select_value), 'strlen');
            foreach ($select_field as $key => $item) {
                if ($select_value[$key] != '_all') {
                    $map[] = [$item, '=', $select_value[$key]];
                }
            }
        }
        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[] = [$filter_time, 'between time', [$filter_time_from . ' 00:00:00', $filter_time_to . ' 23:59:59']];
        }
        // 表头筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                if (isset($filter_content[$key])) {
                    $map[] = [$item, 'in', $filter_content[$key]];
                }
            }
        }

        return $map;
    }

    /**
     * 获取字段排序
     *
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     *
     * @author 胡卫兵 <659998662@qq.com>
     * @return string
     */
    final protected function getOrder($extra_order = '', $before = false) {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order . ' ' . $by;
        }
        if ($before) {
            return $extra_order . ',' . $order . ' ' . $by;
        } else {
            return $order . ' ' . $by . ',' . $extra_order;
        }
    }

    public function disable() {
        $table = input('table/s');
        $ids   = input('ids/a');
        $id    = input('id/d');
        $table == '' && $this->error('缺少数据表');
        ($id == '' && !is_array($ids)) && $this->error('缺少主键值');
        $Model = Db::name($table);
        if ($table == 'users' || $table == 'role') {
            if ((is_array($ids) && in_array(1, $ids)) || $id == 1) {
                $this->error('禁止操作默认账号或角色');
            }
        }
        // 验证是否审核操作
        $protect_table1 = [
            '__VIDEO__',
            '__COMMENT__',
            config('database.prefix') . 'video',
            config('database.prefix') . 'comment',
        ];
        $value          = 0;
        if (in_array($Model->getTable(), $protect_table1)) {
            $value = -1;
        }
        if (is_array($ids)) {
            $code = Db::name($table)->where('id', 'in', $ids)->setField('status', $value);
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '更新ID为[' . implode(',', $ids) . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        } else {
            $code = Db::name($table)->where('id', $id)->setField('status', $value);
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '更新ID为[' . $id . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        }
        if ($code) {
            addSysLog($data);
            $this->success('操作成功~');
        } else
            $this->error('操作失败');
    }

    public function del() {
        $table = input('table/s');
        $ids   = input('post.ids/a');
        $id    = input('id/d');
        $table == '' && $this->error('缺少数据表');
        ($id == '' && !is_array($ids)) && $this->error('缺少主键值');
        if ($table == 'users' || $table == 'role') {
            if ((is_array($ids) && in_array(1, $ids)) || $id == 1) {
                $this->error('禁止操作默认账号或角色');
            }
        }
        if (is_array($ids)) {
            $code = Db::name($table)->where('id', 'in', $ids)->delete();
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '删除ID为[' . implode(',', $ids) . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        } else if ($id) {
            $code = Db::name($table)->where('id', $id)->delete();
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '删除ID为[' . $id . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        }

        if ($code) {
            addSysLog($data);
            $this->success('操作成功~');
        } else
            $this->error('操作失败');
    }

    public function enable() {
        $table = input('table/s');
        $ids   = input('post.ids/a');
        $id    = input('id/d');
        $table == '' && $this->error('缺少数据表');
        ($id == '' && !is_array($ids)) && $this->error('缺少主键值');
        if ($table == 'users' || $table == 'role') {
            if ((is_array($ids) && in_array(1, $ids)) || $id == 1) {
                $this->error('禁止操作默认账号或角色');
            }
        }
        if (is_array($ids)) {
            $code = Db::name($table)->where('id', 'in', $ids)->setField('status', 1);
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '更新ID为[' . implode(',', $ids) . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        } else {
            $code = Db::name($table)->where('id', $id)->setField('status', 1);
            $data = [
                'username'    => session('admin.username'),
                'table'       => $table,
                'remark'      => '更新ID为[' . $id . ']的数据',
                'action_ip'   => get_client_ip(),
                'action_time' => time(),
            ];
        }
        if ($code) {
            addSysLog($data);
            $this->success('操作成功~');
        } else
            $this->error('操作失败');
    }


    //添加标签
    function addTag($data) {
        foreach ($data as $v) {
            if (!Tags::where('tag', $v)->count() && $v) {
                $row['tag']         = $v;
                $row['insert_time'] = time();
                Tags::insert($row);
            }
        }
    }


    /**
     * 获取频道下文章总页数
     * @param $channel
     * @return float
     */
    public function totalPage($where) {
        $count     = Video::where($where)->count('id');
        $totalPage = ceil($count / 18);
        return $totalPage;
    }

    /**
     * 实例化redis
     * @return \Redis
     */
    public function redis() {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        return $redis;
    }
}