<?php
ini_set("zlib.output_compression", "On");
	require_once('config.php');
	require_once('emoji.php');

if(0){
	$content = json_decode(file_get_contents('1.json'));
	$content_count = count($content);
}else{

	session_start();
	require_once('twitteroauth/twitteroauth.php');
	$seconds_to_cache = 3600*30;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
	header("Expires: $ts");
	header("Pragma: cache");
	header("Cache-Control: max-age=$seconds_to_cache");

	//如果本地有cookies，那么会话等于cookies
	if( isset($_COOKIE['c_oauth_token']) && isset($_COOKIE['c_oauth_token_secret']) ){

		$_SESSION['access_token']['oauth_token'] = $_COOKIE["c_oauth_token"];
		$_SESSION['access_token']['oauth_token_secret'] = $_COOKIE["c_oauth_token_secret"];

	}else{

		$_SESSION['access_token']['oauth_token'] = AC_CONSUMER_KEY;
		$_SESSION['access_token']['oauth_token_secret'] = AC_CONSUMER_SECRET;

		$access_token = $_SESSION['access_token'];

		$c_time = time()+3600*24*365;
		$c_path = '/p/';
		$c_domain = $_SERVER['SERVER_NAME'];

		setcookie('c_oauth_token', $access_token['oauth_token'], $c_time,$c_path,$c_domain);
		setcookie('c_oauth_token_secret', $access_token['oauth_token_secret'], $c_time,$c_path,$c_domain);

	}


	/* Get user access tokens out of the session. */
	$access_token = $_SESSION['access_token'];
	session_write_close();

	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

	$page=0;

	if(isset($_GET['status_id']) && !empty($_GET['status_id']))
	{
	$statusid = $connection->get('statuses/show', array('id' => $_GET['status_id'],'include_entities' => true, 'include_rts' => true));
	header('Content-Type: text/html; charset=utf-8');
	//print_r($statusid);
	echo "<!doctype html>
	<html lang=\"en\">
	<head>
		<meta charset=\"UTF-8\"><meta name=\"no_referrer\">
		<title>in_reply</title>

	<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"emoji.css\" />
	<style>body{background-color: #555;font-size: 1.5em;}
	.emoji{zoom:1.2;vertical-align:baseline;}
	.in_reply{margin: 20px;padding:20px;background-color: #FFFFFF;padding: 20px;border-radius: 8px; -moz-border-radius: 8px;  -webkit-border-radius: 8px;}
	.aa{font-size: .8em;background-color: #fff;}
	</style></head>
	<body><div class='in_reply'><p>";
	$tx = str_replace('_normal','_bigger',$statusid->user->profile_image_url_https);
	echo "<img src=\"https://gugui.thisistap.com/resize.php?width=0&pic=".$tx."\" /><br /><span  class='aa'>";
	echo "@";
	echo $statusid->user->screen_name." ";
	echo $statusid->user->name." ";
	echo b($statusid->created_at)."</span></p><p>";
	echo ex($statusid)."</p>";

	$in_reply_to_status_id_str11=$statusid->in_reply_to_status_id_str;

	if ($in_reply_to_status_id_str11){
		echo "<span  class='aa'><a href=\"./?status_id=".$in_reply_to_status_id_str11."\">查看源</a> via ".$statusid->in_reply_to_screen_name;
	}
	echo "</span></div></body>
	</html>";
	exit();
	}


	if(isset($_GET['status_id_ajax']) && !empty($_GET['status_id_ajax']))
	{
	$statusid = $connection->get('statuses/show', array('id' => $_GET['status_id_ajax'],'include_entities' => true, 'include_rts' => true));

	echo status($statusid);
	exit();
	}


	if(isset($_GET['action']) && $_GET['action']=='fo' && !empty($_GET['uid']))
	{
	print_r($connection->post('friendships/create', array('screen_name' => $_GET['uid'])));
	exit();
	}
	if(isset($_GET['action']) && $_GET['action']=='ufo' && !empty($_GET['uid']))
	{
	print_r($connection->post('friendships/destroy', array('screen_name' => $_GET['uid'])));
	exit();
	}



	if(isset($_GET['sid']) && !empty($_GET['sid']) && !isset($_GET['uid'])){
		$sid=$_GET['sid'];
		$content = $connection->get('statuses/home_timeline', array('include_entities' => true, 'include_rts' => true, 'exclude_replies' => false, 'since_id' => $sid, 'count' => COUNT));
	}elseif(isset($_GET['mid']) && !empty($_GET['mid']) && !isset($_GET['uid'])){
		$mid=$_GET['mid'];
		$content = $connection->get('statuses/home_timeline', array('include_entities' => true, 'include_rts' => true, 'exclude_replies' => false, 'max_id' => $mid, 'count' => COUNT));
	}elseif(isset($_GET['uid']) && !empty($_GET['uid'])){
		$uid=$_GET['uid'];
		if(isset($_GET['mid']) && !empty($_GET['mid']))
		{
			$mid=$_GET['mid'];
			$content = $connection->get('statuses/user_timeline', array('include_entities' => true, 'screen_name' => $uid, 'include_rts' => true, 'exclude_replies' => false, 'count' => COUNT, 'max_id'=>$mid));
		}elseif(isset($_GET['sid']) && !empty($_GET['sid']))
		{
			$sid=$_GET['sid'];
			$content = $connection->get('statuses/user_timeline', array('include_entities' => true, 'screen_name' => $uid, 'include_rts' => true, 'exclude_replies' => false, 'count' => COUNT, 'since_id'=>$sid));
		}else{
			$content = $connection->get('statuses/user_timeline', array('include_entities' => true, 'screen_name' => $uid, 'include_rts' => true, 'exclude_replies' => false, 'count' => COUNT));
		}


	}elseif(isset($_GET['public']) && $_GET['public']=='true'){
		$content = $connection->get('statuses/public_timeline', array('count' => COUNT));

	}elseif(isset($_GET['rtbyme']) && $_GET['rtbyme']=='true'){
		$content = $connection->get('statuses/retweeted_by_me', array('count' => COUNT));
	}
	else{
		$content = $connection->get('statuses/home_timeline', array('include_entities' => true, 'include_rts' => true, 'exclude_replies' => false, 'count' => COUNT));
	}


}
if(!$content){
	echo ' Not any Data';
	exit;
}

$content_count = count($content);

$max_id=$content[$content_count-1]->id_str;
$sin_id=$content[0]->id_str;

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" id="top" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" id="top" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" id="top" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" id="top" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" id="top" class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>...</title>
<meta http-equiv="Expires" content="<?php echo date("D,j M Y G:i:s", time()+3600); ?> GMT" />
<meta name="no_referrer">
<meta name="viewport" content="initial-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" media="all" href="style.css" />
<link rel="stylesheet" type="text/css" media="all" href="emoji.css" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<style>
.emoji{zoom:1;vertical-align:middle;}
.link{color:#33FF00;cursor: pointer;}
.in_reply{float: left;margin:10px;padding:10px;width:90%;padding-left:30px;/*background-color:#2C2C36;border-radius: 8px; -moz-border-radius: 8px;  -webkit-border-radius: 8px;*/border:solid 2px #888;}
.in_reply div{}
.in_reply div img{ margin-bottom: 20px; margin-right: 20px;float: left;margin-top: 8px;}
</style>
<style type="text/css">
.c1 {
  color: #6F0;
}
.c2 {
  display: none;
  background-color: #000;
  color: white;
  padding: 0.25em 0.6em;
  cursor: pointer;
  font-size: .9em;
}
span:hover .c2{
  display: block;
  position: absolute;
}
</style>
<script src="./jquery.min.js"></script>
<script type="text/javascript">
( function( $ ) {
	$( document ).ready(function() {

		$("span.c1").mouseenter(function(ev){
			var top = document.documentElement.scrollTop||document.body.scrollTop;
		  $("span.c2")

		  	.css("top", (ev.clientY + top - 20) + "px")
			.css("left", (ev.clientX + -20) + "px");

		});

	});
} )( jQuery );
</script>
<script type="text/javascript">
<!--
function getOs()
{
	if(navigator.userAgent.indexOf("MSIE")>0) {
		return "MSIE";
	}
	if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){
		return "Firefox";
	}
	if(isSafari=navigator.userAgent.indexOf("Safari")>0) {
		return "Safari";//google浏览器的navigator.userAgent也包含Safari当然还包括Chrome字段
	}
	if(isCamino=navigator.userAgent.indexOf("Camino")>0){
		return "Camino";
	}
	if(isMozilla=navigator.userAgent.indexOf("Gecko")>0){
		return "Gecko";//ie11包含该字段
	}
}
function open_without_referrer(link){
	if(getOs()=='Safari')
		newTab2(link);
	else
		newTab1(link);
}
function newTab1(url) {
	location="javascript:'<html><meta http-equiv=\"refresh\" content=\"0; url="+url+"\"></html>'";
}
function newTab2(url) {
	var a = document.createElement("a");
	if(!a.click) {
		window.location = url;
		return;
	}
	a.setAttribute("href", url);
	a.setAttribute("rel","noreferrer");
	a.setAttribute("target","_blank");
	a.style.display = "none";
	document.body.appendChild(a);
	a.click();
}
//-->
</script>

<script type="text/javascript">
<!--
	function fo(uid){
		window.open('?action=fo&uid='+uid, '', 'width=400, height=300, menubar=no, toolbar=no, scrollbars=no');
	}
	function unfo(uid){
		window.open('?action=ufo&uid='+uid, '', 'width=400, height=300, menubar=no, toolbar=no, scrollbars=no');
	}
    function for_db(uid){
		window.open('for_db.php?page=1&endpage=17&submit=%26%2325552%3B%26%2320132%3B&uid='+uid, '_blank', 'width=400, height=300, menubar=no, toolbar=no, scrollbars=yes');
	}
	function brow(uid){
		window.open('?uid='+uid, '');
	}
	function pbs(uid){
		window.open('pbs.php?uid='+uid, '', 'width=400, height=300, menubar=no, toolbar=no, scrollbars=yes');
	}
//-->
</script>
</head>
<body>
<div class="body1">

<div id="page" class="d4">
<?php

if($uid===null){
print <<<EOF
<a id="max_id" href="./?mid={$max_id}" title="快捷键←" target="_blank">较旧</a>
EOF;
}else{
print <<<EOF
<a id="max_id" href="./?mid={$max_id}&uid={$uid}" title="快捷键←" target="_blank">较旧</a>
EOF;
}

print <<<EOF

<a href="update.php">update</a> <!-- -->
<a href="search.php">搜索</a>
<a href="./">主页</a>
<a href="./photo_tweet.php">photo</a>
<a href="javascript:location.reload();">刷新</a>


EOF;

if($uid===null){
print <<<EOF
<a id="sin_id" href="./?sid={$sin_id}" title="快捷键→" target="_blank">最新</a>
EOF;
}else{
print <<<EOF
<a id="sin_id" href="./?sid={$sin_id}&uid={$uid}" title="快捷键→" target="_blank">最新</a>
EOF;
}


print <<<EOF
</div>

EOF;

if(count($content)===0){

print <<<EOF
<p style="color:#fff;"><img src="http://www.d9soft.com/upload/2009/3/27/2009032755220141.gif">暂无内容，或是刷新过快，请稍候继续！</p>
EOF;
}

$content_count = count($content);

for ($i=0; $i<$content_count;$i++)
{
	$retweeted_by='';
	$retweeted_by_id='';
	$dddd='d';

	if( isset($content[$i]->retweeted_status) ){
		$retweeted_by='<strong>RTS '.$content[$i]->user->screen_name.'</strong>';
		$retweeted_by_id='id="'.$content[$i]->retweeted_status->user->screen_name.'"';
		$cs=$content[$i]->retweeted_status;

	}else{
		$cs=$content[$i];
	}

	$id_str=$cs->id_str;
	$screen_name=$cs->user->screen_name;
	$name=$cs->user->name;
	$myText = trim($cs->text);
	$created_at=strtotime($cs->created_at);
	$source=$cs->source;

    $in_reply_to_status_id_str=$cs->in_reply_to_status_id_str;

	if(isset($cs->entities->urls)){

		foreach ($cs->entities->urls as $u) {
			$myText = str_replace($u->url,
				'<a href="'.$u->expanded_url.'" target="_blank">'.$u->display_url.'</a>',
				//'<span class="link" onclick="open_without_referrer(\''.$u->expanded_url.'\');">'.$u->display_url.'</span>',
				$myText);

			if (strpos($u->expanded_url, 'instagram.com/p/') !== false) {
				$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'media/?size=l" />';
			}

			if (strpos($u->expanded_url, 'pbs.twimg.com') !== false) {
				$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'" />';
			}
		}
	}


	if(isset($cs->extended_entities->media)){

		$temp_media = '';

		foreach ($cs->extended_entities->media as $m) {
			if($m->type=='photo'){

				$temp_media .= '<a href="'.$m->media_url_https.'" target="_blank">'.$m->display_url.'</a> ';

				if($m->sizes->large->w > 2048)
					$myText .= '<br /><a href="https://gugui.thisistap.com/resize.php?width=0&pic='.$m->media_url_https.':orig" target="_blank"><img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.':orig" /></a>';
				elseif($m->sizes->large->w > 1200)
					$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.':large" />';
				else
					$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=0&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.'" />';

			}
		}
	}

	$myText = str_replace($cs->extended_entities->media[0]->url, $temp_media, $myText);

	$myText = a($myText);
	$myText = emoji_unified_to_html($myText);

	if($cs->is_quote_status){
		$myText .= '<div id="'.$cs->quoted_status_id_str.'">'.status($cs->quoted_status).'</div>';
	}

	if($in_reply_to_status_id_str){
			$sta_id = 'a_'.md5(time().$in_reply_to_status_id_str);
			$myText .= ' <span class="link" onclick="status_fun(\''.$sta_id.'\',\''.$in_reply_to_status_id_str.'\');">check</span><div id="'.$sta_id.'"></div>';
	}


	$myDateTimeAgo=b($cs->created_at);
	$myDateTime=date('Y-m-d H:i:s',strtotime($cs->created_at));


	$profile_image_url_https=$cs->user->profile_image_url_https;
	$profile_image_url_https=str_replace('_normal.','_bigger.',$profile_image_url_https);
	$description=$cs->user->description;
	$location=$cs->user->location;
	$user_url=$cs->user->url;

	$profile_image=$cs->user->profile_image_url;
	$profile_image=str_replace('_normal.','_bigger.',$profile_image);

	$profile_image_url_https_md5=md5($profile_image_url_https);

print <<<EOF
<div class="{$dddd}">

<div class="d1">
<img src="https://gugui.thisistap.com/resize.php?width=0&_ActionVar_={$profile_image_url_https_md5}&pic={$profile_image_url_https}" align="left" title="{$description}" />
</div><!-- d1 -->
<div class="d2"><div class="text">$myText</div>
<div class="d3"><div class="text2">[<a href="?status_id={$id_str}" target="_blank">查看</a>]
EOF;

if ($in_reply_to_status_id_str)
print <<<EOF
	[<a href="./?status_id={$in_reply_to_status_id_str}" target="_blank">查看源</a>]

EOF;

print <<<EOF

{$retweeted_by}

		<a href="./?uid={$screen_name}" {$retweeted_by_id} title="{$location}">{$name}</a>&nbsp;

EOF;

if ($user_url)
print <<<EOF
			[<a href="{$user_url}">{$screen_name}</a>]

EOF;

else
print <<<EOF
			[{$screen_name}]

EOF;

print <<<EOF

<a href="?action=ufo&uid={$screen_name}" target="_blank">unfo</a>
<a href="pbs.php?uid={$screen_name}" target="_blank">pbs</a>

			<span title="{$myDateTime},{$id_str}">
				$myDateTimeAgo via:{$source}
				<a href="retweet.php?id={$id_str}">retw</a>
<i>{$i}</i>
EOF;

print <<<EOF

<a href="update.php?id={$id_str}&uid={$screen_name}&str=">reply</a>


EOF;

print <<<EOF
			</span>

</div>
		</div><!-- d3 -->
	</div><!-- d2 -->
</div><!-- d -->

EOF;

}


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
    $myText = $cs->text;
	$myText = emoji_unified_to_html($myText);
	if(isset($cs->entities->urls)){

		foreach ($cs->entities->urls as $u) {
			$myText = str_replace($u->url,
				'<a href="'.$u->expanded_url.'" target="_blank">'.$u->display_url.'</a>',
				$myText);
			//https://twitter.com/bfishadow/status/585695934383845377
			/*
			if (strpos($u->expanded_url, 'https://twitter.com/') !== false && strpos($u->expanded_url, '/status/') !== false) {
				$temp_arr = explode('/',$u->expanded_url);
				$myText .= ' <a href="?status_id='.$temp_arr[5].'">check</a>';
			}*/

			if (strpos($u->expanded_url, 'https://twitter.com/') !== false && strpos($u->expanded_url, '/status/') !== false) {
				$temp_arr = explode('/',$u->expanded_url);
				$sta_id = 'a_'.md5(time().$temp_arr[5]);
				$myText .= ' <a href="###" onclick="status_fun(\''.$sta_id.'\',\''.$temp_arr[5].'\');">check</a><div id="'.$sta_id.'"></div>';
			}


			if (strpos($u->expanded_url, 'instagram.com') !== false) {
				$myText .= '<br /><img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'media/?size=l" />';
			}
			if (strpos($u->expanded_url, 'pbs.twimg.com') !== false) {
				$myText .= '<br /><img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($u->expanded_url).'&pic='.$u->expanded_url.'" />';
			}
		}
	}


	if(isset($cs->extended_entities->media)){
		$temp_media ='';
		foreach ($cs->extended_entities->media as $m) {
			if($m->type=='photo'){
				$temp_media .= '<a href="'.$m->media_url_https.'" target="_blank">'.$m->display_url.'</a> ';

				if($m->sizes->medium->w > 2048)
					$myText .= '<br /><a href="https://gugui.thisistap.com/resize.php?width=0&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.':orig" target="_blank"><img src="https://gugui.thisistap.com/resize.php?width=1024&pic='.$m->media_url_https.':large" /></a>';
				elseif($m->sizes->large->w > 1200)
					$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=1024&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.':large" />';
				else
					$myText .= '<img src="https://gugui.thisistap.com/resize.php?width=0&_ActionVar_='.md5($m->media_url_https).'&pic='.$m->media_url_https.'" />';
			}
		}

		$myText = str_replace($cs->extended_entities->media[0]->url, $temp_media, $myText);

	}


    return trim($myText);

}


function a($str)
{

	$str = preg_replace("(@([a-zA-Z0-9_]+))", "<span class=\"c1\"><span class=\"c2\"><em onclick=\"brow('\\1')\">brow</em> <em onclick=\"fo('\\1')\">fo</em> <em onclick=\"unfo('\\1')\">unfo</em> <em onclick=\"pbs('\\1')\">pbs</em></span>\\0</span>", $str);

	$str = str_replace("\n", "<br />", $str);

	return $str;
}

file_put_contents('1.json', json_encode($content));
?>


<div id="page_bom" class="d4"></div>
<script type="text/javascript">
<!--
function status_fun(div_id,sta_id){

            $.ajax({
                url:"./index.php",
                type:"get",
				cache: false,
                data:{status_id_ajax:sta_id},
                success:function(result){
                        //追加数据
						$('#'+div_id).append(result);

                }
            })


}

//-->
</script>
</div>
<script language="JavaScript" src="s.js" type="text/javascript"></script>
</body>
</html>
<?php

/**
 * (功能描述)
 * @Date:
 * @param    (类型)     (参数名)    (描述)
 */
function status($status){

	$description=$status->user->description;
	$location=$status->user->location;
	$user_url=$status->user->url;

	$temp = "<div class='status'><div class='d'>";
	$temp.= "<div class='d1'><img src=\"https://gugui.thisistap.com/resize.php?width=0&_ActionVar_=".md5(str_replace('_normal','_bigger',$status->user->profile_image_url_https))."&pic=".str_replace('_normal','_bigger',$status->user->profile_image_url_https)."\"  title=\"$description\" /></div><div class='d2'>";

	$temp1= ex($status);
	$temp1= a($temp1);
	$temp1= emoji_unified_to_html($temp1);

	$temp.= "<div class='text'>";

    $in_reply_to_status_id_str=$status->in_reply_to_status_id_str;

	if($in_reply_to_status_id_str){
			$sta_id = 'a_'.md5(time().rand(1,9999).$in_reply_to_status_id_str);
			$temp1 .= ' <span class="link" onclick="status_fun(\''.$sta_id.'\',\''.$in_reply_to_status_id_str.'\');">check</span><div id="'.$sta_id.'"></div>';
	}

	$temp.= $temp1;

	$temp.= "</div>";
	$temp.= "<div class='d3'><div class='text2'>@<a href=\"?uid=".$status->user->screen_name."\" title=\"$location\">";
	$temp.= $status->user->screen_name."</a> [<a href=\"$user_url\">";
	$temp.= $status->user->name."</a>] ";
	$temp.= $status->source." ";
	$temp.= b($status->created_at);

	$temp.= "<a href=\"retweet.php?id=".$status->id_str."\">retw</a> ";
	$temp.= "<a href=\"retweet.php?id=".$status->id_str."&uid=".$status->user->screen_name."&str=\">reply</a> ";

	$temp.= "</div></div>";
	$temp.= "</div></div></div>";
	return $temp;
}

?>