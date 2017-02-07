<?php
class CoreView
{
    private static $includes;
    public $base;

    public function __construct()
    {
        $baseKey = "view_manager";
        if (!is_file(BASE_PATH.self::$includes[$baseKey])) {
            
            self::$includes[$baseKey] = APP_PATH.'view/base.html';
        }

        $this->base = self::$includes[$baseKey];
    }

    // 전체 layout을 가지는 master 페이지를 설정한다.
    // 메인페이지, 사이트의 특정 메뉴, 관리자 페이지의 layout을 달리할 수 있다.
    public function setMaster($masterFile, $masterKey) {
        // $masterFile = APP_PATH.'view/'.$masterFile;

        if (!array_key_exists($masterKey, self::$includes)) {
            self::$includes[$masterKey] = '';
        }

        if (is_file(BASE_PATH.$masterFile) && 
                self::$includes[$masterKey] != $masterFile) {
            self::$includes[$masterKey] = $masterFile;
        }

        $this->base = self::$includes[$masterKey];
    }
}
