<?php
//header("Content-Type: application/rss+xml;charset=UTF-8");
header("Content-Type: application/rss+xml;charset=UTF-8");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
echo "<root>\r\n";
?><?php
require_once('config.php');
require_once('twitteroauth/twitteroauth.php');

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, AC_CONSUMER_KEY, AC_CONSUMER_SECRET);

$count=200;
$uid=szstupidcool;

//$content = $connection->get('statuses/home_timeline', array('include_entities' => true, 'include_rts' => true, 'count' => $count));

$content = $connection->get('statuses/user_timeline', array('include_entities' => true, 'screen_name' => $uid, 'include_rts' => true, 'exclude_replies' => false, 'count' => $count));

for ($i=0; $i<count($content);$i++)
{
$myName = $content[$i]->user->name;
$myText =  strip_tags( a($content[$i]) );
$myDateTime=date('Y-m-d H:i:s',strtotime($content[$i]->created_at));
echo "<aa>{$myName}：{$myText} {$myDateTime}</aa>\r\n";
}
echo "\r\n</root>";

function a($c){

		$text=$c->text;

		if(isset($c->entities->urls)){
		
			foreach ($c->entities->urls as $v) {
				$text = str_replace($v->url, $v->expanded_url, $text);
			}
		}


		if(isset($c->extended_entities->media)){
			$temp_media = '';
			foreach($c->extended_entities->media as $val){
				if($val->type=='photo'){
					$temp_media .= $val->media_url_https ." ";
				}
			}
			$text = str_replace($c->extended_entities->media[0]->url, trim($temp_media), $text);
		}


		return $text;

}

?>
