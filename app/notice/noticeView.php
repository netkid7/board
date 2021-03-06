<?php
class NoticeView extends CoreView
{
    public function __construct()
    {
        parent::__construct('notice');
    }

    public function page($fileName, $data)
    {
        $this->render($fileName, $data);
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
        $data['cssLink'] = array('/js/summernote/summernote.css');
        $data['scriptHeader'] = array('/js/summernote/summernote.min.js', '/js/summernote/lang/summernote-ko-KR.js');
        $data['scriptFooter'] = array('/js/summernote/summernote.config.js');

        $data['btnAction'] = '등록';
        $this->render('form', $data);
    }

    public function modify($data)
    {
        $data['cssLink'] = array('/js/summernote/summernote.css');
        $data['scriptHeader'] = array('/js/summernote/summernote.min.js', '/js/summernote/lang/summernote-ko-KR.js');
        $data['scriptFooter'] = array('/js/summernote/summernote.config.js');

        $data['btnAction'] = '수정';
        $this->render('form', $data);
    }

    public function remove($data)
    {
        $this->render('remove', '');
    }
}