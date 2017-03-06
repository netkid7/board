<?php
// 개발시 리턴값 확인용
function echoArray($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    exit();
}

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

/*
 * 권한이 없으면 돌아간다.
 * @param bool 권한 여부
 * history.back()
 */
function checkAuth($hasAuth)
{
    if (!$hasAuth) {
        popupMsg('접근 권한이 없습니다.');
        exit();
    }
}

/*
 * 권한이 있으면 주어진 버튼 태그를 표시한다.
 * @param bool 권한
 * @param string 버튼 태크
 * return bool 권한이 있으면(레벨 <= 세션) TRUE;
 */
function getAuthButton($hasAuth, $strButton)
{
    return ($hasAuth)? $strButton: '';
}

/*
 * 서버에 있는 파일을, 파일이름으로 내보낸다.
 * @param string 다운로드되는 파일 이름
 * @param string 서버에 있는 파일 이름(전체경로 포함)
 * return bool 파일 다운로드
 */
function downloadFile($fileName, $filePath)
{
    if (is_file($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($filePath));

        readfile($filePath);
    } else {
        popupMsg('파일을 찾을 수 없습니다.');
    }
}


/*
 * * 배열 요소 중복이 있는지 확인한다
 * 배열이 너무 크면 에러가 발생한다 by 메모리
 *   cf. return count($arr) !== count(array_unique($arr));
 *   보다 조금(? 0.03초정도) 빠르다.
 *   비슷하게 사용할 수 있는 함수 : array_count_values, array_flip
 * @param array 중복 여부를 확인하려는 배열
 * return bool 중복이 있는지 여부
 */
function hasDuplication($arr)
{
    $dupe = array();
    foreach ($arr as $val) {
        if (array_key_exists($val, $dupe)) {
            return true;
        } else {
            $dupe[$val] = true;
        }
    }

    return false;
}


/*
 * * 배열의 모든 요소를 인코딩변환할 때 array_map()으로 호출한다.
 * @param string UTF-8로 인코딩 하려는 euc-kr 인코딩 문자열
 * return string UTF-8로 인코딩을 변경한 문자열
 */
function convertToUtf8($str)
{
    return iconv('euc-kr', 'UTF-8', $str);
}

/*
 * @param string CSV 파일 경로
 * return array CSV 파일의 내용을 담은 배열
 */
function dataFromCsv($csvfile)
{
    $handle = fopen($csvfile, "r");
    if ($handle) {
        setlocale(LC_CTYPE, "ko_KR.eucKR");

        $rows = array();
        while (($data = fgetcsv($handle, ",")) !== FALSE) {
            if ($data[0]) {
                $rows[] = array_map('convertToUtf8', $data);
            }
        }

        fclose($handle);

        return $rows;
    } else {
        return false;
    }
}

/*
 * 업로드 폴더에 있는 파일을 지운다.
 * @param string 삭제할 파일 이름
 */
function deleteFile($fileName)
{
    $uploadDir = BASE_PATH.UPLOAD_PATH;

    @unlink($uploadDir.$fileName);
}

function uploadMovie($tagName, $allow_file = array("mov", "mpg", "mp4", "avi", "wmv"))
{
    $uploadDir = BASE_PATH.UPLOAD_PATH;

    return uploadFile($tagName, $uploadDir, $allow_file);    
}

function uploadImage($tagName, $allow_file = array("jpg", "png", "bmp", "gif"))
{
    $uploadDir = BASE_PATH.UPLOAD_PATH;

    return uploadFile($tagName, $uploadDir, $allow_file);
}

function uploadImageByDate($tagName)
{
    $allow_file = array("jpg", "png", "bmp", "gif");
    return uploadByDate($tagName, $allow_file);
}

function uploadDocByDate($tagName)
{
    $allow_file = array("txt", "csv", "hwp", "doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf", "zip");
    return uploadByDate($tagName, $allow_file);
}

function uploadByDate($tagName, $allow_file = array())
{
    $uploadDir = BASE_PATH.UPLOAD_PATH;

    return uploadFile($tagName, $uploadDir, $allow_file, 'time');
}

/*
 * 업로드 태그가 배열(name="filename[]")이면 $_FILES 속성값마다 배열로 값을 가진다.
 * 각 속성마다 인덱스의 값으로 업로드 태그 배열을 재구성한다.
 * 재구성한 배열은 새로운 태그(태그이름 + 인덱스 번호)로 할당한다.
 * return array 업로드 처리할 태그 이름
 */
function uploadRearrange()
{
    $uploadNames = array();
    $unsetNames = array();

    foreach ($_FILES as $key => $val) {
        if (is_array($val['name'])) {   // 배열 변수 업로드
            $unsetTag = '';
            for ($i = 0; $i < count($val['name']); $i++) {
                if ($val['name'][$i]) {
                    $file = array(
                        'name' => $_FILES[$key]['name'][$i],
                        'type' => $_FILES[$key]['type'][$i],
                        'tmp_name' => $_FILES[$key]['tmp_name'][$i],
                        'error' => $_FILES[$key]['error'][$i],
                        'size' => $_FILES[$key]['size'][$i]
                    );

                    $_FILES[$key.$i] = $file;
                    $uploadNames[] = $key.$i;

                    $unsetTag = $key;
                }
            }

            if ($unsetTag) {
                $unsetNames[] = $unsetTag;
            }
        } else {
            if ($val['name']) {
                $uploadNames[] = $key;
            }
        }
    }
    
    // foreach ($unsetNames as $val) {
    //     unset($_FILES[$val]);
    // }

    return $uploadNames;
}

/*
 * @param string input 태그의 name 속성 값
 * @param string 파일저장 경로 $uploadDir = realpath(BASE_PATH).'/image/';
 * @param array 허용 파일 확장자 $allow_file = array("jpg", "png", "bmp", "gif");
 * @param string 저장 이름 모양 name: 기존 파일이름, time: 저장시간을 이름으로 사용
 * return array 원본 파일 이름, 저장 파일 이름
 */
function uploadFile($tagName, $uploadDir, $allow_file = array(), $save_filename = 'name')
{
    if (empty($tagName) || empty($uploadDir) || empty($allow_file)) {
        $errLog = 'Fail: required parameters';
        return false;
    }

    $bSuccessUpload = is_uploaded_file($_FILES[$tagName]['tmp_name']);

    // SUCCESSFUL
    $result = '';
    if ($bSuccessUpload) {
        $tmp_name = $_FILES[$tagName]['tmp_name'];
        $name = $_FILES[$tagName]['name'];
        
        $name_element = explode('.', $name);
        $filename_ext = strtolower(array_pop($name_element));
        
        if (!in_array($filename_ext, $allow_file)) {
            $errLog = 'Fail: not supported file type';
            $result = false;
        } else {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777);
            }
            
            // 저장파일 이름: 업로드한 파일이름 또는 저장 시간
            switch ($save_filename) {
                case 'name':
                    $new_file_name = $_FILES[$tagName]['name'];
                    $newPath = $uploadDir.urlencode($new_file_name);
                    break;
                case 'time':
                    $new_file_name = time().'.'.$filename_ext;
                    $newPath = $uploadDir.urlencode($new_file_name);
                    break;
            }

            if (file_exists($newPath)) {
                list($f_name, $f_ext) = explode('.', $new_file_name);

                $i = 1;
                $f_new = $f_name.$i.'.'.$f_ext;
                $newPath = $uploadDir.urlencode($f_new);
                while (file_exists($newPath)) {
                    $i++;
                    $f_new = $f_name.$i.'.'.$f_ext;
                    $newPath = $uploadDir.urlencode($f_new);
                }
            } else {
                $f_new = $new_file_name;
            }
            
            if (move_uploaded_file($tmp_name, $newPath)) {
                $result = $f_new;
            } else {
                $errLog = 'Fail: file save error';
                $result = false;
            }
        }
    } else {
        $errLog = 'Fail: file upload failure';
        $result = false;
    }

    return array('upload_name' => $name, 'save_name' => $result);
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
