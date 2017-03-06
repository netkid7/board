<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$comment = loadClass('CommentControl', 'comment');

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $comment->write();
            break;
        case 'mod':
            $comment->modify();
            break;
        case 'rep':
            $comment->rewrite();
            break;
        case 'del':
            $comment->remove();
            break;
        default:
            $comment->index();
    }
} else {
    $comment->index();
}

