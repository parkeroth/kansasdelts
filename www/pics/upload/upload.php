<?php
	session_start();
	include_once('php/login.php');
	$authUsers = array('admin','photo');
	include_once('/php/authenticate.php');
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

	<!-- Framework CSS -->
	<link rel="stylesheet" href="styles/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="styles/print.css" type="text/css" media="print">
	<!--[if IE]><link rel="stylesheet" href="http://github.com/joshuaclayton/blueprint-css/raw/master/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
<!--[if lte IE 7]>
	<script type="text/javascript" src="http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js"></script>
<![endif]-->


	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.2/mootools.js"></script>

	<script type="text/javascript" src="source/Swiff.Uploader.js"></script>

	<script type="text/javascript" src="source/Fx.ProgressBar.js"></script>

	<script type="text/javascript" src="http://github.com/mootools/mootools-more/raw/master/Source/Core/Lang.js"></script>

	<script type="text/javascript" src="source/FancyUpload2.js"></script>


	<!-- See script.js -->
	<script type="text/javascript">
		//<![CDATA[

		/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

window.addEvent('domready', function() { // wait for the content

	// our uploader instance 
	
	var up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: true,
		
		// url is read from the form, so you just have to change one place
		url: $('form-demo').action,
		
		// path to the SWF file
		path: 'source/Swiff.Uploader.swf',
		
		// remove that line to select all files, or edit it, add more items
		typeFilter: {
			'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
		},
		
		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'demo-browse',
		
		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('demo-status').removeClass('hide'); // we show the actual UI
			$('demo-fallback').destroy(); // ... and hide the plain form
			
			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons
			
			$('demo-clear').addEvent('click', function() {
				up.remove(); // remove all files
				return false;
			});

			$('demo-upload').addEvent('click', function() {
				up.start(); // start upload
				return false;
			});
		},
		
		// Edit the following lines, it is your custom event handling
		
		/**
		 * Is called when files were not added, "files" is an array of invalid File classes.
		 * 
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */ 
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},
		
		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});
			
			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', '<strong>Image was uploaded:</strong> ' + json.get('width') + ' x ' + json.get('height') + 'px, <em>' + json.get('mime') + '</em>)');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
			}
		},
		
		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
					break;
				case 'empty': // Oh oh, wrong path
					alert('A required file was not found, please be patient and we fix this.');
					break;
				case 'flash': // no flash 9+ :(
					alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
			}
		}
		
	});
	
});
		//]]>
	</script>

	<script type="text/javascript" language="javascript">
<!--
function checkform ( form )
{

  var val=form.newGallery.value;
  // ** START **
  if (val == "") {
    alert( "No gallery specified." );
    form.newGallery.focus();
    return false ;
  } else {
	  alert("newGallery Value: "+val);
	}
  // ** END **
  return true ;
}
//-->
	
	</script>

	<!-- See style.css -->
	<style type="text/css">
		/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

/* CSS vs. Adblock tabs */
.swiff-uploader-box a {
	display: none !important;
}

/* .hover simulates the flash interactions */
a:hover, a.hover {
	color: red;
}

#demo-status {
	padding: 10px 15px;
	width: 420px;
	border: 1px solid #eee;
}

#demo-status .progress {
	background: url(assets/progress-bar/progress.gif) no-repeat;
	background-position: +50% 0;
	margin-right: 0.5em;
	vertical-align: middle;
}

#demo-status .progress-text {
	font-size: 0.9em;
	font-weight: bold;
}

#demo-list {
	list-style: none;
	width: 450px;
	margin: 0;
}

#demo-list li.validation-error {
	padding-left: 44px;
	display: block;
	clear: left;
	line-height: 40px;
	color: #8a1f11;
	cursor: pointer;
	border-bottom: 1px solid #fbc2c4;
	background: #fbe3e4 url(assets/failed.png) no-repeat 4px 4px;
}

#demo-list li.file {
	border-bottom: 1px solid #eee;
	background: url(assets/file.png) no-repeat 4px 4px;
	overflow: auto;
}
#demo-list li.file.file-uploading {
	background-image: url(assets/uploading.png);
	background-color: #D9DDE9;
}
#demo-list li.file.file-success {
	background-image: url(assets/success.png);
}
#demo-list li.file.file-failed {
	background-image: url(assets/failed.png);
}

#demo-list li.file .file-name {
	font-size: 1.2em;
	margin-left: 44px;
	display: block;
	clear: left;
	line-height: 40px;
	height: 40px;
	font-weight: bold;
}
#demo-list li.file .file-size {
	font-size: 0.9em;
	line-height: 18px;
	float: right;
	margin-top: 2px;
	margin-right: 6px;
}
#demo-list li.file .file-info {
	display: block;
	margin-left: 44px;
	font-size: 0.9em;
	line-height: 20px;
	clear
}
#demo-list li.file .file-remove {
	clear: right;
	float: right;
	line-height: 18px;
	margin-right: 6px;
}	</style>

	<style type="text/css">
    body {
        background: #181818 !important;
    }
    #centerUpload {
        margin-right: auto;
        margin-left: auto;
    }
    </style>

<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

	<div class="container">

		<h1>Delt Photos</h1>

		<h2>Upload Directory</h2>
		<!-- See index.html -->
		<div id="centerUpload">
<form action="server/script.php" method="post" enctype="multipart/form-data" id="form-demo">
<table border="1" style="width: 600px !important; text-align: center;">
	<tr style="background-color: transparent !important;">
    	<td style="width: 250px !important; background: transparent !important;">
        	Would you like to add to existing album or create a new?
        </td>
        <td style="width: 250px !important; background: transparent !important;">
        	<input type="radio" name="upTo" id="upTo" value="existing" /> Existing Directory <br />
            <input type="radio" name="upTo" id="upTo" value="new" /> New Directory
        </td>
    </tr>
    <tr style="background-color: transparent !important;">
    	<td style="width: 250px !important; background: transparent !important;">
		Select from Existing Directory:
        </td>
        <td style="width: 250px !important; background: transparent !important;">
        	<select name="directories">
				<?php
                    filesInDir($_SERVER['DOCUMENT_ROOT'].'/pics/galleries');
                    function filesInDir($tdir)
                    {
						$dirs = scandir($tdir);
                        foreach($dirs as $file)
                        {
							//echo $file.'<br />';
                            if (($file == '.')||($file == '..'))
                            {
                            }
                            elseif (is_dir($tdir.'/'.$file))
                            {
								$stripDocRoot = str_replace($_SERVER['DOCUMENT_ROOT'].'/pics',"",$tdir.'/'.$file);
                                echo '<option value="'.$stripDocRoot.'">'.$stripDocRoot.'</option>
								';
                                filesInDir($tdir.'/'.$file);
                            }
                        }
                    }
                ?>
            </select>
       	</td>
	</tr>
    <tr style="background-color: transparent !important;">
    	<td style="width: 250px !important; background-color: transparent !important;">
			Name of New Directory:
        </td>
        <td style="width: 250px !important; background-color: transparent !important;">
        	<input type="text" name="newGallery" id="newGallery" onmouseover="return checkform(this);" />
        </td>
	</tr>
</table>
    <h2>Picture Upload</h2>
	<fieldset id="demo-fallback">
		<legend>File Upload</legend>
		<p>
			Use this page to upload photos of Delt events.  Note the subdirectory, as this will be the gallery name.
		</p>
		<label for="demo-photoupload">
			Upload a Photo:
			<input type="file" name="Filedata" />
		</label>
	</fieldset>

	<div id="demo-status" class="hide">
		<p>
			<a href="#" id="demo-browse">Browse Files</a> |
			<a href="#" id="demo-clear">Clear List</a> |
			<a href="#" id="demo-upload">Start Upload</a>
		</p>
		<div>
			<strong class="overall-title"></strong><br />
			<img src="assets/progress-bar/bar.gif" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title"></strong><br />
			<img src="assets/progress-bar/bar.gif" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>

	<ul id="demo-list"></ul>

</form>		</div>


	</div>

	<div class="container quiet" style="line-height: 5em;">
		© 2008-2009 by <a href="http://digitarald.de/">Harald Kirschner</a> and available under <a href="http://www.opensource.org/licenses/mit-license.php">The MIT License</a>
	</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>