<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/3/12
 * Time: 23:04
 */

namespace app\admin\controller;
use think\Controller;

/***
 * Class Ueditorup
 * @package ueditor 编辑器上传类
 */
class Ueditorup extends Controller
{
    function uploader(){
        echo header("Access-Control-Allow-Origin:*");
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("ueditor.config.json")), true);

        $action = $_GET['action'];
        switch ($action) {
            case 'config':
                $result =  $CONFIG;
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $up = new \ueditor\Upload();
                $result = $up->init();
                //self::save($result);
                /**
                 * 得到上传文件所对应的各个参数,数组结构
                 * array(
                 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
                 *     "url" => "",            //返回的地址
                 *     "title" => "",          //新文件名
                 *     "original" => "",       //原始文件名
                 *     "type" => ""            //文件类型
                 *     "size" => "",           //文件大小
                 * )
                 */
                break;

            /* 列出图片 */
            case 'listimage':
                $up = new \ueditor\Lists();
                $result = $up->init();
                break;
            /* 列出文件 */
            case 'listfile':
                $up = new \ueditor\Lists();
                $result = $up->init();
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $up = new \ueditor\Crawler();
                $result = $up->init();
                break;

            default:
                $result = array(
                    'state'=> '请求地址出错'
                );
                break;
        }


        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            return json_encode($result);
        }
    }

    private static function save($result){
        $username = session('admin.nickname') ? session('admin.nickname') : session('admin.username');
        $user_id = session('admin.id');
        $data = array(
            'module' => request()->module(),
            'file_name' => $result['title'],
            'file_path' => $result['url'],
            'file_size' => $result['size'],
            'file_ext' => substr($result['title'],strrpos('.')+1),
            'is_image' => 1,
            'downloads' => 0,
            'user_id' => $user_id,
            'username' => $username,
            'upload_time' => time(),
            'upload_ip' => get_client_ip(),
        );
        @db('attachment')->insertGetId($data);
    }
}