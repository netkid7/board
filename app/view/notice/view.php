
                <h3>공지사항</h3>

                <div class="row">
                    <div class="col-md-10">
                        <h4>공지사항</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th class="col-md-2">ID</th>
                                        <td class="col-md-4"><?=$n_id?></td>
                                        <th class="col-md-2">email</th>
                                        <td class="col-md-4"><?=$n_email?></td>
                                    </tr>
                                    <tr>
                                        <th>작성자</th>
                                        <td><?=$n_name?></td>
                                        <th>작성일</th>
                                        <td><?=substr($n_reg_date, 0, -3)?></td>
                                    </tr>
                                    <tr>
                                        <th>조회</th>
                                        <td><?=$n_count?></td>
                                        <th>IP</th>
                                        <td><?=$n_reg_IP?></td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td colspan="3"><?=$n_title?></td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td colspan="3"><?=$n_content?></td>
                                    </tr>
                                    <tr>
                                        <th>첨부</th>
                                        <td colspan="3"><?=$n_attach?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><?php
                                    if (($n_password == $_SESSION['_id']) || $auth['auth_admin']) { ?>

                                        <?=getAuthButton($auth['auth_remove'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'" class="btn btn-warning" data-method="delete" data-idx="'.$n_idx.'" data-url="'.getQuery('enter,idx').'" data-confirm="삭제하시겠습니까?">삭제</a>')?>
                                        <?=getAuthButton($auth['auth_modify'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'?enter=m&idx='.$n_idx.'&'.getQuery('enter,idx').'" class="btn btn-info">수정</a>')?><?php
                                    } ?>

                                        </td>
                                        <td colspan="2" class="text-right">
                                        <?=getAuthButton($auth['auth_reply'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'?enter=r&idx='.$n_idx.'&'.getQuery('enter,idx').'" class="btn btn-success">답변</a>')?>

                                            <a href="<?=$_SERVER['PHP_SELF']?>?<?=getQuery('enter,idx')?>" class="btn btn-default">목록으로</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <script src="/app/js/dynamicForm.js"></script>
