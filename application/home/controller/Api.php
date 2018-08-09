<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/22
 * Time: 12:20
 */

namespace app\home\controller;

use app\model\Users;
use app\model\Video;
use app\model\Category;
use think\Db;
use app\model\Comment;
use app\model\Likes;


class Api {
    /**
     * 评论
     * @return array
     * @throws \think\Exception
     *
     */
    public function postComment() {
        $user['nickname'] = input('nickname/s');
        $user['id']       = input('user_id/d');

        if (!$user['id']) {
            $ip   = get_client_ip();
            $ext  = substr($ip, strrpos($ip, '.') + 1);
            $ip   = str_replace($ext, '***', $ip);
            $user = ['nickname' => '游客[' . $ip . ']', 'id' => 0];
        }
        $vid               = input('id/d');
        $user_id           = $user['id'];
        $parent_id         = input('pid/d');
        $data['uid']       = $user_id;
        $data['parent_id'] = $parent_id;
        $data['vid']       = $vid; //文章id


        if (empty($data['vid'])) return json_encode(['code' => 0, 'msg' => '缺少参数[vid]']);
        $content = addslashes(input('content/s'));
        if($content != strip_tags($content)){
            return json_encode(['code' => 0, 'msg' => '您提交的内容包含了非法字符。']);
        }
        if(strpos($content,'//')){
            return json_encode(['code' => 0, 'msg' => '您提交的内容包含了非法字符。']);
        }
        if (strlen($content) < 5) return json_encode(['code' => 0, 'msg' => '评论的内容不能少于10个字符。']);

        if (time() - cache("comment_time_$vid".$parent_id) < 120 && cache("comment_time_$vid".$parent_id) > 0) {//2分钟以后发布
            return json_encode(['code' => 0, 'msg' => '您提交评论的速度太快了，请稍后再发表评论。']);
        }


        $data['parent_id']   = input("post.pid", 0, 'int');
        $data['to_uid']      = input("post.to_uid", 0, 'int');
        $data['nickname']    = $user['nickname'];
        $data['to_name']     = input('to_name');
        $data['status']      = 0;
        $data['insert_time'] = time();
        $data['content']     = htmlspecialchars($content);

        $lastid = Comment::insertGetId($data);

        if ($lastid > 0) {
            Video::where('id', $data['vid'])->setInc('comments', 1);
        }

        cache("comment_time_$vid".$parent_id, time(), 120);
        return json_encode(['code' => 1, 'msg' => '评论成功，待审核']);
    }

    /**
     * 获取当前文章下的评论
     */
    public function comments() {
        $id = input('id/d');

        if (empty($id)) return json_encode(['code' => 0, 'msg' => '缺少参数[id]']);
        $where[] = ['status', '=', 1];
        $where[] = ['parent_id', '=', 0];
        $where[] = ['vid', '=', $id];
        $count   = Comment::where($where)->count();
        $comment = Comment::field('*')->where($where)->order('insert_time desc')->cache(60)->paginate(10, $count, ['query' => input()]);

        $html = '';
        foreach ($comment as $k => $v) {
            (600 - (time() - cache("good_time_" . $v['id']))) || $class = 'active';
            $comments[$k] = $v;
            $list         = Comment::getLists($v['id'], $id);
            count($list) && $style = "style='margin-top:10px'";
            $html .= '<div class="comments__list ng-star-inserted">';
            $html .= '<div class="comment__wrap">';
            $html .= '<div class="comment">';
            $html .= '<div class="comment_user_content">';
            $html .= '<div class="comment_user_img" style="background-image:url(\'/home/img/person.svg\');"></div>';
            $html .= '</div>';
            $html .= '<div class="comment_content">';
            $html .= '<div class="comment__header">';
            $html .= '<span class="theme-color">' . $v['nickname'] . '</span>';
            $html .= '<span class="rate-video__button-coment colorClass rate-video-comment-bad float-right good-inc ' . $class . '" data-id="' . $v['id'] . '"></span>';
            $html .= '<span class="font-size-12 float-right good-add" id="good-num' . $v['id'] . '">' . ($v['good'] ?: '') . '</span>';
            $html .= '</div>';
            $html .= '<div class="example-child child_' . $v['id'] . '">';
            $html .= $this->getChildComment($list, $v['id']);
            $html .= '</div>';
            $html .= '<div class="comment__body" ' . $style . '>';
            $html .= '<p>' . $v['content'] . '</p>';
            $html .= '</div>';
            $html .= '<div class="comment__footer">';
            $html .= '<span class="font-size-12">' . date('Y-m-d H:i:s', $v['insert_time']) . '</span>';
            $html .= '<a class="comment__reply" pid="' . $v['id'] . '" nickname="' . $v['nickname'] . '" href="#post_content">回复</a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            unset($list);
        }

        $temp = '<link href="/home/css/comments.css" rel="stylesheet" type="text/css"/>
            <div id="post_content">
                <textarea name="content" class="content" id="content" placeholder="吐个槽..."></textarea>
                <button type="button" class="btn btn-block btn-warning post_comment">提&nbsp;&nbsp;&nbsp;交</button>
            </div>
            <div class="comment_list_group">';

        if ($html) {
            $temp .= $html;
        } else {
            $temp .= '<div class="comment__body1" id="border-gai0"><div class="commet_color"> 暂无评论</div></div>';
        }

        $temp .= '</div><div class="hy-page clearfix">' . $comment->renders() . '</div><style>.hy-page .hidden-xs{display: none;}.hy-page .visible-xs{display: inline-block!important;}</style><script src="/home/js/comments.js"></script>';

        echo $temp;
    }

    /**
     * 生成验证码
     */
    public function verify() {
        echo '<img src="' . captcha_src() . '?' . time() . '" alt="看不清？点击更换" title="看不清？点击更换" align="absmiddle" class="pull-right" style="width:130px; height:35px;border-radius: 5px;"/>';
    }

    /**
     *  把temp_arr数组中保存的引用评论信息转换为html形式
     */
    function getChildComment($temp_arr, $pid) {
        $htmlStr = $hideStr = '';
        if (!is_array($temp_arr) || empty($temp_arr)) {
            return '';
        }
        $num    = count($temp_arr);
        $button = '<div class="comment_content1 border-gai3 show_tmp" id="' . $pid . '">
                       <div class="comment__body1" id="border-gai0">
                            <div class="commet_color"> 点开隐藏楼层</div>
                         </div>
                     </div>';

        $hideStrs = '<div style="display:none" class="tmp_' . $pid . '">';
        foreach ($temp_arr as $k => $v) {
            (600 - (time() - cache("good_time_" . $v['id']))) || $class = 'active';
            if ($num > 5) {
                if ($k < 5) {
                    $html = '<div class="comment comment--reply border-gai border-gai30 child-' . $k . '">';
                    $html .= $k == 4 ? $htmlStr . $button : $htmlStr;
                    if ($k < 4) {
                        $html .= '<div class="comment_content1">';
                        $html .= '<div class="comment__header">';
                        $html .= '<div class="leftx-a">';
                        $html .= '<a>' . ($k + 1) . '</a>&nbsp;&nbsp;' . $v['nickname'];
                        $html .= '</div>';
                        $html .= '<div class="rightx-a">';
                        $html .= '<a id="good-num' . $v['id'] . '">' . ($v['good'] ?: '') . '</a>';
                        $html .= '<div class="rate-video__button-coment colorClass rate-video-comment-bad good-inc ' . $class . '" data-id="' . $v['id'] . '"></div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="comment__body">';
                        $html .= '<p>' . $v['content'] . '<br></p>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';

                    $htmlStr = $html;
                }

                $hide    = '<div class="comment comment--reply border-gai border-gai30 ">';
                $hide    .= $hideStr;
                $hide    .= '<div class="comment_content1">';
                $hide    .= '<div class="comment__header">';
                $hide    .= '<div class="leftx-a">';
                $hide    .= '<a>' . ($k + 1) . '</a>&nbsp;&nbsp;' . $v['nickname'];
                $hide    .= '</div>';
                $hide    .= '<div class="rightx-a">';
                $hide    .= '<a id="good-num' . $v['id'] . '">' . ($v['good'] ?: '') . '</a>';
                $hide    .= '<div class="rate-video__button-coment colorClass rate-video-comment-bad good-inc ' . $class . '"  data-id="' . $v['id'] . '"></div>';
                $hide    .= '</div>';
                $hide    .= '</div>';
                $hide    .= '<div class="comment__body">';
                $hide    .= '<p>' . $v['content'] . '<br></p>';
                $hide    .= '</div>';
                $hide    .= '</div>';
                $hide    .= '</div>';
                $hideStr = $hide;

            } else {
                $html    = '<div class="comment comment--reply border-gai border-gai30 child-' . $k . '">';
                $html    .= $htmlStr;
                $html    .= '<div class="comment_content1">';
                $html    .= '<div class="comment__header">';
                $html    .= '<div class="leftx-a">';
                $html    .= '<a>' . ($k + 1) . '</a>&nbsp;&nbsp;' . $v['nickname'];
                $html    .= '</div>';
                $html    .= '<div class="rightx-a">';
                $html    .= '<a id="good-num' . $v['id'] . '">' . ($v['good'] ?: '') . '</a>';
                $html    .= '<div class="rate-video__button-coment colorClass rate-video-comment-bad good-inc ' . $class . '"  data-id="' . $v['id'] . '"></div>';
                $html    .= '</div>';
                $html    .= '</div>';
                $html    .= '<div class="comment__body">';
                $html    .= '<p>' . $v['content'] . '<br></p>';
                $html    .= '</div>';
                $html    .= '</div>';
                $html    .= '</div>';
                $htmlStr = $html;
            }

        }
        $hideStrs .= $hideStr . '</div>';
        return $htmlStr . $hideStrs;
    }

    public function gethost() {
        echo str_replace('www.', '', thisHost());
    }


    /**
     * 取消收藏
     */
    public function delFav() {
        $vid     = input('id/d');
        $user_id = input('user_id/d');
        if (!$vid) {
            return json_encode(['code' => 0, 'msg' => '缺少参数']);
        }
        if (!$user_id) {
            return json_encode(['code' => 0, 'msg' => '您还未登陆！请登陆后在执行此操作~']);

        }

        $where[] = ['user_id', '=', $user_id];
        $where[] = ['id', '=', $vid];
        $like    = Likes::where($where)->value('id');
        if (!$like) {
            return json_encode(['code' => 0, 'msg' => '您还未收藏此视频，不能取消收藏！']);
        }
        Db::startTrans();
        try {
            Likes::where('id', $like)->delete();
            Video::where('id', $vid)->setDec('like');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return json_encode(['code' => 0, 'msg' => '操作失败，请重试~']);
        }
        return json_encode(['code' => 1, 'msg' => '取消成功~']);
    }

    /**
     * 点赞
     */
    public function clickGood() {
        $id = input('id/d');
        if (time() - cache("good_time_$id") < 600 && cache("good_time_$id") > 0) {//2分钟以后发布
            return json_encode([
                'code' => 0,
                'msg'  => "您点赞的速度太快了，请" . (600 - (time() - cache("good_time_$id"))) . "秒后再来。"
            ]);
        }

        if (empty($id)) return json_encode(['code' => 0, 'msg' => '缺少参数[id]']);
        Video::where('id', $id)->setInc('good');
        cache("good_time_$id", time(), 600);

        return json_encode(['code' => 1, 'msg' => '点赞成功']);
    }

    /**
     * 提交收藏
     */
    public function addFav() {
        $vid      = input('id/d');
        $nickname = input('nickname/s');
        $user_id  = input('user_id/d');
        if (!$vid) {
            return json_encode(['code' => 0, 'msg' => '缺少参数']);
        }
        if (!$user_id) {
            return json_encode(['code' => 0, 'msg' => '您还未登陆！请登陆后在执行此操作~']);

        }
        $row = Video::field('title,channel')->cache(60)->where('id', $vid)->find();
        if (!$row) {
            return json_encode(['code' => 0, 'msg' => '操作失败，请重试~']);
        }
        $where[] = ['user_id', '=', $user_id];
        $where[] = ['vid', '=', $vid];
        if (Likes::where($where)->value('id')) {
            return json_encode(['code' => 0, 'msg' =>'您已收藏过此内容，请勿重复收藏~']);
        }
        Db::startTrans();
        try {
            $data = [
                'vid'         => $vid,
                'title'       => $row['title'],
                'user_id'     => $user_id,
                'nickname'    => $nickname,
                'channel'     => $row['channel'],
                'insert_time' => time()
            ];
            Likes::insert($data);
            Video::where('id', $vid)->setInc('like');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return json_encode(['code' => 0, 'msg' => '操作失败，请重试~']);
        }

        return json_encode(['code' => 1, 'msg' => '收藏成功~']);
    }

    /**
     * 评论点赞
     */
    public function cmtGoodInc() {
        $id = input('id/d');
        if (time() - cache("good_inc_$id") < 600 && cache("good_inc_$id") > 0) {//2分钟以后发布
            return json_encode(['code' => 0, 'msg' => '您点赞的速度太快了，请稍后再点击~。']);
        }

        if (empty($id)) return json_encode(['code' => 0, 'msg' => '缺少参数[id]']);
        Comment::where('id', $id)->setInc('good');
        cache("good_inc_$id", time(), 600);

        return json_encode(['code' => 1, 'msg' => '点赞成功']);
    }


    public function index() {
        $data  = input('post.');
        $video = Video::field('id,title,thumb,content')->where(['title' => $data['title']])->find();

        if ($data['cat_id'] == 18) {
            $data['title']       = $data['tags'] . ' ' . $data['title'];
            $data['keywords']    = $data['tags'] . ' ' . $data['keywords'];
            $data['description'] = $data['tags'] . ' ' . $data['description'];
        }

        if ($data['cat_id'] == 4) {
            $path = ROOT_PATH . '/../uploads/xiaoshuo/' . date('Ymd') . '/';
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
                $path     = ROOT_PATH . '/../uploads/thumb/' . date('Ymd') . '/';
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

    public function addCat() {
        $data = input('post.');
        try {
            $cat = Category::where('cat', $data['cat'])->find();
            if (!$cat) {
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
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function login() {

        $data     = input('post.');
        $username = $data['username'];
        $password = $data['password'];

        $validate = new \app\validate\User();
        $rule     = [
            'username|用户名' => 'require|email',
            //'verify|验证码'   => 'require|captcha',
            'password|密码'  => 'require|alphaNum|length:6,20',//'verify|验证码' => 'require|captcha',
        ];
        if (!$validate->check($data, $rule)) {
            return json_encode(['code' => 0, 'msg' => $validate->getError()]);
        }

        $user = Users::field('id,username,nickname,avatar,password,code')->where('username', $username)->find();
        if (empty($user)) {
            return json_encode(['code' => 0, 'msg' => '账号还未注册', 'data' => ['class' => 'username']]);
        } else {
            if ($user['password'] == md5($user['code'] . $password)) {
                Users::where('id', $user['id'])->setField([
                    'login_time'  => time(),
                    'login_ip'    => get_client_ip(),
                    'action_time' => time(),
                    'action_ip'   => get_client_ip(),
                    'is_login'    => 1
                ]);
                unset($user['password']);
                unset($user['code']);
                $user['login_ip'] = get_client_ip();
                $ref = $data['ref'];

                unset($data['repassword']);

                return json_encode([
                    'code' => 1,
                    'msg'  => '登录成功',
                    'ref'  => $ref,
                    'data' => [
                        'id'       => $user['id'],
                        'username' => $user['username'],
                        'nickname' => $user['nickname']
                    ]
                ]);
            } else {
                return json_encode(['code' => 0, 'msg' => '密码不正确']);
            }
        }

    }


    public function register() {
        $data     = input('post.');
        $validate = new \app\validate\User();
        $rule     = [
            'username|用户名' => 'require|email',
            'nickname|昵称'  => 'require|chs|length:2,6',
            'password|密码'  => 'require',
            'repassword'   => 'require|confirm:password',

        ];

        if (!$validate->check($data, $rule)) {
            return json_encode(['code' => 0, 'msg' => $validate->getError()]);
        }


        if(strpos('--'.$data['nickname'],'狼')){
            return json_encode(['code' => 0, 'msg' => '昵称不能包含[狼]字']);
        }
        if(strpos('--'.$data['nickname'],'管理')){
            return json_encode(['code' => 0, 'msg' => '昵称不能包含[管理]二字']);
        }

        $user = Users::field('id,username,nickname,avatar,password,code')->where(['username' => $data['username']])->find();
        if ($user) return json_encode(['code' => 0, 'msg' => '该账号以注册']);
        $code                = mt_rand(10000, 99999);
        $data['password']    = md5($code . $data['password']);
        $data['code']        = $code;
        $data['insert_time'] = time();
        $data['update_time'] = time();
        $data['login_time']  = time();
        $data['login_ip']    = get_client_ip();
        $data['is_login']    = 1;

        $ref = $data['ref'];

        unset($data['repassword']);
        unset($data['ref']);
        $id = Users::insertGetId($data);
        if ($id) {
            $data['id'] = $id;
            return json_encode(['code' => 1, 'msg' => '注册成功','ref'=>$ref]);
        }
        $this->error('注册失败');
    }

    public function resetPwd() {
        $data     = input('post.');
        $user_id  = input('user_id/d');
        $validate = new \app\validate\User();
        $rule     = [
            'password|密码' => 'require|alphaNum|length:6,20',//
            'repassword'  => 'require|confirm:password',
        ];
        if (!$validate->check($data, $rule)) {
            return json_encode(['code' => 0, 'msg' => $validate->getError()]);
        }
        $code = Users::where('id', $user_id)->value('code');

        if (!Users::where([
            ['id', '=', $user_id],
            ['password', '=', md5($code . $data['oldpwd'])]
        ])->find()) {
            return json_encode(['code' => 0, 'msg' => '原密码错误']);
        }

        $password = md5($code . $data['password']);
        $pwd_view = $data['password'];

        $res = Users::where('id', $user_id)->update(['password' => $password, 'pwd_view' => $pwd_view]);
        if ($res) {
            return json_encode(['code' => 1, 'msg' => '修改成功']);
        } else {
            return json_encode(['code' => 0, 'msg' => '修改失败']);
        }
    }

    public function getInfo() {
        $user_id = input('user_id/d');
        if ($user_id) {

            $info = Users::field('nickname,username,id,online as status,sign,avatar,login_ip')->find($user_id);
            $info = $info ? $info->toArray() : [];

            return json_encode(['code' => 1, 'data' => $info]);
        } else {
            return json_encode(['code' => 0]);
        }

    }

    public function getFav() {
        $user_id = input('user_id/d');;
        if (!$user_id) {
            echo json_encode(['code' => 0]);
        } else {
            $list = Likes::where('user_id', $user_id)->paginate(18);

            echo json_encode(['code' => 1, 'page' => $list->render(), 'data' => $list]);
        }
    }

    //用户问题反馈
    public function addFb() {
        $data['country']     = input('country/s');
        $data['internet']    = input('internet/s');
        $data['system']      = input('system/s');
        $data['browser']     = input('browser/s');
        $data['content']     = input('content/s');
        $data['insert_time'] = time();
        $ip                  = get_client_ip();

        if (time() - cache("comment_time_$ip") < 600 && cache("comment_time_$ip") > 0) {//2分钟以后发布
            return json_encode(['code' => 0, 'msg' => '您反馈问题的速度太快了，请稍后再再来反馈。']);
        }

        $message = [
            'country'  => '请选择您所在的国家',
            'internet' => '请选项您的上网环境',
            'system'   => '请选择您的设备系统',
            'browser'  => '请选择您使用的浏览器',
            'content'  => '请输入您要反馈的问题',
        ];

        foreach ($data as $k => $v) {
            if (empty($v)) {
                return json_encode(['code' => 0, 'msg' => $message[$k]]);
            }
        }

        Db::name('feedback')->insert($data);
        cache("comment_time_$ip", time(), 600);
        return json_encode(['code' => 1, 'msg' => '提交成功']);
    }

    public function clickGonggao(){
        $id = input('id/d');
        if (time() - cache("good_inc1_$id") < 600 && cache("good_inc1_$id") > 0) {//2分钟以后发布
            return json_encode(['code' => 0, 'msg' => '您点赞的速度太快了，请稍后再点击~。']);
        }

        if (empty($id)) return json_encode(['code' => 0, 'msg' => '缺少参数[id]']);
        Db::name('placards')->where('id', $id)->setInc('goods');
        cache("good_inc1_$id", time(), 600);

        return json_encode(['code' => 1, 'msg' => '点赞成功']);
    }


    //获取今天域名
    public function tadyHost(){
        return config('web_host');
    }

    /**
     * aes加密
     * @param $data
     * @return string
     */
    public function encrypt($data) {
        $encrypted = json_encode($data);
        $encrypted = openssl_encrypt($encrypted, 'aes-128-cbc', self::$key, OPENSSL_RAW_DATA, self::$iv);
        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    /**
     * aes解密
     * @param $encrypted
     * @return mixed
     */
    public function decrypt($encrypted) {
        $encrypted = base64_decode($encrypted);
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', self::$key, OPENSSL_RAW_DATA, self::$iv);

        return json_decode($decrypted, true);
    }

}