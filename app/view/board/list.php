<?php
$selected = array(0=>'', '', '', '');
if (empty($get_s)) {
    $selected['0'] = 'selected';
} else {
    $selected[$get_s] = 'selected';
}
?>

                <h3>작업 관리</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>작업</h4>
                        <div class="row">
                            <form name="frmSearch" id="frmSearch" method="GET" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>">
                                <input type="hidden" name="bat" id="bat" value="" />
                                <div class="col-md-2 col-md-offset-4">
                                    <select name="s" id="searchSelect" class="form-control" tabindex="1">
                                        <option value="0" <?=$selected['0']?>>검색대상</option>
                                        <option value="1" <?=$selected['1']?>>제목</option>
                                        <option value="2" <?=$selected['2']?>>내용</option>
                                        <option value="3" <?=$selected['3']?>>작성자</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="k" id="searchWord" placeholder="검색" class="input form-control" tabindex="2" value="<?=$get_k?>">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary" tabindex="3"> <i class="fa fa-search"></i> 검색</button>
                                            <a class="btn btn-default" href="<?=$_SERVER['PHP_SELF']?>"> 리스트</a>
                                            <?=getAuthButton($auth['a_write'],
                                                '<a href="'.$_SERVER['PHP_SELF'].'?enter=w&'.getQuery('enter','idx').'" class="btn btn-info"> <i class="fa fa-plus"></i> 추가</a>')?>

                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
                                        <th class="col-md-6">제목</th>
                                        <th class="col-md-1">작성</th>
                                        <th class="col-md-2">작성일</th>
                                        <th class="col-md-1">조회</th>
                                        <th class="col-md-1">처리</th>
                                    </tr>
                                </thead>
                                <tbody><?php
                                foreach ($rows as $val) {
                                    $row_no += 1;
                                    $idx = $val['b_idx'];
                                    if ($val['b_depth'] > 0) {
                                        $indent = str_repeat('&nbsp;&nbsp;', $val['b_depth']).'<i class="fa fa-angle-right"></i> ';
                                    } else {
                                        $indent = '';
                                    }
                                    $regDate = date('Y-m-d', strtotime($val['b_reg_date']));
                                    if (array_key_exists($val['t_step'], $step)) {
                                        $str_step = $step[$val['t_step']];
                                    } else {
                                        $str_step = '-';
                                    }
                                    ?>

                                    <tr>
                                        <td><?=$row_no?></td>
                                        <td class="ellipsis-base ellipsis-title"><?=$indent?><a href="<?=$_SERVER['PHP_SELF']?>?enter=v&idx=<?=$idx?>&<?=getQuery()?>"><?=$val['b_title']?></a></td>
                                        <td class="ellipsis-base ellipsis-name"><?=$val['b_name']?></td>
                                        <td><?=$regDate?></td>
                                        <td><?=$val['b_count']?></td>
                                        <td><?=$str_step?></td>
                                    </tr><?php
                                } ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-center">
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
