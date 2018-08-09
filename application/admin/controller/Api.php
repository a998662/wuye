<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/22
 * Time: 12:20
 */

namespace app\admin\controller;

use app\model\Video;
use app\model\Category;
use think\Db;

class Api {
    public function index() {
        $data  = input('post.');
        $video = Video::field('id,title,thumb,content')->where(['title' => $data['title']])->find();

        if ($data['cat_id'] == 18) {
            $data['title']       = $data['tags'] . ' ' . $data['title'];
            $data['keywords']    = $data['tags'] . ' ' . $data['keywords'];
            $data['description'] = $data['tags'] . ' ' . $data['description'];
        }

        if ($data['cat_id'] == 4) {
            $path = 'uploads/xiaoshuo/' . date('Ymd') . '/';
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fileName = 'xiaoshuo_' . time() . '.text';
            $locatoin = $path . $fileName;
            if ($video['content']) {
                $locatoin = $video['content'];
            }

            $text            = $data['content'];
            $data['content'] = $locatoin;
            file_put_contents($locatoin, $text);
        }

        if (!strrpos('--' . $video['thumb'], 'uploads/thumb/') && $data['thumb']) {
            $res    = preg_replace_callback('/[^\x20-\x7f]/', function ($match) {
                return urlencode($match[0]);
            }, $data['thumb'] . '?ie=UTF-8');
            $result = @file_get_contents($res);
            if ($result) {
                $ext      = str_replace('?ie=UTF-8', '', substr($res, strrpos($res, '.')));
                $path     = 'uploads/thumb/' . date('Ymd') . '/';
                $fileName = getRandCode(10) . $ext;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $localSrc = $path . $fileName;
                if (@file_put_contents($localSrc, $result)) {
                    $image = \think\Image::open($localSrc);
                    @$image->thumb(248, 355, 3)->save($localSrc);
                    $data['thumb'] = '/' . $localSrc;
                }
            }
        } else {
            $data['thumb'] = $video['thumb'];
        }

        try {
            if ($video) {
                $data['update_time'] = time();
                Db::name('video')->where('id', $video['id'])->update($data);
            } else {
                $data['insert_time'] = time();
                $data['update_time'] = time();
                $data['score']       = rand(5, 9) . '.' . rand(0, 9);
                Db::name('video')->insert($data);
            }
            echo '发布成功';
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function addCat(){
        $data = input('post.');
        try{
            $cat = Category::where('cat',$data['cat'])->find();
            if(!$cat){
                $res    = preg_replace_callback('/[^\x20-\x7f]/', function ($match) {
                    return urlencode($match[0]);
                }, $data['thumb'] . '?ie=UTF-8');
                $result = @file_get_contents($res);
                if ($result) {
                    $ext      = str_replace('?ie=UTF-8', '', substr($res, strrpos($res, '.')));
                    $path     = 'uploads/cat/thumb/' . date('Ymd') . '/';
                    $fileName = getRandCode(10) . $ext;
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    $localSrc = $path . $fileName;
                    if (@file_put_contents($localSrc, $result)) {
                        $image = \think\Image::open($localSrc);
                        @$image->thumb(248, 355, 3)->save($localSrc);
                        $data['thumb'] = '/' . $localSrc;
                    }
                }
                Category::insert($data);
            }
            echo '发布成功';
        }catch (\Exception $e){
            print_r($e->getMessage());
        }
    }
}