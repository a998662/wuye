<?php
/**
 * Created by PhpStorm.
 * User: KQ
 * Date: 2017/3/8
 * Time: 12:24
 */

namespace app\admin\controller;

use app\model\Users;
use think\Controller;
use app\model\Config;
use think\Db;

class Publics extends Controller {

    function login() {
        if (isAjax()) {
            $data = input('post.');
            /*if(!captcha_check($data['verify'])){
                return ['elem'=>'#verify1','msg'=>'验证码不正确','code'=>0];
            }*/
            $user = Users::where(['username' => $data['username']])->find();
            if (empty($user)) {
                $this->error('用户名不正确');
            } else {
                if (!$user['is_admin']) $this->error('非常抱歉~ 您不是管理员，无法登陆后台~');
                if (!$user['status']) $this->error('非常抱歉~ 此账号已被禁用，暂时无法登陆~');

                //if ($user['is_login'] == 1 && session('admin') && $user['action_time'] + 600 >= time()) $this->error('非常抱歉~ 此账号已在别处登录，请确认后重试~');

                if ($user['password'] == md5($user['code'] . $data['password'])) {
                    Users::where(['id' => $user['id']])->setField([
                        'login_time'  => time(),
                        'login_ip'    => get_client_ip(),
                        'action_time' => time(),
                        'action_ip'   => get_client_ip(),
                        'is_login'    => 1
                    ]);
                    session('admin', $user);
                    $this->success('登录成功', '/');
                } else {
                    $this->error('登录密码不正确');
                }
            }
            $this->error('服务器发生错误~');
        }
        return $this->fetch();
    }

    function logout() {
        Users::where('id', session('admin.id'))->update(['action_time' => 0, 'action_ip' => '', 'is_login' => 0]);
        session('admin', null);
        return $this->redirect(url('/publics/login'));
    }

    /**
     * 设置配色方案
     * @param string $theme 配色名称
     *
     */
    public function setTheme($theme = '') {
        $map['name']  = 'system_color';
        $map['group'] = 'system';
        Config::where($map)->setField('value', $theme);
        if (config('app_debug') == 0) cache('system_config', null);

        $this->success('设置成功');
    }

    public function getCats(){
        $channel = input('parent_id/dd');
        $cats = \app\model\Category::where([['status', '=', 1], ['parent_id', '=', $channel]])->select();

        return $cats;

    }

    /**
     * 获取筛选数据
     * @param string $table 表名
     * @param string $field 字段名
     * @param array $map 查询条件
     * @param string $options 选项，用于显示转换
     * @param string $list 选项缓存列表名称
     * @author 胡卫兵 <6599986622@qq.com>
     * @return \think\response\Json
     */
    public function getFilterList($table = '', $field = '', $map = [], $options = '', $list = '') {
        if ($list != '') {
            $result = ['code' => 1, 'msg' => '请求成功', 'list' => Cache::get('_filter_list_' . $field)];
            return json($result);
        }
        if ($table == '') {
            return json(['code' => 0, 'msg' => '缺少表名']);
        }
        if ($field == '') {
            return json(['code' => 0, 'msg' => '缺少字段']);
        }
        if (!empty($map) && is_array($map)) {
            foreach ($map as &$item) {
                if (is_array($item)) {
                    foreach ($item as &$value) {
                        $value = trim($value);
                    }
                } else {
                    $item = trim($item);
                }
            }
        }

        $data_list = Db::name($table)->where($map)->group($field)->column($field);
        if ($data_list === false) {
            return json(['code' => 0, 'msg' => '查询失败']);
        }

        if ($data_list) {
            if ($options != '') {
                // 从缓存获取选项数据
                $options = cache($options);
                if ($options) {
                    $temp_data_list = [];
                    foreach ($data_list as $item) {
                        $temp_data_list[$item] = isset($options[$item]) ? $options[$item] : '';
                    }
                    $data_list = $temp_data_list;
                } else {
                    $data_list = parse_array($data_list);
                }
            } else {
                $data_list = parse_array($data_list);
            }

            $result = ['code' => 1, 'msg' => '请求成功', 'list' => $data_list];
            return json($result);
        } else {
            return json(['code' => 0, 'msg' => '查询不到数据']);
        }
    }

    public function getArea() {
        $cat_id = input('cat_id');
        $area   = \app\model\Area::field('id,name,cat_id')->where([
            ['status', '=', 1],
            ['cat_id', '=', $cat_id]
        ])->select()->toArray();
        if ($area) {
            $this->success('', '', $area);
        } else {
            $this->error();
        }
    }

    public function getActor() {
        $cat_id = input('cat_id');
        $actors = \app\model\Actors::field('id,name,cat_id')->where([
            ['status', '=', 1],
            ['cat_id', '=', $cat_id]
        ])->select()->toArray();
        if ($actors) {
            $this->success('', '', $actors);
        } else {
            $this->error();
        }
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

    public function getTotalList() {
        $redis = $this->redis();

        return [
            'totalPage' => $redis->get('total_page'),
            'carryPage' => $redis->get('total_page') - $redis->lLen('lists')
        ];
    }

    public function getTotal(){
        $channel = input('channel/s');
        $total = \app\model\Video::where([['status','=',1],['channel','=',$channel]])->count('id');

        return $total;
    }
}