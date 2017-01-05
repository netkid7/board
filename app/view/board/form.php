<?php
$checkSecret = ($b_secret)? 'checked': '';
$checkNotice = ($b_notice)? 'checked': '';

if (empty($b_name)) {
    $b_name = $_SESSION['_name'];
}
?>

                <h3>작업 관리</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>작업</h4>
                        <form name="frmRegist" id="frmRegist" method="POST" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" id="action" value="<?=$hdnAction?>" />
                            <input type="hidden" name="parent" id="parent" value="<?=$hdnParent?>" />
                            <input type="hidden" name="idx" id="idx" value="<?=$hdnIdx?>" />
                            <input type="hidden" name="url" id="url" value="<?=getQuery('enter, idx')?>" />

                            <div class="form-group">
                                <label class="col-md-2 control-label">작성자 이름</label>
                                <div class="col-md-4">
                                    <input type="text" name="name" id="name" class="form-control" value="<?=$b_name?>" required maxlength="100" />
                                </div>
                                <label class="col-md-2 control-label">작성자 email</label>
                                <div class="col-md-3">
                                    <input type="text" name="email" id="email" class="form-control" value="<?=$b_email?>" maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2"></div><?php
                            if (hasAuth($auth['a_secret']) && ($auth['f_secret'] == 'y')) { ?>

                                <div class="col-md-2">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="secret" id="secret" value="1" <?=$checkSecret?> /> 비밀글
                                    </label>
                                </div><?php
                            } 

                            if (hasAuth($auth['a_notice']) && ($auth['f_notice'] == 'y')) { ?>

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
                                    <input type="text" name="title" id="title" class="form-control" value="<?=$b_title?>" required maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">내용</label>
                                <div class="col-md-8">
                                    <div><?=$b_parent?></div>
                                    <textarea name="content" id="summercontent" style="display: none;"></textarea>
                                    <div id="summernote"><?=$b_content?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">첨부</label>
                                <div class="col-md-6"><?=$b_attach?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">처리단계</label>
                                <div class="col-md-8"><?=$b_task?></div>
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

                <div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-location" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">회원 검색</h4>
                            </div>
                            <div class="modal-body">
                                <p><strong>검색중...</strong> 입력하신 회원을 찾습니다.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <script>
                $(document).ready(function () {
                    // 폼에 검색어 입력란과 버튼을 추가하세요.
                    // 수신회원 지정해야할 때 사용할 예정입니다.
                    $('#search_member').on('keypresss click', function (e) {
                        if ($.trim($('#search').val()).length <= 0) {
                            e.preventDefault();
                            alert('회원 검색어를 입력하세요.');
                            $('#search').focus();
                            return false;
                        } else {
                            $('#memberModal').modal('show');
                        }
                    });

                    $('#memberModal').on('show.bs.modal', function (event) {
                        var button = $(event.relatedTarget);
                        // var ask_action = button.data('action');

                        var modal = $(this);
                        modal.find('.modal-body').load('/member/search.php?k='+ encodeURI($('#search').val()));
                        // modal.find('.modal-title').text(button.data('title'));
                        // modal.find('.btn-info').text(button.text());
                    });
                    $('#memberModal').on('hidden.bs.modal', function (event) {
                        // if (/^\d+$/.test($('#lno').val()) === true) {
                        //     $('#lati').focus();
                        // } else {
                        //     $('#search').focus();
                        // }
                    });

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

                        if (($('#parent').val() != '') && ($('input[name="step"]').is(':checked') == false)) {
                            alert('처리단계를 선택해 주세요.')
                            $('input[name="step"]').eq(0).focus();
                            return false;
                        }

                        $('#summercontent').html($('#summernote').summernote('code'));

                        return true;
                    });
                });
                </script>
