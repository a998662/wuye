<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 21:44
 */

namespace app\admin\controller;

use app\model\Articles;
use app\model\Tags;

class Article extends Init {


    //文章列表页
    public function index() {
        $search_field = input('search_field');
        $value        = input('keyword');
        $where[]      = ['insert_time', '>', 0];

        $search_field = explode('|', $search_field);
        if ($value) {
            if (count($search_field) > 1) {
                $map1 = ['title', 'LIKE', "%" . $value . "%"];
                $map2 = ['tags', 'like', "%$value%"];
                $article = Articles::field('*')->whereOr([$map1,$map2])->order('id desc')->paginate(10);
            } else {
                switch ($search_field[0]) {
                    case 'title':
                        $where[] = ['title', 'like', "%" . $value . "%"];
                    break;
                    case 'tags':
                        $where[] = ['tags', 'like', "%$value%"];
                    break;
                }
                $article = Articles::field('*')->where($where)->order('id desc')->paginate(10);
            }
        }else{
            $article = Articles::field('*')->where($where)->order('id desc')->paginate(10);
        }


        $this->assign('article', $article);
        return $this->fetch();
    }

    //添加文章
    public function add() {
        if (isPost()) {
            $data                = input('post.');
            $data['user_id']     = session('admin.id');
            $data['content']     = nl2br($data['content']);
            $data['insert_time'] = time();
            $data['update_time'] = time();
            $data['content']     = $data['editorValue'];
            $data['tags'] = implode(',',$data['tags']);

            unset($data['id']);
            unset($data['editorValue']);

            $code = Articles::insertGetId($data);
            $msg  = $code ? '添加成功' : '添加失败';

            if ($code) $this->success($msg, prevUrl()); else
                $this->error($msg);
        }

        $this->assign('all_tags', Tags::where('status',1)->select());
        return $this->fetch('info');
    }

    //编辑文章
    public function edit() {
        if (isPost()) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['content'] = $data['editorValue'];
            $data['tags'] = implode(',',$data['tags']);


            unset($data['editorValue']);
            $code = Articles::update($data);
            $msg  = $code ? '编辑成功' : '编辑失败';

            if ($code) $this->success($msg, prevUrl()); else
                $this->error($msg);
        }
        $info = Articles::where('id',input('id/d'))->find();

        $info = $info ? $info->toArray() : [];
        $info['tags'] = explode(',',$info['tags']);

        $this->assign($info);
        $this->assign('all_tags', Tags::where('status',1)->select());
        return $this->fetch('info');
    }

    public function addTag($data) {
        foreach ($data as $v) {
            if (!Tags::where('tag', $v)->count() && $v) {
                $row['tag']         = $v;
                $row['insert_time'] = time();
                Tags::insert($row);
            }
        }
    }
}