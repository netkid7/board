<?php
class Core
{
    protected $msg;

    public function __construct()
    {
        $this->msg = 'Hello, Core';
    }

    public function viewMsg()
    {
        echo $this->msg.PHP_EOL;
    }

    public function getTable()
    {
        return $this->_table;
    }
}

class Worker extends Core
{
    protected $_table;

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'HelpTable';
    }

    public function viewWorker()
    {
        echo $this->msg.' by worker'.PHP_EOL;
    }
}

$worker = new Worker();
// echo $worker->viewWorker().PHP_EOL;
echo $worker->getTable().PHP_EOL;

$core = new Core();
echo $core->getTable().PHP_EOL;
