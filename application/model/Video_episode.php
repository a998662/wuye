<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2016/5/1 0001
 * Time: 下午 2:38
 */

namespace app\model;
use think\Model;

class Video_episode extends Model
{
    protected $pk = 'id';
    public function getCutStatusAttr($value){
        $status = ['0'=>'准备切片','1'=>'切片完成','2'=>'切片失败','3'=>'文件失败'];
        return $status[$value];
    }
}