<?php

//alert
function popupMsg($msg, $option = "back")
{
    
    if ($option == "back") {
        $opt_str = "history.back(-1);";
    } else {
        $opt_str = $option;
    }

    echo "
    <script type=\"text/javascript\">
    alert('$msg');
    $opt_str
    </script>";
}

// url get query check
function noInject($value, $option = true)
{
    if (empty($value)) {
        $result = $value;
    } else {
        // $result = urlencode(trim($value));

        if ($option == true) {
            $pattern = array("-", ";", "<", ">", "\"", "'","&","javascript","script");
            $value = strip_tags(trim($value));
        } else {
            $pattern = array("--;","javascript","","script");
            $value = trim($value);
        }
        $result = str_ireplace($pattern, "", $value);
    }
    return $result;
}

/*
 * 내용 페이지를 보여준다.
 * @param string 내용 페이지 파일이름
 * @param array 상하단에 필요한 css, js 파일 경로
 * 
 */
function renderPage($page, $data)
{
    if (is_file(BASE_PATH.PAGE_PATH.$page)) {
        $content_page = PAGE_PATH.$page;

        extract($data);
        include_once APP_PATH.'view/base.html';
    } else {
        $page_title = $page;
        include_once APP_PATH.'view/blank.html';
    }
}


function getQuery($unset_key = 'idx')
{
        // page 키값을 제외한 url query 재구성
    $arr_uri = parse_url($_SERVER['REQUEST_URI']);
    if (!empty($arr_uri['query'])) {
        parse_str($arr_uri['query'], $arr_query);
        if (!is_array($unset_key)) {
            $unset_key = preg_split('/,\s*/', $unset_key);
        }
        foreach ($unset_key as $val) {
            unset($arr_query[$val]);
        }

        $url_query = http_build_query($arr_query);
    } else {
        $url_query = '';
    }

    return $url_query;
}

// bootstrap과 함께 사용
function paginate($page_total, $block_size, $page)
{
    // page 키값을 제외한 url query 재구성
    $arr_uri = parse_url($_SERVER['REQUEST_URI']);
    if (!empty($arr_uri['query'])) {
        parse_str($arr_uri['query'], $arr_query);
        unset($arr_query['page']);
        $url_query = http_build_query($arr_query);
    } else {
        $url_query = '';
    }
    $url_self = $_SERVER['PHP_SELF'];


    $page_first = '1';
    $page_last = $page_total;

    $block_start = ((int)ceil($page / $block_size) - 1) * $block_size;
    $block_prev = ($block_start <= 0)? 1: $block_start - $block_size + 1;

    $block_end = $block_start + $block_size;
    $block_next = $block_end + 1;
    if ($block_end > $page_total) {
        $block_end = $page_total;
        $block_next = $block_end;
    }

    $result = "\n";
    if ($block_prev > $block_start) {
        $disabled = "class=\"disabled\"";
    } else {
        $disabled = '';
    }
    $result .= "<li {$disabled}><a href=\"{$url_self}?page={$page_first}&{$url_query}\">&laquo;</a></li>\n";
    $result .= "<li {$disabled}><a href=\"{$url_self}?page={$block_prev}&{$url_query}\">&lsaquo;</a></li>\n";
    for ($i = $block_start + 1; $i <= $block_end; $i++) {
        if ($i == $page) {
            $result .= "<li class=\"active\"><a href=\"#\">$i<span class=\"sr-only\">(current)</span></a></li>\n";
        } else {
            $result .= "<li><a href=\"{$url_self}?page={$i}&{$url_query}\">$i</a></li>\n";
        }
    }
    if ($block_end == $page_total) {
        $disabled = "class=\"disabled\"";
    } else {
        $disabled = '';
    }
    $result .= "<li {$disabled}><a href=\"{$url_self}?page={$block_next}&{$url_query}\">&rsaquo;</a></li>\n";
    $result .= "<li {$disabled}><a href=\"{$url_self}?page={$page_last}&{$url_query}\">&raquo;</a></li>\n";

    return $result;
}
