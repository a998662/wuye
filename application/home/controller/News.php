<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/8/5
 * Time: 15:33
 */

namespace app\home\controller;

use app\model\Likes;
use app\model\Video;
class News extends Index {
    public function detail(){
        $id = input('id/d');
        try{
            $info = Video::field('id,title,content,score,tags,channel as alias,insert_time,thumb,keywords,description')->cache($this->cache_time)->find($id)->toArray();
            if($user_id = session('user.id')){
                $is_like = Likes::where([['user_id','=',$user_id],['vid','=',$id]])->cache($this->cache_time)->value('id');
                $info['active'] = $is_like ? 'active' : '';
            }
        }catch (\Exception $e){
            $this->error('内容错误');
        }

        $this->assign($info);
        return $this->fetch();
    }
}