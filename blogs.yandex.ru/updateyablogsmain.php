<?
//if(date("G")<12) die();
define(ROOT_DIR, preg_replace("%\\\%","/",preg_replace("%[\\\/][^\\\/]*$%","",__FILE__)));
require_once(ROOT_DIR.'/_PEAR/_driver.inc.php');
require_once(ROOT_DIR.'/_PEAR/HTTP/Request.php');


// сначала яндекс блоги
$proxy = trim(file_get_contents("http://vovan.khv.ru/services/proxylist/list.php?count=1"));
$buf = getUrl("http://blogs.yandex.ru",$proxy);

if(strstr($buf,'CoDeeN'))
{
	file("http://vovan.khv.ru/services/proxylist/?action=mark&status=CoDeeN&proxy=".$proxy);
	$buf = getUrl("http://blogs.yandex.ru",$proxy);	
}

if(strstr($buf,'CoDeeN'))
{
	file("http://vovan.khv.ru/services/proxylist/?action=mark&status=CoDeeN&proxy=".$proxy);
	$buf = getUrl("http://blogs.yandex.ru");	
}

file_put_contents("data/blogs.yandex.ru/".date("Y-m-d").".htm",$buf);

function getUrl($url, $proxy = false, $referrer = "")
{
	$a = &new HTTP_Request($url);	
	if($proxy)
	{
		$a->setProxy($proxy);
	}
	$a->setMethod("GET");
	$a->addHeader("referer",$referrer);
	$a->addHeader('User-Agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.0; us; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
	$a->sendRequest();
	return $a->getResponseBody();
}
?>