<?php
class CoreView
{
    private static $includes;

    private $fileExt;
    protected $_file_path;
    protected $_view_path;
    public $base;

    public function __construct($loc)
    {
        $baseKey = "view_manager";
        if (!is_file(BASE_PATH.self::$includes[$baseKey])) {
            
            self::$includes[$baseKey] = APP_PATH.'view/base.html';
        }

        $this->base = self::$includes[$baseKey];


        $this->fileExt = array('.php', '.html');

        $this->_file_path = BASE_PATH.APP_PATH."view/{$loc}/";
        $this->_view_path = APP_PATH."view/{$loc}/";
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

    protected function render($page, $data)
    {
        $content_page = NULL;

        foreach ($this->fileExt as $val) {
            if (is_file($this->_file_path.$page.$val)) {
                $content_page = $this->_view_path.$page.$val;
                break;
            }
        }

        if ($content_page) {
            extract($data);
            include_once $this->base;
        } else {
            $page_title = $page;
            include_once APP_PATH.'view/blank.html';
        }
    }
}
