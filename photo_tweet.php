<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="">
<style>
</style>
</head>
<body>

<div id="photo_tweet">
  <h2>Photo Tweet</h2>
<?php
require_once('config.php');

	require './tmhOAuth.php';
	$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => CONSUMER_KEY,
		'consumer_secret' => CONSUMER_SECRET,
		'user_token'      => AC_CONSUMER_KEY,
		'user_secret'     => AC_CONSUMER_SECRET,
	));


if (!empty($_FILES)) {
  // we set the type and filename are set here as well
  $params = array(
    'media[]' => "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}",
    'status'  => $_POST['status']
  );

  $code = $tmhOAuth->user_request(array(
    'method' => 'POST',
    'url' => $tmhOAuth->url("https://api.twitter.com/1.1/statuses/update_with_media.json"),
    'params' => $params,
    'multipart' => true
  ));

  if ($code == 200) :
  $data = json_decode($tmhOAuth->response['response'], true);
?>
  <p>Hello, @<?php echo htmlspecialchars($data['user']['screen_name']) ?>.</p>
  You just <a href="?status_id=<?php echo htmlspecialchars($data['id_str']) ?>">tweeted</a>
<?php else : ?>
  <h3>Something went wrong</h3>
  <p><?php echo $tmhOAuth->response['error'] ?></p>
<?php endif; ?>
</div>
<?php } else { ?>
  <form action="" method="POST" enctype="multipart/form-data">
    <div>
      <p><label for="status">Tweet Text</label></p>
      <p><textarea type="text" name="status" rows="5" cols="60"></textarea></p>

      <p><label for="image">Photo</label>
      <input type="file" name="image" /></p>

      <p><input type="submit" value="Submit" /></p>
    </div>
  </form>
<?php } ?>

</body>
</html>
