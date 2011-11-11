<?php 
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/loginSystem/session.php');
	
	/**
	 * close all open xhtml tags at the end of the string
	 *
	 * @param string $html
	 * @return string
	 * @author Milian Wolff <mail@milianw.de>
	 */
	function closetags($html) {
	  #put all opened tags into an array
	  preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	  $openedtags = $result[1];
	 
	  #put all closed tags into an array
	  preg_match_all('#</([a-z]+)>#iU', $html, $result);
	  $closedtags = $result[1];
	  $len_opened = count($openedtags);
	  # all tags are closed
	  if (count($closedtags) == $len_opened) {
		return $html;
	  }
	  $openedtags = array_reverse($openedtags);
	  # close tags
	  for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)){
		  $html .= '</'.$openedtags[$i].'>';
		} else {
		  unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	  }
	  return $html;
	}
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<meta name="google-site-verification" content="5gvdjR3w4rQyU5l53mP-W89IHPcC_omrwYTaJqE5Vf4" />

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/fadeslideshow.js"></script>

<script type="text/javascript">

var mygallery=new fadeSlideShow({
	wrapperid: "slideshow", //ID of blank DIV on page to house Slideshow
	dimensions: [300, 225], //width/height of gallery in pixels. Should reflect dimensions of largest image
	imagearray: [		// "file-location", "link-url", "target (_new)", "description"
		["img/flash1.jpg"],
		["img/flash2.jpg", "", "", "Annual ski trip to Colorado"],
		["img/flash3.jpg", "", "", "Our view of the hill"],
		["img/flash4.jpg"],
		["img/flash5.jpg", "", "", "A view from our back patio"],
		["img/flash6.jpg"] //<--no trailing comma after very last image element!
	],
	displaymode: {type:'auto', pause:2500, cycles:0, wraparound:false},
	persist: false, //remember last viewed slide and recall within same session?
	fadeduration: 500, //transition duration (milliseconds)
	descreveal: "ondemand",
	togglerid: ""
})

</script>

<style type="text/css">
	.blogEntry {
		padding-top: 25px;
		width: 500px;
		margin-right: auto;
		margin-left: auto;
		border-bottom: groove;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
	
	<div id="slideshow-container">
		<div id="slideshow" class="slideshow"></div>
    </div>
	
            <h3>Welcome to Delt</h3>

<!-- //This is where you input breaking news -->
            <p class="padded">Gamma Tau chapter of Delta Tau Delta welcomes you.  Over Delta Tau Delta’s rich history at the University of Kansas, beginning in 1912, Gamma Tau chapter of Delta Tau Delta has been enriching the lives of its members and bettering the Lawrence community.  Located off 11th Street, the Delt House provides an ideal location for football tailgating, a picturesque view of Potter Lake and the surrounding KU campus, all the while being less than a fifteen minute walk for nearly any class.  Gamma Tau's rich diversity provides multiple life perspectives and aids in the development of a responsible gentleman. Local</p>
			
<?php
	// get front page blog
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	//echo 'Class UName: '.$this->username.'<br />'; //this DOESN'T WORK
	//echo 'Session UName: '.$_SESSION['username'];
	
	//if($_SESSION['username'] == "disnat" || $_SESSION['username'] == "rotpar" || $_SESSION['username'] == "kasgra" || $_SESSION['username'] == "waljac")
	//{
		echo '<h2>Gamma Tau News</a>';
		//Select the 3 latest blog entries
		$getEntryQ = '
			SELECT *
			FROM blogContent
			ORDER BY date DESC
			LIMIT 3
		';
		$getEntry = mysqli_query($mysqli, $getEntryQ);
		$entryCount = 0;
		while($blogData = mysqli_fetch_array($getEntry, MYSQLI_ASSOC))
		{
			$entryData[$entryCount]['header'] = stripslashes($blogData['header']);
			$entryData[$entryCount]['content'] = html_entity_decode($blogData['content']);
			$entryData[$entryCount]['category'] = stripslashes($blogData['category']);
			$entryData[$entryCount]['date'] = $blogData['date'];
			$entryData[$entryCount]['id'] = $blogData['id'];
			$entryData[$entryCount]['submitter'] = $blogData['submitter'];
			$entryCount++;
		}
		for($i=0;$i<$entryCount;$i++)
		{
			echo '
				<div class="blogEntry">
					<h3>
						'.$entryData[$i]['header'].'
					</h3>
					<div style="float:right; font-style:italic; text-align: right; padding-right: 10px;">
						'.$entryData[$i]['date'].'<br />
						Filed Under: '.$entryData[$i]['category'].'
					</div>
					<div style="margin-top:45px;">
						'.closetags($entryData[$i]['content']).'
					</div>
				</div>
				';
		}
	//}
?>
			
<!-- //Stop -->

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
