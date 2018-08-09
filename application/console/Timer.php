<?php
class Timer
{
    private static $_instance;
    private static $host;
    private static $port;
    private static $password;
    private static $list;
    public static  $redisObj;
    public static  $client;

    private function __construct()
    {
        $redis = new \Redis();
        $redis->connect(self::$host, self::$port);
        $redis->auth     = self::$password;
        self::$_instance = $this;
        self::$redisObj  = $redis;

        return self::$_instance;
    }

    //启动redis服务
    public static function instance(array $param)
    {
        self::$host = $param['host'];
        self::$port = $param['port'];

        self::$password = $param['password'];
        self::$list = $param['list'];
        if (!empty(self::$_instance)) {
            return self::$_instance;
        } else {
            return new self();
        }
    }

    //异步客户端
    public static function client()
    {
        self::$client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
        $res          = self::$client->connect("127.0.0.1", 9527);

        return $res;
    }

    //取出队列里最后一个值并删除
    public static function getList()
    {
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $res = self::$redisObj->rPop(self::$list);
            if (empty($res)) break;
            $res = json_decode($res,true);
            $data[$i] = $res;
        }
        return $data;
    }
}


//定时器
swoole_timer_tick(1000, function () {
    $params['host'] = '127.0.0.1';
    $params['port'] = 6379;
    $params['password'] = '';
    $params['list'] = 'lists';//KEY 为lists
    $Server         = Timer::instance($params);

    $data = $Server::getList();
    //print_r($data);

    //判断redis是否有数据，没有的话就结束任务
    if (empty($data)) return;

    $ret = $Server::client();
    if (empty($ret)) {
        echo '任务推送失败';
    } else {
        //var_dump(json_decode($data,true));
        echo date('Y-m-d H:i:s')."\n";
        $Server::$client->send(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
});
