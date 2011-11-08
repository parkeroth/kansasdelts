<?php 
	session_start();
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

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
	
	<div id="slideshow-container">
		<div id="slideshow" class="slideshow"></div>
    </div>
	
            <h3>Welcome to Delt Ah!</h3>

<!-- //This is where you input breaking news -->
            <p class="padded">Gamma Tau chapter of Delta Tau Delta welcomes you.  Over Delta Tau Delta’s rich history at the University of Kansas, beginning in 1912, Gamma Tau chapter of Delta Tau Delta has been enriching the lives of its members and bettering the Lawrence community.  Located off 11th Street, the Delt House provides an ideal location for football tailgating, a picturesque view of Potter Lake and the surrounding KU campus, all the while being less than a fifteen minute walk for nearly any class.  Gamma Tau's rich diversity provides multiple life perspectives and aids in the development of a responsible gentleman. Local</p>
			
<?php
	/*
	include("php/simple_html_dom.php");
	
	$html = file_get_html('http://kansasdelts.tumblr.com/');
		   
	foreach($html->find('div.post') as $post) {
		$item['title']  = $post->find('div.title', 0)->plaintext;
		$item['body']   = $post->find('div.copy', 0)->plaintext;
		$posts[] = $item;
		
		echo "<h3>".$item['title']."</h3>";
		$snippet = substr($item['body'],0,220);
		echo "<p class=\"padded\">".$snippet."...<a href=\"http://kansasdelts.tumblr.com\">Read More</a></p>";
	}
		*/   
?>
			
<!-- //Stop -->

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>