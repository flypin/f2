<?php
require_once('config.php');
session_start();
error_reporting(0);
require_once('twitteroauth/twitteroauth.php');

if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}

/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

?>
<?php
if(isset($_GET['q']))
{

if(isset($_GET['page'])){
$page=$_GET['page'];
$page=intval($page)+1;
}
else{
$page=1;
}
$url='https://api.twitter.com/1.1/search/tweets.json';

$content = $connection->get($url, array('q'=>urldecode($_GET['q']),'lang'=>$_GET['lang'],'max_id'=>$_GET['mid'],'count'=>100));   //,'page'=>$page

//print_r($content);
file_put_contents('11.json', json_encode($content));

//print_r($content->statuses);

//echo("<h1>".urldecode($content->query)."</h1>");

$n = count($content->statuses);
$mid=$content->statuses[$n-1]->id_str;
$title = $content->statuses[0]->created_at;
$title = b( date('Y-m-d H:i:s',strtotime($title)) );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv=Content-Type content="text/html;charset=utf-8">
  <title><?php echo $title;?></title>
<style>
.a{margin-bottom: 10px;margin-left: 50px;}
.b{margin-top: 5px;color: #555;}
body{background-color: #F2F2F2; width:98%;font-size: .8em;}
.a a{color: blue;}
.b a{color: #555;}
.a1 img{max-width: 40px;}
.a1{float: left;padding-right: 10px;padding-top: 0px;min-height: 40px;}
.abt{padding-left:18px;background-image: url(image_s.gif);background-repeat:no-repeat;background-position:0 3px;}
</style>
<script src="./jquery.min.js" charset="utf-8"></script>

</head>
<body>


<form method="get" action="">
	<input type="text" name="q" value="<?php echo(urldecode($_GET['q']))?>" />
	<select name="lang">
	<?php
	if($_GET['lang']){
		echo('<option value="'.$_GET['lang'].'" selected="selected">'.$_GET['lang'].'</option>');
	}else{
		echo('<option value="zh" selected="selected">zh</option>');
	}?>
		<option value="zh">zh</option>
		<option value="en">en</option>
		<option value="ja">ja</option>
	</select>
	<input type="submit" />
</form>


<?php
//if($n<3)exit;

echo("<p><center><a href=\"?q=".$_GET['q']."&lang=".$_GET['lang']."&mid={$mid}&page={$page}\">下一页</a> {$page} </center></p>");
/*
$link = @mysql_connect(MY_HOST, MY_USER, MY_PWD);
$db_selected = @mysql_select_db(MY_DB, $link);
@mysql_query("set names utf8mb4");
*/


$host = MY_HOST;
$dbname = MY_DB;
$user = MY_USER;
$password = MY_PWD;
$charset = "utf8mb4";
$dsn = "mysql:dbname=$dbname;host=$host";
$dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset") );



for ($i=0; $i<$n;$i++)
{
	/*
        $sql2 = sqltodb2($content->statuses[$i]);
        $j2 = @mysql_query($sql2);
		if($j2==1){
			$ack2='style="background-color: #66FF00;padding: 3px;"';
		}else{
			$ack2='';
		}
*/
		if( isset($content->statuses[$i]->retweeted_status) ){

			$content->statuses[$i]=$content->statuses[$i]->retweeted_status;

		}

		$affected = sqltodb($content->statuses[$i],$dbh);


		
/*
		$sql = sqltodb($content->statuses[$i]);
		//echo $sql;
		@mysql_query($sql);
		$j = mysql_affected_rows();
		if($j==1){
			$ack="#D5D5D5";
		}else{
			$ack="#99CCCC";
		}

		mysql_query(sqltodb5($content->statuses[$i]));

        $sql2 = sqltodb2($content->statuses[$i]);
        $j2 = @mysql_query($sql2);
		if($j2==1){
			$ack3='style="background-color: red;padding: 3px;"';
		}else{
			$ack3='';
		}
*/
	$id_str = $content->statuses[$i]->id_str;
	//$profile_image_url = $content->statuses[$i]->user->profile_image_url;
	$profile_image_url = 'o.png';
	$screen_name = $content->statuses[$i]->user->screen_name;
	$created_at = $content->statuses[$i]->created_at;
	$source = $content->statuses[$i]->source;
	$text = $content->statuses[$i]->text;

	$c=$content->statuses[$i];
	foreach ($c->entities->urls as $v) {
		$text = str_replace($v->url, '<a href="'.$v->expanded_url.'" target="_blank">'.$v->display_url.'</a>', $text);
	}

	foreach($c->entities->media as $val){
		if($val->type=='photo'){
			$text = str_replace($val->url, '<a href="'.$val->media_url.'" target="_blank" class="abt">'.$val->display_url.'</a>', $text);
				
            }
	}

    if ($i==0)
	echo('<div style=" /*float:left;width:45%; margin-left:30px;*/">');
	
    echo('<div style="padding-bottom:10px;margin-bottom: 1px;width:100%; float:left;background-color:'.$ack.';">');

    echo('<div class="a1">');
	echo('<img src="'.$profile_image_url.'" '.$ack2.' '.$ack3.'>');
	echo('</div><div class="a">');
	echo a($text);
	echo('<div class="b"><a href="/?uid='.$screen_name.'">');
	echo($screen_name);
	echo("</a> <span >");
	//echo d($created_at);
	echo('</span>');
	$myDateTime=date('Y-m-d H:i:s',strtotime($created_at));
	echo($myDateTime);
	//echo(b($myDateTime));
	echo(' ');
	//echo(' '. $id_str);
	echo(c($source));
	echo('</div></div></div>');
    if($i==ceil($n/2)){
        echo('</div>');
        echo('<div style=" /*float:left;width:45%; margin-left:30px;*/">');
    }

	//if(($i+1)%2===0){
	//echo('<div style="clear: both;"></div>'."\r\n");
	//}

}
    echo('</div>'."\r\n");
	echo('<div style="clear: both;"></div>'."\r\n");
//@mysql_close($link);
$dbh = null;

}
function d($str)
{
Return date('Y-m-d H:i:s',strtotime($str));
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
	else// if ($n<43200 && $n>=1440)
	{
		$n=$n/1400;
		return floor($n)."天前";
	}
	//else
		//return date("Y-m-d H:i:s", strtotime($str));
}

function a($str)
{
	//$str = preg_replace('/(https?:\/\/)(.*?)([\/\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/', '<a href="\\0">\\0</a>', $str);
	$str = preg_replace("(@([a-zA-Z0-9_]+))", "<a href=\"/p/?uid=\\1\">\\0</a>", $str);
	$str.=$s;
	return $str;
}
function c($str)
{
	$str = str_replace('&lt;', '<', $str);
	$str = str_replace('&gt;', '>', $str);
	$str = str_replace('&quot;', '"', $str);
	return $str;
}

?>

	<?php
    
if($n<3)echo '<div id="autoclick"></div>';
    
echo("<p><center><a id=\"autoclick\" href=\"?q=".$_GET['q']."&lang=".$_GET['lang']."&mid={$mid}&page={$page}\">下一页</a> {$page} </center></p>");


?>


<form method="get" action="">
	<input type="text" name="q" value="<?php echo(urldecode($_GET['q']))?>" />
	<select name="lang">
	<?php
	if($_GET['lang']){
		echo('<option value="'.$_GET['lang'].'" selected="selected">'.$_GET['lang'].'</option>');
	}else{
		echo('<option value="zh" selected="selected">zh</option>');
	}?>
		<option value="zh">zh</option>
		<option value="en">en</option>
		<option value="ja">ja</option>
	</select>
	<input type="submit" />
</form>
<script type="text/javascript" charset="utf-8">
        function show(){
            $('#autoclick').get(0).click();
        }

        $(function() {
  

  
        setTimeout("show()",3000);

  /*
        $("#autoclick").click(function () {
            alert(11);
            window.location.href = $(this).href;
            return true;

        }); //on click move to HOME page

  */

  
  });
</script>

</body>
</html>
<?php

function tco($str){
	if(strpos($str, 't.co')!== false){
		$pat = '/(http:\/\/t.co\/)([a-z0-9]+)/i';
		if(preg_match_all($pat, $str, $m)){

			foreach($m[0] as $key=>$val){
				//echo $val."<br />";

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $val);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_PROXY, P_Y);
				$data = curl_exec($ch);
				curl_close($curl);

				if( preg_match('/Location: (.*?)\n/i', $data, $matches) ){
					$str =  str_replace($val, $matches[1], $str);
				}
			}
		}

	}
	return $str;
}

?>