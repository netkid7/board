<?php
$checkSecret = ($n_secret)? 'checked': '';
$checkNotice = ($n_notice)? 'checked': '';

if (empty($n_name)) {
    $n_name = $_SESSION['_name'];
}
?>

                <h3>공지사항</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>공지사항</h4>
                        <form name="frmRegist" id="frmRegist" method="POST" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" id="action" value="<?=$hdnAction?>" />
                            <input type="hidden" name="parent" id="parent" value="<?=$hdnParent?>" />
                            <input type="hidden" name="idx" id="idx" value="<?=$hdnIdx?>" />
                            <input type="hidden" name="url" id="url" value="<?=getQuery('enter, idx')?>" />

                            <div class="form-group">
                                <label class="col-md-2 control-label">작성자</label>
                                <div class="col-md-4">
                                    <input type="text" name="name" id="name" class="form-control" value="<?=$n_name?>" required maxlength="100" />
                                </div>
                                <label class="col-md-2 control-label">email</label>
                                <div class="col-md-3">
                                    <input type="text" name="email" id="email" class="form-control" value="<?=$n_email?>" maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2"></div><?php
                            if ($auth['auth_secret']) { ?>

                                <div class="col-md-2">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="secret" id="secret" value="1" <?=$checkSecret?> /> 비밀글
                                    </label>
                                </div><?php
                            } 

                            if ($auth['auth_notice']) { ?>

                                <div class="col-md-2">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="notice" id="notice" value="1" <?=$checkNotice?> /> 공지
                                    </label>
                                </div><?php
                            } ?>

                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">제목</label>
                                <div class="col-md-6">
                                    <input type="text" name="title" id="title" class="form-control" value="<?=$n_title?>" required maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">내용</label>
                                <div class="col-md-8">
                                    <div><?=$n_parent?></div>
                                    <textarea name="content" id="summercontent" style="display: none;"></textarea>
                                    <div id="summernote"><?=$n_content?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">첨부</label>
                                <div class="col-md-6"><?=$n_attach?></div>
                            </div>
                            <hr />
                            <div class="form-group">
                                <div class="col-md-3 col-md-offset-4">
                                    <button type="submit" class="btn btn-info"><?=$btnAction?></button>
                                    <a href="javascript:history.back();" class="btn btn-default">취소</a>
                                </div>
                                <div class="col-md-1">
                                    <a href="<?=$_SERVER['PHP_SELF']?>?<?=getQuery('enter,idx')?>" class="btn btn-default">목록으로</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                $(document).ready(function () {
                    $('#frmRegist').submit(function (e) {
                        if ($('#title').val().trim().length <= 0) {
                            alert('제목을 입력해 주세요.')
                            $('#title').focus();
                            return false;
                        }

                        if ($('#name').val().trim().length <= 0) {
                            alert('작성자를 입력해 주세요.')
                            $('#name').focus();
                            return false;
                        }

                        $('#summercontent').html($('#summernote').summernote('code'));

                        return true;
                    });
                });
                </script>
