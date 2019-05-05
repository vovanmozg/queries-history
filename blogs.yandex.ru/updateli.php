<?
//if(date("G")<12) die();
define(ROOT_DIR, preg_replace("%\\\%","/",preg_replace("%[\\\/][^\\\/]*$%","",__FILE__)));
require_once(ROOT_DIR.'/_PEAR/_driver.inc.php');
require_once(ROOT_DIR.'/_PEAR/HTTP/Request.php');

// -------------------------------------
// Теперь надо получить статистику из LiveInternet


	$userAgents = array(
		"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1",
		"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)",
		"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)",
		"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12",
		"Opera/9.50 (Windows NT 5.1; U; ru)",
		"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 WebMoney Advisor",
		"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1",
		"Mozilla/5.0 (Macintosh; U; PPC Mac OS X; es) AppleWebKit/85 (KHTML, like Gecko) Safari/85",
		"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.6) Gecko/20070208 Mandriva/2.0.0.6-12mdv2008.0 (2008.0) Firefox/2.0.0.6"
	);
	$userAgent = $userAgents[array_rand($userAgents)];

// первое - получить проксю
$proxy = trim(file_get_contents("http://vovan.khv.ru/services/proxylist/list.php?sortfield=lastcheck&sortmethod=rand&count=1"));

// второе - скачать страницу со статистикой запросов. Это делается, чтобы получить куки
$a = &new HTTP_Request("http://www.liveinternet.ru/rating/ru/queries.html/");
$a->setProxy($proxy);
$a->setMethod("GET");
$a->addHeader("referer","http://www.liveinternet.ru/rating/ru/queries.html");
$a->addHeader('User-Agent', $userAgent);
$response = $a->sendRequest();
$cookies = $a->getResponseCookies();


file_put_contents("data/liveinternet.ru/out.txt",var_export($a->getResponseBody(),true));

if(is_array($cookies))
{
	foreach($cookies as $cook)
	{
		$a->addCookie($cook["name"],$cook["value"]);
	}	
}

// третье - установить размер страницы в 100. Это нужно для получения нужных куков
$br->setURL("http://www.liveinternet.ru/rating/ru/queries.html?per_page=100&ok=+OK+", array());
$a->addHeader("referer","http://www.liveinternet.ru/rating/ru/queries.html");
$response = $a->sendRequest();
$cookies = $a->getResponseCookies();
if(is_array($cookies))
{
	foreach($cookies as $cook)
	{
		$a->addCookie($cook["name"],$cook["value"]);
	}	
}
$buf = $a->getResponseBody();

file_put_contents("data/liveinternet.ru/".date("Y-m-d")."-1.htm",$buf);
sleep(3);

for($i=2; $i<=10; $i++)
{
	// потом ли page=$i
	$br->setURL("http://www.liveinternet.ru/rating/ru/queries.html?page=2", array());
	$a->addHeader("referer","http://www.liveinternet.ru/rating/ru/queries.html");
	$response = $a->sendRequest();
	$cookies = $a->getResponseCookies();
	if(is_array($cookies))
	{
		foreach($cookies as $cook)
		{
			$a->addCookie($cook["name"],$cook["value"]);
		}	
	}
	$buf = $a->getResponseBody();
	file_put_contents("data/liveinternet.ru/".date("Y-m-d")."-$i.htm",$buf);
	sleep(3);
}


?>