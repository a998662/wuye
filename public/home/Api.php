<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/22
 * Time: 12:20
 */

namespace app\controller;

use think\Db;

class Api {
    public function index() {
        $data  = input('post.');
        $video = Db::name('video')->field('id,title,thumb,content')->where(['title' => $data['title']])->find();

        if ($data['cat_id'] == 18) {
            $data['title']       = $data['tags'] . ' ' . $data['title'];
            $data['keywords']    = $data['tags'] . ' ' . $data['keywords'];
            $data['description'] = $data['tags'] . ' ' . $data['description'];
        }

        if ($data['cat_id'] == 4) {
            if (strpos($video['thumb'], 'loads/xiaoshuo')) {
                $locatoin = $video['thumb'];
            } else {
                $path = 'uploads/xiaoshuo/' . date('Ymd') . '/';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $fileName = 'xiaoshuo_' . time() . '.text';
                $locatoin = $path . $fileName;
            }

            if (!strpos($locatoin, 'loads/xiaoshuo')) {
                echo '发布成功';
                return;
            }

            $text            = $data['content'];
            $data['content'] = 'https://langren16.com/'.$locatoin;
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
                $fileName = time() . mt_rand(1000, 9999) . $ext;
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $localSrc = $path . $fileName;
                if (@file_put_contents($localSrc, $result)) {
                    $image = \think\Image::open($localSrc);
                    @$image->thumb(248, 355, 3)->save($localSrc);
                    $data['thumb'] = 'https://langren16.com/' . $localSrc;
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

    public function addCat() {
        $data = input('post.');
        try {
            $cat = Db::name('category')->where('cat', $data['cat'])->find();
            if (!$cat) {
                $res    = preg_replace_callback('/[^\x20-\x7f]/', function ($match) {
                    return urlencode($match[0]);
                }, $data['thumb'] . '?ie=UTF-8');
                $result = @file_get_contents($res);
                if ($result) {
                    $ext      = str_replace('?ie=UTF-8', '', substr($res, strrpos($res, '.')));
                    $path     = 'uploads/cat/thumb/' . date('Ymd') . '/';
                    $fileName = time() . mt_rand(1000, 9999) . $ext;
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
                Db::name('category')->insert($data);
            }
            echo '发布成功';
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}