<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/21 0021
 * Time: 下午 7:02
 */

namespace app\admin\controller;

use function GuzzleHttp\Psr7\str;
use QL\QueryList;
use think\Db;
use tree\Tree;
use app\model\Category as CateModel;
use app\model\Collect as CollectModel;
use app\model\Video as VideoModel;

class Collect extends Init {
    public static $width  = '';
    public static $height = '';

    //采集节点列表
    public function index() {
        $list = CollectModel::field('*')->order('id')->paginate(15);
        $page = $list->render();

        $this->assign('page', $page);// 赋值分页输出
        $this->assign('collect', $list);

        return $this->fetch();
    }

    //添加采集节点
    public function add() {
        if (isAjax()) {
            $this->save();
        }
        $list = CateModel::where('parent_id', 0)->select();
        $this->assign('list', $list);
        return $this->fetch('info');
    }

    //编辑采集节点
    public function edit() {
        if (isAjax()) {
            $this->save();
        }
        $list = CateModel::where('parent_id', 0)->select();
        $info = CollectModel::find(input('id'));

        $this->assign('info', $info);
        $this->assign('list', $list);
        $this->assign('main', $info['main']);

        return $this->fetch('info');
    }

    //入库操作
    public function save() {
        $data         = input('post.');
        $data['main'] = json_encode($data['main']);


        if ($data['id'] > 0) {
            $code = CollectModel::update($data, true);
            $id   = $data['id'];
        } else {
            unset($data['id']);
            $id = $code = CollectModel::insertGetId($data);
        }

        $code ? $this->success('操作成功', url('/collect/index', ['id' => $id])) : $this->error('操作失敗');
    }

    //采集缓冲页面
    private function tpl() {
        if (isAjax()) {
            session('on_start', 1);
            if (session('on_start')) $this->success();
        }
        $info = CollectModel::alias('a')->field('name,cid,list_url')->find(input('id'));
        $this->assign('info', $info);

        return $this->fetch('tpl');
    }

    //queryList采集
    function getRes() {
        header('Cache-Control: no-cache');         // 告知浏览器不进行缓存
        header('X-Accel-Buffering: no');           // 关闭加速缓冲
        $id = input('id');
        echo $this->tpl();
        set_time_limit(0);
        ignore_user_abort();
        if (!session('on_start')) exit;
        self::$width  = explode(',', config('upload_image_thumb'))[0];
        self::$height = explode(',', config('upload_image_thumb'))[1];
        global $info;
        $info       = CollectModel::find($id);
        $start_page = input('start_page') ?: 1;
        $end_page   = input('end_page') ?: 1;
        $page       = input('list_url');
        if ($page) CollectModel::where('id', $id)->update(['list_url' => $page]);
        $rules = [
            'url'   => [
                $info['main_url'],
                'href',
                '',
                function ($data) {
                    if ($data && !strpos($data, '://')) {//利用回调函数补全相对链接
                        $data = $GLOBALS['info']['site_url'] . $data;
                    }
                    return $data;
                }
            ],
            'title' => [$info['main_title'], 'text'],
            'thumb' => [
                $info['cover_img'],
                'src',
                '',
                function ($data) {
                    if ($data && !strpos($data, '://')) {//利用回调函数补全相对链接
                        $data = $GLOBALS['info']['site_url'] . $data;
                    }
                    return $data;
                }
            ]
        ];

        try {
            if ($start_page && $end_page && strrpos($page, '(*)')) {
                $n = 0;
                for ($i = $end_page; $i >= $start_page; $i--) {
                    $n++;

                    echo '<script>$("#msg").html("");</script>';
                    echo '<script>$("#msg").html("正在采集文章列表： 第' . $n . '页/共' . ($end_page + 1 - $start_page) . '页 ...<br>");</script>';
                    echo str_repeat(" ", 1024 * 4);//人为将缓冲数据扩充到4k
                    flush();
                    ob_flush();
                    sleep(1);

                    //if ($i == 1 && !strpos($page, '_')) $i = '';
                    $page1 = str_replace('(*)', $i, $page);

                    //采集该页面文章列表中所有[文章]的超链接
                    $html = @file_get_contents($page1);
                    $data = QueryList::html($html)->encoding('UTF-8', $info['charset'])->rules($rules)->range($info['list_child'])->query()->getData()->all();
                    if (!$data) continue;
                    $this->get_info(array_reverse($data), $info);
                }
                session('on_start', 0);
                set_time_limit(30);
                unset($GLOBALS['info']);
                unset($info);
                echo '<script>layer.msg("恭喜你采集完成~~~", {time: 0, icon:6});</script>';
                flush();
                ob_flush();
                echo str_repeat(" ", 1024 * 2);//人为将缓冲数据扩充到2k
            } else {
                echo '<script>$("#msg").html("");</script>';
                echo '<script>$("#msg").html("正在采集文章列表： 第' . 1 . '页/共' . ($end_page + 1 - $start_page) . '页 ...<br>");</script>';
                echo str_repeat(" ", 1024 * 4);//人为将缓冲数据扩充到4k
                flush();
                ob_flush();
                sleep(1);
                //采集该页面文章列表中所有[文章]的超链接
                $html = @file_get_contents($page);
                $data = QueryList::html($html)->encoding('UTF-8', $info['charset'])->rules($rules)->range($info['list_child'])->query()->getData()->all();
                if ($data) {
                    $this->get_info(array_reverse($data), $info);
                }
                session('on_start', 0);
                set_time_limit(30);
                unset($GLOBALS['info']);
                unset($info);
                echo '<script>layer.msg("恭喜你采集完成~~~", {time: 0, icon:6});</script>';
                flush();
                ob_flush();
                echo str_repeat(" ", 1024 * 2);//人为将缓冲数据扩充到2k
            }
        } catch (\Exception $e) {
            sleep(0);
            flush();
            ob_flush();
            dump($e);
            session('on_start', 0);
            set_time_limit(30);
            unset($GLOBALS['info']);
            unset($info);
            exit;
        }
    }

    function get_info($list, $info) {

        foreach ($list as $v) {
            try {
                $pageUrl = $v['url'];
                $thumb   = $v['thumb'];

                if (!$pageUrl) continue;
                $html = @file_get_contents($pageUrl);
                if (!$html) continue;
                $ql   = QueryList::html($html)->encoding('UTF-8', $info['charset']);
                $main = json_decode($info['main'], true);

                foreach ($main as $k => $v) {
                    $child        = $v['child'];
                    $attr         = $v['attr'];
                    $element      = $v['element'];
                    $replace_text = $v['replace_text'];
                    if ($element) {
                        if (in_array($child, ['text', 'html'])) {
                            $res = $ql->find($element)->$child();
                            if ($k == 'title') {
                                $video = VideoModel::field('id,title,thumb')->where(['title' => $res])->find();
                            }
                        } else {
                            if (strpos($child, 's')) {
                                $res = $ql->find($element)->$child($attr)->all();
                            } else {
                                $res = $ql->find($element)->$child($attr);
                            }
                        }
                        if ($k == 'down_url' && strpos($res, '2015')) {
                            continue;
                        }
                        if ($replace_text) {
                            if (strpos($replace_text, '|')) {
                                $res = str_replace(explode('|', $replace_text), '', $res);
                            } else {
                                $res = str_replace($replace_text, '', $res);
                            }
                        }

                        $res = str_replace(",", ',', $res);
                        if ($k == 'tags' || $k == 'area') {
                            $res = str_replace(" ", ',', $res);
                        }
                        $data[$k] = $res;
                    }
                }


                if (strpos('--' . $info['name'], '下载')) {
                    $data['down_url'] && $data['down_url'] = 'http://555.maomixia555.com:888' . $data['down_url'];
                } else {
                    $data['down_url'] && $data['down_url'] = 'http://666.maomixia666.com:888' . $data['down_url'];
                }

                $data['video_url'] && $video_url = 'https://768ii.com' . $data['video_url'];
                $res = $this->getVideoUrl($video_url, $info['charset']);
                if ($res) {
                    $res               = str_replace(["var playurl=m3u8url_24k_2+'", "';"], '', $res);
                    $data['video_url'] = 'https://768ii.com' . $res;
                } else {
                    unset($data['video_url']);
                }

                $data['update_time'] = time();

                if ($info['cid'] != 1 && $info['cid'] != 2 && empty($data['content'])) {
                    continue;
                }
                //数据入库操作
                if ($data['title']) {

                    if (is_array($data['content'])) {
                        $data['content'] = implode('$$', $data['content']);
                    }
                    if ($info['cid'] == 4) {
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
                    $data['channel'] = CateModel::where('id', $info['cid'])->value('alias');
                    $data['thumb']   = $thumb ?: $data['thumb'];
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

                    flush();
                    ob_flush();
                    sleep(1);
                    echo '<script>$("#msg").append("文章标题：' . $data['title'] . ' ...<br>");</script>';

                    if ($video) {
                        Db::name('video')->where('id', $video['id'])->update($data);
                    } else {
                        $data['insert_time'] = time();
                        $data['page']        = $pageUrl;
                        $data['col_id']      = $info['id'];
                        $data['col_name']    = $info['name'];
                        $data['cat_id']      = $info['cid'];
                        $data['score']       = rand(5, 9) . '.' . rand(0, 9);
                        Db::name('video')->insert($data);
                    }
                }
                unset($ql);
                unset($data);
                unset($res);
                unset($list);
            } catch (\Exception $e) {
                dump($e);
                return;
                continue;
            }
        }

    }

    //标题列表测试
    public function showTest() {
        set_time_limit(0);
        if (input('type') == 1) {
            global $info;
            $info = input('post.')['data'];
            $page = $info['list_url'];

            $charset = $info['charset'];
            $rules   = [
                'url'   => [
                    $info['main_url'],
                    'href',
                    '',
                    function ($data) {
                        if ($data && !strpos($data, '://')) {//利用回调函数补全相对链接
                            $data = $GLOBALS['info']['site_url'] . $data;
                        }
                        return $data;
                    }
                ],
                'title' => [$info['main_title'], 'text', ''],
                'thumb' => [
                    $info['cover_img'],
                    'src',
                    '',
                    function ($data) {
                        if ($data && !strpos($data, '://')) {//利用回调函数补全相对链接
                            $data = $GLOBALS['info']['site_url'] . $data;
                        }
                        return $data;
                    }
                ]
            ];

            echo '<pre style="margin: 0">';
            if (strpos($page, '*')) {
                $page = str_replace('(*)', 1, $page);
            }

            $html = @file_get_contents($page);
            $data = QueryList::html($html)->encoding('UTF-8', $charset)->rules($rules)->range($info['list_child'])->query()->getData()->all();

            print_r(array_reverse($data));
        } else {
            $this->main_test();
        }
        set_time_limit(30);
    }

    //querylist 内容页测试
    public function main_test() {
        global $info;
        $info = input('post.')['data'];
        echo '<pre>';
        //print_r($info);
        $page    = $info['test_url'];
        $charset = $info['charset'];

        $html = @file_get_contents($page);
        $ql   = QueryList::html($html)->encoding('UTF-8', $charset);

        foreach ($info['main'] as $k => $v) {
            $child        = $v['child'];
            $attr         = $v['attr'];
            $element      = $v['element'];
            $replace_text = $v['replace_text'];
            if ($element) {
                if (in_array($child, ['text', 'html'])) {
                    $res = $ql->find($element)->$child();
                } else {
                    if (strpos($child, 's')) {
                        $res = $ql->find($element)->$child($attr)->all();
                    } else {
                        $res = $ql->find($element)->$child($attr);
                    }
                }
                if ($replace_text) {
                    if (strpos($replace_text, '|')) {
                        $res = str_replace(explode('|', $replace_text), '', $res);
                    } else {
                        $res = str_replace($replace_text, '', $res);
                    }
                }

                $res = str_replace(",", ',', $res);
                if ($k == 'tags' || $k == 'area') {
                    $res = str_replace(" ", ',', $res);
                }
                $data[$k] = $res;
            }
        }

        if (strpos('--' . $info['name'], '下载')) {
            $data['down_url'] && $data['down_url'] = 'http://555.maomixia555.com:888' . $data['down_url'];
        } else {
            $data['down_url'] && $data['down_url'] = 'http://666.maomixia666.com:888' . $data['down_url'];
        }

        $data['video_url'] && $video_url = 'http://192.225.230.242:8082' . $data['video_url'];
        $res = $this->getVideoUrl($video_url, $charset);
        if ($res) {
            $res               = str_replace(["var playurl=m3u8url_24k_2+'", "';"], '', $res);
            $data['video_url'] = 'http://192.225.230.242:8082' . $res;
        } else {
            unset($data['video_url']);
        }
        if (is_array($data['content'])) {
            $data['content'] = implode('$$', $data['content']);
        }
        print_r($data);
    }


    public function getVideoUrl($page, $charset) {
        $html = @file_get_contents($page);
        $ql1  = QueryList::html($html)->encoding('UTF-8', $charset);
        $res  = $ql1->find('script:eq(3)')->text();
        return $res;
    }
}