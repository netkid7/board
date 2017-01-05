
                <h3>사용자 관리</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>사용자</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>아이디</th>
                                        <td colspan="3"><?=$m_id?></td>
                                    </tr>
                                    <tr>
                                        <th>이름</th>
                                        <td><?=$m_name?></td>
                                        <th>그룹</th>
                                        <td><?=$m_level?></td>
                                    </tr>
                                    <tr>
                                        <th>소속</th>
                                        <td colspan="3"><?=$m_dept?></td>
                                    </tr>
                                    <tr>
                                        <th>연락처</th>
                                        <td><?=$m_phone?></td>
                                        <th>이메일</th>
                                        <td><?=$m_email?></td>
                                    </tr>
                                    <tr>
                                        <th>최근 로그인</th>
                                        <td><?=$m_last_in?></td>
                                        <th>등록일자</th>
                                        <td><?=$m_reg_date?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <button type="button" id="btn-leave" class="btn btn-warning">탈퇴</button>
                                        </td>
                                        <td colspan="2" class="text-right">
                                            <a href="<?=$_SERVER['PHP_SELF']?>?mode=mod&<?=getQuery('mode,idx')?>" class="btn btn-info">수정</a>
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
                            window.location.href = '<?=$_SERVER['PHP_SELF']?>?mode=leave';
                        }
                    })
                });
                </script>
