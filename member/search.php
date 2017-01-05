<?php
include_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'app', 'core', 'config.php'));

$member = loadClass('MemberControl', 'member');
$member->hasID();
