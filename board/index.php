<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$board = loadClass('BoardControl', 'board');
// $board->setMasterPage('app/view/master.html');

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $board->write();
            break;
        case 'mod':
            $board->modify();
            break;
        case 'rep':
            $board->reply();
            break;
        case 'del':
            $board->remove();
            break;
        default:
            $board->index();
    }
} elseif (!empty($_GET['enter'])) {
    switch ($_GET['enter']) {
        case 'v':
            $board->view($_GET['idx']);
            break;
        case 'w':
            $board->write();
            break;
        case 'm':
            $board->modify($_GET['idx']);
            break;
        case 'r':
            $board->reply($_GET['idx']);
            break;
        default:
            $board->index();
    }
} else {
    $board->index();
}

