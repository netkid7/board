<?php
class CoreControl
{
    protected $_model;
    protected $_view;

    public function __construct($mv)
    {
        $this->_model = loadClass(ucfirst($mv).'Model', $mv);
        $this->_view = loadClass(ucfirst($mv).'View', $mv);
    }

    public function urlQuery()
    {
        $_GET['page'] = (empty($_GET["page"]))? 1: getPositiveInt($_GET['page']);
        $_GET['s'] = (empty($_GET["s"]))? '': getPositiveInt($_GET["s"]);
        $_GET['k'] = (empty($_GET["k"]))? '': htmlspecialchars($_GET["k"], ENT_QUOTES);

        $get_page = $_GET["page"];
        $get_s = $_GET["s"];
        $get_k = $_GET["k"];

        return compact('get_page', 'get_s', 'get_k');
    }

    public function setMasterPage($masterFile, $masterKey = 'view_open')
    {
        if ($masterKey != 'view_open') {
            $masterKey = 'view_manager';
        }

        $this->_view->setMaster($masterFile, $masterKey);
    }
}
