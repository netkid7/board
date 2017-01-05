/*
 * 목록 헤더를 클릭하여 항목별로 정렬
 * jquery, font-awesome 필요
<form name="frmSearch" id="frmSearch" method="GET" class="form-horizontal" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="ord" id="ord" value="" />
    <input type="hidden" name="way" id="way" value="" />
*/
$('th.sort').on('click', function() {
    $('th.sort').not($(this)).map(function() {
        $('.fa', this).removeClass('fa-sort-asc');
        $('.fa', this).removeClass('fa-sort-desc');
        $('.fa', this).addClass('fa-sort');
    });

    $('#ord').val($(this).attr('data-column'));

    if ($('.fa', this).hasClass('fa-sort-desc')) {
        $('.fa', this).removeClass('fa-sort-desc');
        $('.fa', this).addClass('fa-sort-asc');

        $('#way').val('asc');
    } else {
        $('.fa', this).removeClass('fa-sort');
        $('.fa', this).removeClass('fa-sort-asc');
        $('.fa', this).addClass('fa-sort-desc');

        $('#way').val('desc');
    }

    $('#frmSearch').submit();
});