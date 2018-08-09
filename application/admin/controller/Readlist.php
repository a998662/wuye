<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/7/19
 * Time: 15:20
 */

namespace app\admin\controller;

use app\model\Category;

class Readlist extends Init {
    public function index(){
        $rootCate = Category::where('parent_id = 0')->order(array("sort" => "ASC"))->select();

        $alias = input('alias') ?: 'shipin';

        $this->assign('rootCate', $rootCate);
        $this->assign('alias', $alias);
        return $this->fetch();
    }

    public function read(){
        set_time_limit(0);
        $redis   = $this->redis();
        $channel = input('channel');
        $cat_id  = Category::where('alias', $channel)->value('id');
        $path    = $this->path . "$channel/";



        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $redis->lPush('lists', json_encode([
            'url'  => config('app_host') . "/index.php/$channel/list",
            'file' => $path . 'list.html'
        ]));

        //有声小说
        if ($channel == 'mp3') {
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/list",
                'file' => $path . "list.html"
            ]));

            //有声小说一级列表
            $where[] = ['parent_id', '=', $cat_id];
            $where[] = ['status', '=', 1];
            $count   = Category::where($where)->count('id');
            unset($where);
            $totalPage = ceil($count / 18);
            for ($i = 1; $i <= $totalPage; $i++) {
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-$i",
                    'file' => $path . "list-$i.html"
                ]));
            }

            //有声小说二级列表
            $path = str_replace('/mp3/', '/mp3list/', $path);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $cats = Category::field('cat')->where('parent_id', 5)->order('cat desc')->select();

            foreach ($cats as $v) {
                $id = $v['cat'];
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/mp3list/$id",
                    'file' => $path . "$id.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/mp3list/$id-insert_time",
                    'file' => $path . "$id-insert_time.html"
                ]));
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/mp3list/$id-play",
                    'file' => $path . "$id-play.html"
                ]));
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/mp3list/$id-score",
                    'file' => $path . "$id-score.html"
                ]));

                $where[]   = ['cat_id', '=', $id];
                $where[]   = ['status', '=', 1];
                $where[]   = ['channel', '=', 'mp3'];
                $totalPage = $this->totalPage($where);
                unset($where);
                for ($i = 1; $i <= $totalPage; $i++) {
                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/mp3list/$id-insert_time-$i",
                        'file' => $path . "$id-insert_time-$i.html"
                    ]));
                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/mp3list/$id-play-$i",
                        'file' => $path . "$id-play-$i.html"
                    ]));
                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/mp3list/$id-score-$i",
                        'file' => $path . "$id-score-$i.html"
                    ]));
                }

            }

            //其他分类列表
        } else {
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/list-all",
                'file' => $path . "list-all.html"
            ]));
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/list-all-insert_time",
                'file' => $path . "list-all-insert_time.html"
            ]));

            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/list-all-play",
                'file' => $path . "list-all-play.html"
            ]));

            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/$channel/list-all-score",
                'file' => $path . "list-all-score.html"
            ]));
            $where[]   = ['cat_id', '=', $cat_id];
            $where[]   = ['status', '=', 1];
            $where[]   = ['channel', '=', $channel];
            $totalPage = $this->totalPage($where);
            for ($i = 1; $i <= $totalPage; $i++) {
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-all-insert_time-$i",
                    'file' => $path . "list-all-insert_time-$i.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-all-play-$i",
                    'file' => $path . "list-all-play-$i.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-all-score-$i",
                    'file' => $path . "list-all-score-$i.html"
                ]));
            }
            $cats = Category::field('id,name')->where('parent_id', $cat_id)->select();
            unset($where);
            foreach ($cats as $v) {
                $name = $v['name'];

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-$name",
                    'file' => $path . "list-$name.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-$name-insert_time",
                    'file' => $path . "list-$name-insert_time.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-$name-play",
                    'file' => $path . "list-$name-play.html"
                ]));

                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/$channel/list-$name-score",
                    'file' => $path . "list-$name-score.html"
                ]));

                $where[] = ['cat_id', '=', $cat_id];
                $where[] = ['status', '=', 1];
                $where[] = ['tags', '=', $name == '成人动漫' ? '动漫' : $name];

                $totalPage = $this->totalPage($where);
                for ($i = 1; $i <= $totalPage; $i++) {
                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/$channel/list-$name-insert_time-$i",
                        'file' => $path . "list-$name-insert_time-$i.html"
                    ]));

                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/$channel/list-$name-play-$i",
                        'file' => $path . "list-$name-play-$i.html"
                    ]));

                    $redis->lPush('lists', json_encode([
                        'url'  => config('app_host') . "/index.php/$channel/list-$name-score-$i",
                        'file' => $path . "list-$name-score-$i.html"
                    ]));

                }
                unset($where);
            }
        }
        set_time_limit(30);
        $this->redis()->set('total_page',$this->redis()->lLen('lists'));

        return ['code'=>1,'msg'=>'任务已在后台运行'];
    }
}