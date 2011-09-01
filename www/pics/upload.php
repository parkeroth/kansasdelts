<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'photo');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script type="text/javascript" src="js/jquery-1.3.2.js" ></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>

<style type="text/javascript">
	#upload{
		margin:30px 200px; padding:15px;
		font-weight:bold; font-size:1.3em;
		font-family:Arial, Helvetica, sans-serif;
		text-align:center;
		background:#f2f2f2;
		color:#3366cc;
		border:1px solid #ccc;
		width:150px;
		cursor:pointer !important;
		-moz-border-radius:5px; -webkit-border-radius:5px;
	}
	.darkbg{
		background:#ddd !important;
	}
	#status{
		font-family:Arial; padding:5px;
	}
	ul#files{ list-style:none; padding:0; margin:0; }
	ul#files li{ padding:10px; margin-bottom:2px; width:200px; float:left; margin-right:10px;}
	ul#files li img{ max-width:180px; max-height:150px; }
	.success{ background:#99f099; border:1px solid #339933; }
	.error{ background:#f0c6c3; border:1px solid #cc6622; }
</style>

<script type="text/javascript" >
	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		new AjaxUpload(btnUpload, {
			action: 'upload-file.php',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				status.text('');
				//Add uploaded file to list
				if(response==="success"){
					$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				} else{
					$('<li></li>').appendTo('#files').text(file).addClass('error');
				}
			}
		});
		
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div id="mainbody" >
		<h3>&raquo; AJAX File Upload Form Using jQuery</h3>
		<!-- Upload Button, use any id you wish-->
		<div id="upload" ><span>Upload File<span></div><span id="status" ></span>
		
		<ul id="files" ></ul>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>