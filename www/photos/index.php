<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerFirst.php");
include_once 'auth.php';

$valid_sets = array();                      //list of all flickr sets we want to use
//get all photo sets for user
$all_photo_sets = $f->photosets_getList($my_user_id);

for($i=0; $i<5; $i++) {
    $current_photo_set = $all_photo_sets['photoset'][$i];
    array_push($valid_sets, $current_photo_set);
}

unset($current_photo_set);
?>
<link rel="stylesheet" type="text/css" href="photo_styles.css" />
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/headerLast.php"); ?>

<h2>Gamma Tau Photos</h2>
<div id="list_newest">
    <h3>Latest Photo Sets</h3>
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
<div id="list_category">
    <h3>Photo Sets By Category</h3>
    <ul class="photo_set_links">
        <li><a href="list_sets.php?type=alumni" title="Alumni Photos">Alumni</a></li>
        <li><a href="list_sets.php?type=service" title="Service Photos">Service</a></li>
        <li><a href="list_sets.php?type=brotherhood" title="Brotherhood Photos">Brotherhood</a></li>
        <li><a href="list_sets.php?type=tailgate" title="Tailgate Photos">Tailgate</a></li>
    </ul>
</div>
<div style="clear: both;"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>