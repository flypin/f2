<?php
ini_set("zlib.output_compression", "On");
	require_once('config.php');
	//require_once('emoji.php');

if(0){
	$content_count = count($content);
}else{

	session_start();
	require_once('twitteroauth/twitteroauth.php');
	$seconds_to_cache = 3600*30;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
	header("Expires: $ts");
	header("Pragma: cache");
	header("Cache-Control: max-age=$seconds_to_cache");

	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, AC_CONSUMER_KEY, AC_CONSUMER_SECRET);

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

	<!-- <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"emoji.css\" /> -->
	<style>body{background-color: #555;font-size: 1.5em;}
	.emoji{zoom:1.2;vertical-align:baseline;}
	.in_reply{margin: 20px;padding:20px;background-color: #FFFFFF;padding: 20px;border-radius: 8px; -moz-border-radius: 8px;  -webkit-border-radius: 8px;}
	.aa{font-size: .8em;background-color: #fff;}
	</style></head>
	<body><div class='in_reply'><p>";
	$tx = str_replace('_normal','_bigger',$statusid->user->profile_image_url_https);
	echo "<img src=\"".$tx."\" /><br /><span  class='aa'>";
	echo "@";
	echo $statusid->user->screen_name." ";
	echo $statusid->user->name." ";
	echo b($statusid->created_at)."</span></p><p>";
	echo ex($statusid)."</p>";

	$in_reply_to_status_id_str11=$statusid->in_reply_to_status_id_str;

	if ($in_reply_to_status_id_str11){
		echo "<span  class='aa'><a href=\"?status_id=".$in_reply_to_status_id_str11."\">查看源</a> via ".$statusid->in_reply_to_screen_name;
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
	if(isset($_GET['action']) && $_GET['action']=='crea_fav' && !empty($_GET['id']))
	{
		print_r($connection->post('favorites/create', array('id' => $_GET['id'], 'include_entities' => true)));
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
<html lang="en" id="top" class="no-js">
<head>
<meta charset="utf-8">
<title>...</title>
<meta http-equiv="Expires" content="<?php echo date("D,j M Y G:i:s", time()+3600); ?> GMT" />
<meta name="no_referrer">
<meta name="viewport" content="initial-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" media="all" href="style.css" />
<!-- <link rel="stylesheet" type="text/css" media="all" href="emoji.css" /> -->
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
<div class="warp">

<div id="page" class="d4">
<?php

if($uid===null){
print <<<EOF
<a id="max_id" href="?mid={$max_id}" title="快捷键←" target="_blank">较旧</a>
EOF;
}else{
print <<<EOF
<a id="max_id" href="?mid={$max_id}&uid={$uid}" title="快捷键←" target="_blank">较旧</a>
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
<a id="sin_id" href="?sid={$sin_id}" title="快捷键→" target="_blank">最新</a>
EOF;
}else{
print <<<EOF
<a id="sin_id" href="?sid={$sin_id}&uid={$uid}" title="快捷键→" target="_blank">最新</a>
EOF;
}


print <<<EOF
</div>

EOF;

$content_count = count($content);

for ($i=0; $i<$content_count;$i++){
	$retweeted_by='';
	$retweeted_by_id='';
	$dddd="d";

	if( isset($content[$i]->retweeted_status) ){
		$dddd="ddd";
		$retweeted_by='<strong>RTS '.$content[$i]->user->screen_name.'</strong>';
		$retweeted_by_id='id="'.$content[$i]->retweeted_status->user->screen_name.'"';
		$cs=$content[$i]->retweeted_status;

		$screen_name=$cs->user->screen_name;
		$profile_image=$cs->user->profile_image_url;
		$profile_image=str_replace('_normal.','_bigger.',$profile_image);

	}else{
		$cs=$content[$i];

		$screen_name=$cs->user->screen_name;
		$profile_image=$cs->user->profile_image_url;
		$profile_image=str_replace('_normal.','_bigger.',$profile_image);

	}


	echo status($cs);

}

function ex($cs){
    $text = $cs->text;
	$text = a($text);
	//$text = emoji_unified_to_html($text);

	if($cs->is_quote_status && isset($cs->quoted_status)){
		$text .= '<div id="'.$cs->quoted_status_id_str.'" class="status">'.status($cs->quoted_status).'</div>';
	}elseif($cs->is_quote_status && !isset($cs->quoted_status)){
		$text .= ' <span class="link" onclick="status_fun(\''.$cs->quoted_status_id_str.'\',\''.$cs->quoted_status_id_str.'\');"><i class="fa fa-location-arrow"></i></span><div id="'.$cs->quoted_status_id_str.'"></div>';

	}

	$in_reply_to_status_id_str=$cs->in_reply_to_status_id_str;
	if($in_reply_to_status_id_str){
			$sta_id = 'a_'.md5(time().rand(1,9999).$in_reply_to_status_id_str);
			$text .= ' <span class="link" onclick="status_fun(\''.$sta_id.'\',\''.$in_reply_to_status_id_str.'\');"><i class="fa fa-location-arrow"></i></span><div id="'.$sta_id.'"></div>';
	}

	if(isset($cs->entities->urls)){

		foreach ($cs->entities->urls as $u) {

			$text = str_replace($u->url, '<a href="'.$u->expanded_url.'" target="_blank">'.$u->display_url.'</a>', $text);


			if (strpos($u->expanded_url, 'instagram.com') !== false) {
				$text .= '<img src="'.$u->expanded_url.'media/?size=l" class="twimg" />';
			}
			if (strpos($u->expanded_url, 'pbs.twimg.com') !== false) {
				$text .= '<img src="'.$u->expanded_url.'" class="twimg" style="background-color:#FFFF99;padding:10px;" />';
			}
		}
	}


	if(isset($cs->extended_entities->media)){
		$temp_media_url = '';
		$temp_media ='';
		foreach ($cs->extended_entities->media as $m) {
			$temp_media_url=$m->url;
			$temp_media .= '<a href="'.$m->media_url_https.'" target="_blank">'.$m->display_url.'</a> ';

			if($m->sizes->large->w > 2048){
				$text .= '<a href="'.$m->media_url_https.':orig" target="_blank"><img src="'.$m->media_url_https.'" class="twimg" /></a>';
			}
			elseif($m->sizes->large->w > 1200){
				//$text .= '<img src="'.$m->media_url_https.'" class="twimg" />';
				$text .= '<a href="'.$m->media_url_https.':large" target="_blank"><img src="'.$m->media_url_https.'" class="twimg" /></a>';
			}
			else{
				$text .= '<img src="'.$m->media_url_https.'" class="twimg" />';
			}

			if(isset($m->video_info->variants)){
				foreach($m->video_info->variants as $v){
					if(strpos($v->url, '.mp4')>0){
						$text .= '<video controls="controls" preload="none" src="'. $v->url.'"></video>';
						$text .= $v->url."\n";
					}
				}
			}
		}
		$text = str_replace($temp_media_url, $temp_media, $text);
	}
    return trim($text);
}


function a($str)
{

	$str = preg_replace("(@([a-zA-Z0-9_]+))", "<span class=\"c1\"><span class=\"c2\"><em onclick=\"brow('\\1')\">brow</em> <em onclick=\"fo('\\1')\">fo</em> <em onclick=\"unfo('\\1')\">unfo</em> <em onclick=\"pbs('\\1')\">pbs</em></span>\\0</span>", $str);

	$str = str_replace("\n", "<br />", $str);

	return $str;
}
?>


<div id="page_bom" class="d4"></div>
<script type="text/javascript">
<!--
function status_fun(div_id,sta_id){

            $.ajax({
                url:"./index99.php",
                type:"get",
				cache: false,
                data:{status_id_ajax:sta_id},
                success:function(result){
                        //追加数据
						$('#'+div_id).append("<div class=\"status\">"+result+"</div>");

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

function status($status){
	global $dddd;
	global $retweeted_by;
	global $i;
	$description=$status->user->description;
	$location=$status->user->location;

	if(isset($status->user->entities->url)){
		foreach ($status->user->entities->url->urls as $u) {
			$user_url = $u->expanded_url;
		}
	}

	$temp = "<div class='item'>\n";
	$temp.= "\t<div class='d1'>\n<img src=\"".str_replace('_normal','_bigger',$status->user->profile_image_url_https)."\"  title=\"$description\" />\n\t</div><!-- .d1 -->\n";
	$temp.= "\t<div class='d2'>\n";
	$temp.= "\t\t<div class='text'>\n";
	$temp.= ex($status);

	$temp.= "\n\t\t</div><!-- .text -->\n";
	$temp.= "\t\t<div class='d3'>\n";

	$temp.= $retweeted_by." @<a href=\"?uid=".$status->user->screen_name."\" title=\"$location\">";
	$temp.= $status->user->screen_name."</a>";

	if(isset($user_url)){
		$temp.= " [<a href=\"$user_url\">".$status->user->name."</a>]";
	}else{
		$temp.= " [".$status->user->name."]";
	}

	$temp.= "<span title=\"".$status->id_str.",".date('Y-m-d H:i:s',strtotime($status->created_at))."\">";
	$temp.= b($status->created_at);
	$temp.= "</span> ";
	$temp.= "<a href=\"?status_id=".$status->id_str."\">status</a> ";
	$temp.= "<a href=\"?id=".$status->id_str."&action=crea_fav\">fav</a> ";
	$temp.= "<a href=\"retweet.php?id=".$status->id_str."\">retw</a> ";
	$temp.= "<a href=\"update.php?id=".$status->id_str."&uid=".$status->user->screen_name."&str=\">reply</a> ";
	$temp.= "<a href=\"?uid=".$status->user->screen_name."&action=fo\">fo</a> ";
	$temp.= "<a href=\"iweb.php?id=".$status->id_str."\">comm</a> ";

	$temp.= $status->source.' '.$i;

	$temp.= "\n\t\t</div><!-- .d3 -->\n\t</div><!-- .d2 -->\n</div><!-- .d or .ddd -->\n\n";
	return $temp;
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
?>