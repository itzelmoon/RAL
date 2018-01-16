<?php
$ROOT = '../';
include $ROOT."includes/main.php";
include $ROOT."includes/post.php";

?>
<!DOCTYPE HTML>
<HTML>
<head>
<?php
	if (isset($_GET['hacking']))
		$title = 'HACKING';
	elseif (isset($_GET['install']))
		$title = 'INSTALL';
	elseif (isset($_GET['license']))
		$title = 'LICENSE';
	else
		$title = 'README';
	head($title) ?>
</head>
<body>
<div class=sidebar>
	<h2>RAL</h2>
	<span class=collection>
<?php
	$a = CONFIG_WEBROOT;
	print
<<<HTML
	<a href="$a">Home</a>

HTML;
	if (CONFIG_CLEAN_URL) {
		$items = [
		[
		"name" => "Readme",
		"location" => CONFIG_WEBROOT . "doc",
		],
		[
		"name" => "Install",
		"location" => CONFIG_WEBROOT . "doc/install",
		],
		[
		"name" => "License",
		"location" => CONFIG_WEBROOT . "doc/license",
		],
		[
		"name" => "Hacking",
		"location" => CONFIG_WEBROOT . "doc/hacking",
		] ];
	} else {
		$items = [
		[
		"name" => "Readme",
		"location" => CONFIG_WEBROOT . "doc.php",
		],
		[
		"name" => "Install",
		"location" => CONFIG_WEBROOT . "doc.php?install",
		],
		[
		"name" => "License",
		"location" => CONFIG_WEBROOT . "doc.php?license",
		],
		[
		"name" => "Hacking",
		"location" => CONFIG_WEBROOT . "doc.php?hacking",
		] ];
	}
	include "{$ROOT}template/nav.php";
?>
	</span>

</div>
<div id=rightpanel>
<?php
	$title .= ".pod";
	include "{$ROOT}template/header.php";
?>
	<div class='reader docs'>
<?php
	if (isset($_GET['install'])) {
		$file = CONFIG_LOCALROOT . "docs/INSTALL.pod";
		ppppppp($file);
	} else if (isset($_GET['hacking'])) {
		$file = CONFIG_LOCALROOT . "docs/HACKING.pod";
		ppppppp($file);
	} else if (isset($_GET['license'])) {
		$file = CONFIG_LOCALROOT . "docs/LICENSE";
		ppppppp($file);
	} else {
		$file = CONFIG_LOCALROOT . "docs/README.pod";
		ppppppp($file);
	}
?>
	</div>
</div>
</body>
</HTML>