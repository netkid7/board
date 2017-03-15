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
}

class Worker extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function viewWorker()
    {
        echo $this->msg.' by worker'.PHP_EOL;
    }
}

class Single
{
    private static $instance = NULL;

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new Single();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    public function viewSingle()
    {
        echo 'By single'.PHP_EOL;
    }
}

// $parent = new Core();
// $parent->viewMsg();
// var_dump($parent);

// $worker = new Worker();
// $worker->viewWorker();
// var_dump($worker);

// $single = Single::getInstance();
// $single->viewSingle();
// var_dump($single);

// $singleAnother = Single::getInstance();
// var_dump($singleAnother);


function &get_instance_noref() {
    static $obj;

    echo 'Static object: ';
    var_dump($obj);
    if (!isset($obj)) {
        $obj = new stdclass;
    }
    // $obj->property++;
    return $obj;
}

// $obj = get_instance_noref();
// $still_obj = get_instance_noref();
