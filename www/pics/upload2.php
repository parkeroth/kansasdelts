<?php
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'academics', 'proctor');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>

<style type="text/css">
	.swiff-uploader-box a {
		display: none !important;
	}
	 
	/* .hover simulates the flash interactions */
	a:hover, a.hover {
		color: red;
	}
	 
	#delt-status {
		padding: 10px 15px;
		width: 420px;
		border: 1px solid #eee;
	}
	 
	#delt-status .progress {
		background: url(images/progress-bar/progress.gif) no-repeat;
		background-position: +50% 0;
		margin-right: 0.5em;
		vertical-align: middle;
	}
	 
	#delt-status .progress-text {
		font-size: 0.9em;
		font-weight: bold;
	}
	 
	#delt-list {
		list-style: none;
		width: 450px;
		margin: 0;
	}
	 
	#delt-list li.validation-error {
		padding-left: 44px;
		display: block;
		clear: left;
		line-height: 40px;
		color: #8a1f11;
		cursor: pointer;
		border-bottom: 1px solid #fbc2c4;
		background: #fbe3e4 url(images/failed.png) no-repeat 4px 4px;
	}
	 
	#delt-list li.file {
		border-bottom: 1px solid #eee;
		background: url(images/file.png) no-repeat 4px 4px;
		overflow: auto;
	}
	#delt-list li.file.file-uploading {
		background-image: url(images/uploading.png);
		background-color: #D9DDE9;
	}
	#delt-list li.file.file-success {
		background-image: url(images/success.png);
	}
	#delt-list li.file.file-failed {
		background-image: url(images/failed.png);
	}
	 
	#delt-list li.file .file-name {
		font-size: 1.2em;
		margin-left: 44px;
		display: block;
		clear: left;
		line-height: 40px;
		height: 40px;
		font-weight: bold;
	}
	#delt-list li.file .file-size {
		font-size: 0.9em;
		line-height: 18px;
		float: right;
		margin-top: 2px;
		margin-right: 6px;
	}
	#delt-list li.file .file-info {
		display: block;
		margin-left: 44px;
		font-size: 0.9em;
		line-height: 20px;
		clear
	}
	#delt-list li.file .file-remove {
		clear: right;
		float: right;
		line-height: 18px;
		margin-right: 6px;
	}
</style>

<script language="javascript" src="js/upload.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<form action="../uploadScript.php" method="post" enctype="multipart/form-data" id="upPhoto">
 
	<fieldset id="delt-fallback">
		<legend>File Upload</legend>
		<p>
			This form is just an example fallback for the unobtrusive behaviour of FancyUpload.
			If this part is not changed, something must be wrong with your code.
		</p>
		<label for="delt-photoupload">
			Upload a Photo:
			<input type="file" name="Filedata" />
		</label>
	</fieldset>
 
	<div id="delt-status" class="hide">
		<p>
			<a href="#" id="delt-browse">Browse Files</a> |
			<a href="#" id="delt-clear">Clear List</a> |
			<a href="#" id="delt-upload">Start Upload</a>
		</p>
		<div>
			<strong class="overall-title"></strong><br />
			<img src="images/progress-bar/bar.gif" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title"></strong><br />
			<img src="images/progress-bar/bar.gif" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>
 
	<ul id="delt-list"></ul>
 
</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>