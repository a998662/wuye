<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2016/5/1 0001
 * Time: 下午 2:38
 */

namespace app\model;
use think\Model;

class Comment extends Model
{
    protected $pk = 'id';

    //获取父ID下的所有下级
    static function getLists($id,$vid){
        $where[] = ['parent_id', '=', $id];
        $where[] = ['vid', '=', $vid];
        $where[] = ['status', '=', 1];
        $res = self::fieldRaw('*')->where($where)->order('id')->cache(60)->select()->toArray();
        if($res){
            foreach ($res as $v){
                $res = array_merge($res, self::getLists($v['id'],$vid));
            }
        }
        return $res;
    }
}