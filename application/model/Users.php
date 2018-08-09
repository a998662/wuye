<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2016/5/1 0001
 * Time: 下午 2:38
 */

namespace app\model;
use think\Model;

class Users extends Model
{
    protected $pk = 'id';
    static function getUser(){
        $uid = session('user.id');
        if(empty($uid)) return false;

        $user = self::field('nickname,username,id,online as status,sign,avatar,password,login_ip')->find($uid);
        return $user;
    }

    static function isOnline($uid){
        $online = self::where('id',$uid)->value('status');
        if($online == 'online')
            return true;
        else
            return false;
    }
}