<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/13
 * Time: 17:29
 */

namespace app\home\controller;


use app\model\Likes;
use app\model\Video;

class Meinv extends Index {
    public function detail(){
        $id = input('id/d');

        $info = Video::field('id,title,content,score,tags,channel as alias,insert_time,thumb,keywords,description')->cache($this->cache_time)->find($id)->toArray();
        $info['content'] = explode('$$',$info['content']);

        if($user_id = session('user.id')){
            $is_like = Likes::where([['user_id','=',$user_id],['vid','=',$id]])->cache($this->cache_time)->value('id');
            $info['active'] = $is_like ? 'active' : '';
        }

        $this->assign($info);
        return $this->fetch('/tupian/detail');
    }
}