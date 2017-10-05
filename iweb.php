<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="no_referrer">
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<title></title>
<link rel="stylesheet" href="">
<style>
body{color:#ddd;background-color: #333;font-size:1.85em;}
img{max-width: 100%;display: block;}
</style>
</head>
<body>
<?php
define('USEPROXY', 0);
define('P_Y', '127.0.0.1:8088');

date_default_timezone_set('PRC');
$id=$_GET['id'];
$debug=@$_GET['debug'];
$debug=0;
echo iweb($id, $debug);


function iweb($id=0, $debug=0){
	$url = 'https://twitter.com/i/web/status/'.$id;
	$headers = array(
		'Referer: https://twitter.com/',
	);

if($debug===0){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt ($ch, CURLOPT_RANGE, '0-50');
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	if(USEPROXY==1){
		curl_setopt($ch, CURLOPT_PROXY, P_Y);
	}

	$result = curl_exec ($ch);
	curl_close ($ch);
	//echo $result;

	$result = str_replace('        ','',$result);



	file_put_contents('iweb', $result);
}else{
	$result = file_get_contents('iweb');
}


	preg_match('/title="(.+?) on Twitter:/mis', $result, $matches121);
	preg_match('/<style id="user-style-(.+?)">/mis', $result, $matches1211);
	//print_r($matches1211);exit;


	preg_match('/<meta  property="og:url" content="https:\/\/twitter.com\/(.+?)\/status/mis', $result, $matches122);




	preg_match_all('/<div class="js-tweet-text-container">(.+?)<\/div>/mis', $result, $matches);
	//preg_match_all('/<strong class="fullname js-action-profile-name show-popup-with-id " data-aria-label-part>(.+?)<\/strong>/mis', $result, $matches22);
	preg_match_all('/<strong class="fullname show-popup-with-id " data-aria-label-part>(.+?)<\/strong>/mis', $result, $matches22);
	//preg_match_all('/<span class="username js-action-profile-name" data-aria-label-part><s>@<\/s><b>(.+?)<\/b><\/span>/mis', $result, $matches2);
	preg_match_all('/<span class="username u-dir" dir="ltr" data-aria-label-part>@<b>(.+?)<\/b><\/span>/mis', $result, $matches2);
	preg_match_all('/    <img class="avatar js-action-profile-avatar" src="(.+?)" alt="">/mis', $result, $matches3);
	//preg_match_all('/class="tweet-timestamp js-permalink js-nav js-tooltip" title="(.+?)"  data-conversation-id/mis', $result, $matches4);
	preg_match_all('/class="tweet-timestamp js-permalink js-nav js-tooltip" title="(.+?)"/mis', $result, $matches4);
	preg_match_all('/<meta  property="og:image" content="(.+?):large">/mis', $result, $matches0);

//*
if(!isset($matches22[1][count($matches[1])-1])){
//echo  ' <p>11111111</p>';
//echo count($matches[1]);
//print_r($matches22[1]);
array_unshift($matches22[1], $matches121[1]);
array_unshift($matches2[1], $matches1211[1]);
//print_r($matches22[1]);
}
//*/

	foreach($matches[1] as $key=>$val){
		echo '<p><img style="line-height: .6em; float:left;padding-right: .5em;" src="'.$matches3[1][$key].'" /></p><p style="font-size: .6em;">';

			echo $matches22[1][$key].' <br />';
			echo $matches2[1][$key].' <br />';



		echo date('Y-m-d H:i', strtotime(str_replace(' - ', ' ', $matches4[1][$key]))+16*3600).' </p>';
		echo strip_tags($val);

		if($key===0 && $matches0[1]){

			foreach($matches0[1] as $k=>$v){
				echo '<img src="'.$v.'" />';
			}
		}

		if($key<sizeof($matches[1])-1){
			echo '<hr style="clear:both;" />';

		}

	}
}
?>
</body>
</html>