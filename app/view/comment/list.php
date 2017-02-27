<?php
?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <div>
                                총 <?=number_format($total_count)?> 건
                                현재 <?=number_format($get_page)?> / <?=number_format($total_page)?> 페이지
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="col-md-1">No.</th>
                                        <th class="col-md-8">내용</th>
                                        <th class="col-md-1">작성자</th>
                                        <th class="col-md-2">작성일</th>
                                    </tr>
                                </thead>
                                <tbody><?php
                                foreach ($rows as $val) {
                                    $row_no += 1;
                                    $idx = $val['c_idx'];
                                    if ($val['c_depth'] > 0) {
                                        $indent = str_repeat('&nbsp;&nbsp;', $val['c_depth']).'<i class="fa fa-angle-right"></i> ';
                                    } else {
                                        $indent = '';
                                    }
                                    $regDate = date('Y-m-d', strtotime($val['c_reg_date'])); ?>

                                    <tr>
                                        <td><?=$row_no?></td>
                                        <td class="ellipsis-title"><?=$indent?><?=$val['c_content']?></td>
                                        <td class="ellipsis-name"><?=$val['c_name']?></td>
                                        <td><?=$regDate?></td>
                                    </tr><?php
                                } ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <ul class="pagination">
                                                <?=paginate($total_page, 5, $get_page)?>

                                            </ul>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
