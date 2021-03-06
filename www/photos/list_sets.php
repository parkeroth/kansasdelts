<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerFirst.php");
include_once 'auth.php';

$photo_type = $_GET['type'];            //desired photo type
$valid_sets = array();                      //list of all flickr sets we want to use
//get all photo sets for user
$all_photo_sets = $f->photosets_getList($my_user_id);

foreach ($all_photo_sets['photoset'] as $current_photo_set) {
    $set_title = $current_photo_set['title'];
    //we only want sets of the specified type, so we'll need to check the title to see if they match or not
    if (strpos(strtolower($set_title), strtolower($photo_type)) !== false) {
        //valid set, so lets go ahead and add it to our list
        array_push($valid_sets, $current_photo_set);
    }
}
unset($current_photo_set);
?>
<link rel="stylesheet" type="text/css" href="photo_styles.css" />
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerLast.php"); ?>
<h2>Gamma Tau Photos</h2>
<h3>Category: <?php echo $photo_type; ?></h3>
<div id="photo_set_links_holder">
    <ul class="photo_set_links">
    <?php
    //now have list of valid flickr sets to work with
    //spit out links to view each set

    foreach ($valid_sets as $current_photo_set) {
        $set_id = $current_photo_set['id'];
        $set_title = $current_photo_set['title'];
        echo '<li class="photo_set_link">
                                    <a href="view_set.php?set_id=' . $set_id . '" title="View Photo Set ' . $set_title . '">' . $set_title . '</a>
                            </li>';
    }
    ?>
    </ul>
</div>

        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>