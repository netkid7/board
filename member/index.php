<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$member = loadClass('MemberControl', 'member');

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $member->write();
            break;
        case 'mod':
            $member->modify();
            break;
        case 'del':
            $member->remove();
            break;
        case 'login':
            $member->logIn();
            break;
        case 'reg':
            $member->memberWrite();
            break;
        case 'correct':
            $member->memberModify();
            break;
        default:
            $member->index();
    }
} elseif (!empty($_GET['enter'])) {
    switch ($_GET['enter']) {
        case 'v':
            $member->view($_GET['idx']);
            break;
        case 'w':
            $member->write();
            break;
        case 'm':
            $member->modify($_GET['idx']);
            break;
        case 'login':
            $member->logIn();
            break;
        case 'logout':
            $member->logOut();
            break;
        case 'reg':
            $member->memberWrite();
            break;
        case 'info':
            $member->memberView();
            break;
        case 'correct':
            $member->memberModify();
            break;
        case 'leave':
            $member->memberLeave();
            break;
        default:
            $member->dispatcher();
    }
} else {
    $member->dispatcher();
}

