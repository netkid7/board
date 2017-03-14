<?php
class AuthView extends CoreView
{
    public function __construct()
    {
        parent::__construct('auth');
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

        $this->render('form', $data);
    }

    public function modify($data)
    {
        $data['cssLink'] = array();
        $data['scriptHeader'] = array();
        $data['scriptFooter'] = array();

        $this->render('form', $data);
    }

    public function remove($data)
    {
        $this->render('remove', '');
    }
}