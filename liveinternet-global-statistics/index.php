<?php
header('content-type:text/html; charset=UTF-8');
require_once('browser.class.php');
include('phpQuery.php');

$browser = new Browser(ua(), false, 'cookie.txt');

grab();


/* Главная функция
*/
function grab() {
	global $browser;
	$url = 'http://www.liveinternet.ru/rating/ru/index.html';
	get($url, 10);
	$referer = $url;
	$url = 'http://www.liveinternet.ru/stat/ru/queries.html?id=24957&id=25383&id=25148&id=25390&id=25683&per_page=100&ok=+OK+';
	$response = get($url, 10, $referer);
	$referer = $url;
	fsave(fname(), $response);
	for($page = 2; $page <= 10; $page++) {
		$url = 'http://www.liveinternet.ru/stat/ru/queries.html?id=24957;id=25383;id=25148;id=25390;id=25683;page=' . $page;
		$response = get($url, 10, $referer);
		$referer = $url;
		fsave(fname($page), $response);
	}
	$browser->Clear();
}

/* Сохранить скачанную страницу и сразу распарсенную тоже
*/
function fsave($fname, $content) {
	$fname_txt = preg_replace('/htm$/', 'txt', $fname);
	file_put_contents($fname, $content);
	chmod($fname, 0777);
	$parsed = parse_page($content);  print_r($parsed);
	file_put_contents($fname_txt, $parsed);
	chmod($fname_txt, 0777);
}

/* Распарсить страницу
*/
function parse_page($content) {
  $doc = phpQuery::newDocument($content);
  phpQuery::selectDocument($doc);
  
  //$page['content'] = $content; 
  
  $rows = pq("table:contains('0.0%') tr]");
  
  foreach($rows as $row) {
  	$keyword = trim(pq($row)->find('td:eq(1) a')->text());
  	$keywords[] = $keyword;
  }

  return implode("\n", $keywords);
}

/* Сгенерировать имя файла
*/
function fname($i = 1, $type = 'htm') {
	mkdir('data/' . date('Y-m'));
	chmod('data/' . date('Y-m'), 0777);
	mkdir('data/' . date('Y-m') . '/' . date('Y-m-d'));
	chmod('data/' . date('Y-m') . '/' . date('Y-m-d'), 0777);
	return 'data/' . date('Y-m') . '/' . date('Y-m-d') . '/' . date('Y-m-d').'-'.$i.'.'.$type;
}

//Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
//Accept-Charset:windows-1251,utf-8;q=0.7,*;q=0.3
//Accept-Encoding:gzip,deflate,sdch
//Accept-Language:ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4
//Connection:keep-alive
//Host:www.liveinternet.ru
//User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.97 Safari/537.22

/* Скачать страницу. Если есть в кеше, не качать
*/
function get($url, $timeout = 10, $referer = false) {
	global $browser;
	$cache_file = 'cache/' . md5($url . date('Y-m-d'));
	if(file_exists($cache_file)) {
		return file_get_contents($cache_file);
	}
	if (!file_exists('cache')) mkdir('cache');
	$content = $browser->Get($url, $timeout, $referer);
	file_put_contents($cache_file, $content);
	return $content;
}

// Метод  генерации Юзерагента
function ua() { 
	$UserAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.97 Safari/537.22';
	return $UserAgent;
} 


function myLog($msg)
{
	//echo "<p>$msg</p>\n";
}

?>