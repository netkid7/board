<?php
class CommentView
{
    private $fileExt;
    private $_file_path;
    private $_view_path;

    public function __construct()
    {
        $this->fileExt = array('.php', '.html');

        $this->_file_path = BASE_PATH.APP_PATH.'view/comment/';
        $this->_view_path = APP_PATH.'view/comment/';
    }

    public function index($data)
    {
        return $this->render('list', $data);
    }

    public function view($data)
    {
        $this->render('view', $data);
    }

    public function write($data)
    {
        $this->render('form', $data);
    }

    public function modify($data)
    {
        $this->render('form', $data);
    }

    public function remove($data)
    {
        $this->render('remove', '');
    }

    private function render($page, $data)
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
            $page_title = $page;
            include_once APP_PATH.'view/blank.html';
        }
    }
}