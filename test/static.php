<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

include_once BASE_PATH.APP_PATH.'auth/authSession.php';

$_SERVER['auth'] = array(
    'table' => 'brn_board',
    'readable' => true
    );

$_ENV['test'] = 'foo';

$_SESSION['auth'] = array(
    'table' => 'brn_board',
    'readable' => true
    );

echo '<pre>';
print_r($_SERVER);
print_r($_SESSION);
print_r($_ENV);
echo '</pre>';

// echo '<pre>';
// print_r($_SESSION['auth']);
// print_r(AuthTool::getAuthBy('brn_auth'));
// print_r(AuthTool::getAuthBy('brn_board'));
// print_r(AuthTool::getAuthBy('brn_auth'));
// print_r($_SESSION['auth']);
// echo '</pre>';
// unset($_SESSION['auth']);