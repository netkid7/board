<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$result = uploadImageByDate('image');
echo '/'.UPLOAD_PATH.$result['save_name'];
