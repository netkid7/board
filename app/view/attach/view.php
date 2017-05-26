<?php
$attachImage = array();
$attachFile = array();

$extImage = array("jpg", "png", "bmp", "gif");
foreach ($rows as $key => $val) {
    $ext = strtolower(substr($val['a_save_name'], -3));
    if (in_array($ext, $extImage) ) {
        $attachImage[] = '<img src="/'.UPLOAD_PATH.$val['a_save_name'].'" class="img-responsive" style="width: 100%;" />';
    } else {
        // url 로 직접 접근하는데 대한 조치가 필요하다.
        // 권한이 없는 상태에서 알게된 url을 직접 입력하여 
        // 첨부파일 다운로드할 수 없도록 막아야 한다.
        $attachFile[] = '<a href="/app/download.php?idx='.$val['a_idx'].'">'.$val['a_file_name'].'</a>';
    }
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
