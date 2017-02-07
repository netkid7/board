<?php
/*
 * @param mixed $result 돌려줄 값/집합
 * ajax 요청 결과를 돌려줄 때 사용한다.
 */
function responseOK($result)
{
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/json; charset=UTF-8');
    exit(json_encode($result));
}

function responseError($msg)
{
    $base = array('type' => 'ERROR');
    if (is_array($msg)) {
        $result = array_merge($base, $msg);
    } else {
        $result = array_merge($base, array('message' => $msg));
    }
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json; charset=UTF-8');
    exit(json_encode($result));
}
