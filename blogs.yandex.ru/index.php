<?

if(date("G")<12) die();

define(ROOT_DIR, preg_replace("%\\\%","/",preg_replace("%[\\\/][^\\\/]*$%","",__FILE__)));
//set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_DIR.'/_PEAR');

// dependant files
require_once(ROOT_DIR.'/_PEAR/_driver.inc.php');
require_once(ROOT_DIR.'/_PEAR/HTTP/Request.php');


$istart = 1;
$iend = 5;

$categories = array(
"meeting" => "Знакомства и общение",
"internet" => "Интернет",
"media" => "Новости и СМИ",
"rest" => "Развлечения",
"games" => "Игры",
"sport" => "Спорт",
"humor" => "Юмор",
"cinema" => "Кино",
"software" => "Софт",
"auto" => "Авто",
"goods" => "Товары и услуги",
"house" => "Дом и семья",
"photo" => "Фото",
"communications" => "Связь",
"regions" => "Города и регионы",
"mp3" => "MP3",
"music" => "Музыка",
"computers" => "Компьютеры",
"tourism" => "Путешествия",
"weather" => "Погода",
"education" => "Обучение",
"literature" => "Литература",
"easymoney" => "Бесплатное",
"culture" => "Культура и искусство",
"job" => "Работа",
"health" => "Медицина",
"tv" => "Телевидение",
"finance" => "Финансы",
"society" => "Общество",
"info" => "Справки",
"hosting" => "Хостинг",
"mystery" => "Непознанное",
"construction" => "Строительство",
"realty" => "Недвижимость",
"science" => "Наука и техника",
"homepages" => "Персональные страницы",
"industry" => "Предприятия",
"advertising" => "Реклама",
"security" => "Безопасность",
"politics" => "Политика",
"hi-end" => "Hi-End",
"banks" => "Банки",
"state" => "Государство",
"bookkeeping" => "Бухгалтерия",
"insurance" => "Страхование",
"genealogy" => "Генеалогия",
"parties" => "Политические партии",

);
/*

200.213.20.141:11111
202.28.247.245:80
125.243.145.2:8080
83.211.3.44:80
200.88.223.101:80
*/
while(list($id, $title) = each($categories))
{
	if(file_exists("stop"))
	{
		break;
	}
	
	CreatCurCatStorage($id);
	if(IsNeed($id, 100))
	{
		myLog("Запуск получения страниц категории <b>$title</b>");

		$skoka = 10;
		for($i = 0; $i < $skoka; $i++)
		{
			$pageNum = GetNextPageNum($id);
			$url = "http://pda.liveinternet.ru/stat/ru/$id/queries.html?per_page=100&page=".$pageNum;
			myLog("Обработка страницы $pageNum (<code>$url</code>)");
			$c = getUrl($url);
			SaveResults($id, $pageNum, $c);
		}
		
		//break;
	}
}



/*
for($i=$istart; $i<=$iend; $i++)
{
	$c = getUrl("http://pda.liveinternet.ru/stat/ru/finance/queries.html?per_page=100&page=$i");
	file_put_contents("queries$i.htm", $c);
}

for($i=3000; $i<3010; $i++)
{
	$c = file_get_contents("http://www.liveinternet.ru/stat/ru/queries.html?page=$i");
	file_put_contents("queries$i.htm", $c);
}
*/

//file_put_contents($last,1);

function SaveResults($category, $pageNum, $content)
{
	$dir = ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d");
	file_put_contents("$dir/$pageNum.htm", $content);
}


function GetNextPageNum($category)
{
	$dir = ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d");
	//$last = implode('',file($dir.'/last.dat'));
	$last=1;
	while(file_exists("$dir/$last.htm"))
	{
		$last++;
	}
	return $last;
}

function CreatCurCatStorage($category)
{
	if(!file_exists(ROOT_DIR.'/data/grabbed/'.$category))
	{
		mkdir(ROOT_DIR.'/data/grabbed/'.$category);
		chmod(ROOT_DIR.'/data/grabbed/'.$category, 0777);
	}
		
	if(!file_exists(ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d")))
	{
		mkdir(ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d"));
		chmod(ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d"), 0777);
	}
}

function IsNeed($category, $limit)
{
	$dir = ROOT_DIR.'/data/grabbed/'.$category . '/'.date("Y-m-d");
	//echo $dir;
	$i = -2;
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) { 
			$i++;
		}
		closedir($handle); 
	}
	if($i >= $limit)
	{
		return false;
	}
	return true;
}

function getUrl($url){

	$a = &new HTTP_Request($url);
	$a->setMethod("GET");
	$a->setProxy("200.213.20.141:11111");
	$a->addHeader("referer","http://www.liveinternet.ru/rating/ru/index.html");
	$a->addHeader('User-Agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.0; us; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
//	$a->addHeader("","");
	$a->sendRequest();
	return $a->getResponseBody();
}

function myLog($msg)
{
	//echo "<p>$msg</p>\n";
}

?>