<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

if (empty($_GET['idx'])) {
    popupMsg('파일을 찾을 수 없습니다.');
    exit;
}

$attach = loadClass('AttachControl', 'attach');
$data = $attach->getDownload($_GET['idx']);

$fileName = $data['a_file_name'];
$saveName = $data['a_save_name'];

$filePath = BASE_PATH.UPLOAD_PATH.$saveName;


$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0; rv:11.0') !== false)) {
    // do stuff for IE
    $fileName = iconv('UTF-8', 'euc-kr', $fileName);
}

downloadFile($fileName, $filePath);
