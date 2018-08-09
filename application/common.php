<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use \think\Db;
use think\facade\Cache;

//是否为 POST 请求
function isPost() {
    $bl = \think\facade\Request::isPost();

    return $bl;
}

//是否为 GET 请求
function isGet() {
    $bl = \think\facade\Request::isGet();

    return $bl;
}

//是否为 Ajax 请求
function isAjax() {
    if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
        return true;
    }else{
        return false;
    };


}


/**
 * 文件写入
 * @param $filename
 * @param $content
 * @param string $type
 */
function file_write($filename,$content,$type='w'){
    $handle = fopen($filename,$type);
    fwrite($handle,$content);
    fclose($handle);
}

/**
 * 获取权限节点
 * @return array|PDOStatement|string|\think\Collection
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getMenu() {
    return Db::name('node')->select();
}

/**
 * 返回json字符
 * @param $arr
 */
function jsonReturn($arr){
    exit(json_encode($arr));
}


function addSysLog($data){
    Db::name('syslogs')->insertGetId($data);
}


/**
 * 设置评论楼层
 * @param $num
 * @return string
 */
function getNumName($num) {
    $words = $num . "楼";
    if ($num == 1) {
        $words = "沙发";
    } elseif ($num == 2) {
        $words = "椅子";
    } elseif ($num == 3) {
        $words = "板凳";
    }
    return $words;
}

/**
 *
 * 返回带前缀的表名
 * @param $table
 * @return string
 */
function tbName($table){
    $prefix = \think\facade\Config::get('database.prefix');
    return $prefix.$table;
}

/***
 * 加载js
 * @param $path
 * @param $js
 */
function loadJs($js, $path='home/js/') {
    header("Content-type:application/x-javascript; Charset: utf-8");
    $fileName = $path . md5($js) . '.js';
    if (file_exists('.'.$fileName)) {
        //return $fileName;
    }
    if (isset($js)) {
        $files = explode(",", $js);
        $str   = '';
        foreach ($files as $key => $val) {
            $str .= file_get_contents('.'.$path . $val . '.js');
        }
        $str = str_replace("\t", "", $str); //清除空格
        $str = str_replace("\r\n", "", $str);
        $str = str_replace("\n", "", $str);

        // 删除单行注释
        $str = preg_replace("/\/\/\s*[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", "", $str);
        // 删除多行注释
        $str = preg_replace("/\/\*[^\/]*\*\//s", "", $str);
        if(!file_exists($path)){
            mkdir($path,0755,true);
        }
        file_put_contents('.'.$fileName, $str);
        echo $fileName;
    }
}

/**
 * 加载css
 * @param $path
 * @param $css
 */
function loadCss($css, $path='home/css/') {
    header("content-type:text/css; charset: utf-8");
    $fileName = $path . md5($css) . '.css';
    if (file_exists('.'.$fileName)) {
        //return $fileName;
    }
    if (isset($css)) {
        $files = explode(",", $css);
        $fc    = '';
        foreach ($files as $key => $val) {
            $fc .= file_get_contents('.'.$path . $val . ".css");
        }
        $fc = str_replace("\t", "", $fc); //清除空格
        $fc = str_replace("\r\n", "", $fc);
        $fc = str_replace("\n", "", $fc);
        $fc = preg_replace("/\/\*[^\/]*\*\//s", "", $fc);
        if(!file_exists($path)){
            mkdir($path,0755,true);
        }
        file_put_contents('.'.$fileName, $fc);
        echo $fileName;
    }
}

/**
 * 发送邮件
 * @param $address
 * @param $title
 * @param $message
 * @return mixed
 */
function sendMail($address, $title, $message) {

    $mail = new \PHPMailer\PHPMailer();
    // 开启Debug
    $mail->SMTPDebug = 1;
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet = 'UTF-8';
    // 端口
    $mail->Port = config('mail_port');
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body = $message;
    // 设置邮件头的From字段。
    $mail->From = config('mail_address');
    // 设置发件人名字
    $mail->FromName = config('mail_fromname');
    // 设置邮件标题
    $mail->Subject = $title;
    // 设置SMTP服务器。
    $mail->Host = config('mail_smtp');
    // 设置为"需要验证"
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    // 设置用户名和密码。
    $mail->Username = config('mail_loginname');
    $mail->Password = config('mail_password');

    // 发送邮件。
    return ($mail->Send());
}

/**
 * 获取数据缓存
 * @param $table
 * @return mixed
 */
function getTableFile($table) {
    $commonTable = array("accounts", "friends");
    $ordTable    = array("dictionary", "modals_tags");
    if (in_array($table, $commonTable)) {
        $ord   = "ord ASC";
        $where = "is_check=1";
    } elseif (in_array($table, $ordTable)) {
        $ord = "ord ASC,id DESC";
    }
    $lists = \think\facade\Cache::store('file')->get($table . '/data');
    if (empty($lists)) {
        $lists = Db::name($table)->where($where)->order($ord)->select();
        \think\facade\Cache::store('file')->set($table . '/data', $lists);
    }
    return $lists;
}


if (!function_exists('parse_array')) {
    /**
     * 将一维数组解析成键值相同的数组
     * @param array $arr 一维数组
     * @author 胡卫兵 <6599986622@qq.com>
     * @return array
     */
    function parse_array($arr) {
        $result = [];
        foreach ($arr as $item) {
            $result[$item] = $item;
        }

        return $result;
    }
}

/**
 * 获取文字栏目名称
 * @param $id
 * @return mixed
 */
function getCatName($id) {
    return \app\model\Category::where(['id' => $id])->value('name');
}

/**
 * 去除空格、全角空格、换行等
 * @param $str
 * @return mixed
 */
function myTrim($str) {
    $search  = [" ", "　", "\n", "\r", "\t"];
    $replace = ["", "", "", "", ""];

    return str_replace($search, $replace, $str);
}

function setNum($num){
    return round($num/10000,1) . '万';
}

/**
 * 去除样式
 * @param $content
 * @return null|string|string[]
 */
function clearCss($content) {
    $content = preg_replace("/style=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式
    $content = preg_replace("/id=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/lang=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/width=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/height=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/border=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/face=.+?['|\"]/i", '', $content);//去除样式
    $content = preg_replace("/face=.+?['|\"]/", '', $content);//去除样式 只允许小写 正则匹配没有带 i 参数
    $content = preg_replace("/<p.*?>|<\/p>/is","", $content);
    $content = preg_replace("/<br>/is","", $content);
    return $content;
}


/**************************************************************
 *  生成指定长度的随机码。
 * @param int $length 随机码的长度。
 * @access public--
 **************************************************************/
function getRandCode($length) {
    $randomCode  = "";
    $randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkomnopqrstuvwxyz';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $randomChars{mt_rand(0, strlen($randomChars) + 1)};
    }

    return $randomCode;
}

/**
 * 把url转换迅雷thunder资源下载地址
 * @param $url
 * @param string $type
 * @return string
 */
function Thunder($url, $type = 'en') {
    if ($type == 'en') {
        return "thunder://" . base64_encode("AA" . $url . "ZZ");
    } else {
        return substr(base64_decode(substr(trim($url), 10)), 2, -2);
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function get_client_ip() {
        $unknown = 'unknown';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }
}

function get_session_id(){
    if (PHP_SESSION_ACTIVE != session_status()) {
        session_start();
    }
    return session_id();
}



/**
 * 获取当前域名
 * @return mixed
 */
function thisHost() {
    return $_SERVER['HTTP_HOST'];
}

/**
 * 获取上一个页面URL
 */
function prevUrl() {
    if($_SERVER['HTTP_REFERER'] != $_SERVER['REQUEST_URI'] && $_SERVER['HTTP_REFERER'] != '/user/register'){
        session('prevurl',$_SERVER['HTTP_REFERER']);
    }
    return session('prevurl');
}

/**
 * 递归删除文件
 * @param $dir
 * @return bool
 */
function del_dir($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir("$dir/$file") ? del_dir("$dir/$file") : @unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        @rmdir($dir);
    }
}

/**
 * 获取数据表某个字段
 * @param $table
 * @param $where
 * @param $name
 * @return string
 */
function getValue($table, $where, $name) {
    echo Db::name($table)->where($where)->value($name);
}

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function curl_request($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}

/**
 * URL 替换
 * @param $url
 * @param $replace
 * @return mixed
 */
function url_replace($url, $search, $replace, $order = '') {
    $bl = strpos($url, $search);
    if ($bl) {
        $search1 = strstr($url, $search);
        $url     = str_replace('/' . $search1, '', $url);
    }
    $url .= '/' . $search . '/' . $replace;

    if (!strpos($url, 'order')) {
        $url .= strlen(strstr($order, '/')) > 3 ? $order : '';
    }

    return $url;
}

function curl_post($url, $data = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

function curl_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

function download($url, $path) {
    $file     = curl_get($url);
    $resource = fopen($path, 'a');
    fwrite($resource, $file);
    fclose($resource);
}


/*移动端判断*/
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'];
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }

    return false;
}

// 汉字转拼单
function pinyin($str, $ishead = 0, $isclose = 1) {
    $str = iconv('utf-8', 'gbk', $str);//转成GBK
    global $pinyins;
    $restr = '';
    $str   = trim($str);
    $slen  = strlen($str);
    if ($slen < 2) {
        return $str;
    }
    if (count($pinyins) == 0) {
        $fp = fopen('./extend/pinyin.dat', 'r');
        if ($fp) {
            while (!feof($fp)) {
                $line                         = trim(fgets($fp));
                $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
            }
        }
        fclose($fp);
    }
    for ($i = 0; $i < $slen; $i++) {
        if (ord($str[$i]) > 0x80) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($pinyins[$c])) {
                if ($ishead == 0) {
                    $restr .= $pinyins[$c];
                } else {
                    $restr .= $pinyins[$c][0];
                }
            } else {
                //$restr .= "_";
            }
        } else if (mb_eregi("[a-z0-9]", $str[$i])) {
            $restr .= $str[$i];
        } else {
            //$restr .= "_";
        }
    }
    if ($isclose == 0) {
        unset($pinyins);
    }

    return $restr;
}

function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f) $ret .= chr($val); else if ($val < 0x800) $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f)); else
                $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i   += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}