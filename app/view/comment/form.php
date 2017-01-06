<?php
foreach ($rows as $key => $val) {
    if ($val['a_idx']) {
        $strAttach = $val['a_file_name'].' <label><input type="checkbox" name="a_idx[]" id="del_attach'.($key + 1).'" value="'.$val['a_idx'].'" /> 삭제</label>';
    } else {
        $strAttach = '';
    } ?>

        <span class="help-block">
            <input type="file" name="attach[]" id="attach<?=($key + 1)?>" />
            <?=$strAttach?>
        </span><?php
    }
    /*
     * submit 시 추가 내용있으면 삭제 체크
     * 삭제 상태에 따라 file 사용 on/off
     */
    ?>
