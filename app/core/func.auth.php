<?php
/*
 * 특정 게시판의 부가기능 사용 여부를 확인한다.
 * 설정은 brn_auth 테이블에 있다.
 * 부가기능 on + 부가기능 level
 * @param string 사용하고자 하는 기능(oh/off)
 * @param int 기능을 사용할 수 있는 최소권한
 * 
 */
function hasAble($func, $level)
{
    return (($func == 'y') && hasAuth($level));
}


/*
 * 세션의 레벨값이 최소접근 권한보다 작으면 접근할 수 없다.
 * @param int 최소접근 권한
 * history.back()
 */
function checkAuth($minLevel)
{
    if (empty($_SESSION['_level']) || !hasAuth($minLevel)) {
        popupMsg('접근 권한이 없습니다.');
        exit();
    }
}

/*
 * 특정 권한/레벨을 사용할 수 있는지 학인
 * @param int 권한레벨
 * return bool 권한이 있으면(레벨 <= 세션) TRUE;
 */
function hasAuth($level)
{
    return (isset($_SESSION['_level']) && ($level <= $_SESSION['_level']));
}

function getAuthButton($level, $strButton)
{
    return (hasAuth($level))? $strButton: '';
}

function isAdmin()
{
    // return (isset($_SESSION['_level']) && (9 <= $_SESSION['_level']));
    return hasAuth(9);
}
