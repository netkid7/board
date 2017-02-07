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

    public function setMasterPage($masterFile, $masterKey = 'view_open')
    {
        if ($masterKey != 'view_open') {
            $masterKey = 'view_manager';
        }

        $this->_view->setMaster($masterFile, $masterKey);
    }
}
