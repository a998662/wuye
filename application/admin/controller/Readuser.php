<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/7/19
 * Time: 15:20
 */

namespace app\admin\controller;


use think\Db;

class Readuser extends Init {
    public function index(){
        if (!file_exists($this->path.'user')) {
            mkdir($this->path.'user', 0777, true);
        }
        if(!file_exists($this->path.'gonggao')){
            mkdir($this->path.'gonggao', 0777, true);
        }
        return $this->fetch();
    }

    public function read(){
        try{
            set_time_limit(0);
            $redis   = $this->redis();

            $content = curl_get(config('app_host') . "/index.php/user/info");
            file_write($this->path . "user/info.html",$content);
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/user/info",
                'file' => $this->path . "user/info.html"
            ]));
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/user/favorite",
                'file' => $this->path . "user/favorite.html"
            ]));
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/user/login",
                'file' => $this->path . "user/login.html"
            ]));
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/user/register",
                'file' => $this->path . "user/register.html"
            ]));
            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/search",
                'file' => $this->path . "search.html"
            ]));


            $redis->lPush('lists', json_encode([
                'url'  => config('app_host') . "/index.php/gonggao/list",
                'file' => $this->path . "gonggao/list.html"
            ]));
            $where[]   = ['status', '=', 1];
            $totalPage = $this->totalPage($where);
            for ($i = 1; $i <= $totalPage; $i++) {
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/gonggao/list-$i",
                    'file' => $this->path . "gonggao/list-$i.html"
                ]));
            }
            $list = Db::name('placards')->where('status',1)->order('id desc')->select();
            foreach ($list as $v){
                $id = $v['id'];
                $redis->lPush('lists', json_encode([
                    'url'  => config('app_host') . "/index.php/gonggao/$id",
                    'file' => $this->path . "gonggao/$id.html"
                ]));
            }


        }catch (\Exception $e){
            return ['code'=>0,'msg'=>'生成失败'];
        }

        set_time_limit(30);
        $this->redis()->set('total_page',$this->redis()->lLen('lists'));
        return ['code'=>1,'msg'=>'任务已在后台运行'];
    }

    /**
     * 获取频道下文章总页数
     * @param $channel
     * @return float
     */
    public function totalPage($where) {
        $count     = Db::name('placards')->where($where)->count('id');
        $totalPage = ceil($count / 18);
        return $totalPage;
    }
}