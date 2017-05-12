<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

include_once BASE_PATH.APP_PATH.'auth/authSession.php';

echo '<pre>';
print_r($_SERVER);
print_r($_SESSION);
print_r($_ENV);
echo '</pre>';

// print_r(AuthTool::getAuthBy('brn_auth'));
// print_r($_SESSION['auth']);