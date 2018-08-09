<?php

namespace app\admin\controller;
use think\Controller;
use think\Image;

class Upload extends Controller
{
    /**
     * 图片上传页面
     */
    function upload(){
        $func = input('func');
        $path = input('path');
        $wrap_width = input('wrap_width') ? input('wrap_width') : 490;
        $img_width = input('img_width') ? input('img_width') : 100;
        $thumb_w = input('thumb_w');
        $thumb_h = input('thumb_h');
        $file_width = $wrap_width - 30;
        $username = session('admin.nickname') ? session('admin.nickname') : session('admin.username');
        $info = array(
            'num'=> input('num'),
            'title' => '',
            'upload' =>url('/upload/imageUp',['savepath'=>$path,'dir'=>'image','user_id'=>session('admin.id'),'username'=>$username]),
            'size' => '2M',
            'wrap_width' => $wrap_width,
            'img_width' => $img_width,
            'file_width' => $file_width,
            'thumb_w' => $thumb_w,
            'thumb_h' => $thumb_h,
            'type' =>'jpg,png,gif,jpeg',
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );
        $this->assign('info',$info);
        return $this->fetch('index');
    }

    /**
     * 头像上传
     * @return mixed|void
     */
    function avatar(){
        if(isPost()){
            /** 温馨提示：
             * 在flash的参数名upload_url中可自行定义一些参数（请求方式：GET），定义后在服务器端获取即可，比如可以应用到用户验证，文件的保存名等。
             * 本示例未作极致的用户体验与严谨的安全设计（如用户直接访问此页时该如何，万一客户端数据不可信时验证文件的大小、类型等），只保证正常情况下无误，请阁下注意。
             */
            header('Content-Type: text/html; charset=utf-8');
            $result = array();
            $result['success'] = false;
            $successNum = 0;
            //定义一个变量用以储存当前头像的序号
            $i = 1;
            $msg = '';
            //上传目录
            $path = "/uploads/avatar";
            $dir = $_SERVER['DOCUMENT_ROOT'].$path;
            //遍历所有文件域
            while (list($key, $val) = @each($_FILES))
            {
                if ( $_FILES[$key]['error'] > 0)
                {
                    $msg .= $_FILES[$key]['error'];
                }
                else
                {


                    //如下代码在上传接口upload.php中定义了一个user=xxx的参数：
                    //在此即可用$_POST["user"]获取xxx。
                    $user_id = input('user_id/d');
                    $user_dir = 'user_id_'.$user_id;
                    if(!file_exists("$dir/$user_dir")){
                        mkdir("$dir/$user_dir",0755,true);
                    }
                    //处理原始图片（默认的 file 域的名称是__source，可在插件配置参数中自定义。参数名：src_field_name）
                    //如果在插件中定义可以上传原始图片的话，可在此处理，否则可以忽略。
                    //当前头像基于原图的初始化参数，用于修改头像时保证界面的视图跟保存头像时一致。帮助提升用户体验度。修改头像时设置默认加载的原图的url为此图片的url+该参数即可。
                    if ($key == '__source')
                    {
                        $initParams = $_POST["__initParams"];
                        $virtualPath = "$dir/avatar".$i.".jpg";
                        $result['sourceUrl'] = '/' . $virtualPath.$initParams;
                        move_uploaded_file($_FILES[$key]["tmp_name"], $virtualPath);
                        $successNum++;
                    }
                    //处理头像图片(默认的 file 域的名称：__avatar1,2,3...，可在插件配置参数中自定义，参数名：avatar_field_names)
                    else if (strpos($key, '__avatar') === 0)
                    {
                        $virtualPath = "$dir/$user_dir/avatar_" . $i . ".jpg";
                        $result['avatarUrls']['avatar'.$i] = "$path/$user_dir/avatar_" . $i . ".jpg";
                        move_uploaded_file($_FILES[$key]["tmp_name"], $virtualPath);
                        /*
                            可在此将 $result['avatarUrls'][$i] 储存到数据库
                        */
                        $successNum++;
                        $i++;
                    }
                }
            }
            $result['msg'] = $msg;
            if ($successNum > 0)
            {
                $result['success'] = true;
            }
            //返回图片的保存结果（返回内容为json字符串）
            print json_encode($result);
            return;
        }
        $this->assign('user_id',input('user_id'));
        return $this->fetch();
    }

    /**
     *  删除上传的图片
     */
    function delupload(){

        $action= input('action') ? input('action') : null;
        $filename= input('filename') ? input('filename') : null;
        /*$filename= str_replace('../','',$filename);
        $filename= trim($filename,'.');*/
        $filename= ltrim($filename,'/');
        if($action=='del' && !empty($filename)){
            $size = getimagesize($filename);
            $filetype = explode('/',$size['mime']);
            if($filetype[0]!='image'){
                return false;
                exit;
            }
            unlink($filename);
            unlink(str_replace('thumb_','',$filename));
            exit;
        }
    }

    /**
     * @function 图片上传
     */
    function imageUp()
    {
        // 上传图片框中的描述表单名称，
        $path = input('savepath');
        $user_id = input('user_id');
        $username = input('username');
        $path =   'uploads/'. $path;
        $w =   input('thumb_w');
        $h =   input('thumb_h');

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('Filedata');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move($path);
        if ($info) {
            $state = "SUCCESS";
        } else {
            $state = "ERROR" . $file->getError();
        }
        $fileName = $info->getSaveName();
        $fileName= str_replace('\\','/',$fileName);
        $localSrc = $path . '/' .$fileName;
        if($w>0 && $h>0){
            $image = \think\Image::open($localSrc);
            $fileName = str_replace(date('Ymd').'/', date('Ymd').'/'.'thumb_', $fileName);
            $image->thumb($w, $h, 3)->save($path.'/'.$fileName);
            /*$url = url('/upload/delupload');
            $post_data = array ("action" => "del","filename" => '/'.$localSrc);
            curl_post($url, $post_data);*/
        }

        /*if(!isset($info['upfile'])){
            $info['upfile'] = $info['Filedata'];
        }else{
            //编辑器插入图片水印处理
            if($this->savePath=='Goods/'){
                $image = new \think\Image();
                $water = tpCache('water');
                $imgresource = ".".$info['upfile']['urlpath'];
                $image->open($imgresource);
                if($water['is_mark']==1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
                    if($water['mark_type'] == 'text'){
                        $image->text($water['mark_txt'],'./hgzb.ttf',20,'#000000',9)->save($imgresource);
                    }else{
                        $image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
                    }
                }
            }
        }*/

        $return_data['url'] = '/' . $path . '/' .$fileName;
        //$return_data['url'] = '/' . $localSrc;
        $return_data['state'] = $state;
        $data = array(
            'module' => request()->module(),
            'file_name' => substr($fileName,strrpos($fileName,'/')+1),
            'file_path' => '/' . $path.'/'.$fileName,
            'file_size' => $info->getSize(),
            'file_ext' => substr($fileName,strrpos($fileName,'.')+1),
            'is_image' => 1,
            'downloads' => 0,
            'user_id' => $user_id,
            'username' => $username,
            'upload_time' => time(),
            'upload_ip' => $_SERVER['REMOTE_ADDR'],
        );

        //@db('attachment')->insert($data);
        return json_encode($return_data);
    }



}