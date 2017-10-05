<?php
ini_set("zlib.output_compression", "On");
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, AC_CONSUMER_KEY, AC_CONSUMER_SECRET);
$count=100;

if(isset($_GET['uid']) && !empty($_GET['uid'])){
	$uid=$_GET['uid'];
	if(isset($_GET['sid']) && !empty($_GET['sid'])){
		$sid=$_GET['sid'];
		$content = $connection->get('statuses/user_timeline', array('screen_name' => $uid, 'count' => $count, 'since_id'=>$sid));
	}elseif(isset($_GET['mid']) && !empty($_GET['mid'])){
		$mid=$_GET['mid'];
		$content = $connection->get('statuses/user_timeline', array('screen_name' => $uid, 'count' => $count, 'max_id'=>$mid));
	}else{
		$content = $connection->get('statuses/user_timeline', array('screen_name' => $uid, 'count' => $count));
	}
}elseif(isset($_GET['sid']) && !empty($_GET['sid'])){
	$sid=$_GET['sid'];
	$content = $connection->get('statuses/home_timeline', array('since_id' => $sid, 'count' => $count));
}elseif(isset($_GET['mid']) && !empty($_GET['mid'])){
	$mid=$_GET['mid'];
	$content = $connection->get('statuses/home_timeline', array('max_id' => $mid, 'count' => $count));
}else{
	$content = $connection->get('statuses/home_timeline', array('count' => $count));
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml;charset= UTF-8"/>
<meta http-equiv="Cache-control" content="no-cache" />
<title>wap2.0</title>
<style type="text/css">
body{padding: 0px; margin: 0px;background: #FFFFFF;}
p{padding: 0px; margin: 0px;background: #FFFEF0;}
.p1{background: #F1F4FE;}.p2{background: #ccffcc;}
img, video{max-width:95%;margin:10px;display:block;}
</style>
</head>
<body>
<?php

if(!$content){
	echo '<p>Not any Data</p><p><a href="wap.php">Home</a></p></body></html>';
	exit;
}

$total = count($content);
$sid=$content[0]->id_str;
$mid=$content[$total-1]->id_str;
for ($i=0; $i<$total; $i++)
{
	echo status($content[$i]);

	if($content[$i]->is_quote_status && isset($content[$i]->quoted_status)){
		echo status($content[$i]->quoted_status, 1);
	}
}

echo page($sid,$mid);


function b($str)
{
	$m=time()-strtotime($str);
	$n=$m/60;
	if($m<60)
	{
		return floor($m)."秒前";
	}
	else if($n<60)
	{
		return floor($n)."分钟前";
	}
	else if ($n<1440 && $n>=60)
	{
		$n=$n/60;
		return floor($n)."小时前";
	}
	else if ($n<43200 && $n>=1440)
	{
		$n=$n/1400;
		return floor($n)."天前";
	}
	else
		return date("Y-m-d H:i:s", strtotime($str));
}
function ex($cs){

    $text = $cs->text;
	$text = preg_replace("(@([a-zA-Z0-9_]+))", '@<a href="wap.php?uid=\\1">\\1</a>', $text);
	$text = str_replace("\n", "<br />", $text);

	if(isset($cs->entities->urls)){
		foreach ($cs->entities->urls as $u) {

			if (strpos($u->expanded_url, 'https://twitter.com/') !== false && strpos($u->expanded_url, '/status/') !== false) {
				$text = str_replace($u->url, '', $text);
			}else{

				$text = str_replace($u->url,
				'<a href="'.$u->expanded_url.'" target="_blank">'.$u->display_url.'</a>',
				$text);

				if (strpos($u->expanded_url, 'instagram.com/p/') !== false) {
					$text .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'media/?size=l" />';
				}
				if (strpos($u->expanded_url, 'pbs.twimg.com') !== false) {
					$text .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'" />';
				}

			}

		}
	}
	if(isset($cs->extended_entities->media)){
		$text = str_replace($cs->extended_entities->media[0]->url, '', $text);
		foreach ($cs->extended_entities->media as $m) {
			if($m->sizes->medium->w > 2048)
				$text .= '<a href="https://gugui.thisistap.com/resize.php?width=0&pic='.$m->media_url_https.':orig" target="_blank"><img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.'" /></a>';
			elseif($m->sizes->large->w > 1200)
				$text .= '<a href="https://gugui.thisistap.com/resize.php?width=0&pic='.$m->media_url_https.':large" target="_blank"><img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.'" /></a>';
			else
				$text .= '<img src="https://gugui.thisistap.com/resize.php?width=0&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.'" />';

			if(isset($m->video_info->variants)){
				foreach($m->video_info->variants as $v){
					$text .= '<video controls="controls" preload="none" src="'. $v->url.'"></video>';
				}
			}

		}
	}
    return trim($text);
}
function status($cs,$q=0){
	global $i;
	if(isset($cs->retweeted_status)){
		$cs=$cs->retweeted_status;
	}
	$username = '<a href="wap.php?uid='.$cs->user->screen_name.'">'.$cs->user->screen_name.'</a>';
	$myText = ex($cs);
	$myDateTimeAgo=b($cs->created_at);
	$myDateTime=date('Y-m-d H:i:s',strtotime($cs->created_at));
	$temp='<p';
	if($q){
		$temp .= ' class="p2"';
	}elseif($i%2==0){
		$temp .= ' class="p1"';
	}

	$temp .= '>';
	$temp .= $username . ': ' . $myDateTimeAgo . '<br />';
	$temp .= $myText;
	$temp .= '</p>';
	return $temp;
}
function page($sid,$mid){
	if(isset($_GET['uid']) && !empty($_GET['uid'])){
		$uid=$_GET['uid'];
	}
	$temp = '<a href="wap.php?';
	$news = $temp.'sid='.$sid;
	$older = $temp.'mid='.$mid;
	if(isset($uid)){
		$news .= '&uid='.$uid;
		$older .= '&uid='.$uid;
	}
	$news .= '">news</a> ';
	$older .= '">older</a>';
	$temp = $older.' <a href="wap.php">Home</a> '.$news;
	return $temp;
}
?>
</body>
</html>