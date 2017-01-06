
                <h3>사용자 정보</h3>

                <div class="row">
                    <div class="col-md-7">
                        <h4>사용자</h4>
                        <div class="table-responsive col-md-offset-1">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th class="col-md-2">아이디</th>
                                        <td class="col-md-4"><?=$m_id?></td>
                                    </tr>
                                    <tr>
                                        <th>이름</th>
                                        <td><?=$m_name?></td>
                                    </tr>
                                    <tr>
                                        <th>이메일</th>
                                        <td><?=$m_email?></td>
                                    </tr>
                                    <tr>
                                        <th>연락처</th>
                                        <td><?=$m_phone?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <button type="button" id="btn-leave" class="btn btn-warning">탈퇴</button>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?=$_SERVER['PHP_SELF']?>?enter=correct&<?=getQuery('enter,idx')?>" class="btn btn-info">수정</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <script>
                $(document).ready(function () {
                    $('#btn-leave').on('click keypress', function () {
                        var msg = "재가입시 관리자에게 요청하셔야 합니다.\n탈퇴하시겠습니까?";
                        if (confirm(msg)) {
                            window.location.href = '<?=$_SERVER['PHP_SELF']?>?enter=leave';
                        }
                    })
                });
                </script>
