<?php
class AuthView extends CoreView
{
    public function __construct()
    {
        parent::__construct('auth');
    }

    public function index($data)
    {
        $this->render('list', $data);
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
}