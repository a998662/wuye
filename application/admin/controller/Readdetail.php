<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/7/19
 * Time: 15:20
 */

namespace app\admin\controller;

use app\model\Category;
use app\model\Video;
use think\Db;

class Readdetail extends Init {
    public function index(){
        $total = Video::count('id');

        $rootCate = Category::where('parent_id = 0')->order(array("sort" => "ASC"))->select();

        $alias = input('alias') ?: 'shipin';

        $this->assign('rootCate', $rootCate);
        $this->assign('total', $total);
        return $this->fetch();
    }


    /**
     * 生成内容页和播放页
     * 从[$start]条开始 共生成[$start]数据
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read() {
        set_time_limit(0);
        $start = input('start/d') ?: 0;
        $total = input('total/d') ?: 20000;
        $channel = input('channel/s');
        if (!file_exists($this->path . 'play/')) {
            mkdir($this->path . 'play/', 0777, true);
        }
        $channels = Category::field('alias')->where('parent_id', 0)->select();
        foreach ($channels as $v) {
            $path = $this->path . $v['alias'] . '/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        //$where[] = ['status','=',1];
        $channel && $where[] = ['channel','=',$channel];

        $redis = $this->redis();
        $list  = Video::field('id,channel')->where($where)->order('id desc')->limit($start, $total)->select();

        foreach ($list as $v) {
            $id      = $v['id'];
            $channel = $v['channel'];
            $path    = $this->path . $channel . '/';
            //如果是视频频道就生成播放页面
            if ($channel == 'shipin') {
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/play/$id",
                    'file' => $this->path . 'play/' . $id . ".html"
                ]));
            }
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/$id",
                'file' => $path . $id . ".html"
            ]));


        }
        set_time_limit(30);

        $this->redis()->set('total_page',$this->redis()->lLen('lists'));

        return ['code'=>1,'msg'=>'任务已在后台运行'];
    }
}