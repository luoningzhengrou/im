<?php

namespace App\Console\Commands;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class WorkerManCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a WorkerMan server';

    protected $config;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->config = config('workman');
        global $argv;
        if (!in_array($action = $this->argument('action'), ['start', 'stop', 'restart'])){
            $this->error('Error Arguments');
            exit;
        }

        $argv[0] = 'wk';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        $worker = new BusinessWorker();
//        $worker->name = 'BusinessWorker';
        $worker->name = $this->config['business']['name'];
//        $worker->count = 1;
        $worker->count = $this->config['business']['count'];
//        $worker->registerAddress = '127.0.0.1:1236';
        $worker->registerAddress = $this->config['business']['address'];
        $worker->eventHandler = \App\WorkerMan\Events::class;
    }

    private function startGateWay()
    {
//        $gateway = new Gateway('websocket://0.0.0.0:2346');
        $gateway = new Gateway($this->config['gateway']['socket_name']);
        $gateway->name = 'Gateway';
        $gateway->count = 1;
        $gateway->lanIp = '127.0.0.1';
//        $gateway->startPort = 2300;
        $gateway->startPort = $this->config['gateway']['port'];
        $gateway->pingInterval = 55;
        $gateway->pingNotResponseLimit = 1;
        $gateway->pingData = '';
//        $gateway->registerAddress = '127.0.0.1:1236';
        $gateway->registerAddress = $this->config['gateway']['registerAddress'];
    }

    private function startRegister()
    {
        new Register($this->config['register']['socket_name']);
    }
}
