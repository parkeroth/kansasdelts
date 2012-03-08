<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerFirst.php");
include_once 'auth.php';

$set_id = $_GET['set_id'];            //desired photo set
//get all photo sets for user
$all_set_photos = $f->photosets_getPhotos($set_id);
$set_info = $f->photosets_getInfo($set_id);
?>
<link rel="stylesheet" type="text/css" href="photo_styles.css" />
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerLast.php"); ?>

<div id="photo_set_holder">
    <ul class="photo_set_view">
<?php
//print our our set info
echo '<h2 class="photo_set_name">' . $set_info['title'] . '</h2>';
echo '<p class="photo_set_description">' . $set_info['description'] . '<p>';

//now loop over each picture
foreach ($all_set_photos['photoset']['photo'] as $current_photo) {
    $photo_id = $current_photo['id'];
    $photo_title = $current_photo['title'];
    //get current image thumbnail
    $photo_thumb_url =  $f->buildPhotoURL($current_photo, "Square");

    //echo link to indivual view and thumbnail of current photo
    echo '
                    <li class="photo_link">
                        <a href="view_photo.php?photo_id=' . $photo_id . '" title="Individual Image">
                            <img src="' . $photo_thumb_url . '" alt="' . $photo_title . '" />
                        </a>
                    </li>';
}
?>
    </ul>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>