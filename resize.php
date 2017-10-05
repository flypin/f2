<?php
// Use it this way: resize.php?width=500&pic=http://img.gghappy.com/attachment/forum/201608/14/103440jr446sf02fx16qiq.jpg
// kokesh@kokeshnet.com 2004
	define("_CachePath_","./c/");
	define("_CacheEnable_","1");
	define("_ReCacheTime_","4320000");
	include('cache.php');

	$cache=new cache();
	if ($cache->check())
	{
		//echo $cache->read();
		header('Location: '._CachePath_.$cache->cachefile);
	}
	else
	{

			$originalFile=$_GET["pic"];
			$my_width=isset($_GET["width"])?$_GET["width"]:0;

			$info = getimagesize($originalFile);
			$mime = $info['mime'];

			switch ($mime) {
				case 'image/jpeg':
					header("Content-type: image/jpeg");
					$image_create_func = 'imagecreatefromjpeg';
					$image_save_func = 'imagejpeg';
					$new_image_ext = 'jpg';
					break;

				case 'image/png':
					header("Content-type: image/png");
					$image_create_func = 'imagecreatefrompng';
					$image_save_func = 'imagepng';
					$new_image_ext = 'png';
					break;

				default:
					exit;
			}
			header('Content-Disposition: attachment; filename="'.basename($originalFile).'"');
			$im = $image_create_func($originalFile);

			$old_x=imageSX($im);
			$old_y=imageSY($im);

			$new_w=(int)($my_width);
			if (($new_w<=0) or ($new_w>$old_x)) {
				$new_w=$old_x;
			}

			$new_h=($old_x*($new_w/$old_x));

			if ($old_x > $old_y) {
				$thumb_w=$new_w;
				$thumb_h=$old_y*($new_h/$old_x);

			}
			if ($old_x < $old_y) {
				$thumb_w=$old_x*($new_w/$old_y);
				$thumb_h=$new_h;
			}
			if ($old_x == $old_y) {
				$thumb_w=$new_w;
				$thumb_h=$new_h;
			}
			$thumb=ImageCreateTrueColor($thumb_w,$thumb_h);
			imagecopyresized($thumb,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
			$tmp="tmp.img";
			if($new_image_ext==='png'){
				//$image_save_func($thumb);
				$image_save_func($thumb,$tmp);

			}else{
				//$image_save_func($thumb,NULL,90);
				$image_save_func($thumb,$tmp,90);
			}

			imagedestroy($thumb);



		$cache->write(file_get_contents($tmp));
		echo $cache->read();
	}






?>