<?
//if(date("G")<12) die();
define(ROOT_DIR, preg_replace("%\\\%","/",preg_replace("%[\\\/][^\\\/]*$%","",__FILE__)));
require_once(ROOT_DIR.'/_PEAR/_driver.inc.php');
require_once(ROOT_DIR.'/_PEAR/HTTP/Request.php');


// сначала €ндекс блоги
$count= 10;
while(($count-- > 0) && !getUrl("http://blogs.yandex.ru/rating/requests/",true,"http://blogs.yandex.ru/"))
{
	flush();
	if(is_file('stop')) break;
}


function getUrl($url, $useProxy = false, $referrer = "")
{
	$a = &new HTTP_Request($url);
	if($useProxy)
	{
		$proxy = trim(file_get_contents("http://vovan.khv.ru/services/proxylist/list.php?sortfield=lastcheck&sortmethod=rand&count=1"));
		$a->setProxy($proxy);
	}
	
	echo "ѕопытка скачать с помощью $proxy<br>";
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
	
	$a->setMethod("GET");
	$a->addHeader("referer",$referrer);
	$a->addHeader('User-Agent', $userAgent);
	$a->sendRequest();
	
$cookies = $a->getResponseCookies();
if(is_array($cookies))
{
	foreach($cookies as $cook)
	{
		$a->addCookie($cook["name"],$cook["value"]);
	}	
}	
	
	$buf = $a->getResponseBody();
	
	file_put_contents("data/blogs.yandex.ru-rating/".date("Y-m-d-H").".htm",$buf);
	if(preg_match("|blogs.yandex.ru/search.xml|",$buf))
	{
		echo "страница рейтинга яндекса идентифицирована <br>";
		return true;
	}
	else
	{
		echo "не удалось идентифицировать страницу рейтинга яндекса<br>";
		if(strstr($buf,'CoDeeN'))
		{
			file_get_contents("http://vovan.khv.ru/services/proxylist/?action=mark&status=CoDeeN&proxy=".$proxy);
		}
		else
		{
			file_get_contents("http://vovan.khv.ru/services/proxylist/?action=mark&status=bad&proxy=".$proxy);
		}
		return false;
	}
}
?>