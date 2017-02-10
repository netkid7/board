<?php
class AttachView
{
    private $fileExt;
    private $_file_path;
    private $_view_path;

    public function __construct()
    {
        $this->fileExt = array('.php', '.html');

        $this->_file_path = BASE_PATH.APP_PATH.'view/attach/';
        $this->_view_path = APP_PATH.'view/attach/';
    }

    public function view($data)
    {
        return $this->render('view', $data);
    }

    public function write($data)
    {
        return $this->render('form', $data);
    }

    public function modify($data)
    {
        return $this->render('form', $data);
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

            ob_start();
            include_once $content_page;
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        } else {
            return "$page 페이지를 찾을 수 없습니다.";
        }
    }
}