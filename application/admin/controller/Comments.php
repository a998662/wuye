<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/21
 * Time: 23:29
 */

namespace app\admin\controller;


use app\model\Comment;

class Comments extends Init {

    public function index(){
        $type = input('type/s');
        $status = input('status');
        $title = input('title');

        $type == true && $where[] = ['b.channel','=',$type];
        $title == true && $where[] = ['b.title','like',"%$title%"];
        is_numeric($status) == true && $where[] = ['a.status','=',$status];

        $category= \app\model\Category::where('parent_id=0')->select();
        $comments = Comment::alias('a')->field('a.*,b.title,b.channel')->join('video b','a.vid=b.id')->where($where)->order('id desc')->paginate('',false,['query'=>input()]);

        if(is_numeric($status) == false) $status = '0,1,-1';
        
        $this->assign('comments',$comments);
        $this->assign('type',$type);
        $this->assign('status',$status);
        $this->assign('category',$category);
        return $this->fetch();
    }

    public function info(){

        $info = Comment::alias('a')->field('a.*,b.title')->join('video b','a.vid=b.id')->find(input('id/d'));
        $this->assign('info',$info);
        return $this->fetch();
    }


    public function reply(){
        $nickname = input('nickname/s');
        $content = input('content/s');
        $vid = input('vid/d');
        $id = input('id/d');

        if(!$content){
            return ['code'=>0,'msg'=>'请输入回复内容'];
        }

        $data = [
            'uid'=>1,
            'parent_id'=>$id,
            'vid'=>$vid,
            'nickname'=>$nickname,
            'content'=>$content,
        ];

        print_r($data);
        return;

        $res = Comment::insertGetId($data);

        if($res){
            return ['code'=>1,'msg'=>'回复成功'];
        }else{
            return ['code'=>0,'msg'=>'回复失败'];
        }

    }
}