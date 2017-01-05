<?php
class CoreView
{
    private static $includes;
    public $base;

    public function __construct()
    {
        $baseKey = "view_base";
        if (!is_file(self::$includes[$baseKey])) {
            
            self::$includes[$baseKey] = APP_PATH.'view/base.html';
        }
        $this->base = self::$includes[$baseKey];
    }
}
