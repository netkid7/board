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

// DB 연결정보: coreModel.php 에 있는 connDetails 수정.

set_include_path(realpath(BASE_PATH).PATH_SEPARATOR.get_include_path());

// 게시판 프로그램 설치 폴더
define('APP_PATH', 'app/');
// 첨부파일 등록 폴더
define('UPLOAD_PATH', 'uploads/');
// 일반 콘텐츠 페이지가 있는 폴더
define('PAGE_PATH', 'pages/');

include_once 'class.php'; // __DIR__.'class.php' >= ver 5.3.0
include_once 'func.lib.php';

// if (isset($_SESSION["_id"])) {
// } else {
//     if ($_SERVER['PHP_SELF'] !== '/member/index.php') {
//         header("Location: /member/index.php");
//     }
// }
