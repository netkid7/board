
                <h3>사용자 관리</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>사용자</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th class="col-md-2">아이디</th>
                                        <td class="col-md-4"><?=$m_id?></td>
                                        <th class="col-md-2">상태</th>
                                        <td class="col-md-4"><?=$m_state?></td>
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
                                        <th>메모</th>
                                        <td colspan="3"><?=$m_memo?></td>
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
                                        <?=getAuthButton($auth['a_remove'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'" class="btn btn-warning" data-method="delete" data-idx="'.$m_idx.'" data-url="'.getQuery('enter,idx').'" data-confirm="삭제하시겠습니까?">삭제</a>')?>
                                        <?=getAuthButton($auth['a_modify'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'?enter=m&idx='.$m_idx.'&'.getQuery('enter,idx').'" class="btn btn-info">수정</a>')?>
                                        </td>
                                        <td colspan="2" class="text-right">
                                            <a href="<?=$_SERVER['PHP_SELF']?>?<?=getQuery('enter,idx')?>" class="btn btn-default">목록으로</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <script src="/app/js/dynamicForm.js"></script>
