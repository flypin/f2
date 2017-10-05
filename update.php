<?php
session_start();
header("Content-type: text/html; charset=utf-8");
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');
require_once('emoji.php');
/*
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}*/

$_SESSION['access_token']['oauth_token'] = AC_CONSUMER_KEY;
$_SESSION['access_token']['oauth_token_secret'] = AC_CONSUMER_SECRET;


$access_token = $_SESSION['access_token'];
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, AC_CONSUMER_KEY, AC_CONSUMER_SECRET);




	$src = array(
		array(0x231B),
		array("hi"),
		array(0x1F345),
	);


	foreach ($src as $unified){

		$bytes = '';

		foreach ($unified as $cp){
			$bytes .= utf8_bytes($cp);
		}

		$str .= $bytes;

	}




if($_POST['Submit']=='POST' && !empty($_POST['in_reply_to_status_id']) && !empty($_POST['status'])){
	print_r($connection->post('statuses/update', array('status' => $_POST['status'], 'in_reply_to_status_id' => $_POST['in_reply_to_status_id'])));
	echo('<hr />');
}elseif($_POST['Submit']=='POST' && empty($_POST['in_reply_to_status_id']) && !empty($_POST['status'])){
	print_r($connection->post('statuses/update', array('status' => emoji_docomo_to_unified($_POST['status']))));//.$str
	echo('<hr />');
}

	function utf8_bytes($cp){

		if ($cp > 0x10000){
			# 4 bytes
			return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
				chr(0x80 | (($cp & 0x3F000) >> 12)).
				chr(0x80 | (($cp & 0xFC0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x800){
			# 3 bytes
			return	chr(0xE0 | (($cp & 0xF000) >> 12)).
				chr(0x80 | (($cp & 0xFC0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else if ($cp > 0x80){
			# 2 bytes
			return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
				chr(0x80 | ($cp & 0x3F));
		}else{
			# 1 byte
			return chr($cp);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv=Content-Type content="text/html;charset=utf-8">
  <title>...</title>

<script language="javascript"> 

function keypress2() //textarea输入长度处理 
{
var i=140;
var text1=document.getElementById("mystatus").value; 
var len;//记录剩余字符串的长度 
if(text1.length>=i)//textarea控件不能用maxlength属性，就通过这样显示输入字符数了 
{ 
document.getElementById("mystatus").value=text1.substr(0,i); 
len=0; 
} 
else 
{ 
//len=i-text1.length; 
len=text1.length; 
} 
document.getElementById("munb").innerHTML=len; 
} 

</script> 

 </head>

 <body onload="keypress2()">
<form action="update.php" method="post" name="form">
<?php
if(isset($_GET['id'])){
echo('<input type="text" name="in_reply_to_status_id" value="'.$_GET['id'].'" /><br />');
}
?>

<textarea name="status" id="mystatus" rows="6" cols="60" onKeyUp="keypress2()" onblur="keypress2()">
<?php
if(isset($_GET['uid']))
echo('@'.$_GET['uid'].' ');

if(isset($_GET['str']))
echo(' '.$_GET['str']);
?>
</textarea>
<br />
<input type="submit" name="Submit" value="POST" />&nbsp;字数&nbsp;<span id="munb"></span>
</form>

 </body>
</html>
