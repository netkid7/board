<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set("Asia/Seoul");
session_start();

header("Expires:".gmdate("D, d, M Y H:i:s")." GMT");
header("Last-Modified:".gmdate("D, d, M Y H:i:s")." GMT");
header("Cache-Control:no-cache");
header("Pragma:no-cache");
header("Content-Type: text/html; charset= UTF-8"); 


define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('DS', DIRECTORY_SEPARATOR);

set_include_path(realpath(BASE_PATH).PATH_SEPARATOR.get_include_path());

define('APP_PATH', 'app/');
define('UPLOAD_PATH', 'uploads/');
define('PAGE_PATH', 'pages/');

include_once 'class.php'; // __DIR__.'class.php' >= ver 5.3.0
include_once 'func.lib.php';

// if (isset($_SESSION["_id"])) {
// } else {
//     if ($_SERVER['PHP_SELF'] !== '/member/index.php') {
//         header("Location: /member/index.php");
//     }
// }
