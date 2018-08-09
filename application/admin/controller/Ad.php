<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/27
 * Time: 16:03
 */

namespace app\admin\controller;


use app\model\Actors;
use think\Db;

class Ad extends Init {
    public function index() {

        //        {
        //            1: {
        //            'pic_url': "https://img.alicdn.com/imgextra/i4/4028991139/TB2Z8G5Ir1YBuNjSszhXXcUsFXa_!!2-martrix_bbs.png",
        //        "redirect_url": "http://www.autulin.cn/jumpxl5.html?hcode=qd125"
        //    },
        //    2:{
        //            'pic_url':'https://img.alicdn.com/imgextra/i2/4028991139/TB285mQIAKWBuNjy1zjXXcOypXa_!!1-martrix_bbs.gif',
        //        'redirect_url':'http://8y7811.com'
        //    }
        //}
        $where = null;
        $name  = input('name');
        $name == true && $where[] = ['name', '=', "%$name%"];
        $list = Db::name('ads')->where($where)->paginate(20, false, ['query' => input()]);
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
        $info = Db::name('ads')->find(input('id'));

        $this->assign('info', $info);
        if (isAjax()) {
            $this->save();
        }
        return $this->fetch('info');
    }

    function save() {
        $data = input('post.');

        if ($data['id']) {
            $res = Db::name('ads')->update($data);
        } else {
            $data['insert_time'] = time();
            $res                 = Db::name('ads')->insert($data);
        }

        if($res){
            $this->adJs();
            $this->success('操作成功~');
        }else{
            $this->error('操作失败~');
        }

    }

    function adJs() {
        $ads       = Db::name('ads')->order('sort')->select();
        $tmp_name  = ROOT_PATH . "admin/js/ad_tmp.js";
        $file_name1 = ROOT_PATH . "../public/home/js/ad.js";
        $file_name2 = ROOT_PATH . "../../html/home/js/ad.js";

        if (!file_exists($tmp_name)) $this->error('文件不存在！');
        $js_content = file_get_contents($tmp_name);

        if ($ads) {
            $data = null;
            foreach ($ads as $k => $v) {
                $data[] = ['pic_url' => $v['img_url'], 'redirect_url' => $v['redirect_url']];
            }
            $js_content = str_replace('[{}]', json_encode($data), $js_content);
            $res = file_put_contents($file_name1, $js_content);
            $res = file_put_contents($file_name2, $js_content);
        }
    }

    public function del() {
        $table = input('table/s');
        $ids   = input('post.ids/a');
        $id    = input('id/d');
        $table == '' && $this->error('缺少数据表');
        ($id == '' && !is_array($ids)) && $this->error('缺少主键值');

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
            $file_name1 = ROOT_PATH . "../public/home/js/ad.js";
            $file_name2 = ROOT_PATH . "../../html/home/js/ad.js";
            unlink($file_name1);
            unlink($file_name2);
            addSysLog($data);
            $this->success('操作成功~');
        } else
            $this->error('操作失败');
    }
}