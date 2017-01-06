                <h3>로그인</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>로그인</h4>
                        <form name="frmLogin" id="frmLogin" method="POST" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" id="action" value="<?=$hdnAction?>" />
                            <input type="hidden" name="url" id="url" value="<?=getQuery('enter, idx')?>" />

                            <div class="form-group">
                                <label class="col-md-2 control-label">아이디</label>
                                <div class="col-md-4">
                                    <input type="text" name="id" id="id" class="form-control" value="" required maxlength="20" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">비밀번호</label>
                                <div class="col-md-4">
                                    <input type="password" name="password" id="password" class="form-control" value="" required maxlength="20" />
                                </div>
                            </div>
                            <hr />
                            <div class="form-group">
                                <div class="col-md-2 col-md-offset-1">
                                    <a href="/member/index.php?enter=reg" class="btn btn-default">회원가입</a>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <button type="submit" class="btn btn-info"><?=$btnAction?></button>
                                    <a href="javascript:history.back();" class="btn btn-default">취소</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                $(document).ready(function () {
                    $('#frmLogin').submit(function (e) {
                        if (<?=$id_pattern?>.test($('#id').val()) === false) {
                            alert('ID를 확인해 주세요.');
                            $('#id').focus();
                            return false;
                        }

                        // 비밀번호 확인
                        if ($('#password').val().trim().length <= 0) {
                            alert('비밀번호를 입력해 주세요.')
                            $('#password').focus();
                            return false;
                        }

                        return true;
                    });
                });
                </script>
