<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/12
 * Time: 23:56
 */

namespace app\home\controller;


use app\model\Likes;
use app\model\Video;

class Mp3 extends Index {
    public function detail() {
        $id = input('id/d');

        $info = Video::field('id,title,content,score,tags,channel as alias,insert_time,thumb,keywords,description')->cache($this->cache_time)->find($id)->toArray();
        //当前频道下前三个子类排行前十视频

        $cats = [
            ['name'=>'评分','type'=>'score'],
            ['name'=>'最新','type'=>'insert_time'],
            ['name'=>'点击','type'=>'play'],
        ];

        foreach ($cats as $k => $v) {
            if ($k < 3) {
                $list = Video::field('id,title,score,channel')->where([
                    ['status', '=', 1],
                    ['channel', '=', 'mp3'],
                ])->order($v['type'].' desc')->limit(10)->cache($this->cache_time)->select();
                if ($list) {
                    $catlist[$k]         = $v;
                    $catlist[$k]['data'] = $list;
                    unset($list);
                }
            }
        }
        $this->assign(['rightTop' => $catlist]);


        if($user_id = session('user.id')){
            $is_like = Likes::where([['user_id','=',$user_id],['vid','=',$id]])->cache($this->cache_time)->value('id');
            $info['active'] = $is_like ? 'active' : '';
        }

        $this->assign($info);
        return $this->fetch();
    }
}