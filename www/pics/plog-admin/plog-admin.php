<?php
header('Content-Type: text/html; charset=utf-8');
global $inHead;

// Load configuration variables from database, plog-globals, & plog-includes/plog-functions
require_once(dirname(dirname(__FILE__)).'/plog-load-config.php');

session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'photo', 'historian');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

// Load the admin functions only after the login has been determined
require_once(PLOGGER_DIR.'plog-admin/plog-admin-functions.php');

// Display admin tabs
function display($string, $current) {
	global $inHead;
	global $config;

	$tabs = array();
	$tabs['upload']	= array('url' => 'plog-upload.php', 'caption' => plog_tr('<em>U</em>pload'));
	$tabs['import']		= array('url' => 'plog-import.php?nojs=1', 'caption' => plog_tr('<em>I</em>mport'), 'onclick' => "window.location='plog-import.php'; return false;");
	$tabs['manage']	= array('url' => 'plog-manage.php', 'caption' => plog_tr('<em>M</em>anage'));
	$tabs['feedback']	= array('url' => 'plog-feedback.php', 'caption' => plog_tr('<em>F</em>eedback'));
	$tabs['options']	= array('url' => 'plog-options.php', 'caption' => plog_tr('<em>O</em>ptions'));
	$tabs['themes']	= array('url' => 'plog-themes.php', 'caption' => plog_tr('<em>T</em>hemes'));
	$tabs['plugins']	= array('url' => 'plog-plugins.php', 'caption' => plog_tr('<em>P</em>lugins'));
	$tabs['view']		= array('url' => $config['gallery_url'], 'caption' => plog_tr('<em>V</em>iew'), 'onclick' => "window.open('".$config['gallery_url']."'); return false;");
	$tabs['support']	= array('url' => 'http://www.plogger.org/forum/', 'caption' => plog_tr('<em>S</em>upport'), 'onclick' => "window.open('http://www.plogger.org/forum/'); return false;");
	$tabs['account']		= array('url' => '/account.php', 'caption' => plog_tr('<em>D</em>elt Account Page'));
	// Get the accesskey from the localization - it should be surrounded by <em> tags
	foreach($tabs as $key => $data) {
		if (preg_match('|<em>(.*)</em>|', $data['caption'], $matches)) {
			$tabs[$key]['accesskey'] = $matches[1];
		}
	}

$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Plogger '.plog_tr('Gallery Admin').'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="'.$config['gallery_url'].'plog-admin/css/admin.css" type="text/css" media="all" />
	<link rel="stylesheet" href="'.$config['gallery_url'].'plog-admin/css/lightbox.css" type="text/css" media="all" />
	<script type="text/javascript" src="'.$config['gallery_url'].'plog-admin/js/prototype.js"></script>
	<script type="text/javascript" src="'.$config['gallery_url'].'plog-admin/js/plogger.js"></script>
	<script type="text/javascript" src="'.$config['gallery_url'].'plog-admin/js/lightbox.js"></script>
	'.$inHead.'
</head>

<body onload="initLightbox();">

<div id="header">

	<div id="logo">
		<img src="'.$config['gallery_url'].'plog-admin/images/plogger.gif" width="393" height="90" alt="Plogger" />
	</div><!-- /logo -->

	<div id="plogger-version">
		<div class="align-right">
			'.$config['version'].'&nbsp;&nbsp;&nbsp;['.plogger_show_server_info_link().']
		</div><!-- /align-right -->
		'.plogger_generate_server_info().'
	</div><!-- /plogger-version -->

	<div style="clear: both; height: 15px;">&nbsp;</div>

	<div id="tab-nav">
		<ul>';
		foreach($tabs as $tab => $data) {
		$output .= '
			<li';
			if ($current == $tab) $output .= ' id="current"';
			$output .= '><a';
			if (!empty($data['onclick'])) $output .= ' onclick="'.$data['onclick'].'"';
			if (!empty($data['accesskey'])) $output .= ' accesskey="'.$data['accesskey'].'"';
			$output .= ' href="'.$data['url'].'">'.$data['caption'].'</a></li>';
		}
		$output .= '
		</ul>
	</div><!-- /tab-nav -->

</div><!-- /header -->

<div id="content">
'.$string.'
</div><!-- /content -->';

if (defined('PLOGGER_DEBUG') && PLOGGER_DEBUG == '1') {
	$output .= trace('Queries: '.$GLOBALS['query_count'], false);
	foreach ($GLOBALS['queries'] as $q) {
		$output .= trace($q, false);
	}
	$output .= trace(plog_timer('end'), false);
}

$output .= "\n\n" . '</body>
</html>';

echo $output;

close_db();
close_ftp();
exit;
}

?>