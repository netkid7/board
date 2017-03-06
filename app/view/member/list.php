<?php
$selected = array(0=>'', '', '');
if (empty($get_s)) {
    $selected['0'] = 'selected';
} else {
    $selected[$get_s] = 'selected';
}
?>

                <h3>사용자 관리</h3>

                <div class="row">
                    <div class="col-md-12">
                        <h4>사용자</h4>
                        <div class="row">
                            <form name="frmSearch" id="frmSearch" method="GET" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>">
                                <input type="hidden" name="bat" id="bat" value="" />
                                <div class="col-md-2 col-md-offset-4">
                                    <select name="s" id="searchSelect" class="form-control" tabindex="1">
                                        <option value="0" <?=$selected['0']?>>검색대상</option>
                                        <option value="1" <?=$selected['1']?>>이름</option>
                                        <option value="2" <?=$selected['2']?>>아이디</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="k" id="searchWord" placeholder="검색" class="input form-control" tabindex="2" value="<?=$get_k?>">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary" tabindex="3"> <i class="fa fa-search"></i> 검색</button>
                                            <a class="btn btn-default" href="<?=$_SERVER['PHP_SELF']?>"> 리스트</a>
                                            <?=getAuthButton($auth['auth_write'],
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
                                총 <?=number_format($total_count)?> 명
                                현재 <?=number_format($get_page)?> / <?=number_format($total_page)?> 페이지
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="col-md-4">ID</th>
                                        <th class="col-md-2">이름</th>
                                        <th>그룹</th>
                                        <th>타입</th>
                                        <th>상태</th>
                                        <th class="col-md-2">최근 로그인</th>
                                    </tr>
                                </thead>
                                <tbody><?php
                                foreach ($rows as $val) {
                                    $row_no += 1;
                                    $idx = $val['m_idx'];
                                    $strState = ($val['m_state'] == 'y')? '사용': '미사용';
                                    $strLevel = $level[$val['m_level']];
                                    $lastIn = ($val['m_last_in'])? date('Y-m-d H:i', strtotime($val['m_last_in'])): ''; ?>

                                    <tr>
                                        <td><?=$row_no?></td>
                                        <td><a href="<?=$_SERVER['PHP_SELF']?>?enter=v&idx=<?=$idx?>&<?=getQuery()?>"><?=$val['m_id']?></a></td>
                                        <td class="ellipsis-name ellipsis-name-l"><?=$val['m_name']?></td>
                                        <td><?=$strLevel?></td>
                                        <td><?=$val['m_type']?></td>
                                        <td><?=$strState?></td>
                                        <td><?=$lastIn?></td>
                                    </tr><?php
                                } ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8" class="text-center">
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
