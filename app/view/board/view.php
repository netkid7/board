
                <h3>게시판</h3>

                <div class="row">
                    <div class="col-md-10">
                        <h4>게시판</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th class="col-md-2">ID</th>
                                        <td class="col-md-4"><?=$b_id?></td>
                                        <th class="col-md-2">email</th>
                                        <td class="col-md-4"><?=$b_email?></td>
                                    </tr>
                                    <tr>
                                        <th>이름</th>
                                        <td><?=$b_name?></td>
                                        <th>일자</th>
                                        <td><?=substr($b_reg_date, 0, -3)?></td>
                                    </tr>
                                    <tr>
                                        <th>조회</th>
                                        <td><?=$b_count?></td>
                                        <th>IP</th>
                                        <td><?=$b_reg_IP?></td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td colspan="3"><?=$b_title?></td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td colspan="3"><?=$b_content?></td>
                                    </tr>
                                    <tr>
                                        <th>첨부</th>
                                        <td colspan="3"><?=$b_attach?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><?php
                                    if (($b_password == $_SESSION['_id']) || isAdmin()) { ?>

                                        <?=getAuthButton($auth['a_remove'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'" class="btn btn-warning" data-method="delete" data-idx="'.$b_idx.'" data-url="'.getQuery('enter,idx').'" data-confirm="삭제하시겠습니까?">삭제</a>')?>
                                        <?=getAuthButton($auth['a_modify'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'?enter=m&idx='.$b_idx.'&'.getQuery('enter,idx').'" class="btn btn-info">수정</a>')?><?php
                                    } ?>

                                        </td>
                                        <td colspan="2" class="text-right">
                                        <?=getAuthButton($auth['a_reply'],
                                            '<a href="'.$_SERVER['PHP_SELF'].'?enter=r&idx='.$b_idx.'&'.getQuery('enter,idx').'" class="btn btn-success">답변</a>')?>

                                            <a href="<?=$_SERVER['PHP_SELF']?>?<?=getQuery('enter,idx')?>" class="btn btn-default">목록으로</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <script src="/app/js/dynamicForm.js"></script>
