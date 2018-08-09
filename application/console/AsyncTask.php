<?php
namespace app\Console;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class AsyncTask extends Command
{
    protected $server;

    // 命令行配置函数
    protected function configure()
    {
        // setName 设置命令行名称
        // setDescription 设置命令行描述

        $this->setName('task:start')->setDescription('Start Task Server!');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {

        // 实例化客户端
        $this->server = new \swoole_server('127.0.0.1', 9527);

        // server 运行前配置
        $this->server->set([
            'worker_num'      => 20,
            'daemonize'       => true,
            'task_worker_num' => 50  # task 进程数
        ]);

        // 注册回调函数
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);
        $this->server->on('Close', [$this, 'onClose']);

        $this->server->start();
    }

    // 主进程启动时回调函数
    public function onStart(\swoole_server $server)
    {
        echo "Start\n";
    }

    // 建立连接时回调函数
    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        echo "Connect\n";
    }

    // 收到信息时回调函数
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $list = json_decode($data,true);

        // 投递异步任务
        foreach($list as $v){
            $task_id = $server->task($v);
            //echo "Dispath AsyncTask: id={$task_id}\n";
        }

        // 将受到的客户端消息再返回给客户端
        $server->send($fd, "Message form Server: {$data}， task_id: {$task_id}");
    }

    // 异步任务处理函数
    public function onTask(\swoole_server $server, $task_id, $from_id, $data)
    {
        $content = curl_request($data['url']);
        file_write($data['file'],$content);
        //返回任务执行的结果
        $server->finish($data['file']."-> OK");
    }

    // 异步任务完成通知 Worker 进程函数
    public function onFinish(\swoole_server $server, $task_id, $data)
    {
        //echo "AsyncTask[{$task_id}] Finish: {$data} \n";
    }

    // 关闭连时回调函数
    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        echo "Close\n";
    }
}