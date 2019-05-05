<?php
	/**
	Описание: Интерфейс к модулю CURL
	**/
	class Browser {
		var $UA;
		var $PROXY;
		var $HEADERS;
		var $COOKIE;
		
		function Browser($ua = false, $proxy = false, $cookie = 'cookies.txt') {
			if(!$ua) 
				$ua = 'Opera/9.50';
			if($proxy && !ereg('^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\:[0-9]+$', $proxy)) 
				exit('Вы указали следующие данные: ' . $proxy . '. Либо вы сделали ошибку в IP или указали данные в неправильном виде. Правильный вид: ip:port.');
			if(!function_exists('curl_init')) 
				exit('Не установлен модуль CURL');
			
			$this->UA = $ua;
			$this->PROXY = $proxy;
			$this->HEADERS[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg'; 
			$this->HEADERS[] = 'Connection: Keep-Alive'; 
			//$this->HEADERS[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
			$this->COOKIE = $cookie;
		}		
		
		function Get($url = false, $timeout = 30, $referrer = '')
		{
			if(!$url)
			{
				return false; //new CError("Не указан URL: $url",'Browser::Get');
			}

			if(!$referrer)
			{
				//CError::mylog("Не указан Referrer: Browser::Get");
			}
			
			if(is_array($this->HEADERS))
			{
				for($i=0; $i<count($this->HEADERS); $i++)
				{
					if(stripos($this->HEADERS[$i],"referer") !== false)
					{
						$this->HEADERS[$i] = "Referer: $referrer";
						$set = true;
					}
				}
				if(!$set)
				{
					$this->HEADERS[] = "Referer: $referrer";
				}
			}

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->HEADERS); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_USERAGENT, $this->UA);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			if($this->COOKIE)curl_setopt($ch, CURLOPT_COOKIEFILE, $this->COOKIE);
			if($this->COOKIE)curl_setopt($ch, CURLOPT_COOKIEJAR, $this->COOKIE);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			if($this->PROXY) curl_setopt($ch, CURLOPT_PROXY, $this->PROXY);
			$result = curl_exec($ch);
			curl_close($ch); 
			$rez = $result;

			return $rez;
		}		
		
		function Post($url = false, $data = false, $timeout = 30, $referrer = '')
		{
			if(!$url)
			{
				return false;
			}
			
			if(is_array($this->HEADERS))
			{
				for($i=0; $i<count($this->HEADERS); $i++)
				{
					if(stripos($this->HEADERS[$i],"referer") !== false)
					{
						$this->HEADERS[$i] = "Referer: $referrer";
						$set = true;
					}
				}
				if(!$set)
				{
					$this->HEADERS[] = "Referer: $referrer";
				}
			}
			
			if(is_array($data) && count($data)) {
				foreach($data as $field => $value) $post[] = $field . '=' . $value;
				$postdata = implode('&', $post);
			} else {
				$postdata = $data;
			}

			//echo implode('&', $post);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->HEADERS); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_USERAGENT, $this->UA);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			if($this->COOKIE)curl_setopt($ch, CURLOPT_COOKIEFILE, $this->COOKIE);
			if($this->COOKIE)curl_setopt($ch, CURLOPT_COOKIEJAR, $this->COOKIE);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			if($this->PROXY) curl_setopt($ch, CURLOPT_PROXY, $this->PROXY);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POST, 1);
			
			$result = curl_exec($ch); 
			curl_close($ch); 
			$rez = $result;
			return $rez;
		}
		
		function Clear() {
			if($this->COOKIE) {
				$fh = fopen($this->COOKIE, 'w');
				fclose($fh);
			}
		}
		/*
	function saveTempPage($url,$buf,$suffix)
	{	
		$domenHash = CKutuzov::getDomenHash($url);
		file_put_contents($this->RootDir."/temp/$domenHash-$suffix.htm",$buf);
		chmod($this->RootDir."/temp/$domenHash-$suffix.htm",0777);
	}
	*/
		
	}
?>