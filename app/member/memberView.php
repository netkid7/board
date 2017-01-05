<?php
class MemberView extends CoreView
{
    private $_file_path;
    private $_view_path;

    public function __construct()
    {
        parent::__construct();
        $this->_file_path = BASE_PATH.APP_PATH.'view/member/';
        $this->_view_path = APP_PATH.'view/member/';
    }

    public function page($fileName, $data)
    {
        $this->render($fileName, $data);
    }

    public function memberView($data)
    {
        // $data['cssLink'] = array('/css/bootstrap/bootstrap.min.css');
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();
        $this->render('memberView', $data);
    }

    public function memberWrite($data)
    {
        $data['cssLink'] = array('/app/css/hide_left.css');
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $data['btnAction'] = '등록';
        $this->render('memberForm', $data);
    }

    public function memberModify($data)
    {
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $data['btnAction'] = '수정';
        $this->render('memberForm', $data);
    }    
    public function index($data)
    {
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $this->render('list', $data);
    }

    public function view($data)
    {
        // $data['cssLink'] = array('/css/bootstrap/bootstrap.min.css');
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();
        $this->render('view', $data);
    }

    public function write($data)
    {
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $data['btnAction'] = '등록';
        $this->render('form', $data);
    }

    public function modify($data)
    {
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $data['btnAction'] = '수정';
        $this->render('form', $data);
    }

    public function remove($data)
    {
        $this->render('remove', '');
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

        include_once $this->base;
    }
}