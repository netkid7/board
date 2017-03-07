<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$notice = loadClass('NoticeControl', 'notice');

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $notice->write();
            break;
        case 'mod':
            $notice->modify();
            break;
        case 'rep':
            $notice->rewrite();
            break;
        case 'del':
            $notice->remove();
            break;
        default:
            $notice->index();
    }
} elseif (!empty($_GET['enter'])) {
    switch ($_GET['enter']) {
        case 'v':
            $notice->view($_GET['idx']);
            break;
        case 'w':
            $notice->write();
            break;
        case 'm':
            $notice->modify($_GET['idx']);
            break;
        case 'r':
            $notice->rewrite($_GET['idx']);
            break;
        default:
            $notice->index();
    }
} else {
    $notice->index();
}

