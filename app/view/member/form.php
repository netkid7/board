<?php
$checkState = array('y'=>'', 'n'=>'');
if ($m_state) {
    $checkState[$m_state] = 'checked';
} else {
    $checkState['y'] = 'checked';
}

$m_level = ($hdnAction == 'add')? '1': $m_level;
?>
                <h3>사용자 수정</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>사용자</h4>
                        <form name="frmRegist" id="frmRegist" method="POST" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" id="action" value="<?=$hdnAction?>" />
                            <input type="hidden" name="idx" id="idx" value="<?=$hdnIdx?>" />
                            <input type="hidden" name="url" id="url" value="<?=getQuery('enter, idx')?>" />
                            <input type="hidden" name="id_duple" id="id_duple" value="<?=$hdnDupl?>" />

                        <?php
                        if ($hdnAction == 'add') { ?>

                            <div class="form-group">
                                <label class="col-md-2 control-label">아이디</label>
                                <div class="col-md-3">
                                    <input type="text" name="id" id="id" class="form-control" value="<?=$m_id?>" required maxlength="50" />
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-default" id="check_id" data-target="#idModal">중복체크</button>
                                </div>
                                <div class="col-md-6 col-md-offset-2">
                                    <span class="help-block">알파벳으로 시작, 알파벳과 숫자 조합 6~20글자</span>
                                </div>
                            </div><?php
                        } else { ?>

                            <div class="form-group">
                                <label class="col-md-2 control-label">아이디</label>
                                <div class="col-md-3">
                                    <label class="control-label"><?=$m_id?></label>
                                </div>
                            </div><?php
                            } ?>

                            <div class="form-group">
                                <label class="col-md-2 control-label">비밀번호</label>
                                <div class="col-md-4">
                                    <input type="password" name="password" id="password" class="form-control" value="" maxlength="20" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">비밀번호확인</label>
                                <div class="col-md-4">
                                    <input type="password" name="repassword" id="repassword" class="form-control" value="" maxlength="20" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">이름</label>
                                <div class="col-md-4">
                                    <input type="text" name="name" id="name" class="form-control" value="<?=$m_name?>" required maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">그룹</label>
                                <div class="col-md-2">
                                    <select name="level" id="level" class="form-control"><?php
                                    foreach ($level as $key => $val) {
                                        $selected = ($key == $m_level)? 'selected': ''; ?>

                                        <option value="<?=$key?>" <?=$selected?>><?=$val?></option><?php
                                    } ?>

                                    </select>
                                </div>
                                <label class="col-md-1 control-label">상태</label>
                                <div class="col-md-3">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="state" id="state1" value="y" <?=$checkState['y']?> /> 사용
                                        </label>
                                        <label>
                                            <input type="radio" name="state" id="state1" value="n" <?=$checkState['n']?> />미사용
                                        </lable>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">이메일</label>
                                <div class="col-md-4">
                                    <input type="text" name="email" id="email" class="form-control" value="<?=$m_email?>" maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">연락처</label>
                                <div class="col-md-4">
                                    <input type="text" name="phone" id="phone" class="form-control" value="<?=$m_phone?>" maxlength="50" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">메모</label>
                                <div class="col-md-4">
                                    <input type="text" name="memo" id="memo" class="form-control" value="<?=$m_memo?>" maxlength="100" />
                                </div>
                            </div>
                            <hr />
                            <div class="form-group">
                                <div class="col-md-3 col-md-offset-2">
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

                <div class="modal fade" id="idModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-location" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">ID 중복 체크</h4>
                            </div>
                            <div class="modal-body">
                                <p><strong>검색중...</strong> 입력하신 ID를 확인합니다.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <script>
                $(document).ready(function () {
                    $('#check_id').on('keypresss click', function (e) {
                        if (<?=$id_pattern?>.test($('#id').val()) === false) {
                            e.preventDefault();
                            alert('ID를 확인해 주세요.');
                            $('#id').focus();
                            return false;
                        } else {
                            $('#idModal').modal('show');
                        }
                    });

                    $('#idModal').on('show.bs.modal', function (event) {
                        var button = $(event.relatedTarget);

                        var modal = $(this);

                        $.get('/member/search.php', {'id' : $('#id').val()})
                        .done(function (data) {
                            $('#id_duple').val(data.val);
                            modal.find('.modal-body p')
                                .removeClass('alert alert-success alert-warning')
                                .addClass('alert alert-'+ (data.val == '1' ? 'success' : 'warning'))
                                .html('<strong>'+ data.id +'</strong> ' + data.msg);
                        })
                        .fail(function (data) {
                            $('#id_duple').val('0');
                            modal.find('.modal-body p')
                                .addClass('alert alert-danger')
                                .html('중복체크에 실패했습니다. 다시 시도해 주세요.');
                        });
                    });
                    $('#idModal').on('hidden.bs.modal', function (event) {
                        if ($('#id_duple').val() === '1') {
                            $('#password').focus();
                        } else {
                            $('#id').focus();
                        }
                    });

                    $('#frmRegist').submit(function (e) {
                        if ($('#id_duple').val() == '0') {
                            alert('아이디 중복 체크해 주세요.')
                            $('#id').focus();
                            return false;
                        }
                        <?php
                    // 등록할 때는 비밀번호를 받고
                    // 수정하면서 비밀번호를 넣지 않으면 수정하지 않는다.
                    if ($hdnAction == 'add') { ?>
                        // 비밀번호 확인
                        if ($('#password').val().trim().length <= 0) {
                            alert('비밀번호를 입력해 주세요.')
                            $('#password').focus();
                            return false;
                        }<?php
                    } ?>

                        if ($('#password').val() != $('#repassword').val()) {
                            alert('비밀번호가 일치하지 않습니다.')
                            $('#repassword').focus();
                            return false;
                        }

                        if ($('#name').val().trim().length <= 0) {
                            alert('이름을 입력해 주세요.')
                            $('#name').focus();
                            return false;
                        }

                        return true;
                    });
                });
                </script>
