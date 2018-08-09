<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/12
 * Time: 23:59
 */

namespace app\home\controller;


use app\model\Likes;
use app\model\Video;

class Shipin extends Index {
    public function detail(){
        $id = input('id/d');

        $info = Video::field('id,title,score,tags,channel as alias,insert_time,thumb,video_url,down_url,keywords,description')->cache($this->cache_time)->find($id)->toArray();
        if($user_id = session('user.id')){
            $is_like = Likes::where([['user_id','=',$user_id],['vid','=',$id]])->cache($this->cache_time)->value('id');
            $info['active'] = $is_like ? 'active' : '';
        }
        $this->assign($info);
        return $this->fetch();
    }

    public function play(){
        $id = input('id/d');

        $info = Video::field('id,title,score,tags,channel as alias,insert_time,thumb,video_url,down_url,keywords,description')->cache($this->cache_time)->find($id)->toArray();
        if($user_id = session('user.id')){
            $is_like = Likes::where([['user_id','=',$user_id],['vid','=',$id]])->cache($this->cache_time)->value('id');
            $info['active'] = $is_like ? 'active' : '';
        }
        $this->assign($info);
        return $this->fetch();
    }
}