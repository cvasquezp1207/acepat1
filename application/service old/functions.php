<?php

if ( ! function_exists('clearfix')) {
	function clearfix($string) {
		$unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 
			'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 
			'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 
			'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		$string = strtr($string, $unwanted_array);
		
		$string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);
		$string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);
		$string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
		
		return $string;
	}
}

if ( ! function_exists('download_page')) {
	function download_page($url, $header = false) {
		if($header !== false) {
			if(is_array($header)) {
				$context = stream_context_create($header);
				// return file_get_contents($url, false, $context, -1);
				return file_get_contents($url, false, $context);
			}
		}
		
		return file_get_contents($url, false, null, -1);
	}
}

if ( ! function_exists('download')) {
	function download($url, $mobile = 0, $header = '', $method = 'GET') {
		if ($mobile == 0) {
			$user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20120427 Firefox/15.0a1';
		} else {
			$user_agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ARM; Touch; NOKIA; Lumia 920)';
		}
		if ($header == '') {
			$options = array('http' => array(
					'method' => $method,
					'header' => 'Content-type: text/html;charset=UTF-8' . PHP_EOL .
					'User-Agent: ' . $user_agent
			));
		} else {
			$options = array('http' => array(
					'method' => $method,
					'header' => $header
			));
		}
		$config = stream_context_create($options);
		$page = file_get_contents('' . $url . '', false, $config);
		return $page;
	}
}

if ( ! function_exists('download_curl')) {
	function download_curl($url, $mobile = 0,$time=20) {
		if ($mobile == 0) {
			$user_agent = 'Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.125 Safari/537.36';
		} else {
			$user_agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ARM; Touch; NOKIA; Lumia 920)';
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time);
		curl_setopt($ch, CURLOPT_TIMEOUT, $time);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return ($httpcode >= 200 && $httpcode < 300) ? $data : false;
	}
}

if ( ! function_exists('socket_post')) {
	function socket_post($url, $data) {
		$parseUrl = parse_url($url);
		
		$port = 80;
		$host = $parseUrl["host"];
		if( ! empty($parseUrl["port"])) {
			$port = $parseUrl["port"];
			$host .= ":".$parseUrl["port"];
		}
		
		$socket = fsockopen($parseUrl["host"], $port, $errno, $errstr, 30);
		 
		if( ! $socket) {
			// return 'Error: ' . $errno . ' ' . $errstr;
			return false;
		}
		else {
			// stream_set_blocking($socket, 0);
			
			$http  = "POST " . $parseUrl["path"] . " HTTP/1.1\r\n";
			$http .= "Host: " . $host . "\r\n";
			$http .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
			$http .= "Accept: application/json, text/javascript, */*; q=0.01\r\n";
			$http .= "Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3\r\n";
			$http .= "Accept-Encoding: gzip, deflate, br\r\n";
			$http .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$http .= "X-Requested-With: XMLHttpRequest\r\n";
			$http .= "Content-length: " . strlen($data) . "\r\n";
			$http .= "Cookie: f5_cspm=1234; _ga=GA1.3.1274839380.1483143166; ITMENUSSOSESSION=bPT1Y2lJvcNnZt1mvGrq1gcM8SThxk5TZWmDQxyCkZPXNpdCY1gkdqPppHLLykPhy27Bj9fdQ0tLZNnd162KT2XvwDYYSH6sNQK2qJhYRMhpXJGnxJGQm7ZqTccpLmz1BPgFhnR2QXY7GF2LZxTXxPHf25YXJYhZ0y1JpTT78MPspmhmyD3Pn4QPrct2p8QL2syrplHn2lf715T21ppyW0jJsJ6HvwKxTs1LQL0MGvFFgJy91WNCwv4qQGsJYkMp!-30262575!-859765849; TS017d3b71=019edc9eb8a8ed13b9187daf511d32a482f72e91562e671f5f03f45bee710edca2ae67e112205cdf57a6f715adc703d5fdf14fc3cd0f00596aee7b7c36b865e1d482e546d2e6cc5736d9b1d5056ab2d19cec5f5e919723d5bb9dd0f935033c23793ea30ce1ef7dbc5893ae1f7a58e9d658c6c8a1c7ea4e14c2cbff7c108ca97ba0a17805c533a4541edab796a89fbb00ea42cbea13fbce48607ef8bbfd6836d28d22031fc65745ce083ccbdc00310c43531962559cd450290448f33c386393edd2dd8ae22e; f5avrbbbbbbbbbbbbbbbb=HOMLJAOLKKCANNFMALBCDHEBHMBHJHONDCBODKCEOCNDEKMECMHMLJBNBNKLKGCCJGMDPNIEKDMLPEELKIKAGDHJFKFMDCECCCOHPBIFJCDGGACMOILHEOKOEFMFILAC; S10453004100HILLUBEA=1; 10453004100HILLUBEA=1; IARECIBOELECTRONICOSESSION=NTm7Y2kFLJ2LJFgbhns0HWYPg2CvLNWvkWrMRvG5JjvMNlytNgBbBxrLGxvRLRFv4JlTyk2WFQhNJM2dLXJgvqN6hbvndtWbpHm0xh03xcGJhJ7RLhYyGJjnHb1zJmZ1vTjpdY23X1bPgd6qyG4nhhcRysF19cHRXnNxwTVXnQthqxjnGmmvGYlxHfm1sQFLjvMpyvhnG2bgY0Qlnc0WXRNYp6D1NNXK6wpJMH1Tvn3YCSMGTqSGHZcJpw7F0zKz!-1328803404!-983062877; TS0192bf31=019edc9eb88660697e7fbc5045b05b3c10d0c5910e656abb8fc3598d002e355a2ae7556f806b548fff811105cfd0c0fb221426d51e2978d2f6b3837a6f3c1109e90668a0f6f70412120028659dbd0324f6b9a9479e8d529ca46c6d43e198a38b32b8a72d23; SOLIDXS=rO0ABXNyAC1wZS5nb2Iuc3VuYXQudGVjbm9sb2dpYS5tZW51LmJlYW4uVXN1YXJpb0JlYW6pwYmu/JtYOAIAFFMAB25pdmVsVU9MAAphcGVNYXRlcm5vdAASTGphdmEvbGFuZy9TdHJpbmc7TAAKYXBlUGF0ZXJub3EAfgABTAAHY29kQ2F0ZXEAfgABTAAJY29kRGVwZW5kcQB+AAFMAAxjb2RUT3BlQ29tZXJxAH4AAUwABWNvZFVPcQB+AAFMAAZjb3JyZW9xAH4AAUwAB2Rlc0NhdGVxAH4AAUwABWRlc1VPcQB+AAFMAAJpZHEAfgABTAAJaWRDZWx1bGFycQB+AAFMAAVsb2dpbnEAfgABTAADbWFwdAAPTGphdmEvdXRpbC9NYXA7TAAObm9tYnJlQ29tcGxldG9xAH4AAUwAB25vbWJyZXNxAH4AAUwAC25yb1JlZ2lzdHJvcQB+AAFMAAZudW1SVUNxAH4AAUwABnRpY2tldHEAfgABTAAKdXN1YXJpb1NPTHEAfgABeHIAEGphdmEubGFuZy5PYmplY3QAAAAAAAAAAAAAAHhwAAB0AABxAH4ABXEAfgAFdAAEMDE4M3EAfgAFcQB+AAVxAH4ABXEAfgAFcQB+AAVxAH4ABXB0ABMxMDQ1MzAwNDEwMEhJTExVQkVBc3IAEWphdmEudXRpbC5IYXNoTWFwBQfawcMWYNEDAANJAA1jYWNoZV9iaXRtYXNrRgAKbG9hZEZhY3RvckkACXRocmVzaG9sZHhw/////z9AAAAAAAAMdwgAAAAQAAAACXQAB2RkcERhdGFzcQB+AAj/////P0AAAAAAAAx3CAAAABAAAAAKdAALY29kX3VzdWFyaW9xAH4AB3QACmRkcF9lc3RhZG90AAIwMHQADGNvZF9yZWdpc3Ryb3EAfgAFdAAKZGRwX2ZsYWcyMnQAAjAwdAAKZGRwX3Rwb2VtcHQAAjAxdAAKZGRwX251bXJlZ3EAfgAGdAAMdGlwb191c3VhcmlvdAABMHQACmRkcF90YW1hbm90AAIwM3QACGRkcF9jaWl1dAAFOTMwOTh0AApkZHBfbnVtcnVjdAALMTA0NTMwMDQxMDB4dAAJdGlwT3JpZ2VudAACSVR0AAVyb2xlc3NxAH4ACAAAAAA/QAAAAAAADHcIAAAAEAAAAAB4dAAKdGlwVXN1YXJpb3EAfgAWdAAIam5kaVBvb2x0AAVwMDE4M3QABmlkTWVudXQADDExNzE4MTUyNzA4N3QABmlzQ2xvbnNyABFqYXZhLmxhbmcuQm9vbGVhbs0gcoDVnPruAgABWgAFdmFsdWV4cQB+AAMAdAAHYXV0aFVSTHNyABNqYXZhLnV0aWwuQXJyYXlMaXN0eIHSHZnHYZ0DAAFJAARzaXpleHAAAAABdwQAAAAKdAA5aHR0cHM6Ly93d3cuc3VuYXQuZ29iLnBlL29sLXRpLWl0cmhlZW1pc2lvbi9lbWlzaW9ucmhlLmRveHQADHByaW1lckFjY2Vzb3NxAH4AJwF4dAAaTkFWQVJSTyBUUkFVQ08gSkhPTiBCUk9OQ090ABpOQVZBUlJPIFRSQVVDTyBKSE9OIEJST05DT3EAfgAFdAALMTA0NTMwMDQxMDBxAH4AJXQACEhJTExVQkVB; ITEMISIONRHESESSION=v8QFY2lQJYVqJHJN62pvXKT2pv31hmn13dwcqgSFBRzdT3xhD1GjblT2HGvxcfZ6pCJ4rM59KJVQwpvk6KHLPdyVkYh29pcJWXvd11QX8rGwwWXvx289LDZZXxQx2LzG2BTwG4qB0qZG5LCBs61FLGvg6L1GQ6YsPSvW8zY2pQDYLGNsWbcDLLg0J4hyHpw4MY1f6RFThc8jFNPGpmvzt5ZQ8QryJvQVYGcnsDbyNmCGfhFZyLhH5nVLMFkV9FkG!-983062877!1819764396\r\n";
			$http .= "Connection: close\r\n\r\n";
			// $http .= "Connection: Keep-Alive\r\n\r\n";
			// $http .= "\r\n\r\n";
			$http .= $data . "\r\n\r\n";
			
			if(fwrite($socket, $http) === FALSE) {
				// return 'Failed to send post data';
				return false;
			}
			
			$contents = "";
			while( ! feof($socket)) {
				$contents .= fgets($socket, 4090);
			}
			
			fclose($socket);
			
			return $contents;
		}
		
		return true;
	}
}

?>