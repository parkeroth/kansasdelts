<?php
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
        include_once 'auth.php';

        $photo_id = $_GET['photo_id'];            //desired photo id

        //require_once("phpFlickr.php");
        //$f = new phpFlickr($api_key);

        //get photo info
        $photo_info = $f->photos_getInfo($photo_id);
        //get photo sizes
        $photo_sizes = $f->photos_getSizes($photo_id);

        foreach($photo_sizes['sizes']['size'] as $photo_size) {
            //we'll only grab the medium size
            if($photo_size['label'] == "Medium") {
                $photo_src = $photo_size['source'];
            }
        }
?>
<link rel="stylesheet" type="text/css" href="photo_styles.css" />
<?php 	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2 class="photo_image_title"><?php echo $photo_info['photo']['title']; ?></h2>

<div id="individual_photo">
    <img src="<?php echo $photo_src; ?>" alt="<?php echo $photo_info['photo']['title']; ?>" />
</div>

<a href="<?php echo $photo_info['photo']['urls']['url']['photopage'] ?>" title="View this image on Flickr">[ View This Image on Flickr ]</a>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>