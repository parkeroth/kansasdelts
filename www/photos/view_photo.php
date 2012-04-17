<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerFirst.php");
include_once 'auth.php';

$photo_id = $_GET['photo_id'];            //desired photo id
//get photo info
$photo_info = $f->photos_getInfo($photo_id);
//get photo sizes
$photo_src = $f->buildPhotoURL($photo_info['photo'], "medium");
?>
<link rel="stylesheet" type="text/css" href="photo_styles.css" />
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerLast.php"); ?>

<h2 class="photo_image_title"><?php echo $photo_info['photo']['title']; ?></h2>

<div id="individual_photo">
    <img src="<?php echo $photo_src; ?>" alt="<?php echo $photo_info['photo']['title']; ?>" />
</div>

<div class="center" style="text-align: center;"><a href="http://www.flickr.com/photos/<?php echo $my_user_id.'/'.$photo_id; ?>" title="View this image on Flickr">[ View This Image on Flickr ]</a></div>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>