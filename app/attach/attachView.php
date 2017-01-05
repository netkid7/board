<?php
class AttachView extends CoreView
{
    private $_file_path;
    private $_view_path;

    public function __construct()
    {
        parent::__construct();
        $this->_file_path = BASE_PATH.APP_PATH.'view/attach/';
        $this->_view_path = APP_PATH.'view/attach/';
    }

    public function index($data)
    {
        return $this->render('list', $data);
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

    public function remove($data)
    {
        return $this->render('remove', '');
    }

    private function render($page, $data)
    {
        if (file_exists($this->_file_path.$page.'.php')) {
            $content_page = $this->_view_path.$page.'.php';
        } elseif (file_exists($this->_file_path.$page.'.html')) {
            $content_page = $this->_view_path.$page.'.html';
        } else {
            $page_title = $page;
            $content_page = APP_PATH.'view/blank.html';
        }

        extract($data);

        ob_start();
        include_once $content_page;
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}