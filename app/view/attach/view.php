<?php
$attachImage = array();
$attachFile = array();

$extImage = array("jpg", "png", "bmp", "gif");
foreach ($rows as $key => $val) {
    $ext = strtolower(substr($val['a_save_name'], -3));
    if (in_array($ext, $extImage) ) {
        $attachImage[] = '<img src="/'.UPLOAD_PATH.$val['a_save_name'].'" class="img-responsive" style="width: 100%;" />';
    } else {
        $attachFile[] = '<a href="/app/download.php?idx='.$val['a_idx'].'">'.$val['a_file_name'].'</a>';
    }
    // echo "{$val['a_idx']} | {$val['a_parent']} | {$val['a_parent_idx']} | {$val['a_file_name']} | {$val['a_save_name']} <br />";
}
?>

    <div class="clearfix"><?php
    foreach ($attachImage as $val) { ?>
        <div class="col-md-4">
            <div class="thumbnail"><?=$val?></div>
        </div><?php
    } ?>

    </div>
    <div>
        <ul class="nav nav-pills"><?php
        foreach ($attachFile as $val) {
            echo '<li>'.$val.'</li>';
        } ?>

        </ul>
    </div>
