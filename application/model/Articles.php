<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2016/5/1 0001
 * Time: 下午 2:38
 */

namespace app\model;
use think\Model;

class Articles extends Model
{
    protected $pk = 'id';

    //获取父ID下的所有下级分类
    function getLists($id,$arr=[]){
        $where = ['parent_id'=>$id];
        $where = array_merge($where,$arr);
        $res = $this->field('id,name,parent_id')->where($where)->cache(60)->select()->toArray();
        if($res){
            foreach ($res as $v){
                $where = ['parent_id'=>$v['id']];
                $where = array_merge($where,$arr);
                $res1 = $this->field('id,name,parent_id')->cache(60)->where($where)->select()->toArray();
                $res = array_merge($res, $res1);
            }
        }
        return $res;
    }
}