<?php
/*
 * 주기적으로 rss를 확인하여 갱신한다.
 * 첫 항목이 그대로면 저장된 내용을 보내고
 *           저장된 내용과 다르면 새로운 내용을 보내고 갱신한다.
 * 파일자체 권한에 쓰기 권한이 필요하다.
 * rss 저장 파일 폴더도 쓰기 권한이 필요하다.
 * @param string rss 주소
 * @param string rss 저장 파일 - full path
 * @param int 최근 #개까지 - 기본값 4개
 * return array rss의 item 항목
 */

// $rssJson = $_SERVER['DOCUMENT_ROOT'].'/kor/data/blog.json';
// $rssUrl = 'http://blog.rss.naver.com/gokory.xml';
function readRSS($rssUrl, $rssJson, $recent = 4) {

    if (is_file($rssJson)) {
        $rss = json_decode(file_get_contents($rssJson), TRUE);
    } else {
        $rss = array(
            'hour' => '-1',
            'list' => array(
                array('link'=>'')
                )
            );
    }

    // 시간(0~23)마다 rss 내용을 갱신
    if (date('G') != $rss['hour']) {
        $xml = simplexml_load_file($rssUrl, 'SimpleXMLElement', LIBXML_NOCDATA);

        $firstLink = (string)$xml->channel->item->link;
        // 첫 내용이 기존 내용과 다르면 파일 수정
        if ($rss['list'][0]['link'] != $firstLink) {
            $content = array();
            foreach ($xml->channel->item as $entry) {
                $content[] = array(
                    'title' => (string)$entry->title,
                    'link' => (string)$entry->link,
                    'descript' => (string)$entry->description
                    );
            }

            $content = array_slice($content, 0, $recent);

            $rss = array(
                'hour' => date('G'),
                'list' => $content
                );

            file_put_contents($rssJson, json_encode($rss));
        }
    }

    return $rss['list'];
}
